<?php
/**
 * WooCommerce Print Invoices/Packing Lists
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * PIP Print class
 *
 * Handles printing tasks
 *
 * @since 3.0.0
 */
class WC_PIP_Print {


	/**
	 * Add print related actions
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		if ( ! is_admin() ) {
			add_action( 'wp', array( $this, 'print_document_action' ), 1 );
		} else {
			add_action( 'admin_init', array( $this, 'print_document_action' ), 1 );
		}
	}


	/**
	 * Document print window
	 *
	 * Renders the template inside a blank window for printing
	 *
	 * @since 3.0.0
	 */
	public function print_document_action() {

		// listen for 'print' action query string
		if ( isset( $_GET['wc_pip_document'], $_GET['wc_pip_action'] ) && 'print' === $_GET['wc_pip_action'] ) {

			// sanity check, if user is not logged in, prompt to log in
			if ( ! is_user_logged_in() ) {
				wp_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			$nonce = isset( $_REQUEST['_wpnonce'] ) ? $_REQUEST['_wpnonce'] : '';

			// security admin/frontend checks
			if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wc_pip_document' ) ) {
				die( __( 'You are not allowed to view this page.', 'woocommerce-pip' ) );
			}

			$can_admin = wc_pip()->get_handler_instance()->current_admin_user_can_manage_documents();

			// admin-only security checks
			if ( ! $can_admin && is_admin() ) {
				die( __( 'You are not allowed to view this page. You need to be an admin or be able to manage orders.', 'woocommerce-pip' ) );
			}

			// Get the requested document type and order(s).
			$type     = str_replace( '_', '-', $_GET['wc_pip_document'] );
			$order_id = isset( $_GET['order_id'] ) ? (int) $_GET['order_id'] : 0;

			// Get order IDs temporary option.
			$order_ids_hash = isset( $_GET['order_ids'] ) ? $_GET['order_ids'] : '';
			$order_ids      = empty( $order_ids_hash )    ? array()            : $this->get_print_order_ids( $order_ids_hash );

			// Since this is not a transient, we delete it manually.
			delete_option( "wc_pip_order_ids_{$order_ids_hash}" );

			if ( 0 === $order_id && isset( $order_ids[0] ) ) {
				$order = wc_get_order( $order_ids[0] );
			} else {
				$order = wc_get_order( $order_id );
			}

			if ( ! $order ) {

				// bail if no order
				die( __( 'The related order does not exist or is invalid.', 'woocommerce-pip' ) );

			} else {

				if ( ! $can_admin && ( (int) $order->get_user_id() !== (int) get_current_user_id() ) ) {
					// do not show the document if the user does not match unless is an admin or shop manager
					die( __( 'You are not allowed to view this page.', 'woocommerce-pip' ) );
				}
			}

			$document = wc_pip()->get_document( $type, array(
				'order'     => $order,
				'order_ids' => $order_ids,
			) );

			if ( ! $document ) {
				// last sanity check in case of $type errors
				die( __( 'The requested document could not be found or is of an invalid type.', 'woocommerce-pip' ) );
			}

			$document->print_document();
			exit;
		}
	}


	/**
	 * Gets IDs of orders to print from a temporary option in database.
	 *
	 * @since 3.6.2
	 *
	 * @param string $order_ids_hash hash to be matched to a database option name
	 * @return int[] array of order IDs to print
	 */
	private function get_print_order_ids( $order_ids_hash ) {
		global $wpdb;

		$option = $wpdb->get_row( $wpdb->prepare( "
 			SELECT option_value
 			FROM $wpdb->options
 			WHERE option_name = %s
 			LIMIT 1
 		", "wc_pip_order_ids_{$order_ids_hash}" ) );

		$order_ids = isset( $option->option_value ) && is_string( $option->option_value ) ? maybe_unserialize( $option->option_value ) : array();

		return array_map( 'absint', (array) $order_ids );
	}


}
