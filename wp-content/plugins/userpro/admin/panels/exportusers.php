<form method="post" action="" enctype="multipart/form-data">

<h3><?php _e('Export Users to CSV','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="exp_users_num"><?php _e('Number of Users to export','userpro'); ?></label></th>
		<td>
			<input type="text" name="exp_users_num" id="exp_users_num" value="100" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th ><label for="formdate"><?php _e('From Date','userpro'); ?></label></th>
		<td>
			<input type="date" data-fieldtype='datepicker' class='userpro-datepicker' name="formdate" id="formdate" size="35" />
		</td>
	</tr>
	<tr>
		<th ><label for="todate"><?php _e('To Date','userpro'); ?></label></th>
		<td>
			<input type="date" data-fieldtype='datepicker' class='userpro-datepicker' name="todate" id="todate"  size="35"/>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="exp_exclude"><?php _e('Do not include these fields in export','userpro'); ?></label></th>
		<td>
			<input type="text" name="exp_exclude" id="exp_exclude" value="" class="regular-text" />
			<span class="up-description"><?php _e('A comma seperated list of fields to exclude from the export. e.g. first_name,user_login,id,user_email','userpro'); ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="exp_include"><?php _e('Only Include these fields in export','userpro'); ?></label></th>
		<td>
			<input type="text" name="exp_include" id="exp_include" value="" class="regular-text" />
			<span class="up-description"><?php _e('A comma seperated list of fields to include only in the export. e.g. first_name,user_login,id,user_email','userpro'); ?></span>
		</td>
	</tr>
	
</table>

<p class="submit submit-static">
	<input type="submit" name="export_users" id="export_users" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Export to CSV','userpro'); ?>"  />
</p>
<h3><?php _e('Import Users from CSV','userpro'); ?></h3>
<table class="form-table">
<tr valign="top">
		<th scope="row"><label for="import_users_file"><?php _e('Upload users CSV file to import','userpro'); ?></label></th>
		<td>
			<input type="file" name="import_users_file" id="import_users_file" class="regular-text" />
		</td>
	</tr>
<tr valign="top">
		<th scope="row"><label for="send_email_notification"><?php _e('Send email notification to users on registration','userpro'); ?></label></th>
		<td>
			<select name="send_email_notification" id="send_email_notification" class="chosen-select" style="width:300px">
				<option value="1" ><?php _e('Yes','userpro'); ?></option>
				<option value="0" ><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>	
</table>
<p class="submit submit-static">
	<input type="submit" name="import_users" id="import_users" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Import from CSV','userpro'); ?>"  />
</p>

</form>
