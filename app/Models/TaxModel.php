<?php
namespace App\Models;

use Database\Database;
use PDO;

class TaxModel {

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * Busca a alíquota de ICMS para um estado (UF) específico.
     */
    public function getIcmsRate(string $state_code): float
    {
        $sql = "SELECT icms_rate FROM state_tax_rates WHERE state_code = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$state_code]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (float)($result['icms_rate'] ?? 0.00); 
    }
}