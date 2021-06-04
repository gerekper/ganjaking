<?php
/**
 * Provider: MailerLite
 *
 * @package WC_Newsletter_Subscription/Providers
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Newsletter_Subscription_Provider_Mailerlite', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Provider_Mailerlite.
 */
class WC_Newsletter_Subscription_Provider_Mailerlite extends WC_Newsletter_Subscription_Provider {

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
		$this->id          = 'mailerlite';
		$this->name        = 'MailerLite';
		$this->privacy_url = 'https://www.mailerlite.com/legal/privacy-policy/';
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

		$response = $this->api_request( 'me' );

		// Restore the credentials.
		$this->set_credentials( $saved_credentials );

		return ( ! is_wp_error( $response ) && isset( $response['account'] ) );
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
			'woocommerce_mailerlite_api_key' => array(
				'type'        => 'text',
				'title'       => _x( 'MailerLite API Key', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
				'description' => _x( 'You can obtain your API key by logging in to your MailerLite account.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'desc_tip'    => _x( 'Enter your MailerLite api key', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'disabled'    => $connected,
			),
		);

		if ( $connected ) {
			$fields = array_merge(
				$fields,
				array(
					'woocommerce_mailerlite_list' => array(
						'type'     => 'provider_lists',
						'title'    => _x( 'MailerLite List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
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
	public function fetch_lists() {
		$lists    = array();
		$response = $this->api_request( 'groups' );

		if ( ! is_wp_error( $response ) && is_array( $response ) ) {
			foreach ( $response as $list ) {
				$lists[ $list['id'] ] = $list['name'];
			}
		}

		return $lists;
	}

	/**
	 * Fetches the general stats.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $list The list to fetch the stats.
	 * @return array
	 */
	public function fetch_stats( $list ) {
		$stats = array();

		$timestamp_queries = array(
			'total' => 'now',
			'week'  => '-7 days',
			'month' => '-1 month',
		);

		foreach ( $timestamp_queries as $period => $timestamp ) {
			try {
				$datetime = new WC_DateTime( $timestamp, new DateTimeZone( 'UTC' ) );
			} catch ( Exception $e ) {
				continue;
			}

			$response = $this->api_request( 'stats', array( 'timestamp' => $datetime->getTimestamp() ) );

			if ( ! is_wp_error( $response ) && is_array( $response ) ) {
				if ( 'total' === $period ) {
					$stats[ $period ] = $response;
				} else {
					$stats[ $period ]['subscribed'] = $stats['total']['subscribed'] - $response['subscribed'];
				}
			}
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
	public function format_stats( $stats ) {
		return array(
			'total_subscribed'   => array(
				'label' => __( 'Total subscribers', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['total']['subscribed'],
			),
			'total_unsubscribed' => array(
				'label' => __( 'Total unsubscribes', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['total']['unsubscribed'],
			),
			'week_subscribed'    => array(
				'label' => __( 'Subscribers since last week', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['week']['subscribed'],
			),
			'month_subscribed'   => array(
				'label' => __( 'Subscribers since last month', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['month']['subscribed'],
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
			'groups/' . $list . '/subscribers',
			array(
				'email'       => $subscriber->get_email(),
				'name'        => $subscriber->get_first_name(),
				'fields'      => array(
					'last_name' => $subscriber->get_last_name(),
				),
				'resubscribe' => true,
			),
			'POST'
		);

		return ( is_wp_error( $response ) ? $response : $subscriber );
	}

	/**
	 * Makes a request to the MailerLite API.
	 *
	 * @since 3.0.0
	 *
	 * @param string $endpoint The request endpoint.
	 * @param array  $args     Optional. The request arguments. Default empty.
	 * @param string $method   Optional. The request method. Default 'GET'.
	 * @param string $version  Optional. The API version. Default '3.0'.
	 * @return mixed The request response. WP_Error on failure.
	 */
	protected function api_request( $endpoint, $args = array(), $method = 'GET', $version = '2' ) {
		return $this->trigger_request(
			$this->get_api_url( $version, $endpoint ),
			array(
				'method'  => $method,
				'body'    => $args,
				'headers' => array(
					'X-MailerLite-ApiKey' => $this->get_api_key(),
					'Content-Type'        => 'application/json',
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
		return 'https://api.mailerlite.com/api/v' . wp_normalize_path( $version . '/' . untrailingslashit( $endpoint ) );
	}
}
