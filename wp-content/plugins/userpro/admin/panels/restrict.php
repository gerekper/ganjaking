<form method="post" action="">

<h3><i class="userpro-icon-lock"></i><?php _e('Global Restrict/Lock Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="site_guest_lockout"><?php _e('Do you want to lock entire site for guests?','userpro'); ?></label></th>
		<td>
			<select name="site_guest_lockout" id="site_guest_lockout" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('site_guest_lockout')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('site_guest_lockout')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="site_guest_lockout_pageid"><?php _e('Page ID that guests will be redirected to (If you locked the entire site above)','userpro'); ?></label></th>
		<td>
			<input type="text" name="site_guest_lockout_pageid" id="site_guest_lockout_pageid" value="<?php echo userpro_get_option('site_guest_lockout_pageid'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('This is typically your custom login page. Guests will be automatically redirected to this page If you block entire site for guests.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="site_guest_lockout_pageids"><?php _e('Page IDs (seperated by comma) that guests can view If you block the entire site for guests','userpro'); ?></label></th>
		<td>
			<input type="text" name="site_guest_lockout_pageids" id="site_guest_lockout_pageids" value="<?php echo userpro_get_option('site_guest_lockout_pageids'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="homepage_guest_lockout"><?php _e('Redirect guests from homepage to custom URL','userpro'); ?></label></th>
		<td>
			<input type="text" name="homepage_guest_lockout" id="homepage_guest_lockout" value="<?php echo userpro_get_option('homepage_guest_lockout'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('This option allow you to lock the homepage completely for guests and auto-redirect them to any page you want.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="homepage_member_lockout"><?php _e('Redirect members from homepage to custom URL','userpro'); ?></label></th>
		<td>
			<input type="text" name="homepage_member_lockout" id="homepage_member_lockout" value="<?php echo userpro_get_option('homepage_member_lockout'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('This option allow you to lock the homepage completely for members and auto-redirect them to any page you want.','userpro'); ?></span>
		</td>
	</tr>
	
</table>

<h3><i class="userpro-icon-lock"></i><?php _e('Specific Restrict/Lock Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="restricted_page_verified"><?php _e('Make Restricted Content Available To','userpro'); ?></label></th>
		<td>
			<select name="restricted_page_verified" id="restricted_page_verified" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('restricted_page_verified')); ?>><?php _e('Only Verified Members','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('restricted_page_verified')); ?>><?php _e('All Registered Members','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="restrict_url"><?php _e('Redirect URL (If a whole page is locked/restricted)','userpro'); ?></label></th>
		<td>
			<input type="text" name="restrict_url" id="restrict_url" value="<?php echo userpro_get_option('restrict_url'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Please enter full web address to send users who access a restricted page. You can restrict any page/post by visiting it in post editor.','userpro'); ?></span>
		</td>
	</tr>
	
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Save Changes','userpro'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="up-admin-btn remove small" value="<?php _e('Reset Options','userpro'); ?>"  />
</p>

</form>