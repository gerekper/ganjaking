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
 * Amazon social login provider class
 *
 * @since 1.0.0
 */
class WC_Social_Login_Provider_Amazon extends \WC_Social_Login_Provider {


	/**
	 * Amazon constructor.
	 *
	 * @since 1.0.0
	 * @param string $base_auth_path Base authentication path.
	 */
	public function __construct( $base_auth_path ) {

		$this->id                = 'amazon';
		$this->title             = __( 'Amazon', 'woocommerce-social-login' );
		$this->color             = '#ff9900';
		$this->internal_callback = 'oauth2callback';
		$this->require_ssl       = true;
		$this->ping_url          = 'https://api.amazon.com';

		parent::__construct( $base_auth_path );

		// Update customer's postcode and name from Amazon
		add_action( 'wc_social_login_' . $this->get_id() . '_update_customer_billing_profile', array( $this, 'update_customer_postcode' ), 10, 2 );
		add_filter( 'wc_social_login_' . $this->get_id() . '_profile', array( $this, 'normalize_profile' ) );
	}


	/**
	 * Get the description, overridden to display the callback URL
	 * as a convenience since Amazon requires the admin to enter it for the app
	 *
	 * @since 1.0.0
	 * @see WC_Social_Login_Provider::get_description()
	 * @return string
	 */
	public function get_description() {

		/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
		$description = sprintf( __( 'Need help setting up and configuring Amazon? %1$sRead the docs%2$s', 'woocommerce-social-login' ), '<a href="' . $this->get_documentation_url() . '">', '</a>' );

		$callback_url_format = $this->get_callback_url_format();

		/* translators: Placeholder: %s - a url */
		$description .= '<br/><br/>' . sprintf( __( 'The allowed return URL is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url() . '</code>' );

		if ( 'legacy' === $callback_url_format ) {

			$description .= ' <strong>' . __( '(Please update your Amazon app to use this URL)', 'woocommerce-social-login' ) . '</strong>';

			/* translators: Placeholder: %s - a url */
			$description .= '<br/><br/>' . sprintf( __( 'The legacy allowed return URL is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url( $callback_url_format ) . '</code>' );
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
				'path'  => wc_social_login()->get_plugin_path() . '/src/hybridauth/class-sv-hybrid-providers-amazon.php',
				'class' => 'SV_Hybrid_Providers_Amazon',
			),
			'scope' => 'profile postal_code payments:widget',
		) );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.0.0
	 * @see WC_Social_Login_Provider::get_default_login_button_text()
	 * @return string
	 */
	public function get_default_login_button_text() {
		return __( 'Log in with Amazon', 'woocommerce-social-login' );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.0.0
	 * @see WC_Social_Login_Provider::get_default_login_button_text()
	 * @return string
	 */
	public function get_default_link_button_text() {
		return __( 'Link your account to Amazon', 'woocommerce-social-login' );
	}


	/**
	 * Get notices.
	 *
	 * @since 2.0.4
	 * @return array
	 */
	public function get_notices() {
		return array(
			'account_linked'         => __( 'Your Amazon account is now linked to your account.', 'woocommerce-social-login' ),
			'account_unlinked'       => __( 'Amazon was successfully unlinked from your account.', 'woocommerce-social-login' ),
			'account_already_linked' => __( 'This Amazon account is already linked to another user account.', 'woocommerce-social-login' ),
			'account_already_exists' => __( 'A user account using the same email address as this Amazon account already exists.', 'woocommerce-social-login' ),
		);
	}


	/**
	 * Update customer's billing postcode based on Amazon profile
	 *
	 * @param int $customer_id
	 * @param \WC_Social_Login_Provider_Profile $profile
	 */
	public function update_customer_postcode( $customer_id, WC_Social_Login_Provider_Profile $profile ) {

		$amazon_profile = $profile->get_raw_profile();

		if ( isset( $amazon_profile['raw']['postal_code'] ) && $amazon_profile['raw']['postal_code'] && ! get_user_meta( $customer_id, 'billing_postcode', true ) ) {
			update_user_meta( $customer_id, 'billing_postcode', $amazon_profile['raw']['postal_code'] );
		}
	}


	/**
	 * Amazon returns a `display_name`, so try to map it to `first_name` & `last_name`
	 *
	 * @since 2.0.0
	 * @param array $profile amazon profile data
	 * @return array
	 */
	public function normalize_profile( $profile ) {

		// Amazon only provides the 'name' so we need to try to split this to 'first_name' & 'last_name'
		// but we do not want to overwrite the 'first_name' & 'last_name' if they are already set
		if ( isset( $profile['display_name'] ) ) {

			$name = explode( ' ', $profile['display_name'] );

			if ( ! isset( $profile['first_name'] ) ) {

				// get the first element
				$profile['first_name'] = implode( ' ', array_slice( $name, 0, count( $name ) - 1 ) );
			}

			if ( ! isset( $profile['last_name'] ) ) {

				// get the last element
				$profile['last_name'] = array_pop( $name );
			}
		}

		return $profile;
	}


}
