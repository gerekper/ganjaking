<?php
/**
 * Product Addon Fields
 *
 * @package woocommerce-product-addons
 */

/**
 * WC_Product_Addons_Field class.
 */
abstract class WC_Product_Addons_Field {
	/**
	 * Addon data.
	 *
	 * @var array
	 */
	public $addon;

	/**
	 * Addon value.
	 *
	 * @var string
	 */
	public $value;

	/**
	 * Constructor
	 *
	 * @param array  $addon Addon data.
	 * @param string $value Addon value.
	 */
	public function __construct( $addon, $value = '' ) {
		$this->addon = $addon;
		$this->value = $value;
	}

	/**
	 * Get data for the posted addon
	 */
	public function get_cart_item_data() {
		return false;
	}

	/**
	 * Validate an addon
	 *
	 * @return bool pass or fail, or WP_Error
	 */
	public function validate() {
		return true;
	}

	/**
	 * Get the name of the posted addon
	 *
	 * @return string
	 */
	public function get_field_name() {
		return 'addon-' . sanitize_title( $this->addon['field_name'] );
	}

	/**
	 * Get the label for an option
	 *
	 * @param  string $option The option array object.
	 * @return string
	 */
	public function get_option_label( $option ) {
		return ! empty( $option['label'] ) ? sanitize_text_field( $this->addon['name'] ) . ' - ' . sanitize_text_field( $option['label'] ) : sanitize_text_field( $this->addon['name'] );
	}

	/**
	 * Get the price for an option
	 *
	 * @param  string $option The option array object.
	 * @return string
	 */
	public function get_option_price( $option ) {
		// This gets sanitized before being used.
		return $option['price'];
	}
}
