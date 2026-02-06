<?php

namespace App\Models;

use App\Config\Database;
use PDOException;

/**
 * SimulacaoModel
 * 
 * Gerencia operações de simulações de custos e preços
 * Responsabilidades: CRUD, cálculo de preços, análise de rentabilidade
 */
class SimulacaoModel extends BaseModel
{
    protected $table = 'simulacoes';
    protected $primaryKey = 'id_simulacao';
    protected $fillable = [
        'cliente_id', 'nome_simulacao', 'descricao', 'quantidade_simulada',
        'preco_unitario_simulado', 'custo_total_simulado', 'margem_lucro_simulada',
        'preco_total_simulado', 'data_simulacao', 'status'
    ];

    /**
     * Validar dados da simulação
     *
     * @param array $data
     * @return array [valid => bool, errors => array]
     */
    public function validar(array $data): array
    {
        $errors = [];

        // Validar cliente
        if (empty($data['cliente_id'])) {
            $errors['cliente_id'] = 'Cliente é obrigatório';
        }

        // Validar nome
        if (empty($data['nome_simulacao'])) {
            $errors['nome_simulacao'] = 'Nome da simulação é obrigatório';
        }

        // Validar quantidade
        $quantidade = intval($data['quantidade_simulada'] ?? 0);
        if ($quantidade <= 0) {
            $errors['quantidade_simulada'] = 'Quantidade deve ser maior que zero';
        }

        // Validar preço unitário
        $preco = floatval($data['preco_unitario_simulado'] ?? 0);
        if ($preco < 0) {
            $errors['preco_unitario_simulado'] = 'Preço não pode ser negativo';
        }

        // Validar custo total
        $custo = floatval($data['custo_total_simulado'] ?? 0);
        if ($custo < 0) {
            $errors['custo_total_simulado'] = 'Custo não pode ser negativo';
        }

        // Validar margem de lucro
        $margem = floatval($data['margem_lucro_simulada'] ?? 0);
        if ($margem < 0 || $margem > 100) {
            $errors['margem_lucro_simulada'] = 'Margem deve estar entre 0 e 100%';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Criar simulação de preços
     *
     * @param int $clienteId
     * @param string $nome
     * @param int $quantidade
     * @param float $custoUnitario
     * @param float $margemLucro
     * @return array|null ID da simulação
     */
    public function criarSimulacao(
        int $clienteId,
        string $nome,
        int $quantidade,
        float $custoUnitario,
        float $margemLucro
    ): ?int {
        try {
            // Calcular preços
            $percentualMargem = $margemLucro / 100;
            $precoUnitario = $custoUnitario * (1 + $percentualMargem);
            $custoTotal = $custoUnitario * $quantidade;
            $precoTotal = $precoUnitario * $quantidade;

            $data = [
                'cliente_id' => $clienteId,
                'nome_simulacao' => $nome,
                'quantidade_simulada' => $quantidade,
                'preco_unitario_simulado' => round($precoUnitario, 2),
                'custo_total_simulado' => round($custoTotal, 2),
                'margem_lucro_simulada' => $margemLucro,
                'preco_total_simulado' => round($precoTotal, 2),
                'data_simulacao' => date('Y-m-d H:i:s'),
                'status' => 'ativo'
            ];

            return $this->crear($data);
        } catch (Exception $e) {
            $this->registrarErro('criarSimulacao', $e->getMessage());
            return null;
        }
    }

    /**
     * Listar simulações por cliente
     *
     * @param int $clienteId
     * @return array
     */
    public function listarPorCliente(int $clienteId): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE cliente_id = :cliente_id AND status = 'ativo'
                      ORDER BY data_simulacao DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':cliente_id' => $clienteId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarPorCliente', $e->getMessage());
            return [];
        }
    }

    /**
     * Comparar simulações
     *
     * @param array $idsSimulacoes
     * @return array
     */
    public function compararSimulacoes(array $idsSimulacoes): array
    {
        try {
            $placeholders = implode(',', array_fill(0, count($idsSimulacoes), '?'));
            $query = "SELECT * FROM {$this->table} 
                      WHERE id_simulacao IN ($placeholders)
                      ORDER BY preco_total_simulado";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($idsSimulacoes);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('compararSimulacoes', $e->getMessage());
            return [];
        }
    }

    /**
     * Análise de simulações (estatísticas)
     *
     * @return array
     */
    public function analisar(): array
    {
        try {
            $query = "SELECT 
                        COUNT(*) as total_simulacoes,
                        AVG(preco_total_simulado) as preco_medio,
                        AVG(margem_lucro_simulada) as margem_media,
                        SUM(preco_total_simulado) as valor_total_simulado,
                        MIN(preco_total_simulado) as preco_minimo,
                        MAX(preco_total_simulado) as preco_maximo
                      FROM {$this->table}
                      WHERE status = 'ativo'";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            $this->registrarErro('analisar', $e->getMessage());
            return [];
        }
    }

    /**
     * Simulações mais rentáveis
     *
     * @param int $limite
     * @return array
     */
    public function listarMaisRentaveis(int $limite = 10): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE status = 'ativo'
                      ORDER BY (preco_total_simulado - custo_total_simulado) DESC
                      LIMIT :limite";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarMaisRentaveis', $e->getMessage());
            return [];
        }
    }
}
