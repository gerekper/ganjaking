<?php

/*
 * SMS Tab Setting
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSSms' ) ) {

    class RSSms {

        public static function init() {

            add_action( 'woocommerce_rs_settings_tabs_fpsms' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpsms' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'rs_default_settings_fpsms' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fpsms' , array( __CLASS__ , 'reset_sms_module' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_sms_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'rs_display_save_button_fpsms' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpsms' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;

            return apply_filters( 'woocommerce_fpsms_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'SMS Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_sms_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_sms_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_sms_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Phone Number Field for SMS Notification ' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_ph_no_sms_notification'
                ) ,
                array(
                    'title'   => __( 'Enable this Checkbox for Phone Number field ' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By Enabling this Checkbox for Phone Number field in Registration Page' , SRP_LOCALE ) ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'id'      => 'rs_ph_no_field_registration_page' ,
                    'class'   => 'rs_ph_no_field_registration_page' ,
                    'newids'  => 'rs_ph_no_field_registration_page' ,
                ) ,
                array(
                    'title'   => __( 'Phone Number Field Label' , SRP_LOCALE ) ,
                    'type'    => 'text' ,
                    'std'     => __( 'Phone Number' , SRP_LOCALE ) ,
                    'default' => __( 'Phone Number' , SRP_LOCALE ) ,
                    'id'      => 'rs_ph_no_field_label_registration' ,
                    'class'   => 'rs_ph_no_field_label_registration' ,
                    'newids'  => 'rs_ph_no_field_label_registration' ,
                ) ,
                array(
                    'title'   => __( 'Validation errors for empty Phone Number Field ' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'std'     => __( 'Phone Number is required' , SRP_LOCALE ) ,
                    'default' => __( 'Phone Number is required' , SRP_LOCALE ) ,
                    'id'      => 'rs_ph_no_validationerror_emptyfield' ,
                    'class'   => 'rs_ph_no_validationerror_emptyfield' ,
                    'newids'  => 'rs_ph_no_validationerror_emptyfield' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_ph_no_sms_notification' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'SMS Notification Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_sms_setting'
                ) ,
                array(
                    'title'   => __( 'Notification through SMS' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'id'      => 'rs_enable_send_sms_to_user' ,
                    'newids'  => 'rs_enable_send_sms_to_user' ,
                ) ,
                array(
                    'name'     => __( 'SMS API' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can choose the sms sending API' , SRP_LOCALE ) ,
                    'id'       => 'rs_sms_sending_api_option' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Twilio' ,
                        '2' => 'Nexmo' , ) ,
                    'newids'   => 'rs_sms_sending_api_option' ,
                    'class'    => 'rs_sms_sending_api_option' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'title'   => __( 'Send SMS on Earning Points' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'id'      => 'rs_send_sms_earning_points' ,
                    'class'   => 'rs_send_sms_earning_points' ,
                    'newids'  => 'rs_send_sms_earning_points' ,
                ) ,
                array(
                    'name'    => __( 'SMS Content' , SRP_LOCALE ) ,
                    'id'      => 'rs_points_sms_content_for_earning' ,
                    'std'     => 'Hi {username}, you have earned {points} on this order{orderid}. Currently, you have {rewardpoints} points in your account. You can make use of it to get discount on future purchases in {sitelink}.' ,
                    'default' => 'Hi {username}, you have earned {points} on this order{orderid}. Currently, you have {rewardpoints} points in your account. You can make use of it to get discount on future purchases in {sitelink}.' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_points_sms_content_for_earning' ,
                    'class'   => 'rs_points_sms_content_for_earning' ,
                ) ,
                array(
                    'title'   => __( 'Send SMS on Redeeming Points' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'id'      => 'rs_send_sms_redeeming_points' ,
                    'class'   => 'rs_send_sms_redeeming_points' ,
                    'newids'  => 'rs_send_sms_redeeming_points' ,
                ) ,
                array(
                    'name'    => __( 'SMS Content' , SRP_LOCALE ) ,
                    'id'      => 'rs_points_sms_content_for_redeeming' ,
                    'std'     => 'Hi {username}, you have redeemed {points} on this order {orderid}. Currently, you have {rewardpoints} points in your account. You can make use of it to get discount on future purchases in {sitelink}' ,
                    'default' => 'Hi {username}, you have redeemed {points} on this order {orderid}. Currently, you have {rewardpoints} points in your account. You can make use of it to get discount on future purchases in {sitelink}' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_points_sms_content_for_redeeming' ,
                    'class'   => 'rs_points_sms_content_for_redeeming' ,
                ) ,
                array(
                    'title'   => __( 'Send SMS on Earning Points for Actions' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'id'      => 'rs_send_sms_earning_points_for_actions' ,
                    'class'   => 'rs_send_sms_earning_points_for_actions' ,
                    'newids'  => 'rs_send_sms_earning_points_for_actions' ,
                ) ,
                array(
                    'name'    => __( 'SMS Content' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_sms_earning_points_content_for_actions' ,
                    'std'     => __( 'Hi [username], you have earned [points] for [rs_sms_for_actions]. Currently, you have [rewardpoints] points in your account. You can make use of it to get discount on future purchases in [sitelink].' , SRP_LOCALE ) ,
                    'default' => __( 'Hi [username], you have earned [points] for [rs_sms_for_actions]. Currently, you have [rewardpoints] points in your account. You can make use of it to get discount on future purchases in [sitelink].' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_send_sms_earning_points_content_for_actions' ,
                    'class'   => 'rs_send_sms_earning_points_content_for_actions' ,
                ) ,
                array(
                    'name'    => __( 'Twilio Account SID' , SRP_LOCALE ) ,
                    'id'      => 'rs_twilio_secret_account_id' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_twilio_secret_account_id' ,
                ) ,
                array(
                    'name'    => __( 'Twilio Account Auth Token' , SRP_LOCALE ) ,
                    'id'      => 'rs_twilio_auth_token_id' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_twilio_auth_token_id' ,
                ) ,
                array(
                    'name'    => __( 'Twilio From Number' , SRP_LOCALE ) ,
                    'id'      => 'rs_twilio_from_number' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_twilio_from_number' ,
                ) ,
                array(
                    'name'    => __( 'Nexmo Key' , SRP_LOCALE ) ,
                    'id'      => 'rs_nexmo_key' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_nexmo_key' ,
                ) ,
                array(
                    'name'    => __( 'Nexmo Secret' , SRP_LOCALE ) ,
                    'id'      => 'rs_nexmo_secret' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_nexmo_secret' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_sms_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSSms::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSSms::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_sms_module_checkbox' ] ) ) {
                update_option( 'rs_sms_activated' , $_POST[ 'rs_sms_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_sms_activated' , 'no' ) ;
            }
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSSms::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function reset_sms_module() {
            $settings = RSSms::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_sms_activated' ) , 'rs_sms_module_checkbox' , 'rs_sms_activated' ) ;
        }

    }

    RSSms::init() ;
}