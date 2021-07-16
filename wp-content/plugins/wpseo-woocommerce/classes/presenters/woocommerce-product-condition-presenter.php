<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Represents the product's condition.
 */
class WPSEO_WooCommerce_Product_Condition_Presenter extends WPSEO_WooCommerce_Abstract_Product_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'product:condition';

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return string The raw value.
	 */
	public function get() {
		/**
		 * Filter: Yoast\WP\Woocommerce\product_condition - Allow developers to prevent or change the output of the product condition in the OpenGraph tags.
		 *
		 * @param \WC_Product $product The product we're outputting.
		 *
		 * @api string Defaults to 'new'.
		 */
		return (string) apply_filters( 'Yoast\WP\Woocommerce\product_condition', 'new', $this->product );
	}
}
