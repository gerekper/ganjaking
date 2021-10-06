<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSProductPurchaseFrontend' ) ) {

	class RSProductPurchaseFrontend {

		public static $data ;

		public static function init() {
			add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'save_points_info_in_order' ) , 10 , 2 ) ;

			if ( '1' == get_option( 'rs_message_before_after_cart_table' ) ) {
				if ( '1' == get_option( 'rs_reward_point_troubleshoot_before_cart' ) ) {
					add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'messages_and_validation_for_product_purcahse' ) ) ;
					add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'msg_for_cart_total_based_points' ) ) ;
					add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'msg_for_range_based_points_for_product_purcahse' ) ) ;
				} else {
					add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'messages_and_validation_for_product_purcahse' ) ) ;
					add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'msg_for_cart_total_based_points' ) ) ;
					add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'msg_for_range_based_points_for_product_purcahse' ) ) ;
				}
			} else {
				add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'messages_and_validation_for_product_purcahse' ) ) ;
				add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'msg_for_cart_total_based_points' ) ) ;
				add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'msg_for_range_based_points_for_product_purcahse' ) ) ;
			}
			add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'msg_for_range_based_points_for_product_purcahse' ) ) ;
			add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'msg_for_cart_total_based_points' ) ) ;
			add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'messages_and_validation_for_product_purcahse' ) ) ;
			add_action( 'woocommerce_after_calculate_totals' , array( __CLASS__ , 'set_range_based_rewards_points' ) ) ;
		}

		/* Save Points Detail in Order */

		public static function save_points_info_in_order( $order_id, $orderobj ) {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( 'yes' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
				$PointInfo = ( 'yes' == get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) ) ? RSFrontendAssets::modified_points_for_products() : RSFrontendAssets::original_points_for_product() ;
				if ( srp_check_is_array( $PointInfo ) ) {
					update_post_meta( $order_id , 'points_for_current_order' , $PointInfo ) ;
					$Points = array_sum( $PointInfo ) ;
					$Points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $Points ) ;
					update_post_meta( $order_id , 'rs_points_for_current_order_as_value' , $Points ) ;
				}
			} else {
				if ( '1' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					$PointInfo = ( 'yes' == get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) ) ? RSFrontendAssets::modified_points_for_products() : RSFrontendAssets::original_points_for_product() ;
					if ( srp_check_is_array( $PointInfo ) ) {
						update_post_meta( $order_id , 'points_for_current_order' , $PointInfo ) ;
						$Points = array_sum( $PointInfo ) ;
						$Points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $Points ) ;
						update_post_meta( $order_id , 'rs_points_for_current_order_as_value' , $Points ) ;
					}
				} else if ( '2' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					$Order           = new WC_Order( $order_id ) ;
					$CartTotalPoints = get_reward_points_based_on_cart_total( $Order->get_total() ) ;
					$CartTotalPoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $CartTotalPoints ) ;
					if ( ! empty( $CartTotalPoints ) ) {
						update_post_meta( $order_id , 'points_for_current_order_based_on_cart_total' , $CartTotalPoints ) ;
					}
				} else {
					$range_points = self::get_reward_point_for_range_based_type( $order_id ) ;
					if ( ! empty( $range_points ) ) {
						update_post_meta( $order_id , 'rs_points_for_current_order_based_on_range' , $range_points ) ;
					}
				}
			}
			/* First Purchase Point Meta Update. */
			$first_purchase = rs_get_first_purchase_point() ;
			if ( ! empty( $first_purchase ) ) {
				update_post_meta( $order_id , 'rs_first_purchase_point_for_order' , $first_purchase ) ;
			}
			update_post_meta( $order_id , 'frontendorder' , 1 ) ;
		}

		public static function messages_and_validation_for_product_purcahse() {
			echo wp_kses_post(self::first_purchase_message_for_product_purcahse() );
			if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
				return ;
			}

			global $totalrewardpoints ;
			echo wp_kses_post(self::min_and_max_cart_total_validation() );
			echo wp_kses_post(self::purchase_msg_for_payment_plan_product() );
			echo wp_kses_post(self::purchase_msg_for_each_product()) ;
			$totalrewardpoints = global_variable_points() ;
			WC()->session->set( 'rewardpoints' , $totalrewardpoints ) ;
		}

		/* Assign Global Value($totalrewardpointsnew) and Min/Max Validation Cart Total Limitation */

		public static function min_and_max_cart_total_validation() {
			$CartTotal = WC()->cart->total ;
			if ( empty( $CartTotal ) ) {
				return ;
			}

			$PointsInfo = ( 'yes' == get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) ) ? RSFrontendAssets::modified_points_for_products() : RSFrontendAssets::original_points_for_product() ;
			if ( ! srp_check_is_array( $PointsInfo ) ) {
				return ;
			}

			$MinCartTotal    = get_option( 'rs_minimum_cart_total_for_earning' ) ;
			$MaxCartTotal    = get_option( 'rs_maximum_cart_total_for_earning' ) ;
			$MinCartTotalErr = str_replace( '[carttotal]' , $MinCartTotal , get_option( 'rs_min_cart_total_for_earning_error_message' ) ) ;
			$MaxCartTotalErr = str_replace( '[carttotal]' , $MaxCartTotal , get_option( 'rs_max_cart_total_for_earning_error_message' ) ) ;
			global $totalrewardpointsnew ;
			if ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
				if ( $CartTotal >= $MinCartTotal && $CartTotal <= $MaxCartTotal ) {
					$totalrewardpointsnew = $PointsInfo ;
				} elseif ( $CartTotal <= $MinCartTotal ) {
					if ( '1' == get_option( 'rs_show_hide_minimum_cart_total_earn_error_message' ) ) {
						?>
						<div class="woocommerce-error" ><?php echo wp_kses_post($MinCartTotalErr) ; ?></div>
						<?php
					}
				} elseif ( $CartTotal >= $MaxCartTotal ) {
					if ( '1' == get_option( 'rs_show_hide_maximum_cart_total_earn_error_message' ) ) {
						?>
						<div class="woocommerce-error" ><?php echo wp_kses_post($MaxCartTotalErr) ; ?></div>
						<?php
					}
				}
			} else if ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
				if ( $CartTotal >= $MinCartTotal ) {
					$totalrewardpointsnew = $PointsInfo ;
				} else {
					if ( '1' == get_option( 'rs_show_hide_minimum_cart_total_earn_error_message' ) ) {
						?>
						<div class="woocommerce-error" ><?php echo wp_kses_post($MinCartTotalErr) ; ?></div>
						<?php
					}
				}
			} else if ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
				if ( $CartTotal <= $MaxCartTotal ) {
					$totalrewardpointsnew = $PointsInfo ;
				} else {
					if ( '1' == get_option( 'rs_show_hide_maximum_cart_total_earn_error_message' ) ) {
						?>
						<div class="woocommerce-error" ><?php echo wp_kses_post($MaxCartTotalErr) ; ?></div>
						<?php
					}
				}
			} else if ( empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
				$totalrewardpointsnew = $PointsInfo ;
			} else {
				$totalrewardpointsnew = '' ;
			}
		}

		/* Display Product Purchase message in Cart/Checkout for Product */

		public static function purchase_msg_for_each_product() {
			$ShowPurchaseMsg = is_cart() ? get_option( 'rs_show_hide_message_for_each_products' ) : get_option( 'rs_show_hide_message_for_each_products_checkout_page' ) ;
			if ( 2 == $ShowPurchaseMsg ) {
				return ;
			}

			global $totalrewardpointsnew ;
			global $messageglobal ;
			if ( ! srp_check_is_array( $totalrewardpointsnew ) ) {
				return ;
			}

			if ( ! srp_check_is_array( $messageglobal ) ) {
				return ;
			}

			if ( 0 == array_sum( $totalrewardpointsnew ) ) {
				return ;
			}

			$ShowMsg = is_user_logged_in() ? ( ! check_if_coupon_applied() && ! check_if_discount_applied() ) : ( 'yes' == get_option( 'rs_enable_acc_creation_for_guest_checkout_page' ) && ( ! check_if_coupon_applied() && ! check_if_discount_applied() ) ) ;
			if ( ! $ShowMsg ) {
				return ;
			}

			if ( ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled() ) {
				return ;
			}

			?>
			<div class="woocommerce-info sumo_reward_points_info_message rs_cart_message">
				<?php echo wp_kses_post(implode( '' , $messageglobal )) ; ?>
			</div>
			<?php
		}

		/* Assign Global Value($messageglobal) and Display Product Purchase Message for SUMO Payment Plan Product */

		public static function purchase_msg_for_payment_plan_product() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			$PointsInfo = ( 'yes' == get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) ) ? RSFrontendAssets::modified_points_for_products() : RSFrontendAssets::original_points_for_product() ;
			if ( ! srp_check_is_array( $PointsInfo ) ) {
				return ;
			}

			global $totalrewardpoints ;
			global $messageglobal ;
			global $totalrewardpoints_payment_plan ;
			global $producttitle ;
			foreach ( $PointsInfo as $ProductId => $Points ) {
				if ( empty( $Points ) ) {
					continue ;
				}

				$ProductObj = srp_product_object( $ProductId ) ;
				if ( ! is_object( $ProductObj ) ) {
					continue ;
				}

				if ( srp_product_type( $ProductId ) == 'booking' ) {
					continue ;
				}

				$producttitle      = $ProductId ;
				$totalrewardpoints = $Points ;

				if ( is_initial_payment( $ProductId ) ) {
					$ShowPaymentPlanMsg = is_cart() ? get_option( 'rs_show_hide_message_for_each_payment_plan_products' ) : get_option( 'rs_show_hide_message_for_each_payment_plan_products_checkout_page' ) ;
					$PaymentPlanMsg     = is_cart() ? get_option( 'rs_message_payment_plan_product_in_cart' ) : get_option( 'rs_message_payment_plan_product_in_checkout' ) ;

					$totalrewardpoints_payment_plan = array( $Points ) ;
					if ( '1' == $ShowPaymentPlanMsg ) {
						?>
						<div class="woocommerce-info rs_cart_message" ><?php echo wp_kses_post(do_shortcode( $PaymentPlanMsg )) ; ?></div>
						<?php
					}
				} else {
					$ProductMsg                  = is_cart() ? get_option( 'rs_message_product_in_cart' ) : get_option( 'rs_message_product_in_checkout' ) ;
					$messageglobal[ $ProductId ] = do_shortcode( $ProductMsg ) . '<br>' ;
				}
			}
		}

		public static function msg_for_range_based_points_for_product_purcahse() {

			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( 'yes' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
				return ;
			}

			if ( '3' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
				return ;
			}

			$showmsg = is_cart() ? get_option( 'rs_enable_msg_for_range_based_points_in_cart' , '1' ) : get_option( 'rs_enable_msg_for_range_based_points_in_checkout' , 1 ) ;
			if ( '2' == $showmsg ) {
				return ;
			}

			$check_coupon_applied = check_if_coupon_applied() ;
			if ( $check_coupon_applied ) {
				return ;
			}

			$check_if_discount_applied = check_if_discount_applied() ;
			if ( $check_if_discount_applied ) {
				return ;
			}

			$range_based_point = self::get_reward_point_for_range_based_type() ;
			if ( empty( $range_based_point ) ) {
				return ;
			}

			if ( ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled() ) {
				return ;
			}

			$cart_message        = get_option( 'rs_msg_for_range_based_points' , 'Complete this order and Earn <strong>[rangebasedrewardpoints]</strong> Reward Points([equalvalueforrangebased])' ) ;
			$checkout_message    = get_option( 'rs_msg_for_range_based_points_in_checkout' , 'Complete this order and Earn <strong>[rangebasedrewardpoints]</strong> Reward Points([equalvalueforrangebased])' ) ;
			$msg                 = is_cart() ? $cart_message : $checkout_message ;
			$msgtodisplay        = str_replace( '[rangebasedrewardpoints]' , $range_based_point , $msg ) ;
			$convertionrate      = redeem_point_conversion( $range_based_point , get_current_user_id() , 'price' ) ;
			$currencyvalue       = round_off_type_for_currency( $convertionrate ) ;
			$currencyreplacedmsg = str_replace( '[equalvalueforrangebased]' , wc_price( $currencyvalue ) , $msgtodisplay )
			?>
			<div class="woocommerce-info rs_msg_for_range_based_points">
				<?php echo wp_kses_post($currencyreplacedmsg) ; ?>
			</div>
			<?php
		}

		public static function msg_for_cart_total_based_points() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( 'yes' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
				return ;
			}

			if ( '2' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
				return ;
			}

			if ( '2' == get_option( 'rs_enable_cart_total_reward_points' ) ) {
				return ;
			}

			$ShowMsg = is_cart() ? get_option( 'rs_enable_msg_for_cart_total_based_points' ) : get_option( 'rs_enable_msg_for_cart_total_based_points_in_checkout' ) ;
			if ( '2' == $ShowMsg ) {
				return ;
			}

			$check_coupon_applied = check_if_coupon_applied() ;
			if ( $check_coupon_applied ) {
				return ;
			}

			$check_if_discount_applied = check_if_discount_applied() ;
			if ( $check_if_discount_applied ) {
				return ;
			}

			if ( ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled() ) {
				return ;
			}

			$PointForCartTotal = get_reward_points_based_on_cart_total( WC()->cart->total ) ;
			$PointToReturn     = round_off_type( $PointForCartTotal , array() , false ) ;
			$PointToReturn     = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $PointToReturn ) ;

			if ( empty( $PointToReturn ) ) {
				return ;
			}

			$Msg                 = is_cart() ? get_option( 'rs_msg_for_cart_total_based_points' ) : get_option( 'rs_msg_for_cart_total_based_points_in_checkout' ) ;
			$MsgToDisplay        = str_replace( '[carttotalbasedrewardpoints]' , $PointToReturn , $Msg ) ;
			$ConvertionRate      = redeem_point_conversion( $PointToReturn , get_current_user_id() , 'price' ) ;
			$CurrencyValue       = round_off_type_for_currency( $ConvertionRate ) ;
			$CurrencyReplacedMsg = str_replace( '[equalvalueforcarttotal]' , wc_price( $CurrencyValue ) , $MsgToDisplay )
			?>
			<div class="woocommerce-info rs_msg_for_cart_total_based_points">
				<?php echo wp_kses_post($CurrencyReplacedMsg) ; ?>
			</div>
			<?php
		}

		public static function first_purchase_message_for_product_purcahse() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( 'yes' != get_option( 'rs_enable_first_purchase_reward_points' ) ) {
				return ;
			}

			if ( '2' == get_option( 'rs_show_hide_message_for_first_purchase_points' ) ) {
				return ;
			}

			$OrderCount = get_posts( array(
				'numberposts' => -1 ,
				'meta_key'    => '_customer_user' ,
				'meta_value'  => get_current_user_id() ,
				'post_type'   => wc_get_order_types() ,
				'post_status' => array('wc-pending', 'wc-processing' , 'wc-on-hold' , 'wc-completed' ) ,
					) ) ;
			if ( count( $OrderCount ) > 0 ) {
				return ;
			}
			
			if ( empty( rs_get_first_purchase_point() ) && ! rs_validate_first_purchase_point() ) {
				wc_print_notice( str_replace( '[order_total_value]', srp_formatted_price( round_off_type_for_currency(get_option( 'rs_min_total_for_first_purchase' , '0' ))), get_option( 'rs_minimum_order_total_for_first_purchase_error' , 'Minimum Order Total required to Earn First Purchase Point is [order_total_value]' ) ), 'error' ) ;
				return ;
			}
			 
			?>
			<div class="woocommerce-info sumo_reward_points_payment_plan_complete_message rs_cart_message">
				<?php
				echo wp_kses_post(do_shortcode( get_option( 'rs_message_for_first_purchase' ) )) ;
				?>
			</div>
			<?php
		}

		/**
		 * Set Range Based Reward Points.
		 * */
		public static function set_range_based_rewards_points() {
			self::$data = self::get_data_based_on_range() ;
		}

		/**
		 * Get Range Based Data.
		 *
		 * @return array.
		 * */
		public static function get_data_based_on_range() {

			$range_value = get_option( 'rs_range_based_points' , '' ) ;
			if ( ! srp_check_is_array( $range_value ) ) {
				return array();
			}

			$cart_total = WC()->cart->total ;
			$data       = get_option( 'rs_range_based_points' ) ;
			foreach ( $range_value as $key => $range ) {

				// Validate rule based on date.
				if ( ! self::validate_rule_based_on_date( $range ) ) {
					unset( $data[ $key ] ) ;
					continue ;
				}

				$min_value = isset( $range[ 'min_value' ] ) ? $range[ 'min_value' ] : '' ;
				$max_value = isset( $range[ 'max_value' ] ) ? $range[ 'max_value' ] : '' ;
				if ( ( $min_value <= $cart_total ) && ( $max_value >= $cart_total ) ) {
					$data[ $key ] = $range ;
				} else {
					unset( $data[ $key ] ) ;
				}
			}
			$matched_data = array_values(array_filter( $data )) ;
			if ( empty( $matched_data ) ) {
				return array() ;
			}

			$rule_priority = get_option( 'rs_range_based_rule_priority' , '1' ) ;
			if ( '1' == $rule_priority ) {
				// First Matched Rule.
				$rule = reset( $matched_data ) ;
			} else if ( '2' ==  $rule_priority ) {
				// Last Matched Rule.
				$rule = end( $matched_data ) ;
			} else if ('3'  ==  $rule_priority ) {
				// Minimum points Matched Rule.
				$rule_key  = array_search( min( array_column( $matched_data , 'reward_points' ) ) , array_column( $matched_data , 'reward_points' ) ) ;
				$rule      = isset($matched_data[ $rule_key ] ) ? $matched_data[ $rule_key ] :array();
			} else {
				// Maximum points Matched Rule.
				$rule_key  = array_search( max( array_column( $matched_data , 'reward_points' ) ) , array_column( $matched_data , 'reward_points' ) ) ;
				$rule      = isset($matched_data[ $rule_key ] ) ? $matched_data[ $rule_key ] :array();
			}

			return $rule ;
		}

		/**
		 * Get Range Data.
		 *
		 * @return array.
		 * */
		public static function get_range_data() {
			if ( isset( self::$data ) ) {
				return self::$data ;
			}
			self::set_range_based_rewards_points() ;
			return self::$data ;
		}

		/**
		 * Validate rule based on date
		 *
		 * @return bool.
		 * */
		public static function validate_rule_based_on_date( $rule_data ) {

			$from_date_in_time = isset( $rule_data[ 'from_date' ] ) ? strtotime( $rule_data[ 'from_date' ] ) : '' ;
			$to_date_in_time   = isset( $rule_data[ 'to_date' ] ) ? strtotime( $rule_data[ 'to_date' ] ) : '' ;

			if ( ! $from_date_in_time && ! $to_date_in_time ) {
				return true ;
			}
			$current_time = strtotime( gmdate( 'Y-m-d' ) ) ;
			if ( $from_date_in_time <= $current_time && $to_date_in_time >= $current_time ) {
				return true ;
			}

			if ( ( ! $from_date_in_time && $to_date_in_time ) && $to_date_in_time >= $current_time ) {
				return true ;
			}

			if ( ( ! $to_date_in_time && $from_date_in_time ) && $from_date_in_time <= $current_time ) {
				return true ;
			}

			return false ;
		}

		/**
		 * Get Reward point for range based type.
		 *
		 * @return int.
		 * */
		public static function get_reward_point_for_range_based_type( $order_id = false ) {

			$range_data = self::get_range_data() ;
			if ( ! srp_check_is_array( $range_data ) ) {
				return 0 ;
			}

			$reward_type  = isset( $range_data[ 'type' ] ) ? $range_data[ 'type' ] : 1 ;
			$reward_point = isset( $range_data[ 'reward_points' ] ) ? $range_data[ 'reward_points' ] : 0 ;
			if ( ! $reward_point ) {
				return 0 ;
			}

			$total   = 0 ;
			$user_id = get_current_user_id() ;
			if ( $order_id ) {
				$order = new WC_Order( $order_id ) ;
				if ( ! is_object( $order ) ) {
					return 0 ;
				}

				$shipping_cost = $order->shipping_total + $order->shipping_tax ;
				$total         = 'yes' == get_option( 'rs_exclude_shipping_cost_based_on_cart_total' ) ? $order->get_total() - $shipping_cost : $order->get_total() ;
			} else {
				$shipping_cost = WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax() ;
				$total         = 'yes' == get_option( 'rs_exclude_shipping_cost_based_on_cart_total' ) ? WC()->cart->total - $shipping_cost : WC()->cart->total ;
			}

			$range_based_point = ( 1 == $reward_type ) ? $reward_point : $total * ( float ) $reward_point / 100 ;
			$point             = RSMemberFunction::earn_points_percentage( $user_id , ( float ) $range_based_point ) ;

			return $point ;
		}
	}

	RSProductPurchaseFrontend::init() ;
}
