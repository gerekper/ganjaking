<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSBuyingPointsFrontend' ) ) {

	class RSBuyingPointsFrontend {

		public static function init() {
			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'save_points_info_in_order' ), 10, 1 );

			if ( '1' == get_option( 'rs_message_before_after_cart_table' ) ) {
				if ( '1' == get_option( 'rs_reward_point_troubleshoot_before_cart' ) ) {
					add_action( 'woocommerce_before_cart', array( __CLASS__, 'msg_for_buying_points' ) );
				} else {
					add_action( 'woocommerce_before_cart_table', array( __CLASS__, 'msg_for_buying_points' ) );
				}
			} else {
				add_action( 'woocommerce_after_cart_table', array( __CLASS__, 'msg_for_buying_points' ) );
			}
			add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'msg_for_buying_points' ) );

			add_filter( 'srp_buying_points_in_cart', array( __CLASS__, 'total_buy_points_in_cart' ) );

			add_filter( 'srp_buying_points_for_payment_plan_in_cart', array( __CLASS__, 'total_buy_points_for_payment_plan_in_cart' ) );
		}

		/**
		 * Save Points Detail in Order.
		 *
		 * @param int $order_id Order ID.
		 * */
		public static function save_points_info_in_order( $order_id ) {
			$buy_point = self::buy_points_for_product_in_cart();
			if ( ! empty( $buy_point ) ) {
				$order = wc_get_order( $order_id );
				$order->update_meta_data( 'buy_points_for_current_order', $buy_point );
				$order->save();
			}
		}

		/* Total Buying Points in Cart */

		public static function total_buy_points_in_cart() {
			$buypoint    = self::buy_points_for_product_in_cart();
			$TotalPoints = array();
			if ( srp_check_is_array( $buypoint ) ) {
				foreach ( $buypoint as $Points ) {
					$TotalPoints[] = floatval( RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $Points ) );
				}
			}
			return array_sum( $TotalPoints );
		}

		/* Total Buying Points for Payment Plan in Cart */

		public static function total_buy_points_for_payment_plan_in_cart() {
			global $buying_pts_payment_plan;
			return srp_check_is_array( $buying_pts_payment_plan ) ? round_off_type( array_sum( $buying_pts_payment_plan ) ) : 0;
		}

		/**
		 * Buying Points for Products
		 * */
		public static function buy_points_for_product_in_cart() {
			if ( 'earningonly' === check_banning_type( get_current_user_id() ) || 'both' === check_banning_type( get_current_user_id() ) ) {
				return array();
			}

			$points = array();
			foreach ( WC()->cart->cart_contents as $value ) {
				if ( 'yes' === block_points_for_salepriced_product( $value['product_id'], $value['variation_id'] ) ) {
					continue;
				}

				$ProductId = ! empty( $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];
				$Enable    = get_post_meta( $ProductId, '_rewardsystem_buying_reward_points', true );
				$Points    = get_post_meta( $ProductId, '_rewardsystem_assign_buying_points', true );
				$ProductId = ! empty( $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];

				if ( ( 'yes' === $Enable || '1' === $Enable ) && ! empty( $Points ) ) {
					$points[ $ProductId ] = $Points * $value['quantity'];
				}
			}
			return $points;
		}

		public static function msg_for_buying_points() {
			$payment_plan_buying_points = self::buying_point_msg_for_payment_plan_product();
			$buying_points              = self::buying_point_msg_for_product();

			if ( $payment_plan_buying_points ) {
				echo wp_kses_post( $payment_plan_buying_points );
			}

			if ( $buying_points ) {
				echo wp_kses_post( $buying_points );
			}
		}

		/* Display Message for Buying Points in Cart/Checkout for Product */

		public static function buying_point_msg_for_product() {
			$ShowBuyingPointMsg = is_cart() ? get_option( 'rs_show_hide_buy_point_message_for_each_products' ) : get_option( 'rs_show_hide_buy_point_message_for_each_products_checkout_page' );
			if ( 2 == $ShowBuyingPointMsg ) {
				return;
			}

			global $buyingmsg_global;
			if ( ! srp_check_is_array( $buyingmsg_global ) ) {
				return;
			}

			$ShowMsg = is_user_logged_in() ? ( ! check_if_coupon_applied() && ! check_if_discount_applied() ) : ( 'yes' == get_option( 'rs_enable_acc_creation_for_guest_checkout_page' ) && ( ! check_if_coupon_applied() && ! check_if_discount_applied() ) );
			if ( ! $ShowMsg ) {
				return;
			}
			?><div class="woocommerce-info sumo_reward_points_info_message rs_cart_message">
			<?php
			foreach ( $buyingmsg_global as $msg ) {
				echo do_shortcode( $msg );
			}
			?>
			</div>
			<?php
		}

		/* Assign Global Value($buying_pointsnew,$buyingmsg_global,$buying_pts_payment_plan) and Display Message for Buying Points (SUMO Payment Plan) */

		public static function buying_point_msg_for_payment_plan_product() {
			$BuyingPoint = self::buying_points_for_product_in_cart();
			if ( ! srp_check_is_array( $BuyingPoint ) ) {
				return;
			}

			global $buying_pointsnew;
			global $buyingmsg_global;
			global $buying_pts_payment_plan;
			global $producttitle;
			$buying_pointsnew = $BuyingPoint;
			foreach ( $BuyingPoint as $ProductId => $Points ) {
				if ( empty( $Points ) ) {
					continue;
				}

				$ProductObj = srp_product_object( $ProductId );
				if ( ! is_object( $ProductObj ) ) {
					continue;
				}

				if ( 'booking' === srp_product_type( $ProductId ) ) {
					continue;
				}

				$EnableBuyingPoint = get_post_meta( $ProductId, '_rewardsystem_buying_reward_points', true );
				$BuyPoints         = get_post_meta( $ProductId, '_rewardsystem_assign_buying_points', true );
				if ( 'yes' !== $EnableBuyingPoint && '1' !== $EnableBuyingPoint ) {
					continue;
				}

				if ( empty( $BuyPoints ) ) {
					continue;
				}

				$producttitle = $ProductId;
				if ( is_initial_payment( $ProductId ) ) {
					$ShowBuyingPointMsg      = is_cart() ? get_option( 'rs_show_hide_buy_point_message_for_each_payment_plan_products' ) : get_option( 'rs_show_hide_buy_point_message_for_each_payment_plan_products_checkout_page' );
					$BuyingPointMsg          = is_cart() ? get_option( 'rs_buy_point_message_payment_plan_product_in_cart' ) : get_option( 'rs_buy_point_message_payment_plan_product_in_checkout' );
					$buying_pts_payment_plan = array( $BuyPoints );

					if ( '1' === $ShowBuyingPointMsg ) {
						?>
						<div class="woocommerce-info rs_cart_message" ><?php echo do_shortcode( $BuyingPointMsg ); ?>  </div>
						<?php
					}
				} else {
					$BuyingPointMsg                 = is_cart() ? get_option( 'rs_buy_point_message_product_in_cart' ) : get_option( 'rs_buy_point_message_product_in_checkout' );
					$buyingmsg_global[ $ProductId ] = do_shortcode( $BuyingPointMsg ) . '<br>';
				}
			}
		}

		public static function buying_points_for_product_in_cart() {
			$BanType = check_banning_type( get_current_user_id() );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			global $buying_pointsnew;
			foreach ( WC()->cart->cart_contents as $value ) {
				$CheckIfSalePrice = block_points_for_salepriced_product( $value['product_id'], $value['variation_id'] );
				if ( 'yes' == $CheckIfSalePrice ) {
					continue;
				}

				$ProductId     = ! empty( $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];
				$buying_points = get_post_meta( $ProductId, '_rewardsystem_assign_buying_points', true );

				if ( ( 'yes' == get_post_meta( $ProductId, '_rewardsystem_buying_reward_points', true ) || 1 == get_post_meta( $ProductId, '_rewardsystem_buying_reward_points', true ) ) && ! empty( get_post_meta( $ProductId, '_rewardsystem_assign_buying_points', true ) ) ) {
					$buying_pointsnew[ $ProductId ] = $buying_points * $value['quantity'];
				}
			}
			return $buying_pointsnew;
		}
	}

	RSBuyingPointsFrontend::init();
}
