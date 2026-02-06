<?php
/**
 * Lista de Clientes
 */
$page = 'clientes';
$title = 'Clientes';
$breadcrumb = [
    ['label' => 'Clientes', 'url' => '/clientes', 'active' => true]
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-users"></i> Gerenciar Clientes</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="/clientes/novo" class="btn btn-primary-custom btn-custom">
            <i class="fas fa-plus"></i> Novo Cliente
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <input type="text" id="filtroNome" class="form-control" placeholder="Buscar por nome...">
            </div>
            <div class="col-md-3">
                <select id="filtroTipo" class="form-select">
                    <option value="">Todos os tipos</option>
                    <option value="PF">Pessoa Física</option>
                    <option value="PJ">Pessoa Jurídica</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-secondary w-100" onclick="carregarClientes()">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Clientes -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-list"></i> Lista de Clientes
            <span class="badge bg-secondary float-end" id="totalClientes">0</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabelaClientes">
                <thead>
                    <tr class="text-muted">
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>CPF/CNPJ</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Cidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
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
        carregarClientes();
        
        document.getElementById('filtroNome').addEventListener('keyup', debounce(carregarClientes, 500));
        document.getElementById('filtroTipo').addEventListener('change', carregarClientes);
    });

    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func(...args), delay);
        };
    }

    function carregarClientes(pagina = 1) {
        const nome = document.getElementById('filtroNome').value;
        const tipo = document.getElementById('filtroTipo').value;
        
        let url = `${BASE_URL}/api.php?rota=clientes&pagina=${pagina}`;
        if (nome) url += `&nome=${encodeURIComponent(nome)}`;
        if (tipo) url += `&tipo=${tipo}`;

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#tabelaClientes tbody');
                document.getElementById('totalClientes').textContent = data.total || 0;

                if (!data.clientes || data.clientes.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Nenhum cliente encontrado</td></tr>';
                    return;
                }

                tbody.innerHTML = data.clientes.map(cliente => `
                    <tr>
                        <td><strong>${escapeHtml(cliente.nome)}</strong></td>
                        <td>
                            <span class="badge ${cliente.tipo === 'PF' ? 'bg-info' : 'bg-primary'}">
                                ${cliente.tipo === 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica'}
                            </span>
                        </td>
                        <td><code>${formatarDocumento(cliente.documento)}</code></td>
                        <td><small>${cliente.email || '—'}</small></td>
                        <td><small>${cliente.telefone || '—'}</small></td>
                        <td>${cliente.cidade || '—'}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="/clientes/${cliente.id}" class="btn btn-sm btn-outline-primary" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/clientes/${cliente.id}/editar" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger" onclick="deletarCliente(${cliente.id})" title="Deletar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');

                // Paginação
                if (data.total_paginas > 1) {
                    let paginacao = '<nav><ul class="pagination justify-content-center">';
                    for (let i = 1; i <= data.total_paginas; i++) {
                        paginacao += `<li class="page-item ${i === pagina ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="carregarClientes(${i}); return false;">${i}</a>
                        </li>`;
                    }
                    paginacao += '</ul></nav>';
                    document.getElementById('paginacao').innerHTML = paginacao;
                }
            })
            .catch(err => {
                console.error('Erro:', err);
                document.querySelector('#tabelaClientes tbody').innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Erro ao carregar dados</td></tr>';
            });
    }

    function deletarCliente(id) {
        if (!confirm('Tem certeza que deseja deletar este cliente?')) return;

        fetch(`${BASE_URL}/api.php?rota=clientes&id=${id}`, { method: 'DELETE' })
            .then(r => r.json())
            .then(data => {
                if (data.sucesso) {
                    alert('Cliente deletado com sucesso');
                    carregarClientes();
                } else {
                    alert('Erro: ' + (data.mensagem || 'Desconhecido'));
                }
            })
            .catch(err => alert('Erro ao deletar: ' + err.message));
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function formatarDocumento(doc) {
        if (!doc) return '';
        doc = doc.replace(/\D/g, '');
        if (doc.length === 11) {
            // CPF
            return `${doc.substring(0,3)}.${doc.substring(3,6)}.${doc.substring(6,9)}-${doc.substring(9)}`;
        } else if (doc.length === 14) {
            // CNPJ
            return `${doc.substring(0,2)}.${doc.substring(2,5)}.${doc.substring(5,8)}/0001-${doc.substring(8)}`;
        }
        return doc;
    }
</script>
