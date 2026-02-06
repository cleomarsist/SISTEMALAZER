# ETAPA 3 - Controllers (Lógica de Negócio)

## Visão Geral

8 Controllers implementam a lógica de negócio, validações e respostas HTTP para cada entidade do sistema.

---

## 1. ClienteController

**Arquivo:** `app/Controllers/ClienteController.php`

### Endpoints

#### Listar Clientes
```
GET /clientes
GET /clientes?tipo=PF|PJ
GET /clientes?pagina=1&limite=10

Resposta:
{
    "sucesso": true,
    "dados": {
        "clientes": [...],
        "total": 25,
        "pagina": 1,
        "limite": 10,
        "stats": {
            "PF": 15,
            "PJ": 10
        }
    }
}
```

#### Obter Cliente
```
GET /clientes/{id}

Resposta 200:
{
    "sucesso": true,
    "dados": {
        "id_cliente": 1,
        "tipo_cliente": "PF",
        "nome_razao_social": "João Silva",
        ...
    }
}

Resposta 404:
{
    "sucesso": false,
    "erro": "Cliente não encontrado"
}
```

#### Criar Cliente
```
POST /clientes
Content-Type: application/json

{
    "tipo_cliente": "PF",
    "nome_razao_social": "João Silva",
    "cpf_cnpj": "123.456.789-10",
    "email": "joao@email.com",
    "telefone": "(11) 98765-4321",
    "cep": "01310-100",
    "endereco": "Avenida Paulista",
    "numero": "1000",
    "complemento": "Apto 500",
    "bairro": "Bela Vista",
    "cidade": "São Paulo",
    "estado": "SP"
}

Resposta 201:
{
    "sucesso": true,
    "mensagem": "Cliente criado com sucesso",
    "id": 5
}

Resposta 400:
{
    "sucesso": false,
    "erros": {
        "email": "Email inválido",
        "cpf_cnpj": "CPF inválido"
    }
}

Resposta 409:
{
    "sucesso": false,
    "erro": "CPF/CNPJ já cadastrado"
}
```

#### Atualizar Cliente
```
PUT /clientes/{id}
Content-Type: application/json

{
    "email": "novo@email.com",
    "telefone": "(11) 99999-9999"
}

Resposta 200:
{
    "sucesso": true,
    "mensagem": "Cliente atualizado com sucesso"
}
```

#### Deletar Cliente
```
DELETE /clientes/{id}

Resposta 200:
{
    "sucesso": true,
    "mensagem": "Cliente deletado com sucesso"
}
```

#### Buscar por CPF/CNPJ
```
GET /clientes/buscar/cpf-cnpj?valor=123.456.789-10

Resposta 200: [cliente]
Resposta 404: Cliente não encontrado
```

#### Buscar por Localização
```
GET /clientes/localizacao?cidade=São Paulo&estado=SP

Resposta:
{
    "sucesso": true,
    "dados": [...],
    "total": 5
}
```

#### Estatísticas
```
GET /clientes/stats

Resposta:
{
    "sucesso": true,
    "dados": {
        "total": 25,
        "por_tipo": {
            "PF": 15,
            "PJ": 10
        }
    }
}
```

---

## 2. MaterialController

**Arquivo:** `app/Controllers/MaterialController.php`

### Endpoints

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/materiais` | Listar todos |
| GET | `/materiais?categoria=camisetas` | Filtrar por categoria |
| GET | `/materiais/{id}` | Obter um material |
| POST | `/materiais` | Criar novo |
| PUT | `/materiais/{id}` | Atualizar |
| DELETE | `/materiais/{id}` | Deletar |
| GET | `/materiais/estoque/baixo` | Estoque abaixo do mínimo |
| PATCH | `/materiais/{id}/estoque` | Atualizar estoque |
| GET | `/materiais/preco?min=10&max=100` | Faixa de preço |
| GET | `/materiais/stats` | Estatísticas |

### Exemplo: Criar Material
```
POST /materiais

{
    "nome_material": "Camiseta Branca Premium",
    "categoria": "camisetas",
    "descricao": "100% algodão",
    "custo_unitario": 15.00,
    "margem_lucro": 50,
    "unidade_medida": "un",
    "quantidade_disponivel": 500,
    "estoque_minimo": 50
}

Resposta: Preço de venda calculado como 22.50 (15 * 1.5)
```

### Exemplo: Atualizar Estoque
```
PATCH /materiais/1/estoque

{
    "quantidade": 100  // Adicionar 100 unidades
}

// Para remover, usar quantidade negativa
{
    "quantidade": -50  // Remover 50 unidades
}
```

---

## 3. CustoController

**Arquivo:** `app/Controllers/CustoController.php`

### Endpoints

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/custos` | Listar todos |
| GET | `/custos?tipo=mao_de_obra` | Filtrar por tipo |
| GET | `/custos/{id}` | Obter um custo |
| POST | `/custos` | Criar novo |
| PUT | `/custos/{id}` | Atualizar |
| DELETE | `/custos/{id}` | Deletar |
| GET | `/custos/calcular?id=1&quantidade=100` | Calcular custo |
| GET | `/custos/analisar` | Análise por tipo |
| GET | `/custos/maiscaros?limite=5` | Top 5 |

### Exemplo: Criar Custo com Fórmula
```
POST /custos

{
    "tipo_custo": "mao_de_obra",
    "descricao": "Bordado personalizado",
    "custo_base": 2.50,
    "formula_calculo": "x * base + 10",
    "unidade_referencia": "un"
}

// Com esta fórmula:
// 100 unidades = (100 * 2.50) + 10 = 260
```

---

## 4. SimulacaoController

**Arquivo:** `app/Controllers/SimulacaoController.php`

### Endpoints

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/simulacoes` | Listar todas |
| GET | `/simulacoes?cliente_id=1` | Por cliente |
| GET | `/simulacoes/{id}` | Obter uma |
| POST | `/simulacoes` | Criar nova |
| PUT | `/simulacoes/{id}` | Atualizar |
| DELETE | `/simulacoes/{id}` | Deletar |
| POST | `/simulacoes/comparar` | Comparar múltiplas |
| GET | `/simulacoes/analisar` | Análise geral |
| GET | `/simulacoes/rentaveis?limite=10` | Mais rentáveis |

### Exemplo: Criar Simulação
```
POST /simulacoes

{
    "cliente_id": 1,
    "nome_simulacao": "Camisetas Personalizadas - July 2026",
    "quantidade_simulada": 500,
    "preco_unitario_simulado": 35.00,
    "margem_lucro_simulada": 40
}

Resposta Automática:
- Custo total: calculado
- Preço total: calculado
- Lucro: calculado
```

---

## 5. ProdutoController

**Arquivo:** `app/Controllers/ProdutoController.php`

### Endpoints

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/produtos` | Listar todos |
| GET | `/produtos?categoria=camisetas` | Por categoria |
| GET | `/produtos/{id}` | Obter um |
| POST | `/produtos` | Criar novo |
| PUT | `/produtos/{id}` | Atualizar |
| DELETE | `/produtos/{id}` | Deletar |
| GET | `/produtos/vendidos?limite=10` | Mais vendidos |
| GET | `/produtos/preco?id=1&personalizacoes={}` | Calcular preço |
| GET | `/produtos/stats` | Estatísticas |

### Exemplo: Criar Produto
```
POST /produtos

{
    "nome_produto": "Camiseta Premium Personalizada",
    "descricao": "Camiseta 100% algodão com estampa",
    "categoria": "camisetas",
    "material_id": 5,
    "preco_base": 25.00,
    "margem_lucro": 60
}

Resposta: Preço final = 40.00
```

---

## 6. OrcamentoController

**Arquivo:** `app/Controllers/OrcamentoController.php`

### Endpoints

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/orcamentos` | Listar todos |
| GET | `/orcamentos?status=rascunho` | Por status |
| GET | `/orcamentos?cliente_id=1` | Por cliente |
| GET | `/orcamentos/{id}` | Obter um |
| POST | `/orcamentos` | Criar novo |
| PUT | `/orcamentos/{id}` | Atualizar |
| DELETE | `/orcamentos/{id}` | Deletar |
| GET | `/orcamentos/calcular?subtotal=1000&desconto=10&taxa=50` | Calcular total |
| GET | `/orcamentos/vencidos` | Vencidos |
| GET | `/orcamentos/stats` | Estatísticas |

### Número de Orçamento
Gerado automaticamente: `ORC-2026-0001`, `ORC-2026-0002`, etc.

### Statuses
- `rascunho`: Criado, não enviado
- `enviado`: Enviado ao cliente
- `aprovado`: Cliente aprovou
- `pedido`: Convertido em pedido
- `rejeitado`: Cliente rejeitou

### Exemplo: Atualizar Total
```
GET /orcamentos/calcular?subtotal=1000&desconto=10&taxa=50

Resposta:
{
    "sucesso": true,
    "dados": {
        "subtotal": 1000.00,
        "desconto_percentual": 10,
        "desconto_valor": 100.00,
        "taxa_adicional": 50.00,
        "total": 950.00
    }
}

// Cálculo: 1000 - 100 + 50 = 950
```

---

## 7. PedidoController

**Arquivo:** `app/Controllers/PedidoController.php`

### Endpoints

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/pedidos` | Listar todos |
| GET | `/pedidos?status=pendente` | Por status |
| GET | `/pedidos?cliente_id=1` | Por cliente |
| GET | `/pedidos/{id}` | Obter um |
| POST | `/pedidos` | Criar novo |
| PUT | `/pedidos/{id}` | Atualizar |
| DELETE | `/pedidos/{id}` | Deletar |
| GET | `/pedidos/atrasados` | Prazos vencidos |
| GET | `/pedidos/proximos?dias=7` | Próximos 7 dias |
| GET | `/pedidos/analise?inicio=2026-01-01&fim=2026-02-06` | Análise de período |
| GET | `/pedidos/stats` | Estatísticas |

### Número de Pedido
Gerado automaticamente: `PED-2026-0001`, `PED-2026-0002`, etc.

### Statuses
- `pendente`: Aguardando produção
- `em_producao`: Em produção
- `entregue`: Entregue ao cliente
- `cancelado`: Cancelado

### Exemplo: Criar Pedido
```
POST /pedidos

{
    "orcamento_id": 5,
    "data_entrega_prevista": "2026-02-28"
}

Resposta:
{
    "sucesso": true,
    "id": 10,
    "numero_pedido": "PED-2026-0010"
}

// Dados copiados do orçamento:
// - cliente_id
// - subtotal
// - desconto
// - total
```

---

## 8. ViaCEPController

**Arquivo:** `app/Controllers/ViaCEPController.php`

### Endpoints

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/viacep?cep=01310100` | Buscar endereço |
| POST | `/viacep/multiplos` | Buscar múltiplos |
| GET | `/viacep/validar?cep=01310100` | Validar formato |
| GET | `/viacep/formatar?cep=01310100` | Formatar (01310-100) |
| POST | `/viacep/limpar-cache` | Limpar cache |
| GET | `/viacep/stats` | Estatísticas cache |

### Exemplo: Buscar CEP
```
GET /viacep?cep=01310100

Resposta 200:
{
    "sucesso": true,
    "dados": {
        "cep": "01310-100",
        "endereco": "Avenida Paulista",
        "complemento": "",
        "bairro": "Bela Vista",
        "cidade": "São Paulo",
        "estado": "SP",
        "ibge": "3550308",
        "gia": "",
        "ddd": "11",
        "siafi": "7107"
    }
}

Resposta 404:
{
    "sucesso": false,
    "erro": "CEP não encontrado"
}
```

### Exemplo: Buscar Múltiplos
```
POST /viacep/multiplos

{
    "ceps": ["01310100", "20040020", "30140071"]
}

Resposta:
{
    "sucesso": true,
    "dados": {
        "01310100": { ... },
        "20040020": { ... },
        "30140071": { ... }
    }
}
```

### Cache Local
- Primeira busca: Consultada a API ViaCEP
- Buscas subsequentes: Retornadas do cache
- Duração: 30 dias
- Limpeza automática: Via endpoint `/viacep/limpar-cache`

---

## Padrões de Resposta HTTP

### Sucesso 200
```json
{
    "sucesso": true,
    "dados": {...}
}
```

### Criado 201
```json
{
    "sucesso": true,
    "mensagem": "Recurso criado com sucesso",
    "id": 123
}
```

### Erro 400 (Validação)
```json
{
    "sucesso": false,
    "erros": {
        "campo1": "Mensagem erro",
        "campo2": "Mensagem erro"
    }
}
```

### Erro 404 (Não encontrado)
```json
{
    "sucesso": false,
    "erro": "Recurso não encontrado"
}
```

### Erro 405 (Método não permitido)
```json
{
    "sucesso": false,
    "erro": "Método não permitido"
}
```

### Erro 409 (Conflito)
```json
{
    "sucesso": false,
    "erro": "Recurso já existe"
}
```

### Erro 500 (Servidor)
```json
{
    "sucesso": false,
    "erro": "Erro ao processar: ..."
}
```

---

## Autenticação

Todos os endpoints requerem autenticação de sessão. Verifique se o usuário está logado em `BaseController::__construct()`.

---

## Total

✅ 8 Controllers | 70+ endpoints | ~2500 linhas de código

---

**Próxima Etapa:** Views (Templates HTML para interface web)
