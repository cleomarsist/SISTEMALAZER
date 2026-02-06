<!--
    VIEW: FORMULÁRIO DE LOGIN
    
    ETAPA 1: ARQUITETURA GERAL
    Arquivo: app/views/login/login_form.php
    
    Formulário de autenticação do sistema.
    Para ETAPA 1, usar credenciais de teste:
    Email: admin@example.com
    Senha: admin123
-->

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo COMPANY_NAME; ?></title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        
        .login-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .login-container .subtitle {
            text-align: center;
            color: #999;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        
        .btn-login:hover {
            opacity: 0.9;
        }
        
        .btn-login:active {
            transform: scale(0.98);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left-color: #17a2b8;
        }
        
        .test-credentials {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            font-size: 13px;
            color: #333;
        }
        
        .test-credentials h4 {
            color: #2e7d32;
            margin-bottom: 10px;
            margin-top: 0;
        }
        
        .test-credentials code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        
        .footer-login {
            text-align: center;
            margin-top: 20px;
            color: #999;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- LOGO / TÍTULO -->
        <h1>🔥 <?php echo COMPANY_NAME; ?></h1>
        <p class="subtitle"><?php echo COMPANY_MOTTO; ?></p>
        
        <!-- FORMULÁRIO DE LOGIN -->
        <form id="loginForm" method="POST" action="<?php echo WEB_ROOT; ?>/login/processar">
            
            <!-- CSRF TOKEN -->
            <input type="hidden" name="csrf_token" value="<?php echo Session::getCsrfToken(); ?>">
            
            <!-- CAMPO: EMAIL -->
            <div class="form-group">
                <label for="email">📧 Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="seu@email.com" 
                    required 
                    autofocus
                >
            </div>
            
            <!-- CAMPO: SENHA -->
            <div class="form-group">
                <label for="senha">🔐 Senha</label>
                <input 
                    type="password" 
                    id="senha" 
                    name="senha" 
                    placeholder="sua senha" 
                    required
                >
            </div>
            
            <!-- BOTÃO DE LOGIN -->
            <button type="submit" class="btn-login">
                ▶ ENTRAR
            </button>
        </form>
        
        <!-- CREDENCIAIS DE TESTE (ETAPA 1) -->
        <div class="test-credentials">
            <h4>🧪 Credenciais de Teste (ETAPA 1)</h4>
            <p>
                Para testar o sistema, use as credenciais abaixo:
            </p>
            <p>
                <strong>Email:</strong> <code>admin@example.com</code><br>
                <strong>Senha:</strong> <code>admin123</code>
            </p>
            <small style="color: #999; display: block; margin-top: 10px;">
                ℹ️ Estas credenciais de teste serão substituídas na ETAPA 3 com um sistema de usuários real integrado ao banco de dados.
            </small>
        </div>
        
        <!-- RODAPÉ -->
        <div class="footer-login">
            <p>&copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?></p>
            <small><?php echo IS_development ? '🛠️ Ambiente de Desenvolvimento' : '🚀 Produção'; ?></small>
        </div>
    </div>
    
    <!-- SCRIPT PARA SUBMISSÃO AJAX -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const csrfToken = formData.get('csrf_token');
            
            // Submete formulário via AJAX (comentado para teste)
            // Em produção, pode usar AJAX para evitar reload
            this.submit();
            
            // Alternativa com AJAX:
            /*
            fetch('<?php echo WEB_ROOT; ?>/login/processar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.data.redirect;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Erro ao fazer login: ' + error);
            });
            */
        });
    </script>
</body>
</html>
