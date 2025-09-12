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
        view('erp/index');
    }

    public function home(): void
    {
        view('home');
    }

    public function salesChart($user)
    {
        $this->model->salesChart($user);
    }

}