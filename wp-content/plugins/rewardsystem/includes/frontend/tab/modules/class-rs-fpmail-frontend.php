<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionForEmailTemplate' ) ) {

    class RSFunctionForEmailTemplate {

        public static function init() {
            add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'subscribe_option' ) ) ;

            add_action( 'wp_head' , array( __CLASS__ , 'redirect_after_unsubscribe' ) ) ;
        }

        /* For Unsubscribe option in My account Page */

        public static function field_for_subcribe( $echo = false ) {
            ob_start() ;
            ?>
            <div class="rs_subscriptionoption">
                <p class="rs_email_subscription">
                    <input type="checkbox" name="subscribeoption" id="subscribeoption" value="yes" <?php checked( "yes" , get_user_meta( get_current_user_id() , 'unsub_value' , true ) ) ; ?>/> <?php echo get_option( 'rs_unsub_field_caption' ) ; ?>
                </p>
            </div>
            <?php
            $content = ob_get_contents() ;
            ob_end_clean() ;
            if ( ! $echo )
                return $content ;

            echo $content ;
        }

        public static function subscribe_option() {
            if ( get_option( 'rs_reward_content' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_your_subscribe_link' ) == 2 )
                return ;

            self::field_for_subcribe( true ) ;
        }

        public static function redirect_after_unsubscribe() {
            if ( isset( $_GET[ 'userid' ] ) && isset( $_REQUEST[ 'nonce' ] ) ) {
                if ( ($_GET[ 'unsub' ] == 'yes' ) ) {
                    update_user_meta( $_GET[ 'userid' ] , 'unsub_value' , $_GET[ 'unsub' ] ) ;
                    wp_safe_redirect( site_url() ) ;
                    exit() ;
                }
            }
        }

    }

    RSFunctionForEmailTemplate::init() ;
}