// assets/login.js
// Script para manipulação da tela de login
// Módulo: Usuários
// Etapa: Autenticação

document.getElementById('loginForm').addEventListener('submit', function(e) {
    // Comentário: Impede envio padrão do formulário
    e.preventDefault();
    
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('errorMessage');
    const successDiv = document.getElementById('successMessage');
    
    // Comentário: Limpa mensagens anteriores
    errorDiv.style.display = 'none';
    successDiv.style.display = 'none';
    
    // Comentário: Validação básica do lado do cliente
    if (!username || !password) {
        errorDiv.textContent = 'Preencha todos os campos.';
        errorDiv.style.display = 'block';
        return;
    }
    
    // Comentário: Envia requisição AJAX para autenticação
    fetch('../api/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            username: username,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            successDiv.textContent = 'Login realizado com sucesso! Redirecionando...';
            successDiv.style.display = 'block';
            setTimeout(() => {
                window.location.href = '../views/dashboard.html';
            }, 1500);
        } else {
            errorDiv.textContent = data.message || 'Erro ao fazer login.';
            errorDiv.style.display = 'block';
        }
    })
    .catch(error => {
        errorDiv.textContent = 'Erro na conexão com o servidor.';
        errorDiv.style.display = 'block';
        console.error('Erro:', error);
    });
});
