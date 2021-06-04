<?php
/**
 * Provider: Sendgrid
 *
 * @package WC_Newsletter_Subscription/Providers
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Newsletter_Subscription_Provider_Sendgrid', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Provider_Sendgrid.
 */
class WC_Newsletter_Subscription_Provider_Sendgrid extends WC_Newsletter_Subscription_Provider {

	use WC_Newsletter_Subscription_Provider_API_Key;
	use WC_Newsletter_Subscription_Provider_Stats;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credentials Optional. An array with the provider credentials.
	 */
	public function __construct( $credentials = array() ) {
		$this->id          = 'sendgrid';
		$this->name        = 'SendGrid';
		$this->privacy_url = 'https://sendgrid.com/policies/privacy/';
		$this->supports    = array(
			'stats',
		);

		parent::__construct( $credentials );
	}

	/**
	 * Validates the credentials.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credentials An array with the credentials to validate.
	 * @return bool
	 */
	public function validate_credentials( $credentials ) {
		$saved_credentials = $this->get_credentials();

		$this->set_credentials( $credentials );

		$response = $this->api_request( 'scopes' );

		// Restore the credentials.
		$this->set_credentials( $saved_credentials );

		return ( ! is_wp_error( $response ) && isset( $response['scopes'] ) );
	}

	/**
	 * Gets the form fields to display on the settings page.
	 *
	 * Depending on if the provider is connected or not, the form fields may vary.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $connected Is the provider connected?.
	 * @return array
	 */
	public function get_form_fields( $connected ) {
		$fields = array(
			'woocommerce_sendgrid_api_key' => array(
				'type'        => 'text',
				'title'       => _x( 'SendGrid API Key', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
				'description' => _x( 'You can obtain your API key by logging in to your SendGrid account.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'desc_tip'    => _x( 'Enter your SendGrid api key', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'disabled'    => $connected,
			),
		);

		if ( $connected ) {
			$fields = array_merge(
				$fields,
				array(
					'woocommerce_sendgrid_list' => array(
						'type'     => 'provider_lists',
						'title'    => _x( 'SendGrid List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip' => _x( 'Choose a list customers can subscribe to.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'options'  => array( '' => __( 'Select a list...', 'woocommerce-subscribe-to-newsletter' ) ) + $this->get_lists(),
					),
				)
			);
		}

		return $fields;
	}

	/**
	 * Fetches the available lists to subscribe to the customers.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function fetch_lists() {
		$lists    = array();
		$response = $this->api_request( 'marketing/lists' );

		if ( ! is_wp_error( $response ) && isset( $response['result'] ) ) {
			foreach ( $response['result'] as $list ) {
				$lists[ $list['id'] ] = $list['name'];
			}
		}

		return $lists;
	}

	/**
	 * Fetches the stats for the specified list.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $list The list to fetch the stats.
	 * @return array
	 */
	protected function fetch_stats( $list ) {
		$stats    = array();
		$response = $this->api_request( 'marketing/lists/' . $list . '/contacts/count' );

		if ( ! is_wp_error( $response ) && is_array( $response ) ) {
			$stats = $response;
		}

		return $stats;
	}

	/**
	 * Formats the stats.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $stats The stats to format.
	 * @return array
	 */
	protected function format_stats( $stats ) {
		return array(
			'contact_count' => array(
				'label' => __( 'Total subscribers', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['contact_count'],
			),
		);
	}

	/**
	 * Subscribes a customer to the specified list.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed                                 $list       The list to subscribe to the customer.
	 * @param WC_Newsletter_Subscription_Subscriber $subscriber Subscriber object.
	 * @return WC_Newsletter_Subscription_Subscriber|WP_Error Subscriber object on success. WP_Error on failure.
	 */
	public function subscribe( $list, $subscriber ) {
		$response = $this->api_request(
			'marketing/contacts',
			array(
				'list_ids' => array(
					$list,
				),
				'contacts' => array(
					array(
						'email'      => $subscriber->get_email(),
						'first_name' => $subscriber->get_first_name(),
						'last_name'  => $subscriber->get_last_name(),
					),
				),
			),
			'PUT'
		);

		return ( is_wp_error( $response ) ? $response : $subscriber );
	}

	/**
	 * Makes a request to the SendGrid API.
	 *
	 * @since 3.0.0
	 *
	 * @param string $endpoint The request endpoint.
	 * @param array  $args     Optional. The request arguments. Default empty.
	 * @param string $method   Optional. The request method. Default 'GET'.
	 * @param string $version  Optional. The API version. Default '3.0'.
	 * @return mixed The request response. WP_Error on failure.
	 */
	protected function api_request( $endpoint, $args = array(), $method = 'GET', $version = '3' ) {
		return $this->trigger_request(
			$this->get_api_url( $version, $endpoint ),
			array(
				'method'  => $method,
				'body'    => $args,
				'headers' => array(
					'Authorization' => 'Bearer ' . $this->get_api_key(),
					'Content-Type'  => 'application/json',
				),
			)
		);
	}

	/**
	 * Gets the API URL.
	 *
	 * @since 3.0.0
	 *
	 * @param string $version  The API version.
	 * @param string $endpoint The API endpoint.
	 * @return string
	 */
	protected function get_api_url( $version, $endpoint ) {
		return 'https://api.sendgrid.com/v' . wp_normalize_path( $version . '/' . untrailingslashit( $endpoint ) );
	}
}
