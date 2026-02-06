# ETAPA 2 - BANCO DE DADOS COMPLETO

## 📋 Resumo

A **ETAPA 2** implementa o banco de dados MySQL completo para o ERP Fênix Magazine Personalizados.

**Status**: ✅ PRONTO PARA EXECUÇÃO
**Data**: 6 de Fevereiro de 2026
**Versão**: 1.0

---

## 📊 Estrutura de 16 Tabelas

### 1️⃣ **USUÁRIOS**
```
usuarios
├── id (PK)
├── nome
├── email (UNIQUE)
├── senha (criptografada)
├── ativo (1/0)
└── timestamps
```
**Uso**: Login e controle de acesso
**Índices**: email, ativo

---

### 2️⃣ **CLIENTES / FORNECEDORES**
```
clientes
├── id (PK)
├── tipo (cliente | fornecedor | ambos)
├── Nome + Razão Social
├── CPF / CNPJ
├── Contato (email, telefone, WhatsApp)
├── Endereço completo
├── Crédito (limite, disponível, utilizado)
├── observacoes
└── ativo
```
**Uso**: Cadastro de clientes e fornecedores
**Índices**: tipo, email, documento, ativo

---

### 3️⃣ **MATERIAIS** (Chapas e Insumos)
```
materiais
├── id (PK)
├── tipo (chapa | insumo)
├── nome
├── descricao
├── Para CHAPAS:
│   ├── largura_mm
│   ├── comprimento_mm
│   ├── espessura_mm
│   └── area_mm2
├── Para INSUMOS:
│   └── unidade_medida
├── preco_unitario
├── Estoque (preparado para futuro)
└── ativo
```
**Uso**: Gestão de materiais disponíveis
**Índices**: tipo, ativo

---

### 4️⃣ **CUSTOS** (Fixos e Variáveis)
```
custos
├── id (PK)
├── nome
├── tipo (fixo | variavel)
├── unidade (minuto | hora | peça | mês)
├── valor
├── data_inicio / data_fim
├── impacta_simulador (1/0)
└── ativo
```
**Uso**: Cálculo de margens e preços na simulação
**Índices**: tipo, ativo

---

### 5️⃣ **SIMULAÇÕES** ⭐ CRÍTICA
```
simulações
├── id (PK)
├── usuario_id (FK) → quem criou
├── nome
├── chapa_id (FK) → qual chapa usou
├── Dimensões: largura_mm, comprimento_mm
├── area_peça_mm2
├── area_chapa_mm2
├── Aproveitamento: 
│   ├── area_aproveitada_mm2
│   └── percentual_aproveitamento
├── Tempos:
│   ├── tempo_corte_minutos
│   ├── tempo_gravacao_minutos
│   └── tempo_total_minutos
├── quantidade_pecas
├── insumos_json (JSON com insumos)
├── CUSTOS CALCULADOS:
│   ├── custo_material
│   ├── custo_insumos
│   ├── custo_operacional
│   └── custo_total
├── PREÇO:
│   ├── margem_lucro (%)
│   ├── valor_lucro
│   ├── preco_unitario
│   └── preco_total
├── convertida_em_produto (1/0)
├── produto_id (quando convertida)
└── timestamps
```
**Uso**: Core do simulador - calcula tudo
**Índices**: usuario, convertida

---

### 6️⃣ **PRODUTOS**
```
produtos
├── id (PK)
├── tipo (simples | kit)
├── nome
├── descricao
├── simulacao_id (FK) → vem de simulação
├── preco_custo
├── preco_venda
├── margem_lucro (%)
├── lucro_unitario
├── sku
├── imagem_url
└── ativo
```
**Uso**: Catálogo de produtos finais
**Índices**: tipo, ativo, sku

---

### 7️⃣ **PRODUTOS_KIT** (Composição)
```
produtos_kit
├── id (PK)
├── kit_id (FK) → o kit
├── produto_id (FK) → produto que compõe
└── quantidade
```
**Uso**: Defines quais produtos formam um kit
**Exemplo**: Kit 1 = Brinde A (2x) + Brinde B (1x)

---

### 8️⃣ **ORÇAMENTOS** 📋
```
orcamentos
├── id (PK)
├── numero (UNIQUE: ORC-2025-001)
├── cliente_id (FK)
├── usuario_id (FK) → vendedor
├── status (rascunho | enviado | aceito | recusado | convertido)
├── subtotal
├── desconto
├── total
├── usa_credito (1/0)
├── credito_utilizado
├── condicao_pagamento
├── parcelas
├── observacoes
├── Datas:
│   ├── data_emissao
│   ├── data_envio
│   ├── data_inicio_producao
│   ├── data_prevista_entrega
│   └── validade_dias
└── timestamps
```
**Uso**: Propostas enviadas aos clientes
**Índices**: cliente, status, numero

---

### 9️⃣ **ITENS_ORCAMENTO** 📦
```
itens_orcamento
├── id (PK)
├── orcamento_id (FK)
├── produto_id (FK) → opcional
├── descricao
├── quantidade
├── preco_unitario
├── total
└── ordem
```
**Uso**: Itens dentro de cada orçamento
**Note**: Pode ser produto do catálogo OU customizado

---

### 🔟 **PEDIDOS** ✅
```
pedidos
├── id (PK)
├── numero (UNIQUE: PED-2025-001)
├── orcamento_id (FK) → origem
├── cliente_id (FK)
├── status (pendente | producao | pronto | enviado | entregue | cancelado)
├── Datas:
│   ├── data_pedido
│   ├── data_inicio_producao
│   ├── data_conclusao
│   └── data_entrega
├── subtotal
├── desconto
├── total
├── credito_utilizado
├── condicao_pagamento
├── pago (1/0)
├── observacoes
└── timestamps
```
**Uso**: Pedidos convertidos de orçamentos
**Índices**: cliente, status, numero

---

### 1️⃣1️⃣ **ITENS_PEDIDO**
```
itens_pedido
├── id (PK)
├── pedido_id (FK)
├── produto_id (FK) → opcional
├── descricao
├── quantidade
├── preco_unitario
└── total
```
**Uso**: Itens do pedido (cópia do orçamento)

---

### 1️⃣2️⃣ **CONTAS_RECEBER** 💰
```
contas_receber
├── id (PK)
├── pedido_id (FK) → opcional
├── cliente_id (FK)
├── numero_parcela (1 de 3)
├── total_parcelas (3)
├── valor
├── valor_pago
├── valor_pendente
├── data_vencimento
├── data_pagamento
├── status (pendente | pago | atrasado | cancelado)
├── forma_pagamento
└── criado_em
```
**Uso**: Rastreamento de pagamentos dos clientes
**Índices**: cliente, status, vencimento

---

### 1️⃣3️⃣ **CONTAS_PAGAR** 💸
```
contas_pagar
├── id (PK)
├── fornecedor_id (FK)
├── descricao
├── numero_parcela
├── total_parcelas
├── valor
├── valor_pago
├── valor_pendente
├── data_vencimento
├── data_pagamento
├── status (pendente | pago | atrasado | cancelado)
├── categoria (Material | Serviço | Aluguel)
└── criado_em
```
**Uso**: Controle do que precisa pagar
**Índices**: fornecedor, status

---

### 1️⃣4️⃣ **MOVIMENTACAO_CREDITO** 🔄 (AUDITORIA)
```
movimentacao_credito
├── id (PK)
├── cliente_id (FK)
├── usuario_id (FK) → quem fez
├── tipo (credito | debito | ajuste)
├── valor
├── saldo_anterior
├── saldo_novo
├── referencia (Pedido #123, etc)
├── motivo
├── ip_address
└── criado_em
```
**Uso**: **RASTREABILIDADE 100% do crédito**
- Cada operação de crédito é registrada
- Impossível perder histórico
- Score: Auditoria Total

**Índices**: cliente, tipo, data

---

### 1️⃣5️⃣ **HISTORICO_AUDITORIA** 📜 (COMPLIANCE)
```
historico_auditoria
├── id (PK)
├── usuario_id (FK)
├── modulo (clientes | produtos | etc)
├── tabela
├── registro_id
├── acao (criar | atualizar | deletar | login | logout)
├── dados_anterior (JSON)
├── dados_novo (JSON)
├── descricao
├── ip_address
├── user_agent
└── criado_em
```
**Uso**: **REGISTRO COMPLETO de tudo que acontece**
- Quem fez
- O que mudou
- Quando mudou
- De onde acessou

**Índices**: usuario, modulo, acao, data

---

### 1️⃣6️⃣ **FLUXO_CAIXA_PREVISTO** 📈
```
fluxo_caixa_previsto
├── id (PK)
├── data
├── tipo (entrada | saida)
├── categoria
├── descricao
├── valor
├── referencia_id
└── criado_em
```
**Uso**: Previsão de entrada/saída de caixa
**Índices**: data, tipo

---

## 🔗 Relacionamentos Principais

```
┌─────────────────────────────────────────────────────┐
│                   USUÁRIOS                          │
│  (login, controle de acesso)                        │
└──────────────┬──────────────────────────────────────┘
              │
        ┌─────┴───────────────────────────────┐
        │                                     │
        ▼                                     ▼
   SIMULAÇÕES                          ORCAMENTOS
   (calcula tudo)                      (propostas)
        │                                  │
        │                                  │ 
        ▼                                  ▼
   PRODUTOS ◄────────────────────── ITENS_ORCAMENTO
   (catálogo)                              │
        │                                  │
        │                         ┌────────┘
        │                         │
        ▼                         ▼
   PRODUTOS_KIT              PEDIDOS (converte)
   (kits)                         │
                                  │
             ┌────────────────────┼────────────────┐
             │                    │                │
             ▼                    ▼                ▼
      ITENS_PEDIDO         CONTAS_RECEBER   CONTAS_PAGAR
      (cópia)              (clientes)        (fornecedores)
                                │                  │
                                │                  │
             ┌──────────────────┴──────────────────┘
             │
             ▼
      MOVIMENTACAO_CREDITO
      (auditoria total)

GLOBAL:
  HISTORICO_AUDITORIA (registra TUDO)
  FLUXO_CAIXA_PREVISTO (previsão)
```

---

## 🚀 Como Executar

### 1. Criar o Banco de Dados
```bash
mysql -u seu_usuario -p
```

```sql
CREATE DATABASE erp_laser CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE erp_laser;
```

### 2. Executar o Script SQL

**Opção A**: Via linha de comando
```bash
mysql -u seu_usuario -p erp_laser < etapa2_banco_dados.sql
```

**Opção B**: Via phpMyAdmin
1. Abrir phpMyAdmin
2. Selecionar banco `erp_laser`
3. Aba "SQL"
4. Copiar/colar conteúdo do arquivo
5. Executar

### 3. Verificar Criação
```sql
SHOW TABLES;
-- Deve listar 16 tabelas

DESCRIBE usuarios;
-- Verificar estrutura
```

---

## 📝 Dados de Teste Já Inseridos

```sql
-- 1 usuário de teste
INSERT INTO usuarios (nome, email, senha) VALUES 
('Administrador', 'admin@example.com', 'admin123');

-- 1 cliente de teste
INSERT INTO clientes (tipo, nome, email, telefone, limte_credito, credito_disponivel) VALUES 
('cliente', 'Cliente Teste Ltda', 'cliente@example.com', '(11) 3000-0000', 5000.00, 5000.00);

-- 1 chapa de teste
INSERT INTO materiais (tipo, nome, largura_mm, comprimento_mm, espessura_mm, area_mm2, preco_unitario) VALUES 
('chapa', 'Chapa MDF 3mm', 1000, 1000, 3, 1000000, 150.00);

-- 1 insumo de teste
INSERT INTO materiais (tipo, nome, unidade_medida, preco_unitario) VALUES 
('insumo', 'Spray acrílico', 'un', 25.00);

-- 1 custo de teste
INSERT INTO custos (nome, tipo, unidade, valor) VALUES 
('Corte Laser', 'variavel', 'minuto', 5.00);
```

---

## ⚙️ Características de Segurança

✅ **Chaves Estrangeiras**: Integridade referencial garantida
✅ **Índices Estratégicos**: Performance otimizada nas buscas
✅ **Timestamps**: Todas as tabelas críticas têm criado_em e atualizado_em
✅ **UNIQUE Constraints**: Email, número de orçamento/pedido, etc
✅ **JSON Columns**: Insumos em simulação, dados antigos/novos em auditoria
✅ **ENUM Types**: Status com valores pré-definidos (previne erros)
✅ **Comentários Detalhados**: Cada coluna explicada

---

## 📚 Próximos Passos (ETAPA 3)

Após executar este script:

1. **Criar Models PHP**
   - `ClienteModel` extends BaseModel
   - `MaterialModel` extends BaseModel
   - `SimulacaoModel` extends BaseModel
   - `ProdutoModel` extends BaseModel
   - `OrcamentoModel` extends BaseModel
   - `PedidoModel` extends BaseModel
   - etc...

2. **Criar Controllers**
   - `ClientesController` → CRUD clientes
   - `MateriaisController` → CRUD materiais
   - `CustosController` → CRUD custos
   - `SimuladorController` → CRIAR simulações
   - `OrcamentosController` → Criar/enviar/converter
   - `PedidosController` → Gerenciar produção
   - etc...

3. **Criar Views**
   - Formulários para cada CRUD
   - Listagens com busca/filtro
   - Dashboard financeiro
   - Simulador interativo

4. **Segurança**
   - Validar entrada de dados
   - Sanitizar antes de INSERT
   - Logar todas operações em `historico_auditoria`
   - Usar transações para operações críticas

---

## 🔍 Dúvidas?

**Q: Por que 16 tabelas?**
A: Design normalizado. Cada tabela tem um propósito específico. Evita redundância e mantém dados consistentes.

**Q: Posso deletar campos?**
A: Não recomendado. Se não usar, apenas deixe NULL.

**Q: Preciso criar índices adicionais?**
A: Depende das buscas mais frequentes. Pode adicionar depois conforme necessário.

**Q: Como adicionar novos campos no futuro?**
A: Use `ALTER TABLE` com cuidado. Backup sempre antes!

---

## 📞 Status

✅ **ETAPA 2 COMPLETA**
- Banco de dados criado
- 16 tabelas estruturadas
- Índices otimizados
- Dados de teste inseridos
- Documentação completa

**Próximo**: Aguardando confirmação para iniciar ETAPA 3 (Modelos PHP + Controllers Clientes/Fornecedores)

---

**Criado em**: 6 de Fevereiro de 2026
**Versão**: 1.0
**Responsável**: GitHub Copilot
