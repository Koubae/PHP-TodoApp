<?php declare(strict_types=1);

use App\Lib\HTTP\{Router, Response, Request};
use function App\Lib\HTTP\render;


// ========= LOGIN
$login = function(Request $req, Response $res)
{
    return render("login.php", ["logged" => Router::logged()]);
};
$logout = function(Request $req, Response $res)
{
    if (!Router::logged()) {
        return render("login.php", ["logged" => false, "errors" => ["You need to login first"]]);
    }
    $auth = Router::auth();
    $auth->logOut();
    return render("login.php", ["logged" => false, "success" => [ "Successfully logged out!"]]);

};

function verifyLoginSubmit(?array $param)
{
    $email = $param['email'] ??= null;
    $password = $param['password'] ??= null;
    if (!$email || !$password) {
        $errors = [];
        if (!$email) $errors[] = "Email is Missing";
        if (!$password) $errors[] = "Password is Missing";
        render("login.php", ["logged" => Router::logged(), "errors" => $errors]);
        exit;
    }
    return [$email, $password];
}

$loginSubmit = function(Request $req, Response $res)
{

    $param = $req->params;
    [$email, $password] = verifyLoginSubmit($param);
    $auth = Router::auth();
    $errors = [];
    $success = [];

    try {
        $auth->login($email, $password);
        $success[] = 'User is logged in';
    }
    catch (\Delight\Auth\InvalidEmailException $e) {
        $errors[] = 'Wrong email address';
    }
    catch (\Delight\Auth\InvalidPasswordException $e) {
        $errors[] = 'Wrong password';
    }
    catch (\Delight\Auth\EmailNotVerifiedException $e) {
//        $errors[] = 'Email not verified'; // We just pass it for the moment we dont verift emails
    }
    catch (\Delight\Auth\TooManyRequestsException $e) {
        $errors[] = 'Too many requests';
    }
    if($errors) {
        render("login.php", ["logged" => Router::logged(), "errors" => $errors, "success" => $success]);
        exit;
    }
    echo $res::redirect();
    exit;

};
$signupSubmit = function(Request $req, Response $res)
{

    $param = $req->params;
    [$email, $password] = verifyLoginSubmit($param);

    $auth = Router::auth();
    $errors = [];
    $success = [];
    try {
        // TODO: implement Login with username as well. for the moment the user name is the email
        // with a callback function we can send the token via email
        $userId = $auth->register($email, $password, $email);
        if (!$userId) {
            $errors = [];
            $errors[] = "An Error Occurred while registering the User";
            render("login.php", ["logged" => false, "errors" => $errors]);
            exit;
        } else {
            $success[] = "User $email successfully created now you can log in!";
            // Create Default project for this user
            $cr = Router::$cr;

            try {
                // Actual update
                $query = "INSERT INTO `project` 
                    (`name`, `description`, `state`, `is_system_project`, `sequence`, `user_id`)
                VALUES ('All Project', 'All Project',  'in_progress', true, 1, :user_id)";
                $stmt = $cr->prepare($query);
                $stmt->execute(['user_id' => $userId]);
            } catch (\PDOException $e) {
                $errors[] = "Error while creating a new User!";
                $msg = "Error while creating a new Project! Error $e";
                error_log($msg, 0);
                render("login.php", ["logged" => false, "errors" => $errors]);
                exit;
            }
        }

    }

    catch (\Delight\Auth\InvalidEmailException $e) {
        $errors[] = 'Invalid email address';
    }
    catch (\Delight\Auth\InvalidPasswordException $e) {
        $errors[] = 'Invalid password';
    }
    catch (\Delight\Auth\UserAlreadyExistsException $e) {
        $errors[] = 'User already exists';
    }
    catch (\Delight\Auth\TooManyRequestsException $e) {
        $errors[] = 'Too many requests';
    }

    render("login.php", ["logged" => Router::logged(), "errors" => $errors, "success" => $success]);
    exit;
};

// index Page
$index = function(Request $req, Response $res)
{
    $cr = Router::$cr;
    $db = Router::$db; // todo: cache prepared statements ;)
    $user_id = Router::getUserId();

    // Project
    $query = 'SELECT id, name, datetime_create, state, is_system_project from project WHERE user_id = ? ORDER BY sequence, datetime_create DESC';
    $stmt = $cr->prepare($query);
    $stmt->execute([$user_id]);
    $projects = $stmt->fetchAll();

    $default_project = null;
    $default_project_id = null;
    foreach($projects as $project) {
        if ($project['is_system_project']) {
            $default_project = $project;
            $default_project_id = $project['id'];
            break;
        }
    }


    $param = $req->params;
    // did the user select a project from the project tab?
    $project_selected_id = (int) $param['project'] ??= null;
    if ($project_selected_id) {
        // Then Update the Selected project in session
        $_SESSION['project_selected'] = $project_selected_id;
    }

    // Take now the Project selected in session
    $project_selected_session = $_SESSION['project_selected'] ??= null;
    if (!$project_selected_session) { // if project not in sesssion Determine if we have a selected project
        // Then we add the default one, most probably the session was newly created
        $_SESSION['project_selected'] = $default_project_id;
        $project_selected_id = $default_project;
    } else {
        // Otherwise get the session's project
        $project_selected_id = $project_selected_session;
    }


    // Tasks
    $queryPre = 'SELECT 
                    task.id, task.name, task.description, task.date_start, task.date_end, 
                    task.is_done, task.state, task.project_id,
                    task.datetime_create, task.datetime_write, 
                    (
                        SELECT COUNT(todo.id) FROM todo_list AS todo
                        WHERE todo.task_id = task.id
                    ) AS total_todos,
                    (
                        SELECT COUNT(todo.id) FROM todo_list AS todo
                        WHERE todo.task_id = task.id AND todo.is_done 
                    ) AS done_todos,
                    project.name AS project_name, project.state AS project_state,
                    default_project.name AS default_project_name, default_project.state AS default_project_state
                    FROM task 
                    LEFT JOIN project 
                        ON project.id = task.project_id
                    LEFT JOIN project as default_project
                        ON  default_project.id = task.project_default_id                  
                    
                    ';
    $default_project_selected = false;

    if($project_selected_id === $default_project_id) {
        $query = $queryPre . ' WHERE task.project_default_id = (?)';
        $db_params = [$default_project_id];
        $project_id_in_view = $default_project_id;
        $default_project_selected = true;
    } else {
        $query = $queryPre . ' WHERE task.project_id = (?) ';
        $db_params = [$project_selected_id];
        $project_id_in_view = $project_selected_id;

    }

    $query .= 'AND ( project.user_id = (?) OR task.user_id = (?) )';
    $db_params[] = $user_id;
    $db_params[] = $user_id;
    $query .= 'ORDER BY task.id DESC';

    $stmt = $cr->prepare($query);
    $stmt->execute($db_params);
    $tasks = $stmt->fetchAll();
    $redirectToDefaultIfNoProject = false;
    if (!$tasks && !$default_project_selected && $redirectToDefaultIfNoProject) { // Make sure to it the default project
        $query = $queryPre . ' WHERE task.project_default_id = (?) ORDER BY task.id DESC';
        $db_params = [$default_project_id];
        $project_id_in_view = $default_project_id;
        $stmt = $cr->prepare($query);
        $stmt->execute($db_params);
        $tasks = $stmt->fetchAll();
    }
    $can_create_project = count($projects) < MAX_PROJECT;
    $data = [
        "project_default" => $default_project,
        "projects" => $projects,
        "tasks" => $tasks,
        "project_id_in_view" => $project_id_in_view,
        "can_create_project" => $can_create_project,
        "logged" => Router::logged(),
        "user" => Router::getUserEmail(),
    ];
    return render("home.php", $data);
};
// ======================
//          CRUD
// ======================
// Create
$taskCreate = function(Request $req, Response $res)
{
    $cr = Router::$cr;
    $param = $req->params;
    $name = $param['name'] ??= null;
    $origin = $param['origin'] ??= '/';
    if (!$name)  {
        echo $res::redirect(slug: $origin);
        exit;
    }
    $user_id = Router::getUserId();

    // Select User's default Project todo: improve this, put in cache !
    $query = 'SELECT id FROM project WHERE user_id = ? AND is_system_project = TRUE LIMIT 1';
    $stmt = $cr->prepare($query);
    $stmt->execute([$user_id]);
    $defaultProjectID = $stmt->fetchColumn();

    $project_selected_session = $_SESSION['project_selected'] ??= null;
    // Take now the Project selected in session
    if (!$project_selected_session) {
        $project_id = $defaultProjectID;
    } else {
        $project_id = $project_selected_session;
    }

    $today = date('Y-m-d');
    $nextWeek = date('Y-m-d', strtotime('+7 day', strtotime($today)));
    $data = [
        'name' => $name,
        'state' => 'draft',
        'date_start' => $today,
        'date_end' => $nextWeek,
        'user_id' => $user_id,
        'project_default_id' => $defaultProjectID,
        'project_id' => $project_id,
    ];

    try {
        // Actual update
        $query = "INSERT INTO `task` 
                    (`name`, `state`, `date_start`, `date_end`, `user_id`, `project_default_id`, `project_id`) 
                VALUES (:name, :state, :date_start, :date_end, :user_id, :project_default_id, :project_id)";
        $stmt = $cr->prepare($query);
        $stmt->execute($data);
    } catch (\PDOException $e) {
        $msg = "Error while creating a new Task! Error $e";
        error_log($msg, 0);
        echo $res::redirect(slug: $origin);
        exit;
    }

    echo $res::redirect(slug: $origin);
    exit;
};
$projectCreate = function(Request $req, Response $res)
{
    $cr = Router::$cr;
    $param = $req->params;
    $name = $param['name'] ??= null;
    $origin = $param['origin'] ??= '/';
    $user_id = Router::getUserId();
    if (!$name)  {
        echo $res::redirect(slug: $origin);
        exit;
    }
    $data = [
        'name' => $name,
        'state' => 'draft', // TODO: implement somthing with the project state...
        'user_id' => $user_id,
    ];
    try {
        // Actual update
        $query = "INSERT INTO `project` 
                    (`name`, `state`, `user_id`) 
                VALUES (:name, :state, :user_id)";
        $stmt = $cr->prepare($query);
        $stmt->execute($data);
    } catch (\PDOException $e) {
        $msg = "Error while creating a new Task! Error $e";
        error_log($msg, 0);
        echo $res::redirect(slug: $origin);
        exit;
    }

    echo $res::redirect(slug: $origin);
    exit;
};
// Update
$taskUpdate = function(Request $req, Response $res)
{
    $cr = Router::$cr;
    $param = $req->params;
    $id = (int) $param['id'] ??= null;
    $origin = $param['origin'] ??= '/';
    if (!$id)  {
        echo $res::redirect(slug: $origin);
        exit;
    }

    $taskData = array_filter($param, function ($key) {
        $key_to_skip = ['origin'=>true];
        if (!array_key_exists($key, $key_to_skip)) {
            return true;
        }
    },  ARRAY_FILTER_USE_KEY);


    try {
        // Actual update
        $query = "UPDATE `task` 
                SET 
                    `state` = :state, 
                    `name` = :name, 
                    `date_start` = :date_start,
                    `date_end` = :date_end, 
                    `description` = :description,
                    `project_id` = :project_id
                WHERE `task`.`id` = :id";
        $stmt = $cr->prepare($query);
        $stmt->execute($taskData);
    } catch (\PDOException $e) {
        $msg = "Error while updating task  (# $id)! Error $e";
        error_log($msg, 0);
        echo $res::redirect(slug: $origin);
        exit;
    }

    echo $res::redirect(slug: $origin);
    exit;
};

$taskUpdateToggleDone = function(Request $req, Response $res)
{
    $cr = Router::$cr;
    $param = $req->params;
    $id = (int) $param['id'] ??= null;
    $origin = $param['origin'] ??= '/';
    if (!$id)  {
        echo $res::redirect(slug: $origin);
        exit;
    }

    $is_done = $param['is_done'];

    if (is_null($is_done))  $is_done = false;
    elseif ($is_done === 'false') $is_done = false;
    elseif ($is_done === 'true') $is_done = true;

    $taskData = [
        'id' => $id,
        'is_done' => !$is_done
    ];


    try {
        // Actual update
        $query = "UPDATE `task` 
                SET           
                    `is_done` = :is_done
                WHERE `task`.`id` = :id";
        $stmt = $cr->prepare($query);
        $stmt->execute($taskData);
    } catch (\PDOException $e) {
        $msg = "Error while updating Task $id done state! Error $e";
        error_log($msg, 0);
        echo $res::redirect(slug: $origin);
        exit;
    }

    echo $res::redirect(slug: $origin);
    exit;
};


/// Delete
$taskDelete = function(Request $req, Response $res)
{
    $cr = Router::$cr;
    $param = $req->params;
    $id = (int) $param['id'] ??= null;
    $origin = $param['origin'] ??= '/';
    if (!$id)  {
        echo $res::redirect(slug: $origin);
        exit;
    }

    try {
        $query = "DELETE FROM `task` WHERE `id` = (?)";
        $stmt = $cr->prepare($query);
        $stmt->execute([$id]);
    } catch (\PDOException $e) {
        $msg = "Error while deleting Task $id done state! Error $e";
        error_log($msg, 0);
        echo $res::redirect(slug: $origin);
        exit;
    }

    echo $res::redirect(slug: $origin);
    exit;
};
$projectDelete = function(Request $req, Response $res)
{
    $cr = Router::$cr;
    $param = $req->params;
    $id = (int) $param['id'] ??= null;
    $origin = $param['origin'] ??= '/';
    if (!$id)  {
        echo $res::redirect(slug: $origin);
        exit;
    }

    try {
        $query = "DELETE FROM `project` WHERE `id` = (?)";
        $stmt = $cr->prepare($query);
        $stmt->execute([$id]);
    } catch (\PDOException $e) {
        $msg = "Error while deleting Task $id done state! Error $e";
        error_log($msg, 0);
        echo $res::redirect(slug: $origin);
        exit;
    }

    echo $res::redirect(slug: $origin);
    exit;
};

/*
 * =====================================
 *  HELPERS
 * =====================================
 * */

/** @public
 * Generates all tasks HTML
 */
function generateTasks(array $tasks, array $projects): array
{
    $toolbars = [];
    $output = '';
    foreach($tasks as $task) {
        $is_done = $task['is_done'];
        if (is_null($is_done)) $is_done = false;
        $is_doneClass = $is_done ? 'selected' : '';
        $is_doneHTML = $is_done ?
            '<input type="text" name="is_done" value="true"  hidden>' :
            '<input type="text" name="is_done" value="false" hidden>';
        $taskHTML = <<<TASK
<div class="input-group task--container jsTaskBox" data-task-id="{$task['id']}">
    <div class="task--container-sub">
        <div class="input-group-prepend">
            <form action="/task/{$task['id']}/done" method="POST" class="form--custom-no-skeleton">
                <input type="text" name="origin" value="/" hidden/>
                <input type="number" name="id" value="{$task['id']}" hidden/>
                {$is_doneHTML}
                <button type="submit" class="task--adder-link task--adder-link-no-border btn btn-link">
                    <div class="task--adder-select {$is_doneClass}" data-toggle="tooltip" data-placement="top" title="Mark as complete">
                        <svg class="bi bi-check2 task--adder-check " xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"  viewBox="0 0 16 16">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                        </svg>
                    </div>
                </button>
            </form>            
        </div>
        <div class="task--main">
            <span class="task--name"> {$task['name']} </span>
            <div class="task--info">
                <span class="task--project-name">{$task['project_name']}</span>
                <span> Todos: {$task['done_todos']} of {$task['total_todos']} </span>
            </div>
        </div>
        
        <div class="input-group-append">
            <form action="/task/{$task['id']}/delete" method="POST" class="form--custom-no-skeleton">
                <input type="text" name="origin" value="/" hidden/>
                <input type="number" name="id" value="{$task['id']}" hidden/>
                <button type="submit" class="task--remover-link task--adder-link-no-border btn btn-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                      <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                      <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                </button>
            </form>            
        </div>
        
    </div>
</div>
TASK;
        $output .= $taskHTML;
        // Tollbar

        // Create Project Select

        $toolbar = generateTaskToolBar($task, $projects);
        $toolbars[$task['id']] = $toolbar;
    }
    return [$output, $toolbars];
}

/** @public
 * Output the Toolbars already in HTML string format
 */
function renderToolbars(array $toolbars) : string
{
    $HTML = '';
    foreach($toolbars as $taskID => $toolbar) {
        $HTML .=  $toolbar;
    }
    return $HTML;
}

/** @public
 *  Generates Tasks Toolbar window for each task
 */
function generateTaskToolBar(array $task, array $projects): string
{
    $projectSelect = generateProjectSelect($projects, $task);
    $statesHtml = generateStateSelect($task);

    $is_done = $task['is_done'];
    $stateBallClass = $is_done ? "bg-success" :  "bg-danger";
    $class =  $is_done ? "border-success" :  "bg-light";
    $toolbar = <<<TOOLBAR
<section id="taskToolbar-{$task['id']}" class="screen--toolbar screen--toolbar-hide" data-task-id="{$task['id']}">
    <form action="/task/{$task['id']}" method="POST">   
        <input type="text" name="origin" value="/" hidden/>
        <input type="number" name="id" value="{$task['id']}" hidden/>
        <article class="card {$class}">
            <button type="submit" class="task--submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-plus" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5z"/>
                  <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
                </svg>
            </button>
            <div class="card-header">
                <select class="form-select" id="taskState" name="state">
                    {$statesHtml}
                </select>   
                
                <div class="state-box">
                    <span class=" state--ball {$stateBallClass}">             
                    </span>
                </div>

                
                <span>                    
                    <strong>Tasks:</strong><small> Todos:  <span class="card-text"> {$task['done_todos']} of {$task['total_todos']} </span></small>
                </span>
                
                
            </div>
            
            <div class="card-body">                
            
                <h5 class="card-title">            
                    <input type="text" name="name" value="{$task['name']}" class="form-control task--adder-input font-weight-bold text-capitalize"/>
                </h5>           
                           
                <ul class="list-group list-group-flush">              
                                  
                    <li class="list-group-item">
                          <label for="project_id">Project</label>
                          <select class="form-select" id="taskProject" name="project_id">
                              {$projectSelect}
                           </select>   
                    </li>
                    <li class="list-group-item">
                         Start:
                         <input type="date" name="date_start" value="{$task['date_start']}" class="form-control task--adder-input font-weight-bold text-capitalize"/>   
                    </li>
                    <li class="list-group-item">
                        End:
                         <input type="date" name="date_end" value="{$task['date_start']}" class="form-control task--adder-input font-weight-bold text-capitalize"/>   
                    </li>
                </ul>
    <!--            <div class="text-area-box" contentEditable>{$task['description']}</div>-->
                <textarea name="description" class="task--description-box">{$task['description']}</textarea>
            </div>
            <div class="card-footer text-muted">
                <small>
                    <span style="font-size: .6rem;">Created On <strong>{$task['datetime_create']}</strong></span>
                    <br>
                    <span style="font-size: .6rem;">Last Update On <strong>{$task['datetime_write']}</strong></span>
                </small>
            </div>
        </article>
    </form>
</section>
TOOLBAR;
    return $toolbar;
}

/** @public
 *  Generates HTML input selection of Projects for the tasks left toolbar
 */
function generateProjectSelect(array $projects, array $task): string
{
    $projectSelect = '';
    $selectedProject = (int) $task['project_id'];
    foreach($projects as $project) {
        $label = ucfirst($project['name']);
        $projId = (int) $project['id'];
        if ($projId === $selectedProject) {
            $projectSelect .= "<option value=" . $projId . " selected>$label</option>";
        } else {
            $projectSelect .= "<option value=" . $projId . ">$label</option>";
        }
    }
    return $projectSelect;
}

/** @public
 *  Generates HTML input selection of State for the tasks left toolbar
 */
function generateStateSelect(array $task): string
{
    // Create state Select
    $state = $task['state'];
    $states = ['draft', 'in_progress', 'done', 'discarded'];
    $statesHtml = '';
    foreach($states as $s) {
        $sLabel =  ucfirst(str_replace('_', ' ', $s));
        if ($s === $state) {
            $statesHtml .= "<option value=" . $s . " selected>$sLabel</option>";
        } else {
            $statesHtml .= "<option value=" . $s . ">$sLabel</option>";
        }
    }
    return $statesHtml;
}

/** @public
 * Generates empty placeholder in order to fill app the port view of the user
*/
function generateEmptyPlaceHoldersTasks(int $taskCount): string
{
    $output = '';
    $left = 25 - $taskCount;
    for ($i = 0; $i < $left; $i++) {
        $placeholder = <<<PLACEHOLDER
<div class="input-group">
    <span class="form-control task--adder-input task--adder-input-empty" ></span>
</div>
PLACEHOLDER;
        $output .= $placeholder;

    }
    return $output;
}