<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOStatement;

abstract class Model
{
    protected static function db(): PDO
    {
        return Database::get();
    }

    protected static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }
}
