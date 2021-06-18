<form method="post" action="">
<h3><?php _e('Envato Purchase Code','userpro-tags'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="userpro_tags_envato_code"><?php _e('Envato Purchase Code','userpro-tags'); ?></label></th>
		<td>
			<input type="text" name="userpro_tags_envato_code" id="userpro_tags_envato_code" value="<?php echo userpro_tags_get_option('userpro_tags_envato_code' ); ?>" class="regular-text" />
		</td>
	</tr>
</table>
<h3><?php _e('Settings','userpro-tags'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="limit_tags"><?php _e('Limit Number of Tags','userpro-tags'); ?></label></th>
		<td>
			<input type="text" name="limit_tags" id="limit_tags" value="<?php echo userpro_tags_get_option('limit_tags' ); ?>" class="regular-text" />
		</td>
	</tr>
</table>
<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-tags'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-tags'); ?>"  />
</p>

</form>