<?php
/**
 * PIP delivery details.
 *
 * @package WC_OD/Templates
 * @version 1.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Global variables.
 *
 * @global string $document_type
 * @global string $delivery_date
 * @global array  $delivery_time_frame
 */

printf(
	'<br><br><span class="delivery-date"><strong>%1$s</strong> %2$s</span>',
	esc_html__( 'Delivery date:', 'woocommerce-order-delivery' ),
	esc_html( wc_od_localize_date( $delivery_date ) )
);

if ( $delivery_time_frame ) {
	printf(
		'<br><span class="delivery-time-frame"><strong>%1$s</strong> %2$s</span>',
		esc_html__( 'Time frame:', 'woocommerce-order-delivery' ),
		esc_html( wc_od_time_frame_to_string( $delivery_time_frame ) )
	);
}
