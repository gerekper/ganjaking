<?php
/**
 * The template for displaying a booking summary to customers.
 * It will display in three places:
 * - After checkout,
 * - In the order confirmation email, and
 * - When customer reviews order in My Account > Orders.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/order/booking-display.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @author  Automattic
 * @version 1.10.8
 * @since   1.10.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( $booking_ids ) {
	foreach ( $booking_ids as $booking_id ) {
		$booking = new WC_Booking( $booking_id );
		?>
		<div class="wc-booking-summary">
			<strong class="wc-booking-summary-number">
				<?php
				/* translators: 1: booking id */
				echo esc_html( sprintf( __( 'Booking #%s', 'woocommerce-bookings' ), (string) $booking->get_id() ) );
				?>
				<span class="status-<?php echo esc_attr( $booking->get_status() ); ?>">
					<?php echo esc_html( wc_bookings_get_status_label( $booking->get_status() ) ); ?>
				</span>
			</strong>
			<?php wc_bookings_get_summary_list( $booking ); ?>
			<div class="wc-booking-summary-actions">
				<?php if ( $booking_id && function_exists( 'wc_get_endpoint_url' ) && wc_get_page_id( 'myaccount' ) && 0 !== $booking->get_customer_id() ) : ?>
					<a href="<?php echo esc_url( wc_get_endpoint_url( $endpoint, '', wc_get_page_permalink( 'myaccount' ) ) ); ?>"><?php esc_html_e( 'View my bookings &rarr;', 'woocommerce-bookings' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
