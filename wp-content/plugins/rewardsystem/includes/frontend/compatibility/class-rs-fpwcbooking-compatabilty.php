<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSBookingCompatibility' ) ) {

    class RSBookingCompatibility {

        public static function init() {
            add_filter( 'woocommerce_fprsmessage_settings' , array( __CLASS__ , 'message_for_booking_products' ) ) ;

            add_action( 'woocommerce_before_single_product' , array( __CLASS__ , 'display_message_for_booking_in_product_page' ) ) ;

            if ( get_option( 'rs_message_before_after_cart_table' ) == '1' ) {
                add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'get_message_for_booking' ) ) ;
            } else {
                add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'get_message_for_booking' ) ) ;
            }
            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'get_message_for_booking' ) ) ;

            add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'display_message_for_booking' ) ) ;

            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'display_message_for_booking' ) ) ;

            add_filter( 'woocommerce_available_payment_gateways' , array( __CLASS__ , 'unset_gateways' ) , 10 , 1 ) ;
        }

        public static function message_for_booking_products( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && '_rs_reward_messages' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'     => __( 'Message in Cart Page for each WooCommerce Booking Product' , SRP_LOCALE ) ,
                        'desc'     => __( 'Enter the Message which will be displayed in each WooCommerce Booking Products added in the Cart' , SRP_LOCALE ) ,
                        'id'       => 'rs_woocommerce_booking_product_cart_message' ,
                        'css'      => 'min-width:550px;' ,
                        'std'      => 'Purchase this [bookingproducttitle] and Earn <strong>[bookingrspoint]</strong> Reward Point ([equalbookingamount])' ,
                        'type'     => 'textarea' ,
                        'newids'   => 'rs_woocommerce_booking_product_cart_message' ,
                        'desc_tip' => true ,
                            ) ;
                    $updated_settings[] = array(
                        'name'     => __( 'Message in Checkout Page for each WooCommerce Booking Product' , SRP_LOCALE ) ,
                        'desc'     => __( 'Enter the Message which will be displayed in each WooCommerce Booking Products in the Checkout' , SRP_LOCALE ) ,
                        'id'       => 'rs_woocommerce_booking_product_checkout_message' ,
                        'css'      => 'min-width:550px;' ,
                        'std'      => 'Purchase this [bookingproducttitle] and Earn <strong>[bookingrspoint]</strong> Reward Point ([equalbookingamount])' ,
                        'type'     => 'textarea' ,
                        'newids'   => 'rs_woocommerce_booking_product_checkout_message' ,
                        'desc_tip' => true ,
                            ) ;
                }
                $updated_settings[] = $section ;
            }
            return $updated_settings ;
        }

        public static function unset_gateways( $available_gateways ) {
            foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
                if ( $gateway->id == 'reward_gateway' && $gateway->enabled == 'yes' )
                    $available_gateways[ $gateway->id ] = $gateway ;
            }
            return $available_gateways != 'NULL' ? $available_gateways : array() ;
        }

        public static function display_message_for_booking_in_product_page() {
            global $post ;
            if ( is_user_logged_in() ) {
                $BanningType = check_banning_type( get_current_user_id() ) ;
                if ( $BanningType == 'earningonly' || $BanningType == 'both' )
                    return ;
            }

            $ProductObj = srp_product_object( $post->ID ) ;
            if ( ! is_object( $ProductObj ) )
                return ;

            if ( srp_product_type( $post->ID ) != 'booking' )
                return ;

            if ( get_post_meta( $post->ID , '_rewardsystemcheckboxvalue' , true ) != 'yes' )
                return ;

            if ( get_post_meta( $post->ID , '_rewardsystem_options' , true ) == '1' ) {
                $Points = do_shortcode( '[sumobookingpoints]' ) ;
                if ( $Points > 0 ) {
                    ?>
                    <div class="woocommerce-info"><?php _e( "Book this Product and Earn <span class='sumobookingpoints'>$Points</span> Points" ) ; ?></div>
                    <?php
                }
            } else {
                ?>
                <div class="woocommerce_booking_variations"><?php _e( "Book this Product and Earn <span class='sumobookingpoints'></span> Points" ) ; ?></div>
                <?php
            }
        }

        public static function get_message_for_booking() {
            if ( ! is_user_logged_in() )
                return ;

            $BanningType = check_banning_type( get_current_user_id() ) ;
            if ( $BanningType == 'earningonly' || $BanningType == 'both' )
                return ;

            global $MsgForBooking ;
            global $PointsForBooking ;
            foreach ( WC()->cart->cart_contents as $key => $item ) {
                $ProductObj = srp_product_object( $item[ 'product_id' ] ) ;
                if ( ! is_object( $ProductObj ) )
                    continue ;

                if ( srp_product_type( $item[ 'product_id' ] ) != 'booking' )
                    continue ;

                $args   = array(
                    'productid'   => $item[ 'product_id' ] ,
                    'variationid' => $item[ 'variation_id' ] ,
                    'item'        => $item ,
                        ) ;
                $Points = check_level_of_enable_reward_point( $args ) ;

                $PointsForBooking[ $item[ 'product_id' ] ] = round_off_type( $Points ) ;

                $BookingPoints = do_shortcode( '[bookingrspoint]' ) ;
                if ( $BookingPoints == 0 )
                    continue ;

                if ( is_cart() )
                    $MsgForBooking[] = do_shortcode( get_option( 'rs_woocommerce_booking_product_cart_message' ) ) . "<br>" ;

                if ( is_checkout() )
                    $MsgForBooking[] = do_shortcode( get_option( 'rs_woocommerce_booking_product_checkout_message' ) ) . "<br>" ;
            }
        }

        public static function display_message_for_booking() {
            if ( get_option( 'rs_show_hide_message_for_each_products' ) == 2 )
                return ;

            global $PointsForBooking ;
            global $MsgForBooking ;
            if ( ! srp_check_is_array( $PointsForBooking ) )
                return ;

            if ( array_sum( $PointsForBooking ) == 0 )
                return ;

            if ( ! srp_check_is_array( $MsgForBooking ) )
                return ;
            ?>
            <div class="woocommerce-info">
                <?php
                foreach ( $MsgForBooking as $Msg ) {
                    echo $Msg ;
                }
                ?>
            </div>
            <?php
        }

    }

    RSBookingCompatibility::init() ;
}