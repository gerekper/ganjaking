<?php /* @var YITH_Invoice $document */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$refund_order    = $document->order;
$invoice_details = new YITH_Invoice_Details( $document );
$refund_order_id = yit_get_prop( $refund_order, 'id' );
$main_order_id   = get_post_field( 'post_parent', $refund_order_id );
$main_order      = wc_get_order( $main_order_id ); ?>

<div class="invoice-data-content">
	<table>
		<tr class="ywpi-invoice-number">
			<td class="ywpi-invoice-number-title" colspan="2">
				<?php esc_html_e( "CREDIT NOTE NUMBER", 'yith-woocommerce-pdf-invoice' ); ?>
			</td>
		</tr>
		
		<tr class="ywpi-invoice-number">
			<td class="ywpi-invoice-number-title" colspan="2">
				<?php echo $document->formatted_number; ?>
			</td>
		</tr>
		
		<tr class="ywpi-order-number">
			<td class="left-content">
				<?php echo apply_filters('ywpi_invoice_number_label_for_credit_note', esc_html__( "Invoice number", 'yith-woocommerce-pdf-invoice' ),$document); ?>
			</td>
			<td class="right-content">
				<?php if ( $main_order ) {
					$invoice = new YITH_Invoice( $main_order_id );
					if ( $invoice->generated() ) {
						echo $invoice->get_formatted_document_number();
					}
				} ?>
			</td>
		</tr>
		
		<tr class="ywpi-order-number">
			<td class="left-content">
				<?php esc_html_e( "Order No.", 'yith-woocommerce-pdf-invoice' ); ?>
			</td>
			<td class="right-content">
				<?php echo $main_order->get_order_number(); ?>
				<?php do_action( 'yith_ywpi_template_order_number', $document ); ?>
			</td>
		</tr>
		
		<tr class="ywpi-invoice-date">
			<td class="left-content">
				<?php esc_html_e( "Date", 'yith-woocommerce-pdf-invoice' ); ?>
			</td>
			<td class="right-content">
				<?php echo apply_filters( 'ywpi_template_invoice_data_table_invoice_date', $document->get_formatted_document_date(), $document ); ?>
			</td>
		</tr>
		
		<?php if ( apply_filters( 'ywpi_template_invoice_data_table_order_amount_visible', true ) ) : ?>
			<tr class="invoice-amount">
				<td class="left-content">
					<?php esc_html_e( "Amount", 'yith-woocommerce-pdf-invoice' ); ?>
				</td>
				<td class="right-content">
					<?php echo $invoice_details->get_order_currency_new( $refund_order->get_total() ); ?>
				</td>
			</tr>
		
		<?php endif; ?>
	</table>
</div>