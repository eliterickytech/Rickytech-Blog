<?php get_header(); ?>

<?php
$term    = get_queried_object();
$cat_cfg = rt_get_cat_config($term->slug);
$sort    = sanitize_key($_GET['sort'] ?? 'recent');

$args = [
    'cat'          => $term->term_id,
    'post_status'  => 'publish',
    'posts_per_page' => -1,
];
if ($sort === 'popular') {
    $args['orderby'] = 'comment_count';
    $args['order']   = 'DESC';
} else {
    $args['orderby'] = 'date';
    $args['order']   = 'DESC';
}
$posts = get_posts($args);
$count = count($posts);
$ink   = esc_attr($cat_cfg['ink']);
$deep  = esc_attr($cat_cfg['deep']);
?>

<div class="view-enter">

  <div class="cat-hero" style="background:linear-gradient(180deg,<?php echo $deep; ?>55,transparent)">
    <div class="container-wide">
      <span class="cat-badge" style="background:color-mix(in oklch,<?php echo $ink; ?> 16%,transparent);color:<?php echo $ink; ?>;border:1px solid color-mix(in oklch,<?php echo $ink; ?> 30%,transparent)">
        <?php echo esc_html($cat_cfg['glyph']); ?> <?php echo esc_html($cat_cfg['short']); ?>
      </span>
      <h1><?php echo esc_html($cat_cfg['name']); ?></h1>
      <p><?php echo esc_html($cat_cfg['blurb']); ?></p>
      <div style="margin-top:18px;font-family:var(--font-mono);font-size:13px;color:var(--fg-tertiary)">
        <?php echo esc_html($count); ?> artigos
      </div>
    </div>
  </div>

  <div class="container-wide">
    <div class="sec">
      <div class="sec-head">
        <div class="chips">
          <a href="<?php echo esc_url(get_term_link($term)); ?>"
             class="chip <?php echo $sort === 'recent' ? 'on' : ''; ?>">Recentes</a>
          <a href="<?php echo esc_url(add_query_arg('sort', 'popular', get_term_link($term))); ?>"
             class="chip <?php echo $sort === 'popular' ? 'on' : ''; ?>">Mais lidos</a>
        </div>
      </div>
      <?php if (empty($posts)): ?>
        <p style="color:var(--fg-tertiary);padding:40px 0;text-align:center">Nenhum artigo ainda. Em breve!</p>
      <?php else: ?>
        <div class="card-grid">
          <?php foreach ($posts as $post):
            get_template_part('template-parts/article-card', null, ['post' => $post]);
          endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php get_footer(); ?>
