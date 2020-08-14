<?php

$request = new YITH_Refund_Request( $request_id );
if ( ! ( $request instanceof YITH_Refund_Request && $request->exists() ) ) {
	return;
}
$order = wc_get_order( $request->order_id );
$order_total = $order->get_total() - $order->get_total_refunded();
$tax_enabled = wc_tax_enabled() && 'yes' == get_option( 'yith_wcars_enable_taxes' );
?>

<style>
    table.ywcars_items_table, .ywcars_items_table th, .ywcars_items_table td {
        border: 1px solid darkgrey;
    }
    table.ywcars_items_table {
        border-collapse: collapse;
    }
</style>

<div class="ywcars_items_info">
    <table class="ywcars_items_table">
        <thead>
        <tr>
            <th><?php esc_html_e( 'Item', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
            <th><?php esc_html_e( 'Item value', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
		    <?php if ( $tax_enabled ) : ?>
                <th><?php esc_html_e( 'Tax per item', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
		    <?php endif; ?>
            <th class="ywcars_items_table_totals ywcars_items_table_refund"><?php esc_html_e( 'Qty to refund', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
            <th class="ywcars_items_table_totals ywcars_items_table_refund"><?php esc_html_e( 'Total to be refunded', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $items = $order->get_items();
        if ( $items ) {
	        foreach ( $items as $item_id => $item ) {
		        $product_id = empty( $item['variation_id'] ) ? $item['product_id'] : $item['variation_id'];
		        if ( ! $request->whole_order && $request->product_id != $product_id ) {
	                continue;
                }
		        $product = wc_get_product( $product_id );

		        if ( ! $product || ! $product->exists() ) {
			        ?>
                    <tr>
                        <td><?php esc_html_e( 'The requested product does not exist.', 'yith-advanced-refund-system-for-woocommerce' ); ?></td>
                    </tr>
			        <?php
			        continue;
		        }

		        $qty_requested = '0';
		        if ( $request->whole_order ) {
			        $qty_requested = $item['qty'];
		        }
		        if ( $item_id == $request->item_id ) {
			        $qty_requested = $request->qty;
		        }

		        $item_value   = $item['line_total'] / $item['qty'];
		        $total_refund = $item_value * $qty_requested;
		        if ( $tax_enabled ) {
			        $item_and_tax_value = ( $item['line_total'] + $item['line_tax'] ) / $item['qty'];
			        $tax_value          = $item['line_tax'] / $item['qty'];
			        $total_refund       = $item_and_tax_value * $qty_requested;
		        }

		        ?>
                <tr>
                    <td><?php echo $product->get_title(); ?></td>
                    <td><?php echo wc_price( $item_value, array( 'currency' => $order->get_currency() ) ); ?></td>
			        <?php if ( $tax_enabled ) : ?>
                        <td><?php echo wc_price( $tax_value, array( 'currency' => $order->get_currency() ) ); ?></td>
			        <?php endif; ?>
                    <td class="ywcars_items_table_totals ywcars_items_table_refund ywcars_item_data">
				        <?php echo $qty_requested; ?>
                    </td>
                    <td class="ywcars_items_table_totals ywcars_items_table_refund ywcars_refund_subtotal_data">
				        <?php echo wc_price( $total_refund, array( 'currency' => $order->get_currency() ) ); ?>
                    </td>
                </tr>
		        <?php
	        }
        }
        ?>
        </tbody>
    </table>
</div>