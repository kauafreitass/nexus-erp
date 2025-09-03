<?php

namespace App\Models;

use Database\Database;

class AuthModel
{
    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function storeAccount($name, $business_name, $email, $password, $document_number): void
    {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, business_name, email, password, document_number, phone, created_at, updated_at) 
        VALUES (:name, :business_name, :email, :password, :document_number, :phone, NOW(), NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':business_name', $business_name);
        $stmt->bindParam(':document_number', $document_number);
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();

        $this->login($email, $password);

    }

    public function login($email, $password): void
    {

        $sql = "SELECT id, email, password FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$user) {
            exit();
        } else {
            if (password_verify($password, $user['password'])) {
                $sql = "SELECT * FROM users WHERE email = :email";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                $_SESSION['user'] = $user;
                $_SESSION['auth'] = 'authenticated';

                header("Location: dashboard");;
            }
        }
    }

    public function forgotPassword($email, $new_password): void
    {
        $sql = "UPDATE users SET password = :password WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $new_password);
        $stmt->execute();
    }

}