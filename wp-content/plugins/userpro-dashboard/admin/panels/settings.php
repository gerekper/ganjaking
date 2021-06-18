<?php
	if( !isset( $updb_default_options ) ){
		$updb_default_options = new UPDBDefaultOptions();
	}
?>
<form method="post" action="">
	<h3><?php _e('General Settings','userpro-dashboard'); ?></h3>
	<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="userpro_db_enable"><?php _e('Enable Dashboard view','userpro-dashboard'); ?></label></th>
		<td>
			<select name="userpro_db_enable" id="userpro_db_enable" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, $updb_default_options->updb_get_option('userpro_db_enable')); ?>><?php _e('Yes','userpro-dashboard'); ?></option>
				<option value="0" <?php selected(0, $updb_default_options->updb_get_option('userpro_db_enable')); ?>><?php _e('No','userpro-dashboard'); ?></option>
			</select>
			<span class="description"><?php _e('If enabled, dashboard for every user will be shown'); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="userpro_db_custom_layout"><?php _e('Enable custom layout for all users','userpro-dashboard'); ?></label></th>
		<td>
			<select name="userpro_db_custom_layout" id="userpro_db_custom_layout" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, $updb_default_options->updb_get_option('userpro_db_custom_layout')); ?>><?php _e('Yes','userpro-dashboard'); ?></option>
				<option value="0" <?php selected(0, $updb_default_options->updb_get_option('userpro_db_custom_layout')); ?>><?php _e('No','userpro-dashboard'); ?></option>
			</select>
			<span class="description"><?php _e('If enabled, dashboard view for every user will be of same type'); ?></span>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row"><label for="userpro_db_post_enable"><?php _e("Display user's posts in dashboard",'userpro-dashboard'); ?></label></th>
		<td>
			<select name="userpro_db_post_enable" id="userpro_db_enable" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, $updb_default_options->updb_get_option('userpro_db_post_enable')); ?>><?php _e('Yes','userpro-dashboard'); ?></option>
				<option value="0" <?php selected(0, $updb_default_options->updb_get_option('userpro_db_post_enable')); ?>><?php _e('No','userpro-dashboard'); ?></option>
			</select>
			<span class="description"><?php _e('show/hide "My Posts" tab on user dashboard'); ?></span>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row"><label for="userpro_db_post_count"><?php _e('Number of posts to be displayed per page in "My Posts" tab','userpro-dashboard'); ?></label></th>
		<td>
                    <input type="text" name="userpro_db_post_count" id="userpro_db_post_count" value="<?php echo $updb_default_options->updb_get_option('userpro_db_post_count'); ?>" class="regular-text" />
                    <span class="description"><?php _e('Enter the number of posts per page to show posts on My Posts tab (Recommended - 10 posts per page)'); ?></span>
		</td>
	</tr>
	</table>
	<h3><?php _e('Widget Settings','userpro-dashboard'); ?></h3>
	<table class="form-table">
		<tr valign="top">
		<th scope="row"><label for="number_of_column"><?php _e('Number of columns to show','userpro-dashboard'); ?></label></th>
		<td>
			<input type="number" min="1" max="3" name="number_of_column" id="number_of_column" value="<?php echo $updb_default_options->updb_get_option('number_of_column'); ?>" class="regular-text" />
			<span class="description"><?php _e('Maximum 3 columns are allowed'); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="show_profile_customizer"><?php _e('Enable profile customizer','userpro-dashboard'); ?></label></th>
		<td>
			<select name="show_profile_customizer" id="show_profile_customizer" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, $updb_default_options->updb_get_option('show_profile_customizer')); ?>><?php _e('Yes','userpro-dashboard'); ?></option>
				<option value="0" <?php selected(0, $updb_default_options->updb_get_option('show_profile_customizer')); ?>><?php _e('No','userpro-dashboard'); ?></option>
			</select>
		</td>
	</tr>
	</table>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-dashboard'); ?>"  />
		<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-dashboard'); ?>"  />
	</p>
</form>
