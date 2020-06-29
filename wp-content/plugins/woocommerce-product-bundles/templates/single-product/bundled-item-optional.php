<?php
/**
 * Optional Bundled Item Checkbox template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-item-optional.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 5.0.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><label class="bundled_product_optional_checkbox">
	<input class="bundled_product_checkbox" type="checkbox" name="<?php echo $bundle_fields_prefix; ?>bundle_selected_optional_<?php echo $bundled_item->get_id(); ?>" value="" <?php checked( $bundled_item->is_optional_checked() && $bundled_item->is_in_stock(), true ); echo $bundled_item->is_in_stock() ? '' : 'disabled="disabled"' ; ?> /> <?php

	$price_html         = $bundled_item->product->get_price_html();
	$label_price        = $bundled_item->is_priced_individually() && $price_html ? sprintf( __( ' for %s', 'woocommerce-product-bundles' ), '<span class="price">' . $price_html . '</span>' ) : '';
	$label_title        = $bundled_item->get_title() === '' ? sprintf( __( ' &quot;%s&quot;', 'woocommerce-product-bundles' ), WC_PB_Helpers::format_product_shop_title( $bundled_item->get_raw_title(), ( $quantity > 1 && $bundled_item->get_quantity( 'max' ) === $quantity ) ? $quantity : '' ) ) : '';
	$label_stock_status = '';

	if ( false === $bundled_item->is_in_stock() ) {

		$availability      = $bundled_item->get_availability();
		$availability_html = empty( $availability[ 'availability' ] ) ? '' : esc_html( $availability[ 'availability' ] );
		if ( $availability_html ) {
			$label_stock_status = sprintf( _x( ' &mdash; %s', 'optional label stock status', 'woocommerce-product-bundles' ), '<span class="bundled_item_stock_label stock out-of-stock">' . $availability_html . '</span>' );
		}
	}

	echo sprintf( __( 'Add%1$s%2$s%3$s', 'woocommerce-product-bundles' ), $label_title, $label_price, $label_stock_status );

	?>
</label>
