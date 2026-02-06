<?php
// controllers/UserController.php
// Controller de Usuário para login e permissões
// Módulo: Usuários
// Etapa: Autenticação e controle ampliado

require_once(__DIR__ . '/../models/User.php');
require_once(__DIR__ . '/../session/session.php');

class UserController {
    /**
     * Realiza login do usuário
     * Retorna true se login bem-sucedido, false caso contrário
     */
    public function login($username, $password) {
        // Comentário: Validação básica de entrada
        if (empty($username) || empty($password)) {
            return false;
        }
        
        $user = User::findByUsername($username);
        if ($user && $user->verifyPassword($password)) {
            // Comentário: Login bem-sucedido, inicia sessão
            startSession();
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['role'] = $user->role;
            return true;
        }
        // Comentário: Falha no login
        return false;
    }

    /**
     * Logout do usuário
     */
    public function logout() {
        startSession();
        destroySession();
        return true;
    }

    /**
     * Registra novo usuário
     */
    public function register($username, $password, $role = 'usuario') {
        // Comentário: Validações básicas
        if (empty($username) || empty($password) || strlen($password) < 6) {
            return false;
        }
        
        // Comentário: Verifica se usuário já existe
        if (User::findByUsername($username)) {
            return false;
        }
        
        $user = new User(null, $username, $password, $role);
        return $user->save();
    }

    /**
     * Lista todos os usuários
     */
    public function listUsers() {
        return User::listAll();
    }
}
