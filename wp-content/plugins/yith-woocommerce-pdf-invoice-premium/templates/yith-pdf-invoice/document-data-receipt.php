<?php
/**
 * Document data for receipt template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/document-data-receipt.php
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

$current_order   = $document->order;
$invoice_details = new YITH_Invoice_Details( $document );

?>

<div class="invoice-data-content">
	<table>
		<?php if ( 'yes' === ywpi_get_option( 'ywpi_show_order_number', $document, 'yes' ) ) : ?>
			<tr class="ywpi-order-number">
				<td class="left-content">
					<?php esc_html_e( 'Order No.', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>
				<td class="right-content">
					<?php echo wp_kses_post( $document->order->get_order_number() ); ?>
					<?php do_action( 'yith_ywpi_template_order_number', $document ); ?>
				</td>
			</tr>
		<?php endif ?>

		<?php if ( 'yes' === ywpi_get_option( 'ywpi_show_order_date', $document, 'yes' ) ) : ?>
			<tr class="ywpi-invoice-date">
				<td class="left-content">
					<?php esc_html_e( 'Receipt date', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>
				<td class="right-content">
					<?php echo wp_kses_post( apply_filters( 'ywpi_template_invoice_data_table_invoice_date', $document->get_formatted_document_date(), $document ) ); ?>
				</td>
			</tr>
		<?php endif ?>

		<?php if ( 'yes' === ywpi_get_option( 'ywpi_show_order_amount', $document, 'yes' ) && apply_filters( 'ywpi_template_invoice_data_table_order_amount_visible', true ) ) : ?>
			<tr class="invoice-amount">
				<td class="left-content">
					<?php echo wp_kses_post( apply_filters( 'ywpi_invoice_amount_label', esc_html__( 'Amount', 'yith-woocommerce-pdf-invoice' ) ) ); ?>
				</td>
				<td class="right-content">
					<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $document->order->get_total() ) ); ?>
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

			$payment_method = $document->order->get_payment_method();

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

		<tr>
			<td style="text-align: center">
				<?php
				/**
				 * DO_ACTION: yith_wc_barcodes_and_qr_filter
				 *
				 * Section to display the Barcode or QR from the YITH Barcodes plugin.
				 *
				 * @param int the order ID.
				 */
				do_action( 'yith_wc_barcodes_and_qr_filter', $current_order->get_id() );
				?>
			</td>
		</tr>
	</table>
</div>
