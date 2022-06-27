<?php
/**
 * Abstract data class which includes shipping methods.
 *
 * @package WC_OD/Abstracts
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_OD_Shipping_Methods_Data class.
 *
 * @deprecated 2.0.0 Use the trait WC_OD_Data_Shipping_Methods instead.
 */
class WC_OD_Shipping_Methods_Data extends WC_OD_Data {

	use WC_OD_Data_Shipping_Methods;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param array $data The object data.
	 */
	public function __construct( array $data = array() ) {
		wc_deprecated_function( 'WC_OD_Shipping_Methods_Data::__construct', '2.0.0', 'WC_OD_Data_Shipping_Methods' );

		$this->data = array_merge( $this->data, $this->get_default_shipping_methods_data() );

		parent::__construct( $data );
	}

	/**
	 * Gets the default shipping methods data.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @since 1.6.0
	 * @@deprecated 2.0.0
	 *
	 * @return array
	 */
	protected function get_default_shipping_method_data() {
		wc_deprecated_function( __FUNCTION__, '2.0.0', 'WC_OD_Data_Shipping_Methods->get_default_shipping_methods_data()' );

		return $this->get_default_shipping_methods_data();
	}
}
