/**
 * SCRIPT SQL - BANCO DE DADOS ERP FÊNIX MAGAZINE PERSONALIZADOS
 * 
 * ETAPA 2: BANCO DE DADOS COMPLETO
 * 
 * Este arquivo contém:
 * - 15+ tabelas completas
 * - Chaves primárias e estrangeiras
 * - Índices para performance
 * - Dados iniciais de teste
 * - Comentários explicativos
 * 
 * IMPORTANTE:
 * 1. Criar banco: CREATE DATABASE erp_laser CHARACTER SET utf8mb4;
 * 2. Selecionar: USE erp_laser;
 * 3. Executar este script
 * 
 * Data: 6 de Fevereiro de 2026
 * Versão: 1.0
 */

-- ============================================================================
-- 1. TABELA: USUÁRIOS
-- ============================================================================
-- Armazena usuários do sistema
-- Campos: ID, Nome, Email, Senha, Ativo, Criado, Atualizado

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do usuário',
    nome VARCHAR(255) NOT NULL COMMENT 'Nome completo do usuário',
    email VARCHAR(255) UNIQUE NOT NULL COMMENT 'Email único para login',
    senha VARCHAR(255) NOT NULL COMMENT 'Senha criptografada',
    ativo TINYINT(1) DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data criação',
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização',
    
    INDEX idx_email (email) COMMENT 'Índice para busca rápida por email',
    INDEX idx_ativo (ativo) COMMENT 'Índice para filtrar usuários ativos'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Tabela de usuários do sistema';

-- Inserir usuário de teste
INSERT INTO usuarios (nome, email, senha) VALUES 
('Administrador', 'admin@example.com', 'admin123');

-- ============================================================================
-- 2. TABELA: CLIENTES (E FORNECEDORES)
-- ============================================================================
-- Armazena clientes e fornecedores
-- Campos: ID, Tipo, Nome, Razão Social, CPF/CNPJ, Email, Telefone, Crédito, etc

CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do cliente',
    tipo ENUM('cliente', 'fornecedor', 'ambos') DEFAULT 'cliente' COMMENT 'Tipo: cliente, fornecedor ou ambos',
    
    -- Dados Pessoais
    nome VARCHAR(255) NOT NULL COMMENT 'Nome ou apelido',
    razao_social VARCHAR(255) COMMENT 'Razão social (PJ)',
    documento TEXT COMMENT 'CPF (PF) ou CNPJ (PJ)',
    documento_tipo ENUM('CPF', 'CNPJ') COMMENT 'Tipo de documento',
    
    -- Contato
    email VARCHAR(255) COMMENT 'Email principal',
    telefone VARCHAR(20) COMMENT 'Telefone principal',
    whatsapp VARCHAR(20) COMMENT 'WhatsApp (com flag)',
    telefone_secundario VARCHAR(20) COMMENT 'Telefone secundário',
    
    -- Endereço
    endereco VARCHAR(255) COMMENT 'Logradouro',
    numero VARCHAR(10) COMMENT 'Número',
    complemento VARCHAR(255) COMMENT 'Complemento (apt, bloco)',
    cep VARCHAR(10) COMMENT 'CEP (formato: 00000-000)',
    cidade VARCHAR(100) COMMENT 'Cidade',
    estado CHAR(2) COMMENT 'Estado (UF)',
    pais VARCHAR(100) DEFAULT 'Brasil' COMMENT 'País',
    
    -- Crédito
    limte_credito DECIMAL(10,2) DEFAULT 0 COMMENT 'Limite de crédito permitido',
    credito_disponivel DECIMAL(10,2) DEFAULT 0 COMMENT 'Crédito disponível para usar',
    credito_utilizado DECIMAL(10,2) DEFAULT 0 COMMENT 'Crédito já utilizado',
    
    -- Observações
    observacoes TEXT COMMENT 'Anotações importantes',
    
    -- Status
    ativo TINYINT(1) DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação',
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização',
    
    INDEX idx_tipo (tipo) COMMENT 'Índice para filtrar por tipo',
    INDEX idx_email (email) COMMENT 'Índice para busca rápida por email',
    INDEX idx_documento (documento(50)) COMMENT 'Índice para busca por CPF/CNPJ',
    INDEX idx_ativo (ativo) COMMENT 'Índice para cliente ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Tabela de clientes e fornecedores';

-- ============================================================================
-- 3. TABELA: MATERIAIS (CHAPAS E INSUMOS)
-- ============================================================================
-- Armazena chapas e insumos usados na produção
-- Chapas: Largura, Comprimento, Espessura, Área, Preço
-- Insumos: Unidade de medida, Preço unitário

CREATE TABLE materiais (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do material',
    tipo ENUM('chapa', 'insumo') NOT NULL COMMENT 'Tipo: chapa ou insumo',
    nome VARCHAR(255) NOT NULL COMMENT 'Nome do material',
    descricao TEXT COMMENT 'Descrição detalhada',
    
    -- Para CHAPAS
    largura_mm DECIMAL(8,2) COMMENT 'Largura em milímetros',
    comprimento_mm DECIMAL(8,2) COMMENT 'Comprimento em milímetros',
    espessura_mm DECIMAL(8,2) COMMENT 'Espessura em milímetros',
    area_mm2 DECIMAL(12,2) COMMENT 'Área calculada (largura × comprimento)',
    
    -- Para INSUMOS
    unidade_medida VARCHAR(20) COMMENT 'Unidade: un, metro, kg, litro, etc',
    
    -- Preço
    preco_unitario DECIMAL(10,2) NOT NULL COMMENT 'Preço por unidade/area',
    
    -- Estoque (preparado para futuro)
    estoque_quantidade DECIMAL(10,2) DEFAULT 0 COMMENT 'Quantidade em estoque',
    estoque_minimo DECIMAL(10,2) DEFAULT 0 COMMENT 'Estoque mínimo alerta',
    
    -- Status
    ativo TINYINT(1) DEFAULT 1 COMMENT '1=Ativo, 0=Descontinuado',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data criação',
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização',
    
    INDEX idx_tipo (tipo) COMMENT 'Índice para filtrar por tipo',
    INDEX idx_ativo (ativo) COMMENT 'Índice para materiais ativos'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Tabela de materiais (chapas e insumos)';

-- ============================================================================
-- 4. TABELA: CUSTOS (FIXOS E VARIÁVEIS)
-- ============================================================================
-- Armazena custos operacionais do sistema
-- Custos fixos: aluguel, salário, etc
-- Custos variáveis: por minuto, por hora, por peça, etc

CREATE TABLE custos (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do custo',
    nome VARCHAR(255) NOT NULL COMMENT 'Nome do custo (ex: Manutenção Laser)',
    descricao TEXT COMMENT 'Descrição do custo',
    
    -- Tipo
    tipo ENUM('fixo', 'variavel') NOT NULL COMMENT 'Fixo ou Variável',
    
    -- Unidade (para variáveis)
    unidade ENUM('minuto', 'hora', 'peça', 'mes', 'outro') COMMENT 'Unidade de medição',
    
    -- Valor
    valor DECIMAL(10,2) NOT NULL COMMENT 'Valor do custo',
    
    -- Data de validade
    data_inicio DATE COMMENT 'A partir de quando',
    data_fim DATE COMMENT 'Até quando (NULL = indefinido)',
    
    -- Impacto
    impacta_simulador TINYINT(1) DEFAULT 1 COMMENT '1=Usa na simulação, 0=Não usa',
    
    -- Status
    ativo TINYINT(1) DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data criação',
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização',
    
    INDEX idx_tipo (tipo) COMMENT 'Índice para filtrar fixo/variável',
    INDEX idx_ativo (ativo) COMMENT 'Índice para custos ativos'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Tabela de custos operacionais';

-- ============================================================================
-- 5. TABELA: SIMULAÇÕES
-- ============================================================================
-- Armazena simulações de corte criadas pelo usuário
-- Base para criar produtos posteriormente

CREATE TABLE simulacoes (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único da simulação',
    usuario_id INT NOT NULL COMMENT 'ID do usuário que criou',
    
    -- Dados da simulação
    nome VARCHAR(255) COMMENT 'Nome da simulação',
    descricao TEXT COMMENT 'Descrição do item cortado',
    
    -- Chapa utilizada
    chapa_id INT COMMENT 'ID da chapa usada',
    
    -- Dimensões da peça
    largura_mm DECIMAL(8,2) COMMENT 'Largura da peça em mm',
    comprimento_mm DECIMAL(8,2) COMMENT 'Comprimento da peça em mm',
    area_peça_mm2 DECIMAL(12,2) COMMENT 'Área da peça calculada',
    
    -- Aproveitamento
    area_chapa_mm2 DECIMAL(12,2) COMMENT 'Área total da chapa',
    area_aproveitada_mm2 DECIMAL(12,2) COMMENT 'Área realmente aproveitada',
    percentual_aproveitamento DECIMAL(5,2) COMMENT 'Percentual de aproveitamento',
    
    -- Tempos
    tempo_corte_minutos DECIMAL(8,2) COMMENT 'Tempo de corte em minutos',
    tempo_gravacao_minutos DECIMAL(8,2) COMMENT 'Tempo de gravação em minutos',
    tempo_total_minutos DECIMAL(8,2) COMMENT 'Tempo total em minutos',
    
    -- Quantidade
    quantidade_pecas INT DEFAULT 1 COMMENT 'Quantas peças cortadas',
    
    -- Insumos utilizados
    insumos_json JSON COMMENT 'Insumos utilizados: [{"id": 1, "quantidade": 2}, ...]',
    
    -- Custos calculados
    custo_material DECIMAL(10,2) COMMENT 'Custo do material/chapa',
    custo_insumos DECIMAL(10,2) COMMENT 'Custo dos insumos',
    custo_operacional DECIMAL(10,2) COMMENT 'Custo operacional (tempo × taxa)',
    custo_total DECIMAL(10,2) COMMENT 'Custo total = material + insumos + operacional',
    
    -- Margem e preço
    margem_lucro DECIMAL(5,2) COMMENT 'Margem de lucro em %',
    valor_lucro DECIMAL(10,2) COMMENT 'Valor do lucro = custo_total × (margem / 100)',
    preco_unitario DECIMAL(10,2) COMMENT 'Preço unitário sugerido = custo / quantidade',
    preco_total DECIMAL(10,2) COMMENT 'Preço total = preco_unitario × quantidade',
    
    -- Status
    convertida_em_produto TINYINT(1) DEFAULT 0 COMMENT '1=Virou produto, 0=Ainda simulação',
    produto_id INT COMMENT 'ID do produto gerado (se convertido)',
    
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data criação',
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização',
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (chapa_id) REFERENCES materiais(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id) COMMENT 'Índice para buscar por usuário',
    INDEX idx_convertida (convertida_em_produto) COMMENT 'Índice para filtrar convertidas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Tabela de simulações de corte';

-- ============================================================================
-- 6. TABELA: PRODUTOS
-- ============================================================================
-- Armazena produtos finais (simples ou kits)
-- Simples: vêm da simulação
-- Kit: composição de múltiplos produtos simples

CREATE TABLE produtos (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do produto',
    tipo ENUM('simples', 'kit') DEFAULT 'simples' COMMENT 'Simples ou Kit',
    nome VARCHAR(255) NOT NULL COMMENT 'Nome do produto',
    descricao TEXT COMMENT 'Descrição detalhada',
    
    -- De onde veio
    simulacao_id INT COMMENT 'ID da simulação (se vem de simulação)',
    
    -- Preço
    preco_custo DECIMAL(10,2) NOT NULL COMMENT 'Preço de custo',
    preco_venda DECIMAL(10,2) NOT NULL COMMENT 'Preço de venda',
    
    -- Margem
    margem_lucro DECIMAL(5,2) COMMENT 'Margem de lucro em %',
    lucro_unitario DECIMAL(10,2) COMMENT 'Lucro por unidade',
    
    -- Imagem/SKU
    sku VARCHAR(100) COMMENT 'Código SKU do produto',
    imagem_url VARCHAR(500) COMMENT 'URL da imagem do produto',
    
    -- Status
    ativo TINYINT(1) DEFAULT 1 COMMENT '1=Ativo, 0=Descontinuado',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data criação',
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização',
    
    FOREIGN KEY (simulacao_id) REFERENCES simulacoes(id) ON DELETE SET NULL,
    INDEX idx_tipo (tipo) COMMENT 'Índice para filtrar por tipo',
    INDEX idx_ativo (ativo) COMMENT 'Índice para produtos ativos',
    INDEX idx_sku (sku) COMMENT 'Índice para busca por SKU'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Tabela de produtos';

-- ============================================================================
-- 7. TABELA: PRODUTOS_KIT (Composição de Kits)
-- ============================================================================
-- Armazena quais produtos simples compõem um kit
-- Exemplo: Kit 1 = Produto A (2 un) + Produto B (1 un)

CREATE TABLE produtos_kit (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único da composição',
    kit_id INT NOT NULL COMMENT 'ID do kit (produto tipo=kit)',
    produto_id INT NOT NULL COMMENT 'ID do produto simples que compõe',
    quantidade INT NOT NULL COMMENT 'Quantidade deste produto no kit',
    
    FOREIGN KEY (kit_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    UNIQUE KEY uk_kit_produto (kit_id, produto_id) COMMENT 'Um produto por vez no kit',
    INDEX idx_kit (kit_id) COMMENT 'Índice para listar produtos do kit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Tabela de composição de kits';

-- ============================================================================
-- 8. TABELA: ORÇAMENTOS
-- ============================================================================
-- Armazena orçamentos enviados para clientes
-- Pode conter produtos do catálogo ou itens customizados

CREATE TABLE orcamentos (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do orçamento',
    numero VARCHAR(100) UNIQUE COMMENT 'Número do orçamento (ex: ORC-2025-001)',
    cliente_id INT NOT NULL COMMENT 'ID do cliente',
    usuario_id INT NOT NULL COMMENT 'ID do vendedor',
    
    -- Status
    status ENUM('rascunho', 'enviado', 'aceito', 'recusado', 'convertido') DEFAULT 'rascunho' 
           COMMENT 'Status do orçamento',
    
    -- Dados do orçamento
    subtotal DECIMAL(10,2) DEFAULT 0 COMMENT 'Total sem descontos',
    desconto DECIMAL(10,2) DEFAULT 0 COMMENT 'Desconto total',
    total DECIMAL(10,2) DEFAULT 0 COMMENT 'Total final = subtotal - desconto',
    
    -- Crédito
    usa_credito TINYINT(1) DEFAULT 0 COMMENT '1=Usa crédito, 0=Sem crédito',
    credito_utilizado DECIMAL(10,2) DEFAULT 0 COMMENT 'Quanto de crédito usa',
    
    -- Condição de pagamento
    condicao_pagamento VARCHAR(100) COMMENT 'Ex: À vista, 30 dias, 2x30dias',
    parcelas INT DEFAULT 1 COMMENT 'Número de parcelas',
    
    -- Observações
    observacoes TEXT COMMENT 'Anotações adicionais',
    
    -- Datas
    validade_dias INT DEFAULT 30 COMMENT 'Dias de validade do orçamento',
    data_emissao DATE NOT NULL COMMENT 'Data que foi emitido',
    data_envio DATETIME COMMENT 'Data/hora que foi enviado',
    data_inicio_producao DATE COMMENT 'Quando começar a produzir',
    data_prevista_entrega DATE COMMENT 'Data prevista de entrega',
    
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data criação no sistema',
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização',
    
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_cliente (cliente_id) COMMENT 'Índice para buscar por cliente',
    INDEX idx_status (status) COMMENT 'Índice para filtrar por status',
    INDEX idx_numero (numero) COMMENT 'Índice para buscar por número'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Tabela de orçamentos';

-- ============================================================================
-- 9. TABELA: ITENS_ORCAMENTO
-- ============================================================================
-- Armazena os itens dentro de um orçamento
-- Pode ser produto do catálogo ou customizado

CREATE TABLE itens_orcamento (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do item',
    orcamento_id INT NOT NULL COMMENT 'ID do orçamento',
    produto_id INT COMMENT 'ID do produto (pode ser NULL se customizado)',
    
    -- Dados do item
    descricao VARCHAR(500) NOT NULL COMMENT 'Descrição do item',
    quantidade INT DEFAULT 1 COMMENT 'Quantidade',
    preco_unitario DECIMAL(10,2) NOT NULL COMMENT 'Preço unitário',
    total DECIMAL(10,2) COMMENT 'Total = quantidade × preco_unitario',
    
    -- Ordem
    ordem INT COMMENT 'Ordem de exibição no orçamento',
    
    FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE SET NULL,
    INDEX idx_orcamento (orcamento_id) COMMENT 'Índice para listar itens'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Itens de um orçamento';

-- ============================================================================
-- 10. TABELA: PEDIDOS
-- ============================================================================
-- Armazena pedidos convertidos de orçamentos
-- Controla produção e entrega

CREATE TABLE pedidos (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do pedido',
    numero VARCHAR(100) UNIQUE COMMENT 'Número do pedido',
    orcamento_id INT COMMENT 'ID do orçamento origem',
    cliente_id INT NOT NULL COMMENT 'ID do cliente',
    
    -- Status de produção
    status ENUM('pendente', 'producao', 'pronto', 'enviado', 'entregue', 'cancelado') 
           DEFAULT 'pendente' COMMENT 'Status da produção',
    
    -- Datas
    data_pedido DATE NOT NULL COMMENT 'Data que virou pedido',
    data_inicio_producao DATE COMMENT 'Quando começou',
    data_conclusao DATE COMMENT 'Quando terminou',
    data_entrega DATE COMMENT 'Quando foi entregue',
    
    -- Valores (cópia do orçamento no momento)
    subtotal DECIMAL(10,2) COMMENT 'Subtotal',
    desconto DECIMAL(10,2) DEFAULT 0 COMMENT 'Desconto',
    total DECIMAL(10,2) COMMENT 'Total final',
    
    -- Crédito utilizado
    credito_utilizado DECIMAL(10,2) DEFAULT 0 COMMENT 'Crédito usado neste pedido',
    
    -- Financeiro
    condicao_pagamento VARCHAR(100) COMMENT 'Forma de pagamento',
    pago TINYINT(1) DEFAULT 0 COMMENT '1=Pago, 0=Pendente',
    
    -- Observações
    observacoes TEXT COMMENT 'Anotações sobre produção/entrega',
    
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data criação',
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização',
    
    FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE SET NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    INDEX idx_cliente (cliente_id) COMMENT 'Índice para buscar por cliente',
    INDEX idx_status (status) COMMENT 'Índice para filtrar por status',
    INDEX idx_numero (numero) COMMENT 'Índice para buscar por número'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Tabela de pedidos (conversão de orçamento)';

-- ============================================================================
-- 11. TABELA: ITENS_PEDIDO
-- ============================================================================
-- Armazena os itens de um pedido
-- Cópia dos itens do orçamento no momento da conversão

CREATE TABLE itens_pedido (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do item',
    pedido_id INT NOT NULL COMMENT 'ID do pedido',
    produto_id INT COMMENT 'ID do produto',
    
    -- Dados
    descricao VARCHAR(500) NOT NULL COMMENT 'Descrição',
    quantidade INT DEFAULT 1 COMMENT 'Quantidade',
    preco_unitario DECIMAL(10,2) NOT NULL COMMENT 'Preço unitário',
    total DECIMAL(10,2) COMMENT 'Total do item',
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE SET NULL,
    INDEX idx_pedido (pedido_id) COMMENT 'Índice para listar itens'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Itens de um pedido';

-- ============================================================================
-- 12. TABELA: CONTAS_RECEBER
-- ============================================================================
-- Armazena parcelas a receber de clientes

CREATE TABLE contas_receber (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único da parcela',
    pedido_id INT COMMENT 'ID do pedido (pode ser NULL)',
    cliente_id INT NOT NULL COMMENT 'ID do cliente',
    
    -- Dados da parcela
    numero_parcela INT COMMENT 'Número da parcela (ex: 1 de 3)',
    total_parcelas INT COMMENT 'Total de parcelas (ex: 3)',
    
    -- Valores
    valor DECIMAL(10,2) NOT NULL COMMENT 'Valor da parcela',
    valor_pago DECIMAL(10,2) DEFAULT 0 COMMENT 'Quanto já foi pago',
    valor_pendente DECIMAL(10,2) COMMENT 'O que falta pagar',
    
    -- Datas
    data_vencimento DATE NOT NULL COMMENT 'Quando vence',
    data_pagamento DATE COMMENT 'Quando foi pago (NULL = pendente)',
    
    -- Status
    status ENUM('pendente', 'pago', 'atrasado', 'cancelado') DEFAULT 'pendente' COMMENT 'Status',
    
    -- Forma de pagamento
    forma_pagamento VARCHAR(100) COMMENT 'Dinheiro, Cartão, Transferência',
    
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data criação',
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    INDEX idx_cliente (cliente_id) COMMENT 'Índice para buscar por cliente',
    INDEX idx_status (status) COMMENT 'Índice para filtrar pendentes',
    INDEX idx_vencimento (data_vencimento) COMMENT 'Índice para acompanhamento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Parcelas a receber de clientes';

-- ============================================================================
-- 13. TABELA: CONTAS_PAGAR
-- ============================================================================
-- Armazena parcelas a pagar para fornecedores

CREATE TABLE contas_pagar (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único da parcela',
    fornecedor_id INT NOT NULL COMMENT 'ID do fornecedor',
    
    -- Dados
    descricao VARCHAR(500) NOT NULL COMMENT 'Descrição da despesa',
    numero_parcela INT COMMENT 'Número da parcela',
    total_parcelas INT COMMENT 'Total de parcelas',
    
    -- Valores
    valor DECIMAL(10,2) NOT NULL COMMENT 'Valor da parcela',
    valor_pago DECIMAL(10,2) DEFAULT 0 COMMENT 'Quanto já foi pago',
    valor_pendente DECIMAL(10,2) COMMENT 'O que falta pagar',
    
    -- Datas
    data_vencimento DATE NOT NULL COMMENT 'Quando vence',
    data_pagamento DATE COMMENT 'Quando foi pago',
    
    -- Status
    status ENUM('pendente', 'pago', 'atrasado', 'cancelado') DEFAULT 'pendente' COMMENT 'Status',
    
    -- Categoria
    categoria VARCHAR(100) COMMENT 'Ex: Material, Serviço, Aluguel',
    
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data criação',
    
    FOREIGN KEY (fornecedor_id) REFERENCES clientes(id) ON DELETE CASCADE,
    INDEX idx_fornecedor (fornecedor_id) COMMENT 'Índice para buscar',
    INDEX idx_status (status) COMMENT 'Índice para filtrar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Parcelas a pagar para fornecedores';

-- ============================================================================
-- 14. TABELA: MOVIMENTACAO_CREDITO
-- ============================================================================
-- Registra TODAS as movimentações de crédito com rastreabilidade total
-- Cada operação gera um registro aqui

CREATE TABLE movimentacao_credito (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único da movimentação',
    cliente_id INT NOT NULL COMMENT 'ID do cliente',
    usuario_id INT NOT NULL COMMENT 'ID do usuário que fez',
    
    -- Tipo de movimentação
    tipo ENUM('credito', 'debito', 'ajuste') NOT NULL COMMENT 'Tipo: crédito, débito ou ajuste',
    
    -- Valores
    valor DECIMAL(10,2) NOT NULL COMMENT 'Valor movimentado',
    saldo_anterior DECIMAL(10,2) NOT NULL COMMENT 'Saldo antes desta operação',
    saldo_novo DECIMAL(10,2) NOT NULL COMMENT 'Saldo após operação',
    
    -- Referência
    referencia VARCHAR(100) COMMENT 'O que gerou a movimentação (Pedido #123, etc)',
    motivo TEXT COMMENT 'Motivo da movimentação',
    
    -- Rastreamento
    ip_address VARCHAR(50) COMMENT 'IP de quem fez a operação',
    
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora da operação',
    
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_cliente (cliente_id) COMMENT 'Índice para rastrear',
    INDEX idx_tipo (tipo) COMMENT 'Índice para filtrar tipo',
    INDEX idx_data (criado_em) COMMENT 'Índice para datas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Histórico completo de movimentação de crédito com rastreabilidade';

-- ============================================================================
-- 15. TABELA: HISTORICO_AUDITORIA
-- ============================================================================
-- Registra TODAS as alterações críticas no sistema
-- Para auditoria, compliance e rastreamento

CREATE TABLE historico_auditoria (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do registro',
    usuario_id INT COMMENT 'ID do usuário que fez',
    
    -- O que foi alterado
    modulo VARCHAR(100) NOT NULL COMMENT 'Módulo: clientes, produtos, etc',
    tabela VARCHAR(100) NOT NULL COMMENT 'Tabela alterada',
    registro_id INT NOT NULL COMMENT 'ID do registro alterado',
    
    -- Tipo de ação
    acao ENUM('criar', 'atualizar', 'deletar', 'login', 'logout') NOT NULL COMMENT 'Que ação foi feita',
    
    -- Dados alterados
    dados_anterior JSON COMMENT 'Valores antes da alteração',
    dados_novo JSON COMMENT 'Valores após alteração',
    
    -- Detalhes
    descricao TEXT COMMENT 'Descrição da alteração',
    ip_address VARCHAR(50) COMMENT 'IP de quem fez',
    user_agent VARCHAR(500) COMMENT 'Navegador/cliente',
    
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora da ação',
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id) COMMENT 'Índice para buscar por usuário',
    INDEX idx_modulo (modulo) COMMENT 'Índice para filtrar módulo',
    INDEX idx_acao (acao) COMMENT 'Índice para tipo de ação',
    INDEX idx_data (criado_em) COMMENT 'Índice para período'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Histórico de auditoria - rastreabilidade completa';

-- ============================================================================
-- 16. TABELA: FLUXO_CAIXA (PARA VISUALIZAÇÃO)
-- ============================================================================
-- View auxiliar para fluxo de caixa
-- Mostra previsão de entrada/saída por período

CREATE TABLE fluxo_caixa_previsto (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único',
    
    -- Dados
    data DATE NOT NULL COMMENT 'Data prevista',
    tipo ENUM('entrada', 'saida') NOT NULL COMMENT 'Entrada ou Saída',
    categoria VARCHAR(100) COMMENT 'Categoria (Vendas, Material, etc)',
    descricao VARCHAR(500) COMMENT 'Descrição',
    
    -- Valores
    valor DECIMAL(10,2) NOT NULL COMMENT 'Valor previsto',
    
    -- Referência
    referencia_id INT COMMENT 'ID da conta a receber/pagar',
    
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data criação',
    
    INDEX idx_data (data) COMMENT 'Índice para período',
    INDEX idx_tipo (tipo) COMMENT 'Índice para entrada/saída'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT 'Fluxo de caixa previsto';

-- ============================================================================
-- DADOS DE TESTE INICIAIS
-- ============================================================================

-- Cliente de teste
INSERT INTO clientes (tipo, nome, email, telefone, limte_credito, credito_disponivel) VALUES 
('cliente', 'Cliente Teste Ltda', 'cliente@example.com', '(11) 3000-0000', 5000.00, 5000.00);

-- Material de teste (Chapa)
INSERT INTO materiais (tipo, nome, largura_mm, comprimento_mm, espessura_mm, area_mm2, preco_unitario) VALUES 
('chapa', 'Chapa MDF 3mm', 1000, 1000, 3, 1000000, 150.00);

-- Material de teste (Insumo)
INSERT INTO materiais (tipo, nome, unidade_medida, preco_unitario) VALUES 
('insumo', 'Spray acrílico', 'un', 25.00);

-- Custo de teste
INSERT INTO custos (nome, tipo, unidade, valor) VALUES 
('Corte Laser', 'variavel', 'minuto', 5.00);

-- ============================================================================
-- CRIAÇÃO COMPLETA - FIM
-- ============================================================================

/*
PRÓXIMOS PASSOS:

1. Executar este script no MySQL:
   mysql -u seu_usuario -p erp_laser < etapa2_banco_dados.sql

2. Verificar se todas as tabelas foram criadas:
   SHOW TABLES;

3. Testar integridade das chaves estrangeiras:
   SELECT * FROM usuarios;
   SELECT * FROM clientes;
   SELECT * FROM materiais;

4. PRÓXIMA ETAPA: Criar Model classes para cada tabela
   - ClienteModel
   - MaterialModel
   - CustoModel
   - SimulacaoModel
   - ProdutoModel
   - OrcamentoModel
   - PedidoModel
   - E assim por diante...

Comentários no código explicam cada campo.
Use índices para melhorar performance.
*/
