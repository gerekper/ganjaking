<?php
/**
 * @var string $title
 * @var array  $posts
 * @var array  $post_types
 */

$title = $this->title;
$items = $this->posts;
?>
<h3><?= $title ?></h3>
<table class="ac-table-items -user-posts">
	<tbody>
    <?php
    foreach ($this->posts as $post) : ?>
		<tr>
			<td class="col-id">
				#<?= $post['id'] ?>
			</td>
			<td class="col-title">
				<span><?= $post['post_title'] ?></span>
			</td>
			<td class="col-date">
                <?= $post['post_date'] ?>
			</td>
			<td class="col-post-type">
				<span class="ac-badge"><?= $post['post_type'] ?></span>
			</td>
		</tr>
    <?php
    endforeach; ?>
	</tbody>
</table>
<h3><?= __('Total items', 'codepress-admin-columns') ?></h3>
<?php
foreach ($this->post_types as $post_type) : ?>
	<a target="_blank" href="<?= $post_type['link'] ?>" class="ac-badge-post-count">
		<span class="-label"><?= $post_type['post_type'] ?></span>
		<span class="-count"><?= $post_type['count'] ?></span>
	</a>
<?php
endforeach; ?>


