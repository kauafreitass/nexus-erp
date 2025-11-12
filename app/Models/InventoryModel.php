<?php
namespace App\Models;

use Database\Database;
use PDO;
use Exception;

class InventoryModel {

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * R: (Read) Busca o estoque atual de todos os produtos da EMPRESA.
     */
    public function getCurrentStockByCompanyId($companyId)
    {
        $sql = "SELECT 
                    p.id, p.sku, p.description, p.type, p.unit_of_measure,
                    COALESCE(SUM(il.quantity_change), 0) AS current_stock
                FROM 
                    products AS p
                LEFT JOIN 
                    inventory_ledger AS il ON p.id = il.product_id
                WHERE 
                    p.company_id = ?
                GROUP BY 
                    p.id, p.sku, p.description, p.type, p.unit_of_measure
                ORDER BY 
                    p.description";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * C: (Create) Adiciona um novo lançamento no ledger (Ajuste de estoque).
     */
    public function createLedgerEntry($userId, $companyId, $productId, $quantityChange, $transactionType, $costAtTime = 0)
    {
        // Segurança: Verifica se o produto pertence à empresa
        $sqlCheck = "SELECT id FROM products WHERE id = ? AND company_id = ?";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([$productId, $companyId]);
        if (!$stmtCheck->fetch()) {
             throw new Exception("Produto não encontrado ou não pertence à sua empresa.");
        }
        
        $sql = "INSERT INTO inventory_ledger 
                    (company_id, user_id, product_id, transaction_date, transaction_type, quantity_change, cost_at_time)
                VALUES 
                    (?, ?, NOW(), ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $companyId,
            $userId,
            $productId,
            $transactionType,
            $quantityChange,
            $costAtTime
        ]);
    }

    /**
     * R: (Read) Busca o extrato (logs) de um produto da EMPRESA.
     */
    public function findLedgerByProductId($productId, $companyId)
    {
        $sql = "SELECT il.*, u.name as user_name 
                FROM inventory_ledger AS il
                LEFT JOIN users AS u ON il.user_id = u.id
                WHERE il.product_id = ? AND il.company_id = ?
                ORDER BY il.transaction_date DESC, il.id DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productId, $companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * R: (Read) Busca produtos com estoque baixo para o dashboard da EMPRESA.
     */
    public function getLowStockProducts($companyId, $threshold = 5)
    {
        $sql = "SELECT 
                    p.id, p.description, p.unit_of_measure,
                    COALESCE(SUM(il.quantity_change), 0) AS current_stock
                FROM 
                    products AS p
                LEFT JOIN 
                    inventory_ledger AS il ON p.id = il.product_id
                WHERE 
                    p.company_id = ? AND p.type = 'PRODUCT'
                GROUP BY 
                    p.id, p.description, p.unit_of_measure
                HAVING 
                    current_stock <= ?
                ORDER BY 
                    current_stock ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId, $threshold]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * R: (Read) Busca os produtos com maior estoque para o gráfico do dashboard.
     */
    public function getStockDataForChart($companyId)
    {
        $sql = "SELECT 
                    p.sku, 
                    COALESCE(SUM(il.quantity_change), 0) AS current_stock
                FROM 
                    products AS p
                LEFT JOIN 
                    inventory_ledger AS il ON p.id = il.product_id
                WHERE 
                    p.company_id = ? AND p.type = 'PRODUCT'
                GROUP BY 
                    p.id, p.sku
                HAVING
                    current_stock > 0
                ORDER BY 
                    current_stock DESC
                LIMIT 5";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [['Produto (SKU)', 'Quantidade', ['role' => 'style']]];
        if (empty($results)) {
            $data[] = ['Nenhum', 0, '#3b74e6'];
            return $data;
        }
        foreach ($results as $row) {
            $data[] = [$row['sku'], (float)$row['current_stock'], '#3b74e6'];
        }
        return $data;
    }
}