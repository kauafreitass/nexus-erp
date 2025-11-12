<?php
namespace App\Models;

use Database\Database;
use PDO;
use Exception;
use PDOException;

class ProductModel {

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * R: (Read) Busca todos os produtos da EMPRESA.
     */
    public function findAllByCompanyId($companyId)
    {
        $sql = "SELECT id, description, sku, type, unit_of_measure, cost_price, sale_price 
                FROM products 
                WHERE company_id = ? 
                ORDER BY description";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * R: (Read) Busca um produto específico da EMPRESA.
     */
    public function findByIdAndCompanyId($productId, $companyId)
    {
        $sql = "SELECT * FROM products WHERE id = ? AND company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productId, $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * API: (Read) Busca produtos pela descrição ou SKU DENTRO DA EMPRESA.
     */
    public function searchByDescription($companyId, $query)
    {
        $sql = "SELECT id, description, sku, sale_price 
                FROM products 
                WHERE company_id = :company_id 
                  AND (description LIKE :query OR sku LIKE :query)
                LIMIT 10";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':company_id' => $companyId,
            ':query' => '%' . $query . '%'
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Valida os dados do produto, incluindo a regra do NCM.
     */
    private function validateProductData($data)
    {
        if (empty($data['description']) || empty($data['sku']) || empty($data['type']) || empty($data['unit_of_measure'])) {
            throw new Exception("Descrição, SKU, Tipo e Unidade de Medida são obrigatórios.");
        }
        
        // --- ADICIONADO: VALIDAÇÃO DE REGRA DE NEGÓCIO (NCM) ---
        if ($data['type'] === 'PRODUCT') {
            if (empty($data['ncm_code']) || strlen(preg_replace('/\D/', '', $data['ncm_code'])) !== 8) {
                throw new Exception("O código NCM é obrigatório para 'Produtos' e deve ter 8 dígitos.");
            }
        }
        // --- FIM DA ADIÇÃO ---
    }

    /**
     * C: (Create) Cria um novo produto para a EMPRESA e o USUÁRIO.
     */
    public function store($userId, $companyId, $data)
    {
        $this->validateProductData($data); // Executa a validação
        
        $sql = "INSERT INTO products (
                    company_id, user_id, description, sku, type, ncm_code, 
                    cest_code, unit_of_measure, cost_price, sale_price
                ) VALUES (
                    :company_id, :user_id, :description, :sku, :type, :ncm_code, 
                    :cest_code, :unit_of_measure, :cost_price, :sale_price
                )";
        
        $stmt = $this->pdo->prepare($sql);
        
        try {
            $stmt->execute([
                ':company_id' => $companyId,
                ':user_id' => $userId,
                ':description' => $data['description'],
                ':sku' => $data['sku'],
                ':type' => $data['type'],
                ':ncm_code' => $data['ncm_code'],
                ':cest_code' => $data['cest_code'] ?? null,
                ':unit_of_measure' => $data['unit_of_measure'],
                ':cost_price' => $data['cost_price'] ?? 0.00,
                ':sale_price' => $data['sale_price'] ?? 0.00
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("Erro: O SKU (Código) já está em uso nesta empresa.");
            }
            throw $e;
        }
    }

    /**
     * U: (Update) Atualiza um produto da EMPRESA.
     */
    public function update($productId, $companyId, $data)
    {
        $this->validateProductData($data); // Executa a validação

        $sql = "UPDATE products SET
                    description = :description,
                    sku = :sku,
                    type = :type,
                    ncm_code = :ncm_code,
                    cest_code = :cest_code,
                    unit_of_measure = :unit_of_measure,
                    cost_price = :cost_price,
                    sale_price = :sale_price
                WHERE
                    id = :product_id AND company_id = :company_id";
        
        $stmt = $this->pdo->prepare($sql);
        
        try {
            $stmt->execute([
                ':description' => $data['description'],
                ':sku' => $data['sku'],
                ':type' => $data['type'],
                ':ncm_code' => $data['ncm_code'],
                ':cest_code' => $data['cest_code'] ?? null,
                ':unit_of_measure' => $data['unit_of_measure'],
                ':cost_price' => $data['cost_price'] ?? 0.00,
                ':sale_price' => $data['sale_price'] ?? 0.00,
                ':product_id' => $productId,
                ':company_id' => $companyId
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("Erro: O SKU (Código) já está em uso por outro produto.");
            }
            throw $e;
        }
    }
    
    /**
     * D: (Delete) Exclui um produto da EMPRESA.
     */
    public function delete($productId, $companyId)
    {
        $product = $this->findByIdAndCompanyId($productId, $companyId);
        if (!$product) {
            throw new Exception("Produto não encontrado ou não pertence à sua empresa.");
        }

        $sqlCheck = "SELECT (
                        (SELECT COUNT(*) FROM sales_order_items WHERE product_id = :id1) +
                        (SELECT COUNT(*) FROM nfe_items WHERE product_id = :id2)
                    ) AS total_references";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([':id1' => $productId, ':id2' => $productId]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($result['total_references'] > 0) {
            throw new Exception("Este produto não pode ser excluído pois está vinculado a pedidos ou notas fiscais.");
        }

        $sql = "DELETE FROM products WHERE id = ? AND company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$productId, $companyId]);
    }

    /**
     * R: (Read) Busca dados de status de produtos para o gráfico do dashboard.
     */
    public function getProductStatusForChart($companyId)
    {
        // (O código desta função permanece o mesmo)
        $sql = "WITH StockLevels AS (
                    SELECT 
                        p.id,
                        COALESCE(SUM(il.quantity_change), 0) AS current_stock
                    FROM 
                        products AS p
                    LEFT JOIN 
                        inventory_ledger AS il ON p.id = il.product_id
                    WHERE 
                        p.company_id = ? AND p.type = 'PRODUCT'
                    GROUP BY 
                        p.id
                )
                SELECT 
                   CASE 
                       WHEN current_stock <= 0 THEN 'Esgotado'
                       WHEN current_stock <= 5 THEN 'Baixo estoque'
                       ELSE 'Em estoque'
                   END AS status,
                   COUNT(*) AS quantidade
                FROM 
                   StockLevels
                GROUP BY 
                   status";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [['Status', 'Quantidade']];
        if (empty($results)) {
            $data[] = ['Nenhum produto', 1];
            return $data;
        }
        foreach ($results as $row) {
            $data[] = [$row['status'], (int)$row['quantidade']];
        }
        return $data;
    }
}