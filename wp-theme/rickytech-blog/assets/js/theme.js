// theme.js — interactivity for the Rickytech blog theme:
// theme toggle, search overlay (⌘K), whole-card navigation, mobile nav,
// and (on single posts) reading progress, TOC scrollspy, code-block chrome.
(function () {
  "use strict";
  var root = document.documentElement;

  // ---- Theme toggle (persisted) ------------------------------------------
  function setTheme(mode) {
    root.setAttribute("data-theme", mode);
    try { localStorage.setItem("rt-theme", mode); } catch (e) {}
    document.querySelectorAll("[data-theme-toggle]").forEach(function (btn) {
      btn.setAttribute("aria-label", mode === "dark" ? "Ativar tema claro" : "Ativar tema escuro");
    });
  }
  document.addEventListener("click", function (e) {
    var t = e.target.closest("[data-theme-toggle]");
    if (!t) return;
    setTheme(root.getAttribute("data-theme") === "dark" ? "light" : "dark");
  });

  // ---- Search overlay ----------------------------------------------------
  var overlay = document.getElementById("rt-search");
  function openSearch() {
    if (!overlay) return;
    overlay.classList.remove("hide");
    var i = overlay.querySelector("input[name='s']");
    setTimeout(function () { if (i) i.focus(); }, 30);
  }
  function closeSearch() { if (overlay) overlay.classList.add("hide"); }
  document.addEventListener("click", function (e) {
    if (e.target.closest("[data-search-open]")) { e.preventDefault(); openSearch(); }
    else if (e.target.closest("[data-search-close]")) { e.preventDefault(); closeSearch(); }
    else if (overlay && e.target === overlay.querySelector(".search-scrim")) { closeSearch(); }
  });
  document.addEventListener("keydown", function (e) {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === "k") { e.preventDefault(); openSearch(); }
    else if (e.key === "/" && !/INPUT|TEXTAREA/.test(document.activeElement.tagName)) { e.preventDefault(); openSearch(); }
    else if (e.key === "Escape") { closeSearch(); var nav = document.querySelector(".topnav.open"); if (nav) nav.classList.remove("open"); }
  });

  // ---- Mobile nav --------------------------------------------------------
  document.addEventListener("click", function (e) {
    if (e.target.closest("[data-nav-toggle]")) {
      e.preventDefault();
      var nav = document.querySelector(".topnav");
      if (nav) nav.classList.toggle("open");
    }
  });

  // ---- Whole-card navigation --------------------------------------------
  document.addEventListener("click", function (e) {
    if (e.target.closest("a, button, input")) return; // let real links work
    var card = e.target.closest("[data-href]");
    if (card) window.location.href = card.getAttribute("data-href");
  });

  // ---- Topbar elevation on scroll ---------------------------------------
  var topbar = document.querySelector(".topbar");
  if (topbar) {
    var onScrollBar = function () { topbar.style.boxShadow = window.scrollY > 4 ? "var(--elev-1)" : "none"; };
    window.addEventListener("scroll", onScrollBar, { passive: true }); onScrollBar();
  }

  // ---- Single post: TOC, reading progress, code chrome ------------------
  var article = document.querySelector(".prose");
  if (article) {
    // Wrap <pre> blocks in mac-style code chrome.
    article.querySelectorAll("pre").forEach(function (pre) {
      if (pre.closest(".codeblock")) return;
      var wrap = document.createElement("div");
      wrap.className = "codeblock js-wrap";
      var head = document.createElement("div");
      head.className = "cb-head";
      ["#ff5f57", "#febc2e", "#28c840"].forEach(function (c) {
        var d = document.createElement("span"); d.className = "cb-dot"; d.style.background = c; head.appendChild(d);
      });
      var lang = document.createElement("span"); lang.className = "cb-lang";
      var codeEl = pre.querySelector("code");
      var cls = codeEl ? (codeEl.className.match(/language-([\w-]+)/) || [])[1] : null;
      lang.textContent = cls || "code";
      head.appendChild(lang);
      pre.parentNode.insertBefore(wrap, pre);
      wrap.appendChild(head); wrap.appendChild(pre);
    });

    // Build TOC from h2s.
    var toc = document.querySelector(".toc ul");
    var heads = Array.prototype.slice.call(article.querySelectorAll("h2"));
    if (toc && heads.length) {
      heads.forEach(function (h, i) {
        if (!h.id) h.id = "sec-" + (i + 1) + "-" + (h.textContent || "").toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "").slice(0, 40);
        var li = document.createElement("li");
        var a = document.createElement("a");
        a.href = "#" + h.id; a.textContent = h.textContent;
        a.addEventListener("click", function (ev) {
          ev.preventDefault();
          window.scrollTo({ top: h.getBoundingClientRect().top + window.scrollY - 80, behavior: "smooth" });
        });
        li.appendChild(a); toc.appendChild(li);
      });
    } else {
      var tocAside = document.querySelector(".toc");
      if (tocAside) tocAside.style.display = "none";
    }

    var bar = document.querySelector(".read-progress");
    var bodyEl = article.closest("main") || article;
    var tocLinks = toc ? Array.prototype.slice.call(toc.querySelectorAll("a")) : [];
    var onScroll = function () {
      if (bar) {
        var total = bodyEl.offsetHeight - window.innerHeight + 200;
        var scrolled = window.scrollY - bodyEl.offsetTop + 200;
        var pct = Math.max(0, Math.min(100, (scrolled / total) * 100));
        bar.style.width = pct + "%";
      }
      var cur = "";
      heads.forEach(function (h) { if (h.getBoundingClientRect().top < 120) cur = h.id; });
      tocLinks.forEach(function (a) { a.classList.toggle("active", a.getAttribute("href") === "#" + cur); });
    };
    window.addEventListener("scroll", onScroll, { passive: true }); onScroll();

    // Share / copy-link buttons.
    document.querySelectorAll("[data-copy-link]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        if (navigator.clipboard) navigator.clipboard.writeText(location.href);
        var label = btn.querySelector("[data-copy-label]");
        if (label) { var old = label.textContent; label.textContent = "Copiado"; setTimeout(function () { label.textContent = old; }, 1400); }
      });
    });

    // Save (bookmark) buttons — persisted locally.
    var saveBtns = document.querySelectorAll("[data-save]");
    if (saveBtns.length) {
      var slug = saveBtns[0].getAttribute("data-save");
      var saved = [];
      try { saved = JSON.parse(localStorage.getItem("rt_saved") || "[]"); } catch (e) {}
      var apply = function () {
        var on = saved.indexOf(slug) !== -1;
        saveBtns.forEach(function (b) { b.classList.toggle("on", on); b.setAttribute("aria-pressed", on); });
      };
      apply();
      saveBtns.forEach(function (b) {
        b.addEventListener("click", function () {
          var idx = saved.indexOf(slug);
          if (idx === -1) saved.push(slug); else saved.splice(idx, 1);
          try { localStorage.setItem("rt_saved", JSON.stringify(saved)); } catch (e) {}
          apply();
        });
      });
    }
  }
})();
