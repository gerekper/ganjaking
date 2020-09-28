<form id="userpro-settings-form" name="userpro-settings-form" method="post" action="">

<h3><i class="fas fa-tools"></i><?php _e('Quick Maintenance','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label><?php _e('Clear unused Junk','userpro'); ?></label></th>
		<td>
			<a href="admin.php?page=userpro&tab=settings&userpro_act=clear_unused_uploads" class="up-admin-btn up-admin-btn--dark-blue small"><?php _e('Clear un-used uploads','userpro'); ?></a>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label><?php _e('Clear Members Cache','userpro'); ?></label></th>
		<td>
			<a href="admin.php?page=userpro&tab=settings&userpro_act=clear_cache" class="up-admin-btn up-admin-btn--dark-blue small"><?php _e('Clear Members Cache','userpro'); ?></a>
		</td>
	</tr>
	
        <tr valign="top">
		<th scope="row"><label for="up_delete_cache_interval"><?php _e('Set Interval to Delete Members Cache','userpro'); ?></label></th>
		<td>
			<input type="text" name="up_delete_cache_interval" id="up_delete_cache_interval" value="<?php echo userpro_get_option('up_delete_cache_interval'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Set a interval in days to delete the members cache','userpro'); ?></span>
		</td>
	</tr>
        
	<tr valign="top">
		<th scope="row"><label><?php _e('Clear deleted users stuff','userpro'); ?></label></th>
		<td>
			<a href="admin.php?page=userpro&tab=settings&userpro_act=clear_deleted_users" class="up-admin-btn up-admin-btn--dark-blue small"><?php _e('Clear deleted users stuff','userpro'); ?></a>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label><?php _e('Purge online users data','userpro'); ?></label></th>
		<td>
			<a href="admin.php?page=userpro&tab=settings&userpro_act=reset_online_users" class="up-admin-btn up-admin-btn--dark-blue small"><?php _e('Reset online users','userpro'); ?></a>
            <?php if(get_transient('userpro_users_online') !== FALSE):
                echo count(get_transient('userpro_users_online'));
            else:
                echo '0';
            endif;
            ?>
			 <?php _e('Online','userpro'); ?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label><?php _e('Clear activity stream','userpro'); ?></label></th>
		<td>
			<a href="admin.php?page=userpro&tab=settings&userpro_act=clear_activity" class="up-admin-btn up-admin-btn--dark-blue small"><?php _e('Delete all activity','userpro'); ?></a>
		</td>
	</tr>
	
</table>

<h3><i class="fas fa-bell"></i><?php _e('Module Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="modstate_social"><?php _e('Social Features','userpro'); ?></label></th>
		<td>
			<select name="modstate_social" id="modstate_social" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('modstate_social')); ?>><?php _e('Activate','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('modstate_social')); ?>><?php _e('Deactivate','userpro'); ?></option>
			</select>
                        <span class="up-description"><?php _e('To Enable/Disable Follow feature from user profile','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="modstate_online"><?php _e('Online/Offline Status','userpro'); ?></label></th>
		<td>
			<select name="modstate_online" id="modstate_online" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('modstate_online')); ?>><?php _e('Activate','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('modstate_online')); ?>><?php _e('Deactivate','userpro'); ?></option>
			</select>
  		 <span class="up-description"><?php _e('To Display Online/Offline Status ','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="modstate_showoffline"><?php _e('Show Offline Icon','userpro'); ?></label></th>
		<td>
			<select name="modstate_showoffline" id="modstate_showoffline" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('modstate_showoffline')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('modstate_showoffline')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="hide_online_admin"><?php _e('Hide Administrators from Online Users','userpro'); ?></label></th>
		<td>
			<select name="hide_online_admin" id="hide_online_admin" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('hide_online_admin')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('hide_online_admin')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
</table>

<h3><i class="fas fa-cogs"></i><?php _e('Compatibility Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="ppfix"><?php _e('Profile Pictures Fix','userpro'); ?></label></th>
		<td>
			<select name="ppfix" id="ppfix" class="chosen-select" style="width:300px">
				<option value="a" <?php selected('a', userpro_get_option('ppfix')); ?>><?php _e('Method A','userpro'); ?></option>
				<option value="b" <?php selected('b', userpro_get_option('ppfix')); ?>><?php _e('Method B','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('Try playing with this setting If you encounter issues with viewing profile picture either on single site or multisite install. Play with this setting until your profile pictures appear.','userpro'); ?></span>
		</td>
	</tr>
		<tr valign="top">
		<th scope="row"><label for="pimg"><?php _e('Url Encoding','userpro'); ?></label></th>
		<td>
			<select name="pimg" id="pimg" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('pimg')); ?>><?php _e('On','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('pimg')); ?>><?php _e('Off','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('Try playing with this setting If you encounter issues with viewing profile picture either on single site or multisite install. Play with this setting until your profile pictures appear.','userpro'); ?></span>
		</td>
	</tr>
	
	
	<tr valign="top">
		<th scope="row"><label for="disable_activity_log"><?php _e('Disable Activity Log','userpro'); ?></label></th>
		<td>
			<select name="disable_activity_log" id="disable_activity_log" class="chosen-select" style="width:300px">
				<option value="0" <?php selected('0', userpro_get_option('disable_activity_log')); ?>><?php _e('No','userpro'); ?></option>
				<option value="1" <?php selected('1', userpro_get_option('disable_activity_log')); ?>><?php _e('Yes','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('Please switch off logging activity If you have host problems, your site crashes or cannot save a post, etc.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="allow_dash_display_name"><?php _e('Allow Dash for Display Names','userpro'); ?></label></th>
		<td>
			<select name="allow_dash_display_name" id="allow_dash_display_name" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('allow_dash_display_name')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('allow_dash_display_name')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="use_relative"><?php _e('Path Compatibility','userpro'); ?></label></th>
		<td>
			<select name="use_relative" id="use_relative" class="chosen-select" style="width:300px">
				<option value="relative" <?php selected('relative', userpro_get_option('use_relative')); ?>><?php _e('Use Relative URIs','userpro'); ?></option>
				<option value="full" <?php selected('full', userpro_get_option('use_relative')); ?>><?php _e('Use Full Paths (including domain)','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('If you use CDN or have a problem with thumbnails, try switching this option.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="picture_save_method"><?php _e('External Profile Pictures','userpro'); ?></label></th>
		<td>
			<select name="picture_save_method" id="picture_save_method" class="chosen-select" style="width:300px">
				<option value="internal" <?php selected('internal', userpro_get_option('picture_save_method')); ?>><?php _e('Save them to my server','userpro'); ?></option>
				<option value="external" <?php selected('external', userpro_get_option('picture_save_method')); ?>><?php _e('Use the external source','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('If your host experience some problems when saving an external picture to your uploads directory, try to use the external method.','userpro'); ?></span>
		</td>
	</tr>
<tr valign="top">
		<th scope="row"><label for="twitter_fix"><?php _e('Twitter Fix','userpro'); ?></label></th>
		<td>
			<select name="twitter_fix" id="twitter_fix" class="chosen-select" style="width:300px">
				<option value="a" <?php selected('a', userpro_get_option('twitter_fix')); ?>><?php _e('By Using Session','userpro'); ?></option>
				<option value="b" <?php selected('b', userpro_get_option('twitter_fix')); ?>><?php _e('By Using Cookie','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('Try playing with this setting If you encounter issues with twitter Login/Register.','userpro'); ?></span>
		</td>
	</tr>


</table>

<h3><i class="fas fa-cog"></i><?php _e('General','userpro'); ?></h3>
<table class="form-table">


    <tr valign="top">
        <th scope="row"><label for="ajax-auth"><?php _e('Ajax for Registration/Login','userpro'); ?></label></th>
        <td>
            <select name="ajax-auth" id="ajax-auth" class="chosen-select" style="width:300px">
                <option value="1" <?php selected(1, userpro_get_option('ajax-auth')); ?>><?php _e('Enabled','userpro'); ?></option>
                <option value="0" <?php selected(0, userpro_get_option('ajax-auth')); ?>><?php _e('Disabled','userpro'); ?></option>
            </select>
        </td>
    </tr>

    <tr valign="top">
        <th><label for="stripe_publishable_key"><?php _e('Select Registration Page','userpro'); ?></label></th>
        <td><?php wp_dropdown_pages(array('name' => 'register_page' , 'selected' => userpro_get_option('register_page'))); ?> <span class="description">
			<?php _e('This setting will only be used if Ajax in Login/Registration is disabled' ,'userpro'); ?></span>
        </td>
    </tr>

    <tr valign="top">
        <th><label for="stripe_publishable_key"><?php _e('Select Login Page','userpro'); ?></label></th>
        <td><?php wp_dropdown_pages(array('name' => 'login_page' , 'selected' => userpro_get_option('login_page'))); ?> <span class="description">
			<?php _e('This setting will only be used if Ajax in Login/Registration is disabled' ,'userpro'); ?></span>
        </td>
    </tr>

	<tr valign="top">
		<th scope="row"><label for="rtl"><?php _e('Activate RTL Stylesheet','userpro'); ?></label></th>
		<td>
			<select name="rtl" id="rtl" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('rtl')); ?>><?php _e('RTL Enabled','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('rtl')); ?>><?php _e('RTL Disabled','userpro'); ?></option>
			</select>
        <span class="up-description"><?php _e('If RTL is enabled then content will get display from right to left.','userpro'); ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="users_can_register"><?php _e('Membership','userpro'); ?></label></th>
		<td>
			<select name="users_can_register" id="users_can_register" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('users_can_register')); ?>><?php _e('Anyone can register','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('users_can_register')); ?>><?php _e('Disable registration','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="enable_post_editor"><?php _e('Enable editor for frontend publisher','userpro'); ?></label></th>
		<td>
			<select name="enable_post_editor" id="enable_post_editor" class="chosen-select" style="width:300px">
				<option value="y" <?php selected('y', userpro_get_option('enable_post_editor')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="n" <?php selected('n', userpro_get_option('enable_post_editor')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="enable_connect"><?php _e('Enable Connections','userpro'); ?></label></th>
		<td>
			<select name="enable_connect" id="enable_connect" class="chosen-select" style="width:300px">
				<option value="y" <?php selected('y', userpro_get_option('enable_connect')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="n" <?php selected('n', userpro_get_option('enable_connect')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="enable_save_as_draft"><?php _e('Enable save as draft for frontend publisher','userpro'); ?></label></th>
		<td>
			<select name="enable_save_as_draft" id="enable_save_as_draft" class="chosen-select" style="width:300px">
				<option value="y" <?php selected('y', userpro_get_option('enable_save_as_draft')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="n" <?php selected('n', userpro_get_option('enable_save_as_draft')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="users_approve"><?php _e('New User Approval','userpro'); ?></label></th>
		<td>
			<select name="users_approve" id="users_approve" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('users_approve')); ?>><?php _e('Auto Approve','userpro'); ?></option>
				<option value="2" <?php selected('2', userpro_get_option('users_approve')); ?>><?php _e('Require E-mail Activation','userpro'); ?></option>
				<option value="3" <?php selected('3', userpro_get_option('users_approve')); ?>><?php _e('Require Admin Approval','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="permalink_type"><?php _e('Profile Permalink Structure','userpro'); ?></label></th>
		<td>
			<select name="permalink_type" id="permalink_type" class="chosen-select" style="width:300px">
				<option value="ID" <?php selected('ID', userpro_get_option('permalink_type')); ?>><?php _e('User ID','userpro'); ?></option>
				<option value="username" <?php selected('username', userpro_get_option('permalink_type')); ?>><?php _e('Username','userpro'); ?></option>
				<option value="name" <?php selected('name', userpro_get_option('permalink_type')); ?>><?php _e('Full Name','userpro'); ?></option>
				<option value="display_name" <?php selected('display_name', userpro_get_option('permalink_type')); ?>><?php _e('Display Name','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('User profiles permalink structure setting e.g. /profile/34 or /profile/Username or /profile/FirstName+LastName If you have a problem with permalink structure, you can try to change this setting.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="user_display_name"><?php _e('User Display Name Field','userpro'); ?></label></th>
		<td>
			<select name="user_display_name" id="user_display_name" class="chosen-select" style="width:300px">
				<option value="display_name" <?php selected('display_name', userpro_get_option('user_display_name')); ?>><?php _e('Default (Display Name)','userpro'); ?></option>
				<option value="name" <?php selected('name', userpro_get_option('user_display_name')); ?>><?php _e('Full Name','userpro'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="date_format"><?php _e('Date format','userpro'); ?></label></th>
		<td>
			<input type="text" name="date_format" id="date_format" value="<?php echo userpro_get_option('date_format'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Ex.dd-mm-yy,mm-dd-yy,yy-dd-mm,yy-mm-dd','userpro'); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="date_to_age"><?php _e('Convert date to age','userpro'); ?></label></th>
		<td>
			<select name="date_to_age" id="date_to_age" class="chosen-select" style="width:300px">
				<option value="0" <?php selected(0, userpro_get_option('date_to_age')); ?>><?php _e('No','userpro'); ?></option>
				<option value="1" <?php selected(1, userpro_get_option('date_to_age')); ?>><?php _e('Yes','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('Profile page will show calculated age, instead of date','userpro'); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="user_display_name_key"><?php _e('Replace Display Name with custom field','userpro'); ?></label></th>
		<td>
			<input type="text" name="user_display_name_key" id="user_display_name_key" value="<?php echo userpro_get_option('user_display_name_key'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Enter custom field key to override default display name on profiles with this custom field value.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="hidden_from_view"><?php _e('Fields to hide completely from profile view','userpro'); ?></label></th>
		<td>
			<input type="text" name="hidden_from_view" id="hidden_from_view" value="<?php echo userpro_get_option('hidden_from_view'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('A comma seperated list of custom fields to hide from profile view anyway.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="unverify_on_namechange"><?php _e('Unverify Verified accounts automatically if they change display name','userpro'); ?></label></th>
		<td>
			<select name="unverify_on_namechange" id="unverify_on_namechange" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('unverify_on_namechange')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('unverify_on_namechange')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="verified_badge_by_name"><?php _e('Display verified account badge beside name','userpro'); ?></label></th>
		<td>
			<select name="verified_badge_by_name" id="verified_badge_by_name" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('verified_badge_by_name')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('verified_badge_by_name')); ?>><?php _e('No','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('Should the verified account badge display beside name, or as a standard badge in badges.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="verified_link"><?php _e('Verified Badge Link','userpro'); ?></label></th>
		<td>
			<input type="text" name="verified_link" id="verified_link" value="<?php echo userpro_get_option('verified_link'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Should the verified badge link to a specific page?','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="instant_publish_roles"><?php _e('User Roles that can publish immediately','userpro'); ?></label></th>
		<td>
			<input type="text" name="instant_publish_roles" id="instant_publish_roles" value="<?php echo userpro_get_option('instant_publish_roles'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Enter comma seperated list of user roles to publish automatically using the frontend publisher without waiting admin approval. e.g. author,subscriber,etc.'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="lightbox"><?php _e('Photo Lightbox','userpro'); ?></label></th>
		<td>
			<select name="lightbox" id="lightbox" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('lightbox')); ?>><?php _e('Active','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('lightbox')); ?>><?php _e('Inactive','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('Lightbox for profile photos and other photo uploads. Turn on / off lightbox globally.','userpro'); ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="userpro_enable_webcam"><?php _e('Enable webcam to take profile Picture','userpro'); ?></label></th>
		<td>
			<select name="userpro_enable_webcam" id="userpro_enable_webcam" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('userpro_enable_webcam')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('userpro_enable_webcam')); ?>><?php _e('No','userpro'); ?></option>
			</select>
        <span class="up-description"><?php _e('Enables webcam feature to take a profile picture using webcam','userpro'); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="max_file_size"><?php _e('Maximum file size for uploads','userpro'); ?></label></th>
		<td>
			<input type="text" name="max_file_size" id="max_file_size" value="<?php echo userpro_get_option('max_file_size'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('The maximum file size that user can upload whether it is a file, or a photo. e.g. <code>8388608 bytes = 8MB</code>','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="lightbox"><?php _e('Keep aspect ratio','userpro'); ?></label></th>
		<td>
			<select name="aspect_ratio" id="aspect_ratio" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('aspect_ratio')); ?>><?php _e('Yes','userpro'); ?></option>

				<option value="0" <?php selected(0, userpro_get_option('aspect_ratio')); ?>><?php _e('No','userpro'); ?></option>
			</select>
			<span class="up-description"></span>
		</td>
	</tr>


<tr valign="top">
		<th scope="row"><label for="show_filter"><?php _e('Post by users filter','userpro'); ?></label></th>
		<td>
			<select name="show_filter" id="show_filter" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('show_filter')); ?>><?php _e('Yes','userpro'); ?></option>

				<option value="0" <?php selected(0, userpro_get_option('show_filter')); ?>><?php _e('No','userpro'); ?></option>
			</select>
			
		</td>
	</tr>
<tr valign="top">
		<th scope="row"><label for="categorie_selection"><?php _e('Frontend publisher category selection','userpro'); ?></label></th>
		<td>
			<select name="categorie_selection" id="categorie_selection" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('categorie_selection')); ?>><?php _e('Single Select','userpro'); ?></option>

				<option value="0" <?php selected(0, userpro_get_option('categorie_selection')); ?>><?php _e('Multi Select','userpro'); ?></option>
			</select>
			
		</td>
	</tr>
	
<tr valign="top">
		<th scope="row"><label for="limit_categories"><?php _e('Limit number of categories in frontend publisher','userpro'); ?></label></th>
		<td>
			<input type="text" name="limit_categories" id="limit_categories" value="<?php echo userpro_get_option('limit_categories'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('This option will work only for multiselect categories selection','userpro'); ?></span>
		</td>
	</tr>
<tr valign="top">
		<th scope="row"><label for="sociallogin"><?php _e('Display social login button on registration/login page','userpro'); ?></label></th>
		<td>
			<select name="sociallogin" id="sociallogin" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('sociallogin')); ?>><?php _e('Yes','userpro'); ?></option>

				<option value="0" <?php selected(0, userpro_get_option('sociallogin')); ?>><?php _e('No','userpro'); ?></option>
			</select>
			
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="alphabetical-pagination"><?php _e('Enable Aplhabetical pagination for Members Directory','userpro'); ?></label></th>
		<td>
			<select name="alphabetical_pagination" id="alphabetical_pagination" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('alphabetical_pagination')); ?>><?php _e('Yes','userpro'); ?></option>

				<option value="0" <?php selected(0, userpro_get_option('alphabetical_pagination')); ?>><?php _e('No','userpro'); ?></option>
			</select>
			
		</td>
	</tr>
	
	<tr valign="top">
		
		<th scope="row"><label for="restricted_content_text"><?php _e('Text for Restricted Content','userpro'); ?></label></th>
		<td>
			<textarea name="restricted_content_text" id="restricted_content_text" class="large-text code" rows="3"><?php echo esc_attr(userpro_get_option('restricted_content_text')); ?></textarea>
			<span class="up-description"><?php _e('The variables in {CURLY BRACKETS} are used to represent data and info in text. You can use them to customize your restricted content text.','userpro'); ?><?php userpro_admin_list_builtin_vars_restricted_content(); ?></span>
		</td>
	</tr>
	
	
	<tr valign="top">
		
		<th scope="row"><label for="up_conditional_menu"><?php _e('Enable Conditional Menu','userpro'); ?></label></th>
		<td>
			<select name="up_conditional_menu" id="up_conditional_menu" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('up_conditional_menu')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('up_conditional_menu')); ?>><?php _e('No','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('This option will provide conditional menu as per user\'s login condition. For eg: If user is logged in then it will not display the Login or Register menu in navigation menu and vice versa.' ,'userpro'); ?></span>
		</td>
	</tr>

</table>

<h3><i class="fas fa-palette"></i><?php _e('Appearance and Look','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="googlefont"><?php _e('Use a Google Web Font','userpro'); ?></label></th>
		<td>
			<input type="text" name="googlefont" id="googlefont" value="<?php echo userpro_get_option('googlefont'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('To use a Google Web Font for UserPro plugin, enter the font family name here.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="customfont"><?php _e('Use an installed font','userpro'); ?></label></th>
		<td>
			<input type="text" name="customfont" id="customfont" value="<?php echo userpro_get_option('customfont'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('To override Google font and force plugin to use theme font, or another built in font please enter font face here (e.g. Open Sans, Arial, etc)','userpro'); ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="width"><?php _e('Plugin Default Width','userpro'); ?></label></th>
		<td>
			<input type="text" name="width" id="width" value="<?php echo userpro_get_option('width'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('A pixel or % width. If you want to make the plugin default width take 100% of its parent, just change this setting to 100% (It will take as much space as possible from the parent element). This option can be customized also via shortcode option <code>max_width</code>.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="skin"><?php _e('Default Skin','userpro'); ?></label></th>
		<td>
			<select name="skin" id="skin" class="chosen-select" style="width:300px">
				<?php $skins = userpro_admin_skins(); ?>
				<?php foreach( $skins as $skin ) : if($skin != '.' && $skin != '..') : ?>
				<option value="<?php echo $skin; ?>" <?php selected($skin, userpro_get_option('skin')); ?>><?php echo $skin; ?></option>
				<?php endif; endforeach; ?>
			</select>
			<span class="up-description"><?php _e('This setting will work only with Default layout','userpro')?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="thumb_style"><?php _e('Thumbnail Style','userpro'); ?></label></th>
		<td>
			<select name="thumb_style" id="thumb_style" class="chosen-select" style="width:300px">
				<option value="default" <?php selected('default', userpro_get_option('thumb_style')); ?>><?php _e('Default','userpro'); ?></option>
				<option value="rounded" <?php selected('rounded', userpro_get_option('thumb_style')); ?>><?php _e('Rounded','userpro'); ?></option>
				<option value="abit_rounded" <?php selected('abit_rounded', userpro_get_option('thumb_style')); ?>><?php _e('A bit rounded','userpro'); ?></option>
				<option value="square" <?php selected('square', userpro_get_option('thumb_style')); ?>><?php _e('Square','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('This controls the style of your profile thumbnails globally. If you let it default, these settings will be taken from your active skin.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="profile_lightbox"><?php _e('Profile Photo Lightbox','userpro'); ?></label></th>
		<td>
			<select name="profile_lightbox" id="profile_lightbox" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('profile_lightbox')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('profile_lightbox')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="layout"><?php _e('Fields Layout','userpro'); ?></label></th>
		<td>
			<select name="layout" id="layout" class="chosen-select" style="width:300px">
				<option value="float" <?php selected('float', userpro_get_option('layout')); ?>><?php _e('Float (field label on same line)','userpro'); ?></option>
				<option value="none" <?php selected('none', userpro_get_option('layout')); ?>><?php _e('Block (field label on seperate line)','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="field_icons"><?php _e('Enable Field Icons','userpro'); ?></label></th>
		<td>
			<select name="field_icons" id="field_icons" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('field_icons')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('field_icons')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="hide_admin_bar"><?php _e('Hide Top Bar from Non-Admins','userpro'); ?></label></th>
		<td>
			<select name="hide_admin_bar" id="hide_admin_bar" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('hide_admin_bar')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('hide_admin_bar')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="heading_light"><?php _e('Light Heading Option','userpro'); ?></label></th>
		<td>
			<input type="text" name="heading_light" id="heading_light" value="<?php echo userpro_get_option('heading_light'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('For translation compatibility, enter you "Light" heading color option here.','userpro'); ?></span>
		</td>
	</tr>
	
</table>

<h3><i class="fas fa-check-square"></i><?php _e('Form Validation & Security','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="max_field_length_active"><?php _e('Maximum Field Length Validation','userpro'); ?></label></th>
		<td>
			<select name="max_field_length_active" id="max_field_length_active" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('max_field_length_active')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('max_field_length_active')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="max_field_length"><?php _e('Number of characters allowed','userpro'); ?></label></th>
		<td>
			<input type="text" name="max_field_length" id="max_field_length" value="<?php echo userpro_get_option('max_field_length'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('How many characters should be allowed if the above option is turned on.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="max_field_length_include"><?php _e('Fields that need to be validated for maximum length','userpro'); ?></label></th>
		<td>
			<input type="text" name="max_field_length_include" id="max_field_length_include" value="<?php echo userpro_get_option('max_field_length_include'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Enter a comma seperated list of field keys to include in the maximum field length validation.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="phonefields"><?php _e('Fields that need to be validated as a phone number','userpro'); ?></label></th>
		<td>
			<input type="text" name="phonefields" id="phonefields" value="<?php echo userpro_get_option('phonefields'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Enter a comma seperated list of field keys to automatically validate as a phone number field.','userpro'); ?></span>
		</td>


	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="phonefields_regex"><?php _e('Phone Number Regular Expression','userpro'); ?></label></th>
		<td>
			<input type="text" name="phonefields_regex" id="phonefields_regex" value="<?php echo userpro_get_option('phonefields_regex'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('The regex used to validate the phone number fields specified above, please do not change this unless you know what you are doing.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="mailchimp_checkbox_condition"><?php _e('Check the MailChimp Subscribe checkbox by default','userpro'); ?></label></th>
		<td>
			<select name="mailchimp_checkbox_condition" id="mailchimp_checkbox_condition" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('mailchimp_checkbox_condition')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('mailchimp_checkbox_condition')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row"><label for="min_field_length_active"><?php _e('Minimum Field Length Validation','userpro'); ?></label></th>
		<td>
			<select name="min_field_length_active" id="min_field_length_active" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('min_field_length_active')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('min_field_length_active')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="min_field_length"><?php _e('Minimum number of characters allowed','userpro'); ?></label></th>
		<td>
			<input type="text" name="min_field_length" id="min_field_length" value="<?php echo userpro_get_option('min_field_length'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Minimum many characters should be allowed if the above option is turned on.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="min_field_length_include"><?php _e('Fields that need to be validated for minimun length','userpro'); ?></label></th>
		<td>
			<input type="text" name="min_field_length_include" id="min_field_length_include" value="<?php echo userpro_get_option('min_field_length_include'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Enter a comma seperated list of field keys to include in the minimum field length validation.','userpro'); ?></span>
		</td>
	</tr>
	
</table>

<h3><i class="fas fa-user-alt"></i><?php _e('Profile Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="up_modern_layout"><?php _e('Select layout','userpro'); ?></label></th>
		<td>
			<select name="up_modern_layout" id="up_modern_layout" class="chosen-select" style="width:300px">
				<option value="0" <?php selected(0, userpro_get_option('up_modern_layout')); ?>><?php _e('Default','userpro'); ?></option>
				<option value="1" <?php selected(1, userpro_get_option('up_modern_layout')); ?>><?php _e('Modern Layout','userpro'); ?></option>
     			<option value="2" <?php selected(2, userpro_get_option('up_modern_layout')); ?>><?php _e('Classic Layout','userpro'); ?></option>
     			<option value="3" <?php selected(3, userpro_get_option('up_modern_layout')); ?>><?php _e('Grand Layout','userpro'); ?></option>
     			<option value="4" <?php selected(4, userpro_get_option('up_modern_layout')); ?>><?php _e('Professional Layout','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="allow_guests_view_profiles"><?php _e('Allow guests to view other profiles','userpro'); ?></label></th>
		<td>
			<select name="allow_guests_view_profiles" id="allow_guests_view_profiles" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('allow_guests_view_profiles')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('allow_guests_view_profiles')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="allow_users_view_profiles"><?php _e('Allow members to view other member profiles','userpro'); ?></label></th>
		<td>
			<select name="allow_users_view_profiles" id="allow_users_view_profiles" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('allow_users_view_profiles')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('allow_users_view_profiles')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="roles_can_edit_profiles[]"><?php _e('Roles that can edit other users profile','userpro'); ?></label></th>

		<td>
			<select name="roles_can_edit_profiles[]" id="roles_can_edit_profiles[]" multiple="multiple" class="chosen-select" style="width:300px" data-placeholder="<?php _e('Select roles','userpro'); ?>">
	<option value='none'><?php _e('None','userpro');?></option>				
<?php
				
				if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
				$roles = $wp_roles->get_names();
				foreach($roles as $k=>$v) {
					if ($k != 'administrator') {
				?>
				<option value="<?php echo $k; ?>" <?php userpro_is_selected($k, userpro_get_option('roles_can_edit_profiles') ); ?>><?php echo $v; ?></option>
				<?php }
				} ?>
			</select>
			<span class="up-description"><?php _e('For example,If you want to some special role user has permission to edit users profile from front end then select these role.by default admin user can edit all users profile.','userpro'); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="roles_can_view_profiles[]"><?php _e('Roles that can view other profiles','userpro'); ?></label></th>
		<td>
			<select name="roles_can_view_profiles[]" id="roles_can_view_profiles[]" multiple="multiple" class="chosen-select" style="width:300px" data-placeholder="<?php _e('Select roles','userpro'); ?>">
				<?php
				if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
				$roles = $wp_roles->get_names();
				foreach($roles as $k=>$v) {
					if ($k != 'administrator') {
				?>
				<option value="<?php echo $k; ?>" <?php userpro_is_selected($k, userpro_get_option('roles_can_view_profiles') ); ?>><?php echo $v; ?></option>
				<?php }
				} ?>
			</select>
			<span class="up-description"><?php _e('For example, If you do not want users to view other profiles but want to allow these special roles to always view profiles, including administrators.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="upgrade_role_after_verfied"><?php _e('Assign role to verified users','userpro'); ?></label></th>
		<td>
			<select name="upgrade_role_after_verfied" id="upgrade_role_after_verfied" class="chosen-select" style="width:300px" data-placeholder="<?php _e('Select role','userpro'); ?>">
				<option value='none'><?php _e('None','userpro');?></option>
				<?php
				if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
				$roles = $wp_roles->get_names();
				foreach($roles as $k=>$v) {
					if ($k != 'administrator') {
				?>
				<option value="<?php echo $k; ?>" <?php userpro_is_selected($k, userpro_get_option('upgrade_role_after_verfied') ); ?>><?php echo $v; ?></option>
				<?php }
				} ?>
			</select>
			<span class="up-description"><?php _e('If you don\'t want to change role when user gets verified , select none in dropdown.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="allow_users_verify_request"><?php _e('Allow users to request verified status','userpro'); ?></label></th>
		<td>
			<select name="allow_users_verify_request" id="allow_users_verify_request" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('allow_users_verify_request')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('allow_users_verify_request')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="user_can_delete_profile"><?php _e('Allow users to delete their profiles','userpro'); ?></label></th>
		<td>
			<select name="user_can_delete_profile" id="user_can_delete_profile" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('user_can_delete_profile')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('user_can_delete_profile')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="use_default_avatars"><?php _e('Use default gravatars until users upload a custom avatar for this site','userpro'); ?></label></th>
		<td>
			<select name="use_default_avatars" id="use_default_avatars" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('use_default_avatars')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('use_default_avatars')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="admin_user_notices"><?php _e('Allow admins to set custom notice on user accounts','userpro'); ?></label></th>
		<td>
			<select name="admin_user_notices" id="admin_user_notices" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('admin_user_notices')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('admin_user_notices')); ?>><?php _e('No','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('e.g. Set a custom warning to any user profile for example: This account is for test purposes only, etc.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="show_user_notices_him"><?php _e('Show custom user notice to the logged-in user','userpro'); ?></label></th>
		<td>
			<select name="show_user_notices_him" id="show_user_notices_him" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('show_user_notices_him')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('show_user_notices_him')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="show_user_notices"><?php _e('Show custom user notice to the public','userpro'); ?></label></th>
		<td>
			<select name="show_user_notices" id="show_user_notices" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('show_user_notices')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('show_user_notices')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="show_flag_in_profile"><?php _e('Display User Country Flag in Profile','userpro'); ?></label></th>
		<td>
			<select name="show_flag_in_profile" id="show_flag_in_profile" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('show_flag_in_profile')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('show_flag_in_profile')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="show_flag_in_badges"><?php _e('Display User Country Flag in Badges','userpro'); ?></label></th>
		<td>
			<select name="show_flag_in_badges" id="show_flag_in_badges" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('show_flag_in_badges')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('show_flag_in_badges')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="show_badges_profile"><?php _e('Display badges on profile','userpro'); ?></label></th>
		<td>
			<select name="show_badges_profile" id="show_badges_profile" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('show_badges_profile')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('show_badges_profile')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="default_background_img"><?php _e('Default background image','userpro'); ?></label></th>
		<td>
		<?php $default_backgrnd_img_option = userpro_get_option('default_background_img');
			if(!empty($default_backgrnd_img_option)){$display_condition = 'display:inline-block;';}else{$display_condition='display:none;';}
		?>
			<label for="default_background_img">
			
				<img src="<?php echo userpro_get_option('default_background_img'); ?>" class="default_background_img_src" style="<?php echo $display_condition;?>"/>
    			
    			<input id="default_background_img" type="text" size="36" name="default_background_img" value="<?php echo userpro_get_option('default_background_img'); ?>" style="margin-bottom: 1% !important;" />
    			<input id="default_background_img_upload_button" class="up-admin-btn up-admin-btn--dark-blue small approve" type="button" value="Upload Image" style="margin: 0 !important;" />
    			
    			<input id="default_background_img_remove_button" class="up-admin-btn up-admin-btn--dark-blue small remove" type="button" value="Remove Image" <?php echo $display_condition;?>"/>
			
			</label>
			<span class="up-description up-description--inline"><?php _e('Enter a URL or upload an image. Recommended size : 1200x250','userpro'); ?></span>
		</td>
	</tr>
	
</table>

<h3><i class="fas fa-retweet"></i><?php _e('Redirection & Backend Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="userpro_panic_key"><?php _e('Panic Key Setting','userpro'); ?></label></th>
		<td>
			<input type="text" name="userpro_panic_key" id="userpro_panic_key" value="<?php echo userpro_get_option('userpro_panic_key'); ?>" class="regular-text" />
			<span class="up-description"><?php printf(__('The panic key helps you access WP-admin screen If you enabled the hide backend setting below, and for some reason you cannot get back into your site. To enter your backend with the panic key use this URL: <strong>%s</strong>','userpro'), add_query_arg( 'userpro_panic_key', userpro_get_option('userpro_panic_key'), wp_login_url() ) ); ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="allow_dashboard_for_these_roles"><?php _e('Enable these roles to view the WP-admin (backend)','userpro'); ?></label></th>
		<td>
			<input type="text" name="allow_dashboard_for_these_roles" id="allow_dashboard_for_these_roles" value="<?php echo userpro_get_option('allow_dashboard_for_these_roles'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('By default, UserPro hides backend access from non-admins, however you can type a comma seperated list here of user roles to allow them to access the dashboard regardless of any other setting. example: author,editor','userpro'); ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="backend_users_change"><?php _e('Make Users Backend link to frontend profiles','userpro'); ?></label></th>
		<td>
			<select name="backend_users_change" id="backend_users_change" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('backend_users_change')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('backend_users_change')); ?>><?php _e('No','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('If set on, users backend will link to frontend profile view/edit. Turn off to allow backend user editing.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="redirect_author_to_profile"><?php _e('Redirect Author Archive to UserPro Profile Automatically','userpro'); ?></label></th>
		<td>
			<select name="redirect_author_to_profile" id="redirect_author_to_profile" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('redirect_author_to_profile')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('redirect_author_to_profile')); ?>><?php _e('No','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('If this is set to yes, author archives will be redirected to author profile pages automatically.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="dashboard_redirect_users"><?php _e('Redirect backend to front-end','userpro'); ?></label></th>
		<td>
			<select name="dashboard_redirect_users" id="dashboard_redirect_users" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('dashboard_redirect_users')); ?>><?php _e('Redirect to front-end profile view','userpro'); ?></option>
				<option value="2" <?php selected(2, userpro_get_option('dashboard_redirect_users')); ?>><?php _e('Redirect to custom URL','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('dashboard_redirect_users')); ?>><?php _e('Do not redirect','userpro'); ?></option>
			</select>
			<input type="text" name="dashboard_redirect_users_url" id="dashboard_redirect_users_url" value="<?php echo userpro_get_option('dashboard_redirect_users_url'); ?>" class="regular-text userpro-admin-hide-input" placeholder="<?php _e('If you choose cutom URL for this redirect','userpro'); ?>" />
			<span class="up-description"><?php _e('You can redirect users to the built-in front-end pages by the plugin to hide backend access from them.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="profile_redirect_users"><?php _e('Redirect backend profile to front-end','userpro'); ?></label></th>
		<td>
			<select name="profile_redirect_users" id="profile_redirect_users" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('profile_redirect_users')); ?>><?php _e('Redirect to front-end profile edit','userpro'); ?></option>
				<option value="2" <?php selected(2, userpro_get_option('profile_redirect_users')); ?>><?php _e('Redirect to custom URL','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('profile_redirect_users')); ?>><?php _e('Do not redirect','userpro'); ?></option>
			</select>
			<input type="text" name="profile_redirect_users_url" id="profile_redirect_users_url" value="<?php echo userpro_get_option('profile_redirect_users_url'); ?>" class="regular-text userpro-admin-hide-input" placeholder="<?php _e('If you choose custom URL for this redirect','userpro'); ?>" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="register_redirect_users"><?php _e('Redirect backend registration to front-end','userpro'); ?></label></th>
		<td>
			<select name="register_redirect_users" id="register_redirect_users" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('register_redirect_users')); ?>><?php _e('Redirect to front-end registration','userpro'); ?></option>
				<option value="2" <?php selected(2, userpro_get_option('register_redirect_users')); ?>><?php _e('Redirect to custom URL','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('register_redirect_users')); ?>><?php _e('Do not redirect','userpro'); ?></option>
			</select>
			<input type="text" name="register_redirect_users_url" id="register_redirect_users_url" value="<?php echo userpro_get_option('register_redirect_users_url'); ?>" class="regular-text userpro-admin-hide-input" placeholder="<?php _e('If you choose custom URL for this redirect','userpro'); ?>" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="login_redirect_users"><?php _e('Redirect backend login to front-end','userpro'); ?></label></th>
		<td>
			<select name="login_redirect_users" id="login_redirect_users" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('login_redirect_users')); ?>><?php _e('Redirect to front-end login','userpro'); ?></option>
				<option value="2" <?php selected(2, userpro_get_option('login_redirect_users')); ?>><?php _e('Redirect to custom URL','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('login_redirect_users')); ?>><?php _e('Do not redirect','userpro'); ?></option>
			</select>
			<input type="text" name="login_redirect_users_url" id="login_redirect_users_url" value="<?php echo userpro_get_option('login_redirect_users_url'); ?>" class="regular-text userpro-admin-hide-input" placeholder="<?php _e('If you choose custom URL for this redirect','userpro'); ?>" />
		</td>
	</tr>
	
</table>

<h3><i class="fas fa-sign-in-alt"></i><?php _e('Login Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="show_logout_login"><?php _e('Show logout If user is logged in already','userpro'); ?></label></th>
		<td>
			<select name="show_logout_login" id="show_logout_login" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('show_logout_login')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('show_logout_login')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="after_login"><?php _e('After a successful login','userpro'); ?></label></th>
		<td>
			<select name="after_login" id="after_login" class="chosen-select" style="width:300px">
				<option value="no_redirect" <?php selected('no_redirect', userpro_get_option('after_login')); ?>><?php _e('Refresh page only','userpro'); ?></option>
				<option value="profile" <?php selected('profile', userpro_get_option('after_login')); ?>><?php _e('Redirect user to front-end profile','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('You can also redirect users to custom URL by passing <code>login_redirect</code> valid URL in your shortcode.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="show_admin_after_login"><?php _e('Redirect admins to dashboard always','userpro'); ?></label></th>
		<td>
			<select name="show_admin_after_login" id="show_admin_after_login" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('show_admin_after_login')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('show_admin_after_login')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
</table>

<h3><i class="fab fa-wpforms"></i><?php _e('Registration Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="show_logout_register"><?php _e('Show logout If user is logged in already','userpro'); ?></label></th>
		<td>
			<select name="show_logout_register" id="show_logout_register" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('show_logout_register')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('show_logout_register')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="after_register_autologin"><?php _e('Auto-Login users after registration','userpro'); ?></label></th>
		<td>
			<select name="after_register_autologin" id="after_register_autologin" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('after_register_autologin')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('after_register_autologin')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="after_register"><?php _e('After a successful registration','userpro'); ?></label></th>
		<td>
			<select name="after_register" id="after_register" class="chosen-select" style="width:300px">
				<option value="no_redirect" <?php selected('no_redirect', userpro_get_option('after_register')); ?>><?php _e('Refresh page only','userpro'); ?></option>
				<option value="profile" <?php selected('profile', userpro_get_option('after_register')); ?>><?php _e('Redirect user to front-end profile','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('You can also redirect users to custom URL by passing <code>register_redirect</code> valid URL in your shortcode.','userpro'); ?></span>
		</td>
	</tr>

<tr valign="top">
		<th scope="row"><label for="Update_role"><?php _e('Update User Role After admin approval','userpro'); ?></label></th>
		<td>
			<select name="update_role" id="update_role" class="chosen-select" style="width:300px" data-placeholder="<?php _e('No Role','userpro'); ?>">
				<option value="no_role" <?php selected('no_role', userpro_get_option('update_role')); ?>><?php echo __('Default','userpro'); ?></option>
				<?php
				if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
				$roles = $wp_roles->get_names();
				foreach($roles as $k=>$v) {
				?>
				<option value="<?php echo $k; ?>" <?php selected($k, userpro_get_option('update_role')); ?>><?php echo $v; ?></option>
				<?php } ?>
			</select>
			<span class="up-description"><?php _e('New users will be assigned to this role automatically ones admin approv the user.','userpro'); ?></span>
		</td>
	</tr>

	
	<tr valign="top">
		<th scope="row"><label for="default_role"><?php _e('Default Role for New Users','userpro'); ?></label></th>
		<td>
			<select name="default_role" id="default_role" class="chosen-select" style="width:300px" data-placeholder="<?php _e('No Role','userpro'); ?>">
				<option value="no_role" <?php selected('no_role', userpro_get_option('default_role')); ?>><?php echo __('No Role','userpro'); ?></option>
				<?php
				if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
				$roles = $wp_roles->get_names();
				foreach($roles as $k=>$v) {
				?>
				<option value="<?php echo $k; ?>" <?php selected($k, userpro_get_option('default_role')); ?>><?php echo $v; ?></option>
				<?php } ?>
			</select>
			<span class="up-description"><?php _e('New users will be assigned to this role automatically If you do not allow them to choose role.','userpro'); ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="allowed_roles[]"><?php _e('Allowed Roles during Registration','userpro'); ?></label></th>
		<td>
			<select name="allowed_roles[]" id="allowed_roles[]" multiple="multiple" class="chosen-select" style="width:300px" data-placeholder="<?php _e('Select roles','userpro'); ?>">
				<?php
				if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
				$roles = $wp_roles->get_names();
				foreach($roles as $k=>$v) {
					if($v == 'Administrator'){
						continue;
					}
				?>
				<option value="<?php echo $k; ?>" <?php userpro_is_selected($k, userpro_get_option('allowed_roles') ); ?>><?php echo $v; ?></option>
				<?php } ?>
			</select>
			<span class="up-description"><?php _e('If you enable users to select their role, this option can limit allowed roles for user.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="terms_agree"><?php _e('Allow Terms & Conditions Text Before Registration','userpro'); ?></label></th>
		<td>
			<select name="terms_agree" id="terms_agree" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('terms_agree')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('terms_agree')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="terms_agree_text"><?php _e('Terms & Conditions Text/HTML','userpro'); ?></label></th>
		<td><textarea name="terms_agree_text" id="terms_agree_text" class="large-text code" rows="3"><?php echo esc_attr(userpro_get_option('terms_agree_text')); ?></textarea></td>
	</tr>

    <tr valign="top">
        <th scope="row"><label for="userpro_allow_email_domains"><?php _e('Allow Email Domains for Registration','userpro'); ?></label></th>
        <td>
            <input type="text" name="userpro_allow_email_domains" id="userpro_allow_email_domains" value="<?php echo userpro_get_option('userpro_allow_email_domains'); ?>" class="regular-text" />
            <span class="up-description"><?php _e('A comma seperated list of Email Domains to be allowed for registering on the website. Eg - @hotmail.com, @yahoo.com , leave it blank to allow all email domains','userpro'); ?></span>
        </td>
    </tr>
	
	<tr valign="top">
		<th scope="row"><label for="userpro_block_email_domains"><?php _e('Block Email Domains from Registration','userpro'); ?></label></th>
		<td>
			<input type="text" name="userpro_block_email_domains" id="userpro_block_email_domains" value="<?php echo userpro_get_option('userpro_block_email_domains'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('A comma seperated list of Email Domains to be blocked for registering on the website. Eg - @hotmail.com, @yahoo.com ','userpro'); ?></span>
		</td>
	</tr>
	
</table>

<h3><i class="fas fa-sign-out-alt"></i><?php _e('Logout Page Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="logout_uri"><?php _e('After User Logout','userpro'); ?></label></th>
		<td>
			<select name="logout_uri" id="logout_uri" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('logout_uri')); ?>><?php _e('Redirect to Homepage','userpro'); ?></option>
				<option value="2" <?php selected(2, userpro_get_option('logout_uri')); ?>><?php _e('Redirect to Login Page','userpro'); ?></option>
				<option value="3" <?php selected(3, userpro_get_option('logout_uri')); ?>><?php _e('Redirect to Custom Page (enter below)','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="logout_uri_custom"><?php _e('Custom Logout Redirect','userpro'); ?></label></th>
		<td>
			<input type="text" name="logout_uri_custom" id="logout_uri_custom" value="<?php echo userpro_get_option('logout_uri_custom'); ?>" class="regular-text" />
		</td>
	</tr>
	
</table>

<h3><i class="fas fa-user-lock"></i><?php _e('Password Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="reset_admin_pass"><?php _e('Allow password to be reset for Admin Accounts','userpro'); ?></label></th>
		<td>
			<select name="reset_admin_pass" id="reset_admin_pass" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('reset_admin_pass')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('reset_admin_pass')); ?>><?php _e('No','userpro'); ?></option>
			</select>

		</td>
	</tr>

</table>

<h3><i class="fab fa-facebook-f"></i><?php _e('Facebook Integration','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="facebook_connect"><?php _e('Allow Facebook Social Connect','userpro'); ?></label></th>
		<td>
			<select name="facebook_connect" id="facebook_connect" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('facebook_connect')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('facebook_connect')); ?>><?php _e('No','userpro'); ?></option>
			</select>

		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="facebook_app_id"><?php _e('Facebook App ID','userpro'); ?></label></th>
		<td>
			<input type="text" name="facebook_app_id" id="facebook_app_id" value="<?php echo userpro_get_option('facebook_app_id'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Open <a href="https://developers.facebook.com/apps">https://developers.facebook.com/apps</a> create a new app and edit its settings to make it work on your domain. In App Settings, please paste the App ID or API Key into this field.','userpro'); ?></span>
		</td>
	</tr>
	
	
	<!-- Facebook Autopost Bring Back , Added By Rahul -->
		<tr valign="top">

		<th scope="row"><label for="facebook_publish_autopost"><?php _e('Auto Post on users Facebook wall on post publish from frontend publisher','userpro'); ?></label></th>

		<td>

			<select name="facebook_publish_autopost" id="facebook_publish_autopost" class="chosen-select" style="width:300px">

				<option value="1" <?php selected('1', userpro_get_option('facebook_publish_autopost')); ?>><?php _e('Yes','userpro'); ?></option>

				<option value="0" <?php selected('0', userpro_get_option('facebook_publish_autopost')); ?>><?php _e('No','userpro'); ?></option>

			</select>
			<span class="up-description"><?php _e('When a user post a new post from frontend publisher, a post can be added automatically to his wall.','userpro'); ?></span>

		</td>

	</tr>

	

	<tr valign="top">

		<th scope="row"><label for="facebook_publish_autopost_name"><?php _e('Post Name','userpro'); ?></label></th>

		<td>

			<input type="text" name="facebook_publish_autopost_name" id="facebook_publish_autopost_name" value="<?php echo userpro_get_option('facebook_publish_autopost_name'); ?>" class="regular-text" />

			<span class="up-description"><?php _e('e.g. Check out this website!, Welcome to XX website, etc.','userpro'); ?></span>

		</td>

	</tr>

	

	<tr valign="top">

		<th scope="row"><label for="facebook_publish_autopost_caption"><?php _e('Post Caption','userpro'); ?></label></th>

		<td>

			<input type="text" name="facebook_publish_autopost_caption" id="facebook_publish_autopost_caption" value="<?php echo userpro_get_option('facebook_publish_autopost_caption'); ?>" class="regular-text" />

			<span class="up-description"><?php _e('e.g. URL or small text that appears below the post name.','userpro'); ?></span>

		</td>

	</tr>

	

	<tr valign="top">

		<th scope="row"><label for="facebook_publish_autopost_body"><?php _e('Post Body','userpro'); ?></label></th>

		<td>

			<input type="text" name="facebook_publish_autopost_body" id="facebook_publish_autopost_body" value="<?php echo userpro_get_option('facebook_publish_autopost_body'); ?>" class="regular-text" />

			<span class="up-description"><?php _e('e.g. This is the main body of post which is displayed before the description of the wall post. e.g. Check out this great item and save 50%','userpro'); ?></span>

		</td>

	</tr>

	

	<tr valign="top">

		<th scope="row"><label for="facebook_publish_autopost_description"><?php _e('Post Description','userpro'); ?></label></th>

		<td>

			<input type="text" name="facebook_publish_autopost_description" id="facebook_publish_autopost_description" value="<?php echo userpro_get_option('facebook_publish_autopost_description'); ?>" class="regular-text" />

			<span class="up-description"><?php _e('This is the full description of the Facebook wall post.','userpro'); ?></span>

		</td>

	</tr>

	

	<tr valign="top">

		<th scope="row"><label for="facebook_publish_autopost_link"><?php _e('Post Link','userpro'); ?></label></th>

		<td>

			<input type="text" name="facebook_publish_autopost_link" id="facebook_publish_autopost_link" value="<?php echo userpro_get_option('facebook_publish_autopost_link'); ?>" class="regular-text" />

			<span class="up-description"><?php _e('The user will be taken to that URL address when they click on that post.','userpro'); ?></span>

		</td>

	</tr>
	
		<tr valign="top">

		<th scope="row"><label for="facebook_follow_autopost"><?php _e('Auto Post on users Facebook wall when user follows someone','userpro'); ?></label></th>

		<td>

			<select name="facebook_follow_autopost" id="facebook_follow_autopost" class="chosen-select" style="width:300px">

				<option value="1" <?php selected('1', userpro_get_option('facebook_follow_autopost')); ?>><?php _e('Yes','userpro'); ?></option>

				<option value="0" <?php selected('0', userpro_get_option('facebook_follow_autopost')); ?>><?php _e('No','userpro'); ?></option>

			</select>

			<span class="up-description"><?php _e('When a user follows someone, a post can be added automatically to his facebook wall.','userpro'); ?></span>

		</td>

	</tr>

	

	<tr valign="top">

		<th scope="row"><label for="facebook_follow_autopost_name"><?php _e('Post Name','userpro'); ?></label></th>

		<td>

			<input type="text" name="facebook_follow_autopost_name" id="facebook_follow_autopost_name" value="<?php echo userpro_get_option('facebook_follow_autopost_name'); ?>" class="regular-text" />

			<span class="up-description"><?php _e('e.g. Check out this website!, Welcome to XX website, etc.','userpro'); ?></span>

		</td>

	</tr>

	

	<tr valign="top">

	<th scope="row"><label for="facebook_follow_autopost_caption"><?php _e('Post Caption','userpro'); ?></label></th>

		<td>

			<input type="text" name="facebook_follow_autopost_caption" id="facebook_follow_autopost_caption" value="<?php echo userpro_get_option('facebook_follow_autopost_caption'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('e.g. URL or small text that appears below the post name.','userpro'); ?></span>

		</td>

	</tr>

	

	<tr valign="top">

		<th scope="row"><label for="facebook_follow_autopost_body"><?php _e('Post Body','userpro'); ?></label></th>

		<td>

			<input type="text" name="facebook_follow_autopost_body" id="facebook_follow_autopost_body" value="<?php echo userpro_get_option('facebook_follow_autopost_body'); ?>" class="regular-text" />

			<span class="up-description"><?php _e('e.g. This is the main body of post which is displayed before the description of the wall post. e.g. Check out this great item and save 50%','userpro'); ?></span>

		</td>

	</tr>

	

	<tr valign="top">


		<th scope="row"><label for="facebook_follow_autopost_description"><?php _e('Post Description','userpro'); ?></label></th>

		<td>

			<input type="text" name="facebook_follow_autopost_description" id="facebook_follow_autopost_description" value="<?php echo userpro_get_option('facebook_follow_autopost_description'); ?>" class="regular-text" />

			<span class="up-description"><?php _e('This is the full description of the Facebook wall post.','userpro'); ?></span>

		</td>

	</tr>

	

	<tr valign="top">

		<th scope="row"><label for="facebook_follow_autopost_link"><?php _e('Post Link','userpro'); ?></label></th>

		<td>

			<input type="text" name="facebook_follow_autopost_link" id="facebook_follow_autopost_link" value="<?php echo userpro_get_option('facebook_follow_autopost_link'); ?>" class="regular-text" />

			<span class="up-description"><?php _e('The user will be taken to that URL address when they click on that post.','userpro'); ?></span>

		</td>

	</tr>

</table>


<h3><i class="fab fa-linkedin-in"></i><?php _e('Linkedin Integration','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="linkedin_connect"><?php _e('Allow linkedin Social Connect','userpro'); ?></label></th>
		<td>
			<select name="linkedin_connect" id="linkedin_connect" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('linkedin_connect')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('linkedin_connect')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="linkedin_app_key"><?php _e('linkedin App Key','userpro'); ?></label></th>
		<td>
         <input type="text" name="linkedin_app_key" id="linkedin_app_key" value="<?php echo userpro_get_option('linkedin_app_key'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Open <a href="https://www.linkedin.com/developer/apps">https://www.linkedin.com/developer/apps</a> create a new app and edit its settings to make it work on your domain. In App Settings, please paste the App ID or API Key into this field.','userpro'); ?></span>
	
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="linkedin_Secret_Key"><?php _e('linkedin Secret Key','userpro'); ?></label></th>
		<td>
			<input type="text" name="linkedin_Secret_Key" id="linkedin_Secret_Key" value="<?php echo userpro_get_option('linkedin_Secret_Key'); ?>" class="regular-text" />
			
		</td>
	</tr>

    <tr valign="top">
        <th scope="row"><label for="linkedin_redirect_url"><?php _e('linkedin Redirect URL','userpro'); ?></label></th>
        <td>
            <input type="text" name="linkedin_redirect_url" id="linkedin_redirect_url" value="<?php echo userpro_get_option('linkedin_redirect_url'); ?>" class="regular-text" />
            <span class="up-description"><?php _e('This URL must be in the allowed Redirect URL in your linkedin app. example : ','userpro'); ?> <?php echo get_site_url().'/wp-content/plugins/userpro/lib/linkedin/auth.php'  ?></span>


        </td>
    </tr>

</table>	
<h3><i class="fab fa-instagram"></i><?php _e('Instagram Integration','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="instagram_connect"><?php _e('Allow Instagram Social Connect','userpro'); ?></label></th>
		<td>
			<select name="instagram_connect" id="instagram_connect" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('instagram_connect')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('instagram_connect')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="instagram_app_key"><?php _e('Instagram Client ID','userpro'); ?></label></th>
		<td>
         <input type="text" name="instagram_app_key" id="instagram_app_key" value="<?php echo userpro_get_option('instagram_app_key'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('Open <a href="http://instagram.com/developer/">http://instagram.com/developer/</a> create a new app and edit its settings to make it work on your domain. In App Settings, please paste the Client ID into this field.','userpro'); ?></span>
	
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="instagram_Secret_Key"><?php _e('Instagram Client Secret','userpro'); ?></label></th>
		<td>
			<input type="text" name="instagram_Secret_Key" id="instagram_Secret_Key" value="<?php echo userpro_get_option('instagram_Secret_Key'); ?>" class="regular-text" />
			
		</td>
	</tr>

    <tr valign="top">
        <th scope="row"><label for="instagram_redirect_url"><?php _e('Instagram Redirect URL','userpro'); ?></label></th>
        <td>
            <input type="text" name="instagram_redirect_url" id="instagram_redirect_url" value="<?php echo userpro_get_option('instagram_redirect_url'); ?>" class="regular-text" />
            <span class="up-description"><?php _e('This URL must be in the allowed Redirect URL in your instagram app. example : ','userpro'); ?> <?php echo get_site_url().'/wp-content/plugins/userpro/lib/instagram/auth.php'  ?></span>
        </td>
    </tr>

	
	
</table>
<h3><i class="fab fa-twitter"></i><?php _e('Twitter Integration','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="twitter_connect"><?php _e('Allow Twitter Social Connect','userpro'); ?></label></th>
		<td>
			<select name="twitter_connect" id="twitter_connect" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('twitter_connect')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('twitter_connect')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="twitter_consumer_key"><?php _e('Consumer key','userpro'); ?></label></th>
		<td>
			<input type="text" name="twitter_consumer_key" id="twitter_consumer_key" value="<?php echo userpro_get_option('twitter_consumer_key'); ?>" class="regular-text" />
			<span class="up-description"><?php _e('You must first create an app at <a href="https://dev.twitter.com/apps/">https://dev.twitter.com/apps/</a> and enter your consumer keys to enable twitter connect.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="twitter_consumer_secret"><?php _e('Consumer secret','userpro'); ?></label></th>
		<td>
			<input type="text" name="twitter_consumer_secret" id="twitter_consumer_secret" value="<?php echo userpro_get_option('twitter_consumer_secret'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="twitter_autopost"><?php _e('Auto Post on users Twitter timeline','userpro'); ?></label></th>
		<td>
			<select name="twitter_autopost" id="twitter_autopost" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('twitter_autopost')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('twitter_autopost')); ?>><?php _e('No','userpro'); ?></option>
			</select>
			<span class="up-description"><?php _e('When they first connect to your site.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="twitter_autopost_msg"><?php _e('Twitter timeline message','userpro'); ?></label></th>
		<td>
			<textarea name="twitter_autopost_msg" id="twitter_autopost_msg" class="large-text code" rows="3"><?php echo esc_attr(userpro_get_option('twitter_autopost_msg')); ?></textarea>
			<span class="up-description"><?php _e('If you enabled the above option to automatically post on users timeline.','userpro'); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="twitter_signin_redirect"><?php _e('Custom Redirect URL After Login','userpro'); ?></label></th>
		<td>
			<input type="text" name="twitter_signin_redirect" id="twitter_signin_redirect" value="<?php echo userpro_get_option('twitter_signin_redirect'); ?>" class="regular-text" />
            <span class="up-description"><?php _e('This redirect URL must be same like Twitter callback URL. example : ','userpro'); ?> <?php echo get_site_url().'/wp-content/plugins/userpro/lib/twitterauth/auth.php'  ?></span>
		</td>
	</tr>
	
</table>

<h3><i class="fab fa-google-plus-g"></i><?php _e('Google Integration','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="google_connect"><?php _e('Allow Google Social Connect','userpro'); ?></label></th>
		<td>
			<select name="google_connect" id="google_connect" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('google_connect')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('google_connect')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="google_client_id"><?php _e('Client ID','userpro'); ?></label></th>
		<td>
			<input type="text" name="google_client_id" id="google_client_id" value="<?php echo userpro_get_option('google_client_id'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="google_client_secret"><?php _e('Client secret','userpro'); ?></label></th>
		<td>
			<input type="text" name="google_client_secret" id="google_client_secret" value="<?php echo userpro_get_option('google_client_secret'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="google_redirect_uri"><?php _e('Callback / Redirect URL','userpro'); ?></label></th>
		<td>
			<input type="text" name="google_redirect_uri" id="google_redirect_uri" value="<?php echo userpro_get_option('google_redirect_uri'); ?>" class="regular-text" />
            <span class="up-description"><?php _e('This URL must be in the allowed Redirect URIs for this app in your Google Console. example : ','userpro'); ?> <?php echo get_site_url().'/wp-content/plugins/userpro/lib/google-auth/auth.php'  ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="google_signup_redirect"><?php _e('Custom Redirect URL After Registration','userpro'); ?></label></th>
		<td>
			<input type="text" name="google_signup_redirect" id="google_signup_redirect" value="<?php echo userpro_get_option('google_signup_redirect'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="google_signin_redirect"><?php _e('Custom Redirect URL After Login','userpro'); ?></label></th>
		<td>
			<input type="text" name="google_signin_redirect" id="google_signin_redirect" value="<?php echo userpro_get_option('google_signin_redirect'); ?>" class="regular-text" />
		</td>
	</tr>
	
</table>

<h3><i class="fas fa-cog"></i><?php _e('BuddyPress Compatibility','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="buddypress_userpro_link_sync"><?php _e('Sync UserPro profile links with BuddyPress','userpro'); ?></label></th>
		<td>
			<select name="buddypress_userpro_link_sync" id="buddypress_userpro_link_sync" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('buddypress_userpro_link_sync')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('buddypress_userpro_link_sync')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="buddypress_userpro_avatar_sync"><?php _e('Sync UserPro avatars with BuddyPress','userpro'); ?></label></th>
		<td>
			<select name="buddypress_userpro_avatar_sync" id="buddypress_userpro_avatar_sync" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('buddypress_userpro_avatar_sync')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('buddypress_userpro_avatar_sync')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="buddypress_userpro_displayname_sync"><?php _e('Sync UserPro display name with BuddyPress','userpro'); ?></label></th>
		<td>
			<select name="buddypress_userpro_displayname_sync" id="buddypress_userpro_displayname_sync" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('buddypress_userpro_displayname_sync')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('buddypress_userpro_displayname_sync')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
</table>

<h3><i class="fas fa-cog"></i><?php _e('bbpress Compatibility','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="bbpress_userpro_link_sync"><?php _e('Sync UserPro profile links with bbpress','userpro'); ?></label></th>
		<td>
			<select name="bbpress_userpro_link_sync" id="bbpress_userpro_link_sync" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('bbpress_userpro_link_sync')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('bbpress_userpro_link_sync')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
</table>

<h3><i class="fas fa-cog"></i><?php _e('Envato Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="envato_api"><?php _e('Envato API Key','userpro'); ?></label></th>
		<td>
			<input type="text" name="envato_api" id="envato_api" value="<?php echo userpro_get_option('envato_api'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="envato_username"><?php _e('Envato Username','userpro'); ?></label></th>
		<td>
			<input type="text" name="envato_username" id="envato_username" value="<?php echo userpro_get_option('envato_username'); ?>" class="regular-text" />
		</td>
	</tr>

</table>

<!--Globla hook for adding extra setting fields   Added by Rahul-->
<?php do_action("userpro_add_setting_fields");?>
<p class="submit">
	<input type="submit" name="submit" id="submit" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Save Changes','userpro'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="up-admin-btn small remove" value="<?php _e('Reset Options','userpro'); ?>"  />
</p>

</form>
