<?php

namespace App\Controllers\Admin;

use App\Models\User;

class UserController extends AdminController
{
    public function index(): void
    {
        $users = User::all();
        $message = $this->pullFlash('message');

        $this->view('admin/users/index', compact('users', 'message'));
    }

    public function edit(int $id): void
    {
        $user = User::find($id);
        if (!$user) {
            $this->redirect('/admin/users');
        }

        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData') ?? $user;

        $this->view('admin/users/edit', compact('errors', 'formData'));
    }

    public function update(int $id): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'role_id' => ['required', 'integer']
        ]);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect("/admin/users/{$id}/edit");
        }

        User::updateRole($id, (int)$payload['role_id']);
        $this->flash('message', 'Роль користувача оновлено.');
        $this->redirect('/admin/users');
    }

    public function destroy(int $id): void
    {
        User::delete($id);
        $this->flash('message', 'Користувача видалено.');
        $this->redirect('/admin/users');
    }
}
