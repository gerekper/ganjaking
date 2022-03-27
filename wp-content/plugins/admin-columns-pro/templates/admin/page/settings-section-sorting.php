<form action="" method="post">
	<?php wp_nonce_field( 'reset-sorting-preference', '_acnonce' ); ?>
	<input type="submit" name="acp-reset-sorting" class="button" value="<?php _e( 'Reset', 'codepress-admin-columns' ); ?>">
</form>