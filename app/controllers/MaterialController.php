<?php

namespace App\Controllers;

use App\Models\MaterialModel;

/**
 * MaterialController
 * 
 * Gerencia ações relacionadas a materiais
 * Responsabilidades: CRUD, gestão de estoque, análises
 */
class MaterialController extends BaseController
{
    private $materialModel;

    public function __construct()
    {
        parent::__construct();
        $this->materialModel = new MaterialModel();
    }

    /**
     * Listar todos os materiais
     * GET /materiais
     */
    public function listar()
    {
        try {
            $categoria = $_GET['categoria'] ?? null;
            $pagina = intval($_GET['pagina'] ?? 1);
            $limite = 10;
            $offset = ($pagina - 1) * $limite;

            if ($categoria) {
                $materiais = $this->materialModel->listarPorCategoria($categoria, $limite, $offset);
            } else {
                $materiais = $this->materialModel->obterTodos($limite, $offset);
            }

            $stats = $this->materialModel->obterEstatisticas();
            $total = $this->materialModel->contar();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => [
                    'materiais' => $materiais,
                    'total' => $total,
                    'pagina' => $pagina,
                    'limite' => $limite,
                    'stats' => $stats
                ]
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar materiais: ' . $e->getMessage());
        }
    }

    /**
     * Obter detalhes de um material
     * GET /materiais/{id}
     */
    public function obter($id)
    {
        try {
            $material = $this->materialModel->obterPorId($id);

            if (!$material) {
                $this->retornarErro('Material não encontrado', 404);
                return;
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $material
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter material: ' . $e->getMessage());
        }
    }

    /**
     * Criar novo material
     * POST /materiais
     */
    public function criar()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $data = $this->obterDadosJSON();

            // Validar dados
            $validacao = $this->materialModel->validar($data);

            if (!$validacao['valid']) {
                $this->retornarJson([
                    'sucesso' => false,
                    'erros' => $validacao['errors']
                ], 400);
                return;
            }

            // Calcular preço de venda
            $custoUnitario = floatval($data['custo_unitario'] ?? 0);
            $margemLucro = floatval($data['margem_lucro'] ?? 0);
            $data['preco_venda'] = $this->materialModel->calcularPrecoVenda($custoUnitario, $margemLucro);

            $data['data_cadastro'] = date('Y-m-d H:i:s');
            $data['status'] = 'ativo';

            $idMaterial = $this->materialModel->crear($data);

            if ($idMaterial) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Material criado com sucesso',
                    'id' => $idMaterial
                ], 201);
            } else {
                $this->retornarErro('Erro ao criar material');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao criar material: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar material
     * PUT /materiais/{id}
     */
    public function atualizar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $material = $this->materialModel->obterPorId($id);

            if (!$material) {
                $this->retornarErro('Material não encontrado', 404);
                return;
            }

            $data = $this->obterDadosJSON();

            // Se mudar custo ou margem, recalcular preço
            if (isset($data['custo_unitario']) || isset($data['margem_lucro'])) {
                $custo = floatval($data['custo_unitario'] ?? $material['custo_unitario']);
                $margem = floatval($data['margem_lucro'] ?? $material['margem_lucro']);
                $data['preco_venda'] = $this->materialModel->calcularPrecoVenda($custo, $margem);
            }

            if ($this->materialModel->atualizar($id, $data)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Material atualizado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao atualizar material');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao atualizar material: ' . $e->getMessage());
        }
    }

    /**
     * Deletar material
     * DELETE /materiais/{id}
     */
    public function deletar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $material = $this->materialModel->obterPorId($id);

            if (!$material) {
                $this->retornarErro('Material não encontrado', 404);
                return;
            }

            if ($this->materialModel->deletar($id)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Material deletado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao deletar material');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao deletar material: ' . $e->getMessage());
        }
    }

    /**
     * Listar materiais com estoque baixo
     * GET /materiais/estoque/baixo
     */
    public function listarEstoqueBaixo()
    {
        try {
            $materiais = $this->materialModel->listarEstoqueBaixo();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $materiais,
                'total' => count($materiais)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar estoque baixo: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar estoque de material
     * PATCH /materiais/{id}/estoque
     */
    public function atualizarEstoque($id)
    {
        try {
            $material = $this->materialModel->obterPorId($id);

            if (!$material) {
                $this->retornarErro('Material não encontrado', 404);
                return;
            }

            $data = $this->obterDadosJSON();
            $quantidade = intval($data['quantidade'] ?? 0);

            if ($quantidade === 0) {
                $this->retornarErro('Quantidade é obrigatória', 400);
                return;
            }

            if ($this->materialModel->atualizarEstoque($id, $quantidade)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Estoque atualizado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao atualizar estoque');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao atualizar estoque: ' . $e->getMessage());
        }
    }

    /**
     * Buscar por faixa de preço
     * GET /materiais/preco?min=x&max=y
     */
    public function buscarPorFaixaPreco()
    {
        try {
            $min = floatval($_GET['min'] ?? 0);
            $max = floatval($_GET['max'] ?? 9999999);

            if ($min > $max) {
                $this->retornarErro('Min não pode ser maior que max', 400);
                return;
            }

            $materiais = $this->materialModel->buscarPorFaixaPreco($min, $max);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $materiais,
                'total' => count($materiais)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao buscar por preço: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas de materiais
     * GET /materiais/stats
     */
    public function obterStats()
    {
        try {
            $stats = $this->materialModel->obterEstatisticas();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $stats
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter estatísticas: ' . $e->getMessage());
        }
    }
}
