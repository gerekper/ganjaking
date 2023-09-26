<?php
/**
 * Email delivery details.
 *
 * @package WC_OD/Templates/Emails
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var array  $args            Template arguments.
 * @var string $title           The section title.
 * @var string $date            The selected date by the customer with the ISO 8601 format.
 * @var array  $time_frame      An array with the time frame data.
 * @var bool   $is_local_pickup Whether the order shipping method is a local pickup.
 */
?>
<h2><?php echo esc_html( $title ); ?></h2>

<div style="margin-bottom:40px;">
	<?php
	/**
	 * Fired before displaying the delivery details in the email.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Template arguments.
	 */
	do_action( 'wc_od_email_before_delivery_details', $args );

	if ( $is_local_pickup ) :
		/* translators: %s: pickup date */
		$message = __( 'Pickup date: %s.', 'woocommerce-order-delivery' );
	else :
		/* translators: %s: delivery date */
		$message = __( 'We will try our best to deliver your order on %s.', 'woocommerce-order-delivery' );
	endif;

	echo wp_kses_post( wpautop( sprintf( $message, '<strong>' . wc_od_localize_date( $date ) . '</strong>' ) ) );

	if ( ! empty( $time_frame ) ) :
		/* translators: %s: time frame */
		echo wp_kses_post( wpautop( sprintf( __( 'Time frame: %s', 'woocommerce-order-delivery' ), '<strong>' . wc_od_time_frame_to_string( $time_frame ) . '</strong>' ) ) );
	endif;

	/**
	 * Fired after displaying the delivery details in the email.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Template arguments.
	 */
	do_action( 'wc_od_email_after_delivery_details', $args );
	?>
</div>
