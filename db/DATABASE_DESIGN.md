# 📊 DESIGN DO BANCO DE DADOS - ERP Fênix Magazine Personalizados

**Versão**: 1.0  
**Data**: 06/02/2026  
**Autor**: Arquiteto Mestre de Sistemas  
**Status**: ✅ Completo e Documentado

---

## 📋 ÍNDICE

1. [Visão Geral da Arquitetura](#visão-geral-da-arquitetura)
2. [Princípios de Design](#princípios-de-design)
3. [Diagrama de Relacionamentos](#diagrama-de-relacionamentos)
4. [Tabelas e Campos](#tabelas-e-campos)
5. [Índices de Performance](#índices-de-performance)
6. [Views para Análise](#views-para-análise)
7. [Stored Procedures](#stored-procedures)
8. [Triggers de Auditoria](#triggers-de-auditoria)

---

## 🎯 VISÃO GERAL DA ARQUITETURA

### Objetivos do Design:

1. **Normalização**: Tabelas normalizadas em BCNF (3ª Forma Normal)
2. **Performance**: Índices estratégicos em chaves de busca frequente
3. **Auditoria**: Rastreabilidade completa de ações
4. **Escalabilidade**: Preparado para crescimento
5. **Integridade**: Chaves estrangeiras com constraint
6. **Flexibilidade**: ENUMs para valores limitados, TEXT para extensibilidade

### Modelo Conceitual:

```
┌─────────────────────────────────────────────────────────────┐
│                      CLIENTES/FORNECEDORES                  │
│                      (Relacionamento Principal)              │
└────────────────┬────────────────────────────────────┬────────┘
                 │                                    │
                 ▼                                    ▼
        ┌─────────────────┐                  ┌──────────────┐
        │  PEDIDOS        │                  │ FORNECEDORES │
        │  ORÇAMENTOS     │                  │ (FORNECIMENTO)
        │  SIMULAÇÕES     │
        └────────┬────────┘
                 │
      ┌──────────┴───────────┬───────────────┐
      ▼                      ▼               ▼
  ┌─────────┐          ┌──────────┐    ┌────────────┐
  │ PRODUTOS │          │ MATERIAIS│    │ CONTAS A   │
  │ (Venda) │          │(Estoque) │    │ RECEBER    │
  └────┬────┘          └────┬─────┘    └────────────┘
       │                    │
  ┌────┴────────────────────┴──────┐
  │  CONSUMO DE MATERIAIS POR      │
  │  PRODUTO (Relação Many-to-Many)│
  └────────────────────────────────┘
```

---

## 🏗️ PRINCÍPIOS DE DESIGN

### 1. Normalização de Dados

**Por que normalizar?**
- ✅ Reduz redundância
- ✅ Facilita manutenção
- ✅ Melhora consistência
- ✅ Economiza espaço

**Exemplo - Tabela `clients`:**
```sql
-- ❌ NÃO FAZER (desnormalizado):
CREATE TABLE clientes (
    id INT,
    nome VARCHAR(100),
    endereço_rua VARCHAR(100),
    endereço_numero INT,
    endereço_cidade VARCHAR(50),
    endereço_estado CHAR(2),
    endereço_cep VARCHAR(8)
);

-- ✅ FAZER (normalizado):
CREATE TABLE clients (
    id INT,
    name VARCHAR(100),
    address TEXT
);
-- Endereço como um campo único
```

### 2. Chaves Estrangeiras

**Como funciona:**
```sql
tabela_filha.fk_id → tabela_pai.id

Exemplo: orders.client_id → clients.id
↓
Um cliente (id=1) pode ter MUITOS pedidos
Um pedido só pode pertencer a UM cliente
```

### 3. Índices Estratégicos

**Quando criar índice?**
- ✅ Chaves primárias (automático)
- ✅ Chaves estrangeiras (busca frequente)
- ✅ Colunas em WHERE (filtros)
- ✅ Colunas em ORDER BY (ordenação)
- ✅ Colunas em JOIN (relacionamentos)

**Quando NOT criar?**
- ❌ Colunas com poucos valores únicos (BOOLEAN)
- ❌ Colunas com texto longo (full-text search melhor)
- ❌ Coluna raramente consultada

### 4. ENUMs vs Tabelas Lookup

**Usar ENUM quando:**
- Valores são FIXOS e imutáveis (ex: status)
- Poucos valores (max 5-10)
- Usado em queries frequentes

```sql
-- ✅ BOM: Valores fixos e poucos
status ENUM('aberto','pago','atrasado')

type ENUM('chapa','insumo')

role ENUM('admin','gerente','vendedor','usuario')
```

**Usar tabela lookup quando:**
- Valores podem mudar
- Muitos valores (> 20)
- Precisa de mais informações

```sql
-- ✅ BOM: Muitos valores, podem mudar
CREATE TABLE status_pedido (
    id INT PRIMARY KEY,
    nome VARCHAR(50)
);
```

---

## 🔗 DIAGRAMA DE RELACIONAMENTOS

### Entidades Principais:

```
USERS
 ├─ 1:N → ORDERS (user_id)
 ├─ 1:N → BUDGETS (user_id)
 ├─ 1:N → SIMULATIONS (user_id)
 └─ 1:N → AUDIT_HISTORY (user_id)

CLIENTS
 ├─ 1:N → ORDERS (client_id)
 ├─ 1:N → BUDGETS (client_id)
 ├─ 1:N → SIMULATIONS (client_id)
 ├─ 1:N → ACCOUNTS_RECEIVABLE (client_id)
 ├─ 1:N → ACCOUNTS_PAYABLE (supplier_id)
 └─ 1:N → CREDIT_MOVEMENTS (client_id)

PRODUCTS
 ├─ N:M → PRODUCT_KITS (via KIT_ITEMS)
 └─ N:M → MATERIALS (via PRODUCT_MATERIALS)

PRODUCT_KITS
 └─ 1:N → KIT_ITEMS

PRODUCT_MATERIALS
 └─ 1:N → MATERIALS

ORDERS
 ├─ 1:N → ACCOUNTS_RECEIVABLE (order_id)
 └─ FK: BUDGET_ID, CLIENT_ID, USER_ID

BUDGETS
 ├─ FK: SIMULATION_ID, CLIENT_ID, USER_ID
 └─ n:1 ← ORDERS (budget_id)

SIMULATIONS
 └─ FK: CLIENT_ID, USER_ID
```

---

## 📋 TABELAS E CAMPOS

### 1. TABELA: `users` (Usuários do Sistema)

**Propósito**: Armazenar usuários com autenticação

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador único |
| username | VARCHAR(50) | NOT NULL, UNIQUE | Login único do usuário |
| password | VARCHAR(255) | NOT NULL | Hash bcrypt da senha |
| role | VARCHAR(30) | NOT NULL | Perfil: admin, gerente, vendedor, usuario |
| created_at | DATETIME | DEFAULT NOW() | Data de criação |

**Por quê esse design?**
- ✅ UNIQUE em username para evitar duplicatas
- ✅ 255 caracteres em password para bcrypt (que gera ~60 chars)
- ✅ VARCHAR(30) em role permite adicionar novos perfis sem ALTER TABLE
- ✅ created_at para auditoria

**Índices**:
```sql
INDEX idx_users_username (username)  -- Busca por login frequente
INDEX idx_users_role (role)          -- Filtro por perfil
```

**Relacionamentos**:
- orders.user_id → users.id
- budgets.user_id → users.id
- audit_history.user_id → users.id

---

### 2. TABELA: `clients` (Clientes e Fornecedores)

**Propósito**: Armazenar informações de clientes e fornecedores

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| name | VARCHAR(100) | NOT NULL | Nome da empresa/pessoa |
| document | VARCHAR(20) | NOT NULL, UNIQUE | CPF/CNPJ (formatado ou não) |
| email | VARCHAR(100) | NULL | Email de contato |
| phone | VARCHAR(20) | NULL | Telefone (com ou sem máscara) |
| address | TEXT | NULL | Endereço completo |
| type | ENUM | NOT NULL | 'cliente' ou 'fornecedor' |
| created_at | DATETIME | DEFAULT NOW() | Data criação |
| updated_at | DATETIME | ON UPDATE NOW() | Última alteração |

**Por quê esse design?**
- ✅ UNIQUE em document para evitar CPF/CNPJ duplicado
- ✅ VARCHAR(20) em document (max: "00.000.000/0000-00" = 18 chars)
- ✅ TEXT em address (endereço pode ser longo)
- ✅ ENUM em type para valores fixos
- ✅ updated_at para rastrear mudanças

**Índices**:
```sql
INDEX idx_clients_type (type)           -- Filtro cliente/fornecedor
INDEX idx_clients_document (document)   -- Busca por CPF/CNPJ
INDEX idx_clients_name (name)           -- Busca por nome
```

**Relacionamentos**:
- orders.client_id → clients.id
- budgets.client_id → clients.id
- accounts_receivable.client_id → clients.id
- accounts_payable.supplier_id → clients.id (mesmo tipo)

---

### 3. TABELA: `products` (Produtos Simples)

**Propósito**: Produtos que podem ser vendidos isoladamente

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| name | VARCHAR(100) | NOT NULL | Nome do produto |
| description | TEXT | NULL | Descrição longa |
| unit | VARCHAR(10) | NOT NULL | Unidade: placa, serviço, kit, etc |
| price | DECIMAL(10,2) | NOT NULL | Preço de venda |
| is_active | BOOLEAN | DEFAULT 1 | Produto ativo ou inativo |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Por quê esse design?**
- ✅ DECIMAL(10,2) para preço (até 99.999.999,99)
- ✅ VARCHAR(10) em unit para flexibilidade
- ✅ is_active para soft-delete (não perder histórico)
- ✅ TEXT em description para textos longos

**Índices**:
```sql
INDEX idx_products_name (name)      -- Busca por nome
INDEX idx_products_is_active (is_active)  -- Filtrar produtos ativos
```

**Relacionamentos**:
- product_materials.product_id → products.id
- kit_items.product_id → products.id

---

### 4. TABELA: `product_kits` (Kits de Produtos)

**Propósito**: Combinar múltiplos produtos em uma oferta (ex: Kit Iniciante)

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| name | VARCHAR(100) | NOT NULL | Nome do kit |
| description | TEXT | NULL | Descrição do kit |
| price | DECIMAL(10,2) | NOT NULL | Preço final do kit |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Por quê esse design?**
- ✅ Tabela separada de products (são diferentes)
- ✅ price é o preço final (com desconto se houver)
- ✅ Relacionamento many-to-many via kit_items

**Relacionamentos**:
- kit_items.kit_id → product_kits.id
- Via kit_items, um kit tem MUITOS produtos

---

### 5. TABELA: `kit_items` (Itens do Kit)

**Propósito**: Relacionamento many-to-many entre kits e produtos

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| kit_id | INT | FK, PK | Referência ao kit |
| product_id | INT | FK, PK | Referência ao produto |
| quantity | DECIMAL(10,2) | NOT NULL | Quantidade no kit |

**Por quê esse design?**
- ✅ PK composta (kit_id + product_id) = um produto por kit
- ✅ CASCADE DELETE (se deletar kit, deleta items)
- ✅ DECIMAL em quantity (pode ser fracionado)

**Exemplo de dados**:
```sql
Kit Iniciante (id=1):
  - Placa Acrílico (id=1): 2 unidades
  - Placa MDF (id=2): 3 unidades
  - Gravação (id=4): 1 serviço
```

---

### 6. TABELA: `materials` (Materiais: Chapas e Insumos)

**Propósito**: Controle de estoque de matérias-primas

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| name | VARCHAR(100) | NOT NULL | Nome do material |
| type | ENUM | NOT NULL | 'chapa' ou 'insumo' |
| unit | VARCHAR(10) | NOT NULL | Unidade: placa, litro, kg, etc |
| stock | DECIMAL(10,2) | DEFAULT 0 | Quantidade em estoque |
| min_stock | DECIMAL(10,2) | DEFAULT 0 | Mínimo para alerta |
| cost | DECIMAL(10,2) | NOT NULL | Custo unitário |
| is_active | BOOLEAN | DEFAULT 1 | Ativo ou inativo |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Por quê esse design?**
- ✅ DECIMAL(10,2) em stock e cost (permite frações)
- ✅ min_stock para alertas de compra
- ✅ type ENUM separa chapas (grandes) de insumos (pequenos)
- ✅ is_active para soft-delete

**Índices**:
```sql
INDEX idx_materials_type (type)      -- Filtro chapa/insumo
INDEX idx_materials_stock (stock)    -- Buscar estoque crítico
```

**Relacionamentos**:
- product_materials.material_id → materials.id

---

### 7. TABELA: `product_materials` (Consumo de Materiais por Produto)

**Propósito**: Relacionamento many-to-many entre produtos e materiais

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| product_id | INT | FK, PK | Referência ao produto |
| material_id | INT | FK, PK | Referência ao material |
| quantity | DECIMAL(10,2) | NOT NULL | Quantidade usada |

**Por quê esse design?**
- ✅ PK composta garante um material por produto
- ✅ CASCADE DELETE
- ✅ Permite calcular custo de produção

**Exemplo de dados**:
```sql
Placa Acrílico (id=1) consome:
  - Acrílico 3mm (id=1): 1 placa
  - Tinta preta (id=3): 0.1 litro
```

---

### 8. TABELA: `costs` (Custos Fixos e Variáveis)

**Propósito**: Armazenar componentes de custo para cálculo de preço

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| name | VARCHAR(100) | NOT NULL | Nome do custo |
| type | ENUM | NOT NULL | 'fixo' ou 'variavel' |
| value | DECIMAL(10,2) | NOT NULL | Valor do custo |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Por quê esse design?**
- ✅ ENUM em type para valores fixos
- ✅ Permite múltiplos custos (aluguel, energia, etc)
- ✅ Fácil de estender

**Exemplo de dados**:
```sql
Custos fixos (mensais):
  - Aluguel: 5.000
  - Energia: 3.000
  - Salários: 15.000

Custos variáveis (por unidade):
  - Embalagem: 5.00
  - Combustível: 2.50
  - Mão de obra: 20.00
```

**Índices**:
```sql
INDEX idx_costs_type (type)  -- Separar fixo de variável
```

---

### 9. TABELA: `margins` (Margens de Lucro)

**Propósito**: Definir margens por tipo de produto/cliente

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| name | VARCHAR(100) | NOT NULL | Nome da margem |
| value | DECIMAL(5,2) | NOT NULL | Percentual (ex: 45.00 = 45%) |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Por quê esse design?**
- ✅ DECIMAL(5,2) até 999.99% (suficiente)
- ✅ Permite múltiplas margens (produto, serviço, kit)

**Exemplo de dados**:
```sql
Margem Padrão Produtos: 45%
Margem Premium Serviços: 60%
Margem Kits Promocionais: 35%
```

---

### 10. TABELA: `simulations` (Simulações de Preço)

**Propósito**: Armazenar simulações antes de criar orçamento

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| client_id | INT | FK, NULL | Cliente associado (pode ser nulo) |
| user_id | INT | FK | Usuário que criou |
| description | TEXT | NULL | Descrição da simulação |
| total | DECIMAL(10,2) | NOT NULL | Valor total simulado |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Por quê esse design?**
- ✅ client_id pode ser NULL (simulação genérica)
- ✅ Armazena apenas totais (detalhes em outra tabela se necessário)
- ✅ Permite histórico de simula

ções

**Fluxo**:
```
Simulação (teste) → Orçamento (apresentação) → Pedido (confirmação)
```

---

### 11. TABELA: `budgets` (Orçamentos)

**Propósito**: Orçamentos apresentados aos clientes

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| simulation_id | INT | FK, NULL | Simulação original |
| client_id | INT | FK | Cliente do orçamento |
| user_id | INT | FK | Vendedor que criou |
| total | DECIMAL(10,2) | NOT NULL | Valor do orçamento |
| discount | DECIMAL(10,2) | DEFAULT 0 | Desconto aplicado |
| status | ENUM | NOT NULL | 'aberto','aprovado','rejeitado' |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Por quê esse design?**
- ✅ Pode avoir sem simulação
- ✅ discount permite ajustes
- ✅ ENUM em status para controle
- ✅ Rastreia quem criou

**Índices**:
```sql
INDEX idx_budgets_client_id (client_id)
INDEX idx_budgets_status (status)
INDEX idx_budgets_created_at (created_at)
```

---

### 12. TABELA: `orders` (Pedidos de Venda)

**Propósito**: Pedidos confirmados pelos clientes

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| budget_id | INT | FK, NULL | Orçamento origem |
| client_id | INT | FK | Cliente do pedido |
| user_id | INT | FK | Vendedor responsável |
| total | DECIMAL(10,2) | NOT NULL | Valor do pedido |
| observations | TEXT | NULL | Observações |
| status | ENUM | NOT NULL | 'aberto','em_producao','finalizado','cancelado' |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Por quê esse design?**
- ✅ Referencia orçamento (rastreabilidade)
- ✅ status ENUM para workflow
- ✅ observations para detalhes
- ✅ Trigger automático cria conta_a_receber

**Índices**:
```sql
INDEX idx_orders_client_id (client_id)
INDEX idx_orders_status (status)
INDEX idx_orders_created_at (created_at)
```

**Trigger Automático** (será criado):
```sql
AFTER INSERT ON orders:
  INSERT INTO accounts_receivable (order_id, client_id, ...)
  VALUES (NEW.id, NEW.client_id, ...)
```

---

### 13. TABELA: `accounts_receivable` (Contas a Receber)

**Propósito**: Rastrear pagamentos dos clientes

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| order_id | INT | FK | Pedido relacionado |
| client_id | INT | FK | Cliente devedor |
| due_date | DATE | NOT NULL | Data de vencimento |
| value | DECIMAL(10,2) | NOT NULL | Valor a receber |
| status | ENUM | NOT NULL | 'aberto','pago','atrasado' |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Por quê esse design?**
- ✅ DATE (não DATETIME) para vencimento (sem hora)
- ✅ Rastrear cliente AND order_id
- ✅ Calcula se atrasado via trigger

**Índices**:
```sql
INDEX idx_accounts_receivable_client (client_id)
INDEX idx_accounts_receivable_status (status)
```

**View Automática**:
```sql
SELECT * FROM accounts_receivable
WHERE status = 'atrasado'
  AND due_date < DATE(NOW())
```

---

### 14. TABELA: `accounts_payable` (Contas a Pagar)

**Propósito**: Rastrear pagamentos aos fornecedores

**Campos**: Similares a accounts_receivable, mas com supplier_id

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| supplier_id | INT | FK | Fornecedor (referencia clients) |
| due_date | DATE | NOT NULL | Data vencimento |
| value | DECIMAL(10,2) | NOT NULL | Valor a pagar |
| status | ENUM | NOT NULL | 'aberto','pago','atrasado' |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Por quê usar clients para supplier?**
- ✅ Reutiliza mesma tabela
- ✅ type='fornecedor' diferencia
- ✅ Reduz redundância

---

### 15. TABELA: `credit_movements` (Movimentação de Crédito)

**Propósito**: Rastrear créditos de clientes

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| client_id | INT | FK | Cliente |
| value | DECIMAL(10,2) | NOT NULL | Valor |
| type | ENUM | NOT NULL | 'entrada' ou 'saida' |
| description | TEXT | NULL | Descrição |
| created_at | DATETIME | DEFAULT NOW() | Data criação |

**Exemplo de dados**:
```sql
Cliente XYZ:
  - Entrada: 5.000 (crédito inicial)
  - Saída: 2.500 (usado em pedido)
  - Entrada: 1.000 (reembolso)
  - Saldo: 3.500
```

---

### 16. TABELA: `audit_history` (Auditoria)

**Propósito**: Rastrear todas as ações do sistema

**Campos**:

| Campo | Tipo | Restrição | Descrição |
|-------|------|-----------|-----------|
| id | INT | PK, AUTO_INCREMENT | Identificador |
| user_id | INT | FK | Usuário que fez ação |
| action | VARCHAR(100) | NOT NULL | Tipo: CREATE, UPDATE, DELETE |
| table_name | VARCHAR(50) | NOT NULL | Tabela afetada |
| record_id | INT | NULL | ID do registro |
| description | TEXT | NULL | Descrição da ação |
| created_at | DATETIME | DEFAULT NOW() | Data/hora ação |

**Exemplo de dados**:
```sql
user_id=1, action='CREATE', table_name='orders', record_id=123, description='Novo pedido criado'
user_id=2, action='UPDATE', table_name='orders', record_id=123, description='Status alterado para finalizado'
user_id=1, action='DELETE', table_name='products', record_id=5, description='Produto inativado'
```

---

## ⚡ ÍNDICES DE PERFORMANCE

### Índices Criados:

```sql
-- Users
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role ON users(role);

-- Clients
CREATE INDEX idx_clients_type ON clients(type);
CREATE INDEX idx_clients_document ON clients(document);
CREATE INDEX idx_clients_name ON clients(name);

-- Products
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_products_is_active ON products(is_active);

-- Materials
CREATE INDEX idx_materials_type ON materials(type);
CREATE INDEX idx_materials_stock ON materials(stock);

-- Orders
CREATE INDEX idx_orders_client_id ON orders(client_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created_at ON orders(created_at);

-- Budgets
CREATE INDEX idx_budgets_client_id ON budgets(client_id);
CREATE INDEX idx_budgets_status ON budgets(status);
CREATE INDEX idx_budgets_created_at ON budgets(created_at);

-- Accounts
CREATE INDEX idx_accounts_receivable_client ON accounts_receivable(client_id);
CREATE INDEX idx_accounts_receivable_status ON accounts_receivable(status);
CREATE INDEX idx_audit_user_id ON audit_history(user_id);
CREATE INDEX idx_audit_table_name ON audit_history(table_name);
```

### Por que esses índices?

1. **username**: Login é feito frequentemente
2. **client_id**: Filtro comum em pedidos, orçamentos
3. **status**: Filtros por status são frequentes
4. **created_at**: Ordenação por data
5. **type**: Separar clientes/fornecedores e chapas/insumos

### Quando NÃO criar índices?

```sql
-- ❌ NÃO
CREATE INDEX idx_products_is_active (is_active);
-- Só tem 2 valores, index não ajuda

-- ❌ NÃO
CREATE INDEX idx_orders_total (total);
-- Raramente ordenado por total

-- ✅ SIM
CREATE INDEX idx_orders_status (status);
-- Filtro frequente (em aberto, finalizado, etc)
```

---

## 📊 VIEWS PARA ANÁLISE

### View 1: Relatório de Vendas

```sql
CREATE OR REPLACE VIEW vw_sales_report AS
SELECT 
    o.id as order_id,
    o.created_at,
    c.name as client_name,
    o.total,
    o.status,
    u.username as created_by
FROM orders o
JOIN clients c ON o.client_id = c.id
JOIN users u ON o.user_id = u.id
ORDER BY o.created_at DESC;
```

**Uso**:
```sql
SELECT * FROM vw_sales_report 
WHERE created_at >= '2026-01-01' 
  AND status = 'finalizado';
```

---

### View 2: Análise de Estoque

```sql
CREATE OR REPLACE VIEW vw_stock_analysis AS
SELECT 
    id,
    name,
    type,
    stock,
    min_stock,
    cost,
    stock * cost as total_value,
    CASE 
        WHEN stock < min_stock THEN 'CRÍTICO'
        WHEN stock <= (min_stock * 1.5) THEN 'BAIXO'
        ELSE 'OK'
    END as status
FROM materials
ORDER BY status, stock ASC;
```

**Uso**:
```sql
SELECT * FROM vw_stock_analysis WHERE status IN ('CRÍTICO', 'BAIXO');
```

---

### View 3: Contas a Receber Abertas

```sql
CREATE OR REPLACE VIEW vw_open_receivables AS
SELECT 
    ar.id,
    ar.due_date,
    c.name as client_name,
    ar.value,
    ar.status,
    DATEDIFF(ar.due_date, NOW()) as days_until_due
FROM accounts_receivable ar
JOIN clients c ON ar.client_id = c.id
WHERE ar.status IN ('aberto', 'atrasado')
ORDER BY ar.due_date ASC;
```

---

## 🔧 STORED PROCEDURES

### Procedure: Calcular Receita Total

```sql
DELIMITER $$

CREATE PROCEDURE sp_calculate_total_revenue(OUT total DECIMAL(15,2))
BEGIN
    SELECT COALESCE(SUM(total), 0) INTO total 
    FROM orders 
    WHERE status != 'cancelado';
END$$

DELIMITER ;

-- Uso:
CALL sp_calculate_total_revenue(@revenue);
SELECT @revenue;
```

### Procedure: Registrar Auditoria com Transação

```sql
DELIMITER $$

CREATE PROCEDURE sp_log_audit(
    IN p_user_id INT,
    IN p_action VARCHAR(100),
    IN p_table_name VARCHAR(50),
    IN p_record_id INT,
    IN p_description TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    INSERT INTO audit_history (user_id, action, table_name, record_id, description)
    VALUES (p_user_id, p_action, p_table_name, p_record_id, p_description);
    COMMIT;
END$$

DELIMITER ;
```

---

## 🎯 TRIGGERS DE AUDITORIA

### Trigger: Inserção em Pedidos

```sql
DELIMITER $$

CREATE TRIGGER trg_audit_orders_insert
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    INSERT INTO audit_history (user_id, action, table_name, record_id, description)
    VALUES (
        NEW.user_id, 
        'CREATE', 
        'orders', 
        NEW.id, 
        CONCAT('Novo pedido criado para cliente: ', NEW.client_id)
    );
END$$

DELIMITER ;
```

### Trigger: Atualização em Pedidos

```sql
DELIMITER $$

CREATE TRIGGER trg_audit_orders_update
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status != OLD.status THEN
        INSERT INTO audit_history (user_id, action, table_name, record_id, description)
        VALUES (
            1,
            'UPDATE',
            'orders',
            NEW.id,
            CONCAT('Status alterado de ', OLD.status, ' para ', NEW.status)
        );
    END IF;
END$$

DELIMITER ;
```

---

## 🚀 PRÓXIMOS PASSOS

1. ✅ Executar `setup_complete.sql` no banco
2. ✅ Verificar se todas as tabelas foram criadas
3. ⏳ Criar views e stored procedures
4. ⏳ Implementar triggers

---

**Documento mantido atualizado conforme desenvolvimento avança.**

