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
 * Social Login Global Functions
 *
 * @version 2.6.0
 * @since 1.0.0
 */

if ( ! function_exists( 'woocommerce_social_login_buttons' ) ) :

/**
 * Pluggable function to render social login buttons
 *
 * @since 1.0.0
 * @param string $return_url Return url, defaults to the current url
 * @param bool $fallback_to_link_account Optional. Whether to display buttons to link account
 *                                       when user is logged in. If false, will not display any
 *                                       buttons for logged in users at all.
 */
function woocommerce_social_login_buttons( $return_url = null, $fallback_to_link_account = false ) {

	if ( is_user_logged_in() ) {

		if ( $fallback_to_link_account ) {
			woocommerce_social_login_link_account_buttons( $return_url );
		}

		return;
	}

	// If no return_url, use the current URL
	if ( ! $return_url ) {
		$return_url = home_url( add_query_arg( array() ) );
	}

	/**
	 * Filter the return URL
	 *
	 * @since 1.6.0
	 * @param string $return_url Return url, defaults to the current url
	 */
	$return_url = apply_filters( 'wc_social_login_buttons_return_url', $return_url );

	// Enqueue styles and scripts
	wc_social_login()->get_frontend_instance()->load_styles_scripts();

	if ( is_checkout() ) {

		$login_text = get_option( 'wc_social_login_text' );

	} else {

		$login_text = get_option( 'wc_social_login_text_non_checkout' );
	}

	// load the template
	wc_get_template(
		'global/social-login.php',
		array(
			'providers'  => wc_social_login()->get_available_providers(),
			'return_url' => $return_url,
			'login_text' => $login_text,
		),
		'',
		wc_social_login()->get_plugin_path() . '/templates/'
	);
}

endif;


if ( ! function_exists( 'woocommerce_social_login_link_account_buttons' ) ) :

	/**
	 * Pluggable function to render social login "link your account" buttons
	 *
	 * @since 1.1.0
	 * @param string $return_url Return url, defaults my account page
	 */
	function woocommerce_social_login_link_account_buttons( $return_url = null ) {

		if ( ! is_user_logged_in() ) {
			return;
		}

		// If no return_url, use the my account page
		if ( ! $return_url ) {
			$return_url = wc_get_page_permalink( 'myaccount' );
		}

		// Enqueue styles and scripts
		wc_social_login()->get_frontend_instance()->load_styles_scripts();

		$available_providers = array();

		// determine available providers for user
		foreach ( wc_social_login()->get_available_providers() as $provider ) {

			if ( ! get_user_meta( get_current_user_id(), '_wc_social_login_' . $provider->get_id() . '_profile', true ) ) {
				$available_providers[] = $provider;
			}
		}

		// load the template
		wc_get_template(
			'global/social-login-link-account.php',
			array(
				'available_providers'  => $available_providers,
				'return_url'           => $return_url,
			),
			'',
			wc_social_login()->get_plugin_path() . '/templates/'
		);
	}

endif;
