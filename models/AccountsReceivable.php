<?php
// models/AccountsReceivable.php
// Model de Contas a Receber
// Módulo: Financeiro
// Etapa: Desenvolvimento de modelos

require_once(__DIR__ . '/../db/connection.php');

class AccountsReceivable {
    public $id;
    public $order_id;
    public $client_id;
    public $due_date;
    public $value;
    public $status;
    public $created_at;

    /**
     * Construtor da classe AccountsReceivable
     */
    public function __construct($id = null, $order_id = null, $client_id = null, $due_date = null, $value = 0, $status = 'aberto', $created_at = null) {
        $this->id = $id;
        $this->order_id = $order_id;
        $this->client_id = $client_id;
        $this->due_date = $due_date;
        $this->value = $value;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    /**
     * Busca conta a receber por ID
     */
    public static function findById($id) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM accounts_receivable WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new AccountsReceivable($row['id'], $row['order_id'], $row['client_id'], $row['due_date'], $row['value'], $row['status'], $row['created_at']);
        }
        return null;
    }

    /**
     * Lista contas a receber com filtro opcional
     */
    public static function listAll($status = null, $client_id = null) {
        $pdo = getDbConnection();
        $sql = 'SELECT * FROM accounts_receivable WHERE 1=1';
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
            $result[] = new AccountsReceivable($row['id'], $row['order_id'], $row['client_id'], $row['due_date'], $row['value'], $row['status'], $row['created_at']);
        }
        return $result;
    }

    /**
     * Salva nova conta a receber
     */
    public function save() {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO accounts_receivable (order_id, client_id, due_date, value, status) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$this->order_id, $this->client_id, $this->due_date, $this->value, $this->status]);
    }

    /**
     * Atualiza status da conta a receber
     */
    public function updateStatus($newStatus) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('UPDATE accounts_receivable SET status = ? WHERE id = ?');
        return $stmt->execute([$newStatus, $this->id]);
    }
}
