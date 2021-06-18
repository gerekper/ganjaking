<?php global $userpro_badges; ?>

<p class="upadmin-highlight"><?php printf(__('If you want to add more badges, please put your badges as PNG in <code>%s</code>. To give a new badge, or assign a new achievement, click on a badge below to start.','userpro'), userpro_dg_url . 'badges/'); ?></p>

<form action="" method="post">

<h3><?php echo userpro_badges_admin_title(); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"></th>
		<td>
			<?php echo $userpro_badges->loop_badges(); ?>
		</td>
	</tr>

	<?php if (userpro_badges_admin_edit()){?>
	<input type="hidden" name="badge_url" id="badge_url" value="<?php echo userpro_badges_admin_edit_info('badge_url'); ?>" />
	<?php } else { ?>
	<input type="hidden" name="badge_url" id="badge_url" value="" />
	<?php } ?>
	
	<tr valign="top">
		<th scope="row"><label for="badge_file_local"><?php _e('Upload Badge','userpro'); ?></label></th>
		<td>
			<label for="upload_image">
    			<input id="upload_image" type="text" size="36" name="ad_image" value="http://" />
    			<input id="upload_image_button" class="button" type="button" value="Upload Image" />
</label>
			<span class="description"><?php _e('Enter a URL or upload an badge icon , Icon should be 16 X 16 png file.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="badge_title"><?php _e('Badge Title','userpro'); ?></label></th>
		<td>
			<input type="text" name="badge_title" id="badge_title" value="<?php if (userpro_badges_admin_edit()) echo userpro_badges_admin_edit_info('badge_title'); ?>" class="regular-text" />
			<span class="description"><?php _e('The title of badge will appear when user hovers over the badge e.g. Featured User, User of the Year, etc.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="badge_method"><?php _e('How users can get this badge?','userpro'); ?></label></th>
		<td>
			<select name="badge_method" id="badge_method" class="chosen-select" style="width:300px" data-placeholder="">
				<option value="manual_roles" <?php if (userpro_badges_admin_edit() && (isset($_GET['btype']) && $_GET['btype']!='roles')) echo 'disabled="disabled"'; ?>><?php _e('Give this badge to roles (manual)','userpro'); ?></option>
				<option value="auto_roles" <?php if (userpro_badges_admin_edit() && (isset($_GET['btype']) && $_GET['btype']!='auto_roles')) echo 'disabled="disabled"'; ?>><?php _e('Give this badge to roles (automatic)','userpro'); ?></option>
				<option value="manual" <?php if (userpro_badges_admin_edit()) echo 'disabled="disabled"'; ?>><?php _e('Give this badge to users (manual)','userpro'); ?></option>
				<option value="achievement" <?php if(isset($_GET['btype']) && ($_GET['btype']=='defaultbadge' || $_GET['btype']=='roles')) echo 'disabled="disabled"';?>><?php _e('Require achievement (automatic)','userpro'); ?></option>
				<option value="Defaultbadge" <?php if(isset($_GET['btype']) && $_GET['btype']!='defaultbadge') echo 'disabled="disabled"'?>><?php _e('Default Badge for All','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
</table>

<!-- Conditional Fields -->
<!--Added Yogesh to set Default badge -->
<table class="form-table" data-type="conditional" rel="Defaultbadge">
	<tr valign="top">
	<?php global $userpro;
	
	$result=get_option( 'userpro_defaultbadge' );
	?>
		<th scope="row"><label for="badge_to_users[]"><?php _e('Enable Default Badge','userpro'); ?></label></th>
		<td>
		<select name="defaultbadge" id="defaultbadge" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, $result['defaultbadge']); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, $result['defaultbadge']); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
</table>

<table class="form-table" data-type="conditional" rel="manual_roles">
	<tr valign="top">
		<th scope="row"><label for="badge_to_roles[]"><?php _e('Choose which users should receive this badge','userpro'); ?></label></th>
		<td>
			<select name="badge_to_roles[]" id="badge_to_roles[]" multiple="multiple" class="chosen-select" style="width:300px" data-placeholder="<?php _e('Choose...','userpro'); ?>">
				<?php 
				global $wp_roles;
				$userpro_badges = get_option('_userpro_badges');
				if(isset($_GET['bid']) && (isset($_GET['btype']) && $_GET['btype']=='roles')){
					$badge_roles = $userpro_badges['roles'][$_GET['bid']]['badge_to_role'];
				}
				if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
					$roles = $wp_roles->get_names();
					foreach($roles as $k=>$v) {
				?>
				<option value="<?php echo $k; ?>" <?php if(isset($badge_roles) && in_array($k,$badge_roles)) echo 'selected="selected"'?>><?php echo $v; ?></option>
				<?php } ?>
			</select>
			<span class="description"><?php _e('You can assign this badge to the users belonging to specified role you want by choosing them here.','userpro'); ?></span>
		</td>
	</tr>
</table>
<table class="form-table" data-type="conditional" rel="auto_roles">
	<tr valign="top">
		<th scope="row"><label for="auto_badge_to_roles[]"><?php _e('Choose which users should receive this badge','userpro'); ?></label></th>
		<td>
			<select name="auto_badge_to_roles[]" id="auto_badge_to_roles[]" multiple="multiple" class="chosen-select" style="width:300px" data-placeholder="<?php _e('Choose...','userpro'); ?>">
				<?php 
				global $wp_roles;
				$userpro_badges = get_option('_userpro_badges_auto');
				if(isset($_GET['bid']) && (isset($_GET['btype']) && $_GET['btype']=='auto_roles')){
					
					$badge_roles = $userpro_badges['auto_roles'][$_GET['bid']]['auto_badge_to_role'];
					
				}
				if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
					$roles = $wp_roles->get_names();
					foreach($roles as $k=>$v) {
				?>
				<option value="<?php echo $k; ?>" <?php if(isset($badge_roles) && in_array($k,$badge_roles)) echo 'selected="selected"'?>><?php echo $v; ?></option>
				<?php } ?>
			</select>
			<span class="description"><?php _e('You can assign this badge to the users belonging to specified role you want by choosing them here.','userpro'); ?></span>
		</td>
	</tr>
</table>


<table class="form-table" data-type="conditional" rel="manual">
	<tr valign="top">
		<th scope="row"><label for="badge_to_users[]"><?php _e('Choose which users receive this badge','userpro'); ?></label></th>
		<td>
			<select name="badge_to_users[]" id="badge_to_users[]" multiple="multiple" class="chosen-select" style="width:300px" data-placeholder="<?php _e('Choose...','userpro'); ?>">
				<?php
				$users=userpro_badges_admin_users();
				foreach($users as $user) {
				?>
				<option value="<?php echo $user->ID; ?>"><?php echo userpro_profile_data('display_name', $user->ID); if ($user->user_email) echo ' ('. $user->user_email . ')'; ?></option>
				<?php } ?>
			</select>
			<span class="description"><?php _e('You can assign this badge to specific to the users you want by choosing them here.','userpro'); ?></span>
		</td>
	</tr>
</table>



<table class="form-table" data-type="conditional" rel="achievement">
	<tr valign="top">
		<th scope="row"><label><?php _e('Setup Achievement','userpro'); ?></label></th>
		<td>
			<label for="badge_achieved_num"><?php _e('User has completed','userpro'); ?></label>
			<input type="text" name="badge_achieved_num" id="badge_achieved_num" value="<?php if (userpro_badges_admin_edit()) echo $_GET['bid']; ?>" class="badge_achieved_num" />
			<select name="badge_achieved_type" id="badge_achieved_type" class="chosen-select" style="width:300px" data-placeholder="">
				<option value="any" <?php if ( userpro_badges_admin_edit() ) selected('any', $_GET['btype']); ?> ><?php _e('Posts (Any post type)','userpro'); ?></option>
				<option value="comments" <?php if ( userpro_badges_admin_edit() ) selected('comments', $_GET['btype']); ?> ><?php _e('Comments','userpro'); ?></option>
				<?php echo userpro_badges_admin_post_types(); ?>
				<option value="days" <?php if ( userpro_badges_admin_edit() ) selected('days', $_GET['btype']); ?> ><?php _e('Days (since registration)','userpro'); ?></option>
			</select>
		</td>
	</tr>
</table>

<p class="submit">
	<input type="submit" name="insert-badge" id="insert-badge" class="button button-primary" value="<?php _e('Submit','userpro'); ?>"  />
</p>

</form>
