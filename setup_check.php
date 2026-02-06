<?php
// setup_check.php
// Verifica se o banco de dados existe e foi inicializado
// Se não, mostra instruções para criar

require_once(__DIR__ . '/config/config.php');

function databaseExists() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_THROW,
        ]);
        
        // Verifica se o banco existe
        $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
        $result = $stmt->fetch();
        
        return $result !== false;
    } catch (Exception $e) {
        return false;
    }
}

// Se banco não existe, redireciona para setup
if (!databaseExists()) {
    header('Location: setup.php');
    exit;
}
?>
