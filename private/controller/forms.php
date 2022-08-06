<?php

// Helper\Debug::log($_SERVER, true);
// Helper\Debug::log($_REQUEST);

// catch DB errors
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':

        switch ($_POST['submit']) {
            // create project
            case 'new-project':
                
                try {
                    Database::query('INSERT INTO projects(title) VALUES(:title)', ['title' => $_POST['project-name']]);
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


            // add session
            case 'new-session':

                Helper\Debug::log($_POST);

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

                    if (isset($_POST['next-day']) && $_POST['next-day'] == true) {
                        $end = $end->modify('+1 day');
                    }

                    // $gross_code_hours = Format::subtract_hours_minutes($start, $end);
                    $gross_code_seconds = Format::subtract_seconds($start, $end);
                    $gross_code_formatted = Format::format_seconds($gross_code_seconds);

                    $ratio = (float) $_POST['net-working-time'] / 100;

                    $net_code_seconds = round($gross_code_seconds * $ratio);
                    $net_code_formatted = Format::format_seconds((int) $net_code_seconds);


                    $sql = 'INSERT INTO () ';

                    // Database::query();

                } catch (PDOException $e) {
                    header('Location: /sessions?m=failed');
                    exit();

                } catch (AssertionError) {
                    header('Location: /sessions?m=wrong-format');
                    exit();
                }

                header('Location: /sessions?m=ok&o=task');
                exit();
            }

        break;

    case 'GET':
        break;
    default:
}