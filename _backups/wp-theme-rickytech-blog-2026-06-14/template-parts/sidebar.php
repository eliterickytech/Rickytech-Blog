<?php
$popular   = rt_get_popular_posts(5);
$cat_order = rt_get_all_cats();
$nl_page   = get_page_by_path('newsletter');
$nl_url    = $nl_page ? get_permalink($nl_page) : home_url('/newsletter/');
?>

<!-- Mais lidos -->
<div class="side-block">
  <div class="side-title">Mais lidos</div>
  <?php foreach ($popular as $i => $pop_post):
    $pop_url = get_permalink($pop_post->ID);
    $cat_cfg = rt_get_post_cat_config($pop_post->ID);
    $rt      = rt_get_read_time($pop_post->ID);
  ?>
    <a href="<?php echo esc_url($pop_url); ?>" class="pop-item">
      <span class="rank"><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></span>
      <div class="pop-body">
        <h4><?php echo esc_html($pop_post->post_title); ?></h4>
        <div class="pm"><?php echo esc_html($cat_cfg['short']); ?> · <?php echo esc_html($rt); ?> min</div>
      </div>
    </a>
  <?php endforeach; ?>
</div>

<!-- Explorar temas -->
<div class="side-block">
  <div class="side-title">Explorar temas</div>
  <div class="cat-list">
    <?php foreach ($cat_order as $cat_slug):
      $cfg   = rt_get_cat_config($cat_slug);
      $term  = get_term_by('slug', $cat_slug, 'category');
      $url   = $term ? get_term_link($term) : '#';
      $count = $term ? $term->count : 0;
    ?>
      <a href="<?php echo esc_url($url); ?>" class="cat-row">
        <span class="swatch" style="background:<?php echo esc_attr($cfg['ink']); ?>"></span>
        <span class="cn"><?php echo esc_html($cfg['name']); ?></span>
        <span class="cc"><?php echo esc_html($count); ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- Newsletter CTA -->
<div class="side-cta">
  <div class="glow"></div>
  <h4>Receba os melhores artigos</h4>
  <p>Um e-mail por semana com o que importa em IA e engenharia. Sem ruído.</p>
  <form class="mini-form" id="rt-sidebar-nl" method="post" novalidate>
    <input class="input" type="email" name="email" placeholder="seu@email.com" required>
    <button class="btn btn-primary" type="submit" style="justify-content:center">Assinar grátis</button>
  </form>
</div>

<script>
(function () {
  var form = document.getElementById('rt-sidebar-nl');
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
          form.innerHTML = '<p style="color:var(--accent);font-size:13px;margin:0;text-align:center">✓ Inscrito com sucesso!</p>';
        }
      });
  });
})();
</script>
