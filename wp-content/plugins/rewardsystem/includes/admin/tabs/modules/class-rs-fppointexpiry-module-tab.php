<?php

/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSPointExpiryModule' ) ) {

    class RSPointExpiryModule {

        public static function init() {
            add_action( 'rs_default_settings_fppointexpiry' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_rs_settings_tabs_fppointexpiry' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fppointexpiry' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'woocommerce_admin_field_rs_enable_disable_point_expiry_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fppointexpiry' , array( __CLASS__ , 'reset_point_expiry_module' ) ) ;
            
            add_action( 'rs_display_save_button_fppointexpiry' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fppointexpiry' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;
            return apply_filters( 'woocommerce_fppointexpiry' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Points Expiry Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_point_expiry_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_point_expiry_module' ,
                ) ,
                array(
                    'name'              => __( 'Validity Period for Points' , SRP_LOCALE ) ,
                    'type'              => 'number' ,
                    'id'                => 'rs_point_to_be_expire' ,
                    'class'             => 'rs_point_to_be_expire' ,
                    'newids'            => 'rs_point_to_be_expire' ,
                    'custom_attributes' => array(
                        'min' => '0'
                    ) ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'desc'              => __( 'Reward points earned will expire after the number of days specified. The number of days is calculated from the date of earning' , SRP_LOCALE ) ,
                    'desc_tip'          => true
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_point_expiry_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
            ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSPointExpiryModule::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSPointExpiryModule::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_point_expiry_module_checkbox' ] ) ) {
                update_option( 'rs_point_expiry_activated' , $_POST[ 'rs_point_expiry_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_point_expiry_activated' , 'no' ) ;
            }
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSPointExpiryModule::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function reset_point_expiry_module() {
            $settings = RSPointExpiryModule::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_point_expiry_activated' ) , 'rs_point_expiry_module_checkbox' ,'rs_point_expiry_activated') ;
        }

    }

    RSPointExpiryModule::init() ;
}