<?php declare(strict_types=1);
/**
 * @var $projects
 * @var $project_id_in_view
 * @var $can_create_project
 * @var $project_default
 * @var $logged
 * @var $user
 */

$projects = $projects ??= [];
$project_default = $project_default ??= null;
$logged = $logged ??= null;
$user = $user ??= null;
?>

<nav class="header navbar navbar-expand-lg navbar-dark bg-dark"  id="header">
    <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
    <div>

        <div class="navbar-brand brand--app" >
            <div id="profileDropDown" class="dropdown--custom">
                <span> <i class='bx bxs-user-circle'></i></span>

                <div id="profileDropDownWindow" class="dropdown--custom-content">
                    <div class="card">
                        <div class="card-header">
                            <span class="user--name"><?= $user ?></span>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a href="/logout" >
                                    <i class='bx bx-log-out nav_icon'></i>
                                    <span class="nav_name">SignOut</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</nav>

<section class="l-navbar navbar-dark bg-dark" id="nav-bar">
    <nav class="nav">
        <div class="nav--sidebar-header nav_link--main">
            <a href="/" class="nav_link active">
                <i class='bx bx-grid-alt nav_icon'></i>
                <span class="nav_name">Dashboard</span>
            </a>
            <div class="nav_list">
                <?php if($projects && $can_create_project) { ?>
                    <!-- Create New Project Form -->
                    <form action="/project" method="POST"  class="nav_link project--box project--form">
                        <input type="text" name="origin" value="/" hidden/>
                        <button class="project--submit" type="submit"><i class='bx bx-shape-polygon'></i></button>
                        <input  type="text" class="project--input" name="name" placeholder="New Project name..." >
                    </form>
                <?php } ?>


                <!--  SideBar Items -->
                <?php
                $output = '';

                if($project_default) {
                    $project_default_id = $project_default['id'];
                    $active = (int) $project_default_id === (int) $project_id_in_view ? 'active' : '';
                    $placeholder = <<<HTML
<a href="/?project={$project_default_id}" class="nav_link project--box {$active}">
    <i class='bx bxs-cube-alt' ></i>
    <span class="nav_name">{$project_default['name']}</span>
</a>
HTML;
                    $output .= $placeholder;
                }

                foreach($projects as $project) {
                    $project_id = $project['id'];
                    if ($project_id === $project_default_id) continue;
                    $active = (int) $project_id === (int) $project_id_in_view ? 'active' : '';
                    $placeholder = <<<HTML
<a href="/?project={$project_id}" class="nav_link project--box {$active}">
    <i class='bx bxs-shapes'></i>
    <span class="nav_name">{$project['name']}</span>
    <div class="input-group-append">   
        <form action="/project/{$project_id}/delete" method="POST" class="form--custom-no-skeleton project--delete-form">
            <input type="text" name="origin" value="/" hidden/>
            <input type="number" name="id" value="{$project_id}" hidden/>
            <button type="submit" class="task--remover-link project--submit project--submit-delete">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash task--bi-trash" viewBox="0 0 16 16">
                  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                  <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                </svg>
            </button>
        </form>   
    </div>
</a>
HTML;
                    $output .= $placeholder;
                }
                    echo $output;
                ?>

            </div>

        </div>
        <?php if($logged) {?>
            <div class="nav--sidebar-footer">
                <a href="/logout" class="nav_link">
                    <i class='bx bx-log-out nav_icon'></i>
                    <span class="nav_name">SignOut</span>
                </a>
            </div>
        <?php }?>


    </nav>
</section>

