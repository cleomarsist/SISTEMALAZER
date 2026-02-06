<!DOCTYPE html>
<html>
<head>
    <title>Teste Direto de Inclusão</title>
</head>
<body>
<h1>Teste de Inclusão do Index</h1>
<hr>
<pre>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Testar inclusão do index.php
$_GET['page'] = 'dashboard';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

echo "Incluindo index.php...\n";

try {
    ob_start();
    include 'index.php';
    $output = ob_get_clean();
    echo "SUCESSO! Index.php foi incluído\n\n";
    echo "Primeiras 1000 caracteres do output:\n";
    echo htmlspecialchars(substr($output, 0, 1000));
} catch (Throwable $e) {
    ob_get_clean();
    echo "ERRO ao incluir:\n";
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine();
}
?>
</pre>
</body>
</html>
