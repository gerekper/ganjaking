<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr>
	<td><input type="text" name="product_addon_option_label[<?php echo $loop; ?>][]" value="<?php echo esc_attr( $option['label'] ); ?>" placeholder="<?php esc_html_e( 'Default Label', 'woocommerce-product-addons' ); ?>" /></td>
	<td class="price_column"><input type="text" name="product_addon_option_price[<?php echo $loop; ?>][]" value="<?php echo esc_attr( wc_format_localized_price( $option['price'] ) ); ?>" placeholder="0.00" class="wc_input_price" /></td>

	<td class="minmax_column">
		<input type="number" name="product_addon_option_min[<?php echo $loop; ?>][]" value="<?php echo esc_attr( $option['min'] ) ?>" placeholder="Min" min="0" step="any" />
		<input type="number" name="product_addon_option_max[<?php echo $loop; ?>][]" value="<?php echo esc_attr( $option['max'] ) ?>" placeholder="Max" min="0" step="any" />
	</td>

	<?php do_action( 'woocommerce_product_addons_panel_option_row', isset( $post ) ? $post : null, $product_addons, $loop, $option ); ?>

	<td class="actions" width="1%"><button type="button" class="remove_addon_option button">x</button></td>
</tr>
