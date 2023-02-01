<?php
/**
 * Provider: Sendinblue
 *
 * @package WC_Newsletter_Subscription/Providers
 * @since   3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Newsletter_Subscription_Provider_Sendinblue', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Provider_Sendinblue.
 */
class WC_Newsletter_Subscription_Provider_Sendinblue extends WC_Newsletter_Subscription_Provider {

	use WC_Newsletter_Subscription_Provider_API_Key;
	use WC_Newsletter_Subscription_Provider_Stats;

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 *
	 * @param array $credentials Optional. An array with the provider credentials.
	 */
	public function __construct( $credentials = array() ) {
		$this->id          = 'sendinblue';
		$this->name        = 'Sendinblue';
		$this->privacy_url = 'https://sendinblue.com/legal/privacypolicy/';
		$this->supports    = array(
			'stats',
			'tags',
		);

		parent::__construct( $credentials );
	}

	/**
	 * Validates the credentials.
	 *
	 * @since 3.4.0
	 *
	 * @param array $credentials An array with the credentials to validate.
	 * @return bool
	 */
	public function validate_credentials( $credentials ) {
		$saved_credentials = $this->get_credentials();

		$this->set_credentials( $credentials );

		$response = $this->api_request( 'account' );

		// Restore the credentials.
		$this->set_credentials( $saved_credentials );

		return ( ! is_wp_error( $response ) && isset( $response['plan'] ) );
	}

	/**
	 * Gets the form fields to display on the settings page.
	 *
	 * Depending on if the provider is connected or not, the form fields may vary.
	 *
	 * @since 3.4.0
	 *
	 * @param bool $connected Is the provider connected?.
	 * @return array
	 */
	public function get_form_fields( $connected ) {
		$fields = array(
			'woocommerce_sendinblue_api_key' => array(
				'type'        => 'text',
				'title'       => _x( 'Sendinblue API Key', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
				'description' => sprintf(
					/* translators: %s: Sendinblue URL for getting the API key */
					_x( 'You can obtain your API key by logging in to your <a href="%s" target="_blank">Sendinblue account</a>.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
					esc_url( 'https://account.sendinblue.com/advanced/api' )
				),
				'desc_tip'    => _x( 'Enter your Sendinblue API key', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
				'disabled'    => $connected,
			),
		);

		if ( $connected ) {
			$attr_choices = array( '' => __( 'Select an attribute...', 'woocommerce-subscribe-to-newsletter' ) ) + $this->fetch_contacts_attributes();

			$fields = array_merge(
				$fields,
				array(
					'woocommerce_sendinblue_list' => array(
						'type'     => 'provider_lists',
						'title'    => _x( 'Sendinblue List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip' => _x( 'Choose a list customers can subscribe to.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'options'  => array( '' => __( 'Select a list...', 'woocommerce-subscribe-to-newsletter' ) ) + $this->get_lists(),
					),
					'woocommerce_sendinblue_firstname_attribute' => array(
						'type'        => 'select',
						'title'       => _x( 'First name attribute', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip'    => _x( "Select the attribute where to store the customer's first name.", 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'description' => _x( 'Leave the field empty to not include this customer info.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'options'     => $attr_choices,
					),
					'woocommerce_sendinblue_lastname_attribute' => array(
						'type'        => 'select',
						'title'       => _x( 'Last name attribute', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip'    => _x( "Select the attribute where to store the customer's last name.", 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'description' => _x( 'Leave the field empty to not include this customer info.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'options'     => $attr_choices,
					),
					'woocommerce_sendinblue_tags_attribute' => array(
						'type'     => 'select',
						'title'    => _x( 'TAGS attribute', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
						'desc_tip' => _x( "Select the attribute where to store the product's tags.", 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
						'options'  => $attr_choices,
					),
				)
			);
		}

		return $fields;
	}

	/**
	 * Fetches the available lists to subscribe to the customers.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function fetch_lists() {
		$lists    = array();
		$response = $this->api_request( 'contacts/lists' );

		if ( ! is_wp_error( $response ) && isset( $response['lists'] ) ) {
			foreach ( $response['lists'] as $list ) {
				$lists[ $list['id'] ] = $list['name'];
			}
		}

		return $lists;
	}

	/**
	 * Fetches the available contacts attributes..
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function fetch_contacts_attributes() {
		$attributes = array();
		$response   = $this->api_request( 'contacts/attributes' );

		if ( ! is_wp_error( $response ) && isset( $response['attributes'] ) ) {
			foreach ( $response['attributes'] as $attribute ) {
				if ( isset( $attribute['type'] ) && 'text' === $attribute['type'] ) {
					$attributes[ $attribute['name'] ] = $attribute['name'];
				}
			}
		}

		return $attributes;
	}

	/**
	 * Fetches the stats for the specified list.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $list The list to fetch the stats.
	 * @return array
	 */
	protected function fetch_stats( $list ) {
		$stats = array();

		$response = $this->api_request( 'contacts/lists/' . $list );

		if ( ! is_wp_error( $response ) && isset( $response['id'] ) ) {
			$stats = $response;
		}

		return $stats;
	}

	/**
	 * Formats the stats.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $stats The stats to format.
	 * @return array
	 */
	protected function format_stats( $stats ) {
		return array(
			'total_subscribers'   => array(
				'label' => __( 'Total subscribers', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['totalSubscribers'],
			),
			'unique_subscribers'  => array(
				'label' => __( 'Unique subscribers', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['uniqueSubscribers'],
			),
			'blocked_subscribers' => array(
				'label' => __( 'Blocked subscribers', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['totalBlacklisted'],
			),
		);
	}

	/**
	 * Subscribes a customer to the specified list.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed                                 $list       The list to subscribe to the customer.
	 * @param WC_Newsletter_Subscription_Subscriber $subscriber Subscriber object.
	 * @return WC_Newsletter_Subscription_Subscriber|WP_Error Subscriber object on success. WP_Error on failure.
	 */
	public function subscribe( $list, $subscriber ) {
		$args = array(
			'email'         => $subscriber->get_email(),
			'listIds'       => array(
				(int) $list,
			),
			'updateEnabled' => true,
		);

		$first_name_attr = get_option( 'woocommerce_sendinblue_firstname_attribute' );
		$last_name_attr  = get_option( 'woocommerce_sendinblue_lastname_attribute' );

		if ( $first_name_attr ) {
			$args['attributes'][ $first_name_attr ] = $subscriber->get_first_name();
		}

		if ( $last_name_attr ) {
			$args['attributes'][ $last_name_attr ] = $subscriber->get_last_name();
		}

		$response = $this->api_request( 'contacts', $args, 'POST' );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = $this->update_tags( $subscriber );

		return ( is_wp_error( $response ) ? $response : $subscriber );
	}

	/**
	 * Gets the subscriber's tags.
	 *
	 * @since 3.6.0
	 *
	 * @param WC_Newsletter_Subscription_Subscriber $subscriber Subscriber object.
	 * @return array
	 */
	protected function get_tags( $subscriber ) {
		$tags_attr = get_option( 'woocommerce_sendinblue_tags_attribute' );

		if ( ! $tags_attr ) {
			return array();
		}

		$response = $this->api_request( 'contacts/' . rawurlencode( $subscriber->get_email() ) );

		return ( isset( $response['attributes'][ $tags_attr ] ) ) ? explode( ',', $response['attributes'][ $tags_attr ] ) : array();
	}

	/**
	 * Adds tags to the subscriber.
	 *
	 * @since 3.6.0
	 *
	 * @param WC_Newsletter_Subscription_Subscriber $subscriber Subscriber object.
	 * @return mixed
	 */
	protected function update_tags( $subscriber ) {
		$tags      = $subscriber->get_tags();
		$tags_attr = get_option( 'woocommerce_sendinblue_tags_attribute' );

		if ( empty( $tags ) || ! $tags_attr ) {
			return $subscriber;
		}

		$previous_tags = $this->get_tags( $subscriber );
		$tags          = array_unique( array_merge( $tags, $previous_tags ) );

		$response = $this->api_request(
			'contacts/' . rawurlencode( $subscriber->get_email() ),
			array(
				'attributes' => array(
					$tags_attr => implode( ',', $tags ),
				),
			),
			'PUT'
		);

		return ( is_wp_error( $response ) ? $response : $subscriber );
	}

	/**
	 * Makes a request to the Sendinblue API.
	 *
	 * @since 3.4.0
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
					'api-key'      => $this->get_api_key(),
					'Accept'       => 'application/json',
					'Content-Type' => 'application/json',
				),
			)
		);
	}

	/**
	 * Gets the API URL.
	 *
	 * @since 3.4.0
	 *
	 * @param string $version  The API version.
	 * @param string $endpoint The API endpoint.
	 * @return string
	 */
	protected function get_api_url( $version, $endpoint ) {
		return 'https://api.sendinblue.com/v' . wp_normalize_path( $version . '/' . untrailingslashit( $endpoint ) );
	}
}
