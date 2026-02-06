<?php
// controllers/ClientController.php
// Controller de Cliente/Fornecedor
// Módulo: Clientes/Fornecedores
// Etapa: Operações básicas

require_once(__DIR__ . '/../models/Client.php');

class ClientController {
    /**
     * Lista todos os clientes ou fornecedores
     */
    public function list($type = null) {
        return Client::listAll($type);
    }

    /**
     * Adiciona novo cliente ou fornecedor
     */
    public function add($data) {
        $client = new Client(null, $data['name'], $data['document'], $data['email'], $data['phone'], $data['address'], $data['type']);
        return $client->save();
    }
}
