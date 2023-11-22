<?php
/**
 * WooCommerce Google Analytics Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro;

defined( 'ABSPATH' ) or exit;

use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;
use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.6.0
 *
 * @method Plugin get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 1.7.1
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.3.0',
			'1.5.2',
			'1.8.6',
			'1.11.0',
			'2.0.0',
			'2.0.10',
		];
	}


	/**
	 * Performs any install tasks.
	 *
	 * @since 2.0.10
	 */
	protected function install() {

		$integration = $this->get_plugin()->get_integration_instance();
		$settings    = $integration->settings;

		// ensure a default value is set for revenue tracking - based on WC price tax setting
		$settings['include_tax_and_shipping_in_revenue'] = get_option( 'woocommerce_prices_include_tax', 'no' );

		update_option( 'woocommerce_google_analytics_pro_settings', $settings );

		// ensure that settings are reloaded after the install
		$integration->init_settings();
	}


	/**
	 * Updates to 1.3.0
	 *
	 * @since 1.7.1
	 */
	protected function upgrade_to_1_3_0(): void {

		$settings = get_option( 'woocommerce_google_analytics_pro_settings', [] );

		// pre 1.3.0 `__gaTracker` was the default function name - store an option & setting for it, so we don't break compatibility
		add_option( 'woocommerce_google_analytics_upgraded_from_gatracker', true );

		$settings['function_name'] = '__gaTracker';

		// convert profile > property
		if ( ! empty( $settings['profile'] ) ) {

			$parts = explode( '|', $settings['profile'] );

			$settings['property'] = $parts[0] . '|' . $parts[1];

			unset( $settings['profile'] );
		}

		// install default event names for new events
		$new_events = [
			'provided_billing_email',
			'selected_payment_method',
			'placed_order',
		];

		$form_fields = $this->get_plugin()->get_integration()->get_form_fields();

		foreach ( $new_events as $event ) {

			$settings[ "{$event}_event_name" ] = $form_fields[ "{$event}_event_name" ]['default'];
		}

		update_option( 'woocommerce_google_analytics_pro_settings', $settings );

		delete_transient( 'wc_google_analytics_pro_profiles' );

		// ensure that settings are reloaded after the upgrade
		$this->get_plugin()->get_integration_instance()->init_settings();
	}


	/**
	 * Updates to version 1.5.2
	 *
	 * @since 1.7.1
	 */
	protected function upgrade_to_1_5_2(): void {

		// in v1.5.0 some Subscriptions events were introduced but their default values were not saved in settings
		$saved_settings       = get_option( 'woocommerce_google_analytics_pro_settings', [] );
		$modified_settings    = false;
		$subscriptions_events = [
			'activated_subscription'           => 'activated subscription',
			'subscription_trial_ended'         => 'subscription trial ended',
			'subscription_end_of_prepaid_term' => 'subscription prepaid term ended',
			'subscription_expired'             => 'subscription expired',
			'suspended_subscription'           => 'suspended subscription',
			'reactivated_subscription'         => 'reactivated subscription',
			'cancelled_subscription'           => 'cancelled subscription',
			'renewed_subscription'             => 'subscription billed',
		];

		foreach ( $subscriptions_events as $setting => $default_value ) {

			$setting = "{$setting}_event_name";

			// only set the value if it wasn't saved before
			if ( ! isset( $saved_settings[ $setting ] ) || ! is_string( $saved_settings[ $setting ] ) ) {

				$saved_settings[ $setting ] = $default_value;

				$modified_settings = true;
			}
		}

		if ( $modified_settings ) {

			update_option( 'woocommerce_google_analytics_pro_settings', $saved_settings );

			// ensure that settings are reloaded after the upgrade
			$this->get_plugin()->get_integration_instance()->init_settings();
		}
	}


	/**
	 * Updates to 1.8.6.
	 *
	 * @since 1.8.6
	 */
	protected function upgrade_to_1_8_6(): void {

		$settings = get_option( 'woocommerce_google_analytics_pro_settings', [] );

		// The Enable Google Analytics tracking option was ignored before 1.8.6, so
		// tracking was always on. Here we set the option to 'yes' for existing users
		// to maintain current behavior now that we respect the option.
		if ( isset( $settings['enabled'] ) && 'no' === $settings['enabled'] ) {

			$settings['enabled'] = 'yes';

			update_option( 'woocommerce_google_analytics_pro_settings', $settings );
		}
	}


	/**
	 * Updates to 1.11.0.
	 *
	 * @since 1.11.0
	 */
	protected function upgrade_to_1_11_0(): void {

		$settings = get_option( 'woocommerce_google_analytics_pro_settings', [] );

		$debug_mode = isset( $settings['debug_mode'] ) ? wc_strtolower( $settings['debug_mode'] ) : 'off';

		// migrate Debug mode dropdown (on/off) to checkbox (yes/no)
		if ( 'on' === $debug_mode || 'off' === $debug_mode ) {

			$settings['debug_mode'] = 'on' === $debug_mode ? 'yes' : 'no';

			update_option( 'woocommerce_google_analytics_pro_settings', $settings );
		}
	}


	/**
	 * Updates to 2.0.0
	 *
	 * @since 2.0.0
	 */
	protected function upgrade_to_2_0_0(): void {
		global $wpdb;

		$settings = get_option( 'woocommerce_google_analytics_pro_settings', [] );

		// store default GA4 event names in settings
		foreach( $this->get_plugin()->get_tracking_instance()->get_event_tracking_instance()->get_events() as $event ) {

			if ( ! $event instanceof GA4_Event ) {
				continue;
			}

			$settings[ $event::ID . '_event_name' ] = $event->get_default_name();
		}

		// rename some settings
		$settings['track_item_list_views_on'] = $settings['track_product_impressions_on'];

		unset( $settings['track_product_impressions_on'] );

		update_option( 'woocommerce_google_analytics_pro_settings', $settings );

		if ( Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled() ) {
			$meta_table   = OrdersTableDataStore::get_meta_table_name();
			$order_id_col = 'order_id';
		} else {
			$meta_table   = $wpdb->postmeta;
			$order_id_col = 'post_id';
		}

		// Copy `_wc_google_analytics_pro_tracked` meta value to `_wc_google_analytics_pro_tracked_in_ua` for each tracked order.
		// This helps ensure orders that were already tracked in UA before the upgrade do not get tracked in GA4.
		$wpdb->query( "
			INSERT INTO {$meta_table} ({$order_id_col}, meta_key, meta_value)
			SELECT DISTINCT {$order_id_col}, '_wc_google_analytics_pro_tracked_in_ua', meta_value
			FROM {$meta_table}
			WHERE meta_key = '_wc_google_analytics_pro_tracked';
		" );

		// ensure that settings are reloaded after the upgrade
		$this->get_plugin()->get_integration_instance()->init_settings();
	}


	/**
	 * Updates to 2.0.10
	 *
	 * @since 2.0.10
	 */
	protected function upgrade_to_2_0_10() : void {

		$settings = get_option( 'woocommerce_google_analytics_pro_settings', [] );

		// ensure a default value is set for revenue tracking
		$settings['include_tax_and_shipping_in_revenue'] = 'no';

		update_option( 'woocommerce_google_analytics_pro_settings', $settings );
	}


}
