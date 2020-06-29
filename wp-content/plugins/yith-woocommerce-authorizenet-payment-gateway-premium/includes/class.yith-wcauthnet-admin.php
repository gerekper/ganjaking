<?php
/*  Copyright 2013  Your Inspiration Themes  (email : plugins@yithemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Authorize.net
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAUTHNET' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAUTHNET_Admin' ) ) {
	/**
	 * WooCommerce Authorize.net admin class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAUTHNET_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAUTHNET_Admin
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * @var string Premium landing url
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-authorize-net/';

		/**
		 * @var string live demo url
		 */
		protected $_live_demo = 'https://plugins.yithemes.com/yith-woocommerce-authorize-net';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAUTHNET_Admin
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAUTHNET_Admin
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->admin_tabs = array(
				'credit_card' => __( 'Credit Card', 'yith-woocommerce-authorizenet-payment-gateway' ),
				'premium'     => __( 'Premium Version', 'yith-woocommerce-authorizenet-payment-gateway' )
			);

			// register gateway panel
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// register panel
			add_action( 'yith_wcauthnet_payment_credit_card_gateway_settings_tab', array(
				$this,
				'print_credit_card_panel'
			) );

			// register pointer
			add_action( 'admin_init', array( $this, 'register_pointer' ) );

			//Add action links
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCAUTHNET_DIR . '/' . basename( YITH_WCAUTHNET_FILE ) ), array(
				$this,
				'action_links'
			) );

			//  Show plugin premium tab
			add_action( 'yith_authorizenet_premium', array( $this, 'premium_tab' ) );
		}

		/**
		 * Get the premium landing uri
		 *
		 * @return  string The premium landing link
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since   1.0.0
		 */
		public function get_premium_landing_uri() {
			return $this->_premium_landing;
		}

		/**
		 * Register subpanel for YITH Authorize.net into YI Plugins panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_panel() {
			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ),
				'menu_title'       => __( 'Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => 'yith_wcauthnet_panel',
				'admin-tabs'       => apply_filters( 'yith_wcauthnet_available_tabs', $this->admin_tabs ),
				'options-path'     => YITH_WCAUTHNET_DIR . 'plugin-options'
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WCAUTHNET_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Print custom tab of settings for Authorize.net subpanel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_credit_card_panel() {
			if ( file_exists( YITH_WCAUTHNET_DIR . '/templates/admin/settings-tab.php' ) ) {

				global $current_section;
				$current_section = 'yith_wcauthnet_credit_card_gateway';

				WC_Admin_Settings::get_settings_pages();

				if ( ! empty( $_POST ) ) {
					YITH_WCAUTHNET_Credit_Card_Gateway()->process_admin_options();
				}

				include_once( YITH_WCAUTHNET_DIR . '/templates/admin/settings-tab.php' );
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.0
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, 'yith_wcauthnet_panel', defined( 'YITH_WCAUTHNET_PREMIUM' ) );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCAUTHNET_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']             = YITH_WCAUTHNET_SLUG;
				$new_row_meta_args['live_demo']['url'] = $this->_live_demo;
			}

			if ( defined( 'YITH_WCAUTHNET_PREMIUM' ) ) {
				$new_row_meta_args['is_premium'] = true;

			}

			return $new_row_meta_args;
		}

		/**
		 * Register the pointer for the settings page
		 *
		 * @since 1.0.0
		 */
		public function register_pointer() {

			if ( ! class_exists( 'YIT_Pointers' ) ) {
				include_once( '../plugin-fw/lib/yit-pointers.php' );
			}

			$args[] = array(
				'screen_id'  => 'plugins',
				'pointer_id' => 'yith_wcauthnet_panel',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					__( 'YITH Authorize.net', 'yit' ),
					apply_filters( 'yith_wcauthnet_activated_pointer_content', sprintf( __( 'In the YIT Plugins tab you can find the YITH WooCommerce Authorize.net options. From this menu, you can access all the settings of the YITH plugins activated. YITH Authorize.net is available in an outstanding PREMIUM version with many new options, <a href="%s">discover it now</a>.', 'yith-woocommerce-authorizenet-payment-gateway' ), $this->get_premium_landing_uri() ) )
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'       => YITH_WCAUTHNET_INIT
			);

			YIT_Pointers()->register( $args );
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @return void
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.0
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_WCAUTHNET_DIR . 'templates/admin/premium.php';
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCAUTHNET_Admin class
 *
 * @return \YITH_WCAUTHNET_Admin
 * @since 1.0.0
 */
function YITH_WCAUTHNET_Admin() {
	return YITH_WCAUTHNET_Admin::get_instance();
}