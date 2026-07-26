<?php
$post      = $args['post'] ?? null;
if (!$post) return;
$cat_cfg   = rt_get_post_cat_config($post->ID);
$author    = rt_get_author_data((int) $post->post_author);
$lede      = rt_get_lede($post->ID);
$read_time = rt_get_read_time($post->ID);
$post_url  = get_permalink($post->ID);
$ink       = esc_attr($cat_cfg['ink']);
$fi_term   = get_the_terms($post->ID, 'category');
$fi_cat    = ($fi_term && !is_wp_error($fi_term)) ? $fi_term[0]->slug : '';
?>
<article class="feed-item" style="--cat-ink:<?php echo $ink; ?>"
         data-cat="<?php echo esc_attr($fi_cat); ?>"
         onclick="location.href='<?php echo esc_js($post_url); ?>'">
  <?php rt_render_thumb($post); ?>
  <div class="fi-body">
    <div class="meta-row">
      <span class="cat-tag"><?php echo esc_html($cat_cfg['short']); ?></span>
      <span class="dot-sep"></span>
      <span><?php echo esc_html($read_time); ?> min de leitura</span>
    </div>
    <h3><?php echo esc_html($post->post_title); ?></h3>
    <p><?php echo esc_html($lede); ?></p>
    <div class="byline">
      <?php rt_render_avatar($author, 22); ?>
      <span class="name"><?php echo esc_html($author['name']); ?></span>
      <span class="dot-sep"></span>
      <span class="name"><?php echo esc_html(rt_rel_date($post->post_date)); ?></span>
    </div>
  </div>
</article>
