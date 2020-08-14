<?php /* @var YITH_Invoice $document */

$current_order   = $document->order;
$invoice_details = new YITH_Invoice_Details( $document );
?>

<div class="invoice-data-content">
	<table>

		<tr class="ywpi-order-number">
			<td class="left-content">
				<?php esc_html_e( "Order No.", 'yith-woocommerce-pdf-invoice' ); ?>
			</td>
			<td class="right-content">
				<?php echo $document->order->get_order_number(); ?>
				<?php do_action( 'yith_ywpi_template_order_number', $document ); ?>
			</td>
		</tr>

		<tr class="ywpi-invoice-date">
			<td class="left-content">
				<?php esc_html_e( "Receipt date", 'yith-woocommerce-pdf-invoice' ); ?>
			</td>
			<td class="right-content">
				<?php echo apply_filters( 'ywpi_template_invoice_data_table_invoice_date', $document->get_formatted_document_date(), $document ); ?>
			</td>
		</tr>

		<?php if ( apply_filters( 'ywpi_template_invoice_data_table_order_amount_visible', true ) ) : ?>
			<tr class="invoice-amount">
				<td class="left-content">
					<?php echo apply_filters( 'ywpi_invoice_amount_label', esc_html__( "Amount", 'yith-woocommerce-pdf-invoice' )); ?>
				</td>
				<td class="right-content">
					<?php echo $invoice_details->get_order_currency_new( $document->order->get_total() ); ?>
				</td>
			</tr>

        <?php endif; ?>

		<tr class="ywpi-invoice-date">
			<td class="left-content">
				<?php esc_html_e( "Payment method", 'yith-woocommerce-pdf-invoice' ); ?>
			</td>
			<td class="right-content">
				<?php echo apply_filters( 'ywpi_template_invoice_data_table_invoice_date', $document->order->get_payment_method_title(), $document ); ?>
			</td>
		</tr>


		<tr>
            <td style="text-align: center">
                <?php do_action ( 'yith_wc_barcodes_and_qr_filter', $current_order->get_id() );?>
            </td>
        </tr>


    </table>
</div>
