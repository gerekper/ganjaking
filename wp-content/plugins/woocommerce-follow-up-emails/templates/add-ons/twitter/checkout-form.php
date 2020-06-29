<?php
$handle = '';
$user   = wp_get_current_user();

if ( $user->ID ) {
	$handle = get_user_meta( $user->ID, 'twitter_handle', true );
}
?>
<p class="form-row form-row-wide">
	<label class="" for="twitter_handle"><?php esc_html_e('Twitter Handle', 'follow_up_emails'); ?></label>
	@<input type="text" value="<?php echo esc_attr( $handle ); ?>" placeholder="username" id="twitter_handle" name="twitter_handle" class="input-text" style="width:90%">
</p>
