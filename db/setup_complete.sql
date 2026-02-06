-- db/setup_complete.sql
-- Script completo de setup: Banco + Estrutura + Dados + Índices
-- Módulo: Banco de Dados
-- Etapa: Setup completo do sistema

-- ============================================================================
-- PARTE 1: CRIAR BANCO E TABELAS
-- ============================================================================

CREATE DATABASE IF NOT EXISTS fenix_magazine;
USE fenix_magazine;

-- 1. Usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Clientes e Fornecedores
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    document VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    type ENUM('cliente','fornecedor') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Materiais (chapas e insumos)
CREATE TABLE IF NOT EXISTS materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('chapa','insumo') NOT NULL,
    unit VARCHAR(10) NOT NULL,
    stock DECIMAL(10,2) DEFAULT 0,
    min_stock DECIMAL(10,2) DEFAULT 0,
    cost DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. Produtos simples
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    unit VARCHAR(10) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 5. Kits de produtos
CREATE TABLE IF NOT EXISTS product_kits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 6. Itens de kits
CREATE TABLE IF NOT EXISTS kit_items (
    kit_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (kit_id, product_id),
    FOREIGN KEY (kit_id) REFERENCES product_kits(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 7. Materiais por produto
CREATE TABLE IF NOT EXISTS product_materials (
    product_id INT NOT NULL,
    material_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (product_id, material_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE
);

-- 8. Custos fixos e variáveis
CREATE TABLE IF NOT EXISTS costs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('fixo','variavel') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 9. Margens
CREATE TABLE IF NOT EXISTS margins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    value DECIMAL(5,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 10. Simulações
CREATE TABLE IF NOT EXISTS simulations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    user_id INT,
    description TEXT,
    total DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 11. Orçamentos
CREATE TABLE IF NOT EXISTS budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    simulation_id INT,
    client_id INT,
    user_id INT,
    total DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    status ENUM('aberto','aprovado','rejeitado') NOT NULL DEFAULT 'aberto',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (simulation_id) REFERENCES simulations(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 12. Pedidos
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    budget_id INT,
    client_id INT,
    user_id INT,
    total DECIMAL(10,2) NOT NULL,
    observations TEXT,
    status ENUM('aberto','em_producao','finalizado','cancelado') NOT NULL DEFAULT 'aberto',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (budget_id) REFERENCES budgets(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 13. Contas a receber
CREATE TABLE IF NOT EXISTS accounts_receivable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    client_id INT,
    due_date DATE NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    status ENUM('aberto','pago','atrasado') NOT NULL DEFAULT 'aberto',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- 14. Contas a pagar
CREATE TABLE IF NOT EXISTS accounts_payable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT,
    due_date DATE NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    status ENUM('aberto','pago','atrasado') NOT NULL DEFAULT 'aberto',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES clients(id)
);

-- 15. Movimentação de crédito
CREATE TABLE IF NOT EXISTS credit_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    value DECIMAL(10,2) NOT NULL,
    type ENUM('entrada','saida') NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- 16. Histórico/Auditoria
CREATE TABLE IF NOT EXISTS audit_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ============================================================================
-- PARTE 2: CRIAR ÍNDICES PARA PERFORMANCE
-- ============================================================================
-- Comentário: Índices melhoram velocidade de buscas e filtros
-- Se já existir índice, MySQL ignora (seguro de executar múltiplas vezes)

CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_clients_type ON clients(type);
CREATE INDEX idx_clients_document ON clients(document);
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_materials_type ON materials(type);
CREATE INDEX idx_materials_stock ON materials(stock);
CREATE INDEX idx_orders_client_id ON orders(client_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_budgets_client_id ON budgets(client_id);
CREATE INDEX idx_budgets_status ON budgets(status);
CREATE INDEX idx_accounts_receivable_client ON accounts_receivable(client_id);
CREATE INDEX idx_accounts_receivable_status ON accounts_receivable(status);
CREATE INDEX idx_audit_user_id ON audit_history(user_id);

-- ============================================================================
-- PARTE 3: INSERIR DADOS INICIAIS
-- ============================================================================

-- Usuários padrão
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$YIjlrDwiM.ppFN3T0G2cueSMQLj0XHhDOo9qSRG6gKxh7M5S9MKDK', 'admin'),
('gerente', '$2y$10$YIjlrDwiM.ppFN3T0G2cueSMQLj0XHhDOo9qSRG6gKxh7M5S9MKDK', 'gerente'),
('vendedor', '$2y$10$YIjlrDwiM.ppFN3T0G2cueSMQLj0XHhDOo9qSRG6gKxh7M5S9MKDK', 'vendedor'),
('usuario', '$2y$10$YIjlrDwiM.ppFN3T0G2cueSMQLj0XHhDOo9qSRG6gKxh7M5S9MKDK', 'usuario');

-- Clientes
INSERT INTO clients (name, document, email, phone, address, type) VALUES
('São Paulo Comercio LTDA', '12345678000191', 'contato@spcomercio.com.br', '1133334444', 'Avenida Paulista, 1000, São Paulo, SP', 'cliente'),
('Fornecedor Chapas Brazil', '11223344000155', 'vendas@chapas.com.br', '1144447777', 'Rua Industrial, 100, Guarulhos, SP', 'fornecedor');

-- Produtos
INSERT INTO products (name, description, unit, price) VALUES
('Placa Acrílico Transparente 3mm', 'Placa acrílico transparente para gravação a laser', 'placa', 85.00),
('Placa MDF 3mm', 'Placa MDF para corte a laser', 'placa', 45.00),
('Gravação em Acrílico', 'Serviço de gravação personalizada', 'serviço', 150.00);

-- Materiais
INSERT INTO materials (name, type, unit, stock, min_stock, cost) VALUES
('Acrílico Transparente 3mm', 'chapa', 'placa', 50, 10, 45.00),
('MDF 3mm', 'chapa', 'placa', 100, 20, 25.00),
('Tinta Acrílica Preta', 'insumo', 'litro', 10, 2, 35.00);

-- Custos
INSERT INTO costs (name, type, value) VALUES
('Aluguel Galpão', 'fixo', 5000.00),
('Energia Elétrica', 'fixo', 3000.00),
('Embalagem por unidade', 'variavel', 5.00);

-- Margens
INSERT INTO margins (name, value) VALUES
('Margem Padrão Produtos', 45.00),
('Margem Premium Serviços', 60.00);

-- ============================================================================
-- PARTE 4: CRIAR VIEWS
-- ============================================================================

CREATE OR REPLACE VIEW vw_sales_report AS
SELECT 
    o.id as order_id,
    o.created_at,
    c.name as client_name,
    o.total,
    o.status
FROM orders o
JOIN clients c ON o.client_id = c.id
ORDER BY o.created_at DESC;

CREATE OR REPLACE VIEW vw_open_budgets AS
SELECT 
    b.id,
    b.created_at,
    c.name as client_name,
    b.total,
    b.status
FROM budgets b
JOIN clients c ON b.client_id = c.id
WHERE b.status = 'aberto'
ORDER BY b.created_at DESC;

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

-- ============================================================================
-- PARTE 5: VERIFICAÇÃO FINAL
-- ============================================================================

-- Contar registros inseridos
SELECT 
    (SELECT COUNT(*) FROM users) as total_usuarios,
    (SELECT COUNT(*) FROM clients) as total_clientes,
    (SELECT COUNT(*) FROM products) as total_produtos,
    (SELECT COUNT(*) FROM materials) as total_materiais,
    (SELECT COUNT(*) FROM costs) as total_custos;

-- ============================================================================
-- SETUP COMPLETO FINALIZADO
-- ============================================================================
-- Comentário: Banco de dados pronto para uso
-- Acesse: http://localhost/SISTEMAIA/ControleInvestimento/
-- Senhas padrão: Senha123
