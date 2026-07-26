<?php
/**
 * Author page — profile hero + their articles.
 *
 * @package Rickytech
 */

get_header();

$author_id = get_queried_object_id();
$since     = get_the_author_meta( 'user_registered', $author_id );
$since_y   = $since ? gmdate( 'Y', strtotime( $since ) ) : '';
$count     = count_user_posts( $author_id, 'post', true );
?>
<div class="view-enter">
	<div class="container-wide">
		<div class="author-hero">
			<?php rickytech_avatar( $author_id, 96 ); ?>
			<div>
				<h1><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></h1>
				<?php $url = get_the_author_meta( 'user_url', $author_id ); ?>
				<div class="role"><?php echo $url ? esc_html( $url ) : esc_html__( 'Autor na Rickytech', 'rickytech' ); ?></div>
				<?php if ( get_the_author_meta( 'description', $author_id ) ) : ?>
					<p class="bio"><?php echo esc_html( get_the_author_meta( 'description', $author_id ) ); ?></p>
				<?php endif; ?>
				<div class="author-stats">
					<div class="st"><span class="n"><?php echo esc_html( $count ); ?></span><div class="l"><?php esc_html_e( 'artigos', 'rickytech' ); ?></div></div>
					<?php if ( $since_y ) : ?>
						<div class="st"><span class="n"><?php printf( esc_html__( 'desde %s', 'rickytech' ), esc_html( $since_y ) ); ?></span><div class="l"><?php esc_html_e( 'na central', 'rickytech' ); ?></div></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="container-wide">
		<div class="sec">
			<div class="sec-head"><h2><?php printf( esc_html__( 'Artigos de %s', 'rickytech' ), esc_html( get_the_author_meta( 'first_name', $author_id ) ?: get_the_author_meta( 'display_name', $author_id ) ) ); ?></h2></div>
			<?php if ( have_posts() ) : ?>
				<div class="card-grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'template-parts/card' ); ?>
					<?php endwhile; ?>
				</div>
				<?php rickytech_pagination(); ?>
			<?php else : ?>
				<div class="loop-empty"><?php esc_html_e( 'Este autor ainda não publicou artigos.', 'rickytech' ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
get_footer();
