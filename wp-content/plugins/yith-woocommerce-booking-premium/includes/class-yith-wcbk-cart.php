<?php
/**
 * Class YITH_WCBK_Cart
 * handle add-to-cart processes for Booking products
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Cart' ) ) {
	/**
	 * Class YITH_WCBK_Cart
	 */
	class YITH_WCBK_Cart {
		use YITH_WCBK_Extensible_Singleton_Trait;

		/**
		 * YITH_WCBK_Cart constructor.
		 */
		protected function __construct() {
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'woocommerce_add_cart_item_data' ), 10, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'woocommerce_get_cart_item_from_session' ), 99, 3 );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'woocommerce_add_cart_item' ), 10, 2 );

			add_filter( 'woocommerce_cart_item_class', array( $this, 'add_cart_item_class_to_booking_products' ), 10, 3 );

			add_filter( 'woocommerce_get_item_data', array( $this, 'woocommerce_get_item_data' ), 10, 2 );

			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'prevent_add_to_cart_if_request_confirm' ), 10, 3 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 10, 6 );

			add_action( 'woocommerce_check_cart_items', array( $this, 'check_booking_availability' ) );
			add_action( 'woocommerce_before_checkout_process', array( $this, 'check_booking_availability_before_checkout' ) );
		}

		/**
		 * Get the default booking data
		 *
		 * @return array
		 * @since 2.0.8
		 */
		public static function get_default_booking_data() {
			return array(
				'add-to-cart'                => 0,
				'from'                       => 'now',
				'to'                         => '',
				'duration'                   => 1,
				'persons'                    => 1,
				'person_types'               => array(),
				'booking_services'           => array(),
				'booking_service_quantities' => array(),
				'resource_ids'               => array(),
			);
		}

		/**
		 * Get booking data from Request Form
		 *
		 * @param array $request The Request.
		 *
		 * @return array
		 */
		public static function get_booking_data_from_request( $request = array() ) {
			$request     = empty( $request ) ? $_REQUEST : $request; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$request     = apply_filters( 'yith_wcbk_cart_booking_data_request', $request );
			$date_helper = yith_wcbk_date_helper();

			$booking_fields = self::get_default_booking_data();
			$booking_data   = array();
			foreach ( $booking_fields as $field => $default ) {
				$booking_data[ $field ] = ! empty( $request[ $field ] ) ? $request[ $field ] : $default;
			}

			$product_id = absint( $booking_data['add-to-cart'] );
			if ( ! $product_id && isset( $request['product_id'] ) ) {
				$product_id = absint( $request['product_id'] );
			}

			$product = yith_wcbk_get_booking_product( $product_id );
			if ( ! $product ) {
				return array();
			}

			if ( ! is_numeric( $booking_data['from'] ) ) {
				$booking_data['from'] = strtotime( $booking_data['from'] );
			}

			if ( empty( $request['to'] ) ) {
				$from                     = $booking_data['from'];
				$duration                 = absint( $booking_data['duration'] ) * $product->get_duration();
				$booking_data['to']       = $date_helper->get_time_sum( $from, $duration, $product->get_duration_unit() );
				$booking_data['duration'] = $duration;
				if ( $product->is_full_day() ) {
					$booking_data['to'] -= 1;
				}
			} else {
				if ( ! is_numeric( $booking_data['to'] ) ) {
					$booking_data['to'] = strtotime( $booking_data['to'] );
				}

				if ( $product->is_full_day() ) {
					$booking_data['to'] = $date_helper->get_time_sum( $booking_data['to'], 1, 'day' );
				}

				$booking_data['duration'] = $date_helper->get_time_diff( $booking_data['from'], $booking_data['to'], $product->get_duration_unit() );

				if ( $product->is_full_day() ) {
					$booking_data['to'] -= 1;
				}
			}

			unset( $booking_data['add-to-cart'] );

			return apply_filters( 'yith_wcbk_cart_get_booking_data_from_request', $booking_data, $request, $product );
		}

		/**
		 * Get booking props from booking data.
		 *
		 * @param array $booking_data The booking data.
		 *
		 * @return array
		 */
		public static function get_booking_props_from_booking_data( array $booking_data = array() ): array {
			$props = $booking_data;

			return apply_filters( 'yith_wcbk_cart_get_booking_props_from_booking_data', $props, $booking_data );
		}

		/**
		 * Get booking data from booking
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		public static function get_booking_data_from_booking( YITH_WCBK_Booking $booking ): array {
			$booking_data = array(
				'from'                     => $booking->get_from(),
				'to'                       => $booking->get_to(),
				'duration'                 => $booking->get_duration(),
				'_booking_id'              => $booking->get_id(),
				'_added-to-cart-timestamp' => strtotime( 'now' ),
			);

			return apply_filters( 'yith_wcbk_cart_get_booking_data_from_booking', $booking_data, $booking );
		}

		/**
		 * Get booking props from booking data.
		 *
		 * @param array $booking_data The booking data.
		 *
		 * @return array
		 */
		public static function get_availability_args_from_booking_data( array $booking_data = array() ): array {
			$args    = array();
			$to_keep = array( 'from', 'to', 'duration' );

			foreach ( $to_keep as $param ) {
				if ( isset( $booking_data[ $param ] ) ) {
					$args[ $param ] = $booking_data[ $param ];
				}
			}

			if ( isset( $booking_data['_booking_id'] ) ) {
				$booking_id = absint( $booking_data['_booking_id'] );
				if ( ! ! $booking_id ) {
					$args['exclude'] = $booking_id;
				}
			}

			return apply_filters( 'yith_wcbk_cart_get_availability_args_from_booking_data', $args, $booking_data );
		}

		/**
		 * Bookings that require admin confirmation cannot be added to the cart
		 *
		 * @param bool $passed_validation The validation.
		 * @param int  $product_id        The product ID.
		 * @param int  $quantity          The quantity.
		 *
		 * @return bool
		 */
		public function prevent_add_to_cart_if_request_confirm( $passed_validation, $product_id, $quantity ) {
			if ( yith_wcbk_is_booking_product( $product_id ) ) {
				/**
				 * The booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product = wc_get_product( $product_id );
				if ( ! $product || ( $product->is_confirmation_required() ) ) {
					return false;
				}
			}

			return $passed_validation;
		}

		/**
		 * Add to cart validation for Booking Products
		 *
		 * @param bool   $passed_validation The validation.
		 * @param int    $product_id        The product ID.
		 * @param int    $quantity          The quantity.
		 * @param string $variation_id      The variation ID.
		 * @param array  $variations        The variations.
		 * @param array  $cart_item_data    The cart item data.
		 *
		 * @return bool
		 */
		public function add_to_cart_validation( $passed_validation, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {
			if ( yith_wcbk_is_booking_product( $product_id ) ) {
				$product_id = apply_filters( 'yith_wcbk_booking_product_id_to_translate', $product_id );

				/**
				 * The booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product = wc_get_product( $product_id );

				if ( $product ) {
					// Get the request from cart_item_data; if it's not set, get it by $_REQUEST.
					$request      = ! empty( $cart_item_data['yith_booking_request'] ) ? $cart_item_data['yith_booking_request'] : $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$booking_data = self::get_booking_data_from_request( $request );
					$product_link = "<a href='{$product->get_permalink()}'>{$product->get_title()}</a>";
					if ( ! $booking_data ) {
						// translators: %s is the product link.
						wc_add_notice( sprintf( __( 'There was an error in the request, please try again: %s', 'yith-booking-for-woocommerce' ), $product_link ), 'error' );
						$passed_validation = false;
					} else {
						$availability_args = self::get_availability_args_from_booking_data( $booking_data );
						$persons           = max( 1, absint( $booking_data['persons'] ?? 1 ) );

						$availability_args['return'] = 'array';
						$availability                = $product->is_available( $availability_args );
						if ( ! $availability['available'] ) {
							if ( $availability['non_available_reasons'] ) {
								$non_available_reasons = implode( ', ', $availability['non_available_reasons'] );
								// translators: 1. the product name; 2. list of reasons why the product is not available.
								wc_add_notice( sprintf( __( '%1$s is not available: %2$s', 'yith-booking-for-woocommerce' ), $product->get_title(), $non_available_reasons ), 'error' );
							} else {
								// translators: %s is the product name.
								wc_add_notice( sprintf( __( '%s is not available', 'yith-booking-for-woocommerce' ), $product->get_title() ), 'error' );
							}
							$passed_validation = false;
						}

						if ( $passed_validation && $product->get_max_bookings_per_unit() ) {
							// Check if there are booking products already added to the cart in the same dates.
							$from                      = $booking_data['from'];
							$to                        = $booking_data['to'];
							$count_persons_as_bookings = $product->has_count_people_as_separate_bookings_enabled();
							$include_externals         = $product->has_external_calendars();
							$max_booking_per_block     = $product->get_max_bookings_per_unit();
							$unit                      = $product->get_duration_unit();

							$bookings_added_to_cart_in_same_dates = $this->count_added_to_cart_bookings_in_period( compact( 'product_id', 'from', 'to', 'count_persons_as_bookings' ) );
							$booked_bookings_in_same_dates        = yith_wcbk_booking_helper()->count_max_booked_bookings_per_unit_in_period( compact( 'product_id', 'from', 'to', 'unit', 'include_externals', 'count_persons_as_bookings' ) );
							$total_bookings                       = $bookings_added_to_cart_in_same_dates + $booked_bookings_in_same_dates;
							$booking_weight                       = ! ! $count_persons_as_bookings ? $persons : 1;

							if ( $total_bookings + $booking_weight > $max_booking_per_block ) {
								$remained      = $max_booking_per_block - $total_bookings;
								$remained_text = '';
								if ( ! ! $remained ) {
									if ( $product->has_people() && $count_persons_as_bookings ) {
										// translators: %s is the number of available people remained.
										$remained_text = sprintf( __( 'Too many people selected (%s remaining)', 'yith-booking-for-woocommerce' ), $remained );
									} else {
										// translators: %s is the number of available bookings.
										$remained_text = sprintf( __( '%s remaining', 'yith-booking-for-woocommerce' ), $remained );
									}
								}

								$message = sprintf(
								// translators: %s is the product name; 2. additional details about availability.
									__( 'You cannot add another &quot;%s&quot; to your cart in the dates you selected.', 'yith-booking-for-woocommerce' ),
									$product->get_title()
								);

								if ( $remained_text ) {
									$message .= ' ' . $remained_text . '.'; // Add full stop, since the "remained_text" need to not have the full stop, to allow unique translation with other occurrences in non-available reasons.
								}

								$notice = apply_filters(
									'yith_wcbk_no_add_to_cart_for_selected_data',
									sprintf(
										'<a href="%s" class="button wc-forward">%s</a> %s',
										wc_get_cart_url(),
										__( 'View cart', 'woocommerce' ),
										$message
									),
									wc_get_cart_url(),
									$product->get_title(),
									$remained_text
								);
								wc_add_notice( $notice, 'error' );
								$passed_validation = false;

								yith_wcbk_do_deprecated_action( 'yith_wcbk_add_to_cart_for_selected_data', array(), '3.0', 'yith_wcbk_after_add_to_cart_validation' );
							}
						}

						do_action( 'yith_wcbk_after_add_to_cart_validation', $product, $passed_validation, $booking_data, $cart_item_data );
					}

					$passed_validation = apply_filters( 'yith_wcbk_add_to_cart_validation', $passed_validation, $product, $booking_data );
				}
			}

			return $passed_validation;
		}

		/**
		 * Count Bookings added to cart in the same period passed by $args.
		 *
		 * @param array $args Arguments.
		 *
		 * @return int
		 * @since 1.0.7
		 */
		public function count_added_to_cart_bookings_in_period( $args = array() ) {
			$default_args = array(
				'product_id'                => 0,
				'from'                      => '',
				'to'                        => '',
				'count_persons_as_bookings' => false,
			);

			$args = wp_parse_args( $args, $default_args );

			$found_bookings = 0;

			if ( ! ! $args['product_id'] && ! ! $args['from'] && ! ! $args['to'] ) {
				$cart_contents = WC()->cart->cart_contents;
				if ( ! ! $cart_contents ) {
					foreach ( $cart_contents as $cart_item_key => $cart_item_data ) {
						if ( isset( $cart_item_data['product_id'] ) && absint( $cart_item_data['product_id'] ) === absint( $args['product_id'] ) ) {
							// Booking in cart with the same product_id.
							if ( isset( $cart_item_data['yith_booking_data']['from'] ) && isset( $cart_item_data['yith_booking_data']['to'] ) ) {
								if ( $cart_item_data['yith_booking_data']['from'] < $args['to'] && $cart_item_data['yith_booking_data']['to'] > $args['from'] ) {
									if ( $args['count_persons_as_bookings'] && ! empty( $cart_item_data['yith_booking_data']['persons'] ) ) {
										$found_bookings += max( 1, absint( $cart_item_data['yith_booking_data']['persons'] ) );
									} else {
										$found_bookings ++;
									}
								}
							}
						}
					}
				}
			}

			return $found_bookings;

		}

		/**
		 * Add Cart item data for booking products
		 *
		 * @param array $cart_item_data The cart item data.
		 * @param int   $product_id     The product ID.
		 *
		 * @return array
		 */
		public function woocommerce_add_cart_item_data( $cart_item_data, $product_id ) {
			$is_booking = yith_wcbk_is_booking_product( $product_id );
			if ( $is_booking && ! isset( $cart_item_data['yith_booking_data'] ) ) {

				// Get the request from cart_item_data; if it's not set, get it by $_REQUEST.
				$request      = ! empty( $cart_item_data['yith_booking_request'] ) ? $cart_item_data['yith_booking_request'] : $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$booking_data = self::get_booking_data_from_request( $request );

				if ( ! isset( $booking_data['_added-to-cart-timestamp'] ) ) {
					/**
					 * Add the timestamp to allow adding to cart more booking products with the same configuration.
					 *
					 * @since 1.0.10
					 */
					$booking_data['_added-to-cart-timestamp'] = time();
				}

				$cart_item_data['yith_booking_data'] = $booking_data;
			}

			return $cart_item_data;
		}

		/**
		 * Set correct price for Booking on add-to-cart item
		 *
		 * @param array  $cart_item_data The cart item data.
		 * @param string $cart_item_key  The cart item key.
		 *
		 * @return array
		 */
		public function woocommerce_add_cart_item( $cart_item_data, $cart_item_key ) {
			$product_id = $cart_item_data['product_id'] ?? 0;
			if ( yith_wcbk_is_booking_product( $product_id ) ) {
				/**
				 * The Booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product      = $cart_item_data['data'];
				$booking_data = $cart_item_data['yith_booking_data'];

				$price = $this->get_product_price( $product_id, $booking_data );

				$product->set_price( $price );
				$cart_item_data['data'] = $product;
			}

			return $cart_item_data;
		}

		/**
		 * Set invalid order awaiting payment in WC session
		 * when the customer add a product in cart, since when a new product is added to the cart
		 * the old value of order_awaiting_payment is invalid, because the customer is creating a new order
		 *
		 * @since      2.1.1
		 * @deprecated 3.0.0
		 */
		public function set_invalid_order_awaiting_payment_in_session() {
			$current_session_order_id = isset( WC()->session->order_awaiting_payment ) ? absint( WC()->session->order_awaiting_payment ) : 0;
			WC()->session->set( 'yith_wcbk_invalid_order_awaiting_payment', $current_session_order_id );
		}

		/**
		 * Set correct price for Booking.
		 *
		 * @param array  $session_data  The session data.
		 * @param array  $cart_item     The cart item.
		 * @param string $cart_item_key The cart item key.
		 *
		 * @return array
		 */
		public function woocommerce_get_cart_item_from_session( $session_data, $cart_item, $cart_item_key ) {
			$product_id = $cart_item['product_id'] ?? 0;
			if ( yith_wcbk_is_booking_product( $product_id ) ) {
				/**
				 * The Booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product      = $session_data['data'];
				$booking_data = $session_data['yith_booking_data'];

				$price = $this->get_product_price( $product_id, $booking_data );

				$product->set_price( $price );
				$session_data['data'] = $product;
			}

			return $session_data;
		}

		/**
		 * Check for availability in cart and return errors.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		protected function get_cart_errors_for_booking_availability(): array {
			$cart   = WC()->cart;
			$errors = array();

			foreach ( $cart->get_cart() as $cart_item_key => $values ) {
				/**
				 * The Booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product      = $values['data'];
				$booking_data = $values['yith_booking_data'] ?? false;

				if ( $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) && $booking_data ) {
					$availability_args                     = self::get_availability_args_from_booking_data( $booking_data );
					$availability_args['exclude_order_id'] = yith_wcbk_get_order_awaiting_payment();

					if ( ! $product->is_available( $availability_args ) ) {
						$cart->set_quantity( $cart_item_key, 0 );

						$product_link = sprintf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), esc_html( $product->get_name() ) );
						// translators: %s is the product name (with link).
						$errors[] = sprintf( __( '%s has been removed from your cart because it can no longer be booked.', 'yith-booking-for-woocommerce' ), $product_link );
					}
				}
			}

			return $errors;
		}

		/**
		 * Check the booking availability before checkout
		 * and remove no longer available booking products
		 *
		 * @throws Exception When validation fails.
		 * @since 2.0.1
		 */
		public function check_booking_availability_before_checkout() {
			$errors = $this->get_cart_errors_for_booking_availability();

			if ( ! ! $errors ) {
				throw new Exception( implode( '<br />', $errors ) );
			}
		}

		/**
		 * Check the booking availability in cart.
		 *
		 * @since 2.1.1
		 */
		public function check_booking_availability() {
			$errors = $this->get_cart_errors_for_booking_availability();

			foreach ( $errors as $error ) {
				wc_add_notice( $error, 'error' );
			}
		}

		/**
		 * Filter item data
		 *
		 * @param array $item_data The item data.
		 * @param array $cart_item The cart item.
		 *
		 * @return array
		 */
		public function woocommerce_get_item_data( $item_data, $cart_item ) {
			$product_id = $cart_item['product_id'] ?? 0;
			if ( yith_wcbk_is_booking_product( $product_id ) ) {
				/**
				 * The Booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product      = $cart_item['data'] ?? yith_wcbk_get_booking_product( $product_id );
				$booking_data = $cart_item['yith_booking_data'];
				$from         = $booking_data['from'];
				$to           = $booking_data['to'];
				$duration     = $booking_data['duration'];

				$booking_item_data = array(
					'yith_booking_from'     => array(
						'key'     => yith_wcbk_get_booking_meta_label( 'from' ),
						'value'   => $from,
						'display' => $product->has_time() ? yith_wcbk_datetime( $from ) : yith_wcbk_date( $from ),
					),
					'yith_booking_to'       => array(
						'key'     => yith_wcbk_get_booking_meta_label( 'to' ),
						'value'   => $to,
						'display' => $product->has_time() ? yith_wcbk_datetime( $to ) : yith_wcbk_date( $to ),
					),
					'yith_booking_duration' => array(
						'key'     => yith_wcbk_get_booking_meta_label( 'duration' ),
						'value'   => $duration,
						'display' => yith_wcbk_format_duration( $duration, $product->get_duration_unit() ),
					),
				);

				$booking_item_data = apply_filters( 'yith_wcbk_cart_booking_item_data_before_totals', $booking_item_data, $cart_item, $product );

				if ( 'yes' === get_option( 'yith-wcbk-show-totals-in-cart-and-checkout', 'no' ) ) {
					$totals = $product->calculate_totals( $booking_data );

					if ( $totals ) {
						$totals_html = implode(
							"\n",
							array_map(
								function ( $total ) use ( $product ) {
									$price   = $total['display'] ?? ( yith_wcbk_get_formatted_price_to_display( $product, $total['value'] ) );
									$classes = '';
									if ( $total['value'] < 0 ) {
										$classes = 'yith-wcbk-cart-item-data--discount';
									}

									return '<span class="' . esc_attr( $classes ) . '">' . $total['label'] . ': ' . $price . '</span>';
								},
								$totals
							)
						);

						$booking_item_data['yith_booking_totals'] = array(
							'key'   => __( 'Totals', 'yith-booking-for-woocommerce' ),
							'value' => $totals_html,
						);
					}
				}

				$item_data = array_merge( $item_data, $booking_item_data );
			}

			return $item_data;
		}

		/**
		 * Get product price depending on booking data.
		 *
		 * @param int   $product_id   The product ID.
		 * @param array $booking_data The booking data.
		 *
		 * @return bool|float|string
		 */
		public function get_product_price( $product_id, $booking_data ) {
			$price = false;
			if ( yith_wcbk_is_booking_product( $product_id ) ) {
				/**
				 * The booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product = wc_get_product( $product_id );
				$price   = $product->calculate_price( $booking_data );
			} else {
				$product = wc_get_product( $product_id );
				if ( $product ) {
					$price = $product->get_price();
				}
			}

			return $price;
		}

		/**
		 * Add specific class to booking product cart items.
		 *
		 * @param string $class_name The CSS class.
		 * @param array  $cart_item  The cart item.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function add_cart_item_class_to_booking_products( $class_name, $cart_item ) {
			if ( isset( $cart_item['yith_booking_data'] ) ) {
				$class_name .= ' cart-item--booking';
			}

			return $class_name;
		}
	}
}
