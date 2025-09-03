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

    public function storeAccount($name, $email, $password, $gender, $birthdate): void
    {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, gender, birthdate, created_at, updated_at) 
        VALUES (:name, :email, :password, :gender, :birthdate, NOW(), NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':business_name', $business_name);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->execute();

        $this->login($email, $password);

    }

    public function login($email, $password)
    {

        $sql = "SELECT id, email, password FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$user) {
            exit();
        } else {
            if (password_verify($password, $user['password'])) {
                $sql = "SELECT id, name, email, gender, birthdate, picture FROM users WHERE email = :email";
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


}