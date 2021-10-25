<?php
/*
 * Reward Points for Action Tab Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSRewardPointsForAction' ) ) {

    class RSRewardPointsForAction {

        public static function init() {
            add_action( 'woocommerce_rs_settings_tabs_fpactionreward' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpactionreward' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system

            if ( class_exists( 'bbPress' ) )
                add_filter( 'woocommerce_fpactionreward_settings' , array( __CLASS__ , 'add_field_for_create_topic' ) ) ;

            if ( class_exists( 'BuddyPress' ) )
                add_filter( 'woocommerce_fpactionreward_settings' , array( __CLASS__ , 'add_field_for_create_post' ) ) ;

            if ( class_exists( 'FPWaitList' ) )
                add_filter( 'woocommerce_fpactionreward_settings' , array( __CLASS__ , 'add_field_for_bsn' ) ) ;

            if ( class_exists( 'FPWCRS' ) ) {
                add_filter( 'woocommerce_fpactionreward_settings' , array( __CLASS__ , 'add_field_for_social_and_custom_field' ) ) ;

                add_filter( 'woocommerce_fpactionreward_settings' , array( __CLASS__ , 'add_field_for_social_and_custom_field_account_linking' ) ) ;
            }

            add_action( 'rs_default_settings_fpactionreward' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_filter( 'woocommerce_fpactionreward_settings' , array( __CLASS__ , 'reward_system_add_settings_to_action' ) ) ;

            add_action( 'woocommerce_admin_field_rs_coupon_usage_points_dynamics' , array( __CLASS__ , 'reward_add_coupon_usage_points_to_action' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fpactionreward' , array( __CLASS__ , 'reset_action_tab' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_reward_action_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'woocommerce_admin_field_rs_fpwcrs_rule_for_custom_reg_fields' , array( __CLASS__ , 'rs_fpwcrs_rule_for_custom_reg_fields' ) ) ;

            add_action( 'rs_display_save_button_fpactionreward' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpactionreward' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        public static function add_field_for_social_and_custom_field( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && 'rs_signup_reward_points_setting' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'     => __( 'Social Network Account Signup Reward Points' , SRP_LOCALE ) ,
                        'id'       => 'rs_reward_for_social_network_signup' ,
                        'std'      => '' ,
                        'default'  => '' ,
                        'type'     => 'text' ,
                        'newids'   => 'rs_reward_for_social_network_signup' ,
                        'desc_tip' => true
                            ) ;
                }
                if ( isset( $section[ 'id' ] ) && '_rs_reward_point_action' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'     => __( 'Daily Login Reward Points for Social Login' , SRP_LOCALE ) ,
                        'id'       => 'rs_reward_for_social_network_login' ,
                        'std'      => '' ,
                        'default'  => '' ,
                        'type'     => 'text' ,
                        'newids'   => 'rs_reward_for_social_network_login' ,
                        'desc_tip' => true
                            ) ;
                }
                $updated_settings[] = $section ;
            }
            return $updated_settings ;
        }

        public static function add_field_for_social_and_custom_field_account_linking( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && '_rs_fpwcrs_title' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'    => __( 'Social Account Linking Reward Points' , SRP_LOCALE ) ,
                        'desc'    => __( 'By enabling this option, you can award points when users link any social network' , SRP_LOCALE ) ,
                        'id'      => 'rs_enable_for_social_account_linking' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'type'    => 'checkbox' ,
                        'newids'  => 'rs_enable_for_social_account_linking' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Points to Award' , SRP_LOCALE ) ,
                        'id'      => 'rs_reward_for_social_account_linking' ,
                        'std'     => '' ,
                        'default' => '' ,
                        'type'    => 'text' ,
                        'newids'  => 'rs_reward_for_social_account_linking' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Email Notification for Social Account Linking' , SRP_LOCALE ) ,
                        'desc'    => __( 'Enabling this option, users can receive email notification for linking any social network on the account details menu' , SRP_LOCALE ) ,
                        'id'      => 'rs_send_mail_for_social_account_linking' ,
                        'type'    => 'checkbox' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'newids'  => 'rs_send_mail_for_social_account_linking' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Email Subject' , SRP_LOCALE ) ,
                        'id'      => 'rs_email_subject_for_social_account_linking' ,
                        'std'     => 'Social Account Linking – Notification' ,
                        'default' => 'Social Account Linking – Notification' ,
                        'type'    => 'textarea' ,
                        'newids'  => 'rs_email_subject_for_social_account_linking' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Message' , SRP_LOCALE ) ,
                        'id'      => 'rs_email_message_for_social_account_linking' ,
                        'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                        'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                        'type'    => 'textarea' ,
                        'newids'  => 'rs_email_message_for_social_account_linking' ,
                            ) ;
                }
                $updated_settings[] = $section ;
            }
            return $updated_settings ;
        }

        public static function add_field_for_bsn( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && '_rs_waitlist_subscribing_title' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'    => __( 'Reward Points for Subscribing Out of Stock Products' , SRP_LOCALE ) ,
                        'desc'    => __( 'By enabling this option, you can award points when users subscribe the out of stock product(s)' , SRP_LOCALE ) ,
                        'id'      => 'rs_enable_for_waitlist_subscribing' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'type'    => 'checkbox' ,
                        'newids'  => 'rs_enable_for_waitlist_subscribing' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Points to Award' , SRP_LOCALE ) ,
                        'id'      => 'rs_reward_for_waitlist_subscribing' ,
                        'std'     => '' ,
                        'default' => '' ,
                        'type'    => 'text' ,
                        'newids'  => 'rs_reward_for_waitlist_subscribing' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Send Email Notification to Subscribed Users' , SRP_LOCALE ) ,
                        'desc'    => __( 'Enabling this option will send the email notification to subscribed users regarding points earned for subscribing out of stock product(s)' , SRP_LOCALE ) ,
                        'id'      => 'rs_send_mail_for_waitlist_subscribing' ,
                        'type'    => 'checkbox' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'newids'  => 'rs_send_mail_for_waitlist_subscribing' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Email Subject' , SRP_LOCALE ) ,
                        'id'      => 'rs_email_subject_for_waitlist_subscribing' ,
                        'std'     => 'Subscribed Products[Out of Stock] – Notification' ,
                        'default' => 'Subscribed Products[Out of Stock] – Notification' ,
                        'type'    => 'textarea' ,
                        'newids'  => 'rs_email_subject_for_waitlist_subscribing' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Message' , SRP_LOCALE ) ,
                        'id'      => 'rs_email_message_for_waitlist_subscribing' ,
                        'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                        'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                        'type'    => 'textarea' ,
                        'newids'  => 'rs_email_message_for_waitlist_subscribing' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Reward Points for purchasing In-Stock Products' , SRP_LOCALE ) ,
                        'desc'    => __( 'By enabling this option, you can award points when the subscribed users purchases in-stock product(s) ' , SRP_LOCALE ) ,
                        'id'      => 'rs_enable_for_waitlist_sale_conversion' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'type'    => 'checkbox' ,
                        'newids'  => 'rs_enable_for_waitlist_sale_conversion' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Points to Award' , SRP_LOCALE ) ,
                        'id'      => 'rs_reward_for_waitlist_sale_conversion' ,
                        'std'     => '' ,
                        'default' => '' ,
                        'type'    => 'text' ,
                        'newids'  => 'rs_reward_for_waitlist_sale_conversion' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Send Email Notification to Subscribed Users' , SRP_LOCALE ) ,
                        'desc'    => __( 'Enabling this option will send the email notification to subscribed users regarding points earned for purchasing in-stock products' , SRP_LOCALE ) ,
                        'id'      => 'rs_send_mail_for_waitlist_sale_conversion' ,
                        'type'    => 'checkbox' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'newids'  => 'rs_send_mail_for_waitlist_sale_conversion' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Email Subject' , SRP_LOCALE ) ,
                        'id'      => 'rs_email_subject_for_waitlist_sale_conversion' ,
                        'std'     => 'Subscribed Products[In Stock] Purchased – Notification' ,
                        'default' => 'Subscribed Products[In Stock] Purchased – Notification' ,
                        'type'    => 'textarea' ,
                        'newids'  => 'rs_email_subject_for_waitlist_sale_conversion' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Message' , SRP_LOCALE ) ,
                        'id'      => 'rs_email_message_for_waitlist_sale_conversion' ,
                        'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                        'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                        'type'    => 'textarea' ,
                        'newids'  => 'rs_email_message_for_waitlist_sale_conversion' ,
                            ) ;
                }
                $updated_settings[] = $section ;
            }
            return $updated_settings ;
        }

        public static function add_field_for_create_post( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                $updated_settings[] = $section ;
                if ( isset( $section[ 'id' ] ) && 'rs_page_comment_reward_points_setting' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name' => __( 'BuddyPress Reward Points' , SRP_LOCALE ) ,
                        'type' => 'title' ,
                        'id'   => '_rs_reward_point_for_post_bp'
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Post Creation Reward Points' , SRP_LOCALE ) ,
                        'id'      => 'rs_enable_points_for_bp_post_create' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'type'    => 'checkbox' ,
                        'newids'  => 'rs_enable_points_for_bp_post_create' ,
                            ) ;

                    $updated_settings[] = array(
                        'name'    => __( 'Post Creation Reward Points' , SRP_LOCALE ) ,
                        'id'      => 'rs_points_for_bp_post_create' ,
                        'std'     => '' ,
                        'default' => '' ,
                        'type'    => 'text' ,
                        'newids'  => 'rs_points_for_bp_post_create' ,
                            ) ;

                    $updated_settings[] = array(
                        'name'    => __( 'Post Comment Reward Points' , SRP_LOCALE ) ,
                        'id'      => 'rs_enable_points_for_bp_postcomment' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'type'    => 'checkbox' ,
                        'newids'  => 'rs_enable_points_for_bp_postcomment' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Post Comment Reward Points' , SRP_LOCALE ) ,
                        'id'      => 'rs_points_for_bp_postcomment' ,
                        'std'     => '' ,
                        'default' => '' ,
                        'type'    => 'text' ,
                        'newids'  => 'rs_points_for_bp_postcomment' ,
                            ) ;

                    $updated_settings[] = array(
                        'name'    => __( 'Group Creation Reward Points' , SRP_LOCALE ) ,
                        'id'      => 'rs_enable_points_for_bp_group_create' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'type'    => 'checkbox' ,
                        'newids'  => 'rs_enable_points_for_bp_group_create' ,
                            ) ;

                    $updated_settings[] = array(
                        'name'    => __( 'Group Creation Reward Points' , SRP_LOCALE ) ,
                        'id'      => 'rs_points_for_bp_group_create' ,
                        'std'     => '' ,
                        'default' => '' ,
                        'type'    => 'text' ,
                        'newids'  => 'rs_points_for_bp_group_create' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Number of groups to restrict the points' , SRP_LOCALE ) ,
                        'id'      => 'rs_points_for_bp_group_create_limit' ,
                        'std'     => '1' ,
                        'default' => '1' ,
                        'type'    => 'text' ,
                        'newids'  => 'rs_points_for_bp_group_create_limit' ,
                            ) ;

                    $updated_settings[] = array(
                        'type' => 'sectionend' ,
                        'id'   => '_rs_reward_point_for_post_bp'
                            ) ;
                }
            }
            return $updated_settings ;
        }

        public static function add_field_for_create_topic( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                $updated_settings[] = $section ;
                if ( isset( $section[ 'id' ] ) && 'rs_page_comment_reward_points_setting' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name' => __( 'bbPress Reward Points' , SRP_LOCALE ) ,
                        'type' => 'title' ,
                        'id'   => '_rs_reward_point_for_topic'
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Topic Creation Reward Points' , SRP_LOCALE ) ,
                        'id'      => 'rs_enable_reward_points_for_create_topic' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'type'    => 'checkbox' ,
                        'newids'  => 'rs_enable_reward_points_for_create_topic' ,
                            ) ;

                    $updated_settings[] = array(
                        'name'    => __( 'Topic Creation Reward Points' , SRP_LOCALE ) ,
                        'id'      => 'rs_reward_points_for_creatic_topic' ,
                        'std'     => '' ,
                        'default' => '' ,
                        'type'    => 'text' ,
                        'newids'  => 'rs_reward_points_for_creatic_topic' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Topic Reply Reward Points' , SRP_LOCALE ) ,
                        'id'      => 'rs_enable_reward_points_for_reply_topic' ,
                        'std'     => 'no' ,
                        'default' => 'no' ,
                        'type'    => 'checkbox' ,
                        'newids'  => 'rs_enable_reward_points_for_reply_topic' ,
                            ) ;

                    $updated_settings[] = array(
                        'name'    => __( 'Topic Reply Reward Points' , SRP_LOCALE ) ,
                        'id'      => 'rs_reward_points_for_reply_topic' ,
                        'std'     => '' ,
                        'default' => '' ,
                        'type'    => 'text' ,
                        'newids'  => 'rs_reward_points_for_reply_topic' ,
                            ) ;

                    $updated_settings[] = array(
                        'type' => 'sectionend' ,
                        'id'   => '_rs_reward_point_for_topic'
                            ) ;
                }
            }
            return $updated_settings ;
        }

        /*
         * Function for label Settings in Reward Points For Action.
         */

        public static function reward_system_admin_fields() {
            return apply_filters( 'woocommerce_fpactionreward_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Action Reward Points Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_reward_action_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_reward_action_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_reward_action_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Signup Reward Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_signup_reward_points_setting' ,
                ) ,
                array(
                    'name'    => __( 'Enable Signup Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option for Signup Reward Points' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => '_rs_enable_signup' ,
                    'newids'  => '_rs_enable_signup' ,
                    'class'   => '_rs_enable_signup' ,
                    'std'     => 'yes' ,
                    'default' => 'yes' ,
                ) ,
                array(
                    'name'     => __( 'Account Signup Reward Points is Awarded for' , SRP_LOCALE ) ,
                    'desc'     => __( 'This option controls whether account signup reward points should be awarded for any registered user/users registered through referral links' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_account_signup_points_award' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_select_account_signup_points_award' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'options'  => array(
                        '1' => __( 'All Users' , SRP_LOCALE ) ,
                        '2' => __( 'Referred Users' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Award Account Signup Reward Points only after First Purchase' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will award account signup reward points only after first purchase' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_signup_after_first_purchase' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_reward_signup_after_first_purchase' ,
                ) ,
                array(
                    'name'    => __( 'Prevent Product Purchase Reward Points for First Purchase' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to prevent product purchase reward points for first purchase' , SRP_LOCALE ) ,
                    'id'      => 'rs_signup_points_with_purchase_points' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_signup_points_with_purchase_points' ,
                ) ,
                array(
                    'name'    => __( 'Account Signup Reward Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_signup' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_reward_signup' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Account Signup Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Account Signup Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_account_signup' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_account_signup' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Account Signup Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_account_signup' ,
                    'std'     => 'Account Signup - Notification' ,
                    'default' => 'Account Signup - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_account_signup' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Account Signup Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_account_signup' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_account_signup' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_signup_reward_points_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_fpwcrs_compatible_start' ,
                ) ,
                array(
                    'name' => __( 'Custom Registration Fields Reward Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_fpwcrs_custom_reg_field_title' ,
                ) ,
                array(
                    'name'    => __( 'Custom Registration Fields Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'By enabling this option, users can earn points for entering the details in custom fields during registration' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_points_for_cus_field_reg' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_points_for_cus_field_reg' ,
                ) ,
                array(
                    'name'    => __( 'Email Notification for Custom Registration Fields Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'By enabling this option, users can receive email notification for entering the details in custom fields during registration' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_cus_field_reg' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_cus_field_reg' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Custom Field Registration Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_cus_field_reg' ,
                    'std'     => 'Custom Registration Fields - Notification' ,
                    'default' => 'Custom Registration Fields - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_cus_field_reg' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Custom Field Registration Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_cus_field_reg' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_cus_field_reg' ,
                ) ,
                array(
                    'type' => 'rs_fpwcrs_rule_for_custom_reg_fields'
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_fpwcrs_custom_reg_field_title' ) ,
                array(
                    'type' => 'rs_fpwcrs_compatible_end' ,
                ) ,
                array(
                    'type' => 'rs_bsn_compatible_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points for Subscribing Out of Stock Products' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_waitlist_subscribing_title' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_waitlist_subscribing_title' ) ,
                array(
                    'type' => 'rs_bsn_compatible_end' ,
                ) ,
                array(
                    'type' => 'rs_fpwcrs_compatible_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points for Social Account Linking' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_fpwcrs_title' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_fpwcrs_title' ) ,
                array(
                    'type' => 'rs_fpwcrs_compatible_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Product Review Reward Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_product_review_reward_points_setting' ,
                ) ,
                array(
                    'name'    => __( 'Product Review Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'By enabling this option you can award reward points for product review' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_product_review_points' ,
                    'std'     => 'yes' ,
                    'default' => 'yes' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_product_review_points' ,
                ) ,
                array(
                    'name'    => __( 'Status on which Product Review Reward Points should be awarded' , SRP_LOCALE ) ,
                    'id'      => 'rs_review_reward_status' ,
                    'class'   => 'rs_review_reward_status' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'radio' ,
                    'options' => array( '1' => 'Approve' , '2' => 'Unapprove' ) ,
                    'newids'  => 'rs_review_reward_status' ,
                ) ,
                array(
                    'name'    => __( 'Product Review Reward Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_product_review' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_reward_product_review' ,
                ) ,
                array(
                    'name'    => __( 'Restrict Product Review Reward Points to One Review per Product per User' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will restrict product review reward points will be awarded only for one product per user' , SRP_LOCALE ) ,
                    'id'      => 'rs_restrict_reward_product_review' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_restrict_reward_product_review' ,
                ) ,
                array(
                    'name'    => __( 'Product Review Reward Points should be awarded only for Purchased User' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will award product review reward points only for reviews made by purchased user' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_for_comment_product_review' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_reward_for_comment_product_review' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Product Review Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Product Review Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_product_review' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_product_review' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Product Review Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_product_review' ,
                    'std'     => 'Product Review - Notification' ,
                    'default' => 'Product Review - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_product_review' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Product Review Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_product_review' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [points shortcode] points and currently you have [available points shortcode] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_product_review' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_product_review_reward_points_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Blog Post Reward Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_blog_post_reward_points_setting' ,
                ) ,
                array(
                    'name'    => __( 'Blog Post Creation Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'By enabling this option you can award reward points for blog post creation' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_for_Creating_Post' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_reward_for_Creating_Post' ,
                ) ,
                array(
                    'name'    => __( 'Blog Post Creation Reward Points Visibility Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_post_visible_for' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => 'Public' ,
                        '2' => 'Private' ,
                    ) ,
                    'newids'  => 'rs_post_visible_for' ,
                ) ,
                array(
                    'name'    => __( 'Blog Post Creation Reward Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_post' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_reward_post' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Blog Post Creation Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Blog Post Creation Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_blog_post_create' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_blog_post_create' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Blog Post Creation Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_blog_post_create' ,
                    'std'     => 'Blog Post Creation - Notification' ,
                    'default' => 'Blog Post Creation - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_blog_post_create' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Blog Post Creation Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_blog_post_create' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_blog_post_create' ,
                ) ,
                array(
                    'name'    => __( 'Blog Post Comment Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'By Enabling this option you can award reward points for blog post comment' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_for_comment_Post' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_reward_for_comment_Post' ,
                ) ,
                array(
                    'name'    => __( 'Status on which Post Comment Reward Points should be awarded' , SRP_LOCALE ) ,
                    'id'      => 'rs_post_comment_reward_status' ,
                    'class'   => 'rs_post_comment_reward_status' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'radio' ,
                    'options' => array( '1' => 'Approve' , '2' => 'Unapprove' ) ,
                    'newids'  => 'rs_post_comment_reward_status' ,
                ) ,
                array(
                    'name'    => __( 'Blog Post Comment Reward Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_post_review' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_reward_post_review' ,
                ) ,
                array(
                    'name'    => __( 'Restrict Post Comment Reward Points to One Comment per Post per User' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will restrict post comment reward points will be awarded only for one post per user' , SRP_LOCALE ) ,
                    'id'      => 'rs_restrict_reward_post_comment' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_restrict_reward_post_comment' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Blog Post Comment Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Blog Post Creation Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_blog_post_comment' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_blog_post_comment' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Blog Post Comment Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_blog_post_comment' ,
                    'std'     => 'Blog Post Comment - Notification' ,
                    'default' => 'Blog Post Comment - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_blog_post_comment' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Blog Post Comment Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_blog_post_comment' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_blog_post_comment' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_blog_post_reward_points_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Product Creation Reward Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_product_creation_reward_points_setting' ,
                ) ,
                array(
                    'name'    => __( 'Product Creation Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'By Enabling this option, you can award reward points for creating products' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_for_enable_product_create' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_reward_for_enable_product_create' ,
                ) ,
                array(
                    'name'              => __( 'Product Creation Reward Points' , SRP_LOCALE ) ,
                    'id'                => 'rs_reward_Product_create' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'type'              => 'number' ,
                    'newids'            => 'rs_reward_Product_create' ,
                    'custom_attributes' => array(
                        'min' => 0 ,
                    )
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Product Creation Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Product Creation Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_product_create' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_product_create' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Product Creation Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_product_create' ,
                    'std'     => 'Product Creation - Notification' ,
                    'default' => 'Product Creation - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_product_create' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Product Creation Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_product_create' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_product_create' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_product_creation_reward_points_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Page Comment Reward Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_page_comment_reward_points_setting' ,
                ) ,
                array(
                    'name'    => __( 'Page Comment Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'By Enabling this option, you can award reward points for commenting on pages' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_for_comment_Page' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_reward_for_comment_Page' ,
                ) ,
                array(
                    'name'    => __( 'Status on which Page Comment Reward Points should be awarded' , SRP_LOCALE ) ,
                    'id'      => 'rs_page_comment_reward_status' ,
                    'class'   => 'rs_page_comment_reward_status' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'radio' ,
                    'options' => array( '1' => 'Approve' , '2' => 'Unapprove' ) ,
                    'newids'  => 'rs_page_comment_reward_status' ,
                ) ,
                array(
                    'name'    => __( 'Page Comment Reward Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_page_review' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_reward_page_review' ,
                ) ,
                array(
                    'name'    => __( 'Restrict Page Comment Reward Points to One Comment per Page per User' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will restrict page comment reward points will be awarded only for one page per user' , SRP_LOCALE ) ,
                    'id'      => 'rs_restrict_reward_page_comment' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_restrict_reward_page_comment' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Page Comment Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Page Comment Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_page_comment' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_page_comment' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Page Comment Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_page_comment' ,
                    'std'     => 'Page Comment - Notification' ,
                    'default' => 'Page Comment - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_page_comment' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Page Comment Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_page_comment' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_page_comment' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_page_comment_reward_points_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Daily Login Reward Points Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_point_action'
                ) ,
                array(
                    'name'    => __( 'Daily Login Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'By Enabling this option, you can award reward points for daily login' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_reward_points_for_login' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_reward_points_for_login' ,
                ) ,
                array(
                    'name'    => __( 'Daily Login Reward Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_points_for_login' ,
                    'std'     => '10' ,
                    'default' => '10' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_reward_points_for_login' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Daily Login Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Daily Login Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_login' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_login' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Login Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_login' ,
                    'std'     => 'Login - Notification' ,
                    'default' => 'Login - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_login' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Login Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_login' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_login' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_action' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Payment Gateway Reward Points Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_point_for_payment_gateway' ,
                    'desc' => __( 'You can reward points for using specific payment gateway in order' , SRP_LOCALE )
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_for_payment_gateway' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'WooCommerce Coupon Usage Reward Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_point_coupon_message_settings'
                ) ,
                array(
                    'name'     => __( 'When different Points is associated with the same Coupon Code then' , SRP_LOCALE ) ,
                    'desc'     => __( 'This option controls what points should be awarded to user when different points are associated with the same coupon code' , SRP_LOCALE ) ,
                    'id'       => 'rs_choose_priority_level_selection_coupon_points' ,
                    'class'    => 'rs_choose_priority_level_selection_coupon_points' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'newids'   => 'rs_choose_priority_level_selection_coupon_points' ,
                    'options'  => array(
                        '1' => __( 'Rule with the highest number of points will be awarded' , SRP_LOCALE ) ,
                        '2' => __( 'Rule with the lowest number of points will be awarded' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Coupon Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Coupon Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_coupon_reward' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_coupon_reward' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Coupon Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_coupon_reward' ,
                    'std'     => 'Coupon Points - Notification' ,
                    'default' => 'Coupon Points - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_coupon_reward' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Coupon Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_coupon_reward' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_coupon_reward' ,
                ) ,
                array(
                    'name'    => __( 'Enable to Display Message displayed on cart page when Coupon Reward Points is Earned' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_coupon_reward_success_msg' ,
                    'std'     => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_coupon_reward_success_msg' ,
                    'default' => 'no' ,
                ) ,
                array(
                    'name'    => __( 'Message displayed on cart page when Coupon Reward Points is Earned' , SRP_LOCALE ) ,
                    'id'      => 'rs_coupon_applied_reward_success' ,
                    'std'     => 'You have received [coupon_rewardpoints] Points for using the coupon [coupon_name]' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_coupon_applied_reward_success' ,
                    'default' => 'You have received [coupon_rewardpoints] Points for using the coupon [coupon_name]' ,
                ) ,
                array(
                    'type' => 'rs_coupon_usage_points_dynamics' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_coupon_message_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Shortcode used in Coupon Reward Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_shortcode_for_coupon'
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<b>[coupon_rewardpoints]</b> - To display points earned for using coupon code<br><br>'
                    . '<b>[coupon_name]</b> - To display coupon name<br><br>'
                    . '<b>[gatewaypoints]</b> - Use this shortcode to display the payment gateway points at checkout while selecting the respective payment gateway. You can use this shortcode under '
                    . '<b>WooCommerce -> Settings -> Payments -> Choose the payment gateway -> use it on "Description" text area</b><br><br>' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_shortcode_for_coupon' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                )
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( self::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( self::reward_system_admin_fields() ) ;

            if ( isset( $_POST[ 'rewards_dynamic_rule_coupon_usage' ] ) ) {
                $rewards_dynamic_rule_couponpoints = array_values( $_POST[ 'rewards_dynamic_rule_coupon_usage' ] ) ;
                update_option( 'rewards_dynamic_rule_couponpoints' , $rewards_dynamic_rule_couponpoints ) ;
            } else {
                update_option( 'rewards_dynamic_rule_couponpoints' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_reward_action_module_checkbox' ] ) ) {
                update_option( 'rs_reward_action_activated' , $_POST[ 'rs_reward_action_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_reward_action_activated' , 'no' ) ;
            }
            if ( isset( $_POST[ 'rs_rule_for_custom_reg_field' ] ) ) {
                update_option( 'rs_rule_for_custom_reg_field' , $_POST[ 'rs_rule_for_custom_reg_field' ] ) ;
            } else {
                update_option( 'rs_rule_for_custom_reg_field' , '' ) ;
            }
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( self::reward_system_admin_fields() as $setting ) {
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
            }
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_reward_action_activated' ) , 'rs_reward_action_module_checkbox' , 'rs_reward_action_activated' ) ;
        }

        public static function reward_system_add_settings_to_action( $settings ) {
            $updated_settings = array() ;
            $mainvariable     = array() ;
            global $woocommerce ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && '_rs_reward_point_for_payment_gateway' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    if ( function_exists( 'WC' ) ) {
                        foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
                            $updated_settings[] = array(
                                'name'     => __( $gateway->title . ' Reward Type' , SRP_LOCALE ) ,
                                'desc'     => __( 'Please Select Reward Type for ' . $gateway->title , SRP_LOCALE ) ,
                                'id'       => 'rs_reward_type_for_payment_gateways_' . $gateway->id ,
                                'std'      => '' ,
                                'default'  => '' ,
                                'type'     => 'select' ,
                                'newids'   => 'rs_reward_type_for_payment_gateways_' . $gateway->id ,
                                'desc_tip' => true ,
                                'options'  => array(
                                    '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                                    '2' => __( 'By Percentage of Cart Total' , SRP_LOCALE ) ,
                                ) ,
                                    ) ;
                            $updated_settings[] = array(
                                'name'              => __( $gateway->title . ' Reward Points' , SRP_LOCALE ) ,
                                'desc'              => __( 'Please Enter Reward Points for ' . $gateway->title , SRP_LOCALE ) ,
                                'id'                => 'rs_reward_payment_gateways_' . $gateway->id ,
                                'std'               => '' ,
                                'default'           => '' ,
                                'type'              => 'number' ,
                                'custom_attributes' => array(
                                    'min' => '0'
                                ) ,
                                'newids'            => 'rs_reward_payment_gateways_' . $gateway->id ,
                                'desc_tip'          => true ,
                                    ) ;
                            $updated_settings[] = array(
                                'name'              => __( $gateway->title . ' Reward Points in Percent %' , SRP_LOCALE ) ,
                                'desc'              => __( 'Please Enter Reward Points for ' . $gateway->title . ' in Percent %' , SRP_LOCALE ) ,
                                'id'                => 'rs_reward_points_for_payment_gateways_in_percent_' . $gateway->id ,
                                'std'               => '' ,
                                'default'           => '' ,
                                'type'              => 'number' ,
                                'custom_attributes' => array(
                                    'min' => '0'
                                ) ,
                                'newids'            => 'rs_reward_points_for_payment_gateways_in_percent_' . $gateway->id ,
                                'desc_tip'          => true ,
                                    ) ;
                        }
                        $updated_settings[] = array(
                            'name'    => __( 'Enable To Send Mail For Payment Gateway Reward Points' , SRP_LOCALE ) ,
                            'desc'    => __( 'Enabling this option will send Payment Gateway Points through Mail' , SRP_LOCALE ) ,
                            'id'      => 'rs_send_mail_payment_gateway' ,
                            'type'    => 'checkbox' ,
                            'std'     => 'no' ,
                            'default' => 'no' ,
                            'newids'  => 'rs_send_mail_payment_gateway' ,
                                ) ;
                        $updated_settings[] = array(
                            'name'    => __( 'Email Subject For Payment Gateway Points' , SRP_LOCALE ) ,
                            'id'      => 'rs_email_subject_payment_gateway' ,
                            'std'     => 'Payment Gateway - Notification' ,
                            'default' => 'Payment Gateway - Notification' ,
                            'type'    => 'textarea' ,
                            'newids'  => 'rs_email_subject_payment_gateway' ,
                                ) ;
                        $updated_settings[] = array(
                            'name'    => __( 'Email Message For Payment Gateway Points' , SRP_LOCALE ) ,
                            'id'      => 'rs_email_message_payment_gateway' ,
                            'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                            'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                            'type'    => 'textarea' ,
                            'newids'  => 'rs_email_message_payment_gateway' ,
                                ) ;
                    } else {
                        if ( class_exists( 'WC_Payment_Gateways' ) ) {
                            $paymentgateway = new WC_Payment_Gateways() ;
                            foreach ( $paymentgateway->payment_gateways()as $gateway ) {
                                $updated_settings[] = array(
                                    'name'     => __( $gateway->title . ' Reward Type' , SRP_LOCALE ) ,
                                    'desc'     => __( 'Please Select Reward Type for ' . $gateway->title , SRP_LOCALE ) ,
                                    'id'       => 'rs_reward_type_for_payment_gateways_' . $gateway->id ,
                                    'std'      => '' ,
                                    'default'  => '' ,
                                    'type'     => 'select' ,
                                    'newids'   => 'rs_reward_type_for_payment_gateways_' . $gateway->id ,
                                    'desc_tip' => true ,
                                    'options'  => array(
                                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                                        '2' => __( 'By Percentage of Cart Total' , SRP_LOCALE ) ,
                                    ) ,
                                        ) ;
                                $updated_settings[] = array(
                                    'name'              => __( $gateway->title . ' Reward Points' , SRP_LOCALE ) ,
                                    'desc'              => __( 'Please Enter Reward Points for ' . $gateway->title , SRP_LOCALE ) ,
                                    'id'                => 'rs_reward_payment_gateways_' . $gateway->id ,
                                    'std'               => '' ,
                                    'default'           => '' ,
                                    'type'              => 'number' ,
                                    'custom_attributes' => array(
                                        'min' => '0'
                                    ) ,
                                    'newids'            => 'rs_reward_payment_gateways_' . $gateway->id ,
                                    'desc_tip'          => true ,
                                        ) ;
                                $updated_settings[] = array(
                                    'name'              => __( $gateway->title . ' Reward Points in Percent %' , SRP_LOCALE ) ,
                                    'desc'              => __( 'Please Enter Reward Points for ' . $gateway->title . ' in Percent %' , SRP_LOCALE ) ,
                                    'id'                => 'rs_reward_points_for_payment_gateways_in_percent_' . $gateway->id ,
                                    'std'               => '' ,
                                    'default'           => '' ,
                                    'type'              => 'number' ,
                                    'custom_attributes' => array(
                                        'min' => '0'
                                    ) ,
                                    'newids'            => 'rs_reward_points_for_payment_gateways_in_percent_' . $gateway->id ,
                                    'desc_tip'          => true ,
                                        ) ;
                            }
                            $updated_settings[] = array(
                                'name'    => __( 'Enable To Send Mail For Payment Gateway Reward Points' , SRP_LOCALE ) ,
                                'desc'    => __( 'Enabling this option will send Payment Gateway Points through Mail' , SRP_LOCALE ) ,
                                'id'      => 'rs_send_mail_payment_gateway' ,
                                'type'    => 'checkbox' ,
                                'std'     => 'no' ,
                                'default' => 'no' ,
                                'newids'  => 'rs_send_mail_payment_gateway' ,
                                    ) ;
                            $updated_settings[] = array(
                                'name'    => __( 'Email Subject For Payment Gateway Points' , SRP_LOCALE ) ,
                                'id'      => 'rs_email_subject_payment_gateway' ,
                                'std'     => 'Payment Gateway - Notification' ,
                                'default' => 'Payment Gateway - Notification' ,
                                'type'    => 'textarea' ,
                                'newids'  => 'rs_email_subject_payment_gateway' ,
                                    ) ;
                            $updated_settings[] = array(
                                'name'    => __( 'Email Message For Payment Gateway Points' , SRP_LOCALE ) ,
                                'id'      => 'rs_email_message_payment_gateway' ,
                                'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                                'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                                'type'    => 'textarea' ,
                                'newids'  => 'rs_email_message_payment_gateway' ,
                                    ) ;
                        }
                    }
                    $updated_settings[] = array(
                        'type' => 'sectionend' , 'id'   => '_rs_reward_system_payment_gateway' ,
                            ) ;
                }
                $newsettings        = array( 'type' => 'sectionend' , 'id' => '_rs_reward_system_pg_end' ) ;
                $updated_settings[] = $section ;
            }
            return $updated_settings ;
        }

        public static function reset_action_tab() {
            $settings = self::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

        public static function reward_add_coupon_usage_points_to_action() {
            wp_nonce_field( plugin_basename( __FILE__ ) , 'rsdynamicrulecreation_coupon_usage' ) ;
            ?>
            <style type="text/css">
                .coupon_code_points_selected{
                    width: 60%!important;
                }
                .coupon_code_points{
                    width: 60%!important;
                }
                .chosen-container-multi {
                    position:absolute!important;
                }
            </style>
            <table class="widefat fixed rsdynamicrulecreation_coupon_usage" cellspacing="0">
                <thead>
                    <tr>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Coupon Codes' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Reward Points' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Rule' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </thead>
                <tbody id="here">
                    <?php
                    $CouponRules = get_option( 'rewards_dynamic_rule_couponpoints' ) ;
                    if ( srp_check_is_array( $CouponRules ) ) {
                        $i = 0 ;
                        foreach ( $CouponRules as $Rule ) {
                            ?>
                            <tr>
                                <td class="column-columnname">
                                    <select multiple="multiple" name="rewards_dynamic_rule_coupon_usage[<?php echo $i ; ?>][coupon_codes][]" class="short coupon_code_points_selected">
                                        <?php
                                        if ( isset( $Rule[ "coupon_codes" ] ) && $Rule[ "coupon_codes" ] != "" ) {
                                            $coupons_list = $Rule[ "coupon_codes" ] ;
                                            $coupons      = get_posts(
                                                    array(
                                                        'post_type'   => 'shop_coupon' ,
                                                        'numberposts' => '-1' ,
                                                        's'           => '-sumo_' ,
                                                        'post_status' => 'publish' )
                                                    ) ;
                                            foreach ( $coupons as $coupon ) {
                                                ?>
                                                <option value="<?php echo strtolower( $coupon->post_title ) ; ?>" <?php echo in_array( strtolower( $coupon->post_title ) , $coupons_list ) ? "selected='selected'" : '' ; ?>><?php echo $coupon->post_title ; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td class="column-columnname">
                                    <input type="text" name="rewards_dynamic_rule_coupon_usage[<?php echo $i ; ?>][reward_points]" value="<?php echo $Rule[ "reward_points" ] ; ?>" />
                                </td>
                                <td class="column-columnname num">
                                    <span class="remove button-secondary"><?php _e( 'Remove Rule' , SRP_LOCALE ) ; ?></span>
                                </td>
                            </tr>
                            <?php
                            $i = $i + 1 ;
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="manage-column column-columnname num" scope="col"> <span class="add button-primary"><?php _e( 'Add Rule' , SRP_LOCALE ) ; ?></span></td>
                    </tr>
                    <tr>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Coupon Codes' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Reward Points' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Add Rule' , SRP_LOCALE ) ; ?></th>

                    </tr>
                </tfoot>
            </table>
            <?php
        }

        public static function rs_fpwcrs_rule_for_custom_reg_fields() {
            ?>
            <table class="widefat fixed rs_rule_for_custom_reg_field" cellspacing="0">
                <thead>
                    <tr class="rs_rule_creation_for_custom_reg_field">
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Custom Field Name' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Field Type' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Reward Points' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Repeat Rule' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Award Points for Filling' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Rule' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="rs_rule_creation_for_custom_reg_field">
                        <td class="column-columnname"></td>
                        <td class="column-columnname"></td>
                        <td class="column-columnname"></td>
                        <td class="column-columnname"></td>
                        <td class="column-columnname"></td>
                        <td class="manage-column column-columnname num" scope="col">
                            <span class="rs_add_rule_for_custom_reg_field button-primary"><?php _e( 'Add Rule' , SRP_LOCALE ) ; ?></span>
                        </td>
                    </tr>
                </tfoot>
                <tbody id="rs_append_rule_for_custom_reg_field">
                    <?php
                    $rules_for_custom_reg_field = get_option( 'rs_rule_for_custom_reg_field' ) ;
                    if ( srp_check_is_array( $rules_for_custom_reg_field ) ) {
                        foreach ( $rules_for_custom_reg_field as $key => $individual_rule ) {
                            $repeat       = isset( $individual_rule[ 'repeat_points' ] ) ? $individual_rule[ 'repeat_points' ] : 'no' ;
                            $allow_points = isset( $individual_rule[ 'award_points_for_filling' ] ) ? $individual_rule[ 'award_points_for_filling' ] : 'no' ;
                            ?>
                            <tr class="rs_rule_creation_for_custom_reg_field">
                        <input type="hidden" id="rs_rule_id_for_custom_reg_field" value="<?php echo $key ; ?>" />
                        <td class="column-columnname">
                            <?php
                            $args         = array(
                                'class'              => 'wc-product-search' ,
                                'id'                 => 'rs_rule_for_custom_reg_field[' . $key . '][custom_fields]' ,
                                'name'               => 'rs_rule_for_custom_reg_field[' . $key . '][custom_fields]' ,
                                'type'               => 'customfields' ,
                                'action'             => 'cus_field_search' ,
                                'multiple'           => false ,
                                'css'                => 'width: 100%;' ,
                                'placeholder'        => 'Select Custom Fields' ,
                                'options'            => ( array ) $individual_rule[ 'custom_fields' ] ,
                                'translation_string' => SRP_LOCALE
                                    ) ;
                            rs_custom_search_fields( $args ) ;
                            ?>
                        </td>
                        <td class="column-columnname">
                            <p class="rs_label_for_cus_field_type"><?php echo $individual_rule[ 'field_type' ] ; ?></p>
                            <input type="hidden" class="rs_label_for_cus_field_type_hidden" name="rs_rule_for_custom_reg_field[<?php echo $key ; ?>][field_type]" value="<?php echo $individual_rule[ 'field_type' ] ; ?>"/>
                        </td>
                        <td class="column-columnname">
                            <input type="number" style="width:75% !important;" name="rs_rule_for_custom_reg_field[<?php echo $key ; ?>][reward_points]" min="0" value="<?php echo $individual_rule[ 'reward_points' ] ; ?>"/>
                        </td>
                        <td class="column-columnname">
                            <p class="rs_label_for_datepicker_type">
                                <?php if ( $individual_rule[ 'field_type' ] == 'DATEPICKER' ) { ?>
                                    <select style="width:50% !important;" name="rs_rule_for_custom_reg_field[<?php echo $key ; ?>][repeat_points]">
                                        <option value="no" <?php if ( $repeat == 'no' ) { ?>selected="selected"<?php } ?>><?php _e( 'No' , SRP_LOCALE ) ; ?></option>
                                        <option value="yes" <?php if ( $repeat == 'yes' ) { ?>selected="selected"<?php } ?>><?php _e( 'Yes' , SRP_LOCALE ) ; ?></option>
                                    </select>
                                    <?php
                                } else {
                                    echo 'N/A' ;
                                }
                                ?>
                            </p>
                        </td>
                        <td class="column-columnname">
                            <p class="rs_label_award_points_for_filling_datepicker">
                                <?php if ( $individual_rule[ 'field_type' ] == 'DATEPICKER' ) { ?>
                                    <input type="checkbox" name="rs_rule_for_custom_reg_field[<?php echo $key ; ?>][award_points_for_filling]" <?php if ( $allow_points == 'yes' ) { ?>checked="checked"<?php } ?>/>
                                    <?php
                                } else {
                                    echo 'N/A' ;
                                }
                                ?>
                            </p>
                        </td>
                        <td class="column-columnname">
                            <span class="rs_remove_rule_for_custom_reg_field button-primary"><?php _e( 'Remove Rule' , SRP_LOCALE ) ; ?></span>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
            </table>
            <?php
        }

    }

    RSRewardPointsForAction::init() ;
}
