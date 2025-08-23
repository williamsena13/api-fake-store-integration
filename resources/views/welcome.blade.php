<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API FakeStore Integration - Desafio Técnico</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; background: #f8fafc; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .header { text-align: center; margin-bottom: 3rem; }
        .title { font-size: 2.5rem; font-weight: 700; color: #1a202c; margin-bottom: 1rem; }
        .subtitle { font-size: 1.2rem; color: #718096; }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-bottom: 3rem; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
        .card { background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 4px solid #3182ce; }
        .card h3 { font-size: 1.3rem; font-weight: 600; color: #2d3748; margin-bottom: 1rem; }
        .card p { color: #4a5568; margin-bottom: 1rem; }
        .badge { display: inline-block; background: #e2e8f0; color: #2d3748; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.875rem; margin: 0.25rem 0.25rem 0.25rem 0; }
        .endpoints { background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .endpoint { background: #f7fafc; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; border-left: 3px solid #38a169; }
        .method { font-weight: 600; color: #38a169; }
        .url { font-family: 'Courier New', monospace; color: #2d3748; }
        .footer { text-align: center; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #e2e8f0; color: #718096; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">🏪 API FakeStore Integration</h1>
            <p class="subtitle">Desafio Técnico em Laravel — Integração com Fake Store API</p>
        </div>

        <div class="grid">
            <div class="card">
                <h3>🎯 Características Principais</h3>
                <p>Microserviço Laravel 10+ com padrões modernos de desenvolvimento:</p>
                <div class="badge">Laravel 10+</div>
                <div class="badge">PHP 8.2+</div>
                <div class="badge">Strategy Pattern</div>
                <div class="badge">AbstractService</div>
                <div class="badge">BusinessException</div>
                <div class="badge">Activity Log</div>
                <div class="badge">SoftDeletes</div>
            </div>

            <div class="card">
                <h3>🔄 Sincronização</h3>
                <p>Sistema de sincronização com a FakeStore API usando padrão Strategy:</p>
                <p><strong>Full Sync:</strong> Sincroniza todos os produtos</p>
                <p><strong>Delta Sync:</strong> Sincroniza apenas alterações</p>
                <p><strong>Resiliência:</strong> Retry/backoff e tratamento de timeouts</p>
            </div>

            <div class="card">
                <h3>📊 Funcionalidades</h3>
                <p>API completa para catálogo de produtos:</p>
                <p>• Listagem com filtros avançados</p>
                <p>• Busca por texto, categoria e preço</p>
                <p>• Paginação configurável</p>
                <p>• Estatísticas agregadas</p>
                <p>• Logs estruturados em JSON</p>
            </div>

            <div class="card">
                <h3>🛡️ Middleware & Segurança</h3>
                <p>Middleware de integração com recursos avançados:</p>
                <p>• Header X-Client-Id obrigatório</p>
                <p>• Geração de X-Request-Id</p>
                <p>• Rate limiting por cliente</p>
                <p>• Logs de tempo de resposta</p>
            </div>
        </div>

        <div class="endpoints">
            <h3>🚀 Endpoints Disponíveis</h3>
            <p style="margin-bottom: 1.5rem;">Todos os endpoints requerem o header <code>X-Client-Id</code></p>
            
            <div class="endpoint">
                <span class="method">POST</span> 
                <span class="url">/api/integracoes/fakestore/sync?mode=full|delta</span>
                <p>Sincronização completa ou incremental com a FakeStore API</p>
            </div>

            <div class="endpoint">
                <span class="method">GET</span> 
                <span class="url">/api/catalogo/products</span>
                <p>Listagem de produtos com filtros: category, min_price, max_price, q, sort, order</p>
            </div>

            <div class="endpoint">
                <span class="method">GET</span> 
                <span class="url">/api/catalogo/products/{id}</span>
                <p>Detalhes de um produto específico</p>
            </div>

            <div class="endpoint">
                <span class="method">GET</span> 
                <span class="url">/api/catalogo/stats</span>
                <p>Estatísticas do catálogo: total, preço médio, por categoria</p>
            </div>

            <div class="endpoint">
                <span class="method">GET</span> 
                <span class="url">/api/catalogo/categories</span>
                <p>Lista todas as categorias disponíveis</p>
            </div>
        </div>

        <div class="footer">
            <p>Laravel v{{ Illuminate\Foundation\Application::VERSION }} | PHP v{{ PHP_VERSION }}</p>
            <p>By <a href="https://williamsena13.github.io/" target="_blank" rel="noopener noreferrer" style="color: #3182ce; text-decoration: none; font-weight: 600;">Bassena Dev</a> © {{ date('Y') }}. V. 1.0.0</p>
        </div>
    </div>
</body>
</html>
