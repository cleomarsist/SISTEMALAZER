<?php
// utils/Auth.php
// Utilitários de autenticação e autorização
// Módulo: Segurança
// Etapa: Desenvolvimento de camada de segurança

require_once(__DIR__ . '/../session/session.php');

class Auth {
    /**
     * Verifica se o usuário está autenticado
     */
    public static function isAuthenticated() {
        startSession();
        return isset($_SESSION['user_id']) && isset($_SESSION['username']);
    }

    /**
     * Retorna o ID do usuário autenticado
     */
    public static function getUserId() {
        startSession();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Retorna o nome do usuário autenticado
     */
    public static function getUsername() {
        startSession();
        return $_SESSION['username'] ?? null;
    }

    /**
     * Retorna o role/perfil do usuário autenticado
     */
    public static function getRole() {
        startSession();
        return $_SESSION['role'] ?? null;
    }

    /**
     * Verifica se o usuário tem acesso a um role específico
     */
    public static function hasRole($requiredRole) {
        $userRole = self::getRole();
        return $userRole === $requiredRole || $userRole === 'admin';
    }

    /**
     * Redireciona para login se não autenticado
     */
    public static function requireLogin() {
        if (!self::isAuthenticated()) {
            header('Location: ../views/login.html');
            exit();
        }
    }

    /**
     * Redireciona se não tem a permissão necessária
     */
    public static function requireRole($requiredRole) {
        self::requireLogin();
        if (!self::hasRole($requiredRole)) {
            header('HTTP/1.1 403 Forbidden');
            die('Acesso negado. Você não tem permissão para acessar este recurso.');
        }
    }
}
