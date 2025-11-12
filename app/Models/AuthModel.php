<?php
namespace App\Models;

use Database\Database;
use \PDO;
use \Exception;
use \PDOException;

class AuthModel
{
    private $pdo;
    private $companyModel; // Dependência do CompanyModel

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
        $this->companyModel = new CompanyModel(); // Instancia o CompanyModel
    }

    /**
     * Tenta logar um usuário.
     * Retorna os dados do usuário + company_id + role_name.
     */
    public function login($email, $password): array
    {
        $sql = "SELECT 
                    u.*, 
                    r.name AS role_name 
                FROM 
                    users AS u
                LEFT JOIN 
                    role_user AS ru ON u.id = ru.user_id
                LEFT JOIN 
                    roles AS r ON ru.role_id = r.id
                WHERE 
                    u.email = :email";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Email ou senha incorretos.");
        }
        
        if ($user['status'] !== 'ACTIVE') {
            throw new Exception("Esta conta está desativada.");
        }

        return $user;
    }
    
    /**
     * Busca um usuário apenas pelo seu ID (para atualizar a sessão).
     */
    public function findUserById($userId): ?array
    {
        $sql = "SELECT u.*, r.name AS role_name 
                FROM users AS u
                LEFT JOIN role_user AS ru ON u.id = ru.user_id
                LEFT JOIN roles AS r ON ru.role_id = r.id
                WHERE u.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }

    /**
     * Registra uma NOVA EMPRESA e seu primeiro USUÁRIO (Admin).
     */
    public function registerNewCompanyAndAdminUser($name, $email, $password, $document_number): string
    {
        $this->pdo->beginTransaction();
        try {
            // 1. Cria a nova empresa
            $companyData = [
                'name' => $name . " (Empresa)",
                'business_name' => $name,
                'email' => $email,
                'document_number' => $document_number,
                'legal_nature' => 'LTDA'
            ];
            $companyId = $this->companyModel->create($companyData);

            // 2. Cria o usuário (Administrador)
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $adminRoleId = 1; // ID '1' é 'Administrador'
            $userDocument = preg_replace('/\D/', '', $document_number); // Simplificação

            $sql = "INSERT INTO users (
                        name, email, password, document_number, 
                        company_id, status
                    ) VALUES (
                        :name, :email, :password, :document_number, 
                        :company_id, 'ACTIVE'
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password_hash,
                ':document_number' => $userDocument,
                ':company_id' => $companyId
            ]);
            
            $userId = $this->pdo->lastInsertId();
            
            // 3. Associa o perfil de Administrador
            $sqlRole = "INSERT INTO role_user (user_id, role_id) VALUES (?, ?)";
            $this->pdo->prepare($sqlRole)->execute([$userId, $adminRoleId]);

            $this->pdo->commit();
            return $userId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            // Lança a exceção original (ex: "CNPJ já em uso")
            throw $e;
        }
    }

    /**
     * Verifica a senha atual de um usuário.
     */
    private function findAndVerifyPassword($userId, $currentPassword): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Usuário não encontrado.");
        }
        
        if (!password_verify($currentPassword, $user['password'])) {
            throw new Exception("Senha atual incorreta.");
        }
        
        return $user;
    }

    /**
     * Atualiza o nome do usuário após verificar a senha.
     */
    public function updateName($userId, $newName, $currentPassword): bool
    {
        if (empty(trim($newName))) {
            throw new Exception("O campo de nome não pode estar vazio.");
        }
        $this->findAndVerifyPassword($userId, $currentPassword);
        
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$newName, $userId]);
    }

    /**
     * Atualiza o e-mail do usuário após verificar a senha.
     */
    public function updateEmail($userId, $newEmail, $currentPassword): bool
    {
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Formato de email inválido.");
        }
        $this->findAndVerifyPassword($userId, $currentPassword);
        
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET email = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$newEmail, $userId]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("Esse email já está em uso por outra conta.");
            }
            throw $e;
        }
    }

    /**
     * Atualiza a senha do usuário após verificar a senha antiga.
     */
    public function updatePassword($userId, $currentPassword, $newPassword, $confirmPassword): bool
    {
        if ($newPassword !== $confirmPassword) {
            throw new Exception("As novas senhas não coincidem.");
        }
        if (strlen($newPassword) < 6) {
            throw new Exception("A nova senha deve ter pelo menos 6 caracteres.");
        }

        $this->findAndVerifyPassword($userId, $currentPassword);
        
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$newPasswordHash, $userId]);
    }
    
    /**
     * Atualiza a senha (fluxo "esqueci minha senha").
     */
    public function forgotPassword($email, $new_password): bool
    {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = :password, updated_at = NOW() WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':password' => $new_password_hash,
            ':email' => $email
        ]);
    }
}