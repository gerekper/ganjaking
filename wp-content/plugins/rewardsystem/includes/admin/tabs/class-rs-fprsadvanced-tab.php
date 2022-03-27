<?php
/*
 * Advanced Tab
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSAdvancedSetting' ) ) {

	class RSAdvancedSetting {

		public static function init() {
			add_action( 'woocommerce_rs_settings_tabs_fprsadvanced' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

			add_action( 'woocommerce_update_options_fprsadvanced' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

			add_action( 'rs_default_settings_fprsadvanced' , array( __CLASS__ , 'set_default_value' ) ) ;

			add_action( 'fp_action_to_reset_settings_fprsadvanced' , array( __CLASS__ , 'reset_advanced_tab' ) ) ;

			add_action( 'woocommerce_admin_field_rs_add_old_version_points' , array( __CLASS__ , 'add_old_points_for_all_user' ) ) ;

			add_action( 'woocommerce_admin_field_previous_order_button_range' , array( __CLASS__ , 'rs_add_date_picker' ) ) ;

			add_action( 'woocommerce_admin_field_reward_page_menu_sorting' , array( __CLASS__ , 'reward_page_menu_sorting' ) ) ;

			add_action( 'woocommerce_admin_field_previous_order_button' , array( __CLASS__ , 'rs_apply_points_for_previous_order_button' ) ) ;

			add_filter( 'woocommerce_fprsadvanced_settings' , array( __CLASS__ , 'reward_system_add_settings_based_on_user_role' ) ) ;

			add_action( 'woocommerce_admin_field_rs_administrator_wrapper_start' , array( __CLASS__ , 'rs_wrapper_administrator_start' ) ) ;

			add_action( 'woocommerce_admin_field_rs_administrator_wrapper_end' , array( __CLASS__ , 'rs_wrapper_administrator_close' ) ) ;

			add_action( 'woocommerce_admin_field_rs_points_earned_in_specific_duration_shortcode' , array( __CLASS__ , 'rs_points_earned_in_specific_duration_shortcode' ) ) ;

			add_action( 'rs_display_save_button_fprsadvanced' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fprsadvanced' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
		}

		public static function reward_system_admin_fields() {
			return apply_filters( 'woocommerce_fprsadvanced_settings' , array(
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Advanced Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_advanced_setting' ,
				) ,
				array(
					'name'    => __( 'Reward Points Program' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_reward_program' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_enable_reward_program' ,
					'desc'    => __( 'By enabling this checkbox, new users can have the option to involve in the Reward Points Program on the site during registration. For old users, they will have the checkbox to exit from the reward points program on the account page. By default, it will be enabled for old users' , 'rewardsystem' ) ,
				) ,
				array(
					'name'    => __( 'Notification on My Account Page - Registration' , 'rewardsystem' ) ,
					'id'      => 'rs_msg_in_reg_page' ,
					'std'     => __( 'By selecting this checkbox, you will be involved in Reward Points Program where you can earn points for the actions such as Account Sign up, Product Purchase, Product Review, etc on the site and points earned can be used on future purchases' , 'rewardsystem' ) ,
					'default' => __( 'By selecting this checkbox, you will be involved in Reward Points Program where you can earn points for the actions such as Account Sign up, Product Purchase, Product Review, etc on the site and points earned can be used on future purchases' , 'rewardsystem' ) ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_msg_in_reg_page' ,
				) ,
				array(
					'name'    => __( 'Notification on My Account Page - Checked' , 'rewardsystem' ) ,
					'id'      => 'rs_msg_in_acc_page_when_checked' ,
					'std'     => __( 'By unchecking this checkbox, you will not be in part of Reward Points Program' , 'rewardsystem' ) ,
					'default' => __( 'By unchecking this checkbox, you will not be in part of Reward Points Program' , 'rewardsystem' ) ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_msg_in_acc_page_when_checked' ,
				) ,
				array(
					'name'    => __( 'Notification on My Account Page - Unchecked' , 'rewardsystem' ) ,
					'id'      => 'rs_msg_in_acc_page_when_unchecked' ,
					'std'     => __( 'By selecting this checkbox, you will be in part of Reward Points Program' , 'rewardsystem' ) ,
					'default' => __( 'By selecting this checkbox, you will be in part of Reward Points Program' , 'rewardsystem' ) ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_msg_in_acc_page_when_unchecked' ,
				) ,
				array(
					'name'    => __( 'Alert Message on My Account Page - Checked' , 'rewardsystem' ) ,
					'id'      => 'rs_alert_msg_in_acc_page_when_checked' ,
					'std'     => __( 'Are you sure you want to be part of the Reward Points Program?' , 'rewardsystem' ) ,
					'default' => __( 'Are you sure you want to be part of the Reward Points Program' , 'rewardsystem' ) ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_alert_msg_in_acc_page_when_checked' ,
				) ,
				array(
					'name'    => __( 'Alert Message on My Account Page - Unchecked' , 'rewardsystem' ) ,
					'id'      => 'rs_alert_msg_in_acc_page_when_unchecked' ,
					'std'     => __( 'Are you sure you want to exit the Reward Points Program?' , 'rewardsystem' ) ,
					'default' => __( 'Are you sure you want to exit the Reward Points Program?' , 'rewardsystem' ) ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_alert_msg_in_acc_page_when_unchecked' ,
				) ,
				array(
					'name'    => __( 'Tab section' , 'rewardsystem' ) ,
					'id'      => 'rs_expand_collapse' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'newids'  => 'rs_expand_collapse' ,
					'options' => array(
						'1' => __( 'Collapse all' , 'rewardsystem' ) ,
						'2' => __( 'Expand all' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Show/Hide Reset Button in Tabs' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_reset_all' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'newids'  => 'rs_show_hide_reset_all' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Admin Color Scheme' , 'rewardsystem' ) ,
					'id'      => 'rs_color_scheme' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'newids'  => 'rs_color_scheme' ,
					'options' => array(
						'1' => __( 'Dark' , 'rewardsystem' ) ,
						'2' => __( 'Light' , 'rewardsystem' ) ,
					) ,
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_advanced_setting' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Product Purchase Reward Points for Previous Orders' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_apply_reward_points' ,
				) ,
				array(
					'name'     => __( 'Award Product Purchase Reward Points for' , 'rewardsystem' ) ,
					'id'       => 'rs_sumo_select_order_range' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'select' ,
					'options'  => array(
						'1' => __( 'Any Old Orders' , 'rewardsystem' ) ,
						'2' => __( 'Orders Placed Between Specific Date Range' , 'rewardsystem' )
					) ,
					'newids'   => 'rs_sumo_select_order_range' ,
					'desc_tip' => true ,
				) ,
				array(
					'type' => 'previous_order_button_range' ,
				) ,
				array(
					'name'     => __( 'Previous Order(s) Points for' , 'rewardsystem' ) ,
					'id'       => 'rs_award_previous_order_points' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'select' ,
					'options'  => array(
						'1' => __( 'Order(s) in which points are not awarded for already purchased products' , 'rewardsystem' ) ,
						'2' => __( 'Based on Conversion Settings' , 'rewardsystem' )
					) ,
					'newids'   => 'rs_award_previous_order_points' ,
					'desc_tip' => true ,
				) ,
				array(
					'type' => 'previous_order_button' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_apply_reward_points' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'My Account Page Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_my_acccount_page_settings' ,
				) ,
				array(
					'name'    => __( 'Reward Points Fields in My Account Page' , 'rewardsystem' ) ,
					'desc'    => __( 'Enable this option to show the Reward Points Fields in My Account Page' , 'rewardsystem' ) ,
					'id'      => 'rs_reward_content' ,
					'type'    => 'checkbox' ,
					'std'     => 'yes' ,
					'default' => 'yes' ,
					'newids'  => 'rs_reward_content' ,
				) ,
				array(
					'name'    => __( 'Reward Points Fields in Shortcode' , 'rewardsystem' ) ,
					'desc'    => __( 'Enable this option to show the Reward Points Fields in Shortcode' , 'rewardsystem' ) ,
					'id'      => 'rs_reward_content_shortcode' ,
					'type'    => 'checkbox' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'newids'  => 'rs_reward_content_shortcode' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_my_acccount_page_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'My Rewards Menu Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_my_acccount_menu_page_show_hide_settings' ,
				) ,
				array(
					'name'    => __( 'Reward Points Fields in My Account Menu' , 'rewardsystem' ) ,
					'desc'    => __( 'Enable this option to show the Reward Points Fields in My Account Menu' , 'rewardsystem' ) ,
					'id'      => 'rs_reward_content_menu_page' ,
					'type'    => 'checkbox' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'newids'  => 'rs_reward_content_menu_page' ,
				) ,
				array(
					'name'    => __( 'Reward Points Fields display Name on My Account Page' , 'rewardsystem' ) ,
					'desc'    => __( 'Enter the Title for My Reward Content on My Account Menu Page' , 'rewardsystem' ) ,
					'id'      => 'rs_my_reward_content_title' ,
					'type'    => 'text' ,
					'std'     => 'My Reward' ,
					'default' => 'My Reward' ,
					'newids'  => 'rs_my_reward_content_title' ,
					'desc_tip'=> true,
				) ,
				array(
					'name'    => __( 'Reward Points Fields URL Name on My Account Page' , 'rewardsystem' ) ,
					'desc'    => __( 'Enter the URL for Reward Points Fields in My Account Menu Page' , 'rewardsystem' ) ,
					'id'      => 'rs_my_reward_url_title' ,
					'type'    => 'text' ,
					'std'     => 'sumo-reward-points' ,
					'default' => 'sumo-reward-points' ,
					'newids'  => 'rs_my_reward_url_title' ,
					'desc_tip'=> true,
				) ,
				array(
					'name'    => __( 'My Rewards Table' , 'rewardsystem' ) ,
					'id'      => 'rs_my_reward_table_menu_page' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_my_reward_table_menu_page' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Referral Link' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_generate_referral_menu_page' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_show_hide_generate_referral_menu_page' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Referral Table ' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_referal_table_menu_page' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_show_hide_referal_table_menu_page' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Refer a Friend Form ' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_refer_a_friend_menu_page' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_show_hide_refer_a_friend_menu_page' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'     => __( 'My Cashback Form' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_form_menu_page' ,
					'std'      => '1' ,
					'desc_tip' => true ,
					'default'  => '1' ,
					'newids'   => 'rs_my_cashback_form_menu_page' ,
					'type'     => 'select' ,
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'     => __( 'My Cashback Table' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_table_menu_page' ,
					'std'      => '1' ,
					'desc_tip' => true ,
					'default'  => '1' ,
					'newids'   => 'rs_my_cashback_table_menu_page' ,
					'type'     => 'select' ,
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Nominee Field' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_nominee_field_menu_page' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_show_hide_nominee_field_menu_page' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Gift Voucher Field' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_redeem_voucher_menu_page' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_show_hide_redeem_voucher_menu_page' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Email - Subscribe Link' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_your_subscribe_link_menu_page' ,
					'newids'  => 'rs_show_hide_your_subscribe_link_menu_page' ,
					'class'   => 'rs_show_hide_your_subscribe_link_menu_page' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'type' => 'reward_page_menu_sorting' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_my_acccount_menu_page_show_hide_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_administrator_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Reward Points Menu Restriction based on User Role' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_user_role_menu_restriction_reward_points' ,
				) ,
				array(
					'name'    => __( 'User Role based Menu Restriction' , 'rewardsystem' ) ,
					'desc'    => __( 'Enable this option to restrict the menu based on user role' , 'rewardsystem' ) ,
					'id'      => 'rs_menu_restriction_based_on_user_role' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_menu_restriction_based_on_user_role' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_user_role_menu_restriction_reward_points' ) ,
				array(
					'type' => 'rs_administrator_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Coupon Deletion based on Cron Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_delete_coupon_cron_time' ,
				) ,
				array(
					'name'    => __( 'Coupon Deletion' , 'rewardsystem' ) ,
					'id'      => '_rs_enable_coupon_restriction' ,
					'newids'  => '_rs_enable_coupon_restriction' ,
					'type'    => 'checkbox' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'desc'    => __('By Enabling this checkbox, the coupons created on WooCommerce coupon section when points have been applied will be deleted based on cron time.', 'rewardsystem') ,
				) ,
				array(
					'name'    => __( 'Delete Coupons ' , 'rewardsystem' ) ,
					'id'      => '_rs_restrict_coupon' ,
					'newids'  => '_rs_restrict_coupon' ,
					'type'    => 'select' ,
					'std'     => '1' ,
					'default' => '1' ,
					'options' => array(
						'1' => __( 'After the order has been placed' , 'rewardsystem' ) ,
						'2' => __( 'Based on cron time' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Coupon Deletion Cron Time Type' , 'rewardsystem' ) ,
					'id'      => 'rs_delete_coupon_by_cron_time' ,
					'newids'  => 'rs_delete_coupon_by_cron_time' ,
					'type'    => 'select' ,
					'std'     => '1' ,
					'default' => '1' ,
					'options' => array(
						'1' => __( 'Days' , 'rewardsystem' ) ,
						'2' => __( 'Hours' , 'rewardsystem' ) ,
						'3' => __( 'Minutes' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'              => __( 'Coupon Deletion Cron Time' , 'rewardsystem' ) ,
					'id'                => 'rs_delete_coupon_specific_time' ,
					'newids'            => 'rs_delete_coupon_specific_time' ,
					'type'              => 'number' ,
					'custom_attributes' => array(
						'min' => 0
					) ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_delete_coupon_specific_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Rank based Reward Points Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_short_code_settings_rank' ,
				) ,
				array(
					'name'    => __( 'Rank Table for Total Earned Points to be displayed ' , 'rewardsystem' ) ,
					'id'      => 'rs_select_pagination_for_total_earned_points' ,
					'newids'  => 'rs_select_pagination_for_total_earned_points' ,
					'type'    => 'select' ,
					'std'     => '1' ,
					'default' => '1' ,
					'options' => array(
						'1' => __( 'With Pagination' , 'rewardsystem' ) ,
						'2' => __( 'Without Pagination' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'              => __( 'Enter the number of entries to be displayed' , 'rewardsystem' ) ,
					'id'                => 'rs_value_without_pagination_for_total_earned_points' ,
					'newids'            => 'rs_value_without_pagination_for_total_earned_points' ,
					'type'              => 'number' ,
					'custom_attributes' => array(
						'min' => '0'
					) ,
					'std'               => '' ,
					'default'           => '' ,
				) ,
				array(
					'name'    => __( 'Rank Table for Available Points to be displayed ' , 'rewardsystem' ) ,
					'id'      => 'rs_select_pagination_for_available_points' ,
					'newids'  => 'rs_select_pagination_for_available_points' ,
					'type'    => 'select' ,
					'std'     => '1' ,
					'default' => '1' ,
					'options' => array(
						'1' => __( 'With Pagination' , 'rewardsystem' ) ,
						'2' => __( 'Without Pagination' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'              => __( 'Enter the number of entries to be displayed' , 'rewardsystem' ) ,
					'id'                => 'rs_value_without_pagination_for_available_points' ,
					'newids'            => 'rs_value_without_pagination_for_available_points' ,
					'type'              => 'number' ,
					'custom_attributes' => array(
						'min' => '0'
					) ,
					'std'               => '' ,
					'default'           => '' ,
				) ,
				array(
					'name'    => __( 'User Reward Points display based on specific duration' , 'rewardsystem' ) ,
					'id'      => 'rs_points_earned_in_specific_duration_is_enabled' ,
					'newids'  => 'rs_points_earned_in_specific_duration_is_enabled' ,
					'type'    => 'checkbox' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'desc'    => __('By enabling this checkbox, you can display the table which shows the points earned by the users in a specific duration' , 'rewardsystem'),
				) ,
				array(
					'type' => 'rs_points_earned_in_specific_duration_shortcode' ,
				) ,
				array(
					'name'    => __( 'Pagination' , 'rewardsystem' ) ,
					'id'      => 'rs_points_earned_in_specific_duration_pagination' ,
					'newids'  => 'rs_points_earned_in_specific_duration_pagination' ,
					'type'    => 'number' ,
					'std'     => '5' ,
					'default' => '5' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_short_code_settings_rank' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Custom CSS Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'desc' => __('Try !important if styles doesn\'t apply ' , 'rewardsystem'),
					'id'   => '_rs_general_custom_css_settings' ,
				) ,
				array(
					'name'    => __( 'Custom CSS' , 'rewardsystem' ) ,
					'id'      => 'rs_general_custom_css' ,
					'std'     => '' ,
					'default' => '' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_general_custom_css' ,
				) ,
				array(
					'name'    => __( 'Custom CSS for Shop Page' , 'rewardsystem' ) ,
					'id'      => 'rs_shop_page_custom_css' ,
					'std'     => '' ,
					'default' => '' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_shop_page_custom_css' ,
				) ,
				array(
					'name'    => __( 'Custom CSS for Category Page' , 'rewardsystem' ) ,
					'id'      => 'rs_category_page_custom_css' ,
					'std'     => '' ,
					'default' => '' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_category_page_custom_css' ,
				) ,
				array(
					'name'    => __( 'Custom CSS for Single Product Page' , 'rewardsystem' ) ,
					'id'      => 'rs_single_product_page_custom_css' ,
					'std'     => '.rs_message_for_single_product{ }' ,
					'default' => '.rs_message_for_single_product{ }' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_single_product_page_custom_css' ,
				) ,
				array(
					'name'    => __( 'Custom CSS for Cart Page' , 'rewardsystem' ) ,
					'id'      => 'rs_cart_page_custom_css' ,
					'std'     => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ } .rs_cart_message{ }' ,
					'default' => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ } .rs_cart_message{ }' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_cart_page_custom_css' ,
				) ,
				array(
					'name'    => __( 'Custom CSS for Checkout Page' , 'rewardsystem' ) ,
					'id'      => 'rs_checkout_page_custom_css' ,
					'std'     => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ } .rs_checkout_message{ }' ,
					'default' => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ } .rs_checkout_message{ }' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_checkout_page_custom_css' ,
				) ,
				array(
					'name'   => __( 'Custom CSS for My Account Page' , 'rewardsystem' ) ,
					'id'     => 'rs_myaccount_custom_css' ,
					'std'    => '#generate_referral_field { }  '
					. '#rs_redeem_voucher_code { }  '
					. '#ref_generate_now { } '
					. ' #rs_submit_redeem_voucher { }'
					. '.rs_subscriptionoption h3 { }' ,
					'type'   => 'textarea' ,
					'newids' => 'rs_myaccount_custom_css' ,
				) ,
				array(
					'name'    => __( 'Custom CSS for Social Button' , 'rewardsystem' ) ,
					'id'      => 'rs_social_custom_css' ,
					'std'     => '.rs_social_sharing_buttons{};'
					. '.rs_social_sharing_success_message' ,
					'default' => '.rs_social_sharing_buttons{};'
					. '.rs_social_sharing_success_message' ,
					'newids'  => 'rs_social_custom_css' ,
					'type'    => 'textarea' ,
				) ,
				array(
					'name'    => __( 'Custom CSS for Refer a Friend Form' , 'rewardsystem' ) ,
					'id'      => 'rs_refer_a_friend_custom_css' ,
					'std'     => '#rs_refer_a_friend_form { } #rs_friend_name { } #rs_friend_email { } #rs_friend_subject { } #rs_your_message { } #rs_refer_submit { }' ,
					'default' => '#rs_refer_a_friend_form { } #rs_friend_name { } #rs_friend_email { } #rs_friend_subject { } #rs_your_message { } #rs_refer_submit { }' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_refer_a_friend_custom_css' ,
				) ,
				array(
					'name'    => __( 'Inbuilt Design for Cashback Form' , 'rewardsystem' ) ,
					'id'      => 'rs_encash_form_inbuilt_design' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'radio' ,
					'options' => array(
						'1' => __( 'Inbuilt Design' , 'rewardsystem' )
					) ,
					'newids'  => 'rs_encash_form_inbuilt_design' ,
				) ,
				array(
					'name'              => __( 'Inbuilt CSS (Non Editable) for Cashback Form' , 'rewardsystem' ) ,
					'id'                => 'rs_encash_form_default_css' ,
					'std'               => '#encashing_form{}
.rs_encash_points_value{}
.error{color:#ED0514;}
.rs_encash_points_reason{}
.rs_encash_payment_method{}
.rs_encash_paypal_address{}
.rs_encash_custom_payment_option_value{}
.rs_encash_submit{}
#rs_encash_submit_button{}
.success_info{}
#encash_form_success_info{}' ,
					'default'           => '#encashing_form{}
.rs_encash_points_value{}
.error{color:#ED0514;}
.rs_encash_points_reason{}
.rs_encash_payment_method{}
.rs_encash_paypal_address{}
.rs_encash_custom_payment_option_value{}
.rs_encash_submit{}
#rs_encash_submit_button{}
.success_info{}
#encash_form_success_info{}' ,
					'type'              => 'textarea' ,
					'custom_attributes' => array(
						'readonly' => 'readonly'
					) ,
					'newids'            => 'rs_encash_form_default_css' ,
				) ,
				array(
					'name'    => __( 'Custom Design for Cashback Form' , 'rewardsystem' ) ,
					'id'      => 'rs_encash_form_inbuilt_design' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'radio' ,
					'options' => array(
						'2' => __( 'Custom Design' , 'rewardsystem' )
					) ,
					'newids'  => 'rs_encash_form_inbuilt_design' ,
				) ,
				array(
					'name'    => __( 'Custom CSS for Cashback Form' , 'rewardsystem' ) ,
					'id'      => 'rs_encash_form_custom_css' ,
					'std'     => '' ,
					'default' => '' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_encash_form_custom_css' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_general_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Troubleshoot Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_reward_point_troubleshoot_cart_page'
				) ,
				array(
					'name'     => __( 'Troubleshoot Before Cart Hook' , 'rewardsystem' ) ,
					'desc'     => __( 'Here you can select the different hooks in Cart Page' , 'rewardsystem' ) ,
					'id'       => 'rs_reward_point_troubleshoot_before_cart' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'radio' ,
					'options'  => array( '1' => 'woocommerce_before_cart' , '2' => 'woocommerce_before_cart_table' ) ,
					'newids'   => 'rs_reward_point_troubleshoot_before_cart' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Troubleshoot Referral Link Landing Page Hook' , 'rewardsystem' ) ,
					'desc'     => __( 'Here you can select the different hooks for referral link landing page' , 'rewardsystem' ) ,
					'id'       => 'rs_troubleshoot_referral_link_landing_page' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'radio' ,
					'options'  => array( '1' => 'wp' , '2' => 'wp_head' ) ,
					'newids'   => 'rs_troubleshoot_referral_link_landing_page' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Redeeming Field display Position in Cart Page' , 'rewardsystem' ) ,
					'id'      => 'rs_reward_point_troubleshoot_after_cart' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'radio' ,
					'options' => array(
						'1' => __( 'After cart table' , 'rewardsystem' ) ,
						'2' => __( 'Left side in cart table' , 'rewardsystem' ) ,
						'3' => __( 'Right side in the cart table' , 'rewardsystem' )
					) ,
					'newids'  => 'rs_reward_point_troubleshoot_after_cart' ,
				) ,
				array(
					'name'     => __( 'Enqueue Tipsy jQuery Library in SUMO Reward Points' , 'rewardsystem' ) ,
					'desc'     => __( 'Here you can select to change the load tipsy option if some jQuery conflict occurs' , 'rewardsystem' ) ,
					'id'       => 'rs_reward_point_enable_tipsy_social_rewards' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'radio' ,
					'options'  => array(
						'1' => __( 'Enable' , 'rewardsystem' ) ,
						'2' => __( 'Disable' , 'rewardsystem' )
					) ,
					'newids'   => 'rs_reward_point_enable_tipsy_social_rewards' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Enqueue jQuery UI Library in SUMO Reward Points' , 'rewardsystem' ) ,
					'desc'     => __( 'Here you can select whether to enqueue the jQuery UI library available within SUMO Reward Points' , 'rewardsystem' ) ,
					'id'       => 'rs_reward_point_enable_jquery' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'radio' ,
					'options'  => array(
						'1' => __( 'Enqueue' , 'rewardsystem' ) ,
						'2' => __( 'Do not Enqueue' , 'rewardsystem' )
					) ,
					'newids'   => 'rs_reward_point_enable_jquery' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Enqueue Ajax based Redeeming Library in SUMO Reward Points' , 'rewardsystem' ) ,
					'desc'     => __( 'Here you can select whether to enqueue the Ajax based Redeeming library available within SUMO Reward Points' , 'rewardsystem' ) ,
					'id'       => 'rs_reward_point_enable_ajax_based_redeeming' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'radio' ,
					'options'  => array(
						'1' => __( 'Enqueue' , 'rewardsystem' ) ,
						'2' => __( 'Do not Enqueue' , 'rewardsystem' )
					) ,
					'newids'   => 'rs_reward_point_enable_ajax_based_redeeming' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Dequeue Select2 Library Version 3.x in SUMO Reward Points' , 'rewardsystem' ) ,
					'desc'    => __( 'Here you can dequeue the select2 V3.x library in the Front End within SUMO Reward Points' , 'rewardsystem' ) ,
					'id'      => 'rs_reward_point_dequeue_select2' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_reward_point_dequeue_select2' ,
				) ,
				array(
					'name'    => __( 'Dequeue Google reCaptcha Library in SUMO Reward Points' , 'rewardsystem' ) ,
					'desc'    => __( 'Here you can dequeue the Google reCaptcha library in the Front End within SUMO Reward Points' , 'rewardsystem' ) ,
					'id'      => 'rs_reward_point_dequeue_recaptcha' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_reward_point_dequeue_recaptcha' ,
				) ,
				 array(
					'name'    => __( 'Dequeue Select2 CSS in SUMO Reward Points' , 'rewardsystem' ) ,
					'desc'    => __( 'Here you can dequeue the select2 CSS in the Front End within SUMO Reward Points' , 'rewardsystem' ) ,
					'id'      => 'rs_reward_point_dequeue_select2_css' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'type'    => 'checkbox' ,
					'newids'  => 'rs_reward_point_dequeue_select2_css' ,
				) ,
				array(
					'name'     => __( 'Enqueue Bootstrap CSS in SUMO Reward Points' , 'rewardsystem' ) ,
					'id'       => 'rs_enable_reward_point_bootstrap' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'radio' ,
					'newids'   => 'rs_enable_reward_point_bootstrap' ,
					'options'  => array(
						'1' => __( 'Enqueue' , 'rewardsystem' ) ,
						'2' => __( 'Do not Enqueue' , 'rewardsystem' )
					) ,
					'desc'     => __( 'Here you can dequeue the bootstrap css within SUMO Reward Points' , 'rewardsystem' ) ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Enqueue Footable JQuery in SUMO Reward Points' , 'rewardsystem' ) ,
					'id'       => 'rs_enable_footable_js' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'radio' ,
					'newids'   => 'rs_enable_footable_js' ,
					'options'  => array(
						'1' => __( 'Enqueue' , 'rewardsystem' ) ,
						'2' => __( 'Do not Enqueue' , 'rewardsystem' )
					) ,
					'desc'     => __( 'Here you can dequeue the Footable JQuery within SUMO Reward Points' , 'rewardsystem' ) ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Enqueue JSColor in SUMO Reward Points' , 'rewardsystem' ) ,
					'id'       => 'rs_enable_jscolor_js' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'radio' ,
					'newids'   => 'rs_enable_jscolor_js' ,
					'options'  => array(
						'1' => __( 'Enqueue' , 'rewardsystem' ) ,
						'2' => __( 'Do not Enqueue' , 'rewardsystem' )
					) ,
					'desc'     => __( 'Here you can dequeue the JSColor within SUMO Reward Points' , 'rewardsystem' ) ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Include Header' , 'rewardsystem' ) ,
					'id'       => 'rs_enable_get_header' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'radio' ,
					'newids'   => 'rs_enable_get_header' ,
					'options'  => array(
						'1' => __( 'Include' , 'rewardsystem' ) ,
						'2' => __( 'Exclude' , 'rewardsystem' )
					) ,
					'desc'     => __( 'Here you can exclude the header which break css in page where referral link placed' , 'rewardsystem' ) ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Earn Points Message display in Product Page for Variable product based on' , 'rewardsystem' ) ,
					'id'      => 'rs_earn_message_display_hook' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'radio' ,
					'newids'  => 'rs_earn_message_display_hook' ,
					'options' => array(
						'1' => __( 'woocommerce_get_price_html' , 'rewardsystem' ) ,
						'2' => __( 'WooCommerce template hooks' , 'rewardsystem' )
					) ,
				) ,
				array(
					'name'    => __( 'Set the Hook Priority to display Redeemed Points Label in Cart & Checkout' , 'rewardsystem' ) ,
					'id'      => 'rs_change_coupon_priority_value' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'number' ,
					'newids'  => 'rs_change_coupon_priority_value' ,
				) ,
				array(
					'name'    => __( 'Set the Hook Priority to display Redeemed Points Field in Checkout Page' , 'rewardsystem' ) ,
					'id'      => 'rs_redeeming_field_hook_priority_in_checkout' ,
					'std'     => '10' ,
					'default' => '10' ,
					'type'    => 'number' ,
					'newids'  => 'rs_redeeming_field_hook_priority_in_checkout' ,
				) ,
				array(
					'name'     => __( 'Load SUMO Reward Points Script/Styles in' , 'rewardsystem' ) ,
					'desc'     => __( 'For Footer of the Site Option is experimental why because if your theme doesn\'t contain wp_footer hook then it won\'t work' , 'rewardsystem' ) ,
					'id'       => 'rs_load_script_styles' ,
					'newids'   => 'rs_load_script_styles' ,
					'type'     => 'select' ,
					'desc_tip' => true ,
					'options'  => array(
						'wp_head'   => __( 'Header of the Site' , 'rewardsystem' ) ,
						'wp_footer' => __( 'Footer of the Site (Experimental)' , 'rewardsystem' )
					) ,
					'std'      => 'wp_head' ,
					'default'  => 'wp_head' ,
				) ,
				array(
					'name'     => __( 'Memory Exhaust Issues' , 'rewardsystem' ) ,
					'desc'     => __( 'Enable or Disable Memory Exhaust Troubleshoot' , 'rewardsystem' ) ,
					'id'       => 'rs_load_memory_unit' ,
					'newids'   => 'rs_load_memory_unit' ,
					'type'     => 'select' ,
					'desc_tip' => true ,
					'options'  => array(
						'1' => __( 'Enable' , 'rewardsystem' ) ,
						'2' => __( 'Disable' , 'rewardsystem' )
					) ,
					'std'      => '2' ,
					'default'  => '2' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_troubleshoot_cart_page' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Experimental Features' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_reward_point_table'
				) ,
				array(
					'type' => 'rs_add_old_version_points' ,
				) ,
				array(
					'name'     => __( 'SUMO Reward Points Payment Gateway for Manual Order' , 'rewardsystem' ) ,
					'desc'     => __( 'Enable or Disable SUMO Reward Points Payment Gateway for Manual Order' , 'rewardsystem' ) ,
					'id'       => 'rs_gateway_for_manual_order' ,
					'newids'   => 'rs_gateway_for_manual_order' ,
					'std'      => '2' ,
					'default'  => '2' ,
					'type'     => 'select' ,
					'desc_tip' => true ,
					'options'  => array(
						'1' => __( 'Enable' , 'rewardsystem' ) ,
						'2' => __( 'Disable' , 'rewardsystem' )
					) ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_table' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
					)
					) ;
		}

		public static function rs_wrapper_administrator_start() {
			?>
			<div class="rs_adminstrator_wrapper">
				<?php
		}

		public static function rs_wrapper_administrator_close() {
			?>
			</div>
			<?php
			$user_roles = rs_get_current_user_role() ;
			if ( ! in_array( 'administrator' , $user_roles ) ) {
							$contents = '.rs_adminstrator_wrapper{
						display:none;
					}';
														
				wp_register_style( 'fp-srp-advanced-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
				wp_enqueue_style( 'fp-srp-advanced-style' ) ;
				wp_add_inline_style( 'fp-srp-advanced-style' , $contents ) ;                        
			}
		}

		public static function reward_system_register_admin_settings() {

			woocommerce_admin_fields( self::reward_system_admin_fields() ) ;
		}

		public static function reward_system_update_settings() {
			woocommerce_update_options( self::reward_system_admin_fields() ) ;

			$sorted_menu_data = isset( $_REQUEST[ 'rs_sorted_reward_menu_list' ] ) ? array_filter( wc_clean( wp_unslash( $_REQUEST[ 'rs_sorted_reward_menu_list' ] ) ) ) : array() ;
			if ( srp_check_is_array( $sorted_menu_data ) ) {
				update_option( 'rs_sorted_menu_settings_list' , $sorted_menu_data ) ;
			} else {
				update_option( 'rs_sorted_menu_settings_list' , array() ) ;
			}

			if ( isset( $_REQUEST[ 'rs_points_earned_in_specific_duration_from_date' ] ) ) {
				update_option( 'rs_points_earned_in_specific_duration_from_date' , sanitize_text_field( $_REQUEST[ 'rs_points_earned_in_specific_duration_from_date' ] ) ) ;
			}

			if ( isset( $_REQUEST[ 'rs_points_earned_in_specific_duration_to_date' ] ) ) {
				update_option( 'rs_points_earned_in_specific_duration_to_date' , sanitize_text_field( $_REQUEST[ 'rs_points_earned_in_specific_duration_to_date' ] ) ) ;
			}

			// Update flush option for my reward menu.  
			update_option( 'rs_flush_rewrite_rules' , 1 ) ;
		}

		public static function set_default_value() {
			foreach ( self::reward_system_admin_fields() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		public static function reset_advanced_tab() {
			$settings = self::reward_system_admin_fields() ;
			self::reset_options() ;
			RSTabManagement::reset_settings( $settings ) ;
		}

		public static function reset_options() {
			// Update flush option for my reward menu.  
			update_option( 'rs_flush_rewrite_rules' , 1 ) ;

			update_option( 'rs_sorted_menu_settings_list' , array() ) ;
		}

		public static function add_old_points_for_all_user() {
			?>
			<tr valign="top">
				<th>
					<label class="fp-srp-button-label"><?php esc_html_e( 'Add the Old Available Points to User(s)' , 'rewardsystem' ) ; ?></label>
				</th>
				<td>
					<input type="button" value="<?php esc_html_e( 'Add Old Points' , 'rewardsystem' ) ; ?>"  id="rs_add_old_points" class="rs_oldpoints_button" name="rs_add_old_points" /><b><span class="fp-srp-experimental-label"><?php esc_html_e( '(Experimental)' , 'rewardsystem' ) ; ?></span></b>
				</td>
			</tr>
			<?php
		}

		public static function rs_add_date_picker() {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="rs_sumo_rewards_for_selecting_particular_date"><?php esc_html_e( 'Select from Specific Date' , 'rewardsystem' ) ; ?></label>
				</th>
				<td class="forminp forminp-select">
					<?php esc_html_e('From', 'rewardsystem'); ?> <input type="text" id="rs_from_date" value=""/> <?php esc_html_e('To', 'rewardsystem'); ?> <input type="text" id="rs_to_date" value=""/>
				</td>
			</tr>
			<?php
		}

		public static function rs_apply_points_for_previous_order_button() {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="rs_sumo_rewards_for_previous_order_label"><?php esc_html_e( 'Apply Product Purchase Reward Points to Previous Orders' , 'rewardsystem' ) ; ?></label>
				</th>
				<td class="forminp forminp-select">
					<input type="submit" class="rs_sumo_rewards_for_previous_order button-primary rs_button" value="<?php esc_html_e('Apply Points for Previous Orders', 'rewardsystem'); ?>"/>
					<div class="rs_sumo_rewards_previous_order"></div>
				</td>
			</tr>
			<?php
		}

		public static function reward_page_menu_sorting() {
			include SRP_PLUGIN_PATH . '/includes/admin/views/myreward-page-menu-sorting.php' ;
		}

		public static function reward_system_add_settings_based_on_user_role( $settings ) {
			global $wp_roles ;
			$updated_settings = array() ;
			$mainvariable     = array() ;
			global $woocommerce ;
			$newcombinedarray = rs_list_of_tabs() ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && '_rs_user_role_menu_restriction_reward_points' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
					foreach ( $wp_roles->role_names as $value => $key ) {
						if ( 'Administrator' != $key && 'Customer' != $key ) {
							$updated_settings[] = array(
								'name'     => __( 'Reward Points Menu Restriction For ' . sanitize_text_field($key) . ' User Role' , 'rewardsystem' ) ,
								'desc'     => __( 'Restrict the Reward Points Menus For ' . sanitize_text_field($key) . ' User role' , 'rewardsystem' ) ,
								'class'    => 'rewardpoints_userrole_menu_restriction' ,
								'id'       => 'rewardpoints_userrole_menu_restriction' . sanitize_text_field($value) ,
								'type'     => 'multiselect' ,
								'options'  => $newcombinedarray ,
								'default'  => '' ,
								'std'      => '' ,
								'newids'   => 'rewardpoints_userrole_menu_restriction' . sanitize_text_field($value) ,
								'desc_tip' => true ,
									) ;
						}
					}

					$updated_settings[] = array(
						'type' => 'sectionend' , 'id'   => '_rs_user_role_menu_restriction_reward_points' ,
							) ;
				}

				$updated_settings[] = $section ;
			}

			return $updated_settings ;
		}

		public static function rs_points_earned_in_specific_duration_shortcode() {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label><?php esc_html_e( 'From Date' , 'rewardsystem' ) ; ?></label>
				</th>
				<td class="forminp forminp-select">
					<input type="text" 
						   class="rs_points_earned_in_specific_duration_from_date srp_datepicker" 
						   name="rs_points_earned_in_specific_duration_from_date" id="rs_points_earned_in_specific_duration_from_date"
						   value="<?php echo esc_html( get_option( 'rs_points_earned_in_specific_duration_from_date' ) ); ?>" />
				</td>                
			</tr>                

			<tr valign="top">
				<th class="titledesc" scope="row">
					<label><?php esc_html_e( 'To Date' , 'rewardsystem' ) ; ?></label>
				</th>
				<td class="forminp forminp-select">
					<input type="text" 
						   class="rs_points_earned_in_specific_duration_to_date srp_datepicker"
						   name="rs_points_earned_in_specific_duration_to_date" 
						   id="rs_points_earned_in_specific_duration_to_date" 
						   value="<?php echo esc_html( get_option( 'rs_points_earned_in_specific_duration_to_date' ) ); ?>" />                                
				</td>                
			</tr>
			<?php
		}

	}

	RSAdvancedSetting::init() ;
}
