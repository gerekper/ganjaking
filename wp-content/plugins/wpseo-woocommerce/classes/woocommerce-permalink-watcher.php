<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * The permalink watcher.
 *
 * @deprecated 13.8
 */
class WPSEO_Woocommerce_Permalink_Watcher {

	/**
	 * Registers the hooks.
	 *
	 * @deprecated 13.8
	 * @codeCoverageIgnore
	 */
	public function register_hooks() {
		_deprecated_function( __METHOD__, 'WPSEO Woo 13.8' );
	}

	/**
	 * Filters the product post type from the post type.
	 *
	 * @param array $post_types The post types to filter.
	 *
	 * @return array The filtered post types.
	 */
	public function filter_product_from_post_types( $post_types ) {
		_deprecated_function( __METHOD__, 'WPSEO Woo 13.8' );

		return $post_types;
	}

	/**
	 * Resets the indexables for WooCommerce based on the changed permalink fields.
	 */
	public function reset_woocommerce_permalinks() {
		_deprecated_function( __METHOD__, 'WPSEO Woo 13.8' );
	}
}
