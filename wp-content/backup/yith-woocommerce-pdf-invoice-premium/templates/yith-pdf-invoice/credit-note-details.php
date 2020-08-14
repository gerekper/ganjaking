<?php
/**
 * The Template for invoice details
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-details.php
 *
 * @author        Yithemes
 * @package       yith-woocommerce-pdf-invoice-premium/Templates
 * @version       1.0.0
 */

/** @var YITH_Document $document */

$invoice_details = new YITH_Invoice_Details( $document );
/** @var WC_Order_Refund $order */

$order   = $document->order;
$invoice_details = new YITH_Invoice_Details( $document );
$wc_ge_3 = version_compare( WC()->version, '3.0', '>=' );

$table_header_color = get_option('ywpi_table_header_color');
$table_header_font_color = get_option('ywpi_table_header_font_color');
?>
<table class="credit-note-details">
	<thead style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>">
	<tr style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>">
		<th class="column-refund-text" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Description', 'yith-woocommerce-pdf-invoice' ); ?></th>
		
		<?php if ( ywpi_is_enabled_credit_note_subtotal_column( $document ) ) : ?>
			<th class="column-amount" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>
		
		<?php if ( ywpi_is_enabled_credit_note_total_tax_column( $document ) ) : ?>
			<th class="column-amount" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total tax', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

        <?php if ( ywpi_is_enabled_credit_note_total_shipping_column( $document ) ) : ?>
            <th class="column-amount" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total Shipping', 'yith-woocommerce-pdf-invoice' ); ?></th>
        <?php endif; ?>
		
		<?php if ( ywpi_is_enabled_credit_note_total_column( $document ) ) : ?>
			<th class="column-amount" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td class="column-refund-text">
			
			<?php
			$refund_text = get_option( 'ywpi_credit_note_refund_text', '' );
			echo $refund_text ? $refund_text : esc_html__( 'Your refund', 'yith-woocommerce-pdf-invoice' );
			
			if ( ywpi_is_enabled_credit_note_reason_column( $document ) ) : ?>
				<br>
				<i><?php echo $wc_ge_3 ? $order->get_reason() : $order->get_refund_reason(); ?></i>
			<?php endif; ?>
		</td>

        <?php

        if ( $order->get_parent_id() != 0 )
            $parent = new WC_Order( $order->get_parent_id() );
        else
            $parent = $order;

        ?>

		<?php if ( ywpi_is_enabled_credit_note_subtotal_column( $document ) ) : ?>
			<td class="column-amount">
				<?php echo $invoice_details->get_order_currency_new( $order->get_subtotal() ); ?>
			</td>
		<?php endif; ?>
		
		<?php if ( ywpi_is_enabled_credit_note_total_tax_column( $document ) ) : ?>
			<td class="column-amount">
				<?php echo $invoice_details->get_order_currency_new( $order->get_total_tax() ); ?>
			</td>
		<?php endif; ?>

        <?php if ( ywpi_is_enabled_credit_note_total_shipping_column( $document ) ) : ?>
            <td class="column-amount">
                <?php echo $invoice_details->get_order_currency_new( $order->get_shipping_total() ); ?>
            </td>
        <?php endif; ?>
		
		<?php if ( ywpi_is_enabled_credit_note_total_column( $document ) ) : ?>
			<td class="column-amount">
				<?php echo $invoice_details->get_order_currency_new( $order->get_total() ); ?>
			</td>
		<?php endif; ?>
	
	</tr>
	</tbody>
</table>

