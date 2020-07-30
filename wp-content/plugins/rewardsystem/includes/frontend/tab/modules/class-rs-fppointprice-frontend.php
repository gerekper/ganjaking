<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSPointPriceFrontend' ) ) {

    class RSPointPriceFrontend {

        public static function init() {
            add_action( 'wp_head' , array( __CLASS__ , 'hide_wc_coupon_field' ) ) ;

            add_action( 'wp_head' , array( __CLASS__ , 'redirect_if_coupon_removed' ) ) ;

            add_action( 'woocommerce_before_add_to_cart_button' , array( __CLASS__ , 'display_point_price_for_booking' ) , 10 ) ;

            add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'replace_coupon_notice_for_point_price' ) ) ;

            add_filter( 'woocommerce_checkout_coupon_message' , array( __CLASS__ , 'replace_coupon_notice_for_point_price' ) , 1 ) ;

            add_filter( 'woocommerce_is_purchasable' , array( __CLASS__ , 'is_purchasable_simple_product' ) , 10 , 2 ) ;
            // Commented this hook on version 24.2.3
//            add_filter( 'woocommerce_show_variation_price' , array( __CLASS__ , 'is_purchasable_variable_product' ) , 10 , 3 ) ;

            add_filter( 'woocommerce_get_variation_price_html' , array( __CLASS__ , 'point_price_for_variable_product' ) , 10 , 2 ) ;

            add_action( 'woocommerce_add_to_cart' , array( __CLASS__ , 'set_point_price_for_products_in_session' ) , 1 , 5 ) ;

            add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'save_point_price_info_in_order' ) ) ;

            add_filter( 'woocommerce_cart_total' , array( __CLASS__ , 'total_in_cart_with_shipping_and_tax' ) ) ;

            add_filter( 'woocommerce_cart_item_price' , array( __CLASS__ , 'product_price_in_cart' ) , 999 , 3 ) ;

            add_filter( 'woocommerce_cart_item_subtotal' , array( __CLASS__ , 'line_total_in_cart' ) , 10 , 3 ) ;

            add_filter( 'woocommerce_cart_subtotal' , array( __CLASS__ , 'subtotal_in_cart' ) , 10 , 3 ) ;

            add_filter( 'woocommerce_calculated_total' , array( __CLASS__ , 'total_in_cart' ) , 10 , 2 ) ;

            add_filter( 'woocommerce_order_formatted_line_subtotal' , array( __CLASS__ , 'line_subtotal_in_order_detail' ) , 8 , 3 ) ;

            add_filter( 'woocommerce_order_subtotal_to_display' , array( __CLASS__ , 'subtotal_in_order_detail' ) , 8 , 3 ) ;

            add_filter( 'woocommerce_add_to_cart_validation' , array( __CLASS__ , 'sell_individually_functionality' ) , 9 , 5 ) ;

            add_filter( 'vartable_add_to_cart_validation' , array( __CLASS__ , 'sell_individually_functionality' ) , 10 , 5 ) ; //compatability with woo-variations-table plugin

            add_filter( 'woocommerce_available_payment_gateways' , array( __CLASS__ , 'unset_gateways_for_point_price_products' ) , 10 , 1 ) ;
        }

        /* Display the Point Price Label in Cart Item Price */

        public static function product_price_in_cart( $product_price , $item , $item_key ) {
            $product_price = self::get_point_price_with_label( $product_price , $item , $item_key , 'item_price' ) ;
            return $product_price ;
        }

        /* Display the Point Price Label in Cart Item Total */

        public static function line_total_in_cart( $product_price , $item , $item_key ) {
            $product_price = self::get_point_price_with_label( $product_price , $item , $item_key , 'item_total' ) ;
            return $product_price ;
        }

        public static function get_point_price_with_label( $product_price , $item , $item_key , $position ) {
            if ( get_option( 'rs_enable_disable_point_priceing' ) == '2' )
                return $product_price ;

            $VisibilityForPointPrice = (get_option( 'rs_point_price_visibility' ) == 1) ? true : is_user_logged_in() ;
            if ( ! $VisibilityForPointPrice )
                return $product_price ;

            $ProductId            = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
            $Points               = calculate_point_price_for_products( $ProductId ) ;
            $PointPriceType       = check_display_price_type( $ProductId ) ;
            $PointPriceForProduct = implode( "," , $Points ) ;
            if ( empty( $PointPriceForProduct ) )
                return $product_price ;

            $IndividualPointsForProduct = ($position == 'item_price') ? $PointPriceForProduct : $PointPriceForProduct * $item[ 'quantity' ] ;
            if ( get_option( 'rs_enable_product_category_level_for_points_price' ) == 'no' ) {  //Quick Setup
                $EnablePointPrice = get_option( 'rs_local_enable_disable_point_price_for_product' ) ;
                if ( $EnablePointPrice == '2' )
                    return $product_price ;

                $product_price = self::point_price_value( $item , $PointPriceType , $IndividualPointsForProduct ) ;
                return $product_price ;
            } else {    //Advance Setup
                $EnablePointPriceForVariableinProductLevel = get_post_meta( $ProductId , '_enable_reward_points_price' , true ) ;
                $EnablePointPriceForSimpleinProductLevel   = get_post_meta( $ProductId , '_rewardsystem_enable_point_price' , true ) ;
                if ( $EnablePointPriceForVariableinProductLevel == '1' || $EnablePointPriceForSimpleinProductLevel == 'yes' ) {
                    $product_price = self::point_price_value( $item , $PointPriceType , $IndividualPointsForProduct ) ;
                    return $product_price ;
                }
            }
            return $product_price ;
        }

        public static function point_price_value( $item , $PointPriceType , $IndividualPointsForProduct ) {
            $PointsAfterRoundOff = round_off_type( $IndividualPointsForProduct ) ;
            if ( $PointPriceType == '1' ) { //Currency & Point Pricing
                $ProductPriceToDisplay = display_point_price_value( $IndividualPointsForProduct , true ) ;
                $product_price         = wc_price( $item[ 'data' ]->get_price() ) . $ProductPriceToDisplay ;
                return $product_price ;
            } else {  //Only Point Price
                $product_price = display_point_price_value( $IndividualPointsForProduct ) ;
                return $product_price ;
            }
        }

        /* Display Point Price Label in Order Detail for Subtotal */

        public static function subtotal_in_order_detail( $line_total , $id , $order ) {
            if ( get_option( 'rs_enable_disable_point_priceing' ) == 2 )
                return $line_total ;

            $VisibilityForPointPrice = (get_option( 'rs_point_price_visibility' ) == 1) ? true : is_user_logged_in() ;
            if ( ! $VisibilityForPointPrice )
                return $line_total ;

            $OrderObj = srp_order_obj( $order ) ;
            $Gateway  = get_post_meta( $OrderObj[ 'order_id' ] , '_payment_method' , true ) ;
            if ( $Gateway != 'reward_gateway' )
                return $line_total ;

            $Points     = array() ;
            $OtherValue = array() ;
            foreach ( $order->get_items()as $item ) {
                $ProductId            = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                $PointPriceData       = calculate_point_price_for_products( $ProductId ) ;
                $CheckIfBundleProduct = isset( $item[ 'bundled_by' ] ) ? $item[ 'bundled_by' ] : 0 ;
                if ( ! empty( $PointPriceData[ $ProductId ] ) && $CheckIfBundleProduct == null ) {
                    $Points[] = $PointPriceData[ $ProductId ] * $item[ 'qty' ] ;
                } else {
                    $Points[] = redeem_point_conversion( $item[ 'line_subtotal' ] , $OrderObj[ 'order_userid' ] ) ;
                }
            }
            $TotalPoints   = round_off_type( array_sum( $Points ) ) ;
            $product_price = display_point_price_value( $TotalPoints ) ;
            return $product_price ;
        }

        /* Display Point Price Label in Order Detail for Line Total */

        public static function line_subtotal_in_order_detail( $line_total , $id , $order ) {
            if ( get_option( 'rs_enable_disable_point_priceing' ) == 2 )
                return $line_total ;

            $VisibilityForPointPrice = (get_option( 'rs_point_price_visibility' ) == 1) ? true : is_user_logged_in() ;
            if ( ! $VisibilityForPointPrice )
                return $line_total ;

            $OrderObj = srp_order_obj( $order ) ;
            $Gateway  = get_post_meta( $OrderObj[ 'order_id' ] , '_payment_method' , true ) ;
            if ( $Gateway != 'reward_gateway' )
                return $line_total ;

            $ProductId      = ! empty( $id[ 'variation_id' ] ) ? $id[ 'variation_id' ] : $id[ 'product_id' ] ;
            $PointPriceData = calculate_point_price_for_products( $ProductId ) ;
            if ( ! empty( $PointPriceData[ $ProductId ] ) ) {
                $Points        = $PointPriceData[ $ProductId ] * $id[ 'qty' ] ;
                $product_price = display_point_price_value( $Points ) ;
            } else {
                $PointPriceLabel = str_replace( "/" , "" , get_option( 'rs_label_for_point_value' ) ) ;
                $Points          = redeem_point_conversion( $id[ 'line_subtotal' ] , $OrderObj[ 'order_userid' ] ) ;
                if ( get_option( 'rs_sufix_prefix_point_price_label' ) == '1' ) {
                    $product_price = '<span style="margin-left:10px;">' . $PointPriceLabel . '</span>' . $Points ;
                } else {
                    $product_price = $Points . '<span style="margin-left:10px;">' . $PointPriceLabel . '</span>' ;
                }
            }
            return $product_price ;
        }

        /* Hide WooCommerce Coupon Field when Only Point Price Product is in Cart */

        public static function hide_wc_coupon_field() {
            if ( ! is_user_logged_in() )
                return ;

            if ( is_cart() || is_checkout() ) {
                $PointPriceType = array() ;
                $CartObj        = array() ;
                foreach ( WC()->cart->cart_contents as $item ) {
                    $ProductId        = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                    $PointPriceType[] = check_display_price_type( $ProductId ) ;
                    $PointPriceData   = calculate_point_price_for_products( $ProductId ) ;
                    if ( empty( $PointPriceData[ $ProductId ] ) )
                        continue ;

                    $CartObj[] = $PointPriceData[ $ProductId ] ;
                }
                if ( srp_check_is_array( $CartObj ) || in_array( 2 , $PointPriceType ) )
                    echo woocommerce_coupon_field( 'hide' ) ;
            }
        }

        /* Display Point Price for Booking Product */

        public static function display_point_price_for_booking() {
            if ( class_exists( 'WC_Bookings' ) ) {
                ?>
                <div class="wc-bookings-booking-cost1"></div> 
                <?php
            }
        }

        /* Replace Coupon Message for Point Price */

        public static function replace_coupon_notice_for_point_price( $message ) {
            $PointPriceType  = array() ;
            $PointPriceValue = array() ;
            foreach ( WC()->cart->cart_contents as $item ) {
                $ProductId         = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                $PointPriceType[]  = check_display_price_type( $ProductId ) ;
                $CheckIfEnable     = calculate_point_price_for_products( $ProductId ) ;
                if ( ! empty( $CheckIfEnable[ $ProductId ] ) )
                    $PointPriceValue[] = $CheckIfEnable[ $ProductId ] ;
            }

            if ( ! srp_check_is_array( $PointPriceValue ) && ! in_array( 2 , $PointPriceType ) )
                return $message ;

            $message = get_option( 'rs_show_hide_message_errmsg_for_point_price_coupon' ) == 1 ? get_option( 'rs_errmsg_for_redeem_in_point_price_prt' ) : "" ;
            if ( is_cart() ) {
                if ( $message ) {
                    ?>
                    <div class="woocommerce-info"><?php echo $message ; ?></div>
                    <?php
                }
            }
            if ( is_checkout() ) {
                if ( ! $message )
                    $message = "<span class='displaymessage'></span>" ;

                return $message ;
            }
        }

        /* Display Point Price Label in Cart for Subtotal */

        public static function subtotal_in_cart( $CartSubTotal , $compound , $CartObj ) {
            if ( get_option( 'rs_enable_disable_point_priceing' ) == '2' )
                return $CartSubTotal ;

            $VisibilityForPointPrice = (get_option( 'rs_point_price_visibility' ) == 1) ? true : is_user_logged_in() ;
            if ( ! $VisibilityForPointPrice )
                return $CartSubTotal ;

            $OnlyPointPriceValue     = array() ;
            $CurrencyPointPriceValue = array() ;
            foreach ( $CartObj->cart_contents as $item ) {
                $ProductId = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                if ( check_display_price_type( $ProductId ) == 2 ) {
                    $CheckIfEnable         = calculate_point_price_for_products( $ProductId ) ;
                    if ( ! empty( $CheckIfEnable[ $ProductId ] ) )
                        $OnlyPointPriceValue[] = $CheckIfEnable[ $ProductId ] * $item[ 'quantity' ] ;
                } elseif ( check_display_price_type( $ProductId ) == 1 ) {
                    $CheckIfEnable = calculate_point_price_for_products( $ProductId ) ;
                    if ( ! empty( $CheckIfEnable[ $ProductId ] ) ) {
                        $CurrencyPointPriceValue[] = $CheckIfEnable[ $ProductId ] * $item[ 'quantity' ] ;
                    } else {
                        $CurrencyPointPriceValue[] = $item[ 'line_subtotal' ] ;
                    }
                }
            }
            $CurrencyPointPriceAmnt = array_sum( $CurrencyPointPriceValue ) ;
            if ( ! empty( $CurrencyPointPriceAmnt ) ) {
                $PointPrice = display_point_price_value( $CurrencyPointPriceAmnt ) ;
                return $CartSubTotal . "/$PointPrice" ;
            }
            $OnlyPointPriceAmnt = array_sum( $OnlyPointPriceValue ) ;
            if ( ! empty( $OnlyPointPriceAmnt ) ) {
                $CartSubTotal = display_point_price_value( $OnlyPointPriceAmnt ) ;
                return $CartSubTotal ;
            }
            return $CartSubTotal ;
        }

        /* Display Point Price Label in Cart for Total */

        public static function total_in_cart( $CartTotal , $CartObj ) {
            if ( get_option( 'rs_enable_disable_point_priceing' ) == '2' )
                return $CartTotal ;

            $VisibilityForPointPrice = (get_option( 'rs_point_price_visibility' ) == 1) ? true : is_user_logged_in() ;
            if ( ! $VisibilityForPointPrice )
                return $CartTotal ;

            $PointPriceValue = array() ;
            foreach ( $CartObj->cart_contents as $item ) {
                $ProductId = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                if ( ( check_display_price_type( $ProductId ) == NULL) || check_display_price_type( $ProductId ) != 2 )
                    continue ;

                $CheckIfEnable     = calculate_point_price_for_products( $ProductId ) ;
                if ( ! empty( $CheckIfEnable[ $ProductId ] ) )
                    $PointPriceValue[] = $CheckIfEnable[ $ProductId ] ;
            }

            return srp_check_is_array( $PointPriceValue ) ? array_sum( $PointPriceValue ) : $CartTotal ;
        }

        /* Display Point Price Label in Cart for Total with Shipping and Tax */

        public static function total_in_cart_with_shipping_and_tax( $price ) {
            if ( get_option( 'rs_enable_disable_point_priceing' ) == '2' )
                return $price ;

            $VisibilityForPointPrice = (get_option( 'rs_point_price_visibility' ) == 1) ? true : is_user_logged_in() ;
            if ( ! $VisibilityForPointPrice )
                return $price ;

            $EnablePointPriceValue       = $LineTotal                   = $PointPriceTax               = $ItemPointsTotal             = $EnablePointPriceForVariable = $EnablePointPriceforSimple   = array() ;
            $ShippingTotal               = WC()->shipping->shipping_total ;
            $ShippingTax                 = WC()->shipping->shipping_taxes ;
            $ShippingTaxTotal            = array_sum( $ShippingTax ) ;
            $CouponAmount                = WC()->cart->get_cart_discount_total() ;
            $ShippingCost                = $ShippingTotal + $ShippingTaxTotal ;
            foreach ( WC()->cart->cart_contents as $key ) {
                $ProductId              = ! empty( $key[ 'variation_id' ] ) ? $key[ 'variation_id' ] : $key[ 'product_id' ] ;
                $Points                 = calculate_point_price_for_products( $ProductId ) ;
                $PointPriceType[]       = check_display_price_type( $ProductId ) ;
                $PriceforRegularProduct = empty( $Points[ $ProductId ] ) ? point_price_based_on_conversion( $ProductId ) : $Points[ $ProductId ] ;
                if ( get_option( 'rs_enable_product_category_level_for_points_price' ) == 'no' ) {  //Quick Setup
                    $EnablePointPriceValue[] = get_option( 'rs_local_enable_disable_point_price_for_product' ) ;
                } else {  //Advance Setup
                    $EnablePointPriceForVariable[] = get_post_meta( $ProductId , '_enable_reward_points_price' , true ) ;
                    $EnablePointPriceforSimple[]   = get_post_meta( $ProductId , '_rewardsystem_enable_point_price' , true ) ;
                }
                $CheckIfBundledProduct = isset( $key[ 'bundled_by' ] ) ? $key[ 'bundled_by' ] : 0 ;
                if ( $CheckIfBundledProduct == 0 ) {
                    $ItemPointsTotal[] = $PriceforRegularProduct * $key[ 'quantity' ] ;
                    if ( get_option( 'woocommerce_prices_include_tax' ) == 'no' )
                        $PointPriceTax[]   = $key[ 'line_subtotal_tax' ] ;
                } else {
                    $LineTotal[]     = $key[ 'line_subtotal' ] ;
                    $PointPriceTax[] = $key[ 'line_subtotal_tax' ] ;
                }
            }
            $FeeAmnt                   = apply_filters( 'rs_points_for_additional_fee' , WC()->cart->get_fee_total() ) ;
            $ShippingConversionValue   = redeem_point_conversion( $ShippingCost , get_current_user_id() ) ;
            $TotalPointConversionValue = (array_sum( $LineTotal ) + array_sum( $PointPriceTax )) ;
            $TotalPoint                = (array_sum( $ItemPointsTotal ) + redeem_point_conversion( $TotalPointConversionValue , get_current_user_id() )) - $CouponAmount ;
            $TotalPointsWithTax        = $TotalPoint + $ShippingConversionValue + $FeeAmnt ;
            $TotalPointAfterRoundOff   = round_off_type( $TotalPointsWithTax ) ;
            $CartTotalToDisplay        = display_point_price_value( $TotalPointAfterRoundOff , true ) ;

            if ( in_array( "yes" , $EnablePointPriceforSimple ) || in_array( "1" , $EnablePointPriceForVariable ) || in_array( "1" , $EnablePointPriceValue ) ) {
                if ( in_array( '2' , $PointPriceType ) ) {
                    $DisplayTotal = str_replace( "/" , "" , $CartTotalToDisplay ) ;
                    return $DisplayTotal ;
                } elseif ( in_array( '1' , $PointPriceType ) ) {
                    if ( array_sum( $ItemPointsTotal ) > 0 || $CheckIfBundledProduct != 0 ) {
                        if ( $TotalPointAfterRoundOff == 0 )
                            return $price ;

                        return $price . $CartTotalToDisplay ;
                    }
                }
            }
            return $price ;
        }

        /* Check If Purchaseable Point Price Product - Simple */

        public static function is_purchasable_simple_product( $Purchaseable , $ProductObj ) {
            $ProductId = product_id_from_obj( $ProductObj ) ;
            if ( check_display_price_type( $ProductId ) == '2' )
                return $Purchaseable ;

            $CheckIfEnable = calculate_point_price_for_products( $ProductId ) ;
            if ( ! empty( $CheckIfEnable[ $ProductId ] ) )
                return true ;

            return $Purchaseable ;
        }

        /* Check If Purchaseable Point Price Product - Variable */

        public static function is_purchasable_variable_product( $Purchaseable , $obj , $ProductObj ) {
            $ProductId = product_id_from_obj( $ProductObj ) ;
            if ( check_display_price_type( $ProductId ) == '2' )
                return $Purchaseable ;

            $CheckIfEnable = calculate_point_price_for_products( $ProductId ) ;
            if ( ! empty( $CheckIfEnable[ $ProductId ] ) )
                return true ;

            return $Purchaseable ;
        }

        /* Display Point Price Label for Variable Product */

        public static function point_price_for_variable_product( $Price , $ProductObj ) {
            if ( ! is_user_logged_in() )
                return $Price ;

            $VariationId = product_id_from_obj( $ProductObj ) ;
            if ( check_display_price_type( $VariationId ) != '2' )
                return $Price ;

            $CheckIfEnable = calculate_point_price_for_products( $VariationId ) ;
            if ( ! empty( $CheckIfEnable[ $VariationId ] ) )
                return $Price ;

            $Price = display_point_price_value( $CheckIfEnable[ $VariationId ] ) ;
            return $Price ;
        }

        /* Redirect to Cart if Coupon Removed */

        public static function redirect_if_coupon_removed() {
            ?>
            <script type='text/javascript'>
                jQuery( document ).ready( function () {
                    jQuery( '.displaymessage' ).parent().hide() ;
                } ) ;
            </script>
            <?php
            if ( isset( $_GET[ 'remove_coupon' ] ) )
                wp_redirect( wc_get_page_permalink( 'cart' ) ) ;
        }

        /* Set Point Price Value in Session */

        public static function set_point_price_for_products_in_session( $cart_item_key , $product_id = null , $quantity = null , $variation_id = null , $variation = null ) {
            $ProductId       = ! empty( $variation_id ) ? $variation_id : $product_id ;
            $PointPriceValue = calculate_point_price_for_products( $ProductId ) ;
            WC()->session->set( $cart_item_key . 'point_price_for_product' , $PointPriceValue ) ;
        }

        /* Save Point Price Detail in Order */

        public static function save_point_price_info_in_order( $orderid ) {
            $PointPriceInfo = array() ;
            foreach ( WC()->cart->cart_contents as $key => $value ) {
                if ( WC()->session->get( $key . 'point_price_for_product' ) )
                    $PointPriceInfo[] = WC()->session->get( $key . 'point_price_for_product' ) ;
            }
            update_post_meta( $orderid , 'point_price_for_product_in_order' , $PointPriceInfo ) ;
            WC()->session->set( 'auto_redeemcoupon' , 'yes' ) ;
        }

        /* Check If Normal Product is purchased with Point Price Product */

        public static function sell_individually_functionality( $valid , $product_id , $quantity , $variation_id = NULL , $variations = NULL ) {
            if ( get_option( 'rs_enable_disable_point_priceing' ) == 2 )
                return $valid ;

            $ProductIdAdded = isset( $variation_id ) ? $variation_id : $product_id ;
            if ( ! is_user_logged_in() && check_display_price_type( $ProductIdAdded ) ) {
                wc_add_notice( do_shortcode( get_option( 'rs_point_price_product_added_to_cart_guest_errmsg' , 'Only registered users can purchase this product. Click the link to create an account ([loginlink]).' ) ) , 'error' ) ;
                return ;
            }

            if ( ! function_exists( 'WC' ) ) {
                return $valid ;
            }

            if ( ! srp_check_is_array( WC()->cart->get_cart() ) ) {
                return $valid ;
            }
            foreach ( WC()->cart->get_cart() as $item ) {
                if ( WC()->cart->cart_contents_count > 0 && 1 <= WC()->cart->cart_contents_count ) {
                    $ProductId = product_id_from_obj( $item[ 'data' ] ) ;
                    $valid     = self::check_if_point_price_product_is_added_to_cart( $ProductIdAdded , $ProductId ) ;
                } else {
                    if ( self::check_is_point_pricing_enable( $ProductIdAdded ) ) {
                        WC()->cart->empty_cart() ;
                        wc_add_notice( get_option( 'rs_errmsg_for_normal_product_with_point_price' ) , 'error' ) ;
                        $valid = true ;
                    }
                }
            }
            return $valid ;
        }

        public static function check_is_point_pricing_enable( $ProductId ) {
            $EnablePointPrice = get_post_meta( $ProductId , '_rewardsystem_enable_point_price' , true ) != '' ? get_post_meta( $ProductId , '_rewardsystem_enable_point_price' , true ) : get_post_meta( $ProductId , '_enable_reward_points_price' , true ) ;
            $Points           = get_post_meta( $ProductId , '_rewardsystem__points' , true ) != '' ? get_post_meta( $ProductId , '_rewardsystem__points' , true ) : get_post_meta( $ProductId , 'price_points' , true ) ;
            $DisplayType      = get_post_meta( $ProductId , '_rewardsystem_enable_point_price_type' , true ) != '' ? get_post_meta( $ProductId , '_rewardsystem_enable_point_price_type' , true ) : get_post_meta( $ProductId , '_enable_reward_points_pricing_type' , true ) ;
            if ( ($EnablePointPrice != 'yes') && ($EnablePointPrice != '1') )
                return false ;

            if ( $DisplayType == '2' )
                return false ;

            if ( empty( $Points ) )
                return false ;

            return true ;
        }

        public static function check_if_point_price_product_is_added_to_cart( $ProductIdAdded , $ProductId ) {
            if ( check_display_price_type( $ProductId ) == '2' ) {
                if ( check_display_price_type( $ProductIdAdded ) == '2' ) {
                    if ( $ProductId == $ProductIdAdded ) {
                        wc_add_notice( get_option( 'rs_errmsg_for_point_price_product_with_same' ) , 'error' ) ;
                        return false ;
                    }
                } else {
                    wc_add_notice( get_option( 'rs_errmsg_for_normal_product_with_point_price' ) , 'error' ) ;
                    return false ;
                }
            } else if ( check_display_price_type( $ProductId ) == '' ) {
                if ( check_display_price_type( $ProductIdAdded ) == '2' ) {
                    wc_add_notice( get_option( 'rs_errmsg_for_point_price_product_with_normal' ) , 'error' ) ;
                    return false ;
                }
            } else if ( check_display_price_type( $ProductIdAdded ) == '2' ) {
                if ( check_display_price_type( $ProductId ) == '1' ) {
                    return true ;
                }
            }
            return true ;
        }

        // Shows only SUMO Reward Gateway on using Point price Product
        public static function unset_gateways_for_point_price_products( $gateways ) {
            global $woocommerce ;
            if ( ! srp_check_is_array( $woocommerce->cart->cart_contents ) )
                return $gateways ;

            foreach ( $woocommerce->cart->cart_contents as $key => $values ) {
                $productid = ! empty( $values[ 'variation_id' ] ) ? $values[ 'variation_id' ] : $values[ 'product_id' ] ;
                if ( check_display_price_type( $productid ) != 2 )
                    continue ;

                foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
                    if ( $gateway->id == 'reward_gateway' )
                        continue ;

                    unset( $gateways[ $gateway->id ] ) ;
                }
            }

            return $gateways != 'NULL' ? $gateways : array() ;
        }

    }

    RSPointPriceFrontend::init() ;
}