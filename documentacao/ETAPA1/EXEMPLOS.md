# EXEMPLOS PRÁTICOS - Como Usar o Sistema

## ETAPA 1: Arquitetura Geral com Exemplos

---

## 📚 EXEMPLO 1: Criar um Novo Model

### Cenário
Você quer adicionar um módulo de Produtos

### Passo 1: Criar o Model

**Arquivo:** `app/models/ProdutoModel.php`

```php
<?php
/**
 * MODEL: PRODUTOS
 * 
 * Responsabilidade: Acesso aos dados da tabela produtos
 * Herda de BaseModel todos os métodos CRUD
 */

class ProdutoModel extends BaseModel {
    
    // Nome da tabela no banco de dados
    protected $table = 'produtos';
    
    // Campos que podem ser atribuídos em massa (whitelist)
    protected $fillable = ['nome', 'descricao', 'preco', 'estoque'];
    
    // Campos que não devem ser retornados (exemplo: senha)
    protected $hidden = ['criado_em', 'atualizado_em'];
    
    // Método personalizado: buscar produtos ativos
    public function ativos() {
        return $this->all(['status' => 'ativo']);
    }
    
    // Método personalizado: buscar por faixa de preço
    public function porFaixa($minimo, $maximo) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE preco >= ? AND preco <= ? 
                ORDER BY nome";
        return $this->query($sql, [$minimo, $maximo]);
    }
}
?>
```

---

## 📚 EXEMPLO 2: Criar um novo Controller

### Cenário
Você quer controlar o módulo de Produtos

### Arquivo: `app/controllers/ProdutoController.php`

```php
<?php
/**
 * CONTROLLER: PRODUTOS
 * 
 * Responsabilidade: Orquestra requisições de produtos
 * Chama Model para dados e renderiza views
 */

class ProdutoController extends BaseController {
    
    private $model;
    
    public function __construct() {
        parent::__construct();
        $this->model = new ProdutoModel();
    }
    
    /**
     * Listagem de todos os produtos
     * Acessível por: /produtos ou /produtos/listar
     */
    public function index() {
        // Obtém todos os produtos
        $produtos = $this->model->all();
        
        // Renderiza view com dados
        $this->render('listar', [
            'produtos' => $produtos,
            'total' => count($produtos)
        ]);
    }
    
    /**
     * Mostra formulário para criar novo produto
     * Acessível por: /produtos/criar
     */
    public function criar() {
        // Se método é POST, trata como salvar
        if ($this->isPost()) {
            return $this->salvar();
        }
        
        // Se GET, mostra formulário
        $this->render('formulario', [
            'modo' => 'criar',
            'titulo' => 'Novo Produto'
        ]);
    }
    
    /**
     * Salva novo produto no banco
     * POST para: /produtos/salvar
     */
    public function salvar() {
        // Valida token CSRF
        $this->validateCSRF();
        
        // Obtém dados do formulário
        $dados = [
            'nome' => $this->input('nome'),
            'descricao' => $this->input('descricao'),
            'preco' => $this->input('preco'),
            'estoque' => $this->input('estoque')
        ];
        
        // Valida dados básicos
        if (empty($dados['nome']) || empty($dados['preco'])) {
            return $this->validationError('Nome e preço são obrigatórios');
        }
        
        // Insere no banco
        $id = $this->model->create($dados);
        
        if ($id) {
            // Log da ação
            $this->log('produto_criado', "Produto {$dados['nome']} criado");
            
            // JSON para AJAX
            if ($this->isAjax()) {
                return $this->successResponse(
                    'Produto criado com sucesso!',
                    ['id' => $id]
                );
            }
            
            // Redireciona
            $this->redirect('/produtos/editar/' . $id);
        } else {
            return $this->errorResponse('Erro ao criar produto');
        }
    }
    
    /**
     * Mostra detalhes de um produto
     * Acessível por: /produtos/ver/123
     */
    public function ver($id) {
        // Busca produto por ID
        $produto = $this->model->find($id);
        
        if (!$produto) {
            return $this->notFoundError('Produto não encontrado');
        }
        
        // Renderiza com dados
        $this->render('detalhe', ['produto' => $produto]);
    }
    
    /**
     * Mostra formulário de edição
     * Acessível por: /produtos/editar/123
     */
    public function editar($id) {
        // Busca produto
        $produto = $this->model->find($id);
        
        if (!$produto) {
            $this->redirect('/produtos');
        }
        
        // Se POST, trata como update
        if ($this->isPost()) {
            return $this->atualizar($id);
        }
        
        // Se GET, mostra formulário com dados
        $this->render('formulario', [
            'modo' => 'editar',
            'produto' => $produto,
            'titulo' => 'Editar ' . $produto['nome']
        ]);
    }
    
    /**
     * Atualiza produto no banco
     * POST para: /produtos/atualizar/123
     */
    public function atualizar($id) {
        // Valida CSRF
        $this->validateCSRF();
        
        // Obtém dados
        $dados = [
            'nome' => $this->input('nome'),
            'descricao' => $this->input('descricao'),
            'preco' => $this->input('preco'),
            'estoque' => $this->input('estoque')
        ];
        
        // Atualiza no banco
        $sucesso = $this->model->update($id, $dados);
        
        if ($sucesso) {
            $this->log('produto_atualizado', "Produto ID $id atualizado");
            return $this->successResponse('Produto atualizado com sucesso!');
        } else {
            return $this->errorResponse('Erro ao atualizar produto');
        }
    }
    
    /**
     * Deleta um produto
     * DELETE para: /produtos/deletar/123
     */
    public function deletar($id) {
        // Valida CSRF
        if (!empty($_POST['csrf_token'])) {
            $this->validateCSRF();
        }
        
        // Deleta (soft delete)
        $sucesso = $this->model->delete($id);
        
        if ($sucesso) {
            $this->log('produto_deletado', "Produto ID $id deletado");
            return $this->successResponse('Produto deletado com sucesso!');
        } else {
            return $this->errorResponse('Erro ao deletar produto');
        }
    }
    
    /**
     * Busca produtos por termo
     * Acessível por: /produtos/buscar?termo=notebook
     */
    public function buscar() {
        $termo = $this->input('termo', '');
        
        if (strlen($termo) < 2) {
            return $this->validationError('Digite pelo menos 2 caracteres');
        }
        
        // Query customizada
        $sql = "SELECT * FROM produtos 
                WHERE nome LIKE ? OR descricao LIKE ? 
                ORDER BY nome";
        
        $pesquisa = "%{$termo}%";
        $produtos = $this->model->query($sql, [$pesquisa, $pesquisa]);
        
        // Retorna JSON
        return $this->successResponse(
            'Busca realizada',
            ['produtos' => $produtos, 'total' => count($produtos)]
        );
    }
}
?>
```

---

## 📚 EXEMPLO 3: Criar uma View

### Arquivo: `app/views/produtos/listar.php`

```php
<!--
    VIEW: LISTAGEM DE PRODUTOS
    
    Variáveis disponíveis (via controller):
    - $produtos (array)
    - $total (int)
    - $user (array)
    - $csrf_token (string)
-->

<div class="container">
    <h1>📦 Produtos</h1>
    
    <!-- Barra de ferramentas -->
    <div style="margin: 20px 0;">
        <a href="<?php echo WEB_ROOT; ?>/produtos/criar" class="btn btn-success">
            ➕ Novo Produto
        </a>
        
        <form id="buscarForm" style="display: inline; margin-left: 10px;">
            <input type="text" id="termoBusca" placeholder="Buscar produto..." style="width: 300px; padding: 8px;">
            <button type="submit" class="btn">🔍 Buscar</button>
        </form>
    </div>
    
    <!-- Tabela de produtos -->
    <?php if (empty($produtos)): ?>
        <div class="alert alert-info">
            ℹ️ Nenhum produto encontrado. 
            <a href="<?php echo WEB_ROOT; ?>/produtos/criar">Criar primeiro produto</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td>
                            <a href="<?php echo WEB_ROOT; ?>/produtos/ver/<?php echo $produto['id']; ?>">
                                <?php echo htmlspecialchars($produto['nome']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($produto['descricao']); ?></td>
                        <td><?php echo $this->formatMoney($produto['preco']); ?></td>
                        <td><?php echo $produto['estoque']; ?> un</td>
                        <td>
                            <a href="<?php echo WEB_ROOT; ?>/produtos/editar/<?php echo $produto['id']; ?>" 
                               class="btn btn-small">✏️ Editar</a>
                            
                            <button onclick="deletarProduto(<?php echo $produto['id']; ?>)" 
                                    class="btn btn-small btn-danger">🗑️ Deletar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p style="color: #666; margin-top: 20px;">
            Total: <strong><?php echo $total; ?></strong> produto(s)
        </p>
    <?php endif; ?>
</div>

<!-- SCRIPT PARA DELETAR -->
<script>
function deletarProduto(id) {
    if (!confirm('Tem certeza que deseja deletar?')) {
        return;
    }
    
    ajax.delete('<?php echo WEB_ROOT; ?>/produtos/deletar/' + id)
        .then(response => {
            if (response.success) {
                alert('Produto deletado com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + response.message);
            }
        })
        .catch(error => alert('Erro ao deletar'));
}

// SCRIPT PARA BUSCAR
document.getElementById('buscarForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const termo = document.getElementById('termoBusca').value;
    if (termo.length < 2) {
        alert('Digite pelo menos 2 caracteres');
        return;
    }
    
    ajax.get('<?php echo WEB_ROOT; ?>/produtos/buscar', {termo: termo})
        .then(response => {
            if (response.success) {
                console.log('Resultados:', response.data.produtos);
                alert('Encontrados ' + response.data.total + ' produto(s)');
                // Aqui você poderia atualizar a tabela dinamicamente
            } else {
                alert(response.message);
            }
        });
});
</script>
```

---

## 📚 EXEMPLO 4: Usar o Model Diretamente

```php
<?php
// Criar novo produto
$model = new ProdutoModel();
$id = $model->create([
    'nome' => 'Notebook XYZ',
    'descricao' => 'Notebook de 15"',
    'preco' => 2999.99,
    'estoque' => 10
]);

// Buscar por ID
$produto = $model->find($id);
echo $produto['nome'];

// Listar todos
$todos = $model->all();

// Com filtro
$ativos = $model->all(['status' => 'ativo']);

// Com paginação
$pagina1 = $model->all([], [
    'limit' => 10,
    'offset' => 0,
    'orderBy' => 'nome ASC'
]);

// Primeiro que atende critério
$encontrado = $model->first(['nome' => 'Notebook']);

// Contar
$quantidade = $model->count();

// Atualizar
$model->update($id, ['preco' => 2799.99]);

// Deletar (soft delete)
$model->delete($id);

// Query customizada
$resultado = $model->query(
    "SELECT * FROM produtos WHERE preco > ? AND estoque > 0",
    [2000]
);
?>
```

---

## 📚 EXEMPLO 5: Usar o Controller

```php
<?php
// No seu código você pode chamar:

// Ir para /produtos/listar
// Vai chamar ProdutoController->listar()

// Ir para /produtos/criar
// Vai chamar ProdutoController->criar()

// Ir para /produtos/editar/123
// Vai chamar ProdutoController->editar(123)

// POST para /produtos/salvar
// Vai chamar ProdutoController->salvar()

// DELETE para /produtos/deletar/123
// Vai chamar ProdutoController->deletar(123)
?>
```

---

## 📚 EXEMPLO 6: Usar Session & Autenticação

```php
<?php
// Registrar login
Session::login(
    $usuario['id'],
    $usuario['nome'],
    $usuario['email'],
    ['vendedor', 'cliente']
);

// Verificar se está autenticado
if (Session::isAuthenticated()) {
    echo "Usuário logado!";
}

// Obter ID do usuário
$id = Session::getUserId();

// Obter nome do usuário
$nome = Session::getUserName();

// Verificar permissão
if (Session::hasPermission('gerenciador')) {
    // Fazer algo
}

// Verificar múltiplas permissões
if (Session::hasPermission(['admin', 'gerenciador'])) {
    // Fazer algo
}

// Fazer logout
Session::logout();

// Gerar CSRF token (automaticamente em forms)
$token = Session::getCsrfToken();

// Validar CSRF token
if (!Session::validateCsrfToken($_POST['csrf_token'])) {
    die('Token inválido!');
}
?>
```

---

## 📚 EXEMPLO 7: Usar Database Diretamente

```php
<?php
// Obter instância do banco
$db = Database::getInstance();

// INSERT
$id = $db->execute(
    "INSERT INTO produtos (nome, preco) VALUES (?, ?)",
    ['Notebook', 2999.99]
);
echo "ID inserido: " . $db->getLastInsertId();

// SELECT múltiplos
$produtos = $db->select(
    "SELECT * FROM produtos WHERE preco > ? ORDER BY nome",
    [1000]
);

// SELECT um
$produto = $db->selectOne(
    "SELECT * FROM produtos WHERE id = ?",
    [123]
);

// UPDATE
$alterados = $db->execute(
    "UPDATE produtos SET preco = ? WHERE id = ?",
    [2500, 123]
);

// DELETE
$deletados = $db->execute(
    "DELETE FROM produtos WHERE id = ?",
    [123]
);

// TRANSAÇÃO
try {
    $db->beginTransaction();
    
    $db->execute("INSERT INTO vendas ...", [...]);
    $db->execute("UPDATE produtos SET estoque = ...", [...]);
    
    $db->commit(); // Confirmar
} catch (Exception $e) {
    $db->rollback(); // Desfazer
    echo "Erro: " . $e->getMessage();
}

// Último erro
echo $db->getLastError();

// Última query (debug)
echo $db->getLastQuery();
?>
```

---

## 📚 EXEMPLO 8: Validação e Erros

```php
<?php
// Em Controller:

public function salvar() {
    // Validar CSRF
    $this->validateCSRF();
    
    // Obter inputs (saneados automaticamente)
    $email = $this->input('email');
    $nome = $this->input('nome');
    
    // Validar
    $erros = [];
    
    if (empty($nome)) {
        $erros['nome'] = 'Nome é obrigatório';
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Email inválido';
    }
    
    // Se há erros
    if (!empty($erros)) {
        return $this->validationError(
            'Verifique os dados',
            $erros
        );
    }
    
    // Se OK, processar
    $model = new ClienteModel();
    $model->create(['nome' => $nome, 'email' => $email]);
    
    return $this->successResponse('Salvo com sucesso!');
}
?>
```

---

## 📚 EXEMPLO 9: AJAX com JavaScript

```javascript
// GET request
ajax.get('/produtos/buscar', {termo: 'notebook'})
    .then(response => {
        if (response.success) {
            console.log(response.data);
        } else {
            alert(response.message);
        }
    })
    .catch(error => console.error(error));

// POST request (CSRF automático)
ajax.post('/produtos/salvar', {
    nome: 'Produto Novo',
    preco: 99.99
})
    .then(response => {
        if (response.success) {
            showNotification('Salvo com sucesso!', 'success');
        }
    });

// PUT request
ajax.put('/produtos/atualizar/123', {
    nome: 'Nome Novo',
    preco: 199.99
});

// DELETE request
ajax.delete('/produtos/deletar/123', {})
    .then(response => location.reload());
```

---

## 📚 EXEMPLO 10: Estrutura HTML Form com CSRF

```html
<form method="POST" action="<?php echo WEB_ROOT; ?>/produtos/salvar">
    <!-- Token CSRF (obrigatório) -->
    <input type="hidden" name="csrf_token" value="<?php echo Session::getCsrfToken(); ?>">
    
    <!-- Campos do formulário -->
    <label>Nome do Produto</label>
    <input type="text" name="nome" required>
    
    <label>Descrição</label>
    <textarea name="descricao"></textarea>
    
    <label>Preço</label>
    <input type="number" name="preco" step="0.01" required>
    
    <label>Estoque</label>
    <input type="number" name="estoque" required>
    
    <!-- Botão -->
    <button type="submit">Salvar Produto</button>
</form>
```

---

## 🎯 RESUMO DOS EXEMPLOS

| Exemplo | Arquivo | O Quê |
|---------|---------|-------|
| 1 | models/ProdutoModel.php | Criar Model |
| 2 | controllers/ProdutoController.php | Criar Controller |
| 3 | views/produtos/listar.php | Criar View |
| 4 | - | Usar Model |
| 5 | - | Usar Controller |
| 6 | - | Usar Session |
| 7 | - | Usar Database |
| 8 | - | Validação |
| 9 | - | AJAX |
| 10 | - | Form com CSRF |

---

## ✨ PRONTO!

Você agora sabe como:
- ✅ Criar um novo Model
- ✅ Criar um novo Controller
- ✅ Criar uma nova View
- ✅ Usar Session & Autenticação
- ✅ Fazer queries no banco
- ✅ Validar dados
- ✅ Usar AJAX
- ✅ Proteger contra CSRF

**Próximo passo: Criar seu primeiro módulo completo!** 🚀
