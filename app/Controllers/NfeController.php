<?php
namespace App\Controllers;

use App\Models\NfeModel;
use Exception;

class NfeController extends BaseController {

    private $nfeModel;

    public function __construct() {
        $this->nfeModel = new NfeModel();
    }

    /**
     * Rota: GET /fiscal
     */
    public function nfeList() {
        $this->checkPermission('nfe_view');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $roleName = $_SESSION['role_name'];
        
        $notas = $this->nfeModel->findAllByHierarchy($companyId, $userId, $roleName);
        
        view('erp/nfe/list', [
            'activePage' => 'nfe',
            'notas' => $notas
        ]);
    }

    /**
     * Rota: GET /fiscal/details
     */
    public function showNfeDetails() {
        $this->checkPermission('nfe_view');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $roleName = $_SESSION['role_name'];
        $nfeId = $_GET['id'] ?? 0;
        
        $nota = $this->nfeModel->findByIdAndHierarchy($nfeId, $companyId, $userId, $roleName);
        
        if (!$nota) {
            $this->redirectWithError("/nexus-erp/public/nfe", "Nota Fiscal não encontrada ou inacessível.");
            return;
        }
        
        view('erp/nfe/details', [
            'activePage' => 'nfe',
            'nota' => $nota,
            'itens' => $this->nfeModel->findItemsByNfeId($nfeId)
        ]);
    }
    
    /**
     * Rota: POST /fiscal/generate
     */
    public function generateFromOrder() {
        $this->checkPermission('nfe_issue');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $roleName = $_SESSION['role_name'];
        $orderId = $_POST['sales_order_id'];

        if (empty($orderId)) {
            $this->redirectWithError("/nexus-erp/public/sales", "ID do pedido não fornecido.");
            return;
        }

        try {
            $nfeId = $this->nfeModel->createFromOrder($orderId, $userId, $companyId, $roleName);
            $this->redirectWithSuccess("/nexus-erp/public/nfe/details?id=" . $nfeId, "NFe gerada com sucesso!");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/sales/details?id=" . $orderId, "Erro ao gerar NFe: " . $e->getMessage());
        }
    }

    /**
     * Rota: POST /fiscal/cancel
     */
    public function cancel() {
        $this->checkPermission('nfe_cancel');
        
        $userId = $_SESSION['user']['id'];
        $companyId = $_SESSION['company_id'];
        $roleName = $_SESSION['role_name'];
        $nfeId = $_POST['nfe_id'];

        try {
            $this->nfeModel->cancelNfe($nfeId, $companyId, $userId, $roleName);
            $this->redirectWithSuccess("/nexus-erp/public/nfe/details?id=" . $nfeId, "NFe cancelada com sucesso.");
        } catch (Exception $e) {
            $this->redirectWithError("/nexus-erp/public/nfe/details?id=" . $nfeId, "Erro ao cancelar: " . $e->getMessage());
        }
    }
}