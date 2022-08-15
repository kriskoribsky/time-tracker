<?php 
# query DB with group id from this $_SESSION
$group = Database::query('SELECT * from project_groups WHERE id=:group_id', ['group_id' => $_SESSION['group_id']], PDO::FETCH_OBJ)[0];

// query DB again for latest project, task, session data (because they could be updated)
$queried_projects = Database::query('SELECT * from projects WHERE project_group_id=:group_id ORDER BY date_created DESC', [
    'group_id' => $_SESSION['group_id']
], PDO::FETCH_OBJ);

$queried_tasks = Database::query('SELECT * from tasks', null, PDO::FETCH_OBJ);
$queried_sessions = Database::query('SELECT * from sessions', null, PDO::FETCH_OBJ);

$projects = [];

foreach ($queried_projects as $p) {
    $tasks = [];
    foreach ($queried_tasks as $t) {
        $sessions = [];
        foreach($queried_sessions as $s) {
            if ($s->task_id == $t->id) {
                $sessions[] = new Session($s);
            }
        }
        if ($t->project_id == $p->id) {
            $tasks[] = new Task($t, $sessions);
        }
    }
    $projects[] = new Project($p, $tasks);
}

$group = new Group($group, $projects);

// query DB for current configuration settings
$config_query = Sanitize::sanitize_html_query(Database::query('SELECT option_name, value FROM app_config'));

foreach ($config_query as $query) {
    $config[$query['option_name']] = $query['value'];
}

$display_net = (bool) $config['display_net_time'];
$wage = $config['salary_rate'];

// project specific statistics
foreach ($group->subComponents as $project) {
    $project_sessions = [];

    foreach($project->get_instances(Session::class) as $session) {
        $project_sessions[] = $session;
    }

    $project->work_time = $project->get_sessions_work($project_sessions, $display_net);
    $project->unpaid_work_time = $project->get_unpaid_work($project_sessions, $display_net);
    $project->net_ratio = $project->get_net_ratio($project_sessions);

}

// overall statistics
$group_sessions = [];

foreach ($group->get_instances(Session::class) as $session) {
    $group_sessions[] = $session;
} 

$group->past_days_work = $group->past_days_work($group_sessions, $display_net);
$group->past_days_total = array_sum(array_values($group->past_days_work));
$group->work_time = $group->get_sessions_work($group_sessions, $display_net);
$group->unpaid_work_time = $group->get_unpaid_work($group_sessions, $display_net);
$group->salary = $group->get_salary($group->unpaid_work_time, $wage);
$group->net_ratio = $group->get_net_ratio($group_sessions);

?>



<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'header.php'); ?>

<div class="main-content">

    <h1>
        <span>Your projects</span>
        <a class="heading-icon" data-toggle="modal" data-target="#create-project-modal" href="#" title="Create new project"><i class="fa-solid fa-square-plus"></i></a>
    </h1>

    <div class="content-statistics-wrapper">

        <div class="content-statistics-wrapper-projects">

            <?php foreach($projects as $project): ?>

                <section class="data-section project-section shadow width-100">

                    <h2><?php echo $project->title; ?></h2>

                    <table class="project-info">

                        <tr>
                            <th>Created</th>
                            <td><?php echo Format::database_time($project->date_created); ?></td>
                        </tr>

                        <tr>
                            <th>Tasks</th>
                            <td><?php echo count($project->subComponents); ?></td>
                        </tr>

                        <tr>
                            <th>Work-time</th>
                            <td><?php echo Format::format_seconds($project->work_time); ?></td>
                        </tr>

                        <tr>
                            <th>Unpaid work-time</th>
                            <td class="text-green"><?php echo Format::format_seconds($project->unpaid_work_time); ?></td>
                        </tr>

                        <tr title="Time spent effectively solely on coding (higher = better)">
                            <th>Effectivity ratio</th>
                            <td><?php echo $project->net_ratio; ?></td>
                        </tr>


                    </table>

                    <div class="controls hidden"></div>

                </section>

            <?php endforeach; ?>

        </div>

        <div class="content-statistics-wrapper-statistics">

            <section class="data-section overall-statistics shadow">

                <h2 class="split-heading">
                    <span>Overall statistics</span>
                    <i class="fa-solid fa-chart-simple"></i>
                </h2>

                <div class="canvas-container">
                    <div class="graph-area">
                        <div class="graph-size-monitor">
                            <canvas id="work-time-graph" data-graph-data="<?php echo htmlspecialchars(json_encode($group->past_days_work), ENT_QUOTES, 'UTF-8'); ?>"></canvas>
                            <div id="work-time-tooltip" style="opacity: 0">
                                <h6 id="tooltip-day">Monday</h6>
                                <span id="tooltip-data">28 hours 3 minutes</span>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="text-left">

                    <tr>
                        <th>Working time past 7 days:</th>
                        <td><span class="past-days-work"><?php echo Format::format_seconds($group->past_days_total); ?></span></td>
                    </tr>

                    <tr>
                        <th>Work-time</th>
                        <td><?php echo Format::format_seconds($group->work_time); ?></td>
                    </tr>

                    <tr>
                        <th>Unpaid work-time</th>
                        <td class="text-green"><?php echo Format::format_seconds($group->unpaid_work_time); ?></td>
                    </tr>

                     <tr title="Time spent effectively solely on coding (higher = better)">
                            <th>Effectivity ratio</th>
                            <td><?php echo $group->net_ratio; ?></td>
                    </tr>

                    <tr>
                        <th>Hourly wage</th>
                        <th><?php echo $wage . ' €'; ?></th>
                    </tr>

                    <tr>
                        <th>Salary</th>
                        <td class="text-green"><strong><?php echo number_format($group->salary, 2) . ' €'; ?></strong></td>
                    </tr>

                </table>

                <div class="overall-statistics-foot">

                    <hr>

                    <!-- <button type="submit" is inside pop-up modal -->
                    <button class="form-btn btn-danger" data-toggle="modal" data-target="#checkout-work-time-modal">
                        <i class="fa-solid fa-clock"></i><span>Checkout</span>
                    </button>

                </div>

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

    </div>

</div>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'footer.php'); ?>