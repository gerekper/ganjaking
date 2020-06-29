<?php

function woocommerce_gpf_show_element( $element, $post = null ) {
	global $woocommerce_gpf_common;
	$template = new WoocommerceGpfTemplateLoader();
	if ( ! empty( $woocommerce_gpf_common ) ) {
		$common = $woocommerce_gpf_common;
	} else {
		$common = new WoocommerceGpfCommon();
		$common->initialise();
	}
	return WoocommerceGpfTemplateTags::show_element( $element, $common, $template, $post );
}

function woocommerce_gpf_show_element_with_label( $element, $post = null ) {
	global $woocommerce_gpf_common;
	$template = new WoocommerceGpfTemplateLoader();
	if ( ! empty( $woocommerce_gpf_common ) ) {
		$common = $woocommerce_gpf_common;
	} else {
		$common = new WoocommerceGpfCommon();
		$common->initialise();
	}
	return WoocommerceGpfTemplateTags::show_element_with_label( $element, $common, $template, $post );
}

function woocommerce_gpf_get_element_values( $element, $post = null ) {
	global $woocommerce_gpf_common;
	if ( ! empty( $woocommerce_gpf_common ) ) {
		$common = $woocommerce_gpf_common;
	} else {
		$common = new WoocommerceGpfCommon();
		$common->initialise();
	}
	return WoocommerceGpfTemplateTags::get_element_values( $element, $common, $post );
}

function woocommerce_gpf_get_feed_item( WP_Post $post ) {
	// Bail if it's not a WooCommerce product / variation.
	if ( empty( $post->ID ) || empty( $post->post_type ) ||
		 ( 'product' !== $post->post_type && 'product_variation' !== $post->post_type ) ) {
		return null;
	}
	// Use the global WooCommerceGpfCommon class if ready. If not, create one.
	global $woocommerce_gpf_common;
	if ( ! empty( $woocommerce_gpf_common ) ) {
		$common = $woocommerce_gpf_common;
	} else {
		$common = new WoocommerceGpfCommon();
		$common->initialise();
	}
	if ( 'product_variation' === $post->post_type ) {
		$specific_product = wc_get_product( $post->ID );
		$general_product  = wc_get_product( $post->post_parent );
	} else {
		$specific_product = wc_get_product( $post->ID );
		$general_product  = $specific_product;
	}
	return new WoocommerceGpfFeedItem( $specific_product, $general_product, 'all', $common );
}
