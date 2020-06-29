<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSRedeemingFrontend' ) ) {

    class RSRedeemingFrontend {

        public static function init() {
            add_action( 'wp_loaded' , array( __CLASS__ , 'redeem_points_for_user_automatically' ) , 10 ) ;

            add_action( 'wp_head' , array( __CLASS__ , 'redeem_point_for_user' ) ) ;

            if ( WC_VERSION >= ( float ) '3.7.0' ) {
                add_action( 'woocommerce_remove_cart_item' , array( __CLASS__ , 'trash_sumo_coupon_if_cart_empty' ) , 10 , 2 ) ;
            } else {
                //For newer version of Woocommerce (i.e) Version > 2.3.0;
                add_action( 'woocommerce_cart_item_removed' , array( __CLASS__ , 'trash_sumo_coupon_if_cart_empty' ) , 10 , 2 ) ;
                //For older version of Woocommerce (i.e) Version < 2.3.0;
                add_action( 'woocommerce_before_cart_item_quantity_zero' , array( __CLASS__ , 'trash_sumo_coupon_if_cart_empty' ) , 10 , 2 ) ;
            }

            add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'trash_sumo_coupon_if_order_placed' ) , 10 , 2 ) ;

            add_action( 'rs_delete_coupon_based_on_cron' , array( __CLASS__ , 'trash_sumo_coupon_based_on_cron_time' ) , 10 , 1 ) ;

            add_action( 'woocommerce_removed_coupon' , array( __CLASS__ , 'unset_session' ) ) ;

            add_action( 'woocommerce_after_calculate_totals' , array( __CLASS__ , 'trash_sumo_coupon_if_restricted' ) , 10 , 1 ) ;

            add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'validation_for_redeeming' ) ) ;

            add_action( 'woocommerce_after_cart_totals' , array( __CLASS__ , 'redeem_field_based_on_settings' ) ) ;

            add_action( 'woocommerce_after_checkout_form' , array( __CLASS__ , 'redeem_field_based_on_settings' ) ) ;

            if ( get_option( 'rs_reward_point_troubleshoot_after_cart' ) == '1' ) {
                add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'default_redeem_field_in_cart_and_checkout' ) ) ;
            } elseif ( get_option( 'rs_reward_point_troubleshoot_after_cart' ) == '2' ) {
                add_action( 'woocommerce_cart_coupon' , array( __CLASS__ , 'default_redeem_field_in_cart_and_checkout' ) ) ;
            } else {
                add_action( 'woocommerce_cart_actions' , array( __CLASS__ , 'default_redeem_field_in_cart_and_checkout' ) ) ;
            }

            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'default_redeem_field_in_cart_and_checkout' ) ) ;

            add_filter( 'woocommerce_cart_item_removed_title' , array( __CLASS__ , 'update_coupon_amount' ) , 10 , 1 ) ;

            add_filter( 'woocommerce_update_cart_action_cart_updated' , array( __CLASS__ , 'update_coupon_amount' ) , 10 , 1 ) ;

            if ( get_option( 'rs_message_before_after_cart_table' ) == '1' ) {
                if ( get_option( 'rs_reward_point_troubleshoot_before_cart' ) == '1' ) {
                    add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'messages_for_redeeming' ) ) ;
                } else {
                    add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'messages_for_redeeming' ) ) ;
                }
            } else {
                add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'messages_for_redeeming' ) ) ;
            }
            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'messages_for_redeeming' ) ) ;

            add_filter( 'woocommerce_cart_totals_coupon_label' , array( __CLASS__ , 'change_coupon_label' ) , 1 , 2 ) ;

            add_filter( 'woocommerce_coupons_enabled' , array( __CLASS__ , 'hide_coupon_field_on_checkout' ) ) ;

            add_filter( 'woocommerce_checkout_coupon_message' , array( __CLASS__ , 'hide_coupon_message' ) ) ;

            add_filter( 'woocommerce_coupon_error' , array( __CLASS__ , 'error_message_for_sumo_coupon' ) , 10 , 3 ) ;

            add_filter( 'woocommerce_coupon_message' , array( __CLASS__ , 'success_message_for_sumo_coupon' ) , 10 , 3 ) ;

            add_filter( 'woocommerce_add_message' , array( __CLASS__ , 'replace_msg_for_remove_coupon' ) , 10 , 1 ) ;

            add_filter( 'woocommerce_available_payment_gateways' , array( __CLASS__ , 'unset_gateways_for_excluded_product_to_redeem' ) , 10 , 1 ) ;
        }

        /* Trash Applied SUMO Coupons if Cart Empty */

        public static function trash_sumo_coupon_if_cart_empty( $cart_item_key , $cart_object ) {
            if ( WC()->cart->is_empty() ) {
                $CouponId = get_user_meta( get_current_user_id() , 'redeemcouponids' , true ) ;
                if ( ! empty( $CouponId ) )
                    wp_trash_post( $CouponId ) ;

                $CouponId = get_user_meta( get_current_user_id() , 'auto_redeemcoupon_ids' , true ) ;
                if ( ! empty( $CouponId ) )
                    wp_trash_post( $CouponId ) ;
            }
        }

        /* Trash SUMO Coupon when it satisfies the Reward Restriction */

        public static function trash_sumo_coupon_if_restricted( $CartObj ) {
            if ( ! is_user_logged_in() )
                return ;

            $MinCartTotalToRedeem = ( float ) get_option( 'rs_minimum_cart_total_points' ) ;
            $MaxCartTotalToRedeem = ( float ) get_option( 'rs_maximum_cart_total_points' ) ;
            if ( empty( $MinCartTotalToRedeem ) && empty( $MaxCartTotalToRedeem ) )
                return ;

            $CartSubtotal = srp_cart_subtotal() ;
            $CouponId     = 0 ;
            $UserInfo     = get_user_by( 'id' , get_current_user_id() ) ;
            $Username     = $UserInfo->user_login ;
            $Redeem       = 'sumo_' . strtolower( "$Username" ) ;
            $AutoRedeem   = 'auto_redeem_' . strtolower( "$Username" ) ;
            foreach ( $CartObj->get_applied_coupons() as $CouponCode ) {
                if ( $CouponCode == $Redeem ) {
                    $CouponId = get_user_meta( get_current_user_id() , 'redeemcouponids' , true ) ;
                } else if ( $CouponCode == $AutoRedeem ) {
                    $CouponId = get_user_meta( get_current_user_id() , 'auto_redeemcoupon_ids' , true ) ;
                }
                if ( ( ! empty( $MinCartTotalToRedeem ) && $CartSubtotal < $MinCartTotalToRedeem) || ( ! empty( $MaxCartTotalToRedeem ) && $CartSubtotal > $MaxCartTotalToRedeem) ) {
                    if ( ! empty( $CouponId ) )
                        wp_trash_post( $CouponId ) ;
                }
            }
        }

        /* Trash SUMO Coupon when Order Placed */

        public static function trash_sumo_coupon_if_order_placed( $OrderId , $order_post ) {
            $order    = new WC_Order( $OrderId ) ;
            $OrderObj = srp_order_obj( $order ) ;
            $UserId   = $OrderObj[ 'order_userid' ] ;
            if ( empty( $UserId ) )
                return ;

            $UserInfo     = get_user_by( 'id' , $UserId ) ;
            $UserName     = $UserInfo->user_login ;
            $Redeem       = 'sumo_' . strtolower( $UserName ) ;
            $AutoRedeem   = 'auto_redeem_' . strtolower( $UserName ) ;
            $group        = 'coupons' ;
            $used_coupons = ( float ) WC()->version < ( float ) ('3.7') ? $order->get_used_coupons() : $order->get_coupon_codes() ;
            if ( ! (in_array( $Redeem , $used_coupons ) || in_array( $AutoRedeem , $used_coupons ) ) ) {
                update_post_meta( $OrderId , 'rs_check_enable_option_for_redeeming' , 'no' ) ;
                return ;
            }

            foreach ( $used_coupons as $CouponCode ) {
                $CouponId   = ($Redeem == $CouponCode) ? get_user_meta( $UserId , 'redeemcouponids' , true ) : get_user_meta( $UserId , 'auto_redeemcoupon_ids' , true ) ;
                $CouponName = ($Redeem == $CouponCode) ? $Redeem : $AutoRedeem ;
                if ( empty( $CouponId ) )
                    continue ;

                if ( get_option( '_rs_restrict_coupon' ) == '1' || get_option( '_rs_enable_coupon_restriction' ) == 'no' ) {
                    wp_trash_post( $CouponId ) ;
                } else {
                    self::schedule_cron_to_trash_sumo_coupon( $CouponId ) ;
                }
                if ( class_exists( 'WC_Cache_Helper' ) )
                    wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_' . $CouponName , 'coupons' ) ;
            }
            $EnableRedeem = (in_array( $Redeem , $used_coupons ) || in_array( $AutoRedeem , $used_coupons )) ? get_option( 'rs_enable_redeem_for_order' ) : (srp_check_is_array( $used_coupons ) ? get_option( 'rs_disable_point_if_coupon' ) : 'no') ;
            update_post_meta( $OrderId , 'rs_check_enable_option_for_redeeming' , $EnableRedeem ) ;
        }

        public static function schedule_cron_to_trash_sumo_coupon( $CouponId ) {
            $time = get_option( 'rs_delete_coupon_specific_time' ) ;
            if ( empty( $time ) )
                return ;

            $CouponId = array( 'rs_coupon_id' => $CouponId ) ;
            if ( get_option( 'rs_delete_coupon_by_cron_time' ) == '1' ) {
                $NextScheduleTime = time() + (24 * 60 * 60 * $time) ;
            } elseif ( get_option( 'rs_delete_coupon_by_cron_time' ) == '2' ) {
                $NextScheduleTime = time() + (60 * 60 * $time) ;
            } else {
                $NextScheduleTime = time() + (60 * $time) ;
            }
            if ( wp_next_scheduled( $NextScheduleTime , 'rs_delete_coupon_based_on_cron' ) == false )
                wp_schedule_single_event( $NextScheduleTime , 'rs_delete_coupon_based_on_cron' , $CouponId ) ;
        }

        /* Trash SUMO Coupon when Cron time Reached */

        public static function trash_sumo_coupon_based_on_cron_time( $CouponId ) {
            wp_trash_post( $CouponId ) ;
        }

        /* Validate Redeeming in Cart/Checkout */

        public static function validation_for_redeeming() {
            if ( ! is_user_logged_in() )
                return ;

            $BanningType = check_banning_type( get_current_user_id() ) ;
            if ( $BanningType == 'redeemingonly' || $BanningType == 'both' )
                return ;

            $CartSubtotal = srp_cart_subtotal() ;
            if ( empty( $CartSubtotal ) )
                return ;

            if ( check_if_pointprice_product_exist_in_cart() )
                return ;

            if ( get_option( 'rs_redeem_field_type_option' ) == 2 )
                return ;

            $MemRestrict = 'no' ;
            if ( get_option( 'rs_restrict_redeem_when_no_membership_plan' ) == 'yes' && function_exists( 'check_plan_exists' ) )
                $MemRestrict = check_plan_exists( get_current_user_id() ) ? 'no' : 'yes' ;

            if ( $MemRestrict == 'yes' )
                return ;

            $MinCartTotalToRedeem = get_option( 'rs_minimum_cart_total_points' ) ;
            $MaxCartTotalToRedeem = get_option( 'rs_maximum_cart_total_points' ) ;
            if ( ! empty( $MinCartTotalToRedeem ) && ! empty( $MaxCartTotalToRedeem ) ) {
                if ( $CartSubtotal < $MinCartTotalToRedeem && $CartSubtotal > $MaxCartTotalToRedeem ) {
                    if ( get_option( 'rs_show_hide_maximum_cart_total_error_message' ) == '1' ) {
                        $CurrencyValue = srp_formatted_price( round_off_type_for_currency( $MinCartTotalToRedeem ) ) ;
                        $ReplacedMsg   = str_replace( "[carttotal]" , $CurrencyValue , get_option( 'rs_min_cart_total_redeem_error' ) ) ;
                        $ReplacedMsg   = str_replace( "[currencysymbol]" , "" , $ReplacedMsg ) ;
                        ?>
                        <div class="woocommerce-error"><?php echo $ReplacedMsg ; ?></div>
                        <?php
                    }
                }
            } else if ( ! empty( $MinCartTotalToRedeem ) && empty( $MaxCartTotalToRedeem ) ) {
                if ( $CartSubtotal < $MinCartTotalToRedeem ) {
                    if ( get_option( 'rs_show_hide_minimum_cart_total_error_message' ) == '1' ) {
                        $CurrencyValue = srp_formatted_price( round_off_type_for_currency( $MinCartTotalToRedeem ) ) ;
                        $ReplacedMsg   = str_replace( "[carttotal]" , $CurrencyValue , get_option( 'rs_min_cart_total_redeem_error' ) ) ;
                        $ReplacedMsg   = str_replace( "[currencysymbol]" , "" , $ReplacedMsg ) ;
                        ?>
                        <div class="woocommerce-error"><?php echo $ReplacedMsg ; ?></div>
                        <?php
                    }
                }
            } else if ( empty( $MinCartTotalToRedeem ) && ! empty( $MaxCartTotalToRedeem ) ) {
                if ( $CartSubtotal > $MaxCartTotalToRedeem ) {
                    if ( get_option( 'rs_show_hide_maximum_cart_total_error_message' ) == '1' ) {
                        $CurrencyValue = srp_formatted_price( round_off_type_for_currency( $MaxCartTotalToRedeem ) ) ;
                        $ReplacedMsg   = str_replace( "[carttotal]" , $CurrencyValue , get_option( 'rs_max_cart_total_redeem_error' ) ) ;
                        $ReplacedMsg   = str_replace( "[currencysymbol]" , "" , $ReplacedMsg ) ;
                        ?>
                        <div class="woocommerce-error"><?php echo $ReplacedMsg ; ?></div>
                        <?php
                    }
                }
            }
        }

        /* Default Redeem Field in Cart/Checkout */

        public static function default_redeem_field_in_cart_and_checkout() {
            if ( ! is_user_logged_in() )
                return ;

            $ShowRedeemField = is_cart() ? get_option( 'rs_redeem_field_type_option' ) : get_option( 'rs_redeem_field_type_option_checkout' ) ;
            if ( $ShowRedeemField == 2 )
                return ;

            $MemRestrict = 'no' ;
            if ( get_option( 'rs_restrict_redeem_when_no_membership_plan' ) == 'yes' && function_exists( 'check_plan_exists' ) )
                $MemRestrict = check_plan_exists( get_current_user_id() ) ? 'no' : 'yes' ;

            if ( $MemRestrict == 'yes' )
                return ;

            $MinCartTotal = get_option( 'rs_minimum_cart_total_points' ) ;
            $MaxCartTotal = get_option( 'rs_maximum_cart_total_points' ) ;
            $CartSubTotal = srp_cart_subtotal() ;
            if ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
                if ( $CartSubTotal >= $MinCartTotal && $CartSubTotal <= $MaxCartTotal ) {
                    self::default_redeem_field() ;
                }
            } else if ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
                if ( $CartSubTotal >= $MinCartTotal ) {
                    self::default_redeem_field() ;
                }
            } else if ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
                if ( $CartSubTotal <= $MaxCartTotal ) {
                    self::default_redeem_field() ;
                }
            } else if ( empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
                self::default_redeem_field() ;
            }
        }

        public static function default_redeem_field() {
            if ( ! self::product_filter_for_redeem_field() )
                return ;

            if ( check_if_pointprice_product_exist_in_cart() )
                return ;

            $UserId  = get_current_user_id() ;
            $BanType = check_banning_type( $UserId ) ;
            if ( $BanType == 'redeemingonly' || $BanType == 'both' )
                return ;

            $PointPriceValue = array() ;
            $PointPriceType  = array() ;
            $PointsData      = new RS_Points_Data( $UserId ) ;
            $Points          = $PointsData->total_available_points() ;
            $UserInfo        = get_user_by( 'id' , $UserId ) ;
            $Username        = $UserInfo->user_login ;
            $AutoRedeem      = 'auto_redeem_' . strtolower( $Username ) ;
            $AppliedCoupons  = WC()->cart->get_applied_coupons() ;
            foreach ( WC()->cart->cart_contents as $item ) {
                $ProductId         = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                $PointPriceType[]  = check_display_price_type( $ProductId ) ;
                $CheckIfEnable     = calculate_point_price_for_products( $ProductId ) ;
                if ( ! empty( $CheckIfEnable[ $ProductId ] ) )
                    $PointPriceValue[] = $CheckIfEnable[ $ProductId ] ;
            }
            if ( $Points > 0 ) {
                $MinUserPoints = (get_user_meta( $UserId , 'rsfirsttime_redeemed' , true ) != '1') ? get_option( "rs_first_time_minimum_user_points" ) : get_option( "rs_minimum_user_points_to_redeem" ) ;
                if ( $Points >= $MinUserPoints ) {
                    if ( srp_cart_subtotal() >= get_option( 'rs_minimum_cart_total_points' ) ) {
                        if ( ! in_array( $AutoRedeem , $AppliedCoupons ) ) {
                            if ( ! srp_check_is_array( $PointPriceValue ) && ! in_array( '2' , $PointPriceType ) ) {
                                if ( is_cart() ) {
                                    ?>
                                    <div class="fp_apply_reward">
                                        <?php if ( get_option( "rs_show_hide_redeem_caption" ) == '1' ) { ?>
                                            <label id = "default_field" for="rs_apply_coupon_code_field"><?php echo get_option( 'rs_redeem_field_caption' ) ; ?></label>
                                        <?php } ?>
                                        <?php $placeholder = get_option( 'rs_show_hide_redeem_placeholder' ) == '1' ? get_option( 'rs_redeem_field_placeholder' ) : '' ; ?>
                                        <input id="rs_apply_coupon_code_field" class="input-text" type="text" placeholder="<?php echo $placeholder ; ?>" value="" name="rs_apply_coupon_code_field">
                                        <input class="button <?php echo get_option( 'rs_extra_class_name_apply_reward_points' ) ; ?>" type="submit" id='mainsubmi' value="<?php echo get_option( 'rs_redeem_field_submit_button_caption' ) ; ?>" name="rs_apply_coupon_code">
                                    </div>
                                    <div class='rs_warning_message' style='display:inline-block;color:red'></div>
                                    <?php
                                } elseif ( is_checkout() && get_option( 'rs_show_hide_redeem_field_checkout' ) == '1' ) {

                                    $extra_message = apply_filters( 'rs_extra_messages_for_redeeming' , '' ) ;
                                    ?>
                                    <div class="checkoutredeem">
                                        <div class="woocommerce-info">

                                            <?php if ( $extra_message ) : ?>
                                                <div class="rs_add_extra_notice">
                                                    <?php echo do_shortcode( $extra_message ) ; ?>
                                                </div>
                                            <?php endif ; ?>

                                            <?php echo get_option( 'rs_reedming_field_label_checkout' ) ; ?> 
                                            <a href="javascript:void(0)" class="redeemit"> <?php echo get_option( 'rs_reedming_field_link_label_checkout' ) ; ?></a>
                                        </div>
                                    </div>
                                    <form name="checkout_redeeming" class="checkout_redeeming" method="post">
                                        <div class="fp_apply_reward">
                                            <?php if ( get_option( "rs_show_hide_redeem_caption" ) == '1' ) { ?>
                                                <label id = "default_field" for="rs_apply_coupon_code_field"><?php echo get_option( 'rs_redeem_field_caption' ) ; ?></label>
                                            <?php } ?>
                                            <?php $placeholder = get_option( 'rs_show_hide_redeem_placeholder' ) == '1' ? get_option( 'rs_redeem_field_placeholder' ) : '' ; ?>
                                            <input id="rs_apply_coupon_code_field" class="input-text" type="text" placeholder="<?php echo $placeholder ; ?>" value="" name="rs_apply_coupon_code_field">
                                            <input class="button <?php echo get_option( 'rs_extra_class_name_apply_reward_points' ) ; ?>" type="submit" id='mainsubmi' value="<?php echo get_option( 'rs_redeem_field_submit_button_caption' ) ; ?>" name="rs_apply_coupon_code">
                                        </div>
                                        <div class='rs_warning_message' style='display:inline-block;color:red'></div>
                                    </form>
                                    <?php
                                }
                            }
                        }
                    } else {
                        if ( get_option( 'rs_show_hide_minimum_cart_total_error_message' ) == '1' ) {
                            $CurrencyValue = srp_formatted_price( round_off_type_for_currency( get_option( 'rs_minimum_cart_total_points' ) ) ) ;
                            $ReplacedMsg   = str_replace( "[carttotal]" , $CurrencyValue , get_option( 'rs_min_cart_total_redeem_error' ) ) ;
                            $FinalMsg      = str_replace( "[currencysymbol]" , "" , $ReplacedMsg ) ;
                            ?>
                            <div class="woocommerce-info"><?php echo $FinalMsg ; ?></div>
                            <?php
                        }
                    }
                } else {
                    if ( get_user_meta( $UserId , 'rsfirsttime_redeemed' , true ) != '1' ) {
                        if ( get_option( 'rs_show_hide_first_redeem_error_message' ) == '1' ) {
                            $ReplacedMsg = str_replace( "[firstredeempoints]" , get_option( 'rs_first_time_minimum_user_points' ) , get_option( 'rs_min_points_first_redeem_error_message' ) ) ;
                            ?>
                            <div class="woocommerce-info"><?php echo $ReplacedMsg ; ?></div>
                            <?php
                        }
                    } else {
                        if ( get_option( 'rs_show_hide_after_first_redeem_error_message' ) == '1' ) {
                            $ReplacedMsg = str_replace( "[points_after_first_redeem]" , get_option( 'rs_minimum_user_points_to_redeem' ) , get_option( 'rs_min_points_after_first_error' ) ) ;
                            ?>
                            <div class="woocommerce-info"><?php echo $ReplacedMsg ; ?></div>
                            <?php
                        }
                    }
                }
            } else {
                if ( get_option( 'rs_show_hide_points_empty_error_message' ) == '1' && ! srp_check_is_array( $PointPriceValue ) ) {
                    ?>
                    <div class="woocommerce-info"><?php echo get_option( 'rs_current_points_empty_error_message' ) ; ?></div>
                    <?php
                }
            }
        }

        public static function product_filter_for_redeem_field() {
            if ( get_option( 'rs_hide_redeeming_field' ) == '1' )
                return true ;

            foreach ( WC()->cart->cart_contents as $item ) {
                if ( get_option( 'rs_exclude_products_for_redeeming' ) == 'yes' )
                    if ( ! self::check_exc_products( $item ) )
                        return false ;

                if ( get_option( 'rs_exclude_category_for_redeeming' ) == 'yes' )
                    if ( ! self::check_exc_categories( $item ) )
                        return false ;

                if ( get_option( 'rs_enable_redeem_for_selected_products' ) == 'yes' )
                    if ( self::check_inc_products( $item ) )
                        return true ;

                if ( get_option( 'rs_enable_redeem_for_selected_category' ) == 'yes' )
                    if ( self::check_inc_categories( $item ) )
                        return true ;
            }
            return true ;
        }

        public static function check_inc_products( $item ) {
            $ProductId       = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
            $IncludeProducts = get_option( 'rs_select_products_to_enable_redeeming' ) != '' ? get_option( 'rs_select_products_to_enable_redeeming' ) : array() ;
            $IncludeProducts = srp_check_is_array( $IncludeProducts ) ? $IncludeProducts : explode( ',' , $IncludeProducts ) ;

            if ( ! srp_check_is_array( $IncludeProducts ) )
                return true ;

            if ( in_array( $ProductId , $IncludeProducts ) )
                return true ;

            return false ;
        }

        public static function check_exc_products( $item ) {
            $ProductId       = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
            $ExcludeProducts = get_option( 'rs_exclude_products_to_enable_redeeming' ) != '' ? get_option( 'rs_exclude_products_to_enable_redeeming' ) : array() ;
            $ExcludeProducts = srp_check_is_array( $IncludeProducts ) ? $IncludeProducts : explode( ',' , $IncludeProducts ) ;

            if ( ! srp_check_is_array( $ExcludeProducts ) )
                return true ;

            if ( in_array( $ProductId , $ExcludeProducts ) )
                return false ;

            return true ;
        }

        public static function check_inc_categories( $item ) {
            $ProductId        = $item[ 'product_id' ] ;
            $IncludedCategory = get_option( 'rs_select_category_to_enable_redeeming' ) != '' ? get_option( 'rs_select_category_to_enable_redeeming' ) : array() ;
            $IncludedCategory = srp_check_is_array( $IncludedCategory ) ? $IncludedCategory : explode( ',' , $IncludedCategory ) ;

            if ( ! srp_check_is_array( $IncludedCategory ) )
                return true ;

            $ProductCat = get_the_terms( $ProductId , 'product_cat' ) ;
            if ( ! srp_check_is_array( $ProductCat ) )
                return true ;

            foreach ( $ProductCat as $Cat ) {
                if ( in_array( $Cat->term_id , $IncludedCategory ) )
                    return true ;
            }

            return false ;
        }

        public static function check_exc_categories( $item ) {
            $ProductId        = $item[ 'product_id' ] ;
            $ExcludedCategory = get_option( 'rs_exclude_category_to_enable_redeeming' ) != '' ? get_option( 'rs_exclude_category_to_enable_redeeming' ) : array() ;
            $ExcludedCategory = srp_check_is_array( $IncludedCategory ) ? $IncludedCategory : explode( ',' , $IncludedCategory ) ;

            if ( ! srp_check_is_array( $ExcludedCategory ) )
                return true ;

            $ProductCat = get_the_terms( $ProductId , 'product_cat' ) ;
            if ( ! srp_check_is_array( $ProductCat ) )
                return true ;

            foreach ( $ProductCat as $Cat ) {
                if ( in_array( $Cat->term_id , $ExcludedCategory ) )
                    return false ;
            }

            return true ;
        }

        /* Hide Redeeming Field in Cart and Checkout  */

        public static function redeem_field_based_on_settings() {
            if ( ! is_user_logged_in() )
                return ;

            /* Hide Redeem field before Points applied - Start */
            $HideRedeemField = get_option( 'rs_show_redeeming_field' ) ;
            if ( get_option( 'rs_show_hide_redeem_field' ) == '1' || get_option( 'rs_show_hide_redeem_field' ) == '5' ) { //Show Redeem Field
                if ( $HideRedeemField == 2 && check_if_discount_applied() ) {
                    echo self::cart_redeem_field( 'hide' ) ;
                    echo self::checkout_redeem_field( 'hide' ) ;
                } else {
                    echo self::cart_redeem_field( 'show' ) ;
                    echo self::checkout_redeem_field( 'show' ) ;
                }
            } elseif ( get_option( 'rs_show_hide_redeem_field' ) == '3' ) { //Hide Redeem Field
                echo self::cart_redeem_field( 'hide' ) ;
                echo self::checkout_redeem_field( 'hide' ) ;
            } else { //Hide Coupon and Redeem Field
                echo woocommerce_coupon_field( 'hide' ) ;
                if ( get_option( 'rs_show_hide_redeem_field' ) == '2' ) {
                    if ( $HideRedeemField == 2 && $CheckIfDiscount ) {
                        echo self::cart_redeem_field( 'hide' ) ;
                        echo self::checkout_redeem_field( 'hide' ) ;
                    } else {
                        echo self::cart_redeem_field( 'show' ) ;
                        echo self::checkout_redeem_field( 'show' ) ;
                    }
                }
                if ( get_option( 'rs_show_hide_redeem_field' ) == '4' ) {
                    echo self::cart_redeem_field( 'hide' ) ;
                    echo self::checkout_redeem_field( 'hide' ) ;
                }
            }
            /* Hide Redeem field before Points applied - End */
            $AppliedCoupons = WC()->cart->get_applied_coupons() ;
            if ( ! srp_check_is_array( $AppliedCoupons ) )
                return ;

            $UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
            $Username   = $UserInfo->user_login ;
            $Redeem     = 'sumo_' . strtolower( "$Username" ) ;
            $AutoRedeem = 'auto_redeem_' . strtolower( "$Username" ) ;
            if ( get_option( '_rs_not_allow_earn_points_if_sumo_coupon' ) == 'yes' ) {
                foreach ( $AppliedCoupons as $Code ) {
                    $CouponObj         = new WC_Coupon( $Code ) ;
                    $CouponObj         = srp_coupon_obj( $CouponObj ) ;
                    $CouponId          = $CouponObj[ 'coupon_id' ] ;
                    $CheckIfSUMOCoupon = get_post_meta( $CouponId , 'sumo_coupon_check' , true ) ;
                    if ( $CheckIfSUMOCoupon == 'yes' )
                        self::cart_redeem_field( 'hide' ) ;
                }
            }
            /* Hide Redeem field after Points applied - Start */
            echo (in_array( $Redeem , $AppliedCoupons ) || in_array( $AutoRedeem , $AppliedCoupons )) ? self::cart_redeem_field( 'hide' ) : self::cart_redeem_field( 'show' ) ;
            echo (in_array( $Redeem , $AppliedCoupons ) || in_array( $AutoRedeem , $AppliedCoupons )) ? self::checkout_redeem_field( 'hide' ) : self::checkout_redeem_field( 'show' ) ;
            /* Hide Redeem field after Points applied - End */
        }

        public static function cart_redeem_field( $Param ) {
            ?>
            <style type="text/css">
                .fp_apply_reward, .rs_button_redeem_cart{
                    <?php if ( $Param == 'show' ) { ?>
                        display: block ;
                    <?php } else { ?>
                        display: none ;
                    <?php } ?>
                }
                <?php if ( get_option( 'rs_reward_point_troubleshoot_after_cart' ) == '2' ) { ?>
                    .fp_apply_reward #default_field{
                        margin-top:5px;
                        display:inline-block !important;
                        float:left;
                        line-height:27px;
                        position:static;
                    }
                <?php } ?>
            </style>
            <?php
        }

        public static function checkout_redeem_field( $Param ) {
            ?>
            <style type="text/css">
                .checkoutredeem, .rs_button_redeem_checkout{
                    <?php if ( $Param == 'show' ) { ?>
                        display: block ;
                    <?php } else { ?>
                        display: none ;
                    <?php } ?>
                }
            </style>
            <?php
        }

        /* Update Coupon Amount */

        public static function update_coupon_amount( $BoolVal ) {
            if ( ! is_user_logged_in() )
                return $BoolVal ;

            if ( get_option( 'rs_max_redeem_discount' ) == '1' )
                return $BoolVal ;

            $AppliedCoupons = WC()->cart->get_applied_coupons() ;
            if ( ! srp_check_is_array( $AppliedCoupons ) )
                return $BoolVal ;

            $CartTotal    = (get_option( 'woocommerce_prices_include_tax' ) == 'yes') ? WC()->cart->subtotal : WC()->cart->subtotal_ex_tax ;
            $MinCartTotal = get_option( 'rs_minimum_cart_total_points' ) ;
            $MaxCartTotal = get_option( 'rs_maximum_cart_total_points' ) ;
            $ProductTotal = self::get_sum_of_selected_products() ;
            $RedeemValue  = get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) == 1 ? $CartTotal : $ProductTotal ;
            WC()->cart->calculate_totals() ;
            foreach ( $AppliedCoupons as $Code ) {
                $CouponObj  = new WC_Coupon( $Code ) ;
                $CouponObj  = srp_coupon_obj( $CouponObj ) ;
                $CouponAmnt = $CouponObj[ 'coupon_amount' ] ;
                $CouponId   = $CouponObj[ 'coupon_id' ] ;
                $UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
                $Username   = $UserInfo->user_login ;
                $Redeem     = 'sumo_' . strtolower( "$Username" ) ;
                $AutoRedeem = 'auto_redeem_' . strtolower( "$Username" ) ;
                if ( ($Code != $Redeem ) && ($Code != $AutoRedeem) )
                    continue ;

                if ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
                    if ( $CartTotal < $MinCartTotal || $CartTotal > $MaxCartTotal ) {
                        if ( ! empty( $CouponId ) )
                            wp_trash_post( $CouponId ) ;
                    }
                } else if ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
                    if ( $CartTotal < $MinCartTotal ) {
                        if ( ! empty( $CouponId ) )
                            wp_trash_post( $CouponId ) ;
                    }
                } else if ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
                    if ( $CartTotal > $MaxCartTotal ) {
                        if ( ! empty( $CouponId ) )
                            wp_trash_post( $CouponId ) ;
                    }
                }

                $MaxDiscountAmntForDefault = ! empty( get_option( 'rs_percent_max_redeem_discount' ) ) ? (get_option( 'rs_percent_max_redeem_discount' ) / 100) * $RedeemValue : 0 ;
                $MaxDiscountAmntForButton  = ! empty( get_option( 'rs_percentage_cart_total_redeem' ) ) ? (get_option( 'rs_percentage_cart_total_redeem' ) / 100) * $RedeemValue : 0 ;
                $Discount                  = (get_option( 'rs_redeem_field_type_option' ) == 2) ? $MaxDiscountAmntForButton : $MaxDiscountAmntForDefault ;
                if ( $CouponAmnt <= $Discount )
                    continue ;

                update_post_meta( $CouponId , 'coupon_amount' , $Discount ) ;
            }
            return $BoolVal ;
        }

        public static function unset_session() {
            WC()->session->set( 'auto_redeemcoupon' , 'no' ) ;
        }

        /* Auto Redeeming in Cart and Checkout */

        public static function redeem_points_for_user_automatically() {
            if ( ! is_user_logged_in() )
                return ;

            if ( empty( WC()->cart->get_cart_contents_count() ) ) {
                WC()->session->set( 'auto_redeemcoupon' , 'yes' ) ;
                foreach ( WC()->cart->applied_coupons as $Code )
                    WC()->cart->remove_coupon( $Code ) ;

                return ;
            }

            $UserId     = get_current_user_id() ;
            $PointsData = new RS_Points_Data( $UserId ) ;
            $Points     = $PointsData->total_available_points() ;
            if ( empty( $Points ) )
                return ;

            if ( $Points < get_option( "rs_first_time_minimum_user_points" ) )
                return ;

            if ( $Points < get_option( "rs_minimum_user_points_to_redeem" ) )
                return ;

            if ( check_if_pointprice_product_exist_in_cart() )
                return ;

            if ( get_option( 'rs_enable_disable_auto_redeem_points' ) != 'yes' )
                return ;

            $CartSubtotal = srp_cart_subtotal() ;

            $MinCartTotal = get_option( 'rs_minimum_cart_total_points' ) ;
            $MaxCartTotal = get_option( 'rs_maximum_cart_total_points' ) ;
            if ( is_cart() )
                self::auto_redeeming_in_cart( $UserId , $Points , $CartSubtotal , $MaxCartTotal , $MinCartTotal ) ;

            if ( is_checkout() )
                self::auto_redeeming_in_checkout( $UserId , $Points , $CartSubtotal , $MaxCartTotal , $MinCartTotal ) ;
        }

        public static function auto_redeeming_in_cart( $UserId , $Points , $CartSubtotal , $MaxCartTotal , $MinCartTotal ) {
            if ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
                if ( $CartSubtotal >= $MinCartTotal && $CartSubtotal <= $MaxCartTotal )
                    self::auto_redeeming( $UserId , $Points ) ;
            } else if ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
                if ( $CartSubtotal >= $MinCartTotal )
                    self::auto_redeeming( $UserId , $Points ) ;
            } else if ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
                if ( $CartSubtotal <= $MaxCartTotal )
                    self::auto_redeeming( $UserId , $Points ) ;
            } else if ( empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
                self::auto_redeeming( $UserId , $Points ) ;
            }
        }

        public static function auto_redeeming_in_checkout( $UserId , $Points , $CartSubtotal , $MaxCartTotal , $MinCartTotal ) {
            if ( isset( $_GET[ 'remove_coupon' ] ) )
                WC()->session->set( 'auto_redeemcoupon' , 'no' ) ;

            if ( get_option( 'rs_enable_disable_auto_redeem_checkout' ) != 'yes' )
                return ;

            self::auto_redeeming_in_cart( $UserId , $Points , $CartSubtotal , $MaxCartTotal , $MinCartTotal ) ;
        }

        public static function auto_redeeming( $UserId , $Points ) {
            if ( WC()->session->get( 'auto_redeemcoupon' ) == 'no' )
                return ;

            if ( get_option( 'rs_restrict_sale_price_for_redeeming' ) == 'yes' && ! self::block_redeeming_for_sale_price_product() ) {
                wc_add_notice( __( get_option( 'rs_redeeming_message_restrict_for_sale_price_product' ) ) , 'error' ) ;
                WC()->session->set( 'auto_redeemcoupon' , 'no' ) ;
                return ;
            }

            $PointPriceType  = array() ;
            $PointPriceValue = array() ;
            $UserInfo        = get_user_by( 'id' , $UserId ) ;
            $UserName        = $UserInfo->user_login ;

            if ( WC()->cart->has_discount( 'auto_redeem_' . strtolower( $UserName ) ) )
                return ;

            // Need to Calculate Totals for Auto Redeeming on using Order Again in My Account View Orders Page [Added in V24.4.1].
            if ( is_cart() ) {
                WC()->cart->calculate_totals() ;
            }

            foreach ( WC()->cart->cart_contents as $item ) {
                $ProductId         = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                $PointPriceType[]  = check_display_price_type( $ProductId ) ;
                $CheckIfEnable     = calculate_point_price_for_products( $ProductId ) ;
                if ( ! empty( $CheckIfEnable[ $ProductId ] ) )
                    $PointPriceValue[] = $CheckIfEnable[ $ProductId ] ;
            }
            if ( srp_check_is_array( $PointPriceValue ) )
                return ;

            if ( in_array( 2 , $PointPriceType ) )
                return ;


            $CartTotal        = (get_option( 'woocommerce_prices_include_tax' ) == 'yes') ? WC()->cart->subtotal : WC()->cart->subtotal_ex_tax ;
            $ProductTotal     = self::get_sum_of_selected_products() ;
            $RedeemValue      = get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) == 1 ? $CartTotal : $ProductTotal ;
            $CartContentTotal = get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) == 1 ? WC()->cart->cart_contents_total : $ProductTotal ;
            $CartContentCount = get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) == 1 ? WC()->cart->cart_contents_count : $ProductTotal ;
            if ( $CartTotal < get_option( 'rs_minimum_cart_total_points' ) )
                return ;

            $OldCouponId = get_user_meta( $UserId , 'auto_redeemcoupon_ids' , true ) ;
            wp_delete_post( $OldCouponId , true ) ;
            if ( class_exists( 'WC_Cache_Helper' ) )
                wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_auto_redeem_' . strtolower( $UserName ) , 'coupons' ) ;

            $CouponData = array(
                'post_title'  => 'auto_redeem_' . strtolower( $UserName ) ,
                'post_status' => 'publish' ,
                'post_author' => $UserId ,
                'post_type'   => 'shop_coupon' ,
                    ) ;
            $CouponId   = wp_insert_post( $CouponData ) ;
            update_user_meta( $UserId , 'auto_redeemcoupon_ids' , $CouponId ) ;

            /* For Security Reasons added user email in Allowed Emails field of Edit Coupon page */
            $allowed_email = is_object( $UserInfo ) ? $UserInfo->user_email : '' ;
            update_post_meta( $CouponId , 'customer_email' , $allowed_email ) ;

            if ( get_option( 'rs_enable_redeem_for_selected_products' ) == 'yes' ) {
                $IncProductId = get_option( 'rs_select_products_to_enable_redeeming' ) ;
                $IncProductId = srp_check_is_array( $IncProductId ) ? $IncProductId : (empty( $IncProductId ) ? array() : explode( ',' , $IncProductId ) ) ;
                update_post_meta( $CouponId , 'product_ids' , implode( ',' , array_filter( array_map( 'intval' , $IncProductId ) ) ) ) ;
            }

            if ( get_option( 'rs_exclude_products_for_redeeming' ) == 'yes' ) {
                $ExcProductId = get_option( 'rs_exclude_products_to_enable_redeeming' ) ;
                $ExcProductId = srp_check_is_array( $ExcProductId ) ? $ExcProductId : (empty( $ExcProductId ) ? array() : explode( ',' , $ExcProductId ) ) ;
                update_post_meta( $CouponId , 'exclude_product_ids' , implode( ',' , array_filter( array_map( 'intval' , $ExcProductId ) ) ) ) ;
                $ExcludedId   = get_post_meta( $CouponId , 'exclude_product_ids' , true ) ;
                foreach ( WC()->cart->cart_contents as $key => $value ) {
                    if ( $value[ 'product_id' ] == $ExcludedId )
                        WC()->session->set( 'auto_redeemcoupon' , 'no' ) ;
                }
            }
            if ( get_option( 'rs_enable_redeem_for_selected_category' ) == 'yes' ) {
                $IncCategory = get_option( 'rs_select_category_to_enable_redeeming' ) ;
                $IncCategory = srp_check_is_array( $IncCategory ) ? $IncCategory : (empty( $IncCategory ) ? array() : explode( ',' , $IncCategory ) ) ;
                update_post_meta( $CouponId , 'product_categories' , array_filter( array_map( 'intval' , $IncCategory ) ) ) ;
            }
            if ( get_option( 'rs_exclude_category_for_redeeming' ) == 'yes' ) {
                $ExcCategory = get_option( 'rs_exclude_category_to_enable_redeeming' ) ;
                $ExcCategory = srp_check_is_array( $ExcCategory ) ? $ExcCategory : (empty( $ExcCategory ) ? array() : explode( ',' , $ExcCategory ) ) ;
                update_post_meta( $CouponId , 'exclude_product_categories' , array_filter( array_map( 'intval' , $ExcCategory ) ) ) ;
            }

            update_post_meta( $CouponId , 'carttotal' , $CartContentTotal ) ;
            update_post_meta( $CouponId , 'cartcontenttotal' , $CartContentCount ) ;
            $MaxThreshold = ! empty( get_option( 'rs_percentage_cart_total_auto_redeem' ) ) ? get_option( 'rs_percentage_cart_total_auto_redeem' ) : 100 ;
            $MaxThreshold = ($MaxThreshold / 100) * $RedeemValue ;
            if ( get_option( 'rs_max_redeem_discount' ) == '1' && ! empty( get_option( 'rs_fixed_max_redeem_discount' ) ) ) {
                if ( $MaxThreshold > get_option( 'rs_fixed_max_redeem_discount' ) ) {
                    $CouponValue = get_option( 'rs_fixed_max_redeem_discount' ) ;
                    $ErrMsg      = str_replace( '[percentage] %' , get_option( 'rs_fixed_max_redeem_discount' ) , get_option( 'rs_errmsg_for_max_discount_type' ) ) ;
                    wc_add_notice( __( $ErrMsg ) , 'error' ) ;
                } else {
                    $CouponValue = $MaxThreshold ;
                }
            } else {
                if ( ! empty( get_option( 'rs_percent_max_redeem_discount' ) ) ) {
                    $MaxRedeemDiscount = (get_option( 'rs_percent_max_redeem_discount' ) / 100) * $RedeemValue ;
                    if ( $MaxRedeemDiscount > $MaxThreshold ) {
                        $CouponValue = $MaxThreshold ;
                    } else {
                        $CouponValue = ($MaxThreshold / 100) * get_option( 'rs_percent_max_redeem_discount' ) ;
                        $ErrMsg      = str_replace( '[percentage] ' , get_option( 'rs_percent_max_redeem_discount' ) , get_option( 'rs_errmsg_for_max_discount_type' ) ) ;
                        wc_add_notice( __( $ErrMsg ) , 'error' ) ;
                    }
                } else {
                    $CouponValue = empty( $MaxThreshold ) ? $RedeemValue : $MaxThreshold ;
                }
            }
            $CouponAmnt     = redeem_point_conversion( $CouponValue , $UserId , 'price' ) ;
            $ConvertedPoint = redeem_point_conversion( $Points , $UserId , 'price' ) ;
            $Amount         = ( $CouponAmnt > $Points ) ? $ConvertedPoint : $CouponValue ;
            $Amount         = ($Amount > $ConvertedPoint) ? $ConvertedPoint : $Amount ;
            update_post_meta( $CouponId , 'coupon_amount' , $Amount ) ;
            $FreeShipping   = (get_option( 'rs_apply_shipping_tax' ) == '1') ? 'yes' : 'no' ;
            update_post_meta( $CouponId , 'free_shipping' , $FreeShipping ) ;

            if ( get_post_meta( $CouponId , 'coupon_amount' , true ) == 0 )
                return ;

            if ( ! empty( get_option( 'rs_minimum_redeeming_points' ) ) && empty( get_option( 'rs_maximum_redeeming_points' ) ) )
                if ( $CouponAmnt > get_option( 'rs_minimum_redeeming_points' ) )
                    WC()->cart->add_discount( 'auto_redeem_' . strtolower( $UserName ) ) ;

            if ( ! empty( get_option( 'rs_maximum_redeeming_points' ) ) && empty( get_option( 'rs_minimum_redeeming_points' ) ) )
                if ( $CouponAmnt < get_option( 'rs_maximum_redeeming_points' ) )
                    WC()->cart->add_discount( 'auto_redeem_' . strtolower( $UserName ) ) ;

            if ( get_option( 'rs_minimum_redeeming_points' ) == get_option( 'rs_maximum_redeeming_points' ) )
                if ( ($CouponAmnt == get_option( 'rs_minimum_redeeming_points' )) && ($CouponAmnt == get_option( 'rs_maximum_redeeming_points' )) )
                    WC()->cart->add_discount( 'auto_redeem_' . strtolower( $UserName ) ) ;

            if ( empty( get_option( 'rs_minimum_redeeming_points' ) ) && empty( get_option( 'rs_maximum_redeeming_points' ) ) )
                WC()->cart->add_discount( 'auto_redeem_' . strtolower( $UserName ) ) ;

            if ( ! empty( get_option( 'rs_minimum_redeeming_points' ) ) && ! empty( get_option( 'rs_maximum_redeeming_points' ) ) )
                if ( ($CouponAmnt >= get_option( 'rs_minimum_redeeming_points' )) && ($CouponAmnt <= get_option( 'rs_maximum_redeeming_points' )) )
                    WC()->cart->add_discount( 'auto_redeem_' . strtolower( $UserName ) ) ;

            // usage Limit and Count added in V24.4.1    
            update_post_meta( $CouponId , 'usage_limit' , '1' ) ;
            update_post_meta( $CouponId , 'usage_count' , '0' ) ;

            // Form Submit not occurs properly issue . Added Safe Redirect URL in V24.4.1.
            if ( is_cart() ) {
                wp_safe_redirect( esc_url( wc_get_cart_url() ) ) ;
                exit ;
            } else if ( is_checkout() ) {
                wp_safe_redirect( esc_url( wc_get_checkout_url() ) ) ;
                exit ;
            }
        }

        public static function block_redeeming_for_sale_price_product() {
            if ( ! srp_check_is_array( WC()->cart->cart_contents ) )
                return true ;

            foreach ( WC()->cart->cart_contents as $item ) {
                $ProductId  = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                $ProductObj = srp_product_object( $ProductId ) ;
                $SalePrice  = is_object( $ProductObj ) ? $ProductObj->get_sale_price() : '' ;
                if ( ! empty( $SalePrice ) )
                    return false ;
            }
            return true ;
        }

        public static function redeem_point_for_user() {
            if ( ! is_user_logged_in() )
                return ;

            if ( isset( $_POST[ 'rs_apply_coupon_code' ] ) || isset( $_POST[ 'rs_apply_coupon_code1' ] ) || isset( $_POST[ 'rs_apply_coupon_code2' ] ) ) {
                if ( ! isset( $_POST[ 'rs_apply_coupon_code_field' ] ) )
                    return ;

                if ( empty( $_POST[ 'rs_apply_coupon_code_field' ] ) )
                    return ;

                if ( get_option( 'rs_restrict_sale_price_for_redeeming' ) == 'yes' && ! self::block_redeeming_for_sale_price_product() ) {
                    wc_add_notice( __( get_option( 'rs_redeeming_message_restrict_for_sale_price_product' ) ) , 'error' ) ;
                    return ;
                }

                $UserId           = get_current_user_id() ;
                $CartTotal        = (get_option( 'woocommerce_prices_include_tax' ) == 'yes') ? WC()->cart->subtotal : WC()->cart->subtotal_ex_tax ;
                $ProductTotal     = self::get_sum_of_selected_products() ;
                $RedeemValue      = get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) == 1 ? $CartTotal : $ProductTotal ;
                $CartContentTotal = get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) == 1 ? WC()->cart->cart_contents_total : $ProductTotal ;
                $CartContentCount = get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) == 1 ? WC()->cart->cart_contents_count : $ProductTotal ;
                $UserInfo         = get_user_by( 'id' , $UserId ) ;
                $UserName         = $UserInfo->user_login ;
                $PointsData       = new RS_Points_Data( $UserId ) ;
                $Points           = $PointsData->total_available_points() ;
                $OldCouponId      = get_user_meta( $UserId , 'redeemcouponids' , true ) ;
                wp_delete_post( $OldCouponId , true ) ;
                if ( class_exists( 'WC_Cache_Helper' ) )
                    wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_sumo_' . strtolower( $UserName ) , 'coupons' ) ;

                $CouponData = array(
                    'post_title'   => 'sumo_' . strtolower( $UserName ) ,
                    'post_content' => '' ,
                    'post_status'  => 'publish' ,
                    'post_author'  => $UserId ,
                    'post_type'    => 'shop_coupon' ,
                        ) ;
                $CouponId   = wp_insert_post( $CouponData ) ;
                update_user_meta( $UserId , 'redeemcouponids' , $CouponId ) ;

                /* For Security Reasons added user email in Allowed Emails field of Edit Coupon page */
                $allowed_email = is_object( $UserInfo ) ? $UserInfo->user_email : '' ;
                update_post_meta( $CouponId , 'customer_email' , $allowed_email ) ;

                if ( get_option( 'rs_enable_redeem_for_selected_products' ) == 'yes' ) {
                    $IncProductId = get_option( 'rs_select_products_to_enable_redeeming' ) ;
                    $IncProductId = srp_check_is_array( $IncProductId ) ? $IncProductId : (empty( $IncProductId ) ? array() : explode( ',' , $IncProductId ) ) ;
                    update_post_meta( $CouponId , 'product_ids' , implode( ',' , array_filter( array_map( 'intval' , $IncProductId ) ) ) ) ;
                }

                if ( get_option( 'rs_exclude_products_for_redeeming' ) == 'yes' ) {
                    $ExcProductId = get_option( 'rs_exclude_products_to_enable_redeeming' ) ;
                    $ExcProductId = srp_check_is_array( $ExcProductId ) ? $ExcProductId : (empty( $ExcProductId ) ? array() : explode( ',' , $ExcProductId ) ) ;
                    update_post_meta( $CouponId , 'exclude_product_ids' , implode( ',' , array_filter( array_map( 'intval' , $ExcProductId ) ) ) ) ;
                    $ExcludedId   = get_post_meta( $CouponId , 'exclude_product_ids' , true ) ;
                    foreach ( WC()->cart->cart_contents as $key => $value ) {
                        if ( $value[ 'product_id' ] == $ExcludedId )
                            WC()->session->set( 'auto_redeemcoupon' , 'no' ) ;
                    }
                }
                if ( get_option( 'rs_enable_redeem_for_selected_category' ) == 'yes' ) {
                    $IncCategory = get_option( 'rs_select_category_to_enable_redeeming' ) ;
                    $IncCategory = srp_check_is_array( $IncCategory ) ? $IncCategory : (empty( $IncCategory ) ? array() : explode( ',' , $IncCategory ) ) ;
                    update_post_meta( $CouponId , 'product_categories' , array_filter( array_map( 'intval' , $IncCategory ) ) ) ;
                }
                if ( get_option( 'rs_exclude_category_for_redeeming' ) == 'yes' ) {
                    $ExcCategory = get_option( 'rs_exclude_category_to_enable_redeeming' ) ;
                    $ExcCategory = srp_check_is_array( $ExcCategory ) ? $ExcCategory : (empty( $ExcCategory ) ? array() : explode( ',' , $ExcCategory ) ) ;
                    update_post_meta( $CouponId , 'exclude_product_categories' , array_filter( array_map( 'intval' , $ExcCategory ) ) ) ;
                }

                update_post_meta( $CouponId , 'carttotal' , $CartContentTotal ) ;
                update_post_meta( $CouponId , 'cartcontenttotal' , $CartContentCount ) ;
                update_post_meta( $CouponId , 'discount_type' , 'fixed_cart' ) ;
                $Percentage        = isset( $_POST[ 'rs_apply_coupon_code1' ] ) ? get_option( 'rs_percentage_cart_total_redeem' , 100 ) : get_option( 'rs_percentage_cart_total_redeem_checkout' , 100 ) ;
                $Redeem_field_type = is_cart() ? get_option( 'rs_redeem_field_type_option' ) : get_option( 'rs_redeem_field_type_option_checkout' ) ;
                $MaxThreshold      = $Redeem_field_type == 1 ? $_POST[ 'rs_apply_coupon_code_field' ] : (( float ) $Percentage / 100) * $_POST[ 'rs_apply_coupon_code_field' ] ;
                $MaxThreshold      = redeem_point_conversion( $MaxThreshold , $UserId , 'price' ) ;
                if ( get_option( 'rs_max_redeem_discount' ) == '1' && ! empty( get_option( 'rs_fixed_max_redeem_discount' ) ) ) {
                    if ( $MaxThreshold > get_option( 'rs_fixed_max_redeem_discount' ) ) {
                        $CouponValue = get_option( 'rs_fixed_max_redeem_discount' ) ;
                        $ErrMsg      = str_replace( '[percentage] %' , get_option( 'rs_fixed_max_redeem_discount' ) , get_option( 'rs_errmsg_for_max_discount_type' ) ) ;
                        $ErrMsg      = do_shortcode( $ErrMsg ) ;
                        wc_add_notice( __( $ErrMsg ) , 'error' ) ;
                    } else {
                        $CouponValue = $MaxThreshold ;
                    }
                } else {
                    if ( ! empty( get_option( 'rs_percent_max_redeem_discount' ) ) ) {
                        $MaxRedeemDiscount = (get_option( 'rs_percent_max_redeem_discount' ) / 100) * $RedeemValue ;
                        if ( $MaxRedeemDiscount > $MaxThreshold ) {
                            $CouponValue = $MaxThreshold ;
                        } else {
                            $CouponValue = $MaxRedeemDiscount ;
                            $ErrMsg      = str_replace( '[percentage] ' , get_option( 'rs_percent_max_redeem_discount' ) , get_option( 'rs_errmsg_for_max_discount_type' ) ) ;
                            $ErrMsg      = do_shortcode( $ErrMsg ) ;
                            wc_add_notice( __( $ErrMsg ) , 'error' ) ;
                        }
                    } else {
                        $Applied_points = redeem_point_conversion( $_POST[ 'rs_apply_coupon_code_field' ] , $UserId , 'price' ) ;
                        $CouponValue    = ( $Applied_points > $RedeemValue ) ? ( float ) $RedeemValue : ( float ) $Applied_points ;
                    }
                }
                $CouponAmnt     = redeem_point_conversion( $CouponValue , $UserId ) ;
                $ConvertedPoint = redeem_point_conversion( $Points , $UserId , 'price' ) ;
                $Amount         = ( $CouponAmnt > $Points ) ? $ConvertedPoint : $CouponValue ;
                update_post_meta( $CouponId , 'coupon_amount' , $Amount ) ;

                if ( get_post_meta( $CouponId , 'coupon_amount' , true ) == 0 )
                    return ;

                $IndividualUse = (get_option( 'rs_coupon_applied_individual' ) == 'yes') ? 'yes' : 'no' ;
                update_post_meta( $CouponId , 'individual_use' , $IndividualUse ) ;

                // Usage Count Meta Added in V23.4.6.
                update_post_meta( $CouponId , 'usage_count' , '0' ) ;

                update_post_meta( $CouponId , 'usage_limit' , '1' ) ;
                update_post_meta( $CouponId , 'expiry_date' , '' ) ;
                $ApplyTax     = (get_option( 'rs_apply_redeem_before_tax' ) == '1') ? 'yes' : 'no' ;
                update_post_meta( $CouponId , 'apply_before_tax' , $ApplyTax ) ;
                $FreeShipping = (get_option( 'rs_apply_shipping_tax' ) == '1') ? 'yes' : 'no' ;
                update_post_meta( $CouponId , 'free_shipping' , $FreeShipping ) ;

                if ( WC()->cart->has_discount( 'sumo_' . strtolower( $UserName ) ) )
                    return ;

                WC()->cart->add_discount( 'sumo_' . strtolower( $UserName ) ) ;

                // Form Submit not occurs properly issue . Added Safe Redirect URL in V24.4.1.
                if ( is_cart() ) {
                    wp_safe_redirect( wc_get_cart_url() ) ;
                    exit ;
                } else if ( is_checkout() ) {
                    wp_safe_redirect( wc_get_checkout_url() ) ;
                    exit ;
                }
            }
        }

        public static function get_sum_of_selected_products() {
            $IncProductId = get_option( 'rs_select_products_to_enable_redeeming' ) ;
            $IncProductId = srp_check_is_array( $IncProductId ) ? $IncProductId : (empty( $IncProductId ) ? array() : explode( ',' , $IncProductId ) ) ;

            $ExcProductId = get_option( 'rs_exclude_products_to_enable_redeeming' ) ;
            $ExcProductId = srp_check_is_array( $ExcProductId ) ? $ExcProductId : (empty( $ExcProductId ) ? array() : explode( ',' , $ExcProductId ) ) ;

            $IncCategory = get_option( 'rs_select_category_to_enable_redeeming' ) ;
            $IncCategory = srp_check_is_array( $IncCategory ) ? $IncCategory : (empty( $IncCategory ) ? array() : explode( ',' , $IncCategory ) ) ;

            $ExcCategory = get_option( 'rs_exclude_category_to_enable_redeeming' ) ;
            $ExcCategory = srp_check_is_array( $ExcCategory ) ? $ExcCategory : (empty( $ExcCategory ) ? array() : explode( ',' , $ExcCategory ) ) ;

            $Total = array() ;
            foreach ( WC()->cart->cart_contents as $item ) {
                $ProductId  = empty( $item[ 'variation_id' ] ) ? $item[ 'product_id' ] : $item[ 'variation_id' ] ;
                $ProductCat = get_the_terms( $item[ 'product_id' ] , 'product_cat' ) ;
                $LineTotal  = (get_option( 'woocommerce_prices_include_tax' ) === 'yes') ? ($item[ 'line_subtotal' ] + $item[ 'line_tax' ]) : $item[ 'line_subtotal' ] ;
                /* Checking whether the Product has Category */
                if ( srp_check_is_array( $ProductCat ) ) {
                    foreach ( $ProductCat as $CatObj ) {
                        if ( ! is_object( $CatObj ) )
                            continue ;

                        $termid = $CatObj->term_id ;

                        if ( get_option( 'rs_enable_redeem_for_selected_category' ) == 'yes' && srp_check_is_array( $IncCategory ) )
                            if ( in_array( $termid , $IncCategory ) )
                                $Total[] = $LineTotal ;

                        if ( get_option( 'rs_exclude_category_for_redeeming' ) == 'yes' && srp_check_is_array( $ExcCategory ) )
                            if ( in_array( $termid , $ExcCategory ) )
                                $Total[] = $LineTotal ;
                    }
                }

                if ( get_option( 'rs_enable_redeem_for_selected_products' ) == 'yes' && srp_check_is_array( $IncProductId ) )
                    if ( in_array( $ProductId , $IncProductId ) )
                        $Total[] = $LineTotal ;

                if ( get_option( 'rs_exclude_products_for_redeeming' ) == 'yes' && srp_check_is_array( $ExcProductId ) )
                    if ( ! in_array( $ProductId , $ExcProductId ) )
                        $Total[] = $LineTotal ;
            }
            $ValueToReturn = srp_check_is_array( $Total ) ? array_sum( $Total ) : WC()->cart->subtotal ;
            return $ValueToReturn ;
        }

        public static function messages_for_redeeming() {
            echo self::msg_when_tax_enabled() ;
            echo self::balance_point_msg_after_redeeming() ;
            echo self::button_type_redeem_field_in_cart_and_checkout() ;
        }

        /* Remaining Point message after Redeeming is applied in Cart/Checkout */

        public static function balance_point_msg_after_redeeming() {
            if ( ! is_user_logged_in() )
                return ;

            if ( ! srp_check_is_array( WC()->cart->get_applied_coupons() ) )
                return ;

            $UserId       = get_current_user_id() ;
            $banning_type = check_banning_type( $UserId ) ;
            if ( $banning_type == 'redeemingonly' || $banning_type == 'both' )
                return ;

            $UserInfo   = get_user_by( 'id' , $UserId ) ;
            $UserName   = $UserInfo->user_login ;
            $Redeem     = 'sumo_' . strtolower( "$UserName" ) ;
            $AutoRedeem = 'auto_redeem_' . strtolower( "$UserName" ) ;

            $DiscountAmnt        = isset( WC()->cart->coupon_discount_amounts[ "$AutoRedeem" ] ) ? WC()->cart->coupon_discount_amounts[ "$AutoRedeem" ] : (isset( WC()->cart->coupon_discount_amounts[ "$Redeem" ] ) ? WC()->cart->coupon_discount_amounts[ "$Redeem" ] : 0) ;
            $ShowBalancePointMsg = is_cart() ? get_option( 'rs_show_hide_message_for_redeem_points' ) : get_option( 'rs_show_hide_message_for_redeem_points_checkout_page' ) ;
            $BalancePointMsg     = is_cart() ? get_option( 'rs_message_user_points_redeemed_in_cart' ) : get_option( 'rs_message_user_points_redeemed_in_checkout' ) ;
            foreach ( WC()->cart->get_applied_coupons() as $Code ) {
                if ( get_option( 'rs_disable_point_if_coupon' ) == 'yes' ) {
                    if ( strtolower( $Code ) != $AutoRedeem && strtolower( $Code ) != $Redeem ) {
                        ?>
                        <div class="woocommerce-info sumo_reward_points_auto_redeem_message">
                            <?php echo get_option( 'rs_errmsg_for_coupon_in_order' ) ; ?>
                        </div>
                        <?php
                    }
                }
                if ( $ShowBalancePointMsg == '1' ) {
                    if ( ! empty( $DiscountAmnt ) ) {
                        if ( strtolower( $Code ) == $Redeem || strtolower( $Code ) == $AutoRedeem ) {
                            ?>
                            <div class="woocommerce-message sumo_reward_points_auto_redeem_message rs_cart_message">
                                <?php echo do_shortcode( $BalancePointMsg ) ; ?>
                            </div>
                            <?php
                            if ( get_option( 'rs_product_purchase_activated' ) == 'yes' && get_option( 'rs_enable_redeem_for_order' ) == 'yes' ) {
                                ?>
                                <div class="woocommerce-info sumo_reward_points_auto_redeem_error_message">
                                    <?php echo get_option( 'rs_errmsg_for_redeeming_in_order' ) ; ?>
                                </div>
                                <?php
                            }
                            echo self::cart_redeem_field( 'hide' ) ;
                            echo self::checkout_redeem_field( 'hide' ) ;
                        }
                    }
                }
            }
        }

        /* Button Redeem Field in Cart/Checkout */

        public static function button_type_redeem_field_in_cart_and_checkout() {
            if ( ! is_user_logged_in() )
                return ;

            $ShowRedeemField = is_cart() ? get_option( 'rs_redeem_field_type_option' ) : get_option( 'rs_redeem_field_type_option_checkout' ) ;
            if ( $ShowRedeemField == '1' )
                return ;

            if ( check_if_pointprice_product_exist_in_cart() )
                return ;

            $MemeberShipRestriction = (get_option( 'rs_restrict_redeem_when_no_membership_plan' ) == 'yes' && function_exists( 'check_plan_exists' )) ? (check_plan_exists( get_current_user_id() ) ? 'yes' : 'no') : 'no' ;
            if ( $MemeberShipRestriction == 'yes' )
                return ;

            $EnabledProductList = array() ;
            foreach ( WC()->cart->cart_contents as $item ) {
                $ProductId       = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                $PointPriceValue = calculate_point_price_for_products( $ProductId ) ;
                if ( empty( $PointPriceValue[ $ProductId ] ) )
                    continue ;

                $EnabledProductList[] = $PointPriceValue[ $ProductId ] ;
            }

            if ( ! empty( $EnabledProductList ) && get_option( 'rs_show_hide_message_errmsg_for_point_price_coupon' ) == '1' ) {
                ?><div class="woocommerce-info"><?php echo get_option( 'rs_errmsg_for_redeem_in_point_price_prt' ) ; ?></div><?php
            }
            $MinCartTotalToRedeem          = get_option( 'rs_minimum_cart_total_points' ) ;
            $MaxCartTotalToRedeem          = get_option( 'rs_maximum_cart_total_points' ) ;
            $ErrMsgForMaxCartTotalToRedeem = get_option( 'rs_max_cart_total_redeem_error' ) ;
            $ErrMsgForMinCartTotalToRedeem = get_option( 'rs_min_cart_total_redeem_error' ) ;
            $CartTotal                     = srp_cart_subtotal() ;
            if ( $MinCartTotalToRedeem != '' && $MaxCartTotalToRedeem != '' ) {
                if ( $CartTotal >= $MinCartTotalToRedeem && $CartTotal <= $MaxCartTotalToRedeem ) {
                    self::button_type_redeem_field() ;
                } else {
                    if ( get_option( 'rs_show_hide_maximum_cart_total_error_message' ) == '1' ) {
                        $CartTotalToReplace         = srp_formatted_price( round_off_type_for_currency( $MaxCartTotalToRedeem ) ) ;
                        $CartTotalShortcodeReplaced = str_replace( "[carttotal]" , $CartTotalToReplace , $ErrMsgForMaxCartTotalToRedeem ) ;
                        $FinalErrmsg                = str_replace( "[currencysymbol]" , "" , $CartTotalShortcodeReplaced ) ;
                        ?>
                        <div class="woocommerce-error"><?php echo $FinalErrmsg ; ?></div>
                        <?php
                    }
                }
            } else if ( $MinCartTotalToRedeem != '' && $MaxCartTotalToRedeem == '' ) {
                if ( $CartTotal >= $MinCartTotalToRedeem ) {
                    self::button_type_redeem_field() ;
                } else {
                    if ( get_option( 'rs_show_hide_minimum_cart_total_error_message' ) == '1' ) {
                        $CartTotalToReplace         = srp_formatted_price( round_off_type_for_currency( $MinCartTotalToRedeem ) ) ;
                        $CartTotalShortcodeReplaced = str_replace( "[carttotal]" , $CartTotalToReplace , $ErrMsgForMinCartTotalToRedeem ) ;
                        $FinalErrmsg                = str_replace( "[currencysymbol]" , "" , $CartTotalShortcodeReplaced ) ;
                        ?>
                        <div class="woocommerce-error"><?php echo $FinalErrmsg ; ?></div>
                        <?php
                    }
                }
            } else if ( $MinCartTotalToRedeem == '' && $MaxCartTotalToRedeem != '' ) {
                if ( $CartTotal <= $MaxCartTotalToRedeem ) {
                    self::button_type_redeem_field() ;
                } else {
                    if ( get_option( 'rs_show_hide_maximum_cart_total_error_message' ) == '1' ) {
                        $CartTotalToReplace         = srp_formatted_price( round_off_type_for_currency( $MaxCartTotalToRedeem ) ) ;
                        $CartTotalShortcodeReplaced = str_replace( "[carttotal]" , $CartTotalToReplace , $ErrMsgForMaxCartTotalToRedeem ) ;
                        $FinalErrmsg                = str_replace( "[currencysymbol]" , "" , $CartTotalShortcodeReplaced ) ;
                        ?>
                        <div class="woocommerce-error"><?php echo $FinalErrmsg ; ?></div>
                        <?php
                    }
                }
            } else if ( $MinCartTotalToRedeem == '' && $MaxCartTotalToRedeem == '' ) {
                self::button_type_redeem_field() ;
            }
        }

        public static function button_type_redeem_field() {
            $PercentageToRedeem = is_cart() ? get_option( 'rs_percentage_cart_total_redeem' ) : get_option( 'rs_percentage_cart_total_redeem_checkout' ) ;
            if ( empty( $PercentageToRedeem ) )
                return ;

            $UserId       = get_current_user_id() ;
            $banning_type = check_banning_type( $UserId ) ;
            if ( $banning_type == 'redeemingonly' || $banning_type == 'both' )
                return ;

            $CartWithTax = (get_option( 'woocommerce_prices_include_tax' ) === 'yes') ? WC()->cart->subtotal_ex_tax : WC()->cart->subtotal ;
            if ( $CartWithTax < get_option( 'rs_minimum_cart_total_points' ) )
                return ;

            $UserInfo       = get_user_by( 'id' , $UserId ) ;
            $UserName       = $UserInfo->user_login ;
            $AppliedCoupons = WC()->cart->get_applied_coupons() ;
            $AutoRedeem     = 'auto_redeem_' . strtolower( $UserName ) ;
            if ( in_array( $AutoRedeem , $AppliedCoupons ) )
                return ;

            if ( ! self::product_filter_for_redeem_field() )
                return ;

            $PointsData = new RS_Points_Data( $UserId ) ;
            $Points     = $PointsData->total_available_points() ;
            if ( empty( $Points ) )
                return ;

            $MinUserPoints = (get_user_meta( $UserId , 'rsfirsttime_redeemed' , true ) != '1') ? get_option( "rs_first_time_minimum_user_points" ) : get_option( "rs_minimum_user_points_to_redeem" ) ;
            if ( $Points < $MinUserPoints )
                return ;

            $ProductTotal    = array() ;
            $PointPriceValue = array() ;
            $PointPriceType  = array() ;
            foreach ( WC()->cart->cart_contents as $item ) {
                $ProductId               = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                $PointPriceType[]        = check_display_price_type( $ProductId ) ;
                $CheckIfPointPriceEnable = calculate_point_price_for_products( $ProductId ) ;
                if ( ! empty( $CheckIfPointPriceEnable[ $ProductId ] ) )
                    $PointPriceValue[]       = $CheckIfPointPriceEnable[ $ProductId ] ;

                if ( get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) == '2' ) {
                    if ( get_option( 'rs_enable_redeem_for_selected_products' ) == 'yes' && get_option( 'rs_select_products_to_enable_redeeming' ) != '' ) {
                        $IncProduct     = get_option( 'rs_select_products_to_enable_redeeming' ) ;
                        $IncProduct     = srp_check_is_array( $IncProduct ) ? $IncProduct : explode( ',' , $IncProduct ) ;
                        if ( in_array( $ProductId , $IncProduct ) )
                            $ProductTotal[] = isset( $item[ 'line_subtotal_tax' ] ) ? ((get_option( 'woocommerce_tax_display_cart' ) == 'incl') ? $item[ 'line_subtotal' ] + $item[ 'line_subtotal_tax' ] : $item[ 'line_subtotal' ]) : $item[ 'line_subtotal' ] ;
                    }
                    if ( get_option( 'rs_enable_redeem_for_selected_category' ) == 'yes' && get_option( 'rs_select_category_to_enable_redeeming' ) != '' ) {
                        $Category = get_the_terms( $ProductId , 'product_cat' ) ;
                        if ( srp_check_is_array( $Category ) ) {
                            $IncCategory = get_option( 'rs_select_category_to_enable_redeeming' ) ;
                            $IncCategory = srp_check_is_array( $IncCategory ) ? $IncCategory : explode( ',' , $IncCategory ) ;
                            foreach ( $Category as $CatObj ) {
                                $termid         = $CatObj->term_id ;
                                if ( in_array( $termid , $IncCategory ) )
                                    $ProductTotal[] = isset( $item[ 'line_subtotal_tax' ] ) ? ((get_option( 'woocommerce_tax_display_cart' ) == 'incl') ? $item[ 'line_subtotal' ] + $item[ 'line_subtotal_tax' ] : $item[ 'line_subtotal' ]) : $item[ 'line_subtotal' ] ;
                            }
                        }
                    }
                }
            }
            if ( srp_check_is_array( $PointPriceValue ) )
                return ;

            if ( in_array( 2 , $PointPriceType ) )
                return ;

            $Total            = get_option( 'rs_apply_redeem_basedon_cart_or_product_total' ) == '2' ? array_sum( $ProductTotal ) : WC()->cart->subtotal ;
            $RedeemPercentage = RSMemberFunction::redeem_points_percentage( $UserId ) ;
            $PointValue       = wc_format_decimal( get_option( 'rs_redeem_point' ) ) ;
            $ButtonCaption    = is_cart() ? get_option( 'rs_redeeming_button_option_message' ) : get_option( 'rs_redeeming_button_option_message_checkout' ) ;
            $CurrencyValue    = ($PercentageToRedeem / 100) * $Total ;
            $PointsToRedeem   = redeem_point_conversion( $CurrencyValue , $UserId ) ;
            $CurrencyValue    = ($Points >= $PointsToRedeem) ? srp_formatted_price( round_off_type_for_currency( $CurrencyValue ) ) : srp_formatted_price( round_off_type_for_currency( redeem_point_conversion( $Points , $UserId , 'price' ) ) ) ;
            $PointsToRedeem   = ($Points >= $PointsToRedeem) ? round_off_type( $PointsToRedeem ) : $Points ;
            $Message          = str_replace( "[pointsvalue]" , $CurrencyValue , $ButtonCaption ) ;
            $Message          = str_replace( "[currencysymbol]" , "" , $Message ) ;
            $ButtonMsg        = str_replace( "[cartredeempoints]" , $PointsToRedeem , $Message ) ;
            $DivClass         = is_cart() ? 'sumo_reward_points_cart_apply_discount' : 'sumo_reward_points_checkout_apply_discount' ;
            $FormClass        = is_cart() ? 'rs_button_redeem_cart' : 'rs_button_redeem_checkout' ;
            $ShowRedeemField  = is_checkout() ? get_option( 'rs_show_hide_redeem_field_checkout' ) : '1' ;
            if ( $ShowRedeemField != '1' )
                return ;

            $extra_message = apply_filters( 'rs_extra_messages_for_redeeming' , '' ) ;
            ?>
            <form method="post" class="<?php echo $FormClass ; ?> woocommerce-info">

                <?php if ( $extra_message ) : ?>
                    <div class="rs_add_extra_notice">
                        <?php echo do_shortcode( $extra_message ) ; ?>
                    </div>
                <?php endif ; ?>

                <div class="<?php echo $DivClass ; ?>"><?php echo $ButtonMsg ; ?>
                    <input id="rs_apply_coupon_code_field" class="input-text" type="hidden"  value="<?php echo $PointsToRedeem ; ?>" name="rs_apply_coupon_code_field">
                    <input class="<?php echo get_option( 'rs_extra_class_name_apply_reward_points' ) ; ?>" type="submit" id='mainsubmi' value="<?php echo get_option( 'rs_redeem_field_submit_button_caption' ) ; ?>" name="rs_apply_coupon_code1" />
                </div>
            </form>
            <?php
        }

        /* Button Redeem Field in Cart and Checkout */

        public static function change_coupon_label( $link , $coupon ) {
            if ( ! is_user_logged_in() )
                return $link ;

            $banning_type = check_banning_type( get_current_user_id() ) ;
            if ( $banning_type == 'earningonly' || $banning_type == 'both' )
                return $link ;

            $CouponObj  = srp_coupon_obj( $coupon ) ;
            $CouponCode = $CouponObj[ 'coupon_code' ] ;
            $UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
            $UserName   = $UserInfo->user_login ;
            if ( strtolower( $CouponCode ) == ('sumo_' . strtolower( $UserName )) || strtolower( $CouponCode ) == 'auto_redeem_' . strtolower( $UserName ) )
                $link       = ' ' . get_option( 'rs_coupon_label_message' ) ;

            return $link ;
        }

        /* Display message when tax is enabled in WooCommerce */

        public static function msg_when_tax_enabled() {

            if ( ! is_user_logged_in() )
                return ;

            if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' && get_option( 'rs_show_hide_message_notice_for_redeeming' ) == '1' ) {
                ?>
                <div class="woocommerce-error sumo_reward_points_notice">
                    <?php echo get_option( 'rs_msg_for_redeem_when_tax_enabled' ) ; ?>
                </div>
                <?php
            }
        }

        public static function hide_coupon_message( $message ) {
            $message = is_checkout() ? self::msg_for_coupon( $message , 'yes' ) : $message ;
            return $message ;
        }

        public static function hide_coupon_field_on_checkout( $message ) {
            if ( is_checkout() ) {
                if ( get_option( 'rs_show_hide_coupon_field_checkout' ) == '2' )
                    $message = false ;

                $message = self::msg_for_coupon( $message , 'no' ) ;
            }
            if ( get_option( 'rs_enable_disable_auto_redeem_checkout' ) == 'yes' )
                $message = true ;

            return $message ;
        }

        public static function msg_for_coupon( $message , $hidemsg ) {
            if ( isset( $_POST[ 'rs_apply_coupon_code' ] ) || isset( $_POST[ 'rs_apply_coupon_code1' ] ) || isset( $_POST[ 'rs_apply_coupon_code2' ] ) ) {
                if ( empty( $_POST[ 'rs_apply_coupon_code_field' ] ) )
                    return $message ;

                if ( $hidemsg == 'no' && get_option( 'woocommerce_enable_coupons' ) == 'yes' )
                    return true ;

                if ( $hidemsg == 'yes' && get_option( 'rs_show_hide_coupon_field_checkout' ) == '2' )
                    return '' ;
            }
            return $message ;
        }

        /* Error message for SUMO Coupon */

        public static function error_message_for_sumo_coupon( $msg , $msg_code , $object ) {
            if ( ! is_user_logged_in() )
                return $msg ;

            $CouponObj  = new WC_Coupon( $object ) ;
            $CouponObj  = srp_coupon_obj( $CouponObj ) ;
            $CouponCode = $CouponObj[ 'coupon_code' ] ;
            $UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
            $UserName   = $UserInfo->user_login ;
            $Redeem     = 'sumo_' . strtolower( $UserName ) ;
            $AutoRedeem = 'auto_redeem_' . strtolower( $UserName ) ;
            if ( $CouponCode == $AutoRedeem )
                if ( get_option( 'rs_show_hide_auto_redeem_not_applicable' ) == 2 )
                    return $msg ;

            if ( $CouponCode == $Redeem )
                $msg_code = ($msg_code == 104) ? 204 : $msg_code ;

            switch ( $msg_code ) {
                case 204 :
                    $msg = get_option( 'rs_coupon_applied_individual_error_msg' ) ;
                    break ;
                case 109 :
                case 113 :
                case 101 :
                    $msg = ( $CouponCode == $AutoRedeem ) ? get_option( 'rs_auto_redeem_not_applicable_error_message' ) : $msg ;
                    break ;
                default:
                    $msg = $msg ;
                    break ;
            }
            return $msg ;
        }

        /* Success message for SUMO Coupon */

        public static function success_message_for_sumo_coupon( $msg , $msg_code , $Obj ) {
            if ( ! is_user_logged_in() )
                return $msg ;

            $CouponObj  = new WC_Coupon( $Obj ) ;
            $CouponObj  = srp_coupon_obj( $CouponObj ) ;
            $CouponCode = $CouponObj[ 'coupon_code' ] ;
            update_option( 'appliedcouponcode' , $CouponCode ) ; //Update to Replace Message which is displayed while coupon removed.
            $UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
            $UserName   = $UserInfo->user_login ;
            $AutoRedeem = 'auto_redeem_' . strtolower( $UserName ) ;
            if ( $AutoRedeem == $CouponCode )
                $msg_code   = ($msg_code == 200) ? 501 : $msg_code ;

            switch ( $msg_code ) {
                case 501:
                    $msg = get_option( 'rs_show_hide_message_for_redeem' ) == '1' ? get_option( 'rs_automatic_success_coupon_message' , 'AutoReward Points Successfully Added' ) : '' ;
                    break ;
                case 200 :
                    if ( isset( $_POST[ 'rs_apply_coupon_code' ] ) || isset( $_POST[ 'rs_apply_coupon_code1' ] ) )
                        $msg = get_option( 'rs_show_hide_message_for_redeem' ) == '1' ? __( get_option( 'rs_success_coupon_message' ) , SRP_LOCALE ) : '' ;

                    break ;
                default:
                    $msg = '' ;
                    break ;
            }
            return $msg ;
        }

        /* Replace Remove Message for SUMO Coupon  */

        public static function replace_msg_for_remove_coupon( $message ) {
            if ( ! is_user_logged_in() )
                return $message ;

            $woo_msg = __( 'Coupon has been removed.' , 'woocommerce' ) ;
            if ( $message != $woo_msg )
                return $message ;

            if ( empty( get_option( 'rs_remove_redeem_points_message' ) ) )
                return $message ;

            $CouponCode = get_option( 'appliedcouponcode' ) ;
            $UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
            $UserName   = $UserInfo->user_login ;
            $Redeem     = 'sumo_' . strtolower( "$UserName" ) ;
            $AutoRedeem = 'auto_redeem_' . strtolower( "$UserName" ) ;
            if ( $Redeem == $CouponCode || $AutoRedeem == $CouponCode )
                $message    = __( get_option( 'rs_remove_redeem_points_message' ) , SRP_LOCALE ) ;

            return $message ;
        }

        public static function unset_gateways_for_excluded_product_to_redeem( $gateways ) {
            if ( get_option( 'rs_exclude_products_for_redeeming' ) != 'yes' )
                return $gateways ;

            global $woocommerce ;
            if ( ! srp_check_is_array( $woocommerce->cart->cart_contents ) )
                return $gateways ;

            if ( empty( get_option( 'rs_exclude_products_to_enable_redeeming' ) ) )
                return $gateways ;

            foreach ( $woocommerce->cart->cart_contents as $key => $values ) {
                $ExcProducts = srp_check_is_array( get_option( 'rs_exclude_products_to_enable_redeeming' ) ) ? get_option( 'rs_exclude_products_to_enable_redeeming' ) : explode( ',' , get_option( 'rs_exclude_products_to_enable_redeeming' ) ) ;
                if ( in_array( $values[ 'product_id' ] , $ExcProducts ) ) {
                    foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
                        if ( $gateway->id != 'reward_gateway' )
                            continue ;

                        unset( $gateways[ $gateway->id ] ) ;
                    }
                }
            }

            return $gateways != 'NULL' ? $gateways : array() ;
        }

    }

    RSRedeemingFrontend::init() ;
}