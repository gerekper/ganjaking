<?php
/**
 * Credit note details template.
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-details.php
 *
 * @author  YITH
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

$order           = $document->order; //phpcs:ignore
$invoice_details = new YITH_Invoice_Details( $document );

$template_selected = yith_ywpi_get_selected_template();

$table_header_color      = ( 'default' === $template_selected ) ? wp_kses_post( get_option( 'ywpi_table_header_color' ) ) : wp_kses_post( get_option( 'ywpi_table_header_color_' . $template_selected ) );
$table_header_font_color = ( 'default' === $template_selected ) ? wp_kses_post( get_option( 'ywpi_table_header_font_color' ) ) : wp_kses_post( get_option( 'ywpi_table_header_font_color_' . $template_selected ) );

$negative_value = strval( get_option( 'ywpi_credit_note_positive_values', 'no' ) ) === 'yes' ? '-1' : '1';

?>
<table class="credit-note-details">
	<thead style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
	<tr style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
		<th class="column-refund-text" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Description', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>

		<?php if ( ywpi_is_enabled_credit_note_subtotal_column( $document ) ) : ?>
			<th class="column-amount" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_credit_note_total_tax_column( $document ) ) : ?>
			<th class="column-amount" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total tax', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_credit_note_total_shipping_column( $document ) ) : ?>
			<th class="column-amount" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total Shipping', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_credit_note_total_column( $document ) ) : ?>
			<th class="column-amount" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total', 'yith-woocommerce-pdf-invoice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
		<?php endif; ?>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td class="column-refund-text">

			<?php
			$refund_text = get_option( 'ywpi_credit_note_refund_text', '' );
			echo esc_html( $refund_text ? $refund_text : __( 'Your refund', 'yith-woocommerce-pdf-invoice' ) );

			if ( ywpi_is_enabled_credit_note_reason_column( $document ) ) :
				?>
				<br>
				<i><?php echo esc_html( $order->get_reason() ); ?></i>
			<?php endif; ?>
		</td>

		<?php

		if ( intval( $order->get_parent_id() ) !== 0 ) {
			$parent = new WC_Order( $order->get_parent_id() );
		} else {
			$parent = $order;
		}

		?>

		<?php if ( ywpi_is_enabled_credit_note_subtotal_column( $document ) ) : ?>
			<td class="column-amount">
				<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order->get_subtotal() * $negative_value ) ); ?>
			</td>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_credit_note_total_tax_column( $document ) ) : ?>
			<td class="column-amount">
				<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order->get_total_tax() * $negative_value ) ); ?>
			</td>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_credit_note_total_shipping_column( $document ) ) : ?>
			<td class="column-amount">
				<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order->get_shipping_total() * $negative_value ) ); ?>
			</td>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_credit_note_total_column( $document ) ) : ?>
			<td class="column-amount">
				<?php echo wp_kses_post( $invoice_details->get_order_currency_new( $order->get_total() * $negative_value ) ); ?>
			</td>
		<?php endif; ?>

	</tr>
	</tbody>
</table>

