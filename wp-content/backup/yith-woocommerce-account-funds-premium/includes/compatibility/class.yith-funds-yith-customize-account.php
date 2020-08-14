<?php
if( !defined('ABSPATH' ) ){
    exit;
}


if( !class_exists( 'YITH_Funds_YITH_Customize_Account_Compatibility')){
    /**
     * @author YITHEMES
     * @since 1.0.6
     * Class YITH_Funds_YITH_Customize_Account_Compatibility
     */
    class YITH_Funds_YITH_Customize_Account_Compatibility{

        protected static $_instance;

        public function __construct()
        {
            global $YITH_FUNDS;

           
            remove_action( 'init', array( $YITH_FUNDS,'add_funds_endpoints' ) ,10 );
            remove_action( 'init', array( $YITH_FUNDS, 'rewrite_rules' ), 20 );
            remove_action( 'template_redirect', array( $YITH_FUNDS, 'show_funds_endpoint' ), 100 );
            remove_filter( 'the_content', array( $YITH_FUNDS, 'show_endpoints_content'), 20 );
            remove_action( 'woocommerce_before_my_account', array( $YITH_FUNDS, 'show_customer_funds') );
            remove_action( 'woocommerce_before_my_account', array( $YITH_FUNDS, 'show_customer_make_deposit_form'), 20 );
            remove_action( 'woocommerce_before_my_account', array( $YITH_FUNDS, 'show_customer_recent_history'), 30 );


            add_filter( 'ywf_get_endpoint_url', array( $this, 'get_endpoint_url'), 10 ,3 );
            add_filter( 'ywf_make_deposit_slug', array( $this, 'get_make_deposit_slug')  );
        }

        /**
         * get current endpoint url
         * @author YITHEMES
         * @since 1.0.6
         * @param $url
         * @param $type
         * @param $args
         * @return string
         */
        public function get_endpoint_url( $url, $type, $args ){

            $id = $type == 'make_a_deposit' ? 'make_a_deposit' : 'view_history';

            $slug = get_option( 'woocommerce_myaccount_'.$id.'_endpoint' );

            if( count( $args ) > 0 ) {
                $url = esc_url( add_query_arg( $args, wc_get_page_permalink( 'myaccount' ) . $slug ) );
            }
            else{
                $url = esc_url( wc_get_page_permalink( 'myaccount' ) . $slug );
            }
            return $url;
        }

        /**
         * @author YITHEMES
         * @since 1.0.6
         * @return YITH_Funds_YITH_Customize_Account_Compatibility
         */
        public static function get_instance(){

            if( is_null( self::$_instance ) ){
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * get make a deposit slug
         * @author YITHEMES
         * @since 1.0.6
         * @param $slug
         * @return mixed|void
         */
        public function get_make_deposit_slug( $slug ){

            $slug = get_option( 'woocommerce_myaccount_make_a_deposit_endpoint' , $slug);

            return $slug;
        }
    }
}

function YITH_Funds_YITH_Customize_Account_Compatibility(){
   return  YITH_Funds_YITH_Customize_Account_Compatibility::get_instance();
}