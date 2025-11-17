<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Support;

class SupportController extends Controller
{
    public function index(): void
    {
        $this->requireRole([1]); // Only admins

        $tickets = Support::findAll(50, 0);

        $this->view('admin/support/index', compact('tickets'));
    }

    public function show(int $id): void
    {
        $this->requireRole([1]);

        $ticket = Support::findById($id);

        if (!$ticket) {
            http_response_code(404);
            $this->view('errors/404');
            exit;
        }

        $this->view('admin/support/show', compact('ticket'));
    }

    public function respond(int $id): void
    {
        $this->requireRole([1]);

        $ticket = Support::findById($id);

        if (!$ticket) {
            http_response_code(404);
            $this->view('errors/404');
            exit;
        }

        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'response' => ['required', 'min:10'],
            'status' => ['required'],
        ]);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->redirect("/admin/support/{$id}");
            return;
        }

        Support::update($id, [
            'response' => $payload['response'],
            'status' => $payload['status'],
        ]);

        $this->flash('message', 'Відповідь надіслана');
        $this->redirect("/admin/support/{$id}");
    }

    public function updateStatus(int $id): void
    {
        $this->requireRole([1]);

        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'status' => ['required'],
        ]);

        if ($errors) {
            $this->flash('message', 'Помилка валідації');
            $this->redirect("/admin/support/{$id}");
            return;
        }

        Support::update($id, ['status' => $payload['status']]);

        $this->flash('message', 'Статус оновлено');
        $this->redirect("/admin/support/{$id}");
    }

    public function delete(int $id): void
    {
        $this->requireRole([1]);

        Support::delete($id);

        $this->flash('message', 'Заявка видалена');
        $this->redirect('/admin/support');
    }
}
