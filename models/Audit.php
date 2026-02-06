<?php
// models/Audit.php
// Model de Auditoria para registrar alterações
// Módulo: Auditoria
// Etapa: Desenvolvimento de modelo de auditoria

require_once(__DIR__ . '/../db/connection.php');

class Audit {
    public $id;
    public $user_id;
    public $action;
    public $table_name;
    public $record_id;
    public $description;
    public $created_at;

    /**
     * Construtor da classe Audit
     */
    public function __construct($id = null, $user_id = null, $action = '', $table_name = '', $record_id = null, $description = '', $created_at = null) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->action = $action;
        $this->table_name = $table_name;
        $this->record_id = $record_id;
        $this->description = $description;
        $this->created_at = $created_at;
    }

    /**
     * Registra uma ação na auditoria
     */
    public static function log($user_id, $action, $table_name, $record_id, $description = '') {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO audit_history (user_id, action, table_name, record_id, description) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$user_id, $action, $table_name, $record_id, $description]);
    }

    /**
     * Lista histórico de auditoria
     */
    public static function listAll($limit = 100) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM audit_history ORDER BY created_at DESC LIMIT ?');
        $stmt->execute([$limit]);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new Audit($row['id'], $row['user_id'], $row['action'], $row['table_name'], $row['record_id'], $row['description'], $row['created_at']);
        }
        return $result;
    }

    /**
     * Busca histórico de um usuário
     */
    public static function findByUser($user_id, $limit = 50) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM audit_history WHERE user_id = ? ORDER BY created_at DESC LIMIT ?');
        $stmt->execute([$user_id, $limit]);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new Audit($row['id'], $row['user_id'], $row['action'], $row['table_name'], $row['record_id'], $row['description'], $row['created_at']);
        }
        return $result;
    }
}
