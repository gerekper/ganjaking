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
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * PayPal social login provider class
 *
 * @since 1.1.0
 */
class WC_Social_Login_Provider_PayPal extends \WC_Social_Login_Provider {


	/**
	 * PayPal constructor.
	 *
	 * @since 1.1.0
	 * @param string $base_auth_path Base authentication path.
	 */
	public function __construct( $base_auth_path ) {

		$this->id                = 'paypal';
		$this->title             = __( 'PayPal', 'woocommerce-social-login' );
		$this->color             = '#00457c';
		$this->internal_callback = 'oauth2callback';
		$this->require_ssl       = false;
		$this->ping_url          = 'https://api.paypal.com';

		parent::__construct( $base_auth_path );
	}


	/**
	 * Get the description, overridden to display the callback URL
	 * as a convenience since PayPal requires the admin to enter it for the app
	 *
	 * @since 1.1.0
	 * @see \WC_Social_Login_Provider::get_description()
	 * @return string
	 */
	public function get_description() {

		/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
		$description = sprintf( __( 'Need help setting up and configuring PayPal? %1$sRead the docs%2$s', 'woocommerce-social-login' ), '<a href="' . $this->get_documentation_url() . '">', '</a>' );

		$callback_url_format = $this->get_callback_url_format();

		/* translators: Placeholder: %s - a url */
		$description .= '<br/><br/>' . sprintf( __( 'The App redirect URL is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url() . '</code>' );

		if ( 'legacy' === $callback_url_format ) {

			$description .= ' <strong>' . __( '(Please update your PayPal App to use this URL)', 'woocommerce-social-login' ) . '</strong>';

			/* translators: Placeholder: %s - a url */
			$description .= '<br/><br/>' . sprintf( __( 'The legacy App redirect URL is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url( $callback_url_format ) . '</code>' );
		}

		return $description;
	}


	/**
	 * Returns the providers HybridAuth config.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_hybridauth_config() {

		/**
		 * Filter provider's HybridAuth configuration.
		 *
		 * @see http://hybridauth.sourceforge.net/userguide/Configuration.html
		 *
		 * @since 2.0.0
		 *
		 * @param array $config associative array
		 */
		return apply_filters( 'wc_social_login_' . $this->get_id() . '_hybridauth_config', array(
			'enabled' => true,
			'keys'    => array(
				'id'     => $this->get_client_id(),
				'secret' => $this->get_client_secret(),
			),
			'wrapper' => array(
				'path'  => wc_social_login()->get_plugin_path() . '/src/hybridauth/class-sv-hybrid-providers-paypal.php',
				'class' => 'SV_Hybrid_Providers_PayPal',
			),
			'scope'   => $this->get_scope(),
			'sandbox' => 'sandbox' === $this->get_option( 'environment' ),
		) );
	}


	/**
	 * Gets user attributes scope mappings.
	 *
	 * By default we must require at least the user `profile` (a PayPal identifier which can be used as the WordPress user login name).
	 * PayPal may impose a review process before enabling the sharing of user email addresses, so the `email` scope attribute needs to be optional.
	 *
	 * Other optional scope attributes we can support are `phone` and `address` (full address details).
	 * The `profile` scope can provide access to further optional user profile fields, such as full name, date of birth, gender, language, if these are enabled in PayPal.
	 *
	 * @link https://developer.paypal.com/docs/integration/direct/identity/attributes/
	 *
	 * @since 2.6.2
	 *
	 * @return string space-separated scope attributes
	 */
	private function get_scope() {

		/**
		 * Filters PayPal login request scope attribute mappings.
		 *
		 * For example, it's possible to exclude the profile (full name) field, and use only the email address.
		 * Or add other fields like phone number and address information, provided that these are handled by additional code.
		 *
		 * @since 2.6.2
		 *
		 * @param string[] $scope attribute mappings as a list of array items
		 */
		$scope = apply_filters( 'wc_social_login_' . $this->get_id() . '_scope', explode( ' ', $this->get_option( 'scope', '' ) ) );

		// the 'profile' scope will always be enforced
		return empty( $scope ) || ! is_array( $scope ) ? 'profile' : implode ( ' ', array_unique( array_merge( $scope, array( 'profile' ) ) ) );
	}


	/**
	 * Overrides the default form fields to:
	 *
	 * 1) tweak the title for the client ID/secret so it matches PayPal's UI
	 * 2) add the environment setting
	 * 3) add an hidden field to hold the scope attributes (default may vary across new and existing installs)
	 *
	 * @see \WC_Social_Login_Provider::init_form_fields()
	 * @see \WC_Social_Login_Provider_PayPal::get_scope()
	 *
	 * @since 1.1.0
	 */
	public function init_form_fields() {

		parent::init_form_fields();

		$this->form_fields['secret']['title'] = __( 'Secret', 'woocommerce-social-login' );

		$this->form_fields['environment'] = array(
			/* translators: https:www.skyverge.com/for-translators-environments/ */
			'title'    => __( 'Environment', 'woocommerce-social-login' ),
			'type'     => 'select',
			'desc_tip' => __( 'Select which environment to process logins under.', 'woocommerce-social-login' ),
			'options'  => array(
				/* translators: Live (Production) environment - https:www.skyverge.com/for-translators-environments/ */
				'live'    => __( 'Live', 'woocommerce-social-login' ),
				/* translators: Placeholders: Sandbox (Test) environment - https:www.skyverge.com/for-translators-environments/ */
				'sandbox' => __( 'Sandbox', 'woocommerce-social-login' ),
			),
			'default'  => 'live',
		);

		// sets this to a hidden field for now, until we want to make it editable
		$this->form_fields['scope'] = array(
			'type'    => 'hidden',
			'default' => 'profile',
		);
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.1.0
	 * @see \WC_Social_Login_Provider::get_default_login_button_text()
	 * @return string
	 */
	public function get_default_login_button_text() {
		return __( 'Log in with PayPal', 'woocommerce-social-login' );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.1.0
	 * @see \WC_Social_Login_Provider::get_default_login_button_text()
	 * @return string
	 */
	public function get_default_link_button_text() {
		return __( 'Link your account to PayPal', 'woocommerce-social-login' );
	}


	/**
	 * Get notices.
	 *
	 * @since 2.0.4
	 * @return array
	 */
	public function get_notices() {
		return array(
			'account_linked'         => __( 'Your PayPal account is now linked to your account.', 'woocommerce-social-login' ),
			'account_unlinked'       => __( 'PayPal was successfully unlinked from your account.', 'woocommerce-social-login' ),
			'account_already_linked' => __( 'This PayPal account is already linked to another user account.', 'woocommerce-social-login' ),
			'account_already_exists' => __( 'A user account using the same email address as this PayPal account already exists.', 'woocommerce-social-login' ),
		);
	}


}
