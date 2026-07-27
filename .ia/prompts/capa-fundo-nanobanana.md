# Prompt de capa (fundo por IA) — Nano Banana / Gemini

Prompt pronto para gerar o **fundo** da capa de um post no **app Gemini**
(gemini.google.com, com o plano Pro). Gera só o fundo, **sem texto** — o
branding (logo, categoria, título) é aplicado por cima depois, pela skill
`publicar-post`, pra ficar sempre nítido e alinhado à marca.

> Regras de ouro: **16:9**, **sem nenhum texto/letra/logo**, e **metade
> esquerda escura e vazia** (é onde o título vai entrar).

---

## Prompt (copie e cole) — versão em inglês (o modelo entende melhor)

Troque `{TEMA}` pelo assunto do post e `{MOTIVO}` pela dica de motivo da
categoria (tabela mais abaixo).

```
Ultra high-quality abstract editorial background for a technology blog cover.
Theme: {TEMA}.
Style: modern, cinematic, premium magazine-cover aesthetic, sophisticated and clean (not busy).
Color: dark charcoal-navy base (#0b0b11) with a soft luminous gradient glow blending violet (#8b6dff) into cyan (#22d3ee).
Motifs: {MOTIVO} — fine geometric wireframe grid, subtle floating particles, gentle depth-of-field bokeh, cinematic depth.
Composition: keep the LEFT ~40% of the frame dark and almost empty (negative space) for a text overlay; concentrate all visual interest on the RIGHT side.
Hard constraints: absolutely NO text, NO words, NO letters, NO numbers, NO logos, NO watermarks, NO UI elements.
Format: 16:9 widescreen, 1920x1080, high resolution, crisp.
```

## Versão em português (se preferir)

```
Fundo abstrato editorial de altíssima qualidade para a capa de um blog de tecnologia.
Tema: {TEMA}.
Estilo: moderno, cinematográfico, premium (capa de revista), sofisticado e limpo (nada poluído).
Cores: base escura carvão-azulado (#0b0b11) com um brilho de gradiente suave indo de violeta (#8b6dff) a ciano (#22d3ee).
Motivos: {MOTIVO} — grade geométrica fina, partículas flutuantes sutis, leve desfoque de profundidade, sensação de profundidade cinematográfica.
Composição: mantenha os ~40% da ESQUERDA escuros e quase vazios (espaço negativo) para sobrepor texto; concentre o interesse visual na DIREITA.
Restrições rígidas: absolutamente SEM texto, SEM palavras, SEM letras, SEM números, SEM logos, SEM marcas d'água, SEM elementos de interface.
Formato: 16:9 widescreen, 1920x1080, alta resolução, nítido.
```

---

## Motivos por categoria (`{MOTIVO}`)

| Categoria (slug) | Sugestão de `{MOTIVO}` |
|---|---|
| `ia` | redes neurais, nós conectados, fluxo de dados, partículas |
| `engenharia` | linhas de blueprint, estruturas isométricas, engrenagens abstratas |
| `backend` | pipelines de dados, cilindros/nós de banco, fluxos de servidor |
| `frontend` | painéis de UI em camadas, glassmorphism, formas geométricas coloridas |
| `devops` | pipeline CI/CD, contêineres, topologia de rede, laço de infinito |
| `cloud` | nuvens abstratas, regiões conectadas, malha de pontos |
| `seguranca` | escudo/cadeado abstrato, grade criptografada |
| `carreira` | caminho ascendente, crescimento, setas/degraus abstratos |
| `noticias` / `tutoriais` | tecnologia abstrata, limpo e neutro |

## Exemplo já preenchido (post do Claude Fable 5, categoria `ia`)

```
Ultra high-quality abstract editorial background for a technology blog cover.
Theme: context engineering with AI coding agents (Claude Fable 5).
Style: modern, cinematic, premium magazine-cover aesthetic, sophisticated and clean.
Color: dark charcoal-navy base (#0b0b11) with a soft luminous gradient glow blending violet (#8b6dff) into cyan (#22d3ee).
Motifs: neural-network nodes and connecting lines, flowing data — fine geometric wireframe grid, subtle floating particles, gentle depth-of-field bokeh, cinematic depth.
Composition: keep the LEFT ~40% of the frame dark and almost empty (negative space) for a text overlay; concentrate all visual interest on the RIGHT side.
Hard constraints: absolutely NO text, NO words, NO letters, NO numbers, NO logos, NO watermarks, NO UI elements.
Format: 16:9 widescreen, 1920x1080, high resolution, crisp.
```

## Dicas rápidas
- Se a imagem vier com texto/letras, gere de novo reforçando "no text, no letters".
- Peça algumas variações e escolha a com **mais espaço vazio à esquerda**.
- Baixe em **16:9** e na maior resolução possível (a capa final é 1200×675).
