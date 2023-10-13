<?php
/**
 * Booking List in Calendar page html
 *
 * @var YITH_WCBK_Booking_Abstract[]|YITH_WCBK_Booking[]|YITH_WCBK_Booking_External[] $bookings
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$bookings            = ! ! $bookings ? $bookings : array();
$bookings            = (array) apply_filters( 'yith_wcbk_calendar_booking_list_bookings', $bookings );
$booking_name_format = get_option( 'yith-wcbk-booking-name-format-in-calendar', '#{id} {user_name}' );
?>

<?php foreach ( $bookings as $booking ) : ?>
	<?php if ( $booking instanceof YITH_WCBK_Booking_Abstract ) : ?>
		<?php
		$booking_id    = ! $booking->is_external() ? $booking->get_id() : 'external-' . $booking->get_id();
		$booking_class = 'yith-wcbk-booking-calendar-single-booking-' . $booking_id;
		$classes       = (array) apply_filters(
			'yith_wcbk_calendar_booking_classes',
			array(
				'yith-wcbk-booking-calendar-single-booking',
				$booking_class,
				$booking->get_status(),
			),
			$booking
		);
		$classes       = implode( ' ', $classes );
		?>
		<div class="<?php echo esc_attr( $classes ); ?>"
				data-booking-id="<?php echo esc_attr( $booking_id ); ?>"
				data-booking-class="<?php echo esc_attr( $booking_class ); ?>"
			<?php if ( $booking->is_external() ) : ?>
				data-external-host="<?php echo esc_attr( $booking->get_source_slug() ); ?>"
			<?php endif; ?>
		>
			<div class="yith-wcbk-booking-calendar-single-booking-title">
				<h3>
					<?php
					if ( $booking->is_external() ) {
						$calendar_name  = $booking->get_calendar_name();
						$formatted_name = $booking->get_formatted_name( '' );
						if ( ! ! $calendar_name ) {
							echo '<span class="yith-wcbk-booking-calendar-single-booking-title__external-calendar">' . esc_html( $calendar_name ) . '</span>';
						}
					} else {
						$formatted_name = $booking->get_formatted_name( $booking_name_format );
					}

					echo wp_kses_post( apply_filters( 'yith_wcbk_calendar_booking_title', $formatted_name, $booking ) );
					?>
				</h3>
			</div>
			<div class="yith-wcbk-booking-calendar-single-booking-data">
				<?php include 'html-booking-calendar-single-booking-data.php'; ?>
			</div>
		</div>
	<?php endif; ?>
<?php endforeach; ?>
