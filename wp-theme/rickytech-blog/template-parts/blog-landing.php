<?php
/**
 * Blog landing: featured hero + category chips + recent grid + feed + sidebar.
 * Shared by front-page.php and home.php.
 *
 * @package Rickytech
 */

$featured     = rickytech_featured_post();
$featured_id  = $featured ? $featured->ID : 0;
$current_cat  = is_category() ? get_queried_object_id() : 0;

// Recent posts for the grid + feed (exclude the featured one).
$recent = new WP_Query( array(
	'posts_per_page'      => 12,
	'post__not_in'        => $featured_id ? array( $featured_id ) : array(),
	'ignore_sticky_posts' => true,
) );
$posts_list = $recent->posts;
$grid = array_slice( $posts_list, 0, 6 );
$feed = array_slice( $posts_list, 6, 6 );
?>

<?php if ( $featured ) : ?>
	<div class="container-wide">
		<?php get_template_part( 'template-parts/featured-hero', null, array( 'post' => $featured ) ); ?>
	</div>
<?php endif; ?>

<div class="container-wide" style="padding-top:28px">
	<div class="chips" style="margin-bottom:8px">
		<a class="chip<?php echo ( ! $current_cat ) ? ' on' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Todos', 'rickytech' ); ?></a>
		<?php foreach ( get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true, 'number' => 8 ) ) as $cat ) : ?>
			<a class="chip<?php echo ( $current_cat === $cat->term_id ) ? ' on' : ''; ?>" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
		<?php endforeach; ?>
	</div>
</div>

<div class="container-wide">
	<div class="sec" style="padding-bottom:24px">
		<div class="home-cols">
			<div>
				<div class="sec-head">
					<h2><?php esc_html_e( 'Mais recentes', 'rickytech' ); ?></h2>
				</div>

				<?php if ( $grid ) : ?>
					<div class="card-grid">
						<?php foreach ( $grid as $post ) : setup_postdata( $post ); ?>
							<?php get_template_part( 'template-parts/card' ); ?>
						<?php endforeach; wp_reset_postdata(); ?>
					</div>
				<?php else : ?>
					<div class="loop-empty"><?php esc_html_e( 'Nenhum artigo publicado ainda.', 'rickytech' ); ?></div>
				<?php endif; ?>

				<?php if ( $feed ) : ?>
					<div style="margin-top:56px">
						<div class="sec-head"><h2><?php esc_html_e( 'Continue lendo', 'rickytech' ); ?></h2></div>
						<div class="feed">
							<?php foreach ( $feed as $post ) : setup_postdata( $post ); ?>
								<?php get_template_part( 'template-parts/feed-item' ); ?>
							<?php endforeach; wp_reset_postdata(); ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $recent->found_posts > 12 ) : ?>
					<div style="margin-top:48px">
						<a class="btn btn-ghost" href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ?: home_url( '/' ) ); ?>" style="justify-content:center;width:100%">
							<?php esc_html_e( 'Ver todos os artigos', 'rickytech' ); ?> <?php rickytech_icon( 'arrowRight', 15 ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>
</div>
