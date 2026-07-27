# Tema Rickytech + API — mecânica e convenções

Referência de apoio para publicar no blog **rickytech.com.br** (tema
"Central de Artigos"). Leia quando precisar do detalhe; o SKILL.md cobre o fluxo.

## Índice
- [Acesso à API](#acesso-à-api)
- [Categorias](#categorias)
- [Como o tema decide a "notícia principal"](#como-o-tema-decide-a-notícia-principal)
- [Imagens](#imagens)
- [Convenções de HTML do corpo](#convenções-de-html-do-corpo)
- [Pegadinhas conhecidas](#pegadinhas-conhecidas)

## Acesso à API
- Credenciais em `mcp-wordpress/.env` (`WP_USER`, `WP_APP_PASSWORD` = senha de
  aplicativo, **não** a senha de login). Os scripts carregam sozinhos.
- Rode os scripts com o Python do projeto (tem `requests` + `Pillow`):
  `mcp-wordpress\.venv\Scripts\python.exe`
- A base é a forma `?rest_route=` (a URL bonita `/wp-json/` dá 404 na
  hospedagem). Já está no `.env`: `https://rickytech.com.br/index.php?rest_route=/wp/v2`

## Categorias
Resolva sempre por **slug** (os IDs podem mudar; `resolve_category` faz isso).
Slugs existentes: `ia`, `engenharia`, `cloud`, `backend`, `carreira`, `devops`,
`frontend`, `noticias`, `seguranca`, `tutoriais`. O tema pinta cada categoria
com uma cor derivada do slug — não precisa configurar cor.

## Como o tema decide a "notícia principal"
A função `rickytech_featured_post()` escolhe o hero da home nesta ordem:
1. Post mais recente com a tag de slug `destaque` (**hoje não usada** — a tag
   existente "Destaque" tem slug `featured`, então não conta).
2. **Post sticky** (é o mecanismo limpo e o que usamos: `--destaque` seta
   `sticky: true`).
3. Fallback: o post publicado mais recente.

Ou seja, para fixar um post na chamada principal, publique com `--destaque`.

## Imagens
- Com **imagem destacada**, ela aparece na home (hero) e no topo do artigo.
- Sem imagem destacada, o tema desenha uma **thumb generativa** on-brand no
  `<canvas>` (use `--sem-capa` se quiser esse visual).
- Tamanho ideal da capa: **1200×675 (16:9)**. O `gerar_capa.py` já produz nesse
  tamanho, com a identidade dark violeta→ciano.

## Convenções de HTML do corpo
O corpo é renderizado dentro de `.prose`. Regras que importam para o tema:
- **`<h2>` = seções** e alimentam o índice lateral "Neste artigo". Toda seção
  principal do artigo deve ser um `<h2>`.
- **`<h3>` = subtópicos** (ex.: cada pergunta do FAQ). Não entram no índice,
  então o FAQ fica limpo.
- **`<blockquote><p>…</p></blockquote>`** para citações/frases de efeito — o
  tema estiliza com a barra lateral violeta.
- **`<ul>/<li>`** com `<strong>Termo.</strong> explicação` para listas de
  definição (ex.: a matriz de categorias).
- Links externos: `<a href="…" target="_blank" rel="noopener nofollow">`.
- Termos estrangeiros/código em prosa: `<em>blind spot pass</em>`.
- Não inclua um `<h1>` no corpo — o título do post já vira o `<h1>` da página.
- Estrutura recomendada de artigo: parágrafos de abertura (sem heading) →
  seções `<h2>` → `<h2>Perguntas frequentes</h2>` com `<h3>` → `<h2>Fontes</h2>`
  com `<ul>` de links.

## Pegadinhas conhecidas
- **REST via `?rest_route=`** (não `/wp-json/`). Já tratado no `.env`.
- **Permalinks 404**: se um post publicado abrir a home mas a URL do post der
  404, o `.htaccess` da raiz perdeu as regras de rewrite. Conserto:
  WordPress → **Configurações → Links Permanentes → Salvar** (regenera o
  `.htaccess`). O FTP do projeto é chroot no `wp-content`, então não dá pra
  editar o `.htaccess` da raiz por lá.
- **User-Agent**: o ModSecurity da HostGator bloqueia o UA padrão do `requests`
  com HTTP 406. A sessão já usa um UA de navegador (`wp_common.make_session`).
- **Cache**: a home é cacheada (Endurance Page Cache). Publicar/atualizar
  normalmente limpa; se vir algo velho, limpe o cache no painel.
