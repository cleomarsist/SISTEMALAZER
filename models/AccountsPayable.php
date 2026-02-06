<?php
// models/AccountsPayable.php
// Model de Contas a Pagar
// Módulo: Financeiro
// Etapa: Desenvolvimento de modelos

require_once(__DIR__ . '/../db/connection.php');

class AccountsPayable {
    public $id;
    public $supplier_id;
    public $due_date;
    public $value;
    public $status;
    public $created_at;

    /**
     * Construtor da classe AccountsPayable
     */
    public function __construct($id = null, $supplier_id = null, $due_date = null, $value = 0, $status = 'aberto', $created_at = null) {
        $this->id = $id;
        $this->supplier_id = $supplier_id;
        $this->due_date = $due_date;
        $this->value = $value;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    /**
     * Busca conta a pagar por ID
     */
    public static function findById($id) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM accounts_payable WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new AccountsPayable($row['id'], $row['supplier_id'], $row['due_date'], $row['value'], $row['status'], $row['created_at']);
        }
        return null;
    }

    /**
     * Lista contas a pagar com filtro opcional
     */
    public static function listAll($status = null, $supplier_id = null) {
        $pdo = getDbConnection();
        $sql = 'SELECT * FROM accounts_payable WHERE 1=1';
        $params = [];
        
        if ($status) {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }
        
        if ($supplier_id) {
            $sql .= ' AND supplier_id = ?';
            $params[] = $supplier_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new AccountsPayable($row['id'], $row['supplier_id'], $row['due_date'], $row['value'], $row['status'], $row['created_at']);
        }
        return $result;
    }

    /**
     * Salva nova conta a pagar
     */
    public function save() {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO accounts_payable (supplier_id, due_date, value, status) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$this->supplier_id, $this->due_date, $this->value, $this->status]);
    }

    /**
     * Atualiza status da conta a pagar
     */
    public function updateStatus($newStatus) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('UPDATE accounts_payable SET status = ? WHERE id = ?');
        return $stmt->execute([$newStatus, $this->id]);
    }
}
