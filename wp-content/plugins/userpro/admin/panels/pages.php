<form method="post" action="">

<p class="upadmin-highlight"><?php printf(__('You must update your permalinks or Save Changes on your permalinks setup for the nice URLs to work fine.','userpro'), admin_url().'options-permalink.php'); ?></p>

<h3><i class="userpro-icon-edit-sign"></i><?php _e('Manage Page Slugs','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="slug"><?php _e('"Profile" Slug','userpro'); ?></label></th>
		<td><input type="text" name="slug" id="slug" class="regular-text" value="<?php echo userpro_get_option('slug'); ?>" /></td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="slug_register"><?php _e('"Register" Slug','userpro'); ?></label></th>
		<td><input type="text" name="slug_register" id="slug_register" class="regular-text" value="<?php echo userpro_get_option('slug_register'); ?>" /></td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="slug_edit"><?php _e('"Edit Profile" Slug','userpro'); ?></label></th>
		<td><input type="text" name="slug_edit" id="slug_edit" class="regular-text" value="<?php echo userpro_get_option('slug_edit'); ?>" /></td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="slug_login"><?php _e('"Login" Slug','userpro'); ?></label></th>
		<td><input type="text" name="slug_login" id="slug_login" class="regular-text" value="<?php echo userpro_get_option('slug_login'); ?>" /></td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="slug_directory"><?php _e('"Member Directory" Slug','userpro'); ?></label></th>
		<td><input type="text" name="slug_directory" id="slug_directory" class="regular-text" value="<?php echo userpro_get_option('slug_directory'); ?>" /></td>
	</tr>
    <tr valign="top">
		<th scope="row"><label for="slug_connections"><?php _e('"Connections" Slug','userpro'); ?></label></th>
		<td><input type="text" name="slug_connections" id="slug_connections" class="regular-text" value="<?php echo userpro_get_option('slug_connections'); ?>" /></td>
	</tr>
    <tr valign="top">
		<th scope="row"><label for="slug_followers"><?php _e('"Followers" Slug','userpro'); ?></label></th>
		<td><input type="text" name="slug_followers" id="slug_followers" class="regular-text" value="<?php echo userpro_get_option('slug_followers'); ?>" /></td>
	</tr>

    <tr valign="top">
        <th scope="row"><label for="slug_following"><?php _e('"Following" Slug','userpro'); ?></label></th>
        <td><input type="text" name="slug_following" id="slug_following" class="regular-text" value="<?php echo userpro_get_option('slug_following'); ?>" /></td>
    </tr>
	
	<tr valign="top">
		<th scope="row"><label for="slug_logout"><?php _e('"Logout" Slug','userpro'); ?></label></th>
		<td><input type="text" name="slug_logout" id="slug_logout" class="regular-text" value="<?php echo userpro_get_option('slug_logout'); ?>" /></td>
	</tr>
	
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Save Changes','userpro'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="up-admin-btn remove small" value="<?php _e('Reset Options','userpro'); ?>"  />
</p>

<h3><i class="userpro-icon-sitemap"></i><?php _e('Check / Rebuild Profile Pages','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label><?php _e('Profile Page','userpro'); ?></label></th>
		<td>
			<?php if (userpro_admin_page_exists('view') ) {
				echo userpro_admin_link('view');
				echo '<a href="'.userpro_admin_link('view').'" class="button upadmin-inline">'.__('View Page','userpro').'</a>';
			} else {
				echo userpro_admin_broken_page();
			}
			?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label><?php _e('Registration Page','userpro'); ?></label></th>
		<td>
			<?php if (userpro_admin_page_exists('register') ) {
				echo userpro_admin_link('register');
				echo '<a href="'.userpro_admin_link('register').'" class="button upadmin-inline">'.__('View Page','userpro').'</a>';
			} else {
				echo userpro_admin_broken_page();
			}
			?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label><?php _e('Edit Profile Page','userpro'); ?></label></th>
		<td>
			<?php if (userpro_admin_page_exists('edit') ) {
				echo userpro_admin_link('edit');
				echo '<a href="'.userpro_admin_link('edit').'" class="button upadmin-inline">'.__('View Page','userpro').'</a>';
			} else {
				echo userpro_admin_broken_page();
			}
			?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label><?php _e('Login Page','userpro'); ?></label></th>
		<td>
			<?php if (userpro_admin_page_exists('login') ) {
				echo userpro_admin_link('login');
				echo '<a href="'.userpro_admin_link('login').'" class="button upadmin-inline">'.__('View Page','userpro').'</a>';
			} else {
				echo userpro_admin_broken_page();
			}
			?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label><?php _e('Member Directory Page','userpro'); ?></label></th>
		<td>
			<?php if (userpro_admin_page_exists('directory_page') ) {
				echo userpro_admin_link('directory_page');
				echo '<a href="'.userpro_admin_link('directory_page').'" class="button upadmin-inline">'.__('View Page','userpro').'</a>';
			} else {
				echo userpro_admin_broken_page();
			}
			?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label><?php _e('Logout Page','userpro'); ?></label></th>
		<td>
			<?php if (userpro_admin_page_exists('logout_page') ) {
				echo userpro_admin_link('logout_page');
				echo '<a href="'.userpro_admin_link('logout_page').'" class="button upadmin-inline">'.__('View Page','userpro').'</a>';
			} else {
				echo userpro_admin_broken_page();
			}
			?>
		</td>
	</tr>

</table>

<p class="submit submit-static">
	<input type="submit" name="rebuild-pages" id="rebuild-pages" class="up-admin-btn approve small" value="<?php _e('Rebuild UserPro Pages','userpro'); ?>"  />
</p>

</form>