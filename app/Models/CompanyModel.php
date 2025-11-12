<?php
namespace App\Models;

use Database\Database;
use PDO;
use Exception;
use PDOException;

class CompanyModel {
    private $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * Busca uma empresa pelo ID.
     */
    public function findById($companyId) {
        $stmt = $this->pdo->prepare("SELECT * FROM companies WHERE id = ?");
        $stmt->execute([$companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria uma nova empresa.
     * Usado durante o registro de um novo usuário Admin.
     */
    public function create($data) {
        $sql = "INSERT INTO companies (
                    name, business_name, document_number, email, phone, 
                    legal_nature, state_registration, municipal_registration
                ) VALUES (
                    :name, :business_name, :document_number, :email, :phone, 
                    :legal_nature, :state_registration, :municipal_registration
                )";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([
                ':name' => $data['name'],
                ':business_name' => $data['business_name'] ?? $data['name'],
                ':document_number' => $data['document_number'],
                ':email' => $data['email'],
                ':phone' => $data['phone'] ?? null,
                ':legal_nature' => $data['legal_nature'] ?? 'LTDA',
                ':state_registration' => $data['state_registration'] ?? null,
                ':municipal_registration' => $data['municipal_registration'] ?? null
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("O CNPJ ou Email informado já está em uso por outra empresa.");
            }
            throw $e;
        }
    }

    /**
     * Atualiza os dados de uma empresa.
     * Usado pela página "Minha Conta" do Admin.
     */
    public function update($companyId, $data) {
        $sql = "UPDATE companies SET
                    name = :name,
                    business_name = :business_name,
                    document_number = :document_number,
                    email = :email,
                    phone = :phone,
                    legal_nature = :legal_nature,
                    state_registration = :state_registration,
                    municipal_registration = :municipal_registration,
                    address_street = :address_street,
                    address_number = :address_number,
                    address_complement = :address_complement,
                    address_neighborhood = :address_neighborhood,
                    address_city = :address_city,
                    address_state = :address_state,
                    address_zipcode = :address_zipcode
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        try {
            return $stmt->execute([
                ':name' => $data['name'],
                ':business_name' => $data['business_name'] ?? $data['name'],
                ':document_number' => $data['document_number'],
                ':email' => $data['email'] ?? null,
                ':phone' => $data['phone'] ?? null,
                ':legal_nature' => $data['legal_nature'],
                ':state_registration' => $data['state_registration'] ?? null,
                ':municipal_registration' => $data['municipal_registration'] ?? null,
                ':address_street' => $data['address_street'] ?? null,
                ':address_number' => $data['address_number'] ?? null,
                ':address_complement' => $data['address_complement'] ?? null,
                ':address_neighborhood' => $data['address_neighborhood'] ?? null,
                ':address_city' => $data['address_city'] ?? null,
                ':address_state' => $data['address_state'] ?? null,
                ':address_zipcode' => $data['address_zipcode'] ?? null,
                ':id' => $companyId
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("O CNPJ ou Email informado já está em uso por outra empresa.");
            }
            throw $e;
        }
    }
}