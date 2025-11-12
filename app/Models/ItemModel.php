<?php
namespace App\Models;

use Database\Database;
use PDO;
use Exception;

class ItemModel {

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * Busca todos os itens de um pedido de venda específico
     */
    public function findByOrderId($orderId)
    {
        $sql = "SELECT 
                    soi.*, 
                    p.description AS product_description,
                    p.sku
                FROM 
                    sales_order_items AS soi
                JOIN 
                    products AS p ON soi.product_id = p.id
                WHERE 
                    soi.sales_order_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}