<?php
/**
 * CLASSE BASE PARA TODOS OS MODELS
 * 
 * ETAPA 1: ARQUITETURA GERAL
 * Módulo: Models
 * 
 * Esta é a classe pai de todos os modelos de dados.
 * Fornece métodos comuns (CRUD) que todos os modelos utilizam.
 * 
 * Cada model específico (Cliente, Material, etc) estende BaseModel
 * e herda todos esses métodos, podendo sobrescrever conforme necessário.
 * 
 * Padrão MVC:
 * Models = Acesso e manipulação de dados (banco de dados)
 * Não contêm lógica de negócio complexa, apenas CRUD básico
 * Controllers chamam os models para obter dados
 */

class BaseModel {
    
    // ========================================================================
    // 1. PROPRIEDADES PROTEGIDAS
    // ========================================================================
    
    /**
     * Instância do banco de dados (PDO)
     * Acessível aos models filhos
     * 
     * @var Database
     */
    protected $db;
    
    /**
     * Nome da tabela no banco de dados
     * DEVE ser definido no model filho
     * 
     * Exemplo em ClienteModel:
     * protected $table = 'clientes';
     * 
     * @var string
     */
    protected $table;
    
    /**
     * Nome da coluna de ID (chave primária)
     * Padrão é 'id', mas pode ser diferente
     * 
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Campos que podem ser preenchidos em massa (fillable)
     * Array com nomes das colunas que podem ser atribuidas em massa
     * 
     * Exemplo:
     * protected $fillable = ['nome', 'email', 'telefone'];
     * 
     * Isso protege contra atribuição em massa de campos sensíveis
     * 
     * @var array
     */
    protected $fillable = [];
    
    /**
     * Campos que NÃO devem ser retornados (hidden)
     * Exemplo: senhas, tokens sensíveis
     * 
     * @var array
     */
    protected $hidden = [];
    
    /**
     * Filtros padrão para todas as queries
     * Exemplo: apenas registros não deletados
     * 
     * Pode ser sobrescrito em models filhos
     * 
     * @var array
     */
    protected $defaultFilters = [];
    
    // ========================================================================
    // 2. CONSTRUTOR
    // ========================================================================
    
    /**
     * Construtor do modelo
     * Inicializa conexão com banco de dados
     */
    public function __construct() {
        // Obtém instância única de conexão com banco
        $this->db = Database::getInstance();
        
        // Se nome da tabela não foi definido, lança erro
        if (empty($this->table)) {
            throw new Exception('Propriedade $table não foi definida em ' . get_class($this));
        }
    }
    
    // ========================================================================
    // 3. OPERAÇÕES BÁSICAS (CREATE - INSERT)
    // ========================================================================
    
    /**
     * Insere um novo registro na tabela
     * 
     * @param array $data Array associativo com dados a inserir
     *                    Exemplo: ['nome' => 'João', 'email' => 'joao@example.com']
     * 
     * @return int|false ID do registro inserido, ou false se erro
     * 
     * Validações:
     * - Verifica campos fillable
     * - Valida tipos de dados
     * - Trata caracteres especiais
     */
    public function create($data) {
        try {
            // Valida e filtra dados
            $data = $this->validateAndFilter($data);
            
            // Se nenhum dado válido, retorna false
            if (empty($data)) {
                throw new Exception('Nenhum dado válido para inserir');
            }
            
            // Monta nomes das colunas e placeholders
            $columns = array_keys($data);
            $placeholders = array_fill(0, count($columns), '?');
            
            // SQL: INSERT INTO tabela (col1, col2) VALUES (?, ?)
            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $this->table,
                implode(', ', $columns),
                implode(', ', $placeholders)
            );
            
            // Executa insert
            $this->db->execute($sql, array_values($data));
            
            // Retorna ID do último insert
            return (int)$this->db->getLastInsertId();
            
        } catch (Exception $e) {
            // Log do erro
            @file_put_contents(
                LOGS_PATH . '/model_errors.log',
                date('Y-m-d H:i:s') . " | " . get_class($this) . "->create() | " . $e->getMessage() . "\n",
                FILE_APPEND | LOCK_EX
            );
            return false;
        }
    }
    
    // ========================================================================
    // 4. OPERAÇÕES BÁSICAS (READ - SELECT)
    // ========================================================================
    
    /**
     * Obtém um registro pelo ID
     * 
     * @param int $id ID do registro a buscar
     * @return array|null Array do registro ou null se não encontrado
     * 
     * Exemplo:
     * $cliente = $model->find(123);
     */
    public function find($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $result = $this->db->selectOne($sql, [$id]);
            return $this->formatResult($result);
        } catch (Exception $e) {
            @file_put_contents(
                LOGS_PATH . '/model_errors.log',
                date('Y-m-d H:i:s') . " | " . get_class($this) . "->find() | " . $e->getMessage() . "\n",
                FILE_APPEND | LOCK_EX
            );
            return null;
        }
    }
    
    /**
     * Obtém todos os registros (com filtros opcionais)
     * 
     * @param array $filters Filtros WHERE (array associativo)
     *                       Exemplo: ['status' => 'ativo', 'tipo' => 'cliente']
     * @param array $options Opções de query (limit, offset, orderBy)
     *                       Exemplo: ['limit' => 10, 'offset' => 0, 'orderBy' => 'nome ASC']
     * 
     * @return array Array de registros
     * 
     * Exemplo:
     * $clientes = $model->all(['status' => 'ativo'], ['limit' => 10]);
     */
    public function all($filters = [], $options = []) {
        try {
            // Monta a query SELECT básica
            $sql = "SELECT * FROM {$this->table}";
            $params = [];
            
            // Adiciona filtros WHERE
            if (!empty($filters)) {
                $whereClauses = [];
                foreach ($filters as $column => $value) {
                    $whereClauses[] = "$column = ?";
                    $params[] = $value;
                }
                $sql .= " WHERE " . implode(" AND ", $whereClauses);
            }
            
            // Adiciona ORDER BY
            if (!empty($options['orderBy'])) {
                $sql .= " ORDER BY {$options['orderBy']}";
            }
            
            // Adiciona LIMIT
            if (isset($options['limit'])) {
                $sql .= " LIMIT " . (int)$options['limit'];
                
                // Adiciona OFFSET se houver
                if (isset($options['offset'])) {
                    $sql .= " OFFSET " . (int)$options['offset'];
                }
            }
            
            // Executa query
            $results = $this->db->select($sql, $params);
            
            // Formata e retorna resultados
            return array_map([$this, 'formatResult'], $results);
            
        } catch (Exception $e) {
            @file_put_contents(
                LOGS_PATH . '/model_errors.log',
                date('Y-m-d H:i:s') . " | " . get_class($this) . "->all() | " . $e->getMessage() . "\n",
                FILE_APPEND | LOCK_EX
            );
            return [];
        }
    }
    
    /**
     * Obtém primeiro registro que atende aos critérios
     * 
     * @param array $filters Filtros WHERE
     * @return array|null Primeiro registro encontrado ou null
     * 
     * Exemplo:
     * $cliente = $model->first(['email' => 'joao@example.com']);
     */
    public function first($filters = []) {
        try {
            $sql = "SELECT * FROM {$this->table}";
            $params = [];
            
            // Adiciona WHERE se há filtros
            if (!empty($filters)) {
                $whereClauses = [];
                foreach ($filters as $column => $value) {
                    $whereClauses[] = "$column = ?";
                    $params[] = $value;
                }
                $sql .= " WHERE " . implode(" AND ", $whereClauses);
            }
            
            $sql .= " LIMIT 1";
            
            $result = $this->db->selectOne($sql, $params);
            return $this->formatResult($result);
            
        } catch (Exception $e) {
            @file_put_contents(
                LOGS_PATH . '/model_errors.log',
                date('Y-m-d H:i:s') . " | " . get_class($this) . "->first() | " . $e->getMessage() . "\n",
                FILE_APPEND | LOCK_EX
            );
            return null;
        }
    }
    
    /**
     * Conta registros (opcionalmente com filtros)
     * 
     * @param array $filters Filtros WHERE opcionais
     * @return int Número de registros
     */
    public function count($filters = []) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $params = [];
            
            // Adiciona WHERE se há filtros
            if (!empty($filters)) {
                $whereClauses = [];
                foreach ($filters as $column => $value) {
                    $whereClauses[] = "$column = ?";
                    $params[] = $value;
                }
                $sql .= " WHERE " . implode(" AND ", $whereClauses);
            }
            
            $result = $this->db->selectOne($sql, $params);
            return (int)($result['total'] ?? 0);
            
        } catch (Exception $e) {
            return 0;
        }
    }
    
    // ========================================================================
    // 5. OPERAÇÕES BÁSICAS (UPDATE)
    // ========================================================================
    
    /**
     * Atualiza um registro existente
     * 
     * @param int $id ID do registro a atualizar
     * @param array $data Array com dados a atualizar
     * 
     * @return bool true se sucesso, false se erro
     * 
     * Exemplo:
     * $model->update(123, ['nome' => 'Novo Nome', 'email' => 'novo@example.com']);
     */
    public function update($id, $data) {
        try {
            // Valida e filtra dados
            $data = $this->validateAndFilter($data);
            
            if (empty($data)) {
                throw new Exception('Nenhum dado válido para atualizar');
            }
            
            // Monta SET clauses
            $setClauses = [];
            foreach (array_keys($data) as $column) {
                $setClauses[] = "$column = ?";
            }
            
            // SQL: UPDATE tabela SET col1 = ?, col2 = ? WHERE id = ?
            $sql = sprintf(
                "UPDATE %s SET %s WHERE %s = ?",
                $this->table,
                implode(', ', $setClauses),
                $this->primaryKey
            );
            
            // Parâmetros: valores + ID
            $params = array_merge(array_values($data), [$id]);
            
            // Executa update
            $affected = $this->db->execute($sql, $params);
            
            return $affected > 0;
            
        } catch (Exception $e) {
            @file_put_contents(
                LOGS_PATH . '/model_errors.log',
                date('Y-m-d H:i:s') . " | " . get_class($this) . "->update() | " . $e->getMessage() . "\n",
                FILE_APPEND | LOCK_EX
            );
            return false;
        }
    }
    
    // ========================================================================
    // 6. OPERAÇÕES BÁSICAS (DELETE)
    // ========================================================================
    
    /**
     * Deleta um registro (soft delete)
     * Marca como deletado em vez de remover definitivamente
     * 
     * @param int $id ID do registro a deletar
     * @return bool true se sucesso
     * 
     * Usa coluna 'deletado_em' para soft delete
     * Assim, dados podem ser recuperados se necessário
     */
    public function delete($id) {
        try {
            // Soft delete: marca timestamp de deleção
            $sql = "UPDATE {$this->table} SET deletado_em = NOW() WHERE {$this->primaryKey} = ?";
            $affected = $this->db->execute($sql, [$id]);
            return $affected > 0;
            
        } catch (Exception $e) {
            @file_put_contents(
                LOGS_PATH . '/model_errors.log',
                date('Y-m-d H:i:s') . " | " . get_class($this) . "->delete() | " . $e->getMessage() . "\n",
                FILE_APPEND | LOCK_EX
            );
            return false;
        }
    }
    
    // ========================================================================
    // 7. MÉTODOS DE VALIDAÇÃO E FORMATAÇÃO
    // ========================================================================
    
    /**
     * Valida e filtra dados para inserção/atualização
     * - Remove campos não permitidos (segurança)
     * - Valida tipos de dados
     * - Saneaciona valores
     * 
     * @param array $data Dados a validar
     * @return array Dados validados e filtrados
     */
    protected function validateAndFilter($data) {
        $filtered = [];
        
        // Se há campo fillable definido, usa como whitelist
        if (!empty($this->fillable)) {
            foreach ($this->fillable as $field) {
                if (isset($data[$field])) {
                    $filtered[$field] = $data[$field];
                }
            }
        } else {
            // Se não há fillable, usa todos os dados (menos ID)
            foreach ($data as $key => $value) {
                if ($key !== $this->primaryKey) {
                    $filtered[$key] = $value;
                }
            }
        }
        
        // Saneação básica: remove espaços extras
        foreach ($filtered as $key => $value) {
            if (is_string($value)) {
                $filtered[$key] = trim($value);
            }
        }
        
        return $filtered;
    }
    
    /**
     * Formata resultado da query
     * - Remove campos hidden
     * - Formata datas
     * - Converte tipos conforme necessário
     * 
     * @param array|null $result Resultado da query
     * @return array|null Resultado formatado
     */
    protected function formatResult($result) {
        // Se resultado é null, retorna null
        if ($result === null) {
            return null;
        }
        
        // Remove campos hidden
        foreach ($this->hidden as $field) {
            unset($result[$field]);
        }
        
        // Formata datas (se houver)
        foreach ($result as $key => $value) {
            if (strpos($key, '_em') !== false && !empty($value)) {
                // Se campo termina em "_em" (criado_em, atualizado_em), formata como data
                $result[$key] = date('d/m/Y H:i:s', strtotime($value));
            }
        }
        
        return $result;
    }
    
    // ========================================================================
    // 8. QUERY CUSTOMIZADA
    // ========================================================================
    
    /**
     * Executa uma query customizada
     * Para quando as operações básicas não são suficientes
     * 
     * CUIDADO: Sempre use placeholders para proteger contra SQL Injection
     * 
     * @param string $sql Query SQL com placeholders (?)
     * @param array $params Parâmetros para a query
     * 
     * @return array Resultados da query
     * 
     * Exemplo:
     * $resultado = $model->query(
     *     "SELECT * FROM clientes WHERE idade > ? AND status = ?",
     *     [18, 'ativo']
     * );
     */
    public function query($sql, $params = []) {
        try {
            return $this->db->select($sql, $params);
        } catch (Exception $e) {
            @file_put_contents(
                LOGS_PATH . '/model_errors.log',
                date('Y-m-d H:i:s') . " | " . get_class($this) . "->query() | " . $e->getMessage() . "\n",
                FILE_APPEND | LOCK_EX
            );
            return [];
        }
    }
}
?>
