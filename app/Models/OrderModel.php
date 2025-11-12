<?php
namespace App\Models;

use Database\Database;
use PDO;
use Exception;

class OrderModel {

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * R: (Read) Busca um pedido, verificando a hierarquia da empresa.
     */
     public function findByIdAndHierarchy($orderId, $companyId, $userId, $roleName) {
        
        $sql = "SELECT 
                    so.id, so.user_id, so.customer_id,
                    so.order_date AS data, so.status, so.total_amount AS total,
                    c.business_name AS cliente, c.document_number AS cnpj_cpf,
                    c.phone AS cliente_telefone, c.email AS cliente_email,
                    c.address_street AS cliente_rua, c.address_number AS cliente_numero,
                    c.address_complement AS cliente_complemento, c.address_neighborhood AS cliente_bairro,
                    c.address_city AS cliente_cidade, c.address_state AS cliente_estado,
                    c.address_zipcode AS cliente_cep,
                    nfe.id AS nfe_id 
                FROM 
                    sales_orders AS so
                JOIN 
                    customers AS c ON so.customer_id = c.id
                LEFT JOIN 
                    nfe ON nfe.sales_order_id = so.id 
                WHERE 
                    so.id = :order_id";

        $params = [':order_id' => $orderId];
        
        // Admin/Gerente pode ver todos os pedidos da empresa
        if ($roleName === 'Administrador' || $roleName === 'Gerente') {
            $sql .= " AND so.company_id = :company_id";
            $params[':company_id'] = $companyId;
        } 
        // Funcionário só vê os dele
        else {
            $sql .= " AND so.company_id = :company_id AND so.user_id = :user_id";
            $params[':company_id'] = $companyId;
            $params[':user_id'] = $userId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * R: (Read) Busca todos os pedidos, respeitando a hierarquia.
     */
    public function findAllByHierarchy($companyId, $userId, $roleName) {
        
        $sql = "SELECT 
                    so.id, so.order_date AS data, so.total_amount AS total,
                    so.status, c.business_name AS cliente, c.document_number AS cnpj_cpf,
                    u.name as user_name -- Adiciona o nome do usuário que criou
                FROM 
                    sales_orders AS so
                JOIN 
                    customers AS c ON so.customer_id = c.id
                JOIN
                    users AS u ON so.user_id = u.id -- Pega o nome do criador
                WHERE";

        $params = [];
        // Admin/Gerente vê todos da empresa
        if ($roleName === 'Administrador' || $roleName === 'Gerente') {
            $sql .= " so.company_id = ?";
            $params[] = $companyId;
        } 
        // Funcionário só vê os dele
        else { 
            $sql .= " so.company_id = ? AND so.user_id = ?";
            $params[] = $companyId;
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY so.order_date DESC, so.id DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * U: (UPDATE) Atualiza o status de um pedido
     */
    public function updateStatus($orderId, $novoStatus, $companyId, $userId, $roleName) {
        $statusPermitidos = ['DRAFT', 'CONFIRMED', 'INVOICED', 'CANCELED'];
        if (!in_array($novoStatus, $statusPermitidos)) {
            throw new Exception("Status inválido fornecido.");
        }

        // Verifica se o usuário pode editar este pedido
        $pedido = $this->findByIdAndHierarchy($orderId, $companyId, $userId, $roleName);
        if (!$pedido) {
             throw new Exception("Pedido não encontrado ou você não tem permissão para alterá-lo.");
        }
        
        // Regra de Negócio: Funcionário não pode Faturar ou Cancelar, só Admin/Gerente
        if (($novoStatus === 'INVOICED' || $novoStatus === 'CANCELED') && $roleName === 'Funcionário') {
            throw new Exception("Apenas Gerentes ou Administradores podem Faturar ou Cancelar pedidos.");
        }

        $sql = "UPDATE sales_orders SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$novoStatus, $orderId]);
    }

    /**
     * C: (CREATE) Cria um novo pedido, associando-o ao usuário E à empresa.
     */
    public function createOrder($userId, $companyId, $customerId, $orderDate, $items) {
        
        $this->pdo->beginTransaction();
        try {
            $sqlOrder = "INSERT INTO sales_orders (
                            user_id, company_id, customer_id, order_date, 
                            status, total_amount, created_at, updated_at
                         ) VALUES (
                            :user_id, :company_id, :customer_id, :order_date, 
                            'DRAFT', 0.00, NOW(), NOW()
                         )";
            
            $stmtOrder = $this->pdo->prepare($sqlOrder);
            $stmtOrder->execute([
                ':user_id' => $userId,
                ':company_id' => $companyId,
                ':customer_id' => $customerId,
                ':order_date' => $orderDate
            ]);
            
            $orderId = $this->pdo->lastInsertId();
            $totalAmount = 0;

            $sqlItem = "INSERT INTO sales_order_items (sales_order_id, product_id, quantity, unit_price, total_price) 
                        VALUES (:order_id, :product_id, :quantity, :unit_price, :total_price)";
            $stmtItem = $this->pdo->prepare($sqlItem);

            foreach ($items as $item) {
                $itemTotalPrice = (float)$item['quantity'] * (float)$item['unit_price'];
                $totalAmount += $itemTotalPrice;
                
                $stmtItem->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':unit_price' => $item['unit_price'],
                    ':total_price' => $itemTotalPrice
                ]);
            }

            $sqlTotal = "UPDATE sales_orders SET total_amount = :total WHERE id = :order_id";
            $stmtTotal = $this->pdo->prepare($sqlTotal);
            $stmtTotal->execute([':total' => $totalAmount, ':order_id' => $orderId]);

            $this->pdo->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro ao salvar pedido: " . $e->getMessage());
        }
    }

    /**
     * D: (DELETE) Apaga um pedido, se ele for 'DRAFT'
     */
    public function delete($orderId, $companyId, $userId, $roleName) {
        
        $order = $this->findByIdAndHierarchy($orderId, $companyId, $userId, $roleName);
        if (!$order) {
            throw new Exception("Pedido não encontrado ou não pertence a você.");
        }
        
        if ($order['status'] !== 'DRAFT') {
            throw new Exception("Apenas pedidos em 'Rascunho' podem ser excluídos.");
        }

        $sqlDelete = "DELETE FROM sales_orders WHERE id = ?";
        $stmtDelete = $this->pdo->prepare($sqlDelete);
        
        return $stmtDelete->execute([$orderId]);
    }
    
    /**
     * U: (UPDATE) Atualiza um pedido (cliente, data) e seus itens (Transação)
     */
    public function updateOrder($orderId, $companyId, $userId, $roleName, $customerId, $orderDate, $items) {
        
        $order = $this->findByIdAndHierarchy($orderId, $companyId, $userId, $roleName);
        if (!$order) {
            throw new Exception("Pedido não encontrado ou não pertence a você.");
        }
        if ($order['status'] !== 'DRAFT') {
            throw new Exception("Apenas pedidos em 'Rascunho' podem ser atualizados.");
        }

        $this->pdo->beginTransaction();
        try {
            $stmtDelete = $this->pdo->prepare("DELETE FROM sales_order_items WHERE sales_order_id = :order_id");
            $stmtDelete->execute([':order_id' => $orderId]);

            $totalAmount = 0;
            $sqlItem = "INSERT INTO sales_order_items (sales_order_id, product_id, quantity, unit_price, total_price) 
                        VALUES (:order_id, :product_id, :quantity, :unit_price, :total_price)";
            $stmtItem = $this->pdo->prepare($sqlItem);

            foreach ($items as $item) {
                $itemTotalPrice = (float)$item['quantity'] * (float)$item['unit_price'];
                $totalAmount += $itemTotalPrice;
                
                $stmtItem->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':unit_price' => $item['unit_price'],
                    ':total_price' => $itemTotalPrice
                ]);
            }

            $sqlOrder = "UPDATE sales_orders 
                         SET customer_id = :customer_id, 
                             order_date = :order_date, 
                             total_amount = :total,
                             updated_at = NOW()
                         WHERE id = :order_id";
            
            $stmtOrder = $this->pdo->prepare($sqlOrder);
            $stmtOrder->execute([
                ':customer_id' => $customerId,
                ':order_date' => $orderDate,
                ':total' => $totalAmount,
                ':order_id' => $orderId
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro ao atualizar o pedido: " . $e->getMessage());
        }
    }
    
    /**
     * R: (Read) Busca dados de vendas por mês para o gráfico do dashboard.
     */
    public function getSalesDataForChart($companyId)
    {
        $sql = "SELECT 
                    DATE_FORMAT(order_date, '%Y-%m') AS mes,
                    SUM(total_amount) AS vendas
                FROM 
                    sales_orders
                WHERE 
                    company_id = ?
                    AND status IN ('CONFIRMED', 'INVOICED')
                    AND order_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY 
                    mes
                ORDER BY 
                    mes ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = [['Mês', 'Vendas']];
        if (empty($results)) {
            $data[] = ['Nenhum dado', 0];
            return $data;
        }
        foreach ($results as $row) {
            $data[] = [$row['mes'], (float)$row['vendas']];
        }
        return $data;
    }

    public function getSalesReport($companyId, $userId, $roleName, $filters = [])
    {
        // Campos que o relatório precisa
        $sql = "SELECT 
                    so.id, so.order_date, so.status, so.total_amount,
                    c.name as customer_name,
                    u.name as user_name
                FROM 
                    sales_orders AS so
                JOIN 
                    customers AS c ON so.customer_id = c.id
                JOIN
                    users AS u ON so.user_id = u.id
                WHERE";

        $params = [];

        // 1. Filtro de Hierarquia (Regra de Negócio)
        // Admin/Gerente vê todos os pedidos da empresa
        if ($roleName === 'Administrador' || $roleName === 'Gerente') {
            $sql .= " so.company_id = :company_id";
            $params[':company_id'] = $companyId;
        } 
        // Funcionário só vê os que ele criou
        else { 
            $sql .= " so.company_id = :company_id AND so.user_id = :user_id";
            $params[':company_id'] = $companyId;
            $params[':user_id'] = $userId;
        }

        // 2. Filtros Dinâmicos da View
        if (!empty($filters['date_start'])) {
            $sql .= " AND so.order_date >= :date_start";
            $params[':date_start'] = $filters['date_start'] . ' 00:00:00';
        }
        if (!empty($filters['date_end'])) {
            $sql .= " AND so.order_date <= :date_end";
            $params[':date_end'] = $filters['date_end'] . ' 23:59:59';
        }
        if (!empty($filters['customer_id'])) {
            $sql .= " AND so.customer_id = :customer_id";
            $params[':customer_id'] = $filters['customer_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND so.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        $sql .= " ORDER BY so.order_date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}