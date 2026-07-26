// app-headless.jsx — versão do App que carrega dados do WordPress via API.
// Substitui o app.jsx original. Os componentes (home.jsx, article.jsx, etc.)
// continuam EXATAMENTE iguais — só o carregamento de dados muda aqui.

const { useState: useStateApp, useEffect: useEffectApp, useCallback, createContext, useContext } = React;

// Contexto global de artigos (evita re-fetch em cada view)
const ArticlesCtx = createContext({ articles: [], loading: true });
function useArticles() { return useContext(ArticlesCtx); }

const TWEAK_DEFAULTS = {
  theme: 'dark', accent: 'violet', density: 'regular', cards: 'image', readFont: 'sans'
};
const ACCENTS = [
  { id: 'violet',  color: '#7c5cff' },
  { id: 'cyan',    color: '#22d3ee' },
  { id: 'emerald', color: '#10b981' },
  { id: 'amber',   color: '#f59e0b' },
  { id: 'rose',    color: '#f43f5e' },
];

function parseHash() {
  const h = location.hash.replace(/^#\/?/, '');
  if (!h) return { view: 'home' };
  const [view, arg] = h.split('/');
  const valid = ['home', 'article', 'category', 'author', 'search', 'newsletter'];
  if (!valid.includes(view)) return { view: 'home' };
  return { view, arg: arg ? decodeURIComponent(arg) : undefined };
}
function routeToHash(r) {
  if (r.view === 'home') return '#/';
  return `#/${r.view}${r.arg !== undefined ? '/' + encodeURIComponent(r.arg) : ''}`;
}

// Loading skeleton
function Skeleton() {
  return (
    <div style={{ padding: '80px var(--gutter)', maxWidth: 1320, margin: '0 auto' }}>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 28 }}>
        {[1,2,3].map(i => (
          <div key={i} style={{ background: 'var(--bg-card)', borderRadius: 12, height: 280, animation: 'pulse 1.5s ease-in-out infinite alternate', opacity: 0.6 }}/>
        ))}
      </div>
      <style>{`@keyframes pulse { from{opacity:.4} to{opacity:.7} }`}</style>
    </div>
  );
}

function App() {
  const [t, setTweak] = useTweaks(TWEAK_DEFAULTS);
  const [route, setRoute]       = useStateApp(parseHash());
  const [searchOpen, setSearchOpen] = useStateApp(false);
  const [saved, setSaved] = useStateApp(() => {
    try { return JSON.parse(localStorage.getItem('ca_saved') || '[]'); } catch { return []; }
  });

  // Dados vindos da API
  const [articles, setArticles] = useStateApp([]);
  const [loading, setLoading]   = useStateApp(true);

  // Carrega todos os posts na montagem (para busca e filtros client-side)
  useEffectApp(() => {
    fetchPosts({ perPage: 50 })
      .then(posts => {
        setArticles(posts);
        // Compatibilidade: expõe ARTICLES globalmente como data.jsx fazia
        window.ARTICLES = posts;
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, []);

  // Aplica tweaks no root
  useEffectApp(() => {
    const r = document.documentElement;
    r.setAttribute('data-theme',   t.theme);
    r.setAttribute('data-accent',  t.accent);
    r.setAttribute('data-density', t.density);
    r.setAttribute('data-cards',   t.cards);
    r.style.setProperty('--read-font', t.readFont === 'serif' ? '"Newsreader", Georgia, serif' : 'var(--font-sans)');
    r.style.setProperty('--read-size', t.readFont === 'serif' ? '20px' : '18px');
  }, [t.theme, t.accent, t.density, t.cards, t.readFont]);

  // Hash sync
  useEffectApp(() => {
    const onHash = () => setRoute(parseHash());
    window.addEventListener('hashchange', onHash);
    return () => window.removeEventListener('hashchange', onHash);
  }, []);

  const go = useCallback((r) => {
    const target = routeToHash(r);
    if (location.hash !== target) location.hash = target;
    else setRoute(r);
    window.scrollTo(0, 0);
  }, []);

  // ⌘K / Ctrl+K
  useEffectApp(() => {
    const onKey = (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); setSearchOpen(true); }
      else if (e.key === '/' && !/INPUT|TEXTAREA/.test(document.activeElement.tagName)) { e.preventDefault(); setSearchOpen(true); }
    };
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, []);

  const toggleSave = useCallback((slug) => {
    setSaved(prev => {
      const next = prev.includes(slug) ? prev.filter(s => s !== slug) : [...prev, slug];
      try { localStorage.setItem('ca_saved', JSON.stringify(next)); } catch {}
      return next;
    });
  }, []);

  const toggleTheme = () => setTweak('theme', t.theme === 'dark' ? 'light' : 'dark');

  if (loading) return <Skeleton/>;

  let view;
  switch (route.view) {
    case 'article':    view = <ArticleView    slug={route.arg} go={go} saved={saved} onToggleSave={toggleSave}/>; break;
    case 'category':   view = <CategoryView   catId={route.arg} go={go}/>; break;
    case 'author':     view = <AuthorView     authorId={route.arg} go={go}/>; break;
    case 'search':     view = <SearchView     query={route.arg} go={go}/>; break;
    case 'newsletter': view = <NewsletterView go={go}/>; break;
    default:           view = <HomeView       go={go} onSubscribe={() => go({ view: 'newsletter' })}/>;
  }

  return (
    <React.Fragment>
      <Topbar route={route} go={go} theme={t.theme} onToggleTheme={toggleTheme} onOpenSearch={() => setSearchOpen(true)}/>
      <main key={route.view + (route.arg || '')}>{view}</main>
      <Footer go={go}/>
      <SearchOverlay open={searchOpen} onClose={() => setSearchOpen(false)} go={go}/>

      <TweaksPanel title="Tweaks">
        <TweakSection label="Aparência"/>
        <TweakRadio label="Tema" value={t.theme} options={['dark','light']} onChange={(v) => setTweak('theme', v)}/>
        <TweakColor label="Cor de destaque" value={ACCENTS.find(a => a.id === t.accent).color}
          options={ACCENTS.map(a => a.color)}
          onChange={(color) => setTweak('accent', ACCENTS.find(a => a.color === color).id)}/>
        <TweakSection label="Cards"/>
        <TweakRadio label="Densidade" value={t.density} options={['compact','regular','comfy']} onChange={(v) => setTweak('density', v)}/>
        <TweakRadio label="Estilo" value={t.cards} options={['image','text']} onChange={(v) => setTweak('cards', v)}/>
        <TweakSection label="Leitura"/>
        <TweakRadio label="Fonte do artigo" value={t.readFont} options={['sans','serif']} onChange={(v) => setTweak('readFont', v)}/>
      </TweaksPanel>
    </React.Fragment>
  );
}

ReactDOM.createRoot(document.getElementById('root')).render(<App/>);
