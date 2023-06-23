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
 * @version 6.21.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><label class="bundled_product_optional_checkbox">
	<input class="bundled_product_checkbox" type="checkbox" name="<?php echo esc_attr( $bundle_fields_prefix ); ?>bundle_selected_optional_<?php echo esc_attr( $bundled_item->get_id() ); ?>" value="" <?php checked( $bundled_item->is_optional_checked() && $bundled_item->is_in_stock(), true ); echo $bundled_item->is_in_stock() ? '' : 'disabled="disabled"' ; ?> /> <?php
	/* translators: %1$s: Product title %, %2$s: Product price, %3$s: Deprecated */
	echo wp_kses_post( sprintf( __( 'Add%1$s%2$s%3$s', 'woocommerce-product-bundles' ), $label_title, $label_price, '' ) );
?></label><?php

if ( $availability_html ) {
	echo $availability_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
