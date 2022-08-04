<?php
// 3 main front-controller functionalities: templating, routing, and security

class Router {

    private static array $allowed_requests = [
        'dashboard',
        'session',
        'task'
    ];

    public function __construct() {}

    // public function 


    public function process_uri(string $uri):void {
        echo $uri;
    }
}