<?php

/*
 * Coupon Compatability Setting
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSCouponCompatability' ) ) {

    class RSCouponCompatability {

        public static function init() {

            add_action( 'woocommerce_rs_settings_tabs_fpcoupon' , array( __CLASS__ , 'register_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpcoupon' , array( __CLASS__ , 'update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'fp_action_to_reset_settings_fpcoupon' , array( __CLASS__ , 'reset_coupon_module' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_coupon_compatability_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'rs_display_save_button_fpcoupon' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpcoupon' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_coupon_campatability_activated' ) , 'rs_coupon_compatability_module_checkbox' , 'rs_coupon_campatability_activated' ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function settings_fields() {
            global $woocommerce ;

            return apply_filters( 'woocommerce_rewardsystem_coupon_compatability_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Coupon Compatibility Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_coupon_compatability_module'
                ) ,
                array(
                    'type' => 'rs_enable_coupon_compatability_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_coupon_compatability_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'SUMO Coupon Compatibility Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_coupon_compatability_setting'
                ) ,
                array(
                    'name'    => __( 'Don\'t allow Earn Points when SUMO Coupon is applied' , SRP_LOCALE ) ,
                    'desc'    => __( ' Don\'t allow Earn Points when SUMO Coupon is applied' , SRP_LOCALE ) ,
                    'id'      => '_rs_not_allow_earn_points_if_sumo_coupon' ,
                    'css'     => 'min-width:550px;' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => '_rs_not_allow_earn_points_if_sumo_coupon' ,
                ) ,
                array(
                    'name'    => __( 'Don\'t allow Redeem when SUMO Coupon is applied' , SRP_LOCALE ) ,
                    'desc'    => __( 'Don\'t allow Redeem when SUMO Coupon is applied' , SRP_LOCALE ) ,
                    'id'      => 'rs_dont_allow_redeem_if_sumo_coupon' ,
                    'css'     => 'min-width:550px;' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_dont_allow_redeem_if_sumo_coupon' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_coupon_compatability_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function register_settings() {
            woocommerce_admin_fields( RSCouponCompatability::settings_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function update_settings() {
            woocommerce_update_options( RSCouponCompatability::settings_fields() ) ;
            if ( isset( $_POST[ 'rs_coupon_compatability_module_checkbox' ] ) ) {
                update_option( 'rs_coupon_compatability_activated' , $_POST[ 'rs_coupon_compatability_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_coupon_compatability_activated' , 'no' ) ;
            }
        }

        public static function set_default_values( $modules ) {
            $modules = array_merge( $modules , array( 'fpcoupon' ) ) ;
            return $modules ;
        }

        public static function reset_coupon_module() {
            $settings = RSCouponCompatability::settings_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

    }

    RSCouponCompatability::init() ;
}