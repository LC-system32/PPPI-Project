<?php

namespace App\Models;

use DateTimeImmutable;
use PDO;

class User extends Model
{
    public int $id;
    public string $login;
    public string $email;
    public string $password_hash;
    public ?int $role_id = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $phone = null;
    public ?string $address = null;

    public static function findByEmail(string $email): ?self
    {
        return self::fetchOne('SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
    }

    public static function findByLogin(string $login): ?self
    {
        return self::fetchOne('SELECT * FROM users WHERE login = :login LIMIT 1', ['login' => $login]);
    }

    public static function findById(int $id): ?self
    {
        return self::fetchOne('SELECT * FROM users WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public static function create(array $data): ?self
    {
        $stmt = self::db()->prepare(
            'INSERT INTO users (login, email, password_hash, role_id, created_at, updated_at)
             VALUES (:login, :email, :password_hash, :role_id, :created_at, :updated_at)
             RETURNING id, login, email, password_hash, role_id'
        );

        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

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

    public static function updateProfile(int $id, string $login, string $email, ?string $firstName = null, ?string $lastName = null, ?string $phone = null, ?string $address = null): bool
    {
        $stmt = self::db()->prepare(
            'UPDATE users
             SET login = :login, email = :email, first_name = :first_name, last_name = :last_name, phone = :phone, address = :address, updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'login' => $login,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'address' => $address,
            'id' => $id,
        ]);
    }

    public static function updatePassword(int $id, string $hash): bool
    {
        $stmt = self::db()->prepare(
            'UPDATE users
             SET password_hash = :hash, updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'hash' => $hash,
            'id' => $id,
        ]);
    }

    public static function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function verifyPassword(string $plain): bool
    {
        return password_verify($plain, $this->password_hash);
    }

    private static function fetchOne(string $sql, array $params): ?self
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
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
