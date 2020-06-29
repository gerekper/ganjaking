<?php
/**
 * Bundled order item
 *
 * @var object $item The item being displayed
 * @var int $item_id The id of the item being displayed
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table cellspacing="0" class="bundled_products">
	<thead>
		<th class="bundled_item_col bundled_item_images_head"></th>
		<th class="bundled_item_col bundled_item_details_head"><?php _e( 'Product', 'woocommerce-product-bundles' ); ?></th>
		<th class="bundled_item_col bundled_item_qty_head"><?php _e( 'Quantity', 'woocommerce-product-bundles' ); ?></th>
	</thead><?php

	foreach ( $bundled_items as $bundled_item ) {
		do_action( 'woocommerce_bundled_item_details', $bundled_item, $product );
	}

	?></tbody>
</table>
