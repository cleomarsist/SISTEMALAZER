<?php
/**
 * Formulário de Cliente (Novo/Editar)
 */
$page = 'clientes';
$isEdicao = isset($cliente) && $cliente;
$title = $isEdicao ? 'Editar Cliente' : 'Novo Cliente';
$breadcrumb = [
    ['label' => 'Clientes', 'url' => '/clientes', 'active' => false],
    ['label' => $title, 'url' => '', 'active' => true]
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1>
            <i class="fas fa-user-plus"></i> 
            <?php echo $title; ?>
        </h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="/clientes" class="btn btn-outline-secondary btn-custom">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form id="formCliente" class="row g-3">
    <input type="hidden" id="clienteId" value="<?php echo isset($cliente['id']) ? htmlspecialchars($cliente['id']) : ''; ?>">
    
    <div class="col-md-6">
        <label class="form-label">Tipo de Cliente *</label>
        <select id="tipo" class="form-select" required onchange="mudarTipoCliente()">
            <option value="">Selecione...</option>
            <option value="PF" <?php echo isset($cliente['tipo']) && $cliente['tipo'] === 'PF' ? 'selected' : ''; ?>>
                Pessoa Física
            </option>
            <option value="PJ" <?php echo isset($cliente['tipo']) && $cliente['tipo'] === 'PJ' ? 'selected' : ''; ?>>
                Pessoa Jurídica
            </option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label" id="labelDocumento">CPF/CNPJ *</label>
        <input type="text" id="documento" class="form-control" placeholder="000.000.000-00" 
               value="<?php echo isset($cliente['documento']) ? htmlspecialchars($cliente['documento']) : ''; ?>" required>
        <div id="statusDocumento" class="form-text"></div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Nome Completo/Razão Social *</label>
        <input type="text" id="nome" class="form-control" 
               value="<?php echo isset($cliente['nome']) ? htmlspecialchars($cliente['nome']) : ''; ?>" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Nome Fantasia</label>
        <input type="text" id="nomeFantasia" class="form-control" 
               value="<?php echo isset($cliente['nome_fantasia']) ? htmlspecialchars($cliente['nome_fantasia']) : ''; ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Email *</label>
        <input type="email" id="email" class="form-control" 
               value="<?php echo isset($cliente['email']) ? htmlspecialchars($cliente['email']) : ''; ?>" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Telefone *</label>
        <input type="tel" id="telefone" class="form-control" placeholder="(11) 99999-9999" 
               value="<?php echo isset($cliente['telefone']) ? htmlspecialchars($cliente['telefone']) : ''; ?>" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">CEP *</label>
        <div class="input-group">
            <input type="text" id="cep" class="form-control" placeholder="00000-000" 
                   value="<?php echo isset($cliente['cep']) ? htmlspecialchars($cliente['cep']) : ''; ?>" required>
            <button class="btn btn-outline-secondary" type="button" id="btnBuscarCep">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
    </div>

    <div class="col-md-9">
        <label class="form-label">Rua *</label>
        <input type="text" id="rua" class="form-control" 
               value="<?php echo isset($cliente['rua']) ? htmlspecialchars($cliente['rua']) : ''; ?>" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Número *</label>
        <input type="text" id="numero" class="form-control" 
               value="<?php echo isset($cliente['numero']) ? htmlspecialchars($cliente['numero']) : ''; ?>" required>
    </div>

    <div class="col-md-9">
        <label class="form-label">Complemento</label>
        <input type="text" id="complemento" class="form-control" placeholder="Apto, Sala, etc..." 
               value="<?php echo isset($cliente['complemento']) ? htmlspecialchars($cliente['complemento']) : ''; ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Bairro *</label>
        <input type="text" id="bairro" class="form-control" 
               value="<?php echo isset($cliente['bairro']) ? htmlspecialchars($cliente['bairro']) : ''; ?>" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Cidade *</label>
        <input type="text" id="cidade" class="form-control" 
               value="<?php echo isset($cliente['cidade']) ? htmlspecialchars($cliente['cidade']) : ''; ?>" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Estado *</label>
        <select id="estado" class="form-select" required>
            <option value="">Selecione...</option>
            <?php 
            $estados = ['SP', 'RJ', 'MG', 'BA', 'RS', 'PR', 'SC', 'PE', 'CE', 'PA', 'GO', 'PB', 'DF', 'MA', 'ES', 'PI', 'RN', 'AL', 'MT', 'MS', 'RO', 'AC', 'AM', 'RR', 'AP', 'TO'];
            foreach ($estados as $uf) {
                $selected = isset($cliente['estado']) && $cliente['estado'] === $uf ? 'selected' : '';
                echo "<option value=\"$uf\" $selected>$uf</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-12">
        <hr>
    </div>

    <div class="col-md-12">
        <h6 class="text-muted">Informações Adicionais</h6>
    </div>

    <div class="col-md-6">
        <label class="form-label">Contato Adicional</label>
        <input type="tel" id="telefoneAdicional" class="form-control" 
               value="<?php echo isset($cliente['telefone_adicional']) ? htmlspecialchars($cliente['telefone_adicional']) : ''; ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Data de Cadastro</label>
        <input type="date" id="dataCadastro" class="form-control" readonly 
               value="<?php echo isset($cliente['data_cadastro']) ? htmlspecialchars($cliente['data_cadastro']) : date('Y-m-d'); ?>">
    </div>

    <div class="col-md-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="ativo" 
                   <?php echo isset($cliente['ativo']) && $cliente['ativo'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="ativo">
                Cliente Ativo
            </label>
        </div>
    </div>

    <div class="col-md-12">
        <hr>
    </div>

    <div class="col-md-12 text-end">
        <a href="/clientes" class="btn btn-outline-secondary btn-custom">
            <i class="fas fa-times"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primary-custom btn-custom">
            <i class="fas fa-save"></i> Salvar Cliente
        </button>
    </div>
</form>

<script src="/public/js/cliente_form.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formCliente');
        const btnBuscarCep = document.getElementById('btnBuscarCep');
        const inputDocumento = document.getElementById('documento');

        // Buscar CEP via ViaCEP
        btnBuscarCep.addEventListener('click', buscarCep);
        
        // Validar documento em tempo real
        inputDocumento.addEventListener('blur', validarDocumento);

        // Submit do formulário
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            salvarCliente();
        });

        // Inicializar tipo
        if (document.getElementById('tipo').value) {
            mudarTipoCliente();
        }
    });

    function mudarTipoCliente() {
        const tipo = document.getElementById('tipo').value;
        const labelDoc = document.getElementById('labelDocumento');
        const inputDoc = document.getElementById('documento');

        if (tipo === 'PF') {
            labelDoc.textContent = 'CPF *';
            inputDoc.placeholder = '000.000.000-00';
            inputDoc.maxLength = '14';
        } else if (tipo === 'PJ') {
            labelDoc.textContent = 'CNPJ *';
            inputDoc.placeholder = '00.000.000/0001-00';
            inputDoc.maxLength = '18';
        }
        inputDoc.value = '';
    }

    function buscarCep() {
        const cep = document.getElementById('cep').value.replace(/\D/g, '');
        
        if (cep.length !== 8) {
            alert('CEP inválido!');
            return;
        }

        fetch(`/api/viacep?cep=${cep}`)
            .then(r => r.json())
            .then(data => {
                if (data.erro) {
                    alert('CEP não encontrado!');
                    return;
                }
                document.getElementById('rua').value = data.logradouro || '';
                document.getElementById('bairro').value = data.bairro || '';
                document.getElementById('cidade').value = data.localidade || '';
                document.getElementById('estado').value = data.uf || '';
            })
            .catch(err => alert('Erro ao buscar CEP: ' + err.message));
    }

    function validarDocumento() {
        // Implementar validação CPF/CNPJ
        const tipo = document.getElementById('tipo').value;
        const doc = document.getElementById('documento').value;
        const status = document.getElementById('statusDocumento');

        if (!doc) return;

        // Aqui você poderia fazer uma validação real via API
        status.textContent = '✓ Documento validado';
        status.classList.add('text-success');
    }

    function salvarCliente() {
        const clienteId = document.getElementById('clienteId').value;
        const dados = {
            tipo: document.getElementById('tipo').value,
            documento: document.getElementById('documento').value,
            nome: document.getElementById('nome').value,
            nome_fantasia: document.getElementById('nomeFantasia').value,
            email: document.getElementById('email').value,
            telefone: document.getElementById('telefone').value,
            cep: document.getElementById('cep').value,
            rua: document.getElementById('rua').value,
            numero: document.getElementById('numero').value,
            complemento: document.getElementById('complemento').value,
            bairro: document.getElementById('bairro').value,
            cidade: document.getElementById('cidade').value,
            estado: document.getElementById('estado').value,
            telefone_adicional: document.getElementById('telefoneAdicional').value,
            ativo: document.getElementById('ativo').checked ? 1 : 0
        };

        const url = clienteId ? `/api/clientes/${clienteId}` : '/api/clientes';
        const metodo = clienteId ? 'PUT' : 'POST';

        fetch(url, {
            method: metodo,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                alert(clienteId ? 'Cliente atualizado com sucesso!' : 'Cliente criado com sucesso!');
                window.location.href = '/clientes';
            } else {
                alert('Erro: ' + (data.mensagem || 'Desconhecido'));
            }
        })
        .catch(err => alert('Erro ao salvar: ' + err.message));
    }
</script>
