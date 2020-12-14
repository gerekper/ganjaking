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
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * # WooCommerce Google Analytics Pro Main Plugin Class.
 *
 * @since 1.0.0
 */
class WC_Google_Analytics_Pro extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.10.0';

	/** @var \WC_Google_Analytics_Pro the singleton instance of the plugin */
	protected static $instance;

	/** the plugin ID */
	const PLUGIN_ID = 'google_analytics_pro';

	/** @var string the integration class name */
	const INTEGRATION_CLASS = 'WC_Google_Analytics_Pro_Integration';

	/** @var \WC_Google_Analytics_Pro_Integration the integration class instance */
	private $integration;

	/** @var \WC_Google_Analytics_Pro_AJAX the AJAX class instance */
	protected $ajax;

	/** @var \WC_Google_Analytics_Pro_Subscriptions_Integration the Subscriptions Integration class instance */
	protected $subscriptions_integration;

	/** @var bool whether we have run analytics profile checks */
	private $has_run_analytics_profile_checks = false;


	/**
	 * Constructs the class and initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-google-analytics-pro',
			)
		);

		// loads handlers a bit earlier than the standard framework initialization
		// make sure that the add_action() call in SV_WC_Plugin::add_hooks() in v5_4_1 matches the remove_action() call below
		if ( remove_action( 'plugins_loaded', [ $this, 'init_plugin' ], 15 ) ) {
			add_action( 'after_setup_theme', [ $this, 'init_plugin' ], 0 );
		}

		// add the plugin to available WooCommerce integrations
		add_filter( 'woocommerce_integrations', array( $this, 'load_integration' ) );
	}


	/**
	 * Loads and initializes the lifecycle handler instance.
	 *
	 * @since 1.6.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/class-wc-google-analytics-pro-lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Google_Analytics_Pro\Lifecycle( $this );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function init_plugin() {

		// NOTE: since the plugin is loaded earlier than usual, we need to make sure the translations textdomain is available before gettext strings are loaded below
		$this->load_plugin_textdomain();

		$this->includes();

		// check if free WooCommerce Google Analytics integration is activated and deactivate it
		if ( is_admin() && $this->is_plugin_active( 'woocommerce-google-analytics-integration.php' ) ) {

			$this->deactivate_free_integration();
		}
	}


	/**
	 * Includes the required files.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		// load base integration
		$this->load_integration();

		// load subscriptions integration
		if ( $this->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {
			$this->subscriptions_integration = $this->load_class( '/includes/class-wc-google-analytics-pro-subscriptions-integration.php', 'WC_Google_Analytics_Pro_Subscriptions_Integration' );
		}

		// AJAX includes
		if ( is_ajax() ) {
			$this->load_class( '/includes/class-wc-google-analytics-pro-ajax.php', 'WC_Google_Analytics_Pro_AJAX' );
		}
	}


	/**
	 * Adds GA Pro as a WooCommerce integration.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $integrations the existing integrations (optional argument, used in callback only)
	 * @return string[]
	 */
	public function load_integration( $integrations = array() ) {

		if ( ! class_exists( self::INTEGRATION_CLASS ) ) {
			require_once( $this->get_plugin_path() . '/includes/class-sv-wc-tracking-integration.php' );
			require_once( $this->get_plugin_path() . '/includes/class-wc-google-analytics-pro-integration.php' );
		}

		if ( ! in_array( self::INTEGRATION_CLASS, $integrations, true ) ) {
			$integrations[ self::PLUGIN_ID ] = self::INTEGRATION_CLASS;
		}

		return $integrations;
	}


	/**
	 * Returns the integration class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Google_Analytics_Pro_Integration the integration class instance
	 */
	public function get_integration() {

		if ( null === $this->integration ) {

			$integrations = null === WC()->integrations ? [] : WC()->integrations->get_integrations();
			$integration  = self::INTEGRATION_CLASS;

			if ( isset( $integrations[ self::PLUGIN_ID ] ) && $integrations[ self::PLUGIN_ID ] instanceof $integration ) {

				$this->integration = $integrations[ self::PLUGIN_ID ];

			} else {

				$this->load_integration();

				$this->integration = new $integration();
			}
		}

		return $this->integration;
	}


	/**
	 * Returns the integration class instance.
	 *
	 * @see \WC_Google_Analytics_Pro::get_integration() alias
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Google_Analytics_Pro_Integration
	 */
	public function get_integration_instance() {

		return $this->get_integration();
	}


	/**
	 * Returns the AJAX class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return \WC_Google_Analytics_Pro_AJAX the AJAX class instance
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Returns the Subscriptions integration class instance.
	 *
	 * @since 1.5.0
	 *
	 * @return \WC_Google_Analytics_Pro_Subscriptions_Integration
	 */
	public function get_subscriptions_integration_instance() {

		return $this->subscriptions_integration;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.0.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

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

		return __FILE__;
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

		$deprecated_hooks = array(
			'wc_google_analytics_pro_product_funnel_steps' => array(
				'version' => '1.3.0',
				'removed' => true,
			),
		);

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

		$settings = get_option( 'woocommerce_google_analytics_pro_settings', array() );

		if ( ! isset( $settings['debug_mode'] ) || 'off' === $settings['debug_mode'] ) {
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

		// onboarding notice
		if ( ! $this->get_integration()->get_access_token() && 'yes' !== $this->get_integration()->get_option( 'use_manual_tracking_id' ) ) {

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

		// show MonsterInsights-related notices
		if ( $this->is_monsterinsights_active() && $this->is_plugin_settings() ) {

			// Google Analytics by Yoast was renamed to MonsterInsights in 5.4.9
			$plugin_name = $this->is_monsterinsights_lt_5_4_9() ? 'Google Analytics by Yoast' : 'Google Analytics by MonsterInsights';

			// warn about MonsterInsights's settings taking over ours
			$this->get_admin_notice_handler()->add_admin_notice(
				'<strong>' . $this->get_plugin_name() . ':</strong> ' .
				/* translators: placeholders: %s - plugin name */
				sprintf( __( '%s is active. Its settings will take precedence over the values set in the "Tracking Settings" section.', 'woocommerce-google-analytics-pro' ), $plugin_name ),
				'yoast-active',
				array(
					'dismissible'             => true,
					'always_show_on_settings' => false,
				)
			);

			// warn about MonsterInsights in debug mode
			if ( $this->get_monsterinsights_option( 'debug_mode' ) ) {

				$this->get_admin_notice_handler()->add_admin_notice(
					'<strong>' . $this->get_plugin_name() . ':</strong> ' .
					/* translators: placeholders: %s - plugin name */
					sprintf( __( '%s is set to Debug Mode. Please disable debug mode so WooCommerce Google Analytics Pro can function properly.', 'woocommerce-google-analytics-pro' ), $plugin_name ),
					'yoast-in-debug'
				);
			}

			// warn about MonsterInsights not having universal tracking enabled
			if ( $this->is_monsterinsights_lt_6() && ! $this->get_monsterinsights_option( 'enable_universal' ) ) {

				$this->get_admin_notice_handler()->add_admin_notice(
					'<strong>' . $this->get_plugin_name() . ':</strong> ' .
					/* translators: placeholders: %s - plugin name */
					sprintf( __( 'WooCommerce Google Analytics Pro requires Universal Analytics. Please enable Universal Analytics in %s.', 'woocommerce-google-analytics-pro' ), $plugin_name ),
					'yoast-in-non-universal'
				);
			}
		}

		// show a notice if we detect that the web property is being tracked more than once
		if ( 'yes' === get_transient( 'wc_' . $this->get_id() . '_site_has_duplicate_tracking_codes' ) ) {

			$this->get_admin_notice_handler()->add_admin_notice(
				'<strong>' . __( 'Google Analytics Pro:', 'woocommerce-google-analytics-pro' ) . '</strong>' . ' ' .
				__( "Heads up! We've detected that another plugin is sending duplicated events to Google Analytics, which can result in duplicated tracking data. Please disable any other plugins tracking events in Google Analytics while using Google Analytics Pro.", 'woocommerce-google-analytics-pro' ),
				'duplicate-google-analytics-tracking-code',
				[
					'notice_class' => 'error',
				]
			);
		}
	}


	/**
	 * Adds delayed admin notices on the Integration page if Analytics profile settings are not correct.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_delayed_admin_notices() {

		$this->check_analytics_profile_settings();

		// warn about deprecated javascript function name
		if ( get_option( 'woocommerce_google_analytics_upgraded_from_gatracker' ) && '__gaTracker' === $this->get_integration()->get_option( 'function_name' ) ) {

			$this->get_admin_notice_handler()->add_admin_notice(
				/* translators: %1$s - function name, %2$s, %4$s - opening <a> tag, %3$s, %5$s - closing </a> tag */
				sprintf( esc_html__( 'Please update any custom tracking code & switch the Google Analytics javascript tracker function name to %1$s in the %2$sGoogle Analytics settings%3$s. You can %4$slearn more from the plugin documentation%5$s.', 'woocommerce-google-analytics-pro' ), '<code>ga</code>', '<a href="' . $this->get_settings_url() . '#woocommerce_google_analytics_pro_additional_settings_section">', '</a>', '<a href="https://docs.woocommerce.com/document/woocommerce-google-analytics-pro/#upgrading">', '</a>' ),
				'update_function_name',
				array(
					'dismissible'             => true,
					'notice_class'            => 'error',
					'always_show_on_settings' => true
				)
			);
		}
	}


	/**
	 * Checks the Google Analytics profiles for correct settings.
	 *
	 * @since 1.0.0
	 */
	private function check_analytics_profile_settings() {

		if (    ! $this->has_run_analytics_profile_checks
		     &&   $this->is_plugin_settings() ) {

			$integration = $this->get_integration();

			if (    $integration
			     && $integration->get_access_token()
			     && 'yes' !== $integration->get_option( 'use_manual_tracking_id', 'no' ) ) {

				$analytics   = $integration->get_management_api();
				$account_id  = $integration->get_ga_account_id();
				$property_id = $integration->get_ga_property_id();

				if ( $account_id && $property_id ) {

					try {

						$views       = $analytics->get_profiles( $account_id, $property_id );
						$profiles    = $views->list_views();
						$ec_disabled = $currency_mismatch =[];

						foreach ( $profiles as $profile ) {

							if ( ! isset( $profile->id, $profile->internalWebPropertyId, $profile->name ) ) {
								continue;
							}

							$profile_id           = $profile->id;
							$property_internal_id = $profile->internalWebPropertyId;

							if ( empty( $profile->eCommerceTracking ) ) {

								$url  = "https://analytics.google.com/analytics/web/?authuser=1#/a{$account_id}w{$property_internal_id}p{$profile_id}/admin/ecommerce/settings";
								$link = '<a href="' . $url . '" target="_blank">' . $profile->name . '</a>';

								$ec_disabled[] = array(
									'url'  => $url,
									'link' => $link,
								);
							}

							if ( isset( $profile->currency ) && $profile->currency !== get_woocommerce_currency() ) {

								$url  = "https://analytics.google.com/analytics/web/?authuser=1#/a{$account_id}w{$property_internal_id}p{$profile_id}/admin/view/settings";
								$link = '<a href="' . $url . '" target="_blank">' . $profile->name . '</a>';

								$currency_mismatch[] = array(
									'url'      => $url,
									'link'     => $link,
									'currency' => $profile->currency,
								);
							}
						}

						$plugin_name = '<strong>' . $this->get_plugin_name() . '</strong>';

						if ( ! empty( $ec_disabled ) ) {

							if ( 1 === count( $ec_disabled ) ) {
								$message = sprintf(
									/* translators: Placeholders: %1$s - plugin name (bold), %2$s - opening HTML <a> link tag, %3$s - closing HTML </a> link tag */
									__( '%1$s: WooCommerce Google Analytics Pro requires Enhanced Ecommerce to be enabled. Please enable Enhanced Ecommerce on your %2$sGoogle Analytics View%3$s.', 'woocommerce-google-analytics-pro' ),
									$plugin_name,
									'<a href="' . $ec_disabled[0]['url'] . '" target="_blank">', '</a>'
								);
							} else {
								$message = sprintf(
									/* translators: Placeholders: %1$s - plugin name (bold), %2$s - a list of links */
									__( '%1$s: WooCommerce Google Analytics Pro requires Enhanced Ecommerce to be enabled. Please enable Enhanced Ecommerce on the following Google Analytics Views: %2$s', 'woocommerce-google-analytics-pro' ),
									$plugin_name,
									'<ul><li>' . implode( '</li><li>', wp_list_pluck( $ec_disabled, 'link' ) ) . '</li></ul>'
								);
							}

							$this->get_admin_notice_handler()->add_admin_notice(
								$message,
								'enhanced-ecommerce-not-enabled'
							);
						}

						if ( ! empty( $currency_mismatch ) ) {

							if ( 1 === count( $currency_mismatch ) ) {
								$message = sprintf(
									/* translators: Placeholders: %1$s - plugin name, %2$s and %3$s - currency code, e.g. USD, %4$s - <a> tag, %5$s - </a> tag */
									__( '%1$s: Your Google Analytics View currency (%2$s) does not match WooCommerce currency (%3$s). You can change it %4$son your Google Analytics View%5$s.', 'woocommerce-google-analytics-pro' ),
									$plugin_name,
									$currency_mismatch[0]['currency'],
									get_woocommerce_currency(),
									'<a href="' . $currency_mismatch[0]['url'] . '" target="_blank">', '</a>'
								);
							} else {
								$message = sprintf(
									/* translators: Placeholders: %1$s - plugin name, %2$s - currency code, %3$s - a list of links */
									__( '%1$s: Your Google Analytics Views currencies does not match WooCommerce currency (%2$s). You can change it on the following Google Analytics Views: %3$s', 'woocommerce-google-analytics-pro' ),
									$plugin_name,
									get_woocommerce_currency(),
									'<ul><li>' . implode( '</li><li>', wp_list_pluck( $currency_mismatch, 'link' ) ) . '</li></ul>'
								);
							}

							$this->get_admin_notice_handler()->add_admin_notice(
								$message,
								'analytics-currency-mismatch',
								[
									'dismissible'             => true,
									'always_show_on_settings' => false
								]
							);
						}

						$this->has_run_analytics_profile_checks = true;

					} catch ( \Exception $e ) {

						$this->log( $e->getMessage() );
					}
				}
			}
		}
	}


	/**
	 * Determines if "Google Analytics by MonsterInsights" (formerly Google Analytics by Yoast) is active.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_monsterinsights_active() {

		return    defined( 'GAWP_VERSION' )              // Yoast 4.1 - 5.x
		       || class_exists( 'MonsterInsights_Lite' ) // MonsterInsights 6.x Free
		       || class_exists( 'MonsterInsights' );     // MonsterInsights 6.x Pro
	}


	/**
	 * Returns a "Google Analytics by MonsterInsights" option.
	 *
	 * This also includes MonsterInsights / Yoast pre v6.
	 *
	 * @since 1.3.0
	 *
	 * @param string $option_name the option name
	 * @return mixed|null
	 */
	public function get_monsterinsights_option( $option_name ) {

		$options = array();

		if ( function_exists( 'monsterinsights_get_options' ) ) {
			$options = monsterinsights_get_options();
		} elseif ( class_exists( 'Yoast_GA_Options' ) ) {
			$options = (array) Yoast_GA_Options::instance()->options;
		}

		return isset( $options[ $option_name ] ) ? $options[ $option_name ] : null;
	}


	/**
	 * Returns the "Google Analytics by MonsterInsights" version.
	 *
	 * This also includes MonsterInsights / Yoast pre v6.
	 *
	 * @since 1.3.0
	 *
	 * @return string|null
	 */
	public function get_monsterinsights_version() {

		return defined( 'MONSTERINSIGHTS_VERSION' ) ? MONSTERINSIGHTS_VERSION : ( defined( 'GAWP_VERSION' ) ? GAWP_VERSION : null );
	}


	/**
	 * Checks whether the currently installed version of MonsterInsights is less than 6.0.0
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_monsterinsights_lt_6() {

		return version_compare( $this->get_monsterinsights_version(), '6.0.0', '<' );
	}


	/**
	 * Checks whether the currently installed version of MonsterInsights is at least 6.0.0
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_monsterinsights_gte_6() {

		return version_compare( $this->get_monsterinsights_version(), '6.0.0', '>' );
	}


	/**
	 * Checks whether the currently installed version of MonsterInsights is less than 5.4.9
	 *
	 * Note: 5.4.9 was significant as the plugin was renamed then.
	 *
	 * @internal
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_monsterinsights_lt_5_4_9() {

		return version_compare( $this->get_monsterinsights_version(), '5.4.9', '<' );
	}


	/**
	 * Returns the plugin singleton instance.
	 *
	 * @see wc_google_analytics_pro()
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Google_Analytics_Pro the plugin singleton instance
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


}


/**
 * Returns the one true instance of Google Analytics Pro.
 *
 * @since 1.0.0
 *
 * @return \WC_Google_Analytics_Pro
 */
function wc_google_analytics_pro() {

	return \WC_Google_Analytics_Pro::instance();
}
