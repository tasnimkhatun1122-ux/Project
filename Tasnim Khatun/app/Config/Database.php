<?php
class Database
{
    private static $connection = null;

    public static function getConnection()
    {
        if (self::$connection === null) {
            $host = "localhost";
            $user = "root";
            $pass = "";
            $name = "doctorconnect";

            self::$connection = new mysqli($host, $user, $pass, $name);

            if (self::$connection->connect_error) {
                die("Database connection failed: " . self::$connection->connect_error);
            }
            self::$connection->set_charset("utf8mb4");
        }
        return self::$connection;
    }
}
