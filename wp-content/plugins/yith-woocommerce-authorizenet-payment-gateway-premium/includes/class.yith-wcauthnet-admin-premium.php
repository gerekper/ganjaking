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
 * Admin Premium class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Authorize.net
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAUTHNET' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAUTHNET_Admin_Premium' ) ) {
	/**
	 * WooCommerce Authorize.net admin class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAUTHNET_Admin_Premium extends YITH_WCAUTHNET_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAUTHNET_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAUTHNET_Admin_Premium
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
		 * @return \YITH_WCAUTHNET_Admin_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			$this->admin_tabs['echeck'] = __( 'eCheck', 'yith-woocommerce-authorizenet-payment-gateway' );
			unset( $this->admin_tabs['premium'] );

			// register functions to print premium tabs
			add_action( 'yith_wcauthnet_payment_echeck_gateway_settings_tab', array( $this, 'print_echeck_panel' ) );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
		}

		/**
		 * Print settings panel for credit card gateway
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_credit_card_panel() {
			if ( file_exists( YITH_WCAUTHNET_DIR . '/templates/admin/settings-tab.php' ) ) {
				global $current_section;
				$current_section = 'yith_wcauthnet_credit_card_gateway_premium';

				WC_Admin_Settings::get_settings_pages();

				if ( ! empty( $_POST ) ) {
					YITH_WCAUTHNET_Credit_Card_Gateway_Premium()->process_admin_options();
				}

				include_once( YITH_WCAUTHNET_DIR . '/templates/admin/settings-tab.php' );
			}
		}

		/**
		 * Print settings panel for echeck gateway
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_echeck_panel() {
			if ( file_exists( YITH_WCAUTHNET_DIR . '/templates/admin/settings-tab.php' ) ) {
				global $current_section;
				$current_section = 'yith_wcauthnet_echeck_gateway';

				WC_Admin_Settings::get_settings_pages();

				if ( ! empty( $_POST ) ) {
					YITH_WCAUTHNET_eCheck_Gateway()->process_admin_options();
				}

				include_once( YITH_WCAUTHNET_DIR . '/templates/admin/settings-tab.php' );
			}
		}

		/* === WCAUTHNET LICENCE HANDLING === */

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCAUTHNET_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCAUTHNET_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCAUTHNET_INIT, YITH_WCAUTHNET_SECRET_KEY, YITH_WCAUTHNET_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WCAUTHNET_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}

			YIT_Upgrade()->register( YITH_WCAUTHNET_SLUG, YITH_WCAUTHNET_INIT );
		}

		/* === POINTERS === */

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
					apply_filters( 'yith_wcauthnet_activated_pointer_content', __( 'In the YIT Plugins tab you can find the YITH WooCommerce Authorize.net options. From this menu, you can access all the settings of the YITH plugins activated', 'yith-woocommerce-authorizenet-payment-gateway' ) )
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'       => YITH_WCAUTHNET_INIT
			);

			YIT_Pointers()->register( $args );
		}
	}
}

/**
 * Unique access to instance of YITH_WCAUTHNET_Admin_Premium class
 *
 * @return \YITH_WCAUTHNET_Admin_Premium
 * @since 1.0.0
 */
function YITH_WCAUTHNET_Admin_Premium() {
	return YITH_WCAUTHNET_Admin_Premium::get_instance();
}