<?php
/**
 * Dashboard - Página Principal
 * Visualização geral do sistema com estatísticas
 */
$page = 'dashboard';
$title = 'Dashboard';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '/', 'active' => true]
];
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
        <p class="text-muted">Bem-vindo ao Sistema Lazer - Visualização geral do seu negócio</p>
    </div>
</div>

<!-- Cards de Resumo -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total de Clientes</h6>
                        <h2 class="mb-0">
                            <span id="total-clientes">--</span>
                        </h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> +5 este mês</small>
                    </div>
                    <div style="font-size: 40px; color: #667eea;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total de Orçamentos</h6>
                        <h2 class="mb-0">
                            <span id="total-orcamentos">--</span>
                        </h2>
                        <small class="text-info"><i class="fas fa-info-circle"></i> Abertos</small>
                    </div>
                    <div style="font-size: 40px; color: #764ba2;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Vendas Mês</h6>
                        <h2 class="mb-0">
                            R$ <span id="vendas-mes">--</span>
                        </h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> +15%</small>
                    </div>
                    <div style="font-size: 40px; color: #28a745;">
                        <i class="fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total de Pedidos</h6>
                        <h2 class="mb-0">
                            <span id="total-pedidos">--</span>
                        </h2>
                        <small class="text-warning"><i class="fas fa-clock"></i> Em processamento</small>
                    </div>
                    <div style="font-size: 40px; color: #ffc107;">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Vendas por Mês</h5>
            </div>
            <div class="card-body">
                <canvas id="chartVendas" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Distribuição de Clientes</h5>
            </div>
            <div class="card-body">
                <canvas id="chartClientes" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Últimos Pedidos -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history"></i> Últimos Pedidos</h5>
                <a href="/pedidos" class="btn btn-sm btn-primary-custom btn-custom">Ver Todos</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablePedidos">
                        <thead>
                            <tr class="text-muted">
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin"></i> Carregando...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Carregar estatísticas
        carregarEstatisticas();
        // Carregar gráficos
        criarGraficos();
        // Carregar últimos pedidos
        carregarUltimosPedidos();
    });

    function carregarEstatisticas() {
        // Simular dados - Em produção, fazer requisição para API
        fetch('/api/clientes')
            .then(r => r.json())
            .then(data => {
                document.getElementById('total-clientes').textContent = data.length || 0;
            })
            .catch(() => document.getElementById('total-clientes').textContent = '0');

        fetch('/api/orcamentos')
            .then(r => r.json())
            .then(data => {
                document.getElementById('total-orcamentos').textContent = data.length || 0;
            })
            .catch(() => document.getElementById('total-orcamentos').textContent = '0');

        fetch('/api/pedidos')
            .then(r => r.json())
            .then(data => {
                document.getElementById('total-pedidos').textContent = data.length || 0;
            })
            .catch(() => document.getElementById('total-pedidos').textContent = '0');

        // Vendas do mês (simulado)
        document.getElementById('vendas-mes').textContent = '25.450,00';
    }

    function criarGraficos() {
        // Gráfico de Vendas
        const ctxVendas = document.getElementById('chartVendas').getContext('2d');
        new Chart(ctxVendas, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Vendas (R$)',
                    data: [12000, 19000, 15000, 25000, 22000, 28000],
                    backgroundColor: 'rgba(102, 126, 234, 0.6)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Clientes
        const ctxClientes = document.getElementById('chartClientes').getContext('2d');
        new Chart(ctxClientes, {
            type: 'doughnut',
            data: {
                labels: ['Pessoa Física', 'Pessoa Jurídica'],
                datasets: [{
                    data: [65, 35],
                    backgroundColor: ['rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    function carregarUltimosPedidos() {
        fetch('/api/pedidos?limit=5')
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#tablePedidos tbody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Nenhum pedido encontrado</td></tr>';
                    return;
                }
                tbody.innerHTML = data.map(pedido => `
                    <tr>
                        <td><strong>#${pedido.id}</strong></td>
                        <td>${pedido.cliente || 'N/A'}</td>
                        <td>${new Date(pedido.data).toLocaleDateString('pt-BR')}</td>
                        <td><strong>R$ ${parseFloat(pedido.valor).toFixed(2)}</strong></td>
                        <td>
                            <span class="badge badge-custom bg-${pedido.status === 'concluído' ? 'success' : 'warning'}">
                                ${pedido.status || 'Pendente'}
                            </span>
                        </td>
                        <td>
                            <a href="/pedidos/${pedido.id}" class="btn btn-sm btn-outline-primary">Ver</a>
                        </td>
                    </tr>
                `).join('');
            })
            .catch(err => {
                console.error('Erro ao carregar pedidos:', err);
                document.querySelector('#tablePedidos tbody').innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Erro ao carregar dados</td></tr>';
            });
    }
</script>
