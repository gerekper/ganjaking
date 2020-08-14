<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}

if( !class_exists( 'YITH_PayPal_Adaptive_Payments_Integrations' ) ){
    
    class YITH_PayPal_Adaptive_Payments_Integrations{
        protected static  $instance;

        public function __construct()
        {
            if( self::is_multivendor_active() ){
                require_once( YITH_PAYPAL_ADAPTIVE_DIR.'/integrations/class.yith-multivendor-integration.php' );
            }
                        
            
        }

        /**
         * @return YITH_Delivery_Date_Integrations
         */
        public static function get_instance()
        {
            if( is_null( self::$instance )){
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * check if YITH Multi Vendor is activated
         * @author YITHEMES
         * @since 1.0.0
         * @return bool
         */
        public static function is_multivendor_active(){
            return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;
        }

        /**
         * check if YITH Customize My Account Page is activated
         * @author YITHEMES
         * @since 1.0.0
         * @return bool
         */
        public static function is_customize_my_account_active(){
            
            return defined( 'YITH_WCMAP_PREMIUM' ) && YITH_WCMAP_PREMIUM;
        }
    }
}


function YITH_PayPal_Adaptive_Payments_Integrations(){

    return YITH_PayPal_Adaptive_Payments_Integrations::get_instance();
}
