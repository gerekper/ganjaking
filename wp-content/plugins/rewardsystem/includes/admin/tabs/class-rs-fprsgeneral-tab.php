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
            add_action( 'woocommerce_admin_field_user_purchase_history' , array( __CLASS__ , 'add_rule_for_purchase_history' ) ) ;
            add_action( 'woocommerce_admin_field_rs_user_role_dynamics' , array( __CLASS__ , 'reward_system_add_table_to_action' ) ) ;
            add_action( 'woocommerce_admin_field_earning_conversion' , array( __CLASS__ , 'reward_system_earning_points_conversion' ) ) ;
            add_action( 'woocommerce_admin_field_redeeming_conversion' , array( __CLASS__ , 'reward_system_redeeming_points_conversion' ) ) ;
            add_action( 'woocommerce_admin_field_rs_refresh_button' , array( __CLASS__ , 'refresh_button_for_expired' ) ) ;
            add_action( 'fp_action_to_reset_settings_fprsgeneral' , array( __CLASS__ , 'reset_general_tab' ) ) ;
            add_filter( "woocommerce_fprsgeneral_settings" , array( __CLASS__ , 'reward_system_add_settings_to_action' ) ) ;

            if ( class_exists( 'SUMOMemberships' ) )
                add_filter( 'woocommerce_fprsgeneral_settings' , array( __CLASS__ , 'add_field_for_membership_plan' ) ) ;

            if ( class_exists( 'SUMOSubscriptions' ) )
                add_filter( 'woocommerce_fprsgeneral_settings' , array( __CLASS__ , 'add_custom_field_to_general_tab' ) ) ;

            if ( class_exists( 'SUMORewardcoupons' ) )
                add_filter( 'woocommerce_fprsgeneral_settings' , array( __CLASS__ , 'setting_for_sumo_coupons' ) ) ;

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
                        'name'   => __( 'Don\'t allow Earn Points when the user hasn\'t purchased any membership plan through SUMO Memberships' , SRP_LOCALE ) ,
                        'desc'   => __( 'Don\'t allow Earn Points when the user hasn\'t purchased any membership plan through SUMO Memberships' , SRP_LOCALE ) ,
                        'id'     => 'rs_enable_restrict_reward_points' ,
                        'type'   => 'checkbox' ,
                        'newids' => 'rs_enable_restrict_reward_points' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Membership Plan based Earning Level' , SRP_LOCALE ) ,
                        'desc'    => __( 'Enable this option to modify earning points based on membership plan' , SRP_LOCALE ) ,
                        'id'      => 'rs_enable_membership_plan_based_reward_points' ,
                        'std'     => 'yes' ,
                        'default' => 'yes' ,
                        'type'    => 'checkbox' ,
                        'newids'  => 'rs_enable_membership_plan_based_reward_points' ,
                            ) ;
                    foreach ( $membership_level as $key => $value ) {
                        $updated_settings[] = array(
                            'name'     => __( 'Reward Points Earning Percentage for ' . $value , SRP_LOCALE ) ,
                            'desc'     => __( 'Please Enter Percentage of Reward Points for ' . $value , SRP_LOCALE ) ,
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
                        'name'   => __( 'Don\'t Award Points for Renewal Orders of SUMO Subscriptions' , SRP_LOCALE ) ,
                        'desc'   => __( 'If You Enable this option, Reward Points for Renewal orders will not be awarded.' , SRP_LOCALE ) ,
                        'id'     => 'rs_award_point_for_renewal_order' ,
                        'std'    => 'no' ,
                        'type'   => 'checkbox' ,
                        'newids' => 'rs_award_point_for_renewal_order' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'   => __( 'Don\'t Award Referral Product Purchase Points for Renewal Orders of SUMO Subscriptions' , SRP_LOCALE ) ,
                        'desc'   => __( 'If You Enable this option, Referral Product Purchase Points for Renewal orders will not be awarded.' , SRP_LOCALE ) ,
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
                        'name'    => __( 'Don\'t allow Earn Points when SUMO Coupon is applied' , SRP_LOCALE ) ,
                        'desc'    => __( ' Don\'t allow Earn Points when SUMO Coupon is applied' , SRP_LOCALE ) ,
                        'id'      => '_rs_not_allow_earn_points_if_sumo_coupon' ,
                        'css'     => 'min-width:550px;' ,
                        'type'    => 'checkbox' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'newids'  => '_rs_not_allow_earn_points_if_sumo_coupon' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Don\'t allow Redeem when SUMO Coupon is applied' , SRP_LOCALE ) ,
                        'desc'    => __( 'Don\'t allow Redeem when SUMO Coupon is applied' , SRP_LOCALE ) ,
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
                    'name' => __( 'General Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_general_setting' ,
                ) ,
                array(
                    'type' => 'rs_refresh_button' ,
                ) ,
                array(
                    'name'     => __( 'Plugin Menu Display Name' , SRP_LOCALE ) ,
                    'desc'     => __( 'This name will be used to identify SUMO Reward Settings in Wordpress Dashboard' , SRP_LOCALE ) ,
                    'id'       => 'rs_brand_name' ,
                    'class'    => 'rs_brand_name' ,
                    'std'      => 'SUMO Reward Points' ,
                    'default'  => 'SUMO Reward Points' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_brand_name' ,
                    'type'     => 'text' ,
                ) ,
                array(
                    'name'    => __( 'Round Off Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_round_off_type_for_calculation' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_round_off_type_for_calculation' ,
                    'desc'    => __( 'By enabling this checkbox, points will be earned based on the option configured in Round Off Points Settings. For Redeeming Points, displaying of redeemed reward points will not be in control of Round Off Points Settings.' , SRP_LOCALE ) ,
                ) ,
                array(
                    'name'     => __( 'Round Off Type[Applicable only for Points]' , SRP_LOCALE ) ,
                    'id'       => 'rs_round_off_type' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( '2 Decimal Places' , SRP_LOCALE ) ,
                        '2' => __( 'Whole Number' , SRP_LOCALE ) ,
                    ) ,
                    'newids'   => 'rs_round_off_type' ,
                    'desc'     => __( 'Points will be displayed based on the option selected here and Decimal Separator for Points should obtain from [or] Roundup/Round Down settings.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Round Off Type[Applicable only for Currency] ' , SRP_LOCALE ) ,
                    'id'       => 'rs_roundoff_type_for_currency' ,
                    'css'      => 'min-width:150px;' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( '2 Decimal Places' , SRP_LOCALE ) ,
                        '2' => __( 'Whole Number' , SRP_LOCALE ) ,
                    ) ,
                    'newids'   => 'rs_roundoff_type_for_currency' ,
                    'desc'     => __( 'A currency[points equivalent value] will be displayed based on the option selected here and Decimal Separator for Currency should obtain from [or] Roundup/Round Down settings.<br><b>Note:</b>This settings is only for displaying purpose.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Decimal Separator for Currency should obtain from' , SRP_LOCALE ) ,
                    'id'      => 'rs_decimal_seperator_check_for_currency' ,
                    'css'     => 'min-width:150px;' ,
                    'std'     => '2' ,
                    'default' => '2' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Plugin Settings' , SRP_LOCALE ) ,
                        '2' => __( 'WooCommerce Settings' , SRP_LOCALE ) ,
                    ) ,
                    'newids'  => 'rs_decimal_seperator_check_for_currency' ,
                ) ,
                array(
                    'name'    => __( 'Roundup/Rounddown' , SRP_LOCALE ) ,
                    'id'      => 'rs_round_up_down' ,
                    'css'     => 'min-width:150px;' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Floor' , SRP_LOCALE ) ,
                        '2' => __( 'Ceil' , SRP_LOCALE ) ,
                    ) ,
                    'newids'  => 'rs_round_up_down' ,
                ) ,
                array(
                    'name'    => __( 'Number of decimals for Points should obtain from' , SRP_LOCALE ) ,
                    'id'      => 'rs_decimal_seperator_check' ,
                    'css'     => 'min-width:150px;' ,
                    'std'     => '2' ,
                    'default' => '2' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Plugin Settings' , SRP_LOCALE ) ,
                        '2' => __( 'WooCommerce Settings' , SRP_LOCALE ) ,
                    ) ,
                    'newids'  => 'rs_decimal_seperator_check' ,
                ) ,
                array(
                    'name'     => __( 'Date and Time Format Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_dispaly_time_format' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'newids'   => 'rs_dispaly_time_format' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Default' , SRP_LOCALE ) ,
                        '2' => __( 'WordPress Format' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                    'desc'     => __( 'If Default is selected as Date and Time Format Type, then the date and time should be displayed as d-m-Y h:i:s A. If WordPress Format is selected, then the date and time format in WordPress settings is consider as date and time format' , SRP_LOCALE ) ,
                ) ,
                array(
                    'name'    => __( 'Hide Time Format' , SRP_LOCALE ) ,
                    'id'      => 'rs_hide_time_format' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_hide_time_format' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this option, time should be hidden from the date in My Reward Table.' , SRP_LOCALE ) ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_general_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Earning Points Conversion Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_point_conversion' ,
                    'desc' => __( 'This Conversion settings controls how much points can be earned if Reward Type is set as "By Percentage of Product Price"' , SRP_LOCALE )
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
                    'name' => __( 'Redeeming Points Conversion Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_redeem_point_conversion' ,
                    'desc' => __( 'This conversion settings controls how much discount can be obtained by redeeming the available Reward Points' , SRP_LOCALE )
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
                    'name' => __( 'SUMO Subscriptions Compatability Settings' , SRP_LOCALE ) ,
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
                    'name' => __( 'SUMO Coupons Compatability Settings' , SRP_LOCALE ) ,
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
                    'name' => __( 'Reward Points Order Status Settings for Earning' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_product_purchase_status_settings' ,
                ) ,
                array(
                    'name'     => __( 'Reward Points will be awarded when Order Status reaches' , SRP_LOCALE ) ,
                    'desc'     => __( 'Points will award only when the order status matches with any one of the statuses selected in this field & the earned points for the corresponding order will revise from the account when the status change to any other that is not selected in this field.
<br><br>
<b>Example:</b><br>
Selected only "Processing" status in this field so that points will award once the order status reached to processing. The given points will be revised from the account when changed to any other status(ex. Completed/Canceled).' , SRP_LOCALE ) ,
                    'id'       => 'rs_order_status_control' ,
                    'std'      => array( 'completed' ) ,
                    'default'  => array( 'completed' ) ,
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
                    'name' => __( 'Reward Points Earning Threshold Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_restriction_setting' ,
                ) ,
                array(
                    'name'    => __( 'Maximum Threshold for Accumulating Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to provide restriction on Accumulating Reward Points without using it' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_disable_max_earning_points_for_user' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_disable_max_earning_points_for_user' ,
                ) ,
                array(
                    'name'     => __( 'Maximum Threshold value in Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter a Fixed or Decimal Number greater than 0' , SRP_LOCALE ) ,
                    'id'       => 'rs_max_earning_points_for_user' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_max_earning_points_for_user' ,
                    'type'     => 'text' ,
                ) ,
                array(
                    'name'    => __( 'Email Notification to the user(s) when reaching Maximum Threshold Value' , SRP_LOCALE ) ,
                    'id'      => 'rs_mail_for_reaching_maximum_threshold' ,
                    'class'   => 'rs_mail_for_reaching_maximum_threshold' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_mail_for_reaching_maximum_threshold' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject' , SRP_LOCALE ) ,
                    'id'      => 'rs_mail_subject_for_reaching_maximum_threshold' ,
                    'class'   => 'rs_mail_subject_for_reaching_maximum_threshold' ,
                    'std'     => 'Maximum Threshold Reached - Notification' ,
                    'default' => 'Maximum Threshold Reached - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_mail_subject_for_reaching_maximum_threshold' ,
                ) ,
                array(
                    'name'    => __( 'Email Message' , SRP_LOCALE ) ,
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
                    'name' => __( 'Reward Points Earning Member Level Priority Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_member_level_setting' ,
                    'desc' => __( 'This option controls which earning percentage should apply for the user if more than one  earning percentage is applicable for that user' , SRP_LOCALE )
                ) ,
                array(
                    'name'     => __( 'Priority Level Selection' , SRP_LOCALE ) ,
                    'desc'     => __( 'If more than one type(level) is enabled then use the highest/lowest percentage' , SRP_LOCALE ) ,
                    'id'       => 'rs_choose_priority_level_selection' ,
                    'class'    => 'rs_choose_priority_level_selection' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'newids'   => 'rs_choose_priority_level_selection' ,
                    'options'  => array(
                        '1' => __( 'Use the level that gives highest percentage' , SRP_LOCALE ) ,
                        '2' => __( 'Use the level that gives lowest percentage' , SRP_LOCALE ) ,
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
                    'name' => __( 'Reward Points Earning Percentage based on User Role' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_user_role_reward_points' ,
                ) ,
                array(
                    'name'    => __( 'User Role based Earning Level' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to modify reward points earning percentage based on user role' , SRP_LOCALE ) ,
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
                    'name' => __( 'Reward Points Earning Percentage based on Earned Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_member_level_earning_points' ,
                ) ,
                array(
                    'name'    => __( 'Earned Points based on Earning Level' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to modify earning points based on earned points' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_earned_level_based_reward_points' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_earned_level_based_reward_points' ,
                ) ,
                array(
                    'name'    => __( 'Earned Points is decided' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_earn_points_based_on' ,
                    'std'     => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_select_earn_points_based_on' ,
                    'options' => array(
                        '1' => __( 'Based on Total Earned Points' , SRP_LOCALE ) ,
                        '2' => __( 'Based on Current Points' , SRP_LOCALE ) ) ,
                ) ,
                array(
                    'name'    => __( 'New Member Level will be awarded' , SRP_LOCALE ) ,
                    'id'      => 'rs_free_product_range' ,
                    'std'     => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_free_product_range' ,
                    'options' => array(
                        '1' => __( 'Before reaching specified Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'After reaching specified Reward Points' , SRP_LOCALE ) ) ,
                ) ,
                array(
                    'name'    => __( 'Free Product should be' , SRP_LOCALE ) ,
                    'id'      => 'rs_free_product_add_by_user_or_admin' ,
                    'std'     => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_free_product_add_by_user_or_admin' ,
                    'options' => array(
                        '1' => __( 'Purchased by User' , SRP_LOCALE ) ,
                        '2' => __( 'Added to User Account Automatically' , SRP_LOCALE ) ) ,
                ) ,
                array(
                    'name'     => __( 'Free Product Quantity Selection' , SRP_LOCALE ) ,
                    'id'       => 'rs_free_product_add_quantity' ,
                    'std'      => '1' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_free_product_add_quantity' ,
                    'options'  => array(
                        '1' => __( 'Default' , SRP_LOCALE ) ,
                        '2' => __( 'Quantity Updation' , SRP_LOCALE )
                    ) ,
                    'desc'     => __( ' If default is selected, then quantity for a free product cannot be updated. If Quantity Updation is selected, the user can get the specified quantity for free products. If a user updated the quantity higher than a specified value, they have to buy using the amount for those additional quantities.' , SRP_LOCALE ) ,
                    'desc_tip' => true
                ) ,
                array(
                    'name'              => __( 'Enter the Quantity' , SRP_LOCALE ) ,
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
                    'name'     => __( 'Free Product will be added to the User Account when Order Status reaches' , SRP_LOCALE ) ,
                    'id'       => 'rs_order_status_control_to_automatic_order' ,
                    'std'      => 'processing' ,
                    'default'  => 'processing' ,
                    'type'     => 'select' ,
                    'options'  => $newcombinedarray ,
                    'newids'   => 'rs_order_status_control_to_automatic_order' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Email Subject' , SRP_LOCALE ) ,
                    'id'      => 'rs_subject_for_free_product_mail' ,
                    'std'     => 'Free Product Earned from [sitename]' ,
                    'default' => 'Free Product Earned from [sitename]' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_subject_for_free_product_mail' ,
                    'class'   => 'rs_subject_for_free_product_mail' ,
                ) ,
                array(
                    'name'    => __( 'Email Message' , SRP_LOCALE ) ,
                    'id'      => 'rs_content_for_free_product_mail' ,
                    'std'     => 'You have got this product for reaching [current_level_points] Reward Points [rsorderlink]' ,
                    'default' => 'You have got this product for reaching [current_level_points] Reward Points [rsorderlink]' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_content_for_free_product_mail' ,
                    'class'   => 'rs_content_for_free_product_mail' ,
                ) ,
                array(
                    'type' => 'rs_user_role_dynamics' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_member_level_earning_points' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points Earning Percentage based on Purchase History' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_member_level_earning_points_purchase_history' ,
                ) ,
                array(
                    'name'    => __( 'Purchase History based on Earning Level' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to modify earning points based on Purchase History' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_user_purchase_history_based_reward_points' ,
                    'css'     => 'min-width:150px;' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_user_purchase_history_based_reward_points' ,
                ) ,
                array(
                    'name'    => __( 'New Member Level will be awarded' , SRP_LOCALE ) ,
                    'id'      => 'rs_product_purchase_history_range' ,
                    'std'     => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_product_purchase_history_range' ,
                    'options' => array(
                        '1' => __( 'Before reaching specified Value' , SRP_LOCALE ) ,
                        '2' => __( 'After reaching specified Value' , SRP_LOCALE ) ) ,
                ) ,
                array(
                    'name'     => __( 'Reward Points Earning Percentage based on Order Status' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can set Reward Points Earning Percentage based on which Status of Order' , SRP_LOCALE ) ,
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
                    'name' => __( 'Email Settings For Actions' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_email_settings_for_action' ,
                ) ,
                array(
                    'name'    => __( 'Select Email Function' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_email_function_actions' ,
                    'css'     => 'min-width:150px;' ,
                    'std'     => '2' ,
                    'default' => '2' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_enable_email_function_actions' ,
                    'options' => array(
                        '1' => __( 'mail()' , SRP_LOCALE ) ,
                        '2' => __( 'wp_mail()' , SRP_LOCALE ) ) ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_email_settings_for_action' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_membership_compatible_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points Earning Percentage based on Membership Plan' , SRP_LOCALE ) ,
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
                    'name' => __( 'Member Level Message Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_member_level_message_settings' ,
                ) ,
                array(
                    'name'    => __( 'Message displayed for Free Products when product is added to cart(Default Type)' , SRP_LOCALE ) ,
                    'id'      => 'rs_free_product_message_info' ,
                    'std'     => 'You have got this product for reaching [current_level_points] Reward Points' ,
                    'default' => 'You have got this product for reaching [current_level_points] Reward Points' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_free_product_message_info' ,
                ) ,
                array(
                    'name'    => __( 'Message displayed for the Quantity of Free Products can be applicable to update in cart(Quantity Updation Type)' , SRP_LOCALE ) ,
                    'id'      => 'rs_free_product_quantity_message_info' ,
                    'std'     => 'You have got this product for reaching [current_level_points] Reward Points. Also, you have the access to update up to [free_product_quantity] quantity of this product for free. If you update more than [free_product_quantity] quantity, then those will be purchased by the amount.' ,
                    'default' => 'You have got this product for reaching [current_level_points] Reward Points. Also, you have the access to update up to [free_product_quantity] quantity of this product for free. If you update more than [free_product_quantity] quantity, then those will be purchased by the amount.' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_free_product_quantity_message_info' ,
                ) ,
                array(
                    'name'    => __( 'Free Product Label in Cart' , SRP_LOCALE ) ,
                    'id'      => 'rs_free_product_msg_caption' ,
                    'std'     => 'Free Product' ,
                    'default' => 'Free Product' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_free_product_msg_caption' ,
                ) ,
                array(
                    'name'    => __( 'Display Free Product Message in Cart and Order Details Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_remove_msg_from_cart_order' ,
                    'std'     => 'yes' ,
                    'default' => 'yes' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_remove_msg_from_cart_order' ,
                ) ,
                array(
                    'name'    => __( 'Message for Balance Points to reach next Member Level shortcode' , SRP_LOCALE ) ,
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
                    'name' => __( 'Reward Points Restriction Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_ban_users' ,
                ) ,
                array(
                    'name'    => __( 'Earning Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Restrict Users from Earning Points' , SRP_LOCALE ) ,
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
                    'name'        => __( 'Select the User Role(s)' , SRP_LOCALE ) ,
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
                    'name'    => __( 'Redeeming Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Restrict Users from Redeeming Points' , SRP_LOCALE ) ,
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
                    'name'        => __( 'Select the User Role(s)' , SRP_LOCALE ) ,
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
                    'name' => __( 'Shortcodes used in Product Purchase Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_shortcode_in_member_level' ,
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<b>[current_level_points]</b> - To display current level points<br><br>'
                    . '<b>[balancepoint]</b> - Displays the reward points needed to reach next earning level<br><br>'
                    . '<b>[paymentgatewaytitle]</b> - To display payment gateway title<br><br>'
                    . '<b>[next_level_name]</b> - To display next earning level name<br><br>'
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
            woocommerce_admin_fields( RSGeneralTabSetting::reward_system_admin_fields() ) ;
        }

        /*
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */

        public static function reward_system_update_settings() {
            woocommerce_update_options( RSGeneralTabSetting::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_banned_users_list_for_earning' ] ) ) {
                update_option( 'rs_banned_users_list_for_earning' , $_POST[ 'rs_banned_users_list_for_earning' ] ) ;
            } else {
                update_option( 'rs_banned_users_list_for_earning' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_banned_users_list_for_redeeming' ] ) ) {
                update_option( 'rs_banned_users_list_for_redeeming' , $_POST[ 'rs_banned_users_list_for_redeeming' ] ) ;
            } else {
                update_option( 'rs_banned_users_list_for_redeeming' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_earn_point' ] ) && $_POST[ 'rs_earn_point' ] != ('' || 0) ) {
                update_option( 'rs_earn_point' , $_POST[ 'rs_earn_point' ] ) ;
            } else {
                update_option( 'rs_earn_point' , '1' ) ;
            }
            if ( isset( $_POST[ 'rs_earn_point_value' ] ) && $_POST[ 'rs_earn_point_value' ] != ('' || 0) ) {
                update_option( 'rs_earn_point_value' , $_POST[ 'rs_earn_point_value' ] ) ;
            } else {
                update_option( 'rs_earn_point_value' , '1' ) ;
            }
            if ( isset( $_POST[ 'rs_redeem_point' ] ) && $_POST[ 'rs_redeem_point' ] != ('' || 0) ) {
                update_option( 'rs_redeem_point' , $_POST[ 'rs_redeem_point' ] ) ;
            } else {
                update_option( 'rs_redeem_point' , '1' ) ;
            }
            if ( isset( $_POST[ 'rs_redeem_point_value' ] ) && $_POST[ 'rs_redeem_point_value' ] != ('' || 0) ) {
                update_option( 'rs_redeem_point_value' , $_POST[ 'rs_redeem_point_value' ] ) ;
            } else {
                update_option( 'rs_redeem_point_value' , '1' ) ;
            }
            if ( isset( $_POST[ 'rewards_dynamic_rule' ] ) ) {
                update_option( 'rewards_dynamic_rule' , $_POST[ 'rewards_dynamic_rule' ] ) ;
            } else {
                update_option( 'rewards_dynamic_rule' , '' ) ;
            }
            if ( isset( $_POST[ 'rewards_dynamic_rule_purchase_history' ] ) ) {
                update_option( 'rewards_dynamic_rule_purchase_history' , $_POST[ 'rewards_dynamic_rule_purchase_history' ] ) ;
            } else {
                update_option( 'rewards_dynamic_rule_purchase_history' , '' ) ;
            }
        }

        public static function set_default_value() {
            foreach ( RSGeneralTabSetting::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) )
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
        }

        /*
         * Function to Select user for banning
         */

        public static function ban_user_for_earning() {
            $field_id    = "rs_banned_users_list_for_earning" ;
            $field_label = "Select the User(s)" ;
            $getuser     = get_option( 'rs_banned_users_list_for_earning' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function ban_user_for_redeeming() {
            $field_id    = "rs_banned_users_list_for_redeeming" ;
            $field_label = "Select the User(s)" ;
            $getuser     = get_option( 'rs_banned_users_list_for_redeeming' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function reward_system_earning_points_conversion() {
            ?>
            <tr valign="top">
                <td class="forminp forminp-text">
                    <?php echo get_woocommerce_currency_symbol() ; ?> <input type="number" step="any" min="0" value="<?php echo get_option( 'rs_earn_point_value' ) ; ?>" style="max-width:50px;" id="rs_earn_point_value" name="rs_earn_point_value">
                    &nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                    <input type="number" step="any" min="0" value="<?php echo get_option( 'rs_earn_point' ) ; ?>" style="max-width:50px;" id="rs_earn_point" name="rs_earn_point"> <?php _e( 'Earning Point(s)' , SRP_LOCALE ) ; ?>
                </td>
            </tr>

            <?php
        }

        public static function reward_system_redeeming_points_conversion() {
            ?>
            <tr valign="top">
                <td class="forminp forminp-text">
                    <input type="number" step="any" min="0" value="<?php echo get_option( 'rs_redeem_point' ) ; ?>" style="max-width:50px;" id="rs_redeem_point" name="rs_redeem_point"> <?php _e( 'Redeeming Point(s)' , SRP_LOCALE ) ; ?>
                    &nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                    <?php echo get_woocommerce_currency_symbol() ; ?> 	<input type="number" step="any" min="0" value="<?php echo get_option( 'rs_redeem_point_value' ) ; ?>" style="max-width:50px;" id="rs_redeem_point_value" name="rs_redeem_point_value"></td>
            </td>
            </tr>
            <?php
        }

        public static function refresh_button_for_expired() {
            ?>
            <tr valign="top">
                <th>
                    <label for="rs_refresh_button" style="font-size:14px;"><?php _e( 'Update Expired Points for All Users' , SRP_LOCALE ) ; ?></label>
                </th>
                <td>
                    <input type="button" class="rs_refresh_button" value="<?php _e( 'Update Expired Points' , SRP_LOCALE ) ; ?>"  id="rs_refresh_button" name="rs_refresh_button"/>
                </td>
            </tr>
            <?php
        }

        public static function reset_general_tab() {
            $settings = RSGeneralTabSetting::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

        /*
         * Function to add table for Earning Level in Member Level Tab
         */

        public static function reward_system_add_table_to_action() {
            global $woocommerce ;
            wp_nonce_field( plugin_basename( __FILE__ ) , 'rsdynamicrulecreation' ) ;
            ?>
            <style type="text/css">
                .rs_add_free_product_user_levels{
                    width:100%;
                }
                .chosen-container-active{
                    position: absolute;
                }
            </style>            
            <table class="widefat fixed rs_sample" cellspacing="0">
                <thead>
                    <tr class="rsdynamicrulecreation">
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Level Name' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Reward Points' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Reward Points Earning Percentage' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Free Product(s)' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="rsdynamicrulecreation">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="manage-column column-columnname num" scope="col"> <span class="add button-primary"><?php _e( 'Add New Level' , SRP_LOCALE ) ; ?></span></td>
                    </tr>
                    <tr class="rsdynamicrulecreation">
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Level Name' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Reward Points' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Reward Points Earning Percentage' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Free Product(s)' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></th>

                    </tr>
                </tfoot>
                <tbody id="here">
                    <?php
                    $rewards_dynamic_rulerule = get_option( 'rewards_dynamic_rule' ) ;
                    if ( ! empty( $rewards_dynamic_rulerule ) ) {
                        if ( is_array( $rewards_dynamic_rulerule ) ) {
                            foreach ( $rewards_dynamic_rulerule as $i => $rewards_dynamic_rule ) {
                                ?>
                                <tr class="rsdynamicrulecreation">
                                    <td class="column-columnname">
                                        <input type="text" name="rewards_dynamic_rule[<?php echo $i ; ?>][name]" class="short" value="<?php echo $rewards_dynamic_rule[ 'name' ] ; ?>"/>
                                    </td>
                                    <td class="column-columnname">
                                        <input type="number" step="any" min="0" name="rewards_dynamic_rule[<?php echo $i ; ?>][rewardpoints]" id="rewards_dynamic_rewardpoints<?php echo $i ; ?>" class="short" value="<?php echo $rewards_dynamic_rule[ 'rewardpoints' ] ; ?>"/>
                                    </td>
                                    <td class="column-columnname">
                                        <input type ="number" name="rewards_dynamic_rule[<?php echo $i ; ?>][percentage]" id="rewards_dynamic_rule_percentage<?php echo $i ; ?>" class="short test" value="<?php echo $rewards_dynamic_rule[ 'percentage' ] ; ?>"/>
                                    </td>
                                    <td class="column-columnname">
                                        <?php
                                        if ( ( float ) $woocommerce->version > ( float ) ('2.2.0') ) {
                                            if ( $woocommerce->version >= ( float ) ('3.0.0') ) {
                                                ?>                                                    
                                                <select class="wc-product-search" multiple="multiple" style="width: 100%;" id="rewards_dynamic_rule[<?php echo $i ; ?>]['product_list'][]" name="rewards_dynamic_rule[<?php echo $i ; ?>][product_list][]" data-placeholder="<?php _e( 'Search for a product' , 'woocommerce' ) ; ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true">
                                                    <?php
                                                    $json_ids = array() ;
                                                    if ( isset( $rewards_dynamic_rule[ 'product_list' ] ) && $rewards_dynamic_rule[ 'product_list' ] != "" ) {
                                                        $list_of_produts = $rewards_dynamic_rule[ 'product_list' ] ;
                                                        if ( is_array( $list_of_produts ) && ! empty( $list_of_produts ) ) {
                                                            $product_ids = $list_of_produts ;
                                                        } else {
                                                            $product_ids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $list_of_produts ) ) ) ;
                                                        }
                                                        foreach ( $product_ids as $product_id ) {
                                                            $product = srp_product_object( $product_id ) ;
                                                            if ( is_object( $product ) ) {
                                                                $json_ids = wp_kses_post( $product->get_formatted_name() ) ;
                                                                ?> <option value="<?php echo $product_id ; ?>" selected="selected"><?php echo esc_html( $json_ids ) ; ?></option><?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <?php
                                            } else {
                                                ?>
                                                <input type="hidden" class="wc-product-search" style="width: 100%;" id="rewards_dynamic_rule[<?php echo $i ; ?>][product_list][]" name="rewards_dynamic_rule[<?php echo $i ; ?>][product_list][]" data-placeholder="<?php _e( 'Search for a product' , 'woocommerce' ) ; ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" data-selected="<?php
                                                $json_ids = array() ;
                                                if ( $rewards_dynamic_rule[ 'product_list' ] != "" ) {
                                                    $list_of_produts = $rewards_dynamic_rule[ 'product_list' ] ;
                                                    if ( is_array( $list_of_produts ) && ! empty( $list_of_produts ) ) {
                                                        $product_ids = $list_of_produts ;
                                                    } else {
                                                        $product_ids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $list_of_produts ) ) ) ;
                                                    }
                                                    foreach ( $product_ids as $product_id ) {
                                                        $product = srp_product_object( $product_id ) ;
                                                        if ( is_object( $product ) ) {
                                                            $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() ) ;
                                                        }
                                                    } echo esc_attr( json_encode( $json_ids ) ) ;
                                                }
                                                ?>" value="<?php echo implode( ',' , array_keys( $json_ids ) ) ; ?>" /><?php
                                                   }
                                               } else {
                                                   echo rs_common_ajax_function_to_select_products( 'rs_add_free_product_user_levels' ) ;
                                                   ?>
                                            <!-- For Old Version -->
                                            <select multiple name="rewards_dynamic_rule[<?php echo $i ; ?>][product_list][]" class="rs_add_free_product_user_levels">
                                                <?php
                                                if ( $rewards_dynamic_rule[ 'product_list' ] != "" ) {
                                                    $list_of_produts = $rewards_dynamic_rule[ 'product_list' ] ;
                                                    if ( is_array( $list_of_produts ) && ! empty( $list_of_produts ) ) {
                                                        $product_ids = $list_of_produts ;
                                                    } else {
                                                        $product_ids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $list_of_produts ) ) ) ;
                                                    }
                                                    foreach ( $product_ids as $rs_free_id ) {
                                                        echo '<option value="' . $rs_free_id . '" ' ;
                                                        selected( 1 , 1 ) ;
                                                        echo '>' . ' #' . $rs_free_id . ' &ndash; ' . get_the_title( $rs_free_id ) ;
                                                        ?>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <option value=""></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td class="column-columnname num">
                                        <span class="remove button-secondary"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></span>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <script type="text/javascript">
                jQuery( document ).ready( function ( ) {
                jQuery( ".add" ).on( 'click' , function ( ) {
                var countrewards_dynamic_rule = Math.round( new Date( ).getTime( ) + ( Math.random( ) * 100 ) ) ;
            <?php
            if ( ( float ) $woocommerce->version > ( float ) ('2.2.0') ) {
                if ( $woocommerce->version >= ( float ) ('3.0.0') ) {
                    ?>
                        jQuery( '#here' ).append( '<tr><td><input type="text" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></td>\n\
                                                                                                                                                                                                                            \n\<td><input type="number" step="any" min="0" id="rewards_dynamic_ruleamount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][rewardpoints]" class="short" value=""/></td>\n\
                                                                                                                                                                                                                            \n\\n\
                                                                                                                                                                                                                            <td><input type ="number" id="rewards_dynamic_rule_claimcount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][percentage]" class="short test"  value=""/></td>\n\\n\
                                                                                                                                                                                                                            \n\<td>\n\
                                                                                                                                                                                                                            \n\
                                                                                                                                                                                                                            <select style="width:100%;" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][product_list][]" class="wc-product-search" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="true"></select></td>n\
                                                                                                                                                                                                                            <td class="num"><span class="remove button-secondary">Remove Level</span></td></tr><hr>' ) ;
                        jQuery( 'body' ).trigger( 'wc-enhanced-select-init' ) ;
                <?php } else {
                    ?>
                        jQuery( '#here' ).append( '<tr><td><input type="text" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></td>\n\
                                                                                                                                                                                                                            \n\<td><input type="number" step="any" min="0" id="rewards_dynamic_ruleamount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][rewardpoints]" class="short" value=""/></td>\n\
                                                                                                                                                                                                                            \n\\n\
                                                                                                                                                                                                                            <td><input type ="number" id="rewards_dynamic_rule_claimcount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][percentage]" class="short test"  value=""/></td>\n\\n\
                                                                                                                                                                                                                            \n\<td>\n\
                                                                                                                                                                                                                            \n\
                                                                                                                                                                                                                            <input type=hidden style="width:100%;" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][product_list][]" class="wc-product-search" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="true"/></td>n\
                                                                                                                                                                                                                            <td class="num"><span class="remove button-secondary">Remove Level</span></td></tr><hr>' ) ;
                        jQuery( 'body' ).trigger( 'wc-enhanced-select-init' ) ;
                <?php } ?>
            <?php } else { ?>
                    jQuery( '#here' ).append( '<tr><td><input type="text" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></td>\n\
                                                                                                                                                                                                                        \n\<td><input type="number" step="any" min="0" id="rewards_dynamic_ruleamount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][rewardpoints]" class="short" value=""/></td>\n\
                                                                                                                                                                                                                        \n\\n\
                                                                                                                                                                                                                        <td><input type ="number" id="rewards_dynamic_rule_claimcount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][percentage]" class="short test"  value=""/></td>\n\\n\
                                                                                                                                                                                                                        \n\\n\
                                                                                                                                                                                                                        \n\<td><select multiple name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][product_list][]" class="rs_add_free_product_user_levels"><option value=""></option></select></td>n\
                                                                                                                                                                                                                        <td class="num"><span class="remove button-secondary">Remove Level</span></td></tr><hr>' ) ;
            <?php } if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) { ?>
                    jQuery( function ( ) {
                    jQuery( "select.rs_add_free_product_user_levels" ).ajaxChosen( {
                    method : 'GET' ,
                            url : '<?php echo SRP_ADMIN_AJAX_URL ; ?>' ,
                            dataType : 'json' ,
                            afterTypeDelay : 100 ,
                            data : {
                            action : 'woocommerce_json_search_products_and_variations' ,
                                    security : '<?php echo wp_create_nonce( "search-products" ) ; ?>'
                            }
                    } , function ( data ) {
                    var terms = { } ;
                    jQuery.each( data , function ( i , val ) {
                    terms[i] = val ;
                    } ) ;
                    return terms ;
                    } ) ;
                    } ) ;
            <?php } ?>
                return false ;
                } ) ;
                jQuery( document ).on( 'click' , '.remove' , function ( ) {
                jQuery( this ).parent( ).parent( ).remove( ) ;
                } ) ;
                jQuery( '#rs_enable_user_role_based_reward_points' ).addClass( 'rs_enable_user_role_based_reward_points' ) ;
                jQuery( '#rs_enable_earned_level_based_reward_points' ).addClass( 'rs_enable_user_role_based_reward_points' ) ;
                } ) ;</script>
            <?php
        }

        public static function add_rule_for_purchase_history() {
            wp_nonce_field( plugin_basename( __FILE__ ) , 'rsdynamicrulecreationsforuserpurchasehistory' ) ;
            ?>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr class="rsdynamicrulecreationsforuserpurchasehistory">
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Level Name' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Type' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Value' , SRP_LOCALE ) ; ?></th>      
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Percentage' , SRP_LOCALE ) ; ?></th>   
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="rsdynamicrulecreationsforuserpurchasehistory">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="manage-column column-columnname num" scope="col"> <span class="rs_add_new_level button-primary"><?php _e( 'Add New Level' , SRP_LOCALE ) ; ?></span></td>
                    </tr>
                    <tr class="rsdynamicrulecreationsforuserpurchasehistory">
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Level Name' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Type' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Value' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Percentage' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </tfoot>
                <tbody id="rs_table_data_for_user_purchase_history">
                    <?php
                    $rewards_dynamic_rulerule = get_option( 'rewards_dynamic_rule_purchase_history' ) ;
                    if ( srp_check_is_array( $rewards_dynamic_rulerule ) ) {
                        foreach ( $rewards_dynamic_rulerule as $i => $rewards_dynamic_rule ) {
                            ?>
                            <tr class="rsdynamicrulecreationsforuserpurchasehistory">
                                <td class="column-columnname">
                                    <p class="form-field">
                                        <input type="text" name="rewards_dynamic_rule_purchase_history[<?php echo $i ; ?>][name]" value="<?php echo $rewards_dynamic_rule[ 'name' ] ; ?>"/>
                                    </p>
                                </td>
                                <td class="column-columnname">
                                    <p class="form-field">
                                        <select style="width:225px !important;" name="rewards_dynamic_rule_purchase_history[<?php echo $i ; ?>][type]" id="rewards_dynamic_rule_purchase_history<?php echo $i ; ?>"  />
                        <option value="1" <?php selected( '1' , $rewards_dynamic_rule[ 'type' ] ) ; ?>><?php _e( 'Number of Successful Order(s)' , SRP_LOCALE ) ; ?></option>
                        <option value="2" <?php selected( '2' , $rewards_dynamic_rule[ 'type' ] ) ; ?>><?php _e( 'Total Amount Spent in Site' , SRP_LOCALE ) ; ?></option>
                    </select> 
                    </p>
                    </td>
                    <td class="column-columnname">
                        <p class="form-field">
                            <input type ="number" name="rewards_dynamic_rule_purchase_history[<?php echo $i ; ?>][value]" id="rewards_dynamic_rule_purchase_historyvalue<?php echo $i ; ?>" class="short test" value="<?php echo $rewards_dynamic_rule[ 'value' ] ; ?>"/>
                        </p>
                    </td>
                    <td class="column-columnname">
                        <p class="form-field">
                            <input type ="number" name="rewards_dynamic_rule_purchase_history[<?php echo $i ; ?>][percentage]" id="rewards_dynamic_rule_purchase_historypercentage<?php echo $i ; ?>" class="short test" value="<?php echo $rewards_dynamic_rule[ 'percentage' ] ; ?>"/>
                        </p>
                    </td>

                    <td class="column-columnname num">
                        <span class="remove button-secondary"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></span>
                    </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
            </table>
            <script type="text/javascript">
                jQuery( document ).ready( function ( ) {
                jQuery( ".rs_add_new_level" ).on( 'click' , function ( ) {
                var countrewards_dynamic_rule = Math.round( new Date( ).getTime( ) + ( Math.random( ) * 100 ) ) ;
            <?php ?>
                jQuery( '#rs_table_data_for_user_purchase_history' ).append( '<tr class="rsdynamicrulecreationsforuserpurchasehistory"><td><p class="form-field"><input type="text" name="rewards_dynamic_rule_purchase_history[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></p></td>\n\
            <td><p class="form-field"><select style="width:225px !important;" id="rewards_dynamic_rule_purchase_history' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_purchase_history[' + countrewards_dynamic_rule + '][type]" class="short">\n\
            <option value="1"><?php _e( 'Number of Successful Order(s)' , SRP_LOCALE ) ; ?></option>\n\
            <option value="2"><?php _e( 'Total Amount Spent in Site' , SRP_LOCALE ) ; ?></select></p></td>\n\
            <td><p class="form-field"><input type ="number" id="rewards_dynamic_rule_purchase_history' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_purchase_history[' + countrewards_dynamic_rule + '][value]" class="short test"  value=""/></p></td>\n\
             <td><p class="form-field"><input type ="number" id="rewards_dynamic_rule_purchase_history' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_purchase_history[' + countrewards_dynamic_rule + '][percentage]" class="short"  value=""/></p></td>\n\
            <td class="num"><span class="remove button-secondary"><?php _e( 'Remove Rule' , SRP_LOCALE ) ; ?></span></td></tr><hr>' ) ;
                return false ;
                } ) ;
                jQuery( document ).on( 'click' , '.remove' , function ( ) {
                jQuery( this ).parent( ).parent( ).remove( ) ;
                } ) ;
                } ) ;
            </script>
            <?php
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
                            'name'     => __( 'Reward Points Earning Percentage for ' . $key . ' User Role' , SRP_LOCALE ) ,
                            'desc'     => __( 'Earning Percentage of Reward Points for ' . $key . 'user role' , SRP_LOCALE ) ,
                            'class'    => 'rewardpoints_userrole' ,
                            'id'       => 'rs_reward_user_role_' . $value ,
                            'std'      => '100' ,
                            'type'     => 'text' ,
                            'newids'   => 'rs_reward_user_role_' . $value ,
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