<?php
/**
 * Blog posts index page (the "Blog" page set as Posts page).
 * Renders the same Central de Artigos landing design.
 *
 * @package Rickytech
 */

get_header();
?>
<div class="view-enter">
	<?php get_template_part( 'template-parts/blog-landing' ); ?>
</div>
<?php
get_footer();
