<?php
/**
 * CLASSE BASE PARA TODOS OS CONTROLLERS
 * 
 * ETAPA 1: ARQUITETURA GERAL
 * Módulo: Controllers
 * 
 * Esta é a classe pai de todos os controllers.
 * Fornece métodos comuns (renderizar view, redirecionar, etc).
 * 
 * Padrão MVC:
 * - Controllers = Lógica de negócio, orquestra models e views
 * - Cada controller gerencia um módulo (Clientes, Materiais, etc)
 * - Controllers chamam models para dados e validações
 */

class BaseController {
    
    // ========================================================================
    // 1. PROPRIEDADES PROTEGIDAS
    // ========================================================================
    
    /**
     * Dados a serem passados para a view
     * Array associativo com variáveis disponíveis no template
     * 
     * @var array
     */
    protected $data = [];
    
    /**
     * Nome do módulo/controller
     * Usado para referência e logs
     * 
     * @var string
     */
    protected $module = '';
    
    /**
     * Dados de resposta para API/AJAX
     * Convertido automaticamente para JSON
     * 
     * @var array
     */
    protected $response = [
        'success' => false,
        'message' => '',
        'data' => null,
        'errors' => []
    ];
    
    /**
     * Usuario autenticado na sessão
     * Definido no construtor
     * 
     * @var array|null
     */
    protected $user = null;
    
    // ========================================================================
    // 2. CONSTRUTOR
    // ========================================================================
    
    /**
     * Construtor comon do controller
     * - Verifica autenticação
     * - Define dados padrão
     * - Inicia logger
     */
    public function __construct() {
        // Garante que sessão foi iniciada
        Session::start();
        
        // Inicializa módulo com nome da classe (sem "Controller")
        $this->module = str_replace('Controller', '', get_class($this));
        
        // Se usuário não está autenticado e não está na página de login
        // Redireciona para login (comentado por enquanto)
        // if (!Session::isAuthenticated() && $this->module !== 'Login') {
        //     $this->redirect('/login');
        // }
        
        // Obtém dados de usuário autenticado
        $this->user = [
            'id' => Session::getUserId(),
            'name' => Session::getUserName(),
            'permissions' => Session::get('permissions', [])
        ];
        
        // Dados padrão para todas as views
        $this->data = [
            'company_name' => COMPANY_NAME,
            'user' => $this->user,
            'current_page' => $this->module,
            'csrf_token' => Session::getCsrfToken(),
            'web_root' => WEB_ROOT,
        ];
    }
    
    // ========================================================================
    // 3. PROTEÇÃO E VALIDAÇÕES
    // ========================================================================
    
    /**
     * Verifica se usuário tem permissão específica
     * Se não tiver, retorna erro e para execução
     * 
     * @param string|array $permission Uma ou mais permissões necessárias
     * @return bool true se autorizado
     * 
     * Exemplo:
     * $this->requirePermission('gerenciador');
     */
    protected function requirePermission($permission) {
        // Se não tem permissão, mostra erro
        if (!Session::hasPermission($permission)) {
            $this->unauthorizedError('Você não tem permissão para acessar esta página');
        }
        return true;
    }
    
    /**
     * Requer que usuário esteja autenticado
     * Se não, redireciona para login
     */
    protected function requireAuth() {
        if (!Session::isAuthenticated()) {
            $this->redirect('/login?return=' . urlencode($_SERVER['REQUEST_URI']));
        }
    }
    
    /**
     * Valida CSRF token de um request POST/PUT/DELETE
     * Se inválido, mostra erro
     * 
     * @return bool true se validation passou
     */
    protected function validateCSRF() {
        // Token pode vir em POST ou header
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if (!$token || !Session::validateCsrfToken($token)) {
            $this->errorResponse('Token CSRF inválido', 403);
        }
        
        return true;
    }
    
    /**
     * Verifica se requisição é POST
     * Útil para validar POST em métodos de formulário
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verifica se requisição é AJAX
     */
    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
    
    /**
     * Obtém dados do request ($_GET, $_POST, $_BODY)
     * Saneado e validado
     * 
     * @param string $key Chave específica ou null para todos
     * @param mixed $default Valor padrão se não encontrado
     * @return mixed Dados solicitados
     */
    protected function input($key = null, $default = null) {
        // Combina dados de GET e POST
        $data = array_merge($_GET, $_POST);
        
        // Se pede chave específica
        if ($key !== null) {
            return isset($data[$key]) ? htmlspecialchars($data[$key]) : $default;
        }
        
        // Retorna todos os dados (com saneação básica)
        foreach ($data as $k => $v) {
            $data[$k] = is_string($v) ? htmlspecialchars($v) : $v;
        }
        
        return $data;
    }
    
    // ========================================================================
    // 4. REDIRECIONAMENTO
    // ========================================================================
    
    /**
     * Redireciona para outra página
     * 
     * @param string $url URL para redirecionar (relativa ou absoluta)
     * @param int $statusCode Código HTTP (301, 302, 303)
     * 
     * Exemplo:
     * $this->redirect('/clientes/listar');
     * $this->redirect('https://example.com', 301);
     */
    protected function redirect($url, $statusCode = 302) {
        // Se URL relativa, adiciona raiz da aplicação
        if (!preg_match('~^https?://~i', $url)) {
            $url = WEB_ROOT . $url;
        }
        
        // Define header de redireção
        http_response_code($statusCode);
        header('Location: ' . $url);
        exit;
    }
    
    // ========================================================================
    // 5. RENDERIZAÇÃO DE VIEWS
    // ========================================================================
    
    /**
     * Renderiza uma view (template HTML)
     * 
     * Estrutura de diretórios:
     * app/views/{módulo}/{view}.php
     * 
     * Exemplo:
     * No ClienteController:
     * $this->render('listar', ['clientes' => [...]]);
     * Carrega: app/views/clientes/listar.php
     * 
     * @param string $view Nome do arquivo da view (sem .php)
     * @param array $data Dados adicionais para a view (merge com $this->data)
     * 
     * @return void
     */
    protected function render($view, $data = []) {
        // Merge dados padrão com dados passados
        $this->data = array_merge($this->data, $data);
        
        // Extrair variáveis para estarem disponíveis na view
        // Assim, em view pode-se usar $variavel diretamente
        extract($this->data);
        
        // Caminho do arquivo da view
        $viewFile = APP_PATH . '/visoes/' . strtolower($this->module) . '/' . $view . '.php';
        
        // Verifica se arquivo da view existe
        if (!file_exists($viewFile)) {
            throw new Exception("View não encontrada: {$viewFile}");
        }
        
        // Renderiza layout (header, menu, etc)
        $this->renderLayout('header');
        
        // Carrega arquivo da view
        require $viewFile;
        
        // Renderiza footer (rodapé, etc)
        $this->renderLayout('footer');
    }
    
    /**
     * Renderiza arquivo de layout (header, footer, etc)
     * 
     * @param string $layout Nome do arquivo de layout
     * @return void
     */
    protected function renderLayout($layout) {
        // Extrair variáveis
        extract($this->data);
        
        // Caminho do layout
        $layoutFile = APP_PATH . '/visoes/layout/' . $layout . '.php';
        
        // Se arquivo existe, carrega
        if (file_exists($layoutFile)) {
            require $layoutFile;
        }
    }
    
    /**
     * Renderiza view sem layout (útil para modais, fragments, etc)
     * 
     * @param string $view Nome da view
     * @param array $data Dados para a view
     */
    protected function renderPartial($view, $data = []) {
        // Merge dados
        $this->data = array_merge($this->data, $data);
        extract($this->data);
        
        // Caminho da view
        $viewFile = APP_PATH . '/visoes/' . strtolower($this->module) . '/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("View não encontrada: {$viewFile}");
        }
        
        // Carrega view sem layout
        require $viewFile;
    }
    
    // ========================================================================
    // 6. RESPOSTAS JSON (PARA AJAX/API)
    // ========================================================================
    
    /**
     * Retorna resposta JSON com sucesso
     * 
     * @param string $message Mensagem de sucesso
     * @param array $data Dados adicionais
     * @param int $statusCode Código HTTP
     */
    protected function successResponse($message = 'Operação realizada com sucesso', $data = null, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
        
        exit;
    }
    
    /**
     * Retorna resposta JSON com erro
     * 
     * @param string $message Mensagem de erro
     * @param int $statusCode Código HTTP (padrão 400)
     * @param array $errors Array com campos de erro
     */
    protected function errorResponse($message = 'Erro ao processar', $statusCode = 400, $errors = []) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ]);
        
        exit;
    }
    
    /**
     * Retorna erro 404 (não encontrado)
     * 
     * @param string $message Mensagem customizada
     */
    protected function notFoundError($message = 'Recurso não encontrado') {
        $this->errorResponse($message, 404);
    }
    
    /**
     * Retorna erro 403 (não autorizado)
     * 
     * @param string $message Mensagem customizada
     */
    protected function unauthorizedError($message = 'Acesso não autorizado') {
        $this->errorResponse($message, 403);
    }
    
    /**
     * Retorna erro 400 (requisição inválida)
     * 
     * @param string $message Mensagem customizada
     * @param array $errors Array com erros de validação
     */
    protected function validationError($message = 'Dados inválidos', $errors = []) {
        $this->errorResponse($message, 400, $errors);
    }
    
    // ========================================================================
    // 7. LOGGING
    // ========================================================================
    
    /**
     * Registra uma ação no log
     * Útil para auditoria e debug
     * 
     * @param string $action Ação realizada
     * @param string $description Descrição da ação
     * @param string $level Nível do log (info, warning, error)
     */
    protected function log($action, $description = '', $level = 'info') {
        $message = sprintf(
            "[%s] [%s] [%s] %s - %s | Usuário: %d | IP: %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $this->module,
            $action,
            $description,
            Session::getUserId() ?? 0,
            $this->getUserIP()
        );
        
        @file_put_contents(
            LOGS_PATH . '/application.log',
            $message,
            FILE_APPEND | LOCK_EX
        );
    }
    
    /**
     * Retorna IP do usuário
     */
    private function getUserIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }
    
    // ========================================================================
    // 8. HELPERS
    // ========================================================================
    
    /**
     * Formata um valor monetário
     * 
     * @param float $value Valor a formatar
     * @return string Valor formatado (ex: R$ 1.234,56)
     */
    protected function formatMoney($value) {
        return CURRENCY_SYMBOL . ' ' . number_format($value, 2, DECIMAL_SEPARATOR, THOUSANDS_SEPARATOR);
    }
    
    /**
     * Formata uma data para exibição
     * 
     * @param string $date Data em formato YYYY-MM-DD ou timestamp
     * @return string Data formatada (ex: 01/01/2025 14:30)
     */
    protected function formatDate($date) {
        if (is_numeric($date)) {
            return date('d/m/Y H:i', $date);
        } else {
            return date('d/m/Y H:i', strtotime($date));
        }
    }
}
?>
