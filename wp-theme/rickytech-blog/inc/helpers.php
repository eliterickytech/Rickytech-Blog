<?php
/**
 * Helpers — colors, thumbnails, avatars, icons, reading time, featured query.
 *
 * @package Rickytech
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * On-brand color palette for categories. Each category gets a stable color
 * derived from its slug, so it works whether you keep "Categoria 1-4" or
 * rename them to IA / Engenharia / Cloud, etc.
 */
function rickytech_palette() {
	return array(
		array( 'ink' => '#8b6dff', 'deep' => '#1a1340', 'glyph' => '∑'   ),
		array( 'ink' => '#22d3ee', 'deep' => '#0a2b33', 'glyph' => '{}'  ),
		array( 'ink' => '#34d399', 'deep' => '#06281d', 'glyph' => '☁'   ),
		array( 'ink' => '#fbbf24', 'deep' => '#2e2206', 'glyph' => 'λ'   ),
		array( 'ink' => '#fb7185', 'deep' => '#33101a', 'glyph' => '</>' ),
		array( 'ink' => '#60a5fa', 'deep' => '#0d1f3d', 'glyph' => '↗'   ),
	);
}

/** FNV-1a hash → unsigned int (matches the JS thumbnail seed). */
function rickytech_hash( $s ) {
	$h = 2166136261;
	$len = strlen( $s );
	for ( $i = 0; $i < $len; $i++ ) {
		$h ^= ord( $s[ $i ] );
		$h  = ( $h * 16777619 ) & 0xFFFFFFFF;
	}
	return $h & 0xFFFFFFFF;
}

/** Primary category term for a post (first assigned). */
function rickytech_primary_category( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$cats = get_the_category( $post_id );
	return ! empty( $cats ) ? $cats[0] : null;
}

/** Colors (ink/deep/glyph) for a category term. */
function rickytech_cat_colors( $term ) {
	$palette = rickytech_palette();
	if ( ! $term ) {
		return $palette[0];
	}
	$idx = rickytech_hash( $term->slug ) % count( $palette );
	return $palette[ $idx ];
}

/** Estimated reading time in minutes (200 wpm). */
function rickytech_reading_time( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$content = get_post_field( 'post_content', $post_id );
	$words   = max( 1, str_word_count( wp_strip_all_tags( $content ) ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/** Human-readable relative date, e.g. "há 3 dias". */
function rickytech_rel_date( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$ts   = get_post_time( 'U', true, $post_id );
	$diff = time() - $ts;
	if ( $diff < 0 )        { return __( 'hoje', 'rickytech' ); }
	$days = floor( $diff / DAY_IN_SECONDS );
	if ( $days <= 0 )       { return __( 'hoje', 'rickytech' ); }
	if ( 1 === (int) $days ) { return __( 'ontem', 'rickytech' ); }
	if ( $days < 7 )        { return sprintf( __( 'há %d dias', 'rickytech' ), $days ); }
	if ( $days < 30 )       { return sprintf( __( 'há %d sem', 'rickytech' ), floor( $days / 7 ) ); }
	return get_the_date( 'd M Y', $post_id );
}

/** Avatar color for an author, derived from their name. */
function rickytech_author_color( $user_id ) {
	$colors = array( '#7c5cff', '#22d3ee', '#34d399', '#fb7185', '#fbbf24', '#60a5fa' );
	$name   = get_the_author_meta( 'display_name', $user_id );
	return $colors[ rickytech_hash( (string) $name ) % count( $colors ) ];
}

/** Initials (max 2 letters) from a display name. */
function rickytech_initials( $name ) {
	$parts = preg_split( '/\s+/', trim( (string) $name ) );
	$ini   = '';
	foreach ( $parts as $p ) {
		if ( $p !== '' ) { $ini .= mb_strtoupper( mb_substr( $p, 0, 1 ) ); }
		if ( mb_strlen( $ini ) >= 2 ) { break; }
	}
	return $ini !== '' ? $ini : 'RT';
}

/** Echo a procedural/initials avatar for an author. */
function rickytech_avatar( $user_id, $size = 32 ) {
	$initials = rickytech_initials( get_the_author_meta( 'display_name', $user_id ) );
	$c1 = rickytech_author_color( $user_id );
	$style = sprintf(
		'width:%1$dpx;height:%1$dpx;border-radius:50%%;background:linear-gradient(135deg,%2$s,color-mix(in oklch,%2$s 55%%,#050507));display:inline-flex;align-items:center;justify-content:center;color:#fff;font-family:var(--font-mono);font-weight:500;font-size:%3$dpx;flex:0 0 %1$dpx;box-shadow:inset 0 1px 0 rgba(255,255,255,.18)',
		(int) $size, esc_attr( $c1 ), (int) round( $size * 0.38 )
	);
	printf( '<span class="av" style="%s">%s</span>', $style, esc_html( $initials ) );
}

/** Echo the .thumb block: featured image if set, else a procedural canvas. */
function rickytech_thumb( $post_id = null, $show_cat = true, $extra_class = '' ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$term    = rickytech_primary_category( $post_id );
	$colors  = rickytech_cat_colors( $term );
	$slug    = get_post_field( 'post_name', $post_id );

	printf(
		'<div class="thumb %s" style="--cat-deep:%s;--cat-ink:%s" data-seed="%s" data-ink="%s" data-deep="%s">',
		esc_attr( $extra_class ),
		esc_attr( $colors['deep'] ),
		esc_attr( $colors['ink'] ),
		esc_attr( $slug ),
		esc_attr( $colors['ink'] ),
		esc_attr( $colors['deep'] )
	);

	if ( has_post_thumbnail( $post_id ) ) {
		echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'thumb-img', 'loading' => 'lazy', 'alt' => '' ) );
	} else {
		echo '<canvas></canvas>';
	}

	if ( $show_cat && $term ) {
		printf( '<span class="thumb-cat">%s</span>', esc_html( $term->name ) );
	}
	printf( '<span class="thumb-glyph">%s</span>', esc_html( $colors['glyph'] ) );
	echo '</div>';
}

/** Get the featured post (latest with the "Destaque" tag; fallbacks: sticky, latest). */
function rickytech_featured_post() {
	$q = new WP_Query( array(
		'tag'                 => 'destaque',
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );
	if ( $q->have_posts() ) {
		$q->the_post();
		$p = get_post();
		wp_reset_postdata();
		return $p;
	}
	$sticky = get_option( 'sticky_posts' );
	if ( ! empty( $sticky ) ) {
		$p = get_post( $sticky[0] );
		if ( $p ) { return $p; }
	}
	$recent = get_posts( array( 'posts_per_page' => 1 ) );
	return ! empty( $recent ) ? $recent[0] : null;
}

/** Icon SVG paths (ported from the design's icon set). */
function rickytech_icon( $name, $size = 18, $fill = false ) {
	$paths = array(
		'search'     => 'M11 19a8 8 0 100-16 8 8 0 000 16zM21 21l-4.3-4.3',
		'sun'        => 'M12 3v2M12 19v2M5 5l1.5 1.5M17.5 17.5L19 19M3 12h2M19 12h2M5 19l1.5-1.5M17.5 6.5L19 5M12 8a4 4 0 100 8 4 4 0 000-8z',
		'moon'       => 'M21 12.8A9 9 0 1111.2 3a7 7 0 109.8 9.8z',
		'arrowRight' => 'M5 12h14M13 6l6 6-6 6',
		'arrowLeft'  => 'M19 12H5M11 18l-6-6 6-6',
		'bookmark'   => 'M6 4h12v16l-6-4-6 4V4z',
		'share'      => 'M16 6l-4-4-4 4M12 2v13M4 12v7a2 2 0 002 2h12a2 2 0 002-2v-7',
		'heart'      => 'M12 20s-7-4.6-9.5-9A4.8 4.8 0 0112 5a4.8 4.8 0 019.5 6c-2.5 4.4-9.5 9-9.5 9z',
		'clock'      => 'M12 7v5l3 2M12 21a9 9 0 100-18 9 9 0 000 18z',
		'check'      => 'M5 13l4 4L19 7',
		'twitter'    => 'M22 5.8c-.7.3-1.5.5-2.3.6a4 4 0 001.7-2.2c-.8.5-1.6.8-2.5 1a4 4 0 00-6.8 3.6A11.3 11.3 0 013 4.8a4 4 0 001.2 5.3c-.6 0-1.2-.2-1.7-.5a4 4 0 003.2 3.9c-.6.2-1.2.2-1.7.1a4 4 0 003.7 2.8A8 8 0 012 22a11.3 11.3 0 006.2 1.8c7.4 0 11.5-6.2 11.5-11.5v-.5c.8-.6 1.5-1.3 2.1-2z',
		'link'       => 'M10 13a5 5 0 007.5.5l3-3a5 5 0 00-7-7l-1.5 1.5M14 11a5 5 0 00-7.5-.5l-3 3a5 5 0 007 7L12 19',
		'rss'        => 'M5 19a1 1 0 100-2 1 1 0 000 2zM4 11a9 9 0 019 9M4 5a15 15 0 0115 15',
		'menu'       => 'M3 12h18M3 6h18M3 18h18',
		'x'          => 'M6 6l12 12M18 6L6 18',
		'bolt'       => 'M13 2L4 14h7l-1 8 9-12h-7l1-8z',
	);
	if ( empty( $paths[ $name ] ) ) { return; }
	printf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="%2$s" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="%3$s"></path></svg>',
		(int) $size,
		$fill ? 'currentColor' : 'none',
		esc_attr( $paths[ $name ] )
	);
}

/** URL of the "Assinar"/newsletter CTA: a Contato or Newsletter page if it exists. */
function rickytech_subscribe_url() {
	foreach ( array( 'newsletter', 'contato', 'contact' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) { return get_permalink( $page ); }
	}
	return home_url( '/' );
}
