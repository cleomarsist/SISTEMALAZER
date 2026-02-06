-- db/migrations/001_insert_initial_data.sql
-- Script de atualização do banco de dados com dados iniciais
-- Módulo: Banco de Dados
-- Etapa: Populaçao inicial de dados

-- ============================================================================
-- 1. INSERIR USUÁRIOS PADRÃO
-- ============================================================================
-- Comentário: Criar usuários com diferentes roles para teste do sistema
-- Senha padrão: usar password_hash() no PHP antes de inserir em produção

INSERT INTO users (username, password, role, created_at) VALUES
('admin', '$2y$10$YIjlrDwiM.ppFN3T0G2cueSMQLj0XHhDOo9qSRG6gKxh7M5S9MKDK', 'admin', NOW()),
('gerente', '$2y$10$YIjlrDwiM.ppFN3T0G2cueSMQLj0XHhDOo9qSRG6gKxh7M5S9MKDK', 'gerente', NOW()),
('vendedor', '$2y$10$YIjlrDwiM.ppFN3T0G2cueSMQLj0XHhDOo9qSRG6gKxh7M5S9MKDK', 'vendedor', NOW()),
('usuario', '$2y$10$YIjlrDwiM.ppFN3T0G2cueSMQLj0XHhDOo9qSRG6gKxh7M5S9MKDK', 'usuario', NOW());

-- Comentário: Senhas padrão: "Senha123" (bcrypt hash)

-- ============================================================================
-- 2. INSERIR CLIENTES PADRÃO
-- ============================================================================
-- Comentário: Criar clientes de exemplo para testes

INSERT INTO clients (name, document, email, phone, address, type, created_at) VALUES
('São Paulo Comercio LTDA', '12345678000191', 'contato@spcomercio.com.br', '1133334444', 'Avenida Paulista, 1000, São Paulo, SP', 'cliente', NOW()),
('Rio Design Studio', '98765432000109', 'vendas@riodesign.com.br', '2122225555', 'Rua das Flores, 500, Rio de Janeiro, RJ', 'cliente', NOW()),
('Belo Horizonte Presentes', '55443322000164', 'info@bhpresentes.com.br', '3133336666', 'Avenida Afonso Pena, 300, Belo Horizonte, MG', 'cliente', NOW()),
('Fornecedor Chapas Brazil', '11223344000155', 'vendas@chapas.com.br', '1144447777', 'Rua Industrial, 100, Guarulhos, SP', 'fornecedor', NOW()),
('Insumos Premium LTDA', '22334455000166', 'contato@insumospremium.com.br', '1155558888', 'Avenida das Nações, 200, São Paulo, SP', 'fornecedor', NOW());

-- ============================================================================
-- 3. INSERIR PRODUTOS SIMPLES
-- ============================================================================
-- Comentário: Produtos base que podem ser vendidos isoladamente

INSERT INTO products (name, description, unit, price, created_at) VALUES
('Placa Acrílico Transparente 3mm', 'Placa de acrílico transparente com espessura de 3mm, ideal para gravação a laser', 'placa', 85.00, NOW()),
('Placa MDF 3mm', 'Placa de MDF com espessura de 3mm para corte a laser', 'placa', 45.00, NOW()),
('Placa MDF 6mm', 'Placa de MDF com espessura de 6mm para projetos maiores', 'placa', 75.00, NOW()),
('Gravação em Acrílico', 'Serviço de gravação personalizada em acrílico', 'serviço', 150.00, NOW()),
('Corte em MDF', 'Serviço de corte personalizado em MDF', 'serviço', 120.00, NOW()),
('Estrutura de Alumínio', 'Perfil de alumínio para encadernação de projetos', 'unidade', 35.00, NOW()),
('Parafusos de Alumínio', 'Kit parafusos para fixação em estruturas', 'kit', 15.00, NOW());

-- ============================================================================
-- 4. INSERIR KITS DE PRODUTOS
-- ============================================================================
-- Comentário: Kits combinados para ofertas especiais

INSERT INTO product_kits (name, description, price, created_at) VALUES
('Kit Iniciante Corte a Laser', 'Kit completo com materiais e serviços para começar em corte a laser', 450.00, NOW()),
('Kit Plaqueta Personalizada', 'Kit com acrílico, pintura e estrutura para plaqueta', 280.00, NOW()),
('Kit Brinde Corporativo', 'Kit para criação de brindes personalizados em lote', 650.00, NOW());

-- ============================================================================
-- 5. INSERIR ITENS DO KIT
-- ============================================================================
-- Comentário: Associar produtos aos kits

INSERT INTO kit_items (kit_id, product_id, quantity) VALUES
(1, 1, 2),      -- Kit Iniciante: 2 placas acrílico
(1, 2, 3),      -- Kit Iniciante: 3 placas MDF
(1, 4, 1),      -- Kit Iniciante: 1 serviço de gravação
(2, 1, 1),      -- Kit Plaqueta: 1 placa acrílico
(2, 6, 2),      -- Kit Plaqueta: 2 estruturas alumínio
(3, 2, 5),      -- Kit Brinde: 5 placas MDF
(3, 5, 1);      -- Kit Brinde: 1 serviço de corte

-- ============================================================================
-- 6. INSERIR MATERIAIS (CHAPAS E INSUMOS)
-- ============================================================================
-- Comentário: Materiais estoque para controle de produção

INSERT INTO materials (name, type, unit, stock, min_stock, cost, created_at) VALUES
('Acrílico Transparente 3mm', 'chapa', 'placa', 50, 10, 45.00, NOW()),
('Acrílico Fumê 3mm', 'chapa', 'placa', 30, 5, 50.00, NOW()),
('MDF 3mm', 'chapa', 'placa', 100, 20, 25.00, NOW()),
('MDF 6mm', 'chapa', 'placa', 80, 15, 40.00, NOW()),
('Policarbonato 3mm', 'chapa', 'placa', 20, 5, 65.00, NOW()),
('Tinta Acrílica Preta', 'insumo', 'litro', 10, 2, 35.00, NOW()),
('Tinta Acrílica Branca', 'insumo', 'litro', 8, 2, 35.00, NOW()),
('Verniz Acrílico Brilho', 'insumo', 'litro', 5, 1, 45.00, NOW()),
('Adesivo Industrial', 'insumo', 'frasco', 15, 3, 25.00, NOW());

-- ============================================================================
-- 7. INSERIR RELAÇÃO PRODUTO-MATERIAL
-- ============================================================================
-- Comentário: Definir quais materiais cada produto consome

INSERT INTO product_materials (product_id, material_id, quantity) VALUES
(1, 1, 1),      -- Placa Acrílico: 1 placa acrílico transparente
(2, 3, 1),      -- Placa MDF 3mm: 1 placa MDF 3mm
(3, 4, 1),      -- Placa MDF 6mm: 1 placa MDF 6mm
(4, 1, 0.5),    -- Gravação em Acrílico: 0.5 placa acrílico
(4, 6, 0.1),    -- Gravação em Acrílico: 0.1 litro tinta preta
(5, 3, 1),      -- Corte em MDF: 1 placa MDF
(6, 1, 0.2);    -- Estrutura Alumínio: materiais de fixação

-- ============================================================================
-- 8. INSERIR CUSTOS FIXOS
-- ============================================================================
-- Comentário: Custos que não variam com volume de produção

INSERT INTO costs (name, type, value, created_at) VALUES
('Aluguel Galpão', 'fixo', 5000.00, NOW()),
('Energia Elétrica', 'fixo', 3000.00, NOW()),
('Internet e Telefone', 'fixo', 500.00, NOW()),
('Salários Fixos', 'fixo', 15000.00, NOW()),
('Manutenção Equipamentos', 'fixo', 2000.00, NOW()),
('Seguros', 'fixo', 1500.00, NOW());

-- ============================================================================
-- 9. INSERIR CUSTOS VARIÁVEIS
-- ============================================================================
-- Comentário: Custos que variam conforme produção

INSERT INTO costs (name, type, value, created_at) VALUES
('Embalagem por unidade', 'variavel', 5.00, NOW()),
('Combustível Entrega', 'variavel', 2.50, NOW()),
('Mão de obra variável', 'variavel', 20.00, NOW());

-- ============================================================================
-- 10. INSERIR MARGENS PADRÃO
-- ============================================================================
-- Comentário: Margens de lucro para diferentes tipos de produtos

INSERT INTO margins (name, value, created_at) VALUES
('Margem Padrão Produtos', 45.00, NOW()),
('Margem Premium Serviços', 60.00, NOW()),
('Margem Kits Promocionais', 35.00, NOW()),
('Margem Brindes Corporativos', 50.00, NOW());

-- ============================================================================
-- 11. VERIFICAR INTEGRIDADE DOS DADOS
-- ============================================================================

-- Comentário: Contar registros inseridos
SELECT COUNT(*) as total_usuarios FROM users;
SELECT COUNT(*) as total_clientes FROM clients;
SELECT COUNT(*) as total_produtos FROM products;
SELECT COUNT(*) as total_kits FROM product_kits;
SELECT COUNT(*) as total_materiais FROM materials;
SELECT COUNT(*) as total_custos FROM costs;
SELECT COUNT(*) as total_margens FROM margins;

-- ============================================================================
-- FIM DA INSERÇÃO DE DADOS
-- ============================================================================
-- Comentário: Todos os dados foram inseridos com sucesso
-- Próximo passo: Testar fluxo de vendas no sistema
