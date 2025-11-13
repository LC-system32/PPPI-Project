<?php
namespace App\Models;

use PDO;

class User
{
    public int $id;
    public string $login;
    public string $email;
    public string $password_hash;
    public ?int $role_id = null;

    public static function findByEmail(PDO $db, string $email): ?self
    {
        return self::fetchOne($db, 'SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
    }

    public static function findByLogin(PDO $db, string $login): ?self
    {
        return self::fetchOne($db, 'SELECT * FROM users WHERE login = :login LIMIT 1', ['login' => $login]);
    }

    public static function findById(PDO $db, int $id): ?self
    {
        return self::fetchOne($db, 'SELECT * FROM users WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public static function create(PDO $db, array $data): ?self
    {
        $stmt = $db->prepare(
            'INSERT INTO users (login, email, password_hash, role_id, created_at, updated_at)
             VALUES (:login, :email, :password_hash, :role_id, :created_at, :updated_at)
             RETURNING id, login, email, password_hash, role_id'
        );

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt->execute([
            'login' => $data['login'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'role_id' => $data['role_id'] ?? 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    public static function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function verifyPassword(string $plain): bool
    {
        return password_verify($plain, $this->password_hash);
    }

    private static function fetchOne(PDO $db, string $sql, array $params): ?self
    {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    public static function updateProfile(PDO $db, int $id, string $login, string $email): bool
    {
        $stmt = $db->prepare(
            'UPDATE users
             SET login = :login, email = :email, updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'login' => $login,
            'email' => $email,
            'id' => $id,
        ]);
    }

    public static function updatePassword(PDO $db, int $id, string $hash): bool
    {
        $stmt = $db->prepare(
            'UPDATE users
             SET password_hash = :hash, updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'hash' => $hash,
            'id' => $id,
        ]);
    }

    private static function fromRow(array $row): self
    {
        $user = new self();
        foreach ($row as $key => $value) {
            if (property_exists($user, $key)) {
                $user->$key = $value;
            }
        }

        return $user;
    }
}
