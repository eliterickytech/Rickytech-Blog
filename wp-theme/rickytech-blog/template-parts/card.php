<?php
/**
 * Article card (grid). Use inside the loop.
 *
 * @package Rickytech
 */

$term   = rickytech_primary_category();
$colors = rickytech_cat_colors( $term );
$author = get_the_author_meta( 'ID' );
?>
<article class="acard" style="--cat-ink:<?php echo esc_attr( $colors['ink'] ); ?>" data-href="<?php the_permalink(); ?>">
	<?php rickytech_thumb(); ?>
	<div class="cat-bar"></div>
	<div class="meta-row">
		<?php if ( $term ) : ?>
			<a class="cat-tag" href="<?php echo esc_url( get_category_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
			<span class="dot-sep"></span>
		<?php endif; ?>
		<span><?php echo esc_html( rickytech_reading_time() ); ?> min</span>
		<span class="dot-sep"></span>
		<span><?php echo esc_html( rickytech_rel_date() ); ?></span>
	</div>
	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<p><?php echo esc_html( get_the_excerpt() ); ?></p>
	<div class="byline">
		<?php rickytech_avatar( $author, 24 ); ?>
		<span class="name"><?php the_author(); ?></span>
	</div>
</article>
