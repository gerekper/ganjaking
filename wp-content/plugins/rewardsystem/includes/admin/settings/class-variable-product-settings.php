<?php

/*
 * Variable Product Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSVariableProduct' ) ) {

    class RSVariableProduct {

        public static function init() {
            add_action( 'woocommerce_product_after_variable_attributes' , array( __CLASS__ , 'settings_for_variable_product' ) , 10 , 3 ) ;

            add_action( 'woocommerce_save_product_variation' , array( __CLASS__ , 'save_settings' ) , 10 , 2 ) ;
        }

        public static function settings_for_variable_product( $loop , $variation_data , $variations ) {
            if ( ! is_admin() )
                return ;

            wp_enqueue_script( 'fp_variable_product' , SRP_PLUGIN_DIR_URL . "assets/js/tab/fp-variation-product.js" , array() , SRP_VERSION ) ;
            wp_localize_script( 'fp_variable_product' , 'fp_variation_params' , array( 'loop' => $loop ) ) ;
            $variation_data = get_post_meta( $variations->ID ) ;
            if ( get_option( 'rs_point_price_activated' ) == 'yes' && get_option( 'rs_enable_disable_point_priceing' ) == '1' && get_option( 'rs_enable_product_category_level_for_points_price' ) == 'yes' ) {
                $EnablePointPrice = isset( $variation_data[ '_enable_reward_points_price' ][ 0 ] ) ? $variation_data[ '_enable_reward_points_price' ][ 0 ] : '' ;
                woocommerce_wp_select(
                        array(
                            'id'      => '_enable_reward_points_price[' . $loop . ']' ,
                            'label'   => __( 'Enable Point Pricing' , SRP_LOCALE ) ,
                            'class'   => '_enable_reward_points_price_variation' ,
                            'value'   => $EnablePointPrice ,
                            'default' => '1' ,
                            'options' => array(
                                '1' => __( 'Enable' , SRP_LOCALE ) ,
                                '2' => __( 'Disable' , SRP_LOCALE ) ,
                            )
                        )
                ) ;

                $PricingType = isset( $variation_data[ '_enable_reward_points_pricing_type' ][ 0 ] ) ? $variation_data[ '_enable_reward_points_pricing_type' ][ 0 ] : '' ;
                woocommerce_wp_select(
                        array(
                            'id'      => '_enable_reward_points_pricing_type[' . $loop . ']' ,
                            'label'   => __( 'Pricing Display Type' , SRP_LOCALE ) ,
                            'value'   => $PricingType ,
                            'class'   => 'fp_point_price' ,
                            'default' => '1' ,
                            'options' => array(
                                '1' => __( 'Currency & Points' , SRP_LOCALE ) ,
                                '2' => __( 'Points Only' , SRP_LOCALE ) ,
                            )
                        )
                ) ;

                $PointPriceType = isset( $variation_data[ '_enable_reward_points_price_type' ][ 0 ] ) ? $variation_data[ '_enable_reward_points_price_type' ][ 0 ] : '' ;
                woocommerce_wp_select(
                        array(
                            'id'      => '_enable_reward_points_price_type[' . $loop . ']' ,
                            'label'   => __( 'Point Price Type' , SRP_LOCALE ) ,
                            'value'   => $PointPriceType ,
                            'class'   => 'fp_point_price_currency' ,
                            'default' => '1' ,
                            'options' => array(
                                '1' => __( 'By Fixed' , SRP_LOCALE ) ,
                                '2' => __( 'Based On Conversion' , SRP_LOCALE ) ,
                            )
                        )
                ) ;

                $PointPriceValue = isset( $variation_data[ '_price_points_based_on_conversion' ][ 0 ] ) ? $variation_data[ '_price_points_based_on_conversion' ][ 0 ] : '' ;
                woocommerce_wp_text_input(
                        array(
                            'id'    => '_price_points_based_on_conversion[' . $loop . ']' ,
                            'label' => __( 'Point Price Based on Conversion' , SRP_LOCALE ) ,
                            'class' => 'fp_variation_points_price' ,
                            'size'  => '5' ,
                            'value' => $PointPriceValue ,
                        )
                ) ;

                $FixedPointPrice = isset( $variation_data[ 'price_points' ][ 0 ] ) ? $variation_data[ 'price_points' ][ 0 ] : '' ;
                woocommerce_wp_text_input(
                        array(
                            'id'    => 'price_points[' . $loop . ']' ,
                            'label' => __( 'By Fixed Point Price' , SRP_LOCALE ) ,
                            'size'  => '5' ,
                            'class' => 'fp_variation_points_price_field' ,
                            'value' => $FixedPointPrice ,
                        )
                ) ;
            }
            if ( get_option( 'rs_product_purchase_activated' ) == 'yes' ) {
                if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' ) {
                    $EnableRewardPoint = isset( $variation_data[ '_enable_reward_points' ][ 0 ] ) ? $variation_data[ '_enable_reward_points' ][ 0 ] : '' ;
                    woocommerce_wp_select(
                            array(
                                'id'          => '_enable_reward_points[' . $loop . ']' ,
                                'label'       => __( 'Enable SUMO Reward Points' , SRP_LOCALE ) ,
                                'default'     => '2' ,
                                'desc_tip'    => true ,
                                'description' => __( 'Enable will Turn On Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                                        . 'Disable will Turn Off Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. ' , SRP_LOCALE ) ,
                                'value'       => $EnableRewardPoint ,
                                'options'     => array(
                                    '1' => __( 'Enable' , SRP_LOCALE ) ,
                                    '2' => __( 'Disable' , SRP_LOCALE ) ,
                                )
                            )
                    ) ;

                    $RewardType = isset( $variation_data[ '_select_reward_rule' ][ 0 ] ) ? $variation_data[ '_select_reward_rule' ][ 0 ] : '' ;
                    woocommerce_wp_select(
                            array(
                                'id'      => '_select_reward_rule[' . $loop . ']' ,
                                'label'   => __( 'Reward Type' , SRP_LOCALE ) ,
                                'default' => '2' ,
                                'value'   => $RewardType ,
                                'options' => array(
                                    '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                                    '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                                )
                            )
                    ) ;

                    $RewardPoints = isset( $variation_data[ '_reward_points' ][ 0 ] ) ? $variation_data[ '_reward_points' ][ 0 ] : '' ;
                    woocommerce_wp_text_input(
                            array(
                                'id'          => '_reward_points[' . $loop . ']' ,
                                'label'       => __( 'Reward Points' , SRP_LOCALE ) ,
                                'description' => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                                'desc_tip'    => true ,
                                'value'       => $RewardPoints
                            )
                    ) ;

                    $RewardPercent = isset( $variation_data[ '_reward_percent' ][ 0 ] ) ? $variation_data[ '_reward_percent' ][ 0 ] : '' ;
                    woocommerce_wp_text_input(
                            array(
                                'id'          => '_reward_percent[' . $loop . ']' ,
                                'label'       => __( 'Reward Points in Percent %' , SRP_LOCALE ) ,
                                'description' => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                                'desc_tip'    => true ,
                                'value'       => $RewardPercent
                            )
                    ) ;
                }
            }
            if ( get_option( 'rs_buyingpoints_activated' ) == 'yes' ) {
                $EnableBuyingPoint = isset( $variation_data[ '_rewardsystem_buying_reward_points' ][ 0 ] ) ? $variation_data[ '_rewardsystem_buying_reward_points' ][ 0 ] : '' ;
                woocommerce_wp_select(
                        array(
                            'id'      => '_rewardsystem_buying_reward_points[' . $loop . ']' ,
                            'label'   => __( 'Enable Buying of SUMO Reward Points' , SRP_LOCALE ) ,
                            'default' => '2' ,
                            'value'   => $EnableBuyingPoint ,
                            'options' => array(
                                '1' => __( 'Enable' , SRP_LOCALE ) ,
                                '2' => __( 'Disable' , SRP_LOCALE ) ,
                            )
                        )
                ) ;
                $BuyingPoints      = isset( $variation_data[ '_rewardsystem_assign_buying_points' ][ 0 ] ) ? $variation_data[ '_rewardsystem_assign_buying_points' ][ 0 ] : '' ;
                woocommerce_wp_text_input(
                        array(
                            'id'    => '_rewardsystem_assign_buying_points[' . $loop . ']' ,
                            'label' => __( 'Buy Reward Points' , SRP_LOCALE ) ,
                            'value' => $BuyingPoints
                        )
                ) ;
            }
            if ( get_option( 'rs_referral_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) == 'yes' ) {
                $EnableReferralPoint = isset( $variation_data[ '_enable_referral_reward_points' ][ 0 ] ) ? $variation_data[ '_enable_referral_reward_points' ][ 0 ] : '' ;
                woocommerce_wp_select(
                        array(
                            'id'          => '_enable_referral_reward_points[' . $loop . ']' ,
                            'label'       => __( 'Enable Referral Reward Points' , SRP_LOCALE ) ,
                            'default'     => '2' ,
                            'desc_tip'    => true ,
                            'description' => __( 'Enable will Turn On Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                                    . 'Disable will Turn Off Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available.' , SRP_LOCALE ) ,
                            'value'       => $EnableReferralPoint ,
                            'options'     => array(
                                '1' => __( 'Enable' , SRP_LOCALE ) ,
                                '2' => __( 'Disable' , SRP_LOCALE ) ,
                            )
                        )
                ) ;

                $ReferralRewardType = isset( $variation_data[ '_select_referral_reward_rule' ][ 0 ] ) ? $variation_data[ '_select_referral_reward_rule' ][ 0 ] : '' ;
                woocommerce_wp_select(
                        array(
                            'id'      => '_select_referral_reward_rule[' . $loop . ']' ,
                            'label'   => __( 'Referral Reward Type' , SRP_LOCALE ) ,
                            'default' => '2' ,
                            'value'   => $ReferralRewardType ,
                            'options' => array(
                                '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                                '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                            )
                        )
                ) ;

                $ReferralRewardPoints = isset( $variation_data[ '_referral_reward_points' ][ 0 ] ) ? $variation_data[ '_referral_reward_points' ][ 0 ] : '' ;
                woocommerce_wp_text_input(
                        array(
                            'id'          => '_referral_reward_points[' . $loop . ']' ,
                            'label'       => __( 'Referral Reward Points' , SRP_LOCALE ) ,
                            'description' => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                            'desc_tip'    => true ,
                            'value'       => $ReferralRewardPoints
                        )
                ) ;

                $ReferralRewardPercent = isset( $variation_data[ '_referral_reward_percent' ][ 0 ] ) ? $variation_data[ '_referral_reward_percent' ][ 0 ] : '' ;
                woocommerce_wp_text_input(
                        array(
                            'id'          => '_referral_reward_percent[' . $loop . ']' ,
                            'label'       => __( 'Referral Reward Points in Percent %' , SRP_LOCALE ) ,
                            'description' => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                            'desc_tip'    => true ,
                            'value'       => $ReferralRewardPercent
                        )
                ) ;

                $GettingReferRewardType = isset( $variation_data[ '_select_referral_reward_rule_getrefer' ][ 0 ] ) ? $variation_data[ '_select_referral_reward_rule_getrefer' ][ 0 ] : '' ;
                woocommerce_wp_select(
                        array(
                            'id'      => '_select_referral_reward_rule_getrefer[' . $loop . ']' ,
                            'label'   => __( 'Reward Type for Getting Referred' , SRP_LOCALE ) ,
                            'default' => '2' ,
                            'value'   => $GettingReferRewardType ,
                            'options' => array(
                                '1' => __( 'By Fixed Reward Points' , SRP_LOCALE ) ,
                                '2' => __( 'By Percentage of Product Price' , SRP_LOCALE ) ,
                            )
                        )
                ) ;

                $GettingReferPoints = isset( $variation_data[ '_referral_reward_points_getting_refer' ][ 0 ] ) ? $variation_data[ '_referral_reward_points_getting_refer' ][ 0 ] : '' ;
                woocommerce_wp_text_input(
                        array(
                            'id'          => '_referral_reward_points_getting_refer[' . $loop . ']' ,
                            'label'       => __( 'Reward Points for Getting Referred' , SRP_LOCALE ) ,
                            'description' => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                            'desc_tip'    => true ,
                            'value'       => $GettingReferPoints
                        )
                ) ;

                $GettingReferPercent = isset( $variation_data[ '_referral_reward_percent_getting_refer' ][ 0 ] ) ? $variation_data[ '_referral_reward_percent_getting_refer' ][ 0 ] : '' ;
                woocommerce_wp_text_input(
                        array(
                            'id'          => '_referral_reward_percent_getting_refer[' . $loop . ']' ,
                            'label'       => __( 'Reward Points in Percent % for Getting Referred' , SRP_LOCALE ) ,
                            'description' => __( 'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ' , SRP_LOCALE ) ,
                            'desc_tip'    => true ,
                            'value'       => $GettingReferPercent
                        )
                ) ;
            }
        }

        public static function save_settings( $variation_id , $i ) {
            if ( isset( $_POST[ '_enable_reward_points_price_type' ][ $i ] ) )
                update_post_meta( $variation_id , '_enable_reward_points_price_type' , stripslashes( $_POST[ '_enable_reward_points_price_type' ][ $i ] ) ) ;

            $Price          = isset( $_POST[ 'variable_sale_price' ][ $i ] ) ? $_POST[ 'variable_sale_price' ][ $i ] : $_POST[ 'variable_regular_price' ][ $i ] ;
            $PointPriceType = get_post_meta( $variation_id , '_enable_reward_points_price_type' , true ) ;
            if ( $PointPriceType == 2 ) {
                $Points = redeem_point_conversion( $Price , get_current_user_id() ) ;
                update_post_meta( $variation_id , '_price_points_based_on_conversion' , stripslashes( $Points ) ) ;
            }

            if ( isset( $_POST[ '_rewardsystem_buying_reward_points' ][ $i ] ) )
                update_post_meta( $variation_id , '_rewardsystem_buying_reward_points' , stripslashes( $_POST[ '_rewardsystem_buying_reward_points' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_rewardsystem_assign_buying_points' ][ $i ] ) )
                update_post_meta( $variation_id , '_rewardsystem_assign_buying_points' , stripslashes( $_POST[ '_rewardsystem_assign_buying_points' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_reward_points' ][ $i ] ) )
                update_post_meta( $variation_id , '_reward_points' , stripslashes( $_POST[ '_reward_points' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_enable_reward_points_price' ][ $i ] ) )
                update_post_meta( $variation_id , '_enable_reward_points_price' , stripslashes( $_POST[ '_enable_reward_points_price' ][ $i ] ) ) ;

            if ( isset( $_POST[ 'price_points' ][ $i ] ) )
                update_post_meta( $variation_id , 'price_points' , stripslashes( $_POST[ 'price_points' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_enable_reward_points_pricing_type' ][ $i ] ) )
                update_post_meta( $variation_id , '_enable_reward_points_pricing_type' , stripslashes( $_POST[ '_enable_reward_points_pricing_type' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_reward_percent' ][ $i ] ) )
                update_post_meta( $variation_id , '_reward_percent' , stripslashes( $_POST[ '_reward_percent' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_select_reward_rule' ][ $i ] ) )
                update_post_meta( $variation_id , '_select_reward_rule' , stripslashes( $_POST[ '_select_reward_rule' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_referral_reward_points' ][ $i ] ) )
                update_post_meta( $variation_id , '_referral_reward_points' , stripslashes( $_POST[ '_referral_reward_points' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_referral_reward_percent' ][ $i ] ) )
                update_post_meta( $variation_id , '_referral_reward_percent' , stripslashes( $_POST[ '_referral_reward_percent' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_referral_reward_percent_getting_refer' ][ $i ] ) )
                update_post_meta( $variation_id , '_referral_reward_percent_getting_refer' , stripslashes( $_POST[ '_referral_reward_percent_getting_refer' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_select_referral_reward_rule_getrefer' ][ $i ] ) )
                update_post_meta( $variation_id , '_select_referral_reward_rule_getrefer' , stripslashes( $_POST[ '_select_referral_reward_rule_getrefer' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_referral_reward_points_getting_refer' ][ $i ] ) )
                update_post_meta( $variation_id , '_referral_reward_points_getting_refer' , stripslashes( $_POST[ '_referral_reward_points_getting_refer' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_select_referral_reward_rule' ][ $i ] ) )
                update_post_meta( $variation_id , '_select_referral_reward_rule' , stripslashes( $_POST[ '_select_referral_reward_rule' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_enable_reward_points' ][ $i ] ) )
                update_post_meta( $variation_id , '_enable_reward_points' , stripslashes( $_POST[ '_enable_reward_points' ][ $i ] ) ) ;

            if ( isset( $_POST[ '_enable_referral_reward_points' ][ $i ] ) )
                update_post_meta( $variation_id , '_enable_referral_reward_points' , stripslashes( $_POST[ '_enable_referral_reward_points' ][ $i ] ) ) ;
        }

    }

    RSVariableProduct::init() ;
}