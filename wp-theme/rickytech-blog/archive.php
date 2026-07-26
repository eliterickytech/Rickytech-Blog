<?php
/**
 * Generic archive (tags, dates, etc.) — themed header + card grid.
 *
 * @package Rickytech
 */

get_header();
?>
<div class="view-enter container-wide">
	<div class="page-hero">
		<div class="eyebrow-lbl"><?php esc_html_e( 'Arquivo', 'rickytech' ); ?></div>
		<h1><?php the_archive_title(); ?></h1>
		<?php if ( get_the_archive_description() ) : ?>
			<p style="margin-top:14px;color:var(--fg-secondary);max-width:560px"><?php echo wp_kses_post( get_the_archive_description() ); ?></p>
		<?php endif; ?>
	</div>

	<div class="sec" style="padding-top:24px">
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
