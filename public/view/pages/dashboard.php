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

// overall statistics
$group_sessions = [];

foreach ($group->get_instances(Session::class) as $session) {
    $group_sessions[] = $session;
} 

$group->work_time_7_days = $group->get_latest_sessions_work($group_sessions, $display_net);
$group->work_time = $group->get_sessions_work($group_sessions, $display_net);
$group->unpaid_work_time = $group->get_unpaid_work($group_sessions, $display_net);
$group->salary = $group->get_salary($group->unpaid_work_time, $wage);
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
                            <th>Date created:</th>
                            <td><?php echo Format::database_time($project->date_created); ?></td>
                        </tr>

                        <tr>
                            <th>Number of tasks:</th>
                            <td><?php echo count($project->subComponents); ?></td>
                        </tr>

                        <tr>
                            <th>Unpaid work-time:</th>
                            <td class="text-green">5 hours 1 minute</td>
                        </tr>

                        <tr>
                            <th>Total project work-time:</th>
                            <td>104 hours 54 minutes</td>
                        </tr>

                    </table>

                    <div class="controls hidden"></div>

                </section>

            <?php endforeach; ?>

        </div>

        <div class="content-statistics-wrapper-statistics">

            <section class="data-section overall-statistics shadow">

                <h2>Overall statistics</h2>

                <canvas></canvas>

                <table class="text-left">

                    <tr>
                        <th>Working time past 7 days:</th>
                        <td><?php echo Format::format_seconds($group->work_time_7_days); ?></td>
                    </tr>

                    <tr>
                        <th>Total work-time:</th>
                        <td><?php echo Format::format_seconds($group->work_time); ?></td>
                    </tr>

                    <tr>
                        <th>Total unpaid work-time:</th>
                        <td class="text-green"><?php echo Format::format_seconds($group->unpaid_work_time); ?></td>
                    </tr>

                    <tr>
                        <th>Hourly wage:</th>
                        <th><?php echo $wage . ' €'; ?></th>
                    </tr>

                    <tr>
                        <th>Salary:</th>
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

        </div>

    </div>

</div>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'footer.php'); ?>