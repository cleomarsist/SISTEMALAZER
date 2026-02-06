-- db/erp_schema.sql
-- Modelo completo do banco de dados ERP Fênix Magazine Personalizados
-- Módulo: Banco de Dados
-- Etapa: Modelagem inicial

-- Criar banco de dados e selecionar
CREATE DATABASE IF NOT EXISTS fenix_magazine;
USE fenix_magazine;

-- 1. Usuários
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Clientes e Fornecedores
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    document VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    type ENUM('cliente','fornecedor') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Materiais (chapas e insumos)
CREATE TABLE materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('chapa','insumo') NOT NULL,
    unit VARCHAR(10) NOT NULL,
    stock DECIMAL(10,2) DEFAULT 0,
    min_stock DECIMAL(10,2) DEFAULT 0,
    cost DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. Produtos simples
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    unit VARCHAR(10) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 5. Kits de produtos
CREATE TABLE product_kits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 6. Itens de kits
CREATE TABLE kit_items (
    kit_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (kit_id, product_id),
    FOREIGN KEY (kit_id) REFERENCES product_kits(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 7. Materiais por produto
CREATE TABLE product_materials (
    product_id INT NOT NULL,
    material_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (product_id, material_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE
);

-- 8. Custos fixos e variáveis
CREATE TABLE costs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('fixo','variavel') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 9. Margens
CREATE TABLE margins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    value DECIMAL(5,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 10. Simulações
CREATE TABLE simulations (
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
CREATE TABLE budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    simulation_id INT,
    client_id INT,
    user_id INT,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('aberto','aprovado','rejeitado') NOT NULL DEFAULT 'aberto',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (simulation_id) REFERENCES simulations(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 12. Pedidos
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    budget_id INT,
    client_id INT,
    user_id INT,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('aberto','em_producao','finalizado','cancelado') NOT NULL DEFAULT 'aberto',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (budget_id) REFERENCES budgets(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 13. Contas a receber
CREATE TABLE accounts_receivable (
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
CREATE TABLE accounts_payable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT,
    due_date DATE NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    status ENUM('aberto','pago','atrasado') NOT NULL DEFAULT 'aberto',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES clients(id)
);

-- 15. Movimentação de crédito
CREATE TABLE credit_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    value DECIMAL(10,2) NOT NULL,
    type ENUM('entrada','saida') NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- 16. Histórico/Auditoria
CREATE TABLE audit_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
