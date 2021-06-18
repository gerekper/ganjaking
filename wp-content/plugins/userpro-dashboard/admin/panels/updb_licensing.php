<?php
	if( !isset( $updb_default_options ) ){
		$updb_default_options = new UPDBDefaultOptions();
	}
?>
<form method="post" action="">

<h3><?php _e('Activate UserPro Dashboard','userpro-dashboard'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="userpro_dashboard_code"><?php _e('Enter your Item Purchase Code','userpro-dashboard'); ?></label></th>
		<td>
			<input type="text" name="userpro_dashboard_code" id="userpro_dashboard_code" value="<?php echo $updb_default_options->updb_get_option('userpro_dashboard_code'); ?>" class="regular-text" />
		</td>
	</tr>
</table>

<p class="submit">
   <input type="submit" name="updb_verify" id="updb_verify" class="button button-primary" value="<?php _e('Save Changes','userpro-dashboard'); ?>"/>
</p>

</form>
