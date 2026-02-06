<?php
/**
 * CONFIGURAÇÃO GLOBAL DO SISTEMA ERP - FÊNIX MAGAZINE PERSONALIZADOS
 * 
 * ETAPA 1: ARQUITETURA GERAL
 * 
 * Este arquivo contém todas as configurações globais do sistema:
 * - Informações do banco de dados
 * - Constantes do sistema
 * - Paths do projeto
 * - Configurações de segurança
 * - Idioma e timezone
 * 
 * IMPORTANTE: Nunca exponha as credenciais do banco em produção!
 * Use variáveis de ambiente para dados sensíveis.
 */

// ============================================================================
// 1. INFORMAÇÕES DO BANCO DE DADOS (PDO)
// ============================================================================
// Credenciais de conexão com MySQL
define('DB_HOST', 'localhost');           // Host do servidor MySQL
define('DB_PORT', 3306);                  // Porta padrão MySQL
define('DB_NAME', 'erp_laser');          // Nome do banco de dados
define('DB_USER', 'root');               // Usuário MySQL
define('DB_PASS', '');                   // Senha MySQL (vazio para desenvolvimento)
define('DB_CHARSET', 'utf8mb4');         // Charset do banco (suporta emojis)

// Opções de conexão PDO (segurança e performance)
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lança exceções em erros
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Retorna arrays associativos
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Prepara no servidor (segurança)
    PDO::ATTR_TIMEOUT            => 5                         // Timeout da conexão em segundos
]);

// ============================================================================
// 2. DIRETÓRIOS E PATHS DO PROJETO
// ============================================================================
// Caminho raiz do projeto (ajustar conforme seu servidor)
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));     // Caminho até /
define('APP_PATH', ROOT_PATH . '/app');                       // Caminho até /app
define('PUBLIC_PATH', ROOT_PATH . '/public');                 // Caminho até /public
define('LOGS_PATH', ROOT_PATH . '/logs');                     // Caminho até /logs

// ============================================================================
// 3. CONFIGURAÇÕES DE SEGURANÇA
// ============================================================================
// Pré-configuração de segurança para headers
define('SECURITY_HEADERS', [
    'X-Content-Type-Options' => 'nosniff',                    // Impede MIME type sniffing
    'X-Frame-Options' => 'SAMEORIGIN',                        // Impede clickjacking
    'X-XSS-Protection' => '1; mode=block',                    // Proteção contra XSS
    'Strict-Transport-Security' => 'max-age=31536000'         // Force HTTPS
]);

// Token CSRF para proteção contra ataques
define('CSRF_TOKEN_LENGTH', 32);                             // Tamanho do token
define('SESSION_TIMEOUT', 3600);                             // Timeout de sessão (1 hora)

// ============================================================================
// 4. CONFIGURAÇÕES DO SISTEMA
// ============================================================================
// Nome da empresa
define('COMPANY_NAME', 'Fênix Magazine Personalizados');
define('COMPANY_MOTTO', 'Corte a Laser e Personalizados de Qualidade');

// Moeda padrão do sistema
define('CURRENCY_SYMBOL', 'R$');
define('CURRENCY_CODE', 'BRL');
define('DECIMAL_SEPARATOR', ',');
define('THOUSANDS_SEPARATOR', '.');

// Peças por página (paginação)
define('ITEMS_PER_PAGE', 10);

// ============================================================================
// 5. CONFIGURAÇÕES DE IDIOMA E LOCALIZAÇÃO
// ============================================================================
// Timezone do sistema
date_default_timezone_set('America/Sao_Paulo');

// Idioma padrão
define('DEFAULT_LANGUAGE', 'pt-BR');

// Locale para formatação de números e datas
setlocale(LC_ALL, 'pt_BR.UTF-8');

// ============================================================================
// 6. MODO DE DESENVOLVIMENTO (alterar para false em produção)
// ============================================================================
define('IS_development', true);                              // true: mostra erros, false: oculta

// Se em desenvolvimento, mostra todos os erros
if (IS_development) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . '/php_errors.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . '/php_errors.log');
}

// ============================================================================
// 7. CONFIGURAÇÕES SESSION
// ============================================================================
// Nome da sessão
define('SESSION_NAME', 'FXMAG_SESSION');

// Configurar cookie session (segurança)
ini_set('session.name', SESSION_NAME);
ini_set('session.use_strict_mode', 1);                  // Tira IDs inválidas
ini_set('session.use_only_cookies', 1);                // Apenas cookies
ini_set('session.cookie_httponly', 1);                 // JS não acessa cookie
ini_set('session.cookie_secure', 0);                   // 1 = apenas HTTPS (0 para teste)
ini_set('session.cookie_samesite', 'Lax');             // Proteção contra CSRF
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);    // Tempo de vida da sessão

// ============================================================================
// 8. MENSAGENS DE RESPOSTA DO SISTEMA
// ============================================================================
define('MESSAGES', [
    'success' => 'Operação realizada com sucesso!',
    'error' => 'Erro ao processar solicitação!',
    'not_found' => 'Registro não encontrado!',
    'unauthorized' => 'Acesso não autorizado!',
    'invalid_input' => 'Dados inválidos!',
    'db_error' => 'Erro no banco de dados!',
]);

// ============================================================================
// FIM DA CONFIGURAÇÃO
// ============================================================================
?>
