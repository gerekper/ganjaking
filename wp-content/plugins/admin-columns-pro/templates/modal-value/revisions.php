<?php
/**
 * @var string  $title
 * @var WP_Post $revisions
 */

$title = $this->title;
$revisions = $this->revisions;
?>
	<h3><?= $title ?></h3>
<?php
foreach ($this->revisions as $revision) : ?>
	<div class="acp-row-revision"><?= wp_post_revision_title_expanded($revision) ?></div>
<?php
endforeach; ?>