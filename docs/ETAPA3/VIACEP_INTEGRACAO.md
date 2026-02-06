# Integração ViaCEP

## O que é ViaCEP?

ViaCEP é um serviço web gratuito que fornece dados de endereços brasileiros baseado em CEP.

**Site:** https://viacep.com.br

---

## Como Funciona

### 1. Busca Simples

```javascript
// Frontend JavaScript
fetch('/viacep?cep=01310100')
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            document.getElementById('endereco').value = data.dados.endereco;
            document.getElementById('bairro').value = data.dados.bairro;
            document.getElementById('cidade').value = data.dados.cidade;
            document.getElementById('estado').value = data.dados.estado;
        }
    });
```

### 2. Integração Backend

O Model `ViaCEPModel` implementa:

1. **Busca no Cache Local**
   - Verifica tabela `cep_cache`
   - Se encontrar, retorna imediatamente

2. **Consulta à API**
   - Se não estiver em cache
   - Faz requisição HTTP para ViaCEP
   - Aguarda resposta (timeout 5s)

3. **Salvamento em Cache**
   - Armazena resultado na base local
   - Próximas buscas são imediatas
   - Cache expira em 30 dias

---

## Tabela de Cache

```sql
CREATE TABLE IF NOT EXISTS cep_cache (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cep VARCHAR(8) NOT NULL UNIQUE,
    dados_endereco JSON NOT NULL,
    data_cache TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Exemplo: Formulário de Cadastro de Cliente

```html
<form id="clienteForm">
    <div class="form-group">
        <label for="cep">CEP</label>
        <input type="text" id="cep" name="cep" 
               placeholder="00000-000" 
               pattern="\d{5}-?\d{3}"
               required>
        <button type="button" id="btnBuscaCEP">Buscar</button>
    </div>

    <div class="form-group">
        <label for="endereco">Endereço</label>
        <input type="text" id="endereco" name="endereco" 
               readonly required>
    </div>

    <div class="form-group">
        <label for="bairro">Bairro</label>
        <input type="text" id="bairro" name="bairro" 
               required>
    </div>

    <div class="form-group">
        <label for="cidade">Cidade</label>
        <input type="text" id="cidade" name="cidade" 
               required>
    </div>

    <div class="form-group">
        <label for="estado">Estado</label>
        <select id="estado" name="estado" required>
            <option value="">Selecione</option>
            <option value="SP">São Paulo</option>
            <option value="RJ">Rio de Janeiro</option>
            <!-- ... outros estados ... -->
        </select>
    </div>

    <button type="submit">Salvar Cliente</button>
</form>

<script>
document.getElementById('btnBuscaCEP').addEventListener('click', function() {
    const cep = document.getElementById('cep').value;
    
    if (!cep) {
        alert('Digite um CEP');
        return;
    }

    // Remover formatação
    const cepLimpo = cep.replace(/\D/g, '');

    // Buscar no servidor
    fetch('/viacep?cep=' + cepLimpo)
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                // Preencher campos
                document.getElementById('endereco').value = data.dados.endereco;
                document.getElementById('bairro').value = data.dados.bairro;
                document.getElementById('cidade').value = data.dados.cidade;
                document.getElementById('estado').value = data.dados.estado;
                
                alert('CEP encontrado!');
            } else {
                alert('CEP não encontrado: ' + data.erro);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao buscar CEP');
        });
});
</script>
```

---

## Validações de CEP

### Frontend (JavaScript)
```javascript
function validarCEP(cep) {
    // Remover formatação
    const cepLimpo = cep.replace(/\D/g, '');
    
    // Verificar se tem 8 dígitos
    if (cepLimpo.length !== 8) {
        return false;
    }
    
    // Verificar se é numérico
    return /^\d{8}$/.test(cepLimpo);
}

// Usar
if (validarCEP('01310-100')) {
    console.log('CEP válido');
}
```

### Backend (PHP)
```php
// Automático pelo ViaCEPModel
$viaCepModel = new ViaCEPModel();

$valido = $viaCepModel->validarCEP('01310100');  // true
$valido = $viaCepModel->validarCEP('123');       // false
```

---

## Formatação de CEP

### Formatar (XXXXX-XXX)
```php
$cep = '01310100';
$formatado = $viaCepModel->formatarCEP($cep);
// Resultado: '01310-100'
```

### Expandir (remove hífem)
```php
$cep = '01310-100';
$expandido = $viaCepModel->expandirCEP($cep);
// Resultado: '01310100'
```

---

## Tratamento de Erros

### CEP Não Encontrado
```
GET /viacep?cep=00000000

Resposta:
{
    "sucesso": false,
    "erro": "CEP não encontrado"
}
```

### CEP Inválido
```
GET /viacep?cep=123

Resposta 400:
{
    "sucesso": false,
    "erro": "CEP inválido"
}
```

### Erro de Conexão
```
GET /viacep?cep=01310100
(API ViaCEP offline ou timeout)

Resposta 500:
{
    "sucesso": false,
    "erro": "Erro ao buscar CEP"
}

// Neste caso, recupera do cache local se existir
```

---

## Busca em Lote

Para buscar múltiplos CEPs em uma única requisição:

```javascript
const ceps = ['01310100', '20040020', '30140071'];

fetch('/viacep/multiplos', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ ceps: ceps })
})
.then(response => response.json())
.then(data => {
    // data.dados contém todos os resultados
    console.log(data.dados);
});
```

---

## Performance

### Primeira Requisição (sem cache)
- ~500ms a 2s (depende de latência com API)
- API ViaCEP consulta

### Requisições Seguintes (com cache)
- <10ms
- Dados retornados diretamente do banco

### Limpeza de Cache
```
POST /viacep/limpar-cache
(Remove CEPs com mais de 30 dias)
```

### Estatísticas
```
GET /viacep/stats

Resposta:
{
    "sucesso": true,
    "dados": {
        "total_ceps": 1250,
        "ultima_busca": "2026-02-06 18:30:15",
        "primeira_busca": "2026-01-01 10:00:00"
    }
}
```

---

##Códigos de Sucesso

| Status | Significado |
|--------|------------|
| 200 | CEP encontrado |
| 404 | CEP não encontrado |
| 400 | CEP formato inválido |
| 500 | Erro servidor/API offline |

---

## Exemplo Completo: Formulário com Auto-Preenchimento

```html
<!DOCTYPE html>
<html>
<head>
    <title>Cadastro Cliente</title>
    <style>
        .loading { display: none; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <form id="formCliente">
        <h2>Cadastro de Cliente</h2>

        <!-- CEP com busca automática -->
        <div>
            <label>CEP: 
                <input type="text" id="cep" placeholder="00000-000" required>
                <button type="button" id="btnBuscar">Buscar</button>
                <span class="loading" id="loading">Buscando...</span>
            </label>
            <div id="cepError"></div>
        </div>

        <!-- Endereço (auto-preenchido) -->
        <div>
            <label>Endereço:</label>
            <input type="text" id="endereco" readonly>
        </div>

        <!-- Número -->
        <div>
            <label>Número:</label>
            <input type="text" id="numero" required>
        </div>

        <!-- Bairro (auto-preenchido) -->
        <div>
            <label>Bairro:</label>
            <input type="text" id="bairro" readonly>
        </div>

        <!-- Cidade (auto-preenchido) -->
        <div>
            <label>Cidade:</label>
            <input type="text" id="cidade" readonly>
        </div>

        <!-- Estado (auto-preenchido) -->
        <div>
            <label>Estado:</label>
            <select id="estado" readonly>
                <option value="">-</option>
            </select>
        </div>

        <button type="submit">Salvar</button>
    </form>

    <script>
    const btnBuscar = document.getElementById('btnBuscar');
    const inputCEP = document.getElementById('cep');
    const loading = document.getElementById('loading');
    const cepError = document.getElementById('cepError');

    btnBuscar.addEventListener('click', async function() {
        const cep = inputCEP.value.replace(/\D/g, '');
        
        if (cep.length !== 8) {
            cepError.innerHTML = '<span class="error">CEP deve ter 8 dígitos</span>';
            return;
        }

        loading.style.display = 'inline';
        cepError.innerHTML = '';

        try {
            const response = await fetch(`/viacep?cep=${cep}`);
            const data = await response.json();

            loading.style.display = 'none';

            if (data.sucesso) {
                // Preencher campos
                document.getElementById('endereco').value = data.dados.endereco;
                document.getElementById('bairro').value = data.dados.bairro;
                document.getElementById('cidade').value = data.dados.cidade;
                document.getElementById('estado').value = data.dados.estado;
                
                cepError.innerHTML = '<span class="success">CEP encontrado!</span>';
            } else {
                cepError.innerHTML = `<span class="error">${data.erro}</span>`;
            }
        } catch (error) {
            loading.style.display = 'none';
            cepError.innerHTML = '<span class="error">Erro ao buscar CEP</span>';
            console.error(error);
        }
    });

    // Permitir busca ao pressionar Enter
    inputCEP.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            btnBuscar.click();
        }
    });

    // Formatar CEP enquanto digita
    inputCEP.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 5) {
            value = value.substring(0, 5) + '-' + value.substring(5);
        }
        e.target.value = value;
    });

    // Enviar formulário
    document.getElementById('formCliente').addEventListener('submit', function(e) {
        e.preventDefault();
        // Aqui você enviaria os dados para o servidor
        console.log('Formulário pronto para envio');
    });
    </script>
</body>
</html>
```

---

## Troubleshooting

### "CEP não encontrado" mas o CEP é válido
- Verifique se o CEP existe no Brasil
- Alguns CEPs muito novos podem não estar no banco ViaCEP
- Tente novamente em alguns minutos

### Busca muito lenta
- Primeira busca: Normal (consulta API)
- Próximas buscas: Devem ser rápidas (cache)
- Se continuar lento: Verifique conexão com internet

### Erro 500 ao buscar
- API ViaCEP pode estar momentaneamente offline
- Sistema tenta recuperar do cache local
- Se nada ajudar: Preencha endereço manualmente

---

## Resumo Integração

✅ API ViaCEP integrada  
✅ Cache local (30 dias)  
✅ Validação CPF/CNPJ  
✅ Formatação automática  
✅ Tratamento de erros  
✅ Busca em lote  

**Status:** Pronto para produção
