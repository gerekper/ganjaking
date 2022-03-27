<form id="licence_activation" action="" method="post">

	<?= $this->nonce_field ?>

	<ul class="license-info-list">
		<?php if ( $this->updates_disabled && $this->has_activation ): ?>
			<li><span class="red dashicons dashicons-no-alt"></span> <?php _e( 'Automatic updates are disabled.', 'codepress-admin-columns' ); ?></li>
		<?php endif; ?>
		<?php if ( $this->updates_enabled ): ?>
			<li><span class="green dashicons dashicons-yes"></span> <?php _e( 'Automatic updates are enabled.', 'codepress-admin-columns' ); ?></li>
		<?php endif; ?>
		<?php if ( $this->is_expired && $this->expiry_date ): ?>
			<li><span class="red dashicons dashicons-no-alt"></span> <?php printf( __( 'License has expired on %s', 'codepress-admin-columns' ), sprintf( '<strong>%s</strong>', $this->expiry_date ) ); ?></li>
		<?php endif; ?>
		<?php if ( $this->is_cancelled ): ?>
			<li><span class="red dashicons dashicons-no-alt"></span> <?php _e( 'Your subscription is cancelled.', 'codepress-admin-columns' ); ?></li>
		<?php endif; ?>
		<?php if ( $this->is_active && $this->expiry_date ): ?>
			<li><span class="green dashicons dashicons-yes"></span> <?php printf( __( 'License is valid until %s', 'codepress-admin-columns' ), sprintf( '<strong>%s</strong>', $this->expiry_date ) ); ?></li>
		<?php endif; ?>
	</ul>
	<?php if ( $this->has_activation ): ?>
		<span class="buttons">
			<button type="submit" class="button" name="action" value="acp-license-deactivate"><?php _e( 'Deactivate', 'codepress-admin-columns' ); ?></button>
			<button type="submit" class="button" name="action" value="acp-license-update"><?= _x( 'Refresh', 'Refresh license', 'codepress-admin-columns' ); ?></button>
		</span>
	<?php else : ?>
		<input type="<?= $this->is_license_defined ? 'password' : 'text'; ?>" value="<?= $this->license_key; ?>" name="license" size="40" placeholder="<?php echo esc_attr( __( 'Enter your license key', 'codepress-admin-columns' ) ); ?>">
		<button type="submit" class="button" name="action" value="acp-license-activate"><?php _e( 'Activate', 'codepress-admin-columns' ); ?></button>
		<p>
			<?php if ( $this->has_usage_permission ): ?>
				<?php _e( 'Enter your license key to receive automatic updates.', 'codepress-admin-columns' ); ?>
			<?php endif; ?>
			<?php if ( ! $this->license_key ) : ?>
				<?php printf( __( 'You can find your license key in the welcome email or in your %s.', 'codepress-admin-columns' ), sprintf( '<a href="%s" target="_blank">%s</a>', $this->my_account_link, __( 'account page', 'codepress-admin-columns' ) ) ); ?>
			<?php endif; ?>
		</p>
	<?php endif; ?>
</form>