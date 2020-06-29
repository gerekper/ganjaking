<?php

/*
 * This file belongs to the YITH Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_COG_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_COG
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 */

if ( ! class_exists( 'YITH_COG' ) ) {
    /**
     * Class YITH_COG
     *
     * @author Francisco Mendoza
     */
    class YITH_COG {

        /**
         * Plugin version
         *
         * @since 1.0
         */
        public $version = YITH_COG_VERSION;

        /**
         * Main Instance
         *
         * @var YITH_COG
         * @since 1.0
         */
        protected static $_instance = null;

        public $admin = null;

        /**
         * Construct
         *
         * @since 1.0
         */
        public function __construct(){

            /* === Require Main Files === */
            $require = apply_filters('yith_cog_require_class',
                array(
                    'common' => array(
                        'includes/class.yith-cog-products.php',
                        'includes/class.yith-cog-orders.php',
                        'includes/class.yith-cog-ajax.php',
                        'includes/class.yith-cog-custom-columns.php',
                    ),
                    'frontend' => array(),
                    'admin' => array(
                        'includes/admin/class.yith-cog-admin.php',

                        'includes/admin/reports/class.yith-cog-report-table.php',
                        'includes/admin/reports/class.yith-cog-report-data.php',
                        'includes/admin/reports/class.yith-cog-report-data-category.php',
                        'includes/admin/reports/class.yith-cog-report-data-product.php',
                        'includes/admin/reports/class.yith-cog-report-data-tag.php',
                        'includes/admin/reports/class.yith-cog-report-links.php',

                        'includes/admin/reports/stock-reports/class.yith-cog-report-stock-table.php',
                        'includes/admin/reports/stock-reports/class.yith-cog-report-stock-data-category.php',
                        'includes/admin/reports/stock-reports/class.yith-cog-report-stock-data-product.php',
                        'includes/admin/reports/stock-reports/class.yith-cog-report-stock-data-all-stock.php',
                        'includes/admin/reports/stock-reports/class.yith-cog-report-stock-links.php',
                    )
                ));

            $this->_require($require);
            $this->init_classes();

            /* === Load Plugin Framework === */
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            /* === Plugins Init === */
            add_action( 'init', array( $this, 'init' ) );
        }


        /**
         * Main plugin Instance
         *
         * @return YITH_COG Main instance
         */
        public static function instance(){

            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        public function init_classes(){
            YITH_COG_Orders::get_instance();
            YITH_COG_Ajax::get_instance();
            YITH_COG_Custom_Columns::get_instance();
        }

        /**
         * Add the main classes file
         * Include the admin and frontend classes
         *
         * @param $main_classes array The require classes file path
         * @since  1.0
         */
        protected function _require( $main_classes ){

            foreach ( $main_classes as $section => $classes ) {
                foreach ( $classes as $class ) {
                    if ( 'common' == $section || ( 'frontend' == $section && !is_admin() || ( defined('DOING_AJAX' ) && DOING_AJAX ) ) || ( 'admin' == $section && is_admin() ) && file_exists(YITH_COG_PATH . $class ) ) {
                        require_once( YITH_COG_PATH . $class );
                    }
                }
            }

            //  Aelia Currency Switcher
            class_exists ( 'WC_Aelia_CurrencySwitcher' ) && require_once ( YITH_COG_PATH . 'includes/third-party/class-yith-cog-AeliaCS-module.php' );

            // WPML Currency Switcher
            global $woocommerce_wpml;
            if ( $woocommerce_wpml ) {
               require_once ( YITH_COG_PATH . 'includes/third-party/class-yith-cog-wpml-module.php' );
            }

            do_action('yith_cog_require' );
        }

        /**
         * Load plugin framework
         *
         * @since  1.0
         */
        public function plugin_fw_loader(){

            if ( !defined('YIT_CORE_PLUGIN' )) {
                global $plugin_fw_data;
                if (!empty($plugin_fw_data)) {
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }

        /**
         * Class Initialization
         *
         * Instance the admin class
         *
         * @since  1.0
         */
        public function init(){

            if ( is_admin() ) {
                $this->admin = YITH_COG_Admin::get_instance();
            }
        }

    }

}