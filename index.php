<?php
/**
 * Ponto de entrada principal - index.php
 * ETAPA 4: Views e Templates
 * 
 * Sistema Lazer v1.0
 */

// Configuração básica
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Iniciar sessão
session_start();

// Header padrão para status OK
http_response_code(200);
header('Content-Type: text/html; charset=utf-8');

// Simular página solicitada (em produção, usar roteador)
$page = $_GET['page'] ?? 'dashboard';
$view = null;

// Mapeamento de rotas simples
$routes = [
    'dashboard' => APP_PATH . '/Views/dashboard.php',
    'clientes' => APP_PATH . '/Views/clientes_lista.php',
    'cliente-form' => APP_PATH . '/Views/cliente_form.php',
    'orcamentos' => APP_PATH . '/Views/orcamentos_lista.php',
];

// Validar rota
if (array_key_exists($page, $routes)) {
    $view = $routes[$page];
} else {
    // Página padrão
    $view = $routes['dashboard'];
}

// Verificar si o arquivo de view existe
if (!file_exists($view)) {
    http_response_code(404);
    echo '<div class="alert alert-danger">Página não encontrada: ' . htmlspecialchars($page) . '</div>';
    exit;
}

// Definir variáveis para a view
$page = basename($view, '.php');
$title = ucfirst(str_replace('_', ' ', $page));

// Carregar layout
include APP_PATH . '/Views/layout.php';
?>
