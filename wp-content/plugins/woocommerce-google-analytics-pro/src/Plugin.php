<?php
/**
 * WooCommerce Google Analytics Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Admin\AJAX;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\API_Client;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Identity_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Order_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions_Integration;
use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * # WooCommerce Google Analytics Pro Main Plugin Class.
 *
 * @since 1.0.0
 */
class Plugin extends Framework\SV_WC_Plugin {


	/** plugin version number */
	public const VERSION = '2.0.5';

	/** @var Plugin the singleton instance of the plugin */
	protected static $instance;

	/** the plugin ID */
	public const PLUGIN_ID = 'google_analytics_pro';

	/** @var API_Client the API client for Google APIs */
	protected API_Client $api_client;

	/** @var Properties_Handler the properties handler instance */
	protected Properties_Handler $properties_handler;

	/** @var Tracking the tracking handler instance */
	protected Tracking $tracking;

	/** @var Identity_Helper the identity handler class instance */
	protected Identity_Helper $identity_helper;

	/** @var Order_Helper the order handler class instance */
	protected Order_Helper $order_helper;

	/** @var Integration|null the integration class instance */
	protected ?Integration $integration = null;

	/** @var AJAX|null the AJAX class instance */
	protected ?AJAX $ajax = null;

	/** @var Subscriptions_Integration|null the Subscriptions Integration class instance */
	protected ?Subscriptions_Integration $subscriptions_integration = null;


	/**
	 * Constructs the class and initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			[
				'text_domain'   => 'woocommerce-google-analytics-pro',
				'supports_hpos' => true,
			]
		);

		// Loads handlers a bit earlier than the standard framework initialization, so that we Subscriptions event names
		// settings are displayed in admin settings page.
		// make sure that the add_action() call in SV_WC_Plugin::add_hooks() in v5_4_1 matches the remove_action() call below
		if ( remove_action( 'plugins_loaded', [ $this, 'init_plugin' ], 15 ) ) {
			add_action( 'after_setup_theme', [ $this, 'init_plugin' ], 0 );
		}

		// add the plugin to available WooCommerce integrations
		add_filter( 'woocommerce_integrations', [ $this, 'load_integration' ], PHP_INT_MAX );
	}


	/**
	 * Loads and initializes the lifecycle handler instance.
	 *
	 * @since 1.6.0
	 */
	protected function init_lifecycle_handler() : void {

		$this->lifecycle_handler = new Lifecycle( $this );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function init_plugin(): void {

		// NOTE: since the plugin is loaded earlier than usual, we need to make sure the translations textdomain is available before gettext strings are loaded below
		$this->load_plugin_textdomain();

		$this->setup_handlers();

		// check if free WooCommerce Google Analytics integration is activated and deactivate it
		if ( is_admin() && $this->is_plugin_active( 'woocommerce-google-analytics-integration.php' ) ) {

			$this->deactivate_free_integration();
		}
	}


	/**
	 * Instantiates handlers and stores a reference to them.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function setup_handlers() : void {

		// load subscriptions integration before setting up tracking so that we have a chance to filter
		// events and settings
		if ( $this->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {
			$this->subscriptions_integration = new Subscriptions_Integration();
		}

		$this->api_client         = new API_Client();
		$this->properties_handler = new Properties_Handler();
		$this->tracking           = new Tracking();
		$this->order_helper       = new Order_Helper();
		$this->identity_helper    = new Identity_Helper();

		// AJAX includes
		if ( wp_doing_ajax() ) {
			$this->ajax = new AJAX();
		}
	}


	/**
	 * Gets the Google APIs Client instance
	 *
	 * @since 2.0.0
	 *
	 * @return API_Client
	 */
	public function get_api_client_instance() : API_Client {
		return $this->api_client;
	}


	/**
	 * Gets the Properties_Handler instance
	 *
	 * @since 2.0.0
	 *
	 * @return Properties_Handler
	 */
	public function get_properties_handler_instance() : Properties_Handler {
		return $this->properties_handler;
	}


	/**
	 * Gets the Tracking handler instance
	 *
	 * @since 2.0.0
	 *
	 * @return Tracking
	 */
	public function get_tracking_instance() : Tracking {
		return $this->tracking;
	}


	/**
	 * Adds GA Pro as a WooCommerce integration.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 */
	public function load_integration( array $integrations = []): array {

		if ( ! in_array( Integration::class, $integrations, true ) ) {
			$integrations = array_merge( [ self::PLUGIN_ID => Integration::class ], $integrations );
		}

		return $integrations;
	}


	/**
	 * Returns the integration class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Integration the integration class instance
	 */
	public function get_integration(): Integration {

		if ( ! $this->integration instanceof Integration) {

			$integrations = null === WC()->integrations ? [] : WC()->integrations->get_integrations();

			if ( isset( $integrations[ self::PLUGIN_ID ] ) && $integrations[ self::PLUGIN_ID ] instanceof Integration ) {

				$this->integration = $integrations[ self::PLUGIN_ID ];

			} else {

				$this->integration = new Integration();
			}
		}

		return $this->integration;
	}


	/**
	 * Returns the integration class instance.
	 *
	 * @since 1.6.0
	 *
	 * @see Plugin::get_integration() alias for backwards compatibility
	 *
	 * @return Integration
	 */
	public function get_integration_instance(): Integration {
		return $this->get_integration();
	}


	/**
	 * Returns the AJAX class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return AJAX the AJAX class instance
	 */
	public function get_ajax_instance(): AJAX {

		return $this->ajax;
	}


	/**
	 * Returns the Subscriptions integration class instance.
	 *
	 * @since 1.5.0
	 *
	 * @return Subscriptions_Integration
	 */
	public function get_subscriptions_integration_instance(): Subscriptions_Integration {

		return $this->subscriptions_integration;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.0.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name(): string {

		return __( 'WooCommerce Google Analytics Pro', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * Returns the full path and filename of the plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __DIR__;
	}


	/**
	 * Returns the plugin sales page URL.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/woocommerce-google-analytics-pro/';
	}


	/**
	 * Returns the plugin documentation URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string the plugin documentation URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-google-analytics-pro/';
	}


	/**
	 * Returns the plugin support URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string the plugin support URL
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Returns the settings page URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $_ unused
	 * @return string the settings page URL
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-settings&tab=integration&section=google_analytics_pro' );
	}


	/**
	 * Returns deprecated/removed hooks.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	protected function get_deprecated_hooks() {

		$deprecated_hooks = [
			'wc_google_analytics_pro_product_funnel_steps' => [
				'version' => '1.3.0',
				'removed' => true,
			],
		];

		return $deprecated_hooks;
	}


	/**
	 * Determines if viewing the plugin settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool whether viewing the plugin settings page
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'], $_GET['tab'] )
			&& 'wc-settings' === $_GET['page']
			&& 'integration' === $_GET['tab']
			&& ( ! isset( $_GET['section'] ) || $this->get_id() === $_GET['section'] );
	}


	/**
	 * Logs API requests & responses.
	 *
	 * Overridden to check if debug mode is enabled in the integration settings.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_api_request_logging() {

		$settings = get_option( 'woocommerce_google_analytics_pro_settings', []);

		if ( ! isset( $settings['debug_mode'] ) || 'no' === $settings['debug_mode'] ) {
			return;
		}

		parent::add_api_request_logging();
	}


	/**
	 * Handles deactivating the free integration if needed.
	 *
	 * @since 1.0.0
	 */
	private function deactivate_free_integration() {

		// simply deactivate the free plugin
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		deactivate_plugins( 'woocommerce-google-analytics-integration/woocommerce-google-analytics-integration.php' );

		// hide the free integration's connection notice, if it hasn't already been dismissed
		wc_enqueue_js( "
			jQuery( function( $ ) {
				$( 'a[href$=\"page=wc-settings&tab=integration&section=google_analytics\"]' ).closest( 'div.updated' ).hide();
			} );
		" );

		$this->get_admin_notice_handler()->add_admin_notice(
			/* translators: Placeholder: %s - WooCommerce Google Analytics Pro (plugin name) */
			sprintf( __( '%s: The free WooCommerce Google Analytics integration has been deactivated and is not needed when the Pro version is active.', 'woocommerce-google-analytics-pro' ), '<strong>' . $this->get_plugin_name() . ':</strong>' ),
			'free-integration',
			array(
				'dismissible'  => true,
				'notice_class' => 'updated'
			)
		);
	}


	/**
	 * Adds various admin notices to assist with proper setup and configuration.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		$integration = $this->get_integration();

		// onboarding notice
		if ( ! $integration->is_connected() ) {

			if ( $this->is_plugin_settings() ) {

				// just show "read the docs" notice when on settings
				$notice = sprintf(
					/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - <a> tag, %4$s - </a> tag */
					__( '%1$sNeed help setting up WooCommerce Google Analytics Pro?%2$s Please %3$sread the documentation%4$s.', 'woocommerce-google-analytics-pro' ),
					'<strong>',
					'</strong>',
					'<a target="_blank" href="' . esc_url( $this->get_documentation_url() ) . '">',
					'</a>'
				);

			} else {

				// show "Connect to GA" notice everywhere else
				$notice = sprintf(
					/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - <a> tag, %4$s - </a> tag */
					__( '%1$sWooCommerce Google Analytics Pro is almost ready!%2$s To get started, please ​%3$sconnect to Google Analytics%4$s.', 'woocommerce-google-analytics-pro' ),
					'<strong>',
					'</strong>',
					'<a href="' . esc_url( $this->get_settings_url() ) . '">',
					'</a>'
				);
			}

			$this->get_admin_notice_handler()->add_admin_notice( $notice, 'onboarding', array(
				'dismissible'             => true,
				'notice_class'            => 'updated',
				'always_show_on_settings' => false,
			) );
		}

		// add GA4 compatibility notices
		$this->add_ga4_compatibility_notice();
		$this->add_ua_warning_notice();
		$this->add_optimize_warning_notice();
	}


	/**
	 * Adds the GA4 compatibility notice when viewing plugins page or GA settings.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function add_ga4_compatibility_notice(): void {
		global $pagenow;

		if ( 'plugins.php' !== $pagenow && ! $this->is_plugin_settings() ) {
			return;
		}

		if ( $this->get_integration()->get_option( 'ga4_property' ) ) {
			return;
		}

		$notice = sprintf(
			/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag */
			__( '%1$sWooCommerce Google Analytics Pro is now compatible with GA4!%2$s You can now add GA4 properties for event tracking.', 'woocommerce-google-analytics-pro' ),
			'<strong>',
			'</strong>',
			'<a href="' . esc_url( $this->get_settings_url() ) . '">',
			'</a>'
		);

		$notice .= '<div>';
		$notice .= '<p><a href="https://woocommerce.com/document/woocommerce-google-analytics-pro/#section-3" target="_blank">' . __( 'How to set up GA4?' ) . '</a></p>';
		$notice .= ! $this->is_plugin_settings() ? '<a class="button-primary" href="' . esc_url( $this->get_settings_url() ) . '">' . __( 'Set up now' ) . '</a>' : '';
		$notice .= '</div>';

		$this->get_admin_notice_handler()->add_admin_notice( $notice, 'ga4-compatibility', [ 'always_show_on_settings' => false, 'dismissible' => true ] );
	}


	/**
	 * Adds the UA warning notice when viewing GA settings.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function add_ua_warning_notice(): void {

		if ( ! $this->is_plugin_settings() || ! $this->get_integration()->get_option( 'property' ) ) {
			return;
		}

		$notice = __( 'Google is retiring Universal Analytics in July 2023. After that, Universal Analytics settings will no longer be available, and your store will no longer track events in Universal Analytics.', 'woocommerce-google-analytics-pro' );

		$notice .= '<div>';
		$notice .= '<p><a href="https://support.google.com/analytics/answer/11583528?hl=en" target="_blank">' . __( 'Learn more from Google' ) . '</a></p>';
		$notice .= '</div>';

		$this->get_admin_notice_handler()->add_admin_notice( $notice, 'ua-warning', [ 'always_show_on_settings' => true, 'notice_class' => 'notice-warning' ] );
	}


	/**
	 * Adds the Google Optimize warning notice when viewing GA settings.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function add_optimize_warning_notice(): void {

		if ( ! $this->is_plugin_settings() || ! wc_string_to_bool( $this->get_integration()->get_option( 'enable_google_optimize' ) ) ) {
			return;
		}

		$notice = __( 'Google is retiring Google Optimize. It will no longer be available after September 30, 2023.', 'woocommerce-google-analytics-pro' );

		$notice .= '<div>';
		$notice .= '<p><a href="https://support.google.com/optimize/answer/12979939?hl=en" target="_blank">' . __( 'Learn more from Google' ) . '</a></p>';
		$notice .= '</div>';

		$this->get_admin_notice_handler()->add_admin_notice( $notice, 'optimize-warning', [ 'always_show_on_settings' => true, 'notice_class' => 'notice-warning' ] );
	}


	/**
	 * Adds delayed admin notices on the Integration page if Analytics profile settings are not correct.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_delayed_admin_notices() {

		// warn about deprecated javascript function name
		if ( get_option( 'woocommerce_google_analytics_upgraded_from_gatracker' ) && '__gaTracker' === $this->get_integration()->get_option( 'function_name' ) ) {

			$this->get_admin_notice_handler()->add_admin_notice(
				/* translators: %1$s - function name, %2$s, %4$s - opening <a> tag, %3$s, %5$s - closing </a> tag */
				sprintf( esc_html__( 'Please update any custom tracking code & switch the Google Analytics javascript tracker function name to %1$s in the %2$sGoogle Analytics settings%3$s. You can %4$slearn more from the plugin documentation%5$s.', 'woocommerce-google-analytics-pro' ), '<code>ga</code>', '<a href="' . $this->get_settings_url() . '#woocommerce_google_analytics_pro_additional_settings_section">', '</a>', '<a href="' . $this->get_documentation_url() . '#upgrading">', '</a>' ),
				'update_function_name',
				[
					'dismissible'             => true,
					'notice_class'            => 'error',
					'always_show_on_settings' => true
				]
			);
		}
	}


	/**
	 * Returns the plugin singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @see wc_google_analytics_pro()
	 *
	 * @return Plugin the plugin singleton instance
	 */
	public static function instance() : self {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


}


class_alias( Plugin::class, 'WC_Google_Analytics_Pro' );
