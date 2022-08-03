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
    'database'
];

foreach ($modules as $module) {
    require_once Helper\Path::build_path(PRIVATE_PATH, 'modules', $module . '.php');
}

// Landing page
// ==========================================================================
require_once(Helper\Path::build_path(PUBLIC_PATH, 'view', 'templates', 'dashboard.php'));