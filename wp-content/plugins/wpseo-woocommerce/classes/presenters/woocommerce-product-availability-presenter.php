<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Represents the product's availability.
 */
class WPSEO_WooCommerce_Product_Availability_Presenter extends WPSEO_WooCommerce_Abstract_Product_Availability_Presenter {

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = '<meta property="product:availability" content="%s" />';

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return string The raw value.
	 */
	public function get() {
		if ( $this->is_on_backorder ) {
			return 'available for order';
		}

		if ( $this->is_in_stock ) {
			return 'instock';
		}

		return 'out of stock';
	}
}
