<?php
/**
 * Abstract provider class
 *
 * @package WC_Newsletter_Subscription/Abstracts
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Newsletter_Subscription_Provider', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Provider.
 */
abstract class WC_Newsletter_Subscription_Provider {

	/**
	 * Provider ID.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Provider name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Provider privacy URL.
	 *
	 * @var string
	 */
	protected $privacy_url = '';

	/**
	 * Supported features such as 'tags'.
	 *
	 * @var array
	 */
	protected $supports = array();

	/**
	 * Provider credentials.
	 *
	 * @var array
	 */
	protected $credentials = array();

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credentials Optional. An array with the provider credentials.
	 */
	public function __construct( $credentials = array() ) {
		$this->set_credentials( $credentials );
	}

	/**
	 * Gets the provider ID.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Gets the provider name.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Gets the provider privacy URL.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_privacy_url() {
		return $this->privacy_url;
	}

	/**
	 * Checks if the provider supports a given feature.
	 *
	 * @since 3.0.0
	 *
	 * @param string $feature The feature to test support for.
	 * @return bool
	 */
	public function supports( $feature ) {
		return in_array( $feature, $this->supports, true );
	}

	/**
	 * Gets the provider credentials.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_credentials() {
		return $this->credentials;
	}

	/**
	 * Sets the provider credentials.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credentials An array with the provider credentials.
	 */
	public function set_credentials( $credentials ) {
		$this->credentials = (array) $credentials;
	}

	/**
	 * Validates the credentials.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credentials An array with the credentials to validate.
	 * @return bool
	 */
	abstract public function validate_credentials( $credentials );

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
		return array();
	}

	/**
	 * Gets if the provider is enabled.
	 *
	 * A provider is considered enabled if it can subscribe to customers in a list or update their data.
	 * Most providers require API credentials and others like MailPoet a specific plugin.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	abstract public function is_enabled();

	/**
	 * Gets the available lists to subscribe to the customers.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_lists() {
		$transient = $this->generate_transient_name( 'lists' );
		$lists     = get_transient( $transient );

		if ( ! $lists ) {
			$lists = $this->fetch_lists();

			if ( ! empty( $lists ) ) {
				set_transient( $transient, $lists, HOUR_IN_SECONDS );
			}
		}

		return $lists;
	}

	/**
	 * Clears cached lists.
	 *
	 * @since 3.1.0
	 */
	public function clear_lists() {
		$transient = $this->generate_transient_name( 'lists' );
		delete_transient( $transient );
	}

	/**
	 * Fetches the available lists to subscribe to the customers.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	abstract protected function fetch_lists();

	/**
	 * Subscribes a customer to the specified list.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed                                 $list       The list to subscribe to the customer.
	 * @param WC_Newsletter_Subscription_Subscriber $subscriber Subscriber object.
	 * @return WC_Newsletter_Subscription_Subscriber|WP_Error Subscriber object on success. WP_Error on failure.
	 */
	abstract public function subscribe( $list, $subscriber );

	/**
	 * Generates a transient name for a specific action and associated with the provider credentials.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action The action.
	 * @return string
	 */
	protected function generate_transient_name( $action ) {
		$transient_name = "wc_newsletter_subscription_{$this->id}_{$action}";

		$credentials = array_filter( $this->get_credentials() );

		// Use the provider credentials to generate a unique transient name.
		if ( ! empty( $credentials ) ) {
			// Be sure the credentials parameters are always encoded in the same order.
			array_multisort( $credentials );

			$transient_name .= '_' . md5( wp_json_encode( $credentials ) );
		}

		return $transient_name;
	}
}
