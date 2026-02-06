# 🔥 ERP FÊNIX MAGAZINE PERSONALIZADOS

## Sistema Completo de Gestão para Corte a Laser

---

## 📋 SOBRE O PROJETO

Este é um **Sistema ERP (Enterprise Resource Planning)** desenvolvido especialmente para a empresa **Fênix Magazine Personalizados**, especializada em **corte a laser e personalizados de qualidade**.

O sistema foi construído com tecnologias **abertas e gratuitas**, seguindo as melhores práticas de engenharia de software e com foco em:

✅ **Código Limpo** - Bem estruturado e comentado  
✅ **Segurança** - Proteção contra SQL Injection, XSS, CSRF  
✅ **Escalabilidade** - Arquitetura preparada para crescimento  
✅ **Performance** - Otimizado para múltiplos usuários  
✅ **Usabilidade** - Interface intuitiva e responsiva  

---

## 🛠️ TECNOLOGIAS

- **Backend:** PHP 7.4+ (puro, sem frameworks pagos)
- **Banco de Dados:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (puro)
- **Servidor:** Apache com mod_rewrite

---

## 📁 ESTRUTURA DO PROJETO

```
SISTEMALAZER/
├── public/                    # Raiz do servidor web
│   ├── index.php              # Ponto de entrada (router)
│   ├── .htaccess              # Configuração Apache
│   ├── css/                   # Estilos
│   │   └── style.css          # CSS global
│   ├── js/                    # Scripts
│   │   └── main.js            # JavaScript global
│   └── img/                   # Imagens
│
├── app/                       # Código da aplicação
│   ├── config/                # Configuração
│   │   ├── config.php         # Credenciais e constantes
│   │   └── Session.php        # Gerenciamento de sessão
│   │
│   ├── database/              # Conexão com banco
│   │   └── Database.php       # Classe PDO (Singleton)
│   │
│   ├── models/                # Modelos (dados)
│   │   ├── BaseModel.php      # Classe pai com CRUD
│   │   └── ... (modelos específicos)
│   │
│   ├── controllers/           # Controladores (lógica)
│   │   ├── BaseController.php # Classe pai
│   │   ├── LoginController.php
│   │   ├── DashboardController.php
│   │   └── ... (controllers específicos)
│   │
│   └── views/                 # Views (apresentação)
│       ├── layout/            # Templates comuns
│       │   ├── header.php
│       │   └── footer.php
│       ├── dashboard/         # Views de dashboard
│       ├── login/             # Views de login
│       └── ... (views de módulos)
│
├── logs/                      # Arquivos de log
│   ├── php_errors.log         # Erros do PHP
│   ├── database.log           # Operações do banco
│   ├── session.log            # Login/logout
│   └── application.log        # Ações da aplicação
│
└── Documentação
    ├── README.md              # Este arquivo
    ├── ETAPA1_ARQUITETURA.md  # Arquitetura detalhada
    └── INSTALACAO.md          # Guia de instalação
```

---

## 🚀 INÍCIO RÁPIDO

### 1. Clonar/Copiar Projeto

Copie todos os arquivos para `C:\wamp64\www\SISTEMALAZER\` (Windows) ou equivalente no seu servidor.

### 2. Criar Banco de Dados

Execute no MySQL:

```sql
CREATE DATABASE IF NOT EXISTS erp_laser CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Configurar Credenciais

Edite `app/config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'erp_laser');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### 4. Acessar a Aplicação

```
http://localhost/SISTEMALAZER/public/
```

### 5. Fazer Login (Teste)

```
Email:  admin@example.com
Senha:  admin123
```

---

## 📚 ETAPAS DO DESENVOLVIMENTO

O projeto segue um plano de 12 etapas:

### ✅ ETAPA 1: ARQUITETURA GERAL (COMPLETA)
- Estrutura de pastas MVC
- Configuração global
- Conexão com banco (PDO)
- Gerenciamento de sessão
- Roteamento automático
- Classes base (Model, Controller)
- Login básico de teste

### ⏳ ETAPA 2: BANCO DE DADOS
- Tabelas completas do sistema
- Clientes/Fornecedores
- Produtos, Materiais, Custos
- Orçamentos, Pedidos
- Financeiro

### ⏳ ETAPA 3: MÓDULO CLIENTES/FORNECEDORES
- CRUD completo
- Endereço (ViaCEP)
- Telefone com WhatsApp
- Crédito disponível

### ⏳ ETAPA 4: MÓDULO MATERIAIS
- Chapas (largura, comprimento, espessura)
- Insumos (unidades de medida)
- Cálculo de área
- Controle de estoque

### ⏳ ETAPA 5: MÓDULO CUSTOS
- Custos fixos e variáveis
- Unidades (minuto, hora, peça, mês)
- Impacto nos produtos

### ⏳ ETAPA 6: SIMULADOR DE PEÇAS (CENTRAL)
- Seleção de chapas
- Cálculo de aproveitamento
- Tempo de corte/gravação
- Inclusão de insumos e custos
- Aplicação de margem
- Preço de venda sugerido

### ⏳ ETAPA 7: MÓDULO PRODUTOS
- Produtos simples
- Produtos tipo kit
- Cálculo automático de preço

### ⏳ ETAPA 8: MÓDULO ORÇAMENTOS
- Inclusão de produtos
- Itens customizados
- Uso de crédito
- Condição de pagamento
- Geração de PDF/HTML

### ⏳ ETAPA 9: MÓDULO PEDIDOS
- Conversão orçamento → pedido
- Status de produção
- Datas de entrega
- Integração financeira

### ⏳ ETAPA 10: MÓDULO FINANCEIRO
- Contas a Receber
- Contas a Pagar
- Movimentação de crédito
- Fluxo de caixa

### ⏳ ETAPA 11: DASHBOARD E AUDITORIA
- Dashboard geral
- Dashboard financeiro
- Histórico de ações
- Auditoria

### ⏳ ETAPA 12: SEGURANÇA AVANÇADA
- Proteção avançada
- 2FA (dois fatores)
- Backup automático

---

## 🔐 SEGURANÇA IMPLEMENTADA (ETAPA 1)

### 1. Proteção contra SQL Injection
- ✅ Prepared statements com PDO
- ✅ Placeholders para todos os parâmetros
- ✅ Classe Database com validação

### 2. Proteção contra XSS (Cross-Site Scripting)
- ✅ `htmlspecialchars()` em todas as saídas
- ✅ Sanitização de inputs

### 3. Proteção contra CSRF (Cross-Site Request Forgery)
- ✅ Tokens únicos por sessão
- ✅ Validação obrigatória em POST
- ✅ Tokens armazenados em sessão (não em cookies)

### 4. Sessão Segura
- ✅ Timeout de inatividade (1 hora)
- ✅ Regeneração periódica de ID
- ✅ Validação de IP
- ✅ Cookies HttpOnly e SameSite

### 5. Headers de Segurança
- ✅ X-Content-Type-Options
- ✅ X-Frame-Options
- ✅ X-XSS-Protection
- ✅ Content-Security-Policy

---

## 💻 PADRÃO MVC

### Model (Dados)
Arquivo exemplo: `app/models/ClienteModel.php`

```php
class ClienteModel extends BaseModel {
    protected $table = 'clientes';
    protected $fillable = ['nome', 'email', 'telefone'];
    
    // Herda: create(), find(), all(), update(), delete()
    // Adicione métodos específicos conforme necessário
}
```

### Controller (Lógica)
Arquivo exemplo: `app/controllers/ClienteController.php`

```php
class ClienteController extends BaseController {
    public function listar() {
        $model = new ClienteModel();
        $clientes = $model->all();
        $this->render('listar', ['clientes' => $clientes]);
    }
}
```

### View (Apresentação)
Arquivo exemplo: `app/views/clientes/listar.php`

```php
<h1>Clientes</h1>
<table>
    <tr>
        <th>Nome</th>
        <th>Email</th>
    </tr>
    <?php foreach ($clientes as $cliente): ?>
        <tr>
            <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
            <td><?php echo htmlspecialchars($cliente['email']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
```

---

## 📖 DOCUMENTAÇÃO

Documentação detalhada em:

- **ETAPA1_ARQUITETURA.md** - Explicação completa da arquitetura
- **INSTALACAO.md** - Guia passo a passo
- **Comentários no código** - Explicação de cada função

---

## 🤖 CLASSE BASEMODEL (CRUD GENÉRICO)

Todos os modelos herdam desta classe com métodos:

```php
// CREATE
$model->create(['nome' => 'João', 'email' => 'joao@example.com']);

// READ
$model->find(1);                    // Por ID
$model->all();                      // Todos
$model->first(['email' => 'x@x']);  // Primeiro que atenda critério
$model->count();                    // Total de registros

// UPDATE
$model->update(1, ['nome' => 'José']);

// DELETE (soft delete - marca timestamp)
$model->delete(1);

// QUERY CUSTOMIZADA
$model->query("SELECT * FROM tabela WHERE ...", [params]);
```

---

## 🎯 BASECONTROLLER (FUNÇÕES COMUNS)

Todos os controllers herdam:

```php
// Renderizar view
$this->render('view_name', ['data' => $value]);

// Redirecionar
$this->redirect('/nova-url');

// Resposta JSON (AJAX)
$this->successResponse('Mensagem', ['data' => ...]);
$this->errorResponse('Erro', 400);

// Validação
$this->requireAuth();              // Exige login
$this->requirePermission('admin'); // Exige permissão
$this->validateCSRF();             // Valida token CSRF

// Inputs
$this->input('nome');              // Obtém valor do input

// Logging
$this->log('acao', 'descricao');   // Registra no log
```

---

## 📝 COMO ADICIONAR UM NOVO MÓDULO

### 1. Criar Model

Arquivo: `app/models/MeuModel.php`

```php
class MeuModel extends BaseModel {
    protected $table = 'minha_tabela';
    protected $fillable = ['campo1', 'campo2'];
    
    // Adicione métodos específicos aqui
}
```

### 2. Criar Controller

Arquivo: `app/controllers/MeuController.php`

```php
class MeuController extends BaseController {
    public function index() {
        $model = new MeuModel();
        $dados = $model->all();
        $this->render('index', ['dados' => $dados]);
    }
}
```

### 3. Criar Views

Pasta: `app/views/meu/`
- `index.php` - Listagem
- `criar.php` - Formulário de criação
- `editar.php` - Formulário de edição

### 4. Registrar no Autoload

Edite `public/index.php` e adicione na array `$paths`:

```php
'MeuModel' => APP_PATH . '/models/MeuModel.php',
'MeuController' => APP_PATH . '/controllers/MeuController.php',
```

### 5. Acessar

```
http://localhost/SISTEMALAZER/public/meu/index
```

---

## 🔍 LOGS DO SISTEMA

Os logs são salvos em `logs/`:

- **php_errors.log** - Erros do PHP
- **database.log** - Queries do banco de dados
- **session.log** - Login/logout
- **application.log** - Ações da aplicação
- **routing.log** - Erros de roteamento

---

## 🚦 FLUXO DE UMA REQUISIÇÃO

```
1. User acessa: /clientes/listar
   ↓
2. .htaccess redireciona para: index.php?url=clientes/listar
   ↓
3. index.php:
   - Carrega config.php
   - Auto-registra classes
   - Inicia sessão
   - Define headers
   ↓
4. Roteamento:
   - Extrai: módulo='clientes', ação='listar'
   - Carrega ClienteController
   ↓
5. Controller executa:
   - new ClienteModel()
   - $model->all()
   - $this->render('listar', [...])
   ↓
6. Views renderizam:
   - header.php
   - clientes/listar.php
   - footer.php
   ↓
7. HTML retorna ao navegador
```

---

## 📊 BANCO DE DADOS (A CRIAR NA ETAPA 2)

Será criado com as seguintes tabelas:

- `usuarios` - Usuários do sistema
- `clientes` - Clientes e fornecedores
- `materiais` - Chapas e insumos
- `custos` - Custos fixos e variáveis
- `produtos` - Catálogo de produtos
- `orcamentos` - Orçamentos de clientes
- `pedidos` - Pedidos de vendas
- `contas_receber` - Financeiro a receber
- `contas_pagar` - Financeiro a pagar
- `historico_auditoria` - Registro de alterações

---

## ⚙️ CONFIGURAÇÃO AVANÇADA

### Modo Desenvolvimento vs Produção

Arquivo: `app/config/config.php`

```php
define('IS_development', true);  // Mostra erros
// define('IS_development', false); // Oculta erros
```

### Timeout de Sessão

```php
define('SESSION_TIMEOUT', 3600);  // 1 hora em segundos
```

### Banco de Dados

```php
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'erp_laser');
define('DB_USER', 'root');
define('DB_PASS', '');
```

---

## 🐛 SOLUÇÃO DE PROBLEMAS

### Erro 404 (página não encontrada)
- Verifique se `.htaccess` está na pasta `public/`
- Verifique se `mod_rewrite` está ativado no Apache
- Verifique se a URL segue formato: `/modulo/acao/parametros`

### Erro de conexão ao banco
- Verifique credenciais em `app/config/config.php`
- Verifique se MySQL está rodando
- Abra `logs/database.log` para erro exato

### Sessão não salva
- Verifique permissões da pasta `logs/`
- Verifique se cookies estão habilitados no navegador
- Verifique `SESSION_TIMEOUT` em `config.php`

### CSRF Token inválido
- Recarregue a página
- Limpe cookies do navegador
- Verifique se sessão está ativa

---

## 👨‍💼 ARQUITETURA CRIADA

Desenvolvido seguindo **30+ anos de experiência** em:

- ✅ Desenvolvimento de software escalável
- ✅ Arquitetura de sistemas ERP
- ✅ Segurança de aplicações
- ✅ Boas práticas de programação
- ✅ Otimização de performance
- ✅ Gerenciamento de dados

---

## 📞 SUPORTE

Para dúvidas ou problemas:

1. Verifique a documentação em `ETAPA1_ARQUITETURA.md`
2. Leia `INSTALACAO.md` para configuração
3. Verifique arquivos de log em `logs/`
4. Consulte comentários no código (explicam tudo!)

---

## 📄 LICENÇA

Código livre para uso interno da Fênix Magazine Personalizados.

---

## 📅 VERSÃO E STATUS

- **Versão:** 1.0
- **Data:** Fevereiro 2025
- **Status:** ✅ ETAPA 1 Completa
- **Próxima Etapa:** ETAPA 2 - Banco de Dados Completo

---

## 🎉 DESENVOLVIDO COM

- **PHP** puro (sem frameworks pagos)
- **MySQL** como banco de dados
- **HTML5, CSS3, JavaScript** no frontend
- **Padrão MVC** bem estruturado
- **Segurança** em primeiro lugar
- **Documentação** completa

---

**Fênix Magazine Personalizados**  
*Corte a Laser e Personalizados de Qualidade*
