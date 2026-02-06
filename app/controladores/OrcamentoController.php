<?php

namespace App\Controllers;

use App\Models\OrcamentoModel;

/**
 * OrcamentoController
 * 
 * Gerencia ações relacionadas a orçamentos
 */
class OrcamentoController extends BaseController
{
    private $orcamentoModel;

    public function __construct()
    {
        parent::__construct();
        $this->orcamentoModel = new OrcamentoModel();
    }

    /**
     * Listar orçamentos por cliente
     */
    public function listar()
    {
        try {
            $clienteId = intval($_GET['cliente_id'] ?? 0);
            $status = $_GET['status'] ?? null;
            $limite = intval($_GET['limite'] ?? 10);
            $offset = intval($_GET['offset'] ?? 0);

            if ($status) {
                $orcamentos = $this->orcamentoModel->listarPorStatus($status);
            } elseif ($clienteId > 0) {
                $orcamentos = $this->orcamentoModel->listarPorCliente($clienteId);
            } else {
                $orcamentos = $this->orcamentoModel->obterTodos($limite, $offset);
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $orcamentos,
                'total' => count($orcamentos)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar orçamentos: ' . $e->getMessage());
        }
    }

    /**
     * Obter orçamento por ID
     */
    public function obter($id)
    {
        try {
            $orcamento = $this->orcamentoModel->obterPorId($id);

            if (!$orcamento) {
                $this->retornarErro('Orçamento não encontrado', 404);
                return;
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $orcamento
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter orçamento: ' . $e->getMessage());
        }
    }

    /**
     * Criar novo orçamento
     */
    public function criar()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $data = $this->obterDadosJSON();

            $validacao = $this->orcamentoModel->validar($data);

            if (!$validacao['valid']) {
                $this->retornarJson([
                    'sucesso' => false,
                    'erros' => $validacao['errors']
                ], 400);
                return;
            }

            $diasValidade = intval($data['dias_validade'] ?? 7);
            $idOrcamento = $this->orcamentoModel->criarOrcamento(
                intval($data['cliente_id']),
                $diasValidade
            );

            if ($idOrcamento) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Orçamento criado com sucesso',
                    'id' => $idOrcamento
                ], 201);
            } else {
                $this->retornarErro('Erro ao criar orçamento');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao criar orçamento: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar orçamento
     */
    public function atualizar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $orcamento = $this->orcamentoModel->obterPorId($id);

            if (!$orcamento) {
                $this->retornarErro('Orçamento não encontrado', 404);
                return;
            }

            $data = $this->obterDadosJSON();

            // Recalcular total se necessário
            if (isset($data['subtotal']) || isset($data['desconto_percentual']) || isset($data['taxa_adicional'])) {
                $subtotal = floatval($data['subtotal'] ?? $orcamento['subtotal']);
                $descPercent = floatval($data['desconto_percentual'] ?? $orcamento['desconto_percentual']);
                $taxa = floatval($data['taxa_adicional'] ?? $orcamento['taxa_adicional']);

                $calculo = $this->orcamentoModel->calcularTotal($subtotal, $descPercent, $taxa);
                $data['desconto_valor'] = $calculo['desconto_valor'];
                $data['total_orcamento'] = $calculo['total'];
            }

            if ($this->orcamentoModel->atualizar($id, $data)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Orçamento atualizado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao atualizar orçamento');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao atualizar orçamento: ' . $e->getMessage());
        }
    }

    /**
     * Deletar orçamento
     */
    public function deletar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $orcamento = $this->orcamentoModel->obterPorId($id);

            if (!$orcamento) {
                $this->retornarErro('Orçamento não encontrado', 404);
                return;
            }

            if ($this->orcamentoModel->deletar($id)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Orçamento deletado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao deletar orçamento');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao deletar orçamento: ' . $e->getMessage());
        }
    }

    /**
     * Calcular total de orçamento
     */
    public function calcularTotal()
    {
        try {
            $subtotal = floatval($_GET['subtotal'] ?? 0);
            $descontoPercent = floatval($_GET['desconto'] ?? 0);
            $taxa = floatval($_GET['taxa'] ?? 0);

            $calculo = $this->orcamentoModel->calcularTotal($subtotal, $descontoPercent, $taxa);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => [
                    'subtotal' => $subtotal,
                    'desconto_percentual' => $descontoPercent,
                    'desconto_valor' => $calculo['desconto_valor'],
                    'taxa_adicional' => $taxa,
                    'total' => $calculo['total']
                ]
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao calcular total: ' . $e->getMessage());
        }
    }

    /**
     * Listar orçamentos vencidos
     */
    public function vencidos()
    {
        try {
            $orcamentos = $this->orcamentoModel->listarVencidos();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $orcamentos,
                'total' => count($orcamentos)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar vencidos: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas de orçamentos
     */
    public function obterStats()
    {
        try {
            $stats = $this->orcamentoModel->obterEstatisticas();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $stats
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter estatísticas: ' . $e->getMessage());
        }
    }
}
