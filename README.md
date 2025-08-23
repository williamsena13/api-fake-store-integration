# API FakeStore Integration

Microserviço Laravel 10+ que integra com a Fake Store API.

## Características

- **Laravel 10+** com PHP 8.2+
- **Padrão Strategy** para sincronização (Full/Delta)
- **AbstractService** e **AbstractRepository** para camadas organizadas
- **BusinessException** para tratamento padronizado de erros
- **Spatie Activity Log** configurado em BaseModel
- **Middleware de integração** com logs estruturados e rate limiting
- **SoftDeletes** em todas as tabelas principais
- **SQL puro** para estatísticas agregadas
- **Resiliência** com retry/backoff e tratamento de timeouts

## Setup

### 1. Instalação

```bash
# Clone o projeto
git clone https://github.com/williamsena13/api-fake-store-integration.git
cd api-fake-store-integration

# Instale dependências
composer install

# Configure ambiente
cp .env.example .env
php artisan key:generate
```

### 2. Configuração do Banco

#### MySQL (padrão)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_fake_store
DB_USERNAME=root
DB_PASSWORD=
```

#### PostgreSQL
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=api_fake_store
DB_USERNAME=postgres
DB_PASSWORD=password
```

### 3. Migrações

```bash
# Execute as migrações
php artisan migrate
```

### 4. Configuração de Logs

O projeto usa logs estruturados em JSON. Configure no `.env`:

```env
LOG_CHANNEL=single
LOG_LEVEL=info
```

## Execução

### Servidor de Desenvolvimento

```bash
php artisan serve
# Acesse: http://localhost:8000
```

### Sincronização

#### Via API
```bash
# Sincronização completa
curl -X POST "http://localhost:8000/api/integracoes/fakestore/sync?mode=full" \
  -H "X-Client-Id: demo" \
  -H "Content-Type: application/json"

# Sincronização delta
curl -X POST "http://localhost:8000/api/integracoes/fakestore/sync?mode=delta" \
  -H "X-Client-Id: demo" \
  -H "Content-Type: application/json"
```

#### Via Artisan
```bash
# Sincronização completa
php artisan fakestore:sync --mode=full

# Sincronização delta
php artisan fakestore:sync --mode=delta
```

## Endpoints da API

Todos os endpoints requerem o header `X-Client-Id`.

### Sincronização
```bash
POST /api/integracoes/fakestore/sync?mode=full|delta
```

### Catálogo de Produtos
```bash
# Listagem com filtros
GET /api/catalogo/products?category=electronics&min_price=10&max_price=100&q=shirt&sort=price&order=desc&page=1&per_page=20

# Detalhes do produto
GET /api/catalogo/products/{id}
```

### Estatísticas
```bash
GET /api/catalogo/stats
```

## Exemplos de Uso

### 1. Listagem de Produtos com Filtros

```bash
curl -X GET "http://localhost:8000/api/catalogo/products?q=shirt&min_price=10&max_price=100&sort=price&order=desc&page=1&per_page=20" \
  -H "X-Client-Id: demo"
```

**Resposta:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "external_id": 1,
        "title": "Fjallraven - Foldsack No. 1 Backpack",
        "description": "Your perfect pack...",
        "price": "109.95",
        "image_url": "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg",
        "category": {
          "id": 1,
          "name": "men's clothing"
        }
      }
    ],
    "per_page": 20,
    "total": 20
  }
}
```

### 2. Detalhes do Produto

```bash
curl -X GET "http://localhost:8000/api/catalogo/products/1" \
  -H "X-Client-Id: demo"
```

### 3. Estatísticas

```bash
curl -X GET "http://localhost:8000/api/catalogo/stats" \
  -H "X-Client-Id: demo"
```

**Resposta:**
```json
{
  "success": true,
  "data": {
    "total_products": 20,
    "avg_price": 75.99,
    "by_category": [
      {"category": "electronics", "total": 6},
      {"category": "jewelery", "total": 4}
    ],
    "top5_expensive": [...]
  }
}
```

## Arquitetura e Padrões

### Strategy Pattern

O padrão Strategy é implementado para sincronização:

- **FullSyncStrategy**: Sincroniza todos os produtos
- **DeltaSyncStrategy**: Sincroniza apenas produtos alterados (simulado)
- **SyncContext**: Escolhe a estratégia baseada no parâmetro `mode`

### AbstractService

Centraliza funcionalidades comuns:
- Transações com `withTransaction()`
- Validações com `findOrFailBusiness()`
- Sanitização de filtros
- Paginação configurável

### BusinessException

Padroniza retorno de erros:

```json
{
  "error": {
    "code": "integration.missing_client_id",
    "message": "Missing X-Client-Id",
    "status": 400,
    "context": {},
    "request_id": "uuid-here"
  }
}
```

### Middleware de Integração

- Valida `X-Client-Id` obrigatório
- Gera/propaga `X-Request-Id`
- Logs estruturados com tempo de resposta
- Rate limiting por cliente (opcional)

### Logs Estruturados

Exemplo de log de requisição:
```json
{
  "message": "Integration Request",
  "context": {
    "request_id": "uuid",
    "client_id": "demo",
    "method": "GET",
    "url": "http://localhost:8000/api/catalogo/products",
    "duration_ms": 45.67,
    "status_code": 200,
    "ip": "127.0.0.1"
  }
}
```

## Índices e Performance

### Índices Criados

- `categories.name` - Busca por nome da categoria
- `products.external_id` (unique) - Sincronização por ID externo
- `products.category_id` - Join com categorias
- `products.price` - Filtros por preço

### Prevenção N+1

- `ProductRepository::filter()` usa `with('category')`
- `ProductService::getProductById()` usa `getWithCategory()`

## Resiliência e Tratamento de Erros

### HTTP Client

- **Timeout**: 30 segundos configurável
- **Retry**: 3 tentativas com backoff de 1s
- **Mapeamento de erros**:
  - Timeout → 504 `integration.timeout`
  - 5xx → 502 `integration.upstream_error`
  - 4xx → 424 `integration.upstream_request`

### Sincronização

- Erros por item não interrompem o processo
- Logs detalhados de cada erro
- Resultado consolidado com contadores

## Testes

```bash
# Executar todos os testes
php artisan test

# Testes específicos
php artisan test --filter=SyncEndpointTest
php artisan test --filter=CatalogListTest
```

## Docker (Opcional)

```dockerfile
# Dockerfile
FROM php:8.2-fpm

# Instalar dependências...
COPY . /var/www
WORKDIR /var/www

RUN composer install --no-dev --optimize-autoloader
```

```yaml
# docker-compose.yml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:8000"
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: api_fake_store
```

## Decisões de Modelagem

### 1. Separação de IDs

- **ID interno** (`id`): Auto-increment para referências internas
- **ID externo** (`external_id`): ID da FakeStore API para sincronização

### 2. Relacionamentos

- **Category → Products**: One-to-Many
- **Cascade Delete**: Produtos são removidos se categoria for deletada

### 3. SoftDeletes

- Mantém histórico para auditoria
- Activity Log registra todas as alterações
- Queries filtram automaticamente registros deletados

### 4. Timestamps Customizados

```php
$table->timestamp('created_at')->useCurrent()->comment('The created_at timestamp registered');
$table->timestamp('updated_at')->useCurrentOnUpdate()->comment('The updated_at timestamp registered')->nullable()->default(null);
$table->softDeletes();
```

## Monitoramento

### Logs de Integração

- Tempo de resposta por requisição
- Rate limiting por cliente
- Erros de sincronização
- Performance de queries

### Activity Log

- Todas as alterações nos models
- Rastreamento de quem/quando/o que mudou
- Útil para auditoria e debugging

## Próximos Passos

1. **Cache**: Implementar cache Redis para listagens
2. **Queue**: Mover sincronização para background jobs
3. **Rate Limiting**: Implementar limitador por X-Client-Id
4. **Webhooks**: Receber notificações de mudanças
5. **Métricas**: Prometheus/Grafana para monitoramento

## Contribuição

1. Fork o projeto
2. Crie uma branch: `git checkout -b feature/nova-funcionalidade`
3. Commit: `git commit -m 'feat: adiciona nova funcionalidade'`
4. Push: `git push origin feature/nova-funcionalidade`
5. Abra um Pull Request

## Licença

MIT License - veja [LICENSE](LICENSE) para detalhes.
