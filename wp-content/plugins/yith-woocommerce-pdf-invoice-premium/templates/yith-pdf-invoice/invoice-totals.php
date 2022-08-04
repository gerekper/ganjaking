<?php
/**
 * Invoice totals template.
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
		<div class="invoice-totals-initial"></div>
		<table class="invoice-totals">
			<tr class="invoice-details-subtotal">
				<td class="left-content column-product"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-pdf-invoice' ); ?>
					<?php
					if ( ywpi_is_visible_order_discount( $document ) ) :
						if ( YITH_PDF_Invoice()->subtotal_incl_discount ) :
							esc_html_e( 'Discount inc.', 'yith-woocommerce-pdf-invoice' );
						else :
							esc_html_e( 'Discount exc.', 'yith-woocommerce-pdf-invoice' );
						endif;
					endif;
					?>
				</td>
				<td class="right-content column-total"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_order_subtotal( YITH_PDF_Invoice()->subtotal_incl_discount ) ) ); ?>
				</td>
			</tr>

			<?php if ( ywpi_is_visible_order_discount( $document ) ) : ?>
				<tr>
					<td class="left-content column-product"><?php echo esc_html__( 'Discount', 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo wp_kses_post( apply_filters( 'yith_ywpi_invoice_totals_discount_symbol', '- ' ) . $invoice_details->get_order_currency_new( $invoice_details->get_order_discount() ) ); ?></td>
				</tr>
			<?php endif; ?>

			<?php if ( wc_tax_enabled() ) : ?>
				<?php if ( ywpi_is_visible_broken_down_taxes( $document ) ) : ?>
					<?php foreach ( $current_order->get_tax_totals() as $code => $tax_total ) : ?>
						<tr class="invoice-details-vat">
							<td class="left-content column-product"><?php echo esc_html( $tax_total->label ); ?></td>
							<td class="right-content column-total"><?php echo wp_kses_post( $tax_total->formatted_amount ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				<tr class="invoice-details-vat">
					<td class="left-content column-product"><?php echo esc_html__( 'Tax total', 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo wp_kses_post( wc_price( $current_order->get_total_tax() ) ); ?></td>
				</tr>
			<?php endif; ?>
			<?php if ( $document instanceof YITH_Shipping ) : ?>
			<div class="ywpi-shipping-method">
				<tr class="invoice-details-shipping-method">
					<td class="left-content column-product"><?php echo esc_html__( 'Shipping method', 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo wp_kses_post( $current_order->get_shipping_method() . ' ' . wc_price( $current_order->get_shipping_total() ) ); ?></td>
				</tr>
			</div>
			<?php endif; ?>
			<?php do_action( 'yith_pdf_invoice_before_total', $current_order ); ?>

			<tr class="invoice-details-total">
				<td class="left-content column-product" style="background-color: <?php echo $total_section_color; ?>"><?php echo esc_html__( 'Total', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				<td class="right-content column-total" style="background-color: <?php echo $total_section_color; ?>"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_order_total() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
		</table>
	</div>
<?php endif; ?>
