<?php
/**
 * WooCommerce Authorize.Net Reporting
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net Reporting to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net Reporting for your
 * needs please refer to http://www.skyverge.com/contact/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Authorize_Net_Reporting;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_5 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.8.0
 *
 * @method \WC_Authorize_Net_Reporting get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Installs default settings.
	 *
	 * Attempts to copy over API login ID and transaction key from active Authorize.Net gateways.
	 *
	 * @since 1.8.0
	 */
	protected function install() {

		require_once( $this->get_plugin()->get_plugin_path() . '/src/admin/class-wc-authorize-net-reporting-admin.php' );

		// set default settings
		foreach ( \WC_Authorize_Net_Reporting_Admin::get_fields( 'settings' ) as $setting ) {

			if ( isset( $setting['id'], $setting['default'] ) ) {

				update_option( $setting['id'], $setting['default'] );
			}
		}

		$api_login_id = $api_transaction_key = null;

		// gateway option names that might house API credentials
		$gateway_options = array(
			'woocommerce_authorize_net_aim_settings',
			'woocommerce_authorize_net_aim_echeck_settings',
			'woocommerce_authorize_net_cim_credit_card_settings',
			'woocommerce_authorize_net_cim_echeck_settings',
			'woocommerce_authorize_net_cim_settings',
			'woocommerce_authorize_net_sim_credit_card_settings',
			'woocommerce_authorize_net_sim_echeck_settings',
		);

		// loop through the gateway options to try and find some credentials
		foreach ( $gateway_options as $option_name ) {

			if ( $settings = get_option( $option_name ) ) {

				// skip disabled the gateways
				if ( ! isset( $settings['enabled'] ) || 'yes' !== $settings['enabled'] ) {
					continue;
				}

				// try and get the credentials
				$api_login_id        = ! empty( $settings['api_login_id'] )        ? $settings['api_login_id']        : null;
				$api_transaction_key = ! empty( $settings['api_transaction_key'] ) ? $settings['api_transaction_key'] : null;

				// if credentials were found, our search is over
				if ( $api_login_id && $api_transaction_key ) {
					break;
				}
			}
		}

		// if no credentials were found yet, check some legacy settings
		if ( ! $api_login_id && ! $api_transaction_key ) {

			// SIM
			if ( $settings = get_option( 'woocommerce_authorize_net_sim_settings' ) ) {

				if ( isset( $settings['enabled'] ) && 'yes' === $settings['enabled'] ) {
					$api_login_id        = ! empty( $settings['apilogin'] ) ? $settings['apilogin'] : null;
					$api_transaction_key = ! empty( $settings['transkey'] ) ? $settings['transkey'] : null;
				}

			// AIM
			} elseif ( $settings = get_option( 'woocommerce_authorize_net_settings' ) ) {

				if ( isset( $settings['enabled'] ) && 'yes' === $settings['enabled'] ) {
					$api_login_id        = ! empty( $settings['apilogin'] ) ? $settings['apilogin'] : null;
					$api_transaction_key = ! empty( $settings['transkey'] ) ? $settings['transkey'] : null;
				}

			// DPM
			} elseif ( $settings = get_option( 'woocommerce_authorize_net_dpm_settings' ) ) {

				if ( isset( $settings['enabled'] ) && 'yes' === $settings['enabled'] ) {
					$api_login_id        = ! empty( $settings['api_login'] )       ? $settings['api_login']       : null;
					$api_transaction_key = ! empty( $settings['transaction_key'] ) ? $settings['transaction_key'] : null;
				}
			}
		}

		// finally, if credentials were found, set them for this plugin
		if ( $api_login_id && $api_transaction_key ) {

			update_option( 'wc_authorize_net_reporting_api_login_id',        $api_login_id );
			update_option( 'wc_authorize_net_reporting_api_transaction_key', $api_transaction_key );
			update_option( 'wc_authorize_net_reporting_api_environment',     'production' );

			// set option so a notice can be displayed on the export/settings page
			update_option( 'wc_authorize_net_reporting_api_copied', true );
		}
	}


	/**
	 * Performs any version-related changes.
	 *
	 * @since 1.8.0
	 *
	 * @param string $installed_version the currently installed version of the plugin
	 */
	protected function upgrade( $installed_version ) {

		if ( ! empty( $installed_version ) ) {

			$upgrade_path = array(
				'1.1.1' => 'update_to_1_1_1',
			);

			foreach ( $upgrade_path as $update_to_version => $upgrade_script ) {

				if ( version_compare( $installed_version, $update_to_version, '<' ) ) {

					$this->$upgrade_script();

					$this->get_plugin()->log( sprintf( 'Updated to version %s', $update_to_version ) );
				}
			}
		}
	}


	/**
	 * Updates to version 1.1.1
	 *
	 * @since 1.8.0
	 */
	private function update_to_1_1_1() {

		update_option( 'wc_authorize_net_reporting_debug_mode', 'off' );
	}


}
