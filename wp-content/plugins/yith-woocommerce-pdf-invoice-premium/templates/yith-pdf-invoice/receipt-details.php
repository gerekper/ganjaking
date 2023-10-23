<?php
/**
 * Receipt details template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/receipt-details.php
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

$template_selected = yith_ywpi_get_selected_template();

$table_header_color      = ( 'default' === $template_selected ) ? wp_kses_post( get_option( 'ywpi_table_header_color' ) ) : wp_kses_post( get_option( 'ywpi_table_header_color_' . $template_selected ) );
$table_header_font_color = ( 'default' === $template_selected ) ? wp_kses_post( get_option( 'ywpi_table_header_font_color' ) ) : wp_kses_post( get_option( 'ywpi_table_header_font_color_' . $template_selected ) );

?>

<table class="invoice-details">
	<thead style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
		<tr style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
			<th class="column-product" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Product', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>

			<?php if ( ywpi_is_enabled_column_quantity( $document ) ) : ?>
				<th class="column-quantity" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Qty', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_product_price( $document ) ) : ?>
				<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Product price', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_tax( $document ) ) : ?>
				<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Tax', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_percentage_tax( $document ) ) : ?>
				<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Percentage tax', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_total_taxed( $document ) ) : ?>
				<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total (inc. tax)', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ( $invoice_details->get_order_items() as $item_id => $item ) {
		$_product = $invoice_details->get_item_product( $item );
		?>
		<tr>
			<td class="column-product">
				<!-- Show product title -->
				<?php echo wp_kses_post( apply_filters( 'woocommerce_order_product_title', $item['name'], $_product ) ); ?>
				<br>

				<?php if ( ywpi_is_enabled_column_variation( $document ) ) : ?>
					<?php echo wp_kses_post( urldecode( $invoice_details->get_variation_text( $item_id, $_product ) ) ); ?>
					<br>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_sku( $document ) ) : ?>
					<?php echo wp_kses_post( $invoice_details->get_sku_text( $item, $item_id ) ); ?>
				<?php endif; ?>

				<?php
				/**
				 * DO_ACTION: ywpi_receipt_column_product_after_content
				 *
				 * Section after the product content in the receipt product column.
				 *
				 * @param object $document the document object
				 * @param object $_product the product object
				 * @param object $item_id the item ID
				 */
				do_action( 'ywpi_receipt_column_product_after_content', $document, $_product, $item_id );
				?>
			</td>
			<?php if ( ywpi_is_enabled_column_quantity( $document ) ) : ?>
				<td class="column-quantity">
					<?php echo ( isset( $item['qty'] ) ) ? esc_html( $item['qty'] ) : ''; ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_product_price( $document ) ) : ?>
				<td class="column-price">
					<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit( $item ) ) ); ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_tax( $document ) ) : ?>
				<td class="column-price">
					<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_tax'] ) ); ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_percentage_tax( $document ) && isset( $item['line_tax'] ) && isset( $item['line_total'] ) ) : ?>
				<td class="column-price">
					<?php
					if ( floatval( $item['line_total'] ) !== 0 && '' !== $item['line_total'] ) :
						$tax_percentage = $item['line_tax'] * 100 / $item['line_total'];

						$precision = '1';

						echo wp_kses_post( round( $tax_percentage, $precision ) . '%' );
						?>

					<?php else : ?>
						<?php echo '0%'; ?>
					<?php endif; ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_total_taxed( $document ) ) : ?>
				<td class="column-price">
					<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_tax'] + $item['line_total'] ) ); ?>
				</td>
			<?php endif; ?>
		</tr>

		<?php
	} // foreach;

	?>
	</tbody>
</table>
