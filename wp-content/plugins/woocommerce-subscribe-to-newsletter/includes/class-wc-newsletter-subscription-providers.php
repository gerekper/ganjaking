<?php
/**
 * Newsletter Subscription Providers
 *
 * This class handles the registration and initialization of the different newsletter subscription providers.
 *
 * @package WC_Newsletter_Subscription
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Providers.
 */
class WC_Newsletter_Subscription_Providers {

	/**
	 * The registered providers.
	 *
	 * An array of pairs [provider_id => classname].
	 *
	 * @var array
	 */
	private static $providers = array();

	/**
	 * Registers the providers.
	 *
	 * @since 3.0.0
	 */
	public static function register_providers() {
		$providers = array(
			'activecampaign' => 'WC_Newsletter_Subscription_Provider_ActiveCampaign',
			'activetrail'    => 'WC_Newsletter_Subscription_Provider_ActiveTrail',
			'cmonitor'       => 'WC_Newsletter_Subscription_Provider_Campaign_Monitor',
			'mailchimp'      => 'WC_Newsletter_Subscription_Provider_Mailchimp',
			'mailerlite'     => 'WC_Newsletter_Subscription_Provider_Mailerlite',
			'mailpoet'       => 'WC_Newsletter_Subscription_Provider_Mailpoet',
			'mailpoet_3'     => 'WC_Newsletter_Subscription_Provider_Mailpoet_3',
			'sendgrid'       => 'WC_Newsletter_Subscription_Provider_Sendgrid',
			'sendinblue'     => 'WC_Newsletter_Subscription_Provider_Sendinblue',
		);

		/**
		 * Filters the newsletter subscription providers.
		 *
		 * @since 3.0.0
		 *
		 * @param array $providers Array of registered providers with pairs [provider_id => classname].
		 */
		self::$providers = apply_filters( 'wc_newsletter_subscription_providers', $providers );
	}

	/**
	 * Gets the registered providers.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public static function get_providers() {
		return self::$providers;
	}

	/**
	 * Gets a provider by ID.
	 *
	 * @since 3.0.0
	 *
	 * @param string $provider_id The provider ID.
	 * @param array  $credentials Optional The provider credentials. Default empty.
	 * @return WC_Newsletter_Subscription_Provider|null The provider instance, null on failure.
	 */
	public static function get_provider( $provider_id, $credentials = array() ) {
		if ( ! isset( self::$providers[ $provider_id ] ) ) {
			return null;
		}

		// Initialize the instance.
		$provider = new self::$providers[ $provider_id ]();

		// Set the credentials.
		if ( ! empty( $credentials ) && $provider instanceof WC_Newsletter_Subscription_Provider ) {
			$provider->set_credentials( $credentials );
		}

		return $provider;
	}
}
