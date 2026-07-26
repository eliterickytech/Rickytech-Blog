<!-- Tweaks trigger button -->
<button class="twk-trigger" id="rt-twk-trigger" type="button" title="Personalizar aparência" aria-label="Personalizar aparência">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"></path>
  </svg>
</button>

<!-- Tweaks panel -->
<div class="twk-panel hidden" id="rt-twk-panel">
  <div class="twk-hd">
    <b>Tweaks</b>
    <button class="twk-x" id="rt-twk-close" type="button" aria-label="Fechar tweaks">✕</button>
  </div>
  <div class="twk-body">

    <div class="twk-sect">Aparência</div>

    <div class="twk-row">
      <div class="twk-lbl">Tema</div>
      <div class="twk-seg" id="twk-theme">
        <div class="twk-seg-thumb" id="twk-theme-thumb"></div>
        <button type="button" data-val="dark">dark</button>
        <button type="button" data-val="light">light</button>
      </div>
    </div>

    <div class="twk-row">
      <div class="twk-lbl">Cor de destaque</div>
      <div class="twk-color-opts" id="twk-accent">
        <button class="twk-color-dot" data-accent="violet" style="background:#7c5cff" title="Violet" type="button"></button>
        <button class="twk-color-dot" data-accent="cyan"   style="background:#22d3ee" title="Cyan"   type="button"></button>
        <button class="twk-color-dot" data-accent="emerald" style="background:#10b981" title="Emerald" type="button"></button>
        <button class="twk-color-dot" data-accent="amber"  style="background:#f59e0b" title="Amber"  type="button"></button>
        <button class="twk-color-dot" data-accent="rose"   style="background:#f43f5e" title="Rose"   type="button"></button>
      </div>
    </div>

    <div class="twk-sect">Cards</div>

    <div class="twk-row">
      <div class="twk-lbl">Densidade</div>
      <div class="twk-seg" id="twk-density">
        <div class="twk-seg-thumb" id="twk-density-thumb"></div>
        <button type="button" data-val="compact">compact</button>
        <button type="button" data-val="regular">regular</button>
        <button type="button" data-val="comfy">comfy</button>
      </div>
    </div>

    <div class="twk-row">
      <div class="twk-lbl">Estilo</div>
      <div class="twk-seg" id="twk-cards">
        <div class="twk-seg-thumb" id="twk-cards-thumb"></div>
        <button type="button" data-val="image">image</button>
        <button type="button" data-val="text">text</button>
      </div>
    </div>

    <div class="twk-sect">Leitura</div>

    <div class="twk-row">
      <div class="twk-lbl">Fonte do artigo</div>
      <div class="twk-seg" id="twk-font">
        <div class="twk-seg-thumb" id="twk-font-thumb"></div>
        <button type="button" data-val="sans">sans</button>
        <button type="button" data-val="serif">serif</button>
      </div>
    </div>

  </div>
</div>
