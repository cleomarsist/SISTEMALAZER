# ERP Fênix Magazine Personalizados

## Documentação Técnica do Sistema

### 📋 Visão Geral
Sistema ERP completo para gestão de vendas, estoque, produção e financeiro para uma empresa de corte a laser e personalizados.

### 🏗️ Arquitetura

#### Padrão MVC Adaptado para PHP Puro
- **Models**: Representam entidades do banco de dados
- **Controllers**: Lógica de negócio e manipulação de dados
- **Views**: Interface HTML/CSS/JavaScript

#### Estrutura de Diretórios

```
ControleInvestimento/
├── api/                    # Endpoints REST JSON
├── assets/                 # CSS, JavaScript, imagens
├── config/                 # Configuração global
├── controllers/            # Controladores (lógica de negócio)
├── db/                     # Conexão com banco e SQL
├── models/                 # Entidades e acesso a dados
├── session/                # Controle de sessões
├── utils/                  # Utilitários (Auth, Validator, Response)
└── views/                  # Interfaces HTML
```

### 🔐 Segurança

#### Autenticação
- **Password Hash**: Bcrypt (PASSWORD_BCRYPT)
- **Sessões**: Utilizadas para manter estado de login
- **Validação**: Sanitização de entrada em todos os endpoints

#### Autorização
- **Roles**: admin, gerente, vendedor, usuario
- **Classe Auth**: Verifica permissões antes de executar ações

#### Contra XSS e SQL Injection
- **PDO**: Prepared statements em todas as queries
- **Sanitização**: htmlspecialchars() para output
- **Validação**: Classe Validator para validar entrada

### 📚 Modelos de Dados

#### Entidades Principais
1. **Users** - Usuários do sistema
2. **Clients** - Clientes e fornecedores
3. **Products** - Produtos simples
4. **ProductKits** - Kits de produtos
5. **Materials** - Chapas e insumos
6. **Orders** - Pedidos de venda
7. **Budgets** - Orçamentos
8. **Simulations** - Simulações de preço
9. **AccountsReceivable** - Contas a receber
10. **AccountsPayable** - Contas a pagar
11. **Audit** - Histórico de auditoria

### 🚀 Como Usar

#### 1. Instalação do Banco de Dados
```bash
# Importe o arquivo SQL
mysql -u root -p < db/erp_schema.sql
```

#### 2. Configurar Conexão
Edite `config/config.php` com suas credenciais:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'fenix_magazine');
```

#### 3. Acessar Sistema
```
http://localhost/SISTEMAIA/ControleInvestimento/views/login.html
```

### 📝 Padrões de Código

#### Comentários Obrigatórios
Cada arquivo deve conter:
```php
// caminho/arquivo.php
// Descrição do arquivo
// Módulo: Nome do módulo
// Etapa: Descrição da etapa
```

#### Exemplo de Model
```php
public static function findById($id) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM tabela WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        return new Classe($row['id'], ...);
    }
    return null;
}
```

#### Exemplo de Endpoint
```php
require_once(__DIR__ . '/../utils/Response.php');
require_once(__DIR__ . '/../utils/Auth.php');

Auth::requireRole('admin');
// Lógica de negócio
Response::success($data, 'Mensagem', 200);
```

### 🔄 Fluxo de Funcionamento

#### Login
1. Usuário acessa `login.html`
2. JavaScript envia POST para `api/login.php`
3. Controller valida credenciais
4. Sessão é iniciada com dados do usuário
5. Redirecionamento para dashboard

#### Criar Pedido
1. Usuário acessa formulário de pedido
2. JavaScript valida dados no cliente
3. POST para `api/orders.php` com JSON
4. Controller valida e salva no banco
5. Response retorna sucesso ou erro
6. Dashboard atualiza em tempo real

### 🛠️ Utilitários

#### Auth
```php
Auth::isAuthenticated();    // Verifica se está autenticado
Auth::getUserId();          // Retorna ID do usuário
Auth::getRole();            // Retorna role do usuário
Auth::requireLogin();       // Redireciona se não autenticado
Auth::requireRole('admin'); // Redireciona se sem permissão
```

#### Validator
```php
Validator::validateEmail($email);
Validator::validateDocument($doc);
Validator::validatePhone($phone);
Validator::validateDate($date);
Validator::sanitizeText($text);
Validator::validatePassword($pass);
```

#### Response
```php
Response::success($data, 'Mensagem', 200);
Response::error('Erro', 400);
Response::validationError($errors);
Response::unauthorized();
Response::forbidden();
Response::notFound();
```

#### Audit
```php
Audit::log($user_id, 'CREATE', 'orders', $order_id, 'Descrição');
Audit::listAll();
Audit::findByUser($user_id);
```

### 📊 Endpoints da API

#### Autenticação
- `POST /api/login.php` - Fazer login
- `POST /api/logout.php` - Fazer logout

#### Clientes
- `GET /api/clients.php?type=cliente` - Listar clientes
- `POST /api/clients.php` - Criar cliente
- `GET /api/clients.php?id=1` - Obter cliente

#### Pedidos
- `GET /api/orders.php?status=aberto` - Listar pedidos
- `POST /api/orders.php` - Criar pedido
- `PUT /api/orders.php?id=1&status=finalizado` - Atualizar status

### 🎯 Próximos Passos

1. Criar endpoints REST completos para todas as entidades
2. Implementar relatórios e gráficos
3. Adicionar cálculo de custos e margens
4. Implementar estoque e movimentação
5. Criar sistema de notificações
6. Adicionar exportação para PDF/Excel

### 📞 Suporte

Para dúvidas ou sugestões, consulte a documentação inline no código.

---

**Desenvolvido com PHP puro, MySQL e JavaScript vanila**
**Data: 06/02/2026**
