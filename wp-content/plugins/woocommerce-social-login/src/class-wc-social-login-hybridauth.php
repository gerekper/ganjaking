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
 * HybridAuth class
 *
 * @since 2.0.0
 */
class WC_Social_Login_HybridAuth {


	/** @var string base authentication path */
	private $base_auth_path;

	/** @var array configuration */
	private $config;


	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $base_auth_path base authentication path
	 */
	public function __construct( $base_auth_path ) {

		$this->base_auth_path = $base_auth_path;

		add_action( 'init', array( $this, 'init_config' ), 11 );

		add_action( 'wp_logout', array( $this, 'logout' ) );

		// redirect after updating email
		add_filter ( 'wp_redirect', array( $this, 'redirect_after_save_account_details' ) );
	}


	/**
	 * Initialize HybridAuth configuration
	 *
	 * Initializes HybridAuth configuration with the configured
	 * strategies. HybridAuth will be instantiated separately
	 * in the authentication and callback methods, because HybridAuth
	 * will try to create authentication request instantly when
	 * instantiated.
	 *
	 * @since 2.0.0
	 */
	public function init_config() {

		$config = array(
			'base_url'   => add_query_arg( 'wc-api', $this->base_auth_path, home_url( '/' ) ),
			'providers'  => array(),
			'debug_mode' => defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG,
			'debug_file' => wc_get_log_file_path( wc_social_login()->get_id() . '_hybridauth' ),
		);

		// Loop over available providers and add their configuration
		foreach ( wc_social_login()->get_available_providers() as $provider ) {

			$config['providers'][ $provider->get_id() ] = $provider->get_hybridauth_config();
		}

		$this->config = apply_filters( 'wc_social_login_hybridauth_config', $config );
	}


	/**
	 * Authenticate using HybridAuth
	 *
	 * Loads HybridAuth and tries to authenticate using the given provider.
	 * If user is already authenticated, gets their profile and updates the local
	 * profile or creates a new user if necessary. If user is not logged in or
	 * has not authorized the local app, then HA will redirect the user to the
	 * provider, and then later back to the auth endpoint. In short: this
	 * method should be called both when starting the auth and when ending the auth.
	 *
	 * @link https://www.sitepoint.com/social-logins-php-hybridauth/
	 *
	 * @since 2.0.0
	 */
	public function authenticate( $provider_id, $return_url = null ) {

		$user_id = null;

		try {

			$hybridauth = $this->load_hybridauth();
			$provider   = wc_social_login()->get_provider( $provider_id );

			// authenticate with HA, which will automatically redirect to the provider if
			// we are not already authenticated
			$ha_provider = $hybridauth->authenticate( $provider_id, array(
				'hauth_return_to' => $this->get_current_url(),
				// TODO: remove the format param when removing support for legacy callback URLs {IT 2016-10-12}
				'login_done'      => $provider->get_callback_url(),
			) );

			// unless we are successfully authenticated, we won't reach beyond this point.

			// ask for the user's profile from the provider
			$ha_profile = $ha_provider->getUserProfile();

		} catch ( \Exception $e ) {

			// see https://github.com/skyverge/wc-plugins/issues/1950
			if ( ! empty( $hybridauth ) && is_callable( array( $hybridauth, 'logoutAllProviders' ) ) ) {
				$hybridauth->logoutAllProviders();
			}

			// log the exception, since we cannot extract anything meaningful from the message
			// (it's basically a serialized object concatenated with a string)
			wc_social_login()->log( sprintf( 'Error: %s', $e->getMessage() ) );

			wc_add_notice( __( 'Provider Authentication error', 'woocommerce-social-login' ), 'error' );

			// this could be an error from an admin user trying the setup wizard
			if ( is_user_logged_in() && current_user_can( 'manage_woocommerce' ) ) {

				$setup_wizard_provider_id = get_option( 'wc_social_login_setup_wizard_default_provider' );

				if ( $provider_id === $setup_wizard_provider_id ) {
					update_option( 'wc_social_login_setup_wizard_last_error', [ $provider_id => wc_clean( $e->getMessage() ) ] );
				}
			}

			$this->redirect();
		}

		// convert Hybrid_User_Profile to an associative array with snake_case keys
		$profile_data = (array) $ha_profile;

		if ( ! empty( $profile_data ) ) {
			foreach ( $profile_data as $key => $value ) {

				unset( $profile_data[ $key ] );

				$profile_data[ $this->decamelize( $key ) ] = $value;
			}
		}

		$profile = new \WC_Social_Login_Provider_Profile( $provider_id, $profile_data );

		// process user profile and log in
		try {

			$user_id = $this->process_profile( $profile, $provider_id );

		} catch ( \Exception $e ) {

			wc_add_notice( $e->getMessage(), 'error' );

		}

		// extra level of security for hosts that may leak HybridAuth sessions to other visitors >:(
		// see https://github.com/skyverge/wc-plugins/issues/2481
		if ( ! empty( $hybridauth ) && is_callable( array( $hybridauth, 'logoutAllProviders' ) ) ) {
			$hybridauth->logoutAllProviders();
		}

		$this->redirect( $user_id );
	}


	/**
	 * Logs out from all providers on the HybridAuth session when logging out of WP.
	 *
	 * @internal
	 * @since 2.2.0
	 */
	public function logout() {

		try {

			$hybridauth = $this->load_hybridauth();
			$hybridauth->logoutAllProviders();

		} catch ( \Exception $e ) {

			// log the exception, since we cannot extract anything meaningful from the message
			// (it's basically a serialized object concatenated with a string)
			wc_social_login()->log( sprintf( 'Error: %s', $e->getMessage() ) );
		}
	}


	/**
	 * Loads and returns the HybridAuth class instance.
	 *
	 * @since 2.2.0
	 *
	 * @return \Hybrid_Auth hybridauth instance
	 */
	private function load_hybridauth() {

		require_once( wc_social_login()->get_plugin_path() . '/src/hybridauth/class-hybrid-storage.php' );
		require_once( wc_social_login()->get_plugin_path() . '/vendor/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php' );

		return new Hybrid_Auth( $this->config );
	}


	/**
	 * Process HybridAuth endpoints
	 *
	 * @since 2.0.0
	 * @param array $request Optional. Custom request to pass to HA for processing.
	 */
	public function process_endpoint( $request = null ) {

		require_once( wc_social_login()->get_plugin_path() . '/src/hybridauth/class-hybrid-storage.php' );
		require_once( wc_social_login()->get_plugin_path() . '/vendor/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php' );
		require_once( wc_social_login()->get_plugin_path() . '/vendor/hybridauth/hybridauth/hybridauth/Hybrid/Endpoint.php' );

		try {

			Hybrid_Endpoint::process( $request );

		} catch ( \Hybrid_Exception $e ) {

			$error_code    = 1;
			$error_message = $e->getMessage();

			// if we hit a probable caching issue, we warn the admin as well
			if ( Framework\SV_WC_Helper::str_starts_with( $error_message, 'Endpoint: Error while trying to init Hybrid_Auth: You cannot access this page directly' ) ) {

				$error_message .= sprintf( ' There might be an issue with the host or a WordPress plugin caching the authentication endpoint, please check documentation: %1$s. If you are not seeing any issues with %2$s, please disregard this message.',
					esc_url( wc_social_login()->get_documentation_url() . '#caching-issue' ),
					wc_social_login()->get_plugin_name()
				);

				set_transient( '_wc_social_login_hybridauth_caching_issue', $error_code, WEEK_IN_SECONDS );
			}

			// log the exception message
			wc_social_login()->log( sprintf( 'HybridAuth Error: %s', $error_message ) );

			// This exception may most likely refer to an issue with cookies/session/caching, so instead
			// of setting the error message in session, we store it in a transient and redirect back to
			// my account. See more: https://docs.woocommerce.com/document/configuring-caching-plugins/#section-1
			$return_url = add_query_arg( 'social-login-auth-error', $error_code, wc_get_page_permalink( 'myaccount' ) );

			wp_safe_redirect( $return_url );
			exit;
		}
	}


	/**
	 * Redirect back to the provided return_url
	 *
	 * @since 2.0.0
	 * @param int $user_id the user ID. Default 0.
	 */
	public function redirect( $user_id = 0 ) {

		$user       = get_user_by( 'id', $user_id );
		$return_url = get_transient( 'wcsl_' . md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) );
		$return_url = $return_url ? urldecode( $return_url ) : wc_get_page_permalink( 'myaccount' );

		// if the provider did not provide an email and user does not have an email, display a notice and
		// redirect to my account page, unless user was on checkout page where they are asked for an email
		// address anyway. User will be redirected to original return url after entering their email address.
		if ( isset( $user->user_email ) && '' === $user->user_email && ! Framework\SV_WC_Helper::str_starts_with( $return_url, wc_get_checkout_url() ) ) {

			WC()->session->set( 'wc_social_login_missing_email', true );

			if ( $return_url === wc_get_page_permalink( 'myaccount' ) ) {
				wc_add_notice( __( 'Please enter your email address to complete your registration', 'woocommerce-social-login' ), 'error' );
			} else {
				/* translators: %s - a URL */
				wc_add_notice( sprintf( __( 'Please enter your email address to complete your registration and continue to %s', 'woocommerce-social-login' ), $return_url ), 'error' );
			}

			$return_url = wc_customer_edit_account_url();

		} else {

			delete_transient( 'wcsl_' . md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) );
		}

		wp_safe_redirect( esc_url_raw( $return_url ) );
		exit;
	}


	/**
	 * Redirect back to the provided return_url after saving account details
	 *
	 * @since 2.0.0
	 * @param string $redirect_location
	 * @param string $redirect_location
	 * @return string URL
	 */
	public function redirect_after_save_account_details( $redirect_location ) {

		$safe_redirect_location = wc_get_page_permalink( 'myaccount' );
		$safe_redirect_location = wp_sanitize_redirect( $safe_redirect_location );
		$safe_redirect_location = wp_validate_redirect( $safe_redirect_location, admin_url() );

		if ( $redirect_location === $safe_redirect_location && $new_location = get_transient( 'wcsl_' . md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) ) ) {

			$new_location = wp_sanitize_redirect( $new_location );
			$new_location = wp_validate_redirect( $new_location, admin_url() );

			$redirect_location = $new_location;

			delete_transient( 'wcsl_' . md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) );
		}

		return $redirect_location;
	}


	/**
	 * Process authenticated user's profile
	 *
	 * In 2.0.0 moved here from \WC_Social_Login_Provider and changed
	 * visibility from protected to private
	 *
	 * @since 1.0.0
	 * @param WC_Social_Login_Provider_profile $profile
	 * @return int the user ID
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	private function process_profile( $profile ) {
		global $wpdb;

		$provider = wc_social_login()->get_provider( $profile->get_provider_id() );

		// this should never happen, but let's make sure we handle this anyway
		if ( ! $provider ) {
			throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'No provider class found for %s', 'woocommerce-social-login' ), $profile->get_provider_id() ) );
		}

		$user         = null;
		$found_via    = null;
		$new_customer = false;

		// ensure that providers can't return a blank identifier
		if ( ! $profile->get_identifier() ) {
			throw new Framework\SV_WC_Plugin_Exception( sprintf( __( '%s returned an invalid user identifier.', 'woocommerce-social-login' ), $profile->get_provider_id() ) );
		}

		// look up if the user already exists on WP

		// first, try to identify user based on the social identifier
		$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value = %s", '_wc_social_login_' . $provider->get_id() . '_identifier', $profile->get_identifier() ) );

		if ( $user_id ) {

			$user = get_user_by( 'id', $user_id );

			if ( $user ) {
				$found_via = 'identifier';
			}
		}

		// fall back to email - user may already have an account on WooCommerce with the
		// same email as in their social profile
		if ( ! $user && $profile->has_email() ) {

			$user = get_user_by( 'email', $profile->get_email() );

			if ( $user ) {
				$found_via = 'email';
			}
		}

		// if a user is already logged in...
		if ( is_user_logged_in() ) {

			// ...and a user matching the social profile was found,
			// check that the logged in user and found user are the same.
			// This happens when user is linking a new social profile to their account.
			if ( $user && get_current_user_id() !== $user->ID ) {

				if ( 'identifier' === $found_via ) {
					throw new Framework\SV_WC_Plugin_Exception( $provider->get_notice_text( 'account_already_linked' ) );
				} else {
					throw new Framework\SV_WC_Plugin_Exception( $provider->get_notice_text( 'account_already_exists' ) );
				}
			}

			// if the social profile is not linked to any user accounts,
			// use the currently logged in user as the customer
			if ( ! $user ) {
				$user = get_user_by( 'id', get_current_user_id() );
			}
		}

		// check if a user is found via email and not in one of the allowed roles
		if ( ! is_user_logged_in() && $user && 'email' === $found_via && ! in_array( $user->roles[0], apply_filters( 'wc_social_login_find_by_email_allowed_user_roles', array( 'subscriber', 'customer' ) ) ) ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Oops, it looks like you may already have an account&hellip; please log in to link your profile.', 'woocommerce-social-login' ) );
		}

		// if no user was found, create one
		if ( ! $user ) {

			/**
			 * Fires before creating a new user.
			 *
			 * @since 2.0.3
			 * @param WC_Social_Login_Provider_profile $profile
			 * @param string $provider_id Social Login provider ID
			 */
			do_action( 'wc_social_login_before_create_user', $profile, $provider->get_id() );

			$user_id = $this->create_new_customer( $profile );
			$user    = get_user_by( 'id', $user_id );

			// indicate that a new user was created
			$new_customer = true;
		}

		// update customer's WP user profile and billing details
		$profile->update_customer_profile( $user->ID, $new_customer );

		// log user in or add account linked notice for a logged in user
		if ( ! is_user_logged_in() ) {

			if ( ! $message = apply_filters( 'wc_social_login_set_auth_cookie', '', $user ) ) {

				/**
				 * Fires before logging a user into the site via social network.
				 *
				 * @since 2.0.3
				 * @param int $user_id ID of the user
				 * @param string $provider_id Social Login provider ID
				 * @param WC_Social_Login_Provider_profile $profile
				 */
				do_action( 'wc_social_login_before_user_login', $user->ID, $provider->get_id(), $profile );

				wc_set_customer_auth_cookie( $user->ID );

				// Store login timestamp
				update_user_meta( $user->ID, '_wc_social_login_' . $provider->get_id() . '_login_timestamp', current_time( 'timestamp' ) );
				update_user_meta( $user->ID, '_wc_social_login_' . $provider->get_id() . '_login_timestamp_gmt', time() );

				/** this hook is documened in wp-includes/user.php */
				do_action( 'wp_login', $user->user_login, $user );

				/**
				 * User authenticated via social login.
				 *
				 * @since 1.0.0
				 * @param int $user_id ID of the user
				 * @param string $provider_id Social Login provider ID
				 */
				do_action( 'wc_social_login_user_authenticated', $user->ID, $provider->get_id() );

			} else {

				wc_add_notice( $message, 'notice' );
			}

		} else {

			wc_add_notice( $provider->get_notice_text( 'account_linked' ), 'notice' );
		}

		return $user->ID;
	}


	/**
	 * Create a WP user from the provider's data
	 *
	 * In 2.0.0 moved here from \WC_Social_Login_Provider and changed
	 * visibility from public to private
	 *
	 * @since 1.0.0
	 * @param \WC_Social_Login_Provider_profile $profile user profile object
	 * @return int|WP_Error The newly created user's ID or a WP_Error object if the user could not be created.
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	private function create_new_customer( $profile ) {

		/**
		 * Filter data for user created by social login.
		 *
		 * @since 1.0.0
		 * @param array $userdata
		 * @param \WC_Social_Login_Provider_Profile $profile
		 */
		$userdata = apply_filters( 'wc_social_login_' . $profile->get_provider_id() . '_new_user_data', array(
			'role'       => 'customer',
			'user_login' => $profile->has_email() ? sanitize_email( $profile->get_email() ) : $profile->get_username(),
			'user_email' => $profile->get_email(),
			'user_pass'  => wp_generate_password(),
			'first_name' => $profile->get_first_name(),
			'last_name'  => $profile->get_last_name(),
		), $profile );

		// ensure username is not blank - if it is, use first and last name to generate a username
		if ( empty( $userdata['user_login'] ) ) {
			$userdata['user_login'] = sanitize_key( $userdata['first_name'] . $userdata['last_name'] );
		}

		// mimics behavior of wp_insert_user() which would strip encoded characters and could prompt empty_user_login error
		$username = sanitize_user( $userdata['user_login'], true );

		// if the username is empty, try to build one from other profile properties
		if ( '' === $username ) {

			// try to make a username from user first and last name
			if ( '' !== $userdata['first_name'] || '' !== $userdata['last_name'] ) {
				$name     = is_rtl() ? implode( '_', array_filter( array( $userdata['last_name'], $userdata['first_name'] ) ) ) : implode( '_', array_filter( array( $userdata['first_name'], $userdata['last_name'] ) ) );
				$username = sanitize_user( strtolower( $name ), true );
			}

			// if that didn't work, replace the empty username with a unique user_* ID (tries to use a localized name for user first)
			if ( '' === $username ) {
				$user     = sanitize_user( strtolower( __( 'User', 'woocommerce-social-login' ) ), true );
				$username = uniqid( empty( $user ) ? 'user_' : "{$user}_", false );
			}

			$userdata['user_login'] = $username;
		}

		// ensure username is unique
		$append = 1;

		while ( username_exists( $userdata['user_login'] ) ) {
			$userdata['user_login'] = $username . $append;
			$append ++;
		}

		$customer_id = wp_insert_user( $userdata );

		if ( is_wp_error( $customer_id ) ) {
			throw new Framework\SV_WC_Plugin_Exception( '<strong>' . __( 'ERROR', 'woocommerce-social-login' ) . '</strong>: ' . __( 'Couldn&#8217;t register you&hellip; please contact us if you continue to have problems.', 'woocommerce-social-login' ) );
		}

		// trigger New Account email
		do_action( 'woocommerce_created_customer', $customer_id, $userdata, false );

		return $customer_id;
	}


	/**
	 * Remove/unlink the social login provider from the provided user
	 *
	 * @since 2.0.0
	 * @param int $user_id The User ID
	 * @param string $provider_id Provider ID
	 */
	public function unlink_provider( $user_id, $provider_id ) {

		// remove all metas related to this social profile, except for the profile image
		delete_user_meta( $user_id, '_wc_social_login_' . $provider_id . '_identifier' );
		delete_user_meta( $user_id, '_wc_social_login_' . $provider_id . '_profile' );
		delete_user_meta( $user_id, '_wc_social_login_' . $provider_id . '_login_timestamp' );
		delete_user_meta( $user_id, '_wc_social_login_' . $provider_id . '_login_timestamp_gmt' );

		// unlink the profile image
		$this->unlink_profile_image( $user_id, $provider_id );

		/**
		 * User unlinked a social login profile.
		 *
		 * @since 1.0.0
		 * @param int $user_id ID of the user
		 * @param string $provider_id ID of the Social Login provider that was unlinked
		 */
		do_action( 'wc_social_login_account_unlinked', $user_id, $provider_id );
	}


	/**
	 * Remove social profile image after unlinking the profile.
	 * Otherwise, we end up with an orphaned URL, possibly 404.
	 *
	 * In 2.0.0 moved here from \WC_Social_Login_Provider and changed
	 * visibility from protected to private
	 *
	 * @since 1.6.0
	 * @param int $user_id The User ID
	 * @param string $provider_id Provider ID
	 */
	private function unlink_profile_image( $user_id, $provider_id ) {

		// preserve the value of the profile image being removed before deleting the meta
		$unlinked_image = get_user_meta( $user_id, '_wc_social_login_' . $provider_id . '_profile_image', true );

		delete_user_meta( $user_id, '_wc_social_login_' . $provider_id . '_profile_image' );

		$avatar_image = get_user_meta( $user_id, '_wc_social_login_profile_image', true );

		// check if unlinked image is the current avatar; if so, find a replacement
		if ( $avatar_image === $unlinked_image ) {

			// delete the avatar image
			delete_user_meta( $user_id, '_wc_social_login_profile_image' );

			// check other linked profiles for the replacement image
			foreach ( wc_social_login()->get_user_social_login_profiles( $user_id ) as $profile ) {

				if ( $profile->has_image() ) {

					// A replacement has been found. Set it as the new avatar.
					$profile->update_customer_profile_image( $user_id );
					break;
				}
			}
		}
	}


	/**
	 * Convert a camelCase string to snake_case
	 *
	 * @since 2.0.0
	 * @param string $input
	 * @return string
	 */
	private function decamelize( $input ) {

		return strtolower( preg_replace( array( '/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/' ), '$1_$2', $input ) );
	}


	/**
	 * Get current URL, the WordPress-way
	 *
	 * Instead of relying on the HTTP_HOST server var, we use
	 * home_url(), so that we get the host configured in site options.
	 * Additionally, this automatically uses the correct domain when
	 * using Forward with the WooCommerce Dev Helper plugin.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	private function get_current_url() {

		return home_url() . $_SERVER['REQUEST_URI'];
	}


}
