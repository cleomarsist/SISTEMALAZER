<?php
// models/User.php
// Model de Usuário para login e permissões
// Módulo: Usuários
// Etapa: Desenvolvimento ampliado

require_once(__DIR__ . '/../db/connection.php');

class User {
    public $id;
    public $username;
    public $password;
    public $role;
    public $created_at;

    /**
     * Construtor da classe User
     */
    public function __construct($id = null, $username = '', $password = '', $role = 'usuario', $created_at = null) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->created_at = $created_at;
    }

    /**
     * Busca usuário por nome de usuário
     * Retorna objeto User ou null
     */
    public static function findByUsername($username) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        if ($row) {
            return new User($row['id'], $row['username'], $row['password'], $row['role'], $row['created_at']);
        }
        return null;
    }

    /**
     * Busca usuário por ID
     */
    public static function findById($id) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new User($row['id'], $row['username'], $row['password'], $row['role'], $row['created_at']);
        }
        return null;
    }

    /**
     * Lista todos os usuários
     */
    public static function listAll() {
        $pdo = getDbConnection();
        $stmt = $pdo->query('SELECT * FROM users');
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new User($row['id'], $row['username'], $row['password'], $row['role'], $row['created_at']);
        }
        return $result;
    }

    /**
     * Salva novo usuário com password hash seguro
     */
    public function save() {
        $pdo = getDbConnection();
        // Comentário: password_hash usa bcrypt para maior segurança
        $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        return $stmt->execute([$this->username, $hashedPassword, $this->role]);
    }

    /**
     * Verifica se a senha está correta
     */
    public function verifyPassword($plainPassword) {
        return password_verify($plainPassword, $this->password);
    }
}
