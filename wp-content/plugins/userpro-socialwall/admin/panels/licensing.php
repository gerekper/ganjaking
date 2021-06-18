<form method="post" action="">



<h3><?php _e('Purchase code','userpro'); ?></h3>
<table class="form-table">
<tr valign="top">
		<th scope="row"><label for="userpro_userwall_envato_code"><?php _e('Envato Purchase code','userpro-userwall'); ?></label></th>
		<td>
			<input type="text" style="width:300px !important;" name="userpro_userwall_envato_code" id="userpro_userwall_envato_code" value="<?php echo (userpro_userwall_get_option('userpro_userwall_envato_code')) ? userpro_userwall_get_option('userpro_userwall_envato_code') : ''; ?>" class="regular-text" />
			<span class="description"><?php _e('Enter your envato purchase code.','userpro-userwall'); ?></span>
		</td>
</tr>
</table>
<p class="submit">
	<input type="submit" name="socialwall-verify-license" id="verify-license" class="button button-primary" value="<?php _e('Save Changes','userpro'); ?>"  />
</p>

</form>
