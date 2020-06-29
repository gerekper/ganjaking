<form action="admin-post.php" method="post" enctype="multipart/form-data">

	<?php wp_nonce_field( 'fue-update-settings-verify' ); ?>

	<?php do_action( 'fue_settings_integration' ); ?>

	<p class="submit">
		<input type="hidden" name="action" value="fue_followup_save_settings" />
		<input type="hidden" name="section" value="<?php echo esc_attr( $tab ); ?>" />
		<input type="submit" name="save" value="<?php esc_attr_e('Save Settings', 'follow_up_emails'); ?>" class="button-primary" />
	</p>

</form>
