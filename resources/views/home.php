<?php declare(strict_types=1);
/**
 * @var $tasks
 * @var $projects
 */
?>

<main id="main" class="bg-light">
    <section class="screen">
        <section class="screen--main">
            <header class="card text-center">
                <div class="card-body">
                    <div style="text-align: left;">
                        <p class="font-weight-bold text-uppercase">My Todo List</p>
                        <small><?= date("l, F j"); ?></small>
                    </div>
                </div>
            </header>
            <section class="section--content">
                <!--  Actual Content  -->
                <small class="font-weight-bold">Create new Tasks</small>
                <hr>
                <div class="container-fluid" style="padding: 0">
                    <!--  New Task  -->
                    <form action="/task" method="POST">
                    <div class="input-group task--container">
                            <input type="text" name="origin" value="/" hidden/>
                            <div class="input-group-prepend">
                                <button type="submit" class="task--adder-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg task--adder-btn" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                                    </svg>
                                </button>
                            </div>
                            <input  type="text" class="form-control task--adder-input" name="name" placeholder="Task name..." autofocus>
                    </div>
                    </form>

                    <!--   Create Tasks HTML -->
                    <?php [$taskRendered, $toolbars] =  generateTasks($tasks, $projects); ?>
                    <?= $taskRendered ?>
                    <!--   Generate Empty Placeholder task -->
                    <?= generateEmptyPlaceHoldersTasks(count($tasks)) ?>
                </div>
            </section>
        </section>
        <!-- Tasks Right Side Toolbards -->
        <?= renderToolbars($toolbars); ?>
    </section>

</main>



