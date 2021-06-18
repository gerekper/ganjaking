<form method="post" action="">

<p class="upadmin-highlight"><?php printf(__('You must update your permalinks or Save Changes on your permalinks setup for the nice URLs to work fine.','userpro'), admin_url().'options-permalink.php'); ?></p>

<p class="upadmin-highlight"><?php _e('For a better multi-language experience, UserPro allow you to edit the slugs of the official registration/profile pages here. You must rebuild pages If you changed your slugs for the new slugs to take effect.','userpro'); ?></p>

<h3><?php _e('Manage Page Slugs','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="slug_following"><?php _e('"Following" Slug','userpro'); ?></label></th>
		<td><input type="text" name="slug_following" id="slug_following" class="regular-text" value="<?php echo userpro_sc_get_option('slug_following'); ?>" /></td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="slug_followers"><?php _e('"Followers" Slug','userpro'); ?></label></th>
		<td><input type="text" name="slug_followers" id="slug_followers" class="regular-text" value="<?php echo userpro_sc_get_option('slug_followers'); ?>" /></td>
	</tr>
	
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro'); ?>"  />
</p>

<p class="upadmin-highlight"><?php _e('By default, the extension creates frontend pages automatically. However if for some reason, these pages were removed or deleted you can rebuild them here.','userpro'); ?></p>

<h3><?php _e('Check / Rebuild Extension Pages','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label><?php _e('Following Page','userpro'); ?></label></th>
		<td>
			<?php if (userpro_sc_admin_page_exists('following') ) {
				echo userpro_sc_admin_link('following');
				echo '<a href="'.userpro_sc_admin_link('following').'" class="button upadmin-inline">'.__('View Page','userpro').'</a>';
			} else {
				echo userpro_admin_broken_page();
			}
			?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label><?php _e('Followers Page','userpro'); ?></label></th>
		<td>
			<?php if (userpro_sc_admin_page_exists('followers') ) {
				echo userpro_sc_admin_link('followers');
				echo '<a href="'.userpro_sc_admin_link('followers').'" class="button upadmin-inline">'.__('View Page','userpro').'</a>';
			} else {
				echo userpro_admin_broken_page();
			}
			?>
		</td>
	</tr>
	
</table>

<p class="submit">
	<input type="submit" name="rebuild-pages" id="rebuild-pages" class="button button-primary" value="<?php _e('Rebuild Pages','userpro'); ?>"  />
</p>

</form>