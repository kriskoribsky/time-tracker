<?php

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':

        switch ($_POST['submit']) {
            // create project-group
            case 'new-group':
                try {
                    Database::query('INSERT INTO project_groups(title, primary_color) VALUES(:title, :color)', [
                        'title' => $_POST['group-name'],
                        'color' => $_POST['group-color']
                        ]);

                } catch (PDOException $e) {
                    if (in_array($e->getCode(), Database::MYSQL_DUPLICATE_CODES)) {
                        header('Location: /?m=duplicate&o=project group');
                        exit();
                    } else {
                        header('Location: /?m=failed');
                        exit();
                    }
                }

                header('Location: /');
                exit();
                break;

            // create project
            case 'new-project':
                
                try {
                    // retrieve group id from the current session
                    $id = $_SESSION['group_id'];

                    Database::query('INSERT INTO projects(project_group_id, title) VALUES(:group_id, :title)', [
                        'group_id' => $id,
                        'title' => $_POST['project-name']
                        ]);
                } catch (PDOException $e) {
                    if (in_array($e->getCode(), Database::MYSQL_DUPLICATE_CODES)) {
                        header('Location: /dashboard?m=duplicate&o=project');
                        exit();
                    } else {
                        header('Location: /dashboard?m=failed');
                        exit();
                    }
                }

                header('Location: /dashboard');
                exit();
                break;

            // checkout time
            case 'time-checkout':

                try {
                    // go trough all sessions of the given project-group and reset 'gross_checkout_time' as well as 'net_checkout_time'
                    $projects = Database::query('SELECT * from projects WHERE project_group_id=:group_id', [
                        'group_id' => $_POST['group_id']], PDO::FETCH_OBJ);

                    foreach ($projects as $project) {
                        $tasks = Database::query('SELECT * from tasks WHERE project_id=:project_id', [
                            'project_id' => $project->id], PDO::FETCH_OBJ);

                        foreach ($tasks as $task) {
                            $sql = 'UPDATE sessions SET gross_checkout_time=:no_gross_time, net_checkout_time=:no_net_time WHERE task_id=:task_id';
                            Database::query($sql, [
                                'no_gross_time' => 0,
                                'no_net_time' => 0, 
                                'task_id' => $task->id
                                ]);
                        }
                    }


                } catch (PDOException $e) {
                    header('Location: /dashboard?m=failed');
                    exit();
                }

                header('Location: /dashboard');
                exit();                
                break;

            // add task
            case 'new-task':

                try {
                    Database::query('INSERT INTO tasks(project_id, title) VALUES(:project_id, :title)', [
                        'project_id' => $_POST['project-id'],
                        'title' => $_POST['task-name']
                    ]);

                } catch (PDOException $e) {
                    if (in_array($e->getCode(), Database::MYSQL_DUPLICATE_CODES)) {
                        header('Location: /tasks?m=duplicate&o=task');
                        exit();
                    } else {
                        header('Location: /tasks?m=failed');
                        exit();
                    }
                }

                header('Location: /tasks?m=ok&o=task');
                exit();
                break;

            // add session
            case 'new-session':

                try {
                    // input validation
                    // start and end times will be further validated
                    assert(preg_match('/^\d\d?:\d\d$/', $_POST['start-time']) === 1);
                    assert(preg_match('/^\d\d?:\d\d$/', $_POST['end-time']) === 1);

                    assert(preg_match('/^\d{1,3}$/', $_POST['net-working-time']) === 1);

                    // convert start and end-times to calculate total time worked
                    // $start = DateTime::createFromFormat('G:i', $_POST['start-time']);
                    $start = Format::valid_time('G:i', $_POST['start-time']);
                    $end = Format::valid_time('G:i', $_POST['end-time']);

                    assert($start !== false);
                    assert($end !== false);

                    if (isset($_POST['next-day']) && $_POST['next-day'] == 'true') {
                        $end = $end->modify('+1 day');
                    } elseif (!empty($_POST['next-day']) && $_POST['next-day'] != 'true') {
                        throw new AssertionError('value of next-day input must be true');
                    }

                    $gross_code_seconds = Format::subtract_seconds($start, $end);
                    $gross_code_formatted = Format::format_seconds($gross_code_seconds);
                    $gross_checkout_time = $gross_code_seconds;

                    $ratio = (float) $_POST['net-working-time'] / 100;

                    $net_code_seconds = round($gross_code_seconds * $ratio);
                    $net_code_formatted = Format::format_seconds((int) $net_code_seconds);
                    $net_checkout_time = $net_code_seconds;

                    $sql = 'INSERT INTO sessions(task_id, start, end, next_day, gross_time, gross_formatted_time, gross_checkout_time, net_time_ratio, net_time, net_formatted_time, net_checkout_time, note) VALUES (:task_id, :start, :end, :next_day, :gross_time, :gross_formatted_time, :gross_checkout_time, :net_time_ratio, :net_time, :net_formatted_time, :net_checkout_time, :note)';

                    $placeholder_values = [
                        'task_id' => $_POST['task-id'],
                        'start' => $start->format('G:i'),
                        'end' => $end->format('G:i'),
                        'next_day' => isset($_POST['next-day']) && $_POST['next-day'] == 'true' ? 1 : 0,
                        'gross_time' => $gross_code_seconds,
                        'gross_formatted_time' => $gross_code_formatted,
                        'gross_checkout_time' => $gross_checkout_time,
                        'net_time_ratio' => $ratio,
                        'net_time' => $net_code_seconds,
                        'net_formatted_time' => $net_code_formatted,
                        'net_checkout_time' => $net_checkout_time,
                        'note' => $_POST['note'] ?? NULL
                    ];



                    // Backup session data to external file (currently disabled)
                    // ==========================================================================
                    // $task = Database::query('SELECT title, project_id FROM tasks WHERE id=?', [$_POST['task-id']], PDO::FETCH_OBJ)[0];

                    // $project = Database::query('SELECT title, project_group_id FROM projects WHERE id=?', [$task->project_id], PDO::FETCH_OBJ)[0];

                    // $group = Database::query('SELECT title FROM project_groups WHERE id=?', [$project->project_group_id], PDO::FETCH_OBJ)[0];

                    // // backup export values
                    // $db_export = array_merge($placeholder_values, [Date('Y-m-d H:i:s')]);
                    // $readable_export = [
                    //     Date('d-m'),
                    //     $group->title,
                    //     $project->title,
                    //     $task->title,
                    //     $placeholder_values['start'],
                    //     $placeholder_values['end'],
                    //     $placeholder_values['gross_formatted_time'],
                    //     $placeholder_values['net_time_ratio'],
                    //     $placeholder_values['net_formatted_time'],
                    //     $placeholder_values['note']
                    // ];

                    // // export to global sessions .csv file
                    // $global = new Export();
                    // $global->export_session($readable_export, $db_export, Export::THROW_ERROR_ON_MISSING_FILE);

                    // // export to individual project group sessions.csv file
                    // $group = new Export(ROOT_PATH . '/export/project groups/' .  $group->title . '-sessions.csv');
                    // $group->export_session($readable_export, $db_export, Export::CREATE_MISSING_FILE);



                    // Insert session into database
                    // ==========================================================================
                    Database::query($sql, $placeholder_values);



                } catch (Exception | PDOException) {
                    header('Location: /sessions?m=failed');
                    exit();

                } catch (AssertionError) {
                    header('Location: /sessions?m=wrong-format');
                    exit();
                }

                header('Location: /sessions?m=ok&o=session');
                exit();
                break;


            // changes in configuration
            case 'configuration-change':
                try {
                    // salary config option
                    if (isset($_POST['salary'])) {
                        Database::query('UPDATE app_config SET value=:amount WHERE option_name=:config_option', [
                            'config_option' => 'salary_rate',
                            'amount' => (float) round($_POST['salary'], 1)
                        ]);
                    }

                    // show net coding-time config option
                    $show_net = isset($_POST['show-net-format']) ? 1 : 0; 

                    Database::query('UPDATE app_config SET value=:amount WHERE option_name=:config_option', [
                        'config_option' => 'display_net_time',
                        'amount' => $show_net
                    ]);
    
                } catch (PDOException $e) {
                    header('Location: /configuration?m=failed');
                }

                header('Location: /configuration?m=ok&o=config options');
                exit();
                break;

            default:
                header('Location: /error');
                exit();
        }

    case 'GET':
        header('Location: /error');
        exit();

    default:
        header('Location: /error');
        exit();
}