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
 * Handler class
 *
 * Handles general PIP tasks
 *
 * @since 3.0.0
 */
class WC_PIP_Handler {


	/**
	 * Add hooks/filters
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		$statuses = wc_get_is_paid_statuses();

		if ( ! empty( $statuses ) && is_array( $statuses ) ) {

			// Generate an invoice number when the order is paid.
			foreach ( $statuses as $paid_status ) {
				add_action( "woocommerce_order_status_{$paid_status}", array( $this, 'generate_invoice_number' ), 20 );
			}
		}
	}


	/**
	 * Whether to generate invoice numbers on order paid
	 *
	 * @since 3.1.1
	 * @return bool
	 */
	private function generate_invoice_number_on_order_paid() {

		/**
		 * Toggle invoice number generation upon paid order
		 *
		 * @since 3.0.3
		 * @param bool $generate_invoice_number Default true
		 */
		return (bool) apply_filters( 'wc_pip_generate_invoice_number_on_order_paid', true );
	}


	/**
	 * Get user capabilities allowed to print or email documents
	 *
	 * @since 3.0.5
	 * @return array
	 */
	public function get_admin_capabilities() {

		/**
		 * Filter lower capabilities allowed to manage documents
		 * i.e. print, email, from admin or front end
		 *
		 * @since 3.0.5
		 * @param array $capabilities
		 */
		$can_manage_documents = apply_filters( 'wc_pip_can_manage_documents', array(
			'manage_woocommerce',
			'manage_woocommerce_orders',
			'edit_shop_orders',
		) );

		return (array) $can_manage_documents;
	}


	/**
	 * Check if current user can print a document
	 *
	 * @since 3.0.5
	 * @return bool
	 */
	public function current_admin_user_can_manage_documents() {

		// admin can always manage
		$can_manage = is_user_admin();

		if ( ! $can_manage && $admin_caps = $this->get_admin_capabilities() ) {

			foreach ( $admin_caps as $capability ) {

				// stop as soon as there's at least one capability that grants management rights
				if ( $can_manage = current_user_can( $capability ) ) {
					break;
				}
			}
		}

		return $can_manage;
	}


	/**
	 * Check if customer can view invoices in front end
	 *
	 * @since 3.1.1
	 * @param int $user_id Optional, user id passed in filter, defaults to current user
	 * @return bool Will always return false if the current user (default) is not logged in
	 */
	public function customer_can_view_invoices( $user_id = null ) {

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		// sanity check, returns false for customers not logged in
		if ( 0 === (int) $user_id || ! is_numeric( $user_id ) ) {
			return false;
		}

		/**
		 * Toggle if customers can view invoices
		 *
		 * @since 3.0.5
		 * @param bool $customers_can_view_invoices Whether customers can see invoices (true), or not (false), default true
		 * @param int $user_id Optional user to check for, defaults to current user id
		 */
		return (bool) apply_filters( 'wc_pip_customers_can_view_invoices', true, (int) $user_id );
	}


	/**
	 * Generate a document invoice number.
	 *
	 * Normally runs as a callback upon order status change to paid
	 * It will not generate a new one if already set.
	 *
	 * @since 3.0.0
	 * @param int|\WC_Order $order Order ID (from callback) or object.
	 * @param null|bool|\WC_PIP_Document $document A PIP document, normally an invoice type. Not used in callback.
	 * @return false|string Returns the invoice number on success, false on failure (disregarded in callback).
	 */
	public function generate_invoice_number( $order, $document = null ) {

		$invoice_number = false;

		// If document is defined or true, will force invoice number generation.
		if ( $document || false !== $this->generate_invoice_number_on_order_paid() ) {

			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			if ( ! $document instanceof \WC_PIP_Document ) {
				$document = wc_pip()->get_document( 'invoice', array(
					'order'    => $order,
					'order_id' => $order->get_id(),
				) );
			}

			if ( $document instanceof \WC_PIP_Document && $order instanceof \WC_Order ) {

				// Sanity check: return the invoice number while on a callback.
				if ( ! $document->has_invoice_number() ) {

					$order_id = $order->get_id();
					// start by getting the latest invoice number from a counter
					$invoice_number = get_option( 'wc_pip_invoice_number_start', '1' );
					// store the raw number for bumping later the counter
					$invoice_number_raw = (int) $invoice_number;
					// are we using standard WC Order number or PIP own count?
					$use_order_number = 'yes' === get_option( 'wc_pip_use_order_number', 'yes' );

					if ( $use_order_number ) {

						// we use get_order_number() so plugins like Sequential Order Number Pro can filter this
						$invoice_number = $order->get_order_number();

					} else {

						// add leading zeros if option for minimum digits is set
						$leading_zeros = (int) get_option( 'wc_pip_invoice_minimum_digits', '1' );

						if ( $leading_zeros > 1 ) {
							$invoice_number = str_pad( (string) $invoice_number, $leading_zeros, '0', STR_PAD_LEFT );
						}
					}

					// add any optional suffix/prefix
					$invoice_number = get_option( 'wc_pip_invoice_number_prefix', '' ) . $invoice_number . get_option( 'wc_pip_invoice_number_suffix', '' );

					/**
					 * Filters the invoice number.
					 *
					 * @since 3.0.0
					 * @param string $invoice_number PIP Invoice number
					 * @param int $order_id WC_Order id
					 * @param string $type PIP Document type
					 */
					$invoice_number = apply_filters( 'wc_pip_invoice_number', wc_pip_parse_merge_tags( $invoice_number, $document->type ), $invoice_number, $order_id, $document->type );

					// recursive sanity check to prevent duplicate invoice numbers
					// due to possible concurrency issues (2 or more orders at the same time)
					if ( ! $use_order_number && $this->invoice_number_exists( $invoice_number ) ) {

						// perform an additional sanity check to maybe bump the counter
						// since there's the possibility that identical invoice numbers
						// may exists legitimately in some installations and this would
						// result in a infinite loop otherwise
						if ( $invoice_number_raw === (int) get_option( 'wc_pip_invoice_number_start', '1' ) ) {
							update_option( 'wc_pip_invoice_number_start', $invoice_number_raw + 1 );
						}

						$document->get_invoice_number();
					}

					// add the invoice number post meta
					$order->add_meta_data( '_pip_invoice_number', $invoice_number, true );
					$order->save_meta_data();

					// bump the internal invoice number counter unless the order number was used
					if ( ! $use_order_number ) {
						update_option( 'wc_pip_invoice_number_start', $invoice_number_raw + 1 );
					}

					/**
					 * Fires after an invoice number is created.
					 *
					 * This action is expected to be fired only when
					 * creating an invoice number for the first time
					 *
					 * @since 3.0.0
					 * @param string $invoice_number The invoice number created
					 * @param \WC_Order $order The order associated to the invoice
					 */
					do_action( 'wc_pip_invoice_number_created', $invoice_number, $order );

				} else {

					$invoice_number = $document->get_invoice_number();
				}
			}
		}

		return $invoice_number;
	}


	/**
	 * Check if a document invoice number exists
	 *
	 * TODO this method assumes `get_posts()` to retrieve an invoice number meta associated with a WC_Order, however since WC 3.0 the data store could be moved outside WP domain in the future and this check should account for that accordingly {FN 2017-03-22}
	 *
	 * @since 3.1.1
	 * @param string $invoice_number The invoice number to search
	 * @return bool
	 */
	public function invoice_number_exists( $invoice_number ) {

		$found = get_posts( array(
			'nopaging'    => true,
			'fields'      => 'ids',
			'post_type'   => 'shop_order',
			'post_status' => array_keys( wc_get_order_statuses() ),
			'meta_query'  => array(
				array(
					'key'     => '_pip_invoice_number',
					'value'   => $invoice_number,
				),
			),
		) );

		return ! empty( $found );
	}


}
