<?php

namespace App\Models;

use App\Config\Database;
use PDOException;

/**
 * ProdutoModel
 * 
 * Gerencia operações de produtos (materiais + personalizações)
 * Responsabilidades: CRUD, kits de produtos, preço final
 */
class ProdutoModel extends BaseModel
{
    protected $table = 'produtos';
    protected $primaryKey = 'id_produto';
    protected $fillable = [
        'nome_produto', 'descricao', 'categoria', 'material_id',
        'preco_base', 'preco_final', 'margem_lucro', 'peso',
        'dimensoes', 'data_criacao', 'status'
    ];

    /**
     * Validar dados do produto
     *
     * @param array $data
     * @return array [valid => bool, errors => array]
     */
    public function validar(array $data): array
    {
        $errors = [];

        // Validar nome
        if (empty($data['nome_produto'])) {
            $errors['nome_produto'] = 'Nome do produto é obrigatório';
        } elseif (strlen($data['nome_produto']) < 3) {
            $errors['nome_produto'] = 'Nome deve ter pelo menos 3 caracteres';
        }

        // Validar descrição
        if (empty($data['descricao'])) {
            $errors['descricao'] = 'Descrição é obrigatória';
        }

        // Validar categoria
        if (empty($data['categoria'])) {
            $errors['categoria'] = 'Categoria é obrigatória';
        }

        // Validar material
        if (empty($data['material_id'])) {
            $errors['material_id'] = 'Material é obrigatório';
        }

        // Validar preço base
        $preco = floatval($data['preco_base'] ?? 0);
        if ($preco <= 0) {
            $errors['preco_base'] = 'Preço base deve ser maior que zero';
        }

        // Validar margem de lucro
        $margem = floatval($data['margem_lucro'] ?? 0);
        if ($margem < 0 || $margem > 100) {
            $errors['margem_lucro'] = 'Margem deve estar entre 0 e 100%';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Listar produtos por categoria
     *
     * @param string $categoria
     * @return array
     */
    public function listarPorCategoria(string $categoria): array
    {
        try {
            $query = "SELECT p.*, m.nome_material, m.categoria as categoria_material
                      FROM {$this->table} p
                      LEFT JOIN materiais m ON p.material_id = m.id_material
                      WHERE p.categoria = :categoria AND p.status = 'ativo'
                      ORDER BY p.nome_produto";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':categoria' => $categoria]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarPorCategoria', $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar produtos mais vendidos
     *
     * @param int $limite
     * @return array
     */
    public function listarMaisVendidos(int $limite = 10): array
    {
        try {
            $query = "SELECT p.*, COUNT(io.id_item_orcamento) as quantidade_vendida
                      FROM {$this->table} p
                      LEFT JOIN itens_orcamento io ON p.id_produto = io.produto_id
                      WHERE p.status = 'ativo'
                      GROUP BY p.id_produto
                      ORDER BY quantidade_vendida DESC
                      LIMIT :limite";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarMaisVendidos', $e->getMessage());
            return [];
        }
    }

    /**
     * Calcular preço final com base em personalizações
     *
     * @param int $idProduto
     * @param array $personalizacoes (id_custo => quantidade)
     * @return float
     */
    public function calcularPrecoComPersonalizacoes(int $idProduto, array $personalizacoes = []): float
    {
        try {
            $produto = $this->obterPorId($idProduto);
            
            if (!$produto) {
                return 0;
            }

            $precoFinal = floatval($produto['preco_final']);

            // Adicionar custos de personalização
            foreach ($personalizacoes as $idCusto => $quantidade) {
                // Aqui você buscaria o custo e multiplicaria pela quantidade
                // Exemplo simplificado
                $precoFinal += $quantidade;
            }

            return round($precoFinal, 2);
        } catch (Exception $e) {
            $this->registrarErro('calcularPrecoComPersonalizacoes', $e->getMessage());
            return 0;
        }
    }

    /**
     * Produtos com preço final maior que o base
     *
     * @return array
     */
    public function listarComAumento(): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE preco_final > preco_base AND status = 'ativo'
                      ORDER BY (preco_final - preco_base) DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarComAumento', $e->getMessage());
            return [];
        }
    }

    /**
     * Estatísticas de produtos
     *
     * @return array
     */
    public function obterEstatisticas(): array
    {
        try {
            $query = "SELECT 
                        COUNT(*) as total_produtos,
                        AVG(preco_final) as preco_medio,
                        AVG(margem_lucro) as margem_media,
                        MIN(preco_final) as preco_minimo,
                        MAX(preco_final) as preco_maximo
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
