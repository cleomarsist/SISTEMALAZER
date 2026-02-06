-- db/migrations/002_maintenance_updates.sql
-- Script de manutenção e atualizações do banco de dados
-- Módulo: Banco de Dados
-- Etapa: Manutenção e índices para performance

-- ============================================================================
-- CRIAR ÍNDICES PARA PERFORMANCE
-- ============================================================================
-- Comentário: Índices melhoram velocidade de buscas e filtros

-- Índices em users
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role ON users(role);

-- Índices em clients
CREATE INDEX idx_clients_type ON clients(type);
CREATE INDEX idx_clients_document ON clients(document);
CREATE INDEX idx_clients_name ON clients(name);

-- Índices em products
CREATE INDEX idx_products_name ON products(name);

-- Índices em materials
CREATE INDEX idx_materials_type ON materials(type);
CREATE INDEX idx_materials_stock ON materials(stock);

-- Índices em orders
CREATE INDEX idx_orders_client_id ON orders(client_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created_at ON orders(created_at);

-- Índices em budgets
CREATE INDEX idx_budgets_client_id ON budgets(client_id);
CREATE INDEX idx_budgets_status ON budgets(status);
CREATE INDEX idx_budgets_created_at ON budgets(created_at);

-- Índices em contas
CREATE INDEX idx_accounts_receivable_client ON accounts_receivable(client_id);
CREATE INDEX idx_accounts_receivable_status ON accounts_receivable(status);
CREATE INDEX idx_accounts_payable_supplier ON accounts_payable(supplier_id);
CREATE INDEX idx_accounts_payable_status ON accounts_payable(status);

-- Índices em auditoria
CREATE INDEX idx_audit_user_id ON audit_history(user_id);
CREATE INDEX idx_audit_table_name ON audit_history(table_name);
CREATE INDEX idx_audit_created_at ON audit_history(created_at);

-- ============================================================================
-- ADICIONAR CAMPOS ADICIONAIS (Se necessário)
-- ============================================================================
-- Comentário: Adicionar campos úteis para rastreamento

-- Adicionar campo de atualização em clients
ALTER TABLE clients ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Adicionar campo de ativo/inativo em produtos
ALTER TABLE products ADD COLUMN is_active BOOLEAN DEFAULT 1;
ALTER TABLE materials ADD COLUMN is_active BOOLEAN DEFAULT 1;

-- Adicionar campo de desconto em orçamentos
ALTER TABLE budgets ADD COLUMN discount DECIMAL(10,2) DEFAULT 0;

-- Adicionar campo de observações em pedidos
ALTER TABLE orders ADD COLUMN observations TEXT;

-- ============================================================================
-- CRIAR VIEWS ÚTEIS
-- ============================================================================

-- View para relatório de vendas por período
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

-- View para orçamentos em aberto
CREATE OR REPLACE VIEW vw_open_budgets AS
SELECT 
    b.id,
    b.created_at,
    c.name as client_name,
    b.total,
    b.status,
    u.username as created_by,
    DATEDIFF(NOW(), b.created_at) as days_open
FROM budgets b
JOIN clients c ON b.client_id = c.id
JOIN users u ON b.user_id = u.id
WHERE b.status = 'aberto'
ORDER BY b.created_at DESC;

-- View para contas a receber em aberto
CREATE OR REPLACE VIEW vw_open_receivables AS
SELECT 
    ar.id,
    ar.due_date,
    c.name as client_name,
    ar.value,
    ar.status,
    DATEDIFF(ar.due_date, NOW()) as days_until_due,
    CASE WHEN ar.status = 'atrasado' THEN ar.value ELSE 0 END as overdue_amount
FROM accounts_receivable ar
JOIN clients c ON ar.client_id = c.id
WHERE ar.status IN ('aberto', 'atrasado')
ORDER BY ar.due_date ASC;

-- View para contas a pagar em aberto
CREATE OR REPLACE VIEW vw_open_payables AS
SELECT 
    ap.id,
    ap.due_date,
    c.name as supplier_name,
    ap.value,
    ap.status,
    DATEDIFF(ap.due_date, NOW()) as days_until_due
FROM accounts_payable ap
JOIN clients c ON ap.supplier_id = c.id
WHERE ap.status IN ('aberto', 'atrasado')
ORDER BY ap.due_date ASC;

-- View para análise de estoque
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
-- STORED PROCEDURES ÚTEIS
-- ============================================================================

-- Procedure para calcular receita total
DELIMITER $$

CREATE PROCEDURE sp_calculate_total_revenue(OUT total DECIMAL(15,2))
BEGIN
    SELECT COALESCE(SUM(total), 0) INTO total FROM orders WHERE status != 'cancelado';
END$$

-- Procedure para obter contas vencidas
CREATE PROCEDURE sp_get_overdue_accounts(IN var_days INT)
BEGIN
    SELECT * FROM vw_open_receivables WHERE days_until_due <= var_days AND days_until_due < 0;
END$$

-- Procedure para registrar auditoria com transação
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

-- ============================================================================
-- ATUALIZAÇÕES E CORREÇÕES
-- ============================================================================

-- Atualizar estoque de material com base em consumo
-- (exemplo: depois de criar um pedido, descontar material usado)

-- Exemplo de trigger para auditoria automática
DELIMITER $$

CREATE TRIGGER trg_audit_orders_update
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status != OLD.status THEN
        INSERT INTO audit_history (user_id, action, table_name, record_id, description)
        VALUES (1, 'UPDATE', 'orders', NEW.id, CONCAT('Status alterado de ', OLD.status, ' para ', NEW.status));
    END IF;
END$$

CREATE TRIGGER trg_audit_orders_insert
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    INSERT INTO audit_history (user_id, action, table_name, record_id, description)
    VALUES (1, 'CREATE', 'orders', NEW.id, 'Novo pedido criado');
END$$

DELIMITER ;

-- ============================================================================
-- VERIFICAR INTEGRIDADE REFERENCIAL
-- ============================================================================

-- Comentário: Verificar se todas as chaves estrangeiras estão corretas
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'fenix_magazine' AND REFERENCED_TABLE_NAME IS NOT NULL;

-- ============================================================================
-- FIM DAS ATUALIZAÇÕES
-- ============================================================================
-- Comentário: Índices, views e stored procedures criados com sucesso
-- Próximo passo: Otimizar queries de performance no sistema
