<?php
namespace App\Controllers;

use App\Models\ProductCategoryModel;
use Exception;

class ProductCategoryController extends BaseController
{
    private ProductCategoryModel $model;

    public function __construct()
    {
        $this->model = new ProductCategoryModel();
    }

    /**
     * Exibe a página de listagem e formulário de cadastro.
     * Rota: GET /categories
     */
    public function list(): void
    {
        // Use a permissão de produtos por enquanto
        $this->checkPermission('products_manage'); 
        
        $companyId = $_SESSION['company_id'];
        $categories = [];
        $editCategory = null;

        try {
            $categories = $this->model->findAllByCompany($companyId);
            
            // Verifica se está no modo de edição
            if (isset($_GET['edit_id'])) {
                $editCategory = $this->model->findById($_GET['edit_id'], $companyId);
            }

        } catch (Exception $e) {
            $_SESSION['error_message'] = "Erro ao carregar categorias: " . $e->getMessage();
        }

        view('erp/products/categories', [
            'activePage' => 'categories', // Para manter o menu "Produtos" ativo
            'categories' => $categories,
            'editCategory' => $editCategory // Envia dados da categoria para o form
        ]);
    }

    /**
     * Processa o formulário de CRIAÇÃO de nova categoria.
     * Rota: POST /categories/store
     */
    public function store(): void
    {
        $this->checkPermission('products_manage');
        $companyId = $_SESSION['company_id'];

        try {
            $this->model->create([
                'name' => $_POST['name'],
                'company_id' => $companyId,
                'status' => $_POST['status'] ?? 'ACTIVE'
            ]);
            $this->redirectWithSuccess("/nexus-erp/public/categories", "Categoria criada com sucesso!");
        
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/categories", $e->getMessage());
        }
    }
    
    /**
     * Processa a ATUALIZAÇÃO de uma categoria.
     * Rota: POST /categories/update
     */
    public function update(): void
    {
        $this->checkPermission('products_manage');
        $companyId = $_SESSION['company_id'];
        $categoryId = $_POST['category_id'] ?? 0;

        try {
            $this->model->update($categoryId, $companyId, $_POST);
            $this->redirectWithSuccess("/nexus-erp/public/categories", "Categoria atualizada com sucesso!");
        
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/categories?edit_id=" . $categoryId, $e->getMessage());
        }
    }
    
    /**
     * Processa a DELEÇÃO de uma categoria.
     * Rota: POST /categories/delete
     */
    public function delete(): void
    {
        $this->checkPermission('products_manage');
        $companyId = $_SESSION['company_id'];
        $categoryId = $_POST['category_id'] ?? 0;

        try {
            $this->model->delete($categoryId, $companyId);
            $this->redirectWithSuccess("/nexus-erp/public/categories", "Categoria deletada com sucesso!");
        
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/categories", $e->getMessage());
        }
    }
}