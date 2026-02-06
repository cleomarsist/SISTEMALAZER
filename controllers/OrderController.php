<?php
// controllers/OrderController.php
// Controller de Pedido
// Módulo: Vendas
// Etapa: Operações básicas

require_once(__DIR__ . '/../models/Order.php');

class OrderController {
    /**
     * Lista todos os pedidos
     */
    public function list($status = null, $client_id = null) {
        return Order::listAll($status, $client_id);
    }

    /**
     * Adiciona novo pedido
     */
    public function add($data) {
        $order = new Order(null, $data['budget_id'], $data['client_id'], $data['user_id'], $data['total'], 'aberto');
        return $order->save();
    }

    /**
     * Busca pedido por ID
     */
    public function getById($id) {
        return Order::findById($id);
    }

    /**
     * Atualiza status do pedido
     */
    public function updateStatus($id, $newStatus) {
        $order = Order::findById($id);
        if ($order) {
            return $order->updateStatus($newStatus);
        }
        return false;
    }
}
