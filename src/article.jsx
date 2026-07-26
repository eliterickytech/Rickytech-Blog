// article.jsx — Article reading view: progress bar, TOC, prose, code, related.
const { useState: useStateAr, useEffect: useEffectAr, useRef: useRefAr } = React;

function CodeBlock({ block }) {
  const dots = ["#ff5f57", "#febc2e", "#28c840"];
  const [copied, setCopied] = useStateAr(false);
  const plain = block.tokens.map(t => t[0] === "br" ? "\n" : t[1]).join("");
  return (
    <div className="codeblock">
      <div className="cb-head">
        {dots.map((c, i) => <span key={i} className="cb-dot" style={{ background: c }}></span>)}
        <span className="cb-lang">{block.title || block.lang}</span>
        <span style={{ cursor: "pointer", color: "#8a8aa0", display: "inline-flex", marginLeft: 10 }}
              onClick={() => { navigator.clipboard && navigator.clipboard.writeText(plain); setCopied(true); setTimeout(() => setCopied(false), 1200); }}
              title="Copiar">
          {copied ? <Icon name="check" size={14}/> : <Icon name="link" size={14}/>}
        </span>
      </div>
      <pre><code>{block.tokens.map((t, i) => t[0] === "br" ? <br key={i}/> : <span key={i} className={t[0]}>{t[1]}</span>)}</code></pre>
    </div>
  );
}

function Prose({ body, slug }) {
  return (
    <div className="prose">
      {body.map((b, i) => {
        switch (b.type) {
          case "p": return <p key={i} dangerouslySetInnerHTML={{ __html: b.html }}></p>;
          case "h2": return <h2 key={i} id={b.id}>{b.text}</h2>;
          case "h3": return <h3 key={i} id={b.id}>{b.text}</h3>;
          case "ul": return <ul key={i}>{b.items.map((it, j) => <li key={j}>{it}</li>)}</ul>;
          case "ol": return <ol key={i}>{b.items.map((it, j) => <li key={j}>{it}</li>)}</ol>;
          case "quote": return <blockquote key={i} className={b.pull ? "pull" : ""}>{b.text}</blockquote>;
          case "code": return <CodeBlock key={i} block={b}/>;
          default: return null;
        }
      })}
    </div>
  );
}

function ArticleView({ slug, go, saved, onToggleSave }) {
  const a = ARTICLES.find(x => x.slug === slug) || ARTICLES[0];
  const cat = CATS[a.cat], au = AUTHORS[a.author];
  const [progress, setProgress] = useStateAr(0);
  const [activeH, setActiveH] = useStateAr("");
  const [liked, setLiked] = useStateAr(false);
  const [copied, setCopied] = useStateAr(false);
  const bodyRef = useRefAr(null);

  const headings = a.body.filter(b => b.type === "h2");
  const isSaved = saved && saved.includes(a.slug);

  useEffectAr(() => {
    window.scrollTo(0, 0);
    const onScroll = () => {
      const el = bodyRef.current; if (!el) return;
      const total = el.offsetHeight - window.innerHeight + 200;
      const scrolled = window.scrollY - el.offsetTop + 200;
      setProgress(Math.max(0, Math.min(100, (scrolled / total) * 100)));
      // active heading
      let cur = "";
      for (const h of headings) {
        const he = document.getElementById(h.id);
        if (he && he.getBoundingClientRect().top < 120) cur = h.id;
      }
      setActiveH(cur);
    };
    window.addEventListener("scroll", onScroll, { passive: true }); onScroll();
    return () => window.removeEventListener("scroll", onScroll);
  }, [slug]);

  const related = ARTICLES.filter(x => x.slug !== a.slug && (x.cat === a.cat || x.author === a.author)).slice(0, 3);
  const goHeading = (id) => { const el = document.getElementById(id); if (el) window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 80, behavior: "smooth" }); };
  const doCopyLink = () => { navigator.clipboard && navigator.clipboard.writeText(location.href.split("#")[0] + "#/article/" + a.slug); setCopied(true); setTimeout(() => setCopied(false), 1400); };

  return (
    <div className="view-enter" style={{ "--cat-ink": cat.ink }}>
      <div className="read-progress" style={{ width: progress + "%" }}></div>

      <div className="container-read">
        <div className="art-hero">
          <span className="art-back" onClick={() => go({ view: "home" })}><Icon name="arrowLeft" size={15}/> Voltar</span>
          <div>
            <span className="art-cat" onClick={() => go({ view: "category", arg: a.cat })} style={{ cursor: "pointer" }}>{cat.name}</span>
          </div>
          <h1 className="art-title">{a.title}</h1>
          <p className="art-lede">{a.lede}</p>
          <div className="art-byline">
            <Avatar author={au} size={46} className="" />
            <div onClick={() => go({ view: "author", arg: au.id })}>
              <div className="who">{au.name}</div>
              <div className="sub">{au.role}</div>
            </div>
            <div className="bsep"></div>
            <div>
              <div className="sub" style={{ marginBottom: 2 }}>{fmtDate(a.date)}</div>
              <div className="sub" style={{ display: "inline-flex", alignItems: "center", gap: 5 }}><Icon name="clock" size={13}/> {a.read} min de leitura</div>
            </div>
            <div className="art-actions">
              <button className="icon-btn" title="Salvar" onClick={() => onToggleSave && onToggleSave(a.slug)} style={isSaved ? { color: "var(--accent)", borderColor: "var(--accent)" } : null}>
                <Icon name="bookmark" size={17} fill={isSaved}/>
              </button>
              <button className="icon-btn" title="Compartilhar" onClick={doCopyLink}>
                <Icon name={copied ? "check" : "share"} size={17}/>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div className="container-read">
        <div className="art-cover"><Thumb article={a} showCat={false}/></div>
      </div>

      <div className="art-layout">
        <aside className="toc">
          <div className="toc-lbl">Neste artigo</div>
          <ul>
            {headings.map(h => (
              <li key={h.id}><a className={activeH === h.id ? "active" : ""} onClick={() => goHeading(h.id)}>{h.text}</a></li>
            ))}
          </ul>
        </aside>

        <main ref={bodyRef}>
          <Prose body={a.body} slug={a.slug}/>
        </main>

        <aside className="art-rail">
          <div className={`rail-btn ${liked ? "on" : ""}`} onClick={() => setLiked(v => !v)}>
            <Icon name="heart" size={17} fill={liked}/> <span>{liked ? "124" : "123"}</span>
          </div>
          <div className={`rail-btn ${isSaved ? "on" : ""}`} onClick={() => onToggleSave && onToggleSave(a.slug)}>
            <Icon name="bookmark" size={17} fill={isSaved}/> <span>Salvar</span>
          </div>
          <div className="rail-btn" onClick={doCopyLink}>
            <Icon name={copied ? "check" : "link"} size={17}/> <span>{copied ? "Copiado" : "Link"}</span>
          </div>
          <div className="rail-btn"><Icon name="twitter" size={17}/> <span>Postar</span></div>
        </aside>
      </div>

      <div className="art-foot">
        <div className="tag-row">
          {a.tags.map(t => <span key={t} className="chip" onClick={() => go({ view: "search", arg: t })}>#{t}</span>)}
        </div>
        <div className="author-box">
          <Avatar author={au} size={64} className=""/>
          <div>
            <h3 className="ab-name" onClick={() => go({ view: "author", arg: au.id })}>{au.name}</h3>
            <div className="ab-role">{au.role} · {au.x}</div>
            <p className="ab-bio">{au.bio}</p>
          </div>
        </div>
      </div>

      <div className="related">
        <div className="container-wide">
          <div className="sec-head"><h2>Continue lendo</h2><a className="see-all" onClick={() => go({ view: "category", arg: a.cat })}>mais de {cat.short} →</a></div>
          <div className="card-grid">
            {related.map(r => <ArticleCard key={r.slug} a={r} go={go}/>)}
          </div>
        </div>
      </div>
    </div>
  );
}

Object.assign(window, { ArticleView });
