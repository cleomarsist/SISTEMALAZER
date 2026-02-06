<?php
// session/session.php
// Controle básico de sessões
// Módulo: Sessão
// Etapa: Arquitetura base

/**
 * Inicia sessão de forma segura
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        // Comentário: Configurações extras de segurança podem ser adicionadas aqui
    }
}

/**
 * Encerra sessão
 */
function destroySession() {
    session_unset();
    session_destroy();
}
