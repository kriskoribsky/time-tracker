<?php
final class Database {

    private static $database_connection = false;

    //  Data Source Name
    public const DNS = DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR;

    public const OPTIONS = [
        PDO::ATTR_PERSISTENT => true,
        PDO::ERRMODE_EXCEPTION => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ];

    // for error catching
    public const MYSQL_DUPLICATE_CODES = [1062, 23000];
    
    private function __construct() {}

    // prevent multiple connections to database
    private static function connect(): PDO | false {
        if (!self::$database_connection) {
            // Helper\Debug::log('connecting to db');


            $pdo = new PDO(self::DNS, DB_USER, DB_PASS, self::OPTIONS);
            self::$database_connection = $pdo;
        }
        return self::$database_connection;
    }

    public static function query(string $sql, array $placeholder_values = null, int $fetch_mode = PDO::FETCH_DEFAULT): mixed {
        // Helper\Debug::log('gettting conection from db');
        $connection = self::connect();

        if ($placeholder_values) {
            // Helper\Debug::log('prepare query');
            $pdo_statement = $connection->prepare($sql);
            $pdo_statement->execute($placeholder_values);
        }
        else {
            // Helper\Debug::log('classic query');
            $pdo_statement = $connection->query($sql);
        }

        $res = $pdo_statement->fetchAll($fetch_mode);

        return $res;
    }
}