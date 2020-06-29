<?php
/**
 * Override this template by copying it to [your theme folder]/woocommerce/yith-pdf-invoice
 *
 * @author        Yithemes
 * @package       yith-woocommerce-pdf-invoice-premium/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var WC_Order $current_order */
/** @var YITH_Document $document */

$current_order   = $document->order;
$invoice_details = new YITH_Invoice_Details( $document );

$total_section_color = get_option('ywpi_total_section_color');

?>

<?php if ( ywpi_is_visible_order_totals( $document ) ) : ?>
	<div class="document-totals">
		<table class="invoice-totals">
			<tr class="invoice-details-subtotal">
				<td class="left-content column-product"><?php esc_html_e( "Subtotal", 'yith-woocommerce-pdf-invoice' ); ?>
					<?php
					if( ywpi_is_visible_order_discount( $document ) ):
						if( YITH_PDF_Invoice()->subtotal_incl_discount ):
							esc_html_e('Discount inc.','yith-woocommerce-pdf-invoice');
						else:
							esc_html_e('Discount exc.','yith-woocommerce-pdf-invoice');
						endif;
					endif;
					?>
				</td>
				<td class="right-content column-total"><?php echo $invoice_details->get_order_currency_new( $invoice_details->get_order_subtotal( YITH_PDF_Invoice()->subtotal_incl_discount ) ); ?>
                </td>
			</tr>

			<?php if ( ywpi_is_visible_order_discount( $document ) ): ?>
				<tr>
					<td class="left-content column-product"><?php esc_html_e( "Discount", 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo apply_filters( 'yith_ywpi_invoice_totals_discount_symbol', '- ') . $invoice_details->get_order_currency_new( $invoice_details->get_order_discount() ); ?></td>
				</tr>
			<?php endif; ?>

            <?php if ( wc_tax_enabled() ) : ?>
                <?php if ( ywpi_is_visible_broken_down_taxes( $document ) ): ?>
                    <?php foreach ( $current_order->get_tax_totals() as $code => $tax_total ) :?>
                        <tr class="invoice-details-vat">
                            <td class="left-content column-product"><?php echo esc_html( $tax_total->label ); ?></td>
                            <td class="right-content column-total"><?php echo wp_kses_post( $tax_total->formatted_amount ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <tr class="invoice-details-vat">
                    <td class="left-content column-product"><?php esc_html_e( "Tax total", 'yith-woocommerce-pdf-invoice' ); ?></td>
                    <td class="right-content column-total"><?php echo wc_price( $current_order->get_total_tax() ); ?></td>
                </tr>
            <?php endif; ?>

			<?php do_action( 'yith_pdf_invoice_before_total', $current_order ); ?>

			<tr class="invoice-details-total">
				<td class="left-content column-product" style="background-color: <?php echo $total_section_color;?>"><?php esc_html_e( "Total", 'yith-woocommerce-pdf-invoice' ); ?></td>
				<td class="right-content column-total" style="background-color: <?php echo $total_section_color;?>"><?php echo $invoice_details->get_order_currency_new( $invoice_details->get_order_total() ); ?></td>
			</tr>
		</table>
	</div>
<?php endif; ?>