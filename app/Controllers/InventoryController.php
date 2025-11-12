<?php
namespace App\Controllers;

use App\Models\InventoryModel;
use App\Models\ProductModel;
use Exception;

class InventoryController extends BaseController
{
    private InventoryModel $model;
    private ProductModel $productModel;

    public function __construct()
    {
        $this->model = new InventoryModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Rota: GET /supplies
     */
    public function list(): void
    {
        $this->checkPermission('inventory_view');
        $companyId = $_SESSION['company_id'];

        $stockLevels = $this->model->getCurrentStockByCompanyId($companyId);
        
        view('erp/inventory/list', [
            'activePage' => 'supplies', 
            'stockLevels' => $stockLevels
        ]);
    }

    /**
     * Rota: GET /supplies/details
     */
    public function showDetails(): void
    {
        $this->checkPermission('inventory_view');
        $companyId = $_SESSION['company_id'];
        $productId = $_GET['id'] ?? 0;

        $product = $this->productModel->findByIdAndCompanyId($productId, $companyId);
        if (!$product) {
            $this->redirectWithError("/nexus-erp/public/supplies", "Produto não encontrado.");
            return;
        }
        
        $logs = $this->model->findLedgerByProductId($productId, $companyId);
        
        view('erp/inventory/details', [
            'activePage' => 'supplies',
            'product' => $product,
            'logs' => $logs
        ]);
    }

    /**
     * Rota: POST /supplies/adjust
     */
    public function adjustStock(): void
    {
        $this->checkPermission('inventory_adjust');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        
        $productId = $_POST['product_id'] ?? 0;
        $quantity = (float)($_POST['quantity'] ?? 0);
        $type = $_POST['type'] ?? ''; 
        $cost = (float)($_POST['cost_price'] ?? 0);

        if (empty($productId) || $quantity <= 0 || !in_array($type, ['ADJUSTMENT_IN', 'ADJUSTMENT_OUT'])) {
            $this->redirectWithError("/nexus-erp/public/supplies", "Dados inválidos para ajuste.");
            return;
        }

        try {
            $quantityChange = ($type === 'ADJUSTMENT_OUT') ? -$quantity : $quantity;
            $costAtTime = ($type === 'ADJUSTMENT_IN') ? $cost : 0;
            
            $this->model->createLedgerEntry($userId, $companyId, $productId, $quantityChange, $type, $costAtTime);
            $this->redirectWithSuccess("/nexus-erp/public/supplies", "Estoque ajustado com sucesso!");

        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/supplies", $e->getMessage());
        }
    }
}