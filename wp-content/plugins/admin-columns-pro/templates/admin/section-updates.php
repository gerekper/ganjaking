<section class="ac-settings-box -updates">
	<h2 class="ac-lined-header"><?= $this->title; ?></h2>

	<?= $this->content; ?>

	<div class="buttons">

		<?php if ( $this->button_update_now ) : ?>
			<button class="button" data-acp-update><?= __( 'Update Now', 'codepress-admin-columns' ) ?></button>
		<?php endif; ?>

		<?php if ( $this->button_update_now_disabled ) : ?>
			<button class="button" disabled><?= __( 'Update Now', 'codepress-admin-columns' ) ?></button>
		<?php endif; ?>

		<?php if ( $this->button_check_for_updates ) : ?>
			<form method="post">
				<?= wp_nonce_field( 'acp-force-plugin-update', '_acnonce' ); ?>
				<input type="hidden" name="action" value="acp-force-plugin-updates">
				<button class="button"><?= __( 'Check for Updates', 'codepress-admin-columns' ) ?></button>
			</form>
		<?php endif; ?>

	</div>

</section>