
# 📄 Sistema de Cadastro de Licitações - API REST

Este projeto é um sistema simples para **cadastrar**, **listar**, **editar**, **buscar** e **remover** licitações públicas, desenvolvido com **Symfony**, **PHP**, **Doctrine**, **Docker** e **PostgreSQL**.

---

## 🚀 Tecnologias Utilizadas

- Symfony 6+
- PHP 8.2+
- Doctrine ORM
- PostgreSQL
- PHPUnit
- Intelephense (IDE Support)
- Postman (para testes de requisições)

---

## 📚 Funcionalidades

- Cadastro de licitações
- Listagem de todas as licitações
- Buscar licitação por ID
- Atualizar licitação (parcial com PATCH)
- Remover licitação

---

## 📦 Instalação do Projeto

```bash
git clone https://github.com/isaac-silv/sistema-de-cadastro-de-licitacoes
cd sistema-de-cadastro-de-licitacoes

composer install

# Suba o banco com Docker

docker run --name pg-licitacoes \
  -e POSTGRES_USER=admin \
  -e POSTGRES_PASSWORD=admin \
  -e POSTGRES_DB=licitacoes \
  -p 5432:5432 \
  -d postgres:15
 
#  Configure as variáveis de ambiente
# Copie o arquivo .env.exemple e env.dev.exemple
# edite o .env com a seguinte linha para usar o banco do Docker
DATABASE_URL="pgsql://admin:admin@127.0.0.1:5432/licitacoes?serverVersion=15&charset=utf8"

# Configure o .env com seu banco de dados PostgreSQL
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate

# Inicie o serve
symfone serve
```

## 📨 Requisições (Endpoints)

> ⚠️ Todas as rotas da API estão no formato JSON e prefixadas com `/api`.

### 🔹 Listar todas as licitações

**GET** `/api/licitacoes`

Retorna uma lista de licitações registradas.

---

### 🔹 Buscar licitação por ID

**GET** `/api/licitacoes/{id}`

Retorna os dados de uma licitação específica.

---

### 🔹 Criar nova licitação

**POST** `/api/licitacoes`

#### Exemplo de JSON:

```json
{
  "titulo": "Construção de Escola Municipal",
  "orgaoResponsavel": "Secretaria de Educação",
  "numeroEdital": "SME-2025/019",
  "dataPublicacao": "2025-08-01T09:00:00-03:00",
  "valorEstimado": 500000.00
}
```

---

### 🔹 Atualizar uma licitação

**PATCH** `/api/licitacoes/{id}`

Atualiza parcialmente uma licitação existente.

#### Exemplo de JSON:

```json
{
  "titulo": "Reforma de Escola",
  "valorEstimado": 600000.00
}
```

---

### 🔹 Remover uma licitação

**DELETE** `/api/licitacoes/{id}`

Remove uma licitação do sistema.

---

## 📁 Estrutura do Projeto

```
src/
├── Controller/
│   └── LicitacoesController.php
├── Entity/
│   └── Licitacao.php
├── Dto/
│   ├── CreateLicitacaoDto.php
│   └── UpdateLicitacaoDto.php
├── Repository/
│   └── LicitacaoRepository.php
├── Service/
│   └── LicitacaoService.php
tests/
└── Controller/
    └── LicitacoesControllerTest.php
```

---

## ✅ Validações

- Todos os campos são validados pelo componente **Symfony Validator**.
- Campos como `dataPublicacao` devem estar no formato ISO 8601 (`YYYY-MM-DDTHH:MM:SS±HH:MM`).
- O campo `numeroEdital` deve ser único.

---

## 🔍 Testes

Execute os testes com:

```bash
php bin/phpunit
```

---

## 📦 Postman

Um arquivo `.json` do Postman foi incluído neste projeto para facilitar os testes dos endpoints.

1. Abra o Postman
2. Vá em **Import**
3. Selecione o arquivo `licitacoes_api.postman_collection.json`
4. Teste os endpoints diretamente

---

## 🧑‍💻 Autor

Desenvolvido por Isaac - 2025

---
