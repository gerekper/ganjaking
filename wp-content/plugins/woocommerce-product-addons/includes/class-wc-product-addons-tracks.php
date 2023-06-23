<?php
/**
 * WC_PAO_Tracks class
 *
 * @package  WooCommerce Product Add-ons
 * @since    6.3.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tracks support.
 *
 * @class    WC_PAO_Tracks
 * @version  6.3.3
 */
class WC_PAO_Tracks {

	/**
	 * Tracks event name prefix.
	 */
	const PREFIX = 'pao_';

	/**
	 * Hook in.
	 */
	public static function init() {

		// Records an event when product-level add-ons are created.
		add_action( 'woocommerce_before_product_object_save', array( __CLASS__, 'record_product_with_addons_created_event' ) );
		// Records an event when global add-ons are created.
		add_action( 'woocommerce_product_addons_global_create_addons', array( __CLASS__, 'record_global_addon_created_event' ) );

	}

	/**
	 * Records a 'product_with_addons_created' event in Tracks every time add-ons are added to product that didn't have add-ons before.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function record_product_with_addons_created_event( $product ) {

		// Bail early.
		if ( ! class_exists( 'WC_Tracks' ) || ! class_exists( 'WC_Site_Tracking' ) || ! WC_Site_Tracking::is_tracking_enabled() ) {
			return;
		}

		$addons             = $product->get_meta( '_product_addons', true );
		$has_addons_to_save = ! empty( $addons );
		$addons_in_db       = get_post_meta( $product->get_id(), '_product_addons', true );
		$has_addons_in_db   = ! empty( $addons_in_db );

		// First time saving add-ons on this product?
		if ( $has_addons_to_save && ! $has_addons_in_db ) {
			self::record_event( 'product_with_addons_created' );
		}
	}

	/**
	 * Records a 'global_addon_created' event in Tracks every time a global add-on is created.
	 *
	 * @return void
	 */
	public static function record_global_addon_created_event() {

		// Bail early.
		if ( ! class_exists( 'WC_Tracks' ) || ! class_exists( 'WC_Site_Tracking' ) || ! WC_Site_Tracking::is_tracking_enabled() ) {
			return;
		}

		self::record_event( 'global_addon_created' );
	}

	/**
	 * Record an event in Tracks - this is the preferred way to record events from PHP.
	 *
	 * @param string $event_name The name of the event.
	 * @param array  $props Custom properties to send with the event.
	 * @return bool|WP_Error True for success or WP_Error if the event pixel could not be fired.
	 */
	public static function record_event( $event_name, $props = array() ) {
		$full_event_name = self::PREFIX . $event_name;
		WC_Tracks::record_event( $full_event_name, $props );
	}
}

WC_PAO_Tracks::init();
