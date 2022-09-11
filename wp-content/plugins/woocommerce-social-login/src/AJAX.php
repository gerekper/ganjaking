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

namespace SkyVerge\WooCommerce\Social_Login;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * AJAX handler.
 *
 * @since 2.7.0
 */
class AJAX {


	/**
	 * Adds AJAX hooks.
	 *
	 * @since 2.7.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_wc_social_login_configure_provider', [ $this, 'configure_provider' ] );
	}


	/**
	 * Configures a provider via AJAX.
	 *
	 * @since 2.7.0
	 */
	public function configure_provider() {

		check_ajax_referer( 'wc-social-login-configure-provider', 'security' );

		if ( ! empty( $_REQUEST['provider'] ) ) {

			$provider  = wc_clean( $_REQUEST['provider'] );
			$providers = wc_social_login()->get_providers();

			if ( array_key_exists( $provider, $providers ) ) {

				update_option( 'wc_social_login_setup_wizard_default_provider', $provider );

				if ( isset( $_REQUEST['client_id'], $_REQUEST['client_secret'] ) ) {

					$settings_key        = "wc_social_login_{$provider}_settings";
					$settings            = get_option( $settings_key, [] );
					$settings['enabled'] = 'yes';
					$settings['id']      = wc_clean( $_REQUEST['client_id'] );
					$settings['secret']  = wc_clean( $_REQUEST['client_secret'] );

					update_option( $settings_key, $settings );

					// Reduce the chance of a race condition.
					// If the user receives an "ok" to connect (see below) too quickly before the settings are actually saved to db, the previous settings may be used instead.
					// This is because HybridAuth will use the stored settings and not what we pass here to JavaScript, so the new settings saved now must be made available via option.
					sleep( 1 );

					wp_send_json_success( [ $settings_key => $settings ] );
				}

				wp_send_json_error( printf( 'Could not parse credentials for %s', $provider ?: 'invalid provider' ) );
			}
		}

		wp_send_json_error( 'Unspecified provider' );
	}


}
