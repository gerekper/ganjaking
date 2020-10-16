<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSProductPurchaseModule' ) ) {

    class RSProductPurchaseModule {

        public static function init() {

            add_action( 'rs_default_settings_fpproductpurchase' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_rs_settings_tabs_fpproductpurchase' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpproductpurchase' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system

            add_action( 'woocommerce_admin_field_selected_products' , array( __CLASS__ , 'rs_select_products_to_update' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_product_purchase_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'woocommerce_admin_field_button' , array( __CLASS__ , 'rs_save_button_for_update' ) ) ;

            add_action( 'woocommerce_admin_field_rs_include_products_for_product_purchase' , array( __CLASS__ , 'rs_include_products_for_product_purchase' ) ) ;

            add_action( 'woocommerce_admin_field_rs_exclude_products_for_product_purchase' , array( __CLASS__ , 'rs_exclude_products_for_product_purchase' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fpproductpurchase' , array( __CLASS__ , 'reset_product_purchase_module' ) ) ;

            add_action( 'rs_display_save_button_fpproductpurchase' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpproductpurchase' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            //Section and option details
            if ( class_exists( 'WooCommerce_PDF_Invoices' ) ) {
                $section_title = 'Message Settings in Edit Order Page and Invoices' ;
                $option_title  = __( 'Display Points from Order on Order Details Page and Invoices' , SRP_LOCALE ) ;
            } else {
                $section_title = 'Message Settings in Edit Order Page' ;
                $option_title  = __( 'Display Points from Order on Order Details' , SRP_LOCALE ) ;
            }

            $categorylist               = fp_product_category() ;
            $available_payment_gateways = rs_get_payment_gateways() ;
            return apply_filters( 'woocommerce_fpproductpurchase' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Product Purchase Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_product_purchase_module' ,
                    'desc' => __( 'By Enabling this Module you can award Reward Points for Product Purchase' , SRP_LOCALE ) ,
                ) ,
                array(
                    'type' => 'rs_enable_disable_product_purchase_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_product_purchase_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Product Purchase Reward Points Global Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_product_purchase_module' ,
                ) ,
                array(
                    'name'     => __( 'Product Purchase Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_enable_product_category_level_for_product_purchase' ,
                    'class'    => 'rs_enable_product_category_level_for_product_purchase' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'type'     => 'radio' ,
                    'newids'   => 'rs_enable_product_category_level_for_product_purchase' ,
                    'options'  => array(
                        'no'  => __( 'Quick Setup (Global Level Settings will be enabled)' , SRP_LOCALE ) ,
                        'yes' => __( 'Advanced Setup (Global,Category and Product Level wil be enabled)' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                    'desc'     => __( 'Quick Setup - Points can be configured to products in a single action<br>Advanced Setup - Points can be configured to products based on Product Level/Category Level/Global Level' , SRP_LOCALE )
                ) ,
                array(
                    'name'    => __( 'Earning Points Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_award_points_for_cart_or_product_total' ,
                    'std'     => '1' ,
                    'class'   => 'rs_award_points_for_cart_or_product_total' ,
                    'default' => '1' ,
                    'newids'  => 'rs_award_points_for_cart_or_product_total' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Product Total' , SRP_LOCALE ) ,
                        '2' => __( 'Cart Total' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'        => __( 'Earning Points based on Cart Total' , SRP_LOCALE ) ,
                    'id'          => 'rs_enable_cart_total_reward_points' ,
                    'class'       => 'show_if_cart_total' ,
                    'std'         => '2' ,
                    'default'     => '2' ,
                    'placeholder' => '' ,
                    'newids'      => 'rs_enable_cart_total_reward_points' ,
                    'type'        => 'select' ,
                    'options'     => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Reward Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_reward_type_for_cart_total' ,
                    'class'   => 'show_if_cart_total' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_reward_type_for_cart_total' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Cart Total' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'              => __( 'Reward Points' , SRP_LOCALE ) ,
                    'id'                => 'rs_reward_points_for_cart_total_in_fixed' ,
                    'class'             => 'show_if_cart_total' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'type'              => 'number' ,
                    'newids'            => 'rs_reward_points_for_cart_total_in_fixed' ,
                    'custom_attributes' => array(
                        'min' => '0'
                    ) ,
                ) ,
                array(
                    'name'              => __( 'Reward Points in Percent %' , SRP_LOCALE ) ,
                    'id'                => 'rs_reward_points_for_cart_total_in_percent' ,
                    'class'             => 'show_if_cart_total' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'type'              => 'number' ,
                    'newids'            => 'rs_reward_points_for_cart_total_in_percent' ,
                    'custom_attributes' => array(
                        'min' => '0'
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Product Purchase Reward Points is applicable for' , SRP_LOCALE ) ,
                    'id'      => 'rs_product_purchase_global_level_applicable_for' ,
                    'std'     => '1' ,
                    'class'   => 'rs_product_purchase_global_level_applicable_for' ,
                    'default' => '1' ,
                    'newids'  => 'rs_product_purchase_global_level_applicable_for' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'All Product(s)' , SRP_LOCALE ) ,
                        '2' => __( 'Include Product(s)' , SRP_LOCALE ) ,
                        '3' => __( 'Exclude Product(s)' , SRP_LOCALE ) ,
                        '4' => __( 'All Categories' , SRP_LOCALE ) ,
                        '5' => __( 'Include Categories' , SRP_LOCALE ) ,
                        '6' => __( 'Exclude Categories' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'type' => 'rs_include_products_for_product_purchase' ,
                ) ,
                array(
                    'type' => 'rs_exclude_products_for_product_purchase' ,
                ) ,
                array(
                    'name'    => __( 'Include Categories' , SRP_LOCALE ) ,
                    'id'      => 'rs_include_particular_categories_for_product_purchase' ,
                    'css'     => 'min-width:350px;' ,
                    'std'     => '' ,
                    'class'   => 'rs_include_particular_categories_for_product_purchase' ,
                    'default' => '' ,
                    'newids'  => 'rs_include_particular_categories_for_product_purchase' ,
                    'type'    => 'multiselect' ,
                    'options' => $categorylist ,
                ) ,
                array(
                    'name'    => __( 'Exclude Categories' , SRP_LOCALE ) ,
                    'id'      => 'rs_exclude_particular_categories_for_product_purchase' ,
                    'css'     => 'min-width:350px;' ,
                    'std'     => '' ,
                    'class'   => 'rs_exclude_particular_categories_for_product_purchase' ,
                    'default' => '' ,
                    'newids'  => 'rs_exclude_particular_categories_for_product_purchase' ,
                    'type'    => 'multiselect' ,
                    'options' => $categorylist ,
                ) ,
                array(
                    'name'        => __( 'Global Level Reward Points' , SRP_LOCALE ) ,
                    'id'          => 'rs_global_enable_disable_sumo_reward' ,
                    'std'         => '2' ,
                    'default'     => '2' ,
                    'placeholder' => '' ,
                    'desc_tip'    => false ,
                    'desc'        => __( '<b>Quick Setup</b><br> - To assign points to your products, you should enable the "Global Level Reward Points" option, select the reward type & set the points value based on your needs.
<br><br>
<b>Advanced Setup </b><br>
- It is not mandatory to enable the "Global Level Reward Points" option. You can configure the points to your products either on the Product Level/Category Level/Global Level.
<br>
- Product Purchase settings should be enabled on product level & don\'t set the points there if you wish to assign the points through the Global Level.
<br><br>
<b>Note:</b>
Earning Points Conversion Settings given in the General Settings will consider only when you choose the Reward Type as Percentage of Product Price.' , SRP_LOCALE ) ,
                    'newids'      => 'rs_global_enable_disable_sumo_reward' ,
                    'type'        => 'select' ,
                    'options'     => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Reward Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_global_reward_type' ,
                    'class'   => 'show_if_enable_in_general' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_global_reward_type' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'        => __( 'Reward Points' , SRP_LOCALE ) ,
                    'id'          => 'rs_global_reward_points' ,
                    'class'       => 'show_if_enable_in_general' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'type'        => 'text' ,
                    'newids'      => 'rs_global_reward_points' ,
                    'placeholder' => '' ,
                    'desc'        => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip'    => true ,
                ) ,
                array(
                    'name'        => __( 'Reward Points in Percent %' , SRP_LOCALE ) ,
                    'id'          => 'rs_global_reward_percent' ,
                    'class'       => 'show_if_enable_in_general' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'type'        => 'text' ,
                    'newids'      => 'rs_global_reward_percent' ,
                    'placeholder' => '' ,
                    'desc'        => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip'    => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_product_purchase_module' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points for First Purchase' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_first_purchase_module' ,
                ) ,
                array(
                    'name'    => __( 'Reward Points for First Purchase' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_first_purchase_reward_points' ,
                    'class'   => 'rs_enable_first_purchase_reward_points' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_first_purchase_reward_points' ,
                    'type'    => 'checkbox' ,
                ) ,
                array(
                    'name'              => __( 'Reward Points' , SRP_LOCALE ) ,
                    'id'                => 'rs_reward_points_for_first_purchase_in_fixed' ,
                    'class'             => 'show_if_first_purchase' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'type'              => 'number' ,
                    'newids'            => 'rs_reward_points_for_first_purchase_in_fixed' ,
                    'custom_attributes' => array(
                        'min' => '0'
                    ) ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_first_purchase_module' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_hide_bulk_update_for_product_purchase_start' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Product Purchase Reward Points Bulk Update Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_update_setting' ,
                    'desc' => __( 'This Settings can be used to Configure Reward Points to Multiple Products/Categories at once' , SRP_LOCALE )
                ) ,
                array(
                    'name'     => __( 'Product/Category Selection' , SRP_LOCALE ) ,
                    'id'       => 'rs_which_product_selection' ,
                    'std'      => '1' ,
                    'class'    => 'rs_which_product_selection' ,
                    'default'  => '1' ,
                    'newids'   => 'rs_which_product_selection' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'All Products' , SRP_LOCALE ) ,
                        '2' => __( 'Selected Products' , SRP_LOCALE ) ,
                        '3' => __( 'All Categories' , SRP_LOCALE ) ,
                        '4' => __( 'Selected Categories' , SRP_LOCALE ) ,
                    ) ,
                    'desc'     => __( 'Select the Products/Categories for which the bulk update has to be processed' , SRP_LOCALE ) ,
                    'desc_tip' => true
                ) ,
                array(
                    'name'   => __( 'Selected Particular Products' , SRP_LOCALE ) ,
                    'type'   => 'selected_products' ,
                    'id'     => 'rs_select_particular_products' ,
                    'class'  => 'rs_select_particular_products' ,
                    'newids' => 'rs_select_particular_products' ,
                ) ,
                array(
                    'name'    => __( 'Select Particular Categories' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_particular_categories' ,
                    'css'     => 'min-width:350px;' ,
                    'std'     => '1' ,
                    'class'   => 'rs_select_particular_categories' ,
                    'default' => '1' ,
                    'newids'  => 'rs_select_particular_categories' ,
                    'type'    => 'multiselect' ,
                    'options' => $categorylist ,
                ) ,
                array(
                    'name'     => __( 'Enable SUMO Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_enable_disable_reward' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'desc_tip' => true ,
                    'desc'     => __( 'Enable will Turn On Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                            . 'Disable will Turn Off Reward Points for Product Purchase and Category/Product Settings will be considered if it is available.' , SRP_LOCALE ) ,
                    'newids'   => 'rs_local_enable_disable_reward' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Reward Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_local_reward_type' ,
                    'class'   => 'show_if_enable_in_reward' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_local_reward_type' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Reward Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_local_reward_points' ,
                    'class'   => 'show_if_enable_in_reward' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_local_reward_points' ,
                ) ,
                array(
                    'name'    => __( 'Reward Points in Percent %' , SRP_LOCALE ) ,
                    'id'      => 'rs_local_reward_percent' ,
                    'class'   => 'show_if_enable_in_reward' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_local_reward_percent' ,
                ) ,
                array(
                    'type' => 'button' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_update_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_hide_bulk_update_for_product_purchase_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Guest Registration Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_checkout_force_login' ,
                ) ,
                array(
                    'name'    => __( 'Force Guest to Create Account before placing the order which contain Points associated Product' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_acc_creation_for_guest_checkout_page' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_acc_creation_for_guest_checkout_page' ,
                    'type'    => 'checkbox' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_checkout_force_login' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Product Purchase Reward Points Restrictions' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_restriction_in_cart_settings' ,
                ) ,
                array(
                    'name'     => __( 'Calculate Reward Points for Product Purchase based on' , SRP_LOCALE ) ,
                    'id'       => 'rs_calculate_point_based_on_reg_or_sale' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'newids'   => 'rs_calculate_point_based_on_reg_or_sale' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Regular Price' , SRP_LOCALE ) ,
                        '2' => __( 'Sale Price' , SRP_LOCALE ) ,
                    ) ,
                    'desc'     => __( 'Applicable only for “Percentage of Product Price” reward type' , SRP_LOCALE ) ,
                    'desc_tip' => true
                ) ,
                array(
                    'name'    => __( 'Sale Priced Products' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to prevent earning of points on products that have "sale price"' , SRP_LOCALE ) ,
                    'id'      => 'rs_point_not_award_when_sale_price' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_point_not_award_when_sale_price' ,
                ) ,
                array(
                    'name'    => __( 'Calculate Reward Points after Discounts(WooCommerce Coupons / Points Redeeming)' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will calculate reward points for the price after excluding the coupon/ points redeeming discounts' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_disable_reward_point_based_coupon_amount' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_disable_reward_point_based_coupon_amount' ,
                ) ,
                array(
                    'name'    => __( 'Enable this option to award the product purchase earn point without tax' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to calculate product purchase earn point without tax' , SRP_LOCALE ) ,
                    'id'      => 'rs_display_earn_point_tax_based' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_display_earn_point_tax_based' ,
                ) ,
                array(
                    'name'    => __( 'Restrict Product Purchase Reward Points when Reward Points is Redeemed' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will restrict product purchase reward points when reward points is redeemed for the order' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_redeem_for_order' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_redeem_for_order' ,
                ) ,
                array(
                    'name'    => __( 'Restrict Product Purchase Reward Points when WooCommerce Coupon is applied' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will restrict product purchase reward points when woocommerce coupon is applied on order' , SRP_LOCALE ) ,
                    'id'      => 'rs_disable_point_if_coupon' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_disable_point_if_coupon' ,
                ) ,
                array(
                    'name'     => __( 'Restrict Product Purchase Reward Points when Selected Payment Gateway is used' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enabling this option will restrict product purchase reward points when Selected Payment gateway is used on order' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_payment_gateway_for_restrict_reward' ,
                    'class'    => 'rs_select_payment_gateway_for_restrict_reward' ,
                    'std'      => get_option( 'rs_disable_point_if_reward_points_gateway' , 'no' ) == 'yes' ? array( 'reward_gateway' ) : array() ,
                    'default'  => get_option( 'rs_disable_point_if_reward_points_gateway' , 'no' ) == 'yes' ? array( 'reward_gateway' ) : array() ,
                    'type'     => 'multiselect' ,
                    'options'  => $available_payment_gateways ,
                    'newids'   => 'rs_select_payment_gateway_for_restrict_reward' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Restrict Product Purchase Reward Points when more than one quantity of the product is updated by the user' , SRP_LOCALE ) ,
                    'id'      => 'rs_restrict_reward' ,
                    'desc'    => __( 'By enabling this option, one quantity of the points will be awarded to the user if they purchase more than one quantity of the product' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_restrict_reward' ,
                ) ,
                array(
                    'name'    => __( 'Hold Product Purchase Reward Points for certain period of time' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option, you can hold the product purchase reward points for a specific period of time[after order status reached, General -> Reward Points Order Status Settings for Earning]' , SRP_LOCALE ) ,
                    'id'      => 'rs_restrict_days_for_product_purchase' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_restrict_days_for_product_purchase' ,
                ) ,
                 array(
                    'name'    => __( 'Exclude Shipping Cost' , SRP_LOCALE ) ,
                    'desc'    => __( 'By enabling this checkbox, you can exclude the shipping cost in product purchase points. <br/>Note: Works with WooCommerce v3.2.0 or Above', SRP_LOCALE ) ,
                    'id'      => 'rs_exclude_shipping_cost_based_on_cart_total' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_exclude_shipping_cost_based_on_cart_total' ,
                ) ,
                array(
                    'name'     => __( 'Cron Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_restrict_product_purchase_cron_type' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_restrict_product_purchase_cron_type' ,
                    'desc_tip' => true ,
                    'options'  => array( 'minutes' => 'Minutes' , 'hours' => 'Hours' , 'days' => 'Days' ) ,
                    'std'      => 'days' ,
                    'default'  => 'days' ,
                ) ,
                array(
                    'name'    => __( 'Enter the Cron Time' , SRP_LOCALE ) ,
                    'id'      => 'rs_restrict_product_purchase_time' ,
                    'newids'  => 'rs_restrict_product_purchase_time' ,
                    'type'    => 'text' ,
                    'std'     => '3' ,
                    'default' => '3' ,
                ) ,
                array(
                    'name'              => __( 'Minimum Cart Total to Earn Point(s)' , SRP_LOCALE ) ,
                    'id'                => 'rs_minimum_cart_total_for_earning' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'type'              => 'number' ,
                    'newids'            => 'rs_minimum_cart_total_for_earning' ,
                    'desc'              => __( 'Minimum Cart total needed in order to earn product purchase Reward Points' , SRP_LOCALE ) ,
                    'desc_tip'          => true ,
                    'custom_attributes' => array(
                        'min' => '0'
                    )
                ) ,
                array(
                    'name'    => __( 'Show/Hide Minimum Cart Total Error Message' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_minimum_cart_total_earn_error_message' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_minimum_cart_total_earn_error_message' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when the user doesn\'t have enough Cart Total for Earning' , SRP_LOCALE ) ,
                    'id'       => 'rs_min_cart_total_for_earning_error_message' ,
                    'std'      => 'You need Minimum of [carttotal] carttotal to Earn Points' ,
                    'default'  => 'You need Minimum of [carttotal] carttotal to Earn Points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_min_cart_total_for_earning_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'              => __( 'Maximum Cart Total to Earn Point(s)' , SRP_LOCALE ) ,
                    'id'                => 'rs_maximum_cart_total_for_earning' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'type'              => 'number' ,
                    'newids'            => 'rs_maximum_cart_total_for_earning' ,
                    'desc'              => __( 'Maximum Cart total needed in order to earn product purchase Reward Points' , SRP_LOCALE ) ,
                    'desc_tip'          => true ,
                    'custom_attributes' => array(
                        'min' => '0'
                    )
                ) ,
                array(
                    'name'    => __( 'Show/Hide Maximum Cart Total Error Message' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_maximum_cart_total_earn_error_message' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_maximum_cart_total_earn_error_message' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Error Message Displayed when the user\'s cart total is more than the maximum cart total for earning reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_max_cart_total_for_earning_error_message' ,
                    'std'      => 'Since, you reached the maximum cart total [carttotal],you cannot earn points for this order.' ,
                    'default'  => 'Since, you reached the maximum cart total [carttotal],you cannot earn points for this order.' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_max_cart_total_for_earning_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_restriction_in_cart_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Message Settings in Cart, Checkout and Thank You Page' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_product_purchase_reward_messages' ,
                ) ,
                array(
                    'type' => 'title' ,
                    'id'   => 'rs_cart_page_message_title' ,
                    'desc' => '<h3>' . __( 'Cart page' , SRP_LOCALE ) . '</h3>' ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide Points that can be Earned Message display in Cart Totals Table' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_total_points_cart_field' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_total_points_cart_field' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Points Earned in Order Label in Cart Total Table' , SRP_LOCALE ) ,
                    'id'      => 'rs_total_earned_point_caption' ,
                    'std'     => 'Points that can be earned' ,
                    'default' => 'Points that can be earned' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_total_earned_point_caption' ,
                ) ,
                array(
                    'name'    => __( 'Points that can be Earned Message will display' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_type_for_cart' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => 'Before Cart Total' ,
                        '2' => 'After Cart Total' ,
                    ) ,
                    'newids'  => 'rs_select_type_for_cart' ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide equivalent points in value on Cart Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_equivalent_price_for_points_cart' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_equivalent_price_for_points_cart' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide Points label in Cart Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_custom_msg_for_points_cart' ,
                    'std'     => '2' ,
                    'default' => '2' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_custom_msg_for_points_cart' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Points label in Cart Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_custom_message_for_points_cart' ,
                    'std'     => 'Points' ,
                    'default' => 'Points' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_custom_message_for_points_cart' ,
                ) ,
                array(
                    'type' => 'sectionend' ,
                    'id'   => 'rs_cart_page_message_title'
                ) ,
                array(
                    'type' => 'title' ,
                    'id'   => 'rs_checkout_page_message_title' ,
                    'desc' => '<h3>' . __( 'Checkout page' , SRP_LOCALE ) . '</h3>' ,
                ) ,
                array(
                    'name'    => __( 'Points that can be Earned Message display in Checkout Total Table' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_total_points_checkout_field' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_total_points_checkout_field' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Points Earned in Order Caption in Checkout' , SRP_LOCALE ) ,
                    'id'      => 'rs_total_earned_point_caption_checkout' ,
                    'std'     => 'Points that can be earned' ,
                    'default' => 'Points that can be earned' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_total_earned_point_caption_checkout' ,
                ) ,
                array(
                    'name'    => __( 'Points that can be Earned Message will display' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_type_for_checkout' ,
                    'std'     => '2' ,
                    'default' => '2' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => 'Before Order Total' ,
                        '2' => 'After Order Total' ,
                    ) ,
                    'newids'  => 'rs_select_type_for_checkout' ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide equivalent points in value on Checkout Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_equivalent_price_for_points' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_equivalent_price_for_points' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide Points label in Checkout' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_custom_msg_for_points_checkout' ,
                    'std'     => '2' ,
                    'default' => '2' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_custom_msg_for_points_checkout' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Points label in Checkout Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_custom_message_for_points_checkout' ,
                    'std'     => 'Points' ,
                    'default' => 'Points' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_custom_message_for_points_checkout' ,
                ) ,
                array(
                    'type' => 'sectionend' ,
                    'id'   => 'rs_checkout_page_message_title'
                ) ,
                array(
                    'type' => 'title' ,
                    'id'   => 'rs_thankyou_page_message_title' ,
                    'desc' => '<h3>' . __( 'Thank You Page' , SRP_LOCALE ) . '</h3>' ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide Points that can be Earned Message display in Thank You Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_total_points_order_field' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_total_points_order_field' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Points Earned in Order Thank You Page Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_total_earned_point_caption_thank_you' ,
                    'std'     => 'Points will be added to your account after the order status reached to any of the status [rs_order_status]' ,
                    'default' => 'Points will be added to your account after the order status reached to any of the status [rs_order_status]' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_total_earned_point_caption_thank_you' ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide equivalent points in value on Order Thank You Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_equivalent_price_for_points_thankyou' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_equivalent_price_for_points_thankyou' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide Points label in Thankyou Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_custom_msg_for_points_thankyou' ,
                    'std'     => '2' ,
                    'default' => '2' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_custom_msg_for_points_thankyou' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Points label in Thankyou Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_custom_message_for_points_thankyou' ,
                    'std'     => 'Points' ,
                    'default' => 'Points' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_custom_message_for_points_thankyou' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_thankyou_page_message_title' ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_product_purchase_reward_messages' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( "$section_title" , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_order_setting' ,
                ) ,
                array(
                    'name'   => $option_title ,
                    'id'     => 'rs_enable_msg_for_earned_points' ,
                    'newids' => 'rs_enable_msg_for_earned_points' ,
                    'class'  => 'rs_enable_msg_for_earned_points' ,
                    'type'   => 'checkbox' ,
                ) ,
                array(
                    'name'    => __( 'Message to display Earned Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_msg_for_earned_points' ,
                    'newids'  => 'rs_msg_for_earned_points' ,
                    'class'   => 'rs_msg_for_earned_points' ,
                    'std'     => 'Points Earned in this Order [earnedpoints]' ,
                    'default' => 'Points Earned in this Order [earnedpoints]' ,
                    'type'    => 'textarea' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_order_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields( RSProductPurchaseModule::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSProductPurchaseModule::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_product_purchase_module_checkbox' ] ) ) {
                update_option( 'rs_product_purchase_activated' , $_POST[ 'rs_product_purchase_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_product_purchase_activated' , 'no' ) ;
            }

            if ( isset( $_POST[ 'rs_include_products_for_product_purchase' ] ) ) {
                update_option( 'rs_include_products_for_product_purchase' , $_POST[ 'rs_include_products_for_product_purchase' ] ) ;
            } else {
                update_option( 'rs_include_products_for_product_purchase' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_exclude_products_for_product_purchase' ] ) ) {
                update_option( 'rs_exclude_products_for_product_purchase' , $_POST[ 'rs_exclude_products_for_product_purchase' ] ) ;
            } else {
                update_option( 'rs_exclude_products_for_product_purchase' , '' ) ;
            }
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSProductPurchaseModule::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function reset_product_purchase_module() {
            $settings = RSProductPurchaseModule::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
            update_option( 'rs_earn_point' , '1' ) ;
            update_option( 'rs_earn_point_value' , '1' ) ;
            delete_option( 'rewards_dynamic_rule' ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_product_purchase_activated' ) , 'rs_product_purchase_module_checkbox' , 'rs_product_purchase_activated' ) ;
        }

        public static function rs_save_button_for_update() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row"></th>
                <td class="forminp forminp-select">
                    <input type="submit" class="rs_sumo_reward_button button-primary" value="Save and Update"/>
                </td>
            </tr>
            <?php
        }

        public static function rs_select_products_to_update() {
            $field_id    = "rs_select_particular_products" ;
            $field_label = "Select Particular Products" ;
            $getproducts = get_option( 'rs_select_particular_products' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        public static function rs_include_products_for_product_purchase() {
            $field_id    = "rs_include_products_for_product_purchase" ;
            $field_label = "Include Product(s)" ;
            $getproducts = get_option( 'rs_include_products_for_product_purchase' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        public static function rs_exclude_products_for_product_purchase() {
            $field_id    = "rs_exclude_products_for_product_purchase" ;
            $field_label = "Exclude Product(s)" ;
            $getproducts = get_option( 'rs_exclude_products_for_product_purchase' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        public static function rs_display_save_button() {
            ?>
            <p class="submit sumo_reward_points">
                <?php if ( ! isset( $GLOBALS[ 'hide_save_button' ] ) ) : ?>
                    <input name="save" class="button-primary rs_save_btn" type="submit" value="<?php _e( 'Save changes' , SRP_LOCALE ) ; ?>" />
                <?php endif ; ?>
                <input type="hidden" name="subtab" id="last_tab" />
                <?php wp_nonce_field( 'woocommerce-settings' , '_wpnonce' , true , true ) ; ?>
            </p><?php
        }

        public static function rs_display_reset_button() {
            ?>
            <form method="post" id="mainforms" action="" enctype="multipart/form-data" style="float: left; margin-top: -59px; margin-left: 170px;">
                <input id="resettab" name="reset" class="button-secondary rs_reset" type="submit" value="<?php _e( 'Reset' , SRP_LOCALE ) ; ?>"/>
                <?php wp_nonce_field( 'woocommerce-reset_settings' , '_wpnonce' , true , true ) ; ?>             
            </form>
            <?php
        }

    }

    RSProductPurchaseModule::init() ;
}