<?php

namespace App\Models;

use App\Config\Database;
use PDOException;

/**
 * OrcamentoModel
 * 
 * Gerencia operações de orçamentos
 * Responsabilidades: CRUD, geração de orçamentos, conversão em pedidos
 */
class OrcamentoModel extends BaseModel
{
    protected $table = 'orcamentos';
    protected $primaryKey = 'id_orcamento';
    protected $fillable = [
        'numero_orcamento', 'cliente_id', 'data_orcamento', 'data_validade',
        'subtotal', 'desconto_percentual', 'desconto_valor', 'taxa_adicional',
        'total_orcamento', 'observacoes', 'status', 'data_criacao'
    ];

    /**
     * Validar dados do orçamento
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

        // Validar subtotal
        $subtotal = floatval($data['subtotal'] ?? 0);
        if ($subtotal < 0) {
            $errors['subtotal'] = 'Subtotal não pode ser negativo';
        }

        // Validar desconto percentual
        $descPercent = floatval($data['desconto_percentual'] ?? 0);
        if ($descPercent < 0 || $descPercent > 100) {
            $errors['desconto_percentual'] = 'Desconto percentual deve estar entre 0 e 100%';
        }

        // Validar taxa adicional
        $taxa = floatval($data['taxa_adicional'] ?? 0);
        if ($taxa < 0) {
            $errors['taxa_adicional'] = 'Taxa adicional não pode ser negativa';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Criar novo orçamento
     *
     * @param int $clienteId
     * @param int $diasValidade
     * @return int|null ID do orçamento
     */
    public function criarOrcamento(int $clienteId, int $diasValidade = 7): ?int
    {
        try {
            $data = [
                'numero_orcamento' => $this->gerarNumeroOrcamento(),
                'cliente_id' => $clienteId,
                'data_orcamento' => date('Y-m-d'),
                'data_validade' => date('Y-m-d', strtotime("+{$diasValidade} days")),
                'subtotal' => 0,
                'desconto_percentual' => 0,
                'desconto_valor' => 0,
                'taxa_adicional' => 0,
                'total_orcamento' => 0,
                'status' => 'rascunho',
                'data_criacao' => date('Y-m-d H:i:s')
            ];

            return $this->crear($data);
        } catch (Exception $e) {
            $this->registrarErro('criarOrcamento', $e->getMessage());
            return null;
        }
    }

    /**
     * Gerar número único de orçamento
     *
     * @return string
     */
    private function gerarNumeroOrcamento(): string
    {
        $ano = date('Y');
        
        try {
            $query = "SELECT COALESCE(MAX(CAST(SUBSTRING(numero_orcamento, -4) AS UNSIGNED)), 0) + 1 as proxima_seq
                      FROM {$this->table}
                      WHERE numero_orcamento LIKE '%-{$ano}-%'";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $seq = str_pad($result['proxima_seq'], 4, '0', STR_PAD_LEFT);
            
            return "ORC-{$ano}-{$seq}";
        } catch (Exception $e) {
            $this->registrarErro('gerarNumeroOrcamento', $e->getMessage());
            return 'ORC-' . date('YmdHis');
        }
    }

    /**
     * Calcular total do orçamento
     *
     * @param float $subtotal
     * @param float $descontoPercentual
     * @param float $taxaAdicional
     * @return array [desconto_valor, total]
     */
    public function calcularTotal(
        float $subtotal,
        float $descontoPercentual = 0,
        float $taxaAdicional = 0
    ): array {
        $descontoValor = round($subtotal * ($descontoPercentual / 100), 2);
        $total = round($subtotal - $descontoValor + $taxaAdicional, 2);

        return [
            'desconto_valor' => $descontoValor,
            'total' => $total
        ];
    }

    /**
     * Listar orçamentos por cliente
     *
     * @param int $clienteId
     * @return array
     */
    public function listarPorCliente(int $clienteId): array
    {
        try {
            $query = "SELECT o.*, c.nome_razao_social
                      FROM {$this->table} o
                      JOIN clientes c ON o.cliente_id = c.id_cliente
                      WHERE o.cliente_id = :cliente_id
                      ORDER BY o.data_orcamento DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':cliente_id' => $clienteId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarPorCliente', $e->getMessage());
            return [];
        }
    }

    /**
     * Listar orçamentos por status
     *
     * @param string $status
     * @return array
     */
    public function listarPorStatus(string $status): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE status = :status
                      ORDER BY data_orcamento DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':status' => $status]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarPorStatus', $e->getMessage());
            return [];
        }
    }

    /**
     * Orçamentos vencidos (data validade passou)
     *
     * @return array
     */
    public function listarVencidos(): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE data_validade < DATE(NOW())
                      AND status != 'pedido'
                      ORDER BY data_validade";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarVencidos', $e->getMessage());
            return [];
        }
    }

    /**
     * Estatísticas de orçamentos
     *
     * @return array
     */
    public function obterEstatisticas(): array
    {
        try {
            $query = "SELECT 
                        COUNT(*) as total_orcamentos,
                        SUM(CASE WHEN status = 'rascunho' THEN 1 ELSE 0 END) as rascunhos,
                        SUM(CASE WHEN status = 'enviado' THEN 1 ELSE 0 END) as enviados,
                        SUM(CASE WHEN status = 'aprovado' THEN 1 ELSE 0 END) as aprovados,
                        SUM(CASE WHEN status = 'pedido' THEN 1 ELSE 0 END) as convertidos,
                        SUM(total_orcamento) as valor_total,
                        AVG(total_orcamento) as valor_medio
                      FROM {$this->table}";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            $this->registrarErro('obterEstatisticas', $e->getMessage());
            return [];
        }
    }
}
