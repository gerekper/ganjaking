<!-- the groups container -->
<div class="upadmin-groups upadmin">
	
	<!-- legend info -->
	<div class="upadmin-section">
		<h4><?php _e('How to edit the field settings?','userpro'); ?></h4>
		<p><?php _e('This plugin uses small icons for quick ajax editing for a field. Each icon means something, this little guide will show you how to control each field quickly and instantly.','userpro'); ?></p>
		<p><?php _e('A transparent icon means the setting is turned off. A full-color icon means the setting is turned on. Click on any icon to switch the state of it on and off.','userpro'); ?></p>
		<div class="upadmin-legend">
		
			<div class="upadmin-field-action-remove"><?php _e('This removes the field from a selected group','userpro'); ?></div>
			<div class="upadmin-field-action-edit"><?php _e('Click that icon to edit field label and help text or other misc options','userpro'); ?></div>
			<div class="upadmin-field-action-hideable"><?php _e('Make a field hideable from public by user','userpro'); ?></div>
			<div class="upadmin-field-action-hidden"><?php _e('Make a field hidden from public by default if field is hideable','userpro'); ?></div>
			<div class="upadmin-field-action-locked"><?php _e('Lock a field so that user cannot edit, but admins can still edit all fields','userpro'); ?></div>
			<div class="upadmin-field-action-private"><?php _e('Make a field private so that users cannot view or edit it, but admins will be able to view it','userpro'); ?></div>
			<div class="upadmin-field-action-required"><?php _e('Make a field required','userpro'); ?></div>
			<div class="upadmin-field-action-html"><?php _e('Allow HTML in a field. e.g. If the field is textarea','userpro'); ?></div>
			
			<div class="clear"></div>
			
		</div>
		<div class="clear"></div>
	</div>
	
	<form action="" method="post">
		<br />
		<?php _e('Before resetting, please remove all UserPro pages from your Pages.','userpro'); ?><br />
		<input type="submit" name="userpro-reinstall" id="userpro-reinstall" value="<?php _e('Re-Install UserPro (Warning: This will reset all fields and settings)','userpro'); ?>" class="button-primary" />
	</form>
	
	<!-- loading spinner -->
	<div class="upadmin-loader"></div>
	
	<h2><span class="upadmin-ajax-groups"><?php _e('Groups & Fields','userpro'); ?></span><a href="#" class="button upadmin-reset-groups"><?php _e('Reset All Groups','userpro'); ?></a></h2>
	
	<!-- customize groups parser -->
	<div class="upadmin-groups-view"><?php echo userpro_admin_list_groups(); ?></div>
	
</div>

<!-- the fields container -->
<div class="upadmin-fieldlist upadmin">

	<!-- quick info -->
	<div class="upadmin-section">
		<h4><?php _e('Drag & Drop your fields into Forms','userpro'); ?></h4>
		<p><?php _e('Each field presents a bit of data or a meta key about the user. This allow you to drag any field to any form you want but be careful, not every field is suitable for any form. e.g. It makes no sense to add Country to your login shortcode.','userpro'); ?></p>
	</div>
	
	<!-- add new field -->
	<div class="upadmin-section">
		<h4><?php _e('Add New Field / Add or Sync Existing Data','userpro'); ?></h4>
		<p><?php _e('You can add any field to your forms such as City, Address, or anything else by clicking on <strong>Add New Field</strong> The plugin will automatically sync/recognize any meta key stored in your user database, so you could display the value of any existing meta data.','userpro'); ?></p>
		<p><a href="#" class="button-primary upadmin-toggle-new"><?php _e('Add New Field','userpro'); ?></a></p>
		
		<!-- section to add field -->
		<div class="upadmin-new">
			<i class="userpro-icon-caret-up"></i>
			<form action="" method="post">
			<table class="form-table up-add-new-field">
			
				<tr valign="top">
					<th scope="row"><label for="upadmin_n_title"><?php _e('Field Title','userpro'); ?></label></th>
					<td><input name="upadmin_n_title" type="text" id="upadmin_n_title" value="" class="regular-text" />
					<span class="up-description"><?php _e('Enter title for this data. example: City, How did you hear about us, Twitter, Personal Website, etc.','userpro'); ?></span></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="upadmin_n_type"><?php _e('Field Type','userpro'); ?></label></th>
					<td><select name="upadmin_n_type" id="upadmin_n_type" class="chosen-select" style="width:300px"><?php userpro_admin_field_types() ?></select>
					<span class="up-description"><?php _e('The type of field or how a user is going to enter this data (via text, selecting a choice from dropdown, etc)','userpro'); ?></span></td>
				</tr>
				
				<tr valign="top" class="filetypes">
					<th scope="row"><label for="upadmin_n_filetypes"><?php _e('Allowed file extensions','userpro'); ?></label></th>
					<td><input name="upadmin_n_filetypes" type="text" id="upadmin_n_filetypes" value="" class="regular-text" />
					<span class="up-description"><?php _e('A comma seperated list of allowed file types. If blank, default will be to zip, pdf, txt.','userpro'); ?></span></td>
				</tr>
				
				<tr valign="top" class="choicebased">
					<th scope="row"><label for="upadmin_n_choices_builtin"><?php _e('Builtin Choice List','userpro'); ?></label></th>
					<td><select name="upadmin_n_choices_builtin" id="upadmin_n_choices_builtin">
						<option value="">&mdash;</option>
						<option value="country"><?php _e('Countries List','userpro'); ?></option>
						<option value="roles"><?php _e('Roles','userpro'); ?></option>
					</select><span class="up-description"><?php _e('You can also enter choices for this field manually below.','userpro'); ?></span></td>
				</tr>
				
				<tr valign="top" class="choicebased">
					<th scope="row"><label for="upadmin_n_choices"><?php _e('Manual Choices / Options','userpro'); ?></label></th>
					<td><textarea name="upadmin_n_choices" id="upadmin_n_choices"></textarea>
					<span class="up-description"><?php _e('Please enter one choice per line!','userpro'); ?></span></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="upadmin_n_help"><?php _e('Help Text','userpro'); ?></label></th>
					<td><input name="upadmin_n_help" type="text" id="upadmin_n_help" value="" class="regular-text" />
					<span class="up-description"><?php _e('Help text will be displayed below field to guide users what to put in that field or show any relevant info.','userpro'); ?></span></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="upadmin_n_ph"><?php _e('Custom Placeholder','userpro'); ?></label></th>
					<td><input name="upadmin_n_ph" type="text" id="upadmin_n_ph" value="" class="regular-text" /></td>
				</tr>
				

				<tr valign="top" style="background:#999">
					<th scope="row" colspan="2"><?php _e('Setting up the user meta key','userpro'); ?></th>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="upadmin_n_key"><?php _e('Unique Field Key','userpro'); ?></label></th>
					<td><input name="upadmin_n_key" type="text" id="upadmin_n_key" placeholder="e.g. my_address, phone_number" class="regular-text" value="" />
					<span class="up-description"><?php _e('Your field key must be unique for each field. It must be letters only with only underscore allowd between them.','userpro'); ?></span></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="upadmin_n_sync"><?php _e('Or select/sync an existing meta field key','userpro'); ?></label></th>
					<td><select name="upadmin_n_sync" id="upadmin_n_sync" class="chosen-select" style="width:300px"><?php userpro_admin_usermeta() ?></select>
					<span class="up-description"><?php _e('You can sync or use user meta from any other plugin directly and integrate it with UserPro. Unique key is not needed If you sync with existing field.','userpro'); ?></span></td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<td>
						<input type="submit" value="<?php _e('Publish','userpro'); ?>" name="upadmin_n_new" id="upadmin_n_new" class="button-primary" />
						<input type="button" value="<?php _e('Cancel','userpro'); ?>" name="upadmin_n_cancel" id="upadmin_n_cancel" class="button" />
					</td>
				</tr>
			</table>
			</form>
		</div>
		
	</div>
	
	<!-- loading spinner -->
	<div class="upadmin-loader"></div>

	<!-- sortable fields -->
	<h2><span class="upadmin-ajax-fieldcount"><?php echo userpro_admin_count_fields(); ?></span><a href="#" class="button upadmin-reset-fields"><?php _e('Restore Default Fields','userpro'); ?></a></h2>
	<ul id="upadmin-newsection"><?php echo userpro_admin_new_section(); ?></ul>
	<ul id="upadmin-sortable-fields"><?php echo userpro_admin_list_fields() ?></ul>
	
</div>