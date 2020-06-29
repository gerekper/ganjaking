<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Represents the deprecated OpenGraph action.
 */
class WPSEO_WooCommerce_Product_OpenGraph_Deprecation_Presenter extends WPSEO_WooCommerce_Abstract_Product_Presenter {

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return string The raw value.
	 */
	public function get() {
		/**
		 * Action: 'Yoast\WP\Woocommerce\opengraph' - Allow developers to add to our OpenGraph tags.
		 *
		 * @since 12.6.0
		 * @deprecated 13.0
		 *
		 * @api array $product The WooCommerce product we're outputting for.
		 */
		do_action_deprecated( 'Yoast\WP\Woocommerce\opengraph', (array) $this->product, 'WPSEO Woo 13.0', 'wpseo_frontend_presenters' );

		return '';
	}
}
