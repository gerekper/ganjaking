<?php
/**
 * WC_PB_Tracks class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.18.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tracks support.
 *
 * @class    WC_PB_Tracks
 * @version  6.18.6
 */
class WC_PB_Tracks {

	/**
	 * Tracks event name prefix.
	 */
	const PREFIX = 'pb_';

	/**
	 * Hook in.
	 */
	public static function init() {

		// Record event on bundle or bundle-sell creation.
		add_action( 'woocommerce_before_product_object_save', array( __CLASS__, 'record_bundle_created_event' ) );
	}

	/**
	 * Records a 'bundle_created' event in Tracks every time a product bundle is created.
	 * Records a 'bundle_sell_created' event in Tracks every time a bundle-sell is first created on a product.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function record_bundle_created_event( $product ) {

		// Bail early.
		if ( ! class_exists( 'WC_Tracks' ) || ! class_exists( 'WC_Site_Tracking' ) || ! WC_Site_Tracking::is_tracking_enabled() ) {
			return;
		}

		if ( $product->is_type( 'bundle' ) ) {

			$bundle_exists_in_db = true;

			// This never seems to happen anymore when creating a bundle via the WP Dashboard.
			if ( $product->get_id() == 0 ) {

				$bundle_exists_in_db = false;

			} else {
				/*
				 * We have no better way to detect a new bundle. It seems that get_id() always returns a non-zero value when bundles are created manually.
				 * This seems to have something to do with WP creating an auto-draft before publishing a new product. The result is that the 'woocommerce_new_product' hook does not run.
				 */
				//
				$bundle_exists_in_db = metadata_exists( 'post', $product->get_id(), '_wc_pb_base_price' );
			}

			// First time saving this product?
			if ( ! $bundle_exists_in_db ) {
				self::record_event( 'bundle_created' );
			}

		} else {

			$bundle_sells             = WC_PB_BS_Product::get_bundle_sell_ids( $product, 'edit' );
			$has_bundle_sells_to_save = ! empty( $bundle_sells );
			$has_bundle_sells_in_db   = metadata_exists( 'post', $product->get_id(), '_wc_pb_bundle_sell_ids' );

			// First time saving bundle-sells on this product?
			if ( $has_bundle_sells_to_save && ! $has_bundle_sells_in_db ) {
				self::record_event( 'bundle_sell_created' );
			}
		}
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

WC_PB_Tracks::init();
