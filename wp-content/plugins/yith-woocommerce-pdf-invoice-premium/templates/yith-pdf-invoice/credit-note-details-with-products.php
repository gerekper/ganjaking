<?php
/**
 * Credit note details with products template
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-details.php
 *
 * @author  YITH
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

$order           = $document->order; //phpcs:ignore
$parent_order    = wc_get_order( $order->get_parent_id() );
$invoice_details = new YITH_Invoice_Details( $document );

$template_selected = yith_ywpi_get_selected_template();

$table_header_color      = ( 'default' === $template_selected ) ? wp_kses_post( get_option( 'ywpi_table_header_color' ) ) : wp_kses_post( get_option( 'ywpi_table_header_color_' . $template_selected ) );
$table_header_font_color = ( 'default' === $template_selected ) ? wp_kses_post( get_option( 'ywpi_table_header_font_color' ) ) : wp_kses_post( get_option( 'ywpi_table_header_font_color_' . $template_selected ) );

$has_suborder = $order->get_meta( 'has_sub_order' );
$order_items  = $order->get_items();

$negative_symbol = strval( get_option( 'ywpi_credit_note_positive_values', 'no' ) ) === 'yes' ? '' : '-';

?>

<table class="invoice-details credit-note-product-table">
	<thead style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
	<tr style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
		<?php if ( ywpi_is_enabled_column_picture_credit_notes( $document ) ) : ?>
			<th class="column-picture" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Product', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>
		<?php if ( ywpi_is_enabled_column_product_name_credit_notes( $document ) || ywpi_is_enabled_column_sku_credit_notes( $document ) ) : ?>
			<th class="column-product" style="background-color: <?php echo esc_attr( $table_header_color ); ?>; color:<?php echo esc_attr( $table_header_font_color ); ?>">
				<?php if ( ! ywpi_is_enabled_column_picture_credit_notes( $document ) ) : ?>
					<?php esc_html_e( 'Product', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			</th>
		<?php endif; ?>


		<th class="column-refund-text" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Description', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<th class="column-refund-total" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
	</tr>
	</thead>
	<tbody>
	<?php

	// FULL REFUND.
	if ( floatval( $order->get_total() ) === ( $parent_order->get_total() * -1 ) ) {

		foreach ( $parent_order->get_items() as $item_id => $item ) {

			$_product = $invoice_details->get_item_product( $item );

			?>
			<tr>
				<!-- Show picture if related option is enabled -->
				<?php if ( ywpi_is_enabled_column_picture_credit_notes( $document ) ) : ?>
					<td class="column-picture">
						<?php
						$image_path = apply_filters( 'yith_ywpi_image_path', $invoice_details->get_product_image( $item ), $_product );
						if ( $image_path ) :
							?>
							<img class="product-image" src="<?php echo wp_kses_post( $image_path ); ?>"/>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<td class="column-product">
					<!-- Show product title -->
					<?php
					if ( ywpi_is_enabled_column_product_name_credit_notes( $document ) ) :
						echo esc_html( apply_filters( 'woocommerce_order_product_title', $item['name'], $_product ) );
					endif;
					?>
					<br>

					<?php if ( ywpi_is_enabled_column_variation( $document ) ) : ?>
						<?php echo wp_kses_post( urldecode( $invoice_details->get_variation_text( $item_id, $_product ) ) ); ?>
						<br>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_sku_credit_notes( $document ) ) : ?>
						<?php echo wp_kses_post( $invoice_details->get_sku_text( $item, $item_id ) ); ?>
					<?php endif; ?>


					<?php if ( ywpi_is_enabled_column_short_description( $document ) && ( $invoice_details->get_short_description( $item, $item_id ) !== '' ) ) : ?>
						<div
							class="product-short-description"><?php echo esc_html( $invoice_details->get_short_description( $item, $item_id ) ); ?></div>
					<?php endif; ?>

					<?php do_action( 'yith_ywpi_column_product_after_content', $document, $_product, $item_id ); ?>
				</td>

				<td class="column-refund-text">
					<i><?php echo wp_kses_post( $order->get_reason() ); ?></i>
				</td>
				<td class="column-refund-total">
					<?php echo wp_kses_post( $negative_symbol . $invoice_details->get_order_currency_new( $item['line_tax'] + $item['line_total'] ) ); ?>
				</td>
			</tr>

			<?php
		}
	} else {
		// Partial Refunds.
		foreach ( $order_items as $item_id => $item ) {
			$_product            = $invoice_details->get_item_product( $item );
			$item_qty_refunded   = $item->get_quantity();
			$item_value_refunded = $item->get_total();

			// Partial Refunds.
			?>
			<tr>
				<!-- Show picture if related option is enabled -->
				<?php if ( ywpi_is_enabled_column_picture_credit_notes( $document ) ) : ?>
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
					<?php
					if ( ywpi_is_enabled_column_product_name_credit_notes( $document ) ) :
						$item_name = apply_filters( 'woocommerce_order_item_name', $item['name'], $_product );
						$item_name = apply_filters( 'woocommerce_order_product_title', $item_name, $_product );

						echo esc_html( $item_name );
					endif;
					?>
					<br>

					<?php if ( ywpi_is_enabled_column_variation( $document ) ) : ?>
						<?php echo wp_kses_post( urldecode( $invoice_details->get_variation_text( $item_id, $_product ) ) ); ?>
						<br>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_sku_credit_notes( $document ) ) : ?>
						<?php echo wp_kses_post( $invoice_details->get_sku_text( $item, $item_id ) ); ?>
					<?php endif; ?>


					<?php if ( ywpi_is_enabled_column_short_description( $document ) && ( $invoice_details->get_short_description( $item, $item_id ) != '' ) ) : //phpcs:ignore?>
						<div class="product-short-description"><?php echo esc_html( $invoice_details->get_short_description( $item, $item_id ) ); ?></div>
					<?php endif; ?>

					<?php do_action( 'yith_ywpi_column_product_after_content', $document, $_product, $item_id ); ?>
				</td>

				<td class="column-refund-text">
					<i><?php echo esc_html( $order->get_reason() ); ?></i>
				</td>

				<td class="column-refund-total">
					<?php echo wp_kses_post( wc_price( $item->get_total() + $item->get_total_tax() ) ); ?>
				</td>
			</tr>

			<?php


		} // foreach;
	}

	?>
	</tbody>
</table>
