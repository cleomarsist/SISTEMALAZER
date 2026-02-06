<?php

namespace App\Controllers;

use App\Models\CustoModel;

/**
 * CustoController
 * 
 * Gerencia ações relacionadas a custos
 * Responsabilidades: CRUD, cálculo de custos, análises
 */
class CustoController extends BaseController
{
    private $custoModel;

    public function __construct()
    {
        parent::__construct();
        $this->custoModel = new CustoModel();
    }

    /**
     * Listar todos os custos
     */
    public function listar()
    {
        try {
            $tipo = $_GET['tipo'] ?? null;
            $limite = intval($_GET['limite'] ?? 10);
            $offset = intval($_GET['offset'] ?? 0);

            if ($tipo) {
                $custos = $this->custoModel->listarPorTipo($tipo);
            } else {
                $custos = $this->custoModel->obterTodos($limite, $offset);
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $custos,
                'total' => count($custos)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar custos: ' . $e->getMessage());
        }
    }

    /**
     * Obter custo por ID
     */
    public function obter($id)
    {
        try {
            $custo = $this->custoModel->obterPorId($id);

            if (!$custo) {
                $this->retornarErro('Custo não encontrado', 404);
                return;
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $custo
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter custo: ' . $e->getMessage());
        }
    }

    /**
     * Criar novo custo
     */
    public function criar()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $data = $this->obterDadosJSON();

            $validacao = $this->custoModel->validar($data);

            if (!$validacao['valid']) {
                $this->retornarJson([
                    'sucesso' => false,
                    'erros' => $validacao['errors']
                ], 400);
                return;
            }

            $data['data_criacao'] = date('Y-m-d H:i:s');
            $data['status'] = 'ativo';

            $idCusto = $this->custoModel->crear($data);

            if ($idCusto) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Custo criado com sucesso',
                    'id' => $idCusto
                ], 201);
            } else {
                $this->retornarErro('Erro ao criar custo');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao criar custo: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar custo
     */
    public function atualizar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $custo = $this->custoModel->obterPorId($id);

            if (!$custo) {
                $this->retornarErro('Custo não encontrado', 404);
                return;
            }

            $data = $this->obterDadosJSON();

            if ($this->custoModel->atualizar($id, $data)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Custo atualizado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao atualizar custo');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao atualizar custo: ' . $e->getMessage());
        }
    }

    /**
     * Deletar custo
     */
    public function deletar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $custo = $this->custoModel->obterPorId($id);

            if (!$custo) {
                $this->retornarErro('Custo não encontrado', 404);
                return;
            }

            if ($this->custoModel->deletar($id)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Custo deletado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao deletar custo');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao deletar custo: ' . $e->getMessage());
        }
    }

    /**
     * Calcular custo por quantidade
     */
    public function calcular()
    {
        try {
            $idCusto = intval($_GET['id'] ?? 0);
            $quantidade = floatval($_GET['quantidade'] ?? 0);

            if (!$idCusto || $quantidade <= 0) {
                $this->retornarErro('Parâmetros inválidos', 400);
                return;
            }

            $custo = $this->custoModel->obterPorId($idCusto);

            if (!$custo) {
                $this->retornarErro('Custo não encontrado', 404);
                return;
            }

            $custoTotal = $this->custoModel->calcularCustoQuantidade($idCusto, $quantidade);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => [
                    'id_custo' => $idCusto,
                    'quantidade' => $quantidade,
                    'custo_unitario' => floatval($custo['custo_base']),
                    'custo_total' => $custoTotal
                ]
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao calcular custo: ' . $e->getMessage());
        }
    }

    /**
     * Análise de custos
     */
    public function analisar()
    {
        try {
            $analise = $this->custoModel->analiseCustos();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $analise
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao analisar custos: ' . $e->getMessage());
        }
    }

    /**
     * Listar custos mais caros
     */
    public function maisCaros()
    {
        try {
            $limite = intval($_GET['limite'] ?? 5);

            $custos = $this->custoModel->listarMaisCaros($limite);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $custos
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar custos: ' . $e->getMessage());
        }
    }
}
