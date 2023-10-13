<?php
/**
 * Booking Actions Template
 * Shows booking actions
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/booking-actions.php.
 *
 * @var YITH_WCBK_Booking $booking          The booking.
 * @var bool              $show_view_action True if the view action should be shown.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$show_view_action = isset( $show_view_action ) ? ! ! $show_view_action : false;
?>

<div class="yith-wcbk-booking-actions">
	<?php
	$actions = array(
		'pay'    => array(
			'url'  => $booking->get_confirmed_booking_payment_url(),
			'name' => __( 'Pay', 'yith-booking-for-woocommerce' ),
		),
		'view'   => array(
			'url'  => $booking->get_view_booking_url(),
			'name' => __( 'View', 'yith-booking-for-woocommerce' ),
		),
		'cancel' => array(
			'url'   => $booking->get_cancel_booking_url(),
			'name'  => __( 'Cancel', 'yith-booking-for-woocommerce' ),
			'class' => 'yith-wcbk-confirm-button',
			'data'  => array(
				'confirm-text'  => __( 'Confirm', 'yith-booking-for-woocommerce' ),
				'confirm-class' => 'yith-wcbk-confirm-cancel-button',
			),
		),
	);

	if ( ! $show_view_action ) {
		unset( $actions['view'] );
	}

	if ( ! $booking->has_status( 'confirmed' ) ) {
		unset( $actions['pay'] );
	}

	if ( ! $booking->can_be( 'cancelled_by_user' ) ) {
		unset( $actions['cancel'] );
	}

	$actions = apply_filters( 'yith_wcbk_bookings_actions', $actions, $booking );

	if ( $actions ) {
		foreach ( $actions as $key => $_action ) {
			$class = isset( $_action['class'] ) ? sanitize_html_class( $_action['class'] ) : '';
			$data  = '';
			if ( isset( $_action['data'] ) && is_array( $_action['data'] ) ) {
				foreach ( $_action['data'] as $data_id => $data_value ) {
					$data .= 'data-' . $data_id . '="' . $data_value . '" ';
				}
			}
			echo '<a href="' . esc_url( $_action['url'] ) . '" class="button ' . esc_attr( $key ) . ' ' . esc_attr( $class ) . '" ' . esc_attr( $data ) . '>' . esc_html( $_action['name'] ) . '</a>';
		}
	}
	?>
</div>
