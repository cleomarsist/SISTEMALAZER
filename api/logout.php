<?php
// api/logout.php
// Endpoint de logout via API REST
// Módulo: Usuários
// Etapa: Autenticação via API

header('Content-Type: application/json');

require_once(__DIR__ . '/../controllers/UserController.php');
require_once(__DIR__ . '/../utils/Response.php');
require_once(__DIR__ . '/../utils/Auth.php');

// Comentário: Verifica se o usuário está autenticado
if (!Auth::isAuthenticated()) {
    Response::error('Não autenticado', 401);
}

// Comentário: Realiza logout
$controller = new UserController();
if ($controller->logout()) {
    Response::success(null, 'Logout realizado com sucesso', 200);
} else {
    Response::error('Erro ao fazer logout', 500);
}
