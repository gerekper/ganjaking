<?php
/**
 * WooCommerce Social Login
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * WooCommerce Social Login Main Plugin Class.
 *
 * @since 1.0.0
 */
class WC_Social_Login extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '2.12.0';

	/** @var WC_Social_Login single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'social_login';

	/** plugin meta prefix */
	const PLUGIN_PREFIX = 'wc_social_login_';

	/** @var \WC_Social_Login_Admin instance */
	protected $admin;

	/** @var \WC_Social_Login_Frontend instance */
	protected $frontend;

	/** @var \SkyVerge\WooCommerce\Social_Login\AJAX instance */
	private $ajax;

	/** @var WC_Social_Login_Integrations instance */
	private $integrations;

	/** @var \WC_Social_Login_HybridAuth */
	protected $hybridauth;

	/** @var array login providers */
	private $providers;


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'  => 'woocommerce-social-login',
				'dependencies' => array(
					'php_extensions' => array(
						'curl',
					),
				),
			)
		);

		// register widgets
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// erase personal information when a personal data erasure request is issued
		add_filter( 'woocommerce_privacy_erase_personal_data_customer', array( $this, 'erase_personal_data' ), 10, 2 );
	}


	/**
	 * Autoloads Provider classes.
	 *
	 * @since 1.0.2
	 *
	 * @param string $class class name to load
	 */
	public function autoload( $class ) {

		if ( 0 === stripos( $class, 'wc_social_login_provider_' ) ) {

			$class = strtolower( $class );

			// provider classes
			$path = $this->get_plugin_path() . '/src/providers/';
			$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

			if ( is_readable( $path . $file ) ) {

				require_once( $path . $file );
			}
		}
	}


	/**
	 * Builds the lifecycle handler instance.
	 *
	 * @since 2.6.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/class-wc-social-login-lifecycle.php' );

		$this->lifecycle_handler = new SkyVerge\WooCommerce\Social_Login\Lifecycle( $this );
	}


	/**
	 * Builds the Setup Wizard handler instance.
	 *
	 * @since 2.7.0
	 */
	protected function init_setup_wizard_handler() {

		parent::init_setup_wizard_handler();

		require_once( $this->get_plugin_path() . '/src/admin/Setup_Wizard.php' );

		$this->setup_wizard_handler = new SkyVerge\WooCommerce\Social_Login\Admin\Setup_Wizard( $this );
	}


	/**
	 * Builds the REST API handler instance.
	 *
	 * @since 2.6.0
	 */
	protected function init_rest_api_handler() {

		require_once( $this->get_plugin_path() . '/src/api/class-wc-social-login-rest-api.php' );

		$this->rest_api_handler = new \SkyVerge\WooCommerce\Social_Login\REST_API( $this );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 2.6.0
	 */
	public function init_plugin() {

		$this->includes();

		// set profile image avatar
		add_filter( 'get_avatar', array( $this, 'set_profile_image_avatar' ), 10, 2 );

		// adjust the avatar URL
		add_filter( 'wc_social_login_profile_image', array( $this, 'adjust_avatar_url' ), 0 );
	}


	/**
	 * Includes required files.
	 *
	 * @since 2.6.0
	 */
	private function includes() {

		require_once( $this->get_plugin_path() . '/src/class-wc-social-login-hybridauth.php' );

		$this->hybridauth = new \WC_Social_Login_HybridAuth( $this->get_auth_path() );

		// frontend includes
		if ( ! is_admin() ) {
			$this->frontend_includes();
		}

		// admin includes
		if ( is_admin() && ! wp_doing_ajax() ) {
			$this->admin_includes();
		}

		// ajax includes
		if ( wp_doing_ajax() ) {
			$this->ajax = $this->load_class( '/src/AJAX.php', '\\SkyVerge\\WooCommerce\\Social_Login\\AJAX' );
		}

		// integrations
		$this->integrations = $this->load_class( '/src/integrations/class-wc-social-login-integrations.php', 'WC_Social_Login_Integrations' );
	}


	/**
	 * Includes required frontend files.
	 *
	 * @since 1.0.0
	 */
	private function frontend_includes() {

		require_once( $this->get_plugin_path() . '/src/wc-social-login-template-functions.php' );

		$this->frontend = $this->load_class( '/src/frontend/class-wc-social-login-frontend.php', 'WC_Social_Login_Frontend' );
	}


	/**
	 * Includes required admin files.
	 *
	 * @since 1.0.0
	 */
	private function admin_includes() {

		$this->admin = $this->load_class( '/src/admin/class-wc-social-login-admin.php', 'WC_Social_Login_Admin' );
	}


	/**
	 * Returns the admin class instance.
	 *
	 * @since 1.8.0
	 *
	 * @return \WC_Social_Login_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Gets the AJAX handler instance.
	 *
	 * @since 2.7.0
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Returns the frontend class instance.
	 *
	 * @since 1.8.0
	 *
	 * @return \WC_Social_Login_Frontend
	 */
	public function get_frontend_instance() {

		return $this->frontend;
	}


	/**
	 * Return the Hybridauth class instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Social_Login_HybridAuth
	 */
	public function get_hybridauth_instance() {

		return $this->hybridauth;
	}


	/**
	 * Returns the integrations handler instance.
	 *
	 * @since 2.5.0
	 *
	 * @return \WC_Social_Login_Integrations
	 */
	public function get_integrations_instance() {

		return $this->integrations;
	}


	/**
	 * Sets the profile image avatar.
	 *
	 * Filters `get_avatar()` function and sets the img src to stored profile image.
	 * @see get_avatar()
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 *
	 * @param string $avatar image tag for the user's avatar.
	 * @param mixed $id_or_email a user ID, email address, or comment object.
	 * @return string avatar img src
	 */
	public function set_profile_image_avatar( $avatar, $id_or_email ) {

		if ( is_admin() && Framework\SV_WC_Helper::is_current_screen( 'options-discussion' ) ) {
			return $avatar;
		}

		$user_id = 0;

		if ( is_numeric( $id_or_email ) ) {

			$user_id = (int) $id_or_email;

		} elseif ( is_object( $id_or_email ) ) {

			if ( ! empty( $id_or_email->user_id ) ) {

				$user_id = (int) $id_or_email->user_id;
			}

		} else {

			$user = get_user_by( 'email', $id_or_email );

			if ( $user ) {

				$user_id = $user->ID;
			}
		}

		if ( $user_id && $image = get_user_meta( $user_id, '_wc_social_login_profile_image', true ) ) {

			/**
			 * Filters the profile image URL.
			 *
			 * @since 1.2.0
			 *
			 * @param string $image the profile image URL
			 */
			$image = (string) apply_filters( 'wc_social_login_profile_image', $image );

			if ( ! ( is_ssl() || 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) ) ) {

				$avatar = preg_replace( "/src='(.*?)'/i", "src='" . $image . "'", $avatar );
				$avatar = preg_replace( "/srcset='(.*?)'/i", "srcset='" . $image . " 2x'", $avatar );
			}
		}

		return $avatar;
	}


	/**
	 * Fixes URLs of the avatars provided by social networks.
	 *
	 * @since 1.6.0
	 *
	 * @param string $url URL received from the social profile
	 * @return string URL after our changes
	 */
	public function adjust_avatar_url( $url ) {

		// VK does not support SSL avatars. For others - we force https.
		if ( false === strpos( $url, '.vk.me' ) ) {

			$url = set_url_scheme( $url, 'https' );
		}

		return $url;
	}


	/** Provider methods ******************************************************/


	/**
	 * Loads all social login providers which are hooked in.
	 *
	 * Providers are sorted into their user-defined order after being loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function load_providers() {

		// autoload classes
		spl_autoload_register( array( $this, 'autoload' ) );

		// Base social login provider & profile
		require_once( $this->get_plugin_path() . '/src/abstract-wc-social-login-provider.php' );
		require_once( $this->get_plugin_path() . '/src/class-wc-social-login-provider-profile.php' );

		// Providers can register themselves through this hook
		do_action( 'wc_social_login_load_providers' );

		/**
		 * Filters the list of providers to load.
		 *
		 * @since 1.0.0
		 *
		 * @param array $provider_classes list of provider classes to load
		 */
		$providers_to_load = (array) apply_filters( 'wc_social_login_providers', [
			'facebook'  => 'WC_Social_Login_Provider_Facebook',
			'twitter'   => 'WC_Social_Login_Provider_Twitter',
			'google'    => 'WC_Social_Login_Provider_Google',
			'amazon'    => 'WC_Social_Login_Provider_Amazon',
			'linkedin'  => 'WC_Social_Login_Provider_LinkedIn',
			'paypal'    => 'WC_Social_Login_Provider_PayPal',
			'disqus'    => 'WC_Social_Login_Provider_Disqus',
			'vkontakte' => 'WC_Social_Login_Provider_VKontakte',
		] );

		foreach ( $providers_to_load as $provider ) {
			$this->register_provider( $provider );
		}

		$this->sort_providers();

		return $this->providers;
	}


	/**
	 * Registers a provider.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Social_Login_Provider|string $provider either the name of the provider's class, or an instance of the provider's class
	 */
	public function register_provider( $provider ) {

		if ( ! is_object( $provider ) ) {
			$provider = new $provider( $this->get_auth_path() );
		}

		$id = empty( $provider->instance_id ) ? $provider->get_id() : $provider->instance_id;

		$this->providers[ $id ] = $provider;
	}


	/**
	 * Sorts providers into the user defined order.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Social_Login_Provider[]
	 */
	public function sort_providers() {

		$sorted_providers = array();

		// get order option
		$ordering  = (array) get_option( 'wc_social_login_provider_order', array() );
		$order_end = 999;

		// load shipping providers in order
		foreach ( $this->providers as $provider ) {

			if ( isset( $ordering[ $provider->get_id() ] ) && is_numeric( $ordering[ $provider->get_id() ] ) ) {
				// add in position
				$sorted_providers[ $ordering[ $provider->get_id() ] ][] = $provider;
			} else {
				// add to end of the array
				$sorted_providers[ $order_end ][] = $provider;
			}
		}

		ksort( $sorted_providers );

		$this->providers = array();

		foreach ( $sorted_providers as $providers ) {

			foreach ( $providers as $provider ) {

				$id = empty( $provider->instance_id ) ? $provider->get_id() : $provider->instance_id;

				$this->providers[ $id ] = $provider;
			}
		}

		return $this->providers;
	}


	/**
	 * Returns the authentication base path, defaults to `auth`.
	 *
	 * e.g.: skyverge.com/wc-api/auth/facebook
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_auth_path() {

		/**
		 * Filters the authentication base path.
		 *
		 * @since 1.0.0
		 *
		 * @param string $auth_path the authentication base path
		 */
		return (string) apply_filters( 'wc_social_login_auth_path', 'auth' );
	}


	/**
	 * Returns all registered providers for usage.
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Social_Login_Provider[]
	 */
	public function get_providers() {

		if ( null === $this->providers ) {

			$this->load_providers();
		}

		return $this->providers;
	}


	/**
	 * Returns the requested provider, if found.
	 *
	 * @since 1.0.0
	 *
	 * @param string $provider_id
	 * @return \WC_Social_Login_Provider|null
	 */
	public function get_provider( $provider_id ) {

		$providers = $this->get_providers();

		return isset( $providers[ $provider_id ] ) ? $providers[ $provider_id ] : null;
	}


	/**
	 * Gets available providers.
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Social_Login_Provider[]
	 */
	public function get_available_providers() {

		$available_providers = array();

		foreach ( $this->get_providers() as $provider ) {

			if ( $provider->is_available() ) {

				$available_providers[ $provider->get_id() ] = $provider;
			}
		}

		/**
		 * Filters the available providers.
		 *
		 * @since 1.0.0
		 *
		 * @param array $available_providers the available providers
		 */
		return (array) apply_filters( 'wc_social_login_available_providers', $available_providers );
	}


	/** Admin methods ******************************************************/


	/**
	 * Renders a notice for the user to read the docs before configuring.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function add_delayed_admin_notices() {

		// show any dependency notices
		parent::add_delayed_admin_notices();

		// add notice to read the documentation
		if ( $this->is_plugin_settings() ) {

			$this->get_admin_notice_handler()->add_admin_notice(
				/* translators: Placeholders: %1$s - opening HTML <a> tag, %2$s - closing HTML </a> tag */
				sprintf( __( 'Thanks for installing Social Login! Before you get started, please take a moment to %1$sread through the documentation%2$s.', 'woocommerce-social-login' ), '<a href="' . $this->get_documentation_url() . '">', '</a>' ),
				'read-the-docs',
				[
					'always_show_on_settings' => false,
					'notice_class'            => 'updated',
				]
			);
		}

		// display a notice specifically for Facebook Redirect URIs
		$facebook = $this->get_provider( 'facebook' );

		if ( $facebook && $facebook->is_enabled() && ! $facebook->is_redirect_uri_configured() ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag, %3$s - <a> tag, %4$s - </a> tag */
				__( 'WooCommerce Social Login: Facebook now requires your site\'s full OAuth Redirect URI to be configured for your app. Please head over to the %1$sFacebook settings%2$s to get your Redirect URI, and read more about %3$sconfiguring your app &raquo;%4$s', 'woocommerce-social-login' ),
				'<a href="' . esc_url( add_query_arg( 'section', strtolower( get_class( $facebook ) ), $this->get_settings_url() ) ) . '">', '</a>',
				'<a href="' . esc_url( 'https://docs.woocommerce.com/document/woocommerce-social-login-create-social-apps/#section-1' ) . '">', '</a>'
			);

			$this->get_admin_notice_handler()->add_admin_notice( $message, $facebook->get_id() . '-redirect-uri-not-configured', [
				'notice_class' => 'notice-warning',
				'dismissible'  => false,
			] );
		}

		// if the shop was using Instagram, notify the administrator that the provider has been removed following deprecation of its API
		if ( 'yes' === get_option( 'wc_social_login_instagram_removed' ) ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - <strong> opening HTML tag, %2$s - </strong> closing HTML tag, %3$s - <a> opening HTML tag, %4$s - </a> closing HTML tag, , %5$s - <a> opening HTML tag, %6$s - </a> closing HTML tag */
				__( '%1$sHeads up!%2$s Instagram has %3$sdeprecated its support for user logins%4$s. Your customers cannot use Instagram to login to your site. Please consider %5$ssetting up another provider%6$s to let your customers login via social platforms.', 'woocommerce-social-login' ),
				'<strong>', '</strong>',
				'<a href="https://www.instagram.com/developer/" target="_blank">', '</a>',
				'<a href="https://docs.woocommerce.com/document/woocommerce-social-login/#section-7" target="_blank">', '</a>'
			);

			$this->get_admin_notice_handler()->add_admin_notice( $message, $facebook->get_id() . '-redirect-uri-not-configured', [
				'always_show_on_settings' => false,
				'notice_class'            => 'notice-warning',
			] );
		}
	}


	/**
	 * Checks if SSL is required for any providers and not available.
	 *
	 * If unavailable, adds a dismissible admin notice.
	 * The Notice will not be rendered to the admin user once dismissed, unless on the plugin settings page, if any.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	protected function add_ssl_admin_notices() {

		// shop supports SSL
		if ( wc_checkout_is_https() ) {
			return;
		}

		// get available providers
		foreach ( $this->get_providers() as $provider ) {

			// check if the provider requires SSL
			if ( $provider->is_enabled() && $provider->requires_ssl() ) {

				$message = sprintf(
					/* translators: Placeholder: %s - social provider name */
					esc_html_x( 'WooCommerce Social Login: %s requires SSL for authentication, please force WooCommerce over SSL.', 'Requires SSL', 'woocommerce-social-login' ),
					'<strong>' . $provider->get_title() . '</strong>'
				);

				$this->get_admin_notice_handler()->add_admin_notice( $message, $provider->get_id() . '-ssl-required' );
			}
		}
	}


	/**
	 * Renders admin notices.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		// add missing SSL support notices
		$this->add_ssl_admin_notices();

		// warn about iThemes Security 'Filter Long URL Strings' setting
		if ( class_exists( 'ITSEC_Tweaks' ) ) {

			$ithemes_security_settings = get_site_option( 'itsec_tweaks', array( 'long_url_strings' => false ) );

			if (    ! empty( $ithemes_security_settings['long_url_strings'] )
				&&   $this->is_plugin_settings() ) {

				$this->get_admin_notice_handler()->add_admin_notice(
					esc_html__( 'Oops, looks like iThemes Security is set to Filter Long URLs. This is likely to cause a conflict with Social Login -- please disable that setting for optimal functionality.', 'woocommerce-social-login' ),
					'ithemes_security_long_url_strings',
					array( 'always_show_on_settings' => false )
				);
			}
		}

		// warn about deprecated callback URLs
		if (     get_option( 'wc_social_login_upgraded_from_opauth' )
			&& 'legacy' === get_option( 'wc_social_login_callback_url_format' ) ) {

			$this->get_admin_notice_handler()->add_admin_notice(
			/* translators: %1$s, %3$s - opening <a> tag, %2$s, %4$s - closing </a> tag */
				sprintf( esc_html__( 'Please update callback URLs for each Social Login provider, then switch callback URLs to the Default format in the %1$sSocial Login settings%2$s. You can %3$slearn more from the plugin documentation%4$s.', 'woocommerce-social-login' ), '<a href="' . $this->get_settings_url() . '">', '</a>', '<a href="https://docs.woocommerce.com/document/woocommerce-social-login/#upgrading-to-v2">', '</a>' ),
				'update_callback_url_format',
				array( 'dismissible' => true, 'notice_class' => 'error', 'always_show_on_settings' => true )
			);
		}

		// warn about possible caching issue preventing customers from linking accounts and logging in
		if ( 1 === (int) get_transient( '_wc_social_login_hybridauth_caching_issue' ) ) {

			$this->get_admin_notice_handler()->add_admin_notice(
			/* translators: Placeholders: %1$s - WooCommerce Social Login plugin name, %2$s - opening <a> HTML link tag, %3$s - closing </a> HTML link tag, %4$s cookie name wrapped in <code> HTML tags */
				sprintf( __( 'If you are experiencing issues with customers linking a social account using %1$s, please %2$sfollow the guidelines here%3$s, as your host should fix your site\'s caching rules to exclude %4$s cookies from caching. If you\'re not seeing any issues with %1$s, please disregard this notice.', 'woocommerce-social-login' ),
					wc_social_login()->get_plugin_name(),
					'<a href="' . esc_url( wc_social_login()->get_documentation_url() . '#caching-issue' ) . '">',
					'</a>',
					'<code>wp_woocommerce_session*</code>'
				),
				'hybridauth-caching-issue',
				array(
					'notice_class'            => 'error',
					'dismissible'             => true,
					'always_show_on_settings' => true,
				)
			);
		}

		if ( 'yes' === get_option( 'wc_social_login_upgraded_linkedin' ) ) {

			$linkedin_settings_url = add_query_arg(
				[ 'section' => 'wc_social_login_provider_linkedin' ],
				$this->get_settings_url()
			);

			$this->get_admin_notice_handler()->add_admin_notice(
				sprintf(
					/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s closing </a> HTML link tag, %3$s - opening <a> HTML link tag, %4$s closing </a> HTML link tag */
					__( 'Heads up! LinkedIn is deprecating v1 of their API. If you run into provider authentication issues, try %1$sswitching your API version to v2%2$s, or %3$screate a new LinkedIn app%4$s and update your settings.', 'woocommerce-social-login' ),
					'<a href="' . esc_url( $linkedin_settings_url ) . '">', '</a>',
					'<a href="https://docs.woocommerce.com/document/woocommerce-social-login-create-social-apps/#linkedin">', '</a>'
				),
				'wc-social-login-linkedin-provider-api-v2-update',
				[
					'notice_class'            => 'updated',
					'always_show_on_settings' => false,
				]
			);
		}
	}


	/**
	 * Register social login widgets
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function register_widgets() {

		// load widget
		require_once( $this->get_plugin_path() . '/src/widgets/class-wc-social-login-widget.php' );

		// register widget
		register_widget( 'WC_Social_Login_Widget' );
	}


	/** Privacy methods ******************************************************/


	/**
	 * Erases a customer's Social Login data when an erasure request is issued.
	 *
	 * Handles GDPR compliance for right to be forgotten.
	 *
	 * @internal
	 *
	 * @since 2.5.1
	 *
	 * @param array $response associative array containing the erasure response
	 * @param \WC_Customer $customer the customer being processed for personal data erasure
	 * @return array GDPR response
	 */
	public function erase_personal_data( $response, $customer ) {

		$response = wp_parse_args( (array) $response, array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		) );

		if ( $customer instanceof \WC_Customer ) {

			$meta_data = get_user_meta( $customer->get_id() );
			$erased    = false;

			if ( ! empty( $meta_data ) ) {

				foreach ( array_keys( $meta_data ) as $meta_key ) {

					if ( Framework\SV_WC_Helper::str_starts_with( $meta_key, '_wc_social_login' ) ) {

						$deleted = delete_user_meta( $customer->get_id(), $meta_key );

						if ( $deleted ) {
							$erased = true;
						}
					}
				}
			}

			if ( $erased ) {
				$response['messages'][]    = __( 'Removed customer Social Login data', 'woocommerce-social-login' );
				$response['items_removed'] = true;
			}
		}

		return $response;
	}


	/** Helper methods ********************************************************/


	/**
	 * Returns the main Social Login instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @see wc_social_login()
	 *
	 * @since 1.4.0
	 *
	 * @return \WC_Social_Login
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.0.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Social Login', 'woocommerce-social-login' );
	}


	/**
	 * Returns the full path and filename of the plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Returns the URL to the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-settings&tab=social_login' );
	}


	/**
	 * Returns the plugin sales page URL.
	 *
	 * @since 2.6.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/woocommerce-social-login/';
	}


	/**
	 * Returns the plugin documentation URL
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-social-login/';
	}


	/**
	 * Returns the plugin support URL.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Returns true if on the Social Login settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'], $_GET['tab'] ) && 'wc-settings' === $_GET['page'] && 'social_login' === $_GET['tab'];
	}


	/**
	 * Returns the user's social login profiles.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id optional (default: current user id)
	 * @return \WC_Social_Login_Provider_Profile[] array of found profiles or empty array if none are found
	 */
	public function get_user_social_login_profiles( $user_id = null ) {

		$linked_social_login_profiles = array();

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		// bail out if user is not logged in
		if ( (int) $user_id > 0 ) {

			foreach ( $this->get_available_providers() as $provider ) {

				$social_profile = get_user_meta( $user_id, '_wc_social_login_' . $provider->get_id() . '_profile', true );

				if ( $social_profile ) {

					$linked_social_login_profiles[ $provider->id ] = new \WC_Social_Login_Provider_Profile( $provider->get_id(), $social_profile );
				}
			}
		}

		return $linked_social_login_profiles;
	}


	/**
	 * Returns CSS for styling button colors.
	 *
	 * @since 1.1.0
	 *
	 * @return string CSS
	 */
	public function get_button_colors_css() {

		ob_start();

		foreach ( $this->get_available_providers() as $provider ) {
			?>
			a.button-social-login.button-social-login-<?php echo esc_attr( $provider->get_id() ); ?>,
			.widget-area a.button-social-login.button-social-login-<?php echo esc_attr( $provider->get_id() ); ?>,
			.social-badge.social-badge-<?php echo esc_attr( $provider->get_id() ); ?> {
			background: <?php echo esc_attr( $provider->get_color() ) ?>;
			border-color: <?php echo esc_attr( $provider->get_color() ) ?>;
			}
			<?php
		}

		return preg_replace( '/\s+/', ' ', ob_get_clean() );
	}


}


/**
 * Returns the One True Instance of Social Login.
 *
 * @since 1.4.0
 *
 * @return \WC_Social_Login
 */
function wc_social_login() {

	return \WC_Social_Login::instance();
}
