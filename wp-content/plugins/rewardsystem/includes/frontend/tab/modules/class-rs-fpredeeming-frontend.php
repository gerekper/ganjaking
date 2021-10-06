<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSRedeemingFrontend' ) ) {

	class RSRedeemingFrontend {

		public static function init() {

			add_action( 'wp' , array( __CLASS__ , 'redeem_points_for_user_automatically' ) ) ;

			add_action( 'wp_head' , array( __CLASS__ , 'redeem_point_for_user' ) ) ;

			// Trash Redeeming Coupons when cart is empty.
			add_action( 'woocommerce_cart_is_empty' , array( __CLASS__ , 'trash_sumo_coupon_if_cart_empty' ) , 10 ) ;
			// Trash Redeeming Coupons when coupon is removed .
			add_action( 'woocommerce_removed_coupon' , array( __CLASS__ , 'trash_sumo_coupon_is_removed' ) , 10 , 1 ) ;

			add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'trash_sumo_coupon_if_order_placed' ) , 10 , 2 ) ;

			add_action( 'rs_delete_coupon_based_on_cron' , array( __CLASS__ , 'trash_sumo_coupon_based_on_cron_time' ) , 10 , 1 ) ;

			add_action( 'woocommerce_removed_coupon' , array( __CLASS__ , 'unset_session' ) ) ;

			add_action( 'woocommerce_after_calculate_totals' , array( __CLASS__ , 'trash_sumo_coupon_if_restricted' ) , 10 , 1 ) ;

			add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'validation_for_redeeming' ) ) ;

			add_action( 'woocommerce_after_cart_totals' , array( __CLASS__ , 'redeem_field_based_on_settings' ) ) ;

			add_action( 'woocommerce_after_checkout_form' , array( __CLASS__ , 'redeem_field_based_on_settings' ) ) ;

			if ( get_option( 'rs_reward_point_troubleshoot_after_cart' ) == '1' ) {
				add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'default_redeem_field_in_cart_and_checkout' ) ) ;
			} elseif ( get_option( 'rs_reward_point_troubleshoot_after_cart' ) == '2' ) {
				add_action( 'woocommerce_cart_coupon' , array( __CLASS__ , 'default_redeem_field_in_cart_and_checkout' ) ) ;
			} else {
				add_action( 'woocommerce_cart_actions' , array( __CLASS__ , 'default_redeem_field_in_cart_and_checkout' ) ) ;
			}

			add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'default_redeem_field_in_cart_and_checkout' ) , get_option('rs_redeeming_field_hook_priority_in_checkout', 10) ) ;

			add_filter( 'woocommerce_cart_item_removed_title' , array( __CLASS__ , 'update_coupon_amount' ) , 10 , 1 ) ;

			add_filter( 'woocommerce_update_cart_action_cart_updated' , array( __CLASS__ , 'update_coupon_amount' ) , 10 , 1 ) ;

			if ( '1' == get_option( 'rs_message_before_after_cart_table' ) ) {
				if ( '1' == get_option( 'rs_reward_point_troubleshoot_before_cart' ) ) {
					add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'messages_for_redeeming' ) ) ;
				} else {
					add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'messages_for_redeeming' ) ) ;
				}
			} else {
				add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'messages_for_redeeming' ) ) ;
			}
			add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'messages_for_redeeming' ) ) ;

			add_filter( 'woocommerce_cart_totals_coupon_label' , array( __CLASS__ , 'change_coupon_label' ) , get_option( 'rs_change_coupon_priority_value' , 1 ) , 2 ) ;

			add_filter( 'woocommerce_coupons_enabled' , array( __CLASS__ , 'hide_coupon_field_on_checkout' ) ) ;

			add_filter( 'woocommerce_checkout_coupon_message' , array( __CLASS__ , 'hide_coupon_message' ) ) ;

			add_filter( 'woocommerce_coupon_error' , array( __CLASS__ , 'error_message_for_sumo_coupon' ) , 10 , 3 ) ;

			add_filter( 'woocommerce_coupon_message' , array( __CLASS__ , 'success_message_for_sumo_coupon' ) , 10 , 3 ) ;

			add_filter( 'woocommerce_add_message' , array( __CLASS__ , 'replace_msg_for_remove_coupon' ) , 10 , 1 ) ;

			add_filter( 'woocommerce_available_payment_gateways' , array( __CLASS__ , 'unset_gateways_for_excluded_product_to_redeem' ) , 10 , 1 ) ;
			
			add_action( 'woocommerce_after_checkout_validation', array( __CLASS__, 'validate_redeeming_for_specific_gateways' ), 11, 2 ) ;
		}

		/*
		 * Trash Sumo Coupons.
		 * */

		public static function trash_sumo_coupon( $cartobj ) {

			self::trash_sumo_coupon_if_restricted( $cartobj ) ;

			self::trash_sumo_coupon_if_restricted_based_on_available_points( $cartobj ) ;
		}

		/*
		 * Trash Sumo Coupons when it is removed.
		 * */

		public static function trash_sumo_coupon_is_removed( $coupon_code ) {

			$UserInfo = get_user_by( 'id' , get_current_user_id() ) ;
			if ( ! is_object( $UserInfo ) ) {
				return ;
			}
			
			// Check ajax referer when remove coupon button is clicked.
			if ( ! check_ajax_referer( 'remove-coupon' , 'security' , false ) ) {
				return ;
			}

			$Redeem     = 'sumo_' . strtolower( "$UserInfo->user_login" ) ;
			$AutoRedeem = 'auto_redeem_' . strtolower( "$UserInfo->user_login" ) ;

			if ( $Redeem == $coupon_code || $AutoRedeem == $coupon_code ) {
				$coupon = new WC_Coupon( $coupon_code ) ;
				if ( is_object( $coupon ) && $coupon->get_id() ) {
					wp_trash_post( $coupon->get_id() ) ;
				}
			}
		}

		/*
		 *  Trash SUMO Coupons if Cart is Empty.
		 * */

		public static function trash_sumo_coupon_if_cart_empty() {

			if ( ! WC()->cart->is_empty() ) {
				return ;
			}

			$CouponId = get_user_meta( get_current_user_id() , 'redeemcouponids' , true ) ;
			if ( ! empty( $CouponId ) ) {
				wp_trash_post( $CouponId ) ;
			}

			$CouponId = get_user_meta( get_current_user_id() , 'auto_redeemcoupon_ids' , true ) ;
			if ( ! empty( $CouponId ) ) {
				wp_trash_post( $CouponId ) ;
			}
		}

		/* Trash SUMO Coupon when it satisfies the Reward Restriction */

		public static function trash_sumo_coupon_if_restricted( $CartObj ) {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			$MinCartTotalToRedeem = ( float ) get_option( 'rs_minimum_cart_total_points' ) ;
			$MaxCartTotalToRedeem = ( float ) get_option( 'rs_maximum_cart_total_points' ) ;
			if ( empty( $MinCartTotalToRedeem ) && empty( $MaxCartTotalToRedeem ) ) {
				return ;
			}

			$CartSubtotal = srp_cart_subtotal() ;
			$CouponId     = 0 ;
			$UserInfo     = get_user_by( 'id' , get_current_user_id() ) ;
			$Username     = $UserInfo->user_login ;
			$Redeem       = 'sumo_' . strtolower( "$Username" ) ;
			$AutoRedeem   = 'auto_redeem_' . strtolower( "$Username" ) ;
			foreach ( $CartObj->get_applied_coupons() as $CouponCode ) {
				if ( $CouponCode == $Redeem ) {
					$CouponId = get_user_meta( get_current_user_id() , 'redeemcouponids' , true ) ;
				} else if ( $CouponCode == $AutoRedeem ) {
					$CouponId = get_user_meta( get_current_user_id() , 'auto_redeemcoupon_ids' , true ) ;
				}
				if ( ( ! empty( $MinCartTotalToRedeem ) && $CartSubtotal < $MinCartTotalToRedeem ) || ( ! empty( $MaxCartTotalToRedeem ) && $CartSubtotal > $MaxCartTotalToRedeem ) ) {
					if ( ! empty( $CouponId ) ) {
						wp_trash_post( $CouponId ) ;
					}
				}
			}
		}

		/* Trash SUMO Coupon if restricted based on available points */

		public static function trash_sumo_coupon_if_restricted_based_on_available_points( $cartobj ) {

			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( ! is_object( $cartobj ) || ! srp_check_is_array( $cartobj->get_applied_coupons() ) ) {
				return ;
			}
			
			$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction();
			if ( ! $minimum_available_points ) {
				return ;
			}

			$pointsdata = new RS_Points_Data( get_current_user_id() ) ;
			if ( ! is_object( $pointsdata ) ) {
				return ;
			}

			$points   = $pointsdata->total_available_points() ;
			$couponid = 0 ;
			$userinfo = get_user_by( 'id' , get_current_user_id() ) ;
			if ( ! is_object( $userinfo ) ) {
				return ;
			}

			$username   = $userinfo->user_login ;
			$redeem     = 'sumo_' . strtolower( "$username" ) ;
			$autoredeem = 'auto_redeem_' . strtolower( "$username" ) ;
			$coupon_id  = '' ;
			foreach ( $cartobj->get_applied_coupons() as $couponcode ) {
				$coupon    = new WC_Coupon( $couponcode ) ;
				$coupon_id = is_object( $coupon ) ? $coupon->id : '' ;
			}

			if ( $coupon_id && $points < $minimum_available_points ) {
				wp_trash_post( $coupon_id ) ;
			}
		}

		/* Trash SUMO Coupon when Order Placed */

		public static function trash_sumo_coupon_if_order_placed( $OrderId, $order_post ) {
			$order    = new WC_Order( $OrderId ) ;
			$OrderObj = srp_order_obj( $order ) ;
			$UserId   = $OrderObj[ 'order_userid' ] ;
			if ( empty( $UserId ) ) {
				return ;
			}

			$UserInfo     = get_user_by( 'id' , $UserId ) ;
			$UserName     = $UserInfo->user_login ;
			$Redeem       = 'sumo_' . strtolower( $UserName ) ;
			$AutoRedeem   = 'auto_redeem_' . strtolower( $UserName ) ;
			$group        = 'coupons' ;
			$used_coupons = ( float ) WC()->version < ( float ) ( '3.7' ) ? $order->get_used_coupons() : $order->get_coupon_codes() ;
			if ( ! ( in_array( $Redeem , $used_coupons ) || in_array( $AutoRedeem , $used_coupons ) ) ) {
				update_post_meta( $OrderId , 'rs_check_enable_option_for_redeeming' , 'no' ) ;
				return ;
			}

			foreach ( $used_coupons as $CouponCode ) {
				$CouponId   = ( $Redeem == $CouponCode ) ? get_user_meta( $UserId , 'redeemcouponids' , true ) : get_user_meta( $UserId , 'auto_redeemcoupon_ids' , true ) ;
				$CouponName = ( $Redeem == $CouponCode ) ? $Redeem : $AutoRedeem ;
				if ( empty( $CouponId ) ) {
					continue ;
				}

				if ( get_option( '_rs_restrict_coupon' ) == '1' || get_option( '_rs_enable_coupon_restriction' ) == 'no' ) {
					wp_trash_post( $CouponId ) ;
				} else {
					self::schedule_cron_to_trash_sumo_coupon( $CouponId ) ;
				}
				if ( class_exists( 'WC_Cache_Helper' ) ) {
					wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_' . $CouponName , 'coupons' ) ;
				}
			}
			$EnableRedeem = ( in_array( $Redeem , $used_coupons ) || in_array( $AutoRedeem , $used_coupons ) ) ? get_option( 'rs_enable_redeem_for_order' ) : ( srp_check_is_array( $used_coupons ) ? get_option( 'rs_disable_point_if_coupon' ) : 'no' ) ;
			update_post_meta( $OrderId , 'rs_check_enable_option_for_redeeming' , $EnableRedeem ) ;
		}

		public static function schedule_cron_to_trash_sumo_coupon( $CouponId ) {
			$time = get_option( 'rs_delete_coupon_specific_time' ) ;
			if ( empty( $time ) ) {
				return ;
			}

			$CouponId = array( 'rs_coupon_id' => $CouponId ) ;
			if ( '1' == get_option( 'rs_delete_coupon_by_cron_time' ) ) {
				$NextScheduleTime = time() + ( 24 * 60 * 60 * $time ) ;
			} elseif ( '2' == get_option( 'rs_delete_coupon_by_cron_time' ) ) {
				$NextScheduleTime = time() + ( 60 * 60 * $time ) ;
			} else {
				$NextScheduleTime = time() + ( 60 * $time ) ;
			}
			if ( false == wp_next_scheduled( $NextScheduleTime , 'rs_delete_coupon_based_on_cron' ) ) {
				wp_schedule_single_event( $NextScheduleTime , 'rs_delete_coupon_based_on_cron' , $CouponId ) ;
			}
		}

		/* Trash SUMO Coupon when Cron time Reached */

		public static function trash_sumo_coupon_based_on_cron_time( $CouponId ) {
			wp_trash_post( $CouponId ) ;
		}

		/* Validate Redeeming in Cart/Checkout */

		public static function validation_for_redeeming() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			$BanningType = check_banning_type( get_current_user_id() ) ;
			if ( 'redeemingonly' == $BanningType || 'both' == $BanningType ) {
				return ;
			}

			$CartSubtotal = srp_cart_subtotal() ;
			if ( empty( $CartSubtotal ) ) {
				return ;
			}

			if ( check_if_pointprice_product_exist_in_cart() ) {
				return ;
			}

			if ( 2 == get_option( 'rs_redeem_field_type_option' ) ) {
				return ;
			}

			$MemRestrict = 'no' ;
			if ( 'yes' == get_option( 'rs_restrict_redeem_when_no_membership_plan' ) && function_exists( 'check_plan_exists' ) ) {
				$MemRestrict = check_plan_exists( get_current_user_id() ) ? 'no' : 'yes' ;
			}

			if ( 'yes' == $MemRestrict ) {
				return ;
			}

			$pointsdata       = new RS_Points_Data( get_current_user_id() ) ;
			$points           = $pointsdata->total_available_points() ;
			$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction();
			if ( $minimum_available_points && $points < $minimum_available_points ) {
				return ;
			}

			$MinCartTotalToRedeem = get_option( 'rs_minimum_cart_total_points' ) ;
			$MaxCartTotalToRedeem = get_option( 'rs_maximum_cart_total_points' ) ;
			if ( ! empty( $MinCartTotalToRedeem ) && ! empty( $MaxCartTotalToRedeem ) ) {
				if ( $CartSubtotal < $MinCartTotalToRedeem && $CartSubtotal > $MaxCartTotalToRedeem ) {
					if ( '1' == get_option( 'rs_show_hide_maximum_cart_total_error_message' ) ) {
						$CurrencyValue = srp_formatted_price( round_off_type_for_currency( $MinCartTotalToRedeem ) ) ;
						$ReplacedMsg   = str_replace( '[carttotal]' , $CurrencyValue , get_option( 'rs_min_cart_total_redeem_error' ) ) ;
						$ReplacedMsg   = str_replace( '[currencysymbol]' , '' , $ReplacedMsg ) ;
						?>
						<div class="woocommerce-error"><?php echo wp_kses_post($ReplacedMsg) ; ?></div>
						<?php
					}
				}
			} else if ( ! empty( $MinCartTotalToRedeem ) && empty( $MaxCartTotalToRedeem ) ) {
				if ( $CartSubtotal < $MinCartTotalToRedeem ) {
					if ( '1' == get_option( 'rs_show_hide_minimum_cart_total_error_message' ) ) {
						$CurrencyValue = srp_formatted_price( round_off_type_for_currency( $MinCartTotalToRedeem ) ) ;
						$ReplacedMsg   = str_replace( '[carttotal]' , $CurrencyValue , get_option( 'rs_min_cart_total_redeem_error' ) ) ;
						$ReplacedMsg   = str_replace( '[currencysymbol]' , '' , $ReplacedMsg ) ;
						?>
						<div class="woocommerce-error"><?php echo wp_kses_post($ReplacedMsg) ; ?></div>
						<?php
					}
				}
			} else if ( empty( $MinCartTotalToRedeem ) && ! empty( $MaxCartTotalToRedeem ) ) {
				if ( $CartSubtotal > $MaxCartTotalToRedeem ) {
					if ( '1' == get_option( 'rs_show_hide_maximum_cart_total_error_message' ) ) {
						$CurrencyValue = srp_formatted_price( round_off_type_for_currency( $MaxCartTotalToRedeem ) ) ;
						$ReplacedMsg   = str_replace( '[carttotal]' , $CurrencyValue , get_option( 'rs_max_cart_total_redeem_error' ) ) ;
						$ReplacedMsg   = str_replace( '[currencysymbol]' , '' , $ReplacedMsg ) ;
						?>
						<div class="woocommerce-error"><?php echo wp_kses_post($ReplacedMsg) ; ?></div>
						<?php
					}
				}
			}
		}

		/* Default Redeem Field in Cart/Checkout */

		public static function default_redeem_field_in_cart_and_checkout() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			$ShowRedeemField = is_cart() ? get_option( 'rs_redeem_field_type_option' ) : get_option( 'rs_redeem_field_type_option_checkout' ) ;
			if ( 2 == $ShowRedeemField ) {
				return ;
			}

			$MemRestrict = 'no' ;
			if ( 'yes' == get_option( 'rs_restrict_redeem_when_no_membership_plan' ) && function_exists( 'check_plan_exists' ) ) {
				$MemRestrict = check_plan_exists( get_current_user_id() ) ? 'no' : 'yes' ;
			}

			if ( 'yes' == $MemRestrict ) {
				return ;
			}

			$MinCartTotal = get_option( 'rs_minimum_cart_total_points' ) ;
			$MaxCartTotal = get_option( 'rs_maximum_cart_total_points' ) ;
			$CartSubTotal = srp_cart_subtotal() ;
			if ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
				if ( $CartSubTotal >= $MinCartTotal && $CartSubTotal <= $MaxCartTotal ) {
					self::default_redeem_field() ;
				}
			} else if ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
				if ( $CartSubTotal >= $MinCartTotal ) {
					self::default_redeem_field() ;
				}
			} else if ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
				if ( $CartSubTotal <= $MaxCartTotal ) {
					self::default_redeem_field() ;
				}
			} else if ( empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
				self::default_redeem_field() ;
			}
		}

		public static function default_redeem_field() {
			if ( ! self::product_filter_for_redeem_field() ) {
				return ;
			}

			if ( check_if_pointprice_product_exist_in_cart() ) {
				return ;
			}

			$UserId  = get_current_user_id() ;
			$BanType = check_banning_type( $UserId ) ;
			if ( 'redeemingonly' == $BanType || 'both' == $BanType ) {
				return ;
			}

			$PointPriceValue = array() ;
			$PointPriceType  = array() ;
			$PointsData      = new RS_Points_Data( $UserId ) ;
			$Points          = $PointsData->total_available_points() ;
			$UserInfo        = get_user_by( 'id' , $UserId ) ;
			$user_role       = is_object( $UserInfo ) ? $UserInfo->roles : array() ;
			$user_role       = implode( '' , $user_role ) ;
			$Username        = $UserInfo->user_login ;
			$AutoRedeem      = 'auto_redeem_' . strtolower( $Username ) ;
			$AppliedCoupons  = WC()->cart->get_applied_coupons() ;

			$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction() ;
			if ( $minimum_available_points && $Points < $minimum_available_points ) {
				$restriction_msg = str_replace( '[available_points]' , absint( $minimum_available_points ) , get_option( 'rs_available_points_redeem_error' , 'You are eligible to redeem your points only when you have [available_points] Points in your account' ) ) ;
				wc_print_notice( __( $restriction_msg ) , 'error' ) ;
				return ;
			}

			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId         = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
				$PointPriceType[]  = check_display_price_type( $ProductId ) ;
				$CheckIfEnable     = calculate_point_price_for_products( $ProductId ) ;
				if ( ! empty( $CheckIfEnable[ $ProductId ] ) ) {
					$PointPriceValue[] = $CheckIfEnable[ $ProductId ] ;
				}
			}
			if ( $Points > 0 ) {
				$MinUserPoints = ( '1' != get_user_meta( $UserId , 'rsfirsttime_redeemed' , true ) ) ? get_option( 'rs_first_time_minimum_user_points' ) : get_option( 'rs_minimum_user_points_to_redeem' ) ;
				if ( $Points >= $MinUserPoints ) {
					if ( srp_cart_subtotal() >= get_option( 'rs_minimum_cart_total_points' ) ) {
						if ( ! in_array( $AutoRedeem , $AppliedCoupons ) ) {
							if ( ! srp_check_is_array( $PointPriceValue ) && ! in_array( '2' , $PointPriceType ) ) {
								if ( is_cart() ) {
									?>
									<div class="fp_apply_reward">
										<?php if ( '1' == get_option( 'rs_show_hide_redeem_caption' ) ) { ?>
											<label id = "default_field" for="rs_apply_coupon_code_field"><?php echo wp_kses_post(get_option( 'rs_redeem_field_caption' ) ); ?></label>
										<?php } ?>
										<?php $placeholder = '1' == get_option( 'rs_show_hide_redeem_placeholder' ) ? get_option( 'rs_redeem_field_placeholder' ) : '' ; ?>
										<input id="rs_apply_coupon_code_field" class="input-text" type="text" placeholder="<?php echo wp_kses_post($placeholder) ; ?>" value="" name="rs_apply_coupon_code_field">
										<input class="button <?php echo esc_attr(get_option( 'rs_extra_class_name_apply_reward_points' )) ; ?>" type="submit" id='mainsubmi' value="<?php echo wp_kses_post(get_option( 'rs_redeem_field_submit_button_caption' )) ; ?>" name="rs_apply_coupon_code">
									</div>
									<div class='rs_warning_message'></div>
									<?php
								} elseif ( is_checkout() && '1' == get_option( 'rs_show_hide_redeem_field_checkout' ) ) {
									$extra_message = apply_filters( 'rs_extra_messages_for_redeeming' , '' ) ;
									?>
									<div class="checkoutredeem">
										<div class="woocommerce-info">

											<?php if ( $extra_message ) : ?>
												<div class="rs_add_extra_notice">
													<?php echo wp_kses_post(do_shortcode( $extra_message )) ; ?>
												</div>
											<?php endif ; ?>

											<?php echo wp_kses_post( do_shortcode( get_option( 'rs_reedming_field_label_checkout' ) ) ) ; ?> 
											<a href="javascript:void(0)" class="redeemit"> <?php echo wp_kses_post(get_option( 'rs_reedming_field_link_label_checkout' ) ); ?></a>
										</div>
									</div>
									<form name="checkout_redeeming" class="checkout_redeeming" method="post">
										<div class="fp_apply_reward">
											<?php if ( '1' == get_option( 'rs_show_hide_redeem_caption' ) ) { ?>
												<label id = "default_field" for="rs_apply_coupon_code_field"><?php echo wp_kses_post(get_option( 'rs_redeem_field_caption' )) ; ?></label>
											<?php } ?>
											<?php $placeholder = '1' == get_option( 'rs_show_hide_redeem_placeholder' ) ? get_option( 'rs_redeem_field_placeholder' ) : '' ; ?>
											<input id="rs_apply_coupon_code_field" class="input-text" type="text" placeholder="<?php echo wp_kses_post($placeholder) ; ?>" value="" name="rs_apply_coupon_code_field">
											<input class="button <?php echo esc_attr(get_option( 'rs_extra_class_name_apply_reward_points' )) ; ?>" type="submit" id='mainsubmi' value="<?php echo wp_kses_post(get_option( 'rs_redeem_field_submit_button_caption' )) ; ?>" name="rs_apply_coupon_code">
										</div>
										<div class='rs_warning_message'></div>
									</form>
									<?php
								}
							}
						}
					} else {
						if ( '1' == get_option( 'rs_show_hide_minimum_cart_total_error_message' ) ) {
							$CurrencyValue = srp_formatted_price( round_off_type_for_currency( get_option( 'rs_minimum_cart_total_points' ) ) ) ;
							$ReplacedMsg   = str_replace( '[carttotal]' , $CurrencyValue , get_option( 'rs_min_cart_total_redeem_error' ) ) ;
							$FinalMsg      = str_replace( '[currencysymbol]' , '' , $ReplacedMsg ) ;
							?>
							<div class="woocommerce-info"><?php echo wp_kses_post($FinalMsg) ; ?></div>
							<?php
						}
					}
				} else {
					if ( '1' != get_user_meta( $UserId , 'rsfirsttime_redeemed' , true ) ) {
						if ( '1' == get_option( 'rs_show_hide_first_redeem_error_message' )  ) {
							$ReplacedMsg = str_replace( '[firstredeempoints]' , get_option( 'rs_first_time_minimum_user_points' ) , get_option( 'rs_min_points_first_redeem_error_message' ) ) ;
							?>
							<div class="woocommerce-info"><?php echo wp_kses_post($ReplacedMsg) ; ?></div>
							<?php
						}
					} else {
						if ( '1' == get_option( 'rs_show_hide_after_first_redeem_error_message' ) ) {
							$ReplacedMsg = str_replace( '[points_after_first_redeem]' , get_option( 'rs_minimum_user_points_to_redeem' ) , get_option( 'rs_min_points_after_first_error' ) ) ;
							?>
							<div class="woocommerce-info"><?php echo wp_kses_post($ReplacedMsg) ; ?></div>
							<?php
						}
					}
				}
			} else {
				if ( '1' == get_option( 'rs_show_hide_points_empty_error_message' ) && ! srp_check_is_array( $PointPriceValue ) ) {
					?>
					<div class="woocommerce-info"><?php echo wp_kses_post(get_option( 'rs_current_points_empty_error_message' )) ; ?></div>
					<?php
				}
			}
		}

		public static function product_filter_for_redeem_field() {
			if ( '1' == get_option( 'rs_hide_redeeming_field' ) ) {
				return true ;
			}

			foreach ( WC()->cart->cart_contents as $item ) {
				if ('yes' ==  get_option( 'rs_exclude_products_for_redeeming' )  ) {
					if ( ! self::check_exc_products( $item ) ) {
						return false ;
					}
				}

				if ( 'yes' == get_option( 'rs_exclude_category_for_redeeming' ) ) {
					if ( ! self::check_exc_categories( $item ) ) {
						return false ;
					}
				}

				if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_products' ) ) {
					if ( self::check_inc_products( $item ) ) {
						return true ;
					}
				}

				if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_category' ) ) {
					if ( self::check_inc_categories( $item ) ) {
						return true ;
					}
				}
			}
			return true ;
		}

		public static function check_inc_products( $item ) {
			$ProductId       = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
			$IncludeProducts = ''!= get_option( 'rs_select_products_to_enable_redeeming' ) ? get_option( 'rs_select_products_to_enable_redeeming' ) : array() ;
			$IncludeProducts = srp_check_is_array( $IncludeProducts ) ? $IncludeProducts : explode( ',' , $IncludeProducts ) ;

			if ( ! srp_check_is_array( $IncludeProducts ) ) {
				return true ;
			}

			if ( in_array( $ProductId , $IncludeProducts ) ) {
				return true ;
			}

			return false ;
		}

		public static function check_exc_products( $item ) {
			$ProductId       = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
			$ExcludeProducts = get_option( 'rs_exclude_products_to_enable_redeeming' ) != '' ? get_option( 'rs_exclude_products_to_enable_redeeming' ) : array() ;
			$ExcludeProducts = srp_check_is_array( $IncludeProducts ) ? $IncludeProducts : explode( ',' , $IncludeProducts ) ;

			if ( ! srp_check_is_array( $ExcludeProducts ) ) {
				return true ;
			}

			if ( in_array( $ProductId , $ExcludeProducts ) ) {
				return false ;
			}

			return true ;
		}

		public static function check_inc_categories( $item ) {
			$ProductId        = $item[ 'product_id' ] ;
			$IncludedCategory = ''!=get_option( 'rs_select_category_to_enable_redeeming' ) ? get_option( 'rs_select_category_to_enable_redeeming' ) : array() ;
			$IncludedCategory = srp_check_is_array( $IncludedCategory ) ? $IncludedCategory : explode( ',' , $IncludedCategory ) ;

			if ( ! srp_check_is_array( $IncludedCategory ) ) {
				return true ;
			}

			$ProductCat = get_the_terms( $ProductId , 'product_cat' ) ;
			if ( ! srp_check_is_array( $ProductCat ) ) {
				return true ;
			}

			foreach ( $ProductCat as $Cat ) {
				if ( in_array( $Cat->term_id , $IncludedCategory ) ) {
					return true ;
				}
			}

			return false ;
		}

		public static function check_exc_categories( $item ) {
			$ProductId        = $item[ 'product_id' ] ;
			$ExcludedCategory = ''!=get_option( 'rs_exclude_category_to_enable_redeeming' ) ? get_option( 'rs_exclude_category_to_enable_redeeming' ) : array() ;
			$ExcludedCategory = srp_check_is_array( $ExcludedCategory ) ? $ExcludedCategory : explode( ',' , $ExcludedCategory ) ;

			if ( ! srp_check_is_array( $ExcludedCategory ) ) {
				return true ;
			}

			$ProductCat = get_the_terms( $ProductId , 'product_cat' ) ;
			if ( ! srp_check_is_array( $ProductCat ) ) {
				return true ;
			}

			foreach ( $ProductCat as $Cat ) {
				if ( in_array( $Cat->term_id , $ExcludedCategory ) ) {
					return false ;
				}
			}

			return true ;
		}

		/* Hide Redeeming Field in Cart and Checkout  */

		public static function redeem_field_based_on_settings() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			/* Hide Redeem field before Points applied - Start */
			$HideRedeemField = get_option( 'rs_show_redeeming_field' ) ;
			if ( '1' == get_option( 'rs_show_hide_redeem_field' ) || '5' == get_option( 'rs_show_hide_redeem_field' ) ) { //Show Redeem Field
				if ( 2 == $HideRedeemField && check_if_discount_applied() ) {
					echo wp_kses_post(self::cart_redeem_field( 'hide' )) ;
					echo wp_kses_post(self::checkout_redeem_field( 'hide' )) ;
				} else {
					echo wp_kses_post(self::cart_redeem_field( 'show' )) ;
					echo wp_kses_post(self::checkout_redeem_field( 'show' )) ;
				}
			} elseif ( '3' == get_option( 'rs_show_hide_redeem_field' ) ) { //Hide Redeem Field
				echo wp_kses_post(self::cart_redeem_field( 'hide' )) ;
				echo wp_kses_post(self::checkout_redeem_field( 'hide' )) ;
			} else { //Hide Coupon and Redeem Field
				echo wp_kses_post(woocommerce_coupon_field( 'hide' )) ;
				if ( '2' == get_option( 'rs_show_hide_redeem_field' ) ) {
					if ( 2 == $HideRedeemField && check_if_discount_applied() ) {
						echo wp_kses_post(self::cart_redeem_field( 'hide' )) ;
						echo wp_kses_post(self::checkout_redeem_field( 'hide' ) );
					} else {
						echo wp_kses_post(self::cart_redeem_field( 'show' ) );
						echo wp_kses_post(self::checkout_redeem_field( 'show' ) );
					}
				}
				if ('4' == get_option( 'rs_show_hide_redeem_field' ) ) {
					echo wp_kses_post(self::cart_redeem_field( 'hide' ) );
					echo wp_kses_post(self::checkout_redeem_field( 'hide' ) );
				}
			}
			/* Hide Redeem field before Points applied - End */
			$AppliedCoupons = WC()->cart->get_applied_coupons() ;
			if ( ! srp_check_is_array( $AppliedCoupons ) ) {
				return ;
			}

			$UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
			$Username   = $UserInfo->user_login ;
			$Redeem     = 'sumo_' . strtolower( "$Username" ) ;
			$AutoRedeem = 'auto_redeem_' . strtolower( "$Username" ) ;
			if ( 'yes' == get_option( '_rs_not_allow_earn_points_if_sumo_coupon' ) ) {
				foreach ( $AppliedCoupons as $Code ) {
					$CouponObj         = new WC_Coupon( $Code ) ;
					$CouponObj         = srp_coupon_obj( $CouponObj ) ;
					$CouponId          = $CouponObj[ 'coupon_id' ] ;
					$CheckIfSUMOCoupon = get_post_meta( $CouponId , 'sumo_coupon_check' , true ) ;
					if ( 'yes' == $CheckIfSUMOCoupon ) {
						self::cart_redeem_field( 'hide' ) ;
					}
				}
			}
			/* Hide Redeem field after Points applied - Start */
			echo wp_kses_post( in_array( $Redeem , $AppliedCoupons ) || in_array( $AutoRedeem , $AppliedCoupons )  ? self::cart_redeem_field( 'hide' ) : self::cart_redeem_field( 'show' )) ;
			echo wp_kses_post( in_array( $Redeem , $AppliedCoupons ) || in_array( $AutoRedeem , $AppliedCoupons )  ? self::checkout_redeem_field( 'hide' ) : self::checkout_redeem_field( 'show' )) ;
			/* Hide Redeem field after Points applied - End */
		}

		public static function cart_redeem_field( $Param ) {
			if ( 'show' == $Param ) {
				$contents = '.fp_apply_reward, .rs_button_redeem_cart{
                                                            display: block;
                                                    }' ;
			} else {
				$contents = '.fp_apply_reward, .rs_button_redeem_cart{
                                                            display: none;
                                                    }' ;
			}
					
			if ( '2' == get_option( 'rs_reward_point_troubleshoot_after_cart' ) ) {
				$contents .= '.fp_apply_reward #default_field{
						margin-top:5px;
						display:inline-block !important;
						float:left;
						line-height:27px;
						position:static;
					}';
			}
						
			self::add_inline_style($contents);                       
		}

		public static function checkout_redeem_field( $Param ) {
			if ( 'show' == $Param ) {
				$contents = '.checkoutredeem, .rs_button_redeem_checkout{
                                                            display: block;
                                                    }' ;
			} else {
				$contents = '.checkoutredeem, .rs_button_redeem_checkout{
                                                            display: none;
                                                    }' ;
			}
					
			self::add_inline_style($contents);
		}
				
		public static function add_inline_style( $contents) {
					
			 wp_register_style( 'fp-srp-redeeming-field-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
			 wp_enqueue_style( 'fp-srp-redeeming-field-style' ) ;
			 wp_add_inline_style( 'fp-srp-redeeming-field-style' , $contents ) ;
		}

		/* Update Coupon Amount */

		public static function update_coupon_amount( $BoolVal ) {
			if ( ! is_user_logged_in() ) {
				return $BoolVal ;
			}

			if ( '1' == get_option( 'rs_max_redeem_discount' ) ) {
				return $BoolVal ;
			}

			$AppliedCoupons = WC()->cart->get_applied_coupons() ;
			if ( ! srp_check_is_array( $AppliedCoupons ) ) {
				return $BoolVal ;
			}
			
			// Calculate totals. 
			WC()->cart->calculate_totals() ;

			$CartTotal    = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) ? WC()->cart->subtotal : WC()->cart->subtotal_ex_tax ;
			$MinCartTotal = get_option( 'rs_minimum_cart_total_points' ) ;
			$MaxCartTotal = get_option( 'rs_maximum_cart_total_points' ) ;
			$ProductTotal = self::get_sum_of_selected_products() ;
			$RedeemValue  = 1 == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ? $CartTotal : $ProductTotal ;
			foreach ( $AppliedCoupons as $Code ) {
				$CouponObj  = new WC_Coupon( $Code ) ;
				$CouponObj  = srp_coupon_obj( $CouponObj ) ;
				$CouponAmnt = $CouponObj[ 'coupon_amount' ] ;
				$CouponId   = $CouponObj[ 'coupon_id' ] ;
				$UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
				$Username   = $UserInfo->user_login ;
				$Redeem     = 'sumo_' . strtolower( "$Username" ) ;
				$AutoRedeem = 'auto_redeem_' . strtolower( "$Username" ) ;
				if ( ( $Code != $Redeem ) && ( $Code != $AutoRedeem ) ) {
					continue ;
				}

				if ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
					if ( $CartTotal < $MinCartTotal || $CartTotal > $MaxCartTotal ) {
						if ( ! empty( $CouponId ) ) {
							wp_trash_post( $CouponId ) ;
						}
					}
				} else if ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
					if ( $CartTotal < $MinCartTotal ) {
						if ( ! empty( $CouponId ) ) {
							wp_trash_post( $CouponId ) ;
						}
					}
				} else if ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
					if ( $CartTotal > $MaxCartTotal ) {
						if ( ! empty( $CouponId ) ) {
							wp_trash_post( $CouponId ) ;
						}
					}
				}

				$MaxDiscountAmntForDefault = ! empty( get_option( 'rs_percent_max_redeem_discount' ) ) ? ( get_option( 'rs_percent_max_redeem_discount' ) / 100 ) * $RedeemValue : 0 ;
				$MaxDiscountAmntForButton  = ! empty( get_option( 'rs_percentage_cart_total_redeem' ) ) ? ( get_option( 'rs_percentage_cart_total_redeem' ) / 100 ) * $RedeemValue : 0 ;
				$Discount                  = ( 2 == get_option( 'rs_redeem_field_type_option' ) ) ? $MaxDiscountAmntForButton : $MaxDiscountAmntForDefault ;
				if ( $CouponAmnt <= $Discount ) {
					continue ;
				}

				update_post_meta( $CouponId , 'coupon_amount' , $Discount ) ;
			}
			return $BoolVal ;
		}

		public static function unset_session() {
			// Check ajax referer when remove button is clicked.
			if ( ! check_ajax_referer( 'remove-coupon' , 'security' , false ) ) {
				return ;
			}
			WC()->session->set( 'auto_redeemcoupon' , 'no' ) ;
		}

		/* Auto Redeeming in Cart and Checkout */

		public static function redeem_points_for_user_automatically() {

			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( ! is_object( WC()->cart ) ) {
				return ;
			}
			
			$BanningType = check_banning_type( get_current_user_id() ) ;
			if ( 'redeemingonly' == $BanningType || 'both' == $BanningType ) {
				return ;
			}

			if ( empty( WC()->cart->get_cart_contents_count() ) ) {
				WC()->session->set( 'auto_redeemcoupon' , 'yes' ) ;
				foreach ( WC()->cart->applied_coupons as $Code ) {
					WC()->cart->remove_coupon( $Code ) ;
				}

				return ;
			}

			$UserId     = get_current_user_id() ;
			$PointsData = new RS_Points_Data( $UserId ) ;
			$Points     = $PointsData->total_available_points() ;

			$UserInfo  = get_user_by( 'id' , $UserId ) ;
			$user_role = is_object( $UserInfo ) ? $UserInfo->roles : array() ;
			$user_role = implode( '' , $user_role ) ;

			$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction() ;
			if ( $minimum_available_points && $Points < $minimum_available_points ) {
				return ;
			}

			if ( empty( $Points ) ) {
				return ;
			}

			if ( $Points < get_option( 'rs_first_time_minimum_user_points' ) ) {
				return ;
			}

			if ( $Points < get_option( 'rs_minimum_user_points_to_redeem' ) ) {
				return ;
			}

			if ( check_if_pointprice_product_exist_in_cart() ) {
				return ;
			}

			if ( 'yes' != get_option( 'rs_enable_disable_auto_redeem_points' ) ) {
				return ;
			}

			$CartSubtotal = srp_cart_subtotal() ;

			$MinCartTotal = get_option( 'rs_minimum_cart_total_points' ) ;
			$MaxCartTotal = get_option( 'rs_maximum_cart_total_points' ) ;
			if ( is_cart() ) {
				self::auto_redeeming_in_cart( $UserId , $Points , $CartSubtotal , $MaxCartTotal , $MinCartTotal ) ;
			}

			if ( is_checkout() ) {
				self::auto_redeeming_in_checkout( $UserId , $Points , $CartSubtotal , $MaxCartTotal , $MinCartTotal ) ;
			}
		}

		public static function auto_redeeming_in_cart( $UserId, $Points, $CartSubtotal, $MaxCartTotal, $MinCartTotal ) {
			if ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
				if ( $CartSubtotal >= $MinCartTotal && $CartSubtotal <= $MaxCartTotal ) {
					self::auto_redeeming( $UserId , $Points ) ;
				}
			} else if ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
				if ( $CartSubtotal >= $MinCartTotal ) {
					self::auto_redeeming( $UserId , $Points ) ;
				}
			} else if ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
				if ( $CartSubtotal <= $MaxCartTotal ) {
					self::auto_redeeming( $UserId , $Points ) ;
				}
			} else if ( empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
				self::auto_redeeming( $UserId , $Points ) ;
			}
		}

		public static function auto_redeeming_in_checkout( $UserId, $Points, $CartSubtotal, $MaxCartTotal, $MinCartTotal ) {
			if ( isset( $_GET[ 'remove_coupon' ] ) ) {
				WC()->session->set( 'auto_redeemcoupon' , 'no' ) ;
			}

			if ( 'yes' != get_option( 'rs_enable_disable_auto_redeem_checkout' ) ) {
				return ;
			}

			self::auto_redeeming_in_cart( $UserId , $Points , $CartSubtotal , $MaxCartTotal , $MinCartTotal ) ;
		}

		public static function auto_redeeming( $UserId, $Points ) {
			if ( 'no' == WC()->session->get( 'auto_redeemcoupon' ) ) {
				return ;
			}

			if ( 'yes' == get_option( 'rs_restrict_sale_price_for_redeeming' ) && ! self::block_redeeming_for_sale_price_product() ) {
				wc_add_notice( __( get_option( 'rs_redeeming_message_restrict_for_sale_price_product' ) ) , 'error' ) ;
				WC()->session->set( 'auto_redeemcoupon' , 'no' ) ;
				return ;
			}

			$PointPriceType  = array() ;
			$PointPriceValue = array() ;
			$UserInfo        = get_user_by( 'id' , $UserId ) ;
			$UserName        = $UserInfo->user_login ;

			if ( WC()->cart->has_discount( 'auto_redeem_' . strtolower( $UserName ) ) ) {
				return ;
			}

			// Need to Calculate Totals for Auto Redeeming on using Order Again in My Account View Orders Page [Added in V24.4.1].
			if ( is_cart() ) {
				WC()->cart->calculate_totals() ;
			}

			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId         = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
				$PointPriceType[]  = check_display_price_type( $ProductId ) ;
				$CheckIfEnable     = calculate_point_price_for_products( $ProductId ) ;
				if ( ! empty( $CheckIfEnable[ $ProductId ] ) ) {
					$PointPriceValue[] = $CheckIfEnable[ $ProductId ] ;
				}
			}
			if ( srp_check_is_array( $PointPriceValue ) ) {
				return ;
			}

			if ( in_array( 2 , $PointPriceType ) ) {
				return ;
			}


			$CartTotal        = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) ? WC()->cart->subtotal : WC()->cart->subtotal_ex_tax ;
			$ProductTotal     = self::get_sum_of_selected_products() ;
			$RedeemValue      = 1 == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ? $CartTotal : $ProductTotal ;
			$CartContentTotal = 1 == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ? WC()->cart->cart_contents_total : $ProductTotal ;
			$CartContentCount = 1 == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ? WC()->cart->cart_contents_count : $ProductTotal ;
			if ( $CartTotal < get_option( 'rs_minimum_cart_total_points' ) ) {
				return ;
			}

			$OldCouponId = get_user_meta( $UserId , 'auto_redeemcoupon_ids' , true ) ;
			wp_delete_post( $OldCouponId , true ) ;
			if ( class_exists( 'WC_Cache_Helper' ) ) {
				wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_auto_redeem_' . strtolower( $UserName ) , 'coupons' ) ;
			}

			$CouponData = array(
				'post_title'  => 'auto_redeem_' . strtolower( $UserName ) ,
				'post_status' => 'publish' ,
				'post_author' => $UserId ,
				'post_type'   => 'shop_coupon' ,
					) ;
			$CouponId   = wp_insert_post( $CouponData ) ;
			update_user_meta( $UserId , 'auto_redeemcoupon_ids' , $CouponId ) ;

			/* For Security Reasons added user email in Allowed Emails field of Edit Coupon page */
			$allowed_email = is_object( $UserInfo ) ? $UserInfo->user_email : '' ;
			update_post_meta( $CouponId , 'customer_email' , $allowed_email ) ;

			if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_products' ) ) {
				$IncProductId = get_option( 'rs_select_products_to_enable_redeeming' ) ;
				$IncProductId = srp_check_is_array( $IncProductId ) ? $IncProductId : ( empty( $IncProductId ) ? array() : explode( ',' , $IncProductId ) ) ;
				update_post_meta( $CouponId , 'product_ids' , implode( ',' , array_filter( array_map( 'intval' , $IncProductId ) ) ) ) ;
			}

			if ( 'yes' == get_option( 'rs_exclude_products_for_redeeming' ) ) {
				$ExcProductId = get_option( 'rs_exclude_products_to_enable_redeeming' ) ;
				$ExcProductId = srp_check_is_array( $ExcProductId ) ? $ExcProductId : ( empty( $ExcProductId ) ? array() : explode( ',' , $ExcProductId ) ) ;
				update_post_meta( $CouponId , 'exclude_product_ids' , implode( ',' , array_filter( array_map( 'intval' , $ExcProductId ) ) ) ) ;
				$ExcludedId   = get_post_meta( $CouponId , 'exclude_product_ids' , true ) ;
				foreach ( WC()->cart->cart_contents as $key => $value ) {
					if ( $ExcludedId == $value[ 'product_id' ] ) {
						WC()->session->set( 'auto_redeemcoupon' , 'no' ) ;
					}
				}
			}
			if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_category' ) ) {
				$IncCategory = get_option( 'rs_select_category_to_enable_redeeming' ) ;
				$IncCategory = srp_check_is_array( $IncCategory ) ? $IncCategory : ( empty( $IncCategory ) ? array() : explode( ',' , $IncCategory ) ) ;
				update_post_meta( $CouponId , 'product_categories' , array_filter( array_map( 'intval' , $IncCategory ) ) ) ;
			}
			if ( 'yes' == get_option( 'rs_exclude_category_for_redeeming' ) ) {
				$ExcCategory = get_option( 'rs_exclude_category_to_enable_redeeming' ) ;
				$ExcCategory = srp_check_is_array( $ExcCategory ) ? $ExcCategory : ( empty( $ExcCategory ) ? array() : explode( ',' , $ExcCategory ) ) ;
				update_post_meta( $CouponId , 'exclude_product_categories' , array_filter( array_map( 'intval' , $ExcCategory ) ) ) ;
			}

			update_post_meta( $CouponId , 'carttotal' , $CartContentTotal ) ;
			update_post_meta( $CouponId , 'cartcontenttotal' , $CartContentCount ) ;
			$MaxThreshold = ! empty( get_option( 'rs_percentage_cart_total_auto_redeem' ) ) ? get_option( 'rs_percentage_cart_total_auto_redeem' ) : 100 ;
			$MaxThreshold = ( $MaxThreshold / 100 ) * $RedeemValue ;
			if ( '1' == get_option( 'rs_max_redeem_discount' ) && ! empty( get_option( 'rs_fixed_max_redeem_discount' ) ) ) {
				if ( $MaxThreshold > get_option( 'rs_fixed_max_redeem_discount' ) ) {
					$CouponValue = get_option( 'rs_fixed_max_redeem_discount' ) ;
					$ErrMsg      = str_replace( '[percentage] %' , get_option( 'rs_fixed_max_redeem_discount' ) , get_option( 'rs_errmsg_for_max_discount_type' ) ) ;
					wc_add_notice( __( $ErrMsg ) , 'error' ) ;
				} else {
					$CouponValue = $MaxThreshold ;
				}
			} else {
				if ( ! empty( get_option( 'rs_percent_max_redeem_discount' ) ) ) {
					$MaxRedeemDiscount = ( get_option( 'rs_percent_max_redeem_discount' ) / 100 ) * $RedeemValue ;
					if ( $MaxRedeemDiscount > $MaxThreshold ) {
						$CouponValue = $MaxThreshold ;
					} else {
						$CouponValue = ( $MaxThreshold / 100 ) * get_option( 'rs_percent_max_redeem_discount' ) ;
						$ErrMsg      = str_replace( '[percentage] ' , get_option( 'rs_percent_max_redeem_discount' ) , get_option( 'rs_errmsg_for_max_discount_type' ) ) ;
						wc_add_notice( __( $ErrMsg ) , 'error' ) ;
					}
				} else {
					$CouponValue = empty( $MaxThreshold ) ? $RedeemValue : $MaxThreshold ;
				}
			}
			$CouponAmnt     = redeem_point_conversion( $CouponValue , $UserId , 'price' ) ;
			$ConvertedPoint = redeem_point_conversion( $Points , $UserId , 'price' ) ;
			$Amount         = ( $CouponAmnt > $Points ) ? $ConvertedPoint : $CouponValue ;
			$Amount         = ( $Amount > $ConvertedPoint ) ? $ConvertedPoint : $Amount ;
			update_post_meta( $CouponId , 'coupon_amount' , $Amount ) ;
			$FreeShipping   = ( '1' == get_option( 'rs_apply_shipping_tax' ) ) ? 'yes' : 'no' ;
			update_post_meta( $CouponId , 'free_shipping' , $FreeShipping ) ;

			if ( get_post_meta( $CouponId , 'coupon_amount' , true ) == 0 ) {
				return ;
			}

			if ( ! empty( get_option( 'rs_minimum_redeeming_points' ) ) && empty( get_option( 'rs_maximum_redeeming_points' ) ) ) {
				if ( $CouponAmnt > get_option( 'rs_minimum_redeeming_points' ) ) {
					WC()->cart->add_discount( 'auto_redeem_' . strtolower( $UserName ) ) ;
				}
			}

			if ( ! empty( get_option( 'rs_maximum_redeeming_points' ) ) && empty( get_option( 'rs_minimum_redeeming_points' ) ) ) {
				if ( $CouponAmnt < get_option( 'rs_maximum_redeeming_points' ) ) {
					WC()->cart->add_discount( 'auto_redeem_' . strtolower( $UserName ) ) ;
				}
			}

			if ( get_option( 'rs_minimum_redeeming_points' ) == get_option( 'rs_maximum_redeeming_points' ) ) {
				if ( ( get_option( 'rs_minimum_redeeming_points' ) == $CouponAmnt ) && ( get_option( 'rs_maximum_redeeming_points' ) == $CouponAmnt ) ) {
					WC()->cart->add_discount( 'auto_redeem_' . strtolower( $UserName ) ) ;
				}
			}

			if ( empty( get_option( 'rs_minimum_redeeming_points' ) ) && empty( get_option( 'rs_maximum_redeeming_points' ) ) ) {
				WC()->cart->add_discount( 'auto_redeem_' . strtolower( $UserName ) ) ;
			}

			if ( ! empty( get_option( 'rs_minimum_redeeming_points' ) ) && ! empty( get_option( 'rs_maximum_redeeming_points' ) ) ) {
				if ( ( $CouponAmnt >= get_option( 'rs_minimum_redeeming_points' ) ) && ( $CouponAmnt <= get_option( 'rs_maximum_redeeming_points' ) ) ) {
					WC()->cart->add_discount( 'auto_redeem_' . strtolower( $UserName ) ) ;
				}
			}

			// usage Limit and Count added in V24.4.1    
			update_post_meta( $CouponId , 'usage_limit' , '1' ) ;
			update_post_meta( $CouponId , 'usage_count' , '0' ) ;
		}

		public static function block_redeeming_for_sale_price_product() {
			if ( ! srp_check_is_array( WC()->cart->cart_contents ) ) {
				return true ;
			}

			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId  = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
				$ProductObj = srp_product_object( $ProductId ) ;
				$SalePrice  = is_object( $ProductObj ) ? $ProductObj->get_sale_price() : '' ;
				if ( ! empty( $SalePrice ) ) {
					return false ;
				}
			}
			return true ;
		}

		public static function redeem_point_for_user() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( isset( $_REQUEST[ 'rs_apply_coupon_code' ] ) || isset( $_REQUEST[ 'rs_apply_coupon_code1' ] ) || isset( $_REQUEST[ 'rs_apply_coupon_code2' ] ) ) {
				if ( ! isset( $_REQUEST[ 'rs_apply_coupon_code_field' ] ) ) {
					return ;
				}

				if ( empty( $_REQUEST[ 'rs_apply_coupon_code_field' ] ) ) {
					return ;
				}

				if ( 'yes' == get_option( 'rs_restrict_sale_price_for_redeeming' ) && ! self::block_redeeming_for_sale_price_product() ) {
					wc_add_notice( __( get_option( 'rs_redeeming_message_restrict_for_sale_price_product' ) ) , 'error' ) ;
					return ;
				}


				$redeeming_value = wc_clean( wp_unslash( $_REQUEST[ 'rs_apply_coupon_code_field' ] ) ) ;
				$redeeming_value = floatval( str_replace( wc_get_price_decimal_separator() , '.' , $redeeming_value ) ) ;

				$UserId           = get_current_user_id() ;
				$CartTotal        = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) ? WC()->cart->subtotal : WC()->cart->subtotal_ex_tax ;
				$ProductTotal     = self::get_sum_of_selected_products() ;
				$RedeemValue      = 1 == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ? $CartTotal : $ProductTotal ;
				$CartContentTotal = 1 == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ? WC()->cart->cart_contents_total : $ProductTotal ;
				$CartContentCount = 1 == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ? WC()->cart->cart_contents_count : $ProductTotal ;
				$UserInfo         = get_user_by( 'id' , $UserId ) ;
				$UserName         = $UserInfo->user_login ;
				$PointsData       = new RS_Points_Data( $UserId ) ;
				$Points           = $PointsData->total_available_points() ;
				$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction();
				if ( $minimum_available_points && $Points < $minimum_available_points ) {
					return ;
				}

				$OldCouponId = get_user_meta( $UserId , 'redeemcouponids' , true ) ;
				wp_delete_post( $OldCouponId , true ) ;
				if ( class_exists( 'WC_Cache_Helper' ) ) {
					wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_sumo_' . strtolower( $UserName ) , 'coupons' ) ;
				}

				$CouponData = array(
					'post_title'   => 'sumo_' . strtolower( $UserName ) ,
					'post_content' => '' ,
					'post_status'  => 'publish' ,
					'post_author'  => $UserId ,
					'post_type'    => 'shop_coupon' ,
						) ;
				$CouponId   = wp_insert_post( $CouponData ) ;
				update_user_meta( $UserId , 'redeemcouponids' , $CouponId ) ;

				/* For Security Reasons added user email in Allowed Emails field of Edit Coupon page */
				$allowed_email = is_object( $UserInfo ) ? $UserInfo->user_email : '' ;
				update_post_meta( $CouponId , 'customer_email' , $allowed_email ) ;

				if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_products' )  ) {
					$IncProductId = get_option( 'rs_select_products_to_enable_redeeming' ) ;
					$IncProductId = srp_check_is_array( $IncProductId ) ? $IncProductId : ( empty( $IncProductId ) ? array() : explode( ',' , $IncProductId ) ) ;
					update_post_meta( $CouponId , 'product_ids' , implode( ',' , array_filter( array_map( 'intval' , $IncProductId ) ) ) ) ;
				}

				if ( 'yes' == get_option( 'rs_exclude_products_for_redeeming' ) ) {
					$ExcProductId = get_option( 'rs_exclude_products_to_enable_redeeming' ) ;
					$ExcProductId = srp_check_is_array( $ExcProductId ) ? $ExcProductId : ( empty( $ExcProductId ) ? array() : explode( ',' , $ExcProductId ) ) ;
					update_post_meta( $CouponId , 'exclude_product_ids' , implode( ',' , array_filter( array_map( 'intval' , $ExcProductId ) ) ) ) ;
					$ExcludedId   = get_post_meta( $CouponId , 'exclude_product_ids' , true ) ;
					foreach ( WC()->cart->cart_contents as $key => $value ) {
						if ( $value[ 'product_id' ] == $ExcludedId ) {
							WC()->session->set( 'auto_redeemcoupon' , 'no' ) ;
						}
					}
				}
				if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_category' ) ) {
					$IncCategory = get_option( 'rs_select_category_to_enable_redeeming' ) ;
					$IncCategory = srp_check_is_array( $IncCategory ) ? $IncCategory : ( empty( $IncCategory ) ? array() : explode( ',' , $IncCategory ) ) ;
					update_post_meta( $CouponId , 'product_categories' , array_filter( array_map( 'intval' , $IncCategory ) ) ) ;
				}
				if ( 'yes' == get_option( 'rs_exclude_category_for_redeeming' ) ) {
					$ExcCategory = get_option( 'rs_exclude_category_to_enable_redeeming' ) ;
					$ExcCategory = srp_check_is_array( $ExcCategory ) ? $ExcCategory : ( empty( $ExcCategory ) ? array() : explode( ',' , $ExcCategory ) ) ;
					update_post_meta( $CouponId , 'exclude_product_categories' , array_filter( array_map( 'intval' , $ExcCategory ) ) ) ;
				}

				update_post_meta( $CouponId , 'carttotal' , $CartContentTotal ) ;
				update_post_meta( $CouponId , 'cartcontenttotal' , $CartContentCount ) ;
				update_post_meta( $CouponId , 'discount_type' , 'fixed_cart' ) ;
				$Percentage        = isset( $_REQUEST[ 'rs_apply_coupon_code1' ] ) ? get_option( 'rs_percentage_cart_total_redeem' , 100 ) : get_option( 'rs_percentage_cart_total_redeem_checkout' , 100 ) ;
				$Redeem_field_type = is_cart() ? get_option( 'rs_redeem_field_type_option' ) : get_option( 'rs_redeem_field_type_option_checkout' ) ;
				$MaxThreshold      = 1 == $Redeem_field_type ? $redeeming_value : ( ( float ) $Percentage / 100 ) * $redeeming_value ;
				$MaxThreshold      = redeem_point_conversion( $MaxThreshold , $UserId , 'price' ) ;
				if ( '1' == get_option( 'rs_max_redeem_discount' ) && ! empty( get_option( 'rs_fixed_max_redeem_discount' ) ) ) {
					if ( $MaxThreshold > get_option( 'rs_fixed_max_redeem_discount' ) ) {
						$CouponValue = get_option( 'rs_fixed_max_redeem_discount' ) ;
						$ErrMsg      = str_replace( '[percentage] %' , get_option( 'rs_fixed_max_redeem_discount' ) , get_option( 'rs_errmsg_for_max_discount_type' ) ) ;
						$ErrMsg      = do_shortcode( $ErrMsg ) ;
						wc_add_notice( __( $ErrMsg ) , 'error' ) ;
					} else {
						$CouponValue = $MaxThreshold ;
					}
				} else {
					if ( ! empty( get_option( 'rs_percent_max_redeem_discount' ) ) ) {
						$MaxRedeemDiscount = ( get_option( 'rs_percent_max_redeem_discount' ) / 100 ) * $RedeemValue ;
						if ( $MaxRedeemDiscount > $MaxThreshold ) {
							$CouponValue = $MaxThreshold ;
						} else {
							$CouponValue = $MaxRedeemDiscount ;
							$ErrMsg      = str_replace( '[percentage] ' , get_option( 'rs_percent_max_redeem_discount' ) , get_option( 'rs_errmsg_for_max_discount_type' ) ) ;
							$ErrMsg      = do_shortcode( $ErrMsg ) ;
							wc_add_notice( __( $ErrMsg ) , 'error' ) ;
						}
					} else {
						$Applied_points = redeem_point_conversion( $redeeming_value , $UserId , 'price' ) ;
						$CouponValue    = ( $Applied_points > $RedeemValue ) ? ( float ) $RedeemValue : ( float ) $Applied_points ;
					}
				}
				$CouponAmnt     = redeem_point_conversion( $CouponValue , $UserId ) ;
				$ConvertedPoint = redeem_point_conversion( $Points , $UserId , 'price' ) ;
				$Amount         = ( $CouponAmnt > $Points ) ? $ConvertedPoint : $CouponValue ;
				update_post_meta( $CouponId , 'coupon_amount' , $Amount ) ;

				if ( 0 == get_post_meta( $CouponId , 'coupon_amount' , true ) ) {
					return ;
				}

				$IndividualUse = ( 'yes' == get_option( 'rs_coupon_applied_individual' ) ) ? 'yes' : 'no' ;
				update_post_meta( $CouponId , 'individual_use' , $IndividualUse ) ;

				// Usage Count Meta Added in V23.4.6.
				update_post_meta( $CouponId , 'usage_count' , '0' ) ;

				update_post_meta( $CouponId , 'usage_limit' , '1' ) ;
				update_post_meta( $CouponId , 'expiry_date' , '' ) ;
				$ApplyTax     = ( '1' == get_option( 'rs_apply_redeem_before_tax' ) ) ? 'yes' : 'no' ;
				update_post_meta( $CouponId , 'apply_before_tax' , $ApplyTax ) ;
				$FreeShipping = ( '1' == get_option( 'rs_apply_shipping_tax' ) ) ? 'yes' : 'no' ;
				update_post_meta( $CouponId , 'free_shipping' , $FreeShipping ) ;

				if ( WC()->cart->has_discount( 'sumo_' . strtolower( $UserName ) ) ) {
					return ;
				}

				WC()->cart->add_discount( 'sumo_' . strtolower( $UserName ) ) ;
				if ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) && 'incl' == get_option( 'woocommerce_tax_display_shop' ) && 'incl' == get_option( 'woocommerce_tax_display_cart' ) ) {
					if ( 'yes' == get_option( 'rs_enable_redeem_point_without_incl_tax' ) ) {
						$discount = WC()->cart->get_coupon_discount_amount( 'sumo_' . strtolower( $UserName ) ) ;
						update_post_meta( $CouponId, 'coupon_amount', $discount ) ;
					}
				}

				// Form Submit not occurs properly issue . Added Safe Redirect URL in V24.4.1.
				if ( is_cart() ) {
					wp_safe_redirect( wc_get_cart_url() ) ;
					exit ;
				} else if ( is_checkout() ) {
					wp_safe_redirect( wc_get_checkout_url() ) ;
					exit ;
				}
			}
		}

		public static function get_sum_of_selected_products() {
			$IncProductId = get_option( 'rs_select_products_to_enable_redeeming' ) ;
			$IncProductId = srp_check_is_array( $IncProductId ) ? $IncProductId : ( empty( $IncProductId ) ? array() : explode( ',' , $IncProductId ) ) ;

			$ExcProductId = get_option( 'rs_exclude_products_to_enable_redeeming' ) ;
			$ExcProductId = srp_check_is_array( $ExcProductId ) ? $ExcProductId : ( empty( $ExcProductId ) ? array() : explode( ',' , $ExcProductId ) ) ;

			$IncCategory = get_option( 'rs_select_category_to_enable_redeeming' ) ;
			$IncCategory = srp_check_is_array( $IncCategory ) ? $IncCategory : ( empty( $IncCategory ) ? array() : explode( ',' , $IncCategory ) ) ;

			$ExcCategory = get_option( 'rs_exclude_category_to_enable_redeeming' ) ;
			$ExcCategory = srp_check_is_array( $ExcCategory ) ? $ExcCategory : ( empty( $ExcCategory ) ? array() : explode( ',' , $ExcCategory ) ) ;

			$Total = array() ;
			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId  = empty( $item[ 'variation_id' ] ) ? $item[ 'product_id' ] : $item[ 'variation_id' ] ;
				$ProductCat = get_the_terms( $item[ 'product_id' ] , 'product_cat' ) ;
				$LineTotal  = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) ? ( $item[ 'line_subtotal' ] + $item[ 'line_tax' ] ) : $item[ 'line_subtotal' ] ;
				/* Checking whether the Product has Category */
				if ( srp_check_is_array( $ProductCat ) ) {
					foreach ( $ProductCat as $CatObj ) {
						if ( ! is_object( $CatObj ) ) {
							continue ;
						}

						$termid = $CatObj->term_id ;

						if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_category' ) && srp_check_is_array( $IncCategory ) ) {
							if ( in_array( $termid , $IncCategory ) ) {
								$Total[] = $LineTotal ;
							}
						}

						if ( 'yes' == get_option( 'rs_exclude_category_for_redeeming' ) && srp_check_is_array( $ExcCategory ) ) {
							if ( in_array( $termid , $ExcCategory ) ) {
								$Total[] = $LineTotal ;
							}
						}
					}
				}

				if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_products' ) && srp_check_is_array( $IncProductId ) ) {
					if ( in_array( $ProductId , $IncProductId ) ) {
						$Total[] = $LineTotal ;
					}
				}

				if ( 'yes' == get_option( 'rs_exclude_products_for_redeeming' ) && srp_check_is_array( $ExcProductId ) ) {
					if ( ! in_array( $ProductId , $ExcProductId ) ) {
						$Total[] = $LineTotal ;
					}
				}
			}
			$ValueToReturn = srp_check_is_array( $Total ) ? array_sum( $Total ) : WC()->cart->subtotal ;
			return $ValueToReturn ;
		}

		public static function messages_for_redeeming() {
			echo wp_kses_post(self::msg_when_tax_enabled()) ;
			echo wp_kses_post(self::balance_point_msg_after_redeeming() );
			echo wp_kses_post(self::button_type_redeem_field_in_cart_and_checkout()) ;
		}

		/* Remaining Point message after Redeeming is applied in Cart/Checkout */

		public static function balance_point_msg_after_redeeming() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( ! srp_check_is_array( WC()->cart->get_applied_coupons() ) ) {
				return ;
			}

			$UserId       = get_current_user_id() ;
			$banning_type = check_banning_type( $UserId ) ;
			if ( 'redeemingonly' == $banning_type || 'both' == $banning_type ) {
				return ;
			}

			$UserInfo   = get_user_by( 'id' , $UserId ) ;
			$UserName   = $UserInfo->user_login ;
			$Redeem     = 'sumo_' . strtolower( "$UserName" ) ;
			$AutoRedeem = 'auto_redeem_' . strtolower( "$UserName" ) ;

			$DiscountAmnt        = isset( WC()->cart->coupon_discount_amounts[ "$AutoRedeem" ] ) ? WC()->cart->coupon_discount_amounts[ "$AutoRedeem" ] : ( isset( WC()->cart->coupon_discount_amounts[ "$Redeem" ] ) ? WC()->cart->coupon_discount_amounts[ "$Redeem" ] : 0 ) ;
			$ShowBalancePointMsg = is_cart() ? get_option( 'rs_show_hide_message_for_redeem_points' ) : get_option( 'rs_show_hide_message_for_redeem_points_checkout_page' ) ;
			$BalancePointMsg     = is_cart() ? get_option( 'rs_message_user_points_redeemed_in_cart' ) : get_option( 'rs_message_user_points_redeemed_in_checkout' ) ;
			foreach ( WC()->cart->get_applied_coupons() as $Code ) {
				if ( 'yes' == get_option( 'rs_disable_point_if_coupon' ) ) {
					if ( strtolower( $Code ) != $AutoRedeem && strtolower( $Code ) != $Redeem ) {
						?>
						<div class="woocommerce-info sumo_reward_points_auto_redeem_message">
							<?php echo wp_kses_post(get_option( 'rs_errmsg_for_coupon_in_order' )) ; ?>
						</div>
						<?php
					}
				}
				if ( '1' == $ShowBalancePointMsg ) {
					if ( ! empty( $DiscountAmnt ) ) {
						if ( strtolower( $Code ) == $Redeem || strtolower( $Code ) == $AutoRedeem ) {
							?>
							<div class="woocommerce-message sumo_reward_points_auto_redeem_message rs_cart_message">
								<?php echo wp_kses_post(do_shortcode( $BalancePointMsg ) ); ?>
							</div>
							<?php
							if ( 'yes' == get_option( 'rs_product_purchase_activated' ) && 'yes' == get_option( 'rs_enable_redeem_for_order' ) ) {
								?>
								<div class="woocommerce-info sumo_reward_points_auto_redeem_error_message">
									<?php echo wp_kses_post(get_option( 'rs_errmsg_for_redeeming_in_order' )) ; ?>
								</div>
								<?php
							}
							echo wp_kses_post(self::cart_redeem_field( 'hide' )) ;
							echo wp_kses_post(self::checkout_redeem_field( 'hide' ) );
						}
					}
				}
			}
		}

		/* Button Redeem Field in Cart/Checkout */

		public static function button_type_redeem_field_in_cart_and_checkout() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			$ShowRedeemField = is_cart() ? get_option( 'rs_redeem_field_type_option' ) : get_option( 'rs_redeem_field_type_option_checkout' ) ;
			if ( '1'  == $ShowRedeemField) {
				return ;
			}

			if ( check_if_pointprice_product_exist_in_cart() ) {
				return ;
			}

			$MemeberShipRestriction = ( 'yes' == get_option( 'rs_restrict_redeem_when_no_membership_plan' ) && function_exists( 'check_plan_exists' ) ) ? ( check_plan_exists( get_current_user_id() ) ? 'yes' : 'no' ) : 'no' ;
			if ( 'yes' ==  $MemeberShipRestriction ) {
				return ;
			}

			$EnabledProductList = array() ;
			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId       = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
				$PointPriceValue = calculate_point_price_for_products( $ProductId ) ;
				if ( empty( $PointPriceValue[ $ProductId ] ) ) {
					continue ;
				}

				$EnabledProductList[] = $PointPriceValue[ $ProductId ] ;
			}

			if ( ! empty( $EnabledProductList ) && '1' == get_option( 'rs_show_hide_message_errmsg_for_point_price_coupon' ) ) {
				?>
				<div class="woocommerce-info"><?php echo wp_kses_post(get_option( 'rs_errmsg_for_redeem_in_point_price_prt' )) ; ?></div>
				<?php
			}
			$MinCartTotalToRedeem          = get_option( 'rs_minimum_cart_total_points' ) ;
			$MaxCartTotalToRedeem          = get_option( 'rs_maximum_cart_total_points' ) ;
			$ErrMsgForMaxCartTotalToRedeem = get_option( 'rs_max_cart_total_redeem_error' ) ;
			$ErrMsgForMinCartTotalToRedeem = get_option( 'rs_min_cart_total_redeem_error' ) ;
			$CartTotal                     = srp_cart_subtotal() ;
			if ( '' != $MinCartTotalToRedeem && ''!=$MaxCartTotalToRedeem ) {
				if ( $CartTotal >= $MinCartTotalToRedeem && $CartTotal <= $MaxCartTotalToRedeem ) {
					self::button_type_redeem_field() ;
				} else {
					if ( '1' == get_option( 'rs_show_hide_maximum_cart_total_error_message' ) ) {
						$CartTotalToReplace         = srp_formatted_price( round_off_type_for_currency( $MaxCartTotalToRedeem ) ) ;
						$CartTotalShortcodeReplaced = str_replace( '[carttotal]' , $CartTotalToReplace , $ErrMsgForMaxCartTotalToRedeem ) ;
						$FinalErrmsg                = str_replace( '[currencysymbol]' , '' , $CartTotalShortcodeReplaced ) ;
						?>
						<div class="woocommerce-error"><?php echo wp_kses_post($FinalErrmsg) ; ?></div>
						<?php
					}
				}
			} else if ( '' != $MinCartTotalToRedeem && '' == $MaxCartTotalToRedeem ) {
				if ( $CartTotal >= $MinCartTotalToRedeem ) {
					self::button_type_redeem_field() ;
				} else {
					if ( '1' == get_option( 'rs_show_hide_minimum_cart_total_error_message' ) ) {
						$CartTotalToReplace         = srp_formatted_price( round_off_type_for_currency( $MinCartTotalToRedeem ) ) ;
						$CartTotalShortcodeReplaced = str_replace( '[carttotal]' , $CartTotalToReplace , $ErrMsgForMinCartTotalToRedeem ) ;
						$FinalErrmsg                = str_replace( '[currencysymbol]' , '' , $CartTotalShortcodeReplaced ) ;
						?>
						<div class="woocommerce-error"><?php echo wp_kses_post($FinalErrmsg) ; ?></div>
						<?php
					}
				}
			} else if ( '' == $MinCartTotalToRedeem && ''!=$MaxCartTotalToRedeem ) {
				if ( $CartTotal <= $MaxCartTotalToRedeem ) {
					self::button_type_redeem_field() ;
				} else {
					if ( '1' == get_option( 'rs_show_hide_maximum_cart_total_error_message' ) ) {
						$CartTotalToReplace         = srp_formatted_price( round_off_type_for_currency( $MaxCartTotalToRedeem ) ) ;
						$CartTotalShortcodeReplaced = str_replace( '[carttotal]' , $CartTotalToReplace , $ErrMsgForMaxCartTotalToRedeem ) ;
						$FinalErrmsg                = str_replace( '[currencysymbol]' , '' , $CartTotalShortcodeReplaced ) ;
						?>
						<div class="woocommerce-error"><?php echo wp_kses_post($FinalErrmsg) ; ?></div>
						<?php
					}
				}
			} else if ( '' == $MinCartTotalToRedeem && '' == $MaxCartTotalToRedeem ) {
				self::button_type_redeem_field() ;
			}
		}

		public static function button_type_redeem_field() {
			$PercentageToRedeem = is_cart() ? get_option( 'rs_percentage_cart_total_redeem' ) : get_option( 'rs_percentage_cart_total_redeem_checkout' ) ;
			if ( empty( $PercentageToRedeem ) ) {
				return ;
			}

			$UserId       = get_current_user_id() ;
			$banning_type = check_banning_type( $UserId ) ;
			if ( 'redeemingonly' == $banning_type || 'both' == $banning_type ) {
				return ;
			}

			$CartWithTax = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) ? WC()->cart->subtotal_ex_tax : WC()->cart->subtotal ;
			if ( $CartWithTax < get_option( 'rs_minimum_cart_total_points' ) ) {
				return ;
			}

			$UserInfo       = get_user_by( 'id' , $UserId ) ;
			$user_role      = is_object( $UserInfo ) ? $UserInfo->roles : array() ;
			$user_role      = implode( '' , $user_role ) ;
			$UserName       = $UserInfo->user_login ;
			$AppliedCoupons = WC()->cart->get_applied_coupons() ;
			$AutoRedeem     = 'auto_redeem_' . strtolower( $UserName ) ;
			if ( in_array( $AutoRedeem , $AppliedCoupons ) ) {
				return ;
			}

			if ( ! self::product_filter_for_redeem_field() ) {
				return ;
			}

			$PointsData = new RS_Points_Data( $UserId ) ;
			$Points     = $PointsData->total_available_points() ;

			$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction() ;
			if ( $minimum_available_points && $Points < $minimum_available_points ) {
				$restriction_msg = str_replace( '[available_points]' , absint( $minimum_available_points ) , get_option( 'rs_available_points_redeem_error' , 'You are eligible to redeem your points only when you have [available_points] Points in your account' ) ) ;
				wc_print_notice( __( $restriction_msg ) , 'error' ) ;
				return ;
			}

			if ( empty( $Points ) ) {
				return ;
			}

			$MinUserPoints = ( '1' != get_user_meta( $UserId , 'rsfirsttime_redeemed' , true ) ) ? get_option( 'rs_first_time_minimum_user_points' ) : get_option( 'rs_minimum_user_points_to_redeem' ) ;
			if ( $Points < $MinUserPoints ) {
				return ;
			}

			$ProductTotal    = array() ;
			$PointPriceValue = array() ;
			$PointPriceType  = array() ;
			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId               = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
				$PointPriceType[]        = check_display_price_type( $ProductId ) ;
				$CheckIfPointPriceEnable = calculate_point_price_for_products( $ProductId ) ;
				if ( ! empty( $CheckIfPointPriceEnable[ $ProductId ] ) ) {
					$PointPriceValue[]       = $CheckIfPointPriceEnable[ $ProductId ] ;
				}

				if ( '2' == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ) {
					$ProductTotal[] = isset( $item[ 'line_subtotal_tax' ] ) ? ( ( 'incl' == get_option( 'woocommerce_tax_display_cart' ) ) ? $item[ 'line_subtotal' ] + $item[ 'line_subtotal_tax' ] : $item[ 'line_subtotal' ] ) : $item[ 'line_subtotal' ] ;
					if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_products' ) && '' != get_option( 'rs_select_products_to_enable_redeeming' ) ) {
						$IncProduct     = get_option( 'rs_select_products_to_enable_redeeming' ) ;
						$IncProduct     = srp_check_is_array( $IncProduct ) ? $IncProduct : explode( ',' , $IncProduct ) ;
						if ( in_array( $ProductId , $IncProduct ) ) {
							$ProductTotal[] = isset( $item[ 'line_subtotal_tax' ] ) ? ( ( 'incl' == get_option( 'woocommerce_tax_display_cart' ) ) ? $item[ 'line_subtotal' ] + $item[ 'line_subtotal_tax' ] : $item[ 'line_subtotal' ] ) : $item[ 'line_subtotal' ] ;
						}
					}
					if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_category' ) && '' != get_option( 'rs_select_category_to_enable_redeeming' ) ) {
						$Category = get_the_terms( $ProductId , 'product_cat' ) ;
						if ( srp_check_is_array( $Category ) ) {
							$IncCategory = get_option( 'rs_select_category_to_enable_redeeming' ) ;
							$IncCategory = srp_check_is_array( $IncCategory ) ? $IncCategory : explode( ',' , $IncCategory ) ;
							foreach ( $Category as $CatObj ) {
								$termid         = $CatObj->term_id ;
								if ( in_array( $termid , $IncCategory ) ) {
									$ProductTotal[] = isset( $item[ 'line_subtotal_tax' ] ) ? ( ( 'incl' == get_option( 'woocommerce_tax_display_cart' ) ) ? $item[ 'line_subtotal' ] + $item[ 'line_subtotal_tax' ] : $item[ 'line_subtotal' ] ) : $item[ 'line_subtotal' ] ;
								}
							}
						}
					}
				}
			}
			if ( srp_check_is_array( $PointPriceValue ) ) {
				return ;
			}

			if ( in_array( 2 , $PointPriceType ) ) {
				return ;
			}

			$Total            = '2' == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ? array_sum( $ProductTotal ) : WC()->cart->subtotal ;
			$RedeemPercentage = RSMemberFunction::redeem_points_percentage( $UserId ) ;
			$PointValue       = wc_format_decimal( get_option( 'rs_redeem_point' ) ) ;
			$ButtonCaption    = is_cart() ? get_option( 'rs_redeeming_button_option_message' ) : get_option( 'rs_redeeming_button_option_message_checkout' ) ;
			$CurrencyValue    = ( $PercentageToRedeem / 100 ) * $Total ;
			$PointsToRedeem   = redeem_point_conversion( $CurrencyValue , $UserId ) ;
			$CurrencyValue    = ( $Points >= $PointsToRedeem ) ? srp_formatted_price( round_off_type_for_currency( $CurrencyValue ) ) : srp_formatted_price( round_off_type_for_currency( redeem_point_conversion( $Points , $UserId , 'price' ) ) ) ;
			$PointsToRedeem   = ( $Points >= $PointsToRedeem ) ? round_off_type( $PointsToRedeem ) : $Points ;
			$Message          = str_replace( '[pointsvalue]' , $CurrencyValue , $ButtonCaption ) ;
			$Message          = str_replace( '[currencysymbol]' , '' , $Message ) ;
			$ButtonMsg        = str_replace( '[cartredeempoints]' , $PointsToRedeem , $Message ) ;
			$DivClass         = is_cart() ? 'sumo_reward_points_cart_apply_discount' : 'sumo_reward_points_checkout_apply_discount' ;
			$FormClass        = is_cart() ? 'rs_button_redeem_cart' : 'rs_button_redeem_checkout' ;
			$ShowRedeemField  = is_checkout() ? get_option( 'rs_show_hide_redeem_field_checkout' ) : '1' ;
			if ( '1' != $ShowRedeemField) {
				return ;
			}

			$extra_message = apply_filters( 'rs_extra_messages_for_redeeming' , '' ) ;
			?>
			<form method="post" class="<?php echo esc_attr($FormClass) ; ?> woocommerce-info">

				<?php if ( $extra_message ) : ?>
					<div class="rs_add_extra_notice">
						<?php echo wp_kses_post(do_shortcode( $extra_message )) ; ?>
					</div>
				<?php endif ; ?>

				<div class="<?php echo esc_attr($DivClass) ; ?>"><?php echo wp_kses_post($ButtonMsg) ; ?>
					<input id="rs_apply_coupon_code_field" class="input-text" type="hidden"  value="<?php echo esc_attr($PointsToRedeem) ; ?>" name="rs_apply_coupon_code_field">
					<input class="<?php echo esc_attr(get_option( 'rs_extra_class_name_apply_reward_points' ) ); ?>" type="submit" id='mainsubmi' value="<?php echo wp_kses_post(get_option( 'rs_redeem_field_submit_button_caption' ) ); ?>" name="rs_apply_coupon_code1" />
				</div>
			</form>
			<?php
		}

		/* Button Redeem Field in Cart and Checkout */

		public static function change_coupon_label( $link, $coupon ) {
			if ( ! is_user_logged_in() ) {
				return $link ;
			}

			$banning_type = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return $link ;
			}

			$CouponObj  = srp_coupon_obj( $coupon ) ;
			$CouponCode = $CouponObj[ 'coupon_code' ] ;
			$UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
			$UserName   = $UserInfo->user_login ;
			if ( strtolower( $CouponCode ) == ( 'sumo_' . strtolower( $UserName ) ) || strtolower( $CouponCode ) == 'auto_redeem_' . strtolower( $UserName ) ) {
				$link       = ' ' . get_option( 'rs_coupon_label_message' ) ;
			}

			return $link ;
		}

		/* Display message when tax is enabled in WooCommerce */

		public static function msg_when_tax_enabled() {

			if ( ! is_user_logged_in() ) {
				return ;
			}
			
			$banning_type = check_banning_type( get_current_user_id() ) ;
			if ( 'redeemingonly' == $banning_type || 'both' == $banning_type ) {
				return ;
			}

			if ( check_if_pointprice_product_exist_in_cart() ) {
				return ;
			}

			if ( 'yes' == get_option( 'woocommerce_calc_taxes' ) && '1' == get_option( 'rs_show_hide_message_notice_for_redeeming' )  ) {
				?>
				<div class="woocommerce-error sumo_reward_points_notice">
					<?php echo wp_kses_post(get_option( 'rs_msg_for_redeem_when_tax_enabled' ) ); ?>
				</div>
				<?php
			}
		}

		public static function hide_coupon_message( $message ) {
			$message = is_checkout() ? self::msg_for_coupon( $message , 'yes' ) : $message ;
			return $message ;
		}

		public static function hide_coupon_field_on_checkout( $message ) {
			if ( is_checkout() ) {
				if ('2' == get_option( 'rs_show_hide_coupon_field_checkout' )  ) {
					$message = false ;
				}

				$message = self::msg_for_coupon( $message , 'no' ) ;
			}
			if ( 'yes' == get_option( 'rs_enable_disable_auto_redeem_checkout' ) ) {
				$message = true ;
			}

			return $message ;
		}

		public static function msg_for_coupon( $message, $hidemsg ) {
			if ( isset( $_REQUEST[ 'rs_apply_coupon_code' ] ) || isset( $_REQUEST[ 'rs_apply_coupon_code1' ] ) || isset( $_REQUEST[ 'rs_apply_coupon_code2' ] ) ) {
				if ( empty( $_REQUEST[ 'rs_apply_coupon_code_field' ] ) ) {
					return $message ;
				}

				if ( 'no' == $hidemsg && 'yes' == get_option( 'woocommerce_enable_coupons' ) ) {
					return true ;
				}

				if ( 'yes' == $hidemsg && '2' == get_option( 'rs_show_hide_coupon_field_checkout' ) ) {
					return '' ;
				}
			}
			return $message ;
		}

		/* Error message for SUMO Coupon */

		public static function error_message_for_sumo_coupon( $msg, $msg_code, $object ) {
			if ( ! is_user_logged_in() ) {
				return $msg ;
			}

			$CouponObj  = new WC_Coupon( $object ) ;
			$CouponObj  = srp_coupon_obj( $CouponObj ) ;
			$CouponCode = $CouponObj[ 'coupon_code' ] ;
			$UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
			$UserName   = $UserInfo->user_login ;
			$Redeem     = 'sumo_' . strtolower( $UserName ) ;
			$AutoRedeem = 'auto_redeem_' . strtolower( $UserName ) ;
			if ( $CouponCode == $AutoRedeem ) {
				if ( 2 == get_option( 'rs_show_hide_auto_redeem_not_applicable' ) ) {
					return $msg ;
				}
			}

			if ( $CouponCode == $Redeem ) {
				$msg_code = ( 104 == $msg_code ) ? 204 : $msg_code ;
			}

			switch ( $msg_code ) {
				case 204:
					$msg = get_option( 'rs_coupon_applied_individual_error_msg' ) ;
					break ;
				case 109:
				case 113:
				case 101:
					$msg = ( $CouponCode == $AutoRedeem ) ? get_option( 'rs_auto_redeem_not_applicable_error_message' ) : $msg ;
					break ;
				default:
					$msg = $msg ;
					break ;
			}
			return $msg ;
		}

		/* Success message for SUMO Coupon */

		public static function success_message_for_sumo_coupon( $msg, $msg_code, $Obj ) {
			if ( ! is_user_logged_in() ) {
				return $msg ;
			}

			$CouponObj  = new WC_Coupon( $Obj ) ;
			$CouponObj  = srp_coupon_obj( $CouponObj ) ;
			$CouponCode = $CouponObj[ 'coupon_code' ] ;
			update_option( 'appliedcouponcode' , $CouponCode ) ; //Update to Replace Message which is displayed while coupon removed.
			$UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
			$UserName   = $UserInfo->user_login ;
			$AutoRedeem = 'auto_redeem_' . strtolower( $UserName ) ;
			if ( $AutoRedeem == $CouponCode ) {
				$msg_code   = ( 200 == $msg_code ) ? 501 : $msg_code ;
			}

			switch ( $msg_code ) {
				case 501:
					$msg = '1' == get_option( 'rs_show_hide_message_for_redeem' ) ? get_option( 'rs_automatic_success_coupon_message' , 'AutoReward Points Successfully Added' ) : '' ;
					break ;
				case 200:
					if ( isset( $_REQUEST[ 'rs_apply_coupon_code' ] ) || isset( $_REQUEST[ 'rs_apply_coupon_code1' ] ) ) {
						$msg = get_option( 'rs_show_hide_message_for_redeem' ) == '1' ? __( get_option( 'rs_success_coupon_message' ) , 'rewardsystem' ) : '' ;
					}

					break ;
				default:
					$msg = '' ;
					break ;
			}
			return $msg ;
		}

		/* Replace Remove Message for SUMO Coupon  */

		public static function replace_msg_for_remove_coupon( $message ) {
			if ( ! is_user_logged_in() ) {
				return $message ;
			}

			$woo_msg = __( 'Coupon has been removed.' , 'woocommerce' ) ;
			if ( $message != $woo_msg ) {
				return $message ;
			}

			if ( empty( get_option( 'rs_remove_redeem_points_message' ) ) ) {
				return $message ;
			}

			$CouponCode = get_option( 'appliedcouponcode' ) ;
			$UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
			$UserName   = $UserInfo->user_login ;
			$Redeem     = 'sumo_' . strtolower( "$UserName" ) ;
			$AutoRedeem = 'auto_redeem_' . strtolower( "$UserName" ) ;
			if ( $Redeem == $CouponCode || $AutoRedeem == $CouponCode ) {
				$message    = __( get_option( 'rs_remove_redeem_points_message' ) , 'rewardsystem' ) ;
			}

			return $message ;
		}
		
		/* Validate Redeeming for Specific Gateway  */

		public static function validate_redeeming_for_specific_gateways( $data, $error ) {

			if ( ! is_user_logged_in() ) {
				return ;
			}

			$restrict_gateway = get_option( 'rs_select_payment_gateway_for_restrict_redeem_points' ) ;
			if ( ! srp_check_is_array( $restrict_gateway ) ) {
				return ;
			}

			$payment_method = isset( $data[ 'payment_method' ] ) ? $data[ 'payment_method' ] : '' ;
			if ( ! in_array( $payment_method, $restrict_gateway ) ) {
				return ;
			}

			$applied_coupons = WC()->cart->get_applied_coupons() ;
			if ( ! srp_check_is_array( $applied_coupons ) ) {
				return ;
			}

			$user_id = get_current_user_id() ;
			$user    = get_user_by( 'id', $user_id ) ;
			if ( ! is_object( $user ) ) {
				return ;
			}

			$user_name   = $user->user_login ;
			$redeem      = 'sumo_' . strtolower( "$user_name" ) ;
			$auto_redeem = 'auto_redeem_' . strtolower( "$user_name" ) ;
			
			$coupon_id = 0;
			foreach ( $applied_coupons as $coupon_code ) {
				if ( $coupon_code == $redeem || $coupon_code == $auto_redeem) {
					$coupon = new WC_Coupon($coupon_code);
					if (!is_object($coupon)) {
						continue;
					}
					
					$coupon_id = $coupon->get_id();
					WC()->cart->remove_coupon( $coupon_code );
				}
			}
			
			if (!$coupon_id) {
				return;
			}
			
			wp_trash_post($coupon_id);
			
			$error->add( 'error', get_option('rs_redeeming_gateway_restriction_error', 'Redeeming is not applicable to the payment gateway you have selected. Hence, the discount applied through points has been removed.') ) ;
		}

		public static function unset_gateways_for_excluded_product_to_redeem( $gateways ) {
			if ( 'yes' != get_option( 'rs_exclude_products_for_redeeming' ) ) {
				return $gateways ;
			}

			global $woocommerce ;
			if ( ! srp_check_is_array( $woocommerce->cart->cart_contents ) ) {
				return $gateways ;
			}

			if ( empty( get_option( 'rs_exclude_products_to_enable_redeeming' ) ) ) {
				return $gateways ;
			}

			foreach ( $woocommerce->cart->cart_contents as $key => $values ) {
				$ExcProducts = srp_check_is_array( get_option( 'rs_exclude_products_to_enable_redeeming' ) ) ? get_option( 'rs_exclude_products_to_enable_redeeming' ) : explode( ',' , get_option( 'rs_exclude_products_to_enable_redeeming' ) ) ;
				if ( in_array( $values[ 'product_id' ] , $ExcProducts ) ) {
					foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
						if ( 'reward_gateway' != $gateway->id ) {
							continue ;
						}

						unset( $gateways[ $gateway->id ] ) ;
					}
				}
			}

			return 'NULL' != $gateways ? $gateways : array() ;
		}
		
		public static function get_minimum_available_points_for_redeeming_restriction() {
			
			$user = get_user_by( 'id' , get_current_user_id() ) ;
			if ( ! is_object( $user ) ) {
				return 0;
			}
			
			$minimum_available_points = 0 ;
			
			if ( 'yes' != get_option( 'rs_minimum_available_points_restriction_is_enabled' , 'no' ) ) {
				return $minimum_available_points;
			}
			
			if ( '1' == get_option( 'rs_minimum_available_points_based_on' , '1' ) ) {
					$minimum_available_points = ( float ) get_option( 'rs_available_points_based_redeem' , '0' ) ;
			} else {
					$user_roles = $user->roles ;
				if (!srp_check_is_array($user_roles)) {
					return $minimum_available_points;
				}
					
					$minimum_points_based_on_roles = array();
				foreach ($user_roles as $role) {
					$minimum_points_based_on_roles[] = ( float ) get_option( 'rs_minimum_available_points_to_redeem_for_' . $role , '0') ;
				}
					
					$minimum_available_points = max($minimum_points_based_on_roles);
			}
						
			return $minimum_available_points;
		}

	}

	RSRedeemingFrontend::init() ;
}
