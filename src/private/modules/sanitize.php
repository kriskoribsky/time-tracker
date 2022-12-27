<?php
class Sanitize {

    // private const HTML_SANITIZE_METHOD = 'htmlspecialchars';
    
    private function __construct() {}

    public static function sanitize_html(string $input):string {
        return htmlspecialchars($input, ENT_QUOTES);
    }

    public static function sanitize_html_query(array $queries):array {

        $sanitized_queries = [];

        for($i = 0; $i < count($queries); $i++) {
            foreach($queries[$i] as $key => $value) {
                $sanitized_queries[$i][$key] = self::sanitize_html($value);
            }
        }

        return $sanitized_queries;
    }
}