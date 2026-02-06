# 🎯 SOLUÇÃO: Erro de Conexão no Login

## ✅ Problema Identificado

O erro **"Erro na conexão com o servidor"** ocorre porque o **banco de dados não foi criado ainda**.

## 🚀 SOLUÇÃO RÁPIDA (3 MINUTOS)

### Passo 1: Certifique que WAMP está rodando ✅
- Procure o ícone **WAMP** no canto inferior direito
- Deve estar **VERDE** ✅
- Se estiver vermelho ❌:
  - Clique nele
  - Selecione **"Start All Services"**
  - Aguarde 3-5 segundos até ficar verde

### Passo 2: Criar o Banco de Dados (ESCOLHA UMA OPÇÃO)

#### **OPÇÃO A: Automática (Recomendado)** ⚡
1. Acesse no navegador:
   ```
   http://localhost/SISTEMAIA/ControleInvestimento/setup.php
   ```
2. Clique no botão **"⚡ Executar Setup (Rápido)"**
3. Aguarde a mensagem de sucesso
4. Pronto! Banco foi criado automaticamente

#### **OPÇÃO B: Manual (phpMyAdmin)**
1. Acesse:
   ```
   http://localhost/phpmyadmin
   ```
2. Clique em "SQL" no topo
3. Copie TODO o conteúdo de:
   ```
   c:\wamp64\www\SISTEMAIA\ControleInvestimento\db\setup_complete.sql
   ```
4. Cole em phpMyAdmin na aba "SQL"
5. Clique em "Executar"

### Passo 3: Fazer Login 🔑
1. Acesse:
   ```
   http://localhost/SISTEMAIA/ControleInvestimento/
   ```
2. Use as credenciais:
   ```
   Usuário: admin
   Senha: Senha123
   ```
3. Clique em **"Entrar"**

## ✨ Pronto! 

Você verá o dashboard do sistema.

---

## 🐛 Se Continuar com Erro

### Erro: "MySQL não está respondendo"
- WAMP não está iniciado
- Solução: Inicie WAMP e tente novamente

### Erro ao executar setup em setup.php
- Acesse manualmente via phpMyAdmin (OPÇÃO B acima)
- Ou compartilhe a mensagem de erro exata

### Erro do banco já existir
- É só um aviso, pode ignorar
- O setup continuará normalmente

---

## 📁 Arquivos Envolvidos

✅ **setup.php** - Página de setup (nova)  
✅ **api/setup.php** - Endpoint de setup (novo)  
✅ **setup_check.php** - Verificador de banco (novo)  
✅ **index.php** - Redireciona para setup se necessário (atualizado)  

---

**Status**: Sistema pronto para uso após criar banco de dados!

Quando completar o setup, poderá fazer login normalmente. 🎉
