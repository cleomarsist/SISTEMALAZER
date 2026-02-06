<?php

namespace App\Controllers;

use App\Models\PedidoModel;

/**
 * PedidoController
 * 
 * Gerencia ações relacionadas a pedidos
 */
class PedidoController extends BaseController
{
    private $pedidoModel;

    public function __construct()
    {
        parent::__construct();
        $this->pedidoModel = new PedidoModel();
    }

    /**
     * Listar pedidos
     */
    public function listar()
    {
        try {
            $clienteId = intval($_GET['cliente_id'] ?? 0);
            $status = $_GET['status'] ?? null;
            $limite = intval($_GET['limite'] ?? 10);
            $offset = intval($_GET['offset'] ?? 0);

            if ($status) {
                $pedidos = $this->pedidoModel->listarPorStatus($status);
            } elseif ($clienteId > 0) {
                $pedidos = $this->pedidoModel->listarPorCliente($clienteId);
            } else {
                $pedidos = $this->pedidoModel->obterTodos($limite, $offset);
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $pedidos,
                'total' => count($pedidos)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar pedidos: ' . $e->getMessage());
        }
    }

    /**
     * Obter pedido por ID
     */
    public function obter($id)
    {
        try {
            $pedido = $this->pedidoModel->obterPorId($id);

            if (!$pedido) {
                $this->retornarErro('Pedido não encontrado', 404);
                return;
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $pedido
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter pedido: ' . $e->getMessage());
        }
    }

    /**
     * Criar novo pedido a partir de orçamento
     */
    public function criar()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $data = $this->obterDadosJSON();

            $validacao = $this->pedidoModel->validar($data);

            if (!$validacao['valid']) {
                $this->retornarJson([
                    'sucesso' => false,
                    'erros' => $validacao['errors']
                ], 400);
                return;
            }

            $idPedido = $this->pedidoModel->criarDePedido(
                intval($data['orcamento_id']),
                $data['data_entrega_prevista']
            );

            if ($idPedido) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Pedido criado com sucesso',
                    'id' => $idPedido
                ], 201);
            } else {
                $this->retornarErro('Erro ao criar pedido');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao criar pedido: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar pedido
     */
    public function atualizar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $pedido = $this->pedidoModel->obterPorId($id);

            if (!$pedido) {
                $this->retornarErro('Pedido não encontrado', 404);
                return;
            }

            $data = $this->obterDadosJSON();

            if ($this->pedidoModel->atualizar($id, $data)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Pedido atualizado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao atualizar pedido');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao atualizar pedido: ' . $e->getMessage());
        }
    }

    /**
     * Deletar pedido
     */
    public function deletar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $pedido = $this->pedidoModel->obterPorId($id);

            if (!$pedido) {
                $this->retornarErro('Pedido não encontrado', 404);
                return;
            }

            if ($this->pedidoModel->deletar($id)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Pedido deletado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao deletar pedido');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao deletar pedido: ' . $e->getMessage());
        }
    }

    /**
     * Listar pedidos atrasados
     */
    public function atrasados()
    {
        try {
            $pedidos = $this->pedidoModel->listarAtrasados();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $pedidos,
                'total' => count($pedidos)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar pedidos: ' . $e->getMessage());
        }
    }

    /**
     * Listar pedidos para entrega próximos dias
     */
    public function paraEntregarProximos()
    {
        try {
            $dias = intval($_GET['dias'] ?? 7);

            $pedidos = $this->pedidoModel->listarParaEntregarProximos($dias);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $pedidos,
                'total' => count($pedidos)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar pedidos: ' . $e->getMessage());
        }
    }

    /**
     * Análise de vendas por período
     */
    public function analisarVendas()
    {
        try {
            $dataInicio = $_GET['data_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
            $dataFim = $_GET['data_fim'] ?? date('Y-m-d');

            $analise = $this->pedidoModel->analisarVendasPeriodo($dataInicio, $dataFim);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => array_merge($analise, [
                    'periodo' => [
                        'inicio' => $dataInicio,
                        'fim' => $dataFim
                    ]
                ])
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao analisar vendas: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas de pedidos
     */
    public function obterStats()
    {
        try {
            $stats = $this->pedidoModel->obterEstatisticas();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $stats
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter estatísticas: ' . $e->getMessage());
        }
    }
}
