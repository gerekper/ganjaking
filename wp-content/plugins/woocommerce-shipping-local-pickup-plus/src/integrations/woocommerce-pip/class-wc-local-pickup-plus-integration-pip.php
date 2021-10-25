<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_9 as Framework;

/**
 * WooCommerce PIP integration class.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Integration_PIP {


	/**
	 * Initialize PIP support.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// display pickup data after order items table
		add_action( 'wc_pip_after_body', array( $this, 'display_pickup_data' ), 5, 4 );
	}


	/**
	 * Output pickup data on documents.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $type \WC_PIP_Document type
	 * @param string $action document being printed or emailed
	 * @param \WC_PIP_Document $document Invoice, Packing List or Pick List
	 * @param \WC_Order $order the order the document is for
	 */
	public function display_pickup_data( $type, $action, $document, $order ) {

		if ( 'invoice' === $type || 'packing-list' === $type ) {

			$local_pickup   = wc_local_pickup_plus();
			$orders_handler = $local_pickup->get_orders_instance();

			if ( $orders_handler && ( $pickup_data = $orders_handler->get_order_pickup_data( $order ) ) ) {
				wc_get_template( 'pip/order-pickup-details.php', array(
					'document_type'   => $type,
					'document_action' => $action,
					'document'        => $document,
					'order'           => $order,
					'pickup_data'     => $pickup_data,
					'shipping_method' => $local_pickup->get_shipping_method_instance(),
				), '', $local_pickup->get_plugin_path() . '/templates/' );
			}
		}
	}


}
