<?php
/**
 * CONTROLLER DE LOGIN
 * 
 * ETAPA 1: ARQUITETURA GERAL
 * Módulo: Login
 * 
 * Responsabilidade:
 * - Página de login
 * - Validação de credenciais
 * - Gerenciar autenticação
 * 
 * NOTA: Esta é uma versão de teste para ETAPA 1.
 * Na ETAPA 3 será integrada com banco de dados de usuários.
 */

class LoginController extends BaseController {
    
    /**
     * Página de login
     */
    public function index() {
        // Se já está autenticado, redireciona para dashboard
        if (Session::isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        
        // Renderiza formulário de login
        $this->renderPartial('login_form', [
            'page_title' => 'Login - ' . COMPANY_NAME
        ]);
    }
    
    /**
     * Processa login
     * 
     * IMPORTANTE: Para ETAPA 1 (teste), usa credenciais fixas:
     * Email: admin@example.com | Senha: admin123
     * 
     * Na ETAPA 3 será integrada com banco de dados real
     */
    public function processar() {
        // Se não é POST, redireciona
        if (!$this->isPost()) {
            $this->redirect('/login');
        }
        
        // Valida CSRF token
        $this->validateCSRF();
        
        // Obtém dados do formulário
        $email = $this->input('email');
        $senha = $this->input('senha');
        
        // Validação básica
        if (empty($email) || empty($senha)) {
            $this->errorResponse('Email e senha são obrigatórios', 400);
        }
        
        // TESTE PARA ETAPA 1
        // =====================
        // Credenciais padrão para teste
        $usuario_teste = [
            'id' => 1,
            'nome' => 'Administrador',
            'email' => 'admin@example.com',
            'senha' => 'admin123',
            'permissoes' => ['admin', 'vendedor', 'cliente']
        ];
        
        // Verifica credenciais
        if ($email === $usuario_teste['email'] && $senha === $usuario_teste['senha']) {
            // Login bem-sucedido
            Session::login(
                $usuario_teste['id'],
                $usuario_teste['nome'],
                $usuario_teste['email'],
                $usuario_teste['permissoes']
            );
            
            // Log da ação
            $this->log('login_success', "Usuário {$usuario_teste['nome']} fez login com sucesso");
            
            // Se é AJAX, retorna JSON
            if ($this->isAjax()) {
                $this->successResponse('Login realizado com sucesso!', ['redirect' => '/dashboard']);
            } else {
                // Caso contrário, redireciona
                $this->redirect('/dashboard');
            }
        } else {
            // Login falhou
            $this->log('login_failed', "Tentativa de login falhou para email: {$email}");
            
            // Retorna erro
            $this->errorResponse('Email ou senha incorretos', 401);
        }
    }
    
    /**
     * Logout - Desloga usuário
     */
    public function logout() {
        // Registra logout
        $this->log('logout', 'Usuário fez logout');
        
        // Destrói sessão
        Session::logout();
        
        // Redireciona para login
        $this->redirect('/login');
    }
}
?>
