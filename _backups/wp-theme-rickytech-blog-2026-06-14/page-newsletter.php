<?php
/*
 * Template Name: Newsletter
 */
get_header();
?>

<div class="view-enter">
  <div class="nl-hero">
    <div class="nl-aurora"></div>
    <div class="container">
      <div class="nl-card" id="rt-nl-card">
        <span class="badge">
          <?php rt_render_icon('rss', 13); ?> Newsletter semanal
        </span>
        <h1>O essencial de IA e engenharia, toda terça</h1>
        <p>Curadoria sem ruído: os artigos, ferramentas e ideias que valem o seu tempo. Junte-se a <strong>9.400+</strong> devs que constroem software de verdade.</p>
        <form class="nl-form" id="rt-nl-form" method="post" novalidate>
          <?php wp_nonce_field('rt_search', 'security'); ?>
          <input class="input" type="email" name="email" placeholder="seu@email.com" required>
          <button class="btn btn-primary" type="submit">Assinar grátis</button>
        </form>
        <div class="nl-meta">Sem spam. Cancele quando quiser.</div>
      </div>

      <div class="nl-feats">
        <div class="nl-feat"><?php rt_render_icon('check', 16); ?> 1 e-mail por semana</div>
        <div class="nl-feat"><?php rt_render_icon('check', 16); ?> Conteúdo aprofundado</div>
        <div class="nl-feat"><?php rt_render_icon('check', 16); ?> Zero anúncios</div>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  var form = document.getElementById('rt-nl-form');
  if (!form) return;
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var email = form.querySelector('[name="email"]').value;
    var data  = new FormData();
    data.append('action', 'rt_newsletter');
    data.append('nonce',  rtConfig.nonce);
    data.append('email',  email);
    fetch(rtConfig.ajaxUrl, { method: 'POST', body: data })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.success) {
          var card = document.getElementById('rt-nl-card');
          card.innerHTML = '<div class="nl-success">' +
            '<div class="check"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg></div>' +
            '<h1 style="margin:0">Inscrição confirmada!</h1>' +
            '<p style="margin:0">Bem-vindo à Central. O próximo número chega na terça — enquanto isso, que tal um artigo?</p>' +
            '<a href="' + rtConfig.homeUrl + '" class="btn btn-primary" style="margin-top:6px">Explorar artigos</a>' +
            '</div>';
        }
      });
  });
})();
</script>

<?php get_footer(); ?>
