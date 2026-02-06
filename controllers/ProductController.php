<?php
// controllers/ProductController.php
// Controller de Produto Simples
// Módulo: Produtos
// Etapa: Operações básicas

require_once(__DIR__ . '/../models/Product.php');

class ProductController {
    /**
     * Lista todos os produtos
     */
    public function list() {
        return Product::listAll();
    }

    /**
     * Adiciona novo produto
     */
    public function add($data) {
        $product = new Product(null, $data['name'], $data['description'], $data['unit'], $data['price']);
        return $product->save();
    }
}
