    </main>
    
    <!-- FOOTER -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?> - Todos os direitos reservados</p>
        <p>
            <small>
                Versão: <?php echo isset($versao) ? $versao : '1.0'; ?> | 
                Ambiente: <?php echo IS_development ? 'Desenvolvimento' : 'Produção'; ?> |
                <?php echo date('d/m/Y H:i:s'); ?>
            </small>
        </p>
    </footer>
    
    <!-- JAVASCRIPT EXTERNO -->
    <script src="<?php echo WEB_ROOT; ?>/js/main.js"></script>
    
    <script>
        // Configuração global JavaScript
        const WEB_ROOT = '<?php echo WEB_ROOT; ?>';
        const CSRF_TOKEN = '<?php echo Session::getCsrfToken(); ?>';
        const COMPANY_NAME = '<?php echo COMPANY_NAME; ?>';
        const IS_AUTHENTICATED = <?php echo Session::isAuthenticated() ? 'true' : 'false'; ?>;
        const USER_ID = <?php echo Session::getUserId() ?? 'null'; ?>;
    </script>
</body>
</html>
