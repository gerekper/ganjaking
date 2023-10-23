<?php
/**
 * Credit note totals template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/credit-note-totals.php
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$current_order   = $document->order;
$parent_order    = wc_get_order( $current_order->get_parent_id() );
$invoice_details = new YITH_Invoice_Details( $document );

$template_selected   = yith_ywpi_get_selected_template();
$total_section_color = ( 'default' === $template_selected ) ? wp_kses_post( get_option( 'ywpi_total_section_color' ) ) : wp_kses_post( get_option( 'ywpi_total_section_color_' . $template_selected ) );

$negative_value = strval( get_option( 'ywpi_credit_note_positive_values', 'no' ) ) === 'yes' ? '1' : '-1';

if ( intval( $current_order->get_total() ) === intval( $parent_order->get_total() * -1 ) ) {
	$order_subtotal       = $parent_order->get_subtotal() * $negative_value;
	$order_tax_total      = $parent_order->get_total_tax() * $negative_value;
	$order_total          = $parent_order->get_total() * $negative_value;
	$order_total_shipping = $parent_order->get_shipping_total() * $negative_value;
} else {
	$order_subtotal       = $current_order->get_subtotal();
	$order_tax_total      = $current_order->get_total_tax();
	$order_total          = $current_order->get_total();
	$order_total_shipping = $current_order->get_shipping_total();
}

?>

<?php if ( ywpi_is_visible_order_totals( $document ) ) : ?>
	<div class="document-totals">
		<table class="invoice-totals">
			<?php if ( ywpi_is_enabled_credit_note_subtotal_column( $document ) ) : ?>
				<tr class="invoice-details-subtotal">
					<td class="left-content column-product"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order_subtotal ) ); ?>
					</td>
				</tr>
			<?php endif; ?>
			<?php if ( wc_tax_enabled() ) : ?>
				<?php if ( ywpi_is_enabled_credit_note_total_tax_column( $document ) ) : ?>
					<tr class="invoice-details-vat">
						<td class="left-content column-product"><?php echo esc_html__( 'Tax total', 'yith-woocommerce-pdf-invoice' ); ?></td>
						<td class="right-content column-total"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order_tax_total ) ); ?></td>
					</tr>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $order_total_shipping ) : ?>
				<tr class="invoice-details-vat">
					<td class="left-content column-product"><?php esc_html_e( 'Shipping', 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order_total_shipping ) ); ?></td>
				</tr>
			<?php endif; ?>

			<?php
			/**
			 * DO_ACTION: yith_pdf_invoice_before_total
			 *
			 * Section before the document total.
			 *
			 * @param object $current_order the order object
			 */
			do_action( 'yith_pdf_invoice_before_total', $current_order );
			?>

			<tr class="invoice-details-total">
				<td class="left-content column-product" style="background-color: <?php echo wp_kses_post( $total_section_color ); ?>"><?php esc_html_e( 'Refunded amount', 'yith-woocommerce-pdf-invoice' ); ?></td>
				<td class="right-content column-total" style="background-color: <?php echo wp_kses_post( $total_section_color ); ?>"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order_total ) ); ?></td>
			</tr>

			<?php
			/**
			 * DO_ACTION: yith_pdf_invoice_after_total
			 *
			 * Section after the document total.
			 *
			 * @param object $current_order the order object
			 */
			do_action( 'yith_pdf_invoice_after_total', $current_order );
			?>
		</table>
	</div>
<?php endif; ?>
