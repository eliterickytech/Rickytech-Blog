<?php
/**
 * Generic page (Sobre, Contato, Equipe, FAQ, Recursos, Privacy Policy, etc.).
 *
 * @package Rickytech
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="view-enter container-read">
		<div class="page-hero">
			<div class="eyebrow-lbl"><?php bloginfo( 'name' ); ?></div>
			<h1><?php the_title(); ?></h1>
		</div>

		<?php if ( has_post_thumbnail() ) : ?>
			<div style="margin:8px 0 8px"><?php the_post_thumbnail( 'large', array( 'style' => 'border-radius:var(--radius-xl);box-shadow:var(--elev-2)' ) ); ?></div>
		<?php endif; ?>

		<div class="page-body">
			<div class="prose">
				<?php the_content(); ?>
			</div>
			<?php
			wp_link_pages( array(
				'before' => '<div class="pagination" style="margin-top:32px">',
				'after'  => '</div>',
			) );
			?>
		</div>

		<?php
		if ( comments_open() || get_comments_number() ) {
			echo '<div class="comments-area" style="padding-left:0;padding-right:0">';
			comments_template();
			echo '</div>';
		}
		?>
	</div>
	<?php
endwhile;

get_footer();
