<?php

/*
 * Discounts Compatability Setting
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSDiscountsCompatability' ) ) {

    class RSDiscountsCompatability {

        public static function init() {

            add_action( 'woocommerce_rs_settings_tabs_fpdiscounts' , array( __CLASS__ , 'register_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpdiscounts' , array( __CLASS__ , 'update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'fp_action_to_reset_settings_fpdiscounts' , array( __CLASS__ , 'reset_discounts_module' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_discounts_compatability_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_filter( 'rs_default_value_modules' , array( __CLASS__ , 'set_default_values' ) ) ;

            add_action( 'rs_display_save_button_fpdiscounts' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpdiscounts' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_discounts_compatability_activated' ) , 'rs_discounts_compatability_module_checkbox' , 'rs_discounts_compatability_activated' ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function settings_fields() {
            global $woocommerce ;

            return apply_filters( 'woocommerce_fpdiscounts_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Discounts Compatibility Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_discounts_compatability_module'
                ) ,
                array(
                    'type' => 'rs_enable_discounts_compatability_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_discounts_compatability_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'SUMO Discounts Compatibility Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_discounts_compatability_setting'
                ) ,
                array(
                    'name'    => __( 'Don\'t allow Earn Points when SUMO Discount is applied' , SRP_LOCALE ) ,
                    'desc'    => __( 'Don\'t allow Earn Points when SUMO Discount is applied' , SRP_LOCALE ) ,
                    'id'      => '_rs_not_allow_earn_points_if_sumo_discount' ,
                    'css'     => 'min-width:550px;' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => '_rs_not_allow_earn_points_if_sumo_discount' ,
                ) ,
                array(
                    'name'    => __( 'Show Redeeming Field' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_redeeming_field' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_redeeming_field' ,
                    'options' => array(
                        '1' => __( 'Always' , SRP_LOCALE ) ,
                        '2' => __( 'When Price is not altered through SUMO Discounts Plugin' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Message' , SRP_LOCALE ) ,
                    'id'      => 'rs_redeeming_usage_restriction_for_discount' ,
                    'std'     => __( 'Since you got the discount, you can\'t redeem the points in this order' , SRP_LOCALE ) ,
                    'default' => __( 'Since you got the discount, you can\'t redeem the points in this order' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_redeeming_usage_restriction_for_discount' ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide WooCommerce Coupon Field' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this checkbox to prevent woocommerce coupon usage when discount from SUMO Discount plugin is applied' , SRP_LOCALE ) ,
                    'id'      => '_rs_show_hide_coupon_if_sumo_discount' ,
                    'css'     => 'min-width:550px;' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => '_rs_show_hide_coupon_if_sumo_discount' ,
                ) ,
                array(
                    'name'    => __( 'Message' , SRP_LOCALE ) ,
                    'id'      => 'rs_message_in_cart_and_checkout_for_discount' ,
                    'std'     => __( 'Since you got the discount, you can\'t use the WooCommerce Coupon' , SRP_LOCALE ) ,
                    'default' => __( 'Since you got the discount, you can\'t use the WooCommerce Coupon' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_message_in_cart_and_checkout_for_discount' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_discounts_compatability_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function register_settings() {

            woocommerce_admin_fields( RSDiscountsCompatability::settings_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function update_settings() {
            woocommerce_update_options( RSDiscountsCompatability::settings_fields() ) ;
            if ( isset( $_POST[ 'rs_discounts_compatability_module_checkbox' ] ) ) {
                update_option( 'rs_discounts_compatability_activated' , $_POST[ 'rs_discounts_compatability_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_discounts_compatability_activated' , 'no' ) ;
            }
        }

        public static function set_default_values( $modules ) {
            $modules = array_merge( $modules , array( 'fpdiscounts' ) ) ;
            return $modules ;
        }

        public static function reset_discounts_module() {
            $settings = RSDiscountsCompatability::settings_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

    }

    RSDiscountsCompatability::init() ;
}
