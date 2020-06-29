<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}

if ( ! function_exists( 'srp_check_is_array' ) ) {

    function srp_check_is_array( $array ) {
        return (is_array( $array ) && ! empty( $array )) ? true : false ;
    }

}

if ( ! function_exists( 'woocommerce_coupon_field' ) ) {

    function woocommerce_coupon_field( $Param ) {
        ?>
        <style type="text/css">
            .coupon, .woocommerce-form-coupon{
                <?php if ( $Param == 'show' ) { ?>
                    display: block !important;
                <?php } else { ?>
                    display: none !important;
                <?php } ?>
            }
        </style>
        <?php
    }

}

if ( ! function_exists( 'check_banning_type' ) ) {

    function check_banning_type( $UserId ) {
        if ( ! ban_user_from_earning( $UserId ) && ! ban_user_from_redeeming( $UserId ) )
            return "no_banning" ;

        if ( ! ban_user_from_earning( $UserId ) && ban_user_from_redeeming( $UserId ) )
            return 'redeemingonly' ;

        if ( ban_user_from_earning( $UserId ) && ! ban_user_from_redeeming( $UserId ) )
            return 'earningonly' ;

        if ( ban_user_from_earning( $UserId ) && ban_user_from_redeeming( $UserId ) )
            return 'both' ;
    }

}

if ( ! function_exists( 'ban_user_from_earning' ) ) {

    function ban_user_from_earning( $UserId ) {
        if ( get_option( 'rs_enable_banning_users_earning_points' ) == 'no' )
            return false ;

        $BannedUserListForEarning = get_option( 'rs_banned_users_list_for_earning' ) ;
        $BannedUserRoleForEarning = get_option( 'rs_banning_user_role_for_earning' ) ;
        if ( ! srp_check_is_array( $BannedUserListForEarning ) )
            $BannedUserListForEarning = ($BannedUserListForEarning != '') ? explode( ',' , $BannedUserListForEarning ) : array() ;

        if ( empty( $BannedUserListForEarning ) && empty( $BannedUserRoleForEarning ) )
            return false ;

        if ( in_array( $UserId , $BannedUserListForEarning ) ) {
            return true ;
        } else {
            $UserData   = get_userdata( $UserId ) ;
            $RoleofUser = is_object( $UserData ) ? $UserData->roles[ 0 ] : 'guest' ;
            if ( in_array( $RoleofUser , ( array ) $BannedUserRoleForEarning ) )
                return true ;
        }

        return false ;
    }

}

if ( ! function_exists( 'ban_user_from_redeeming' ) ) {

    function ban_user_from_redeeming( $UserId ) {
        if ( get_option( 'rs_enable_banning_users_redeeming_points' ) == 'no' )
            return false ;

        $BannedUserListForRedeeming = get_option( 'rs_banned_users_list_for_redeeming' ) ;
        $BannedUserRoleForRedeeming = get_option( 'rs_banning_user_role_for_redeeming' ) ;
        if ( ! srp_check_is_array( $BannedUserListForRedeeming ) )
            $BannedUserListForRedeeming = ($BannedUserListForRedeeming != '') ? explode( ',' , $BannedUserListForRedeeming ) : array() ;

        if ( empty( $BannedUserListForRedeeming ) && empty( $BannedUserRoleForRedeeming ) )
            return false ;

        if ( in_array( $UserId , $BannedUserListForRedeeming ) ) {
            return true ;
        } else {
            $UserData   = get_userdata( $UserId ) ;
            $RoleofUser = is_object( $UserData ) ? $UserData->roles[ 0 ] : 'guest' ;
            if ( in_array( $RoleofUser , ( array ) $BannedUserRoleForRedeeming ) )
                return true ;
        }

        return false ;
    }

}

if ( ! function_exists( 'redirect_url_for_guest' ) ) {

    function redirect_url_for_guest( $redirect ) {
        if ( isset( $_REQUEST[ 'redirect_to' ] ) )
            $redirect = $_REQUEST[ 'redirect_to' ] ;

        return $redirect ;
    }

    add_filter( 'woocommerce_login_redirect' , 'redirect_url_for_guest' ) ;

    add_filter( 'woocommerce_registration_redirect' , 'redirect_url_for_guest' ) ;
}

if ( ! function_exists( 'check_if_coupon_exist_in_cart' ) ) {

    function check_if_coupon_exist_in_cart( $Code , $AppliedCoupons = array() ) {

        if ( ! srp_check_is_array( $AppliedCoupons ) )
            return false ;

        if ( in_array( $Code , $AppliedCoupons ) )
            return true ;

        return false ;
    }

}

if ( ! function_exists( 'multi_dimensional_sort' ) ) {

    function multi_dimensional_sort( $Rules , $Index , $SortType = 'asc' ) {

        $ArrToSort   = array() ;
        $ArrToReturn = array() ;
        if ( ! srp_check_is_array( $Rules ) )
            return array() ;

        foreach ( $Rules as $Key => $Rule ) {
            $ArrToSort[ $Key ] = $Rule[ $Index ] ;
        }

        $SortedArr = ($SortType == 'asc') ? asort( $ArrToSort ) : arsort( $ArrToSort ) ;

        if ( ! srp_check_is_array( $ArrToSort ) )
            return array() ;

        foreach ( $ArrToSort as $NewKey => $value ) {
            $ArrToReturn[ $NewKey ] = $Rules[ $NewKey ] ;
        }
        return $ArrToReturn ;
    }

}

if ( ! function_exists( 'srp_cart_subtotal' ) ) {

    function srp_cart_subtotal( $exc_discount = false , $OrderId = 0 ) {
        $subtotal = 0 ;
        $discount = 0 ;
        if ( ! empty( $OrderId ) ) {
            $Order = new WC_Order( $OrderId ) ;
            if ( is_object( $Order ) ) {
                $subtotal = (get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) ? $Order->get_subtotal() + $Order->get_total_tax() : ($Order->get_subtotal() - $Order->get_total_tax()) ;
                $discount = $Order->get_total_discount() ;
            }
        } else {
            global $woocommerce ;
            $Obj = function_exists( 'WC' ) ? WC() : $woocommerce ;
            if ( ( float ) $Obj->version >= ( float ) '3.2.0' ) {
                $discount = (get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) ? $Obj->cart->get_discount_tax() + $Obj->cart->get_discount_total() : $Obj->cart->get_discount_total() ;
            } else {
                $discount = $Obj->cart->discount_cart + $Obj->cart->discount_cart_tax ;
            }
            $subtotal = ( float ) (get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) ? $Obj->cart->subtotal : $Obj->cart->subtotal_ex_tax ;
        }
        return ($exc_discount) ? ( float ) ($subtotal - $discount) : ( float ) $subtotal ;
    }

}

if ( ! function_exists( 'check_if_pointprice_product_exist_in_cart' ) ) {

    function check_if_pointprice_product_exist_in_cart() {
        global $woocommerce ;
        $Obj = function_exists( 'WC' ) ? WC() : $woocommerce ;
        if ( get_option( 'rs_point_price_activated' ) == 'no' )
            return false ;

        if ( empty( $Obj->cart->cart_contents ) )
            return false ;

        foreach ( $Obj->cart->cart_contents as $values ) {
            $ProductId = ! empty( $values[ 'variation_id' ] ) ? $values[ 'variation_id' ] : $values[ 'product_id' ] ;
            if ( ! empty( check_display_price_type( $ProductId ) ) )
                return true ;
        }
    }

}

if ( ! function_exists( 'check_if_coupon_applied' ) ) {

    function check_if_coupon_applied() {
        global $woocommerce ;
        $Obj = function_exists( 'WC' ) ? WC() : $woocommerce ;
        if ( ! is_user_logged_in() )
            return false ;

        if ( ! srp_check_is_array( $Obj->cart->get_applied_coupons() ) )
            return false ;

        foreach ( $Obj->cart->get_applied_coupons() as $Code ) {
            $CouponObj         = new WC_Coupon( $Code ) ;
            $CouponObj         = srp_coupon_obj( $CouponObj ) ;
            $CouponId          = $CouponObj[ 'coupon_id' ] ;
            $CheckIfSUMOCoupon = get_post_meta( $CouponId , 'sumo_coupon_check' , true ) ;
            if ( get_option( '_rs_not_allow_earn_points_if_sumo_coupon' ) == 'yes' && $CheckIfSUMOCoupon == 'yes' )
                return true ;

            $UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
            $UserName   = $UserInfo->user_login ;
            $Redeem     = 'sumo_' . strtolower( "$UserName" ) ;
            $AutoRedeem = 'auto_redeem_' . strtolower( $UserName ) ;
            if ( get_option( 'rs_enable_redeem_for_order' ) == 'yes' )
                if ( strtolower( $Code ) == $Redeem || strtolower( $Code ) == $AutoRedeem )
                    return true ;

            if ( get_option( 'rs_disable_point_if_coupon' ) == 'yes' )
                if ( strtolower( $Code ) != $Redeem && strtolower( $Code ) != $AutoRedeem )
                    return true ;
        }
        return false ;
    }

}

if ( ! function_exists( 'check_if_discount_applied' ) ) {

    function check_if_discount_applied() {
        if ( get_option( 'rs_discounts_compatability_activated' ) != 'yes' )
            return false ;

        if ( ! class_exists( 'SUMODiscounts' ) )
            return false ;

        if ( get_option( '_rs_not_allow_earn_points_if_sumo_discount' ) != 'yes' )
            return false ;

        return function_exists( 'check_sumo_discounts_are_applied_in_cart' ) ? check_sumo_discounts_are_applied_in_cart() : false ;
    }

}

if ( ! function_exists( 'enable_reward_program_in_checkout' ) ) {

    function enable_reward_program_in_checkout( $OrderId , $data ) {
        if ( is_user_logged_in() )
            return ;

        if ( isset( $data[ 'enable_reward_prgm' ] ) && ! empty( $data[ 'enable_reward_prgm' ] ) )
            update_user_meta( get_current_user_id() , 'allow_user_to_earn_reward_points' , 'yes' ) ;
    }

    add_action( 'woocommerce_checkout_update_order_meta' , 'enable_reward_program_in_checkout' , 10 , 2 ) ;
}

if ( ! function_exists( 'check_if_referral_is_restricted' ) ) {

    function check_if_referral_is_restricted() {
        $UserSelectionType = get_option( 'rs_select_type_of_user_for_referral' ) ;
        if ( is_user_logged_in() ) {
            $UserId      = get_current_user_id() ;
            $UserRoleObj = wp_get_current_user() ;
            $UserRole    = $UserRoleObj->roles ;
        } elseif ( isset( $_GET[ 'ref' ] ) ) {
            $UserObj  = get_user_by( 'login' , $_GET[ 'ref' ] ) ;
            $UserId   = is_object( $UserObj ) ? $UserObj->ID : $_GET[ 'ref' ] ;
            $UserRole = is_object( $UserObj ) ? $UserObj->roles : get_user_by( 'id' , $_GET[ 'ref' ] )->roles ;
        } else {
            $UserId   = '' ;
            $UserRole = array() ;
        }
        if ( $UserSelectionType == '1' ) {
            return true ;
        } elseif ( $UserSelectionType == '2' ) {
            if ( get_option( 'rs_select_include_users_for_show_referral_link' ) != "" ) {
                $UserIds = srp_check_is_array( get_option( 'rs_select_include_users_for_show_referral_link' ) ) ? get_option( 'rs_select_include_users_for_show_referral_link' ) : explode( ',' , get_option( 'rs_select_include_users_for_show_referral_link' ) ) ;
                if ( in_array( $UserId , $UserIds ) )
                    return true ;
            }
        } elseif ( $UserSelectionType == '3' ) {
            $getuser = get_option( 'rs_select_exclude_users_list_for_show_referral_link' ) ;
            if ( get_option( 'rs_select_exclude_users_list_for_show_referral_link' ) != "" ) {
                $UserIds = srp_check_is_array( get_option( 'rs_select_exclude_users_list_for_show_referral_link' ) ) ? get_option( 'rs_select_exclude_users_list_for_show_referral_link' ) : explode( ',' , get_option( 'rs_select_exclude_users_list_for_show_referral_link' ) ) ;
                if ( ! in_array( $UserId , $UserIds ) )
                    return true ;
            }
        } elseif ( $UserSelectionType == '4' ) {
            if ( srp_check_is_array( get_option( 'rs_select_users_role_for_show_referral_link' ) ) ) {
                $inc_role = array_intersect( $UserRole , get_option( 'rs_select_users_role_for_show_referral_link' ) ) ;
                if ( srp_check_is_array( $inc_role ) )
                    return true ;
            }
        } else {
            if ( srp_check_is_array( get_option( 'rs_select_exclude_users_role_for_show_referral_link' ) ) ) {
                $exc_role = array_intersect( $UserRole , get_option( 'rs_select_exclude_users_role_for_show_referral_link' ) ) ;
                if ( ! srp_check_is_array( $exc_role ) )
                    return true ;
            }
        }
        return false ;
    }

}

if ( ! function_exists( 'custom_message_in_thankyou_page' ) ) {

    function custom_message_in_thankyou_page( $Points , $CurrencyValue , $ShowCurrencyValue , $ShowCustomMsg , $CustomMsg , $PaymentPlanPoints ) {
        $Msg = '' ;

        $PointsToDisplay = ( float ) $Points - ( float ) $PaymentPlanPoints ;
        $PointsToDisplay = round_off_type( $PointsToDisplay ) ;

        if ( get_option( "$ShowCustomMsg" ) == '1' )
            $Msg .= ' ' . get_option( "$CustomMsg" ) ;

        if ( get_option( "$ShowCurrencyValue" ) == '1' )
            $Msg .= '&nbsp;(' . $CurrencyValue . ')' ;

        echo $PointsToDisplay . $Msg ;
    }

}

if ( ! function_exists( 'fp_user_roles' ) ) {

    function fp_user_roles() {
        global $wp_roles ;
        foreach ( $wp_roles->roles as $values => $key ) {
            $userroleslug[] = $values ;
            $userrolename[] = $key[ 'name' ] ;
        }
        return array_combine( ( array ) $userroleslug , ( array ) $userrolename ) ;
    }

}

if ( ! function_exists( 'points_for_simple_product' ) ) {

    function points_for_simple_product() {
        global $post ;
        if ( ! is_object( $post ) )
            return ;

        if ( block_points_for_salepriced_product( $post->ID , 0 ) == 'yes' )
            return ;

        $ProductObj = srp_product_object( $post->ID ) ;
        if ( ! is_object( $ProductObj ) )
            return ;

        if ( is_shop() || is_product() || is_page() || is_product_category() || is_tax( 'pwb-brand' ) ) {
            if ( (srp_product_type( $post->ID ) == 'simple' || (srp_product_type( $post->ID ) == 'subscription') || srp_product_type( $post->ID ) == 'bundle' ) ) {
                $args   = array(
                    'productid' => $post->ID ,
                    'item'      => array( 'qty' => '1' ) ,
                        ) ;
                $Points = check_level_of_enable_reward_point( $args ) ;
                $Points = get_current_user_id() > 0 ? RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $Points ) : ( float ) $Points ;
                return $Points ;
            }
        }
        return 0 ;
    }

}

if ( ! function_exists( 'referral_points_for_simple_product' ) ) {

    function referral_points_for_simple_product() {
        if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
            $refuser = (get_option( 'rs_generate_referral_link_based_on_user' ) == 1) ? get_user_by( 'login' , $_COOKIE[ 'rsreferredusername' ] ) : get_user_by( 'id' , $_COOKIE[ 'rsreferredusername' ] ) ;
            if ( ! $refuser ) {
                return 0 ;
            }
            $UserId = $refuser->ID ;
        } else {
            $UserId = check_if_referrer_has_manual_link( get_current_user_id() ) ;
        }

        if ( ! $UserId )
            return 0 ;

        global $post ;
        if ( ! is_object( $post ) )
            return 0 ;

        if ( block_points_for_salepriced_product( $post->ID , 0 ) == 'yes' )
            return 0 ;

        $ProductObj = srp_product_object( $post->ID ) ;
        if ( ! is_object( $ProductObj ) )
            return 0 ;

        if ( is_shop() || is_product() || is_page() || is_product_category() || is_tax( 'pwb-brand' ) ) {
            if ( (srp_product_type( $post->ID ) == 'simple' || (srp_product_type( $post->ID ) == 'subscription') ) ) {
                $args   = array(
                    'productid'     => $post->ID ,
                    'item'          => array( 'qty' => '1' ) ,
                    'referred_user' => $UserId
                        ) ;
                $Points = check_level_of_enable_reward_point( $args ) ;
                $Points = ( $UserId > 0 ) ? RSMemberFunction::earn_points_percentage( $UserId , ( float ) $Points ) : ( float ) $Points ;
                return $Points ;
            }
        }
        return 0 ;
    }

}

if ( ! function_exists( 'buying_points_for_simple_product' ) ) {

    function buying_points_for_simple_product() {
        global $post ;
        if ( ! is_object( $post ) )
            return ;

        if ( block_points_for_salepriced_product( $post->ID , 0 ) == 'yes' )
            return ;

        $ProductObj = srp_product_object( $post->ID ) ;
        if ( ! is_object( $ProductObj ) )
            return ;

        if ( is_shop() || is_product() || is_page() || is_product_category() || is_tax( 'pwb-brand' ) ) {
            if ( (srp_product_type( $post->ID ) == 'simple' || (srp_product_type( $post->ID ) == 'subscription') || srp_product_type( $post->ID ) == 'bundle' ) ) {
                $item   = array( 'qty' => '1' ) ;
                $Points = get_post_meta( $post->ID , '_rewardsystem_assign_buying_points' , true ) ;
                $Points = get_current_user_id() > 0 ? RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $Points ) : ( float ) $Points ;
                return $Points ;
            }
        }
        return 0 ;
    }

}

/* Common Function For Sending Mail after reaching the minimum Threshold Points */

if ( ! function_exists( 'send_mail_for_thershold_points' ) ) {

    function send_mail_for_thershold_points() {
        if ( get_option( 'rs_email_activated' ) != 'yes' )
            return ;

        if ( get_option( 'rs_mail_enable_threshold_points' ) != 'yes' )
            return ;

        $UserId     = get_current_user_id() ;
        $PointsData = new RS_Points_Data( $UserId ) ;
        $Points     = $PointsData->total_available_points() ;

        if ( get_option( 'rs_mail_threshold_points' ) < $Points )
            update_user_meta( $UserId , 'rs_mail_minimum_threshold_points' , 'yes' ) ;

        if ( $Points > get_option( 'rs_mail_threshold_points' ) && get_user_meta( $UserId , 'rs_mail_minimum_threshold_points' , true ) == 'no' )
            return ;

        $UserInfo = get_user_by( 'id' , 1 ) ;
        $UserName = get_user_by( 'id' , $UserId )->display_name ;
        $subject  = get_option( 'rs_email_subject_threshold_points' ) ;
        $msg      = get_option( 'rs_email_message_threshold_points' ) ;
        if ( empty( $subject ) || empty( $msg ) )
            return ;

        $message      = str_replace( '[Username]' , $UserName , str_replace( '[TotalPoint]' , $Points , get_option( 'rs_email_message_threshold_points' ) ) ) ;
        ob_start() ;
        wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $subject ) ) ;
        echo $message ;
        wc_get_template( 'emails/email-footer.php' ) ;
        $woo_temp_msg = ob_get_clean() ;
        $headers      = "MIME-Version: 1.0\r\n" ;
        $headers      .= "Content-Type: text/html; charset=UTF-8\r\n" ;
        if ( '2' == get_option( 'rs_select_mail_function' ) ) {
            $mailer = WC()->mailer() ;
            if ( $mailer->send( $UserInfo->user_email , $subject , $woo_temp_msg , $headers ) )
                update_user_meta( $UserId , 'rs_mail_minimum_threshold_points' , 'no' ) ;
        } elseif ( '1' == get_option( 'rs_select_mail_function' ) ) {
            if ( mail( $UserInfo->user_email , $subject , $woo_temp_msg , $headers ) )
                update_user_meta( $UserId , 'rs_mail_minimum_threshold_points' , 'no' ) ;
        }
    }

}

if ( ! function_exists( 'get_referrer_id_from_payment_plan' ) ) {

    function get_referrer_id_from_payment_plan( $OrderId ) {
        if ( ! class_exists( 'SUMOPaymentPlans' ) )
            return 0 ;

        $ParentId = wp_get_post_parent_id( $OrderId ) ;
        if ( empty( $ParentId ) )
            return 0 ;

        $ReferId = get_post_meta( $ParentId , '_referrer_name' , true ) ;
        update_post_meta( $OrderId , '_referrer_name' , $ReferId ) ;
        return $ReferId ;
    }

}

if ( ! function_exists( 'is_payment_product' ) ) {

    function is_payment_product( $order_id , $product_id ) {
        if ( ! function_exists( '_sumo_pp_is_balance_payment_order' ) )
            return false ;

        if ( _sumo_pp_is_balance_payment_order( $order_id ) && 'yes' === get_post_meta( $order_id , 'is_sumo_pp_order' , 'yes' ) )
            return get_post_meta( $order_id , '_payment_id' , true ) == get_post_meta( $payment_id , '_product_id' , true ) ;

        return false ;
    }

}

function get_payment_product_price( $order_id , $check_in_initial_order = false ) {
    if ( ! class_exists( 'SUMOPaymentPlans' ) )
        return 0 ;

    if ( $check_in_initial_order && function_exists( '_sumo_pp_is_initial_payment_order' ) && function_exists( '_sumo_pp_get_posts' ) ) {

        if ( ! _sumo_pp_is_initial_payment_order( $order_id ) )
            return 0 ;

        $order          = wc_get_order( $order_id ) ;
        $initial_amount = 0 ;

        foreach ( $order->get_items() as $item ) {
            $itemid = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;

            $payments = _sumo_pp_get_posts( array(
                'post_type'   => 'sumo_pp_payments' ,
                'post_status' => array_keys( _sumo_pp_get_payment_statuses() ) ,
                'meta_query'  => array(
                    'relation' => 'AND' ,
                    array(
                        'key'   => '_initial_payment_order_id' ,
                        'value' => $order_id ,
                    ) ,
                    array(
                        'key'   => '_product_id' ,
                        'value' => $itemid ,
                    ) ,
                ) ,
                    ) ) ;

            if ( srp_check_is_array( $payments ) ) {
                foreach ( $payments as $payment_id ) {
                    if ( 'payment-plans' === get_post_meta( $payment_id , '_payment_type' , true ) ) {
                        $product_amount = floatval( get_post_meta( $payment_id , '_product_price' , true ) ) * absint( get_post_meta( $payment_id , '_product_qty' , true ) ) ;
                        $initial_amount += (floatval( get_post_meta( $payment_id , '_initial_payment' , true ) ) * $product_amount) / 100 ;
                    }
                    if ( 'pay-in-deposit' === get_post_meta( $payment_id , '_payment_type' , true ) ) {
                        $initial_amount += floatval( get_post_meta( $payment_id , '_deposited_amount' , true ) ) * absint( get_post_meta( $payment_id , '_product_qty' , true ) ) ;
                    }
                }
            }
        }
        return $initial_amount ;
    } elseif ( function_exists( '_sumo_pp_is_balance_payment_order' ) ) {
        if ( _sumo_pp_is_balance_payment_order( $order_id ) && 'yes' === get_post_meta( $order_id , 'is_sumo_pp_order' , 'yes' ) ) {
            $payment_id = absint( get_post_meta( $order_id , '_payment_id' , true ) ) ;
            return floatval( get_post_meta( $payment_id , '_product_price' , true ) ) ;
        }
    }
}

if ( ! function_exists( 'is_final_payment' ) ) {

    function is_final_payment( $order_id ) {

        if ( class_exists( 'SUMOPaymentPlans' ) && function_exists( '_sumo_pp_is_balance_payment_order' ) ) {
            if ( _sumo_pp_is_balance_payment_order( $order_id ) && 'yes' === get_post_meta( $order_id , 'is_sumo_pp_order' , 'yes' ) ) {
                $payment_id             = absint( get_post_meta( $order_id , '_payment_id' , true ) ) ;
                $remaining_installments = absint( get_post_meta( $payment_id , '_remaining_installments' , true ) ) ;

                $order_status = '' ;
                if ( $order        = wc_get_order( $order_id ) )
                    $order_status = defined( 'WC_VERSION' ) && version_compare( WC_VERSION , '3.0' , '<' ) ? $order->status : $order->get_status() ;

                return $payment_id > 0 && ((1 === $remaining_installments) || (0 === $remaining_installments && in_array( $order_status , array( 'processing' , 'completed' ) ))) ;
            }
        }
        return false ;
    }

}

if ( ! function_exists( 'is_initial_payment' ) ) {

    function is_initial_payment( $product_id , $order_user_id = 0 ) {
        if ( class_exists( 'SUMOPaymentPlans' ) && function_exists( '_sumo_pp_is_payment_product' ) )
            return _sumo_pp_is_payment_product( $product_id , $order_user_id ) ;

        return false ;
    }

}

if ( ! function_exists( 'get_payment_data_for_payment_plan' ) ) {

    function get_payment_data_for_payment_plan( $product_id ) {
        if ( class_exists( 'SUMOPaymentPlans' ) && function_exists( '_sumo_pp_get_payment_data' ) ) {
            $payment_data = _sumo_pp_get_payment_data( $product_id ) ;
            return $payment_data[ 'product_price' ] ;
        }
    }

}

/* Common Function For Sending Email For Actions */

if ( ! function_exists( 'rs_send_mail_for_actions' ) ) {

    function rs_send_mail_for_actions( $to , $event_slug , $earned_point , $user_name = '' , $order_id = '' ) {
        $user_info          = get_user_by( 'email' , $to ) ;
        $first_name         = is_object( $user_info ) ? $user_info->first_name : '' ;
        $last_name          = is_object( $user_info ) ? $user_info->last_name : '' ;
        $subject            = '' ;
        $message            = '' ;
        $PointsData         = new RS_Points_Data( $user_info->ID ) ;
        $PointsData->reset( $user_info->ID ) ;
        $total_earned_point = $PointsData->total_available_points() ;
        $earned_point       = round_off_type( $earned_point ) ;
        /* Send SMS for Actions - Start */
        if ( get_option( 'rs_sms_activated' ) == 'yes' && get_option( 'rs_enable_send_sms_to_user' ) == 'yes' ) {
            if ( get_option( 'rs_send_sms_earning_points_for_actions' ) == 'yes' ) {
                if ( $event_slug == 'RRP' ) {
                    $MsgFor = "signup" ;
                } elseif ( $event_slug == 'RPPR' ) {
                    $MsgFor = "review" ;
                } elseif ( $event_slug == 'RRRP' ) {
                    $MsgFor = "referralregistration" ;
                } elseif ( $event_slug == 'PPRRP' ) {
                    $MsgFor = "referralpurchase" ;
                }
                $PhoneNumber = ! empty( get_user_meta( $user_info->ID , 'rs_phone_number_value_from_signup' , true ) ) ? get_user_meta( $user_info->ID , 'rs_phone_number_value_from_signup' , true ) : get_user_meta( $user_info->ID , 'rs_phone_number_value_from_account_details' , true ) ;
                $PhoneNumber = ! empty( $PhoneNumber ) ? $PhoneNumber : get_user_meta( $user_info->ID , 'billing_phone' , true ) ;
                if ( get_option( 'rs_sms_sending_api_option' ) == '1' ) {
                    RSFunctionForSms::send_sms_twilio_api( '' , $MsgFor , $earned_point , $PhoneNumber ) ;
                } elseif ( get_option( 'rs_sms_sending_api_option' ) == '2' ) {
                    RSFunctionForSms::send_sms_nexmo_api( '' , $MsgFor , $earned_point , $PhoneNumber ) ;
                }
            }
        }
        /* Send SMS for Actions - End */

        // Product Review
        if ( get_option( 'rs_send_mail_product_review' ) == 'yes' ) {
            if ( $event_slug == 'RPPR' ) {
                $subject = get_option( 'rs_email_subject_product_review' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_product_review' ) ) ) ;
            }
        }
        // Account Signup
        if ( get_option( 'rs_send_mail_account_signup' ) == 'yes' ) {
            if ( $event_slug == 'RRP' || $event_slug == 'SLRRP' ) {
                $subject = get_option( 'rs_email_subject_account_signup' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_account_signup' ) ) ) ;
            }
        }
        // Blog Post Create
        if ( get_option( 'rs_send_mail_blog_post_create' ) == 'yes' ) {
            if ( $event_slug == 'RPFP' ) {
                $subject = get_option( 'rs_email_subject_blog_post_create' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_blog_post_create' ) ) ) ;
            }
        }
        // Blog Post Comment
        if ( get_option( 'rs_send_mail_blog_post_comment' ) == 'yes' ) {
            if ( $event_slug == 'RPCPAR' ) {
                $subject = get_option( 'rs_email_subject_blog_post_comment' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_blog_post_comment' ) ) ) ;
            }
        }
        if ( get_option( 'rs_send_mail_blog_post_comment' ) == 'yes' ) {
            if ( $event_slug == 'RPFPOC' ) {
                $subject = get_option( 'rs_email_subject_blog_post_comment' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_blog_post_comment' ) ) ) ;
            }
        }
        // Page Comment
        if ( get_option( 'rs_send_mail_page_comment' ) == 'yes' ) {
            if ( $event_slug == 'RPFPAC' || $event_slug == 'RPCPR' ) {
                $subject = get_option( 'rs_email_subject_page_comment' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_page_comment' ) ) ) ;
            }
        }
        // Product Creation
        if ( get_option( 'rs_send_mail_product_create' ) == 'yes' ) {
            if ( $event_slug == 'RPCPRO' ) {
                $subject = get_option( 'rs_email_subject_product_create' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_product_create' ) ) ) ;
            }
        }
        // Login
        if ( get_option( 'rs_send_mail_login' ) == 'yes' ) {
            if ( $event_slug == 'LRP' || $event_slug == 'SLRP' ) {
                $subject = get_option( 'rs_email_subject_login' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_login' ) ) ) ;
            }
        }

        //Social Linking
        if ( get_option( 'rs_send_mail_for_social_account_linking' ) == 'yes' ) {
            if ( $event_slug == 'SLLRP' ) {
                $subject = get_option( 'rs_email_subject_for_social_account_linking' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_for_social_account_linking' ) ) ) ;
            }
        }

        //Birthday Reward
        if ( get_option( 'rs_send_mail_cus_field_reg' ) == 'yes' ) {
            if ( $event_slug == 'CRFRP' || $event_slug == 'CRPFDP' ) {
                $subject = get_option( 'rs_email_subject_cus_field_reg' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_cus_field_reg' ) ) ) ;
                $message = str_replace( '[rsfirstname]' , $first_name , str_replace( '[rslastname]' , $last_name , $message ) ) ;
            }
        }

        // Reward Gateway
        if ( get_option( 'rs_send_mail_payment_gateway' ) == 'yes' ) {
            if ( $event_slug == 'RPG' ) {
                $subject = get_option( 'rs_email_subject_payment_gateway' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_payment_gateway' ) ) ) ;
            }
        }

        // Coupon Points
        if ( get_option( 'rs_send_mail_coupon_reward' ) == 'yes' ) {
            if ( $event_slug == 'RPC' ) {
                $subject = get_option( 'rs_email_subject_coupon_reward' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_coupon_reward' ) ) ) ;
            }
        }
        //Facebook Like
        if ( get_option( 'rs_send_mail_Facebook_like' ) == 'yes' ) {
            if ( $event_slug == 'RPFL' ) {
                $subject = get_option( 'rs_email_subject_facebook_like' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_facebook_like' ) ) ) ;
            }
        }
        // Instagram
        if ( get_option( 'rs_send_mail_instagram' ) == 'yes' ) {
            if ( $event_slug == 'RPIF' ) {
                $subject = get_option( 'rs_email_subject_instagram' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_instagram' ) ) ) ;
            }
        }
        // OK
        if ( get_option( 'rs_send_mail_ok' ) == 'yes' ) {
            if ( $event_slug == 'RPOK' ) {
                $subject = get_option( 'rs_email_subject_ok' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_ok' ) ) ) ;
            }
        }
        // FacebookSare
        if ( get_option( 'rs_send_mail_facebook_share' ) == 'yes' ) {
            if ( $event_slug == 'RPFS' ) {
                $subject = get_option( 'rs_email_subject_facebook_share' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_facebook_share' ) ) ) ;
            }
        }
        // Twitter Tweet
        if ( get_option( 'rs_send_mail_tewitter_tweet' ) == 'yes' ) {
            if ( $event_slug == 'RPTT' ) {
                $subject = get_option( 'rs_email_subject_twitter_tweet' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_twitter_tweet' ) ) ) ;
            }
        }
        // Twitter Follow
        if ( get_option( 'rs_send_mail_twitter_follow' ) == 'yes' ) {
            if ( $event_slug == 'RPTF' ) {
                $subject = get_option( 'rs_email_subject_twitter_follow' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_twitter_follow' ) ) ) ;
            }
        }
        // Google Share
        if ( get_option( 'rs_send_mail_google' ) == 'yes' ) {
            if ( $event_slug == 'RPGPOS' ) {
                $subject = get_option( 'rs_email_subject_google' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_google' ) ) ) ;
            }
        }
        // VK
        if ( get_option( 'rs_send_mail_vk' ) ) {
            if ( $event_slug == 'RPVL' ) {
                $subject = get_option( 'rs_email_subject_vk' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_vk' ) ) ) ;
            }
        }

        /* Social icons Post Or Page Mail - Start */
        // OK Post
        if ( $event_slug == 'RPOKP' ) {
            if ( get_option( 'rs_send_mail_post_ok_ru' ) == 'yes' ) {
                $subject = get_option( 'rs_email_subject_post_ok_ru' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_post_ok_ru' ) ) ) ;
            }
        }
        // Instagram Post
        if ( $event_slug == 'RPIFP' ) {
            if ( get_option( 'rs_send_mail_post_instagram' ) == 'yes' ) {
                $subject = get_option( 'rs_email_subject_post_instagram' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_post_instagram' ) ) ) ;
            }
        }
        // VK Like Post
        if ( $event_slug == 'RPVLP' ) {
            if ( get_option( 'rs_send_mail_post_vk' ) == 'yes' ) {
                $subject = get_option( 'rs_email_subject_post_vk' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_post_vk' ) ) ) ;
            }
        }
        // Google Share Post
        if ( $event_slug == 'RPGPOSP' ) {
            if ( get_option( 'rs_send_mail_post_gplus' ) == 'yes' ) {
                $subject = get_option( 'rs_email_subject_post_gplus' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_post_gplus' ) ) ) ;
            }
        }
        // Twitter Share Post
        if ( $event_slug == 'RPTFP' ) {
            if ( get_option( 'rs_send_mail_post_follow' ) == 'yes' ) {
                $subject = get_option( 'rs_email_subject_post_follow' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_post_follow' ) ) ) ;
            }
        }
        // Twitter Tweet Post
        if ( $event_slug == 'RPTTP' ) {
            if ( get_option( 'rs_send_mail_post_tweet' ) == 'yes' ) {
                $subject = get_option( 'rs_email_subject_post_tweet' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_post_tweet' ) ) ) ;
            }
        }
        // Facebook Share Post
        if ( $event_slug == 'RPFSP' ) {
            if ( get_option( 'rs_send_mail_post_fb_share' ) == 'yes' ) {
                $subject = get_option( 'rs_email_subject_post_fb_share' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_post_fb_share' ) ) ) ;
            }
        }
        // Facebook Like Post
        if ( $event_slug == 'RPFLP' ) {
            if ( get_option( 'rs_send_mail_post_fb_like' ) == 'yes' ) {
                $subject = get_option( 'rs_email_subject_post_fb_like' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_post_fb_like' ) ) ) ;
            }
        }
        /* Social icons Post Or Page Mail - End */

        //Gift Voucher
        if ( get_option( 'rs_send_mail_gift_voucher' ) == 'yes' ) {
            if ( $event_slug == 'RPGV' ) {
                $subject = get_option( 'rs_email_subject_gift_voucher' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_gift_voucher' ) ) ) ;
            }
        }
        //Point URL
        if ( get_option( 'rs_send_mail_point_url' ) == 'yes' ) {
            if ( $event_slug == 'RPFURL' ) {
                $subject = get_option( 'rs_email_subject_point_url' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_point_url' ) ) ) ;
            }
        }

        //Referral Registration Points for Referral
        if ( get_option( 'rs_send_mail_referral_signup' ) == 'yes' ) {
            if ( $event_slug == 'RRRP' ) {
                $subject = get_option( 'rs_email_subject_referral_signup' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_user_name]' , $user_name , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_referral_signup' ) ) ) ) ;
            }
        }
        //  Referral Reward Points Getting Referred
        if ( get_option( 'rs_send_mail_getting_referred' ) == 'yes' ) {
            if ( $event_slug == 'RRPGR' ) {
                $subject = get_option( 'rs_email_subject_getting_referred' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_user_name]' , $user_name , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_getting_referred' ) ) ) ) ;
            }
        }
        //  Product Purchase for Referral
        if ( get_option( 'rs_send_mail_pdt_purchase_referral' ) == 'yes' ) {
            if ( $event_slug == 'PPRRP' ) {
                $subject = get_option( 'rs_email_subject_pdt_purchase_referral' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_pdt_purchase_referral' ) ) ) ;
                $message = rs_get_referrer_email_info_in_order( $order_id , $message ) ;
            }
        }

        // Product Purchase For Getting Referred
        if ( get_option( 'rs_send_mail_pdt_purchase_referrer' ) == 'yes' ) {
            if ( $event_slug == 'PPRRPG' ) {
                $subject = get_option( 'rs_email_subject_pdt_purchase_referrer' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_pdt_purchase_referrer' ) ) ) ;
            }
        }

        // Waiting List Subscribing
        if ( get_option( 'rs_send_mail_for_waitlist_subscribing' ) == 'yes' ) {
            if ( $event_slug == 'RPFWLS' ) {
                $subject = get_option( 'rs_email_subject_for_waitlist_subscribing' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_for_waitlist_subscribing' ) ) ) ;
            }
        }

        // Waiting List Sale Conversion
        if ( get_option( 'rs_send_mail_for_waitlist_sale_conversion' ) == 'yes' ) {
            if ( $event_slug == 'RPFWLSC' ) {
                $subject = get_option( 'rs_email_subject_for_waitlist_sale_conversion' ) ;
                $message = str_replace( '[rs_earned_points]' , $earned_point , str_replace( '[rs_available_points]' , $total_earned_point , get_option( 'rs_email_message_for_waitlist_sale_conversion' ) ) ) ;
            }
        }

        if ( $subject != '' || $message != '' ) {
            $message = str_replace( '[rsfirstname]' , $first_name , $message ) ;
            $message = str_replace( '[rslastname]' , $last_name , $message ) ;
            $message = do_shortcode( $message ) ; //shortcode feature
            send_mail( $to , $subject , $message ) ;
        }
    }

}

if ( ! function_exists( 'fp_order_status' ) ) {

    function fp_order_status() {
        $order_statuses = array() ;
        if ( function_exists( 'wc_get_order_statuses' ) ) {
            $orderstatus    = str_replace( 'wc-' , '' , array_keys( wc_get_order_statuses() ) ) ;
            $orderslugs     = array_values( wc_get_order_statuses() ) ;
            $order_statuses = array_combine( ( array ) $orderstatus , ( array ) $orderslugs ) ;
        } else {
            $term_args = array(
                'hide_empty' => false ,
                'orderby'    => 'date' ,
                    ) ;
            $tax_terms = get_terms( 'shop_order_status' , $term_args ) ;
            if ( srp_check_is_array( $tax_terms ) ) {
                $orderstatus = array() ;
                $orderslugs  = array() ;
                foreach ( $tax_terms as $getterms ) {
                    if ( is_object( $getterms ) ) {
                        $orderstatus[] = $getterms->name ;
                        $orderslugs[]  = $getterms->slug ;
                    }
                }
                $order_statuses = array_combine( ( array ) $orderslugs , ( array ) $orderstatus ) ;
            }
        }
        return $order_statuses ;
    }

}

if ( ! function_exists( 'redeem_point_conversion' ) ) {

    function redeem_point_conversion( $Value , $UserId , $Type = 'points' ) {
        $PointValue     = ( float ) wc_format_decimal( get_option( 'rs_redeem_point' ) ) ; //Conversion Points
        $RedeemPercent  = RSMemberFunction::redeem_points_percentage( $UserId ) ;
        $ConvertedValue = ($Type == 'price') ? ((( float ) $Value / $PointValue) * $RedeemPercent) : ((( float ) $Value * $PointValue) / $RedeemPercent) ; //Ex:10 * 2 = 20
        return $ConvertedValue ; // $.20
    }

}

if ( ! function_exists( 'earn_point_conversion' ) ) {

    function earn_point_conversion( $Points ) {
        $ConversionRate = wc_format_decimal( get_option( 'rs_earn_point' ) ) ; //Conversion Points
        $PointsValue    = wc_format_decimal( get_option( 'rs_earn_point_value' ) ) ; //Value for the Conversion Points (i.e)  1 points is equal to $.2
        $ConvertedValue = ($Points / $PointsValue) * $ConversionRate ; //Ex:10 * 2 = 20
        return $ConvertedValue ; // $.20
    }

}

if ( ! function_exists( 'check_if_referrer_has_manual_link' ) ) {

    function check_if_referrer_has_manual_link( $buyer_id ) {
        $linkarray = get_option( 'rewards_dynamic_rule_manual' ) ;
        if ( ! srp_check_is_array( $linkarray ) )
            return false ;

        foreach ( $linkarray as $key => $eachreferer ) {
            if ( $eachreferer[ "refferal" ] == $buyer_id )
                return $eachreferer[ 'referer' ] ;
        }
        return false ;
    }

}

if ( ! function_exists( 'send_mail_for_product_purchase' ) ) {

    function send_mail_for_product_purchase( $user_id , $order_id ) {
        global $wpdb ;
        $tablename = $wpdb->prefix . 'rs_templates_email' ;
        $templates = $wpdb->get_results( "SELECT * FROM $tablename" ) ; //all email templates
        if ( ! srp_check_is_array( $templates ) )
            return ;

        foreach ( $templates as $emails ) {
            if ( $emails->rs_status != "ACTIVE" )
                continue ;

            if ( $emails->rsmailsendingoptions == '3' )
                continue ;

            $SendMail = ($emails->mailsendingoptions == '1') ? ( get_post_meta( $order_id , 'rsearningtemplates' . $emails->id , true ) != '1') : true ;
            if ( $SendMail )
                include 'frontend/emails/class-fp-productpurchase-mail.php' ;
        }
    }

}

if ( ! function_exists( 'currency_value_for_available_points' ) ) {

    function currency_value_for_available_points( $UserId ) {
        $PointsData    = new RS_Points_Data( $UserId ) ;
        $Points        = $PointsData->total_available_points() ;
        $CurrencyValue = redeem_point_conversion( $Points , $UserId , 'price' ) ;
        return '<span class="rs_user_total_points"><b>' . $Points . ' (' . srp_formatted_price( round_off_type_for_currency( $CurrencyValue ) ) . ')</b></span>' ;
    }

}

if ( ! function_exists( 'date_display_format' ) ) {

    function date_display_format( $values ) {
        $gmtdate = is_numeric( $values[ 'earneddate' ] ) ? $values[ 'earneddate' ] + ( int ) get_option( 'gmt_offset' ) * HOUR_IN_SECONDS : $values[ 'earneddate' ] ;
        if ( get_option( 'rs_dispaly_time_format' ) == '1' ) {
            $update_start_date = is_numeric( $values[ 'earneddate' ] ) ? date_i18n( "d-m-Y h:i:s A" , $gmtdate ) : $values[ 'earneddate' ] ;
        } else {
            $timeformat        = get_option( 'time_format' ) ;
            $dateformat        = get_option( 'date_format' ) . ' ' . $timeformat ;
            $update_start_date = is_numeric( $values[ 'earneddate' ] ) ? date_i18n( $dateformat , $gmtdate ) : $values[ 'earneddate' ] ;
            $update_start_date = strftime( $update_start_date ) ;
        }
        return $update_start_date ;
    }

}

if ( ! function_exists( 'earned_points_from_order' ) ) {

    function earned_points_from_order( $OrderId ) {
        global $wpdb ;
        $TableName    = $wpdb->prefix . 'rsrecordpoints' ;
        $EarnedTotal  = array() ;
        $RevisedTotal = array() ;
        $EarnedData   = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints FROM $TableName WHERE checkpoints NOT IN ('RVPFRP','PPRRP') AND orderid = %d" , $OrderId ) , ARRAY_A ) ;
        foreach ( $EarnedData as $EarnedPoints ) {
            $EarnedTotal[] = $EarnedPoints[ 'earnedpoints' ] ;
        }
        $RevisedData = $wpdb->get_results( $wpdb->prepare( "SELECT redeempoints FROM $TableName WHERE checkpoints = 'RVPFPPRP' AND orderid = %d" , $OrderId ) , ARRAY_A ) ;
        foreach ( $RevisedData as $RevisedPoints ) {
            $RevisedTotal[] = $RevisedPoints[ 'redeempoints' ] ;
        }
        $TotalValue = array_sum( $EarnedTotal ) - array_sum( $RevisedTotal ) ;
        return round_off_type( $TotalValue ) ;
    }

}

if ( ! function_exists( 'redeem_points_from_order' ) ) {

    function redeem_points_from_order( $OrderId ) {
        global $wpdb ;
        $TableName    = $wpdb->prefix . 'rsrecordpoints' ;
        $RedeemTotal  = array() ;
        $RevisedTotal = array() ;
        $RedeemData   = $wpdb->get_results( $wpdb->prepare( "SELECT redeempoints FROM $TableName WHERE orderid = %d and checkpoints != 'RVPFPPRP'" , $OrderId ) , ARRAY_A ) ;
        foreach ( $RedeemData as $RedeemPoints ) {
            $RedeemTotal[] = $RedeemPoints[ 'redeempoints' ] ;
        }
        $RevisedData = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints FROM $TableName WHERE checkpoints = 'RVPFRP' and orderid = %d" , $OrderId ) , ARRAY_A ) ;
        foreach ( $RevisedData as $RevisedPoints ) {
            $RevisedTotal[] = $RevisedPoints[ 'earnedpoints' ] ;
        }
        $TotalValue = array_sum( $RedeemTotal ) - array_sum( $RevisedTotal ) ;
        return $TotalValue ;
    }

}

if ( ! function_exists( 'srp_footer_link' ) ) {

    function srp_footer_link( $footer_string ) {
        global $unsublink2 ;
        if ( $unsublink2 ) {
            return $unsublink2 ;
        }
        return $footer_string ;
    }

}

if ( ! function_exists( 'points_for_payment_gateways' ) ) {

    function points_for_payment_gateways( $order_id , $userid , $gatewayid ) {
        if ( get_option( 'rs_reward_type_for_payment_gateways_' . $gatewayid ) == '1' ) {
            $gatewaypoints = get_option( 'rs_reward_payment_gateways_' . $gatewayid ) ;
        } else {
            $percentpoints   = get_option( 'rs_reward_points_for_payment_gateways_in_percent_' . $gatewayid ) ;
            $cart_subtotal   = get_post_meta( $order_id , 'rs_cart_subtotal' , true ) == '' ? srp_cart_subtotal( true , $order_id ) : get_post_meta( $order_id , 'rs_cart_subtotal' , true ) ;
            $point_coversion = ((( float ) $percentpoints / 100) * $cart_subtotal) ;
            $gatewaypoints   = earn_point_conversion( $point_coversion ) ;
        }
        return round_off_type( $gatewaypoints ) ;
    }

}

if ( ! function_exists( 'block_points_for_renewal_order' ) ) {

    function block_points_for_renewal_order( $order_id , $enable ) {
        if ( $enable == 'yes' && get_post_meta( $order_id , 'sumo_renewal_order_date' , true ) != '' )
            return false ;

        return true ;
    }

}

if ( ! function_exists( 'expiry_date_for_points' ) ) {

    function expiry_date_for_points() {
        $date = 999999999999 ;
        if ( get_option( 'rs_point_expiry_activated' ) == 'yes' && ! empty( get_option( 'rs_point_to_be_expire' ) ) )
            $date = time() + (get_option( 'rs_point_to_be_expire' ) * 24 * 60 * 60) ;

        return $date ;
    }

}

if ( ! function_exists( 'round_off_type' ) ) {

    function round_off_type( $points , $args = array() ) {
        if ( get_option( 'rs_round_off_type' ) == '1' ) {
            if ( get_option( 'rs_decimal_seperator_check' ) == '1' ) {
                return round( $points , 2 ) ;
            } else {
                extract( wp_parse_args( $args , array(
                    'decimal_separator'  => wc_get_price_decimal_separator() ,
                    'thousand_separator' => wc_get_price_thousand_separator() ,
                    'decimals'           => wc_get_price_decimals() ,
                ) ) ) ;
                return round( $points , $decimals ) ;
            }
        } else {
            return (get_option( 'rs_round_up_down' ) == '1') ? floor( $points ) : ceil( $points ) ;
        }
    }

}

if ( ! function_exists( 'round_off_type_for_currency' ) ) {

    function round_off_type_for_currency( $currency , $args = array() ) {
        if ( get_option( 'rs_round_off_type' ) == '1' ) {
            return round_off_type( $currency ) ;
        } else {
            if ( get_option( 'rs_roundoff_type_for_currency' ) == '1' ) {
                if ( get_option( 'rs_decimal_seperator_check_for_currency' ) == '1' ) {
                    return round( $currency , 2 ) ;
                } else {
                    extract( wp_parse_args( $args , array(
                        'decimal_separator'  => wc_get_price_decimal_separator() ,
                        'thousand_separator' => wc_get_price_thousand_separator() ,
                        'decimals'           => wc_get_price_decimals() ,
                    ) ) ) ;
                    return round( $currency , $decimals ) ;
                }
            } else {
                return (get_option( 'rs_round_up_down' ) == '1') ? floor( $currency ) : ceil( $currency ) ;
            }
        }
    }

}

if ( ! function_exists( 'days_from_point_expiry_email' ) ) {

    function days_from_point_expiry_email() {
        global $wpdb ;
        $table_name = $wpdb->prefix . 'rs_expiredpoints_email' ;
        $templates  = $wpdb->get_results( $wpdb->prepare( "SELECT noofdays FROM $table_name WHERE template_name = %s AND rs_status='ACTIVE'" , get_option( 'rs_select_template' ) ) , ARRAY_A ) ;
        $days       = srp_check_is_array( $templates ) ? $templates[ 0 ][ 'noofdays' ] : 0 ;
        return ( int ) $days ;
    }

}

if ( ! function_exists( 'allow_reward_points_for_user' ) ) {

    function allow_reward_points_for_user( $userid ) {
        $allow_earn_points = get_user_meta( $userid , 'allow_user_to_earn_reward_points' , true ) ;
        if ( get_option( 'rs_enable_reward_program' ) != 'yes' )
            return true ;

        if ( ! ($allow_earn_points == 'yes') && ! ($allow_earn_points == '') )
            return false ;

        return true ;
    }

}

if ( ! function_exists( 'srp_enable_reward_program' ) ) {

    function srp_enable_reward_program( $userid ) {
        if ( get_option( 'rs_enable_reward_program' ) == 'yes' ) {
            if ( isset( $_POST[ 'rs_enable_earn_points_for_user_in_reg_form' ] ) || isset( $_POST[ 'enable_reward_prgm' ] ) ) {
                update_user_meta( $userid , 'allow_user_to_earn_reward_points' , 'yes' ) ;
                update_user_meta( $userid , 'unsub_value' , 'no' ) ;
            } else {
                update_user_meta( $userid , 'allow_user_to_earn_reward_points' , 'no' ) ;
            }
        }
    }

    add_action( 'user_register' , 'srp_enable_reward_program' , 10 , 1 ) ;
}

if ( ! function_exists( 'check_referral_count_if_exist' ) ) {

    function check_referral_count_if_exist( $userid ) {
        if ( get_option( 'rs_enable_referral_link_limit' ) != 'yes' )
            return true ;

        if ( get_option( 'rs_referral_link_limit' ) == '' )
            return true ;

        if ( get_user_meta( $userid , 'referral_link_count_value' , true ) == '' )
            return true ;

        $default_value = ( int ) get_user_meta( $userid , 'referral_link_count_value' , true ) ;
        if ( $default_value >= get_option( 'rs_referral_link_limit' ) )
            return false ;

        return true ;
    }

}

if ( ! function_exists( 'update_order_meta_if_points_awarded' ) ) {

    function update_order_meta_if_points_awarded( $orderid , $userid ) {
        update_user_meta( $userid , 'rsfirsttime_redeemed' , 1 ) ;
        add_post_meta( $orderid , 'reward_points_awarded' , 'yes' ) ;
        add_post_meta( $orderid , 'earning_point_once' , 1 ) ;
        update_post_meta( $orderid , 'rs_revised_points_once' , 2 ) ;
    }

}

if ( ! function_exists( 'update_product_count_for_social_action' ) ) {

    function update_product_count_for_social_action( $UserId , $MetaKey , $PostId ) {
        $ProductId[] = $PostId ;
        $OldData     = ( array ) get_user_meta( $UserId , $MetaKey , true ) ;
        if ( srp_check_is_array( $OldData ) ) {
            $ArrayFilter = array_filter( $OldData ) ;
            if ( isset( $ArrayFilter[ date( 'd/m/Y' ) ] ) ) {
                $DataToMerge                     = $ArrayFilter[ date( 'd/m/Y' ) ] ;
                $MergedData                      = array_merge( $DataToMerge , $ProductId ) ;
                $DataToUpdate[ date( 'd/m/Y' ) ] = $MergedData ;
                update_user_meta( $UserId , $MetaKey , $DataToUpdate ) ;
            } else {
                $DataToUpdate[ date( 'd/m/Y' ) ] = $ProductId ;
                update_user_meta( $UserId , $MetaKey , $DataToUpdate ) ;
            }
        } else {
            $DataToUpdate[ date( 'd/m/Y' ) ] = $ProductId ;
            update_user_meta( $UserId , $MetaKey , $DataToUpdate ) ;
        }
    }

}

if ( ! function_exists( 'allow_points_for_social_action' ) ) {

    function allow_points_for_social_action( $UserId , $MetaKey , $EnableAction , $Count ) {
        if ( $EnableAction == 'no' )
            return true ;

        if ( empty( $Count ) )
            return true ;

        $TotalCount = ( array ) get_user_meta( $UserId , $MetaKey , true ) ;
        if ( empty( $TotalCount ) )
            return true ;

        if ( ! isset( $TotalCount[ date( 'd/m/Y' ) ] ) )
            return true ;

        $ProductCount = count( $TotalCount[ date( 'd/m/Y' ) ] ) ;
        if ( $ProductCount >= $Count )
            return false ;

        return true ;
    }

}

if ( ! function_exists( 'get_reward_points_based_on_cart_total' ) ) {

    function get_reward_points_based_on_cart_total( $OrderTotal ) {
        if ( get_option( 'rs_enable_cart_total_reward_points' ) == '2' )
            return 0 ;

        if ( get_option( 'rs_reward_type_for_cart_total' ) == '1' ) {
            $PointsToAward = empty( get_option( 'rs_reward_points_for_cart_total_in_fixed' ) ) ? 0 : get_option( 'rs_reward_points_for_cart_total_in_fixed' ) ;
        } else {
            $PointsToAward = empty( get_option( 'rs_reward_points_for_cart_total_in_percent' ) ) ? 0 : convert_percent_value_as_points( get_option( 'rs_reward_points_for_cart_total_in_percent' ) , $OrderTotal ) ;
        }
        return $PointsToAward ;
    }

}

if ( ! function_exists( 'get_list_of_modules' ) ) {

    function get_list_of_modules( $value = '' ) {
        return array(
            'fpproductpurchase'    => $value == 'name' ? __( 'Product Purchase' , SRP_LOCALE ) : get_option( 'rs_product_purchase_activated' ) ,
            'fpbuyingpoints'       => $value == 'name' ? __( 'Buying Points' , SRP_LOCALE ) : get_option( 'rs_buyingpoints_activated' ) ,
            'fpreferralsystem'     => $value == 'name' ? __( 'Referral System' , SRP_LOCALE ) : get_option( 'rs_referral_activated' ) ,
            'fpsocialreward'       => $value == 'name' ? __( 'Social Reward Points' , SRP_LOCALE ) : get_option( 'rs_social_reward_activated' ) ,
            'fpactionreward'       => $value == 'name' ? __( 'Action Reward Points' , SRP_LOCALE ) : get_option( 'rs_reward_action_activated' ) ,
            'fppointexpiry'        => $value == 'name' ? __( 'Points Expiry' , SRP_LOCALE ) : get_option( 'rs_point_expiry_activated' ) ,
            'fpredeeming'          => $value == 'name' ? __( 'Redeeming Points' , SRP_LOCALE ) : get_option( 'rs_redeeming_activated' ) ,
            'fppointprice'         => $value == 'name' ? __( 'Points Price' , SRP_LOCALE ) : get_option( 'rs_point_price_activated' ) ,
            'fpmail'               => $value == 'name' ? __( 'Email' , SRP_LOCALE ) : get_option( 'rs_email_activated' ) ,
            'fpemailexpiredpoints' => $value == 'name' ? __( 'Email Template for Expire' , SRP_LOCALE ) : get_option( 'rs_email_template_expire_activated' ) ,
            'fpgiftvoucher'        => $value == 'name' ? __( 'Gift Voucher' , SRP_LOCALE ) : get_option( 'rs_gift_voucher_activated' ) ,
            'fpsms'                => $value == 'name' ? __( 'SMS' , SRP_LOCALE ) : get_option( 'rs_sms_activated' ) ,
            'fpcashback'           => $value == 'name' ? __( 'Cashback' , SRP_LOCALE ) : get_option( 'rs_cashback_activated' ) ,
            'fpnominee'            => $value == 'name' ? __( 'Nominee' , SRP_LOCALE ) : get_option( 'rs_nominee_activated' ) ,
            'fppointurl'           => $value == 'name' ? __( 'Point URL' , SRP_LOCALE ) : get_option( 'rs_point_url_activated' ) ,
            'fprewardgateway'      => $value == 'name' ? __( 'Reward Points Payment Gateway' , SRP_LOCALE ) : get_option( 'rs_gateway_activated' ) ,
            'fpsendpoints'         => $value == 'name' ? __( 'Send Points' , SRP_LOCALE ) : get_option( 'rs_send_points_activated' ) ,
            'fpimportexport'       => $value == 'name' ? __( 'Import/Export Points' , SRP_LOCALE ) : get_option( 'rs_imp_exp_activated' ) ,
            'fpreportsincsv'       => $value == 'name' ? __( 'Reports' , SRP_LOCALE ) : get_option( 'rs_report_activated' ) ,
            'fpdiscounts'          => $value == 'name' ? __( 'SUMO Discounts Compatibility' , SRP_LOCALE ) : get_option( 'rs_discounts_compatability_activated' ) ,
            'fpcoupon'             => $value == 'name' ? __( 'SUMO Coupon Compatibility' , SRP_LOCALE ) : get_option( 'rs_coupon_compatability_activated' ) ,
            'fpreset'              => $value == 'name' ? __( 'Reset' , SRP_LOCALE ) : get_option( 'rs_reset_activated' ) ,
                ) ;
    }

}

if ( ! function_exists( 'modules_file_name' ) ) {

    function modules_file_name() {
        return array(
            'fpproductpurchase' ,
            'fpbuyingpoints' ,
            'fpreferralsystem' ,
            'fpsocialreward' ,
            'fpactionreward' ,
            'fppointexpiry' ,
            'fpredeeming' ,
            'fppointprice' ,
            'fpmail' ,
            'fpemailexpiredpoints' ,
            'fpgiftvoucher' ,
            'fpsms' ,
            'fpcashback' ,
            'fpnominee' ,
            'fppointurl' ,
            'fprewardgateway' ,
            'fpsendpoints' ,
            'fpimportexport' ,
            'fpreportsincsv' ,
            'fpdiscounts' ,
            'fpcoupon' ,
            'fpreset' ,
                ) ;
    }

}

if ( ! function_exists( 'check_if_referral_is_restricted_based_on_history' ) ) {

    function check_if_referral_is_restricted_based_on_history() {
        if ( ! is_user_logged_in() )
            return false ;

        if ( get_option( 'rs_enable_referral_link_generate_after_first_order' ) != 'yes' )
            return true ;

        global $wpdb ;
        $OrderStatuses = get_option( 'rs_set_order_status_for_generate_link' ) ;
        if ( empty( $OrderStatuses ) )
            return true ;

        $WCStatus       = array_keys( wc_get_order_statuses() ) ;
        $reached_status = array() ;
        foreach ( $OrderStatuses as $OrderStatus ) {
            if ( ! in_array( $OrderStatus , $WCStatus ) )
                $reached_status[] = 'wc-' . $OrderStatus ;
        }
        $OrderIds = $wpdb->get_results( "SELECT posts.ID
                        FROM $wpdb->posts as posts
                        LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
                        WHERE   meta.meta_key       = '_customer_user'
                        AND     posts.post_status   IN ('" . implode( "','" , $reached_status ) . "')
                        AND     meta_value          = '" . get_current_user_id() . "'
                " , ARRAY_A ) ;

        if ( ! srp_check_is_array( $OrderIds ) )
            return false ;

        if ( get_option( 'rs_referral_link_generated_settings' ) == '1' ) {
            $Count      = count( $OrderIds ) ;
            $Nooforders = ( int ) get_option( 'rs_getting_number_of_orders' ) ;
            if ( empty( $Nooforders ) )
                return true ;

            if ( $Count >= $Nooforders )
                return true ;
        } else if ( get_option( 'rs_referral_link_generated_settings' ) == '2' ) {
            $AmountSpent = ( float ) get_option( 'rs_number_of_amount_spent' ) ;
            if ( empty( $AmountSpent ) )
                return true ;

            $OrderTotal = array() ;
            foreach ( $OrderIds as $OrderId ) {
                $OrderTotal[] = get_post_meta( $OrderId[ 'ID' ] , '_order_total' , true ) ;
            }
            $TotalAmnt = srp_check_is_array( $OrderTotal ) ? array_sum( $OrderTotal ) : 0 ;
            if ( $TotalAmnt >= $AmountSpent )
                return true ;
        }
        return false ;
    }

}

if ( ! function_exists( 'send_mail' ) ) {

    function send_mail( $to , $subject , $message ) {
        global $unsublink2 ;
        $user       = get_user_by( 'email' , $to ) ;
        $wpnonce    = wp_create_nonce( 'rs_unsubscribe_' . $user->ID ) ;
        $unsublink  = esc_url_raw( add_query_arg( array( 'userid' => $user->ID , 'unsub' => 'yes' , 'nonce' => $wpnonce ) , site_url() ) ) ;
        $unsublink2 = str_replace( '{rssitelinkwithid}' , $unsublink , get_option( 'rs_unsubscribe_link_for_email' ) ) ;

        add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;

        ob_start() ;
        wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $subject ) ) ;
        echo $message ;
        wc_get_template( 'emails/email-footer.php' ) ;
        $woo_temp_msg = ob_get_clean() ;
        $headers      = "MIME-Version: 1.0\r\n" ;
        $headers      .= "Content-Type: text/html; charset=UTF-8\r\n" ;
        if ( '2' == get_option( 'rs_enable_email_function_actions' , '2' ) ) {
            $mailer = WC()->mailer() ;
            if ( $mailer->send( $to , $subject , $woo_temp_msg , $headers ) ) {
                
            }
        } elseif ( '1' == get_option( 'rs_enable_email_function_actions' , '2' ) ) {
            if ( mail( $to , $subject , $woo_temp_msg , $headers ) ) {
                
            }
        }

        remove_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
    }

}

if ( ! function_exists( 'get_referrer_ip_address' ) ) {

    function get_referrer_ip_address() {
        $ipaddress = '' ;

        if ( isset( $_SERVER[ 'HTTP_X_REAL_IP' ] ) )
            $ipaddress = $_SERVER[ 'HTTP_X_REAL_IP' ] ;
        else if ( isset( $_SERVER[ 'HTTP_CLIENT_IP' ] ) )
            $ipaddress = $_SERVER[ 'HTTP_CLIENT_IP' ] ;
        else if ( isset( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) )
            $ipaddress = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ;
        else if ( isset( $_SERVER[ 'HTTP_X_FORWARDED' ] ) )
            $ipaddress = $_SERVER[ 'HTTP_X_FORWARDED' ] ;
        else if ( isset( $_SERVER[ 'HTTP_FORWARDED_FOR' ] ) )
            $ipaddress = $_SERVER[ 'HTTP_FORWARDED_FOR' ] ;
        else if ( isset( $_SERVER[ 'HTTP_FORWARDED' ] ) )
            $ipaddress = $_SERVER[ 'HTTP_FORWARDED' ] ;
        else if ( isset( $_SERVER[ 'REMOTE_ADDR' ] ) )
            $ipaddress = $_SERVER[ 'REMOTE_ADDR' ] ;

        return $ipaddress ;
    }

}

if ( ! function_exists( 'global_variable_points' ) ) {

    function global_variable_points() {
        global $totalrewardpointsnew ;
        global $totalrewardpoints_payment_plan ;
        $ProductPlanPoints = srp_check_is_array( $totalrewardpoints_payment_plan ) ? ( array_sum( $totalrewardpoints_payment_plan ) ) : 0 ;
        $EarnPoints        = srp_check_is_array( $totalrewardpointsnew ) ? ( array_sum( $totalrewardpointsnew ) ) : 0 ;
        $EarnPoints        = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $EarnPoints ) ;
        $TotalPoints       = ($EarnPoints - $ProductPlanPoints) - apply_filters( 'srp_buying_points_for_payment_plan_in_cart' , 0 ) ;

        return $TotalPoints ;
    }

}

if ( ! function_exists( 'rs_custom_search_fields' ) ) {

    function rs_custom_search_fields( $args ) {
        $args = wp_parse_args( $args , array(
            'class'              => '' ,
            'id'                 => '' ,
            'name'               => '' ,
            'type'               => '' ,
            'action'             => '' ,
            'title'              => '' ,
            'placeholder'        => '' ,
            'css'                => '' ,
            'multiple'           => true ,
            'allow_clear'        => true ,
            'selected'           => true ,
            'options'            => array() ,
            'translation_string' => ''
                ) ) ;
        ob_start() ;
        if ( ( float ) WC_VERSION <= ( float ) ('2.2') ) {
            ?><select <?php echo $args[ 'multiple' ] ? 'multiple="multiple"' : '' ?> name="<?php
            echo esc_attr( '' !== $args[ 'name' ] ? $args[ 'name' ] : $args[ 'id' ]  ) ;
            if ( $args[ 'multiple' ] ) {
                ?>[]<?php } ?>" id="<?php echo esc_attr( $args[ 'id' ] ) ; ?>" class="<?php echo esc_attr( $args[ 'id' ] ) ; ?>" data-placeholder="<?php _e( esc_attr( $args[ 'placeholder' ] ) , $args[ 'translation_string' ] ) ; ?>" style="<?php echo esc_attr( $args[ 'css' ] ) ; ?>"><?php
                                                                                       if ( is_array( $args[ 'options' ] ) ) {
                                                                                           foreach ( $args[ 'options' ] as $id ) {
                                                                                               $option_value = '' ;
                                                                                               switch ( $args[ 'type' ] ) {
                                                                                                   case 'product':
                                                                                                       if ( $product = wc_get_product( $id ) ) {
                                                                                                           $option_value = wp_kses_post( $product->get_formatted_name() ) ;
                                                                                                       }
                                                                                                       break ;
                                                                                                   case 'customer':
                                                                                                       if ( $user = get_user_by( 'id' , $id ) ) {
                                                                                                           $option_value = esc_html( esc_html( $user->display_name ) . '(#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ) ;
                                                                                                       }
                                                                                                       break ;
                                                                                                   case 'customfields':
                                                                                                       $option_value = esc_html( get_the_title( $id ) ) ;
                                                                                                       break ;
                                                                                               }
                                                                                               if ( $option_value ) {
                                                                                                   ?>
                            <option value="<?php echo esc_attr( $id ) ; ?>" <?php echo $args[ 'selected' ] ? 'selected="selected"' : '' ?>><?php echo $option_value ; ?></option>
                            <?php
                        }
                    }
                }
                ?></select><?php } else if ( ( float ) WC_VERSION < ( float ) ('3.0') ) {
                ?>
            <input type="hidden" name="<?php echo esc_attr( '' !== $args[ 'name' ] ? $args[ 'name' ] : $args[ 'id' ]  ) ; ?>" id="<?php echo esc_attr( $args[ 'id' ] ) ; ?>" class="<?php echo esc_attr( $args[ 'class' ] ) ; ?>" data-action="<?php echo esc_attr( $args[ 'action' ] ) ; ?>" data-placeholder="<?php _e( esc_attr( $args[ 'placeholder' ] ) , $args[ 'translation_string' ] ) ; ?>" <?php echo $args[ 'multiple' ] ? 'data-multiple="true"' : '' ?> <?php echo $args[ 'allow_clear' ] ? 'data-allow_clear="true"' : '' ?> style="<?php echo esc_attr( $args[ 'css' ] ) ; ?>" <?php if ( $args[ 'selected' ] ) { ?> data-selected="<?php
                $json_ids = array() ;

                if ( is_array( $args[ 'options' ] ) ) {
                    foreach ( $args[ 'options' ] as $id ) {
                        switch ( $args[ 'type' ] ) {
                            case 'product':
                                if ( $product = wc_get_product( $id ) ) {
                                    $json_ids[ $id ] = wp_kses_post( $product->get_formatted_name() ) ;
                                }
                                break ;
                            case 'customer':
                                if ( $user = get_user_by( 'id' , $id ) ) {
                                    $json_ids[ $id ] = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
                                }
                                break ;
                            case 'customfields':
                                $json_ids[ $id ] = esc_html( get_the_title( $id ) ) ;
                                break ;
                        }
                    }
                }
                echo esc_attr( json_encode( $json_ids ) ) ;
                ?>" value="<?php
                       echo implode( ',' , array_keys( $json_ids ) ) ;
                   }
                   ?>"/><?php } else {
                   ?>
            <select <?php echo $args[ 'multiple' ] ? 'multiple="multiple"' : '' ?> name="<?php
            echo esc_attr( '' !== $args[ 'name' ] ? $args[ 'name' ] : $args[ 'id' ]  ) ;
            if ( $args[ 'multiple' ] ) {
                ?>[]<?php } ?>" id="<?php echo esc_attr( $args[ 'id' ] ) ; ?>" class="<?php echo esc_attr( $args[ 'class' ] ) ; ?>" data-action="<?php echo esc_attr( $args[ 'action' ] ) ; ?>" data-placeholder="<?php _e( esc_attr( $args[ 'placeholder' ] ) , $args[ 'translation_string' ] ) ; ?>" style="<?php echo esc_attr( $args[ 'css' ] ) ; ?>"><?php
                                                                                       if ( is_array( $args[ 'options' ] ) ) {
                                                                                           foreach ( $args[ 'options' ] as $id ) {
                                                                                               $option_value = '' ;
                                                                                               switch ( $args[ 'type' ] ) {
                                                                                                   case 'product':
                                                                                                       if ( $product = wc_get_product( $id ) ) {
                                                                                                           $option_value = wp_kses_post( $product->get_formatted_name() ) ;
                                                                                                       }
                                                                                                       break ;
                                                                                                   case 'customer':
                                                                                                       if ( $user = get_user_by( 'id' , $id ) ) {
                                                                                                           $option_value = esc_html( esc_html( $user->display_name ) . '(#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ) ;
                                                                                                       }
                                                                                                       break ;
                                                                                                   case 'customfields':
                                                                                                       $option_value = esc_html( get_the_title( $id ) ) ;
                                                                                                       break ;
                                                                                               }
                                                                                               if ( $option_value ) {
                                                                                                   ?><option value="<?php echo esc_attr( $id ) ; ?>" <?php echo $args[ 'selected' ] ? 'selected="selected"' : '' ?>><?php echo $option_value ; ?></option><?php
                        }
                    }
                }
                ?></select><?php
        }
        echo ob_get_clean() ;
    }

}

function calculate_point_price_for_products( $product_id ) {
    $data[ $product_id ] = '' ;
    if ( get_option( 'rs_point_price_activated' ) != 'yes' )
        return $data ;

    if ( get_option( 'rs_enable_disable_point_priceing' ) == '2' )
        return $data ;

    //Simple Product Data
    $EnablePointPriceForSimple        = get_post_meta( $product_id , '_rewardsystem_enable_point_price' , true ) ;
    $ProductLevelPointsForSimple      = get_post_meta( $product_id , '_rewardsystem__points' , true ) ;
    $PointsTypeForSimple              = get_post_meta( $product_id , '_rewardsystem_point_price_type' , true ) ;
    $PointsBasedOnConversionForSimple = get_post_meta( $product_id , '_rewardsystem__points_based_on_conversion' , true ) ;
    $PointPriceTypeForSimple          = get_post_meta( $product_id , '_rewardsystem_enable_point_price_type' , true ) ;

    //Variable Product Data
    $EnablePointPriceForVariation       = get_post_meta( $product_id , '_enable_reward_points_price' , true ) ;
    $ProductLevelPointsForVariation     = get_post_meta( $product_id , 'price_points' , true ) ;
    $PointsTypeForVariable              = get_post_meta( $product_id , '_enable_reward_points_price_type' , true ) ;
    $PointsBasedOnConversionForVariable = get_post_meta( $product_id , '_price_points_based_on_conversion' , true ) ;
    $PointPriceTypeForVariable          = get_post_meta( $product_id , '_enable_reward_points_pricing_type' , true ) ;

    $GlobalPointPriceType = get_option( 'rs_global_point_price_type' ) ;
    $ProductObj           = srp_product_object( $product_id ) ;
    if ( get_option( 'rs_enable_product_category_level_for_points_price' ) == 'no' ) {
        $Options        = array(
            'applicable_for'      => get_option( 'rs_point_pricing_global_level_applicable_for' ) ,
            'included_products'   => get_option( 'rs_include_products_for_point_pricing' ) ,
            'excluded_products'   => get_option( 'rs_exclude_products_for_point_pricing' ) ,
            'included_categories' => get_option( 'rs_include_particular_categories_for_point_pricing' ) ,
            'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_point_pricing' )
                ) ;
        $product_filter = srp_product_filter_for_quick_setup( $product_id , $product_id , $Options ) ;
    } elseif ( get_option( 'rs_enable_product_category_level_for_points_price' ) == 'yes' ) {
        $product_filter = '1' ;
    }
    if ( is_object( $ProductObj ) && (srp_product_type( $product_id ) == 'simple' || (srp_product_type( $product_id ) == 'subscription') || (srp_product_type( $product_id ) == 'lottery')) ) {
        if ( $product_filter == '1' && $EnablePointPriceForSimple == 'yes' ) {
            $data[ $product_id ] = product_level_point_pricing_value( $PointsTypeForSimple , $PointPriceTypeForSimple , $ProductLevelPointsForSimple , $product_id ) ;
        } elseif ( $product_filter == '2' ) {
            $data[ $product_id ] = global_level_point_pricing_value( $product_id ) ;
        }
    } else {
        if ( wp_get_post_parent_id( $product_id ) != '0' ) {
            $ProductObjForVariable = new WC_Product_Variation( $product_id ) ;
            $ProductIdForVariable  = get_parent_id( $ProductObjForVariable ) ;
        } else {
            $ProductIdForVariable = $product_id ;
        }
        if ( $product_filter == '1' ) {
            if ( $EnablePointPriceForVariation == '1' ) {
                if ( ($PointPriceTypeForVariable == '2' && ! empty( $ProductLevelPointsForVariation ) ) ) {
                    $data[ $product_id ] = $ProductLevelPointsForVariation ;
                } else {
                    if ( $PointsTypeForVariable == 1 ) {
                        $data[ $product_id ] = ( empty( $ProductLevelPointsForVariation ) ) ? category_level_point_pricing_value( $ProductIdForVariable ) : $ProductLevelPointsForVariation ;
                    } else {
                        $data[ $product_id ] = point_price_based_on_conversion( $product_id ) ;
                    }
                }
            }
        } elseif ( $product_filter == '2' ) {
            $data[ $product_id ] = global_level_point_pricing_value( $product_id ) ;
        }
    }
    if ( is_object( $ProductObj ) && (srp_product_type( $product_id ) == 'booking') ) {
        $booking_points      = get_post_meta( $product_id , 'booking_points' , true ) ;
        $data[ $product_id ] = $booking_points ;
    }
    return $data ;
}

function product_level_point_pricing_value( $PointsTypeForSimple , $PointPriceTypeForSimple , $ProductLevelPointsForSimple , $product_id ) {
    if ( $PointPriceTypeForSimple == '2' ) {
        $data = empty( $ProductLevelPointsForSimple ) ? category_level_point_pricing_value( $product_id ) : $ProductLevelPointsForSimple ;
    } else {
        if ( ( $PointsTypeForSimple == 1 ) ) {
            $data = ( empty( $ProductLevelPointsForSimple ) ) ? category_level_point_pricing_value( $product_id ) : $ProductLevelPointsForSimple ;
        } else {
            $data = point_price_based_on_conversion( $product_id ) ;
        }
    }
    return $data ;
}

function category_level_point_pricing_value( $product_id ) {
    $term = get_the_terms( $product_id , 'product_cat' ) ;
    if ( srp_check_is_array( $term ) ) {
        foreach ( $term as $term ) {
            $EnablePointPriceInCategory = srp_term_meta( $term->term_id , 'enable_point_price_category' ) ;
            if ( ($EnablePointPriceInCategory == 'yes' ) ) {
                $PointsPriceType = srp_term_meta( $term->term_id , 'pricing_category_types' ) ;
                $PointsType      = srp_term_meta( $term->term_id , 'point_price_category_type' ) ;
                $PointPriceValue = srp_term_meta( $term->term_id , 'rs_category_points_price' ) ;
                if ( $PointsPriceType == '2' ) {
                    $data = empty( $PointPriceValue ) ? global_level_point_pricing_value( $product_id ) : $PointPriceValue ;
                } else {
                    if ( $PointsType == '1' ) {
                        $data = empty( $PointPriceValue ) ? global_level_point_pricing_value( $product_id ) : $PointPriceValue ;
                    } else {
                        $data = point_price_based_on_conversion( $product_id ) ;
                    }
                }
            } else {
                $data = global_level_point_pricing_value( $product_id ) ;
            }
        }
    } else {
        $data = global_level_point_pricing_value( $product_id ) ;
    }
    return $data ;
}

function global_level_point_pricing_value( $product_id ) {
    $data                     = '' ;
    $EnablePointPriceInGlobal = get_option( 'rs_local_enable_disable_point_price_for_product' ) ;
    if ( $EnablePointPriceInGlobal == '1' ) {
        $PointPricingType = get_option( 'rs_pricing_type_global_level' ) ;
        $PointsType       = get_option( 'rs_global_point_price_type' ) ;
        if ( ($PointPricingType == '2' && ! empty( get_option( 'rs_local_price_points_for_product' ) )) || ( $PointsType == '1' && ! empty( get_option( 'rs_local_price_points_for_product' ) )) ) {
            $data = get_option( 'rs_local_price_points_for_product' ) ;
        } else {
            $data = point_price_based_on_conversion( $product_id ) ;
        }
    }
    return $data ;
}

function point_price_based_on_conversion( $product_id ) {
    $product       = srp_product_object( $product_id ) ;
    $product_price = srp_product_price( $product ) ;
    return redeem_point_conversion( $product_price , get_current_user_id() ) ;
}

function check_display_price_type( $product_id ) {
    if ( get_option( 'rs_point_price_activated' ) != 'yes' )
        return ;

    if ( get_option( 'rs_enable_disable_point_priceing' ) == '2' )
        return ;

    if ( get_option( 'rs_enable_product_category_level_for_points_price' ) == 'no' ) {  //Quick Setup
        if ( get_option( 'rs_local_enable_disable_point_price_for_product' ) == '2' )
            return ;

        $ProductFilters            = array(
            'applicable_for'      => get_option( 'rs_point_pricing_global_level_applicable_for' ) ,
            'included_products'   => get_option( 'rs_include_products_for_point_pricing' ) ,
            'excluded_products'   => get_option( 'rs_exclude_products_for_point_pricing' ) ,
            'included_categories' => get_option( 'rs_include_particular_categories_for_point_pricing' ) ,
            'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_point_pricing' )
                ) ;
        $ProductsToApplyPointPrice = srp_product_filter_for_quick_setup( $product_id , $product_id , $ProductFilters ) ;
        if ( $ProductsToApplyPointPrice == '2' ) {
            if ( get_option( 'rs_pricing_type_global_level' ) == '1' ) {
                if ( get_option( 'rs_local_price_points_for_product' , '' ) != '' && get_option( 'rs_global_point_price_type' , '1' ) == '1' ) {
                    return '1' ;
                } else if ( get_option( 'rs_global_point_price_type' , '1' ) == '2' ) {
                    return '1' ;
                }
            } else {
                if ( get_option( 'rs_local_price_points_for_product' ) != '' )
                    return '2' ;
            }
        }
    } else {
        //Product Level
        $PointPriceinProductLevel = get_post_meta( $product_id , '_rewardsystem_enable_point_price' , true ) != '' ? get_post_meta( $product_id , '_rewardsystem_enable_point_price' , true ) : get_post_meta( $product_id , '_enable_reward_points_price' , true ) ;
        $display_type             = get_post_meta( $product_id , '_rewardsystem_enable_point_price_type' , true ) != '' ? get_post_meta( $product_id , '_rewardsystem_enable_point_price_type' , true ) : get_post_meta( $product_id , '_enable_reward_points_pricing_type' , true ) ;
        $point_price_type         = get_post_meta( $product_id , '_rewardsystem_point_price_type' , true ) != '' ? get_post_meta( $product_id , '_rewardsystem_point_price_type' , true ) : get_post_meta( $product_id , '_enable_reward_points_price_type' , true ) ;
        $PointPriceValue          = get_post_meta( $product_id , '_rewardsystem__points' , true ) != '' ? get_post_meta( $product_id , '_rewardsystem__points' , true ) : get_post_meta( $product_id , 'price_points' , true ) ;
        if ( $PointPriceinProductLevel == 'no' || $PointPriceinProductLevel == '2' )
            return ;

        if ( '1' === $display_type ) {
            if ( '1' === $point_price_type && $PointPriceValue ) {
                return '1' ;
            } else if ( '2' === $point_price_type ) {
                return '1' ;
            }
        } else {
            if ( $PointPriceValue ) {
                return '2' ;
            }
        }

        return category_level_display_type( $product_id ) ;
    }
}

function category_level_display_type( $product_id ) {
    $term = get_the_terms( $product_id , 'product_cat' ) ;
    if ( ! srp_check_is_array( $term ) )
        return global_level_display_type() ;

    foreach ( $term as $term ) {
        if ( (srp_term_meta( $term->term_id , 'enable_point_price_category' ) != 'yes' ) )
            return global_level_display_type() ;

        $PointsPriceType = srp_term_meta( $term->term_id , 'pricing_category_types' ) ;
        $PointPriceValue = srp_term_meta( $term->term_id , 'rs_category_points_price' ) ;
        if ( $PointsPriceType == '1' && $PointPriceValue != '' ) {
            return '1' ;
        } else {
            if ( $PointPriceValue != '' )
                return '2' ;
        }
    }
    return global_level_display_type() ;
}

function global_level_display_type() {
    if ( get_option( 'rs_local_enable_disable_point_price_for_product' ) == '1' ) {
        if ( get_option( 'rs_pricing_type_global_level' ) == '1' && get_option( 'rs_local_price_points_for_product' ) != '' ) {
            return '1' ;
        } else {
            if ( get_option( 'rs_local_price_points_for_product' ) != '' )
                return '2' ;
        }
    }
}

function get_point_level( $productid , $variationid , $referred_user , $getting_referrer , $socialreward ) {
    if ( $socialreward == 'yes' ) {
        if ( get_option( 'rs_enable_product_category_level_for_social_reward' ) == 'no' ) {
            $Options = array(
                'applicable_for'      => get_option( 'rs_social_reward_global_level_applicable_for' ) ,
                'included_products'   => get_option( 'rs_include_products_for_social_reward' ) ,
                'excluded_products'   => get_option( 'rs_exclude_products_for_social_reward' ) ,
                'included_categories' => get_option( 'rs_include_particular_categories_for_social_reward' ) ,
                'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_social_reward' )
                    ) ;
            if ( get_option( 'rs_global_social_enable_disable_reward' ) === '1' ) {
                return srp_product_filter_for_quick_setup( $productid , $variationid , $Options ) ;
            } else {
                return false ;
            }
        } elseif ( get_option( 'rs_enable_product_category_level_for_social_reward' ) == 'yes' ) {
            return '1' ;
        }
    } elseif ( $referred_user != '' || $getting_referrer == 'yes' ) {
        if ( get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) == 'no' ) {
            $Options = array(
                'applicable_for'      => get_option( 'rs_referral_product_purchase_global_level_applicable_for' ) ,
                'included_products'   => get_option( 'rs_include_products_for_referral_product_purchase' ) ,
                'excluded_products'   => get_option( 'rs_exclude_products_for_referral_product_purchase' ) ,
                'included_categories' => get_option( 'rs_include_particular_categories_for_referral_product_purchase' ) ,
                'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_referral_product_purchase' )
                    ) ;
            return srp_product_filter_for_quick_setup( $productid , $variationid , $Options ) ;
        } elseif ( get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) == 'yes' ) {
            return '1' ;
        }
    } else {
        if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' ) {
            $Options = array(
                'applicable_for'      => get_option( 'rs_product_purchase_global_level_applicable_for' ) ,
                'included_products'   => get_option( 'rs_include_products_for_product_purchase' ) ,
                'excluded_products'   => get_option( 'rs_exclude_products_for_product_purchase' ) ,
                'included_categories' => get_option( 'rs_include_particular_categories_for_product_purchase' ) ,
                'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_product_purchase' )
                    ) ;
            return srp_product_filter_for_quick_setup( $productid , $variationid , $Options ) ;
        } elseif ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' ) {
            return '1' ;
        }
    }
}

function check_level_of_enable_reward_point( $args = array() ) {
    $default_args        = array(
        'variationid'      => 0 ,
        'checklevel'       => 'no' ,
        'referred_user'    => '' ,
        'getting_referrer' => 'no' ,
        'socialreward'     => 'no' ,
        'rewardfor'        => '' ,
        'payment_price'    => 0
            ) ;
    $parse_args          = wp_parse_args( $args , $default_args ) ;
    extract( $parse_args ) ;
    $memebershiprestrict = 'no' ;
    if ( get_option( 'rs_enable_restrict_reward_points' ) == 'yes' && function_exists( 'check_plan_exists' ) )
        $memebershiprestrict = check_plan_exists( get_current_user_id() ) ? 'no' : 'yes' ;

    $itemquantity = isset( $item[ 'qty' ] ) ? $item[ 'qty' ] : $item[ 'quantity' ] ;
    if ( $memebershiprestrict == 'no' ) {
        $point_level = get_point_level( $productid , $variationid , $referred_user , $getting_referrer , $socialreward ) ;
        if ( $point_level == '1' ) {
            return is_product_level( $productid , $variationid , $item , $checklevel , $referred_user , $getting_referrer , $socialreward , $rewardfor , $payment_price , $itemquantity ) ;
        } elseif ( $point_level == '2' ) {
            return is_global_level( $productid , $variationid , $item , $checklevel , $referred_user , $getting_referrer , $socialreward , $rewardfor , $payment_price , $itemquantity ) ;
        }
    }
}

function is_product_level( $productid , $variationid , $item , $checklevel , $referred_user , $getting_referrer , $socialreward , $rewardfor , $payment_price , $itemquantity ) {
    //Product Level
    if ( $referred_user != '' ) {
        $productlevel              = empty( $variationid ) ? get_post_meta( $productid , '_rewardsystemreferralcheckboxvalue' , true ) : get_post_meta( $variationid , '_enable_referral_reward_points' , true ) ;
        $productlevelrewardtype    = empty( $variationid ) ? get_post_meta( $productid , '_referral_rewardsystem_options' , true ) : get_post_meta( $variationid , '_select_referral_reward_rule' , true ) ;
        $productlevelrewardpoints  = empty( $variationid ) ? get_post_meta( $productid , '_referralrewardsystempoints' , true ) : get_post_meta( $variationid , '_referral_reward_points' , true ) ;
        $productlevelrewardpercent = empty( $variationid ) ? get_post_meta( $productid , '_referralrewardsystempercent' , true ) : get_post_meta( $variationid , '_referral_reward_percent' , true ) ;
        if ( $getting_referrer == 'yes' ) {
            $productlevel              = empty( $variationid ) ? get_post_meta( $productid , '_rewardsystemreferralcheckboxvalue' , true ) : get_post_meta( $variationid , '_enable_referral_reward_points' , true ) ;
            $productlevelrewardtype    = empty( $variationid ) ? get_post_meta( $productid , '_referral_rewardsystem_options_getrefer' , true ) : get_post_meta( $variationid , '_select_referral_reward_rule_getrefer' , true ) ;
            $productlevelrewardpoints  = empty( $variationid ) ? get_post_meta( $productid , '_referralrewardsystempoints_for_getting_referred' , true ) : get_post_meta( $variationid , '_referral_reward_points_getting_refer' , true ) ;
            $productlevelrewardpercent = empty( $variationid ) ? get_post_meta( $productid , '_referralrewardsystempercent_for_getting_referred' , true ) : get_post_meta( $variationid , '_referral_reward_percent_getting_refer' , true ) ;
        }
        $regularprice    = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
        $convertedpoints = convert_percent_value_as_points( $productlevelrewardpercent , $regularprice ) ;
        if ( (get_option( 'rs_restrict_referral_reward' ) == 'yes' ) ) {
            $convertedpoints = $convertedpoints / $itemquantity ;
            $itemquantity    = 1 ;
        }
    } elseif ( $getting_referrer == 'yes' ) {
        $productlevel              = empty( $variationid ) ? get_post_meta( $productid , '_rewardsystemreferralcheckboxvalue' , true ) : get_post_meta( $variationid , '_enable_referral_reward_points' , true ) ;
        $productlevelrewardtype    = empty( $variationid ) ? get_post_meta( $productid , '_referral_rewardsystem_options_getrefer' , true ) : get_post_meta( $variationid , '_select_referral_reward_rule_getrefer' , true ) ;
        $productlevelrewardpoints  = empty( $variationid ) ? get_post_meta( $productid , '_referralrewardsystempoints_for_getting_referred' , true ) : get_post_meta( $variationid , '_referral_reward_points_getting_refer' , true ) ;
        $productlevelrewardpercent = empty( $variationid ) ? get_post_meta( $productid , '_referralrewardsystempercent_for_getting_referred' , true ) : get_post_meta( $variationid , '_referral_reward_percent_getting_refer' , true ) ;
        $regularprice              = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
        $convertedpoints           = convert_percent_value_as_points( $productlevelrewardpercent , $regularprice ) ;
    } elseif ( $socialreward == 'yes' ) {
        $newarray                  = get_social_rewardpoints( $productid , $rewardfor , '1' ) ;
        $productlevel              = $newarray[ 'enable_level' ] ;
        $productlevelrewardtype    = $newarray[ 'rewardtype' ] ;
        $productlevelrewardpoints  = $newarray[ 'rewardpoints' ] ;
        $productlevelrewardpercent = $newarray[ 'rewardpercent' ] ;
        $regularprice              = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
        $convertedpoints           = convert_percent_value_as_points( $productlevelrewardpercent , $regularprice ) ;
    } else {
        $productlevel              = empty( $variationid ) ? get_post_meta( $productid , '_rewardsystemcheckboxvalue' , true ) : get_post_meta( $variationid , '_enable_reward_points' , true ) ;
        $productlevelrewardtype    = empty( $variationid ) ? get_post_meta( $productid , '_rewardsystem_options' , true ) : get_post_meta( $variationid , '_select_reward_rule' , true ) ;
        $productlevelrewardpoints  = empty( $variationid ) ? get_post_meta( $productid , '_rewardsystempoints' , true ) : get_post_meta( $variationid , '_reward_points' , true ) ;
        $productlevelrewardpercent = empty( $variationid ) ? get_post_meta( $productid , '_rewardsystempercent' , true ) : get_post_meta( $variationid , '_reward_percent' , true ) ;
        $regularprice              = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
        $convertedpoints           = convert_percent_value_as_points( $productlevelrewardpercent , $regularprice ) ;
        if ( get_option( 'rs_restrict_reward' ) == 'yes' ) {
            $convertedpoints = $convertedpoints / $itemquantity ;
            $itemquantity    = 1 ;
        }
    }
    if ( ($productlevel == 'yes') || ($productlevel == '1') ) {
        if ( $productlevelrewardtype == '1' && $productlevelrewardpoints != '' ) {
            return ( $checklevel == 'yes' ) ? '1' : ($productlevelrewardpoints * $itemquantity) ;
        } elseif ( $productlevelrewardtype == '2' && $productlevelrewardpercent != '' ) {
            return ( $checklevel == 'yes' ) ? '1' : $convertedpoints ;
        }
        return is_category_level( $productid , $variationid , $item , $checklevel , $referred_user , $getting_referrer , $socialreward , $rewardfor , $payment_price , $itemquantity ) ;
    }
}

function is_category_level( $productid , $variationid , $item , $checklevel , $referred_user , $getting_referrer , $socialreward , $rewardfor , $payment_price , $itemquantity ) {
    //Category Level
    $term              = get_the_terms( $productid , 'product_cat' ) ;
    $cat_level_enabled = array() ;
    $cat_level_point   = array() ;
    $cat_level_percent = array() ;
    if ( srp_check_is_array( $term ) ) {
        $categorylist = wp_get_post_terms( $productid , 'product_cat' ) ;
        $getcount     = count( $categorylist ) ;
        foreach ( $term as $terms ) {
            $termid = $terms->term_id ;
            if ( $referred_user != '' ) {
                $categorylevel              = srp_term_meta( $termid , 'enable_referral_reward_system_category' ) ;
                $categorylevelrewardtype    = srp_term_meta( $termid , 'referral_enable_rs_rule' ) ;
                $categorylevelrewardpoints  = srp_term_meta( $termid , 'referral_rs_category_points' ) ;
                $categorylevelrewardpercent = srp_term_meta( $termid , 'referral_rs_category_percent' ) ;
                if ( $getting_referrer == 'yes' ) {
                    $categorylevel              = srp_term_meta( $termid , 'enable_referral_reward_system_category' ) ;
                    $categorylevelrewardtype    = srp_term_meta( $termid , 'referral_enable_rs_rule_refer' ) ;
                    $categorylevelrewardpoints  = srp_term_meta( $termid , 'referral_rs_category_points_get_refered' ) ;
                    $categorylevelrewardpercent = srp_term_meta( $termid , 'referral_rs_category_percent_get_refer' ) ;
                }
                $regularprice    = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
                $convertedpoints = convert_percent_value_as_points( $categorylevelrewardpercent , $regularprice ) ;
                if ( (get_option( 'rs_restrict_referral_reward' ) == 'yes' ) ) {
                    $convertedpoints = $convertedpoints / $itemquantity ;
                    $itemquantity    = 1 ;
                }
            } elseif ( $getting_referrer == 'yes' ) {
                $categorylevel              = srp_term_meta( $termid , 'enable_referral_reward_system_category' ) ;
                $categorylevelrewardtype    = srp_term_meta( $termid , 'referral_enable_rs_rule_refer' ) ;
                $categorylevelrewardpoints  = srp_term_meta( $termid , 'referral_rs_category_points_get_refered' ) ;
                $categorylevelrewardpercent = srp_term_meta( $termid , 'referral_rs_category_percent_get_refer' ) ;
                $regularprice               = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
                $convertedpoints            = convert_percent_value_as_points( $categorylevelrewardpercent , $regularprice ) ;
            } elseif ( $socialreward == 'yes' ) {
                $newarray                   = get_social_rewardpoints( $productid , $rewardfor , '2' , $termid ) ;
                $categorylevel              = $newarray[ 'enable_level' ] ;
                $categorylevelrewardtype    = $newarray[ 'rewardtype' ] ;
                $categorylevelrewardpoints  = $newarray[ 'rewardpoints' ] ;
                $categorylevelrewardpercent = $newarray[ 'rewardpercent' ] ;
                $regularprice               = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
                $convertedpoints            = convert_percent_value_as_points( $categorylevelrewardpercent , $regularprice ) ;
            } else {
                $categorylevel              = srp_term_meta( $termid , 'enable_reward_system_category' ) ;
                $categorylevelrewardtype    = srp_term_meta( $termid , 'enable_rs_rule' ) ;
                $categorylevelrewardpoints  = srp_term_meta( $termid , 'rs_category_points' ) ;
                $categorylevelrewardpercent = srp_term_meta( $termid , 'rs_category_percent' ) ;
                $regularprice               = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
                $convertedpoints            = convert_percent_value_as_points( $categorylevelrewardpercent , $regularprice ) ;
                if ( get_option( 'rs_restrict_reward' ) == 'yes' ) {
                    $convertedpoints = $convertedpoints / $itemquantity ;
                    $itemquantity    = 1 ;
                }
            }
            if ( $getcount >= 1 ) {
                if ( ($categorylevel == 'yes' ) ) {
                    if ( ($categorylevelrewardtype == '1') && $categorylevelrewardpoints != '' ) {
                        if ( $checklevel == 'yes' ) {
                            $cat_level_enabled[] = '2' ;
                        } else {
                            $quantity          = get_option( 'rs_restrict_reward' ) == 'yes' ? 1 : $itemquantity ;
                            $cat_level_point[] = $categorylevelrewardpoints * $quantity ;
                        }
                    } else if ( ($categorylevelrewardtype == '2') && $categorylevelrewardpercent != '' ) {
                        if ( $checklevel == 'yes' ) {
                            $cat_level_enabled[] = '2' ;
                        } else {
                            $cat_level_point[] = $convertedpoints ;
                        }
                    }
                }
            }
        }
        if ( ! empty( $cat_level_point ) ) {
            return max( $cat_level_point ) ;
        } elseif ( ! empty( $cat_level_enabled ) ) {
            return '2' ;
        }
    }
    if ( empty( $cat_level_point ) || empty( $cat_level_enabled ) ) {
        return is_global_level( $productid , $variationid , $item , $checklevel , $referred_user , $getting_referrer , $socialreward , $rewardfor , $payment_price , $itemquantity ) ;
    }
}

function is_global_level( $productid , $variationid , $item , $checklevel , $referred_user , $getting_referrer , $socialreward , $rewardfor , $payment_price , $itemquantity ) {
    //Global Level
    if ( $referred_user != '' ) {
        $global_enable        = get_option( 'rs_global_enable_disable_sumo_referral_reward' ) ;
        $global_reward_type   = get_option( 'rs_global_referral_reward_type' ) ;
        $global_rewardpoints  = get_option( 'rs_global_referral_reward_point' ) ;
        $global_rewardpercent = get_option( 'rs_global_referral_reward_percent' ) ;
        if ( $getting_referrer == 'yes' ) {
            $global_enable        = get_option( 'rs_global_enable_disable_sumo_referral_reward' ) ;
            $global_reward_type   = get_option( 'rs_global_referral_reward_type_refer' ) ;
            $global_rewardpoints  = get_option( 'rs_global_referral_reward_point_get_refer' ) ;
            $global_rewardpercent = get_option( 'rs_global_referral_reward_percent_get_refer' ) ;
        }
        $regularprice    = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
        $convertedpoints = convert_percent_value_as_points( $global_rewardpercent , $regularprice ) ;
        if ( (get_option( 'rs_restrict_referral_reward' ) == 'yes' ) ) {
            $convertedpoints = $convertedpoints / $itemquantity ;
            $itemquantity    = 1 ;
        }
    } elseif ( $getting_referrer == 'yes' ) {
        $global_enable        = get_option( 'rs_global_enable_disable_sumo_referral_reward' ) ;
        $global_reward_type   = get_option( 'rs_global_referral_reward_type_refer' ) ;
        $global_rewardpoints  = get_option( 'rs_global_referral_reward_point_get_refer' ) ;
        $global_rewardpercent = get_option( 'rs_global_referral_reward_percent_get_refer' ) ;
        $regularprice         = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
        $convertedpoints      = convert_percent_value_as_points( $global_rewardpercent , $regularprice ) ;
    } elseif ( $socialreward == 'yes' ) {
        $newarray             = get_social_rewardpoints( $productid , $rewardfor , '3' ) ;
        $global_enable        = $newarray[ 'enable_level' ] ;
        $global_reward_type   = $newarray[ 'rewardtype' ] ;
        $global_rewardpoints  = $newarray[ 'rewardpoints' ] ;
        $global_rewardpercent = $newarray[ 'rewardpercent' ] ;
        $regularprice         = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
        $convertedpoints      = convert_percent_value_as_points( $global_rewardpercent , $regularprice ) ;
    } else {
        $global_enable        = get_option( 'rs_global_enable_disable_sumo_reward' ) ;
        $global_reward_type   = get_option( 'rs_global_reward_type' ) ;
        $global_rewardpoints  = get_option( 'rs_global_reward_points' ) ;
        $global_rewardpercent = get_option( 'rs_global_reward_percent' ) ;
        $regularprice         = get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) ;
        $convertedpoints      = convert_percent_value_as_points( $global_rewardpercent , $regularprice ) ;
        if ( get_option( 'rs_restrict_reward' ) == 'yes' ) {
            $convertedpoints = $convertedpoints / $itemquantity ;
            $itemquantity    = 1 ;
        }
    }

    if ( $global_enable == '1' ) {
        if ( $global_reward_type == '1' ) {
            if ( $global_rewardpoints != '' ) {
                if ( $checklevel == 'yes' ) {
                    return '3' ;
                } else {
                    $quantity = get_option( 'rs_restrict_reward' ) == 'yes' ? 1 : $itemquantity ;
                    return $global_rewardpoints * $quantity ;
                }
            }
        } else {
            if ( $global_rewardpercent != '' )
                return ( $checklevel == 'yes' ) ? '3' : $convertedpoints ;
        }
    }
    return 0 ;
}

function convert_percent_value_as_points( $rewardpercent , $regularprice ) {
    $Points = (( float ) $rewardpercent / 100) * $regularprice ;
    return earn_point_conversion( $Points ) ;
}

function get_social_rewardpoints( $productid , $rewardfor , $level , $termid = '' ) {
    $productlevel  = get_post_meta( $productid , '_socialrewardsystemcheckboxvalue' , true ) ;
    $categorylevel = srp_term_meta( $termid , 'enable_social_reward_system_category' ) ;
    $global_enable = get_option( 'rs_global_social_enable_disable_reward' ) ;
    if ( $rewardfor == 'instagram' ) {
        if ( $level == '1' ) {
            $productlevelrewardtype    = get_post_meta( $productid , '_social_rewardsystem_options_instagram' , true ) ;
            $productlevelrewardpoints  = get_post_meta( $productid , '_socialrewardsystempoints_instagram' , true ) ;
            $productlevelrewardpercent = get_post_meta( $productid , '_socialrewardsystempercent_instagram' , true ) ;
            return array( 'enable_level' => $productlevel , 'rewardtype' => $productlevelrewardtype , 'rewardpoints' => $productlevelrewardpoints , 'rewardpercent' => $productlevelrewardpercent ) ;
        } elseif ( $level == '2' ) {
            $categorylevelrewardtype     = srp_term_meta( $termid , 'social_instagram_enable_rs_rule' ) ;
            $categorylevelrewardpoints   = srp_term_meta( $termid , 'social_instagram_rs_category_points' ) ;
            $categorylevelrewardpercents = srp_term_meta( $termid , 'social_instagram_rs_category_percent' ) ;
            return array( 'enable_level' => $categorylevel , 'rewardtype' => $categorylevelrewardtype , 'rewardpoints' => $categorylevelrewardpoints , 'rewardpercent' => $categorylevelrewardpercents ) ;
        } else {
            $global_reward_type    = get_option( 'rs_global_social_reward_type_instagram' ) ;
            $global_reward_points  = get_option( 'rs_global_social_instagram_reward_points' ) ;
            $global_reward_percent = get_option( 'rs_global_social_instagram_reward_percent' ) ;
            return array( 'enable_level' => $global_enable , 'rewardtype' => $global_reward_type , 'rewardpoints' => $global_reward_points , 'rewardpercent' => $global_reward_percent ) ;
        }
    } elseif ( $rewardfor == 'twitter_follow' ) {
        if ( $level == '1' ) {
            $gettype    = get_post_meta( $productid , '_social_rewardsystem_options_twitter_follow' , true ) ;
            $getpoints  = get_post_meta( $productid , '_socialrewardsystempoints_twitter_follow' , true ) ;
            $getpercent = get_post_meta( $productid , '_socialrewardsystempercent_twitter_follow' , true ) ;
            return array( 'enable_level' => $productlevel , 'rewardtype' => $gettype , 'rewardpoints' => $getpoints , 'rewardpercent' => $getpercent ) ;
        } elseif ( $level == '2' ) {
            $categorylevelrewardtype     = srp_term_meta( $termid , 'social_twitter_follow_enable_rs_rule' ) ;
            $categorylevelrewardpoints   = srp_term_meta( $termid , 'social_twitter_follow_rs_category_points' ) ;
            $categorylevelrewardpercents = srp_term_meta( $termid , 'social_twitter_follow_rs_category_percent' ) ;
            return array( 'enable_level' => $categorylevel , 'rewardtype' => $categorylevelrewardtype , 'rewardpoints' => $categorylevelrewardpoints , 'rewardpercent' => $categorylevelrewardpercents ) ;
        } else {
            $global_reward_type    = get_option( 'rs_global_social_reward_type_twitter_follow' ) ;
            $global_reward_points  = get_option( 'rs_global_social_twitter_follow_reward_points' ) ;
            $global_reward_percent = get_option( 'rs_global_social_twitter_follow_reward_percent' ) ;
            return array( 'enable_level' => $global_enable , 'rewardtype' => $global_reward_type , 'rewardpoints' => $global_reward_points , 'rewardpercent' => $global_reward_percent ) ;
        }
    } elseif ( $rewardfor == 'fb_like' ) {
        if ( $level == '1' ) {
            $gettype    = get_post_meta( $productid , '_social_rewardsystem_options_facebook' , true ) ;
            $getpoints  = get_post_meta( $productid , '_socialrewardsystempoints_facebook' , true ) ;
            $getpercent = get_post_meta( $productid , '_socialrewardsystempercent_facebook' , true ) ;
            return array( 'enable_level' => $productlevel , 'rewardtype' => $gettype , 'rewardpoints' => $getpoints , 'rewardpercent' => $getpercent ) ;
        } elseif ( $level == '2' ) {
            $categorylevelrewardtype     = srp_term_meta( $termid , 'social_facebook_enable_rs_rule' ) ;
            $categorylevelrewardpoints   = srp_term_meta( $termid , 'social_facebook_rs_category_points' ) ;
            $categorylevelrewardpercents = srp_term_meta( $termid , 'social_facebook_rs_category_percent' ) ;
            return array( 'enable_level' => $categorylevel , 'rewardtype' => $categorylevelrewardtype , 'rewardpoints' => $categorylevelrewardpoints , 'rewardpercent' => $categorylevelrewardpercents ) ;
        } else {
            $global_reward_type    = get_option( 'rs_global_social_reward_type_facebook' ) ;
            $global_reward_points  = get_option( 'rs_global_social_facebook_reward_points' ) ;
            $global_reward_percent = get_option( 'rs_global_social_facebook_reward_percent' ) ;
            return array( 'enable_level' => $global_enable , 'rewardtype' => $global_reward_type , 'rewardpoints' => $global_reward_points , 'rewardpercent' => $global_reward_percent ) ;
        }
    } elseif ( $rewardfor == 'fb_share' ) {
        if ( $level == '1' ) {
            $gettype    = get_post_meta( $productid , '_social_rewardsystem_options_facebook_share' , true ) ;
            $getpoints  = get_post_meta( $productid , '_socialrewardsystempoints_facebook_share' , true ) ;
            $getpercent = get_post_meta( $productid , '_socialrewardsystempercent_facebook_share' , true ) ;
            return array( 'enable_level' => $productlevel , 'rewardtype' => $gettype , 'rewardpoints' => $getpoints , 'rewardpercent' => $getpercent ) ;
        } elseif ( $level == '2' ) {
            $categorylevelrewardtype     = srp_term_meta( $termid , 'social_facebook_share_enable_rs_rule' ) ;
            $categorylevelrewardpoints   = srp_term_meta( $termid , 'social_facebook_share_rs_category_points' ) ;
            $categorylevelrewardpercents = srp_term_meta( $termid , 'social_facebook_share_rs_category_percent' ) ;
            return array( 'enable_level' => $categorylevel , 'rewardtype' => $categorylevelrewardtype , 'rewardpoints' => $categorylevelrewardpoints , 'rewardpercent' => $categorylevelrewardpercents ) ;
        } else {
            $global_reward_type    = get_option( 'rs_global_social_reward_type_facebook_share' ) ;
            $global_reward_points  = get_option( 'rs_global_social_facebook_share_reward_points' ) ;
            $global_reward_percent = get_option( 'rs_global_social_facebook_share_reward_percent' ) ;
            return array( 'enable_level' => $global_enable , 'rewardtype' => $global_reward_type , 'rewardpoints' => $global_reward_points , 'rewardpercent' => $global_reward_percent ) ;
        }
    } elseif ( $rewardfor == 'twitter_tweet' ) {
        if ( $level == '1' ) {
            $gettype    = get_post_meta( $productid , '_social_rewardsystem_options_twitter' , true ) ;
            $getpoints  = get_post_meta( $productid , '_socialrewardsystempoints_twitter' , true ) ;
            $getpercent = get_post_meta( $productid , '_socialrewardsystempercent_twitter' , true ) ;
            return array( 'enable_level' => $productlevel , 'rewardtype' => $gettype , 'rewardpoints' => $getpoints , 'rewardpercent' => $getpercent ) ;
        } elseif ( $level == '2' ) {
            $categorylevelrewardtype     = srp_term_meta( $termid , 'social_twitter_enable_rs_rule' ) ;
            $categorylevelrewardpoints   = srp_term_meta( $termid , 'social_twitter_rs_category_points' ) ;
            $categorylevelrewardpercents = srp_term_meta( $termid , 'social_twitter_rs_category_percent' ) ;
            return array( 'enable_level' => $categorylevel , 'rewardtype' => $categorylevelrewardtype , 'rewardpoints' => $categorylevelrewardpoints , 'rewardpercent' => $categorylevelrewardpercents ) ;
        } else {
            $global_reward_type    = get_option( 'rs_global_social_reward_type_twitter' ) ;
            $global_reward_points  = get_option( 'rs_global_social_twitter_reward_points' ) ;
            $global_reward_percent = get_option( 'rs_global_social_twitter_reward_percent' ) ;
            return array( 'enable_level' => $global_enable , 'rewardtype' => $global_reward_type , 'rewardpoints' => $global_reward_points , 'rewardpercent' => $global_reward_percent ) ;
        }
    } elseif ( $rewardfor == 'g_plus' ) {
        if ( $level == '1' ) {
            $gettype    = get_post_meta( $productid , '_social_rewardsystem_options_google' , true ) ;
            $getpoints  = get_post_meta( $productid , '_socialrewardsystempoints_google' , true ) ;
            $getpercent = get_post_meta( $productid , '_socialrewardsystempercent_google' , true ) ;
            return array( 'enable_level' => $productlevel , 'rewardtype' => $gettype , 'rewardpoints' => $getpoints , 'rewardpercent' => $getpercent ) ;
        } elseif ( $level == '2' ) {
            $categorylevelrewardtype     = srp_term_meta( $termid , 'social_google_enable_rs_rule' ) ;
            $categorylevelrewardpoints   = srp_term_meta( $termid , 'social_google_rs_category_points' ) ;
            $categorylevelrewardpercents = srp_term_meta( $termid , 'social_google_rs_category_percent' ) ;
            return array( 'enable_level' => $categorylevel , 'rewardtype' => $categorylevelrewardtype , 'rewardpoints' => $categorylevelrewardpoints , 'rewardpercent' => $categorylevelrewardpercents ) ;
        } else {
            $global_reward_type    = get_option( 'rs_global_social_reward_type_google' ) ;
            $global_reward_points  = get_option( 'rs_global_social_google_reward_points' ) ;
            $global_reward_percent = get_option( 'rs_global_social_google_reward_percent' ) ;
            return array( 'enable_level' => $global_enable , 'rewardtype' => $global_reward_type , 'rewardpoints' => $global_reward_points , 'rewardpercent' => $global_reward_percent ) ;
        }
    } elseif ( $rewardfor == 'vk_like' ) {
        if ( $level == '1' ) {
            $gettype    = get_post_meta( $productid , '_social_rewardsystem_options_vk' , true ) ;
            $getpoints  = get_post_meta( $productid , '_socialrewardsystempoints_vk' , true ) ;
            $getpercent = get_post_meta( $productid , '_socialrewardsystempercent_vk' , true ) ;
            return array( 'enable_level' => $productlevel , 'rewardtype' => $gettype , 'rewardpoints' => $getpoints , 'rewardpercent' => $getpercent ) ;
        } elseif ( $level == '2' ) {
            $categorylevelrewardtype     = srp_term_meta( $termid , 'social_vk_enable_rs_rule' ) ;
            $categorylevelrewardpoints   = srp_term_meta( $termid , 'social_vk_rs_category_points' ) ;
            $categorylevelrewardpercents = srp_term_meta( $termid , 'social_vk_rs_category_percent' ) ;
            return array( 'enable_level' => $categorylevel , 'rewardtype' => $categorylevelrewardtype , 'rewardpoints' => $categorylevelrewardpoints , 'rewardpercent' => $categorylevelrewardpercents ) ;
        } else {
            $global_reward_type    = get_option( 'rs_global_social_reward_type_vk' ) ;
            $global_reward_points  = get_option( 'rs_global_social_vk_reward_points' ) ;
            $global_reward_percent = get_option( 'rs_global_social_vk_reward_percent' ) ;
            return array( 'enable_level' => $global_enable , 'rewardtype' => $global_reward_type , 'rewardpoints' => $global_reward_points , 'rewardpercent' => $global_reward_percent ) ;
        }
    } elseif ( $rewardfor == 'ok_follow' ) {
        if ( $level == '1' ) {
            $gettype    = get_post_meta( $productid , '_social_rewardsystem_options_ok_follow' , true ) ;
            $getpoints  = get_post_meta( $productid , '_socialrewardsystempoints_ok_follow' , true ) ;
            $getpercent = get_post_meta( $productid , '_socialrewardsystempercent_ok_follow' , true ) ;
            return array( 'enable_level' => $productlevel , 'rewardtype' => $gettype , 'rewardpoints' => $getpoints , 'rewardpercent' => $getpercent ) ;
        } elseif ( $level == '2' ) {
            $categorylevelrewardtype     = srp_term_meta( $termid , 'social_ok_follow_enable_rs_rule' ) ;
            $categorylevelrewardpoints   = srp_term_meta( $termid , 'social_ok_follow_rs_category_points' ) ;
            $categorylevelrewardpercents = srp_term_meta( $termid , 'social_ok_follow_rs_category_percent' ) ;
            return array( 'enable_level' => $categorylevel , 'rewardtype' => $categorylevelrewardtype , 'rewardpoints' => $categorylevelrewardpoints , 'rewardpercent' => $categorylevelrewardpercents ) ;
        } else {
            $global_reward_type    = get_option( 'rs_global_social_reward_type_ok_follow' ) ;
            $global_reward_points  = get_option( 'rs_global_social_ok_follow_reward_points' ) ;
            $global_reward_percent = get_option( 'rs_global_social_ok_follow_reward_percent' ) ;
            return array( 'enable_level' => $global_enable , 'rewardtype' => $global_reward_type , 'rewardpoints' => $global_reward_points , 'rewardpercent' => $global_reward_percent ) ;
        }
    }
}

if ( ! function_exists( 'srp_formatted_price' ) ) {

    function srp_formatted_price( $price ) {
        return function_exists( 'wc_price' ) ? wc_price( $price ) : woocommerce_price( $price ) ;
    }

}

if ( ! function_exists( 'srp_order_obj' ) ) {

    function srp_order_obj( $order ) {
        if ( is_object( $order ) && ! empty( $order ) ) {
            global $woocommerce ;
            if ( ( float ) $woocommerce->version >= ( float ) '3.0' ) {
                $order_id      = $order->get_id() ;
                $post_status   = $order->get_status() ;
                $order_user_id = $order->get_user_id() ;
                if ( $order->get_parent_id() === 0 ) {
                    $payment_method       = $order->get_payment_method() ;
                    $payment_method_title = $order->get_payment_method_title() ;
                } else {
                    $payment_method       = '' ;
                    $payment_method_title = '' ;
                }
            } else {
                $order_id      = $order->id ;
                $post_status   = $order->post_status ;
                $order_user_id = $order->user_id ;
                if ( $order->parent_id === 0 ) {
                    $payment_method       = $order->payment_method ;
                    $payment_method_title = $order->payment_method_title ;
                } else {
                    $payment_method       = '' ;
                    $payment_method_title = '' ;
                }
            }
            $first_name = get_post_meta( $order_id , '_billing_first_name' , true ) ;
            $new_array  = array(
                'order_id'             => $order_id ,
                'order_status'         => $post_status ,
                'order_userid'         => $order_user_id ,
                'payment_method'       => $payment_method ,
                'payment_method_title' => $payment_method_title ,
                'first_name'           => $first_name
                    ) ;
            return $new_array ;
        }
    }

}

if ( ! function_exists( 'srp_coupon_obj' ) ) {

    function srp_coupon_obj( $object ) {
        if ( is_object( $object ) && ! empty( $object ) ) {
            global $woocommerce ;
            if ( ( float ) $woocommerce->version >= ( float ) '3.0' ) {
                $coupon_id          = $object->get_id() ;
                $coupon_code        = $object->get_code() ;
                $coupon_amnt        = $object->get_amount() ;
                $coupon_product_ids = $object->get_product_ids() ;
                $discount_type      = $object->get_discount_type() ;
                $product_cat        = $object->get_product_categories() ;
            } else {
                $coupon_id          = $object->id ;
                $coupon_code        = $object->code ;
                $coupon_amnt        = $object->coupon_amount ;
                $coupon_product_ids = $object->product_ids ;
                $discount_type      = $object->discount_type ;
                $product_cat        = $object->product_categories ;
            }
            $new_array = array(
                'coupon_id'          => $coupon_id ,
                'coupon_code'        => $coupon_code ,
                'coupon_amount'      => $coupon_amnt ,
                'product_ids'        => $coupon_product_ids ,
                'discount_type'      => $discount_type ,
                'product_categories' => $product_cat
                    ) ;
            return $new_array ;
        }
    }

}

function check_whether_hoicker_is_active() {
    if ( class_exists( 'HR_Wallet' ) )
        return true ;

    return false ;
}

function is_sumo_booking_active( $pdt_id ) {
    if ( class_exists( 'SUMO_Bookings' ) )
        if ( function_exists( 'is_sumo_bookings_product' ) && (is_sumo_bookings_product( $pdt_id )) )
            return true ;

    return false ;
}

add_filter( 'sumo_bookings_calculated_format_price' , 'point_price_format_for_booking_product' , 10 , 3 ) ;

function point_price_format_for_booking_product( $format_price , $booking_price , $product_id ) {

    if ( get_option( 'rs_point_price_activated' ) != 'yes' )
        return $format_price ;

    if ( ! is_sumo_booking_active( $product_id ) )
        return $format_price ;

    if ( get_post_meta( $product_id , '_rewardsystem_enable_point_price' , true ) == 'yes' && get_option( 'rs_enable_product_category_level_for_points_price' ) == 'yes' ) {
        $point_price_label = get_option( 'rs_label_for_point_value' ) ;
        $pixel             = get_option( 'rs_pixel_val' ) ;
        $price             = calculate_point_price_for_products( $product_id ) ;
        if ( get_post_meta( $product_id , '_rewardsystem_enable_point_price_type' , true ) == 2 ) {
            return $price[ $product_id ] . $point_price_label ;
        } else {
            if ( get_option( 'rs_sufix_prefix_point_price_label' ) == 2 ) {
                return $format_price . '/' . "{$price[ $product_id ]}<span style='margin-left:{$pixel}px;'>{$point_price_label}</span>" ;
            } else {
                return $format_price . '/' . "{$point_price_label}<span style='margin-left:{$pixel}px;'>{$price[ $product_id ]}</span>" ;
            }
        }
    }
    return $format_price ;
}

function rs_alter_from_email_of_woocommerce( $email , $obj ) {
    if ( FPRewardSystem::$rs_from_email_address )
        return '<' . FPRewardSystem::$rs_from_email_address . '>' ;

    return $email ;
}

function rs_alter_from_name_of_woocommerce( $name , $obj ) {
    if ( FPRewardSystem::$rs_from_name )
        return FPRewardSystem::$rs_from_name ;

    return $name ;
}

function rs_get_next_menu() {
    if ( get_option( 'rs_menu_restriction_based_on_user_role' ) == 'yes' ) {
        $tabtoshow = RSAdminAssets::menu_restriction_based_on_user_role() ;
        return reset( $tabtoshow ) ;
    }
}

function rs_get_current_user_role() {
    global $wp_roles ;
    $UserRole = array() ;
    foreach ( $wp_roles->role_names as $value => $key ) {
        $user     = new WP_User( get_current_user_id() ) ;
        if ( srp_check_is_array( $user->roles ) )
            $UserRole = $user->roles ;
    }
    return $UserRole ;
}

function award_points_for_product_purchase_based_on_cron( $order_id ) {
    $order         = new WC_Order( $order_id ) ;
    $orderid       = srp_order_obj( $order ) ;
    $orderstatus   = $orderid[ 'order_status' ] ;
    $replacestatus = str_replace( 'wc-' , '' , $orderstatus ) ;
    $status        = get_option( 'rs_order_status_control' ) ;
    if ( in_array( $replacestatus , $status ) ) {
        $new_obj                     = new RewardPointsOrder( $order_id , $apply_previous_order_points = 'no' ) ;
        $new_obj->update_earning_points_for_user() ;
    }
}

if ( ! function_exists( 'order_total_in_order_detail' ) ) {

    function order_total_in_order_detail( $total , $order ) {
        if ( ! is_user_logged_in() )
            return $total ;

        if ( get_option( 'rs_point_price_activated' ) != 'yes' )
            return $total ;

        if ( get_option( 'rs_enable_disable_point_priceing' ) == 2 )
            return $total ;

        $OrderObj = srp_order_obj( $order ) ;
        $Gateway  = get_post_meta( $OrderObj[ 'order_id' ] , '_payment_method' , true ) ;
        if ( $Gateway != 'reward_gateway' )
            return $total ;

        $DiscountAmnt = array() ;
        $OtherValue   = array() ;
        $Points       = array() ;
        $CouponData   = $order->get_items( array( 'coupon' ) ) ;
        foreach ( $CouponData as $Coupon ) {
            $DiscountAmnt[] = $Coupon[ 'discount_amount' ] ;
        }
        $CouponAmnt     = array_sum( $DiscountAmnt ) ;
        $tax_display    = get_option( 'woocommerce_tax_display_cart' ) ;
        $excl_tax_total = ($tax_display == "excl") ? $order->get_total_tax() : 0 ;
        $ReplaceLabel   = str_replace( "/" , "" , get_option( 'rs_label_for_point_value' ) ) ;
        foreach ( $order->get_items()as $item ) {
            $ProductId             = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
            $PointPriceData        = calculate_point_price_for_products( $ProductId ) ;
            $CheckIfBundledProduct = isset( $item[ 'bundled_by' ] ) ? $item[ 'bundled_by' ] : 0 ;
            if ( $PointPriceData[ $ProductId ] != '' && $CheckIfBundledProduct == null ) {
                $Points[] = $PointPriceData[ $ProductId ] * $item[ 'qty' ] ;
            } else {
                if ( 'incl' == $tax_display ) {
                    $LineTotal = $item[ 'line_subtotal' ] + $item[ 'line_subtotal_tax' ] ;
                } else {
                    $LineTotal = $item[ 'line_subtotal' ] ;
                }
                $OtherValue[] = redeem_point_conversion( $LineTotal , $OrderObj[ 'order_userid' ] ) ;
            }
        }

        $fee_total = 0 ;
        // The fee total amount
        foreach ( $order->get_items( 'fee' ) as $item_fee ) {
            if ( 'incl' == $tax_display ) {
                $fee_total = $fee_total + $item_fee->get_total() + $item_fee->get_total_tax() ;
            } else {
                $fee_total += $item_fee->get_total() ;
            }
        }
        $TotalPoints  = array_sum( $Points ) + array_sum( $OtherValue ) - $CouponAmnt ;
        $shipping_tax = $tax_display == 'excl' ? $order->get_total_shipping() : $order->get_total_shipping() + $order->get_shipping_tax() ;
        $TotalPoints  = round_off_type( $TotalPoints ) + $shipping_tax + $excl_tax_total + $fee_total ;
        if ( get_option( 'rs_sufix_prefix_point_price_label' ) == '1' ) {
            $product_price = "{$ReplaceLabel}<span style='margin-left:{'" . get_option( 'rs_pixel_val' ) . "'}px;'>{$TotalPoints}</span>" ;
        } else {
            $product_price = "{$TotalPoints}<span style='margin-left:{'" . get_option( 'rs_pixel_val' ) . "'}px;'>{$ReplaceLabel}</span>" ;
        }
        return $product_price ;
    }

    add_filter( 'woocommerce_get_formatted_order_total' , 'order_total_in_order_detail' , 10 , 2 ) ;
}

if ( ! function_exists( 'rs_redeemed_point_in_thank_you_page' ) ) {

    function rs_redeemed_point_in_thank_you_page( $total_rows , $order , $tax_display ) {
        $OrderObj           = srp_order_obj( $order ) ;
        $OrderId            = $OrderObj[ 'order_id' ] ;
        $UserID             = $OrderObj[ 'order_userid' ] ;
        $UserData           = get_user_by( 'id' , $UserID ) ;
        $UserName           = is_object( $UserData ) ? $UserData->user_login : 'Guest' ;
        $SumoCouponName     = 'sumo_' . strtolower( $UserName ) ;
        $AutoSumoCouponName = 'auto_redeem_' . strtolower( $UserName ) ;
        $CouponsUsedInOrder = $order->get_items( array( 'coupon' ) ) ;
        if ( ! srp_check_is_array( $CouponsUsedInOrder ) )
            return $total_rows ;

        $CouponData = array() ;
        foreach ( $CouponsUsedInOrder as $value ) {
            $CouponData[ $value[ 'code' ] ] = ( $tax_display == "incl" ) ? ($value[ 'discount' ] + $value[ "discount_tax" ]) : $value[ 'discount' ] ;
        }

        if ( ! srp_check_is_array( $CouponData ) )
            return $total_rows ;

        if ( ! array_key_exists( $SumoCouponName , $CouponData ) && ! array_key_exists( $AutoSumoCouponName , $CouponData ) )
            return $total_rows ;

        unset( $total_rows[ 'discount' ] ) ;
        $RedeemedPoints    = isset( $CouponData[ $SumoCouponName ] ) ? $CouponData[ $SumoCouponName ] : $CouponData[ $AutoSumoCouponName ] ;
        $OtherCouponValue  = array_sum( $CouponData ) - $RedeemedPoints ;
        $ArrayKeys         = array_keys( $total_rows ) ;
        $IndexofArray      = array_search( 'payment_method' , $ArrayKeys ) ;
        $PositionOfanIndex = $IndexofArray ? $IndexofArray + 1 : count( $total_rows ) ;

        if ( $RedeemedPoints > 0 ) {
            $total_rows = array_slice( $total_rows , 0 , $PositionOfanIndex , true ) +
                    array(
                        'redeeming' => array(
                            'label' => get_option( 'rs_coupon_label_message' ) ,
                            'value' => __( '-' . srp_formatted_price( $RedeemedPoints ) , SRP_LOCALE ) ,
                        )
                    ) + array_slice( $total_rows , $PositionOfanIndex , count( $total_rows ) - 1 , true ) ;
        }
        if ( $OtherCouponValue > 0 ) {
            $total_rows = array_slice( $total_rows , 0 , $PositionOfanIndex , true ) +
                    array(
                        'othercoupon' => array(
                            'label' => __( 'Discount Value:' , SRP_LOCALE ) ,
                            'value' => __( '-' . srp_formatted_price( $OtherCouponValue ) , SRP_LOCALE ) ,
                        )
                    ) + array_slice( $total_rows , $PositionOfanIndex , count( $total_rows ) - 1 , true ) ;
        }
        return $total_rows ;
    }

    add_filter( 'woocommerce_get_order_item_totals' , 'rs_redeemed_point_in_thank_you_page' , 8 , 3 ) ;
}

if ( ! function_exists( 'srp_term_meta' ) ) {

    function srp_term_meta( $Id , $MetaKey ) {
        return function_exists( 'get_term_meta' ) ? get_term_meta( $Id , $MetaKey , true ) : get_woocommerce_term_meta( $Id , $MetaKey , true ) ;
    }

}

if ( ! function_exists( 'srp_update_term_meta' ) ) {

    function srp_update_term_meta( $Id , $MetaKey , $Value ) {
        return function_exists( 'update_term_meta' ) ? update_term_meta( $Id , $MetaKey , $Value ) : update_woocommerce_term_meta( $Id , $MetaKey , $Value ) ;
    }

}

if ( ! function_exists( 'get_earned_redeemed_points_message' ) ) {

    function get_earned_redeemed_points_message( $orderid ) {
        $OrderObj = wc_get_order( $orderid ) ;
        $OrderObj = srp_order_obj( $OrderObj ) ;
        $UserId   = $OrderObj[ 'order_userid' ] ;
        if ( empty( $UserId ) )
            return ;

        global $wpdb ;
        $Message            = array() ;
        $EarnedTotal        = array() ;
        $RedeemTotal        = array() ;
        $RevisedEarnTotal   = array() ;
        $RevisedRedeemTotal = array() ;
        $table_name         = $wpdb->prefix . 'rsrecordpoints' ;
        $Status             = $OrderObj[ 'order_status' ] ;
        $OrderStatus        = str_replace( 'wc-' , '' , $Status ) ;
        $TotalEarnPoints    = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints FROM $table_name WHERE orderid = %d and userid = %d and checkpoints != 'RVPFRP'and  checkpoints != 'RVPFRPG' and checkpoints != 'RRP'" , $orderid , $UserId ) , ARRAY_A ) ;
        $ReplacedPoints     = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints FROM $table_name WHERE orderid = %d AND reasonindetail = 'Replaced'" , $orderid ) , ARRAY_A ) ;
        $TotalEarnPoints    = (srp_check_is_array( $ReplacedPoints )) ? $ReplacedPoints : $TotalEarnPoints ;
        foreach ( $TotalEarnPoints as $EarnPoints ) {
            $EarnedTotal[] = $EarnPoints[ 'earnedpoints' ] ;
        }
        $TotalRedeemPoints = $wpdb->get_results( $wpdb->prepare( "SELECT redeempoints FROM $table_name WHERE orderid = %d and userid = %d and checkpoints != 'RVPFPPRP'" , $orderid , $UserId ) , ARRAY_A ) ;
        foreach ( $TotalRedeemPoints as $RedeemPoints ) {
            $RedeemTotal[] = $RedeemPoints[ 'redeempoints' ] ;
        }
        $TotalRevisedEarnPoints = $wpdb->get_results( $wpdb->prepare( "SELECT redeempoints FROM $table_name WHERE checkpoints = 'RVPFPPRP' and userid = %d and orderid = %d" , $UserId , $orderid ) , ARRAY_A ) ;
        foreach ( $TotalRevisedEarnPoints as $RevisedEarnPoints ) {
            $RevisedEarnTotal[] = $RevisedEarnPoints[ 'redeempoints' ] ;
        }
        $TotalRevisedRedeemPoints = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints FROM $table_name WHERE orderid = %d and userid = %d and checkpoints != 'PPRP' and checkpoints != 'PPRRPG' and checkpoints != 'RRP' and checkpoints != 'RPG' and checkpoints != 'RPBSRP'" , $orderid , $UserId ) , ARRAY_A ) ;
        foreach ( $TotalRevisedRedeemPoints as $RevisedRedeemPoints ) {
            $RevisedRedeemTotal[] = $RevisedRedeemPoints[ 'earnedpoints' ] ;
        }
        if ( in_array( $OrderStatus , get_option( 'rs_order_status_control_redeem' ) ) )
            RSPointExpiry::update_redeem_point_for_user( $orderid ) ;

        $totalredeemvalue = array_sum( $RedeemTotal ) - array_sum( $RevisedRedeemTotal ) ;
        $RedeemPointMsg   = (get_option( 'rs_enable_msg_for_redeem_points' ) == 'yes') ? str_replace( '[redeempoints]' , round_off_type( $totalredeemvalue ) , get_option( 'rs_msg_for_redeem_points' ) ) : '' ;
        $totalearnedvalue = array_sum( $EarnedTotal ) - array_sum( $RevisedEarnTotal ) ;
        $EarnPointMsg     = (get_option( 'rs_enable_msg_for_earned_points' ) == 'yes') ? str_replace( '[earnedpoints]' , round_off_type( $totalearnedvalue ) , get_option( 'rs_msg_for_earned_points' ) ) : '' ;

        $Message[ $EarnPointMsg ] = $RedeemPointMsg ;
        return $Message ;
    }

}

if ( ! function_exists( 'total_points_for_current_purchase' ) ) {

    function total_points_for_current_purchase( $Total , $UserId ) {
        if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
            $CartTotalPoints = get_reward_points_based_on_cart_total( $Total ) ;
            $CartTotalPoints = RSMemberFunction::earn_points_percentage( $UserId , ( float ) $CartTotalPoints ) ;
            $Points          = $CartTotalPoints + apply_filters( 'srp_buying_points_in_cart' , 0 ) ;
        } else {
            $Points = global_variable_points() + apply_filters( 'srp_buying_points_in_cart' , 0 ) ;
        }
        if ( get_option( 'rs_enable_first_purchase_reward_points' ) == 'yes' && $UserId ) {
            $OrderCount          = get_posts( array(
                'numberposts' => -1 ,
                'meta_key'    => '_customer_user' ,
                'meta_value'  => $UserId ,
                'post_type'   => wc_get_order_types() ,
                'post_status' => array( 'wc-processing' , 'wc-on-hold' , 'wc-completed' ) ,
                    ) ) ;
            $FirstPurchasePoints = RSMemberFunction::earn_points_percentage( $UserId , ( float ) get_option( 'rs_reward_points_for_first_purchase_in_fixed' ) ) ;
            $Points              = ( count( $OrderCount ) == 0 ) ? ($Points + $FirstPurchasePoints) : $Points ;
        }
        return $Points ;
    }

}

/* To display Earning Level Name */

if ( ! function_exists( 'earn_level_name' ) ) {

    function earn_level_name( $UserId ) {
        if ( get_option( 'rs_enable_earned_level_based_reward_points' ) != 'yes' )
            return ;

        $Pointsdata = new RS_Points_Data( $UserId ) ;
        $Points     = get_option( 'rs_select_earn_points_based_on' ) == '1' ? $Pointsdata->total_earned_points() : $Pointsdata->total_available_points() ;
        $RuleId     = FPRewardSystem_Free_Product::earning_and_redeeming_level_id( $Points , 'earning' ) ;
        $Rules      = get_option( 'rewards_dynamic_rule' ) ;
        $LevelName  = isset( $Rules[ $RuleId ][ 'name' ] ) ? $Rules[ $RuleId ][ 'name' ] : "" ;
        return $LevelName ;
    }

}

/* display Points to reach next level in earning */

if ( ! function_exists( 'points_to_reach_next_earn_level' ) ) {

    function points_to_reach_next_earn_level( $UserId ) {
        if ( get_option( 'rs_enable_earned_level_based_reward_points' ) != 'yes' )
            return ;

        $Rules = get_option( 'rewards_dynamic_rule' ) ;

        if ( ! srp_check_is_array( $Rules ) )
            return ;

        $Pointsdata = new RS_Points_Data( $UserId ) ;
        $Points     = get_option( 'rs_select_earn_points_based_on' ) == '1' ? $Pointsdata->total_earned_points() : $Pointsdata->total_available_points() ;

        $RuleId = FPRewardSystem_Free_Product::earning_and_redeeming_level_id( $Points , 'earning' ) ;
        if ( get_option( 'rs_free_product_range' ) == '1' ) {
            $LevelName = isset( $Rules[ $RuleId ][ 'name' ] ) ? $Rules[ $RuleId ][ 'name' ] : "" ;
            if ( ! isset( $Rules[ $RuleId ][ 'rewardpoints' ] ) )
                return ;

            $NextLevelPoints = ( float ) $Rules[ $RuleId ][ 'rewardpoints' ] - $Points ;
            $NextLevelPoints = ($NextLevelPoints == 0) ? 1 : ($NextLevelPoints + 1) ;

            $rule_keys = array() ;
            foreach ( $Rules as $key => $rule ) {
                if ( $rule[ "rewardpoints" ] > ($NextLevelPoints + $Points) ) {
                    $rule_keys[] = $key ;
                }
            }

            if ( empty( $rule_keys ) ) {
                return ;
            }
            $next_rule_id = min( $rule_keys ) ;
            $LevelName    = isset( $Rules[ $next_rule_id ][ 'name' ] ) ? $Rules[ $next_rule_id ][ 'name' ] : "" ;
        } else {
            $max_rewards = array() ;
            foreach ( $Rules as $Rule ) {

                if ( ! $RuleId ) {
                    $max_rewards[ $Rule[ 'rewardpoints' ] ] = $Rule[ 'name' ] ;
                } else if ( ($Rule[ 'rewardpoints' ] > $Rules[ $RuleId ][ 'rewardpoints' ] ) ) {
                    $max_rewards[ $Rule[ 'rewardpoints' ] ] = $Rule[ 'name' ] ;
                }
            }

            if ( ! srp_check_is_array( $max_rewards ) )
                return ;

            $RewardPoints    = min( array_keys( $max_rewards ) ) ;
            $NextLevelPoints = ( float ) $RewardPoints - $Points ;
            $LevelName       = $max_rewards[ $RewardPoints ] ;
        }
        $Msg = str_replace( '[balancepoint]' , $NextLevelPoints , str_replace( '[next_level_name]' , $LevelName , get_option( 'rs_point_to_reach_next_level' ) ) ) ;
        return $Msg ;
    }

}

if ( ! function_exists( 'rs_get_referrer_email_info_in_order' ) ) {

    function rs_get_referrer_email_info_in_order( $order_id , $message ) {

        $referred_id = get_post_meta( $order_id , '_referrer_name' , true ) ;
        if ( ! referred_id )
            return $message ;

        $UserInfo = get_user_by( 'id' , $referred_id ) ;

        $UserName  = is_object( $UserInfo ) ? $UserInfo->user_login : 'Guest' ;
        $FirstName = is_object( $UserInfo ) ? $UserInfo->first_name : 'Guest' ;
        $LastName  = is_object( $UserInfo ) ? $UserInfo->last_name : 'Guest' ;
        $Email     = is_object( $UserInfo ) ? $UserInfo->user_email : 'Guest' ;

        $message = str_replace( array( '[rs_referrer_name]' , '[rs_referrer_first_name]' , '[rs_referrer_last_name]' , '[rs_referrer_email_id]' ) , array( $UserName , $FirstName , $LastName , $Email ) , $message ) ;
        return $message ;
    }

}

if ( ! function_exists( 'rs_check_product_purchase_notice_for_variation' ) ) {
    /*
     * Check for Display product Purchase Notice for Variation Messages
     */

    function rs_check_product_purchase_notice_for_variation() {

        if ( get_option( 'rs_product_purchase_activated' , 'no' ) == 'no' ):
            return 'no' ;
        endif ;

        $variation_level_for_logged_in = get_option( 'rs_show_hide_message_for_variable_in_single_product_page' ) ;
        $variation_level_for_guest     = get_option( 'rs_show_hide_message_for_variable_in_single_product_page_guest' ) ;
        $variation_earn_notice         = get_option( 'rs_show_hide_message_for_variable_product' ) ;
        $default_level_earn_notice     = get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) ;
        $variation_level_for_related   = get_option( 'rs_show_hide_message_for_shop_archive_variable_related_products' ) ;

        if ( is_user_logged_in() ):
            if ( '2' == $variation_level_for_logged_in && '2' == $variation_earn_notice && '2' == $default_level_earn_notice && '2' == $variation_level_for_related ):
                return 'no' ;
            endif ;
        else :
            if ( '2' == $variation_level_for_guest && '2' == $variation_earn_notice && '2' == $default_level_earn_notice && '2' == $variation_level_for_related ):
                return 'no' ;
            endif ;
        endif ;

        return 'yes' ;
    }

}

if ( ! function_exists( 'rs_check_referral_notice_variation' ) ) {
    /*
     * Check for Display Referral Notice for Variation Messages
     */

    function rs_check_referral_notice_variation() {

        if ( get_option( 'rs_referral_activated' , 'no' ) == 'no' ):
            return 'no' ;
        endif ;

        if ( '2' == get_option( 'rs_show_hide_message_for_variable_product_referral' ) ):
            return 'no' ;
        endif ;

        return 'yes' ;
    }

}

if ( ! function_exists( 'rs_check_buying_points_notice_for_variation' ) ) {
    /*
     * Check for Display Buying Point for Variation Messages
     */

    function rs_check_buying_points_notice_for_variation() {

        if ( get_option( 'rs_buyingpoints_activated' , 'no' ) == 'no' ):
            return 'no' ;
        endif ;

        $buying_point_message_for_logged_in  = get_option( 'rs_show_hide_buy_point_message_for_variable_product' ) ;
        $buying_point_earn_message           = get_option( 'rs_show_hide_buy_points_message_for_variable_in_product' ) ;
        $buying_point_earn_message_for_guest = get_option( 'rs_show_hide_buy_point_message_for_variable_in_product_guest' ) ;

        if ( is_user_logged_in() ):
            if ( '2' == $buying_point_message_for_logged_in && '2' == $buying_point_earn_message ):
                return 'no' ;
            endif ;
        else:
            if ( '2' == $buying_point_earn_message_for_guest && '2' == $buying_point_earn_message ):
                return 'no' ;
            endif ;
        endif ;

        return 'yes' ;
    }

}
if ( ! function_exists( 'msg_for_rewardgateway' ) ) {

    function msg_for_rewardgateway( $checkout ) {
        if ( ! is_user_logged_in() )
            return ;

        $BanType = check_banning_type( get_current_user_id() ) ;
        if ( $BanType == 'earningonly' || $BanType == 'both' )
            return ;

        $default_value = ('yes' == get_option( 'rs_disable_point_if_reward_points_gateway' , 'no' )) ? array( 'reward_gateway' ) : array() ;
        /* Product Purchase restriction Notice on using Payment Gateways */
        if ( get_option( 'rs_product_purchase_activated' , 'no' ) == 'yes' && srp_check_is_array( get_option( 'rs_select_payment_gateway_for_restrict_reward' , $default_value ) ) ) {
            rs_add_notice( 'rsgatewaypointsmsg' , '' , '' ) ;
        }

        if ( get_option( 'rs_reward_action_activated' , 'no' ) != 'yes' )
            return ;

        if ( get_option( 'rs_show_hide_message_payment_gateway_reward_points' ) == 2 )
            return ;

        $msg = get_option( 'rs_message_payment_gateway_reward_points' ) ;

        /* Earn Notice on using Payment Gateways */
        rs_add_notice( 'rspgpoints' , '' , get_option( 'rs_message_payment_gateway_reward_points' ) ) ;
    }

    add_action( 'woocommerce_after_checkout_form' , 'msg_for_rewardgateway' ) ;
}

if ( ! function_exists( 'rs_add_notice' ) ) {

    function rs_add_notice( $div_class = '' , $span_class = '' , $message , $notice_type = 'notice' ) {

        if ( 'notice' == $notice_type ) {
            ?>
            <div class="woocommerce-info <?php echo esc_attr( $div_class ) ; ?>">
                <?php
                $html = sprintf( '<span class ="%s">%s</span>' , esc_attr( $span_class ) , wp_kses_post( $message ) ) ;
                ?>
            </div>
            <?php
        }
    }

}

if ( ! function_exists( 'rs_get_payment_gateways' ) ) {

    function rs_get_payment_gateways() {
        $gateways           = array() ;
        $wc_gateways        = new WC_Payment_Gateways() ;
        $available_gateways = $wc_gateways->get_available_payment_gateways() ;

        foreach ( $available_gateways as $id => $gateway ) {
            $gateways[ $id ] = $gateway->get_title() ;
        }

        return $gateways ;
    }

}

if ( ! function_exists( 'check_if_user_already_purchase' ) ) {

    function check_if_user_already_purchase( $ProductId , $RuleId , $PurcahsedProductList ) {
        if ( ! srp_check_is_array( $PurcahsedProductList ) )
            return true ;

        if ( ! isset( $PurcahsedProductList[ $RuleId ] ) )
            return true ;

        if ( ! srp_check_is_array( $PurcahsedProductList[ $RuleId ] ) )
            return true ;


        if ( ! in_array( $ProductId , $PurcahsedProductList[ $RuleId ] ) )
            return true ;

        return false ;
    }

}
