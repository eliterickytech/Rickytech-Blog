<?php
/**
 * Featured hero. Expects $args['post'] (a WP_Post).
 *
 * @package Rickytech
 */

$fp = isset( $args['post'] ) ? $args['post'] : null;
if ( ! $fp ) { return; }

$pid    = $fp->ID;
$term   = rickytech_primary_category( $pid );
$colors = rickytech_cat_colors( $term );
?>
<section class="feat" style="--cat-ink:<?php echo esc_attr( $colors['ink'] ); ?>">
	<a class="thumb-wrap" href="<?php echo esc_url( get_permalink( $pid ) ); ?>" style="cursor:pointer">
		<?php rickytech_thumb( $pid ); ?>
	</a>
	<div>
		<span class="eyebrow-pill"><?php rickytech_icon( 'bolt', 13, true ); ?> <?php esc_html_e( 'Em destaque', 'rickytech' ); ?></span>
		<h1><a href="<?php echo esc_url( get_permalink( $pid ) ); ?>" style="color:inherit"><?php echo esc_html( get_the_title( $pid ) ); ?></a></h1>
		<p class="lede"><?php echo esc_html( get_the_excerpt( $pid ) ); ?></p>
		<div class="feat-foot">
			<?php rickytech_avatar( $fp->post_author, 38 ); ?>
			<div>
				<div class="who"><?php echo esc_html( get_the_author_meta( 'display_name', $fp->post_author ) ); ?></div>
				<div class="when"><?php echo esc_html( get_the_date( 'd M Y', $pid ) ); ?> · <?php echo esc_html( rickytech_reading_time( $pid ) ); ?> min de leitura</div>
			</div>
			<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( get_permalink( $pid ) ); ?>" style="margin-left:auto">
				<?php esc_html_e( 'Ler artigo', 'rickytech' ); ?> <?php rickytech_icon( 'arrowRight', 15 ); ?>
			</a>
		</div>
	</div>
</section>
