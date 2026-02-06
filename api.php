<?php
/**
 * API Gateway - api.php
 * Processa requisições AJAX para os Controllers
 */

header('Content-Type: application/json; charset=utf-8');

// Obter a rota
$rota = $_GET['rota'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

try {
    // Rota: GET /api.php?rota=clientes
    if ($method === 'GET' && $rota === 'clientes') {
        $pagina = intval($_GET['pagina'] ?? 1);
        $nome = $_GET['nome'] ?? '';
        $tipo = $_GET['tipo'] ?? '';
        
        // Simular dados de clientes
        $clientes = [
            ['id' => 1, 'nome' => 'João Silva', 'tipo' => 'PF', 'documento' => '12345678901', 'email' => 'joao@email.com', 'telefone' => '(11) 99999-9999', 'cidade' => 'São Paulo'],
            ['id' => 2, 'nome' => 'Empresa ABC', 'tipo' => 'PJ', 'documento' => '12345678901234', 'email' => 'contato@abc.com', 'telefone' => '(11) 98888-8888', 'cidade' => 'São Paulo'],
            ['id' => 3, 'nome' => 'Maria Santos', 'tipo' => 'PF', 'documento' => '98765432109', 'email' => 'maria@email.com', 'telefone' => '(11) 97777-7777', 'cidade' => 'Rio de Janeiro'],
            ['id' => 4, 'nome' => 'Pedro Costa', 'tipo' => 'PF', 'documento' => '11122233344', 'email' => 'pedro@email.com', 'telefone' => '(21) 96666-6666', 'cidade' => 'Rio de Janeiro'],
            ['id' => 5, 'nome' => 'Empresa XYZ', 'tipo' => 'PJ', 'documento' => '98765432100122', 'email' => 'contato@xyz.com', 'telefone' => '(11) 95555-5555', 'cidade' => 'Belo Horizonte'],
        ];
        
        // Filtrar por nome
        if ($nome) {
            $clientes = array_filter($clientes, function($c) use ($nome) {
                return stripos($c['nome'], $nome) !== false;
            });
        }
        
        // Filtrar por tipo
        if ($tipo) {
            $clientes = array_filter($clientes, function($c) use ($tipo) {
                return $c['tipo'] === $tipo;
            });
        }
        
        $clientes = array_values($clientes);
        $total = count($clientes);
        
        // Paginação
        $itens_por_pagina = 10;
        $total_paginas = ceil($total / $itens_por_pagina);
        $inicio = ($pagina - 1) * $itens_por_pagina;
        $clientes_paginados = array_slice($clientes, $inicio, $itens_por_pagina);
        
        echo json_encode([
            'sucesso' => true,
            'clientes' => $clientes_paginados,
            'total' => $total,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas
        ]);
        exit;
    }
    
    // Rota: POST /api.php?rota=clientes
    if ($method === 'POST' && $rota === 'clientes') {
        $dados = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($dados['tipo']) || !isset($dados['documento']) || !isset($dados['nome'])) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Campos obrigatórios faltando']);
            exit;
        }
        
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Cliente criado com sucesso',
            'id' => rand(4, 1000)
        ]);
        exit;
    }
    
    // Rota: PUT /api.php?rota=clientes&id=X
    if ($method === 'PUT' && $rota === 'clientes' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $dados = json_decode(file_get_contents('php://input'), true);
        
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Cliente atualizado com sucesso',
            'id' => $id
        ]);
        exit;
    }
    
    // Rota: DELETE /api.php?rota=clientes&id=X
    if ($method === 'DELETE' && $rota === 'clientes' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Cliente deletado com sucesso',
            'id' => $id
        ]);
        exit;
    }
    
    // Rota: GET /api.php?rota=orcamentos
    if ($method === 'GET' && $rota === 'orcamentos') {
        $pagina = intval($_GET['pagina'] ?? 1);
        $numero = $_GET['numero'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $orcamentos = [
            ['id' => 1, 'numero' => 'ORC-2026-0001', 'cliente' => 'João Silva', 'data_criacao' => '2026-01-15', 'data_validade' => '2026-02-15', 'valor_total' => 1500.00, 'qtd_itens' => 3, 'status' => 'aberto'],
            ['id' => 2, 'numero' => 'ORC-2026-0002', 'cliente' => 'Empresa ABC', 'data_criacao' => '2026-01-20', 'data_validade' => '2026-02-20', 'valor_total' => 5000.00, 'qtd_itens' => 5, 'status' => 'aceito'],
            ['id' => 3, 'numero' => 'ORC-2026-0003', 'cliente' => 'Maria Santos', 'data_criacao' => '2026-02-01', 'data_validade' => '2026-03-01', 'valor_total' => 800.00, 'qtd_itens' => 2, 'status' => 'convertido'],
            ['id' => 4, 'numero' => 'ORC-2026-0004', 'cliente' => 'Pedro Costa', 'data_criacao' => '2026-02-03', 'data_validade' => '2026-03-03', 'valor_total' => 2200.00, 'qtd_itens' => 4, 'status' => 'rejeitado'],
        ];
        
        if ($numero) {
            $orcamentos = array_filter($orcamentos, function($o) use ($numero) {
                return stripos($o['numero'], $numero) !== false;
            });
        }
        
        if ($status) {
            $orcamentos = array_filter($orcamentos, function($o) use ($status) {
                return $o['status'] === $status;
            });
        }
        
        $orcamentos = array_values($orcamentos);
        
        echo json_encode([
            'sucesso' => true,
            'orcamentos' => $orcamentos,
            'total' => count($orcamentos)
        ]);
        exit;
    }
    
    // Rota: GET /api.php?rota=viacep&cep=X
    if ($method === 'GET' && $rota === 'viacep' && isset($_GET['cep'])) {
        $cep = str_replace('-', '', $_GET['cep']);
        
        if (strlen($cep) !== 8) {
            http_response_code(400);
            echo json_encode(['erro' => true, 'mensagem' => 'CEP inválido']);
            exit;
        }
        
        // Simular resposta ViaCEP
        echo json_encode([
            'cep' => substr($cep, 0, 5) . '-' . substr($cep, 5),
            'logradouro' => 'Rua das Flores',
            'bairro' => 'Centro',
            'localidade' => 'São Paulo',
            'uf' => 'SP'
        ]);
        exit;
    }
    
    // Rota não encontrada
    http_response_code(404);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Rota não encontrada',
        'rota' => $rota,
        'metodo' => $method
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}
?>

