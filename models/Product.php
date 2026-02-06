<?php
// models/Product.php
// Model de Produto Simples
// Módulo: Produtos
// Etapa: Modelagem inicial

require_once(__DIR__ . '/../db/connection.php');

class Product {
    public $id;
    public $name;
    public $description;
    public $unit;
    public $price;
    public $created_at;

    /**
     * Construtor da classe Product
     */
    public function __construct($id = null, $name = '', $description = '', $unit = '', $price = 0, $created_at = null) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->unit = $unit;
        $this->price = $price;
        $this->created_at = $created_at;
    }

    /**
     * Busca produto por ID
     */
    public static function findById($id) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Product($row['id'], $row['name'], $row['description'], $row['unit'], $row['price'], $row['created_at']);
        }
        return null;
    }

    /**
     * Lista todos os produtos
     */
    public static function listAll() {
        $pdo = getDbConnection();
        $stmt = $pdo->query('SELECT * FROM products');
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new Product($row['id'], $row['name'], $row['description'], $row['unit'], $row['price'], $row['created_at']);
        }
        return $result;
    }

    /**
     * Salva novo produto
     */
    public function save() {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO products (name, description, unit, price) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$this->name, $this->description, $this->unit, $this->price]);
    }
}
