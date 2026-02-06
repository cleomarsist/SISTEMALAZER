<?php

namespace App\Controllers;

use App\Models\SimulacaoModel;

/**
 * SimulacaoController
 * 
 * Gerencia ações relacionadas a simulações de preços
 */
class SimulacaoController extends BaseController
{
    private $simulacaoModel;

    public function __construct()
    {
        parent::__construct();
        $this->simulacaoModel = new SimulacaoModel();
    }

    /**
     * Listar simulações por cliente
     */
    public function listar()
    {
        try {
            $clienteId = intval($_GET['cliente_id'] ?? 0);
            $limite = intval($_GET['limite'] ?? 10);
            $offset = intval($_GET['offset'] ?? 0);

            if ($clienteId > 0) {
                $simulacoes = $this->simulacaoModel->listarPorCliente($clienteId);
            } else {
                $simulacoes = $this->simulacaoModel->obterTodos($limite, $offset);
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $simulacoes,
                'total' => count($simulacoes)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar simulações: ' . $e->getMessage());
        }
    }

    /**
     * Obter simulação por ID
     */
    public function obter($id)
    {
        try {
            $simulacao = $this->simulacaoModel->obterPorId($id);

            if (!$simulacao) {
                $this->retornarErro('Simulação não encontrada', 404);
                return;
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $simulacao
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter simulação: ' . $e->getMessage());
        }
    }

    /**
     * Criar nova simulação
     */
    public function criar()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $data = $this->obterDadosJSON();

            $validacao = $this->simulacaoModel->validar($data);

            if (!$validacao['valid']) {
                $this->retornarJson([
                    'sucesso' => false,
                    'erros' => $validacao['errors']
                ], 400);
                return;
            }

            $idSimulacao = $this->simulacaoModel->criarSimulacao(
                intval($data['cliente_id']),
                $data['nome_simulacao'],
                intval($data['quantidade_simulada']),
                floatval($data['preco_unitario_simulado']) ?? 0,
                floatval($data['margem_lucro_simulada'])
            );

            if ($idSimulacao) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Simulação criada com sucesso',
                    'id' => $idSimulacao
                ], 201);
            } else {
                $this->retornarErro('Erro ao criar simulação');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao criar simulação: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar simulação
     */
    public function atualizar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $simulacao = $this->simulacaoModel->obterPorId($id);

            if (!$simulacao) {
                $this->retornarErro('Simulação não encontrada', 404);
                return;
            }

            $data = $this->obterDadosJSON();

            if ($this->simulacaoModel->atualizar($id, $data)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Simulação atualizada com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao atualizar simulação');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao atualizar simulação: ' . $e->getMessage());
        }
    }

    /**
     * Deletar simulação
     */
    public function deletar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $simulacao = $this->simulacaoModel->obterPorId($id);

            if (!$simulacao) {
                $this->retornarErro('Simulação não encontrada', 404);
                return;
            }

            if ($this->simulacaoModel->deletar($id)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Simulação deletada com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao deletar simulação');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao deletar simulação: ' . $e->getMessage());
        }
    }

    /**
     * Comparar simulações
     */
    public function comparar()
    {
        try {
            $idsJson = $_GET['ids'] ?? '[]';
            $ids = json_decode($idsJson, true);

            if (!is_array($ids) || empty($ids)) {
                $this->retornarErro('IDs inválidos', 400);
                return;
            }

            $simulacoes = $this->simulacaoModel->compararSimulacoes($ids);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $simulacoes
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao comparar simulações: ' . $e->getMessage());
        }
    }

    /**
     * Análise de simulações
     */
    public function analisar()
    {
        try {
            $analise = $this->simulacaoModel->analisar();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $analise
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao analisar simulações: ' . $e->getMessage());
        }
    }

    /**
     * Listar simulações mais rentáveis
     */
    public function maisRentaveis()
    {
        try {
            $limite = intval($_GET['limite'] ?? 10);

            $simulacoes = $this->simulacaoModel->listarMaisRentaveis($limite);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $simulacoes
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar simulações: ' . $e->getMessage());
        }
    }
}
