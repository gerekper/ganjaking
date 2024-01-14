<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSRedeemingFrontend' ) ) {

	class RSRedeemingFrontend {

		public static function init() {

			add_action( 'wp', array( __CLASS__, 'redeem_points_for_user_automatically' ) );

			add_action( 'wp_head', array( __CLASS__, 'redeem_point_for_user' ) );

			// Trash Redeeming Coupons when cart is empty.
			add_action( 'woocommerce_cart_is_empty', array( __CLASS__, 'trash_sumo_coupon_if_cart_empty' ), 10 );
			// Trash Redeeming Coupons when coupon is removed .
			add_action( 'woocommerce_removed_coupon', array( __CLASS__, 'trash_sumo_coupon_is_removed' ), 10, 1 );

			add_action( 'rs_delete_coupon_based_on_cron', array( __CLASS__, 'trash_sumo_coupon_based_on_cron_time' ), 10, 1 );

			add_action( 'woocommerce_removed_coupon', array( __CLASS__, 'unset_session' ) );

			add_action( 'woocommerce_after_calculate_totals', array( __CLASS__, 'trash_sumo_coupon_if_restricted' ), 10, 1 );

			add_action( 'woocommerce_before_cart_table', array( __CLASS__, 'validation_for_redeeming' ) );

			add_action( 'woocommerce_after_cart_totals', array( __CLASS__, 'redeem_field_based_on_settings' ) );

			add_action( 'woocommerce_after_checkout_form', array( __CLASS__, 'redeem_field_based_on_settings' ) );

			if ( '1' === get_option( 'rs_reward_point_troubleshoot_after_cart' ) ) {
				add_action( 'woocommerce_after_cart_table', array( __CLASS__, 'default_redeem_field_in_cart_and_checkout' ) );
			} elseif ( '2' === get_option( 'rs_reward_point_troubleshoot_after_cart' ) ) {
				add_action( 'woocommerce_cart_coupon', array( __CLASS__, 'default_redeem_field_in_cart_and_checkout' ) );
			} else {
				add_action( 'woocommerce_cart_actions', array( __CLASS__, 'default_redeem_field_in_cart_and_checkout' ) );
			}

			add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'default_redeem_field_in_cart_and_checkout' ), get_option( 'rs_redeeming_field_hook_priority_in_checkout', 10 ) );

			// add_action( 'woocommerce_review_order_after_cart_contents', array( __CLASS__, 'default_redeem_field_in_cart_and_checkout' ), get_option( 'rs_redeeming_field_hook_priority_in_checkout', 10 ) );

			add_filter( 'woocommerce_cart_item_removed_title', array( __CLASS__, 'update_coupon_amount' ), 10, 1 );

			add_filter( 'woocommerce_update_cart_action_cart_updated', array( __CLASS__, 'update_coupon_amount' ), 10, 1 );

			if ( '1' == get_option( 'rs_message_before_after_cart_table' ) ) {
				if ( '1' == get_option( 'rs_reward_point_troubleshoot_before_cart' ) ) {
					add_action( 'woocommerce_before_cart', array( __CLASS__, 'messages_for_redeeming' ) );
				} else {
					add_action( 'woocommerce_before_cart_table', array( __CLASS__, 'messages_for_redeeming' ) );
				}
			} else {
				add_action( 'woocommerce_after_cart_table', array( __CLASS__, 'messages_for_redeeming' ) );
			}
			add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'messages_for_redeeming' ) );

			add_filter( 'woocommerce_cart_totals_coupon_label', array( __CLASS__, 'change_coupon_label' ), get_option( 'rs_change_coupon_priority_value', 1 ), 2 );

			add_filter( 'woocommerce_coupons_enabled', array( __CLASS__, 'hide_coupon_field_on_checkout' ) );

			add_filter( 'woocommerce_checkout_coupon_message', array( __CLASS__, 'hide_coupon_message' ) );

			add_filter( 'woocommerce_coupon_error', array( __CLASS__, 'error_message_for_sumo_coupon' ), 10, 3 );

			add_filter( 'woocommerce_coupon_message', array( __CLASS__, 'success_message_for_sumo_coupon' ), 10, 3 );

			add_filter( 'woocommerce_add_message', array( __CLASS__, 'replace_msg_for_remove_coupon' ), 10, 1 );

			add_filter( 'woocommerce_available_payment_gateways', array( __CLASS__, 'unset_gateways_for_excluded_product_to_redeem' ), 10, 1 );

			add_action( 'woocommerce_after_checkout_validation', array( __CLASS__, 'validate_redeeming_for_specific_gateways' ), 11, 2 );
		}

		/*
		 * Trash Sumo Coupons.
		 * */

		public static function trash_sumo_coupon( $cartobj ) {

			self::trash_sumo_coupon_if_restricted( $cartobj );

			self::trash_sumo_coupon_if_restricted_based_on_available_points( $cartobj );
		}

		/*
		 * Trash Sumo Coupons when it is removed.
		 * */

		public static function trash_sumo_coupon_is_removed( $coupon_code ) {

			$UserInfo = get_user_by( 'id', get_current_user_id() );
			if ( ! is_object( $UserInfo ) ) {
				return;
			}

			// Check ajax referer when remove coupon button is clicked.
			if ( ! check_ajax_referer( 'remove-coupon', 'security', false ) ) {
				return;
			}

			$Redeem     = 'sumo_' . strtolower( "$UserInfo->user_login" );
			$AutoRedeem = 'auto_redeem_' . strtolower( "$UserInfo->user_login" );

			if ( $Redeem == $coupon_code || $AutoRedeem == $coupon_code ) {
				$coupon = new WC_Coupon( $coupon_code );
				if ( is_object( $coupon ) && $coupon->get_id() ) {
					wp_trash_post( $coupon->get_id() );
				}
			}
		}

		/*
		 *  Trash SUMO Coupons if Cart is Empty.
		 * */

		public static function trash_sumo_coupon_if_cart_empty() {

			if ( ! WC()->cart->is_empty() ) {
				return;
			}

			$CouponId = get_user_meta( get_current_user_id(), 'redeemcouponids', true );
			if ( ! empty( $CouponId ) ) {
				wp_trash_post( $CouponId );
			}

			$CouponId = get_user_meta( get_current_user_id(), 'auto_redeemcoupon_ids', true );
			if ( ! empty( $CouponId ) ) {
				wp_trash_post( $CouponId );
			}
		}

		/* Trash SUMO Coupon when it satisfies the Reward Restriction */

		public static function trash_sumo_coupon_if_restricted( $CartObj ) {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$MinCartTotalToRedeem = (float) get_option( 'rs_minimum_cart_total_points' );
			$MaxCartTotalToRedeem = (float) get_option( 'rs_maximum_cart_total_points' );
			if ( empty( $MinCartTotalToRedeem ) && empty( $MaxCartTotalToRedeem ) ) {
				return;
			}

			$CartSubtotal = srp_cart_subtotal();
			$CouponId     = 0;
			$UserInfo     = get_user_by( 'id', get_current_user_id() );
			$Username     = $UserInfo->user_login;
			$Redeem       = 'sumo_' . strtolower( "$Username" );
			$AutoRedeem   = 'auto_redeem_' . strtolower( "$Username" );
			foreach ( $CartObj->get_applied_coupons() as $CouponCode ) {
				if ( $CouponCode == $Redeem ) {
					$CouponId = get_user_meta( get_current_user_id(), 'redeemcouponids', true );
				} elseif ( $CouponCode == $AutoRedeem ) {
					$CouponId = get_user_meta( get_current_user_id(), 'auto_redeemcoupon_ids', true );
				}
				if ( ( ! empty( $MinCartTotalToRedeem ) && $CartSubtotal < $MinCartTotalToRedeem ) || ( ! empty( $MaxCartTotalToRedeem ) && $CartSubtotal > $MaxCartTotalToRedeem ) ) {
					if ( ! empty( $CouponId ) ) {
						wp_trash_post( $CouponId );
					}
				}
			}
		}

		/* Trash SUMO Coupon if restricted based on available points */

		public static function trash_sumo_coupon_if_restricted_based_on_available_points( $cartobj ) {

			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( ! is_object( $cartobj ) || ! srp_check_is_array( $cartobj->get_applied_coupons() ) ) {
				return;
			}

			$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction();
			if ( ! $minimum_available_points ) {
				return;
			}

			$pointsdata = new RS_Points_Data( get_current_user_id() );
			if ( ! is_object( $pointsdata ) ) {
				return;
			}

			$points   = $pointsdata->total_available_points();
			$couponid = 0;
			$userinfo = get_user_by( 'id', get_current_user_id() );
			if ( ! is_object( $userinfo ) ) {
				return;
			}

			$username   = $userinfo->user_login;
			$redeem     = 'sumo_' . strtolower( "$username" );
			$autoredeem = 'auto_redeem_' . strtolower( "$username" );
			$coupon_id  = '';
			foreach ( $cartobj->get_applied_coupons() as $couponcode ) {
				$coupon    = new WC_Coupon( $couponcode );
				$coupon_id = is_object( $coupon ) ? $coupon->id : '';
			}

			if ( $coupon_id && $points < $minimum_available_points ) {
				wp_trash_post( $coupon_id );
			}
		}

		/**
		 * Trash SUMO Coupon when Cron time Reached.
		 *
		 * @param int $coupon_id Coupon ID.
		 * */
		public static function trash_sumo_coupon_based_on_cron_time( $coupon_id ) {
			wp_trash_post( $coupon_id );
		}

		/**
		 * Validate Redeeming in Cart/Checkout.
		 * */
		public static function validation_for_redeeming() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$BanningType = check_banning_type( get_current_user_id() );
			if ( 'redeemingonly' == $BanningType || 'both' == $BanningType ) {
				return;
			}

			$CartSubtotal = srp_cart_subtotal();
			if ( empty( $CartSubtotal ) ) {
				return;
			}

			if ( check_if_pointprice_product_exist_in_cart() ) {
				return;
			}

			if ( '2' === get_option( 'rs_redeem_field_type_option' ) ) {
				return;
			}

			$MemRestrict = 'no';
			if ( 'yes' === get_option( 'rs_restrict_redeem_when_no_membership_plan' ) && function_exists( 'check_plan_exists' ) && get_current_user_id() ) {
				$MemRestrict = check_plan_exists( get_current_user_id() ) ? 'no' : 'yes';
			}

			if ( 'yes' === $MemRestrict ) {
				return;
			}

			$pointsdata               = new RS_Points_Data( get_current_user_id() );
			$points                   = $pointsdata->total_available_points();
			$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction();
			if ( $minimum_available_points && $points < $minimum_available_points ) {
				return;
			}

			$MinCartTotalToRedeem = get_option( 'rs_minimum_cart_total_points' );
			$MaxCartTotalToRedeem = get_option( 'rs_maximum_cart_total_points' );
			if ( ! empty( $MinCartTotalToRedeem ) && ! empty( $MaxCartTotalToRedeem ) ) {
				if ( $CartSubtotal < $MinCartTotalToRedeem && $CartSubtotal > $MaxCartTotalToRedeem ) {
					if ( '1' == get_option( 'rs_show_hide_maximum_cart_total_error_message' ) ) {
						$CurrencyValue = srp_formatted_price( round_off_type_for_currency( $MinCartTotalToRedeem ) );
						$ReplacedMsg   = str_replace( '[carttotal]', $CurrencyValue, get_option( 'rs_min_cart_total_redeem_error' ) );
						$ReplacedMsg   = str_replace( '[currencysymbol]', '', $ReplacedMsg );
						?>
						<div class="woocommerce-error"><?php echo do_shortcode( $ReplacedMsg ); ?></div>
						<?php
					}
				}
			} elseif ( ! empty( $MinCartTotalToRedeem ) && empty( $MaxCartTotalToRedeem ) ) {
				if ( $CartSubtotal < $MinCartTotalToRedeem ) {
					if ( '1' == get_option( 'rs_show_hide_minimum_cart_total_error_message' ) ) {
						$CurrencyValue = srp_formatted_price( round_off_type_for_currency( $MinCartTotalToRedeem ) );
						$ReplacedMsg   = str_replace( '[carttotal]', $CurrencyValue, get_option( 'rs_min_cart_total_redeem_error' ) );
						$ReplacedMsg   = str_replace( '[currencysymbol]', '', $ReplacedMsg );
						?>
						<div class="woocommerce-error"><?php echo do_shortcode( $ReplacedMsg ); ?></div>
						<?php
					}
				}
			} elseif ( empty( $MinCartTotalToRedeem ) && ! empty( $MaxCartTotalToRedeem ) ) {
				if ( $CartSubtotal > $MaxCartTotalToRedeem ) {
					if ( '1' == get_option( 'rs_show_hide_maximum_cart_total_error_message' ) ) {
						$CurrencyValue = srp_formatted_price( round_off_type_for_currency( $MaxCartTotalToRedeem ) );
						$ReplacedMsg   = str_replace( '[carttotal]', $CurrencyValue, get_option( 'rs_max_cart_total_redeem_error' ) );
						$ReplacedMsg   = str_replace( '[currencysymbol]', '', $ReplacedMsg );
						?>
						<div class="woocommerce-error"><?php echo do_shortcode( $ReplacedMsg ); ?></div>
						<?php
					}
				}
			}
		}

		/* Default Redeem Field in Cart/Checkout */

		public static function default_redeem_field_in_cart_and_checkout() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$ShowRedeemField = is_cart() ? get_option( 'rs_redeem_field_type_option' ) : get_option( 'rs_redeem_field_type_option_checkout' );
			if ( '2' === $ShowRedeemField ) {
				return;
			}

			$MemRestrict = 'no';
			if ( 'yes' == get_option( 'rs_restrict_redeem_when_no_membership_plan' ) && function_exists( 'check_plan_exists' ) && get_current_user_id() ) {
				$MemRestrict = check_plan_exists( get_current_user_id() ) ? 'no' : 'yes';
			}

			if ( 'yes' === $MemRestrict ) {
				return;
			}

			$MinCartTotal = get_option( 'rs_minimum_cart_total_points' );
			$MaxCartTotal = get_option( 'rs_maximum_cart_total_points' );

			$CartSubTotal = srp_cart_subtotal();
			if ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
				if ( $CartSubTotal >= $MinCartTotal && $CartSubTotal <= $MaxCartTotal ) {
					self::default_redeem_field();
				}
			} elseif ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
				if ( $CartSubTotal >= $MinCartTotal ) {
					self::default_redeem_field();
				}
			} elseif ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
				if ( $CartSubTotal <= $MaxCartTotal ) {
					self::default_redeem_field();
				}
			} elseif ( empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
				self::default_redeem_field();
			}
		}

		public static function default_redeem_field() {
			if ( ! self::product_filter_for_redeem_field() ) {
				return;
			}

			if ( check_if_pointprice_product_exist_in_cart() ) {
				return;
			}

			$UserId  = get_current_user_id();
			$BanType = check_banning_type( $UserId );
			if ( 'redeemingonly' === $BanType || 'both' === $BanType ) {
				return;
			}

			$PointPriceValue = array();
			$PointPriceType  = array();
			$PointsData      = new RS_Points_Data( $UserId );
			$Points          = $PointsData->total_available_points();
			$UserInfo        = get_user_by( 'id', $UserId );
			$user_role       = is_object( $UserInfo ) ? $UserInfo->roles : array();
			$user_role       = implode( '', $user_role );
			$Username        = $UserInfo->user_login;
			$AutoRedeem      = 'auto_redeem_' . strtolower( $Username );
			$AppliedCoupons  = WC()->cart->get_applied_coupons();

			$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction();
			if ( $minimum_available_points && $Points < $minimum_available_points ) {
				$restriction_msg = str_replace( '[available_points]', absint( $minimum_available_points ), get_option( 'rs_available_points_redeem_error', 'You are eligible to redeem your points only when you have [available_points] Points in your account' ) );
				wc_print_notice( __( $restriction_msg ), 'error' );
				return;
			}

			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId        = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				$PointPriceType[] = check_display_price_type( $ProductId );
				$CheckIfEnable    = calculate_point_price_for_products( $ProductId );
				if ( ! empty( $CheckIfEnable[ $ProductId ] ) ) {
					$PointPriceValue[] = $CheckIfEnable[ $ProductId ];
				}
			}
			if ( $Points > 0 ) {
								$readonly                 = false;
								$predefined_option_values = '';
								$sequence_nos             = '';
				if ( '1' == get_option( 'rs_redeem_field_type_option' ) && 'yes' == get_option( 'rs_default_redeeming_type_enabled' ) ) {
					if ( '1' == get_option( 'rs_default_redeeming_type' ) ) {
						$predefined_option_values = get_option( 'rs_redeeming_predefined_option_values' );
					} else {
						$sequence_nos = get_option( 'rs_redeeming_start_sequence_number' );
					}
				}
				$MinUserPoints = ( '1' != get_user_meta( $UserId, 'rsfirsttime_redeemed', true ) ) ? get_option( 'rs_first_time_minimum_user_points' ) : get_option( 'rs_minimum_user_points_to_redeem' );
				if ( $Points >= $MinUserPoints ) {
					if ( srp_cart_subtotal() >= get_option( 'rs_minimum_cart_total_points' ) ) {
						if ( ! in_array( $AutoRedeem, $AppliedCoupons ) ) {
							if ( ! srp_check_is_array( $PointPriceValue ) && ! in_array( '2', $PointPriceType ) ) {
								if ( is_cart() ) {
									?>
									<div class="fp_apply_reward">
																				<?php
																				if ( $predefined_option_values ) :
																					$predefined_option_values = explode( ',', trim( $predefined_option_values ) );
																					$readonly                 = true;
																					?>
																					<div class="rs-predefined-button-wrapper">
																						<label><?php echo esc_html( get_option( 'rs_redeeming_predefined_points_selection_label', 'Points Selection' ) ); ?></label>
																						<select>
																							<option value="0"><?php echo esc_html( get_option( 'rs_redeeming_predefined_choose_option_label', 'Select the Points' ) ); ?></option>
																							<?php foreach ( $predefined_option_values as $predefined_option_value ) : ?>
																								<option value="<?php echo esc_attr( $predefined_option_value ); ?>"><?php echo esc_html( $predefined_option_value ); ?></option>
																							<?php endforeach; ?>
																						</select>
																					</div>
																					<?php
																				endif;
																				if ( '1' == get_option( 'rs_show_hide_redeem_caption' ) ) {
																					?>
											<label id = "default_field" for="rs_apply_coupon_code_field"><?php echo esc_html( get_option( 'rs_redeem_field_caption' ) ); ?></label>
										<?php } ?>
										<?php $placeholder = '1' == get_option( 'rs_show_hide_redeem_placeholder' ) ? get_option( 'rs_redeem_field_placeholder' ) : ''; ?>
																				<input id="rs_apply_coupon_code_field" class="input-text" 
																				<?php
																				if ( $readonly ) :
																					?>
																					readonly="readonly" <?php endif; ?> type="text" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="" name="rs_apply_coupon_code_field">
										<input class="button <?php echo esc_attr( get_option( 'rs_extra_class_name_apply_reward_points' ) ); ?>" type="submit" id='mainsubmi' value="<?php echo esc_attr( get_option( 'rs_redeem_field_submit_button_caption' ) ); ?>" name="rs_apply_coupon_code">
									</div>
									<div class='rs_warning_message'></div>
									<?php
								} elseif ( is_checkout() && '1' == get_option( 'rs_show_hide_redeem_field_checkout' ) ) {
																		$readonly                 = false;
																		$predefined_option_values = '';
																		$sequence_nos             = '';
									if ( '1' == get_option( 'rs_redeem_field_type_option_checkout' ) && 'yes' == get_option( 'rs_default_redeeming_type_enabled_checkout' ) ) {
										if ( '1' == get_option( 'rs_default_redeeming_type_checkout' ) ) {
											$predefined_option_values = get_option( 'rs_redeeming_predefined_option_values_checkout' );
										} else {
											$sequence_nos = get_option( 'rs_redeeming_start_sequence_number_checkout' );
										}
									}
																		/**
																		 * Hook:rs_extra_messages_for_redeeming.
																		 *
																		 * @since 1.0
																		 */
									$extra_message = apply_filters( 'rs_extra_messages_for_redeeming', '' );
									?>
									<div class="checkoutredeem">
										<div class="woocommerce-info">

											<?php if ( $extra_message ) : ?>
												<div class="rs_add_extra_notice">
													<?php echo do_shortcode( $extra_message ); ?>
												</div>
											<?php endif; ?>

											<?php echo do_shortcode( get_option( 'rs_reedming_field_label_checkout' ) ); ?> 
											<a href="javascript:void(0)" class="redeemit"> <?php echo esc_html( get_option( 'rs_reedming_field_link_label_checkout' ) ); ?></a>
										</div>
									</div>
									<form name="checkout_redeeming" class="checkout_redeeming" method="post">
										<div class="fp_apply_reward">
												<?php
												if ( $predefined_option_values ) :
													$predefined_option_values = explode( ',', trim( $predefined_option_values ) );
													$readonly                 = true;
													?>
													<div class="rs-predefined-button-wrapper">
														<label><?php echo esc_html( get_option( 'rs_redeeming_predefined_points_selection_label_checkout', 'Points Selection' ) ); ?></label>
														<select>
															<option value="0"><?php echo esc_html( get_option( 'rs_redeeming_predefined_choose_option_label_checkout', 'Select the Points' ) ); ?></option>
															<?php foreach ( $predefined_option_values as $predefined_option_value ) : ?>
																<option value="<?php echo esc_attr( $predefined_option_value ); ?>"><?php echo esc_html( $predefined_option_value ); ?></option>
															<?php endforeach; ?>
														</select>
													</div>
													<?php
													endif;
												if ( '1' == get_option( 'rs_show_hide_redeem_caption' ) ) {
													?>
												<label id = "default_field" for="rs_apply_coupon_code_field"><?php echo esc_html( get_option( 'rs_redeem_field_caption' ) ); ?></label>
											<?php } ?>
											<?php $placeholder = '1' == get_option( 'rs_show_hide_redeem_placeholder' ) ? get_option( 'rs_redeem_field_placeholder' ) : ''; ?>
											<input id="rs_apply_coupon_code_field" class="input-text" type="text" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="" name="rs_apply_coupon_code_field" 
																																		  <?php
																																			if ( $readonly ) :
																																				?>
												 readonly="readonly" <?php endif; ?>>
											<input class="button <?php echo esc_attr( get_option( 'rs_extra_class_name_apply_reward_points' ) ); ?>" type="submit" id='mainsubmi' value="<?php echo esc_attr( get_option( 'rs_redeem_field_submit_button_caption' ) ); ?>" name="rs_apply_coupon_code">
										</div>
										<div class='rs_warning_message'></div>
									</form>
									<?php
								}
							}
						}
					} elseif ( '1' == get_option( 'rs_show_hide_minimum_cart_total_error_message' ) ) {
							$CurrencyValue = srp_formatted_price( round_off_type_for_currency( get_option( 'rs_minimum_cart_total_points' ) ) );
							$ReplacedMsg   = str_replace( '[carttotal]', $CurrencyValue, get_option( 'rs_min_cart_total_redeem_error' ) );
							$FinalMsg      = str_replace( '[currencysymbol]', '', $ReplacedMsg );
						?>
							<div class="woocommerce-info"><?php echo do_shortcode( $FinalMsg ); ?></div>
							<?php

					}
				} elseif ( '1' != get_user_meta( $UserId, 'rsfirsttime_redeemed', true ) ) {
					if ( '1' == get_option( 'rs_show_hide_first_redeem_error_message' ) ) {
						$ReplacedMsg = str_replace( '[firstredeempoints]', get_option( 'rs_first_time_minimum_user_points' ), get_option( 'rs_min_points_first_redeem_error_message' ) );
						?>
							<div class="woocommerce-info"><?php echo do_shortcode( $ReplacedMsg ); ?></div>
							<?php
					}
				} elseif ( '1' == get_option( 'rs_show_hide_after_first_redeem_error_message' ) ) {
						$ReplacedMsg = str_replace( '[points_after_first_redeem]', get_option( 'rs_minimum_user_points_to_redeem' ), get_option( 'rs_min_points_after_first_error' ) );
					?>
							<div class="woocommerce-info"><?php echo do_shortcode( $ReplacedMsg ); ?></div>
							<?php

				}
			} elseif ( '1' == get_option( 'rs_show_hide_points_empty_error_message' ) && ! srp_check_is_array( $PointPriceValue ) ) {
				?>
					<div class="woocommerce-info"><?php echo do_shortcode( get_option( 'rs_current_points_empty_error_message' ) ); ?></div>
					<?php

			}
		}

		public static function product_filter_for_redeem_field() {
			if ( '1' == get_option( 'rs_hide_redeeming_field' ) ) {
				return true;
			}

			foreach ( WC()->cart->cart_contents as $item ) {
				if ( 'yes' == get_option( 'rs_exclude_products_for_redeeming' ) ) {
					if ( ! self::check_exc_products( $item ) ) {
						return false;
					}
				}

				if ( 'yes' == get_option( 'rs_exclude_category_for_redeeming' ) ) {
					if ( ! self::check_exc_categories( $item ) ) {
						return false;
					}
				}

				if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_products' ) ) {
					if ( self::check_inc_products( $item ) ) {
						return true;
					}
				}

				if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_category' ) ) {
					if ( self::check_inc_categories( $item ) ) {
						return true;
					}
				}
			}
			return true;
		}

		public static function check_inc_products( $item ) {
			$ProductId       = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
			$IncludeProducts = '' != get_option( 'rs_select_products_to_enable_redeeming' ) ? get_option( 'rs_select_products_to_enable_redeeming' ) : array();
			$IncludeProducts = srp_check_is_array( $IncludeProducts ) ? $IncludeProducts : explode( ',', $IncludeProducts );

			if ( ! srp_check_is_array( $IncludeProducts ) ) {
				return true;
			}

			if ( in_array( $ProductId, $IncludeProducts ) ) {
				return true;
			}

			return false;
		}

		public static function check_exc_products( $item ) {
			$ProductId       = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
			$ExcludeProducts = get_option( 'rs_exclude_products_to_enable_redeeming' ) != '' ? get_option( 'rs_exclude_products_to_enable_redeeming' ) : array();
			$ExcludeProducts = srp_check_is_array( $IncludeProducts ) ? $IncludeProducts : explode( ',', $IncludeProducts );

			if ( ! srp_check_is_array( $ExcludeProducts ) ) {
				return true;
			}

			if ( in_array( $ProductId, $ExcludeProducts ) ) {
				return false;
			}

			return true;
		}

		public static function check_inc_categories( $item ) {
			$ProductId        = $item['product_id'];
			$IncludedCategory = '' != get_option( 'rs_select_category_to_enable_redeeming' ) ? get_option( 'rs_select_category_to_enable_redeeming' ) : array();
			$IncludedCategory = srp_check_is_array( $IncludedCategory ) ? $IncludedCategory : explode( ',', $IncludedCategory );

			if ( ! srp_check_is_array( $IncludedCategory ) ) {
				return true;
			}

			$ProductCat = get_the_terms( $ProductId, 'product_cat' );
			if ( ! srp_check_is_array( $ProductCat ) ) {
				return true;
			}

			foreach ( $ProductCat as $Cat ) {
				if ( in_array( $Cat->term_id, $IncludedCategory ) ) {
					return true;
				}
			}

			return false;
		}

		public static function check_exc_categories( $item ) {
			$ProductId        = $item['product_id'];
			$ExcludedCategory = '' != get_option( 'rs_exclude_category_to_enable_redeeming' ) ? get_option( 'rs_exclude_category_to_enable_redeeming' ) : array();
			$ExcludedCategory = srp_check_is_array( $ExcludedCategory ) ? $ExcludedCategory : explode( ',', $ExcludedCategory );

			if ( ! srp_check_is_array( $ExcludedCategory ) ) {
				return true;
			}

			$ProductCat = get_the_terms( $ProductId, 'product_cat' );
			if ( ! srp_check_is_array( $ProductCat ) ) {
				return true;
			}

			foreach ( $ProductCat as $Cat ) {
				if ( in_array( $Cat->term_id, $ExcludedCategory ) ) {
					return false;
				}
			}

			return true;
		}

		/* Hide Redeeming Field in Cart and Checkout  */

		public static function redeem_field_based_on_settings() {
			if ( ! is_user_logged_in() ) {
				return;
			}
		}

		public static function cart_redeem_field( $Param ) {
			ob_start();
			if ( 'show' == $Param ) {
				$contents = '.fp_apply_reward, .rs_button_redeem_cart{
                                                            display: block;
                                                    }';
			} else {
				$contents = '.fp_apply_reward, .rs_button_redeem_cart{
                                                            display: none;
                                                    }';
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

			self::add_inline_style( $contents );
			$return = ob_get_clean();

			return $return;
		}

		public static function checkout_redeem_field( $Param ) {
			ob_start();
			if ( 'show' == $Param ) {
				$contents = '.checkoutredeem, .rs_button_redeem_checkout{
                                                            display: block;
                                                    }';
			} else {
				$contents = '.checkoutredeem, .rs_button_redeem_checkout{
                                                            display: none;
                                                    }';
			}

			self::add_inline_style( $contents );
			$return = ob_get_clean();

			return $return;
		}

		/**
		 * Add Inline Styles.
		 *
		 * @param string $contents CSS Contents.
		 * */
		public static function add_inline_style( $contents ) {

			 wp_register_style( 'fp-srp-redeeming-field-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
			 wp_enqueue_style( 'fp-srp-redeeming-field-style' );
			 wp_add_inline_style( 'fp-srp-redeeming-field-style', $contents );
		}

		/* Update Coupon Amount */

		public static function update_coupon_amount( $BoolVal ) {
			if ( ! is_user_logged_in() ) {
				return $BoolVal;
			}

			$AppliedCoupons = WC()->cart->get_applied_coupons();
			if ( ! srp_check_is_array( $AppliedCoupons ) ) {
				return $BoolVal;
			}

			$points_data      = new RS_Points_Data( get_current_user_id() );
			$available_points = $points_data->total_available_points();
			if ( ! $available_points ) {
				return $BoolVal;
			}

			// Calculate totals.
			WC()->cart->calculate_totals();

			$CartTotal            = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) ? WC()->cart->subtotal : WC()->cart->subtotal_ex_tax;
			$CartTotal            = $CartTotal - self::get_coupon_amount_without_redeeming_coupons();
			$MinCartTotal         = get_option( 'rs_minimum_cart_total_points' );
			$MaxCartTotal         = get_option( 'rs_maximum_cart_total_points' );
			$ProductTotal         = self::get_sum_of_selected_products();
			$RedeemValue          = 1 == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ? $CartTotal : $ProductTotal;
			$overall_max_discount = self::get_max_discount_value();
			foreach ( $AppliedCoupons as $Code ) {
				$CouponObj  = new WC_Coupon( $Code );
				$CouponObj  = srp_coupon_obj( $CouponObj );
				$CouponAmnt = $CouponObj['coupon_amount'];
				$CouponId   = $CouponObj['coupon_id'];
				$UserInfo   = get_user_by( 'id', get_current_user_id() );
				$Username   = $UserInfo->user_login;
				$Redeem     = 'sumo_' . strtolower( "$Username" );
				$AutoRedeem = 'auto_redeem_' . strtolower( "$Username" );
				if ( ( $Code != $Redeem ) && ( $Code != $AutoRedeem ) ) {
					continue;
				}

				if ( $Code == $Redeem && '1' == get_option( 'rs_max_redeem_discount' ) ) {
					continue;
				}

				if ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
					if ( $CartTotal < $MinCartTotal || $CartTotal > $MaxCartTotal ) {
						if ( ! empty( $CouponId ) ) {
							wp_trash_post( $CouponId );
						}
					}
				} elseif ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
					if ( $CartTotal < $MinCartTotal ) {
						if ( ! empty( $CouponId ) ) {
							wp_trash_post( $CouponId );
						}
					}
				} elseif ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
					if ( $CartTotal > $MaxCartTotal ) {
						if ( ! empty( $CouponId ) ) {
							wp_trash_post( $CouponId );
						}
					}
				}

				$MaxDiscountAmntForDefault = ! empty( get_option( 'rs_percent_max_redeem_discount' ) ) ? ( get_option( 'rs_percent_max_redeem_discount' ) / 100 ) * $RedeemValue : $RedeemValue;
				$MaxDiscountAmntForButton  = ! empty( get_option( 'rs_percentage_cart_total_redeem' ) ) ? ( get_option( 'rs_percentage_cart_total_redeem' ) / 100 ) * $RedeemValue : $RedeemValue;
				$MaxDiscountAmntForButton  = ! empty( $MaxDiscountAmntForDefault ) ? $MaxDiscountAmntForDefault : $MaxDiscountAmntForButton;

				if ( $AutoRedeem == $Code ) {
					$Discount = ! empty( get_option( 'rs_percentage_cart_total_auto_redeem' ) ) ? get_option( 'rs_percentage_cart_total_auto_redeem' ) / 100 * $RedeemValue : $RedeemValue;
				} else {
					$Discount = ( 2 == get_option( 'rs_redeem_field_type_option' ) ) ? $MaxDiscountAmntForButton : $MaxDiscountAmntForDefault;
				}

				$available_points_on_conversion = redeem_point_conversion( $available_points, get_current_user_id(), 'price' );
				$Discount                       = $Discount > $available_points_on_conversion ? $available_points_on_conversion : $Discount;
				if ( ! $Discount || $Discount > $available_points ) {
					continue;
				}

				if ( '1' === get_option( 'rs_select_redeeming_based_on' ) ) {
					$Discount = self::srp_get_maximum_redeem_points_based_on_product_total();
				}

				update_post_meta( $CouponId, 'coupon_amount', $Discount );
			}
			return $BoolVal;
		}

		public static function unset_session() {
			// Check ajax referer when remove button is clicked.
			if ( ! check_ajax_referer( 'remove-coupon', 'security', false ) ) {
				return;
			}
			WC()->session->set( 'auto_redeemcoupon', 'no' );
		}

		/* Auto Redeeming in Cart and Checkout */

		public static function redeem_points_for_user_automatically() {

			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( ! is_object( WC()->cart ) ) {
				return;
			}

			$BanningType = check_banning_type( get_current_user_id() );
			if ( 'redeemingonly' == $BanningType || 'both' == $BanningType ) {
				return;
			}

			if ( empty( WC()->cart->get_cart_contents_count() ) ) {
				WC()->session->set( 'auto_redeemcoupon', 'yes' );
				foreach ( WC()->cart->applied_coupons as $Code ) {
					WC()->cart->remove_coupon( $Code );
				}

				return;
			}

			$UserId     = get_current_user_id();
			$PointsData = new RS_Points_Data( $UserId );
			$Points     = $PointsData->total_available_points();

			$UserInfo  = get_user_by( 'id', $UserId );
			$user_role = is_object( $UserInfo ) ? $UserInfo->roles : array();
			$user_role = implode( '', $user_role );

			$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction();
			if ( $minimum_available_points && $Points < $minimum_available_points ) {
				return;
			}

			if ( empty( $Points ) ) {
				return;
			}

			if ( $Points < get_option( 'rs_first_time_minimum_user_points' ) ) {
				return;
			}

			if ( $Points < get_option( 'rs_minimum_user_points_to_redeem' ) ) {
				return;
			}

			if ( check_if_pointprice_product_exist_in_cart() ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_disable_auto_redeem_points' ) ) {
				return;
			}

			$CartSubtotal = srp_cart_subtotal();

			$MinCartTotal = get_option( 'rs_minimum_cart_total_points' );
			$MaxCartTotal = get_option( 'rs_maximum_cart_total_points' );

			if ( is_cart() ) {
				self::auto_redeeming_in_cart( $UserId, $Points, $CartSubtotal, $MaxCartTotal, $MinCartTotal );
			}

			if ( is_checkout() ) {
				self::auto_redeeming_in_checkout( $UserId, $Points, $CartSubtotal, $MaxCartTotal, $MinCartTotal );
			}
		}

		public static function auto_redeeming_in_cart( $UserId, $Points, $CartSubtotal, $MaxCartTotal, $MinCartTotal ) {

			if ( '1' === get_option( 'rs_select_redeeming_based_on' ) ) {
				$redeem_points = self::srp_get_maximum_redeem_points_based_on_product_total();
				self::auto_redeeming( $UserId, $redeem_points );
			} elseif ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
				if ( $CartSubtotal >= $MinCartTotal && $CartSubtotal <= $MaxCartTotal ) {
					self::auto_redeeming( $UserId, $Points );
				}
			} elseif ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
				if ( $CartSubtotal >= $MinCartTotal ) {
					self::auto_redeeming( $UserId, $Points );
				}
			} elseif ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
				if ( $CartSubtotal <= $MaxCartTotal ) {
					self::auto_redeeming( $UserId, $Points );
				}
			} elseif ( empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
				self::auto_redeeming( $UserId, $Points );
			}
		}

		public static function auto_redeeming_in_checkout( $UserId, $Points, $CartSubtotal, $MaxCartTotal, $MinCartTotal ) {
			if ( isset( $_GET['remove_coupon'] ) ) {
				WC()->session->set( 'auto_redeemcoupon', 'no' );
			}

			if ( 'yes' != get_option( 'rs_enable_disable_auto_redeem_checkout' ) ) {
				return;
			}

			self::auto_redeeming_in_cart( $UserId, $Points, $CartSubtotal, $MaxCartTotal, $MinCartTotal );
		}

		public static function auto_redeeming( $UserId, $Points ) {
			if ( 'no' == WC()->session->get( 'auto_redeemcoupon' ) ) {
				return;
			}

			if ( ! SRP_Coupon_Validator::is_valid(get_current_user_id(), 0)) {
				WC()->session->set( 'auto_redeemcoupon', 'no' );
				return;
			}

			$PointPriceType  = array();
			$PointPriceValue = array();
			$UserInfo        = get_user_by( 'id', $UserId );
			$UserName        = $UserInfo->user_login;

			if ( WC()->cart->has_discount( 'auto_redeem_' . strtolower( $UserName ) ) ) {
				return;
			}

			// Need to Calculate Totals for Auto Redeeming on using Order Again in My Account View Orders Page [Added in V24.4.1].
			if ( is_cart() ) {
				WC()->cart->calculate_totals();
			}

			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId        = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				$PointPriceType[] = check_display_price_type( $ProductId );
				$CheckIfEnable    = calculate_point_price_for_products( $ProductId );
				if ( ! empty( $CheckIfEnable[ $ProductId ] ) ) {
					$PointPriceValue[] = $CheckIfEnable[ $ProductId ];
				}
			}
			if ( srp_check_is_array( $PointPriceValue ) ) {
				return;
			}

			if ( in_array( 2, $PointPriceType ) ) {
				return;
			}

			$coupon_code = SRP_Coupon_Handler::create_coupon($UserId, $Points, 'auto_redeem');

			if ( ! empty( get_option( 'rs_minimum_redeeming_points' ) ) && empty( get_option( 'rs_maximum_redeeming_points' ) ) ) {
				if ( $CouponAmnt > get_option( 'rs_minimum_redeeming_points' ) ) {
					WC()->cart->add_discount( $coupon_code );
				}
			}

			if ( ! empty( get_option( 'rs_maximum_redeeming_points' ) ) && empty( get_option( 'rs_minimum_redeeming_points' ) ) ) {
				if ( $CouponAmnt < get_option( 'rs_maximum_redeeming_points' ) ) {
					WC()->cart->add_discount( $coupon_code );
				}
			}

			if ( get_option( 'rs_minimum_redeeming_points' ) == get_option( 'rs_maximum_redeeming_points' ) ) {
				if ( ( get_option( 'rs_minimum_redeeming_points' ) == $CouponAmnt ) && ( get_option( 'rs_maximum_redeeming_points' ) == $CouponAmnt ) ) {
					WC()->cart->add_discount( $coupon_code );
				}
			}

			if ( empty( get_option( 'rs_minimum_redeeming_points' ) ) && empty( get_option( 'rs_maximum_redeeming_points' ) ) ) {
				WC()->cart->add_discount( $coupon_code );
			}

			if ( ! empty( get_option( 'rs_minimum_redeeming_points' ) ) && ! empty( get_option( 'rs_maximum_redeeming_points' ) ) ) {
				if ( ( $CouponAmnt >= get_option( 'rs_minimum_redeeming_points' ) ) && ( $CouponAmnt <= get_option( 'rs_maximum_redeeming_points' ) ) ) {
					WC()->cart->add_discount( $coupon_code );
				}
			}
		}

		public static function redeem_point_for_user() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( isset( $_REQUEST['rs_apply_coupon_code'] ) || isset( $_REQUEST['rs_apply_coupon_code1'] ) || isset( $_REQUEST['rs_apply_coupon_code2'] ) ) {

				if ( ! isset( $_REQUEST['rs_apply_coupon_code_field'] ) ) {
					return;
				}

				if ( empty( $_REQUEST['rs_apply_coupon_code_field'] ) ) {
					return;
				}

				$redeeming_value = wc_clean( wp_unslash( $_REQUEST['rs_apply_coupon_code_field'] ) );

				if ( ! SRP_Coupon_Validator::is_valid(get_current_user_id(), $redeeming_value)) {
					return;
				}

				if ( '1' !== get_option( 'rs_select_redeeming_based_on' ) ) {
					$max_redeem = get_option( 'rs_maximum_redeeming_points' );
					if ( '' !== $max_redeem && $redeeming_value > $max_redeem ) {
						wc_add_notice( __( do_shortcode( get_option( 'rs_maximum_redeem_point_error_message_for_button_type' ) ) ), 'error' );
						$redeeming_value = $max_redeem;
					}
				} else {
					$max_redeem = self::srp_get_maximum_redeem_points_based_on_product_total();
					if ( '' !== $max_redeem && $redeeming_value > $max_redeem ) {
						wc_add_notice( __( do_shortcode( get_option( 'rs_maximum_redeem_point_error_message_for_button_type' ) ) ), 'error' );
						$redeeming_value = $max_redeem;
					}
				}

				$redeeming_value = floatval( str_replace( wc_get_price_decimal_separator(), '.', $redeeming_value ) );

				if ( is_cart() ) {
					if ( 'yes' == get_option( 'rs_default_redeeming_type_enabled' ) && '1' == get_option( 'rs_redeem_field_type_option' ) ) {
						if ( '1' == get_option( 'rs_default_redeeming_type' ) ) {
							$option_values = trim( get_option( 'rs_redeeming_predefined_option_values' ) );
							$option_values = '' != $option_values ? explode( ',', $option_values ) : '';
							if ( srp_check_is_array( $option_values ) && ! in_array( $redeeming_value, $option_values ) ) {
								wc_add_notice( __( 'Please select values based on predefined options', 'rewardsystem' ), 'error' );
								return;
							}
						} else {
							$seq_nos = get_option( 'rs_redeeming_start_sequence_number' );
							if ( $seq_nos && 0 != $redeeming_value % $seq_nos ) {
								wc_add_notice( str_replace( '{multiplier_value}', '<b>' . $seq_nos . '</b>', get_option( 'rs_redeeming_start_sequence_msg', 'Please enter the points value multiples of {multiplier_value}' ) ), 'error' );
								return;
							}
						}
					}
				}

				if ( is_checkout() ) {
					if ( '1' == get_option( 'rs_show_hide_redeem_field_checkout' ) && 'yes' == get_option( 'rs_default_redeeming_type_enabled_checkout' ) && '1' == get_option( 'rs_redeem_field_type_option_checkout' ) ) {
						if ( '1' == get_option( 'rs_default_redeeming_type_checkout' ) ) {
							$option_values = trim( get_option( 'rs_redeeming_predefined_option_values_checkout' ) );
							$option_values = '' != $option_values ? explode( ',', $option_values ) : '';
							if ( srp_check_is_array( $option_values ) && ! in_array( $redeeming_value, $option_values ) ) {
								wc_add_notice( __( 'Please select values based on predefined options', 'rewardsystem' ), 'error' );
								return;
							}
						} else {
							$seq_nos = get_option( 'rs_redeeming_start_sequence_number_checkout' );
							if ( $seq_nos && 0 != $redeeming_value % $seq_nos ) {
								wc_add_notice( str_replace( '{multiplier_value}', '<b>' . $seq_nos . '</b>', get_option( 'rs_redeeming_start_sequence_msg_checkout', 'Please enter the points value multiples of {multiplier_value}' ) ), 'error' );
								return;
							}
						}
					}
				}

				$coupon_code = SRP_Coupon_Handler::create_coupon(get_current_user_id(), $redeeming_value, 'sumo');

				if ( WC()->cart->has_discount( $coupon_code ) ) {
					return;
				}

				WC()->cart->add_discount( $coupon_code );
				if ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) && 'incl' == get_option( 'woocommerce_tax_display_shop' ) && 'incl' == get_option( 'woocommerce_tax_display_cart' ) ) {
					if ( 'yes' == get_option( 'rs_enable_redeem_point_without_incl_tax' ) ) {
						$discount = WC()->cart->get_coupon_discount_amount( $coupon_code );
						update_post_meta( $CouponId, 'coupon_amount', $discount );
					}
				}

				// Form Submit not occurs properly issue . Added Safe Redirect URL in V24.4.1.
								/**
				 * Hook:rs_check_redirection_after_redeming_applied.
				 *
				 * @since 24.4.1
				 */
				if ( apply_filters( 'rs_check_redirection_after_redeming_applied', false ) ) {
					if ( is_cart() ) {
						wp_safe_redirect( wc_get_cart_url() );
						exit;
					} elseif ( is_checkout() ) {
						wp_safe_redirect( wc_get_checkout_url() );
						exit;
					}
				}
			}
		}

		public static function get_sum_of_selected_products() {
			$IncProductId = get_option( 'rs_select_products_to_enable_redeeming' );
			$IncProductId = srp_check_is_array( $IncProductId ) ? $IncProductId : ( empty( $IncProductId ) ? array() : explode( ',', $IncProductId ) );

			$ExcProductId = get_option( 'rs_exclude_products_to_enable_redeeming' );
			$ExcProductId = srp_check_is_array( $ExcProductId ) ? $ExcProductId : ( empty( $ExcProductId ) ? array() : explode( ',', $ExcProductId ) );

			$IncCategory = get_option( 'rs_select_category_to_enable_redeeming' );
			$IncCategory = srp_check_is_array( $IncCategory ) ? $IncCategory : ( empty( $IncCategory ) ? array() : explode( ',', $IncCategory ) );

			$ExcCategory = get_option( 'rs_exclude_category_to_enable_redeeming' );
			$ExcCategory = srp_check_is_array( $ExcCategory ) ? $ExcCategory : ( empty( $ExcCategory ) ? array() : explode( ',', $ExcCategory ) );

			$Total = array();
			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId  = empty( $item['variation_id'] ) ? $item['product_id'] : $item['variation_id'];
				$ProductCat = get_the_terms( $item['product_id'], 'product_cat' );
				$LineTotal  = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) ? ( $item['line_subtotal'] + $item['line_tax'] ) : $item['line_subtotal'];
				/* Checking whether the Product has Category */
				if ( srp_check_is_array( $ProductCat ) ) {
					foreach ( $ProductCat as $CatObj ) {
						if ( ! is_object( $CatObj ) ) {
							continue;
						}

						$termid = $CatObj->term_id;

						if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_category' ) && srp_check_is_array( $IncCategory ) ) {
							if ( in_array( $termid, $IncCategory ) ) {
								$Total[] = $LineTotal;
							}
						}

						if ( 'yes' == get_option( 'rs_exclude_category_for_redeeming' ) && srp_check_is_array( $ExcCategory ) ) {
							if ( in_array( $termid, $ExcCategory ) ) {
								$Total[] = $LineTotal;
							}
						}
					}
				}

				if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_products' ) && srp_check_is_array( $IncProductId ) ) {
					if ( in_array( $ProductId, $IncProductId ) ) {
						$Total[] = $LineTotal;
					}
				}

				if ( 'yes' == get_option( 'rs_exclude_products_for_redeeming' ) && srp_check_is_array( $ExcProductId ) ) {
					if ( ! in_array( $ProductId, $ExcProductId ) ) {
						$Total[] = $LineTotal;
					}
				}
			}
			$ValueToReturn = srp_check_is_array( $Total ) ? array_sum( $Total ) : WC()->cart->subtotal;
			return $ValueToReturn;
		}

		public static function messages_for_redeeming() {
			$tax_msg               = self::msg_when_tax_enabled();
			$balance_points        = self::balance_point_msg_after_redeeming();
			$button_type_redeeming = self::button_type_redeem_field_in_cart_and_checkout();

			if ( $tax_msg ) {
				echo wp_kses_post( $tax_msg );
			}

			if ( $balance_points ) {
				echo wp_kses_post( $balance_points );
			}

			if ( $button_type_redeeming ) {
				echo wp_kses_post( $button_type_redeeming );
			}
		}

		/* Remaining Point message after Redeeming is applied in Cart/Checkout */

		public static function balance_point_msg_after_redeeming() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( ! srp_check_is_array( WC()->cart->get_applied_coupons() ) ) {
				return;
			}

			$UserId       = get_current_user_id();
			$banning_type = check_banning_type( $UserId );
			if ( 'redeemingonly' == $banning_type || 'both' == $banning_type ) {
				return;
			}

			$UserInfo   = get_user_by( 'id', $UserId );
			$UserName   = $UserInfo->user_login;
			$Redeem     = 'sumo_' . strtolower( "$UserName" );
			$AutoRedeem = 'auto_redeem_' . strtolower( "$UserName" );

			$DiscountAmnt        = isset( WC()->cart->coupon_discount_amounts[ "$AutoRedeem" ] ) ? WC()->cart->coupon_discount_amounts[ "$AutoRedeem" ] : ( isset( WC()->cart->coupon_discount_amounts[ "$Redeem" ] ) ? WC()->cart->coupon_discount_amounts[ "$Redeem" ] : 0 );
			$ShowBalancePointMsg = is_cart() ? get_option( 'rs_show_hide_message_for_redeem_points' ) : get_option( 'rs_show_hide_message_for_redeem_points_checkout_page' );
			$BalancePointMsg     = is_cart() ? get_option( 'rs_message_user_points_redeemed_in_cart' ) : get_option( 'rs_message_user_points_redeemed_in_checkout' );
			foreach ( WC()->cart->get_applied_coupons() as $Code ) {
				if ( 'yes' == get_option( 'rs_disable_point_if_coupon' ) ) {
					if ( strtolower( $Code ) != $AutoRedeem && strtolower( $Code ) != $Redeem ) {
						?>
						<div class="woocommerce-info sumo_reward_points_auto_redeem_message">
							<?php echo do_shortcode( get_option( 'rs_errmsg_for_coupon_in_order' ) ); ?>
						</div>
						<?php
					}
				}
				if ( '1' == $ShowBalancePointMsg ) {
					if ( ! empty( $DiscountAmnt ) ) {
						if ( strtolower( $Code ) == $Redeem || strtolower( $Code ) == $AutoRedeem ) {
							?>
							<div class="woocommerce-message sumo_reward_points_auto_redeem_message rs_cart_message">
								<?php echo do_shortcode( $BalancePointMsg ); ?>
							</div>
							<?php
							if ( 'yes' == get_option( 'rs_product_purchase_activated' ) && 'yes' == get_option( 'rs_enable_redeem_for_order' ) ) {
								?>
								<div class="woocommerce-info sumo_reward_points_auto_redeem_error_message">
									<?php echo do_shortcode( get_option( 'rs_errmsg_for_redeeming_in_order' ) ); ?>
								</div>
								<?php
							}
							echo wp_kses_post( self::cart_redeem_field( 'hide' ) );
							echo wp_kses_post( self::checkout_redeem_field( 'hide' ) );
						}
					}
				}
			}
		}

		/* Button Redeem Field in Cart/Checkout */

		public static function button_type_redeem_field_in_cart_and_checkout() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$ShowRedeemField = is_cart() ? get_option( 'rs_redeem_field_type_option' ) : get_option( 'rs_redeem_field_type_option_checkout' );
			if ( '1' == $ShowRedeemField ) {
				return;
			}

			if ( check_if_pointprice_product_exist_in_cart() ) {
				return;
			}

			$MemeberShipRestriction = ( 'yes' == get_option( 'rs_restrict_redeem_when_no_membership_plan' ) && function_exists( 'check_plan_exists' ) ) && get_current_user_id() ? ( check_plan_exists( get_current_user_id() ) ? 'yes' : 'no' ) : 'no';
			if ( 'yes' == $MemeberShipRestriction ) {
				return;
			}

			$EnabledProductList = array();
			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId       = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				$PointPriceValue = calculate_point_price_for_products( $ProductId );
				if ( empty( $PointPriceValue[ $ProductId ] ) ) {
					continue;
				}

				$EnabledProductList[] = $PointPriceValue[ $ProductId ];
			}

			if ( ! empty( $EnabledProductList ) && '1' == get_option( 'rs_show_hide_message_errmsg_for_point_price_coupon' ) ) {
				?>
				<div class="woocommerce-info"><?php echo do_shortcode( get_option( 'rs_errmsg_for_redeem_in_point_price_prt' ) ); ?></div>
				<?php
			}

			$MinCartTotalToRedeem          = get_option( 'rs_minimum_cart_total_points' );
			$MaxCartTotalToRedeem          = get_option( 'rs_maximum_cart_total_points' );
			$ErrMsgForMaxCartTotalToRedeem = get_option( 'rs_max_cart_total_redeem_error' );
			$ErrMsgForMinCartTotalToRedeem = get_option( 'rs_min_cart_total_redeem_error' );
			$CartTotal                     = srp_cart_subtotal();

			if ( '' !== $MinCartTotalToRedeem || '' !== $MaxCartTotalToRedeem ) {
				if ( '' !== $MinCartTotalToRedeem && $CartTotal <= $MinCartTotalToRedeem ) {
					if ( '1' == get_option( 'rs_show_hide_minimum_cart_total_error_message' ) ) {
						$CartTotalToReplace         = srp_formatted_price( round_off_type_for_currency( $MinCartTotalToRedeem ) );
						$CartTotalShortcodeReplaced = str_replace( '[carttotal]', $CartTotalToReplace, $ErrMsgForMinCartTotalToRedeem );
						$FinalErrmsg                = str_replace( '[currencysymbol]', '', $CartTotalShortcodeReplaced );
						?>
						<div class="woocommerce-error"><?php echo do_shortcode( $FinalErrmsg ); ?></div>
						<?php
					}
				} elseif ( '' !== $MaxCartTotalToRedeem && $CartTotal >= $MaxCartTotalToRedeem ) {
					if ( '1' == get_option( 'rs_show_hide_maximum_cart_total_error_message' ) ) {
						$CartTotalToReplace         = srp_formatted_price( round_off_type_for_currency( $MaxCartTotalToRedeem ) );
						$CartTotalShortcodeReplaced = str_replace( '[carttotal]', $CartTotalToReplace, $ErrMsgForMaxCartTotalToRedeem );
						$FinalErrmsg                = str_replace( '[currencysymbol]', '', $CartTotalShortcodeReplaced );
						?>
						<div class="woocommerce-error"><?php echo do_shortcode( $FinalErrmsg ); ?></div>
						<?php
					}
				} elseif ( $CartTotal >= $MinCartTotalToRedeem && $CartTotal <= $MaxCartTotalToRedeem ) {
					self::button_type_redeem_field();
				}
			} else {
				self::button_type_redeem_field();
			}
		}

		public static function button_type_redeem_field() {
			$PercentageToRedeem = is_cart() ? get_option( 'rs_percentage_cart_total_redeem' ) : get_option( 'rs_percentage_cart_total_redeem_checkout' );
			if ( empty( $PercentageToRedeem ) ) {
				return;
			}

			$UserId       = get_current_user_id();
			$banning_type = check_banning_type( $UserId );
			if ( 'redeemingonly' == $banning_type || 'both' == $banning_type ) {
				return;
			}

			$CartWithTax = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) ? WC()->cart->subtotal_ex_tax : WC()->cart->subtotal;
			if ( $CartWithTax < get_option( 'rs_minimum_cart_total_points' ) ) {
				return;
			}

			$UserInfo       = get_user_by( 'id', $UserId );
			$user_role      = is_object( $UserInfo ) ? $UserInfo->roles : array();
			$user_role      = implode( '', $user_role );
			$UserName       = $UserInfo->user_login;
			$AppliedCoupons = WC()->cart->get_applied_coupons();
			$AutoRedeem     = 'auto_redeem_' . strtolower( $UserName );
			if ( in_array( $AutoRedeem, $AppliedCoupons ) ) {
				return;
			}

			if ( ! self::product_filter_for_redeem_field() ) {
				return;
			}

			$PointsData = new RS_Points_Data( $UserId );
			$Points     = $PointsData->total_available_points();

			$minimum_available_points = self::get_minimum_available_points_for_redeeming_restriction();
			if ( $minimum_available_points && $Points < $minimum_available_points ) {
				$restriction_msg = str_replace( '[available_points]', absint( $minimum_available_points ), get_option( 'rs_available_points_redeem_error', 'You are eligible to redeem your points only when you have [available_points] Points in your account' ) );
				wc_print_notice( __( $restriction_msg ), 'error' );
				return;
			}

			if ( empty( $Points ) ) {
				return;
			}

			$MinUserPoints = ( '1' != get_user_meta( $UserId, 'rsfirsttime_redeemed', true ) ) ? get_option( 'rs_first_time_minimum_user_points' ) : get_option( 'rs_minimum_user_points_to_redeem' );
			if ( $Points < $MinUserPoints ) {
				return;
			}

			$ProductTotal    = array();
			$PointPriceValue = array();
			$PointPriceType  = array();
			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId               = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				$PointPriceType[]        = check_display_price_type( $ProductId );
				$CheckIfPointPriceEnable = calculate_point_price_for_products( $ProductId );
				if ( ! empty( $CheckIfPointPriceEnable[ $ProductId ] ) ) {
					$PointPriceValue[] = $CheckIfPointPriceEnable[ $ProductId ];
				}

				if ( '2' == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ) {
					$ProductTotal[] = isset( $item['line_subtotal_tax'] ) ? ( ( 'incl' == get_option( 'woocommerce_tax_display_cart' ) ) ? $item['line_subtotal'] + $item['line_subtotal_tax'] : $item['line_subtotal'] ) : $item['line_subtotal'];
					if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_products' ) && '' != get_option( 'rs_select_products_to_enable_redeeming' ) ) {
						$IncProduct = get_option( 'rs_select_products_to_enable_redeeming' );
						$IncProduct = srp_check_is_array( $IncProduct ) ? $IncProduct : explode( ',', $IncProduct );
						if ( in_array( $ProductId, $IncProduct ) ) {
							$ProductTotal[] = isset( $item['line_subtotal_tax'] ) ? ( ( 'incl' == get_option( 'woocommerce_tax_display_cart' ) ) ? $item['line_subtotal'] + $item['line_subtotal_tax'] : $item['line_subtotal'] ) : $item['line_subtotal'];
						}
					}
					if ( 'yes' == get_option( 'rs_enable_redeem_for_selected_category' ) && '' != get_option( 'rs_select_category_to_enable_redeeming' ) ) {
						$Category = get_the_terms( $ProductId, 'product_cat' );
						if ( srp_check_is_array( $Category ) ) {
							$IncCategory = get_option( 'rs_select_category_to_enable_redeeming' );
							$IncCategory = srp_check_is_array( $IncCategory ) ? $IncCategory : explode( ',', $IncCategory );
							foreach ( $Category as $CatObj ) {
								$termid = $CatObj->term_id;
								if ( in_array( $termid, $IncCategory ) ) {
									$ProductTotal[] = isset( $item['line_subtotal_tax'] ) ? ( ( 'incl' == get_option( 'woocommerce_tax_display_cart' ) ) ? $item['line_subtotal'] + $item['line_subtotal_tax'] : $item['line_subtotal'] ) : $item['line_subtotal'];
								}
							}
						}
					}
				}
			}
			if ( srp_check_is_array( $PointPriceValue ) ) {
				return;
			}

			if ( in_array( 2, $PointPriceType ) ) {
				return;
			}

			$Total            = '2' == get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) ? array_sum( $ProductTotal ) : WC()->cart->get_subtotal();
			$discount         = WC()->cart->get_discount_total();
			$Total            = $Total - $discount;
			$RedeemPercentage = RSMemberFunction::redeem_points_percentage( $UserId );
			$PointValue       = wc_format_decimal( get_option( 'rs_redeem_point' ) );
			$ButtonCaption    = is_cart() ? get_option( 'rs_redeeming_button_option_message' ) : get_option( 'rs_redeeming_button_option_message_checkout' );
			$CurrencyValue    = ( $PercentageToRedeem / 100 ) * $Total;
			$PointsToRedeem   = redeem_point_conversion( $CurrencyValue, $UserId );
			$CurrencyValue    = ( $Points >= $PointsToRedeem ) ? srp_formatted_price( round_off_type_for_currency( $CurrencyValue ) ) : srp_formatted_price( round_off_type_for_currency( redeem_point_conversion( $Points, $UserId, 'price' ) ) );
			$PointsToRedeem   = ( $Points >= $PointsToRedeem ) ? $PointsToRedeem : $Points;

			if ( '1' === get_option( 'rs_select_redeeming_based_on' ) ) {
				$points = self::srp_get_maximum_redeem_points_based_on_product_total( true );

				if ( srp_check_is_array( $points ) ) {
					$redeem_points = isset( $points['points'] ) ? $points['points'] : 0;
					if ( isset( $points['points'] ) && true === $points['error'] ) {
						$message = do_shortcode( get_option( 'rs_error_msg_for_disabled_redeeming_products' ) );
						wc_print_notice( __( $message ), 'error' );
						return;
					}
				}

				$CurrencyValue  = ( $Points >= $redeem_points ) ? srp_formatted_price( round_off_type_for_currency( $redeem_points ) ) : srp_formatted_price( round_off_type_for_currency( redeem_point_conversion( $Points, $UserId, 'price' ) ) );
				$PointsToRedeem = ( $Points >= $redeem_points ) ? $redeem_points : $Points;
			}

			if ( 0 == $PointsToRedeem ) {
				return;
			}

			$Message         = str_replace( '[pointsvalue]', $CurrencyValue, $ButtonCaption );
			$Message         = str_replace( '[currencysymbol]', '', $Message );
			$ButtonMsg       = str_replace( '[cartredeempoints]', $PointsToRedeem, $Message );
			$DivClass        = is_cart() ? 'sumo_reward_points_cart_apply_discount' : 'sumo_reward_points_checkout_apply_discount';
			$FormClass       = is_cart() ? 'rs_button_redeem_cart' : 'rs_button_redeem_checkout';
			$ShowRedeemField = is_checkout() ? get_option( 'rs_show_hide_redeem_field_checkout' ) : '1';
			if ( '1' != $ShowRedeemField ) {
				return;
			}

			/**
			 * Hook:rs_extra_messages_for_redeeming.
			 *
			 * @since 1.0
			 */
			$extra_message = apply_filters( 'rs_extra_messages_for_redeeming', '' );
			?>
			<form method="post" class="<?php echo esc_attr( $FormClass ); ?> woocommerce-info">

				<?php if ( $extra_message ) : ?>
					<div class="rs_add_extra_notice">
						<?php echo do_shortcode( $extra_message ); ?>
					</div>
				<?php endif; ?>

				<div class="<?php echo esc_attr( $DivClass ); ?>"><?php echo do_shortcode( $ButtonMsg ); ?>
					<input id="rs_apply_coupon_code_field" class="input-text" type="hidden"  value="<?php echo esc_attr( $PointsToRedeem ); ?>" name="rs_apply_coupon_code_field">
					<button id='mainsubmi' class="<?php echo esc_attr( get_option( 'rs_extra_class_name_apply_reward_points' ) ); ?>" type="submit" name="rs_apply_coupon_code1"><?php echo esc_html( get_option( 'rs_redeem_field_submit_button_caption' ) ); ?></button>
				</div>
			</form>
			<?php
		}

		/**
		 * Get Product Level Maximum Redeem Points.
		 *
		 * @since 28.8
		 */
		public static function srp_get_maximum_redeem_points_based_on_product_total( $notice = false ) {
			$user_id       = get_current_user_id();
			$cart_contents = WC()->cart->cart_contents;

			if ( ! srp_check_is_array( $cart_contents ) ) {
				return;
			}

			$redeem_points     = array();
			$disabled_products = array();

			foreach ( $cart_contents as $value ) {
				if ( isset( $value['product_id'] ) ) {
					$product_id = $value['product_id'];
				}

				$product = wc_get_product( $product_id );
				if ( 'variable' === $product->get_type() ) {
					if ( isset( $value['variation_id'] ) ) {
						$product_id = $value['variation_id'];
					} else {
						continue;
					}
				}

				$enable_redeem     = get_post_meta( $product_id, '_rewardsystem_redeeming_points_enable', true );
				$max_redeem_points = get_post_meta( $product_id, '_rewardsystem_max_redeeming_points', true );
				$points_data       = new RS_Points_Data( $user_id );

				$price            = isset( $value['line_subtotal'] ) ? $value['line_subtotal'] : 0;
				$converted_points = redeem_point_conversion( $price, $user_id, 'points' );

				if ( '1' === $enable_redeem ) {
					if ( '' !== $max_redeem_points && $converted_points > $max_redeem_points ) {
						$redeem_points[] = (float) $max_redeem_points;
					} else {
						$redeem_points[] = (float) $converted_points;
					}

					$disabled_products[] = 'no';
				} else {
					$redeem_points[]     = 0;
					$disabled_products[] = 'yes';
				}
			}

			$error = false;
			if ( srp_check_is_array( $disabled_products ) ) {
				if ( ! in_array( 'no', $disabled_products ) ) {
					$error = true;
				}
			}

			if ( ! srp_check_is_array( $redeem_points ) ) {
				return;
			}

			if ( $notice ) {
				return array(
					'points' => array_sum( $redeem_points ),
					'error'  => $error,
				);
			}

			return array_sum( $redeem_points );
		}

		public static function change_coupon_label( $link, $coupon ) {
			if ( ! is_user_logged_in() ) {
				return $link;
			}

			$banning_type = check_banning_type( get_current_user_id() );
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return $link;
			}

			$CouponObj  = srp_coupon_obj( $coupon );
			$CouponCode = $CouponObj['coupon_code'];
			$UserInfo   = get_user_by( 'id', get_current_user_id() );
			$UserName   = $UserInfo->user_login;
			if ( strtolower( $CouponCode ) == ( 'sumo_' . strtolower( $UserName ) ) || strtolower( $CouponCode ) == 'auto_redeem_' . strtolower( $UserName ) ) {
				$link = ' ' . get_option( 'rs_coupon_label_message' );
			}

			return $link;
		}

		/* Display message when tax is enabled in WooCommerce */

		public static function msg_when_tax_enabled() {

			if ( ! is_user_logged_in() ) {
				return;
			}

			$banning_type = check_banning_type( get_current_user_id() );
			if ( 'redeemingonly' == $banning_type || 'both' == $banning_type ) {
				return;
			}

			if ( check_if_pointprice_product_exist_in_cart() ) {
				return;
			}

			if ( 'yes' == get_option( 'woocommerce_calc_taxes' ) && '1' == get_option( 'rs_show_hide_message_notice_for_redeeming' ) ) {
				?>
				<div class="woocommerce-error sumo_reward_points_notice">
					<?php echo esc_html( get_option( 'rs_msg_for_redeem_when_tax_enabled' ) ); ?>
				</div>
				<?php
			}
		}

		public static function hide_coupon_message( $message ) {
			$message = is_checkout() ? self::msg_for_coupon( $message, 'yes' ) : $message;
			return $message;
		}

		public static function hide_coupon_field_on_checkout( $message ) {
			if ( is_checkout() ) {
				if ( '2' == get_option( 'rs_show_hide_coupon_field_checkout' ) ) {
					$message = false;
				}

				$message = self::msg_for_coupon( $message, 'no' );
			}
			if ( 'yes' === get_option( 'rs_enable_disable_auto_redeem_points' ) && 'yes' === get_option( 'rs_enable_disable_auto_redeem_checkout' ) ) {
				$message = true;
			}

			return $message;
		}

		public static function msg_for_coupon( $message, $hidemsg ) {
			if ( isset( $_REQUEST['rs_apply_coupon_code'] ) || isset( $_REQUEST['rs_apply_coupon_code1'] ) || isset( $_REQUEST['rs_apply_coupon_code2'] ) ) {
				if ( empty( $_REQUEST['rs_apply_coupon_code_field'] ) ) {
					return $message;
				}

				if ( 'no' == $hidemsg && 'yes' == get_option( 'woocommerce_enable_coupons' ) ) {
					return true;
				}

				if ( 'yes' == $hidemsg && '2' == get_option( 'rs_show_hide_coupon_field_checkout' ) ) {
					return '';
				}
			}
			return $message;
		}

		/* Error message for SUMO Coupon */

		public static function error_message_for_sumo_coupon( $msg, $msg_code, $object ) {
			if ( ! is_user_logged_in() ) {
				return $msg;
			}

			$CouponObj  = new WC_Coupon( $object );
			$CouponObj  = srp_coupon_obj( $CouponObj );
			$CouponCode = $CouponObj['coupon_code'];
			$UserInfo   = get_user_by( 'id', get_current_user_id() );
			$UserName   = $UserInfo->user_login;
			$Redeem     = 'sumo_' . strtolower( $UserName );
			$AutoRedeem = 'auto_redeem_' . strtolower( $UserName );
			if ( $CouponCode == $AutoRedeem ) {
				if ( 2 == get_option( 'rs_show_hide_auto_redeem_not_applicable' ) ) {
					return $msg;
				}
			}

			if ( $CouponCode == $Redeem ) {
				$msg_code = ( 104 == $msg_code ) ? 204 : $msg_code;
			}

			switch ( $msg_code ) {
				case 109:
				case 113:
				case 101:
					$msg = ( $CouponCode == $AutoRedeem ) ? get_option( 'rs_auto_redeem_not_applicable_error_message' ) : $msg;
					break;
				default:
					$msg = $msg;
					break;
			}
			return $msg;
		}

		/* Success message for SUMO Coupon */

		public static function success_message_for_sumo_coupon( $msg, $msg_code, $Obj ) {
			if ( ! is_user_logged_in() ) {
				return $msg;
			}

			$CouponObj  = new WC_Coupon( $Obj );
			$CouponObj  = srp_coupon_obj( $CouponObj );
			$CouponCode = $CouponObj['coupon_code'];
			update_option( 'appliedcouponcode', $CouponCode ); // Update to Replace Message which is displayed while coupon removed.
			$UserInfo   = get_user_by( 'id', get_current_user_id() );
			$UserName   = $UserInfo->user_login;
			$AutoRedeem = 'auto_redeem_' . strtolower( $UserName );
			if ( $AutoRedeem == $CouponCode ) {
				$msg_code = ( 200 == $msg_code ) ? 501 : $msg_code;
			}

			switch ( $msg_code ) {
				case 501:
					$msg = '1' == get_option( 'rs_show_hide_message_for_redeem' ) ? get_option( 'rs_automatic_success_coupon_message', 'AutoReward Points Successfully Added' ) : '';
					break;
				case 200:
					if ( isset( $_REQUEST['rs_apply_coupon_code'] ) || isset( $_REQUEST['rs_apply_coupon_code1'] ) ) {
						$msg = get_option( 'rs_show_hide_message_for_redeem' ) == '1' ? __( get_option( 'rs_success_coupon_message' ), 'rewardsystem' ) : '';
					}

					break;
				default:
					$msg = '';
					break;
			}
			return $msg;
		}

		/* Replace Remove Message for SUMO Coupon  */

		public static function replace_msg_for_remove_coupon( $message ) {
			if ( ! is_user_logged_in() ) {
				return $message;
			}

			$woo_msg = __( 'Coupon has been removed.', 'woocommerce' );
			if ( $message != $woo_msg ) {
				return $message;
			}

			if ( empty( get_option( 'rs_remove_redeem_points_message' ) ) ) {
				return $message;
			}

			$CouponCode = get_option( 'appliedcouponcode' );
			$UserInfo   = get_user_by( 'id', get_current_user_id() );
			$UserName   = $UserInfo->user_login;
			$Redeem     = 'sumo_' . strtolower( "$UserName" );
			$AutoRedeem = 'auto_redeem_' . strtolower( "$UserName" );
			if ( $Redeem == $CouponCode || $AutoRedeem == $CouponCode ) {
				$message = __( get_option( 'rs_remove_redeem_points_message' ), 'rewardsystem' );
			}

			return $message;
		}

		/* Validate Redeeming for Specific Gateway  */

		public static function validate_redeeming_for_specific_gateways( $data, $error ) {

			if ( ! is_user_logged_in() ) {
				return;
			}

			$restrict_gateway = get_option( 'rs_select_payment_gateway_for_restrict_redeem_points' );
			if ( ! srp_check_is_array( $restrict_gateway ) ) {
				return;
			}

			$payment_method = isset( $data['payment_method'] ) ? $data['payment_method'] : '';
			if ( ! in_array( $payment_method, $restrict_gateway ) ) {
				return;
			}

			$applied_coupons = WC()->cart->get_applied_coupons();
			if ( ! srp_check_is_array( $applied_coupons ) ) {
				return;
			}

			$user_id = get_current_user_id();
			$user    = get_user_by( 'id', $user_id );
			if ( ! is_object( $user ) ) {
				return;
			}

			$user_name   = $user->user_login;
			$redeem      = 'sumo_' . strtolower( "$user_name" );
			$auto_redeem = 'auto_redeem_' . strtolower( "$user_name" );

			$coupon_id = 0;
			foreach ( $applied_coupons as $coupon_code ) {
				if ( $coupon_code == $redeem || $coupon_code == $auto_redeem ) {
					$coupon = new WC_Coupon( $coupon_code );
					if ( ! is_object( $coupon ) ) {
						continue;
					}

					$coupon_id = $coupon->get_id();
					WC()->cart->remove_coupon( $coupon_code );
				}
			}

			if ( ! $coupon_id ) {
				return;
			}

			wp_trash_post( $coupon_id );

			$error->add( 'error', get_option( 'rs_redeeming_gateway_restriction_error', 'Redeeming is not applicable to the payment gateway you have selected. Hence, the discount applied through points has been removed.' ) );
		}

		public static function unset_gateways_for_excluded_product_to_redeem( $gateways ) {
			if ( 'yes' != get_option( 'rs_exclude_products_for_redeeming' ) ) {
				return $gateways;
			}

			global $woocommerce;
			if ( ! srp_check_is_array( $woocommerce->cart->cart_contents ) ) {
				return $gateways;
			}

			if ( empty( get_option( 'rs_exclude_products_to_enable_redeeming' ) ) ) {
				return $gateways;
			}

			foreach ( $woocommerce->cart->cart_contents as $key => $values ) {
				$ExcProducts = srp_check_is_array( get_option( 'rs_exclude_products_to_enable_redeeming' ) ) ? get_option( 'rs_exclude_products_to_enable_redeeming' ) : explode( ',', get_option( 'rs_exclude_products_to_enable_redeeming' ) );
				if ( in_array( $values['product_id'], $ExcProducts ) ) {
					foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
						if ( 'reward_gateway' != $gateway->id ) {
							continue;
						}

						unset( $gateways[ $gateway->id ] );
					}
				}
			}

			return 'NULL' != $gateways ? $gateways : array();
		}

		public static function get_minimum_available_points_for_redeeming_restriction() {

			$user = get_user_by( 'id', get_current_user_id() );
			if ( ! is_object( $user ) ) {
				return 0;
			}

			$minimum_available_points = 0;

			if ( 'yes' != get_option( 'rs_minimum_available_points_restriction_is_enabled', 'no' ) ) {
				return $minimum_available_points;
			}

			if ( '1' == get_option( 'rs_minimum_available_points_based_on', '1' ) ) {
					$minimum_available_points = (float) get_option( 'rs_available_points_based_redeem', '0' );
			} else {
					$user_roles = $user->roles;
				if ( ! srp_check_is_array( $user_roles ) ) {
					return $minimum_available_points;
				}

					$minimum_points_based_on_roles = array();
				foreach ( $user_roles as $role ) {
					$minimum_points_based_on_roles[] = (float) get_option( 'rs_minimum_available_points_to_redeem_for_' . $role, '0' );
				}

					$minimum_available_points = max( $minimum_points_based_on_roles );
			}

			return $minimum_available_points;
		}

		public static function get_coupon_amount_without_redeeming_coupons() {
			$coupons = WC()->cart->get_coupons();
			if ( ! srp_check_is_array( $coupons ) ) {
				return 0;
			}

			$user = get_user_by( 'id', get_current_user_id() );
			if ( ! is_object( $user ) ) {
				return 0;
			}

			$redeem      = 'sumo_' . strtolower( "$user->user_login" );
			$auto_redeem = 'auto_redeem_' . strtolower( "$user->user_login" );

			$coupon_amount = 0;
			foreach ( $coupons as $coupon_code => $coupon ) {
				if ( ! is_object( $coupon ) || $coupon_code == $redeem || $coupon_code == $auto_redeem ) {
					continue;
				}

				$coupon_amount += WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );
			}

			return $coupon_amount;
		}

		public static function get_max_discount_value() {
			$max_discount_values = array();
			if ( srp_check_is_array( WC()->cart->get_cart() ) ) {
				foreach ( WC()->cart->get_cart() as $cart ) {
					$product_id   = isset( $cart['product_id'] ) ? absint( $cart['product_id'] ) : 0;
					$variation_id = isset( $cart['variation_id'] ) ? absint( $cart['variation_id'] ) : 0;
					$product_id   = ! empty( $variation_id ) ? $variation_id : $product_id;
					$product      = wc_get_product( $product_id );
					if ( ! is_object( $product ) ) {
						continue;
					}

					$qty            = isset( $cart['quantity'] ) ? absint( $cart['quantity'] ) : 1;
					$discount_value = get_post_meta( $product_id, '_rs_max_redeeming_discount_value', true );
					if ( ! $discount_value ) {
						$discount_value = $product->get_price() * $qty;
					}

					$max_discount_values[] = $discount_value;
				}
			}

			return array_sum( $max_discount_values );
		}
	}

	RSRedeemingFrontend::init();
}
