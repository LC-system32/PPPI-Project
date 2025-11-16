<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductReturn;
use App\Models\Order;
use App\Models\User;

class ReturnController extends Controller
{
    /**
     * Show list of user's returns
     */
    public function index(): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/auth');
            return;
        }

        $returns = ProductReturn::getByUser($user['id']);
        $this->view('returns/index', compact('user', 'returns'));
    }

    /**
     * Show form to create a new return request
     */
    public function create(): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/auth');
            return;
        }

        // Get user's orders that are eligible for return
        $orders = Order::byUser($user['id'], 100);
        
        // Filter: only orders within 14 days
        $eligibleOrders = [];
        foreach ($orders as $order) {
            if (ProductReturn::isEligibleForReturn($order['id'])) {
                $eligibleOrders[] = $order;
            }
        }

        $reasons = [
            'defect' => 'Товар має дефект',
            'not_matching' => 'Товар не відповідає опису',
            'damaged' => 'Товар пошкоджений при доставці',
            'not_needed' => 'Передумала, не потрібен',
            'exchange' => 'Бажаю обміняти на інший розмір/колір',
        ];

        $returnMethods = [
            'courier' => 'Кур\'єр (Nous Logistics)',
            'nova_poshta' => 'Нова Пошта',
            'pickup' => 'Самовивіз',
        ];

        $errors = $this->pullFlash('errors') ?? [];
        $message = $this->pullFlash('message') ?? null;

        $this->view('returns/create', compact('user', 'eligibleOrders', 'reasons', 'returnMethods', 'errors', 'message'));
    }

    /**
     * Store a new return request
     */
    public function store(): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/auth');
            return;
        }

        $orderId = $_POST['order_id'] ?? null;
        $reason = $_POST['reason'] ?? null;
        $description = $_POST['description'] ?? null;
        $returnMethod = $_POST['return_method'] ?? 'courier';
        $selectedItems = $_POST['items'] ?? [];

        if (!$orderId || !$reason) {
            $this->flash('errors', ['Заповніть обов\'язкові поля']);
            $this->redirect('/returns/create');
            return;
        }

        // Verify order exists and belongs to user
        $orders = Order::byUser($user['id'], 100);
        $order = null;
        foreach ($orders as $o) {
            if ($o['id'] == $orderId) {
                $order = $o;
                break;
            }
        }

        if (!$order) {
            $this->flash('errors', ['Замовлення не знайдене']);
            $this->redirect('/returns');
            return;
        }

        if (!ProductReturn::isEligibleForReturn($orderId)) {
            $this->flash('errors', ['Період повернення товару закінчився (14 днів)']);
            $this->redirect('/returns');
            return;
        }

        // Create return request
        $returnData = ProductReturn::create([
            'order_id' => $orderId,
            'user_id' => $user['id'],
            'reason' => $reason,
            'description' => $description ?: null,
            'items' => !empty($selectedItems) ? $selectedItems : null,
            'return_method' => $returnMethod,
        ]);

        if ($returnData) {
            // Update order status to indicate a return was requested so staff can act on it
            try {
                Order::updateStatus((int)$orderId, 'return_requested');
            } catch (\Throwable $e) {
                // If update fails, continue but log could be added here
            }

            $this->flash('message', 'Запит на повернення товару прийнято! Ми зв\'яжемося з вами найближчим часом.');
            $this->redirect('/returns');
            return;
        }

        $this->flash('errors', ['Помилка при створенні запиту на повернення']);
        $this->redirect('/returns/create');
    }

    /**
     * Show return request details
     */
    public function show($id): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/auth');
            return;
        }

        $return = ProductReturn::findWithItems((int)$id);
        if (!$return || ($return['user_id'] !== $user['id'] && $user['role_id'] != 1)) {
            $this->view('errors/403');
            return;
        }

        $this->view('returns/show', compact('user', 'return'));
    }

    /**
     * Admin: List all returns
     */
    public function adminIndex(): void
    {
        $user = $this->user();
        if (!$user || $user['role_id'] != 1) {
            $this->view('errors/403');
            return;
        }

        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $returns = ProductReturn::getAllPending($limit, $offset);
        $stats = ProductReturn::getStats();

        $this->view('admin/returns/index', compact('returns', 'stats', 'page'));
    }

    /**
     * Admin: Update return status
     */
    public function adminUpdateStatus(): void
    {
        $user = $this->user();
        if (!$user || $user['role_id'] != 1) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $returnId = $_POST['return_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $comment = $_POST['comment'] ?? null;

        if (!$returnId || !$status) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $validStatuses = ['pending', 'approved', 'rejected', 'received', 'completed'];
        if (!in_array($status, $validStatuses)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Invalid status']);
            return;
        }

        $success = ProductReturn::updateStatus($returnId, $status, $comment);

        if ($success) {
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Статус оновлено']);
            return;
        }

        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update status']);
    }
}
