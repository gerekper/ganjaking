<form method="post" action="">

<p class="upadmin-highlight"><?php _e('Here you can set fields to be available for certain role(s) only. For example, If you wish to make a set of fields not visible to Subscriber role, or want to make other fields available only to a Customer role. This does the job for you.','userpro'); ?></p>

<h3><i class="userpro-icon-pencil"></i><?php _e('Setup Field / Role Relationships','userpro'); ?></h3>
<table class="form-table form-table-fieldroles">

	<?php
	$fields = get_option('userpro_fields');
	foreach($fields as $key => $field){
	?>
	<tr valign="top">
		<th scope="row"><label for=""><?php echo $field['label']; ?><span><?php echo $key; ?></span></label></th>
		<td>
			<?php
			if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
			$roles = $wp_roles->get_names();
			foreach($roles as $k=>$v) {
				if($k == 'administrator') continue;
			?>
			<label><input type='checkbox' value='<?php echo $k; ?>' name='<?php echo $key; ?>_roles[]' <?php if (userpro_get_option($key.'_roles') && in_array($k, userpro_get_option($key.'_roles')) ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php echo $v; ?></label>
			<?php } ?>
		</td>
	</tr>
	<?php } ?>
	
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Save Changes','userpro'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="up-admin-btn remove small" value="<?php _e('Reset Options','userpro'); ?>"  />
</p>

</form>