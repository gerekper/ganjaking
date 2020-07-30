<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'FP_Reward_Points_WC_2P6' ) ) {

    /*
     * Reward Points Compatible with 2.6 of WooCommerce
     */

    class FP_Reward_Points_WC_2P6 {

        public static function init() {
            add_action( 'wp_enqueue_scripts' , array( __CLASS__ , 'enqueue_scripts' ) , 999 ) ;
            add_action( 'wp_ajax_rs_point_price_compatability' , array( __CLASS__ , 'combatibility_point_price' ) , 10 ) ;
        }

        public static function combatibility_point_price() {
            if ( get_option( 'rs_enable_disable_point_priceing' ) == 'yes' && get_option( 'rs_point_price_activated' ) == 'yes' ) {
                global $post ;
                $posted = array() ;
                parse_str( $_POST[ 'form' ] , $posted ) ;
                if ( isset( $posted[ 'add-to-cart' ] ) ) {
                    $booking_id   = $posted[ 'add-to-cart' ] ;
                    $product      = srp_product_object( $booking_id ) ;
                    $booking_form = new WC_Booking_Form( $product ) ;
                    $cost         = $booking_form->calculate_booking_cost( $posted ) ;
                    $args         = array( 'qty' => 1 , 'price' => $cost ) ;
                    if ( is_wp_error( $cost ) )
                        die( json_encode( array( 'sumorewardpoints' => 0 ) ) ) ;

                    $getpointprice        = 0 ;
                    $price_to_display_inc = function_exists( 'wc_get_price_including_tax' ) ? wc_get_price_including_tax( $product , $args ) : $product->get_price_including_tax( 1 , $cost ) ;
                    $price_to_display_exc = function_exists( 'wc_get_price_excluding_tax' ) ? wc_get_price_excluding_tax( $product , $args ) : $product->get_price_excluding_tax( 1 , $cost ) ;
                    $product_price        = (get_option( 'woocommerce_tax_display_shop' ) == 'incl') ? $price_to_display_inc : $price_to_display_exc ;
                    $checkproducttype     = srp_product_object( $booking_id ) ;
                    $product_id           = $booking_id ;
                    if ( is_object( $checkproducttype ) && srp_product_type( $booking_id ) == 'booking' ) {
                        $product_level_fixed_price        = get_post_meta( $product_id , '_rewardsystem__points' , true ) ;
                        $product_level_enable             = get_post_meta( $product_id , '_rewardsystem_enable_point_price' , true ) ;
                        $point_price_type                 = get_post_meta( $product_id , '_enable_reward_points_price_type' , true ) ;
                        $point_based_on_conversion        = get_post_meta( $product_id , '_price_points_based_on_conversion' , true ) ;
                        $product_level_price_type         = get_post_meta( $product_id , '_rewardsystem_point_price_type' , true ) ;
                        $product_level_price_display_type = get_post_meta( $product_id , '_rewardsystem_enable_point_price_type' , true ) ;
                        $data                             = array( '0' ) ;
                        if ( $product_level_enable == 'yes' ) {
                            if ( $product_level_price_display_type == '2' ) {
                                $data[] = get_post_meta( $product_id , '_rewardsystem__points' , true ) ;
                            } else {
                                if ( $product_level_price_type == '1' ) {
                                    if ( $product_level_fixed_price == '' ) {
                                        $term = get_the_terms( $product_id , 'product_cat' ) ;
                                        if ( is_array( $term ) ) {
                                            foreach ( $term as $term ) {
                                                $cat_level_enable = srp_term_meta( $term->term_id , 'enable_point_price_category' ) ;
                                                if ( ($cat_level_enable == 'yes') && ($cat_level_enable != '') ) {
                                                    $cat_level_price_type = srp_term_meta( $term->term_id , 'point_price_category_type' ) ;
                                                    if ( $cat_level_price_type == '1' ) {
                                                        $cat_level_fixed_price = srp_term_meta( $term->term_id , 'rs_category_points_price' ) ;

                                                        $data[] = ($cat_level_fixed_price == '') ? self::get_global_vlaue( $product_price ) : $cat_level_fixed_price ;
                                                    } else {
                                                        $data[] = redeem_point_conversion( $product_price , get_current_user_id() ) ;
                                                    }
                                                } else {
                                                    $data[] = self::get_global_vlaue( $product_price ) ;
                                                }
                                            }
                                        } else {
                                            $data[] = self::get_global_vlaue( $product_price ) ;
                                        }
                                    } else {
                                        $data[] = $product_level_fixed_price ;
                                    }
                                } else {
                                    $data[] = redeem_point_conversion( $product_price , get_current_user_id() ) ;
                                }
                            }
                        } else {
                            $data[] = '' ;
                        }
                        if ( ! empty( $data ) )
                            $getpointprice = max( $data ) ;
                    }
                    $finalpointprice = round_off_type( $getpointprice ) ;

                    $pointpricemessage = display_point_price_value( $getpointprice ) ;

                    $label1 = '/' ;
                    if ( $finalpointprice == '0' || $finalpointprice == '' ) {
                        $finalpointprice = '' ;
                        $label1          = '' ;
                    }
                    update_post_meta( $product_id , 'booking_points' , $finalpointprice ) ;
                    $type[] = check_display_price_type( $product_id ) ;
                    if ( in_array( 2 , $type ) ) {
                        die( json_encode( array(
                            'result' => 'SUCCESS' ,
                            'html'   => __( 'Booking cost' , 'woocommerce-bookings' ) . ': <strong>' . $pointpricemessage . '</strong>'
                        ) ) ) ;
                    } else {
                        die( json_encode( array(
                            'result' => 'SUCCESS' ,
                            'html'   => __( 'Booking cost' , 'woocommerce-bookings' ) . ': <strong>' . wc_price( $product_price ) . $label1 . $pointpricemessage . '</strong>'
                        ) ) ) ;
                    }
                }
            }
        }

        public static function get_global_vlaue( $product_price ) {
            $data = array() ;
            if ( get_option( 'rs_local_enable_disable_point_price_for_product' ) == '1' ) {
                if ( get_option( 'rs_global_point_price_type' ) == '1' && get_option( 'rs_local_price_points_for_product' ) != '' ) {
                    $data[] = get_option( 'rs_local_price_points_for_product' ) ;
                } else {
                    $data[] = redeem_point_conversion( $product_price , get_current_user_id() ) ;
                }
            }
            return $data ;
        }

        //register enqueue script for to perform redeeming on cart FP_Reward_Points_Main_Path
        public static function enqueue_scripts() {
            global $woocommerce ;
            if ( ( float ) $woocommerce->version >= ( float ) ('2.6.0') ) {
                $minimum_points    = get_option( "rs_minimum_redeeming_points" ) ;
                $maximum_points    = get_option( "rs_maximum_redeeming_points" ) ;
                $error_msg_min_max = do_shortcode( addslashes( get_option( "rs_minimum_and_maximum_redeem_point_error_message" ) ) ) ;
                $error_msg_min     = do_shortcode( addslashes( get_option( "rs_minimum_redeem_point_error_message" ) ) ) ;
                $error_msg_max     = do_shortcode( addslashes( get_option( "rs_maximum_redeem_point_error_message" ) ) ) ;

//Storefront theme Redeembutton compatibitily          

                $redeem_buton_display = class_exists( 'Storefront' ) ? true : false ;
                if ( class_exists( 'WC_Bookings' ) ) {
                    if ( is_shop() ) {
                        wp_enqueue_script( 'jquery' ) ;
                        wp_register_script( 'pointpricecompatibility' , SRP_PLUGIN_DIR_URL . "assets/js/pointpricecompatibility.js" ) ;
                        $global_variable_for_js = array( 'wp_ajax_url' => SRP_ADMIN_AJAX_URL , 'user_id' => get_current_user_id() ) ;
                        wp_localize_script( 'pointpricecompatibility' , 'pointpricecompatibility_variable_js' , $global_variable_for_js ) ;
                        wp_enqueue_script( 'pointpricecompatibility' , false , array() , '' , true ) ;
                    }
                }

                if ( is_checkout() && is_user_logged_in() ) {
                    wp_enqueue_script( 'jquery' ) ;
                    wp_register_script( 'checkoutscript' , SRP_PLUGIN_DIR_URL . "assets/js/checkoutscript.js" ) ;
                    $global_variable_for_js = array( 'wp_ajax_url' => SRP_ADMIN_AJAX_URL , 'user_id' => get_current_user_id() , 'redeem_it_link' => get_option( 'rs_show_hide_redeem_it_field_checkout' ) , 'redeem_restriction' => get_option( 'rs_show_hide_redeem_field' ) , 'checkout_redeem_check' => get_option( 'rs_show_hide_redeem_field_checkout' ) , 'rs_available_message_check' => get_option( 'rs_available_points_display' ) , '_rs_storefront_redeem_button' => $redeem_buton_display ) ;
                    wp_localize_script( 'checkoutscript' , 'checkoutscript_variable_js' , $global_variable_for_js ) ;
                    wp_enqueue_script( 'checkoutscript' , false , array() , '' , true ) ;
                }
            }
        }

    }

    FP_Reward_Points_WC_2P6::init() ;
}