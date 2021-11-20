<?php
/**
 * WooCommerce Order Status Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @package     WC-Order-Status-Manager
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Integrations handler for third party extensions and plugins compatibility.
 *
 * Adds integrations for:
 *
 * - WooCommerce Subscriptions
 *
 * @since 1.13.3
 */
class WC_Order_Status_Manager_Integrations {


	/** @var \SkyVerge\WooCommerce\Order_Status_Manager\Integration\Subscriptions|null */
	private $subscriptions;


	/**
	 * Loads integrations.
	 *
	 * @since 1.13.3
	 */
	public function __construct() {

		// Subscriptions
		if ( wc_order_status_manager()->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {

			require_once( wc_order_status_manager()->get_plugin_path() . '/src/integrations/woocommerce-subscriptions/class-wc-order-status-manager-integration-subscriptions.php' );

			$this->subscriptions = new \SkyVerge\WooCommerce\Order_Status_Manager\Integration\Subscriptions();
		}
	}


	/**
	 * Gets the Subscriptions' integration handler instance.
	 *
	 * @since 1.3.3
	 *
	 * @return \SkyVerge\WooCommerce\Order_Status_Manager\Integration\Subscriptions|null
	 */
	public function get_subscriptions_instance() {

		return $this->subscriptions;
	}


}
