
<?php

$page_id = '';
if(!empty($_REQUEST['page_id'])){
    $page_id = $_REQUEST['page_id'];
}


// Get form settings
$settings_name = 'seed_cspv5_'.$page_id.'_form';
$settings = get_option($settings_name);
if(!empty($settings)){
    $settings = maybe_unserialize($settings);
}

// Get name field settings
// Get Page
global $wpdb;
$tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
$sql = "SELECT * FROM $tablename WHERE id= %d";
$safe_sql = $wpdb->prepare($sql,$page_id);
$page = $wpdb->get_row($safe_sql);

// Check for base64 encoding of settings
if ( base64_encode(base64_decode($page->settings, true)) === $page->settings){
    $page_settings = unserialize(base64_decode($page->settings));
} else {
    $page_settings = unserialize($page->settings);
}

$display_name = 0;
if(!empty($page_settings['display_name'])){
    $display_name = 1;
}

$require_name = 0;
if(!empty($page_settings['require_name'])){
    $require_name = 1;

}

$custom_field_count = 0;
$highest = 0;




?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<style>
.seed-cspv5 .ui-state-highlight { 
  height: auto; 
  background:#FEF6DD;
  border:1px dotted #F9D975 ;
}

.seed-cspv5 .dashicons-trash{
    cursor:pointer;
}

.seed-cspv5 .dashicons-move{
    cursor:move;
}

</style>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/jquery.repeatable.js"></script>
<div class="wrap columns-2 seed-cspv5">

<?php include(SEED_CSPV5_PLUGIN_PATH.'admin/header.php') ?>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">

<div id="post-body-content" >
<form id="seed_cspv5_form_builder">
<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name ?>"/>
<div id="seed_cspv5_section_general" class="postbox seedprod-postbox">
<h3 class="hndle">From Builder</h3>
<div class="inside">
<table style="width:100%" class="form_builder_repeatable-container">
<tbody>
<tr valign="top">
<td><strong>Field Label</strong></td>
<td style="display:none"><strong>Field Name</strong></td>
<td><strong>Type</strong></td>
<td><strong>Visible</strong></td>
<td><strong>Required</strong></td>
<td><strong>Actions</strong></td>
</tr>
<tr><td colspan="6"><hr/></td></tr>
<?php 
if(!empty($settings)){
    foreach($settings as $k => $v){ 
        if(is_array($v)){

            ?>
            <tr <?php echo ($k == 'field_email') ? 'id="seed_cspv5_email_tr"': ''; ?> valign="top" class="field-group <?php echo ($k != 'field_email') ? 'seed_cspv5_sortable': ''; ?>">
                <td>
                <input name="<?php echo $k ?>[type]" type="hidden" value="<?php echo $v['type'] ?>"></input>
                <input name="<?php echo $k ?>[label]" type="text" value="<?php echo $v['label'] ?>"></input>
                </td>
                <td style="display:none">><input name="<?php echo $k ?>[name]" type="text" value="<?php echo $v['name'] ?>" <?php echo ($k == 'field_email' || $k == 'field_name') ? 'readonly': ''; ?>></input></td>
                <td>Textbox</td>
                <?php if($k == 'field_name'){ ?>
                <td><input name="<?php echo $k ?>[visible]" type="checkbox" <?php echo (!empty($display_name)) ? 'checked': ''; ?>></input></td>
                <td><input name="<?php echo $k ?>[required]" type="checkbox" <?php echo (!empty($require_name)) ? 'checked': ''; ?>></input></td>
                <td><span class="dashicons dashicons-move" title="Move"></span></td>
                <?php }elseif($k == 'field_email'){ ?>
                    <td><input name="<?php echo $k ?>[visible]" type="hidden" checked></input><i class="fa fa-check" aria-hidden="true"></i></td>
                    <td><input name="<?php echo $k ?>[required]" type="hidden" checked></input><i class="fa fa-check" aria-hidden="true"></i></td>
                    <td></td>
                <?php }else{ ?>
                    <td><input name="<?php echo $k ?>[visible]" type="checkbox" <?php echo (!empty($v['visible'])) ? 'checked': ''; ?>></input></td>
                    <td><input name="<?php echo $k ?>[required]" type="checkbox" <?php echo (!empty($v['required'])) ? 'checked': ''; ?>></input></td>
                    <td>
                    <span class="dashicons dashicons-move" title="Move"></span> <span class="dashicons dashicons-trash seed_cspv5_delete" title="Delete"></span>
                    </td>
                <?php } ?>
                
            </tr>
            <?php
            if($k != 'field_email'){
                if($k != 'field_name'){
                $current_count = substr($k, 6);
                if ($current_count >= $highest){
                    $highest = $current_count;
                    $custom_field_count = substr($k, 6) + 1;
                }
                
            }}
        }
    } 
}else{
?>
<tr valign="top" class="seed_cspv5_sortable">
<td>
<input name="field_name[type]" type="hidden" value="textbox"></input>
<input name="field_name[label]" type="text" value="<?php echo (!empty($settings['field_name']['label'])) ? $settings['field_name']['label'] : 'Name' ?>"></input>
</td>
<td style="display:none"><input name="field_name[name]" type="text" value="name" readonly></input></td>
<td>Name</td>
<td><input name="field_name[visible]" type="checkbox" <?php echo (!empty($display_name)) ? 'checked' : '' ?>></input></td>
<td><input name="field_name[required]" type="checkbox" <?php echo (!empty($require_name)) ? 'checked' : '' ?>></input></td>
<td><span class="dashicons dashicons-move" title="Move"></span></td>
</tr>
<tr id="seed_cspv5_email_tr" valign="top">
<td>
<input name="field_email[type]" type="hidden" value="textbox"></input>
<input name="field_email[label]" type="text" value="<?php echo (!empty($settings['field_email']['label'])) ? $settings['field_email']['label'] : 'Email' ?>"></input>
</td>
<td style="display:none"><input name="field_email[name]" type="text" value="email" readonly></input></td>
<td>Email</td>
<td><input name="field_email[visible]" type="hidden" checked ></input><i class="fa fa-check" aria-hidden="true"></i></td>
<td><input name="field_email[required]" type="hidden" checked ></input><i class="fa fa-check" aria-hidden="true"></i></td>
<td></td>
</tr>
<?php } ?>
</tbody>
</table>   
            
            </div></div>
            <input id="seed_cspv5_save_form" name="submit" type="submit" value="Save All Changes" class="button-primary"><br><br>
<button id="seed_cspv5_cancel-btn" class="button-secondary">Go Back to Page Customizer</button>
</form> 
                
                
</div><!-- #post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">

                             <div class="postbox ">
                                <div class="handlediv" title="Click to toggle"><br /></div>
                                <h3 class="hndle"><span><i class="fa fa-rocket"></i>&nbsp;&nbsp;<?php _e('Form Fields', 'seedprod-coming-soon-pro-5') ?></span></h3>
                                <div class="inside">
                                    <p>Click on a Field to Add it to the Form.</p>
                                   <button id="textbox-btn" class="seed_cspv5_add button-secondary">Textbox</button> 
                                </div>
                            </div>
                        </div>
                    </div><!-- #postbox-container-1 -->



</div>
</div>
</div>

<script type="text/template" id="seed_cspv5_field_textbox">
    <tr valign="top" class="field-group seed_cspv5_sortable">
        <td>
        <input name="field_{?}[type]" type="hidden" value="textbox"></input>
        <input name="field_{?}[label]" type="text" value="Untitled"></input>
        </td>
        <td style="display:none"><input name="field_{?}[name]" type="text" value="field_{?}" readonly></input></td>
        <td>Textbox</td>
        <td><input name="field_{?}[visible]" type="checkbox" checked></input></td>
        <td><input name="field_{?}[required]" type="checkbox" checked></input></td>
        <td><span class="dashicons dashicons-move" title="Move"></span> <span class="dashicons dashicons-trash seed_cspv5_delete" title="Delete"></span></td>
    </tr>
</script>

<script>

<?php $save_form_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_save_form','seed_cspv5_save_form')); ?>
var save_form_url = "<?php echo $save_form_ajax_url; ?>";

var start = <?php echo $custom_field_count; ?>;
<?php $return_url = (isset($_GET['return']))? preg_replace("/#.*|#.*&|&tab=design/", "", $_GET['return']).'&tab=form#header-form-settings' : ''; ?>
var return_url = '<?php echo urldecode($return_url); ?>';
<?php $page_id = $page_id; ?>
var page_id = '<?php echo $page_id; ?>';

jQuery( "#seed_cspv5_cancel-btn" ).click(function(e) {
	e.preventDefault();
	if(return_url != ''){
		window.location.href = return_url;
	}
	
});

jQuery( document ).ready(function() {
    jQuery(function($) {
        $(".form_builder_repeatable-container").repeatable({
            addTrigger: ".seed_cspv5_add",
            deleteTrigger: ".seed_cspv5_delete",
            template: "#seed_cspv5_field_textbox",
            startWith: start,
            onDelete: function () {$(this).parent().parent().remove();},
            onAdd: function () { $("#seed_cspv5_email_tr").before($(".form_builder_repeatable-container tr:last"))},
        });
    });

    jQuery( ".form_builder_repeatable-container tbody" ).sortable({
      placeholder: "ui-state-highlight",
      helper: fixWidthHelper,
      items: "> .seed_cspv5_sortable",
      cursor: "move",
      start: function(e, ui){
        ui.placeholder.height(ui.item.height());
    }
    });
});

function fixWidthHelper(e, ui) {
    ui.children().each(function() {
        jQuery(this).width(jQuery(this).width());
    });
    return ui;
}

// Save Form
jQuery('#seed_cspv5_save_form').on('click',function(e){
    e.preventDefault();
    jQuery(this).prop( "disabled", true );
    var data = jQuery( "#seed_cspv5_form_builder" ).serialize();


    var jqxhr = jQuery.post( save_form_url+'&page_id='+page_id,data, function(data) {
        jQuery("#seed_cspv5_save_form").prop( "disabled", false );
        location.href= location.href+'&updated=true';
        //console.log(data);
        })
        

});

</script>