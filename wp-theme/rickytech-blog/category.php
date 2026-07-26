<?php
/**
 * Category archive — themed hero + sort chips + card grid.
 *
 * @package Rickytech
 */

get_header();

$term   = get_queried_object();
$colors = rickytech_cat_colors( $term );
$sort   = ( isset( $_GET['orderby'] ) && 'popular' === $_GET['orderby'] ) ? 'popular' : 'recent';
?>
<div class="view-enter">
	<div class="cat-hero" style="background:linear-gradient(180deg, <?php echo esc_attr( $colors['deep'] ); ?>55, transparent)">
		<div class="container-wide">
			<span class="cat-badge" style="background:color-mix(in oklch, <?php echo esc_attr( $colors['ink'] ); ?> 16%, transparent);color:<?php echo esc_attr( $colors['ink'] ); ?>;border:1px solid color-mix(in oklch, <?php echo esc_attr( $colors['ink'] ); ?> 30%, transparent)">
				<?php echo esc_html( $colors['glyph'] ); ?> <?php echo esc_html( single_cat_title( '', false ) ); ?>
			</span>
			<h1><?php single_cat_title(); ?></h1>
			<?php if ( category_description() ) : ?>
				<p><?php echo wp_kses_post( category_description() ); ?></p>
			<?php endif; ?>
			<div style="margin-top:18px;font-family:var(--font-mono);font-size:13px;color:var(--fg-tertiary)"><?php echo esc_html( $term->count ); ?> <?php esc_html_e( 'artigos', 'rickytech' ); ?></div>
		</div>
	</div>

	<div class="container-wide">
		<div class="sec">
			<div class="sec-head">
				<div class="chips">
					<a class="chip<?php echo 'recent' === $sort ? ' on' : ''; ?>" href="<?php echo esc_url( get_category_link( $term ) ); ?>"><?php esc_html_e( 'Recentes', 'rickytech' ); ?></a>
					<a class="chip<?php echo 'popular' === $sort ? ' on' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'orderby', 'popular', get_category_link( $term ) ) ); ?>"><?php esc_html_e( 'Mais lidos', 'rickytech' ); ?></a>
				</div>
			</div>

			<?php if ( have_posts() ) : ?>
				<div class="card-grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'template-parts/card' ); ?>
					<?php endwhile; ?>
				</div>
				<?php rickytech_pagination(); ?>
			<?php else : ?>
				<div class="loop-empty"><?php esc_html_e( 'Nenhum artigo nesta categoria ainda.', 'rickytech' ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
get_footer();
