<?php
/**
 * Provider: ActiveTrail
 *
 * @package WC_Newsletter_Subscription/Providers
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Newsletter_Subscription_Provider_ActiveTrail', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Provider_ActiveTrail.
 */
class WC_Newsletter_Subscription_Provider_ActiveTrail extends WC_Newsletter_Subscription_Provider {

	use WC_Newsletter_Subscription_Provider_API_Key;
	use WC_Newsletter_Subscription_Provider_Stats;

	/**
	 * Constructor.
	 *
	 * @since 3.2.0
	 *
	 * @param array $credentials Optional. An array with the provider credentials.
	 */
	public function __construct( $credentials = array() ) {
		$this->id          = 'activetrail';
		$this->name        = 'ActiveTrail';
		$this->privacy_url = 'https://www.activetrail.com/privacy_policy/';
		$this->supports    = array(
			'stats',
		);

		parent::__construct( $credentials );
	}

	/**
	 * Validates the credentials.
	 *
	 * @since 3.2.0
	 *
	 * @param array $credentials An array with the credentials to validate.
	 * @return bool
	 */
	public function validate_credentials( $credentials ) {
		$saved_credentials = $this->get_credentials();

		$this->set_credentials( $credentials );

		$response = $this->api_request( 'account/balance' );

		// Restore the credentials.
		$this->set_credentials( $saved_credentials );

		return ( ! is_wp_error( $response ) && isset( $response['email'] ) );
	}

	/**
	 * Gets the form fields to display on the settings page.
	 *
	 * Depending on if the provider is connected or not, the form fields may vary.
	 *
	 * @since 3.2.0
	 *
	 * @param bool $connected Is the provider connected?.
	 * @return array
	 */
	public function get_form_fields( $connected ) {
		$fields = array(
			'woocommerce_activetrail_api_key' => array(
				'type'        => 'text',
				'title'       => _x( 'ActiveTrail API Key', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
				'description' => sprintf(
					/* translators: %s: ActiveTrail URL for getting the API key */
					_x( 'You can obtain your API key by logging in to your <a href="%s" target="_blank">ActiveTrail account</a>.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
					esc_url( 'https://app.activetrail.com/Members/Settings/ApiApps.aspx' )
				),
				'desc_tip'    => _x( 'Enter your ActiveTrail API key', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'disabled'    => $connected,
			),
		);

		if ( $connected ) {
			$fields = array_merge(
				$fields,
				array(
					'woocommerce_activetrail_list' => array(
						'type'     => 'provider_lists',
						'title'    => _x( 'ActiveTrail Group', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip' => _x( 'Choose the group that contacts will subscribe to.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'options'  => array( '' => __( 'Select a group...', 'woocommerce-subscribe-to-newsletter' ) ) + $this->get_lists(),
					),
				)
			);
		}

		return $fields;
	}

	/**
	 * Fetches the available lists to subscribe to the customers.
	 *
	 * @since 3.2.0
	 *
	 * @return array
	 */
	protected function fetch_lists() {
		$lists = array();

		$response = $this->api_request( 'groups', array( 'limit' => 100 ) );

		if ( ! is_wp_error( $response ) && is_array( $response ) ) {
			foreach ( $response as $list ) {
				$lists[ $list['id'] ] = $list['name'];
			}
		}

		return $lists;
	}

	/**
	 * Fetches the stats for the specified list.
	 *
	 * @since 3.2.0
	 *
	 * @param mixed $list The list to fetch the stats.
	 * @return array
	 */
	protected function fetch_stats( $list ) {
		$stats = array();

		$response = $this->api_request( 'groups/' . $list );

		if ( ! is_wp_error( $response ) && isset( $response['counter'] ) && isset( $response['active_counter'] ) ) {
			$stats['counter']        = $response['counter'];
			$stats['active_counter'] = $response['active_counter'];
		}

		return $stats;
	}

	/**
	 * Formats the stats.
	 *
	 * @since 3.2.0
	 *
	 * @param mixed $stats The stats to format.
	 * @return array
	 */
	protected function format_stats( $stats ) {
		return array(
			'counter'        => array(
				'label' => __( 'Total subscribers', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['counter'],
			),
			'active_counter' => array(
				'label' => __( 'Total active subscribers', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['active_counter'],
			),
		);
	}

	/**
	 * Subscribes a customer to the specified list.
	 *
	 * @since 3.2.0
	 *
	 * @param mixed                                 $list       The list to subscribe to the customer.
	 * @param WC_Newsletter_Subscription_Subscriber $subscriber Subscriber object.
	 * @return WC_Newsletter_Subscription_Subscriber|WP_Error Subscriber object on success. WP_Error on failure.
	 */
	public function subscribe( $list, $subscriber ) {
		$response = $this->api_request(
			'groups/' . $list . '/members',
			array(
				'email'      => $subscriber->get_email(),
				'first_name' => $subscriber->get_first_name(),
				'last_name'  => $subscriber->get_last_name(),
				'is_deleted' => 'false', // Falsy values are removed from the args.
			),
			'POST'
		);

		return ( is_wp_error( $response ) ? $response : $subscriber );
	}

	/**
	 * Makes a request to the ActiveTrail API.
	 *
	 * @since 3.2.0
	 *
	 * @param string $endpoint The request endpoint.
	 * @param array  $args     Optional. The request arguments. Default empty.
	 * @param string $method   Optional. The request method. Default 'GET'.
	 * @return mixed The request response. WP_Error on failure.
	 */
	protected function api_request( $endpoint, $args = array(), $method = 'GET' ) {
		return $this->trigger_request(
			$this->get_api_url( $endpoint ),
			array(
				'method'  => $method,
				'body'    => $args,
				'headers' => array(
					'Authorization' => $this->get_api_key(),
					'Content-Type'  => 'application/json',
				),
			)
		);
	}

	/**
	 * Gets the API URL.
	 *
	 * @since 3.2.0
	 *
	 * @param string $endpoint The API endpoint.
	 * @return string
	 */
	protected function get_api_url( $endpoint ) {
		return 'https://webapi.mymarketing.co.il/' . wp_normalize_path( 'api/' . untrailingslashit( $endpoint ) );
	}
}
