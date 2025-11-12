<?php
namespace App\Controllers;

use App\Models\ItemModel;
use App\Models\OrderModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SystemSettingsModel; // 1. IMPORTAR O NOVO MODEL
use Exception;

class OrderController extends BaseController 
{
    private $orderModel;
    private $itemModel;
    private $customerModel;
    private $productModel;
    private $settingsModel; // 2. ADICIONAR PROPRIEDADE

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->itemModel = new ItemModel();
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->settingsModel = new SystemSettingsModel(); // 3. INSTANCIAR
    }

    /**
     * Rota: GET /sales
     */
    public function salesList() {
        $this->checkPermission('sales_orders_view');
        
        try {
            $userId = $_SESSION['user']['id'];
            $companyId = $_SESSION['company_id'];
            $roleName = $_SESSION['role_name'];
            
            $orders = $this->orderModel->findAllByHierarchy($companyId, $userId, $roleName);

            view('erp/orders/sales', [
                'orders' => $orders,
                'activePage' => 'sales' 
            ]);

        } catch (Exception $e) {
            die("Erro ao carregar a lista de pedidos: " . $e->getMessage());
        }
    }
    
    /**
     * Rota: GET /sales/details
     */
    public function showSaleDetails() {
        $this->checkPermission('sales_orders_view');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $roleName = $_SESSION['role_name'];
        $pedidoId = $_GET['id'] ?? 0;

        try {
            $pedido = $this->orderModel->findByIdAndHierarchy($pedidoId, $companyId, $userId, $roleName);
            
            if (!$pedido) {
                $this->redirectWithError('/nexus-erp/public/sales', 'Pedido não encontrado ou você não tem permissão para vê-lo.');
                return;
            }
            
            $itens = $this->itemModel->findByOrderId($pedidoId); 

            view('erp/orders/sales_detail', [
                'pedido' => $pedido,
                'itens' => $itens,
                'activePage' => 'sales'
            ]);

        } catch (Exception $e) {
            die("Erro ao carregar os detalhes do pedido: " . $e->getMessage());
        }
    }

    /**
     * Rota: POST /sales/update_status
     */
    public function updateStatus() {
        $this->checkPermission('sales_orders_edit');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $roleName = $_SESSION['role_name'];
        $pedidoId = $_POST['pedido_id'] ?? 0;
        $novoStatus = $_POST['novo_status'] ?? '';

        try {
            // --- 4. VALIDAÇÃO DE REGRA DE NEGÓCIO (PEDIDO MÍNIMO) ---
            if ($novoStatus === 'CONFIRMED' || $novoStatus === 'INVOICED') {
                $minOrderValue = (float)$this->settingsModel->getSetting('minimum_order_value');
                if ($minOrderValue > 0) {
                    $pedido = $this->orderModel->findByIdAndHierarchy($pedidoId, $companyId, $userId, $roleName);
                    if (!$pedido) {
                        throw new Exception("Pedido não encontrado.");
                    }
                    if ($pedido['total'] < $minOrderValue) {
                        throw new Exception("O pedido (R$ " . number_format($pedido['total'], 2, ',', '.') . ") não atingiu o valor mínimo de R$ " . number_format($minOrderValue, 2, ',', '.') . " para ser faturado.");
                    }
                }
            }
            // --- FIM DA VALIDAÇÃO ---

            $this->orderModel->updateStatus($pedidoId, $novoStatus, $companyId, $userId, $roleName);
            $this->redirectWithSuccess("/nexus-erp/public/sales/details?id=" . $pedidoId, "Status atualizado com sucesso!");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/sales/details?id=" . $pedidoId, $e->getMessage());
        }
    }

    /**
     * Rota: GET /sales/create
     */
    public function showCreateForm() {
        $this->checkPermission('sales_orders_create');
        $companyId = $_SESSION['company_id'];
        
        // Busca clientes e produtos da empresa
        $customers = $this->customerModel->findAllByCompanyId($companyId);
        $products = $this->productModel->findAllByCompanyId($companyId);
        
        view('erp/orders/sales_create', [
            'activePage' => 'sales',
            'customers' => $customers,
            'products' => $products
        ]);
    }

    /**
     * Rota: POST /sales/store
     */
    public function store() {
        $this->checkPermission('sales_orders_create');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $customerId = $_POST['customer_id'];
        $orderDate = $_POST['order_date'];
        $items = $_POST['items'] ?? []; 

        if (empty($customerId) || empty($orderDate) || empty($items)) {
            $this->redirectWithError("/nexus-erp/public/sales/create", "Cliente, Data e Itens são obrigatórios.");
            return;
        }

        try {
            $this->orderModel->createOrder($userId, $companyId, $customerId, $orderDate, $items);
            $this->redirectWithSuccess("/nexus-erp/public/sales", "Pedido criado com sucesso!");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/sales/create", "Erro: " . $e->getMessage());
        }
    }

    /**
     * Rota: GET /sales/edit
     */
    public function showEditForm() {
        $this->checkPermission('sales_orders_edit');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $roleName = $_SESSION['role_name'];
        $pedidoId = (int)$_GET['id'];

        try {
            $pedido = $this->orderModel->findByIdAndHierarchy($pedidoId, $companyId, $userId, $roleName);

            if (!$pedido) {
                $this->redirectWithError("/nexus-erp/public/sales", "Pedido não encontrado.");
                return;
            }
            if ($pedido['status'] !== 'DRAFT') {
                $this->redirectWithError("/nexus-erp/public/sales/details?id=" . $pedidoId, "Apenas pedidos em 'Rascunho' podem ser editados.");
                return;
            }
            
            $itens = $this->itemModel->findByOrderId($pedidoId);
            $customers = $this->customerModel->findAllByCompanyId($companyId);
            $products = $this->productModel->findAllByCompanyId($companyId);

            view('erp/orders/sales_edit', [
                'pedido' => $pedido,
                'itens' => $itens,
                'customers' => $customers,
                'products' => $products,
                'activePage' => 'sales'
            ]);

        } catch (Exception $e) {
            die("Erro ao carregar edição: " . $e->getMessage());
        }
    }

    /**
     * Rota: POST /sales/update
     */
    public function update() {
        $this->checkPermission('sales_orders_edit');

        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $roleName = $_SESSION['role_name'];
        $orderId = $_POST['order_id'];
        $customerId = $_POST['customer_id'];
        $orderDate = $_POST['order_date'];
        $items = $_POST['items'] ?? [];

        if (empty($orderId) || empty($customerId) || empty($orderDate) || empty($items)) {
            $this->redirectWithError("/nexus-erp/public/sales/edit?id=" . $orderId, "Dados insuficientes.");
            return;
        }

        try {
            $this->orderModel->updateOrder($orderId, $companyId, $userId, $roleName, $customerId, $orderDate, $items);
            $this->redirectWithSuccess("/nexus-erp/public/sales/details?id=" . $orderId, "Pedido atualizado com sucesso!");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/sales/edit?id=" . $orderId, "Erro ao atualizar: " . $e->getMessage());
        }
    }
    
    /**
     * Rota: POST /sales/delete
     */
    public function delete() {
        $this->checkPermission('sales_orders_edit'); 

        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $roleName = $_SESSION['role_name'];
        $orderId = $_POST['order_id']; 

        try {
            $this->orderModel->delete($orderId, $companyId, $userId, $roleName);
            $this->redirectWithSuccess("/nexus-erp/public/sales", "Pedido excluído com sucesso.");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/sales", $e->getMessage());
        }
    }
}