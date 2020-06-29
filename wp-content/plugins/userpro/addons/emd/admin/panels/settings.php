<form method="post" action="">

<p class="upadmin-highlight"><?php _e('All settings can be overriden using shortcode options.','userpro'); ?></p>

<h3><?php _e('Global Layout Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="emd_per_page"><?php _e('Number of results per page','userpro'); ?></label></th>
		<td>
			<input type="text" name="emd_per_page" id="emd_per_page" value="<?php echo userpro_ed_get_option('emd_per_page'); ?>" class="regular-text" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="emd_col_width"><?php _e('Result Column Width','userpro'); ?></label></th>
		<td>
			<input type="text" name="emd_col_width" id="emd_col_width" value="<?php echo userpro_ed_get_option('emd_col_width'); ?>" class="regular-text" />
			<span class="description"><?php _e('A percentage (%) or pixel value. This decides how many columns the plugin will attempt to display relative to 100% width. Example: 30% means there will be max of 3 users per column.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="emd_col_margin"><?php _e('Result Column Margin','userpro'); ?></label></th>
		<td>
			<input type="text" name="emd_col_margin" id="emd_col_margin" value="<?php echo userpro_ed_get_option('emd_col_margin'); ?>" class="regular-text" />
			<span class="description"><?php _e('Left margin applied to the result column, which leaves a gap so that whole width is close to 100% including the column widths.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="emd_layout"><?php _e('Results Layout','userpro'); ?></label></th>
		<td>
			<select name="emd_layout" id="emd_layout" class="chosen-select" style="width:300px">
				<option value="masonry" <?php selected('masonry', userpro_ed_get_option('emd_layout')); ?>><?php _e('Masonry','userpro'); ?></option>
				<option value="fitColumns" <?php selected('fitColumns', userpro_ed_get_option('emd_layout')); ?>><?php _e('Grid / Fit Columns','userpro'); ?></option>
			</select>
		</td>
	</tr>

</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro'); ?>"  />
</p>

</form>