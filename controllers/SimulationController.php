<?php
// controllers/SimulationController.php
// Controller de Simulação
// Módulo: Vendas
// Etapa: Operações básicas

require_once(__DIR__ . '/../models/Simulation.php');

class SimulationController {
    /**
     * Lista todas as simulações
     */
    public function list($client_id = null) {
        return Simulation::listAll($client_id);
    }

    /**
     * Adiciona nova simulação
     */
    public function add($data) {
        $simulation = new Simulation(null, $data['client_id'], $data['user_id'], $data['description'], $data['total']);
        return $simulation->save();
    }

    /**
     * Busca simulação por ID
     */
    public function getById($id) {
        return Simulation::findById($id);
    }
}
