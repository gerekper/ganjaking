<?php
	if( !isset( $updb_default_options ) ){
		$updb_default_options = new UPDBDefaultOptions();
	}
?>
<form method="post" action="">
	
	<h3><?php _e('Custom Widget Settings','userpro-dashboard'); ?></h3>
	<table class="form-table">
	
		<tr valign="top">
			<th scope="row"><label for="custom_widget_section"><?php _e('Enable Custom Widgets','userpro-dashboard'); ?></label></th>
			<td>
				<select name="custom_widget_section" id="custom_widget_section" class="chosen-select" style="width:300px">
					<option value="1" <?php selected(1, $updb_default_options->updb_get_option('custom_widget_section')); ?>><?php _e('Yes','userpro-dashboard'); ?></option>
					<option value="0" <?php selected(0, $updb_default_options->updb_get_option('custom_widget_section')); ?>><?php _e('No','userpro-dashboard'); ?></option>
				</select>
			</td>
		</tr>
	<?php if($updb_default_options->updb_get_option('custom_widget_section') == 1){
	
				$updb_custom_widgets = get_option('updb_custom_widgets');
				if(!empty($updb_custom_widgets)){
	?>
	<tr valign="top">
		<td><label for="custom_widget_title" style="font-weight: bolder;padding-left:15px"><?php _e('Widget Title','userpro-dashboard'); ?></label></td>
		<td><label for="custom_widget_content" style="font-weight: bolder"><?php _e('Widget Content','userpro-dashboard'); ?></label></td>
		<td><label for="custom_widget_action" style="font-weight: bolder"><?php _e('Widget Action','userpro-dashboard'); ?></label></td>
	</tr>
	<?php 
					foreach($updb_custom_widgets as $k => $v){
						$widget_id= $k;
						$widget_content=$v;
						include UPDB_PATH . 'admin/templates/display-custom-widgets.php';
			
					}
				}
	?>
	<tr id="add-widget-tr" valign="top">
		<td>
			<input type="button" class="button" style="" name="userpro-add-widget" value="<?php _e('Add New Widget','userpro-dashboard'); ?>" id="userpro-add-widget" />	
		</td>
	</tr>
	<?php }?>
	</table>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-dashboard'); ?>"  />
		<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-dashboard'); ?>"  />
	</p>
</form>