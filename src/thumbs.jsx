// thumbs.jsx — procedural generative thumbnails + avatars + icons.
// Deterministic art keyed by article slug so each card looks distinct
// but stable across renders. On-brand: dot grids, circuit lines, orbs.

const { useRef: useRefT, useEffect: useEffectT } = React;

function hashStr(s) { let h = 2166136261; for (let i = 0; i < s.length; i++) { h ^= s.charCodeAt(i); h = Math.imul(h, 16777619); } return h >>> 0; }
function mulberry(seed) { return function () { seed |= 0; seed = (seed + 0x6D2B79F5) | 0; let t = Math.imul(seed ^ (seed >>> 15), 1 | seed); t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t; return ((t ^ (t >>> 14)) >>> 0) / 4294967296; }; }
function hex2rgb(h) { const n = parseInt(h.slice(1), 16); return [(n >> 16) & 255, (n >> 8) & 255, n & 255]; }

// Four generative styles, chosen by seed
function paintThumb(canvas, seed, inkHex, deepHex) {
  const dpr = Math.min(window.devicePixelRatio || 1, 2);
  const r = canvas.getBoundingClientRect();
  const W = Math.max(2, r.width), H = Math.max(2, r.height);
  canvas.width = W * dpr; canvas.height = H * dpr;
  const ctx = canvas.getContext("2d"); ctx.scale(dpr, dpr);
  const rnd = mulberry(seed);
  const ink = hex2rgb(inkHex), deep = hex2rgb(deepHex);
  const style = seed % 4;

  // base gradient
  const g = ctx.createLinearGradient(0, 0, W, H);
  g.addColorStop(0, `rgb(${deep[0]},${deep[1]},${deep[2]})`);
  g.addColorStop(1, `rgb(${Math.round(deep[0]*0.5)},${Math.round(deep[1]*0.5)},${Math.round(deep[2]*0.5)})`);
  ctx.fillStyle = g; ctx.fillRect(0, 0, W, H);

  // glow orbs
  for (let i = 0; i < 2; i++) {
    const ox = rnd() * W, oy = rnd() * H, rad = (0.4 + rnd() * 0.5) * Math.max(W, H);
    const rg = ctx.createRadialGradient(ox, oy, 0, ox, oy, rad);
    rg.addColorStop(0, `rgba(${ink[0]},${ink[1]},${ink[2]},${0.28 + rnd()*0.18})`);
    rg.addColorStop(1, `rgba(${ink[0]},${ink[1]},${ink[2]},0)`);
    ctx.fillStyle = rg; ctx.fillRect(0, 0, W, H);
  }

  ctx.lineWidth = 1;
  if (style === 0) {
    // dot grid
    const gap = 16, jitter = 3;
    for (let y = gap; y < H; y += gap) for (let x = gap; x < W; x += gap) {
      const a = 0.06 + rnd() * 0.5;
      ctx.fillStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},${a})`;
      const s = 0.8 + rnd() * 1.8;
      ctx.beginPath(); ctx.arc(x + (rnd()-0.5)*jitter, y + (rnd()-0.5)*jitter, s, 0, 7); ctx.fill();
    }
  } else if (style === 1) {
    // circuit lines
    const nodes = 6 + (seed % 4);
    let px = rnd()*W, py = rnd()*H;
    ctx.strokeStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},0.45)`;
    for (let i = 0; i < nodes*3; i++) {
      const horiz = rnd() > 0.5;
      const nx = horiz ? px + (rnd()-0.5)*W*0.5 : px;
      const ny = horiz ? py : py + (rnd()-0.5)*H*0.5;
      ctx.beginPath(); ctx.moveTo(px, py); ctx.lineTo(nx, py); ctx.lineTo(nx, ny); ctx.stroke();
      ctx.fillStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},0.8)`;
      ctx.beginPath(); ctx.arc(nx, ny, 2.2, 0, 7); ctx.fill();
      px = nx; py = ny; if (px < 0 || px > W || py < 0 || py > H) { px = rnd()*W; py = rnd()*H; }
    }
  } else if (style === 2) {
    // concentric arcs / waveform
    const cx = rnd()*W, cy = H*(0.4+rnd()*0.4);
    for (let i = 1; i < 14; i++) {
      ctx.strokeStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},${0.5 - i*0.03})`;
      ctx.lineWidth = 1.4;
      ctx.beginPath(); ctx.arc(cx, cy, i*14 + rnd()*4, Math.PI*0.1, Math.PI*0.9); ctx.stroke();
    }
  } else {
    // flow field strokes
    ctx.strokeStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},0.35)`;
    for (let i = 0; i < 28; i++) {
      let x = rnd()*W, y = rnd()*H; ctx.beginPath(); ctx.moveTo(x, y);
      for (let j = 0; j < 14; j++) { const ang = (Math.sin(x*0.01)+Math.cos(y*0.012))*1.6; x += Math.cos(ang)*10; y += Math.sin(ang)*10; ctx.lineTo(x, y); }
      ctx.lineWidth = 0.6 + rnd()*1.4; ctx.stroke();
    }
  }
  // subtle vignette
  const vg = ctx.createRadialGradient(W/2, H/2, Math.min(W,H)*0.3, W/2, H/2, Math.max(W,H)*0.75);
  vg.addColorStop(0, "rgba(0,0,0,0)"); vg.addColorStop(1, "rgba(0,0,0,0.32)");
  ctx.fillStyle = vg; ctx.fillRect(0, 0, W, H);
}

function Thumb({ article, showCat = true }) {
  const ref = useRefT(null);
  const cat = CATS[article.cat];
  const seed = hashStr(article.slug);
  useEffectT(() => {
    const cv = ref.current; if (!cv) return;
    const draw = () => paintThumb(cv, seed, cat.ink, cat.deep);
    draw();
    const ro = new ResizeObserver(draw); ro.observe(cv);
    return () => ro.disconnect();
  }, [article.slug]);
  return (
    <div className="thumb" style={{ "--cat-deep": cat.deep, "--cat-ink": cat.ink }}>
      <canvas ref={ref}></canvas>
      {showCat && <span className="thumb-cat">{cat.short}</span>}
      <span className="thumb-glyph">{cat.glyph}</span>
    </div>
  );
}

function Avatar({ author, size = 32, className = "" }) {
  const a = typeof author === "string" ? AUTHORS[author] : author;
  if (!a) return null;
  const c1 = a.av, c2 = `color-mix(in oklch, ${a.av} 55%, #050507)`;
  return (
    <div className={`av ${className}`} style={{
      width: size, height: size, borderRadius: "50%",
      background: `linear-gradient(135deg, ${c1}, ${c2})`,
      display: "inline-flex", alignItems: "center", justifyContent: "center",
      color: "#fff", fontFamily: "var(--font-mono)", fontWeight: 500,
      fontSize: size * 0.38, letterSpacing: "0.02em", flex: `0 0 ${size}px`,
      boxShadow: "inset 0 1px 0 rgba(255,255,255,.18)",
    }}>{a.initials}</div>
  );
}

// ---- Icons (stroke, 24 viewBox) ----
const Ic = {
  search: "M11 19a8 8 0 100-16 8 8 0 000 16zM21 21l-4.3-4.3",
  sun: "M12 3v2M12 19v2M5 5l1.5 1.5M17.5 17.5L19 19M3 12h2M19 12h2M5 19l1.5-1.5M17.5 6.5L19 5M12 8a4 4 0 100 8 4 4 0 000-8z",
  moon: "M21 12.8A9 9 0 1111.2 3a7 7 0 109.8 9.8z",
  arrowRight: "M5 12h14M13 6l6 6-6 6",
  arrowLeft: "M19 12H5M11 18l-6-6 6-6",
  bookmark: "M6 4h12v16l-6-4-6 4V4z",
  share: "M16 6l-4-4-4 4M12 2v13M4 12v7a2 2 0 002 2h12a2 2 0 002-2v-7",
  heart: "M12 20s-7-4.6-9.5-9A4.8 4.8 0 0112 5a4.8 4.8 0 019.5 6c-2.5 4.4-9.5 9-9.5 9z",
  clock: "M12 7v5l3 2M12 21a9 9 0 100-18 9 9 0 000 18z",
  check: "M5 13l4 4L19 7",
  twitter: "M22 5.8c-.7.3-1.5.5-2.3.6a4 4 0 001.7-2.2c-.8.5-1.6.8-2.5 1a4 4 0 00-6.8 3.6A11.3 11.3 0 013 4.8a4 4 0 001.2 5.3c-.6 0-1.2-.2-1.7-.5a4 4 0 003.2 3.9c-.6.2-1.2.2-1.7.1a4 4 0 003.7 2.8A8 8 0 012 22a11.3 11.3 0 006.2 1.8c7.4 0 11.5-6.2 11.5-11.5v-.5c.8-.6 1.5-1.3 2.1-2z",
  link: "M10 13a5 5 0 007.5.5l3-3a5 5 0 00-7-7l-1.5 1.5M14 11a5 5 0 00-7.5-.5l-3 3a5 5 0 007 7L12 19",
  rss: "M5 19a1 1 0 100-2 1 1 0 000 2zM4 11a9 9 0 019 9M4 5a15 15 0 0115 15",
  menu: "M3 12h18M3 6h18M3 18h18",
  x: "M6 6l12 12M18 6L6 18",
  bolt: "M13 2L4 14h7l-1 8 9-12h-7l1-8z",
  layers: "M12 3l9 5-9 5-9-5 9-5zM3 13l9 5 9-5",
};
function Icon({ name, size = 18, fill = false, style }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill={fill ? "currentColor" : "none"} stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" style={style}>
      <path d={Ic[name]}></path>
    </svg>
  );
}

Object.assign(window, { Thumb, Avatar, Icon, Ic, hashStr });
