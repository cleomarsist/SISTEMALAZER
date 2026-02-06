<?php
/**
 * Layout Base - Template Principal
 * Núcleo de todas as páginas do sistema
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) . ' - ' : ''; ?>SISTEMA LAZER</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/public/css/style.css">
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .sidebar {
            background-color: #343a40;
            color: #fff;
            min-height: calc(100vh - 56px);
            padding: 20px 0;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
            color: #fff;
            border-left: 3px solid #667eea;
            padding-left: 17px;
        }
        .content {
            background-color: #fff;
            border-radius: 8px;
            margin: 20px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
            margin-bottom: 20px;
        }
        .btn-custom {
            border-radius: 5px;
            padding: 8px 16px;
            font-weight: 500;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,.2);
        }
        .table-hover tbody tr:hover {
            background-color: #f1f3f5 !important;
        }
        .badge-custom {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        .alert {
            border: none;
            border-left: 4px solid;
        }
        .alert-success {
            border-left-color: #28a745;
            background-color: #f1fcf5;
        }
        .alert-danger {
            border-left-color: #dc3545;
            background-color: #fcf1f1;
        }
        .alert-warning {
            border-left-color: #ffc107;
            background-color: #fffbf0;
        }
        .alert-info {
            border-left-color: #17a2b8;
            background-color: #f0f8fb;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-sun"></i> SISTEMA LAZER
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> Usuário
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/perfil"><i class="fas fa-cog"></i> Meu Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Container Principal -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar">
                <div class="position-sticky">
                    <h6 class="text-muted text-uppercase px-3 mt-2 mb-3">Menu</h6>
                    
                    <a href="/" class="<?php echo isset($page) && $page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>

                    <h6 class="text-muted text-uppercase px-3 mt-4 mb-2">Cadastros</h6>
                    
                    <a href="/clientes" class="<?php echo isset($page) && $page === 'clientes' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> Clientes
                    </a>
                    <a href="/materiais" class="<?php echo isset($page) && $page === 'materiais' ? 'active' : ''; ?>">
                        <i class="fas fa-box"></i> Materiais
                    </a>
                    <a href="/custos" class="<?php echo isset($page) && $page === 'custos' ? 'active' : ''; ?>">
                        <i class="fas fa-calculator"></i> Custos
                    </a>
                    <a href="/produtos" class="<?php echo isset($page) && $page === 'produtos' ? 'active' : ''; ?>">
                        <i class="fas fa-cube"></i> Produtos
                    </a>

                    <h6 class="text-muted text-uppercase px-3 mt-4 mb-2">Operações</h6>
                    
                    <a href="/orcamentos" class="<?php echo isset($page) && $page === 'orcamentos' ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt"></i> Orçamentos
                    </a>
                    <a href="/pedidos" class="<?php echo isset($page) && $page === 'pedidos' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart"></i> Pedidos
                    </a>
                    <a href="/simulacoes" class="<?php echo isset($page) && $page === 'simulacoes' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie"></i> Simulações
                    </a>

                    <h6 class="text-muted text-uppercase px-3 mt-4 mb-2">Ferramentas</h6>
                    
                    <a href="/viacep" class="<?php echo isset($page) && $page === 'viacep' ? 'active' : ''; ?>">
                        <i class="fas fa-map-marker-alt"></i> Buscar CEP
                    </a>
                    <a href="/relatorios" class="<?php echo isset($page) && $page === 'relatorios' ? 'active' : ''; ?>">
                        <i class="fas fa-file-pdf"></i> Relatórios
                    </a>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10">
                <div class="content">
                    <!-- Breadcrumb -->
                    <?php if (isset($breadcrumb)): ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i> Home</a></li>
                            <?php foreach ($breadcrumb as $crumb): ?>
                            <li class="breadcrumb-item <?php echo $crumb['active'] ? 'active' : ''; ?>">
                                <?php if (!$crumb['active']): ?>
                                    <a href="<?php echo htmlspecialchars($crumb['url']); ?>">
                                        <?php echo htmlspecialchars($crumb['label']); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($crumb['label']); ?>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                    <?php endif; ?>

                    <!-- Mensagens -->
                    <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); endif; ?>

                    <!-- Conteúdo da Página -->
                    <?php include isset($view) ? $view : '404.php'; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container-fluid">
            <p class="mb-0">
                <i class="fas fa-copyright"></i> 2025-2026 Sistema Lazer - Todos os direitos reservados
            </p>
            <small class="text-muted">Desenvolvido com <i class="fas fa-heart text-danger"></i> para o seu negócio</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (opcional, para AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/public/js/main.js"></script>
    
    <!-- Scripts adicionais (carregado por página) -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
