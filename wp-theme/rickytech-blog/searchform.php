<?php
/**
 * Search form.
 *
 * @package Rickytech
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="rt-s"><?php esc_html_e( 'Buscar:', 'rickytech' ); ?></label>
	<input type="search" id="rt-s" class="input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Buscar artigos…', 'rickytech' ); ?>">
</form>
