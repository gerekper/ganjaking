<span class="buttons">
	<?php if ( ! $this->is_license_defined ): ?>
		<button type="submit" class="button" name="action" value="<?= \ACP\Controller\License::DEACTIVATE_ACTION; ?>"><?php _e( 'Deactivate license', 'codepress-admin-columns' ); ?></button>
	<?php endif; ?>
	<button type="submit" class="button" name="action" value="<?= \ACP\Controller\License::UPDATE_ACTION; ?>"><?php _e( 'Check license', 'codepress-admin-columns' ); ?></button>
</span>