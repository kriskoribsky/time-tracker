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
const EXPORT_DIR = '';

