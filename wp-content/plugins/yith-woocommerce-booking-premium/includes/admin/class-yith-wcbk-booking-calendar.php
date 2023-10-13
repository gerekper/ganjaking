<?php
/**
 * Calendar class.
 * handle the booking calendar in admin.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Booking_Calendar' ) ) {

	/**
	 * Class YITH_WCBK_Booking_Calendar
	 *
	 * @author YITH <plugins@yithemes.com>
	 */
	class YITH_WCBK_Booking_Calendar {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBK_Booking_Calendar
		 */
		protected static $instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Booking_Calendar
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBK_Booking_Calendar constructor.
		 */
		protected function __construct() {
			add_action( 'yith_wcbk_panel_render_calendar_page', array( $this, 'render_calendar_page' ) );
		}

		/**
		 * Render Calendar page in base of requests
		 */
		public function render_calendar_page() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$view      = isset( $_REQUEST['view'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['view'] ) ) : 'month';
			$view_file = YITH_WCBK_VIEWS_PATH . 'calendar/html-booking-calendar-' . $view . '.php';

			switch ( $view ) {
				case 'day':
					$default_time_step  = yith_wcbk()->settings->get( 'calendar-day-default-time-step', '1h' );
					$default_start_time = yith_wcbk()->settings->get( 'calendar-day-default-start-time', '00:00' );

					$default_start_time_check = explode( ':', $default_start_time );
					if ( ! ( 2 === count( $default_start_time_check ) && $default_start_time_check[0] < 24 && $default_start_time_check[1] < 60 ) ) {
						$default_start_time = '';
					}

					$date       = isset( $_REQUEST['date'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['date'] ) ) : gmdate( 'Y-m-d' );
					$time_step  = isset( $_REQUEST['time_step'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['time_step'] ) ) : $default_time_step;
					$start_time = isset( $_REQUEST['start_time'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['start_time'] ) ) : $default_start_time;

					$args = array(
						'view'       => $view,
						'date'       => $date,
						'time_step'  => $time_step,
						'start_time' => $start_time,
					);

					break;

				default:
					$view = 'month';

					$default_month = isset( $_REQUEST['date'] ) ? gmdate( 'n', strtotime( sanitize_text_field( wp_unslash( $_REQUEST['date'] ) ) ) ) : gmdate( 'n' );
					$default_year  = isset( $_REQUEST['date'] ) ? gmdate( 'Y', strtotime( sanitize_text_field( wp_unslash( $_REQUEST['date'] ) ) ) ) : gmdate( 'Y' );

					$month = absint( $_REQUEST['month'] ?? $default_month );
					$year  = absint( $_REQUEST['year'] ?? $default_year );

					$start_of_week              = absint( get_option( 'start_of_week', 1 ) );
					$first_day_of_current_month = gmdate( 'N', strtotime( "$year-$month-01" ) );

					$diff = $start_of_week - $first_day_of_current_month;

					$start_timestamp = strtotime( $diff . ' days midnight', strtotime( "$year-$month-01" ) );
					$end_timestamp   = strtotime( '+34 days midnight', $start_timestamp );

					$last_day_of_month = strtotime( '+1 month -1 day', strtotime( "$year-$month-01" ) );
					if ( $end_timestamp < $last_day_of_month ) {
						$end_timestamp = strtotime( '+7 days', $end_timestamp );
					}

					$args = array(
						'view'            => $view,
						'month'           => $month,
						'year'            => $year,
						'start_timestamp' => $start_timestamp,
						'end_timestamp'   => $end_timestamp,
					);

					break;
			}

			$args['url_query_args'] = self::get_url_query_args();

			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			if ( file_exists( $view_file ) ) {
				include $view_file;
			}

			// phpcs:enable
		}

		/**
		 * Print the action bar.
		 *
		 * @param array $args Arguments.
		 */
		public function print_action_bar( $args ) {
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			$view_file = YITH_WCBK_VIEWS_PATH . 'calendar/html-booking-calendar-action-bar.php';

			if ( file_exists( $view_file ) ) {
				include $view_file;
			}
		}

		/**
		 * Print the status legend.
		 */
		public function print_status_legend() {
			yith_wcbk_get_view( 'calendar/html-booking-calendar-status-legend.php' );
		}

		/**
		 * Return an array of time steps.
		 *
		 * @return array
		 */
		public static function get_time_steps() {
			$time_steps = array(
				'1h'  => __( '1 hour', 'yith-booking-for-woocommerce' ),
				'30m' => __( '30 minutes', 'yith-booking-for-woocommerce' ),
				'15m' => __( '15 minutes', 'yith-booking-for-woocommerce' ),
			);

			return apply_filters( 'yith_wcbk_calendar_day_time_steps', $time_steps );
		}


		/**
		 * Get the calendar URL
		 *
		 * @param int|false $product_id The product ID. Set to false if you want to retrieve the general calendar URL.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public static function get_url( $product_id = false ) {
			$args = self::get_url_query_args();
			if ( $product_id ) {
				$args['product_id'] = $product_id;
			}

			return add_query_arg( $args, admin_url( 'admin.php' ) );
		}

		/**
		 * Get the query args used in the calendar URL.
		 *
		 * @return array
		 */
		public static function get_url_query_args() {
			return apply_filters(
				'yith_wcbk_calendar_url_query_args',
				array(
					'page'    => 'yith_wcbk_panel',
					'tab'     => 'dashboard',
					'sub_tab' => 'dashboard-bookings-calendar',
				)
			);
		}
	}
}
