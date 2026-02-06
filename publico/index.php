<?php
/**
 * ARQUIVO PRINCIPAL DE ROTEAMENTO
 * 
 * ETAPA 1: ARQUITETURA GERAL
 * Localização: public/index.php
 * 
 * Este é o arquivo de ponto de entrada da aplicação.
 * Todas as requisições HTTP são direcionadas para este arquivo.
 * 
 * Procedimento:
 * 1. Inclui arquivo de configuração global
 * 2. Inicia sessão
 * 3. Define headers de segurança
 * 4. Rota a requisição para o controller apropriado
 * 5. Renderiza a view
 * 
 * CONFIGURAÇÃO DO SERVIDOR:
 * Se usando Apache, criar arquivo .htaccess na pasta public:
 * 
 * <IfModule mod_rewrite.c>
 *     RewriteEngine On
 *     RewriteCond %{REQUEST_FILENAME} !-f
 *     RewriteCond %{REQUEST_FILENAME} !-d
 *     RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
 * </IfModule>
 * 
 * Isso redireciona todas as requisições para index.php
 */

// ============================================================================
// 1. CARREGAR CONFIGURAÇÕES GLOBAIS
// ============================================================================

// Diretório raiz da aplicação (sobe dois níveis de public/)
// public/ -> RAIZ -> /app
define('WEB_ROOT', 'http://' . $_SERVER['HTTP_HOST'] . '/SISTEMALAZER');

// Carrega arquivo de configuração global (credenciais, constantes, etc)
require_once dirname(dirname(__FILE__)) . '/app/configuracao/config.php';

// ============================================================================
// 2. CARREGADORES AUTOMÁTICOS (AutoLoad das Classes)
// ============================================================================

/**
 * Function de autoload automático de classes
 * Quando uma classe é usada, o PHP chama essa função automaticamente
 * Esta função tenta carregar o arquivo correspondente
 * 
 * Exemplo:
 * Si uso "new Database()", o PHP chama autoload('Database')
 * A função entonces carga app/database/Database.php
 */
spl_autoload_register(function($class) {
    // Mapeia nomes de classes para arquivos
    $paths = [
        // Banco de dados
        'Database' => APP_PATH . '/banco_dados/Database.php',
        
        // Sessão e configuração
        'Session' => APP_PATH . '/configuracao/Session.php',
        
        // Models (base)
        'BaseModel' => APP_PATH . '/modelos/BaseModel.php',
        'ClienteModel' => APP_PATH . '/modelos/ClienteModel.php',
        'MaterialModel' => APP_PATH . '/modelos/MaterialModel.php',
        'CustoModel' => APP_PATH . '/modelos/CustoModel.php',
        'ProdutoModel' => APP_PATH . '/modelos/ProdutoModel.php',
        'OrcamentoModel' => APP_PATH . '/modelos/OrcamentoModel.php',
        'PedidoModel' => APP_PATH . '/modelos/PedidoModel.php',
        'FinanceiroModel' => APP_PATH . '/modelos/FinanceiroModel.php',
        
        // Controllers (base)
        'BaseController' => APP_PATH . '/controladores/BaseController.php',
        'ClienteController' => APP_PATH . '/controladores/ClienteController.php',
        'MaterialController' => APP_PATH . '/controladores/MaterialController.php',
        'CustoController' => APP_PATH . '/controladores/CustoController.php',
        'SimuladorController' => APP_PATH . '/controladores/SimuladorController.php',
        'ProdutoController' => APP_PATH . '/controladores/ProdutoController.php',
        'OrcamentoController' => APP_PATH . '/controladores/OrcamentoController.php',
        'PedidoController' => APP_PATH . '/controladores/PedidoController.php',
        'FinanceiroController' => APP_PATH . '/controladores/FinanceiroController.php',
        'DashboardController' => APP_PATH . '/controladores/DashboardController.php',
        'LoginController' => APP_PATH . '/controladores/LoginController.php',
    ];
    
    // Se a classe está mapeada, carrega o arquivo
    if (isset($paths[$class])) {
        require_once $paths[$class];
    }
});

// ============================================================================
// 3. INICIALIZAR SESSÃO
// ============================================================================

// Inicia gerenciamento de sessão
Session::start();

// ============================================================================
// 4. DEFINIR HEADERS DE SEGURANÇA
// ============================================================================

// Define headers de segurança configurados
foreach (SECURITY_HEADERS as $header => $value) {
    header("$header: $value");
}

// Define charset de resposta
header('Content-Type: text/html; charset=utf-8');

// ============================================================================
// 5. ROTEAMENTO DA APLICAÇÃO
// ============================================================================

/**
 * Processa a requisição e roteia para o controller apropriado
 * 
 * A URL é parseada para extrair:
 * - Módulo (controller)
 * - Ação (método)
 * - Parâmetros (argumentos)
 * 
 * Formato da URL:
 * /modulo/acao/param1/param2/...
 * 
 * Exemplos:
 * /clientes/listar              -> ClienteController->listar()
 * /clientes/salvar/1            -> ClienteController->salvar(1)
 * /orcamentos/gerar_pdf/123     -> OrcamentoController->gerarPdf(123)
 */

// Obter a URL da requisição
$url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';

// Dividir URL em partes
$urlParts = !empty($url) ? explode('/', $url) : [];

// Define valores padrão
$module = !empty($urlParts[0]) ? ucfirst(strtolower($urlParts[0])) : 'dashboard';
$action = !empty($urlParts[1]) ? strtolower($urlParts[1]) : 'index';
$params = array_slice($urlParts, 2); // Dados adicionais

// Mapeia módulo para controller
$controllerName = $module . 'Controller';
$controllerFile = APP_PATH . '/controladores/' . $controllerName . '.php';

try {
    // Verifica se controller existe
    if (!file_exists($controllerFile)) {
        throw new Exception("Controller não encontrado: {$controllerName}");
    }
    
    // Carrega e instancia o controller
    require_once $controllerFile;
    $controller = new $controllerName();
    
    // Verifica se método existe
    if (!method_exists($controller, $action)) {
        throw new Exception("Ação não encontrada: {$action}");
    }
    
    // Chama o método com os parâmetros
    if (empty($params)) {
        $controller->$action();
    } else {
        call_user_func_array([$controller, $action], $params);
    }
    
} catch (Exception $e) {
    // Captura erros e mostra página de erro
    http_response_code(404);
    
    // Se em desenvolvimento, mostra erro detalhado
    if (IS_development) {
        echo "<h1>Erro de Roteamento</h1>";
        echo "<p><strong>Mensagem:</strong> " . $e->getMessage() . "</p>";
        echo "<p><strong>Controller:</strong> {$controllerName}</p>";
        echo "<p><strong>Ação:</strong> {$action}</p>";
        
        if (IS_development) {
            echo "<pre>";
            echo $e->getTraceAsString();
            echo "</pre>";
        }
    } else {
        // Em produção, mostra mensagem genérica
        echo "Página não encontrada.";
    }
    
    // Log do erro
    @file_put_contents(
        LOGS_PATH . '/routing.log',
        date('Y-m-d H:i:s') . " | Erro: " . $e->getMessage() . "\n",
        FILE_APPEND | LOCK_EX
    );
}

// ============================================================================
// FIM DO ARQUIVO PRINCIPAL
// ============================================================================
?>
