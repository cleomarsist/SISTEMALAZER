<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "=== TESTE RÁPIDO DO SISTEMA ===\n\n";

echo "1. Verificando diretórios...\n";
echo "   app/views: " . (is_dir(__DIR__ . '/app/views') ? "OK" : "ERRO") . "\n";
echo "   public/css: " . (is_dir(__DIR__ . '/public/css') ? "OK" : "ERRO") . "\n";
echo "   public/js: " . (is_dir(__DIR__ . '/public/js') ? "OK" : "ERRO") . "\n\n";

echo "2. Verificando arquivos de view...\n";
$views = ['layout.php', 'dashboard.php', 'clientes_lista.php', 'cliente_form.php', 'orcamentos_lista.php'];
foreach ($views as $view) {
    $path = __DIR__ . '/app/views/' . $view;
    echo "   $view: " . (file_exists($path) ? "OK (" . filesize($path) . " bytes)" : "ERRO") . "\n";
}

echo "\n3. Testando simulação de acesso ao index.php...\n";
echo "   Simulando GET ?page=dashboard\n\n";

// Simular acesso
ob_start();
$_GET['page'] = 'dashboard';
$_SERVER['SCRIPT_NAME'] = '/SISTEMALAZER/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

try {
   include __DIR__ . '/index.php';
    $output = ob_get_clean();
    echo "✓ Index.php executado com sucesso!\n";
    echo "   Output: " . strlen($output) . " bytes\n";
    echo "   HTML válido: " . (strpos($output, '<!DOCTYPE html>') !== false ? "SIM" : "NÃO") . "\n";
} catch (Throwable $e) {
    ob_get_clean();
    echo "✗ ERRO ao executar index.php:\n";
    echo "   " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "   Arquivo: " . $e->getFile() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
}
?>
