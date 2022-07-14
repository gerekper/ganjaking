<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

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
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Woodeposits {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Woodeposits|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'wc_epo_add_compatibility', [ $this, 'add_compatibility' ] );

	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {
		add_action( 'wpp_add_product_to_cart_holder', [ $this, 'tm_wpp_add_product_to_cart_holder' ], 10, 2 );
	}

	/**
	 * Undocumented function
	 *
	 * @param array  $additional_data Data to aadd.
	 * @param object $product The product object.
	 * @return Array
	 */
	public function tm_wpp_add_product_to_cart_holder( $additional_data, $product ) {

		$epo_data = [
			'tmhasepo',
			'tmcartepo',
			'tmsubscriptionfee',
			'tmcartfee',
			'tm_epo_product_original_price',
			'tm_epo_options_prices',
			'tm_epo_product_price_with_options',
		];

		foreach ( $epo_data as $key => $value ) {
			if ( isset( $product[ $value ] ) ) {
				$additional_data[ $value ] = $product[ $value ];
			}
		}

		return $additional_data;
	}
}
