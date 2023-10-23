<?php
/**
 * Invoice total table content
 *
 * @var YITH_Document $resource       Current document.
 * @var array         $attr           List of template options.
 * @var string        $label_subtotal Label of subtotals.
 *
 * @package YITH\PDFInvoice\Templates
 */

$current_order   = $resource->order;
$invoice_details = new YITH_Invoice_Details( $resource );
$parent_order    = wc_get_order( $current_order->get_parent_id() );
$negative_amount = 'yes' === get_option( 'ywpi_credit_note_positive_values_builder', 'no' ) ? 1 : -1;

if ( intval( $current_order->get_total() ) === intval( $parent_order->get_total() * -1 ) ) {
	$order = $parent_order; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
} else {
	$order           = $current_order; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	$negative_amount = $negative_amount * -1;
}

$order_subtotal       = $order->get_subtotal() * $negative_amount;
$order_tax_total      = $order->get_total_tax() * $negative_amount;
$order_total          = $order->get_total() * $negative_amount;
$order_total_shipping = $order->get_shipping_total() * $negative_amount;
?>

<?php if ( isset( $attr['showSubtotal'] ) && $attr['showSubtotal'] ) : ?>
	<tr class="total-row">
		<td class="total-label"><?php echo esc_html_x( 'Subtotal', 'total label inside product totals', 'yith-woocommerce-pdf-invoice' ); ?></td>
		<td style="text-align:right"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order_subtotal ) ); ?></td>
	</tr>
<?php endif; ?>
<?php if ( isset( $attr['showTax'] ) && $attr['showTax'] ) : ?>
	<tr class="total-row">
		<td class="total-label"><?php echo esc_html__( 'Tax total', 'yith-woocommerce-pdf-invoice' ); ?></td>
		<td style="text-align:right"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order_tax_total ) ); ?></td>
	</tr>
<?php endif; ?>
<?php if ( $order_total_shipping ) : ?>
	<tr class="total-row">
		<td class="total-label"><?php esc_html_e( 'Shipping', 'yith-woocommerce-pdf-invoice' ); ?></td>
		<td style="text-align:right"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order_total_shipping ) ); ?></td>
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
<tr class="total-row">
	<td class="total-label"><?php esc_html_e( 'Refunded amount', 'yith-woocommerce-pdf-invoice' ); ?></td>
	<td style="text-align:right"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order_total ) ); ?></td>
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
