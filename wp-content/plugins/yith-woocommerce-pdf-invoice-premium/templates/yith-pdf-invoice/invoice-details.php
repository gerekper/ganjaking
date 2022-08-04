<?php
/**
 * Invoice details template.
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-details.php
 *
 * @author  YITH
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

$current_order     = $document->order;
$invoice_details   = new YITH_Invoice_Details( $document );
$template_selected = yith_ywpi_get_selected_template();

$table_header_color      = ( 'default' === $template_selected ) ? wp_kses_post( get_option( 'ywpi_table_header_color' ) ) : wp_kses_post( get_option( 'ywpi_table_header_color_' . $template_selected ) );
$table_header_font_color = ( 'default' === $template_selected ) ? wp_kses_post( get_option( 'ywpi_table_header_font_color' ) ) : wp_kses_post( get_option( 'ywpi_table_header_font_color_' . $template_selected ) );

$show_shipping_section = true;

$has_suborder = $current_order->get_meta( 'has_sub_order' );

if ( ! $has_suborder ) {
	$order_items = $invoice_details->get_order_items();
} else {
	$order_items = $current_order->get_items();
}
?>

<table class="invoice-details">
	<thead style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
	<tr style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
		<?php if ( ywpi_is_enabled_column_picture( $document ) ) { ?>
			<th class="column-picture" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
				<?php esc_html_e( 'Product', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</th>
			<th class="column-product" style="background-color: <?php echo esc_attr( $table_header_color ); ?>; color:<?php echo esc_attr( $table_header_font_color ); ?>"></th>
		<?php } else { ?>
			<th class="column-product" style="background-color: <?php echo esc_attr( $table_header_color ); ?>; color:<?php echo esc_attr( $table_header_font_color ); ?>">
				<?php esc_html_e( 'Product', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</th>
		<?php } ?>

		<?php if ( ywpi_is_enabled_column_quantity( $document ) ) : ?>
			<th class="column-quantity" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Qty', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_regular_price( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Regular price', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_sale_price( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Sale price', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_product_price( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Price', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_percentage( $document ) ) : ?>
			<th class="column-discount-percentage" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Discount percentage', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_tax( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Tax', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_percentage_tax( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Percentage tax', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_line_total( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_total_taxed( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total (inc. tax)', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>
	</tr>
	</thead>
	<tbody>
	<?php

	foreach ( $order_items as $item_id => $item ) {

		$_product = $invoice_details->get_item_product( $item );

		$hide_bundle_items = get_option( 'yith-wcpb-hide-bundled-items-in-cart', 'no' ) === 'yes';

		if ( 1 === intval( $hide_bundle_items ) || apply_filters( 'yith_ywpi_hide_bundled_items', false ) ) {

			$is_bundled_by = wc_get_order_item_meta( $item_id, '_bundled_by', true );

			if ( '' != $is_bundled_by ) { //phpcs:ignore
				continue;
			}
		}

		?>
		<tr>
			<!-- Show picture if related option is enabled -->
			<?php if ( ywpi_is_enabled_column_picture( $document ) ) : ?>
				<td class="column-picture">
					<?php
					$image_path = apply_filters( 'yith_ywpi_image_path', $invoice_details->get_product_image( $item ), $_product );
					if ( $image_path ) :
						?>
						<img class="product-image" src="<?php echo wp_kses_post( $image_path ); ?>" />
					<?php endif; ?>
				</td>
			<?php endif; ?>

			<td class="column-product">
				<!-- Show product title -->
				<span class="ywpi-product-text"><?php echo wp_kses_post( apply_filters( 'woocommerce_order_product_title', $item['name'], $_product ) ); ?></span>

				<?php if ( ywpi_is_enabled_column_variation( $document ) ) : ?>
					<?php echo wp_kses_post( urldecode( $invoice_details->get_variation_text( $item_id, $_product ) ) ); ?>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_sku( $document ) ) : ?>
					<br>
					<span class="ywpi-sku-text"><?php echo wp_kses_post( $invoice_details->get_sku_text( $item, $item_id ) ); ?></span>
				<?php endif; ?>

				<?php
				if ( $document instanceof YITH_Shipping ) {

					if ( ywpi_is_enabled_column_weight_dimension( $document ) ) :

						$has_dimensions = ( $_product->get_length() || $_product->get_height() || $_product->get_width() ) && ! $_product->is_virtual();

						if ( $_product->has_weight() || $has_dimensions ) :
							$dimensions = version_compare( WC()->version, '2.7.0', '<' ) ?
							$_product->get_dimensions() :
							wc_format_dimensions( $_product->get_dimensions( false ) );
							?>
				<div style="font-size: 10px">
							<?php if ( $_product->has_weight() ) : ?>
						<span class="invoice-weight"><?php esc_html_e( 'Weight: ', 'yith-woocommerce-pdf-invoice' ); ?></span>
						<span><?php echo wp_kses_post( $_product->get_weight() . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ) ); ?></span>
					<?php endif; ?>

							<?php if ( $_product->has_dimensions() ) : ?>
						<br>
						<span class="invoice-dimensions"><?php esc_html_e( 'Dimensions: ', 'yith-woocommerce-pdf-invoice' ); ?></span>
						<span><?php echo wp_kses_post( $dimensions ); ?></span>

					<?php endif; ?>
				</div>
							<?php
							endif;
					endif;
				}


				if ( ywpi_is_enabled_column_short_description( $document ) && ( $invoice_details->get_short_description( $item, $item_id ) != '' ) ) : //phpcs:ignore
					?>
				<div class="product-short-description"><?php echo wp_kses_post( $invoice_details->get_short_description( $item, $item_id ) ); ?></div>
				<?php endif; ?>

				<?php do_action( 'yith_ywpi_column_product_after_content', $document, $_product, $item_id ); ?>
			</td>
			<?php if ( ywpi_is_enabled_column_quantity( $document ) ) : ?>
				<td class="column-quantity">
					<?php echo ( isset( $item['qty'] ) ) ? esc_html( $item['qty'] ) : ''; ?>
				</td>
			<?php endif; ?>


			<?php if ( ywpi_is_enabled_column_regular_price( $document ) ) : ?>
				<td class="column-price">
					<?php echo wp_kses_post( wc_price( $invoice_details->get_item_product_regular_price( $item, $item_id ) ) ); ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_sale_price( $document ) ) : ?>
				<td class="column-price">
					<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit_sale( $item ) ) ); ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_product_price( $document ) ) : ?>
				<td class="column-price">
					<?php
					if ( $invoice_details->get_item_product_regular_price( $item, $item_id ) > round( $invoice_details->get_item_price_per_unit( $item ), 2 ) ) {
						?>
					<span class="ywpi-price-labeled">
						<?php echo wp_kses_post( wc_price( $invoice_details->get_item_product_regular_price( $item, $item_id ) ) ); ?>
					</span>
					<b>
						<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit( $item ) ) ); ?>
					</b>
						<?php
					} else {
						echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit( $item ) ) );
					}
					?>
				</td>
			<?php endif; ?>


			<?php if ( ywpi_is_enabled_column_percentage( $document ) ) : ?>
				<td class="column-discount-percentage">
					<?php echo wp_kses_post( $invoice_details->get_item_percentage_discount( $item ) ); ?>
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
					if ( $item['line_total'] != 0 && $item['line_total'] != '' ) : //phpcs:ignore

						/**
						 * Before 3.2.0
						 */
						/*
						$tax_percentage = $item['line_tax'] * 100 / $item['line_total'];
						$precision = '1';
						echo wp_kses_post( round( $tax_percentage, $precision ) . '%' );
						*/

						$tax   = new WC_Tax(); //phpcs:ignore
						$taxes = $tax->get_rates( $_product->get_tax_class() );

						$rates = array_shift( $taxes );
						// Take only the item rate and round it.
						$item_rate = round( array_shift( $rates ) );

						echo wp_kses_post( $item_rate ) . '%';
						?>

					<?php else : ?>
						<?php echo '0%'; ?>
					<?php endif; ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_line_total( $document ) ) : ?>
				<td class="column-price">
					<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_total'] ) ); ?>
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

	if ( apply_filters( 'ywpi_is_visible_fee_details_section', true, $document ) ) :

		foreach ( $invoice_details->get_order_fees() as $item_id => $item ) {
			?>

			<tr class="border-top">
				<?php if ( ywpi_is_enabled_column_picture( $document ) ) : ?>
					<td class="column-picture">
					</td>
				<?php endif; ?>

				<td class="column-product">
					<?php echo ! empty( $item['name'] ) ? esc_html( $item['name'] ) : esc_html__( 'Fee', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>

				<?php if ( ywpi_is_enabled_column_quantity( $document ) ) : ?>
					<td class="column-quantity">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_product_price( $document ) ) : ?>
					<td class="column-price">
						<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_total'] ) ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_regular_price( $document ) ) : ?>
					<td class="column-price">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_sale_price( $document ) ) : ?>
					<td class="column-price">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_percentage( $document ) ) : ?>
					<td class="column-discount-percentage"></td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_tax( $document ) ) : ?>
					<td class="column-price">
						<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_tax'] ) ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_percentage_tax( $document ) && isset( $item['line_tax'] ) && isset( $item['line_total'] ) ) : ?>
					<td class="column-price">
						<?php
						if ( $item['line_total'] != 0 && $item['line_total'] != '' ) : //phpcs:ignore

							$tax_percentage = $item['line_tax'] * 100 / $item['line_total'];

							echo wp_kses_post( round( $tax_percentage, 0 ) . '%' );
							?>


						<?php else : ?>
							<?php echo '0%'; ?>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_line_total( $document ) ) : ?>
					<td class="column-price">
						<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_total'] ) ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_total_taxed( $document ) ) : ?>
					<td class="column-price">
						<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_tax'] + $item['line_total'] ) ); ?>
					</td>
				<?php endif; ?>

			</tr>

			<?php
		}   // foreach
	endif;

	if ( ( ( $document instanceof YITH_Invoice || $document instanceof YITH_Pro_Forma ) && 'no' === get_option( 'ywpi_invoice_shipping_details', 'yes' ) ) ) {
		$show_shipping_section = false;
	}

	if ( $show_shipping_section && apply_filters( 'ywpi_is_visible_shipping_details_section', true, $document ) ) :

		foreach ( $invoice_details->get_order_shipping() as $item_id => $item ) {
			?>

			<tr>
				<?php if ( ywpi_is_enabled_column_picture( $document ) ) : ?>
					<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> <?php //phpcs:ignore ?>
					<td class="column-picture">
					</td>
				<?php endif; ?>

				<td class="column-product">
					<?php echo wp_kses_post( ! empty( $item['name'] ) ? esc_html( $item['name'] ) : esc_html__( 'Shipping', 'yith-woocommerce-pdf-invoice' ) ); ?>
				</td>

				<?php if ( ywpi_is_enabled_column_quantity( $document ) ) : ?>
					<td class="column-quantity">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_product_price( $document ) ) : ?>

					<td class="column-price">
						<?php echo wp_kses_post( ( isset( $item['cost'] ) ) ? $invoice_details->get_order_currency_new( wc_round_tax_total( $item['cost'] ) ) : '' ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_regular_price( $document ) ) : ?>
					<td class="column-price">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_sale_price( $document ) ) : ?>
					<td class="column-price">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_percentage( $document ) ) : ?>
					<td class="column-discount-percentage"></td>
				<?php endif; ?>

				<?php

				if ( ywpi_is_enabled_column_tax( $document ) ) :
					?>
					<td class="column-price">
						<?php
						echo wp_kses_post( $invoice_details->get_order_currency_new( wc_round_tax_total( $invoice_details->get_item_shipping_taxes( $item ) ) ) );
						?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_percentage_tax( $document ) && isset( $item['cost'] ) ) : ?>
					<td class="column-price">
						<?php
						if ( $item['cost'] != 0 && $item['cost'] != '' ) : //phpcs:ignore

							$tax_percentage = ( ( $invoice_details->get_item_shipping_taxes( $item ) ) * 100 ) / $item['cost'];

							$precision = '1';

							echo wp_kses_post( round( $tax_percentage, $precision ) . '%' );
							?>

						<?php else : ?>
							<?php echo '0%'; ?>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_line_total( $document ) ) : ?>
					<td class="column-price">
						<?php echo wp_kses_post( ( isset( $item['cost'] ) ) ? $invoice_details->get_order_currency_new( $item['cost'] ) : '' ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_total_taxed( $document ) ) : ?>
					<td class="column-price">
						<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['cost'] + $invoice_details->get_item_shipping_taxes( $item ) ) ); ?>
					</td>
				<?php endif; ?>
			</tr>
			<?php
		};
	endif;

	?>
	</tbody>
</table>
