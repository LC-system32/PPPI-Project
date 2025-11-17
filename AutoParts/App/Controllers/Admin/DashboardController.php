<?php

namespace App\Controllers\Admin;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $db = $this->db();

        $counts = [];

        $stmt = $db->query('SELECT COUNT(*) AS cnt FROM products');
        $counts['products'] = (int) ($stmt->fetchColumn() ?: 0);

        $stmt = $db->query('SELECT COUNT(*) AS cnt FROM categories');
        $counts['categories'] = (int) ($stmt->fetchColumn() ?: 0);

        $stmt = $db->query('SELECT COUNT(*) AS cnt FROM brands');
        $counts['brands'] = (int) ($stmt->fetchColumn() ?: 0);

        $stmt = $db->query('SELECT COUNT(*) AS cnt FROM car_models');
        $counts['car_models'] = (int) ($stmt->fetchColumn() ?: 0);

        $stmt = $db->query('SELECT COUNT(*) AS cnt FROM orders');
        $counts['orders'] = (int) ($stmt->fetchColumn() ?: 0);

        $stmt = $db->query('SELECT COUNT(*) AS cnt FROM returns');
        $counts['returns'] = (int) ($stmt->fetchColumn() ?: 0);

        $stmt = $db->query('SELECT COUNT(*) AS cnt FROM users');
        $counts['users'] = (int) ($stmt->fetchColumn() ?: 0);

        // Pending reviews count for moderation
        try {
            $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM product_reviews WHERE status = :status");
            $stmt->execute(['status' => 'pending']);
            $counts['reviews_pending'] = (int) ($stmt->fetchColumn() ?: 0);
        } catch (\PDOException $e) {
            // if table doesn't exist, default to 0
            $counts['reviews_pending'] = 0;
        }

        // recent orders
        $stmt = $db->prepare('SELECT id, user_id, total, status, created_at FROM orders ORDER BY created_at DESC LIMIT 6');
        $stmt->execute();
        $recentOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $this->view('admin/dashboard', compact('counts', 'recentOrders'));
    }
}
