<?php
namespace App\Controllers;

use App\Models\InventoryModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use Exception;

class ReportController extends BaseController {

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
    
    public function dashboard() {
        // Protege a rota.
        $this->checkPermission('reports_sales_view'); 
        
        $companyId = $_SESSION['company_id'];
        
        $salesChartData = $this->orderModel->getSalesDataForChart($companyId);
        $productsChartData = $this->productModel->getProductStatusForChart($companyId);
        $stockChartData = $this->inventoryModel->getStockDataForChart($companyId);
        $customerChartData = $this->customerModel->getCustomerDataForChart($companyId);
        
        // Carrega a view que você tem: erp/reports/dashboard.php
        view('erp/reports/dashboard', [
            'activePage' => 'reports',
            'salesChartData' => $salesChartData,
            'productsChartData' => $productsChartData,
            'stockChartData' => $stockChartData, 
            'customerChartData' => $customerChartData
        ]);
    }

    /**
     * Rota: GET /reports/sales
     * Exibe o Relatório de Vendas filtrável.
     */
    public function salesReport() {
        $this->checkPermission('reports_sales_view');
        
        $companyId = $_SESSION['company_id'];
        $roleName = $_SESSION['role_name'];
        $userId = $_SESSION['user']['id'];

        $filters = [
            'date_start' => $_GET['date_start'] ?? date('Y-m-01'),
            'date_end' => $_GET['date_end'] ?? date('Y-m-t'),
            'customer_id' => $_GET['customer_id'] ?? null,
            'status' => $_GET['status'] ?? null,
        ];
        
        $customers = $this->customerModel->findAllByCompanyId($companyId);
        $orders = $this->orderModel->getSalesReport($companyId, $userId, $roleName, $filters);
        $totalSales = array_reduce($orders, fn($sum, $order) => $sum + $order['total_amount'], 0);

        view('erp/reports/sales', [
            'activePage' => 'reports',
            'orders' => $orders,
            'customers' => $customers,
            'filters' => $filters,
            'totalSales' => $totalSales
        ]);
    }
}