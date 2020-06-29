<?php

use ACP\Migrate\Import;

?>

<div class="ac-section -import">
	<div class="ac-section__header">
		<h2 class="ac-section__header__title"><?php _e( 'Import', 'codepress-admin-columns' ); ?></h2>
	</div>
	<div class="ac-section__body">
		<p>
			<?= esc_html( __( 'Select the Admin Columns JSON file you would like to import. When you click the import button below, Admin Columns will import the column settings.', 'codepress-admin-columns' ) ); ?>
		</p>
		<form method="post" action="" enctype="multipart/form-data" class="ac-import">
			<?php wp_nonce_field( 'file-import', '_ac_nonce', false ); ?>
			<?php wp_nonce_field( Import\Request::ACTION, Import\Request::NONCE_NAME ); ?>
			<input type="hidden" name="action" value="<?= Import\Request::ACTION ?>">
			<div class="ac-import__field">
				<label for=""><?php _e( 'Select File', 'codepress-admin-columns' ); ?></label>
				<input type="file" size="25" name="import" id="upload" accept=".json">
			</div>

			<input type="submit" value="<?php _e( 'Import File', 'codepress-admin-columns' ); ?>" class="button button-primary" name="file-submit">
		</form>
	</div>
</div>