<?php

namespace App\Models;

use App\Config\Database;
use PDOException;

/**
 * ClienteModel
 * 
 * Gerencia operações de clientes (pessoas físicas e jurídicas)
 * Responsabilidades: CRUD, validações, busca por CPF/CNPJ
 */
class ClienteModel extends BaseModel
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $fillable = [
        'tipo_cliente', 'nome_razao_social', 'cpf_cnpj', 'email', 'telefone',
        'cep', 'endereco', 'numero', 'complemento', 'bairro', 'cidade',
        'estado', 'data_cadastro', 'status'
    ];

    /**
     * Validar dados do cliente
     *
     * @param array $data
     * @return array [valid => bool, errors => array]
     */
    public function validar(array $data): array
    {
        $errors = [];

        // Validar tipo de cliente
        if (!isset($data['tipo_cliente']) || !in_array($data['tipo_cliente'], ['PF', 'PJ'])) {
            $errors['tipo_cliente'] = 'Tipo de cliente inválido (PF ou PJ)';
        }

        // Validar nome/razão social
        if (empty($data['nome_razao_social'])) {
            $errors['nome_razao_social'] = 'Nome/Razão Social é obrigatório';
        } elseif (strlen($data['nome_razao_social']) < 3) {
            $errors['nome_razao_social'] = 'Nome deve ter pelo menos 3 caracteres';
        }

        // Validar CPF/CNPJ
        $cpfCnpj = preg_replace('/[^0-9]/', '', $data['cpf_cnpj'] ?? '');
        if (empty($cpfCnpj)) {
            $errors['cpf_cnpj'] = 'CPF/CNPJ é obrigatório';
        } elseif ($data['tipo_cliente'] === 'PF') {
            if (!$this->validarCPF($cpfCnpj)) {
                $errors['cpf_cnpj'] = 'CPF inválido';
            }
        } elseif ($data['tipo_cliente'] === 'PJ') {
            if (!$this->validarCNPJ($cpfCnpj)) {
                $errors['cpf_cnpj'] = 'CNPJ inválido';
            }
        }

        // Validar email
        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        }

        // Validar telefone
        $telefone = preg_replace('/[^0-9]/', '', $data['telefone'] ?? '');
        if (empty($telefone) || strlen($telefone) < 10) {
            $errors['telefone'] = 'Telefone inválido (mínimo 10 dígitos)';
        }

        // Validar CEP
        $cep = preg_replace('/[^0-9]/', '', $data['cep'] ?? '');
        if (empty($cep) || strlen($cep) !== 8) {
            $errors['cep'] = 'CEP inválido';
        }

        // Validar endereço
        if (empty($data['endereco'])) {
            $errors['endereco'] = 'Endereço é obrigatório';
        }

        // Validar número
        if (empty($data['numero'])) {
            $errors['numero'] = 'Número é obrigatório';
        }

        // Validar bairro
        if (empty($data['bairro'])) {
            $errors['bairro'] = 'Bairro é obrigatório';
        }

        // Validar cidade
        if (empty($data['cidade'])) {
            $errors['cidade'] = 'Cidade é obrigatória';
        }

        // Validar estado
        $estadosValidos = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
        if (!in_array($data['estado'] ?? '', $estadosValidos)) {
            $errors['estado'] = 'Estado inválido';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validar CPF usando algoritmo de dígitos verificadores
     *
     * @param string $cpf
     * @return bool
     */
    private function validarCPF(string $cpf): bool
    {
        // Remover caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verificar se tem 11 dígitos
        if (strlen($cpf) !== 11) {
            return false;
        }

        // Verificar se não é sequência repetida
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Validar primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }
        $resto = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;

        if (intval($cpf[9]) !== $digito1) {
            return false;
        }

        // Validar segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }
        $resto = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;

        return intval($cpf[10]) === $digito2;
    }

    /**
     * Validar CNPJ usando algoritmo de dígitos verificadores
     *
     * @param string $cnpj
     * @return bool
     */
    private function validarCNPJ(string $cnpj): bool
    {
        // Remover caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Verificar se tem 14 dígitos
        if (strlen($cnpj) !== 14) {
            return false;
        }

        // Verificar se não é sequência repetida
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        // Validar primeiro dígito verificador
        $soma = 0;
        $multiplicador = 5;
        for ($i = 0; $i < 12; $i++) {
            $soma += intval($cnpj[$i]) * $multiplicador;
            $multiplicador--;
            if ($multiplicador < 2) {
                $multiplicador = 9;
            }
        }
        $resto = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;

        if (intval($cnpj[12]) !== $digito1) {
            return false;
        }

        // Validar segundo dígito verificador
        $soma = 0;
        $multiplicador = 6;
        for ($i = 0; $i < 13; $i++) {
            $soma += intval($cnpj[$i]) * $multiplicador;
            $multiplicador--;
            if ($multiplicador < 2) {
                $multiplicador = 9;
            }
        }
        $resto = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;

        return intval($cnpj[13]) === $digito2;
    }

    /**
     * Buscar cliente por CPF/CNPJ
     *
     * @param string $cpfCnpj
     * @return array|null
     */
    public function buscarPorCPFCNPJ(string $cpfCnpj): ?array
    {
        $cpfCnpj = preg_replace('/[^0-9]/', '', $cpfCnpj);
        
        try {
            $query = "SELECT * FROM {$this->table} WHERE cpf_cnpj = :cpf_cnpj LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':cpf_cnpj' => $cpfCnpj]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            $this->registrarErro('buscarPorCPFCNPJ', $e->getMessage());
            return null;
        }
    }

    /**
     * Listar clientes por tipo (PF ou PJ)
     *
     * @param string $tipo (PF|PJ)
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function listarPorTipo(string $tipo, int $limit = 10, int $offset = 0): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE tipo_cliente = :tipo AND status = 'ativo'
                      ORDER BY nome_razao_social
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':tipo', $tipo);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('listarPorTipo', $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar clientes por cidade/estado
     *
     * @param string $cidade
     * @param string $estado
     * @return array
     */
    public function buscarPorLocalizacao(string $cidade, string $estado): array
    {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE cidade = :cidade AND estado = :estado 
                      AND status = 'ativo'
                      ORDER BY nome_razao_social";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':cidade' => $cidade,
                ':estado' => $estado
            ]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registrarErro('buscarPorLocalizacao', $e->getMessage());
            return [];
        }
    }

    /**
     * Contar clientes por tipo
     *
     * @return array ['PF' => int, 'PJ' => int]
     */
    public function contarPorTipo(): array
    {
        try {
            $query = "SELECT tipo_cliente, COUNT(*) as total 
                      FROM {$this->table} 
                      WHERE status = 'ativo'
                      GROUP BY tipo_cliente";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $resultado = ['PF' => 0, 'PJ' => 0];
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $resultado[$row['tipo_cliente']] = (int)$row['total'];
            }
            
            return $resultado;
        } catch (PDOException $e) {
            $this->registrarErro('contarPorTipo', $e->getMessage());
            return ['PF' => 0, 'PJ' => 0];
        }
    }
}
