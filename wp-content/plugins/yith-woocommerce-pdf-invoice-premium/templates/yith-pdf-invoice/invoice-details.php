<?php
/**
 * Override this template by copying it to [your theme folder]/woocommerce/yith-pdf-invoice
 *
 * @author        Yithemes
 * @package       yith-woocommerce-pdf-invoice-premium/Templates
 * @version       1.0.0
 */

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/** @var YITH_Document $document */

$current_order   = $document->order;
$invoice_details = new YITH_Invoice_Details( $document );

$table_header_color = get_option('ywpi_table_header_color');
$table_header_font_color = get_option('ywpi_table_header_font_color');


$has_suborder = $current_order->get_meta( 'has_sub_order' );

if ( ! $has_suborder ){
	$order_items = $invoice_details->get_order_items ();
}
else{
	$order_items = $current_order->get_items();
}


?>

<table class="invoice-details">
	<thead style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>">
	<tr style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>">
		<?php if ( ywpi_is_enabled_column_picture ( $document ) ) : ?>
			<th class="column-picture" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"></th>
		<?php endif;?>

		<th class="column-product" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Product', 'yith-woocommerce-pdf-invoice' ); ?></th>

		<?php if ( ywpi_is_enabled_column_quantity ( $document ) ) : ?>
			<th class="column-quantity" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Qty', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_regular_price ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Regular price', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_sale_price ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Sale price', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

        <?php if ( ywpi_is_enabled_column_product_price ( $document ) ) : ?>
            <th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Product price', 'yith-woocommerce-pdf-invoice' ); ?></th>
        <?php endif; ?>

		<?php if ( ywpi_is_enabled_column_percentage ( $document ) ) : ?>
			<th class="column-discount-percentage" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Discount percentage', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_line_total ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Line total', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_tax ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Tax', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_percentage_tax ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Percentage tax', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_total_taxed ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total (inc. tax)', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>
	</tr>
	</thead>
	<tbody>
	<?php

	/** @var WC_Product $_product */
	foreach ( $order_items as $item_id => $item ) {

		$_product = $invoice_details->get_item_product ( $item );

		?>
		<tr>
			<!-- Show picture if related option is enabled -->
			<?php if ( ywpi_is_enabled_column_picture ( $document ) ): ?>
				<td class="column-picture">
					<?php $image_path = apply_filters ( 'yith_ywpi_image_path', $invoice_details->get_product_image ( $item ), $_product );
					if ( $image_path ): ?>
						<img class="product-image" src="<?php echo $image_path; ?>" />
					<?php endif; ?>
				</td>
			<?php endif; ?>

			<td class="column-product">
				<!-- Show product title -->
				<?php echo apply_filters ( 'woocommerce_order_product_title', $item['name'], $_product ); ?>
				<br>

				<?php if ( ywpi_is_enabled_column_variation ( $document ) ) : ?>
					<?php echo urldecode($invoice_details->get_variation_text ( $item_id, $_product )); ?>
                    <br>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_sku ( $document ) ) : ?>
					<?php echo $invoice_details->get_sku_text ( $item, $item_id ); ?>
				<?php endif; ?>


                <?php
                if ( $document instanceof YITH_Shipping ) {

                if ( ywpi_is_enabled_column_weight_dimension($document) ):
                $has_dimensions = ($_product->get_length() || $_product->get_height() || $_product->get_width()) && !$_product->is_virtual();

                if ( $_product->has_weight() || $has_dimensions ) :
                $dimensions = version_compare(WC()->version, '2.7.0', '<') ?
                    $_product->get_dimensions() :
                    wc_format_dimensions($_product->get_dimensions(false));
                ?>
                <div style="font-size: 10px">
                    <?php if ($_product->has_weight()) : ?>
                        <br>
                        <span><?php esc_html_e('Weight: ', 'yith-woocommerce-pdf-invoice') ?></span>
                        <span><?php echo $_product->get_weight() . ' ' . esc_attr(get_option('woocommerce_weight_unit')); ?></span>
                    <?php endif; ?>

                    <?php if ($_product->has_dimensions()) : ?>
                        <br>
                        <span><?php esc_html_e('Dimensions: ', 'yith-woocommerce-pdf-invoice') ?></span>
                        <span><?php echo $dimensions; ?></span>

                    <?php endif; ?>
                </div>
                <?php
                        endif;
                    endif;
                }


                if ( ywpi_is_enabled_column_short_description ( $document ) && ( $invoice_details->get_short_description( $item, $item_id) != '' ) ) : ?>
                <div class="product-short-description"><?php echo $invoice_details->get_short_description( $item, $item_id); ?></div>
                <?php endif; ?>

				<?php do_action ( 'yith_ywpi_column_product_after_content', $document, $_product, $item_id ); ?>
			</td>
			<?php if ( ywpi_is_enabled_column_quantity ( $document ) ) : ?>
				<td class="column-quantity">
					<?php echo ( isset( $item['qty'] ) ) ? esc_html ( $item['qty'] ) : ''; ?>
				</td>
			<?php endif; ?>


            <?php if ( ywpi_is_enabled_column_regular_price ( $document ) ) : ?>
                <td class="column-price">
                    <?php echo wc_price( $invoice_details->get_item_product_regular_price ( $item, $item_id ) ); ?>
                </td>
            <?php endif; ?>

            <?php if ( ywpi_is_enabled_column_sale_price ( $document ) ) : ?>
                <td class="column-price">
                    <?php echo $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit_sale ( $item ) ); ?>
                </td>
            <?php endif; ?>

            <?php if ( ywpi_is_enabled_column_product_price ( $document ) ) : ?>
                <td class="column-price">
                    <?php echo $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit ( $item ) ); ?>
                </td>
            <?php endif; ?>


			<?php if ( ywpi_is_enabled_column_percentage ( $document ) ) : ?>
				<td class="column-discount-percentage">
					<?php echo $invoice_details->get_item_percentage_discount ( $item ); ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_line_total ( $document ) ) : ?>
				<td class="column-price">
					<?php echo $invoice_details->get_order_currency_new( $item["line_total"] ); ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_tax ( $document ) ) : ?>
				<td class="column-price">
					<?php echo $invoice_details->get_order_currency_new( $item["line_tax"] ); ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_percentage_tax ( $document ) && isset($item['line_tax']) && isset($item['line_total']) ) : ?>
				<td class="column-price">
					<?php if( $item['line_total'] != 0 && $item['line_total'] != '' ):

                        $tax_percentage = $item['line_tax'] * 100 / $item['line_total'];

                        $precision = '1';

                        echo round( $tax_percentage, $precision ) . '%'; ?>

					<?php else: ?>
						<?php echo '0%'; ?>
					<?php endif; ?>
				</td>
			<?php endif; ?>

			<?php if ( ywpi_is_enabled_column_total_taxed ( $document ) ) : ?>
				<td class="column-price">
					<?php echo $invoice_details->get_order_currency_new( $item["line_tax"] + $item["line_total"] ); ?>
				</td>
			<?php endif; ?>
		</tr>

		<?php
	} // foreach;

	if ( apply_filters ( 'ywpi_is_visible_fee_details_section', true, $document ) ) :

		foreach ( $invoice_details->get_order_fees () as $item_id => $item ) {
			?>

			<tr class="border-top">
				<?php if ( ywpi_is_enabled_column_picture ( $document ) ) : ?>
					<td class="column-picture">
					</td>
				<?php endif; ?>

				<td class="column-product">
					<?php echo ! empty( $item['name'] ) ? esc_html ( $item['name'] ) : esc_html__( 'Fee', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>

				<?php if ( ywpi_is_enabled_column_quantity ( $document ) ) : ?>
					<td class="column-quantity">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_product_price ( $document ) ) : ?>
					<td class="column-price">
						<?php echo $invoice_details->get_order_currency_new( $item['line_total'] ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_regular_price ( $document ) ) : ?>
					<td class="column-price">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_sale_price ( $document ) ) : ?>
					<td class="column-price">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_percentage ( $document ) ) : ?>
					<td class="column-discount-percentage"></td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_line_total ( $document ) ) : ?>
					<td class="column-price">
						<?php echo $invoice_details->get_order_currency_new( $item['line_total'] ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_tax ( $document ) ) : ?>
					<td class="column-price">
						<?php echo $invoice_details->get_order_currency_new( $item['line_tax'] ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_percentage_tax ( $document ) && isset($item['line_tax']) && isset($item['line_total']) ) : ?>
					<td class="column-price">
                        <?php if( $item['line_total'] != 0 && $item['line_total'] != '' ):

                            $tax_percentage = $item['line_tax'] * 100 / $item['line_total'];

                            $precision = '1';

                            echo round( $tax_percentage, $precision ) . '%'; ?>

                        <?php else: ?>
							<?php echo '0%'; ?>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_total_taxed ( $document ) ) : ?>
					<td class="column-price">
						<?php echo $invoice_details->get_order_currency_new( $item["line_tax"] + $item["line_total"] ); ?>
					</td>
				<?php endif; ?>

			</tr>

			<?php
		}   // foreach
	endif;

	if ( apply_filters ( 'ywpi_is_visible_shipping_details_section', true, $document ) ) :

		foreach ( $invoice_details->get_order_shipping () as $item_id => $item ) {
			?>

			<tr>
				<?php if ( ywpi_is_enabled_column_picture ( $document ) ) : ?>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
					<td class="column-picture">
                    </td>
				<?php endif; ?>

				<td class="column-product">
					<?php echo ! empty( $item['name'] ) ? esc_html ( $item['name'] ) : esc_html__( 'Shipping', 'yith-woocommerce-pdf-invoice' ); ?>
				</td>

				<?php if ( ywpi_is_enabled_column_quantity ( $document ) ) : ?>
					<td class="column-quantity">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_product_price ( $document ) ) : ?>

					<td class="column-price">
						<?php echo ( isset( $item['cost'] ) ) ? $invoice_details->get_order_currency_new( wc_round_tax_total ( $item['cost']) ) : ''; ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_regular_price ( $document ) ) : ?>
					<td class="column-price">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_sale_price ( $document ) ) : ?>
					<td class="column-price">
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_percentage ( $document ) ) : ?>
					<td class="column-discount-percentage"></td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_line_total ( $document ) ) : ?>
					<td class="column-price">
						<?php echo ( isset( $item['cost'] ) ) ? $invoice_details->get_order_currency_new( $item['cost'] ) : ''; ?>
					</td>
				<?php endif; ?>

				<?php

				if ( ywpi_is_enabled_column_tax ( $document ) ) : ?>
					<td class="column-price">
						<?php
						echo ( $invoice_details->get_order_currency_new( wc_round_tax_total ( $invoice_details->get_item_shipping_taxes ( $item ) ) ) );
						?>
					</td>
				<?php endif; ?>

                <?php if ( ywpi_is_enabled_column_percentage_tax ( $document ) && isset($item['cost']) ) : ?>
                    <td class="column-price">
                        <?php if( $item['cost'] != 0 && $item['cost'] != '' ):

                            $tax_percentage = ( ( $invoice_details->get_item_shipping_taxes ( $item ) ) * 100 ) / $item["cost"];

                            $precision = '1';

                            echo round( $tax_percentage, $precision ) . '%'; ?>

                        <?php else: ?>
                            <?php echo '0%'; ?>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>

				<?php if ( ywpi_is_enabled_column_total_taxed ( $document ) ) : ?>
					<td class="column-price">
						<?php echo $invoice_details->get_order_currency_new( $item["cost"] + $invoice_details->get_item_shipping_taxes ( $item ) ); ?>
					</td>
				<?php endif; ?>
			</tr>
			<?php
		};
	endif;

	?>
	</tbody>
</table>
