<?php
/**
 * Class YITH_WCBK_Request_A_Quote_Integration
 * Request a Quote integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Request_A_Quote_Integration
 *
 * @since   1.0.1
 */
class YITH_WCBK_Request_A_Quote_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Init
	 */
	protected function init() {
		if ( $this->is_enabled() ) {
			add_filter( 'ywraq_add_item', array( $this, 'add_booking_to_quote' ), 10, 2 );

			add_filter( 'yith_ywraq_product_price', array( $this, 'price_in_raq_table_total' ), 10, 3 );
			add_filter( 'yith_ywraq_product_price_html', array( $this, 'price_in_raq_table' ), 15, 3 );
			add_action( 'ywraq_quote_adjust_price', array( $this, 'adjust_price_in_raq_table' ), 10, 2 );

			add_filter( 'ywraq_request_quote_view_item_data', array( $this, 'add_booking_info_in_table' ), 10, 4 );
			add_filter( 'ywraq_quantity_max_value', array( $this, 'set_booking_max_quantity' ), 10, 2 );

			add_action( 'ywraq_from_cart_to_order_item', array( $this, 'add_order_item_meta' ), 10, 3 );
			add_filter( 'ywraq_order_cart_item_data', array( $this, 'order_cart_item_data' ), 10, 3 );

			add_action( 'ywraq_request_quote_email_view_item_after_title', array( $this, 'add_booking_data_in_raq_emails' ) );

			add_filter( 'yith_ywraq_before_print_button', array( $this, 'disable_raq_for_request_confirmation_products' ), 10, 2 );

			add_action( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'filter_order_item_formatted_meta_data' ), 10, 2 );

			// Multi Vendor integration.
			add_filter( 'yith_wcbk_order_check_order_for_booking', array( $this, 'not_check_for_bookings_in_raq_orders' ), 10, 3 );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_custom_scripts' ), 99 );

		}
	}

	/**
	 * Disable RAQ button for bookable products requiring confirmation.
	 *
	 * @param bool                                $show_button Show button flag.
	 * @param WC_Product|WC_Product_Booking|false $product     The product.
	 *
	 * @return bool
	 * @since 3.6.1
	 */
	public function disable_raq_for_request_confirmation_products( $show_button, $product = false ) {
		if ( ! ! $show_button && $product && yith_wcbk_is_booking_product( $product ) && $product->is_confirmation_required() ) {
			$show_button = false;
		}

		return $show_button;
	}

	/**
	 * Add booking data in RAQ emails.
	 *
	 * @param array $item The item.
	 *
	 * @use   YITH_WCBK_Cart::get_booking_data_from_request
	 * @use   YITH_WCBK_Cart::woocommerce_get_item_data
	 * @since 1.0.16
	 */
	public function add_booking_data_in_raq_emails( $item ) {
		if ( is_array( $item ) && ! empty( $item['yith_booking_request'] ) && ! empty( $item['product_id'] ) ) {
			$request        = $item['yith_booking_request'];
			$booking_data   = YITH_WCBK_Cart::get_booking_data_from_request( $request );
			$new_line       = apply_filters( 'yith_wcbk_raq_booking_data_in_raq_emails_item_new_line', '<br/>' );
			$format         = apply_filters( 'yith_wcbk_raq_booking_data_in_raq_emails_item_format', '<strong>%1$s</strong>: %2$s%3$s' );
			$fake_cart_item = array(
				'product_id'        => $item['product_id'],
				'yith_booking_data' => $booking_data,
			);

			$data = yith_wcbk()->frontend->cart->woocommerce_get_item_data( array(), $fake_cart_item );

			echo wp_kses_post( ! ! $data ? $new_line : '' );

			foreach ( $data as $data_key => $single_data ) {
				if ( isset( $single_data['key'] ) && isset( $single_data['display'] ) ) {
					$key   = esc_html( $single_data['key'] );
					$value = wp_kses_post( $single_data['display'] );
					echo wp_kses_post( sprintf( $format, $key, $value, $new_line ) );
				}
			}
		}
	}

	/**
	 * Do not create Bookings if the order is a Quote
	 * fixes issue in combination with Multi Vendor
	 *
	 * @param bool  $check    Check flag.
	 * @param int   $order_id Order ID.
	 * @param array $posted   Posted Arguments.
	 *
	 * @return bool
	 * @since 1.0.11
	 */
	public function not_check_for_bookings_in_raq_orders( $check, $order_id, $posted = array() ) {
		$order = wc_get_order( $order_id );
		if ( $order && $order->has_status( 'ywraq-new' ) ) {
			$check = false;
		}

		return $check;
	}

	/**
	 * Add booking to quote.
	 *
	 * @param array $raq         Request a quote params.
	 * @param array $product_raq Product params.
	 *
	 * @return array
	 */
	public function add_booking_to_quote( $raq, $product_raq ) {
		$product_id = $raq['product_id'] ?? false;

		if ( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
				$booking_data_keys = array_keys( YITH_WCBK_Cart::get_default_booking_data() );
				foreach ( $product_raq as $key => $value ) {
					if ( in_array( $key, $booking_data_keys, true ) ) {
						if ( in_array( $key, array( 'from', 'to' ), true ) ) {
							$value = urldecode( $value );
						}
						$raq['yith_booking_request'][ $key ] = $value;
					}
				}
				$raq['yith_booking_request']['add-to-cart'] = $raq['product_id'];
			}
		}

		return $raq;
	}

	/**
	 * Price in RAQ table total.
	 *
	 * @param string     $price   Price.
	 * @param WC_Product $product The product.
	 * @param array      $raq     Request a quote params.
	 *
	 * @return string
	 */
	public function price_in_raq_table_total( $price, $product, $raq ) {
		if ( $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
			/**
			 * The booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			$booking_data = $this->get_booking_data_from_raq( $product, $raq );

			$price = $product->calculate_price( $booking_data );
		}

		return $price;
	}

	/**
	 * Adjust price in raq table.
	 *
	 * @param array      $raq     Request a quote params.
	 * @param WC_Product $product Product.
	 */
	public function adjust_price_in_raq_table( $raq, $product ) {
		if ( $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
			/**
			 * The booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			$booking_data = $this->get_booking_data_from_raq( $product, $raq );

			$price = $product->calculate_price( $booking_data );
			$product->set_price( $price );
		}
	}

	/**
	 * Set price in RAQ table.
	 *
	 * @param string     $price   Price.
	 * @param WC_Product $product Product.
	 * @param array      $raq     Request a quote params.
	 *
	 * @return string
	 */
	public function price_in_raq_table( $price, $product, $raq ) {
		if ( $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
			/**
			 * The booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			$booking_data = $this->get_booking_data_from_raq( $product, $raq );

			$price = wc_price( $product->calculate_price( $booking_data ) );
		}

		return $price;
	}

	/**
	 * Get the booking data from raq array
	 *
	 * @param WC_Product_Booking $product            Product.
	 * @param array              $raq                Request a quote params.
	 * @param bool               $parse_person_types Parse person types flag.
	 *
	 * @return array
	 */
	public function get_booking_data_from_raq( $product, $raq, $parse_person_types = true ) {
		$booking_data = false;
		if ( isset( $raq['yith_booking_request'] ) ) {
			$booking_data = YITH_WCBK_Cart::get_booking_data_from_request( $raq['yith_booking_request'] );

			if ( $parse_person_types && $product->has_people_types_enabled() && isset( $booking_data['person_types'] ) ) {
				$booking_data['person_types'] = yith_wcbk_booking_person_types_to_list( $booking_data['person_types'] );
			}
		}

		return $booking_data;
	}

	/**
	 * Add booking info in table.
	 *
	 * @param array      $item_data  Item data.
	 * @param array      $raq        Request a quote params.
	 * @param WC_Product $product    The product.
	 * @param bool       $show_price Show price flag.
	 *
	 * @return array
	 */
	public function add_booking_info_in_table( $item_data, $raq, $product, $show_price ) {
		if ( $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
			/**
			 * The booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			$cart_item                 = array(
				'product_id'        => $product->get_id(),
				'yith_booking_data' => $this->get_booking_data_from_raq( $product, $raq, false ),
			);
			$booking_item_data         = yith_wcbk()->frontend->cart->woocommerce_get_item_data( $item_data, $cart_item );
			$booking_item_data_for_raq = array();
			foreach ( $booking_item_data as $booking_item_data_single ) {
				if ( isset( $booking_item_data_single['key'] ) && isset( $booking_item_data_single['display'] ) ) {
					$singe_for_raq               = array(
						'key'   => $booking_item_data_single['key'],
						'value' => $booking_item_data_single['display'],
					);
					$booking_item_data_for_raq[] = $singe_for_raq;
				}
			}
			$item_data = array_merge( $item_data, $booking_item_data_for_raq );
		}

		return $item_data;
	}

	/**
	 * Set booking max quantity.
	 *
	 * @param int        $quantity Quantity.
	 * @param WC_Product $product  Product.
	 *
	 * @return int
	 */
	public function set_booking_max_quantity( $quantity, $product ) {
		if ( $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
			$quantity = 1;
		}

		return $quantity;
	}


	/**
	 * Set order cart item data.
	 *
	 * @param array    $cart_item_data Cart item data.
	 * @param array    $item           Item.
	 * @param WC_Order $order          Order.
	 *
	 * @return array
	 */
	public function order_cart_item_data( $cart_item_data, $item, $order ) {
		$to_copy = array(
			'yith_booking_request',
		);

		foreach ( $to_copy as $c ) {
			if ( isset( $item[ $c ] ) ) {
				$cart_item_data[ $c ] = maybe_unserialize( $item[ $c ] );
			}
		}

		return $cart_item_data;
	}

	/**
	 * Add order item meta.
	 *
	 * @param array  $values        Values.
	 * @param string $cart_item_key Cart item key.
	 * @param int    $item_id       Item ID.
	 *
	 * @throws Exception The exception.
	 */
	public function add_order_item_meta( $values, $cart_item_key, $item_id ) {
		if ( isset( $values['yith_booking_request'] ) ) {
			wc_add_order_item_meta( $item_id, 'yith_booking_request', $values['yith_booking_request'] );
		}
	}

	/**
	 * Add custom scripts.
	 */
	public function add_custom_scripts() {
		if ( is_product() ) {
			global $post;
			$product    = wc_get_product( $post->ID );
			$hide_price = get_option( 'ywraq_hide_price' ) === 'yes';
			if ( $hide_price && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {

				$css = '.product-type-booking .price{ display:none }';

				wp_add_inline_style( 'yith-wcbk-frontend-style', $css );
			}
		}
	}

	/**
	 * Show booking details in order item for new RAQ orders.
	 *
	 * @param bool          $show Show flag.
	 * @param WC_Order_Item $item The order item.
	 *
	 * @return bool
	 * @since 4.0.0
	 */
	public function filter_show_booking_details_in_order_item( bool $show, WC_Order_Item $item ) {
		$order   = $item->get_order();
		$old_way = $item->get_meta( 'From' );
		if ( $order && $order->has_status( 'ywraq-new' ) && ! $old_way ) {
			$show = true;
		}

		return $show;
	}

	/**
	 * Filter formatted meta
	 *
	 * @param array         $formatted_meta The formatted meta.
	 * @param WC_Order_Item $item           The order item.
	 *
	 * @return array
	 * @since 4.0.0
	 */
	public function filter_order_item_formatted_meta_data( array $formatted_meta, WC_Order_Item $item ): array {
		if ( $item instanceof WC_Order_Item_Product ) {
			$order   = $item->get_order();
			$old_way = $item->get_meta( 'From' );
			if ( $order && $order->has_status( ywraq_get_quote_status_list() ) && ! $old_way ) {
				$product = $item->get_product();
				if ( yith_wcbk_is_booking_product( $product ) ) {
					/**
					 * The booking product.
					 *
					 * @var WC_Product_Booking $product
					 */
					$booking_request = $item->get_meta( 'yith_booking_request' );
					if ( ! ! $booking_request ) {
						$booking_data = YITH_WCBK_Cart::get_booking_data_from_request( $booking_request );
						$props        = YITH_WCBK_Cart::get_booking_props_from_booking_data( $booking_data );
						$booking      = new YITH_WCBK_Booking();
						$booking->set_props( $props );

						// Set product props.
						$booking->set_product_id( $product->get_id() );
						$booking->set_duration_unit( $product->get_duration_unit() );
						$booking->set_can_be_cancelled( $product->is_cancellation_available() );
						$booking->set_cancelled_duration( $product->get_cancellation_available_up_to() );
						$booking->set_cancelled_unit( $product->get_cancellation_available_up_to_unit() );
						$booking->set_location( $product->get_location() );
						$booking->set_all_day( $product->is_full_day() );
						$booking->set_has_persons( $product->has_people() );

						$data         = $booking->get_booking_data_to_display( is_admin() ? 'admin' : 'frontend' );
						$data_to_hide = array( 'status', 'product', 'order', 'user' );
						foreach ( $data_to_hide as $key ) {
							if ( isset( $data[ $key ] ) ) {
								unset( $data[ $key ] );
							}
						}

						foreach ( $data as $key => $value ) {
							$label   = $value['label'] ?? '';
							$display = $value['display'] ?? '';
							if ( $display ) {
								$the_key                    = 'booking-' . $key;
								$formatted_meta[ $the_key ] = (object) array(
									'key'           => $the_key,
									'display_key'   => $label,
									'display_value' => $display,
								);
							}
						}
					}
				}
			}
		}

		return $formatted_meta;
	}
}
