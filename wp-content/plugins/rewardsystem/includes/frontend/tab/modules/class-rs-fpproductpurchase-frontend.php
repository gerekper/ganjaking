<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSProductPurchaseFrontend' ) ) {

    class RSProductPurchaseFrontend {

        public static function init() {
            add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'save_points_info_in_order' ) , 10 , 2 ) ;

            if ( get_option( 'rs_message_before_after_cart_table' ) == '1' ) {
                if ( get_option( 'rs_reward_point_troubleshoot_before_cart' ) == '1' ) {
                    add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'messages_and_validation_for_product_purcahse' ) ) ;
                    add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'msg_for_cart_total_based_points' ) ) ;
                } else {
                    add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'messages_and_validation_for_product_purcahse' ) ) ;
                    add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'msg_for_cart_total_based_points' ) ) ;
                }
            } else {
                add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'messages_and_validation_for_product_purcahse' ) ) ;
                add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'msg_for_cart_total_based_points' ) ) ;
            }
            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'msg_for_cart_total_based_points' ) ) ;
            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'messages_and_validation_for_product_purcahse' ) ) ;
        }

        /* Save Points Detail in Order */

        public static function save_points_info_in_order( $order_id , $orderobj ) {
            if ( ! is_user_logged_in() )
                return ;

            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' ) {
                $PointInfo = (get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) == 'yes') ? RSFrontendAssets::modified_points_for_products() : RSFrontendAssets::original_points_for_product() ;
                if ( srp_check_is_array( $PointInfo ) ) {
                    update_post_meta( $order_id , 'points_for_current_order' , $PointInfo ) ;
                    $Points = array_sum( $PointInfo ) ;
                    $Points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $Points ) ;
                    update_post_meta( $order_id , 'rs_points_for_current_order_as_value' , $Points ) ;
                }
            } else {
                if ( get_option( 'rs_award_points_for_cart_or_product_total' ) == '1' ) {
                    $PointInfo = (get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) == 'yes') ? RSFrontendAssets::modified_points_for_products() : RSFrontendAssets::original_points_for_product() ;
                    if ( srp_check_is_array( $PointInfo ) ) {
                        update_post_meta( $order_id , 'points_for_current_order' , $PointInfo ) ;
                        $Points = array_sum( $PointInfo ) ;
                        $Points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $Points ) ;
                        update_post_meta( $order_id , 'rs_points_for_current_order_as_value' , $Points ) ;
                    }
                } else {
                    $Order           = new WC_Order( $order_id ) ;
                    $CartTotalPoints = get_reward_points_based_on_cart_total( $Order->get_total() ) ;
                    $CartTotalPoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $CartTotalPoints ) ;
                    if ( ! empty( $CartTotalPoints ) )
                        update_post_meta( $order_id , 'points_for_current_order_based_on_cart_total' , $CartTotalPoints ) ;
                }
            }
            update_post_meta( $order_id , 'frontendorder' , 1 ) ;
        }

        public static function messages_and_validation_for_product_purcahse() {
            echo self::first_purchase_message_for_product_purcahse() ;
            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' )
                return ;

            global $totalrewardpoints ;
            echo self::min_and_max_cart_total_validation() ;
            echo self::purchase_msg_for_payment_plan_product() ;
            echo self::purchase_msg_for_each_product() ;
            $totalrewardpoints = global_variable_points() ;
            WC()->session->set( 'rewardpoints' , $totalrewardpoints ) ;
        }

        /* Assign Global Value($totalrewardpointsnew) and Min/Max Validation Cart Total Limitation */

        public static function min_and_max_cart_total_validation() {
            $CartTotal = WC()->cart->total ;
            if ( empty( $CartTotal ) )
                return ;

            $PointsInfo = ( get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) == 'yes') ? RSFrontendAssets::modified_points_for_products() : RSFrontendAssets::original_points_for_product() ;
            if ( ! srp_check_is_array( $PointsInfo ) )
                return ;

            $MinCartTotal    = get_option( 'rs_minimum_cart_total_for_earning' ) ;
            $MaxCartTotal    = get_option( 'rs_maximum_cart_total_for_earning' ) ;
            $MinCartTotalErr = str_replace( '[carttotal]' , $MinCartTotal , get_option( 'rs_min_cart_total_for_earning_error_message' ) ) ;
            $MaxCartTotalErr = str_replace( '[carttotal]' , $MaxCartTotal , get_option( 'rs_max_cart_total_for_earning_error_message' ) ) ;
            global $totalrewardpointsnew ;
            if ( ! empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
                if ( $CartTotal >= $MinCartTotal && $CartTotal <= $MaxCartTotal ) {
                    $totalrewardpointsnew = $PointsInfo ;
                } elseif ( $CartTotal <= $MinCartTotal ) {
                    if ( get_option( 'rs_show_hide_minimum_cart_total_earn_error_message' ) == '1' ) {
                        ?>
                        <div class="woocommerce-error" ><?php echo $MinCartTotalErr ; ?></div>
                        <?php
                    }
                } elseif ( $CartTotal >= $MaxCartTotal ) {
                    if ( get_option( 'rs_show_hide_maximum_cart_total_earn_error_message' ) == '1' ) {
                        ?>
                        <div class="woocommerce-error" ><?php echo $MaxCartTotalErr ; ?></div>
                        <?php
                    }
                }
            } else if ( ! empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
                if ( $CartTotal >= $MinCartTotal ) {
                    $totalrewardpointsnew = $PointsInfo ;
                } else {
                    if ( get_option( 'rs_show_hide_minimum_cart_total_earn_error_message' ) == '1' ) {
                        ?>
                        <div class="woocommerce-error" ><?php echo $MinCartTotalErr ; ?></div>
                        <?php
                    }
                }
            } else if ( empty( $MinCartTotal ) && ! empty( $MaxCartTotal ) ) {
                if ( $CartTotal <= $MaxCartTotal ) {
                    $totalrewardpointsnew = $PointsInfo ;
                } else {
                    if ( get_option( 'rs_show_hide_maximum_cart_total_earn_error_message' ) == '1' ) {
                        ?>
                        <div class="woocommerce-error" ><?php echo $MaxCartTotalErr ; ?></div>
                        <?php
                    }
                }
            } else if ( empty( $MinCartTotal ) && empty( $MaxCartTotal ) ) {
                $totalrewardpointsnew = $PointsInfo ;
            } else {
                $totalrewardpointsnew = '' ;
            }
        }

        /* Display Product Purchase message in Cart/Checkout for Product */

        public static function purchase_msg_for_each_product() {
            $ShowPurchaseMsg = is_cart() ? get_option( 'rs_show_hide_message_for_each_products' ) : get_option( 'rs_show_hide_message_for_each_products_checkout_page' ) ;
            if ( $ShowPurchaseMsg == 2 )
                return ;

            global $totalrewardpointsnew ;
            global $messageglobal ;
            if ( ! srp_check_is_array( $totalrewardpointsnew ) )
                return ;

            if ( ! srp_check_is_array( $messageglobal ) )
                return ;

            if ( array_sum( $totalrewardpointsnew ) == 0 )
                return ;

            $ShowMsg = is_user_logged_in() ? ( ! check_if_coupon_applied() && ! check_if_discount_applied()) : (get_option( 'rs_enable_acc_creation_for_guest_checkout_page' ) == 'yes' && ( ! check_if_coupon_applied() && ! check_if_discount_applied()) ) ;
            if ( ! $ShowMsg )
                return ;
            ?>
            <div class="woocommerce-info sumo_reward_points_info_message rs_cart_message">
                <?php echo implode( '' , $messageglobal ) ; ?>
            </div><?php
        }

        /* Assign Global Value($messageglobal) and Display Product Purchase Message for SUMO Payment Plan Product */

        public static function purchase_msg_for_payment_plan_product() {
            if ( ! is_user_logged_in() )
                return ;

            $PointsInfo = ( get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) == 'yes') ? RSFrontendAssets::modified_points_for_products() : RSFrontendAssets::original_points_for_product() ;
            if ( ! srp_check_is_array( $PointsInfo ) )
                return ;

            global $totalrewardpoints ;
            global $messageglobal ;
            global $totalrewardpoints_payment_plan ;
            global $producttitle ;
            foreach ( $PointsInfo as $ProductId => $Points ) {
                if ( empty( $Points ) )
                    continue ;

                $ProductObj = srp_product_object( $ProductId ) ;
                if ( ! is_object( $ProductObj ) )
                    continue ;

                if ( srp_product_type( $ProductId ) == 'booking' )
                    continue ;

                $producttitle      = $ProductId ;
                $totalrewardpoints = $Points ;

                if ( is_initial_payment( $ProductId ) ) {
                    $ShowPaymentPlanMsg = is_cart() ? get_option( 'rs_show_hide_message_for_each_payment_plan_products' ) : get_option( 'rs_show_hide_message_for_each_payment_plan_products_checkout_page' ) ;
                    $PaymentPlanMsg     = is_cart() ? get_option( 'rs_message_payment_plan_product_in_cart' ) : get_option( 'rs_message_payment_plan_product_in_checkout' ) ;

                    $totalrewardpoints_payment_plan = array( $Points ) ;
                    if ( $ShowPaymentPlanMsg == '1' ) {
                        ?>
                        <div class="woocommerce-info rs_cart_message" ><?php echo do_shortcode( $PaymentPlanMsg ) ; ?></div>
                        <?php
                    }
                } else {
                    $ProductMsg                  = is_cart() ? get_option( 'rs_message_product_in_cart' ) : get_option( 'rs_message_product_in_checkout' ) ;
                    $messageglobal[ $ProductId ] = do_shortcode( $ProductMsg ) . "<br>" ;
                }
            }
        }

        public static function msg_for_cart_total_based_points() {
            if ( ! is_user_logged_in() )
                return ;

            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' )
                return ;

            if ( get_option( 'rs_award_points_for_cart_or_product_total' ) == '1' )
                return ;

            if ( get_option( 'rs_enable_cart_total_reward_points' ) == '2' )
                return ;

            $ShowMsg = is_cart() ? get_option( 'rs_enable_msg_for_cart_total_based_points' ) : get_option( 'rs_enable_msg_for_cart_total_based_points_in_checkout' ) ;
            if ( $ShowMsg == '2' )
                return ;

            $check_coupon_applied = check_if_coupon_applied() ;
            if ( $check_coupon_applied )
                return ;

            $check_if_discount_applied = check_if_discount_applied() ;
            if ( $check_if_discount_applied )
                return ;

            $PointForCartTotal = get_reward_points_based_on_cart_total( WC()->cart->total ) ;
            $PointToReturn     = round_off_type( $PointForCartTotal ) ;
            $PointToReturn     = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $PointToReturn ) ;

            if ( empty( $PointToReturn ) )
                return ;

            $Msg                 = is_cart() ? get_option( 'rs_msg_for_cart_total_based_points' ) : get_option( 'rs_msg_for_cart_total_based_points_in_checkout' ) ;
            $MsgToDisplay        = str_replace( '[carttotalbasedrewardpoints]' , $PointToReturn , $Msg ) ;
            $ConvertionRate      = redeem_point_conversion( $PointToReturn , get_current_user_id() , 'price' ) ;
            $CurrencyValue       = round_off_type_for_currency( $ConvertionRate ) ;
            $CurrencyReplacedMsg = str_replace( '[equalvalueforcarttotal]' , wc_price( $CurrencyValue ) , $MsgToDisplay )
            ?>
            <div class="woocommerce-info rs_msg_for_cart_total_based_points">
                <?php echo $CurrencyReplacedMsg ; ?>
            </div>
            <?php
        }

        public static function first_purchase_message_for_product_purcahse() {
            if ( ! is_user_logged_in() )
                return ;

            if ( get_option( 'rs_enable_first_purchase_reward_points' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_message_for_first_purchase_points' ) == '2' )
                return ;

            if ( empty( get_option( 'rs_reward_points_for_first_purchase_in_fixed' ) ) )
                return ;

            $OrderCount = get_posts( array(
                'numberposts' => -1 ,
                'meta_key'    => '_customer_user' ,
                'meta_value'  => get_current_user_id() ,
                'post_type'   => wc_get_order_types() ,
                'post_status' => array( 'wc-processing' , 'wc-on-hold' , 'wc-completed' ) ,
                    ) ) ;
            if ( count( $OrderCount ) > 0 )
                return ;
            ?>
            <div class="woocommerce-info sumo_reward_points_payment_plan_complete_message rs_cart_message">
                <?php
                echo do_shortcode( get_option( 'rs_message_for_first_purchase' ) ) ;
                ?>
            </div>
            <?php
        }

    }

    RSProductPurchaseFrontend::init() ;
}