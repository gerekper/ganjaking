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
		$this->id               = 'settings';
		$this->form_title       = _x( 'Newsletter', 'settings page title', 'woocommerce-subscribe-to-newsletter' );
		$this->form_description = _x( 'Configure your newsletter provider.', 'settings page description', 'woocommerce-subscribe-to-newsletter' );

		parent::__construct();
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
		$providers = array( 'mailchimp', 'cmonitor', 'mailpoet' );

		$form_fields = array(
			'woocommerce_newsletter_service' => array(
				'type'     => 'select',
				'title'    => _x( 'Service provider', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
				'desc_tip' => _x( 'Choose which service is handling your subscribers.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'disabled' => $connected,
				'options'  => array(
					'mailchimp' => _x( 'MailChimp', 'setting option', 'woocommerce-subscribe-to-newsletter' ),
					'cmonitor'  => _x( 'Campaign Monitor', 'setting option', 'woocommerce-subscribe-to-newsletter' ),
					'mailpoet'  => _x( 'MailPoet', 'setting option', 'woocommerce-subscribe-to-newsletter' ),
				),
			),
		);

		if ( $connected ) {
			$provider = wc_newsletter_subscription_get_provider();

			if ( $provider ) {
				$form_fields = array_merge(
					$form_fields,
					$this->get_provider_form_fields( $provider, $connected )
				);
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
			foreach ( $providers as $provider ) {
				$form_fields = array_merge(
					$form_fields,
					$this->get_provider_form_fields( $provider, $connected )
				);
			}
		}

		$this->form_fields = $form_fields;
	}

	/**
	 * Gets the form fields for the specified provider.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed $provider  Provider ID or object.
	 * @param bool  $connected Optional. Whether the provider is connected. Default false.
	 * @return array
	 */
	protected function get_provider_form_fields( $provider, $connected = false ) {
		$form_fields = array();
		$provider_id = ( is_string( $provider ) ? $provider : $provider->get_id() );
		$method      = 'get_' . $provider_id . '_form_fields';

		if ( method_exists( $this, $method ) ) {
			$form_fields = call_user_func( array( $this, $method ), $provider, $connected );
		}

		return $form_fields;
	}

	/**
	 * Gets the MailChimp form fields.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed $provider  Object provider.
	 * @param bool  $connected Whether the provider is connected.
	 * @return array
	 */
	protected function get_mailchimp_form_fields( $provider = null, $connected = false ) {
		$fields = array(
			'woocommerce_mailchimp_api_key' => array(
				'type'        => 'text',
				'title'       => _x( 'MailChimp API Key', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
				'desc_tip'    => _x( 'Enter your MailChimp api key', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'disabled'    => $connected,
				'description' => sprintf(
					/* translators: %s: MailChimp URL for getting the API key */
					_x( 'You can obtain your API key by logging in to your <a href="%s" target="_blank">MailChimp account</a>.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
					esc_url( 'https://admin.mailchimp.com/account/api/' )
				),
			),
		);

		if ( $connected ) {
			$fields = array_merge(
				$fields,
				array(
					'woocommerce_mailchimp_list'          => array(
						'type'     => 'select',
						'title'    => _x( 'MailChimp List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip' => _x( 'Choose a list customers can subscribe to.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'options'  => $this->get_provider_list_choices( $provider ),
					),
					'woocommerce_mailchimp_double_opt_in' => array(
						'type'    => 'checkbox',
						'title'   => _x( 'Enable Double Opt-in?', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'label'   => _x( 'Controls whether a double opt-in confirmation message is sent, defaults to true. Abusing this may cause your account to be suspended.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'default' => 'yes',
					),
				)
			);
		}

		return $fields;
	}

	/**
	 * Gets the Campaign Monitor form fields.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed $provider  Object provider.
	 * @param bool  $connected Whether the provider is connected.
	 * @return array
	 */
	protected function get_cmonitor_form_fields( $provider = null, $connected = false ) {
		$fields = array(
			'woocommerce_cmonitor_api_key' => array(
				'type'        => 'text',
				'title'       => _x( 'Campaign Monitor API Key', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
				'description' => _x( 'You can obtain your API key by logging in to your Campaign Monitor account.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'desc_tip'    => _x( 'Enter your Campaign Monitor api key', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'disabled'    => $connected,
			),
		);

		if ( $connected ) {
			$fields = array_merge(
				$fields,
				array(
					'woocommerce_cmonitor_list' => array(
						'type'     => 'select',
						'title'    => _x( 'Campaign Monitor List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip' => _x( 'Choose a list customers can subscribe to.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'options'  => $this->get_provider_list_choices( $provider ),
					),
				)
			);
		}

		return $fields;
	}

	/**
	 * Gets the MailPoet form fields.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed $provider  Object provider.
	 * @param bool  $connected Whether the provider is connected.
	 * @return array
	 */
	protected function get_mailpoet_form_fields( $provider = null, $connected = false ) {
		$fields = array();

		if ( $connected ) {
			$fields = array(
				'woocommerce_mailpoet_list' => array(
					'type'        => 'select',
					'title'       => _x( 'MailPoet List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
					'desc_tip'    => _x( 'Choose a list customers can subscribe to (MailPoet WordPress plugin must be installed and configured first).', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
					'description' => _x( 'Choose a list customers can subscribe to. The <a href="https://www.mailpoet.com/" target="_blank">MailPoet</a> WordPress plugin must be installed and configured first.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
					'options'     => $this->get_provider_list_choices( $provider ),
				),
			);
		}

		return $fields;
	}

	/**
	 * Gets an array with list options for the specified provider.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed $provider Object provider.
	 * @return array
	 */
	protected function get_provider_list_choices( $provider ) {
		$lists = array( '' => __( 'Select a list...', 'woocommerce-subscribe-to-newsletter' ) );
		$lists = $lists + $provider->get_lists();

		return $lists;
	}

	/**
	 * Generates the HTML to the 'disconnect' field.
	 *
	 * @since 2.8.0
	 *
	 * @param string $key The field key.
	 * @param mixed  $data The field data.
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
}
