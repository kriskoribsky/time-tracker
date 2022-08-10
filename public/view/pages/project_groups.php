<?php
// Object–relational mapping (ORM)
// class DatabaseObject {
//     public int $id;
//     public string $created_at;
//     public string $title;
//     public int $parent_id;
//     public array $child_objects;

//     public function __construct(int $id, string $created_at, string|null $title = null, int|null $parent_id = null, array|null $child_objects = null) {
//         $this->id = $id;
//         $this->created_at = $created_at;
//         $this->title = $title;
//         $this->parent_id = $parent_id;
//         $this->child_objects = $child_objects;
//     }
// }

// class Group extends DatabaseObject {
//     public string $primary_color;
//     public string $primary_gradient_second_color;
//     public string $text_color;

//     public function __construct(int $id, string $created_at, string $title, string $primary_color, array $projects = null) {
//         parent::__construct($id, $created_at, $title, null, $projects);

//         $this->primary_color = $primary_color;
//         $this->primary_gradient_second_color = Format::generate_secondary_gradient_clr($this->primary_color);
//         $this->text_color = Format::get_contrast_clr($this->primary_color);
//     }
// }

// class Project extends DatabaseObject{

//     public function __construct(int $id, string $created_at, string $title, int $group_id, array $tasks) {
//         parent::__construct($id, $created_at, $title, $group_id, $tasks);
//     }
// }

// class Task extends DatabaseObject{

//     public function __construct(string $id, string $created_at, string $title, int $project_id, array $sessions) {
//         parent::__construct($id, $created_at, $title, $project_id, $sessions);
//     }
// }

// class Session extends DatabaseObject{
//     public int $start;
//     public int $end;
//     public int $next_day;
//     public int $gross_time;
//     public string $gross_formatted_time;
//     public int $gross_checkout_time;
//     public float $net_time_ratio;
//     public int $net_time;
//     public string $net_formatted_time;
//     public int $net_checkout_time;
//     public string $note;

//     public function __construct(string $id, string $created_at, int $task_id, int $start, int $end, int $next_day, int $gross_time, string $gross_formatted_time, int $gross_checkout_time, float $net_time_ratio, int $net_time, string $net_formatted_time, int $net_checkout_time, string $note) {
//         parent::__construct($id, $created_at, null, $task_id, null);

//         $this->start = $start;
//         $this->end = $end;
//         $this->next_day = $next_day;
//         $this->gross_time = $gross_time;
//         $this->gross_formatted_time = $gross_formatted_time;
//         $this->gross_checkout_time = $gross_checkout_time;
//         $this->net_time_ratio = $net_time_ratio;
//         $this->net_time = $net_time;
//         $this->net_formatted_time = $net_formatted_time;
//         $this->net_checkout_time = $net_checkout_time;
//         $this->note = $note;
//     }
// }

class Group {
    public string $primary_gradient_second_color;
    public string $text_color;

    public function __construct(object $fetchObjGroup) {
        foreach ($fetchObjGroup as $key => $value) {
            $this->$key = $value;
        }

        $this->primary_gradient_second_color = Format::generate_secondary_gradient_clr($this->primary_color);
        $this->text_color = Format::get_contrast_clr($this->primary_color);
    }
}

// query project groups -> their projects & tasks & last session edit date
$queried_groups = Database::query('SELECT * from project_groups', null, PDO::FETCH_OBJ);
$groups = [];

foreach($queried_groups as $group) {
    $groups[] = new Group($group);
}
?>

<?php require_once 'inc/inc/head.php'; ?>
    
<body class="no-background">

    <div class="project-groups-container shadow">

        <?php require_once 'inc/inc/modals.php' ?>

        <header>
            <h1 class="main-logo">
                <a href="/">
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

                            <?php foreach($groups as $group): ?>
                                <div class="carousel-card" title="Click to select">
                                    <div class="card-header">
                                        <h3><?php echo $group->title; ?></h3>

                                        <p title="Hexadecimal color value"><code><?php echo $group->primary_color; ?></code></p>
                                    </div>

                                    <div class="card-body">

                                        <div class="card-projects" title="Projects in this project group">
                                            <h4>Projects</h4>
                                            <ul class="group-projects-dialog">

                                                <li>Pevasoft</li>
                                                <li>Elcop</li>
                                                <li>Elcop2</li>
                                                <li>Jobin</li>
                                                <li>Yeet-sass</li>
                                            </ul>
                                        </div>

                                        <div class="card-tasks" title="Tasks from projects above">
                                                <h4>Tasks</h4>
                                                <ul class="group-projects-dialog">

                                                    <li>Pricelist</li>
                                                    <li>Searcher</li>
                                                    <li>Grid.scss</li>
                                                    <li>Spacing.scss</li>
                                                    <li>Template</li>
                                                    <li>Error fixing</li>
                                                    <li>Maintenance</li>

                                                </ul>
                                        </div>

                                    </div>


                                    <div class="card-footer">
                                        <div>
                                            <p><span>Last activity: </span><i>18:44</i></p>
                                            <p><span>Created at: </span><i><?php echo $group->date_created; ?></i></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>

                    </div>

                    <button class="carousel-next-btn" style="display: none;">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <button class="carousel-previous-btn" style="display: none;">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <ol class="carousel-card-dots">
                        <li class="dot"></li>
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

