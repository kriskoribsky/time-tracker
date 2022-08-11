<?php
// query project groups -> their projects & tasks & last session edit date
$queried_groups = Database::query('SELECT * from project_groups', null, PDO::FETCH_OBJ);
$queried_projects = Database::query('SELECT * from projects', null, PDO::FETCH_OBJ);
$queried_tasks = Database::query('SELECT * from tasks', null, PDO::FETCH_OBJ);
$queried_sessions = Database::query('SELECT * from sessions', null, PDO::FETCH_OBJ);

$groups = [];

// inefficient way (am in time trouble, had to do the first solution i could think of)
foreach ($queried_groups as $g) {
    $projects = [];

    foreach ($queried_projects as $p) {
        $tasks = [];

        foreach ($queried_tasks as $t) {
            $sessions = [];

            foreach ($queried_sessions as $s) {
                if ($s->task_id === $t->id) {
                    $sessions[] = new Session($s);
                }
            }
            $task = new Task($t, $sessions);

            if ($task->project_id === $p->id) {
                $tasks[] = $task;
            }
        }
        $project = new Project($p, $tasks);

        if ($project->project_group_id === $g->id) {
            $projects[] = $project;
        }
    }
    $groups[] = new Group($g, $projects);
}

if (isset($_POST['group_id'])) {
    // set object to session in a serialized format
    foreach ($groups as $group) {
        if ($group->id == $_POST['group_id']) {
            $_SESSION['group_id'] = $group->id;
            $_SESSION['group_title'] = $group->title;
            $_SESSION['expire'] = time() + 24 * 60 * 60; // set session for one day

            header('Location: /dashboard');
            exit;
        }
    }
}
?>

<?php require_once 'inc/inc/head.php'; ?>

<body class="no-background">

    <div class="project-groups-container shadow">

        <?php require_once 'inc/inc/modals.php' ?>

        <header>
            <h1 class="main-logo">
                <a href="/project-groups">
                    <i class="fa-solid fa-clock"></i>
                    <span>Time-tracker</span>
                </a>
            </h1>
        </header>

        <main>

            <div class="project-groups">

                <h2>
                    <span>Select a project group</span>
                    <a class="heading-icon" data-toggle="modal" data-target="#create-group-modal" href="#" title="Create new project group"><i class="fa-solid fa-folder-plus"></i></a>
                </h2>

                <div class="carousel">

                    <div class="carousel-viewport fade">

                        <div class="carousel-slider">
                            <form action="<?php echo Sanitize::sanitize_html(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>" method="POST">

                                <?php foreach ($groups as $group) : ?>
                                    <button type="submit" class="carousel-card" title="Click to select" name="group_id" value="<?php echo $group->id; ?>" style="color: <?php echo $group->text_color; ?>;
                                            background-image: linear-gradient(180deg, <?php echo $group->primary_color; ?> 10%, <?php echo $group->primary_gradient_second_color; ?> 100%);">
                                        <div class="card-header">
                                            <h3><?php echo $group->title; ?></h3>

                                            <p title="Hexadecimal color value"><code><?php echo $group->primary_color; ?></code></p>
                                        </div>

                                        <div class="card-body">

                                            <div class="card-projects" title="Projects in this project group">
                                                <h4>Projects</h4>
                                                <ul class="group-projects-dialog">

                                                    <?php foreach ($group->subComponents as $project) : ?>
                                                        <li><?php echo $project->title; ?></li>
                                                    <?php endforeach; ?>

                                                </ul>
                                            </div>

                                            <div class="card-tasks" title="Tasks from projects above">
                                                <h4>Tasks</h4>
                                                <ul class="group-projects-dialog">

                                                    <?php foreach ($group->subComponents as $project) : ?>
                                                        <?php foreach ($project->subComponents as $task) : ?>
                                                            <li><?php echo $task->title; ?></li>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>

                                                </ul>
                                            </div>

                                        </div>

                                        <div class="card-footer">
                                            <div>
                                                <p><span>Last activity:</span><i><?php echo $group->last_session_activity(); ?></i></p>
                                                <p><span>Date created:</span><i><?php echo Format::database_time($group->date_created); ?></i></p>
                                            </div>
                                        </div>
                                    </button>
                                <?php endforeach; ?>

                            </form>
                        </div>

                    </div>

                    <button class="carousel-next-btn" style="display: none;">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <button class="carousel-previous-btn" style="display: none;">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <ol class="carousel-card-dots">
                        <?php foreach ($groups as $_) : ?>
                            <li class="dot"></li>
                        <?php endforeach; ?>
                    </ol>

                </div>

            </div>

        </main>

        <footer class="shadow">
            <span>Copyright © Time-tracker 2022</span>
        </footer>

    </div>

</body>

</html>