# estudosPhalcon

Projeto de estudos com **PHP 8.4**, **Phalcon 5.17.0** e **PostgreSQL**.

Contém um CRUD simples de **Pessoa Física**, migrations oficiais do Phalcon e testes com Pest PHP.

---

## Requisitos

- Docker
- Docker Compose

---

## Subir o ambiente

```bash
docker compose up -d
```

A aplicação estará disponível em:

```
http://localhost:8000
```

---

## Configuração de ambiente

Copie o arquivo de exemplo:

```bash
cp .env.example .env
```

Ajuste as variáveis em `.env` conforme necessário. O padrão já aponta para o container PostgreSQL configurado no `docker-compose.yml`.

---

## Comandos do Composer

Entrar no container workspace:

```bash
docker exec -it php858_workspace_phalcon bash
```

Instalar dependências:

```bash
composer install
```

Atualizar dependências:

```bash
composer update
```

---

## Migrations oficiais do Phalcon

As migrations ficam em `app/migrations/`.

Rodar migrations:

```bash
docker exec php858_workspace_phalcon php -d error_reporting=0 /var/www/vendor/bin/phalcon-migrations run
```

Rodar migration para uma versão específica:

```bash
docker exec php858_workspace_phalcon php -d error_reporting=0 /var/www/vendor/bin/phalcon-migrations run --version=1.0.0
```

Gerar nova migration a partir do banco de dados:

```bash
docker exec php858_workspace_phalcon php -d error_reporting=0 /var/www/vendor/bin/phalcon-migrations generate
```

> O `error_reporting=0` é usado apenas para suprimir warnings de depreciação do Phalcon Migrations no PHP 8.4.

---

## Testes com Pest PHP

Rodar todos os testes:

```bash
docker exec php858_workspace_phalcon /var/www/vendor/bin/pest
```

Rodar apenas testes unitários:

```bash
docker exec php858_workspace_phalcon /var/www/vendor/bin/pest --filter=Unit
```

Rodar apenas testes de feature:

```bash
docker exec php858_workspace_phalcon /var/www/vendor/bin/pest --filter=Feature
```

---

## Comandos úteis do Phalcon DevTools

O Phalcon DevTools está instalado em `vendor/bin/phalcon`.

Listar comandos disponíveis:

```bash
docker exec php858_workspace_phalcon /var/www/vendor/bin/phalcon
```

Criar um novo controller:

```bash
docker exec php858_workspace_phalcon /var/www/vendor/bin/phalcon controller nome-do-controller
```

Criar um novo model:

```bash
docker exec php858_workspace_phalcon /var/www/vendor/bin/phalcon model NomeDoModel
```

---

## Banco de dados

Acessar o PostgreSQL:

```bash
docker exec -it postgres_phalcon psql -U phalcon_user -d phalcon_db
```

Resetar a tabela de pessoas físicas:

```bash
docker exec -i postgres_phalcon psql -U phalcon_user -d phalcon_db -c 'TRUNCATE pessoa_fisica RESTART IDENTITY;'
```

---

## Estrutura do projeto

```
app/
  config/           # Configurações (services, router, loader, config)
  controllers/      # Controllers da aplicação
  models/           # Models do Phalcon
  views/            # Views .phtml
  migrations/       # Migrations oficiais do Phalcon
tests/
  Unit/             # Testes unitários
  Feature/          # Testes de integração via HTTP
public/             # Document root
.env                # Variáveis de ambiente (não versionado)
.env.example        # Template de variáveis de ambiente
.env.testing        # Variáveis para testes
composer.json       # Dependências PHP
phpunit.xml         # Configuração do Pest/PHPUnit
```

---

## Observações

- O servidor embutido do PHP é iniciado automaticamente pelo comando configurado no `docker-compose.yml`.
- O banco de dados persiste os dados no diretório `pgdata`.
- Os testes de feature acessam a aplicação em `http://localhost:8000`, portanto o container deve estar rodando.
