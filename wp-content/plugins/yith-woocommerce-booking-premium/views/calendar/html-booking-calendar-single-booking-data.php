<?php
/**
 * Single booking data for Month Calendar page html
 *
 * @var YITH_WCBK_Booking|YITH_WCBK_Booking_External $booking
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$booking_edit_link   = $booking->get_edit_link();
$booking_name_format = get_option( 'yith-wcbk-booking-name-format-in-calendar', '#{id} {user_name}' );
$data_to_display     = $booking->get_booking_data_to_display( 'admin' );
?>
<div class="yith-wcbk-booking-calendar-single-booking-data-wrapper">

	<?php
	/**
	 * DO_ACTION: yith_wcbk_calendar_single_booking_data_before
	 * Hook to output something before single booking data shown in the admin calendar.
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 */
	do_action( 'yith_wcbk_calendar_single_booking_data_before', $booking );
	?>

	<div class="yith-wcbk-booking-calendar-single-booking-data-actions__container">
		<?php if ( $booking_edit_link ) : ?>
			<a href="<?php echo esc_url( $booking_edit_link ); ?>" target="_blank">
				<span class="dashicons dashicons-edit yith-wcbk-booking-calendar-single-booking-data-action yith-wcbk-booking-calendar-single-booking-data-action-edit"></span>
			</a>
		<?php endif; ?>
		<span class="dashicons dashicons-no-alt yith-wcbk-booking-calendar-single-booking-data-action yith-wcbk-booking-calendar-single-booking-data-action-close"></span>
	</div>

	<div class="yith-wcbk-booking-calendar-single-booking-data-title__container">
		<div class="yith-wcbk-booking-calendar-single-booking-data-title"><?php echo wp_kses_post( apply_filters( 'yith_wcbk_calendar_single_booking_data_booking_title', $booking->get_formatted_name( $booking_name_format ), $booking ) ); ?></div>
	</div>

	<div class="yith-wcbk-booking-calendar-single-booking-data-table__container">

		<table class="yith-wcbk-booking-calendar-single-booking-data-table">
			<?php
			/**
			 * DO_ACTION: yith_wcbk_calendar_single_booking_data_table_before
			 * Hook to output something before single booking data in the table shown in the admin calendar.
			 *
			 * @param YITH_WCBK_Booking $booking The booking.
			 */
			do_action( 'yith_wcbk_calendar_single_booking_data_table_before', $booking );
			?>

			<?php foreach ( $data_to_display as $data_key => $data ) : ?>
				<?php
				$data_label = $data['label'] ?? '';
				$data_value = $data['display'] ?? '';
				?>
				<?php if ( $data_value ) : ?>
					<tr>
						<th><?php echo esc_html( $data_label ); ?></th>
						<td><?php echo wp_kses_post( $data_value ); ?> </td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php
			/**
			 * DO_ACTION: yith_wcbk_calendar_single_booking_data_table_after
			 * Hook to output something after single booking data in the table shown in the admin calendar.
			 *
			 * @param YITH_WCBK_Booking $booking The booking.
			 */
			do_action( 'yith_wcbk_calendar_single_booking_data_table_after', $booking );
			?>

		</table>
	</div>

	<?php
	/**
	 * DO_ACTION: yith_wcbk_calendar_single_booking_data_after
	 * Hook to output something after single booking data shown in the admin calendar.
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 */
	do_action( 'yith_wcbk_calendar_single_booking_data_after', $booking );
	?>
</div>
