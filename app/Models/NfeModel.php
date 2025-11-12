<?php
namespace App\Models;

use Database\Database;
use PDO;
use Exception;
// 1. IMPORTAR OS MODELS NECESSÁRIOS
use App\Models\OrderModel;
use App\Models\CustomerModel;
use App\Models\ItemModel;
use App\Models\TaxModel;

class NfeModel {

    private $pdo;
    // 2. ADICIONAR PROPRIEDADES DOS MODELS
    private $orderModel;
    private $customerModel;
    private $itemModel;
    private $taxModel;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
        // 3. INSTANCIAR OS MODELS
        $this->orderModel = new OrderModel();
        $this->customerModel = new CustomerModel();
        $this->itemModel = new ItemModel();
        $this->taxModel = new TaxModel();
    }

    /**
     * C: (CREATE) Lógica principal de geração da NFe (Transação)
     */
    public function createFromOrder($orderId, $userId, $companyId, $roleName) {
        
        $order = $this->orderModel->findByIdAndHierarchy($orderId, $companyId, $userId, $roleName);
        
        if (!$order) {
            throw new Exception("Pedido não encontrado ou não pertence a você.");
        }
        if ($order['status'] !== 'CONFIRMED') { 
            throw new Exception("Apenas pedidos 'Confirmados' podem ser faturados.");
        }
        
        $sqlCheck = "SELECT id FROM nfe WHERE sales_order_id = ?";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([$orderId]);
        if ($stmtCheck->fetch()) {
            throw new Exception("Este pedido já foi faturado.");
        }

        $items = $this->itemModel->findByOrderId($orderId);
        if (empty($items)) {
            throw new Exception("Pedido não contém itens.");
        }
        
        // --- 4. BUSCAR DADOS PARA CÁLCULO DE IMPOSTO ---
        $customer = $this->customerModel->findByIdAndCompanyId($order['customer_id'], $companyId);
        if (!$customer || empty($customer['address_state'])) {
            throw new Exception("O cadastro do cliente está incompleto. O 'Estado (UF)' é obrigatório para o cálculo de impostos.");
        }

        $icmsRate = $this->taxModel->getIcmsRate($customer['address_state']);
        if ($icmsRate <= 0) {
            throw new Exception("Não foi possível encontrar uma alíquota de ICMS válida para o estado: " . $customer['address_state']);
        }
        // --- FIM DA BUSCA ---
        
        $this->pdo->beginTransaction();
        try {
            
            $sqlNfe = "INSERT INTO nfe (
                            company_id, user_id, sales_order_id, customer_id, nfe_number, series, 
                            issue_date, operation_type, status, created_at, updated_at
                        ) VALUES (
                            :company_id, :user_id, :order_id, :customer_id, :nfe_num, :series, 
                            NOW(), 'VENDA DE MERCADORIA', 'AUTHORIZED', NOW(), NOW()
                        )";
            
            $stmtNfe = $this->pdo->prepare($sqlNfe);
            $stmtNfe->execute([
                ':company_id' => $companyId,
                ':user_id' => $userId, 
                ':order_id' => $orderId,
                ':customer_id' => $order['customer_id'],
                ':nfe_num' => 0, 
                ':series' => 1
            ]);
            
            $nfeId = $this->pdo->lastInsertId();
            $this->pdo->query("UPDATE nfe SET nfe_number = $nfeId WHERE id = $nfeId");

            // --- 5. INSERÇÃO DE ITENS COM CÁLCULO DE ICMS ---
            $sqlItem = "INSERT INTO nfe_items (
                            nfe_id, product_id, quantity, unit_price, total_price, 
                            cfop, icms_base, icms_value
                        ) VALUES (
                            :nfe_id, :product_id, :qty, :price, :total, 
                            '5102', :icms_base, :icms_value
                        )"; 
            
            $stmtItem = $this->pdo->prepare($sqlItem);

            foreach ($items as $item) {
                $total_price = (float)$item['quantity'] * (float)$item['unit_price'];
                $icms_base = $total_price; // Simplificação (base de cálculo = valor total)
                $icms_value = $icms_base * ($icmsRate / 100.00);
                
                $stmtItem->execute([
                    ':nfe_id' => $nfeId,
                    ':product_id' => $item['product_id'],
                    ':qty' => $item['quantity'],
                    ':price' => $item['unit_price'],
                    ':total' => $total_price,
                    ':icms_base' => $icms_base,
                    ':icms_value' => $icms_value
                ]);
            }
            // --- FIM DA INSERÇÃO ---

            $sqlOrderUpdate = "UPDATE sales_orders SET status = 'INVOICED' WHERE id = ?";
            $this->pdo->prepare($sqlOrderUpdate)->execute([$orderId]);

            $this->pdo->commit();
            return $nfeId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro na transação da NFe: " . $e->getMessage());
        }
    }
    
    /**
     * U: (UPDATE) Cancela uma NFe e reabre o pedido
     */
    public function cancelNfe($nfeId, $companyId, $userId, $roleName) {
        
        $nfe = $this->findByIdAndHierarchy($nfeId, $companyId, $userId, $roleName);
        if (!$nfe) {
            throw new Exception("NFe não encontrada ou não pertence a você.");
        }

        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("UPDATE nfe SET status = 'CANCELED' WHERE id = ?")
                 ->execute([$nfeId]);
            
            if ($nfe['sales_order_id']) {
                $this->pdo->prepare("UPDATE sales_orders SET status = 'CONFIRMED' WHERE id = ?")
                     ->execute([$nfe['sales_order_id']]);
            }
            
            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro ao cancelar NFe: " . $e->getMessage());
        }
    }
    
    /**
     * R: (Read) Lista todas as NFes (com hierarquia)
     */
    public function findAllByHierarchy($companyId, $userId, $roleName) {
        $sql = "SELECT n.*, c.name as customer_name, u.name as user_name
                FROM nfe AS n
                JOIN customers AS c ON n.customer_id = c.id
                JOIN users AS u ON n.user_id = u.id
                WHERE";
        
        $params = [];
        if ($roleName === 'Administrador' || $roleName === 'Gerente') {
            $sql .= " n.company_id = ?";
            $params[] = $companyId;
        } else { 
            $sql .= " n.company_id = ? AND n.user_id = ?";
            $params[] = $companyId;
            $params[] = $userId;
        }
        $sql .= " ORDER BY n.issue_date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * R: (Read) Mostra detalhes de uma NFe (com hierarquia)
     */
    public function findByIdAndHierarchy($nfeId, $companyId, $userId, $roleName) {
        $sql = "SELECT n.*, c.name as customer_name
                FROM nfe AS n
                JOIN customers AS c ON n.customer_id = c.id
                WHERE n.id = :nfe_id";
        
        $params = [':nfe_id' => $nfeId];
        if ($roleName === 'Administrador' || $roleName === 'Gerente') {
            $sql .= " AND n.company_id = :company_id";
            $params[':company_id'] = $companyId;
        } else { 
            $sql .= " AND n.company_id = :company_id AND n.user_id = :user_id";
            $params[':company_id'] = $companyId;
            $params[':user_id'] = $userId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findItemsByNfeId($nfeId) {
        $sql = "SELECT i.*, p.description, p.sku 
                FROM nfe_items AS i
                JOIN products AS p ON i.product_id = p.id
                WHERE i.nfe_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nfeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}