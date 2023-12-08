<table class="ac-table-items -clean -purchased">
	<thead>
	<tr>
		<th class="col-product"><?= __('Product', 'codepress-admin-columns') ?></th>
		<th class="col-qty"><?= __('Quantity', 'codepress-admin-columns') ?></th>
	</tr>
	</thead>
	<tbody>
    <?php
    foreach ($this->items as $item) : ?>

		<tr>
			<td class="col-product">
				<div class="col-product__name">
                    <?php
                    if ($item['link']) : ?>
						<a href="<?= $item['link'] ?>">
                            <?= $item['title'] ?>

						</a>
                    <?php
                    else: ?>
                        <?= $item['title'] ?>
						<span style="color: #999; transform: translateY(-1px); display: inline-block;">
							<?= ac_helper()->icon->dashicon(['icon' => 'warning']) ?>
						</span>
                    <?php
                    endif; ?>
				</div>
			</td>
			<td class="col-qty">
                <?= $item['total']; ?>
			</td>
		</tr>

    <?php
    endforeach; ?>
	</tbody>
</table>