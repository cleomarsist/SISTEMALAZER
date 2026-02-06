<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- META TAGS -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo COMPANY_MOTTO; ?>">
    <meta name="author" content="<?php echo COMPANY_NAME; ?>">
    
    <!-- SEGURANÇA -->
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;">
    
    <!-- TÍTULO -->
    <title><?php echo isset($page_title) ? $page_title : COMPANY_NAME; ?></title>
    
    <!-- CSS EXTERNO -->
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/css/layout.css">
    
    <!-- FAVICON -->
    <link rel="icon" type="image/x-icon" href="<?php echo WEB_ROOT; ?>/img/favicon.ico">
    
    <!-- FONTS -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        /* Estilos básicos temporários (será expandido na ETAPA 1) */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            color: #333;
            background-color: #f5f5f5;
        }
        
        a {
            color: #007bff;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        /* HEADER */
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        header nav {
            display: flex;
            gap: 20px;
        }
        
        header a {
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
        }
        
        header a:hover {
            background: rgba(255,255,255,0.2);
            text-decoration: none;
        }
        
        /* MAIN CONTAINER */
        main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .container {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
        }
        
        h1 {
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        h2 {
            margin-top: 20px;
            margin-bottom: 10px;
            color: #333;
        }
        
        /* FOOTER */
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }
        
        /* MENSAGENS */
        .alert {
            padding: 15px;
            margin: 10px 0;
            border-radius: 3px;
            border-left: 4px solid #ddd;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
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
        
        /* FORMULÁRIOS */
        form label {
            display: block;
            margin: 10px 0 5px 0;
            font-weight: 500;
        }
        
        form input,
        form textarea,
        form select {
            width: 100%;
            padding: 10px;
            margin: 0 0 15px 0;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }
        
        form button {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        
        form button:hover {
            background: #764ba2;
        }
        
        /* TABELAS */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-weight: 600;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        table tr:hover {
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <!-- HEADER / NAVEGAÇÃO -->
    <header>
        <div class="container">
            <div class="logo">
                🔥 <?php echo COMPANY_NAME; ?>
            </div>
            
            <nav>
                <?php if (Session::isAuthenticated()): ?>
                    <span>Bem-vindo, <?php echo htmlspecialchars(Session::getUserName()); ?>!</span>
                    <a href="<?php echo WEB_ROOT; ?>/dashboard">Dashboard</a>
                    <a href="<?php echo WEB_ROOT; ?>/login/logout">Logout</a>
                <?php else: ?>
                    <a href="<?php echo WEB_ROOT; ?>/login">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <!-- CONTEÚDO PRINCIPAL -->
    <main>
