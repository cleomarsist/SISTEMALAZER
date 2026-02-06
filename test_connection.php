<?php
// test_connection.php
// Arquivo de teste para diagnosticar problemas de conexão
// Use: http://localhost/SISTEMAIA/ControleInvestimento/test_connection.php

header('Content-Type: application/json; charset=utf-8');

// Teste 1: Verificar configuração
echo "<h2>📋 Configuração do Sistema</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Extensions Loaded:\n";
$extensions = ['pdo', 'pdo_mysql', 'mysqli'];
foreach ($extensions as $ext) {
    echo "  - " . $ext . ": " . (extension_loaded($ext) ? "✅ SIM" : "❌ NÃO") . "\n";
}
echo "</pre>";

// Teste 2: Incluir configuração
echo "<h2>🔧 Incluindo Arquivos de Configuração</h2>";
try {
    require_once(__DIR__ . '/config/config.php');
    echo "✅ config.php incluído com sucesso<br>";
    echo "   DB_HOST: " . DB_HOST . "<br>";
    echo "   DB_USER: " . DB_USER . "<br>";
    echo "   DB_NAME: " . DB_NAME . "<br>";
} catch (Exception $e) {
    echo "❌ Erro ao incluir config.php: " . $e->getMessage() . "<br>";
    exit;
}

// Teste 3: Incluir e executar conexão
echo "<h2>🚀 Testando Conexão com Banco de Dados</h2>";
try {
    require_once(__DIR__ . '/db/connection.php');
    $pdo = getDbConnection();
    echo "✅ Conexão com PDO estabelecida com sucesso!<br>";
    
    // Teste 4: Verificar se tabelas existem
    echo "<h2>📊 Tabelas no Banco de Dados</h2>";
    $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([DB_NAME]);
    $tables = $stmt->fetchAll();
    
    if (count($tables) > 0) {
        echo "✅ Total de tabelas: " . count($tables) . "<br>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table['TABLE_NAME'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "⚠️ Nenhuma tabela encontrada no banco de dados '" . DB_NAME . "'<br>";
        echo "Execute o arquivo setup_complete.sql em phpMyAdmin para criar as tabelas<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
    echo "Possíveis causas:<br>";
    echo "1. MySQL/MariaDB não está rodando<br>";
    echo "2. Credenciais incorretas (usuário: " . DB_USER . ", senha: [vazia])<br>";
    echo "3. Banco de dados '" . DB_NAME . "' não existe<br>";
    echo "4. Host '" . DB_HOST . "' está incorreto<br>";
} catch (Exception $e) {
    echo "❌ Erro inesperado: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<a href='index.php'>🏠 Voltar ao Sistema</a>";
?>
