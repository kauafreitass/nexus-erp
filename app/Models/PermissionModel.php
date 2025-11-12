<?php
namespace App\Models;

use Database\Database;
use PDO;
use Exception;

class PermissionModel {

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * Busca todas as "strings" de permissão (ex: 'customers_manage')
     * para um usuário específico.
     */
    public function getPermissionsForUser($userId)
    {
        $sql = "SELECT 
                    p.name
                FROM 
                    permissions AS p
                JOIN 
                    role_permissions AS rp ON p.id = rp.permission_id
                JOIN 
                    roles AS r ON rp.role_id = r.id
                JOIN 
                    role_user AS ru ON r.id = ru.role_id
                WHERE 
                    ru.user_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}