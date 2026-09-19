<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class Connection
{
    private static ?PDO $instance = null;

    public static function get(): PDO
    {
        if (self::$instance === null) {
            $host   = $_ENV['DB_HOST']     ?? 'localhost';
            $port   = $_ENV['DB_PORT']     ?? '3306';
            $dbname = $_ENV['DB_DATABASE'] ?? 'inventory';
            $user   = $_ENV['DB_USERNAME'] ?? 'root';
            $pass   = $_ENV['DB_PASSWORD'] ?? '';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$instance;
    }
}
