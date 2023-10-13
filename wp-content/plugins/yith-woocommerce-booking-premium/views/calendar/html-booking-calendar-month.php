<?php
/**
 * Month Calendar page html
 *
 * @var YITH_WCBK_Booking_Calendar $this
 * @var array                      $args
 * @var string                     $view
 * @var string                     $month
 * @var string                     $year
 * @var int                        $start_timestamp
 * @var int                        $end_timestamp
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$date_helper    = yith_wcbk_date_helper();
$show_externals = yith_wcbk()->settings->show_externals_in_calendar();
$product_id     = ! empty( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$_product       = ! ! $product_id ? wc_get_product( $product_id ) : false;
/**
 * Booking product or false
 *
 * @var WC_Product_Booking|false $_product
 */
if ( $_product && ! $_product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
	$_product = false;
}

$current_day = strtotime( 'midnight' );
$timestamp   = $start_timestamp;
?>

<div id="yith-wcbk-booking-calendar-wrap">
	<?php $this->print_action_bar( $args ); ?>
	<div class="yith-wcbk-booking-calendar yith-wcbk-booking-calendar--month-view">
		<?php for ( $i = 0; $i < 7; $i ++ ) : ?>
			<?php
			$day_timestamp = strtotime( '+' . $i . ' day', $start_timestamp );
			$day_name      = date_i18n( 'D', $day_timestamp );
			?>
			<div class="yith-wcbk-booking-calendar-week-day">
				<?php echo esc_html( $day_name ); ?>
			</div>
		<?php endfor; ?>
		<?php while ( $timestamp <= $end_timestamp ) : ?>
			<?php
			$day_class      = absint( gmdate( 'n', $timestamp ) ) !== absint( $month ) ? '' : 'current-month';
			$day_number     = gmdate( 'd', $timestamp );
			$day_name       = date_i18n( 'D', $timestamp );
			$single_day_url = add_query_arg(
				array(
					'view' => 'day',
					'date' => gmdate( 'Y-m-d', $timestamp ),
				)
			);

			$day_class .= $timestamp === $current_day ? ' today' : '';

			$timestamp_tomorrow = strtotime( '+1 day', $timestamp );
			?>
			<div class="yith-wcbk-booking-calendar-day-container">
				<div class="yith-wcbk-booking-calendar-day">
					<a class="yith-wcbk-booking-calendar-day__number" href="<?php echo esc_url( $single_day_url ); ?>"><?php echo esc_html( $day_number ); ?></a>
					<?php
					if ( $_product && 'day' === $_product->get_duration_unit() ) {
						echo wp_kses_post( yith_wcbk_get_calendar_product_availability_per_units_html( $_product, $timestamp, $timestamp_tomorrow - 1 - 1, 'day' ) );
					}
					?>
				</div>
				<div class="bookings">
					<?php
					$bookings = yith_wcbk_booking_helper()->get_bookings_in_time_range( $timestamp, $timestamp_tomorrow - 1, 'all', $show_externals, $product_id );
					include 'html-booking-calendar-booking-list.php';
					?>
				</div>
				<?php if ( current_user_can( 'yith_create_booking' ) ) : ?>
					<div class="yith-wcbk-booking-calendar-create-button yith-wcbk-create-booking" data-create-start-date="<?php echo esc_attr( gmdate( 'Y-m-d', $timestamp ) ); ?>">
						<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"></path>
						</svg>
					</div>
				<?php endif; ?>
			</div>
			<?php
			$timestamp = strtotime( '+1 day', $timestamp );
			?>

		<?php endwhile; ?>
	</div>
	<?php $this->print_status_legend(); ?>
</div>
