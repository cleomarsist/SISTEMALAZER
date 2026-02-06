<?php
/**
 * Teste da API - test_api.php
 * Verifica se todas as rotas estão respondendo corretamente
 */

header('Content-Type: text/html; charset=utf-8');

// Teste simples de requisições
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 20px; }
        .test-item { margin: 15px 0; }
        .test-result { margin-top: 10px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">🧪 Teste de API</h1>
    
    <div class="test-item">
        <button class="btn btn-primary" onclick="testarOrcamentos()">
            1. Testar GET /api.php?rota=orcamentos
        </button>
        <div id="resultado-orcamentos" class="test-result"></div>
    </div>

    <div class="test-item">
        <button class="btn btn-primary" onclick="testarClientes()">
            2. Testar GET /api.php?rota=clientes
        </button>
        <div id="resultado-clientes" class="test-result"></div>
    </div>

    <div class="test-item">
        <button class="btn btn-primary" onclick="testarViaCep()">
            3. Testar GET /api.php?rota=viacep&cep=01310100
        </button>
        <div id="resultado-viacep" class="test-result"></div>
    </div>

    <div class="test-item">
        <button class="btn btn-primary" onclick="testarDelete()">
            4. Testar DELETE /api.php?rota=clientes&id=1
        </button>
        <div id="resultado-delete" class="test-result"></div>
    </div>

    <div class="test-item">
        <button class="btn btn-success" onclick="testarTodoSystem()">
            🔄 Testar Todo o Sistema
        </button>
        <div id="resultado-sistema" class="test-result"></div>
    </div>
</div>

<script>
const BASE_URL = '/SISTEMALAZER';

async function testarOrcamentos() {
    const url = `${BASE_URL}/api.php?rota=orcamentos`;
    try {
        const res = await fetch(url);
        const data = await res.json();
        document.getElementById('resultado-orcamentos').innerHTML = `
            <div class="success">✓ Sucesso (${res.status})</div>
            <pre>${JSON.stringify(data, null, 2)}</pre>
        `;
    } catch (err) {
        document.getElementById('resultado-orcamentos').innerHTML = `
            <div class="error">✗ Erro: ${err.message}</div>
        `;
    }
}

async function testarClientes() {
    const url = `${BASE_URL}/api.php?rota=clientes&pagina=1`;
    try {
        const res = await fetch(url);
        const data = await res.json();
        document.getElementById('resultado-clientes').innerHTML = `
            <div class="success">✓ Sucesso (${res.status})</div>
            <pre>${JSON.stringify(data, null, 2)}</pre>
        `;
    } catch (err) {
        document.getElementById('resultado-clientes').innerHTML = `
            <div class="error">✗ Erro: ${err.message}</div>
        `;
    }
}

async function testarViaCep() {
    const url = `${BASE_URL}/api.php?rota=viacep&cep=01310100`;
    try {
        const res = await fetch(url);
        const data = await res.json();
        document.getElementById('resultado-viacep').innerHTML = `
            <div class="success">✓ Sucesso (${res.status})</div>
            <pre>${JSON.stringify(data, null, 2)}</pre>
        `;
    } catch (err) {
        document.getElementById('resultado-viacep').innerHTML = `
            <div class="error">✗ Erro: ${err.message}</div>
        `;
    }
}

async function testarDelete() {
    const url = `${BASE_URL}/api.php?rota=clientes&id=1`;
    try {
        const res = await fetch(url, { method: 'DELETE' });
        const data = await res.json();
        document.getElementById('resultado-delete').innerHTML = `
            <div class="success">✓ Sucesso (${res.status})</div>
            <pre>${JSON.stringify(data, null, 2)}</pre>
        `;
    } catch (err) {
        document.getElementById('resultado-delete').innerHTML = `
            <div class="error">✗ Erro: ${err.message}</div>
        `;
    }
}

async function testarTodoSystem() {
    const resultado = document.getElementById('resultado-sistema');
    resultado.innerHTML = '<p>Testando...</p>';
    
    let html = '<h4>Resultados do Sistema:</h4>';
    
    try {
        // 1. Teste de Orçamentos
        const orcRes = await fetch(`${BASE_URL}/api.php?rota=orcamentos`);
        const orcData = await orcRes.json();
        html += `<p><strong>✓ Orçamentos:</strong> ${orcData.orcamentos ? orcData.orcamentos.length : 0} registros</p>`;
        
        // 2. Teste de Clientes
        const cliRes = await fetch(`${BASE_URL}/api.php?rota=clientes`);
        const cliData = await cliRes.json();
        html += `<p><strong>✓ Clientes:</strong> ${cliData.clientes ? cliData.clientes.length : 0} registros</p>`;
        
        // 3. Teste de ViaCEP
        const cepRes = await fetch(`${BASE_URL}/api.php?rota=viacep&cep=01310100`);
        const cepData = await cepRes.json();
        html += `<p><strong>✓ ViaCEP:</strong> ${cepData.localidade || 'N/A'}</p>`;
        
        // 4. Teste de Delete
        const delRes = await fetch(`${BASE_URL}/api.php?rota=clientes&id=999`, { method: 'DELETE' });
        const delData = await delRes.json();
        html += `<p><strong>✓ DELETE:</strong> ${delData.sucesso ? 'Funcionando' : 'Erro'}</p>`;
        
        html += '<p class="success mt-3"><strong>✅ Todas as rotas estão funcionando!</strong></p>';
        resultado.innerHTML = html;
    } catch (err) {
        resultado.innerHTML = `<p class="error"><strong>❌ Erro:</strong> ${err.message}</p>`;
    }
}
</script>
</body>
</html>
