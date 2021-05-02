<?php use ACP\Controller\License; ?>
<span class="buttons">
<?php if ( ! $this->is_license_defined ): ?>
	<button type="submit" class="button" name="action" value="<?= License::DEACTIVATE_ACTION; ?>"><?php _e( 'Deactivate license', 'codepress-admin-columns' ); ?></button>
<?php endif; ?>
	<button type="submit" class="button" name="action" value="<?= License::UPDATE_ACTION; ?>"><?php _e( 'Check license', 'codepress-admin-columns' ); ?></button>
</span>