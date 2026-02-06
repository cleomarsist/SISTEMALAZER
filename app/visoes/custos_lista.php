<?php
/**
 * Lista de Custos
 */
$page = 'custos';
$title = 'Custos';
$breadcrumb = [
    ['label' => 'Custos', 'url' => '/custos', 'active' => true]
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1>
            <i class="fas fa-money-bill-wave"></i> Custos
        </h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="#" class="btn btn-primary btn-custom">
            <i class="fas fa-plus"></i> Novo Custo
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <p class="text-muted"><i class="fas fa-info-circle"></i> Nenhum custo cadastrado ainda.</p>
    </div>
</div>
