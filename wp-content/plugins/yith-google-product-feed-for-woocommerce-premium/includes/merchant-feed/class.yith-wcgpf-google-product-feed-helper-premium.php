<?php
/**
 * Class Helper Generate Product Feed
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Helper_Premium' ) ) {
    /**
     * YITH_WCGPF_Helper_Premium
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Helper_Premium extends YITH_WCGPF_Helper {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCGPF_Helper
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCGPF_Helper
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        public $feed_type;
        public $merchant;
        public $feed_id;

        /**
         * @type array
         */
        public $allowed_merchant = array();

        public function __construct() {
            parent::__construct();
        }

        public function generate_feed() {
            if ( $this->is_url_for_generate_feed() ) {
                list( $type, $merchant, $id ) = $this->get_params_for_generating_feed();
                $premium_suffix = defined( 'YITH_WCGPF_PREMIUM' ) && YITH_WCGPF_PREMIUM ? '_Premium' : '';
                $provider       = 'YITH_WCGPF_Generate_Feed_' . $merchant . $premium_suffix;
                if(class_exists($provider)) {
                    $limit         = isset( $_GET[ 'limit' ] ) ? $_GET[ 'limit' ] : 0;
                    $offset        = isset( $_GET[ 'offset' ] ) ? $_GET[ 'offset' ] : 0;
                    $generate_feed = new $provider( $id, $type, $merchant, $limit, $offset );
                }
            }
        }
    }
}