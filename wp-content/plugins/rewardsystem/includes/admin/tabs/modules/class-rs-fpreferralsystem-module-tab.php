<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSReferralSystemModule' ) ) {

	class RSReferralSystemModule {

		public static function init() {

			add_action( 'rs_default_settings_fpreferralsystem', array( __CLASS__, 'set_default_value' ) );

			add_action( 'woocommerce_rs_settings_tabs_fpreferralsystem', array( __CLASS__, 'reward_system_register_admin_settings' ) ); // Call to register the admin settings in the Reward System Submenu with general Settings tab

			add_action( 'woocommerce_update_options_fprsmodules_fpreferralsystem', array( __CLASS__, 'reward_system_update_settings' ) ); // call the woocommerce_update_options_{slugname} to update the reward system

			add_action( 'woocommerce_admin_field_rs_user_role_dynamics_manual', array( __CLASS__, 'reward_system_add_manual_table_to_action' ) );

			add_action( 'woocommerce_admin_field_display_referral_reward_log', array( __CLASS__, 'rs_list_referral_rewards_log' ) );

			add_action( 'woocommerce_admin_field_rs_enable_disable_referral_system_module', array( __CLASS__, 'enable_module' ) );

			add_action( 'woocommerce_admin_field_image_uploader', array( __CLASS__, 'rs_add_upload_your_facebook_share_image' ) );

			add_action( 'woocommerce_admin_field_selected_products', array( __CLASS__, 'rs_select_products_to_update' ) );

			add_action( 'woocommerce_admin_field_rs_select_exclude_user_for_referral_link', array( __CLASS__, 'rs_exclude_user_as_hide_referal_link' ) );

			add_action( 'woocommerce_admin_field_rs_hide_bulk_update_for_referral_product_purchase_start', array( __CLASS__, 'rs_hide_bulk_update_for_referral_product_purchase_start' ) );

			add_action( 'woocommerce_admin_field_rs_hide_bulk_update_for_referral_product_purchase_end', array( __CLASS__, 'rs_hide_bulk_update_for_referral_product_purchase_end' ) );

			add_action( 'woocommerce_admin_field_referral_button', array( __CLASS__, 'rs_save_button_for_referral_update' ) );

			add_action( 'woocommerce_admin_field_rs_select_user_for_referral_link', array( __CLASS__, 'rs_include_user_as_hide_referal_link' ) );

			add_action( 'woocommerce_admin_field_rs_include_products_for_referral_product_purchase', array( __CLASS__, 'rs_include_products_for_referral_product_purchase' ) );

			add_action( 'woocommerce_admin_field_rs_exclude_products_for_referral_product_purchase', array( __CLASS__, 'rs_exclude_products_for_referral_product_purchase' ) );

			add_action( 'fp_action_to_reset_module_settings_fpreferralsystem', array( __CLASS__, 'reset_referral_system_module' ) );

			add_action( 'rs_display_save_button_fpreferralsystem', array( 'RSTabManagement', 'rs_display_save_button' ) );

			add_action( 'rs_display_reset_button_fpreferralsystem', array( 'RSTabManagement', 'rs_display_reset_button' ) );

			add_filter( 'rs_alter_manual_referral_link_rules', array( __CLASS__, 'manual_referral_link_user_search_filter' ) );

			if ( class_exists( 'SUMOSubscriptions' ) || class_exists( 'WC_Subscriptions' ) ) {
				add_filter( 'woocommerce_fpreferralsystem', array( __CLASS__, 'render_subscription_settings' ) );
			}
			add_action( 'woocommerce_admin_field_get_reward_signup_bonus', array( __CLASS__, 'srp_get_reward_signup_bonus' ) );
		}

		/*
		 * Function label settings to Member Level Tab
		 */

		public static function reward_system_admin_fields() {
			global $woocommerce;
			global $wp_roles;
			foreach ( $wp_roles->roles as $values => $key ) {
				$userroleslug[] = $values;
				$userrolename[] = $key['name'];
			}
			$newcombineduserrole       = array_combine( (array) $userroleslug, (array) $userrolename );
			$categorylist              = fp_product_category();
			$newcombinedarray          = fp_paid_order_status();
			$referral_product_purchase = get_option( 'rs_enable_product_category_level_for_referral_product_purchase' );
			$send_mail_for_referral    = get_option( 'rs_send_mail_pdt_purchase_referral' );
			$send_mail_for_referrer    = get_option( 'rs_send_mail_pdt_purchase_referrer' );
			if ( 'yes' == $referral_product_purchase && 'yes' == $send_mail_for_referral ) {
				$referral_mail = 'yes';
			} else {
				$referral_mail = 'no';
			}
			if ( 'yes' == $referral_product_purchase && 'yes' == $send_mail_for_referrer ) {
				$refered_mail = 'yes';
			} else {
				$refered_mail = 'no';
			}
						/**
						 * Hook:woocommerce_fpreferralsystem.
						 *
						 * @since 1.0
						 */
			return apply_filters(
				'woocommerce_fpreferralsystem',
				array(
					array(
						'type' => 'rs_modulecheck_start',
					),
					array(
						'name' => __( 'Referral System Module', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_activate_referral_module',
					),
					array(
						'type' => 'rs_enable_disable_referral_system_module',
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_activate_referral_module',
					),
					array(
						'type' => 'rs_modulecheck_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Referral Link Cookies Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_referral_cookies_settings',
					),
					array(
						'name'     => __( 'Referral Link Cookies Expires in', 'rewardsystem' ),
						'id'       => 'rs_referral_cookies_expiry',
						'std'      => '3',
						'default'  => '3',
						'newids'   => 'rs_referral_cookies_expiry',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Minutes', 'rewardsystem' ),
							'2' => __( 'Hours', 'rewardsystem' ),
							'3' => __( 'Days', 'rewardsystem' ),
						),
						'desc_tip' => false,
					),
					array(
						'name'     => __( 'Referral Link Cookies Expiry in Minutes', 'rewardsystem' ),
						'desc'     => __( 'Enter a Fixed Number greater than or equal to 0', 'rewardsystem' ),
						'id'       => 'rs_referral_cookies_expiry_in_min',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_referral_cookies_expiry_in_min',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Referral Link Cookies Expiry in Hours', 'rewardsystem' ),
						'desc'     => __( 'Enter a Fixed Number greater than or equal to 0', 'rewardsystem' ),
						'id'       => 'rs_referral_cookies_expiry_in_hours',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_referral_cookies_expiry_in_hours',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Referral Link Cookies Expiry in Days', 'rewardsystem' ),
						'desc'     => __( 'Enter a Fixed Number greater than or equal to 0', 'rewardsystem' ),
						'id'       => 'rs_referral_cookies_expiry_in_days',
						'std'      => '1',
						'default'  => '1',
						'type'     => 'text',
						'newids'   => 'rs_referral_cookies_expiry_in_days',
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Delete Cookies After X Number of Purchase(s)', 'rewardsystem' ),
						'desc'    => __( 'Enable this option to delete cookies after X number of purchase(s)', 'rewardsystem' ),
						'id'      => 'rs_enable_delete_referral_cookie_after_first_purchase',
						'std'     => 'no',
						'default' => 'no',
						'type'    => 'checkbox',
						'newids'  => 'rs_enable_delete_referral_cookie_after_first_purchase',
					),
					array(
						'name'     => __( 'Number of Purchase(s)', 'rewardsystem' ),
						'desc'     => __( 'Number of Purchase(s) in which cookie to be deleted', 'rewardsystem' ),
						'id'       => 'rs_no_of_purchase',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_no_of_purchase',
						'desc_tip' => true,
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_referral_cookies_settings',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Linking Referrals for Life Time Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_life_time_referral',
					),
					array(
						'name'    => __( 'Linking Referrals for Life Time', 'rewardsystem' ),
						'desc'    => __( 'Enable this option to link referrals for life time', 'rewardsystem' ),
						'id'      => 'rs_enable_referral_link_for_life_time',
						'std'     => 'no',
						'default' => 'no',
						'type'    => 'checkbox',
						'newids'  => 'rs_enable_referral_link_for_life_time',
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_life_time_referral',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Referral Link Limit Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_referral_link_for_specific_limit',
					),
					array(
						'name'    => __( 'Maximum Referral Link Usage', 'rewardsystem' ),
						'id'      => 'rs_enable_referral_link_limit',
						'std'     => 'no',
						'default' => 'no',
						'type'    => 'checkbox',
						'newids'  => 'rs_enable_referral_link_limit',
						'desc'    => __( 'Enable this checkbox to restrict referral link usage count', 'rewardsystem' ),
					),
					array(
						'name'              => __( 'Enter the Value', 'rewardsystem' ),
						'id'                => 'rs_referral_link_limit',
						'std'               => '',
						'default'           => '',
						'type'              => 'number',
						'custom_attributes' => array(
							'min' => '0',
						),
						'newids'            => 'rs_referral_link_limit',
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_life_time_referral',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Referral Product Purchase Reward Points Global Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_global_referral_reward_points',
					),
					array(
						'name'     => __( 'Referral Product Purchase Reward Points', 'rewardsystem' ),
						'id'       => 'rs_enable_product_category_level_for_referral_product_purchase',
						'class'    => 'rs_enable_product_category_level_for_referral_product_purchase',
						'std'      => 'no',
						'default'  => 'no',
						'type'     => 'radio',
						'newids'   => 'rs_enable_product_category_level_for_referral_product_purchase',
						'options'  => array(
							'no'  => __( 'Quick Setup (Global Level Settings will be enabled)', 'rewardsystem' ),
							'yes' => __( 'Advanced Setup (Global,Category and Product Level wil be enabled)', 'rewardsystem' ),
						),
						'desc_tip' => true,
						'desc'     => __( 'Quick Setup - Global Level will be enabled<br>Advanced Setup - Global,Category and Product Level wil be enabled', 'rewardsystem' ),
					),
					array(
						'name'    => __( 'Earning Points Type', 'rewardsystem' ),
						'id'      => 'rs_award_points_for_cart_or_product_total_for_refferal_system',
						'std'     => '1',
						'class'   => 'rs_award_points_for_cart_or_product_total_for_refferal_system',
						'default' => '1',
						'newids'  => 'rs_award_points_for_cart_or_product_total_for_refferal_system',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Product Total', 'rewardsystem' ),
							'2' => __( 'Cart Total', 'rewardsystem' ),
						),
					),
					array(
						'name'    => __( 'Referral Product Purchase Reward Points is applicable for', 'rewardsystem' ),
						'id'      => 'rs_referral_product_purchase_global_level_applicable_for',
						'std'     => '1',
						'class'   => 'rs_referral_product_purchase_global_level_applicable_for',
						'default' => '1',
						'newids'  => 'rs_referral_product_purchase_global_level_applicable_for',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'All Product(s)', 'rewardsystem' ),
							'2' => __( 'Include Product(s)', 'rewardsystem' ),
							'3' => __( 'Exclude Product(s)', 'rewardsystem' ),
							'4' => __( 'All Categories', 'rewardsystem' ),
							'5' => __( 'Include Categories', 'rewardsystem' ),
							'6' => __( 'Exclude Categories', 'rewardsystem' ),
						),
					),
					array(
						'type' => 'rs_include_products_for_referral_product_purchase',
					),
					array(
						'type' => 'rs_exclude_products_for_referral_product_purchase',
					),
					array(
						'name'    => __( 'Include Categories', 'rewardsystem' ),
						'id'      => 'rs_include_particular_categories_for_referral_product_purchase',
						'css'     => 'min-width:350px;',
						'std'     => '',
						'class'   => 'rs_include_particular_categories_for_referral_product_purchase',
						'default' => '',
						'newids'  => 'rs_include_particular_categories_for_referral_product_purchase',
						'type'    => 'multiselect',
						'options' => $categorylist,
					),
					array(
						'name'    => __( 'Exclude Categories', 'rewardsystem' ),
						'id'      => 'rs_exclude_particular_categories_for_referral_product_purchase',
						'css'     => 'min-width:350px;',
						'std'     => '',
						'class'   => 'rs_exclude_particular_categories_for_referral_product_purchase',
						'default' => '',
						'newids'  => 'rs_exclude_particular_categories_for_referral_product_purchase',
						'type'    => 'multiselect',
						'options' => $categorylist,
					),
					array(
						'name'     => __( 'Global Level Referral Reward Points', 'rewardsystem' ),
						'id'       => 'rs_global_enable_disable_sumo_referral_reward',
						'std'      => '2',
						'default'  => '2',
						'desc_tip' => true,
						'desc'     => __(
							'Global Settings will be considered when Product and Category Settings are Enabled and Values are Empty. '
							. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order.',
							'rewardsystem'
						),
						'newids'   => 'rs_global_enable_disable_sumo_referral_reward',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Enable', 'rewardsystem' ),
							'2' => __( 'Disable', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Referral Reward Type', 'rewardsystem' ),
						'desc'     => __( 'Select Reward Type by Points/Percentage', 'rewardsystem' ),
						'id'       => 'rs_global_referral_reward_type',
						'class'    => 'rs_global_referral_reward_type show_if_enable_in_referral show_referral_based_on_product_total',
						'std'      => '1',
						'default'  => '1',
						'newids'   => 'rs_global_referral_reward_type',
						'type'     => 'select',
						'desc_tip' => true,
						'options'  => array(
							'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
							'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
						),
					),
					array(
						'name'        => __( 'Referral Reward Points', 'rewardsystem' ),
						'id'          => 'rs_global_referral_reward_point',
						'class'       => 'rs_global_referral_reward_point show_if_enable_in_referral show_referral_based_on_product_total',
						'std'         => '',
						'default'     => '',
						'type'        => 'text',
						'newids'      => 'rs_global_referral_reward_point',
						'placeholder' => '',
						'desc'        => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.',
							'rewardsystem'
						),
						'desc_tip'    => true,
					),
					array(
						'name'        => __( 'Referral Reward Points in Percent %', 'rewardsystem' ),
						'id'          => 'rs_global_referral_reward_percent',
						'class'       => 'rs_global_referral_reward_percent show_if_enable_in_referral show_referral_based_on_product_total',
						'std'         => '',
						'default'     => '',
						'type'        => 'text',
						'newids'      => 'rs_global_referral_reward_percent',
						'placeholder' => '',
						'desc'        => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.',
							'rewardsystem'
						),
						'desc_tip'    => true,
					),
					array(
						'name'     => __( 'Referral Reward Type', 'rewardsystem' ),
						'desc'     => __( 'Select Reward Type by Points/Percentage', 'rewardsystem' ),
						'id'       => 'rs_global_referral_reward_type_for_cart_total',
						'class'    => 'rs_global_referral_reward_type_for_cart_total show_if_enable_in_referral show_referral_based_on_cart_total',
						'std'      => '1',
						'default'  => '1',
						'newids'   => 'rs_global_referral_reward_type_for_cart_total',
						'type'     => 'select',
						'desc_tip' => true,
						'options'  => array(
							'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
							'2' => __( 'By Percentage of Cart Total', 'rewardsystem' ),
						),
					),
					array(
						'name'        => __( 'Referral Reward Points', 'rewardsystem' ),
						'id'          => 'rs_global_referral_reward_point_for_cart_total',
						'class'       => 'rs_global_referral_reward_point_for_cart_total show_if_enable_in_referral show_referral_based_on_cart_total',
						'std'         => '',
						'default'     => '',
						'type'        => 'text',
						'newids'      => 'rs_global_referral_reward_point_for_cart_total',
						'placeholder' => '',
						'desc'        => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.',
							'rewardsystem'
						),
						'desc_tip'    => true,
					),
					array(
						'name'        => __( 'Referral Reward Points in Percent %', 'rewardsystem' ),
						'id'          => 'rs_global_referral_reward_percent_for_cart_total',
						'class'       => 'rs_global_referral_reward_percent_for_cart_total show_if_enable_in_referral show_referral_based_on_cart_total',
						'std'         => '',
						'default'     => '',
						'type'        => 'text',
						'newids'      => 'rs_global_referral_reward_percent_for_cart_total',
						'placeholder' => '',
						'desc'        => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.',
							'rewardsystem'
						),
						'desc_tip'    => true,
					),
					array(
						'name'    => __( 'Enable to Send Email for Referral Product Purchase Reward Points', 'rewardsystem' ),
						'desc'    => __( 'Enabling this option will send Referral Product Purchase Points through Email', 'rewardsystem' ),
						'id'      => 'rs_send_mail_pdt_purchase_referral',
						'class'   => 'rs_send_mail_pdt_purchase_referral show_if_enable_in_referral',
						'type'    => 'checkbox',
						'std'     => $referral_mail,
						'default' => $referral_mail,
						'newids'  => 'rs_send_mail_pdt_purchase_referral',
					),
					array(
						'name'    => __( 'Email Subject for Referral Product Purchase Points', 'rewardsystem' ),
						'id'      => 'rs_email_subject_pdt_purchase_referral',
						'class'   => 'rs_email_subject_pdt_purchase_referral show_if_enable_in_referral',
						'std'     => 'Product Purchase Referral - Notification',
						'default' => 'Product Purchase Referral - Notification',
						'type'    => 'textarea',
						'newids'  => 'rs_email_subject_pdt_purchase_referral',
					),
					array(
						'name'    => __( 'Email Message for Referral Product Purchase Points', 'rewardsystem' ),
						'id'      => 'rs_email_message_pdt_purchase_referral',
						'class'   => 'rs_email_message_pdt_purchase_referral show_if_enable_in_referral',
						'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account',
						'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account',
						'type'    => 'textarea',
						'newids'  => 'rs_email_message_pdt_purchase_referral',
					),
					array(
						'name'     => __( 'Getting Referred Reward Type', 'rewardsystem' ),
						'desc'     => __( 'Select Reward Type by Points/Percentage', 'rewardsystem' ),
						'id'       => 'rs_global_referral_reward_type_refer',
						'class'    => 'rs_global_referral_reward_type_refer show_if_enable_in_referral show_referral_based_on_product_total',
						'std'      => '1',
						'default'  => '1',
						'newids'   => 'rs_global_referral_reward_type_refer',
						'type'     => 'select',
						'desc_tip' => true,
						'options'  => array(
							'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
							'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
						),
					),
					array(
						'name'        => __( 'Reward Points for Getting Referred', 'rewardsystem' ),
						'id'          => 'rs_global_referral_reward_point_get_refer',
						'class'       => 'rs_global_referral_reward_point_get_refer show_if_enable_in_referral show_referral_based_on_product_total',
						'std'         => '',
						'default'     => '',
						'type'        => 'text',
						'newids'      => 'rs_global_referral_reward_point_get_refer',
						'placeholder' => '',
						'desc'        => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.',
							'rewardsystem'
						),
						'desc_tip'    => true,
					),
					array(
						'name'     => __( 'Reward Points in Percent % For Getting Referred', 'rewardsystem' ),
						'id'       => 'rs_global_referral_reward_percent_get_refer',
						'class'    => 'rs_global_referral_reward_percent_get_refer show_if_enable_in_referral show_referral_based_on_product_total',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_global_referral_reward_percent_get_refer',
						'desc'     => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.',
							'rewardsystem'
						),
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Getting Referred Reward Type', 'rewardsystem' ),
						'desc'     => __( 'Select Reward Type by Points/Percentage', 'rewardsystem' ),
						'id'       => 'rs_global_referral_reward_type_refer_for_cart_total',
						'class'    => 'rs_global_referral_reward_type_refer_for_cart_total show_if_enable_in_referral show_referral_based_on_cart_total',
						'std'      => '1',
						'default'  => '1',
						'newids'   => 'rs_global_referral_reward_type_refer_for_cart_total',
						'type'     => 'select',
						'desc_tip' => true,
						'options'  => array(
							'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
							'2' => __( 'By Percentage of Cart Total', 'rewardsystem' ),
						),
					),
					array(
						'name'        => __( 'Reward Points for Getting Referred', 'rewardsystem' ),
						'id'          => 'rs_global_referral_reward_point_get_refer_for_cart_total',
						'class'       => 'rs_global_referral_reward_point_get_refer_for_cart_total show_if_enable_in_referral show_referral_based_on_cart_total',
						'std'         => '',
						'default'     => '',
						'type'        => 'text',
						'newids'      => 'rs_global_referral_reward_point_get_refer_for_cart_total',
						'placeholder' => '',
						'desc'        => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.',
							'rewardsystem'
						),
						'desc_tip'    => true,
					),
					array(
						'name'     => __( 'Reward Points in Percent % For Getting Referred', 'rewardsystem' ),
						'id'       => 'rs_global_referral_reward_percent_get_refer_for_cart_total',
						'class'    => 'rs_global_referral_reward_percent_get_refer_for_cart_total show_if_enable_in_referral show_referral_based_on_cart_total',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_global_referral_reward_percent_get_refer_for_cart_total',
						'desc'     => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.',
							'rewardsystem'
						),
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Enable to Send Email for Getting Referred Product Purchase Reward Points', 'rewardsystem' ),
						'desc'    => __( 'Enabling this option will send Getting Referred Product Purchase Points through Email', 'rewardsystem' ),
						'id'      => 'rs_send_mail_pdt_purchase_referrer',
						'class'   => 'rs_send_mail_pdt_purchase_referrer show_if_enable_in_referral',
						'type'    => 'checkbox',
						'std'     => $refered_mail,
						'default' => $refered_mail,
						'newids'  => 'rs_send_mail_pdt_purchase_referrer',
					),
					array(
						'name'    => __( 'Email Subject for Getting Referred Product Purchase Points', 'rewardsystem' ),
						'id'      => 'rs_email_subject_pdt_purchase_referrer',
						'class'   => 'rs_email_subject_pdt_purchase_referrer show_if_enable_in_referral',
						'std'     => 'Product Purchase Getting Referred - Noification',
						'default' => 'Product Purchase Getting Referred - Noification',
						'type'    => 'textarea',
						'newids'  => 'rs_email_subject_pdt_purchase_referrer',
					),
					array(
						'name'    => __( 'Email Message for Getting Referred Product Purchase Points', 'rewardsystem' ),
						'id'      => 'rs_email_message_pdt_purchase_referrer',
						'class'   => 'rs_email_message_pdt_purchase_referrer show_if_enable_in_referral',
						'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account',
						'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account',
						'type'    => 'textarea',
						'newids'  => 'rs_email_message_pdt_purchase_referrer',
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_global_referral_reward_points',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_hide_bulk_update_for_referral_product_purchase_start',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Referral Product Purchase Rewards Bulk Update Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => 'rs_update_setting',
					),
					array(
						'name'     => __( 'Product/Category Selection', 'rewardsystem' ),
						'id'       => 'rs_which_product_selection',
						'std'      => '1',
						'class'    => 'rs_which_product_selection',
						'default'  => '1',
						'newids'   => 'rs_which_product_selection',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'All Products', 'rewardsystem' ),
							'2' => __( 'Selected Products', 'rewardsystem' ),
							'3' => __( 'All Categories', 'rewardsystem' ),
							'4' => __( 'Selected Categories', 'rewardsystem' ),
						),
						'desc_tip' => true,
					),
					array(
						'name'   => __( 'Selected Particular Products', 'rewardsystem' ),
						'type'   => 'selected_products',
						'id'     => 'rs_select_particular_products',
						'class'  => 'rs_select_particular_products',
						'newids' => 'rs_select_particular_products',
					),
					array(
						'name'    => __( 'Select Particular Categories', 'rewardsystem' ),
						'id'      => 'rs_select_particular_categories',
						'css'     => 'min-width:350px;',
						'std'     => '1',
						'class'   => 'rs_select_particular_categories',
						'default' => '1',
						'newids'  => 'rs_select_particular_categories',
						'type'    => 'multiselect',
						'options' => $categorylist,
					),
					array(
						'name'     => __( 'Enable Referral Reward Points', 'rewardsystem' ),
						'id'       => 'rs_local_enable_disable_referral_reward',
						'std'      => '2',
						'default'  => '2',
						'desc_tip' => true,
						'desc'     => __(
							'Enable will Turn On Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
							. 'Disable will Turn Off Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available.',
							'rewardsystem'
						),
						'newids'   => 'rs_local_enable_disable_referral_reward',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Enable', 'rewardsystem' ),
							'2' => __( 'Disable', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Referral Reward Type', 'rewardsystem' ),
						'id'       => 'rs_local_referral_reward_type',
						'class'    => 'show_if_enable_in_update_referral',
						'std'      => '1',
						'default'  => '1',
						'newids'   => 'rs_local_referral_reward_type',
						'type'     => 'select',
						'desc_tip' => true,
						'options'  => array(
							'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
							'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Referral Reward Points', 'rewardsystem' ),
						'id'       => 'rs_local_referral_reward_point',
						'class'    => 'show_if_enable_in_update_referral',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_local_referral_reward_point',
						'desc'     => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.',
							'rewardsystem'
						),
						'desc_tip' => true,
					),
					array(
						'name'        => __( 'Referral Reward Points in Percent %', 'rewardsystem' ),
						'id'          => 'rs_local_referral_reward_percent',
						'class'       => 'show_if_enable_in_update_referral',
						'std'         => '',
						'default'     => '',
						'type'        => 'text',
						'newids'      => 'rs_local_referral_reward_percent',
						'placeholder' => '',
						'desc'        => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.',
							'rewardsystem'
						),
						'desc_tip'    => true,
					),
					array(
						'name'     => __( 'Getting Referred Reward Type', 'rewardsystem' ),
						'id'       => 'rs_local_referral_reward_type_get_refer',
						'class'    => 'show_if_enable_in_update_referral',
						'std'      => '1',
						'default'  => '1',
						'newids'   => 'rs_local_referral_reward_type_get_refer',
						'type'     => 'select',
						'desc_tip' => true,
						'options'  => array(
							'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
							'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
						),
					),
					array(
						'name'        => __( 'Referral Reward Points for Getting Referred', 'rewardsystem' ),
						'desc'        => __( 'Please Enter Referral Reward Points for getting referred', 'rewardsystem' ),
						'id'          => 'rs_local_referral_reward_point_for_getting_referred',
						'class'       => 'show_if_enable_in_update_referral',
						'std'         => '',
						'default'     => '',
						'type'        => 'text',
						'newids'      => 'rs_local_referral_reward_point_for_getting_referred',
						'placeholder' => '',
						'desc_tip'    => true,
					),
					array(
						'name'        => __( 'Referral Reward Points in Percent % for Getting Referred', 'rewardsystem' ),
						'desc'        => __( 'Please Enter Percentage value of Reward Points for getting referred', 'rewardsystem' ),
						'id'          => 'rs_local_referral_reward_percent_for_getting_referred',
						'class'       => 'show_if_enable_in_update_referral',
						'std'         => '',
						'default'     => '',
						'type'        => 'text',
						'newids'      => 'rs_local_referral_reward_percent_for_getting_referred',
						'placeholder' => '',
						'desc_tip'    => true,
					),
					array(
						'type' => 'referral_button',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'rs_update_setting',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_hide_bulk_update_for_referral_product_purchase_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Referral Product Purchase Reward Points by Guest Users', 'rewardsystem' ),
						'type' => 'title',
						'id'   => 'rs_referrer_earn_point_by_guest_users',
					),
					array(
						'name'    => __( 'Referral Product Purchase Reward Points by Guest Users', 'rewardsystem' ),
						'type'    => 'checkbox',
						'id'      => 'rs_referrer_earn_point_purchase_by_guest_users',
						'newids'  => 'rs_referrer_earn_point_purchase_by_guest_users',
						'std'     => 'no',
						'default' => 'no',
						'desc'    => __( 'By enabling this checkbox, you can allow referrer to earn referral product purchase points by the guest user(s)', 'rewardsystem' ),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'rs_referrer_earn_point_through_guest_users',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Referral Sign up Reward Points', 'rewardsystem' ),
						'type' => 'title',
						'id'   => 'rs_referral_action_setting',
					),
					array(
						'name'    => __( 'Enable Referral Signup Reward Points', 'rewardsystem' ),
						'desc'    => __( 'Enable this option for Referral Signup Reward Points', 'rewardsystem' ),
						'type'    => 'checkbox',
						'id'      => '_rs_referral_enable_signups',
						'newids'  => '_rs_referral_enable_signups',
						'std'     => 'yes',
						'default' => 'yes',
					),
					array(
						'name'     => __( 'Referral Account Sign up Reward Points is Awarded ', 'rewardsystem' ),
						'desc'     => __( 'Select Referral Reward Account Sign up Points Reward type ', 'rewardsystem' ),
						'id'       => 'rs_select_referral_points_award',
						'type'     => 'select',
						'newids'   => 'rs_select_referral_points_award',
						'std'      => '1',
						'default'  => '1',
						'options'  => array(
							'1' => __( 'Instantly', 'rewardsystem' ),
							'2' => __( 'After Referral Places Minimum Number of Successful Order(s)', 'rewardsystem' ),
							'3' => __( 'After Referral Spents the Minimum Amount in Site', 'rewardsystem' ),
						),
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Number of Successful Order(s)', 'rewardsystem' ),
						'desc'     => __( 'Please Enter the Minimum Number Of Sucessful Orders', 'rewardsystem' ),
						'id'       => 'rs_number_of_order_for_referral_points',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_number_of_order_for_referral_points',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Minimum Amount to be Spent by the User', 'rewardsystem' ),
						'desc'     => __( 'Please Enter the Minimum Amount Spent by User', 'rewardsystem' ),
						'id'       => 'rs_amount_of_order_for_referral_points',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_amount_of_order_for_referral_points',
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Account Sign up Referral Reward Points after First Purchase', 'rewardsystem' ),
						'desc'    => __( 'Enable this option to award referral reward points for account signup after first purchase', 'rewardsystem' ),
						'id'      => 'rs_referral_reward_signup_after_first_purchase',
						'std'     => 'no',
						'default' => 'no',
						'type'    => 'checkbox',
						'newids'  => 'rs_referral_reward_signup_after_first_purchase',
					),
					array(
						'name'     => __( 'Referral Reward Points for Account Sign up', 'rewardsystem' ),
						'desc'     => __( 'Please Enter the Referral Reward Points that will be earned for Account Sign up', 'rewardsystem' ),
						'id'       => 'rs_referral_reward_signup',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_referral_reward_signup',
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Referral Account Signup Bonus Points', 'rewardsystem' ),
						'desc'    => __( 'By enabling this option, you can award bonus points to your users when they bring more users to your site.', 'rewardsystem' ),
						'id'      => 'rs_enable_referral_bonus_reward_signup',
						'std'     => 'no',
						'default' => 'no',
						'type'    => 'checkbox',
						'newids'  => 'rs_enable_referral_bonus_reward_signup',
					),
					array(
						'name'    => __( 'Awarding Type', 'rewardsystem' ),
						'id'      => 'rs_referral_reward_signup_bonus',
						'type'    => 'select',
						'newids'  => 'rs_referral_reward_signup_bonus',
						'std'     => '1',
						'default' => '1',
						'options' => array(
							'1' => __( 'Only Once', 'rewardsystem' ),
							'2' => __( 'Recurring', 'rewardsystem' ),
						),
					),
					array(
						'type' => 'get_reward_signup_bonus',
					),
					array(
						'name'    => __( 'Enter the Points Value', 'rewardsystem' ),
						'id'      => 'rs_referral_reward_signup_bonus_points',
						'std'     => '',
						'default' => '',
						'type'    => 'text',
						'newids'  => 'rs_referral_reward_signup_bonus_points',
					),
					array(
						'name'    => __( 'Enable To Send Mail For Referral Account Signup Reward Points', 'rewardsystem' ),
						'desc'    => __( 'Enabling this option will send Referral Account Signup Points through Mail', 'rewardsystem' ),
						'id'      => 'rs_send_mail_referral_signup',
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_send_mail_referral_signup',
					),
					array(
						'name'    => __( 'Email Subject For Referral Account Signup Points', 'rewardsystem' ),
						'id'      => 'rs_email_subject_referral_signup',
						'std'     => 'ReferralAccount Signup - Notification',
						'default' => 'ReferralAccount Signup - Notification',
						'type'    => 'textarea',
						'newids'  => 'rs_email_subject_referral_signup',
					),
					array(
						'name'    => __( 'Email Message For Referral Account Signup Points', 'rewardsystem' ),
						'id'      => 'rs_email_message_referral_signup',
						'std'     => 'You have earned [rs_earned_points] points for referred [rs_user_name] and currently you have [rs_available_points] in your account',
						'default' => 'You have earned [rs_earned_points] points for referred [rs_user_name] and currently you have [rs_available_points] in your account',
						'type'    => 'textarea',
						'newids'  => 'rs_email_message_referral_signup',
					),
					array(
						'name'     => __( 'Enable Reward Points for Getting Referred', 'rewardsystem' ),
						'desc'     => __( 'Enable the Reward Points that will be earned for Getting Referred', 'rewardsystem' ),
						'id'       => 'rs_referral_reward_signup_getting_refer',
						'type'     => 'select',
						'newids'   => 'rs_referral_reward_signup_getting_refer',
						'std'      => '2',
						'default'  => '2',
						'options'  => array(
							'1' => __( 'Enable', 'rewardsystem' ),
							'2' => __( 'Disable', 'rewardsystem' ),
						),
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Enable Reward Points for Getting Referred after first purchase', 'rewardsystem' ),
						'desc'    => __( 'Enable the Reward Points that will be earned for Getting Referred after first purchase', 'rewardsystem' ),
						'id'      => 'rs_referral_reward_getting_refer_after_first_purchase',
						'std'     => 'no',
						'default' => 'no',
						'type'    => 'checkbox',
						'newids'  => 'rs_referral_reward_getting_refer_after_first_purchase',
					),
					array(
						'name'     => __( 'Reward Points for Getting Referred', 'rewardsystem' ),
						'desc'     => __( 'Please Enter the Reward Points that will be earned for Getting Referred', 'rewardsystem' ),
						'id'       => 'rs_referral_reward_getting_refer',
						'std'      => '1000',
						'default'  => '1000',
						'type'     => 'text',
						'newids'   => 'rs_referral_reward_getting_refer',
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Enable To Send Mail For Getting Referred Reward Points', 'rewardsystem' ),
						'desc'    => __( 'Enabling this option will send Getting Referred Points through Mail', 'rewardsystem' ),
						'id'      => 'rs_send_mail_getting_referred',
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_send_mail_getting_referred',
					),
					array(
						'name'    => __( 'Email Subject For Getting Referred Points', 'rewardsystem' ),
						'id'      => 'rs_email_subject_getting_referred',
						'std'     => 'Getting Referred - Notification',
						'default' => 'Getting Referred - Notification',
						'type'    => 'textarea',
						'newids'  => 'rs_email_subject_getting_referred',
					),
					array(
						'name'    => __( 'Email Message For Getting Referred Points', 'rewardsystem' ),
						'id'      => 'rs_email_message_getting_referred',
						'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account',
						'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account',
						'type'    => 'textarea',
						'newids'  => 'rs_email_message_getting_referred',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'rs_referral_action_setting',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Generate Referral Link Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_my_generate_referral_settings',
					),
					array(
						'name'    => __( 'Generate Referral Link', 'rewardsystem' ),
						'id'      => 'rs_show_hide_generate_referral',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_generate_referral',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Referral System of SUMO Reward Points is accessible by', 'rewardsystem' ),
						'id'       => 'rs_select_type_of_user_for_referral',
						'css'      => 'min-width:100px;',
						'std'      => '1',
						'default'  => '1',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'All Users', 'rewardsystem' ),
							'2' => __( 'Include User(s)', 'rewardsystem' ),
							'3' => __( 'Exclude User(s)', 'rewardsystem' ),
							'4' => __( 'Include User Role(s)', 'rewardsystem' ),
							'5' => __( 'Exclude User Role(s)', 'rewardsystem' ),
						),
						'newids'   => 'rs_select_type_of_user_for_referral',
						'desc'     => __( 'Referral System includes Referral Table,Refer A Friend Form and Generate Referral Link', 'rewardsystem' ),
						'desc_tip' => true,
					),
					array(
						'type' => 'rs_select_user_for_referral_link',
					),
					array(
						'type' => 'rs_select_exclude_user_for_referral_link',
					),
					array(
						'name'        => __( 'Select the User Role for Providing access to Referral System', 'rewardsystem' ),
						'id'          => 'rs_select_users_role_for_show_referral_link',
						'css'         => 'min-width:343px;',
						'std'         => '',
						'default'     => '',
						'placeholder' => 'Select for a User Role',
						'type'        => 'multiselect',
						'options'     => $newcombineduserrole,
						'newids'      => 'rs_select_users_role_for_show_referral_link',
						'desc_tip'    => false,
					),
					array(
						'name'        => __( 'Select the User Role for Preventing access to Referral System', 'rewardsystem' ),
						'id'          => 'rs_select_exclude_users_role_for_show_referral_link',
						'css'         => 'min-width:343px;',
						'std'         => '',
						'default'     => '',
						'placeholder' => 'Select for a User Role',
						'type'        => 'multiselect',
						'options'     => $newcombineduserrole,
						'newids'      => 'rs_select_exclude_users_role_for_show_referral_link',
						'desc_tip'    => false,
					),
					array(
						'name'     => __( 'Fallback Message for Referral Restriction', 'rewardsystem' ),
						'id'       => 'rs_display_msg_when_access_is_prevented',
						'std'      => '1',
						'default'  => '1',
						'desc_tip' => true,
						'newids'   => 'rs_display_msg_when_access_is_prevented',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
					),
					array(
						'name'    => __( 'Fallback Message for Referral Restriction', 'rewardsystem' ),
						'id'      => 'rs_msg_for_restricted_user',
						'std'     => 'Referral System is currently restricted for your account',
						'default' => 'Referral System is currently restricted for your account',
						'type'    => 'text',
						'newids'  => 'rs_msg_for_restricted_user',
					),
					array(
						'name'    => __( 'Referral System Restriction based on Purchase History', 'rewardsystem' ),
						'id'      => 'rs_enable_referral_link_generate_after_first_order',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_enable_referral_link_generate_after_first_order',
						'type'    => 'checkbox',
						'desc'    => __( 'By enabling this option, you can restrict the users to participate in the Referral System', 'rewardsystem' ),
					),
					array(
						'name'     => __( 'Restrict Referral System based on', 'rewardsystem' ),
						'id'       => 'rs_referral_link_generated_settings',
						'std'      => '1',
						'default'  => '1',
						'newids'   => 'rs_referral_link_generated_settings',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Number of Successful Order(s)', 'rewardsystem' ),
							'2' => __( 'Total Amount Spent on the site', 'rewardsystem' ),
						),
						'desc_tip' => true,
					),
					array(
						'name'              => __( 'Enter the number of orders', 'rewardsystem' ),
						'id'                => 'rs_getting_number_of_orders',
						'std'               => '',
						'default'           => '',
						'newids'            => 'rs_getting_number_of_orders',
						'type'              => 'number',
						'custom_attributes' => array(
							'min' => 0,
						),
						'desc_tip'          => true,
					),
					array(
						'name'              => __( 'Enter the value', 'rewardsystem' ),
						'id'                => 'rs_number_of_amount_spent',
						'std'               => '',
						'default'           => '',
						'newids'            => 'rs_number_of_amount_spent',
						'type'              => 'number',
						'desc_tip'          => true,
						'custom_attributes' => array(
							'min' => 0,
						),
					),
					array(
						'name'    => __( 'Referral System can accessible only when the order status reaches', 'rewardsystem' ),
						'id'      => 'rs_set_order_status_for_generate_link',
						'std'     => array( 'completed' ),
						'default' => array( 'completed' ),
						'type'    => 'multiselect',
						'class'   => 'wc-enhanced-select',
						'options' => $newcombinedarray,
						'newids'  => 'rs_set_order_status_for_generate_link',
					),
					array(
						'name'    => __( 'Generate Referral Link Label', 'rewardsystem' ),
						'id'      => 'rs_generate_link_label',
						'std'     => 'Generate Referral Link',
						'default' => 'Generate Referral Link',
						'type'    => 'text',
						'newids'  => 'rs_generate_link_label',
					),
					array(
						'name'    => __( 'S.No Label', 'rewardsystem' ),
						'id'      => 'rs_generate_link_sno_label',
						'std'     => 'S.No',
						'default' => 'S.No',
						'type'    => 'text',
						'newids'  => 'rs_generate_link_sno_label',
					),
					array(
						'name'    => __( 'Date Label', 'rewardsystem' ),
						'id'      => 'rs_generate_link_date_label',
						'std'     => 'Date',
						'default' => 'Date',
						'type'    => 'text',
						'newids'  => 'rs_generate_link_date_label',
					),
					array(
						'name'    => __( 'Referral Link Label', 'rewardsystem' ),
						'id'      => 'rs_generate_link_referrallink_label',
						'std'     => 'Referral Link',
						'default' => 'Referral Link',
						'type'    => 'text',
						'newids'  => 'rs_generate_link_referrallink_label',
					),
					array(
						'name'    => __( 'Social Label', 'rewardsystem' ),
						'id'      => 'rs_generate_link_social_label',
						'std'     => 'Social',
						'default' => 'Social',
						'type'    => 'text',
						'newids'  => 'rs_generate_link_social_label',
					),
					array(
						'name'    => __( 'Action Label', 'rewardsystem' ),
						'id'      => 'rs_generate_link_action_label',
						'std'     => 'Action',
						'default' => 'Action',
						'type'    => 'text',
						'newids'  => 'rs_generate_link_action_label',
					),
					array(
						'name'    => __( 'Referral Link Hover Label', 'rewardsystem' ),
						'id'      => 'rs_generate_link_hover_label',
						'std'     => 'Click this button to generate the referral link',
						'default' => 'Click this button to generate the referral link',
						'type'    => 'text',
						'newids'  => 'rs_generate_link_hover_label',
					),
					array(
						'name'    => __( 'Generate Referral Link Button Label', 'rewardsystem' ),
						'id'      => 'rs_generate_link_button_label',
						'std'     => 'Generate Referral Link',
						'default' => 'Generate Referral Link',
						'type'    => 'text',
						'newids'  => 'rs_generate_link_button_label',
					),
					array(
						'name'     => __( 'Generate Referral Link based on Username/User ID', 'rewardsystem' ),
						'id'       => 'rs_generate_referral_link_based_on_user',
						'std'      => '1',
						'default'  => '1',
						'newids'   => 'rs_generate_referral_link_based_on_user',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Username', 'rewardsystem' ),
							'2' => __( 'User ID', 'rewardsystem' ),
						),
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Copy Referral Link', 'rewardsystem' ),
						'id'      => 'rs_enable_copy_to_clipboard',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_enable_copy_to_clipboard',
						'type'    => 'checkbox',
						'desc'    => __( 'By enabling this checkbox, users will have the option to copy the referral link', 'rewardsystem' ),
					),
					array(
						'name'    => __( 'Type of Referral Link to be displayed', 'rewardsystem' ),
						'id'      => 'rs_show_hide_generate_referral_link_type',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_generate_referral_link_type',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Default', 'rewardsystem' ),
							'2' => __( 'Static Url', 'rewardsystem' ),
						),
					),
					array(
						'name'    => __( 'Prefill Generate Referral Link', 'rewardsystem' ),
						'id'      => 'rs_prefill_generate_link',
						'std'     => site_url(),
						'default' => site_url(),
						'type'    => 'text',
						'newids'  => 'rs_prefill_generate_link',
					),
					array(
						'name'    => __( 'My Referral Link Label', 'rewardsystem' ),
						'id'      => 'rs_my_referral_link_button_label',
						'std'     => 'My Referral Link',
						'default' => 'My Referral Link',
						'type'    => 'text',
						'newids'  => 'rs_my_referral_link_button_label',
					),
					array(
						'name'    => __( 'Static Referral Link', 'rewardsystem' ),
						'id'      => 'rs_static_generate_link',
						'std'     => site_url(),
						'default' => site_url(),
						'type'    => 'text',
						'newids'  => 'rs_static_generate_link',
					),
					array(
						'name'    => __( 'Referral Link Table Position', 'rewardsystem' ),
						'id'      => 'rs_display_generate_referral',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_display_generate_referral',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Before My Account ', 'rewardsystem' ),
							'2' => __( 'After My Account', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Extra Class Name for Generate Referral Link Button', 'rewardsystem' ),
						'desc'     => __( 'Add Extra Class Name to the My Account Generate Referral Link Button, Don\'t Enter dot(.) before Class Name', 'rewardsystem' ),
						'id'       => 'rs_extra_class_name_generate_referral_link',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_extra_class_name_generate_referral_link',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Social Share Button', 'rewardsystem' ),
						'id'       => 'rs_account_show_hide_social_share_button',
						'std'      => '1',
						'default'  => '1',
						'desc_tip' => true,
						'newids'   => 'rs_account_show_hide_social_share_button',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Facebook Share Button', 'rewardsystem' ),
						'id'       => 'rs_account_show_hide_facebook_share_button',
						'std'      => '1',
						'default'  => '1',
						'desc_tip' => true,
						'newids'   => 'rs_account_show_hide_facebook_share_button',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Title used for Facebook Share', 'rewardsystem' ),
						'desc'     => __( 'Enter the title of website that shown in Facebook Share', 'rewardsystem' ),
						'type'     => 'text',
						'id'       => 'rs_facebook_title',
						'std'      => get_bloginfo(),
						'default'  => get_bloginfo(),
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Description used for Facebook Share', 'rewardsystem' ),
						'desc'     => __( 'Enter the description of website that shown in Facebook Share', 'rewardsystem' ),
						'type'     => 'text',
						'id'       => 'rs_facebook_description',
						'std'      => get_option( 'blogdescription' ),
						'default'  => get_option( 'blogdescription' ),
						'desc_tip' => true,
					),
					array(
						'type' => 'image_uploader',
					),
					array(
						'name'     => __( 'Twitter Tweet Button', 'rewardsystem' ),
						'id'       => 'rs_account_show_hide_twitter_tweet_button',
						'std'      => '1',
						'default'  => '1',
						'desc_tip' => true,
						'newids'   => 'rs_account_show_hide_twitter_tweet_button',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Description', 'rewardsystem' ),
						'type'     => 'text',
						'id'       => 'rs_twitter_share_text',
						'std'      => get_option( 'blogdescription' ),
						'default'  => get_option( 'blogdescription' ),
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Whatsapp Button', 'rewardsystem' ),
						'id'       => 'rs_acount_show_hide_whatsapp_button',
						'std'      => '1',
						'default'  => '1',
						'desc_tip' => true,
						'newids'   => 'rs_acount_show_hide_whatsapp_button',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
					),
					array(
						'name'    => __( 'Email Button', 'rewardsystem' ),
						'id'      => 'rs_acount_show_hide_email_button',
						'std'     => '1',
						'default' => '1',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
						'newids'  => 'rs_acount_show_hide_email_button',
					),
					array(
						'name'     => __( 'Prefilled Subject Text', 'rewardsystem' ),
						'desc'     => __( 'This Message will be displayed in the Subject field', 'rewardsystem' ),
						'id'       => 'rs_email_referral_subject',
						'std'      => 'Referral Link',
						'default'  => 'Referral Link',
						'type'     => 'textarea',
						'newids'   => 'rs_email_referral_subject',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Prefilled Message for Refer a Friend', 'rewardsystem' ),
						'desc'     => __( 'This Message will be displayed in the Message field along with the Referral link', 'rewardsystem' ),
						'std'      => 'Hi,<br/>Please make use of this link([site_referral_url]) & earn points for the actions offered[eg. Account Signup, Product Purchase, Referring Others etc] in the site.<br/><br/><br/>Regards.',
						'id'       => 'rs_email_referral_message',
						'default'  => 'Hi,<br/>Please make use of this link([site_referral_url]) & earn points for the actions offered[eg. Account Signup, Product Purchase, Referring Others etc] in the site.<br/><br/><br/>Regards.',
						'type'     => 'textarea',
						'newids'   => 'rs_email_referral_message',
						'desc_tip' => true,
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_my_generate_referral_settings',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Referral Link Settings For Shortcode', 'rewardsystem' ),
						'type' => 'title',
						'id'   => 'rs_referral_link_short_code',
					),
					array(
						'name'     => __( 'Static Referral Link', 'rewardsystem' ),
						'id'       => '_rs_static_referral_link',
						'std'      => '2',
						'default'  => '2',
						'newids'   => '_rs_static_referral_link',
						'type'     => 'select',
						'desc_tip' => false,
						'options'  => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'rs_referral_link_short_code',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Referrer Earning Restriction Settings', 'rewardsystem' ),
						'type' => 'title',
						'desc' => __( 'For eg: If A Refers B then A is the Referrer and B is the Referral', 'rewardsystem' ),
						'id'   => '_rs_ban_referee_points_time',
					),
					array(
						'name'     => __( 'Referrer should earn points only after the user(Buyer or Referral) is X days old', 'rewardsystem' ),
						'id'       => '_rs_select_referral_points_referee_time',
						'std'      => '1',
						'default'  => '1',
						'newids'   => '_rs_select_referral_points_referee_time',
						'type'     => 'select',
						'desc_tip' => false,
						'options'  => array(
							'1' => __( 'Unlimited', 'rewardsystem' ),
							'2' => __( 'Limited', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Number of Day(s)', 'rewardsystem' ),
						'desc'     => __( 'Enter Fixed Number greater than or equal to 0', 'rewardsystem' ),
						'id'       => '_rs_select_referral_points_referee_time_content',
						'newids'   => '_rs_select_referral_points_referee_time_content',
						'type'     => 'text',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'If the Referred Person\'s account is deleted, the Referral Points', 'rewardsystem' ),
						'id'       => '_rs_reward_referal_point_user_deleted',
						'std'      => '2',
						'default'  => '2',
						'newids'   => '_rs_reward_referal_point_user_deleted',
						'type'     => 'select',
						'desc_tip' => false,
						'options'  => array(
							'1' => __( 'Should be Revoked', 'rewardsystem' ),
							'2' => __( 'Shouldn\'t be Revoked', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Applies for Referral account created', 'rewardsystem' ),
						'id'       => '_rs_time_validity_to_redeem',
						'std'      => '1',
						'default'  => '1',
						'newids'   => '_rs_time_validity_to_redeem',
						'type'     => 'select',
						'desc_tip' => false,
						'options'  => array(
							'1' => __( 'Any time', 'rewardsystem' ),
							'2' => __( 'Within specific number of days', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Number of Day(s)', 'rewardsystem' ),
						'desc'     => __( 'Enter Fixed Number greater than or equal to 0', 'rewardsystem' ),
						'id'       => '_rs_days_for_redeeming_points',
						'newids'   => '_rs_days_for_redeeming_points',
						'type'     => 'text',
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Restrict Referral Product Purchase Reward Points when more than one quantity of the product is purchased by the Referred Person', 'rewardsystem' ),
						'id'      => 'rs_restrict_referral_reward',
						'desc'    => __( 'By enabling this option, one quantity of the points will be awarded to referrer if referred person purchase more than one quantity of the product', 'rewardsystem' ),
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_restrict_referral_reward',
					),
					array(
						'name'    => __( 'Restrict Referral Product Purchase Reward Points when more than one Referrer refer same Referral', 'rewardsystem' ),
						'id'      => 'rs_restrict_referral_points_for_multiple_referrer',
						'desc'    => __( 'By enabling this option, the referral points will be awarded only for first referrer when multiple referrer refer same referral', 'rewardsystem' ),
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_restrict_referral_points_for_multiple_referrer',
					),
					array(
						'name'    => __( 'Restrict Referral & Getting Referred Reward Points based on Free Shipping', 'rewardsystem' ),
						'desc'    => __( 'By enabling this option, you can restrict referral & getting referred reward points when woocommerce free shipping is applied on order.', 'rewardsystem' ),
						'id'      => 'rs_restrict_referral_system_when_free_shipping_is_enabled',
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_restrict_referral_system_when_free_shipping_is_enabled',
					),
					array(
						'name'    => __( 'Restrict Referral Product Purchase Reward Points when Referrer and Referral IP is same', 'rewardsystem' ),
						'id'      => 'rs_restrict_referral_points_for_same_ip',
						'desc'    => __( 'By enabling this option, the referral points will not be awarded when referrer and referral IP is same', 'rewardsystem' ),
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_restrict_referral_points_for_same_ip',
					),
					array(
						'name'    => __( 'Exclude Shipping Cost', 'rewardsystem' ),
						'desc'    => __( 'By enabling this checkbox, referral product purchase points & getting referred points will be awarded without shipping cost. Note: Works with WooCommerce v3.2.0 or Above', 'rewardsystem' ),
						'id'      => 'rs_exclude_shipping_cost_based_on_cart_total_for_referral_module',
						'class'   => 'show_if_enable_in_referral',
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_exclude_shipping_cost_based_on_cart_total_for_referral_module',
					),
					array(
						'name'    => __( 'Calculate Referral Reward Points after Discounts(WooCommerce Coupons / Points Redeeming)', 'rewardsystem' ),
						'id'      => 'rs_referral_points_after_discounts',
						'desc'    => __( 'Enabling this option will calculate referral reward points for the price after excluding the coupon/ points redeeming discounts ', 'rewardsystem' ),
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_referral_points_after_discounts',
					),
					array(
						'name'    => __( 'Restrict Referral Product Purchase Reward Points based on Existing Users', 'rewardsystem' ),
						'desc'    => __( 'By enabling this checkbox, the referrer cannot earn referral product purchase points when a referred person already exists on the site', 'rewardsystem' ),
						'id'      => 'rs_restrict_referral_points_old_user_not_in_referral_system',
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_restrict_referral_points_old_user_not_in_referral_system',
					),
					array(
						'name'    => __( 'Restrict Referral Product Purchase Points based on Sale Price Products', 'rewardsystem' ),
						'id'      => 'rs_restrict_sale_price_product_points_referral_system',
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'desc'    => __( 'By enabling this checkbox, you can restrict awarding referral product purchase points to the referrer when a referred person purchases products that have a sale price in the order.', 'rewardsystem' ),
						'newids'  => 'rs_restrict_sale_price_product_points_referral_system',
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_ban_referee_points_time',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Referrer Label Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_referrer_label_settings',
					),
					array(
						'name'    => __( 'To display the Message to Referral Person', 'rewardsystem' ),
						'id'      => 'rs_show_hide_generate_referral_message',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_generate_referral_message',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
					),
					array(
						'name'    => __( 'Select to Send Message by ', 'rewardsystem' ),
						'id'      => 'rs_send_message_by_referrer',
						'std'     => '1',
						'default' => '1',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Referrer User Name', 'rewardsystem' ),
							'2' => __( 'Referrer First Name', 'rewardsystem' ),
						),
						'newids'  => 'rs_send_message_by_referrer',
					),
					array(
						'name'    => __( 'Message to display the Referral Person', 'rewardsystem' ),
						'id'      => 'rs_show_hide_generate_referral_message_text',
						'std'     => 'You are being referred by [rs_referrer_name]',
						'default' => 'You are being referred by [rs_referrer_name]',
						'type'    => 'textarea',
						'newids'  => 'rs_show_hide_generate_referral_message_text',
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_referrer_label_settings',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'My Referral Table Label Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_my_referal_label_settings',
					),
					array(
						'name'    => __( 'Referral Table ', 'rewardsystem' ),
						'id'      => 'rs_show_hide_referal_table',
						'std'     => '2',
						'default' => '2',
						'newids'  => 'rs_show_hide_referal_table',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Referral Table Label', 'rewardsystem' ),
						'desc'     => __( 'Enter the Referral Table Label', 'rewardsystem' ),
						'id'       => 'rs_referal_table_title',
						'std'      => 'Referral Table',
						'default'  => 'Referral Table',
						'type'     => 'text',
						'newids'   => 'rs_referal_table_title',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'S.No Label', 'rewardsystem' ),
						'desc'     => __( 'Enter the Serial Number Label', 'rewardsystem' ),
						'id'       => 'rs_my_referal_sno_label',
						'std'      => 'S.No',
						'default'  => 'S.No',
						'type'     => 'text',
						'newids'   => 'rs_my_referal_sno_label',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Select Referral Option for ', 'rewardsystem' ),
						'id'       => 'rs_select_option_for_referral',
						'std'      => '1',
						'default'  => '1',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Username', 'rewardsystem' ),
							'2' => __( 'Email ID', 'rewardsystem' ),
						),
						'newids'   => 'rs_select_option_for_referral',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Referral Username Label', 'rewardsystem' ),
						'desc'     => __( 'Enter the Referral Username Label', 'rewardsystem' ),
						'id'       => 'rs_my_referal_userid_label',
						'std'      => 'Username',
						'default'  => 'Username',
						'type'     => 'text',
						'newids'   => 'rs_my_referal_userid_label',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Referral Email Label', 'rewardsystem' ),
						'desc'     => __( 'Enter the Referral Email label', 'rewardsystem' ),
						'id'       => 'rs_referral_email_ids',
						'std'      => 'Email ID',
						'default'  => 'Email ID',
						'type'     => 'text',
						'newids'   => 'rs_referral_email_ids',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Total Referral Points Label', 'rewardsystem' ),
						'desc'     => __( 'Enter the Total Referral Points Label', 'rewardsystem' ),
						'id'       => 'rs_my_total_referal_points_label',
						'std'      => 'Total Referral Points',
						'default'  => 'Total Referral Points',
						'type'     => 'text',
						'newids'   => 'rs_my_total_referal_points_label',
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Referral Table - Shortcode', 'rewardsystem' ),
						'id'      => 'rs_show_hide_referal_table_shortcode',
						'std'     => '2',
						'default' => '2',
						'newids'  => 'rs_show_hide_referal_table_shortcode',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show', 'rewardsystem' ),
							'2' => __( 'Hide', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Referral Table Label', 'rewardsystem' ),
						'desc'     => __( 'Enter the Referral Table Label', 'rewardsystem' ),
						'id'       => 'rs_referal_table_title_shortcode',
						'std'      => 'Referral Table',
						'default'  => 'Referral Table',
						'type'     => 'text',
						'newids'   => 'rs_referal_table_title_shortcode',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'S.No Label', 'rewardsystem' ),
						'desc'     => __( 'Enter the Serial Number Label', 'rewardsystem' ),
						'id'       => 'rs_my_referal_sno_label_shortcode',
						'std'      => 'S.No',
						'default'  => 'S.No',
						'type'     => 'text',
						'newids'   => 'rs_my_referal_sno_label_shortcode',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Select Referral Option for ', 'rewardsystem' ),
						'id'       => 'rs_select_option_for_referral_shortcode',
						'std'      => '1',
						'default'  => '1',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'Username', 'rewardsystem' ),
							'2' => __( 'Email ID', 'rewardsystem' ),
						),
						'newids'   => 'rs_select_option_for_referral_shortcode',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Referral Username Label', 'rewardsystem' ),
						'desc'     => __( 'Enter the Referral Username Label', 'rewardsystem' ),
						'id'       => 'rs_my_referal_userid_label_shortcode',
						'std'      => 'Username',
						'default'  => 'Username',
						'type'     => 'text',
						'newids'   => 'rs_my_referal_userid_label_shortcode',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Referral Email Label', 'rewardsystem' ),
						'desc'     => __( 'Enter the Referral Email label', 'rewardsystem' ),
						'id'       => 'rs_referral_email_ids_shortcode',
						'std'      => 'Email ID',
						'default'  => 'Email ID',
						'type'     => 'text',
						'newids'   => 'rs_referral_email_ids_shortcode',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Total Referral Points Label', 'rewardsystem' ),
						'desc'     => __( 'Enter the Total Referral Points Label', 'rewardsystem' ),
						'id'       => 'rs_my_total_referal_points_label_shortcode',
						'std'      => 'Total Referral Points',
						'default'  => 'Total Referral Points',
						'type'     => 'text',
						'newids'   => 'rs_my_total_referal_points_label_shortcode',
						'desc_tip' => true,
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_my_referal_label_settings',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Refer a Friend Form Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_reward_referfriend_status',
					),
					array(
						'name'    => __( 'Enable Friend Form Settings', 'rewardsystem' ),
						'id'      => 'rs_enable_message_for_friend_form',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_enable_message_for_friend_form',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Enable', 'rewardsystem' ),
							'2' => __( 'Disable', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'Friend Name Label', 'rewardsystem' ),
						'desc'     => __( 'Enter Friend Name Label which will be available in Frontend when you use shortcode', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_name_label',
						'std'      => 'Your Friend Name',
						'default'  => 'Your Friend Name',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_name_label',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Friend Name Field Placeholder', 'rewardsystem' ),
						'desc'     => __( 'Enter Friend Name Field Placeholder which will be appear in frontend when you use shortcode', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_name_placeholder',
						'std'      => 'Enter your Friend Name',
						'default'  => 'Enter your Friend Name',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_name_placeholder',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Friend Email Label', 'rewardsystem' ),
						'desc'     => __( 'Enter Friend Email Label which will be available in Frontend when you use shortcode', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_email_label',
						'std'      => 'Your Friend Email',
						'default'  => 'Your Friend Email',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_email_label',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Friend Email Field Placeholder', 'rewardsystem' ),
						'desc'     => __( 'Enter Friend Email Field Placeholder which will be appear in frontend when you use shortcode', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_email_placeholder',
						'std'      => 'Enter your Friend Email',
						'default'  => 'Enter your Friend Email',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_email_placeholder',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Friend Email Subject Label', 'rewardsystem' ),
						'desc'     => __( 'Enter Friend Subject which will be appear in Frontend when you use shortcode', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_subject_label',
						'std'      => 'Your Subject',
						'default'  => 'Your Subject',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_subject_label',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Friend Email Subject Field Placeholder', 'rewardsystem' ),
						'desc'     => __( 'Enter Friend Email Subject Field Placeholder which will be appear in frontend when you use shortcode', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_email_subject_placeholder',
						'std'      => 'Enter your Subject',
						'default'  => 'Enter your Subject',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_email_subject_placeholder',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Friend Email Message Label', 'rewardsystem' ),
						'desc'     => __( 'Enter Friend Email Message which will be appear in frontend when you use shortcode', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_message_label',
						'std'      => 'Your Message',
						'default'  => 'Your Message',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_message_label',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Friend Email Message Field Placeholder', 'rewardsystem' ),
						'desc'     => __( 'Enter Friend Email Message Field Placeholder which will be appear in frontend when you use shortcode', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_email_message_placeholder',
						'std'      => 'Enter your Message',
						'default'  => 'Enter your Message',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_email_message_placeholder',
						'desc_tip' => true,
					),
					array(
						'name'     => __( ' Email Subject Type', 'rewardsystem' ),
						'id'       => 'rs_allow_user_to_request_prefilled_subject',
						'std'      => '1',
						'default'  => '1',
						'type'     => 'select',
						'newids'   => 'rs_allow_user_to_request_prefilled_subject',
						'options'  => array(
							'1' => __( 'Editable', 'rewardsystem' ),
							'2' => __( 'Non-Editable', 'rewardsystem' ),
						),
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Prefilled Subject Text', 'rewardsystem' ),
						'desc'     => __( 'This Message will be displayed in the Subject field', 'rewardsystem' ),
						'id'       => 'rs_subject_field',
						'std'      => 'Referral Link',
						'default'  => 'Referral Link',
						'type'     => 'textarea',
						'newids'   => 'rs_subject_field',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Prefilled Heading Text', 'rewardsystem' ),
						'desc'     => __( 'This Message will be displayed in the Heading field', 'rewardsystem' ),
						'id'       => 'rs_heading_field',
						'std'      => 'Referral Link',
						'default'  => 'Referral Link',
						'type'     => 'textarea',
						'newids'   => 'rs_heading_field',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Allow User to Enter the Prefilled Message for Refer a Friend', 'rewardsystem' ),
						'id'       => 'rs_allow_user_to_request_prefilled_message',
						'std'      => '1',
						'default'  => '1',
						'type'     => 'select',
						'newids'   => 'rs_allow_user_to_request_prefilled_message',
						'options'  => array(
							'1' => __( 'Editable', 'rewardsystem' ),
							'2' => __( 'Non-Editable', 'rewardsystem' ),
						),
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Enter Referral Link for Refer a Friend Form', 'rewardsystem' ),
						'id'      => 'rs_referral_link_refer_a_friend_form',
						'std'     => site_url(),
						'default' => site_url(),
						'type'    => 'text',
						'newids'  => 'rs_referral_link_refer_a_friend_form',
					),
					array(
						'name'     => __( 'Prefilled Message for Refer a Friend', 'rewardsystem' ),
						'desc'     => __( 'This Message will be displayed in the Message field along with the Referral link', 'rewardsystem' ),
						'id'       => 'rs_friend_referral_link',
						'std'      => 'Hi [rs_your_friend_name]<br>You can Customize your message here.[site_referral_url] [rs_user_name]<br>Referrer First Name : [rs_referrer_first_name]<br>Referrer Last Name : [rs_referrer_last_name]<br>Referrer Email ID : [rs_referrer_email_id]',
						'default'  => 'Hi [rs_your_friend_name]<br>You can Customize your message here.[site_referral_url] [rs_user_name]<br>Referrer First Name : [rs_referrer_first_name]<br>Referrer Last Name : [rs_referrer_last_name]<br>Referrer Email ID : [rs_referrer_email_id]',
						'type'     => 'textarea',
						'newids'   => 'rs_friend_referral_link',
						'desc_tip' => true,
					),
					array(
						'name'    => __( 'Show/Hide I agree to the Terms and Condition Field', 'rewardsystem' ),
						'id'      => 'rs_show_hide_iagree_termsandcondition_field',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_iagree_termsandcondition_field',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Hide', 'rewardsystem' ),
							'2' => __( 'Show', 'rewardsystem' ),
						),
					),
					array(
						'name'     => __( 'I Agree Field Label', 'rewardsystem' ),
						'desc'     => __( 'This Caption will be displayed for the I agree field in Refer a Friend Form', 'rewardsystem' ),
						'id'       => 'rs_refer_friend_iagreecaption_link',
						'std'      => 'I agree to the {termsandconditions}',
						'default'  => 'I agree to the {termsandconditions}',
						'type'     => 'textarea',
						'newids'   => 'rs_refer_friend_iagreecaption_link',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Terms and Conditions Label', 'rewardsystem' ),
						'desc'     => __( 'This Caption will be displayed for terms and condition', 'rewardsystem' ),
						'id'       => 'rs_refer_friend_termscondition_caption',
						'std'      => 'Terms and Conditions',
						'default'  => 'Terms and Conditions',
						'type'     => 'textarea',
						'newids'   => 'rs_refer_friend_termscondition_caption',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Terms and Conditions URL', 'rewardsystem' ),
						'desc'     => __( 'Enter the URL for Terms and Conditions', 'rewardsystem' ),
						'id'       => 'rs_refer_friend_termscondition_url',
						'std'      => '',
						'default'  => '',
						'type'     => 'text',
						'newids'   => 'rs_refer_friend_termscondition_url',
						'desc_tip' => true,
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_reward_referfriend_status',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Error Message Settings for Refer a Friend Form', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_reward_referfriend_error_settings',
					),
					array(
						'name'     => __( 'Error Message to display when Friend Name Field is left empty', 'rewardsystem' ),
						'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Friend Name is Empty', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_name_error_message',
						'std'      => 'Please Enter your Friend Name',
						'default'  => 'Please Enter your Friend Name',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_name_error_message',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Error Message to display when Friend Email Field is left empty', 'rewardsystem' ),
						'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Friend Email is Empty', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_email_error_message',
						'std'      => 'Please Enter your Friend Email',
						'default'  => 'Please Enter your Friend Email',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_email_error_message',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Error Message to display when Email format is not valid', 'rewardsystem' ),
						'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Friend Email is not Valid', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_friend_email_is_not_valid',
						'std'      => 'Enter Email is not Valid',
						'default'  => 'Enter Email is not Valid',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_friend_email_is_not_valid',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Error Message to display when Friend Email Field is already used', 'rewardsystem' ),
						'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Friend Email is already used', 'rewardsystem' ),
						'id'       => 'rs_referred_email_already_occured_error_message',
						'std'      => 'This email id is already exist. Hence, you cannot use it here.',
						'default'  => 'This email id is already exist. Hence, you cannot use it here.',
						'type'     => 'text',
						'newids'   => 'rs_referred_email_already_occured_error_message',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Error Message to display when Email Subject is left empty', 'rewardsystem' ),
						'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Email Subject is Empty', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_email_subject_error_message',
						'std'      => 'Email Subject should not be left blank',
						'default'  => 'Email Subject should not be left blank',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_email_subject_error_message',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Error Message to display when Email Message is left empty', 'rewardsystem' ),
						'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Email Message is Empty', 'rewardsystem' ),
						'id'       => 'rs_my_rewards_email_message_error_message',
						'std'      => 'Please Enter your Message',
						'default'  => 'Please Enter your Message',
						'type'     => 'text',
						'newids'   => 'rs_my_rewards_email_message_error_message',
						'desc_tip' => true,
					),
					array(
						'name'     => __( 'Error Message to display when I agree checkbox is unchecked', 'rewardsystem' ),
						'desc'     => __( 'Enter your Error Message which will be appear in frontend if i agree is unchecked', 'rewardsystem' ),
						'id'       => 'rs_iagree_error_message',
						'std'      => 'Please Accept our Terms and Condition',
						'default'  => 'Please Accept our Terms and Condition',
						'type'     => 'text',
						'newids'   => 'rs_iagree_error_message',
						'desc_tip' => true,
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_reward_referfriend_error_settings',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Manual Referral Link Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_manual_setting',
					),
					array(
						'type' => 'rs_user_role_dynamics_manual',
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_manual_setting',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Referral Reward Table', 'rewardsystem' ),
						'type' => 'title',
						'id'   => 'rs_referral_setting',
					),
					array(
						'type' => 'display_referral_reward_log',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'rs_referral_setting',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
					array(
						'type' => 'rs_subscription_compatible_start',
					),
					array(
						'name' => __( 'Subscriptions Compatibility Settings', 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_subscription_settings',
					),
					array(
						'type' => 'sectionend',
						'id'   => '_rs_subscription_settings',
					),
					array(
						'type' => 'rs_subscription_compatible_end',
					),
					array(
						'type' => 'rs_wrapper_start',
					),
					array(
						'name' => __( 'Shortcodes used in Refer a Friend', 'rewardsystem' ),
						'type' => 'title',
						'id'   => 'rs_shortcodes_in_refer_a_friend',
					),
					array(
						'type' => 'title',
						'desc' => __(
							'<b>[site_referral_url]</b> - To display referrer url<br><br>'
							   . '<b>[rs_user_name]</b> - To display current user name<br><br>'
							   . '<b>[rs_referrer_name]</b> - To display referrer name<br><br>'
							   . '<b>[rs_referrer_first_name]</b> - To display referrer first name<br><br>'
							   . '<b>[rs_referrer_last_name]</b> - To display referrer last name<br><br>'
							   . '<b>[rs_referrer_email_id]</b> - To display referrer email<br><br>'
							. '<b>{termsandconditions}</b> - To display the link for terms and conditions',
							'rewardsystem'
						),
					),
					array(
						'type' => 'title',
						'desc' => __( '<b>Note:</b> <br/>We recommend don’t use the above shortcodes anywhere on your site. It will give the value only on the place where we have predefined.<br/> Please check by using the shortcodes available in the <b>Shortcodes </b> tab which will give the value globally.', 'rewardsystem' ),
						'id'   => 'rs_shortcode_note_referral',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'rs_shortcodes_in_refer_a_friend',
					),
					array(
						'type' => 'rs_wrapper_end',
					),
				)
			);
		}

		/**
		 * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
		 */
		public static function reward_system_register_admin_settings() {
			woocommerce_admin_fields( self::reward_system_admin_fields() );
		}

		/**
		 * Update the Settings on Save Changes may happen in SUMO Reward Points
		 */
		public static function reward_system_update_settings() {
			woocommerce_update_options( self::reward_system_admin_fields() );
			if ( isset( $_REQUEST['rs_select_exclude_users_list_for_show_referral_link'] ) ) {
				update_option( 'rs_select_exclude_users_list_for_show_referral_link', wc_clean( wp_unslash( $_REQUEST['rs_select_exclude_users_list_for_show_referral_link'] ) ) );
			} else {
				update_option( 'rs_select_exclude_users_list_for_show_referral_link', '' );
			}
			if ( isset( $_REQUEST['rs_select_include_users_for_show_referral_link'] ) ) {
				update_option( 'rs_select_include_users_for_show_referral_link', wc_clean( wp_unslash( $_REQUEST['rs_select_include_users_for_show_referral_link'] ) ) );
			} else {
				update_option( 'rs_select_include_users_for_show_referral_link', '' );
			}
			if ( isset( $_REQUEST['rs_fbshare_image_url_upload'] ) ) {
				update_option( 'rs_fbshare_image_url_upload', wc_clean( wp_unslash( $_REQUEST['rs_fbshare_image_url_upload'] ) ) );
			} else {
				update_option( 'rs_fbshare_image_url_upload', '' );
			}
			if ( isset( $_REQUEST['rs_referral_module_checkbox'] ) ) {
				update_option( 'rs_referral_activated', wc_clean( wp_unslash( $_REQUEST['rs_referral_module_checkbox'] ) ) );
			} else {
				update_option( 'rs_referral_activated', 'no' );
			}
			if ( isset( $_REQUEST['rs_include_products_for_referral_product_purchase'] ) ) {
				update_option( 'rs_include_products_for_referral_product_purchase', wc_clean( wp_unslash( $_REQUEST['rs_include_products_for_referral_product_purchase'] ) ) );
			} else {
				update_option( 'rs_include_products_for_referral_product_purchase', '' );
			}
			if ( isset( $_REQUEST['rs_exclude_products_for_referral_product_purchase'] ) ) {
				update_option( 'rs_exclude_products_for_referral_product_purchase', wc_clean( wp_unslash( $_REQUEST['rs_exclude_products_for_referral_product_purchase'] ) ) );
			} else {
				update_option( 'rs_exclude_products_for_referral_product_purchase', '' );
			}
			if ( isset( $_REQUEST['rs_no_of_users_referral_to_get_reward_signup_bonus'] ) ) {
				update_option( 'rs_no_of_users_referral_to_get_reward_signup_bonus', wc_clean( wp_unslash( $_REQUEST['rs_no_of_users_referral_to_get_reward_signup_bonus'] ) ) );
			} else {
				update_option( 'rs_no_of_users_referral_to_get_reward_signup_bonus', '' );
			}

			if ( isset( $_REQUEST['rewards_dynamic_rule_manual'] ) ) {

				$manual_link_rules = get_option( 'rewards_dynamic_rule_manual', array() );
				foreach ( wc_clean( wp_unslash( $_REQUEST['rewards_dynamic_rule_manual'] ) ) as $key => $values ) {

					$manual_link_rules[ $key ] = $values;

					if ( isset( $_REQUEST['rs_removed_link_rule'][ $key ] ) ) {
						if ( 'yes' == wc_clean( wp_unslash( $_REQUEST['rs_removed_link_rule'][ $key ] ) ) ) {
							$manual_link_rules[ $key ] = '';
						}
					}
				}

				$manual_link_rules = ( array_values( array_filter( (array) $manual_link_rules ) ) );

				update_option( 'rewards_dynamic_rule_manual', $manual_link_rules );
			}
		}

		/**
		 * Initialize the Default Settings by looping this function
		 */
		public static function set_default_value() {
			foreach ( self::reward_system_admin_fields() as $setting ) {
				if ( isset( $setting['newids'] ) && isset( $setting['std'] ) ) {
					add_option( $setting['newids'], $setting['std'] );
				}
			}
		}

		public static function rs_save_button_for_referral_update() {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">                    
				</th>
				<td class="forminp forminp-select">
					<input type="submit" class="rs_sumo_referral_update_button button-primary" value="<?php esc_html_e( 'Save and Update', 'rewardsystem' ); ?>"/>
				</td>
			</tr>
			<?php
		}

		public static function rs_hide_bulk_update_for_referral_product_purchase_start() {
			?>
			<div class="rs_hide_bulk_update_for_referral_product_purchase_start">
				<?php
		}

		public static function rs_hide_bulk_update_for_referral_product_purchase_end() {
			?>
			</div>
			<?php
		}

		public static function reset_referral_system_module() {
			$settings = self::reward_system_admin_fields();
			RSTabManagement::reset_settings( $settings );
			delete_option( 'rewards_dynamic_rule_manual' );
			update_option( 'rs_no_of_users_referral_to_get_reward_signup_bonus', '' );
		}

		public static function enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_referral_activated' ), 'rs_referral_module_checkbox', 'rs_referral_activated' );
		}

		public static function rs_select_products_to_update() {
			$field_id    = 'rs_select_particular_products';
			$field_label = esc_html__( 'Select Particular Products', 'rewardsystem' );
			$getproducts = get_option( 'rs_select_particular_products' );
			echo do_shortcode( rs_function_to_add_field_for_product_select( $field_id, $field_label, $getproducts ) );
		}

		public static function rs_exclude_user_as_hide_referal_link() {
			$field_id    = 'rs_select_exclude_users_list_for_show_referral_link';
			$field_label = __( 'Select the Users for Preventing access to Referral System', 'rewardsystem' );
			$getuser     = get_option( 'rs_select_exclude_users_list_for_show_referral_link' );
			echo do_shortcode( user_selection_field( $field_id, $field_label, $getuser ) );
		}

		public static function rs_include_user_as_hide_referal_link() {
			$field_id    = 'rs_select_include_users_for_show_referral_link';
			$field_label = __( 'Select the Users for Providing access to Referral System', 'rewardsystem' );
			$getuser     = get_option( 'rs_select_include_users_for_show_referral_link' );
			echo do_shortcode( user_selection_field( $field_id, $field_label, $getuser ) );
		}

		public static function reward_system_add_manual_table_to_action() {
			global $woocommerce;
			wp_nonce_field( plugin_basename( __FILE__ ), 'rsdynamicrulecreation_manual' );

			$per_page = RSTabManagement::rs_get_value_for_no_of_item_perpage( get_current_user_id(), get_current_screen() );
						/**
						 * Hook:rs_alter_manual_referral_link_rules.
						 *
						 * @since 1.0
						 */
			$manual_link_rules = apply_filters( 'rs_alter_manual_referral_link_rules', (array) get_option( 'rewards_dynamic_rule_manual', array() ) );
			// Search User filter.
			$searched_user = isset( $_REQUEST['rs_search_user'] ) ? sanitize_title( $_REQUEST['rs_search_user'] ) : '';

			if ( ! $searched_user ) {
				$chunk_rules             = array_chunk( $manual_link_rules, $per_page );
				$current_page            = isset( $_REQUEST['page_no'] ) ? wc_clean( wp_unslash( absint( $_REQUEST['page_no'] ) ) ) : '1';
				$rules_based_on_per_page = isset( $chunk_rules[ $current_page - 1 ] ) ? $chunk_rules[ $current_page - 1 ] : array();
			} else {
				$rules_based_on_per_page = $manual_link_rules;
				$current_page            = 1;
			}

			$page_count = ceil( count( $manual_link_rules ) / $per_page );
			$query_args = array(
				'page'    => 'rewardsystem_callback',
				'tab'     => 'fprsmodules',
				'section' => 'fpreferralsystem',
			);

			// Display Manual Referral link Rules.
			include SRP_PLUGIN_PATH . '/includes/admin/views/manual-referral-link-rules.php';
		}

		public static function rs_list_referral_rewards_log() {
			if ( ! ( isset( $_GET['view'] ) ) ) {
				$newwp_list_table_for_users = new SRP_Referral_List_Table();
				$newwp_list_table_for_users->prepare_items();
				$newwp_list_table_for_users->search_box( __( 'Search Users', 'rewardsystem' ), 'search_id' );
				$newwp_list_table_for_users->display();
			} else {
				$newwp_list_table_for_users = new SRP_View_Referral_Table();
				$newwp_list_table_for_users->prepare_items();
				$newwp_list_table_for_users->search_box( __( 'Search', 'rewardsystem' ), 'search_id' );
				$newwp_list_table_for_users->display();
				?>
				<a href="<?php echo esc_url( remove_query_arg( array( 'view' ), get_permalink() ) ); ?>"><?php esc_html_e( 'Go Back', 'rewardsystem' ); ?></a>
				<?php
			}
		}

		public static function rs_add_upload_your_facebook_share_image() {
			?>
					   
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="rs_fbshare_image_url_upload"><?php esc_html_e( 'Image used for Facebook Share', 'rewardsystem' ); ?></label>
				</th>
				<td class="forminp forminp-select">
					<input type="text" id="rs_fbshare_image_url_upload" name="rs_fbshare_image_url_upload" value="<?php echo esc_url( get_option( 'rs_fbshare_image_url_upload' ) ); ?>"/>
					<input type="submit" id="rs_fbimage_upload_button" class="rs_imgupload_button" name="rs_fbimage_upload_button" value="<?php esc_html_e( 'Upload Image', 'rewardsystem' ); ?>"/>
				</td>
			</tr>            
			<?php
		}

		public static function rs_include_products_for_referral_product_purchase() {
			$field_id    = 'rs_include_products_for_referral_product_purchase';
			$field_label = 'Include Product(s)';
			$getproducts = get_option( 'rs_include_products_for_referral_product_purchase' );
			echo do_shortcode( rs_function_to_add_field_for_product_select( $field_id, $field_label, $getproducts ) );
		}

		public static function rs_exclude_products_for_referral_product_purchase() {
			$field_id    = 'rs_exclude_products_for_referral_product_purchase';
			$field_label = 'Exclude Product(s)';
			$getproducts = get_option( 'rs_exclude_products_for_referral_product_purchase' );
			echo do_shortcode( rs_function_to_add_field_for_product_select( $field_id, $field_label, $getproducts ) );
		}

		/*
		 * Manual refferal user search filter.
		 */

		public static function manual_referral_link_user_search_filter( $manual_link_rules ) {

			if ( ! srp_check_is_array( $manual_link_rules ) ) {
				return array();
			}

			if ( ! isset( $_REQUEST['rs_search_user_action'] ) ) {
				return $manual_link_rules;
			}

			$searched_user = isset( $_REQUEST['rs_search_user'] ) ? sanitize_text_field( $_REQUEST['rs_search_user'] ) : '';
			if ( ! $searched_user ) {
				return $manual_link_rules;
			}

			$user    = is_object( get_user_by( 'ID', $searched_user ) ) ? get_user_by( 'ID', $searched_user ) : get_user_by( 'login', $searched_user );
			$user    = is_object( $user ) ? $user : get_user_by( 'email', $searched_user );
			$user_id = is_object( $user ) ? $user->ID : false;
			if ( ! $user_id ) {
				return array();
			}

			foreach ( $manual_link_rules as $key => $values ) {
				if ( isset( $values['referer'], $values['refferal'] ) && $user_id == $values['referer'] || $user_id == $values['refferal'] ) {
					$manual_link_rules[ $key ] = $values;
				} else {
					$manual_link_rules[ $key ] = '';
				}
			}

			return array_filter( $manual_link_rules );
		}

		public static function render_subscription_settings( $settings ) {

				$updated_settings = array();
			foreach ( $settings as $section ) {
				if ( isset( $section['id'] ) && '_rs_subscription_settings' == $section['id'] &&
						isset( $section['type'] ) && 'sectionend' == $section['type'] ) {
					if ( class_exists( 'WC_Subscriptions' ) ) {
						$updated_settings[] = array(
							'type' => 'title',
							'id'   => 'rs_wc_subscription',
							'desc' => __( '<h3>WooCommerce Subscriptions</h3><br><br>', 'rewardsystem' ),
						);
						$updated_settings[] = array(
							'name'   => __( 'Restrict Referral Product Purchase Points for Renewal Orders', 'rewardsystem' ),
							'id'     => 'rs_award_referral_point_wc_renewal_order',
							'std'    => 'no',
							'type'   => 'checkbox',
							'newids' => 'rs_award_referral_point_wc_renewal_order',
						);
						$updated_settings[] = array(
							'type' => 'sectionend',
							'id'   => 'rs_wc_subscription',
						);
					}

					if ( class_exists( 'SUMOSubscriptions' ) ) {
						$updated_settings[] = array(
							'type' => 'title',
							'id'   => 'rs_sumo_subscription_referral',
							'desc' => __( '<h3>SUMO Subscriptions</h3><br><br>', 'rewardsystem' ),
						);
						$updated_settings[] = array(
							'name'   => __( 'Restrict Referral Product Purchase Points for Renewal Orders', 'rewardsystem' ),
							'id'     => 'rs_award_referral_point_for_renewal_order',
							'std'    => 'no',
							'type'   => 'checkbox',
							'newids' => 'rs_award_referral_point_for_renewal_order',
						);
						$updated_settings[] = array(
							'type' => 'sectionend',
							'id'   => 'rs_sumo_subscription_referral',
						);
					}
				}
				$updated_settings[] = $section;
			}

			return $updated_settings;
		}

		public static function srp_get_reward_signup_bonus() {
			?>
			<tr valign="top">
				<th scope="row">
					<label><?php esc_html_e( 'Refer', 'rewardsystem' ); ?></label>
				</th>
				<td class="forminp forminp-select">
					<fieldset>
					<input type="text" 
							id="rs_no_of_users_referral_to_get_reward_signup_bonus" 
							name="rs_no_of_users_referral_to_get_reward_signup_bonus"
							newids="rs_no_of_users_referral_to_get_reward_signup_bonus"
							std=" "
							value="<?php echo esc_attr( get_option( 'rs_no_of_users_referral_to_get_reward_signup_bonus', '' ) ); ?>" /><span><?php esc_html_e( 'Users', 'rewardsystem' ); ?></span>
					</fieldset>
				</td>
			</tr>            
			<?php
		}
	}

	RSReferralSystemModule::init();
}
