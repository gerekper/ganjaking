<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! function_exists( 'fp_user_roles' ) ) {

	function fp_user_roles() {
		global $wp_roles ;
		foreach ( $wp_roles->roles as $values => $key ) {
			$userroleslug[] = $values ;
			$userrolename[] = $key[ 'name' ] ;
		}
		return array_combine( ( array ) $userroleslug , ( array ) $userrolename ) ;
	}

}

function rs_get_next_menu() {
	if ( 'yes' == get_option( 'rs_menu_restriction_based_on_user_role' ) ) {
		$tabtoshow = RSAdminAssets::menu_restriction_based_on_user_role() ;
		return reset( $tabtoshow ) ;
	}
}

function rs_get_current_user_role() {
	global $wp_roles ;
	$UserRole = array() ;
	foreach ( $wp_roles->role_names as $value => $key ) {
		$user = new WP_User( get_current_user_id() ) ;
		if ( srp_check_is_array( $user->roles ) ) {
			$UserRole = $user->roles ;
		}
	}
	return $UserRole ;
}

if ( ! function_exists( 'get_earned_redeemed_points_message' ) ) {

	function get_earned_redeemed_points_message( $orderid, $earned_and_redeemed_point = false ) {
		$OrderObj = wc_get_order( $orderid ) ;
		if ( ! is_object( $OrderObj ) ) {
			return ;
		}

		$OrderObj = srp_order_obj( $OrderObj ) ;
		$UserId   = $OrderObj[ 'order_userid' ] ;
		if ( empty( $UserId ) ) {
			return ;
		}

		global $wpdb ;
		$Message            = array() ;
		$EarnedTotal        = array() ;
		$RedeemTotal        = array() ;
		$RevisedEarnTotal   = array() ;
		$RevisedRedeemTotal = array() ;
		$Status             = $OrderObj[ 'order_status' ] ;
		$OrderStatus        = str_replace( 'wc-' , '' , $Status ) ;
		$TotalEarnPoints    = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints FROM {$wpdb->prefix}rsrecordpoints WHERE orderid = %d and userid = %d and checkpoints != 'RVPFRP'and  checkpoints != 'RVPFRPG' and checkpoints != 'RRP'" , $orderid , $UserId ) , ARRAY_A ) ;
		$ReplacedPoints     = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints FROM {$wpdb->prefix}rsrecordpoints WHERE orderid = %d AND reasonindetail = 'Replaced'" , $orderid ) , ARRAY_A ) ;
		$TotalEarnPoints    = ( srp_check_is_array( $ReplacedPoints ) ) ? $ReplacedPoints : $TotalEarnPoints ;
		foreach ( $TotalEarnPoints as $EarnPoints ) {
			$EarnedTotal[] = $EarnPoints[ 'earnedpoints' ] ;
		}
		$TotalRedeemPoints = $wpdb->get_results( $wpdb->prepare( "SELECT redeempoints FROM {$wpdb->prefix}rsrecordpoints WHERE orderid = %d and userid = %d and checkpoints != 'RVPFPPRP'" , $orderid , $UserId ) , ARRAY_A ) ;
		foreach ( $TotalRedeemPoints as $RedeemPoints ) {
			$RedeemTotal[] = $RedeemPoints[ 'redeempoints' ] ;
		}
		$TotalRevisedEarnPoints = $wpdb->get_results( $wpdb->prepare( "SELECT redeempoints FROM {$wpdb->prefix}rsrecordpoints WHERE checkpoints = 'RVPFPPRP' and userid = %d and orderid = %d" , $UserId , $orderid ) , ARRAY_A ) ;
		foreach ( $TotalRevisedEarnPoints as $RevisedEarnPoints ) {
			$RevisedEarnTotal[] = $RevisedEarnPoints[ 'redeempoints' ] ;
		}
		$TotalRevisedRedeemPoints = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints FROM {$wpdb->prefix}rsrecordpoints WHERE orderid = %d and userid = %d and checkpoints NOT IN ('PPRP','PPRRPG','RRP','RPG','RPBSRP','PPRPBCT','PPRRPGCT')" , $orderid , $UserId ) , ARRAY_A ) ;
		foreach ( $TotalRevisedRedeemPoints as $RevisedRedeemPoints ) {
			$RevisedRedeemTotal[] = $RevisedRedeemPoints[ 'earnedpoints' ] ;
		}
		if ( in_array( $OrderStatus , get_option( 'rs_order_status_control_redeem' ) ) ) {
			RSPointExpiry::update_redeem_point_for_user( $orderid ) ;
		}

		$totalredeemvalue = array_sum( $RedeemTotal ) - array_sum( $RevisedRedeemTotal ) ;
		$RedeemPointMsg   = ( 'yes' == get_option( 'rs_enable_msg_for_redeem_points' ) ) ? str_replace( '[redeempoints]' , round_off_type( $totalredeemvalue ) , get_option( 'rs_msg_for_redeem_points' ) ) : '' ;
		$totalearnedvalue = array_sum( $EarnedTotal ) - array_sum( $RevisedEarnTotal ) ;

		if ( $earned_and_redeemed_point ) {
			return array( $totalearnedvalue => $totalredeemvalue ) ;
		}

		$EarnPointMsg = ( 'yes' == get_option( 'rs_enable_msg_for_earned_points' ) ) ? str_replace( '[earnedpoints]' , round_off_type( $totalearnedvalue ) , get_option( 'rs_msg_for_earned_points' ) ) : '' ;

		$Message[ $EarnPointMsg ] = $RedeemPointMsg ;
		return $Message ;
	}

}

if ( ! function_exists( 'rs_get_payment_gateways' ) ) {

	function rs_get_payment_gateways() {
		$gateways           = array() ;
		$wc_gateways        = new WC_Payment_Gateways() ;
		$available_gateways = $wc_gateways->get_available_payment_gateways() ;

		foreach ( $available_gateways as $id => $gateway ) {
			$gateways[ $id ] = $gateway->get_title() ;
		}

		return $gateways ;
	}

}

if ( ! function_exists( 'rs_get_screen_option_names' ) ) {

	function rs_get_screen_option_names() {

		$option_names = array(
			'fpgiftvoucher' ,
			'fprsmasterlog' ,
			'fpnominee' ,
			'fpreferralsystem' ,
			'fprsuserrewardpoints' ,
			'fppointurl' ,
			'fpsendpoints' ,
			'fprsmodules'
				) ;

		return apply_filters( 'rs_screen_option_names' , $option_names ) ;
	}

}

if ( ! function_exists( 'rs_get_manual_referral_link_rule_count' ) ) {

	function rs_get_manual_referral_link_rule_count() {

		$per_page      = RSTabManagement::rs_get_value_for_no_of_item_perpage( get_current_user_id() , get_current_screen() ) ;
		// Search User filter.
		$searched_user = isset( $_REQUEST[ 'rs_search_user' ] ) ? sanitize_title( $_REQUEST[ 'rs_search_user' ] ) : '' ;
		if ( ! $searched_user ) {
			$current_page = isset( $_REQUEST[ 'page_no' ] ) ? wc_clean( wp_unslash( absint( $_REQUEST[ 'page_no' ] ) ) ) : '1' ;
		} else {
			$current_page = 1 ;
		}

		$manual_link_rules = apply_filters( 'rs_alter_manual_referral_link_rules' , ( array ) get_option( 'rewards_dynamic_rule_manual' , array() ) ) ;
		$rule_count        = count( $manual_link_rules ) + ( $per_page * $current_page ) - $per_page ;

		return $rule_count ;
	}

}

if ( ! function_exists( 'rs_list_of_tabs' ) ) {

	function rs_list_of_tabs() {

		return array(
			'fprsgeneral'          => 'General' ,
			'fprsmodules'          => 'Modules' ,
			'fprsaddremovepoints'  => 'Add/Remove Reward Points' ,
			'fprsmessage'          => 'Messages' ,
			'fprslocalization'     => 'Localization' ,
			'fprsuserrewardpoints' => 'User Reward Points' ,
			'fprsmasterlog'        => 'Master Log' ,
			'fprsshortcodes'       => 'Shortcode' ,
			'fprsadvanced'         => 'Advanced' ,
			'fprssupport'          => 'Support'
				) ;
	}

}

if ( ! function_exists( 'fp_srp_page_screen_ids' ) ) {

	/**
	 * Get the page screen IDs.
	 *
	 * @return array
	 */
	function fp_srp_page_screen_ids() {

		$wc_screen_id = sanitize_title( __( 'WooCommerce' , 'woocommerce' ) ) ;
		return apply_filters(
				'fp_srp_page_screen_ids' , array(
			$wc_screen_id . '_page_rewardsystem_callback' ,
			'dashboard_page_sumo-reward-points-welcome-page'
				)
				) ;
	}

}

if ( ! function_exists( 'birthday_field_in_user_form' ) ) {

	/**
	 * Display Birthday Field in User Form
	 */
	function birthday_field_in_user_form() {

		if ( ! isset( $_GET[ 'user_id' ] ) ) {
			return ;
		}

		$user_id = absint( $_GET[ 'user_id' ] ) ;

		$birthday_date = get_user_meta( $user_id , 'srp_birthday_date' , true ) ;

		include SRP_PLUGIN_PATH . '/includes/admin/views/birthday-field.php' ;
	}

	//Display Birthday Field in Add New User Form
	add_action( 'user_new_form' , 'birthday_field_in_user_form' ) ;
	//Display Birthday Field in Edit User Form
	add_action( 'show_user_profile' , 'birthday_field_in_user_form' ) ;
	//Display Birthday Field in Edit User Form
	add_action( 'edit_user_profile' , 'birthday_field_in_user_form' ) ;
}

if ( ! function_exists( 'get_paypal_id_form_cashback_form' ) ) {

	function get_paypal_id_form_cashback_form( $userid ) {
		if ( empty( $userid ) ) {
			return ;
		}

		global $wpdb ;
		$table_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sumo_reward_encashing_submitted_data WHERE userid=%d" , $userid ) , ARRAY_A ) ;
		foreach ( $table_data as $data ) {
			$data_to_return = ( 'encash_through_paypal_method' == $data[ 'encashpaymentmethod' ] ) ? $data[ 'paypalemailid' ] : $data[ 'otherpaymentdetails' ] ;
		}
		return $data_to_return ;
	}

}
	
