r"""Testa as credenciais e a conexão com o WordPress sem subir o MCP.

Uso: .\.venv\Scripts\python.exe smoke_test.py
"""

import os
from pathlib import Path

import requests

# Carrega o .env manualmente (sem dependência extra)
env_path = Path(__file__).parent / ".env"
if env_path.exists():
    for linha in env_path.read_text(encoding="utf-8").splitlines():
        linha = linha.strip()
        if not linha or linha.startswith("#") or "=" not in linha:
            continue
        chave, _, valor = linha.partition("=")
        os.environ.setdefault(chave.strip(), valor.strip())

WP_URL = os.environ.get("WP_URL", "https://rickytech.com.br/wp-json/wp/v2")
WP_USER = os.environ.get("WP_USER", "")
WP_APP_PASSWORD = os.environ.get("WP_APP_PASSWORD", "")

if not WP_USER or WP_USER.startswith("PREENCHA"):
    raise SystemExit("[X] Preencha WP_USER no arquivo .env primeiro.")

print(f"-> Testando {WP_URL} como '{WP_USER}' ...")
r = requests.get(
    f"{WP_URL}/posts",
    params={"per_page": 3, "status": "any"},
    auth=(WP_USER, WP_APP_PASSWORD),
    headers={"User-Agent": "Mozilla/5.0 (MCP-WordPress-Rickytech)"},
    timeout=30,
)

if r.ok:
    posts = r.json()
    print(f"[OK] Autenticado! {len(posts)} post(s) retornado(s):")
    for p in posts:
        print(f"   #{p['id']} [{p['status']}] {p['title']['rendered']}")
else:
    print(f"[X] Falhou: HTTP {r.status_code}")
    print(r.text[:500])
