<?php
// models/Budget.php
// Model de Orçamento
// Módulo: Vendas
// Etapa: Desenvolvimento de modelos

require_once(__DIR__ . '/../db/connection.php');

class Budget {
    public $id;
    public $simulation_id;
    public $client_id;
    public $user_id;
    public $total;
    public $status;
    public $created_at;

    /**
     * Construtor da classe Budget
     */
    public function __construct($id = null, $simulation_id = null, $client_id = null, $user_id = null, $total = 0, $status = 'aberto', $created_at = null) {
        $this->id = $id;
        $this->simulation_id = $simulation_id;
        $this->client_id = $client_id;
        $this->user_id = $user_id;
        $this->total = $total;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    /**
     * Busca orçamento por ID
     */
    public static function findById($id) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM budgets WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Budget($row['id'], $row['simulation_id'], $row['client_id'], $row['user_id'], $row['total'], $row['status'], $row['created_at']);
        }
        return null;
    }

    /**
     * Lista todos os orçamentos com filtro opcional
     */
    public static function listAll($status = null, $client_id = null) {
        $pdo = getDbConnection();
        $sql = 'SELECT * FROM budgets WHERE 1=1';
        $params = [];
        
        if ($status) {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }
        
        if ($client_id) {
            $sql .= ' AND client_id = ?';
            $params[] = $client_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new Budget($row['id'], $row['simulation_id'], $row['client_id'], $row['user_id'], $row['total'], $row['status'], $row['created_at']);
        }
        return $result;
    }

    /**
     * Salva novo orçamento
     */
    public function save() {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO budgets (simulation_id, client_id, user_id, total, status) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$this->simulation_id, $this->client_id, $this->user_id, $this->total, $this->status]);
    }

    /**
     * Atualiza status do orçamento
     */
    public function updateStatus($newStatus) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('UPDATE budgets SET status = ? WHERE id = ?');
        return $stmt->execute([$newStatus, $this->id]);
    }
}
