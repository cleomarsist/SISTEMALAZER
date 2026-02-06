<?php
/**
 * VERIFICADOR DE STATUS DO SISTEMA
 * 
 * Este arquivo verifica rapidamente se tudo está funcionando
 * Acesse: http://localhost/SISTEMAIA/ControleInvestimento/status.php
 */

// Sem restrições de headers para facilitar debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status do Sistema - ERP Fênix</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
            text-align: center;
        }
        .status-group {
            margin-bottom: 25px;
        }
        .status-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 10px;
            background: #f5f5f5;
            border-left: 4px solid #ddd;
        }
        .status-item.success {
            background: #e8f5e9;
            border-left-color: #4caf50;
        }
        .status-item.error {
            background: #ffebee;
            border-left-color: #f44336;
        }
        .status-item.warning {
            background: #fff3e0;
            border-left-color: #ff9800;
        }
        .icon {
            font-size: 20px;
            margin-right: 12px;
            width: 24px;
            text-align: center;
        }
        .label {
            flex: 1;
            font-weight: 500;
            color: #333;
        }
        .value {
            color: #666;
            font-size: 12px;
            text-align: right;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }
        a, button {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }
        a.btn-primary, button.btn-primary {
            background: #667eea;
            color: white;
        }
        a.btn-primary:hover, button.btn-primary:hover {
            background: #5568d3;
        }
        a.btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        a.btn-secondary:hover {
            background: #e0e0e0;
        }
        .summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
        .summary p {
            margin: 5px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏥 Status do Sistema</h1>
        
        <?php
        // ==================== TESTES ====================
        
        $tests = [
            'php' => [
                'name' => 'PHP',
                'check' => function() { return phpversion(); }
            ],
            'pdo' => [
                'name' => 'Extensão PDO',
                'check' => function() { return extension_loaded('pdo'); }
            ],
            'pdo_mysql' => [
                'name' => 'PDO MySQL',
                'check' => function() { return extension_loaded('pdo_mysql'); }
            ],
            'config' => [
                'name' => 'Arquivo config.php',
                'check' => function() { return file_exists(__DIR__ . '/config/config.php'); }
            ],
            'connection_file' => [
                'name' => 'Arquivo connection.php',
                'check' => function() { return file_exists(__DIR__ . '/db/connection.php'); }
            ],
        ];
        
        $results = [];
        
        // Testes rápidos (sem conexão)
        echo '<div class="status-group"><h3>⚙️ AMBIENTE PHP</h3>';
        foreach ($tests as $key => $test) {
            $result = $test['check']();
            $status = $result ? 'success' : 'error';
            $icon = $result ? '✅' : '❌';
            $value = $result && is_string($result) ? $result : ($result ? 'OK' : 'ERRO');
            
            $results[$key] = $result;
            
            echo '<div class="status-item ' . $status . '">';
            echo '<span class="icon">' . $icon . '</span>';
            echo '<span class="label">' . $test['name'] . '</span>';
            echo '<span class="value">' . htmlspecialchars($value) . '</span>';
            echo '</div>';
        }
        echo '</div>';
        
        // Teste de conexão
        echo '<div class="status-group"><h3>🗄️ BANCO DE DADOS</h3>';
        
        $db_status = 'error';
        $db_message = 'Não testado';
        $db_tables = 0;
        
        if ($results['config'] && $results['connection_file']) {
            try {
                require_once(__DIR__ . '/db/connection.php');
                $pdo = getDbConnection();
                
                $db_status = 'success';
                $db_message = 'Conectado';
                
                // Contar tabelas
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $db_tables = $result['count'];
                
            } catch (Exception $e) {
                $db_status = 'error';
                $db_message = substr($e->getMessage(), 0, 50) . '...';
            }
        }
        
        $icon = ($db_status === 'success') ? '✅' : '❌';
        
        echo '<div class="status-item ' . $db_status . '">';
        echo '<span class="icon">' . $icon . '</span>';
        echo '<span class="label">Conexão MySQL</span>';
        echo '<span class="value">' . htmlspecialchars($db_message) . '</span>';
        echo '</div>';
        
        if ($db_status === 'success') {
            $table_status = $db_tables >= 16 ? 'success' : ($db_tables > 0 ? 'warning' : 'error');
            $table_icon = $db_tables >= 16 ? '✅' : ($db_tables > 0 ? '⚠️' : '❌');
            
            echo '<div class="status-item ' . $table_status . '">';
            echo '<span class="icon">' . $table_icon . '</span>';
            echo '<span class="label">Tabelas do Banco</span>';
            echo '<span class="value">' . $db_tables . '/16</span>';
            echo '</div>';
        }
        echo '</div>';
        
        // Verificação geral
        $all_ok = $results['php'] && $results['pdo'] && $results['pdo_mysql'] && 
                  $results['config'] && $results['connection_file'] && 
                  $db_status === 'success' && $db_tables === 16;
        
        // Summary
        if ($all_ok) {
            echo '<div class="summary">';
            echo '<p>✅ SISTEMA PRONTO PARA USO</p>';
            echo '<p>Todos os componentes estão funcionando corretamente</p>';
            echo '</div>';
        } else {
            echo '<div class="summary" style="background: linear-gradient(135deg, #f44336 0%, #e91e63 100%);">';
            echo '<p>❌ SISTEMA COM PROBLEMAS</p>';
            echo '<p>Alguns componentes precisam ser configurados</p>';
            echo '</div>';
        }
        
        ?>
        
        <div class="button-group">
            <a href="index.php" class="btn-primary">🏠 Ir para Sistema</a>
            <a href="DIAGNOSTICO.md" class="btn-secondary">📖 Diagnostico</a>
            <a href="INSTRUCOES.md" class="btn-secondary">📚 Instruções</a>
        </div>
    </div>
</body>
</html>
