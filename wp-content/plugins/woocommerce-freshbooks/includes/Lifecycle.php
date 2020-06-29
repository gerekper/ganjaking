<?php
/**
 * WooCommerce FreshBooks
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce FreshBooks to newer
 * versions in the future. If you wish to customize WooCommerce FreshBooks for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-freshbooks/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\FreshBooks;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 3.12.0
 *
 * @method \WC_FreshBooks get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 3.12.1
	 *
	 * @param \WC_FreshBooks $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'3.0',
			'3.8.0',
		];
	}


	/**
	 * Performs installation tasks.
	 *
	 * @since 3.12.0
	 */
	protected function install() {

		$settings = $this->get_plugin()->get_settings_instance();

		if ( ! $settings instanceof \WC_FreshBooks_Settings ) {

			// include settings so we can install defaults
			require_once( WC()->plugin_path() . '/includes/admin/settings/class-wc-settings-page.php' );

			$settings = $this->get_plugin()->load_class( '/includes/admin/class-wc-freshbooks-settings.php', 'WC_FreshBooks_Settings' );
		}

		foreach ( $settings->get_settings() as $setting ) {

			if ( isset( $setting['id'], $setting['default'] ) ) {

				update_option( $setting['id'], $setting['default'] );
			}
		}

		// versions prior to 3.0 did not set a version option, so the upgrade method needs to be called manually
		if ( get_option( 'wc_fb_api_url' ) ) {

			$this->upgrade( '2.1.3' );
		}
	}


	/**
	 * Updates to version 3.0
	 *
	 * @since 3.12.1
	 */
	protected function upgrade_to_3_0() {

		// API URL / token
		update_option( 'wc_freshbooks_api_url',              get_option( 'wc_fb_api_url' ) );
		update_option( 'wc_freshbooks_authentication_token', get_option( 'wc_fb_api_token' ) );

		// invoice send method
		update_option( 'wc_freshbooks_invoice_sending_method', ( 'SnailMail' === get_option( 'wc_fb_send_method' ) ? 'snail_mail' : 'email' ) );

		// use order number as invoice number
		update_option( 'wc_freshbooks_use_order_number', ( get_option( 'wc_fb_use_order_number' ) ? 'yes' : 'no' ) );

		// invoice number prefix
		update_option( 'wc_freshbooks_invoice_number_prefix', get_option( 'wc_fb_inv_num_prefix' ) );

		// auto-send invoice
		update_option( 'wc_freshbooks_auto_send_invoices', ( get_option( 'wc_fb_send_invoice' ) ? 'yes' : 'no' ) );

		// auto-apply payments
		update_option( 'wc_freshbooks_auto_apply_payments', ( get_option( 'wc_fb_add_payments' ) ? 'yes' : 'no' ) );

		// mark as migrated
		update_option( 'wc_freshbooks_upgraded_from_v2', 1 );

		// remove old options
		$old_options = [
			'wc_fb_api_url',
			'wc_fb_api_token',
			'wc_fb_create_client',
			'wc_fb_generic_client',
			'wc_fb_add_payments',
			'wc_fb_send_invoice',
			'wc_fb_send_method',
			'wc_fb_use_order_number',
			'wc_fb_inv_num_prefix'
		];

		foreach ( $old_options as $option ) {
			delete_option( $option );
		}
	}


	/**
	 * Updates to version 3.8.0
	 *
	 * @since 3.12.1
	 */
	protected function upgrade_to_3_8_0() {

		if ( 'none' !== get_option( 'wc_freshbooks_default_client' ) ) {
			// we shouldn't display the "View Invoice" order action by default when a site is using a default FreshBooks client because FreshBooks automatically logs in when the client view link is visited
			add_option( 'wc_freshbooks_display_view_invoice_my_account', 'no' );
		}
	}


}
