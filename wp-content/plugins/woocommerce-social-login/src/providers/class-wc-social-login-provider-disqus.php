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
 * Disqus social login provider class
 *
 * @since 1.6.0
 */
class WC_Social_Login_Provider_Disqus extends \WC_Social_Login_Provider {


	/**
	 * Disqus constructor.
	 *
	 * @since 1.6.0
	 * @param string $base_auth_path Base authentication path.
	 */
	public function __construct( $base_auth_path ) {

		$this->id                = 'disqus';
		$this->title             = __( 'Disqus', 'woocommerce-social-login' );
		$this->color             = '#2e9fff';
		$this->require_ssl       = false;
		$this->internal_callback = 'oauth2callback';
		$this->ping_url          = 'https://disqus.com/api';

		parent::__construct( $base_auth_path );

		// normalize profile
		add_filter( 'wc_social_login_' . $this->get_id() . '_profile', array( $this, 'normalize_profile' ) );
	}


	/**
	 * Get the description
	 *
	 * @since 1.6.0
	 * @see WC_Social_Login_Provider::get_description()
	 * @return string
	 */
	public function get_description() {

		/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
		$description = sprintf( __( 'Need help setting up and configuring Disqus? %1$sRead the docs%2$s', 'woocommerce-social-login' ), '<a href="' . $this->get_documentation_url() . '">', '</a>' );

		$callback_url_format = $this->get_callback_url_format();

		/* translators: Placeholder: %s - a url */
		$description .= '<br/><br/>' . sprintf( __( 'The callback URL is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url() . '</code>' );

		if ( 'legacy' === $callback_url_format ) {

			$description .= ' <strong>' . __( '(Please update your Disqus app to use this URL)', 'woocommerce-social-login' ) . '</strong>';

			/* translators: Placeholder: %s - a url */
			$description .= '<br/><br/>' . sprintf( __( 'The legacy callback URL is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url( $callback_url_format ) . '</code>' );
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
				'path'  => wc_social_login()->get_plugin_path() . '/src/hybridauth/class-sv-hybrid-providers-disqus.php',
				'class' => 'SV_Hybrid_Providers_Disqus',
			),
		) );
	}


	/**
	 * Gets the oAuth app client ID field label.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	public function get_client_id_field_label() {

		return __( 'API Key', 'woocommerce-social-login' );
	}


	/**
	 * Gets the oAuth app client secret field label.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	public function get_client_secret_field_label() {

		return __( 'API Secret', 'woocommerce-social-login' );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.6.0
	 * @see \WC_Social_Login_Provider::get_default_login_button_text()
	 * @return string
	 */
	public function get_default_login_button_text() {
		return __( 'Log in with Disqus', 'woocommerce-social-login' );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.6.0
	 * @see \WC_Social_Login_Provider::get_default_login_button_text()
	 * @return string
	 */
	public function get_default_link_button_text() {
		return __( 'Link your account to Disqus', 'woocommerce-social-login' );
	}


	/**
	 * Get notices.
	 *
	 * @since 2.0.4
	 * @return array
	 */
	public function get_notices() {
		return array(
			'account_linked'         => __( 'Your Disqus account is now linked to your account.', 'woocommerce-social-login' ),
			'account_unlinked'       => __( 'Disqus was successfully unlinked from your account.', 'woocommerce-social-login' ),
			'account_already_linked' => __( 'This Disqus account is already linked to another user account.', 'woocommerce-social-login' ),
			'account_already_exists' => __( 'A user account using the same email address as this Disqus account already exists.', 'woocommerce-social-login' ),
		);
	}


	/**
	 * Disqus returns a `name`, so try to map it to `first_name` & `last_name`
	 *
	 * @since 1.6.0
	 * @param array $profile Disqus profile data
	 * @return array Profile after our mappings
	 */
	public function normalize_profile( $profile ) {

		// Disqus only provides the 'name' so we need to try to split this to 'first_name' & 'last_name'
		// but we do not want to overwrite the 'first_name' & 'last_name' if they are already set
		$profile_types = array( 'raw', 'info' );

		foreach ( $profile_types as $type ) {

			if ( isset( $profile[ $type ]['name'] ) ) {

				$name = explode( ' ', $profile[ $type ]['name'] );

				if ( ! isset( $profile[ $type ]['first_name'] ) ) {
					// slice the last element
					$profile[ $type ]['first_name'] = implode( ' ', array_slice( $name, 0, count( $name ) - 1 ) );
				}

				if ( ! isset( $profile[ $type ]['last_name'] ) ) {
					// get the last element
					$profile[ $type ]['last_name'] = array_pop( $name );
				}
			}
		}

		return $profile;
	}
}
