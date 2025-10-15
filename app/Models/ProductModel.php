<?php

namespace App\Models;

use Database\Database;

class ProductModel
{

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function getProducts($user)
    {
        $sql = "SELECT * FROM products WHERE product_owner = :id ORDER BY description";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $user["id"]);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProduct($id)
    {
        try {
            $sql = "SELECT * FROM products WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function newProduct()
    {
        try {
            $sql = "INSERT INTO products (description, sale_price, product_owner) VALUES (:description, :price, :product_owner)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':product_owner', $product_owner);
            $stmt->execute();
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }

    }

    public function updateProduct()
    {
        try {
            $sql = "UPDATE products SET description = :description, sale_price = :price WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }


}