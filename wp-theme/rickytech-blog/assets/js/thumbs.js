// thumbs.js — procedural generative thumbnails (vanilla port of the design's canvas art).
// Paints every .thumb[data-seed] that contains a <canvas> (i.e. posts with no featured image).
(function () {
  function hashStr(s) { let h = 2166136261; for (let i = 0; i < s.length; i++) { h ^= s.charCodeAt(i); h = Math.imul(h, 16777619); } return h >>> 0; }
  function mulberry(seed) { return function () { seed |= 0; seed = (seed + 0x6D2B79F5) | 0; let t = Math.imul(seed ^ (seed >>> 15), 1 | seed); t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t; return ((t ^ (t >>> 14)) >>> 0) / 4294967296; }; }
  function hex2rgb(h) { const n = parseInt(h.slice(1), 16); return [(n >> 16) & 255, (n >> 8) & 255, n & 255]; }

  function paint(canvas, seed, inkHex, deepHex) {
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    const r = canvas.getBoundingClientRect();
    const W = Math.max(2, r.width), H = Math.max(2, r.height);
    canvas.width = W * dpr; canvas.height = H * dpr;
    const ctx = canvas.getContext("2d"); ctx.scale(dpr, dpr);
    const rnd = mulberry(seed);
    const ink = hex2rgb(inkHex), deep = hex2rgb(deepHex);
    const style = seed % 4;

    const g = ctx.createLinearGradient(0, 0, W, H);
    g.addColorStop(0, `rgb(${deep[0]},${deep[1]},${deep[2]})`);
    g.addColorStop(1, `rgb(${Math.round(deep[0] * 0.5)},${Math.round(deep[1] * 0.5)},${Math.round(deep[2] * 0.5)})`);
    ctx.fillStyle = g; ctx.fillRect(0, 0, W, H);

    for (let i = 0; i < 2; i++) {
      const ox = rnd() * W, oy = rnd() * H, rad = (0.4 + rnd() * 0.5) * Math.max(W, H);
      const rg = ctx.createRadialGradient(ox, oy, 0, ox, oy, rad);
      rg.addColorStop(0, `rgba(${ink[0]},${ink[1]},${ink[2]},${0.28 + rnd() * 0.18})`);
      rg.addColorStop(1, `rgba(${ink[0]},${ink[1]},${ink[2]},0)`);
      ctx.fillStyle = rg; ctx.fillRect(0, 0, W, H);
    }

    ctx.lineWidth = 1;
    if (style === 0) {
      const gap = 16, jitter = 3;
      for (let y = gap; y < H; y += gap) for (let x = gap; x < W; x += gap) {
        const a = 0.06 + rnd() * 0.5;
        ctx.fillStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},${a})`;
        const s = 0.8 + rnd() * 1.8;
        ctx.beginPath(); ctx.arc(x + (rnd() - 0.5) * jitter, y + (rnd() - 0.5) * jitter, s, 0, 7); ctx.fill();
      }
    } else if (style === 1) {
      const nodes = 6 + (seed % 4);
      let px = rnd() * W, py = rnd() * H;
      ctx.strokeStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},0.45)`;
      for (let i = 0; i < nodes * 3; i++) {
        const horiz = rnd() > 0.5;
        const nx = horiz ? px + (rnd() - 0.5) * W * 0.5 : px;
        const ny = horiz ? py : py + (rnd() - 0.5) * H * 0.5;
        ctx.beginPath(); ctx.moveTo(px, py); ctx.lineTo(nx, py); ctx.lineTo(nx, ny); ctx.stroke();
        ctx.fillStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},0.8)`;
        ctx.beginPath(); ctx.arc(nx, ny, 2.2, 0, 7); ctx.fill();
        px = nx; py = ny; if (px < 0 || px > W || py < 0 || py > H) { px = rnd() * W; py = rnd() * H; }
      }
    } else if (style === 2) {
      const cx = rnd() * W, cy = H * (0.4 + rnd() * 0.4);
      for (let i = 1; i < 14; i++) {
        ctx.strokeStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},${0.5 - i * 0.03})`;
        ctx.lineWidth = 1.4;
        ctx.beginPath(); ctx.arc(cx, cy, i * 14 + rnd() * 4, Math.PI * 0.1, Math.PI * 0.9); ctx.stroke();
      }
    } else {
      ctx.strokeStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},0.35)`;
      for (let i = 0; i < 28; i++) {
        let x = rnd() * W, y = rnd() * H; ctx.beginPath(); ctx.moveTo(x, y);
        for (let j = 0; j < 14; j++) { const ang = (Math.sin(x * 0.01) + Math.cos(y * 0.012)) * 1.6; x += Math.cos(ang) * 10; y += Math.sin(ang) * 10; ctx.lineTo(x, y); }
        ctx.lineWidth = 0.6 + rnd() * 1.4; ctx.stroke();
      }
    }

    const vg = ctx.createRadialGradient(W / 2, H / 2, Math.min(W, H) * 0.3, W / 2, H / 2, Math.max(W, H) * 0.75);
    vg.addColorStop(0, "rgba(0,0,0,0)"); vg.addColorStop(1, "rgba(0,0,0,0.32)");
    ctx.fillStyle = vg; ctx.fillRect(0, 0, W, H);
  }

  function init() {
    document.querySelectorAll(".thumb[data-seed]").forEach(function (el) {
      const cv = el.querySelector("canvas"); if (!cv) return;
      const seed = hashStr(el.getAttribute("data-seed") || "x");
      const ink = el.getAttribute("data-ink") || "#8b6dff";
      const deep = el.getAttribute("data-deep") || "#16161f";
      const draw = function () { paint(cv, seed, ink, deep); };
      draw();
      if (window.ResizeObserver) { const ro = new ResizeObserver(draw); ro.observe(cv); }
    });
  }

  if (document.readyState !== "loading") init();
  else document.addEventListener("DOMContentLoaded", init);
})();
