<?php
// models/Simulation.php
// Model de Simulação
// Módulo: Vendas
// Etapa: Desenvolvimento de modelos

require_once(__DIR__ . '/../db/connection.php');

class Simulation {
    public $id;
    public $client_id;
    public $user_id;
    public $description;
    public $total;
    public $created_at;

    /**
     * Construtor da classe Simulation
     */
    public function __construct($id = null, $client_id = null, $user_id = null, $description = '', $total = 0, $created_at = null) {
        $this->id = $id;
        $this->client_id = $client_id;
        $this->user_id = $user_id;
        $this->description = $description;
        $this->total = $total;
        $this->created_at = $created_at;
    }

    /**
     * Busca simulação por ID
     */
    public static function findById($id) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM simulations WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Simulation($row['id'], $row['client_id'], $row['user_id'], $row['description'], $row['total'], $row['created_at']);
        }
        return null;
    }

    /**
     * Lista todas as simulações
     */
    public static function listAll($client_id = null) {
        $pdo = getDbConnection();
        $sql = 'SELECT * FROM simulations WHERE 1=1';
        $params = [];
        
        if ($client_id) {
            $sql .= ' AND client_id = ?';
            $params[] = $client_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new Simulation($row['id'], $row['client_id'], $row['user_id'], $row['description'], $row['total'], $row['created_at']);
        }
        return $result;
    }

    /**
     * Salva nova simulação
     */
    public function save() {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO simulations (client_id, user_id, description, total) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$this->client_id, $this->user_id, $this->description, $this->total]);
    }
}
