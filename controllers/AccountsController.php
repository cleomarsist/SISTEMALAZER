<?php
// controllers/AccountsController.php
// Controller de Contas a Receber e a Pagar
// Módulo: Financeiro
// Etapa: Operações básicas

require_once(__DIR__ . '/../models/AccountsReceivable.php');
require_once(__DIR__ . '/../models/AccountsPayable.php');

class AccountsController {
    /**
     * Lista contas a receber
     */
    public function listReceivable($status = null, $client_id = null) {
        return AccountsReceivable::listAll($status, $client_id);
    }

    /**
     * Lista contas a pagar
     */
    public function listPayable($status = null, $supplier_id = null) {
        return AccountsPayable::listAll($status, $supplier_id);
    }

    /**
     * Adiciona nova conta a receber
     */
    public function addReceivable($data) {
        $account = new AccountsReceivable(null, $data['order_id'], $data['client_id'], $data['due_date'], $data['value'], 'aberto');
        return $account->save();
    }

    /**
     * Adiciona nova conta a pagar
     */
    public function addPayable($data) {
        $account = new AccountsPayable(null, $data['supplier_id'], $data['due_date'], $data['value'], 'aberto');
        return $account->save();
    }

    /**
     * Atualiza status de conta a receber
     */
    public function updateReceivableStatus($id, $newStatus) {
        $account = AccountsReceivable::findById($id);
        if ($account) {
            return $account->updateStatus($newStatus);
        }
        return false;
    }

    /**
     * Atualiza status de conta a pagar
     */
    public function updatePayableStatus($id, $newStatus) {
        $account = AccountsPayable::findById($id);
        if ($account) {
            return $account->updateStatus($newStatus);
        }
        return false;
    }
}
