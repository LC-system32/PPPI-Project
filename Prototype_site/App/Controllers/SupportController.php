<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Support;

class SupportController extends Controller
{
    public function index(): void
    {
        $user = $this->requireUser();
        $user_id = $user['id'] ?? null;
        $tickets = Support::findByUser($user_id);

        $this->view('support/index', compact('tickets'));
    }

    public function create(): void
    {
        $this->requireUser();

        $categories = [
            'technical' => 'Технічна проблема',
            'order' => 'Питання про замовлення',
            'product' => 'Питання про товар',
            'delivery' => 'Питання про доставку',
            'return' => 'Повернення/обмін',
            'other' => 'Інше',
        ];

        $priorities = [
            'low' => 'Низька',
            'normal' => 'Звичайна',
            'high' => 'Висока',
            'urgent' => 'Термінова',
        ];

        $message = $this->pullFlash('message');
        $errors = $this->pullFlash('errors') ?? [];
        $old = $this->pullFlash('old') ?? [];

        $this->view('support/create', compact('categories', 'priorities', 'message', 'errors', 'old'));
    }

    public function store(): void
    {
        $user = $this->requireUser();

        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'subject' => ['required', 'min:5', 'max:255'],
            'category' => ['required'],
            'priority' => ['required'],
            'description' => ['required', 'min:20'],
        ]);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('old', $payload);
            $this->redirect('/support/create');
            return;
        }

        $user_id = $user['id'] ?? null;

        $ticket = Support::create([
            'user_id' => $user_id,
            'subject' => $payload['subject'],
            'category' => $payload['category'],
            'priority' => $payload['priority'],
            'description' => $payload['description'],
        ]);

        if ($ticket) {
            $this->flash('message', "Заявка успішно створена. Номер: {$ticket->ticket_number}");
            $this->redirect('/support');
            return;
        }

        $this->flash('message', 'Помилка при створенні заявки. Спробуйте ще раз.');
        $this->redirect('/support/create');
    }

    public function show(int $id): void
    {
        $user = $this->requireUser();

        $ticket = Support::findById($id);

        if (!$ticket) {
            http_response_code(404);
            echo '404 Not Found';
            exit;
        }

        // Check authorization - user can only see their own tickets
        $user_id = $user['id'] ?? null;
        if ($ticket->user_id !== $user_id && $user['role_id'] !== 1) {
            http_response_code(403);
            echo '403 Forbidden';
            exit;
        }

        $this->view('support/show', compact('ticket'));
    }

    public function update(int $id): void
    {
        $user = $this->requireUser();

        $ticket = Support::findById($id);

        if (!$ticket) {
            http_response_code(404);
            echo '404 Not Found';
            exit;
        }

        // Check authorization - user can only update their own tickets
        $user_id = $user['id'] ?? null;
        if ($ticket->user_id !== $user_id && $user['role_id'] !== 1) {
            http_response_code(403);
            echo '403 Forbidden';
            exit;
        }

        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'description' => ['required', 'min:5'],
        ]);

        if ($errors) {
            $this->flash('message', 'Помилка валідації');
            $this->redirect("/support/{$id}");
            return;
        }

        // Users can only add to description
        $updated_description = $ticket->description . "\n\n---\n" . date('Y-m-d H:i:s') . " (Користувач):\n" . $payload['description'];

        Support::update($id, ['description' => $updated_description]);

        $this->flash('message', 'Заявка оновлена');
        $this->redirect("/support/{$id}");
    }

    public function close(int $id): void
    {
        $user = $this->requireUser();

        $ticket = Support::findById($id);

        if (!$ticket) {
            http_response_code(404);
            echo '404 Not Found';
            exit;
        }

        $user_id = $user['id'] ?? null;
        if ($ticket->user_id !== $user_id && $user['role_id'] !== 1) {
            http_response_code(403);
            echo '403 Forbidden';
            exit;
        }

        Support::update($id, ['status' => 'closed']);

        $this->flash('message', 'Заявка закрита');
        $this->redirect('/support');
    }
}
