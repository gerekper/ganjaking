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

use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Auth;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;
use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The plugin integration class.
 *
 * Handles plugin settings.
 *
 * @since 1.0.0
 */
class Integration extends \WC_Integration {


	/** @var string Data Stream name to be used when creating data streams for GA4 properties */
	public const DATA_STREAM_NAME = 'WooCommerce Google Analytics Pro';

	/** @var string API secret name to be used when creating API secrets for GA4 properties */
	public const API_SECRET_NAME = 'WooCommerce Google Analytics Pro';


	/**
	 * Constructs the class.
	 *
	 * Sets up the settings page & adds the necessary hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// setup integration
		$this->id                 = 'google_analytics_pro';
		$this->method_title       = __( 'Google Analytics Pro', 'woocommerce-google-analytics-pro' );
		$this->method_description = __( "Measure your site's success using advanced eCommerce tracking via Google Analytics", 'woocommerce-google-analytics-pro' );

		// load admin form
		$this->init_form_fields();

		// save admin options
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_integration_' . $this->id, [ $this, 'process_admin_options' ] );
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'load_styles_scripts' ] );

		add_filter( 'woocommerce_settings_api_sanitized_fields_google_analytics_pro', [ $this, 'filter_admin_options' ] );
	}


	/**
	 * Loads admin styles and scripts.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function load_styles_scripts(): void {

		if ( wc_google_analytics_pro()->is_plugin_settings() ) {

			wp_enqueue_script( 'wc-google-analytics-pro-admin', wc_google_analytics_pro()->get_plugin_url() . '/assets/js/admin/wc-google-analytics-pro-admin.min.js', array( 'jquery' ), Plugin::VERSION );

			wp_localize_script( 'wc-google-analytics-pro-admin', 'wc_google_analytics_pro', array(
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'auth_url'            => wc_google_analytics_pro()->get_api_client_instance()->get_auth_instance()->get_auth_url(),
				'revoke_access_nonce' => wp_create_nonce( 'revoke-access' ),
				'i18n' => array(
					'ays_revoke'                      => esc_html__( 'Are you sure you wish to revoke access to your Google Account?', 'woocommerce-google-analytics-pro' ),
					'recommended_event_warning'       => '&#9888; ' . esc_html__( 'Using a custom name may break automated reporting.', 'woocommerce-google-analytics-pro' ),
					'recommended_event_warning_empty' => '&#9888; ' . esc_html__( 'Tracking for this event is disabled, add a name to enable tracking.', 'woocommerce-google-analytics-pro' ),
				),
			) );

			wp_enqueue_style( 'wc-google-analytics-pro-admin', wc_google_analytics_pro()->get_plugin_url() . '/assets/css/admin/wc-google-analytics-pro-admin.min.css', Plugin::VERSION );
		}
	}


	/** Helper methods ********************************************************/


	/**
	 * Determines if the integration is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {

		return 'yes' === $this->get_option( 'enabled' );
	}


	/**
	 * Determines if debug mode is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function debug_mode_on(): bool {

		return 'yes' === $this->get_option( 'debug_mode', 'no' );
	}


	/**
	 * Determines whether the integration is authenticated with Google.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_authenticated() : bool {

		return ! empty( wc_google_analytics_pro()->get_api_client_instance()->get_auth_instance()->get_access_token() );
	}


	/**
	 * Determines whether the integration has edit scope/permission for Google Analytics APIs.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_edit_scope() : bool {

		return in_array( Auth::SCOPE_ANALYTICS_EDIT, wc_google_analytics_pro()->get_api_client_instance()->get_auth_instance()->get_scopes(), true );
	}


	/**
	 * Determines whether the integration is connected.
	 *
	 * @since 1.11.0
	 *
	 * @return bool
	 */
	public function is_connected() : bool {

/** @TODO restore the line below after the proxy app change has been approved by Google */
		// return ! empty( $this->get_tracking_id() ) || ( $this->is_authenticated() && ! empty( Properties_Handler::get_ga4_properties() ) );

/** v2.0.1 temporary code -- start */

		if ( ! empty( $this->get_tracking_id() ) ) {
			return true;
		}

		if ( $this->is_authenticated() && ! empty( Properties_Handler::get_ga4_properties() ) ) {

			$ga4_property = Framework\SV_WC_Helper::get_posted_value( $this->get_field_key( 'ga4_property' ) );

			if ( ! $ga4_property ) {
				$ga4_property = $this->get_option( 'ga4_property', '' );
			}

			if ( $ga4_property ) {
				[ , $ga4_property ] = explode( '|', $ga4_property );
			}

			$ga4_property_data_stream = $this->get_plugin()->get_properties_handler_instance()->get_ga4_property_data_stream( $ga4_property );

			return $ga4_property_data_stream && $this->get_plugin()->get_properties_handler_instance()->get_ga4_data_stream_api_secret( $ga4_property_data_stream->name );
		}

		return false;

/** v2.0.1 temporary code -- end */
	}


	/**
	 * Returns the customized event name for the given event.
	 *
	 * @since 1.3.0
	 *
	 * @param string $id event ID
	 * @return string event name or an empty string
	 */
	public function get_event_name( string $id ): string {

		return $this->get_option( $this->get_event_name_field_key( $id ) );
	}


	/**
	 * Returns the pretty title for the event.
	 *
	 * @since 1.3.0
	 *
	 * @param string $id event ID
	 * @return string event title or an empty string
	 */
	public function get_event_title( string $id ): string {

		return $this->form_fields[ $this->get_event_name_field_key( $id ) ]['title'] ?? '';
	}


	/**
	 * Returns the currently selected Google Analytics Account ID.
	 *
	 * @since 1.0.0
	 * @deprecated 2.0.0
	 *
	 * @return string|null
	 */
	public function get_ga_account_id(): ?string {

		wc_deprecated_function(
			__METHOD__,
			'2.0.0',
			'wc_google_analytics_pro()->get_properties_handler_instance()->get_ua_account_id()'
		);

		return wc_google_analytics_pro()->get_properties_handler_instance()->get_ua_account_id();
	}


	/**
	 * Returns the currently selected Google Analytics property ID.
	 *
	 * @since 1.0.0
	 * @deprecated 2.0.0
	 *
	 * @return string|null
	 */
	public function get_ga_property_id(): ?string {

		wc_deprecated_function(
			__METHOD__,
			'2.0.0',
			'wc_google_analytics_pro()->get_properties_handler_instance()->get_ua_property_id()'
		);

		return wc_google_analytics_pro()->get_properties_handler_instance()->get_ua_property_id();
	}


	/**
	 * Gets the plugin instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	protected function get_plugin(): Plugin {

		return wc_google_analytics_pro();
	}


	/** Settings **************************************************************/


	/**
	 * Initializes form fields in the format required by \WC_Integration.
	 *
	 * @see \WC_Integration::init_form_fields()
	 *
	 * @since 1.0.0
	 */
	public function init_form_fields() {

		$form_fields = $this->get_auth_fields();

		if ( $this->is_connected() ) {
			$form_fields['enabled'] = [
				'title'   => __( 'Enable Google Analytics tracking', 'woocommerce-google-analytics-pro' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];
		}

		$form_fields['debug_mode'] = [
			'title'   => __( 'Debug Mode', 'woocommerce-google-analytics-pro' ),
			'label'   => __( 'Log API requests, responses, and errors for debugging. Only enable this if you experience issues!', 'woocommerce-google-analytics-pro' ),
			'type'    => 'checkbox',
			'default' => 'no',
		];

		if ( $this->is_connected() ) {

			$form_fields = array_merge(
				$form_fields,
				$this->get_tracking_settings_fields(),
				$this->get_event_name_fields(),
				$this->get_checkout_funnel_fields()
			);

			// TODO: remove this block when removing backwards compatibility with __gaTracker {IT 2016-10-12}
			if ( get_option( 'woocommerce_google_analytics_upgraded_from_gatracker' ) ) {

				$compat_fields['function_name'] = [
					'title'       => __( 'JavaScript function name', 'woocommerce-google-analytics-pro' ),
					/* translators: %1$s - function name, %2$s - function name */
					'description' => sprintf( __( 'Set the global tracker function name. %1$s is deprecated and support for it will be removed in a future version. IMPORTANT: set the function name to %2$s only after any custom code is updated to use %2$s.', 'woocommerce-google-analytics-pro' ), '<code>__gaTracker</code>', '<code>ga</code>' ),
					'type'        => 'select',
					'class'       => 'wc-enhanced-select',
					'options'     => [
						'ga'          => 'ga ' . __( '(Recommended)', 'woocommerce-google-analytics-pro' ),
						'__gaTracker' => '__gaTracker',
					],
					'default'     => '__gaTracker',
				];

				$form_fields = Framework\SV_WC_Helper::array_insert_after( $form_fields, 'additional_settings_section', $compat_fields );
			}
		}

		/**
		 * Filters Google Analytics Pro Settings.
		 *
		 * @since 1.3.0
		 *
		 * @param array $settings settings fields
		 * @param Integration $ga_pro_integration instance
		 */
		$this->form_fields = apply_filters( 'wc_google_analytics_pro_settings', $form_fields, $this );
	}


	/**
	 * Returns the authentication fields.
	 *
	 * Only when on the plugin settings screen as this requires an API call to GA to get property data.
	 *
	 * @since 1.0.0
	 *
	 * @return array the authentication fields or an empty array
	 */
	protected function get_auth_fields(): array {

		if ( ! wc_google_analytics_pro()->is_plugin_settings() ) {
			return [];
		}

		$auth_fields = [
			'auth_section' => [
				'title' => __( 'Get Connected', 'woocommerce-google-analytics-pro' ),
				'type'  => 'title',
			],
		];

		$ga_properties    = $this->is_authenticated() ? Properties_Handler::get_ga4_properties() : null;
		$ua_properties    = $this->is_authenticated() ? Properties_Handler::get_ua_properties() : null;
		$auth_button_text = $this->is_authenticated() ? esc_html__( 'Re-authenticate with your Google account', 'woocommerce-google-analytics-pro' ) : esc_html__( 'Connect your Google account', 'woocommerce-google-analytics-pro' );


/** @TODO restore the code block below after the GA proxy app update has been approved by Google */

		// if ( $this->is_authenticated() && ! $this->has_edit_scope() ) {
		// 	$auth_fields = array_merge($auth_fields, [
		// 		'ga4_property' => [
		// 			'title'       => __( 'Google Analytics 4 Property', 'woocommerce-google-analytics-pro' ),
		// 			'type'        => 'hidden',
		// 			'default'     => '',
		// 			'description' => __( 'Please re-authenticate & allow edit access to your Google Analytics account to see GA4 properties', 'woocommerce-google-analytics-pro' ),
		// 			'desc_tip'    => __( 'Choose which Google Analytics property you want to track', 'woocommerce-google-analytics-pro' ),
		// 		],
		// 	]);
		// } else if ( ! empty( $ga_properties ) ) {


/** v2.0.1 temporary code -- start */

		$ga4_property = Framework\SV_WC_Helper::get_posted_value( $this->get_field_key( 'ga4_property' ) );

		if ( ! $ga4_property ) {
			$ga4_property = $this->get_option( 'ga4_property', '' );
		}

		if ( $ga4_property ) {
			[ , $ga4_property ] = explode( '|', $ga4_property );
		}

		if ( $this->is_authenticated() && $ga4_property ) {

			// this will ensure that the wc_google_analytics_pro_ga4_data_streams and wc_google_analytics_pro_ga4_data_stream_api_secrets are populated
			// so at that point we are able to determine if to show a notice or not if a data stream for the matching property is there
			$this->ensure_ga4_property_setup( $ga4_property );

			$ga4_property_data_stream = $this->get_plugin()->get_properties_handler_instance()->get_ga4_property_data_stream( $ga4_property );

			if ( ! $ga4_property_data_stream ) {
				$auth_fields['auth_section']['description'] = sprintf( __( 'Please follow the steps found %1$shere%2$s to add a Data Stream and API Secret and then click "Save changes" below to complete setup', 'woocommerce-google-analytics-pro' ), '<a target="_blank" href="' . ( $this->get_plugin()->get_documentation_url() ) . '#data-stream">', '</a>' );
			} elseif ( ! $this->get_plugin()->get_properties_handler_instance()->get_ga4_data_stream_api_secret( $ga4_property_data_stream->name ) ) {
				$auth_fields['auth_section']['description'] = sprintf( __( 'Please follow the steps found %1$shere%2$s to add a Data Stream API Secret and then click "Save changes" below to complete setup', 'woocommerce-google-analytics-pro' ), '<a target="_blank" href="' . ( $this->get_plugin()->get_documentation_url() ) . '#data-stream">', '</a>' );
			}
		}

		if ( ! empty( $ga_properties ) ) {

/** v2.0.1 temporary code -- end */


			// add empty option so clearing the field is possible
			$ga_properties = array_merge( [ '' => '' ], $ga_properties );

			$auth_fields = array_merge( $auth_fields, [
				'ga4_property' => [
					'title'    => __( 'Google Analytics 4 Property', 'woocommerce-google-analytics-pro' ),
					'type'     => 'deep_select',
					'default'  => '',
					'class'    => 'wc-enhanced-select-nostd',
					'options'  => $ga_properties,
					'custom_attributes' => [
						'data-placeholder' => __( 'Select a property&hellip;', 'woocommerce-google-analytics-pro' ),
					],
					'desc_tip' => __( "Choose which Google Analytics property you want to track", 'woocommerce-google-analytics-pro' ),
				],
			] );
		}

		if ( ! empty( $ua_properties ) ) {

			// add empty option so clearing the field is possible
			$ua_properties = array_merge( [ '' => '' ], $ua_properties );

			// TODO: consider renaming the existing 'property' option in Lifecycle to 'ua_property' {@itambek 2023-03-13}
			$auth_fields = array_merge( $auth_fields, [
				'property' => [
					'title'    => __( 'Universal Analytics Property', 'woocommerce-google-analytics-pro' ),
					'type'     => 'deep_select',
					'default'  => '',
					'class'    => 'wc-enhanced-select-nostd',
					'options'  => $ua_properties,
					'custom_attributes' => [
						'data-placeholder' => __( 'Select a property&hellip;', 'woocommerce-google-analytics-pro' ),
					],
					'desc_tip'    => __( "Choose which Universal Analytics property you want to track.", 'woocommerce-google-analytics-pro' ),
					'description' => '&#9888; ' . sprintf( __( 'Clear this setting to disconnect Universal Analytics once you have migrated to GA4. This setting will stop working after %1$sGoogle retires Universal Analytics%2$s (expected mid-2023).', 'woocommerce-google-analytics-pro' ), '<a href="ttps://support.google.com/analytics/answer/11583528?hl=en" target="_blank">', '</a>' ),
				],
			] );
		}

		$auth_fields['oauth_button'] = [
			'type'        => 'button',
			'default'     => $auth_button_text,
			'class'       => 'button',
			'desc_tip'    => __( 'We need view & edit access to your Analytics account so we can display reports and automatically configure Analytics settings for you.', 'woocommerce-google-analytics-pro' ),
            'description' => __( 'You\'ll be prompted to choose an existing Google Analytics account. Click "Allow" to complete the connection and return here to choose a property to link to this website', 'woocommerce-google-analytics-pro' ),
		];

		if ( empty( $ua_properties ) && empty( $ga_properties ) ) {
			$auth_fields['oauth_button']['title'] = __( 'Google Analytics account', 'woocommerce-google-analytics-pro' );
		}

		if ( $this->is_authenticated() ) {
			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
			$auth_fields['oauth_button']['description'] = sprintf( __( 'or %1$srevoke authorization%2$s' ), '<a href="#" class="js-wc-google-analytics-pro-revoke-authorization">', '</a>' );
		}

		return $auth_fields;
	}


	/**
	 * Returns the Tracking Settings fields.
	 *
	 * @since 1.11.0
	 *
	 * @return array the Tracking Settings fields
	 */
	protected function get_tracking_settings_fields() : array {

		$fields = [
			'tracking_settings_section' => [
				'title' => __( 'Tracking Settings', 'woocommerce-google-analytics-pro' ),
				'type'  => 'title',
			],
		];

		// GA4 property
		// posted property needed as previous settings values pre-loads before the updated values
		$posted_ga4_property = $_POST[ $this->get_field_key( 'ga4_property' ) ] ?? '';

		if ( '' === $posted_ga4_property &&
				'' !== $this->get_option( 'measurement_id', '' ) &&
				'' === $this->get_option( 'ga4_property', '' ) ) {
			$fields = array_merge([
				'measurement_id' => [
					'title'             => __( 'Google Analytics measurement ID', 'woocommerce-google-analytics-pro' ),
					'description'       => __( 'To change your Google Analytics measurement ID, please connect a Google account.', 'woocommerce-google-analytics-pro' ),
					'type'              => 'text',
					'default'           => '',
					'placeholder'       => 'G-XXXXXXXXXX',
					'custom_attributes' => [
						'readonly' => 'readonly',
					],
				],
			], $fields);
		}

		// UA property
		// posted property needed as previous settings values pre-loads before the updated values
		$posted_ua_property = $_POST[ $this->get_field_key( 'property' ) ] ?? '';

		if ( '' === $posted_ua_property &&
		     '' !== $this->get_option( 'tracking_id', '' ) &&
		     '' === $this->get_option( 'property', '' ) ) {
			$fields = array_merge( [
				'tracking_id' => [
					'title'             => __( 'Google Analytics tracking ID', 'woocommerce-google-analytics-pro' ),
					'description'       => __( 'To change your Google Analytics tracking ID, please connect a Google account.', 'woocommerce-google-analytics-pro' ),
					'type'              => 'text',
					'default'           => '',
					'placeholder'       => 'UA-XXXXX-X',
					'custom_attributes' => [
						'readonly' => 'readonly',
					],
				],
			], $fields );
		}

		return array_merge( $fields, [
			'admin_tracking_enabled' => [
				'title'       => __( 'Track Administrators?', 'woocommerce-google-analytics-pro' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'description' => __( 'Check to enable tracking when logged in as Administrator or Shop Manager.', 'woocommerce-google-analytics-pro' ),
			],

			'enable_displayfeatures' => [
				'title'         => __( 'Tracking Options', 'woocommerce-google-analytics-pro' ),
				'label'         => __( 'Use Advertising Features', 'woocommerce-google-analytics-pro' ) . ' ' . __( '(UA only)', 'woocommerce-google-analytics-pro' ),
				'type'          => 'checkbox',
				'class'         => 'universal-analytics-option',
				'default'       => 'no',
				'checkboxgroup' => 'start',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description'   => sprintf( __( 'Set the Google Analytics code to support Demographics and Interests Reports for Remarketing and Advertising. %1$sRead more about Advertising Features%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/analytics/answer/2700409" target="_blank">', '</a>' ),
			],

			'enable_linkid' => [
				'label'       => __( 'Use Enhanced Link Attribution', 'woocommerce-google-analytics-pro' ) . ' ' . __( '(UA only)', 'woocommerce-google-analytics-pro' ),
				'type'        => 'checkbox',
				'class'         => 'universal-analytics-option',
				'default'     => 'no',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description' => sprintf( __( 'Set the Google Analytics code to support Enhanced Link Attribution. %1$sRead more about Enhanced Link Attribution%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution" target="_blank">', '</a>' ),
			],

			'anonymize_ip' => [
				'label'         => __( 'Anonymize IP addresses', 'woocommerce-google-analytics-pro' ) . ' ' . __( '(UA only)', 'woocommerce-google-analytics-pro' ),
				'type'          => 'checkbox',
				'class'         => 'universal-analytics-option',
				'default'       => 'no',
				'checkboxgroup' => '',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description'   => sprintf( __( 'Enabling this option is mandatory in certain countries due to national privacy laws. %1$sRead more about IP Anonymization%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/analytics/answer/2763052" target="_blank">', '</a>' ),
			],

			'track_user_id' => [
				'label'         => __( 'Track User ID', 'woocommerce-google-analytics-pro' ),
				'type'          => 'checkbox',
				'default'       => 'no',
				'checkboxgroup' => '',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description'   => sprintf( __( 'Enable User ID tracking. %1$sRead more about the User ID feature%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/analytics/answer/3123662" target="_blank">', '</a>' ),
			],

			'enable_google_optimize' => [
				'title'       => __( 'Google Optimize', 'woocommerce-google-analytics-pro' ),
				'label'       => __( 'Enable Google Optimize', 'woocommerce-google-analytics-pro' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description' => '&#9888; ' . sprintf( __( 'Google Optimize will be %1$sretired on September 30, 2023%2$s. Your experiences can continue to run until that date.', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/optimize/answer/12979939" target="_blank">', '</a>' ) . ' ' .  sprintf( __( '%1$sRead more about Google Optimize%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://www.google.com/analytics/optimize" target="_blank">', '</a>' ),
			],

			'google_optimize_code' => [
				'title'       => __( 'Google Optimize Code', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => '',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description' => sprintf( __( 'e.g. "GTM-XXXXXX". %1$sRead more about this code%2$s', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/360suite/optimize/answer/6262084" target="_blank">', '</a>' ),
			],

			'track_item_list_views_on' => [
				// TODO: remove the reference to 'impressions' when removing support for UA {@itambek 2023-03-24}
				'title'       => __( 'Track product list views (impressions) on:', 'woocommerce-google-analytics-pro' ),
				'desc_tip'    => __( 'Control where product list views are tracked.', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'If you\'re running into issues, particularly if you see the "No HTTP response detected" error, try disabling product impressions on archive pages.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'multiselect',
				'class'       => 'wc-enhanced-select',
				'options'     => [
					'single_product_pages' => __( 'Single Product Pages', 'woocommerce-google-analytics-pro' ),
					'archive_pages'        => __( 'Archive Pages', 'woocommerce-google-analytics-pro' ),
				],
				'default'     => [ 'single_product_pages', 'archive_pages' ],
			],
		] );
	}


	/**
	 * Returns the event name fields
	 *
	 * @since 2.0.0
	 *
	 * @return array the event name fields
	 */
	protected function get_event_name_fields() : array {

		$fields = [];

		$fields['recommended_event_names_section'] = [
			// TODO: remove the suffix when removing support for UA {@itambek 2023-03-21}
			'title'       => __( 'Recommended Events', 'woocommerce-google-analytics-pro' ) . ' (GA4)',
			'description' => sprintf(
				/** translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag, %3$s - <strong> tag, %4$s - </strong> tag */
				__( 'These events are %1$srecommended by Google%2$s to help you measure features and behavior as well as generate more useful reports. %3$sCustomizing these event names is not recommended%4$s, as it will break automated reporting in Google Analytics. Leave a field blank to disable tracking for the event.', 'woocommerce-google-analytics-pro' ),
				'<a href="' . esc_url( 'https://support.google.com/analytics/answer/9267735' ) . '" target="_blank">',
				'</a>',
				'<strong>',
				'</strong>',
			),
			'type' => 'title',
		];

		foreach ( $this->get_ga4_recommended_events() as $event ) {

			$fields[ $this->get_event_name_field_key( $event::ID ) ] = $event->get_form_field();
		}

		$fields['custom_event_names_section'] = [
			// TODO: remove the suffix when removing support for UA {@itambek 2023-03-21}
			'title'       => __( 'Custom Events', 'woocommerce-google-analytics-pro' ) . ' (GA4)',
			'description' => __( 'Customize the event names you wish to track in Google Analytics. Leave a field blank to disable tracking for the event.', 'woocommerce-google-analytics-pro' ),
			'type'        => 'title',
		];

		foreach ( $this->get_ga4_custom_events() as $event ) {

			$fields[ $this->get_event_name_field_key( $event::ID ) ] = $event->get_form_field();
		}

		$fields['event_names_section'] = [
			'title'       => __( 'Universal Analytics Events', 'woocommerce-google-analytics-pro' ),
			'description' => __( 'Customize the event names you wish to track in Google Universal Analytics. Leave a field blank to disable tracking for the event.', 'woocommerce-google-analytics-pro' ),
			'type'        => 'title',
		];

		foreach ( $this->get_ua_events() as $event ) {

			$fields[ $this->get_event_name_field_key( $event::ID ) ] = $event->get_form_field();
		}

		return $fields;
	}


	/**
	 * Gets a list of GA4 recommended events.
	 *
	 * @since 2.0.0
	 *
	 * @return GA4_Event[]
	 */
	protected function get_ga4_recommended_events() : array {

		return array_filter( wc_google_analytics_pro()->get_tracking_instance()->get_event_tracking_instance()->get_events(), static function($event) {
			return $event instanceof GA4_Event && $event->is_recommended_event();
		} );

	}


	/**
	 * Gets a list of GA4 custom events.
	 *
	 * @since 2.0.0
	 *
	 * @return GA4_Event[]
	 */
	protected function get_ga4_custom_events() : array {

		return array_filter( wc_google_analytics_pro()->get_tracking_instance()->get_event_tracking_instance()->get_events(), static function($event) {
			return $event instanceof GA4_Event && ! $event->is_recommended_event();
		} );

	}


	/**
	 * Gets a list of Universal Analytics events.
	 *
	 * @since 2.0.0
	 *
	 * @return Universal_Analytics_Event[]
	 */
	protected function get_ua_events() : array {

		return array_filter( wc_google_analytics_pro()->get_tracking_instance()->get_event_tracking_instance()->get_events(), static function($event) {
			return $event instanceof Universal_Analytics_Event;
		} );

	}


	/**
	 * Gets the event form field key.
	 *
	 * @since 2.0.0
	 *
	 * @param string $id
	 * @return string
	 */
	protected function get_event_name_field_key( string $id ) : string {

		return "{$id}_event_name";

	}


	/**
	 * Returns the Checkout Funnel fields.
	 *
	 * @since 1.11.0
	 *
	 * @return array the Checkout Funnel fields
	 */
	protected function get_checkout_funnel_fields(): array {

		return [

			'funnel_steps_section' => [
				'title'       => __( 'Checkout Funnel (UA-only)', 'woocommerce-google-analytics-pro' ),
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description' => sprintf( __( 'Configure your Analytics account to match the checkout funnel steps below to take advantage of %1$sCheckout Behavior Analysis%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/analytics/answer/6014872?hl=en#cba">', '</a>' ),
				'type'        => 'title',
			],

			'funnel_steps' => [
				'title' => __( 'Funnel Steps', 'woocommerce-google-analytics-pro' ),
				'type'  => 'ga_pro_funnel_steps',
			],

		];
	}


	/**
	 * Outputs checkout funnel steps table.
	 *
	 * @internal
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $key
	 * @param mixed $data
	 * @return string HTML
	 */
	public function generate_ga_pro_funnel_steps_html( $key, $data ): string {

		$columns = [
			'step'    => __( 'Step', 'woocommerce-google-analytics-pro' ),
			'event'   => __( 'Event', 'woocommerce-google-analytics-pro' ),
			'name'    => __( 'Name', 'woocommerce-google-analytics-pro' ),
			'status'  => __( 'Enabled', 'woocommerce-google-analytics-pro' ),
		];

		$steps = [
			1 => 'started_checkout',
			2 => 'provided_billing_email',
			3 => 'selected_payment_method',
			4 => 'placed_order',
		];

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php esc_html__( $data['title'] ); ?></th>
			<td class="forminp">
				<table class="wc-google-analytics-pro-funnel-steps widefat" cellspacing="0">
					<thead>
					<tr>
						<?php
						foreach ( $columns as $column_key => $column ) {
							echo '<th class="' . esc_attr( $column_key ) . '">' . esc_html( $column ) . '</th>';
						}
						?>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ( $steps as $step => $event_id ) {

						$event_field = $this->get_event_name_field_key( $event_id );
						$event_title = $this->get_event_title( $event_id );
						$event_name  = $this->get_event_name( $event_id );

						echo '<tr class="event-' . esc_attr( $event_id ) . '" data-event="' . esc_attr( $event_id ) . '">';

						foreach ( $columns as $column_key => $column ) {

							switch ( $column_key ) {

								case 'step' :
									echo '<td class="step">' . $step . '</td>';
									break;

								case 'event' :
									echo '<td class="event"><a href="#woocommerce_google_analytics_pro_' . esc_attr( $event_field ) . '">' . esc_html( $event_title ) . '</a></td>';
									break;

								case 'name' :
									echo '<td class="name">' . esc_html( $event_name ) . '</td>';
									break;

								case 'status' :
									echo '<td class="status">';
									echo '<span class="status-enabled tips" ' . ( ! $event_name ? 'style="display:none;"' : '' ) . ' data-tip="' . __( 'Yes', 'woocommerce-google-analytics-pro' ) . '">' . __( 'Yes', 'woocommerce-google-analytics-pro' ) . '</span>';
									echo '<span class="status-disabled tips" ' . ( $event_name ? 'style="display:none;"' : '' ) . ' data-tip="' . __( 'Currently disabled, because the event name is not set.', 'woocommerce-google-analytics-pro' ) . '">-</span>';
									echo '</td>';
									break;
							}
						}

						echo '</tr>';
					}
					?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}


	/**
	 * Generates the "deep select" field HTML.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $key the setting key
	 * @param array $data the setting data
	 * @return string the field HTML
	 */
	public function generate_deep_select_html( $key, $data ): string {

		$field    = $this->get_field_key( $key );
		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'options'           => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<select class="select <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>>

						<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>

							<?php if ( is_array( $option_value ) ) : ?>

								<optgroup label="<?php echo esc_attr( $option_key ); ?>">

								<?php foreach ( $option_value as $option_sub_key => $option_sub_value ) : ?>

									<option value="<?php echo esc_attr( $option_sub_key ); ?>" <?php selected( $option_sub_key, esc_attr( $this->get_option( $key ) ) ); ?>><?php echo esc_attr( $option_sub_value ); ?></option>

								<?php endforeach; ?>

							<?php else : ?>

								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, esc_attr( $this->get_option( $key ) ) ); ?>><?php echo esc_attr( $option_value ); ?></option>

							<?php endif; ?>

						<?php endforeach; ?>

					</select>

					<?php echo $this->get_description_html( $data ); ?>

				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}


	/**
	 * Generate Text Input HTML for event name fields.
	 *
	 * Moves the field description below the title, instead of the field and aligns the field with the description.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array  $data field data
	 * @return string
	 */
	public function generate_event_name_html( string $key, array $data ): string {

		$field_key = $this->get_field_key( $key );
		$defaults  = [
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => [],
		];

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top" class="event-name">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
				<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="text" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?> />
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}


	/**
	 * Bypasses validation for the oAuth button value.
	 *
	 * @see \WC_Settings_API::get_field_value()
	 *
	 * @internal
	 *
	 * @since 1.1.6
	 *
	 * @return string the button default value
	 */
	protected function validate_oauth_button_field(): string {

		$form_fields = $this->get_form_fields();

		return ! empty( $form_fields[ 'oauth_button' ]['default'] ) ? $form_fields[ 'oauth_button' ]['default'] : '';
	}


	/**
	 * Filters the admin options before saving.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param array $sanitized_fields
	 * @return array
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function filter_admin_options( array $sanitized_fields ): array {

		// prevent button labels from being saved
		unset( $sanitized_fields['oauth_button'] );

		// get measurement ID from GA4 property, if using oAuth, and save it to the measurement ID option
		if ( ! empty( $sanitized_fields['ga4_property'] ) ) {

			[ , $property ] = explode( '|', $sanitized_fields['ga4_property'] );

			// set the tracking ID
			$sanitized_fields['measurement_id'] = $this->ensure_ga4_property_setup( $property );
		}

		// get tracking ID from web property, if using oAuth, and save it to the tracking ID option
		if ( ! empty( $sanitized_fields['property'] ) ) {

			$parts = explode( '|', $sanitized_fields['property'] );

			// set the tracking ID
			$sanitized_fields['tracking_id'] = $parts[1];
		}

		return $sanitized_fields;
	}


	/**
	 * Ensures that the selected GA4 property has been properly set up and returns the Measurement ID.
	 *
	 * @since 2.0.0
	 *
	 * @param string $property
	 * @return ?string
	 */
	protected function ensure_ga4_property_setup( string $property ): ?string {

		// we can't set up the property without being authenticated
		if ( ! $this->is_authenticated() ) {
			return null;
		}

		$admin_api = wc_google_analytics_pro()->get_api_client_instance()->get_admin_api();

		try {

			if ( ! ( $data_stream = Properties_Handler::get_ga4_property_data_stream( $property ) ) ) {

				// try to get the data stream from the API - for example, if the stream has already been created, but our
				// access token was revoked, and we have cleared the data stream from the database
				foreach( $admin_api->get_data_streams( $property )->list_data_streams() as $stream ) {
					if ( 'WEB_DATA_STREAM' === $stream->type && $stream->displayName === self::DATA_STREAM_NAME ) {
						$data_stream = $stream;
						break;
					}
				}

				// create the data stream
				if ( ! $data_stream ) {
					$data_stream = $admin_api->create_data_stream( $property, [
						'type'          => 'WEB_DATA_STREAM',
						'displayName'   => self::DATA_STREAM_NAME,
						'webStreamData' => [
							'defaultUri' => home_url(),
						],
					] )->get_data_stream();
				}

				Properties_Handler::set_ga4_property_data_stream( $property, $data_stream );
			}

			if ( ! ( $api_secret = Properties_Handler::get_ga4_data_stream_api_secret( $data_stream->name ) ) ) {
				// try to get the API secret from the API - for example, if the secret has already been created, but our
				// access token was revoked, and we have cleared the secret from the database
				foreach( $admin_api->get_measurement_protocol_secrets( $data_stream->name )->list_measurement_protocol_secrets() as $secret ) {
					if ( $secret->displayName === self::API_SECRET_NAME ) {
						$api_secret = $secret;
						break;
					}
				}

				// create the API secret
				if (! $api_secret ) {
					try {
						// try to attest User Data Collection Acknowledgement before creating the API secret
						$admin_api->acknowledge_user_data_collection( $property );
					} catch ( Framework\SV_WC_API_Exception $e ) {
						// ignore errors, as this is not a critical step
					}

					$api_secret = $admin_api
						->create_measurement_protocol_secret( $data_stream->name, self::API_SECRET_NAME )
						->get_measurement_protocol_secret();
				}

				// save the API secret
				Properties_Handler::set_ga4_data_stream_api_secret( $data_stream->name, $api_secret );
			}

			// store current secret
			update_option( 'wc_google_analytics_pro_mp_api_secret', $api_secret->secretValue );

			return $data_stream->webStreamData->measurementId;

		} catch ( Framework\SV_WC_API_Exception $e ) {

			// log the error
			wc_google_analytics_pro()->log( $e->getMessage() );

/** @TODO restore the notice below after the GA proxy app update has been approved by Google */

			// // possibly a timeout, or other issue
			// wc_google_analytics_pro()->get_admin_notice_handler()->add_admin_notice(
			// 	/* translators: Placeholder: %1$s - plugin name, in bold; %2$s - error message */
			// 	sprintf( esc_html__( '%1$s: Something went wrong when trying to set up API access for the selected GA4 property - a Google API error occurred: %2$s. Please try again in a few minutes or try re-authenticating with your Google account.', 'woocommerce-google-analytics-pro' ), '<strong>' . wc_google_analytics_pro()->get_plugin_name() . '</strong> ', $e->getMessage() ),
			// 	wc_google_analytics_pro()->get_id() . '-account-' . get_option( 'wc_google_analytics_pro_account_id', '' ) . '-setup-error',
			// 	[
			// 		'always_show_on_settings' => true,
			// 		'notice_class'            => 'error'
			// 	]
			// );

			// return null;
		}

/** v2.0.1 temporary code -- start */

		wc_google_analytics_pro()->get_admin_notice_handler()->add_admin_notice(
		/* translators: Placeholder: %1$s - plugin name, in bold; %2$s - error message */
			sprintf( esc_html__( '%1$s: Please follow the steps found %2$shere%3$s to complete setup.', 'woocommerce-google-analytics-pro' ), '<strong>' . wc_google_analytics_pro()->get_plugin_name() . '</strong> ', '<a target="_blank" href="' . ( $this->get_plugin()->get_documentation_url() ) . '#data-stream">', '</a>' ),
			wc_google_analytics_pro()->get_id() . '-account-' . get_option( 'wc_google_analytics_pro_account_id', '' ) . '-setup-error',
			[
				'always_show_on_settings' => true,
				'dismissible'             => false,
				'notice_class'            => 'error'
			]
		);

		return null;

/** v2.0.1 temporary code -- end */
	}


	/**
	 * Initializes (loads) settings for the integration.
	 *
	 * @since 2.0.0
	 */
	public function init_settings(): void {

		parent::init_settings();

		/**
		 * Fires when Google Analytics Pro settings are loaded.
		 *
		 * @since 2.0.0
		 *
		 * @param array $settings
		 */
		do_action( 'wc_google_analytics_pro_after_settings_loaded', $this->settings );
	}


	/**
	 * Gets the configured Google Analytics measurement ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null the measurement ID
	 */
	public function get_measurement_id(): ?string {

		/**
		 * Filters the measurement ID for the Google Analytics property being used.
		 *
		 * @since 2.0.0
		 *
		 * @param ?string $measurement_id the measurement ID (if set)
		 */
		return apply_filters( 'wc_google_analytics_pro_measurement_id', $this->get_option( 'measurement_id' ) ?: null );
	}


	/**
	 * Gets the configured Google Analytics tracking ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string the tracking ID
	 */
	public function get_tracking_id(): string {

		/**
		 * Filters the tracking ID for the Google Analytics property being used.
		 *
		 * @since 2.0.0
		 *
		 * @param string $tracking_id the tracking code
		 */
		return apply_filters( 'wc_google_analytics_pro_ua_tracking_id', $this->get_option( 'tracking_id' ) );
	}


}

class_alias( Integration::class, 'WC_Google_Analytics_Pro_Integration' );
