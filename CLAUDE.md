# CLAUDE.md

Este arquivo fornece orientações para o Claude Code (claude.ai/code) ao trabalhar com código neste repositório.

## Visão Geral da Arquitetura

Esta é uma aplicação full-stack com backend **Laravel 5.4** e frontend **Angular 17**, containerizada com Docker:

- **Backend**: Laravel 5.4 (legado) + PHP 7.4 + Apache + MariaDB 10.5
- **Frontend**: Angular 17 + Bootstrap 5 + TypeScript
- **Containerização**: Docker Compose orquestrando todos os serviços

### Notas Arquiteturais Importantes

- **Framework Legado**: Usa Laravel 5.4 (Janeiro 2017) que requer tratamento especial de compatibilidade
- **Autenticação Simples**: Autenticação por token sem Laravel Passport (devido às limitações da versão)
- **Sistema Médico**: Gerencia entidades (hospitais/clínicas), especialidades médicas e usuários
- **Design API-First**: API RESTful com operações CRUD completas e endpoints de dashboard

## Comandos de Desenvolvimento

### Ambiente Docker
```bash
# Iniciar todos os serviços
docker-compose up --build

# Ver logs dos containers
docker-compose logs backend
docker-compose logs frontend
docker-compose logs db

# Entrar nos containers
docker exec -it backend_app bash
docker exec -it frontend_app bash
```

### Backend (Laravel 5.4)
```bash
# Executar migrações
docker exec -it backend_app php artisan migrate

# Popular banco de dados
docker exec -it backend_app php artisan db:seed

# Listar rotas (comando personalizado para compatibilidade)
docker exec -it backend_app php artisan route:list-custom
docker exec -it backend_app php artisan route:list-custom --path=api

# Limpar caches
docker exec -it backend_app php artisan route:clear
docker exec -it backend_app php artisan config:clear
docker exec -it backend_app php artisan cache:clear

# Executar testes
docker exec -it backend_app ./vendor/bin/phpunit
```

### Frontend (Angular 17)
```bash
# Servidor de desenvolvimento
docker exec -it frontend_app npm start

# Build para produção
docker exec -it frontend_app npm run build

# Executar testes
docker exec -it frontend_app npm run test

# Modo watch
docker exec -it frontend_app npm run watch
```

## Diretórios Principais

### Estrutura Backend
- `app/Http/Controllers/API/` - Controllers da API (AuthController, etc.)
- `app/Http/Controllers/` - Controllers web para operações CRUD
- `app/Console/Commands/` - Comandos Artisan personalizados (CustomRouteList)
- `routes/api.php` - Todos os endpoints da API com documentação abrangente
- `database/migrations/` - Definições do schema do banco de dados
- `database/seeds/` - Seeders de dados para entidades e especialidades

### Estrutura Frontend
- `src/app/` - Componentes da aplicação Angular
- `angular.json` - Configuração do Angular CLI com integração Bootstrap

## Endpoints da API

A API está extensivamente documentada em `backend/routes/api.php`. Endpoints principais:

**Rotas Públicas:**
- `GET /api/test` - Verificação de saúde da API
- `POST /api/register` - Registro de usuário
- `POST /api/login` - Autenticação de usuário

**Rotas Protegidas (requerem token Bearer):**
- `GET /api/me` - Informações do usuário atual
- `POST /api/logout` - Revogação de token
- Rotas de recurso para entidades, especialidades e usuários
- Endpoints de estatísticas do dashboard

## Notas de Compatibilidade Laravel 5.4

Devido à versão legada do Laravel:
- Use `Carbon::now()` ao invés do helper `now()`
- Comando personalizado de listagem de rotas em `app/Console/Commands/CustomRouteList.php`
- Autenticação por token simples sem Passport
- Algumas funcionalidades modernas do Laravel não estão disponíveis

## Configuração do Banco de Dados

- **Banco**: MariaDB 10.5
- **Conexão**: localhost:3306 nos containers
- **Credenciais**: laravel/secret (ver docker-compose.yml)

## Fluxo de Desenvolvimento

1. Use Docker para todo o desenvolvimento (não precisa de PHP/Node local)
2. Mudanças no backend refletem imediatamente devido ao volume mounting
3. Frontend usa servidor dev do Angular CLI com hot reload
4. Dados do banco persistem no volume nomeado `dbdata` do Docker

## Testes

- Backend: Testes PHPUnit no diretório `tests/`
- Frontend: Testes Jasmine/Karma (setup padrão do Angular)
- Coleção Postman disponível em `backend/Postman_collection.json`

## URLs dos Serviços

- Frontend: http://localhost:4200
- Backend API: http://localhost:8000
- Banco de dados: localhost:3306