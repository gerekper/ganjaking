<?php
/**
 * Provider: MailPoet v3
 *
 * @package WC_Newsletter_Subscription/Providers
 * @since   3.0.0
 */

use MailPoet\API\API as Mailpoet_Api;
use MailPoet\API\MP\v1\APIException as Mailpoet_Api_Exception;

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Newsletter_Subscription_Provider_Mailpoet_3', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Provider_Mailpoet_3.
 */
class WC_Newsletter_Subscription_Provider_Mailpoet_3 extends WC_Newsletter_Subscription_Provider {

	use WC_Newsletter_Subscription_Provider_Require_Plugin;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->id          = 'mailpoet_3';
		$this->name        = 'MailPoet 3';
		$this->privacy_url = 'https://www.mailpoet.com/privacy-notice/';
		$this->plugin_name = 'MailPoet';
		$this->plugin_url  = 'https://wordpress.org/plugins/mailpoet/';
		$this->plugin_path = 'mailpoet/mailpoet.php';

		parent::__construct();
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
		$fields = array();

		if ( $connected ) {
			$fields = array(
				'woocommerce_mailpoet_3_list' => array(
					'type'        => 'provider_lists',
					'title'       => _x( 'MailPoet List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
					'desc_tip'    => _x( 'Choose a list customers can subscribe to (MailPoet WordPress plugin must be installed and configured first).', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
					'description' => _x( 'Choose a list customers can subscribe to. The <a href="https://www.mailpoet.com/" target="_blank">MailPoet</a> WordPress plugin must be installed and configured first.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
					'options'     => array( '' => __( 'Select a list...', 'woocommerce-subscribe-to-newsletter' ) ) + $this->get_lists(),
				),
			);
		}

		return $fields;
	}

	/**
	 * Gets if the provider is enabled.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return class_exists( 'MailPoet\API\API' );
	}

	/**
	 * Return Mailpoet Api
	 *
	 * @since 3.0.0
	 *
	 * @return MailPoet\API\MP\v1\API|false
	 */
	protected function get_api_instance() {
		try {
			$mailpoet_api = Mailpoet_Api::MP( 'v1' );
		} catch ( Exception $e ) {
			return false;
		}

		return $mailpoet_api;
	}

	/**
	 * Gets the available lists to subscribe to the customers.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_lists() {
		return $this->fetch_lists();
	}

	/**
	 * Fetches the available lists to subscribe to the customers.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function fetch_lists() {
		$lists = array();

		$mailpoet_api = $this->get_api_instance();

		if ( $mailpoet_api ) {
			$mailpoet_lists = $mailpoet_api->getLists();

			if ( ! empty( $mailpoet_lists ) && is_array( $mailpoet_lists ) ) {
				foreach ( $mailpoet_lists as $list ) {
					$lists[ $list['id'] ] = $list['name'];
				}
			}
		}

		return $lists;
	}

	/**
	 * Subscribes a customer to the specified list.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed                                 $list       The list to subscribe to the customer.
	 * @param WC_Newsletter_Subscription_Subscriber $subscriber Subscriber object.
	 * @return WC_Newsletter_Subscription_Subscriber|WP_Error True on success. WP_Error on failure.
	 */
	public function subscribe( $list, $subscriber ) {
		$subscriber_data = array(
			'email'      => $subscriber->get_email(),
			'first_name' => $subscriber->get_first_name(),
			'last_name'  => $subscriber->get_last_name(),
		);

		$mailpoet_api = $this->get_api_instance();

		if ( ! $mailpoet_api ) {
			return new WP_Error( 'mailpoet_3_invalid_subscription', 'MailPoet v3 invalid subscription.' );
		}

		try {
			$mailpoet_subscriber = $mailpoet_api->getSubscriber( $subscriber_data['email'] );
		} catch ( Exception $e ) {
			$mailpoet_subscriber = false;
		}

		try {
			if ( ! $mailpoet_subscriber ) {
				// Subscriber doesn't exist let's create one.
				$mailpoet_api->addSubscriber( $subscriber_data, array( $list ) );
			} else {
				// In case subscriber exists just add him to new lists.
				$mailpoet_api->subscribeToLists( $subscriber_data['email'], array( $list ) );
			}
		} catch ( Exception $e ) {
			if ( Mailpoet_Api_Exception::CONFIRMATION_FAILED_TO_SEND !== $e->getCode() || Mailpoet_Api_Exception::WELCOME_FAILED_TO_SEND !== $e->getCode() ) {
				return new WP_Error( 'mailpoet_3_invalid_subscription', 'MailPoet v3 invalid subscription.', $e->getMessage() );
			}
		}

		return $subscriber;
	}
}
