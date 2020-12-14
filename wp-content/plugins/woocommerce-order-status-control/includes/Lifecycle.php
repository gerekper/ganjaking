<?php
/**
 * WooCommerce Order Status Control
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net Accept Hosted Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net Accept Hosted Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-authorize-net-sim/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Order_Status_Control;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.10.0
 *
 * @method \WC_Order_Status_Control get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 1.10.1
	 *
	 * @param \WC_Order_Status_Control $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.7.0',
			'1.13.0',
		];
	}


	/**
	 * Install default settings.
	 *
	 * @since 1.10.0
	 *
	 * @see Framework\SV_WC_Plugin::install()
	 */
	protected function install() {

		$global_settings = $this->get_plugin()->get_global_settings();

		// install default settings, terms, etc
		foreach ( $global_settings as $setting ) {

			if ( isset( $setting['default'] ) ) {
				add_option( $setting['id'], $setting['default'] );
			}
		}
	}


	/**
	 * Upgrades to version 1.7.0
	 *
	 * @since 1.10.1
	 */
	protected function upgrade_to_1_7_0() {

		// before v1.7.0 setting "None" meant to use WC core's behaviour
		// and it was changed to not complete any orders to allow customers
		// who want the opposite use case to do it easily
		if ( 'none' === get_option( 'wc_order_status_control_auto_complete_orders' ) ) {

			update_option( 'wc_order_status_control_auto_complete_orders', 'virtual_downloadable' );
		}
	}


	/**
	 * Upgrades to version 1.13.0
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_13_0() {

		// for merchants updating, set a flag to not show the onboarding tips
		update_option( sprintf( 'wc_%s_onboarding_status', $this->get_plugin()->get_id() ), 'updated' );
	}


}
