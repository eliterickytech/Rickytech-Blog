<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="dark" data-accent="violet" data-density="regular" data-cards="image">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
$cat_order = rt_get_all_cats();
$current_route = is_category() ? get_queried_object()->slug : (is_home() || is_front_page() ? 'home' : '');
?>

<header class="topbar" id="rt-topbar">
  <div class="topbar-inner">

    <a href="<?php echo esc_url(home_url('/')); ?>" class="brand">
      <img class="mark"
           src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/logos/ricky-mark-transparent.png"
           alt="<?php bloginfo('name'); ?>">
      <span class="wm">Central de <span class="accent">Artigos</span></span>
    </a>

    <nav class="topnav" aria-label="Categorias">
      <a href="<?php echo esc_url(home_url('/')); ?>"
         class="<?php echo ($current_route === 'home') ? 'active' : ''; ?>">Início</a>
      <?php foreach (array_slice($cat_order, 0, 4) as $cat_slug):
        $cfg  = rt_get_cat_config($cat_slug);
        $term = get_term_by('slug', $cat_slug, 'category');
        $url  = $term ? get_term_link($term) : '#';
        $active = ($current_route === $cat_slug) ? 'active' : '';
      ?>
        <a href="<?php echo esc_url($url); ?>" class="<?php echo $active; ?>"><?php echo esc_html($cfg['short']); ?></a>
      <?php endforeach; ?>
    </nav>

    <div class="topbar-spacer"></div>

    <div class="topbar-actions">
      <button class="search-trigger" id="rt-search-trigger" type="button" aria-label="Buscar artigos">
        <?php rt_render_icon('search', 16); ?>
        <span>Buscar artigos…</span>
        <span class="kbd">⌘K</span>
      </button>

      <button class="icon-btn" id="rt-theme-toggle" type="button" title="Alternar tema" aria-label="Alternar tema">
        <?php rt_render_icon('sun', 18); ?>
      </button>

      <?php
      $nl_page = get_page_by_path('newsletter');
      $nl_url  = $nl_page ? get_permalink($nl_page) : home_url('/newsletter/');
      ?>
      <a href="<?php echo esc_url($nl_url); ?>" class="btn btn-primary btn-sm">Assinar</a>
    </div>

  </div>
</header>
