<?php
$post      = $args['post'] ?? null;
if (!$post) return;
$cat_cfg   = rt_get_post_cat_config($post->ID);
$author    = rt_get_author_data((int) $post->post_author);
$lede      = rt_get_lede($post->ID);
$read_time = rt_get_read_time($post->ID);
$post_url  = get_permalink($post->ID);
$cat_url   = '';
$term      = get_the_terms($post->ID, 'category');
if ($term && !is_wp_error($term)) $cat_url = get_term_link($term[0]);
$ink       = esc_attr($cat_cfg['ink']);
?>
<article class="acard" style="--cat-ink:<?php echo $ink; ?>"
         data-cat="<?php echo esc_attr($term && !is_wp_error($term) ? $term[0]->slug : ''); ?>"
         onclick="location.href='<?php echo esc_js($post_url); ?>'"><?php // data-cat used by JS home filter ?>
  <?php rt_render_thumb($post); ?>
  <div class="cat-bar"></div>
  <div class="meta-row">
    <a class="cat-tag" href="<?php echo esc_url($cat_url); ?>"
       onclick="event.stopPropagation()"><?php echo esc_html($cat_cfg['short']); ?></a>
    <span class="dot-sep"></span>
    <span><?php echo esc_html($read_time); ?> min</span>
    <span class="dot-sep"></span>
    <span><?php echo esc_html(rt_rel_date($post->post_date)); ?></span>
  </div>
  <h3><?php echo esc_html($post->post_title); ?></h3>
  <p><?php echo esc_html($lede); ?></p>
  <div class="byline">
    <?php rt_render_avatar($author, 24); ?>
    <span class="name"><?php echo esc_html($author['name']); ?></span>
  </div>
</article>
