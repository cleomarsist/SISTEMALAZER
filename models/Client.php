<?php
// models/Client.php
// Model de Cliente/Fornecedor
// Módulo: Clientes/Fornecedores
// Etapa: Modelagem inicial

require_once(__DIR__ . '/../db/connection.php');

class Client {
    public $id;
    public $name;
    public $document;
    public $email;
    public $phone;
    public $address;
    public $type;
    public $created_at;

    /**
     * Construtor da classe Client
     */
    public function __construct($id = null, $name = '', $document = '', $email = '', $phone = '', $address = '', $type = 'cliente', $created_at = null) {
        $this->id = $id;
        $this->name = $name;
        $this->document = $document;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->type = $type;
        $this->created_at = $created_at;
    }

    /**
     * Busca cliente/fornecedor por ID
     */
    public static function findById($id) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM clients WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Client($row['id'], $row['name'], $row['document'], $row['email'], $row['phone'], $row['address'], $row['type'], $row['created_at']);
        }
        return null;
    }

    /**
     * Lista todos os clientes/fornecedores
     */
    public static function listAll($type = null) {
        $pdo = getDbConnection();
        $sql = 'SELECT * FROM clients';
        if ($type) {
            $sql .= ' WHERE type = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$type]);
        } else {
            $stmt = $pdo->query($sql);
        }
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new Client($row['id'], $row['name'], $row['document'], $row['email'], $row['phone'], $row['address'], $row['type'], $row['created_at']);
        }
        return $result;
    }

    /**
     * Salva novo cliente/fornecedor
     */
    public function save() {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO clients (name, document, email, phone, address, type) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$this->name, $this->document, $this->email, $this->phone, $this->address, $this->type]);
    }
}
