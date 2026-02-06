<?php
// utils/Response.php
// Utilitários para padronização de respostas da API
// Módulo: Resposta
// Etapa: Desenvolvimento de camada de resposta

class Response {
    /**
     * Envia resposta JSON de sucesso
     */
    public static function success($data = null, $message = 'Operação realizada com sucesso', $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }

    /**
     * Envia resposta JSON de erro
     */
    public static function error($message = 'Erro ao processar requisição', $statusCode = 400, $errors = null) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ]);
        exit();
    }

    /**
     * Envia resposta JSON de validação
     */
    public static function validationError($errors) {
        self::error('Erro de validação', 422, $errors);
    }

    /**
     * Envia resposta de não autorizado
     */
    public static function unauthorized($message = 'Não autorizado') {
        self::error($message, 401);
    }

    /**
     * Envia resposta de acesso proibido
     */
    public static function forbidden($message = 'Acesso negado') {
        self::error($message, 403);
    }

    /**
     * Envia resposta de recurso não encontrado
     */
    public static function notFound($message = 'Recurso não encontrado') {
        self::error($message, 404);
    }
}
