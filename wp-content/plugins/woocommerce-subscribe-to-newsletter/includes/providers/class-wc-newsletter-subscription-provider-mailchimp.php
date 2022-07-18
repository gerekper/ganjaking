<?php
/**
 * Provider: Mailchimp
 *
 * @package WC_Newsletter_Subscription/Providers
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Newsletter_Subscription_Provider_Mailchimp', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Provider_Mailchimp.
 */
class WC_Newsletter_Subscription_Provider_Mailchimp extends WC_Newsletter_Subscription_Provider {

	use WC_Newsletter_Subscription_Provider_Stats;
	use WC_Newsletter_Subscription_Provider_API_Key {
		set_credentials as trait_set_credentials;
	}

	/**
	 * Mailchimp API URL.
	 *
	 * @var string
	 */
	protected $api_url;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credentials Optional. An array with the provider credentials.
	 */
	public function __construct( $credentials = array() ) {
		$this->id          = 'mailchimp';
		$this->name        = 'Mailchimp';
		$this->privacy_url = 'https://mailchimp.com/legal/privacy/';
		$this->supports    = array(
			'stats',
		);

		parent::__construct( $credentials );
	}

	/**
	 * Sets the provider credentials.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credentials An array with the provider credentials.
	 */
	public function set_credentials( $credentials ) {
		$this->trait_set_credentials( $credentials );

		// Clear API URL.
		$this->api_url = null;
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

		$response = $this->api_request( '' );

		// Restore the credentials.
		$this->set_credentials( $saved_credentials );

		return ( ! is_wp_error( $response ) && isset( $response['account_id'] ) );
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
						'type'     => 'provider_lists',
						'title'    => _x( 'MailChimp List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip' => _x( 'Choose a list customers can subscribe to.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'options'  => array( '' => __( 'Select a list...', 'woocommerce-subscribe-to-newsletter' ) ) + $this->get_lists(),
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
	 * Fetches the available lists to subscribe to the customers.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function fetch_lists() {
		$lists    = array();
		$response = $this->api_request(
			'lists',
			array(
				'fields' => 'lists.id,lists.name',
				'count'  => 1000,
			)
		);

		if ( ! is_wp_error( $response ) && isset( $response['lists'] ) ) {
			foreach ( $response['lists'] as $list ) {
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
		$response = $this->api_request( 'lists/' . $list, array( 'fields' => 'stats' ) );

		if ( ! is_wp_error( $response ) ) {
			$stats = $response['stats'];
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
			'member_count'                 => array(
				'label' => __( 'Total subscribers', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['member_count'],
			),
			'unsubscribe_count'            => array(
				'label' => __( 'Unsubscribes', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['unsubscribe_count'],
			),
			'member_count_since_send'      => array(
				'label' => __( 'Subscribers since last newsletter', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['member_count_since_send'],
			),
			'unsubscribe_count_since_send' => array(
				'label' => __( 'Unsubscribes since last newsletter', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['unsubscribe_count_since_send'],
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
		$email  = $subscriber->get_email();
		$fields = apply_filters( // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
			'wc_mailchimp_subscribe_vars',
			array(
				'FNAME' => $subscriber->get_first_name(),
				'LNAME' => $subscriber->get_last_name(),
			)
		);

		$response = $this->api_request(
			'lists/' . $list . '/members/' . md5( $email ),
			array(
				'email_address' => $email,
				'status_if_new' => ( wc_string_to_bool( get_option( 'woocommerce_mailchimp_double_opt_in', 'yes' ) ) ? 'pending' : 'subscribed' ),
				'merge_fields'  => $fields,
			),
			'PUT'
		);

		return ( is_wp_error( $response ) ? $response : $subscriber );
	}

	/**
	 * Makes a request to the Mailchimp API.
	 *
	 * @since 3.0.0
	 *
	 * @param string $endpoint The request endpoint.
	 * @param array  $args     Optional. The request arguments. Default empty.
	 * @param string $method   Optional. The request method. Default 'GET'.
	 * @param string $version  Optional. The API version. Default '3.0'.
	 * @return mixed The request response. WP_Error on failure.
	 */
	protected function api_request( $endpoint, $args = array(), $method = 'GET', $version = '3.0' ) {
		$url = $this->get_api_url( $version, $endpoint );

		$args = array(
			'method'  => $method,
			'body'    => $args,
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'apikey:' . $this->get_api_key() ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'Content-Type'  => 'application/json',
			),
		);

		if ( has_filter( 'woocommerce_newsletter_mailchimp_api_request' ) ) {
			wc_deprecated_hook( 'woocommerce_newsletter_mailchimp_api_request', '3.0.0', 'wc_newsletter_subscription_mailchimp_api_request_args' );

			/**
			 * Filters the arguments of a Mailchimp API request.
			 *
			 * @since 2.3.11
			 * @deprecated 3.0.0 Use `wc_newsletter_subscription_mailchimp_api_request_args` instead.
			 *
			 * @param array  $args   The request arguments.
			 * @param string $method The request method.
			 */
			$args = apply_filters( 'woocommerce_newsletter_mailchimp_api_request', $args, $method );
		}

		return $this->trigger_request( $url, $args );
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
		if ( is_null( $this->api_url ) ) {
			$this->api_url = $this->generate_api_url( $this->get_api_key() );
		}

		return $this->api_url . wp_normalize_path( $version . '/' . untrailingslashit( $endpoint ) );
	}

	/**
	 * Generates the Mailchimp API URL from an API key.
	 *
	 * @since 3.0.0
	 *
	 * @param string $api_key The API key.
	 * @return string
	 */
	protected function generate_api_url( $api_key ) {
		$datacenter = 'us2';

		if ( is_string( $api_key ) && ! empty( $api_key ) ) {
			// Extract the datacenter from the API key.
			$parts = explode( '-', $api_key );

			if ( 2 === count( $parts ) && isset( $parts[1] ) ) {
				$datacenter = sanitize_text_field( $parts[1] );
			}
		}

		return "https://{$datacenter}.api.mailchimp.com/";
	}
}
