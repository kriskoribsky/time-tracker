<?php 
# Query database for projects
$projects = Sanitize::sanitize_html_query(Database::query("SELECT * FROM projects ORDER BY date_created DESC"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit-project'])) {
        $sql = "SELECT * FROM tasks WHERE project_id=:selected_project ORDER BY date_created DESC";

        $tasks = Sanitize::sanitize_html_query(Database::query($sql, ['selected_project' => $_POST['submit-project']]));
    }
}
?>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'header.php'); ?>

<div class="main-content">

    <h1>Sessions</h1>

    <section class="data-section shadow add-session">

        <h2>Track new working session</h2>

        
            <form class="form" action="<?php echo Sanitize::sanitize_html($_SERVER['REQUEST_URI']); ?>" method="POST">

                <div class="form-row">
                    <label for="project-selection">Project:</label>
                    <select onchange='if(this.value != 0) { this.form.submit(); }' class="selection-dropdown" name="submit-project" id="project-selection" >

                        <option value="" disabled hidden <?php echo $_POST['submit-project'] ?? 'selected'; ?>>Choose a project</option>
                        <?php foreach($projects as $project): ?>
                            <option <?php echo (isset($_POST['submit-project']) && $_POST['submit-project'] === $project['id']) ? 'selected' : null; ?> value="<?php echo $project['id'] ?>"><?php echo $project['title']; ?></option>
                        <?php endforeach; ?>

                    </select>
                </div>

            </form>

        <?php if (!isset($tasks)): ?>

            <div class="session-padding"></div>

        <?php endif; ?>

        <?php if (isset($tasks)): ?>

            <form class="form" action="/forms" method="POST">

                <div class="form-row">
                    <label for="task-selection">Task:</label>
                    <select class="selection-dropdown" name="" id="task-selection" required>

                        <option value="" disabled hidden selected>Choose a task</option>
                        <?php foreach($tasks as $task): ?>
                            <option value="<?php echo $task['id']; ?>"><?php echo $task['title']; ?></option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <hr class="form-divide">

                <div class="form-row">

                    <label>
                        <span>Started: </span>
                        <input class="time-input" type="text" placeholder="08:30" name="start-time" required>
                    </label>
                    
                    <label>
                        <span>Finished: </span>
                        <input class="time-input" type="text" placeholder="17:45" name="end-time" required>
                    </label>
                    
                    <label class="next-day-checkbox">
                        <span>Next day:</span>
                        <input type="checkbox" name="next-day" value="true">
                    </label>
                    
                </div>

                <div class="form-row">

                    <label title="Ratio of time spent solely on coding compared to googling or other research.">
                        <span>Net coding time : </span>
                        <input type="number" min="0" max="100" step="5" class="int-input" type="text" name="net-working-time" value="100" required>
                        <span>%</span>
                    </label>
                    
                </div>

                <hr class="form-divide">

                <label>
                    <span>Note: </span>
                    <input type="text" name="note" placeholder="(optional)">
                </label>


                <div class="form-row button-margin">
                    <button type="submit" name="check" value="new-session" class="form-btn btn-submit">
                        <i class="fa-solid fa-check"></i><span>Submit</span>
                    </button>
                </div>

                <div class="alert danger hidden" role="alert">Please enter the correct time format and try again.</div>

            </form>

        <?php endif; ?>

    </section>

</div>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'footer.php'); ?>