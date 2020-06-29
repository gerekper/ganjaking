<?php
/**
 * Admin class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Bulk Edit Products
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBEP_Admin' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBEP_Admin {

        /**
         * Single instance of the class
         *
         * @var YITH_WCBEP_Admin
         * @since 1.0.0
         */
        protected static $_instance;

        /** @var YIT_Plugin_Panel_WooCommerce $_panel Panel Object */
        protected $_panel;

        /** @var string Premium version landing link */
        protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-bulk-product-editing/';

        /** @var string Quick View panel page */
        protected $_panel_page = 'yith_wcbep_panel';

        /** @var bool */
        public $show_premium_landing;

        /** @var string Doc URL */
        public $doc_url = 'https://docs.yithemes.com/yith-woocommerce-bulk-product-editing/';

        public $templates = array();

        /**
         * Returns single instance of the class
         *
         * @return YITH_WCBEP_Admin
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

            $this->show_premium_landing = !( defined( 'YITH_WCBEP_PREMIUM' ) && YITH_WCBEP_PREMIUM );

            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

            //Add action links
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCBEP_DIR . '/' . basename( YITH_WCBEP_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

            // Enqueue Scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

            //Bulk edit Tab
            add_action( 'yith_wcbep_bulk_edit_main_tab', array( $this, 'main_tab' ) );

            add_action( 'wp_ajax_yith_wcbep_bulk_edit_products', array( $this, 'bulk_edit_products' ) );
            //add_action( 'wp_ajax_yith_wcbep_get_table', array($this, 'get_table_ajax') );

            // AJAX TABLE
            add_action( 'wp_ajax__ajax_fetch_yith_wcbep_list', array( $this, 'ajax_fetch_table_callback' ) );

            add_action( 'admin_init', array( $this, 'redirect_to_bulk_edit_page' ) );

            // Premium Tabs
            add_action( 'yith_wcbep_premium_tab', array( $this, 'show_premium_tab' ) );
        }


        /**
         * Get table [AJAX]
         *
         * @access public
         * @since  1.0.0
         */
        public function ajax_fetch_table_callback() {
            // Disable display_errors during this ajax requests to prevent malformed JSON
            $current_error_reporting = error_reporting();
            error_reporting( 0 );

            $table = new YITH_WCBEP_List_Table();
            $table->ajax_response();

            // Enable display_errors
            error_reporting( $current_error_reporting );
        }

        /**
         * Get main-tab template
         *
         * @access public
         * @since  1.0.0
         */
        public function main_tab() {
            $args = array();
            yith_wcbep_get_template( 'main-tab.php', $args );
        }

        /**
         * Get table [AJAX]
         *
         * @access public
         * @since  1.0.0
         */
        public function get_table_ajax() {
            $table = new YITH_WCBEP_List_Table();
            $table->prepare_items();
            $table->display();
            die();
        }

        /**
         * Bulk Edit Products [AJAX]
         *
         * @access public
         * @since  1.0.0
         */
        public function bulk_edit_products() {
            if ( isset( $_POST[ 'matrix_modify' ] ) && isset( $_POST[ 'matrix_keys' ] ) && isset( $_POST[ 'edited_matrix' ] ) ) {
                $matrix_modify = $_POST[ 'matrix_modify' ];
                $matrix_keys   = $_POST[ 'matrix_keys' ];
                $edited_matrix = $_POST[ 'edited_matrix' ];

                foreach ( $edited_matrix as $row => $cols ) {
                    foreach ( $cols as $id_col => $col ) {
                        if ( $col == 0 && $id_col != 2 ) {
                            $matrix_modify[ $row ][ $id_col ] = null;
                        }
                    }
                }

                $id_index         = array_search( 'ID', $matrix_keys );
                $reg_price_index  = array_search( 'regular_price', $matrix_keys );
                $sale_price_index = array_search( 'sale_price', $matrix_keys );

                $counter = 0;
                foreach ( $matrix_modify as $single_modify ) {
                    $id         = $single_modify[ $id_index ];
                    $reg_price  = $single_modify[ $reg_price_index ];
                    $sale_price = $single_modify[ $sale_price_index ];

                    $product = wc_get_product( $id );
                    if ( $product ) {
                        $counter++;

                        // EDIT REGULAR PRICE AND SALE PRICE
                        if ( isset( $reg_price ) ) {
                            yit_save_prop( $product, '_regular_price', ( $reg_price === '' ) ? '' : wc_format_decimal( $reg_price ) );
                        }
                        if ( isset( $sale_price ) ) {
                            yit_save_prop( $product, '_sale_price', ( $sale_price === '' ? '' : wc_format_decimal( $sale_price ) ) );
                        }

                        $date_from = yit_get_prop( $product, '_sale_price_dates_from', true );
                        $date_to   = yit_get_prop( $product, '_sale_price_dates_to', true );

                        if ( is_numeric( $sale_price ) && '' !== $sale_price && empty( $date_to ) && empty( $date_from ) ) {
                            yit_save_prop( $product, '_price', wc_format_decimal( $sale_price ) );
                        } else {
                            yit_save_prop( $product, '_price', ( $reg_price === '' ) ? '' : wc_format_decimal( $reg_price ) );
                        }
                    }
                }
                echo sprintf( _n( '%s product edited', '%s products edited', $counter, 'yith-woocommerce-bulk-product-editing' ), $counter );
            }
            die();
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
            return yith_add_action_links( $links, $this->_panel_page, defined( 'YITH_WCBEP_PREMIUM' ) );
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
            $init = defined( 'YITH_WCBEP_FREE_INIT' ) ? YITH_WCBEP_FREE_INIT : YITH_WCBEP_INIT;

            if ( $init === $plugin_file ) {
                $row_meta_args[ 'slug' ]       = YITH_WCBEP_SLUG;
                $row_meta_args[ 'is_premium' ] = defined( 'YITH_WCBEP_PREMIUM' );
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
                'bulk-edit' => __( 'Bulk Product Editing', 'yith-woocommerce-bulk-product-editing' )
            );

            if ( $this->show_premium_landing )
                $admin_tabs_free[ 'premium' ] = __( 'Premium Version', 'yith-woocommerce-bulk-product-editing' );


            $admin_tabs = apply_filters( 'yith_wcbep_settings_admin_tabs', $admin_tabs_free );

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'plugin_slug'      => YITH_WCBEP_SLUG,
                'class'            => yith_set_wrapper_class(),
                'page_title'       => 'WooCommerce Bulk Product Editing',
                'menu_title'       => 'Bulk Product Editing',
                'capability'       => 'edit_products',
                'parent'           => '',
                'parent_page'      => 'yith_plugin_panel',
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_WCBEP_DIR . '/plugin-options'
            );

            /* === Fixed: not updated theme  === */
            if ( !class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

            add_action( 'woocommerce_admin_field_yith_wcbep_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );

            add_submenu_page( 'edit.php?post_type=product', 'Bulk Product Editing', 'Bulk Product Editing', 'manage_woocommerce', 'yith-wcbep-bulk-product-editing', '__return_empty_string' );
        }

        public function redirect_to_bulk_edit_page() {
            global $pagenow;
            /* Check current admin page. */
            if ( $pagenow == 'edit.php' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == 'product' && isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'yith-wcbep-bulk-product-editing' ) {
                wp_redirect( admin_url( "admin.php?page=yith_wcbep_panel" ) );
                exit;
            }
        }

        public function admin_enqueue_scripts() {
            $premium_suffix = defined( 'YITH_WCBEP_PREMIUM' ) ? '_premium' : '';
            $suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            $screen = get_current_screen();

            $main_tab_js = 'main_tab' . $premium_suffix . $suffix . '.js';

            $is_panel = strpos( $screen->id, '_page_yith_wcbep_panel' ) > -1;
            if ( $is_panel ) {
                wp_enqueue_style( 'yith-wcbep-admin-styles', YITH_WCBEP_ASSETS_URL . '/css/admin' . $premium_suffix . '.css', array('yith-plugin-fw-fields'), YITH_WCBEP_VERSION );
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wp-color-picker' );
                wp_enqueue_script( 'jquery-ui-tabs' );
                wp_enqueue_style( 'googleFontsOpenSans', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' );

                wp_enqueue_script( 'jquery-ui-datepicker' );


                wp_enqueue_script( 'select2' );
                wp_enqueue_style( 'select2' );

                wp_enqueue_script( 'jquery-ui-resizable' );

                wp_enqueue_script( 'yith_wcbep_main_tab_js', YITH_WCBEP_ASSETS_URL . '/js/' . $main_tab_js, array( 'jquery', 'jquery-ui-resizable', 'jquery-blockui' ), YITH_WCBEP_VERSION, true );
                wp_localize_script( 'yith_wcbep_main_tab_js', 'ajax_object', array(
                    'no_product_selected'            => __( 'No Product Selected: to make a bulk edit, select one or more products', 'yith-woocommerce-bulk-product-editing' ),
                    'leave_empty'                    => __( '- - - Empty - - -', 'yith-woocommerce-bulk-product-editing' ),
                    'file'                           => __( 'file', 'yith-woocommerce-bulk-product-editing' ),
                    'files'                          => __( 'files', 'yith-woocommerce-bulk-product-editing' ),
                    'not_editable_variations'        => __( 'This field is not editable for this product!', 'yith-woocommerce-bulk-product-editing' ),
                    'not_editable_new_product'       => __( 'This field is not editable right now! Please save before trying to edit this field.', 'yith-woocommerce-bulk-product-editing' ),
                    'file_error'                     => __( 'ERROR: file not correct!', 'yith-woocommerce-bulk-product-editing' ),
                    'delete_confirm_txt'             => __( 'Confirm Deletion [Selected Products: %s]', 'yith-woocommerce-bulk-product-editing' ),
                    'delete_txt'                     => __( 'Delete Selected', 'yith-woocommerce-bulk-product-editing' ),
                    'round_prices'                   => get_option( 'yith-wcbep-round-prices', 'no' ),
                    'use_regex'                      => get_option( 'yith-wcbep-use-regex-on-search', 'no' ),
                    'woocommerce_price_num_decimals' => get_option( 'woocommerce_price_num_decimals', 2 ),
                ) );

                wp_localize_script( 'yith_wcbep_main_tab_js', 'extra_class', array(
                    'select' => implode( ', ', apply_filters( 'yith_wcbep_td_extra_class_select', array() ) ),
                    'chosen' => implode( ', ', apply_filters( 'yith_wcbep_td_extra_class_chosen', array() ) ),
                    'text'   => apply_filters( 'yith_wcbep_td_extra_class_text', array() ),
                ) );

                wp_localize_script( 'yith_wcbep_main_tab_js', 'extra_obj', array(
                    'chosen' => apply_filters( 'yith_wcbep_extra_obj_class_chosen', array() ),
                ) );

                wp_localize_script( 'yith_wcbep_main_tab_js', 'extra_bulk', array(
                    'select' => apply_filters( 'yith_wcbep_extra_bulk_columns_select', array() ),
                    'chosen' => apply_filters( 'yith_wcbep_extra_bulk_columns_chosen', array() ),
                    'text'   => apply_filters( 'yith_wcbep_extra_bulk_columns_text', array() ),
                ) );

                wp_enqueue_script( 'yith-wcbep-utils-js', YITH_WCBEP_ASSETS_URL . '/js/utils' . $suffix . '.js', array( 'jquery' ), YITH_WCBEP_VERSION, true );

            }
        }

        /**
         * Show premium landing tab
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francescp <leanzafrancesco@gmail.com>
         */
        public function show_premium_tab() {
            $landing = YITH_WCBEP_TEMPLATE_PATH . '/premium.php';
            file_exists( $landing ) && require( $landing );
        }

        /**
         * Get the premium landing uri
         *
         * @return  string The premium landing link
         * @author   Leanza Francescp <leanzafrancesco@gmail.com>
         * @since    1.0.0
         */
        public function get_premium_landing_uri() {
            return $this->_premium_landing;
        }
    }
}

/**
 * Unique access to instance of YITH_WCBEP_Admin class
 *
 * @return YITH_WCBEP_Admin|YITH_WCBEP_Admin_Premium
 * @since 1.0.0
 */
function YITH_WCBEP_Admin() {
    return YITH_WCBEP_Admin::get_instance();
}