<?php
namespace App\Controllers;

use App\Models\InventoryModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use Exception;

class ErpController extends BaseController { // Extende BaseController

    private $inventoryModel;
    private $orderModel;
    private $productModel;
    private $customerModel;

    public function __construct() {
        $this->inventoryModel = new InventoryModel();
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
    }
    
    // (O checkGuest() é herdado do BaseController)
    
    public function index() {
        // Redireciona para o login se não estiver logado, ou dashboard se estiver
        if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== 'authenticated') {
            view('index');
        } else {
            header('Location: /nexus-erp/public/dashboard');
        }
        exit;
    }

    public function dashboard() {
        $this->checkAuth(); // Herdado (verifica se está logado)
        
        $companyId = $_SESSION['company_id']; // Pega o ID da EMPRESA
        
        // Busca todos os dados para os 4 gráficos, filtrando pela EMPRESA
        $salesChartData = $this->orderModel->getSalesDataForChart($companyId);
        $productsChartData = $this->productModel->getProductStatusForChart($companyId);
        $stockChartData = $this->inventoryModel->getStockDataForChart($companyId);
        $customerChartData = $this->customerModel->getCustomerDataForChart($companyId);
        
        view('erp/dashboard', [
            'activePage' => 'dashboard',
            'salesChartData' => $salesChartData,
            'productsChartData' => $productsChartData,
            'stockChartData' => $stockChartData, 
            'customerChartData' => $customerChartData
        ]);
    }
}