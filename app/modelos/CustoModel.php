<?php

namespace App\Models;

use App\Config\Database;
use PDOException;

/**
 * CustoModel
 * 
 * Gerencia operações de custos de personalização
 * Responsabilidades: CRUD, cálculo de custos por tipo, análise de margens
 */
class CustoModel extends BaseModel
{
    protected $table = 'custos';
    protected $primaryKey = 'id_custo';
    protected $fillable = [
        'tipo_custo', 'descricao', 'custo_base', 'formula_calculo',
        'unidade_referencia', 'observacoes', 'data_criacao', 'status'
    ];

    /**
     * Validar dados do custo
     *
     * @param array $data
     * @return array [valid => bool, errors => array]
     */
    public function validar(array $data): array
    {
        $errors = [];

        // Validar tipo de custo
        $tiposValidos = ['mao_de_obra', 'material', 'overhead', 'lucro'];
        if (!in_array($data['tipo_custo'] ?? '', $tiposValidos)) {
            $errors['tipo_custo'] = 'Tipo de custo inválido';
        }

        // Validar descrição
        if (empty($data['descricao'])) {
            $errors['descricao'] = 'Descrição é obrigatória';
        }

        // Validar custo base
        $custoBase = floatval($data['custo_base'] ?? 0);
        if ($custoBase < 0) {
            $errors['custo_base'] = 'Custo base não pode ser negativo';
        }

        // Validar fórmula de cálculo
        if (!empty($data['formula_calculo'])) {
            if (!$this->validarFormula($data['formula_calculo'])) {
                $errors['formula_calculo'] = 'Fórmula de cálculo inválida';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validar sintaxe da fórmula de cálculo
     *
     * @param string $formula
     * @return bool
     */
    private function validarFormula(string $formula): bool
    {
        // Verificar se contém apenas operadores válidos e números
        return preg_match('/^[0-9+\-*()\/.,\s]+$/', $formula) === 1;
    }

    /**
     * Listar custos por tipo
     *
     * @param string $tipo
     * @return array
     */
    public function listarPorTipo(string $tipo): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE tipo_custo = :tipo AND status = 'ativo'
                      ORDER BY descricao";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':tipo' => $tipo]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarPorTipo', $e->getMessage());
            return [];
        }
    }

    /**
     * Calcular custo total baseado em quantidade
     *
     * @param int $idCusto
     * @param float $quantidade
     * @return float
     */
    public function calcularCustoQuantidade(int $idCusto, float $quantidade): float
    {
        try {
            $custo = $this->obterPorId($idCusto);
            
            if (!$custo) {
                return 0;
            }

            $custoBase = floatval($custo['custo_base']);
            
            // Se houver fórmula, usar ela
            if (!empty($custo['formula_calculo'])) {
                return $this->calcularPorFormula($custo['formula_calculo'], $quantidade, $custoBase);
            }

            return round($custoBase * $quantidade, 2);
        } catch (Exception $e) {
            $this->registrarErro('calcularCustoQuantidade', $e->getMessage());
            return 0;
        }
    }

    /**
     * Calcular custo usando fórmula customizada
     *
     * @param string $formula
     * @param float $quantidade
     * @param float $custoBase
     * @return float
     */
    private function calcularPorFormula(string $formula, float $quantidade, float $custoBase): float
    {
        // Substituir variáveis
        $formula = str_replace('x', $quantidade, $formula);
        $formula = str_replace('base', $custoBase, $formula);
        
        // Usar eval com segurança (já validamos a fórmula)
        try {
            $resultado = @eval('return ' . $formula . ';');
            return round($resultado, 2);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Análise de custos por tipo
     *
     * @return array
     */
    public function analiseCustos(): array
    {
        try {
            $query = "SELECT 
                        tipo_custo,
                        COUNT(*) as quantidade,
                        SUM(custo_base) as custo_total,
                        AVG(custo_base) as custo_medio,
                        MIN(custo_base) as custo_minimo,
                        MAX(custo_base) as custo_maximo
                      FROM {$this->table}
                      WHERE status = 'ativo'
                      GROUP BY tipo_custo";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('analiseCustos', $e->getMessage());
            return [];
        }
    }

    /**
     * Obter custo total por tipo
     *
     * @return array
     */
    public function getCustoTotalPorTipo(): array
    {
        try {
            $query = "SELECT tipo_custo, SUM(custo_base) as total
                      FROM {$this->table}
                      WHERE status = 'ativo'
                      GROUP BY tipo_custo";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $resultado = [];
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $resultado[$row['tipo_custo']] = floatval($row['total']);
            }
            
            return $resultado;
        } catch (PDOException $e) {
            $this->registrarErro('getCustoTotalPorTipo', $e->getMessage());
            return [];
        }
    }

    /**
     * Custos mais caros
     *
     * @param int $limite
     * @return array
     */
    public function listarMaisCaros(int $limite = 5): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE status = 'ativo'
                      ORDER BY custo_base DESC
                      LIMIT :limite";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarMaisCaros', $e->getMessage());
            return [];
        }
    }
}
