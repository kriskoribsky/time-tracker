<?php
// 3 main front-controller functionalities: templating, routing, and security

class Router {

    private static array $allowed_pages = [
        'dashboard',
        'session',
        'task',
        '404'
    ];

    public function __construct() {}

    private function parse_uri(string $uri): array {
        return parse_url($uri);
    }

    public function process_uri(string $uri, array $get, array $post):void {
        $uri_parts = $this->parse_uri($uri);
        $path = $uri_parts['path'];

        switch($path) {
            case '/':
                // check if there is unexpired session
                if (isset($_SESSION['expire']) && $_SESSION['expire'] > time()) {
                    require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'dashboard.php');
                } else {
                    // unset expired session
                    session_unset();
                    require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'project_groups.php');
                }
                break;
            case '/project-groups':
                require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'project_groups.php');
                break;
            case '/dashboard':
                require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'dashboard.php');
                break;
            case '/sessions':
                require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'sessions.php');
                break;
            case '/tasks':
                require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'tasks.php');
                break;
            case '/forms':
                require_once Helper\Path::build_path(PRIVATE_PATH, 'controller', 'forms.php');
                break;
            case '/configuration':
                require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'configuration.php');
                break;
            default:
                require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'errors', '404.php');
        }
    }
}