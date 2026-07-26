// pages.jsx — Category, Author, Search results, Newsletter views.
const { useState: useStatePg, useMemo: useMemoPg, useEffect: useEffectPg } = React;

function CategoryView({ catId, go }) {
  const cat = CATS[catId] || CATS.ia;
  const [sort, setSort] = useStatePg("recent");
  const list = useMemoPg(() => {
    const arr = ARTICLES.filter(a => a.cat === cat.id);
    return sort === "recent" ? [...arr].sort((a, b) => b.date.localeCompare(a.date)) : [...arr].sort((a, b) => a.pop - b.pop);
  }, [cat.id, sort]);
  useEffectPg(() => window.scrollTo(0, 0), [catId]);

  return (
    <div className="view-enter">
      <div className="cat-hero" style={{ background: `linear-gradient(180deg, ${cat.deep}55, transparent)` }}>
        <div className="container-wide">
          <span className="cat-badge" style={{ background: `color-mix(in oklch, ${cat.ink} 16%, transparent)`, color: cat.ink, border: `1px solid color-mix(in oklch, ${cat.ink} 30%, transparent)` }}>
            {cat.glyph} {cat.short}
          </span>
          <h1>{cat.name}</h1>
          <p>{cat.blurb}</p>
          <div style={{ marginTop: 18, fontFamily: "var(--font-mono)", fontSize: 13, color: "var(--fg-tertiary)" }}>{list.length} artigos</div>
        </div>
      </div>

      <div className="container-wide">
        <div className="sec">
          <div className="sec-head">
            <div className="chips">
              <button className={`chip ${sort === "recent" ? "on" : ""}`} onClick={() => setSort("recent")}>Recentes</button>
              <button className={`chip ${sort === "popular" ? "on" : ""}`} onClick={() => setSort("popular")}>Mais lidos</button>
            </div>
          </div>
          <div className="card-grid">
            {list.map(a => <ArticleCard key={a.slug} a={a} go={go}/>)}
          </div>
        </div>
      </div>
    </div>
  );
}

function AuthorView({ authorId, go }) {
  const au = AUTHORS[authorId] || AUTHORS.ricky;
  const list = ARTICLES.filter(a => a.author === au.id).sort((a, b) => b.date.localeCompare(a.date));
  useEffectPg(() => window.scrollTo(0, 0), [authorId]);
  return (
    <div className="view-enter">
      <div className="container-wide">
        <div className="author-hero">
          <Avatar author={au} size={96}/>
          <div>
            <h1>{au.name}</h1>
            <div className="role">{au.role}</div>
            <p className="bio">{au.bio}</p>
            <div className="author-stats">
              <div className="st"><span className="n">{au.articles}</span><div className="l">artigos</div></div>
              <div className="st"><span className="n">{list.length}</span><div className="l">na central</div></div>
              <div className="st"><span className="n">desde {au.since}</span><div className="l">{au.x}</div></div>
            </div>
          </div>
          <div style={{ marginLeft: "auto", display: "flex", gap: 8 }}>
            <button className="btn btn-ghost btn-sm"><Icon name="twitter" size={15}/> Seguir</button>
          </div>
        </div>
      </div>
      <div className="container-wide">
        <div className="sec">
          <div className="sec-head"><h2>Artigos de {au.name.split(" ")[0]}</h2></div>
          <div className="card-grid">
            {list.map(a => <ArticleCard key={a.slug} a={a} go={go}/>)}
          </div>
        </div>
      </div>
    </div>
  );
}

function SearchView({ query, go }) {
  const [q, setQ] = useStatePg(query || "");
  useEffectPg(() => { setQ(query || ""); window.scrollTo(0, 0); }, [query]);
  const term = q.trim().toLowerCase();
  const results = useMemoPg(() => {
    if (!term) return [];
    return ARTICLES.filter(a =>
      a.title.toLowerCase().includes(term) ||
      a.lede.toLowerCase().includes(term) ||
      a.tags.some(t => t.toLowerCase().includes(term)) ||
      CATS[a.cat].name.toLowerCase().includes(term) ||
      AUTHORS[a.author].name.toLowerCase().includes(term)
    );
  }, [term]);

  return (
    <div className="view-enter">
      <div className="container-wide">
        <div className="sec" style={{ paddingBottom: 24 }}>
          <div className="eyebrow-lbl" style={{ marginBottom: 14 }}>Busca</div>
          <div style={{ position: "relative", maxWidth: 640 }}>
            <span style={{ position: "absolute", left: 16, top: "50%", transform: "translateY(-50%)", color: "var(--fg-tertiary)" }}><Icon name="search" size={20}/></span>
            <input className="input" style={{ height: 54, paddingLeft: 48, fontSize: 17 }} autoFocus value={q}
                   onChange={(e) => setQ(e.target.value)} placeholder="Buscar por título, tema, tag ou autor…"/>
          </div>
          <div style={{ marginTop: 14, fontFamily: "var(--font-mono)", fontSize: 13, color: "var(--fg-tertiary)" }}>
            {term ? `${results.length} resultado${results.length === 1 ? "" : "s"} para “${q}”` : "Digite para buscar entre os artigos"}
          </div>
        </div>

        {!term && (
          <div className="sec" style={{ paddingTop: 0 }}>
            <div className="eyebrow-lbl" style={{ marginBottom: 14 }}>Buscas populares</div>
            <div className="chips">
              {["Agentes", "RAG", "Performance", "Observabilidade", ".NET", "Carreira", "CSS"].map(t => (
                <button key={t} className="chip" onClick={() => setQ(t)}>{t}</button>
              ))}
            </div>
          </div>
        )}

        {term && (
          <div className="sec" style={{ paddingTop: 0 }}>
            {results.length === 0
              ? <div style={{ padding: "60px 0", textAlign: "center", color: "var(--fg-tertiary)" }}>Nenhum artigo encontrado. Tente outro termo.</div>
              : <div className="card-grid">{results.map(a => <ArticleCard key={a.slug} a={a} go={go}/>)}</div>}
          </div>
        )}
      </div>
    </div>
  );
}

function NewsletterView({ go, presetDone }) {
  const [email, setEmail] = useStatePg("");
  const [done, setDone] = useStatePg(!!presetDone);
  useEffectPg(() => window.scrollTo(0, 0), []);
  return (
    <div className="view-enter">
      <div className="nl-hero">
        <div className="nl-aurora"></div>
        <div className="container">
          <div className="nl-card">
            {!done ? (
              <React.Fragment>
                <span className="badge"><Icon name="rss" size={13}/> Newsletter semanal</span>
                <h1>O essencial de IA e engenharia, toda terça</h1>
                <p>Curadoria sem ruído: os artigos, ferramentas e ideias que valem o seu tempo. Junte-se a <strong>9.400+</strong> devs que constroem software de verdade.</p>
                <form className="nl-form" onSubmit={(e) => { e.preventDefault(); setDone(true); }}>
                  <input className="input" type="email" placeholder="seu@email.com" value={email} onChange={(e) => setEmail(e.target.value)} required/>
                  <button className="btn btn-primary" type="submit">Assinar grátis</button>
                </form>
                <div className="nl-meta">Sem spam. Cancele quando quiser.</div>
              </React.Fragment>
            ) : (
              <div className="nl-success">
                <div className="check"><Icon name="check" size={28}/></div>
                <h1 style={{ margin: 0 }}>Inscrição confirmada!</h1>
                <p style={{ margin: 0 }}>Bem-vindo à Central. O próximo número chega na terça — enquanto isso, que tal um artigo?</p>
                <button className="btn btn-primary" onClick={() => go({ view: "home" })} style={{ marginTop: 6 }}>Explorar artigos <Icon name="arrowRight" size={15}/></button>
              </div>
            )}
          </div>
          {!done && (
            <div className="nl-feats">
              <div className="nl-feat"><Icon name="check" size={16}/> 1 e-mail por semana</div>
              <div className="nl-feat"><Icon name="check" size={16}/> Conteúdo aprofundado</div>
              <div className="nl-feat"><Icon name="check" size={16}/> Zero anúncios</div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

Object.assign(window, { CategoryView, AuthorView, SearchView, NewsletterView });
