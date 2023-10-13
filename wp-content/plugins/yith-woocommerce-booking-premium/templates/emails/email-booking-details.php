<?php
/**
 * Booking details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-booking-details.php.
 *
 * @var YITH_WCBK_Booking $booking       The booking.
 * @var string            $email_heading The heading.
 * @var WC_Email          $email         The email.
 * @var bool              $sent_to_admin Is this sent to admin?
 * @var bool              $plain_text    Is this plain?
 *
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit;

$booking_url     = $sent_to_admin ? get_edit_post_link( $booking->get_id() ) : $booking->get_view_booking_url();
$order_id        = apply_filters( 'yith_wcbk_email_booking_details_order_id', $booking->get_order_id(), $booking, $sent_to_admin, $plain_text, $email );
$the_order       = ! ! $order_id ? wc_get_order( $order_id ) : false;
$args            = array(
	'order_id'       => $order_id,
	'order'          => $the_order,
	'split_services' => true,
);
$data_to_display = $booking->get_booking_data_to_display( $sent_to_admin ? 'admin' : 'frontend', $args );
if ( isset( $data_to_display['status'] ) ) {
	unset( $data_to_display['status'] );
}
/**
 * DO_ACTION: yith_wcbk_email_before_booking_table
 * Hook to output something before the booking details table in emails.
 *
 * @param YITH_WCBK_Booking $booking       The booking.
 * @param bool              $sent_to_admin True if the email is sent to admin, false otherwise.
 * @param bool              $plain_text    True if the email type is text/plain.
 * @param WC_Email          $email         The email object
 */
do_action( 'yith_wcbk_email_before_booking_table', $booking, $sent_to_admin, $plain_text, $email ); ?>
<div class="booking-details__wrapper">
	<table class="booking-details" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; background: #f1f1f1" border="0">
		<tr>
			<th scope="row" colspan="2" style="text-align:left;"><?php esc_html_e( 'Booking ID', 'yith-booking-for-woocommerce' ); ?></th>
			<td style="text-align:left;"><a href="<?php echo esc_url( $booking_url ); ?>">#<?php echo esc_html( $booking->get_id() ); ?></a></td>
		</tr>

		<?php foreach ( $data_to_display as $data_key => $data ) : ?>
			<?php
			$data_label = $data['label'] ?? '';
			$data_value = $data['display'] ?? '';
			?>
			<?php if ( $data_value ) : ?>
				<tr>
					<th scope="row" colspan="2" style="text-align:left;"><?php echo esc_html( $data_label ); ?></th>
					<td style="text-align:left;"><?php echo wp_kses_post( $data_value ); ?> </td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>

		<tr class="booking-status-row">
			<th scope="row" colspan="2" style="text-align:left;"><?php esc_html_e( 'Status', 'yith-booking-for-woocommerce' ); ?></th>
			<td class="booking-status booking-status--<?php echo esc_attr( $booking->get_status() ); ?>" style="text-align:left;"><?php echo esc_html( $booking->get_status_text() ); ?></td>
		</tr>
	</table>


	<?php if ( ( $sent_to_admin && $booking->has_status( 'pending-confirm' ) ) || ( ! $sent_to_admin && $booking->has_status( 'confirmed' ) ) ) : ?>
		<div class="booking-actions">
			<?php if ( $sent_to_admin && $booking->has_status( 'pending-confirm' ) ) : ?>
				<?php
				$confirm_url = $booking->get_mark_action_url( 'confirmed', array( 'source' => 'email' ) );
				$reject_url  = $booking->get_mark_action_url( 'unconfirmed', array( 'source' => 'email' ) );
				?>
				<div class="booking-actions__row">
					<a class='booking-button booking-action--confirm' href="<?php echo esc_url( $confirm_url ); ?>"><?php esc_html_e( 'Confirm booking', 'yith-booking-for-woocommerce' ); ?></a>
				</div>
				<div class="booking-actions__row">
					<?php
					echo wp_kses_post(
						sprintf(
						// translators: %s is an action link.
							_x( 'or %s', 'Email action alternative', 'yith-booking-for-woocommerce' ),
							sprintf(
								'<a class="booking-link booking-action--reject" href="%s">%s</a>',
								esc_url( $reject_url ),
								esc_html__( 'Reject booking', 'yith-booking-for-woocommerce' )
							)
						)
					);
					?>
				</div>

			<?php elseif ( ! $sent_to_admin && $booking->has_status( 'confirmed' ) ) : ?>
				<?php
				$pay_url  = $booking->get_confirmed_booking_payment_url();
				$view_url = $booking->get_view_booking_url();
				?>
				<div class="booking-actions__row">
					<a class="booking-button booking-action--pay" href="<?php echo esc_url( $pay_url ); ?>"><?php esc_html_e( 'Pay booking', 'yith-booking-for-woocommerce' ); ?></a>
				</div>
				<div class="booking-actions__row">
					<?php
					echo wp_kses_post(
						sprintf(
						// translators: %s is an action link.
							_x( 'or %s', 'Email action alternative', 'yith-booking-for-woocommerce' ),
							sprintf(
								'<a class="booking-link booking-action--view" href="%s">%s</a>',
								esc_url( $view_url ),
								esc_html__( 'View booking details', 'yith-booking-for-woocommerce' )
							)
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</div>

<?php
/**
 * DO_ACTION: yith_wcbk_email_after_booking_table
 * Hook to output something after the booking details table in emails.
 *
 * @param YITH_WCBK_Booking $booking       The booking.
 * @param bool              $sent_to_admin True if the email is sent to admin, false otherwise.
 * @param bool              $plain_text    True if the email type is text/plain.
 * @param WC_Email          $email         The email object
 */
do_action( 'yith_wcbk_email_after_booking_table', $booking, $sent_to_admin, $plain_text, $email );
?>
