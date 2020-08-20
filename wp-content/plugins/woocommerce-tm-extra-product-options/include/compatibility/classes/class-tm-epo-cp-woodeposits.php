<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WooDeposits - Woocommerce partial payments and deposits plugin 
 * http://webatix.com/
 *
 * The particular plugin is not currently in development
 * and this compatibility may be removed in a future update.
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_woodeposits {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'wc_epo_add_compatibility', array( $this, 'add_compatibility' ) );

	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {
		add_action( 'wpp_add_product_to_cart_holder', array( $this, 'tm_wpp_add_product_to_cart_holder' ), 10, 2 );
	}

	public function tm_wpp_add_product_to_cart_holder( $additional_data, $product ) {

		$epo_data = array(
			"tmhasepo",
			"tmcartepo",
			"tmsubscriptionfee",
			"tmcartfee",
			"tm_epo_product_original_price",
			"tm_epo_options_prices",
			"tm_epo_product_price_with_options"
		);

		foreach ( $epo_data as $key => $value ) {
			if ( isset( $product[ $value ] ) ) {
				$additional_data[ $value ] = $product[ $value ];
			}
		}

		return $additional_data;
	}
}
