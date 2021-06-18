<form method="post" action="">
<p class="upadmin-highlight"><?php _e('The variables in {CURLY BRACKETS} are used to present data and info in email. You can use them to customize your email template.','userpro'); 
$res='';
$array = array(
		'{USERPRO_ADMIN_EMAIL}' => __('Displays the admin email that users can contact you at. You can configure it under Mail settings.','userpro'),
		'{USERPRO_BLOGNAME}' => __('Displays blog name','userpro'),
		'{USERPRO_BLOG_URL}' => __('Displays blog URL','userpro'),
		'{USERPRO_BLOG_ADMIN}' => __('Displays blog WP-admin URL','userpro'),
		'{USERPRO_TO_USERNAME}' => __('Displays the Username of receiver','userpro'),
		'{USERPRO_TO_FIRST_NAME}' => __('Displays the first name of receiver','userpro'),
		'{USERPRO_TO_LAST_NAME}' => __('Displays the last name of receiver','userpro'),
		'{USERPRO_TO_NAME}' => __('Displays the display name or public name of receiver','userpro'),
		'{USERPRO_TO_EMAIL}' => __('Displays the E-mail address of receiver','userpro'),
		'{USERPRO_TO_PROFILE_LINK}' => __('Displays the Profile address of receiver','userpro'),
		'{USERPRO_FROM_USERNAME}' => __('Displays the Username of sender','userpro'),
		'{USERPRO_FROM_FIRST_NAME}' => __('Displays the first name of sender','userpro'),
		'{USERPRO_FROM_LAST_NAME}' => __('Displays the last name of sender','userpro'),
		'{USERPRO_FROM_NAME}' => __('Displays the display name or public name of sender','userpro'),
		'{USERPRO_FROM_EMAIL}' => __('Displays the E-mail address of sender','userpro'),
		'{USERPRO_FROM_PROFILE_LINK}' => __('Displays the Profile address of sender','userpro'),
		);
foreach($array as $key => $val) {
	$res .= '<br /><code>'.$key.'</code> '. $val;
}

echo $res;
 ?></p>
<h3><?php _e('Email Notification Settings','userpro-userwall'); ?></h3>
<table class="form-table">
<tr valign="top">
		<th scope="row"><label for="send_email_on_comment"><?php _e('Send email notification when user comments on a post','userpro-userwall'); ?></label></th>
		<td>
			<select name="send_email_on_comment" id="send_email_on_comment" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_userwall_get_option('send_email_on_comment')); ?>><?php _e('Yes','userpro-userwall'); ?></option>
				<option value="0" <?php selected(0, userpro_userwall_get_option('send_email_on_comment')); ?>><?php _e('No','userpro-userwall'); ?></option>
			</select>
		</td>
</tr>
<tr valign="top">
		<th scope="row"><label for="send_email_on_post_likedis"><?php _e('Send email notification to author when user like/dislike on a post','userpro-userwall'); ?></label></th>
		<td>
			<select name="send_email_on_post_likedis" id="send_email_on_post_likedis" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_userwall_get_option('send_email_on_post_likedis')); ?>><?php _e('Yes','userpro-userwall'); ?></option>
				<option value="0" <?php selected(0, userpro_userwall_get_option('send_email_on_post_likedis')); ?>><?php _e('No','userpro-userwall'); ?></option>
			</select>
		</td>
</tr>
<tr valign="top">
		<th scope="row"><label for="send_email_on_comment_likedis"><?php _e('Send email notification to author when user like/dislike on a comment','userpro-userwall'); ?></label></th>
		<td>
			<select name="send_email_on_comment_likedis" id="send_email_on_comment_likedis" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_userwall_get_option('send_email_on_comment_likedis')); ?>><?php _e('Yes','userpro-userwall'); ?></option>
				<option value="0" <?php selected(0, userpro_userwall_get_option('send_email_on_comment_likedis')); ?>><?php _e('No','userpro-userwall'); ?></option>
			</select>
		</td>
</tr>

</table>
<h3><?php _e('Notifiaction email when some one comment on post','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="mail_user_on_comment_s"><?php _e('Subject','userpro'); ?></label></th>
		<td><input type="text" name="mail_user_on_comment_s" id="mail_user_on_comment_s" value="<?php echo userpro_userwall_get_option('mail_user_on_comment_s'); ?>" class="regular-text" /></td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="mail_user_on_comment"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_user_on_comment" id="mail_user_on_comment" class="large-text code" rows="10"><?php echo userpro_userwall_get_option('mail_user_on_comment'); ?></textarea></td>
	</tr>
	
</table>
<h3><?php _e('Notifiaction email to author when some one like/dislike on post','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="mail_user_on_likedis_post_s"><?php _e('Subject','userpro'); ?></label></th>
		<td><input type="text" name="mail_user_on_likedis_post_s" id="mail_user_on_likedis_post_s" value="<?php echo userpro_userwall_get_option('mail_user_on_likedis_post_s'); ?>" class="regular-text" /></td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="mail_user_on_likedis_post"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_user_on_likedis_post" id="mail_user_on_likedis_post" class="large-text code" rows="10"><?php echo userpro_userwall_get_option('mail_user_on_likedis_post'); ?></textarea></td>
	</tr>
	
</table>
<h3><?php _e('Notifiaction email to author when some one like/dislike on comment','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="mail_user_on_likedis_comment_s"><?php _e('Subject','userpro'); ?></label></th>
		<td><input type="text" name="mail_user_on_likedis_comment_s" id="mail_user_on_likedis_comment_s" value="<?php echo userpro_userwall_get_option('mail_user_on_likedis_comment_s'); ?>" class="regular-text" /></td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="mail_user_on_likedis_comment"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_user_on_likedis_comment" id="mail_user_on_likedis_comment" class="large-text code" rows="10"><?php echo userpro_userwall_get_option('mail_user_on_likedis_comment'); ?></textarea></td>
	</tr>
	
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro'); ?>"  />
</p>

</form>
