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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_2 as Framework;

/**
 * Instagram social login provider class
 *
 * @since 1.1.0
 */
class WC_Social_Login_Provider_Instagram extends \WC_Social_Login_Provider {


	/**
	 * Constructor for the provider.
	 *
	 * @since 1.1.0
	 * @param string $base_auth_path Base authentication path.
	 */
	public function __construct( $base_auth_path ) {

		$this->id                = 'instagram';
		$this->title             = __( 'Instagram', 'woocommerce-social-login' );
		$this->color             = '#e4405f';
		$this->internal_callback = 'int_callback';
		$this->require_ssl       = false;
		$this->ping_url          = 'https://api.instagram.com';

		parent::__construct( $base_auth_path );
	}


	/**
	 * Get the description, overridden to display the callback URL
	 * as a convenience since Instagram requires the admin to enter it for the app
	 *
	 * @since 1.1.0
	 * @see \WC_Social_Login_Provider::get_description()
	 * @return string
	 */
	public function get_description() {

		/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
		$description = sprintf( __( 'Need help setting up and configuring Instagram? %1$sRead the docs%2$s', 'woocommerce-social-login' ), '<a href="' . $this->get_documentation_url() . '">', '</a>' );

		$callback_url_format = $this->get_callback_url_format();

		/* translators: Placeholder: %s - a url */
		$description .= '<br/><br/>' . sprintf( __( 'The OAuth redirect_uri is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url() . '</code>' );

		if ( 'legacy' === $callback_url_format ) {

			$description .= ' <strong>' . __( '(Please update your Instagram app to use this URL)', 'woocommerce-social-login' ) . '</strong>';

			/* translators: Placeholder: %s - a url */
			$description .= '<br/><br/>' . sprintf( __( 'The legacy OAuth redirect_uri is %s', 'woocommerce-social-login' ), '<code>' . $this->get_callback_url( $callback_url_format ) . '</code>' );
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
				'path'  => wc_social_login()->get_plugin_path() . '/includes/hybridauth/class-sv-hybrid-providers-instagram.php',
				'class' => 'SV_Hybrid_Providers_Instagram',
			),
		) );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.1.0
	 * @see \WC_Social_Login_Provider::get_default_login_button_text()
	 * @return string
	 */
	public function get_default_login_button_text() {
		return __( 'Log in with Instagram', 'woocommerce-social-login' );
	}


	/**
	 * Return the default login button text
	 *
	 * @since 1.1.0
	 * @see \WC_Social_Login_Provider::get_default_login_button_text()
	 * @return string
	 */
	public function get_default_link_button_text() {
		return __( 'Link your account to Instagram', 'woocommerce-social-login' );
	}


	/**
	 * Get notices.
	 *
	 * @since 2.0.4
	 * @return array
	 */
	public function get_notices() {
		return array(
			'account_linked'         => __( 'Your Instagram account is now linked to your account.', 'woocommerce-social-login' ),
			'account_unlinked'       => __( 'Instagram was successfully unlinked from your account.', 'woocommerce-social-login' ),
			'account_already_linked' => __( 'This Instagram account is already linked to another user account.', 'woocommerce-social-login' ),
			'account_already_exists' => __( 'A user account using the same email address as this Instagram account already exists.', 'woocommerce-social-login' ),
		);
	}


	/**
	 * Adds admin options to the Instagram settings page.
	 *
	 * Also produces a notice warning existing users about imminent Instagram API deprecation.
	 *
	 * @since 2.8.4
	 */
	public function admin_options() {

		?>
		<div class="notice error">
			<p>
				<?php

				printf(
					/* translators: Placeholders: %1$s - Social Login, %2$s - Instagram provider name, %3$s - opening <a> HTML link tag, %4$s - closing </a> HTML link tag */
					esc_html__( '%1$s: Please note that %2$s will deprecate support for social logins in early 2020. %3$sClick here to read more about this update%4$s.', 'woocommerce-social-login' ),
					wc_social_login()->get_plugin_name(),
					'<strong>' . $this->get_title() . '</strong>',
					'<a href="https://docs.woocommerce.com/document/woocommerce-social-login/#faq-instagram" target="_blank">',
					'</a>'
				);

				?>
			</p>
		</div>
		<?php

		parent::admin_options();
	}


}
