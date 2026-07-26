<?php
/**
 * Search results — themed header + card grid.
 *
 * @package Rickytech
 */

get_header();

global $wp_query;
$found = (int) $wp_query->found_posts;
$q     = get_search_query();
?>
<div class="view-enter container-wide">
	<div class="sec" style="padding-bottom:24px">
		<div class="eyebrow-lbl" style="margin-bottom:14px"><?php esc_html_e( 'Busca', 'rickytech' ); ?></div>
		<div style="position:relative;max-width:640px">
			<span style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--fg-tertiary)"><?php rickytech_icon( 'search', 20 ); ?></span>
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input class="input" style="height:54px;padding-left:48px;font-size:17px" type="search" name="s" value="<?php echo esc_attr( $q ); ?>" placeholder="<?php esc_attr_e( 'Buscar por título, tema ou autor…', 'rickytech' ); ?>" autofocus>
			</form>
		</div>
		<div style="margin-top:14px;font-family:var(--font-mono);font-size:13px;color:var(--fg-tertiary)">
			<?php printf( esc_html( _n( '%1$d resultado para "%2$s"', '%1$d resultados para "%2$s"', $found, 'rickytech' ) ), $found, esc_html( $q ) ); ?>
		</div>
	</div>

	<div class="sec" style="padding-top:0">
		<?php if ( have_posts() ) : ?>
			<div class="card-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/card' ); ?>
				<?php endwhile; ?>
			</div>
			<?php rickytech_pagination(); ?>
		<?php else : ?>
			<div class="loop-empty"><?php esc_html_e( 'Nenhum artigo encontrado. Tente outro termo.', 'rickytech' ); ?></div>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
