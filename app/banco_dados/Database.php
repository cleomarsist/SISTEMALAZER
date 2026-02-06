<?php
/**
 * CLASSE DE CONEXÃO COM BANCO DE DADOS (PDO)
 * 
 * ETAPA 1: ARQUITETURA GERAL
 * Módulo: Database
 * 
 * Responsabilidades:
 * - Estabelecer conexão segura com MySQL via PDO
 * - Gerenciar instância única de conexão (Singleton)
 * - Tratamento de erros e exceções
 * - Executar queries preparadas (proteção contra SQL Injection)
 * - Transações do banco de dados
 * 
 * SEGURANÇA:
 * - Usa prepared statements com placeholders
 * - Nunca concatena SQL diretamente
 * - Trata exceções de forma segura
 */

class Database {
    
    // ========================================================================
    // 1. PROPRIEDADES PRIVADAS
    // ========================================================================
    
    /**
     * Instância única da conexão (Singleton Pattern)
     * Assegura que apenas uma conexão seja mantida com o banco
     * 
     * @var PDO|null
     */
    private static $instance = null;
    
    /**
     * Objeto PDO de conexão com o banco de dados
     * 
     * @var PDO
     */
    private $connection;
    
    /**
     * Última query executada (para debug)
     * 
     * @var string
     */
    private $lastQuery = '';
    
    /**
     * Último erro do banco de dados
     * 
     * @var string
     */
    private $lastError = '';
    
    // ========================================================================
    // 2. CONSTRUTOR PRIVADO (Singleton)
    // ========================================================================
    
    /**
     * Construtor privado para impedir instanciação múltipla
     * Estabelece conexão com o banco de dados
     * 
     * Procedimento:
     * 1. Verifica se config.php foi incluída
     * 2. Monta DSN (Data Source Name)
     * 3. Tenta conectar ao banco via PDO
     * 4. Se erro: lança exceção com mensagem clara
     * 5. Define charset para UTF-8
     */
    private function __construct() {
        try {
            // Verificam se as constantes de banco foram definidas (config.php incluído)
            if (!defined('DB_HOST') || !defined('DB_NAME')) {
                throw new Exception('Arquivo de configuração não carregado!');
            }
            
            // Monta a string de conexão (DSN) para o MySQL
            // Formato: mysql:host=localhost; dbname=banco; charset=utf8mb4
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );
            
            // Estabelece conexão PDO com as opções de segurança
            $this->connection = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                DB_OPTIONS
            );
            
            // Define o charset para UTF-8 (suporta acentos e emojis)
            $this->connection->exec("SET CHARACTER SET utf8mb4");
            
            // Log de sucesso (desenvolvimento)
            $this->log('Conexão ao banco de dados estabelecida com sucesso', 'success');
            
        } catch (PDOException $e) {
            // Se falhar a conexão, registra erro no log
            $this->log('Erro ao conectar ao banco: ' . $e->getMessage(), 'error');
            
            // Se em desenvolvimento, mostra o erro (não fazer em produção!)
            if (IS_development) {
                die('Erro de Conexão: ' . $e->getMessage());
            } else {
                die('Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.');
            }
        }
    }
    
    // ========================================================================
    // 3. MÉTODO SINGLETON - getInstance()
    // ========================================================================
    
    /**
     * Retorna instância única da conexão com banco de dados
     * 
     * Se ainda não há instância, cria uma nova
     * Se já existe, retorna a mesma (reutiliza conexão)
     * 
     * @return Database Instância única de conexão
     */
    public static function getInstance() {
        // Se não existe instância ainda, criar uma
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        // Retornar a instância existente
        return self::$instance;
    }
    
    // ========================================================================
    // 4. MÉTODOS DE QUERY - EXECUTE (INSERT, UPDATE, DELETE)
    // ========================================================================
    
    /**
     * Executa uma query de modificação (INSERT, UPDATE, DELETE)
     * 
     * SEGURANÇA:
     * - Usa prepared statements com placeholders (?)
     * - Parâmetros são separados da SQL, evitando SQL Injection
     * 
     * @param string $sql Query com placeholders (?) para parâmetros
     * @param array $params Array com parâmetros para substituir os ?
     * 
     * @return int Número de linhas afetadas pela query
     * 
     * Exemplo:
     * $db->execute("INSERT INTO clientes (nome, email) VALUES (?, ?)", 
     *              ["João Silva", "joao@example.com"]);
     */
    public function execute($sql, $params = []) {
        try {
            // Guarda a query para debug
            $this->lastQuery = $sql;
            
            // Prepara a statement (valida sintaxe SQL)
            $stmt = $this->connection->prepare($sql);
            
            // Se houver parâmetros, bind deles na statement
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    // O $key começa de 1 (não 0) nos placeholders
                    $stmt->bindValue($key + 1, $value);
                }
            }
            
            // Executa a statement preparada
            $stmt->execute();
            
            // Retorna quantas linhas foram afetadas
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            // Guarda erro para consulta posterior
            $this->lastError = $e->getMessage();
            
            // Log do erro
            $this->log("Erro na execute: " . $e->getMessage() . " | SQL: " . $sql, 'error');
            
            // Re-lança exceção para tratamento na controller
            throw new Exception($e->getMessage());
        }
    }
    
    // ========================================================================
    // 5. MÉTODOS DE QUERY - SELECT (CONSULTA)
    // ========================================================================
    
    /**
     * Consulta múltiplos registros
     * Retorna um array com todos os registros encontrados
     * 
     * @param string $sql Query com placeholders
     * @param array $params Parâmetros para a query
     * 
     * @return array Array de registros (assoc) ou vazio se nenhum encontrado
     * 
     * Exemplo:
     * $clientes = $db->select("SELECT * FROM clientes WHERE tipo = ?", ["cliente"]);
     */
    public function select($sql, $params = []) {
        try {
            // Guarda query para debug
            $this->lastQuery = $sql;
            
            // Prepara a statement
            $stmt = $this->connection->prepare($sql);
            
            // Bind dos parâmetros
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key + 1, $value);
                }
            }
            
            // Executa
            $stmt->execute();
            
            // Retorna todos os resultados como array associativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->log("Erro na select: " . $e->getMessage() . " | SQL: " . $sql, 'error');
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Consulta um único registro
     * Retorna um array com um registro ou null se não encontrado
     * 
     * @param string $sql Query com placeholders
     * @param array $params Parâmetros para a query
     * 
     * @return array|null Array do registro ou null
     * 
     * Exemplo:
     * $cliente = $db->selectOne("SELECT * FROM clientes WHERE id = ?", [1]);
     */
    public function selectOne($sql, $params = []) {
        try {
            // Guarda query para debug
            $this->lastQuery = $sql;
            
            // Prepara a statement
            $stmt = $this->connection->prepare($sql);
            
            // Bind dos parâmetros
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key + 1, $value);
                }
            }
            
            // Executa
            $stmt->execute();
            
            // Retorna apenas um resultado (ou null se não houver)
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->log("Erro na selectOne: " . $e->getMessage() . " | SQL: " . $sql, 'error');
            throw new Exception($e->getMessage());
        }
    }
    
    // ========================================================================
    // 6. TRANSAÇÕES DO BANCO DE DADOS
    // ========================================================================
    
    /**
     * Inicia uma transação no banco de dados
     * Usada quando precisa executar múltiplas queries que devem ser
     * todas confirmadas ou todas revertidas juntas
     * 
     * Exemplo:
     * $db->beginTransaction();
     * try {
     *     $db->execute("INSERT INTO pedidos ...", [...]);
     *     $db->execute("UPDATE clientes ...", [...]);
     *     $db->commit();
     * } catch (Exception $e) {
     *     $db->rollback();
     * }
     */
    public function beginTransaction() {
        try {
            $this->connection->beginTransaction();
            $this->log('Transação iniciada', 'info');
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->log("Erro ao iniciar transação: " . $e->getMessage(), 'error');
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Confirma (commit) uma transação
     * Todas as mudanças são salvas permanentemente no banco
     */
    public function commit() {
        try {
            $this->connection->commit();
            $this->log('Transação confirmada', 'info');
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->log("Erro ao confirmar transação: " . $e->getMessage(), 'error');
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Desfaz (rollback) uma transação
     * Todas as mudanças desde beginTransaction() são descartadas
     */
    public function rollback() {
        try {
            $this->connection->rollback();
            $this->log('Transação revertida', 'info');
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->log("Erro ao reverter transação: " . $e->getMessage(), 'error');
            throw new Exception($e->getMessage());
        }
    }
    
    // ========================================================================
    // 7. MÉTODOS AUXILIARES
    // ========================================================================
    
    /**
     * Retorna a última query executada (util para debug)
     * 
     * @return string Última query executada
     */
    public function getLastQuery() {
        return $this->lastQuery;
    }
    
    /**
     * Retorna o último erro do banco
     * 
     * @return string Descrição do último erro
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Retorna o ID da última linha inserida
     * Usado após INSERT para pegar o ID gerado automaticamente
     * 
     * @return string ID da última inserção
     */
    public function getLastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Registra eventos no log do sistema
     * Usado internamente para rastreabilidade
     * 
     * @param string $message Mensagem do log
     * @param string $level Nível: success, info, warning, error
     */
    private function log($message, $level = 'info') {
        // Se não está em desenvolvimento, não registra tudo
        if (!IS_development && $level === 'info') {
            return;
        }
        
        // Monta mensagem com timestamp e nível
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message\n";
        
        // Log para arquivo de database
        @file_put_contents(
            LOGS_PATH . '/database.log',
            $logMessage,
            FILE_APPEND | LOCK_EX
        );
    }
    
    /**
     * Função para impedir clonagem da instância Singleton
     */
    private function __clone() {}
    
    /**
     * Função para impedir desserialização da instância Singleton
     */
    private function __wakeup() {}
}
?>
