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
 * FreshBooks Order Class
 *
 * Extends the WooCommerce Order class to add additional information and
 * functionality specific to FreshBooks
 *
 * @since 3.0
 * @extends \WC_Order
 */
class WC_FreshBooks_Order extends \WC_Order {


	/** @var \stdClass invoice data */
	public $invoice;

	/** @var int invoice ID */
	public $invoice_id;

	/** @var int invoice client ID */
	public $invoice_client_id;

	/** @var string invoice status */
	public $invoice_status;

	/**
	 * Sets up the order normally and add invoice-specific class members.
	 *
	 * @since 3.0
	 *
	 * @param string|int $order_id
	 */
	public function __construct( $order_id ) {

		parent::__construct( $order_id );

		// easy access to invoice data
		$this->invoice      = new \stdClass();
		$freshbooks_invoice = $this->get_meta( '_wc_freshbooks_invoice' );

		if ( $freshbooks_invoice ) {
			foreach ( (array) $freshbooks_invoice as $key => $value ) {
				$this->invoice->$key = $value;
			}
		}
	}


	/**
	 * Gets the invoice status.
	 *
	 * Note this retrieves the status using the post meta stored instead of the invoice object, as it's guaranteed to be the most up-to-date status
	 *
	 * @since 3.0
	 *
	 * @return string invoice status, or null if the invoice hasn't been created yet
	 */
	public function get_invoice_status() {

		return $this->invoice_was_created() ? $this->get_meta( '_wc_freshbooks_invoice_status' ) : null;
	}


	/**
	 * Get the invoice status formatted for display
	 *
	 * @since 3.0
	 * @return string pretty invoice status
	 */
	public function get_invoice_status_for_display() {

		$status = $this->get_invoice_status();

		return $status ? ucwords( $status ) : '-';
	}


	/**
	 * Sends the invoice.
	 *
	 * @since 3.0
	 *
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function send_invoice() {

		// bail if the invoice was already sent
		if ( $this->invoice_was_sent() ) {
			return;
		}

		$sending_method = get_option( 'wc_freshbooks_invoice_sending_method', 'email' );
		$invoice_id     = $this->get_meta( '_wc_freshbooks_invoice_id' );

		$this->api()->send_invoice( $invoice_id, $sending_method );

		/* translators: Placeholders: %s - sending method */
		$this->add_order_note( sprintf( __( 'FreshBooks invoice sent via %s.', 'woocommerce-freshbooks' ), str_replace( '_', ' ', $sending_method ) ) );
	}


	/**
	 * Creates an invoice for the order.
	 *
	 * This process involves:
	 *
	 * + Finding or creating a client for the invoice
	 * + Sending the invoice (if enabled)
	 * + Saving the invoice data to order meta
	 *
	 * @since 3.0
	 *
	 * @param bool $auto_send automatically send the invoice upon completion, or false to create a draft invoice
	 * @return string|null created invoice ID or null if invoice creation failed
	 */
	public function create_invoice( $auto_send = true ) {

		if ( $this->invoice_was_created() ) {

			return $this->get_meta( '_wc_freshbooks_invoice_id' );
		}

		try {

			$this->invoice_client_id = $this->get_client();

			// save immediately in case invoice creation fails
			$this->update_meta_data( '_wc_freshbooks_client_id', $this->invoice_client_id );
			$this->save_meta_data();

			// create the invoice
			$invoice_id = $this->api()->create_invoice( $this );

			// save invoice/client ID
			$this->update_meta_data( '_wc_freshbooks_invoice_id', $invoice_id );
			$this->save_meta_data();

			// auto-send invoice?
			if ( $auto_send && ( 'yes' === get_option( 'wc_freshbooks_auto_send_invoices' ) ) ) {

				$this->send_invoice();
				$this->add_order_note( __( 'FreshBooks invoice created.', 'woocommerce-freshbooks' ) );

			} else {

				$this->add_order_note( __( 'FreshBooks draft invoice created.', 'woocommerce-freshbooks' ) );
			}

			// important to do this immediately as the webhook that also updates this could be slow or unreachable
			$this->refresh_invoice();

			return $invoice_id;

		} catch ( Framework\SV_WC_API_Exception $e ) {

			/* translators: Placeholders: %1$s - error code, %2$s - error message */
			wc_freshbooks()->log( sprintf( '%1$s - %2$s', $e->getCode(), $e->getMessage() ) );

			/* translators: Placeholders: %s - error message */
			$this->add_order_note( sprintf( __( 'Unable to create FreshBooks invoice (%s).', 'woocommerce-freshbooks' ), $e->getMessage() ) );
		}
	}


	/**
	 * Updates the invoice in FreshBooks from the current order.
	 *
	 * Useful if line items are added or removed.
	 *
	 * @since 3.2.0
	 */
	public function update_invoice_from_order() {

		if ( ! $this->invoice_was_created() ) {
			return;
		}

		try {

			// required info when updating
			$this->invoice_id        = $this->get_meta( '_wc_freshbooks_invoice_id' );
			$this->invoice_client_id = $this->get_meta( '_wc_freshbooks_client_id' );
			$this->invoice_status    = $this->get_invoice_status();

			$this->api()->update_invoice( $this );

			$this->refresh_invoice();

			$this->add_order_note( __( 'FreshBooks invoice updated.', 'woocommerce-freshbooks' ) );

		} catch ( Framework\SV_WC_API_Exception $e ) {

			/* translators: Placeholders: %1$s - error code, %2$s - error message */
			wc_freshbooks()->log( sprintf( '%1$s - %2$s', $e->getCode(), $e->getMessage() ) );

			/* translators: Placeholders: %s - error message */
			$this->add_order_note( sprintf( __( 'Unable to update FreshBooks invoice (%s).', 'woocommerce-freshbooks' ), $e->getMessage() ) );
		}
	}


	/**
	 * Refresh the invoice data saved to the order by getting the invoice data from FreshBooks.
	 *
	 * Used primarily after creating an invoice or when an invoice is updated in FreshBooks and should be reflected in WooCommerce.
	 *
	 * @since 3.0
	 *
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function refresh_invoice() {

		if ( ! $this->invoice_was_created() ) {
			return;
		}

		$invoice = $this->api()->get_invoice( $this->get_meta( '_wc_freshbooks_invoice_id' ) );

		$this->update_meta_data( '_wc_freshbooks_invoice', $invoice );
		$this->update_meta_data( '_wc_freshbooks_invoice_status', $invoice['status'] );
		$this->save_meta_data();
	}


	/**
	 * Gets the client ID for the order using this logic:
	 *
	 * 1) Use the default client option if set
	 * 2) For registered customers, check if a client ID is stored in user meta
	 * 3) Check if a client was previously created for this order and stored in order meta
	 * 4) Try to lookup the customer in FreshBooks using their billing email
	 * 5) Otherwise, create a new client
	 *
	 * Created client IDs are saved to user meta if the customer is logged in.
	 *
	 * @since 3.0
	 *
	 * @return string the FreshBooks client ID to create invoice under
	 * @throws Framework\SV_WC_API_Exception
	 */
	private function get_client() {

		// return default client if set
		if ( 'none' !== ( $client_id = get_option( 'wc_freshbooks_default_client' ) ) ) {

			return $client_id;
		}

		$user_id = $this->get_user_id();

		// for registered customer, check user meta
		if ( ! empty( $user_id ) && metadata_exists( 'user', $this->get_user_id(), '_wc_freshbooks_client_id' ) ) {

			return get_user_meta( $this->get_user_id(), '_wc_freshbooks_client_id', true );
		}

		// when invoice creation fails after the client has been created, the order
		// meta contains the created client ID
		$the_client_id = $this->get_meta( '_wc_freshbooks_client_id', true );

		if ( ! empty( $the_client_id ) ) {
			return $the_client_id;
		}

		$client_email = $this->get_billing_email( 'edit' );

		if ( ! empty( $client_email ) ) {

			// lookup in freshbooks by billing email
			$clients = $this->api()->get_clients_by_email( $client_email );

			// note that FreshBooks allows multiple clients with the same email, but
			// the first one is chosen here for simplicity
			if ( ! empty( $clients[0] ) ) {
				return $clients[0]['client_id'];
			}
		}

		// no client found, create one and return the ID
		$client_id = $this->api()->create_client( $this );

		// save to user meta if registered customer
		$customer_user = $this->get_user_id();

		if ( ! empty( $customer_user) ) {

			update_user_meta( $this->get_user_id(), '_wc_freshbooks_client_id', $client_id );
		}

		$this->add_order_note( __( 'New FreshBooks client created.', 'woocommerce-freshbooks' ) );

		return $client_id;
	}


	/**
	 * Applies the order total as a payment to the invoice.
	 *
	 * @since 3.0
	 *
	 * @return bool true if the payment was successful, false otherwise
	 */
	public function apply_invoice_payment() {

		if ( ! $this->invoice_needs_payment() ) {

			return true;
		}

		try {

			$payment_types = wc_freshbooks()->get_fb_payment_type_mapping();

			// set the invoice payment type
			$this->invoice_payment_type = isset( $payment_types[ $this->get_payment_method_id() ] ) ? $payment_types[ $this->get_payment_method_id() ] : '';

			$payment_id = $this->api()->create_payment( $this );

			$this->update_meta_data( '_wc_freshbooks_payment_id', $payment_id );

			// update status immediately as the payment webhook could be delayed
			$this->update_meta_data( '_wc_freshbooks_invoice_status', 'paid' );

			$this->save_meta_data();

			$this->add_order_note( __( 'FreshBooks payment applied.', 'woocommerce-freshbooks' ) );

			return true;

		} catch ( Framework\SV_WC_API_Exception $e ) {

			/* translators: Placeholders: %1$s - error code, %2$s - error message */
			wc_freshbooks()->log( sprintf( '%1$s - %2$s', $e->getCode(), $e->getMessage() ) );

			/* translators: Placeholders: %s - error message */
			$this->add_order_note( sprintf( __( 'FreshBooks payment could not be applied (%s).', 'woocommerce-freshbooks' ), $e->getMessage() ) );

			return false;
		}
	}


	/**
	 * Helper to get the payment method for an order.
	 *
	 * @since 3.10.0
	 *
	 * @return string the order payment method ID
	 */
	public function get_payment_method_id() {
		return $this->get_payment_method();
	}


	/**
	 * Gets the FreshBooks payment method type
	 *
	 * @since 3.10.0
	 * @return string payment method type
	 */
	public function get_invoice_payment_type() {
		return $this->invoice_payment_type;
	}


	/**
	 * Returns true if an invoice was created for this order.
	 *
	 * @since 3.0
	 *
	 * @return bool true if invoice was created, false otherwise
	 */
	public function invoice_was_created() {

		return metadata_exists( 'post', $this->get_id(), '_wc_freshbooks_invoice_id' );
	}


	/**
	 * Returns true if an invoice was sent for this order
	 *
	 * @since 3.0
	 * @return bool true if invoice was sent, false otherwise
	 */
	public function invoice_was_sent() {

		if ( ! $this->invoice_was_created() ) {
			return false;
		}

		return ( $this->get_invoice_status() && 'draft' !== $this->get_invoice_status() );
	}


	/**
	 * Returns true if an invoice needs payment
	 *
	 * @since 3.0
	 * @return bool true if invoice is not marked as paid, false otherwise
	 */
	public function invoice_needs_payment() {

		// orders without invoices cannot need payment
		if ( ! $this->invoice_was_created() ) {

			return false;
		}

		return ! in_array( $this->get_invoice_status(), array( 'paid', 'auto-paid', 'deleted' ) );
	}


	/**
	 * Helper method to improve the readability of methods calling the API.
	 *
	 * @since 3.0
	 *
	 * @return \WC_FreshBooks_API instance
	 * @throws Framework\SV_WC_API_Exception
	 */
	private function api() {

		return wc_freshbooks()->get_api();
	}


	/**
	 * Helper method to return valid FreshBooks invoice statuses
	 *
	 * @since 3.0
	 * @return array invoice statuses
	 */
	public static function get_invoice_statuses() {

		// note that 'deleted' is a custom status added by the plugin
		return array(
			'disputed',
			'draft',
			'sent',
			'viewed',
			'paid',
			'auto-paid',
			'retry',
			'deleted',
		);
	}


}
