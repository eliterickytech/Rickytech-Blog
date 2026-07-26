<?php
/**
 * Feed item (horizontal list row). Use inside the loop.
 *
 * @package Rickytech
 */

$term   = rickytech_primary_category();
$colors = rickytech_cat_colors( $term );
$author = get_the_author_meta( 'ID' );
?>
<article class="feed-item" style="--cat-ink:<?php echo esc_attr( $colors['ink'] ); ?>" data-href="<?php the_permalink(); ?>">
	<?php rickytech_thumb(); ?>
	<div class="fi-body">
		<div class="meta-row">
			<?php if ( $term ) : ?>
				<span class="cat-tag"><?php echo esc_html( $term->name ); ?></span>
				<span class="dot-sep"></span>
			<?php endif; ?>
			<span><?php echo esc_html( rickytech_reading_time() ); ?> min de leitura</span>
		</div>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html( get_the_excerpt() ); ?></p>
		<div class="byline">
			<?php rickytech_avatar( $author, 22 ); ?>
			<span class="name"><?php the_author(); ?></span>
			<span class="dot-sep"></span>
			<span class="name"><?php echo esc_html( rickytech_rel_date() ); ?></span>
		</div>
	</div>
</article>
