<?php
/*
 * Frontend Assests
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFrontendAssets' ) ) {

    class RSFrontendAssets {

        public static function init() {
            add_action( 'wp_head' , array( __CLASS__ , 'custom_css_for_page' ) ) ;

            if ( get_option( 'rs_message_before_after_cart_table' ) == '1' ) {
                if ( get_option( 'rs_reward_point_troubleshoot_before_cart' ) == '1' ) {
                    add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'message_for_guest' ) , 999 ) ;
                } else {
                    add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'message_for_guest' ) , 999 ) ;
                }
            } else {
                add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'message_for_guest' ) , 999 ) ;
            }
            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'message_for_guest' ) , 999 ) ;

            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'force_guest_signup_in_checkout' ) , 10 , 1 ) ;

            add_action( 'woocommerce_checkout_process' , array( __CLASS__ , 'create_account_for_guest' ) ) ;

            add_filter( 'woocommerce_checkout_fields' , array( __CLASS__ , 'allow_user_to_involve_reward_program_in_checkbox' ) ) ;

            /* Points that can be Earned Message based on select Type in Cart page */
            if ( get_option( 'rs_select_type_for_cart' , '1' ) == '1' ) {
                add_action( 'woocommerce_cart_totals_before_order_total' , array( __CLASS__ , 'total_points_in_cart' ) ) ;
            } else {
                add_action( 'woocommerce_cart_totals_after_order_total' , array( __CLASS__ , 'total_points_in_cart' ) ) ;
            }

            /* Points that can be Earned Message based on select Type in Checkout page */
            if ( get_option( 'rs_select_type_for_checkout' , '2' ) == '2' ) {
                add_action( 'woocommerce_review_order_after_order_total' , array( __CLASS__ , 'total_points_in_checkout' ) ) ;
            } else {
                add_action( 'woocommerce_review_order_before_order_total' , array( __CLASS__ , 'total_points_in_checkout' ) ) ;
            }

            add_action( 'woocommerce_order_details_after_order_table_items' , array( __CLASS__ , 'total_points_in_order_detail' ) ) ; //For WooCommerce  > V3.3

            if ( WC_VERSION == 3.0 )
                add_action( 'woocommerce_order_items_table' , array( __CLASS__ , 'total_points_in_order_detail' ) ) ; //For WooCommerce V3.0

            if ( get_option( 'rs_message_before_after_cart_table' ) == '1' ) {
                if ( get_option( 'rs_reward_point_troubleshoot_before_cart' ) == '1' ) {
                    add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'complete_message_for_purchase' ) , 999 ) ;
                } else {
                    add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'complete_message_for_purchase' ) , 999 ) ;
                }
            } else {
                add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'complete_message_for_purchase' ) , 999 ) ;
            }
            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'complete_message_for_purchase' ) , 999 ) ;
        }

        public static function custom_css_for_page() {
            ?>
            <style type="text/css">
            <?php
            echo get_option( 'rs_general_custom_css' ) ;

            if ( is_shop() )
                echo get_option( 'rs_shop_page_custom_css' ) ;

            if ( is_product_category() )
                echo get_option( 'rs_category_page_custom_css' ) ;

            if ( is_product() )
                echo get_option( 'rs_single_product_page_custom_css' ) ;

            if ( is_cart() )
                echo get_option( 'rs_cart_page_custom_css' ) ;

            if ( is_checkout() )
                echo get_option( 'rs_checkout_page_custom_css' ) ;
            ?> 
            </style>
            <?php
        }

        public static function message_for_guest() {
            if ( is_user_logged_in() )
                return ;

            $ShowMsg = is_cart() ? get_option( 'rs_show_hide_message_for_guest' ) : get_option( 'rs_show_hide_message_for_guest_checkout_page' ) ;
            if ( $ShowMsg == 2 )
                return ;

            $MsgToDisplay = is_cart() ? get_option( 'rs_message_for_guest_in_cart' ) : get_option( 'rs_message_for_guest_in_checkout' ) ;
            $Divclass     = is_cart() ? 'rs_cart_message' : 'rs_checkout_message' ;
            ?>
            <div class="woocommerce-info <?php echo $Divclass ; ?>"><?php echo do_shortcode( $MsgToDisplay ) ; ?></div>
            <?php
        }

        public static function force_guest_signup_in_checkout( $checkout ) {
            if ( is_user_logged_in() )
                return ;

            if ( ! is_checkout() )
                return ;

            if ( get_option( 'rs_enable_acc_creation_for_guest_checkout_page' ) == 'no' )
                return ;

            if ( ! isset( $checkout->enable_signup ) )
                return ;

            if ( ! isset( $checkout->enable_guest_checkout ) )
                return ;

            $PointsInfo = ( get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) == 'yes') ? RSFrontendAssets::modified_points_for_products() : RSFrontendAssets::original_points_for_product() ;
            if ( ! srp_check_is_array( $PointsInfo ) )
                return ;

            $checkout->enable_signup         = true ;
            $checkout->enable_guest_checkout = false ;
        }

        /* To Create account for Guest */

        public static function create_account_for_guest() {
            if ( is_user_logged_in() )
                return ;

            if ( ! is_checkout() )
                return ;

            if ( get_option( 'rs_enable_acc_creation_for_guest_checkout_page' ) == 'no' )
                return ;

            if ( ! self::check_if_product_has_reward_points() )
                return ;

            $_POST[ 'createaccount' ] = 1 ;
        }

        public static function check_if_product_has_reward_points() {
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                if ( ! isset( $cart_item[ 'product_id' ] ) )
                    continue ;

                $args   = array(
                    'productid'   => $cart_item[ 'product_id' ] ,
                    'variationid' => isset( $cart_item[ 'variation_id' ] ) ? $cart_item[ 'variation_id' ] : 0 ,
                    'item'        => $cart_item ,
                    'checklevel'  => 'yes' ,
                        ) ;
                $Points = check_level_of_enable_reward_point( $args ) ;
                if ( ! empty( $Points ) )
                    return true ;
            }
            return false ;
        }

        /* Display checkbox to involve guest in reward program in Checkout */

        public static function allow_user_to_involve_reward_program_in_checkbox( $fields ) {
            if ( get_option( 'rs_enable_reward_program' ) == 'no' )
                return $fields ;

            $fields[ 'account' ][ 'account_password' ]   = array(
                'type'        => 'password' ,
                'label'       => __( 'Create account password' , 'woocommerce' ) ,
                'required'    => true ,
                'priority'    => 90 ,
                'placeholder' => esc_attr__( 'Password' , 'woocommerce' ) ,
                    ) ;
            $fields[ 'account' ][ 'enable_reward_prgm' ] = array(
                'type'     => 'checkbox' ,
                'label'    => get_option( 'rs_msg_in_acc_page_when_unchecked' ) ,
                'required' => false ,
                'priority' => 100 ,
                    ) ;
            return $fields ;
        }

        /* Display Points in Cart before Order Total */

        public static function total_points_in_cart() {
            if ( ! is_user_logged_in() )
                return ;

            if ( get_option( 'rs_product_purchase_activated' ) != 'yes' && get_option( 'rs_buyingpoints_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_total_points_cart_field' ) == '2' )
                return ;

            $PaymentPlanPoints = 0 ;
            if ( get_option( 'rs_product_purchase_activated' ) == 'yes' ) {
                if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                    $CartTotalPoints = get_reward_points_based_on_cart_total( WC()->cart->total ) ;
                    $CartTotalPoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $CartTotalPoints ) ;
                    $Points          = apply_filters( 'srp_buying_points_in_cart' , 0 ) + $CartTotalPoints ;
                } else {
                    $TotalPoints = WC()->session->get( 'rewardpoints' ) ;
                    if ( $TotalPoints == 0 )
                        return ;

                    global $totalrewardpoints_payment_plan ;
                    $PointsInfo            = (get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) == 'yes') ? self::modified_points_for_products() : self::original_points_for_product() ;
                    $ProductPlanPoints     = srp_check_is_array( $totalrewardpoints_payment_plan ) ? round_off_type( array_sum( $totalrewardpoints_payment_plan ) ) : 0 ;
                    $PaymentPlanPoints     = $ProductPlanPoints + apply_filters( 'srp_buying_points_for_payment_plan_in_cart' , 0 ) ;
                    $ProductPurchasePoints = array() ;
                    if ( srp_check_is_array( $PointsInfo ) ) {
                        foreach ( $PointsInfo as $ProductId => $Points ) {
                            $Points                  = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $Points ) ;
                            $ProductPurchasePoints[] = floatval( $Points ) ;
                        }
                    }
                    $Points = (array_sum( $ProductPurchasePoints ) + apply_filters( 'srp_buying_points_in_cart' , 0 )) - $PaymentPlanPoints ;
                }
                if ( get_option( 'rs_enable_first_purchase_reward_points' ) == 'yes' ) {
                    $OrderCount          = get_posts( array(
                        'numberposts' => -1 ,
                        'meta_key'    => '_customer_user' ,
                        'meta_value'  => get_current_user_id() ,
                        'post_type'   => wc_get_order_types() ,
                        'post_status' => array( 'wc-processing' , 'wc-on-hold' , 'wc-completed' ) ,
                            ) ) ;
                    $FirstPurchasePoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) get_option( 'rs_reward_points_for_first_purchase_in_fixed' ) ) ;
                    $Points              = (count( $OrderCount ) == 0) ? ($Points + $FirstPurchasePoints) : $Points ;
                }
            } elseif ( get_option( 'rs_buyingpoints_activated' ) == 'yes' ) {
                $Points            = apply_filters( 'srp_buying_points_in_cart' , 0 ) ;
                $PaymentPlanPoints = apply_filters( 'srp_buying_points_for_payment_plan_in_cart' , 0 ) ;
            }

            if ( empty( $Points ) )
                return ;

            $ConvertedValue = redeem_point_conversion( $Points , get_current_user_id() , 'price' ) ;
            $CurrencyValue  = srp_formatted_price( round_off_type_for_currency( $ConvertedValue ) ) ;
            $BoolVal        = empty( WC()->cart->discount_cart ) ? true : (get_option( 'rs_enable_redeem_for_order' ) == 'no' && get_option( 'rs_disable_point_if_coupon' ) == 'no') ;
            if ( ! $BoolVal )
                return ;
            ?>
            <div class="points_total" >
                <tr class="points-totalvalue">
                    <th><?php echo get_option( 'rs_total_earned_point_caption' ) ; ?></th>
                    <td data-title="<?php esc_attr_e( get_option( 'rs_total_earned_point_caption' ) , SRP_LOCALE ) ; ?>"><?php echo custom_message_in_thankyou_page( $Points , $CurrencyValue , "rs_show_hide_equivalent_price_for_points_cart" , 'rs_show_hide_custom_msg_for_points_cart' , 'rs_custom_message_for_points_cart' , $PaymentPlanPoints ) ; ?> </td>
                </tr>
            </div>
            <?php
        }

        /* Display Points in Checkout before Order Total */

        public static function total_points_in_checkout() {
            if ( ! is_user_logged_in() )
                return ;

            if ( get_option( 'rs_product_purchase_activated' ) != 'yes' && get_option( 'rs_buyingpoints_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_total_points_checkout_field' ) == 2 )
                return ;

            if ( get_option( 'rs_product_purchase_activated' ) == 'yes' ) {
                if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'no' && get_option( 'rs_award_points_for_cart_or_product_total' ) == '2' ) {
                    $Points = get_reward_points_based_on_cart_total( WC()->cart->total ) ;
                    $Points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $Points ) + apply_filters( 'srp_buying_points_in_cart' , 0 ) ;
                } else {
                    $Points = WC()->session->get( 'rewardpoints' ) + apply_filters( 'srp_buying_points_in_cart' , 0 ) ;
                }
                if ( get_option( 'rs_enable_first_purchase_reward_points' ) == 'yes' ) {
                    $OrderCount          = get_posts( array(
                        'numberposts' => -1 ,
                        'meta_key'    => '_customer_user' ,
                        'meta_value'  => get_current_user_id() ,
                        'post_type'   => wc_get_order_types() ,
                        'post_status' => array( 'wc-processing' , 'wc-on-hold' , 'wc-completed' ) ,
                            ) ) ;
                    $FirstPurchasePoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) get_option( 'rs_reward_points_for_first_purchase_in_fixed' ) ) ;
                    $Points              = (count( $OrderCount ) == 0) ? ($Points + $FirstPurchasePoints) : $Points ;
                }
            } elseif ( get_option( 'rs_buyingpoints_activated' ) == 'yes' ) {
                $Points = apply_filters( 'srp_buying_points_in_cart' , 0 ) ;
            }
            if ( empty( $Points ) )
                return ;

            $ConvertedValue = redeem_point_conversion( $Points , get_current_user_id() , 'price' ) ;
            $CurrencyValue  = srp_formatted_price( round_off_type_for_currency( $ConvertedValue ) ) ;
            $BoolVal        = empty( WC()->cart->discount_cart ) ? true : (get_option( 'rs_enable_redeem_for_order' ) == 'no' && get_option( 'rs_disable_point_if_coupon' ) == 'no') ;
            if ( ! $BoolVal )
                return ;
            ?>
            <tr class="tax-total">
                <th><?php echo get_option( 'rs_total_earned_point_caption_checkout' ) ; ?></th>
                <td>
                    <?php
                    echo custom_message_in_thankyou_page( $Points , $CurrencyValue , 'rs_show_hide_equivalent_price_for_points' , 'rs_show_hide_custom_msg_for_points_checkout' , 'rs_custom_message_for_points_checkout' , 0 ) ;
                    ?>
                </td>
            </tr>
            <?php
        }

        /* Display Points in Order Detail */

        public static function total_points_in_order_detail( $order ) {
            if ( ! is_user_logged_in() )
                return ;

            if ( get_option( 'rs_product_purchase_activated' ) != 'yes' && get_option( 'rs_buyingpoints_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_total_points_order_field' ) == '2' )
                return ;

            $OrderObj         = srp_order_obj( $order ) ;
            $CheckIfRedeeming = (get_option( 'rs_redeeming_activated' ) == 'yes') ? get_post_meta( $OrderObj[ 'order_id' ] , 'rs_check_enable_option_for_redeeming' , true ) : 'no' ;
            if ( $CheckIfRedeeming != 'no' )
                return ;

            $obj                            = new RewardPointsOrder( $OrderObj[ 'order_id' ] , 'no' ) ;
            $check_restriction_for_purchase = $obj->check_redeeming_in_order() ;
            if ( $check_restriction_for_purchase )
                return ;

            $PaymentPlanPoints = get_payment_product_price( $OrderObj[ 'order_id' ] , true ) ;
            $BuyingPoints      = ( float ) srp_check_is_array( get_post_meta( $OrderObj[ 'order_id' ] , 'buy_points_for_current_order' , true ) ) ? array_sum( get_post_meta( $OrderObj[ 'order_id' ] , 'buy_points_for_current_order' , true ) ) : 0 ;
            $BuyingPoints      = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $BuyingPoints ) ;
            $ProductPoints     = ( float ) srp_check_is_array( get_post_meta( $OrderObj[ 'order_id' ] , 'points_for_current_order' , true ) ) ? array_sum( get_post_meta( $OrderObj[ 'order_id' ] , 'points_for_current_order' , true ) ) : 0 ;
            $ProductPoints     = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $ProductPoints ) ;
            $CartTotalPoints   = ( float ) get_post_meta( $OrderObj[ 'order_id' ] , 'points_for_current_order_based_on_cart_total' , true ) ;
            $Points            = empty( $ProductPoints ) ? ($CartTotalPoints + $BuyingPoints) : ($ProductPoints + $BuyingPoints) ;
            if ( get_option( 'rs_enable_first_purchase_reward_points' ) == 'yes' ) {
                $OrderCount          = get_posts( array(
                    'numberposts' => -1 ,
                    'meta_key'    => '_customer_user' ,
                    'meta_value'  => get_current_user_id() ,
                    'post_type'   => wc_get_order_types() ,
                    'post_status' => array( 'wc-processing' , 'wc-on-hold' , 'wc-completed' ) ,
                        ) ) ;
                $FirstPurchasePoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) get_option( 'rs_reward_points_for_first_purchase_in_fixed' ) ) ;
                $Points              = (count( $OrderCount ) == 1) ? ($Points + $FirstPurchasePoints) : $Points ;
            }
            if ( empty( $Points ) )
                return ;

            $ConvertedValue = redeem_point_conversion( $Points , $OrderObj[ 'order_userid' ] , 'price' ) ;
            $CurrencyValue  = srp_formatted_price( round_off_type_for_currency( $ConvertedValue ) ) ;
            ?>
            <tfoot>
                <tr class="cart-total">
                    <th><?php echo do_shortcode( get_option( 'rs_total_earned_point_caption_thank_you' ) ) ; ?></th>
                    <td><?php
                        echo custom_message_in_thankyou_page( $Points , $CurrencyValue , 'rs_show_hide_equivalent_price_for_points_thankyou' , 'rs_show_hide_custom_msg_for_points_thankyou' , 'rs_custom_message_for_points_thankyou' , $PaymentPlanPoints ) ;
                        ?></td>
                </tr>
            </tfoot>
            <?php
        }

        public static function complete_message_for_purchase() {
            if ( ! is_user_logged_in() )
                return ;

            if ( get_option( 'rs_product_purchase_activated' ) != 'yes' && get_option( 'rs_buyingpoints_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_award_points_for_cart_or_product_total' , '1' ) == '2' )
                return ;

            $ShowMsg = is_cart() ? get_option( 'rs_show_hide_message_for_total_points' ) : get_option( 'rs_show_hide_message_for_total_points_checkout_page' ) ;
            if ( $ShowMsg == '2' )
                return ;

            if ( check_if_coupon_applied() )
                return ;

            if ( check_if_discount_applied() )
                return ;

            if ( get_option( 'rs_product_purchase_activated' ) == 'yes' ) {
                global $totalrewardpoints_payment_plan ;
                $PaymentPlanPoints = srp_check_is_array( $totalrewardpoints_payment_plan ) ? round_off_type( array_sum( $totalrewardpoints_payment_plan ) ) : 0 ;
                $Points            = $PaymentPlanPoints + apply_filters( 'srp_buying_points_for_payment_plan_in_cart' , 0 ) ;
            } elseif ( get_option( 'rs_buyingpoints_activated' ) == 'yes' ) {
                $Points = apply_filters( 'srp_buying_points_for_payment_plan_in_cart' , 0 ) ;
            }

            if ( empty( $Points ) && class_exists( 'SUMOPaymentPlans' ) ) {
                $ShowMsgForPaymentPlan = is_cart() ? get_option( 'rs_show_hide_message_for_total_payment_plan_points' ) : get_option( 'rs_show_hide_message_for_total_points_checkout_page' ) ;
                if ( $ShowMsgForPaymentPlan == '1' ) {
                    $TotalPointsMsg = is_cart() ? get_option( 'rs_message_payment_plan_total_price_in_cart' ) : get_option( 'rs_message_payment_plan_total_price_in_checkout' ) ;
                    $Divclass1      = is_cart() ? 'sumo_reward_points_payment_plan_complete_message' : 'rs_complete_payment_plan_message_checkout' ;
                    $Divclass2      = is_cart() ? 'rs_cart_message' : 'rs_checkout_message' ;
                    ?>
                    <div class="woocommerce-info <?php echo $Divclass1 ; ?> <?php echo $Divclass2 ; ?>">
                        <?php echo do_shortcode( $TotalPointsMsg ) ; ?>
                    </div>
                    <?php
                }
            } else {
                $Points = total_points_for_current_purchase( WC()->cart->total , get_current_user_id() ) ;
                if ( empty( $Points ) )
                    return ;

                $TotalPointsMsg = is_cart() ? get_option( 'rs_message_total_price_in_cart' ) : get_option( 'rs_message_total_price_in_checkout' ) ;
                $Divclass1      = is_cart() ? 'sumo_reward_points_complete_message' : 'rs_complete_message_checkout' ;
                $Divclass2      = is_cart() ? 'rs_cart_message' : 'rs_checkout_message' ;
                ?>
                <div class="woocommerce-info <?php echo $Divclass1 ; ?> <?php echo $Divclass2 ; ?>">
                    <?php echo do_shortcode( $TotalPointsMsg ) ; ?>
                </div>
                <?php
            }
        }

        /* Modified Points for Products */

        public static function modified_points_for_products() {
            $Points         = array() ;
            $OriginalPoints = self::original_points_for_product() ;
            if ( ! srp_check_is_array( $OriginalPoints ) )
                return $Points ;

            foreach ( $OriginalPoints as $ProductId => $Point ) {
                $ModifiedPoints       = self::coupon_points_conversion( $ProductId , $Point ) ;
                if ( ! empty( $ModifiedPoints ) )
                    $Points[ $ProductId ] = $ModifiedPoints ;
            }

            return $Points ;
        }

        /* Original Points for Products */

        public static function original_points_for_product() {
            $user_id = get_current_user_id() ;
            if ( check_banning_type( $user_id ) == 'earningonly' || check_banning_type( $user_id ) == 'both' )
                return array() ;

            global $totalrewardpoints ;
            $points = array() ;
            foreach ( WC()->cart->cart_contents as $value ) {
                if ( block_points_for_salepriced_product( $value[ 'product_id' ] , $value[ 'variation_id' ] ) == 'yes' )
                    continue ;

                $args                 = array(
                    'productid'   => $value[ 'product_id' ] ,
                    'variationid' => $value[ 'variation_id' ] ,
                    'item'        => $value ,
                        ) ;
                $Points               = check_level_of_enable_reward_point( $args ) ;
                $user_role_percentage = RSMemberFunction::earn_points_percentage( $user_id , ( float ) $Points ) ;
                if ( empty( $user_role_percentage ) )
                    continue ;

                $totalrewardpoints = $Points ;
                $ProductId         = ! empty( $value[ 'variation_id' ] ) ? $value[ 'variation_id' ] : $value[ 'product_id' ] ;

                if ( ! empty( $totalrewardpoints ) )
                    $points[ $ProductId ] = $Points ;
            }
            return $points ;
        }

        public static function coupon_points_conversion( $ProductId , $Points , $extra_args = array() ) {

            if ( empty( $Points ) )
                return $Points ;

            $DiscountedTotal = WC()->cart->coupon_discount_amounts ;
            if ( ! srp_check_is_array( $DiscountedTotal ) )
                return $Points ;

            $DiscountedTotal = array_sum( array_values( $DiscountedTotal ) ) ;
            $CouponAmounts   = self::get_product_price_for_individual_product( $ProductId , $Points , $DiscountedTotal ) ;
            if ( ! srp_check_is_array( $CouponAmounts ) )
                return $Points ;

            $ConversionRate  = array() ;
            $ConvertedPoints = 0 ;

            $product_price = self::get_product_price_in_cart( $extra_args ) ;

            foreach ( WC()->cart->applied_coupons as $CouponCode ) {
                $CouponObj    = new WC_Coupon( $CouponCode ) ;
                $CouponObj    = srp_coupon_obj( $CouponObj ) ;
                $ProductList  = $CouponObj[ 'product_ids' ] ;
                $CouponAmount = $CouponAmounts[ $CouponCode ][ $ProductId ] ;
                $LineTotal    = self::get_product_price_for_included_products( $ProductList ) ;

                if ( empty( $ProductList ) && $product_price ) {
                    $ConvertedPoints = $DiscountedTotal / $product_price ;
                } else if ( $LineTotal ) {
                    $ConvertedPoints = $CouponAmount / $LineTotal ;
                }

                $ConvertedAmount  = $ConvertedPoints * $Points ;
                if ( $Points > $ConvertedAmount )
                    $ConversionRate[] = $Points - $ConvertedAmount ;
            }

            return end( $ConversionRate ) ;
        }

        public static function get_product_price_for_individual_product( $ProductId , $Points , $DiscountedTotal ) {
            $CouponAmount = array() ;
            foreach ( WC()->cart->applied_coupons as $CouponCode ) {
                $CouponObj   = new WC_Coupon( $CouponCode ) ;
                $CouponObj   = srp_coupon_obj( $CouponObj ) ;
                $ProductList = $CouponObj[ 'product_ids' ] ;
                if ( ! empty( $ProductList ) ) {
                    if ( in_array( $ProductId , $ProductList ) )
                        $CouponAmount[ $CouponCode ][ $ProductId ] = $DiscountedTotal ;
                } else {
                    $CouponAmount[ $CouponCode ][ $ProductId ] = $DiscountedTotal ;
                }
            }
            return $CouponAmount ;
        }

        public static function get_product_price_for_included_products( $ProductList ) {
            $LineTotal = array() ;
            foreach ( WC()->cart->cart_contents as $Item ) {
                $ProductId   = ! empty( $Item[ 'variation_id' ] ) ? $Item[ 'variation_id' ] : $Item[ 'product_id' ] ;
                if ( in_array( $ProductId , $ProductList ) )
                    $LineTotal[] = $Item[ 'line_subtotal' ] ;
            }
            return array_sum( $LineTotal ) ;
        }

        public static function get_product_price_in_cart( $referrer_args = array() ) {
            $Price = array() ;
            foreach ( WC()->cart->cart_contents as $Items ) {
                $args = array(
                    'productid'   => $Items[ 'product_id' ] ,
                    'variationid' => $Items[ 'variation_id' ] ,
                    'item'        => $Items ,
                        ) ;

                $Points            = check_level_of_enable_reward_point( $args ) ;
                $totalrewardpoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $Points ) ;

                if ( srp_check_is_array( $referrer_args ) ) {
                    $args              = array_merge( $args , $referrer_args ) ;
                    $Points            = check_level_of_enable_reward_point( $args ) ;
                    $totalrewardpoints = RSMemberFunction::earn_points_percentage( $args[ 'referred_user' ] , ( float ) $Points ) ;
                }

                if ( empty( $totalrewardpoints ) )
                    continue ;

                $Price[] = $Items[ 'line_subtotal' ] ;
            }
            return array_sum( $Price ) ;
        }

    }

    RSFrontendAssets::init() ;
}