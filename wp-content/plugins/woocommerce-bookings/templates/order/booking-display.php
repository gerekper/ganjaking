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
	$text_align  = is_rtl() ? 'right' : 'left';
	$margin_side = is_rtl() ? 'left' : 'right';

	$show_status_date = ! ( isset( $only_title ) && $only_title );
	$show_title       = ! ( isset( $hide_item_details ) && $hide_item_details );

	foreach ( $booking_ids as $booking_id ) {
		$booking    = new WC_Booking( $booking_id );
		$order      = $booking->get_order();
		$plain_text = false;

		if ( ! $order ) {
			continue;
		}
		?>
		<div class="wc-booking-summary" style="margin-top: 1em">
			<?php
			foreach ( $order->get_items() as $item_id => $item ) {
				if ( $item_id !== $booking->get_order_item_id() ) {
					continue;
				}

				if ( $show_title ) {
					// Product name.
					echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );

					// allow other plugins to add additional product information here.
					do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

					wc_display_item_meta(
						$item,
						array(
							'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
						)
					);
				}

				if ( $show_status_date ) :
					?>
					<strong class="wc-booking-summary-number">
						<?php
						/* translators: 1: booking id */
						printf( esc_html__( 'Booking #%s', 'woocommerce-bookings' ), (string) $booking->get_id() );
						?>
						<span class="status-<?php echo esc_attr( $booking->get_status() ); ?>">
							<?php echo esc_html( wc_bookings_get_status_label( $booking->get_status() ) ); ?>
						</span>
					</strong>

					<?php
					wc_bookings_get_summary_list( $booking, true );
				endif;
			}
			?>
		</div>
		<?php
	}
}
