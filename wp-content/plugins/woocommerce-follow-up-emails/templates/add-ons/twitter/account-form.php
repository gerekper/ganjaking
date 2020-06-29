<?php
$handle = get_user_meta( get_current_user_id(), 'twitter_handle', true );
?>
<fieldset>
	<p class="form-row form-row-first">
		<label for="twitter_handle"><?php esc_html_e( 'Twitter Handle', 'follow_up_emails' ); ?></label>
		@<input type="text" class="input-text twitter-handle" name="twitter_handle" id="twitter_handle" value="<?php echo esc_attr( $handle ); ?>" style="width: 80%;" />
	</p>
</fieldset>
