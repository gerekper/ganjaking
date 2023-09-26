<?php
/**
 * Template functions.
 *
 * @package WC_OD/Functions
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Displays the delivery details in the checkout form.
 *
 * @since 2.6.0
 *
 * @param array $args Template arguments.
 */
function wc_od_checkout_details( $args ) {
	if ( 'text' !== $args['checkout_option'] ) {
		return;
	}

	if ( $args['is_local_pickup'] ) {
		wc_od_get_template(
			'order-delivery/estimated-pickup-details.php',
			array(
				'pickup_date'  => $args['shipping_date'],
				'pickup_range' => $args['delivery_range'],
			)
		);
	} else {
		wc_od_get_template(
			'order-delivery/estimated-delivery-details.php',
			array(
				'shipping_date'  => $args['shipping_date'],
				'delivery_range' => $args['delivery_range'],
			)
		);
	}
}
add_action( 'wc_od_checkout_delivery_details', 'wc_od_checkout_details' );
