<?php
/**
 * Class YITH_WCBK_Orders
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Orders' ) ) {
	/**
	 * Class YITH_WCBK_Orders
	 * handle order processes for Booking products
	 */
	class YITH_WCBK_Orders {
		use YITH_WCBK_Extensible_Singleton_Trait;

		/**
		 * Order item data prefix
		 *
		 * @var string
		 */
		public static $order_item_data_prefix = 'yith_booking_';

		/**
		 * Order bookings meta
		 *
		 * @var string
		 */
		public static $order_bookings_meta = 'yith_bookings';

		/**
		 * YITH_WCBK_Orders constructor.
		 */
		protected function __construct() {
			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'woocommerce_checkout_create_order_line_item' ), 10, 4 );

			add_action( 'woocommerce_checkout_order_processed', array( $this, 'check_order_for_booking' ), 999, 2 );
			add_action( 'woocommerce_store_api_checkout_order_processed', array( $this, 'check_order_for_booking' ), 999 );
			add_action( 'yith_wcbk_check_order_with_booking', array( $this, 'check_order_for_booking' ) );

			add_action( 'woocommerce_order_status_completed', array( $this, 'set_booking_as_paid' ), 10, 2 );
			add_action( 'woocommerce_order_status_processing', array( $this, 'set_booking_as_paid' ), 10, 2 );
			add_action( 'woocommerce_order_status_cancelled', array( $this, 'set_booking_as_cancelled' ), 10, 2 );

			add_action( 'woocommerce_resume_order', array( $this, 'cancel_bookings_before_resuming_order' ), 10, 1 );

			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'show_related_bookings' ) );

			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_meta' ) );

			add_action( 'wp_ajax_woocommerce_add_order_item', array( $this, 'prevent_adding_booking_products_in_orders' ), 5 );

			add_action( 'add_meta_boxes', array( $this, 'add_order_related_bookings_meta_box' ) );

			if ( 'yes' === get_option( 'yith-wcbk-show-booking-of-in-cart-and-checkout', 'no' ) ) {
				add_filter( 'woocommerce_order_item_name', array( $this, 'order_item_name' ), 10, 2 );
			}

			add_action( 'woocommerce_order_item_display_meta_key', array( $this, 'filter_order_item_display_meta_key' ), 10, 1 );
			add_action( 'woocommerce_order_item_display_meta_value', array( $this, 'filter_order_item_display_meta_value' ), 10, 3 );
		}

		/**
		 * Add the related bookings metabox in orders
		 *
		 * @param string $post_type The post type.
		 *
		 * @since 2.1.16
		 */
		public function add_order_related_bookings_meta_box( $post_type ) {
			// todo: HPOS - remove shop_order when removing support for older WC versions.
			if ( in_array( $post_type, array( wc_get_page_screen_id( 'shop-order' ), 'shop_order' ), true ) ) {
				add_meta_box(
					'yith-wcbk-order-related-bookings',
					__( 'Related Bookings', 'yith-booking-for-woocommerce' ),
					array( $this, 'print_order_related_bookings_meta_box' ),
					$post_type,
					'normal',
					'default'
				);
			}
		}

		/**
		 * Print the related bookings metabox in orders
		 *
		 * @param WP_Post $post The post.
		 *
		 * @since 2.1.16
		 */
		public function print_order_related_bookings_meta_box( $post ) {
			$order = wc_get_order( $post->ID );
			if ( $order ) {
				$bookings = yith_wcbk()->booking_helper->get_bookings_by_order( $order->get_id() );
				$bookings = apply_filters( 'yith_wcbk_order_bookings_related_to_order', $bookings, $order );
				if ( ! ! $bookings ) {
					yith_wcbk_get_view( '/metaboxes/html-order-related-bookings.php', compact( 'bookings', 'order' ) );
				}
			}
		}

		/**
		 * When resuming orders the old bookings related to the order will be cancelled
		 * since the cart items will be re-created  by WooCommerce
		 * so also the bookings will be re-created
		 *
		 * @param int $order_id Order ID.
		 *
		 * @since 2.1.2
		 */
		public function cancel_bookings_before_resuming_order( $order_id ) {
			$bookings = yith_wcbk_booking_helper()->get_bookings_by_order( $order_id );
			if ( ! ! $bookings ) {
				$order        = wc_get_order( $order_id );
				$order_number = $order ? $order->get_order_number() : $order_id;
				foreach ( $bookings as $booking ) {
					$booking = yith_get_booking( $booking );
					if ( $booking ) {
						$additional_note = sprintf(
						// translators: %s is the order ID (with link).
							__( 'Reason: order %s has been resumed (probably due to a failed payment).', 'yith-booking-for-woocommerce' ),
							sprintf(
								'<a href="%s">#%s</a>',
								esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ),
								absint( $order_number )
							)
						);
						$booking->update_status( 'cancelled', $additional_note );
					}
				}
			}
		}

		/**
		 * Don't allow adding booking to orders through "Add products" box in orders
		 *
		 * @since 2.0.7
		 */
		public function prevent_adding_booking_products_in_orders() {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( isset( $_POST['data'] ) ) {
				$items_to_add = array_filter( wc_clean( wp_unslash( (array) $_POST['data'] ) ) );

				$booking_titles = array();
				foreach ( $items_to_add as $item ) {
					if ( ! isset( $item['id'], $item['qty'] ) || empty( $item['id'] ) ) {
						continue;
					}
					$product_id = absint( $item['id'] );
					if ( yith_wcbk_is_booking_product( $product_id ) ) {
						$product = wc_get_product( $product_id );
						if ( $product ) {
							$booking_titles[] = $product->get_formatted_name();
						}
					}
				}

				if ( $booking_titles ) {
					wp_send_json_error(
						array(
							// translators: %s a comma-separated list of booking products.
							'error' => sprintf( __( 'You are trying to add the following Bookable Products to the order: %s. You cannot add Bookable products to orders through this box. To do it, please use the Create Booking page in Bookings menu', 'yith-booking-for-woocommerce' ), implode( ', ', $booking_titles ) ),
						)
					);
				}
			}
			// phpcs:enable
		}


		/**
		 * Hide order item meta
		 *
		 * @param array $hidden Hidden meta(s).
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function hide_order_item_meta( $hidden ) {
			$hidden[] = '_added-to-cart-timestamp';

			return $hidden;
		}

		/**
		 * Show related bookings in order table
		 *
		 * @param WC_Order $order The order.
		 */
		public function show_related_bookings( $order ) {
			$order_id = $order->get_id();
			$bookings = yith_wcbk()->booking_helper->get_bookings_by_order( $order_id );
			$bookings = apply_filters( 'yith_wcbk_order_bookings_related_to_order', $bookings, $order );
			if ( ! ! $bookings ) {
				echo '<h2>' . wp_kses_post( apply_filters( 'yith_wcbk_related_booking_title', __( 'Related Bookings', 'yith-booking-for-woocommerce' ) ) ) . '</h2>';
			}
			do_action( 'yith_wcbk_show_bookings_table', $bookings );
		}

		/**
		 * Add meta in order
		 *
		 * @param int          $item_id Item ID.
		 * @param array|object $values  values.
		 *
		 * @deprecated  since 2.0.0 use YITH_WCBK_Orders::woocommerce_checkout_create_order_line_item instead
		 */
		public function woocommerce_add_order_item_meta( $item_id, $values ) {
			// Do nothing.
		}

		/**
		 * Add booking data to order items
		 *
		 * @param WC_Order_Item_Product $item          Order Item.
		 * @param string                $cart_item_key Cart item key.
		 * @param array                 $values        Values.
		 * @param WC_Order              $order         The Order.
		 *
		 * @since 2.0.0
		 */
		public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
			$booking_data = $values['yith_booking_data'] ?? false;
			$booking_data = is_array( $booking_data ) ? $booking_data : false;

			if ( $booking_data ) {
				$item->add_meta_data( 'yith_booking_data', $booking_data, true );

				// Add booking id data if booking requires confirmation and it's confirmed.
				if ( isset( $booking_data['_booking_id'] ) ) {
					$item->add_meta_data( '_booking_id', $booking_data['_booking_id'], true );
				}
			}
		}

		/**
		 * Parse booking data to retrieve correct values from people, services and service quantities
		 *
		 * @param array $booking_data Booking data array.
		 *
		 * @return array
		 * @since      2.0.6
		 * @deprecated 4.0.0
		 */
		public function parse_booking_data( $booking_data ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Orders::parse_booking_data', '4.0.0' );
			if ( ! empty( $booking_data['person_types'] ) ) {
				$booking_data_person_types = array();
				foreach ( $booking_data['person_types'] as $person_type_id => $person_type_number ) {
					$person_type_title           = get_the_title( $person_type_id );
					$booking_data_person_types[] = array(
						'id'     => $person_type_id,
						'title'  => $person_type_title,
						'number' => $person_type_number,
					);
				}
				$booking_data['person_types'] = $booking_data_person_types;
			}

			if ( yith_wcbk_is_services_module_active() ) {
				if ( ! empty( $booking_data['booking_services'] ) ) {
					$booking_data_services = array();
					$service_quantities    = $booking_data['booking_service_quantities'] ?? array();
					foreach ( $booking_data['booking_services'] as $service_id ) {
						$service = yith_wcbk_get_service( $service_id );
						if ( $service ) {
							$quantity                   = $service_quantities[ $service_id ] ?? false;
							$booking_data_services[]    = array(
								'id'     => $service_id,
								'title'  => $service->get_name_with_quantity( $quantity ),
								'hidden' => $service->is_hidden(),
							);
							$booking_data['services'][] = $service_id;
						}
					}
					$booking_data['booking_services'] = $booking_data_services;
				}

				if ( ! empty( $booking_data['booking_service_quantities'] ) ) {
					$booking_data['service_quantities'] = $booking_data['booking_service_quantities'];
					unset( $booking_data['booking_service_quantities'] );
				}
			}

			return apply_filters( 'yith_wcbk_order_parse_booking_data', $booking_data );
		}

		/**
		 * Get booking details from booking data.
		 *
		 * @param array $booking_data Booking data array.
		 * @param int   $product_id   Product ID.
		 *
		 * @return array
		 * @since      2.0.6
		 * @deprecated 4.0.0
		 */
		public function get_booking_order_item_details( $booking_data, $product_id = 0 ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Orders::get_booking_order_item_details', '4.0.0' );
			$details = array();
			foreach ( $booking_data as $booking_data_key => $booking_data_value ) {
				$this_title = yith_wcbk_get_booking_meta_label( $booking_data_key );

				switch ( $booking_data_key ) {
					case 'person_types':
						if ( is_array( $booking_data_value ) ) {
							foreach ( $booking_data_value as $person_type ) {
								if ( ! empty( $person_type['number'] ) ) {
									$details[] = array(
										'key'   => $person_type['title'],
										'value' => $person_type['number'],
									);
								}
							}
						}
						break;
					case 'booking_services':
						if ( is_array( $booking_data_value ) ) {
							$booking_services        = array();
							$hidden_booking_services = array();
							foreach ( $booking_data_value as $service ) {
								if ( ! $service['hidden'] ) {
									$booking_services[] = $service['title'];
								} else {
									$hidden_booking_services[] = $service['title'];
								}
							}
							if ( ! ! $booking_services ) {
								$details[] = array(
									'key'   => yith_wcbk_get_label( 'booking-services' ),
									'value' => yith_wcbk_booking_services_html( $booking_services ),
								);
							}
							if ( ! ! $hidden_booking_services ) {
								$details[] = array(
									'key'   => '_hidden_booking_services',
									'value' => yith_wcbk_booking_services_html( $hidden_booking_services ),
								);
							}
						}
						break;

					case 'from':
					case 'to':
						/**
						 * The booking product.
						 *
						 * @var WC_Product_Booking $product
						 */
						$product     = wc_get_product( $product_id );
						$date_format = wc_date_format();
						if ( $product && $product->is_type( 'booking' ) && $product->has_time() ) {
							$date_format .= ' ' . wc_time_format();
						}
						$this_value = date_i18n( $date_format, $booking_data_value );
						$details[]  = array(
							'key'   => $this_title,
							'value' => $this_value,
						);
						break;
					case 'duration':
						$this_value = $booking_data_value;
						$product    = wc_get_product( $product_id );
						if ( $product && $product instanceof WC_Product_Booking ) {
							$duration_unit       = $product->get_duration_unit();
							$duration_unit_label = yith_wcbk_get_duration_unit_label( $duration_unit, absint( $booking_data_value ) );

							$this_value .= ' ' . $duration_unit_label;
						}
						$details[] = array(
							'key'   => $this_title,
							'value' => $this_value,
						);
						break;
					default:
						$details[] = array(
							'key'   => $this_title,
							'value' => $booking_data_value,
						);
						break;
				}
			}

			return apply_filters( 'yith_wcbk_order_get_booking_order_item_details', $details, $booking_data, $product_id );
		}

		/**
		 * Check if order contains booking products.
		 * If it contains a booking product, it will create the booking
		 *
		 * @param WC_Order|int $order      Order ID.
		 * @param array        $deprecated Posted array.
		 */
		public function check_order_for_booking( $order, $deprecated = array() ) {
			if ( ! apply_filters( 'yith_wcbk_order_check_order_for_booking', true, $order instanceof WC_Order ? $order->get_id() : $order, $deprecated ) ) {
				return;
			}

			$order       = $order instanceof WC_Order ? $order : wc_get_order( $order );
			$order_items = $order->get_items();

			if ( ! $order_items ) {
				return;
			}

			$bookings = $order->get_meta( self::$order_bookings_meta );
			$bookings = ! ! $bookings && is_array( $bookings ) ? $bookings : array();

			foreach ( $order_items as $order_item_id => $order_item ) {
				if ( $order_item->is_type( 'line_item' ) ) {

					if ( ! apply_filters( 'yith_wcbk_order_check_order_item_for_booking', true, $order, $order_item ) ) {
						continue;
					}

					/**
					 * Order item product and booking product.
					 *
					 * @var WC_Order_Item_Product $order_item
					 * @var WC_Product_Booking    $product
					 */
					$product = $order_item->get_product();
					if ( ! $product || ! yith_wcbk_is_booking_product( $product ) ) {
						continue;
					}

					$booking_data   = $order_item->get_meta( 'yith_booking_data' );
					$the_booking_id = $order_item->get_meta( '_booking_id' );

					if ( ! $the_booking_id && ! ! $booking_data && isset( $booking_data['from'] ) ) {
						$props      = array();
						$meta_data  = array();
						$product_id = apply_filters( 'yith_wcbk_booking_product_id_to_translate', $product->get_id() );
						$booking    = new YITH_WCBK_Booking();
						$all_props  = YITH_WCBK_Cart::get_booking_props_from_booking_data( $booking_data );

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

						$booking->set_product_id( $product_id );
						$booking->set_raw_title( $product->get_title() );
						$booking->set_order_id( $order->get_id() );
						$booking->set_order_item_id( $order_item_id );
						$booking->set_user_id( $order->get_user_id() );

						$booking->save();

						if ( $booking->get_id() ) {
							$order_item->add_meta_data( '_booking_id', $booking->get_id(), true );
							$order_item->save_meta_data();

							$bookings[] = $booking->get_id();

							$order->add_order_note(
								sprintf(
								// translators: 1. is the URL of the booking edit page; 2. is the booking ID.
									__( 'A new booking <a href="%1$s">#%2$s</a> has been created from this order', 'yith-booking-for-woocommerce' ),
									admin_url( 'post.php?post=' . $booking->get_id() . '&action=edit' ),
									$booking->get_id()
								)
							);

							do_action( 'yith_wcbk_order_booking_created', $booking, $order, $order_item_id );
						}
					} elseif ( $the_booking_id ) {
						$booking = yith_get_booking( $the_booking_id );
						if ( $booking && $booking->is_valid() && apply_filters( 'yith_wcbk_orders_associate_order_item_booking_to_order', $booking->has_status( 'confirmed' ), $booking, $order, $order_item ) ) {
							$booking->set_order_id( $order->get_id() );
							$booking->set_order_item_id( $order_item->get_id() );

							$booking_note = sprintf(
							// translators: 1. is the URL of the booking edit page; 2. is the booking ID.
								__( 'Booking associated to order <a href="%1$s">#%2$s</a>', 'yith-booking-for-woocommerce' ),
								$order->get_edit_order_url(),
								$order->get_order_number()
							);
							$booking->enqueue_note( 'new-order', $booking_note );
							$booking->update_status( 'unpaid' );

							$booking->save();
						}
					}
				}
			}

			$order->update_meta_data( self::$order_bookings_meta, array_unique( $bookings ) );
			$order->save_meta_data();
		}

		/**
		 * Set Booking as paid
		 *
		 * @param int      $order_id Order ID.
		 * @param WC_Order $order    The Order.
		 */
		public function set_booking_as_paid( $order_id, $order ) {
			if ( ! ! apply_filters( 'yith_wcbk_orders_set_booking_as_paid', true, $order_id, $order ) ) {
				$bookings = yith_wcbk_booking_helper()->get_bookings_by_order( $order_id );
				if ( ! ! ( $bookings ) ) {
					foreach ( $bookings as $booking ) {
						if ( $booking instanceof YITH_WCBK_Booking && apply_filters( 'yith_wcbk_orders_should_set_booking_as_paid', ! $booking->has_status( 'cancelled' ), $booking, $order ) ) {
							$booking->update_status( 'paid' );
						}
					}
				}
			}
		}

		/**
		 * Set Booking as cancelled
		 *
		 * @param int      $order_id Order ID.
		 * @param WC_Order $order    The order.
		 *
		 * @since 1.0.1
		 */
		public function set_booking_as_cancelled( $order_id, $order ) {
			$bookings = yith_wcbk_booking_helper()->get_bookings_by_order( $order_id );
			if ( ! ! $bookings ) {
				$order_number = $order ? $order->get_order_number() : $order_id;
				foreach ( $bookings as $booking ) {
					if ( $booking instanceof YITH_WCBK_Booking ) {
						$additional_note = sprintf(
						// translators: %s is the order ID (with link).
							__( 'Reason: order %s has been cancelled.', 'yith-booking-for-woocommerce' ),
							sprintf(
								'<a href="%s">#%s</a>',
								esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ),
								absint( $order_number )
							)
						);

						$booking->update_status( 'cancelled', $additional_note );
					}
				}
			}
		}

		/**
		 * Filter order item name
		 *
		 * @param string                              $name The product name shown in order.
		 * @param WC_Order_Item|WC_Order_Item_Product $item The order item.
		 *
		 * @return string
		 */
		public function order_item_name( $name, $item ) {
			$product = $item->is_type( 'line_item' ) ? $item->get_product() : false;

			if ( $product && is_a( $product, 'WC_Product' ) && yith_wcbk_is_booking_product( $product ) ) {
				$name = yith_wcbk_product_booking_of_name( $name );
			}

			return $name;
		}

		/**
		 * Filter order item display meta key.
		 *
		 * @param string $key The key.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function filter_order_item_display_meta_key( $key ) {
			$map = array(
				'_booking_id' => __( 'Booking', 'yith-booking-for-woocommerce' ),
			);

			if ( isset( $map[ $key ] ) ) {
				return $map[ $key ];
			}

			return $key;
		}

		/**
		 * Filter order item display meta value.
		 *
		 * @param mixed  $display_value The display value.
		 * @param object $meta          The meta.
		 *
		 * @return mixed|string
		 * @since 4.0.0
		 */
		public function filter_order_item_display_meta_value( $display_value, $meta ) {
			$key   = $meta->key;
			$value = $meta->value;

			switch ( $key ) {
				case '_booking_id':
					$display_value = sprintf(
						'<a href="%s">%s</a>',
						esc_url( get_edit_post_link( $value ) ),
						'#' . $value
					);
					break;
			}

			return $display_value;
		}
	}
}
