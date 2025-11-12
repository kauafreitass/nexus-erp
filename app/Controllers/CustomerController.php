<?php
namespace App\Controllers;

use App\Models\CustomerModel;
use Exception;

class CustomerController extends BaseController 
{
    private CustomerModel $model;

    public function __construct()
    {
        $this->model = new CustomerModel();
    }

    /**
     * Rota: GET /customers
     */
    public function list(): void
    {
        $this->checkPermission('customers_view'); // [cite: 213]
        $companyId = $_SESSION['company_id'];

        $customers = $this->model->findAllByCompanyId($companyId);
        
        view('erp/customers/list', [
            'activePage' => 'customers',
            'customers' => $customers
        ]);
    }

    /**
     * Rota: GET /customers/create
     */
    public function showCreateForm(): void
    {
        $this->checkPermission('customers_manage'); // [cite: 214]
        
        view('erp/customers/create', [
            'activePage' => 'customers'
        ]);
    }

    /**
     * Rota: POST /customers/store
     */
    public function store(): void
    {
        $this->checkPermission('customers_manage');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        
        try {
            $this->model->store($userId, $companyId, $_POST);
            $this->redirectWithSuccess("/nexus-erp/public/customers", "Cliente criado com sucesso!");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/customers/create", $e->getMessage());
        }
    }

    /**
     * Rota: GET /customers/edit
     */
    public function showEditForm(): void
    {
        $this->checkPermission('customers_manage');
        
        $companyId = $_SESSION['company_id'];
        $customerId = $_GET['id'] ?? 0;
        $customer = $this->model->findByIdAndCompanyId($customerId, $companyId);

        if (!$customer) {
            $this->redirectWithError("/nexus-erp/public/customers", "Cliente não encontrado.");
            return;
        }

        view('erp/customers/edit', [
            'activePage' => 'customers',
            'customer' => $customer
        ]);
    }

    /**
     * Rota: POST /customers/update
     */
    public function update(): void
    {
        $this->checkPermission('customers_manage');
        
        $companyId = $_SESSION['company_id'];
        $customerId = $_POST['customer_id'] ?? 0;

        try {
            $this->model->update($customerId, $companyId, $_POST);
            $this->redirectWithSuccess("/nexus-erp/public/customers/edit?id=" . $customerId, "Cliente atualizado com sucesso!");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/customers/edit?id=" . $customerId, $e->getMessage());
        }
    }

    /**
     * Rota: POST /customers/delete
     */
    public function delete(): void
    {
        $this->checkPermission('customers_manage');
        
        $companyId = $_SESSION['company_id'];
        $customerId = $_POST['customer_id'] ?? 0;

        try {
            $this->model->delete($customerId, $companyId);
            $this->redirectWithSuccess("/nexus-erp/public/customers", "Cliente excluído com sucesso.");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/customers", $e->getMessage());
        }
    }

    /**
     * Rota: GET /api/customers/search
     */
    public function search(): void
    {
        $this->checkPermission('customers_view'); // Apenas usuários logados podem pesquisar
        $companyId = $_SESSION['company_id'];
        $query = $_GET['q'] ?? '';

        $customers = $this->model->searchByName($companyId, $query);
        
        header('Content-Type: application/json');
        echo json_encode($customers);
        exit;
    }
}