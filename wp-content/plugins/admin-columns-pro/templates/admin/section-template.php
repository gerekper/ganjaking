<?php

declare(strict_types=1);

use ACP\Nonce\ImportFileNonce;

?>
<div class="ac-tool-section -presets">
	<h2 class="ac-lined-header">
        <?= esc_html__('Import Templates', 'codepress-admin-columns') ?>
	</h2>
	<p>
        <?= esc_html__('Select the table view template you would like to import.', 'codepress-admin-columns'); ?>
	</p>
	<table class="widefat fixed ac-table -preset">
		<thead>
		<tr>
			<th class="list-table"><?= __('List Table', 'codepress-admin-columns') ?></th>
			<th class="name"><?= __('Name', 'codepress-admin-columns') ?></th>
			<th class="source"><?= __('Source', 'codepress-admin-columns') ?></th>
			<th class="actions">&nbsp;</th>
		</tr>
		</thead>
		<tbody>
        <?php
        foreach ($this->list_items as $list_item) : ?>
			<tr>
				<td class="list-table">
                    <?= esc_html($list_item['label_page']) ?>
				</td>
				<td class="name">
                    <?= esc_html($list_item['label_view']) ?>
					<div class="-description">
                        <?= $list_item['description'] ?>
					</div>
				</td>
				<td class="source">
                    <?= $list_item['source'] ?>
				</td>
				<td class="actions">
					<form method="post">
						<a class="button" href="<?= esc_url($list_item['url_preview']) ?>">
                            <?= __('Preview', 'codepress-admin-columns') ?></a>

                        <?= (new ImportFileNonce())->create_field() ?>
						<input type="hidden" name="action" value="acp-import-file">
						<input type="hidden" name="file_name" value="<?= $list_item['file_name'] ?>">
						<button class="button button-primary"><?= __('Import', 'codepress-admin-columns') ?></button>
					</form>

				</td>
			</tr>
        <?php
        endforeach; ?>
		</tbody>
	</table>
</div>