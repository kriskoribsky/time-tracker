<?php
namespace Helper;

class Path {
    private function __construct() {}

    public static function build_path(string ...$segments): string {
        return join(DIRECTORY_SEPARATOR, $segments);
    }
}