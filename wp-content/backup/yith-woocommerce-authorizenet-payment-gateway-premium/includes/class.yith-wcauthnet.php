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
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Authorize.net
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAUTHNET' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAUTHNET' ) ) {
	/**
	 * WooCommerce Authorize.net main class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAUTHNET {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAUTHNET
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAUTHNET
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @param array $details
		 *
		 * @return \YITH_WCAUTHNET
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'privacy_loader' ), 20 );

			// enqueue assets
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

			// add filter to append wallet as payment gateway
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_to_gateways' ) );

			if ( defined( 'YITH_WCAUTHNET_PREMIUM' ) && YITH_WCAUTHNET_PREMIUM ) {
				YITH_WCAUTHNET_Premium();
			}
		}

		/**
		 * Enqueue scripts
		 *
		 * @return void
		 */
		public function enqueue() {
			global $wp;
			$path   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'unminified/' : '';
			$suffix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';

			if ( is_checkout() || isset( $wp->query_vars['add-payment-method'] ) ) {
				wp_enqueue_script( 'yith-wcauthnet-form-handler', YITH_WCAUTHNET_URL . 'assets/js/' . $path . 'authorize-net' . $suffix . '.js', array( 'jquery' ), YITH_WCAUTHNET_VERSION, true );
			}
		}

		/**
		 * Adds Authorize.net Gateway to payment gateways available for woocommerce checkout
		 *
		 * @param $methods array Previously available gataways, to filter with the function
		 *
		 * @return array New list of available gateways
		 * @since  1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function add_to_gateways( $methods ) {
			if ( defined( 'YITH_WCAUTHNET_PREMIUM' ) && YITH_WCAUTHNET_PREMIUM ) {
				$methods[] = 'YITH_WCAUTHNET_Credit_Card_Gateway_Premium';
				$methods[] = 'YITH_WCAUTHNET_eCheck_Gateway';
			} else {
				$methods[] = 'YITH_WCAUTHNET_Credit_Card_Gateway';
			}

			return $methods;
		}

		/* === PLUGIN FW LOADER === */

		/**
		 * Loads plugin fw, if not yet created
		 *
		 * @return void
		 * @since 1.0.0
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

		/* === PRIVACY LOADER === */

		/**
		 * Loads privacy class
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function privacy_loader() {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once( YITH_WCAUTHNET_INC . 'class.yith-wcauthnet-privacy.php' );
				new YITH_WCAUTHNET_Privacy();
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCAUTHNET class
 *
 * @return \YITH_WCAUTHNET
 * @since 1.0.0
 */
function YITH_WCAUTHNET() {
	return YITH_WCAUTHNET::get_instance();
}

// Let's start the game!
// Create unique instance of the class
YITH_WCAUTHNET();