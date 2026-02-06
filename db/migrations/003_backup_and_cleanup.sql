-- db/migrations/003_backup_and_cleanup.sql
-- Script para backup, limpeza de dados de teste e operações especiais
-- Módulo: Banco de Dados
-- Etapa: Backup e manutenção

-- ============================================================================
-- BACKUP: EXPORTS DE DADOS CRÍTICOS
-- ============================================================================
-- Comentário: Estes comandos devem ser executados como SELECT INTO para gerar arquivos

-- Export de usuários para backup
-- SELECT * FROM users INTO OUTFILE '/var/lib/mysql/backup_users.csv'
-- FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n';

-- Export de clientes para backup
-- SELECT * FROM clients INTO OUTFILE '/var/lib/mysql/backup_clients.csv'
-- FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n';

-- ============================================================================
-- LIMPEZA DE DADOS DE TESTE
-- ============================================================================
-- Comentário: Limpar dados obsoletos ou de teste (CUIDADO: operação irreversível)

-- Deletar transações com mais de 2 anos
-- DELETE FROM orders WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 YEAR) AND status = 'cancelado';

-- Deletar auditoria antiga (mais de 1 ano)
-- DELETE FROM audit_history WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Produtos inativos
-- DELETE FROM products WHERE is_active = 0 AND created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);

-- ============================================================================
-- OTIMIZAÇÃO DO BANCO
-- ============================================================================
-- Comentário: Operações para manter saúde do banco

-- Otimizar todas as tabelas
OPTIMIZE TABLE users;
OPTIMIZE TABLE clients;
OPTIMIZE TABLE products;
OPTIMIZE TABLE product_kits;
OPTIMIZE TABLE kit_items;
OPTIMIZE TABLE materials;
OPTIMIZE TABLE product_materials;
OPTIMIZE TABLE costs;
OPTIMIZE TABLE margins;
OPTIMIZE TABLE orders;
OPTIMIZE TABLE budgets;
OPTIMIZE TABLE simulations;
OPTIMIZE TABLE accounts_receivable;
OPTIMIZE TABLE accounts_payable;
OPTIMIZE TABLE credit_movements;
OPTIMIZE TABLE audit_history;

-- ============================================================================
-- RELATÓRIOS DE ANÁLISE
-- ============================================================================

-- Relatório 1: Resumo do estoque
SELECT 
    'RELATÓRIO DE ESTOQUE' as titulo,
    NOW() as data_relatorio;

SELECT 
    m.name,
    m.type,
    m.stock,
    m.min_stock,
    m.cost,
    m.stock * m.cost as valor_total,
    CASE 
        WHEN m.stock < m.min_stock THEN 'CRÍTICO - COMPRAR'
        WHEN m.stock <= (m.min_stock * 1.5) THEN 'BAIXO - MONITORAR'
        ELSE 'ESTOQUE OK'
    END as recomendacao
FROM materials m
ORDER BY m.type, m.stock ASC;

-- Relatório 2: Análise de vendas
SELECT 
    'RELATÓRIO DE VENDAS' as titulo,
    NOW() as data_relatorio;

SELECT 
    DATE(o.created_at) as data_venda,
    COUNT(o.id) as total_pedidos,
    SUM(o.total) as valor_total,
    AVG(o.total) as ticket_medio,
    o.status
FROM orders o
GROUP BY DATE(o.created_at), o.status
ORDER BY data_venda DESC;

-- Relatório 3: Clientes mais ativos
SELECT 
    c.name,
    c.type,
    COUNT(o.id) as total_pedidos,
    SUM(o.total) as valor_total,
    MAX(o.created_at) as ultima_compra
FROM clients c
LEFT JOIN orders o ON c.id = o.client_id
GROUP BY c.id
ORDER BY valor_total DESC
LIMIT 10;

-- Relatório 4: Contas a receber vs a pagar
SELECT 
    'CONTAS A RECEBER' as tipo_conta,
    COUNT(*) as total_contas,
    SUM(value) as valor_total,
    SUM(CASE WHEN status = 'pago' THEN value ELSE 0 END) as valor_pago,
    SUM(CASE WHEN status = 'aberto' THEN value ELSE 0 END) as valor_aberto,
    SUM(CASE WHEN status = 'atrasado' THEN value ELSE 0 END) as valor_atrasado
FROM accounts_receivable

UNION ALL

SELECT 
    'CONTAS A PAGAR' as tipo_conta,
    COUNT(*) as total_contas,
    SUM(value) as valor_total,
    SUM(CASE WHEN status = 'pago' THEN value ELSE 0 END) as valor_pago,
    SUM(CASE WHEN status = 'aberto' THEN value ELSE 0 END) as valor_aberto,
    SUM(CASE WHEN status = 'atrasado' THEN value ELSE 0 END) as valor_atrasado
FROM accounts_payable;

-- Relatório 5: Auditoria - últimas ações
SELECT 
    ah.created_at as data_acao,
    u.username as usuario,
    ah.action as tipo_acao,
    ah.table_name as tabela_afetada,
    ah.record_id as id_registro,
    ah.description as descricao
FROM audit_history ah
LEFT JOIN users u ON ah.user_id = u.id
ORDER BY ah.created_at DESC
LIMIT 50;

-- ============================================================================
-- VERIFICAÇÃO DE INTEGRIDADE
-- ============================================================================

-- Comentário: Verificar se existem tabelas órfãs ou referências quebradas

-- Verificar produtos sem kit
SELECT p.id, p.name 
FROM products p
WHERE p.id NOT IN (SELECT product_id FROM kit_items)
AND p.id NOT IN (SELECT product_id FROM product_materials);

-- Verificar kits vazios
SELECT pk.id, pk.name 
FROM product_kits pk
WHERE pk.id NOT IN (SELECT kit_id FROM kit_items);

-- Verificar orçamentos sem cliente
SELECT b.id, b.total 
FROM budgets b
WHERE b.client_id NOT IN (SELECT id FROM clients);

-- Verificar pedidos sem orçamento válido
SELECT o.id, o.total 
FROM orders o
WHERE o.budget_id IS NOT NULL 
AND o.budget_id NOT IN (SELECT id FROM budgets);

-- ============================================================================
-- ESTATÍSTICAS DO BANCO
-- ============================================================================

-- Tamanho das tabelas
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb,
    table_rows
FROM information_schema.TABLES
WHERE table_schema = 'fenix_magazine'
ORDER BY (data_length + index_length) DESC;

-- Número de registros por tabela
SELECT 
    'users' as tabela, COUNT(*) as total FROM users
UNION ALL SELECT 'clients', COUNT(*) FROM clients
UNION ALL SELECT 'products', COUNT(*) FROM products
UNION ALL SELECT 'materials', COUNT(*) FROM materials
UNION ALL SELECT 'orders', COUNT(*) FROM orders
UNION ALL SELECT 'budgets', COUNT(*) FROM budgets
UNION ALL SELECT 'accounts_receivable', COUNT(*) FROM accounts_receivable
UNION ALL SELECT 'accounts_payable', COUNT(*) FROM accounts_payable
UNION ALL SELECT 'audit_history', COUNT(*) FROM audit_history;

-- ============================================================================
-- COMANDOS DE MANUTENÇÃO AVANÇADA
-- ============================================================================

-- Recriar índices (executar se houver fragmentação)
-- ANALYZE TABLE users;
-- REPAIR TABLE users;

-- Verificar integridade
-- CHECK TABLE users;
-- CHECK TABLE clients;
-- CHECK TABLE orders;

-- ============================================================================
-- FIM DO SCRIPT DE BACKUP E LIMPEZA
-- ============================================================================
-- Comentário: Execute regularmente para manter o banco saudável e otimizado
