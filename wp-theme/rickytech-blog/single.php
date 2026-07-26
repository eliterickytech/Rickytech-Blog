<?php
/**
 * Single article — reading view with TOC, progress bar, prose, author box, related.
 *
 * @package Rickytech
 */

get_header();

while ( have_posts() ) :
	the_post();

	$term   = rickytech_primary_category();
	$colors = rickytech_cat_colors( $term );
	$author = get_the_author_meta( 'ID' );
	$slug   = get_post_field( 'post_name', get_the_ID() );
	?>
	<div class="view-enter" style="--cat-ink:<?php echo esc_attr( $colors['ink'] ); ?>">
		<div class="read-progress" style="width:0%"></div>

		<div class="container-read">
			<div class="art-hero">
				<a class="art-back" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php rickytech_icon( 'arrowLeft', 15 ); ?> <?php esc_html_e( 'Voltar', 'rickytech' ); ?></a>
				<?php if ( $term ) : ?>
					<div><a class="art-cat" href="<?php echo esc_url( get_category_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></div>
				<?php endif; ?>
				<h1 class="art-title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="art-lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<div class="art-byline">
					<a href="<?php echo esc_url( get_author_posts_url( $author ) ); ?>"><?php rickytech_avatar( $author, 46 ); ?></a>
					<a href="<?php echo esc_url( get_author_posts_url( $author ) ); ?>" style="text-decoration:none">
						<div class="who"><?php the_author(); ?></div>
						<div class="sub"><?php echo esc_html( get_the_author_meta( 'description' ) ? wp_trim_words( get_the_author_meta( 'description' ), 4, '' ) : __( 'Autor', 'rickytech' ) ); ?></div>
					</a>
					<div class="bsep"></div>
					<div>
						<div class="sub" style="margin-bottom:2px"><?php echo esc_html( get_the_date( 'd M Y' ) ); ?></div>
						<div class="sub" style="display:inline-flex;align-items:center;gap:5px"><?php rickytech_icon( 'clock', 13 ); ?> <?php echo esc_html( rickytech_reading_time() ); ?> min de leitura</div>
					</div>
					<div class="art-actions">
						<button class="icon-btn" type="button" data-save="<?php echo esc_attr( $slug ); ?>" title="<?php esc_attr_e( 'Salvar', 'rickytech' ); ?>" aria-pressed="false"><?php rickytech_icon( 'bookmark', 17 ); ?></button>
						<button class="icon-btn" type="button" data-copy-link title="<?php esc_attr_e( 'Compartilhar', 'rickytech' ); ?>"><?php rickytech_icon( 'share', 17 ); ?></button>
					</div>
				</div>
			</div>
		</div>

		<div class="container-read">
			<div class="art-cover"><?php rickytech_thumb( get_the_ID(), false ); ?></div>
		</div>

		<div class="art-layout">
			<aside class="toc">
				<div class="toc-lbl"><?php esc_html_e( 'Neste artigo', 'rickytech' ); ?></div>
				<ul><!-- populated by theme.js from H2 headings --></ul>
			</aside>

			<main>
				<div class="prose">
					<?php the_content(); ?>
				</div>
				<?php
				wp_link_pages( array(
					'before' => '<div class="pagination" style="margin-top:32px">',
					'after'  => '</div>',
				) );
				?>
			</main>

			<aside class="art-rail">
				<div class="rail-btn" data-save="<?php echo esc_attr( $slug ); ?>"><?php rickytech_icon( 'bookmark', 17 ); ?> <span><?php esc_html_e( 'Salvar', 'rickytech' ); ?></span></div>
				<div class="rail-btn" data-copy-link><?php rickytech_icon( 'link', 17 ); ?> <span data-copy-label><?php esc_html_e( 'Link', 'rickytech' ); ?></span></div>
				<a class="rail-btn" href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_permalink() ); ?>&text=<?php echo rawurlencode( get_the_title() ); ?>" target="_blank" rel="noopener"><?php rickytech_icon( 'twitter', 17 ); ?> <span><?php esc_html_e( 'Postar', 'rickytech' ); ?></span></a>
			</aside>
		</div>

		<div class="art-foot">
			<?php $tags = get_the_tags(); if ( $tags ) : ?>
				<div class="tag-row">
					<?php foreach ( $tags as $tag ) : ?>
						<a class="chip" href="<?php echo esc_url( get_tag_link( $tag ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="author-box">
				<a href="<?php echo esc_url( get_author_posts_url( $author ) ); ?>"><?php rickytech_avatar( $author, 64 ); ?></a>
				<div>
					<h3 class="ab-name"><a href="<?php echo esc_url( get_author_posts_url( $author ) ); ?>" style="color:inherit"><?php the_author(); ?></a></h3>
					<?php $url = get_the_author_meta( 'user_url' ); ?>
					<?php if ( $url ) : ?><div class="ab-role"><?php echo esc_html( $url ); ?></div><?php endif; ?>
					<?php if ( get_the_author_meta( 'description' ) ) : ?>
						<p class="ab-bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php
		// Related: same category, excluding current.
		$related = new WP_Query( array(
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'category__in'        => $term ? array( $term->term_id ) : array(),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		) );
		if ( $related->have_posts() ) :
			?>
			<div class="related">
				<div class="container-wide">
					<div class="sec-head">
						<h2><?php esc_html_e( 'Continue lendo', 'rickytech' ); ?></h2>
						<?php if ( $term ) : ?>
							<a class="see-all" href="<?php echo esc_url( get_category_link( $term ) ); ?>"><?php printf( esc_html__( 'mais de %s →', 'rickytech' ), esc_html( $term->name ) ); ?></a>
						<?php endif; ?>
					</div>
					<div class="card-grid">
						<?php while ( $related->have_posts() ) : $related->the_post(); ?>
							<?php get_template_part( 'template-parts/card' ); ?>
						<?php endwhile; ?>
					</div>
				</div>
			</div>
			<?php
		endif;
		wp_reset_postdata();
		?>

		<?php
		if ( comments_open() || get_comments_number() ) {
			echo '<div class="comments-area">';
			comments_template();
			echo '</div>';
		}
		?>
	</div>
	<?php
endwhile;

get_footer();
