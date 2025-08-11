# Teste Full Stack - Ger Financeiro Soluções

Este projeto é uma implementação completa do desafio descrito em:  
**🔗 [https://github.com/gerfinanceirosolucoes/teste-full-stack](https://github.com/gerfinanceirosolucoes/teste-full-stack)**

---

## 🐋 **Ambiente Dockerizado**

Este projeto roda completamente em Docker, facilitando a configuração e execução em qualquer ambiente.

### **Tecnologias e Versões Utilizadas**

| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| **Docker** | 3.8 | Containerização do ambiente |
| **PHP** | 7.4 | Backend runtime com Apache |
| **Laravel** | 5.4 | Framework PHP (Janeiro 2017) |
| **MariaDB** | 10.5 | Banco de dados |
| **Angular** | Latest | Frontend framework |
| **Bootstrap** | Latest | Framework CSS |
| **Apache** | 2.4 | Servidor web integrado |

---

## ⚠️ **Observações Importantes**

### **Laravel 5.4 - Versão Legada**

Este projeto utiliza **Laravel 5.4** (lançado em Janeiro de 2017), uma versão com mais de 7 anos. Devido a esta escolha, algumas implementações especiais foram necessárias:

#### **Compatibilidades Implementadas:**
- ✅ **Autenticação por tokens simples** (sem Laravel Passport)
- ✅ **Middleware personalizado** para PHP 7.4+
- ✅ **Comando personalizado** para listar rotas (`route:list-custom`)
- ✅ **Correções de compatibilidade** para novas versões do PHP
- ✅ **Sistema de fallback** para funcionalidades não disponíveis

#### **Limitações da Versão:**
- ❌ Não possui helper `now()` (usamos `Carbon::now()`)
- ❌ Alguns middlewares modernos não existem
- ❌ Laravel Passport não está disponível nesta versão
- ❌ Sintaxes mais modernas do Eloquent podem não funcionar

---

## 🚀 **Como Executar o Projeto**

### **Pré-requisitos**
- Docker
- Docker Compose

### **Comandos para Execução**

```bash
# Clonar o repositório
git clone [seu-repositório]
cd teste-full-stack

# Executar com Docker Compose
docker-compose up --build

# Aguardar todos os serviços subirem
# Frontend: http://localhost:4200
# Backend API: http://localhost:8000
# Banco de dados: localhost:3306 (MariaDB)
```

### **Configuração Inicial**

```bash
# Entrar no container do backend
docker exec -it backend_app bash

# Instalar dependências do Composer (se necessário)
composer install

# Configurar permissões
chown -R www-data:www-data /var/www/html
chmod -R 775 storage bootstrap/cache

# Rodar migrações
php artisan migrate

# (Opcional) Criar dados de exemplo
php artisan db:seed
```

---

## 📚 **Documentação da API**

### **Base URL**
```
http://localhost:8000/api
```

### **Autenticação**
A API utiliza **Bearer Token**. Inclua o token no header das requisições protegidas:
```
Authorization: Bearer {seu_token}
```

### **Testando com Postman**
Uma collection completa do Postman está disponível no arquivo:
```
backend/Postman_collection.json
```

**Como usar:**
1. Abra o Postman
2. Clique em **Import** 
3. Selecione o arquivo `backend/Postman_collection.json`
4. Configure as variáveis de ambiente:
   - `base_url`: `http://localhost:8000`
   - `token`: (será preenchido automaticamente após login)

A collection inclui todos os endpoints documentados com exemplos de requisições e variáveis automáticas.

---

## 🔓 **Endpoints Públicos**

### **Teste e Status**

#### `GET /api/test`
Testa se a API está funcionando.

**Resposta:**
```json
{
  "message": "API funcionando!",
  "timestamp": "2025-01-15 10:30:00",
  "laravel_version": "5.4.36"
}
```

#### `GET /api/status`
Retorna status detalhado da API.

**Resposta:**
```json
{
  "api": "Sistema de Clínicas API",
  "version": "1.0.0",
  "status": "online",
  "timestamp": "2025-01-15 10:30:00",
  "laravel_version": "5.4.36"
}
```

### **Autenticação**

#### `POST /api/register`
Registra um novo usuário no sistema.

**Body:**
```json
{
  "nome": "João Silva",
  "email": "joao@exemplo.com",
  "password": "123456",
  "password_confirmation": "123456"
}
```

**Resposta de Sucesso:**
```json
{
  "success": true,
  "message": "Usuário registrado com sucesso",
  "data": {
    "user": {
      "id": 1,
      "nome": "João Silva",
      "email": "joao@exemplo.com",
      "ativo": true
    },
    "token": "hash_do_token_aqui",
    "token_type": "Bearer"
  }
}
```

#### `POST /api/login`
Realiza login do usuário.

**Body:**
```json
{
  "email": "joao@exemplo.com",
  "password": "123456"
}
```

**Resposta de Sucesso:**
```json
{
  "success": true,
  "message": "Login realizado com sucesso",
  "data": {
    "user": {
      "id": 1,
      "nome": "João Silva",
      "email": "joao@exemplo.com",
      "ativo": true
    },
    "token": "hash_do_token_aqui",
    "token_type": "Bearer"
  }
}
```

---

## 🔒 **Endpoints Protegidos**

*Requerem token de autenticação no header*

### **Gerenciamento de Usuário**

#### `GET /api/me`
Retorna dados do usuário autenticado.

#### `POST /api/logout`
Realiza logout (revoga o token atual).

#### `POST /api/refresh`
Renova o token de acesso.

#### `POST /api/change-password`
Altera a senha do usuário.

**Body:**
```json
{
  "current_password": "123456",
  "new_password": "654321",
  "new_password_confirmation": "654321"
}
```

### **CRUD de Recursos**

#### **Entidades** (`/api/entidades`)
- `GET /api/entidades` - Lista todas as entidades
- `POST /api/entidades` - Cria nova entidade
- `GET /api/entidades/{id}` - Mostra entidade específica
- `PUT /api/entidades/{id}` - Atualiza entidade
- `DELETE /api/entidades/{id}` - Remove entidade

#### **Especialidades** (`/api/especialidades`)
- `GET /api/especialidades` - Lista todas as especialidades
- `POST /api/especialidades` - Cria nova especialidade
- `GET /api/especialidades/{id}` - Mostra especialidade específica
- `PUT /api/especialidades/{id}` - Atualiza especialidade
- `DELETE /api/especialidades/{id}` - Remove especialidade

#### **Usuários** (`/api/users`)
- `GET /api/users` - Lista todos os usuários (admin)
- `POST /api/users` - Cria novo usuário (admin)
- `GET /api/users/{id}` - Mostra usuário específico (admin)
- `PUT /api/users/{id}` - Atualiza usuário (admin)
- `DELETE /api/users/{id}` - Remove usuário (admin)

### **Rotas Auxiliares**

#### `GET /api/entidades-regionais`
Lista entidades organizadas por região.

#### `GET /api/entidades-especialidades`
Lista especialidades por entidade.

### **Dashboard e Estatísticas**

#### `GET /api/dashboard/stats`
Retorna estatísticas gerais do sistema.

**Resposta:**
```json
{
  "success": true,
  "data": {
    "usuarios": {
      "total": 150,
      "ativos": 140,
      "inativos": 10,
      "novos_ultimo_mes": 25
    },
    "entidades": {
      "total": 45
    },
    "especialidades": {
      "total": 20
    },
    "sistema": {
      "versao": "1.0.0",
      "uptime": "15 dias, 8 horas, 30 minutos",
      "ultima_atualizacao": "2025-01-15 10:30:00"
    }
  }
}
```

#### `GET /api/dashboard/entidades-por-regional`
Retorna entidades agrupadas por região do Brasil.

**Resposta:**
```json
{
  "success": true,
  "data": [
    {"regiao": "Sudeste", "total": 35},
    {"regiao": "Sul", "total": 20},
    {"regiao": "Nordeste", "total": 28},
    {"regiao": "Norte", "total": 12},
    {"regiao": "Centro-Oeste", "total": 15}
  ],
  "total": 110
}
```

#### `GET /api/dashboard/especialidades-populares?limit=5`
Retorna as especialidades mais populares.

**Parâmetros de Query:**
- `limit` (opcional): Número de especialidades a retornar (padrão: 10)

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "Cardiologia",
      "total_uso": 150,
      "percentual": 75.0
    },
    {
      "id": 2,
      "nome": "Pediatria",
      "total_uso": 130,
      "percentual": 65.0
    }
  ],
  "meta": {
    "limit": 5,
    "total_registros": 2
  }
}
```

---

## 🛠️ **Ferramentas de Desenvolvimento**

### **Comando Personalizado para Listar Rotas**

Devido às limitações do Laravel 5.4 com PHP 7.4+, foi criado um comando personalizado:

```bash
# Listar todas as rotas
php artisan route:list-custom

# Filtrar apenas rotas da API
php artisan route:list-custom --path=api

# Filtrar rotas do dashboard
php artisan route:list-custom --path=dashboard
```

### **Limpeza de Cache**

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

## 🧪 **Testando a API**

### **Exemplo com cURL**

```bash
# 1. Registrar usuário
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"nome": "Teste", "email": "admin@clinicas.com", "password": "123456", "password_confirmation": "123456"}'

# 2. Fazer login (guardar o token retornado)
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@clinicas.com", "password": "123456"}'

# 3. Acessar dados do usuário (usar o token obtido)
curl -H "Authorization: Bearer SEU_TOKEN_AQUI" http://localhost:8000/api/me

# 4. Ver estatísticas do dashboard
curl -H "Authorization: Bearer SEU_TOKEN_AQUI" http://localhost:8000/api/dashboard/stats
```

### **Códigos de Resposta HTTP**

| Código | Significado |
|--------|-------------|
| `200` | Sucesso |
| `201` | Criado com sucesso |
| `401` | Não autorizado (token inválido/ausente) |
| `403` | Usuário inativo |
| `404` | Não encontrado |
| `422` | Erro de validação |
| `500` | Erro interno do servidor |

---

## 🏗️ **Arquitetura Docker**

### **Serviços Containerizados**

```yaml
# docker-compose.yml
services:
  backend:        # PHP 7.4 + Apache + Laravel 5.4
  frontend:       # Node.js + Angular
  db:             # MariaDB 10.5
```

### **Portas Expostas**
- **Frontend (Angular)**: `http://localhost:4200`
- **Backend (Laravel + Apache)**: `http://localhost:8000`  
- **Banco de Dados (MariaDB)**: `localhost:3306`

### **Volumes Persistentes**
- `./backend:/var/www/html` - Código do backend
- `./frontend:/app` - Código do frontend  
- `dbdata:/var/lib/mysql` - Dados do banco de dados

---

## 🏗️ **Estrutura do Projeto**

```
├── backend/                    # Laravel 5.4 + PHP 7.4
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── API/
│   │   │   │   │   └── AuthController.php
│   │   │   │   └── DashboardController.php
│   │   │   └── Middleware/
│   │   ├── Console/
│   │   │   └── Commands/
│   │   │       └── CustomRouteList.php
│   │   └── User.php
│   ├── routes/
│   │   ├── api.php             # Endpoints da API
│   │   └── web.php             # Rotas web (comentadas)
│   ├── database/
│   └── public/                 # DocumentRoot do Apache
├── frontend/                   # Angular + Bootstrap
├── docker/
│   ├── php/
│   │   └── Dockerfile          # PHP 7.4 + Apache
│   └── frontend/
│       └── Dockerfile          # Node.js + Angular CLI
├── docker-compose.yml          # Orquestração dos containers
└── README.md
```

---

## 🤝 **Contribuição**

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

---

## 📝 **Licença**

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

---

## 🐛 **Problemas Conhecidos e Soluções**

### **Relacionados ao Laravel 5.4**
- **Versão Legada**: Pode ter incompatibilidades com PHP 8+ (usar PHP 7.4)
- **Tokens**: Sistema de autenticação simples (sem expiração automática)
- **Helper `now()`**: Não existe - usamos `Carbon::now()`

### **Relacionados ao Docker**
- **Permissões**: Container Apache precisa de permissões corretas para `storage/` e `bootstrap/cache/`
- **CORS**: Pode necessitar configuração adicional em produção
- **Node modules**: Volume anônimo previne reinstalação a cada build

### **Soluções Implementadas**
- ✅ **Dockerfile otimizado** com PHP 7.4 + Apache + mod_rewrite
- ✅ **Permissões automáticas** configuradas no docker-compose
- ✅ **DocumentRoot correto** apontando para `/var/www/html/public`
- ✅ **Comando personalizado** para listar rotas compatível com PHP 7.4+

---

## 📞 **Suporte e Troubleshooting**

### **Comandos de Diagnóstico**

```bash
# Verificar status dos containers
docker ps

# Ver logs do backend
docker-compose logs backend

# Ver logs do frontend  
docker-compose logs frontend

# Ver logs do banco de dados
docker-compose logs db

# Entrar no container do backend
docker exec -it backend_app bash

# Entrar no container do frontend
docker exec -it frontend_app bash
```

### **Problemas Comuns**

#### **Backend não responde**
```bash
# Verificar se o Apache está rodando
docker exec -it backend_app service apache2 status

# Verificar permissões
docker exec -it backend_app ls -la /var/www/html/storage
```

#### **Erro de banco de dados**
```bash
# Verificar se MariaDB está rodando
docker exec -it db mysql -u root -proot -e "SHOW DATABASES;"

# Rodar migrações novamente
docker exec -it backend_app php artisan migrate
```

#### **Frontend não carrega**
```bash
# Verificar se Node.js está instalado
docker exec -it frontend_app node --version

# Reinstalar dependências
docker exec -it frontend_app npm install
```

### **Em caso de dúvidas:**
1. Execute os comandos de diagnóstico acima
2. Consulte os logs dos containers
3. Verifique se todas as portas estão livres (4200, 8000, 3306)
4. Abra uma issue no repositório com os logs de erro

---

## 🚀 **Roadmap - Implementações Futuras**

### 🔐 **Melhorias de Segurança**
- **Expiração automática de tokens** - Tokens com TTL configurável
- **Rate limiting avançado** - Proteção contra ataques de força bruta  
- **Refresh tokens** - Sistema de renovação segura de tokens
- **Log de auditoria** - Rastreamento de ações dos usuários
- **2FA (Two-Factor Authentication)** - Autenticação em duas etapas
- **RBAC (Role-Based Access Control)** - Sistema de papéis e permissões
- **Criptografia de dados sensíveis** - Proteção de informações médicas
- **Validação de entrada robusta** - Sanitização e validação avançada

### 🚀 **Escalabilidade e Performance**
- **Redis para cache** - Cache de consultas e sessões
- **Database indexing** - Otimização de consultas frequentes
- **Compressão de resposta** - Gzip/Deflate para APIs
- **Paginação otimizada** - Cursor-based pagination
- **Queue system** - Processamento assíncrono de tarefas
- **Load balancing** - Múltiplas instâncias do backend
- **CDN integration** - Distribuição de assets estáticos
- **Elasticsearch** - Busca avançada e analytics
- **Microserviços** - Separação de responsabilidades

### 🧪 **Qualidade de Código**
- **Testes automatizados** - Unit tests para controllers e models
- **GitHub Actions CI/CD** - Pipeline básico de deploy
- **PHPStan análise estática** - Detecção de bugs e type safety
- **ESLint/Prettier** - Padronização do código frontend
- **Integration e E2E tests** - Testes de ponta a ponta
- **Code coverage reporting** - Cobertura mínima de 80%
- **Swagger/OpenAPI** - Documentação automática da API
- **Conventional commits** - Padrão de commits e changelog automático

### 📊 **Monitoramento e Observabilidade**
- **Health checks** - Endpoints de saúde da aplicação
- **Logs estruturados** - Logging em formato JSON
- **Error tracking básico** - Captura e notificação de erros
- **Database monitoring** - Métricas de performance do DB
- **Prometheus + Grafana** - Métricas e dashboards avançados
- **Sentry integration** - Monitoramento de erros em produção
- **APM (Application Performance Monitoring)** - New Relic/DataDog
- **Distributed tracing** - Rastreamento de requisições complexas
- **Real-time alerts** - Notificações proativas de problemas

---

**Desenvolvido para o desafio Ger Financeiro Soluções** 🚀