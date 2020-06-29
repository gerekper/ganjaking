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

if ( !class_exists( 'YITH_WCGPF_Helper' ) ) {
    /**
     * YITH_WCGPF_Helper
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Helper {

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
            $this->allowed_merchant = apply_filters( 'yith_wcgpf_allowed_merchant', array( 'google' ) );

            add_action( 'get_header', array( $this, 'generate_feed' ) );
            add_action( 'init', array( $this, 'add_rewrite_rules' ) );
        }

        public function add_rewrite_rules() {
            add_rewrite_tag( '%yith_wcgpf_feed%', '([^/]+)' );
            add_rewrite_tag( '%feed_id%', '([0-9]+)' );
            add_rewrite_tag( '%merchant%', '([^/]+)' );
            add_rewrite_rule( 'yith_wcgpf_feed/([^/]+)/feed_id/([0-9]+)/merchant/([^/]+)/?', 'index.php?yith_wcgpf_feed=$matches[1]&feed_id=$matches[2]&merchant=$matches[3]', 'top' );
        }


        function is_url_for_generate_feed() {
            list( $type, $merchant, $id ) = $this->get_params_for_generating_feed();
            return !!$type && !!$merchant && !!$id && !isset( $_GET[ 'yith_wcgpf_file' ] );
        }

        public function generate_feed() {
            if ( $this->is_url_for_generate_feed() ) {

                $premium_suffix = defined( 'YITH_WCGPF_PREMIUM' ) && YITH_WCGPF_PREMIUM ? '_Premium' : '';
                $provider       = 'YITH_WCGPF_Generate_Feed_' . $this->merchant . $premium_suffix;
                $generate_feed  = new $provider( $this->feed_id, $this->feed_type, $this->merchant );
            }
        }


        public function get_params_for_generating_feed() {
            global $wp;
            if ( isset( $wp->query_vars[ 'yith_wcgpf_feed' ] ) ) {
                $feed_type = $wp->query_vars[ 'yith_wcgpf_feed' ];
                $merchant  = isset( $wp->query_vars[ 'merchant' ] ) && in_array( $wp->query_vars[ 'merchant' ], $this->allowed_merchant ) ? $wp->query_vars[ 'merchant' ] : false;
                $feed_id   = !empty( $wp->query_vars[ 'feed_id' ] ) ? $wp->query_vars[ 'feed_id' ] : false;
            } else {
                $feed_type = isset( $_GET[ 'yith_wcgpf_feed' ] ) ? $_GET[ 'yith_wcgpf_feed' ] : false;
                $merchant  = isset( $_GET[ 'merchant' ] ) && in_array( $_GET[ 'merchant' ], $this->allowed_merchant ) ? $_GET[ 'merchant' ] : false;
                $feed_id   = !empty( $_GET[ 'feed_id' ] ) ? $_GET[ 'feed_id' ] : false;
            }

            return array( $feed_type, $merchant, $feed_id );
        }
    }
}