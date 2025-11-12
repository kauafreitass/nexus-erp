<?php
namespace App\Controllers;

use App\Models\ProductModel;
use Exception;

class ProductController extends BaseController 
{
    private ProductModel $model;

    public function __construct()
    {
        $this->model = new ProductModel();
    }

    /**
     * Rota: GET /products
     */
    public function list(): void
    {
        $this->checkPermission('products_view');
        $companyId = $_SESSION['company_id'];

        $products = $this->model->findAllByCompanyId($companyId);
        
        view('erp/products/list', [
            'activePage' => 'products',
            'products' => $products
        ]);
    }

    /**
     * Rota: GET /products/create
     */
    public function showCreateForm(): void
    {
        $this->checkPermission('products_manage');
        view('erp/products/create', [
            'activePage' => 'products'
        ]);
    }

    /**
     * Rota: POST /products/store
     */
    public function store(): void
    {
        $this->checkPermission('products_manage');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        
        try {
            $this->model->store($userId, $companyId, $_POST);
            $this->redirectWithSuccess("/nexus-erp/public/products", "Produto criado com sucesso!");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/products/create", $e->getMessage());
        }
    }

    /**
     * Rota: GET /products/edit
     */
    public function showEditForm(): void
    {
        $this->checkPermission('products_manage');
        
        $companyId = $_SESSION['company_id'];
        $productId = $_GET['id'] ?? 0;
        $product = $this->model->findByIdAndCompanyId($productId, $companyId);

        if (!$product) {
            $this->redirectWithError("/nexus-erp/public/products", "Produto não encontrado.");
            return;
        }

        view('erp/products/edit', [
            'activePage' => 'products',
            'product' => $product
        ]);
    }

    /**
     * Rota: POST /products/update
     */
    public function update(): void
    {
        $this->checkPermission('products_manage');
        
        $companyId = $_SESSION['company_id'];
        $productId = $_POST['product_id'] ?? 0;

        try {
            $this->model->update($productId, $companyId, $_POST);
            $this->redirectWithSuccess("/nexus-erp/public/products/edit?id=" . $productId, "Produto atualizado com sucesso!");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/products/edit?id=" . $productId, $e->getMessage());
        }
    }

    /**
     * Rota: POST /products/delete
     */
    public function delete(): void
    {
        $this->checkPermission('products_manage');
        
        $companyId = $_SESSION['company_id'];
        $productId = $_POST['product_id'] ?? 0;

        try {
            $this->model->delete($productId, $companyId);
            $this->redirectWithSuccess("/nexus-erp/public/products", "Produto excluído com sucesso.");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/products", $e->getMessage());
        }
    }
    
    /**
     * Rota: GET /api/products/search
     */
    public function search(): void
    {
        $this->checkPermission('products_view');
        $companyId = $_SESSION['company_id'];
        $query = $_GET['q'] ?? '';

        $products = $this->model->searchByDescription($companyId, $query);
        
        header('Content-Type: application/json');
        echo json_encode($products);
        exit;
    }
}