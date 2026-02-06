# EXECUTAR ETAPA 2 - PASSO A PASSO

## 🎯 Objetivo
Criar o banco de dados MySQL completo para o ERP.

---

## 📋 Pré-requisitos

✅ WAMP64 instalado
✅ MySQL rodando (ícone verde)
✅ Arquivo `etapa2_banco_dados.sql` na pasta do projeto

---

## 🚀 Opção 1: VIA phpMyAdmin (MAIS FÁCIL)

### Passo 1: Acessar phpMyAdmin
1. Abrir navegador
2. Digitar: `http://localhost/phpmyadmin`
3. Fazer login (padrão WAMP: sem usuário, sem senha)

### Passo 2: Criar Banco de Dados
1. Clicar em "Novo" (ou menu esquerdo)
2. Nome do banco: `erp_laser`
3. Collation: `utf8mb4_unicode_ci`
4. **Criar**

Pronto! Banco criado.

### Passo 3: Selecionar Banco
1. No menu esquerdo, clicar em `erp_laser`
2. Aba **SQL** (ou "Consultar")

### Passo 4: Executar SQL
1. **Abrir arquivo** `etapa2_banco_dados.sql` em editor de texto
2. **Copiar TUDO** (Ctrl+A, Ctrl+C)
3. **Colar no phpMyAdmin** (campo branco)
4. **Executar** (botão abaixo)

✅ PRONTO! Banco criado com 16 tabelas

### Passo 5: Verificar
1. Menu esquerdo → `erp_laser` → expandir
2. Deve listar:
   - usuarios
   - clientes
   - materiais
   - custos
   - simulacoes
   - produtos
   - ... (16 tabelas no total)

---

## 🖥️ Opção 2: VIA LINHA DE COMANDO

### Passo 1: Abrir PowerShell

**Windows 11/10**:
1. `Ctrl + X`
2. Selecionar "Terminal do Windows PowerShell"

**Ou**:
1. `Win + R`
2. Digitar: `powershell`
3. Enter

### Passo 2: Navegar até Pasta do Projeto

```powershell
cd C:\wamp64\www\SISTEMALAZER
```

Verificar se o arquivo está lá:
```powershell
ls etapa2_banco_dados.sql
```

Deve aparecer o arquivo!

### Passo 3: Executar Script

**Primeiro**, criar o banco:
```powershell
cd C:\wamp64\bin\mysql\mysql8.0.31\bin
```

**Ou** (se caminho for diferente):
```powershell
ls C:\wamp64\bin\mysql\
# Ver qual versão está aí
```

**Criar banco**:
```powershell
.\mysql -u root -p
```

Se pedir senha, deixar em branco (só pressionar Enter).

Dentro do MySQL, digitar:
```sql
CREATE DATABASE erp_laser CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Passo 4: Executar Script SQL

```powershell
cd C:\wamp64\www\SISTEMALAZER
.\mysql -u root erp_laser < etapa2_banco_dados.sql
```

Se der erro, tentar:
```powershell
C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql -u root erp_laser < etapa2_banco_dados.sql
```

✅ Se não der erro, está criado!

### Passo 5: Verificar (via phpMyAdmin)

Abrir `http://localhost/phpmyadmin`
1. Deve aparecer `erp_laser` no menu esquerdo
2. Expandir e contar 16 tabelas

---

## ⚠️ Possíveis Erros e Soluções

### Erro: "Access denied for user 'root'"

**Causa**: Senha diferente
**Solução**: 
1. Abrir `C:\wamp64\phpmyadmin\config.inc.php`
2. Procurar por `$cfg['Servers'][1]['password']`
3. Usar essa senha no comando:
```powershell
mysql -u root -p seu_password erp_laser < etapa2_banco_dados.sql
```

### Erro: "mysql: command not found"

**Causa**: MySQL não está no PATH
**Solução**: Usar caminho completo
```powershell
C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql -u root erp_laser < etapa2_banco_dados.sql
```

### Erro: "No such file or directory"

**Causa**: Arquivo em local errado
**Solução**:
1. Verificar se `etapa2_banco_dados.sql` está em `C:\wamp64\www\SISTEMALAZER\`
2. Usar caminho completo ao executar:
```powershell
mysql -u root erp_laser < C:\wamp64\www\SISTEMALAZER\etapa2_banco_dados.sql
```

### Erro: "Syntax Error" ou "Unknown column"

**Causa**: Arquivo corrompido ou com encoding errado
**Solução**:
1. Usar phpMyAdmin (método mais seguro)
2. Ou verificar se o arquivo não tem caracteres especiais

---

## ✅ Checklist Final

- [ ] Banco de dados `erp_laser` criado
- [ ] 16 tabelas aparecem no phpMyAdmin
- [ ] Dados de teste inseridos (1 usuário, 1 cliente, 1 chapa, 1 insumo, 1 custo)
- [ ] Nenhum erro de SQL
- [ ] Posso ver  a tabela `usuarios` com:
  - Usuario: `admin@example.com`
  - Senha: `admin123` (será criptografada depois)

---

## 🔗 Próximo Passo

Após confirmar que o banco foi criado com sucesso:

1. **ETAPA 3**: Criar Models PHP para cada tabela
   - `ClienteModel` → CRUD clientes
   - `MaterialModel` → CRUD materiais
   - `CustoModel` → CRUD custos
   - etc...

2. Com Models criados, criar Controllers e Views para:
   - Gerenciar Clientes/Fornecedores
   - Cadastro de Materiais
   - Configuração de Custos

---

## 📞 Precisa de Ajuda?

Se der erro, copie a mensagem e descreva os passos que fez. Vou ajudar!

**Principais comandos úteis**:

```powershell
# Ver versão MySQL
mysql --version

# Conectar ao MySQL
mysql -u root

# Lista de bancos
SHOW DATABASES;

# Tabelas do banco
SHOW TABLES FROM erp_laser;

# Estrutura de uma tabela
DESCRIBE usuarios;

# Contar registros
SELECT COUNT(*) FROM usuarios;

# Ver usuários criados
SELECT * FROM usuarios;
```

---

**Status**: ✅ PRONTO PARA EXECUÇÃO
**Data**: 6 de Fevereiro de 2026
**Versão**: 1.0
