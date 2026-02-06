# SISTEMA ERP - FÊNIX MAGAZINE PERSONALIZADOS
## ETAPA 1: ARQUITETURA GERAL

---

## ÍNDICE
1. [Visão Geral](#visão-geral)
2. [Estrutura de Pastas](#estrutura-de-pastas)
3. [Padrão MVC](#padrão-mvc)
4. [Fluxo de Requisição](#fluxo-de-requisição)
5. [Configuração e Setup](#configuração-e-setup)
6. [Segurança](#segurança)
7. [Próximas Etapas](#próximas-etapas)

---

## VISÃO GERAL

O sistema ERP para a **Fênix Magazine Personalizados** é uma aplicação web completa desenvolvida em:
- **Backend:** PHP puro (sem frameworks pagos)
- **Banco de Dados:** MySQL
- **Frontend:** HTML, CSS e JavaScript puro

A arquitetura segue o padrão **MVC (Model-View-Controller)** adaptado para PHP puro com as seguintes características:

✅ Código limpo e bem comentado  
✅ Segurança contra SQL Injection  
✅ Proteção CSRF  
✅ Gerenciamento de sessão seguro  
✅ Estrutura escalável para múltiplos módulos  

---

## ESTRUTURA DE PASTAS

```
SISTEMALAZER/
│
├── public/           # Raiz do servidor web
│   ├── index.php     # Ponto de entrada (router)
│   ├── css/          # Arquivos CSS
│   ├── js/           # Arquivos JavaScript
│   └── img/          # Imagens
│
├── app/              # Código da aplicação
│   ├── config/       # Configurações globais
│   │   ├── config.php              # Constantes e credenciais
│   │   └── Session.php             # Gerenciamento de sessão
│   │
│   ├── database/     # Banco de dados
│   │   └── Database.php            # Classe PDO singleton
│   │
│   ├── models/       # Modelos (dados)
│   │   ├── BaseModel.php           # Classe pai (CRUD genérico)
│   │   ├── ClienteModel.php        # Modelo de clientes
│   │   ├── MaterialModel.php       # Modelo de materiais
│   │   ├── ProdutoModel.php        # Modelo de produtos
│   │   └── ... (outros modelos)
│   │
│   ├── controllers/  # Controladores (lógica)
│   │   ├── BaseController.php        # Classe pai
│   │   ├── ClienteController.php     # Lógica de clientes
│   │   ├── MaterialController.php    # Lógica de materiais
│   │   └── ... (outros controllers)
│   │
│   └── views/        # Views (apresentação)
│       ├── layout/   # Templates comuns
│       │   ├── header.php
│       │   └── footer.php
│       ├── clientes/     # Views de clientes
│       ├── materiais/    # Views de materiais
│       └── ... (outras views)
│
├── logs/             # Arquivos de log
│   ├── php_errors.log
│   ├── database.log
│   ├── application.log
│   └── session.log
│
└── README.md         # Este arquivo
```

### O QUE CADA PASTA FAZ:

**public/** - Pasta pública (acessível via web)
- `index.php` é o ponto de entrada para TODAS as requisições
- Contém arquivos estáticos (CSS, JS, imagens)

**app/config/** - Configurações da aplicação
- `config.php`: Credenciais de banco, constantes, timezone, etc
- `Session.php`: Gerenciamento seguro de sessão

**app/database/** - Conexão com banco
- `Database.php`: Classe PDO para queries seguras

**app/models/** - Modelos (acesso a dados)
- Herdam de `BaseModel`
- Contêm queries específicas da tabela
- Retornam dados formatados

**app/controllers/** - Controladores (lógica)
- Recebem requisições
- Chamam models e views
- Processam formulários
- Retornam resposta (HTML ou JSON)

**app/views/** - Templates HTML
- Recebem dados dos controllers
- Renderizam interface

**logs/** - Registro de eventos
- Erros, acessos, auditoria

---

## PADRÃO MVC

O padrão MVC divide a aplicação em três camadas:

### 1. MODEL (Dados)
```
Arquivo: app/models/ClienteModel.php
Responsabilidade: Acesso ao banco de dados
Métodos típicos: create(), find(), update(), delete(), all()
```

**Exemplo:**
```php
class ClienteModel extends BaseModel {
    protected $table = 'clientes';
    protected $fillable = ['nome', 'email', 'telefone'];
    
    // Aqui adicionar métodos específicos conforme necessário
}
```

### 2. CONTROLLER (Lógica)
```
Arquivo: app/controllers/ClienteController.php
Responsabilidade: Processar requisições, orquestra models e views
Métodos típicos: index(), show(), create(), store(), edit(), update(), delete()
```

**Exemplo:**
```php
class ClienteController extends BaseController {
    
    public function listar() {
        // Obtém clientes do modelo
        $model = new ClienteModel();
        $clientes = $model->all();
        
        // Passa para view
        $this->render('listar', ['clientes' => $clientes]);
    }
    
    public function salvar() {
        if (!$this->isPost()) {
            $this->render('formulario');
            return;
        }
        
        // Valida CSRF
        $this->validateCSRF();
        
        // Obtém dados do formulário
        $dados = $this->input();
        
        // Salva no banco
        $model = new ClienteModel();
        $id = $model->create($dados);
        
        if ($id) {
            $this->successResponse('Cliente salvo com sucesso!', ['id' => $id]);
        } else {
            $this->errorResponse('Erro ao salvar cliente');
        }
    }
}
```

### 3. VIEW (Apresentação)
```
Arquivo: app/views/clientes/listar.php
Responsabilidade: Renderizar HTML
Recebe variáveis dos controllers via extract()
```

**Exemplo:**
```php
<div class="container">
    <h1>Clientes</h1>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                    <td>
                        <a href="<?php echo WEB_ROOT; ?>/clientes/editar/<?php echo $cliente['id']; ?>">Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

---

## FLUXO DE REQUISIÇÃO

Quando usuário acessa uma URL como `/clientes/listar`:

```
1. REQUISIÇÃO HTTP
   /clientes/listar
   ↓
2. SERVER REDIRECIONA PARA index.php
   (Configurado em .htaccess)
   $_GET['url'] = 'clientes/listar'
   ↓
3. index.php PROCESSA
   - Carrega config.php
   - Autorregister de classes
   - Inicia sessão
   - Define headers de segurança
   ↓
4. ROTEAMENTO
   - Extrai módulo: 'clientes'
   - Extrai ação: 'listar'
   - Carrega ClienteController
   ↓
5. CONTROLLER EXECUTA
   - new ClienteModel()
   - $model->all()
   - $this->render('listar', [...])
   ↓
6. RENDER DA VIEW
   - Carrega app/views/layout/header.php
   - Carrega app/views/clientes/listar.php
   - Carrega app/views/layout/footer.php
   ↓
7. RESPOSTA HTML
   Navegador exibe página
```

---

## CONFIGURAÇÃO E SETUP

### PASSO 1: Criar Banco de Dados

```sql
CREATE DATABASE IF NOT EXISTS erp_laser CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE erp_laser;
```

### PASSO 2: Atualizar config.php

Editar `app/config/config.php` com suas credenciais:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'erp_laser');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### PASSO 3: Configurar Servidor Web

Se usando Apache, criar arquivo `.htaccess` em `public/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /SISTEMALAZER/public/
    
    # Não reescreve arquivos ou pastas existentes
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Redireciona tudo para index.php
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>
```

### PASSO 4: Criar Diretório de Logs

```bash
mkdir logs
chmod 755 logs
```

### PASSO 5: Acessar a Aplicação

```
http://localhost/SISTEMALAZER/public/
```

---

## SEGURANÇA

A arquitetura implementa múltiplas camadas de segurança:

### 1. SQL Injection
- ✅ Usa prepared statements (PDO com placeholders)
- ✅ Nunca concatena SQL com dados do usuário

**Exemplo seguro:**
```php
// ❌ INSEGURO
$sql = "SELECT * FROM clientes WHERE email = '" . $_POST['email'] . "'";

// ✅ SEGURO (usado no sistema)
$sql = "SELECT * FROM clientes WHERE email = ?";
$db->selectOne($sql, [$_POST['email']]);
```

### 2. Cross-Site Scripting (XSS)
- ✅ Usa `htmlspecialchars()` em dados exibidos no HTML
- ✅ Sanitiza inputs em BaseController::input()

### 3. Cross-Site Request Forgery (CSRF)
- ✅ Gera tokens únicos por sessão
- ✅ Valida tokens em formulários POST
- ✅ Tokens armazenados em session (não em cookies)

**Uso em formulários HTML:**
```html
<form method="POST" action="<?php echo WEB_ROOT; ?>/clientes/salvar">
    <input type="hidden" name="csrf_token" value="<?php echo Session::getCsrfToken(); ?>">
    <input type="text" name="nome" required>
    <button type="submit">Salvar</button>
</form>
```

**Validação no Controller:**
```php
public function salvar() {
    if ($this->isPost()) {
        $this->validateCSRF(); // Valida automaticamente
        // ... processar formulário
    }
}
```

### 4. Sessão Segura
- ✅ Timeout de inatividade (1 hora padrão)
- ✅ Regeneração periódica de ID
- ✅ Validação de IP
- ✅ Cookies seguro (HttpOnly, SameSite)

### 5. Autenticação
- ✅ Apenas usuários autenticados acessam áreas restritas
- ✅ Permissões por usuário
- ✅ Logout destrua sessão completamente

```php
// Exigir autenticação
public function listar() {
    $this->requireAuth(); // Para se não autenticado
    // ...
}

// Exigir permissão
public function deletar($id) {
    $this->requirePermission('adminrador');
    // ...
}
```

---

## PRÓXIMAS ETAPAS

- **ETAPA 2:** Banco de Dados - Criar tabelas completas
- **ETAPA 3:** Módulo de Clientes e Fornecedores
- **ETAPA 4:** Módulo de Materiais (Chapas e Insumos)
- **ETAPA 5:** Módulo de Custos
- **ETAPA 6:** Simulador de Peças (módulo central)
- **ETAPA 7:** Produtos
- **ETAPA 8:** Orçamentos
- **ETAPA 9:** Pedidos
- **ETAPA 10:** Financeiro
- **ETAPA 11:** Dashboard e Auditoria
- **ETAPA 12:** Segurança avançada

---

## ARQUITETURA CRIADA

A ETAPA 1 estabelece a base sólida para todo o projeto:

✅ **Estrutura de Pastas** - Bem organizada, escalável  
✅ **MVC Estruturado** - Separação clara de responsabilidades  
✅ **Database.php** - Conexão segura com PDO  
✅ **Session.php** - Gerenciamento seguro de sessão  
✅ **BaseModel** - CRUD genérico para todos os modelos  
✅ **BaseController** - Métodos comuns para todos os controllers  
✅ **Roteamento** - Automático baseado em URL  
✅ **Segurança** - Proteção contra principais vulnerabilidades  

A partir de agora, para criar um novo módulo, basta:

1. Estender `BaseModel` para criar um modelo
2. Estender `BaseController` para criar um controller
3. Criar views em `app/views/meu_modulo/`
4. O roteamento automático direciona a requisição

---

**Desenvolvido em:** 6 de Fevereiro de 2025  
**Versão:** 1.0  
**Arquiteto:** Sistema Maestro de ERP
