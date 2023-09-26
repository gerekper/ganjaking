<?php
/**
 * Specific pickup details.
 *
 * @package WC_OD/Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var string $date       The pickup date in ISO 8601 format.
 * @var array  $time_frame An array with the pickup time frame data.
 */

/* translators: %s: pickup date */
echo wp_kses_post( wpautop( sprintf( __( 'Pickup date: %s', 'woocommerce-order-delivery' ), '<strong>' . wc_od_localize_date( $date ) . '</strong>' ) ) );

if ( ! empty( $time_frame ) ) :
	/* translators: %s: time frame */
	echo wp_kses_post( wpautop( sprintf( __( 'Time frame: %s', 'woocommerce-order-delivery' ), '<strong>' . wc_od_time_frame_to_string( $time_frame ) . '</strong>' ) ) );
endif;
