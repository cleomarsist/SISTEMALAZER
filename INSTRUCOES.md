# 📘 GUIA COMPLETO DE USO - ERP Fênix Magazine Personalizados

**Última atualização**: 06/02/2026  
**Versão**: 1.0 (Em Desenvolvimento)  
**Status**: ✅ Fase Base Completa | 🔄 Endpoints REST em Desenvolvimento

---

## 📑 ÍNDICE

1. [Instalação e Setup](#instalação-e-setup)
2. [Primeiro Acesso](#primeiro-acesso)
3. [Estrutura do Projeto](#estrutura-do-projeto)
4. [Como Usar as Funcionalidades](#como-usar-as-funcionalidades)
5. [Endpoints da API](#endpoints-da-api)
6. [Banco de Dados](#banco-de-dados)
7. [Troubleshooting](#troubleshooting)
8. [Roadmap de Desenvolvimento](#roadmap-de-desenvolvimento)

---

## 🚀 INSTALAÇÃO E SETUP

### Pré-requisitos:
- ✅ WAMP/XAMPP instalado
- ✅ PHP 7.4+
- ✅ MySQL 5.7+
- ✅ Navegador moderno (Chrome, Firefox, Edge)

### Passo 1: Preparar o Banco de Dados

**📝 INSTRUÇÕES MANUAIS:**

1. Abra seu navegador
2. Acesse: `http://localhost/phpmyadmin`
3. Faça login com seus dados (geralmente user: `root`, senha vazia)
4. No painel esquerdo, clique em "Nova" (ou "New")
5. Digite o nome do banco: `fenix_magazine`
6. Clique em "Criar"
7. Agora clique na aba **SQL**
8. Copie TODO o conteúdo do arquivo: `c:\wamp64\www\SISTEMAIA\ControleInvestimento\db\setup_complete.sql`
9. Cole no campo SQL do phpMyAdmin
10. Clique no botão **Executar**
11. Aguarde a conclusão (deve aparecer mensagem verde: "0 linhas afetadas")

**✅ Banco de dados criado com sucesso!**

---

### Passo 2: Configurar a Conexão (se necessário alterar)

**📁 INSTRUÇÕES MANUAIS:**

1. Abra o arquivo: `c:\wamp64\www\SISTEMAIA\ControleInvestimento\config\config.php`
2. Verifique as credenciais:
   ```php
   define('DB_HOST', 'localhost');    // Host (geralmente localhost)
   define('DB_USER', 'root');         // Usuário MySQL
   define('DB_PASS', '');             // Senha MySQL (vazio para padrão)
   define('DB_NAME', 'fenix_magazine'); // Nome do banco
   ```
3. Se suas credenciais forem diferentes, altere aqui
4. Salve o arquivo (Ctrl+S)

---

## 🔑 PRIMEIRO ACESSO

### Acessar o Sistema

**🌐 INSTRUÇÕES MANUAIS:**

1. Abra seu navegador
2. Acesse: `http://localhost/SISTEMAIA/ControleInvestimento/`
3. Será redirecionado para a tela de login
4. Digite as credenciais padrão:
   ```
   Usuário: admin
   Senha: Senha123
   ```
5. Clique em "Entrar"
6. Será redirecionado para o dashboard

### Usuários Padrão do Sistema:

| Usuário | Senha | Perfil | Acesso |
|---------|-------|--------|--------|
| admin | Senha123 | Administrador | Total |
| gerente | Senha123 | Gerente | Vendas, Financeiro, Relatórios |
| vendedor | Senha123 | Vendedor | Criar orçamentos e pedidos |
| usuario | Senha123 | Usuário | Visualizar dados |

**⚠️ IMPORTANTE**: Altere todas as senhas após primeiro acesso em produção!

---

## 📁 ESTRUTURA DO PROJETO

```
ControleInvestimento/
│
├── 📂 api/                          # Endpoints REST (JSON)
│   ├── login.php                    # Login do usuário
│   ├── logout.php                   # Logoff
│   ├── clients.php                  # CRUD Clientes (Em desenvolvimento)
│   ├── products.php                 # CRUD Produtos (Em desenvolvimento)
│   ├── orders.php                   # CRUD Pedidos (Em desenvolvimento)
│   └── ... (mais endpoints)
│
├── 📂 assets/                       # Arquivos estáticos
│   ├── style.css                    # CSS base
│   ├── dashboard.css                # CSS dashboard
│   ├── login.js                     # JavaScript login
│   ├── dashboard.js                 # JavaScript dashboard
│   └── ... (mais JS/CSS)
│
├── 📂 config/                       # Configurações
│   └── config.php                   # Credenciais do banco
│
├── 📂 controllers/                  # Lógica de negócio
│   ├── UserController.php           # Autenticação
│   ├── ClientController.php         # Gestão de clientes
│   ├── ProductController.php        # Gestão de produtos
│   ├── OrderController.php          # Gestão de pedidos
│   ├── BudgetController.php         # Gestão de orçamentos
│   ├── AccountsController.php       # Contas a receber/pagar
│   └── ... (mais controllers)
│
├── 📂 db/                           # Banco de dados
│   ├── connection.php               # Conexão PDO
│   ├── erp_schema.sql               # Schema base
│   ├── setup_complete.sql           # Setup completo em um arquivo
│   ├── MIGRATION_GUIDE.md           # Guia de migrações
│   └── 📂 migrations/
│       ├── 001_insert_initial_data.sql    # Dados iniciais
│       ├── 002_maintenance_updates.sql    # Índices e views
│       └── 003_backup_and_cleanup.sql     # Backup e reports
│
├── 📂 models/                       # Modelos (Entidades)
│   ├── User.php                     # Usuário
│   ├── Client.php                   # Cliente
│   ├── Product.php                  # Produto
│   ├── Material.php                 # Material
│   ├── Order.php                    # Pedido
│   ├── Budget.php                   # Orçamento
│   ├── Simulation.php               # Simulação
│   ├── AccountsReceivable.php       # Contas a receber
│   ├── AccountsPayable.php          # Contas a pagar
│   ├── Audit.php                    # Auditoria
│   └── ... (mais models)
│
├── 📂 session/                      # Controle de sessões
│   └── session.php                  # Funções de sessão
│
├── 📂 utils/                        # Utilitários
│   ├── Auth.php                     # Autenticação e autorização
│   ├── Validator.php                # Validação e sanitização
│   ├── Response.php                 # Respostas JSON padrão
│   └── ... (mais utilitários)
│
├── 📂 views/                        # Interfaces HTML
│   ├── login.html                   # Tela de login
│   ├── dashboard.html               # Dashboard principal
│   ├── client_form.html             # Formulário de cliente
│   ├── product_form.html            # Formulário de produto
│   ├── material_form.html           # Formulário de material
│   └── ... (mais views)
│
├── index.php                        # Ponto de entrada
├── README.md                        # Documentação técnica
├── .gitignore                       # Arquivos ignorados no Git
└── INSTRUCOES.md                    # Este arquivo (Guia de uso)
```

---

## 💡 COMO USAR AS FUNCIONALIDADES

### 1. LOGIN E AUTENTICAÇÃO

**Fluxo:**
1. Acesse: `http://localhost/SISTEMAIA/ControleInvestimento/views/login.html`
2. Digite usuário e senha
3. Sistema autentica via `/api/login.php`
4. Se sucesso, redireciona para dashboard
5. Se erro, exibe mensagem de erro

**Segurança:**
- ✅ Senhas com hash bcrypt
- ✅ Sessão PHP segura
- ✅ Validação no servidor
- ✅ Proteção contra SQL Injection

---

### 2. DASHBOARD

**O que você vê:**
- ✅ Menu lateral com todas as funcionalidades
- ✅ Cards com resumo (pedidos, orçamentos, contas)
- ✅ Data e hora atualizadas
- ✅ Nome do usuário logado
- ✅ Botão de Logout

**📝 INSTRUÇÕES PARA TESTAR:**

1. Faça login com: `admin` / `Senha123`
2. Verifique se o nome "admin" aparece no canto superior direito
3. Clique em "Dashboard" no menu para voltar
4. Clique em "Sair" para fazer logout

---

### 3. CADASTRO DE CLIENTES (Em Desenvolvimento)

**Quando estiver pronto:**

**📝 INSTRUÇÕES DE USO:**

1. Clique em "Clientes" no menu lateral
2. Clique em "Novo Cliente"
3. Preencha os dados:
   - Nome (obrigatório)
   - CPF/CNPJ (obrigatório, único)
   - Email (opcional)
   - Telefone (opcional)
   - Endereço (opcional)
   - Tipo: Cliente ou Fornecedor
4. Clique em "Salvar"
5. Cliente será adicionado ao banco de dados

---

### 4. CADASTRO DE PRODUTOS (Em Desenvolvimento)

**Quando estiver pronto:**

**📝 INSTRUÇÕES DE USO:**

1. Clique em "Produtos" no menu lateral
2. Clique em "Novo Produto"
3. Preencha os dados:
   - Nome (obrigatório)
   - Descrição (opcional)
   - Unidade (obrigatório): placa, serviço, unidade, kit
   - Preço (obrigatório)
4. Clique em "Salvar"
5. Produto será adicionado ao banco

---

### 5. CADASTRO DE MATERIAIS (Em Desenvolvimento)

**Quando estiver pronto:**

**📝 INSTRUÇÕES DE USO:**

1. Clique em "Materiais" no menu lateral
2. Clique em "Novo Material"
3. Preencha os dados:
   - Nome (obrigatório)
   - Tipo: Chapa ou Insumo
   - Unidade (obrigatório): placa, litro, frasco, etc
   - Estoque atual
   - Estoque mínimo (para alertas)
   - Custo unitário
4. Clique em "Salvar"
5. Material adicionado ao estoque

---

### 6. CRIAR PEDIDO (Em Desenvolvimento)

**Quando estiver pronto:**

**📝 INSTRUÇÕES DE USO:**

1. Clique em "Pedidos" no menu lateral
2. Clique em "Novo Pedido"
3. Busque o cliente
4. Selecione produtos
5. Sistema calcula automaticamente:
   - Custo dos materiais
   - Custo fixo proporcional
   - Margem de lucro
   - Preço final
6. Clique em "Confirmar Pedido"
7. Pedido é criado com status "aberto"
8. Pode ser alterado para: em_producao, finalizado ou cancelado

---

### 7. CRIAR ORÇAMENTO (Em Desenvolvimento)

**Quando estiver pronto:**

**📝 INSTRUÇÕES DE USO:**

1. Clique em "Orçamentos" no menu lateral
2. Clique em "Novo Orçamento"
3. Busque cliente
4. Faça uma simulação de produtos
5. Sistema calcula com margens
6. Pode adicionar desconto
7. Clique em "Gerar Orçamento"
8. Orçamento gerado com status "aberto"
9. Cliente pode: aprovar, rejeitar ou pedir alterações
10. Se aprovado, pode converter para pedido

---

## 🔗 ENDPOINTS DA API

### Status: 🟢 Pronto | 🟡 Em Desenvolvimento | 🔴 Não Iniciado

### AUTENTICAÇÃO

#### 🟢 Login
```
POST /api/login.php
Content-Type: application/json

{
  "username": "admin",
  "password": "Senha123"
}

Resposta (sucesso):
{
  "status": "success",
  "message": "Login realizado com sucesso",
  "data": {
    "user_id": 1,
    "username": "admin",
    "role": "admin"
  }
}
```

#### 🟢 Logout
```
POST /api/logout.php

Resposta (sucesso):
{
  "status": "success",
  "message": "Logout realizado com sucesso"
}
```

---

### CLIENTES 🟡 (Em Desenvolvimento)

#### Listar Clientes
```
GET /api/clients.php?type=cliente

Filtros opcionais:
- ?type=cliente      (apenas clientes)
- ?type=fornecedor   (apenas fornecedores)
```

#### Criar Cliente
```
POST /api/clients.php
Content-Type: application/json

{
  "name": "Empresa XYZ",
  "document": "12345678000191",
  "email": "contato@xyz.com",
  "phone": "11999999999",
  "address": "Rua X, 100",
  "type": "cliente"
}
```

#### Obter Cliente por ID
```
GET /api/clients.php?id=1
```

---

### PRODUTOS 🟡 (Em Desenvolvimento)

#### Listar Produtos
```
GET /api/products.php
```

#### Criar Produto
```
POST /api/products.php
Content-Type: application/json

{
  "name": "Placa Acrílico",
  "description": "3mm transparente",
  "unit": "placa",
  "price": 85.00
}
```

---

### PEDIDOS 🟡 (Em Desenvolvimento)

#### Listar Pedidos
```
GET /api/orders.php?status=aberto

Filtros opcionais:
- ?status=aberto
- ?status=em_producao
- ?status=finalizado
- ?status=cancelado
- ?client_id=1
```

#### Criar Pedido
```
POST /api/orders.php
Content-Type: application/json

{
  "budget_id": 1,
  "client_id": 1,
  "user_id": 1,
  "total": 1500.00
}
```

#### Atualizar Status
```
PUT /api/orders.php?id=1&status=finalizado
```

---

### MAIS ENDPOINTS 🔴 (Não Iniciados)

- [ ] `/api/budgets.php` - Orçamentos
- [ ] `/api/simulations.php` - Simulações
- [ ] `/api/materials.php` - Materiais
- [ ] `/api/accounts.php` - Contas a receber/pagar
- [ ] `/api/reports.php` - Relatórios

---

## 💾 BANCO DE DADOS

### Ver Dados Inseridos

**📝 INSTRUÇÕES MANUAIS:**

1. Abra phpMyAdmin: `http://localhost/phpmyadmin`
2. Selecione banco: `fenix_magazine`
3. Na aba "SQL", execute:

```sql
-- Ver usuários
SELECT username, role FROM users;

-- Ver clientes
SELECT name, type FROM clients;

-- Ver produtos
SELECT name, price FROM products;

-- Ver estoque
SELECT name, stock, min_stock FROM materials;

-- Ver pedidos abertos
SELECT o.id, c.name, o.total, o.status FROM orders o 
JOIN clients c ON o.client_id = c.id 
WHERE o.status = 'aberto';

-- Ver contas a receber
SELECT ar.id, c.name, ar.value, ar.status FROM accounts_receivable ar
JOIN clients c ON ar.client_id = c.id
WHERE ar.status IN ('aberto', 'atrasado');
```

### Backup do Banco

**📝 INSTRUÇÕES MANUAIS:**

1. Abra phpMyAdmin
2. Selecione banco: `fenix_magazine`
3. Clique em "Exportar"
4. Formato: SQL
5. Clique em "Executar"
6. Arquivo será baixado

---

## 🐛 TROUBLESHOOTING

### Erro: "Nenhum banco de dados foi selecionado"

**Solução:**
1. Verifique se executou o `setup_complete.sql`
2. Abra phpMyAdmin
3. Verifique se existe banco: `fenix_magazine`
4. Se não existir, execute o SQL novamente

---

### Erro: "Conexão recusada" ou "Cannot connect to MySQL"

**Solução:**
1. Verifique se MySQL está rodando
2. No WAMP/XAMPP, clique no ícone e selecione "Restart All Services"
3. Aguarde até ficar verde
4. Tente novamente

---

### Erro: "Usuário ou senha inválidos"

**Solução:**
1. Verifique se digitou corretamente:
   - Usuário: `admin` (case-sensitive)
   - Senha: `Senha123`
2. Se tiver alterado credenciais, verifique em `/config/config.php`
3. Se dados do banco foram apagados, execute setup novamente

---

### Sistema não redireciona do login para dashboard

**Solução:**
1. Abra o console do navegador (F12)
2. Verifique se há erros em vermelho
3. Verifique a aba "Network" para requisições falhadas
4. Pode ser problema de caminho relativo do arquivo JavaScript

---

### Estoque não atualiza após criar pedido

**Solução (quando funcionalidade estiver pronta):**
1. O sistema não atualiza estoque automaticamente ainda
2. Será desenvolvido na próxima fase
3. Por enquanto, atualize manualmente em Materiais

---

### Erro: "Erro na conexão com o servidor" ou "PDOException"

**Causas e Soluções:**

1. **WAMP/MySQL não está rodando**
   - Procure o ícone WAMP na bandeja (canto inferior direito)
   - Se estiver vermelho ❌: clique e selecione "Start All Services"
   - Aguarde alguns segundos até ficar verde ✅
   - Tente novamente

2. **Arquivo `test_connection.php` para diagnosticar**
   - Acesse: `http://localhost/SISTEMAIA/ControleInvestimento/test_connection.php`
   - Este arquivo mostrará exatamente qual é o problema
   - Compartilhe o resultado para suporte

3. **Banco de dados não foi criado**
   - Se test_connection.php disser "Unknown database 'fenix_magazine'"
   - Execute setup novamente (veja Passo 1 em "Instalação e Setup")

4. **Configuração do banco incorreta**
   - Abra: `c:\wamp64\www\SISTEMAIA\ControleInvestimento\config\config.php`
   - Verifique as 4 constantes:
     ```php
     define('DB_HOST', 'localhost');     // seu host
     define('DB_USER', 'root');          // seu usuário MySQL
     define('DB_PASS', '');              // sua senha MySQL
     define('DB_NAME', 'fenix_magazine'); // seu banco
     ```
   - Se não souber, deixe como está (padrão WAMP)

5. **PDO não está instalado**
   - Se test_connection.php disser "PDO not loaded"
   - Seu PHP não tem PDO ativado
   - Procure WAMP → PHP Settings → PHP Extensions
   - Marque a opção `pdo_mysql`

---

## 🗺️ ROADMAP DE DESENVOLVIMENTO

### ✅ FASE 1 - CONCLUÍDA (Até 06/02/2026)

- ✅ Estrutura base do projeto
- ✅ Banco de dados completo
- ✅ Models e Controllers básicos
- ✅ Sistema de autenticação
- ✅ Dashboard inicial
- ✅ Segurança: Auth, Validator, Response

### ✅ FASE 2 - BANCO DE DADOS DOCUMENTADO (Completa)

**Novo arquivo**: `db/DATABASE_DESIGN.md`

**O que foi documentado:**
- ✅ Design de cada tabela com justificativa
- ✅ Explicação de normalização
- ✅ Índices de performance (quais e por quê)
- ✅ Relacionamentos entre tabelas
- ✅ Views para análises
- ✅ Stored Procedures
- ✅ Triggers de auditoria
- ✅ Exemplos de dados

**Como estudar:**
- Leia: `db/DATABASE_DESIGN.md` (guia completo de design)
- Execute: `db/setup_complete.sql` (todo o banco em um arquivo)
- Veja: `db/migrations/` (scripts separados por fase)

---

### 🔄 FASE 3 - ENDPOINTS REST (Próxima)

- [ ] API de Clientes (CRUD completo)
- [ ] API de Produtos (CRUD completo)
- [ ] API de Materiais
- [ ] API de Pedidos
- [ ] API de Orçamentos
- [ ] Validação JSON completa

**Prazo estimado**: 1-2 semanas

### 🔄 FASE 3 - VIEWS AVANÇADAS

- [ ] Formulário de Clientes (criar/editar)
- [ ] Formulário de Produtos (com imagens)
- [ ] Formulário de Pedidos (com cálculo automático)
- [ ] Tabelas com listagem e filtros
- [ ] Busca e paginação

**Prazo estimado**: 1-2 semanas

### 🔄 FASE 4 - CÁLCULO E SIMULAÇÃO

- [ ] Simulador de preços (custo + margem)
- [ ] Cálculo automático de materiais
- [ ] Conversão Simulação → Orçamento → Pedido
- [ ] Controle de estoque (entrada/saída)

**Prazo estimado**: 2-3 semanas

### 🔄 FASE 5 - FINANCEIRO

- [ ] Registro de Contas a Receber
- [ ] Registro de Contas a Pagar
- [ ] Fluxo de Caixa
- [ ] Movimentação de Crédito
- [ ] Alertas de vencimento

**Prazo estimado**: 1-2 semanas

### 🔄 FASE 6 - RELATÓRIOS

- [ ] Relatório de Vendas
- [ ] Relatório de Estoque
- [ ] Relatório Financeiro
- [ ] Gráficos (vendas, clientes, lucro)
- [ ] Exportação PDF/Excel

**Prazo estimado**: 1-2 semanas

### 🔄 FASE 7 - OTIMIZAÇÕES

- [ ] Performance (cache, query optimization)
- [ ] Interface responsiva (mobile)
- [ ] Temas e customizações
- [ ] Exportação/Importação dados
- [ ] APIs externas (correios, pagamento)

**Prazo estimado**: Contínuo

---

## 📞 SUPORTE

**Dúvidas sobre:**
- **Instalação**: Verifique a seção [Instalação e Setup](#instalação-e-setup)
- **Uso do sistema**: Verifique [Como Usar as Funcionalidades](#como-usar-as-funcionalidades)
- **Erros**: Verifique [Troubleshooting](#troubleshooting)
- **API**: Verifique [Endpoints da API](#endpoints-da-api)
- **Arquitetura**: Veja `README.md` na raiz do projeto

---

## 📝 HISTÓRICO DE ATUALIZAÇÕES

| Data | Versão | Alterações |
|------|--------|-----------|
| 06/02/2026 | 1.0 | Documento inicial criado com fase 1 completa |
| TBD | 1.1 | Endpoints REST (Fase 2) |
| TBD | 1.2 | Views Avançadas (Fase 3) |
| TBD | 1.3 | Simulação e Cálculo (Fase 4) |
| TBD | 1.4 | Financeiro (Fase 5) |
| TBD | 1.5 | Relatórios (Fase 6) |

---

**Desenvolvido com PHP puro, MySQL e JavaScript vanilla**  
**ERP Fênix Magazine Personalizados - Sistema robusto, seguro e escalável**

