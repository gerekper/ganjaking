<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<script type="text/html" id="tmpl-quick-import">
	<form method="post" enctype="multipart/form-data" id="tmpl-quick-import-form" class="ls-hidden">
		<?php wp_nonce_field('import-sliders'); ?>
		<input type="hidden" name="ls-import" value="1">
		<input type="file" name="import_file">
	</form>
</script>