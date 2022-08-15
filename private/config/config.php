<?php
// Paths
// ==========================================================================
// root path = server root + uri
define('ROOT_PATH', realpath(dirname($_SERVER['DOCUMENT_ROOT'])));
// pravite path = path of this file one dir up
define('PRIVATE_PATH', realpath(dirname(__DIR__)));
// public path = path of index.php = current working directory
define('PUBLIC_PATH', realpath(getcwd()));

// Helpers
// ==========================================================================
const HELPERS = ['debug.php', 'path.php'];

// Database
// ==========================================================================
const DB_TYPE = 'mysql';
const DB_HOST = 'localhost';
const DB_NAME = 'time_tracker';
const DB_CHAR = 'utf8mb4';
const DB_USER = 'time-tracker-admin';
const DB_PASS = 'D)2M*pDyuV0IAKqI';

// Backup
// ==========================================================================
const EXPORT_PATH = ROOT_PATH . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . 'allgroups-sessions.csv';

// App functionality
// ==========================================================================
const DEFAULT_SALARY = null;
const DEFAULT_TIME_AMOUNT = 'net';
// default color of sidebar nav for every project group
const DEFAULT_PRIMARY_CLR = '#4e73df';