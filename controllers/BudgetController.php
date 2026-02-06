<?php
// controllers/BudgetController.php
// Controller de Orçamento
// Módulo: Vendas
// Etapa: Operações básicas

require_once(__DIR__ . '/../models/Budget.php');

class BudgetController {
    /**
     * Lista todos os orçamentos
     */
    public function list($status = null, $client_id = null) {
        return Budget::listAll($status, $client_id);
    }

    /**
     * Adiciona novo orçamento
     */
    public function add($data) {
        $budget = new Budget(null, $data['simulation_id'], $data['client_id'], $data['user_id'], $data['total'], 'aberto');
        return $budget->save();
    }

    /**
     * Busca orçamento por ID
     */
    public function getById($id) {
        return Budget::findById($id);
    }

    /**
     * Atualiza status do orçamento
     */
    public function updateStatus($id, $newStatus) {
        $budget = Budget::findById($id);
        if ($budget) {
            return $budget->updateStatus($newStatus);
        }
        return false;
    }
}
