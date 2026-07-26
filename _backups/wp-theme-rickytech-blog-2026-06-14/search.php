<?php get_header(); ?>

<?php
$query  = get_search_query();
$posts  = $query ? get_posts(['s' => $query, 'numberposts' => 20, 'post_status' => 'publish']) : [];
$count  = count($posts);
$popular_tags = ['Agentes', 'RAG', 'Performance', 'Observabilidade', '.NET', 'Carreira', 'CSS'];
?>

<div class="view-enter">
  <div class="container-wide">

    <div class="sec" style="padding-bottom:24px">
      <div class="eyebrow-lbl" style="margin-bottom:14px">Busca</div>
      <form method="get" action="<?php echo esc_url(home_url('/')); ?>" style="position:relative;max-width:640px">
        <span style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--fg-tertiary)">
          <?php rt_render_icon('search', 20); ?>
        </span>
        <input class="input" type="search" name="s" autofocus
               style="height:54px;padding-left:48px;font-size:17px"
               value="<?php echo esc_attr($query); ?>"
               placeholder="Buscar por título, tema, tag ou autor…">
      </form>
      <div style="margin-top:14px;font-family:var(--font-mono);font-size:13px;color:var(--fg-tertiary)">
        <?php if ($query): ?>
          <?php echo esc_html($count); ?> resultado<?php echo $count !== 1 ? 's' : ''; ?> para "<?php echo esc_html($query); ?>"
        <?php else: ?>
          Digite para buscar entre os artigos
        <?php endif; ?>
      </div>
    </div>

    <?php if (!$query): ?>
    <div class="sec" style="padding-top:0">
      <div class="eyebrow-lbl" style="margin-bottom:14px">Buscas populares</div>
      <div class="chips">
        <?php foreach ($popular_tags as $tag): ?>
          <a href="<?php echo esc_url(home_url('/?s=' . urlencode($tag))); ?>" class="chip">
            <?php echo esc_html($tag); ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($query): ?>
    <div class="sec" style="padding-top:0">
      <?php if (empty($posts)): ?>
        <div style="padding:60px 0;text-align:center;color:var(--fg-tertiary)">
          Nenhum artigo encontrado. Tente outro termo.
        </div>
      <?php else: ?>
        <div class="card-grid">
          <?php foreach ($posts as $post):
            get_template_part('template-parts/article-card', null, ['post' => $post]);
          endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>
