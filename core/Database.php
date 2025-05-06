<?php

namespace Database;

use PDO;
use PDOException;

class Database
{
    protected static $connection;

    public static function connect()
    {
        try {
            self::$connection = new PDO(
                'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';port=' . getenv('DB_PORT'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            );
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}
