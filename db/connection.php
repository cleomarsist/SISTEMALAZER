<?php
// db/connection.php
// Responsável pela conexão com o banco de dados MySQL via PDO
// Módulo: Conexão com Banco
// Etapa: Inicialização do projeto

require_once(__DIR__ . '/../config/config.php');

/**
 * Função para conectar ao banco de dados MySQL usando PDO
 * Retorna um objeto PDO ou encerra o script em caso de erro
 * PDO oferece melhor segurança (prepared statements) e portabilidade
 */
function getDbConnection() {
    try {
        // Criando string de conexão DSN (Data Source Name)
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        
        // Opções de segurança e desempenho para PDO
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_THROW,           // Lança exceções ao erro
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Retorna arrays associativos
            PDO::ATTR_EMULATE_PREPARES => false,               // Usa prepared statements nativos
        ];
        
        // Criando conexão com o banco
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $pdo;
        
    } catch (PDOException $e) {
        // Em produção, logar o erro ao invés de exibir
        // Log::error('Erro na conexão com banco de dados: ' . $e->getMessage());
        die('Erro na conexão com o banco de dados: ' . $e->getMessage());
    }
}
