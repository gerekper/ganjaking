<form method="post" action="">

<h3><?php _e('Display Settings','userpro-userwall'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="media_per_page"><?php _e('Title','userpro-userwall'); ?></label></th>
		<td>
			<input type="text" name="title" id="title" value="<?php echo userpro_userwall_get_option( 'title' ); ?>" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="personalwall_title"><?php _e('Personal Wall Title','userpro-userwall'); ?></label></th>
		<td>
			<input type="text" name="personalwall_title" id="personalwall_title" value="<?php echo userpro_userwall_get_option( 'personalwall_title' ); ?>" class="regular-text" />
		</td>
	</tr>
<tr valign="top">
		<th scope="row"><label for="totalpost"><?php _e('Post Per Page','userpro-userwall'); ?></label></th>
		<td>
			<input type="text" name="totalpost" id="totalpost" value="<?php echo userpro_userwall_get_option( 'totalpost' ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="postcontent_color"><?php _e('Post content color','userpro-userwall'); ?></label></th>
		<td>
			<input type="color" name="postcontent_color" id="totalpost" value="<?php echo userpro_userwall_get_option('postcontent_color'); ?>" class="regular-text" />
		</td>
	</tr>


<tr valign="top">
		<th scope="row"><label for="userpro-userwall_roles_can_poston_wall"><?php _e('Allow Roles From Post on wall','userpro-userwall'); ?></label></th>
		<td>

				<input type="text" name="userpro-userwall_roles_can_poston_wall" id="userpro-userwall_roles_can_poston_wall" value="<?php echo userpro_userwall_get_option('userpro-userwall_roles_can_poston_wall'); ?>" class="regular-text" />

			<span class="description"><?php _e('The users belonging to above roles will be able to Post on the social wall. By default all users will able to post on the wall.','userpro-userwall'); ?></span>
		</td>
	</tr>
<tr valign="top">
		<th scope="row"><label for="For All User"><?php _e('Display Social Share button ','userpro-userwall'); ?></label></th>
		<td>
			<select name="display_socialbutton" id="display_socialbutton" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_userwall_get_option('display_socialbutton')); ?>><?php _e('Yes','userpro-userwall'); ?></option>
				<option value="0" <?php selected(0, userpro_userwall_get_option('display_socialbutton')); ?>><?php _e('No','userpro-userwall'); ?></option>
			</select>
		</td>
	</tr>


<tr valign="top">

		<th scope="row"><label for="For All User"><?php _e('Display only followers post','userpro-userwall'); ?></label></th>
		<td>
			<select name="followerspost" id="followerspost" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_userwall_get_option('followerspost')); ?>><?php _e('Yes','userpro-userwall'); ?></option>
				<option value="0" <?php selected(0, userpro_userwall_get_option('followerspost')); ?>><?php _e('No','userpro-userwall'); ?></option>
			</select>
        <span class="description"><?php _e('If this option is set to yes then only followers post will be displayed on the wall','userpro-userwall'); ?></span>
		</td>
	</tr>
<tr valign="top">

		<th scope="row"><label for="For All User"><?php _e('Display Wall To Visitors','userpro-userwall'); ?></label></th>
		<td>
			<select name="nonloginusers" id="nonloginusers" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_userwall_get_option('nonloginusers')); ?>><?php _e('Yes','userpro-userwall'); ?></option>
				<option value="0" <?php selected(0, userpro_userwall_get_option('nonloginusers')); ?>><?php _e('No','userpro-userwall'); ?></option>
			</select>
        <span class="description"><?php _e('If this option is set to yes then non logged in users will be able to view the wall','userpro-userwall'); ?></span>
		</td>
	</tr>

<tr valign="top">

		<th scope="row"><label for="For All User"><?php _e('Enable comment notification','userpro-userwall'); ?></label></th>
		<td>
			<select name="sw_comment_notification" id="sw_comment_notification" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_userwall_get_option('sw_comment_notification')); ?>><?php _e('Yes','userpro-userwall'); ?></option>
				<option value="0" <?php selected(0, userpro_userwall_get_option('sw_comment_notification')); ?>><?php _e('No','userpro-userwall'); ?></option>
			</select>
        <span class="description"><?php _e('If this option is set to yes then user gets notification for new comment on their post','userpro-userwall'); ?></span>
		</td>
	</tr>
<tr valign="top">
		<th scope="row"><label for="limit_number_of_commemt"><?php _e('Limit Number of comment','userpro-userwall'); ?></label></th>
		<td>

				<input type="text" name="limit_number_of_comment" id="limit_number_of_comment" value="<?php echo userpro_userwall_get_option('limit_number_of_comment'); ?>" class="regular-text" />


		</td>
	</tr>
<tr valign="top">
		<th scope="row"><label for="limit_number_of_post"><?php _e('Limit Number of Post','userpro-userwall'); ?></label></th>
		<td>

				<input type="text" name="limit_number_of_post" id="limit_number_of_post" value="<?php echo userpro_userwall_get_option('limit_number_of_post'); ?>" class="regular-text" />


		</td>
	</tr>

<tr valign="top">

		<th scope="row"><label for="For All User"><?php _e('Display Personal Wall on Profile','userpro-userwall'); ?></label></th>
		<td>
			<select name="enablepersonalwall" id="enablepersonalwall" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_userwall_get_option('enablepersonalwall')); ?>><?php _e('Yes','userpro-userwall'); ?></option>
				<option value="0" <?php selected(0, userpro_userwall_get_option('enablepersonalwall')); ?>><?php _e('No','userpro-userwall'); ?></option>
			</select>
        <span class="description"><?php _e('If this option is set to yes then personal wall will show up on user profile','userpro-userwall'); ?></span>
		</td>
	</tr>

	<tr valign="top">
			<th scope="row"><label for="For All User"><?php _e('Allow users to upload media','userpro-userwall'); ?></label></th>
			<td>
				<select name="allow_mediabutton" id="allow_mediabutton" class="chosen-select" style="width:300px">
					<option value="1" <?php selected(1, userpro_userwall_get_option('allow_mediabutton')); ?>><?php _e('Yes','userpro-userwall'); ?></option>
					<option value="0" <?php selected(0, userpro_userwall_get_option('allow_mediabutton')); ?>><?php _e('No','userpro-userwall'); ?></option>
				</select>
			</td>
		</tr>

</table>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-userwall'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-userwall'); ?>"  />
</p>

</form>
