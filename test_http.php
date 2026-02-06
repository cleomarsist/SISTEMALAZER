<?php
echo "Testando acesso HTTP...\n";

$url = 'http://localhost/SISTEMALAZER/index.php?page=dashboard';
echo "URL: $url\n\n";

$context = stream_context_create(['http' => ['timeout' => 5]]);
$content = @file_get_contents($url, false, $context);

if ($content !== false) {
    echo "✓ SUCESSO! Página carregada\n";
    echo "Tamanho: " . strlen($content) . " bytes\n\n";
    echo "Primeiros 1000 caracteres:\n";
    echo substr($content, 0, 1000);
    echo "\n\n...";
} else {
    echo "✗ ERRO: Não conseguiu acessar a página\n";
    echo "Verifique se o servidor Apache/WAMP está rodando\n";
    echo "localhost:80 deve estar mais acessível\n";
}
?>
