<?php
/*
 * General Tab Setting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSGeneralTabSetting' ) ) {

	class RSGeneralTabSetting {

		public static function init() {
			add_action( 'rs_default_settings_fprsgeneral' , array( __CLASS__ , 'set_default_value' ) ) ;
			add_action( 'woocommerce_rs_settings_tabs_fprsgeneral' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab
			add_action( 'woocommerce_update_options_fprsgeneral' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system
			add_action( 'woocommerce_admin_field_ban_user_for_earning' , array( __CLASS__ , 'ban_user_for_earning' ) ) ;
			add_action( 'woocommerce_admin_field_ban_user_for_redeeming' , array( __CLASS__ , 'ban_user_for_redeeming' ) ) ;
			add_action( 'woocommerce_admin_field_user_purchase_history' , array( __CLASS__ , 'render_add_rule_for_purchase_history' ) ) ;
			add_action( 'woocommerce_admin_field_rs_user_role_dynamics' , array( __CLASS__ , 'render_earning_percentage_rule' ) ) ;
			add_action( 'woocommerce_admin_field_earning_conversion' , array( __CLASS__ , 'reward_system_earning_points_conversion' ) ) ;
			add_action( 'woocommerce_admin_field_redeeming_conversion' , array( __CLASS__ , 'reward_system_redeeming_points_conversion' ) ) ;
			add_action( 'woocommerce_admin_field_rs_refresh_button' , array( __CLASS__ , 'refresh_button_for_expired' ) ) ;
			add_action( 'fp_action_to_reset_settings_fprsgeneral' , array( __CLASS__ , 'reset_general_tab' ) ) ;
			add_filter( 'woocommerce_fprsgeneral_settings' , array( __CLASS__ , 'reward_system_add_settings_to_action' ) ) ;

			if ( class_exists( 'SUMOMemberships' ) ) {
				add_filter( 'woocommerce_fprsgeneral_settings' , array( __CLASS__ , 'add_field_for_membership_plan' ) ) ;
			}

			if ( class_exists( 'SUMOSubscriptions' ) ) {
				add_filter( 'woocommerce_fprsgeneral_settings' , array( __CLASS__ , 'add_custom_field_to_general_tab' ) ) ;
			}

			if ( class_exists( 'SUMORewardcoupons' ) ) {
				add_filter( 'woocommerce_fprsgeneral_settings' , array( __CLASS__ , 'setting_for_sumo_coupons' ) ) ;
			}

			add_action( 'rs_display_save_button_fprsgeneral' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fprsgeneral' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
		}

		public static function add_field_for_membership_plan( $settings ) {
			$updated_settings = array() ;
			$membership_level = sumo_get_membership_levels() ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && '_rs_membership_plan_reward_points' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
					$updated_settings[] = array(
						'name'   => __( 'Don\'t allow Earn Points when the user hasn\'t purchased any membership plan through SUMO Memberships' , 'rewardsystem' ) ,
						'desc'   => __( 'Don\'t allow Earn Points when the user hasn\'t purchased any membership plan through SUMO Memberships' , 'rewardsystem' ) ,
						'id'     => 'rs_enable_restrict_reward_points' ,
						'type'   => 'checkbox' ,
						'newids' => 'rs_enable_restrict_reward_points' ,
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Membership Plan based Earning Level' , 'rewardsystem' ) ,
						'desc'    => __( 'Enable this option to modify earning points based on membership plan' , 'rewardsystem' ) ,
						'id'      => 'rs_enable_membership_plan_based_reward_points' ,
						'std'     => 'yes' ,
						'default' => 'yes' ,
						'type'    => 'checkbox' ,
						'newids'  => 'rs_enable_membership_plan_based_reward_points' ,
							) ;
					foreach ( $membership_level as $key => $value ) {
						$updated_settings[] = array(
							'name'     => __( 'Reward Points Earning Percentage for ' . sanitize_text_field($value) , 'rewardsystem' ) ,
							'desc'     => __( 'Please Enter Percentage of Reward Points for ' . sanitize_text_field($value) , 'rewardsystem' ) ,
							'class'    => 'rewardpoints_membership_plan' ,
							'id'       => 'rs_reward_membership_plan_' . $key ,
							'std'      => '100' ,
							'default'  => '100' ,
							'type'     => 'text' ,
							'newids'   => 'rs_reward_membership_plan_' . $key ,
							'desc_tip' => true ,
								) ;
					}
				}
				$updated_settings[] = $section ;
			}
			return $updated_settings ;
		}

		public static function add_custom_field_to_general_tab( $settings ) {
			$updated_settings = array() ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && '_rs_subscription_settings' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
					$updated_settings[] = array(
						'name'   => __( 'Don\'t Award Points for Renewal Orders of SUMO Subscriptions' , 'rewardsystem' ) ,
						'desc'   => __( 'If You Enable this option, Reward Points for Renewal orders will not be awarded.' , 'rewardsystem' ) ,
						'id'     => 'rs_award_point_for_renewal_order' ,
						'std'    => 'no' ,
						'type'   => 'checkbox' ,
						'newids' => 'rs_award_point_for_renewal_order' ,
							) ;
					$updated_settings[] = array(
						'name'   => __( 'Don\'t Award Referral Product Purchase Points for Renewal Orders of SUMO Subscriptions' , 'rewardsystem' ) ,
						'desc'   => __( 'If You Enable this option, Referral Product Purchase Points for Renewal orders will not be awarded.' , 'rewardsystem' ) ,
						'id'     => 'rs_award_referral_point_for_renewal_order' ,
						'std'    => 'no' ,
						'type'   => 'checkbox' ,
						'newids' => 'rs_award_referral_point_for_renewal_order' ,
							) ;
				}
				$updated_settings[] = $section ;
			}

			return $updated_settings ;
		}

		public static function setting_for_sumo_coupons( $settings ) {
			$updated_settings = array() ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && '_rs_coupon_settings' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
					$updated_settings[] = array(
						'name'    => __( 'Don\'t allow Earn Points when SUMO Coupon is applied' , 'rewardsystem' ) ,
						'desc'    => __( ' Don\'t allow Earn Points when SUMO Coupon is applied' , 'rewardsystem' ) ,
						'id'      => '_rs_not_allow_earn_points_if_sumo_coupon' ,
						'css'     => 'min-width:550px;' ,
						'type'    => 'checkbox' ,
						'std'     => 'no' ,
						'default' => 'no' ,
						'newids'  => '_rs_not_allow_earn_points_if_sumo_coupon' ,
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Don\'t allow Redeem when SUMO Coupon is applied' , 'rewardsystem' ) ,
						'desc'    => __( 'Don\'t allow Redeem when SUMO Coupon is applied' , 'rewardsystem' ) ,
						'id'      => 'rs_dont_allow_redeem_if_sumo_coupon' ,
						'css'     => 'min-width:550px;' ,
						'type'    => 'checkbox' ,
						'std'     => 'no' ,
						'default' => 'no' ,
						'newids'  => 'rs_dont_allow_redeem_if_sumo_coupon' ,
							) ;
				}
				$updated_settings[] = $section ;
			}
			return $updated_settings ;
		}

		public static function reward_system_admin_fields() {
			$GetUserRoleList  = fp_user_roles() ;
			$ListofRoles      = array_merge( $GetUserRoleList , array( 'guest' => 'Guest' ) ) ;
			$newcombinedarray = fp_order_status() ;
			return apply_filters( 'woocommerce_fprsgeneral_settings' , array(
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'General Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_general_setting' ,
				) ,
				array(
					'type' => 'rs_refresh_button' ,
				) ,
				array(
					'name'     => __( 'Plugin Menu Display Name' , 'rewardsystem' ) ,
					'desc'     => __( 'This name will be used to identify SUMO Reward Settings in Wordpress Dashboard' , 'rewardsystem' ) ,
					'id'       => 'rs_brand_name' ,
					'class'    => 'rs_brand_name' ,
					'std'      => 'SUMO Reward Points' ,
					'default'  => 'SUMO Reward Points' ,
					'desc_tip' => true ,
					'newids'   => 'rs_brand_name' ,
					'type'     => 'text' ,
				) ,
				array(
					'name'    => __( 'Round Off Type' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_round_off_type_for_calculation' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_enable_round_off_type_for_calculation' ,
					'desc'    => __( 'By enabling this checkbox, points will be earned based on the option configured in Round Off Points Settings. For Redeeming Points, displaying of redeemed reward points will not be in control of Round Off Points Settings.' , 'rewardsystem' ) ,
				) ,
				array(
					'name'     => __( 'Round Off Type[Applicable only for Points]' , 'rewardsystem' ) ,
					'id'       => 'rs_round_off_type' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'select' ,
					'options'  => array(
						'1' => __( '2 Decimal Places' , 'rewardsystem' ) ,
						'2' => __( 'Whole Number' , 'rewardsystem' ) ,
					) ,
					'newids'   => 'rs_round_off_type' ,
					'desc'     => __( 'Points will be displayed based on the option selected here and Decimal Separator for Points should obtain from [or] Roundup/Round Down settings.' , 'rewardsystem' ) ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Round Off Type[Applicable only for Currency] ' , 'rewardsystem' ) ,
					'id'       => 'rs_roundoff_type_for_currency' ,
					'css'      => 'min-width:150px;' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'select' ,
					'options'  => array(
						'1' => __( '2 Decimal Places' , 'rewardsystem' ) ,
						'2' => __( 'Whole Number' , 'rewardsystem' ) ,
					) ,
					'newids'   => 'rs_roundoff_type_for_currency' ,
					'desc'     => __( 'A currency[points equivalent value] will be displayed based on the option selected here and Decimal Separator for Currency should obtain from [or] Roundup/Round Down settings.<br><b>Note:</b>This settings is only for displaying purpose.' , 'rewardsystem' ) ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Decimal Separator for Currency should obtain from' , 'rewardsystem' ) ,
					'id'      => 'rs_decimal_seperator_check_for_currency' ,
					'css'     => 'min-width:150px;' ,
					'std'     => '2' ,
					'default' => '2' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Plugin Settings' , 'rewardsystem' ) ,
						'2' => __( 'WooCommerce Settings' , 'rewardsystem' ) ,
					) ,
					'newids'  => 'rs_decimal_seperator_check_for_currency' ,
				) ,
				array(
					'name'    => __( 'Roundup/Rounddown' , 'rewardsystem' ) ,
					'id'      => 'rs_round_up_down' ,
					'css'     => 'min-width:150px;' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Floor' , 'rewardsystem' ) ,
						'2' => __( 'Ceil' , 'rewardsystem' ) ,
					) ,
					'newids'  => 'rs_round_up_down' ,
				) ,
				array(
					'name'    => __( 'Number of decimals for Points should obtain from' , 'rewardsystem' ) ,
					'id'      => 'rs_decimal_seperator_check' ,
					'css'     => 'min-width:150px;' ,
					'std'     => '2' ,
					'default' => '2' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Plugin Settings' , 'rewardsystem' ) ,
						'2' => __( 'WooCommerce Settings' , 'rewardsystem' ) ,
					) ,
					'newids'  => 'rs_decimal_seperator_check' ,
				) ,
				array(
					'name'     => __( 'Date and Time Format Type' , 'rewardsystem' ) ,
					'id'       => 'rs_dispaly_time_format' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'newids'   => 'rs_dispaly_time_format' ,
					'type'     => 'select' ,
					'options'  => array(
						'1' => __( 'Default' , 'rewardsystem' ) ,
						'2' => __( 'WordPress Format' , 'rewardsystem' ) ,
					) ,
					'desc_tip' => true ,
					'desc'     => __( 'If Default is selected as Date and Time Format Type, then the date and time should be displayed as d-m-Y h:i:s A. If WordPress Format is selected, then the date and time format in WordPress settings is consider as date and time format' , 'rewardsystem' ) ,
				) ,
				array(
					'name'    => __( 'Hide Time Format' , 'rewardsystem' ) ,
					'id'      => 'rs_hide_time_format' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'newids'  => 'rs_hide_time_format' ,
					'type'    => 'checkbox' ,
					'desc'    => __( 'By enabling this option, time should be hidden from the date in My Reward Table.' , 'rewardsystem' ) ,
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_general_setting' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Earning Points Conversion Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_point_conversion' ,
					'desc' => __( 'This Conversion settings controls how much points can be earned if Reward Type is set as "By Percentage of Product Price"' , 'rewardsystem' )
				) ,
				array(
					'type' => 'earning_conversion' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_point_conversion' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Redeeming Points Conversion Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_redeem_point_conversion' ,
					'desc' => __( 'This conversion settings controls how much discount can be obtained by redeeming the available Reward Points' , 'rewardsystem' )
				) ,
				array(
					'type' => 'redeeming_conversion' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_redeem_point_conversion' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_subscription_compatible_start' ,
				) ,
				array(
					'name' => __( 'SUMO Subscriptions Compatability Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_subscription_settings' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_subscription_settings' ) ,
				array(
					'type' => 'rs_subscription_compatible_end' ,
				) ,
				array(
					'type' => 'rs_coupon_compatible_start' ,
				) ,
				array(
					'name' => __( 'SUMO Coupons Compatability Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_coupon_settings'
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_coupon_settings' ) ,
				array(
					'type' => 'rs_coupon_compatible_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Reward Points Order Status Settings for Earning' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_product_purchase_status_settings' ,
				) ,
				array(
					'name'     => __( 'Reward Points will be awarded when Order Status reaches' , 'rewardsystem' ) ,
					'desc'     => __( 'Points will award only when the order status matches with any one of the statuses selected in this field & the earned points for the corresponding order will revise from the account when the status change to any other that is not selected in this field.
<br><br>
<b>Example:</b><br>
Selected only "Processing" status in this field so that points will award once the order status reached to processing. The given points will be revised from the account when changed to any other status(ex. Completed/Canceled).' , 'rewardsystem' ) ,
					'id'       => 'rs_order_status_control' ,
					'std'      => array( 'processing','completed' ) ,
					'default'  => array( 'processing','completed' ) ,
					'type'     => 'multiselect' ,
					'options'  => $newcombinedarray ,
					'newids'   => 'rs_order_status_control' ,
					'desc_tip' => false ,
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_product_purchase_status_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Reward Points Earning Threshold Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_restriction_setting' ,
				) ,
				array(
					'name'    => __( 'Maximum Threshold for Accumulating Reward Points' , 'rewardsystem' ) ,
					'desc'    => __( 'Enable this option to provide restriction on Accumulating Reward Points without using it' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_disable_max_earning_points_for_user' ,
					'type'    => 'checkbox' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'newids'  => 'rs_enable_disable_max_earning_points_for_user' ,
				) ,
				array(
					'name'     => __( 'Maximum Threshold value in Points' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter a Fixed or Decimal Number greater than 0' , 'rewardsystem' ) ,
					'id'       => 'rs_max_earning_points_for_user' ,
					'desc_tip' => true ,
					'newids'   => 'rs_max_earning_points_for_user' ,
					'type'     => 'text' ,
				) ,
				array(
					'name'    => __( 'Message' , 'rewardsystem' ) ,
					'id'      => 'rs_maximum_threshold_error_message' ,
					'class'   => 'rs_maximum_threshold_error_message' ,
					'std'     => 'Maximum Threshold Limit is <b>[threshold_value]</b>. Hence, you cannot earn points more than <b>[threshold_value]</b>' ,
					'default' => 'Maximum Threshold Limit is <b>[threshold_value]</b>. Hence, you cannot earn points more than <b>[threshold_value]</b>' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_maximum_threshold_error_message' ,
				) ,
				array(
					'name'    => __( 'Email Notification to the user(s) when reaching Maximum Threshold Value' , 'rewardsystem' ) ,
					'id'      => 'rs_mail_for_reaching_maximum_threshold' ,
					'class'   => 'rs_mail_for_reaching_maximum_threshold' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_mail_for_reaching_maximum_threshold' ,
				) ,
				array(
					'name'    => __( 'Email Subject' , 'rewardsystem' ) ,
					'id'      => 'rs_mail_subject_for_reaching_maximum_threshold' ,
					'class'   => 'rs_mail_subject_for_reaching_maximum_threshold' ,
					'std'     => 'Maximum Threshold Reached - Notification' ,
					'default' => 'Maximum Threshold Reached - Notification' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_mail_subject_for_reaching_maximum_threshold' ,
				) ,
				array(
					'name'    => __( 'Email Message' , 'rewardsystem' ) ,
					'id'      => 'rs_mail_message_for_reaching_maximum_threshold' ,
					'class'   => 'rs_mail_message_for_reaching_maximum_threshold' ,
					'std'     => 'You have reached the maximum threshold value [maximum_threshold]. By redeeming the points which you have earned on the site, you can earn points by performing upcoming actions. Your Available Points is [availablepoints].' ,
					'default' => 'You have reached the maximum threshold value [maximum_threshold]. By redeeming the points which you have earned on the site, you can earn points by performing upcoming actions. Your Available Points is [availablepoints].' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_mail_message_for_reaching_maximum_threshold' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_restriction_setting' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Reward Points Earning Member Level Priority Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_member_level_setting' ,
					'desc' => __( 'This option controls which earning percentage should apply for the user if more than one  earning percentage is applicable for that user' , 'rewardsystem' )
				) ,
				array(
					'name'     => __( 'Priority Level Selection' , 'rewardsystem' ) ,
					'desc'     => __( 'If more than one type(level) is enabled then use the highest/lowest percentage' , 'rewardsystem' ) ,
					'id'       => 'rs_choose_priority_level_selection' ,
					'class'    => 'rs_choose_priority_level_selection' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'radio' ,
					'newids'   => 'rs_choose_priority_level_selection' ,
					'options'  => array(
						'1' => __( 'Use the level that gives highest percentage' , 'rewardsystem' ) ,
						'2' => __( 'Use the level that gives lowest percentage' , 'rewardsystem' ) ,
					) ,
					'desc_tip' => true ,
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_member_level_setting' , 'class' => 'rs_member_level_setting' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Reward Points Earning Percentage based on User Role' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_user_role_reward_points' ,
				) ,
				array(
					'name'    => __( 'User Role based Earning Level' , 'rewardsystem' ) ,
					'desc'    => __( 'Enable this option to modify reward points earning percentage based on user role' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_user_role_based_reward_points' ,
					'std'     => 'yes' ,
					'default' => 'yes' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_enable_user_role_based_reward_points' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_user_role_reward_points' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Reward Points Earning Percentage based on Earned Points' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_member_level_earning_points' ,
				) ,
				array(
					'name'    => __( 'Earned Points based on Earning Level' , 'rewardsystem' ) ,
					'desc'    => __( 'Enable this option to modify earning points based on earned points' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_earned_level_based_reward_points' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_enable_earned_level_based_reward_points' ,
				) ,
				array(
					'name'    => __( 'Earned Points is decided' , 'rewardsystem' ) ,
					'id'      => 'rs_select_earn_points_based_on' ,
					'std'     => '1' ,
					'type'    => 'select' ,
					'newids'  => 'rs_select_earn_points_based_on' ,
					'options' => array(
						'1' => __( 'Based on Total Earned Points' , 'rewardsystem' ) ,
						'2' => __( 'Based on Current Points' , 'rewardsystem' ) ) ,
				) ,
				array(
					'name'    => __( 'New Member Level will be awarded' , 'rewardsystem' ) ,
					'id'      => 'rs_free_product_range' ,
					'std'     => '1' ,
					'type'    => 'select' ,
					'newids'  => 'rs_free_product_range' ,
					'options' => array(
						'1' => __( 'Before reaching specified Reward Points' , 'rewardsystem' ) ,
						'2' => __( 'After reaching specified Reward Points' , 'rewardsystem' ) ) ,
				) ,
				array(
					'name'    => __( 'Free Product should be' , 'rewardsystem' ) ,
					'id'      => 'rs_free_product_add_by_user_or_admin' ,
					'std'     => '1' ,
					'type'    => 'select' ,
					'newids'  => 'rs_free_product_add_by_user_or_admin' ,
					'options' => array(
						'1' => __( 'Purchased by User' , 'rewardsystem' ) ,
						'2' => __( 'Added to User Account Automatically' , 'rewardsystem' ) ) ,
				) ,
				array(
					'name'     => __( 'Free Product Quantity Selection' , 'rewardsystem' ) ,
					'id'       => 'rs_free_product_add_quantity' ,
					'std'      => '1' ,
					'type'     => 'select' ,
					'newids'   => 'rs_free_product_add_quantity' ,
					'options'  => array(
						'1' => __( 'Default' , 'rewardsystem' ) ,
						'2' => __( 'Quantity Updation' , 'rewardsystem' )
					) ,
					'desc'     => __( ' If default is selected, then quantity for a free product cannot be updated. If Quantity Updation is selected, the user can get the specified quantity for free products. If a user updated the quantity higher than a specified value, they have to buy using the amount for those additional quantities.' , 'rewardsystem' ) ,
					'desc_tip' => true
				) ,
				array(
					'name'              => __( 'Enter the Quantity' , 'rewardsystem' ) ,
					'id'                => 'rs_free_product_quantity' ,
					'std'               => '2' ,
					'default'           => '2' ,
					'desc_tip'          => true ,
					'newids'            => 'rs_free_product_quantity' ,
					'custom_attributes' => array(
						'min' => '0'
					) ,
					'type'              => 'number' ,
				) ,
				array(
					'name'     => __( 'Free Product will be added to the User Account when Order Status reaches' , 'rewardsystem' ) ,
					'id'       => 'rs_order_status_control_to_automatic_order' ,
					'std'      => 'processing' ,
					'default'  => 'processing' ,
					'type'     => 'select' ,
					'options'  => $newcombinedarray ,
					'newids'   => 'rs_order_status_control_to_automatic_order' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Email Subject' , 'rewardsystem' ) ,
					'id'      => 'rs_subject_for_free_product_mail' ,
					'std'     => 'Free Product Earned from [sitename]' ,
					'default' => 'Free Product Earned from [sitename]' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_subject_for_free_product_mail' ,
					'class'   => 'rs_subject_for_free_product_mail' ,
				) ,
				array(
					'name'    => __( 'Email Message' , 'rewardsystem' ) ,
					'id'      => 'rs_content_for_free_product_mail' ,
					'std'     => 'You have got this product for reaching [current_level_points] Reward Points [rsorderlink]' ,
					'default' => 'You have got this product for reaching [current_level_points] Reward Points [rsorderlink]' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_content_for_free_product_mail' ,
					'class'   => 'rs_content_for_free_product_mail' ,
				) ,
				array(
					'name'    => __( 'Email Notification for Admin' , 'rewardsystem' ) ,
					'desc'    => __( 'Enable this checkbox to send an email to admin when a user gets a free product' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_admin_email_for_free_product' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_enable_admin_email_for_free_product' ,
				) ,
				array(
					'name'    => __( 'Subject' , 'rewardsystem' ) ,
					'id'      => 'rs_subject_for_free_product_mail_send_admin' ,
					'std'     => 'Free Product - Notification' ,
					'default' => 'Free Product - Notification' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_subject_for_free_product_mail_send_admin' ,
					'class'   => 'rs_subject_for_free_product_mail_send_admin' ,
				) ,
				array(
					'name'    => __( 'Message' , 'rewardsystem' ) ,
					'id'      => 'rs_content_for_free_product_mail_send_admin' ,
					'std'     => 'Hi,<br/> Your user has got the product as free for reaching the configured level. Please check the below details,<br/> Username: [username]<br/>Product Name: [product_id]<br/>Level Name: [current_level_name].<br/>Thanks' ,
					'default' => 'Hi,<br/> Your user has got the product as free for reaching the configured level. Please check the below details,<br/> Username: [username]<br/>Product Name: [product_id]<br/>Level Name: [current_level_name].<br/>Thanks' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_content_for_free_product_mail_send_admin' ,
					'class'   => 'rs_content_for_free_product_mail_send_admin' ,
				) ,
				array(
					'name'    => __( 'Email Notification for Admin - Bonus Points', 'rewardsystem' ),
					'desc'    => __( 'Enable this checkbox to send an email to admin when a user gets a bonus point' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_admin_email_for_bonus_points',
					'std'     => 'no',
					'default' => 'no',
					'type'    => 'checkbox',
					'newids'  => 'rs_enable_admin_email_for_bonus_points',
				),
				array(
					'name'    => __( 'Subject', 'rewardsystem' ),
					'id'      => 'rs_subject_for_bonus_points_admin_email',
					'std'     => 'Bonus Points - Notification',
					'default' => 'Bonus Points - Notification',
					'type'    => 'textarea',
					'newids'  => 'rs_subject_for_bonus_points_admin_email',
					'class'   => 'rs_subject_for_bonus_points_admin_email',
				),
				array(
					'name'    => __( 'Message', 'rewardsystem' ),
					'id'      => 'rs_message_for_bonus_points_admin_email',
					'std'     => 'Hi,<br/><br/> Your user <b>[username]</b> has received <b>[points_value]</b> bonus points for reaching the <b>[level_name]</b> level.<br/><br/>Thanks',
					'default' => 'Hi,<br/><br/> Your user <b>[username]</b> has received <b>[points_value]</b> bonus points for reaching the <b>[level_name]</b> level.<br/><br/>Thanks',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_bonus_points_admin_email',
					'class'   => 'rs_message_for_bonus_points_admin_email',
				),
				array(
					'name'    => __( 'Email Notification for Customer - Bonus Points', 'rewardsystem' ),
					'desc'    => __( 'Enable this checkbox to send an email to customer when they receive bonus for reaching the level' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_user_email_for_bonus_points',
					'std'     => 'no',
					'default' => 'no',
					'type'    => 'checkbox',
					'newids'  => 'rs_enable_user_email_for_bonus_points',
				),
				array(
					'name'    => __( 'Subject', 'rewardsystem' ),
					'id'      => 'rs_subject_for_bonus_points_customer_email',
					'std'     => 'Bonus Points - Notification',
					'default' => 'Bonus Points - Notification',
					'type'    => 'textarea',
					'newids'  => 'rs_subject_for_bonus_points_customer_email',
					'class'   => 'rs_subject_for_bonus_points_customer_email',
				),
				array(
					'name'    => __( 'Message', 'rewardsystem' ),
					'id'      => 'rs_message_for_bonus_points_customer_email',
					'std'     => 'Hi [username],<br/><br/> You have received <b>[points_value]</b> bonus point for reaching the [level_name] level.<br/><br/>Thanks',
					'default' => 'Hi [username],<br/><br/> You have received <b>[points_value]</b> bonus point for reaching the [level_name] level.<br/><br/>Thanks',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_bonus_points_customer_email',
					'class'   => 'rs_message_for_bonus_points_customer_email',
				),
				array(
					'type' => 'rs_user_role_dynamics',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_member_level_earning_points' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Reward Points Earning Percentage based on Purchase History', 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_member_level_earning_points_purchase_history',
				),
				array(
					'name'    => __( 'Purchase History based on Earning Level', 'rewardsystem' ),
					'desc'    => __( 'Enable this option to modify earning points based on Purchase History', 'rewardsystem' ),
					'id'      => 'rs_enable_user_purchase_history_based_reward_points',
					'css'     => 'min-width:150px;',
					'std'     => 'no',
					'default' => 'no',
					'type'    => 'checkbox',
					'newids'  => 'rs_enable_user_purchase_history_based_reward_points',
				),
				array(
					'name'    => __( 'New Member Level will be awarded', 'rewardsystem' ),
					'id'      => 'rs_product_purchase_history_range',
					'std'     => '1',
					'type'    => 'select',
					'newids'  => 'rs_product_purchase_history_range',
					'options' => array(
						'1' => __( 'Before reaching specified Value' , 'rewardsystem' ) ,
						'2' => __( 'After reaching specified Value' , 'rewardsystem' ) ) ,
				) ,
				array(
					'name'     => __( 'Reward Points Earning Percentage based on Order Status' , 'rewardsystem' ) ,
					'desc'     => __( 'Here you can set Reward Points Earning Percentage based on which Status of Order' , 'rewardsystem' ) ,
					'id'       => 'rs_earning_percentage_order_status_control' ,
					'std'      => array( 'completed' ) ,
					'default'  => array( 'completed' ) ,
					'type'     => 'multiselect' ,
					'options'  => $newcombinedarray ,
					'newids'   => 'rs_earning_percentage_order_status_control' ,
					'desc_tip' => true ,
				) ,
				array(
					'type' => 'user_purchase_history' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_member_level_earning_points_purchase_history' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Email Settings For Actions' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_email_settings_for_action' ,
				) ,
				array(
					'name'    => __( 'Select Email Function' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_email_function_actions' ,
					'css'     => 'min-width:150px;' ,
					'std'     => '2' ,
					'default' => '2' ,
					'type'    => 'select' ,
					'newids'  => 'rs_enable_email_function_actions' ,
					'options' => array(
						'1' => __( 'mail()' , 'rewardsystem' ) ,
						'2' => __( 'wp_mail()' , 'rewardsystem' ) ) ,
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_email_settings_for_action' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_membership_compatible_start' ,
				) ,
				array(
					'name' => __( 'Reward Points Earning Percentage based on Membership Plan' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_membership_plan_reward_points' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_membership_plan_reward_points' ) ,
				array(
					'type' => 'rs_membership_compatible_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Member Level Message Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_member_level_message_settings' ,
				) ,
				array(
					'name'    => __( 'Message displayed for Free Products when product is added to cart(Default Type)' , 'rewardsystem' ) ,
					'id'      => 'rs_free_product_message_info' ,
					'std'     => 'You have got this product for reaching [current_level_points] Reward Points' ,
					'default' => 'You have got this product for reaching [current_level_points] Reward Points' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_free_product_message_info' ,
				) ,
				array(
					'name'    => __( 'Message displayed for the Quantity of Free Products can be applicable to update in cart(Quantity Updation Type)' , 'rewardsystem' ) ,
					'id'      => 'rs_free_product_quantity_message_info' ,
					'std'     => 'You have got this product for reaching [current_level_points] Reward Points. Also, you have the access to update up to [free_product_quantity] quantity of this product for free. If you update more than [free_product_quantity] quantity, then those will be purchased by the amount.' ,
					'default' => 'You have got this product for reaching [current_level_points] Reward Points. Also, you have the access to update up to [free_product_quantity] quantity of this product for free. If you update more than [free_product_quantity] quantity, then those will be purchased by the amount.' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_free_product_quantity_message_info' ,
				) ,
				array(
					'name'    => __( 'Free Product Label in Cart' , 'rewardsystem' ) ,
					'id'      => 'rs_free_product_msg_caption' ,
					'std'     => 'Free Product' ,
					'default' => 'Free Product' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_free_product_msg_caption' ,
				) ,
				array(
					'name'    => __( 'Display Free Product Message in Cart and Order Details Page' , 'rewardsystem' ) ,
					'id'      => 'rs_remove_msg_from_cart_order' ,
					'std'     => 'yes' ,
					'default' => 'yes' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_remove_msg_from_cart_order' ,
				) ,
				array(
					'name'    => __( 'Message for Balance Points to reach next Member Level shortcode' , 'rewardsystem' ) ,
					'id'      => 'rs_point_to_reach_next_level' ,
					'std'     => '[balancepoint] more Points to reach [next_level_name] Earning Level ' ,
					'default' => '[balancepoint] more Points to reach [next_level_name] Earning Level' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_point_to_reach_next_level' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_member_level_message_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Reward Points Restriction Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_ban_users' ,
				) ,
				array(
					'name'    => __( 'Earning Points' , 'rewardsystem' ) ,
					'desc'    => __( 'Restrict Users from Earning Points' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_banning_users_earning_points' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_enable_banning_users_earning_points' ,
				) ,
				array(
					'type' => 'ban_user_for_earning' ,
				) ,
				array(
					'name'        => __( 'Select the User Role(s)' , 'rewardsystem' ) ,
					'id'          => 'rs_banning_user_role_for_earning' ,
					'css'         => 'min-width:343px;' ,
					'std'         => '' ,
					'default'     => '' ,
					'placeholder' => 'Search for a User Role' ,
					'type'        => 'multiselect' ,
					'options'     => $ListofRoles ,
					'newids'      => 'rs_banning_user_role_for_earning' ,
					'desc_tip'    => false ,
				) ,
				array(
					'name'    => __( 'Redeeming Points' , 'rewardsystem' ) ,
					'desc'    => __( 'Restrict Users from Redeeming Points' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_banning_users_redeeming_points' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_enable_banning_users_redeeming_points' ,
				) ,
				array(
					'type' => 'ban_user_for_redeeming' ,
				) ,
				array(
					'name'        => __( 'Select the User Role(s)' , 'rewardsystem' ) ,
					'id'          => 'rs_banning_user_role_for_redeeming' ,
					'css'         => 'min-width:343px;' ,
					'std'         => '' ,
					'default'     => '' ,
					'placeholder' => 'Search for a User Role' ,
					'type'        => 'multiselect' ,
					'options'     => $ListofRoles ,
					'newids'      => 'rs_banning_user_role_for_redeeming' ,
					'desc_tip'    => false ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_ban_users' ) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_general_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Shortcodes used in Product Purchase Module' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_shortcode_in_member_level' ,
				) ,
				array(
					'type' => 'title' ,
					'desc' =>__('<b>[current_level_points]</b> - To display current level points<br><br>'
					. '<b>[balancepoint]</b> - Displays the reward points needed to reach next earning level<br><br>'
					. '<b>[paymentgatewaytitle]</b> - To display payment gateway title<br><br>'
					. '<b>[next_level_name]</b> - To display next earning level name<br><br>', 'rewardsystem')
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_shortcode_in_member_level' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
					) ) ;
		}

		/*
		 * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields function
		 */

		public static function reward_system_register_admin_settings() {
			woocommerce_admin_fields( self::reward_system_admin_fields() ) ;
		}

		/*
		 * Update the Settings on Save Changes may happen in SUMO Reward Points
		 */

		public static function reward_system_update_settings() {
			woocommerce_update_options( self::reward_system_admin_fields() ) ;
			if ( isset( $_REQUEST[ 'rs_banned_users_list_for_earning' ] ) ) {
				update_option( 'rs_banned_users_list_for_earning' , wc_clean(wp_unslash($_REQUEST[ 'rs_banned_users_list_for_earning' ] ))) ;
			} else {
				update_option( 'rs_banned_users_list_for_earning' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_banned_users_list_for_redeeming' ] ) ) {
				update_option( 'rs_banned_users_list_for_redeeming' , wc_clean(wp_unslash($_REQUEST[ 'rs_banned_users_list_for_redeeming' ] ) ));
			} else {
				update_option( 'rs_banned_users_list_for_redeeming' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_earn_point' ] ) && ( '' || 0 ) != wc_clean(wp_unslash($_REQUEST[ 'rs_earn_point' ])) ) {
				update_option( 'rs_earn_point' , wc_clean(wp_unslash($_REQUEST[ 'rs_earn_point' ])) ) ;
			} else {
				update_option( 'rs_earn_point' , '1' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_earn_point_value' ] ) && ( '' || 0 ) != wc_clean(wp_unslash($_REQUEST[ 'rs_earn_point_value' ])) ) {
				update_option( 'rs_earn_point_value' , wc_clean(wp_unslash($_REQUEST[ 'rs_earn_point_value' ])) ) ;
			} else {
				update_option( 'rs_earn_point_value' , '1' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_redeem_point' ] ) && ( '' || 0 ) != $_REQUEST[ 'rs_redeem_point' ] ) {
				update_option( 'rs_redeem_point' , wc_clean(wp_unslash($_REQUEST[ 'rs_redeem_point' ])) ) ;
			} else {
				update_option( 'rs_redeem_point' , '1' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_redeem_point_value' ] ) && ( '' || 0  != wc_clean(wp_unslash($_REQUEST[ 'rs_redeem_point_value' ])) ) ) {
				update_option( 'rs_redeem_point_value' , wc_clean(wp_unslash($_REQUEST[ 'rs_redeem_point_value' ] ))) ;
			} else {
				update_option( 'rs_redeem_point_value' , '1' ) ;
			}
			if ( isset( $_REQUEST[ 'rewards_dynamic_rule' ] ) ) {
				update_option( 'rewards_dynamic_rule' , wc_clean(wp_unslash($_REQUEST[ 'rewards_dynamic_rule' ] ) ));
			} else {
				update_option( 'rewards_dynamic_rule' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rewards_dynamic_rule_purchase_history' ] ) ) {
				update_option( 'rewards_dynamic_rule_purchase_history' , wc_clean(wp_unslash($_REQUEST[ 'rewards_dynamic_rule_purchase_history' ] ) ));
			} else {
				update_option( 'rewards_dynamic_rule_purchase_history' , '' ) ;
			}
		}

		public static function set_default_value() {
			foreach ( self::reward_system_admin_fields() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		/*
		 * Function to Select user for banning
		 */

		public static function ban_user_for_earning() {
			$field_id    = 'rs_banned_users_list_for_earning' ;
			$field_label = __('Select the User(s)', 'rewardsystem') ;
			$getuser     = get_option( 'rs_banned_users_list_for_earning' ) ;
			echo wp_kses_post(user_selection_field( $field_id , $field_label , $getuser ) );
		}

		public static function ban_user_for_redeeming() {
			$field_id    = 'rs_banned_users_list_for_redeeming' ;
			$field_label = __('Select the User(s)', 'rewardsystem') ;
			$getuser     = get_option( 'rs_banned_users_list_for_redeeming' ) ;
			echo wp_kses_post(user_selection_field( $field_id , $field_label , $getuser ) );
		}

		public static function reward_system_earning_points_conversion() {

			?>
			<tr valign="top">
				<td class="forminp forminp-text">
					<?php echo wp_kses_post(get_woocommerce_currency_symbol() ); ?> <input class="fp-srp-earn-points-value" type="number" step="any" min="0" value="<?php echo esc_attr(get_option( 'rs_earn_point_value' )) ; ?>" id="rs_earn_point_value" name="rs_earn_point_value">
					&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
					<input class="fp-srp-earn-points-value" type="number" step="any" min="0" value="<?php echo esc_attr(get_option( 'rs_earn_point' ) ); ?>" id="rs_earn_point" name="rs_earn_point"> <?php esc_html_e( 'Earning Point(s)' , 'rewardsystem' ) ; ?>
				</td>
			</tr>

			<?php
		}

		public static function reward_system_redeeming_points_conversion() {
			?>
			<tr valign="top">
				<td class="forminp forminp-text">
					<input type="number" step="any" min="0" value="<?php echo esc_attr(get_option( 'rs_redeem_point' )) ; ?>" class="fp-srp-redeem-points-value" id="rs_redeem_point" name="rs_redeem_point"> <?php esc_html_e( 'Redeeming Point(s)' , 'rewardsystem' ) ; ?>
					&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
					<?php echo wp_kses_post(get_woocommerce_currency_symbol()) ; ?><input type="number" step="any" min="0" value="<?php echo esc_attr(get_option( 'rs_redeem_point_value' )) ; ?>" class="fp-srp-redeem-points-value" id="rs_redeem_point_value" name="rs_redeem_point_value"></td>
			</td>
			</tr>
			<?php
		}

		public static function refresh_button_for_expired() {
			?>
			<tr valign="top">
				<th>
					<label class="fp-srp-refresh-button" for="rs_refresh_button"><?php esc_html_e( 'Update Expired Points for All Users' , 'rewardsystem' ) ; ?></label>
				</th>
				<td>
					<input type="button" class="rs_refresh_button" value="<?php esc_html_e( 'Update Expired Points' , 'rewardsystem' ) ; ?>"  id="rs_refresh_button" name="rs_refresh_button"/>
				</td>
			</tr>
			<?php
		}

		public static function reset_general_tab() {
			$settings = self::reward_system_admin_fields() ;
			RSTabManagement::reset_settings( $settings ) ;
		}

		/*
		 * Render Earning Percentage rule.
		 */

		public static function render_earning_percentage_rule() {
			global $woocommerce ;
			wp_nonce_field( plugin_basename( __FILE__ ) , 'rsdynamicrulecreation' ) ;
			include (SRP_PLUGIN_PATH . '/includes/admin/views/earning-percentage-rule.php') ;
		}

		/*
		 * Render add rule for purchase history.
		 */

		public static function render_add_rule_for_purchase_history() {
			include (SRP_PLUGIN_PATH . '/includes/admin/views/user-purchase-history.php') ;
		}

		/*
		 * Function to add settings for Member Level in Member Level Tab
		 */

		public static function reward_system_add_settings_to_action( $settings ) {
			global $wp_roles ;
			$updated_settings = array() ;
			$mainvariable     = array() ;
			global $woocommerce ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && '_rs_user_role_reward_points' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
					foreach ( $wp_roles->role_names as $value => $key ) {
						$updated_settings[] = array(
							'name'     => __( 'Reward Points Earning Percentage for ' . sanitize_text_field($key) . ' User Role' , 'rewardsystem' ) ,
							'desc'     => __( 'Earning Percentage of Reward Points for ' . sanitize_text_field($key) . 'user role' , 'rewardsystem' ) ,
							'class'    => 'rewardpoints_userrole' ,
							'id'       => 'rs_reward_user_role_' . sanitize_text_field($value) ,
							'std'      => '100' ,
							'type'     => 'text' ,
							'newids'   => 'rs_reward_user_role_' . sanitize_text_field($value) ,
							'desc_tip' => true ,
								) ;
					}

					$updated_settings[] = array(
						'type' => 'sectionend' , 'id'   => '_rs_user_role_reward_points' ,
							) ;
				}

				$updated_settings[] = $section ;
			}

			return $updated_settings ;
		}

	}

	RSGeneralTabSetting::init() ;
}
