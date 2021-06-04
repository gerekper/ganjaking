<?php
/**
 * Provider: Campaign Monitor
 *
 * @package WC_Newsletter_Subscription/Providers
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Newsletter_Subscription_Provider_Campaign_Monitor', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Provider_Campaign_Monitor.
 */
class WC_Newsletter_Subscription_Provider_Campaign_Monitor extends WC_Newsletter_Subscription_Provider {

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
		$this->id          = 'cmonitor';
		$this->name        = 'Campaign Monitor';
		$this->privacy_url = 'https://www.campaignmonitor.com/policies/#privacy-policy';
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

		$response = $this->api_request( 'systemdate' );

		// Restore the credentials.
		$this->set_credentials( $saved_credentials );

		return ( ! is_wp_error( $response ) && isset( $response['SystemDate'] ) );
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
						'type'     => 'provider_lists',
						'title'    => _x( 'Campaign Monitor List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
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
		$lists   = array();
		$clients = $this->get_clients();

		foreach ( $clients as $client ) {
			$response = $this->api_request( 'clients/' . $client['ClientID'] . '/lists' );

			if ( ! is_wp_error( $response ) && is_array( $response ) ) {
				foreach ( $response as $list ) {
					$lists[ $list['ListID'] ] = $list['Name'];
				}
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
		$response = $this->api_request( 'lists/' . $list . '/stats' );

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
			'TotalActiveSubscribers'        => array(
				'label' => __( 'Total subscribers', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['TotalActiveSubscribers'],
			),
			'NewActiveSubscribersToday'     => array(
				'label' => __( 'Subscribers today', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['NewActiveSubscribersToday'],
			),
			'NewActiveSubscribersThisMonth' => array(
				'label' => __( 'Subscribers this month', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['NewActiveSubscribersThisMonth'],
			),
			'UnsubscribesThisMonth'         => array(
				'label' => __( 'Unsubscribes this month', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['UnsubscribesThisMonth'],
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
			'subscribers/' . $list,
			array(
				'EmailAddress'   => $subscriber->get_email(),
				'Name'           => $subscriber->get_full_name(),
				'ConsentToTrack' => 'yes',
				'Resubscribe'    => true,
			),
			'POST'
		);

		return ( is_wp_error( $response ) ? $response : $subscriber );
	}

	/**
	 * Gets the different CampaignMonitor clients
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_clients() {
		$transient = 'wc_newsletter_subscription_cmonitor_clients_' . md5( $this->get_api_key() );
		$clients   = get_transient( $transient );

		if ( ! $clients ) {
			$clients  = array();
			$response = $this->api_request( 'clients' );

			if ( ! is_wp_error( $response ) && is_array( $response ) ) {
				$clients = $response;

				if ( ! empty( $clients ) ) {
					set_transient( $transient, $clients, HOUR_IN_SECONDS );
				}
			}
		}

		return $clients;
	}

	/**
	 * Makes a request to the CampaignMonitor API.
	 *
	 * @since 3.0.0
	 *
	 * @param string $endpoint The request endpoint.
	 * @param array  $args     Optional. The request arguments. Default empty.
	 * @param string $method   Optional. The request method. Default 'GET'.
	 * @param string $version  Optional. The API version. Default '3.2'.
	 * @return mixed The request response. WP_Error on failure.
	 */
	protected function api_request( $endpoint, $args = array(), $method = 'GET', $version = '3.2' ) {
		return $this->trigger_request(
			$this->get_api_url( $version, $endpoint ),
			array(
				'method'  => $method,
				'body'    => $args,
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $this->get_api_key() ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
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
		return 'https://api.createsend.com/api/v' . wp_normalize_path( $version . '/' . untrailingslashit( $endpoint ) ) . '.json';
	}
}
