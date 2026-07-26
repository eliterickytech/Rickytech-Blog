<?php
/**
 * Home/blog sidebar: most-read, categories, newsletter CTA.
 *
 * @package Rickytech
 */
?>
<aside class="sidebar">

	<div class="side-block">
		<div class="side-title"><?php esc_html_e( 'Mais lidos', 'rickytech' ); ?></div>
		<?php
		$popular = new WP_Query( array(
			'posts_per_page'      => 5,
			'orderby'             => 'comment_count',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		) );
		$rank = 0;
		while ( $popular->have_posts() ) :
			$popular->the_post();
			$rank++;
			$t = rickytech_primary_category();
			?>
			<div class="pop-item" data-href="<?php the_permalink(); ?>">
				<span class="rank"><?php echo esc_html( str_pad( $rank, 2, '0', STR_PAD_LEFT ) ); ?></span>
				<div class="pop-body">
					<h4><a href="<?php the_permalink(); ?>" style="color:inherit"><?php the_title(); ?></a></h4>
					<div class="pm"><?php echo $t ? esc_html( $t->name ) . ' · ' : ''; ?><?php echo esc_html( rickytech_reading_time() ); ?> min</div>
				</div>
			</div>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
	</div>

	<div class="side-block">
		<div class="side-title"><?php esc_html_e( 'Explorar temas', 'rickytech' ); ?></div>
		<div class="cat-list">
			<?php
			foreach ( get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true ) ) as $cat ) :
				$cc = rickytech_cat_colors( $cat );
				?>
				<a class="cat-row" href="<?php echo esc_url( get_category_link( $cat ) ); ?>">
					<span class="swatch" style="background:<?php echo esc_attr( $cc['ink'] ); ?>"></span>
					<span class="cn"><?php echo esc_html( $cat->name ); ?></span>
					<span class="cc"><?php echo esc_html( $cat->count ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="side-cta">
		<div class="glow"></div>
		<h4><?php esc_html_e( 'Receba os melhores artigos', 'rickytech' ); ?></h4>
		<p><?php esc_html_e( 'Um e-mail por semana com o que importa em IA e engenharia. Sem ruído.', 'rickytech' ); ?></p>
		<a class="btn btn-primary" href="<?php echo esc_url( rickytech_subscribe_url() ); ?>" style="justify-content:center;width:100%"><?php esc_html_e( 'Assinar grátis', 'rickytech' ); ?></a>
	</div>

</aside>
