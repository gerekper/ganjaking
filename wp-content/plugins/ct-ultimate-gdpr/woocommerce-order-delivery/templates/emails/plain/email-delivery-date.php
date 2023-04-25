<?php
/**
 * Email delivery details (plain text)
 *
 * @package WC_OD/Templates/Emails/Plain
 * @version 1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Global variables.
 *
 * @global string $delivery_date
 * @global array $delivery_time_frame
 */

echo "\n" . esc_html( strtoupper( $title ) ) . "\n\n";

do_action( 'wc_od_email_before_delivery_details', $args );

/* translators: %s: delivery date */
echo sprintf( esc_html__( 'We will try our best to deliver your order on %s.', 'woocommerce-order-delivery' ), $delivery_date ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

if ( ! empty( $delivery_time_frame ) ) :
	/* translators: %s: delivery time frame */
	echo sprintf( esc_html__( 'Time frame: %s', 'woocommerce-order-delivery' ), wc_od_time_frame_to_string( $delivery_time_frame ) ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
endif;

do_action( 'wc_od_email_after_delivery_details', $args );
