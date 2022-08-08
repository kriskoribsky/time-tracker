<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time-tracker</title>

     <!-- description is important for SEO purposes (should be 155 characters at max) -->
    <meta name="description" content="Web app for tracking time spent on activities. Create an account and manage your time. Includes various time tracking tools. Originally designed for work-time / time spent coding hours for remotely working programmers.">

      <!-- Always force latest IE rendering engine or request Chrome Frame -->
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:title" content="">
    <meta property="og:type" content="">
    <meta property="og:url" content="">
    <meta property="og:image" content="">

    <!-- -------------------------------------------------------------------------------- -->
    <!-- everything that is needed for icons: (from https://medium.com/swlh/are-you-using-svg-favicons-yet-a-guide-for-modern-browsers-836a6aace3df) -->
    <!-- <meta name="theme-color" content="#ffffff"> -->

    <!-- svg of any size for main icon: -->
    <!-- <link rel="icon" href="favicon.svg" sizes="any" type=”image/svg+xml” > -->

    <!-- mask icon (Safari) has to be made of a single colour and placed on a transparent background
    the browser adds the colour of the attribute. -->
    <!-- <link rel="mask-icon" href="mask-icon.svg" color="#000000"> -->

    <!-- (for iOS), only the 180 x 180 size is needed, and the sizes attribute is superfluous -->
    <!-- <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="manifest" href="site.webmanifest"> -->
    <!-- and at last, place favicon.ico in the root directory (for older browser support) -->
    <!-- this favicon will be in .ico format (containing various sizes), use this generator: https://realfavicongenerator.net/ -->
    <!-- good resource is also this favicon checker: https://realfavicongenerator.net/favicon_checker#.YpZIKmhByUk -->
    <!-- -------------------------------------------------------------------------------- -->

    <!-- favicons HTML generated from https://realfavicongenerator.net/ -->
    <!-- <link rel="apple-touch-icon" sizes="180x180" href="img/generated favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/generated favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/generated favicons/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="img/generated favicons/safari-pinned-tab.svg" color="#31ffaf">
    <meta name="apple-mobile-web-app-title" content="InterFinance">
    <meta name="application-name" content="InterFinance">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#da2c2c"> -->

    <!-- stylesheets -->
    <link rel="stylesheet" href="/view/css/normalize.css">
    <link rel="stylesheet" href="/view/css/main.css?<?php echo time(); ?>">

    <!-- font awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">

    <!-- scripts -->
    <script src="/view/js/main.js?<?php echo time(); ?>" defer></script>

    <!-- favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/view/assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/view/assets/img/favicons/favicon-16x16.png">

</head>
<body class="wrapper">

    <?php require_once 'inc/modals.php'; ?>

    <header>

        <nav class="side-nav bg-gradient-primary">

            <h1 class="main-logo">
                <a href="/">
                    <i class="fa-solid fa-clock"></i>
                    <span>Time-tracker</span>
                </a>
            </h1>

            <ul class="nav">

                <div class="nav-section">
                    <hr class="nav-divide">
                </div>

                <li>
                    <a href="/dashboard" class="<?php echo ($_SERVER['REQUEST_URI'] == '/dashboard' || $_SERVER['REQUEST_URI'] == '/') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <div class="nav-section">
                    <hr class="nav-divide">
                    <p class="nav-section-name">Tracking</p>
                </div>

                <li>
                    <a href="/tasks" class="<?php echo $_SERVER['REQUEST_URI'] == '/tasks' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-file-circle-plus"></i>
                        <span>Manage tasks</span>
                    </a>
                </li>

                <li>
                    <a href="/sessions" class="<?php echo $_SERVER['REQUEST_URI'] == '/sessions' ? 'active' : ''; ?>">
                        <i class="fa-regular fa-square-plus"></i>
                        <span>Add session</span>
                    </a>
                </li>

                <div class="nav-section">
                    <hr class="nav-divide">
                    <p class="nav-section-name">Data</p>
                </div>

                <li>
                    <a href="#">
                        <i class="fa-solid fa-chart-simple"></i>
                        <span>Charts</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-table-columns"></i>
                        <span>Tables</span>
                    </a>
                </li>

                <div class="nav-section">
                    <hr class="nav-divide">
                    <p class="nav-section-name">Other</p>
                </div>

                <li>
                    <a href="/configuration" class="<?php echo $_SERVER['REQUEST_URI'] == '/configuration' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                        <span>Configuration</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fa-solid fa-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>

            </ul>

        </nav>

    </header>

    <div class="main-footer">

        <main>

            <ul class="top-nav shadow">

                <li>

                    <form action="">

                        <input type="text" placeholder="Search for..."><button><i class="fa-solid fa-magnifying-glass"></i></button>

                    </form>

                </li>

                <li class="user">
                    <a href="">
                        <span>Kristián Koribský</span>
                        <img src="/view/assets/img/undraw_profile.svg" alt="profile picture">
                    </a>
                </li>

            </ul>