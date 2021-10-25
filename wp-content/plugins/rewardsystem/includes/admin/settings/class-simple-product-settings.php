<?php
/*
 * Simple Product Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSSimpleProduct' ) ) {

    class RSSimpleProduct {

        public static function init() {

            add_action( 'woocommerce_product_options_general_product_data' , array( __CLASS__ , 'settings_for_simple_product' ) ) ;

            add_action( 'woocommerce_product_options_advanced' , array( __CLASS__ , 'setting_for_social_actions' ) ) ;

            add_action( 'woocommerce_product_options_advanced' , array( __CLASS__ , 'setting_for_point_pricing' ) ) ;

            add_action( 'woocommerce_process_product_meta' , array( __CLASS__ , 'save_settings' ) , 10 , 2 ) ;
        }

        /* Product and Referral Product Purchase Settings for Simple Product */

        public static function settings_for_simple_product() {
            if ( ! is_admin() )
                return ;
            ?>
            <div class="options_group show_if_simple show_if_subscription show_if_booking show_if_external">
                <?php
                if ( get_option( 'rs_product_purchase_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' ) {
                    woocommerce_wp_select( array(
                        'id'          => '_rewardsystemcheckboxvalue' ,
                        'class'       => 'rewardsystemcheckboxvalue' ,
                        'desc_tip'    => true ,
                        'description' => __( 'Enable will Turn On Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                                . 'Disable will Turn Off Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. ' , SRP_LOCALE ) ,
                        'label'       => __( 'Enable SUMO Reward Points for Product Purchase' , SRP_LOCALE ) ,
                        'options'     => array(
                            'no'  => __( 'Disable' , SRP_LOCALE ) ,
                            'yes' => __( 'Enable' , SRP_LOCALE ) ,
                        )
                            )
                    ) ;
                    woocommerce_wp_select( array(
                        'id'      => '_rewardsystem_options' ,
                        'class'   => 'rewardsystem_options show_if_enable' ,
                        'label'   => __( 'Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                        )
                            )
                    ) ;
                    woocommerce_wp_text_input(
                            array(
                                'id'          => '_rewardsystempoints' ,
                                'class'       => 'show_if_enable' ,
                                'name'        => '_rewardsystempoints' ,
                                'desc_tip'    => true ,
                                'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                                'label'       => __( 'Reward Points' , SRP_LOCALE ) ,
                            )
                    ) ;
                    woocommerce_wp_text_input(
                            array(
                                'id'          => '_rewardsystempercent' ,
                                'class'       => 'show_if_enable' ,
                                'name'        => '_rewardsystempercent' ,
                                'desc_tip'    => true ,
                                'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                                'label'       => __( 'Reward Points in Percent %' , SRP_LOCALE )
                            )
                    ) ;
                }
                if ( get_option( 'rs_buyingpoints_activated' ) == 'yes' ) {
                    woocommerce_wp_select( array(
                        'id'      => '_rewardsystem_buying_reward_points' ,
                        'class'   => '_rewardsystem_buying_reward_points' ,
                        'label'   => __( 'Enable Buying of SUMO Reward Points' , SRP_LOCALE ) ,
                        'options' => array(
                            'no'  => __( 'Disable' , SRP_LOCALE ) ,
                            'yes' => __( 'Enable' , SRP_LOCALE ) ,
                        )
                    ) ) ;
                    woocommerce_wp_text_input(
                            array(
                                'id'    => '_rewardsystem_assign_buying_points' ,
                                'class' => 'show_if_buy_reward_points_enable' ,
                                'name'  => '_rewardsystem_assign_buying_points' ,
                                'label' => __( 'Buy Reward Points' , SRP_LOCALE )
                    ) ) ;
                }
                if ( get_option( 'rs_referral_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) == 'yes' ) {
                    woocommerce_wp_select( array(
                        'id'          => '_rewardsystemreferralcheckboxvalue' ,
                        'class'       => '_rewardsystemreferralcheckboxvalue' ,
                        'desc_tip'    => true ,
                        'description' => __( 'Enable will Turn On Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                                . 'Disable will Turn Off Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. ' , SRP_LOCALE ) ,
                        'label'       => __( 'Enable Referral Reward Points for Product Purchase' , SRP_LOCALE ) ,
                        'options'     => array(
                            'no'  => __( 'Disable' , SRP_LOCALE ) ,
                            'yes' => __( 'Enable' , SRP_LOCALE ) ,
                        )
                            )
                    ) ;
                    woocommerce_wp_select( array(
                        'id'      => '_referral_rewardsystem_options' ,
                        'class'   => 'referral_rewardsystem_options show_if_referral_enable' ,
                        'label'   => __( 'Referral Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                        )
                            )
                    ) ;
                    woocommerce_wp_text_input(
                            array(
                                'id'          => '_referralrewardsystempoints' ,
                                'class'       => 'show_if_referral_enable' ,
                                'name'        => '_referralrewardsystempoints' ,
                                'desc_tip'    => true ,
                                'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                                'label'       => __( 'Referral Reward Points' , SRP_LOCALE )
                            )
                    ) ;
                    woocommerce_wp_text_input(
                            array(
                                'id'          => '_referralrewardsystempercent' ,
                                'class'       => 'show_if_referral_enable' ,
                                'name'        => '_referralrewardsystempercent' ,
                                'desc_tip'    => true ,
                                'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                                'label'       => __( 'Referral Reward Points in Percent %' , SRP_LOCALE )
                            )
                    ) ;

                    woocommerce_wp_select( array(
                        'id'      => '_referral_rewardsystem_options_getrefer' ,
                        'class'   => 'referral_rewardsystem_options_get show_if_referral_enable' ,
                        'label'   => __( 'Getting Referred Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                        )
                            )
                    ) ;

                    woocommerce_wp_text_input(
                            array(
                                'id'          => '_referralrewardsystempoints_for_getting_referred' ,
                                'class'       => 'show_if_referral_enable' ,
                                'name'        => '_referralrewardsystempoints_for_getting_referred' ,
                                'desc_tip'    => true ,
                                'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                                'label'       => __( 'Reward Points for Getting Referred' , SRP_LOCALE )
                            )
                    ) ;

                    woocommerce_wp_text_input(
                            array(
                                'id'          => '_referralrewardsystempercent_for_getting_referred' ,
                                'class'       => 'show_if_referral_enable' ,
                                'name'        => '_referralrewardsystempercent_for_getting_referred' ,
                                'desc_tip'    => true ,
                                'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                                'label'       => __( ' Reward Points in Percent % for Getting Referred' , SRP_LOCALE )
                            )
                    ) ;
                }
                ?>
            </div>
            <?php
        }

        /* Social Action Settings */

        public static function setting_for_social_actions() {
            if ( ! is_admin() )
                return ;

            if ( get_option( 'rs_social_reward_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_enable_product_category_level_for_social_reward' ) != 'yes' )
                return ;

            woocommerce_wp_select( array(
                'id'          => '_socialrewardsystemcheckboxvalue' ,
                'class'       => 'socialrewardsystemcheckboxvalue' ,
                'desc_tip'    => true ,
                'description' => __( 'Enable will Turn On Reward Points for Product Purchase and Category/Global Settings will be considered when applicable. '
                        . 'Disable will Turn Off Reward Points for Product Purchase and Category/Global Settings will not be considered. ' , SRP_LOCALE ) ,
                'label'       => __( 'Enable SUMO Reward Points for Social Promotion' , SRP_LOCALE ) ,
                'options'     => array(
                    'no'  => __( 'Disable' , SRP_LOCALE ) ,
                    'yes' => __( 'Enable' , SRP_LOCALE ) ,
                )
                    )
            ) ;

            woocommerce_wp_select(
                    array(
                        'id'      => '_social_rewardsystem_options_facebook' ,
                        'class'   => 'social_rewardsystem_options_facebook show_if_social_enable' ,
                        'label'   => __( 'Facebook Like Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE )
                        )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempoints_facebook' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempoints_facebook' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Facebook Like Reward Points' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempercent_facebook' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempercent_facebook' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Facebook Like Reward Points in Percent %' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_select(
                    array(
                        'id'      => '_social_rewardsystem_options_facebook_share' ,
                        'class'   => ' _social_rewardsystem_options_facebook_share show_if_social_enable' ,
                        'label'   => __( 'Facebook Share Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE )
                        )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempoints_facebook_share' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempoints_facebook_share' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Facebook Share Reward Points' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempercent_facebook_share' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempercent_facebook_share' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Facebook Share Reward Points in Percent %' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_select(
                    array(
                        'id'      => '_social_rewardsystem_options_twitter' ,
                        'class'   => 'social_rewardsystem_options_twitter show_if_social_enable' ,
                        'label'   => __( 'Twitter Tweet Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE )
                        )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempoints_twitter' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempoints_twitter' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Twitter Tweet Reward Points' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempercent_twitter' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempercent_twitter' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Twitter Tweet Reward Percent %' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_select(
                    array(
                        'id'      => '_social_rewardsystem_options_twitter_follow' ,
                        'class'   => '_social_rewardsystem_options_twitter_follow show_if_social_enable' ,
                        'label'   => __( 'Twitter Follow Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE )
                        )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempoints_twitter_follow' ,
                        'class'       => '_socialrewardsystempoints_twitter_follow_field show_if_social_enable' ,
                        'name'        => '_socialrewardsystempoints_twitter_follow' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Twitter Follow Reward Points' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempercent_twitter_follow' ,
                        'class'       => '_socialrewardsystempercent_twitter_follow_field show_if_social_enable' ,
                        'name'        => '_socialrewardsystempercent_twitter_follow' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Twitter Follow Reward Percent %' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_select(
                    array(
                        'id'      => '_social_rewardsystem_options_google' ,
                        'class'   => 'social_rewardsystem_options_google show_if_social_enable' ,
                        'label'   => __( 'Google+1 Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE )
                        )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempoints_google' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempoints_google' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Google+1 Reward Points' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempercent_google' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempercent_google' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Google+1 Reward Percent %' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_select(
                    array(
                        'id'      => '_social_rewardsystem_options_vk' ,
                        'class'   => 'social_rewardsystem_options_vk show_if_social_enable' ,
                        'label'   => __( 'VK.com Like Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE )
                        )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempoints_vk' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempoints_vk' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'VK.com Like Reward Points ' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempercent_vk' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempercent_vk' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'VK.com Like Reward Percent %' , SRP_LOCALE )
                    )
            ) ;

            woocommerce_wp_select(
                    array(
                        'id'      => '_social_rewardsystem_options_instagram' ,
                        'class'   => '_social_rewardsystem_options_instagram show_if_social_enable' ,
                        'label'   => __( 'Instagram Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE )
                        )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempoints_instagram' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempoints_instagram' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Instagram Reward Points ' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempercent_instagram' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempercent_instagram' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'Instagram Reward Percent %' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_select(
                    array(
                        'id'      => '_social_rewardsystem_options_ok_follow' ,
                        'class'   => '_social_rewardsystem_options_ok_follow show_if_social_enable' ,
                        'label'   => __( 'OK.ru Share Reward Type' , SRP_LOCALE ) ,
                        'options' => array(
                            '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                            '2' => __( 'By Percentage of Product Price' , SRP_LOCALE )
                        )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempoints_ok_follow' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempoints_ok_follow' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                        'label'       => __( 'OK.ru Share Reward Points' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'          => '_socialrewardsystempercent_ok_follow' ,
                        'class'       => 'show_if_social_enable' ,
                        'name'        => '_socialrewardsystempercent_ok_follow' ,
                        'desc_tip'    => true ,
                        'description' => __( 'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.' , SRP_LOCALE ) ,
                        'label'       => __( 'OK.ru Share Reward Percent %' , SRP_LOCALE )
                    )
            ) ;
        }

        /* Point Price Settings */

        public static function setting_for_point_pricing() {
            if ( ! is_admin() )
                return ;

            if ( get_option( 'rs_point_price_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_enable_product_category_level_for_points_price' ) != 'yes' )
                return ;

            if ( get_option( 'rs_enable_disable_point_priceing' ) == 2 )
                return ;

            global $post ;
            $ProductObj = srp_product_object( $post->ID ) ;
            if ( srp_product_type( $post->ID ) == 'variation' || srp_product_type( $post->ID ) == 'variable' )
                return ;

            woocommerce_wp_select( array(
                'id'      => '_rewardsystem_enable_point_price' ,
                'class'   => '_rewardsystem_enable_point_price' ,
                'label'   => __( 'Enable Point Pricing' , SRP_LOCALE ) ,
                'options' => array(
                    'no'  => __( 'Disable' , SRP_LOCALE ) ,
                    'yes' => __( 'Enable' , SRP_LOCALE ) ,
                )
            ) ) ;


            woocommerce_wp_select( array(
                'id'      => '_rewardsystem_enable_point_price_type' ,
                'class'   => '_rewardsystem_enable_point_price_type' ,
                'label'   => __( 'Pricing Display Type' , SRP_LOCALE ) ,
                'options' => array(
                    '1' => __( 'Currency & Point Price' , SRP_LOCALE ) ,
                    '2' => __( 'Only Point Price' , SRP_LOCALE ) ,
                ) ,
                'std'     => '1'
            ) ) ;

            woocommerce_wp_select( array(
                'id'      => '_rewardsystem_enable_point_price_type_booking' ,
                'class'   => '_rewardsystem_enable_point_price_type_booking' ,
                'label'   => __( 'Point Price Type' , SRP_LOCALE ) ,
                'options' => array(
                    '1' => __( 'By Fixed' , SRP_LOCALE ) ,
                ) ,
                'std'     => '1'
            ) ) ;
            woocommerce_wp_select( array(
                'id'      => '_rewardsystem_point_price_type' ,
                'class'   => '_rewardsystem_point_price_type' ,
                'label'   => __( 'Point Price Type' , SRP_LOCALE ) ,
                'options' => array(
                    '1' => __( 'By Fixed' , SRP_LOCALE ) ,
                    '2' => __( 'Based On Conversion' , SRP_LOCALE ) ,
                ) ,
                'std'     => '1'
            ) ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'    => '_rewardsystem__points' ,
                        'class' => '_rewardsystem__points' ,
                        'name'  => '_rewardsystem__points' ,
                        'label' => __( 'Points to Product' , SRP_LOCALE )
                    )
            ) ;
            woocommerce_wp_text_input(
                    array(
                        'id'       => '_rewardsystem__points_based_on_conversion' ,
                        'class'    => '_rewardsystem__points_based_on_conversion' ,
                        'name'     => '_rewardsystem__points_based_on_conversion' ,
                        'readonly' => "readonly" ,
                        'label'    => __( 'Points Based On Conversion' , SRP_LOCALE )
            ) ) ;
        }

        /* Save Product Level Settings */

        public static function save_settings( $post_id , $post ) {
            if ( ! is_admin() )
                return ;

            /* Save Buying Point Settings - Start */
            if ( get_option( 'rs_buyingpoints_activated' ) == 'yes' ) {
                if ( isset( $_POST[ '_rewardsystem_buying_reward_points' ] ) )
                    update_post_meta( $post_id , '_rewardsystem_buying_reward_points' , $_POST[ '_rewardsystem_buying_reward_points' ] ) ;

                if ( isset( $_POST[ '_rewardsystem_assign_buying_points' ] ) )
                    update_post_meta( $post_id , '_rewardsystem_assign_buying_points' , $_POST[ '_rewardsystem_assign_buying_points' ] ) ;
            }

            /* Save Buying Point Settings - End */

            /* Save Product Purchase Settings - Start */
            if ( get_option( 'rs_product_purchase_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' ) {
                if ( isset( $_POST[ '_rewardsystemcheckboxvalue' ] ) )
                    update_post_meta( $post_id , '_rewardsystemcheckboxvalue' , $_POST[ '_rewardsystemcheckboxvalue' ] ) ;

                if ( isset( $_POST[ '_rewardsystem_options' ] ) )
                    update_post_meta( $post_id , '_rewardsystem_options' , $_POST[ '_rewardsystem_options' ] ) ;

                if ( isset( $_POST[ '_rewardsystempoints' ] ) )
                    update_post_meta( $post_id , '_rewardsystempoints' , $_POST[ '_rewardsystempoints' ] ) ;

                if ( isset( $_POST[ '_rewardsystempercent' ] ) )
                    update_post_meta( $post_id , '_rewardsystempercent' , $_POST[ '_rewardsystempercent' ] ) ;
            }
            /* Save Product Purchase Settings - End */

            /* Save Referral Product Purchase Settings - Start */
            if ( get_option( 'rs_referral_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) == 'yes' ) {
                if ( isset( $_POST[ '_rewardsystemreferralcheckboxvalue' ] ) )
                    update_post_meta( $post_id , '_rewardsystemreferralcheckboxvalue' , $_POST[ '_rewardsystemreferralcheckboxvalue' ] ) ;

                if ( isset( $_POST[ '_referral_rewardsystem_options' ] ) )
                    update_post_meta( $post_id , '_referral_rewardsystem_options' , $_POST[ '_referral_rewardsystem_options' ] ) ;

                if ( isset( $_POST[ '_referralrewardsystempoints' ] ) )
                    update_post_meta( $post_id , '_referralrewardsystempoints' , $_POST[ '_referralrewardsystempoints' ] ) ;

                if ( isset( $_POST[ '_referralrewardsystempercent' ] ) )
                    update_post_meta( $post_id , '_referralrewardsystempercent' , $_POST[ '_referralrewardsystempercent' ] ) ;

                if ( isset( $_POST[ '_referralrewardsystempoints_for_getting_referred' ] ) )
                    update_post_meta( $post_id , '_referralrewardsystempoints_for_getting_referred' , $_POST[ '_referralrewardsystempoints_for_getting_referred' ] ) ;

                if ( isset( $_POST[ '_referral_rewardsystem_options_getrefer' ] ) )
                    update_post_meta( $post_id , '_referral_rewardsystem_options_getrefer' , $_POST[ '_referral_rewardsystem_options_getrefer' ] ) ;

                if ( isset( $_POST[ '_referralrewardsystempercent_for_getting_referred' ] ) )
                    update_post_meta( $post_id , '_referralrewardsystempercent_for_getting_referred' , $_POST[ '_referralrewardsystempercent_for_getting_referred' ] ) ;
            }
            /* Save Referral Product Purchase Settings - End */

            /* Save Social Action Settings - Start */
            if ( get_option( 'rs_social_reward_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_social_reward' ) == 'yes' ) {
                if ( isset( $_POST[ '_socialrewardsystemcheckboxvalue' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystemcheckboxvalue' , $_POST[ '_socialrewardsystemcheckboxvalue' ] ) ;

                if ( isset( $_POST[ '_social_rewardsystem_options_facebook' ] ) )
                    update_post_meta( $post_id , '_social_rewardsystem_options_facebook' , $_POST[ '_social_rewardsystem_options_facebook' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempoints_facebook' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempoints_facebook' , $_POST[ '_socialrewardsystempoints_facebook' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempercent_facebook' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempercent_facebook' , $_POST[ '_socialrewardsystempercent_facebook' ] ) ;

                if ( isset( $_POST[ '_social_rewardsystem_options_facebook_share' ] ) )
                    update_post_meta( $post_id , '_social_rewardsystem_options_facebook_share' , $_POST[ '_social_rewardsystem_options_facebook_share' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempoints_facebook_share' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempoints_facebook_share' , $_POST[ '_socialrewardsystempoints_facebook_share' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempercent_facebook_share' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempercent_facebook_share' , $_POST[ '_socialrewardsystempercent_facebook_share' ] ) ;

                if ( isset( $_POST[ '_social_rewardsystem_options_twitter' ] ) )
                    update_post_meta( $post_id , '_social_rewardsystem_options_twitter' , $_POST[ '_social_rewardsystem_options_twitter' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempoints_twitter' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempoints_twitter' , $_POST[ '_socialrewardsystempoints_twitter' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempercent_twitter' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempercent_twitter' , $_POST[ '_socialrewardsystempercent_twitter' ] ) ;

                if ( isset( $_POST[ '_social_rewardsystem_options_twitter_follow' ] ) )
                    update_post_meta( $post_id , '_social_rewardsystem_options_twitter_follow' , $_POST[ '_social_rewardsystem_options_twitter_follow' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempoints_twitter_follow' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempoints_twitter_follow' , $_POST[ '_socialrewardsystempoints_twitter_follow' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempercent_twitter_follow' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempercent_twitter_follow' , $_POST[ '_socialrewardsystempercent_twitter_follow' ] ) ;

                if ( isset( $_POST[ '_social_rewardsystem_options_google' ] ) )
                    update_post_meta( $post_id , '_social_rewardsystem_options_google' , $_POST[ '_social_rewardsystem_options_google' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempoints_google' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempoints_google' , $_POST[ '_socialrewardsystempoints_google' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempercent_google' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempercent_google' , $_POST[ '_socialrewardsystempercent_google' ] ) ;

                if ( isset( $_POST[ '_social_rewardsystem_options_vk' ] ) )
                    update_post_meta( $post_id , '_social_rewardsystem_options_vk' , $_POST[ '_social_rewardsystem_options_vk' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempoints_vk' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempoints_vk' , $_POST[ '_socialrewardsystempoints_vk' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempercent_vk' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempercent_vk' , $_POST[ '_socialrewardsystempercent_vk' ] ) ;

                if ( isset( $_POST[ '_social_rewardsystem_options_instagram' ] ) )
                    update_post_meta( $post_id , '_social_rewardsystem_options_instagram' , $_POST[ '_social_rewardsystem_options_instagram' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempoints_instagram' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempoints_instagram' , $_POST[ '_socialrewardsystempoints_instagram' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempercent_instagram' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempercent_instagram' , $_POST[ '_socialrewardsystempercent_instagram' ] ) ;

                if ( isset( $_POST[ '_social_rewardsystem_options_ok_follow' ] ) )
                    update_post_meta( $post_id , '_social_rewardsystem_options_ok_follow' , $_POST[ '_social_rewardsystem_options_ok_follow' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempoints_ok_follow' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempoints_ok_follow' , $_POST[ '_socialrewardsystempoints_ok_follow' ] ) ;

                if ( isset( $_POST[ '_socialrewardsystempercent_ok_follow' ] ) )
                    update_post_meta( $post_id , '_socialrewardsystempercent_ok_follow' , $_POST[ '_socialrewardsystempercent_ok_follow' ] ) ;
            }
            /* Save Social Action Settings - End */

            /* Save Point Price Settings - Start */
            if ( get_option( 'rs_point_price_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_points_price' ) == 'yes' ) {
                if ( isset( $_POST[ '_rewardsystem_enable_point_price' ] ) )
                    update_post_meta( $post_id , '_rewardsystem_enable_point_price' , $_POST[ '_rewardsystem_enable_point_price' ] ) ;

                if ( isset( $_POST[ '_rewardsystem__points' ] ) )
                    update_post_meta( $post_id , '_rewardsystem__points' , $_POST[ '_rewardsystem__points' ] ) ;

                if ( isset( $_POST[ '_rewardsystem_point_price_type' ] ) )
                    update_post_meta( $post_id , '_rewardsystem_point_price_type' , $_POST[ '_rewardsystem_point_price_type' ] ) ;

                if ( isset( $_POST[ '_rewardsystem_enable_point_price_type' ] ) )
                    update_post_meta( $post_id , '_rewardsystem_enable_point_price_type' , $_POST[ '_rewardsystem_enable_point_price_type' ] ) ;

                if ( isset( $_POST[ '_rewardsystem__points_based_on_conversion' ] ) ) {
                    $Price          = $_POST[ '_sale_price' ] == '' ? $_POST[ '_regular_price' ] : $_POST[ '_sale_price' ] ;
                    $ConvertedValue = redeem_point_conversion( $Price , get_current_user_id() ) ;
                    update_post_meta( $post_id , '_rewardsystem__points_based_on_conversion' , $ConvertedValue ) ;
                }
            }
            /* Save Point Price Settings - End */
        }

    }

    RSSimpleProduct::init() ;
}