<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSPointPriceFrontend' ) ) {

	class RSPointPriceFrontend {

		public static function init() {
			add_action( 'wp_head', array( __CLASS__, 'hide_wc_coupon_field' ) );

			add_action( 'wp_head', array( __CLASS__, 'redirect_if_coupon_removed' ) );

			add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'display_point_price_for_booking' ), 10 );

			add_action( 'woocommerce_before_cart_table', array( __CLASS__, 'replace_coupon_notice_for_point_price' ) );

			add_filter( 'woocommerce_checkout_coupon_message', array( __CLASS__, 'replace_coupon_notice_for_point_price' ), 1 );

			add_filter( 'woocommerce_is_purchasable', array( __CLASS__, 'is_purchasable_simple_product' ), 10, 2 );
			// Commented this hook on version 24.2.3
			// add_filter( 'woocommerce_show_variation_price' , array( __CLASS__ , 'is_purchasable_variable_product' ) , 10 , 3 ) ;

			add_filter( 'woocommerce_get_variation_price_html', array( __CLASS__, 'point_price_for_variable_product' ), 10, 2 );

			add_action( 'woocommerce_add_to_cart', array( __CLASS__, 'set_point_price_for_products_in_session' ), 1, 5 );

			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'save_point_price_info_in_order' ) );

			add_filter( 'woocommerce_cart_total', array( __CLASS__, 'total_in_cart_with_shipping_and_tax' ) );

			add_filter( 'woocommerce_cart_item_price', array( __CLASS__, 'product_price_in_cart' ), 999, 3 );

			add_filter( 'woocommerce_cart_item_subtotal', array( __CLASS__, 'line_total_in_cart' ), 10, 3 );

			add_filter( 'woocommerce_cart_subtotal', array( __CLASS__, 'subtotal_in_cart' ), 10, 3 );

			add_filter( 'woocommerce_calculated_total', array( __CLASS__, 'total_in_cart' ), 10, 2 );

			add_filter( 'woocommerce_order_formatted_line_subtotal', array( __CLASS__, 'line_subtotal_in_order_detail' ), 8, 3 );

			add_filter( 'woocommerce_order_subtotal_to_display', array( __CLASS__, 'subtotal_in_order_detail' ), 8, 3 );

			add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'sell_individually_functionality' ), 9, 5 );

			add_filter( 'vartable_add_to_cart_validation', array( __CLASS__, 'sell_individually_functionality' ), 10, 5 ); // compatability with woo-variations-table plugin

			add_filter( 'woocommerce_available_payment_gateways', array( __CLASS__, 'unset_gateways_for_point_price_products' ), 10, 1 );
			add_filter( 'woocommerce_shipping_packages', array( __CLASS__, 'render_shipping_method_for_point_price_products' ) );
		}

		/* Display the Point Price Label in Cart Item Price */

		public static function product_price_in_cart( $product_price, $item, $item_key ) {
			$product_price = self::get_point_price_with_label( $product_price, $item, $item_key, 'item_price' );
			return $product_price;
		}

		/* Display the Point Price Label in Cart Item Total */

		public static function line_total_in_cart( $product_price, $item, $item_key ) {
			$product_price = self::get_point_price_with_label( $product_price, $item, $item_key, 'item_total' );
			return $product_price;
		}

		public static function get_point_price_with_label( $product_price, $item, $item_key, $position ) {
			if ( '2' === get_option( 'rs_enable_disable_point_priceing' ) ) {
				return $product_price;
			}

			$visibility_for_point_price = ( '1' === get_option( 'rs_point_price_visibility' ) ) ? true : is_user_logged_in();
			if ( ! $visibility_for_point_price ) {
				return $product_price;
			}

			$ProductId            = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
			$Points               = calculate_point_price_for_products( $ProductId );
			$PointPriceType       = check_display_price_type( $ProductId );
			$PointPriceForProduct = implode( ',', $Points );
			if ( empty( $PointPriceForProduct ) ) {
				return $product_price;
			}

			$IndividualPointsForProduct = ( 'item_price' === $position ) ? $PointPriceForProduct : $PointPriceForProduct * $item['quantity'];
			if ( 'no' === get_option( 'rs_enable_product_category_level_for_points_price' ) ) {  // Quick Setup
				if ( '2' === get_option( 'rs_local_enable_disable_point_price_for_product' ) ) {
					return $product_price;
				}

				$product_price = self::point_price_value( $product_price, $PointPriceType, $IndividualPointsForProduct );
				return $product_price;
			} elseif ( '1' === get_post_meta( $ProductId, '_enable_reward_points_price', true ) || 'yes' === get_post_meta( $ProductId, '_rewardsystem_enable_point_price', true ) ) {    // Advance Setup.
					$product_price = self::point_price_value( $product_price, $PointPriceType, $IndividualPointsForProduct );
					return $product_price;
			}
			return $product_price;
		}

		/**
		 * Get Point Price Value to display.
		 *
		 * @param float  $product_price Product Price.
		 * @param string $point_price_type Point Price Type.
		 * @param float  $product_points Each Product Points.
		 * */
		public static function point_price_value( $product_price, $point_price_type, $product_points ) {
			$points            = round_off_type( $product_points );
			$point_price_value = display_point_price_value( $points );
			$separator         = get_option( 'rs_separator_for_point_price' );

			if ( '1' === $point_price_type ) { // Currency & Point Pricing.
				$product_price = $product_price . $point_price_value;
			} else {  // Only Point Price.
				$product_price = str_replace( $separator, '', $point_price_value );
			}

			return $product_price;
		}

		/**
		 * Display Point Price Label in Order Detail for Subtotal.
		 *
		 * @param float   $line_total Line Total.
		 * @param int     $id Order Id.
		 * @param WP_Post $order Order Object.
		 * */
		public static function subtotal_in_order_detail( $line_total, $id, $order ) {
			if ( '2' === get_option( 'rs_enable_disable_point_priceing' ) ) {
				return $line_total;
			}

			$visibility_for_point_price = ( '1' === get_option( 'rs_point_price_visibility' ) ) ? true : is_user_logged_in();
			if ( ! $visibility_for_point_price ) {
				return $line_total;
			}

			$order_object = srp_order_obj( $order );
			if ( 'reward_gateway' !== $order->get_payment_method() ) {
				return $line_total;
			}

			$order_obj = $order->get_items();

			if ( ! srp_check_is_array( $order_obj ) ) {
				return $line_total;
			}

			$point_price = srp_pp_get_point_price_values( $order_obj );

			if ( ! srp_check_is_array( $point_price ) ) {
				return $line_total;
			}

			if ( 'yes' === $point_price['regular_product'] ) {
				return $line_total;
			}

			if ( 'yes' === $point_price['enable_point_price'] ) {
				$total_points  = round_off_type( $point_price['points'] );
				$product_price = display_point_price_value( $total_points );
				$separator     = get_option( 'rs_separator_for_point_price' );
				$product_price = str_replace( $separator, '', $product_price );
				return $product_price;
			}

			return $line_total;
		}

		/**
		 * Display Point Price Label in Order Detail for Line Total.
		 * */
		public static function line_subtotal_in_order_detail( $line_total, $id, $order ) {
			if ( '2' === get_option( 'rs_enable_disable_point_priceing' ) ) {
				return $line_total;
			}

			$visibility_for_point_price = ( '1' === get_option( 'rs_point_price_visibility' ) ) ? true : is_user_logged_in();
			if ( ! $visibility_for_point_price ) {
				return $line_total;
			}

			$order_object = srp_order_obj( $order );
			$gateway_id   = $order->get_meta( 'payment_method' );
			if ( 'reward_gateway' !== $gateway_id ) {
				return $line_total;
			}

			$point_priced_product = array();
			$ProductId            = ! empty( $id['variation_id'] ) ? $id['variation_id'] : $id['product_id'];
			$PointPriceData       = calculate_point_price_for_products( $ProductId );
			$tax_display          = get_option( 'woocommerce_tax_display_cart' );

			if ( ! check_display_price_type( $ProductId ) ) {
				$point_priced_product[] = $ProductId;
			}

			if ( srp_check_is_array( $point_priced_product ) ) {
				return $line_total;
			}

			if ( ! empty( $PointPriceData[ $ProductId ] ) ) {
				$Points        = $PointPriceData[ $ProductId ] * $id['qty'];
				$product_price = display_point_price_value( $Points );
				$separator     = get_option( 'rs_separator_for_point_price' );
				$product_price = str_replace( $separator, '', $product_price );
			} else {
				$PointPriceLabel   = str_replace( '/', '', get_option( 'rs_label_for_point_value' ) );
				$line_subtotal     = isset( $id['line_subtotal'] ) ? $id['line_subtotal'] : 0;
				$line_subtotal_tax = isset( $id['line_subtotal_tax'] ) ? $id['line_subtotal_tax'] : 0;
				$subtotal          = 'incl' == $tax_display ? $line_subtotal + $line_subtotal_tax : $line_subtotal;
				$Points            = redeem_point_conversion( $subtotal, $order_object['order_userid'] );
				if ( '1' == get_option( 'rs_sufix_prefix_point_price_label' ) ) {
					$product_price = '<span class="fp-srp-point-price-label">' . $PointPriceLabel . '</span>' . $Points;
				} else {
					$product_price = $Points . '<span class="fp-srp-point-price-label">' . $PointPriceLabel . '</span>';
				}
			}
			return $product_price;
		}

		/* Hide WooCommerce Coupon Field when Only Point Price Product is in Cart */

		public static function hide_wc_coupon_field() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			/* Hide Redeem field after Points applied - End */
			if ( is_cart() || is_checkout() ) {
				$PointPriceType = array();
				$CartObj        = array();
				foreach ( WC()->cart->cart_contents as $item ) {
					$ProductId        = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
					$PointPriceType[] = check_display_price_type( $ProductId );
					$PointPriceData   = calculate_point_price_for_products( $ProductId );
					if ( empty( $PointPriceData[ $ProductId ] ) ) {
						continue;
					}

					$CartObj[] = $PointPriceData[ $ProductId ];
				}
				if ( srp_check_is_array( $CartObj ) || in_array( 2, $PointPriceType ) ) {
					woocommerce_coupon_field( 'hide' );
				}
			}
		}

		/* Display Point Price for Booking Product */

		public static function display_point_price_for_booking() {
			if ( class_exists( 'WC_Bookings' ) ) {
				?>
				<div class="wc-bookings-booking-cost1"></div> 
				<?php
			}
		}

		/* Replace Coupon Message for Point Price */

		public static function replace_coupon_notice_for_point_price( $message ) {
			$PointPriceType  = array();
			$PointPriceValue = array();
			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId        = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				$PointPriceType[] = check_display_price_type( $ProductId );
				$CheckIfEnable    = calculate_point_price_for_products( $ProductId );
				if ( ! empty( $CheckIfEnable[ $ProductId ] ) ) {
					$PointPriceValue[] = $CheckIfEnable[ $ProductId ];
				}
			}

			if ( ! srp_check_is_array( $PointPriceValue ) && ! in_array( 2, $PointPriceType ) ) {
				return $message;
			}

			$message = ( '1' === get_option( 'rs_show_hide_message_errmsg_for_point_price_coupon' ) ) ? get_option( 'rs_errmsg_for_redeem_in_point_price_prt' ) : '';
			if ( is_cart() ) {
				if ( $message ) {
					?>
					<div class="woocommerce-info"><?php echo do_shortcode( $message ); ?></div>
					<?php
				}
			}
			if ( is_checkout() ) {
				if ( ! $message ) {
					$message = "<span class='displaymessage'></span>";
				}

				return $message;
			}
		}

		/**
		 * Display Point Price Label in Cart for Subtotal.
		 *
		 * @param float  $cart_sub_total Cart Subtotal.
		 * @param string $compound Compound.
		 * @param Object $cart_obj Cart Object.
		 * */
		public static function subtotal_in_cart( $cart_sub_total, $compound, $cart_obj ) {
			if ( '2' === get_option( 'rs_enable_disable_point_priceing' ) ) {
				return $cart_sub_total;
			}

			$visibility_for_point_price = ( '1' === get_option( 'rs_point_price_visibility' ) ) ? true : is_user_logged_in();
			if ( ! $visibility_for_point_price ) {
				return $cart_sub_total;
			}

			$OnlyPointPriceValue     = array();
			$CurrencyPointPriceValue = array();
			foreach ( $cart_obj->cart_contents as $item ) {
				$ProductId = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];

				if ( '2' === check_display_price_type( $ProductId ) ) {
					$CheckIfEnable = calculate_point_price_for_products( $ProductId );
					if ( ! empty( $CheckIfEnable[ $ProductId ] ) ) {
						$OnlyPointPriceValue[] = $CheckIfEnable[ $ProductId ] * $item['quantity'];
					}
				} elseif ( 1 == check_display_price_type( $ProductId ) ) {
					$CheckIfEnable = calculate_point_price_for_products( $ProductId );
					if ( ! empty( $CheckIfEnable[ $ProductId ] ) ) {
						$CurrencyPointPriceValue[] = $CheckIfEnable[ $ProductId ] * $item['quantity'];
					} else {
						$CurrencyPointPriceValue[] = $item['line_subtotal'];
					}
				}
			}

			$CurrencyPointPriceAmnt = array_sum( $CurrencyPointPriceValue );

			if ( ! empty( $CurrencyPointPriceAmnt ) ) {
				$CurrencyPointPriceAmnt = $CurrencyPointPriceAmnt + self::get_point_price_value_for_normal_product( $cart_obj );
				$CurrencyPointPriceAmnt = round_off_type( $CurrencyPointPriceAmnt );
				$PointPrice             = display_point_price_value( $CurrencyPointPriceAmnt );
				return $cart_sub_total . $PointPrice;
			}

			$OnlyPointPriceAmnt = array_sum( $OnlyPointPriceValue );
			if ( ! empty( $OnlyPointPriceAmnt ) ) {
				$cart_sub_total = display_point_price_value( $OnlyPointPriceAmnt );
				$separator      = get_option( 'rs_separator_for_point_price' );
				$cart_sub_total = str_replace( $separator, '', $cart_sub_total );
				return $cart_sub_total;
			}

			return $cart_sub_total;
		}

		/**
		 * Get point price value for normal product
		 *
		 * @param Object $cart Cart Object.
		 * */
		public static function get_point_price_value_for_normal_product( $cart ) {

			if ( ! srp_check_is_array( $cart->cart_contents ) ) {
				return;
			}

			$price_include_tax = wc_prices_include_tax();
			$tax_display       = get_option( 'woocommerce_tax_display_cart' );

			$calculate_tax = false;
			if ( $price_include_tax ) {
				if ( 'incl' === $tax_display ) {
					$calculate_tax = true;
				}
			} elseif ( 'incl' === $tax_display ) {
					$calculate_tax = true;
			}

			$total_line_subtotal     = 0;
			$total_line_subtotal_tax = 0;
			foreach ( $cart->cart_contents as $cart_content ) {

				$variation_id = ! empty( $cart_content['variation_id'] ) ? $cart_content['variation_id'] : 0;
				$product_id   = ! empty( $cart_content['variation_id'] ) ? $variation_id : $cart_content['product_id'];

				if ( ! $product_id || check_display_price_type( $product_id ) ) {
					continue;
				}

				$line_subtotal_total  = isset( $cart_content['line_subtotal'] ) ? $cart_content['line_subtotal'] : 0;
				$total_line_subtotal += $line_subtotal_total;

				if ( $calculate_tax ) {
					$line_subtotal_tax        = isset( $cart_content['line_subtotal_tax'] ) ? $cart_content['line_subtotal_tax'] : 0;
					$total_line_subtotal_tax += $line_subtotal_tax;
				}
			}

			$total_line_subtotal = ! empty( $total_line_subtotal_tax ) ? $total_line_subtotal + $total_line_subtotal_tax : $total_line_subtotal;

			return ! empty( $total_line_subtotal ) ? redeem_point_conversion( $total_line_subtotal, get_current_user_id() ) : $total_line_subtotal;
		}


		/**
		 * Display Point Price Label in Cart for Total
		 *
		 * @param float  $cart_total Cart Total.
		 * @param Object $cart_object Cart Object.
		 * */
		public static function total_in_cart( $cart_total, $cart_object ) {
			if ( '2' === get_option( 'rs_enable_disable_point_priceing' ) ) {
				return $cart_total;
			}

			$visibility_for_point_price = ( '1' === get_option( 'rs_point_price_visibility' ) ) ? true : is_user_logged_in();
			if ( ! $visibility_for_point_price ) {
				return $cart_total;
			}

			$point_price_value = array();
			foreach ( $cart_object->cart_contents as $item ) {
				$product_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				if ( ( null === check_display_price_type( $product_id ) ) || '2' !== check_display_price_type( $product_id ) ) {
					continue;
				}

				$check_if_enable = calculate_point_price_for_products( $product_id );
				if ( ! empty( $check_if_enable[ $product_id ] ) ) {
					$point_price_value[] = $check_if_enable[ $product_id ] * $item['quantity'];
				}
			}

			return srp_check_is_array( $point_price_value ) ? array_sum( $point_price_value ) : $cart_total;
		}

		/**
		 * Display Point Price Label in Cart for Total with Shipping and Tax.
		 *
		 * @param float $price Price.
		 * */
		public static function total_in_cart_with_shipping_and_tax( $price ) {
			if ( '2' === get_option( 'rs_enable_disable_point_priceing' ) ) {
				return $price;
			}

			$visibility_for_point_price = ( '1' === get_option( 'rs_point_price_visibility' ) ) ? true : is_user_logged_in();
			if ( ! $visibility_for_point_price ) {
				return $price;
			}

			$EnablePointPriceValue       = array();
			$ItemPointsTotal             = array();
			$EnablePointPriceForVariable = array();
			$EnablePointPriceforSimple   = array();
			$ShippingTotal               = WC()->shipping->shipping_total;
			$CouponAmount                = WC()->cart->get_cart_discount_total();
			$ShippingCost                = $ShippingTotal;
			$point_priced_product        = array();
			$cart_obj                    = WC()->cart->cart_contents;

			if ( ! srp_check_is_array( $cart_obj ) ) {
				return $price;
			}

			$point_price = srp_pp_get_point_price_values( $cart_obj );

			if ( ! srp_check_is_array( $point_price ) ) {
				return $price;
			}

			$tax_total        = WC()->cart->get_total_tax();
			$tax_total_points = redeem_point_conversion( $tax_total, get_current_user_id() );
			$shipping_points  = redeem_point_conversion( $ShippingCost, get_current_user_id() );

			if ( 'yes' === $point_price['enable_point_price'] ) {
				$total_points      = round_off_type( $point_price['points'] + $tax_total_points + $shipping_points );
				$point_price_value = display_point_price_value( $total_points );
				if ( '2' === $point_price['point_price_type'] ) {
					$display_total = str_replace( '/', '', $point_price_value );
					$separator     = get_option( 'rs_separator_for_point_price' );
					$display_total = str_replace( $separator, '', $display_total );

					return $display_total;
				} else {
					if ( 0 === $point_price['points'] ) {
						return $price;
					}

					return $price . $point_price_value;
				}
			}

			return $price;
		}

		/* Check If Purchaseable Point Price Product - Simple */

		public static function is_purchasable_simple_product( $Purchaseable, $ProductObj ) {
			$ProductId = product_id_from_obj( $ProductObj );
			if ( '2' == check_display_price_type( $ProductId ) ) {
				return $Purchaseable;
			}

			$CheckIfEnable = calculate_point_price_for_products( $ProductId );
			if ( ! empty( $CheckIfEnable[ $ProductId ] ) ) {
				return true;
			}

			return $Purchaseable;
		}

		/* Check If Purchaseable Point Price Product - Variable */

		public static function is_purchasable_variable_product( $Purchaseable, $obj, $ProductObj ) {
			$ProductId = product_id_from_obj( $ProductObj );
			if ( '2' === check_display_price_type( $ProductId ) ) {
				return $Purchaseable;
			}

			$CheckIfEnable = calculate_point_price_for_products( $ProductId );
			if ( ! empty( $CheckIfEnable[ $ProductId ] ) ) {
				return true;
			}

			return $Purchaseable;
		}

		/**
		 * Display Point Price Label for Variable Product.
		 *
		 * @param float  $price Price to display.
		 * @param Object $product_obj Product Object.
		 * */
		public static function point_price_for_variable_product( $price, $product_obj ) {
			if ( ! is_user_logged_in() ) {
				return $price;
			}

			$variation_id = product_id_from_obj( $product_obj );
			if ( '2' !== check_display_price_type( $variation_id ) ) {
				return $price;
			}

			$enable = calculate_point_price_for_products( $variation_id );
			if ( ! empty( $enable[ $variation_id ] ) ) {
				return $price;
			}

			$price = display_point_price_value( $enable[ $variation_id ] );
			return $price;
		}

		/**
		 * Redirect to Cart if Coupon Removed.
		 * */
		public static function redirect_if_coupon_removed() {
			if ( isset( $_GET['remove_coupon'] ) ) {
				wp_redirect( wc_get_page_permalink( 'cart' ) );
			}
		}

		/* Set Point Price Value in Session */

		public static function set_point_price_for_products_in_session( $cart_item_key, $product_id = null, $quantity = null, $variation_id = null, $variation = null ) {
			$ProductId       = ! empty( $variation_id ) ? $variation_id : $product_id;
			$PointPriceValue = calculate_point_price_for_products( $ProductId );
			WC()->session->set( $cart_item_key . 'point_price_for_product', $PointPriceValue );
		}

		/**
		 * Save Point Price Detail in Order
		 *
		 * @param int $order_id Order ID.
		 */
		public static function save_point_price_info_in_order( $order_id ) {
			$point_price_info = array();
			foreach ( WC()->cart->cart_contents as $key => $value ) {
				if ( WC()->session->get( $key . 'point_price_for_product' ) ) {
					$point_price_info[] = WC()->session->get( $key . 'point_price_for_product' );
				}
			}
			$order = wc_get_order( $order_id );
			$order->update_meta_data( 'point_price_for_product_in_order', $point_price_info );
			$order->save();

			WC()->session->set( 'auto_redeemcoupon', 'yes' );
		}

		/**
		 * Check If Normal Product is purchased with Point Price Product
		 * */
		public static function sell_individually_functionality( $valid, $product_id, $quantity, $variation_id = null, $variations = null ) {
			if ( '2' === get_option( 'rs_enable_disable_point_priceing' ) ) {
				return $valid;
			}

			$ProductIdAdded = isset( $variation_id ) ? $variation_id : $product_id;
			if ( ! is_user_logged_in() && '2' != get_option( 'rs_point_price_visibility' ) && check_display_price_type( $ProductIdAdded ) ) {
				wc_add_notice( do_shortcode( get_option( 'rs_point_price_product_added_to_cart_guest_errmsg', 'Only registered users can purchase this product. Click the link to create an account ([loginlink]).' ) ), 'error' );
				return;
			}

			if ( ! function_exists( 'WC' ) ) {
				return $valid;
			}

			if ( ! srp_check_is_array( WC()->cart->get_cart() ) ) {
				return $valid;
			}

			foreach ( WC()->cart->get_cart() as $item ) {
				if ( WC()->cart->cart_contents_count > 0 && 1 <= WC()->cart->cart_contents_count ) {
					$ProductId = product_id_from_obj( $item['data'] );
					$valid     = self::check_if_point_price_product_is_added_to_cart( $ProductIdAdded, $ProductId );
				} elseif ( self::check_is_point_pricing_enable( $ProductIdAdded ) ) {
						WC()->cart->empty_cart();
						wc_add_notice( get_option( 'rs_errmsg_for_normal_product_with_point_price' ), 'error' );
						$valid = true;
				}
			}
			return $valid;
		}

		public static function check_is_point_pricing_enable( $ProductId ) {
			$EnablePointPrice = get_post_meta( $ProductId, '_rewardsystem_enable_point_price', true ) != '' ? get_post_meta( $ProductId, '_rewardsystem_enable_point_price', true ) : get_post_meta( $ProductId, '_enable_reward_points_price', true );
			$Points           = get_post_meta( $ProductId, '_rewardsystem__points', true ) != '' ? get_post_meta( $ProductId, '_rewardsystem__points', true ) : get_post_meta( $ProductId, 'price_points', true );
			$DisplayType      = get_post_meta( $ProductId, '_rewardsystem_enable_point_price_type', true ) != '' ? get_post_meta( $ProductId, '_rewardsystem_enable_point_price_type', true ) : get_post_meta( $ProductId, '_enable_reward_points_pricing_type', true );
			if ( ( 'yes' != $EnablePointPrice ) && ( '1' != $EnablePointPrice ) ) {
				return false;
			}

			if ( '2' == $DisplayType ) {
				return false;
			}

			if ( empty( $Points ) ) {
				return false;
			}

			return true;
		}

		public static function check_if_point_price_product_is_added_to_cart( $ProductIdAdded, $ProductId ) {
			if ( '2' == check_display_price_type( $ProductId ) ) {
				if ( '1' == check_display_price_type( $ProductIdAdded ) || is_null(check_display_price_type( $ProductIdAdded )) ) {
					wc_add_notice( get_option( 'rs_errmsg_for_normal_product_with_point_price' ), 'error' );
					return false;
				}
			} elseif ( '' == check_display_price_type( $ProductId ) ) {
				if ( '2' == check_display_price_type( $ProductIdAdded ) ) {
					wc_add_notice( get_option( 'rs_errmsg_for_point_price_product_with_normal' ), 'error' );
					return false;
				}
			} elseif ( '2' == check_display_price_type( $ProductIdAdded ) ) {
				if ( '1' == check_display_price_type( $ProductId ) ) {
					return true;
				}
			}
			return true;
		}

		// Shows only SUMO Reward Gateway on using Point price Product
		public static function unset_gateways_for_point_price_products( $gateways ) {
			global $woocommerce;
			if ( ! isset( $woocommerce->cart->cart_contents ) || ! srp_check_is_array( $woocommerce->cart->cart_contents ) ) {
				return $gateways;
			}

						$visibility_for_point_price = ( 1 == get_option( 'rs_point_price_visibility' ) ) ? true : is_user_logged_in();
			if ( ! $visibility_for_point_price ) {
				return $gateways;
			}

			foreach ( $woocommerce->cart->cart_contents as $key => $values ) {
				$productid = ! empty( $values['variation_id'] ) ? $values['variation_id'] : $values['product_id'];
				if ( '2' !== check_display_price_type( $productid ) ) {
					continue;
				}

				foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
					if ( 'reward_gateway' === $gateway->id ) {
						continue;
					}

					unset( $gateways[ $gateway->id ] );
				}
			}

			return 'NULL' != $gateways ? $gateways : array();
		}

		/**
		 * Display only free shipping method for point price products.
		 *
		 * @return array.
		 * @since 28.9
		 */
		public static function render_shipping_method_for_point_price_products( $packages ) {
			if ( 'yes' !== get_option( 'rs_point_price_activated' ) ) {
				return $packages;
			}

			if ( ! srp_check_is_array( $packages ) ) {
				return $packages;
			}

			if ( 'yes' !== get_option( 'rs_pp_restrict_shipping_cost' ) ) {
				return $packages;
			}

			$cart_obj = WC()->cart->cart_contents;

			if ( ! srp_check_is_array( $cart_obj ) ) {
				return $packages;
			}

			$is_only_point_price = srp_pp_check_is_only_point_price_product( $cart_obj );

			if ( true !== $is_only_point_price ) {
				return $packages;
			}

			foreach ( $packages as $i => $package ) {
				$has_free_shipping = false;

				if ( ! isset( $package['rates'] ) || ! srp_check_is_array( $package['rates'] ) ) {
					continue;
				}

				foreach ( $package['rates'] as $method_id => $rate ) {
					if ( 'free_shipping' === $rate->method_id ) {
						$has_free_shipping = true;
					} else {
						unset( $packages[ $i ]['rates'][ $method_id ] );
					}
				}

				if ( ! $has_free_shipping ) {
					unset( $packages[ $i ] );
				}
			}

			return $packages;
		}
	}

	RSPointPriceFrontend::init();
}
