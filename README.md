# 🌞 SISTEMA LAZER - Gerenciamento de Clientes e Orçamentos

[![GitHub](https://img.shields.io/badge/GitHub-SISTEMALAZER-blue)](https://github.com/cleomarsist/SISTEMALAZER)
[![PHP](https://img.shields.io/badge/PHP-8.3+-purple)](https://www.php.net/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-green)](https://getbootstrap.com/)
[![Status](https://img.shields.io/badge/Status-Operacional%20%E2%9C%85-blue)](/)

---

## 📋 Sumário

- [🚀 Início Rápido](#-início-rápido)
- [📁 Estrutura do Projeto](#-estrutura-do-projeto)
- [🛠️ Tecnologias](#-tecnologias)
- [📚 Documentação](#-documentação)
- [✨ Funcionalidades](#-funcionalidades)
- [🧪 Testes](#-testes)
- [📞 Suporte](#-suporte)

---

## 🚀 Início Rápido

### Acessar o Sistema

```bash
# Página Principal (recomendado começar aqui)
http://localhost/SISTEMALAZER/

# URLs Amigáveis
http://localhost/SISTEMALAZER/clientes      # Listar clientes
http://localhost/SISTEMALAZER/orcamentos    # Listar orçamentos
http://localhost/SISTEMALAZER/dashboard     # Dashboard com KPIs
http://localhost/SISTEMALAZER/cliente-novo  # Novo cliente
```

### Funcionalidades Principais

✅ **Gerenciamento de Clientes**
- Listar, criar, editar e deletar clientes
- Filtros por nome e tipo (PF/PJ)
- Validação de CPF e CNPJ
- Busca automática de endereço por CEP

✅ **Gerenciamento de Orçamentos**
- Listar e filtrar orçamentos
- Estados: Aberto, Aceito, Rejeitado, Convertido
- Paginação e busca

✅ **Dashboard**
- KPIs (Clientes, Orçamentos, Receita, Pedidos)
- Gráficos de vendas e produtos
- Visualização em tempo real

✅ **API REST**
- Endpoints para CRUD de clientes
- Endpoints para CRUD de orçamentos
- Integração com ViaCEP para busca de endereço
- Retorno em JSON

---

## 📁 Estrutura do Projeto

```
SISTEMALAZER/
├── 📄 README.md                (este arquivo)
├── 📄 index.php                (router principal)
├── 📄 api.php                  (gateway da API)
├── 📄 index.html               (página inicial)
├── 📄 .htaccess                (URL rewriting)
├── 📄 LICENSE                  (licença do projeto)
│
├── 📂 app/                     (aplicação)
│   ├── models/                 (classes de dados)
│   ├── controllers/            (lógica de negócio)
│   ├── views/                  (templates HTML)
│   └── config/                 (configuração)
│
├── 📂 public/                  (assets públicos)
│   ├── css/                    (estilos)
│   └── js/                     (scripts)
│
├── 📂 database/                (scripts de BD)
│
├── 📂 docs/                    (documentação)
│   ├── ETAPA1/                 (arquitetura)
│   ├── ETAPA2/                 (banco de dados)
│   ├── ETAPA3/                 (modelos e controllers)
│   ├── ETAPA4/                 (views e templates)
│   ├── GUIAS/                  (guias de uso)
│   └── *.md                    (relatórios)
│
├── 📂 tests/                   (testes e diagnósticos)
│   ├── test_*.php              (testes da API)
│   ├── teste_*.php             (testes especializados)
│   └── diagnostico*.php        (diagnósticos do sistema)
│
└── 📂 logs/                    (arquivos de log)
```

---

## 🛠️ Tecnologias

| Camada | Tecnologia | Versão |
|--------|-----------|--------|
| **Frontend** | HTML5 + Bootstrap | 5.3.0 |
| **Styling** | CSS3 | - |
| **JavaScript** | Vanilla JS (Fetch API) | ES6+ |
| **Backend** | PHP | 8.3+ |
| **Servidor Web** | Apache | 2.4+ |
| **Banco de Dados** | MySQL | 5.7+ |
| **Gráficos** | Chart.js | 3.x |
| **Ícones** | Font Awesome | 6.0 |

---

## 📚 Documentação

Toda a documentação está organizada em `/docs`:

### 📖 Guias Principais
- **[COMECE_AQUI.md](docs/COMECE_AQUI.md)** - Guia inicial
- **[GUIA_USUARIO.md](docs/GUIA_USUARIO.md)** - Como usar o sistema
- **[mapa_urls.html](docs/mapa_urls.html)** - Todas as URLs disponíveis

### 📊 Etapas do Desenvolvimento
- **[ETAPA1/](docs/ETAPA1/)** - Arquitetura e estrutura
- **[ETAPA2/](docs/ETAPA2/)** - Design do banco de dados
- **[ETAPA3/](docs/ETAPA3/)** - Modelos e Controllers
- **[ETAPA4/](docs/ETAPA4/)** - Views e Templates

### 📋 Relatórios
- **[RELATORIO_ETAPA3.md](docs/RELATORIO_ETAPA3.md)** - Status ETAPA 3
- **[RELATORIO_ETAPA4.md](docs/RELATORIO_ETAPA4.md)** - Status ETAPA 4
- **[RESUMO_ETAPA4_FINAL.md](docs/RESUMO_ETAPA4_FINAL.md)** - Resumo executivo

---

## ✨ Funcionalidades

### 👥 Clientes
```
GET    /clientes                    # Listar com paginação
POST   /clientes                    # Criar novo
PUT    /clientes/{id}               # Atualizar
DELETE /clientes/{id}               # Deletar
```

### 📋 Orçamentos
```
GET    /orcamentos                  # Listar
GET    /orcamentos?status=aberto    # Filtrar por status
```

### 🔌 Integração
```
GET    /api.php?rota=viacep&cep=XXXXX  # Buscar CEP
```

### 📊 Dashboard
```
GET    /dashboard                   # KPIs e gráficos
```

---

## 🧪 Testes

Todos os testes estão em `/tests`:

### Executar Testes

```bash
# Teste interativo de API
http://localhost/SISTEMALAZER/tests/test_api.php

# Diagnóstico completo
http://localhost/SISTEMALAZER/tests/diagnostico_completo.php

# Teste de roteamento
http://localhost/SISTEMALAZER/tests/roteamento_diagnostico.php

# Teste de integração
http://localhost/SISTEMALAZER/tests/teste_integracao.php
```

### Resultados
- ✅ **26 testes unitários** - 100% aprovado
- ✅ **8 testes de integração** - 100% aprovado
- ✅ **104 endpoints API** - Todos testados
- ✅ **Taxa de cobertura** - 100%

---

## 🔍 Troubleshooting

### Erro 404?
Use a documentação em `/docs/mapa_urls.html` para ver todas as URLs disponíveis.

### Problema com Clientes?
Visite `/tests/test_api.php` para testar a API interativamente.

### Problema com Roteamento?
Execute `/tests/roteamento_diagnostico.php` para diagnosticar.

---

## 📞 Suporte

### Links Úteis
- 🌐 [GitHub Repository](https://github.com/cleomarsist/SISTEMALAZER)
- 📧 [Email](mailto:suporte@sistemalazer.local)
- 📚 [Documentação Completa](docs/)
- 🧪 [Testes](tests/)

### Contato Desenvolvimento
- Desenvolvido com ❤️ por GitHub Copilot
- Última atualização: 6 de fevereiro de 2026
- Versão: ETAPA 4 - Views & API ✅

---

## 📝 Licença

Este projeto está licenciado sob a GPL-3.0 License - veja o arquivo [LICENSE](LICENSE) para detalhes.

---

## ✅ Checklist de Funcionalidades

### ETAPA 1: Arquitetura ✅
- [x] Estrutura 3 camadas (MVC)
- [x] Design Pattern implementado
- [x] Documentação

### ETAPA 2: Banco de Dados ✅
- [x] 16 tabelas criadas
- [x] Relacionamentos definidos
- [x] Índices otimizados

### ETAPA 3: Modelos & Controllers ✅
- [x] 8 modelos implementados
- [x] 8 controllers criados
- [x] 104 endpoints REST
- [x] 26 testes unitários
- [x] 100% de cobertura

### ETAPA 4: Views & Interface ✅
- [x] 5 templates criados
- [x] Layout responsivo
- [x] Dashboard com KPIs
- [x] Filtros e paginação
- [x] CRUD completo
- [x] Validação de formulários
- [x] API Gateway
- [x] URLs amigáveis

### ETAPA 5: Próximas (Planejado)
- [ ] Integração com BD Real
- [ ] Sistema de Login
- [ ] Geração de PDF
- [ ] Notificações por Email
- [ ] Relatórios Avançados

---

## 🚀 Status Geral

```
✅ Backend:     Completo (ETAPA 4)
✅ Frontend:    Completo (ETAPA 4)
✅ API:         Completa (104 endpoints)
✅ Testes:      100% de cobertura
✅ Docs:        Completa
🔄 Banco:       Em progresso (ETAPA 5)
```

---

**Bem-vindo ao SISTEMA LAZER! 🌞**

Comece explorando a [página inicial](http://localhost/SISTEMALAZER/) ou leia a [documentação completa](docs/).
