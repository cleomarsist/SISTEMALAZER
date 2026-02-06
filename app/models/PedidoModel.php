<?php

namespace App\Models;

use App\Config\Database;
use PDOException;

/**
 * PedidoModel
 * 
 * Gerencia operações de pedidos (conversão de orçamentos)
 * Responsabilidades: CRUD, geração de PVs, análise de vendas
 */
class PedidoModel extends BaseModel
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id_pedido';
    protected $fillable = [
        'numero_pedido', 'orcamento_id', 'cliente_id', 'data_pedido',
        'data_entrega_prevista', 'subtotal', 'desconto', 'taxa_adicional',
        'total_pedido', 'status', 'observacoes', 'data_criacao'
    ];

    /**
     * Validar dados do pedido
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

        // Validar orçamento
        if (empty($data['orcamento_id'])) {
            $errors['orcamento_id'] = 'Orçamento é obrigatório';
        }

        // Validar data entrega
        $dataEntrega = $data['data_entrega_prevista'] ?? null;
        if (empty($dataEntrega)) {
            $errors['data_entrega_prevista'] = 'Data de entrega é obrigatória';
        } elseif (strtotime($dataEntrega) < time()) {
            $errors['data_entrega_prevista'] = 'Data de entrega não pode ser no passado';
        }

        // Validar total
        $total = floatval($data['total_pedido'] ?? 0);
        if ($total <= 0) {
            $errors['total_pedido'] = 'Total deve ser maior que zero';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Criar novo pedido a partir de orçamento
     *
     * @param int $orcamentoId
     * @param string $dataEntrega (Y-m-d)
     * @return int|null ID do pedido
     */
    public function criarDePedido(int $orcamentoId, string $dataEntrega): ?int
    {
        try {
            $orcamento = new OrcamentoModel();
            $orc = $orcamento->obterPorId($orcamentoId);

            if (!$orc) {
                return null;
            }

            $data = [
                'numero_pedido' => $this->gerarNumeroPedido(),
                'orcamento_id' => $orcamentoId,
                'cliente_id' => $orc['cliente_id'],
                'data_pedido' => date('Y-m-d'),
                'data_entrega_prevista' => $dataEntrega,
                'subtotal' => $orc['subtotal'],
                'desconto' => $orc['desconto_valor'],
                'taxa_adicional' => $orc['taxa_adicional'],
                'total_pedido' => $orc['total_orcamento'],
                'status' => 'pendente',
                'data_criacao' => date('Y-m-d H:i:s')
            ];

            return $this->crear($data);
        } catch (Exception $e) {
            $this->registrarErro('criarDePedido', $e->getMessage());
            return null;
        }
    }

    /**
     * Gerar número único de pedido
     *
     * @return string
     */
    private function gerarNumeroPedido(): string
    {
        $ano = date('Y');
        
        try {
            $query = "SELECT COALESCE(MAX(CAST(SUBSTRING(numero_pedido, -4) AS UNSIGNED)), 0) + 1 as proxima_seq
                      FROM {$this->table}
                      WHERE numero_pedido LIKE '%-{$ano}-%'";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $seq = str_pad($result['proxima_seq'], 4, '0', STR_PAD_LEFT);
            
            return "PED-{$ano}-{$seq}";
        } catch (Exception $e) {
            $this->registrarErro('gerarNumeroPedido', $e->getMessage());
            return 'PED-' . date('YmdHis');
        }
    }

    /**
     * Listar pedidos por cliente
     *
     * @param int $clienteId
     * @return array
     */
    public function listarPorCliente(int $clienteId): array
    {
        try {
            $query = "SELECT p.*, c.nome_razao_social
                      FROM {$this->table} p
                      JOIN clientes c ON p.cliente_id = c.id_cliente
                      WHERE p.cliente_id = :cliente_id
                      ORDER BY p.data_pedido DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':cliente_id' => $clienteId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarPorCliente', $e->getMessage());
            return [];
        }
    }

    /**
     * Listar pedidos por status
     *
     * @param string $status
     * @return array
     */
    public function listarPorStatus(string $status): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE status = :status
                      ORDER BY data_pedido DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':status' => $status]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarPorStatus', $e->getMessage());
            return [];
        }
    }

    /**
     * Pedidos atrasados (data entrega passou)
     *
     * @return array
     */
    public function listarAtrasados(): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE data_entrega_prevista < DATE(NOW())
                      AND status NOT IN ('entregue', 'cancelado')
                      ORDER BY data_entrega_prevista";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarAtrasados', $e->getMessage());
            return [];
        }
    }

    /**
     * Pedidos para entregar próximos dias
     *
     * @param int $dias
     * @return array
     */
    public function listarParaEntregarProximos(int $dias = 7): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE DATE(data_entrega_prevista) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL :dias DAY)
                      AND status != 'entregue'
                      ORDER BY data_entrega_prevista";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':dias', $dias, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarParaEntregarProximos', $e->getMessage());
            return [];
        }
    }

    /**
     * Análise de vendas por período
     *
     * @param string $dataInicio (Y-m-d)
     * @param string $dataFim (Y-m-d)
     * @return array
     */
    public function analisarVendasPeriodo(string $dataInicio, string $dataFim): array
    {
        try {
            $query = "SELECT 
                        COUNT(*) as total_pedidos,
                        SUM(total_pedido) as valor_total,
                        AVG(total_pedido) as valor_medio,
                        MIN(total_pedido) as valor_minimo,
                        MAX(total_pedido) as valor_maximo,
                        COUNT(DISTINCT cliente_id) as clientes_unicos
                      FROM {$this->table}
                      WHERE DATE(data_pedido) BETWEEN :data_inicio AND :data_fim";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':data_inicio' => $dataInicio,
                ':data_fim' => $dataFim
            ]);
            
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            $this->registrarErro('analisarVendasPeriodo', $e->getMessage());
            return [];
        }
    }

    /**
     * Estatísticas de pedidos
     *
     * @return array
     */
    public function obterEstatisticas(): array
    {
        try {
            $query = "SELECT 
                        COUNT(*) as total_pedidos,
                        SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
                        SUM(CASE WHEN status = 'em_producao' THEN 1 ELSE 0 END) as em_producao,
                        SUM(CASE WHEN status = 'entregue' THEN 1 ELSE 0 END) as entregues,
                        SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                        SUM(total_pedido) as valor_total,
                        AVG(total_pedido) as valor_medio
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
