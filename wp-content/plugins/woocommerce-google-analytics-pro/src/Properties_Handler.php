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

use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;
use stdClass;

defined( 'ABSPATH' ) or exit;

/**
 * Properties handler class
 *
 * @since 2.0.0
 */
class Properties_Handler {


	/** @var bool whether we have run analytics profile checks */
	private bool $has_run_analytics_profile_checks = false;


	public function __construct() {

		// check settings in admin footer, so that we can add displayed admin notices, @see Framework\SV_WC_Plugin::add_delayed_admin_notices
		add_action( 'admin_footer', [ $this, 'check_analytics_settings' ] );
	}


	/**
	 * Checks the Google Analytics profile / property for correct settings.
	 *
	 * @since 2.0.0
	 *
	 * @internal
	 *
	 * @return void
	 */
	public function check_analytics_settings() : void {

		if ( $this->has_run_analytics_profile_checks || ! wc_google_analytics_pro()->is_plugin_settings() ) {

			return;
		}

		if ( wc_google_analytics_pro()->get_integration()->is_authenticated() ) {
			$this->check_ga_property_settings();
			$this->check_ua_property_settings();

			$this->has_run_analytics_profile_checks = true;
		}
	}


	/**
	 * Checks the selected GA4 property for correct settings.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function check_ga_property_settings() : void {

		if (! Tracking::get_measurement_id()) {
			return;
		}

		$account_id        = $this->get_ga_account_id();
		$property_id       = $this->get_ga_property_id();
		$short_account_id  = str_replace( 'accounts/', 'a', $account_id );
		$short_property_id = str_replace( 'properties/', 'p', $property_id );
		$plugin_name       = '<strong>' . wc_google_analytics_pro()->get_plugin_name() . '</strong>';
		$base_url          = "https://analytics.google.com/analytics/web/#/{$short_account_id}{$short_property_id}";

		if (! $account_id || ! $property_id) {
			return;
		}

		try {

			$admin_api = wc_google_analytics_pro()->get_api_client_instance()->get_admin_api();
			$property  = $admin_api->get_property( $property_id )->get();

			if (! $property->name) {
				return;
			}

			if ( isset( $property->currencyCode ) && $property->currencyCode !== get_woocommerce_currency() ) {

				$url = "{$base_url}/admin/property/settings";

				$message = sprintf(
					/* translators: Placeholders: %1$s - plugin name, %2$s and %3$s - currency code, e.g. USD, %4$s - <a> tag, %5$s - </a> tag */
					__( '%1$s: Your Google Analytics property currency (%2$s) does not match WooCommerce currency (%3$s). You can change it %4$son your Google Analytics Property Settings%5$s.', 'woocommerce-google-analytics-pro' ),
					$plugin_name,
					$property->currencyCode,
					get_woocommerce_currency(),
					'<a href="' . $url . '" target="_blank">', '</a>'
				);

				wc_google_analytics_pro()->get_admin_notice_handler()->add_admin_notice(
					$message,
					'ga4-currency-mismatch',
					[
						'dismissible'             => true,
						'always_show_on_settings' => false,
						'notice_class'            => 'notice-warning',
					]
				);
			}

			if ( ! wc_google_analytics_pro()->get_api_client_instance()->get_auth_instance()->get_mp_api_secret() ) {


				$url = "{$base_url}/admin/streams/table/4699708520";

				$message = sprintf(
					/* translators: Placeholders: %1$s - plugin name, %2$s - <a> tag, %3$s - </a> tag */
					__( '%1$s: The selected Data Stream for your Analytics property is missing a Measurement Protocol API secret. Please create a secret %2$son your Google Analytics Data Stream details page%3$s and then re-save settings.', 'woocommerce-google-analytics-pro' ),
					$plugin_name,
					'<a href="' . $url . '" target="_blank">', '</a>'
				);

				wc_google_analytics_pro()->get_admin_notice_handler()->add_admin_notice(
					$message,
					'ga4-missing-measurement-protocol-secret',
					[
						'dismissible'             => false,
						'always_show_on_settings' => true,
						'notice_class'            => 'notice-error',
					]
				);
			}

		} catch ( \Exception $e ) {

			wc_google_analytics_pro()->log( $e->getMessage() );
		}
	}


	/**
	 * Checks the selected UA profile (view) for correct settings.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function check_ua_property_settings() : void {

		if (! Tracking::get_tracking_id()) {
			return;
		}

		$account_id  = $this->get_ua_account_id();
		$property_id = $this->get_ua_property_id();

		if ( ! $account_id || ! $property_id ) {
			return;
		}

		try {

			$management_api = wc_google_analytics_pro()->get_api_client_instance()->get_management_api();
			$views          = $management_api->get_profiles( $account_id, $property_id );
			$profiles       = $views->list_views();
			$ec_disabled    = $currency_mismatch =[];

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

			$plugin_name = '<strong>' . wc_google_analytics_pro()->get_plugin_name() . '</strong>';

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

				wc_google_analytics_pro()->get_admin_notice_handler()->add_admin_notice(
					$message,
					'enhanced-ecommerce-not-enabled',
					[
						'notice_class' => 'notice-warning',
					]
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

				wc_google_analytics_pro()->get_admin_notice_handler()->add_admin_notice(
					$message,
					'analytics-currency-mismatch',
					[
						'dismissible'             => true,
						'always_show_on_settings' => false,
						'notice_class'            => 'notice-warning',
					]
				);
			}

		} catch ( \Exception $e ) {

			wc_google_analytics_pro()->log( $e->getMessage() );
		}
	}


	/**
	 * Returns the currently selected Universal Analytics Account ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	public function get_ga_account_id(): ?string {

		return $this->get_property_part( 'ga4_property', 0 );
	}


	/**
	 * Returns the currently selected Google Analytics property ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	public function get_ga_property_id(): ?string {

		return explode( '/dataStreams', $this->get_property_part( 'ga4_property', 1 ) )[0];
	}


	/**
	 * Returns the currently selected Google Analytics data stream ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	public function get_ga_data_stream_id(): ?string {

		return $this->get_property_part( 'ga4_property', 1 );
	}


	/**
	 * Returns the currently selected Universal Analytics Account ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	public function get_ua_account_id(): ?string {

		return $this->get_property_part( 'property', 0 );
	}


	/**
	 * Returns the currently selected Google Analytics property ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	public function get_ua_property_id(): ?string {

		return $this->get_property_part( 'property', 1 );
	}


	/**
	 * Returns the given part from the property option.
	 *
	 * @since 2.0.0
	 *
	 * @param string $option_name
	 * @param int $key the array key
	 * @return string|null
	 */
	private function get_property_part( string $option_name, int $key ) {

		if ( ! ( $property = wc_google_analytics_pro()->get_integration()->get_option( $option_name ) ) ) {
			return null;
		}

		$pieces = explode( '|', $property );

		return $pieces[$key] ?: null;
	}


	/**
	 * Gets a list of Google Analytics Universal Analytics properties.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public static function get_ua_properties(): array {

		$properties = get_transient( 'wc_google_analytics_pro_properties' );

		if ( ! is_array( $properties ) ) {

			$properties     = [];
			$management_api = wc_google_analytics_pro()->get_api_client_instance()->get_management_api();

			// try to fetch analytics accounts
			try {

				// give ourselves an unlimited timeout if possible
				@set_time_limit( 0 );

				// get the account summaries in one API call
				$account_summaries = $management_api->get_account_summaries();
				$list_summaries    = $account_summaries->list_account_summaries();

				// loop over the account summaries to get available web properties
				foreach ( $list_summaries as $account_summary ) {

					// sanity checks to ensure we have the right kind of data
					if ( ! isset( $account_summary->kind, $account_summary->id, $account_summary->name, $account_summary->webProperties ) ) {
						continue;
					}
					if ( 'analytics#accountSummary' !== $account_summary->kind ) {
						continue;
					}

					// loop over the properties to create property options
					foreach ( $account_summary->webProperties as $property ) {

						// sanity checks to ensure we have the right kind of data
						if ( ! isset( $property->kind, $property->id, $property->name ) ) {
							continue;
						}
						if ( 'analytics#webPropertySummary' !== $property->kind ) {
							continue;
						}

						$optgroup = $account_summary->name;

						if ( ! isset( $properties[ $optgroup ] ) ) {
							$properties[ $optgroup ] = [];
						}

						$properties[ $optgroup ][ $account_summary->id . '|' . $property->id ] = sprintf( '%s (%s)', $property->name, $property->id );

						// sort properties naturally
						natcasesort( $properties[ $optgroup ] );
					}
				}

				// if something goes wrong we should inform the user...
			} catch ( Framework\SV_WC_API_Exception $e ) {

				// log the error
				self::handleAPIException( $e );
			}

			if ( is_array( $properties ) ) {
				// sort properties by keys, by comparing them naturally
				uksort( $properties, 'strnatcasecmp' );
			}

			// set a 5 minute transient
			set_transient( 'wc_google_analytics_pro_properties', $properties, 5 * MINUTE_IN_SECONDS );
		}

		return $properties;
	}


	/**
	 * Gets a list of Google Analytics GA4 properties.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public static function get_ga4_properties(): array {

		if ( ! is_array( $properties = get_transient( 'wc_google_analytics_pro_ga4_properties' ) ) ) {

			$properties = [];

			// try to fetch analytics accounts
			try {

				// give ourselves an unlimited timeout if possible
				@set_time_limit( 0 );

				$admin_api         = wc_google_analytics_pro()->get_api_client_instance()->get_admin_api();
				$account_summaries = $admin_api->get_account_summaries()->list_account_summaries();

				// loop over the account summaries to get available properties
				foreach ( $account_summaries as $account_summary ) {

					if (empty( $account_summary->propertySummaries )) {
						continue;
					}

					foreach ( $account_summary->propertySummaries as $property_summary ) {

						$optgroup = $account_summary->displayName;

						if ( ! isset( $properties[ $optgroup ] ) ) {
							$properties[ $optgroup ] = [];
						}

						$key   = implode( '|', [$account_summary->account, $property_summary->property ] );
						$label = $property_summary->displayName;

						$properties[ $optgroup ][ $key ] = $label;

						// sort properties naturally
						natcasesort( $properties[ $optgroup ] );
					}
				}

				// if something goes wrong we should inform the user...
			} catch ( Framework\SV_WC_API_Exception $e ) {

				self::handleAPIException( $e );

				// just in case ensure the array is empty in case of errors
				$properties = [];
			}

			if ( is_array( $properties ) ) {
				// sort properties by keys, by comparing them naturally
				uksort( $properties, 'strnatcasecmp' );
			}

			// set a 5 minute transient
			set_transient( 'wc_google_analytics_pro_ga4_properties', $properties, 5 * MINUTE_IN_SECONDS );
		}

		return $properties;
	}


	/**
	 * Handles API Exceptions while fetching Analytics properties.
	 *
	 * @since 2.0.0
	 *
	 * @param $e
	 * @return void
	 */
	protected static function handleAPIException($e): void {

		// log the error
		wc_google_analytics_pro()->log( $e->getMessage() );

		if (! is_admin()) {
			return;
		}

		// leave an additional admin notice
		$error_code    = (int) $e->getCode();
		$plugin_name   = '<strong>' . wc_google_analytics_pro()->get_plugin_name() . '</strong> ';
		$notice_id     = wc_google_analytics_pro()->get_id() . '-account-' . get_option( 'wc_google_analytics_pro_account_id', '' ) . '-no-analytics-access';
		$notice_params = [
			'dismissible'             => true,
			'always_show_on_settings' => false,
			'notice_class'            => 'error'
		];

		// authentication error (normally 401)
		if (in_array( $error_code, [401, 403, 407], true )) {

			wc_google_analytics_pro()->get_admin_notice_handler()->add_admin_notice(
				/* translators: Placeholder: %s - plugin name, in bold */
				sprintf( esc_html__( '%s: The currently authenticated Google account does not have access to any Analytics accounts. Please re-authenticate with an account that has access to Google Analytics.', 'woocommerce-google-analytics-pro' ), $plugin_name ),
				$notice_id,
				$notice_params
			);

			return;

		}

		// possibly a timeout, or other issue
		wc_google_analytics_pro()->get_admin_notice_handler()->add_admin_notice(
			/* translators: Placeholder: %s - plugin name, in bold */
			sprintf( esc_html__( '%s: Something went wrong with the request to list the Google Analytics properties for the currently authenticated Google account. Please try again in a few minutes or try re-authenticating with your Google account.', 'woocommerce-google-analytics-pro' ), $plugin_name ),
			$notice_id,
			$notice_params
		);
	}


	/**
	 * Gets the locally stored data stream for a given property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $parent
	 * @return stdClass|null
	 */
	public static function get_ga4_property_data_stream(string $parent ) : ?stdClass {

		$data_streams = get_option( 'wc_google_analytics_pro_ga4_data_streams' );

		return $data_streams[ $parent ] ?? null;
	}


	/**
	 * Sets the locally stored data stream for a given property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $parent
	 * @param stdClass $data_stream
	 * @return stdClass
	 */
	public static function set_ga4_property_data_stream( string $parent, stdClass $data_stream ) : stdClass {

		$data_streams = get_option( 'wc_google_analytics_pro_ga4_data_streams', [] );

		$data_streams[ $parent ] = $data_stream;

		update_option( 'wc_google_analytics_pro_ga4_data_streams', $data_streams );

		return $data_stream;
	}


	/**
	 * Gets the locally stored API secret for the given data stream.
	 *
	 * @since 2.0.0
	 *
	 * @param string $parent
	 * @return stdClass|null
	 */
	public static function get_ga4_data_stream_api_secret( string $parent ) : ?stdClass {

		$api_secrets = get_option( 'wc_google_analytics_pro_ga4_data_stream_api_secrets' );

		return $api_secrets[ $parent ] ?? null;
	}


	/**
	 * Sets the locally stored API secret for the given data stream.
	 *
	 * @since 2.0.0
	 *
	 * @param string $parent
	 * @param stdClass $api_secret
	 * @return stdClass
	 */
	public static function set_ga4_data_stream_api_secret( string $parent, stdClass $api_secret ) : stdClass {

		$api_secrets = get_option( 'wc_google_analytics_pro_ga4_data_stream_api_secrets' );

		$api_secrets[ $parent ] = $api_secret;

		update_option( 'wc_google_analytics_pro_ga4_data_stream_api_secrets', $api_secrets );

		return $api_secret;
	}


}
