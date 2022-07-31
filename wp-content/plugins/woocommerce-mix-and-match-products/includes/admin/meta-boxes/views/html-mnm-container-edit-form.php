<?php
/**
 * Mix and Match order item
 *
 * @package  WooCommerce Mix and Match Products/Admin/Meta-Boxes/Order/Views
 * 
 * @version 2.1.0
 *
 * @var WC+Product_Mix_and_Match $product The mix and match product
 * @var WC_Order_Item_Product $order_item The item being displayed
 * @var int $item_id The id of the item being displayed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form class="mnm_form edit_container layout_tabular" action="" method="post" enctype="multipart/form-data" >

	<table cellspacing="0" class="mnm_child_products">
		<thead>
			<th class="child_item_col child_item_images_head"></th>
			<th class="child_item_col child_item_details_head"><?php _e( 'Product', 'woocommerce-mix-and-match-products' ); ?></th>
			<th class="child_item_col child_item_qty_head"><?php _e( 'Quantity', 'woocommerce-mix-and-match-products' ); ?></th>
		</thead>
		<?php

		foreach ( $child_items as $child_item ) {
			do_action( 'wc_mnm_child_item_details', $child_item, $product );
		}

		?>
		</tbody>
	</table>

	<?php wc_mnm_template_reset_link(); ?>

    <div class="mnm_cart mnm_data cart" <?php echo $product->get_data_attributes( array( 'context' => 'edit' ) ); ?>>

		<div class="mnm_wrap mnm_button_wrap">

			<p class="mnm_price" style="display:none;"></p>

			<div class="mnm_message woocommerce-message" style="display:none;">
				<ul class="msg mnm_message_content">
					<li><?php echo wc_mnm_get_quantity_message( $product ); ?></li>
				</ul>
			</div>

			<?php

			// MnM Availability.
			?>
			<div class="mnm_availability">

				<?php

				// Availability html.
				echo wc_get_stock_html( $product );

				?>
				
			</div>

		</div>

	</div>

</form>