// assets/product.js
// Script para manipulação do formulário de produto
// Módulo: Produtos
// Etapa: Cadastro inicial

document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Comentário: Exemplo de manipulação, pode ser expandido para AJAX
    document.getElementById('productMsg').textContent = 'Cadastro enviado...';
    // Aqui pode ser implementado AJAX para cadastro
});
