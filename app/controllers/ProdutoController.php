<?php

namespace App\Controllers;

use App\Models\ProdutoModel;

/**
 * ProdutoController
 * 
 * Gerencia ações relacionadas a produtos
 */
class ProdutoController extends BaseController
{
    private $produtoModel;

    public function __construct()
    {
        parent::__construct();
        $this->produtoModel = new ProdutoModel();
    }

    /**
     * Listar todos os produtos
     */
    public function listar()
    {
        try {
            $categoria = $_GET['categoria'] ?? null;
            $limite = intval($_GET['limite'] ?? 10);
            $offset = intval($_GET['offset'] ?? 0);

            if ($categoria) {
                $produtos = $this->produtoModel->listarPorCategoria($categoria);
            } else {
                $produtos = $this->produtoModel->obterTodos($limite, $offset);
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $produtos,
                'total' => count($produtos)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar produtos: ' . $e->getMessage());
        }
    }

    /**
     * Obter produto por ID
     */
    public function obter($id)
    {
        try {
            $produto = $this->produtoModel->obterPorId($id);

            if (!$produto) {
                $this->retornarErro('Produto não encontrado', 404);
                return;
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $produto
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter produto: ' . $e->getMessage());
        }
    }

    /**
     * Criar novo produto
     */
    public function criar()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $data = $this->obterDadosJSON();

            $validacao = $this->produtoModel->validar($data);

            if (!$validacao['valid']) {
                $this->retornarJson([
                    'sucesso' => false,
                    'erros' => $validacao['errors']
                ], 400);
                return;
            }

            // Calcular preço final
            $precoBase = floatval($data['preco_base']);
            $margemLucro = floatval($data['margem_lucro'] ?? 0);
            $percentualMargem = $margemLucro / 100;
            $data['preco_final'] = round($precoBase * (1 + $percentualMargem), 2);

            $data['data_criacao'] = date('Y-m-d H:i:s');
            $data['status'] = 'ativo';

            $idProduto = $this->produtoModel->crear($data);

            if ($idProduto) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Produto criado com sucesso',
                    'id' => $idProduto
                ], 201);
            } else {
                $this->retornarErro('Erro ao criar produto');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao criar produto: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar produto
     */
    public function atualizar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $produto = $this->produtoModel->obterPorId($id);

            if (!$produto) {
                $this->retornarErro('Produto não encontrado', 404);
                return;
            }

            $data = $this->obterDadosJSON();

            // Se mudar preço ou margem, recalcular
            if (isset($data['preco_base']) || isset($data['margem_lucro'])) {
                $preço = floatval($data['preco_base'] ?? $produto['preco_base']);
                $margem = floatval($data['margem_lucro'] ?? $produto['margem_lucro']);
                $percentualMargem = $margem / 100;
                $data['preco_final'] = round($preço * (1 + $percentualMargem), 2);
            }

            if ($this->produtoModel->atualizar($id, $data)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Produto atualizado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao atualizar produto');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    /**
     * Deletar produto
     */
    public function deletar($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $produto = $this->produtoModel->obterPorId($id);

            if (!$produto) {
                $this->retornarErro('Produto não encontrado', 404);
                return;
            }

            if ($this->produtoModel->deletar($id)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Produto deletado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao deletar produto');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao deletar produto: ' . $e->getMessage());
        }
    }

    /**
     * Listar produtos mais vendidos
     */
    public function maisVendidos()
    {
        try {
            $limite = intval($_GET['limite'] ?? 10);

            $produtos = $this->produtoModel->listarMaisVendidos($limite);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $produtos
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar produtos: ' . $e->getMessage());
        }
    }

    /**
     * Calcular preço com personalizações
     */
    public function calcularPreco()
    {
        try {
            $idProduto = intval($_GET['id'] ?? 0);
            $personalizacoesJson = $_GET['personalizacoes'] ?? '{}';
            $personalizacoes = json_decode($personalizacoesJson, true);

            if ($idProduto <= 0) {
                $this->retornarErro('ID do produto inválido', 400);
                return;
            }

            $preco = $this->produtoModel->calcularPrecoComPersonalizacoes($idProduto, $personalizacoes);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => [
                    'id_produto' => $idProduto,
                    'preco' => $preco
                ]
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao calcular preço: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas de produtos
     */
    public function obterStats()
    {
        try {
            $stats = $this->produtoModel->obterEstatisticas();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $stats
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter estatísticas: ' . $e->getMessage());
        }
    }
}
