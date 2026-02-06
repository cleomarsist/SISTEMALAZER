<?php
// index.php
// Arquivo inicial do ERP Fênix Magazine Personalizados
// Módulo: Inicialização
// Etapa: Ponto de entrada do sistema

// Comentário: Verifica se banco de dados foi criado antes de tudo
require_once(__DIR__ . '/setup_check.php');

// Comentário: Redireciona para o login se não autenticado
require_once(__DIR__ . '/utils/Auth.php');

if (Auth::isAuthenticated()) {
    // Comentário: Se já está autenticado, redireciona para dashboard
    header('Location: views/dashboard.html');
    exit();
} else {
    // Comentário: Se não está autenticado, redireciona para login
    header('Location: views/login.html');
    exit();
}
