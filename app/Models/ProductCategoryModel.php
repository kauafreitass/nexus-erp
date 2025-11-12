<?php
namespace App\Models;

use Database\Database;
use \PDO;
use \Exception;

class ProductCategoryModel
{
    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * Lista todas as categorias de UMA empresa específica.
     */
    public function findAllByCompany(string|int $companyId): array
    {
        // Usando BIGINT(20) UNSIGNED, é mais seguro tratar como string no bind
        $sql = "SELECT * FROM product_categories WHERE company_id = ? ORDER BY name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca uma categoria específica pelo ID.
     */
    public function findById(string|int $id, string|int $companyId): ?array
    {
        $sql = "SELECT * FROM product_categories WHERE id = ? AND company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $companyId]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        return $category ?: null;
    }

    /**
     * Cria uma nova categoria.
     */
    public function create(array $data): string
    {
        if (empty($data['name']) || empty($data['company_id'])) {
            throw new Exception("Nome e ID da empresa são obrigatórios.");
        }

        $sql = "INSERT INTO product_categories (name, company_id, status) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['company_id'],
            $data['status'] ?? 'ACTIVE'
        ]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Atualiza uma categoria existente.
     */
    public function update(string|int $id, string|int $companyId, array $data): bool
    {
        if (empty($data['name']) || empty($data['status'])) {
            throw new Exception("Nome e Status são obrigatórios.");
        }

        $sql = "UPDATE product_categories SET name = ?, status = ? WHERE id = ? AND company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['status'],
            $id,
            $companyId
        ]);
    }

    /**
     * Deleta uma categoria.
     */
    public function delete(string|int $id, string|int $companyId): bool
    {
        // Verifica se a categoria existe e pertence à empresa
        $category = $this->findById($id, $companyId);
        if (!$category) {
            throw new Exception("Categoria não encontrada ou não pertence à sua empresa.");
        }
        
        // A chave estrangeira na tabela 'products' está como ON DELETE SET NULL,
        // então não precisamos nos preocupar em apagar produtos.
        
        $sql = "DELETE FROM product_categories WHERE id = ? AND company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id, $companyId]);
    }
}