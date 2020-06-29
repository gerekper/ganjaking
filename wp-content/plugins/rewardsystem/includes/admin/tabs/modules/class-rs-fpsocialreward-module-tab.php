<?php
/*
 * Social Rewards Tab Setting
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSSocialReward' ) ) {

    class RSSocialReward {

        public static function init() {

            add_action( 'woocommerce_rs_settings_tabs_fpsocialreward' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpsocialreward' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'rs_default_settings_fpsocialreward' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_admin_field_selected_social_products' , array( __CLASS__ , 'rs_select_products_to_update_social' ) ) ;

            add_action( 'woocommerce_admin_field_button_social' , array( __CLASS__ , 'rs_save_button_for_update_social' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_social_reward_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'woocommerce_admin_field_rs_hide_bulk_update_for_social_reward_start' , array( __CLASS__ , 'rs_hide_bulk_update_for_social_reward_start' ) ) ;

            add_action( 'woocommerce_admin_field_rs_hide_bulk_update_for_social_reward_end' , array( __CLASS__ , 'rs_hide_bulk_update_for_social_reward_end' ) ) ;

            add_action( 'woocommerce_admin_field_rs_include_products_for_social_reward' , array( __CLASS__ , 'rs_include_products_for_social_reward' ) ) ;

            add_action( 'woocommerce_admin_field_rs_exclude_products_for_social_reward' , array( __CLASS__ , 'rs_exclude_products_for_social_reward' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fpsocialreward' , array( __CLASS__ , 'reset_social_reward_module' ) ) ;

            add_action( 'rs_display_save_button_fpsocialreward' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpsocialreward' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            return apply_filters( 'woocommerce_rewardsystem_social_reward_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Social Reward Points Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_social_reward_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_social_reward_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_social_reward_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Social Reward Points Global Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_global_social_reward_points'
                ) ,
                array(
                    'name'     => __( 'Social Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_enable_product_category_level_for_social_reward' ,
                    'class'    => 'rs_enable_product_category_level_for_social_reward' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'type'     => 'radio' ,
                    'newids'   => 'rs_enable_product_category_level_for_social_reward' ,
                    'options'  => array(
                        'no'  => __( 'Quick Setup (Global Level Settings will be enabled)' , SRP_LOCALE ) ,
                        'yes' => __( 'Advanced Setup (Global,Category and Product Level wil be enabled)' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                    'desc'     => __( 'Quick Setup - Global Level will be enabled<br>Advanced Setup - Global,Category and Product Level wil be enabled' , SRP_LOCALE )
                ) ,
                array(
                    'name'    => __( 'Social Reward Points is applicable for' , SRP_LOCALE ) ,
                    'id'      => 'rs_social_reward_global_level_applicable_for' ,
                    'std'     => '1' ,
                    'class'   => 'rs_social_reward_global_level_applicable_for' ,
                    'default' => '1' ,
                    'newids'  => 'rs_social_reward_global_level_applicable_for' ,
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
                    'type' => 'rs_include_products_for_social_reward' ,
                ) ,
                array(
                    'type' => 'rs_exclude_products_for_social_reward' ,
                ) ,
                array(
                    'name'    => __( 'Include Categories' , SRP_LOCALE ) ,
                    'id'      => 'rs_include_particular_categories_for_social_reward' ,
                    'css'     => 'min-width:350px;' ,
                    'std'     => '' ,
                    'class'   => 'rs_include_particular_categories_for_social_reward' ,
                    'default' => '' ,
                    'newids'  => 'rs_include_particular_categories_for_social_reward' ,
                    'type'    => 'multiselect' ,
                    'options' => fp_product_category() ,
                ) ,
                array(
                    'name'    => __( 'Exclude Categories' , SRP_LOCALE ) ,
                    'id'      => 'rs_exclude_particular_categories_for_social_reward' ,
                    'css'     => 'min-width:350px;' ,
                    'std'     => '' ,
                    'class'   => 'rs_exclude_particular_categories_for_social_reward' ,
                    'default' => '' ,
                    'newids'  => 'rs_exclude_particular_categories_for_social_reward' ,
                    'type'    => 'multiselect' ,
                    'options' => fp_product_category() ,
                ) ,
                array(
                    'name'     => __( 'Enable SUMO Reward Points for Social Promotion' , SRP_LOCALE ) ,
                    'desc'     => __( 'This helps to Enable Social Reward Points in Global Level' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_enable_disable_reward' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_enable_disable_reward' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_reward_type_facebook' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_reward_type_facebook' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for facebook' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_facebook_reward_points' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_facebook_reward_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like Reward Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_facebook_reward_percent' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_facebook_reward_percent' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_reward_type_facebook_share' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_reward_type_facebook_share' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for facebook share' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_facebook_share_reward_points' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_facebook_share_reward_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Reward Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_facebook_share_reward_percent' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_facebook_share_reward_percent' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_reward_type_twitter' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_reward_type_twitter' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Twitter' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_twitter_reward_points' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_twitter_reward_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet Reward Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_twitter_reward_percent' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_twitter_reward_percent' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Follow Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_reward_type_twitter_follow' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_reward_type_twitter_follow' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Twitter Follow Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Twitter Follow ' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_twitter_follow_reward_points' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_twitter_follow_reward_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Follow Reward Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_twitter_follow_reward_percent' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_twitter_follow_reward_percent' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Google+1 Reward Type' , SRP_LOCALE ) ,
                    'desc'     => __( 'Select Social Reward Type for Google by Points/Percentage' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_reward_type_google' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_reward_type_google' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Google+1 Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Google' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_google_reward_points' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_google_reward_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Google+1 Reward Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_google_reward_percent' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_google_reward_percent' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'VK.com Like Reward Type' , SRP_LOCALE ) ,
                    'desc'     => __( 'Select Social Reward Type for VK by Points/Percentage' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_reward_type_vk' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_reward_type_vk' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'VK.com Like Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for VK' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_vk_reward_points' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_vk_reward_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'VK.com Like Reward Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_vk_reward_percent' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_vk_reward_percent' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Instagram Follow Reward Type' , SRP_LOCALE ) ,
                    'desc'     => __( 'Select Social Reward Type for Instagram Follow by Points/Percentage' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_reward_type_instagram' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_reward_type_instagram' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Instagram Follow Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Instagram' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_instagram_reward_points' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_instagram_reward_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Instagram Follow Reward Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_instagram_reward_percent' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_instagram_reward_percent' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'OK.ru Share Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_reward_type_ok_follow' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_reward_type_ok_follow' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'OK.ru Share Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for OK.ru share' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_ok_follow_reward_points' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_ok_follow_reward_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'OK.ru Share Reward Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_ok_follow_reward_percent' ,
                    'class'    => 'show_if_social_tab_enable' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_ok_follow_reward_percent' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_global_social_reward_points' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Social Reward Points Setting For Post/Page' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_global_social_reward_points_post'
                ) ,
                array(
                    'name'     => __( 'Enable SUMO Reward Points for Social Promotion in Post/Page' , SRP_LOCALE ) ,
                    'desc'     => __( 'This helps to Enable Social Reward Points in Global Level' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_enable_disable_reward_post' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_enable_disable_reward_post' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for facebook' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_facebook_reward_points_post' ,
                    'class'    => 'show_if_social_tab_enable_post' ,
                    'std'      => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_facebook_reward_points_post' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for facebook share' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_facebook_share_reward_points_post' ,
                    'class'    => 'show_if_social_tab_enable_post' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_facebook_share_reward_points_post' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Twitter' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_twitter_reward_points_post' ,
                    'class'    => 'show_if_social_tab_enable_post' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_twitter_reward_points_post' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Follow Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Twitter Follow ' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_twitter_follow_reward_points_post' ,
                    'class'    => 'show_if_social_tab_enable_post' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_twitter_follow_reward_points_post' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Google+1 Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Google' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_google_reward_points_post' ,
                    'class'    => 'show_if_social_tab_enable_post' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_google_reward_points_post' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'VK.com Like Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for VK' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_vk_reward_points_post' ,
                    'class'    => 'show_if_social_tab_enable_post' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_vk_reward_points_post' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Instagram Follow Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Instagram' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_instagram_reward_points_post' ,
                    'class'    => 'show_if_social_tab_enable_post' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_instagram_reward_points_post' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'OK.ru Share Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for OK.ru share' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_ok_follow_reward_points_post' ,
                    'class'    => 'show_if_social_tab_enable_post' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_global_social_ok_follow_reward_points_post' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_global_social_reward_points_post' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_hide_bulk_update_for_social_reward_start' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Social Reward Bulk Update Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_update_social_settings'
                ) ,
                array(
                    'name'     => __( 'Product/Category Selection' , SRP_LOCALE ) ,
                    'id'       => 'rs_which_social_product_selection' ,
                    'std'      => '1' ,
                    'class'    => 'rs_which_social_product_selection' ,
                    'default'  => '1' ,
                    'newids'   => 'rs_which_social_product_selection' ,
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
                    'type'   => 'selected_social_products' ,
                    'id'     => 'rs_select_particular_social_products' ,
                    'class'  => 'rs_select_particular_social_categories' ,
                    'newids' => 'rs_select_particular_social_products' ,
                ) ,
                array(
                    'name'    => __( 'Select Particular Categories' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_particular_social_categories' ,
                    'css'     => 'min-width:350px;' ,
                    'std'     => '1' ,
                    'class'   => 'rs_select_particular_social_categories' ,
                    'default' => '1' ,
                    'newids'  => 'rs_select_particular_social_categories' ,
                    'type'    => 'multiselect' ,
                    'options' => fp_product_category() ,
                ) ,
                array(
                    'name'     => __( 'Enable SUMO Reward Points for Social Promotion' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_enable_disable_social_reward' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'desc_tip' => true ,
                    'desc'     => __( 'Enable will Turn On Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                            . 'Disable will Turn Off Reward Points for Product Purchase and Category/Product Settings will be considered if it is available.' , SRP_LOCALE ) ,
                    'newids'   => 'rs_local_enable_disable_social_reward' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_type_for_facebook' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_local_reward_type_for_facebook' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Facebook' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_points_facebook' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_points_facebook' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like Reward Points in Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_percent_facebook' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_percent_facebook' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_type_for_facebook_share' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_local_reward_type_for_facebook_share' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Facebook Share' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_points_facebook_share' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_points_facebook_share' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Reward Points in Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_percent_facebook_share' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_percent_facebook_share' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_type_for_twitter' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_local_reward_type_for_twitter' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Twitter' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_points_twitter' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_points_twitter' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet Reward Points in Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points for Twitter' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_percent_twitter' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_percent_twitter' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Follow Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_type_for_twitter_follow' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_local_reward_type_for_twitter_follow' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Twitter Follow Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Twitter' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_points_twitter_follow' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_points_twitter_follow' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Follow Reward Points in Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points for Twitter' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_percent_twitter_follow' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_percent_twitter_follow' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Google+1 Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_type_for_google' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_local_reward_type_for_google' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Google+1 Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Google+' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_points_google' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_points_google' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Google+1 Reward Points in Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points for Google+' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_percent_google' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_percent_google' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'VK.com Like Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_type_for_vk' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_local_reward_type_for_vk' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'VK.com Like Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for VK' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_points_vk' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_points_vk' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'VK.com Like Reward Points in Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points for VK' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_percent_vk' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_percent_vk' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Instagram Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_type_for_instagram' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_local_reward_type_for_instagram' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Instagram Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Instagram' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_points_instagram' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_points_instagram' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Instagram Reward Points in Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points for Instagram' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_percent_instagram' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_percent_instagram' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'OK.ru Share Reward Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_type_for_ok_follow' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_local_reward_type_for_ok_follow' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'OK.ru Share Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Reward Points for Ok.ru' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_points_ok_follow' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_points_ok_follow' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'OK.ru Share Reward Points in Percent %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Percentage value of Reward Points for OK.ru' , SRP_LOCALE ) ,
                    'id'       => 'rs_local_reward_percent_ok_follow' ,
                    'class'    => 'show_if_social_enable_in_update' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_local_reward_percent_ok_follow' ,
                    'desc'     => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Test Button' , SRP_LOCALE ) ,
                    'desc'     => __( 'This is for testing button' , SRP_LOCALE ) ,
                    'id'       => 'rs_sumo_reward_button' ,
                    'std'      => '' ,
                    'type'     => 'button_social' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_sumo_reward_button' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_update_redeem_settings' ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_update_social_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_hide_bulk_update_for_social_reward_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Facebook Like & Share Button Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_fblike_setting' ,
                    'desc' => __( '<br><b>Warning:</b> Please note callback for Facebook Like has been deprecated for API versions v2.11 or later. If you are creating an app id with v2.11 or later, then points will not be awarded.' , SRP_LOCALE )
                ) ,
                array(
                    'name'    => __( 'Language Selection for Facebook Like & Share' , SRP_LOCALE ) ,
                    'id'      => 'rs_language_selection_for_button' ,
                    'class'   => 'rs_language_selection_for_button' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'radio' ,
                    'options' => array(
                        '1' => __( 'English(US)' , SRP_LOCALE ) ,
                        '2' => __( 'Default Site Language' , SRP_LOCALE ) ,
                    ) ,
                    'newids'  => 'rs_language_selection_for_button' ,
                ) ,
                array(
                    'name'     => __( 'Facebook Application ID' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Application ID of your Facebook' , SRP_LOCALE ) ,
                    'id'       => 'rs_facebook_application_id' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_facebook_application_id' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_facebook_like_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_show_hide_facebook_like_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Social Button Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_social_button_like' ,
                    'std'     => '1' ,
                    'class'   => 'rs_social_button_like' ,
                    'default' => '1' ,
                    'newids'  => 'rs_social_button_like' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Native Button' , SRP_LOCALE ) ,
                        '2' => __( 'Custom Button' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like Button Size' , SRP_LOCALE ) ,
                    'id'       => 'rs_facebook_like_icon_size' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_facebook_like_icon_size' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        'small' => __( 'Small' , SRP_LOCALE ) ,
                        'large' => __( 'Large' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like URL Selection' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_facebook_url' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_facebook_url' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Default' , SRP_LOCALE ) ,
                        '2' => __( 'Custom' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Custom URL For Like' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Custom URL that you wish to enable for Facebook' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'id'       => 'rs_global_social_facebook_url_custom' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Facebook Like Reward Points in Single Product Page' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Facebook Like Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_Facebook_like' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_Facebook_like' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Facebook Like Reward Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_facebook_like' ,
                    'std'     => 'Facebook Like - Notification' ,
                    'default' => 'Facebook Like - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_facebook_like' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail for Facebook Like Post/Page Reward Points  ' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Post Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_post_fb_like' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_post_fb_like' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject for Facebook Like Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_post_fb_like' ,
                    'std'     => 'Facebook Like Post - Notification' ,
                    'default' => 'Facebook Like Post - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_post_fb_like' ,
                ) ,
                array(
                    'name'    => __( 'Email Message for Facebook Like Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_post_fb_like' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_post_fb_like' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Facebook Like Reward Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_facebook_like' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_facebook_like' ,
                ) ,
                array(
                    'name'     => __( 'Social ToolTip for Facebook Like' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_social_tooltip_for_facebook' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Show' ,
                        '2' => 'Hide'
                    ) ,
                    'newids'   => 'rs_global_show_hide_social_tooltip_for_facebook' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'ToolTip Facebook Like Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter ToolTip Message for Facebook Like' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_message_for_facebook' ,
                    'std'      => 'Facebook Like will fetch you [facebook_like_reward_points] Reward Points' ,
                    'default'  => 'Facebook Like will fetch you [facebook_like_reward_points] Reward Points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_social_message_for_facebook' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Like Success Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon successful Facebook like' , SRP_LOCALE ) ,
                    'id'       => 'rs_succcess_message_for_facebook_like' ,
                    'std'      => 'Thanks for liking the Product.  [facebook_like_reward_points] Reward Points has been added to your Account.' ,
                    'default'  => 'Thanks for liking the Product.  [facebook_like_reward_points] Reward Points has been added to your Account.' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_succcess_message_for_facebook_like' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Unlike Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon when Facebook unlike' , SRP_LOCALE ) ,
                    'id'       => 'rs_unsucccess_message_for_facebook_unlike' ,
                    'std'      => 'You have already Unliked this product on Facebook.You cannot earn points again' ,
                    'default'  => 'You have already Unliked this product on Facebook.You cannot earn points again' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_unsucccess_message_for_facebook_unlike' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_facebook_share_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_show_hide_facebook_share_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share URL Selection' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_facebook_share_url' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_facebook_share_url' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Default' , SRP_LOCALE ) ,
                        '2' => __( 'Custom' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Facebook Custom URL For Share' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Custom URL that you wish to enable for Facebook' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'id'       => 'rs_global_social_facebook_share_url_custom' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Social Button Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_social_button_share' ,
                    'std'     => '1' ,
                    'class'   => 'rs_social_button_share' ,
                    'default' => '1' ,
                    'newids'  => 'rs_social_button_share' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Native Button' , SRP_LOCALE ) ,
                        '2' => __( 'Custom Button' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Enable To Send Mail For Facebook Share Reward Points in Single Product Page' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enabling this option will send Facebook Share Points through Mail' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                    'id'       => 'rs_send_mail_facebook_share' ,
                    'type'     => 'checkbox' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'newids'   => 'rs_send_mail_facebook_share' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Facebook Share Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_facebook_share' ,
                    'std'     => 'Facebook Share - Notification' ,
                    'default' => 'Facebook Share - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_facebook_share' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Facebook Share Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_facebook_share' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_facebook_share' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Facebook Share Post/Page Reward Points ' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Post/Page Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_post_fb_share' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_post_fb_share' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Facebook Share Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_post_fb_share' ,
                    'std'     => 'Facebook Share Post - Notification' ,
                    'default' => 'Facebook Share Post - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_post_fb_share' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Facebook Share Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_post_fb_share' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_post_fb_share' ,
                ) ,
                array(
                    'name'     => __( 'Social ToolTip for Facebook Share' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_social_tooltip_for_facebook_share' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Show' ,
                        '2' => 'Hide'
                    ) ,
                    'newids'   => 'rs_global_show_hide_social_tooltip_for_facebook_share' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'ToolTip Facebook Share Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter ToolTip Message for Facebook Share' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_message_for_facebook_share' ,
                    'std'      => 'Facebook Share will fetch you [facebook_share_reward_points] Reward Points' ,
                    'default'  => 'Facebook Share will fetch you [facebook_share_reward_points] Reward Points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_social_message_for_facebook_share' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Success Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon successful Facebook Share' , SRP_LOCALE ) ,
                    'id'       => 'rs_succcess_message_for_facebook_share' ,
                    'std'      => 'Thanks for Sharing the Product.[facebook_share_reward_points] Reward Points has been added to your Account.' ,
                    'default'  => 'Thanks for Sharing the Product.[facebook_share_reward_points] Reward Points has been added to your Account.' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_succcess_message_for_facebook_share' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share UnSuccess Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon Unsuccessful Facebook Share' , SRP_LOCALE ) ,
                    'id'       => 'rs_unsucccess_message_for_facebook_share' ,
                    'std'      => 'You already shared this post.You cannot earn points again' ,
                    'default'  => 'You already shared this post.You cannot earn points again' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_unsucccess_message_for_facebook_share' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Facebook Share Button Label' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'id'       => 'rs_fbshare_button_label' ,
                    'class'    => 'rs_fbshare_button_label' ,
                    'newids'   => 'rs_fbshare_button_label' ,
                    'std'      => 'Share' ,
                    'default'  => 'Share' ,
                    'desc_tip' => true ,
                    'desc'     => __( 'Enter the Name of Facebook Share that will be displayed in button' , SRP_LOCALE ) ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_fblike_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Twitter Tweet & Follow Button Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_twitter_setting'
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_twitter_tweet_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_show_hide_twitter_tweet_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Twitter URL Selection' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_twitter_url' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_twitter_url' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Default' , SRP_LOCALE ) ,
                        '2' => __( 'Custom' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Twitter Custom URL' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Custom URL that you wish to enable for Twitter' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'id'       => 'rs_global_social_twitter_url_custom' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Social Button Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_social_button_tweet' ,
                    'std'     => '1' ,
                    'class'   => 'rs_social_button_tweet' ,
                    'default' => '1' ,
                    'newids'  => 'rs_social_button_tweet' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Native Button' , SRP_LOCALE ) ,
                        '2' => __( 'Custom Button' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Enable To Send Mail For Twitter Tweet Reward Points in Single Product Page' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enabling this option will send Twitter Tweet Points through Mail' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                    'id'       => 'rs_send_mail_tewitter_tweet' ,
                    'type'     => 'checkbox' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'newids'   => 'rs_send_mail_tewitter_tweet' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Twitter Tweet Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_twitter_tweet' ,
                    'std'     => 'Twitter Tweet - Notification' ,
                    'default' => 'Twitter Tweet - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_twitter_tweet' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Twitter Tweet Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_twitter_tweet' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_twitter_tweet' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Twitter Tweet Post/Page Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Post/Page Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_post_tweet' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_post_tweet' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Twitter Tweet Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_post_tweet' ,
                    'std'     => 'Twitter Tweet Post - Notification' ,
                    'default' => 'Twitter Tweet Post - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_post_tweet' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Twitter Tweet Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_post_tweet' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_post_tweet' ,
                ) ,
                array(
                    'name'     => __( 'Social ToolTip for Twitter Tweet' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_social_tooltip_for_twitter' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Show' ,
                        '2' => 'Hide'
                    ) ,
                    'newids'   => 'rs_global_show_hide_social_tooltip_for_twitter' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'ToolTip Twitter Tweet Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter ToolTip Message for Twitter' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_message_for_twitter' ,
                    'std'      => 'Twitter Tweet will fetch you [twitter_tweet_reward_points] Reward Points' ,
                    'default'  => 'Twitter Tweet will fetch you [twitter_tweet_reward_points] Reward Points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_social_message_for_twitter' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet Success Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon successful Twitter Tweet' , SRP_LOCALE ) ,
                    'id'       => 'rs_succcess_message_for_twitter_share' ,
                    'std'      => 'Thanks for the tweet . [twitter_tweet_reward_points] Reward Points has been added to your Account' ,
                    'default'  => 'Thanks for the tweet . [twitter_tweet_reward_points] Reward Points has been added to your Account' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_succcess_message_for_twitter_share' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Tweet UnSuccess Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed when tweet deleted in Twitter' , SRP_LOCALE ) ,
                    'id'       => 'rs_unsucccess_message_for_twitter_unshare' ,
                    'std'      => 'You have already Unshared this product on Twitter.You cannot earn points again' ,
                    'default'  => 'You have already Unshared this product on Twitter.You cannot earn points again' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_unsucccess_message_for_twitter_unshare' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Follow Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_twitter_follow_tweet_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_show_hide_twitter_follow_tweet_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Twitter Profile Name' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Profile  Name For Twitter Follow ' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'id'       => 'rs_global_social_twitter_profile_name' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Social Button Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_social_button_twitter_follow' ,
                    'std'     => '1' ,
                    'class'   => 'rs_social_button_twitter_follow' ,
                    'default' => '1' ,
                    'newids'  => 'rs_social_button_twitter_follow' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Native Button' , SRP_LOCALE ) ,
                        '2' => __( 'Custom Button' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Enable To Send Mail For Twitter Follow Reward Points in Single Product Page' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enabling this option will send Twitter Follow Points through Mail' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                    'id'       => 'rs_send_mail_twitter_follow' ,
                    'type'     => 'checkbox' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'newids'   => 'rs_send_mail_twitter_follow' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Twitter Follow Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_twitter_follow' ,
                    'std'     => 'Twitter Follow - Notification' ,
                    'default' => 'Twitter Follow - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_twitter_follow' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Twitter Follow Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_twitter_follow' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_twitter_follow' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Twitter Follow Post/Page Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Post/Page Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_post_follow' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_post_follow' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Twitter Follow Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_post_follow' ,
                    'std'     => 'Twitter Share Post - Notification' ,
                    'default' => 'Twitter Share Post - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_post_follow' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Twitter Follow Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_post_follow' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_post_follow' ,
                ) ,
                array(
                    'name'     => __( 'Social ToolTip for Twitter Follow' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_social_tooltip_for_twitter_follow' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Show' ,
                        '2' => 'Hide'
                    ) ,
                    'newids'   => 'rs_global_show_hide_social_tooltip_for_twitter_follow' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'ToolTip Twitter Follow Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter ToolTip Message for Twitter Follow' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_message_for_twitter_follow' ,
                    'std'      => 'Twitter Follow will fetch you [twitter_follow_reward_points] Reward Points' ,
                    'default'  => 'Twitter Follow will fetch you [twitter_follow_reward_points] Reward Points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_social_message_for_twitter_follow' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Follow Success Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon successful Twitter Follow' , SRP_LOCALE ) ,
                    'id'       => 'rs_succcess_message_for_twitter_follow' ,
                    'std'      => 'Thanks for the Follow . [twitter_follow_reward_points] Reward Points has been added to your Account' ,
                    'default'  => 'Thanks for the Follow . [twitter_follow_reward_points] Reward Points has been added to your Account' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_succcess_message_for_twitter_follow' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Twitter Follow UnSuccess Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed when Follow deleted in Twitter' , SRP_LOCALE ) ,
                    'id'       => 'rs_unsucccess_message_for_twitter_unfollow' ,
                    'std'      => 'You have already follow this Profile on Twitter.You cannot earn points again' ,
                    'default'  => 'You have already follow this Profile on Twitter.You cannot earn points again' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_unsucccess_message_for_twitter_unfollow' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_twitter_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Instagram Button Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_instagram_setting'
                ) ,
                array(
                    'name'     => __( 'Instagram Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_instagram_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_show_hide_instagram_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Instagram Profile Name' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Profile Name of your Instagram' , SRP_LOCALE ) ,
                    'id'       => 'rs_instagram_profile_name' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_instagram_profile_name' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Social Button Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_social_button_instagram' ,
                    'std'     => '1' ,
                    'class'   => 'rs_social_button_instagram' ,
                    'default' => '1' ,
                    'newids'  => 'rs_social_button_instagram' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Native Button' , SRP_LOCALE ) ,
                        '2' => __( 'Custom Button' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Enable To Send Mail For Instagram Reward Points in Single Product Page' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enabling this option will send Instagram Points through Mail' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                    'id'       => 'rs_send_mail_instagram' ,
                    'type'     => 'checkbox' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'newids'   => 'rs_send_mail_instagram' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Instagram Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_instagram' ,
                    'std'     => 'Instagram - Notification' ,
                    'default' => 'Instagram - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_instagram' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Instagram Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_instagram' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_instagram' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Instagram Post/Page Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Post/Page Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_post_instagram' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_post_instagram' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Instagram Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_post_instagram' ,
                    'std'     => 'Instagram Follow Post - Notification' ,
                    'default' => 'Instagram Follow Post - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_post_instagram' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Instagram Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_post_instagram' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_post_instagram' ,
                ) ,
                array(
                    'name'     => __( 'Social ToolTip for Instagram' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_social_tooltip_for_instagram' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Show' ,
                        '2' => 'Hide'
                    ) ,
                    'newids'   => 'rs_global_show_hide_social_tooltip_for_instagram' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'ToolTip Instagram Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter ToolTip Message for Instagram' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_message_for_instagram' ,
                    'std'      => 'Instagram Follow will fetch you [instagram_reward_points] Reward Points' ,
                    'default'  => 'Instagram Follow will fetch you [instagram_reward_points] Reward Points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_social_message_for_instagram' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Instagram Success Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon successful Instagram follow' , SRP_LOCALE ) ,
                    'id'       => 'rs_succcess_message_for_instagram' ,
                    'std'      => 'Thanks for the follow on Instagram. [instagram_reward_points] Reward Points has been added to your Account' ,
                    'default'  => 'Thanks for the follow on Instagram. [instagram_reward_points] Reward Points has been added to your Account' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_succcess_message_for_instagram' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Instagram Unfollow Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed Unfollow Instagram' , SRP_LOCALE ) ,
                    'id'       => 'rs_unsucccess_message_for_instagram' ,
                    'std'      => 'You have already Follow this Profile on Instagram.You cannot earn points again' ,
                    'default'  => 'You have already Follow this Profile on Instagram.You cannot earn points again' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_unsucccess_message_for_instagram' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_instagram_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'VK.Com Button Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_vk_setting'
                ) ,
                array(
                    'name'     => __( 'VK.com Like Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_vk_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_show_hide_vk_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'VK Application ID' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Application ID of your VK' , SRP_LOCALE ) ,
                    'id'       => 'rs_vk_application_id' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_vk_application_id' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Social Button Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_social_button_vk_like' ,
                    'std'     => '1' ,
                    'class'   => 'rs_social_button_vk_like' ,
                    'default' => '1' ,
                    'newids'  => 'rs_social_button_vk_like' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Native Button' , SRP_LOCALE ) ,
                        '2' => __( 'Custom Button' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Enable To Send Mail For VK Reward Points in Single Product Page' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enabling this option will send VK Points through Mail' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                    'id'       => 'rs_send_mail_vk' ,
                    'type'     => 'checkbox' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'newids'   => 'rs_send_mail_vk' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For VK Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_vk' ,
                    'std'     => 'VK - Notification' ,
                    'default' => 'VK - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_vk' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For VK Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_vk' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_vk' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For VK Post/Page Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Post/Page Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_post_vk' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_post_vk' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For VK Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_post_vk' ,
                    'std'     => 'VK Like Post - Notification' ,
                    'default' => 'VK Like Post - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_post_vk' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For VK Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_post_vk' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_post_vk' ,
                ) ,
                array(
                    'name'     => __( 'Social ToolTip for VK.com' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_social_tooltip_for_vk' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Show' ,
                        '2' => 'Hide'
                    ) ,
                    'newids'   => 'rs_global_show_hide_social_tooltip_for_vk' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'ToolTip VK.com Like Message ' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter ToolTip Message for VK.com Like' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_message_for_vk' ,
                    'std'      => 'VK Share will fetch you [vk_reward_points] Reward Points' ,
                    'default'  => 'VK Share will fetch you [vk_reward_points] Reward Points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_social_message_for_vk' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'VK Like Success Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon successful VK Like' , SRP_LOCALE ) ,
                    'id'       => 'rs_succcess_message_for_vk' ,
                    'std'      => 'Thanks for the like on VK. [vk_reward_points] Reward Points has been added to your Account' ,
                    'default'  => 'Thanks for the like on VK. [vk_reward_points] Reward Points has been added to your Account' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_succcess_message_for_vk' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'VK Unlike Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed Unlike VK' , SRP_LOCALE ) ,
                    'id'       => 'rs_unsucccess_message_for_vk' ,
                    'std'      => 'You have already Unlike this product on VK.You cannot earn points again' ,
                    'default'  => 'You have already Unlike this product on VK.You cannot earn points again' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_unsucccess_message_for_vk' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_vk_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Google+1 Button Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_gplus_setting'
                ) ,
                array(
                    'name'     => __( 'Google+1 Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_google_plus_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_show_hide_google_plus_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Google+1 URL Selection' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_google_url' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_google_url' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Default' , SRP_LOCALE ) ,
                        '2' => __( 'Custom' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Google+1 Custom URL' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Custom URL that you wish to enable for Google' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'id'       => 'rs_global_social_google_url_custom' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Social Button Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_social_button_gplus' ,
                    'std'     => '1' ,
                    'class'   => 'rs_social_button_gplus' ,
                    'default' => '1' ,
                    'newids'  => 'rs_social_button_gplus' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Native Button' , SRP_LOCALE ) ,
                        '2' => __( 'Custom Button' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Enable To Send Mail For Google+1 Reward Points in Single Product Page' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enabling this option will send Google+1 Points through Mail' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                    'id'       => 'rs_send_mail_google' ,
                    'type'     => 'checkbox' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'newids'   => 'rs_send_mail_google' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Google+1 Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_google' ,
                    'std'     => 'Google+1 - Notification' ,
                    'default' => 'Google+1 - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_google' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Google+1 Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_google' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_google' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Google+1 Post/Page Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Post/Page Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_post_gplus' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_post_gplus' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Google+1 Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_post_gplus' ,
                    'std'     => 'Google+1 Share Post - Notification' ,
                    'default' => 'Google+1 Share Post - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_post_gplus' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Google+1 Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_post_gplus' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_post_gplus' ,
                ) ,
                array(
                    'name'     => __( 'Social ToolTip for Google+1' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_social_tooltip_for_google' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Show' ,
                        '2' => 'Hide'
                    ) ,
                    'newids'   => 'rs_global_show_hide_social_tooltip_for_google' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'ToolTip Google+1 Message ' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter ToolTip Message for Google+ Share' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_message_for_google_plus' ,
                    'std'      => 'Google+1 Share will fetch you [google_share_reward_points] Reward Points' ,
                    'default'  => 'Google+1 Share will fetch you [google_share_reward_points] Reward Points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_social_message_for_google_plus' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Google+1 Share Success Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon successful Google+ Share' , SRP_LOCALE ) ,
                    'id'       => 'rs_succcess_message_for_google_share' ,
                    'std'      => 'Thanks for Sharing the Product on Google+ . [google_share_reward_points] Reward Points has been added to your Account' ,
                    'default'  => 'Thanks for Sharing the Product on Google+ . [google_share_reward_points] Reward Points has been added to your Account' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_succcess_message_for_google_share' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Google+1 UnShare Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon when unshare Google+' , SRP_LOCALE ) ,
                    'id'       => 'rs_unsucccess_message_for_google_unshare' ,
                    'std'      => 'You have already Unshared this product on Google +.You cannot earn points again' ,
                    'default'  => 'You have already Unshared this product on Google +.You cannot earn points again' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_unsucccess_message_for_google_unshare' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_gplus_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'OK.ru Button Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_ok_setting'
                ) ,
                array(
                    'name'     => __( 'OK.ru Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_ok_button' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_show_hide_ok_button' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'OK.ru URL Selection' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_social_ok_url' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'desc_tip' => true ,
                    'newids'   => 'rs_global_social_ok_url' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Default' , SRP_LOCALE ) ,
                        '2' => __( 'Custom' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'OK.ru Custom URL' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Custom URL that you wish to enable for OK.ru' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'id'       => 'rs_global_social_ok_url_custom' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Social Button Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_social_button_ok_ru' ,
                    'std'     => '1' ,
                    'class'   => 'rs_social_button_ok_ru' ,
                    'default' => '1' ,
                    'newids'  => 'rs_social_button_ok_ru' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Native Button' , SRP_LOCALE ) ,
                        '2' => __( 'Custom Button' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Enable To Send Mail For OK.ru Reward Points in Single Product Page' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enabling this option will send Instagram Points through Mail' , SRP_LOCALE ) ,
                    'desc_tip' => true ,
                    'id'       => 'rs_send_mail_ok' ,
                    'type'     => 'checkbox' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'newids'   => 'rs_send_mail_ok' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For OK.ru Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_ok' ,
                    'std'     => 'OK.ru - Notification' ,
                    'default' => 'OK.ru - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_ok' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For OK.ru Points in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_ok' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_ok' ,
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For OK.ru Post/Page Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Post/Page Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_post_ok_ru' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_post_ok_ru' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For OK.ru Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_post_ok_ru' ,
                    'std'     => 'OK.ru Post - Notification' ,
                    'default' => 'OK.ru Post - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_post_ok_ru' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For OK.ru Post/Page Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_post_ok_ru' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_post_ok_ru' ,
                ) ,
                array(
                    'name'     => __( 'Social ToolTip for OK.ru Share' , SRP_LOCALE ) ,
                    'id'       => 'rs_global_show_hide_social_tooltip_for_ok_follow' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => 'Show' ,
                        '2' => 'Hide'
                    ) ,
                    'newids'   => 'rs_global_show_hide_social_tooltip_for_ok_follow' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'ToolTip OK.ru Share Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter ToolTip Message for OK.ru Share' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_message_for_ok_follow' ,
                    'std'      => 'OK.ru Share will fetch you [ok_share_reward_points] Reward Points' ,
                    'default'  => 'OK.ru Share will fetch you [ok_share_reward_points] Reward Points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_social_message_for_ok_follow' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'OK.ru Share Success Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed upon successful OK.ru Share' , SRP_LOCALE ) ,
                    'id'       => 'rs_succcess_message_for_ok_follow' ,
                    'std'      => 'Thanks for the Share. [ok_share_reward_points] Reward Points has been added to your Account' ,
                    'default'  => 'Thanks for the Share. [ok_share_reward_points] Reward Points has been added to your Account' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_succcess_message_for_ok_follow' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'OK.ru Share UnSuccess Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message that will be displayed when Shared in OK.ru' , SRP_LOCALE ) ,
                    'id'       => 'rs_unsucccess_message_for_ok_unfollow' ,
                    'std'      => 'You have already Shared this Profile on OK.ru.You cannot earn points again' ,
                    'default'  => 'You have already Shared this Profile on OK.ru.You cannot earn points again' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_unsucccess_message_for_ok_unfollow' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_ok_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Social Reward Points Restriction Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_point_socialrewards_restriction' ,
                    'desc' => __( 'For eg: Consider for Facebook Share, if the value is set to 2, then the users can earn points for sharing the first 2 products on Facebook per day. Further, they can share the other products but cannot earn points. Similarly, for all the other actions.' , SRP_LOCALE ) ,
                ) ,
                array(
                    'name'    => __( 'Facebook Like' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_fblike_restriction' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_fblike_restriction' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this checkbox, you can restrict the users to earn points for liking the product(s) per day.' , SRP_LOCALE )
                ) ,
                array(
                    'name'              => __( 'Enter the value' , SRP_LOCALE ) ,
                    'id'                => 'rs_no_of_fblike_count' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'newids'            => 'rs_no_of_fblike_count' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => 0
                    )
                ) ,
                array(
                    'name'    => __( 'Notification' , SRP_LOCALE ) ,
                    'id'      => 'rs_restriction_message_for_fblike' ,
                    'std'     => 'Since you have reached the limit for liking the product(s) on Facebook, you cannot earn points anymore.' ,
                    'default' => 'Since you have reached the limit for liking the product(s) on Facebook, you cannot earn points anymore.' ,
                    'newids'  => 'rs_restriction_message_for_fblike' ,
                    'type'    => 'textarea' ,
                ) ,
                array(
                    'name'    => __( 'Facebook Share' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_fbshare_restriction' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_fbshare_restriction' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this checkbox, you can restrict the users to earn points for sharing the product(s) per day.' , SRP_LOCALE )
                ) ,
                array(
                    'name'              => __( 'Enter the value' , SRP_LOCALE ) ,
                    'id'                => 'rs_no_of_fbshare_count' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'newids'            => 'rs_no_of_fbshare_count' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => 0
                    )
                ) ,
                array(
                    'name'    => __( 'Notification' , SRP_LOCALE ) ,
                    'id'      => 'rs_restriction_message_for_fbshare' ,
                    'std'     => 'Since you have reached the limit for sharing the product(s) on Facebook, you cannot earn points anymore.' ,
                    'default' => 'Since you have reached the limit for sharing the product(s) on Facebook, you cannot earn points anymore.' ,
                    'newids'  => 'rs_restriction_message_for_fbshare' ,
                    'type'    => 'textarea' ,
                ) ,
                array(
                    'name'    => __( 'Twitter Tweet' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_tweet_restriction' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_tweet_restriction' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this checkbox, you can restrict the users to earn points for tweeting the product(s) per day.' , SRP_LOCALE )
                ) ,
                array(
                    'name'              => __( 'Enter the value' , SRP_LOCALE ) ,
                    'id'                => 'rs_no_of_tweet_count' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'newids'            => 'rs_no_of_tweet_count' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => 0
                    )
                ) ,
                array(
                    'name'    => __( 'Notification' , SRP_LOCALE ) ,
                    'id'      => 'rs_restriction_message_for_tweet' ,
                    'std'     => 'Since you have reached the limit for tweeting the product(s) on Twitter, you cannot earn points anymore.' ,
                    'default' => 'Since you have reached the limit for tweeting the product(s) on Twitter, you cannot earn points anymore.' ,
                    'newids'  => 'rs_restriction_message_for_tweet' ,
                    'type'    => 'textarea' ,
                ) ,
                array(
                    'name'    => __( 'Twitter Follow' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_twitter_follow_restriction' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_twitter_follow_restriction' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this checkbox, you can restrict the users to earn points for following the product(s) per day.' , SRP_LOCALE )
                ) ,
                array(
                    'name'              => __( 'Enter the value' , SRP_LOCALE ) ,
                    'id'                => 'rs_no_of_twitter_follow_count' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'newids'            => 'rs_no_of_twitter_follow_count' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => 0
                    )
                ) ,
                array(
                    'name'    => __( 'Notification' , SRP_LOCALE ) ,
                    'id'      => 'rs_restriction_message_for_twitter_follow' ,
                    'std'     => 'Since you have reached the limit for following the product(s) on Twitter, you cannot earn points anymore.' ,
                    'default' => 'Since you have reached the limit for following the product(s) on Twitter, you cannot earn points anymore.' ,
                    'newids'  => 'rs_restriction_message_for_twitter_follow' ,
                    'type'    => 'textarea' ,
                ) ,
                array(
                    'name'    => __( 'Instagram' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_instagram_restriction' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_instagram_restriction' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this checkbox, you can restrict the users to earn points for following the product(s) per day.' , SRP_LOCALE )
                ) ,
                array(
                    'name'              => __( 'Enter the value' , SRP_LOCALE ) ,
                    'id'                => 'rs_no_of_instagram_count' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'newids'            => 'rs_no_of_instagram_count' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => 0
                    )
                ) ,
                array(
                    'name'    => __( 'Notification' , SRP_LOCALE ) ,
                    'id'      => 'rs_restriction_message_for_instagram' ,
                    'std'     => 'Since you have reached the limit for following the product(s) on Instagram, you cannot earn points anymore.' ,
                    'default' => 'Since you have reached the limit for following the product(s) on Instagram, you cannot earn points anymore.' ,
                    'newids'  => 'rs_restriction_message_for_instagram' ,
                    'type'    => 'textarea' ,
                ) ,
                array(
                    'name'    => __( 'VK.Com' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_vk_restriction' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_vk_restriction' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this checkbox, you can restrict the users to earn points for liking the product(s) per day.' , SRP_LOCALE )
                ) ,
                array(
                    'name'              => __( 'Enter the value' , SRP_LOCALE ) ,
                    'id'                => 'rs_no_of_vk_count' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'newids'            => 'rs_no_of_vk_count' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => 0
                    )
                ) ,
                array(
                    'name'    => __( 'Notification' , SRP_LOCALE ) ,
                    'id'      => 'rs_restriction_message_for_vk' ,
                    'std'     => 'Since you have reached the limit for liking the product(s) on VK.Com, you cannot earn points anymore.' ,
                    'default' => 'Since you have reached the limit for liking the product(s) on VK.Com, you cannot earn points anymore.' ,
                    'newids'  => 'rs_restriction_message_for_vk' ,
                    'type'    => 'textarea' ,
                ) ,
                array(
                    'name'    => __( 'Google+1' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_gplus_restriction' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_gplus_restriction' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this checkbox, you can restrict the users to earn points for sharing the product(s) per day.' , SRP_LOCALE )
                ) ,
                array(
                    'name'              => __( 'Enter the value' , SRP_LOCALE ) ,
                    'id'                => 'rs_no_of_gplus_count' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'newids'            => 'rs_no_of_gplus_count' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => 0
                    )
                ) ,
                array(
                    'name'    => __( 'Notification' , SRP_LOCALE ) ,
                    'id'      => 'rs_restriction_message_for_gplus' ,
                    'std'     => 'Since you have reached the limit for sharing the product(s) on Google+1, you cannot earn points anymore.' ,
                    'default' => 'Since you have reached the limit for sharing the product(s) on Google+1, you cannot earn points anymore.' ,
                    'newids'  => 'rs_restriction_message_for_gplus' ,
                    'type'    => 'text' ,
                ) ,
                array(
                    'name'    => __( 'OK.ru' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_ok_restriction' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_ok_restriction' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'By enabling this checkbox, you can restrict the users to earn points for sharing the product(s) per day.' , SRP_LOCALE )
                ) ,
                array(
                    'name'              => __( 'Enter the value' , SRP_LOCALE ) ,
                    'id'                => 'rs_no_of_ok_count' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'newids'            => 'rs_no_of_ok_count' ,
                    'type'              => 'number' ,
                    'custom_attributes' => array(
                        'min' => 0
                    )
                ) ,
                array(
                    'name'    => __( 'Notification' , SRP_LOCALE ) ,
                    'id'      => 'rs_restriction_message_for_ok' ,
                    'std'     => 'Since you have reached the limit for sharing the product(s) on OK.ru, you cannot earn points anymore.' ,
                    'default' => 'Since you have reached the limit for sharing the product(s) on OK.ru, you cannot earn points anymore.' ,
                    'newids'  => 'rs_restriction_message_for_ok' ,
                    'type'    => 'text' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_socialrewards_restriction' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Social Button Position Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_point_socialrewards_position_settings'
                ) ,
                array(
                    'name'    => __( 'Social Buttons display Position in Single Product Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_global_position_sumo_social_buttons' ,
                    'std'     => '5' ,
                    'default' => '5' ,
                    'desc'    => __( 'Some theme do not support all the positions. If the position not support then it might result in a JQuery Conflict.' , SRP_LOCALE ) ,
                    'newids'  => 'rs_global_position_sumo_social_buttons' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Before Single Product' , SRP_LOCALE ) ,
                        '2' => __( 'Before Single Product Summary' , SRP_LOCALE ) ,
                        '3' => __( 'Single Product Summary' , SRP_LOCALE ) ,
                        '4' => __( 'After Single Product' , SRP_LOCALE ) ,
                        '5' => __( 'After Single Product Summary' , SRP_LOCALE ) ,
                        '6' => __( 'After Product Meta End' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Social Buttons display Position in Post/Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_global_position_sumo_social_share_buttons' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'desc'    => __( 'Some theme do not support all the positions. If the position not support then it might result in a JQuery Conflict.' , SRP_LOCALE ) ,
                    'newids'  => 'rs_global_position_sumo_social_share_buttons' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'After Header' , SRP_LOCALE ) ,
                        '2' => __( 'Before Footer' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Display Social Buttons as' , SRP_LOCALE ) ,
                    'id'      => 'rs_display_position_social_buttons' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_display_position_social_buttons' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Horizontal' , SRP_LOCALE ) ,
                        '2' => __( 'Vertical' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Social Buttons Position display type' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_button_position_troubleshoot' ,
                    'newids'   => 'rs_social_button_position_troubleshoot' ,
                    'std'      => 'inline' ,
                    'default'  => 'inline' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        'inline'       => __( 'Inline' , SRP_LOCALE ) ,
                        'inline-block' => __( 'Inline Block' , SRP_LOCALE ) ,
                        'inline-flex'  => __( 'Inline Flex' , SRP_LOCALE ) ,
                        'inline-table' => __( 'Inline Table' , SRP_LOCALE ) ,
                        'table'        => __( 'Table' , SRP_LOCALE ) ,
                        'block'        => __( 'Block' , SRP_LOCALE ) ,
                        'flex'         => __( 'Flex' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_socialrewards_position_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'ToolTip Color Customization' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_global_social_color_customization'
                ) ,
                array(
                    'name'     => __( 'ToolTip Background Color' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter ToolTip Background Color' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_tooltip_bg_color' ,
                    'std'      => '000' ,
                    'default'  => '000' ,
                    'type'     => 'text' ,
                    'class'    => 'color' ,
                    'newids'   => 'rs_social_tooltip_bg_color' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'ToolTip Text Color' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter ToolTip Text Color' , SRP_LOCALE ) ,
                    'id'       => 'rs_social_tooltip_text_color' ,
                    'std'      => 'fff' ,
                    'default'  => 'fff' ,
                    'type'     => 'text' ,
                    'class'    => 'color' ,
                    'newids'   => 'rs_social_tooltip_text_color' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_global_social_color_customization' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Shortcodes used in Social Reward' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_shortcodes_in_social_reward' ,
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<b>[facebook_like_reward_points]</b> - To display earning points for facebook like<br><br>'
                    . '<b>[facebook_share_reward_points]</b> - To display earning points for facebook share<br><br>'
                    . '<b>[twitter_tweet_reward_points]</b> - To display earning points for twitter tweet<br><br>'
                    . '<b>[twitter_follow_reward_points]</b> - To display earning points for twitter follow<br><br>'
                    . '<b>[google_share_reward_points]</b> - To display earning points for google share<br><br>'
                    . '<b>[vk_reward_points]</b> - To display earning points for vk like<br><br>'
                    . '<b>[instagram_reward_points]</b> - To display earning points for instagram follow<br><br>'
                    . '<b>[ok_share_reward_points]</b> - To display earning points for ok share' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_shortcodes_in_social_reward' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields( RSSocialReward::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSSocialReward::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_select_particular_social_products' ] ) ) {
                update_option( 'rs_select_particular_social_products' , $_POST[ 'rs_select_particular_social_products' ] ) ;
            } else {
                update_option( 'rs_select_particular_social_products' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_social_reward_module_checkbox' ] ) ) {
                update_option( 'rs_social_reward_activated' , $_POST[ 'rs_social_reward_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_social_reward_activated' , 'no' ) ;
            }
            if ( isset( $_POST[ 'rs_include_products_for_social_reward' ] ) ) {
                update_option( 'rs_include_products_for_social_reward' , $_POST[ 'rs_include_products_for_social_reward' ] ) ;
            } else {
                update_option( 'rs_include_products_for_social_reward' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_exclude_products_for_social_reward' ] ) ) {
                update_option( 'rs_exclude_products_for_social_reward' , $_POST[ 'rs_exclude_products_for_social_reward' ] ) ;
            } else {
                update_option( 'rs_exclude_products_for_social_reward' , '' ) ;
            }
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSSocialReward::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function reset_social_reward_module() {
            $settings = RSSocialReward::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_social_reward_activated' ) , 'rs_social_reward_module_checkbox' , 'rs_social_reward_activated' ) ;
        }

        public static function rs_select_products_to_update_social( $value ) {
            $field_id    = "rs_select_particular_social_products" ;
            $field_label = "Select Particular Products" ;
            $getproducts = get_option( 'rs_select_particular_social_products' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        public static function rs_hide_bulk_update_for_social_reward_start() {
            ?>
            <div class="rs_hide_bulk_update_for_social_reward_start">
                <?php
            }

            public static function rs_hide_bulk_update_for_social_reward_end() {
                ?>
            </div>
            <?php
        }

        public static function rs_save_button_for_update_social() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">                    
                </th>
                <td class="forminp forminp-select">
                    <input type="submit" class="rs_sumo_reward_button_social button-primary rs_save_update" value="Save and Update"/>
                    <div class='rs_sumo_rewards_social' style='margin-bottom:10px; margin-top:10px; color:green;'></div>
                </td>
            </tr>
            <?php
        }

        public static function rs_include_products_for_social_reward() {
            $field_id    = "rs_include_products_for_social_reward" ;
            $field_label = "Include Product(s)" ;
            $getproducts = get_option( 'rs_include_products_for_social_reward' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        public static function rs_exclude_products_for_social_reward() {
            $field_id    = "rs_exclude_products_for_social_reward" ;
            $field_label = "Exclude Product(s)" ;
            $getproducts = get_option( 'rs_exclude_products_for_social_reward' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

    }

    RSSocialReward::init() ;
}