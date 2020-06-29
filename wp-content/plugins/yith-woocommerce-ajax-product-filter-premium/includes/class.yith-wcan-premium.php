<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Premium' ) ) {
    /**
     * YITH WooCommerce Ajax Navigation
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Premium extends YITH_WCAN {

        /**
         * Constructor
         *
         * @return mixed|YITH_WCAN_Admin|YITH_WCAN_Frontend
         * @since 1.0.0
         */
        public function __construct() {
            $this->version = YITH_WCAN_VERSION;

            /* Require Premium Files */
            add_filter( 'yith_wcan_required_files', array( $this, 'require_premium_files' ) );
            add_filter( 'woocommerce_taxonomy_args_product_tag', array( $this, 'enabled_hierarchical_product_tags' ), 10, 1 );

            parent::__construct();

            //3rd-party support
            $theme = strtolower( wp_get_theme()->Name );
            $theme = str_replace( '-child', '', $theme );

            $supported_themes = array( 'salient' );

            if( 'salient' == $theme ){
                require_once YITH_WCAN_DIR . "compatibility/themes/{$theme}/{$theme}.php";
            }

            $supported_plugins = array(
                'wc-list-grid' => class_exists( 'WC_List_Grid' )
            );

            foreach( $supported_plugins as $plugin => $check ){
                if( $check ){
                    require_once YITH_WCAN_DIR . "compatibility/plugins/{$plugin}/{$plugin}.php";
                }
            }
        }

        /**
         * Add require premium files
         */
        public function require_premium_files( $files ){
            $files[] = 'includes/class.yith-wcan-admin-premium.php';
            $files[] = 'includes/class.yith-wcan-frontend-premium.php';
            $files[] = 'widgets/class.yith-wcan-navigation-widget-premium.php';
            $files[] = 'widgets/class.yith-wcan-reset-navigation-widget-premium.php';
            $files[] = 'widgets/class.yith-wcan-sort-by-widget.php';
            $files[] = 'widgets/class.yith-wcan-stock-on-sale-widget.php';
            $files[] = 'widgets/class.yith-wcan-list-price-filter-widget.php';

            return $files;
        }

        public function init() {
            if ( is_admin() ) {
                $this->admin = new YITH_WCAN_Admin_Premium( $this->version );
            }
            else {
                $this->frontend = new YITH_WCAN_Frontend_Premium( $this->version );
            }
        }

        /**
         * Main plugin Instance
         *
         * @return YITH_Vendors Main instance
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Load and register widgets
         *
         * @access public
         * @since  1.0.0
         */
        public function registerWidgets() {
            $widgets = apply_filters( 'yith_wcan_widgets', array(
                    'YITH_WCAN_Navigation_Widget_Premium',
                    'YITH_WCAN_Reset_Navigation_Widget_Premium',
                    'YITH_WCAN_Sort_By_Widget',
                    'YITH_WCAN_Stock_On_Sale_Widget',
                    'YITH_WCAN_List_Price_Filter_Widget'
                )
            );

            foreach( $widgets as $widget ){
                register_widget( $widget );
            }
        }

        public function enabled_hierarchical_product_tags( $args ){
            $args['hierarchical'] = 'yes' == yith_wcan_get_option( 'yith_wcan_enable_hierarchical_tags_link', 'no' ) ? true : false;
            $args['labels']['parent_item'] = $args['labels']['parent_item_colon'] = __( 'Parent tag', 'yith-woocommerce-ajax-navigation' );
            return $args;
        }

    }
}