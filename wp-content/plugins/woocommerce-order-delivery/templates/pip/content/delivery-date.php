<?php
/**
 * PIP delivery details.
 *
 * @package WC_OD/Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var string $date            The selected date by the customer with the ISO 8601 format.
 * @var array  $time_frame      An array with the time frame data.
 * @var bool   $is_local_pickup Whether the order shipping method is a local pickup.
 */

if ( $is_local_pickup ) :
	$label = __( 'Pickup date:', 'woocommerce-order-delivery' );
else :
	$label = __( 'Delivery date:', 'woocommerce-order-delivery' );
endif;

printf(
	'<br><br><span class="wc-od-pip-details-date"><strong>%1$s</strong> %2$s</span>',
	esc_html( $label ),
	esc_html( wc_od_localize_date( $date ) )
);

if ( $time_frame ) {
	printf(
		'<br><span class="wc-od-pip-details-time-frame"><strong>%1$s</strong> %2$s</span>',
		esc_html__( 'Time frame:', 'woocommerce-order-delivery' ),
		esc_html( wc_od_time_frame_to_string( $time_frame ) )
	);
}
