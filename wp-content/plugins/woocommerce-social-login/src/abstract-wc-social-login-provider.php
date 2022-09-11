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
 * Abstract social login provider class.
 *
 * @since 1.0.0
 */
abstract class WC_Social_Login_Provider extends \WC_Settings_API {


	/** @var string Provider title. Shown in admin */
	protected $title = '';

	/** @var string Provider description. Shown in admin */
	protected $description = '';

	/** @var string 'yes' if the provider is enabled. */
	public $enabled = '';

	/** @var string Login button text. */
	protected $button_text = '';

	/** @var string Opauth-specific internal callback path, provided for backwards compatibility */
	protected $internal_callback = 'int_callback';

	/** @var boolean true if this provider requires SSL for authentication, false otherwise */
	protected $require_ssl = false;

	/** @var string Provider color. Used in admin reports. */
	protected $color = '';

	/** @var string URL used by this provider (used to check if reachable) */
	protected $ping_url = '';


	/**
	 * Provider constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $base_auth_path Base authentication path.
	 */
	public function __construct( $base_auth_path ) {

		// Set Plugin ID used for option names.
		$this->plugin_id = 'wc_social_login_';

		// Define and load provider settings.
		$this->init_form_fields();
		$this->init_settings();

		// WC 2.6+ has removed the default enabled member so we must set it.
		$this->enabled = $this->get_option( 'enabled' );

		// Add admin actions.
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_social_login_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		// Handle auth endpoints, supporting both ugly & pretty permalinks, ie both of the following work:
		// * example.com/?wc-api=auth&start=facebook
		// * example.com/wc-api/auth/facebook
		add_action( 'woocommerce_api_' . $base_auth_path, array( $this, 'process_endpoints' ) );
		add_action( 'woocommerce_api_' . $base_auth_path . '/' . $this->id , array( $this, 'authenticate' ) );
		add_action( 'woocommerce_api_' . $base_auth_path . '/callback/' . $this->id , array( $this, 'process_callback' ) );
		add_action( 'woocommerce_api_' . $base_auth_path . '/unlink/'   . $this->id , array( $this, 'unlink_account' ) );

		// TODO: remove the following endpoint handler when removing backwards compatibility with OpAuth-style callbacks {IT 2016-09-15}
		// Instantiate Hybridauth to process the redirect back from the provider using the old, opauth-style callback endpoints.
		add_action( 'woocommerce_api_' . $base_auth_path . '/' . $this->id . '/' . $this->internal_callback, array( $this, 'process_callback' ) );
	}


	/**
	 * Render provider settings and description
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {

		?><h3><?php echo esc_html( $this->get_title() ); ?></h3><?php

		echo wpautop( $this->get_description() );

		?>
		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table>
		<?php
	}


	/**
	 * Gets the oAuth app client ID field label.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	public function get_client_id_field_label() {

		return _x( 'Client ID', 'Social Login provider app identifier', 'woocommerce-social-login' );
	}


	/**
	 * Gets the oAuth app client secret field label.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	public function get_client_secret_field_label() {

		return __( 'Client Secret', 'woocommerce-social-login' );
	}


	/**
	 * Defines default provider settings fields.
	 *
	 * @since 1.0.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(

			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-social-login' ),
				'type'    => 'checkbox',
				/* translators: Placeholders: %s - social login provider name/title, eg: Facebook, Amazon, etc. */
				'label'   => sprintf( __( 'Enable %s', 'woocommerce-social-login' ), $this->get_title() ),
				'default' => 'no',
			),

			'id' => array(
				/* translators: Client (app) ID for a Social Login provider, used for identifying and authenticating the app (in our case, the WooCommerce store). This is NOT a WooCommerce customer ID */
				'title'       => $this->get_client_id_field_label(),
				'type'        => 'text',
				'description' => __( 'Your app ID', 'woocommerce-social-login' ),
				'desc_tip'    => true,
				'default'     => '',
			),

			'secret' => array(
				/* translators: Client (app) secret for a Social Login provider, used for identifying and authenticating the app (in our case, the WooCommerce store). */
				'title'       => $this->get_client_secret_field_label(),
				'type'        => 'password',
				'description' => __( 'Your app secret', 'woocommerce-social-login' ),
				'desc_tip'    => true,
				'default'     => '',
			),

			'login_button_text' => array(
				'title'       => __( 'Login Button Text', 'woocommerce-social-login' ),
				'type'        => 'text',
				'description' => __( 'Controls the text displayed on the login button.', 'woocommerce-social-login' ),
				'desc_tip'    => true,
				'default'     => $this->get_default_login_button_text(),
			),

			'link_button_text' => array(
				'title'       => _x( 'Link Button Text', 'noun', 'woocommerce-social-login' ),
				'type'        => 'text',
				'description' => __( 'Controls the text displayed on the link account button.', 'woocommerce-social-login' ),
				'desc_tip'    => true,
				'default'     => $this->get_default_link_button_text(),
			),

		);

		/**
		 * Filters default provider settings form fields.
		 *
		 * @since 1.0.0
		 * @param array $form_fields array of setting fields
		 * @param string $provider_id the provider identifier
		 */
		$this->form_fields = apply_filters( 'wc_social_login_provider_default_form_fields', $this->form_fields, $this->get_id() );
	}


	/**
	 * Check if the provider is available for use
	 *
	 * A provider is available when it's enabled and configured
	 *
	 * @since 1.0.0
	 * @return bool true if the provider is available, false otherwise
	 */
	public function is_available() {

		$is_available = ( $this->is_enabled() && $this->is_configured() );

		/**
		 * Filter whether the provider is available or not.
		 *
		 * @since 1.0.0
		 * @param bool $enabled True if enabled, false otherwise
		 * @param WC_Social_Login_Provider $provider Social Login provider
		 */
		return apply_filters( 'wc_social_login_provider_available', $is_available, $this );
	}


	/**
	 * Checks if a provider is enabled
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_enabled() {

		return 'yes' === $this->enabled;
	}


	/**
	 * Checks if a provider is configured
	 *
	 * By default, id and secret are the only required fields
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_configured() {

		return $this->get_option( 'id' ) && $this->get_option( 'secret' );
	}


	/**
	 * Returns true if this provider requires SSL to function properly
	 *
	 * @since 1.0.0
	 * @return boolean true if this provider requires ssl
	 */
	public function requires_ssl() {

		return $this->require_ssl;
	}


	/**
	 * Process authentication endpoints
	 *
	 * @since 2.0.0
	 */
	public function process_endpoints() {

		if ( ! empty( $_GET['start'] ) && $this->id === $_GET['start'] ) {

			// authenticate user using HA and register/login the user
			$this->authenticate();

		} elseif ( ! empty( $_GET['done'] ) && $this->id === $_GET['done'] ) {

			// process the callback/return from provider
			$this->process_callback();

		} elseif ( ! empty( $_GET['unlink'] ) && $this->id === $_GET['unlink'] ) {

			// unlink provider from user account
			$this->unlink_account();

		} elseif ( ! empty( $_GET['hauth_start'] ) || ! empty( $_GET['hauth_done'] ) ) {

			// let HybridAuth work it's magic
			wc_social_login()->get_hybridauth_instance()->process_endpoint();
		}

	}


	/**
	 * Handle authentication using HybridAuth
	 *
	 * @since 2.0.0
	 */
	public function authenticate() {

		$return = isset( $_GET['return'] ) ? $_GET['return'] : null;

		// Store return URL in WC session, as HybridAuth does not
		// provide a way to pass around custom query vars
		if ( $return ) {

			$return = wp_sanitize_redirect( $return );
			$return = wp_validate_redirect( $return, admin_url() );

			set_transient( 'wcsl_' . md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ), $return, 5 * MINUTE_IN_SECONDS );
		}

		wc_social_login()->get_hybridauth_instance()->authenticate( $this->id, $return );
	}


	/**
	 * Handle custom HA `login_done` endpoint by setting the `hauth_done`
	 * request param manually.
	 *
	 * @since 2.0.0
	 */
	public function process_callback() {

		$_REQUEST['hauth_done'] = $this->id;

		wc_social_login()->get_hybridauth_instance()->process_endpoint();
	}


	/**
	 * Remove/unlink the social login profile from the currently logged in user
	 *
	 * In 2.0.0 moved the unlink logic to \WC_Social_Login_HybridAuth::unlink_provider()
	 *
	 * @since 1.0.0
	 */
	public function unlink_account() {

		// security check
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'unlink' ) ) {
			wp_die( esc_html__( 'Oops, you took too long, please try again.', 'woocommerce-social-login' ), 'Error' );
		}

		if ( ! $user_id = get_current_user_id() ) {
			return;
		}

		wc_social_login()->get_hybridauth_instance()->unlink_provider( $user_id, $this->get_id() );

		wc_add_notice( $this->get_notice_text( 'account_unlinked' ), 'notice' );

		$return_url = isset( $_GET['return'] ) ? esc_url( urldecode( $_GET['return'] ) ) : get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

		wp_safe_redirect( $return_url );
		exit;
	}


	/** Getters ******************************************************/


	/**
	 * Get the provider ID, e.g. `facebook`
	 *
	 * @since 1.0.0
	 * @return string provider ID
	 */
	public function get_id() {

		return $this->id;
	}


	/**
	 * Get the provider title, e.g. 'Facebook'
	 *
	 * @since 1.0.0
	 * @return string provider title
	 */
	public function get_title() {

		/**
		 * Filter social login provider's title.
		 *
		 * @since 1.0.0
		 * @param string $title
		 * @param string $provider_id Social Login provider ID
		 */
		return apply_filters( 'wc_social_login_provider_title', $this->title, $this->get_id() );
	}


	/**
	 * Get the provider's app client ID
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_client_id() {

		/**
		 * Filter the provider's app client ID.
		 *
		 * @since 1.0.0
		 * @param string $client_id
		 * @param string $provider_id Social Login provider ID
		 */
		return apply_filters( 'wc_social_login_provider_client_id', $this->get_option( 'id' ), $this->get_id() );
	}


	/**
	 * Get the provider's app client secret
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_client_secret() {

		/**
		 * Filter the provider's app client secret.
		 *
		 * @since 1.0.0
		 * @param string $client_secret
		 * @param string $provider_id Social Login provider ID
		 */
		return apply_filters( 'wc_social_login_provider_client_secret', $this->get_option( 'secret' ), $this->get_id() );
	}


	/**
	 * Get the login button text for the provider, e.g. 'Login with Facebook'
	 *
	 * This is admin-configurable
	 *
	 * @since 1.0.0
	 * @return string login button text
	 */
	public function get_login_button_text() {

		/**
		 * Filter social login provider's login button text.
		 *
		 * @since 1.0.0
		 * @param string $button_text
		 * @param string $provider_id Social Login provider ID
		 */
		return apply_filters( 'wc_social_login_provider_login_button_text', $this->get_option( 'login_button_text' ), $this->get_id() );
	}


	/**
	 * Return the default login button text. This is implemented by provider
	 * classes to ease translation as the text may vary depending on the
	 * context the provider name is used in.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	abstract public function get_default_login_button_text();


	/**
	 * Get the link account button text for the provider, e.g. 'Link your account with Facebook'
	 *
	 * This is admin-configurable
	 *
	 * @since 1.0.0
	 * @return string link button text
	 */
	public function get_link_button_text() {

		/**
		 * Filter social login provider's link button text.
		 *
		 * @since 1.0.0
		 * @param string $button_text
		 * @param string $provider_id Social Login provider ID
		 */
		return apply_filters( 'wc_social_login_provider_link_button_text', $this->get_option( 'link_button_text' ), $this->get_id() );
	}


	/**
	 * Return the default link button text. This is implemented by provider
	 * classes to ease translation as the text may vary depending on the
	 * context the provider name is used in.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	abstract public function get_default_link_button_text();


	/**
	 * Get notices to display when a user performs an action with the current provider.
	 *
	 * @since 2.0.4
	 * @return array Associative array of notices IDs and notices text to display for each key.
	 */
	abstract public function get_notices();


	/**
	 * Get the notice text shown to the user given the specified action
	 *
	 * + `account_linked` - social account successfully linked
	 * + `account_unlinked` - social account removed
	 * + `account_already_linked` - social account already linked to existing WP account
	 * + `account_already_exists` - WP account using the email provided by the provider already exists
	 *
	 * Note that notices are defined per-provider so they can be translated properly,
	 * see https://github.com/skyverge/wc-plugins/commit/59b16ecce9aa20ffa8fe3d0228b3d1640312d8ce
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param string $action
	 * @return string notice text
	 */
	public function get_notice_text( $action ) {

		$notices = wc_social_login()->get_frontend_instance()->get_account_notices( $this->id );

		return isset( $notices[ $action ] ) ? $notices[ $action ] : '';
	}


	/**
	 * Get the provider's color
	 *
	 * @since 1.0.0
	 * @return string strategy class
	 */
	public function get_color() {

		/**
		 * Filter social login provider's color.
		 *
		 * @since 1.0.0
		 * @param string $color
		 * @param string $provider_id Social Login provider ID
		 */
		return apply_filters( 'wc_social_login_provider_color',  $this->color, $this->get_id() );
	}


	/**
	 * Get the provider's description
	 *
	 * Individual providers may override this to provide specific instructions,
	 * like displaying a callback URL
	 *
	 * @since 1.0.0
	 * @return string strategy class
	 */
	public function get_description() {

		return $this->description;
	}


	/**
	 * Get the Opauth-style internal callback, e.g. `int_callback`
	 *
	 * TODO: remove this once backwards compatibility with OA-style callbacks
	 * is removed {IT 2016-09-29}
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_internal_callback() {

		return $this->internal_callback;
	}


	/**
	 * Get the auth URL for logging in with the provider
	 *
	 * Note this forces plain HTTP for the redirect to avoid redirect issues
	 * with SSL, where WC tries to break out of SSL on non-checkout pages
	 *
	 * @since 1.0.0
	 * @param string $action auth action, either `login` (default) to link account or `unlink` to unlink
	 * @param string $return_url URL to return the user to after authenticating
	 * @return string url
	 */
	public function get_auth_url( $return_url, $action = 'login' ) {

		$action = 'unlink' === $action ? 'unlink' : 'start';

		$params = array();
		$params['wc-api']  = urlencode( wc_social_login()->get_auth_path() );
		$params[ $action ] = esc_attr( $this->get_id() );
		$params['return']  = urlencode( $return_url );

		// returns a url like https://www.skyverge.com/?wc-api=auth&{action}=amazon&return={return_url}
		return add_query_arg( $params, home_url( '/' ) );
	}


	/**
	 * Gets the callback URL for the provider.
	 *
	 * For providers that require an explicitly declared callback URL,
	 * use this method to display it in provider settings
	 *
	 * In 2.0.0 added the $format and $encode params
	 *
	 * @since 1.0.0
	 * @param string $format Optional
	 * @return string url
	 */
	public function get_callback_url( $format = null ) {

		$auth_path   = wc_social_login()->get_auth_path();
		$provider_id = esc_attr( $this->get_id() );

		/**
		 * Filters whether SSL should be forced for this provider's callback.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $force_ssl whether SSL should be forced for this provider's callback (default false)
		 * @param \WC_Social_Login_Provider $provider provider instance
		 */
		$force_ssl = $this->requires_ssl() || is_ssl() || 'yes' === get_option( 'wc_social_login_force_ssl_callback_url', 'no' ) || (bool) apply_filters( 'wc_social_login_force_ssl_callback', false, $this );

		// TODO: remove legacy callback url format support when removing backwards compatibility
		// with OpAuth-style callbacks {IT 2016-10-12}

		$format = $this->get_callback_url_format( $format );

		if ( 'legacy' === $format ) {

			$internal_callback  = esc_attr( $this->get_internal_callback() );

			// returns a url like http://www.skyverge.com/wc-api/auth/amazon/oauth2callback
			$callback_url = get_home_url( null, "wc-api/{$auth_path}/{$provider_id}/{$internal_callback}", $force_ssl ? 'https' : 'http' );

		} else {

			// returns a url like http://www.skyverge.com/?wc-api=auth&done=facebook
			$callback_url = add_query_arg( array( 'wc-api' => urlencode( $auth_path ), 'done' => $provider_id ), get_home_url( null, '/', $force_ssl ? 'https' : 'http' ) );
		}

		return $callback_url;
	}


	/**
	 * Gets the callback URL format for the provider.
	 *
	 * @since 2.6.4
	 *
	 * @param string|null $format the default callback URL format (optional)
	 * @return string the callback URL format
	 */
	public function get_callback_url_format( $format = null ) {

		if ( null === $format ) {
			$format = get_option( 'wc_social_login_callback_url_format', '' );
		}

		/**
		 * Filters the callback URL format for the provider.
		 *
		 * @since 2.6.4
		 *
		 * @param string $format the callback URL format
		 * @param string $provider_id the provider ID
		 */
		return (string) apply_filters( 'wc_social_login_callback_url_format', $format, $this->get_id() );
	}


	/**
	 * Returns the documentation URL for the provider.
	 *
	 * @since 2.6.0
	 *
	 * @return string URL
	 */
	public function get_documentation_url() {

		return "https://docs.woocommerce.com/document/woocommerce-social-login-create-social-apps/#{$this->id}";
	}


	/**
	 * Returns the providers HybridAuth config.
	 *
	 * @see http://hybridauth.sourceforge.net/userguide/Configuration.html
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	abstract public function get_hybridauth_config();


	/**
	 * Determines if the user has confirmed redirect URI config.
	 *
	 * Assumes true for most providers that do not have a setting to confirm this.
	 *
	 * @since 2.4.1
	 *
	 * @return bool
	 */
	public function is_redirect_uri_configured() {

		return true;
	}


	/**
	 * Loosely determines whether a provider can be reached.
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function is_reachable() {

		return empty( $this->ping_url ) || ! wp_safe_remote_get( $this->ping_url ) instanceof \WP_Error;
	}


}
