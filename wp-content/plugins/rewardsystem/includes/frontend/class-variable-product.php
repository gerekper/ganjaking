<?php
/*
 * Simple Product Functionality
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionforVariableProduct' ) ) {

    class RSFunctionforVariableProduct {

        public static function init() {

            if ( get_option( 'rs_msg_position_in_product_page' ) == '1' ) {

                add_action( 'woocommerce_before_single_product' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_before_single_product' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_before_single_product' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '2' ) {

                add_action( 'woocommerce_before_single_product_summary' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_before_single_product_summary' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_before_single_product_summary' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '3' ) {

                add_action( 'woocommerce_single_product_summary' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_single_product_summary' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_single_product_summary' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '4' ) {

                add_action( 'woocommerce_after_single_product' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_after_single_product' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_after_single_product' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '5' ) {

                add_action( 'woocommerce_after_single_product_summary' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_after_single_product_summary' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_after_single_product_summary' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '6' ) {

                add_action( 'woocommerce_product_meta_end' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_product_meta_end' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_product_meta_end' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '7' ) {

                add_action( 'woocommerce_before_add_to_cart_quantity' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_before_add_to_cart_quantity' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_before_add_to_cart_quantity' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '8' ) {

                add_action( 'woocommerce_after_add_to_cart_quantity' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_after_add_to_cart_quantity' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_after_add_to_cart_quantity' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
            } else {

                add_action( 'woocommerce_after_add_to_cart_form' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_after_add_to_cart_form' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
                add_action( 'woocommerce_after_add_to_cart_form' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
            }

            /* Filter for to alter variation range for only point price products */
            add_filter( 'woocommerce_get_price_html' , array( __CLASS__ , 'display_variation_price' ) , 10 , 2 ) ;

            add_filter( 'woocommerce_get_price_html' , array( __CLASS__ , 'display_buying_point_msg_for_variation' ) , 9999 , 2 ) ;

            add_filter( 'woocommerce_ajax_variation_threshold' , array( __CLASS__ , 'set_variation_limit' ) , 999 , 2 ) ;
        }

        public static function display_msg_for_variable_product() {
            if ( get_option( 'rs_product_purchase_activated' ) == 'no' )
                return ;

            $banning_type = check_banning_type( get_current_user_id() ) ;
            if ( $banning_type == 'earningonly' || $banning_type == 'both' )
                return ;

            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == 2 )
                return ;

            global $post ;
            if ( get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) == 'yes' ) {
                $variationid  = get_variation_id( $post->ID ) ;
                $earnmessages = '' ;
                $earnmessage  = '' ;
                $image        = '' ;

                if ( srp_check_is_array( $variationid ) ) {
                    $varpointss = RSFunctionforSimpleProduct::rewardpoints_of_variation( $variationid[ 0 ] , $post->ID ) ;
                    if ( $varpointss != '' ) {
                        $CurrencyValue = redeem_point_conversion( $varpointss , get_current_user_id() , 'price' ) ;
                        $CurrencyValue = number_format( ( float ) round_off_type_for_currency( $CurrencyValue ) , get_option( 'woocommerce_price_num_decimals' ) ) ;
                        $message       = get_option( 'rs_message_for_variation_products' ) ;
                        $earnmessage   = str_replace( '[variationrewardpoints]' , round_off_type( $varpointss ) , $message ) ;
                        if ( get_option( 'woocommerce_currency_pos' ) == 'right' || get_option( 'woocommerce_currency_pos' ) == 'right_space' ) {
                            $CurrencyValue = $CurrencyValue . get_woocommerce_currency_symbol() ;
                        } elseif ( get_option( 'woocommerce_currency_pos' ) == 'left' || get_option( 'woocommerce_currency_pos' ) == 'left_space' ) {
                            $CurrencyValue = get_woocommerce_currency_symbol() . $CurrencyValue ;
                        }
                        $earnmessage  = str_replace( '[variationpointsvalue]' , $CurrencyValue , $earnmessage ) ;
                        $messages     = get_option( "rs_message_for_single_product_variation" ) ;
                        $earnmessages = str_replace( '[variationrewardpoints]' , round_off_type( $varpointss ) , $messages ) ;
                        if ( get_option( '_rs_enable_disable_gift_icon' ) == '1' ) {
                            if ( get_option( 'rs_image_url_upload' ) != '' ) {
                                $image = "<img class = 'gifticon' src=" . get_option( 'rs_image_url_upload' ) . " style='width:16px;height:16px;display:inline;' />&nbsp;" ;
                            }
                        }
                        $earnmessages = $image . $earnmessages ;
                    }
                }
            }
            ?>
            <div id='value_variable_product'></div>
            <?php if ( get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) == 'yes' ) { ?>
                <div id='value_variable_product1'></div>
                <?php
                if ( get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) == 'yes' ) {
                    if ( ($earnmessages != '' || $earnmessage != '' ) ) {
                        ?>
                        <script type='text/javascript'>
                            jQuery( document ).ready( function () {
                        <?php if ( get_option( 'rs_show_hide_message_for_variable_product' ) == '1' ) { ?>
                                    jQuery( '#value_variable_product1' ).addClass( 'woocommerce-info' ) ;
                                    jQuery( '#value_variable_product1' ).addClass( 'rs_message_for_single_product' ) ;
                                    jQuery( '#value_variable_product1' ).show() ;
                                    jQuery( '.gift_icon' ).hide() ;
                                    jQuery( '#value_variable_product1' ).html( "<?php echo $earnmessage ; ?>" ) ;
                        <?php } if ( get_option( 'rs_show_hide_message_for_variable_in_single_product_page' ) == '1' ) { ?>
                                    jQuery( '.variableshopmessage' ).show() ;
                                    jQuery( '.variableshopmessage' ).html( "<?php echo $earnmessages ; ?>" ) ;
                        <?php } ?>
                            } ) ;
                        </script>
                        <?php
                    }
                }
            }

            //Span Tag Added for Variations as a Troubleshoot Option in Version 24.3.6 ['The Issue' - Theme Compatibility].
            if ( '2' == get_option( 'rs_earn_message_display_hook' ) ) {
                ?> <span class = "rs_variable_earn_messages" style="display:none;" ></span> <?php
            }
        }

        public static function display_referral_msg_for_variable_product() {
            if ( get_option( 'rs_referral_activated' ) == 'no' )
                return ;

            $banning_type = check_banning_type( get_current_user_id() ) ;
            if ( $banning_type == 'earningonly' || $banning_type == 'both' )
                return ;

            if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) )
                return ;

            if ( get_option( 'rs_show_hide_message_for_single_product_guest_referral' ) != 1 )
                return ;

            if ( ! is_user_logged_in() && get_option( 'rs_referrer_earn_point_purchase_by_guest_users' ) != 'yes' )
                return ;
            ?>
            <div id='referral_value_variable_product'></div>
            <?php
        }

        public static function display_buying_points_msg_for_variable_product() {
            if ( get_option( 'rs_buyingpoints_activated' ) != 'yes' )
                return ;

            $banning_type = check_banning_type( get_current_user_id() ) ;
            if ( $banning_type == 'earningonly' || $banning_type == 'both' )
                return ;

            global $post ;
            if ( get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) == 'yes' ) {
                $variationid = get_variation_id( $post->ID ) ;
                if ( srp_check_is_array( $variationid ) ) {
                    if ( get_post_meta( $variationid[ 0 ] , '_rewardsystem_buying_reward_points' , true ) == 1 ) {
                        $BuyPoints = get_post_meta( $variationid[ 0 ] , '_rewardsystem_assign_buying_points' , true ) ;
                        $BuyPoints = empty( get_current_user_id() ) ? $BuyPoints : RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $BuyPoints ) ;

                        $CurrencyValueForBuyingPoints = redeem_point_conversion( $BuyPoints , get_current_user_id() , 'price' ) ;
                        $CurrencyValueForBuyingPoints = number_format( ( float ) round_off_type_for_currency( $CurrencyValueForBuyingPoints ) , get_option( 'woocommerce_price_num_decimals' ) ) ;
                        if ( get_option( 'woocommerce_currency_pos' ) == 'right' || get_option( 'woocommerce_currency_pos' ) == 'right_space' ) {
                            $CurrencyValueForBuyingPoints = $CurrencyValueForBuyingPoints . get_woocommerce_currency_symbol() ;
                        } elseif ( get_option( 'woocommerce_currency_pos' ) == 'left' || get_option( 'woocommerce_currency_pos' ) == 'left_space' ) {
                            $CurrencyValueForBuyingPoints = get_woocommerce_currency_symbol() . $CurrencyValueForBuyingPoints ;
                        }
                        $BuyMsg    = ( ! empty( $BuyPoints )) ? str_replace( array( '[buypoints]' , '[buypointvalue]' ) , array( $BuyPoints , $CurrencyValueForBuyingPoints ) , get_option( 'rs_buy_point_message_in_product_page_for_variable' ) ) : '' ;
                        $BuyingMsg = str_replace( '[variationbuyingpoint]' , round_off_type( $BuyPoints ) , get_option( 'rs_buy_point_message_for_variation_products' ) ) ;
                        $BuyingMsg = str_replace( '[variationbuyingpointvalue]' , $CurrencyValueForBuyingPoints , $BuyingMsg ) ;
                        if ( get_option( 'rs_buy_point_message_for_variation_products' ) != '' && $BuyPoints ) {
                            ?>
                            <script type='text/javascript'>
                                jQuery( document ).ready( function () {
                            <?php if ( get_option( 'rs_show_hide_buy_point_message_for_variable_product' ) == '1' ) { ?>
                                        jQuery( '#buy_Point_value_variable_product' ).addClass( 'woocommerce-info rs_message_for_single_product' ) ;
                                        jQuery( '#buy_Point_value_variable_product' ).show() ;
                                        jQuery( '.gift_icon' ).hide() ;
                                        jQuery( '#buy_Point_value_variable_product' ).html( "<?php echo $BuyingMsg ; ?>" ) ;
                                <?php
                            }
                            if ( get_option( 'rs_show_hide_buy_points_message_for_variable_in_product' ) == '1' ) {
                                if ( get_option( 'rs_product_purchase_activated' ) == 'yes' ) {
                                    ?>
                                            jQuery( '.variableshopmessage' ).show() ;
                                            jQuery( '.variableshopmessage' ).append( "<br><?php echo $BuyMsg ; ?><br>" ) ;
                                <?php } else { ?>
                                            jQuery( '.variableshopmessage' ).show() ;
                                            jQuery( '.variableshopmessage' ).html( "<?php echo $BuyMsg ; ?>" ) ;
                                    <?php
                                }
                            }
                            ?>
                                } ) ;
                            </script>
                            <?php
                        }
                    }
                }
            }
            ?>
            <div id='buy_Point_value_variable_product'></div>
            <?php
        }

        public static function display_variation_price( $price , $ProductObj ) {
            if ( is_product() || is_shop() || is_page() || is_product_category() || is_tax( 'pwb-brand' ) ) {
                if ( ! is_user_logged_in() )
                    return $price ;

                if ( get_option( 'rs_point_price_activated' ) != 'yes' )
                    return $price ;

                if ( get_option( 'rs_enable_disable_point_priceing' ) == 2 )
                    return $price ;

                if ( get_option( 'rs_point_price_visibility' ) == '2' && !is_user_logged_in() )
                    return $price ;

                $id = product_id_from_obj( $ProductObj ) ;

                $display_only_points = true ;

                $gettheproducts = srp_product_object( $id ) ;
                if ( is_object( $gettheproducts ) && check_if_variable_product( $gettheproducts ) ) {
                    $variation_ids = get_variation_id( $id ) ;
                    if ( ! srp_check_is_array( $variation_ids ) ) {
                        return $price ;
                    }

                    foreach ( $variation_ids as $eachvariation ) {
                        if ( check_display_price_type( $eachvariation ) != '2' ) {
                            $display_only_points = false ;
                            continue ;
                        }

                        $enable = calculate_point_price_for_products( $eachvariation ) ;
                        if ( empty( $enable[ $eachvariation ] ) ) {
                            $display_only_points = false ;
                            continue ;
                        }
                    }
                }

                if ( $display_only_points && 'variable' == $ProductObj->get_type()) {
                    $price = '' ;
                }

                return $price ;
            }
            return $price ;
        }

        public static function set_variation_limit( $variation_limit , $product ) {
            return 1000 ;
        }

        public static function display_buying_point_msg_for_variation( $price , $ProductObj ) {
            if ( get_option( 'rs_buyingpoints_activated' ) != 'yes' )
                return $price ;

            global $post ;
            if ( ! is_object( $post ) )
                return $price ;

            $banning_type = check_banning_type( get_current_user_id() ) ;
            if ( $banning_type == 'earningonly' || $banning_type == 'both' )
                return $price ;

            if ( ! self::is_in_stock( $ProductObj ) )
                return $price ;

            if ( ! check_if_variable_product( $ProductObj ) )
                return $price ;

            $id            = product_id_from_obj( $ProductObj ) ;
            $variation_ids = get_variation_id( $id ) ;
            if ( ! srp_check_is_array( $variation_ids ) )
                return $price ;

            $BuyingPoints = array() ;
            foreach ( $variation_ids as $eachvariation ) {
                if ( get_post_meta( $eachvariation , '_rewardsystem_buying_reward_points' , true ) != '1' )
                    continue ;

                if ( get_post_meta( $eachvariation , '_rewardsystem_assign_buying_points' , true ) == '' )
                    continue ;

                $BuyingPoints[] = get_post_meta( $eachvariation , '_rewardsystem_assign_buying_points' , true ) ;
            }

            if ( ! srp_check_is_array( $BuyingPoints ) )
                return $price ;

            if ( is_shop() || is_product_category() ) {
                if ( is_user_logged_in() ) {
                    if ( get_option( 'rs_show_hide_buy_points_message_for_variable_in_shop' ) == '1' )
                        return $price . '<br>' . do_shortcode( get_option( 'rs_buy_point_message_in_shop_page_for_variable' ) ) ;
                } else {
                    if ( get_option( 'rs_show_hide_buy_pont_message_for_variable_in_shop_guest' ) == '1' )
                        return $price . '<br>' . do_shortcode( get_option( 'rs_buy_point_message_in_shop_page_for_variable' ) ) ;
                }
            }
            return $price ;
        }

        public static function is_in_stock( $ProductObj ) {
            $bool = $ProductObj->is_in_stock() ;
            if ( is_shop() ) {
                if ( ! ($ProductObj->is_in_stock()) && get_option( 'rs_show_or_hide_message_for_outofstock' ) == '1' )
                    $bool = true ;
            }
            if ( is_product() ) {
                if ( ! ($ProductObj->is_in_stock()) && get_option( 'rs_message_outofstockproducts_product_page' ) == '1' )
                    $bool = true ;
            }
            return $bool ;
        }

    }

    RSFunctionforVariableProduct::init() ;
}