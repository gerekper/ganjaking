<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe_Admin_Premium' ) ) {
	/**
	 * WooCommerce Stripe main class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Admin_Premium extends YITH_WCStripe_Admin {

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCStripe_Admin_Premium
		 * @since 1.0.0
		 */
		public function __construct() {

			parent::__construct();

			// enqueue admin scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// ajax actions
			add_action( 'wp_ajax_yith_wcstripe_set_webhook', array( $this, 'set_webhook' ) );

			include_once( 'class-yith-stripe-blacklist-admin.php' );
		}

		/**
		 * Enqueue admin scripts
		 *
		 * @return void
		 * @since 1.5.1
		 */
		public function enqueue() {
			$current_screen    = get_current_screen();
			$current_screen_id = $current_screen->id;

			wp_register_script( 'stripe-js', YITH_WCSTRIPE_URL . 'assets/js/admin/yiths.js', array(
				'jquery',
				'jquery-blockui'
			), YITH_WCSTRIPE_VERSION, true );
			wp_localize_script( 'stripe-js', 'yith_stripe', array(
				'actions'  => array(
					'set_webhook' => 'yith_wcstripe_set_webhook'
				),
				'security' => array(
					'set_webhook' => wp_create_nonce( 'set_webhook' )
				)
			) );

			if ( 'yith-plugins_page_yith_wcstripe_panel' == $current_screen_id || ( 'woocommerce_page_wc-settings' == $current_screen_id && isset( $_GET['section'] ) && 'yith-stripe' == $_GET['section'] ) ) {
				wp_enqueue_script( 'stripe-js' );
			}
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCSTRIPE_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_WCSTRIPE_INIT, YITH_WCSTRIPE_SECRET_KEY, YITH_WCSTRIPE_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_WCSTRIPE_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_WCSTRIPE_SLUG, YITH_WCSTRIPE_INIT );
		}

		/**
		 *
		 */
		public function set_webhook() {
			if ( ! isset( $_GET['security'] ) || ! wp_verify_nonce( $_GET['security'], 'set_webhook' ) ) {
				$res = false;
			} else {
				$gateway = YITH_WCStripe()->get_gateway();

				if ( ! $gateway ) {
					$res = false;
				} else {
					$env = apply_filters( 'yith_wcstripe_environment', ( $gateway->get_option( 'enabled_test_mode' ) == 'yes' || ( defined( 'WP_ENV' ) && 'development' == WP_ENV ) ) ? 'test' : 'live' );;

					try {
						$gateway->init_stripe_sdk();
						$res = $gateway->api->create_webhook( array(
							'enabled_events' => array( '*' ),
							'url'            => esc_url( add_query_arg( 'wc-api', 'stripe_webhook', site_url( '/' ) ) )
						) );

						if ( $res ) {
							update_option( "yith_wcstripe_{$env}_webhook_processed", true );
						}
					} catch ( Exception $e ) {
						$res = false;
					}
				}
			}

			wp_send_json( array(
				'status'  => $res,
				'message' => $res ? __( 'Webhook correctly configured', 'yith-woocommerce-stripe' ) : __( 'It wasn\'t possible to correctly configure webhooks; please try later.', 'yith-woocommerce-stripe' )
			) );
		}
	}
}