<?php

/*
 * Buying Points Module Setting
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSBuyingPoints' ) ) {

    class RSBuyingPoints {

        public static function init() {

            add_action( 'woocommerce_rs_settings_tabs_fpbuyingpoints' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpbuyingpoints' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'rs_default_settings_fpbuyingpoints' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_buyingpoints_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'woocommerce_admin_field_rs_include_products_for_buying_points' , array( __CLASS__ , 'rs_include_products_for_buying_points' ) ) ;

            add_action( 'woocommerce_admin_field_rs_exclude_products_for_buying_points' , array( __CLASS__ , 'rs_exclude_products_for_buying_points' ) ) ;

            add_action( 'woocommerce_admin_field_rs_button_for_buying_points' , array( __CLASS__ , 'rs_save_button_for_buying_points' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;

            return apply_filters( 'woocommerce_fpbuyingpoints_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Buying Points Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_buyingpoints_module'
                ) ,
                array(
                    'type' => 'rs_enable_buyingpoints_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_buyingpoints_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Buying Reward Points Bulk Update Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_bulk_update_settings_for_buying_points' ,
                ) ,
                array(
                    'name'    => __( 'Product Selection' , SRP_LOCALE ) ,
                    'id'      => 'rs_buying_points_is_applicable' ,
                    'std'     => '1' ,
                    'class'   => 'rs_buying_points_is_applicable' ,
                    'default' => '1' ,
                    'newids'  => 'rs_buying_points_is_applicable' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'All Product(s)' , SRP_LOCALE ) ,
                        '2' => __( 'Include Product(s)' , SRP_LOCALE ) ,
                        '3' => __( 'Exclude Product(s)' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'type' => 'rs_include_products_for_buying_points' ,
                ) ,
                array(
                    'type' => 'rs_exclude_products_for_buying_points' ,
                ) ,
                array(
                    'name'    => __( 'Enable Buying of SUMO Reward Points' ) ,
                    'id'      => 'rs_enable_buying_points' ,
                    'std'     => 'no' ,
                    'class'   => 'rs_enable_buying_points' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_buying_points' ,
                    'type'    => 'select' ,
                    'options' => array(
                        'yes' => __( 'Enable' , SRP_LOCALE ) ,
                        'no'  => __( 'Disable' , SRP_LOCALE ) ,
                    )
                ) ,
                array(
                    'name'    => __( 'Buy Reward Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_points_for_buying_points' ,
                    'class'   => 'rs_points_for_buying_points' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_points_for_buying_points' ,
                ) ,
                array(
                    'type' => 'rs_button_for_buying_points' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_bulk_update_settings_for_buying_points' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSBuyingPoints::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSBuyingPoints::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_buyingpoints_module_checkbox' ] ) ) {
                update_option( 'rs_buyingpoints_activated' , $_POST[ 'rs_buyingpoints_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_buyingpoints_activated' , 'no' ) ;
            }
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSBuyingPoints::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_buyingpoints_activated' ) , 'rs_buyingpoints_module_checkbox' , 'rs_buyingpoints_activated' ) ;
        }

        public static function rs_include_products_for_buying_points() {
            $field_id    = "rs_include_products_for_buying_points" ;
            $field_label = "Include Product(s)" ;
            $getproducts = get_option( 'rs_include_products_for_buying_points' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        public static function rs_exclude_products_for_buying_points() {
            $field_id    = "rs_exclude_products_for_buying_points" ;
            $field_label = "Exclude Product(s)" ;
            $getproducts = get_option( 'rs_exclude_products_for_buying_points' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        public static function rs_save_button_for_buying_points() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row"></th>
                <td class="forminp forminp-select">
                    <input type="button" class="rs_bulk_update_button_for_buying_points button-primary" value="Save and Update"/>
                </td>
            </tr>
            <?php

        }

    }

    RSBuyingPoints::init() ;
}