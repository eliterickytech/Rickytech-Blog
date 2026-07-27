---
name: publicar-post
description: >-
  Publica e edita artigos no blog WordPress da Rickytech (rickytech.com.br,
  "Central de Artigos") via REST API, com capa on-brand gerada automaticamente,
  categoria, tags, resumo e opção de fixar como notícia principal da home.
  Use SEMPRE que o usuário quiser criar, escrever, publicar, postar, subir,
  agendar ou editar um post/artigo/notícia do blog Rickytech — mesmo que ele só
  cole o texto e diga "publica isso" ou "cria um post sobre X". Também vale para
  ajustes num post existente (título, corpo, categoria, capa, destaque) e para
  promover um rascunho a publicado.
---

# Publicar post no blog Rickytech

Fluxo para publicar no blog **rickytech.com.br** (tema "Central de Artigos").
O padrão é **rascunho primeiro**: cria como rascunho, você mostra o preview ao
usuário e só publica de verdade após o "OK" dele. Isso evita mandar pro ar algo
com erro de formatação — reverter um post publicado é chato.

Detalhes de tema, categorias e HTML estão em
[`references/tema-e-api.md`](references/tema-e-api.md) — leia quando precisar do
detalhe. O essencial está aqui.

## Pré-requisitos
- Rode os scripts com o Python do projeto (tem `requests` + `Pillow`):
  `mcp-wordpress\.venv\Scripts\python.exe`
- Credenciais já ficam em `mcp-wordpress/.env` (os scripts carregam sozinhos).

## Pautar a partir de uma fonte
Quando o usuário quiser **tirar uma notícia/tema de uma fonte** ("pega algo do
Hugging Face e posta", "resume a última do .NET Blog"), consulte a curadoria em
[`references/fontes-de-conteudo.md`](references/fontes-de-conteudo.md) — são
blogs/portais por tema (IA, .NET, Banco de Dados, Frontend, DevOps), já com o
**mapeamento para a categoria do blog**. Fluxo: escolher a fonte → ler/buscar a
matéria (use WebFetch/WebSearch) → **resumir com as próprias palavras** →
formatar em HTML → seguir o passo a passo abaixo.

**Atribuição (importante):** escreva um resumo **original**, não copie trechos
do texto-fonte. Sempre **credite e linke a fonte** numa seção
`<h2>Fontes</h2>` ao final do post (com `rel="noopener nofollow"`). O valor do
post está na sua síntese e no contexto que você adiciona, não na cópia.

## Passo a passo

### 1. Reúna o que o post precisa
Colete (ou infira do que o usuário deu):
- **Título** — claro e específico.
- **Corpo** — o conteúdo. Você vai formatá-lo em HTML (próximo passo).
- **Categoria** — slug do blog (`ia`, `engenharia`, `cloud`, `backend`,
  `carreira`, `devops`, `frontend`, `noticias`, `seguranca`, `tutoriais`).
  Escolha a mais adequada ao tema; se estiver genuinamente ambíguo entre duas,
  pergunte. Não invente categoria que não existe.
- **Resumo (lede)** — 1–2 frases de chamada (vira o subtítulo na home e no
  topo do artigo). Se o usuário não deu, escreva um bom resumo você mesmo.
- **Tags** — 2–5 nomes, opcional (são criadas se não existirem).
- **Destaque?** — se o usuário quer isso como *notícia principal da home*.

### 2. Formate o corpo em HTML
Escreva o corpo em HTML semântico e salve num arquivo temporário `.html`.
Convenções que o tema espera (resumo — detalhe em `references/tema-e-api.md`):
- **`<h2>` para cada seção** — elas alimentam o índice lateral "Neste artigo".
- **`<h3>` para subtópicos** (ex.: cada pergunta do FAQ) — ficam fora do índice.
- **`<blockquote><p>…</p></blockquote>`** para frases de efeito/citações.
- **`<ul><li><strong>Termo.</strong> …</li></ul>** para listas de definição.
- Links externos com `rel="noopener nofollow"`; termos estrangeiros em `<em>`.
- **Sem `<h1>`** no corpo (o título já é o `<h1>` da página).
- Feche com `<h2>Perguntas frequentes</h2>` (opcional) e `<h2>Fontes</h2>` se
  houver referências.

### 3. Crie o rascunho (capa automática)
A capa on-brand 1200×675 é gerada e enviada sozinha — não precisa pedir imagem.

```
mcp-wordpress\.venv\Scripts\python.exe .claude\skills\publicar-post\scripts\publicar.py ^
  --titulo "TÍTULO" ^
  --corpo "CAMINHO\corpo.html" ^
  --categoria ia ^
  --tags "Tag A,Tag B" ^
  --resumo "Linha de chamada do post."
```

O script imprime o `id`, o **link de preview** e o **link de edição**. Se o
usuário quiser usar a própria imagem em vez da capa gerada, passe `--capa
caminho.png`; para deixar a thumb generativa do tema, passe `--sem-capa`.

**Fundo por IA (Nano Banana):** se o usuário gerou um fundo no app Gemini e
salvou em `.ia/capas/`, passe `--fundo .ia/capas/arquivo.png` — o script usa
essa imagem como fundo e **desenha o branding por cima** (logo, pill, título).
O prompt pronto e o passo a passo estão em `.ia/prompts/` (fora da skill, na
raiz do projeto).

### 4. Mostre o preview e espere o OK
Dê ao usuário o link de preview (e o de edição no wp-admin). Deixe claro que
ainda é **rascunho**. Não publique sem a confirmação dele — publicar é uma ação
pública e é ele quem aprova.

### 5. Publique (e fixe como destaque, se for o caso)
Com o "OK", promova o rascunho a publicado usando o `id` retornado. Adicione
`--destaque` para fixá-lo como notícia principal da home.

```
mcp-wordpress\.venv\Scripts\python.exe .claude\skills\publicar-post\scripts\publicar.py ^
  --update ID --status publish --destaque
```

### 6. Verifique no ar
Confirme que a URL do post responde 200 (não 404):

```
curl.exe -sS -o NUL -w "HTTP %{http_code}\n" -A "Mozilla/5.0" "URL_DO_POST"
```

Se der **404** mesmo publicado (a home abre, mas o post não), o `.htaccess` da
raiz perdeu o rewrite. Peça ao usuário: **WordPress → Configurações → Links
Permanentes → Salvar** — isso regenera o `.htaccess` e conserta todos os posts.
(O FTP do projeto é chroot no `wp-content`, então não dá pra editar esse
`.htaccess` por lá.) Ver `references/tema-e-api.md`.

## Editar um post existente
Passe `--update ID` com só os campos a mudar. Exemplos:
- Trocar o corpo: `--update 184 --corpo corpo_v2.html`
- Trocar a capa: `--update 184 --capa nova-capa.png`
- Tirar do destaque: republique sem `--destaque` **não** desmarca sozinho —
  para remover o sticky, use a REST API/`atualizar_post` com `sticky:false` ou
  o wp-admin.

## Observações
- **Nunca** coloque senha em código ou no post; as credenciais vivem só no
  `.env` (fora do git).
- Se o usuário pedir para publicar direto (sem rascunho), tudo bem — use
  `--status publish` já na criação. O rascunho-primeiro é o padrão seguro,
  não uma regra rígida.
- O idioma do blog é **português do Brasil**; escreva o conteúdo assim.
