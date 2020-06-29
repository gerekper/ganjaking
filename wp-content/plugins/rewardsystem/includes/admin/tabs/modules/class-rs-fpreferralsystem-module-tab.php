<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSReferralSystemModule' ) ) {

    class RSReferralSystemModule {

        public static function init() {

            add_action( 'rs_default_settings_fpreferralsystem' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_rs_settings_tabs_fpreferralsystem' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpreferralsystem' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system

            add_action( 'woocommerce_admin_field_rs_user_role_dynamics_manual' , array( __CLASS__ , 'reward_system_add_manual_table_to_action' ) ) ;

            add_action( 'woocommerce_admin_field_display_referral_reward_log' , array( __CLASS__ , 'rs_list_referral_rewards_log' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_referral_system_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'woocommerce_admin_field_image_uploader' , array( __CLASS__ , 'rs_add_upload_your_facebook_share_image' ) ) ;

            add_action( 'woocommerce_admin_field_selected_products' , array( __CLASS__ , 'rs_select_products_to_update' ) ) ;

            add_action( 'woocommerce_admin_field_rs_select_exclude_user_for_referral_link' , array( __CLASS__ , 'rs_exclude_user_as_hide_referal_link' ) ) ;

            add_action( 'woocommerce_admin_field_rs_hide_bulk_update_for_referral_product_purchase_start' , array( __CLASS__ , 'rs_hide_bulk_update_for_referral_product_purchase_start' ) ) ;

            add_action( 'woocommerce_admin_field_rs_hide_bulk_update_for_referral_product_purchase_end' , array( __CLASS__ , 'rs_hide_bulk_update_for_referral_product_purchase_end' ) ) ;

            add_action( 'woocommerce_admin_field_referral_button' , array( __CLASS__ , 'rs_save_button_for_referral_update' ) ) ;

            add_action( 'woocommerce_admin_field_rs_select_user_for_referral_link' , array( __CLASS__ , 'rs_include_user_as_hide_referal_link' ) ) ;

            add_action( 'woocommerce_admin_field_rs_include_products_for_referral_product_purchase' , array( __CLASS__ , 'rs_include_products_for_referral_product_purchase' ) ) ;

            add_action( 'woocommerce_admin_field_rs_exclude_products_for_referral_product_purchase' , array( __CLASS__ , 'rs_exclude_products_for_referral_product_purchase' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fpreferralsystem' , array( __CLASS__ , 'reset_referral_system_module' ) ) ;

            add_action( 'rs_display_save_button_fpreferralsystem' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpreferralsystem' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;
            global $wp_roles ;
            foreach ( $wp_roles->roles as $values => $key ) {
                $userroleslug[] = $values ;
                $userrolename[] = $key[ 'name' ] ;
            }
            $newcombineduserrole = array_combine( ( array ) $userroleslug , ( array ) $userrolename ) ;
            $categorylist        = fp_product_category() ;
            $newcombinedarray    = fp_order_status() ;
            return apply_filters( 'woocommerce_fpreferralsystem' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Referral System Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_referral_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_referral_system_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_referral_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Link Cookies Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_referral_cookies_settings'
                ) ,
                array(
                    'name'     => __( 'Referral Link Cookies Expires in' , SRP_LOCALE ) ,
                    'id'       => 'rs_referral_cookies_expiry' ,
                    'std'      => '3' ,
                    'default'  => '3' ,
                    'newids'   => 'rs_referral_cookies_expiry' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Minutes' , SRP_LOCALE ) ,
                        '2' => __( 'Hours' , SRP_LOCALE ) ,
                        '3' => __( 'Days' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => false ,
                ) ,
                array(
                    'name'     => __( 'Referral Link Cookies Expiry in Minutes' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter a Fixed Number greater than or equal to 0' , SRP_LOCALE ) ,
                    'id'       => 'rs_referral_cookies_expiry_in_min' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_referral_cookies_expiry_in_min' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Referral Link Cookies Expiry in Hours' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter a Fixed Number greater than or equal to 0' , SRP_LOCALE ) ,
                    'id'       => 'rs_referral_cookies_expiry_in_hours' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_referral_cookies_expiry_in_hours' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Referral Link Cookies Expiry in Days' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter a Fixed Number greater than or equal to 0' , SRP_LOCALE ) ,
                    'id'       => 'rs_referral_cookies_expiry_in_days' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_referral_cookies_expiry_in_days' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Delete Cookies After X Number of Purchase(s)' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to delete cookies after X number of purchase(s)' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_delete_referral_cookie_after_first_purchase' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_delete_referral_cookie_after_first_purchase' ,
                ) ,
                array(
                    'name'     => __( 'Number of Purchase(s)' , SRP_LOCALE ) ,
                    'desc'     => __( 'Number of Purchase(s) in which cookie to be deleted' , SRP_LOCALE ) ,
                    'id'       => 'rs_no_of_purchase' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_no_of_purchase' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_referral_cookies_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Linking Referrals for Life Time Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_life_time_referral' ,
                ) ,
                array(
                    'name'    => __( 'Linking Referrals for Life Time' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to link referrals for life time' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_referral_link_for_life_time' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_referral_link_for_life_time' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_life_time_referral' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Link Limit Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_referral_link_for_specific_limit' ,
                ) ,
                array(
                    'name'    => __( 'Maximum Referral Link Usage' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_referral_link_limit' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_referral_link_limit' ,
                    'desc'    => __( 'Enable this checkbox to restrict referral link usage count' , SRP_LOCALE ) ,
                ) ,
                array(
                    'name'              => __( 'Enter the Value' , SRP_LOCALE ) ,
                    'id'                => 'rs_referral_link_limit' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => '0'
                    ) ,
                    'newids'            => 'rs_referral_link_limit' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_life_time_referral' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Product Purchase Reward Points Global Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_global_referral_reward_points'
                ) ,
                array(
                    'name'     => __( 'Referral Product Purchase Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_enable_product_category_level_for_referral_product_purchase' ,
                    'class'    => 'rs_enable_product_category_level_for_referral_product_purchase' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'type'     => 'radio' ,
                    'newids'   => 'rs_enable_product_category_level_for_referral_product_purchase' ,
                    'options'  => array(
                        'no'  => __( 'Quick Setup (Global Level Settings will be enabled)' , SRP_LOCALE ) ,
                        'yes' => __( 'Advanced Setup (Global,Category and Product Level wil be enabled)' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                    'desc'     => __( 'Quick Setup - Global Level will be enabled<br>Advanced Setup - Global,Category and Product Level wil be enabled' , SRP_LOCALE )
                ) ,
                array(
                    'name'    => __( 'Referral Product Purchase Reward Points is applicable for' , SRP_LOCALE ) ,
                    'id'      => 'rs_referral_product_purchase_global_level_applicable_for' ,
                    'std'     => '1' ,
                    'class'   => 'rs_referral_product_purchase_global_level_applicable_for' ,
                    'default' => '1' ,
                    'newids'  => 'rs_referral_product_purchase_global_level_applicable_for' ,
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
                    'type' => 'rs_include_products_for_referral_product_purchase' ,
                ) ,
                array(
                    'type' => 'rs_exclude_products_for_referral_product_purchase' ,
                ) ,
                array(
                    'name'    => __( 'Include Categories' , SRP_LOCALE ) ,
                    'id'      => 'rs_include_particular_categories_for_referral_product_purchase' ,
                    'css'     => 'min-width:350px;' ,
                    'std'     => '' ,
                    'class'   => 'rs_include_particular_categories_for_referral_product_purchase' ,
                    'default' => '' ,
                    'newids'  => 'rs_include_particular_categories_for_referral_product_purchase' ,
                    'type'    => 'multiselect' ,
                    'options' => $categorylist ,
                ) ,
                array(
                    'name'    => __( 'Exclude Categories' , SRP_LOCALE ) ,
                    'id'      => 'rs_exclude_particular_categories_for_referral_product_purchase' ,
                    'css'     => 'min-width:350px;' ,
                    'std'     => '' ,
                    'class'   => 'rs_exclude_particular_categories_for_referral_product_purchase' ,
                    'default' => '' ,
                    'newids'  => 'rs_exclude_particular_categories_for_referral_product_purchase' ,
                    'type'    => 'multiselect' ,
                    'options' => $categorylist ,
                ) ,
                array(
                    'name'     => __( 'Global Level Referral Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_enable_disable_sumo_referral_reward' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'desc_tip' => true ,
                    'desc'     => __( 'Global Settings will be considered when Product and Category Settings are Enabled and Values are Empty. '
                            . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order.' , SRP_LOCALE ) ,
                    'newids'   => 'rs_global_enable_disable_sumo_referral_reward' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Referral Reward Type' , SRP_LOCALE ) ,
                    'desc'     => __( 'Select Reward Type by Points/Percentage' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_referral_reward_type' ,
                    'class'    => 'show_if_enable_in_referral' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'newids'   => 'rs_global_referral_reward_type' ,
                    'type'     => 'select' ,
                    'desc_tip' => true ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'        => __( 'Referral Reward Points' , SRP_LOCALE ) ,
                    'id'          => 'rs_global_referral_reward_point' ,
                    'class'       => 'show_if_enable_in_referral' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'type'        => 'text' ,
                    'newids'      => 'rs_global_referral_reward_point' ,
                    'placeholder' => '' ,
                    'desc'        => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip'    => true ,
                ) ,
                array(
                    'name'        => __( 'Referral Reward Points in Percent %' , SRP_LOCALE ) ,
                    'id'          => 'rs_global_referral_reward_percent' ,
                    'class'       => 'show_if_enable_in_referral' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'type'        => 'text' ,
                    'newids'      => 'rs_global_referral_reward_percent' ,
                    'placeholder' => '' ,
                    'desc'        => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip'    => true ,
                ) ,
                array(
                    'name'     => __( 'Getting Referred Reward Type' , SRP_LOCALE ) ,
                    'desc'     => __( 'Select Reward Type by Points/Percentage' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_referral_reward_type_refer' ,
                    'class'    => 'show_if_enable_in_referral' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'newids'   => 'rs_global_referral_reward_type_refer' ,
                    'type'     => 'select' ,
                    'desc_tip' => true ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'        => __( 'Reward Points for Getting Referred' , SRP_LOCALE ) ,
                    'id'          => 'rs_global_referral_reward_point_get_refer' ,
                    'class'       => 'show_if_enable_in_referral' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'type'        => 'text' ,
                    'newids'      => 'rs_global_referral_reward_point_get_refer' ,
                    'placeholder' => '' ,
                    'desc'        => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip'    => true ,
                ) ,
                array(
                    'name'     => __( 'Reward Points in Percent % For Getting Referred' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_referral_reward_percent_get_refer' ,
                    'class'    => 'show_if_enable_in_referral' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_referral_reward_percent_get_refer' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_global_referral_reward_points' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_hide_bulk_update_for_referral_product_purchase_start' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Product Purchase Rewards Bulk Update Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_update_setting' ,
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
                    'desc_tip' => true ,
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
                    'name'     => __( 'Enable Referral Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_enable_disable_referral_reward' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'desc_tip' => true ,
                    'desc'     => __( 'Enable will Turn On Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                            . 'Disable will Turn Off Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available.' , SRP_LOCALE ) ,
                    'newids'   => 'rs_local_enable_disable_referral_reward' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Referral Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_referral_reward_type' ,
                    'class'    => 'show_if_enable_in_update_referral' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'newids'   => 'rs_local_referral_reward_type' ,
                    'type'     => 'select' ,
                    'desc_tip' => true ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Referral Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_referral_reward_point' ,
                    'class'    => 'show_if_enable_in_update_referral' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_referral_reward_point' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'        => __( 'Referral Reward Points in Percent %' , SRP_LOCALE ) ,
                    'id'          => 'rs_local_referral_reward_percent' ,
                    'class'       => 'show_if_enable_in_update_referral' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'type'        => 'text' ,
                    'newids'      => 'rs_local_referral_reward_percent' ,
                    'placeholder' => '' ,
                    'desc'        => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip'    => true ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Product Purchase Referral Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Product Purchase Referral Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_pdt_purchase_referral' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_pdt_purchase_referral' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Product Purchase Referral Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_pdt_purchase_referral' ,
                    'std'     => 'Product Purchase Referral - Notification' ,
                    'default' => 'Product Purchase Referral - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_pdt_purchase_referral' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Product Purchase Referral Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_pdt_purchase_referral' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_pdt_purchase_referral' ,
                ) ,
                array(
                    'name'     => __( 'Getting Referred Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_referral_reward_type_get_refer' ,
                    'class'    => 'show_if_enable_in_update_referral' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'newids'   => 'rs_local_referral_reward_type_get_refer' ,
                    'type'     => 'select' ,
                    'desc_tip' => true ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'        => __( 'Referral Reward Points for Getting Referred' , SRP_LOCALE ) ,
                    'desc'        => __( 'Please Enter Referral Reward Points for getting referred' , SRP_LOCALE ) ,
                    'id'          => 'rs_local_referral_reward_point_for_getting_referred' ,
                    'class'       => 'show_if_enable_in_update_referral' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'type'        => 'text' ,
                    'newids'      => 'rs_local_referral_reward_point_for_getting_referred' ,
                    'placeholder' => '' ,
                    'desc'        => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip'    => true ,
                ) ,
                array(
                    'name'        => __( 'Referral Reward Points in Percent % for Getting Referred' , SRP_LOCALE ) ,
                    'desc'        => __( 'Please Enter Percentage value of Reward Points for getting referred' , SRP_LOCALE ) ,
                    'id'          => 'rs_local_referral_reward_percent_for_getting_referred' ,
                    'class'       => 'show_if_enable_in_update_referral' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'type'        => 'text' ,
                    'newids'      => 'rs_local_referral_reward_percent_for_getting_referred' ,
                    'placeholder' => '' ,
                    'desc'        => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip'    => true ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Product Purchase Getting Referred Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Product Purchase Getting Referred Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_pdt_purchase_referrer' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_pdt_purchase_referrer' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Product Purchase Getting Referred Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_pdt_purchase_referrer' ,
                    'std'     => 'Product Purchase Getting Referred - Noification' ,
                    'default' => 'Product Purchase Getting Referred - Noification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_pdt_purchase_referrer' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Product Purchase Getting Referred Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_pdt_purchase_referrer' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_pdt_purchase_referrer' ,
                ) ,
                array(
                    'type' => 'referral_button' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_update_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_hide_bulk_update_for_referral_product_purchase_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Product Purchase Reward Points by Guest Users' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_referrer_earn_point_by_guest_users' ,
                ) ,
                array(
                    'name'    => __( 'Referral Product Purchase Reward Points by Guest Users' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => 'rs_referrer_earn_point_purchase_by_guest_users' ,
                    'newids'  => 'rs_referrer_earn_point_purchase_by_guest_users' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'desc'    => __( 'By enabling this checkbox, you can allow referrer to earn referral product purchase points by the guest user(s)' , SRP_LOCALE )
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_referrer_earn_point_through_guest_users' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Sign up Reward Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_referral_action_setting' ,
                ) ,
                array(
                    'name'    => __( 'Enable Referral Signup Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option for Referral Signup Reward Points' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => '_rs_referral_enable_signups' ,
                    'newids'  => '_rs_referral_enable_signups' ,
                    'std'     => 'yes' ,
                    'default' => 'yes' ,
                ) ,
                array(
                    'name'     => __( 'Referral Account Sign up Reward Points is Awarded ' , SRP_LOCALE ) ,
                    'desc'     => __( 'Select Referral Reward Account Sign up Points Reward type ' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_referral_points_award' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_select_referral_points_award' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'options'  => array(
                        '1' => __( 'Instantly' , SRP_LOCALE ) ,
                        '2' => __( 'After Referral Places Minimum Number of Successful Order(s)' , SRP_LOCALE ) ,
                        '3' => __( 'After Referral Spents the Minimum Amount in Site' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Number of Successful Order(s)' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter the Minimum Number Of Sucessful Orders' , SRP_LOCALE ) ,
                    'id'       => 'rs_number_of_order_for_referral_points' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_number_of_order_for_referral_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Minimum Amount to be Spent by the User' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter the Minimum Amount Spent by User' , SRP_LOCALE ) ,
                    'id'       => 'rs_amount_of_order_for_referral_points' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_amount_of_order_for_referral_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Account Sign up Referral Reward Points after First Purchase' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to award referral reward points for account signup after first purchase' , SRP_LOCALE ) ,
                    'id'      => 'rs_referral_reward_signup_after_first_purchase' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_referral_reward_signup_after_first_purchase' ,
                ) ,
                array(
                    'name'     => __( 'Referral Reward Points for Account Sign up' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter the Referral Reward Points that will be earned for Account Sign up' , SRP_LOCALE ) ,
                    'id'       => 'rs_referral_reward_signup' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_referral_reward_signup' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Referral Account Signup Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Referral Account Signup Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_referral_signup' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_referral_signup' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Referral Account Signup Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_referral_signup' ,
                    'std'     => 'ReferralAccount Signup - Notification' ,
                    'default' => 'ReferralAccount Signup - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_referral_signup' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Referral Account Signup Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_referral_signup' ,
                    'std'     => 'You have earned [rs_earned_points] points for referred [rs_user_name] and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points for referred [rs_user_name] and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_referral_signup' ,
                ) ,
                array(
                    'name'     => __( 'Enable Reward Points for Getting Referred' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enable the Reward Points that will be earned for Getting Referred' , SRP_LOCALE ) ,
                    'id'       => 'rs_referral_reward_signup_getting_refer' ,
                    'std'      => '2' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_referral_reward_signup_getting_refer' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Enable Reward Points for Getting Referred after first purchase' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable the Reward Points that will be earned for Getting Referred after first purchase' , SRP_LOCALE ) ,
                    'id'      => 'rs_referral_reward_getting_refer_after_first_purchase' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_referral_reward_getting_refer_after_first_purchase' ,
                ) ,
                array(
                    'name'     => __( 'Reward Points for Getting Referred' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter the Reward Points that will be earned for Getting Referred' , SRP_LOCALE ) ,
                    'id'       => 'rs_referral_reward_getting_refer' ,
                    'std'      => '1000' ,
                    'default'  => '1000' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_referral_reward_getting_refer' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Getting Referred Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Getting Referred Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_getting_referred' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_getting_referred' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Getting Referred Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_getting_referred' ,
                    'std'     => 'Getting Referred - Notification' ,
                    'default' => 'Getting Referred - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_getting_referred' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Getting Referred Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_getting_referred' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_getting_referred' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_referral_action_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Generate Referral Link Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_my_generate_referral_settings'
                ) ,
                array(
                    'name'    => __( 'Generate Referral Link' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_generate_referral' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_generate_referral' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Referral System of SUMO Reward Points is accessible by' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_type_of_user_for_referral' ,
                    'css'      => 'min-width:100px;' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'All Users' , SRP_LOCALE ) ,
                        '2' => __( 'Include User(s)' , SRP_LOCALE ) ,
                        '3' => __( 'Exclude User(s)' , SRP_LOCALE ) ,
                        '4' => __( 'Include User Role(s)' , SRP_LOCALE ) ,
                        '5' => __( 'Exclude User Role(s)' , SRP_LOCALE ) ,
                    ) ,
                    'newids'   => 'rs_select_type_of_user_for_referral' ,
                    'desc'     => __( 'Referral System includes Referral Table,Refer A Friend Form and Generate Referral Link' , SRP_LOCALE ) ,
                    'desc_tip' => true
                ) ,
                array(
                    'type' => 'rs_select_user_for_referral_link' ,
                ) ,
                array(
                    'type' => 'rs_select_exclude_user_for_referral_link' ,
                ) ,
                array(
                    'name'        => __( 'Select the User Role for Providing access to Referral System' , SRP_LOCALE ) ,
                    'id'          => 'rs_select_users_role_for_show_referral_link' ,
                    'css'         => 'min-width:343px;' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'placeholder' => 'Select for a User Role' ,
                    'type'        => 'multiselect' ,
                    'options'     => $newcombineduserrole ,
                    'newids'      => 'rs_select_users_role_for_show_referral_link' ,
                    'desc_tip'    => false ,
                ) ,
                array(
                    'name'        => __( 'Select the User Role for Preventing access to Referral System' , SRP_LOCALE ) ,
                    'id'          => 'rs_select_exclude_users_role_for_show_referral_link' ,
                    'css'         => 'min-width:343px;' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'placeholder' => 'Select for a User Role' ,
                    'type'        => 'multiselect' ,
                    'options'     => $newcombineduserrole ,
                    'newids'      => 'rs_select_exclude_users_role_for_show_referral_link' ,
                    'desc_tip'    => false ,
                ) ,
                array(
                    'name'     => __( 'Fallback Message for Referral Restriction' , SRP_LOCALE ) ,
                    'id'       => 'rs_display_msg_when_access_is_prevented' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_display_msg_when_access_is_prevented' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Fallback Message for Referral Restriction' , SRP_LOCALE ) ,
                    'id'      => 'rs_msg_for_restricted_user' ,
                    'std'     => 'Referral System is currently restricted for your account' ,
                    'default' => 'Referral System is currently restricted for your account' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_msg_for_restricted_user' ,
                ) ,
                array(
                    'name'    => __( 'Referral System Restriction based on Purchase History' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_referral_link_generate_after_first_order' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_referral_link_generate_after_first_order' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this option, you can restrict the users to participate in the Referral System' , SRP_LOCALE ) ,
                ) ,
                array(
                    'name'     => __( 'Restrict Referral System based on' , SRP_LOCALE ) ,
                    'id'       => 'rs_referral_link_generated_settings' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'newids'   => 'rs_referral_link_generated_settings' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Number of Successful Order(s)' , SRP_LOCALE ) ,
                        '2' => __( 'Total Amount Spent on the site' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'              => __( 'Enter the number of orders' , SRP_LOCALE ) ,
                    'id'                => 'rs_getting_number_of_orders' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'newids'            => 'rs_getting_number_of_orders' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => 0
                    ) ,
                    'desc_tip'          => true ,
                ) ,
                array(
                    'name'              => __( 'Enter the value' , SRP_LOCALE ) ,
                    'id'                => 'rs_number_of_amount_spent' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'newids'            => 'rs_number_of_amount_spent' ,
                    'type'              => 'number' ,
                    'desc_tip'          => true ,
                    'custom_attributes' => array(
                        'min' => 0
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Referral System can accessible only when the order status reaches' , SRP_LOCALE ) ,
                    'id'      => 'rs_set_order_status_for_generate_link' ,
                    'std'     => array( 'completed' ) ,
                    'default' => array( 'completed' ) ,
                    'type'    => 'multiselect' ,
                    'class'   => 'wc-enhanced-select' ,
                    'options' => $newcombinedarray ,
                    'newids'  => 'rs_set_order_status_for_generate_link' ,
                ) ,
                array(
                    'name'    => __( 'Generate Referral Link Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_generate_link_label' ,
                    'std'     => 'Generate Referral Link' ,
                    'default' => 'Generate Referral Link' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_generate_link_label' ,
                ) ,
                array(
                    'name'    => __( 'S.No Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_generate_link_sno_label' ,
                    'std'     => 'S.No' ,
                    'default' => 'S.No' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_generate_link_sno_label' ,
                ) ,
                array(
                    'name'    => __( 'Date Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_generate_link_date_label' ,
                    'std'     => 'Date' ,
                    'default' => 'Date' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_generate_link_date_label' ,
                ) ,
                array(
                    'name'    => __( 'Referral Link Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_generate_link_referrallink_label' ,
                    'std'     => 'Referral Link' ,
                    'default' => 'Referral Link' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_generate_link_referrallink_label' ,
                ) ,
                array(
                    'name'    => __( 'Social Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_generate_link_social_label' ,
                    'std'     => 'Social' ,
                    'default' => 'Social' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_generate_link_social_label' ,
                ) ,
                array(
                    'name'    => __( 'Action Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_generate_link_action_label' ,
                    'std'     => 'Action' ,
                    'default' => 'Action' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_generate_link_action_label' ,
                ) ,
                array(
                    'name'    => __( 'Referral Link Hover Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_generate_link_hover_label' ,
                    'std'     => 'Click this button to generate the referral link' ,
                    'default' => 'Click this button to generate the referral link' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_generate_link_hover_label' ,
                ) ,
                array(
                    'name'    => __( 'Generate Referral Link Button Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_generate_link_button_label' ,
                    'std'     => 'Generate Referral Link' ,
                    'default' => 'Generate Referral Link' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_generate_link_button_label' ,
                ) ,
                array(
                    'name'     => __( 'Generate Referral Link based on Username/User ID' , SRP_LOCALE ) ,
                    'id'       => 'rs_generate_referral_link_based_on_user' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'newids'   => 'rs_generate_referral_link_based_on_user' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Username' , SRP_LOCALE ) ,
                        '2' => __( 'User ID' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Copy Referral Link' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_copy_to_clipboard' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_copy_to_clipboard' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this checkbox, users will have the option to copy the referral link' , SRP_LOCALE ) ,
                ) ,
                array(
                    'name'    => __( 'Type of Referral Link to be displayed' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_generate_referral_link_type' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_generate_referral_link_type' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Default' , SRP_LOCALE ) ,
                        '2' => __( 'Static Url' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Prefill Generate Referral Link' , SRP_LOCALE ) ,
                    'id'      => 'rs_prefill_generate_link' ,
                    'std'     => site_url() ,
                    'default' => site_url() ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_prefill_generate_link' ,
                ) ,
                array(
                    'name'    => __( 'My Referral Link Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_my_referral_link_button_label' ,
                    'std'     => 'My Referral Link' ,
                    'default' => 'My Referral Link' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_my_referral_link_button_label' ,
                ) ,
                array(
                    'name'    => __( 'Static Referral Link' , SRP_LOCALE ) ,
                    'id'      => 'rs_static_generate_link' ,
                    'std'     => site_url() ,
                    'default' => site_url() ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_static_generate_link' ,
                ) ,
                array(
                    'name'    => __( 'Referral Link Table Position' , SRP_LOCALE ) ,
                    'id'      => 'rs_display_generate_referral' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_display_generate_referral' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Before My Account ' , SRP_LOCALE ) ,
                        '2' => __( 'After My Account' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Extra Class Name for Generate Referral Link Button' , SRP_LOCALE ) ,
                    'desc'     => __( 'Add Extra Class Name to the My Account Generate Referral Link Button, Don\'t Enter dot(.) before Class Name' , SRP_LOCALE ) ,
                    'id'       => 'rs_extra_class_name_generate_referral_link' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_extra_class_name_generate_referral_link' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_account_show_hide_facebook_share_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_account_show_hide_facebook_share_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Title used for Facebook Share' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the title of website that shown in Facebook Share' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'id'       => 'rs_facebook_title' ,
                    'std'      => get_bloginfo() ,
                    'default'  => get_bloginfo() ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Description used for Facebook Share' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the description of website that shown in Facebook Share' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'id'       => 'rs_facebook_description' ,
                    'std'      => get_option( 'blogdescription' ) ,
                    'default'  => get_option( 'blogdescription' ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'type' => 'image_uploader' ,
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_account_show_hide_twitter_tweet_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_account_show_hide_twitter_tweet_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Google+1 Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_acount_show_hide_google_plus_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_acount_show_hide_google_plus_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_my_generate_referral_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Link Settings For Shortcode' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'desc' => '' ,
                    'id'   => 'rs_referral_link_short_code' ,
                ) ,
                array(
                    'name'     => __( 'Static Referral Link' , SRP_LOCALE ) ,
                    'id'       => '_rs_static_referral_link' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'newids'   => '_rs_static_referral_link' ,
                    'type'     => 'select' ,
                    'desc_tip' => false ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_referral_link_short_code' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referrer Earning Restriction Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'desc' => __( 'For eg: If A Refers B then A is the Referrer and B is the Referral' , SRP_LOCALE ) ,
                    'id'   => '_rs_ban_referee_points_time' ,
                ) ,
                array(
                    'name'     => __( 'Referrer should earn points only after the user(Buyer or Referral) is X days old' , SRP_LOCALE ) ,
                    'id'       => '_rs_select_referral_points_referee_time' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'newids'   => '_rs_select_referral_points_referee_time' ,
                    'type'     => 'select' ,
                    'desc_tip' => false ,
                    'options'  => array(
                        '1' => __( 'Unlimited' , SRP_LOCALE ) ,
                        '2' => __( 'Limited' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Number of Day(s)' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Fixed Number greater than or equal to 0' , SRP_LOCALE ) ,
                    'id'       => '_rs_select_referral_points_referee_time_content' ,
                    'newids'   => '_rs_select_referral_points_referee_time_content' ,
                    'type'     => 'text' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'If the Referred Person\'s account is deleted, the Referral Points' , SRP_LOCALE ) ,
                    'id'       => '_rs_reward_referal_point_user_deleted' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'newids'   => '_rs_reward_referal_point_user_deleted' ,
                    'type'     => 'select' ,
                    'desc_tip' => false ,
                    'options'  => array(
                        '1' => __( 'Should be Revoked' , SRP_LOCALE ) ,
                        '2' => __( 'Shouldn\'t be Revoked' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Applies for Referral account created' , SRP_LOCALE ) ,
                    'id'       => '_rs_time_validity_to_redeem' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'newids'   => '_rs_time_validity_to_redeem' ,
                    'type'     => 'select' ,
                    'desc_tip' => false ,
                    'options'  => array(
                        '1' => __( 'Any time' , SRP_LOCALE ) ,
                        '2' => __( 'Within specific number of days' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Number of Day(s)' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Fixed Number greater than or equal to 0' , SRP_LOCALE ) ,
                    'id'       => '_rs_days_for_redeeming_points' ,
                    'newids'   => '_rs_days_for_redeeming_points' ,
                    'type'     => 'text' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Restrict Referral Product Purchase Reward Points when more than one quantity of the product is purchased by the Referred Person' , SRP_LOCALE ) ,
                    'id'      => 'rs_restrict_referral_reward' ,
                    'desc'    => __( 'By enabling this option, one quantity of the points will be awarded to referrer if referred person purchase more than one quantity of the product' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_restrict_referral_reward' ,
                ) ,
                array(
                    'name'    => __( 'Restrict Referral Product Purchase Reward Points when more than one Referrer refer same Referral' , SRP_LOCALE ) ,
                    'id'      => 'rs_restrict_referral_points_for_multiple_referrer' ,
                    'desc'    => __( 'By enabling this option, the referral points will be awarded only for first referrer when multiple referrer refer same referral' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_restrict_referral_points_for_multiple_referrer' ,
                ) ,
                array(
                    'name'    => __( 'Restrict Referral Product Purchase Reward Points when Referrer and Referral IP is same' , SRP_LOCALE ) ,
                    'id'      => 'rs_restrict_referral_points_for_same_ip' ,
                    'desc'    => __( 'By enabling this option, the referral points will not be awarded when referrer and referral IP is same' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_restrict_referral_points_for_same_ip' ,
                ) ,
                array(
                    'name'    => __( 'Calculate Referral Reward Points after Discounts(WooCommerce Coupons / Points Redeeming)' , SRP_LOCALE ) ,
                    'id'      => 'rs_referral_points_after_discounts' ,
                    'desc'    => __( 'Enabling this option will calculate referral reward points for the price after excluding the coupon/ points redeeming discounts ' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_referral_points_after_discounts' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_ban_referee_points_time' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referrer Label Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_referrer_label_settings'
                ) ,
                array(
                    'name'    => __( 'To display the Message to Referral Person' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_generate_referral_message' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_generate_referral_message' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Select to Send Message by ' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_message_by_referrer' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => 'Referrer User Name' ,
                        '2' => 'Referrer First Name' ,
                    ) ,
                    'newids'  => 'rs_send_message_by_referrer' ,
                ) ,
                array(
                    'name'    => __( 'Message to display the Referral Person' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_generate_referral_message_text' ,
                    'std'     => 'You are being referred by [rs_referrer_name]' ,
                    'default' => 'You are being referred by [rs_referrer_name]' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_show_hide_generate_referral_message_text' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_referrer_label_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'My Referral Table Label Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_my_referal_label_settings'
                ) ,
                array(
                    'name'    => __( 'Referral Table ' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_referal_table' ,
                    'std'     => '2' ,
                    'default' => '2' ,
                    'default' => '2' ,
                    'newids'  => 'rs_show_hide_referal_table' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Referral Table Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Referral Table Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_referal_table_title' ,
                    'std'      => 'Referral Table' ,
                    'default'  => 'Referral Table' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_referal_table_title' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'S.No Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Serial Number Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_referal_sno_label' ,
                    'std'      => 'S.No' ,
                    'default'  => 'S.No' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_referal_sno_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Select Referral Option for ' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_option_for_referral' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Username' ,
                        '2' => 'Email ID' ,
                    ) ,
                    'newids'   => 'rs_select_option_for_referral' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Referral Username Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Referral Username Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_referal_userid_label' ,
                    'std'      => 'Username' ,
                    'default'  => 'Username' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_referal_userid_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Referral Email Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Referral Email label' , SRP_LOCALE ) ,
                    'id'       => 'rs_referral_email_ids' ,
                    'std'      => 'Email ID' ,
                    'default'  => 'Email ID' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_referral_email_ids' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Total Referral Points Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Total Referral Points Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_total_referal_points_label' ,
                    'std'      => 'Total Referral Points' ,
                    'default'  => 'Total Referral Points' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_total_referal_points_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Referral Table - Shortcode' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_referal_table_shortcode' ,
                    'std'     => '2' ,
                    'default' => '2' ,
                    'default' => '2' ,
                    'newids'  => 'rs_show_hide_referal_table_shortcode' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Referral Table Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Referral Table Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_referal_table_title_shortcode' ,
                    'std'      => 'Referral Table' ,
                    'default'  => 'Referral Table' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_referal_table_title_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'S.No Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Serial Number Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_referal_sno_label_shortcode' ,
                    'std'      => 'S.No' ,
                    'default'  => 'S.No' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_referal_sno_label_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Select Referral Option for ' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_option_for_referral_shortcode' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Username' ,
                        '2' => 'Email ID' ,
                    ) ,
                    'newids'   => 'rs_select_option_for_referral_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Referral Username Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Referral Username Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_referal_userid_label_shortcode' ,
                    'std'      => 'Username' ,
                    'default'  => 'Username' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_referal_userid_label_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Referral Email Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Referral Email label' , SRP_LOCALE ) ,
                    'id'       => 'rs_referral_email_ids_shortcode' ,
                    'std'      => 'Email ID' ,
                    'default'  => 'Email ID' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_referral_email_ids_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Total Referral Points Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Total Referral Points Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_total_referal_points_label_shortcode' ,
                    'std'      => 'Total Referral Points' ,
                    'default'  => 'Total Referral Points' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_total_referal_points_label_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_my_referal_label_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Refer a Friend Form Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_referfriend_status'
                ) ,
                array(
                    'name'    => __( 'Enable Friend Form Settings' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_message_for_friend_form' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_enable_message_for_friend_form' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Friend Name Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Friend Name Label which will be available in Frontend when you use shortcode' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_name_label' ,
                    'std'      => 'Your Friend Name' ,
                    'default'  => 'Your Friend Name' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_name_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Friend Name Field Placeholder' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Friend Name Field Placeholder which will be appear in frontend when you use shortcode' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_name_placeholder' ,
                    'std'      => 'Enter your Friend Name' ,
                    'default'  => 'Enter your Friend Name' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_name_placeholder' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Friend Email Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Friend Email Label which will be available in Frontend when you use shortcode' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_email_label' ,
                    'std'      => 'Your Friend Email' ,
                    'default'  => 'Your Friend Email' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_email_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Friend Email Field Placeholder' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Friend Email Field Placeholder which will be appear in frontend when you use shortcode' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_email_placeholder' ,
                    'std'      => 'Enter your Friend Email' ,
                    'default'  => 'Enter your Friend Email' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_email_placeholder' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Friend Email Subject Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Friend Subject which will be appear in Frontend when you use shortcode' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_subject_label' ,
                    'std'      => 'Your Subject' ,
                    'default'  => 'Your Subject' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_subject_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Friend Email Subject Field Placeholder' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Friend Email Subject Field Placeholder which will be appear in frontend when you use shortcode' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_email_subject_placeholder' ,
                    'std'      => 'Enter your Subject' ,
                    'default'  => 'Enter your Subject' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_email_subject_placeholder' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Friend Email Message Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Friend Email Message which will be appear in frontend when you use shortcode' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_message_label' ,
                    'std'      => 'Your Message' ,
                    'default'  => 'Your Message' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_message_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Friend Email Message Field Placeholder' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Friend Email Message Field Placeholder which will be appear in frontend when you use shortcode' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_email_message_placeholder' ,
                    'std'      => 'Enter your Message' ,
                    'default'  => 'Enter your Message' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_email_message_placeholder' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( ' Email Subject Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_allow_user_to_request_prefilled_subject' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_allow_user_to_request_prefilled_subject' ,
                    'options'  => array(
                        '1' => __( 'Editable' , SRP_LOCALE ) ,
                        '2' => __( 'Non-Editable' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Prefilled Subject Text' , SRP_LOCALE ) ,
                    'desc'     => __( 'This Message will be displayed in the Subject field' , SRP_LOCALE ) ,
                    'id'       => 'rs_subject_field' ,
                    'std'      => 'Referral Link' ,
                    'default'  => 'Referral Link' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_subject_field' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Prefilled Heading Text' , SRP_LOCALE ) ,
                    'desc'     => __( 'This Message will be displayed in the Heading field' , SRP_LOCALE ) ,
                    'id'       => 'rs_heading_field' ,
                    'std'      => 'Referral Link' ,
                    'default'  => 'Referral Link' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_heading_field' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Allow User to Enter the Prefilled Message for Refer a Friend' , SRP_LOCALE ) ,
                    'id'       => 'rs_allow_user_to_request_prefilled_message' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_allow_user_to_request_prefilled_message' ,
                    'options'  => array(
                        '1' => __( 'Editable' , SRP_LOCALE ) ,
                        '2' => __( 'Non-Editable' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Enter Referral Link for Refer a Friend Form' , SRP_LOCALE ) ,
                    'id'      => 'rs_referral_link_refer_a_friend_form' ,
                    'std'     => site_url() ,
                    'default' => site_url() ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_referral_link_refer_a_friend_form' ,
                ) ,
                array(
                    'name'     => __( 'Prefilled Message for Refer a Friend' , SRP_LOCALE ) ,
                    'desc'     => __( 'This Message will be displayed in the Message field along with the Referral link' , SRP_LOCALE ) ,
                    'id'       => 'rs_friend_referral_link' ,
                    'std'      => 'Hi [rs_your_friend_name],' . '<br>'
                    . 'You can Customize your message here.[site_referral_url] [rs_user_name]' . '<br>' . 'Referrer First Name : [rs_referrer_first_name]' . '<br>' . ' Referrer Last Name : [rs_referrer_last_name]' . '<br>' . 'Referrer Email ID : [rs_referrer_email_id]' ,
                    'default'  => 'Hi [rs_your_friend_name],' . '<br>'
                    . 'You can Customize your message here.[site_referral_url] [rs_user_name]' . '<br>' . 'Referrer First Name : [rs_referrer_first_name]' . '<br>' . ' Referrer Last Name : [rs_referrer_last_name]' . '<br>' . 'Referrer Email ID : [rs_referrer_email_id]' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_friend_referral_link' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide I agree to the Terms and Condition Field' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_iagree_termsandcondition_field' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_iagree_termsandcondition_field' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Hide' , SRP_LOCALE ) ,
                        '2' => __( 'Show' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'I Agree Field Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'This Caption will be displayed for the I agree field in Refer a Friend Form' , SRP_LOCALE ) ,
                    'id'       => 'rs_refer_friend_iagreecaption_link' ,
                    'std'      => 'I agree to the {termsandconditions}' ,
                    'default'  => 'I agree to the {termsandconditions}' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_refer_friend_iagreecaption_link' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Terms and Conditions Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'This Caption will be displayed for terms and condition' , SRP_LOCALE ) ,
                    'id'       => 'rs_refer_friend_termscondition_caption' ,
                    'std'      => 'Terms and Conditions' ,
                    'default'  => 'Terms and Conditions' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_refer_friend_termscondition_caption' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Terms and Conditions URL' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the URL for Terms and Conditions' , SRP_LOCALE ) ,
                    'id'       => 'rs_refer_friend_termscondition_url' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_refer_friend_termscondition_url' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_referfriend_status' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Error Message Settings for Refer a Friend Form' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_referfriend_error_settings'
                ) ,
                array(
                    'name'     => __( 'Error Message to display when Friend Name Field is left empty' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Friend Name is Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_name_error_message' ,
                    'std'      => 'Please Enter your Friend Name' ,
                    'default'  => 'Please Enter your Friend Name' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_name_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when Friend Email Field is left empty' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Friend Email is Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_email_error_message' ,
                    'std'      => 'Please Enter your Friend Email' ,
                    'default'  => 'Please Enter your Friend Email' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_email_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when Email format is not valid' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Friend Email is not Valid' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_friend_email_is_not_valid' ,
                    'std'      => 'Enter Email is not Valid' ,
                    'default'  => 'Enter Email is not Valid' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_friend_email_is_not_valid' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when Email Subject is left empty' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Email Subject is Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_email_subject_error_message' ,
                    'std'      => 'Email Subject should not be left blank' ,
                    'default'  => 'Email Subject should not be left blank' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_email_subject_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when Email Message is left empty' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter your Error Message which will be appear in frontend if the Email Message is Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_rewards_email_message_error_message' ,
                    'std'      => 'Please Enter your Message' ,
                    'default'  => 'Please Enter your Message' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_rewards_email_message_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when I agree checkbox is unchecked' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter your Error Message which will be appear in frontend if i agree is unchecked' , SRP_LOCALE ) ,
                    'id'       => 'rs_iagree_error_message' ,
                    'std'      => 'Please Accept our Terms and Condition' ,
                    'default'  => 'Please Accept our Terms and Condition' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_iagree_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_referfriend_error_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Manual Referral Link Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_manual_setting'
                ) ,
                array(
                    'type' => 'rs_user_role_dynamics_manual' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_manual_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Referral Reward Table' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_referral_setting' ,
                ) ,
                array(
                    'type' => 'display_referral_reward_log' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_referral_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Shortcodes used in Refer a Friend' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_shortcodes_in_refer_a_friend' ,
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<b>[site_referral_url]</b> - To display referrer url<br><br>'
                    . '<b>[rs_user_name]</b> - To display current user name<br><br>'
                    . '<b>[rs_referrer_name]</b> - To display referrer name<br><br>'
                    . '<b>[rs_referrer_first_name]</b> - To display referrer first name<br><br>'
                    . '<b>[rs_referrer_last_name]</b> - To display referrer last name<br><br>'
                    . '<b>[rs_referrer_email_id]</b> - To display referrer email<br><br>'
                    . '<b>{termsandconditions}</b> - To display the link for terms and conditions' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_shortcodes_in_refer_a_friend' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields( RSReferralSystemModule::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSReferralSystemModule::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_select_exclude_users_list_for_show_referral_link' ] ) ) {
                update_option( 'rs_select_exclude_users_list_for_show_referral_link' , $_POST[ 'rs_select_exclude_users_list_for_show_referral_link' ] ) ;
            } else {
                update_option( 'rs_select_exclude_users_list_for_show_referral_link' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_select_include_users_for_show_referral_link' ] ) ) {
                update_option( 'rs_select_include_users_for_show_referral_link' , $_POST[ 'rs_select_include_users_for_show_referral_link' ] ) ;
            } else {
                update_option( 'rs_select_include_users_for_show_referral_link' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_fbshare_image_url_upload' ] ) ) {
                update_option( 'rs_fbshare_image_url_upload' , $_POST[ 'rs_fbshare_image_url_upload' ] ) ;
            } else {
                update_option( 'rs_fbshare_image_url_upload' , '' ) ;
            }
            if ( isset( $_POST[ 'rewards_dynamic_rule_manual' ] ) ) {
                $reward_dynamic_rule_manual = array_values( $_POST[ 'rewards_dynamic_rule_manual' ] ) ;
                update_option( 'rewards_dynamic_rule_manual' , $reward_dynamic_rule_manual ) ;
            }
            if ( isset( $_POST[ 'rs_referral_module_checkbox' ] ) ) {
                update_option( 'rs_referral_activated' , $_POST[ 'rs_referral_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_referral_activated' , 'no' ) ;
            }
            if ( isset( $_POST[ 'rs_include_products_for_referral_product_purchase' ] ) ) {
                update_option( 'rs_include_products_for_referral_product_purchase' , $_POST[ 'rs_include_products_for_referral_product_purchase' ] ) ;
            } else {
                update_option( 'rs_include_products_for_referral_product_purchase' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_exclude_products_for_referral_product_purchase' ] ) ) {
                update_option( 'rs_exclude_products_for_referral_product_purchase' , $_POST[ 'rs_exclude_products_for_referral_product_purchase' ] ) ;
            } else {
                update_option( 'rs_exclude_products_for_referral_product_purchase' , '' ) ;
            }
            if ( isset( $_POST[ 'rewards_dynamic_rule_manual' ] ) ) {
                update_option( 'rewards_dynamic_rule_manual' , $_POST[ 'rewards_dynamic_rule_manual' ] ) ;
            } else {
                update_option( 'rewards_dynamic_rule_manual' , '' ) ;
            }
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSReferralSystemModule::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function rs_save_button_for_referral_update() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">                    
                </th>
                <td class="forminp forminp-select">
                    <input type="submit" class="rs_sumo_reward_button button-primary" value="Save and Update"/>
                </td>
            </tr>
            <?php
        }

        public static function rs_hide_bulk_update_for_referral_product_purchase_start() {
            ?>
            <div class="rs_hide_bulk_update_for_referral_product_purchase_start">
                <?php
            }

            public static function rs_hide_bulk_update_for_referral_product_purchase_end() {
                ?>
            </div>
            <?php
        }

        public static function reset_referral_system_module() {
            $settings = RSReferralSystemModule::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
            delete_option( 'rewards_dynamic_rule_manual' ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_referral_activated' ) , 'rs_referral_module_checkbox' , 'rs_referral_activated' ) ;
        }

        public static function rs_select_products_to_update() {
            $field_id    = "rs_select_particular_products" ;
            $field_label = "Select Particular Products" ;
            $getproducts = get_option( 'rs_select_particular_products' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        public static function rs_exclude_user_as_hide_referal_link() {
            $field_id    = "rs_select_exclude_users_list_for_show_referral_link" ;
            $field_label = "Select the Users for Preventing access to Referral System" ;
            $getuser     = get_option( 'rs_select_exclude_users_list_for_show_referral_link' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function rs_include_user_as_hide_referal_link() {
            $field_id    = "rs_select_include_users_for_show_referral_link" ;
            $field_label = "Select the Users for Providing access to Referral System" ;
            $getuser     = get_option( 'rs_select_include_users_for_show_referral_link' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function reward_system_add_manual_table_to_action() {
            global $woocommerce ;
            wp_nonce_field( plugin_basename( __FILE__ ) , 'rsdynamicrulecreation_manual' ) ;
            global $woocommerce ;
            ?>
            <style type="text/css">
                .rs_manual_linking_referral{
                    width:60%;
                }
                .rs_manual_linking_referer{
                    width:60%;
                }
                .column-columnname-link{
                    width:10%;               
                }            

            </style>
            <?php
            echo rs_common_ajax_function_to_select_user( 'rs_manual_linking_referer' ) ;
            echo rs_common_ajax_function_to_select_user( 'rs_manual_linking_referral' ) ;
            ?>
            <table class="widefat fixed rsdynamicrulecreation_manual" cellspacing="0">
                <thead>
                    <tr>

                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Referrer Username' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Buyer Username' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname-link" scope="col"><?php _e( 'Linking Type' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Linking' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </thead>

                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="manage-column column-columnname num" scope="col"> <span class="add button-primary"><?php _e( 'Add Linking' , SRP_LOCALE ) ; ?></span></td>
                    </tr>
                    <tr>

                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Referrer Username' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Buyer Username' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname-link" scope="col"><?php _e( 'Linking Type' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Add Linking' , SRP_LOCALE ) ; ?></th>

                    </tr>
                </tfoot>

                <tbody id="here">
                    <?php
                    $reward_dynamic_rule_manual = get_option( 'rewards_dynamic_rule_manual' ) ;
                    $i                          = 0 ;
                    if ( is_array( $reward_dynamic_rule_manual ) ) {
                        foreach ( $reward_dynamic_rule_manual as $rewards_dynamic_rule ) {
                            if ( $rewards_dynamic_rule[ 'referer' ] != '' && $rewards_dynamic_rule[ 'refferal' ] != '' ) {
                                ?>
                                <tr>
                                    <td class="column-columnname">
                                        <?php if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) { ?>
                                            <select name="rewards_dynamic_rule_manual[<?php echo $i ; ?>][referer]" class="short rs_manual_linking_referer">
                                                <?php
                                                $user = get_user_by( 'id' , absint( $rewards_dynamic_rule[ 'referer' ] ) ) ;
                                                echo '<option value="' . absint( $user->ID ) . '" ' ;
                                                selected( 1 , 1 ) ;
                                                echo '>' . esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')</option>' ;
                                                ?>
                                            </select>
                                            <?php
                                        } else {
                                            $user_id     = absint( $rewards_dynamic_rule[ 'referer' ] ) ;
                                            $user        = get_user_by( 'id' , $user_id ) ;
                                            $user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
                                            if ( ( float ) $woocommerce->version >= ( float ) ('3.0.0') ) {
                                                ?>
                                                <select multiple="multiple"  class="wc-customer-search" name="rewards_dynamic_rule_manual[<?php echo $i ; ?>][referer]" data-placeholder="<?php _e( 'Search Users' , SRP_LOCALE ) ; ?>" >
                                                    <option value="<?php echo $user_id ; ?>" selected="selected"><?php echo esc_attr( $user_string ) ; ?><option>
                                                </select>
                                            <?php } else {
                                                ?>
                                                <input type="hidden" class="wc-customer-search" name="rewards_dynamic_rule_manual[<?php echo $i ; ?>][referer]" data-placeholder="<?php _e( 'Search for a customer' , SRP_LOCALE ) ; ?>" data-selected="<?php echo esc_attr( $user_string ) ; ?>" value="<?php echo $user_id ; ?>" data-allow_clear="true" />
                                                <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="column-columnname">
                                        <?php if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) { ?>
                                            <select name="rewards_dynamic_rule_manual[<?php echo $i ; ?>][refferal]" class="short rs_manual_linking_referral">
                                                <?php
                                                $user = get_user_by( 'id' , absint( $rewards_dynamic_rule[ 'refferal' ] ) ) ;
                                                echo '<option value="' . absint( $user->ID ) . '" ' ;
                                                selected( 1 , 1 ) ;
                                                echo '>' . esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')</option>' ;
                                                ?>
                                            </select>
                                        <?php } else { ?>
                                            <?php
                                            $user_id     = absint( $rewards_dynamic_rule[ 'refferal' ] ) ;
                                            $user        = get_user_by( 'id' , $user_id ) ;
                                            $user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
                                            if ( ( float ) $woocommerce->version >= ( float ) ('3.0.0') ) {
                                                ?>
                                                <select multiple="multiple"  class="wc-customer-search" name="rewards_dynamic_rule_manual[<?php echo $i ; ?>][refferal]" data-placeholder="<?php _e( 'Search Users' , SRP_LOCALE ) ; ?>" >
                                                    <option value="<?php echo $user_id ; ?>" selected="selected"><?php echo esc_attr( $user_string ) ; ?><option>
                                                </select>
                                            <?php } else { ?>
                                                <input type="hidden" class="wc-customer-search" name="rewards_dynamic_rule_manual[<?php echo $i ; ?>][refferal]" data-placeholder="<?php _e( 'Search for a customer' , SRP_LOCALE ) ; ?>" data-selected="<?php echo esc_attr( $user_string ) ; ?>" value="<?php echo $user_id ; ?>" data-allow_clear="true" />
                                                <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="column-columnname-link">    <?php
                                        if ( @$rewards_dynamic_rule[ 'type' ] != '' ) {
                                            ?>
                                            <span> <b>Automatic</b></span>
                                            <?php
                                        } else {
                                            ?>
                                            <span> <b>Manual</b></span>
                                            <?php
                                        }
                                        ?>
                                        <input type="hidden" value="<?php echo @$rewards_dynamic_rule[ 'type' ] ; ?>" name="rewards_dynamic_rule_manual[<?php echo $i ; ?>][type]"/>
                                    </td>
                                    <td class="column-columnname num">
                                        <span class="remove button-secondary"><?php _e( 'Remove Linking' , SRP_LOCALE ) ; ?></span>
                                    </td>
                                </tr>
                                <?php
                                $i = $i + 1 ;
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <script>
                jQuery( document ).ready( function () {
                    var countrewards_dynamic_rule = <?php echo $i ; ?> ;
                    jQuery( ".add" ).click( function () {
                        countrewards_dynamic_rule = countrewards_dynamic_rule + 1 ;
            <?php if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) { ?>

                            jQuery( '#here' ).append( '<tr><td><select name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][referer]" class="short rs_manual_linking_referer"><option value=""></option></select></td>\n\
                                                                                                                                                                                                                        \n\<td><select name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][refferal]" class="short rs_manual_linking_referral"><option value=""></option></select></td>\n\
                                                                                                                                                                                                                        \n\<td class="column-columnname-link" ><span><input type="hidden" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][type]"  value="" class="short "/><b>Manual</b></span></td>\n\
                                                                                                                                                                                                                        \n\
                                                                                                                                                                                                                        <td class="num"><span class="remove button-secondary">Remove Linking</span></td></tr><hr>' ) ;
                            jQuery( function () {
                                // Ajax Chosen Product Selectors
                                jQuery( "select.rs_manual_linking_referer" ).ajaxChosen( {
                                    method : 'GET' ,
                                    url : '<?php echo SRP_ADMIN_AJAX_URL ; ?>' ,
                                    dataType : 'json' ,
                                    afterTypeDelay : 100 ,
                                    data : {
                                        action : 'woocommerce_json_search_customers' ,
                                        security : '<?php echo wp_create_nonce( "search-customers" ) ; ?>'
                                    }
                                } , function ( data ) {
                                    var terms = { } ;

                                    jQuery.each( data , function ( i , val ) {
                                        terms[i] = val ;
                                    } ) ;
                                    return terms ;
                                } ) ;
                            } ) ;
                            jQuery( function () {
                                // Ajax Chosen Product Selectors
                                jQuery( "select.rs_manual_linking_referral" ).ajaxChosen( {
                                    method : 'GET' ,
                                    url : '<?php echo SRP_ADMIN_AJAX_URL ; ?>' ,
                                    dataType : 'json' ,
                                    afterTypeDelay : 100 ,
                                    data : {
                                        action : 'woocommerce_json_search_customers' ,
                                        security : '<?php echo wp_create_nonce( "search-customers" ) ; ?>'
                                    }
                                } , function ( data ) {
                                    var terms = { } ;

                                    jQuery.each( data , function ( i , val ) {
                                        terms[i] = val ;
                                    } ) ;
                                    return terms ;
                                } ) ;
                            } ) ;
                <?php
            } else {
                if ( ( float ) $woocommerce->version >= ( float ) ('3.0.0') ) {
                    ?>
                                jQuery( '#here' ).append( '<tr><td><select class="wc-customer-search" style="width:250px;" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][referer]" data-placeholder="<?php _e( "Search for a customer" , "rewardsystem" ) ; ?>" data-allow_clear="true"><option value=""></option></select></td>\n\
                                                                                                                                                                                                                            \n\<td><select class="wc-customer-search" style="width:250px;" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][refferal]" data-placeholder="<?php _e( "Search for a customer" , "rewardsystem" ) ; ?>" data-allow_clear="true"><option value=""></option></select></td>\n\
                                                                                                                                                                                                                            \n\<td class="column-columnname-link" ><span><input type="hidden" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][type]"  value="" class="short "/><b>Manual</b></span></td>\n\
                                                                                                                                                                                                                            \n\
                                                                                                                                                                                                                            <td class="num"><span class="remove button-secondary">Remove Linking</span></td></tr><hr>' ) ;
                                jQuery( 'body' ).trigger( 'wc-enhanced-select-init' ) ;
                <?php } else { ?>
                                jQuery( '#here' ).append( '<tr><td><input type="hidden" class="wc-customer-search" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][referer]" data-placeholder="<?php _e( "Search for a customer" , "rewardsystem" ) ; ?>" data-selected="" value="" data-allow_clear="true"/></td>\n\
                                                                                                                                                                                                                            \n\<td><input type="hidden" class="wc-customer-search" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][refferal]" data-placeholder="<?php _e( "Search for a customer" , "rewardsystem" ) ; ?>" data-selected="" value="" data-allow_clear="true"/></td>\n\
                                                                                                                                                                                                                            \n\<td class="column-columnname-link" ><span><input type="hidden" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][type]"  value="" class="short "/><b>Manual</b></span></td>\n\
                                                                                                                                                                                                                            \n\
                                                                                                                                                                                                                            <td class="num"><span class="remove button-secondary">Remove Linking</span></td></tr><hr>' ) ;
                                jQuery( 'body' ).trigger( 'wc-enhanced-select-init' ) ;
                    <?php
                }
            }
            ?>
                        return false ;
                    } ) ;
                    jQuery( document ).on( 'click' , '.remove' , function () {
                        jQuery( this ).parent().parent().remove() ;
                    } ) ;
                } ) ;</script>

            <?php
        }

        public static function rs_list_referral_rewards_log() {
            if ( ! (isset( $_GET[ 'view' ] )) ) {
                $newwp_list_table_for_users = new WP_List_Table_for_Referral_Table() ;
                $newwp_list_table_for_users->prepare_items() ;
                $newwp_list_table_for_users->search_box( 'Search Users' , 'search_id' ) ;
                $newwp_list_table_for_users->display() ;
            } else {
                $newwp_list_table_for_users = new WP_List_Table_for_View_Referral_Table() ;
                $newwp_list_table_for_users->prepare_items() ;
                $newwp_list_table_for_users->search_box( 'Search' , 'search_id' ) ;
                $newwp_list_table_for_users->display() ;
                ?>
                <a href="<?php echo remove_query_arg( array( 'view' ) , get_permalink() ) ; ?>">Go Back</a>
                <?php
            }
        }

        public static function rs_add_upload_your_facebook_share_image() {
            ?>           
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_fbshare_image_url_upload"><?php _e( 'Image used for Facebook Share' , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" id="rs_fbshare_image_url_upload" name="rs_fbshare_image_url_upload" value="<?php echo get_option( 'rs_fbshare_image_url_upload' ) ; ?>"/>
                    <input type="submit" id="rs_fbimage_upload_button" class="rs_imgupload_button" name="rs_fbimage_upload_button" value="Upload Image"/>
                </td>
            </tr>            
            <?php
            $button_id = 'rs_fbimage_upload_button' ;
            $field_id  = 'rs_fbshare_image_url_upload' ;
            rs_ajax_for_upload_your_gift_voucher( $button_id , $field_id ) ;
        }

        public static function rs_include_products_for_referral_product_purchase() {
            $field_id    = "rs_include_products_for_referral_product_purchase" ;
            $field_label = "Include Product(s)" ;
            $getproducts = get_option( 'rs_include_products_for_referral_product_purchase' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        public static function rs_exclude_products_for_referral_product_purchase() {
            $field_id    = "rs_exclude_products_for_referral_product_purchase" ;
            $field_label = "Exclude Product(s)" ;
            $getproducts = get_option( 'rs_exclude_products_for_referral_product_purchase' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

    }

    RSReferralSystemModule::init() ;
}