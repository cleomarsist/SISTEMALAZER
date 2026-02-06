# GUIA DE INSTALAÇÃO E TESTE - ETAPA 1

## PASSO A PASSO PARA COMEÇAR

### 1. VERIFICAR REQUISITOS

Antes de iniciar, verifique se seu servidor atende os requisitos:

- **PHP:** Versão 7.4+
- **MySQL:** Versão 5.7+
- **Apache:** Com módulo `mod_rewrite` ativado
- **Extensões PHP necessárias:**
  - `PDO` (para banco de dados)
  - `pdo_mysql` (para MySQL)
  - `session` (para gerenciamento de sessão)

Para verificar as extensões, crie um arquivo `phpinfo.php` na pasta `public/`:

```php
<?php phpinfo(); ?>
```

E acesse: `http://localhost/SISTEMALAZER/public/phpinfo.php`

### 2. CRIAR BANCO DE DADOS

Abra seu MySQL (via PHPMyAdmin ou command line) e execute:

```sql
-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS erp_laser CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar banco de dados
USE erp_laser;

-- Criar tabela de exemplo (será expandida na ETAPA 2)
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir usuário de teste
INSERT INTO usuarios (nome, email, senha) VALUES (
    'Administrador',
    'admin@example.com',
    'admin123'
);
```

### 3. ATUALIZAR CONFIGURAÇÃO

Edite o arquivo `app/config/config.php` e defina suas credenciais:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'erp_laser');
define('DB_USER', 'root');         // Seu usuário MySQL
define('DB_PASS', '');             // Sua senha (vazio se nenhuma)
```

### 4. HABILITAR MOD_REWRITE (Apache)

Certifique-se de que o módulo está ativado:

```bash
# Linux/Mac
sudo a2enmod rewrite
sudo systemctl restart apache2

# Windows (de dentro do arquivo Apache)
LoadModule rewrite_module modules/mod_rewrite.so
```

### 5. TESTAR A APLICAÇÃO

Acesse em seu navegador:

```
http://localhost/SISTEMALAZER/public/
```

Você deverá ver o **Dashboard da ETAPA 1** com:

✅ Logo e mensagem de boas-vindas  
✅ Status do sistema  
✅ Links para os módulos principais  
✅ Informações de autenticação  

### 6. FAZER LOGIN (TESTE)

Clique em "Login" no header ou acesse:

```
http://localhost/SISTEMALAZER/public/login
```

Use as credenciais de teste:

- **Email:** `admin@example.com`
- **Senha:** `admin123`

Se fizer login com sucesso:

✅ Será redirecionado para `/dashboard`  
✅ Verá "Bem-vindo, Administrador!" no header  
✅ Terá acesso aos módulos autenticados  

### 7. TESTAR LOGOUT

Clique em "Logout" no header para:

✅ Destruir a sessão  
✅ Ser redirecionado para login  
✅ Verificar que não há cookies persistidos  

## VERIFICAÇÃO DE FUNCIONAMENTO

### Teste 1: Roteamento
- ✅ Acesse `/dashboard` - deve chamar DashboardController->index()
- ✅ Acesse `/login` - deve mostrar formulário de login
- ✅ Acesse `/pagina-inexistente` - deve mostrar erro 404

### Teste 2: Banco de Dados
- ✅ Abra `logs/database.log` e verifique se há conexão registrada
- ✅ Se erro, apareça no arquivo de log

### Teste 3: Sessão
- ✅ Faça login e abra `logs/session.log`
- ✅ Deve haver registro de "login" e "session_timeout"
- ✅ Verifique validação de CSRF token

### Teste 4: Segurança
- ✅ Try acessar formulário POST sem CSRF token - deve falhar
- ✅ Tente fazer login com email inválido - deve retornar erro 401
- ✅ Tente SQL injection no email (`' OR '1'='1`) - deve ser bloqueado

## ARQUIVOS DE LOG

Após testes, você encontrará logs em:

```
logs/
├── php_errors.log      # Erros do PHP
├── database.log        # Operações do banco
├── session.log         # Login/logout
├── application.log     # Ações da aplicação
└── routing.log         # Erros de roteamento
```

## PRÓXIMOS PASSOS

Após verificar que tudo está funcionando:

1. **ETAPA 2:** Criar todas as tabelas do banco de dados
2. **ETAPA 3:** Desenvolver módulo CRUD de Clientes
3. **ETAPA 4:** Desenvolver módulo de Materiais
4. E assim por diante...

## SOLUÇÃO DE PROBLEMAS

### "Página não encontrada" ou "erro 404"

**Causa possível:** Apache não está reescrevendo URLs ou `.htaccess` está bloqueado

**Solução:**
1. Verifique se `mod_rewrite` está ativado
2. Verifique se `.htaccess` está na pasta `public/`
3. Verifique permissões de arquivo (deve ser 644)

### "Erro de conexão ao banco"

**Causa possível:** Credenciais de banco incorretas em `config.php`

**Solução:**
1. Verifique o valor de `DB_USER`, `DB_PASS`, `DB_NAME`
2. Verifique se servidor MySQL está rodando
3. Abra `logs/database.log` para mensagem de erro exata

### "Sessão não inicia"

**Causa possível:** Permissões de pasta `logs/`

**Solução:**
```bash
chmod 755 logs/
chmod 666 logs/*.log
```

### "CSRF token invalid"

**Causa possível:** Sessão expirou ou token foi perdido

**Solução:**
1. Recarregue a página
2. Verifique se cookies estão habilitados
3. Verifique timeout da sessão em `config.php`

## ESTRUTURA DO PROJETO CRIADA

```
SISTEMALAZER/
├── public/
│   ├── index.php          ← Ponto de entrada
│   ├── .htaccess          ← Roteamento Apache
│   ├── css/               ← Estilos
│   ├── js/                ← Scripts
│   └── img/               ← Imagens
├── app/
│   ├── config/
│   │   ├── config.php     ← Configurações globais
│   │   └── Session.php    ← Gerenciamento de sessão
│   ├── database/
│   │   └── Database.php   ← Conexão PDO
│   ├── models/
│   │   ├── BaseModel.php  ← Classe pai (CRUD)
│   │   └── ... (outros modelos)
│   ├── controllers/
│   │   ├── BaseController.php ← Classe pai
│   │   ├── DashboardController.php
│   │   ├── LoginController.php
│   │   └── ... (outros controllers)
│   └── views/
│       ├── layout/
│       │   ├── header.php
│       │   └── footer.php
│       ├── dashboard/
│       └── login/
├── logs/                  ← Arquivos de log
├── ETAPA1_ARQUITETURA.md  ← Documentação
└── INSTALACAO.md          ← Este arquivo
```

## DÚVIDAS OU PROBLEMAS?

1. Verifique os arquivos de log em `logs/`
2. Leia a documentação em `ETAPA1_ARQUITETURA.md`
3. Verifique se todos os arquivos PHP têm o comentário de propósito no início
4. Em desenvolvimento, erros detalhados aparecem na tela (se `IS_development = true`)

---

**Versão:** 1.0  
**Data:** Fevereiro 2025  
**Status:** ETAPA 1 - Arquitetura Geral ✅
