<?php
/**
 * Comments.
 *
 * @package Rickytech
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( post_password_required() ) { return; }
?>

<?php if ( have_comments() ) : ?>
	<h3>
		<?php
		$cn = get_comments_number();
		printf( esc_html( _n( '%s comentário', '%s comentários', $cn, 'rickytech' ) ), number_format_i18n( $cn ) );
		?>
	</h3>
	<ol class="comment-list">
		<?php
		wp_list_comments( array(
			'callback'    => 'rickytech_comment',
			'style'       => 'ol',
			'avatar_size' => 0,
		) );
		?>
	</ol>
	<?php the_comments_pagination(); ?>
<?php endif; ?>

<?php
comment_form( array(
	'class_submit' => 'btn btn-primary',
	'title_reply'  => __( 'Deixe um comentário', 'rickytech' ),
) );
