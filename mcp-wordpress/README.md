# MCP WordPress — Rickytech

Servidor MCP para gerenciar o blog (rickytech.com.br) via WordPress REST API direto do Claude.

## Ferramentas expostas

| Ferramenta | O que faz |
|---|---|
| `listar_posts` | Lista posts (filtra por busca, status, quantidade) |
| `obter_post` | Conteúdo completo de um post pelo ID |
| `criar_rascunho` | Cria post (rascunho por padrão, ou publica) |
| `atualizar_post` | Edita título/conteúdo/status de um post |
| `listar_categorias` | Lista categorias com contagem |

## Setup

1. Dependências já instaladas em `.venv/`. Se precisar refazer:
   ```powershell
   python -m venv .venv
   .\.venv\Scripts\python.exe -m pip install -r requirements.txt
   ```

2. Preencha o `.env` (copie de `.env.example`):
   - `WP_USER` — seu usuário do WordPress (login, não o e-mail necessariamente)
   - `WP_APP_PASSWORD` — senha de aplicativo (Perfil → Senhas de aplicativo)

3. Teste rápido:
   ```powershell
   .\.venv\Scripts\python.exe smoke_test.py
   ```

## Conectar no Claude Desktop

Edite `%APPDATA%\Claude\claude_desktop_config.json` e adicione:

```json
{
  "mcpServers": {
    "wordpress-rickytech": {
      "command": "C:\\Projetos\\Rickytech\\Rickytech-Blog\\mcp-wordpress\\.venv\\Scripts\\python.exe",
      "args": ["C:\\Projetos\\Rickytech\\Rickytech-Blog\\mcp-wordpress\\server.py"],
      "env": {
        "WP_URL": "https://rickytech.com.br/wp-json/wp/v2",
        "WP_USER": "seu_usuario",
        "WP_APP_PASSWORD": "sua senha de aplicativo"
      }
    }
  }
}
```

Reinicie o Claude Desktop. As ferramentas aparecem no ícone de plugues.

## Conectar no Claude Code (CLI)

```powershell
claude mcp add wordpress-rickytech `
  -e WP_USER=seu_usuario `
  -e "WP_APP_PASSWORD=sua senha de aplicativo" `
  -- C:\Projetos\Rickytech\Rickytech-Blog\mcp-wordpress\.venv\Scripts\python.exe C:\Projetos\Rickytech\Rickytech-Blog\mcp-wordpress\server.py
```

## Segurança

- O `.env` está no `.gitignore` — não suba ele pro git.
- Se a senha de aplicativo vazar, revogue em **WordPress → Perfil → Senhas de aplicativo** e gere outra.
