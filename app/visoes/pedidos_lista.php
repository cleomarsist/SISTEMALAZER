<?php
/**
 * Lista de Pedidos
 */
$page = 'pedidos';
$title = 'Pedidos';
$breadcrumb = [
    ['label' => 'Pedidos', 'url' => '/pedidos', 'active' => true]
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1>
            <i class="fas fa-shopping-cart"></i> Pedidos
        </h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="#" class="btn btn-primary btn-custom">
            <i class="fas fa-plus"></i> Novo Pedido
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <p class="text-muted"><i class="fas fa-info-circle"></i> Nenhum pedido cadastrado ainda.</p>
    </div>
</div>
