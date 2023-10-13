<?php
/**
 * Class YITH_WCBK_Endpoints
 * handle Booking endpoints
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Endpoints' ) ) {
	/**
	 * Class YITH_WCBK_Enpoints
	 */
	class YITH_WCBK_Endpoints {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The endpoint.
		 *
		 * @var array
		 */
		public $endpoints = array();

		/**
		 * YITH_WCBK_Endpoints constructor.
		 */
		protected function __construct() {
			$this->init_vars();

			add_action( 'init', array( $this, 'add_endpoints' ) );
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

			add_filter( 'woocommerce_account_menu_items', array( $this, 'add_booking_endpoint_in_myaccount_menu' ) );
			add_filter( 'the_title', array( $this, 'get_endpoint_title' ), 10, 2 );

			foreach ( $this->endpoints as $key => $value ) {
				if ( ! empty( $value ) ) {
					add_action( 'woocommerce_account_' . $value . '_endpoint', array( $this, 'show_endpoint' ) );
				}
			}

			add_filter( 'woocommerce_account_settings', array( $this, 'add_booking_endpoint_settings' ) );
			add_action( 'woocommerce_update_options_account', array( $this, 'add_endpoints' ) );
		}

		/**
		 * Init class vars
		 */
		private function init_vars() {
			$this->endpoints = apply_filters(
				'yith_wcbk_booking_endpoints',
				array(
					'bookings'     => get_option( 'woocommerce_myaccount_bookings_endpoint', 'bookings' ),
					'view-booking' => get_option( 'woocommerce_myaccount_view_booking_endpoint', 'view-booking' ),
				)
			);
		}

		/**
		 * Add Booking Endpoint settings in WooCommerce endpoint settings
		 *
		 * @param array $settings Settings.
		 *
		 * @return array
		 */
		public function add_booking_endpoint_settings( $settings ) {
			$booking_endpoint_settings = array(
				array(
					'title' => __( 'Booking endpoints', 'yith-booking-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'yith_wcbk_endpoint_options',
				),
				array(
					'title'    => __( 'Bookings', 'yith-booking-for-woocommerce' ),
					'desc'     => __( 'Endpoint for the My Account &rarr; Bookings page', 'yith-booking-for-woocommerce' ),
					'id'       => 'woocommerce_myaccount_bookings_endpoint',
					'type'     => 'text',
					'default'  => 'bookings',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'View booking', 'yith-booking-for-woocommerce' ),
					'desc'     => __( 'Endpoint for the My Account &rarr; View Booking page', 'yith-booking-for-woocommerce' ),
					'id'       => 'woocommerce_myaccount_view_booking_endpoint',
					'type'     => 'text',
					'default'  => 'view-booking',
					'desc_tip' => true,
				),
				array(
					'type' => 'sectionend',
					'id'   => 'yith_wcbk_endpoint_options',
				),
			);

			return array_merge( $settings, $booking_endpoint_settings );
		}

		/**
		 * Add the new endpoints to WP
		 */
		public function add_endpoints() {
			$this->init_vars();
			foreach ( $this->endpoints as $key => $value ) {
				if ( ! empty( $value ) ) {
					add_rewrite_endpoint( $value, EP_ROOT | EP_PAGES );
				}
			}
		}

		/**
		 * Add new query var.
		 *
		 * @param array $vars Query vars.
		 *
		 * @return array
		 */
		public function add_query_vars( $vars ) {
			foreach ( $this->endpoints as $key => $value ) {
				if ( ! empty( $value ) ) {
					$vars[] = $value;
				}
			}

			return $vars;
		}

		/**
		 * Add booking in My Account Nav menu
		 *
		 * @param array $items Menu items.
		 *
		 * @return array
		 */
		public function add_booking_endpoint_in_myaccount_menu( $items ) {
			$a = array_slice( $items, 0, 1, true );
			$b = array_slice( $items, 1 );

			$wc_bookings_endpoint = apply_filters( 'yith_wcbk_endpoint_bookings', 'bookings' );

			$bookings_endpoint = $this->get_endpoint( $wc_bookings_endpoint );

			$endpoints_to_add = array(
				$bookings_endpoint => __( 'Bookings', 'yith-booking-for-woocommerce' ),
			);

			$items = array_merge( $a, $endpoints_to_add, $b );

			return $items;
		}

		/**
		 * Retrieve an endpoint
		 *
		 * @param string $key The key.
		 *
		 * @return string
		 */
		public function get_endpoint( $key ) {
			return ! empty( $this->endpoints[ $key ] ) ? $this->endpoints[ $key ] : $key;
		}

		/**
		 * Get the current endpoint
		 *
		 * @return bool|int|string
		 */
		public function get_current_endpoint() {
			global $wp;

			if ( apply_filters( 'yith_wcbk_is_current_endpoint', true ) && ( is_admin() || ! is_main_query() || ! in_the_loop() || ! is_account_page() ) ) {
				return false;
			}

			$current_endpoint = false;
			foreach ( $this->endpoints as $endpoint_id => $endpoint ) {
				if ( isset( $wp->query_vars[ $endpoint ] ) ) {
					$current_endpoint = $endpoint_id;
					break;
				}
			}

			return $current_endpoint;
		}

		/**
		 * Get the title of the current endpoint
		 *
		 * @param string $title The title.
		 * @param int    $id    The ID.
		 *
		 * @return string
		 */
		public function get_endpoint_title( $title, $id = 0 ) {
			$id = absint( $id );
			if ( wc_get_page_id( 'myaccount' ) !== $id ) {
				return $title;
			}
			global $wp;

			$endpoint          = $this->get_current_endpoint();
			$bookings_endpoint = apply_filters( 'yith_wcbk_endpoint_bookings', 'bookings' );

			switch ( $endpoint ) {
				case $bookings_endpoint:
					$endpoint = $this->get_endpoint( $bookings_endpoint );

					if ( ! empty( $wp->query_vars[ $endpoint ] ) ) {
						// translators: %s is the page number.
						$title = sprintf( __( 'Bookings (page %d)', 'yith-booking-for-woocommerce' ), intval( $wp->query_vars[ $endpoint ] ) );
					} else {
						$title = __( 'Bookings', 'yith-booking-for-woocommerce' );
					}
					break;
				case 'view-booking':
					$endpoint = $this->get_endpoint( 'view-booking' );
					$booking  = yith_get_booking( $wp->query_vars[ $endpoint ] );
					$title    = ( $booking ) ? $booking->get_name() : '';
					break;
			}

			return $title;
		}

		/**
		 * Plugin install action.
		 * Flush rewrite rules to make our custom endpoint available.
		 */
		public static function install() {
			flush_rewrite_rules();
		}

		/**
		 * Show the endpoint content
		 */
		public function show_endpoint() {
			global $wp;

			$endpoint = $this->get_current_endpoint();

			$bookings_endpoint = apply_filters( 'yith_wcbk_endpoint_bookings', 'bookings' );

			switch ( $endpoint ) {
				case $bookings_endpoint:
					$current_page = ! empty( $wp->query_vars[ $endpoint ] ) ? absint( $wp->query_vars[ $endpoint ] ) : 1;
					$per_page     = apply_filters( 'yith_wcbk_my_account_bookings_per_page', 10 );
					$user_id      = get_current_user_id();
					$query        = new WP_Query(
						array(
							'post_type'      => YITH_WCBK_Post_Types::BOOKING,
							'posts_per_page' => $per_page,
							'meta_key'       => '_user_id',
							'meta_value'     => $user_id,
							'paged'          => $current_page,
							'fields'         => 'ids',
						)
					);
					$bookings     = array_map( 'yith_get_booking', $query->posts );

					$args = array(
						'bookings'      => $bookings,
						'has_bookings'  => 0 < $query->found_posts,
						'current_page'  => $current_page,
						'total'         => $query->found_posts,
						'max_num_pages' => $query->max_num_pages,
					);
					wc_get_template( 'myaccount/bookings.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
					break;
				case 'view-booking':
					$endpoint   = $this->get_endpoint( 'view-booking' );
					$booking_id = $wp->query_vars[ $endpoint ];
					$booking    = yith_get_booking( $booking_id );

					if ( ! $booking || ! $booking->is_valid() || ! current_user_can( 'view_booking', $booking_id ) ) {
						echo '<div class="woocommerce-error">' . esc_html__( 'Invalid booking.', 'yith-booking-for-woocommerce' ) . ' <a href="' . esc_url( wc_get_account_endpoint_url( $this->get_endpoint( 'bookings' ) ) ) . '" class="wc-forward">' . esc_html__( 'View your bookings', 'yith-booking-for-woocommerce' ) . '</a></div>';

						return;
					}
					$args = array(
						'booking'    => $booking,
						'booking_id' => $booking_id,
					);
					wc_get_template( 'myaccount/view-booking.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
					break;
			}
		}
	}
}
