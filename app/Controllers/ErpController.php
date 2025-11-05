<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Models\ErpModel;

class ErpController
{

    private ErpModel $model;
    private AuthMiddleware $isAuth;

    public function __construct()
    {
        $this->isAuth = new AuthMiddleware();
        $this->model = new ErpModel();
    }

    public function index(): void
    {
        view('index');
    }

    public function dashboard(): void
    {
        $this->isAuth->handle();
        view('erp/dashboard');
    }

    public function getSalesChart($user)
    {
        $this->isAuth->handle();
        $this->model->getSalesChart($user);
    }

}