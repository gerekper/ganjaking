<?php 
$custom_widget_content = stripslashes($widget_content['content']);
?>
<tr id = "<?php echo $widget_id; ?>" valign="top">
	<td style="padding-left:27px" class= "widget-title" id = "<?php echo $widget_id; ?>"> 
		<label class="display-widget-title" for="<?php echo $widget_id; ?>"><?php echo $widget_content['title']; ?></label> 	
		<input style="display:none;" class="widget-edit" type = "text" name = "widget-id-hidden" value="<?php echo $widget_content['title']; ?>"/>
	</td> 
	<td class = "widget-content" id = "<?php echo $widget_id; ?>">     
		<textarea style="width:90%;height:100px" class="display-widget-content" name = "display-widget-content" disabled><?php echo $custom_widget_content; ?></textarea>
		<textarea style="display:none;width:90%;height:100px" class="widget-edit" name = "widget-content-edit"><?php echo $custom_widget_content; ?></textarea>
	</td>
	<td class = "widget-action">
		<div class="widget_edit_btn" style="display:inline-block;" id= "edit_<?php echo $widget_id;?>"></div>
		<div class="widget_save_btn" style="display:none;" id= "save_<?php echo $widget_id;?>"></div>
		<div class="widget_delete_btn" style="display:inline-block;" id= "delete_<?php echo $widget_id;?>"></div>
	</td>
</tr>