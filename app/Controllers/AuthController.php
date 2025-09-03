<?php

namespace App\Controllers;

use App\Models\AuthModel;

class AuthController
{
    public AuthModel $model;

    public function __construct()
    {
        if (!isset($_SESSION['auth'])) {
        $_SESSION['auth'] = "notAuthenticated";
    }
        $this->model = new AuthModel();
    }


    public function showLogin(): void
    {
        view('auth/login', [
            'title' => 'Entrar - Nexus ERP'
        ]);
    }

    public function showRegister(): void
    {
        view('auth/register', [
            'title' => 'Cadastre-se - Nexus ERP'
        ]);
    }

    public function showForgotPassword(): void
    {
        view('auth/forgot-password');
    }


    public function showLogout()
    {
        session_destroy();
        header('Location: login');
        exit;
    }


    public function storeAccount($name, $email, $password, $gender, $birthdate)
    {
        $this->model->storeAccount($name, $email, $password, $gender, $birthdate);
    }



    public function login($email, $password)
    {
        $this->model->login($email, $password);
    }
}

