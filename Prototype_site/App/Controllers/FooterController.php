<?php
namespace App\Controllers;

use App\Core\Controller;

class FooterController extends Controller
{
    public function about()
    {
        return $this->view('footer/about');
    }

    public function privacyPolicy()
    {
        return $this->view('footer/privacyPolicy');
    }

    public function faq()
    {
        return $this->view('footer/faq');
    }

    public function returns()
    {
        return $this->view('footer/returns');
    }

    public function deliveryPayment()
    {
        return $this->view('footer/deliveryPayment');
    }

    public function information()
    {
        return $this->view('footer/information');
    }

    public function support()
    {
        return $this->view('footer/support');
    }
}
