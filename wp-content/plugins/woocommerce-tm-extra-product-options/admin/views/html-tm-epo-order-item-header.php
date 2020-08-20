<?php
/**
 * Shows an order item header
 *
 * @var object $item    The item being displayed
 * @var int    $item_id The id of the item being displayed
 *
 * @package Extra Product Options/Admin/Views
 * @version 4.8
 */

defined( 'ABSPATH' ) || exit;

$tax_data  = empty( $legacy_order ) && wc_tax_enabled() ? maybe_unserialize( isset( $item['line_tax_data'] ) ? $item['line_tax_data'] : '' ) : FALSE;
$row_class = apply_filters( 'woocommerce_admin_html_order_item_class', isset( $class ) && ! empty( $class ) ? $class : '', $item, $order );
?>
<tr class="tm-order-line <?php echo esc_attr( $row_class ); ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
	<?php echo ( version_compare( WC()->version, '2.6', '>=' ) ) ? '' : '<td class="check-column">&nbsp;</td>'; ?>
    <td class="thumb">&nbsp;</td>
    <td class="tm-c name" data-sort-value="<?php echo esc_attr( $item['name'] ); ?>">
        <div class="tm-view tm-order-header">
			<?php echo esc_html( $header_title ); ?>
        </div>
        <div class="tm-view tm-header">
            <div class="tm-50"><?php esc_html_e( 'Option', 'woocommerce-tm-extra-product-options' ); ?></div>
            <div class="tm-50"><?php esc_html_e( 'Value', 'woocommerce-tm-extra-product-options' ); ?></div>
        </div>
    </td>
	<?php

	do_action( 'woocommerce_admin_order_item_values', $_product, $item, 0 );

	?>
    <td class="item_cost" width="1%" data-sort-value="<?php echo esc_attr( $order->get_item_subtotal( $item, FALSE, TRUE ) ); ?>">
		<?php esc_html_e( 'Cost', 'woocommerce' ); ?>
    </td>
    <td class="quantity" width="1%">
		<?php esc_html_e( 'Qty', 'woocommerce' ); ?>
    </td>
    <td class="line_cost" width="1%" data-sort-value="<?php echo esc_attr( isset( $item['line_total'] ) ? $item['line_total'] : '' ); ?>">
		<?php esc_html_e( 'Total', 'woocommerce' ); ?>
    </td>
	<?php
	if ( ! empty( $tax_data ) ) {
		foreach ( $order_taxes as $tax_item ) {
			$column_label = ! empty( $tax_item['label'] ) ? $tax_item['label'] : esc_attr__( 'Tax', 'woocommerce' );
			?>
            <td class="line_tax" width="1%">
				<?php echo esc_html( $column_label ); ?>
            </td>
			<?php
		}
	}
	?>
    <td class="wc-order-edit-line-item" width="1%">
        &nbsp;
    </td>
</tr>

