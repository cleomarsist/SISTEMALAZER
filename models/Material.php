<?php
// models/Material.php
// Model de Material (chapa/insumo)
// Módulo: Materiais
// Etapa: Modelagem inicial

require_once(__DIR__ . '/../db/connection.php');

class Material {
    public $id;
    public $name;
    public $type;
    public $unit;
    public $stock;
    public $min_stock;
    public $cost;
    public $created_at;

    /**
     * Construtor da classe Material
     */
    public function __construct($id = null, $name = '', $type = 'chapa', $unit = '', $stock = 0, $min_stock = 0, $cost = 0, $created_at = null) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->unit = $unit;
        $this->stock = $stock;
        $this->min_stock = $min_stock;
        $this->cost = $cost;
        $this->created_at = $created_at;
    }

    /**
     * Busca material por ID
     */
    public static function findById($id) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM materials WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Material($row['id'], $row['name'], $row['type'], $row['unit'], $row['stock'], $row['min_stock'], $row['cost'], $row['created_at']);
        }
        return null;
    }

    /**
     * Lista todos os materiais
     */
    public static function listAll($type = null) {
        $pdo = getDbConnection();
        $sql = 'SELECT * FROM materials';
        if ($type) {
            $sql .= ' WHERE type = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$type]);
        } else {
            $stmt = $pdo->query($sql);
        }
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new Material($row['id'], $row['name'], $row['type'], $row['unit'], $row['stock'], $row['min_stock'], $row['cost'], $row['created_at']);
        }
        return $result;
    }

    /**
     * Salva novo material
     */
    public function save() {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO materials (name, type, unit, stock, min_stock, cost) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$this->name, $this->type, $this->unit, $this->stock, $this->min_stock, $this->cost]);
    }
}
