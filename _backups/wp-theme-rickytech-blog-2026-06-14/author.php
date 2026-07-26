<?php get_header(); ?>

<?php
$user      = get_queried_object();
$author    = rt_get_author_data($user->ID);
$posts     = get_posts([
    'author'        => $user->ID,
    'post_status'   => 'publish',
    'posts_per_page' => -1,
    'orderby'       => 'date',
    'order'         => 'DESC',
]);
$total_count = count_user_posts($user->ID, 'post');
$since_year  = date('Y', strtotime($user->user_registered));
?>

<div class="view-enter">

  <div class="container-wide">
    <div class="author-hero">
      <?php rt_render_avatar($author, 96); ?>
      <div>
        <h1><?php echo esc_html($author['name']); ?></h1>
        <div class="role"><?php echo esc_html($author['role']); ?></div>
        <p class="bio"><?php echo esc_html($author['bio']); ?></p>
        <div class="author-stats">
          <div class="st">
            <span class="n"><?php echo esc_html($total_count); ?></span>
            <div class="l">artigos</div>
          </div>
          <div class="st">
            <span class="n"><?php echo esc_html(count($posts)); ?></span>
            <div class="l">na central</div>
          </div>
          <div class="st">
            <span class="n">desde <?php echo esc_html($since_year); ?></span>
            <div class="l"><?php echo esc_html($author['x_handle']); ?></div>
          </div>
        </div>
      </div>
      <?php if ($author['x_handle']): ?>
      <div style="margin-left:auto;display:flex;gap:8px">
        <a href="https://x.com/<?php echo esc_attr(ltrim($author['x_handle'], '@')); ?>"
           target="_blank" rel="noopener" class="btn btn-ghost btn-sm">
          <?php rt_render_icon('twitter', 15); ?> Seguir
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="container-wide">
    <div class="sec">
      <div class="sec-head">
        <h2>Artigos de <?php echo esc_html(explode(' ', $author['name'])[0]); ?></h2>
      </div>
      <?php if (empty($posts)): ?>
        <p style="color:var(--fg-tertiary);padding:40px 0;text-align:center">Nenhum artigo publicado ainda.</p>
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
