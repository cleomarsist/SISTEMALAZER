<?php
/**
 * Teste de Loading do Index
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simular acesso web
$_GET['page'] = 'dashboard';
$_SERVER['SCRIPT_NAME'] = '/SISTEMALAZER/index.php';
$_SERVER['SCRIPT_FILENAME'] = 'C:\wamp64\www\SISTEMALAZER\index.php';

echo '<pre>';
echo 'SIMULANDO ACESSO WEB' . PHP_EOL;
echo '=====================' . PHP_EOL;
echo 'SCRIPT_NAME: ' . $_SERVER['SCRIPT_NAME'] . PHP_EOL;
echo 'dirname(SCRIPT_NAME): ' . dirname($_SERVER['SCRIPT_NAME']) . PHP_EOL;
echo 'GET page: ' . $_GET['page'] . PHP_EOL;
echo '</pre>';

// Agora executar o índex
echo '<hr>';
echo '<h2>Tentando carregar index.php...</h2>';

// Redirecionar output para capturar
ob_start();
try {
    include 'index.php';
    $output = ob_get_clean();
    echo 'Sucesso! Index.php carregado.';
    echo '<pre>Primeiras 500 caracteres do output:</pre>';
    echo '<pre>' . htmlspecialchars(substr($output, 0, 500)) . '</pre>';
} catch (Exception $e) {
    $output = ob_get_clean();
    echo 'Erro: ' . $e->getMessage();
    echo '<pre>' . htmlspecialchars($output) . '</pre>';
}
?>
