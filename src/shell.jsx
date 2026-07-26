// shell.jsx — Topbar, Search overlay, Footer. Navigation via go() prop.
const { useState: useStateSh, useEffect: useEffectSh, useRef: useRefSh, useMemo: useMemoSh } = React;

function Topbar({ route, go, theme, onToggleTheme, onOpenSearch }) {
  const [scrolled, setScrolled] = useStateSh(false);
  useEffectSh(() => {
    const f = () => setScrolled(window.scrollY > 4);
    window.addEventListener("scroll", f, { passive: true }); f();
    return () => window.removeEventListener("scroll", f);
  }, []);
  const navItem = (view, label, arg) => {
    const active = route.view === view && (arg === undefined || route.arg === arg);
    return <a className={active ? "active" : ""} onClick={() => go({ view, arg })}>{label}</a>;
  };
  return (
    <header className="topbar" style={scrolled ? { boxShadow: "var(--elev-1)" } : null}>
      <div className="topbar-inner">
        <div className="brand" onClick={() => go({ view: "home" })}>
          <img className="mark" src="../assets/logos/ricky-mark-transparent.png" alt="Rickytech"/>
          <span className="wm">Central de <span className="accent">Artigos</span></span>
        </div>
        <nav className="topnav">
          {navItem("home", "Início")}
          {CAT_ORDER.slice(0, 4).map(id => (
            <a key={id} className={route.view === "category" && route.arg === id ? "active" : ""}
               onClick={() => go({ view: "category", arg: id })}>{CATS[id].short}</a>
          ))}
        </nav>
        <div className="topbar-spacer"></div>
        <div className="topbar-actions">
          <button className="search-trigger" onClick={onOpenSearch}>
            <Icon name="search" size={16}/>
            <span>Buscar artigos…</span>
            <span className="kbd">⌘K</span>
          </button>
          <button className="icon-btn" onClick={onToggleTheme} title="Alternar tema" aria-label="Alternar tema">
            <Icon name={theme === "dark" ? "sun" : "moon"} size={18}/>
          </button>
          <button className="btn btn-primary btn-sm" onClick={() => go({ view: "newsletter" })}>Assinar</button>
        </div>
      </div>
    </header>
  );
}

function SearchOverlay({ open, onClose, go }) {
  const [q, setQ] = useStateSh("");
  const [sel, setSel] = useStateSh(0);
  const inputRef = useRefSh(null);
  useEffectSh(() => { if (open) { setQ(""); setSel(0); setTimeout(() => inputRef.current && inputRef.current.focus(), 30); } }, [open]);

  const results = useMemoSh(() => {
    const term = q.trim().toLowerCase();
    if (!term) return ARTICLES.slice(0, 6);
    return ARTICLES.filter(a =>
      a.title.toLowerCase().includes(term) ||
      a.lede.toLowerCase().includes(term) ||
      a.tags.some(t => t.toLowerCase().includes(term)) ||
      CATS[a.cat].name.toLowerCase().includes(term) ||
      AUTHORS[a.author].name.toLowerCase().includes(term)
    ).slice(0, 8);
  }, [q]);

  useEffectSh(() => {
    if (!open) return;
    const onKey = (e) => {
      if (e.key === "Escape") onClose();
      else if (e.key === "ArrowDown") { e.preventDefault(); setSel(s => Math.min(s + 1, results.length - 1)); }
      else if (e.key === "ArrowUp") { e.preventDefault(); setSel(s => Math.max(s - 1, 0)); }
      else if (e.key === "Enter" && results[sel]) { go({ view: "article", arg: results[sel].slug }); onClose(); }
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [open, results, sel]);

  if (!open) return null;
  return (
    <div className="search-overlay" onClick={onClose}>
      <div className="search-scrim"></div>
      <div className="search-panel" onClick={(e) => e.stopPropagation()}>
        <div className="search-input-row">
          <Icon name="search" size={20}/>
          <input ref={inputRef} value={q} onChange={(e) => { setQ(e.target.value); setSel(0); }}
                 placeholder="Buscar por título, tema, tag ou autor…"/>
          <span className="esc" onClick={onClose} style={{ cursor: "pointer" }}>ESC</span>
        </div>
        <div className="search-results">
          {results.length === 0 && <div className="search-empty">Nenhum artigo encontrado para “{q}”.</div>}
          {results.map((a, i) => (
            <div key={a.slug} className={`search-res ${i === sel ? "sel" : ""}`}
                 onMouseEnter={() => setSel(i)}
                 onClick={() => { go({ view: "article", arg: a.slug }); onClose(); }}>
              <div className="sr-thumb"><Thumb article={a} showCat={false}/></div>
              <div style={{ minWidth: 0 }}>
                <h4 style={{ overflow: "hidden", textOverflow: "ellipsis", whiteSpace: "nowrap" }}>{a.title}</h4>
                <div className="srm">{CATS[a.cat].short} · {a.read} min · {AUTHORS[a.author].name}</div>
              </div>
            </div>
          ))}
        </div>
        <div className="search-hint">
          <span><span className="k">↑↓</span>navegar</span>
          <span><span className="k">↵</span>abrir</span>
          <span><span className="k">esc</span>fechar</span>
        </div>
      </div>
    </div>
  );
}

function Footer({ go }) {
  return (
    <footer className="foot">
      <div className="container-wide">
        <div className="foot-top">
          <div>
            <div className="brand" onClick={() => go({ view: "home" })}>
              <img className="mark" src="../assets/logos/ricky-mark-transparent.png" alt="Rickytech"/>
              <span className="wm">Central de <span className="accent">Artigos</span></span>
            </div>
            <p className="ftag">Engenharia de IA e software que vai pra produção. Escrito por quem constrói.</p>
          </div>
          <div className="foot-cols">
            <div className="foot-col">
              <h5>Temas</h5>
              {CAT_ORDER.map(id => <a key={id} onClick={() => go({ view: "category", arg: id })}>{CATS[id].short}</a>)}
            </div>
            <div className="foot-col">
              <h5>Central</h5>
              <a onClick={() => go({ view: "home" })}>Todos os artigos</a>
              <a onClick={() => go({ view: "newsletter" })}>Newsletter</a>
              <a onClick={() => go({ view: "author", arg: "ricky" })}>Autores</a>
            </div>
            <div className="foot-col">
              <h5>Rickytech</h5>
              <a href="#">Site</a>
              <a href="#">Sobre</a>
              <a href="#">Contato</a>
            </div>
          </div>
        </div>
        <div className="foot-bot">
          <span>© 2026 Rickytech · Central de Artigos</span>
          <span style={{ display: "inline-flex", gap: 14 }}>
            <a href="#" aria-label="RSS"><Icon name="rss" size={16}/></a>
            <a href="#" aria-label="X"><Icon name="twitter" size={16}/></a>
          </span>
        </div>
      </div>
    </footer>
  );
}

Object.assign(window, { Topbar, SearchOverlay, Footer });
