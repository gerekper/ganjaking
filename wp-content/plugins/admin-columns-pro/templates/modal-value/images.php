<?php
/**
 * @var string  $title
 * @var array[] $items
 */

$title = $this->title;
$items = $this->items;
?>

<div class="ac-modal-images-container">
    <?php
    foreach ($this->items as $item) : ?>
		<div class="ac-image-container">
			<div class="ac-image-center">
				<img alt="<?= esc_attr($item['alt']) ?>" src="<?= esc_url($item['img_src']) ?>">
			</div>
			<div class="ac-image-meta">
                <?php
                if ($item['filename']) : ?>
					<div class="ac-image-meta-item -filename">
                        <?= __('File name', 'codepress-admin-columns') ?>: <strong><?= $item['filename'] ?></strong>
					</div>
                <?php
                endif ?>
                <?php
                if ($item['filetype']) : ?>
					<div class="ac-image-meta-item -filetype">
                        <?= __('File type', 'codepress-admin-columns') ?>: <strong><?= $item['filetype'] ?></strong>
					</div>
                <?php
                endif ?>
                <?php
                if ($item['filesize']) : ?>
					<div class="ac-image-meta-item -filesize">
                        <?= __('File size', 'codepress-admin-columns') ?>: <strong><?= $item['filesize'] ?></strong>
					</div>
                <?php
                endif ?>
                <?php
                if ($item['dimensions']) : ?>
					<div class="ac-image-meta-item -dimensions">
                        <?= __('Dimensions', 'codepress-admin-columns') ?>:
						<strong><?= $item['dimensions'] ?></strong>
					</div>
                <?php
                endif ?>
				<div class="ac-image-meta-item -download">
					<a download href="<?= $item['img_src'] ?>"><?= __('Download file') ?></a>
				</div>
                <?php
                if ($item['edit_url']) : ?>
					<div class="ac-image-meta-item -edit">
						<a target="_blank" href="<?= $item['edit_url'] ?>"><?= __('Edit file') ?></a>
					</div>
                <?php
                endif ?>
			</div>
		</div>
    <?php
    endforeach; ?>
</div>
