<?php
/**
 * Header: <head>, topbar, and search overlay.
 *
 * @package Rickytech
 */
?>
<!doctype html>
<html <?php language_attributes(); ?> data-theme="dark">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<script>/* no-flash theme */(function(){try{var m=localStorage.getItem('rt-theme');if(m){document.documentElement.setAttribute('data-theme',m);}}catch(e){}})();</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content"><?php esc_html_e( 'Pular para o conteúdo', 'rickytech' ); ?></a>

<header class="topbar">
	<div class="topbar-inner">
		<?php if ( has_custom_logo() ) : ?>
			<div class="brand"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img class="mark" src="<?php echo esc_url( get_template_directory_uri() . '/assets/logos/ricky-mark-transparent.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
				<span class="wm">Central de <span class="accent">Artigos</span></span>
			</a>
		<?php endif; ?>

		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => 'nav',
				'container_class'=> 'topnav',
				'menu_class'     => '',
				'items_wrap'     => '%3$s', // links only, styled by .topnav a
				'depth'          => 1,
				'fallback_cb'    => false,
			) );
		} else {
			rickytech_nav_fallback();
		}
		?>

		<div class="topbar-spacer"></div>

		<div class="topbar-actions">
			<button class="search-trigger" type="button" data-search-open aria-label="<?php esc_attr_e( 'Buscar artigos', 'rickytech' ); ?>">
				<?php rickytech_icon( 'search', 16 ); ?>
				<span><?php esc_html_e( 'Buscar artigos…', 'rickytech' ); ?></span>
				<span class="kbd">⌘K</span>
			</button>
			<button class="icon-btn" type="button" data-theme-toggle title="<?php esc_attr_e( 'Alternar tema', 'rickytech' ); ?>" aria-label="<?php esc_attr_e( 'Alternar tema', 'rickytech' ); ?>">
				<?php rickytech_icon( 'sun', 18 ); ?>
			</button>
			<a class="btn btn-primary btn-sm" href="<?php echo esc_url( rickytech_subscribe_url() ); ?>"><?php esc_html_e( 'Assinar', 'rickytech' ); ?></a>
			<button class="icon-btn nav-toggle" type="button" data-nav-toggle aria-label="<?php esc_attr_e( 'Menu', 'rickytech' ); ?>">
				<?php rickytech_icon( 'menu', 18 ); ?>
			</button>
		</div>
	</div>
</header>

<div class="search-overlay hide" id="rt-search" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Buscar', 'rickytech' ); ?>">
	<div class="search-scrim" data-search-close></div>
	<div class="search-panel">
		<form class="search-input-row" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php rickytech_icon( 'search', 20 ); ?>
			<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Buscar por título, tema ou autor…', 'rickytech' ); ?>" autocomplete="off">
			<button type="button" class="esc" data-search-close><?php esc_html_e( 'ESC', 'rickytech' ); ?></button>
		</form>
		<div class="search-hint">
			<span><?php esc_html_e( 'Pressione', 'rickytech' ); ?> <span class="k">↵</span><?php esc_html_e( 'para buscar', 'rickytech' ); ?></span>
			<span><span class="k">esc</span><?php esc_html_e( 'fechar', 'rickytech' ); ?></span>
		</div>
	</div>
</div>

<main id="content">
