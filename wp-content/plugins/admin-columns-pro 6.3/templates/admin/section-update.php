<div class="acp-plugin-version-section" data-update-ready="<?= $this->plugin_update_ready ?>" data-update-basename="<?= $this->plugin_update_basename ?>" data-update-slug="<?= $this->plugin_update_slug ?>" data-update-nonce="<?= $this->plugin_update_nonce ?>">
	<div class="acp-plugin-version-section__current">
		<span class="acp-plugin-version-section__current__label"><?= $this->plugin_label; ?></span>
		<a href="<?= $this->changelog_link ?>" data-acp-comp="changelog" class="acp-plugin-version-badge <?= $this->available_version ? '-old' : ''; ?>">
			<?= $this->current_version; ?>
		</a>
	</div>
	<div class="acp-plugin-version-section__available" data-loading="">
		<?php if ( $this->available_version ) : ?>
			<span class="acp-plugin-version-section__available__label"><?= __( 'Available version', 'codepress-admin-columns' ) ?></span>
			<a href="<?= $this->changelog_link ?>" data-acp-comp="changelog" class="acp-plugin-version-badge -new">
				<?= $this->available_version ?>
			</a>
		<?php else: ?>
			<em><?= __( 'This plugin is up to date', 'codepress-admin-columns' ); ?></em>
		<?php endif; ?>
	</div>
</div>