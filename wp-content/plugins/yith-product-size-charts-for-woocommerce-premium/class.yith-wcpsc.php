<?php
/**
 * Main class
 *
 * @author  Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPSC' ) ) {
    /**
     * YITH Product Size Charts for WooCommerce
     *
     * @since 1.0.0
     */
    class YITH_WCPSC {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPSC
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return YITH_WCPSC
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }

        /**
         * Constructor
         *
         * @return mixed| YITH_WCPSC_Admin | YITH_WCPSC_Frontend
         * @since 1.0.0
         */
        protected function __construct() {
            // Load Plugin Framework
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            add_action( 'init', array( $this, 'post_type_register' ) );

            if ( is_admin() && !defined( 'DOING_AJAX' ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( !isset( $_REQUEST[ 'context' ] ) || ( isset( $_REQUEST[ 'context' ] ) && $_REQUEST[ 'context' ] !== 'frontend' ) ) ) ) {
                YITH_WCPSC_Admin();
            }
            
            YITH_WCPSC_Frontend();
        }

        /**
         * Register Size Chart custom post type with options metabox
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function post_type_register() {
            $labels = array(
                'name'               => __( 'Size Charts', 'yith-product-size-charts-for-woocommerce' ),
                'singular_name'      => __( 'Size Chart', 'yith-product-size-charts-for-woocommerce' ),
                'add_new'            => __( 'Add Size Chart', 'yith-product-size-charts-for-woocommerce' ),
                'add_new_item'       => __( 'New Size Chart', 'yith-product-size-charts-for-woocommerce' ),
                'edit_item'          => __( 'Edit Size Chart', 'yith-product-size-charts-for-woocommerce' ),
                'view_item'          => __( 'View Size Chart', 'yith-product-size-charts-for-woocommerce' ),
                'not_found'          => __( 'Size Chart not found', 'yith-product-size-charts-for-woocommerce' ),
                'not_found_in_trash' => __( 'Size Chart not found in trash', 'yith-product-size-charts-for-woocommerce' )
            );

            $capability_type = 'size_chart';
            $caps            = array(
                'edit_post'              => "edit_{$capability_type}",
                'read_post'              => "read_{$capability_type}",
                'delete_post'            => "delete_{$capability_type}",
                'edit_posts'             => "edit_{$capability_type}s",
                'edit_others_posts'      => "edit_others_{$capability_type}s",
                'publish_posts'          => "publish_{$capability_type}s",
                'read_private_posts'     => "read_private_{$capability_type}s",
                'read'                   => "read",
                'delete_posts'           => "delete_{$capability_type}s",
                'delete_private_posts'   => "delete_private_{$capability_type}s",
                'delete_published_posts' => "delete_published_{$capability_type}s",
                'delete_others_posts'    => "delete_others_{$capability_type}s",
                'edit_private_posts'     => "edit_private_{$capability_type}s",
                'edit_published_posts'   => "edit_published_{$capability_type}s",
                'create_posts'           => "edit_{$capability_type}s",
                'manage_posts'           => "manage_{$capability_type}s",
            );

            $args = array(
                'labels'               => $labels,
                'public'               => false,
                'show_ui'              => true,
                'menu_position'        => 10,
                'exclude_from_search'  => true,
                'capability_type'      => 'size_chart',
                'capabilities'         => $caps,
                'map_meta_cap'         => true,
                'rewrite'              => true,
                'has_archive'          => true,
                'hierarchical'         => false,
                'show_in_nav_menus'    => false,
                'menu_icon'            => 'dashicons-grid-view',
                'supports'             => array( 'title' ),
            );

            register_post_type( 'yith-wcpsc-wc-chart', $args );
        }


        /**
         * Load Plugin Framework
         *
         * @since  1.0
         * @access public
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function plugin_fw_loader() {
            if ( !defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if ( !empty( $plugin_fw_data ) ) {
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }
    }
}

/**
 * Unique access to instance of YITH_WCPSC class
 *
 * @return YITH_WCPSC|YITH_WCPSC_Premium
 * @since 1.0.0
 */
function YITH_WCPSC() {
    return YITH_WCPSC::get_instance();
}