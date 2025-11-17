<?php

namespace App\Models;

use DateTimeImmutable;
use PDO;

class Support extends Model
{
    public int $id;
    public ?int $user_id = null;
    public string $ticket_number;
    public string $subject;
    public string $category;
    public string $priority;
    public string $description;
    public string $status;
    public ?string $response = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function findById(int $id): ?self
    {
        return self::fetchOne('SELECT * FROM support_tickets WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public static function findByTicketNumber(string $ticket_number): ?self
    {
        return self::fetchOne('SELECT * FROM support_tickets WHERE ticket_number = :ticket_number LIMIT 1', ['ticket_number' => $ticket_number]);
    }

    public static function findByUser(int $user_id): array
    {
        return self::fetch(
            'SELECT * FROM support_tickets WHERE user_id = :user_id ORDER BY created_at DESC',
            ['user_id' => $user_id]
        );
    }

    public static function findAll(int $limit = 50, int $offset = 0): array
    {
        return self::fetch(
            'SELECT * FROM support_tickets ORDER BY created_at DESC LIMIT :limit OFFSET :offset',
            ['limit' => $limit, 'offset' => $offset]
        );
    }

    public static function create(array $data): ?self
    {
        $ticketNumber = self::generateTicketNumber();
        $stmt = self::db()->prepare(
            'INSERT INTO support_tickets (user_id, ticket_number, subject, category, priority, description, status, created_at, updated_at)
             VALUES (:user_id, :ticket_number, :subject, :category, :priority, :description, :status, :created_at, :updated_at)
             RETURNING id, user_id, ticket_number, subject, category, priority, description, status, response, created_at, updated_at'
        );

        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt->execute([
            'user_id' => $data['user_id'] ?? null,
            'ticket_number' => $ticketNumber,
            'subject' => $data['subject'],
            'category' => $data['category'],
            'priority' => $data['priority'] ?? 'normal',
            'description' => $data['description'],
            'status' => 'open',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    public static function update(int $id, array $data): bool
    {
        $allowedFields = ['subject', 'category', 'priority', 'description', 'status', 'response'];
        $updates = [];
        $params = ['id' => $id];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($updates)) {
            return false;
        }

        $updates[] = 'updated_at = NOW()';
        $sql = 'UPDATE support_tickets SET ' . implode(', ', $updates) . ' WHERE id = :id';

        $stmt = self::db()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare('DELETE FROM support_tickets WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function generateTicketNumber(): string
    {
        // Format: SUP-YYMMDDHHMMSS + 4 hex chars (max 23 chars)
        $now = new DateTimeImmutable();
        $timestamp = $now->format('ymdHis');
        $random = strtoupper(bin2hex(random_bytes(2)));

        return "SUP-{$timestamp}{$random}";
    }

    public static function countByStatus(string $status): int
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) as count FROM support_tickets WHERE status = :status');
        $stmt->execute(['status' => $status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] ?? 0;
    }

    // For mapping row data to object
    protected static function fetchOne(string $sql, array $params = []): ?self
    {
        $stmt = self::query($sql, $params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    protected static function fetch(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => self::fromRow($row), $rows);
    }

    protected static function fromRow(array $row): self
    {
        $instance = new self();
        foreach ($row as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }

        return $instance;
    }
}
