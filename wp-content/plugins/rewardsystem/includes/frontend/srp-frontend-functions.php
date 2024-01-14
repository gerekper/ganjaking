<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'woocommerce_coupon_field' ) ) {

	function woocommerce_coupon_field( $Param ) {
		if ( 'show' == $Param ) {
			$contents = '.coupon, .woocommerce-form-coupon{
						display: block !important;
					}';
		} else {
			$contents = '.coupon, .woocommerce-form-coupon{
						display: none !important;
					}';
		}

		wp_register_style( 'fp-srp-coupon-field-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
		wp_enqueue_style( 'fp-srp-coupon-field-style' );
		wp_add_inline_style( 'fp-srp-coupon-field-style', $contents );
	}
}

if ( ! function_exists( 'redirect_url_for_guest' ) ) {

	function redirect_url_for_guest( $redirect ) {
		if ( isset( $_REQUEST['redirect_to'] ) ) {
			$redirect = wc_clean( wp_unslash( $_REQUEST['redirect_to'] ) );
		}

		return $redirect;
	}

	add_filter( 'woocommerce_login_redirect', 'redirect_url_for_guest' );

	add_filter( 'woocommerce_registration_redirect', 'redirect_url_for_guest' );
}

if ( ! function_exists( 'check_if_pointprice_product_exist_in_cart' ) ) {

	function check_if_pointprice_product_exist_in_cart() {
		global $woocommerce;
		$Obj = function_exists( 'WC' ) ? WC() : $woocommerce;
		if ( get_option( 'rs_point_price_activated' ) == 'no' ) {
			return false;
		}

		if ( empty( $Obj->cart->cart_contents ) ) {
			return false;
		}

		foreach ( $Obj->cart->cart_contents as $values ) {
			$ProductId = ! empty( $values['variation_id'] ) ? $values['variation_id'] : $values['product_id'];
			if ( ! empty( check_display_price_type( $ProductId ) ) ) {
				return true;
			}
		}
	}
}

if ( ! function_exists( 'check_if_coupon_applied' ) ) {

	function check_if_coupon_applied() {
		global $woocommerce;
		$Obj = function_exists( 'WC' ) ? WC() : $woocommerce;
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( ! srp_check_is_array( $Obj->cart->get_applied_coupons() ) ) {
			return false;
		}

		foreach ( $Obj->cart->get_applied_coupons() as $Code ) {
			$CouponObj         = new WC_Coupon( $Code );
			$CouponObj         = srp_coupon_obj( $CouponObj );
			$CouponId          = $CouponObj['coupon_id'];
			$CheckIfSUMOCoupon = get_post_meta( $CouponId, 'sumo_coupon_check', true );
			if ( 'yes' == get_option( '_rs_not_allow_earn_points_if_sumo_coupon' ) && 'yes' == $CheckIfSUMOCoupon ) {
				return true;
			}

			$UserInfo   = get_user_by( 'id', get_current_user_id() );
			$UserName   = $UserInfo->user_login;
			$Redeem     = 'sumo_' . strtolower( "$UserName" );
			$AutoRedeem = 'auto_redeem_' . strtolower( $UserName );
			if ( get_option( 'rs_enable_redeem_for_order' ) == 'yes' ) {
				if ( strtolower( $Code ) == $Redeem || strtolower( $Code ) == $AutoRedeem ) {
					return true;
				}
			}

			if ( get_option( 'rs_disable_point_if_coupon' ) == 'yes' ) {
				if ( strtolower( $Code ) != $Redeem && strtolower( $Code ) != $AutoRedeem ) {
					return true;
				}
			}
		}
		return false;
	}
}

if ( ! function_exists( 'enable_reward_program_in_checkout' ) ) {

	function enable_reward_program_in_checkout( $OrderId, $data ) {
		if ( is_user_logged_in() ) {
			return;
		}

		if ( isset( $data['enable_reward_prgm'] ) && ! empty( $data['enable_reward_prgm'] ) ) {
			update_user_meta( get_current_user_id(), 'allow_user_to_earn_reward_points', 'yes' );

			/**
			 * This hook is used to do extra action when user involved in Reward Program.
			 *
			 * @param int $userid User ID.
			 * @since 29.4
			 */
			do_action( 'fp_rs_reward_program_enabled', get_current_user_id() );
		}
	}

	add_action( 'woocommerce_checkout_update_order_meta', 'enable_reward_program_in_checkout', 10, 2 );
}

if ( ! function_exists( 'send_notification_when_reward_program_enabled' ) ) {

	/**
	 * Send email notification for user when they involved in Reward Program.
	 *
	 * @param int $user_id User ID.
	 */
	function send_notification_when_reward_program_enabled( $user_id ) {
		if ( 'yes' !== get_option( 'rs_enable_email_for_reward_program' ) ) {
			return;
		}

		$user_info = get_userdata( $user_id );
		if ( ! is_object( $user_info ) ) {
			return;
		}

		$admin_email = get_option( 'admin_email' );
		$subject     = get_option( 'rs_subject_for_reward_program_email' );
		$message     = str_replace( array( '[username]', '[email_id]' ), array( $user_info->user_email, $user_info->user_login ), get_option( 'rs_message_for_reward_program_email' ) );
		send_mail( $admin_email, $subject, $message );
	}

	add_action( 'fp_rs_reward_program_enabled', 'send_notification_when_reward_program_enabled' );
}

if ( ! function_exists( 'check_if_referral_is_restricted' ) ) {

	function check_if_referral_is_restricted() {
		$UserSelectionType = get_option( 'rs_select_type_of_user_for_referral' );
		if ( is_user_logged_in() ) {
			$UserId      = get_current_user_id();
			$UserRoleObj = wp_get_current_user();
			$UserRole    = $UserRoleObj->roles;
		} elseif ( isset( $_GET['ref'] ) ) {
			$ref      = wc_clean( wp_unslash( $_GET['ref'] ) );
			$UserObj  = get_user_by( 'login', $ref );
			$UserId   = is_object( $UserObj ) ? $UserObj->ID : $ref;
			$UserRole = is_object( $UserObj ) ? $UserObj->roles : get_user_by( 'id', $ref )->roles;
		} else {
			$UserId   = '';
			$UserRole = array();
		}
		if ( '1' == $UserSelectionType ) {
			return true;
		} elseif ( '2' == $UserSelectionType ) {
			if ( get_option( 'rs_select_include_users_for_show_referral_link' ) != '' ) {
				$UserIds = srp_check_is_array( get_option( 'rs_select_include_users_for_show_referral_link' ) ) ? get_option( 'rs_select_include_users_for_show_referral_link' ) : explode( ',', get_option( 'rs_select_include_users_for_show_referral_link' ) );
				if ( in_array( $UserId, $UserIds ) ) {
					return true;
				}
			}
		} elseif ( '3' == $UserSelectionType ) {
			$getuser = get_option( 'rs_select_exclude_users_list_for_show_referral_link' );
			if ( get_option( 'rs_select_exclude_users_list_for_show_referral_link' ) != '' ) {
				$UserIds = srp_check_is_array( get_option( 'rs_select_exclude_users_list_for_show_referral_link' ) ) ? get_option( 'rs_select_exclude_users_list_for_show_referral_link' ) : explode( ',', get_option( 'rs_select_exclude_users_list_for_show_referral_link' ) );
				if ( ! in_array( $UserId, $UserIds ) ) {
					return true;
				}
			}
		} elseif ( '4' == $UserSelectionType ) {
			if ( srp_check_is_array( get_option( 'rs_select_users_role_for_show_referral_link' ) ) ) {
				$inc_role = array_intersect( (array) $UserRole, (array) get_option( 'rs_select_users_role_for_show_referral_link' ) );
				if ( srp_check_is_array( $inc_role ) ) {
					return true;
				}
			}
		} elseif ( srp_check_is_array( get_option( 'rs_select_exclude_users_role_for_show_referral_link' ) ) ) {
				$exc_role = array_intersect( (array) $UserRole, (array) get_option( 'rs_select_exclude_users_role_for_show_referral_link' ) );
			if ( ! srp_check_is_array( $exc_role ) ) {
				return true;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'custom_message_in_thankyou_page' ) ) {

	function custom_message_in_thankyou_page( $Points, $CurrencyValue, $ShowCurrencyValue, $ShowCustomMsg, $CustomMsg, $PaymentPlanPoints ) {
		$Msg = '';

		$PointsToDisplay = (float) $Points - (float) $PaymentPlanPoints;
		$PointsToDisplay = round_off_type( $PointsToDisplay );

		if ( '1' === get_option( "$ShowCustomMsg" ) ) {
			$Msg .= ' ' . get_option( "$CustomMsg" );
		}

		if ( '1' === get_option( "$ShowCurrencyValue" ) ) {
			$Msg .= '&nbsp;(' . $CurrencyValue . ')';
		}

		return $PointsToDisplay . $Msg;
	}
}

if ( ! function_exists( 'points_for_simple_product' ) ) {

	function points_for_simple_product( $product_id = false ) {
		global $post;
		if ( ! is_object( $post ) ) {
			return;
		}

		$product_id = ! $product_id ? $post->ID : $product_id;

		if ( 'yes' === block_points_for_salepriced_product( $product_id, 0 ) ) {
			return;
		}

		$ProductObj = srp_product_object( $product_id );
		if ( ! is_object( $ProductObj ) ) {
			return;
		}

		if ( is_shop() || is_product() || is_page() || is_product_category() || is_product_tag() || fp_check_is_taxonomy_page() ) {
			if ( ( srp_product_type( $product_id ) == 'simple' || ( srp_product_type( $product_id ) == 'subscription' ) || srp_product_type( $product_id ) == 'bundle' ) || srp_product_type( $product_id ) == 'woosb' ) {
				$args   = array(
					'productid' => $product_id,
					'item'      => array( 'qty' => '1' ),
				);
				$Points = check_level_of_enable_reward_point( $args );
				$Points = get_current_user_id() > 0 ? RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $Points ) : (float) $Points;
				return $Points;
			}
		}
		return 0;
	}
}

if ( ! function_exists( 'referral_points_for_simple_product' ) ) {

	function referral_points_for_simple_product() {
		if ( isset( $_COOKIE['rsreferredusername'] ) ) {
			$ref_name = wc_clean( wp_unslash( $_COOKIE['rsreferredusername'] ) );
			$refuser  = ( 1 == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login', $ref_name ) : get_user_by( 'id', $ref_name );
			if ( ! $refuser ) {
				return 0;
			}
			$UserId = $refuser->ID;
		} else {
			$UserId = check_if_referrer_has_manual_link( get_current_user_id() );
		}

		if ( ! $UserId ) {
			return 0;
		}

		global $post;
		if ( ! is_object( $post ) ) {
			return 0;
		}

		if ( block_points_for_salepriced_product( $post->ID, 0 ) == 'yes' ) {
			return 0;
		}

		$ProductObj = srp_product_object( $post->ID );
		if ( ! is_object( $ProductObj ) ) {
			return 0;
		}

		if ( is_shop() || is_product() || is_page() || is_product_category() || is_tax( 'pwb-brand' ) ) {
			if ( ( srp_product_type( $post->ID ) == 'simple' || ( srp_product_type( $post->ID ) == 'subscription' ) ) ) {
				$args   = array(
					'productid'     => $post->ID,
					'item'          => array( 'qty' => '1' ),
					'referred_user' => $UserId,
				);
				$Points = check_level_of_enable_reward_point( $args );
				$Points = ( $UserId > 0 ) ? RSMemberFunction::earn_points_percentage( $UserId, (float) $Points ) : (float) $Points;
				return $Points;
			}
		}
		return 0;
	}
}

if ( ! function_exists( 'buying_points_for_simple_product' ) ) {

	function buying_points_for_simple_product() {
		global $post;
		if ( ! is_object( $post ) ) {
			return;
		}

		if ( block_points_for_salepriced_product( $post->ID, 0 ) == 'yes' ) {
			return;
		}

		$ProductObj = srp_product_object( $post->ID );
		if ( ! is_object( $ProductObj ) ) {
			return;
		}

		if ( is_shop() || is_product() || is_page() || is_product_category() || is_tax( 'pwb-brand' ) ) {
			if ( ( srp_product_type( $post->ID ) == 'simple' || ( srp_product_type( $post->ID ) == 'subscription' ) || srp_product_type( $post->ID ) == 'bundle' ) ) {
				$item   = array( 'qty' => '1' );
				$Points = get_post_meta( $post->ID, '_rewardsystem_assign_buying_points', true );
				$Points = get_current_user_id() > 0 ? RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $Points ) : (float) $Points;
				return $Points;
			}
		}
		return 0;
	}
}

if ( ! function_exists( 'srp_enable_reward_program' ) ) {

	function srp_enable_reward_program( $userid ) {
		if ( 'yes' == get_option( 'rs_enable_reward_program' ) ) {
			if ( isset( $_REQUEST['rs_enable_earn_points_for_user_in_reg_form'] ) || isset( $_REQUEST['enable_reward_prgm'] ) ) {
				update_user_meta( $userid, 'allow_user_to_earn_reward_points', 'yes' );
				update_user_meta( $userid, 'unsub_value', 'no' );

				/**
				 * This hook is used to do extra action when user involved in Reward Program.
				 *
				 * @param int $userid User ID.
				 * @since 29.4
				 */
				do_action( 'fp_rs_reward_program_enabled', $userid );
			} else {
				update_user_meta( $userid, 'allow_user_to_earn_reward_points', 'no' );
			}
		}
	}

	add_action( 'user_register', 'srp_enable_reward_program', 10, 1 );
}

if ( ! function_exists( 'check_referral_count_if_exist' ) ) {

	function check_referral_count_if_exist( $userid ) {
		if ( 'yes' != get_option( 'rs_enable_referral_link_limit' ) ) {
			return true;
		}

		if ( '' == get_option( 'rs_referral_link_limit' ) ) {
			return true;
		}

		if ( '' == get_user_meta( $userid, 'referral_link_count_value', true ) ) {
			return true;
		}

		$default_value = (int) get_user_meta( $userid, 'referral_link_count_value', true );
		if ( $default_value >= get_option( 'rs_referral_link_limit' ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'update_product_count_for_social_action' ) ) {

	function update_product_count_for_social_action( $UserId, $MetaKey, $PostId ) {
		$ProductId[] = $PostId;
		$OldData     = (array) get_user_meta( $UserId, $MetaKey, true );
		if ( srp_check_is_array( $OldData ) ) {
			$ArrayFilter = array_filter( $OldData );
			if ( isset( $ArrayFilter[ gmdate( 'd/m/Y' ) ] ) ) {
				$DataToMerge                       = $ArrayFilter[ gmdate( 'd/m/Y' ) ];
				$MergedData                        = array_merge( $DataToMerge, $ProductId );
				$DataToUpdate[ gmdate( 'd/m/Y' ) ] = $MergedData;
				update_user_meta( $UserId, $MetaKey, $DataToUpdate );
			} else {
				$DataToUpdate[ gmdate( 'd/m/Y' ) ] = $ProductId;
				update_user_meta( $UserId, $MetaKey, $DataToUpdate );
			}
		} else {
			$DataToUpdate[ gmdate( 'd/m/Y' ) ] = $ProductId;
			update_user_meta( $UserId, $MetaKey, $DataToUpdate );
		}
	}
}

if ( ! function_exists( 'allow_points_for_social_action' ) ) {

	function allow_points_for_social_action( $UserId, $MetaKey, $EnableAction, $Count ) {
		if ( 'no' == $EnableAction ) {
			return true;
		}

		if ( empty( $Count ) ) {
			return true;
		}

		$TotalCount = (array) get_user_meta( $UserId, $MetaKey, true );
		if ( empty( $TotalCount ) ) {
			return true;
		}

		if ( ! isset( $TotalCount[ gmdate( 'd/m/Y' ) ] ) ) {
			return true;
		}

		$ProductCount = count( $TotalCount[ gmdate( 'd/m/Y' ) ] );
		if ( $ProductCount >= $Count ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'check_if_referral_is_restricted_based_on_history' ) ) {

	function check_if_referral_is_restricted_based_on_history() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( 'yes' != get_option( 'rs_enable_referral_link_generate_after_first_order' ) ) {
			return true;
		}

		global $wpdb;
		$OrderStatuses = get_option( 'rs_set_order_status_for_generate_link' );
		if ( empty( $OrderStatuses ) ) {
			return true;
		}

		$WCStatus       = array_keys( wc_get_order_statuses() );
		$reached_status = array();
		foreach ( $OrderStatuses as $OrderStatus ) {
			if ( ! in_array( $OrderStatus, $WCStatus ) ) {
				$reached_status[] = 'wc-' . $OrderStatus;
			}
		}
		$db       = &$wpdb;
		$OrderIds = $db->get_results(
			"SELECT posts.ID
                        FROM $db->posts as posts
                        LEFT JOIN {$db->postmeta} AS meta ON posts.ID = meta.post_id
                        WHERE   meta.meta_key       = '_customer_user'
                        AND     posts.post_status   IN ('" . implode( "','", $reached_status ) . "')
                        AND     meta_value          = '" . get_current_user_id() . "'
                ",
			ARRAY_A
		);

		if ( ! srp_check_is_array( $OrderIds ) ) {
			return false;
		}

		if ( '1' == get_option( 'rs_referral_link_generated_settings' ) ) {
			$Count      = count( $OrderIds );
			$Nooforders = (int) get_option( 'rs_getting_number_of_orders' );
			if ( empty( $Nooforders ) ) {
				return true;
			}

			if ( $Count >= $Nooforders ) {
				return true;
			}
		} elseif ( '2' == get_option( 'rs_referral_link_generated_settings' ) ) {
			$AmountSpent = (float) get_option( 'rs_number_of_amount_spent' );
			if ( empty( $AmountSpent ) ) {
				return true;
			}

			$OrderTotal = array();
			foreach ( $OrderIds as $OrderId ) {
				$OrderTotal[] = get_post_meta( $OrderId['ID'], '_order_total', true );
			}
			$TotalAmnt = srp_check_is_array( $OrderTotal ) ? array_sum( $OrderTotal ) : 0;
			if ( $TotalAmnt >= $AmountSpent ) {
				return true;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'global_variable_points' ) ) {

	function global_variable_points() {
		global $totalrewardpointsnew;
		global $totalrewardpoints_payment_plan;
		$ProductPlanPoints = srp_check_is_array( $totalrewardpoints_payment_plan ) ? ( array_sum( $totalrewardpoints_payment_plan ) ) : 0;
		$EarnPoints        = srp_check_is_array( $totalrewardpointsnew ) ? ( array_sum( $totalrewardpointsnew ) ) : 0;
		$EarnPoints        = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $EarnPoints );
		/**
				 * Hook:srp_buying_points_for_payment_plan_in_cart.
				 *
				 * @since 1.0
				 */
				$TotalPoints = ( $EarnPoints - $ProductPlanPoints ) - apply_filters( 'srp_buying_points_for_payment_plan_in_cart', 0 );

		return $TotalPoints;
	}
}

function point_price_based_on_conversion( $product_id ) {
	$product       = srp_product_object( $product_id );
	$product_price = srp_product_price( $product );
	return redeem_point_conversion( $product_price, get_current_user_id() );
}

if ( ! function_exists( 'total_points_for_current_purchase' ) ) {

	function total_points_for_current_purchase( $Total, $UserId ) {
		if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '2' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
			$CartTotalPoints = get_reward_points_based_on_cart_total( $Total, false, $UserId );
			$CartTotalPoints = RSMemberFunction::earn_points_percentage( $UserId, (float) $CartTotalPoints );
						/**
						 * Hook:srp_buying_points_in_cart.
						 *
						 * @since 1.0
						 */
			$Points = $CartTotalPoints + apply_filters( 'srp_buying_points_in_cart', 0 );
		} elseif ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '3' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
			$range_points = RSProductPurchaseFrontend::get_reward_point_for_range_based_type();
						/**
						 * Hook:srp_buying_points_in_cart.
						 *
						 * @since 1.0
						 */
			$Points = $range_points + apply_filters( 'srp_buying_points_in_cart', 0 );
		} else {
						/**
						 * Hook:srp_buying_points_in_cart.
						 *
						 * @since 1.0
						 */
			$Points = global_variable_points() + apply_filters( 'srp_buying_points_in_cart', 0 );
		}
		if ( 'yes' == get_option( 'rs_enable_first_purchase_reward_points' ) && $UserId ) {
			$OrderCount          = get_posts(
				array(
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => $UserId,
					'post_type'   => wc_get_order_types(),
					'post_status' => array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed' ),
				)
			);
			$FirstPurchasePoints = RSMemberFunction::earn_points_percentage( $UserId, (float) rs_get_first_purchase_point() );
			$Points              = ( 0 == count( $OrderCount ) ) ? ( $Points + $FirstPurchasePoints ) : $Points;
		}

		if ( 'yes' == get_option( 'rs_referral_activated' ) ) {
			$Points = $Points + RSFrontendAssets::get_referred_points_in_cart_and_checkout();
		}

		return $Points;
	}
}

if ( ! function_exists( 'rs_check_product_purchase_notice_for_variation' ) ) {

	function rs_check_product_purchase_notice_for_variation() {

		if ( 'no' == get_option( 'rs_product_purchase_activated', 'no' ) ) :
			return 'no';
		endif;

		$variation_level_for_logged_in = get_option( 'rs_show_hide_message_for_variable_in_single_product_page' );
		$variation_level_for_guest     = get_option( 'rs_show_hide_message_for_variable_in_single_product_page_guest' );
		$variation_earn_notice         = is_user_logged_in() ? get_option( 'rs_show_hide_message_for_variable_product' ) : get_option( 'rs_show_hide_message_variation_single_product_guest', 1 );
		$default_level_earn_notice     = get_option( 'rs_enable_display_earn_message_for_variation_single_product' );
		$variation_level_for_related   = get_option( 'rs_show_hide_message_for_shop_archive_variable_related_products' );

		if ( is_user_logged_in() ) :
			if ( '2' == $variation_level_for_logged_in && '2' == $variation_earn_notice && '2' == $default_level_earn_notice && '2' == $variation_level_for_related ) :
				return 'no';
			endif;
		elseif ( '2' == $variation_level_for_guest && '2' == $variation_earn_notice && '2' == $default_level_earn_notice && '2' == $variation_level_for_related ) :
				return 'no';
		endif;

		return 'yes';
	}
}

if ( ! function_exists( 'rs_check_referral_notice_variation' ) ) {

	function rs_check_referral_notice_variation() {

		if ( 'no' == get_option( 'rs_referral_activated', 'no' ) ) :
			return 'no';
		endif;

		if ( '2' == get_option( 'rs_show_hide_message_for_variable_product_referral' ) ) :
			return 'no';
		endif;

		return 'yes';
	}
}

if ( ! function_exists( 'rs_check_buying_points_notice_for_variation' ) ) {

	function rs_check_buying_points_notice_for_variation() {

		if ( 'no' === get_option( 'rs_buyingpoints_activated', 'no' ) ) :
			return 'no';
		endif;

		$buying_point_message_for_logged_in  = get_option( 'rs_show_hide_buy_point_message_for_variable_product' );
		$buying_point_earn_message           = get_option( 'rs_show_hide_buy_points_message_for_variable_in_product' );
		$buying_point_earn_message_for_guest = get_option( 'rs_show_hide_buy_point_message_for_variable_in_product_guest' );

		if ( is_user_logged_in() ) :
			if ( '2' === $buying_point_message_for_logged_in && '2' === $buying_point_earn_message ) :
				return 'no';
			endif;
		elseif ( '2' === $buying_point_earn_message_for_guest && '2' === $buying_point_earn_message ) :
				return 'no';
		endif;

		return 'yes';
	}
}

if ( ! function_exists( 'msg_for_reward_gateway' ) ) {

	function msg_for_reward_gateway( $checkout ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( 'earningonly' === check_banning_type( get_current_user_id() ) || 'both' === check_banning_type( get_current_user_id() ) ) {
			return;
		}

		$default_value = ( 'yes' === get_option( 'rs_disable_point_if_reward_points_gateway', 'no' ) ) ? array( 'reward_gateway' ) : array();
		/* Product Purchase restriction Notice on using Payment Gateways */
		if ( 'yes' === get_option( 'rs_product_purchase_activated', 'no' ) && srp_check_is_array( get_option( 'rs_select_payment_gateway_for_restrict_reward', $default_value ) ) ) {
			rs_add_notice( '', 'rsgatewaypointsmsg', '' );
		}

		if ( 'yes' !== get_option( 'rs_reward_action_activated', 'no' ) ) {
			return;
		}

		if ( '2' === get_option( 'rs_show_hide_message_payment_gateway_reward_points' ) ) {
			return;
		}

		$msg = get_option( 'rs_message_payment_gateway_reward_points' );

		/* Earn Notice on using Payment Gateways */
		rs_add_notice( $msg, 'rspgpoints', '' );
	}

	add_action( 'woocommerce_after_checkout_form', 'msg_for_reward_gateway' );
}

if ( ! function_exists( 'rs_add_notice' ) ) {

	function rs_add_notice( $message, $div_class = '', $span_class = '', $notice_type = 'notice' ) {

		if ( 'notice' === $notice_type ) {
			?>
			<div class="woocommerce-info <?php echo esc_attr( $div_class ); ?> ">
				<?php
				$html = sprintf( '<span class ="%s">%s</span>', esc_attr( $span_class ), wp_kses_post( $message ) );
				?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'check_if_user_already_purchase' ) ) {

	/**
	 * Check if user already has purchased the free product.
	 *
	 * @param int    $product_id Product Id.
	 * @param string $rule_id Rule Id.
	 * @param array  $purchased_product_list List of products.
	 */
	function check_if_user_already_purchase( $product_id, $rule_id, $purchased_product_list ) {
		if ( ! srp_check_is_array( $purchased_product_list ) ) {
			return true;
		}

		if ( ! isset( $purchased_product_list[ $rule_id ] ) ) {
			return true;
		}

		if ( ! srp_check_is_array( $purchased_product_list[ $rule_id ] ) ) {
			return true;
		}

		if ( ! in_array( $product_id, $purchased_product_list[ $rule_id ] ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'display_point_price_value' ) ) {

	function display_point_price_value( $Points, $Slash = false ) {

		$Label     = get_option( 'rs_label_for_point_value' );
		$separator = get_option( 'rs_separator_for_point_price' );

		if ( $Slash ) {
			$Label     = str_replace( '/', '', $Label );
			$separator = str_replace( '/', '', $separator );
		}

		if ( '1' === get_option( 'rs_sufix_prefix_point_price_label' ) ) {
			$PointPrice = ' ' . $separator . ' ' . $Label . '<span class="fp-srp-point-price">' . $Points . '</span>';
		} else {
			$PointPrice = ' ' . $separator . ' ' . $Points . '<span class="fp-srp-point-price">' . $Label . '</span>';
		}

		return $PointPrice;
	}
}
