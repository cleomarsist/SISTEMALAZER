<?php
/**
 * Lista de Materiais
 */
$page = 'materiais';
$title = 'Materiais';
$breadcrumb = [
    ['label' => 'Materiais', 'url' => '/materiais', 'active' => true]
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1>
            <i class="fas fa-list"></i> Materiais
        </h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="#" class="btn btn-primary btn-custom">
            <i class="fas fa-plus"></i> Novo Material
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <p class="text-muted"><i class="fas fa-info-circle"></i> Nenhum material cadastrado ainda.</p>
    </div>
</div>
