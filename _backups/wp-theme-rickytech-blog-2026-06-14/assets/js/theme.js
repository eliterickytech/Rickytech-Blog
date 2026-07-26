/* Rickytech Blog — theme.js
   Handles: tweaks (dark/light, accent, density, cards, readFont),
   search overlay (AJAX), generative canvas thumbs, TOC active
   heading, read progress bar, topbar scroll shadow, share button. */

(function () {
  'use strict';

  /* ----------------------------------------------------------------
     TWEAKS — localStorage persistence + DOM application
  ---------------------------------------------------------------- */
  var TWEAK_DEFAULTS = { theme: 'dark', accent: 'violet', density: 'regular', cards: 'image', readFont: 'sans' };
  var ACCENT_COLORS  = { violet: '#7c5cff', cyan: '#22d3ee', emerald: '#10b981', amber: '#f59e0b', rose: '#f43f5e' };

  function loadTweaks() {
    try { return Object.assign({}, TWEAK_DEFAULTS, JSON.parse(localStorage.getItem('rt_tweaks') || '{}')); }
    catch (e) { return Object.assign({}, TWEAK_DEFAULTS); }
  }

  function saveTweaks(t) {
    try { localStorage.setItem('rt_tweaks', JSON.stringify(t)); } catch (e) {}
  }

  function applyTweaks(t) {
    var r = document.documentElement;
    r.setAttribute('data-theme',   t.theme);
    r.setAttribute('data-accent',  t.accent);
    r.setAttribute('data-density', t.density);
    r.setAttribute('data-cards',   t.cards);
    r.style.setProperty('--read-font', t.readFont === 'serif' ? '"Newsreader", Georgia, serif' : 'var(--font-sans)');
    r.style.setProperty('--read-size', t.readFont === 'serif' ? '20px' : '18px');
  }

  // Apply immediately (before DOMContentLoaded) to avoid flash
  var tweaks = loadTweaks();
  applyTweaks(tweaks);

  /* ----------------------------------------------------------------
     DOM READY
  ---------------------------------------------------------------- */
  document.addEventListener('DOMContentLoaded', function () {

    /* --- Theme toggle button --- */
    var themeBtn = document.getElementById('rt-theme-toggle');
    if (themeBtn) {
      updateThemeIcon(themeBtn, tweaks.theme);
      themeBtn.addEventListener('click', function () {
        tweaks.theme = tweaks.theme === 'dark' ? 'light' : 'dark';
        saveTweaks(tweaks);
        applyTweaks(tweaks);
        updateThemeIcon(themeBtn, tweaks.theme);
      });
    }

    /* --- Tweaks panel --- */
    initTweaksPanel();

    /* --- Search overlay --- */
    initSearch();

    /* --- Topbar shadow on scroll --- */
    var topbar = document.getElementById('rt-topbar');
    if (topbar) {
      window.addEventListener('scroll', function () {
        topbar.style.boxShadow = window.scrollY > 4 ? 'var(--elev-1)' : '';
      }, { passive: true });
    }

    /* --- Read progress bar --- */
    var progressBar = document.getElementById('rt-read-progress');
    var proseBody   = document.getElementById('rt-prose-body');
    if (progressBar && proseBody) {
      window.addEventListener('scroll', function () {
        var total   = proseBody.offsetHeight - window.innerHeight + 200;
        var scrolled = window.scrollY - proseBody.offsetTop + 200;
        var pct = Math.max(0, Math.min(100, (scrolled / total) * 100));
        progressBar.style.width = pct + '%';
      }, { passive: true });
    }

    /* --- TOC active heading --- */
    var toc = document.getElementById('rt-toc');
    if (toc) {
      var tocLinks = toc.querySelectorAll('[data-heading-id]');
      window.addEventListener('scroll', function () {
        var cur = '';
        tocLinks.forEach(function (a) {
          var id = a.getAttribute('data-heading-id');
          var el = document.getElementById(id);
          if (el && el.getBoundingClientRect().top < 120) cur = id;
        });
        tocLinks.forEach(function (a) {
          a.classList.toggle('active', a.getAttribute('data-heading-id') === cur);
        });
      }, { passive: true });
    }

    /* --- Share button --- */
    var shareBtn  = document.getElementById('rt-share-btn');
    var railShare = document.getElementById('rt-rail-share');
    [shareBtn, railShare].forEach(function (btn) {
      if (!btn) return;
      btn.addEventListener('click', function () {
        navigator.clipboard && navigator.clipboard.writeText(location.href);
        var span = btn.querySelector('span');
        if (span) { span.textContent = 'Copiado'; setTimeout(function () { span.textContent = 'Link'; }, 1400); }
      });
    });

    /* --- Home category filter (client-side DOM filter) --- */
    initCatFilter();

    /* --- Generative canvas thumbnails --- */
    paintAllThumbs();

  }); // DOMContentLoaded

  /* ----------------------------------------------------------------
     ICON HELPER
  ---------------------------------------------------------------- */
  function updateThemeIcon(btn, theme) {
    btn.innerHTML = theme === 'dark'
      ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v2M12 19v2M5 5l1.5 1.5M17.5 17.5L19 19M3 12h2M19 12h2M5 19l1.5-1.5M17.5 6.5L19 5M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>'
      : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1111.2 3a7 7 0 109.8 9.8z"></path></svg>';
  }

  /* ----------------------------------------------------------------
     TWEAKS PANEL
  ---------------------------------------------------------------- */
  function initTweaksPanel() {
    var trigger = document.getElementById('rt-twk-trigger');
    var panel   = document.getElementById('rt-twk-panel');
    var closeBtn = document.getElementById('rt-twk-close');
    if (!trigger || !panel) return;

    // Sync UI to current tweaks
    syncTweakSegment('twk-theme',   'twk-theme-thumb',   tweaks.theme,   ['dark','light']);
    syncTweakSegment('twk-density', 'twk-density-thumb', tweaks.density, ['compact','regular','comfy']);
    syncTweakSegment('twk-cards',   'twk-cards-thumb',   tweaks.cards,   ['image','text']);
    syncTweakSegment('twk-font',    'twk-font-thumb',     tweaks.readFont, ['sans','serif']);
    syncAccentDots(tweaks.accent);

    trigger.addEventListener('click', function () {
      panel.classList.toggle('hidden');
    });
    if (closeBtn) {
      closeBtn.addEventListener('click', function () { panel.classList.add('hidden'); });
    }

    // Theme segment
    bindSegment('twk-theme', ['dark','light'], function (val) {
      tweaks.theme = val;
      saveTweaks(tweaks); applyTweaks(tweaks);
      var btn = document.getElementById('rt-theme-toggle');
      if (btn) updateThemeIcon(btn, val);
    });

    // Density segment
    bindSegment('twk-density', ['compact','regular','comfy'], function (val) {
      tweaks.density = val; saveTweaks(tweaks); applyTweaks(tweaks);
    });

    // Cards segment
    bindSegment('twk-cards', ['image','text'], function (val) {
      tweaks.cards = val; saveTweaks(tweaks); applyTweaks(tweaks);
    });

    // Read font segment
    bindSegment('twk-font', ['sans','serif'], function (val) {
      tweaks.readFont = val; saveTweaks(tweaks); applyTweaks(tweaks);
    });

    // Accent dots
    var accentContainer = document.getElementById('twk-accent');
    if (accentContainer) {
      accentContainer.querySelectorAll('[data-accent]').forEach(function (dot) {
        dot.addEventListener('click', function () {
          tweaks.accent = dot.getAttribute('data-accent');
          saveTweaks(tweaks); applyTweaks(tweaks);
          syncAccentDots(tweaks.accent);
        });
      });
    }
  }

  function syncTweakSegment(segId, thumbId, currentVal, opts) {
    var seg   = document.getElementById(segId);
    var thumb = document.getElementById(thumbId);
    if (!seg || !thumb) return;
    var idx = opts.indexOf(currentVal);
    if (idx < 0) idx = 0;
    var n = opts.length;
    thumb.style.left  = 'calc(2px + ' + idx + ' * (100% - 4px) / ' + n + ')';
    thumb.style.width = 'calc((100% - 4px) / ' + n + ')';
  }

  function bindSegment(segId, opts, onChange) {
    var seg = document.getElementById(segId);
    if (!seg) return;
    var thumbId = segId + '-thumb';
    seg.querySelectorAll('button').forEach(function (btn, i) {
      btn.addEventListener('click', function () {
        var val = btn.getAttribute('data-val');
        onChange(val);
        syncTweakSegment(segId, thumbId, val, opts);
      });
    });
  }

  function syncAccentDots(currentAccent) {
    var container = document.getElementById('twk-accent');
    if (!container) return;
    container.querySelectorAll('[data-accent]').forEach(function (dot) {
      dot.classList.toggle('on', dot.getAttribute('data-accent') === currentAccent);
    });
  }

  /* ----------------------------------------------------------------
     SEARCH OVERLAY
  ---------------------------------------------------------------- */
  function initSearch() {
    var overlay  = document.getElementById('rt-search-overlay');
    var scrim    = document.getElementById('rt-search-scrim');
    var input    = document.getElementById('rt-search-input');
    var results  = document.getElementById('rt-search-results');
    var closeBtn = document.getElementById('rt-search-close');
    var trigger  = document.getElementById('rt-search-trigger');

    if (!overlay || !input) return;

    var sel = 0;
    var items = [];
    var debounceTimer = null;

    function openSearch() {
      overlay.classList.remove('hidden');
      input.value = '';
      input.focus();
      sel = 0;
      showRecentOrEmpty();
    }

    function closeSearch() { overlay.classList.add('hidden'); }

    function showRecentOrEmpty() {
      results.innerHTML = '<div class="search-empty" style="padding:20px;text-align:left;color:var(--fg-tertiary);font-size:13px">Digite para buscar…</div>';
      items = [];
    }

    if (trigger) trigger.addEventListener('click', openSearch);
    if (closeBtn) closeBtn.addEventListener('click', closeSearch);
    if (scrim) scrim.addEventListener('click', closeSearch);

    // ⌘K / Ctrl+K / "/"
    document.addEventListener('keydown', function (e) {
      if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); openSearch(); return; }
      if (e.key === '/' && !['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) { e.preventDefault(); openSearch(); return; }
      if (!overlay.classList.contains('hidden')) {
        if (e.key === 'Escape') { closeSearch(); return; }
        if (e.key === 'ArrowDown') { e.preventDefault(); sel = Math.min(sel + 1, items.length - 1); highlightItem(); return; }
        if (e.key === 'ArrowUp')   { e.preventDefault(); sel = Math.max(sel - 1, 0); highlightItem(); return; }
        if (e.key === 'Enter' && items[sel]) { location.href = items[sel].dataset.url; closeSearch(); return; }
      }
    });

    input.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      var q = input.value.trim();
      if (q.length < 2) { showRecentOrEmpty(); return; }
      debounceTimer = setTimeout(function () { doSearch(q); }, 200);
    });

    function highlightItem() {
      items.forEach(function (el, i) { el.classList.toggle('sel', i === sel); });
    }

    function doSearch(q) {
      if (!window.rtConfig) return;
      var url = rtConfig.ajaxUrl + '?action=rt_search&nonce=' + rtConfig.nonce + '&q=' + encodeURIComponent(q);
      fetch(url)
        .then(function (r) { return r.json(); })
        .then(function (data) {
          renderResults(data);
        })
        .catch(function () { results.innerHTML = '<div class="search-empty">Erro ao buscar.</div>'; });
    }

    function renderResults(data) {
      if (!data || !data.length) {
        results.innerHTML = '<div class="search-empty">Nenhum artigo encontrado.</div>';
        items = [];
        return;
      }
      results.innerHTML = data.map(function (r, i) {
        return '<a class="search-res" data-url="' + escAttr(r.url) + '" href="' + escAttr(r.url) + '">' +
          '<div class="sr-thumb"><div class="thumb" data-slug="' + escAttr(r.slug) + '" data-ink="' + escAttr(r.cat_ink) + '" data-deep="' + escAttr(r.cat_deep) + '" style="--cat-deep:' + escAttr(r.cat_deep) + ';--cat-ink:' + escAttr(r.cat_ink) + ';aspect-ratio:16/10"><canvas></canvas></div></div>' +
          '<div style="min-width:0"><h4 style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + escHtml(r.title) + '</h4>' +
          '<div class="srm">' + escHtml(r.cat_short) + ' · ' + r.read_time + ' min · ' + escHtml(r.author) + '</div></div>' +
          '</a>';
      }).join('');
      items = Array.from(results.querySelectorAll('.search-res'));
      items.forEach(function (el, i) {
        el.addEventListener('mouseenter', function () { sel = i; highlightItem(); });
        el.addEventListener('click', function () { closeSearch(); });
      });
      sel = 0;
      highlightItem();
      // Paint newly rendered thumbnails
      results.querySelectorAll('.thumb canvas').forEach(paintCanvas);
    }
  }

  /* ----------------------------------------------------------------
     HOME CATEGORY FILTER
  ---------------------------------------------------------------- */
  function initCatFilter() {
    var filter  = document.getElementById('rt-cat-filter');
    var grid    = document.getElementById('rt-card-grid');
    var feed    = document.getElementById('rt-feed');
    var gridHead = document.getElementById('rt-grid-head');
    if (!filter || !grid) return;

    filter.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-cat]');
      if (!btn) return;
      var cat = btn.getAttribute('data-cat');

      // Update active chip
      filter.querySelectorAll('.chip').forEach(function (c) { c.classList.remove('on'); });
      btn.classList.add('on');

      // Filter cards
      var cards = grid.querySelectorAll('.acard');
      cards.forEach(function (card) {
        var cardCat = card.getAttribute('data-cat');
        if (cat === 'all' || cardCat === cat) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });

      // Feed items
      if (feed) {
        var feedItems = feed.querySelectorAll('.feed-item');
        feedItems.forEach(function (item) {
          var itemCat = item.getAttribute('data-cat');
          item.style.display = (cat === 'all' || itemCat === cat) ? '' : 'none';
        });
      }

      // Update section heading
      if (gridHead) {
        var h2 = gridHead.querySelector('h2');
        if (h2) h2.textContent = cat === 'all' ? 'Mais recentes' : btn.textContent.trim();
      }
    });

    // Set data-cat on each card from its category color (we need to embed it)
    // Cards need data-cat attribute — see article-card.php output
  }

  /* ----------------------------------------------------------------
     GENERATIVE CANVAS THUMBNAILS
     Ported 1:1 from thumbs.jsx — same deterministic algorithm.
  ---------------------------------------------------------------- */
  function hashStr(s) {
    var h = 2166136261;
    for (var i = 0; i < s.length; i++) {
      h ^= s.charCodeAt(i);
      h = Math.imul(h, 16777619);
    }
    return h >>> 0;
  }

  function mulberry(seed) {
    return function () {
      seed |= 0;
      seed = (seed + 0x6D2B79F5) | 0;
      var t = Math.imul(seed ^ (seed >>> 15), 1 | seed);
      t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
      return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
  }

  function hex2rgb(h) {
    var n = parseInt(h.replace('#', ''), 16);
    return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
  }

  function paintThumb(canvas, seed, inkHex, deepHex) {
    var dpr = Math.min(window.devicePixelRatio || 1, 2);
    var r   = canvas.getBoundingClientRect();
    var W   = Math.max(2, r.width);
    var H   = Math.max(2, r.height);
    canvas.width  = W * dpr;
    canvas.height = H * dpr;
    var ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);
    var rnd   = mulberry(seed);
    var ink   = hex2rgb(inkHex);
    var deep  = hex2rgb(deepHex);
    var style = seed % 4;

    // base gradient
    var g = ctx.createLinearGradient(0, 0, W, H);
    g.addColorStop(0, 'rgb(' + deep[0] + ',' + deep[1] + ',' + deep[2] + ')');
    g.addColorStop(1, 'rgb(' + Math.round(deep[0]*.5) + ',' + Math.round(deep[1]*.5) + ',' + Math.round(deep[2]*.5) + ')');
    ctx.fillStyle = g; ctx.fillRect(0, 0, W, H);

    // glow orbs
    for (var i = 0; i < 2; i++) {
      var ox = rnd()*W, oy = rnd()*H, rad = (0.4 + rnd()*0.5) * Math.max(W,H);
      var rg = ctx.createRadialGradient(ox, oy, 0, ox, oy, rad);
      rg.addColorStop(0, 'rgba(' + ink[0] + ',' + ink[1] + ',' + ink[2] + ',' + (0.28 + rnd()*.18) + ')');
      rg.addColorStop(1, 'rgba(' + ink[0] + ',' + ink[1] + ',' + ink[2] + ',0)');
      ctx.fillStyle = rg; ctx.fillRect(0, 0, W, H);
    }

    ctx.lineWidth = 1;
    if (style === 0) {
      var gap = 16, jitter = 3;
      for (var y = gap; y < H; y += gap) {
        for (var x = gap; x < W; x += gap) {
          var a = 0.06 + rnd()*.5;
          ctx.fillStyle = 'rgba(' + ink[0] + ',' + ink[1] + ',' + ink[2] + ',' + a + ')';
          var s = 0.8 + rnd()*1.8;
          ctx.beginPath(); ctx.arc(x + (rnd()-.5)*jitter, y + (rnd()-.5)*jitter, s, 0, 7); ctx.fill();
        }
      }
    } else if (style === 1) {
      var nodes = 6 + (seed % 4);
      var px = rnd()*W, py = rnd()*H;
      ctx.strokeStyle = 'rgba(' + ink[0] + ',' + ink[1] + ',' + ink[2] + ',0.45)';
      for (var j = 0; j < nodes*3; j++) {
        var horiz = rnd() > 0.5;
        var nx = horiz ? px + (rnd()-.5)*W*.5 : px;
        var ny = horiz ? py : py + (rnd()-.5)*H*.5;
        ctx.beginPath(); ctx.moveTo(px,py); ctx.lineTo(nx,py); ctx.lineTo(nx,ny); ctx.stroke();
        ctx.fillStyle = 'rgba(' + ink[0] + ',' + ink[1] + ',' + ink[2] + ',0.8)';
        ctx.beginPath(); ctx.arc(nx, ny, 2.2, 0, 7); ctx.fill();
        px = nx; py = ny;
        if (px < 0 || px > W || py < 0 || py > H) { px = rnd()*W; py = rnd()*H; }
      }
    } else if (style === 2) {
      var cx = rnd()*W, cy = H*(0.4+rnd()*.4);
      for (var k = 1; k < 14; k++) {
        ctx.strokeStyle = 'rgba(' + ink[0] + ',' + ink[1] + ',' + ink[2] + ',' + (0.5 - k*.03) + ')';
        ctx.lineWidth = 1.4;
        ctx.beginPath(); ctx.arc(cx, cy, k*14 + rnd()*4, Math.PI*.1, Math.PI*.9); ctx.stroke();
      }
    } else {
      ctx.strokeStyle = 'rgba(' + ink[0] + ',' + ink[1] + ',' + ink[2] + ',0.35)';
      for (var m = 0; m < 28; m++) {
        var fx = rnd()*W, fy = rnd()*H;
        ctx.beginPath(); ctx.moveTo(fx, fy);
        for (var n = 0; n < 14; n++) {
          var ang = (Math.sin(fx*.01) + Math.cos(fy*.012)) * 1.6;
          fx += Math.cos(ang)*10; fy += Math.sin(ang)*10;
          ctx.lineTo(fx, fy);
        }
        ctx.lineWidth = 0.6 + rnd()*1.4; ctx.stroke();
      }
    }

    // vignette
    var vg = ctx.createRadialGradient(W/2,H/2,Math.min(W,H)*.3, W/2,H/2,Math.max(W,H)*.75);
    vg.addColorStop(0, 'rgba(0,0,0,0)'); vg.addColorStop(1, 'rgba(0,0,0,0.32)');
    ctx.fillStyle = vg; ctx.fillRect(0, 0, W, H);
  }

  function paintCanvas(canvas) {
    var wrap = canvas.closest('.thumb');
    if (!wrap) return;
    var slug = wrap.getAttribute('data-slug') || 'default';
    var ink  = wrap.getAttribute('data-ink')  || '#7c5cff';
    var deep = wrap.getAttribute('data-deep') || '#16161f';
    var seed = hashStr(slug);
    paintThumb(canvas, seed, ink, deep);
  }

  function paintAllThumbs() {
    document.querySelectorAll('.thumb canvas').forEach(function (canvas) {
      paintCanvas(canvas);
      // Repaint on resize
      if (typeof ResizeObserver !== 'undefined') {
        var ro = new ResizeObserver(function () { paintCanvas(canvas); });
        ro.observe(canvas.closest('.thumb'));
      }
    });
  }

  /* ----------------------------------------------------------------
     UTILS
  ---------------------------------------------------------------- */
  function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function escAttr(str) { return escHtml(str); }

})();
