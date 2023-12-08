<?php

/**
 * @global array $items
 */
$items = $this->items;
?>
<table class="ac-table-items -clean -order-notes">
	<thead>
	<tr>
		<th class="col-note"><?= __('Note', 'codepress-admin-columns') ?></th>
		<th class="col-type"><?= __('Type', 'codepress-admin-columns') ?></th>
		<th class="col-date"><?= __('Date', 'codepress-admin-columns') ?></th>
	</tr>
	</thead>
	<tbody>
    <?php
    foreach ($this->items as $item) : ?>
		<tr class="ac-table-item <?= $item['class'] ?>">
			<td class="col-note">
                <?= $item['note'] ?>
			</td>
			<td class="col-type">
				<span class="ac-badge"><?= $item['type'] ?></span>
			</td>
			<td class="col-date">
                <?= $item['date'] ?>
			</td>
		</tr>
    <?php
    endforeach; ?>
	</tbody>
</table>