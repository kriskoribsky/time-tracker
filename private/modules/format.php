<?php
class Format {

    // text colors for color manipulation & calculating constrast color
    const TEXT_LIGHT = '#fff';
    const TEXT_DARK = '#212529';
    const OUTPUT_DATE_FORMAT = 'd.m.Y';



    private function __construct() {}

    public static function valid_time(string $format, string $time): DateTime|false {
        $datetime_object = DateTime::createFromFormat($format, $time);

        return $datetime_object && $datetime_object->format($format) === $time ? $datetime_object : false;
    }

    public static function database_time(string $time, string $format = self::OUTPUT_DATE_FORMAT): string {
        $datetime = new DateTime($time);

        return $datetime->format($format);
    }

    // returns last activty -> 'H:i' if on the same day else 'd-m-Y'
    public static function last_activity(string $time, string $format = self::OUTPUT_DATE_FORMAT): string {
        $datetime = new DateTime($time);
        $now = new DateTime();

        if ($datetime->format($format) !== $now->format($format)) {
            return $datetime->format($format);
        } else {
            return $datetime->format('H:i');
        }
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

        $rgb = self::hex_to_rgb($primary_clr);

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


        return self::rgb_to_hex($rgb);
    }

    public static function get_contrast_clr(string $background_clr): string {
        $rgb = self::hex_to_rgb($background_clr);

        return array_sum($rgb) > 450 ? self::TEXT_DARK : self::TEXT_LIGHT;
    }

    public static function hex_to_rgb(string $hex): array {
        $hex = ltrim($hex, '#');
        return array_map(fn($substr) => hexdec(str_pad($substr, 2, $substr)), str_split($hex, strlen($hex) > 3 ? 2 : 1));
    }

    public static function rgb_to_hex(array $rgb): string {
        return '#' . join('', array_map(fn($subpart) => dechex($subpart), $rgb));
    }

}