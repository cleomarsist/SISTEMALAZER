<?php
/**
 * Diagnóstico de Roteamento
 * Verifica se o erro 404 é causado pelo .htaccess ou se é outro problema
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Roteamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #f5f5f5; 
            padding: 20px;
            font-family: 'Courier New', monospace;
        }
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 10px 0;
            font-size: 13px;
            line-height: 1.5;
        }
        .test-item {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .success {
            border-left-color: #28a745;
            color: #28a745;
        }
        .error {
            border-left-color: #dc3545;
            color: #dc3545;
        }
        .warning {
            border-left-color: #ffc107;
            color: #ffc107;
        }
        h2 {
            color: #667eea;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .info-card strong {
            color: #667eea;
            display: block;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">🔍 Diagnóstico de Roteamento - SISTEMALAZER</h1>
    
    <div class="alert alert-info">
        <strong>Erro 404?</strong> Use este diagnóstico para identificar o problema e como corrigi-lo.
    </div>

    <!-- Informações do Servidor -->
    <h2>1️⃣ Informações do Servidor</h2>
    <div class="info-grid">
        <div class="info-card">
            <strong>Servidor Web:</strong>
            <?php echo $_SERVER['SERVER_SOFTWARE']; ?>
        </div>
        <div class="info-card">
            <strong>PHP Versão:</strong>
            <?php echo phpversion(); ?>
        </div>
        <div class="info-card">
            <strong>Diretório Raiz:</strong>
            <?php echo $_SERVER['DOCUMENT_ROOT']; ?>
        </div>
        <div class="info-card">
            <strong>Script Atual:</strong>
            <?php echo $_SERVER['SCRIPT_FILENAME']; ?>
        </div>
    </div>

    <!-- Teste de .htaccess -->
    <h2>2️⃣ Status do .htaccess</h2>
    <?php
    $htaccess_file = __DIR__ . '/.htaccess';
    if (file_exists($htaccess_file)) {
        echo '<div class="test-item success">';
        echo '<strong>✅ .htaccess Encontrado</strong>';
        echo '<p style="margin: 10px 0 0 0;">Arquivo localizado em: ' . htmlspecialchars($htaccess_file) . '</p>';
        echo '</div>';
        
        echo '<div class="test-item">';
        echo '<strong>Conteúdo do .htaccess:</strong>';
        $content = file_get_contents($htaccess_file);
        echo '<div class="code-block">' . htmlspecialchars($content) . '</div>';
        echo '</div>';
    } else {
        echo '<div class="test-item error">';
        echo '<strong>❌ .htaccess NÃO Encontrado</strong>';
        echo '<p>O arquivo .htaccess não existe. Isso pode causar problemas no roteamento.</p>';
        echo '</div>';
    }
    ?>

    <!-- Teste de mod_rewrite -->
    <h2>3️⃣ Módulos do Apache Carregados</h2>
    <?php
    if (extension_loaded('apcu')) {
        echo '<div class="test-item success">';
        echo '<strong>✅ Função apache_get_modules() Disponível</strong>';
        
        // Tenta obter a lista de módulos
        if (function_exists('apache_get_modules')) {
            $modules = apache_get_modules();
            $has_rewrite = in_array('mod_rewrite', $modules);
            
            if ($has_rewrite) {
                echo '<p style="color: #28a745;">✅ mod_rewrite está ATIVADO</p>';
            } else {
                echo '<p style="color: #dc3545;">⚠️ mod_rewrite pode NÃO estar ativado</p>';
            }
            
            echo '<p>Módulos carregados:</p>';
            echo '<div class="code-block">';
            foreach ($modules as $module) {
                echo htmlspecialchars($module) . "\n";
            }
            echo '</div>';
        } else {
            echo '<p>Não foi possível verificar módulos do Apache.</p>';
        }
        echo '</div>';
    } else {
        echo '<div class="test-item warning">';
        echo '<strong>⚠️ Algumas funções de diagnóstico não estão disponíveis</strong>';
        echo '<p>Isso é normal em ambientes de compartilhamento. Veja instruções abaixo.</p>';
        echo '</div>';
    }
    ?>

    <!-- Teste de Arquivos -->
    <h2>4️⃣ Arquivos Críticos</h2>
    <?php
    $files = [
        'index.php' => 'Roteador principal',
        'api.php' => 'API Gateway',
        'app/views/dashboard.php' => 'View: Dashboard',
        'app/views/clientes_lista.php' => 'View: Lista de Clientes',
        '.htaccess' => 'Roteamento moderno',
    ];
    
    foreach ($files as $file => $description) {
        $path = __DIR__ . '/' . $file;
        $exists = file_exists($path);
        $class = $exists ? 'success' : 'error';
        $icon = $exists ? '✅' : '❌';
        
        echo '<div class="test-item ' . $class . '">';
        echo '<strong>' . htmlspecialchars($icon . ' ' . $file) . '</strong>';
        echo '<br><small style="color: #666;">' . htmlspecialchars($description) . '</small>';
        if ($exists) {
            echo '<br><small>Tamanho: ' . number_format(filesize($path), 0, ',', '.') . ' bytes</small>';
        }
        echo '</div>';
    }
    ?>

    <!-- Guia de Troubleshooting -->
    <h2>5️⃣ Solução de Problemas 404</h2>
    
    <div class="test-item">
        <h5>🔴 Se você recebeu erro 404:</h5>
        
        <p><strong>Verificar qual URL deu erro:</strong></p>
        <div class="code-block" style="color: #ffc107;">
URL que funcionam:
✅ http://localhost/SISTEMALAZER/
✅ http://localhost/SISTEMALAZER/index.php
✅ http://localhost/SISTEMALAZER/index.php?page=clientes
✅ http://localhost/SISTEMALAZER/test_api.php
✅ http://localhost/SISTEMALAZER/api.php?rota=clientes

URLs que NÃO funcionam (ainda):
❌ http://localhost/SISTEMALAZER/api/clientes (sem extensão .php)
❌ http://localhost/SISTEMALAZER/clientes (sem page=)
        </div>
        
        <p style="margin-top: 20px;"><strong>Solução 1: Use o formulário corretamente</strong></p>
        <p>Substitua <code>http://localhost/SISTEMALAZER/api/clientes</code></p>
        <p>Por: <code>http://localhost/SISTEMALAZER/api.php?rota=clientes</code></p>
        
        <p style="margin-top: 20px;"><strong>Solução 2: Verifique o .htaccess (mod_rewrite)</strong></p>
        <p>Se o erro 404 é persistente, o Apache pode não ter mod_rewrite ativado:</p>
        <div class="code-block">
1. Abra c:\wamp64\bin\apache\apache2.4.x\conf\httpd.conf
2. Procure por: #LoadModule rewrite_module modules/mod_rewrite.so
3. Remova o # da frente da linha
4. Restart Apache (WAMP → Restart All Services)
5. Teste novamente
        </div>
        
        <p style="margin-top: 20px;"><strong>Solução 3: Verifique permissões do .htaccess</strong></p>
        <p>O arquivo .htaccess não pode estar em apenas leitura.</p>
    </div>

    <!-- Teste de Roteamento -->
    <h2>6️⃣ Teste Automático de Roteamento</h2>
    <button class="btn btn-primary btn-sm" onclick="testarRotas()">
        🚀 Testar Todas as Rotas
    </button>
    <div id="resultado-rotamento" style="margin-top: 15px;"></div>

    <!-- Links de Acesso Rápido -->
    <h2>7️⃣ Links para Testar o Sistema</h2>
    <div class="row">
        <div class="col-md-6">
            <a href="/" class="btn btn-outline-primary btn-sm mb-2 w-100">🏠 Raiz (/)</a>
            <a href="index.php" class="btn btn-outline-primary btn-sm mb-2 w-100">📄 index.php</a>
            <a href="index.php?page=dashboard" class="btn btn-outline-primary btn-sm mb-2 w-100">📊 Dashboard</a>
            <a href="index.php?page=clientes" class="btn btn-outline-primary btn-sm mb-2 w-100">👥 Clientes</a>
        </div>
        <div class="col-md-6">
            <a href="test_api.php" class="btn btn-outline-success btn-sm mb-2 w-100">🧪 Teste de API</a>
            <a href="test_paths.php" class="btn btn-outline-success btn-sm mb-2 w-100">🔍 Teste de URLs</a>
            <a href="diagnostico.php" class="btn btn-outline-success btn-sm mb-2 w-100">🔧 Diagnóstico Completo</a>
            <button class="btn btn-outline-info btn-sm mb-2 w-100" onclick="testarRotas()">🚀 Teste de Rotas</button>
        </div>
    </div>
</div>

<script>
async function testarRotas() {
    const div = document.getElementById('resultado-rotamento');
    div.innerHTML = '<p class="text-muted">Testando rotas...</p>';
    
    const rotas = [
        { url: 'api.php?rota=clientes', desc: 'API: Listar Clientes' },
        { url: 'api.php?rota=orcamentos', desc: 'API: Listar Orçamentos' },
        { url: 'api.php?rota=viacep&cep=01310100', desc: 'API: Buscar CEP' },
        { url: 'index.php?page=clientes', desc: 'View: Clientes' },
        { url: 'index.php?page=dashboard', desc: 'View: Dashboard' },
    ];
    
    let html = '<div style="background: white; padding: 15px; border-radius: 8px;">';
    
    for (const rota of rotas) {
        try {
            const fullUrl = window.location.origin + window.location.pathname.replace('roteamento_diagnostico.php', '') + rota.url;
            const response = await fetch(rota.url);
            const status = response.status === 200 ? '✅' : '❌';
            html += `<p>${status} ${rota.desc} (${response.status})</p>`;
        } catch (err) {
            html += `<p>❌ ${rota.desc} (Erro: ${err.message})</p>`;
        }
    }
    
    html += '</div>';
    div.innerHTML = html;
}
</script>

</body>
</html>
