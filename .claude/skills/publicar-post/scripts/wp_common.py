# -*- coding: utf-8 -*-
"""Helpers compartilhados para publicar no blog Rickytech via WordPress REST API.

Descobre o .env, monta a sessão autenticada (com o User-Agent que passa pelo
ModSecurity da HostGator) e expõe funções de alto nível: upload de mídia,
resolução de categorias e get-or-create de tags.

Importante: a API é acessada pela forma ?rest_route= porque a URL bonita
/wp-json/ é bloqueada/roteada errado na hospedagem. A WP_URL no .env já vem
nesse formato: https://rickytech.com.br/index.php?rest_route=/wp/v2
"""
import os
import sys
from pathlib import Path

import requests

UA = "Mozilla/5.0 (MCP-WordPress-Rickytech)"


def project_root() -> Path:
    """Sobe a partir deste arquivo até achar a raiz do projeto (mcp-wordpress/.env)."""
    here = Path(__file__).resolve()
    for parent in [here, *here.parents]:
        if (parent / "mcp-wordpress" / ".env").exists() or (parent / ".git").exists():
            return parent
    # fallback: 4 níveis acima (.claude/skills/publicar-post/scripts -> raiz)
    return here.parents[3]


def load_env() -> dict:
    """Carrega mcp-wordpress/.env em os.environ e devolve as chaves relevantes."""
    env_path = os.environ.get("WP_ENV_FILE")
    env_file = Path(env_path) if env_path else project_root() / "mcp-wordpress" / ".env"
    if not env_file.exists():
        sys.exit(f"[X] .env não encontrado em {env_file}. Configure WP_USER/WP_APP_PASSWORD.")
    for line in env_file.read_text(encoding="utf-8").splitlines():
        line = line.strip()
        if line and not line.startswith("#") and "=" in line:
            k, _, v = line.partition("=")
            os.environ.setdefault(k.strip(), v.strip())
    url = os.environ.get("WP_URL", "https://rickytech.com.br/index.php?rest_route=/wp/v2")
    user = os.environ.get("WP_USER", "")
    pwd = os.environ.get("WP_APP_PASSWORD", "")
    if not user or not pwd:
        sys.exit("[X] WP_USER/WP_APP_PASSWORD ausentes no .env.")
    return {"base": url, "user": user, "password": pwd}


def make_session() -> tuple[requests.Session, str]:
    """Devolve (sessão autenticada, base_url)."""
    cfg = load_env()
    s = requests.Session()
    s.auth = (cfg["user"], cfg["password"])
    s.headers.update({"User-Agent": UA})
    return s, cfg["base"]


# -----------------------------------------------------------------------------
# Operações de alto nível
# -----------------------------------------------------------------------------

def upload_media(s: requests.Session, base: str, path: str, *,
                 filename: str | None = None, alt: str = "", title: str = "",
                 caption: str = "") -> dict:
    """Sobe uma imagem para a biblioteca de mídia e retorna o objeto (com 'id')."""
    p = Path(path)
    filename = filename or p.name
    ext = p.suffix.lower().lstrip(".") or "png"
    ctype = {"jpg": "image/jpeg", "jpeg": "image/jpeg", "png": "image/png",
             "webp": "image/webp", "gif": "image/gif"}.get(ext, "application/octet-stream")
    r = s.post(f"{base}/media", data=p.read_bytes(), timeout=90, headers={
        "Content-Disposition": f'attachment; filename="{filename}"',
        "Content-Type": ctype,
    })
    r.raise_for_status()
    media = r.json()
    if alt or title or caption:
        s.post(f"{base}/media/{media['id']}",
               json={"alt_text": alt, "title": title, "caption": caption}, timeout=30)
    return media


def resolve_category(s: requests.Session, base: str, ref: str) -> int:
    """Resolve uma categoria por slug ou nome. Devolve o ID. Erro se não achar."""
    cats = s.get(f"{base}/categories", params={"per_page": 100}, timeout=30).json()
    ref_l = ref.strip().lower()
    for c in cats:
        if c["slug"].lower() == ref_l or c["name"].lower() == ref_l:
            return c["id"]
    disponiveis = ", ".join(f"{c['slug']}" for c in cats)
    sys.exit(f"[X] Categoria '{ref}' não existe. Disponíveis: {disponiveis}")


def get_or_create_tag(s: requests.Session, base: str, nome: str) -> int:
    """Retorna o ID de uma tag pelo nome, criando-a se não existir."""
    for t in s.get(f"{base}/tags", params={"search": nome}, timeout=30).json():
        if t["name"].lower() == nome.lower():
            return t["id"]
    r = s.post(f"{base}/tags", json={"name": nome}, timeout=30)
    r.raise_for_status()
    return r.json()["id"]
