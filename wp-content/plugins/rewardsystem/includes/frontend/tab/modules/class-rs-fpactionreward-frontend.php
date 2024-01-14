<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RSActionRewardModule' ) ) {

	class RSActionRewardModule {

		public static function init() {
			add_action( 'user_register', array( __CLASS__, 'award_points_for_account_signup' ), 10, 1 );

			add_action( 'user_register', array( __CLASS__, 'user_register_login_points' ), 10, 1 );

			add_action( 'wp_login', array( __CLASS__, 'login_points' ), 10, 2 );

			add_action( 'fpsl_loggedin_successfully', array( __CLASS__, 'award_points_for_social_login' ), 10, 3 );

			add_action( 'fpsl_linked_successfully', array( __CLASS__, 'award_points_for_social_link' ), 10, 2 );

			add_action( 'fpcf_cus_fields_after_save', array( __CLASS__, 'award_points_for_cus_fields' ), 10, 2 );

			add_action( 'rs_award_points_for_datepicker_in_cusfields', array( __CLASS__, 'award_points_for_datepicker_in_cus_fields_repeatedly' ), 10, 4 );

			if ( '1' == get_option( 'rs_message_before_after_cart_table' ) ) {
				add_action( 'woocommerce_before_cart_table', array( __CLASS__, 'display_messages_in_cart_and_checkout' ), 999 );
			} else {
				add_action( 'woocommerce_after_cart_table', array( __CLASS__, 'display_messages_in_cart_and_checkout' ), 999 );
			}

			add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'display_messages_in_cart_and_checkout' ), 999 );

			add_action( 'woocommerce_applied_coupon', array( __CLASS__, 'applied_coupon_notice' ), 10, 1 );

			add_action( 'woocommerce_before_customer_login_form', array( __CLASS__, 'signup_msg_in_my_account' ) );

			add_action( 'woocommerce_before_customer_login_form', array( __CLASS__, 'login_msg_in_my_account_page' ) );

			add_action( 'bbp_new_topic_post_extras', array( __CLASS__, 'award_points_for_topic_creation_in_bbpress' ), 10, 1 );

			add_action( 'bbp_new_reply_post_extras', array( __CLASS__, 'award_points_for_reply_in_bbpress' ), 10, 1 );

			add_action( 'fp_wl_user_subscribed', array( __CLASS__, 'award_points_for_subscribing_waitlist' ), 10, 2 );

			add_action( 'fp_wl_sale_converted', array( __CLASS__, 'award_points_for_waitlist_sale_converion' ), 10, 2 );

			add_filter( 'woocommerce_gateway_description', array( __CLASS__, 'shortcode_for_gateway_description' ), 10, 2 );
		}

		/* Shortcode for Gateway Description */

		public static function shortcode_for_gateway_description( $gateway_description, $gateway_id ) {

			if ( '1' === get_option( 'rs_reward_type_for_payment_gateways_' . $gateway_id, '1' ) ) {

				$gateway_point = get_option( 'rs_reward_payment_gateways_' . $gateway_id );
				if ( ! $gateway_point ) {
					return $gateway_description;
				}
			} else {
				$percentage_value = get_option( 'rs_reward_points_for_payment_gateways_in_percent_' . $gateway_id );
				if ( ! $percentage_value ) {
					return $gateway_description;
				}

				$cart_subtotal   = srp_cart_subtotal( true );
				$point_coversion = ( ( (float) $percentage_value / 100 ) * $cart_subtotal );
				$gateway_point   = earn_point_conversion( $point_coversion );
			}

			$gateway_point       = sprintf( "<span class='rs_gateway_points'><b>%s</b></span>", esc_html( $gateway_point ) );
			$gateway_description = str_replace( '[gatewaypoints]', $gateway_point, $gateway_description );

			return $gateway_description;
		}

		public static function award_points_for_account_signup( $user_id ) {
			$BanType = check_banning_type( $user_id );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			if ( '1' == get_option( 'rs_select_account_signup_points_award' ) ) {
				self::rs_add_registration_rewards_points( $user_id );
			} elseif ( isset( $_COOKIE['rsreferredusername'] ) ) {
					self::rs_add_registration_rewards_points( $user_id );
			}
		}

		public static function rs_add_registration_rewards_points( $user_id ) {
			if ( '' != get_post_meta( $user_id, 'rs_registered_user', true ) ) {
				return;
			}

			if ( ( 'yes' == get_option( 'rs_reward_signup_after_first_purchase' ) ) ) {
				// After First Purchase Registration Points
				self::rs_add_regpoints_to_user_after_first_purchase( $user_id );
			} else {
				// Instant Registration Points
				self::rs_add_regpoints_to_user_instantly( $user_id );
			}
						/**
						 * Hook:fp_reward_point_for_registration.
						 *
						 * @since 1.0
						 */
			do_action( 'fp_reward_point_for_registration' );
			update_user_meta( $user_id, 'rs_registered_user', 1 );
		}

		/* Instant Registration Points */

		public static function rs_add_regpoints_to_user_instantly( $user_id ) {
			if ( 'yes' != get_option( '_rs_enable_signup' ) ) {
				return;
			}

			$pointstoinsert = get_option( 'rs_reward_signup' );
			if ( '' == $pointstoinsert ) {
				return;
			}

			self::award_reg_points_instantly( $pointstoinsert, $user_id, $event_slug = 'RRP', $Network    = '' );
		}

		/* After First Purchase Registration Points */

		public static function rs_add_regpoints_to_user_after_first_purchase( $user_id ) {
			if ( 'yes' != get_option( '_rs_enable_signup' ) ) {
				return;
			}

			$pointstoaward = get_option( 'rs_reward_signup' );
			if ( '' == $pointstoaward ) {
				return;
			}

			self::award_reg_points_after_first_purchase( $pointstoaward, $user_id, $event_slug = 'RRP', $Network    = '' );
		}

		/*
		 * User register login points.
		 *
		 * @return void
		 */

		public static function user_register_login_points( $user_id ) {

			if ( ! $user_id ) {
				return;
			}

			self::insert_points_for_login( get_option( 'rs_reward_points_for_login' ), 'LRP', $user_id, '' );
		}

		/*
		 * Login points.
		 *
		 * @return void
		 */

		public static function login_points( $user_name, $user ) {

			if ( ! is_object( $user ) || ! $user->exists() ) {
				return;
			}

			self::insert_points_for_login( get_option( 'rs_reward_points_for_login' ), 'LRP', $user->ID, '' );
		}

		/* Award Reward Points for Social Login - Compatability with Social Login Plugin */

		public static function award_points_for_social_login( $UserId, $Network, $Type ) {
			$BanType = check_banning_type( $UserId );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			$network_name = fpsl_get_plugin_networks();
			$Network      = $network_name[ $Network ];
			if ( '2' == $Type || '3' == $Type ) {
				$pointsforsociallogin = get_option( 'rs_reward_for_social_network_login' );
				self::insert_points_for_login( $pointsforsociallogin, $event_slug           = 'SLRP', $UserId, $Network );
			}
			if ( '1' == $Type ) {
				$pointsforsocialsignup = get_option( 'rs_reward_for_social_network_signup' );
				self::award_points_for_signup( $pointsforsocialsignup, $UserId, $Network );
			}
		}

		/* Insert Points for Login */

		public static function insert_points_for_login( $pointsforlogin, $event_slug, $userid, $Network = '' ) {

			if ( ! $userid || 'yes' != get_option( 'rs_reward_action_activated' ) || 'yes' != get_option( 'rs_enable_reward_points_for_login' ) ) {
				return;
			}

			if ( empty( $pointsforlogin ) ) {
				return;
			}

			$BanType = check_banning_type( $userid );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			if ( ! allow_reward_points_for_user( $userid ) ) {
				return;
			}

			global $wpdb;
						$table_data = $wpdb->get_row( $wpdb->prepare( "SELECT earnedpoints FROM {$wpdb->prefix}rsrecordpoints WHERE checkpoints = %s AND userid = %d AND earneddate >= %d", $event_slug, $userid, strtotime( gmdate( 'Y-m-d' ) ) ), ARRAY_A );
			if ( ! empty( $table_data['earnedpoints'] ) ) {
				return;
			}

			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $pointsforlogin, $pointsredeemed = 0, $event_slug, $userid, $nomineeid      = '', $referrer_id    = '', $productid      = '', $variationid    = '', $reasonindetail = $Network );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $pointsforlogin,
					'event_slug'        => $event_slug,
					'user_id'           => $userid,
					'reasonindetail'    => $Network,
					'totalearnedpoints' => $pointsforlogin,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}
						/**
						 * Hook:fp_reward_point_for_login.
						 *
						 * @since 1.0
						 */
			do_action( 'fp_reward_point_for_login' );
		}

		/* Award Points for Social Signup - Compatability with Social Login Plugin */

		public static function award_points_for_signup( $pointsforsocialsignup, $UserId, $Network ) {
			if ( ! allow_reward_points_for_user( $UserId ) ) {
				return;
			}

			if ( '' == $pointsforsocialsignup ) {
				return;
			}

			if ( '1' == get_option( 'rs_select_account_signup_points_award' ) ) {
				self::insert_point_for_social_signup( $UserId, $pointsforsocialsignup, $Network );
			} elseif ( isset( $_COOKIE['rsreferredusername'] ) ) {
					self::insert_point_for_social_signup( $UserId, $pointsforsocialsignup, $Network );
			}
		}

		/* Insert Points for Social Signup */

		public static function insert_point_for_social_signup( $user_id, $pointsforsocialsignup, $Network ) {
			$CheckAlreadyRegistedUser = get_post_meta( $user_id, 'rs_check_already_reg_user_through_social_login', true );
			if ( '' != $CheckAlreadyRegistedUser ) {
				return;
			}

			$AwardAfterFirstPurchase = get_option( 'rs_reward_signup_after_first_purchase' );
			if ( ( 'yes' == $AwardAfterFirstPurchase ) ) {
				// After First Purchase Registration Points for Social Signup
				self::award_reg_points_after_first_purchase( $pointsforsocialsignup, $user_id, $event_slug = 'SLRRP', $Network );
			} else {
				// Instant Registration Points for Social Signup
				self::award_reg_points_instantly( $pointsforsocialsignup, $user_id, $event_slug = 'SLRRP', $Network );
			}

			update_user_meta( $user_id, 'rs_check_already_reg_user_through_social_login', 1 );
		}

		public static function award_reg_points_instantly( $pointstoaward, $user_id, $event_slug, $Network ) {
			if ( ! allow_reward_points_for_user( $user_id ) ) {
				return;
			}

			$registration_points = RSMemberFunction::earn_points_percentage( $user_id, (float) $pointstoaward );
			$restrictuserpoints  = get_option( 'rs_max_earning_points_for_user' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) && '' != $restrictuserpoints ) {
				$currentregistrationpoints = ( $registration_points <= $restrictuserpoints ) ? $registration_points : $restrictuserpoints;
			} else {
				$currentregistrationpoints = $registration_points;
			}
			$table_args = array(
				'user_id'           => $user_id,
				'pointstoinsert'    => $currentregistrationpoints,
				'checkpoints'       => $event_slug,
				'totalearnedpoints' => $currentregistrationpoints,
				'reason'            => $Network,
			);
			RSPointExpiry::insert_earning_points( $table_args );
			RSPointExpiry::record_the_points( $table_args );
			add_user_meta( $user_id, '_points_awarded', '1' );
		}

		public static function award_reg_points_after_first_purchase( $pointstoaward, $user_id, $event_slug, $Network ) {
			global $wpdb;
			$banning_type = check_banning_type( $user_id );
			if ( 'earningonly' != $banning_type && 'both' != $banning_type ) {
				$registration_points       = RSMemberFunction::earn_points_percentage( $user_id, (float) $pointstoaward );
				$restrictuserpoints        = get_option( 'rs_max_earning_points_for_user' );
								$oldpoints = $wpdb->get_results( $wpdb->prepare( "SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM {$wpdb->prefix}rspointexpiry WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid=%d", $user_id ), ARRAY_A );
				$totaloldpoints            = $oldpoints[0]['availablepoints'];
				$currentregistrationpoints = $totaloldpoints + $registration_points;
				if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) && ( '' != $restrictuserpoints ) ) {
					$currentregistrationpoints = ( $currentregistrationpoints <= $restrictuserpoints ) ? $registration_points : $restrictuserpoints;
				}

				$extra_args = get_user_meta( $user_id, 'srp_data_for_reg_points', true );
				if ( srp_check_is_array( $extra_args ) ) {
					$reg_point_args   = array(
						'userid'         => $user_id,
						'points'         => $currentregistrationpoints,
						'refuserid'      => '',
						'refpoints'      => '',
						'event_slug'     => $event_slug,
						'reaseonidetail' => $Network,
					);
					$args[ $user_id ] = isset( $extra_args[ $user_id ] ) ? array_merge( $reg_point_args, $extra_args[ $user_id ] ) : $reg_point_args;
				} else {
					$args[ $user_id ] = array(
						'userid'         => $user_id,
						'points'         => $currentregistrationpoints,
						'refuserid'      => '',
						'refpoints'      => '',
						'event_slug'     => $event_slug,
						'reaseonidetail' => $Network,
					);
				}
				update_user_meta( $user_id, 'srp_data_for_reg_points', $args );
			}
		}

		/* Award Points for Social Linking - Compatability with Social Login Plugin */

		public static function award_points_for_social_link( $userid, $Network ) {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$BanType = check_banning_type( $userid );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			if ( ! allow_reward_points_for_user( $userid ) ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_for_social_account_linking' ) ) {
				return;
			}

			$pointstoinsert = get_option( 'rs_reward_for_social_account_linking' );
			if ( empty( $pointstoinsert ) ) {
				return;
			}

			$getusermeta  = (array) get_user_meta( $userid, 'rs_restrict_points_for_social_linking', true );
			$network_name = fpsl_get_plugin_networks();
			$Network      = $network_name[ $Network ];
			if ( in_array( $Network, $getusermeta ) ) {
				return;
			}

			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $pointstoinsert, $pointsredeemed = 0, $event_slug     = 'SLLRP', $UserId, $nomineeid      = '', $referrer_id    = '', $productid      = '', $variationid    = '', $reasonindetail = $Network );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $pointstoinsert,
					'event_slug'        => 'SLLRP',
					'user_id'           => $userid,
					'reasonindetail'    => $Network,
					'totalearnedpoints' => $pointstoinsert,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}

			$oldlogindata = (array) get_user_meta( $userid, 'rs_restrict_points_for_social_linking', true );
			$newdata      = (array) $Network;
			$mergedata    = array_merge( $oldlogindata, $newdata );
			update_user_meta( $userid, 'rs_restrict_points_for_social_linking', array_filter( $mergedata ) );
		}

		/* Award Points for Custom Fields */

		public static function award_points_for_cus_fields( $userid, $custom_fields ) {
			$BanType = check_banning_type( $userid );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			if ( ! allow_reward_points_for_user( $userid ) ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_points_for_cus_field_reg' ) ) {
				return;
			}

			$Rules = get_option( 'rs_rule_for_custom_reg_field' );
			if ( ! srp_check_is_array( $Rules ) ) {
				return;
			}

			foreach ( $Rules as $individual_rule ) {
				$field_id                   = $individual_rule['custom_fields'];
				$pointstoinsert             = $individual_rule['reward_points'];
				$pointsforfillingdatepicker = isset( $individual_rule['award_points_for_filling'] ) ? $individual_rule['award_points_for_filling'] : 'no';
				$repeat_points              = isset( $individual_rule['repeat_points'] ) ? $individual_rule['repeat_points'] : 'no';

				if ( empty( $pointstoinsert ) || empty( $field_id ) ) {
					continue;
				}

				$field_data = fpcf_get_custom_fields( $field_id );
				if ( ! $field_data ) {
					continue;
				}

				$getusermeta = (array) get_user_meta( $userid, 'rs_points_awarded_for_' . $field_data->field_type, true );
				if ( in_array( $field_id, $getusermeta ) ) {
					continue;
				}

				if ( ! in_array( $field_id, $custom_fields ) ) {
					continue;
				}

				$field_value = fpcf_get_user_meta( $userid, $field_data->field_name );
				if ( empty( $field_value ) ) {
					continue;
				}

				if ( 'datepicker' == $field_data->field_type ) {
					$current_date = strtotime( gmdate( 'd-m-Y' ) );
					$bday_date    = strtotime( $field_value ) ? strtotime( $field_value ) : strtotime( $field_value . '-' . gmdate( 'Y' ) );
					if ( ! $bday_date ) {
						continue;
					}

					$next_schedule = true;
					if ( $current_date > $bday_date ) {
						$currentbdaydate          = strtotime( gmdate( 'd-m', $bday_date ) . '-' . gmdate( 'Y' ) );
						$TimeStampForNextSchedule = ( $current_date < $currentbdaydate ) ? ( $current_date + ( $currentbdaydate - $current_date ) ) : strtotime( '+1 year', $currentbdaydate );
						if ( $current_date == $currentbdaydate ) {
							self::insert_points_for_custom_fields( $pointstoinsert, 'CRPFDP', $userid, $field_data, $field_id );
							$next_schedule = ( 'yes' == $repeat_points ) ? true : false;
						}
					} else {
						$TimeStampForNextSchedule = ( $current_date < $bday_date ) ? ( $current_date + ( $bday_date - $current_date ) ) : strtotime( '+1 year', $bday_date );
						if ( $current_date == $bday_date ) {
							self::insert_points_for_custom_fields( $pointstoinsert, 'CRPFDP', $userid, $field_data, $field_id );
							$next_schedule = ( 'yes' == $repeat_points ) ? true : false;
						}
					}

					if ( $next_schedule ) {
						wp_schedule_single_event( $TimeStampForNextSchedule, 'rs_award_points_for_datepicker_in_cusfields', array( $TimeStampForNextSchedule, $field_data, $pointstoinsert, $userid ) );
					}

					if ( 'no' == $pointsforfillingdatepicker ) {
						continue;
					}
				}

				self::insert_points_for_custom_fields( $pointstoinsert, 'CRFRP', $userid, $field_data, $field_id );
			}
		}

		/* Award Points for Datepicker(i.e for Birthday) */

		public static function award_points_for_datepicker_in_cus_fields_repeatedly( $timestamp, $field_data, $pointstoinsert, $userid ) {
			$BanType = check_banning_type( $userid );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			if ( ! allow_reward_points_for_user( $userid ) ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_points_for_cus_field_reg' ) ) {
				return;
			}

			$Rules = get_option( 'rs_rule_for_custom_reg_field' );
			if ( ! srp_check_is_array( $Rules ) ) {
				return;
			}

			foreach ( $Rules as $individual_rule ) {
				$field_id       = $individual_rule['custom_fields'];
				$pointstoinsert = $individual_rule['reward_points'];
				$repeat_points  = isset( $individual_rule['repeat_points'] ) ? $individual_rule['repeat_points'] : 'no';

				if ( empty( $pointstoinsert ) || empty( $field_id ) ) {
					continue;
				}

				$field_data = fpcf_get_custom_fields( $field_id );
				if ( ! $field_data ) {
					continue;
				}

				if ( 'datepicker' == $field_data->field_type ) {
					if ( 'yes' == $repeat_points ) {
						self::insert_points_for_custom_fields( $pointstoinsert, 'CRPFDP', $userid, $field_data, $field_id );
						$TimeStampForNextSchedule = strtotime( '+1 year', $timestamp );
						wp_schedule_single_event( $TimeStampForNextSchedule, 'rs_award_points_for_datepicker_in_cusfields', array( $TimeStampForNextSchedule, $field_data, $pointstoinsert, $userid ) );
					} else {
						self::insert_points_for_custom_fields( $pointstoinsert, 'CRPFDP', $userid, $field_data, $field_id );
					}
				}
			}
		}

		public static function insert_points_for_custom_fields( $pointstoinsert, $event_slug, $userid, $field_data, $field_id ) {
			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $pointstoinsert, 0, $event_slug, $userid, '', '', '', '', $field_data->field_label );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $pointstoinsert,
					'event_slug'        => $event_slug,
					'user_id'           => $userid,
					'reasonindetail'    => $field_data->field_label,
					'totalearnedpoints' => $pointstoinsert,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}
			$oldlogindata = (array) get_user_meta( $userid, 'rs_points_awarded_for_' . $field_data->field_type, true );
			$newdata      = (array) $field_id;
			$mergedata    = array_merge( $oldlogindata, $newdata );
			update_user_meta( $userid, 'rs_points_awarded_for_' . $field_data->field_type, array_filter( $mergedata ) );
		}

		/* Display messages in cart and checkout */

		public static function display_messages_in_cart_and_checkout() {

			self::display_message_for_coupon_reward_points( WC()->cart->applied_coupons );
		}

		/* Applied coupon notice */

		public static function applied_coupon_notice( $coupon_name ) {

			self::display_message_for_coupon_reward_points( array( $coupon_name ) );
		}

		/* Display Earn Points Message for Coupon */

		public static function display_message_for_coupon_reward_points( $applied_coupons ) {
			$BanType = check_banning_type( get_current_user_id() );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_coupon_reward_success_msg' ) ) {
				return;
			}

			$SortType = ( '1' == get_option( 'rs_choose_priority_level_selection_coupon_points' ) ) ? 'desc' : 'asc';
			$Rules    = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_couponpoints' ), 'reward_points', $SortType );
			$Codes    = array();
			$Datas    = array();
			if ( ! srp_check_is_array( $Rules ) ) {
				return;
			}

			foreach ( $Rules as $Key => $Rule ) {
				if ( ! isset( $Rule['coupon_codes'] ) ) {
					continue;
				}

				if ( ! srp_check_is_array( $Rule['coupon_codes'] ) ) {
					continue;
				}

				foreach ( $Rule['coupon_codes'] as $Code ) {
					if ( in_array( $Code, $Codes ) ) {
						continue;
					}

					$Codes[ $Key ] = $Code;
				}
			}

			if ( ! srp_check_is_array( $Codes ) ) {
				return;
			}

			foreach ( $Codes as $KeyToFind => $Value ) {
				$Datas[] = $Rules[ $KeyToFind ];
			}

			if ( ! srp_check_is_array( $Datas ) ) {
				return;
			}

			foreach ( $Datas as $Data ) {
				$CouponCodes = $Data['coupon_codes'];
				if ( ! srp_check_is_array( $CouponCodes ) ) {
					continue;
				}

				$Points = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $Data['reward_points'] );

				foreach ( $CouponCodes as $Code ) {
					if ( ! check_if_coupon_exist_in_cart( $Code, $applied_coupons ) ) {
						continue;
					}

					$points_in_price = srp_formatted_price( round_off_type_for_currency( redeem_point_conversion( (float) $Points, get_current_user_id(), 'price' ) ) );
					$Msg             = str_replace( array( '[coupon_name]', '[coupon_rewardpoints]', '[equalamount]' ), array( $Code, $Points, $points_in_price ), get_option( 'rs_coupon_applied_reward_success' ) );
					?>
					<div class="woocommerce-message rs-coupon-reward-message">
						<?php echo esc_html( $Msg ); ?>
					</div>
					<?php
				}
			}
		}

		/* Display Earn Points Message for Signup */

		public static function signup_msg_in_my_account() {
			if ( is_user_logged_in() ) {
				return;
			}

			if ( ! is_account_page() ) {
				return;
			}

			if ( 'yes' != get_option( '_rs_enable_signup' ) ) {
				return;
			}

			if ( '2' == get_option( 'rs_show_hide_message_for_sign_up' ) ) {
				return;
			}

			$SignUpPoints = get_option( 'rs_reward_signup' );
			if ( empty( $SignUpPoints ) ) {
				return;
			}

			$ReplacedMessage = str_replace( '[rssignuppoints]', round_off_type( $SignUpPoints ), get_option( 'rs_message_user_points_for_sign_up' ) );
			$BoolVal         = '1' == get_option( 'rs_select_account_signup_points_award' ) ? true : isset( $_COOKIE['rsreferredusername'] );
			if ( ! $BoolVal ) {
				return;
			}
			?>
			<div class="woocommerce-info">
				<?php
				echo do_shortcode( $ReplacedMessage );
				?>
			</div>
			<?php
		}

		/* Display Earn Points Message for Login */

		public static function login_msg_in_my_account_page() {
			if ( ! is_account_page() ) {
				return;
			}

			if ( is_user_logged_in() ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_reward_points_for_login' ) ) {
				return;
			}

			if ( '2' == get_option( 'rs_show_hide_message_for_daily_login' ) ) {
				return;
			}

			$LoginPoints = get_option( 'rs_reward_points_for_login' );
			if ( empty( $LoginPoints ) ) {
				return;
			}

			$ReplacedMessage = str_replace( '[rsdailyloginpoints]', round_off_type( $LoginPoints ), get_option( 'rs_message_user_points_for_daily_login' ) );
			?>
			<div class="woocommerce-info">
				<?php
				echo do_shortcode( $ReplacedMessage );
				?>
			</div>
			<?php
		}

		/* Award Points for Topic Creation in BBPress */

		public static function award_points_for_topic_creation_in_bbpress( $topic_id ) {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$BanType = check_banning_type( get_current_user_id() );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_reward_points_for_create_topic' ) ) {
				return;
			}

			$Points = get_option( 'rs_reward_points_for_creatic_topic' );
			if ( empty( $Points ) ) {
				return;
			}

			$Post = get_post( $topic_id );
			if ( ! is_object( $Post ) ) {
				return;
			}

			$PostType = $Post->post_type;
			if ( 'topic' != $PostType ) {
				return;
			}

			$PostParent            = $Post->post_parent;
			$CheckIfAlreadyAwarded = get_user_meta( get_current_user_id(), 'topiccreation' . $PostParent, true );
			if ( '1' == $CheckIfAlreadyAwarded ) {
				return;
			}

			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $Points, $pointsredeemed = 0, 'RPCT', get_current_user_id(), $nomineeid      = '', $referrer_id    = '', $productid      = '', $variationid    = '', $reasonindetail );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $Points,
					'event_slug'        => 'RPCT',
					'user_id'           => get_current_user_id(),
					'totalearnedpoints' => $Points,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}
			update_user_meta( get_current_user_id(), 'topiccreation' . $PostParent, '1' );
		}

		/* Award Points for Reply Topic in BBPress */

		public static function award_points_for_reply_in_bbpress( $topic_id ) {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$BanType = check_banning_type( get_current_user_id() );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_reward_points_for_reply_topic' ) ) {
				return;
			}

			$Points = get_option( 'rs_reward_points_for_reply_topic' );
			if ( empty( $Points ) ) {
				return;
			}

			$Post = get_post( $topic_id );
			if ( ! is_object( $Post ) ) {
				return;
			}

			$PostType = $Post->post_type;
			if ( 'reply' != $PostType ) {
				return;
			}

			$PostParent            = $Post->post_parent;
			$CheckIfAlreadyAwarded = get_user_meta( get_current_user_id(), 'userreplytopic' . $PostParent, true );
			if ( '1' == $CheckIfAlreadyAwarded ) {
				return;
			}

			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( get_option( 'rs_max_earning_points_for_user' ), $Points, $pointsredeemed = 0, 'RPRT', get_current_user_id(), $nomineeid      = '', $referrer_id    = '', $productid      = '', $variationid    = '', $reasonindetail );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $Points,
					'event_slug'        => 'RPRT',
					'user_id'           => get_current_user_id(),
					'totalearnedpoints' => $Points,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}
			update_user_meta( get_current_user_id(), 'userreplytopic' . $PostParent, '1' );
		}

		/* Award Points for Waitlist Subscription */

		public static function award_points_for_subscribing_waitlist( $product_id, $user_id ) {
			$BanType = check_banning_type( $user_id );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_for_waitlist_subscribing' ) ) {
				return;
			}

			if ( '' == get_option( 'rs_reward_for_waitlist_subscribing' ) ) {
				return;
			}

			$Points  = get_option( 'rs_reward_for_waitlist_subscribing' );
			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $Points, $pointsredeemed = 0, $event_slug     = 'RPFWLS', $user_id, $nomineeid      = '', $referrer_id    = '', $product_id, $variationid    = '', $reasonindetail = '' );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $Points,
					'event_slug'        => 'RPFWLS',
					'user_id'           => $user_id,
					'product_id'        => $product_id,
					'totalearnedpoints' => $Points,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}
		}

		public static function award_points_for_waitlist_sale_converion( $product_id, $order_id ) {
			if ( 'yes' != get_option( 'rs_enable_for_waitlist_sale_conversion' ) ) {
				return;
			}

			if ( '' == get_option( 'rs_reward_for_waitlist_sale_conversion' ) ) {
				return;
			}

			$order      = wc_get_order( $order_id );
			$order_data = srp_order_obj( $order );
			$user_id    = $order_data['order_userid'];
			$BanType    = check_banning_type( $user_id );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			$sale_converted_points = get_option( 'rs_reward_for_waitlist_sale_conversion' );
			$new_obj               = new RewardPointsOrder( $order_id, $apply_previous_order_points = 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $sale_converted_points, $pointsredeemed = 0, $event_slug     = 'RPFWLSC', $user_id, $nomineeid      = '', $referrer_id    = '', $product_id, $variationid    = '', $reasonindetail = '' );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $sale_converted_points,
					'event_slug'        => 'RPFWLSC',
					'user_id'           => $user_id,
					'product_id'        => $product_id,
					'totalearnedpoints' => $sale_converted_points,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}
		}
	}

	RSActionRewardModule::init();
}
