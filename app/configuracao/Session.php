<?php
/**
 * CLASSE DE GERENCIAMENTO DE SESSÃO
 * 
 * ETAPA 1: ARQUITETURA GERAL
 * Módulo: Session
 * 
 * Responsabilidades:
 * - Iniciar e gerenciar sessão do usuário
 * - Controlar timeout de sessão
 * - Armazenar dados de sessão com segurança
 * - Validar CSRF tokens
 * - Destruir sessão ao logout
 * 
 * SEGURANÇA:
 * - Valida timeout de inatividade
 * - Regenera ID de sessão periodicamente
 * - Protege contra fixação de sessão
 */

class Session {
    
    // ========================================================================
    // 1. PROPRIEDADES PRIVADAS
    // ========================================================================
    
    /**
     * Indica se sessão foi iniciada
     * @var bool
     */
    private static $started = false;
    
    /**
     * Timeout de inatividade em segundos
     * @var int
     */
    private static $timeout = 3600; // 1 hora
    
    /**
     * Intervalo para regenerar ID (para segurança)
     * @var int
     */
    private static $regenerate_interval = 300; // 5 minutos
    
    // ========================================================================
    // 2. INICIALIZAÇÃO DA SESSÃO
    // ========================================================================
    
    /**
     * Inicia e configura a sessão com segurança
     * 
     * Procedimento:
     * 1. Verifica se sessão já foi iniciada
     * 2. Inicia a sessão do PHP
     * 3. Define timeout de inatividade
     * 4. Valida timeout
     * 5. Regenera ID periodicamente (segurança)
     * 
     * @return void
     */
    public static function start() {
        // Se sessão já iniciada, não faz nada
        if (self::$started) {
            return;
        }
        
        // Verifica se há sessão ativa antes de iniciar
        if (session_status() === PHP_SESSION_NONE) {
            // Inicia sessão com nome configurado em config.php
            session_start();
        }
        
        // Define timeout configurado
        self::$timeout = SESSION_TIMEOUT;
        
        // Valida timeout: se inativo há muito tempo, destrói sessão
        self::validateTimeout();
        
        // Se primeira vez nesta sessão, registra horário
        if (empty($_SESSION['initialized'])) {
            $_SESSION['initialized'] = time();
            $_SESSION['created_at'] = date('Y-m-d H:i:s');
            $_SESSION['ip_address'] = self::getUserIP();
        }
        
        // Regenera ID de sessão periodicamente (segurança)
        self::regenerateId();
        
        // Marca que sessão foi iniciada
        self::$started = true;
    }
    
    // ========================================================================
    // 3. VALIDAÇÃO DE TIMEOUT
    // ========================================================================
    
    /**
     * Valida se sessão não expirou por inatividade
     * Se expirou, destrói a sessão
     * 
     * Lógica:
     * 1. Pega horário da última atividade
     * 2. Calcula tempo inativo
     * 3. Se maior que timeout, destrói sessão
     * 
     * @return void
     */
    private static function validateTimeout() {
        // Se não há registro de atividade, é primeira vez
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return;
        }
        
        // Calcula quanto tempo ficou inativo
        $elapsed = time() - $_SESSION['last_activity'];
        
        // Se inativo há mais que o timeout, destrói sessão
        if ($elapsed > self::$timeout) {
            // Registra no log que sessão expirou
            self::logActivity('session_timeout', 'Sessão expirada por inatividade');
            
            // Destrói a sessão
            self::destroy();
            
            // Redireciona para login
            header('Location: ' . WEB_ROOT . '/login');
            exit;
        }
        
        // Atualiza horário da última atividade
        $_SESSION['last_activity'] = time();
    }
    
    // ========================================================================
    // 4. REGENERAÇÃO DE ID (SEGURANÇA)
    // ========================================================================
    
    /**
     * Regenera o ID de sessão periodicamente
     * Protege contra ataques de fixação de sessão
     * 
     * Cria um novo ID a cada intervalo definido
     * 
     * @return void
     */
    private static function regenerateId() {
        // Se não há registro de última regeneração
        if (!isset($_SESSION['last_regenerate'])) {
            $_SESSION['last_regenerate'] = time();
            return;
        }
        
        // Calcula tempo desde última regeneração
        $elapsed = time() - $_SESSION['last_regenerate'];
        
        // Se passou o intervalo, regenera ID
        if ($elapsed > self::$regenerate_interval) {
            // Regenera ID (PHP cria novo e copia dados)
            session_regenerate_id(true);
            
            // Atualiza horário de regeneração
            $_SESSION['last_regenerate'] = time();
        }
    }
    
    // ========================================================================
    // 5. GERENCIAR DADOS DE SESSÃO
    // ========================================================================
    
    /**
     * Define um valor na sessão
     * 
     * Exemplo:
     * Session::set('usuario_id', 123);
     * Session::set('permissoes', ['cliente', 'vendedor']);
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Obtém um valor da sessão
     * Retorna null se chave não existe
     * 
     * Exemplo:
     * $usuarioId = Session::get('usuario_id');
     */
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Verifica se uma chave existe na sessão
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove uma chave da sessão
     */
    public static function unset($key) {
        self::start();
        unset($_SESSION[$key]);
    }
    
    /**
     * Retorna toda a sessão como array
     */
    public static function all() {
        self::start();
        return $_SESSION;
    }
    
    // ========================================================================
    // 6. CONTROLE DE AUTENTICAÇÃO
    // ========================================================================
    
    /**
     * Formula para registrar login de usuário
     * Guarda dados de authenticação na sessão
     * 
     * Essa função é chamada pelo controller de login
     * após validar credenciais no banco
     * 
     * @param int $userId ID do usuário logado
     * @param string $userName Nome do usuário
     * @param string $userEmail Email do usuário
     * @param array $permissions Array de permissões do usuário
     * 
     * Exemplo:
     * Session::login($usuario['id'], $usuario['nome'], $usuario['email'], ['cliente']);
     */
    public static function login($userId, $userName, $userEmail, $permissions = []) {
        self::start();
        
        // Armazena dados do usuário autenticado
        $_SESSION['authenticated'] = true;
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $userName;
        $_SESSION['user_email'] = $userEmail;
        $_SESSION['permissions'] = $permissions; // Array de permissões/roles
        $_SESSION['login_at'] = date('Y-m-d H:i:s');
        
        // Regenera ID após login (segurança)
        session_regenerate_id(true);
        
        // Log da ação
        self::logActivity('login', "Usuário {$userName} fez login");
    }
    
    /**
     * Desloga o usuário
     * Remove dados de autenticação e destroi sessão
     */
    public static function logout() {
        self::start();
        
        // Se estava autenticado, registra logout
        if (self::isAuthenticated()) {
            self::logActivity('logout', "Usuário foi deslogado");
        }
        
        // Destrói toda a sessão
        self::destroy();
    }
    
    /**
     * Verifica se usuário está autenticado
     * 
     * @return bool true se autenticado, false caso contrário
     */
    public static function isAuthenticated() {
        self::start();
        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }
    
    /**
     * Retorna ID do usuário autenticado
     * Retorna null se não autenticado
     */
    public static function getUserId() {
        return self::get('user_id');
    }
    
    /**
     * Retorna nome do usuário autenticado
     */
    public static function getUserName() {
        return self::get('user_name');
    }
    
    /**
     * Verifica se usuário tem uma permissão específica
     * 
     * @param string|array $permission Uma ou mais permissões
     * @return bool true se tem a permissão
     */
    public static function hasPermission($permission) {
        self::start();
        
        $permissions = self::get('permissions', []);
        
        // Se é array, verifica se tem qualquer uma das permissões
        if (is_array($permission)) {
            foreach ($permission as $p) {
                if (in_array($p, $permissions)) {
                    return true;
                }
            }
            return false;
        }
        
        // Se é string, verifica se tem exatamente aquela
        return in_array($permission, $permissions);
    }
    
    // ========================================================================
    // 7. PROTEÇÃO CSRF
    // ========================================================================
    
    /**
     * Gera e retorna token CSRF
     * Deve ser incluído em todos os forms do sistema
     * 
     * Exemplo no HTML:
     * <input type="hidden" name="csrf_token" value="<?php echo Session::getCsrfToken(); ?>">
     * 
     * @return string Token CSRF gerado
     */
    public static function getCsrfToken() {
        self::start();
        
        // Se não há token, gera um novo
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Valida um token CSRF fornecido
     * Deve ser chamado sempre que processar um form POST
     * 
     * Exemplo:
     * if ($_POST && !Session::validateCsrfToken($_POST['csrf_token'])) {
     *     die('Token CSRF inválido!');
     * }
     * 
     * @param string $token Token fornecido pelo usuário
     * @return bool true se válido, false caso contrário
     */
    public static function validateCsrfToken($token) {
        self::start();
        
        // Verifica se token foi fornecido
        if (empty($token)) {
            return false;
        }
        
        // Verifica se token coincide com o armazenado
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        // Comparação timing-safe (protege contra timing attacks)
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // ========================================================================
    // 8. LOG DE ATIVIDADES
    // ========================================================================
    
    /**
     * Registra uma atividade na sessão
     * Usado internamente para auditoria
     * 
     * @param string $action Tipo de ação (login, logout, etc)
     * @param string $description Descrição da ação
     */
    private static function logActivity($action, $description = '') {
        $logMessage = sprintf(
            "[%s] [%s] %s | IP: %s | User: %s\n",
            date('Y-m-d H:i:s'),
            $action,
            $description,
            self::getUserIP(),
            self::get('user_name', 'guest')
        );
        
        @file_put_contents(
            LOGS_PATH . '/session.log',
            $logMessage,
            FILE_APPEND | LOCK_EX
        );
    }
    
    /**
     * Retorna endereço IP do usuário
     * Tenta múltiplos métodos para detectar o IP real
     * 
     * @return string Endereço IP do usuário
     */
    private static function getUserIP() {
        // Verifica múltiplas variáveis para encontrar IP real
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }
    
    // ========================================================================
    // 9. DESTRUIÇÃO DE SESSÃO
    // ========================================================================
    
    /**
     * Destroi completamente a sessão
     * Remove todos os dados e cookies
     */
    public static function destroy() {
        self::start();
        
        // Destroi todos os dados da sessão
        $_SESSION = [];
        
        // Remove o cookie da sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destroi a sessão
        session_destroy();
        self::$started = false;
    }
}
?>
