<?php
/**
 * Document data for credit note template.
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-details.php
 *
 * @author  YITH
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$refund_order    = $document->order;
$invoice_details = new YITH_Invoice_Details( $document );
$refund_order_id = yit_get_prop( $refund_order, 'id' );
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
					<?php echo esc_html( apply_filters( 'ywpi_credit_note_number_label', __( 'Credit Note No.', 'yith-woocommerce-pdf-invoice' ), $document ) ); ?>
				</span>
				<span class="ywpi-invoice-number-formatted">
					<?php echo esc_html( $document->get_formatted_document_number() ); ?>
				</span>
			</td>
		</tr>

		<tr class="ywpi-order-number">
			<td class="left-content">
				<?php esc_html_e( 'Order No.', 'yith-woocommerce-pdf-invoice' ); ?>
			</td>
			<td class="right-content">
				<?php echo esc_html( $main_order->get_order_number() ); ?>
				<?php do_action( 'yith_ywpi_template_order_number', $document ); ?>
			</td>
		</tr>

		<tr class="ywpi-invoice-date">
			<td class="left-content">
				<?php esc_html_e( 'Date:', 'yith-woocommerce-pdf-invoice' ); ?>
			</td>
			<td class="right-content">
				<?php echo esc_html( apply_filters( 'ywpi_template_invoice_data_table_invoice_date', $document->get_formatted_document_date(), $document ) ); ?>
			</td>
		</tr>

		<?php if ( apply_filters( 'ywpi_template_invoice_data_table_order_amount_visible', true ) ) : ?>
			<tr class="invoice-amount">
				<td class="left-content">
					<?php esc_html_e( 'Refunded amount:', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>
				<td class="right-content">
					<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $refund_total ) ); ?>
				</td>
			</tr>
		<?php endif; ?>
	</table>
</div>
