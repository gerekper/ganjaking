<form method="post" action="">
  <h3><?php _e('General Settings','userpro'); ?></h3>
  <table class="form-table">
  	<tr valign="top">
  		<th scope="row"><label for="enable_timeline"><?php _e('Enable Timeline','userpro'); ?></label></th>
  		<td>
  			<select name="enable_timeline" id="enable_timeline" class="chosen-select" style="width:300px">
  				<option value="1" <?php selected(1, userpro_timeline_get_option('enable_timeline')); ?>><?php _e('Yes','userpro'); ?></option>
  				<option value="0" <?php selected(0, userpro_timeline_get_option('enable_timeline')); ?>><?php _e('No','userpro'); ?></option>
  			</select>
  		</td>
  	</tr>
  </table>

  <p class="submit">
  	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-gmap'); ?>"  />
  	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-gmap'); ?>"  />
  </p>

</form>
