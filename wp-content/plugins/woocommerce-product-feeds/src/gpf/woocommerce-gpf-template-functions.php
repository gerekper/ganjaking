<?php

/**
 * Show a specific element with the value that would be rendered in the feed.
 *
 * Pass a product post object to fetch the value for a specific product, or leave blank
 * to fetch the value for the global $post.
 *
 * @param string $element
 * @param WP_Post|null $post
 */
function woocommerce_gpf_show_element( $element, $post = null ) {
	global $woocommerce_gpf_di;
	$common   = $woocommerce_gpf_di['WoocommerceGpfCommon'];
	$template = $woocommerce_gpf_di['WoocommerceGpfTemplateLoader'];
	$common->initialise();
	WoocommerceGpfTemplateTags::show_element( $element, $common, $template, $post );
}

/**
 * Show a specific element with label, with the value that would be rendered in the feed.
 *
 * Pass a product post object to fetch the value for a specific product, or leave blank
 * to fetch the value for the global $post.
 *
 * @param string $element
 * @param WP_Post|null $post
 */
function woocommerce_gpf_show_element_with_label( $element, $post = null ) {
	global $woocommerce_gpf_di;
	$common   = $woocommerce_gpf_di['WoocommerceGpfCommon'];
	$template = $woocommerce_gpf_di['WoocommerceGpfTemplateLoader'];
	$common->initialise();
	WoocommerceGpfTemplateTags::show_element_with_label( $element, $common, $template, $post );
}

/**
 * Retrieve a specific element value that would be rendered in the feed.
 *
 * Pass a product post object to fetch the value for a specific product, or leave blank
 * to fetch the value for the global $post.
 *
 * @param string $element
 * @param WP_Post|null $post
 *
 * @return array
 */
function woocommerce_gpf_get_element_values( $element, $post = null ) {
	global $woocommerce_gpf_di;
	$common = $woocommerce_gpf_di['WoocommerceGpfCommon'];
	$common->initialise();

	return WoocommerceGpfTemplateTags::get_element_values( $element, $common, $post );
}

/**
 * Retrieve a specific element value that would be rendered in the feed.
 *
 * Pass a product post object to fetch the value for a specific product, or leave blank
 * to fetch the value for the global $post.
 *
 * @param string $element
 * @param WP_Post|null $post
 *
 * @return WoocommerceGpfFeedItem
 */
function woocommerce_gpf_get_feed_item( WP_Post $post ) {
	global $woocommerce_gpf_di;
	// Bail if it's not a WooCommerce product / variation.
	if ( empty( $post->ID ) || empty( $post->post_type ) ||
		 ( 'product' !== $post->post_type && 'product_variation' !== $post->post_type ) ) {
		return null;
	}
	$common = $woocommerce_gpf_di['WoocommerceGpfCommon'];
	$common->initialise();
	if ( 'product_variation' === $post->post_type ) {
		$specific_product = wc_get_product( $post->ID );
		$general_product  = wc_get_product( $post->post_parent );
	} else {
		$specific_product = wc_get_product( $post->ID );
		$general_product  = $specific_product;
	}

	return new WoocommerceGpfFeedItem( $specific_product, $general_product, 'all', $common, $woocommerce_gpf_di['WoocommerceGpfDebugService'] );
}

/**
 * Determine if this is a feed URL.
 *
 * May need to be used before parse_query, so we have to manually check all
 * sorts of combinations.
 *
 * @return boolean  True if a feed is being generated.
 */
function woocommerce_gpf_is_generating_feed() {
	return ( isset( $_REQUEST['action'] ) && 'woocommerce_gpf' === $_REQUEST['action'] ) ||
		   ( isset( $_SERVER['REQUEST_URI'] ) && stripos( $_SERVER['REQUEST_URI'], '/woocommerce_gpf' ) === 0 ) ||
		   isset( $_REQUEST['woocommerce_gpf'] );
}
