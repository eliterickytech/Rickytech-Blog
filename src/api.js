// api.js — substitui data.jsx estático, busca dados do WordPress REST API
// Troque WP_BASE_URL pela URL do seu WordPress no HostGator.

const WP_BASE_URL = 'https://seusite.com.br/wp-json/wp/v2';

// Configuração de categorias (igual ao functions.php — mantida aqui para
// o frontend poder usar cores/glyphs sem precisar de chamada extra)
const CATS = {
  ia:       { id: 'ia',       name: 'IA & Machine Learning',    short: 'IA',        ink: '#8b6dff', deep: '#1a1340', glyph: '∑',   blurb: 'Modelos, LLMs, agentes e a engenharia por trás da inteligência artificial aplicada.' },
  eng:      { id: 'eng',      name: 'Engenharia de Software',   short: 'Engenharia', ink: '#22d3ee', deep: '#0a2b33', glyph: '{}',  blurb: 'Arquitetura, design de sistemas, qualidade e as práticas que sustentam software de produção.' },
  cloud:    { id: 'cloud',    name: 'Cloud & DevOps',           short: 'Cloud',     ink: '#34d399', deep: '#06281d', glyph: '☁',   blurb: 'Infraestrutura, observabilidade, CI/CD e operação de sistemas em escala.' },
  backend:  { id: 'backend',  name: '.NET & Backend',           short: 'Backend',   ink: '#fbbf24', deep: '#2e2206', glyph: 'λ',   blurb: 'APIs, performance, dados e o ecossistema .NET para back-end robusto.' },
  front:    { id: 'front',    name: 'Frontend & Web',           short: 'Frontend',  ink: '#fb7185', deep: '#33101a', glyph: '</>', blurb: 'Interfaces, performance no browser, design systems e a plataforma web moderna.' },
  carreira: { id: 'carreira', name: 'Carreira & Produtividade', short: 'Carreira',  ink: '#60a5fa', deep: '#0d1f3d', glyph: '↗',   blurb: 'Crescimento, liderança técnica e hábitos para quem constrói software.' },
};
const CAT_ORDER = ['ia', 'eng', 'cloud', 'backend', 'front', 'carreira'];

// Mapeia o slug do WP para a config de categoria
function catConfigFromSlug(slug) {
  return CATS[slug] ?? { id: slug, name: slug, short: slug, ink: '#7c5cff', deep: '#1a1340', glyph: '∑', blurb: '' };
}

// Converte um post do WP REST API para o formato que os componentes React esperam
function wpPostToArticle(wp) {
  const catCfg = wp.rt_cat_config ?? catConfigFromSlug('ia');
  const author = wp.rt_author_data ?? {};
  return {
    slug:     wp.slug,
    title:    wp.title?.rendered ?? wp.title ?? '',
    lede:     wp.rt_lede ?? '',
    date:     wp.date?.split('T')[0] ?? '',
    read:     wp.rt_read_time ?? 5,
    featured: wp.rt_featured ?? false,
    tags:     (wp.tags_labels ?? []),
    cat:      catCfg.id ?? catCfg.slug ?? 'ia',
    author:   author.id ?? wp.author,
    pop:      wp.comment_count ?? 99,
    // Dados extras já expandidos para uso direto nos componentes
    _catCfg:  catCfg,
    _author:  author,
    body:     [], // conteúdo rico fica no WP, não carregado nos cards
    _wpContent: wp.content?.rendered ?? '', // HTML completo para a view de artigo
  };
}

// ── Funções públicas de fetch ───────────────────────────────────────────────

// Busca lista de posts (para home, categoria, busca)
async function fetchPosts({ perPage = 20, catSlug = null, search = null, page = 1 } = {}) {
  const params = new URLSearchParams({
    per_page: perPage,
    page,
    _embed: 1,
  });
  if (catSlug) {
    // Precisamos do term_id; busca a categoria primeiro
    const catRes = await fetch(`${WP_BASE_URL}/categories?slug=${catSlug}&_fields=id`);
    const cats   = await catRes.json();
    if (cats[0]) params.set('categories', cats[0].id);
  }
  if (search) params.set('search', search);

  const res  = await fetch(`${WP_BASE_URL}/posts?${params}`);
  const data = await res.json();
  return Array.isArray(data) ? data.map(wpPostToArticle) : [];
}

// Busca um único post pelo slug
async function fetchPost(slug) {
  const res  = await fetch(`${WP_BASE_URL}/posts?slug=${encodeURIComponent(slug)}&_embed=1`);
  const data = await res.json();
  return data[0] ? wpPostToArticle(data[0]) : null;
}

// Busca o post em destaque (_featured = true)
async function fetchFeaturedPost() {
  // O WP REST API não filtra por meta nativo — usamos uma rota customizada (ver abaixo)
  // Fallback: pega o mais recente
  const res  = await fetch(`${WP_BASE_URL}/posts?meta_key=_featured&meta_value=1&per_page=1&_embed=1`);
  const data = await res.json();
  if (data[0]) return wpPostToArticle(data[0]);
  // Fallback: primeiro post
  const posts = await fetchPosts({ perPage: 1 });
  return posts[0] ?? null;
}

// Busca posts relacionados (mesma categoria, excluindo o atual)
async function fetchRelated(article, limit = 3) {
  const posts = await fetchPosts({ perPage: limit + 1, catSlug: article.cat });
  return posts.filter(p => p.slug !== article.slug).slice(0, limit);
}

// Expõe globalmente (como data.jsx fazia via Object.assign(window, ...))
Object.assign(window, {
  CATS,
  CAT_ORDER,
  catConfigFromSlug,
  fetchPosts,
  fetchPost,
  fetchFeaturedPost,
  fetchRelated,
});
