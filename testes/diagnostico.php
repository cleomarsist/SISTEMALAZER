<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste do Sistema</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .ok { color: green; }
        .error { color: red; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Teste de Configuração do Sistema Lazer</h1>
    <hr>

    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    define('BASE_PATH', __DIR__);
    define('APP_PATH', BASE_PATH . '/app');
    define('VIEWS_PATH', APP_PATH . '/views');

    $checks = [];

    // Verificar diretórios
    $checks['BASE_PATH'] = [
        'test' => is_dir(BASE_PATH),
        'path' => BASE_PATH
    ];

    $checks['app/views/'] = [
        'test' => is_dir(VIEWS_PATH),
        'path' => VIEWS_PATH
    ];

    $checks['public/css/'] = [
        'test' => is_dir(BASE_PATH . '/public/css'),
        'path' => BASE_PATH . '/public/css'
    ];

    $checks['public/js/'] = [
        'test' => is_dir(BASE_PATH . '/public/js'),
        'path' => BASE_PATH . '/public/js'
    ];

    // Verificar arquivos críticos
    $files = [
        'layout.php' => VIEWS_PATH . '/layout.php',
        'dashboard.php' => VIEWS_PATH . '/dashboard.php',
        'clientes_lista.php' => VIEWS_PATH . '/clientes_lista.php',
        'cliente_form.php' => VIEWS_PATH . '/cliente_form.php',
        'orcamentos_lista.php' => VIEWS_PATH . '/orcamentos_lista.php',
        'style.css' => BASE_PATH . '/public/css/style.css',
        'main.js' => BASE_PATH . '/public/js/main.js',
        'cliente_form.js' => BASE_PATH . '/public/js/cliente_form.js',
    ];

    foreach ($files as $name => $path) {
        $checks[$name] = [
            'test' => file_exists($path),
            'path' => $path
        ];
    }

    // Exibir resultados
    foreach ($checks as $name => $check) {
        $status = $check['test'] ? '<span class="ok">✓ OK</span>' : '<span class="error">✗ ERRO</span>';
        echo "<p>{$status} {$name}</p>";
        if (!$check['test']) {
            echo "<p style='margin-left: 20px; color: gray; font-size: 0.9em;'>Procurando: {$check['path']}</p>";
        }
    }

    echo '<hr>';
    echo '<h2>Testes de Roteamento</h2>';

    // Testar roteamento
    $routes = [
        'dashboard' => VIEWS_PATH . '/dashboard.php',
        'clientes' => VIEWS_PATH . '/clientes_lista.php',
        'cliente-novo' => VIEWS_PATH . '/cliente_form.php',
        'orcamentos' => VIEWS_PATH . '/orcamentos_lista.php',
    ];

    foreach ($routes as $page => $file) {
        $exists = file_exists($file);
        $status = $exists ? '<span class="ok">✓ OK</span>' : '<span class="error">✗ ERRO</span>';
        echo "<p>{$status} /{$page}</p>";
    }

    echo '<hr>';
    echo '<h2>Teste de Acesso</h2>';
    echo '<p><a href="/SISTEMALAZER/index.php?page=dashboard">Abrir Dashboard</a></p>';
    echo '<p><a href="/SISTEMALAZER/index.php?page=clientes">Abrir Lista de Clientes</a></p>';
    echo '<p><a href="/SISTEMALAZER/index.php?page=cliente-novo">Novo Cliente</a></p>';

    ?>
</body>
</html>
