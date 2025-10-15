<?php

namespace App\Controllers;

use App\Models\ErpModel;

class ErpController
{

    private ErpModel $model;

    public function __construct()
    {
        $this->model = new ErpModel();
    }

    public function index(): void
    {
        view('home');
    }

    public function dashboard(): void
    {
        view('erp/dashboard');
    }

    public function getSalesChart($user)
    {
        $this->model->getSalesChart($user);
    }

}