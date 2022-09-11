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
 * Google social login provider class
 *
 * @since 1.0.0
 */
class WC_Social_Login_Provider_Google extends \WC_Social_Login_Provider {


	/**
	 * Google constructor.
	 *
	 * @since 1.0.0
	 * @param string $base_auth_path Base authentication path.
	 */
	public function __construct( $base_auth_path ) {

		$this->id                = 'google';
		$this->title             = __( 'Google', 'woocommerce-social-login' );
		$this->color             = '#4285f4';
		$this->internal_callback = 'oauth2callback';
		$this->require_ssl       = true;
		$this->ping_url          = 'https://www.googleapis.com';

		parent::__construct( $base_auth_path );
	}


	/**
	 * Get the description, overridden to display the callback URL
	 * as a convenience since Google requires the admin to enter it for the app
	 *
	 * @since 1.0.0
	 * @see \WC_Social_Login_Provider::get_description()
	 * @return string
	 */
	public function get_description() {

		/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
		$description = sprintf( __( 'Need help setting up and configuring Google? %1$sRead the docs%2$s', 'woocommerce-social-login' ), '<a href="' . $this->get_documentation_url() . '">', '</a>' );

		$callback_url_format = $this->get_callback_url_format();

		/* translators: Placeholder: %s - a url */
		$description .= '<br/><br/>' . sprintf( __( 'The Authorized Redirect URI is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url() . '</code>' );

		if ( 'legacy' === $callback_url_format ) {

			$description .= ' <strong>' . __( '(Please update your Google app to use this URL)', 'woocommerce-social-login' ) . '</strong>';

			/* translators: Placeholder: %s - a url */
			$description .= '<br/><br/>' . sprintf( __( 'The legacy Authorized Redirect URI is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url( $callback_url_format ) . '</code>' );
		}

		return $description;
	}


	/**
	 * Return the providers HybridAuth config
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_hybridauth_config() {

		/**
		 * Filter provider's HybridAuth configuration.
		 *
		 * @since 2.0.0
		 * @param array $config See http://hybridauth.sourceforge.net/userguide/Configuration.html
		 */
		return apply_filters( 'wc_social_login_' . $this->get_id() . '_hybridauth_config', array(
			'enabled' => true,
			'keys'    => array(
				'id'     => $this->get_client_id(),
				'secret' => $this->get_client_secret(),
			),
			'wrapper' => array(
				'path'  => wc_social_login()->get_plugin_path() . '/src/hybridauth/class-sv-hybrid-providers-google.php',
				'class' => 'SV_Hybrid_Providers_Google',
			),
			'scope' => implode( ' ', array(
				'https://www.googleapis.com/auth/userinfo.profile',
				'https://www.googleapis.com/auth/userinfo.email'
			) )
		) );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.0.0
	 * @see \WC_Social_Login_Provider::get_default_login_button_text()
	 * @return string
	 */
	public function get_default_login_button_text() {
		return __( 'Log in with Google', 'woocommerce-social-login' );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.0.0
	 * @see \WC_Social_Login_Provider::get_default_login_button_text()
	 * @return string
	 */
	public function get_default_link_button_text() {
		return __( 'Link your account to Google', 'woocommerce-social-login' );
	}


	/**
	 * Get notices.
	 *
	 * @since 2.0.4
	 * @return array
	 */
	public function get_notices() {
		return array(
			'account_linked'         => __( 'Your Google account is now linked to your account.', 'woocommerce-social-login' ),
			'account_unlinked'       => __( 'Google was successfully unlinked from your account.', 'woocommerce-social-login' ),
			'account_already_linked' => __( 'This Google account is already linked to another user account.', 'woocommerce-social-login' ),
			'account_already_exists' => __( 'A user account using the same email address as this Google account already exists.', 'woocommerce-social-login' ),
		);
	}


}
