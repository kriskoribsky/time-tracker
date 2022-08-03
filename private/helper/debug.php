<?php 
namespace Helper;

class Debug {
    private static array $logs = [];

    private function __construct() {}

    public static function log(mixed $var, bool $exit = false): void {
        
        echo    '<br>
                <fieldset style="border: 2px groove red; padding: 0 2rem">
                <legend style="color: red; font-weight: bold">Debug</legend>
                <pre style="text-align:left; font-size:12px">';

        switch (gettype($var)) {
            case 'array':
            case 'object':
                echo htmlentities(print_r($var, true));
                break;
            case 'string':
                echo 'string(' . strlen($var) . ') "' . htmlentities($var) . '"';
                break;
            default:
                var_dump($var);
        }

        if ($exit) {
            echo "<br><u>Exit called from:</u><br>\t";

            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            exit('<br><strong>Execution explicitly terminated using <em>' . __METHOD__ . ' </em>method.</strong>');
        }
        echo '</pre></fieldset>';
    }
}