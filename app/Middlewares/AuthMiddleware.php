<?php

namespace App\Middlewares;

class AuthMiddleware
{
    public static function handle()
    {
//        session_start();

        if (!isset($_SESSION['user'])) {
            $login = asset("login");
            header("Location: $login");
            exit;
        }
    }
}
