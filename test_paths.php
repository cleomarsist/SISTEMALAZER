<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('VIEWS_PATH', APP_PATH . '/views');

echo 'TESTE DE CAMINHOS' . PHP_EOL;
echo '=================' . PHP_EOL;
echo 'BASE_PATH: ' . BASE_PATH . PHP_EOL;
echo 'APP_PATH: ' . APP_PATH . PHP_EOL;
echo 'VIEWS_PATH: ' . VIEWS_PATH . PHP_EOL;
echo PHP_EOL;

$page = 'dashboard';
$routes = [
    'dashboard' => VIEWS_PATH . '/dashboard.php',
    'clientes' => VIEWS_PATH . '/clientes_lista.php',
];

$view = $routes[$page];
echo 'View procurada: ' . $view . PHP_EOL;
echo 'Arquivo existe: ' . (file_exists($view) ? 'SIM' : 'NÃO') . PHP_EOL;
echo 'Arquivo readable: ' . (is_readable($view) ? 'SIM' : 'NÃO') . PHP_EOL;

echo PHP_EOL . 'Listando arquivos em ' . VIEWS_PATH . ':' . PHP_EOL;
$files = scandir(VIEWS_PATH);
foreach ($files as $f) {
    if (strpos($f, '.php') !== false) {
        echo '  - ' . $f . PHP_EOL;
    }
}

echo PHP_EOL . 'Testando incluir layout...' . PHP_EOL;
$layout_path = VIEWS_PATH . '/layout.php';
if (file_exists($layout_path)) {
    echo 'Layout pode ser incluído: SIM' . PHP_EOL;
} else {
    echo 'Layout NÃO encontrado!' . PHP_EOL;
}
?>
