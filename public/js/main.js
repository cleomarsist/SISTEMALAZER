/**
 * SCRIPT PRINCIPAL DA APLICAÇÃO
 * 
 * ETAPA 1: ARQUITETURA GERAL
 * Arquivo: public/js/main.js
 * 
 * Funções JavaScript globais e utilidades
 * Será expandido nas próximas etapas
 */

// ============================================================================
// 1. CONFIGURAÇÃO GLOBAL
// ============================================================================

/**
 * Objeto global de configuração
 * Preenchido automaticamente em header.php
 * Contém:
 * - WEB_ROOT: URL raiz da aplicação
 * - CSRF_TOKEN: Token CSRF da sessão
 * - COMPANY_NAME: Nome da empresa
 * - IS_AUTHENTICATED: Se usuário está autenticado
 * - USER_ID: ID do usuário logado
 */

const APP = {
    version: '1.0',
    debug: true,
    csrfToken: CSRF_TOKEN || '',
    webRoot: WEB_ROOT || '',
    
    /**
     * Log para console (apenas em development)
     */
    log: function(message, data = null) {
        if (!this.debug) return;
        
        if (data) {
            console.log(`%c[APP] ${message}`, 'color: #667eea; font-weight: bold;', data);
        } else {
            console.log(`%c[APP] ${message}`, 'color: #667eea; font-weight: bold;');
        }
    },
    
    /**
     * Mostra erro no console
     */
    error: function(message, error = null) {
        console.error(`%c[ERROR] ${message}`, 'color: #dc3545; font-weight: bold;', error);
    }
};

// ============================================================================
// 2. AJAX HELPER
// ============================================================================

/**
 * Classe para fazer requisições AJAX facilmente
 * 
 * Uso:
 * ajax.post('/clientes/salvar', {nome: 'João'}).then(response => {
 *     console.log(response);
 * });
 */

const ajax = {
    /**
     * Faz uma requisição GET
     */
    get: function(url, params = {}) {
        // Monta query string
        let queryString = new URLSearchParams(params).toString();
        if (queryString) {
            url += '?' + queryString;
        }
        
        return fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => response.json());
    },
    
    /**
     * Faz uma requisição POST
     */
    post: function(url, data = {}) {
        // Adiciona CSRF token automaticamente
        data.csrf_token = APP.csrfToken;
        
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': APP.csrfToken
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .catch(error => {
            APP.error('Erro na requisição AJAX', error);
            throw error;
        });
    },
    
    /**
     * Faz uma requisição PUT (atualização)
     */
    put: function(url, data = {}) {
        data.csrf_token = APP.csrfToken;
        
        return fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': APP.csrfToken
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json());
    },
    
    /**
     * Faz uma requisição DELETE
     */
    delete: function(url, data = {}) {
        data.csrf_token = APP.csrfToken;
        
        return fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': APP.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json());
    }
};

// ============================================================================
// 3. UTILIDADES
// ============================================================================

/**
 * Formata um valor monetário
 */
function formatMoney(value, currency = 'R$') {
    return currency + ' ' + parseFloat(value).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Formata uma data
 */
function formatDate(date, format = 'DD/MM/YYYY') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    format = format.replace('DD', day);
    format = format.replace('MM', month);
    format = format.replace('YYYY', year);
    format = format.replace('HH', hours);
    format = format.replace('mm', minutes);
    
    return format;
}

/**
 * Valida email
 */
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Valida CPF/CNPJ simples (apenas formato)
 */
function isValidCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    return cpf.length === 11;
}

function isValidCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, '');
    return cnpj.length === 14;
}

/**
 * Mostra notificação (toast)
 */
function showNotification(message, type = 'info', duration = 3000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${getNotificationColor(type)};
        color: white;
        border-radius: 5px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Remove após duração
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, duration);
}

function getNotificationColor(type) {
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    return colors[type] || colors.info;
}

/**
 * Confirma ação antes de executar
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Carrega elemento e mostra loading
 */
function withLoading(element, callback) {
    element.disabled = true;
    element.textContent = 'Carregando...';
    
    Promise.resolve(callback()).finally(() => {
        element.disabled = false;
        element.textContent = element.dataset.originalText || 'Salvar';
    });
}

// ============================================================================
// 4. VALIDAÇÃO DE FORMULÁRIO
// ============================================================================

class FormValidator {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        this.errors = {};
    }
    
    /**
     * Adiciona validação a um campo
     */
    addRule(fieldName, rules) {
        // rules: {required: true, min: 3, max: 100, email: true}
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        
        if (!field) return this;
        
        field.addEventListener('blur', () => {
            this.validateField(fieldName, rules);
        });
        
        return this;
    }
    
    /**
     * Valida um campo específico
     */
    validateField(fieldName, rules) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        const value = field.value.trim();
        
        // Validação obrigatória
        if (rules.required && !value) {
            this.setError(fieldName, 'Este campo é obrigatório');
            return false;
        }
        
        // Validação comprimento mínimo
        if (rules.min && value.length < rules.min) {
            this.setError(fieldName, `Mínimo ${rules.min} caracteres`);
            return false;
        }
        
        // Validação comprimento máximo
        if (rules.max && value.length > rules.max) {
            this.setError(fieldName, `Máximo ${rules.max} caracteres`);
            return false;
        }
        
        // Validação email
        if (rules.email && !isValidEmail(value)) {
            this.setError(fieldName, 'Email inválido');
            return false;
        }
        
        // Se passou em tudo, limpa erro
        this.clearError(fieldName);
        return true;
    }
    
    /**
     * Define erro de campo
     */
    setError(fieldName, message) {
        this.errors[fieldName] = message;
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        field.classList.add('error');
        
        // Mostra mensagem de erro
        let errorElement = field.parentElement.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('small');
            errorElement.className = 'error-message';
            field.parentElement.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }
    
    /**
     * Limpa erro de campo
     */
    clearError(fieldName) {
        delete this.errors[fieldName];
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        field.classList.remove('error');
        
        const errorElement = field.parentElement.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    /**
     * Valida todo o formulário
     */
    validate() {
        return Object.keys(this.errors).length === 0;
    }
}

// ============================================================================
// 5. INICIALIZAÇÃO
// ============================================================================

/**
 * Função de inicialização quando DOM carrega
 */
document.addEventListener('DOMContentLoaded', function() {
    APP.log('Aplicação iniciada');
    
    // Adicione inicializações aqui conforme necessário
    // Exemplo: inicializar plugins, event listeners globais, etc
});

// ============================================================================
// 6. ANIMAÇÕES CSS
// ============================================================================

/**
 * CSS para animações
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    input.error,
    textarea.error,
    select.error {
        border-color: #dc3545 !important;
        background: #fff5f5;
    }
    
    .error-message {
        color: #dc3545;
        font-size: 12px;
        display: block;
        margin-top: 5px;
    }
`;
document.head.appendChild(style);

// ============================================================================
// FIM DO ARQUIVO PRINCIPAL
// ============================================================================
