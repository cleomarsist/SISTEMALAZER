# ETAPA 3 - Models (Modelos de Negócio)

## Visão Geral

A Etapa 3 implementa 8 modelos de negócio que encapsulam toda a lógica de dados e operações do sistema. Cada modelo estende o `BaseModel` fornecido pelo framework.

---

## 1. ClienteModel

**Arquivo:** `app/Models/ClienteModel.php`

### Responsabilidades
- Gerenciar dados de clientes (PF e PJ)
- Validar CPF e CNPJ
- Buscar clientes por diferentes critérios
- Contabilizar clientes por tipo

### Métodos Principais

```php
validar(array $data): array
// Valida todos os campos (tipo, nome, CPF/CNPJ, email, telefone, endereço)

validarCPF(string $cpf): bool
// Algoritmo de dígitos verificadores para CPF

validarCNPJ(string $cnpj): bool
// Algoritmo de dígitos verificadores para CNPJ

buscarPorCPFCNPJ(string $cpfCnpj): ?array
// Busca cliente pelo CPF/CNPJ

listarPorTipo(string $tipo, int $limit, int $offset): array
// Lista clientes PF ou PJ com paginação

buscarPorLocalizacao(string $cidade, string $estado): array
// Busca clientes em uma cidade/estado

contarPorTipo(): array
// Retorna ['PF' => int, 'PJ' => int]
```

### Campos da Tabela
- `id_cliente` (PK)
- `tipo_cliente` (PF|PJ)
- `nome_razao_social`
- `cpf_cnpj`
- `email`
- `telefone`
- `cep`, `endereco`, `numero`, `complemento`, `bairro`, `cidade`, `estado`
- `data_cadastro`
- `status` (ativo|inativo)

---

## 2. MaterialModel

**Arquivo:** `app/Models/MaterialModel.php`

### Responsabilidades
- Gerenciar catálogo de materiais para personalização
- Calcular preços baseado em margem de lucro
- Monitorar estoque
- Análises de estoque e preços

### Métodos Principais

```php
validar(array $data): array
// Valida nome, categoria, custo, margem, unidade

calcularPrecoVenda(float $custo, float $margemLucro): float
// Fórmula: preco = custo * (1 + margem/100)

listarPorCategoria(string $categoria, int $limit, int $offset): array
// Lista materiais de uma categoria

listarEstoqueBaixo(): array
// Materiais com quantidade < estoque mínimo

buscarPorFaixaPreco(float $min, float $max): array
// Busca por range de preço

atualizarEstoque(int $idMaterial, int $quantidade): bool
// Adiciona/subtrai quantidade (entrada negativa)

obterEstatisticas(): array
// Retorna: total de materiais, quantidade total, valor de estoque, etc.
```

### Categorias Disponíveis
- camisetas
- bolsas
- bone
- jaqueta
- calca
- shorts
- moleton
- gravata
- meia
- outro

### Unidades de Medida
- un (unidade)
- kg (quilograma)
- metro
- litro

---

## 3. CustoModel

**Arquivo:** `app/Models/CustoModel.php`

### Responsabilidades
- Gerenciar custos de produção e personalização
- Calcular custos com fórmulas customizadas
- Analisar estrutura de custos

### Métodos Principais

```php
validar(array $data): array
// Valida tipo, descrição, custo_base, formula

calcularCustoQuantidade(int $idCusto, float $quantidade): float
// Usa custo_base ou formula_calculo para calcular

analiseCustos(): array
// Retorna por tipo: count, total, média, mín, máx

getCustoTotalPorTipo(): array
// Retorna ['mao_de_obra' => float, 'material' => float, ...]

listarMaisCaros(int $limite): array
// Top N custos mais altos
```

### Tipos de Custo
- `mao_de_obra`: Custo de trabalho
- `material`: Custo de material
- `overhead`: Custos gerais (aluguel, energia, etc)
- `lucro`: Markup de lucro

### Fórmulas Customizadas
Exemplo: `"x * base + 50"` (x = quantidade, base = custo_base)

---

## 4. SimulacaoModel

**Arquivo:** `app/Models/SimulacaoModel.php`

### Responsabilidades
- Simular preços e margens de lucro
- Comparar diferentes simulações
- Análise de rentabilidade

### Métodos Principais

```php
criarSimulacao(
    int $clienteId,
    string $nome,
    int $quantidade,
    float $custoUnitario,
    float $margemLucro
): ?int
// Cria simulação e calcula preços automaticamente

listarPorCliente(int $clienteId): array
// Todas as simulações de um cliente

compararSimulacoes(array $idsSimulacoes): array
// Comparação lado a lado

analisar(): array
// Estatísticas: média de preços, margens, rentabilidade

listarMaisRentaveis(int $limite): array
// Simulações com maior diferença (preço - custo)
```

---

## 5. ProdutoModel

**Arquivo:** `app/Models/ProdutoModel.php`

### Responsabilidades
- Gerenciar produtos (material + configurações)
- Calcular preços finais com personalizações
- Análise de vendas

### Métodos Principais

```php
validar(array $data): array
// Valida nome, descrição, categoria, material, preço

listarPorCategoria(string $categoria): array
// Produtos de uma categoria

listarMaisVendidos(int $limite): array
// Top N produtos por quantidade vendida

calcularPrecoComPersonalizacoes(
    int $idProduto,
    array $personalizacoes
): float
// Preço final com custos adicionais

obterEstatisticas(): array
// Preço médio, mínimo, máximo, margem média
```

---

## 6. OrcamentoModel

**Arquivo:** `app/Models/OrcamentoModel.php`

### Responsabilidades
- Gerenciar orçamentos de clientes
- Geração de números sequenciais
- Cálculos de descontos e taxas
- Análise de orçamentos

### Métodos Principais

```php
criarOrcamento(int $clienteId, int $diasValidade = 7): ?int
// Cria novo orçamento com número único (ORC-2026-0001)

calcularTotal(
    float $subtotal,
    float $descontoPercent = 0,
    float $taxaAdicional = 0
): array
// Retorna ['desconto_valor' => float, 'total' => float]

listarPorCliente(int $clienteId): array
// Orçamentos de um cliente

listarPorStatus(string $status): array
// Status: rascunho|enviado|aprovado|pedido

listarVencidos(): array
// Orçamentos cuja data_validade passou

obterEstatisticas(): array
// Conta por status, valor total, valor médio
```

---

## 7. PedidoModel

**Arquivo:** `app/Models/PedidoModel.php`

### Responsabilidades
- Gerenciar pedidos (conversão de orçamentos)
- Geração de números sequenciais (PED-2026-0001)
- Monitoramento de prazos de entrega
- Análise de vendas

### Métodos Principais

```php
criarDePedido(int $orcamentoId, string $dataEntrega): ?int
// Cria pedido a partir de orçamento existente

listarPorCliente(int $clienteId): array
// Pedidos de um cliente

listarPorStatus(string $status): array
// Status: pendente|em_producao|entregue|cancelado

listarAtrasados(): array
// Pedidos com data_entrega_prevista no passado

listarParaEntregarProximos(int $dias = 7): array
// Pedidos para próximos N dias

analisarVendasPeriodo(string $dataInicio, string $dataFim): array
// Valor total, quantidade, clientes únicos, etc.

obterEstatisticas(): array
// Contagem por status, valor total/médio
```

---

## 8. ViaCEPModel

**Arquivo:** `app/Models/ViaCEPModel.php`

### Responsabilidades
- Integrar com API ViaCEP
- Cachear CEPs localmente
- Validação e formatação de CEPs

### Métodos Principais

```php
buscarEndereçoPorCEP(string $cep): ?array
// 1. Busca no cache local
// 2. Se não encontrar, consulta API ViaCEP
// 3. Salva no cache para futuras buscas

validarCEP(string $cep): bool
// Retorna true se CEP tem 8 dígitos numéricos

formatarCEP(string $cep): string
// Transforma 12345678 em 12345-678

buscarMultiplosCEPs(array $ceps): array
// Busca vários CEPs em loop

limparCacheAntigo(): bool
// Remove registros com mais de 30 dias

obterEstatisticasCache(): array
// Total de CEPs em cache, última busca, etc.
```

---

## Relacionamentos Entre Models

```
Cliente (1) ──────────> (N) Orçamento
                        │
                        └──────> (N) Pedido

Cliente (1) ──────────> (N) Simulação

Produto (N) ──────────> Material (1)

Custo (1) ──────────> (N) Produto (via tabela de junção)
                      (N) ItemOrcamento
                      (N) ItemPedido
```

---

## Padrões de Código

### Validação
Todo model implementa `validar()` que retorna:
```php
[
    'valid' => bool,
    'errors' => ['campo' => 'mensagem erro']
]
```

### Manipulação de Datas
- Datas salvas em format `Y-m-d H:i:s`
- Usa `date()` para timestamp atual

### Tratamento de Erros
- Todos os métodos usam try/catch
- Registram erros via `$this->registrarErro()`
- Retornam null/false em caso de erro

### Queries Parametrizadas
Sempre usam prepared statements com placeholders `:nomeParametro`

---

## Próximas Etapas

✅ Models implementados
⏳ Controllers (Etapa 3 continuação)
⏳ Views (Etapa 3 continuação)
⏳ Integração Frontend ViaCEP
⏳ Testes automatizados

---

**Total**: 8 Models | ~2000 linhas de código | Pronto para integração com Controllers
