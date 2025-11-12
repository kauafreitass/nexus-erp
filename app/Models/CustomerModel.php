<?php
namespace App\Models;

use Database\Database;
use PDO;
use Exception;
use PDOException;

class CustomerModel
{

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * R: (Read) Busca todos os clientes da EMPRESA.
     */
    public function findAllByCompanyId($companyId)
    {
        $sql = "SELECT id, name, business_name, document_number, email, phone 
                FROM customers 
                WHERE company_id = ? 
                ORDER BY name";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * R: (Read) Busca um cliente específico da EMPRESA.
     */
    public function findByIdAndCompanyId($customerId, $companyId)
    {
        $sql = "SELECT * FROM customers WHERE id = ? AND company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$customerId, $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * API: (Read) Busca clientes pelo nome ou documento DENTRO DA EMPRESA.
     */
    public function searchByName($companyId, $query)
    {
        $sql = "SELECT id, name, document_number 
                FROM customers 
                WHERE company_id = :company_id 
                  AND (name LIKE :query OR document_number LIKE :query)
                LIMIT 10";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':company_id' => $companyId,
            ':query' => '%' . $query . '%'
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * C: (Create) Cria um novo cliente para a EMPRESA e o USUÁRIO.
     */
    public function store($userId, $companyId, $data)
    {
        $sql = "INSERT INTO customers (
                    company_id, user_id, name, business_name, document_type, document_number, 
                    state_registration, municipal_registration, email, phone, 
                    address_street, address_number, address_complement, 
                    address_neighborhood, address_city, address_state, address_zipcode, 
                    address_country_code
                ) VALUES (
                    :company_id, :user_id, :name, :business_name, :document_type, :document_number, 
                    :state_registration, :municipal_registration, :email, :phone, 
                    :address_street, :address_number, :address_complement, 
                    :address_neighborhood, :address_city, :address_state, :address_zipcode, 
                    '1058'
                )";

        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute([
                ':company_id' => $companyId,
                ':user_id' => $userId,
                ':name' => $data['name'],
                ':business_name' => $data['business_name'] ?? null,
                ':document_type' => $data['document_type'],
                ':document_number' => $data['document_number'],
                ':state_registration' => $data['state_registration'] ?? null,
                ':municipal_registration' => $data['municipal_registration'] ?? null,
                ':email' => $data['email'] ?? null,
                ':phone' => $data['phone'] ?? null,
                ':address_street' => $data['address_street'],
                ':address_number' => $data['address_number'],
                ':address_complement' => $data['address_complement'] ?? null,
                ':address_neighborhood' => $data['address_neighborhood'],
                ':address_city' => $data['address_city'],
                ':address_state' => $data['address_state'],
                ':address_zipcode' => $data['address_zipcode']
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("Erro: O número do documento (CPF/CNPJ) já está em uso nesta empresa.");
            }
            throw $e;
        }
    }

    /**
     * U: (Update) Atualiza um cliente da EMPRESA.
     */
    public function update($customerId, $companyId, $data)
    {
        $sql = "UPDATE customers SET
                    name = :name,
                    business_name = :business_name,
                    document_type = :document_type,
                    document_number = :document_number,
                    state_registration = :state_registration,
                    municipal_registration = :municipal_registration,
                    email = :email,
                    phone = :phone,
                    address_street = :address_street,
                    address_number = :address_number,
                    address_complement = :address_complement,
                    address_neighborhood = :address_neighborhood,
                    address_city = :address_city,
                    address_state = :address_state,
                    address_zipcode = :address_zipcode
                WHERE
                    id = :customer_id AND company_id = :company_id";

        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute([
                ':name' => $data['name'],
                ':business_name' => $data['business_name'] ?? null,
                ':document_type' => $data['document_type'],
                ':document_number' => $data['document_number'],
                ':state_registration' => $data['state_registration'] ?? null,
                ':municipal_registration' => $data['municipal_registration'] ?? null,
                ':email' => $data['email'] ?? null,
                ':phone' => $data['phone'] ?? null,
                ':address_street' => $data['address_street'],
                ':address_number' => $data['address_number'],
                ':address_complement' => $data['address_complement'] ?? null,
                ':address_neighborhood' => $data['address_neighborhood'],
                ':address_city' => $data['address_city'],
                ':address_state' => $data['address_state'],
                ':address_zipcode' => $data['address_zipcode'],
                ':customer_id' => $customerId,
                ':company_id' => $companyId
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("Erro: O número do documento (CPF/CNPJ) já está em uso por outro cliente.");
            }
            throw $e;
        }
    }

    /**
     * D: (Delete) Exclui um cliente da EMPRESA.
     */
    public function delete($customerId, $companyId)
    {
        $customer = $this->findByIdAndCompanyId($customerId, $companyId);
        if (!$customer) {
            throw new Exception("Cliente não encontrado ou não pertence à sua empresa.");
        }

        $sqlCheck = "SELECT (
                        (SELECT COUNT(*) FROM sales_orders WHERE customer_id = :id1) +
                        (SELECT COUNT(*) FROM nfe WHERE customer_id = :id2)
                    ) AS total_references";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([':id1' => $customerId, ':id2' => $customerId]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($result['total_references'] > 0) {
            throw new Exception("Este cliente não pode ser excluído pois está vinculado a pedidos ou notas fiscais.");
        }

        $sql = "DELETE FROM customers WHERE id = ? AND company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$customerId, $companyId]);
    }


    /**
     *
     * R: (Read) Busca dados de novos clientes por mês para o gráfico do dashboard.
     */
    public function getCustomerDataForChart($companyId)
    {
        // Conta novos clientes dos últimos 6 meses
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') AS mes,
                    COUNT(id) AS novos_clientes
                FROM 
                    customers
                WHERE 
                    company_id = ?
                    AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY 
                    mes
                ORDER BY 
                    mes ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Formata para o Google Charts
        $data = [['Mês', 'Novos Clientes']];
        foreach ($results as $row) {
            $data[] = [$row['mes'], (int) $row['novos_clientes']];
        }

        // Se não houver dados, retorna um placeholder
        if (count($data) === 1) {
            $data[] = ['Nenhum', 0];
        }
        return $data;
    }
    /**
     * NOVO: Retorna os Top N Clientes por Valor de Venda para o gráfico.
     * @param int $companyId O ID da empresa.
     * @param int $limit O número de clientes a retornar (padrão: 5).
     * @return array Dados formatados para o Google Charts.
     */
    public function getTopCustomersForChart($companyId, $limit = 5)
    {
        $sql = "SELECT 
                    c.name AS customer_name,
                    SUM(so.total_amount) AS total_spent
                FROM customers AS c
                JOIN sales_orders AS so ON c.id = so.customer_id
                WHERE so.company_id = :company_id
                  AND so.status IN ('CONFIRMED', 'INVOICED') -- Apenas vendas efetivadas
                GROUP BY c.id, c.name
                ORDER BY total_spent DESC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':company_id', $companyId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [['Cliente', 'Valor Gasto']];
        if (empty($results)) {
            $data[] = ['Nenhum cliente', 0];
            return $data;
        }
        foreach ($results as $row) {
            $data[] = [$row['customer_name'], (float) $row['total_spent']];
        }
        return $data;
    }
}