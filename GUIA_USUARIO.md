# 🎉 SISTEMA LAZER - GUIA COMPLETO

## ⚠️ RECEBEU ERRO 404?

Se você recebeu um erro "Não encontrado (404)" do Apache, é provável que:

1. **Você tentou acessar uma URL incorreta**
   - ❌ Errado: `http://localhost/SISTEMALAZER/api/clientes`
   - ✅ Certo: `http://localhost/SISTEMALAZER/api.php?rota=clientes`

2. **O .htaccess não está funcionando (mod_rewrite desativado)**
   - Verifique em: [http://localhost/SISTEMALAZER/roteamento_diagnostico.php](http://localhost/SISTEMALAZER/roteamento_diagnostico.php)

---

## 🚀 COMO USAR O SISTEMA

### 📍 Entrada Principal
```
🏠 Acesse: http://localhost/SISTEMALAZER/
```

Você verá uma página de boas-vindas com links rápidos para todas as seções.

---

## 📋 TODAS AS URLs DISPONÍVEIS

### 🎯 Páginas Principais
| URL | Descrição |
|-----|-----------|
| `/SISTEMALAZER/` | Página inicial (recomendado) |
| `/SISTEMALAZER/index.php` | Dashboard padrão |
| `/SISTEMALAZER/index.php?page=dashboard` | Dashboard com KPIs |
| `/SISTEMALAZER/index.php?page=clientes` | Gerenciar Clientes |
| `/SISTEMALAZER/index.php?page=cliente-novo` | Criar novo Cliente |
| `/SISTEMALAZER/index.php?page=orcamentos` | Gerenciar Orçamentos |

### 🔌 APIs (Requisições AJAX)
| URL | Método | Descrição | 
|-----|--------|-----------|
| `/api.php?rota=clientes` | GET | Listar clientes |
| `/api.php?rota=clientes&pagina=2` | GET | Listar com paginação |
| `/api.php?rota=clientes&nome=João` | GET | Filtrar por nome |
| `/api.php?rota=clientes` | POST | Criar cliente |
| `/api.php?rota=clientes&id=1` | PUT | Atualizar cliente |
| `/api.php?rota=clientes&id=1` | DELETE | Deletar cliente |
| `/api.php?rota=orcamentos` | GET | Listar orçamentos |
| `/api.php?rota=orcamentos&status=aberto` | GET | Filtrar por status |
| `/api.php?rota=viacep&cep=01310100` | GET | Buscar CEP |

### 🧪 Testes e Diagnóstico
| URL | Descrição |
|-----|-----------|
| `/SISTEMALAZER/test_api.php` | Testes interativos de API |
| `/SISTEMALAZER/roteamento_diagnostico.php` | Diagnóstico de roteamento |
| `/SISTEMALAZER/diagnostico.php` | Diagnóstico completo |
| `/SISTEMALAZER/test_paths.php` | Teste de caminho de arquivos |

---

## 🧪 COMO TESTAR O SISTEMA

### ✅ Teste 1: Verificar se tudo está carregando
```bash
Acesse: http://localhost/SISTEMALAZER/
```
Você deve ver a página de boas-vindas com o logo do Sistema Lazer.

### ✅ Teste 2: Ver dados de Clientes
```bash
Acesse: http://localhost/SISTEMALAZER/index.php?page=clientes
```
Você deve ver uma tabela com 5 clientes de exemplo carregando via AJAX.

### ✅ Teste 3: Testar API diretamente
```bash
Acesse: http://localhost/SISTEMALAZER/test_api.php
```
Clique em "Testar Todo o Sistema" para verificar se todas as rotas respondem.

### ✅ Teste 4: Filtrar Clientes
```
1. Na página de Clientes
2. Digite um nome no filtro (ex: "João")
3. Clique em "Filtrar" ou aguarde 500ms
4. A tabela deve atualizar com os resultados filtrados
```

### ✅ Teste 5: Criar um Novo Cliente
```
1. Clique em "Novo Cliente"
2. Selecione o tipo (Pessoa Física ou Jurídica)
3. Digite um CEP e clique "Buscar"
4. Veja o endereço sendo preenchido automaticamente
5. Clique "Salvar"
6. Você deve retornar à lista com a mensagem de sucesso
```

### ✅ Teste 6: Testar API via Console
```javascript
// Abra o DevTools (F12) e cole isso no Console:

// Listar clientes
fetch('/SISTEMALAZER/api.php?rota=clientes')
    .then(r => r.json())
    .then(data => console.log(data))

// Buscar CEP
fetch('/SISTEMALAZER/api.php?rota=viacep&cep=01310100')
    .then(r => r.json())
    .then(data => console.log(data))
```

---

## 📊 DADOS DE EXEMPLO

O sistema vem com dados simulados para testes:

### Clientes de Exemplo
```json
[
  {
    "id": 1,
    "nome": "João Silva",
    "tipo": "PF",
    "documento": "12345678901",
    "email": "joao@email.com",
    "telefone": "(11) 99999-9999"
  },
  {
    "id": 2,
    "nome": "Empresa ABC",
    "tipo": "PJ",
    "documento": "12345678901234",
    "email": "contato@abc.com",
    "telefone": "(11) 98888-8888"
  }
  // ... mais clientes
]
```

### Orçamentos de Exemplo
```json
[
  {
    "id": 1,
    "numero": "ORC-2026-0001",
    "cliente": "João Silva",
    "valor_total": 1500.00,
    "status": "aberto"
  },
  // ... mais orçamentos
]
```

---

## 🔧 SOLUÇÃO DE PROBLEMAS

### Problema: Recebo 404 em qualquer página
**Solução:**
1. Abra: http://localhost/SISTEMALAZER/roteamento_diagnostico.php
2. Siga as instruções de diagnóstico
3. Se mod_rewrite não está ativado:
   - Abra `c:\wamp64\bin\apache\apache2.4.x\conf\httpd.conf`
   - Procure por `#LoadModule rewrite_module modules/mod_rewrite.so`
   - Remova o `#` do início
   - Reinicie o Apache

### Problema: Clientes não carregam na tabela
**Solução:**
1. Abra DevTools (F12)
2. Vá para a aba "Network"
3. Clique em "Filtrar"
4. Procure por requisições para `api.php?rota=clientes`
5. Verifique se a resposta é 200 OK

### Problema: CEP não autocompleta
**Solução:**
- Verifique se a API está respondendo:
- Abra: http://localhost/SISTEMALAZER/api.php?rota=viacep&cep=01310100
- Você deve ver JSON com dados de endereço

---

## 📁 ESTRUTURA DO PROJETO

```
SISTEMALAZER/
├── 📄 index.html                  (Página inicial)
├── 📄 index.php                   (Router das views)
├── 📄 api.php                     (API Gateway)
├── 📄 .htaccess                   (Reescrita de URLs)
│
├── 📂 app/
│   ├── models/                    (Classes de BD)
│   ├── controllers/               (Lógica de negócio)
│   └── views/                     (Templates HTML)
│
├── 📂 public/
│   ├── css/                       (Estilos)
│   └── js/                        (JavaScript)
│
└── 📂 tests/                      (Testes unitários)
```

---

## 🔍 LOGS e DEBUGGING

Se encontrar problemas, verifique:

1. **Verificar erro no navegador:**
   - Abra DevTools (F12)
   - Aba "Console" para ver erros JavaScript
   - Aba "Network" para ver status HTTP

2. **Verificar erro do servidor:**
   - Arquivo de log do Apache: `c:\wamp64\logs\apache_error.log`
   - Arquivo de log do PHP: `c:\wamp64\logs\php_error.log`

3. **Teste de conectividade:**
   ```
   curl -I http://localhost/SISTEMALAZER/
   ```

---

## 📞 QUESTÕES FREQUENTES

**P: Por que o erro 404 aparece?**
R: Geralmente é porque você tentou acessar uma URL que não existe. Use `/index.php?page=...` para páginas e `/api.php?rota=...` para APIs.

**P: Os dados são reais ou simulados?**
R: Atualmente são simulados. Na ETAPA 5, serão integrados com banco de dados real.

**P: Posso testar DELETE e PUT?**
R: Sim! Use o `test_api.php` ou o Console do navegador. Os dados são removidos apenas da memória (não do BD).

**P: Como autenticar?**
R: Ainda não há sistema de login. Será implementado na ETAPA 5.

---

## ✨ CHECKLIST DE INICIAÇÃO

- [ ] Acesso http://localhost/SISTEMALAZER/
- [ ] Vir a página inicial com botão "Ir para o Dashboard"
- [ ] Clicar em "Clientes" e ver tabela com dados
- [ ] Usar filtro para buscar clientes
- [ ] Clicar em "Novo Cliente" e preencher formulário
- [ ] Testar busca de CEP
- [ ] Clicar em "Orçamentos" e ver lista
- [ ] Abrir test_api.php e rodar testes

Se completou todos, o sistema está funcionando! ✅

---

## 🎓 TECNOLOGIAS UTILIZADAS

- **Frontend**: HTML5 + Bootstrap 5.3 + Vanilla JS
- **Backend**: PHP 8.3
- **Servidor**: Apache 2.4
- **Database**: MySQL (futuro)
- **Gráficos**: Chart.js
- **Icons**: Font Awesome 6

---

**Desenvolvido com ❤️**  
Última atualização: 6 de fevereiro de 2026  
Status: ✅ Operacional (ETAPA 4)
