<?php

namespace App\Controllers;

use App\Models\AuthModel;

class AuthController
{
    private AuthModel $model;

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


    public function showLogout(): void
    {
        session_destroy();
        header('Location: login');
    }


    public function storeAccount($name, $email, $password, $gender, $birthdate): void
    {
        $this->model->storeAccount($name, $email, $password, $gender, $birthdate);
    }



    public function login($email, $password): void
    {
        $this->model->login($email, $password);
    }

    public function forgotPassword($email, $new_password): void
    {
        $this->model->forgotPassword($email, $new_password);
    }
}