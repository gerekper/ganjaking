<form method="post" action="">

<h3><?php _e('Activate UserPro Messaging','userpro-msg'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="userpro_msg_envato_code"><?php _e('Enter your Item Purchase Code','userpro-msg'); ?></label></th>
		<td>
			<input type="text" name="userpro_msg_envato_code" id="userpro_msg_envato_code" value="<?php echo userpro_msg_get_option( 'userpro_msg_envato_code' ); ?>" class="regular-text" />
			<span class="description"><?php _e('Enter Envato Purchase Code to enable automatic updates.','userpro-msg'); ?></span>
		</td>
	</tr>
</table>

<p class="submit">
   <input type="submit" name="up_msg_license_verify" id="up_msg_license_verify" class="button button-primary" value="<?php _e('Save Changes','userpro-msg'); ?>"/>
</p>

</form>
