<?php
/**
 * Class YITH_WCBK_Wpml_Booking_Product
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Wpml_Booking_Product
 *
 * @since   1.0.3
 */
class YITH_WCBK_Wpml_Booking_Product {
	/**
	 * Single intance of the class.
	 *
	 * @var YITH_WCBK_Wpml_Booking_Product
	 */
	private static $instance;

	/**
	 * WPML Integration instance.
	 *
	 * @var YITH_WCBK_Wpml_Integration
	 */
	public $wpml_integration;

	/**
	 * Singleton implementation
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration WPML Integration instance.
	 *
	 * @return YITH_WCBK_Wpml_Booking_Product
	 */
	public static function get_instance( $wpml_integration ) {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new static( $wpml_integration );
	}

	/**
	 * Constructor
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration WPML Integration instance.
	 */
	private function __construct( $wpml_integration ) {
		$this->wpml_integration = $wpml_integration;

		// Get the parent id of the booking product to associate it to the Booking object.
		add_filter( 'yith_wcbk_booking_product_id_to_translate', array( 'YITH_WCBK_Wpml_Integration', 'get_parent_id' ) );

		add_filter( 'yith_wcbk_request_confirmation_product_id', array( 'YITH_WCBK_Wpml_Integration', 'get_parent_id' ) );

		// Get the parent id of the booking product for cache data.
		add_filter( 'yith_wcbk_cache_get_object_data_product_id', array( 'YITH_WCBK_Wpml_Integration', 'get_parent_id' ) );
	}
}
