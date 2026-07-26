<?php
/**
 * Rickytech — Central de Artigos theme functions.
 *
 * @package Rickytech
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'RICKYTECH_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/helpers.php';

/**
 * Theme setup.
 */
function rickytech_setup() {
	load_theme_textdomain( 'rickytech', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 64,
		'width'       => 64,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script',
	) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	register_nav_menus( array(
		'primary' => __( 'Menu principal', 'rickytech' ),
		'footer'  => __( 'Menu do rodapé', 'rickytech' ),
	) );
}
add_action( 'after_setup_theme', 'rickytech_setup' );

/**
 * Enqueue styles and scripts.
 */
function rickytech_assets() {
	$dir = get_template_directory_uri();

	wp_enqueue_style( 'rickytech-tokens', $dir . '/assets/css/colors_and_type.css', array(), RICKYTECH_VERSION );
	wp_enqueue_style( 'rickytech-blog', $dir . '/assets/css/blog.css', array( 'rickytech-tokens' ), RICKYTECH_VERSION );
	// WordPress requires style.css to be loaded for the theme header.
	wp_enqueue_style( 'rickytech-style', get_stylesheet_uri(), array( 'rickytech-blog' ), RICKYTECH_VERSION );

	wp_enqueue_script( 'rickytech-thumbs', $dir . '/assets/js/thumbs.js', array(), RICKYTECH_VERSION, true );
	wp_enqueue_script( 'rickytech-theme', $dir . '/assets/js/theme.js', array(), RICKYTECH_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'rickytech_assets' );

/**
 * Image size for thumbnails/cards.
 */
add_action( 'after_setup_theme', function () {
	set_post_thumbnail_size( 1200, 675, true ); // 16:9-ish.
} );

/**
 * Excerpt tuning to feed the card "lede".
 */
add_filter( 'excerpt_length', function () { return 26; } );
add_filter( 'excerpt_more', function () { return '…'; } );

/**
 * Category archive sorting (?orderby=popular → most commented).
 */
function rickytech_archive_sort( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) { return; }
	if ( ( $query->is_category() || $query->is_home() ) && isset( $_GET['orderby'] ) && 'popular' === $_GET['orderby'] ) {
		$query->set( 'orderby', 'comment_count' );
		$query->set( 'order', 'DESC' );
	}
}
add_action( 'pre_get_posts', 'rickytech_archive_sort' );

/**
 * Primary nav fallback when no menu is assigned: Início + top categories.
 */
function rickytech_nav_fallback() {
	echo '<nav class="topnav" aria-label="' . esc_attr__( 'Principal', 'rickytech' ) . '">';
	printf( '<a href="%s">%s</a>', esc_url( home_url( '/' ) ), esc_html__( 'Início', 'rickytech' ) );
	$cats = get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 4, 'hide_empty' => true ) );
	foreach ( $cats as $cat ) {
		printf( '<a href="%s">%s</a>', esc_url( get_category_link( $cat ) ), esc_html( $cat->name ) );
	}
	echo '</nav>';
}

/**
 * Pagination markup matching the design.
 */
function rickytech_pagination() {
	$links = paginate_links( array(
		'type'      => 'array',
		'prev_text' => '←',
		'next_text' => '→',
	) );
	if ( ! $links ) { return; }
	echo '<nav class="pagination" aria-label="' . esc_attr__( 'Paginação', 'rickytech' ) . '">';
	foreach ( $links as $link ) { echo $link; } // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</nav>';
}

/**
 * Custom comment renderer (kept simple, styled by the theme).
 */
function rickytech_comment( $comment, $args, $depth ) {
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<div class="comment-body">
			<div class="comment-author"><?php comment_author(); ?></div>
			<div class="comment-meta"><?php printf( '%1$s · %2$s', get_comment_date(), get_comment_time() ); ?></div>
			<?php comment_text(); ?>
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div>
	<?php
	// Note: WP closes the <li> for us.
}
