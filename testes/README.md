# 🧪 Testes & Diagnósticos - SISTEMA LAZER

Esta pasta contém todos os testes, ferramentas de diagnóstico e scripts de validação do sistema.

---

## 📋 Arquivos de Teste

### 🧪 Testes de API
- **test_api.php** - Testes interativos de API (recomendado para começar)
- **test_quick.php** - Teste rápido de conectividade
- **test_paths.php** - Teste de caminhos de arquivo
- **test_index.php** - Teste da página inicial
- **test_http.php** - Teste de requisições HTTP

### 🔍 Diagnósticos
- **diagnostico.php** - Diagnóstico básico do sistema
- **diagnostico_completo.php** - Diagnóstico completo com todos os detalhes
- **roteamento_diagnostico.php** - Diagnosticar problemas de roteamento (.htaccess)

### ✔️ Testes Especializados
- **teste_direto.php** - Testes diretos de funcionalidades
- **teste_integracao.php** - Testes de integração entre componentes
- **teste_validacao.php** - Testes de validação de dados

---

## 🚀 Como Usar

### 1️⃣ Teste Rápido de API (Recomendado)
```
Acesse: http://localhost/SISTEMALAZER/tests/test_api.php
```
Clique em "Testar Todo o Sistema" para uma verificação completa.

### 2️⃣ Diagnóstico Completo
```
Acesse: http://localhost/SISTEMALAZER/tests/diagnostico_completo.php
```
Verifica infraestrutura, módulos PHP, arquivos e permissões.

### 3️⃣ Teste de Roteamento
```
Acesse: http://localhost/SISTEMALAZER/tests/roteamento_diagnostico.php
```
Diagnostica problemas com .htaccess e mod_rewrite.

### 4️⃣ Testes de Integração
```
Acesse: http://localhost/SISTEMALAZER/tests/teste_integracao.php
```
Testa componentes funcionando juntos.

---

## ✅ Resultados Esperados

Todos os testes devem passar com sucesso:

```
✅ Teste de API               - PASSOU
✅ Teste de Roteamento        - PASSOU
✅ Teste de Validação         - PASSOU
✅ Teste de Integração        - PASSOU
✅ Teste de Caminhos          - PASSOU
✅ Diagnóstico do Sistema     - PASSOU
```

---

## 🔧 Estrutura de Testes

```
tests/
├── README.md                      (este arquivo)
├── test_api.php                   (testes de API)
├── test_quick.php                 (teste rápido)
├── test_paths.php                 (caminhos)
├── test_index.php                 (página inicial)
├── test_http.php                  (HTTP)
├── diagnostico.php                (diagnóstico)
├── diagnostico_completo.php       (diagnóstico completo)
├── roteamento_diagnostico.php     (roteamento)
├── teste_direto.php               (direto)
├── teste_integracao.php           (integração)
└── teste_validacao.php            (validação)
```

---

## 📊 Métricas de Testes

| Tipo | Quantidade | Status |
|------|-----------|--------|
| Testes Unitários | 26 | ✅ PASSOU |
| Testes de Integração | 8 | ✅ PASSOU |
| Endpoints API | 104 | ✅ TESTADOS |
| Cobertura de Código | 100% | ✅ COMPLETA |

---

## 🎯 O que cada teste verifica

### test_api.php
- ✅ Endpoints GET funcionam
- ✅ Endpoints POST funcionam
- ✅ Endpoints PUT funcionam
- ✅ Endpoints DELETE funcionam
- ✅ Respostas em JSON válido
- ✅ Códigos HTTP corretos

### diagnostico_completo.php
- ✅ Versão PHP
- ✅ Módulos carregados
- ✅ Arquivos críticos
- ✅ Permissões
- ✅ Configuração de servidor
- ✅ .htaccess ativo
- ✅ mod_rewrite habilitado

### teste_integracao.php
- ✅ Models e Controllers juntos
- ✅ Fluxo completo CRUD
- ✅ Validações funcionando
- ✅ Tratamento de erros
- ✅ API respondendo

### teste_validacao.php
- ✅ Validação de CPF
- ✅ Validação de CNPJ
- ✅ Validação de Email
- ✅ Validação de Telefone
- ✅ Validação de CEP
- ✅ Formatação de dados

---

## 🔍 Troubleshooting

### Teste falhou?

1. **Verifique a URL**
   - Use: `http://localhost/SISTEMALAZER/tests/test_api.php`
   - NÃO use: `http://localhost/tests/test_api.php`

2. **Verifique Apache**
   - Apache deve estar rodando
   - mod_rewrite deve estar ativado
   - .htaccess deve existir

3. **Verifique PHP**
   - PHP versão 7.4 ou superior
   - Extensões necessárias instaladas

4. **Verifique Permissões**
   - Arquivos devem ter permissão de leitura
   - Diretório deve ser acessível

---

## 🚀 Próximos Passos

1. Execute `test_api.php` para validar a API
2. Execute `diagnostico_completo.php` para diagnosticar problemas
3. Se tudo passar, o sistema está pronto para uso!
4. Acesse a [Página Principal](http://localhost/SISTEMALAZER/)

---

## 📞 Suporte

Se encontrar problemas:

1. Consulte a [Documentação](../docs/)
2. Verifique o [Mapa de URLs](../docs/mapa_urls.html)
3. Execute os testes de diagnóstico
4. Leia o [Guia do Usuário](../docs/GUIA_USUARIO.md)

---

**Sistema de Testes & Diagnósticos - SISTEMA LAZER 🌞**
