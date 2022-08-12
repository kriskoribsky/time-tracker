<?php 
// # Query database for projects only from session's project group
$sql = 'SELECT * from projects WHERE project_group_id=:group_id ORDER BY date_created DESC';
$projects = Sanitize::sanitize_html_query(Database::query($sql, ['group_id' => $_SESSION['group_id']]));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit-project'])) {

        // query DB for tasks, session & initialize their ORM objects
        $sql = "SELECT * FROM tasks WHERE project_id=:selected_project ORDER BY date_created DESC";
        $queried_tasks = Database::query($sql, ['selected_project' => $_POST['submit-project']], PDO::FETCH_OBJ);
        $tasks = [];

        foreach($queried_tasks as $t) {
            $queried_sessions = Database::query('SELECT * from sessions WHERE task_id=:task_id', ['task_id' => $t->id], PDO::FETCH_OBJ);
            $sessions = [];

            foreach ($queried_sessions as $s) {
                $sessions[] = new Session($s);
            }
            $tasks[] = new Task($t, $sessions);
        }

        // query DB for current configuration settings
        $config_query = Sanitize::sanitize_html_query(Database::query('SELECT option_name, value FROM app_config'));

        foreach ($config_query as $query) {
            $config[$query['option_name']] = $query['value'];
        }
        $display_net = (bool) $config['display_net_time'];


        // calculate working times with DataBase object's methods
        foreach ($tasks as $task) {
            $task->work_time = $task->get_sessions_work($task->subComponents, $display_net);
            $task->unpaid_work_time = $task->get_unpaid_work($task->subComponents, $display_net);
            $task->net_ratio = $task->get_net_ratio($task->subComponents); 

        }

    }
}

// map project ids to their titles (for hidden input value in 'task add' section)
$ids = [];

foreach ($projects as $project) {
    $ids[$project['id']] = $project['title'];
}

?>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'header.php'); ?>

<div class="main-content">

    <h1>Tasks</h1>


    <div class="content-statistics-wrapper">

        <div class="content-statistics-wrapper-browse">

            <section class="data-section shadow browse-tasks project-select">
                
                <form class="form form-heading" action="<?php echo Sanitize::sanitize_html($_SERVER['REQUEST_URI']); ?>" method="POST">
                    <div class="double-header">
                        <h2>Browse tasks</h2>

                        <div>
                            <label for="project-selection">Project:</label>
                            <select onchange='if(this.value != 0) { this.form.submit(); }' class="selection-dropdown" name="submit-project" id="project-selection" >

                                <option value="" disabled hidden <?php echo $_POST['submit-project'] ?? 'selected'; ?>>Choose a project</option>
                                <?php foreach($projects as $project): ?>
                                    <option <?php echo (isset($_POST['submit-project']) && $_POST['submit-project'] === $project['id']) ? 'selected' : null; ?> value="<?php echo $project['id'] ?>"><?php echo $project['title']; ?></option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                    </div>
                    
                </form>

            </section>

            <?php foreach((isset($tasks) ? $tasks : []) as $task):?>

                <section class="data-section shadow">

                    <h2 class="split-heading">
                        <span><?php echo $task->title; ?></span>
                        <span title="The date this task was created" class="text-created-at"><?php echo Format::database_time($task->date_created); ?></span>
                    </h2>

                    <table class="project-info">

                            <tr>
                                <th>Sessions:</th>
                                <td><?php echo count($task->subComponents); ?></td>
                            </tr>

                            <tr>
                                <th>Work-time:</th>
                                <td><?php echo Format::format_seconds($task->work_time); ?></td>
                            </tr>

                            <tr>
                                <th>Unpaid work-time:</th>
                                <td class="text-green"><?php echo Format::format_seconds($task->unpaid_work_time); ?></td>
                            </tr>

                            <tr title="Time spent effectively solely on coding (higher = better)">
                                <th>Effectivity ratio</th>
                                <td><?php echo $task->net_ratio; ?></td>
                            </tr>

                        </table>

                        <div class="controls hidden"></div>

                </section>

            <?php endforeach; ?>

        </div>

        <!-- add-task part -->
        <?php if(isset($_POST['submit-project'])): ?>

            <div class="add-task">

                <section class="data-section shadow addional-padding-bottom">

                    <h2 class="split-heading">
                        <span>Add new tasks to your project</span>
                        <i class="fa-solid fa-plus"></i>
                    </h2>

                    <form class="form" action="/forms" method="POST">

                        <div class="form-row">

                            <label for="project-selection">Project:</label>
                            <select class="selection-dropdown" id="project-selection" disabled>
                               <option value="" selected><?php echo $ids[$_POST['submit-project']]; ?></option>
                            </select>

                            <input type="hidden" name="project-id" value="<?php echo $_POST['submit-project']; ?>">
                        </div>

                        <hr class="form-divide">

                        <div class="form-row">

                            <label>
                                <span>Task name: </span>
                                <input name="task-name" type="text" required>
                            </label>

                        </div>

                        <div class="form-row">
                            <button name="submit" value="new-task" type="submit" class="form-btn btn-submit">
                                <i class="fa-solid fa-check"></i><span>Add</span>
                            </button>
                        </div>

                        <div class="alert danger hidden" role="alert">Please choose a project and try again</div>

                    </form>

                </section>

                <section class="data-section overall-statistics shadow current-config">

                    <h2 class="split-heading">
                        <span>Display configuration</span>
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </h2>

                    <table class="text-left">
                    
                        <tr title="<?php echo $display_net ? 'Currently displaying in net-time format' : 'Currently displaying in gross-time format'; ?>">
                            <th>Time format:</th>
                            <td class="time-format"><em><?php echo $display_net ? 'net' : 'gross' ?></em></td>
                        </tr>

                    </table>

                </section>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'footer.php'); ?>