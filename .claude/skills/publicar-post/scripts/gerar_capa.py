# -*- coding: utf-8 -*-
"""Gera a capa 1200x675 on-brand para um post do blog Rickytech.

Dois modos:
  1. Fundo por código (padrão): desenha um fundo dark violeta->ciano com grid
     e marca-d'água { }.
  2. Fundo pronto (--fundo img.png): usa uma imagem existente como fundo — ex.:
     um fundo gerado por IA (Nano Banana). Encaixa em 16:9 e escurece a esquerda
     pra legibilidade.

Em ambos, o branding (logo + wordmark, pill de categoria, título, rodapé) é
desenhado POR CÓDIGO por cima — sempre nítido e correto (IA erra texto).

Uso:
  python gerar_capa.py --titulo "Meu título" --eyebrow "IA" --saida capa.png
  python gerar_capa.py --titulo "Meu título" --eyebrow "IA" --saida capa.png \
      --fundo .ia/capas/meu-fundo.png
"""
import argparse
from pathlib import Path

from PIL import Image, ImageDraw, ImageFont, ImageFilter, ImageOps

W, H = 1200, 675
FONTS = Path(r"C:\Windows\Fonts")
VIOLET = (139, 109, 255)   # #8b6dff
CYAN = (34, 211, 238)      # #22d3ee
INK = (11, 11, 17)

# Tema de cor do fundo por categoria (glow esquerdo, glow direito).
# Dá identidade distinta a cada categoria mantendo a família da marca.
THEMES = {
    "ia":         ((139, 109, 255), (34, 211, 238)),   # violeta -> ciano
    "engenharia": ((251, 191, 36),  (139, 109, 255)),  # âmbar   -> violeta
    "backend":    ((45, 212, 191),  (96, 165, 250)),   # teal    -> azul
    "devops":     ((96, 165, 250),  (34, 211, 238)),   # azul    -> ciano
    "frontend":   ((251, 113, 133), (139, 109, 255)),  # rosa    -> violeta
    "cloud":      ((56, 189, 248),  (129, 140, 248)),  # céu     -> índigo
    "seguranca":  ((251, 113, 133), (96, 165, 250)),   # rosa    -> azul
    "carreira":   ((52, 211, 153),  (96, 165, 250)),   # verde   -> azul
}


def font(name, size):
    return ImageFont.truetype(str(FONTS / name), size)


def wrap(draw, text, fnt, max_w):
    words, lines, cur = text.split(), [], ""
    for wd in words:
        t = (cur + " " + wd).strip()
        if draw.textlength(t, font=fnt) <= max_w:
            cur = t
        else:
            if cur:
                lines.append(cur)
            cur = wd
    if cur:
        lines.append(cur)
    return lines


def _fundo_codigo(tema: str | None = None) -> Image.Image:
    """Fundo desenhado por código (dark, glow bicolor por categoria, grid)."""
    c1, c2 = THEMES.get((tema or "").lower(), (VIOLET, CYAN))
    img = Image.new("RGB", (W, H), INK).convert("RGBA")
    glow = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    gd = ImageDraw.Draw(glow)
    gd.ellipse([-260, -320, 640, 420], fill=c1 + (170,))
    gd.ellipse([760, 320, 1500, 900], fill=c2 + (140,))
    gd.ellipse([420, -160, 980, 320], fill=c1 + (90,))
    img = Image.alpha_composite(img, glow.filter(ImageFilter.GaussianBlur(190)))
    img = Image.alpha_composite(img, Image.new("RGBA", (W, H), (7, 7, 12, 96)))
    d = ImageDraw.Draw(img)
    for x in range(0, W, 48):
        d.line([(x, 0), (x, H)], fill=(255, 255, 255, 6))
    for y in range(0, H, 48):
        d.line([(0, y), (W, y)], fill=(255, 255, 255, 6))
    # (sem glifo { } no fundo)
    return img


def _fundo_imagem(path: str) -> Image.Image:
    """Usa uma imagem pronta como fundo: encaixa em 16:9 e escurece a esquerda."""
    base = ImageOps.fit(Image.open(path).convert("RGBA"), (W, H), Image.LANCZOS)
    # véu escuro degradê: forte à esquerda (área do texto), some à direita
    veil = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    vd = ImageDraw.Draw(veil)
    for x in range(W):
        a = int(215 * max(0.0, 1 - (x / (W * 0.62))))  # ~0.62 da largura
        vd.line([(x, 0), (x, H)], fill=(6, 6, 11, a))
    base = Image.alpha_composite(base, veil)
    # vinheta suave no canto inferior direito: esconde marca d'água de geradores
    # de IA (ex.: o ✦ do Gemini) e ancora o crédito do autor no rodapé.
    corner = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    ImageDraw.Draw(corner).ellipse([W - 380, H - 250, W + 140, H + 180],
                                   fill=(6, 6, 11, 235))
    corner = corner.filter(ImageFilter.GaussianBlur(75))
    return Image.alpha_composite(base, corner)


def _branding(img: Image.Image, titulo: str, eyebrow: str,
              logo: str | None, autor: str) -> Image.Image:
    """Desenha logo+wordmark, pill de categoria, título e rodapé sobre img."""
    MARGIN = 74
    draw = ImageDraw.Draw(img)

    tx = MARGIN
    if logo and Path(logo).exists():
        mark = Image.open(logo).convert("RGBA")
        mh = 46
        mark = mark.resize((int(mark.width * mh / mark.height), mh), Image.LANCZOS)
        img.paste(mark, (MARGIN, 86), mark)
        tx = MARGIN + mark.width + 14
    wf = font("segoeuib.ttf", 26)
    draw.text((tx, 92), "Central de ", font=wf, fill=(236, 238, 245))
    w1 = draw.textlength("Central de ", font=wf)
    draw.text((tx + w1, 92), "Artigos", font=wf, fill=VIOLET)

    # (sem pill de categoria: a marca no topo-esquerdo já identifica o site)

    for size in (62, 56, 50, 44):
        tf = font("segoeuib.ttf", size)
        lines = wrap(draw, titulo, tf, W - 2 * MARGIN - 40)
        if len(lines) <= 3:
            break
    ty = 340
    line_h = int(size * 1.18)
    for ln in lines:
        draw.text((MARGIN, ty), ln, font=tf, fill=(245, 246, 250))
        ty += line_h
    draw.rounded_rectangle([MARGIN, ty + 8, MARGIN + 96, ty + 15], radius=4, fill=CYAN)

    ff = font("consola.ttf", 22)
    draw.text((MARGIN, H - 62), "rickytech.com.br", font=ff, fill=(150, 154, 170))
    # (sem crédito de autor na capa)
    return img


def gerar(titulo: str, eyebrow: str, saida: str, logo: str | None = None,
          autor: str = "por Ricardo Perdigão", fundo: str | None = None,
          tema: str | None = None):
    base = _fundo_imagem(fundo) if fundo else _fundo_codigo(tema)
    img = _branding(base, titulo, eyebrow, logo, autor)
    Path(saida).parent.mkdir(parents=True, exist_ok=True)
    img.convert("RGB").save(saida, "PNG", optimize=True)
    return saida


if __name__ == "__main__":
    ap = argparse.ArgumentParser(description="Gera capa on-brand para o blog Rickytech.")
    ap.add_argument("--titulo", required=True)
    ap.add_argument("--eyebrow", default="ARTIGO", help="rótulo do pill (ex.: IA, ENGENHARIA)")
    ap.add_argument("--saida", required=True, help="caminho do PNG de saída")
    ap.add_argument("--logo", default=None, help="caminho da logo mark (PNG transparente)")
    ap.add_argument("--autor", default="por Ricardo Perdigão")
    ap.add_argument("--fundo", default=None,
                    help="imagem de fundo pronta (ex.: fundo gerado por IA)")
    ap.add_argument("--tema", default=None,
                    help="slug de categoria p/ o tema de cor do fundo por código")
    a = ap.parse_args()
    print("OK ->", gerar(a.titulo, a.eyebrow, a.saida, a.logo, a.autor, a.fundo, a.tema))
