/**
 * Script para formulário de cliente
 */

document.addEventListener('DOMContentLoaded', function() {
    // Aplicar máscaras
    const inputCPF = document.getElementById('documento');
    const inputTelefone = document.getElementById('telefone');
    const inputTelefoneAd = document.getElementById('telefoneAdicional');
    const inputCep = document.getElementById('cep');

    if (inputCPF) {
        inputCPF.addEventListener('input', function() {
            const tipo = document.getElementById('tipo').value;
            if (tipo === 'PF') {
                aplicarMascaraCPF(this);
            } else if (tipo === 'PJ') {
                aplicarMascaraCNPJ(this);
            }
        });
    }

    if (inputTelefone) {
        inputTelefone.addEventListener('input', function() {
            aplicarMascaraTelefone(this);
        });
    }

    if (inputTelefoneAd) {
        inputTelefoneAd.addEventListener('input', function() {
            aplicarMascaraTelefone(this);
        });
    }

    if (inputCep) {
        inputCep.addEventListener('input', function() {
            aplicarMascaraCEP(this);
        });
    }
});

function aplicarMascaraCPF(campo) {
    campo.value = campo.value
        .replace(/\D/g, '')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2')
        .substring(0, 14);
}

function aplicarMascaraCNPJ(campo) {
    campo.value = campo.value
        .replace(/\D/g, '')
        .replace(/(\d{2})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1/$2')
        .replace(/(\d{4})(\d)/, '$1-$2')
        .substring(0, 18);
}

function aplicarMascaraTelefone(campo) {
    campo.value = campo.value
        .replace(/\D/g, '')
        .replace(/(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{5})(\d)/, '$1-$2')
        .substring(0, 15);
}

function aplicarMascaraCEP(campo) {
    campo.value = campo.value
        .replace(/\D/g, '')
        .replace(/(\d{5})(\d)/, '$1-$2')
        .substring(0, 9);
}

function validarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    if (cpf.length !== 11) return false;
    
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf[i]) * (10 - i);
    }
    let resto = soma % 11;
    let digito1 = resto < 2 ? 0 : 11 - resto;
    
    if (parseInt(cpf[9]) !== digito1) return false;
    
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf[i]) * (11 - i);
    }
    resto = soma % 11;
    let digito2 = resto < 2 ? 0 : 11 - resto;
    
    return parseInt(cpf[10]) === digito2;
}

function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, '');
    if (cnpj.length !== 14) return false;
    
    if (/^(\d)\1{13}$/.test(cnpj)) return false;
    
    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0, tamanho);
    let digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado !== parseInt(digitos.charAt(0))) return false;
    
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    return resultado === parseInt(digitos.charAt(1));
}

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
        alert('CEP inválido! Digite 8 dígitos.');
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
        .catch(err => {
            console.error('Erro:', err);
            alert('Erro ao buscar CEP. Tente novamente.');
        });
}

function salvarCliente() {
    const clienteId = document.getElementById('clienteId').value;
    const tipo = document.getElementById('tipo').value;
    const documento = document.getElementById('documento').value;
    
    // Validar tipo
    if (!tipo) {
        alert('Selecione o tipo de cliente');
        return;
    }

    // Validar documento
    const docValido = tipo === 'PF' ? validarCPF(documento) : validarCNPJ(documento);
    if (!docValido) {
        alert(tipo === 'PF' ? 'CPF inválido!' : 'CNPJ inválido!');
        return;
    }

    const dados = {
        tipo: tipo,
        documento: documento,
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
    .catch(err => {
        console.error('Erro:', err);
        alert('Erro ao salvar: ' + err.message);
    });
}
