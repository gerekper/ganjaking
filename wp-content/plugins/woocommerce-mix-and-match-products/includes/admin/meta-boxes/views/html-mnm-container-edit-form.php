<?php
/**
 * Mix and Match order item
 *
 * @var object $item The item being displayed
 * @var int $item_id The id of the item being displayed
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table cellspacing="0" class="mnm_child_products">
	<thead>
		<th class="child_item_col child_item_images_head"></th>
		<th class="child_item_col child_item_details_head"><?php _e( 'Product', 'woocommerce-mix-and-match-products' ); ?></th>
		<th class="child_item_col child_item_qty_head"><?php _e( 'Quantity', 'woocommerce-mix-and-match-products' ); ?></th>
	</thead><?php

	foreach ( $child_items as $child_item ) {
		do_action( 'woocommerce_mnm_child_item_details', $child_item, $product );
	}

	?></tbody>
</table>
