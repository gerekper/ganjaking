<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once 'srp-layout-functions.php';
require_once 'srp-template-functions.php';
require_once 'srp-post-functions.php';
require_once 'srp-admin-functions.php';
require_once 'frontend/srp-frontend-functions.php';

if ( ! function_exists( 'srp_check_is_array' ) ) {

	function srp_check_is_array( $array ) {
		return ( is_array( $array ) && ! empty( $array ) ) ? true : false;
	}
}

if ( ! function_exists( 'check_banning_type' ) ) {

	function check_banning_type( $UserId ) {
		if ( ! ban_user_from_earning( $UserId ) && ! ban_user_from_redeeming( $UserId ) ) {
			return 'no_banning';
		}

		if ( ! ban_user_from_earning( $UserId ) && ban_user_from_redeeming( $UserId ) ) {
			return 'redeemingonly';
		}

		if ( ban_user_from_earning( $UserId ) && ! ban_user_from_redeeming( $UserId ) ) {
			return 'earningonly';
		}

		if ( ban_user_from_earning( $UserId ) && ban_user_from_redeeming( $UserId ) ) {
			return 'both';
		}
	}
}

if ( ! function_exists( 'ban_user_from_earning' ) ) {

	function ban_user_from_earning( $UserId ) {
		if ( get_option( 'rs_enable_banning_users_earning_points' ) == 'no' ) {
			return false;
		}

		$BannedUserListForEarning = get_option( 'rs_banned_users_list_for_earning' );
		$BannedUserRoleForEarning = get_option( 'rs_banning_user_role_for_earning' );
		if ( ! srp_check_is_array( $BannedUserListForEarning ) ) {
			$BannedUserListForEarning = ( '' != $BannedUserListForEarning ) ? explode( ',', $BannedUserListForEarning ) : array();
		}

		if ( empty( $BannedUserListForEarning ) && empty( $BannedUserRoleForEarning ) ) {
			return false;
		}

		if ( in_array( $UserId, $BannedUserListForEarning ) ) {
			return true;
		} else {
			$UserData   = get_userdata( $UserId );
			$RoleofUser = is_object( $UserData ) ? $UserData->roles : array( 'guest' );
			if ( ! empty( array_intersect( $RoleofUser, (array) $BannedUserRoleForEarning ) ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'ban_user_from_redeeming' ) ) {

	function ban_user_from_redeeming( $UserId ) {
		if ( get_option( 'rs_enable_banning_users_redeeming_points' ) == 'no' ) {
			return false;
		}

		$BannedUserListForRedeeming = get_option( 'rs_banned_users_list_for_redeeming' );
		$BannedUserRoleForRedeeming = get_option( 'rs_banning_user_role_for_redeeming' );
		if ( ! srp_check_is_array( $BannedUserListForRedeeming ) ) {
			$BannedUserListForRedeeming = ( '' != $BannedUserListForRedeeming ) ? explode( ',', $BannedUserListForRedeeming ) : array();
		}

		if ( empty( $BannedUserListForRedeeming ) && empty( $BannedUserRoleForRedeeming ) ) {
			return false;
		}

		if ( in_array( $UserId, $BannedUserListForRedeeming ) ) {
			return true;
		} else {
			$UserData   = get_userdata( $UserId );
			$RoleofUser = is_object( $UserData ) ? $UserData->roles : array( 'guest' );
			if ( ! empty( array_intersect( $RoleofUser, (array) $BannedUserRoleForRedeeming ) ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'check_if_coupon_exist_in_cart' ) ) {

	function check_if_coupon_exist_in_cart( $Code, $AppliedCoupons = array() ) {

		if ( ! srp_check_is_array( $AppliedCoupons ) ) {
			return false;
		}

		if ( in_array( $Code, $AppliedCoupons ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'multi_dimensional_sort' ) ) {

	function multi_dimensional_sort( $Rules, $Index, $SortType = 'asc' ) {

		$ArrToSort   = array();
		$ArrToReturn = array();
		if ( ! srp_check_is_array( $Rules ) ) {
			return array();
		}

		foreach ( $Rules as $Key => $Rule ) {
			$ArrToSort[ $Key ] = $Rule[ $Index ];
		}

		$SortedArr = ( 'asc' == $SortType ) ? asort( $ArrToSort ) : arsort( $ArrToSort );

		if ( ! srp_check_is_array( $ArrToSort ) ) {
			return array();
		}

		foreach ( $ArrToSort as $NewKey => $value ) {
			$ArrToReturn[ $NewKey ] = $Rules[ $NewKey ];
		}
		return $ArrToReturn;
	}
}

if ( ! function_exists( 'srp_cart_subtotal' ) ) {

	function srp_cart_subtotal( $exc_discount = false, $OrderId = 0 ) {
		$subtotal = 0;
		$discount = 0;
		if ( ! empty( $OrderId ) ) {
			$Order = wc_get_order( $OrderId );
			if ( is_object( $Order ) ) {
				$subtotal = ( get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) ? $Order->get_subtotal() + $Order->get_total_tax() : ( $Order->get_subtotal() - $Order->get_total_tax() );
				$discount = $Order->get_total_discount();
			}
		} else {
			global $woocommerce;
			$Obj = function_exists( 'WC' ) ? WC() : $woocommerce;
			if ( (float) $Obj->version >= (float) '3.2.0' ) {
				$discount = ( get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) ? $Obj->cart->get_discount_tax() + $Obj->cart->get_discount_total() : $Obj->cart->get_discount_total();
			} else {
				$discount = $Obj->cart->discount_cart + $Obj->cart->discount_cart_tax;
			}
			$subtotal = (float) ( get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) ? $Obj->cart->subtotal : $Obj->cart->subtotal_ex_tax;
		}
		return ( $exc_discount ) ? (float) ( $subtotal - $discount ) : (float) $subtotal;
	}
}

if ( ! function_exists( 'check_if_discount_applied' ) ) {

	function check_if_discount_applied() {
		if ( get_option( 'rs_discounts_compatability_activated' ) != 'yes' ) {
			return false;
		}

		if ( ! class_exists( 'SUMODiscounts' ) ) {
			return false;
		}

		if ( get_option( '_rs_not_allow_earn_points_if_sumo_discount' ) != 'yes' ) {
			return false;
		}

		return function_exists( 'check_sumo_discounts_are_applied_in_cart' ) ? check_sumo_discounts_are_applied_in_cart() : false;
	}
}

if ( ! function_exists( 'send_mail_for_thershold_points' ) ) {

	function send_mail_for_thershold_points() {
		if ( get_option( 'rs_email_activated' ) != 'yes' ) {
			return;
		}

		if ( get_option( 'rs_mail_enable_threshold_points' ) != 'yes' ) {
			return;
		}

		$UserId     = get_current_user_id();
		$PointsData = new RS_Points_Data( $UserId );
		$Points     = $PointsData->total_available_points();

		if ( get_option( 'rs_mail_threshold_points' ) < $Points ) {
			update_user_meta( $UserId, 'rs_mail_minimum_threshold_points', 'yes' );
		}

		if ( $Points > get_option( 'rs_mail_threshold_points' ) && get_user_meta( $UserId, 'rs_mail_minimum_threshold_points', true ) == 'no' ) {
			return;
		}

		$UserInfo = get_user_by( 'id', 1 );
		$UserName = get_user_by( 'id', $UserId )->display_name;
		$subject  = get_option( 'rs_email_subject_threshold_points' );
		$msg      = get_option( 'rs_email_message_threshold_points' );
		if ( empty( $subject ) || empty( $msg ) ) {
			return;
		}

		$message = str_replace( '[Username]', $UserName, str_replace( '[TotalPoint]', $Points, get_option( 'rs_email_message_threshold_points' ) ) );
		ob_start();
		wc_get_template( 'emails/email-header.php', array( 'email_heading' => $subject ) );
		echo do_shortcode( $message );
		wc_get_template( 'emails/email-footer.php' );
		$woo_temp_msg = ob_get_clean();
		$headers      = "MIME-Version: 1.0\r\n";
		$headers     .= "Content-Type: text/html; charset=UTF-8\r\n";
		if ( '2' == get_option( 'rs_select_mail_function' ) ) {
			$mailer = WC()->mailer();
			if ( $mailer->send( $UserInfo->user_email, $subject, $woo_temp_msg, $headers ) ) {
				update_user_meta( $UserId, 'rs_mail_minimum_threshold_points', 'no' );
			}
		} elseif ( '1' == get_option( 'rs_select_mail_function' ) ) {
			if ( mail( $UserInfo->user_email, $subject, $woo_temp_msg, $headers ) ) {
				update_user_meta( $UserId, 'rs_mail_minimum_threshold_points', 'no' );
			}
		}
	}
}

if ( ! function_exists( 'get_referrer_id_from_payment_plan' ) ) {

	function get_referrer_id_from_payment_plan( $order_id ) {
		if ( ! class_exists( 'SUMOPaymentPlans' ) ) {
			return 0;
		}

		$parent_id = wp_get_post_parent_id( $order_id );
		if ( empty( $parent_id ) ) {
			return 0;
		}

		$order_obj = wc_get_order( $parent_id );
		$refer_id  = $order_obj->get_meta( '_referrer_name' );
		$order     = wc_get_order( $order_id );
		$order->update_meta_data( '_referrer_name', $refer_id );
		$order->save();

		return $refer_id;
	}
}

if ( ! function_exists( 'is_payment_product' ) ) {

	function is_payment_product( $order_id, $product_id ) {
		if ( ! function_exists( '_sumo_pp_is_balance_payment_order' ) ) {
			return false;
		}

		$order = wc_get_order( $order_id );
		if ( _sumo_pp_is_balance_payment_order( $order_id ) && 'yes' === $order->get_meta( 'is_sumo_pp_order' ) ) {
			$payment_id = $order->get_meta( '_payment_id' );
			return get_post_meta( $payment_id, '_product_id', true ) == $product_id;
		}

		return false;
	}
}

if ( ! function_exists( 'get_payment_product_price' ) ) {

	function get_payment_product_price( $order_id, $check_in_initial_order = false ) {
		if ( ! class_exists( 'SUMOPaymentPlans' ) ) {
			return 0;
		}

		$order = wc_get_order( $order_id );
		if ( $check_in_initial_order && function_exists( '_sumo_pp_is_initial_payment_order' ) && function_exists( '_sumo_pp_get_posts' ) ) {

			if ( ! _sumo_pp_is_initial_payment_order( $order_id ) ) {
				return 0;
			}

			$initial_amount = 0;

			foreach ( $order->get_items() as $item ) {
				$itemid = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];

				$payments = _sumo_pp_get_posts(
					array(
						'post_type'   => 'sumo_pp_payments',
						'post_status' => array_keys( _sumo_pp_get_payment_statuses() ),
						'meta_query'  => array(
							'relation' => 'AND',
							array(
								'key'   => '_initial_payment_order_id',
								'value' => $order_id,
							),
							array(
								'key'   => '_product_id',
								'value' => $itemid,
							),
						),
					)
				);

				if ( srp_check_is_array( $payments ) ) {
					foreach ( $payments as $payment_id ) {
						if ( 'payment-plans' === get_post_meta( $payment_id, '_payment_type', true ) ) {
							$product_amount  = floatval( get_post_meta( $payment_id, '_product_price', true ) ) * absint( get_post_meta( $payment_id, '_product_qty', true ) );
							$initial_amount += ( floatval( get_post_meta( $payment_id, '_initial_payment', true ) ) * $product_amount ) / 100;
						}
						if ( 'pay-in-deposit' === get_post_meta( $payment_id, '_payment_type', true ) ) {
							$initial_amount += floatval( get_post_meta( $payment_id, '_deposited_amount', true ) ) * absint( get_post_meta( $payment_id, '_product_qty', true ) );
						}
					}
				}
			}
			return $initial_amount;
		} elseif ( function_exists( '_sumo_pp_is_balance_payment_order' ) ) {
			if ( _sumo_pp_is_balance_payment_order( $order_id ) && 'yes' === $order->get_meta( 'is_sumo_pp_order' ) ) {
				$payment_id = absint( $order->get_meta( '_payment_id' ) );
				return floatval( get_post_meta( $payment_id, '_product_price', true ) );
			}
		}
	}
}

if ( ! function_exists( 'is_final_payment' ) ) {

	function is_final_payment( $order_id ) {

		if ( class_exists( 'SUMOPaymentPlans' ) && function_exists( '_sumo_pp_is_balance_payment_order' ) ) {
			$order = wc_get_order( $order_id );
			if ( _sumo_pp_is_balance_payment_order( $order_id ) && 'yes' === $order->get_meta( 'is_sumo_pp_order' ) ) {
				$payment_id             = absint( $order->get_meta( '_payment_id' ) );
				$remaining_installments = absint( get_post_meta( $payment_id, '_remaining_installments', true ) );

				$order_status = '';
				$order        = wc_get_order( $order_id );
				if ( $order ) {
					$order_status = defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0', '<' ) ? $order->status : $order->get_status();
				}

				return $payment_id > 0 && ( ( 1 === $remaining_installments ) || ( 0 === $remaining_installments && in_array( $order_status, array( 'processing', 'completed' ) ) ) );
			}
		}
		return false;
	}
}

if ( ! function_exists( 'is_initial_payment' ) ) {

	function is_initial_payment( $product_id, $order_user_id = 0 ) {
		if ( class_exists( 'SUMOPaymentPlans' ) && function_exists( '_sumo_pp_is_payment_product' ) ) {
			return _sumo_pp_is_payment_product( $product_id, $order_user_id );
		}

		return false;
	}
}

if ( ! function_exists( 'get_payment_data_for_payment_plan' ) ) {

	function get_payment_data_for_payment_plan( $product_id ) {
		if ( class_exists( 'SUMOPaymentPlans' ) && function_exists( '_sumo_pp_get_payment_data' ) ) {
			$payment_data = _sumo_pp_get_payment_data( $product_id );
			return isset( $payment_data['product_price'] ) ? $payment_data['product_price'] : '';
		}
	}
}

if ( ! function_exists( 'rs_send_mail_for_actions' ) ) {

	function rs_send_mail_for_actions( $to, $event_slug, $earned_point, $user_name = '', $order_id = '' ) {
		$user_info  = get_user_by( 'email', $to );
		$first_name = is_object( $user_info ) ? $user_info->first_name : '';
		$last_name  = is_object( $user_info ) ? $user_info->last_name : '';
		$subject    = '';
		$message    = '';
		$PointsData = new RS_Points_Data( $user_info->ID );
		$PointsData->reset( $user_info->ID );
		$total_earned_point = $PointsData->total_available_points();
		$earned_point       = round_off_type( $earned_point );
		/* Send SMS for Actions - Start */
		if ( 'yes' == get_option( 'rs_sms_activated' ) && 'yes' == get_option( 'rs_enable_send_sms_to_user' ) ) {
			if ( 'yes' == get_option( 'rs_send_sms_earning_points_for_actions' ) ) {
				if ( 'RRP' == $event_slug ) {
					$MsgFor = 'signup';
				} elseif ( 'RPPR' == $event_slug ) {
					$MsgFor = 'review';
				} elseif ( 'RRRP' == $event_slug ) {
					$MsgFor = 'referralregistration';
				} elseif ( 'PPRRP' == $event_slug || 'PPRRPCT' == $event_slug ) {
					$MsgFor = 'referralpurchase';
				} elseif ( 'RRRP' == $event_slug ) {
					$MsgFor = 'referralregistrationbonus';
				}

				$PhoneNumber = ! empty( get_user_meta( $user_info->ID, 'rs_phone_number_value_from_signup', true ) ) ? get_user_meta( $user_info->ID, 'rs_phone_number_value_from_signup', true ) : get_user_meta( $user_info->ID, 'rs_phone_number_value_from_account_details', true );
				$PhoneNumber = ! empty( $PhoneNumber ) ? $PhoneNumber : get_user_meta( $user_info->ID, 'billing_phone', true );
				if ( '1' == get_option( 'rs_sms_sending_api_option' ) ) {
					RSFunctionForSms::send_sms_twilio_api( '', $MsgFor, $earned_point, $PhoneNumber );
				} elseif ( '2' == get_option( 'rs_sms_sending_api_option' ) ) {
					RSFunctionForSms::send_sms_nexmo_api( '', $MsgFor, $earned_point, $PhoneNumber );
				}
			}
		}
		/* Send SMS for Actions - End */

		// Birthday Reward Points
		if ( 'yes' == get_option( 'rs_send_mail_for_bday_points' ) ) {
			if ( 'BRP' == $event_slug ) {
				$subject = str_replace( '[username]', $first_name, get_option( 'rs_email_subject_for_bday_points' ) );
				$message = str_replace( '[rs_birthday_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_for_bday_points' ) ) );
				$message = str_replace( '[username]', $first_name, $message );
			}
		}

		// Product Review
		if ( 'yes' == get_option( 'rs_send_mail_product_review' ) ) {
			if ( 'RPPR' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_product_review' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_product_review' ) ) );
			}
		}
		// Account Signup
		if ( 'yes' == get_option( 'rs_send_mail_account_signup' ) ) {
			if ( 'RRP' == $event_slug || 'SLRRP' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_account_signup' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_account_signup' ) ) );
			}
		}
		// Blog Post Create
		if ( 'yes' == get_option( 'rs_send_mail_blog_post_create' ) ) {
			if ( 'RPFP' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_blog_post_create' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_blog_post_create' ) ) );
			}
		}
		// Blog Post Comment
		if ( 'yes' == get_option( 'rs_send_mail_blog_post_comment' ) ) {
			if ( 'RPCPAR' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_blog_post_comment' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_blog_post_comment' ) ) );
			}
		}
		if ( 'yes' == get_option( 'rs_send_mail_blog_post_comment' ) ) {
			if ( 'RPFPOC' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_blog_post_comment' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_blog_post_comment' ) ) );
			}
		}
		// Page Comment
		if ( 'yes' == get_option( 'rs_send_mail_page_comment' ) ) {
			if ( 'RPFPAC' == $event_slug || 'RPCPR' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_page_comment' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_page_comment' ) ) );
			}
		}
		// Product Creation
		if ( 'yes' == get_option( 'rs_send_mail_product_create' ) ) {
			if ( 'RPCPRO' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_product_create' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_product_create' ) ) );
			}
		}
		// Login
		if ( 'yes' == get_option( 'rs_send_mail_login' ) ) {
			if ( 'LRP' == $event_slug || 'SLRP' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_login' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_login' ) ) );
			}
		}

		// Social Linking
		if ( 'yes' == get_option( 'rs_send_mail_for_social_account_linking' ) ) {
			if ( 'SLLRP' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_for_social_account_linking' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_for_social_account_linking' ) ) );
			}
		}

		// Birthday Reward
		if ( 'yes' == get_option( 'rs_send_mail_cus_field_reg' ) ) {
			if ( 'CRFRP' == $event_slug || 'CRPFDP' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_cus_field_reg' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_cus_field_reg' ) ) );
				$message = str_replace( '[rsfirstname]', $first_name, str_replace( '[rslastname]', $last_name, $message ) );
			}
		}

		// Reward Gateway
		if ( 'yes' == get_option( 'rs_send_mail_payment_gateway' ) ) {
			if ( 'RPG' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_payment_gateway' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_payment_gateway' ) ) );
			}
		}

		// Coupon Points
		if ( 'yes' == get_option( 'rs_send_mail_coupon_reward' ) ) {
			if ( 'RPC' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_coupon_reward' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_coupon_reward' ) ) );
			}
		}
		// Facebook Like
		if ( 'yes' == get_option( 'rs_send_mail_Facebook_like' ) ) {
			if ( 'RPFL' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_facebook_like' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_facebook_like' ) ) );
			}
		}
		// Instagram
		if ( 'yes' == get_option( 'rs_send_mail_instagram' ) ) {
			if ( 'RPIF' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_instagram' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_instagram' ) ) );
			}
		}
		// OK
		if ( 'yes' == get_option( 'rs_send_mail_ok' ) ) {
			if ( 'RPOK' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_ok' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_ok' ) ) );
			}
		}
		// FacebookSare
		if ( 'yes' == get_option( 'rs_send_mail_facebook_share' ) ) {
			if ( 'RPFS' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_facebook_share' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_facebook_share' ) ) );
			}
		}
		// Twitter Tweet.
		if ( 'yes' === get_option( 'rs_send_mail_tewitter_tweet' ) ) {
			if ( 'RPTT' === $event_slug ) {
				$subject = get_option( 'rs_email_subject_twitter_tweet' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_twitter_tweet' ) ) );
			}
		}
		// Twitter Follow.
		if ( 'yes' === get_option( 'rs_send_mail_twitter_follow' ) ) {
			if ( 'RPTF' === $event_slug ) {
				$subject = get_option( 'rs_email_subject_twitter_follow' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_twitter_follow' ) ) );
			}
		}
		// Google Share.
		if ( 'yes' === get_option( 'rs_send_mail_google' ) ) {
			if ( 'RPGPOS' === $event_slug ) {
				$subject = get_option( 'rs_email_subject_google' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_google' ) ) );
			}
		}
		// VK.
		if ( get_option( 'rs_send_mail_vk' ) ) {
			if ( 'RPVL' === $event_slug ) {
				$subject = get_option( 'rs_email_subject_vk' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_vk' ) ) );
			}
		}

		/*
		 Social icons Post Or Page Mail - Start */
		// OK Post.
		if ( 'RPOKP' === $event_slug ) {
			if ( 'yes' === get_option( 'rs_send_mail_post_ok_ru' ) ) {
				$subject = get_option( 'rs_email_subject_post_ok_ru' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_post_ok_ru' ) ) );
			}
		}
		// Instagram Post.
		if ( 'RPIFP' === $event_slug ) {
			if ( 'yes' === get_option( 'rs_send_mail_post_instagram' ) ) {
				$subject = get_option( 'rs_email_subject_post_instagram' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_post_instagram' ) ) );
			}
		}
		// VK Like Post.
		if ( 'RPVLP' === $event_slug ) {
			if ( 'yes' === get_option( 'rs_send_mail_post_vk' ) ) {
				$subject = get_option( 'rs_email_subject_post_vk' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_post_vk' ) ) );
			}
		}
		// Google Share Post.
		if ( 'RPGPOSP' === $event_slug ) {
			if ( 'yes' === get_option( 'rs_send_mail_post_gplus' ) ) {
				$subject = get_option( 'rs_email_subject_post_gplus' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_post_gplus' ) ) );
			}
		}
		// Twitter Share Post.
		if ( 'RPTFP' === $event_slug ) {
			if ( 'yes' === get_option( 'rs_send_mail_post_follow' ) ) {
				$subject = get_option( 'rs_email_subject_post_follow' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_post_follow' ) ) );
			}
		}
		// Twitter Tweet Post.
		if ( 'RPTTP' === $event_slug ) {
			if ( 'yes' === get_option( 'rs_send_mail_post_tweet' ) ) {
				$subject = get_option( 'rs_email_subject_post_tweet' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_post_tweet' ) ) );
			}
		}
		// Facebook Share Post.
		if ( 'RPFSP' === $event_slug ) {
			if ( 'yes' === get_option( 'rs_send_mail_post_fb_share' ) ) {
				$subject = get_option( 'rs_email_subject_post_fb_share' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_post_fb_share' ) ) );
			}
		}
		// Facebook Like Post.
		if ( 'RPFLP' === $event_slug ) {
			if ( 'yes' === get_option( 'rs_send_mail_post_fb_like' ) ) {
				$subject = get_option( 'rs_email_subject_post_fb_like' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_post_fb_like' ) ) );
			}
		}
		/* Social icons Post Or Page Mail - End */

		// Gift Voucher.
		if ( 'yes' === get_option( 'rs_send_mail_gift_voucher' ) ) {
			if ( 'RPGV' === $event_slug ) {
				$subject = get_option( 'rs_email_subject_gift_voucher' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_gift_voucher' ) ) );
			}
		}
		// Point URL.
		if ( 'yes' == get_option( 'rs_send_mail_point_url' ) ) {
			if ( 'RPFURL' === $event_slug ) {
				$subject = get_option( 'rs_email_subject_point_url' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_point_url' ) ) );
			}
		}

		// Referral Registration Points for Referral.
		if ( 'yes' == get_option( 'rs_send_mail_referral_signup' ) ) {
			if ( 'RRRP' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_referral_signup' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_user_name]', $user_name, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_referral_signup' ) ) ) );

				if ( $order_id ) {
					$order   = wc_get_order( $order_id );
					$user_id = $order->get_meta( '_referrer_name' );
					$user    = get_user_by( 'ID', $user_id );
					if ( is_object( $user ) ) {
						$message = str_replace( array( '[rs_referrer_name]', '[rs_referrer_email_id]' ), array( $user->user_login, $user->user_email ), $message );
					}
				}
			}
		}
		// Referral Reward Points Getting Referred.
		if ( 'yes' == get_option( 'rs_send_mail_getting_referred' ) ) {
			if ( 'RRPGR' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_getting_referred' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_user_name]', $user_name, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_getting_referred' ) ) ) );
			}
		}
		// Product Purchase for Referral
		if ( 'yes' == get_option( 'rs_send_mail_pdt_purchase_referral' ) ) {
			if ( 'PPRRP' == $event_slug || 'PPRRPCT' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_pdt_purchase_referral' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_pdt_purchase_referral' ) ) );
				$message = rs_get_referrer_email_info_in_order( $order_id, $message );
			}
		}

		// Product Purchase For Getting Referred
		if ( 'yes' == get_option( 'rs_send_mail_pdt_purchase_referrer' ) ) {
			if ( 'PPRRPG' == $event_slug || 'PPRRPGCT' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_pdt_purchase_referrer' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_pdt_purchase_referrer' ) ) );
			}
		}

		// Waiting List Subscribing
		if ( 'yes' == get_option( 'rs_send_mail_for_waitlist_subscribing' ) ) {
			if ( 'RPFWLS' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_for_waitlist_subscribing' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_for_waitlist_subscribing' ) ) );
			}
		}

		// Waiting List Sale Conversion
		if ( 'yes' == get_option( 'rs_send_mail_for_waitlist_sale_conversion' ) ) {
			if ( 'RPFWLSC' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_for_waitlist_sale_conversion' );
				$message = str_replace( '[rs_earned_points]', $earned_point, str_replace( '[rs_available_points]', $total_earned_point, get_option( 'rs_email_message_for_waitlist_sale_conversion' ) ) );
			}
		}

		// Bonus point for order
		if ( 'yes' == get_option( 'rs_number_of_orders_bonus_email_enabled' ) ) {
			if ( 'OBP' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_number_of_orders_bonus_point', 'Bonus Reward Points for Orders - Notification' );
				$message = str_replace( array( '[rs_bonus_points_for_orders]', '[rs_available_points]', '[username]', '[site_link]' ), array( $earned_point, $total_earned_point, $user_name, site_url() ), get_option( 'rs_email_message_number_of_orders_bonus_point', 'Hi [username],<br/><br/> You have earned <b>[rs_bonus_points_for_orders]</b> bonus points for placing succesfull orders on [site_link].<br/><br/>Currently, you have <b>[rs_available_points]</b> points in your account.<br/><br/>Thanks' ) );
			}
		}

		// Aniversary points mail
		if ( 'yes' == get_option( 'rs_anniversary_points_activated' ) ) {
			// Account Aniversary points mail
			if ( 'yes' == get_option( 'rs_enable_account_anniversary_point' ) && 'yes' == get_option( 'rs_enable_account_anniversary_mail' ) && 'AAP' == $event_slug ) {
				$subject = get_option( 'rs_email_subject_account_anniversary', 'Account Anniversary Reward Points - Notification' );
				$message = str_replace( array( '[rs_account_maintenance_points]', '[rs_available_points]', '[username]', '[site_link]' ), array( $earned_point, $total_earned_point, $user_name, site_url() ), get_option( 'rs_email_message_account_anniversary', 'Hi [username],<br /><br /> You have earned <b>[rs_account_maintenance_points]</b> points for maintaining the account on the site [site_link].<br /><br />Currently, you have <b>[rs_available_points]</b> points in your account.<br /><br />Thanks' ) );
			}

			// Custom Aniversary points mail
			if ( 'yes' == get_option( 'rs_enable_custom_anniversary_point' ) && 'yes' == get_option( 'rs_enable_custom_anniversary_mail' ) && ( 'CSAP' == $event_slug || 'CMAP' == $event_slug ) ) {
				$subject = get_option( 'rs_email_subject_custom_anniversary', 'Anniversary Reward Points - Notification' );
				$message = str_replace( array( '[rs_anniversary_points]', '[rs_available_points]', '[username]', '[site_link]' ), array( $earned_point, $total_earned_point, $user_name, site_url() ), get_option( 'rs_email_message_custom_anniversary', 'Hi [username],<br /><br /> You have earned <b>[rs_anniversary_points]</b> points for reaching the Anniversary Date given on the site [site_link].<br /><br />Currently, you have <b>[rs_available_points]</b> points in your account.<br /><br />Thanks' ) );
			}
		}

		$un_subscribe = get_user_meta( get_current_user_id(), 'unsub_value', true );

		if ( 'yes' !== $un_subscribe ) {
			if ( '' != $subject || '' != $message ) {
				$message = str_replace( '[rsfirstname]', $first_name, $message );
				$message = str_replace( '[rslastname]', $last_name, $message );
				$message = do_shortcode( $message ); // shortcode feature
				send_mail( $to, $subject, $message );
			}
		}
	}
}

if ( ! function_exists( 'redeem_point_conversion' ) ) {

	function redeem_point_conversion( $Value, $UserId, $Type = 'points' ) {
		$PointValue     = (float) wc_format_decimal( get_option( 'rs_redeem_point' ) ); // Conversion Points
		$RedeemPercent  = RSMemberFunction::redeem_points_percentage( $UserId );
		$ConvertedValue = ( 'price' == $Type ) ? ( ( (float) $Value / $PointValue ) * $RedeemPercent ) : ( ( (float) $Value * $PointValue ) / $RedeemPercent ); // Ex:10 * 2 = 20
		return $ConvertedValue; // $.20
	}
}

if ( ! function_exists( 'earn_point_conversion' ) ) {

	function earn_point_conversion( $Points ) {
		$ConversionRate = wc_format_decimal( get_option( 'rs_earn_point' ) ); // Conversion Points
		$PointsValue    = wc_format_decimal( get_option( 'rs_earn_point_value' ) ); // Value for the Conversion Points (i.e)  1 points is equal to $.2
		$ConvertedValue = ( $Points / $PointsValue ) * $ConversionRate; // Ex:10 * 2 = 20
		return $ConvertedValue; // $.20
	}
}

if ( ! function_exists( 'check_if_referrer_has_manual_link' ) ) {

	function check_if_referrer_has_manual_link( $buyer_id ) {
		$linkarray = get_option( 'rewards_dynamic_rule_manual' );
		if ( ! srp_check_is_array( $linkarray ) ) {
			return false;
		}

		foreach ( $linkarray as $key => $eachreferer ) {
			if ( $eachreferer['refferal'] == $buyer_id ) {
				return $eachreferer['referer'];
			}
		}
		return false;
	}
}

if ( ! function_exists( 'send_mail_for_product_purchase' ) ) {

	function send_mail_for_product_purchase( $user_id, $order_id, $email_type = 'earning' ) {
		global $wpdb;
		$templates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rs_templates_email" ); // all email templates
		if ( ! srp_check_is_array( $templates ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		foreach ( $templates as $emails ) {
			if ( 'ACTIVE' != $emails->rs_status ) {
				continue;
			}

			if ( '3' == $emails->rsmailsendingoptions ) {
				continue;
			}

			if ( 'earning' == $email_type && '2' == $emails->rsmailsendingoptions ) {
				continue;
			}

			if ( 'redeeming' == $email_type && '1' == $emails->rsmailsendingoptions ) {
				continue;
			}

			$SendMail = ( '1' === $emails->mailsendingoptions ) ? ( '1' !== $order->get_meta( 'rsearningtemplates' . $emails->id ) ) : true;
			if ( $SendMail ) {
				include 'frontend/emails/class-fp-productpurchase-mail.php';
			}
		}
	}
}

if ( ! function_exists( 'currency_value_for_available_points' ) ) {

	function currency_value_for_available_points( $UserId ) {
		$PointsData    = new RS_Points_Data( $UserId );
		$Points        = $PointsData->total_available_points();
		$CurrencyValue = redeem_point_conversion( $Points, $UserId, 'price' );
		return '<span class="rs_user_total_points"><b>' . $Points . ' (' . srp_formatted_price( round_off_type_for_currency( $CurrencyValue ) ) . ')</b></span>';
	}
}

if ( ! function_exists( 'date_display_format' ) ) {

	function date_display_format( $date ) {

		$gmtdate = is_numeric( $date ) ? (int) $date + ( (float) get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) : $date;
		if ( '1' == get_option( 'rs_dispaly_time_format' ) ) {
			$date_time_format  = ( 'yes' == get_option( 'rs_hide_time_format' ) ) ? 'd-m-Y' : 'd-m-Y H:i:s A';
			$update_start_date = is_numeric( $date ) ? date_i18n( $date_time_format, $gmtdate ) : $date;
		} else {
			$date_time_format  = ( 'yes' == get_option( 'rs_hide_time_format' ) ) ? get_option( 'date_format' ) : get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
			$update_start_date = is_numeric( $date ) ? date_i18n( $date_time_format, $gmtdate ) : $date;
			$update_start_date = strftime( $update_start_date );
		}

		return $update_start_date;
	}
}

if ( ! function_exists( 'earned_points_from_order' ) ) {

	function earned_points_from_order( $OrderId ) {
		global $wpdb;
		$EarnedTotal  = array();
		$RevisedTotal = array();
		$EarnedData   = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints FROM {$wpdb->prefix}rsrecordpoints WHERE checkpoints NOT IN ('RVPFRP','PPRRP', 'PPRRPCT') AND orderid = %d", $OrderId ), ARRAY_A );
		foreach ( $EarnedData as $EarnedPoints ) {
			$EarnedTotal[] = $EarnedPoints['earnedpoints'];
		}
		$RevisedData = $wpdb->get_results( $wpdb->prepare( "SELECT redeempoints FROM {$wpdb->prefix}rsrecordpoints WHERE checkpoints = 'RVPFPPRP' AND orderid = %d", $OrderId ), ARRAY_A );
		foreach ( $RevisedData as $RevisedPoints ) {
			$RevisedTotal[] = $RevisedPoints['redeempoints'];
		}
		$TotalValue = array_sum( $EarnedTotal ) - array_sum( $RevisedTotal );
		return round_off_type( $TotalValue );
	}
}

if ( ! function_exists( 'redeem_points_from_order' ) ) {

	function redeem_points_from_order( $OrderId ) {
		global $wpdb;
		$RedeemTotal  = array();
		$RevisedTotal = array();
		$RedeemData   = $wpdb->get_results( $wpdb->prepare( "SELECT redeempoints FROM {$wpdb->prefix}rsrecordpoints WHERE orderid = %d and checkpoints != 'RVPFPPRP'", $OrderId ), ARRAY_A );
		foreach ( $RedeemData as $RedeemPoints ) {
			$RedeemTotal[] = $RedeemPoints['redeempoints'];
		}
		$RevisedData = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints FROM {$wpdb->prefix}rsrecordpoints WHERE checkpoints = 'RVPFRP' and orderid = %d", $OrderId ), ARRAY_A );
		foreach ( $RevisedData as $RevisedPoints ) {
			$RevisedTotal[] = $RevisedPoints['earnedpoints'];
		}
		$TotalValue = array_sum( $RedeemTotal ) - array_sum( $RevisedTotal );
		return $TotalValue;
	}
}

if ( ! function_exists( 'srp_footer_link' ) ) {

	function srp_footer_link( $footer_string ) {
		global $unsublink2;

		if ( $unsublink2 ) {
			return $unsublink2;
		}
		$domain = wp_parse_url( home_url(), PHP_URL_HOST );

		return str_replace(
			array(
				'{site_title}',
				'{site_address}',
				'{site_url}',
				'{woocommerce}',
				'{WooCommerce}',
			),
			array(
				wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
				$domain,
				$domain,
				'<a href="https://woocommerce.com">WooCommerce</a>',
				'<a href="https://woocommerce.com">WooCommerce</a>',
			),
			$footer_string
		);
	}
}

if ( ! function_exists( 'points_for_payment_gateways' ) ) {

	function points_for_payment_gateways( $order_id, $userid, $gatewayid ) {
		if ( '1' === get_option( 'rs_reward_type_for_payment_gateways_' . $gatewayid ) ) {
			$gatewaypoints = get_option( 'rs_reward_payment_gateways_' . $gatewayid );
		} else {
			$order = wc_get_order( $order_id );
			if ( is_object( $order ) ) {
				$cart_subtotal = ( '' === $order->get_meta( 'rs_cart_subtotal' ) ) ? srp_cart_subtotal( true, $order_id ) : $order->get_meta( 'rs_cart_subtotal' );
			} else {
				$cart_subtotal = srp_cart_subtotal( true, $order_id );
			}
			$percentpoints   = get_option( 'rs_reward_points_for_payment_gateways_in_percent_' . $gatewayid );
			$point_coversion = ( ( (float) $percentpoints / 100 ) * $cart_subtotal );
			$gatewaypoints   = earn_point_conversion( $point_coversion );
		}

		return round_off_type( $gatewaypoints );
	}
}

if ( ! function_exists( 'block_points_for_renewal_order_sumo_subscriptions' ) ) {

	/**
	 * Block points for renewal order subscriptions for SUMO Subsctipiton.
	 *
	 * @param int    $order_id Order ID.
	 * @param string $enable Whether to enable or disable.
	 */
	function block_points_for_renewal_order_sumo_subscriptions( $order_id, $enable ) {

		$order = wc_get_order( $order_id );
		if ( ! is_object( $order ) ) {
			return true;
		}

		if ( ! class_exists( 'SUMOSubscriptions' ) || 'yes' !== $enable ) {
			return true;
		}

		if ( function_exists( 'sumosubs_is_renewal_order' ) && sumosubs_is_renewal_order( $order ) && 'yes' === $order->get_meta( 'sumo_is_subscription_order' ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'rs_block_points_for_renewal_order_wc_subscriptions' ) ) {

	/**
	 * Block points for Renewal order in WooCommerce subscription.
	 *
	 * @param int    $order_id Order ID.
	 * @param string $enable Whether restriction is enabled or not.
	 */
	function rs_block_points_for_renewal_order_wc_subscriptions( $order_id, $enable ) {

		$order = wc_get_order( $order_id );
		if ( ! is_object( $order ) ) {
			return true;
		}

		if ( 'yes' !== $enable ) {
			return true;
		}

		if ( class_exists( 'WC_Subscriptions' ) && function_exists( 'wcs_order_contains_renewal' ) && wcs_order_contains_renewal( $order ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'expiry_date_for_points' ) ) {

	function expiry_date_for_points( $checkpoints = '' ) {
		$date = 999999999999;
		if ( 'BRP' === $checkpoints && 'yes' == get_option( 'rs_bday_points_activated' ) && ! empty( get_option( 'rs_bday_validity_period' ) ) ) {
			$date = time() + ( get_option( 'rs_bday_validity_period' ) * 24 * 60 * 60 );
		} elseif ( 'yes' == get_option( 'rs_point_expiry_activated' ) && ! empty( get_option( 'rs_point_to_be_expire' ) ) ) {
			$date = time() + ( get_option( 'rs_point_to_be_expire' ) * 24 * 60 * 60 );
		}

		return $date;
	}
}

if ( ! function_exists( 'round_off_type' ) ) {

	function round_off_type( $points, $args = array(), $display_separator = true ) {

		if ( ! $points ) {
			return 0;
		}

		extract(
			wp_parse_args(
				$args,
				array(
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'decimals'           => wc_get_price_decimals(),
				)
			)
		);

		if ( '1' == get_option( 'rs_round_off_type' ) ) {

			$decimals        = ( '1' == get_option( 'rs_decimal_seperator_check' ) ) ? 2 : $decimals;
			$round_off_value = ( '1' == get_option( 'rs_decimal_seperator_check' ) ) ? round( (float) $points, 2 ) : round( (float) $points, $decimals );
		} else {
			$decimals        = 0;
			$points          = str_replace( ',', '', $points );
			$round_off_value = ( '1' == get_option( 'rs_round_up_down' ) ) ? floor( $points ) : ceil( $points );
		}

		return ( true == $display_separator ) ? number_format( (float) $round_off_value, $decimals, $decimal_separator, $thousand_separator ) : $round_off_value;
	}
}

if ( ! function_exists( 'round_off_type_for_currency' ) ) {

	function round_off_type_for_currency( $currency, $args = array() ) {
		if ( '1' == get_option( 'rs_round_off_type' ) ) {
			return round_off_type( $currency, array(), false );
		} elseif ( '1' == get_option( 'rs_roundoff_type_for_currency' ) ) {
			if ( '1' == get_option( 'rs_decimal_seperator_check_for_currency' ) ) {
				return round( $currency, 2 );
			} else {
				extract(
					wp_parse_args(
						$args,
						array(
							'decimal_separator'  => wc_get_price_decimal_separator(),
							'thousand_separator' => wc_get_price_thousand_separator(),
							'decimals'           => wc_get_price_decimals(),
						)
					)
				);
				return round( $currency, $decimals );
			}
		} else {
			return ( '1' == get_option( 'rs_round_up_down' ) ) ? floor( $currency ) : ceil( $currency );
		}
	}
}

if ( ! function_exists( 'days_from_point_expiry_email' ) ) {

	function days_from_point_expiry_email() {
		global $wpdb;
		$templates = $wpdb->get_results( $wpdb->prepare( "SELECT noofdays FROM {$wpdb->prefix}rs_expiredpoints_email WHERE template_name = %s AND rs_status='ACTIVE'", get_option( 'rs_select_template' ) ), ARRAY_A );
		$days      = srp_check_is_array( $templates ) ? $templates[0]['noofdays'] : 0;
		return (int) $days;
	}
}

if ( ! function_exists( 'allow_reward_points_for_user' ) ) {

	function allow_reward_points_for_user( $userid ) {
		$allow_earn_points = get_user_meta( $userid, 'allow_user_to_earn_reward_points', true );
		if ( 'yes' != get_option( 'rs_enable_reward_program' ) ) {
			return true;
		}

		if ( ! ( 'yes' == $allow_earn_points ) && ! ( '' == $allow_earn_points ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'update_order_meta_if_points_awarded' ) ) {

	function update_order_meta_if_points_awarded( $orderid, $userid ) {
		$order = wc_get_order( $orderid );
		update_user_meta( $userid, 'rsfirsttime_redeemed', 1 );
		$order->update_meta_data( 'reward_points_awarded', 'yes' );
		$order->update_meta_data( 'earning_point_once', 1 );
		$order->update_meta_data( 'rs_revised_points_once', 2 );
		$order->save();
	}
}

if ( ! function_exists( 'get_reward_points_based_on_cart_total' ) ) {

	function get_reward_points_based_on_cart_total( $OrderTotal, $order_shipping_cost = false, $user_id = false ) {
		if ( '2' == get_option( 'rs_enable_cart_total_reward_points' ) ) {
			return 0;
		}

		// Membership compatibility.
		$restrict_membership = 'no';
		if ( 'yes' == get_option( 'rs_enable_restrict_reward_points' ) && function_exists( 'check_plan_exists' ) && $user_id ) {
			$restrict_membership = check_plan_exists( $user_id ) ? 'yes' : 'no';
			if ( 'yes' != $restrict_membership ) {
				return 0;
			}
		}

		$shipping_cost = is_object( WC()->cart ) ? WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax() : 0;
		if ( $order_shipping_cost ) {
			$shipping_cost = $order_shipping_cost;
		}

		$OrderTotal = ( 'yes' == get_option( 'rs_exclude_shipping_cost_based_on_cart_total' ) ) ? $OrderTotal - $shipping_cost : $OrderTotal;

		if ( '1' == get_option( 'rs_reward_type_for_cart_total' ) ) {
			$PointsToAward = empty( get_option( 'rs_reward_points_for_cart_total_in_fixed' ) ) ? 0 : get_option( 'rs_reward_points_for_cart_total_in_fixed' );
		} else {
			$PointsToAward    = empty( get_option( 'rs_reward_points_for_cart_total_in_percent' ) ) ? 0 : convert_percent_value_as_points( get_option( 'rs_reward_points_for_cart_total_in_percent' ), $OrderTotal );
			$max_points_limit = get_option( 'rs_restrict_maximum_points_for_product_purchase', '' );

			if ( '' !== $max_points_limit && $PointsToAward >= $max_points_limit ) {
				$PointsToAward = $max_points_limit;
			}
		}

		return $PointsToAward;
	}
}

if ( ! function_exists( 'rs_get_reward_points_based_on_cart_total_for_referrer' ) ) {

	function rs_get_reward_points_based_on_cart_total_for_referrer( $order = false, $order_shipping_cost = false ) {

		if ( '2' == get_option( 'rs_global_enable_disable_sumo_referral_reward' ) ) {
			return 0;
		}

		if ( '1' == get_option( 'rs_award_points_for_cart_or_product_total_for_refferal_system', 1 ) ) {
			return 0;
		}

		// Cart shipping cost
		$shipping_cost = is_object( WC()->cart ) ? WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax() : 0;
		if ( $order_shipping_cost ) {
			// Order shipping cost
			$shipping_cost = $order_shipping_cost;
		}

		$total           = is_object( $order ) ? $order->get_total() : WC()->cart->total;
		$total           = ( 'yes' == get_option( 'rs_exclude_shipping_cost_based_on_cart_total_for_referral_module' ) ) ? $total - $shipping_cost : $total;
		$referrer_points = '2' == get_option( 'rs_global_referral_reward_type_for_cart_total', 1 ) ? $total * (float) get_option( 'rs_global_referral_reward_percent_for_cart_total', 0 ) / 100 : get_option( 'rs_global_referral_reward_point_for_cart_total', 0 );

		return $referrer_points;
	}
}

if ( ! function_exists( 'rs_get_reward_points_based_on_cart_total_for_referred' ) ) {

	function rs_get_reward_points_based_on_cart_total_for_referred( $order = false, $order_shipping_cost = false ) {

		if ( '2' == get_option( 'rs_global_enable_disable_sumo_referral_reward' ) ) {
			return 0;
		}

		if ( '1' == get_option( 'rs_award_points_for_cart_or_product_total_for_refferal_system', 1 ) ) {
			return 0;
		}

		// Cart shipping cost
		$shipping_cost = is_object( WC()->cart ) ? WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax() : 0;
		if ( $order_shipping_cost ) {
			// Order shipping cost
			$shipping_cost = $order_shipping_cost;
		}

		$total           = is_object( $order ) ? $order->get_total() : WC()->cart->total;
		$total           = ( 'yes' == get_option( 'rs_exclude_shipping_cost_based_on_cart_total_for_referral_module' ) ) ? $total - $shipping_cost : $total;
		$referred_points = '2' == get_option( 'rs_global_referral_reward_type_refer_for_cart_total', 1 ) ? $total * (float) get_option( 'rs_global_referral_reward_percent_get_refer_for_cart_total', 0 ) / 100 : get_option( 'rs_global_referral_reward_point_get_refer_for_cart_total', 0 );

		return $referred_points;
	}
}

if ( ! function_exists( 'get_list_of_modules' ) ) {

	function get_list_of_modules( $value = '' ) {
		$args = array(
			'fpproductpurchase'    => 'name' == $value ? __( 'Product Purchase', 'rewardsystem' ) : get_option( 'rs_product_purchase_activated' ),
			'fpbuyingpoints'       => 'name' == $value ? __( 'Buying Points', 'rewardsystem' ) : get_option( 'rs_buyingpoints_activated' ),
			'fpreferralsystem'     => 'name' == $value ? __( 'Referral System', 'rewardsystem' ) : get_option( 'rs_referral_activated' ),
			'fpsocialreward'       => 'name' == $value ? __( 'Social Reward Points', 'rewardsystem' ) : get_option( 'rs_social_reward_activated' ),
			'fpactionreward'       => 'name' == $value ? __( 'Action Reward Points', 'rewardsystem' ) : get_option( 'rs_reward_action_activated' ),
			'fpbirthday'           => 'name' == $value ? __( 'Birthday Reward Points', 'rewardsystem' ) : get_option( 'rs_bday_points_activated' ),
			'fppromotional'        => 'name' == $value ? __( 'Promotion Reward Points', 'rewardsystem' ) : get_option( 'rs_promotional_points_activated' ),
			'fpbonuspoints'        => 'name' == $value ? __( 'Bonus Reward Points', 'rewardsystem' ) : get_option( 'rs_bonus_points_activated' ),
			'fpanniversarypoints'  => 'name' == $value ? __( 'Anniversary Reward Points', 'rewardsystem' ) : get_option( 'rs_anniversary_points_activated' ),
			'fppointexpiry'        => 'name' == $value ? __( 'Points Expiry', 'rewardsystem' ) : get_option( 'rs_point_expiry_activated' ),
			'fpredeeming'          => 'name' == $value ? __( 'Redeeming Points', 'rewardsystem' ) : get_option( 'rs_redeeming_activated' ),
			'fppointprice'         => 'name' == $value ? __( 'Point Price', 'rewardsystem' ) : get_option( 'rs_point_price_activated' ),
			'fpmail'               => 'name' == $value ? __( 'Email', 'rewardsystem' ) : get_option( 'rs_email_activated' ),
			'fpemailexpiredpoints' => 'name' == $value ? __( 'Email Template for Expire', 'rewardsystem' ) : get_option( 'rs_email_template_expire_activated' ),
			'fpgiftvoucher'        => 'name' == $value ? __( 'Gift Voucher', 'rewardsystem' ) : get_option( 'rs_gift_voucher_activated' ),
			'fpsms'                => 'name' == $value ? __( 'SMS', 'rewardsystem' ) : get_option( 'rs_sms_activated' ),
			'fpcashback'           => 'name' == $value ? __( 'Cashback', 'rewardsystem' ) : get_option( 'rs_cashback_activated' ),
			'fpnominee'            => 'name' == $value ? __( 'Nominee', 'rewardsystem' ) : get_option( 'rs_nominee_activated' ),
			'fppointurl'           => 'name' == $value ? __( 'Point URL', 'rewardsystem' ) : get_option( 'rs_point_url_activated' ),
			'fprewardgateway'      => 'name' == $value ? __( 'Reward Points Payment Gateway', 'rewardsystem' ) : get_option( 'rs_gateway_activated' ),
			'fpsendpoints'         => 'name' == $value ? __( 'Send Points', 'rewardsystem' ) : get_option( 'rs_send_points_activated' ),
			'fpimportexport'       => 'name' == $value ? __( 'Import/Export Points', 'rewardsystem' ) : get_option( 'rs_imp_exp_activated' ),
			'fpreportsincsv'       => 'name' == $value ? __( 'Reports', 'rewardsystem' ) : get_option( 'rs_report_activated' ),
			'fpreset'              => 'name' == $value ? __( 'Reset', 'rewardsystem' ) : get_option( 'rs_reset_activated' ),
		);

		if ( class_exists( 'SUMODiscounts' ) ) {
			$args['fpdiscounts'] = 'name' == $value ? __( 'SUMO Discounts Compatibility', 'rewardsystem' ) : get_option( 'rs_discounts_compatability_activated' );
		}

		if ( class_exists( 'SUMORewardcoupons' ) ) {
			$args['fpcoupon'] = 'name' == $value ? __( 'SUMO Coupon Compatibility', 'rewardsystem' ) : get_option( 'rs_coupon_compatability_activated' );
		}

		return $args;
	}
}

if ( ! function_exists( 'modules_file_name' ) ) {

	function modules_file_name() {
		return array(
			'fpproductpurchase',
			'fpbuyingpoints',
			'fpreferralsystem',
			'fpsocialreward',
			'fpactionreward',
			'fpbirthday',
			'fppointexpiry',
			'fpredeeming',
			'fppointprice',
			'fpemailexpiredpoints',
			'fpgiftvoucher',
			'fpsms',
			'fpcashback',
			'fpnominee',
			'fppointurl',
			'fprewardgateway',
			'fpsendpoints',
			'fpimportexport',
			'fpreportsincsv',
			'fpdiscounts',
			'fpcoupon',
			'fpreset',
		);
	}
}

if ( ! function_exists( 'send_mail' ) ) {

	function send_mail( $to, $subject, $message ) {
		global $unsublink2;
		$user = get_user_by( 'email', $to );

		if ( ! $user ) {
			return;
		}

		$wpnonce    = wp_create_nonce( 'rs_unsubscribe_' . $user->ID );
		$unsublink  = esc_url_raw(
			add_query_arg(
				array(
					'userid' => $user->ID,
					'unsub'  => 'yes',
					'nonce'  => $wpnonce,
				),
				site_url()
			)
		);
		$unsublink  = '<a href=' . $unsublink . '>' . $unsublink . '</a>';
		$unsublink2 = str_replace( '{rssitelinkwithid}', $unsublink, get_option( 'rs_unsubscribe_link_for_email' ) );

		add_filter( 'woocommerce_email_footer_text', 'srp_footer_link' );

		ob_start();
		wc_get_template( 'emails/email-header.php', array( 'email_heading' => $subject ) );
		echo do_shortcode( $message );
		wc_get_template( 'emails/email-footer.php' );
		$woo_temp_msg = ob_get_clean();
		$headers      = "MIME-Version: 1.0\r\n";
		$headers     .= "Content-Type: text/html; charset=UTF-8\r\n";
		if ( '2' == get_option( 'rs_enable_email_function_actions', '2' ) ) {
			$mailer = WC()->mailer();
			$mailer->send( $to, $subject, $woo_temp_msg, $headers );
		} elseif ( '1' == get_option( 'rs_enable_email_function_actions', '2' ) ) {
			mail( $to, $subject, $woo_temp_msg, $headers );
		}

		remove_filter( 'woocommerce_email_footer_text', 'srp_footer_link' );
	}
}

if ( ! function_exists( 'get_referrer_ip_address' ) ) {

	function get_referrer_ip_address() {
		$ipaddress = '';

		if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
			$ipaddress = wc_clean( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) );
		} elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ipaddress = wc_clean( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ipaddress = wc_clean( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ipaddress = wc_clean( wp_unslash( $_SERVER['HTTP_X_FORWARDED'] ) );
		} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ipaddress = wc_clean( wp_unslash( $_SERVER['HTTP_FORWARDED_FOR'] ) );
		} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ipaddress = wc_clean( wp_unslash( $_SERVER['HTTP_FORWARDED'] ) );
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ipaddress = wc_clean( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		return $ipaddress;
	}
}

if ( ! function_exists( 'rs_custom_search_fields' ) ) {

	function rs_custom_search_fields( $args ) {
		$args     = wp_parse_args(
			$args,
			array(
				'class'              => '',
				'id'                 => '',
				'name'               => '',
				'type'               => '',
				'action'             => '',
				'title'              => '',
				'placeholder'        => '',
				'css'                => '',
				'multiple'           => true,
				'allow_clear'        => true,
				'selected'           => true,
				'options'            => array(),
				'translation_string' => '',
			)
		);
			$name = esc_attr( '' !== $args['name'] ? $args['name'] : $args['id'] );
		?>
		<select <?php echo esc_attr( $args['multiple'] ? 'multiple="multiple"' : '' ); ?> 
			name="<?php echo esc_attr( $args['multiple'] ? $name . '[]' : $name ); ?>"
			id="<?php echo esc_attr( $args['id'] ); ?>"
			class="<?php echo esc_attr( $args['class'] ); ?>" 
			data-action="<?php echo esc_attr( $args['action'] ); ?>" 
			data-placeholder="<?php esc_html_e( esc_attr( $args['placeholder'] ), 'rewardsystem' ); ?>">
				<?php
				if ( is_array( $args['options'] ) ) {
					foreach ( $args['options'] as $id ) {
						$option_value = '';
						switch ( $args['type'] ) {
							case 'product':
								$product = wc_get_product( $id );
								if ( $product ) {
									$option_value = wp_kses_post( $product->get_formatted_name() );
								}
								break;
							case 'customer':
								$user = get_user_by( 'id', $id );
								if ( $user ) {
									$option_value = esc_html( esc_html( $user->display_name ) . '(#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' );
								}
								break;
							case 'customfields':
								$option_value = esc_html( get_the_title( $id ) );
								break;
						}
						if ( $option_value ) {
							?>
						<option value="<?php echo esc_attr( $id ); ?>" <?php echo esc_html( $args['selected'] ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php
						}
					}
				}
				?>
		</select>
		<?php
	}
}

function calculate_point_price_for_products( $product_id ) {
	$data[ $product_id ] = '';
	if ( 'yes' != get_option( 'rs_point_price_activated' ) ) {
		return $data;
	}

	if ( '2' == get_option( 'rs_enable_disable_point_priceing' ) ) {
		return $data;
	}

	// Simple Product Data
	$EnablePointPriceForSimple        = get_post_meta( $product_id, '_rewardsystem_enable_point_price', true );
	$ProductLevelPointsForSimple      = get_post_meta( $product_id, '_rewardsystem__points', true );
	$PointsTypeForSimple              = get_post_meta( $product_id, '_rewardsystem_point_price_type', true );
	$PointsBasedOnConversionForSimple = get_post_meta( $product_id, '_rewardsystem__points_based_on_conversion', true );
	$PointPriceTypeForSimple          = get_post_meta( $product_id, '_rewardsystem_enable_point_price_type', true );

	// Variable Product Data
	$EnablePointPriceForVariation       = get_post_meta( $product_id, '_enable_reward_points_price', true );
	$ProductLevelPointsForVariation     = get_post_meta( $product_id, 'price_points', true );
	$PointsTypeForVariable              = get_post_meta( $product_id, '_enable_reward_points_price_type', true );
	$PointsBasedOnConversionForVariable = get_post_meta( $product_id, '_price_points_based_on_conversion', true );
	$PointPriceTypeForVariable          = get_post_meta( $product_id, '_enable_reward_points_pricing_type', true );

	$GlobalPointPriceType = get_option( 'rs_global_point_price_type' );
	$ProductObj           = srp_product_object( $product_id );
	if ( 'no' == get_option( 'rs_enable_product_category_level_for_points_price' ) ) {
		$Options        = array(
			'applicable_for'      => get_option( 'rs_point_pricing_global_level_applicable_for' ),
			'included_products'   => get_option( 'rs_include_products_for_point_pricing' ),
			'excluded_products'   => get_option( 'rs_exclude_products_for_point_pricing' ),
			'included_categories' => get_option( 'rs_include_particular_categories_for_point_pricing' ),
			'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_point_pricing' ),
		);
		$product_filter = srp_product_filter_for_quick_setup( $product_id, $product_id, $Options );
	} elseif ( 'yes' == get_option( 'rs_enable_product_category_level_for_points_price' ) ) {
		$product_filter = '1';
	}
	if ( is_object( $ProductObj ) && ( 'simple' == srp_product_type( $product_id ) || ( 'subscription' == srp_product_type( $product_id ) ) || ( 'lottery' == srp_product_type( $product_id ) ) ) ) {
		if ( '1' == $product_filter && 'yes' == $EnablePointPriceForSimple ) {
			$data[ $product_id ] = product_level_point_pricing_value( $PointsTypeForSimple, $PointPriceTypeForSimple, $ProductLevelPointsForSimple, $product_id );
		} elseif ( '2' == $product_filter ) {
			$data[ $product_id ] = global_level_point_pricing_value( $product_id );
		}
	} else {
		if ( '0' != wp_get_post_parent_id( $product_id ) ) {
			$ProductObjForVariable = new WC_Product_Variation( $product_id );
			$ProductIdForVariable  = get_parent_id( $ProductObjForVariable );
		} else {
			$ProductIdForVariable = $product_id;
		}
		if ( '1' == $product_filter ) {
			if ( '1' == $EnablePointPriceForVariation ) {
				if ( ( '2' == $PointPriceTypeForVariable && ! empty( $ProductLevelPointsForVariation ) ) ) {
					$data[ $product_id ] = $ProductLevelPointsForVariation;
				} elseif ( 1 == $PointsTypeForVariable ) {
						$data[ $product_id ] = ( empty( $ProductLevelPointsForVariation ) ) ? category_level_point_pricing_value( $ProductIdForVariable ) : $ProductLevelPointsForVariation;
				} else {
					$data[ $product_id ] = point_price_based_on_conversion( $product_id );
				}
			}
		} elseif ( '2' == $product_filter ) {
			$data[ $product_id ] = global_level_point_pricing_value( $product_id );
		}
	}
	if ( is_object( $ProductObj ) && ( 'booking' == srp_product_type( $product_id ) ) ) {
		$booking_points      = get_post_meta( $product_id, 'booking_points', true );
		$data[ $product_id ] = $booking_points;
	}
	return $data;
}

function product_level_point_pricing_value( $PointsTypeForSimple, $PointPriceTypeForSimple, $ProductLevelPointsForSimple, $product_id ) {
	if ( '2' == $PointPriceTypeForSimple ) {
		$data = empty( $ProductLevelPointsForSimple ) ? category_level_point_pricing_value( $product_id ) : $ProductLevelPointsForSimple;
	} elseif ( ( 1 == $PointsTypeForSimple ) ) {
			$data = ( empty( $ProductLevelPointsForSimple ) ) ? category_level_point_pricing_value( $product_id ) : $ProductLevelPointsForSimple;
	} else {
		$data = point_price_based_on_conversion( $product_id );
	}
	return $data;
}

function category_level_point_pricing_value( $product_id ) {
	$term = get_the_terms( $product_id, 'product_cat' );
	if ( srp_check_is_array( $term ) ) {
		foreach ( $term as $term ) {
			$EnablePointPriceInCategory = srp_term_meta( $term->term_id, 'enable_point_price_category' );
			if ( ( 'yes' == $EnablePointPriceInCategory ) ) {
				$PointsPriceType = srp_term_meta( $term->term_id, 'pricing_category_types' );
				$PointsType      = srp_term_meta( $term->term_id, 'point_price_category_type' );
				$PointPriceValue = srp_term_meta( $term->term_id, 'rs_category_points_price' );
				if ( '2' == $PointsPriceType ) {
					$data = empty( $PointPriceValue ) ? global_level_point_pricing_value( $product_id ) : $PointPriceValue;
				} elseif ( '1' == $PointsType ) {
						$data = empty( $PointPriceValue ) ? global_level_point_pricing_value( $product_id ) : $PointPriceValue;
				} else {
					$data = point_price_based_on_conversion( $product_id );
				}
			} else {
				$data = global_level_point_pricing_value( $product_id );
			}
		}
	} else {
		$data = global_level_point_pricing_value( $product_id );
	}
	return $data;
}

function global_level_point_pricing_value( $product_id ) {
	$data                     = '';
	$EnablePointPriceInGlobal = get_option( 'rs_local_enable_disable_point_price_for_product' );
	if ( '1' == $EnablePointPriceInGlobal ) {
		$PointPricingType = get_option( 'rs_pricing_type_global_level' );
		$PointsType       = get_option( 'rs_global_point_price_type' );
		if ( ( '2' == $PointPricingType && ! empty( get_option( 'rs_local_price_points_for_product' ) ) ) || ( '1' == $PointsType && ! empty( get_option( 'rs_local_price_points_for_product' ) ) ) ) {
			$data = get_option( 'rs_local_price_points_for_product' );
		} else {
			$data = point_price_based_on_conversion( $product_id );
		}
	}
	return $data;
}

function check_display_price_type( $product_id ) {
	if ( 'yes' !== get_option( 'rs_point_price_activated' ) ) {
		return;
	}

	if ( '2' === get_option( 'rs_enable_disable_point_priceing' ) ) {
		return;
	}

	if ( 'no' == get_option( 'rs_enable_product_category_level_for_points_price' ) ) {  // Quick Setup
		if ( '2' == get_option( 'rs_local_enable_disable_point_price_for_product' ) ) {
			return;
		}

		$ProductFilters            = array(
			'applicable_for'      => get_option( 'rs_point_pricing_global_level_applicable_for' ),
			'included_products'   => get_option( 'rs_include_products_for_point_pricing' ),
			'excluded_products'   => get_option( 'rs_exclude_products_for_point_pricing' ),
			'included_categories' => get_option( 'rs_include_particular_categories_for_point_pricing' ),
			'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_point_pricing' ),
		);
		$ProductsToApplyPointPrice = srp_product_filter_for_quick_setup( $product_id, $product_id, $ProductFilters );
		if ( '2' == $ProductsToApplyPointPrice ) {      
			if ( '1' == get_option( 'rs_pricing_type_global_level' ) ) {
				if ( '' != get_option( 'rs_local_price_points_for_product', '' ) && '1' == get_option( 'rs_global_point_price_type', '1' ) ) {
					return '1';
				} elseif ( '2' == get_option( 'rs_global_point_price_type', '1' ) ) {
					return '1';
				}
			} elseif ( '' != get_option( 'rs_local_price_points_for_product' ) ) {
					return '2';
			}
		}
	} else {
		$product_obj = wc_get_product( $product_id );

		if ( 'simple' === $product_obj->get_type() ) {
			$PointPriceinProductLevel = get_post_meta( $product_id, '_rewardsystem_enable_point_price', true );
			$point_price_type         = get_post_meta( $product_id, '_rewardsystem_enable_point_price_type', true );
			$PointPriceValue          = get_post_meta( $product_id, '_rewardsystem__points', true );
			$display_type             = get_post_meta( $product_id, '_rewardsystem_point_price_type', true );

		} else {
			$PointPriceinProductLevel = get_post_meta( $product_id, '_enable_reward_points_price', true );
			$point_price_type         = get_post_meta( $product_id, '_enable_reward_points_pricing_type', true );
			$PointPriceValue          = get_post_meta( $product_id, 'price_points', true );
			$display_type             = get_post_meta( $product_id, '_enable_reward_points_price_type', true );

		}

		if ( 'no' == $PointPriceinProductLevel || '2' == $PointPriceinProductLevel ) {
			return;
		}

		if ( '1' === $point_price_type ) {
			return '1';
		} else {
			return '2';
		}

		return category_level_display_type( $product_id );
	}
}

function category_level_display_type( $product_id ) {
	$term = get_the_terms( $product_id, 'product_cat' );
	if ( ! srp_check_is_array( $term ) ) {
		return global_level_display_type();
	}

	foreach ( $term as $term ) {
		if ( ( 'yes' != srp_term_meta( $term->term_id, 'enable_point_price_category' ) ) ) {
			return global_level_display_type();
		}

		$PointsPriceType = srp_term_meta( $term->term_id, 'pricing_category_types' );
		$PointPriceValue = srp_term_meta( $term->term_id, 'rs_category_points_price' );
		if ( '1' == $PointsPriceType && '' != $PointPriceValue ) {
			return '1';
		} elseif ( '' != $PointPriceValue ) {
				return '2';
		}
	}
	return global_level_display_type();
}

function global_level_display_type() {
	if ( '1' == get_option( 'rs_local_enable_disable_point_price_for_product' ) ) {
		if ( '1' == get_option( 'rs_pricing_type_global_level' ) && '' != get_option( 'rs_local_price_points_for_product' ) ) {
			return '1';
		} elseif ( '' != get_option( 'rs_local_price_points_for_product' ) ) {
				return '2';
		}
	}
}

function get_point_level( $productid, $variationid, $referred_user, $getting_referrer, $socialreward ) {
	if ( 'yes' == $socialreward ) {
		if ( 'no' == get_option( 'rs_enable_product_category_level_for_social_reward' ) ) {
			$Options = array(
				'applicable_for'      => get_option( 'rs_social_reward_global_level_applicable_for' ),
				'included_products'   => get_option( 'rs_include_products_for_social_reward' ),
				'excluded_products'   => get_option( 'rs_exclude_products_for_social_reward' ),
				'included_categories' => get_option( 'rs_include_particular_categories_for_social_reward' ),
				'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_social_reward' ),
			);
			if ( '1' == get_option( 'rs_global_social_enable_disable_reward' ) ) {
				return srp_product_filter_for_quick_setup( $productid, $variationid, $Options );
			} else {
				return false;
			}
		} elseif ( 'yes' == get_option( 'rs_enable_product_category_level_for_social_reward' ) ) {
			return '1';
		}
	} elseif ( '' != $referred_user || 'yes' == $getting_referrer ) {
		if ( 'no' == get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) ) {
			$Options = array(
				'applicable_for'      => get_option( 'rs_referral_product_purchase_global_level_applicable_for' ),
				'included_products'   => get_option( 'rs_include_products_for_referral_product_purchase' ),
				'excluded_products'   => get_option( 'rs_exclude_products_for_referral_product_purchase' ),
				'included_categories' => get_option( 'rs_include_particular_categories_for_referral_product_purchase' ),
				'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_referral_product_purchase' ),
			);
			return srp_product_filter_for_quick_setup( $productid, $variationid, $Options );
		} elseif ( 'yes' == get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) ) {
			return '1';
		}
	} elseif ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
			$Options = array(
				'applicable_for'      => get_option( 'rs_product_purchase_global_level_applicable_for' ),
				'included_products'   => get_option( 'rs_include_products_for_product_purchase' ),
				'excluded_products'   => get_option( 'rs_exclude_products_for_product_purchase' ),
				'included_categories' => get_option( 'rs_include_particular_categories_for_product_purchase' ),
				'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_product_purchase' ),
			);
			return srp_product_filter_for_quick_setup( $productid, $variationid, $Options );
	} elseif ( 'yes' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
		return '1';
	}
}

function check_level_of_enable_reward_point( $args = array() ) {
	$default_args = array(
		'variationid'      => 0,
		'checklevel'       => 'no',
		'referred_user'    => '',
		'getting_referrer' => 'no',
		'socialreward'     => 'no',
		'rewardfor'        => '',
		'payment_price'    => 0,
		'order'            => false,
	);
	$parse_args   = wp_parse_args( $args, $default_args );
	extract( $parse_args );

	$user_id             = ( $order && is_object( wc_get_order( $order ) ) ) ? $order->get_user_id() : get_current_user_id();
	$memebershiprestrict = 'no';
	if ( 'yes' == get_option( 'rs_enable_restrict_reward_points' ) && function_exists( 'check_plan_exists' ) && $user_id ) {
		$memebershiprestrict = check_plan_exists( $user_id ) ? 'no' : 'yes';
	}

	$itemquantity = isset( $item['qty'] ) ? $item['qty'] : $item['quantity'];
	if ( 'no' == $memebershiprestrict ) {
		$point_level = get_point_level( $productid, $variationid, $referred_user, $getting_referrer, $socialreward );
		if ( '1' == $point_level ) {
			return is_product_level( $productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor, $payment_price, $itemquantity );
		} elseif ( '2' == $point_level ) {
			return is_global_level( $productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor, $payment_price, $itemquantity );
		}
	}
}

function is_product_level( $productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor, $payment_price, $itemquantity ) {
	// Product Level
	if ( '' != $referred_user ) {
		$productlevel              = empty( $variationid ) ? get_post_meta( $productid, '_rewardsystemreferralcheckboxvalue', true ) : get_post_meta( $variationid, '_enable_referral_reward_points', true );
		$productlevelrewardtype    = empty( $variationid ) ? get_post_meta( $productid, '_referral_rewardsystem_options', true ) : get_post_meta( $variationid, '_select_referral_reward_rule', true );
		$productlevelrewardpoints  = empty( $variationid ) ? get_post_meta( $productid, '_referralrewardsystempoints', true ) : get_post_meta( $variationid, '_referral_reward_points', true );
		$productlevelrewardpercent = empty( $variationid ) ? get_post_meta( $productid, '_referralrewardsystempercent', true ) : get_post_meta( $variationid, '_referral_reward_percent', true );
		if ( 'yes' == $getting_referrer ) {
			$productlevel              = empty( $variationid ) ? get_post_meta( $productid, '_rewardsystemreferralcheckboxvalue', true ) : get_post_meta( $variationid, '_enable_referral_reward_points', true );
			$productlevelrewardtype    = empty( $variationid ) ? get_post_meta( $productid, '_referral_rewardsystem_options_getrefer', true ) : get_post_meta( $variationid, '_select_referral_reward_rule_getrefer', true );
			$productlevelrewardpoints  = empty( $variationid ) ? get_post_meta( $productid, '_referralrewardsystempoints_for_getting_referred', true ) : get_post_meta( $variationid, '_referral_reward_points_getting_refer', true );
			$productlevelrewardpercent = empty( $variationid ) ? get_post_meta( $productid, '_referralrewardsystempercent_for_getting_referred', true ) : get_post_meta( $variationid, '_referral_reward_percent_getting_refer', true );
		}
		$regularprice    = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
		$convertedpoints = convert_percent_value_as_points( $productlevelrewardpercent, $regularprice );
		if ( ( 'yes' == get_option( 'rs_restrict_referral_reward' ) ) ) {
			$convertedpoints = $convertedpoints / $itemquantity;
			$itemquantity    = 1;
		}
	} elseif ( 'yes' == $getting_referrer ) {
		$productlevel              = empty( $variationid ) ? get_post_meta( $productid, '_rewardsystemreferralcheckboxvalue', true ) : get_post_meta( $variationid, '_enable_referral_reward_points', true );
		$productlevelrewardtype    = empty( $variationid ) ? get_post_meta( $productid, '_referral_rewardsystem_options_getrefer', true ) : get_post_meta( $variationid, '_select_referral_reward_rule_getrefer', true );
		$productlevelrewardpoints  = empty( $variationid ) ? get_post_meta( $productid, '_referralrewardsystempoints_for_getting_referred', true ) : get_post_meta( $variationid, '_referral_reward_points_getting_refer', true );
		$productlevelrewardpercent = empty( $variationid ) ? get_post_meta( $productid, '_referralrewardsystempercent_for_getting_referred', true ) : get_post_meta( $variationid, '_referral_reward_percent_getting_refer', true );
		$regularprice              = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
		$convertedpoints           = convert_percent_value_as_points( $productlevelrewardpercent, $regularprice );
	} elseif ( 'yes' == $socialreward ) {
		$newarray                  = get_social_rewardpoints( $productid, $rewardfor, '1' );
		$productlevel              = $newarray['enable_level'];
		$productlevelrewardtype    = $newarray['rewardtype'];
		$productlevelrewardpoints  = $newarray['rewardpoints'];
		$productlevelrewardpercent = $newarray['rewardpercent'];
		$regularprice              = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
		$convertedpoints           = convert_percent_value_as_points( $productlevelrewardpercent, $regularprice );
	} else {
		$productlevel              = empty( $variationid ) ? get_post_meta( $productid, '_rewardsystemcheckboxvalue', true ) : get_post_meta( $variationid, '_enable_reward_points', true );
		$productlevelrewardtype    = empty( $variationid ) ? get_post_meta( $productid, '_rewardsystem_options', true ) : get_post_meta( $variationid, '_select_reward_rule', true );
		$productlevelrewardpoints  = empty( $variationid ) ? get_post_meta( $productid, '_rewardsystempoints', true ) : get_post_meta( $variationid, '_reward_points', true );
		$productlevelrewardpercent = empty( $variationid ) ? get_post_meta( $productid, '_rewardsystempercent', true ) : get_post_meta( $variationid, '_reward_percent', true );
		$regularprice              = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
		$convertedpoints           = convert_percent_value_as_points( $productlevelrewardpercent, $regularprice );
		if ( 'yes' == get_option( 'rs_restrict_reward' ) ) {
			$convertedpoints = $convertedpoints / $itemquantity;
			$itemquantity    = 1;
		}
	}
	if ( ( 'yes' == $productlevel ) || ( '1' == $productlevel ) || ( '' == $productlevel ) ) {
		if ( '1' == $productlevelrewardtype && '' != $productlevelrewardpoints ) {
			return ( 'yes' == $checklevel ) ? '1' : ( $productlevelrewardpoints * $itemquantity );
		} elseif ( '2' == $productlevelrewardtype && '' != $productlevelrewardpercent ) {
			return ( 'yes' == $checklevel ) ? '1' : $convertedpoints;
		}
		return is_category_level( $productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor, $payment_price, $itemquantity );
	}
}

function is_category_level( $productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor, $payment_price, $itemquantity ) {
	// Category Level
	$term              = get_the_terms( $productid, 'product_cat' );
	$cat_level_enabled = array();
	$cat_level_point   = array();
	$cat_level_percent = array();
	if ( srp_check_is_array( $term ) ) {
		$categorylist = wp_get_post_terms( $productid, 'product_cat' );
		$getcount     = count( $categorylist );
		foreach ( $term as $terms ) {
			$termid = $terms->term_id;
			if ( '' != $referred_user ) {
				$categorylevel              = srp_term_meta( $termid, 'enable_referral_reward_system_category' );
				$categorylevelrewardtype    = srp_term_meta( $termid, 'referral_enable_rs_rule' );
				$categorylevelrewardpoints  = srp_term_meta( $termid, 'referral_rs_category_points' );
				$categorylevelrewardpercent = srp_term_meta( $termid, 'referral_rs_category_percent' );
				if ( 'yes' == $getting_referrer ) {
					$categorylevel              = srp_term_meta( $termid, 'enable_referral_reward_system_category' );
					$categorylevelrewardtype    = srp_term_meta( $termid, 'referral_enable_rs_rule_refer' );
					$categorylevelrewardpoints  = srp_term_meta( $termid, 'referral_rs_category_points_get_refered' );
					$categorylevelrewardpercent = srp_term_meta( $termid, 'referral_rs_category_percent_get_refer' );
				}
				$regularprice    = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
				$convertedpoints = convert_percent_value_as_points( $categorylevelrewardpercent, $regularprice );
				if ( ( 'yes' == get_option( 'rs_restrict_referral_reward' ) ) ) {
					$convertedpoints = $convertedpoints / $itemquantity;
					$itemquantity    = 1;
				}
			} elseif ( 'yes' == $getting_referrer ) {
				$categorylevel              = srp_term_meta( $termid, 'enable_referral_reward_system_category' );
				$categorylevelrewardtype    = srp_term_meta( $termid, 'referral_enable_rs_rule_refer' );
				$categorylevelrewardpoints  = srp_term_meta( $termid, 'referral_rs_category_points_get_refered' );
				$categorylevelrewardpercent = srp_term_meta( $termid, 'referral_rs_category_percent_get_refer' );
				$regularprice               = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
				$convertedpoints            = convert_percent_value_as_points( $categorylevelrewardpercent, $regularprice );
			} elseif ( 'yes' == $socialreward ) {
				$newarray                   = get_social_rewardpoints( $productid, $rewardfor, '2', $termid );
				$categorylevel              = $newarray['enable_level'];
				$categorylevelrewardtype    = $newarray['rewardtype'];
				$categorylevelrewardpoints  = $newarray['rewardpoints'];
				$categorylevelrewardpercent = $newarray['rewardpercent'];
				$regularprice               = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
				$convertedpoints            = convert_percent_value_as_points( $categorylevelrewardpercent, $regularprice );
			} else {
				$categorylevel              = srp_term_meta( $termid, 'enable_reward_system_category' );
				$categorylevelrewardtype    = srp_term_meta( $termid, 'enable_rs_rule' );
				$categorylevelrewardpoints  = srp_term_meta( $termid, 'rs_category_points' );
				$categorylevelrewardpercent = srp_term_meta( $termid, 'rs_category_percent' );
				$categorylevelminqty        = srp_term_meta( $termid, 'rs_get_min_quantity' );
				$regularprice               = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
				$convertedpoints            = convert_percent_value_as_points( $categorylevelrewardpercent, $regularprice );
				if ( 'yes' == get_option( 'rs_restrict_reward' ) ) {
					$convertedpoints = $convertedpoints / $itemquantity;
					$itemquantity    = 1;
				}
			}
			if ( $getcount >= 1 ) {
				if ( ( 'yes' == $categorylevel ) ) {
					if ( ( '1' == $categorylevelrewardtype ) && '' != $categorylevelrewardpoints ) {
						if ( 'yes' == $checklevel ) {
							$cat_level_enabled[] = '2';
						} else {
							$quantity          = 'yes' == get_option( 'rs_restrict_reward' ) ? 1 : $itemquantity;
							$cat_level_point[] = $categorylevelrewardpoints * $quantity;
						}
					} elseif ( ( '2' == $categorylevelrewardtype ) && '' != $categorylevelrewardpercent ) {
						if ( 'yes' == $checklevel ) {
							$cat_level_enabled[] = '2';
						} else {
							$cat_level_point[] = $convertedpoints;
						}
					}
				}
			}
		}
		if ( ! empty( $cat_level_point ) ) {
			return max( $cat_level_point );
		} elseif ( ! empty( $cat_level_enabled ) ) {
			return '2';
		}
	}

	if ( empty( $cat_level_point ) || empty( $cat_level_enabled ) ) {
		return is_global_level( $productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor, $payment_price, $itemquantity );
	}
}

function is_global_level( $productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor, $payment_price, $itemquantity ) {
	// Global Level
	if ( '' != $referred_user ) {
		$global_enable        = get_option( 'rs_global_enable_disable_sumo_referral_reward' );
		$global_reward_type   = get_option( 'rs_global_referral_reward_type' );
		$global_rewardpoints  = get_option( 'rs_global_referral_reward_point' );
		$global_rewardpercent = get_option( 'rs_global_referral_reward_percent' );
		if ( 'yes' == $getting_referrer ) {
			$global_enable        = get_option( 'rs_global_enable_disable_sumo_referral_reward' );
			$global_reward_type   = get_option( 'rs_global_referral_reward_type_refer' );
			$global_rewardpoints  = get_option( 'rs_global_referral_reward_point_get_refer' );
			$global_rewardpercent = get_option( 'rs_global_referral_reward_percent_get_refer' );
		}
		$regularprice    = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
		$convertedpoints = convert_percent_value_as_points( $global_rewardpercent, $regularprice );
		if ( ( 'yes' == get_option( 'rs_restrict_referral_reward' ) ) ) {
			$convertedpoints = $convertedpoints / $itemquantity;
			$itemquantity    = 1;
		}
	} elseif ( 'yes' == $getting_referrer ) {
		$global_enable        = get_option( 'rs_global_enable_disable_sumo_referral_reward' );
		$global_reward_type   = get_option( 'rs_global_referral_reward_type_refer' );
		$global_rewardpoints  = get_option( 'rs_global_referral_reward_point_get_refer' );
		$global_rewardpercent = get_option( 'rs_global_referral_reward_percent_get_refer' );
		$regularprice         = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
		$convertedpoints      = convert_percent_value_as_points( $global_rewardpercent, $regularprice );
	} elseif ( 'yes' == $socialreward ) {
		$newarray             = get_social_rewardpoints( $productid, $rewardfor, '3' );
		$global_enable        = $newarray['enable_level'];
		$global_reward_type   = $newarray['rewardtype'];
		$global_rewardpoints  = $newarray['rewardpoints'];
		$global_rewardpercent = $newarray['rewardpercent'];
		$regularprice         = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
		$convertedpoints      = convert_percent_value_as_points( $global_rewardpercent, $regularprice );
	} else {
		$global_enable        = get_option( 'rs_global_enable_disable_sumo_reward', '1' );
		$global_reward_type   = get_option( 'rs_global_reward_type', '2' );
		$global_rewardpoints  = get_option( 'rs_global_reward_points' );
		$global_rewardpercent = get_option( 'rs_global_reward_percent', 100 );
		$regularprice         = get_regular_price( $productid, $variationid, $item, $itemquantity, $payment_price );
		$convertedpoints      = convert_percent_value_as_points( $global_rewardpercent, $regularprice );
		if ( 'yes' == get_option( 'rs_restrict_reward' ) ) {
			$convertedpoints = $convertedpoints / $itemquantity;
			$itemquantity    = 1;
		}
	}

	if ( '1' == $global_enable ) {
		if ( '1' == $global_reward_type ) {
			if ( '' != $global_rewardpoints ) {
				if ( 'yes' == $checklevel ) {
					return '3';
				} else {
					$quantity = 'yes' == get_option( 'rs_restrict_reward' ) ? 1 : $itemquantity;
					return $global_rewardpoints * $quantity;
				}
			}
		} elseif ( '' != $global_rewardpercent ) {
				return ( 'yes' == $checklevel ) ? '3' : $convertedpoints;
		}
	}
	return 0;
}

function convert_percent_value_as_points( $rewardpercent, $regularprice ) {
	$Points = ( (float) $rewardpercent / 100 ) * $regularprice;
	return earn_point_conversion( $Points );
}

function get_social_rewardpoints( $productid, $rewardfor, $level, $termid = '' ) {
	$productlevel  = get_post_meta( $productid, '_socialrewardsystemcheckboxvalue', true );
	$categorylevel = srp_term_meta( $termid, 'enable_social_reward_system_category' );
	$global_enable = get_option( 'rs_global_social_enable_disable_reward' );
	if ( 'instagram' == $rewardfor ) {
		if ( '1' == $level ) {
			$productlevelrewardtype    = get_post_meta( $productid, '_social_rewardsystem_options_instagram', true );
			$productlevelrewardpoints  = get_post_meta( $productid, '_socialrewardsystempoints_instagram', true );
			$productlevelrewardpercent = get_post_meta( $productid, '_socialrewardsystempercent_instagram', true );
			return array(
				'enable_level'  => $productlevel,
				'rewardtype'    => $productlevelrewardtype,
				'rewardpoints'  => $productlevelrewardpoints,
				'rewardpercent' => $productlevelrewardpercent,
			);
		} elseif ( '2' == $level ) {
			$categorylevelrewardtype     = srp_term_meta( $termid, 'social_instagram_enable_rs_rule' );
			$categorylevelrewardpoints   = srp_term_meta( $termid, 'social_instagram_rs_category_points' );
			$categorylevelrewardpercents = srp_term_meta( $termid, 'social_instagram_rs_category_percent' );
			return array(
				'enable_level'  => $categorylevel,
				'rewardtype'    => $categorylevelrewardtype,
				'rewardpoints'  => $categorylevelrewardpoints,
				'rewardpercent' => $categorylevelrewardpercents,
			);
		} else {
			$global_reward_type    = get_option( 'rs_global_social_reward_type_instagram' );
			$global_reward_points  = get_option( 'rs_global_social_instagram_reward_points' );
			$global_reward_percent = get_option( 'rs_global_social_instagram_reward_percent' );
			return array(
				'enable_level'  => $global_enable,
				'rewardtype'    => $global_reward_type,
				'rewardpoints'  => $global_reward_points,
				'rewardpercent' => $global_reward_percent,
			);
		}
	} elseif ( 'twitter_follow' == $rewardfor ) {
		if ( '1' == $level ) {
			$gettype    = get_post_meta( $productid, '_social_rewardsystem_options_twitter_follow', true );
			$getpoints  = get_post_meta( $productid, '_socialrewardsystempoints_twitter_follow', true );
			$getpercent = get_post_meta( $productid, '_socialrewardsystempercent_twitter_follow', true );
			return array(
				'enable_level'  => $productlevel,
				'rewardtype'    => $gettype,
				'rewardpoints'  => $getpoints,
				'rewardpercent' => $getpercent,
			);
		} elseif ( '2' == $level ) {
			$categorylevelrewardtype     = srp_term_meta( $termid, 'social_twitter_follow_enable_rs_rule' );
			$categorylevelrewardpoints   = srp_term_meta( $termid, 'social_twitter_follow_rs_category_points' );
			$categorylevelrewardpercents = srp_term_meta( $termid, 'social_twitter_follow_rs_category_percent' );
			return array(
				'enable_level'  => $categorylevel,
				'rewardtype'    => $categorylevelrewardtype,
				'rewardpoints'  => $categorylevelrewardpoints,
				'rewardpercent' => $categorylevelrewardpercents,
			);
		} else {
			$global_reward_type    = get_option( 'rs_global_social_reward_type_twitter_follow' );
			$global_reward_points  = get_option( 'rs_global_social_twitter_follow_reward_points' );
			$global_reward_percent = get_option( 'rs_global_social_twitter_follow_reward_percent' );
			return array(
				'enable_level'  => $global_enable,
				'rewardtype'    => $global_reward_type,
				'rewardpoints'  => $global_reward_points,
				'rewardpercent' => $global_reward_percent,
			);
		}
	} elseif ( 'fb_like' == $rewardfor ) {
		if ( '1' == $level ) {
			$gettype    = get_post_meta( $productid, '_social_rewardsystem_options_facebook', true );
			$getpoints  = get_post_meta( $productid, '_socialrewardsystempoints_facebook', true );
			$getpercent = get_post_meta( $productid, '_socialrewardsystempercent_facebook', true );
			return array(
				'enable_level'  => $productlevel,
				'rewardtype'    => $gettype,
				'rewardpoints'  => $getpoints,
				'rewardpercent' => $getpercent,
			);
		} elseif ( '2' == $level ) {
			$categorylevelrewardtype     = srp_term_meta( $termid, 'social_facebook_enable_rs_rule' );
			$categorylevelrewardpoints   = srp_term_meta( $termid, 'social_facebook_rs_category_points' );
			$categorylevelrewardpercents = srp_term_meta( $termid, 'social_facebook_rs_category_percent' );
			return array(
				'enable_level'  => $categorylevel,
				'rewardtype'    => $categorylevelrewardtype,
				'rewardpoints'  => $categorylevelrewardpoints,
				'rewardpercent' => $categorylevelrewardpercents,
			);
		} else {
			$global_reward_type    = get_option( 'rs_global_social_reward_type_facebook' );
			$global_reward_points  = get_option( 'rs_global_social_facebook_reward_points' );
			$global_reward_percent = get_option( 'rs_global_social_facebook_reward_percent' );
			return array(
				'enable_level'  => $global_enable,
				'rewardtype'    => $global_reward_type,
				'rewardpoints'  => $global_reward_points,
				'rewardpercent' => $global_reward_percent,
			);
		}
	} elseif ( 'fb_share' == $rewardfor ) {
		if ( '1' == $level ) {
			$gettype    = get_post_meta( $productid, '_social_rewardsystem_options_facebook_share', true );
			$getpoints  = get_post_meta( $productid, '_socialrewardsystempoints_facebook_share', true );
			$getpercent = get_post_meta( $productid, '_socialrewardsystempercent_facebook_share', true );
			return array(
				'enable_level'  => $productlevel,
				'rewardtype'    => $gettype,
				'rewardpoints'  => $getpoints,
				'rewardpercent' => $getpercent,
			);
		} elseif ( '2' == $level ) {
			$categorylevelrewardtype     = srp_term_meta( $termid, 'social_facebook_share_enable_rs_rule' );
			$categorylevelrewardpoints   = srp_term_meta( $termid, 'social_facebook_share_rs_category_points' );
			$categorylevelrewardpercents = srp_term_meta( $termid, 'social_facebook_share_rs_category_percent' );
			return array(
				'enable_level'  => $categorylevel,
				'rewardtype'    => $categorylevelrewardtype,
				'rewardpoints'  => $categorylevelrewardpoints,
				'rewardpercent' => $categorylevelrewardpercents,
			);
		} else {
			$global_reward_type    = get_option( 'rs_global_social_reward_type_facebook_share' );
			$global_reward_points  = get_option( 'rs_global_social_facebook_share_reward_points' );
			$global_reward_percent = get_option( 'rs_global_social_facebook_share_reward_percent' );
			return array(
				'enable_level'  => $global_enable,
				'rewardtype'    => $global_reward_type,
				'rewardpoints'  => $global_reward_points,
				'rewardpercent' => $global_reward_percent,
			);
		}
	} elseif ( 'twitter_tweet' == $rewardfor ) {
		if ( '1' == $level ) {
			$gettype    = get_post_meta( $productid, '_social_rewardsystem_options_twitter', true );
			$getpoints  = get_post_meta( $productid, '_socialrewardsystempoints_twitter', true );
			$getpercent = get_post_meta( $productid, '_socialrewardsystempercent_twitter', true );
			return array(
				'enable_level'  => $productlevel,
				'rewardtype'    => $gettype,
				'rewardpoints'  => $getpoints,
				'rewardpercent' => $getpercent,
			);
		} elseif ( '2' == $level ) {
			$categorylevelrewardtype     = srp_term_meta( $termid, 'social_twitter_enable_rs_rule' );
			$categorylevelrewardpoints   = srp_term_meta( $termid, 'social_twitter_rs_category_points' );
			$categorylevelrewardpercents = srp_term_meta( $termid, 'social_twitter_rs_category_percent' );
			return array(
				'enable_level'  => $categorylevel,
				'rewardtype'    => $categorylevelrewardtype,
				'rewardpoints'  => $categorylevelrewardpoints,
				'rewardpercent' => $categorylevelrewardpercents,
			);
		} else {
			$global_reward_type    = get_option( 'rs_global_social_reward_type_twitter' );
			$global_reward_points  = get_option( 'rs_global_social_twitter_reward_points' );
			$global_reward_percent = get_option( 'rs_global_social_twitter_reward_percent' );
			return array(
				'enable_level'  => $global_enable,
				'rewardtype'    => $global_reward_type,
				'rewardpoints'  => $global_reward_points,
				'rewardpercent' => $global_reward_percent,
			);
		}
	} elseif ( 'g_plus' == $rewardfor ) {
		if ( '1' == $level ) {
			$gettype    = get_post_meta( $productid, '_social_rewardsystem_options_google', true );
			$getpoints  = get_post_meta( $productid, '_socialrewardsystempoints_google', true );
			$getpercent = get_post_meta( $productid, '_socialrewardsystempercent_google', true );
			return array(
				'enable_level'  => $productlevel,
				'rewardtype'    => $gettype,
				'rewardpoints'  => $getpoints,
				'rewardpercent' => $getpercent,
			);
		} elseif ( '2' == $level ) {
			$categorylevelrewardtype     = srp_term_meta( $termid, 'social_google_enable_rs_rule' );
			$categorylevelrewardpoints   = srp_term_meta( $termid, 'social_google_rs_category_points' );
			$categorylevelrewardpercents = srp_term_meta( $termid, 'social_google_rs_category_percent' );
			return array(
				'enable_level'  => $categorylevel,
				'rewardtype'    => $categorylevelrewardtype,
				'rewardpoints'  => $categorylevelrewardpoints,
				'rewardpercent' => $categorylevelrewardpercents,
			);
		} else {
			$global_reward_type    = get_option( 'rs_global_social_reward_type_google' );
			$global_reward_points  = get_option( 'rs_global_social_google_reward_points' );
			$global_reward_percent = get_option( 'rs_global_social_google_reward_percent' );
			return array(
				'enable_level'  => $global_enable,
				'rewardtype'    => $global_reward_type,
				'rewardpoints'  => $global_reward_points,
				'rewardpercent' => $global_reward_percent,
			);
		}
	} elseif ( 'vk_like' == $rewardfor ) {
		if ( '1' == $level ) {
			$gettype    = get_post_meta( $productid, '_social_rewardsystem_options_vk', true );
			$getpoints  = get_post_meta( $productid, '_socialrewardsystempoints_vk', true );
			$getpercent = get_post_meta( $productid, '_socialrewardsystempercent_vk', true );
			return array(
				'enable_level'  => $productlevel,
				'rewardtype'    => $gettype,
				'rewardpoints'  => $getpoints,
				'rewardpercent' => $getpercent,
			);
		} elseif ( '2' == $level ) {
			$categorylevelrewardtype     = srp_term_meta( $termid, 'social_vk_enable_rs_rule' );
			$categorylevelrewardpoints   = srp_term_meta( $termid, 'social_vk_rs_category_points' );
			$categorylevelrewardpercents = srp_term_meta( $termid, 'social_vk_rs_category_percent' );
			return array(
				'enable_level'  => $categorylevel,
				'rewardtype'    => $categorylevelrewardtype,
				'rewardpoints'  => $categorylevelrewardpoints,
				'rewardpercent' => $categorylevelrewardpercents,
			);
		} else {
			$global_reward_type    = get_option( 'rs_global_social_reward_type_vk' );
			$global_reward_points  = get_option( 'rs_global_social_vk_reward_points' );
			$global_reward_percent = get_option( 'rs_global_social_vk_reward_percent' );
			return array(
				'enable_level'  => $global_enable,
				'rewardtype'    => $global_reward_type,
				'rewardpoints'  => $global_reward_points,
				'rewardpercent' => $global_reward_percent,
			);
		}
	} elseif ( 'ok_follow' == $rewardfor ) {
		if ( '1' == $level ) {
			$gettype    = get_post_meta( $productid, '_social_rewardsystem_options_ok_follow', true );
			$getpoints  = get_post_meta( $productid, '_socialrewardsystempoints_ok_follow', true );
			$getpercent = get_post_meta( $productid, '_socialrewardsystempercent_ok_follow', true );
			return array(
				'enable_level'  => $productlevel,
				'rewardtype'    => $gettype,
				'rewardpoints'  => $getpoints,
				'rewardpercent' => $getpercent,
			);
		} elseif ( '2' == $level ) {
			$categorylevelrewardtype     = srp_term_meta( $termid, 'social_ok_follow_enable_rs_rule' );
			$categorylevelrewardpoints   = srp_term_meta( $termid, 'social_ok_follow_rs_category_points' );
			$categorylevelrewardpercents = srp_term_meta( $termid, 'social_ok_follow_rs_category_percent' );
			return array(
				'enable_level'  => $categorylevel,
				'rewardtype'    => $categorylevelrewardtype,
				'rewardpoints'  => $categorylevelrewardpoints,
				'rewardpercent' => $categorylevelrewardpercents,
			);
		} else {
			$global_reward_type    = get_option( 'rs_global_social_reward_type_ok_follow' );
			$global_reward_points  = get_option( 'rs_global_social_ok_follow_reward_points' );
			$global_reward_percent = get_option( 'rs_global_social_ok_follow_reward_percent' );
			return array(
				'enable_level'  => $global_enable,
				'rewardtype'    => $global_reward_type,
				'rewardpoints'  => $global_reward_points,
				'rewardpercent' => $global_reward_percent,
			);
		}
	}
}

if ( ! function_exists( 'srp_formatted_price' ) ) {

	function srp_formatted_price( $price ) {
		return function_exists( 'wc_price' ) ? wc_price( $price ) : woocommerce_price( $price );
	}
}

if ( ! function_exists( 'srp_order_obj' ) ) {

	function srp_order_obj( $order ) {
		if ( is_object( $order ) && ! empty( $order ) ) {
			global $woocommerce;
			if ( (float) $woocommerce->version >= (float) '3.0' ) {
				$order_id      = $order->get_id();
				$post_status   = $order->get_status();
				$order_user_id = $order->get_user_id();
				if ( 0 == $order->get_parent_id() ) {
					$payment_method       = $order->get_payment_method();
					$payment_method_title = $order->get_payment_method_title();
				} else {
					$payment_method       = '';
					$payment_method_title = '';
				}
			} else {
				$order_id      = $order->id;
				$post_status   = $order->post_status;
				$order_user_id = $order->user_id;
				if ( 0 == $order->parent_id ) {
					$payment_method       = $order->payment_method;
					$payment_method_title = $order->payment_method_title;
				} else {
					$payment_method       = '';
					$payment_method_title = '';
				}
			}
			$first_name = $order->get_billing_first_name();
			$new_array  = array(
				'order_id'             => $order_id,
				'order_status'         => $post_status,
				'order_userid'         => $order_user_id,
				'payment_method'       => $payment_method,
				'payment_method_title' => $payment_method_title,
				'first_name'           => $first_name,
			);
			return $new_array;
		}
	}
}

if ( ! function_exists( 'srp_coupon_obj' ) ) {

	function srp_coupon_obj( $object ) {
		if ( is_object( $object ) && ! empty( $object ) ) {
			global $woocommerce;
			if ( (float) $woocommerce->version >= (float) '3.0' ) {
				$coupon_id          = $object->get_id();
				$coupon_code        = $object->get_code();
				$coupon_amnt        = $object->get_amount();
				$coupon_product_ids = $object->get_product_ids();
				$discount_type      = $object->get_discount_type();
				$product_cat        = $object->get_product_categories();
			} else {
				$coupon_id          = $object->id;
				$coupon_code        = $object->code;
				$coupon_amnt        = $object->coupon_amount;
				$coupon_product_ids = $object->product_ids;
				$discount_type      = $object->discount_type;
				$product_cat        = $object->product_categories;
			}
			$new_array = array(
				'coupon_id'          => $coupon_id,
				'coupon_code'        => $coupon_code,
				'coupon_amount'      => $coupon_amnt,
				'product_ids'        => $coupon_product_ids,
				'discount_type'      => $discount_type,
				'product_categories' => $product_cat,
			);
			return $new_array;
		}
	}
}

function check_whether_hoicker_is_active() {
	if ( class_exists( 'HR_Wallet' ) ) {
		return true;
	}

	return false;
}

function is_sumo_booking_active( $pdt_id ) {
	if ( class_exists( 'SUMO_Bookings' ) ) {
		if ( function_exists( 'is_sumo_bookings_product' ) && ( is_sumo_bookings_product( $pdt_id ) ) ) {
			return true;
		}
	}

	return false;
}

add_filter( 'sumo_bookings_calculated_format_price', 'point_price_format_for_booking_product', 10, 3 );

function point_price_format_for_booking_product( $format_price, $booking_price, $product_id ) {

	if ( 'yes' != get_option( 'rs_point_price_activated' ) ) {
		return $format_price;
	}

	if ( ! is_sumo_booking_active( $product_id ) ) {
		return $format_price;
	}

	if ( 'yes' == get_post_meta( $product_id, '_rewardsystem_enable_point_price', true ) && 'yes' == get_option( 'rs_enable_product_category_level_for_points_price' ) ) {
		$point_price_label = get_option( 'rs_label_for_point_value' );
		$price             = calculate_point_price_for_products( $product_id );
		if ( 2 == get_post_meta( $product_id, '_rewardsystem_enable_point_price_type', true ) ) {
			return $price[ $product_id ] . $point_price_label;
		} else {
			$PointPrice = display_point_price_value( $price[ $product_id ] );
			return $format_price . $PointPrice;
		}
	}
	return $format_price;
}

function rs_alter_from_email_of_woocommerce( $email, $obj ) {
	if ( FPRewardSystem::$rs_from_email_address ) {
		return '<' . FPRewardSystem::$rs_from_email_address . '>';
	}

	return $email;
}

function rs_alter_from_name_of_woocommerce( $name, $obj ) {
	if ( FPRewardSystem::$rs_from_name ) {
		return FPRewardSystem::$rs_from_name;
	}

	return $name;
}

function award_points_for_product_purchase_based_on_cron( $order_id ) {
	$order         = new WC_Order( $order_id );
	$orderid       = srp_order_obj( $order );
	$orderstatus   = $orderid['order_status'];
	$replacestatus = str_replace( 'wc-', '', $orderstatus );
	$status        = get_option( 'rs_order_status_control', array( 'processing', 'completed' ) );
	if ( in_array( $replacestatus, $status ) ) {
		$new_obj = new RewardPointsOrder( $order_id, $apply_previous_order_points = 'no' );
		$new_obj->update_earning_points_for_user();
	}
}

if ( ! function_exists( 'rs_redeemed_point_in_thank_you_page' ) ) {

	function rs_redeemed_point_in_thank_you_page( $total_rows, $order, $tax_display ) {
		$OrderObj           = srp_order_obj( $order );
		$OrderId            = $OrderObj['order_id'];
		$UserID             = $OrderObj['order_userid'];
		$UserData           = get_user_by( 'id', $UserID );
		$UserName           = is_object( $UserData ) ? $UserData->user_login : 'Guest';
		$SumoCouponName     = 'sumo_' . strtolower( $UserName );
		$AutoSumoCouponName = 'auto_redeem_' . strtolower( $UserName );
		$CouponsUsedInOrder = $order->get_items( array( 'coupon' ) );
		if ( ! srp_check_is_array( $CouponsUsedInOrder ) ) {
			return $total_rows;
		}

		$CouponData = array();
		foreach ( $CouponsUsedInOrder as $item ) {
			$CouponData[ $item->get_code() ] = ( 'incl' == $tax_display ) ? ( $item->get_discount() + $item->get_discount_tax() ) : $item->get_discount();
		}

		if ( ! srp_check_is_array( $CouponData ) ) {
			return $total_rows;
		}

		if ( ! array_key_exists( $SumoCouponName, $CouponData ) && ! array_key_exists( $AutoSumoCouponName, $CouponData ) ) {
			return $total_rows;
		}

		unset( $total_rows['discount'] );
		$RedeemedPoints    = isset( $CouponData[ $SumoCouponName ] ) ? $CouponData[ $SumoCouponName ] : $CouponData[ $AutoSumoCouponName ];
		$OtherCouponValue  = array_sum( $CouponData ) - $RedeemedPoints;
		$ArrayKeys         = array_keys( $total_rows );
		$IndexofArray      = array_search( 'payment_method', $ArrayKeys );
		$PositionOfanIndex = $IndexofArray ? $IndexofArray + 1 : count( $total_rows );

		if ( $RedeemedPoints > 0 ) {
			$total_rows = array_slice( $total_rows, 0, $PositionOfanIndex, true ) +
					array(
						'redeeming' => array(
							'label' => get_option( 'rs_coupon_label_message' ),
							'value' => __( '-' . srp_formatted_price( $RedeemedPoints ), 'rewardsystem' ),
						),
					) + array_slice( $total_rows, $PositionOfanIndex, count( $total_rows ) - 1, true );
		}
		if ( $OtherCouponValue > 0 ) {
			$total_rows = array_slice( $total_rows, 0, $PositionOfanIndex, true ) +
					array(
						'othercoupon' => array(
							'label' => __( 'Discount Value:', 'rewardsystem' ),
							'value' => __( '-' . srp_formatted_price( $OtherCouponValue ), 'rewardsystem' ),
						),
					) + array_slice( $total_rows, $PositionOfanIndex, count( $total_rows ) - 1, true );
		}
		return $total_rows;
	}

	add_filter( 'woocommerce_get_order_item_totals', 'rs_redeemed_point_in_thank_you_page', 8, 3 );
}

if ( ! function_exists( 'srp_term_meta' ) ) {

	function srp_term_meta( $Id, $MetaKey ) {
		return function_exists( 'get_term_meta' ) ? get_term_meta( $Id, $MetaKey, true ) : get_woocommerce_term_meta( $Id, $MetaKey, true );
	}
}

if ( ! function_exists( 'srp_update_term_meta' ) ) {

	function srp_update_term_meta( $Id, $MetaKey, $Value ) {
		return function_exists( 'update_term_meta' ) ? update_term_meta( $Id, $MetaKey, $Value ) : update_woocommerce_term_meta( $Id, $MetaKey, $Value );
	}
}

if ( ! function_exists( 'rs_get_first_purchase_point' ) ) {

	function rs_get_first_purchase_point( $order = false ) {

		if ( 'yes' != get_option( 'rs_enable_first_purchase_reward_points' ) ) {
			return 0;
		}

		$points = 0;
		if ( ! rs_validate_first_purchase_point( $order ) ) {
			return $points;
		}

		if ( is_object( $order ) ) {
			$points = $order->get_meta( 'rs_first_purchase_point_for_order' );
		} elseif ( '1' == get_option( 'rs_global_reward_points_type', 1 ) ) {
			// Fixed Amount.
			$points = (float) get_option( 'rs_reward_points_for_first_purchase_in_fixed', '0' );
		} elseif ( '2' == get_option( 'rs_global_reward_points_type' ) ) {
			$subtotal_tax = ( is_cart() || is_checkout() ) && wc_tax_enabled() && wc_prices_include_tax() && 'incl' == get_option( 'woocommerce_tax_display_cart' ) ? WC()->cart->get_subtotal_tax() : 0;
			$total        = 'yes' == get_option( 'rs_display_earn_point_tax_based' ) ? WC()->cart->get_subtotal() - $subtotal_tax : WC()->cart->get_subtotal();
			$points       = (float) ( $total * (float) get_option( 'rs_reward_points_for_first_purchase_in_sub_total', '0' ) ) / 100;
		} else {
			$total  = 'yes' === get_option( 'rs_exclude_shipping_cost_based_on_cart_total' ) ? WC()->cart->total - ( WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax() ) : WC()->cart->total;
			$points = (float) ( $total * (float) get_option( 'rs_reward_points_for_first_purchase_in_cart_total', '0' ) ) / 100;
		}

		return $points;
	}
}

if ( ! function_exists( 'rs_validate_first_purchase_point' ) ) {

	/**
	 * Validate first purchase point.
	 *
	 * @return bool.
	 * */
	function rs_validate_first_purchase_point( $order = false ) {

		$total = is_object( $order ) ? $order->get_total() : WC()->cart->total;
		// Return if minimum order total is not matched.
		if ( $total < get_option( 'rs_min_total_for_first_purchase', '0' ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'earn_level_name' ) ) {

	function earn_level_name( $UserId ) {
		if ( 'yes' != get_option( 'rs_enable_earned_level_based_reward_points' ) ) {
			return;
		}

		$Pointsdata = new RS_Points_Data( $UserId );
		$Points     = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $Pointsdata->total_earned_points() : $Pointsdata->total_available_points();
		$RuleId     = rs_get_earning_and_redeeming_level_id( $Points, 'earning' );
		$Rules      = get_option( 'rewards_dynamic_rule' );
		$LevelName  = isset( $Rules[ $RuleId ]['name'] ) ? $Rules[ $RuleId ]['name'] : '';
		return $LevelName;
	}
}

if ( ! function_exists( 'rs_get_earning_and_redeeming_level_id' ) ) {

	function rs_get_earning_and_redeeming_level_id( $Points, $Type ) {

		$RuleValue = ( 'earning' == $Type ) ? get_option( 'rewards_dynamic_rule' ) : get_option( 'rewards_dynamic_rule_for_redeem' );
		$Rules     = multi_dimensional_sort( $RuleValue, 'rewardpoints' );
		if ( ! srp_check_is_array( $Rules ) ) {
			return 0;
		}

		$NewArr = array();
		foreach ( $Rules as $key => $Rule ) {
			if ( '2' == get_option( 'rs_free_product_range' ) ) {
				if ( $Rule['rewardpoints'] <= $Points ) {
					$NewArr[ $Rule['rewardpoints'] ] = $key;
				}
			} elseif ( $Rule['rewardpoints'] >= $Points ) {
					$NewArr[ $Rule['rewardpoints'] ] = $key;
			}
		}

		if ( ! srp_check_is_array( $NewArr ) ) {
			return 0;
		}

		if ( '2' == get_option( 'rs_free_product_range' ) ) {
			$MaxValue = max( array_keys( $NewArr ) );
			return $NewArr[ $MaxValue ];
		} else {
			$MinValue = min( array_keys( $NewArr ) );
			return $NewArr[ $MinValue ];
		}
	}
}

if ( ! function_exists( 'points_to_reach_next_earn_level' ) ) {

	function points_to_reach_next_earn_level( $UserId, $tag = '' ) {
		if ( 'yes' != get_option( 'rs_enable_earned_level_based_reward_points' ) ) {
			return;
		}

		$Rules = get_option( 'rewards_dynamic_rule' );

		if ( ! srp_check_is_array( $Rules ) ) {
			return;
		}

		$Pointsdata = new RS_Points_Data( $UserId );
		$Points     = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $Pointsdata->total_earned_points() : $Pointsdata->total_available_points();

		$RuleId = rs_get_earning_and_redeeming_level_id( $Points, 'earning' );
		if ( '1' == get_option( 'rs_free_product_range' ) ) {
			$LevelName = isset( $Rules[ $RuleId ]['name'] ) ? $Rules[ $RuleId ]['name'] : '';
			if ( ! isset( $Rules[ $RuleId ]['rewardpoints'] ) ) {
				return;
			}

			$NextLevelPoints = (float) $Rules[ $RuleId ]['rewardpoints'] - $Points;
			$NextLevelPoints = ( 0 == $NextLevelPoints ) ? 1 : ( $NextLevelPoints + 1 );

			$rule_keys = array();
			foreach ( $Rules as $key => $rule ) {
				if ( $rule['rewardpoints'] > ( $NextLevelPoints + $Points ) ) {
					$rule_keys[] = $key;
				}
			}

			if ( empty( $rule_keys ) ) {
				return;
			}
			$next_rule_id = min( $rule_keys );
			$LevelName    = isset( $Rules[ $next_rule_id ]['name'] ) ? $Rules[ $next_rule_id ]['name'] : '';
		} else {
			$max_rewards = array();
			foreach ( $Rules as $Rule ) {

				if ( ! $RuleId ) {
					$max_rewards[ $Rule['rewardpoints'] ] = $Rule['name'];
				} elseif ( ( $Rule['rewardpoints'] > $Rules[ $RuleId ]['rewardpoints'] ) ) {
					$max_rewards[ $Rule['rewardpoints'] ] = $Rule['name'];
				}
			}

			if ( ! srp_check_is_array( $max_rewards ) ) {
				return;
			}

			$RewardPoints    = min( array_keys( $max_rewards ) );
			$NextLevelPoints = (float) $RewardPoints - $Points;
			$LevelName       = $max_rewards[ $RewardPoints ];
		}

		if ( '' !== $tag ) {
			switch ( $tag ) {
				case 'balancepoint':
					return $NextLevelPoints;
					break;
				case 'next_level_name':
					return $LevelName;
					break;
			}
		}

		$Msg = str_replace( '[balancepoint]', $NextLevelPoints, str_replace( '[next_level_name]', $LevelName, get_option( 'rs_point_to_reach_next_level' ) ) );
		return $Msg;
	}
}

if ( ! function_exists( 'rs_get_referrer_email_info_in_order' ) ) {

	function rs_get_referrer_email_info_in_order( $order_id, $message ) {
		$order       = wc_get_order( $order_id );
		$referred_id = $order->get_meta( '_referrer_name' );
		if ( ! $referred_id ) {
			return $message;
		}

		$UserInfo = get_user_by( 'id', $referred_id );

		$UserName  = is_object( $UserInfo ) ? $UserInfo->user_login : 'Guest';
		$FirstName = is_object( $UserInfo ) ? $UserInfo->first_name : 'Guest';
		$LastName  = is_object( $UserInfo ) ? $UserInfo->last_name : 'Guest';
		$Email     = is_object( $UserInfo ) ? $UserInfo->user_email : 'Guest';

		$message = str_replace( array( '[rs_referrer_name]', '[rs_referrer_first_name]', '[rs_referrer_last_name]', '[rs_referrer_email_id]' ), array( $UserName, $FirstName, $LastName, $Email ), $message );
		return $message;
	}
}

if ( ! function_exists( 'get_payment_gateway_title' ) ) {

	function get_payment_gateway_title( $gateway_id ) {
		if ( 'reward_gateway' != $gateway_id ) {
			return '';
		}

		$wc_gateways        = WC()->payment_gateways();
		$available_gateways = $wc_gateways->get_available_payment_gateways();
		$available_gateways = isset( $available_gateways[ $gateway_id ] ) ? $available_gateways[ $gateway_id ] : '';
		$gateway_title      = is_object( $available_gateways ) ? $available_gateways->get_title() : '';
		return $gateway_title;
	}
}

if ( ! function_exists( 'rs_get_endpoint_url' ) ) {

	/**
	 * Get endpoint URL .
	 */
	function rs_get_endpoint_url( $query_args, $page = false, $permalink = '' ) {

		if ( ! $permalink ) {
			$permalink = get_permalink();
		}

		$url = trailingslashit( $permalink );

		if ( $page ) {
			$query_args = array_merge( $query_args, array( 'page_no' => $page ) );
		}

		return add_query_arg( $query_args, $url );
	}
}

if ( ! function_exists( 'rs_exclude_particular_users' ) ) {

	function rs_exclude_particular_users( $field_id ) {

		$unsubscribe_user_ids = array();

		switch ( $field_id ) {

			case 'rs_select_user_to_unsubscribe':
				$args                 = array(
					'fields'       => 'ids',
					'meta_key'     => 'unsub_value',
					'meta_value'   => 'yes',
					'meta_compare' => '=',
				);
				$unsubscribe_user_ids = get_users( $args );
				break;
		}

		return $unsubscribe_user_ids;
	}
}

if ( ! function_exists( 'rs_restrict_product_purchase_point_when_free_shipping_is_enabled' ) ) {

	function rs_restrict_product_purchase_point_when_free_shipping_is_enabled( $order_id = false ) {

		if ( 'yes' != get_option( 'rs_disable_point_if_free_shipping_is_enabled' ) ) {
			return true;
		}

		if ( $order_id ) {

			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return true;
			}

			$shipping_items = $order->get_items( 'shipping' );
			if ( srp_check_is_array( $shipping_items ) ) {
				$shipping_methods = array();
				foreach ( $shipping_items as $shipping ) {
					if ( ! is_object( $shipping ) ) {
						continue;
					}
					$shipping_methods[] = $shipping->get_method_id();
				}

				if ( in_array( 'free_shipping', $shipping_methods ) ) {
					return false;
				}
			}
		} else {

			$cart_shipping = WC()->cart->calculate_shipping();

			if ( srp_check_is_array( $cart_shipping ) ) {
				$shipping_methods = array();
				foreach ( $cart_shipping as $shipping ) {
					$shipping_methods[] = $shipping->method_id;
				}

				if ( in_array( 'free_shipping', $shipping_methods ) ) {
					return false;
				}
			}
		}

		return true;
	}
}

if ( ! function_exists( 'rs_restrict_referral_system_purchase_point_for_free_shipping' ) ) {

	/**
	 * Restrict referral product purchase point when free shipping is enabled.
	 *
	 * @return bool
	 */
	function rs_restrict_referral_system_purchase_point_for_free_shipping( $order_id = false ) {

		if ( 'yes' != get_option( 'rs_restrict_referral_system_when_free_shipping_is_enabled' ) ) {
			return true;
		}

		if ( $order_id ) {

			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return true;
			}

			$shipping_items = $order->get_items( 'shipping' );
			if ( srp_check_is_array( $shipping_items ) ) {
				$shipping_methods = array();
				foreach ( $shipping_items as $shipping ) {
					if ( ! is_object( $shipping ) ) {
						continue;
					}
					$shipping_methods[] = $shipping->get_method_id();
				}

				if ( in_array( 'free_shipping', $shipping_methods ) ) {
					return false;
				}
			}
		} else {

			$cart_shipping = WC()->cart->calculate_shipping();

			if ( srp_check_is_array( $cart_shipping ) ) {
				$shipping_methods = array();
				foreach ( $cart_shipping as $shipping ) {
					if ( ! is_object( $shipping ) ) {
						continue;
					}

					$shipping_methods[] = $shipping->method_id;
				}

				if ( in_array( 'free_shipping', $shipping_methods ) ) {
					return false;
				}
			}
		}

		return true;
	}
}

if ( ! function_exists( 'rs_validate_referral_system_restrictions' ) ) {

	/*
	 * Validate referral system restrictions.
	 *
	 *
	 * @return bool
	 */

	function rs_validate_referral_system_restrictions( $order ) {

		if ( ! is_object( $order ) ) {
			return false;
		}

		$order_statuses       = array();
		$general_order_status = get_option( 'rs_order_status_control', array( 'processing', 'completed' ) );
		if ( srp_check_is_array( $general_order_status ) ) {
			foreach ( $general_order_status as $status ) {
				$order_statuses[] = 'wc-' . $status;
			}
		}

		$referrer_name = $order->get_meta( '_referrer_name' );
		$billing_email = ( WC_VERSION <= (float) ( '3.0' ) ) ? $order->billing_email : $order->get_billing_email();
		$order_count   = RSPointExpiry::get_order_count( $billing_email, $order->get_user_id(), $order_statuses, $referrer_name );
		$count_limit   = RSPointExpiry::check_order_count_limit( $order_count, 'no' );
		if ( $count_limit ) {
			return false;
		}

		$check_multiple_referrer = RSPointExpiry::check_if_user_has_multiple_referrer( $billing_email, $order );
		if ( ! $check_multiple_referrer ) {
			return false;
		}

		$check_same_ip = RSPointExpiry::check_if_referrer_and_referral_from_same_ip( $order );
		if ( ! $check_same_ip ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'rs_validate_referrer_id_from_restrictions' ) ) {

	/**
	 * Validate referrer id from restrictions.
	 *
	 * @return bool
	 */
	function rs_validate_referrer_id_from_restrictions( $referrer_id, $order ) {

		if ( ! is_object( $order ) ) {
			return false;
		}

		// User Info who referred the user to place the order
		$referrer = new WP_User( $referrer_id );
		if ( ! is_object( $referrer ) ) {
			return false;
		}

		// User Info who placed the order
		$user_registered_time = '';
		if ( 0 == $order->get_user_id() && 'yes' == get_option( 'rs_referrer_earn_point_purchase_by_guest_users' ) ) {
			$user_registered_time = time();
		} else {
			$user_info            = new WP_User( $order->get_user_id() );
			$user_registered_time = is_object( $user_info ) ? strtotime( $user_info->user_registered ) : '';
		}

		$limitation = false;
		if ( '1' == get_option( '_rs_select_referral_points_referee_time' ) ) {
			// Is for Immediatly.
			$limitation = true;
		} else {
			// Is for Limited Time with Number of Days.
			$user_registered_date = gmdate( 'Y-m-d h:i:sa', $user_registered_time );
			$delay_days           = get_option( '_rs_select_referral_points_referee_time_content' );
			$user_modified_time   = strtotime( gmdate( 'Y-m-d h:i:sa', strtotime( $user_registered_date . ' + ' . $delay_days . ' days ' ) ) );
			$limitation           = ( strtotime( gmdate( 'Y-m-d h:i:sa' ) ) > $user_modified_time ) ? true : false;
		}

		if ( ! $limitation ) {
			return false;
		}

		$check_referrer_already_exists = $user_registered_time ? ( $user_registered_time > strtotime( $referrer->user_registered ) ) : false;
		if ( ! $check_referrer_already_exists ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'rs_get_minimum_quantity_based_on_product_total' ) ) {

	/**
	 * Get Minimum Quantity based on Product Total
	 *
	 * @return int.
	 * */
	function rs_get_minimum_quantity_based_on_product_total( $product_id, $variation_id = 0 ) {
		$quantity = 1;

		if ( 'no' == get_option( 'rs_product_purchase_activated' ) ) {
			return $quantity;
		}

		$term = get_the_terms( $product_id, 'product_cat' );
		if ( srp_check_is_array( $term ) ) {
			foreach ( $term as $terms ) {
				$termid              = $terms->term_id;
				$categorylevelminqty = srp_term_meta( $termid, 'rs_get_min_quantity' );
			}
		}

		if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
			if ( '1' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
				$quantity = get_option( 'rs_minimum_number_of_quantity', '1' );
			}
		} else {
			$product = wc_get_product( $product_id );
			if ( ! is_object( $product ) ) {
				return;
			}
			if ( $product->is_type( 'variable' ) && $variation_id ) {
				$quantity = get_post_meta( $variation_id, 'rs_number_of_qty_for_variable_product', true );
				if ( '' == $quantity && isset( $categorylevelminqty ) && '' !== $categorylevelminqty ) {
					$quantity = $categorylevelminqty;
				} elseif ( '' == $quantity ) {
					$quantity = get_option( 'rs_minimum_number_of_qty', '' );
				}
			} else {
				$quantity = get_post_meta( $product_id, 'rs_number_of_qty_for_simple_product', true );
				if ( '' == $quantity && isset( $categorylevelminqty ) && '' !== $categorylevelminqty ) {
					$quantity = $categorylevelminqty;
				} elseif ( '' == $quantity ) {
					$quantity = get_option( 'rs_minimum_number_of_qty', '' );
				}
			}
		}

		return ! empty( $quantity ) ? absint( $quantity ) : 0;
	}
}

if ( ! function_exists( 'rs_get_product_review_reward_points' ) ) {
	/*
	 * Get Product Review Reward Points
	 *
	 *
	 * @return int
	 */

	function rs_get_product_review_reward_points( $product_id ) {

		$product_review_points = 0;
		if ( 'no' == get_option( 'rs_reward_action_activated' ) || 'no' == get_option( 'rs_enable_product_review_points', 'yes' ) ) {
			return $product_review_points;
		}

		$product = wc_get_product( $product_id );
		if ( ! is_object( $product ) ) {
			return $product_review_points;
		}

		$product_review_points = get_post_meta( $product_id, 'rs_product_review_reward_points_for_product_level', true );

		return ! empty( $product_review_points ) ? $product_review_points : get_option( 'rs_reward_product_review' );
	}
}

if ( ! function_exists( 'rs_create_free_product_order_automatically' ) ) {

	/**
	 * Create free product order automatically.
	 *
	 * @return void
	 */
	function rs_create_free_product_order_automatically( $user_id ) {

		if ( ! $user_id ) {
			return;
		}

		$banning_type = check_banning_type( $user_id );
		if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
			return;
		}

		$rules = get_option( 'rewards_dynamic_rule' );
		if ( ! srp_check_is_array( $rules ) ) {
			return;
		}

		$points_data = new RS_Points_Data( $user_id );
		$points      = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $points_data->total_earned_points() : $points_data->total_available_points();
		if ( ! $points ) {
			return;
		}

		$level_id = rs_get_earning_and_redeeming_level_id( $points, 'earning' );
		if ( ! $level_id ) {
			return;
		}

		$levelname         = isset( $rules[ $level_id ]['name'] ) ? $rules[ $level_id ]['name'] : '';
		$free_product_type = isset( $rules[ $level_id ]['type'] ) ? $rules[ $level_id ]['type'] : 1;
		if ( '2' == $free_product_type ) {
			return;
		}

		$free_product_list = isset( $rules[ $level_id ]['product_list'] ) ? $rules[ $level_id ]['product_list'] : array();
		if ( ! srp_check_is_array( $free_product_list ) ) {
			return;
		}

		foreach ( $free_product_list as $product_id ) {

			if ( ! $product_id ) {
				continue;
			}

			$meta_key = 'userid_' . $user_id . $product_id;
			if ( 'yes' == get_user_meta( $user_id, $meta_key, true ) ) {
				continue;
			}

			$stored_level_ids = ! empty( get_user_meta( $user_id, 'rs_free_product_added_level_id', true ) ) ? get_user_meta( $user_id, 'rs_free_product_added_level_id', true ) : array();
			if ( in_array( $level_id, $stored_level_ids ) ) {
				continue;
			}

			$customer = new WC_Customer( $user_id );
			if ( ! is_object( $customer ) ) {
				continue;
			}

			// Create order.
			$order = wc_create_order(
				array(
					'status'        => 'wc-pending',
					'customer_id'   => $user_id,
					'customer_note' => 'Imported order',
				)
			);
			// Add product to order.
			$order->add_product( srp_product_object( $product_id ), 1 );

			$order_id = $order->get_order_number();
			// Update user id.
			$order->update_meta_data( '_customer_user', $user_id );

			$order->set_address( $order->get_address() );
			$address = array(
				'first_name' => get_user_meta( $user_id, 'shipping_first_name', true ),
				'last_name'  => get_user_meta( $user_id, 'shipping_last_name', true ),
				'company'    => get_user_meta( $user_id, 'shipping_company', true ),
				'address_1'  => get_user_meta( $user_id, 'shipping_address_1', true ),
				'address_2'  => get_user_meta( $user_id, 'shipping_address_2', true ),
				'city'       => get_user_meta( $user_id, 'shipping_city', true ),
				'state'      => get_user_meta( $user_id, 'shipping_state', true ),
				'postcode'   => get_user_meta( $user_id, 'shipping_postcode', true ),
				'country'    => get_user_meta( $user_id, 'shipping_country', true ),
			);

			// Billing email.
			$billing_email = get_user_meta( $user_id, 'billing_email', true );
			// Update billing email.
			$order->update_meta_data( '_billing_email', $billing_email );
			// Set address.
			$order->set_address( $address, 'shipping' );
			$order->set_address( $address );

			// Update free product added level id.
			update_user_meta( $user_id, 'rs_free_product_added_level_id', array( $level_id ) );
			// Set Order status.
			$order->set_status( 'wc-' . get_option( 'rs_order_status_control_to_automatic_order' ) );
			$order->save();

			// Send Email notification for Free Product to user.
			rs_free_product_notification_for_user( $user_id, $order_id );

			// Admin email.
			if ( 'yes' == get_option( 'rs_enable_admin_email_for_free_product' ) ) {
				$subject = get_option( 'rs_subject_for_free_product_mail_send_admin', 'Free Product - Notification' );
				$msg     = get_option( 'rs_content_for_free_product_mail_send_admin', 'Hi,<br/> Your user has got the product as free for reaching the configured level. Please check the below details,<br/> Username: [username]<br/>Product Name: [product_id]<br/>Level Name: [current_level_name].<br/>Thanks' );
				$message = str_replace( array( '[username] , [current_level_name]', '[product_id]' ), array( get_option( 'woocommerce_email_from_name' ), $levelname, implode( ',', $free_product_list ) ), $msg );

				send_mail( get_option( 'woocommerce_email_from_address' ), $subject, $message );
			}

			update_user_meta( $user_id, $meta_key, 'yes' );
		}
	}
}

if ( ! function_exists( 'rs_free_product_notification_for_user' ) ) {

	function rs_free_product_notification_for_user( $user_id, $order_id ) {
		$points_data = new RS_Points_Data( $user_id );
		$points      = ( '1' === get_option( 'rs_select_earn_points_based_on' ) ) ? $points_data->total_earned_points() : $points_data->total_available_points();

		// User Email.
		$user_email_subject = str_replace( '[sitename]', get_option( 'blogname' ), get_option( 'rs_subject_for_free_product_mail' ) );
		$user_email_message = str_replace( '[current_level_points]', $points, get_option( 'rs_content_for_free_product_mail' ) );

		$order_link         = esc_url_raw( add_query_arg( 'view-order', $order_id, get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) );
		$order_link         = '<a target="_blank" href="' . $order_link . '">#' . $order_id . '</a>';
		$user_email_message = str_replace( '[rsorderlink]', $order_link, $user_email_message );

		$user      = get_userdata( $user_id );
		$user_mail = is_object( $user ) ? $user->user_email : '';

		send_mail( $user_mail, $user_email_subject, $user_email_message );
	}
}

if ( ! function_exists( 'fp_paid_order_status' ) ) {

	function fp_paid_order_status( $exclude_status = false ) {
		$orderslugs    = array_map( 'wc_get_order_status_name', wc_get_is_paid_statuses() );
		$orderstatus     =  wc_get_is_paid_statuses();
		$order_statuses = array_combine( (array) $orderstatus, (array) $orderslugs );

		return $order_statuses;
	}
}

if ( ! function_exists( 'rs_check_maximum_points_restriction_per_day' ) ) {

	function rs_check_maximum_points_restriction_per_day( $user_id, $redeeming_value = 0 ) {
		$max_pts_restriction_per_day = get_option( 'rs_maximum_redeeming_per_day_restriction' );
		if ( 'yes' != get_option( 'rs_maximum_redeeming_per_day_restriction_enabled' ) || ! $max_pts_restriction_per_day || ! $user_id ) {
			return true;
		}

		if ( $redeeming_value && $redeeming_value > $max_pts_restriction_per_day ) {
			return false;
		}

		global $wpdb;
		$redeemed_points_data = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(redeempoints) as redeemed FROM {$wpdb->prefix}rsrecordpoints WHERE checkpoints = 'RP' AND userid = %d AND earneddate >= %d ", $user_id, strtotime( gmdate( 'Y-m-d' ) ) ), ARRAY_A );
		$revised_points_data  = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(earnedpoints) as revised FROM {$wpdb->prefix}rsrecordpoints WHERE checkpoints = 'RVPFRP' AND userid = %d AND earneddate >= %d ", $user_id, strtotime( gmdate( 'Y-m-d' ) ) ), ARRAY_A );

		$redeemed               = isset( $redeemed_points_data[0]['redeemed'] ) ? $redeemed_points_data[0]['redeemed'] : 0;
		$revised                = isset( $revised_points_data[0]['revised'] ) ? $revised_points_data[0]['revised'] : 0;
		$stored_redeemed_points = 0;
		if ( $redeemed > $revised ) {
			$stored_redeemed_points = $redeemed - $revised;
		} elseif ( $revised > $redeemed ) {
			$stored_redeemed_points = $revised - $redeemed;
		}

		if ( $stored_redeemed_points <= 0 ) {
			return true;
		}

		return ( $stored_redeemed_points + $redeeming_value ) <= $max_pts_restriction_per_day;
	}
}

if ( ! function_exists( 'srp_get_bulk_action_redeeming_points_field_keys' ) ) {

	/**
	 * Get Bulk Action Field Keys
	 *
	 * @since 28.8
	 * @return array
	 */
	function srp_get_bulk_action_redeeming_points_field_keys() {
		return array(
			'rs_select_redeeming_based_on',
			'rs_enable_bulk_update_for_product_level_redeeming',
			'rs_product_level_redeem_product_selection_type',
			'rs_include_products_for_product_level_redeem',
			'rs_exclude_products_for_product_level_redeem',
			'rs_product_level_redeem_include_categories',
			'rs_product_level_redeem_exclude_categories',
			'rs_enable_maximum_redeeming_points',
			'rs_maximum_redeeming_points',
		);
	}
}

if ( ! function_exists( 'srp_get_selected_product_ids' ) ) {

	/**
	 * Get Bulk Action Field Keys
	 *
	 * @since 28.8
	 * @param array $primary_data Bulk Action product filter.
	 * @return array
	 */
	function srp_get_selected_product_ids( $primary_data ) {
		global $wpdb;

		// Get all product ids.
		$all_product_ids = $wpdb->get_col(
			"SELECT DISTINCT p.ID FROM {$wpdb->posts} AS p WHERE p.post_type IN ('product','product_variation') AND p.post_status = 'publish'"
		);

		if ( '2' == $primary_data['srp_product_selection_type'] ) {
			$product_ids = $primary_data['srp_include_products'];
		} elseif ( '3' == $primary_data['srp_product_selection_type'] ) {
			$exclude_ids = isset( $primary_data['srp_exclude_products'] ) ? $primary_data['srp_exclude_products'] : array();
			$product_ids = array_diff( $all_product_ids, $exclude_ids );
		} elseif ( '4' == $primary_data['srp_product_selection_type'] ) {
			if ( ! srp_check_is_array( $primary_data['srp_include_categories'] ) ) {
				$primary_data['srp_include_categories'] = array();
			}

			$include_cat = implode( ',', $primary_data['srp_include_categories'] );
			$product_ids = $wpdb->get_col(
				/* translators: %1s: Include category , %2s: Include category  */
				$wpdb->prepare(
					"SELECT DISTINCT p.ID FROM {$wpdb->posts} AS p WHERE p.post_type IN ('product','product_variation') AND p.post_status = 'publish' AND ( p.post_parent IN ( SELECT DISTINCT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (%1s)) OR p.ID IN ( SELECT DISTINCT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (%2s) ) )",
					$include_cat,
					$include_cat
				)
			);
		} elseif ( '5' == $primary_data['srp_product_selection_type'] ) {
			if ( ! srp_check_is_array( $primary_data['srp_exclude_categories'] ) ) {
				$primary_data['srp_exclude_categories'] = array();
			}

			$exclude_cat = implode( ',', $primary_data['srp_exclude_categories'] );
			$product_ids = $wpdb->get_col(
				/* translators: %1s: Exclude category , %2s: Exclude category  */
				$wpdb->prepare(
					"SELECT DISTINCT p.ID FROM {$wpdb->posts} AS p WHERE p.post_type IN ('product','product_variation') AND p.post_status = 'publish' AND ( p.post_parent IN ( SELECT DISTINCT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (%1s)) OR p.ID IN ( SELECT DISTINCT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (%2s) ) )",
					$exclude_cat,
					$exclude_cat
				)
			);
			$product_ids = array_diff( $all_product_ids, $product_ids );
		} else {
			$product_ids = $all_product_ids;
		}

		return $product_ids;
	}
}

if ( ! function_exists( 'srp_pp_get_point_price_values' ) ) {

	/**
	 * Get Point Price Values
	 *
	 * @since 28.9
	 * @return array
	 */
	function srp_pp_get_point_price_values( $obj ) {
		$point_price = array();

		if ( ! srp_check_is_array( $obj ) ) {
			return $point_price;
		}

		$point_price_type     = array();
		$item_points_total    = array();
		$point_priced_product = array();

		foreach ( $obj as $key ) {
			$product_id = ! empty( $key['variation_id'] ) ? $key['variation_id'] : $key['product_id'];
			$points     = calculate_point_price_for_products( $product_id );

			if ( null !== check_display_price_type( $product_id ) ) {
				$point_price_type[] = check_display_price_type( $product_id );
			}

			$price_for_regular_product = empty( $points[ $product_id ] ) ? point_price_based_on_conversion( $product_id ) : $points[ $product_id ];
			$check_if_bundled_product  = isset( $key['bundled_by'] ) ? $key['bundled_by'] : 0;

			if ( 0 == $check_if_bundled_product ) {
				$item_points_total[] = $price_for_regular_product * $key['quantity'];
			}

			if ( ! check_display_price_type( $product_id ) ) {
				$point_priced_product[] = $product_id;
			}
		}

		$enable_point_price = 'no';
		$regular_product    = 'no';
		$_point_price_type  = '1';

		if ( srp_check_is_array( $point_price_type ) ) {
			$enable_point_price = 'yes';
		}

		if ( srp_check_is_array( $point_priced_product ) ) {
			$regular_product = 'yes';
		}

		if ( in_array( '2', $point_price_type ) ) {
			$_point_price_type = '2';
		} elseif ( 'yes' !== $regular_product ) {
			$_point_price_type = '1';
		}

		$point_price = array(
			'enable_point_price' => $enable_point_price,
			'point_price_type'   => $_point_price_type,
			'points'             => array_sum( $item_points_total ),
			'regular_product'    => $regular_product,
		);

		return $point_price;
	}
}

if ( ! function_exists( 'srp_pp_check_is_only_point_price_product' ) ) {

	/**
	 * Check is Only Point Price Product
	 *
	 * @since 28.9
	 * @return bool
	 */
	function srp_pp_check_is_only_point_price_product( $obj ) {
		if ( ! srp_check_is_array( $obj ) ) {
			return false;
		}

		$point_price_type     = array();
		$point_priced_product = array();

		foreach ( $obj as $key ) {
			$product_id = ! empty( $key['variation_id'] ) ? $key['variation_id'] : $key['product_id'];

			if ( null !== check_display_price_type( $product_id ) ) {
				$point_price_type[] = check_display_price_type( $product_id );
			}

			if ( ! check_display_price_type( $product_id ) ) {
				$point_priced_product[] = $product_id;
			}
		}

		if ( srp_check_is_array( $point_price_type ) && in_array( '2', $point_price_type ) && ! srp_check_is_array( $point_priced_product ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'fp_check_is_taxonomy_page' ) ) {
	function fp_check_is_taxonomy_page() {
		$taxonomies = get_option( 'rs_messages_in_taxonomy_page' );
		if ( ! srp_check_is_array( $taxonomies ) ) {
			return true;
		}

		$current_taxonomy = get_queried_object()->taxonomy;
		if ( in_array( $current_taxonomy, $taxonomies ) ) {
			return true;
		}

		return false;
	}
}
