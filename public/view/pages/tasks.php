<?php 
# Query database for projects
$projects = Sanitize::sanitize_html_query(Database::query("SELECT * FROM projects ORDER BY date_created DESC"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit-project'])) {
        $sql = "SELECT * FROM tasks WHERE project_id=:selected_project ORDER BY date_created DESC";

        $tasks = Sanitize::sanitize_html_query(Database::query($sql, ['selected_project' => $_POST['submit-project']]));
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

                    <h2><?php echo $task['title']; ?></h2>

                    <table class="project-info">

                            <!-- <tr>
                                <th>Date created:</th>
                                <td><?php echo implode('.', array_reverse(explode('-', explode(' ', $task['date_created'])[0]))); ?></td>
                            </tr> -->

                            <tr>
                                <th>Number of sessions:</th>
                                <td>5</td>
                            </tr>

                            <tr>
                                <th>Unpaid task work-time:</th>
                                <td class="text-green">5 hours 1 minute</td>
                            </tr>

                            <tr>
                                <th>Total task work-time:</th>
                                <td>104 hours 54 minutes</td>
                            </tr>

                        </table>

                        <div class="controls hidden"></div>

                </section>

            <?php endforeach; ?>

        </div>

        <!-- add-session part -->
        <?php if(isset($_POST['submit-project'])): ?>

            <div class="add-task">

                <section class="data-section shadow">

                    <h2>Add new tasks to your project</h2>

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

            </div>

        <?php endif; ?>

    </div>

</div>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'footer.php'); ?>