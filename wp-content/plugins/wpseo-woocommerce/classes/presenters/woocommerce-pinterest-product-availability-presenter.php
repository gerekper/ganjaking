<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Represents the Pinterest product availability.
 */
class WPSEO_WooCommerce_Pinterest_Product_Availability_Presenter extends WPSEO_WooCommerce_Abstract_Product_Availability_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'og:availability';

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return string The raw value.
	 */
	public function get() {
		if ( $this->is_on_backorder ) {
			return 'backorder';
		}

		if ( $this->is_in_stock ) {
			return 'instock';
		}

		return 'out of stock';
	}
}
