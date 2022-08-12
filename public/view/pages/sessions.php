<?php 
# Query database for projects
$projects = Sanitize::sanitize_html_query(Database::query("SELECT * FROM projects ORDER BY date_created DESC"));



// for project selection
$project_selected = null;

// map project ids to their titles (for hidden input value in 'task add' section)
$ids = [];

foreach ($projects as $project) {
    $ids[$project['id']] = $project['title'];
}



// self-form handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // load tasks after user selects project 
    if (isset($_POST['submit-project'])) {
        $sql = "SELECT * FROM tasks WHERE project_id=:selected_project ORDER BY date_created DESC";

        $tasks = Sanitize::sanitize_html_query(Database::query($sql, ['selected_project' => $_POST['submit-project']]));

        $project_selected = $_POST['submit-project'];

    // perform input validation and calculate & display information to confirm by user
    // input validation will be also performed in forms.php file after final form submission
    } elseif (isset($_POST['check-inputs']) && $_POST['check-inputs'] === 'new-session-inputs') {

        $project_selected = $_POST['project-selected'];
        $wrong_input = false;

        // split $_POST['task-id]
        $task_id = explode(';', $_POST['task-id'])[0];
        $task_name = explode(';', $_POST['task-id'])[1];

        try {
            // start and end times will be further validated
            assert(preg_match('/^\d\d?:\d\d$/', $_POST['start-time']) === 1);
            assert(preg_match('/^\d\d?:\d\d$/', $_POST['end-time']) === 1);

            assert(preg_match('/^\d{1,3}$/', $_POST['net-working-time']) === 1);

            // convert start and end-times to calculate total time worked
            $start = Format::valid_time('G:i', $_POST['start-time']);
            $end = Format::valid_time('G:i', $_POST['end-time']);

            assert($start !== false);
            assert($end !== false);

            if (isset($_POST['next-day']) && $_POST['next-day'] == true) {
                $end = $end->modify('+1 day');
            }

            // $gross_code_hours = Format::subtract_hours_minutes($start, $end);
            $gross_code_seconds = Format::subtract_seconds($start, $end);
            $gross_code_formatted = Format::format_seconds($gross_code_seconds);

            $ratio = (float) $_POST['net-working-time'] / 100;

            $net_code_seconds = round($gross_code_seconds * $ratio);
            $net_code_formatted = Format::format_seconds((int) $net_code_seconds);

        } catch(AssertionError) {
            $wrong_input = true;
        }  
    }   
}

?>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'header.php'); ?>

<div class="main-content">

    <h1>Sessions</h1>

    <section class="data-section shadow add-session">

        <h2 class="split-heading">
            <span>Track new working session</span>
            <i class="fa-solid fa-plus"></i>
        </h2>

        
            <form class="form" action="<?php echo Sanitize::sanitize_html($_SERVER['REQUEST_URI']); ?>" method="POST">

                <div class="form-row">
                    <label for="project-selection">Project:</label>
                    <select onchange='if(this.value != 0) { this.form.submit(); }' class="selection-dropdown" name="submit-project" id="project-selection" <?php echo isset($_POST['check-inputs']) ? 'disabled' : null; ?>>

                        <option value="" disabled hidden <?php echo $_POST['submit-project'] ?? 'selected'; ?>>Choose a project</option>
                        <?php foreach($projects as $project): ?>
                            <option <?php echo (isset($project_selected) && $project_selected === $project['id']) ? 'selected' : null; ?> value="<?php echo $project['id'] ?>"><?php echo $project['title']; ?></option>
                        <?php endforeach; ?>

                    </select>
                </div>

            </form>

        <?php if (!isset($tasks) && !isset($_POST['check-inputs'])): ?>

            <div class="session-padding"></div>

        <?php endif; ?>

        <!-- INPUT CHECK -->
        <?php if (isset($tasks) && !isset($_POST['check-inputs'])): ?>

            <form class="form" action="<?php echo Sanitize::sanitize_html($_SERVER['REQUEST_URI']); ?>" method="POST">

                <div class="form-row">
                    <label for="task-selection">Task:</label>
                    <select class="selection-dropdown" name="task-id" id="task-selection" required <?php echo empty($tasks) ? 'disabled' : ''; ?>>

                        <option value="" disabled hidden selected>Choose a task</option>
                        <?php foreach($tasks as $task): ?>
                            <!-- submit for check both task id and its title -->
                            <option value="<?php echo $task['id'] . ';' . $task['title']; ?>"><?php echo $task['title']; ?></option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <hr class="form-divide">

                <div class="form-row">

                    <label>
                        <span>Started: </span>
                        <input class="time-input" type="text" placeholder="8:30" name="start-time" required>
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
                    <button type="submit" name="check-inputs" value="new-session-inputs" class="form-btn btn-secondary">
                        <i class="fa-solid fa-list-check"></i><span>Check</span>
                    </button>
                </div>

                <!-- also pass selected project so that next page load that option will still be selected -->
                <input type="hidden" name="project-selected" value="<?php echo $project_selected; ?>">

                <div class="alert danger hidden" role="alert">Please enter the correct time format and try again.</div>

            </form>

        <?php endif; ?>

        <!-- INPUT SUBMIT (wrong_input = false) -->
        <?php if (isset($_POST['check-inputs']) && $_POST['check-inputs'] === 'new-session-inputs' && $wrong_input === false): ?>

            <form class="form" action="/forms" method="POST">

                <div class="form-row">
                    <label for="task-selection">Task:</label>
                    <select class="selection-dropdown" name="task-id" id="task-selection" disabled>

                        <option value="<?php echo $task_id; ?>"><?php echo $task_name; ?></option>

                    </select>
                    <!-- also pass selected task so that next page load that option will still be selected -->
                    <input type="hidden" name="task-id" value="<?php echo $task_id; ?>">
                </div>

                <hr class="form-divide">

                <div class="form-row calculated-time-wrapper">
                    
                    <div>
                        <label>
                            <span>Started: </span>
                            <input class="time-input" value="<?php echo $_POST['start-time']; ?>" type="text" disabled>
                            <input type="hidden" name="start-time" value="<?php echo $_POST['start-time']; ?>">
                        </label>
                        
                        <label>
                            <span>Finished: </span>
                            <input class="time-input" value="<?php echo $_POST['end-time']; ?>" type="text" disabled>
                            <input type="hidden" name="end-time" value="<?php echo $_POST['end-time']; ?>">
                        </label>
                        
                        <label class="next-day-checkbox">
                            <span>Next day:</span>
                            <input type="checkbox" <?php echo isset($_POST['next-day']) && $_POST['next-day'] == 'true' ? 'checked' : null; ?> disabled >
                            <input type="hidden" name="next-day" value="<?php echo isset($_POST['next-day']) && $_POST['next-day'] == 'true' ? 'true' : null; ?>">
                        </label>
                    </div>

                    <div>
                        <output class="calculated-time"><?php echo $gross_code_formatted; ?></output>
                    </div>

                </div>

                <div class="form-row calculated-time-wrapper">

                    <div>
                        <label title="Ratio of time spent solely on coding compared to googling or other research.">
                            <span>Net coding time : </span>
                            <input type="number" min="0" max="100" step="5" class="int-input" type="text" value="<?php echo $_POST['net-working-time']; ?>" disabled>
                            <input type="hidden" name="net-working-time" value="<?php echo $_POST['net-working-time']; ?>">
                            <span>%</span>

                        </label>
                    </div>

                    <div>
                        <output class="calculated-time"><?php echo $net_code_formatted; ?></output>
                    </div>

                </div>

                <hr class="form-divide">

                <label>
                    <span>Note: </span>
                    <input type="text" value="<?php echo $_POST['note'] ?? null; ?>" disabled>
                    <input type="hidden" name="note" value="<?php echo $_POST['note'] ?? null; ?>">
                </label>


                <div class="form-row button-margin">

                    <div class="alert success" role="alert">Inputs have correct format, please check the values & <strong>submit</strong>.</div>

                    <div class="a-btn-and-btn">
                        <a class="form-btn btn-secondary btn-with-alert a-btn" href="javascript: window.history.back();">
                        <i class="fa-solid fa-arrow-left"></i><span>Go back</span>
                        </a>
                        <button type="submit" name="submit" value="new-session" class="form-btn btn-submit btn-with-alert">
                            <i class="fa-solid fa-repeat"></i><span>Submit</span>
                        </button>
                    </div>

                </div>

            </form>

        <?php endif; ?>

        <!-- INPUT SUBMIT (wrong_input = true) -->
        <?php if (isset($_POST['check-inputs']) && $_POST['check-inputs'] === 'new-session-inputs' && $wrong_input === true): ?>

            <form class="form" action="<?php echo Sanitize::sanitize_html($_SERVER['REQUEST_URI']); ?>" method="POST">

                <div class="form-row">
                    <label for="task-selection">Task:</label>
                    <select class="selection-dropdown" name="task-id" id="task-selection" disabled>

                        <option value="<?php echo $task_id; ?>"><?php echo $task_name; ?></option>

                    </select>
                    <!-- also pass selected task so that next page load that option will still be selected -->
                    <input type="hidden" name="task-id" value="<?php echo $_POST['task-id']; ?>">
                </div>

                <hr class="form-divide">

                <div class="form-row">

                    <label>
                        <span>Started: </span>
                        <input class="time-input" value="<?php echo $_POST['start-time']; ?>" type="text" placeholder="8:30" name="start-time" required>
                    </label>
                    
                    <label>
                        <span>Finished: </span>
                        <input class="time-input" value="<?php echo $_POST['end-time']; ?>" type="text" placeholder="17:45" name="end-time" required>
                    </label>
                    
                    <label class="next-day-checkbox">
                        <span>Next day:</span>
                        <input type="checkbox" <?php echo isset($_POST['next-day']) && $_POST['next-day'] == 'true' ? 'checked' : null; ?> name="next-day" value="true">
                    </label>
                    
                </div>

                <div class="form-row">

                    <label title="Ratio of time spent solely on coding compared to googling or other research.">
                        <span>Net coding time : </span>
                        <input type="number" min="0" max="100" step="5" class="int-input" type="text" name="net-working-time" value="<?php echo $_POST['net-working-time']; ?>" required>
                        <span>%</span>
                    </label>
                    
                </div>

                <hr class="form-divide">

                <label>
                    <span>Note: </span>
                    <input type="text" name="note" placeholder="(optional)" value="<?php echo $_POST['note'] ?? null; ?>">
                </label>


                <div class="form-row button-margin">

                    <div class="alert warning" role="alert">Please enter the correct time format and try again.</div>

                    <div class="a-btn-and-btn">
                        <a class="form-btn btn-secondary btn-with-alert a-btn" href="javascript: window.history.back();">
                        <i class="fa-solid fa-arrow-left"></i><span>Go back</span>
                        </a>
                        <button type="submit" name="check-inputs" value="new-session-inputs" class="form-btn btn-warning btn-with-alert">
                            <i class="fa-solid fa-repeat"></i><span>Try again</span>
                        </button>
                    </div>

                    <!-- also pass selected project so that next page load that option will still be selected -->
                    <input type="hidden" name="project-selected" value="<?php echo $project_selected; ?>">

                    <!-- pass calculated net & gross working times to submit form on this same page -->
                    <input type="hidden" name="gross-time" value="<?php echo $gross_code_formatted ?>">
                    <input type="hidden" name="net-time" value="<?php echo $net_code_formatted ?>">

                </div>

            </form>

        <?php endif; ?>

    </section>

</div>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'footer.php'); ?>