<?php
/**
 * 404.
 *
 * @package Rickytech
 */

get_header();
?>
<div class="view-enter container">
	<div class="nf">
		<div class="code">404</div>
		<h1><?php esc_html_e( 'Página não encontrada', 'rickytech' ); ?></h1>
		<p><?php esc_html_e( 'O artigo que você procura mudou de lugar ou nunca existiu.', 'rickytech' ); ?></p>
		<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Voltar para a Central', 'rickytech' ); ?> <?php rickytech_icon( 'arrowRight', 15 ); ?></a>
	</div>
</div>
<?php
get_footer();
