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
 * Store Exporter Deluxe for WooCommerce
 * https://www.visser.com.au/solutions/woocommerce-export/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Store_Exporter {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Store_Exporter|null
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
		add_filter( 'woo_ce_order_item', [ $this, 'tm_woo_ce_extend_order_item' ], 9999, 2 );
	}

	/**
	 * Change order item.
	 *
	 * @param array   $order_item The order item.
	 * @param integer $order_id The order id.
	 * @return array
	 */
	public function tm_woo_ce_extend_order_item( $order_item = [], $order_id = 0 ) {

		if ( function_exists( 'woo_ce_get_extra_product_option_fields' ) ) {
			$tm_fields = woo_ce_get_extra_product_option_fields();
			if ( $tm_fields ) {
				foreach ( $tm_fields as $tm_field ) {
					$order_item->{sprintf( 'tm_%s', sanitize_key( $tm_field['name'] ) )} = $tm_field['value'];
				}
			}

			unset( $tm_fields, $tm_field );
		}

		return $order_item;
	}

}
