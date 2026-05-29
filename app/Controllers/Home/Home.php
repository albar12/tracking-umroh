<?php

namespace App\Controllers\Home;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Home extends ResourceController
{
    public function index(): string
    {

        return view('dashboard/dashboard', [
            'title' => 'Dashboard'
        ]);
    }
}
