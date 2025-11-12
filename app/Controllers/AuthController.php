<?php
namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\PermissionModel;
use App\Models\CompanyModel; // Precisa do CompanyModel
use Exception;

class AuthController extends BaseController // <-- 1. HERDA DO BASECONTROLLER
{
    private AuthModel $model;
    private PermissionModel $permissionModel;
    private CompanyModel $companyModel;

    public function __construct()
    {
        $this->model = new AuthModel();
        $this->permissionModel = new PermissionModel();
        $this->companyModel = new CompanyModel();
    }

    // --- MÉTODOS DE EXIBIÇÃO DE VIEW ---

    public function showLogin(): void
    {
        $this->checkGuest();
        view('auth/login');
    }

    public function showRegister(): void
    {
        $this->checkGuest();
        view('auth/register');
    }

    public function showForgotPasswordForm()
    {
        if (isset($_SESSION['user_id'])) {
            header('/nexus-erp/public/dashboard');
        }
        view('auth/forgot-password');
    }

    public function showLogout(): void
    {
        $this->checkAuth();
        session_destroy();
        header('Location: /nexus-erp/public/login');
        exit;
    }

    public function showAccount(): void
    {
        $this->checkAuth();
        $companyId = $_SESSION['company_id'];

        // Busca os dados da EMPRESA para preencher o formulário
        $company = $this->companyModel->findById($companyId);

        view('auth/account', [
            'activePage' => 'account',
            'company' => $company // Passa os dados da empresa para a view
        ]);
    }

    // --- MÉTODOS DE PROCESSAMENTO DE FORMULÁRIO (HANDLE) ---

    public function handleRegister(): void
    {
        $this->checkGuest();

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        // O $document_number vem do formulário de registro
        $document_number = $_POST['document_number'] ?? '';

        try {
            // Usa o método de registro que cria a empresa e o usuário admin
            $this->model->registerNewCompanyAndAdminUser($name, $email, $password, $document_number);
            $this->redirectWithSuccess("/nexus-erp/public/login", "Sua empresa e conta de administrador foram criadas com sucesso! Faça login.");

        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/register", $e->getMessage());
        }
    }

    public function handleLogin($internalCall = false): void
    {
        if (!$internalCall) {
            $this->checkGuest();
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            $user = $this->model->login($email, $password);
            $permissions = $this->permissionModel->getPermissionsForUser($user['id']);

            $_SESSION['user'] = $user;
            $_SESSION['auth'] = 'authenticated';
            $_SESSION['permissions'] = $permissions;
            $_SESSION['company_id'] = $user['company_id'];
            $_SESSION['role_name'] = $user['role_name'];

            $this->redirectWithSuccess("/nexus-erp/public/dashboard", message: "");

        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/login", $e->getMessage());
        }
    }

    // ============================================
    // == MÉTODO ANTIGO SUBSTITUÍDO POR ESTE AQUI ==
    // ============================================
    public function handleForgotPassword()
    {
        // 1. Redireciona se o usuário já estiver logado
        if (isset($_SESSION['user'])) {
            header('Location: /nexus-erp/public/dashboard');
            exit;
        }

        // 2. Pega os dados do formulário (com os 'names' do seu HTML)
        $email = $_POST['email'] ?? '';
        $newPassword = $_POST['nova_senha'] ?? ''; // Nome do campo no seu HTML
        
        $redirectPath = "/nexus-erp/public/forgot-password";

        try {
            // 3. Validações básicas
            if (empty($email) || empty($newPassword)) {
                throw new Exception("E-mail e nova senha são obrigatórios.");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Formato de e-mail inválido.");
            }
            if (strlen($newPassword) < 6) {
                throw new Exception("A nova senha deve ter pelo menos 6 caracteres.");
            }

            // 4. Verifica se o email existe (usando o método que criamos no Passo 2)
            $user = $this->model->findByEmail($email); // Usando $this->model

            if (!$user) {
                // Mensagem genérica por segurança, para não revelar e-mails cadastrados
                throw new Exception("Não foi possível redefinir a senha. Verifique o e-mail digitado.");
            }

            // 5. Se o usuário existe, chama o método do AuthModel para atualizar a senha
            $this->model->forgotPassword($email, $newPassword);

            // 6. Redireciona para o login com mensagem de sucesso
            $this->redirectWithSuccess(
                "/nexus-erp/public/login", 
                "Sua senha foi redefinida com sucesso! Você já pode fazer login."
            );

        } catch (Exception $e) {
            // 7. Em caso de erro, volta para o formulário com a mensagem
            $this->redirectWithError($redirectPath, $e->getMessage());
        }
    }


    /**
     * Rota de atualização principal para a página "Minha Conta".
     * Lida com todas as ações dos modais.
     */
    public function update(): void
    {
        $this->checkAuth();

        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $action = $_POST['action'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';

        // Senha atual é obrigatória para TODAS as alterações
        if (empty($currentPassword)) {
            $this->redirectWithError("/nexus-erp/public/account", "A senha atual é obrigatória para confirmar as alterações.");
            return;
        }

        try {
            $message = "";

            switch ($action) {
                case 'update_password':
                    $newPassword = $_POST['new_password'] ?? '';
                    $confirmPassword = $_POST['confirm_password'] ?? '';
                    $this->model->updatePassword($userId, $currentPassword, $newPassword, $confirmPassword);
                    $message = "Senha atualizada com sucesso!";
                    break;

                case 'update_email':
                    $newEmail = $_POST['new_email'] ?? '';
                    $this->model->updateEmail($userId, $newEmail, $currentPassword);
                    $message = "Email de acesso atualizado com sucesso!";
                    break;

                case 'update_name':
                    $newName = $_POST['new_name'] ?? '';
                    $this->model->updateName($userId, $newName, $currentPassword);
                    $message = "Nome de usuário atualizado com sucesso!";
                    break;

                // Esta é a ação que estava a falhar
                case 'update_company_data':
                    // Apenas Admins podem mudar dados da empresa
                    $this->checkPermission('settings_manage');

                    // O CompanyModel->update não precisa da senha, mas a verificamos
                    $this->companyModel->update($companyId, $_POST);
                    $message = "Dados da empresa atualizados com sucesso!";
                    break;

                default:
                    // Causa do erro "Ação desconhecida"
                    throw new Exception("Ação de atualização desconhecida ou não especificada.");
            }

            // Se a atualização deu certo, atualiza a sessão
            $this->updateSessionData($userId);
            $this->redirectWithSuccess("/nexus-erp/public/account", $message);

        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/account", $e->getMessage());
        }
    }

    /**
     * Atualiza os dados do usuário na sessão após uma alteração.
     */
    private function updateSessionData($userId): void
    {
        try {
            $updatedUser = $this->model->findUserById($userId);
            if ($updatedUser) {
                // Preserva as permissões, atualiza o resto
                $_SESSION['user'] = $updatedUser;
                $_SESSION['company_id'] = $updatedUser['company_id'];
                $_SESSION['role_name'] = $updatedUser['role_name'];
            }
        } catch (Exception $e) {
            error_log("Erro ao atualizar dados da sessão para o usuário $userId: " . $e->getMessage());
        }
    }
}