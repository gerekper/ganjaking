<?php
/**
 * Specific delivery details.
 *
 * @package WC_OD/Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var string $date       The delivery date in ISO 8601 format.
 * @var array  $time_frame An array with the delivery time frame data.
 */

/* translators: %s: delivery date */
echo wp_kses_post( wpautop( sprintf( __( 'We will try our best to deliver your order on %s.', 'woocommerce-order-delivery' ), '<strong>' . wc_od_localize_date( $date ) . '</strong>' ) ) );

if ( ! empty( $time_frame ) ) :
	/* translators: %s: time frame */
	echo wp_kses_post( wpautop( sprintf( __( 'Time frame: %s', 'woocommerce-order-delivery' ), '<strong>' . wc_od_time_frame_to_string( $time_frame ) . '</strong>' ) ) );
endif;
