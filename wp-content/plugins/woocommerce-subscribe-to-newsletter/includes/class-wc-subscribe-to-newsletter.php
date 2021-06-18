<?php
/**
 * Newsletter Subscription setup
 *
 * @package WC_Newsletter_Subscription
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Singleton pattern.
 */
if ( ! trait_exists( 'WC_Newsletter_Subscription_Singleton' ) ) {
	require_once dirname( WC_NEWSLETTER_SUBSCRIPTION_FILE ) . '/includes/traits/trait-wc-newsletter-subscription-singleton.php';
}

/**
 * WC_Subscribe_To_Newsletter class.
 */
final class WC_Subscribe_To_Newsletter {

	use WC_Newsletter_Subscription_Singleton;

	/**
	 * Newsletter provider.
	 *
	 * @var mixed
	 */
	protected $provider = null;

	/**
	 * Constructor.
	 *
	 * @since 2.3.5
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed $key Key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( 'service' === $key ) {
			wc_doing_it_wrong( 'WC_Subscribe_To_Newsletter->service', 'This property is no longer available. Use the method WC_Subscribe_To_Newsletter->provider() instead.', '2.8.0' );

			return $this->provider();
		}
	}

	/**
	 * Define constants.
	 *
	 * @since 2.5.0
	 */
	public function define_constants() {
		$this->define( 'WC_NEWSLETTER_SUBSCRIPTION_VERSION', '3.2.0' );
		$this->define( 'WC_NEWSLETTER_SUBSCRIPTION_PATH', plugin_dir_path( WC_NEWSLETTER_SUBSCRIPTION_FILE ) );
		$this->define( 'WC_NEWSLETTER_SUBSCRIPTION_URL', plugin_dir_url( WC_NEWSLETTER_SUBSCRIPTION_FILE ) );
		$this->define( 'WC_NEWSLETTER_SUBSCRIPTION_BASENAME', plugin_basename( WC_NEWSLETTER_SUBSCRIPTION_FILE ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 2.5.0
	 *
	 * @param string      $name  The constant name.
	 * @param string|bool $value The constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Includes the necessary files.
	 *
	 * @since 2.5.0
	 */
	public function includes() {
		/**
		 * Class autoloader.
		 */
		include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-autoloader.php';

		/**
		 * Core traits.
		 */
		include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/traits/trait-wc-newsletter-subscription-provider-api-key.php';
		include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/traits/trait-wc-newsletter-subscription-provider-stats.php';
		include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/traits/trait-wc-newsletter-subscription-provider-require-plugin.php';

		/**
		 * Abstract classes.
		 */
		include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/abstracts/abstract-wc-newsletter-subscription-provider.php';

		/**
		 * Core classes.
		 */
		include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/wc-newsletter-subscription-functions.php';
		include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-providers.php';
		include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-orders.php';

		if ( wc_newsletter_subscription_is_plugin_active( 'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php' ) ) {
			include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-points-rewards.php';
		}

		if ( wc_newsletter_subscription_is_request( 'admin' ) ) {
			include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/admin/class-wc-newsletter-subscription-admin.php';
		}

		if ( wc_newsletter_subscription_is_request( 'frontend' ) ) {
			include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-frontend-scripts.php';
			include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-checkout.php';
			include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-register.php';
		}
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 3.0.0
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'widgets_init', array( $this, 'init_widget' ) );
		add_action( 'woocommerce_loaded', array( $this, 'load_post_wc_class' ) );
	}

	/**
	 * Init plugin.
	 *
	 * @since 3.0.0
	 */
	public function init() {
		// Load text domain.
		load_plugin_textdomain( 'woocommerce-subscribe-to-newsletter', false, dirname( WC_NEWSLETTER_SUBSCRIPTION_BASENAME ) . '/languages' );

		// Register the providers.
		WC_Newsletter_Subscription_Providers::register_providers();
	}

	/**
	 * Registers custom widgets.
	 *
	 * @since 2.3.5
	 */
	public function init_widget() {
		if ( ! class_exists( 'WC_Widget', false ) ) {
			include_once WC_ABSPATH . '/includes/abstracts/abstract-wc-widget.php';
		}

		include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-widget-subscribe-to-newsletter.php';

		register_widget( 'WC_Widget_Subscribe_To_Newsletter' );
	}

	/**
	 * Loads any class that needs to check for WC loaded.
	 *
	 * @since 2.3.12
	 */
	public function load_post_wc_class() {
		if ( class_exists( 'WC_Abstract_Privacy' ) ) {
			include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-privacy.php';
		}
	}

	/**
	 * Gets an instance of the newsletter provider.
	 *
	 * @since 2.8.0
	 *
	 * @return WC_Newsletter_Subscription_Provider|null
	 */
	public function provider() {
		if ( is_null( $this->provider ) ) {
			$provider_id = get_option( 'woocommerce_newsletter_service' );
			$provider    = WC_Newsletter_Subscription_Providers::get_provider( $provider_id );

			if ( $provider instanceof WC_Newsletter_Subscription_Provider ) {
				// The provider uses an API key for authentication.
				if ( method_exists( $provider, 'get_api_key' ) ) {
					$api_key = get_option( 'woocommerce_' . $provider_id . '_api_key' );

					if ( $api_key ) {
						$provider->set_credentials(
							array(
								'api_key' => $api_key,
							)
						);
					}
				}

				$this->provider = $provider;
			}
		}

		return $this->provider;
	}

	/**
	 * Register Custom dashboard widgets.
	 *
	 * @deprecated 3.0.0
	 */
	public function init_dashboard() {
		wc_deprecated_function( __FUNCTION__, '3.0.0', 'WC_Newsletter_Subscription_Admin_Dashboard' );
	}
}
