<?php

namespace App\Models;

use Database\Database;

class ErpModel
{

    private $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function salesChart($user)
    {
        $sql = "SELECT * FROM sales_orders WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $user["id"]);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function customersChart($user)
    {
        $sql = "SELECT * FROM customers WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $user["id"]);
        $stmt->execute();
        return $stmt->fetch();
    }
}