<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $config = require BASE_PATH . '/config/database.php';
        $connection = $config['connections'][$config['default']] ?? null;

        if (!$connection) {
            throw new \RuntimeException('Database connection configuration not found.');
        }

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s',
            $connection['driver'],
            $connection['host'],
            $connection['port'],
            $connection['database']
        );

        try {
            self::$pdo = new PDO(
                $dsn,
                $connection['username'],
                $connection['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage(), 0, $e);
        }

        return self::$pdo;
    }
}
