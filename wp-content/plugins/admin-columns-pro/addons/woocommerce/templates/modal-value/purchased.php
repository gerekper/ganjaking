<?php

/**
 * @global WC_Order_Item[] $items
 */
$items = $this->items;
?>
<table class="ac-table-items -clean -purchased">
	<thead>
	<tr>
		<th class="col-product"><?= __('Product', 'codepress-admin-columns') ?></th>
		<th class="col-qty"><?= __('Quantity', 'codepress-admin-columns') ?></th>
		<th class="col-total"><?= __('Tax', 'codepress-admin-columns') ?></th>
		<th class="col-total"><?= __('Total', 'codepress-admin-columns') ?></th>
	</tr>
	</thead>
	<tbody>
    <?php
    foreach ($this->items as $item) : ?>
		<tr>
			<td class="col-product">
				<div class="col-product__name">
                    <?= $item['name'] ?>

                    <?php
                    if ($item['sku']) : ?>
						<span class="ac-badge"><?= $item['sku'] ?></span>
                    <?php
                    endif; ?>
				</div>
				<div class="col-product__meta">
                    <?= $item['meta'] ?>
				</div>

			</td>
			<td class="col-qty">
                <?= $item['qty'] ?>
			</td>
			<td class="col-tax">
                <?= $item['tax'] ?>
			</td>
			<td class="col-total">
                <?= $item['total'] ?>
			</td>
		</tr>
    <?php
    endforeach; ?>
	</tbody>
</table>