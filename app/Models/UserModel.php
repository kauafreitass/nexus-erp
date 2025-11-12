<?php
namespace App\Models;

use Database\Database;
use PDO;
use Exception;
use PDOException;

class UserModel {

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * Busca todos os usuários de UMA EMPRESA e seus perfis.
     */
    public function findAllByCompanyId($companyId)
    {
        $sql = "SELECT 
                    u.id, u.name, u.email, u.status, r.name AS role_name
                FROM 
                    users AS u
                LEFT JOIN 
                    role_user AS ru ON u.id = ru.user_id
                LEFT JOIN 
                    roles AS r ON ru.role_id = r.id
                WHERE
                    u.company_id = :company_id
                ORDER BY 
                    u.name";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':company_id' => $companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um usuário específico DENTRO DE UMA EMPRESA.
     */
    public function findByIdAndCompanyId($userId, $companyId)
    {
        $sql = "SELECT 
                    u.*, ru.role_id
                FROM 
                    users AS u
                LEFT JOIN 
                    role_user AS ru ON u.id = ru.user_id
                WHERE 
                    u.id = :user_id AND u.company_id = :company_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':company_id' => $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca todos os perfis (roles) disponíveis.
     */
    public function findAllRoles()
    {
        return $this->pdo->query("SELECT * FROM roles ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo usuário DENTRO DE UMA EMPRESA EXISTENTE (Transação).
     */
    public function store($companyId, $data)
    {
        $this->pdo->beginTransaction();
        try {
            // 1. Cria o usuário
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $sqlUser = "INSERT INTO users (
                            company_id, name, email, password, document_number, status
                        ) VALUES (
                            :company_id, :name, :email, :password, :document_number, :status
                        )";
            
            $stmtUser = $this->pdo->prepare($sqlUser);
            $stmtUser->execute([
                ':company_id' => $companyId,
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => $password_hash,
                ':document_number' => $data['document_number'] ?? '',
                ':status' => $data['status'] ?? 'ACTIVE'
            ]);
            
            $userId = $this->pdo->lastInsertId();

            // 2. Associa o perfil (role)
            $sqlRole = "INSERT INTO role_user (user_id, role_id) VALUES (?, ?)";
            $this->pdo->prepare($sqlRole)->execute([$userId, $data['role_id']]);

            // 3. Completa a transação
            $this->pdo->commit();
            return $userId;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            if ($e->getCode() == 23000) {
                throw new Exception("Erro: O Email ou Documento (CPF) já está em uso.");
            }
            throw new Exception("Erro de banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Atualiza um usuário e seu perfil (Transação).
     */
    public function update($userId, $companyId, $data)
    {
        // Garante que o usuário que está sendo editado pertence à empresa do admin
        $user = $this->findByIdAndCompanyId($userId, $companyId);
        if (!$user) {
            throw new Exception("Usuário não encontrado nesta empresa.");
        }

        $this->pdo->beginTransaction();
        try {
            // 1. Atualiza os dados da tabela 'users'
            $sqlUser = "UPDATE users SET
                            name = :name,
                            email = :email,
                            document_number = :document_number,
                            status = :status
                        WHERE id = :user_id AND company_id = :company_id";
            
            $stmtUser = $this->pdo->prepare($sqlUser);
            $stmtUser->execute([
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':document_number' => $data['document_number'] ?? '',
                ':status' => $data['status'] ?? 'ACTIVE',
                ':user_id' => $userId,
                ':company_id' => $companyId
            ]);

            // 2. Atualiza o perfil (role)
            $this->pdo->prepare("DELETE FROM role_user WHERE user_id = ?")->execute([$userId]);
            $sqlRole = "INSERT INTO role_user (user_id, role_id) VALUES (?, ?)";
            $this->pdo->prepare($sqlRole)->execute([$userId, $data['role_id']]);

            // 3. Atualiza a senha (se fornecida)
            if (!empty($data['password'])) {
                $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
                $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
                          ->execute([$password_hash, $userId]);
            }

            // 4. Completa a transação
            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            if ($e->getCode() == 23000) {
                throw new Exception("Erro: O Email ou Documento (CPF) já está em uso por outro usuário.");
            }
            throw new Exception("Erro de banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Exclui um usuário.
     */
    public function delete($userId, $companyId)
    {
        // Garante que o usuário que está sendo deletado pertence à empresa do admin
        $user = $this->findByIdAndCompanyId($userId, $companyId);
        if (!$user) {
            throw new Exception("Usuário não encontrado nesta empresa.");
        }

        // `ON DELETE CASCADE` na `role_user` e `ON DELETE SET NULL` 
        // em `customers`, `products`, etc. cuidarão das referências.
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$userId]);
    }
}