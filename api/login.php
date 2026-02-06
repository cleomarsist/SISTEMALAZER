<?php
// api/login.php
// Endpoint de autenticação via API REST
// Módulo: Usuários
// Etapa: Autenticação via API

header('Content-Type: application/json');

require_once(__DIR__ . '/../controllers/UserController.php');
require_once(__DIR__ . '/../utils/Response.php');
require_once(__DIR__ . '/../utils/Validator.php');

// Comentário: Apenas POST é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Método não permitido', 405);
}

// Comentário: Obtém dados JSON da requisição
$input = json_decode(file_get_contents('php://input'), true);

// Comentário: Validação de entrada
if (empty($input['username']) || empty($input['password'])) {
    Response::error('Usuário e senha são obrigatórios', 400);
}

// Comentário: Sanitiza entrada
$username = Validator::sanitizeText($input['username']);
$password = $input['password'];

// Comentário: Tenta autenticar o usuário
$controller = new UserController();
if ($controller->login($username, $password)) {
    Response::success([
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role']
    ], 'Login realizado com sucesso', 200);
} else {
    Response::error('Usuário ou senha inválidos', 401);
}
