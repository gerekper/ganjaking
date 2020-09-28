<?php
/**
 * This file belongs to the YIT Plugin Framework.
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @author YITH
 * @package YITH License & Upgrade Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Plugin_Licence' ) ) {
	/**
	 * YIT Plugin Licence Panel
	 * Setting Page to Manage Plugins
	 *
	 * @class      YITH_Plugin_Licence
	 * @since      1.0
	 * @author     Andrea Grillo      <andrea.grillo@yithemes.com>
	 * @package    YITH
	 */
	class YITH_Plugin_Licence extends YITH_Licence {

		/**
		 * The settings require to add the submenu page "Activation"
		 *
		 * @since 1.0
		 * @var array
		 */
		protected $settings = array();

		/**
		 * The single instance of the class
		 *
		 * @since 1.0
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Option name
		 *
		 * @since 1.0
		 * @var string
		 */
		protected $licence_option = 'yit_plugin_licence_activation';

		/**
		 * The product type
		 *
		 * @since 1.0
		 * @var string
		 */
		protected $product_type = 'plugin';

		/**
		 * Constructor
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function __construct() {
			parent::__construct();

			if ( ! is_admin() ) {
				return;
			}

			$this->settings = array(
				'parent_page' => 'yith_plugin_panel',
				'page_title'  => __( 'License Activation', 'yith-plugin-upgrade-fw' ),
				'menu_title'  => __( 'License Activation', 'yith-plugin-upgrade-fw' ),
				'capability'  => 'manage_options',
				'page'        => 'yith_plugins_activation',
			);
			add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 99 );
			add_action( "wp_ajax_yith_activate-{$this->product_type}", array( $this, 'activate' ) );
			add_action( "wp_ajax_yith_deactivate-{$this->product_type}", array( $this, 'deactivate' ) );
			add_action( "wp_ajax_yith_remove-{$this->product_type}", array( $this, 'deactivate' ) );
			add_action( "wp_ajax_yith_update_licence_information-{$this->product_type}", array( $this, 'update_licence_information' ) );
			add_action( 'yit_licence_after_check', 'yith_plugin_fw_force_regenerate_plugin_update_transient' );
			add_filter( 'extra_plugin_headers', array( $this, 'extra_plugin_headers' ) );
		}

		/**
		 * Get the activation licence url
		 *
		 * @author Francesco Licandro
		 * @return bool|string
		 */
		public function get_license_url() {
			return add_query_arg( array( 'page' => 'yith_plugins_activation' ), admin_url( 'admin.php' ) );
		}

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return object Main instance
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Add "Activation" submenu page under YITH Plugins
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function add_submenu_page() {
			$no_active_products = $this->get_no_active_licence_key();
			$expired_product    = ! empty( $no_active_products['106'] ) ? count( $no_active_products['106'] ) : 0;
			$bubble             = ! empty( $expired_product ) ? " <span data-count='{$expired_product}' id='yith-expired-license-count' class='awaiting-mod count-{$expired_product}'><span class='expired-count'>{$expired_product}</span></span>" : '';

			add_submenu_page(
				$this->settings['parent_page'],
				$this->settings['page_title'],
				$this->settings['menu_title'] . $bubble,
				$this->settings['capability'],
				$this->settings['page'],
				array( $this, 'show_activation_panel' )
			);
		}

		/**
		 * Premium plugin registration
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param string $plugin_init The plugin init file.
		 * @param string $secret_key The product secret key.
		 * @param string $product_id The plugin slug (product_id).
		 * @return void
		 */
		public function register( $plugin_init, $secret_key, $product_id ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$plugins                                = get_plugins();
			$plugins[ $plugin_init ]['secret_key']  = $secret_key;
			$plugins[ $plugin_init ]['product_id']  = $product_id;
			$plugins[ $plugin_init ]['marketplace'] = ! empty( $plugins[ $plugin_init ]['YITH Marketplace'] ) ? $plugins[ $plugin_init ]['YITH Marketplace'] : 'yith';
			$this->products[ $plugin_init ]         = $plugins[ $plugin_init ];
		}

		/**
		 * Get the product type
		 *
		 * @author Francesco Licandro
		 * @return string
		 */
		public function get_product_type() {
			return $this->product_type;
		}

		/**
		 * Get license activation URL
		 *
		 * @since 3.0.17
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return string
		 */
		public static function get_license_activation_url() {
			return add_query_arg( array( 'page' => 'yith_plugins_activation' ), admin_url( 'admin.php' ) );
		}

		/**
		 * Add Extra Headers for Marketplace
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param array $headers An array of headers.
		 * @return array
		 */
		public function extra_plugin_headers( $headers ) {
			$headers[] = 'YITH Marketplace';

			return $headers;
		}
	}
}

if ( ! function_exists( 'YITH_Plugin_Licence' ) ) {
	/**
	 * Get the main instance of class
	 *
	 * @since  1.0
	 * @author Francesco Licandro
	 * @return YITH_Plugin_Licence
	 */
	function YITH_Plugin_Licence() { // phpcs:ignore
		return YITH_Plugin_Licence::instance();
	}
}
