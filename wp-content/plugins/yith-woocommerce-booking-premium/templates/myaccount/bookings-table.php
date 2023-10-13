<?php
/**
 * Bookings Table
 * Shows booking on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/bookings-table.php.
 *
 * @var YITH_WCBK_Booking[] $bookings     The bookings.
 * @var bool                $has_bookings True if there are bookings to show.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$account_bookings_columns = array(
	'booking-id'      => __( 'Booking', 'yith-booking-for-woocommerce' ),
	'booking-from'    => __( 'From', 'yith-booking-for-woocommerce' ),
	'booking-to'      => __( 'To', 'yith-booking-for-woocommerce' ),
	'booking-status'  => __( 'Status', 'yith-booking-for-woocommerce' ),
	'booking-actions' => '&nbsp;',

);
$account_bookings_columns = apply_filters( 'yith_wcbk_my_account_booking_columns', $account_bookings_columns );
?>
<?php
/**
 * DO_ACTION: yith_wcbk_before_bookings_table
 * Hook to output something before the table in the bookings' list in My Account > Bookings.
 *
 * @param bool $has_bookings True if the current user has at least one booking.
 */
do_action( 'yith_wcbk_before_bookings_table', $has_bookings );
?>

<?php if ( $has_bookings ) : ?>

	<table class="shop_table shop_table_responsive my_account_bookings account-bookings-table">
		<thead>
		<tr>
			<?php foreach ( $account_bookings_columns as $column_id => $column_name ) : ?>
				<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
			<?php endforeach; ?>
		</tr>
		</thead>

		<tbody>
		<?php foreach ( $bookings as $booking ) : ?>
			<?php
			$the_order = $booking->get_order();
			?>
			<tr class="booking">
				<?php foreach ( $account_bookings_columns as $column_id => $column_name ) : ?>
					<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
						<?php
						if ( has_action( 'yith_wcbk_my_account_booking_column_' . $column_id ) ) {
							/**
							 * DO_ACTION: yith_wcbk_my_account_booking_column_$column_id
							 * Hook to output something in a specific column in the bookings' list in My Account > Bookings.
							 *
							 * @param WC_Order          $the_order The order.
							 * @param YITH_WCBK_Booking $booking   The booking.
							 */
							do_action( 'yith_wcbk_my_account_booking_column_' . $column_id, $the_order, $booking );
						} else {
							switch ( $column_id ) {
								case 'booking-id':
									$url = esc_url( $booking->get_view_booking_url() );
									echo '<a href="' . esc_url( $url ) . '">' . esc_html( $booking->get_title() ) . '</a>';
									break;
								case 'booking-order':
									$order_title = _x( '#', 'hash before order number', 'woocommerce' ) . $the_order->get_order_number();
									echo '<a href="' . esc_url( $the_order->get_view_order_url() ) . '">' . esc_html( $order_title ) . '</a>';
									break;
								case 'booking-from':
									echo esc_html( $booking->get_formatted_from() );
									break;
								case 'booking-to':
									echo esc_html( $booking->get_formatted_to() );
									break;
								case 'booking-status':
									echo esc_html( $booking->get_status_text() );
									break;
								case 'booking-actions':
									/**
									 * DO_ACTION: yith_wcbk_show_booking_actions
									 * Hook to output something in the "actions" column in the bookings' list in My Account > Bookings.
									 *
									 * @hooked yith_wcbk_booking_actions - 10
									 *
									 * @param YITH_WCBK_Booking $booking          The booking.
									 * @param bool              $show_view_action True to include the "view" action.
									 */
									do_action( 'yith_wcbk_show_booking_actions', $booking, true );
									break;
							}
						}
						?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php
	/**
	 * DO_ACTION: yith_wcbk_before_account_bookings_pagination
	 * Hook to output something before pagination in the bookings' list in My Account > Bookings.
	 */
	do_action( 'yith_wcbk_before_account_bookings_pagination' );
	?>

<?php endif; ?>

<?php
/**
 * DO_ACTION: yith_wcbk_after_bookings_table
 * Hook to output something after the table in the bookings' list in My Account > Bookings.
 *
 * @param bool $has_bookings True if the current user has at least one booking.
 */
do_action( 'yith_wcbk_after_bookings_table', $has_bookings );
?>
