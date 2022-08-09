<?php
class Format {

    private function __construct() {}


    public static function valid_time(string $format, string $time): DateTime|false {
        $datetime_object = DateTime::createFromFormat($format, $time);

        return $datetime_object && $datetime_object->format($format) === $time ? $datetime_object : false;
    }

    // returns hours:minutes
    public static function subtract_hours_minutes(DateTime $start, DateTime $end): string {
        $diff_interval = $end->diff($start);

        $hours = (string) $diff_interval->h;

        $minutes = $diff_interval->i;
        $minutes = $minutes < 10 ? '0' . (string) $minutes : (string) $minutes;

        return $hours . ':' . $minutes;
    }

    // returns number of seconds
    public static function subtract_seconds(DateTime $start, DateTime $end): int {
        $diff_interval = $end->diff($start);

        $hours = $diff_interval->h;
        $minutes = $diff_interval->i;

        return ($hours * 60 * 60) + ($minutes * 60);
    }

    // returns string in the format %H hours : %i minutes
    public static function format_seconds(int $seconds): string {
        $hours_minutes = explode(':', gmdate('G:i', $seconds));

        return $hours_minutes[0] . ' hours ' . $hours_minutes[1] . ' minutes';
    }

    // dynamically generates sidebar nav secondary gradient color based on primary
    public static function generate_secondary_gradient_clr(string $primary_clr): string {

        $primary_clr = ltrim($primary_clr, '#');

        $rgb = array_map(fn($substr) => hexdec(str_pad($substr, 2, $substr)), str_split($primary_clr, strlen($primary_clr) > 3 ? 2 : 1));

        // create darker tone (if is the result would be negative it adds instead of subtraction => lighter tone)
        // rgb offsets [r, g, b]
        $offsets = [-44, -41, -33];
        $negative = 1;

        for ($i = 0; $i < count($rgb); $i++) {
            if ($rgb[$i] + $offsets[$i] < 0) {
                $negative = -1;
                break;
            }
        }

        $rgb[0] += $negative * $offsets[0];
        $rgb[1] += $negative * $offsets[1];
        $rgb[2] += $negative * $offsets[2];


        return '#' . join('', array_map(fn($subpart) => dechex($subpart), $rgb));
    }

}