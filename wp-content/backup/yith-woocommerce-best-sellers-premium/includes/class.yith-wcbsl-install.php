<?php

/**
 * Install class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCBSL' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBSL_Install' ) ) {
    /**
     * Install class.
     * for first activation of plugin
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     * @since    1.0.0
     */
    class YITH_WCBSL_Install {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCBSL_Install
         * @since 1.0.0
         */
        private static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCBSL_Install
         * @since 1.0.0
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        private function __construct() {
            add_action( 'init', array( $this, 'add_pages' ) );
        }

        /**
         * Add a page "BestSellers".
         *
         * @return void
         * @since 1.0.0
         */
        public function add_pages() {
            global $wpdb;

            $bestsellers_option  = 'yith-wcbsl-bestsellers-page-id';
            $bestsellers_page_id = get_option( $bestsellers_option );

            if ( $bestsellers_page_id > 0 && get_post( $bestsellers_page_id ) )
                return;

            $page_found = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_name` = 'bestsellers' LIMIT 1;" );
            if ( $page_found ) :
                if ( !$bestsellers_page_id )
                    update_option( $bestsellers_option, $page_found );

                return;
            endif;

            $page_data = array(
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => 1,
                'post_name'      => esc_sql( _x( 'bestsellers', 'page_slug', 'yith-woocommerce-best-sellers' ) ),
                'post_title'     => __( 'Bestsellers', 'yith-woocommerce-best-sellers' ),
                'post_content'   => '<!-- wp:shortcode -->[bestsellers]<!-- /wp:shortcode -->',
                'post_parent'    => 0,
                'comment_status' => 'closed'
            );
            $page_id   = wp_insert_post( $page_data );

            update_option( $bestsellers_option, $page_id );
        }

    }
}


/**
 * Unique access to instance of YITH_WCBSL_Install class
 *
 * @return \YITH_WCBSL_Install
 * @since 1.0.0
 */
function YITH_WCBSL_Install() {
    return YITH_WCBSL_Install::get_instance();
}