<?php
/**
 * Footer.
 *
 * @package Rickytech
 */
?>
</main><!-- #content -->

<footer class="foot">
	<div class="container-wide">
		<div class="foot-top">
			<div>
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img class="mark" src="<?php echo esc_url( get_template_directory_uri() . '/assets/logos/ricky-mark-transparent.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
					<span class="wm">Central de <span class="accent">Artigos</span></span>
				</a>
				<p class="ftag"><?php echo esc_html( get_bloginfo( 'description' ) ?: 'Engenharia de IA e software que vai pra produção. Escrito por quem constrói.' ); ?></p>
			</div>
			<div class="foot-cols">
				<div class="foot-col">
					<h5><?php esc_html_e( 'Temas', 'rickytech' ); ?></h5>
					<?php
					$cats = get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 6, 'hide_empty' => true ) );
					foreach ( $cats as $cat ) {
						printf( '<a href="%s">%s</a>', esc_url( get_category_link( $cat ) ), esc_html( $cat->name ) );
					}
					?>
				</div>
				<div class="foot-col">
					<h5><?php esc_html_e( 'Central', 'rickytech' ); ?></h5>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Todos os artigos', 'rickytech' ); ?></a>
					<a href="<?php echo esc_url( rickytech_subscribe_url() ); ?>"><?php esc_html_e( 'Newsletter', 'rickytech' ); ?></a>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'Arquivo', 'rickytech' ); ?></a>
				</div>
				<div class="foot-col">
					<h5><?php bloginfo( 'name' ); ?></h5>
					<?php
					if ( has_nav_menu( 'footer' ) ) {
						wp_nav_menu( array(
							'theme_location' => 'footer',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'depth'          => 1,
							'fallback_cb'    => false,
						) );
					} else {
						wp_list_pages( array( 'title_li' => '', 'depth' => 1, 'number' => 5 ) );
					}
					?>
				</div>
			</div>
		</div>
		<div class="foot-bot">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · Central de Artigos</span>
			<span style="display:inline-flex;gap:14px">
				<a href="<?php echo esc_url( get_feed_link() ); ?>" aria-label="RSS"><?php rickytech_icon( 'rss', 16 ); ?></a>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
