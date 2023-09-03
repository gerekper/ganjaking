<?php
/**
 * Bundled order item
 *
 * @var object $item The item being displayed
 * @var int $item_id The id of the item being displayed
 *
 * @version  6.21.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table cellspacing="0" class="bundled_products">
	<thead>
		<th class="bundled_item_col bundled_item_images_head"></th>
		<th class="bundled_item_col bundled_item_details_head"><?php esc_html_e( 'Product', 'woocommerce-product-bundles' ); ?></th>
		<th class="bundled_item_col bundled_item_qty_head"><?php esc_html_e( 'Quantity', 'woocommerce-product-bundles' ); ?></th>
	</thead><?php

	// All bundled items (including hidden ones) should be vidible in the order edit page.
	add_filter( 'woocommerce_bundles_bundled_item_visibility', '__return_true' );

	foreach ( $bundled_items as $bundled_item ) {
		do_action( 'woocommerce_bundled_item_details', $bundled_item, $product );
	}

	remove_filter( 'woocommerce_bundles_bundled_item_visibility', '__return_true' );

	?></tbody>
</table>
