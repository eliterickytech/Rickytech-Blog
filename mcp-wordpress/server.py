"""MCP server para o blog Rickytech (WordPress REST API).

Expõe ferramentas para listar, ler, criar e atualizar posts.
Credenciais vêm de variáveis de ambiente — nunca escreva a senha no código.
"""

import os
from pathlib import Path
from typing import Any

import requests
from mcp.server.fastmcp import FastMCP

# Carrega .env se existir (útil para rodar standalone; o Claude Desktop
# injeta as variáveis pela config, então isso não atrapalha).
_env = Path(__file__).parent / ".env"
if _env.exists():
    for _linha in _env.read_text(encoding="utf-8").splitlines():
        _linha = _linha.strip()
        if _linha and not _linha.startswith("#") and "=" in _linha:
            _k, _, _v = _linha.partition("=")
            os.environ.setdefault(_k.strip(), _v.strip())

mcp = FastMCP("wordpress-rickytech")

WP_URL = os.environ.get("WP_URL", "https://rickytech.com.br/wp-json/wp/v2")
WP_USER = os.environ.get("WP_USER", "")
WP_APP_PASSWORD = os.environ.get("WP_APP_PASSWORD", "")

if not WP_USER or not WP_APP_PASSWORD:
    raise RuntimeError(
        "Defina WP_USER e WP_APP_PASSWORD nas variáveis de ambiente "
        "(veja o .env.example)."
    )

TIMEOUT = 30

# Session com User-Agent de navegador: o ModSecurity do servidor bloqueia
# o UA padrão do requests ("python-requests/...") com HTTP 406.
sessao = requests.Session()
sessao.auth = (WP_USER, WP_APP_PASSWORD)
sessao.headers.update({"User-Agent": "Mozilla/5.0 (MCP-WordPress-Rickytech)"})


def _resumir_post(post: dict[str, Any]) -> dict[str, Any]:
    """Reduz o payload gigante do WP aos campos úteis."""
    return {
        "id": post.get("id"),
        "status": post.get("status"),
        "link": post.get("link"),
        "titulo": (post.get("title") or {}).get("rendered", ""),
        "trecho": (post.get("excerpt") or {}).get("rendered", ""),
        "data": post.get("date"),
    }


def _erro(resp: requests.Response) -> dict[str, Any]:
    try:
        corpo = resp.json()
    except ValueError:
        corpo = resp.text
    return {"erro": True, "status_http": resp.status_code, "detalhe": corpo}


@mcp.tool()
def listar_posts(busca: str = "", status: str = "publish", quantidade: int = 10) -> list[dict]:
    """Lista posts do blog.

    Args:
        busca: termo opcional para filtrar por título/conteúdo.
        status: publish, draft, pending, future, private (ou 'any').
        quantidade: número máximo de posts (1-100).
    """
    params = {
        "search": busca,
        "status": status,
        "per_page": max(1, min(quantidade, 100)),
        "orderby": "date",
        "order": "desc",
    }
    r = sessao.get(f"{WP_URL}/posts", params=params, timeout=TIMEOUT)
    if not r.ok:
        return [_erro(r)]
    return [_resumir_post(p) for p in r.json()]


@mcp.tool()
def obter_post(post_id: int) -> dict:
    """Retorna o conteúdo completo de um post pelo ID."""
    r = sessao.get(f"{WP_URL}/posts/{post_id}", params={"context": "edit"}, timeout=TIMEOUT)
    if not r.ok:
        return _erro(r)
    p = r.json()
    return {
        **_resumir_post(p),
        "conteudo_html": (p.get("content") or {}).get("rendered", ""),
        "conteudo_raw": (p.get("content") or {}).get("raw", ""),
        "categorias": p.get("categories", []),
        "tags": p.get("tags", []),
    }


@mcp.tool()
def criar_rascunho(titulo: str, conteudo: str, publicar: bool = False) -> dict:
    """Cria um post. Por padrão como rascunho (draft).

    Args:
        titulo: título do post.
        conteudo: corpo em HTML ou blocos Gutenberg.
        publicar: se True, publica direto; senão fica como rascunho.
    """
    payload = {
        "title": titulo,
        "content": conteudo,
        "status": "publish" if publicar else "draft",
    }
    r = sessao.post(f"{WP_URL}/posts", json=payload, timeout=TIMEOUT)
    if not r.ok:
        return _erro(r)
    return _resumir_post(r.json())


@mcp.tool()
def atualizar_post(post_id: int, titulo: str = "", conteudo: str = "", status: str = "") -> dict:
    """Atualiza título, conteúdo e/ou status de um post existente.

    Passe apenas os campos que quer mudar (deixe os outros vazios).
    status pode ser: draft, publish, pending, private.
    """
    payload: dict[str, Any] = {}
    if titulo:
        payload["title"] = titulo
    if conteudo:
        payload["content"] = conteudo
    if status:
        payload["status"] = status
    if not payload:
        return {"erro": True, "detalhe": "Nenhum campo para atualizar foi informado."}
    r = sessao.post(f"{WP_URL}/posts/{post_id}", json=payload, timeout=TIMEOUT)
    if not r.ok:
        return _erro(r)
    return _resumir_post(r.json())


@mcp.tool()
def listar_categorias() -> list[dict]:
    """Lista as categorias do blog com id, nome e contagem de posts."""
    r = sessao.get(f"{WP_URL}/categories", params={"per_page": 100}, timeout=TIMEOUT)
    if not r.ok:
        return [_erro(r)]
    return [{"id": c["id"], "nome": c["name"], "slug": c["slug"], "posts": c["count"]} for c in r.json()]


if __name__ == "__main__":
    mcp.run()
