<?php
/**
 * Receipt totals template.
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-details.php
 *
 * @author  YITH
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$current_order   = $document->order;
$invoice_details = new YITH_Invoice_Details( $document );

$template_selected   = yith_ywpi_get_selected_template();
$total_section_color = ( 'default' === $template_selected ) ? wp_kses_post( get_option( 'ywpi_total_section_color' ) ) : wp_kses_post( get_option( 'ywpi_total_section_color_' . $template_selected ) );

?>

<?php if ( ywpi_is_visible_order_totals( $document ) ) : ?>
	<div class="document-totals">
		<table class="invoice-totals">
			<tr class="invoice-details-subtotal">
				<td class="left-content column-product"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-pdf-invoice' ); ?></td>
				<td class="right-content column-total"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_order_subtotal( YITH_PDF_Invoice()->subtotal_incl_discount ) ) ); ?>
				</td>
			</tr>

			<?php if ( ywpi_is_visible_order_discount( $document ) && $invoice_details->get_order_discount() > 0 ) : ?>
				<tr>
					<td class="left-content column-product"><?php esc_html_e( 'Discount', 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo wp_kses_post( apply_filters( 'yith_ywpi_invoice_totals_discount_symbol', '- ' ) . $invoice_details->get_order_currency_new( $invoice_details->get_order_discount() ) ); ?></td>
				</tr>
			<?php endif; ?>

			<tr class="invoice-details-shipping">
				<td class="left-content column-product"><?php esc_html_e( 'Shipping', 'yith-woocommerce-pdf-invoice' ); ?></td>
				<td class="right-content column-total"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( (float) $current_order->get_shipping_total() + (float) $current_order->get_shipping_tax() ) ); ?></td>
			</tr>

			<?php if ( wc_tax_enabled() ) : ?>
				<tr class="invoice-details-vat">
					<td class="left-content column-product"><?php esc_html_e( 'Tax', 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo wp_kses_post( wc_price( $current_order->get_total_tax() ) ); ?></td>
				</tr>
			<?php endif; ?>

			<?php do_action( 'yith_pdf_invoice_before_total', $current_order ); ?>

			<tr class="invoice-details-total">
				<td class="left-content column-product" style="background-color: <?php echo wp_kses_post( $total_section_color ); ?>"><?php esc_html_e( 'Total', 'yith-woocommerce-pdf-invoice' ); ?></td>
				<td class="right-content column-total" style="background-color: <?php echo wp_kses_post( $total_section_color ); ?>"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_order_total() ) ); ?></td>
			</tr>
		</table>
	</div>
<?php endif; ?>
