<?php
// Export session data to backup file
// ==========================================================================

use Helper\Path;

class Export {

    const DEFAULT_EXPORT_PATH = EXPORT_PATH;

    const THROW_ERROR_ON_MISSING_FILE = 1;
    const CREATE_MISSING_FILE = 2;

    // formatted readable columns
    const FORMATTED_COLUMNS = [
        'Date',
        'Group',
        'Project',
        'Task',
        'Start',
        'End',
        'Total t',
        'Ratio',
        'Net t',
        'Note'
    ];

    // database session columns replication`
    const DB_COLUMNS = [
        'id', 
        'start',
        'end',
        'next_day',
        'gross_time',
        'gross_formatted_time',
        'gross_checkout_time',
        'net_time_ratio',
        'net_time',
        'net_formatted_time',
        'net_checkout_time',
        'note', 
        'date_created'
    ];

    private string $path;

    public function __construct(string $path = self::DEFAULT_EXPORT_PATH) {
        $this->path = $path;
    }

    // file exists check, create file / return file handle
    private function get_file(int $mode) {

        if (!file_exists($this->path) && $mode === self::THROW_ERROR_ON_MISSING_FILE) {
            throw new Exception('Saving to backup failed: file ' . $this->path . ' doesn\'t exist.');
        }

        $mode = file_exists($this->path) ? 'a' : 'w';
        $handle = fopen($this->path, $mode);

        if(!$handle) {
            throw new Exception('Saving session data to backup file failed.');
        }
        return $handle;
    }

    private function placeholder_array(int $length): array {
        $arr = [];

        for ($i = 0; $i < $length; $i++) {
            $arr[] = '';
        }
        return $arr;
    }

    public function export_session(array $formatted_columns, array $db_columns, int $mode) {
        $existed = file_exists($this->path);
        // file pointer
        $fp = $this->get_file($mode);

        $column_sections_space = $this->placeholder_array(3);

        // if file was created create new column headings
        if (!$existed) {
            // heading for each column section
            fputcsv($fp, array_merge(['Formatted'], $this->placeholder_array(count($formatted_columns) - 1 + 3), ['Database']), ';');

            // formatted columns + 3 cell space + db columns
            fputcsv($fp, array_merge(self::FORMATTED_COLUMNS, ['', '', ''], self::DB_COLUMNS), ';');
        }

        fputcsv($fp, array_merge($formatted_columns, $column_sections_space, $db_columns), ';');

        fclose($fp);
    }
}