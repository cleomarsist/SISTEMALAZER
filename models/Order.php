<?php
// models/Order.php
// Model de Pedido
// Módulo: Vendas
// Etapa: Desenvolvimento de modelos

require_once(__DIR__ . '/../db/connection.php');

class Order {
    public $id;
    public $budget_id;
    public $client_id;
    public $user_id;
    public $total;
    public $status;
    public $created_at;

    /**
     * Construtor da classe Order
     */
    public function __construct($id = null, $budget_id = null, $client_id = null, $user_id = null, $total = 0, $status = 'aberto', $created_at = null) {
        $this->id = $id;
        $this->budget_id = $budget_id;
        $this->client_id = $client_id;
        $this->user_id = $user_id;
        $this->total = $total;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    /**
     * Busca pedido por ID
     */
    public static function findById($id) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Order($row['id'], $row['budget_id'], $row['client_id'], $row['user_id'], $row['total'], $row['status'], $row['created_at']);
        }
        return null;
    }

    /**
     * Lista todos os pedidos com filtro opcional
     */
    public static function listAll($status = null, $client_id = null) {
        $pdo = getDbConnection();
        $sql = 'SELECT * FROM orders WHERE 1=1';
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
            $result[] = new Order($row['id'], $row['budget_id'], $row['client_id'], $row['user_id'], $row['total'], $row['status'], $row['created_at']);
        }
        return $result;
    }

    /**
     * Salva novo pedido
     */
    public function save() {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO orders (budget_id, client_id, user_id, total, status) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$this->budget_id, $this->client_id, $this->user_id, $this->total, $this->status]);
    }

    /**
     * Atualiza status do pedido
     */
    public function updateStatus($newStatus) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        return $stmt->execute([$newStatus, $this->id]);
    }
}
