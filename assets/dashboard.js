// assets/dashboard.js
// Script para manipulação do dashboard
// Módulo: Dashboard
// Etapa: Interface principal

// Comentário: Verifica autenticação ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    // Comentário: Simula carregamento de dados (seria substituído por chamadas AJAX)
    loadDashboardData();
    
    // Comentário: Configura botão de logout
    document.getElementById('logoutBtn').addEventListener('click', function() {
        logout();
    });
});

/**
 * Carrega dados do dashboard
 */
function loadDashboardData() {
    // Comentário: Neste exemplo, usamos dados fictícios
    // Na produção, estes dados viriam de endpoints AJAX
    document.getElementById('usernameDisplay').textContent = getUserInfoFromSession();
    document.getElementById('openOrders').textContent = '5';
    document.getElementById('openBudgets').textContent = '3';
    document.getElementById('receivableAccounts').textContent = 'R$ 15.000,00';
    document.getElementById('payableAccounts').textContent = 'R$ 8.500,00';
}

/**
 * Obtém informações do usuário (seria melhor obter via sessão do backend)
 */
function getUserInfoFromSession() {
    // Comentário: Aqui você poderia fazer um fetch para uma API que retorna os dados
    return localStorage.getItem('username') || 'Usuário';
}

/**
 * Realiza logout do usuário
 */
function logout() {
    fetch('../api/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            localStorage.clear();
            window.location.href = '../views/login.html';
        }
    })
    .catch(error => {
        console.error('Erro ao fazer logout:', error);
        localStorage.clear();
        window.location.href = '../views/login.html';
    });
}
