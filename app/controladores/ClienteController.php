<?php

namespace App\Controllers;

use App\Models\ClienteModel;

/**
 * ClienteController
 * 
 * Gerencia ações relacionadas a clientes
 * Responsabilidades: CRUD, validações, redirecionamentos
 */
class ClienteController extends BaseController
{
    private $clienteModel;

    public function __construct()
    {
        parent::__construct();
        $this->clienteModel = new ClienteModel();
    }

    /**
     * Listar todos os clientes
     * GET /clientes
     */
    public function listar()
    {
        try {
            $tipo = $_GET['tipo'] ?? null;
            $pagina = intval($_GET['pagina'] ?? 1);
            $limite = 10;
            $offset = ($pagina - 1) * $limite;

            if ($tipo && in_array($tipo, ['PF', 'PJ'])) {
                $clientes = $this->clienteModel->listarPorTipo($tipo, $limite, $offset);
            } else {
                $clientes = $this->clienteModel->obterTodos($limite, $offset);
            }

            $stats = $this->clienteModel->contarPorTipo();
            $total = $this->clienteModel->contar();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => [
                    'clientes' => $clientes,
                    'total' => $total,
                    'pagina' => $pagina,
                    'limite' => $limite,
                    'stats' => $stats
                ]
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao listar clientes: ' . $e->getMessage());
        }
    }

    /**
     * Obter detalhes de um cliente
     * GET /clientes/{id}
     */
    public function obter($id)
    {
        try {
            $cliente = $this->clienteModel->obterPorId($id);

            if (!$cliente) {
                $this->retornarErro('Cliente não encontrado', 404);
                return;
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $cliente
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter cliente: ' . $e->getMessage());
        }
    }

    /**
     * Criar novo cliente
     * POST /clientes
     */
    public function criar()
    {
        try {
            // Validar método
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            // Obter dados do requisiçã
            $data = $this->obterDadosJSON();

            // Validar dados
            $validacao = $this->clienteModel->validar($data);

            if (!$validacao['valid']) {
                $this->retornarJson([
                    'sucesso' => false,
                    'erros' => $validacao['errors']
                ], 400);
                return;
            }

            // Verificar se CPF/CNPJ já existe
            $cpfCnpj = preg_replace('/[^0-9]/', '', $data['cpf_cnpj']);
            $existente = $this->clienteModel->buscarPorCPFCNPJ($cpfCnpj);

            if ($existente) {
                $this->retornarJson([
                    'sucesso' => false,
                    'erro' => 'CPF/CNPJ já cadastrado'
                ], 409);
                return;
            }

            // Adicionar data de cadastro
            $data['data_cadastro'] = date('Y-m-d H:i:s');
            $data['status'] = 'ativo';

            // Criar cliente
            $idCliente = $this->clienteModel->crear($data);

            if ($idCliente) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Cliente criado com sucesso',
                    'id' => $idCliente
                ], 201);
            } else {
                $this->retornarErro('Erro ao criar cliente');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao criar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar cliente
     * PUT /clientes/{id}
     */
    public function atualizar($id)
    {
        try {
            // Validar método
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            // Verificar existência
            $cliente = $this->clienteModel->obterPorId($id);

            if (!$cliente) {
                $this->retornarErro('Cliente não encontrado', 404);
                return;
            }

            // Obter dados do requisição
            $data = $this->obterDadosJSON();

            // Validar dados (importante: CPF/CNPJ não pode mudar)
            if (isset($data['cpf_cnpj'])) {
                unset($data['cpf_cnpj']);
            }

            $validacao = $this->clienteModel->validar(array_merge($cliente, $data));

            if (!$validacao['valid']) {
                $this->retornarJson([
                    'sucesso' => false,
                    'erros' => $validacao['errors']
                ], 400);
                return;
            }

            // Atualizar
            if ($this->clienteModel->atualizar($id, $data)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Cliente atualizado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao atualizar cliente');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao atualizar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Deletar cliente
     * DELETE /clientes/{id}
     */
    public function deletar($id)
    {
        try {
            // Validar método
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            // Verificar existência
            $cliente = $this->clienteModel->obterPorId($id);

            if (!$cliente) {
                $this->retornarErro('Cliente não encontrado', 404);
                return;
            }

            // Deletar
            if ($this->clienteModel->deletar($id)) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Cliente deletado com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao deletar cliente');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao deletar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Buscar cliente por CPF/CNPJ
     * GET /clientes/buscar/cpf-cnpj?valor=xxx
     */
    public function buscarPorCPFCNPJ()
    {
        try {
            $cpfCnpj = $_GET['valor'] ?? null;

            if (!$cpfCnpj) {
                $this->retornarErro('CPF/CNPJ é obrigatório', 400);
                return;
            }

            $cliente = $this->clienteModel->buscarPorCPFCNPJ($cpfCnpj);

            if (!$cliente) {
                $this->retornarErro('Cliente não encontrado', 404);
                return;
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $cliente
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao buscar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Buscar clientes por localização
     * GET /clientes/localizacao?cidade=xxx&estado=xxx
     */
    public function buscarPorLocalizacao()
    {
        try {
            $cidade = $_GET['cidade'] ?? null;
            $estado = $_GET['estado'] ?? null;

            if (!$cidade || !$estado) {
                $this->retornarErro('Cidade e estado são obrigatórios', 400);
                return;
            }

            $clientes = $this->clienteModel->buscarPorLocalizacao($cidade, $estado);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $clientes,
                'total' => count($clientes)
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao buscar clientes: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas de clientes
     * GET /clientes/stats
     */
    public function obterStats()
    {
        try {
            $stats = $this->clienteModel->contarPorTipo();
            $total = $this->clienteModel->contar();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => [
                    'total' => $total,
                    'por_tipo' => $stats
                ]
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter estatísticas: ' . $e->getMessage());
        }
    }
}
