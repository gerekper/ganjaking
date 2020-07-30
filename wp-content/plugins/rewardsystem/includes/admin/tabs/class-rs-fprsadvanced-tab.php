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

            add_action( 'woocommerce_admin_field_previous_order_button' , array( __CLASS__ , 'rs_apply_points_for_previous_order_button' ) ) ;

            add_filter( "woocommerce_fprsadvanced_settings" , array( __CLASS__ , 'reward_system_add_settings_based_on_user_role' ) ) ;

            add_action( 'woocommerce_admin_field_rs_administrator_wrapper_start' , array( __CLASS__ , 'rs_wrapper_administrator_start' ) ) ;

            add_action( 'woocommerce_admin_field_rs_administrator_wrapper_end' , array( __CLASS__ , 'rs_wrapper_administrator_close' ) ) ;

            add_action( 'rs_display_save_button_fprsadvanced' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fprsadvanced' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        public static function reward_system_admin_fields() {
            return apply_filters( 'woocommerce_fprsadvanced_settings' , array(
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Advanced Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_advanced_setting' ,
                ) ,
                array(
                    'name'    => __( 'Reward Points Program' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_reward_program' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_reward_program' ,
                    'desc'    => __( 'By enabling this checkbox, new users can have the option to involve in the Reward Points Program on the site during registration. For old users, they will have the checkbox to exit from the reward points program on the account page. By default, it will be enabled for old users' , SRP_LOCALE ) ,
                ) ,
                array(
                    'name'    => __( 'Notification on My Account Page - Registration' , SRP_LOCALE ) ,
                    'id'      => 'rs_msg_in_reg_page' ,
                    'std'     => __( 'By selecting this checkbox, you will be involved in Reward Points Program where you can earn points for the actions such as Account Sign up, Product Purchase, Product Review, etc on the site and points earned can be used on future purchases' , SRP_LOCALE ) ,
                    'default' => __( 'By selecting this checkbox, you will be involved in Reward Points Program where you can earn points for the actions such as Account Sign up, Product Purchase, Product Review, etc on the site and points earned can be used on future purchases' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_msg_in_reg_page' ,
                ) ,
                array(
                    'name'    => __( 'Notification on My Account Page - Checked' , SRP_LOCALE ) ,
                    'id'      => 'rs_msg_in_acc_page_when_checked' ,
                    'std'     => __( 'By unchecking this checkbox, you will not be in part of Reward Points Program' , SRP_LOCALE ) ,
                    'default' => __( 'By unchecking this checkbox, you will not be in part of Reward Points Program' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_msg_in_acc_page_when_checked' ,
                ) ,
                array(
                    'name'    => __( 'Notification on My Account Page - Unchecked' , SRP_LOCALE ) ,
                    'id'      => 'rs_msg_in_acc_page_when_unchecked' ,
                    'std'     => __( 'By selecting this checkbox, you will be in part of Reward Points Program' , SRP_LOCALE ) ,
                    'default' => __( 'By selecting this checkbox, you will be in part of Reward Points Program' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_msg_in_acc_page_when_unchecked' ,
                ) ,
                array(
                    'name'    => __( 'Alert Message on My Account Page - Checked' , SRP_LOCALE ) ,
                    'id'      => 'rs_alert_msg_in_acc_page_when_checked' ,
                    'std'     => __( 'Are you sure you want to be part of the Reward Points Program?' , SRP_LOCALE ) ,
                    'default' => __( 'Are you sure you want to be part of the Reward Points Program' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_alert_msg_in_acc_page_when_checked' ,
                ) ,
                array(
                    'name'    => __( 'Alert Message on My Account Page - Unchecked' , SRP_LOCALE ) ,
                    'id'      => 'rs_alert_msg_in_acc_page_when_unchecked' ,
                    'std'     => __( 'Are you sure you want to exit the Reward Points Program?' , SRP_LOCALE ) ,
                    'default' => __( 'Are you sure you want to exit the Reward Points Program?' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_alert_msg_in_acc_page_when_unchecked' ,
                ) ,
                array(
                    'name'    => __( 'Tab section' , SRP_LOCALE ) ,
                    'id'      => 'rs_expand_collapse' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_expand_collapse' ,
                    'options' => array(
                        '1' => __( 'Collapse all' , SRP_LOCALE ) ,
                        '2' => __( 'Expand all' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide Reset Button in Tabs' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_reset_all' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_reset_all' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Admin Color Scheme' , SRP_LOCALE ) ,
                    'id'      => 'rs_color_scheme' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_color_scheme' ,
                    'options' => array(
                        '1' => __( 'Dark' , SRP_LOCALE ) ,
                        '2' => __( 'Light' , SRP_LOCALE ) ,
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
                    'name' => __( 'Product Purchase Reward Points for Previous Orders' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_apply_reward_points' ,
                ) ,
                array(
                    'name'     => __( 'Award Product Purchase Reward Points for' , SRP_LOCALE ) ,
                    'id'       => 'rs_sumo_select_order_range' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Any Old Orders' , SRP_LOCALE ) ,
                        '2' => __( 'Orders Placed Between Specific Date Range' , SRP_LOCALE )
                    ) ,
                    'newids'   => 'rs_sumo_select_order_range' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'type' => 'previous_order_button_range' ,
                ) ,
                array(
                    'name'     => __( 'Previous Order(s) Points for' , SRP_LOCALE ) ,
                    'id'       => 'rs_award_previous_order_points' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Order(s) in which points are not awarded for already purchased products' , SRP_LOCALE ) ,
                        '2' => __( 'Based on Conversion Settings' , SRP_LOCALE )
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
                    'name' => __( 'My Account Page Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_my_acccount_page_settings' ,
                ) ,
                array(
                    'name'    => __( 'Reward Points Fields in My Account Page' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to show the Reward Points Fields in My Account Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_content' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'yes' ,
                    'default' => 'yes' ,
                    'newids'  => 'rs_reward_content' ,
                ) ,
                array(
                    'name'    => __( 'Reward Points Fields in Shortcode' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to show the Reward Points Fields in Shortcode' , SRP_LOCALE ) ,
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
                    'name' => __( 'My Account Menu Page Show/Hide Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_my_acccount_menu_page_show_hide_settings' ,
                ) ,
                array(
                    'name'    => __( 'Reward Points Fields in My Account Menu' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to show the Reward Points Fields in My Account Menu' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_content_menu_page' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_reward_content_menu_page' ,
                ) ,
                array(
                    'name'    => __( 'Reward Points Fields display Name on My Account Page' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enter the Title for My Reward Content on My Account Menu Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_my_reward_content_title' ,
                    'type'    => 'text' ,
                    'std'     => 'My Reward' ,
                    'default' => 'My Reward' ,
                    'newids'  => 'rs_my_reward_content_title' ,
                ) ,
                array(
                    'name'    => __( 'Reward Points Fields URL Name on My Account Page' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enter the URL for Reward Points Fields in My Account Menu Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_my_reward_url_title' ,
                    'type'    => 'text' ,
                    'std'     => 'sumo-reward-points' ,
                    'default' => 'sumo-reward-points' ,
                    'newids'  => 'rs_my_reward_url_title' ,
                ) ,
                array(
                    'name'    => __( 'My Rewards Table' , SRP_LOCALE ) ,
                    'id'      => 'rs_my_reward_table_menu_page' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_my_reward_table_menu_page' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Generate Referral Link' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_generate_referral_menu_page' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_generate_referral_menu_page' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Referral Table ' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_referal_table_menu_page' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_referal_table_menu_page' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Refer a Friend Form ' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_refer_a_friend_menu_page' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_refer_a_friend_menu_page' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'My Cashback Table' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_table_menu_page' ,
                    'std'      => '1' ,
                    'desc_tip' => true ,
                    'default'  => '1' ,
                    'newids'   => 'rs_my_cashback_table_menu_page' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Nominee Field' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_nominee_field_menu_page' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_nominee_field_menu_page' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Gift Voucher Field' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_redeem_voucher_menu_page' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_redeem_voucher_menu_page' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Email - Subscribe Link' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_your_subscribe_link_menu_page' ,
                    'newids'  => 'rs_show_hide_your_subscribe_link_menu_page' ,
                    'class'   => 'rs_show_hide_your_subscribe_link_menu_page' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_my_acccount_menu_page_show_hide_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_administrator_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points Menu Restriction based on User Role' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_user_role_menu_restriction_reward_points' ,
                ) ,
                array(
                    'name'    => __( 'User Role based Menu Restriction' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to restrict the menu based on user role' , SRP_LOCALE ) ,
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
                    'name' => __( 'Coupon Deletion based on Cron Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_delete_coupon_cron_time' ,
                ) ,
                array(
                    'name'    => __( 'Coupon Deletion' , SRP_LOCALE ) ,
                    'id'      => '_rs_enable_coupon_restriction' ,
                    'newids'  => '_rs_enable_coupon_restriction' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'desc'    => 'By Enabling this checkbox, the coupons created on WooCommerce coupon section when points have been applied will be deleted based on cron time.' ,
                ) ,
                array(
                    'name'    => __( 'Delete Coupons ' , SRP_LOCALE ) ,
                    'id'      => '_rs_restrict_coupon' ,
                    'newids'  => '_rs_restrict_coupon' ,
                    'type'    => 'select' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'options' => array(
                        '1' => __( 'After the order has been placed' , SRP_LOCALE ) ,
                        '2' => __( 'Based on cron time' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Coupon Deletion Cron Time Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_delete_coupon_by_cron_time' ,
                    'newids'  => 'rs_delete_coupon_by_cron_time' ,
                    'type'    => 'select' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'options' => array(
                        '1' => __( 'Days' , SRP_LOCALE ) ,
                        '2' => __( 'Hours' , SRP_LOCALE ) ,
                        '3' => __( 'Minutes' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'              => __( 'Coupon Deletion Cron Time' , SRP_LOCALE ) ,
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
                    'name' => __( 'Rank based Reward Points Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_short_code_settings_rank' ,
                ) ,
                array(
                    'name'    => __( 'Rank Table for Total Earned Points to be displayed ' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_pagination_for_total_earned_points' ,
                    'newids'  => 'rs_select_pagination_for_total_earned_points' ,
                    'type'    => 'select' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'options' => array(
                        '1' => __( 'With Pagination' , SRP_LOCALE ) ,
                        '2' => __( 'Without Pagination' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'              => __( 'Enter the number of entries to be displayed' , SRP_LOCALE ) ,
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
                    'name'    => __( 'Rank Table for Available Points to be displayed ' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_pagination_for_available_points' ,
                    'newids'  => 'rs_select_pagination_for_available_points' ,
                    'type'    => 'select' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'options' => array(
                        '1' => __( 'With Pagination' , SRP_LOCALE ) ,
                        '2' => __( 'Without Pagination' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'              => __( 'Enter the number of entries to be displayed' , SRP_LOCALE ) ,
                    'id'                => 'rs_value_without_pagination_for_available_points' ,
                    'newids'            => 'rs_value_without_pagination_for_available_points' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => '0'
                    ) ,
                    'std'               => '' ,
                    'default'           => '' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_short_code_settings_rank' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Custom CSS Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'desc' => 'Try !important if styles doesn\'t apply ' ,
                    'id'   => '_rs_general_custom_css_settings' ,
                ) ,
                array(
                    'name'    => __( 'Custom CSS' , SRP_LOCALE ) ,
                    'id'      => 'rs_general_custom_css' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_general_custom_css' ,
                ) ,
                array(
                    'name'    => __( 'Custom CSS for Shop Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_shop_page_custom_css' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_shop_page_custom_css' ,
                ) ,
                array(
                    'name'    => __( 'Custom CSS for Category Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_category_page_custom_css' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_category_page_custom_css' ,
                ) ,
                array(
                    'name'    => __( 'Custom CSS for Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_single_product_page_custom_css' ,
                    'std'     => '.rs_message_for_single_product{ }' ,
                    'default' => '.rs_message_for_single_product{ }' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_single_product_page_custom_css' ,
                ) ,
                array(
                    'name'    => __( 'Custom CSS for Cart Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_cart_page_custom_css' ,
                    'std'     => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ } .rs_cart_message{ }' ,
                    'default' => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ } .rs_cart_message{ }' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_cart_page_custom_css' ,
                ) ,
                array(
                    'name'    => __( 'Custom CSS for Checkout Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_checkout_page_custom_css' ,
                    'std'     => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ } .rs_checkout_message{ }' ,
                    'default' => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ } .rs_checkout_message{ }' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_checkout_page_custom_css' ,
                ) ,
                array(
                    'name'   => __( 'Custom CSS for My Account Page' , SRP_LOCALE ) ,
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
                    'name'    => __( 'Custom CSS for Social Button' , SRP_LOCALE ) ,
                    'id'      => 'rs_social_custom_css' ,
                    'std'     => '.rs_social_sharing_buttons{};'
                    . '.rs_social_sharing_success_message' ,
                    'default' => '.rs_social_sharing_buttons{};'
                    . '.rs_social_sharing_success_message' ,
                    'newids'  => 'rs_social_custom_css' ,
                    'type'    => 'textarea' ,
                ) ,
                array(
                    'name'    => __( 'Custom CSS for Refer a Friend Form' , SRP_LOCALE ) ,
                    'id'      => 'rs_refer_a_friend_custom_css' ,
                    'std'     => '#rs_refer_a_friend_form { } #rs_friend_name { } #rs_friend_email { } #rs_friend_subject { } #rs_your_message { } #rs_refer_submit { }' ,
                    'default' => '#rs_refer_a_friend_form { } #rs_friend_name { } #rs_friend_email { } #rs_friend_subject { } #rs_your_message { } #rs_refer_submit { }' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_refer_a_friend_custom_css' ,
                ) ,
                array(
                    'name'    => __( 'Inbuilt Design for Cashback Form' , SRP_LOCALE ) ,
                    'id'      => 'rs_encash_form_inbuilt_design' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'radio' ,
                    'options' => array(
                        '1' => __( 'Inbuilt Design' , SRP_LOCALE )
                    ) ,
                    'newids'  => 'rs_encash_form_inbuilt_design' ,
                ) ,
                array(
                    'name'              => __( 'Inbuilt CSS (Non Editable) for Cashback Form' , SRP_LOCALE ) ,
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
                    'name'    => __( 'Custom Design for Cashback Form' , SRP_LOCALE ) ,
                    'id'      => 'rs_encash_form_inbuilt_design' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'radio' ,
                    'options' => array(
                        '2' => __( 'Custom Design' , SRP_LOCALE )
                    ) ,
                    'newids'  => 'rs_encash_form_inbuilt_design' ,
                ) ,
                array(
                    'name'    => __( 'Custom CSS for Cashback Form' , SRP_LOCALE ) ,
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
                    'name' => __( 'Troubleshoot Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_point_troubleshoot_cart_page'
                ) ,
                array(
                    'name'     => __( 'Troubleshoot Before Cart Hook' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can select the different hooks in Cart Page' , SRP_LOCALE ) ,
                    'id'       => 'rs_reward_point_troubleshoot_before_cart' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'options'  => array( '1' => 'woocommerce_before_cart' , '2' => 'woocommerce_before_cart_table' ) ,
                    'newids'   => 'rs_reward_point_troubleshoot_before_cart' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Troubleshoot Referral Link Landing Page Hook' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can select the different hooks for referral link landing page' , SRP_LOCALE ) ,
                    'id'       => 'rs_troubleshoot_referral_link_landing_page' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'options'  => array( '1' => 'wp' , '2' => 'wp_head' ) ,
                    'newids'   => 'rs_troubleshoot_referral_link_landing_page' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Redeeming Field display Position in Cart Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_point_troubleshoot_after_cart' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'radio' ,
                    'options' => array(
                        '1' => __( 'After cart table' , SRP_LOCALE ) ,
                        '2' => __( 'Left side in cart table' , SRP_LOCALE ) ,
                        '3' => __( 'Right side in the cart table' , SRP_LOCALE )
                    ) ,
                    'newids'  => 'rs_reward_point_troubleshoot_after_cart' ,
                ) ,
                array(
                    'name'     => __( 'Enqueue Tipsy jQuery Library in SUMO Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can select to change the load tipsy option if some jQuery conflict occurs' , SRP_LOCALE ) ,
                    'id'       => 'rs_reward_point_enable_tipsy_social_rewards' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE )
                    ) ,
                    'newids'   => 'rs_reward_point_enable_tipsy_social_rewards' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Enqueue jQuery UI Library in SUMO Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can select whether to enqueue the jQuery UI library available within SUMO Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_reward_point_enable_jquery' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'options'  => array(
                        '1' => __( 'Enqueue' , SRP_LOCALE ) ,
                        '2' => __( 'Do not Enqueue' , SRP_LOCALE )
                    ) ,
                    'newids'   => 'rs_reward_point_enable_jquery' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Enqueue Ajax based Redeeming Library in SUMO Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can select whether to enqueue the Ajax based Redeeming library available within SUMO Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_reward_point_enable_ajax_based_redeeming' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'options'  => array(
                        '1' => __( 'Enqueue' , SRP_LOCALE ) ,
                        '2' => __( 'Do not Enqueue' , SRP_LOCALE )
                    ) ,
                    'newids'   => 'rs_reward_point_enable_ajax_based_redeeming' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Dequeue Select2 Library Version 3.x in SUMO Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Here you can dequeue the select2 V3.x library in the Front End within SUMO Reward Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_point_dequeue_select2' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_reward_point_dequeue_select2' ,
                ) ,
                array(
                    'name'    => __( 'Dequeue Google reCaptcha Library in SUMO Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Here you can dequeue the Google reCaptcha library in the Front End within SUMO Reward Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_point_dequeue_recaptcha' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_reward_point_dequeue_recaptcha' ,
                ) ,
                array(
                    'name'     => __( 'Enqueue Bootstrap CSS in SUMO Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_enable_reward_point_bootstrap' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'newids'   => 'rs_enable_reward_point_bootstrap' ,
                    'options'  => array(
                        '1' => __( 'Enqueue' , SRP_LOCALE ) ,
                        '2' => __( 'Do not Enqueue' , SRP_LOCALE )
                    ) ,
                    'desc'     => __( 'Here you can dequeue the bootstrap css within SUMO Reward Points' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Enqueue Footable JQuery in SUMO Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_enable_footable_js' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'newids'   => 'rs_enable_footable_js' ,
                    'options'  => array(
                        '1' => __( 'Enqueue' , SRP_LOCALE ) ,
                        '2' => __( 'Do not Enqueue' , SRP_LOCALE )
                    ) ,
                    'desc'     => __( 'Here you can dequeue the Footable JQuery within SUMO Reward Points' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Include Header' , SRP_LOCALE ) ,
                    'id'       => 'rs_enable_get_header' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'newids'   => 'rs_enable_get_header' ,
                    'options'  => array(
                        '1' => __( 'Include' , SRP_LOCALE ) ,
                        '2' => __( 'Exclude' , SRP_LOCALE )
                    ) ,
                    'desc'     => __( 'Here you can exclude the header which break css in page where referral link placed' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Earn Points Message display in Product Page for Variable product based on' , SRP_LOCALE ) ,
                    'id'      => 'rs_earn_message_display_hook' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'radio' ,
                    'newids'  => 'rs_earn_message_display_hook' ,
                    'options' => array(
                        '1' => __( 'woocommerce_get_price_html' , SRP_LOCALE ) ,
                        '2' => __( 'WooCommerce template hooks' , SRP_LOCALE )
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Set the Hook Priority to display Redeemed Points Label in Cart & Checkout' , SRP_LOCALE ) ,
                    'id'      => 'rs_change_coupon_priority_value' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'number' ,
                    'newids'  => 'rs_change_coupon_priority_value' ,
                ) ,
                array(
                    'name'     => __( 'Load SUMO Reward Points Script/Styles in' , SRP_LOCALE ) ,
                    'desc'     => __( 'For Footer of the Site Option is experimental why because if your theme doesn\'t contain wp_footer hook then it won\'t work' , SRP_LOCALE ) ,
                    'id'       => 'rs_load_script_styles' ,
                    'newids'   => 'rs_load_script_styles' ,
                    'type'     => 'select' ,
                    'desc_tip' => false ,
                    'options'  => array(
                        'wp_head'   => __( 'Header of the Site' , SRP_LOCALE ) ,
                        'wp_footer' => __( 'Footer of the Site (Experimental)' , SRP_LOCALE )
                    ) ,
                    'std'      => 'wp_head' ,
                    'default'  => 'wp_head' ,
                ) ,
                array(
                    'name'     => __( 'Memory Exhaust Issues' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enable or Disable Memory Exhaust Troubleshoot' , SRP_LOCALE ) ,
                    'id'       => 'rs_load_memory_unit' ,
                    'newids'   => 'rs_load_memory_unit' ,
                    'type'     => 'select' ,
                    'desc_tip' => false ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE )
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
                    'name' => __( 'Experimental Features' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_point_table'
                ) ,
                array(
                    'type' => 'rs_add_old_version_points' ,
                ) ,
                array(
                    'name'     => __( 'SUMO Reward Points Payment Gateway for Manual Order' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enable or Disable SUMO Reward Points Payment Gateway for Manual Order' , SRP_LOCALE ) ,
                    'id'       => 'rs_gateway_for_manual_order' ,
                    'newids'   => 'rs_gateway_for_manual_order' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'type'     => 'select' ,
                    'desc_tip' => true ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE )
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
                ?>
                <style type="text/css">
                    .rs_adminstrator_wrapper{
                        display:none;
                    }
                </style>
                <?php
            }
        }

        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSAdvancedSetting::reward_system_admin_fields() ) ;
        }

        public static function reward_system_update_settings() {
            woocommerce_update_options( RSAdvancedSetting::reward_system_admin_fields() ) ;
        }

        public static function set_default_value() {
            foreach ( RSAdvancedSetting::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function reset_advanced_tab() {
            $settings = RSAdvancedSetting::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

        public static function add_old_points_for_all_user() {
            ?>
            <tr valign="top">
                <th>
                    <label style="font-size:14px;font-weight:600;"><?php _e( 'Add the Old Available Points to User(s)' , SRP_LOCALE ) ; ?></label>
                </th>
                <td>
                    <input type="button" value="<?php _e( 'Add Old Points' , SRP_LOCALE ) ; ?>"  id="rs_add_old_points" class="rs_oldpoints_button" name="rs_add_old_points" /><b><span style="font-size: 18px;"><?php _e( '(Experimental)' , SRP_LOCALE ) ; ?></span></b>
                </td>
            </tr>
            <?php
        }

        public static function rs_add_date_picker() {
            ?>
            <script type="text/javascript">
                jQuery( function () {
                    jQuery( "#rs_from_date" ).datepicker( {
                        defaultDate : "+1w" ,
                        changeMonth : true ,
                        dateFormat : 'yy-mm-dd' ,
                        numberOfMonths : 1 ,
                        onClose : function ( selectedDate ) {
                            jQuery( "#to" ).datepicker( "option" , "minDate" , selectedDate ) ;
                        }
                    } ) ;
                    jQuery( '#rs_from_date' ).datepicker( 'setDate' , '-1' ) ;
                    jQuery( "#rs_to_date" ).datepicker( {
                        defaultDate : "+1w" ,
                        changeMonth : true ,
                        dateFormat : 'yy-mm-dd' ,
                        numberOfMonths : 1 ,
                        onClose : function ( selectedDate ) {
                            jQuery( "#from" ).datepicker( "option" , "maxDate" , selectedDate ) ;
                        }

                    } ) ;
                    jQuery( "#rs_to_date" ).datepicker( 'setDate' , new Date() ) ;
                } ) ;
            </script>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_sumo_rewards_for_selecting_particular_date"><?php _e( 'Select from Specific Date' , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    From <input type="text" id="rs_from_date" value=""/> To <input type="text" id="rs_to_date" value=""/>
                </td>
            </tr>
            <?php
        }

        public static function rs_apply_points_for_previous_order_button() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_sumo_rewards_for_previous_order_label"><?php _e( 'Apply Product Purchase Reward Points to Previous Orders' , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="submit" class="rs_sumo_rewards_for_previous_order button-primary rs_button" value="Apply Points for Previous Orders"/>
                    <div class="rs_sumo_rewards_previous_order" style="margin-bottom:10px;margin-top:10px; color:green;"></div>
                </td>
            </tr>
            <?php
        }

        public static function reward_system_add_settings_based_on_user_role( $settings ) {
            global $wp_roles ;
            $updated_settings = array() ;
            $mainvariable     = array() ;
            global $woocommerce ;
            $newcombinedarray = RSAdminAssets::list_of_tabs() ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && '_rs_user_role_menu_restriction_reward_points' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    foreach ( $wp_roles->role_names as $value => $key ) {
                        if ( $key != 'Administrator' && $key != 'Customer' ) {
                            $updated_settings[] = array(
                                'name'     => __( 'Reward Points Menu Restriction For ' . $key . ' User Role' , SRP_LOCALE ) ,
                                'desc'     => __( 'Restrict the Reward Points Menus For ' . $key . ' User role' , SRP_LOCALE ) ,
                                'class'    => 'rewardpoints_userrole_menu_restriction' ,
                                'id'       => 'rewardpoints_userrole_menu_restriction' . $value ,
                                'type'     => 'multiselect' ,
                                'options'  => $newcombinedarray ,
                                'default'  => '' ,
                                'std'      => '' ,
                                'newids'   => 'rewardpoints_userrole_menu_restriction' . $value ,
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

    }

    RSAdvancedSetting::init() ;
}