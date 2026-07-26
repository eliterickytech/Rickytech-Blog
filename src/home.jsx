// home.jsx — Home view + reusable ArticleCard / FeedItem / PopularItem.
const { useState: useStateHm, useMemo: useMemoHm } = React;

function ArticleCard({ a, go }) {
  const cat = CATS[a.cat], au = AUTHORS[a.author];
  return (
    <article className="acard" style={{ "--cat-ink": cat.ink }} onClick={() => go({ view: "article", arg: a.slug })}>
      <Thumb article={a}/>
      <div className="cat-bar"></div>
      <div className="meta-row">
        <span className="cat-tag" onClick={(e) => { e.stopPropagation(); go({ view: "category", arg: a.cat }); }} style={{ cursor: "pointer" }}>{cat.short}</span>
        <span className="dot-sep"></span>
        <span>{a.read} min</span>
        <span className="dot-sep"></span>
        <span>{relDate(a.date)}</span>
      </div>
      <h3>{a.title}</h3>
      <p>{a.lede}</p>
      <div className="byline">
        <Avatar author={au} size={24}/>
        <span className="name">{au.name}</span>
      </div>
    </article>
  );
}

function FeedItem({ a, go }) {
  const cat = CATS[a.cat], au = AUTHORS[a.author];
  return (
    <article className="feed-item" style={{ "--cat-ink": cat.ink }} onClick={() => go({ view: "article", arg: a.slug })}>
      <Thumb article={a}/>
      <div className="fi-body">
        <div className="meta-row">
          <span className="cat-tag">{cat.short}</span>
          <span className="dot-sep"></span>
          <span>{a.read} min de leitura</span>
        </div>
        <h3>{a.title}</h3>
        <p>{a.lede}</p>
        <div className="byline">
          <Avatar author={au} size={22}/>
          <span className="name">{au.name}</span>
          <span className="dot-sep"></span>
          <span className="name">{relDate(a.date)}</span>
        </div>
      </div>
    </article>
  );
}

function PopularList({ go }) {
  const top = [...ARTICLES].sort((x, y) => x.pop - y.pop).slice(0, 5);
  return (
    <div className="side-block">
      <div className="side-title">Mais lidos</div>
      {top.map((a, i) => (
        <div key={a.slug} className="pop-item" onClick={() => go({ view: "article", arg: a.slug })}>
          <span className="rank">{String(i + 1).padStart(2, "0")}</span>
          <div className="pop-body">
            <h4>{a.title}</h4>
            <div className="pm">{CATS[a.cat].short} · {a.read} min</div>
          </div>
        </div>
      ))}
    </div>
  );
}

function CategoryMini({ go, current }) {
  return (
    <div className="side-block">
      <div className="side-title">Explorar temas</div>
      <div className="cat-list">
        {CAT_ORDER.map(id => (
          <div key={id} className="cat-row" onClick={() => go({ view: "category", arg: id })}>
            <span className="swatch" style={{ background: CATS[id].ink }}></span>
            <span className="cn">{CATS[id].name}</span>
            <span className="cc">{catCount(id)}</span>
          </div>
        ))}
      </div>
    </div>
  );
}

function SideNewsletter({ go, onSubscribe }) {
  const [email, setEmail] = useStateHm("");
  return (
    <div className="side-cta">
      <div className="glow"></div>
      <h4>Receba os melhores artigos</h4>
      <p>Um e-mail por semana com o que importa em IA e engenharia. Sem ruído.</p>
      <form className="mini-form" onSubmit={(e) => { e.preventDefault(); onSubscribe ? onSubscribe(email) : go({ view: "newsletter" }); }}>
        <input className="input" type="email" placeholder="seu@email.com" value={email} onChange={(e) => setEmail(e.target.value)} required/>
        <button className="btn btn-primary" type="submit" style={{ justifyContent: "center" }}>Assinar grátis</button>
      </form>
    </div>
  );
}

function FeaturedHero({ a, go }) {
  const cat = CATS[a.cat], au = AUTHORS[a.author];
  return (
    <section className="feat" style={{ "--cat-ink": cat.ink }}>
      <div className="thumb-wrap" onClick={() => go({ view: "article", arg: a.slug })} style={{ cursor: "pointer" }}>
        <Thumb article={a}/>
      </div>
      <div>
        <span className="eyebrow-pill"><Icon name="bolt" size={13} fill={true}/> Em destaque</span>
        <h1 onClick={() => go({ view: "article", arg: a.slug })} style={{ cursor: "pointer" }}>{a.title}</h1>
        <p className="lede">{a.lede}</p>
        <div className="feat-foot">
          <Avatar author={au} size={38}/>
          <div>
            <div className="who">{au.name}</div>
            <div className="when">{fmtDate(a.date)} · {a.read} min de leitura</div>
          </div>
          <button className="btn btn-ghost btn-sm" style={{ marginLeft: "auto" }} onClick={() => go({ view: "article", arg: a.slug })}>
            Ler artigo <Icon name="arrowRight" size={15}/>
          </button>
        </div>
      </div>
    </section>
  );
}

function HomeView({ go, onSubscribe }) {
  const [filter, setFilter] = useStateHm("all");
  const featured = ARTICLES.find(a => a.featured);
  const pool = useMemoHm(() => {
    const rest = ARTICLES.filter(a => a.slug !== featured.slug);
    return filter === "all" ? rest : rest.filter(a => a.cat === filter);
  }, [filter, featured]);
  const grid = pool.slice(0, 6);
  const feed = pool.slice(6, 12);

  return (
    <div className="view-enter">
      <div className="container-wide">
        <FeaturedHero a={featured} go={go}/>
      </div>

      <div className="container-wide" style={{ paddingTop: 28 }}>
        <div className="chips" style={{ marginBottom: 8 }}>
          <button className={`chip ${filter === "all" ? "on" : ""}`} onClick={() => setFilter("all")}>Todos</button>
          {CAT_ORDER.map(id => (
            <button key={id} className={`chip ${filter === id ? "on" : ""}`} onClick={() => setFilter(id)}>{CATS[id].short}</button>
          ))}
        </div>
      </div>

      <div className="container-wide">
        <div className="sec" style={{ paddingBottom: 24 }}>
          <div className="home-cols">
            <div>
              <div className="sec-head">
                <h2>{filter === "all" ? "Mais recentes" : CATS[filter].name}</h2>
                {filter !== "all" && <a className="see-all" onClick={() => go({ view: "category", arg: filter })}>ver tudo →</a>}
              </div>
              <div className="card-grid">
                {grid.map(a => <ArticleCard key={a.slug} a={a} go={go}/>)}
              </div>

              {feed.length > 0 && (
                <div style={{ marginTop: 56 }}>
                  <div className="sec-head"><h2>Continue lendo</h2></div>
                  <div className="feed">
                    {feed.map(a => <FeedItem key={a.slug} a={a} go={go}/>)}
                  </div>
                </div>
              )}
            </div>

            <aside className="sidebar">
              <PopularList go={go}/>
              <CategoryMini go={go}/>
              <SideNewsletter go={go} onSubscribe={onSubscribe}/>
            </aside>
          </div>
        </div>
      </div>
    </div>
  );
}

Object.assign(window, { HomeView, ArticleCard, FeedItem, PopularList, CategoryMini, SideNewsletter });
