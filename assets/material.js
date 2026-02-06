// assets/material.js
// Script para manipulação do formulário de material
// Módulo: Materiais
// Etapa: Cadastro inicial

document.getElementById('materialForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Comentário: Exemplo de manipulação, pode ser expandido para AJAX
    document.getElementById('materialMsg').textContent = 'Cadastro enviado...';
    // Aqui pode ser implementado AJAX para cadastro
});
