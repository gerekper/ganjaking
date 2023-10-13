<?php
/**
 * Class YITH_WCBK_AJAX
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_AJAX' ) ) {
	/**
	 * Class YITH_WCBK_AJAX
	 */
	class YITH_WCBK_AJAX {
		use YITH_WCBK_Singleton_Trait;

		const ADMIN_AJAX_ACTION    = 'yith_wcbk_admin_ajax_action';
		const FRONTEND_AJAX_ACTION = 'yith_wcbk_frontend_ajax_action';

		/**
		 * Is testing?
		 *
		 * @var bool
		 */
		public $testing = false;

		/**
		 * YITH_WCBK_AJAX constructor.
		 */
		protected function __construct() {
			$ajax_actions = array(
				'json_search_order',
				'json_search_booking_products',
				'get_product_booking_form',
				'add_booking_note',
				'delete_booking_note',
			);

			foreach ( $ajax_actions as $ajax_action ) {
				add_action( 'wp_ajax_yith_wcbk_' . $ajax_action, array( $this, $ajax_action ) );
				add_action( 'wp_ajax_nopriv_yith_wcbk_' . $ajax_action, array( $this, $ajax_action ) );
			}

			add_action( 'wp_ajax_' . self::ADMIN_AJAX_ACTION, array( $this, 'handle_admin_ajax_action' ) );
			add_action( 'wp_ajax_' . self::FRONTEND_AJAX_ACTION, array( $this, 'handle_frontend_ajax_action' ) );
			add_action( 'wp_ajax_nopriv_' . self::FRONTEND_AJAX_ACTION, array( $this, 'handle_frontend_ajax_action' ) );
		}

		/**
		 * Start Booking AJAX call
		 *
		 * @param string $context The context (admin or frontend).
		 */
		private function ajax_start( string $context = 'admin' ) {
			yith_wcbk_ajax_start( $context );
		}

		/**
		 * Add booking note via ajax.
		 */
		public function add_booking_note() {
			$this->ajax_start();

			check_ajax_referer( 'add-booking-note', 'security' );

			if ( ! current_user_can( 'edit_' . YITH_WCBK_Post_Types::BOOKING . 's' ) ) {
				wp_die( - 1 );
			}

			$post_id      = absint( $_POST['post_id'] ?? 0 );
			$note         = apply_filters( 'yith_wcbk_booking_note', sanitize_textarea_field( wp_unslash( $_POST['note'] ?? '' ) ) );
			$note_type    = sanitize_text_field( wp_unslash( $_POST['note_type'] ?? 'admin' ) );
			$booking      = yith_get_booking( $post_id );
			$note_classes = 'note ' . $note_type;

			if ( $booking && $note ) {
				$note_id = $booking->add_note( $note_type, $note );

				echo '<li rel="' . esc_attr( $note_id ) . '" class="' . esc_attr( $note_classes ) . '">';
				echo '<div class="note_content">';
				echo wp_kses_post( wpautop( wptexturize( $note ) ) );
				echo '</div><p class="meta"><a href="#" class="delete-booking-note">' . esc_html__( 'Delete note', 'yith-booking-for-woocommerce' ) . '</a></p>';
				echo '</li>';
			}
			wp_die();
		}

		/**
		 * Delete booking note via ajax.
		 */
		public function delete_booking_note() {
			$this->ajax_start();

			check_ajax_referer( 'delete-booking-note', 'security' );

			if ( ! current_user_can( 'edit_' . YITH_WCBK_Post_Types::BOOKING . 's' ) ) {
				wp_die( - 1 );
			}

			$note_id = absint( $_POST['note_id'] ?? 0 );

			if ( $note_id ) {
				yith_wcbk_delete_booking_note( $note_id );
			}
			wp_die();
		}

		/**
		 * Order Search
		 */
		public function json_search_order() {
			$this->ajax_start();

			global $wpdb;
			ob_start();

			check_ajax_referer( 'search-orders', 'security' );

			$term = wc_clean( wp_unslash( $_GET['term'] ?? '' ) );

			if ( empty( $term ) ) {
				die();
			}

			$json_orders = array();
			$orders      = array();
			$term        = apply_filters( 'yith_wcbk_json_search_order_term', $term );
			$limit       = absint( apply_filters( 'yith_wcbk_json_search_order_limit', 10 ) );

			if ( yith_wcbk_is_wc_custom_orders_table_usage_enabled() ) {
				$orders = wc_get_orders(
					array(
						's'     => $term,
						'limit' => $limit,
					)
				);
			} else {
				// todo: HPOS - remove when removing support for older WC versions.
				$order_ids = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT ID FROM {$wpdb->posts} AS posts WHERE posts.post_type = 'shop_order' AND posts.ID LIKE %s",
						'%' . $wpdb->esc_like( $term ) . '%'
					)
				);

				if ( $order_ids ) {
					$orders = wc_get_orders(
						array(
							'post__in' => $order_ids,
							'limit'    => $limit,
						)
					);
				}
			}

			if ( $orders ) {
				$date_format = sprintf( '%s %s', wc_date_format(), wc_time_format() );
				foreach ( $orders as $order ) {

					$buyer = '';
					if ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
						$buyer = trim( sprintf( '%s %s', $order->get_billing_first_name(), $order->get_billing_last_name() ) );
					} elseif ( $order->get_billing_company() ) {
						$buyer = trim( $order->get_billing_company() );
					} elseif ( $order->get_customer_id() ) {
						$user  = get_user_by( 'id', $order->get_customer_id() );
						$buyer = ucwords( $user->display_name );
					}

					$order_number                    = apply_filters( 'yith_wcbk_order_number', '#' . $order->get_id(), $order->get_id() );
					$json_orders[ $order->get_id() ] = sprintf(
						'%s %s - <small>%s</small>',
						$order_number,
						esc_html( $buyer ),
						esc_html( $order->get_date_created()->format( $date_format ) )
					);
				}
			}

			return $this->send_json( $json_orders );
		}

		/**
		 * Booking Products Search
		 */
		public function json_search_booking_products() {
			$this->ajax_start();

			ob_start();
			check_ajax_referer( 'search-bookings', 'security' );

			$term    = wc_clean( wp_unslash( $_REQUEST['term']['term'] ?? $_REQUEST['term'] ?? '' ) );
			$exclude = array();

			if ( empty( $term ) ) {
				die();
			}

			if ( ! empty( $_REQUEST['exclude'] ) ) {
				$exclude = array_map( 'intval', explode( ',', wc_clean( wp_unslash( $_REQUEST['exclude'] ) ) ) );
			}

			$found_products = array();
			$booking_term   = get_term_by( 'slug', 'booking', 'product_type' );
			if ( $booking_term ) {
				$posts_in = array_unique( (array) get_objects_in_term( $booking_term->term_id, 'product_type' ) );
				if ( count( $posts_in ) > 0 ) {
					$args = array(
						'post_type'        => 'product',
						'post_status'      => 'publish',
						'numberposts'      => - 1,
						'orderby'          => 'title',
						'order'            => 'asc',
						'post_parent'      => 0,
						'suppress_filters' => 0,
						'include'          => $posts_in,
						's'                => $term,
						'fields'           => 'ids',
						'exclude'          => $exclude,
					);

					$args  = apply_filters( 'yith_wcbk_json_search_booking_products_args', $args );
					$posts = get_posts( $args );

					if ( ! empty( $posts ) ) {
						foreach ( $posts as $post ) {
							$product = wc_get_product( $post );

							if ( ! current_user_can( 'read_product', $post ) ) {
								continue;
							}

							$found_products[ $post ] = rawurldecode( $product->get_formatted_name() );
						}
					}
				}
			}

			$found_products = apply_filters( 'yith_wcbk_json_search_found_booking_products', $found_products );

			return $this->send_json( $found_products );
		}

		/**
		 * Get the product booking form
		 */
		public function get_product_booking_form() {
			$this->ajax_start();

			check_ajax_referer( 'yith-wcbk-get-booking-form', 'security' );

			if ( isset( $_POST['product_id'] ) ) {
				$product = wc_get_product( absint( $_POST['product_id'] ) );
				$args    = array(
					'show_price'      => (bool) $_POST['show_price'] ?? true,
					'additional_data' => wc_clean( wp_unslash( $_POST['additional_data'] ?? array() ) ),
				);
				do_action( 'yith_wcbk_booking_form', $product, $args );
			}
			die();
		}

		/**
		 * Define Search Form Results const
		 *
		 * @deprecated 4.0.0
		 */
		public function set_in_search_form_const() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_AJAX::set_in_search_form_const', '4.0.0' );
			if ( ! defined( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS' ) ) {
				define( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS', true );
			}
		}

		/**
		 * Get booking data as Availability and price.
		 * Note: this is 'public' to be tested through  PHPUnit tests.
		 *
		 * @param array|false $request The request.
		 *
		 * @return array
		 */
		public function frontend_ajax_get_booking_data( $request = false ) {
			$this->ajax_start( 'frontend' );

			$booking_data = false;
			$request      = ! ! $request && is_array( $request ) ? $request : wc_clean( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$request      = apply_filters( 'yith_wcbk_ajax_booking_data_request', $request );

			// The minimum number of persons is 1, also in case of bookings without people (to allow correct price calculation).
			$request['persons'] = max( 1, $request['persons'] ?? 1 );

			if ( empty( $request['product_id'] ) || empty( $request['from'] ) || ( empty( $request['duration'] ) && empty( $request['to'] ) ) ) {
				$booking_data = array( 'error' => _x( 'Required POST variable not set', 'Error', 'yith-booking-for-woocommerce' ) );
			} else {
				$date_helper = yith_wcbk_date_helper();
				$product_id  = absint( $request['product_id'] );
				/**
				 * The booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product = wc_get_product( $product_id );

				if ( $product ) {
					$request_booking_data = YITH_WCBK_Cart::get_booking_data_from_request( $request );
					$is_available_args    = YITH_WCBK_Cart::get_availability_args_from_booking_data( $request_booking_data );
					$is_available_args    = apply_filters( 'yith_wcbk_product_form_get_booking_data_available_args', $is_available_args, $product, $request );

					$is_available_args['return'] = 'array';

					$availability = $product->is_available( $is_available_args );
					$is_available = $availability['available'];

					$bookable_args = array(
						'product'               => $product,
						'bookable'              => $is_available,
						'from'                  => $request_booking_data['from'],
						'to'                    => $request_booking_data['to'],
						'non_available_reasons' => $availability['non_available_reasons'],
					);
					ob_start();
					wc_get_template( 'single-product/add-to-cart/bookable.php', $bookable_args, '', YITH_WCBK_TEMPLATE_PATH );
					$message = ob_get_clean();

					$show_totals = $is_available && yith_wcbk()->settings->show_totals();
					$totals      = $product->calculate_totals( $request_booking_data, $show_totals );
					$price       = $product->calculate_price_from_totals( $totals );
					$price       = apply_filters( 'yith_wcbk_booking_product_calculated_price', $price, $request, $product );
					$price_html  = $product->get_calculated_price_html( $price );

					if ( $is_available ) {
						/**
						 * Use this filter to show the totals.
						 *
						 * @hooked YITH_WCBK_Frontend_Premium::show_booking_form_totals_html
						 */
						$totals_html = apply_filters( 'yith_wcbk_booking_form_totals_html', '', $totals, $price_html, $product, $request );
					} else {
						$totals_html = '';
						$price_html  = apply_filters( 'yith_wcbk_product_form_not_bookable_price_html', $price_html, $request, $bookable_args );
					}

					$booking_data = array(
						'is_available' => $is_available,
						'totals'       => $totals,
						'totals_html'  => $totals_html,
						'price'        => $price_html,
						'raw_price'    => $price,
						'message'      => $message,
					);

					$booking_data = apply_filters( 'yith_wcbk_product_form_get_booking_data', $booking_data, $product, $bookable_args, $request );
				}
			}

			if ( ! $booking_data ) {
				return $this->send_json_error( array( 'error' => _x( 'Product not found', 'Error', 'yith-booking-for-woocommerce' ) ) );
			}

			return $this->send_json_success( $booking_data );
		}


		/**
		 * Get booking available times
		 *
		 * @since 4.0.0
		 */
		protected function frontend_ajax_get_booking_available_times() {
			$data    = false;
			$request = wc_clean( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$request = apply_filters( 'yith_wcbk_ajax_booking_available_times_request', $request );

			if ( empty( $request['product_id'] ) || empty( $request['from_date'] ) || empty( $request['duration'] ) ) {
				$data = array( 'error' => _x( 'Required POST variable not set', 'Error', 'yith-booking-for-woocommerce' ) );
			} else {
				$availability_args = apply_filters( 'yith_wcbk_ajax_booking_available_times_availability_args', array(), $request );
				$product_id        = $request['product_id'];
				$product           = yith_wcbk_get_booking_product( $product_id );

				if ( $product ) {
					$time_data      = $product->create_availability_time_array( $request['from_date'], $request['duration'], $availability_args );
					$time_data_html = '<option value="">' . __( 'Select Time', 'yith-booking-for-woocommerce' ) . '</option>';

					$default_start_time = $product->get_default_start_time();
					$first              = true;

					foreach ( $time_data as $time ) {
						$formatted_time = date_i18n( yith_wcbk()->settings->get_time_picker_format(), strtotime( $time ) );
						$formatted_time = apply_filters( 'yith_wcbk_ajax_booking_available_times_formatted_time', $formatted_time, $time, $product );
						$selected       = 'first-available' === $default_start_time ? selected( $first, true, false ) : '';
						$first          = false;

						$time_data_html .= "<option value='$time' $selected>$formatted_time</option>";
					}

					$data = array(
						'time_data'      => $time_data,
						'time_data_html' => $time_data_html,
					);

					if ( ! $time_data ) {
						$data['time_data_html'] = '<option value="">' . __( 'No time available', 'yith-booking-for-woocommerce' ) . '</option>';
					}
				}
			}

			if ( false === $data ) {
				wp_send_json_error( array( 'error' => _x( 'Product not found', 'Error', 'yith-booking-for-woocommerce' ) ) );
			}

			wp_send_json_error( $data );
		}

		/**
		 * Get non-available dates for product.
		 *
		 * @since 4.0.0
		 */
		protected function frontend_ajax_get_product_non_available_dates() {
			$data    = false;
			$request = wc_clean( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

			if ( empty( $request['product_id'] ) ) {
				wp_send_json_error( array( 'error' => _x( 'Required POST variable not set', 'Error', 'yith-booking-for-woocommerce' ) ) );
			} else {
				$product_id = $request['product_id'];
				$product    = yith_wcbk_get_booking_product( $product_id );

				if ( $product ) {
					$date_info_args = array();
					if ( ! empty( $request['month'] ) && ! empty( $request['year'] ) ) {
						$month                   = $request['month'];
						$year                    = $request['year'];
						$date_info_args['start'] = "$year-$month-01";
					}

					if ( ! empty( $request['months_to_load'] ) ) {
						$date_info_args['months_to_load'] = absint( $request['months_to_load'] );
					}

					$date_info                          = yith_wcbk_get_booking_form_date_info( $product, $date_info_args );
					$check_min_max_duration_in_calendar = yith_wcbk()->settings->check_min_max_duration_in_calendar();

					$availability_args = array(
						'range'                  => 'day',
						'exclude_booked'         => false,
						'check_start_date'       => false,
						'check_min_max_duration' => $check_min_max_duration_in_calendar,
					);
					$availability_args = apply_filters( 'yith_wcbk_ajax_booking_non_available_dates_availability_args', $availability_args, $request );

					$data = array(
						'not_available_dates' => $product->get_non_available_dates( $date_info['current_year'], $date_info['current_month'], $date_info['next_year'], $date_info['next_month'], $availability_args ),
						'year_to_load'        => $date_info['next_year'],
						'month_to_load'       => $date_info['next_month'],
						'loaded_months'       => $date_info['loaded_months'],
					);
				}
			}

			if ( false === $data ) {
				wp_send_json_error( array( 'error' => _x( 'Product not found', 'Error', 'yith-booking-for-woocommerce' ) ) );
			}

			wp_send_json_success( $data );
		}


		/**
		 * Send JSON or return if testing.
		 *
		 * @param array $data The data.
		 *
		 * @return array|bool
		 */
		public function send_json( $data ) {
			if ( $this->testing ) {
				return $data;
			} else {
				wp_send_json( $data );

				return false;
			}
		}

		/**
		 * Send JSON or return if testing.
		 *
		 * @param array $data The data.
		 *
		 * @return array|bool
		 */
		public function send_json_error( $data ) {
			if ( $this->testing ) {
				return array(
					'success' => false,
					'data'    => $data,
				);
			} else {
				wp_send_json_error( $data );

				return false;
			}
		}

		/**
		 * Send JSON or return if testing.
		 *
		 * @param array $data The data.
		 *
		 * @return array|bool
		 */
		public function send_json_success( $data ) {
			if ( $this->testing ) {
				return array(
					'success' => true,
					'data'    => $data,
				);
			} else {
				wp_send_json_success( $data );

				return false;
			}
		}

		/**
		 * Handle generic admin Ajax action.
		 */
		public function handle_admin_ajax_action() {
			check_ajax_referer( self::ADMIN_AJAX_ACTION, 'security' );

			$request = sanitize_title( wp_unslash( $_REQUEST['request'] ?? '' ) );
			if ( ! ! $request ) {
				$method = 'admin_ajax_' . $request;

				if ( is_callable( array( $this, $method ) ) ) {
					$result = $this->$method();
					wp_send_json_success( $result );
				}

				do_action( 'yith_wcbk_admin_ajax_' . $request );
			}

			wp_send_json_error();
		}

		/**
		 * Handle generic frontend Ajax action.
		 */
		public function handle_frontend_ajax_action() {
			// Frontend actions don't require nonce check.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$request = sanitize_title( wp_unslash( $_REQUEST['request'] ?? '' ) );
			if ( ! ! $request ) {
				$method = 'frontend_ajax_' . $request;

				if ( is_callable( array( $this, $method ) ) ) {
					$result = $this->$method();
					wp_send_json_success( $result );
				}

				do_action( 'yith_wcbk_frontend_ajax_' . $request );
			}

			wp_send_json_error();
		}
	}
}
