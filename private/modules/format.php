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

}