<form method="post" action="">

<h3><i class="userpro-icon-wrench"></i><?php _e('Import / Export (Settings only)','userpro'); ?></h3>

<table class="form-table">

	<tr valign="top">
		<th scope="row"><label><?php _e('Export Settings','userpro'); ?></label></th>
		<td>
			<?php $this->create_export_download_link(true, 'userpro_export_options'); ?>
			<span class="up-description up-description--inline"><?php _e('Download the file of userpro settings','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="userpro_import"><?php _e('Import Settings','userpro'); ?></label></th>
		<td>
			<textarea name="userpro_import" id="userpro_import" class="large-text" rows="10"></textarea>
			<p><input type="submit" name="import_settings" id="import_settings" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Import','userpro'); ?>" /></p>
			<span class="up-description"><?php _e('Just copy the export file data in to above text box and click on the import button','userpro'); ?></span>
		</td>
	</tr>

</table>

<h3><i class="userpro-icon-wrench"></i><?php _e('Import / Export Fields','userpro'); ?></h3>

<table class="form-table">

	<tr valign="top">
		<th scope="row"><label><?php _e('Export Fields','userpro'); ?></label></th>
		<td>
			<?php $this->create_export_download_link(true, 'userpro_export_fields'); ?>
			<span class="up-description up-description--inline "><?php _e('Download the file of userpro fields','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="userpro_import_fields"><?php _e('Import Fields','userpro'); ?></label></th>
		<td>
			<textarea name="userpro_import_fields" id="userpro_import_fields" class="large-text" rows="10"></textarea>
			<p><input type="submit" name="import_fields" id="import_fields" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Import','userpro'); ?>" /></p><span class="up-description"><?php _e('Just copy the export file data in to above text box and click on the import button','userpro'); ?></span>
		</td>
	</tr>

</table>

<h3><i class="userpro-icon-wrench"></i><?php _e('Import / Export Field Groups','userpro'); ?></h3>

<table class="form-table">

	<tr valign="top">
		<th scope="row"><label><?php _e('Export Field Groups','userpro'); ?></label></th>
		<td>
			<?php $this->create_export_download_link(true, 'userpro_export_groups'); ?>
			<span class="up-description up-description--inline"><?php _e('Download the file of userpro Field Groups','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="userpro_import_groups"><?php _e('Import Field Groups','userpro'); ?></label></th>
		<td>
			<textarea name="userpro_import_groups" id="userpro_import_groups" class="large-text" rows="10"></textarea>
			<p><input type="submit" name="import_groups" id="import_groups" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Import','userpro'); ?>" /></p><span class="up-description"><?php _e('Just copy the export file data in to above text box and click on the import button','userpro'); ?></span>
		</td>
	</tr>

</table>

</form>