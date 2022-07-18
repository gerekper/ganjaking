<?php
/**
 * Provider: ActiveCampaign
 *
 * @package WC_Newsletter_Subscription/Providers
 * @since   3.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Newsletter_Subscription_Provider_ActiveCampaign', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Provider_ActiveCampaign.
 */
class WC_Newsletter_Subscription_Provider_ActiveCampaign extends WC_Newsletter_Subscription_Provider {

	use WC_Newsletter_Subscription_Provider_API_Key;
	use WC_Newsletter_Subscription_Provider_Stats;

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 *
	 * @param array $credentials Optional. An array with the provider credentials.
	 */
	public function __construct( $credentials = array() ) {
		$this->id          = 'activecampaign';
		$this->name        = 'ActiveCampaign';
		$this->privacy_url = 'https://www.activecampaign.com/legal/privacy-policy/';
		$this->supports    = array(
			'stats',
		);

		parent::__construct( $credentials );
	}

	/**
	 * Validates the credentials.
	 *
	 * @since 3.5.0
	 *
	 * @param array $credentials An array with the credentials to validate.
	 * @return bool
	 */
	public function validate_credentials( $credentials ) {
		$saved_credentials = $this->get_credentials();

		$this->set_credentials( $credentials );

		$response = $this->api_request( 'users' );

		// Restore the credentials.
		$this->set_credentials( $saved_credentials );

		return ( ! is_wp_error( $response ) && isset( $response['users'] ) );
	}

	/**
	 * Gets the form fields to display on the settings page.
	 *
	 * Depending on if the provider is connected or not, the form fields may vary.
	 *
	 * @since 3.5.0
	 *
	 * @param bool $connected Is the provider connected?.
	 * @return array
	 */
	public function get_form_fields( $connected ) {
		$fields = array(
			'woocommerce_activecampaign_api_url' => array(
				'type'        => 'text',
				'title'       => _x( 'ActiveCampaign API URL', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
				'description' => _x( 'You can obtain your API URL by logging in to your ActiveCampaign account.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'desc_tip'    => _x( 'Enter your ActiveCampaign API URL', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'disabled'    => $connected,
			),
			'woocommerce_activecampaign_api_key' => array(
				'type'        => 'text',
				'title'       => _x( 'ActiveCampaign API Key', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
				'description' => _x( 'You can obtain your API key by logging in to your ActiveCampaign account.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'desc_tip'    => _x( 'Enter your ActiveCampaign API key', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'disabled'    => $connected,
			),
		);

		if ( $connected ) {
			$fields = array_merge(
				$fields,
				array(
					'woocommerce_activecampaign_list' => array(
						'type'     => 'provider_lists',
						'title'    => _x( 'ActiveCampaign List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip' => _x( 'Choose the list that contacts will subscribe to.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
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
	 * @since 3.5.0
	 *
	 * @return array
	 */
	protected function fetch_lists() {
		$lists = array();

		$response = $this->api_request( 'lists' );

		if ( ! is_wp_error( $response ) && is_array( $response['lists'] ) ) {
			foreach ( $response['lists'] as $list ) {
				$lists[ $list['id'] ] = $list['name'];
			}
		}

		return $lists;
	}

	/**
	 * Fetches the stats for the specified list.
	 *
	 * @since 3.5.0
	 *
	 * @param mixed $list The list to fetch the stats.
	 * @return array
	 */
	protected function fetch_stats( $list ) {
		$stats = array();

		$total_response = $this->api_request(
			'contacts',
			array(
				'listid' => $list,
			)
		);

		if ( ! is_wp_error( $total_response ) && isset( $total_response['meta']['total'] ) ) {
			$stats['total_contacts'] = $total_response['meta']['total'];
		}

		$active_response = $this->api_request(
			'contacts',
			array(
				'listid' => $list,
				'status' => 1,
			)
		);

		if ( ! is_wp_error( $active_response ) && isset( $active_response['meta']['total'] ) ) {
			$stats['active_contacts'] = $active_response['meta']['total'];
		}

		return $stats;
	}

	/**
	 * Formats the stats.
	 *
	 * @since 3.5.0
	 *
	 * @param mixed $stats The stats to format.
	 * @return array
	 */
	protected function format_stats( $stats ) {
		return array(
			'total_contacts'  => array(
				'label' => __( 'Total contacts', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['total_contacts'],
			),
			'active_contacts' => array(
				'label' => __( 'Active contacts', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['active_contacts'],
			),
		);
	}

	/**
	 * Subscribes a customer to the specified list.
	 *
	 * @since 3.5.0
	 *
	 * @param mixed                                 $list       The list to subscribe to the customer.
	 * @param WC_Newsletter_Subscription_Subscriber $subscriber Subscriber object.
	 * @return WC_Newsletter_Subscription_Subscriber|WP_Error Subscriber object on success. WP_Error on failure.
	 */
	public function subscribe( $list, $subscriber ) {
		$response = $this->api_request(
			'contact/sync',
			array(
				'contact' => array(
					'email'     => $subscriber->get_email(),
					'firstName' => $subscriber->get_first_name(),
					'lastName'  => $subscriber->get_last_name(),
				),
			),
			'POST'
		);

		if ( is_array( $response ) && isset( $response['contact']['id'] ) ) {
			$response = $this->add_contact_to_list( $response['contact']['id'], $list );
		}

		return ( is_wp_error( $response ) ? $response : $subscriber );
	}

	/**
	 * Adds the contact to the specified list.
	 *
	 * @since 3.5.0
	 *
	 * @param int   $contact_id The contact ID.
	 * @param mixed $list       The list to subscribe to the customer.
	 * @return mixed The request response. WP_Error on failure.
	 */
	protected function add_contact_to_list( $contact_id, $list ) {
		return $this->api_request(
			'contactLists',
			array(
				'contactList' => array(
					'list'    => $list,
					'contact' => $contact_id,
					'status'  => 1,
				),
			),
			'POST'
		);
	}

	/**
	 * Makes a request to the ActiveCampaign API.
	 *
	 * @since 3.5.0
	 *
	 * @param string $endpoint The request endpoint.
	 * @param array  $args     Optional. The request arguments. Default empty.
	 * @param string $method   Optional. The request method. Default 'GET'.
	 * @param string $version  Optional. The API version. Default '3'.
	 * @return mixed The request response. WP_Error on failure.
	 */
	protected function api_request( $endpoint, $args = array(), $method = 'GET', $version = '3' ) {
		return $this->trigger_request(
			$this->get_api_url( $version, $endpoint ),
			array(
				'method'  => $method,
				'body'    => $args,
				'headers' => array(
					'api-token' => $this->get_api_key(),
					'Accept'    => 'application/json',
				),
			)
		);
	}

	/**
	 * Gets the API URL.
	 *
	 * @since 3.5.0
	 *
	 * @param string $version  The API version.
	 * @param string $endpoint The API endpoint.
	 * @return string
	 */
	protected function get_api_url( $version, $endpoint ) {
		$api_url = get_option( 'woocommerce_activecampaign_api_url', 'https://account.api-us1.com' );

		return $api_url . wp_normalize_path( '/api/' . $version . '/' . untrailingslashit( $endpoint ) );
	}
}
