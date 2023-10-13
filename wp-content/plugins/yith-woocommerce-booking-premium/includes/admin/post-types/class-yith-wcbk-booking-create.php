<?php
/**
 * Class YITH_WCBK_Booking_Create
 * handle booking creating in backend
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Booking_Create' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Create
	 * handle booking creating in backend
	 */
	class YITH_WCBK_Booking_Create {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Screen id of the 'Create Booking' page
		 *
		 * @var string
		 * @deprecated 3.0.0
		 */
		public static $screen_id = 'yith_booking_page_create_booking';

		/**
		 * YITH_WCBK_Booking_Create constructor.
		 */
		protected function __construct() {
			add_action( 'admin_init', array( $this, 'redirect_to_create_page' ), 99 );
			add_action( 'admin_init', array( $this, 'handle_create_booking' ) );
			add_action( 'admin_init', array( $this, 'filter_availability_when_creating_booking_by_admin' ) );

			add_action( 'admin_footer', array( $this, 'print_create_booking_template' ) );
			add_action( 'yith_wcbk_booking_form_service_info_layout', array( $this, 'force_service_info_layout_to_tooltip' ) );
		}

		/**
		 * Are we creating a booking in the panel?
		 *
		 * @return bool
		 * @since 5.0.0
		 */
		private function is_admin_create_booking_request() {
			$is_get_form_request = isset( $_REQUEST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ), 'yith-wcbk-get-booking-form' );
			$is_form_request     = isset( $_REQUEST['bk_page_nonce'], $_REQUEST['bk_page'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['bk_page_nonce'] ) ), sanitize_text_field( wp_unslash( $_REQUEST['bk_page'] ) ) );

			return ( $is_get_form_request || $is_form_request ) && current_user_can( 'yith_create_booking' ) && isset( $_REQUEST['bk_page'], $_REQUEST['bk_context'] ) && 'create_booking' === $_REQUEST['bk_context']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		/**
		 * Are we creating a booking in the calendar?
		 *
		 * @return bool
		 * @since 5.0.0
		 */
		private function is_admin_create_booking_request_in_calendar() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $this->is_admin_create_booking_request() && isset( $_REQUEST['bk_page'] ) && 'panel/dashboard/bookings-calendar' === $_REQUEST['bk_page'];
		}

		/**
		 * Filter availability for admins to enable booking:
		 * - for past and future dates
		 * - and to exclude checks for minimum/maximum duration and for the allowed start days.
		 *
		 * @since 5.0.0
		 */
		public function filter_availability_when_creating_booking_by_admin() {
			if ( $this->is_admin_create_booking_request() ) {
				yith_wcbk_cache()->set_context( 'admin' );
				add_filter( 'yith_wcbk_get_booking_form_date_info_min_date', '__return_empty_string' );
				add_filter( 'yith_wcbk_get_booking_form_date_info_max_date', '__return_empty_string' );
				add_filter( 'yith_wcbk_booking_product_is_available_args', array( $this, 'filter_is_available_args_when_creating_booking_by_admin' ), 10, 1 );
			}
		}

		/**
		 * Filter availability args for admins.
		 *
		 * @param array $args Arguments.
		 *
		 * @return array
		 * @since 5.0.0
		 */
		public function filter_is_available_args_when_creating_booking_by_admin( $args ) {
			$args['check_non_available_in_past']       = false;
			$args['check_maximum_advance_reservation'] = false;
			$args['check_min_max_duration']            = false;
			$args['check_start_date']                  = false;

			return $args;
		}

		/**
		 * Handle Booking creation.
		 */
		public function handle_create_booking() {
			global $pagenow;

			if ( 'post-new.php' === $pagenow && isset( $_REQUEST['post_type'] ) && YITH_WCBK_Post_Types::BOOKING === $_REQUEST['post_type'] ) {
				wp_safe_redirect( admin_url( 'edit.php?post_type=' . YITH_WCBK_Post_Types::BOOKING ) );
				exit();
			}

			if ( isset( $_REQUEST['yith-wcbk-nonce'], $_REQUEST['create-booking'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['yith-wcbk-nonce'] ) ), 'create-booking' ) ) {
				if ( current_user_can( 'yith_create_booking' ) ) {
					$this->create_booking();
					exit();
				} else {
					wp_die( esc_html__( 'You don\'t have permissions to create bookings!', 'yith-booking-for-woocommerce' ) );
				}
			}
		}

		/**
		 * Handle create Booking.
		 */
		protected function create_booking() {

			if ( ! current_user_can( 'yith_create_booking' ) ) {
				wp_die( esc_html__( 'You don\'t have permissions to create bookings!', 'yith-booking-for-woocommerce' ) );
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$booking_data = YITH_WCBK_Cart::get_booking_data_from_request();
			$user_id      = isset( $_REQUEST['user_id'] ) ? absint( $_REQUEST['user_id'] ) : 0;
			$product_id   = isset( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : 0;
			$assign_order = isset( $_REQUEST['assign_order'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['assign_order'] ) ) : 'no';
			$order_id     = isset( $_REQUEST['order_id'] ) ? absint( $_REQUEST['order_id'] ) : 0;

			$redirect = false;
			if ( $this->is_admin_create_booking_request_in_calendar() ) {
				$redirect = wp_get_raw_referer();
			}

			$product_id = apply_filters( 'yith_wcbk_booking_product_id_to_translate', $product_id );

			/**
			 * The booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			$product = wc_get_product( $product_id );
			if ( $product && yith_wcbk_is_booking_product( $product ) ) {
				if ( in_array( $assign_order, array( 'specific', 'new' ), true ) ) {
					if ( 'new' === $assign_order || ! $order_id ) {
						$order_data = array(
							'status'      => apply_filters( 'woocommerce_default_order_status', 'pending' ),
							'customer_id' => $user_id,
							'created_via' => 'yith_booking',
						);

						$order = wc_create_order( $order_data );
					} else {
						$order = wc_get_order( $order_id );
					}

					if ( is_wp_error( $order ) ) {
						wp_die( esc_html__( 'Error: Unable to create order. Please try again.', 'yith-booking-for-woocommerce' ) );
					} elseif ( false === $order ) {
						wp_die( esc_html__( 'Error: Unable to create order. Please try again.', 'yith-booking-for-woocommerce' ) );
					}

					$order_id = $order->get_id();
					do_action( 'woocommerce_new_order', $order_id );

					$booking_cost = $product->calculate_price( $booking_data );
					if ( wc_prices_include_tax() ) {
						$booking_cost = wc_get_price_excluding_tax( $product, array( 'price' => $booking_cost ) );
					}

					$item_id = $order->add_product(
						$product,
						1,
						apply_filters(
							'yith_wcbk_create_booking_order_item_data',
							array(
								'variation' => '',
								'totals'    => array(
									'subtotal' => $booking_cost,
									'total'    => $booking_cost,
								),
							),
							$product
						)
					);

					if ( ! $item_id ) {
						wp_die( esc_html__( 'Error: Unable to create order item. Please try again.', 'yith-booking-for-woocommerce' ) );
					}

					$values = array( 'yith_booking_data' => $booking_data );

					/**
					 * The order Item.
					 *
					 * @var WC_Order_Item_Product $item
					 */
					$item = $order->get_item( $item_id );
					yith_wcbk()->orders->woocommerce_checkout_create_order_line_item( $item, '', $values, $order );
					$item->save_meta_data();

					// Allow plugins to add order item meta.
					do_action( 'woocommerce_new_order_item', $item->get_id(), $item, $item->get_order_id() );

					$order->calculate_totals();

					// Fire action to check if order has booking and create Bookings.
					do_action( 'yith_wcbk_check_order_with_booking', $order_id );

					wp_safe_redirect( ! ! $redirect ? $redirect : admin_url( 'post.php?post=' . $order_id . '&action=edit' ) );
					exit();

				} elseif ( 'no' === $assign_order ) {
					$booking = new YITH_WCBK_Booking();
					$booking->set_raw_title( $product->get_title() );
					$booking->set_user_id( $user_id );
					$booking->set_product_id( $product_id );
					$booking->set_status( 'unpaid' );

					$args_to_unset = array( 'raw_title', 'user_id', 'product_id', 'status' );
					foreach ( $args_to_unset as $arg ) {
						if ( isset( $booking_data[ $arg ] ) ) {
							unset( $booking_data[ $arg ] );
						}
					}

					$props     = array();
					$meta_data = array();
					$all_props = YITH_WCBK_Cart::get_booking_props_from_booking_data( $booking_data );
					foreach ( $all_props as $prop => $value ) {
						if ( $booking->is_internal_prop( $prop ) ) {
							$props[ $prop ] = $value;
						} else {
							if ( apply_filters( 'yith_wcbk_force_private_meta_data_on_booking_creation', true, $prop ) ) {
								$prop = '_' === substr( $prop, 0, 1 ) ? $prop : ( '_' . $prop );
							}
							$meta_data[ $prop ] = $value;
						}
					}
					$booking->set_props( $props );
					$booking->update_metas( $meta_data );
					$booking->save();

					if ( $booking->is_valid() ) {
						wp_safe_redirect( ! ! $redirect ? $redirect : admin_url( 'post.php?post=' . $booking->get_id() . '&action=edit' ) );
						exit();
					}
				}
			}
			wp_die( esc_html__( 'Error when creating booking', 'yith-booking-for-woocommerce' ) );

			// phpcs:enable
		}

		/**
		 * Redirect to custom page when go to add new booking
		 *
		 * @deprecated 3.0.0 | use YITH_WCBK_Booking_Create::handle_create_booking instead
		 */
		public function redirect_to_create_page() {
			$this->handle_create_booking();
		}

		/**
		 * Print the "create booking" template
		 *
		 * @since 3.0.0
		 */
		public function print_create_booking_template() {
			if ( yith_wcbk_is_admin_page( array( 'edit-' . YITH_WCBK_Post_Types::BOOKING, 'panel/dashboard/bookings-calendar' ) ) ) {
				yith_wcbk_print_create_booking_template();
			}
		}

		/**
		 * Force service info layout to tooltip when creating booking though backend.
		 *
		 * @param string $layout The layout.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function force_service_info_layout_to_tooltip( $layout ) {
			if (
				isset( $_REQUEST['bk_context'], $_REQUEST['security'] ) &&
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ), 'yith-wcbk-get-booking-form' ) &&
				'create_booking' === $_REQUEST['bk_context'] ) {
				$layout = 'tooltip';
			}

			return $layout;
		}

		/**
		 * Return true if this is Create Page
		 *
		 * @return bool
		 * @deprecated 3.0.0
		 */
		public static function is_create_page() {
			return false;
		}

		/**
		 * Print section
		 *
		 * @deprecated 3.0.0
		 */
		public function output() {
			// Do nothing.
		}
	}
}

return YITH_WCBK_Booking_Create::get_instance();
