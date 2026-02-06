<?php
/**
 * Lista de Orçamentos
 */
$page = 'orcamentos';
$title = 'Orçamentos';
$breadcrumb = [
    ['label' => 'Orçamentos', 'url' => '/orcamentos', 'active' => true]
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-file-alt"></i> Gerenciar Orçamentos</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="/orcamentos/novo" class="btn btn-primary-custom btn-custom">
            <i class="fas fa-plus"></i> Novo Orçamento
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <input type="text" id="filtroNumero" class="form-control" placeholder="Buscar por número...">
            </div>
            <div class="col-md-4">
                <select id="filtroStatus" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="aberto">Aberto</option>
                    <option value="aceito">Aceito</option>
                    <option value="rejeitado">Rejeitado</option>
                    <option value="convertido">Convertido para Pedido</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-outline-secondary w-100" onclick="carregarOrcamentos()">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Orçamentos -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-list"></i> Lista de Orçamentos
            <span class="badge bg-secondary float-end" id="totalOrcamentos">0</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabelaOrcamentos">
                <thead>
                    <tr class="text-muted">
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Itens</th>
                        <th>Status</th>
                        <th>Validade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin"></i> Carregando...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light text-center" id="paginacao"></div>
</div>

<script>
    // Configuração - Base URL para requisições AJAX
    const BASE_URL = '/SISTEMALAZER';
    
    document.addEventListener('DOMContentLoaded', function() {
        carregarOrcamentos();
        document.getElementById('filtroNumero').addEventListener('keyup', debounce(carregarOrcamentos, 500));
        document.getElementById('filtroStatus').addEventListener('change', carregarOrcamentos);
    });

    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func(...args), delay);
        };
    }

    function carregarOrcamentos(pagina = 1) {
        const numero = document.getElementById('filtroNumero').value;
        const status = document.getElementById('filtroStatus').value;
        
        let url = `${BASE_URL}/api.php?rota=orcamentos&pagina=${pagina}`;
        if (numero) url += `&numero=${encodeURIComponent(numero)}`;
        if (status) url += `&status=${status}`;

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#tabelaOrcamentos tbody');
                document.getElementById('totalOrcamentos').textContent = data.total || 0;

                if (!data.orcamentos || data.orcamentos.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Nenhum orçamento encontrado</td></tr>';
                    return;
                }

                const badges = {
                    'aberto': 'warning',
                    'aceito': 'success',
                    'rejeitado': 'danger',
                    'convertido': 'info'
                };

                tbody.innerHTML = data.orcamentos.map(orc => `
                    <tr>
                        <td><strong>${orc.numero || 'N/A'}</strong></td>
                        <td>${escapeHtml(orc.cliente || '')}</td>
                        <td>${new Date(orc.data_criacao).toLocaleDateString('pt-BR')}</td>
                        <td><strong>R$ ${parseFloat(orc.valor_total || 0).toFixed(2)}</strong></td>
                        <td>
                            <span class="badge bg-light text-dark">${orc.qtd_itens || 0}</span>
                        </td>
                        <td>
                            <span class="badge badge-custom bg-${badges[orc.status] || 'secondary'}">
                                ${capitalizar(orc.status || 'N/A')}
                            </span>
                        </td>
                        <td>${new Date(orc.data_validade).toLocaleDateString('pt-BR')}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="/orcamentos/${orc.id}" class="btn btn-sm btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/orcamentos/${orc.id}/editar" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/orcamentos/${orc.id}/pdf" class="btn btn-sm btn-outline-danger" title="PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `).join('');
            })
            .catch(err => {
                console.error('Erro:', err);
                document.querySelector('#tabelaOrcamentos tbody').innerHTML = 
                    '<tr><td colspan="8" class="text-center text-danger py-4">Erro ao carregar dados</td></tr>';
            });
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function capitalizar(texto) {
        return texto.charAt(0).toUpperCase() + texto.slice(1);
    }
</script>
