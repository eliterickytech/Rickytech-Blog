<?php
/**
 * Fallback index — generic post listing grid.
 *
 * @package Rickytech
 */

get_header();
?>
<div class="view-enter container-wide">
	<div class="sec">
		<div class="sec-head"><h2><?php esc_html_e( 'Artigos', 'rickytech' ); ?></h2></div>
		<?php if ( have_posts() ) : ?>
			<div class="card-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/card' ); ?>
				<?php endwhile; ?>
			</div>
			<?php rickytech_pagination(); ?>
		<?php else : ?>
			<div class="loop-empty"><?php esc_html_e( 'Nenhum artigo encontrado.', 'rickytech' ); ?></div>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
