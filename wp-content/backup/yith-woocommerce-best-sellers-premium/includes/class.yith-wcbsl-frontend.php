<?php
/**
 * Frontend class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.1.1
 */

if ( !defined( 'YITH_WCBSL' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBSL_Frontend' ) ) {
    /**
     * Frontend class.
     * The class manage all the Frontend behaviors.
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     * @since    1.0.0
     *
     */
    class YITH_WCBSL_Frontend {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCBSL_Frontend
         * @since 1.0.0
         */
        protected static $_instance;

        /** @var YITH_WCBSL_Reports|YITH_WCBSL_Reports_Premium */
        public $reports;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCBSL_Frontend
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        protected function __construct() {

            $this->reports = class_exists( 'YITH_WCBSL_Reports_Premium' ) ? new YITH_WCBSL_Reports_Premium() : new YITH_WCBSL_Reports();

            // add frontend css
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            // Add Shortcode for Product Size Charts
            add_shortcode( 'bestsellers', array( $this, 'shortcode_handler' ) );


            $show_bestseller_badge = get_option( 'yith-wcbsl-show-bestseller-badge', 'no' ) == 'yes';
            if ( $show_bestseller_badge ) {
                // Display Badge in single product Page
                add_filter( 'woocommerce_product_thumbnails', array( $this, 'print_badge' ));
                // Display Badge in Shop Page
                add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'print_badge' ) );
            }
        }

        /**
         * Display Bestsellers Badge in Shop Page
         *
         * @access public
         * @since  1.1.4
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function print_badge() {
            global $product;

            $base_product_id = yit_get_base_product_id( $product );

            $is_bestseller = $this->reports->check_is_bestseller( $base_product_id );
            if ( !$is_bestseller )
                return;

            $args[ 'class' ] = 'yith-wcbsl-mini-badge';
            wc_get_template( '/bestseller-badge.php', $args, YITH_WCBSL_TEMPLATE_PATH, YITH_WCBSL_TEMPLATE_PATH );
        }

        /**
         * Show Bestseller Badge in products
         *
         * @access public
         * @return string
         *
         * @param $val        string product image
         * @param $product_id int product id
         * @param $args       array
         *
         *
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function show_bestseller_badge( $val, $product_id, $args = array() ) {
            $is_bestseller = $this->reports->check_is_bestseller( $product_id );

            if ( !$is_bestseller )
                return $val;

            $default_args = array(
                'class' => '',
            );

            $args = wp_parse_args( $args, $default_args );

            ob_start();
            wc_get_template( '/bestseller-badge.php', $args, YITH_WCBSL_TEMPLATE_PATH, YITH_WCBSL_TEMPLATE_PATH );
            $val = $val . ob_get_clean();

            return $val;
        }

        /**
         * Add Shortcode for Bestsellers
         *
         * @access   public
         * @since    1.0.0
         *
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         *
         * @param      $atts array the attributes of shortcode
         * @param null $content
         *
         * @return string
         */
        public function shortcode_handler( $atts, $content = null ) {
            ob_start();
            wc_get_template( 'bestsellers.php', array(), '', YITH_WCBSL_TEMPLATE_PATH . '/' );

            return ob_get_clean();
        }

        public function enqueue_scripts() {
            wp_enqueue_style( 'yith_wcbsl_frontend_style', YITH_WCBSL_ASSETS_URL . '/css/frontend.css', array(), YITH_WCBSL_VERSION );
        }
    }
}
/**
 * Unique access to instance of YITH_WCBSL_Frontend class
 *
 * @return YITH_WCBSL_Frontend|YITH_WCBSL_Frontend_Premium
 * @since 1.0.0
 */
function YITH_WCBSL_Frontend() {
    return YITH_WCBSL_Frontend::get_instance();
}