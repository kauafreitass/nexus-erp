<?php
session_start();
require_once __DIR__ . '/../core/Helpers.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\ErpController;
use App\Controllers\OrderController;
use App\Controllers\NfeController;
use App\Controllers\CustomerController;
use App\Controllers\ProductController;
use App\Controllers\InventoryController;
use App\Controllers\UserController;
use App\Controllers\ReportController;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

spl_autoload_register(function ($class) {
    echo "Trying to load: " . $class . "\n";
});

// Routes

Router::get('/', [ErpController::class, 'index']);

// ERP

Router::get('/dashboard', [ErpController::class, 'dashboard']);

// Vendas
Router::get('/sales', [OrderController::class, 'salesList']);
Router::get('/sales/create', [OrderController::class, 'showCreateForm']);
Router::post('/sales/store', [OrderController::class, 'store']);
Router::get('/sales/details', [OrderController::class, 'showSaleDetails']);
Router::post('/sales/delete', [OrderController::class, 'delete']);
Router::post('/sales/update_status', [OrderController::class, 'updateStatus']);
Router::get('/sales/edit', [OrderController::class, 'showEditForm']);
Router::post('/sales/update', [OrderController::class, 'update']);
// === API (Para pesquisas AJAX) ===
// Rota: /api/customers/search?q=termo
Router::get('/api/customers/search', [CustomerController::class, 'search']);
// Rota: /api/products/search?q=termo
Router::get('/api/products/search', [ProductController::class, 'search']);

// Fiscal (NFe) 
Router::get('/nfe', [NfeController::class, 'nfeList']);
Router::get('/nfe/details', [NfeController::class, 'showNfeDetails']);
Router::post('/nfe/generate', [NfeController::class, 'generateFromOrder']);
Router::post('/nfe/cancel', [NfeController::class, 'cancel']);

// Clientes
Router::get('/customers', [CustomerController::class, 'list']);
Router::get('/customers/create', [CustomerController::class, 'showCreateForm']);
Router::post('/customers/store', [CustomerController::class, 'store']);
Router::get('/customers/edit', [CustomerController::class, 'showEditForm']);
Router::post('/customers/update', [CustomerController::class, 'update']);
Router::post('/customers/delete', [CustomerController::class, 'delete']);

// Produtos
Router::get('/products', [ProductController::class, 'list']);
Router::get('/products/create', [ProductController::class, 'showCreateForm']);
Router::post('/products/store', [ProductController::class, 'store']);
Router::get('/products/edit', [ProductController::class, 'showEditForm']);
Router::post('/products/update', [ProductController::class, 'update']);
Router::post('/products/delete', [ProductController::class, 'delete']);

// Estoque
Router::get('/supplies', [InventoryController::class, 'list']);
Router::get('/supplies/details', [InventoryController::class, 'showDetails']);
Router::post('/supplies/adjust', [InventoryController::class, 'adjustStock']);

// Relatórios
Router::get('/reports', [ReportController::class, 'dashboard']); 
Router::get('/reports/dashboard', [ReportController::class, 'dashboard']);
Router::get('/reports/sales', [ReportController::class, 'salesReport']);

// Auth
Router::get('/login', [AuthController::class, 'showLogin']);
Router::get('/register', [AuthController::class, 'showRegister']);
Router::get('/forgot-password', [AuthController::class, 'showForgotPassword']);
Router::get('/logout', [AuthController::class, 'showLogout']);
Router::post('/login', [AuthController::class, 'handleLogin']);
Router::post('/register', [AuthController::class, 'handleRegister']);
Router::post('/forgot-password', [AuthController::class, 'handleForgotPassword']);

// Controle de usuários
Router::get('/users', [UserController::class, 'list']);
Router::get('/users/create', [UserController::class, 'showCreateForm']);
Router::post('/users/store', [UserController::class, 'store']);
Router::get('/users/edit', [UserController::class, 'showEditForm']);
Router::post('/users/update', [UserController::class, 'update']);
Router::post('/users/delete', [UserController::class, 'delete']);

// Settings
Router::get('/account', [AuthController::class, 'showAccount']);
Router::post('/account/update', [AuthController::class, 'update']);