<?php get_header(); ?>

<?php while (have_posts()): the_post();
  $post      = get_post();
  $cat_cfg   = rt_get_post_cat_config($post->ID);
  $author    = rt_get_author_data((int) $post->post_author);
  $lede      = rt_get_lede($post->ID);
  $read_time = rt_get_read_time($post->ID);
  $tags      = get_the_tags($post->ID) ?: [];
  $related   = rt_get_related_posts($post, 3);

  // Generate TOC from headings in content
  $content = get_the_content();
  preg_match_all('/<h2[^>]*id="([^"]*)"[^>]*>(.*?)<\/h2>/i', apply_filters('the_content', $content), $h2_matches);
  // Also detect headings without IDs and auto-assign
  $headings = [];
  preg_match_all('/<h2[^>]*>(.*?)<\/h2>/i', apply_filters('the_content', $content), $raw_h2);
  foreach ($raw_h2[1] as $idx => $text) {
    $clean = strip_tags($text);
    $id    = 'h-' . $idx . '-' . sanitize_title($clean);
    $headings[] = ['id' => $id, 'text' => $clean];
  }
?>

<div class="view-enter" style="--cat-ink:<?php echo esc_attr($cat_cfg['ink']); ?>">

  <!-- READ PROGRESS -->
  <div class="read-progress" id="rt-read-progress" style="width:0%"></div>

  <!-- ARTICLE HERO -->
  <div class="container-read">
    <div class="art-hero">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="art-back">
        <?php rt_render_icon('arrowLeft', 15); ?> Voltar
      </a>
      <div>
        <?php
        $term = get_the_terms($post->ID, 'category');
        $cat_url = ($term && !is_wp_error($term)) ? get_term_link($term[0]) : home_url('/');
        ?>
        <a href="<?php echo esc_url($cat_url); ?>" class="art-cat">
          <?php echo esc_html($cat_cfg['name']); ?>
        </a>
      </div>
      <h1 class="art-title"><?php the_title(); ?></h1>
      <?php if ($lede): ?>
        <p class="art-lede"><?php echo esc_html($lede); ?></p>
      <?php endif; ?>
      <div class="art-byline">
        <a href="<?php echo esc_url($author['url']); ?>">
          <?php rt_render_avatar($author, 46); ?>
        </a>
        <div>
          <a class="who" href="<?php echo esc_url($author['url']); ?>"><?php echo esc_html($author['name']); ?></a>
          <div class="sub"><?php echo esc_html($author['role']); ?></div>
        </div>
        <div class="bsep"></div>
        <div>
          <div class="sub" style="margin-bottom:2px"><?php echo esc_html(date_i18n('d M Y', strtotime($post->post_date))); ?></div>
          <div class="sub" style="display:inline-flex;align-items:center;gap:5px">
            <?php rt_render_icon('clock', 13); ?> <?php echo esc_html($read_time); ?> min de leitura
          </div>
        </div>
        <div class="art-actions">
          <button class="icon-btn" id="rt-share-btn" title="Compartilhar">
            <?php rt_render_icon('share', 17); ?>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- COVER IMAGE -->
  <div class="container-read">
    <div class="art-cover">
      <?php rt_render_thumb($post, false); ?>
    </div>
  </div>

  <!-- ARTICLE LAYOUT: TOC | PROSE | RAIL -->
  <div class="art-layout">

    <!-- TOC -->
    <aside class="toc" id="rt-toc" aria-label="Índice do artigo">
      <div class="toc-lbl">Neste artigo</div>
      <ul>
        <?php foreach ($headings as $h): ?>
          <li>
            <a data-heading-id="<?php echo esc_attr($h['id']); ?>"
               onclick="rtScrollToHeading('<?php echo esc_js($h['id']); ?>')">
              <?php echo esc_html($h['text']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>

    <!-- PROSE -->
    <main id="rt-prose-body">
      <div class="prose">
        <?php
        // Auto-inject IDs into h2 headings
        $filtered_content = apply_filters('the_content', $content);
        $idx = 0;
        $filtered_content = preg_replace_callback('/<h2([^>]*)>(.*?)<\/h2>/i', function ($m) use (&$idx) {
          $clean = strip_tags($m[2]);
          $id    = 'h-' . $idx . '-' . sanitize_title($clean);
          $idx++;
          return "<h2 id=\"{$id}\"{$m[1]}>{$m[2]}</h2>";
        }, $filtered_content);
        echo $filtered_content;
        ?>
      </div>
    </main>

    <!-- RIGHT RAIL -->
    <aside class="art-rail" aria-label="Ações do artigo">
      <button class="rail-btn" id="rt-rail-share" type="button">
        <?php rt_render_icon('link', 17); ?> <span>Link</span>
      </button>
      <a href="https://x.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>"
         target="_blank" rel="noopener" class="rail-btn">
        <?php rt_render_icon('twitter', 17); ?> <span>Postar</span>
      </a>
    </aside>

  </div><!-- .art-layout -->

  <!-- ARTICLE FOOTER -->
  <div class="art-foot">
    <?php if ($tags): ?>
    <div class="tag-row">
      <?php foreach ($tags as $tag): ?>
        <a href="<?php echo esc_url(get_tag_link($tag)); ?>" class="chip">#<?php echo esc_html($tag->name); ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="author-box">
      <?php rt_render_avatar($author, 64); ?>
      <div>
        <a class="ab-name" href="<?php echo esc_url($author['url']); ?>"><?php echo esc_html($author['name']); ?></a>
        <div class="ab-role"><?php echo esc_html($author['role']); ?><?php if ($author['x_handle']): ?> · <?php echo esc_html($author['x_handle']); ?><?php endif; ?></div>
        <p class="ab-bio"><?php echo esc_html($author['bio']); ?></p>
      </div>
    </div>
  </div>

  <!-- RELATED POSTS -->
  <?php if (!empty($related)): ?>
  <div class="related">
    <div class="container-wide">
      <div class="sec-head">
        <h2>Continue lendo</h2>
        <a class="see-all" href="<?php echo esc_url($cat_url); ?>">mais de <?php echo esc_html($cat_cfg['short']); ?> →</a>
      </div>
      <div class="card-grid">
        <?php foreach ($related as $rel_post):
          get_template_part('template-parts/article-card', null, ['post' => $rel_post]);
        endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

</div><!-- .view-enter -->

<?php endwhile; ?>

<script>
function rtScrollToHeading(id) {
  const el = document.getElementById(id);
  if (el) window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 84, behavior: 'smooth' });
}
</script>

<?php get_footer(); ?>
