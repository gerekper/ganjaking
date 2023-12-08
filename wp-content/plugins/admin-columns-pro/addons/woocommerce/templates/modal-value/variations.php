<?php

/**
 * @var string $title
 * @var array  $items
 */
$title = $this->title;
$items = $this->items;
?>
<table class="ac-table-items -clean -variations">
	<thead>
	<tr>
		<th class="col-name"><?= sprintf(
                '%s (%d)',
                _n('Variation', 'Variations', count($items), 'codepress-admin-columns'),
                count($items)
            ) ?></th>
		<th class="col-attributes"><?= __('Attributes', 'codepress-admin-columns') ?></th>
		<th class="col-stock"><?= __('Stock', 'codepress-admin-columns') ?></th>
	</tr>
	</thead>
	<tbody>
    <?php
    foreach ($this->items as $item) : ?>
		<tr>
			<td class="col-name">
                <?= $item['name'] ?>
                <?php
                if ($item['sku']) : ?>
					<span class="ac-badge"><?= $item['sku'] ?></span>
                <?php
                endif; ?>
			</td>
			<td class="col-attributes">
                <?= $item['attributes'] ?>
			</td>
			<td class="col-stock">
                <?= $item['stock'] ?>
			</td>
		</tr>
    <?php
    endforeach; ?>
	</tbody>
</table>