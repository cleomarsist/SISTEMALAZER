// assets/client.js
// Script para manipulação do formulário de cliente/fornecedor
// Módulo: Clientes/Fornecedores
// Etapa: Cadastro inicial

document.getElementById('clientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Comentário: Exemplo de manipulação, pode ser expandido para AJAX
    document.getElementById('clientMsg').textContent = 'Cadastro enviado...';
    // Aqui pode ser implementado AJAX para cadastro
});
