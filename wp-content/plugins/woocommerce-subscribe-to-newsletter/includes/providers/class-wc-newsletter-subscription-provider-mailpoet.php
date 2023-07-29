<?php
/**
 * Provider: MailPoet
 *
 * @package WC_Newsletter_Subscription/Providers
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Newsletter_Subscription_Provider_Mailpoet', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Provider_Mailpoet.
 */
class WC_Newsletter_Subscription_Provider_Mailpoet extends WC_Newsletter_Subscription_Provider {

	use WC_Newsletter_Subscription_Provider_Stats;
	use WC_Newsletter_Subscription_Provider_Require_Plugin;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->id          = 'mailpoet';
		$this->name        = 'MailPoet 2 (Deprecated)';
		$this->privacy_url = 'https://www.mailpoet.com/privacy-notice/';
		$this->plugin_name = 'MailPoet Newsletters';
		$this->plugin_url  = 'https://wordpress.org/plugins/wysija-newsletters/';
		$this->plugin_path = 'wysija-newsletters/index.php';
		$this->supports    = array(
			'stats',
		);

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
				'woocommerce_mailpoet_list' => array(
					'type'     => 'provider_lists',
					'title'    => _x( 'MailPoet List', 'setting title', 'woocommerce-subscribe-to-newsletter' ),
					'desc_tip' => _x( 'Choose a list customers can subscribe to.', 'setting desc', 'woocommerce-subscribe-to-newsletter' ),
					'options'  => array( '' => __( 'Select a list...', 'woocommerce-subscribe-to-newsletter' ) ) + $this->get_lists(),
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
		return class_exists( 'WYSIJA', false );
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
		$lists          = array();
		$model_list     = WYSIJA::get( 'list', 'model' );
		$mailpoet_lists = $model_list->get( array( 'name', 'list_id' ), array( 'is_enabled' => 1 ) );

		if ( ! empty( $mailpoet_lists ) && is_array( $mailpoet_lists ) ) {
			foreach ( $mailpoet_lists as $list ) {
				$lists[ $list['list_id'] ] = $list['name'];
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
		$stats = array(
			'subscribed'   => 0,
			'unconfirmed'  => 0,
			'unsubscribed' => 0,
		);

		$model_user = WYSIJA::get( 'user', 'model' );
		$select     = array( 'COUNT(*) AS users', 'status' );

		// Find total subscribers and subscribes.
		$count_by_status = $model_user->get_subscribers( $select, array( 'lists' => $list ), 'status' );

		foreach ( $count_by_status as $count ) {
			if ( '-1' === $count['status'] ) {
				$stats['unsubscribed'] = $count['users'];
				continue;
			} elseif ( '0' === $count['status'] ) {
				$stats['unconfirmed'] = $count['users'];
				continue;
			} elseif ( '1' === $count['status'] ) {
				$stats['subscribed'] = $count['users'];
				continue;
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
	protected function format_stats( $stats ) {
		$formatted_stats = array(
			'subscribed'   => array(
				'label' => __( 'Total subscribers', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['subscribed'],
			),
			'unconfirmed'  => array(
				'label' => __( 'Unconfirmed', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['unconfirmed'],
			),
			'unsubscribed' => array(
				'label' => __( 'Unsubscribes', 'woocommerce-subscribe-to-newsletter' ),
				'value' => $stats['unsubscribed'],
			),
		);

		$config_model = WYSIJA::get( 'config', 'model' );
		$double_optin = $config_model->getValue( 'confirm_dbleoptin' );

		if ( ! $double_optin ) {
			$formatted_stats['subscribed']['value'] = $stats['subscribed'] + $stats['unconfirmed'];
			unset( $formatted_stats['unconfirmed'] );
		}

		return $formatted_stats;
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
		$data = array(
			'user'      => array(
				'email'     => $subscriber->get_email(),
				'firstname' => $subscriber->get_first_name(),
				'lastname'  => $subscriber->get_last_name(),
			),
			'user_list' => array(
				'list_ids' => array( $list ),
			),
		);

		$user_helper    = WYSIJA::get( 'user', 'helper' );
		$add_subscriber = $user_helper->addSubscriber( $data );

		if ( false !== $add_subscriber ) {
			return $subscriber;
		}

		$messages = $user_helper->getMsgs();

		if ( is_array( $messages ) && is_array( $messages['error'] ) ) {
			$response = wp_json_encode( $messages['error'] );
		} else {
			$response = 'No error messages were returned by MailPoet.';
		}

		return wc_newsletter_subscription_log_error(
			new WP_Error( 'mailpoet_invalid_subscription', 'MailPoet invalid subscription.', $response )
		);
	}
}
