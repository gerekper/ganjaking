<?php
$field = 1;
$title = $_GET["title"];
$output = userpro_mu_getfields_edit($title);
echo $output;?>
<div id="results-wrap"></div>
<p>
<input type="button" class="userpro_mu_edit button button-primary" id="save_field" value="Save Changes" data-edit="<?php _e('Done! Form edited successfully','userpro'); ?>" onclick="save_edit_form('<?php echo $title; ?>',this)"/>
<img src="<?php echo userpro_url . 'admin/images/loading.gif'; ?>" alt="" class="upadmin-load-inline" />
</p>
