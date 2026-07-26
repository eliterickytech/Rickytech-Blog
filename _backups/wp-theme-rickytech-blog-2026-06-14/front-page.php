<?php get_header(); ?>

<?php
$featured  = rt_get_featured_post();
$cat_order = rt_get_all_cats();

// Grid: up to 6 posts (excluding featured)
$grid_posts = get_posts([
    'numberposts'  => 6,
    'post_status'  => 'publish',
    'post__not_in' => $featured ? [$featured->ID] : [],
]);

// Feed: next 6
$feed_posts = get_posts([
    'numberposts'  => 6,
    'post_status'  => 'publish',
    'post__not_in' => $featured ? array_merge([$featured->ID], wp_list_pluck($grid_posts, 'ID')) : wp_list_pluck($grid_posts, 'ID'),
    'offset'       => 0,
]);
?>

<div class="view-enter">

  <!-- FEATURED HERO -->
  <?php if ($featured):
    $cat_cfg   = rt_get_post_cat_config($featured->ID);
    $author    = rt_get_author_data((int) $featured->post_author);
    $lede      = rt_get_lede($featured->ID);
    $read_time = rt_get_read_time($featured->ID);
    $post_url  = get_permalink($featured->ID);
  ?>
  <div class="container-wide">
    <section class="feat" style="--cat-ink:<?php echo esc_attr($cat_cfg['ink']); ?>">
      <a href="<?php echo esc_url($post_url); ?>" class="thumb-wrap" style="display:block">
        <?php rt_render_thumb($featured); ?>
      </a>
      <div>
        <span class="eyebrow-pill">
          <?php rt_render_icon('bolt', 13, true); ?> Em destaque
        </span>
        <h1 onclick="location.href='<?php echo esc_js($post_url); ?>'"><?php echo esc_html($featured->post_title); ?></h1>
        <p class="lede"><?php echo esc_html($lede); ?></p>
        <div class="feat-foot">
          <?php rt_render_avatar($author, 38); ?>
          <div>
            <div class="who"><?php echo esc_html($author['name']); ?></div>
            <div class="when">
              <?php echo esc_html(date_i18n('d M Y', strtotime($featured->post_date))); ?>
              · <?php echo esc_html($read_time); ?> min de leitura
            </div>
          </div>
          <a href="<?php echo esc_url($post_url); ?>" class="btn btn-ghost btn-sm" style="margin-left:auto">
            Ler artigo <?php rt_render_icon('arrowRight', 15); ?>
          </a>
        </div>
      </div>
    </section>
  </div>
  <?php endif; ?>

  <!-- CATEGORY FILTER (JS-driven) -->
  <div class="container-wide" style="padding-top:28px">
    <div class="chips" id="rt-cat-filter" style="margin-bottom:8px">
      <button class="chip on" data-cat="all" type="button">Todos</button>
      <?php foreach ($cat_order as $cat_slug):
        $cfg = rt_get_cat_config($cat_slug);
      ?>
        <button class="chip" data-cat="<?php echo esc_attr($cat_slug); ?>" type="button">
          <?php echo esc_html($cfg['short']); ?>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- GRID + SIDEBAR -->
  <div class="container-wide">
    <div class="sec" style="padding-bottom:24px">
      <div class="home-cols">
        <div>
          <div class="sec-head" id="rt-grid-head">
            <h2>Mais recentes</h2>
          </div>
          <div class="card-grid" id="rt-card-grid">
            <?php foreach ($grid_posts as $post):
              get_template_part('template-parts/article-card', null, ['post' => $post]);
            endforeach; ?>
          </div>

          <?php if (!empty($feed_posts)): ?>
          <div style="margin-top:56px">
            <div class="sec-head"><h2>Continue lendo</h2></div>
            <div class="feed" id="rt-feed">
              <?php foreach ($feed_posts as $post):
                get_template_part('template-parts/feed-item', null, ['post' => $post]);
              endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <aside class="sidebar">
          <?php get_template_part('template-parts/sidebar'); ?>
        </aside>
      </div>
    </div>
  </div>

</div><!-- .view-enter -->

<?php get_footer(); ?>
