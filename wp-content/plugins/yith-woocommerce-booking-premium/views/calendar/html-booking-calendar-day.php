<?php
/**
 * Day Calendar page html
 *
 * @var YITH_WCBK_Booking_Calendar $this
 * @var array                      $args
 * @var string                     $view
 * @var string                     $date
 * @var string                     $time_step
 * @var string                     $start_time
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$show_externals = yith_wcbk()->settings->show_externals_in_calendar();

$product_id = ! empty( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$_product   = ! ! $product_id ? wc_get_product( $product_id ) : false;
if ( $_product && ! $_product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
	$_product = false;
}
/**
 * The booking product
 *
 * @var WC_Product_Booking|false $_product
 */

// translate the time step.
$strtotime_time_step = str_replace( 'h', ' hours', $time_step );
$strtotime_time_step = str_replace( 'm', ' minutes', $strtotime_time_step );
$strtotime_time_step = '+' . $strtotime_time_step;

if ( $_product ) {
	// TODO: maybe we can use time-slot ranges to show only these time-slots.
	$time_slot_ranges = $_product->get_daily_time_slot_ranges( strtotime( $date ) );
	$daily_start_time = ! ! $time_slot_ranges ? current( $time_slot_ranges )['from'] : '00:00';
	if ( $daily_start_time > $start_time ) {
		$start_time = $daily_start_time;
	}
}
?>

<div id="yith-wcbk-booking-calendar-wrap">
	<?php $this->print_action_bar( $args ); ?>
	<div class="yith-wcbk-booking-calendar yith-wcbk-booking-calendar--day-view">
		<?php
		$timestamp     = strtotime( "$date {$start_time}" );
		$end_timestamp = strtotime( '+1 day midnight', $timestamp ) - 1;
		?>
		<div class="yith-wcbk-booking-calendar-day-row yith-wcbk-booking-calendar-day-row--all-day">
			<div class="yith-wcbk-booking-calendar-day-time"><?php esc_html_e( 'All Day', 'yith-booking-for-woocommerce' ); ?>
				<?php
				if ( $_product && 'day' === $_product->get_duration_unit() ) {
					echo wp_kses_post( yith_wcbk_get_calendar_product_availability_per_units_html( $_product, $timestamp, $end_timestamp, 'day' ) );
				}
				?>
			</div>
			<div class="yith-wcbk-booking-calendar-day-container yith-wcbk-create-booking"
					data-create-start-date="<?php echo esc_attr( gmdate( 'Y-m-d', $timestamp ) ); ?>"
			>
				<div class="bookings">
					<?php
					$bookings = yith_wcbk_booking_helper()->get_bookings_in_time_range( $timestamp, $end_timestamp, array( 'month', 'day' ), $show_externals, $product_id );
					require 'html-booking-calendar-booking-list.php';
					?>
					<?php if ( current_user_can( 'yith_create_booking' ) ) : ?>
						<div class="yith-wcbk-booking-calendar-create-button yith-wcbk-create-booking"
								data-create-start-date="<?php echo esc_attr( gmdate( 'Y-m-d', $timestamp ) ); ?>"
								data-create-start-time="<?php echo esc_attr( gmdate( 'H:i', $timestamp ) ); ?>"
						>
							<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"></path>
							</svg>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		$index = 0;
		?>
		<?php while ( $timestamp <= $end_timestamp ) : ?>
			<?php
			$hour_html      = gmdate( 'H:i', $timestamp );
			$next_timestamp = strtotime( $strtotime_time_step, $timestamp );
			$index ++;
			?>
			<div class="yith-wcbk-booking-calendar-day-row">
				<div class="yith-wcbk-booking-calendar-day-time"><?php echo esc_html( $hour_html ); ?>
					<?php
					$_step = '1h' === $time_step ? 'hour' : 'minute';
					if ( $_product && $_product->has_time() ) {
						echo wp_kses_post( yith_wcbk_get_calendar_product_availability_per_units_html( $_product, $timestamp, $next_timestamp - 1, $_step ) );
					}
					?>
				</div>
				<div class="yith-wcbk-booking-calendar-day-container">
					<div class="bookings">
						<?php
						$bookings = yith_wcbk_booking_helper()->get_bookings_in_time_range( $timestamp, $next_timestamp - 1, array( 'hour', 'minute' ), $show_externals, $product_id );
						include 'html-booking-calendar-booking-list.php';
						?>
						<?php if ( current_user_can( 'yith_create_booking' ) ) : ?>
							<div class="yith-wcbk-booking-calendar-create-button yith-wcbk-create-booking"
									data-create-start-date="<?php echo esc_attr( gmdate( 'Y-m-d', $timestamp ) ); ?>"
									data-create-start-time="<?php echo esc_attr( gmdate( 'H:i', $timestamp ) ); ?>"
							>
								<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"></path>
								</svg>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php $timestamp = $next_timestamp; ?>
		<?php endwhile; ?>
	</div>
	<?php $this->print_status_legend(); ?>
</div>
