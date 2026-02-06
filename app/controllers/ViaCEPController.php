<?php

namespace App\Controllers;

use App\Models\ViaCEPModel;

/**
 * ViaCEPController
 * 
 * Gerencia integração com API ViaCEP
 */
class ViaCEPController extends BaseController
{
    private $viaCepModel;

    public function __construct()
    {
        parent::__construct();
        $this->viaCepModel = new ViaCEPModel();
    }

    /**
     * Buscar endereço por CEP
     * GET /viacep?cep=xxxxx
     */
    public function buscar()
    {
        try {
            $cep = $_GET['cep'] ?? '';

            if (!$this->viaCepModel->validarCEP($cep)) {
                $this->retornarErro('CEP inválido', 400);
                return;
            }

            $resultado = $this->viaCepModel->buscarEndereçoPorCEP($cep);

            if (!$resultado) {
                $this->retornarErro('Erro ao buscar CEP', 500);
                return;
            }

            if (isset($resultado['erro'])) {
                $this->retornarJson([
                    'sucesso' => false,
                    'erro' => $resultado['erro']
                ], 404);
                return;
            }

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $resultado
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao buscar CEP: ' . $e->getMessage());
        }
    }

    /**
     * Buscar múltiplos CEPs
     * POST /viacep/multiplos
     */
    public function buscarMultiplos()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $data = $this->obterDadosJSON();
            $ceps = $data['ceps'] ?? [];

            if (!is_array($ceps) || empty($ceps)) {
                $this->retornarErro('CEPs inválidos', 400);
                return;
            }

            $resultados = $this->viaCepModel->buscarMultiplosCEPs($ceps);

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $resultados
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao buscar CEPs: ' . $e->getMessage());
        }
    }

    /**
     * Validar CEP
     * GET /viacep/validar?cep=xxxxx
     */
    public function validar()
    {
        try {
            $cep = $_GET['cep'] ?? '';
            $valido = $this->viaCepModel->validarCEP($cep);

            $this->retornarJson([
                'sucesso' => true,
                'valido' => $valido,
                'cep' => $cep
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao validar CEP: ' . $e->getMessage());
        }
    }

    /**
     * Formatar CEP
     * GET /viacep/formatar?cep=xxxxx
     */
    public function formatar()
    {
        try {
            $cep = $_GET['cep'] ?? '';

            if (!$this->viaCepModel->validarCEP($cep)) {
                $this->retornarErro('CEP inválido', 400);
                return;
            }

            $formatado = $this->viaCepModel->formatarCEP($cep);

            $this->retornarJson([
                'sucesso' => true,
                'cep_original' => $cep,
                'cep_formatado' => $formatado
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao formatar CEP: ' . $e->getMessage());
        }
    }

    /**
     * Limpar cache antigo
     * POST /viacep/limpar-cache
     */
    public function limparCache()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->retornarErro('Método não permitido', 405);
                return;
            }

            $sucesso = $this->viaCepModel->limparCacheAntigo();

            if ($sucesso) {
                $this->retornarJson([
                    'sucesso' => true,
                    'mensagem' => 'Cache limpo com sucesso'
                ]);
            } else {
                $this->retornarErro('Erro ao limpar cache');
            }
        } catch (Exception $e) {
            $this->retornarErro('Erro ao limpar cache: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas do cache
     * GET /viacep/stats
     */
    public function obterStats()
    {
        try {
            $stats = $this->viaCepModel->obterEstatisticasCache();

            $this->retornarJson([
                'sucesso' => true,
                'dados' => $stats
            ]);
        } catch (Exception $e) {
            $this->retornarErro('Erro ao obter estatísticas: ' . $e->getMessage());
        }
    }
}
