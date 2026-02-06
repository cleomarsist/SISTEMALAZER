<?php

namespace App\Models;

use App\Config\Database;
use PDOException;

/**
 * MaterialModel
 * 
 * Gerencia operações de materiais para personalização
 * Responsabilidades: CRUD, cálculo de custos, busca por categoria
 */
class MaterialModel extends BaseModel
{
    protected $table = 'materiais';
    protected $primaryKey = 'id_material';
    protected $fillable = [
        'nome_material', 'categoria', 'descricao', 'especificacao_tecnica',
        'custo_unitario', 'margem_lucro', 'preco_venda', 'unidade_medida',
        'quantidade_disponivel', 'estoque_minimo', 'fornecedor_id',
        'data_cadastro', 'status'
    ];

    /**
     * Validar dados do material
     *
     * @param array $data
     * @return array [valid => bool, errors => array]
     */
    public function validar(array $data): array
    {
        $errors = [];

        // Validar nome
        if (empty($data['nome_material'])) {
            $errors['nome_material'] = 'Nome do material é obrigatório';
        } elseif (strlen($data['nome_material']) < 3) {
            $errors['nome_material'] = 'Nome deve ter pelo menos 3 caracteres';
        }

        // Validar categoria
        $categoriasValidas = ['camisetas', 'bolsas', 'bone', 'jaqueta', 'calca', 'shorts', 'moleton', 'gravata', 'meia', 'outro'];
        if (!in_array($data['categoria'] ?? '', $categoriasValidas)) {
            $errors['categoria'] = 'Categoria inválida';
        }

        // Validar custo unitário
        $custo = floatval($data['custo_unitario'] ?? 0);
        if ($custo <= 0) {
            $errors['custo_unitario'] = 'Custo unitário deve ser maior que zero';
        }

        // Validar margem de lucro
        $margem = floatval($data['margem_lucro'] ?? 0);
        if ($margem < 0 || $margem > 100) {
            $errors['margem_lucro'] = 'Margem de lucro deve estar entre 0 e 100%';
        }

        // Validar unidade de medida
        $unidadesValidas = ['un', 'kg', 'metro', 'litro'];
        if (!in_array($data['unidade_medida'] ?? '', $unidadesValidas)) {
            $errors['unidade_medida'] = 'Unidade de medida inválida';
        }

        // Validar quantidade disponível
        if (intval($data['quantidade_disponivel'] ?? 0) < 0) {
            $errors['quantidade_disponivel'] = 'Quantidade não pode ser negativa';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Listar materiais por categoria
     *
     * @param string $categoria
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function listarPorCategoria(string $categoria, int $limit = 10, int $offset = 0): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE categoria = :categoria AND status = 'ativo'
                      ORDER BY nome_material
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':categoria', $categoria);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarPorCategoria', $e->getMessage());
            return [];
        }
    }

    /**
     * Calcular preço de venda baseado em custo e margem
     *
     * @param float $custoUnitario
     * @param float $margemLucro (0-100)
     * @return float
     */
    public function calcularPrecoVenda(float $custoUnitario, float $margemLucro): float
    {
        if ($custoUnitario <= 0) {
            return 0;
        }
        
        $percentualMargem = $margemLucro / 100;
        return round($custoUnitario * (1 + $percentualMargem), 2);
    }

    /**
     * Produtos com estoque baixo (menor que estoque mínimo)
     *
     * @return array
     */
    public function listarEstoqueBaixo(): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE quantidade_disponivel < estoque_minimo 
                      AND status = 'ativo'
                      ORDER BY nome_material";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarEstoqueBaixo', $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar materiais por faixa de preço
     *
     * @param float $precoMin
     * @param float $precoMax
     * @return array
     */
    public function buscarPorFaixaPreco(float $precoMin, float $precoMax): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE preco_venda BETWEEN :preco_min AND :preco_max
                      AND status = 'ativo'
                      ORDER BY preco_venda";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':preco_min' => $precoMin,
                ':preco_max' => $precoMax
            ]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('buscarPorFaixaPreco', $e->getMessage());
            return [];
        }
    }

    /**
     * Atualizar quantidade em estoque
     *
     * @param int $idMaterial
     * @param int $quantidade (positivo para adicionar, negativo para remover)
     * @return bool
     */
    public function atualizarEstoque(int $idMaterial, int $quantidade): bool
    {
        try {
            $query = "UPDATE {$this->table} 
                      SET quantidade_disponivel = quantidade_disponivel + :quantidade
                      WHERE id_material = :id";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':quantidade' => $quantidade,
                ':id' => $idMaterial
            ]);
        } catch (PDOException $e) {
            $this->registrarErro('atualizarEstoque', $e->getMessage());
            return false;
        }
    }

    /**
     * Estatísticas de materiais
     *
     * @return array
     */
    public function obterEstatisticas(): array
    {
        try {
            $query = "SELECT 
                        COUNT(*) as total_materiais,
                        SUM(quantidade_disponivel) as quantidade_total,
                        SUM(custo_unitario * quantidade_disponivel) as valor_total_estoque,
                        AVG(preco_venda) as preco_medio,
                        COUNT(CASE WHEN quantidade_disponivel < estoque_minimo THEN 1 END) as materiais_com_estoque_baixo
                      FROM {$this->table}
                      WHERE status = 'ativo'";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            $this->registrarErro('obterEstatisticas', $e->getMessage());
            return [];
        }
    }
}
