<?php
/**
 * Print Content of all Pages  - For Gutenberg Full Site Editing
 *
 * @since 6.5.0
 */
$template = PORTO_DIR . '/index.php';
if ( is_front_page() || is_page() ) {
	$template = PORTO_DIR . '/page.php';
} elseif ( porto_is_shop() ) {
	$template = PORTO_DIR . '/woocommerce/' . 'archive-product.php';
} elseif ( porto_is_product() ) {
	$template = WC()->plugin_path() . '/templates/' . 'single-product.php';
} elseif ( is_singular() ) {
	$post_type = get_post_type();
	if ( ! $post_type ) {
		$post_type = get_query_var( 'post_type' );
	}
	if ( 'event' == $post_type || 'portfolio' == $post_type || 'faq' == $post_type || 'member' == $post_type ) {
		$template = PORTO_DIR . '/single-' . $post_type . '.php';
	} elseif ( $post_type ) {
		$template = PORTO_DIR . '/single.php';
	}
} elseif ( is_tax() ) {
	$post_type = get_post_type();
	if ( ! $post_type ) {
		$post_type = get_query_var( 'post_type' );
	}
	if ( 'product' == $post_type ) {
		$template = PORTO_DIR . '/woocommerce/' . 'archive-product.php';
	} elseif ( $post_type ) {
		$template = PORTO_DIR . '/archive-' . $post_type . '.php';
	}
} elseif ( porto_is_archive() ) {
	$post_type = get_post_type();
	if ( ! $post_type ) {
		$post_type = get_query_var( 'post_type' );
	}
	if ( 'event' == $post_type || 'portfolio' == $post_type || 'faq' == $post_type ) {
		$template = PORTO_DIR . '/archive-' . $post_type . '.php';
	} elseif ( $post_type ) {
		$template = PORTO_DIR . '/index.php';
	}
} elseif ( is_404() ) {
	$template = PORTO_DIR . '/404.php';
} elseif ( is_search() ) {
	$template = PORTO_DIR . '/search.php';
}

if ( $template ) {
	global $porto_block_template;
	$porto_block_template = true;
	include_once $template;
	unset( $GLOBALS['porto_block_template'] );
}
