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
 * LinkedIn social login provider class.
 *
 * @since 1.1.0
 */
class WC_Social_Login_Provider_LinkedIn extends \WC_Social_Login_Provider {


	/**
	 * LinkedIn constructor.
	 *
	 * @since 1.1.0
	 *
	 * @param string $base_auth_path Base authentication path.
	 */
	public function __construct( $base_auth_path ) {

		$this->id                = 'linkedin';
		$this->title             = __( 'LinkedIn', 'woocommerce-social-login' );
		$this->color             = '#0077b5';
		$this->internal_callback = 'oauth2callback';
		$this->require_ssl       = false;
		$this->ping_url          = 'https://api.linkedin.com';

		parent::__construct( $base_auth_path );
	}


	/**
	 * Get the description, overridden to display the callback URL as a convenience since LinkedIn requires the admin to enter it for the app.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_description() {

		/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
		$description = sprintf( __( 'Need help setting up and configuring LinkedIn? %1$sRead the docs%2$s', 'woocommerce-social-login' ), '<a href="' . $this->get_documentation_url() . '">', '</a>' );

		$callback_url_format = $this->get_callback_url_format();

		/* translators: Placeholder: %s - a url */
		$description .= '<br/><br/>' . sprintf( __( 'The OAuth 2.0 Redirect URL is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url() . '</code>' );

		if ( 'legacy' === $callback_url_format ) {

			$description .= ' <strong>' . __( '(Please update your LinkedIn app to use this URL)', 'woocommerce-social-login' ) . '</strong>';

			/* translators: Placeholder: %s - a url */
			$description .= '<br/><br/>' . sprintf( __( 'The legacy OAuth 2.0 Redirect URL is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url( $callback_url_format ) . '</code>' );
		}

		return $description;
	}


	/**
	 * Gets the providers HybridAuth config.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_hybridauth_config() {

		/**
		 * Filters the provider's HybridAuth configuration.
		 *
		 * @since 2.0.0
		 *
		 * @param array $config  http://hybridauth.sourceforge.net/userguide/Configuration.html
		 */
		return (array) apply_filters( 'wc_social_login_' . $this->get_id() . '_hybridauth_config', [
			'enabled' => true,
			'keys'    => [
				'id'     => $this->get_client_id(),
				'secret' => $this->get_client_secret(),
			],
			'wrapper' => [
				'path'  => wc_social_login()->get_plugin_path() . '/src/hybridauth/class-sv-hybrid-providers-linkedin.php',
				'class' => 'SV_Hybrid_Providers_Linkedin',
			],
			'scope'   => $this->get_scope(),
		] );
	}


	/**
	 * Adds an additional form field to handle API versions.
	 *
	 * @since 2.6.4
	 */
	public function init_form_fields() {

		parent::init_form_fields();

		$this->form_fields = Framework\SV_WC_Helper::array_insert_after( $this->form_fields, 'enabled', [
			'api_version' => [
				/* translators: https:www.skyverge.com/for-translators-environments/ */
				'title'    => __( 'API Version', 'woocommerce-social-login' ),
				'type'     => 'select',
				'desc_tip' => __( 'Select which API version your application uses.', 'woocommerce-social-login' ),
				'options'  => [
					'v1' => 'v1',
					'v2' => 'v2',
				],
				'default'  => 'v2',
			],
		] );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_default_login_button_text() {

		return __( 'Log in with LinkedIn', 'woocommerce-social-login' );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_default_link_button_text() {

		return __( 'Link your account to LinkedIn', 'woocommerce-social-login' );
	}


	/**
	 * Get notices.
	 *
	 * @since 2.0.4
	 * @return array
	 */
	public function get_notices() {

		return [
			'account_linked'         => __( 'Your LinkedIn account is now linked to your account.', 'woocommerce-social-login' ),
			'account_unlinked'       => __( 'LinkedIn was successfully unlinked from your account.', 'woocommerce-social-login' ),
			'account_already_linked' => __( 'This LinkedIn account is already linked to another user account.', 'woocommerce-social-login' ),
			'account_already_exists' => __( 'A user account using the same email address as this LinkedIn account already exists.', 'woocommerce-social-login' ),
		];
	}


	/**
	 * Gets the API version to use in requests.
	 *
	 * @since 2.6.4
	 *
	 * @return string v1 or v2
	 */
	public function get_api_version() {

		/**
		 * Filters the API version to use with LinkedIn.
		 *
		 * @since 2.6.4
		 *
		 * @param string $api_version should be either v1 or v2
		 * @param \WC_Social_Login_Provider_LinkedIn $linkedin provider instance
		 */
		$api_version = (string) apply_filters( 'wc_social_login_linkedin_api_version', $this->get_option( 'api_version', 'v2' ), $this );

		return in_array( $api_version, [ 'v1', 'v2' ], true ) ? $api_version : 'v2';
	}


	/**
	 * Gets the profile scope to use in API requests.
	 *
	 * @since 2.6.4
	 *
	 * @return string
	 */
	public function get_scope() {

		switch ( $this->get_api_version() ) {
			case 'v1' :
				$scope = 'r_basicprofile r_emailaddress';
			break;
			case 'v2' :
			default :
				$scope = 'r_liteprofile r_emailaddress w_member_social';
			break;
		}

		/**
		 * Filters the LinkedIn provider scope.
		 *
		 * @since 2.6.4
		 *
		 * @param string $scope scope
		 * @param \WC_Social_Login_Provider_LinkedIn $linkedin provider instance
		 */
		return (string) apply_filters( 'wc_social_login_linkedin_scope', $scope, $this );
	}


}
