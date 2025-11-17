<?php

namespace App\Controllers\Admin;

use App\Models\Review;
use App\Models\Product;

class ReviewController extends AdminController
{
    public function index(): void
    {
        $page = max((int) ($_GET['page'] ?? 1), 1);
        $perPage = 20;

        $status = trim((string)($_GET['status'] ?? 'pending'));
        if ($status === '') $status = 'pending';
        // allow 'all' to list everything
        $listStatus = $status === 'all' ? '' : $status;
        $reviewsPage = Review::listByStatus($listStatus, $page, $perPage);
        $message = $this->pullFlash('message');

        // simple stats
        $stats = [
            'pending' => Review::countByStatus('pending'),
            'approved' => Review::countByStatus('approved'),
            'rejected' => Review::countByStatus('rejected'),
        ];

        $this->view('admin/reviews/index', compact('reviewsPage', 'message', 'stats'));
    }

    public function show(int $id): void
    {
        $review = Review::find($id);
        if (!$review) {
            $this->redirect('/admin/reviews');
        }

        $this->view('admin/reviews/show', compact('review'));
    }

    public function approve(int $id): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
        ]);

        if ($errors) {
            $this->flash('message', implode('\n', $errors));
            $this->redirect('/admin/reviews');
        }

        $ok = Review::updateStatus($id, 'approved');
        if ($ok) {
            $this->flash('message', 'Відгук схвалено.');
        } else {
            $this->flash('message', 'Не вдалося оновити статус відгуку.');
        }

        $this->redirect('/admin/reviews');
    }

    public function reject(int $id): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
        ]);

        if ($errors) {
            $this->flash('message', implode('\n', $errors));
            $this->redirect('/admin/reviews');
        }

        $ok = Review::updateStatus($id, 'rejected');
        if ($ok) {
            $this->flash('message', 'Відгук відхилено.');
        } else {
            $this->flash('message', 'Не вдалося оновити статус відгуку.');
        }

        $this->redirect('/admin/reviews');
    }
}
