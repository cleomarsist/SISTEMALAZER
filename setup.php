<?php
/**
 * PÁGINA DE SETUP DO BANCO DE DADOS
 * 
 * Esta página aparece quando o banco de dados não existe
 * Guia o usuário através dos passos para criar o banco
 */

require_once(__DIR__ . '/config/config.php');

// Tenta conectar sem especificar banco para verificar credenciais
$connection_ok = false;
$error_message = '';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_THROW,
    ]);
    $connection_ok = true;
} catch (PDOException $e) {
    $error_message = $e->getMessage();
}

// Lê o conteúdo do arquivo SQL
$sql_file = __DIR__ . '/db/setup_complete.sql';
$sql_content = file_exists($sql_file) ? file_get_contents($sql_file) : '';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - ERP Fênix Magazine</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px;
        }
        .step {
            margin-bottom: 30px;
            padding: 20px;
            border-left: 4px solid #667eea;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .step h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 20px;
        }
        .step p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .step-number {
            display: inline-block;
            background: #667eea;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            margin-right: 10px;
            font-weight: bold;
        }
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.4;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-success {
            background: #27ae60;
            color: white;
        }
        .btn-success:hover {
            background: #1e8449;
        }
        .status {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .status.error {
            background: #ffebee;
            border-left: 4px solid #f44336;
            color: #c62828;
        }
        .status.success {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            color: #1b5e20;
        }
        .status.warning {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            color: #e65100;
        }
        .icon {
            font-size: 20px;
            margin-right: 10px;
        }
        .loader {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .success-message {
            background: #e8f5e9;
            border: 2px solid #4caf50;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
            color: #1b5e20;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Setup do Sistema</h1>
            <p>ERP Fênix Magazine Personalizados</p>
        </div>
        
        <div class="content">
            <h2 style="margin-bottom: 20px;">🔧 Configuração Inicial</h2>
            
            <?php if (!$connection_ok): ?>
                <div class="status error">
                    <span class="icon">❌</span>
                    <strong>Erro de Conexão:</strong> Não consegui conectar ao MySQL
                    <br><small><?php echo htmlspecialchars($error_message); ?></small>
                </div>
                
                <div class="step">
                    <h2><span class="step-number">1</span>Verificar MySQL</h2>
                    <p>O MySQL não está respondendo. Certifique-se de que:</p>
                    <ul style="margin-left: 20px; color: #666;">
                        <li>WAMP está <strong>iniciado</strong> (ícone verde no canto inferior direito)</li>
                        <li>MySQL está <strong>rodando</strong></li>
                        <li>As credenciais em config.php estão corretas:
                            <div class="code-block">DB_HOST: <?php echo DB_HOST; ?><br>DB_USER: <?php echo DB_USER; ?><br>DB_PASS: <?php echo DB_PASS ? '***' : '(vazio)'; ?></div>
                        </li>
                    </ul>
                    <div class="btn-group">
                        <button class="btn btn-primary" onclick="location.reload()">🔄 Tentar Novamente</button>
                    </div>
                </div>
            <?php else: ?>
                <div class="status success">
                    <span class="icon">✅</span>
                    MySQL está funcionando corretamente
                </div>
                
                <div class="step">
                    <h2><span class="step-number">1</span>Criar Banco de Dados</h2>
                    <p>Vamos criar o banco de dados e as tabelas necessárias. Escolha uma opção:</p>
                    
                    <div class="btn-group">
                        <button class="btn btn-success" onclick="setupDatabase()">
                            ⚡ Executar Setup (Rápido)
                        </button>
                        <a href="../phpmyadmin" class="btn btn-primary" target="_blank">
                            🖥️ Abrir phpMyAdmin (Manual)
                        </a>
                    </div>
                </div>
                
                <div id="setupProgress" class="loader">
                    <div class="spinner"></div>
                    <p style="margin-top: 20px;">Criando banco de dados...</p>
                </div>
                
                <div id="setupResult"></div>
            <?php endif; ?>
            
            <div class="step" style="margin-top: 30px;">
                <h2><span class="step-number">2</span>Próximos Passos</h2>
                <p>Após criar o banco de dados:</p>
                <ol style="margin-left: 20px; color: #666; line-height: 2;">
                    <li>Acesse: <strong>http://localhost/SISTEMAIA/ControleInvestimento/</strong></li>
                    <li>Login com:
                        <div class="code-block" style="margin-top: 5px;">
                            Usuário: <strong>admin</strong><br>
                            Senha: <strong>Senha123</strong>
                        </div>
                    </li>
                    <li>Comece a usar o sistema!</li>
                </ol>
            </div>
            
            <div class="step" style="background: #f0f0f0; border-left-color: #999;">
                <h2>💡 Informações</h2>
                <p>
                    📁 <strong>Banco:</strong> <?php echo DB_NAME; ?><br>
                    🖥️ <strong>Host:</strong> <?php echo DB_HOST; ?><br>
                    👤 <strong>Usuário:</strong> <?php echo DB_USER; ?><br>
                    📊 <strong>Tabelas:</strong> 16 tabelas com dados iniciais
                </p>
            </div>
        </div>
    </div>
    
    <script>
        function setupDatabase() {
            const progressDiv = document.getElementById('setupProgress');
            const resultDiv = document.getElementById('setupResult');
            
            progressDiv.style.display = 'block';
            resultDiv.innerHTML = '';
            
            fetch('api/setup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                progressDiv.style.display = 'none';
                
                if (data.status === 'success') {
                    resultDiv.innerHTML = `
                        <div class="success-message">
                            <strong>✅ Setup Realizado com Sucesso!</strong>
                            <p style="margin-top: 10px;">Banco de dados criado com todas as tabelas e dados iniciais.</p>
                            <p style="margin-top: 15px;">
                                <a href="http://localhost/SISTEMAIA/ControleInvestimento/" style="color: #1b5e20; text-decoration: underline;">
                                    ➜ Ir para o Login
                                </a>
                            </p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="status error">
                            <strong>❌ Erro ao criar banco:</strong><br>
                            ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                progressDiv.style.display = 'none';
                resultDiv.innerHTML = `
                    <div class="status error">
                        <strong>❌ Erro:</strong><br>
                        ${error.message}
                    </div>
                `;
            });
        }
    </script>
</body>
</html>
