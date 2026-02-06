<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teste de Acesso ao Sistema</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .ok {
            background: #dff0d8;
            color: #3c763d;
            border-left: 4px solid #5cb85c;
        }
        .error {
            background: #f2dede;
            color: #a94442;
            border-left: 4px solid #d9534f;
        }
        .warning {
            background: #fcf8e3;
            color: #8a6d3b;
            border-left: 4px solid #f0ad4e;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            max-height: 300px;
        }
        a {
            display: inline-block;
            margin: 5px 5px 5px 0;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        a:hover {
            background: #764ba2;
        }
        .links {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico do Sistema Lazer</h1>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 0);

        define('BASE_PATH', __DIR__);
        define('VIEWS_PATH', BASE_PATH . '/app/views');

        $issues = [];
        $warnings = [];

        // Teste 1: Verificar arquivo index.php
        echo '<h2>1. Verificação de Arquivos</h2>';
        
        $files_to_check = [
            'index.php' => BASE_PATH . '/index.php',
            'layout.php' => VIEWS_PATH . '/layout.php',
            'dashboard.php' => VIEWS_PATH . '/dashboard.php',
            'clientes_lista.php' => VIEWS_PATH . '/clientes_lista.php',
            'cliente_form.php' => VIEWS_PATH . '/cliente_form.php',
            'orcamentos_lista.php' => VIEWS_PATH . '/orcamentos_lista.php',
            'style.css' => BASE_PATH . '/public/css/style.css',
            'main.js' => BASE_PATH . '/public/js/main.js',
        ];

        foreach ($files_to_check as $name => $path) {
            if (file_exists($path)) {
                echo '<div class="status ok">✓ ' . $name . ' encontrado</div>';
            } else {
                echo '<div class="status error">✗ ' . $name . ' NÃO ENCONTRADO</div>';
                $issues[] = "Arquivo não encontrado: $path";
            }
        }

        // Teste 2: Verificar permissões
        echo '<h2>2. Verificação de Permissões</h2>';
        
        $readable = true;
        foreach ($files_to_check as $name => $path) {
            if (file_exists($path) && !is_readable($path)) {
                echo '<div class="status warning">⚠ ' . $name . ' não é legível</div>';
                $warnings[] = "Arquivo não é legível: $path";
                $readable = false;
            }
        }

        if ($readable) {
            echo '<div class="status ok">✓ Todos os arquivos são legíveis</div>';
        }

        // Teste 3: Verificar sintaxe PHP
        echo '<h2>3. Verificação de Sintaxe PHP</h2>';
        
        $php_files = [
            'index.php',
            'layout.php',
            'dashboard.php',
            'clientes_lista.php',
            'cliente_form.php',
            'orcamentos_lista.php',
        ];

        $syntax_ok = true;
        foreach ($php_files as $file) {
            $path = in_array($file, ['index.php']) ? 
                    BASE_PATH . '/' . $file : 
                    VIEWS_PATH . '/' . $file;
            
            $output = shell_exec("php -l \"$path\" 2>&1");
            if (strpos($output, 'No syntax errors') !== false) {
                echo '<div class="status ok">✓ ' . $file . ' - Sintaxe OK</div>';
            } else {
                echo '<div class="status error">✗ ' . $file . ' - ERRO DE SINTAXE</div>';
                echo '<pre>' . htmlspecialchars($output) . '</pre>';
                $issues[] = "Erro de sintaxe em: $file";
                $syntax_ok = false;
            }
        }

        // Teste 4: Teste de HTTP
        echo '<h2>4. Teste de Acesso HTTP</h2>';
        
        $test_urls = [
            'http://localhost/SISTEMALAZER/index.php?page=dashboard',
            'http://localhost/SISTEMALAZER/index.php?page=clientes',
        ];

        foreach ($test_urls as $url) {
            $context = stream_context_create(['http' => ['timeout' => 5]]);
            $response = @file_get_contents($url, false, $context);
            
            if ($response !== false) {
                $size = strlen($response);
                echo '<div class="status ok">✓ Acesso a ' . str_replace('http://localhost', '', $url) . ' ('. $size . ' bytes)</div>';
            } else {
                echo '<div class="status error">✗ Erro ao acessar ' . str_replace('http://localhost', '', $url) . '</div>';
                $issues[] = "Erro ao acessar: $url";
            }
        }

        // Resumo
        echo '<h2>📊 Resumo</h2>';
        
        if (empty($issues)) {
            echo '<div class="status ok"><strong>✓ Sistema OK!</strong> Todos os testes passaram.</div>';
        } else {
            echo '<div class="status error"><strong>✗ Problemas encontrados:</strong><ul>';
            foreach ($issues as $issue) {
                echo '<li>' . htmlspecialchars($issue) . '</li>';
            }
            echo '</ul></div>';
        }

        if (!empty($warnings)) {
            echo '<div class="status warning"><strong>⚠ Avisos:</strong><ul>';
            foreach ($warnings as $warning) {
                echo '<li>' . htmlspecialchars($warning) . '</li>';
            }
            echo '</ul></div>';
        }

        // Links para acessar o sistema
        echo '<div class="links">';
        echo '<h2>🚀 Acessar Sistema</h2>';
        echo '<a href="http://localhost/SISTEMALAZER/index.php?page=dashboard">Dashboard</a>';
        echo '<a href="http://localhost/SISTEMALAZER/index.php?page=clientes">Clientes</a>';
        echo '<a href="http://localhost/SISTEMALAZER/index.php?page=cliente-novo">Novo Cliente</a>';
        echo '<a href="http://localhost/SISTEMALAZER/index.php?page=orcamentos">Orçamentos</a>';
        echo '</div>';
        ?>
    </div>
</body>
</html>
