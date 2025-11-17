<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

abstract class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole([1, 2]);
    }
}
