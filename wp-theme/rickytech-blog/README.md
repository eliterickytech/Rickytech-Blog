# Rickytech — Central de Artigos (Tema WordPress)

Tema clássico de blog para a Rickytech, recriado a partir do design "Central de Artigos".
Dark-first, identidade violeta + ciano, tipografia DM Sans / DM Mono, thumbnails generativas,
leitura com índice (TOC) e barra de progresso.

## Instalação

1. No painel: **Aparência → Temas → Adicionar → Enviar tema**.
2. Envie o arquivo `rickytech-blog.zip`.
3. Clique em **Ativar**.

> Se preferir FTP/SSH: copie a pasta `rickytech-blog/` para `wp-content/themes/` e ative.

## Configuração recomendada (5 minutos)

1. **Página inicial** — `Configurações → Leitura`:
   - A *home page* (`rickytech.com.br`) já mostra o design do blog automaticamente
     (via `front-page.php`). Você pode deixar "Sua página inicial exibe" como
     **Seus posts mais recentes** ou como **Uma página estática** — em ambos os casos
     a home renderiza a Central de Artigos.
   - A página **Blog** (posts page) também usa o mesmo layout.
2. **Menu** — `Aparência → Menus`: crie um menu e atribua a **Menu principal**.
   Sugestão: Início, IA, Engenharia, Cloud, Sobre, Contato. Sem menu, o tema mostra
   automaticamente "Início" + as categorias mais usadas.
3. **Logo** — `Aparência → Personalizar → Identidade do site`: envie um logo se quiser
   (senão usa o mark "RT" embutido).

## Como o tema mapeia o seu conteúdo

| Elemento do design            | De onde vem no WordPress |
|-------------------------------|--------------------------|
| Artigo em destaque (hero)     | Post mais recente com a **tag `Destaque`** (fallback: post fixo, depois o mais recente) |
| Cor de cada categoria         | Gerada de forma estável a partir do *slug* da categoria (paleta da marca). Funciona com "Categoria 1-4" ou nomes renomeados |
| Thumbnail do card             | **Imagem destacada** do post, se houver; senão uma arte generativa por canvas |
| Tempo de leitura              | Calculado do conteúdo (~200 palavras/min) |
| "Mais lidos" (sidebar)        | Posts com mais comentários (proxy de popularidade) |
| Avatar do autor               | Iniciais + cor derivada do nome (sem foto externa) |
| Índice do artigo (TOC)        | Gerado dos `H2` do conteúdo, via JS |
| Botão "Assinar" / Newsletter  | Aponta para a página de slug `newsletter` ou `contato`, se existir |

## Templates incluídos

`front-page.php` · `home.php` · `single.php` · `category.php` · `archive.php` ·
`author.php` · `search.php` · `page.php` · `404.php` · `index.php` · `comments.php`
e *template-parts* (`card`, `feed-item`, `featured-hero`, `blog-landing`).

## Personalização

- **Cores / tipografia**: `assets/css/colors_and_type.css` (tokens da marca).
- **Layout / componentes**: `assets/css/blog.css`.
- **Paleta de categorias e glifos**: função `rickytech_palette()` em `inc/helpers.php`.
- **Lógica de destaque, tempo de leitura, avatar, ícones, thumbnails**: `inc/helpers.php`.

## Notas

- Tema **clássico** (PHP templates) — sem build step, sem dependências.
- As fontes vêm do Google Fonts via `@import` no CSS. Para servir offline, baixe os
  `.woff2` e ajuste o CSS.
- Tradução: text domain `rickytech` (strings prontas para `.po/.mo` em `/languages`).
