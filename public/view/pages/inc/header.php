<?php require_once 'inc/head.php'; ?>

<body class="wrapper">

    <?php require_once 'inc/modals.php'; ?>

    <header>

        <nav class="side-nav bg-gradient-primary">

            <h1 class="main-logo">
                <a href="/project-groups">
                    <i class="fa-solid fa-clock"></i>
                    <span><?php echo $_SESSION['group_title']; ?></span>
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