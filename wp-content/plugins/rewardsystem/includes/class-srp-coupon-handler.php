<?php
/**
 * Coupon Handler.
 *
 * @package SUMO Reward Points
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'SRP_Coupon_Handler' ) ) {

	/**
	 * Class.
	 */
	class SRP_Coupon_Handler {

		/**
		 * Redeemed Points.
		 *
		 * @var float
		 * */
		protected $redeemed_points;

		/**
		 * Available Points.
		 *
		 * @var float
		 * */
		protected $available_points;

		/**
		 * User ID.
		 *
		 * @var int
		 * */
		protected $user_id;

		/**
		 * User Data.
		 *
		 * @var Object
		 * */
		protected $user_data;

		/**
		 * Redeeming Type.
		 *
		 * @var string
		 * */
		protected $type = 'sumo';

		/**
		 * Coupon Code.
		 *
		 * @var string
		 * */
		protected $coupon_code;

		/**
		 * Coupon iD.
		 *
		 * @var int
		 * */
		protected $coupon_id;

		/**
		 * Class Initialization.
		 *
		 * @param string $user_id User ID.
		 * @param string $redeemed_points Redeemed Points.
		 * @param string $type Redeeming Type.
		 */
		public function __construct( $user_id, $redeemed_points, $type ) {
			$points_data = new RS_Points_Data( $user_id );

			$this->type     = $type;
			$this->user_id     = $user_id;
			$this->user_data     = get_user_by( 'id', $user_id );
			$this->redeemed_points  = $redeemed_points;
			$this->available_points = $points_data->total_available_points();
			$this->coupon_code = $type . '_' . strtolower( $this->user_data->user_login );
		}

		/**
		 * Create Coupon?
		 *
		 * @param string $user_id User ID.
		 * @param string $redeemed_points Redeemed Points.
		 * @param string $type Redeeming Type.
		 *
		 * @return bool
		 */
		public static function create_coupon( $user_id, $redeemed_points, $type ) {
			$coupon_constructor = new self( $user_id, $redeemed_points, $type );

			return $coupon_constructor->create();
		}

		/**
		 * Create Coupon.
		 *
		 */
		public function create() {

			if ('sumo' == $this->type) {
				$old_coupon_id = get_user_meta( $this->user_id, 'redeemcouponids', true );
			} else {
				$old_coupon_id = get_user_meta( $this->user_id, 'auto_redeemcoupon_ids', true );
			}

			wp_delete_post( $old_coupon_id, true );
			if ( class_exists( 'WC_Cache_Helper' ) ) {
				wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_sumo_' . strtolower( $this->user_data->user_login ), 'coupons' );
			}

			$coupon_data = array(
				'post_title'   => $this->coupon_code,
				'post_content' => '',
				'post_status'  => 'publish',
				'post_author'  => $this->user_id,
				'post_type'    => 'shop_coupon',
			);
			
			$coupon_id = wp_insert_post( $coupon_data );
			$this->coupon_id = $coupon_id;

			if ($coupon_id) {
				$this->update_coupon_meta();
			}

			return $this->coupon_code;
		}

		/**
		 * Update Coupon meta
		 *
		 */
		public function update_coupon_meta() {
			$coupon_id = $this->coupon_id;
			update_post_meta( $coupon_id, 'customer_email', $this->user_data->user_email );
			update_post_meta( $coupon_id, 'discount_type', 'fixed_cart' );

			if ('sumo' == $this->type) {
				update_user_meta( $this->user_id, 'redeemcouponids', $this->coupon_id );
			} else {
				update_user_meta( $this->user_id, 'auto_redeemcoupon_ids', $this->coupon_id );
			}

			if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_products' ) ) {
				$inc_products = get_option( 'rs_select_products_to_enable_redeeming' );
				$inc_products = srp_check_is_array( $inc_products ) ? $inc_products : explode( ',', $inc_products );
				update_post_meta( $coupon_id, 'product_ids', implode( ',', array_filter( array_map( 'intval', $inc_products ) ) ) );
			}

			if ( 'yes' == get_option( 'rs_exclude_products_for_redeeming' ) ) {
				$exc_products = get_option( 'rs_exclude_products_to_enable_redeeming' );
				$exc_products = srp_check_is_array( $exc_products ) ? $exc_products : explode( ',', $exc_products );
				update_post_meta( $coupon_id, 'exclude_product_ids', implode( ',', array_filter( array_map( 'intval', $exc_products ) ) ) );

				if ('auto_redeem' == $this->type) {
					$excluded_id = get_post_meta( $coupon_id, 'exclude_product_ids', true );
					foreach ( WC()->cart->cart_contents as $key => $value ) {
						if ( $excluded_id == $value['product_id'] ) {
							WC()->session->set( 'auto_redeemcoupon', 'no' );
						}
					}
				}
			}

			if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_category' ) ) {
				$inc_category = get_option( 'rs_select_category_to_enable_redeeming' );
				$inc_category = srp_check_is_array( $inc_category ) ? $inc_category : explode( ',', $inc_category );
				update_post_meta( $coupon_id, 'product_categories', array_filter( array_map( 'intval', $inc_category ) ) );
			}

			if ( 'yes' == get_option( 'rs_exclude_category_for_redeeming' ) ) {
				$exc_category = get_option( 'rs_exclude_category_to_enable_redeeming' );
				$exc_category = srp_check_is_array( $exc_category ) ? $exc_category : explode( ',', $exc_category );
				update_post_meta( $coupon_id, 'exclude_product_categories', array_filter( array_map( 'intval', $exc_category ) ) );
			}

			$cart_total    = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) ? WC()->cart->subtotal : WC()->cart->subtotal_ex_tax;
			$cart_total    = $cart_total - $this->get_discount_amount();
			$redeem_value   = ( '1' == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ) ? $cart_total : $this->get_product_total();
			$max_threshold = ( 'sumo' == $this->type ) ? (float) get_option( 'rs_percentage_cart_total_redeem' , 100) : (float) get_option( 'rs_percentage_cart_total_auto_redeem' , 100) ;
			$max_threshold = empty( $max_threshold ) ? 100 : $max_threshold ;
			$max_threshold = ( $max_threshold / 100 ) * $this->redeemed_points;

			$fixed_value = get_option( 'rs_fixed_max_redeem_discount' );
			$percent_value = get_option( 'rs_percent_max_redeem_discount' );
			if ( '1' == get_option( 'rs_max_redeem_discount' ) && ! empty( $fixed_value ) ) {
				if ( $max_threshold > $fixed_value ) {
					$coupon_value = $fixed_value;
					$msg      = str_replace( '[percentage] %', $fixed_value, get_option( 'rs_errmsg_for_max_discount_type' ) );
					wc_add_notice( __( $msg ), 'error' );
				} else {
					$coupon_value = $max_threshold;
				}
			} elseif ( ! empty( $percent_value ) ) {
					$MaxRedeemDiscount = ( $percent_value / 100 ) * $redeem_value;
				if ( $MaxRedeemDiscount > $max_threshold ) {
					$coupon_value = ( $max_threshold / 100 ) * $percent_value;
					$msg      = str_replace( '[percentage] ', $percent_value, get_option( 'rs_errmsg_for_max_discount_type' ) );
					wc_add_notice( __( $msg ), 'error' );
				} else {
					$coupon_value = $max_threshold;
				}
			} elseif ('sumo' == $this->type) {
					$applied_points = redeem_point_conversion( $this->redeemed_points, $this->user_id, 'price' );
					$coupon_value    = ( $applied_points > $redeem_value ) ? (float) $redeem_value : (float) $applied_points;
			} else {
				$coupon_value    = empty( $max_threshold ) ? (float) $redeem_value : (float) $max_threshold;
			}

			$coupon_amount  = redeem_point_conversion( $coupon_value, $this->user_id );
			$converted_point = redeem_point_conversion( $this->available_points, $this->user_id, 'price' );
			$amount         = ( $coupon_amount > $this->available_points ) ? $converted_point : $coupon_value;
			update_post_meta( $coupon_id, 'coupon_amount', $amount );
			update_post_meta( $coupon_id, 'usage_count', '0' );
			update_post_meta( $coupon_id, 'usage_limit', '1' );
			update_post_meta( $coupon_id, 'expiry_date', '' );
			$apply_tax = ( '1' == get_option( 'rs_apply_redeem_before_tax' ) ) ? 'yes' : 'no';
			update_post_meta( $coupon_id, 'apply_before_tax', $apply_tax );
			$free_shipping = ( '1' == get_option( 'rs_apply_shipping_tax' ) ) ? 'yes' : 'no';
			update_post_meta( $coupon_id, 'free_shipping', $free_shipping );
		}

		/**
		 * Get Total Discount Amount except redeemed points
		 *
		 * @return float
		 */
		public function get_discount_amount() {
			$coupons = WC()->cart->get_coupons();
			$coupon_amount = 0;
			if ( ! srp_check_is_array( $coupons ) ) {
				return $coupon_amount;
			}

			$user = get_user_by( 'id', $this->user_data );
			if ( ! is_object( $user ) ) {
				return $coupon_amount;
			}

			$redeem      = 'sumo_' . strtolower( "$this->user_data->user_login" );
			$auto_redeem = 'auto_redeem_' . strtolower( "$this->user_data->user_login" );

			foreach ( $coupons as $coupon_code => $coupon ) {
				if ( ! is_object( $coupon ) || $coupon_code == $redeem || $coupon_code == $auto_redeem ) {
					continue;
				}

				$coupon_amount += WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );
			}

			return $coupon_amount;
		}

		/**
		 * Get Sum of selected Product Total.
		 *
		 * @return float
		 */
		public function get_product_total() {
			$inc_products = get_option( 'rs_select_products_to_enable_redeeming' );
			$inc_products = srp_check_is_array( $inc_products ) ? $inc_products : explode(',', $inc_products);

			$exc_products = get_option( 'rs_exclude_products_to_enable_redeeming' );
			$exc_products = srp_check_is_array( $exc_products ) ? $exc_products : explode(',', $exc_products);

			$inc_category = get_option( 'rs_select_category_to_enable_redeeming' );
			$inc_category = srp_check_is_array( $inc_category ) ? $inc_category : explode(',', $inc_category);

			$exc_category = get_option( 'rs_exclude_category_to_enable_redeeming' );
			$exc_category = srp_check_is_array( $exc_category ) ? $exc_category : explode(',', $exc_category);

			$product_total = array();
			foreach ( WC()->cart->cart_contents as $item ) {
				$product_id  = empty( $item['variation_id'] ) ? $item['product_id'] : $item['variation_id'];
				$product_cat = get_the_terms( $item['product_id'], 'product_cat' );
				$line_total  = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) ? ( $item['line_subtotal'] + $item['line_tax'] ) : $item['line_subtotal'];
				/* Checking whether the Product has Category */
				if ( srp_check_is_array( $product_cat ) ) {
					foreach ( $product_cat as $cat_obj ) {
						if ( ! is_object( $cat_obj ) ) {
							continue;
						}

						if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_category' ) ) {
							if ( in_array( $cat_obj->term_id, $inc_category ) ) {
								$product_total[] = $line_total;
							}
						}

						if ( 'yes' == get_option( 'rs_exclude_category_for_redeeming' ) ) {
							if ( in_array( $cat_obj->term_id, $exc_category ) ) {
								$product_total[] = $line_total;
							}
						}
					}
				}

				if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_products' ) ) {
					if ( in_array( $product_id, $inc_products ) ) {
						$product_total[] = $line_total;
					}
				}

				if ( 'yes' == get_option( 'rs_exclude_products_for_redeeming' ) ) {
					if ( ! in_array( $product_id, $exc_products ) ) {
						$product_total[] = $line_total;
					}
				}
			}

			$total = srp_check_is_array( $product_total ) ? array_sum( $product_total ) : WC()->cart->subtotal;
			return $total;
		}
	}
}
