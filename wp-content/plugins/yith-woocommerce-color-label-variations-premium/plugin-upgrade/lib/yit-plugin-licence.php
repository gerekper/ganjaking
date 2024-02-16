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
		 * Check global license on network transient name
		 *
		 * @since 1.0
		 * @var string
		 */
		protected $check_global_license_transient = 'yith_plugin_global_license_activation';

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

			if( is_multisite() ){
				add_action( "yith_{$this->product_type}_licence_check", array( $this, 'check_global_license_for_all_blogs' ), 10, 3 );

				//Delete the global license activation for all blogs if the admin add a new site, delete or edit a site
				$actions = array(
					'wp_delete_site',
					'wp_insert_site',
					'wp_update_site'
				);

				foreach ( $actions as $action ){
					add_action( $action, array( $this, 'delete_global_license_transient' ) );
				}
			}
		}

		/**
		 * Get the activation licence url
		 *
		 * @return bool|string
		 * @author Francesco Licandro
		 */
		public function get_license_url() {
			return add_query_arg( array( 'page' => 'yith_plugins_activation' ), admin_url( 'admin.php' ) );
		}

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return object Main instance
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
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
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
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
		 * @param string $plugin_init The plugin init file.
		 * @param string $secret_key The product secret key.
		 * @param string $product_id The plugin slug (product_id).
		 *
		 * @return void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
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
		 * @return string
		 * @author Francesco Licandro
		 */
		public function get_product_type() {
			return $this->product_type;
		}

		/**
		 * Get license activation URL
		 *
		 * @return string
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 3.0.17
		 */
		public static function get_license_activation_url( $plugin_slug = '' ) {
			$args = array( 'page' => 'yith_plugins_activation' );
			if ( ! empty( $plugin_slug ) ) {
				$args['plugin'] = $plugin_slug;
			}

			return add_query_arg( $args, admin_url( 'admin.php' ) );
		}

		/**
		 * Add Extra Headers for Marketplace
		 *
		 * @param array $headers An array of headers.
		 *
		 * @return array
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function extra_plugin_headers( $headers ) {
			$headers[] = 'YITH Marketplace';

			return $headers;
		}

		/**
		 *
		 */
		public function check_global_license_for_all_blogs( $product_init, $activated, $product_type ) {
			$plugin = $this->get_product( $product_init );

			if( ! empty( $plugin ) ){
				$slug = ! empty( $plugin['product_id'] ) ? $plugin['product_id'] : false;

				if( $slug ){
					$global_license_information = $this->get_global_license_transient();
					$data_changed               = false;
					if( false === $activated ){
						$global_license_information[ $slug ] = false;
						$data_changed                        = true;
					}

					else { //Plugin activated
						$blog_ids        = wp_list_pluck( get_sites(), 'blog_id' );
						$network_enabled = true;
						foreach ( $blog_ids as $blog_id ) {
							$blog_license = get_blog_option( $blog_id, YITH_Plugin_Licence()->get_licence_option_name() );
							if ( empty( $blog_license[ $slug ]['activated'] ) ) {
								$network_enabled = false;
								break;
							}
						}

						if( isset( $global_license_information[ $slug ] ) ){
							$data_changed = (bool) $network_enabled !== (bool) $global_license_information[ $slug ];
						}

						else {
							$data_changed = true;
						}

						$global_license_information[ $slug ] = $network_enabled;
					}

					if( $data_changed ){
						$this->set_global_license_transient( $global_license_information );
					}
				}
			}
		}

		/**
		 * Get the global license information for all networks
		 *
		 * @return mixed Activation array check if exists. False otherwise
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function get_global_license_transient(){
			$data = get_site_transient( $this->check_global_license_transient );
			return $data;
		}

		/**
		 * Save the global license information in a transient
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function set_global_license_transient( $data ){
			$expiration = apply_filters( 'yith_check_global_license_expiration', DAY_IN_SECONDS );
			set_site_transient( $this->check_global_license_transient, $data, $expiration );
		}

		/**
		 * Delete the global license information in a transient
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function delete_global_license_transient(){
			delete_site_transient( $this->check_global_license_transient );
		}
	}

	if ( ! function_exists( 'YITH_Plugin_Licence' ) ) {
		/**
		 * Get the main instance of class
		 *
		 * @return YITH_Plugin_Licence
		 * @author Francesco Licandro
		 * @since  1.0
		 */
		function YITH_Plugin_Licence() { // phpcs:ignore
			return YITH_Plugin_Licence::instance();
		}
	}
}
