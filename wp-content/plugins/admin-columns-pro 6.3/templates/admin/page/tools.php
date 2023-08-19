<h1 class="screen-reader-text"><?= __( 'Tools', 'codepress-admin-columns' ) ?></h1>
<div class="ac-section-group -tools">

	<?php foreach ( $this->sections as $section ) : ?>
		<?= $section->render(); ?>
	<?php endforeach; ?>

</div>