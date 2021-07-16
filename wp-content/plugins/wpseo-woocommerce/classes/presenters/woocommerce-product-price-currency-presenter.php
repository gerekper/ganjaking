<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Represents the product's price currency.
 */
class WPSEO_WooCommerce_Product_Price_Currency_Presenter extends WPSEO_WooCommerce_Abstract_Product_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'product:price:currency';

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return string The raw value.
	 */
	public function get() {
		$product_type = WPSEO_WooCommerce_Utils::get_product_type( $this->product );

		// Omit the currency for variable and grouped products.
		if ( $product_type === 'variable' || $product_type === 'grouped' ) {
			return '';
		}

		return (string) get_woocommerce_currency();
	}
}
