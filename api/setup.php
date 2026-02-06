<?php
/**
 * api/setup.php
 * Endpoint para executar o setup do banco de dados
 * Lê setup_complete.sql e executa todos os comandos
 */

header('Content-Type: application/json');

require_once(__DIR__ . '/../config/config.php');

// POST só
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Método não permitido'
    ]);
    exit;
}

try {
    // Conecta ao MySQL sem especificar banco
    $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_THROW,
    ]);
    
    // Lê o arquivo SQL
    $sql_file = __DIR__ . '/../db/setup_complete.sql';
    if (!file_exists($sql_file)) {
        throw new Exception('Arquivo setup_complete.sql não encontrado');
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Executa cada comando SQL
    $commands = explode(';', $sql_content);
    $executed = 0;
    
    foreach ($commands as $command) {
        $command = trim($command);
        
        // Ignora linhas vazias e comentários
        if (empty($command) || substr(trim($command), 0, 2) === '--') {
            continue;
        }
        
        try {
            $pdo->exec($command);
            $executed++;
        } catch (PDOException $e) {
            // Se o erro é apenas sobre banco existente, continua
            if (strpos($e->getMessage(), 'already exists') === false && 
                strpos($e->getMessage(), 'Duplicate') === false) {
                throw $e;
            }
        }
    }
    
    // Verifica se banco foi criado
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $table_count = $result['count'];
    
    if ($table_count >= 16) {
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Banco de dados criado com sucesso',
            'tables' => $table_count,
            'commands' => $executed
        ]);
    } else {
        throw new Exception("Banco foi criado mas nem todas as tabelas estão presentes. Encontradas: $table_count/16");
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao criar banco: ' . $e->getMessage(),
        'code' => $e->getCode()
    ]);
}

exit;
?>
