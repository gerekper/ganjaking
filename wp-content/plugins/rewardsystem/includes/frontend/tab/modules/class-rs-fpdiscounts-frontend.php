<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSDiscountCompatability' ) ) {

    class RSDiscountCompatability {

        public static function init() {

            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'display_msg_when_discounts_applied' ) ) ;

            /* Trash when Coupon is removed */
            add_action( 'woocommerce_after_checkout_validation' , array( __CLASS__ , 'trash_coupon_or_redeeming_on_placing_the_order' ) , 11 , 2 ) ;
        }

        /*
         * Display Message when Discount is applied.
         */

        public static function display_msg_when_discounts_applied() {
            if ( ! class_exists( 'SUMODiscounts' ) ) {
                return ;
            }

            if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
                return ;
            }

            if ( check_if_discount_applied() ) {
                return ;
            }

            if ( WC()->session->get( 'check_if_fee_exist' ) == 'yes' ) {
                return ;
            }

            self::display_coupon_restriction_notice() ;

            self::display_redeeming_restriction_notice() ;
        }

        /*
         * Trash Coupon or Redeeming if Exists on placing the order.
         * 
         */

        public static function trash_coupon_or_redeeming_on_placing_the_order( $data , $error ) {

            $user_id = get_current_user_id() ;
            if ( ! $user_id ) {
                return ;
            }
            if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
                return ;
            }
            if ( ! class_exists( 'SUMODiscounts' ) ) {
                return ;
            }
            if ( check_if_discount_applied() ) {
                return ;
            }
            if ( WC()->session->get( 'check_if_fee_exist' ) == 'yes' ) {
                return ;
            }
            $AppliedCoupons = WC()->cart->get_applied_coupons() ;
            if ( ! srp_check_is_array( array_filter( $AppliedCoupons ) ) ) {
                return ;
            }

            $restriction_for_redeeming = get_option( 'rs_show_redeeming_field' ) ;
            $restriction_for_coupon    = get_option( '_rs_show_hide_coupon_if_sumo_discount' ) ;
            $UserInfo                  = get_user_by( 'id' , $user_id ) ;
            $UserName                  = $UserInfo->user_login ;
            $Redeem                    = 'sumo_' . strtolower( "$UserName" ) ;
            $AutoRedeem                = 'auto_redeem_' . strtolower( "$UserName" ) ;

            foreach ( $AppliedCoupons as $code ) :
                if ( $restriction_for_redeeming == '2' ) {
                    if ( $code == $Redeem || $code == $AutoRedeem ) {
                        WC()->cart->remove_coupon( $code ) ;
                        $error->add( 'Coupon ' , __( 'Since you got a discount, the applied points have been removed' , SRP_LOCALE ) ) ;
                    }
                }
                if ( $restriction_for_coupon == 'yes' && ($code != $Redeem && $code != $AutoRedeem ) ) {
                    WC()->cart->remove_coupon( $code ) ;
                    $error->add( 'Coupon ' , __( 'Since you got a discount, the coupon have been removed' , SRP_LOCALE ) ) ;
                }
            endforeach ;
        }

        /*
         * Display Coupon Restriction notice when Discount is applied.
         */

        public static function display_coupon_restriction_notice() {

            if ( get_option( '_rs_show_hide_coupon_if_sumo_discount' ) == 'no' ) {
                return ;
            }
            ?>
            <div class="woocommerce-info rs_show_notice_for_hide_coupon_field">
                <?php echo get_option( 'rs_message_in_cart_and_checkout_for_discount' ) ; ?>
            </div>
            <?php
        }

        /*
         * Display Redeeming Restriction notice when Discount is applied.
         */

        public static function display_redeeming_restriction_notice() {

            $user_id = get_current_user_id() ;
            if ( ! $user_id ) {
                return ;
            }
            if ( get_option( 'rs_show_redeeming_field' , '1' ) == '1' ) {
                return ;
            }
            ?>
            <div class="woocommerce-info rs_show_notice_for_hide_redeem_field">
                <?php echo get_option( 'rs_redeeming_usage_restriction_for_discount' ) ; ?>
            </div>
            <?php
        }

    }

    RSDiscountCompatability::init() ;
}