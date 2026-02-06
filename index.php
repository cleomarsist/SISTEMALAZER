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
define('VIEWS_PATH', APP_PATH . '/views');

// Iniciar sessão
session_start();

// Header padrão
http_response_code(200);
header('Content-Type: text/html; charset=utf-8');

// Obter página requisitada
$page = $_GET['page'] ?? 'dashboard';
$view = null;

// Mapeamento de rotas
$routes = [
    'dashboard' => VIEWS_PATH . '/dashboard.php',
    'clientes' => VIEWS_PATH . '/clientes_lista.php',
    'cliente-novo' => VIEWS_PATH . '/cliente_form.php',
    'cliente-form' => VIEWS_PATH . '/cliente_form.php',
    'orcamentos' => VIEWS_PATH . '/orcamentos_lista.php',
];

// Obter view da rota
if (array_key_exists($page, $routes)) {
    $view = $routes[$page];
} else {
    $view = $routes['dashboard']; // padrão
}

// Verificar arquivo
if (!file_exists($view)) {
    http_response_code(404);
    echo '<div class="alert alert-danger">Arquivo não encontrado: ' . htmlspecialchars($page) . '</div>';
    echo '<p>Procurando em: ' . htmlspecialchars($view) . '</p>';
    exit;
}

// Preparar variáveis para layout
$page_name = basename($view, '.php');
$title = ucfirst(str_replace('_', ' ', $page_name));

// Incluir layout (que incluirá a view específica)
include VIEWS_PATH . '/layout.php';
?>
