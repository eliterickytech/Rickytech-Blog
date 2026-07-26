<?php
$cat_order = rt_get_all_cats();
$nl_page = get_page_by_path('newsletter');
$nl_url  = $nl_page ? get_permalink($nl_page) : home_url('/newsletter/');
$author_page = get_author_posts_url(1);
?>

<footer class="foot">
  <div class="container-wide">
    <div class="foot-top">
      <div>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="brand">
          <img class="mark"
               src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/logos/ricky-mark-transparent.png"
               alt="<?php bloginfo('name'); ?>">
          <span class="wm">Central de <span class="accent">Artigos</span></span>
        </a>
        <p class="ftag">Engenharia de IA e software que vai pra produção. Escrito por quem constrói.</p>
      </div>
      <div class="foot-cols">
        <div class="foot-col">
          <h5>Temas</h5>
          <?php foreach ($cat_order as $cat_slug):
            $cfg  = rt_get_cat_config($cat_slug);
            $term = get_term_by('slug', $cat_slug, 'category');
            $url  = $term ? get_term_link($term) : '#';
          ?>
            <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($cfg['short']); ?></a>
          <?php endforeach; ?>
        </div>
        <div class="foot-col">
          <h5>Central</h5>
          <a href="<?php echo esc_url(home_url('/')); ?>">Todos os artigos</a>
          <a href="<?php echo esc_url($nl_url); ?>">Newsletter</a>
          <a href="<?php echo esc_url(home_url('/autores/')); ?>">Autores</a>
        </div>
        <div class="foot-col">
          <h5>Rickytech</h5>
          <a href="https://rickytech.com.br" target="_blank" rel="noopener">Site</a>
          <a href="<?php echo esc_url(home_url('/sobre/')); ?>">Sobre</a>
          <a href="<?php echo esc_url(home_url('/contato/')); ?>">Contato</a>
        </div>
      </div>
    </div>
    <div class="foot-bot">
      <span>© <?php echo date('Y'); ?> Rickytech · Central de Artigos</span>
      <span style="display:inline-flex;gap:14px">
        <a href="<?php echo esc_url(get_feed_link()); ?>" aria-label="RSS"><?php rt_render_icon('rss', 16); ?></a>
        <a href="https://x.com/rickytech" target="_blank" rel="noopener" aria-label="X"><?php rt_render_icon('twitter', 16); ?></a>
      </span>
    </div>
  </div>
</footer>

<?php get_template_part('template-parts/search-overlay'); ?>
<?php get_template_part('template-parts/tweaks-panel'); ?>

<?php wp_footer(); ?>
</body>
</html>
