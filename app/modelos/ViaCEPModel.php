<?php

namespace App\Models;

use PDOException;

/**
 * ViaCEPModel
 * 
 * Integração com API ViaCEP para busca de endereços por CEP
 * Responsabilidades: Buscar dados do CEP, cache local, formatação
 */
class ViaCEPModel extends BaseModel
{
    protected $table = 'cep_cache';
    protected $primaryKey = 'id';
    protected $urlAPI = 'https://viacep.com.br/ws';

    /**
     * Buscar endereço por CEP (com cache local)
     *
     * @param string $cep
     * @return array|null
     */
    public function buscarEndereçoPorCEP(string $cep): ?array
    {
        // Validar CEP
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        if (strlen($cep) !== 8) {
            return null;
        }

        // Buscar no cache
        $resultado = $this->buscarNoCache($cep);
        
        if ($resultado) {
            return $resultado;
        }

        // Buscar na API
        $resultado = $this->buscarNaAPI($cep);
        
        if ($resultado && !isset($resultado['erro'])) {
            // Salvar no cache
            $this->salvarNoCache($cep, $resultado);
        }

        return $resultado;
    }

    /**
     * Buscar CEP no cache local
     *
     * @param string $cep
     * @return array|null
     */
    private function buscarNoCache(string $cep): ?array
    {
        try {
            $query = "SELECT * FROM {$this->table} WHERE cep = :cep LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':cep' => $cep]);
            
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($resultado) {
                return json_decode($resultado['dados_endereco'], true);
            }
            
            return null;
        } catch (PDOException $e) {
            $this->registrarErro('buscarNoCache', $e->getMessage());
            return null;
        }
    }

    /**
     * Buscar CEP na API ViaCEP
     *
     * @param string $cep
     * @return array|null
     */
    private function buscarNaAPI(string $cep): ?array
    {
        try {
            $url = "{$this->urlAPI}/{$cep}/json";
            
            $contexto = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'SistemaLazer/1.0'
                ]
            ]);
            
            $resposta = @file_get_contents($url, false, $contexto);
            
            if (!$resposta) {
                $this->registrarErro('buscarNaAPI', 'Timeout ou erro ao conectar ViaCEP');
                return null;
            }
            
            $dados = json_decode($resposta, true);
            
            if (isset($dados['erro'])) {
                return ['erro' => 'CEP não encontrado'];
            }

            return $this->formatarResposta($dados);
        } catch (Exception $e) {
            $this->registrarErro('buscarNaAPI', $e->getMessage());
            return null;
        }
    }

    /**
     * Formatar resposta da API ViaCEP
     *
     * @param array $dados
     * @return array
     */
    private function formatarResposta(array $dados): array
    {
        return [
            'cep' => $dados['cep'] ?? '',
            'endereco' => $dados['logradouro'] ?? '',
            'complemento' => $dados['complemento'] ?? '',
            'bairro' => $dados['bairro'] ?? '',
            'cidade' => $dados['localidade'] ?? '',
            'estado' => $dados['uf'] ?? '',
            'ibge' => $dados['ibge'] ?? '',
            'gia' => $dados['gia'] ?? '',
            'ddd' => $dados['ddd'] ?? '',
            'siafi' => $dados['siafi'] ?? ''
        ];
    }

    /**
     * Salvar CEP no cache local
     *
     * @param string $cep
     * @param array $dados
     * @return bool
     */
    private function salvarNoCache(string $cep, array $dados): bool
    {
        try {
            // Verificar se já existe
            $query = "SELECT COUNT(*) as existe FROM {$this->table} WHERE cep = :cep";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':cep' => $cep]);
            
            $existe = $stmt->fetch(\PDO::FETCH_ASSOC)['existe'] > 0;
            
            if ($existe) {
                return true;
            }

            // Inserir novo registro
            $query = "INSERT INTO {$this->table} (cep, dados_endereco, data_cache) 
                      VALUES (:cep, :dados, NOW())";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':cep' => $cep,
                ':dados' => json_encode($dados)
            ]);
        } catch (PDOException $e) {
            $this->registrarErro('salvarNoCache', $e->getMessage());
            return false;
        }
    }

    /**
     * Validar formato de CEP
     *
     * @param string $cep
     * @return bool
     */
    public function validarCEP(string $cep): bool
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        return strlen($cep) === 8 && is_numeric($cep);
    }

    /**
     * Formatar CEP para exibição (XXXXX-XXX)
     *
     * @param string $cep
     * @return string
     */
    public function formatarCEP(string $cep): string
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        if (strlen($cep) !== 8) {
            return $cep;
        }

        return substr($cep, 0, 5) . '-' . substr($cep, 5);
    }

    /**
     * Expandir CEP formatado (XXXXX-XXX para XXXXXXXX)
     *
     * @param string $cep
     * @return string
     */
    public function expandirCEP(string $cep): string
    {
        return preg_replace('/[^0-9]/', '', $cep);
    }

    /**
     * Buscar múltiplos CEPs
     *
     * @param array $ceps
     * @return array
     */
    public function buscarMultiplosCEPs(array $ceps): array
    {
        $resultados = [];
        
        foreach ($ceps as $cep) {
            $resultado = $this->buscarEndereçoPorCEP($cep);
            $resultados[$cep] = $resultado;
        }

        return $resultados;
    }

    /**
     * Limpar cache antigo (mais de 30 dias)
     *
     * @return bool
     */
    public function limparCacheAntigo(): bool
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE data_cache < DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->registrarErro('limparCacheAntigo', $e->getMessage());
            return false;
        }
    }

    /**
     * Estatísticas do cache
     *
     * @return array
     */
    public function obterEstatisticasCache(): array
    {
        try {
            $query = "SELECT 
                        COUNT(*) as total_ceps,
                        MAX(data_cache) as ultima_busca,
                        MIN(data_cache) as primeira_busca
                      FROM {$this->table}";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            $this->registrarErro('obterEstatisticasCache', $e->getMessage());
            return [];
        }
    }
}
