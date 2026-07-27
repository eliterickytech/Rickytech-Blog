# -*- coding: utf-8 -*-
"""Publica ou atualiza um post no blog Rickytech (WordPress REST API).

Cria RASCUNHO por padrão (fluxo seguro: revisa o preview, depois promove a
publicado). Gera e sobe a capa on-brand automaticamente, resolve a categoria
por slug/nome e cria as tags que faltarem.

Exemplos:
  # 1) criar rascunho (gera capa automática)
  python publicar.py --titulo "Meu post" --corpo corpo.html \
      --categoria ia --tags "Claude,Anthropic" --resumo "Linha de chamada."

  # 2) revisar no preview, depois publicar e fixar como destaque da home
  python publicar.py --update 184 --status publish --destaque

  # 3) editar o corpo de um post existente
  python publicar.py --update 184 --corpo corpo_v2.html
"""
import argparse
import json
import sys
import tempfile
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
import wp_common as wp  # noqa: E402
from gerar_capa import gerar as gerar_capa  # noqa: E402


def main():
    ap = argparse.ArgumentParser(description="Publica/atualiza post no blog Rickytech.")
    ap.add_argument("--titulo")
    ap.add_argument("--corpo", help="arquivo .html com o corpo do artigo")
    ap.add_argument("--categoria", default=None, help="slug ou nome (ex.: ia, engenharia)")
    ap.add_argument("--tags", default=None, help="nomes separados por vírgula")
    ap.add_argument("--resumo", default=None, help="excerpt / linha de chamada (lede)")
    ap.add_argument("--status", choices=["draft", "publish", "pending", "private"],
                    default=None,
                    help="ao criar, padrão é draft; ao atualizar, só muda se informado")
    ap.add_argument("--destaque", action="store_true",
                    help="marca como sticky -> vira a notícia principal da home")
    ap.add_argument("--capa", default=None, help="caminho de uma imagem de capa PRONTA (usada como está)")
    ap.add_argument("--fundo", default=None,
                    help="fundo pronto (ex.: gerado por IA); o branding é desenhado por cima")
    ap.add_argument("--sem-capa", action="store_true",
                    help="não usa imagem destacada (deixa a thumb generativa do tema)")
    ap.add_argument("--eyebrow", default=None, help="rótulo do pill da capa (default = categoria)")
    ap.add_argument("--logo", default=None, help="logo mark p/ a capa (default: assets/logos)")
    ap.add_argument("--update", type=int, default=None, help="ID de um post p/ atualizar")
    ap.add_argument("--json", action="store_true", help="saída em JSON")
    a = ap.parse_args()

    if not a.update and (not a.titulo or not a.corpo):
        ap.error("para criar um post são obrigatórios --titulo e --corpo (ou use --update).")

    s, base = wp.make_session()
    root = wp.project_root()
    payload: dict = {}

    if a.titulo:
        payload["title"] = a.titulo
    if a.corpo:
        payload["content"] = Path(a.corpo).read_text(encoding="utf-8")
    if a.resumo is not None:
        payload["excerpt"] = a.resumo
    # status: só entra no payload se informado; ao CRIAR sem informar, vira draft.
    # (Evita rebaixar um post publicado a rascunho num --update sem --status.)
    if a.status:
        payload["status"] = a.status
    elif not a.update:
        payload["status"] = "draft"
    if a.destaque:
        payload["sticky"] = True

    if a.categoria:
        payload["categories"] = [wp.resolve_category(s, base, a.categoria)]
    if a.tags:
        nomes = [t.strip() for t in a.tags.split(",") if t.strip()]
        payload["tags"] = [wp.get_or_create_tag(s, base, n) for n in nomes]

    # ----- capa -----
    capa_path = None
    if not a.sem_capa:
        if a.capa:
            capa_path = a.capa  # imagem final pronta, usada como está
        elif a.fundo or not a.update:
            # gera a capa: com --fundo, desenha o branding sobre o fundo dado;
            # sem --fundo, usa o fundo por código. (Ao atualizar, só gera se veio --fundo.)
            eyebrow = a.eyebrow or (a.categoria.upper() if a.categoria else "ARTIGO")
            logo = a.logo or str(root / "assets" / "logos" / "ricky-mark-transparent.png")
            tmp = Path(tempfile.gettempdir()) / "rickytech-capa.png"
            gerar_capa(a.titulo or "", eyebrow, str(tmp), logo, fundo=a.fundo, tema=a.categoria)
            capa_path = str(tmp)

    if capa_path:
        media = wp.upload_media(
            s, base, capa_path,
            filename=(a.titulo or "capa").lower().replace(" ", "-")[:60] + ".png",
            alt=f"Capa: {a.titulo or ''} — Central de Artigos".strip(" —"),
            title=a.titulo or "Capa",
            caption="Ilustração de capa — Central de Artigos, Rickytech.",
        )
        payload["featured_media"] = media["id"]

    # ----- cria ou atualiza -----
    endpoint = f"{base}/posts/{a.update}" if a.update else f"{base}/posts"
    r = s.post(endpoint, json=payload, timeout=90)
    if not r.ok:
        sys.exit(f"[X] Falhou HTTP {r.status_code}: {r.text[:600]}")
    post = r.json()

    out = {
        "id": post["id"],
        "status": post["status"],
        "sticky": post.get("sticky"),
        "link": post["link"],
        "preview": f"{post['link']}?preview=true" if post["status"] != "publish" else post["link"],
        "editar": f"https://rickytech.com.br/wp-admin/post.php?post={post['id']}&action=edit",
        "featured_media": post.get("featured_media"),
    }
    if a.json:
        print(json.dumps(out, ensure_ascii=False, indent=2))
    else:
        print(f"[OK] Post #{out['id']} — status={out['status']} sticky={out['sticky']}")
        print(f"     link:    {out['link']}")
        print(f"     editar:  {out['editar']}")
        if out["featured_media"]:
            print(f"     capa:    media #{out['featured_media']}")


if __name__ == "__main__":
    main()
