<?php
/**
 * Settings: General
 *
 * @package WC_Newsletter_Subscription/Admin/Settings
 * @since   2.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Newsletter_Subscription_Settings_API', false ) ) {
	include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/abstracts/abstract-wc-newsletter-subscription-settings-api.php';
}

if ( class_exists( 'WC_Newsletter_Subscription_Settings_General', false ) ) {
	return;
}

/**
 * WC_Newsletter_Subscription_Settings_General class.
 */
class WC_Newsletter_Subscription_Settings_General extends WC_Newsletter_Subscription_Settings_API {

	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$this->id = 'settings';

		parent::__construct();
	}

	/**
	 * Gets the form title.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_form_title() {
		return _x( 'Newsletter', 'settings page title', 'woocommerce-subscribe-to-newsletter' );
	}

	/**
	 * Gets the form description.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_form_description() {
		return _x( 'Configure your newsletter provider.', 'settings page description', 'woocommerce-subscribe-to-newsletter' );
	}

	/**
	 * Gets the name of the option in the WP DB.
	 *
	 * @since 2.8.0
	 *
	 * @param string $setting Optional. Setting key.
	 * @return string
	 */
	public function get_option_key( $setting = '' ) {
		return $setting;
	}

	/**
	 * Initialise form fields.
	 *
	 * @since 2.8.0
	 */
	public function init_form_fields() {
		$connected = wc_newsletter_subscription_is_connected();

		$form_fields = array(
			'woocommerce_newsletter_service' => array(
				'type'     => 'select',
				'title'    => _x( 'Service provider', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
				'desc_tip' => _x( 'Choose which service is handling your subscribers.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'disabled' => $connected,
				'options'  => array(),
			),
		);

		$provider_options = array();

		if ( $connected ) {
			$provider = wc_newsletter_subscription_get_provider();

			if ( $provider ) {
				$provider_options[ $provider->get_id() ] = $provider->get_name();

				$form_fields = array_merge( $form_fields, $provider->get_form_fields( true ) );
			}

			$form_fields = array_merge(
				$form_fields,
				array(
					'woocommerce_newsletter_disconnect_provider' => array(
						'type'        => 'disconnect',
						'title'       => _x( 'Disconnect account', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'label'       => _x( 'Disconnect', 'disconnect account button', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip'    => _x( 'Disconnect newsletter provider.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'class'       => 'button',
						'no_validate' => true,
					),
					'woocommerce_newsletter_section' => array(
						'type'  => 'title',
						'title' => _x( 'Subscription Settings', 'settings section title', 'woocommerce-subscribe-to-newsletter' ),
					),
					'woocommerce_newsletter_order_statuses' => array(
						'type'     => 'multiselect',
						'class'    => 'wc-enhanced-select-nostd',
						'title'    => _x( 'Subscribe on order statuses', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip' => _x( 'Customers will only be subscribed when the order status change to one of the selected. Subscription will happen on the first status match.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'desc'     => _x( 'Leave this field empty to subscribe the customers always when the order is created.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'options'  => wc_get_order_statuses(),
					),
					'woocommerce_newsletter_checkbox_status' => array(
						'type'    => 'select',
						'title'   => _x( 'Default checkbox status', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'label'   => _x( 'The default state of the subscribe checkbox. Be aware some countries have laws against using opt-out checkboxes.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'default' => 'checked',
						'options' => array(
							'checked'   => esc_html__( 'Checked', 'woocommerce-subscribe-to-newsletter' ),
							'unchecked' => esc_html__( 'Un-checked', 'woocommerce-subscribe-to-newsletter' ),
						),
					),
					'woocommerce_newsletter_label'   => array(
						'type'        => 'text',
						'placeholder' => _x( 'Subscribe to our newsletter', 'subscription checkbox label', 'woocommerce-subscribe-to-newsletter' ),
						'title'       => _x( 'Subscribe checkbox label', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip'    => _x( 'The text you want to display next to the "subscribe to newsletter" checkboxes.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
					),
				)
			);
		} else {
			$providers = array_keys( WC_Newsletter_Subscription_Providers::get_providers() );

			$provider_options[''] = __( 'Select a provider&hellip;', 'woocommerce-subscribe-to-newsletter' );

			foreach ( $providers as $provider_id ) {
				$provider = WC_Newsletter_Subscription_Providers::get_provider( $provider_id );

				if ( $provider ) {
					$provider_options[ $provider_id ] = $provider->get_name();

					$provider_fields = $provider->get_form_fields( false );

					if ( ! empty( $provider_fields ) ) {
						$form_fields = array_merge(
							$form_fields,
							array_merge(
								array(
									"woocommerce_newsletter_provider_{$provider_id}_section" => array(
										'type'  => 'section',
										'class' => 'newsletter-provider-fields ' . $provider_id,
									),
								),
								$provider_fields
							)
						);
					}
				}
			}
		}

		// Load the provider selector options.
		$form_fields['woocommerce_newsletter_service']['options'] = $provider_options;

		$this->form_fields = $form_fields;
	}

	/**
	 * Generates the HTML for the 'disconnect' field.
	 *
	 * @since 2.8.0
	 *
	 * @param string $key  Field key.
	 * @param array  $data Field data.
	 * @return string
	 */
	public function generate_disconnect_html( $key, $data ) {
		$defaults = array(
			'label'             => '',
			'class'             => 'button',
			'css'               => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();

		$this->output_field_start( $key, $data );

		printf(
			'<a href="%1$s" class="%2$s" style="%3$s"4$s>%5$s</a>',
			esc_url( wp_nonce_url( wc_newsletter_subscription_get_settings_url( array( 'action' => 'disconnect' ) ), 'wc_newsletter_subscription_disconnect' ) ),
			esc_attr( $data['class'] ),
			esc_attr( $data['css'] ),
			wp_kses_post( $this->get_custom_attribute_html( $data ) ),
			esc_html( $data['label'] )
		);

		$this->output_field_end( $key, $data );

		return ob_get_clean();
	}


	/**
	 * Generates the HTML for the 'provider_lists' field.
	 *
	 * @since 3.1.0
	 *
	 * @param string $key  Field key.
	 * @param array  $data Field data.
	 * @return string
	 */
	public function generate_provider_lists_html( $key, $data ) {
		ob_start();
		$this->output_field_start( $key, $data );

		$this->output_select_html( $key, $data );
		echo '<button class="refresh-lists button">' . esc_html__( 'Refresh', 'woocommerce-subscribe-to-newsletter' ) . '</button>';

		$this->output_field_end( $key, $data );
		return ob_get_clean();
	}

	/**
	 * Validates the settings.
	 *
	 * The non-returned settings won't be updated.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings The settings to validate.
	 * @return array
	 */
	public function validate_fields( $settings ) {
		$settings = parent::validate_fields( $settings );

		$provider_id = ( isset( $settings['woocommerce_newsletter_service'] ) ? $settings['woocommerce_newsletter_service'] : '' );

		if ( ! $provider_id ) {
			WC_Admin_Settings::add_error( _x( 'Select a newsletter provider.', 'settings notice', 'woocommerce-subscribe-to-newsletter' ) );
		} elseif ( ! wc_newsletter_subscription_is_connected() ) {
			$settings = $this->validate_provider( $provider_id, $settings );
		}

		return $settings;
	}

	/**
	 * Validates the provider.
	 *
	 * @since 3.0.0
	 *
	 * @param string $provider_id Provider object.
	 * @param array  $settings    The settings to validate.
	 * @return array
	 */
	protected function validate_provider( $provider_id, $settings ) {
		$provider = WC_Newsletter_Subscription_Providers::get_provider( $provider_id );

		// This provider requires a plugin to work.
		if ( method_exists( $provider, 'is_plugin_active' ) ) {
			$settings = $this->validate_provider_plugin_required( $provider, $settings );
		} elseif ( method_exists( $provider, 'get_api_key' ) ) {
			$settings = $this->validate_provider_api_key( $provider, $settings );
		}

		return $settings;
	}

	/**
	 * Validates if the plugin required by the provider is installed.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Newsletter_Subscription_Provider $provider Provider object.
	 * @param array                               $settings The settings to validate.
	 * @return array
	 */
	protected function validate_provider_plugin_required( $provider, $settings ) {
		if ( ! $provider->is_plugin_active() ) {
			unset( $settings['woocommerce_newsletter_service'] );

			WC_Admin_Settings::add_error(
				sprintf(
				/* translators: %s plugin name */
					_x( 'This provider requires the WordPress plugin "%s" to work.', 'settings notice', 'woocommerce-subscribe-to-newsletter' ),
					esc_html( $provider->get_plugin_name() )
				)
			);
		}

		return $settings;
	}

	/**
	 * Validates if the the provider API key is valid.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Newsletter_Subscription_Provider $provider Provider object.
	 * @param array                               $settings The settings to validate.
	 * @return array
	 */
	protected function validate_provider_api_key( $provider, $settings ) {
		$field_key   = "woocommerce_{$provider->get_id()}_api_key";
		$credentials = array(
			'api_key' => ( isset( $settings[ $field_key ] ) ? $settings[ $field_key ] : '' ),
		);

		if ( ! $this->validate_provider_credentials( $provider, $credentials ) ) {
			unset( $settings[ $field_key ], $settings['woocommerce_newsletter_service'] );
		}

		return $settings;
	}

	/**
	 * Validates the provider credentials.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Newsletter_Subscription_Provider $provider    Provider object.
	 * @param array                               $credentials The provider credentials.
	 * @return bool
	 */
	protected function validate_provider_credentials( $provider, $credentials ) {
		$valid       = true;
		$credentials = array_filter( $credentials );

		if ( empty( $credentials ) ) {
			$valid = false;
			WC_Admin_Settings::add_error( _x( 'Enter the provider credentials.', 'settings notice', 'woocommerce-subscribe-to-newsletter' ) );
		} elseif ( ! $provider->validate_credentials( $credentials ) ) {
			$valid = false;
			WC_Admin_Settings::add_error( _x( 'The credentials are not valid.', 'settings notice', 'woocommerce-subscribe-to-newsletter' ) );
		}

		return $valid;
	}

	/**
	 * Gets the form fields for the specified provider.
	 *
	 * @since 2.8.0
	 * @deprecated 3.0.0
	 *
	 * @param mixed $provider  Provider ID or object.
	 * @param bool  $connected Optional. Whether the provider is connected. Default false.
	 * @return array
	 */
	protected function get_provider_form_fields( $provider, $connected = false ) {
		wc_deprecated_function( __FUNCTION__, '3.0.0', 'WC_Newsletter_Subscription_Provider->get_form_fields()' );

		if ( ! $provider instanceof WC_Newsletter_Subscription_Provider ) {
			$provider = WC_Newsletter_Subscription_Providers::get_provider( $provider );
		}

		return ( $provider ? $provider->get_form_fields( $connected ) : array() );
	}

	/**
	 * Gets the MailChimp form fields.
	 *
	 * @since 2.8.0
	 * @deprecated 3.0.0
	 *
	 * @param mixed $provider  Object provider.
	 * @param bool  $connected Whether the provider is connected.
	 * @return array
	 */
	protected function get_mailchimp_form_fields( $provider = null, $connected = false ) {
		wc_deprecated_function( __FUNCTION__, '3.0.0', 'WC_Newsletter_Subscription_Provider_Mailchimp->get_form_fields()' );

		return $this->get_provider_form_fields( 'mailchimp', $connected );
	}

	/**
	 * Gets the Campaign Monitor form fields.
	 *
	 * @since 2.8.0
	 * @deprecated 3.0.0
	 *
	 * @param mixed $provider  Object provider.
	 * @param bool  $connected Whether the provider is connected.
	 * @return array
	 */
	protected function get_cmonitor_form_fields( $provider = null, $connected = false ) {
		wc_deprecated_function( __FUNCTION__, '3.0.0', 'WC_Newsletter_Subscription_Provider_Campaign_Monitor->get_form_fields()' );

		return $this->get_provider_form_fields( 'cmonitor', $connected );
	}

	/**
	 * Gets the MailPoet form fields.
	 *
	 * @since 2.8.0
	 * @deprecated 3.0.0
	 *
	 * @param mixed $provider  Object provider.
	 * @param bool  $connected Whether the provider is connected.
	 * @return array
	 */
	protected function get_mailpoet_form_fields( $provider = null, $connected = false ) {
		wc_deprecated_function( __FUNCTION__, '3.0.0', 'WC_Newsletter_Subscription_Provider_Mailpoet->get_form_fields()' );

		return $this->get_provider_form_fields( 'mailpoet', $connected );
	}

	/**
	 * Gets an array with list options for the specified provider.
	 *
	 * @since 2.8.0
	 * @deprecated 3.0.0
	 *
	 * @param mixed $provider Object provider.
	 * @return array
	 */
	protected function get_provider_list_choices( $provider ) {
		wc_deprecated_function( __FUNCTION__, '3.0.0' );

		$lists = array( '' => __( 'Select a list...', 'woocommerce-subscribe-to-newsletter' ) );
		$lists = $lists + $provider->get_lists();

		return $lists;
	}
}
