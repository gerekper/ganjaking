<?php
/**
 * Template to render the product table
 *
 * @package YITH\PDFInvoice\Templates
 */

$current_order   = $resource->order;
$has_suborder    = $current_order->get_meta( 'has_sub_order' );
$invoice_details = new YITH_Invoice_Details( $resource );
$negative_amount = 1;
$parent_order    = wc_get_order( $current_order->get_parent_id() );
$reason          = '';

if ( $resource instanceof YITH_Credit_Note ) {
	$negative_amount = 'yes' === get_option( 'ywpi_credit_note_positive_values_builder', 'no' ) ? 1 : - 1;
	$reason          = is_callable( array( $current_order, 'get_reason' ) ) ? $current_order->get_reason() : '';

	if ( $parent_order && intval( $current_order->get_total() ) === intval( $parent_order->get_total() * - 1 ) ) {
		// Full refund, we can show all items.
		$current_order = $parent_order;
	} else {
		// On refund orders the totals are negative, so it is necessary to change the sign.
		$negative_amount *= -1;
	}

	$order_items    = $current_order->get_items();
	$order_shipping = $current_order->get_items( 'shipping' );
	$order_fees     = $current_order->get_items( 'fee' );
} else {
	$order_items    = ! $has_suborder ? $invoice_details->get_order_items() : $current_order->get_items();
	$order_shipping = $invoice_details->get_order_shipping();
	$order_fees     = $invoice_details->get_order_fees();
}

$size = count( $order_items + $order_shipping + $order_fees );
$i    = 0;

foreach ( $order_items as $item_id => $item ) {
	/**
	 * Current product
	 *
	 * @var WC_Product $product
	 */
	$product  = $invoice_details->get_item_product( $item );
	$tr_class = ++$i === $size ? 'class="last"' : '';

	?>
	<tr <?php echo wp_kses_post( $tr_class ); ?>>
		<?php
		if ( isset( $attr['thumbnails'] ) && $attr['thumbnails'] ) {
			$image_path = apply_filters( 'yith_ywpi_image_path', $invoice_details->get_product_image( $item ), $product );
			if ( $image_path ) :
				?>
					<td class="thumbnail"><img src="<?php echo esc_url( $image_path ); ?>" class="thumbnail-img"/></td>
				<?php
			endif;
			/**
			 * DO_ACTION: yith_ywpi_after_product_image
			 *
			 * Section after display the product image in the invoice data.
			 *
			 * @param   object  $_product  the product object.
			 * @param   object  $resource  the document object.
			 */
			do_action( 'yith_ywpi_after_product_image', $product, $resource, $item_id );
		}

		if ( isset( $attr['productName'] ) && $attr['productName'] ) :
			?>
			<td>
				<span class="ywpi-product-text"><?php echo wp_kses_post( apply_filters( 'woocommerce_order_product_title', $item['name'], $product ) ); ?></span>
				<?php
				/**
				 * DO_ACTION: yith_ywpi_after_product_name
				 *
				 * Section after display the product name in the invoice data.
				 *
				 * @param   object  $_product  the product object.
				 * @param   object  $resource  the document object.
				 */
				do_action( 'yith_ywpi_after_product_name', $product, $resource, $item_id );
				?>
				<br>
				<small><?php echo wp_kses_post( urldecode( $invoice_details->get_variation_text( $item_id, $product ) ) ); ?></small>

				<?php
				$sku_text = $invoice_details->get_sku_text( $item, $item_id );

				if ( ! empty( $sku_text ) && ( ! isset( $attr['productSku'] ) || $attr['productSku'] ) ) :
					?>
					<br>
					<small class="ywpi-sku-text"><?php echo wp_kses_post( $sku_text ); ?></small>
				<?php endif; ?>

				<?php
				if ( ! ! $product && is_object( $product ) ) {
					$short_description = wc_format_content( wp_kses_post( $product->get_short_description() ? $product->get_short_description() : wc_trim_string( $product->get_description(), 200 ) ) );

					if ( ! empty( $short_description ) && isset( $attr['productDescription'] ) && $attr['productDescription'] ) :
						?>
						<div class="product-short-description"><small><?php echo wp_kses_post( $short_description ); ?></small></div>
						<?php
					endif;

					if ( $attr['invoiceType'] ) :
						$has_dimensions = ( $product->get_length() || $product->get_height() || $product->get_width() ) && ! $product->is_virtual();

						if ( $product->has_weight() && isset( $attr['productWeight'] ) && $attr['productWeight'] ) :
							?>
							<br/><small> <?php echo esc_html__( ' Weight:', 'yith-woocommerce-pdf-invoice' ) . ' ' . esc_html( $product->get_weight() ) . ' ' . esc_html( get_option( 'woocommerce_weight_unit' ) ); ?></small>
							<?php
						endif;

						$dimensions = wc_format_dimensions( $product->get_dimensions( false ) );

						if ( ! empty( $dimensions ) && 'N/A' !== $dimensions && isset( $attr['productDimensions'] ) && $attr['productDimensions'] ) :
							?>
							<br/><small> <?php echo esc_html__( ' Dimensions:', 'yith-woocommerce-pdf-invoice' ) . ' ' . esc_html( $dimensions ); ?></small>
							<?php
						endif;
					endif;
					/**
					 * DO_ACTION: yith_ywpi_column_product_after_content
					 *
					 * Section after the product content in the invoice product column.
					 *
					 * @param object $resource the document object
					 * @param object $_product the product object
					 * @param object $item_id the item ID
					 */
					do_action( 'yith_ywpi_column_product_after_content', $resource, $product, $item_id, $item );
				}
				?>
			</td>
		<?php endif; ?>

		<?php if ( ! $attr['invoiceType'] && $attr['refundDescription'] ) : ?>
			<td class="refund-description"><i><?php echo wp_kses_post( $reason ); ?></i></td>
		<?php endif; ?>

		<?php if ( $attr['invoiceType'] && $attr['quantity'] ) : ?>
			<td class="quantity number"><?php echo ( isset( $item['qty'] ) ) ? esc_html( $item['qty'] ) : ''; ?></td>
		<?php endif; ?>

		<?php if ( $attr['invoiceType'] && $attr['regularPrice'] ) : ?>
			<td class="product-price number"><?php echo wp_kses_post( wc_price( $invoice_details->get_item_product_regular_price( $item, $item_id ) ) ); ?></td>
		<?php endif; ?>

		<?php if ( $attr['invoiceType'] && $attr['salePrice'] ) : ?>
			<td class="product-price number"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit_sale( $item ) ) ); ?></td>
		<?php endif; ?>

		<?php if ( $attr['invoiceType'] && $attr['price'] ) : ?>
			<td class="product-price number">
				<?php
				if ( apply_filters( 'yith_ywpi_invoice_round_price', $invoice_details->get_item_product_regular_price( $item, $item_id ) > round( $invoice_details->get_item_price_per_unit( $item ), 2 ) ) ) {
					?>
					<span class="old-price">
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

		<?php if ( $attr['invoiceType'] && $attr['discountPercentage'] ) : ?>
			<td class="subtotal number"><?php echo wp_kses_post( $invoice_details->get_item_percentage_discount( $item ) ); ?></td>
		<?php endif; ?>

		<?php if ( $attr['invoiceType'] && $attr['tax'] ) : ?>
			<td class="product-price number"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_tax'] ) ); ?></td>
		<?php endif; ?>

		<?php if ( $attr['invoiceType'] && $attr['percentageTax'] ) : ?>
			<td class="product-price number">
				<?php
				if ( 0 !== floatval( $item['line_total'] ) && '' !== $item['line_total'] ) :
					/**
					 * APPLY_FILTERS: yith_ywpi_apply_old_percentage_tax_calculation
					 *
					 * Filter the condition to calculate the taxes with legacy method.
					 *
					 * @param   bool true to use the legacy calculation, false to not.
					 *
					 * @return bool
					 */
					if ( ! is_object( $product ) || apply_filters( 'yith_ywpi_apply_old_percentage_tax_calculation', false ) ) {
						$tax_percentage = $item['line_tax'] * 100 / $item['line_total'];
						$precision      = apply_filters( 'yith_ywpi_apply_old_percentage_tax_calculation_precision', '1' );

						echo wp_kses_post( round( $tax_percentage, $precision ) . '%' );
					} else {
						$tax = new WC_Tax(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

						if ( ! ! $product && is_object( $product ) ) {
							$taxes = $tax->get_rates( $product->get_tax_class() );
						}

						$taxes = is_array( $taxes ) ? $taxes : array();

						$rates = ! empty( $taxes ) ? array_shift( $taxes ) : array();
						// Take only the item rate and round it.
						$item_rate = round( array_shift( $rates ) );

						echo wp_kses_post( $item_rate ) . '%';
					}

					?>
				<?php else : ?>
					<?php echo '0%'; ?>
				<?php endif; ?>
			</td>
		<?php endif; ?>

		<?php if ( $attr['productSubtotal'] ) : ?>
			<td class="subtotal number"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $negative_amount * $item['line_total'] ) ); ?></td>
		<?php endif; ?>

		<?php if ( $attr['invoiceType'] && $attr['productTotal'] ) : ?>
			<td class="subtotal number"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_tax'] + $item['line_total'] ) ); ?></td>
		<?php endif; ?>
	</tr>
	<?php
}//end foreach

/**
 * APPLY_FILTERS: ywpi_is_visible_fee_details_section
 *
 * Filter the condition show the fees section in the invoice.
 *
 * @param   bool $show_fee_items True to show it, false to not.
 * @param   object  $resource  the document object.
 *
 * @return bool
 */
if ( apply_filters( 'ywpi_is_visible_fee_details_section', isset( $attr['feeItems'] ) && $attr['feeItems'], $resource ) ) :
	foreach ( $order_fees as $item_id => $item ) :
		$tr_class = ++$i === $size ? 'class="last"' : '';

		?>
		<tr <?php echo wp_kses_post( $tr_class ); ?>>
			<?php if ( isset( $attr['thumbnails'] ) && $attr['thumbnails'] ) : ?>
				<td class="thumbnail"></td>
				<?php
			endif;

			if ( isset( $attr['productName'] ) && $attr['productName'] ) :
				?>
				<td>
					<span class="ywpi-product-text"><?php echo ! empty( $item['name'] ) ? esc_html( $item['name'] ) : esc_html__( 'Fee', 'yith-woocommerce-pdf-invoice' ); ?></span>
				</td>
			<?php endif; ?>
			<?php if ( ! $attr['invoiceType'] && $attr['refundDescription'] ) : ?>
				<td class="refund-description"><?php echo wp_kses_post( $reason ); ?></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['quantity'] ) : ?>
				<td class="quantity number"></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['regularPrice'] ) : ?>
				<td class="product-price number"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_total'] ) ); ?></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['salePrice'] ) : ?>
				<td class="product-price number"></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['price'] ) : ?>
				<td class="product-price number"></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['discountPercentage'] ) : ?>
				<td class="subtotal number"></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['tax'] ) : ?>
				<td class="product-price number"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_tax'] ) ); ?></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['percentageTax'] ) : ?>
				<td class="product-price number">
					<?php
					if ( 0 !== floatval( $item['line_total'] ) && '' !== $item['line_total'] ) :
						$tax_percentage = $item['line_tax'] * 100 / $item['line_total'];

						echo wp_kses_post( round( $tax_percentage, 0 ) . '%' );
						?>
					<?php else : ?>
						<?php echo '0%'; ?>
					<?php endif; ?>
				</td>
			<?php endif; ?>
			<?php if ( $attr['productSubtotal'] ) : ?>
				<td class="subtotal number"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $negative_amount * $item['line_total'] ) ); ?></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['productTotal'] ) : ?>
				<td class="subtotal number"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['line_tax'] + $item['line_total'] ) ); ?></td>
			<?php endif; ?>
		</tr>
		<?php
	endforeach;   // foreach.
endif;

/**
 * APPLY_FILTERS: ywpi_is_visible_shipping_details_section
 *
 * Filter the condition show the shipping section in the invoice.
 *
 * @param   bool $show_shipping True to show it, false to not.
 * @param   object  $resource  The document object.
 *
 * @return bool
 */
if ( apply_filters( 'ywpi_is_visible_shipping_details_section', isset( $attr['shippingItems'] ) && $attr['shippingItems'], $resource ) ) :
	foreach ( $order_shipping as $item_id => $item ) :
		$tr_class = ++$i === $size ? 'class="last"' : '';

		?>
		<tr <?php echo wp_kses_post( $tr_class ); ?>>
			<?php if ( isset( $attr['thumbnails'] ) && $attr['thumbnails'] ) : ?>
				<td class="thumbnail"></td>
			<?php endif; ?>
			<?php if ( isset( $attr['productName'] ) && $attr['productName'] ) : ?>
				<td>
					<span class="ywpi-product-text"><?php echo wp_kses_post( ! empty( $item['name'] ) ? esc_html( $item['name'] ) : esc_html__( 'Shipping', 'yith-woocommerce-pdf-invoice' ) ); ?></span>
				</td>
			<?php endif; ?>

			<?php if ( ! $attr['invoiceType'] && $attr['refundDescription'] ) : ?>
				<td class="refund-description"><?php echo wp_kses_post( $reason ); ?></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['quantity'] ) : ?>
				<td class="quantity number"></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['regularPrice'] ) : ?>
				<td class="product-price number"><?php echo wp_kses_post( ( isset( $item['cost'] ) ) ? $invoice_details->get_order_currency_new( wc_round_tax_total( $item['cost'] ) ) : '' ); ?></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['salePrice'] ) : ?>
				<td class="product-price number"></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['price'] ) : ?>
				<td class="product-price number"></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['discountPercentage'] ) : ?>
				<td class="subtotal number"></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['tax'] ) : ?>
				<td class="product-price number"> <?php echo wp_kses_post( $invoice_details->get_order_currency_new( wc_round_tax_total( $invoice_details->get_item_shipping_taxes( $item ) ) ) ); ?></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['percentageTax'] ) : ?>
			<td class="subtotal number">
				<?php
				if ( ! empty( $item['cost'] ) && 0 !== floatval( $item['cost'] ) ) :
					$tax_percentage = ( ( $invoice_details->get_item_shipping_taxes( $item ) ) * 100 ) / $item['cost'];

					$precision = '1';

					echo wp_kses_post( round( $tax_percentage, $precision ) . '%' );
					?>
				<?php else : ?>
					<?php echo '0%'; ?>
				<?php endif; ?>
			</td>
			<?php endif; ?>
			<?php if ( $attr['productSubtotal'] ) : ?>
				<td class="subtotal number"><?php echo wp_kses_post( ( isset( $item['cost'] ) ) ? $invoice_details->get_order_currency_new( $negative_amount * $item['cost'] ) : '' ); ?></td>
			<?php endif; ?>

			<?php if ( $attr['invoiceType'] && $attr['productTotal'] ) : ?>
				<td class="subtotal number"><?php echo wp_kses_post( $invoice_details->get_order_currency_new( $item['cost'] + $invoice_details->get_item_shipping_taxes( $item ) ) ); ?></td>
			<?php endif; ?>
		</tr>
		<?php
	endforeach;
endif;
