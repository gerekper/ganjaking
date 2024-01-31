<?php
/**
 * Tracking info template for plain email.
 *
 * @package woocommerce-shipment-tracking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shipment Tracking
 *
 * Shows tracking information in the plain text order email
 *
 * @package woocommerce-shipment-tracking
 * @version 1.6.4
 */

if ( $tracking_items ) :

	/**
	 * Filter to manipulate the tracking information title.
	 *
	 * @since 1.6.4
	 */
	echo esc_html( apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( 'TRACKING INFORMATION', 'woocommerce-shipment-tracking' ) ) );

		echo "\n";

	foreach ( $tracking_items as $tracking_item ) {
		echo esc_html( $tracking_item['formatted_tracking_provider'] ) . "\n";
		echo esc_html( $tracking_item['tracking_number'] ) . "\n";
		echo esc_url( $tracking_item['formatted_tracking_link'] ) . "\n\n";
	}

	echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= \n\n";

endif;


