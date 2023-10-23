<?php
/**
 * Document data for packing slip template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/document-data-packing-slip.php
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$current_order   = $document->order;
$invoice_details = new YITH_Invoice_Details( $document );

?>

<div class="invoice-data-content">
	<table>
		<?php
		/**
		 * DO_ACTION: yith_ywpi_show_packing_slip_data_start
		 *
		 * Section at the start of the packing slip.
		 *
		 * @param object $document the document object.
		 */
		do_action( 'yith_ywpi_show_packing_slip_data_start', $document );
		?>

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

		<?php
		/**
		 * DO_ACTION: yith_ywpi_show_packing_slip_data_after_order_number
		 *
		 * Display the packing slip data after the order number.
		 *
		 * @param object $document the document object.
		 */
		do_action( 'yith_ywpi_show_packing_slip_data_after_order_number', $document );
		?>

		<?php if ( 'yes' === ywpi_get_option( 'ywpi_show_order_date', $document, 'yes' ) ) : ?>
			<tr class="ywpi-invoice-date">
				<td class="left-content">
					<?php esc_html_e( 'Order date', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>
				<td class="right-content">
					<?php echo wp_kses_post( $document->get_formatted_order_date() ); ?>
				</td>
			</tr>
		<?php endif ?>

		<?php
		/**
		 * DO_ACTION: yith_ywpi_show_packing_slip_data_after_order_date
		 *
		 * Display the packing slip data after the order date.
		 *
		 * @param object $document the document object.
		 */
		do_action( 'yith_ywpi_show_packing_slip_data_after_order_date', $document );
		?>

		<?php if ( 'yes' === ywpi_get_option( 'ywpi_show_order_amount', $document, 'yes' ) && 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_show_order_totals', $document, 'yes' ) ) ) : ?>
			<tr class="invoice-amount">
				<td class="left-content">
					<?php esc_html_e( 'Amount', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>
				<td class="right-content">
					<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $document->order->get_total() ) ); ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php
		/**
		 * DO_ACTION: yith_ywpi_show_packing_slip_data_end
		 *
		 * Section at the end of the packing slip.
		 *
		 * @param object $document the document object.
		 */
		do_action( 'yith_ywpi_show_packing_slip_data_end', $document );

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
	</table>
</div>
