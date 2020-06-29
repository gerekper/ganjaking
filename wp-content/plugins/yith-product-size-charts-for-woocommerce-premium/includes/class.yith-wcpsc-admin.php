<?php
/**
 * Admin class
 *
 * @author  Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPSC_Admin' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCPSC_Admin {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPSC_Admin
         * @since 1.0.0
         */
        protected static $_instance;

        /** @var YIT_Plugin_Panel_WooCommerce $_panel Panel Object */
        protected $_panel;

        /** @var string Premium version landing link */
        protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-product-size-charts-for-woocommerce/';

        /** @var string panel page */
        protected $_panel_page = 'yith_wcpsc_panel';

        /** @var string plugin documentation */
        public $doc_url = 'https://yithemes.com/docs-plugins/yith-product-size-charts-for-woocommerce/';

        /**
         * Returns single instance of the class
         *
         * @return YITH_WCPSC_Admin
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

            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

            // Add Capabilities to Administrator and Shop Manager
            add_action( 'admin_init', array( $this, 'add_capabilities' ) );

            //Add action links
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCPSC_DIR . '/' . basename( YITH_WCPSC_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

            // Enqueue Scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 15 );

            // Register Post Type
            add_action( 'init', array( $this, 'register_product_metabox' ) );
            add_action( 'add_meta_boxes', array( $this, 'register_table_metabox' ) );
            add_action( 'save_post', array( $this, 'metabox_table_save' ) );

            add_action( 'yith_wcpsc_premium_tab', array( $this, 'show_premium_tab' ) );
            add_action( 'yith_wcpsc_description_tab', array( $this, 'show_description_tab' ) );

        }

        /**
         * Action Links
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         * @return   mixed Array
         * @return mixed
         * @use      plugin_action_links_{$plugin_file_name}
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @since    1.0
         */
        public function action_links( $links ) {
            return yith_add_action_links( $links, $this->_panel_page, defined( 'YITH_WCPSC_PREMIUM' ) );
        }

        /**
         * plugin_row_meta
         * add the action links to plugin admin page
         *
         * @param $row_meta_args
         * @param $plugin_meta
         * @param $plugin_file
         * @return   array
         * @since    1.0
         * @use      plugin_row_meta
         */
        public function plugin_row_meta( $row_meta_args, $plugin_meta, $plugin_file ) {
            $init = defined( 'YITH_WCPSC_FREE_INIT' ) ? YITH_WCPSC_FREE_INIT : YITH_WCPSC_INIT;

            if ( $init === $plugin_file ) {
                $row_meta_args[ 'slug' ]       = YITH_WCPSC_SLUG;
                $row_meta_args[ 'is_premium' ] = defined( 'YITH_WCPSC_PREMIUM' );
            }

            return $row_meta_args;
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @use      /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( !empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs_free = array(
                'description' => __( 'Description', 'yith-product-size-charts-for-woocommerce' ),
                'premium'     => __( 'Premium Version', 'yith-product-size-charts-for-woocommerce' )
            );

            $admin_tabs = apply_filters( 'yith_wcpsc_settings_admin_tabs', $admin_tabs_free );

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'plugin_slug'      => YITH_WCPSC_SLUG,
                'class'            => yith_set_wrapper_class(),
                'page_title'       => 'Product Size Charts for WooCommerce',
                'menu_title'       => 'Product Size Charts',
                'capability'       => 'manage_options',
                'parent'           => '',
                'parent_page'      => 'yit_plugin_panel',
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_WCPSC_DIR . '/plugin-options'
            );

            /* === Fixed: not updated theme  === */
            if ( !class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once YITH_WCPSC_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php';
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
        }

        /**
         * Register product metabox (free version only)
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function register_product_metabox() {
            global $pagenow;
            if ( !in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
                return;
            }

            $prod_sel = array( 'none' => __( 'None', 'yith-product-size-charts-for-woocommerce' ) );

            if ( !defined( 'YITH_WCPSC_PREMIUM' ) ) {
                $prod_args = array(
                    'posts_per_page' => -1,
                    'post_type'      => 'product',
                    'post_status'    => 'publish',
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'fields'         => 'ids'
                );

                $products = get_posts( $prod_args );

                if ( count( $products ) > 0 ) {
                    foreach ( $products as $product_id ) {
                        $prod_sel[ $product_id ] = get_the_title( $product_id );
                    }
                }
            }

            $args    = array(
                'label'    => __( 'Chart Options', 'yith-product-size-charts-for-woocommerce' ),
                'pages'    => 'yith-wcpsc-wc-chart',
                'context'  => 'normal',
                'priority' => 'default',
                'tabs'     => apply_filters( 'yith_wcpsc_tabs_metabox_chart_options', array(
                    'settings' => array( //tab
                                         'label'  => __( 'Chart Options', 'yith-product-size-charts-for-woocommerce' ),
                                         'fields' => array(
                                             'product' => array(
                                                 'label'   => __( 'Product', 'yith-product-size-charts-for-woocommerce' ),
                                                 'desc'    => __( 'Select the product in which this size chart has to be shown.', 'yith-product-size-charts-for-woocommerce' ),
                                                 'type'    => 'select',
                                                 'private' => false,
                                                 'options' => $prod_sel,
                                                 'std'     => 'none'
                                             )
                                         ),
                    )
                ) )
            );
            $metabox = YIT_Metabox( 'yith-wcpsc-metabox-settings' );
            $metabox->init( $args );
        }


        /**
         * Add size_chart management capabilities to Admin and Shop Manager
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_capabilities() {

            $capability_type = 'size_chart';
            $caps            = array(
                'edit_post'              => "edit_{$capability_type}",
                'delete_post'            => "delete_{$capability_type}",
                'edit_posts'             => "edit_{$capability_type}s",
                'edit_others_posts'      => "edit_others_{$capability_type}s",
                'publish_posts'          => "publish_{$capability_type}s",
                'read_private_posts'     => "read_private_{$capability_type}s",
                'delete_posts'           => "delete_{$capability_type}s",
                'delete_private_posts'   => "delete_private_{$capability_type}s",
                'delete_published_posts' => "delete_published_{$capability_type}s",
                'delete_others_posts'    => "delete_others_{$capability_type}s",
                'edit_private_posts'     => "edit_private_{$capability_type}s",
                'edit_published_posts'   => "edit_published_{$capability_type}s",
                'create_posts'           => "edit_{$capability_type}s",
            );

            // gets the admin and shop_mamager roles
            $admin        = get_role( 'administrator' );
            $shop_manager = get_role( 'shop_manager' );

            foreach ( $caps as $key => $cap ) {
                $admin->add_cap( $cap );
                $shop_manager->add_cap( $cap );
            }
        }

        /**
         * Register the metabox containing the chart table
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function register_table_metabox( $post_type ) {
            if ( 'yith-wcpsc-wc-chart' !== $post_type ) {
                return;
            }
            add_meta_box( 'yith-wcpsc-metabox-table', __( 'Create your Size Chart', 'yith-product-size-charts-for-woocommerce' ), array( $this, 'metabox_table_render' ), 'yith-wcpsc-wc-chart', 'normal', 'high' );
        }

        /**
         * Render the metabox containing the chart table
         *
         * @param       $post
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function metabox_table_render( $post ) {
            $table_meta = get_post_meta( $post->ID, '_table_meta', true ) ? get_post_meta( $post->ID, '_table_meta', true ) : '[[""]]';

            include YITH_WCPSC_TEMPLATE_PATH . '/metaboxes/table.php';
        }

        /**
         * Save meta for the metabox containing the chart table
         *
         * @param       $post_id
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function metabox_table_save( $post_id ) {
            if ( !empty( $_POST[ '_table_meta' ] ) ) {
                $table_meta = $_POST[ '_table_meta' ];

                update_post_meta( $post_id, '_table_meta', $table_meta );
            }
        }

        public function admin_enqueue_scripts() {
            $premium_suffix = defined( 'YITH_WCPSC_PREMIUM' ) ? '_premium' : '';

            wp_enqueue_style( 'yith-wcpsc-admin-styles', YITH_WCPSC_ASSETS_URL . '/css/admin' . $premium_suffix . '.css' );
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_script( 'jquery-ui-tabs' );
            wp_enqueue_style( 'jquery-ui-style-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css' );
            wp_enqueue_style( 'googleFontsOpenSans', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' );

            $screen = get_current_screen();

            $metabox_js = 'metabox.js';

            if ( 'yith-wcpsc-wc-chart' === $screen->id ) {

                wp_enqueue_style( 'yith-wcpsc-hide-cpt-parts', YITH_WCPSC_ASSETS_URL . '/css/hide_cpt_parts.css' );

                wp_enqueue_script( 'yith_wcpsc_metabox_js', YITH_WCPSC_ASSETS_URL . '/js/' . $metabox_js, array( 'jquery' ), '1.0.0', true );
                wp_localize_script( 'yith_wcpsc_metabox_js', 'ajax_object', array() );
            }

        }

        /**
         * Show premium landing tab
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function show_premium_tab() {
            $landing = YITH_WCPSC_TEMPLATE_PATH . '/premium.php';
            file_exists( $landing ) && require( $landing );
        }

        /**
         * Show description tab
         *
         * @return   void
         * @since    1.0.9
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function show_description_tab() {
            $description = '<div class="yith-wcpsc-description-tab">';
            $description .= '<h2>' . __( 'Product Size Charts', 'yith-product-size-charts-for-woocommerce' ) . '</h2>';

            $description_rows   = array();
            $description_rows[] = sprintf( __( '%s allows you to create custom size charts.', 'yith-product-size-charts-for-woocommerce' ), '<strong>YITH Product Size Charts for WooCommerce</strong>' );
            $description_rows[] = sprintf( __( 'Go to %s menu.', 'yith-product-size-charts-for-woocommerce' ),
                                           '<a href="edit.php?post_type=yith-wcpsc-wc-chart">' . __( 'Size Charts', 'yith-product-size-charts-for-woocommerce' ) . '</a>' );
            $description_rows[] = __( 'Create your Size Chart and assign it to a product.', 'yith-product-size-charts-for-woocommerce' );
            $description_rows[] = __( 'It will be visible on detail page of the selected product.', 'yith-product-size-charts-for-woocommerce' );
            $description        .= implode( '<br />', $description_rows );
            $description        .= '</div>';
            echo $description;
        }

        /**
         * Get the premium landing uri
         *
         * @return  string The premium landing link
         * @author  Leanza Francesco <leanzafrancesco@gmail.com>
         * @since   1.0.0
         */
        public function get_premium_landing_uri() {
            return $this->_premium_landing;
        }
    }
}

/**
 * Unique access to instance of YITH_WCPSC_Admin class
 *
 * @return YITH_WCPSC_Admin
 * @since 1.0.0
 */
function YITH_WCPSC_Admin() {
    return YITH_WCPSC_Admin::get_instance();
}