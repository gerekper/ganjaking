<?php
/**
 * Booking PDF Template - User info
 *
 * @var YITH_WCBK_Booking $booking  The booking.
 * @var bool              $is_admin Is admin flag.
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! $booking->get_user_id() || apply_filters( 'yith_wcbk_show_user_info_in_pdf_only_for_admin', ! $is_admin ) ) {
	return;
}

$user_id = $booking->get_user_id();
$user    = $booking->get_user();
if ( ! $user ) {
	return;
}

$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
$user_link   = get_edit_user_link( $user_id );
?>
<h3><?php esc_html_e( 'User info', 'yith-booking-for-woocommerce' ); ?></h3>
<table class="booking-table booking-user-info">
	<tr>
		<th scope="row"><?php esc_html_e( 'User', 'yith-booking-for-woocommerce' ); ?></th>
		<td><a href="<?php echo esc_url( $user_link ); ?>"><?php echo esc_html( $user->nickname ); ?></a></td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'First Name', 'yith-booking-for-woocommerce' ); ?></th>
		<td><?php echo esc_html( $user->user_firstname ); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Last Name', 'yith-booking-for-woocommerce' ); ?></th>
		<td><?php echo esc_html( $user->user_lastname ); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Email', 'yith-booking-for-woocommerce' ); ?></th>
		<td><a href="mailto:<?php echo esc_attr( $user->user_email ); ?>"><?php echo esc_html( $user->user_email ); ?></a></td>
	</tr>
</table>
