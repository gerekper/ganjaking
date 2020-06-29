<?php
/**
 * WooCommerce Intuit Payments
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit Payments to newer
 * versions in the future. If you wish to customize WooCommerce Intuit Payments for your
 * needs please refer to http://docs.woothemes.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-Payments/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Intuit;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

/**
 * The Intuit plugin lifecycle handler.
 *
 * @since 2.3.3
 *
 * @method \WC_Intuit_Payments get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @param \WC_Intuit_Payments $plugin
	 */
	public function __construct( \WC_Intuit_Payments $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'2.0.0',
			'2.1.0',
			'2.3.0',
			'2.4.0',
			'2.6.0',
		];

		// TODO: Remove legacy options by version 3.0 or August 2020 {JB 2019-08-27}
		// wc_intuit_payments_access_token
		// wc_intuit_payments_refresh_token
		// wc_intuit_payments_access_token_expiry
		// wc_intuit_payments_sandbox_access_token
		// wc_intuit_payments_sandbox_refresh_token
		// wc_intuit_payments_sandbox_access_token_expiry
		// wc_intuit_payments_credit_card_oauth_token
		// wc_intuit_payments_credit_card_sandbox_oauth_token
		// wc_intuit_payments_credit_card_oauth_token_secret
		// wc_intuit_payments_credit_card_sandbox_oauth_token_secret
		// wc_intuit_payments_credit_card_oauth_token_expiry
		// wc_intuit_payments_credit_card_sandbox_oauth_token_expiry
		// wc_intuit_payments_echeck_oauth_token
		// wc_intuit_payments_echeck_sandbox_oauth_token
		// wc_intuit_payments_echeck_oauth_token_secret
		// wc_intuit_payments_echeck_sandbox_oauth_token_secret
		// wc_intuit_payments_echeck_oauth_token_expiry
		// wc_intuit_payments_echeck_sandbox_oauth_token_expiry
	}


	/**
	 * Handles installation tasks.
	 *
	 * @since 2.3.3
	 */
	protected function install() {

		// handle upgrades from pre v2.0.0 versions, as the plugin ID changed then
		// and the upgrade routine won't be triggered automatically
		if ( $old_version = get_option( 'wc_intuit_qbms_version' ) ) {

			$this->upgrade( $old_version );

		} else {

			update_option( 'wc_intuit_payments_active_integration', \WC_Intuit_Payments::PLUGIN_ID );

			set_transient( 'wc_intuit_payments_setup_wizard_redirect', 1, 30 );
		}
	}


	/**
	 * Upgrades the plugin to v2.0.0.
	 *
	 * @since 2.4.0
	 */
	protected function upgrade_to_2_0_0() {
		global $wpdb;

		/** Update order payment method meta ******************************/

		$this->get_plugin()->log( 'Starting order meta upgrade.' );

		// meta key: _payment_method
		// old value: intuit_qbms
		// new value: intuit_qbms_credit_card
		$rows = $wpdb->update( $wpdb->postmeta, [ 'meta_value' => 'intuit_qbms_credit_card' ], [ 'meta_key' => '_payment_method', 'meta_value' => 'intuit_qbms' ] );

		$this->get_plugin()->log( sprintf( '%d orders updated for payment method meta', $rows ) );

		// meta key: _recurring_payment_method
		// old value: intuit_qbms
		// new value: intuit_qbms_credit_card
		$rows = $wpdb->update( $wpdb->postmeta, [ 'meta_value' => 'intuit_qbms_credit_card' ], [ 'meta_key' => '_recurring_payment_method', 'meta_value' => 'intuit_qbms' ] );

		$this->get_plugin()->log( sprintf( '%d orders updated for recurring payment method meta', $rows ) );

		$order_meta_keys = [
			'trans_id',
			'capture_trans_id',
			'trans_date',
			'txn_authorization_stamp',
			'payment_grouping_code',
			'recon_batch_id',
			'merchant_account_number',
			'client_trans_id',
			'capture_client_trans_id',
			'card_type',
			'card_expiry_date',
			'charge_captured',
			'authorization_code',
			'capture_authorization_code',
			'account_four',
			'payment_token',
			'customer_id',
			'environment',
			'retry_count',
		];

		foreach ( $order_meta_keys as $key ) {

			// old key: _wc_intuit_qbms_*
			// new key: _wc_intuit_qbms_credit_card_*
			$wpdb->update( $wpdb->postmeta, [ 'meta_key' => '_wc_intuit_qbms_credit_card_' . $key ], [ 'meta_key' => '_wc_intuit_qbms_' . $key ] );
		}

		/** Update user token method meta *********************************/

		$this->get_plugin()->log( 'Starting legacy token upgrade.' );

		// old key: _wc_intuit_qbms_payment_tokens_test
		// new key: _wc_intuit_qbms_credit_card_payment_tokens_test
		$wpdb->update( $wpdb->usermeta, [ 'meta_key' => '_wc_intuit_qbms_credit_card_payment_tokens_test' ], [ 'meta_key' => '_wc_intuit_qbms_payment_tokens_test' ] );

		// old key: _wc_intuit_qbms_payment_tokens
		// new key: _wc_intuit_qbms_credit_card_payment_tokens
		$wpdb->update( $wpdb->usermeta, [ 'meta_key' => '_wc_intuit_qbms_credit_card_payment_tokens' ], [ 'meta_key' => '_wc_intuit_qbms_payment_tokens' ] );

		/** Update the QBMS settings **************************************/

		if ( $settings = get_option( 'woocommerce_intuit_qbms_settings' ) ) {

			$this->get_plugin()->log( 'Starting legacy settings upgrade.' );

			// update switcher option
			update_option( 'wc_intuit_payments_active_integration', \WC_Intuit_Payments::QBMS_PLUGIN_ID );

			// store the settings under the new option name
			update_option( 'woocommerce_intuit_qbms_credit_card_settings', $settings );

			// remove the old option
			delete_option( 'woocommerce_intuit_qbms_settings' );
		}

		delete_option( 'wc_intuit_qbms_version' );

		$this->get_plugin()->log( 'Completed upgrade for v2.0.0' );
	}


	/**
	 * Upgrades the plugin to v2.3.0.
	 *
	 * @since 2.4.0
	 */
	protected function upgrade_to_2_3_0() {

		// all of the possible OAuth 1.0 credential option names
		$credential_options = [
			'wc_intuit_payments_credit_card_oauth_token',
			'wc_intuit_payments_credit_card_sandbox_oauth_token',
			'wc_intuit_payments_credit_card_oauth_token_secret',
			'wc_intuit_payments_credit_card_sandbox_oauth_token_secret',
			'wc_intuit_payments_echeck_oauth_token',
			'wc_intuit_payments_echeck_sandbox_oauth_token',
			'wc_intuit_payments_echeck_oauth_token_secret',
			'wc_intuit_payments_echeck_sandbox_oauth_token_secret',
		];

		foreach ( $credential_options as $option_name ) {

			if ( $value = get_option( $option_name, false ) ) {

				$value = $this->get_plugin()->decrypt_credential_legacy( $value );

				update_option( $option_name, $this->get_plugin()->encrypt_credential( $value ) );
			}
		}
	}


	/**
	 * Upgrades the plugin to v2.4.0.
	 *
	 * @since 2.4.0
	 */
	protected function upgrade_to_2_4_0() {

		// probably safe to say if QBMS is still being used, there isn't any OAuth info to upgrade
		if ( $this->get_plugin()->is_qbms_active() ) {
			return;
		}

		$this->migrate_gateway_oauth_settings( \WC_Intuit_Payments::CREDIT_CARD_ID );
		$this->migrate_gateway_oauth_settings( \WC_Intuit_Payments::ECHECK_ID );
	}


	/**
	 * Migrates OAuth settings for gateways.
	 *
	 * @since 2.4.0
	 *
	 * @param string $gateway_id the gateway ID
	 */
	private function migrate_gateway_oauth_settings( $gateway_id ) {

		/** @var \WC_Gateway_Inuit_Payments $gateway */
		$gateway = $this->get_plugin()->get_gateway( $gateway_id );

		if ( ! $gateway ) {
			return;
		}

		$gateway_options = get_option( 'woocommerce_' . $gateway->get_id() . '_settings', [] );

		// migrate OAuth 2 credentials to new option names and clear old values
		if ( ! empty( $gateway_options['oauth_version'] ) && \WC_Gateway_Inuit_Payments::OAUTH_VERSION_2 === $gateway_options['oauth_version'] ) {

			$gateway_options['client_id']             = isset( $gateway_options['consumer_key'] ) ? $gateway_options['consumer_key'] : '';
			$gateway_options['client_secret']         = isset( $gateway_options['consumer_secret'] ) ? $gateway_options['consumer_secret'] : '';
			$gateway_options['sandbox_client_id']     = isset( $gateway_options['sandbox_consumer_key'] ) ? $gateway_options['sandbox_consumer_key'] : '';
			$gateway_options['sandbox_client_secret'] = isset( $gateway_options['sandbox_consumer_secret'] ) ? $gateway_options['sandbox_consumer_secret'] : '';

			unset( $gateway_options['consumer_key'], $gateway_options['consumer_secret'], $gateway_options['sandbox_consumer_key'], $gateway_options['sandbox_consumer_secret'] );

			update_option( 'woocommerce_' . $gateway->get_id() . '_settings', $gateway_options );
		}

		// the gateway has already been loaded with old settings, so we need to update the gateway settings
		// in-place in order to display the correct settings on the first pageload after plugin upgrade
		$gateway->init_settings();
		$gateway->load_settings();

		if ( ! $gateway->inherit_settings() ) {

			// active oauth connection migration
			if ( $gateway->is_configured() ) {

				$environment               = $gateway->is_test_environment() ? '_sandbox' : '';
				$oauth_access_token        = get_option( 'wc_' . $gateway->get_plugin()->get_id() . $environment . '_access_token', '' );
				$oauth_refresh_token       = get_option( 'wc_' . $gateway->get_plugin()->get_id() . $environment . '_refresh_token', '' );
				$oauth_access_token_expiry = get_option( 'wc_' . $gateway->get_plugin()->get_id() . $environment . '_access_token_expiry', '' );

				if ( ! empty( $oauth_access_token ) && ! empty( $oauth_refresh_token ) && ! empty( $oauth_access_token_expiry ) ) {

					$gateway->get_connection_handler()->set_access_token( $oauth_access_token );
					$gateway->get_connection_handler()->set_refresh_token( $oauth_refresh_token );
					$gateway->get_connection_handler()->set_access_token_expiry( $oauth_access_token_expiry );
				}
			}
		}
	}


	/**
	 * Upgrades the plugin to v2.6.0.
	 *
	 * @since 2.4.0
	 */
	protected function upgrade_to_2_6_0() {

		// do nothing if still using QBMS
		if ( \WC_Intuit_Payments::PLUGIN_ID !== get_option( 'wc_intuit_payments_active_integration' ) ) {
			return;
		}

		$this->migrate_order_data();
	}


	/**
	 * Migrates order data from QBMS to Payments.
	 *
	 * @since 2.6.0
	 */
	public function migrate_order_data() {
		global $wpdb;

		$this->get_plugin()->log( 'Starting order data migration from QBMS.' );

		// meta key: _payment_method
		// old value: intuit_qbms_credit_card
		// new value: intuit_payments_credit_card
		$rows = $wpdb->update( $wpdb->postmeta, [ 'meta_value' => 'intuit_payments_credit_card' ], [ 'meta_key' => '_payment_method', 'meta_value' => 'intuit_qbms_credit_card' ] );

		$this->get_plugin()->log( sprintf( '%d orders updated for payment method meta', $rows ) );

		// meta key: _recurring_payment_method
		// old value: intuit_qbms_credit_card
		// new value: intuit_payments_credit_card
		$rows = $wpdb->update( $wpdb->postmeta, [ 'meta_value' => 'intuit_payments_credit_card' ], [ 'meta_key' => '_recurring_payment_method', 'meta_value' => 'intuit_qbms_credit_card' ] );

		$this->get_plugin()->log( sprintf( '%d orders updated for recurring payment method meta', $rows ) );
	}


}
