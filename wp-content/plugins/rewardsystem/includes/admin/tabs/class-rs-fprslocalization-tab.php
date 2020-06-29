<?php

/*
 * Localization Setting Tab
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSLocalization' ) ) {

    class RSLocalization {

        public static function init() {
            add_action( 'woocommerce_rs_settings_tabs_fprslocalization' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'rs_display_save_button_fprslocalization' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fprslocalization' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;

            add_action( 'woocommerce_update_options_fprslocalization' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system

            if ( class_exists( 'bbPress' ) )
                add_filter( 'woocommerce_fprslocalization_settings' , array( __CLASS__ , 'add_message_for_create_topic' ) ) ;

            if ( class_exists( 'BuddyPress' ) )
                add_filter( 'woocommerce_fprslocalization_settings' , array( __CLASS__ , 'add_message_for_create_post' ) ) ;

            if ( class_exists( 'FPWaitList' ) )
                add_filter( 'woocommerce_fprslocalization_settings' , array( __CLASS__ , 'add_message_for_waitlist' ) ) ;

            if ( class_exists( 'FPWCRS' ) )
                add_filter( 'woocommerce_fprslocalization_settings' , array( __CLASS__ , 'add_message_for_fpwcrs' ) ) ;

            if ( class_exists( 'FS_Affiliates' ) )
                add_filter( 'woocommerce_fprslocalization_settings' , array( __CLASS__ , 'add_message_for_affs' ) ) ;

            add_action( 'rs_default_settings_fprslocalization' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'fp_action_to_reset_settings_fprslocalization' , array( __CLASS__ , 'reset_localization_tab' ) ) ;
        }

        public static function add_message_for_waitlist( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && '_rs_reward_points_log_for_waitlist' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'    => __( 'Reward Points for Subscribing Out of Stock Products Log' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_reward_points_for_waitlist_subscribing' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points earned for subscribing the Product {rs_waitlist_product_name}' ,
                        'default' => 'Points earned for subscribing the Product {rs_waitlist_product_name}' ,
                        'newids'  => '_rs_localize_reward_points_for_waitlist_subscribing' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Reward Points for purchasing In-Stock Products Log' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_reward_points_for_waitlist_sale_conversion' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points earned for purchasing the In-Stock Product {rs_waitlist_product_name}' ,
                        'default' => 'Points earned for purchasing the In-Stock Product {rs_waitlist_product_name}' ,
                        'newids'  => '_rs_localize_reward_points_for_waitlist_sale_conversion' ,
                            ) ;
                }
                $updated_settings[] = $section ;
            }
            return $updated_settings ;
        }

        public static function add_message_for_fpwcrs( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && '_rs_reward_points_log_for_login_settings' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'    => __( 'Daily Social Login Reward Points Log' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_reward_points_for_social_login' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points Earned for today\'s login through [network_name] Social Account' ,
                        'default' => 'Points Earned for today\'s login through [network_name] Social Account' ,
                        'newids'  => '_rs_localize_reward_points_for_social_login' ,
                            ) ;
                }
                if ( isset( $section[ 'id' ] ) && '_rs_log_registration_reward_points' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'    => __( 'Social Registration Reward Points Log' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_points_earned_for_social_registration' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points Earned for Registering through [network_name]' ,
                        'default' => 'Points Earned for Registering through [network_name]' ,
                        'newids'  => '_rs_localize_points_earned_for_social_registration' ,
                            ) ;
                }
                if ( isset( $section[ 'id' ] ) && '_rs_social_linking_title' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'    => __( 'Social Account Linking Reward Points Log' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_reward_points_for_social_linking' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points Earned for linking [network_name] Account on Account Details Menu' ,
                        'default' => 'Points Earned for linking [network_name] Account on Account Details Menu' ,
                        'newids'  => '_rs_localize_reward_points_for_social_linking' ,
                            ) ;
                }
                if ( isset( $section[ 'id' ] ) && '_rs_cus_reg_field_title' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'    => __( 'Custom Registration Fields Log[During registration]' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_reward_points_for_cus_reg_field' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points earned for filling [field_name] while registering on the site' ,
                        'default' => 'Points earned for filling [field_name] while registering on the site' ,
                        'newids'  => '_rs_localize_reward_points_for_cus_reg_field' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Custom Registration Fields Log[Reaching the corresponding date]' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_reward_points_for_datepicker_cus_reg_field' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points earned for reaching the [field_name] date' ,
                        'default' => 'Points earned for reaching the [field_name] date' ,
                        'newids'  => '_rs_localize_reward_points_for_datepicker_cus_reg_field' ,
                            ) ;
                }
                $updated_settings[] = $section ;
            }
            return $updated_settings ;
        }

        public static function add_message_for_affs( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && 'rs_message_for_affiliates_pro' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'    => __( 'SUMO Affiliates Pro Log Settings' , SRP_LOCALE ) ,
                        'id'      => 'rs_reward_log_for_affiliate' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points earned for Payout' ,
                        'default' => 'Points earned for Payout' ,
                        'newids'  => 'rs_reward_log_for_affiliate' ,
                            ) ;
                }
                $updated_settings[] = $section ;
            }
            return $updated_settings ;
        }

        public static function add_message_for_create_topic( $settings ) {
            $updated_settings = array() ;

            foreach ( $settings as $section ) {
                $updated_settings[] = $section ;
                if ( isset( $section[ 'id' ] ) && '_rs_referral_log_localization_settings' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name' => __( 'Reward Points Log Create or Replied Topic' , SRP_LOCALE ) ,
                        'type' => 'title' ,
                        'id'   => '_rs_reward_points_log_for_topic' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Create Topic Reward Points Log' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_reward_points_for_create_topic' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points Earned for Create Topic' ,
                        'default' => 'Points Earned for Create Topic' ,
                        'newids'  => '_rs_localize_reward_points_for_create_topic' ,
                            ) ;

                    $updated_settings[] = array(
                        'name'   => __( 'Replied Topic Reward Points Log' , SRP_LOCALE ) ,
                        'id'     => '_rs_localize_reward_points_for_replied_topic' ,
                        'type'   => 'textarea' ,
                        'std'    => 'Points Earned for Replied Topic' ,
                        'newids' => '_rs_localize_reward_points_for_replied_topic' ,
                            ) ;
                    $updated_settings[] = array(
                        'type' => 'sectionend' ,
                        'id'   => '_rs_reward_points_log_for_topic'
                            ) ;
                }
            }
            return $updated_settings ;
        }

        public static function add_message_for_create_post( $settings ) {
            $updated_settings = array() ;

            foreach ( $settings as $section ) {
                $updated_settings[] = $section ;
                if ( isset( $section[ 'id' ] ) && '_rs_referral_log_localization_settings' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name' => __( 'BuddyPress Log Settings' , SRP_LOCALE ) ,
                        'type' => 'title' ,
                        'id'   => '_rs_reward_points_log_for_post' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Post Creation Reward Points Log' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_reward_points_for_create_post' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points Earned for Creating the Post' ,
                        'default' => 'Points Earned for Creating the Post' ,
                        'newids'  => '_rs_localize_reward_points_for_create_post' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Group Creation Reward Points Log' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_reward_points_for_create_group' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points Earned for Creating the Group' ,
                        'default' => 'Points Earned for Creating the Group' ,
                        'newids'  => '_rs_localize_reward_points_for_create_group' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Post Comment Reward Points Log' , SRP_LOCALE ) ,
                        'id'      => '_rs_localize_reward_points_for_post_comment' ,
                        'type'    => 'textarea' ,
                        'std'     => 'Points Earned for Posting the Comment' ,
                        'default' => 'Points Earned for Posting the Comment' ,
                        'newids'  => '_rs_localize_reward_points_for_post_comment' ,
                            ) ;
                    $updated_settings[] = array(
                        'type' => 'sectionend' ,
                        'id'   => '_rs_reward_points_log_for_topic'
                            ) ;
                }
            }
            return $updated_settings ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;

            return apply_filters( 'woocommerce_fprslocalization_settings' , array(
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Registration Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_log_registration_reward_points' ,
                ) ,
                array(
                    'name'    => __( 'Registration Reward Points Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_earned_for_registration' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Earned for Registration' , SRP_LOCALE ) ,
                    'default' => __( 'Points Earned for Registration' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_earned_for_registration' ,
                ) ,
                array(
                    'name'    => __( 'Referral Registration Reward Points Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_earned_for_referral_registration' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Earned for Referral Registration by {registereduser}' , SRP_LOCALE ) ,
                    'default' => __( 'Points Earned for Referral Registration by {registereduser}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_earned_for_referral_registration' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_log_registration_reward_points' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'First Purchase Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_log_for_first_purchase_points_title' ,
                ) ,
                array(
                    'name'    => __( 'First Purchase Points Log' , SRP_LOCALE ) ,
                    'id'      => 'rs_log_for_first_purchase_points' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Earned for First Purchase' , SRP_LOCALE ) ,
                    'default' => __( 'Points Earned for First Purchase' , SRP_LOCALE ) ,
                    'newids'  => 'rs_log_for_first_purchase_points' ,
                ) ,
                array(
                    'name'    => __( 'Revised First Purchase Points Log' , SRP_LOCALE ) ,
                    'id'      => 'rs_log_for_revised_first_purchase_points' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Points for First Purchase {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Points for First Purchase {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => 'rs_log_for_revised_first_purchase_points' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_log_for_first_purchase_points_title' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Product Purchase Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_product_purchase_log_localization_settings' ,
                ) ,
                array(
                    'type' => 'title' ,
                    'id'   => '_rs_product_total_log_localization_settings' ,
                    'desc' => '<h3>Product Total based Reward Points Log</h3>' ,
                ) ,
                array(
                    'name'    => __( 'Product Purchase Log displayed in MasterLog - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_product_purchase_reward_points' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Earned for Purchasing the Product #{itemproductid} with Order {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Points Earned for Purchasing the Product #{itemproductid} with Order {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_product_purchase_reward_points' ,
                ) ,
                array(
                    'name'    => __( 'Product Purchase Log displayed in My Reward Table - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_earned_for_purchase_main' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Earned for Purchasing the Product of Order {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Points Earned for Purchasing the Product of Order {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_earned_for_purchase_main' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_product_total_log_localization_settings' ) ,
                array(
                    'type' => 'title' ,
                    'id'   => '_rs_cart_total_log_localization_settings' ,
                    'desc' => '<h3>Cart Total based Reward Points Log</h3>' ,
                ) ,
                array(
                    'name'    => __( 'Cart Total based Reward Points Log displayed in Master Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_earned_for_purchase_based_on_cart_total_for_master_log' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Earned for this Order {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Points Earned for this Order {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_earned_for_purchase_based_on_cart_total_for_master_log' ,
                ) ,
                array(
                    'name'    => __( 'Cart Total based Reward Points Log displayed in My Reward Table – Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_earned_for_purchase_based_on_cart_total' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Earned for this Order {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Points Earned for this Order {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_earned_for_purchase_based_on_cart_total' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_cart_total_log_localization_settings' ) ,
                array(
                    'type' => 'title' ,
                    'id'   => '_rs_overrided_log_localization_settings' ,
                    'desc' => '<h3>Overrided Product Purchase Log</h3>' ,
                ) ,
                array(
                    'name'    => __( 'Product Purchase Log displayed when Points are Overidded' , SRP_LOCALE ) ,
                    'id'      => 'rs_log_for_product_purchase_when_overidded' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points earned for purchasing the product of order {currentorderid} has modified and overridden the existing points' , SRP_LOCALE ) ,
                    'default' => __( 'Points earned for purchasing the product of order {currentorderid} has modified and overridden the existing points' , SRP_LOCALE ) ,
                    'newids'  => 'rs_log_for_product_purchase_when_overidded' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_overrided_log_localization_settings' ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_product_purchase_log_localization_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_referral_log_localization_settings' ,
                ) ,
                array(
                    'name'    => __( 'Referral Product Purchase Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_referral_reward_points_for_purchase' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Referral Reward Points earned for Purchase {itemproductid} by {purchasedusername}' , SRP_LOCALE ) ,
                    'default' => __( 'Referral Reward Points earned for Purchase {itemproductid} by {purchasedusername}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_referral_reward_points_for_purchase' ,
                ) ,
                array(
                    'name'    => __( 'Getting Referred Log for Product Purchase - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_referral_reward_points_for_purchase_gettin_referred' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Getting Referred Reward Points earned for Purchase {itemproductid}' , SRP_LOCALE ) ,
                    'default' => __( 'Getting Referred Reward Points earned for Purchase {itemproductid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_referral_reward_points_for_purchase_gettin_referred' ,
                ) ,
                array(
                    'name'    => __( 'Getting Referred Log for Registration' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_referral_reward_points_gettin_referred' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points for Getting Referred' , SRP_LOCALE ) ,
                    'default' => __( 'Points for Getting Referred' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_referral_reward_points_gettin_referred' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_referral_log_localization_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points Redeemed Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_product_redeeming_settings' ,
                ) ,
                array(
                    'name'    => __( 'Points Redeemed Log - Deducted from Account' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_redeemed_towards_purchase' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Redeemed Towards Purchase for Order {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Points Redeemed Towards Purchase for Order {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_redeemed_towards_purchase' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_product_redeeming_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points for Payment Gateway Usage Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_payment_gateway_reward_points' ,
                ) ,
                array(
                    'name'    => __( 'Payment Gateway Reward Points Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_payment_gateway_message' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward Points for Using Payment Gateway {payment_title}' , SRP_LOCALE ) ,
                    'default' => __( 'Reward Points for Using Payment Gateway {payment_title}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_payment_gateway_message' ,
                ) ,
                array(
                    'name'    => __( 'Payment Gateway Reward Points Log - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_revise_reward_for_payment_gateway_message' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Reward Points for Using Payment Gateway {payment_title}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Reward Points for Using Payment Gateway {payment_title}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_revise_reward_for_payment_gateway_message' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_payment_gateway_reward_points' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'SUMO Reward Points Payment Gateway Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_points_gateway_localization' ,
                ) ,
                array(
                    'name'    => __( 'SUMO Reward Points Payment Gateway Redeemed Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_reward_points_gateway_log_localizaation' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Redeemed for using Reward Points Gateway {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Points Redeemed for using Reward Points Gateway {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_reward_points_gateway_log_localizaation' ,
                ) ,
                array(
                    'name'    => __( 'Subscription Product Auto Renewal Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_using_subscription' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Redeemed For Renewal Of Subscription {subscription_id}' , SRP_LOCALE ) ,
                    'default' => __( 'Points Redeemed For Renewal Of Subscription {subscription_id}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_using_subscription' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_points_gateway_localization' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Social Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_localize_social_reward_points' ,
                ) ,
                array(
                    'name'    => __( 'Facebook Like Reward Points Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_facebook_like' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social Facebook Like' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social Facebook Like' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_facebook_like' ,
                ) ,
                array(
                    'name'    => __( 'Facebook Share Reward Points Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_facebook_share' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social Facebook Share' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social Facebook Share' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_facebook_share' ,
                ) ,
                array(
                    'name'    => __( 'Twitter Tweet Reward Points Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_twitter_tweet' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social Twitter Tweet' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social Twitter Tweet' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_twitter_tweet' ,
                ) ,
                array(
                    'name'    => __( 'Twitter Follow Reward Points Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_twitter_follow' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social Twitter Follow' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social Twitter Follow' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_twitter_follow' ,
                ) ,
                array(
                    'name'    => __( 'Google Plus Reward Points Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_google_plus' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social Google Plus' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social Google Plus' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_google_plus' ,
                ) ,
                array(
                    'name'    => __( 'VK.Com Like Reward Points Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_vk' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social VK.Com Like' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social VK.Com Like' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_vk' ,
                ) ,
                array(
                    'name'    => __( 'Instagram Follow Reward Points Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_instagram' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social Instagram Follow' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social Instagram Follow' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_instagram' ,
                ) ,
                array(
                    'name'    => __( 'OK.ru Share Reward Points Log - Earned' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_ok_follow' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social OK.ru Share' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social OK.ru Share' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_ok_follow' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_localize_social_reward_points' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Product Review Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_review_localize_settings' ,
                ) ,
                array(
                    'name'    => __( 'Product Review Reward Points Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_earned_for_product_review' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Reviewing a Product {reviewproductid}' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Reviewing a Product {reviewproductid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_earned_for_product_review' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_review_localize_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Blog Post Creation Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_blogposts_localize_settings' ,
                ) ,
                array(
                    'name'    => __( 'Blog Post Creation Reward Points Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_earned_for_post' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Posting {postid}' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Posting {postid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_earned_for_post' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_blogposts_localize_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Blog Post Comment Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_post_review_localize_settings' ,
                ) ,
                array(
                    'name'    => __( 'Blog Post Comment Reward Points Log Settings' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_earned_for_post_review' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Commenting a Post {postid}' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Commenting a Post {postid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_earned_for_post_review' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_post_review_localize_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Product Creation Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_product_localize_settings' ,
                ) ,
                array(
                    'name'    => __( 'Product Creation Reward Points Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_earned_for_product_creation' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward Points for Creating a Product {ProductName}' , SRP_LOCALE ) ,
                    'default' => __( 'Reward Points for Creating a Product {ProductName}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_earned_for_product_creation' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_product_localize_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Page Comment Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_page_review_localize_settings' ,
                ) ,
                array(
                    'name'    => __( 'Page Comment Reward Points Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_earned_for_page_review' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Commenting a Page {pagename}' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Commenting a Page {pagename}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_earned_for_page_review' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_page_review_localize_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Daily Login Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_points_log_for_login_settings' ,
                ) ,
                array(
                    'name'    => __( 'Daily Login Reward Points Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_points_for_login' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Earned for today login' , SRP_LOCALE ) ,
                    'default' => __( 'Points Earned for today login' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_points_for_login' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_points_log_for_login_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_bsn_compatible_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points for Subscribing Out of Stock/In-Stock Products Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_points_log_for_waitlist' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_points_log_for_waitlist' ) ,
                array(
                    'type' => 'rs_bsn_compatible_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Buying Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_buying_reward_points_localization' ,
                ) ,
                array(
                    'name'    => __( 'Buying Reward Points Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_buying_reward_points_log' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Bought Reward Points  {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Bought Reward Points  {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_buying_reward_points_log' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_buying_reward_points_localization' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_fpwcrs_compatible_start' ,
                ) ,
                array(
                    'name' => __( 'Social Account Linking Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_social_linking_title' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_social_linking_title' ) ,
                array(
                    'type' => 'rs_fpwcrs_compatible_end' ,
                ) ,
                array(
                    'type' => 'rs_fpwcrs_compatible_start' ,
                ) ,
                array(
                    'name' => __( 'Custom Registration Field Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_cus_reg_field_title' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_cus_reg_field_title' ) ,
                array(
                    'type' => 'rs_fpwcrs_compatible_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Product Purchase Reward Points Revised Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_revise_purchase_log_settings' ,
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<h3>Product Total based Reward Points Log</h3><br><br>' ,
                    'id'   => '_rs_product_total_based_log_title' ,
                ) ,
                array(
                    'name'    => __( 'Product Purchase Log displayed in MasterLog - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_log_revise_product_purchase' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Product Purchase {productid}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Product Purchase {productid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_log_revise_product_purchase' ,
                ) ,
                array(
                    'name'    => __( 'Product Purchase Log displayed in My Reward Table - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_log_revise_product_purchase_main' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Product Purchase {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Product Purchase {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_log_revise_product_purchase_main' ,
                ) ,
                array(
                    'name'    => __( 'Buy Points Log displayed in My Reward Table - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_log_revise_buy_points_main' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Buying Reward Points {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Buying Reward Points {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_log_revise_buy_points_main' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_product_total_based_log_title' ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<h3>Cart Total based Reward Points Log</h3><br><br>' ,
                    'id'   => '_rs_cart_total_based_log_title' ,
                ) ,
                array(
                    'name'    => __( 'Cart Total based Reward Points Log displayed in Master Log - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_log_revise_for_product_purchase_based_on_cart_total' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Points for this {orderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Points for this {orderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_log_revise_for_product_purchase_based_on_cart_total' ,
                ) ,
                array(
                    'name'    => __( 'Cart Total based Reward Points Log displayed in My Reward Table - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_log_revise_for_product_purchase_based_on_cart_total_in_my_reward' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Points for this {orderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Points for this {orderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_log_revise_for_product_purchase_based_on_cart_total_in_my_reward' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_cart_total_based_log_title' ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_revise_purchase_log_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Product Purchase Reward Points Revised Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_revise_referral_purchase_log_settings' ,
                ) ,
                array(
                    'name'    => __( 'Referral Product Purchase Log - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_log_revise_referral_product_purchase' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Referral Product Purchase {productid}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Referral Product Purchase {productid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_log_revise_referral_product_purchase' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_revise_referral_purchase_log_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Redeemed Reward Points Revised Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_revise_product_redeeming_settings' ,
                ) ,
                array(
                    'name'    => __( 'Points Redeemed Log - Added to Account' , SRP_LOCALE ) ,
                    'id'      => '_rs_log_revise_points_redeemed_towards_purchase' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revise Points Redeemed Towards Purchase {currentorderid}' , SRP_LOCALE ) ,
                    'default' => __( 'Revise Points Redeemed Towards Purchase {currentorderid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_log_revise_points_redeemed_towards_purchase' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_revise_product_redeeming_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Getting Referred Reward Points for Product Purchase Revised Log settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_revise_getting_referred_log_settings' ,
                ) ,
                array(
                    'name'    => __( 'Getting Referred Log for Product Purchase - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_log_revise_getting_referred_product_purchase' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Getting Referred Product Purchase {productid}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Getting Referred Product Purchase {productid}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_log_revise_getting_referred_product_purchase' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_revise_getting_referred_log_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Registration Points Revised upon Account Deletion Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_localize_revise_points_for_deleted_user' ,
                ) ,
                array(
                    'name'    => __( 'Referral Account Sign up Log - Revoked on User Deletion' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_referral_account_signup_points_revised' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Referral Account Sign up Points Revised with Referred User Deleted {usernickname}' , SRP_LOCALE ) ,
                    'default' => __( 'Referral Account Sign up Points Revised with Referred User Deleted {usernickname}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_referral_account_signup_points_revised' ,
                ) ,
                array(
                    'name'    => __( 'Referral Product Purchase Log - Revoked on User Deletion' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_revise_points_for_referral_purchase' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Referral Reward Points earned for Purchase {productid} by deleted user {usernickname}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Referral Reward Points earned for Purchase {productid} by deleted user {usernickname}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_revise_points_for_referral_purchase' ,
                ) ,
                array(
                    'name'    => __( 'Getting Referred Log for Product Purchase - Revoked on User Deletion' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_revise_points_for_getting_referred_purchase' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Revised Getting Referred Reward Points earned for Purchase {productid} by deleted user {usernickname}' , SRP_LOCALE ) ,
                    'default' => __( 'Revised Getting Referred Reward Points earned for Purchase {productid} by deleted user {usernickname}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_revise_points_for_getting_referred_purchase' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_localize_revise_points_for_deleted_user' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Social Reward Points Revised Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_localize_social_redeeming' ,
                ) ,
                array(
                    'name'    => __( 'Facebook Like Reward Points Log - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_facebook_like_revised' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social Facebook Like is Revised' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social Facebook Like is Revised' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_facebook_like_revised' ,
                ) ,
                array(
                    'name'    => __( 'Google Plus Reward Points Log - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_google_plus_revised' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social Google Plus is Revised' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social Google Plus is Revised' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_google_plus_revised' ,
                ) ,
                array(
                    'name'    => __( 'VK.Com Like Reward Points Log - Revoked' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_reward_for_vk_like_revised' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Reward for Social VK.Com Like is Revised' , SRP_LOCALE ) ,
                    'default' => __( 'Reward for Social VK.Com Like is Revised' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_reward_for_vk_like_revised' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_localize_social_redeeming' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Send Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_log_for_sendpoints' ,
                ) ,
                array(
                    'name'    => __( 'Points Received through Send Points Log - Receiver' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_log_for_reciver' ,
                    'type'    => 'textarea' ,
                    'std'     => __( '[name] Received [points] Points from [user]' , SRP_LOCALE ) ,
                    'default' => __( '[name] Received [points] Points from [user]' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_log_for_reciver' ,
                ) ,
                array(
                    'name'    => __( 'Send Points Request Approved Log - Sender' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_log_for_sender' ,
                    'type'    => 'textarea' ,
                    'std'     => __( '[name] [points] Points has been Approved by Admin Successfully and Sent to [user]' , SRP_LOCALE ) ,
                    'default' => __( '[name] [points] Points has been Approved by Admin Successfully and Sent to [user]' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_log_for_sender' ,
                ) ,
                array(
                    'name'    => __( 'Send Points Request Submitted Log - Sender' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_log_for_sender_after_submit' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Your request to Send Points is Submitted Successfully and waiting for Admin Approval.' , SRP_LOCALE ) ,
                    'default' => __( 'Your request to Send Points is Submitted Successfully and waiting for Admin Approval.' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_log_for_sender_after_submit' ,
                ) ,
                array(
                    'name'    => __( 'Send Points Request Rejected Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_to_send_log_revised' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Admin has been Rejected Your Request to Send Points.So Your Requested Points to Send were revised to your account' , SRP_LOCALE ) ,
                    'default' => __( 'Admin has been Rejected Your Request to Send Points.So Your Requested Points to Send were revised to your account' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_to_send_log_revised' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_log_for_sendpoints' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Voucher Code Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_voucher_code_log_localization' ,
                ) ,
                array(
                    'name'    => __( 'Voucher Code Redeemed Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_voucher_code_usage_log_message' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Redeem Voucher Code {rsusedvouchercode}' , SRP_LOCALE ) ,
                    'default' => __( 'Redeem Voucher Code {rsusedvouchercode}' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_voucher_code_usage_log_message' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_voucher_code_log_localization' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Coupon Reward Points Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_coupon_reward_points_localization' ,
                ) ,
                array(
                    'name'    => __( 'Reward Points for Coupon Usage Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_coupon_reward_points_log' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Earned for using Coupons' , SRP_LOCALE ) ,
                    'default' => __( 'Points Earned for using Coupons' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_coupon_reward_points_log' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_coupon_reward_points_localization' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points Earning Threshold Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_log_for_max_earning' ,
                ) ,
                array(
                    'name'    => __( 'Maximum Threshold for Total Points Log' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_max_earning_points_log' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'You Cannot Earn More than [rsmaxpoints]' , SRP_LOCALE ) ,
                    'default' => __( 'You Cannot Earn More than [rsmaxpoints]' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_max_earning_points_log' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_log_for_max_earning' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Cashback Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_log_for_points_to_cash' ,
                ) ,
                array(
                    'name'    => __( 'Cashback Request Log displayed in My Reward Table - Submitted' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_to_cash_log' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Points Requested For Cashback' , SRP_LOCALE ) ,
                    'default' => __( 'Points Requested For Cashback' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_to_cash_log' ,
                ) ,
                array(
                    'name'    => __( 'Cashback Request Log displayed in My Reward Table - Cancelled' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_to_cash_log_revised' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Admin has been Cancelled your Request For Cashback.So Your Requested Cashback Points were revised to your account' , SRP_LOCALE ) ,
                    'default' => __( 'Admin has been Cancelled your Request For Cashback.So Your Requested Cashback Points were revised to your account' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_to_cash_log_revised' ,
                ) ,
                array(
                    'name'    => __( 'Cashback Request Log displayed in My Cashback Table - Submitted' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_points_to_cash_log_in_my_cashback_table' ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'You have Requested [pointstocashback] points for Cashback ([cashbackamount])' , SRP_LOCALE ) ,
                    'default' => __( 'You have Requested [pointstocashback] points for Cashback ([cashbackamount])' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_points_to_cash_log_in_my_cashback_table' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_log_for_points_to_cash' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Nominee Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_log_for_nominee' ,
                ) ,
                array(
                    'name'    => __( 'Nominated Product Purchase Reward Points Log - Receiver' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_log_for_nominee' ,
                    'type'    => 'textarea' ,
                    'std'     => __( '[name] Received [points] Points from [user]' , SRP_LOCALE ) ,
                    'default' => __( '[name] Received [points] Points from [user]' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_log_for_nominee' ,
                ) ,
                array(
                    'name'    => __( 'Nominated Product Purchase Reward Points Log - Sender' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_log_for_nominated_user' ,
                    'type'    => 'textarea' ,
                    'std'     => __( '[name] [points] Points has been nominated to [user]' , SRP_LOCALE ) ,
                    'default' => __( '[name] [points] Points has been nominated to [user]' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_log_for_nominated_user' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_log_for_nominee' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Import/Export Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_log_for_import_export' ,
                ) ,
                array(
                    'name'    => __( 'Points Imported Log - Added to Existing Points' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_log_for_import_add' ,
                    'type'    => 'textarea' ,
                    'std'     => __( '[points] Points were added with existing points by importing' , SRP_LOCALE ) ,
                    'default' => __( '[points] Points were added with existing points by importing' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_log_for_import_add' ,
                ) ,
                array(
                    'name'    => __( 'Points Imported Log - Override Existing Points' , SRP_LOCALE ) ,
                    'id'      => '_rs_localize_log_for_import_override' ,
                    'type'    => 'textarea' ,
                    'std'     => __( '[points] Points were overrided by importing' , SRP_LOCALE ) ,
                    'default' => __( '[points] Points were overrided by importing' , SRP_LOCALE ) ,
                    'newids'  => '_rs_localize_log_for_import_override' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_log_for_import_export' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_affs_compatible_start' ,
                ) ,
                array(
                    'name' => __( 'SUMO Affiliates Pro Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_message_for_affiliates_pro' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_message_for_affiliates_pro' ) ,
                array(
                    'type' => 'rs_affs_compatible_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Shortcode used in Localization' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_shortcode_for_localization'
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<b>{productname},{ProductName}</b> - To display product name in log<br><br>'
                    . '<b>{itemproductid}, {productid}, {reviewproductid}, {postid}, {rsbuyiedrewardpoints}</b> - To display product id n log<br><br>'
                    . '<b>{purchasedusername}</b> - To display purchased username in log<br><br>'
                    . '<b>{currentorderid}</b> - To display order id in log<br><br>'
                    . '<b>{registereduser}, {usernickname}</b> - To display username in log<br><br>'
                    . '<b>[name]</b> - To display receiver name in send points and nominee log<br><br>'
                    . '<b>[points]</b> - To display points received  in send points and nominee log<br><br>'
                    . '<b>[user]</b> - To display sender name in send points and nominee log<br><br>'
                    . '<b>{pagename}</b> - To display commented page name<br><br>'
                    . '<b>{payment_title}</b> - To display payment gateway name<br><br>'
                    . '<b>{subscription_id}</b> - To display subscription id in points redeemed in subscription renewal log<br><br>'
                    . '<b>{rsusedvouchercode}</b> - To display voucher code<br><br>'
                    . '<b>[rsmaxpoints]</b> - To display maximum threshold value for points<br><br>'
                    . '<b>[pointstocashback]</b> - To display points requested for cashback<br><br>'
                    . '<b>[cashbackamount]</b> - To display equivalent amount for requested cashback points'
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_shortcode_for_localization' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSLocalization::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSLocalization::reward_system_admin_fields() ) ;
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSLocalization::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function reset_localization_tab() {
            $settings = RSLocalization::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

    }

    RSLocalization::init() ;
}