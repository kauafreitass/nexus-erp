<?php
namespace App\Controllers;

use Exception;

class BaseController {

    /**
     * Garante que o usuário esteja autenticado.
     */
    protected function checkAuth(): void
    {
        if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== 'authenticated') {
            $_SESSION['error'] = 'Você precisa estar logado para acessar esta página.';
            header('Location: /nexus-erp/public/login'); 
            exit;
        }
    }

    /**
     * Garante que o usuário NÃO esteja autenticado (convidado).
     */
    protected function checkGuest(): void
    {
        if (isset($_SESSION['auth']) && $_SESSION['auth'] === 'authenticated') {
            header('Location: /nexus-erp/public/dashboard'); 
            exit;
        }
    }

    /**
     * Garante que o usuário logado tenha uma permissão específica.
     *
     * @param string $permissionName O nome da permissão a ser verificada (ex: 'customers_manage').
     */
    protected function checkPermission(string $permissionName): void
    {
        // 1. Garante que está logado
        $this->checkAuth();
        
        // 2. Verifica se as permissões estão na sessão
        if (!isset($_SESSION['permissions'])) {
            // Se as permissões não estiverem carregadas, redireciona para o login
            // Isso pode acontecer se a sessão for corrompida ou expirada
            $this->redirectWithError("/nexus-erp/public/login", "Sessão inválida. Por favor, faça login novamente.");
            return;
        }
        
        // 3. Verifica se a permissão específica existe no array de permissões do usuário
        if (!in_array($permissionName, $_SESSION['permissions'])) {
            // Regra "fail-safe": redireciona para o dashboard com erro se não tiver permissão.
            $this->redirectWithError("/nexus-erp/public/dashboard", "Acesso negado. Você não tem permissão para esta ação.");
            return;
        }
    }
    
    // --- Funções Auxiliares de Redirecionamento ---

    /**
     * Redireciona para uma URL com uma mensagem de sucesso na sessão.
     * @param string $url A URL para redirecionar.
     * @param string $message A mensagem de sucesso.
     */
    protected function redirectWithSuccess($url, $message): void
    {
        $_SESSION['message'] = $message;
        header("Location: $url");
        exit;
    }

    /**
     * Redireciona para uma URL com uma mensagem de erro na sessão.
     * @param string $url A URL para redirecionar.
     * @param string $message A mensagem de erro.
     */
    protected function redirectWithError($url, $message): void
    {
        $_SESSION['error'] = $message;
        header("Location: $url");
        exit;
    }
}