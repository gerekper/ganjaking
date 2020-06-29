<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_Plugin_Licence' ) ) {
    /**
     * YIT Plugin Licence Panel
     *
     * Setting Page to Manage Plugins
     *
     * @class      YITH_Plugin_Licence
     * @package    YITH
     * @since      1.0
     * @author     Andrea Grillo      <andrea.grillo@yithemes.com>
     */
    class YITH_Plugin_Licence extends YITH_Licence {

        /**
         * @var array The settings require to add the submenu page "Activation"
         * @since 1.0
         */
        protected $_settings = array();

        /**
         * @var object The single instance of the class
         * @since 1.0
         */
        protected static $_instance = null;

        /**
         * @var string Option name
         * @since 1.0
         */
        protected $_licence_option = 'yit_plugin_licence_activation';

        /**
         * @var string product type
         * @since 1.0
         */
        protected $_product_type = 'plugin';

        /**
         * Constructor
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function __construct() {
            parent::__construct();

            if ( !is_admin() ) {
                return;
            }

            $this->_settings = array(
                'parent_page' => 'yith_plugin_panel',
                'page_title'  => __( 'License Activation', 'yith-plugin-upgrade-fw' ),
                'menu_title'  => __( 'License Activation', 'yith-plugin-upgrade-fw' ),
                'capability'  => 'manage_options',
                'page'        => 'yith_plugins_activation',
            );
            add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 99 );
            add_action( "wp_ajax_yith_activate-{$this->_product_type}", array( $this, 'activate' ) );
            add_action( "wp_ajax_yith_deactivate-{$this->_product_type}", array( $this, 'deactivate' ) );
            add_action( "wp_ajax_yith_remove-{$this->_product_type}", array( $this, 'deactivate' ) );
            add_action( "wp_ajax_yith_update_licence_information-{$this->_product_type}", array( $this, 'update_licence_information' ) );
            add_action( 'yit_licence_after_check', 'yith_plugin_fw_force_regenerate_plugin_update_transient' );
            add_filter( 'extra_plugin_headers', array( $this, 'extra_plugin_headers' ) );
        }

	    public function get_license_url(){return add_query_arg( array( 'page' => 'yith_plugins_activation' ), admin_url( 'admin.php' ) );}

        /**
         * Main plugin Instance
         *
         * @static
         * @return object Main instance
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Add "Activation" submenu page under YITH Plugins
         *
         * @return void
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function add_submenu_page() {
	        $no_active_products = $this->get_no_active_licence_key();
	        $expired_product = ! empty( $no_active_products['106'] ) ? count( $no_active_products['106'] ) : 0;
	        $bubble = ! empty( $expired_product ) ? " <span data-count='{$expired_product}' id='yith-expired-license-count' class='awaiting-mod count-{$expired_product}'><span class='expired-count'>{$expired_product}</span></span>" : '';

            add_submenu_page(
                $this->_settings[ 'parent_page' ],
                $this->_settings[ 'page_title' ],
                $this->_settings[ 'menu_title' ] . $bubble,
                $this->_settings[ 'capability' ],
                $this->_settings[ 'page' ],
                array( $this, 'show_activation_panel' )
            );
        }

        /**
         * Premium plugin registration
         *
         * @param $plugin_init | string | The plugin init file
         * @param $secret_key  | string | The product secret key
         * @param $product_id  | string | The plugin slug (product_id)
         *
         * @return void
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register( $plugin_init, $secret_key, $product_id ) {
            if ( !function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $plugins                                = get_plugins();
            $plugins[ $plugin_init ]['secret_key']  = $secret_key;
            $plugins[ $plugin_init ]['product_id']  = $product_id;
	        $plugins[ $plugin_init ]['marketplace'] = ! empty( $plugins[ $plugin_init ]['YITH Marketplace'] ) ? $plugins[ $plugin_init ]['YITH Marketplace'] : 'yith';
            $this->_products[ $plugin_init ]        = $plugins[ $plugin_init ];
        }

        public function get_product_type() {
            return $this->_product_type;
        }

        /**
         * Get license activation URL
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 3.0.17
         */
        public static function get_license_activation_url(){
            return add_query_arg( array( 'page' => 'yith_plugins_activation' ), admin_url( 'admin.php' ) );
        }

        /**
         * Add Extra Headers for Marketplace
         *
         * @Author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function extra_plugin_headers( $headers ){
        	$headers[] = 'YITH Marketplace';
        	return $headers;
        }
    }
}

/**
 * Main instance of plugin
 *
 * @return YITH_Plugin_Licence object of license class
 * @since  1.0
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( !function_exists( 'YITH_Plugin_Licence' ) ) {
    function YITH_Plugin_Licence() {
        return YITH_Plugin_Licence::instance();
    }
}