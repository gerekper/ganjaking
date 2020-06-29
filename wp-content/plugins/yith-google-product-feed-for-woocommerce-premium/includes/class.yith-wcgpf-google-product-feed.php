<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCGPF_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_WCGPF_Google_Product_Feed
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCGPF_Google_Product_Feed' ) ) {

    /**
     * Class YITH_WCGPF_Google_Product_Feed
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCGPF_Google_Product_Feed {

        /**
         * Plugin version
         *
         * @var string
         * @since 1.0
         */
        public $version = YITH_WCGPF_VERSION;
        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Google_Product_Feed
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main Admin Instance
         *
         * @var YITH_WCGPF_Admin
         * @since 1.0
         */
        public $admin = null;
        /**
         * Main Functions Instance
         *
         * @var YITH_WCGPF_Feed_Functions
         * @since 1.0
         */
        public $functions = null;
        /**
         * Main Product Functions Instance
         *
         * @var YITH_WCGPF_Product_Functions
         * @since 1.0
         */
        public $product_function = null;
        /**
         * Main Merchant Instance
         *
         * @var YITH_WCGPF_Google_Product_Feed_Merchant
         * @since 1.0
         */
        public $merchant = null;
        /**
         * Main Merchant Instance
         *
         * @var YITH_WCGPF_Google_Product_Feed_Ajax
         * @since 1.0
         */
        public $ajax = null;
        /**
         * Main Merchant Instance
         *
         * @var YITH_WCGPF_Products
         * @since 1.0
         */
        public $products;
        /**
         * Main Merchant Instance
         *
         * @var YITH_WCGPF_Helper
         * @since 1.0
         */
        public $helper;

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        public function __construct()
        {
            /* === Require Main Files === */
            $require = apply_filters( 'yith_wcgpf_require_class',
                array(
                    'common'    => array(
                        'includes/class.yith-wcgpf-google-feed-functions.php',
                        'includes/class.yith-wcgpf-product-function.php',
                        'includes/class.yith-wcgpf-products.php',
                        'includes/merchant-feed/class.yith-wcgpf-google-product-feed-helper.php',
                        'includes/merchant-feed/class.yith-wcgpf-google-product-feed-generate-feed.php',
                        'includes/merchant-feed/class.yith-wcgpf-google-product-feed-generate-feed-google.php',
                        'includes/function-merchant/class.yith-wcgpf-merchant-google.php',
                        'includes/functions.yith-wcgpf.php',
                    ),
                    'admin'     => array(
                        'includes/admin/class.yith-wcgpf-admin.php',
                    ),
                    'frontend'  => array(

                    ),
                )
            );
            $this->_require( $require );

            $this->init_classes();


            /* === Load Plugin Framework === */
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            /* == Plugins Init === */
            add_action( 'init', array( $this, 'init' ) );
        }

        /**
         * Main plugin Instance
         *
         * @return YITH_WCGPF_Google_Product_Feed Main instance
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public static function instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }
        /**
         * Init classes function
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        function init_classes(){
            $this->functions =  YITH_WCGPF_Feed_Functions::get_instance();
            $this->product_function = YITH_WCGPF_Product_Functions::get_instance();
            $this->merchant_google = YITH_WCGPF_Merchant_Google::get_instance();
            $this->products = YITH_WCGPF_Products::get_instance();
            $this->helper = YITH_WCGPF_Helper::get_instance();
        }

        /**
         * Add the main classes file
         *
         * Include the admin and frontend classes
         *
         * @param $main_classes array The require classes file path
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         *
         * @return void
         * @access protected
         */
        protected function _require( $main_classes ) {
            foreach ( $main_classes as $section => $classes ) {
                foreach ( $classes as $class ) {
                    if ( 'common' == $section  || ( 'frontend' == $section && ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) || ( 'admin' == $section && is_admin() ) && file_exists( YITH_WCGPF_PATH . $class ) ) {
                        require_once( YITH_WCGPF_PATH . $class );
                    }
                }
            }
        }

        /**
         * Load plugin framework
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
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

        /**
         * Function init()
         *
         * Instance the admin or frontend classes
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
         * @access protected
         */
        public function init() {
            if ( is_admin() ) {
                $this->admin = YITH_WCGPF_Admin();
            }
        }


    }

}