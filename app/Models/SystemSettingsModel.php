<?php
namespace App\Models;

use Database\Database;
use PDO;

class SystemSettingsModel {

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * Busca o valor de uma configuração específica do sistema.
     */
    public function getSetting(string $key): ?string
    {
        $sql = "SELECT setting_value FROM system_settings WHERE setting_key = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['setting_value'] ?? null;
    }
}