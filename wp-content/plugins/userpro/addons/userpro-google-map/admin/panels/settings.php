<form method="post" action="">

<h3><?php _e('General Settings','userpro-gmap'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="enable_gmap"><?php _e('Enable Google Map','userpro-gmap'); ?></label></th>
		<td>
			<select name="enable_gmap" id="enable_gmap" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_gmap_get_option('enable_gmap')); ?>><?php _e('Yes','userpro-gmap'); ?></option>
				<option value="0" <?php selected(0, userpro_gmap_get_option('enable_gmap')); ?>><?php _e('No','userpro-gmap'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
			<th scope="row"><label for="userpro_gmap_key"><?php _e('Google Map API Key','userpro-gmap'); ?></label></th>
			<td>
				<input type="text" style="width:300px !important;" name="userpro_gmap_key" id="userpro_gmap_key" value="<?php echo (userpro_gmap_get_option('userpro_gmap_key')) ? userpro_gmap_get_option('userpro_gmap_key') : ''; ?>" class="regular-text" />
				<span class="up-description"><?php _e('Enter the Google map API key value. Generate your <a target="_blank" href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend,places_backend&reusekey=true">API Key</a>','userpro-gmap'); ?></span>
			</td>
	</tr>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-gmap'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-gmap'); ?>"  />
</p>

</form>
