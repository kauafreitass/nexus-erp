<?php
namespace App\Controllers;

use App\Models\UserModel;
use Exception;

class UserController extends BaseController 
{
    private UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    /**
     * Rota: GET /users
     * Exibe a lista de usuários DA EMPRESA.
     */
    public function list(): void
    {
        $this->checkPermission('users_manage'); 
        $companyId = $_SESSION['company_id'];
        
        $users = $this->model->findAllByCompanyId($companyId);
        
        view('erp/users/list', [
            'activePage' => 'users',
            'users' => $users
        ]);
    }

    /**
     * Rota: GET /users/create
     * Exibe o formulário de criação.
     */
    public function showCreateForm(): void
    {
        $this->checkPermission('users_manage');
        $roles = $this->model->findAllRoles();
        
        view('erp/users/form', [
            'activePage' => 'users',
            'user' => null, 
            'roles' => $roles,
            'action' => '/nexus-erp/public/users/store'
        ]);
    }

    /**
     * Rota: POST /users/store
     * Processa a criação de um novo usuário DENTRO DA EMPRESA.
     */
    public function store(): void
    {
        $this->checkPermission('users_manage');
        $companyId = $_SESSION['company_id'];
        
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role_id'])) {
            $this->redirectWithError("/nexus-erp/public/users/create", "Nome, Email, Senha e Perfil são obrigatórios.");
            return;
        }

        try {
            $this->model->store($companyId, $_POST);
            $this->redirectWithSuccess("/nexus-erp/public/users", "Usuário criado com sucesso!");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/users/create", $e->getMessage());
        }
    }

    /**
     * Rota: GET /users/edit
     * Exibe o formulário de edição.
     */
    public function showEditForm(): void
    {
        $this->checkPermission('users_manage');
        $companyId = $_SESSION['company_id'];
        $userId = $_GET['id'] ?? 0;

        $user = $this->model->findByIdAndCompanyId($userId, $companyId);
        $roles = $this->model->findAllRoles();

        if (!$user) {
            $this->redirectWithError("/nexus-erp/public/users", "Usuário não encontrado.");
            return;
        }

        view('erp/users/form', [
            'activePage' => 'users',
            'user' => $user, 
            'roles' => $roles,
            'action' => '/nexus-erp/public/users/update'
        ]);
    }

    /**
     * Rota: POST /users/update
     * Processa a atualização de um usuário.
     */
    public function update(): void
    {
        $this->checkPermission('users_manage');
        $companyId = $_SESSION['company_id'];
        $userId = $_POST['user_id'] ?? 0;

        if (empty($userId) || empty($_POST['name']) || empty($_POST['email']) || empty($_POST['role_id'])) {
            $this->redirectWithError("/nexus-erp/public/users/edit?id=" . $userId, "Nome, Email e Perfil são obrigatórios.");
            return;
        }
        
        if (!empty($_POST['password']) && strlen($_POST['password']) < 6) {
             $this->redirectWithError("/nexus-erp/public/users/edit?id=" . $userId, "A nova senha deve ter pelo menos 6 caracteres.");
             return;
        }

        try {
            $this->model->update($userId, $companyId, $_POST);
            $this->redirectWithSuccess("/nexus-erp/public/users/edit?id=" . $userId, "Usuário atualizado com sucesso!");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/users/edit?id=" . $userId, $e->getMessage());
        }
    }

    /**
     * Rota: POST /users/delete
     * Processa a exclusão de um usuário.
     */
    public function delete(): void
    {
        $this->checkPermission('users_manage');
        $companyId = $_SESSION['company_id'];
        $userId = $_POST['user_id'] ?? 0;

        if ($userId == $_SESSION['user']['id']) {
            $this->redirectWithError("/nexus-erp/public/users", "Você não pode excluir sua própria conta.");
            return;
        }

        try {
            $this->model->delete($userId, $companyId);
            $this->redirectWithSuccess("/nexus-erp/public/users", "Usuário excluído com sucesso.");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/users", $e->getMessage());
        }
    }
}