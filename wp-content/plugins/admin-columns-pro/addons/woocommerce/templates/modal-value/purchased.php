<?php
/** @global WC_Order_Item[] $this- >items */
?>
<table class="ac-table-items -purchased">
	<thead>
	<tr>
		<th class="col-product"><?= __( 'Product', 'codepress-admin-columns' ) ?></th>
		<th class="col-qty"><?= __( 'Quantity', 'codepress-admin-columns' ) ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $this->items as $item ) : ?>

		<?php if ( $item instanceof WC_Order_Item_Product ) : ?>

			<?php
			$product = $item->get_product();
			$meta = $item->get_formatted_meta_data();
			?>

			<tr>
				<td class="col-product">
					<div class="col-product__name">
						<?php if ( $product ): ?>

							<?= ac_helper()->html->link( get_edit_post_link( $product->get_id() ), $item->get_name() ) ?>

							<?php if ( wc_product_sku_enabled() && $product->get_sku() ): ?>
								<span class="ac-badge"><?= $product->get_sku() ?></span>
							<?php endif; ?>

						<?php else: ?>
							<?= $item->get_name() ?>
						<?php endif; ?>
					</div>
					<div class="col-product__meta">
						<?php foreach ( $meta as $meta_item ): ?>
							<strong><?= $meta_item->display_key ?> :</strong>
							<?= $meta_item->value ?><br>
						<?php endforeach; ?>
					</div>

				</td>
				<td class="col-qty">
					<?php echo $item->get_quantity(); ?>x
				</td>
			</tr>
		<?php endif; ?>
	<?php endforeach; ?>
	</tbody>
</table>