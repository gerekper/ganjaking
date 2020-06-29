<?php
/**
 * Notes class
 *
 * @author  Yithemes
 * @package YITH Desktop Notifications for WooCommerce
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCDN_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}


if ( !class_exists( 'YITH_WCDN_Desktop_Notifications_Options' ) ) {
    /**
     * YITH_WCDN_Desktop_Notifications_Options
     *
     * @since 1.0.0
     */
    class YITH_WCDN_Desktop_Notifications_Options {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCDN_Desktop_Notifications_Options
         * @since 1.0.0
         */
        protected static $instance;


        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCDN_Desktop_Notifications_Options
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$instance ) ) {
                $self::$instance = new $self;
            }

            return $self::$instance;
        }

        /**
         * Constructor
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        private function __construct() {
            
        }

        public function get_notification_type(){
            $array = array(
                'sold' => esc_html__('A product is sold','yith-desktop-notifications-for-woocommerce'),
                'placed' => esc_html__('A new order is placed','yith-desktop-notifications-for-woocommerce'),
                'refund' => esc_html__('An order is refunded','yith-desktop-notifications-for-woocommerce'),
                'out_of_stock' => esc_html__('A product goes out of stock','yith-desktop-notifications-for-woocommerce'),
                'status_changed' => esc_html__('An order is switched to a specific status','yith-desktop-notifications-for-woocommerce'),
                'low_stock'      => esc_html__('A product low of stock','yith-desktop-notifications-for-woocommerce'),

            );

            return apply_filters('yith_wcdn_get_notification_type',$array);
        }
    }
}