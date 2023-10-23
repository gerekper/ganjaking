<?php
/**
 * Document data for credit note template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/document-data-credit-note.php
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$refund_order    = $document->order;
$invoice_details = new YITH_Invoice_Details( $document );
$refund_order_id = $refund_order->get_id();
$main_order_id   = get_post_field( 'post_parent', $refund_order_id );
$main_order      = wc_get_order( $main_order_id );


$negative_value = strval( get_option( 'ywpi_credit_note_positive_values', 'no' ) ) === 'yes' ? '-1' : '1';

$refund_total = $refund_order->get_total() * $negative_value;

?>

<div class="invoice-data-content">
	<table>
		<tr class="ywpi-invoice-number">
			<td class="ywpi-invoice-number-title" colspan="2" >
				<span class="ywpi-invoice-number">
					<?php
					/**
					 * APPLY_FILTERS: ywpi_credit_note_number_label
					 *
					 * Filter the credit note number label.
					 *
					 * @param string the label.
					 * @param object $document the document object.
					 *
					 * @return string
					 */
					echo esc_html( apply_filters( 'ywpi_credit_note_number_label', __( 'Credit Note No.', 'yith-woocommerce-pdf-invoice' ), $document ) );
					?>
				</span>
				<span class="ywpi-invoice-number-formatted">
					<?php echo esc_html( $document->get_formatted_document_number() ); ?>
				</span>
			</td>
		</tr>

		<?php if ( 'yes' === ywpi_get_option( 'ywpi_show_order_number', $document, 'yes' ) ) : ?>
			<tr class="ywpi-order-number">
				<td class="left-content">
					<?php esc_html_e( 'Order No.', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>
				<td class="right-content">
					<?php echo esc_html( $main_order->get_order_number() ); ?>
					<?php
					/**
					 * DO_ACTION: yith_ywpi_template_order_number
					 *
					 * Section after the display of the order number in the template.
					 *
					 * @param object $document the document object
					 */
					do_action( 'yith_ywpi_template_order_number', $document );
					?>
				</td>
			</tr>
		<?php endif ?>

		<?php if ( 'yes' === ywpi_get_option( 'ywpi_show_order_date', $document, 'yes' ) ) : ?>
			<tr class="ywpi-invoice-date">
				<td class="left-content">
					<?php esc_html_e( 'Date:', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>
				<td class="right-content">
					<?php
					/**
					 * APPLY_FILTERS: ywpi_template_invoice_data_table_invoice_date
					 *
					 * Filter the date displayed in the document data.
					 *
					 * @param string the date.
					 * @param object $document the document object.
					 *
					 * @return string
					 */
					echo esc_html( apply_filters( 'ywpi_template_invoice_data_table_invoice_date', $document->get_formatted_document_date(), $document ) );
					?>
				</td>
			</tr>
		<?php endif ?>

		<?php
		/**
		 * APPLY_FILTERS: ywpi_template_invoice_data_table_order_amount_visible
		 *
		 * Filter the condition to display the order amount in the document data.
		 *
		 * @param bool true to display it, false to not.
		 *
		 * @return bool
		 */
		if ( 'yes' === ywpi_get_option( 'ywpi_show_order_amount', $document, 'yes' ) && apply_filters( 'ywpi_template_invoice_data_table_order_amount_visible', true ) ) :
			?>
			<tr class="invoice-amount">
				<td class="left-content">
					<?php esc_html_e( 'Refunded amount:', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>
				<td class="right-content">
					<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $refund_total ) ); ?>
				</td>
			</tr>
			<?php
		endif;

		/** PAYMENT METHOD */

		if ( 'yes' === ywpi_get_option( 'ywpi_show_order_payment_method', $document, 'yes' ) ) :
			if ( WC()->payment_gateways() ) {
				$payment_gateways = WC()->payment_gateways->payment_gateways();
			} else {
				$payment_gateways = array();
			}

			$payment_method = $main_order->get_payment_method();

			if ( $payment_method && 'other' !== $payment_method ) :
				?>
				<tr class="ywpi-invoice-payment-method">
					<td class="left-content">
						<?php esc_html_e( 'Payment method:', 'yith-woocommerce-pdf-invoice' ); ?>
					</td>
					<td class="right-content">
						<?php echo esc_html( isset( $payment_gateways[ $payment_method ] ) ? $payment_gateways[ $payment_method ]->get_title() : $payment_method ); ?>
					</td>
				</tr>
				<?php
				endif;
			endif;
			/** END PAYMENT METHOD */
		?>
	</table>
</div>
