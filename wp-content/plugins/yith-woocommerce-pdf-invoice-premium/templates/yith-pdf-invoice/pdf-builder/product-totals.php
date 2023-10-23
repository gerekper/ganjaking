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

if ( ! $resource instanceof YITH_Credit_Note ) :
	if ( $attr['invoiceType'] ) :
		$show_discount          = ! isset( $attr['showDiscount'] ) || ( isset( $attr['showDiscount'] ) && $attr['showDiscount'] );
		$subtotal_with_discount = $show_discount && ( ! isset( $attr['subtotalInclusiveDiscount'] ) || ( isset( $attr['subtotalInclusiveDiscount'] ) && $attr['subtotalInclusiveDiscount'] ) );

		if ( $show_discount ) {
			$discount_sbt_label = $subtotal_with_discount ? _x( 'Discount incl.', 'discount included inside subtotal', 'yith-woocommerce-pdf-invoice' ) : _x( 'Discount excl.', 'discount exluded inside subtotal', 'yith-woocommerce-pdf-invoice' );
			$label_subtotal     = apply_filters( 'yith_ywpi_invoice_subtotal_label', sprintf( '%s <small>(%s)</small>', $label_subtotal, $discount_sbt_label ), $label_subtotal );
		}

		$split_tax = ! isset( $attr['brokenDownTaxes'] ) || ( isset( $attr['brokenDownTaxes'] ) && $attr['brokenDownTaxes'] ); ?>

		<tr class="subtotal-row">
			<td class="subtotal-label"><?php echo wp_kses_post( $label_subtotal ); ?></td>
			<td class="subtotal number"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_order_subtotal( YITH_PDF_Invoice()->subtotal_incl_discount ) ) ); ?> </td>
		</tr>

		<?php if ( ! isset( $attr['showDiscount'] ) || ( isset( $attr['showDiscount'] ) && $attr['showDiscount'] ) ) : ?>
			<tr class="subtotal-row">
				<td class="subtotal-label"><?php echo esc_html( apply_filters( 'yith_ywpi_invoice_discount_label', _x( 'Discount', 'discount label inside product totals', 'yith-woocommerce-pdf-invoice' ) ) ); ?></td>
				<td class="subtotal number">
					<?php
					/**
					 * APPLY_FILTERS: yith_ywpi_invoice_totals_discount_symbol
					 *
					 * Filter the negative discount symbol in the invoice totals.
					 *
					 * @param   string the symbol.
					 *
					 * @return string
					 */
					echo wp_kses_post( apply_filters( 'yith_ywpi_invoice_totals_discount_symbol', '- ' ) . $invoice_details->get_order_currency_new( $invoice_details->get_order_discount() ) );
					?>
				</td>
			</tr>
			<?php
		endif;

		if ( $split_tax ) :
			foreach ( $current_order->get_tax_totals() as $code => $tax_total ) :
				?>
				<tr class="subtotal-row">
					<td class="subtotal-label"><?php echo esc_html( apply_filters( 'yith_ywpi_invoice_tax_label', $tax_total->label ) ); ?></td>
					<td class="subtotal number"><?php echo wp_kses_post( $tax_total->formatted_amount ); ?></td>
				</tr>
				<?php
			endforeach;
		endif;

		?>
		<tr class="subtotal-row">
			<td class="subtotal-label"> <?php echo wp_kses_post( apply_filters( 'yith_ywpi_invoice_totals_tax_label', esc_html__( 'Tax total', 'yith-woocommerce-pdf-invoice' ) ) ); ?></td>
			<td class="subtotal number"><?php echo wp_kses_post( wc_price( $current_order->get_total_tax() ) ); ?></td>
		</tr>

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
			<td class="total-label"><?php echo esc_html( apply_filters( 'yith_ywpi_invoice_totals_label', _x( 'Total', 'total label inside product totals', 'yith-woocommerce-pdf-invoice' ) ) ); ?></td>
			<td style="text-align:right"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_order_total() ) ); ?></td>
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
	<?php endif; ?>
	<?php
else :
	include_once 'product-totals-credit-notes.php';
	?>
	<?php
endif;
?>
