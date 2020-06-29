<?php
/**
 * Frontend class
 *
 * @author  Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.1.1
 */

if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPSC_Frontend' ) ) {
    /**
     * Frontend class.
     * The class manage all the Frontend behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCPSC_Frontend {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPSC_Frontend
         * @since 1.0.0
         */
        protected static $_instance;

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
            // add frontend css
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            // add tab on products
            add_filter( 'woocommerce_product_tabs', array( $this, 'product_tabs' ) );
        }

        /**
         * add tabs to product
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function product_tabs( $tabs ) {
            global $post;
            $args = array(
                'post_per_page' => -1,
                'post_type'     => 'yith-wcpsc-wc-chart',
                'post_status'   => 'publish',
                'meta_key'      => 'product',
                'meta_value'    => $post->ID
            );
            $charts = get_posts( $args );

            if ( count( $charts ) > 0 ) {
                foreach ( $charts as $chart ) {

                    $tabs[ 'yith-wcpsc-tab-' . $chart->ID ] = array(
                        'title'         => $chart->post_title,
                        'priority'      => 99,
                        'callback'      => array( $this, 'create_tab_content' ),
                        'yith_wcpsc_id' => $chart->ID
                    );
                }
            }

            return $tabs;
        }

        /**
         * create the content of table in product page
         *
         * @access   public
         * @since    1.0.0
         *
         * @param string $key the key of the tab
         * @param array  $tab array that contains info of tab (title, priority, callback, yith_wcpsc_id)
         *
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function create_tab_content( $key, $tab ) {
            if ( !isset( $tab[ 'yith_wcpsc_id' ] ) )
                return;
            $c_id = $tab[ 'yith_wcpsc_id' ];
            $table_meta = get_post_meta( $c_id, '_table_meta', true );
            $args = array(
                'table_meta' => $table_meta
            );
            wc_get_template( 'product/table.php', $args, YITH_WCPSC_SLUG . '/', YITH_WCPSC_TEMPLATE_PATH . '/' );
        }


        public function enqueue_scripts() {
            $premium_suffix = defined( 'YITH_WCPSC_PREMIUM' ) ? '_premium' : '';
            wp_enqueue_style( 'yith-wcpsc-frontent-styles', YITH_WCPSC_ASSETS_URL . '/css/frontend' . $premium_suffix . '.css' );
        }

    }
}
/**
 * Unique access to instance of YITH_WCPSC_Frontend class
 *
 * @return YITH_WCPSC_Frontend|YITH_WCPSC_Frontend_Premium
 * @since 1.0.0
 */
function YITH_WCPSC_Frontend() {
    return YITH_WCPSC_Frontend::get_instance();
}