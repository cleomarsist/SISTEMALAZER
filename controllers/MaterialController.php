<?php
// controllers/MaterialController.php
// Controller de Material
// Módulo: Materiais
// Etapa: Operações básicas

require_once(__DIR__ . '/../models/Material.php');

class MaterialController {
    /**
     * Lista todos os materiais
     */
    public function list($type = null) {
        return Material::listAll($type);
    }

    /**
     * Adiciona novo material
     */
    public function add($data) {
        $material = new Material(null, $data['name'], $data['type'], $data['unit'], $data['stock'], $data['min_stock'], $data['cost']);
        return $material->save();
    }
}
