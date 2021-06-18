<form method="post" action="" class="userpro_new_redirect">

<p class="upadmin-highlight"><?php _e('The top rules always have higher priority. So any new rule you add will be higher in priority and checked first in the redirection rules during login or registering (depending on where you set the redirection). You can setup a rule for a specific role, or a specific user, or perhaps redirect all users who choose United States as country to be redirected to a custom page. This addon also supports automatic mapping. Please check the help tab for examples and help.','userpro'); ?></p>

<h3><?php _e('Add New Redirection Rule','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<td>
			<select name="rd_role" id="rd_role" class="chosen-select" style="width:200px">
				<option value=""><?php _e('&mdash; For a specific role &mdash;','userpro'); ?></option>
				<?php echo userpro_rd_get_roles(); ?>
			</select>
			
			<select name="rd_user" id="rd_user" class="chosen-select" style="width:200px">
				<option value=""><?php _e('&mdash; For a user / all users &mdash;','userpro'); ?></option>
				<?php echo userpro_rd_get_users(); ?>
			</select>
			
			<select name="rd_field" id="rd_field" class="chosen-select" style="width:200px">
				<option value=""><?php _e('&mdash; For a custom field &mdash;','userpro'); ?></option>
				<?php echo userpro_rd_get_fields(); ?>
			</select>
			
			<p>
			<span class="up-description"><?php _e('If you want redirection based on specific <strong>custom field value</strong> enter the value here: (e.g. country or city) This is helpful if you wish to redirect users who have a specific city in profile, be redirected to a custom page after their login. If you wish to <strong>auto-map</strong> or redirect users automatically based on the custom field value you could use wildcards like this: <strong>{your_custom_field}</strong> in the redirection URL. The wildcard will also work on {username}','userpro'); ?></span>
			<input type="text" name="rd_field_value" id="rd_field_value" value="" class="regular-text" placeholder="<?php _e('Enter field value','userpro'); ?>"/>
			</p>
			
			<p><label for="rd_url"><strong><?php _e('Redirection URL','userpro'); ?></strong></label><br />
			<span class="up-description"><?php _e('You can use wildcards for automated mapping. For example, if you enter {username} that will be replaced automatically by the username in redirection url. If you want to map a custom field <strong>country</strong> then use the wildcart {country} to map the user to his country automatically. If you use wildcards, the field value is not required.','userpro'); ?></span>
			<input type="text" name="rd_url" id="rd_url" value="" class="regular-text" placeholder="<?php _e('Enter redirection URL here.','userpro'); ?>"/></p>
			
			<input type="submit" class="button button-primary" value="<?php _e('Submit New Rule','userpro'); ?>"  />
			
			<img src="<?php echo userpro_url; ?>admin/images/loading.gif" alt="" class="upadmin-load-inline" />
			
			<input type="hidden" name="type" id="type" value="login" />
			
			<div class="upadmin-errors">
			
			</div>
			
		</td>
	</tr>
	
</table>

<h3><?php _e('Existing Login Redirects','userpro'); ?></h3>

<div id="login_redirects" class="upadmin-redirects">
	<table class="wp-list-table widefat fixed">
		<?php echo userpro_rd_list_redirects('login'); ?>
	</table>
</div>

</form>