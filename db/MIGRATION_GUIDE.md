# Guia de Atualização do Banco de Dados

## 📋 Descrição dos Scripts de Migração

O sistema ERP Fênix Magazine Personalizados possui 3 scripts SQL de manutenção:

### 1️⃣ **001_insert_initial_data.sql**
**Objetivo**: Popular o banco com dados iniciais

#### Insere:
- ✅ 4 usuários padrão (admin, gerente, vendedor, usuario)
- ✅ 5 clientes/fornecedores de exemplo
- ✅ 7 produtos simples
- ✅ 3 kits de produtos com seus itens
- ✅ 9 materiais (chapas e insumos)
- ✅ Relação produto-material (consumo)
- ✅ 6 custos fixos e 3 variáveis
- ✅ 4 margens padrão

#### Senhas Padrão:
```
Usuário: admin / Senha: Senha123
Usuário: gerente / Senha: Senha123
Usuário: vendedor / Senha: Senha123
Usuário: usuario / Senha: Senha123
```

**Hash bcrypt**: `$2y$10$YIjlrDwiM.ppFN3T0G2cueSMQLj0XHhDOo9qSRG6gKxh7M5S9MKDK`

---

### 2️⃣ **002_maintenance_updates.sql**
**Objetivo**: Otimizar performance e adicionar funcionalidades avançadas

#### Inclui:
- ✅ **Índices** em todas as tabelas principais para buscas rápidas
- ✅ **Views** para:
  - Relatório de vendas
  - Orçamentos em aberto
  - Contas a receber/pagar abertas
  - Análise de estoque
- ✅ **Stored Procedures** para:
  - Calcular receita total
  - Obter contas vencidas
  - Registrar auditoria com transação
- ✅ **Triggers** para auditoria automática em alterações
- ✅ **Campos adicionais** (updated_at, is_active, discount, observations)

---

### 3️⃣ **003_backup_and_cleanup.sql**
**Objetivo**: Backup, limpeza e relatórios analíticos

#### Contém:
- ✅ **OPTIMIZE TABLE** - Otimizar todas as tabelas
- ✅ **Relatórios**:
  - Relatório de estoque (com recomendações)
  - Análise de vendas por data
  - Clientes mais ativos
  - Contas a receber vs pagar
  - Auditoria - últimas ações
- ✅ **Verificação de integridade** de dados
- ✅ **Estatísticas** do banco (tamanho das tabelas)
- ✅ **Comandos de manutenção** avançada

---

## 🚀 Como Usar

### Executar Script Completo:
```bash
# Criar banco, tabelas e popular dados
mysql -u root -p < db/erp_schema.sql
mysql -u root -p < db/migrations/001_insert_initial_data.sql

# Otimizar e adicionar índices
mysql -u root -p < db/migrations/002_maintenance_updates.sql

# Gerar relatórios
mysql -u root -p < db/migrations/003_backup_and_cleanup.sql
```

### Executar em phpMyAdmin:
1. Abra phpMyAdmin
2. Selecione o banco `fenix_magazine`
3. Clique em **SQL**
4. Copie o conteúdo do script
5. Clique **Executar**

---

## ⚠️ CUIDADOS IMPORTANTES

### ❌ NÃO EXECUTE EM PRODUÇÃO:
```sql
-- Estes comandos deletam dados (comentados por segurança):
DELETE FROM orders WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 YEAR);
DELETE FROM audit_history WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

### ✅ SEMPRE FAÇA BACKUP ANTES:
```bash
# Backup do banco
mysqldump -u root -p fenix_magazine > backup_fenix.sql

# Restaurar do backup
mysql -u root -p fenix_magazine < backup_fenix.sql
```

### 🔒 SEGURANÇA:
- Altere as senhas padrão após primeira execução
- Use usuários com permissões mínimas em produção
- Não compartilhe credenciais de admin
- Ative logs de auditoria para rastreabilidade

---

## 📊 Verificar Dados Inseridos

Após executar os scripts, verifique:

```sql
-- Contar registros
SELECT COUNT(*) as usuarios FROM users;
SELECT COUNT(*) as clientes FROM clients;
SELECT COUNT(*) as produtos FROM products;
SELECT COUNT(*) as pedidos FROM orders;

-- Ver estoque crítico
SELECT name, stock, min_stock FROM materials 
WHERE stock < min_stock;

-- Ver contas vencidas
SELECT * FROM vw_open_receivables 
WHERE days_until_due < 0;

-- Ver clientes mais ativos
SELECT c.name, COUNT(o.id) as pedidos 
FROM clients c 
LEFT JOIN orders o ON c.id = o.client_id 
GROUP BY c.id 
ORDER BY pedidos DESC;
```

---

## 🔄 Atualização Incremental

Para adicionar novos dados sem perder existentes:

```sql
-- Novo cliente
INSERT INTO clients (name, document, email, phone, address, type) VALUES
('Nova Empresa', '11111111000181', 'novo@empresa.com', '1199999999', 'Endereço', 'cliente');

-- Novo produto
INSERT INTO products (name, description, unit, price) VALUES
('Novo Produto', 'Descrição', 'unidade', 99.99);

-- Novo usuário
INSERT INTO users (username, password, role) VALUES
('novo_usuario', '$2y$10$YIjlrDwiM.ppFN3T0G2cueSMQLj0XHhDOo9qSRG6gKxh7M5S9MKDK', 'vendedor');
```

---

## 📈 Monitoramento Contínuo

Recomendações:

1. **Diária**: Verificar contas vencidas
2. **Semanal**: Analisar vendas e estoque crítico
3. **Mensal**: Executar OPTIMIZE TABLE
4. **Trimestral**: Revisar auditoria e fazer backup
5. **Anual**: Limpeza de dados obsoletos

---

## 🆘 Troubleshooting

### Erro: "No database selected"
```sql
USE fenix_magazine;
```

### Erro: "Constraint violated"
```sql
-- Verificar integridade referencial
SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'fenix_magazine';
```

### Banco muito lento
```sql
-- Executar otimização completa
OPTIMIZE TABLE [tabela];
ANALYZE TABLE [tabela];
```

### Recuperar dados acidentalmente deletados
```bash
# Se tiver backup
mysql -u root -p fenix_magazine < backup_fenix.sql
```

---

## 📞 Suporte

Para dúvidas sobre os scripts SQL, consulte:
- Comentários no próprio arquivo SQL
- Documentação MySQL oficial
- Logs de erro do servidor MySQL

**Data última atualização**: 06/02/2026
