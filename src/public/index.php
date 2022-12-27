<?php
// Configuration
// ==========================================================================
declare(strict_types=1);
require_once('..' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

// Helpers
// ==========================================================================
require_once(PRIVATE_PATH . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'helper.init.php');

// Modules
// ==========================================================================
$modules = [
    'db_object_mapping',
    'router',
    'database',
    'sanitize',
    'format',
    'export'
];

foreach ($modules as $module) {
    require_once Helper\Path::build_path(PRIVATE_PATH, 'modules', $module . '.php');
}

// Start session
// ==========================================================================
session_start();

// Page routing
// ==========================================================================
require_once(Helper\Path::build_path(PRIVATE_PATH, 'modules', 'router.php'));

$router = new Router();
$router->process_uri($_SERVER['REQUEST_URI'], $_GET, $_POST);