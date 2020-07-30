<?php
/*
 * Simple Product Functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionforSimpleProduct' ) ) {

    class RSFunctionforSimpleProduct {

        public static function init() {
            add_action( 'woocommerce_before_shop_loop' , array( __CLASS__ , 'rs_msg_in_shop_page' ) ) ;
            add_filter( 'woocommerce_get_price_html' , array( __CLASS__ , 'display_reward_point_msg_for_product' ) , 999 , 2 ) ;
            add_filter( 'woocommerce_get_price_html' , array( __CLASS__ , 'display_buying_point_msg_for_simple_product' ) , 9999 , 2 ) ;

            if ( get_option( 'rs_msg_position_in_product_page' ) == '9' ) {

                add_action( 'woocommerce_after_add_to_cart_form' , array( __CLASS__ , 'display_purchase_message_for_simple_in_single_product_page' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '2' ) {

                add_action( 'woocommerce_before_single_product_summary' , array( __CLASS__ , 'display_purchase_message_for_simple_in_single_product_page' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '3' ) {

                add_action( 'woocommerce_single_product_summary' , array( __CLASS__ , 'display_purchase_message_for_simple_in_single_product_page' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '4' ) {

                add_action( 'woocommerce_after_single_product' , array( __CLASS__ , 'display_purchase_message_for_simple_in_single_product_page' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '5' ) {

                add_action( 'woocommerce_after_single_product_summary' , array( __CLASS__ , 'display_purchase_message_for_simple_in_single_product_page' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '6' ) {

                add_action( 'woocommerce_product_meta_end' , array( __CLASS__ , 'display_purchase_message_for_simple_in_single_product_page' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '7' ) {

                add_action( 'woocommerce_before_add_to_cart_quantity' , array( __CLASS__ , 'display_purchase_message_for_simple_in_single_product_page' ) ) ;
            } elseif ( get_option( 'rs_msg_position_in_product_page' ) == '8' ) {

                add_action( 'woocommerce_after_add_to_cart_quantity' , array( __CLASS__ , 'display_purchase_message_for_simple_in_single_product_page' ) ) ;
            } else {

                add_action( 'woocommerce_before_single_product' , array( __CLASS__ , 'display_purchase_message_for_simple_in_single_product_page' ) ) ;
            }

            add_filter( 'woocommerce_variation_sale_price_html' , array( __CLASS__ , 'display_point_price_in_variable_product' ) , 99 , 2 ) ;
            add_filter( 'woocommerce_variation_price_html' , array( __CLASS__ , 'display_point_price_in_variable_product' ) , 99 , 2 ) ;
        }

        public static function rs_msg_in_shop_page() {
            if ( get_option( 'rs_product_purchase_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' )
                return ;

            if ( get_option( 'rs_award_points_for_cart_or_product_total' ) == '1' )
                return ;

            if ( get_option( 'rs_enable_cart_total_reward_points' ) == '2' )
                return ;

            if ( get_option( 'rs_reward_type_for_cart_total' ) == '1' ) {
                if ( get_option( 'rs_enable_msg_for_fixed_cart_total_based_product_purchase_in_shop' ) == '2' )
                    return ;

                $ShopPageMsg               = get_option( 'rs_msg_for_fixed_cart_total_based_product_purchase_in_shop' ) ;
                $FixedCartTotalBasedPoints = get_option( 'rs_reward_points_for_cart_total_in_fixed' ) ;
                $FixedCartTotalBasedPoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $FixedCartTotalBasedPoints ) ;
                if ( $FixedCartTotalBasedPoints == 0 )
                    return ;
            }else {
                if ( get_option( 'rs_enable_msg_for_percent_cart_total_based_product_purchase_in_shop' ) == '2' )
                    return ;

                $ShopPageMsg                 = get_option( 'rs_msg_for_percent_cart_total_based_product_purchase_in_shop' ) ;
                $PercentCartTotalBasedPoints = get_option( 'rs_reward_points_for_cart_total_in_percent' ) ;
                $PercentCartTotalBasedPoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $PercentCartTotalBasedPoints ) ;
                if ( $PercentCartTotalBasedPoints == 0 )
                    return ;
            }
            ?>
            <div class="woocommerce-info"><?php echo $ShopPageMsg ; ?></div>
            <?php
        }

        public static function display_buying_point_msg_for_simple_product( $price , $ProductObj ) {
            if ( get_option( 'rs_buyingpoints_activated' ) != 'yes' )
                return $price ;

            $banning_type = check_banning_type( get_current_user_id() ) ;
            if ( $banning_type == 'earningonly' || $banning_type == 'both' )
                return $price ;

            if ( ! self::is_in_stock( $ProductObj ) )
                return $price ;

            global $post ;
            if ( ! is_object( $post ) )
                return $price ;

            if ( get_post_meta( $post->ID , '_rewardsystem_buying_reward_points' , true ) != 'yes' )
                return $price ;

            if ( get_post_meta( $post->ID , '_rewardsystem_assign_buying_points' , true ) == '' )
                return $price ;

            if ( is_object( $ProductObj ) && (srp_product_type( $post->ID ) == 'simple' || (srp_product_type( $post->ID ) == 'subscription')) ) {
                $sumo_bookings_check = is_sumo_booking_active( $post->ID ) ;
                if ( ! $sumo_bookings_check ) {
                    if ( is_product() ) {
                        if ( is_user_logged_in() ) {
                            if ( get_option( 'rs_show_hide_buy_points_message_for_simple_in_product' ) == '1' ) {
                                $shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_product_page_for_simple' ) ) ;
                                return $price . '<br>' . '<div class = rs_buypoints_message_simple >' . $shop_msg . '</div>' ;
                            }
                        } else {
                            if ( get_option( 'rs_show_hide_buy_point_message_for_simple_in_product_guest' ) == '1' ) {
                                $shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_product_page_for_simple' ) ) ;
                                return $price . '<br>' . '<div class = rs_buypoints_message_simple >' . $shop_msg . '</div>' ;
                            }
                        }
                    }
                    if ( is_shop() || is_product_category() ) {
                        if ( is_user_logged_in() ) {
                            if ( get_option( 'rs_show_hide_buy_points_message_for_simple_in_shop' ) == '1' ) {
                                $shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_shop_page_for_simple' ) ) ;
                                return $price . '<br>' . $shop_msg ;
                            }
                        } else {
                            if ( get_option( 'rs_show_hide_buy_pont_message_for_simple_in_shop_guest' ) == '1' ) {
                                $shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_shop_page_for_simple' ) ) ;
                                return $price . '<br>' . $shop_msg ;
                            }
                        }
                    }
                }
                if ( is_page() || is_tax( 'pwb-brand' ) ) {
                    if ( is_user_logged_in() ) {
                        if ( get_option( 'rs_show_hide_buy_points_message_for_simple_in_custom' ) == '1' ) {
                            $shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_custom_shop_page_for_simple' ) ) ;
                            return $price . '<br>' . $shop_msg ;
                        }
                    } else {
                        if ( get_option( 'rs_show_hide_buy_point_message_for_simple_in_custom_shop_guest' ) == '1' ) {
                            $shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_custom_shop_page_for_simple' ) ) ;
                            return $price . '<br>' . $shop_msg ;
                        }
                    }
                }
            }
        }

        public static function display_reward_point_msg_for_product( $price , $ProductObj ) {
            $banning_type = check_banning_type( get_current_user_id() ) ;
            if ( $banning_type == 'earningonly' || $banning_type == 'both' )
                return $price ;

            global $post ;
            global $woocommerce ;
            global $woocommerce_loop ;
            $related_product     = false ;
            $point_price_display = '' ;
            $id                  = product_id_from_obj( $ProductObj ) ;
            $pdt_type            = srp_product_type( $id ) ;
            $bool                = self::is_in_stock( $ProductObj ) ;
            if ( ( float ) $woocommerce->version >= ( float ) '3.3.0' ) {
                if ( isset( $woocommerce_loop[ 'name' ] ) ) {
                    if ( ($woocommerce_loop[ 'name' ] != NULL) && ($woocommerce_loop[ 'name' ] == 'related' || $woocommerce_loop[ 'name' ] == 'up-sells') ) {
                        $related_product = true ;
                    }
                }
            }
            if ( is_object( $ProductObj ) && check_if_variable_product( $ProductObj ) ) {
                $variation_ids = get_variation_id( $id ) ;
                if ( srp_check_is_array( $variation_ids ) && block_points_for_salepriced_product( $variation_ids[ 0 ] , 0 ) == 'yes' )
                    return $price ;

                $varpointss = srp_check_is_array( $variation_ids ) ? self::rewardpoints_of_variation( $variation_ids[ 0 ] , $id ) : '' ;
                $pointmin   = self::get_point_price( $id ) ;
                if ( ! empty( $pointmin ) && $bool == true ) {
                    $displaymin = min( $pointmin ) ;
                    $displaymax = max( $pointmin ) ;
                    $display    = ! empty( $price ) ? '/' : '' ;
                    if ( get_option( 'rs_point_price_visibility' ) == '2' && ! is_user_logged_in() ) {
                        $point_price_display = '' ;
                    } else {
                        if ( $displaymin == $displaymax ) {
                            $PointPrice          = display_point_price_value( $displaymin ) ;
                            $point_price_display = $display . $PointPrice ;
                        } else {
                            $MinPointPrice       = display_point_price_value( $displaymin ) ;
                            $MaxPointPrice       = display_point_price_value( $displaymax ) ;
                            $point_price_display = $display . $MinPointPrice . '-' . $MaxPointPrice ;
                        }
                    }
                }
                if ( is_page() || is_tax( 'pwb-brand' ) ) {                                                                        //Shop and Category Page Message for Variable Product                            
                    if ( get_option( 'rs_enable_display_earn_message_for_variation_custom_shop' ) == 'yes' ) {
                        if ( $varpointss != '' ) {
                            $earnmessage = (get_option( 'rs_product_purchase_activated' ) == 'yes') ? str_replace( '[variationrewardpoints]' , round_off_type( $varpointss ) , get_option( "rs_message_for_custom_shop_variation" ) ) : '' ;
                            // Shop Page Message for Variable Product with Gift Icon
                            return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage , $price , $point_price_display ) ;
                        }
                    } else {
                        // To display point price in the custom pages.
                        if ( ! empty( $pointmin ) ) {
                            return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage = '' , $price , $point_price_display ) ;
                        }
                    }
                }
                if ( is_shop() || is_product_category() ) {                                                                        //Shop and Category Page Message for Variable Product                            
                    if ( get_option( 'rs_enable_display_earn_message_for_variation' ) == 'yes' && $bool == true ) {
                        if ( $varpointss != '' ) {
                            $earnmessage = (get_option( 'rs_product_purchase_activated' ) == 'yes') ? str_replace( '[variationrewardpoints]' , round_off_type( $varpointss ) , get_option( "rs_message_for_single_product_variation" ) ) : '' ;
                            // Shop Page Message for Variable Product with Gift Icon
                            return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage , $price , $point_price_display ) ;
                        }
                    } else {
                        return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage = '' , $price , $point_price_display ) ;
                    }
                }

                if ( is_product() ) {
                    //Single Product and Custom Page Message for Variable Product
                    if ( $related_product == true && get_option( 'rs_show_hide_message_for_shop_archive_variable_related_products' ) == '1' && $bool == true ) {
                        global $post ;
                        $variation_ids = get_variation_id( $post->ID ) ;
                        $varpointss    = srp_check_is_array( $variation_ids ) ? self::rewardpoints_of_variation( $variation_ids[ 0 ] , $post->ID ) : '' ;
                        if ( $varpointss != '' ) {
                            $earnmessage = str_replace( '[variationrewardpoints]' , round_off_type( $varpointss ) , get_option( 'rs_message_in_variable_related_products' ) ) ;
                            return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage , $price , $point_price_display , true ) ;
                        }
                    }
                    if ( $related_product == false && $bool == true ) {
                        if ( get_option( 'rs_show_hide_message_for_variable_in_single_product_page' ) == '1' ) {
                            // Shop Page Message for Variable Product with Gift Icon
                            if ( get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) == 'yes' ) {
                                if ( $varpointss != '' ) {
                                    if ( $pdt_type === 'variable' ) {
                                        return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage = '' , $price , $point_price_display ) ;
                                    }
                                }
                            } else {
                                if ( $pdt_type === 'variable' ) {
                                    return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage = '' , $price , $point_price_display ) ;
                                }
                            }
                        }
                        if ( $pdt_type === 'variable' ) {
                            if ( get_option( 'rs_enable_disable_point_priceing' ) == '1' && get_option( 'rs_point_price_activated' ) == 'yes' ) {
                                return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage = '' , $price , $point_price_display ) ;
                            }
                        }
                        if ( $pdt_type === 'variation' ) {
                            if ( get_option( 'rs_enable_disable_point_priceing' ) == '1' ) {
                                return self :: display_point_price_in_variable_product( $price , $ProductObj ) ;
                            }
                        }
                    }
                }
            } else {
                $getshortcodevalues   = points_for_simple_product() ;
                $enabledpoints        = calculate_point_price_for_products( $id ) ;
                $enabled_point_price  = $enabledpoints[ $id ] ;
                $point_price          = empty( $enabled_point_price ) ? 0 : round_off_type( $enabled_point_price ) ;
                $point_price_type     = check_display_price_type( $id ) ;
                $enable_reward_points = get_post_meta( $id , '_rewardsystemcheckboxvalue' , true ) ;
                if ( $point_price != 0 && $bool == true ) {
                    $point_price_info = display_point_price_value( $point_price , true ) ;
                } else {
                    $point_price_info = $price ;
                }
                if ( $getshortcodevalues > 0 ) {
                    if ( is_shop() || is_product_category() ) {//Shop and Category Page Message for Simple Product
                        global $post ;
                        $sumo_bookings_check = is_sumo_booking_active( $post->ID ) ;
                        if ( ! $sumo_bookings_check ) {
                            $earnmessage           = do_shortcode( get_option( "rs_message_in_shop_page_for_simple" ) ) ;
                            // Shop Page Message for Simple Product with Gift Icon
                            $earnpoint_msg_in_shop = self::rs_function_to_get_msg_with_gift_icon( $earnmessage ) ;
                            $msg_position          = get_option( 'rs_message_position_for_simple_products_in_shop_page' ) ;
                            //Shop Page Message for Simple Product for User                            
                            if ( is_user_logged_in() ) {
                                if ( get_option( 'rs_show_hide_message_for_simple_in_shop' ) == '1' && $bool == true ) {

                                    //Function to Return Shop Page Message
                                    return self::rs_function_to_get_earnpoint_msg( $point_price_type , $point_price , $price , $point_price_info , $earnpoint_msg_in_shop , $msg_position ) ;
                                }
                            } else {
                                //Shop Page Message for Simple Product for Guest
                                if ( get_option( 'rs_show_hide_message_for_simple_in_shop_guest' ) == '1' && $bool == true ) {

                                    //Function to Return Shop Page Message
                                    return self::rs_function_to_get_earnpoint_msg( $point_price_type , $point_price , $price , $point_price_info , $earnpoint_msg_in_shop , $msg_position ) ;
                                }
                            }
                        }
                    }
                    if ( (is_page() || is_tax( 'pwb-brand' )) && $bool ) {
                        $earnmessage = do_shortcode( get_option( "rs_message_in_custom_shop_page_for_simple" ) ) ;

                        // Shop Page Message for Simple Product with Gift Icon
                        $earnpoint_msg_in_shop = self::rs_function_to_get_msg_with_gift_icon( $earnmessage ) ;
                        $msg_position          = get_option( 'rs_message_position_for_simple_products_in_custom_shop_page' ) ;
                        //Shop Page Message for Simple Product for User                            
                        if ( is_user_logged_in() ) {
                            if ( get_option( 'rs_show_hide_message_for_simple_in_custom_shop' ) == '1' ) {

                                //Function to Return Shop Page Message
                                return self::rs_function_to_get_earnpoint_msg( $point_price_type , $point_price , $price , $point_price_info , $earnpoint_msg_in_shop , $msg_position ) ;
                            }
                        } else {
                            //Shop Page Message for Simple Product for Guest
                            if ( get_option( 'rs_show_hide_message_for_simple_in_custom_shop_guest' ) == '1' ) {

                                //Function to Return Shop Page Message
                                return self::rs_function_to_get_earnpoint_msg( $point_price_type , $point_price , $price , $point_price_info , $earnpoint_msg_in_shop , $msg_position ) ;
                            }
                        }
                    }
                    if ( is_product() ) {                              //Single Product and Custom Page Message for Simple Product
                        global $post ;
                        $sumo_bookings_check = is_sumo_booking_active( $post->ID ) ;

                        if ( $related_product == true && get_option( 'rs_show_hide_message_for_shop_archive_single_related_products' ) == '1' && $bool == true ) {
                            $sumo_bookings_check = is_sumo_booking_active( $post->ID ) ;
                            if ( ! $sumo_bookings_check ) {
                                $earnmessage           = do_shortcode( get_option( "rs_message_in_single_product_page_related_products" ) ) ;
                                $earnpoint_msg_in_shop = self::rs_function_to_get_msg_with_gift_icon( $earnmessage ) ;
                                $msg_position          = get_option( 'rs_message_position_in_single_product_page_for_simple_products' ) ;
                                return self::rs_function_to_get_earnpoint_msg( $point_price_type , $point_price , $price , $point_price_info , $earnpoint_msg_in_shop , $msg_position ) ;
                            }
                        }
                        if ( $related_product == false && $bool == true ) {
                            if ( ! $sumo_bookings_check ) {
                                if ( get_option( 'rs_show_hide_message_for_shop_archive_single' ) == '1' ) {
                                    $earnmessage           = do_shortcode( get_option( "rs_message_in_single_product_page" ) ) ;
                                    // Single Product and Custom Page Message for Simple Product with Gift Icon
                                    $earnpoint_msg_in_shop = self::rs_function_to_get_msg_with_gift_icon( $earnmessage ) ;
                                    $msg_position          = get_option( 'rs_message_position_in_single_product_page_for_simple_products' ) ;
                                    //Function to Return Single Product and Custom Page Message
                                    return self::rs_function_to_get_earnpoint_msg( $point_price_type , $point_price , $price , $point_price_info , $earnpoint_msg_in_shop , $msg_position ) ;
                                }
                            }
                        }
                    }
                }

                if ( get_option( 'rs_enable_disable_point_priceing' ) == '1' && get_option( 'rs_point_price_activated' ) == 'yes' && $bool == true ) {
                    $VisibilityForPointPrice = (get_option( 'rs_point_price_visibility' ) == 1) ? true : is_user_logged_in() ;
                    if ( ! $VisibilityForPointPrice )
                        return $price ;

                    global $post ;
                    if ( ! is_sumo_booking_active( $post->ID ) ) {
                        if ( $point_price_type == '2' ) {
                            return str_replace( "/" , "" , $point_price_info ) ;
                        } else {
                            if ( $point_price != 0 ) {
                                return $price . '<span class="point_price_label">' . $point_price_info ;
                            }
                        }
                    }
                }
            }
            return $price ;
        }

        public static function rs_function_to_get_earnpoint_msg( $point_price_type , $point_price , $price , $point_price_info , $earnpoint_msg_in_shop , $msg_position ) {
            $VisibilityForPointPrice = (get_option( 'rs_point_price_visibility' ) == 1) ? true : is_user_logged_in() ;
            if ( get_option( 'rs_enable_disable_point_priceing' ) == '1' && get_option( 'rs_point_price_activated' ) == 'yes' && $VisibilityForPointPrice ) {                          //Shop Page Message for Simple Product when Points Price is Enabled                
                if ( $point_price_type == '2' ) {
                    $point_price_info = str_replace( "/" , "" , $point_price_info ) ;
                    if ( $msg_position == '1' ) {    //Position of Shop Page Message for Simple Product - Before
                        if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                            return "<small>" . $point_price_info . "</small><br>" ;
                        } else {
                            return "<small>" . $earnpoint_msg_in_shop . "</small> <br>" . $point_price_info ;
                        }
                    } else {                                                                            //Position of Shop Page Message for Simple Product - After
                        if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                            return "<small>" . $point_price_info . "</small><br>" ;
                        } else {
                            return "<small>" . $point_price_info . "<br>" . $earnpoint_msg_in_shop . "</small><br>" ;
                        }
                    }
                } else {
                    if ( $point_price != 0 ) {
                        if ( $msg_position == '1' ) {    //Position of Shop Page Message for Simple Product - Before
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                                return $price . '<span class="point_price_label">' . $point_price_info . "</span>" ;
                            } else {
                                return $earnpoint_msg_in_shop . "<br>" . $price . '<span class="point_price_label">' . $point_price_info . "</span>" ;
                            }
                        } else {                                                                              //Position of Shop Page Message for Simple Product - After
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                                return $price . '<span class="point_price_label">' . $point_price_info . "</span>" ;
                            } else {
                                return $price . '<span class="point_price_label">' . $point_price_info . "</span><br><small>" . $earnpoint_msg_in_shop . "</small>" ;
                            }
                        }
                    } else {
                        if ( $msg_position == '1' ) {    //Position of Shop Page Message for Simple Product - Before
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                                return $price ;
                            } else {
                                return $earnpoint_msg_in_shop . "<br>" . $price ;
                            }
                        } else {                                                                              //Position of Shop Page Message for Simple Product - After
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                                return $price ;
                            } else {
                                return $price . "<br>" . $earnpoint_msg_in_shop ;
                            }
                        }
                    }
                }
            } else {                                                                              //Shop Page Message for Simple Product when Points Price is Disabled
                if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' )
                    return $price ;

                if ( $msg_position == '1' ) {    //Position of Shop Page Message for Simple Product - Before
                    return $earnpoint_msg_in_shop . "<br>" . $price ;
                } else {                                                                              //Position of Shop Page Message for Simple Product - After
                    return $price . "<br>" . $earnpoint_msg_in_shop ;
                }
            }
            return $price ;
        }

        public static function rs_function_to_get_msg_with_gift_icon( $earnmessage ) {
            if ( get_option( 'rs_product_purchase_activated' ) == 'yes' ) {
                if ( get_option( '_rs_enable_disable_gift_icon' ) == '1' ) {
                    if ( get_option( 'rs_image_url_upload' ) != '' ) {
                        $earnpoint_msg_in_shop = "<span class='simpleshopmessage'><img src=" . get_option( 'rs_image_url_upload' ) . " style='width:16px;height:16px;display:inline;' />&nbsp; " . $earnmessage . "</span>" ;
                    } else {
                        $earnpoint_msg_in_shop = "<span class='simpleshopmessage'>" . $earnmessage . "</span>" ;
                    }
                } else {
                    $earnpoint_msg_in_shop = "<span class='simpleshopmessage'>" . $earnmessage . "</span>" ;
                }
            } else {
                $earnpoint_msg_in_shop = '' ;
            }
            return $earnpoint_msg_in_shop ;
        }

        public static function rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage , $price , $point_price_display , $related_product = false ) {
            $image = "<img class='gift_icon' src=" . get_option( 'rs_image_url_upload' ) . " style='width:16px;height:16px;display:inline;' />&nbsp;" ;
            if ( $earnmessage != '' ) {
                $break = '<br>' ;
            } else {
                $break = '' ;
                if ( is_shop() || is_product_category() )
                    $image = '' ;

                if ( is_product() || is_page() || is_tax( 'pwb-brand' ) )
                    $break = '<br>' ;
            }

            $classname = ($related_product) ? 'variablerelatedmessage' : 'variableshopmessage' ;

            $VisibilityForPointPrice = (get_option( 'rs_point_price_visibility' ) == 1) ? true : is_user_logged_in() ;
            if ( get_option( '_rs_enable_disable_gift_icon' ) == '1' ) {
                if ( get_option( 'rs_enable_disable_point_priceing' ) == '1' && get_option( 'rs_point_price_activated' ) == 'yes' && $VisibilityForPointPrice ) {
                    if ( get_option( 'rs_image_url_upload' ) != '' ) {
                        if ( get_option( 'rs_message_position_in_single_product_page_for_variable_products' ) == '1' ) {
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                                return $image . $break . $price . $point_price_display ;
                            } else {
                                return $image . "<span class='$classname'>" . $earnmessage . "</span>" . $break . $price . $point_price_display ;
                            }
                        } else {
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                                return $price . $point_price_display . '<br>' . $image ;
                            } else {
                                return $price . $point_price_display . '<br>' . $image . "<span class='$classname'>" . $earnmessage . "</span>" ;
                            }
                        }
                    } else {
                        if ( get_option( 'rs_message_position_in_single_product_page_for_variable_products' ) == '1' ) {
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                                return $break . $price . $point_price_display ;
                            } else {
                                return "<span class='$classname'>" . $earnmessage . "</span>" . $break . $price . $point_price_display ;
                            }
                        } else {
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                                return $price . $point_price_display . $break ;
                            } else {
                                return $price . $point_price_display . $break . "<span class='$classname'>" . $earnmessage . "</span>" ;
                            }
                        }
                    }
                } else {
                    if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' )
                        return $price ;

                    if ( get_option( 'rs_image_url_upload' ) != '' ) {
                        if ( get_option( 'rs_message_position_in_single_product_page_for_variable_products' ) == '1' ) {
                            return $image . "<span class='$classname'>" . $earnmessage . "</span>" . $break . $price ;
                        } else {
                            return $price . '<br>' . $image . "<span class='$classname'>" . $earnmessage . "</span>" ;
                        }
                    } else {
                        if ( get_option( 'rs_message_position_in_single_product_page_for_variable_products' ) == '1' ) {
                            return "<span class='$classname'>" . $earnmessage . "</span>" . $break . $price ;
                        } else {
                            return $price . $break . "<span class='$classname'>" . $earnmessage . "</span>" ;
                        }
                    }
                }
            } else {
                if ( is_product() ) {
                    if ( get_option( 'rs_enable_disable_point_priceing' ) == '1' && get_option( 'rs_point_price_activated' ) == 'yes' && $VisibilityForPointPrice ) {
                        if ( get_option( 'rs_message_position_in_single_product_page_for_variable_products' ) == '1' ) {
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                                return $break . $price . $point_price_display ;
                            } else {
                                return "<span class='$classname'>" . $earnmessage . "</span>" . $break . $price . $point_price_display ;
                            }
                        } else {
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                                return $price . $point_price_display . $break ;
                            } else {
                                return $price . $point_price_display . $break . "<span class='$classname'>" . $earnmessage . "</span>" ;
                            }
                        }
                    } else {
                        if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' )
                            return $price ;

                        if ( get_option( 'rs_message_position_in_single_product_page_for_variable_products' ) == '1' ) {
                            return "<span class='$classname'>" . $earnmessage . "</span>" . $break . $price ;
                        } else {
                            return $price . $break . "<span class='$classname'>" . $earnmessage . "</span>" ;
                        }
                    }
                } else if ( is_shop() || is_product_category() || is_page() || is_tax( 'pwb-brand' ) ) {
                    if ( get_option( 'rs_enable_disable_point_priceing' ) == '1' && get_option( 'rs_point_price_activated' ) == 'yes' && $VisibilityForPointPrice ) {
                        if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                            return $price . $point_price_display . $break ;
                        } else {
                            if ( get_option( 'rs_msg_position_for_var_products_in_shop_page' ) == '1' ) {
                                if ( $point_price_display ) {
                                    return $point_price_display . $break . "<span class='$classname'>" . $earnmessage . "</span>" . $break . $price ;
                                } else {
                                    return "<span class='$classname'>" . $earnmessage . "</span>" . $break . $price ;
                                }
                            } else {
                                if ( $point_price_display ) {
                                    return $price . $point_price_display . $break . "<span class='$classname'>" . $earnmessage . "</span>" ;
                                } else {
                                    return $price . $break . "<span class='$classname'>" . $earnmessage . "</span>" ;
                                }
                            }
                        }
                    } else {
                        if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' )
                            return $price ;

                        if ( get_option( 'rs_msg_position_for_var_products_in_shop_page' ) == '1' ) {
                            return "<span class='$classname'>" . $earnmessage . "</span>" . $break . $price ;
                        } else {
                            return $price . $break . "<span class='$classname'>" . $earnmessage . "</span>" ;
                        }
                    }
                }
            }
            return $price ;
        }

        public static function get_point_price( $post_id ) {

            if ( get_option( 'rs_point_price_activated' ) != 'yes' )
                return ;

            $enabledpoints1 = array() ;
            $args           = array(
                'post_parent' => $post_id ,
                'post_type'   => 'product_variation' ,
                'orderby'     => 'menu_order' ,
                'order'       => 'ASC' ,
                'fields'      => 'ids' ,
                'post_status' => 'publish' ,
                'numberposts' => -1
                    ) ;
            $variation_ids  = get_posts( $args ) ;
            foreach ( $variation_ids as $key ) {
                $enabledpoints = calculate_point_price_for_products( $key ) ;
                if ( $enabledpoints[ $key ] != '' ) {
                    $enabledpoints1[ $key ] = $enabledpoints[ $key ] ;
                }
            }
            return $enabledpoints1 ;
        }

        public static function display_purchase_msg_in_single_product_page() {
            if ( ! is_user_logged_in() )
                return ;

            if ( ! class_exists( 'FPWaitList' ) )
                return ;

            if ( get_option( 'rs_reward_action_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_message_for_waitlist' ) == '2' )
                return ;

            $points = round_off_type( get_option( 'rs_reward_for_waitlist_subscribing' ) ) ;
            if ( $points == 0 )
                return ;

            global $post ;
            $checkproducttype = srp_product_object( $post->ID ) ;

            if ( $checkproducttype->is_in_stock() )
                return ;

            if ( get_option( 'wl_show_form_member' ) != 'yes' )
                return ;

            $message = get_option( 'rs_message_for_subscribing_product' ) ;
            $replace = str_replace( '[subscribingpoints]' , RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $points ) , $message ) ;
            ?>
            <div class="woocommerce-info rs_message_for_single_product"><?php echo $replace ; ?></div>
            <?php
        }

        public static function display_cart_total_based_product_purchase_msg_in_single_product_page() {
            if ( get_option( 'rs_product_purchase_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' )
                return ;

            if ( get_option( 'rs_award_points_for_cart_or_product_total' ) == '1' )
                return ;

            if ( get_option( 'rs_enable_cart_total_reward_points' ) == '2' )
                return ;

            if ( get_option( 'rs_reward_type_for_cart_total' ) == '1' ) {
                $ShowMsg = is_user_logged_in() ? get_option( 'rs_enable_msg_for_fixed_cart_total_based_product_purchase' ) : get_option( 'rs_enable_msg_for_fixed_cart_total_based_product_purchase_guest' ) ;
                if ( $ShowMsg == 2 )
                    return ;

                $ProductPageMsg            = get_option( 'rs_msg_for_fixed_cart_total_based_product_purchase' ) ;
                $FixedCartTotalBasedPoints = get_option( 'rs_reward_points_for_cart_total_in_fixed' ) ;
                $FixedCartTotalBasedPoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $FixedCartTotalBasedPoints ) ;
                if ( $FixedCartTotalBasedPoints == 0 )
                    return ;
            }else {
                $ShowMsg = is_user_logged_in() ? get_option( 'rs_enable_msg_for_percent_cart_total_based_product_purchase' ) : get_option( 'rs_enable_msg_for_percent_cart_total_based_product_purchase_guest' ) ;
                if ( $ShowMsg == 2 )
                    return ;

                $ProductPageMsg              = get_option( 'rs_msg_for_percent_cart_total_based_product_purchase' ) ;
                $PercentCartTotalBasedPoints = get_option( 'rs_reward_points_for_cart_total_in_percent' ) ;
                $PercentCartTotalBasedPoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $PercentCartTotalBasedPoints ) ;
                if ( $PercentCartTotalBasedPoints == 0 )
                    return ;
            }
            ?>
            <div class="woocommerce-info"><?php echo $ProductPageMsg ; ?></div>
            <?php
        }

        public static function display_product_review_msg_in_single_product_page() {
            if ( get_option( 'rs_reward_action_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_enable_product_review_points' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_message_for_product_review' ) == '2' )
                return ;

            if ( get_option( 'rs_show_hide_message_for_product_review_for_guest_user' ) == '2' )
                if ( ! is_user_logged_in() )
                    return ;

            $ProductReviewMsg              = get_option( 'rs_message_for_product_review' ) ;
            $ProductReviewPoints           = round_off_type( get_option( 'rs_reward_product_review' ) ) ;
            $ProductReviewPoints           = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $ProductReviewPoints ) ;
            $ProductReviewMsginProductPage = str_replace( '[productreviewpoint]' , $ProductReviewPoints , $ProductReviewMsg ) ;

            /* Display Review Notice based on Restrict Points Once Per User functionality */
            global $product ;
            $product_id                = ! empty( $product ) ? $product->get_id() : '' ;
            $RestrictPointsOncePerUser = get_option( 'rs_restrict_reward_product_review' , 'no' ) ;
            if ( $product_id && $RestrictPointsOncePerUser == 'yes' ) {
                $CheckIfUserAlreadyReviewed = get_user_meta( get_current_user_id() , 'userreviewed' . $product_id , true ) ;
                if ( $CheckIfUserAlreadyReviewed == '1' )
                    return ;
            }

            if ( $ProductReviewPoints == 0 )
                return ;
            ?>
            <div class="woocommerce-info rs_message_for_single_product"><?php echo $ProductReviewMsginProductPage ; ?></div>
            <?php
        }

        public static function display_product_total_based_product_purchase_msg_in_single_product_page() {
            $userid       = get_current_user_id() ;
            $banning_type = check_banning_type( $userid ) ;
            if ( $banning_type == 'earningonly' || $banning_type == 'both' )
                return ;

            global $post ;
            if ( is_user_logged_in() ) {
                $sumo_bookings_check = is_sumo_booking_active( $post->ID ) ;
                $checkproducttype    = srp_product_object( $post->ID ) ;
                if ( referral_points_for_simple_product() > 0 ) {
                    if ( get_option( 'rs_show_hide_message_for_single_product_referral' ) == '1' && get_option( 'rs_referral_activated' ) == 'yes' ) {
                        if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
                            $refuser = (get_option( 'rs_generate_referral_link_based_on_user' ) == 1) ? get_user_by( 'login' , $_COOKIE[ 'rsreferredusername' ] ) : get_user_by( 'id' , $_COOKIE[ 'rsreferredusername' ] ) ;
                            $myid    = $refuser->ID ;
                        } else {
                            $myid = check_if_referrer_has_manual_link( get_current_user_id() ) ;
                        }
                        $username = get_user_by( 'id' , $myid )->user_login ;
                        ?>
                        <div class="woocommerce-info rs_message_for_single_product">
                            <?php
                            $strrplc  = str_replace( '[rsreferredusername]' , $username , get_option( 'rs_message_for_single_product_point_rule_referral' ) ) ;
                            echo do_shortcode( $strrplc ) ;
                            ?>
                        </div>
                        <?php
                    }
                }

                if ( get_option( 'rs_buyingpoints_activated' ) == 'yes' ) {
                    $buy_points = buying_points_for_simple_product() ;
                    if ( get_option( 'rs_show_hide_buy_point_message_for_single_product' ) == '1' && get_post_meta( $post->ID , '_rewardsystem_buying_reward_points' , true ) == 'yes' && $buy_points > 0 ) {
                        ?>                
                        <div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( get_option( 'rs_buy_point_message_for_single_product_point_rule' ) ) ; ?></div>
                        <?php
                    }
                }

                if ( get_option( 'rs_product_purchase_activated' ) != 'yes' )
                    return ;

                if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' )
                    return ;

                if ( get_option( 'rs_show_hide_message_for_single_product' ) == '2' )
                    return ;

                if ( is_object( $checkproducttype ) && (srp_product_type( $post->ID ) == 'simple' || srp_product_type( $post->ID ) == 'subscription' || srp_product_type( $post->ID ) == 'bundle') ) {
                    $rewardpoints = points_for_simple_product() ;
                    if ( $rewardpoints > 0 && ! $sumo_bookings_check ) {
                        ?>
                        <div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( get_option( 'rs_message_for_single_product_point_rule' ) ) ; ?></div>
                    <?php } elseif ( $rewardpoints > 0 && $sumo_bookings_check ) {
                        ?>
                        <div class="woocommerce-info rs_message_for_single_product"><?php echo get_option( 'rs_message_for_booking_product' ) ; ?></div>

                        <?php
                    }
                }
            } else {
                $sumo_bookings_check = is_sumo_booking_active( $post->ID ) ;
                if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
                    if ( get_option( 'rs_referrer_earn_point_purchase_by_guest_users' ) == 'yes' && get_option( 'rs_show_hide_message_for_single_product_guest_referral' ) == '1' ) {
                        $referralpoints = referral_points_for_simple_product() ;
                        if ( $referralpoints > 0 ) {
                            $msg = str_replace( '[rsreferredusername]' , $_COOKIE[ 'rsreferredusername' ] , get_option( 'rs_message_for_single_product_point_rule_referral' ) ) ;
                            ?>
                            <div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( $msg ) ; ?></div>
                            <?php
                        }
                    }
                }
                if ( get_option( 'rs_product_purchase_activated' ) != 'yes' )
                    return ;

                if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' )
                    return ;

                if ( get_option( 'rs_show_hide_message_for_single_product_guest' ) == '2' )
                    return ;

                $checkproducttype = srp_product_object( $post->ID ) ;
                if ( is_object( $checkproducttype ) && (srp_product_type( $post->ID ) == 'simple' || (srp_product_type( $post->ID ) == 'subscription') || srp_product_type( $post->ID ) == 'bundle') ) {
                    $rewardpoints = points_for_simple_product() ;
                    if ( $rewardpoints > 0 && ! $sumo_bookings_check ) {
                        ?>
                        <div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( get_option( 'rs_message_for_single_product_point_rule' ) ) ; ?></div>
                    <?php } elseif ( $rewardpoints > 0 && $sumo_bookings_check ) {
                        ?>
                        <div class="woocommerce-info rs_message_for_single_product"><?php echo get_option( 'rs_message_for_booking_product' ) ; ?></div>
                        <?php
                    }
                }
            }
        }

        public static function display_purchase_message_for_simple_in_single_product_page() {
            global $post ;
            $product = srp_product_object( $post->ID ) ;
            $bool    = self::is_in_stock( $product ) ;
            if ( ! $bool )
                return ;

            if ( did_action( 'woocommerce_before_single_product' ) === 1 ) {

                /* To Display Product Review Message */
                self::display_product_review_msg_in_single_product_page() ;

                /* To Display Subscribing Product Message */
                self::display_purchase_msg_in_single_product_page() ;

                /* To Display Product Purchase Message Based on Cart Total */
                self::display_cart_total_based_product_purchase_msg_in_single_product_page() ;

                /* To Display Product Purchase Message Based on Product Total */
                self::display_product_total_based_product_purchase_msg_in_single_product_page() ;
            }
        }

        public static function display_point_price_in_variable_product( $price , $ProductObj ) {
            if ( get_option( 'rs_point_price_activated' ) != 'yes' )
                return $price ;

            $ProductId = product_id_from_obj( $ProductObj ) ;
            global $post ;
            if ( get_option( 'rs_point_price_visibility' ) == '2' && ! is_user_logged_in() ) {
                return $price ;
            } else {
                if ( get_option( 'rs_enable_disable_point_priceing' ) == '1' ) {
                    $enabledpoints = calculate_point_price_for_products( $ProductId ) ;
                    $point_price   = $enabledpoints[ $ProductId ] ;
                    $typeofprice   = check_display_price_type( $ProductId ) ;
                    if ( $typeofprice == '2' ) {
                        $point_price = round_off_type( $point_price ) ;
                        $totalamount = display_point_price_value( $point_price ) ;
                        return $totalamount ;
                    } else {
                        if ( $point_price != '' ) {
                            $point_price = round_off_type( $point_price ) ;
                            $totalamount = display_point_price_value( $point_price , true ) ;
                            return $price . '<span class="point_price_label">' . $totalamount ;
                        } else {
                            return $price ;
                        }
                    }
                }
            }
        }

        public static function rewardpoints_of_variation( $variation_id , $newparentid ) {
            $args         = array(
                'productid'   => $newparentid ,
                'variationid' => $variation_id ,
                'item'        => array( 'qty' => '1' ) ,
                    ) ;
            $rewardpoints = check_level_of_enable_reward_point( $args ) ;
            $varpoints    = is_user_logged_in() ? RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $rewardpoints ) : ( float ) $rewardpoints ;
            return $varpoints ;
        }

        public static function is_in_stock( $ProductObj ) {

            if ( ! is_object( $ProductObj ) ) {
                return false ;
            }

            $bool = $ProductObj->is_in_stock() ;

            if ( is_shop() ) {
                if ( ! $bool && get_option( 'rs_show_or_hide_message_for_outofstock' ) == '1' )
                    $bool = true ;
            }
            if ( is_product() ) {
                if ( ! $bool && get_option( 'rs_message_outofstockproducts_product_page' ) == '1' )
                    $bool = true ;
            }
            if ( is_page() ) {
                if ( ! $bool && get_option( 'rs_show_or_hide_message_for_customshop' ) == '1' )
                    $bool = true ;
            }
            return $bool ;
        }

    }

    RSFunctionforSimpleProduct::init() ;
}