<?php

/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSGatewayModule' ) ) {

    class RSGatewayModule {

        public static function init() {
            add_action( 'woocommerce_rs_settings_tabs_fprewardgateway' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fprewardgateway' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'woocommerce_admin_field_rs_enable_disable_gateway_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'woocommerce_admin_field_rs_product_for_purchase' , array( __CLASS__ , 'rs_purchase_selected_product_using_points' ) ) ;

            add_action( 'woocommerce_admin_field_rs_hide_gateway' , array( __CLASS__ , 'rs_selected_product_hide_gateway' ) ) ;

            add_action( 'rs_default_settings_fprewardgateway' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fprewardgateway' , array( __CLASS__ , 'reset_gateway_module' ) ) ;

            add_action( 'rs_display_save_button_fprewardgateway' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fprewardgateway' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;
            $newcombinedarray = fp_order_status() ;
            $categorylist     = fp_product_category() ;
            return apply_filters( 'woocommerce_fprewardgateway' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'SUMO Reward Points Payment Gateway Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_gateway_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_gateway_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_gateway_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'SUMO Reward Points Payment Gateway Visibility Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_reward_gateway_settings'
                ) ,
                array(
                    'name'     => __( 'SUMO Reward Points Payment Gateway is' , SRP_LOCALE ) ,
                    'desc'     => __( 'SUMO Reward Points Payment Gateway is Visible or Hidden for Selected Products And Categories' , SRP_LOCALE ) ,
                    'id'       => 'rs_show_hide_reward_points_gateway' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_show_hide_reward_points_gateway' ,
                    'options'  => array(
                        '1' => __( 'Visible for Selected Products/Categories' , SRP_LOCALE ) ,
                        '2' => __( 'Hidden for Selected Products/Categories' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'   => __( 'Product Purchase Using SUMO Reward Points Payment Gateway for Selected Product(s)' , SRP_LOCALE ) ,
                    'desc'   => __( 'Enable this option to purchase the selected product(s) using SUMO Reward Points Payment Gateway' , SRP_LOCALE ) ,
                    'id'     => 'rs_enable_selected_product_for_purchase_using_points' ,
                    'class'  => 'rs_enable_selected_product_for_purchase_using_points' ,
                    'newids' => 'rs_enable_selected_product_for_purchase_using_points' ,
                    'type'   => 'checkbox' ,
                ) ,
                array(
                    'type' => 'rs_product_for_purchase' ,
                ) ,
                array(
                    'name'   => __( 'Product Purchase Using SUMO Reward Points Payment Gateway for Selected Category' , SRP_LOCALE ) ,
                    'desc'   => __( 'Enable this option to purchase the product(s) in selected category using SUMO Reward Points Payment Gateway' , SRP_LOCALE ) ,
                    'id'     => 'rs_enable_selected_category_for_purchase_using_points' ,
                    'class'  => 'rs_enable_selected_category_for_purchase_using_points' ,
                    'newids' => 'rs_enable_selected_category_for_purchase_using_points' ,
                    'type'   => 'checkbox' ,
                ) ,
                array(
                    'name'     => __( 'Select Category' , SRP_LOCALE ) ,
                    'desc'     => __( 'Select Categories for Purchase Using SUMO Reward Points Payment Gateway' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_category_for_purchase_using_points' ,
                    'class'    => 'rs_select_category_for_purchase_using_points' ,
                    'css'      => 'min-width:350px' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'multiselect' ,
                    'newids'   => 'rs_select_category_for_purchase_using_points' ,
                    'options'  => $categorylist ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'For Other Product(s) display SUMO Reward Points Payment Gateway' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to display SUMO Reward Points Payment Gateway for other product(s)' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_gateway_visible_to_all_product' ,
                    'class'   => 'rs_enable_gateway_visible_to_all_product' ,
                    'newids'  => 'rs_enable_gateway_visible_to_all_product' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'yes' ,
                    'default' => 'yes' ,
                ) ,
                array(
                    'name'   => __( 'SUMO Reward Points Payment Gateway is hidden for Selected Product(s)' , SRP_LOCALE ) ,
                    'desc'   => __( 'Enable this option to hide SUMO Reward Points Payment Gateway for selected product(s) (Don\'t select point price product)' , SRP_LOCALE ) ,
                    'id'     => 'rs_enable_selected_product_for_hide_gateway' ,
                    'class'  => 'rs_enable_selected_product_for_hide_gateway' ,
                    'newids' => 'rs_enable_selected_product_for_hide_gateway' ,
                    'type'   => 'checkbox' ,
                ) ,
                array(
                    'type' => 'rs_hide_gateway' ,
                ) ,
                array(
                    'name'   => __( 'SUMO Reward Points Payment Gateway is hidden for Selected Category' , SRP_LOCALE ) ,
                    'desc'   => __( 'Enable this option to hide SUMO Reward Points Payment Gateway for product(s) in selected cateogry (Don\'t select category that contain point price product)' , SRP_LOCALE ) ,
                    'id'     => 'rs_enable_selected_category_to_hide_gateway' ,
                    'class'  => 'rs_enable_selected_category_to_hide_gateway' ,
                    'newids' => 'rs_enable_selected_category_to_hide_gateway' ,
                    'type'   => 'checkbox' ,
                ) ,
                array(
                    'name'     => __( 'Select Category' , SRP_LOCALE ) ,
                    'desc'     => __( 'Select Category to hide SUMO Reward Points Payment Gateway' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_category_to_hide_gateway' ,
                    'class'    => 'rs_select_category_to_hide_gateway' ,
                    'css'      => 'min-width:350px' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'multiselect' ,
                    'newids'   => 'rs_select_category_to_hide_gateway' ,
                    'options'  => $categorylist ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message when other products added to Cart Page' , SRP_LOCALE ) ,
                    'desc'     => __( 'Error Message when other products added to Cart Page' , SRP_LOCALE ) ,
                    'id'       => 'rs_errmsg_when_other_products_added_to_cart_page' ,
                    'std'      => '[productname] is removed from the Cart.Because it can be purchased only through Reward points' ,
                    'default'  => '[productname] is removed from the Cart.Because it can be purchased only through Reward points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_errmsg_when_other_products_added_to_cart_page' ,
                    'class'    => 'rs_errmsg_when_other_products_added_to_cart_page' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_reward_gateway_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'SUMO Reward Points Payment Gateway Status Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_status_setting'
                ) ,
                array(
                    'name'     => __( 'Order(s) placed through SUMO Reward Points Payment Gateway will go to' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can set what should be the order status after successful payment with SUMO Reward Points Gateway' , SRP_LOCALE ) ,
                    'id'       => 'rs_order_status_after_gateway_purchase' ,
                    'std'      => 'completed' ,
                    'default'  => 'completed' ,
                    'type'     => 'radio' ,
                    'options'  => $newcombinedarray ,
                    'newids'   => 'rs_order_status_after_gateway_purchase' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Redeemed Points will be revised to the account when the Order Status reaches' , SRP_LOCALE ) ,
                    'desc'     => __( 'This option controls when the points redeemed in order should be revised from user\'s account' , SRP_LOCALE ) ,
                    'id'       => 'rs_order_status_control_revise_redeem' ,
                    'std'      => array( 'cancelled' , 'refunded' , 'failed' ) ,
                    'default'  => array( 'cancelled' , 'refunded' , 'failed' ) ,
                    'type'     => 'multiselect' ,
                    'options'  => $newcombinedarray ,
                    'newids'   => 'rs_order_status_control_revise_redeem' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_status_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Minimum Cart Total Settings for SUMO Reward Points Payment Gateway' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_discount_control_for_gateway'
                ) ,
                array(
                    'name'              => __( 'Minimum Cart Total for using SUMO Reward Points Payment Gateway' , SRP_LOCALE ) ,
                    'desc'              => __( 'Enter the Minimum Cart Total that can be used using SUMO Reward Points Payment Gateway' , SRP_LOCALE ) ,
                    'id'                => 'rs_max_redeem_discount_for_sumo_reward_points' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => 0
                    ) ,
                    'newids'            => 'rs_max_redeem_discount_for_sumo_reward_points' ,
                    'desc_tip'          => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_discount_control_for_gateway' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSGatewayModule::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSGatewayModule::reward_system_admin_fields() ) ;

            //Save payment gateway selected products
            if ( isset( $_POST[ 'rs_select_product_for_purchase_using_points' ] ) ) {
                update_option( 'rs_select_product_for_purchase_using_points' , $_POST[ 'rs_select_product_for_purchase_using_points' ] ) ;
            } else {
                update_option( 'rs_select_product_for_purchase_using_points' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_select_product_for_hide_gateway' ] ) ) {
                update_option( 'rs_select_product_for_hide_gateway' , $_POST[ 'rs_select_product_for_hide_gateway' ] ) ;
            } else {
                update_option( 'rs_select_product_for_hide_gateway' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_gateway_module_checkbox' ] ) ) {
                update_option( 'rs_gateway_activated' , $_POST[ 'rs_gateway_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_gateway_activated' , 'no' ) ;
            }
        }

        public static function reset_gateway_module() {
            $settings = RSGatewayModule::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_gateway_activated' ) , 'rs_gateway_module_checkbox' , 'rs_gateway_activated' ) ;
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSGatewayModule::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        /*
         * Function to select the products which are going to be buy using Reward Points
         */

        public static function rs_purchase_selected_product_using_points() {
            $field_id    = "rs_select_product_for_purchase_using_points" ;
            $field_label = "Select Product(s)" ;
            $getproducts = get_option( 'rs_select_product_for_purchase_using_points' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        public static function rs_selected_product_hide_gateway() {
            $field_id    = "rs_select_product_for_hide_gateway" ;
            $field_label = "Select Product(s)" ;
            $getproducts = get_option( 'rs_select_product_for_hide_gateway' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

    }

    RSGatewayModule::init() ;
}