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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * FreshBooks Frontend class.
 *
 * Handles general frontend tasks.
 *
 * @since 3.8.0
 */
class WC_FreshBooks_Frontend {


	/**
	 * Adds various admin hooks/filters.
	 *
	 * @since 3.8.0
	 */
	public function __construct() {

		// add My Account actions
		add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'my_orders_actions' ), 10, 2 );

		// add inline JS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_inline_js' ) );
	}


	/**
	 * Adds a customer view invoice link to the My Orders quick actions.
	 *
	 * @internal
	 *
	 * @since 3.8.0
	 *
	 * @param array $actions Associative array of actions
	 * @param \WC_Order $order The order object
	 * @return array The filtered array of actions
	 */
	public function my_orders_actions( $actions, $order ) {

		if ( 'yes' === get_option( 'wc_freshbooks_display_view_invoice_my_account', 'yes' ) ) {

			$invoice = $order->get_meta( '_wc_freshbooks_invoice', true );

			if ( isset( $invoice['client_view_url'] ) ) {

				$actions['wc_freshbooks_view_invoice'] = array(
					'url'  => $invoice['client_view_url'],
					'name' => __( 'View Invoice', 'woocommerce-freshbooks' )
				);
			}
		}

		return $actions;
	}


	/**
	 * Adds inline script to the My Account page.
	 *
	 * @internal
	 *
	 * @since 3.8.0
	 */
	public function enqueue_inline_js() {

		if ( is_account_page() ) {

			// ensure that Freshbooks invoices are opened in a new tab
			wc_enqueue_js( "$( '.wc_freshbooks_view_invoice' ).attr( 'target', '_blank' );" );
		}
	}


}
