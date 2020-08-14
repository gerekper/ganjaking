<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCP' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCP' ) ) {
    /**
     * YITH Composite Products for WooCommerce
     *
     * @since 1.0.0
     */
    class YITH_WCP {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version;

        /**
         * Frontend object
         *
         * @var string
         * @since 1.0.0
         */
        public $frontend = null;

        /**
         * Admin object
         *
         * @var string
         * @since 1.0.0
         */
        public $admin = null;

        /**
         * Check if YITH Multi Vendor is installed
         *
         * @var boolean
         * @since 1.0.0
         */
        public $_is_vendor_installed;

        /**
         * Check if WPML is installed
         *
         * @var boolean
         * @since 1.0.0
         */
        public $_is_wpml_installed;

        /**
         * Main instance
         *
         * @var string
         * @since 1.4.0
         */
        protected static $_instance = null;

        /**
         * Constructor
         *
         * @return mixed|YITH_WCP_Admin|YITH_WCP_Frontend
         * @since 1.0.0
         */
        public function __construct() {

            $this->version = YITH_WCP_VERSION;

            /* External plugin support */

            $this->_is_vendor_installed = function_exists( 'YITH_Vendors' );

            global $sitepress;
            $this->_is_wpml_installed = ! empty( $sitepress );

            /* Load Plugin Framework */
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
            add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

            $this->create_tables();

            $this->required();

            $this->init();
        }

        /**
         * Load plugin framework
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0
         * @return void
         */
        public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if ( ! empty( $plugin_fw_data ) ) {
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }
        
        /**
         * Load Privacy
         */  
        function load_privacy() {
            require_once( YITH_WCP_DIR . 'includes/class.yith-wcp-privacy.php' );
        }

        /**
         * Main plugin Instance
         *
         * @return YITH_WCP Main instance
         * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public static function instance() {

            if ( is_null( YITH_WCP::$_instance ) ) {
                YITH_WCP::$_instance = new YITH_WCP();
            }

            return YITH_WCP::$_instance;
        }

        public static function create_tables() {

            /**
             * If exists yith_wcp_db_version option return null
             */
            if ( apply_filters( 'yith_wcp_db_version', get_option( 'yith_wcp_db_version' ) ) ) {
                return;
            }

            //add_option( 'yith_wcp_db_version', YITH_WCP_DB_VERSION );

        }

        /**
         * Load required files
         *
         * @since  1.4
         * @return void
         * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public function required() {
            $required = apply_filters( 'yith_wcp_required_files', array(
                    'includes/class.yith-wcp-admin.php',
                    'includes/class.yith-wcp-cart.php',
                    'includes/class.yith-wcp-frontend.php',
                )
            );

            if ( $this->_is_wpml_installed ) {
                $required[] = 'includes/class.yith-wcp-wpml.php';
            }

            foreach ( $required as $file ) {
                if( file_exists( YITH_WCP_DIR . $file ) ) {
                    require_once( YITH_WCP_DIR . $file );
                }
            }
        }

        public function init() {

            if ( is_admin() && ! $this->is_quick_view() ) {
               $this->admin = new YITH_WCP_Admin( $this );
            } else {
                $this->frontend = new YITH_WCP_Frontend( $this );
            }
        }

        /**
         * @return bool
         */
        private function is_quick_view() {
            return ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'yit_load_product_quick_view' || $_REQUEST['action'] == 'yith_load_product_quick_view' || $_REQUEST['action'] == 'ux_quickview' ) ) ? true : false;
        }

        /**
         * @return mixed|void
         */
        public function getAllowedProductTypes() {

            return apply_filters( 'yith_wcp_product_type_list', array( 'simple', 'variable' ) );

        }

        /**
         * @return null|YITH_Vendor
         */
        public function get_current_multivendor() {

            if ( $this->_is_vendor_installed && is_user_logged_in() ) {

                $vendor = yith_get_vendor( 'current', 'user' );

                if ( $vendor->is_valid() ) {
                    return $vendor;
                }

            }

            return null;
        }

        /**
         * @param        $id
         * @param string $obj
         *
         * @return null|YITH_Vendor
         */
        public function get_multivendor_by_id( $id, $obj = 'vendor' ) {

            if ( $this->_is_vendor_installed ) {

                $vendor = yith_get_vendor( $id, $obj );

                if ( $vendor->is_valid() ) {
                    return $vendor;
                }

            }

            return null;
        }

        /**
         * @return bool
         */
        public function is_plugin_enabled_for_vendors() {
            return get_option( 'yith_wpv_vendors_option_composite_products_management' ) == 'yes';
        }

        /**
         * @param     $post_id
         * @param     $wcp_data_single_item
         * @param int $post_per_page
         * @param int $current_page
         *
         * @return array
         */
        public function getProductsQueryArgs( $post_id , $wcp_data_single_item, $post_per_page = -1, $current_page = 1, $custom_order_by = 'menu_order', $custom_order = 'asc' ) {

            $atts = array(
                'orderby'  => $custom_order_by,
                'order'    => $custom_order,
            );

            $atts = apply_filters( 'ywcp_order_product_incomponent', $atts, $post_id, $wcp_data_single_item );

            // Default ordering args
            $ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );

            $args = array(
                'post_type'           => 'product',
                'posts_per_page'      => $post_per_page,
                'ignore_sticky_posts' => 1,
                'post_status'         => array( 'publish' ),
                'post__not_in'        => array( $post_id ),
                'orderby'             => $ordering_args['orderby'],
                'order'               => $ordering_args['order'],
            );

            if ( isset( $ordering_args['meta_key'] ) ) {
                $args['meta_key'] = $ordering_args['meta_key'];
            }

            if ( apply_filters( 'ywcp_hide_outofstock_components', false ) ) {
                $args['meta_query']['stock_status'] = array(
                    'key'       => '_stock_status',
                    'value'     => 'outofstock',
                    'compare'   => 'NOT IN'
                );
            }

            if ( $post_per_page > 0 ) {
                $args['paged'] = $current_page;
            }

            switch ( $wcp_data_single_item['option_type'] ) {
                case 'product' :
                    $ids = is_array( $wcp_data_single_item['option_type_product_id_values'] ) ? $wcp_data_single_item['option_type_product_id_values'] : explode( ',', $wcp_data_single_item['option_type_product_id_values'] );
                    $ids = array_map( 'trim', $ids );
                    $args['post__in'] = $ids;
                    break;
                case 'product_categories' :
                    $args['product_cat'] = is_array( $wcp_data_single_item['option_type_cat_id_values'] ) ? implode( ',', $wcp_data_single_item['option_type_cat_id_values'] ) : '';
                    break;
                case 'product_tags' :
                    $args['product_tag'] = is_array( $wcp_data_single_item['option_type_tag_id_values'] ) ? implode( ',', $wcp_data_single_item['option_type_tag_id_values'] ) : '';
                    break;
            }
            
            return  $args;
            
        }

        /**
         * @return mixed
         */
        public static function yit_wc_deprecated_filters() {
            return apply_filters( 'yit_wc_deprecated_filters', array(
                'woocommerce_email_order_schema_markup'      => 'woocommerce_structured_data_order',
                'woocommerce_product_width'                  => 'woocommerce_product_get_width',
                'woocommerce_product_height'                 => 'woocommerce_product_get_height',
                'woocommerce_product_length'                 => 'woocommerce_product_get_length',
                'woocommerce_product_weight'                 => 'woocommerce_product_get_weight',
                'woocommerce_get_sku'                        => 'woocommerce_product_get_sku',
                'woocommerce_get_price'                      => 'woocommerce_product_get_price',
                'woocommerce_get_regular_price'              => 'woocommerce_product_get_regular_price',
                'woocommerce_get_sale_price'                 => 'woocommerce_product_get_sale_price',
                'woocommerce_product_tax_class'              => 'woocommerce_product_get_tax_class',
                'woocommerce_get_stock_quantity'             => 'woocommerce_product_get_stock_quantity',
                'woocommerce_get_product_attributes'         => 'woocommerce_product_get_attributes',
                'woocommerce_product_gallery_attachment_ids' => 'woocommerce_product_get_gallery_image_ids',
                'woocommerce_product_review_count'           => 'woocommerce_product_get_review_count',
                'woocommerce_product_files'                  => 'woocommerce_product_get_downloads',
                'woocommerce_get_currency'                   => 'woocommerce_order_get_currency',
                'woocommerce_order_amount_discount_total'    => 'woocommerce_order_get_discount_total',
                'woocommerce_order_amount_discount_tax'      => 'woocommerce_order_get_discount_tax',
                'woocommerce_order_amount_shipping_total'    => 'woocommerce_order_get_shipping_total',
                'woocommerce_order_amount_shipping_tax'      => 'woocommerce_order_get_shipping_tax',
                'woocommerce_order_amount_cart_tax'          => 'woocommerce_order_get_cart_tax',
                'woocommerce_order_amount_total'             => 'woocommerce_order_get_total',
                'woocommerce_order_amount_total_tax'         => 'woocommerce_order_get_total_tax',
                'woocommerce_order_amount_total_discount'    => 'woocommerce_order_get_total_discount',
                'woocommerce_order_amount_subtotal'          => 'woocommerce_order_get_subtotal',
                'woocommerce_order_tax_totals'               => 'woocommerce_order_get_tax_totals',
                'woocommerce_refund_amount'                  => 'woocommerce_get_order_refund_get_amount',
                'woocommerce_refund_reason'                  => 'woocommerce_get_order_refund_get_reason',
                'default_checkout_country'                   => 'default_checkout_billing_country',
                'default_checkout_state'                     => 'default_checkout_billing_state',
                'default_checkout_postcode'                  => 'default_checkout_billing_postcode',
                'woocommerce_add_order_item_meta'            => 'woocommerce_new_order_item',

            ) );
        }

    }
}