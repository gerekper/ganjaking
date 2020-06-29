<?php

$page_id = '';
if(!empty($_REQUEST['page_id'])){
    $page_id = $_REQUEST['page_id'];
}


// Get settings
$settings_name = 'seed_cspv5_'.$page_id.'_language';
$settings = get_option($settings_name);
if(!empty($settings)){
    $settings = maybe_unserialize($settings);
}

?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<style>
.seed-cspv5 .ui-state-highlight { 
  height: auto; 
  background:#FEF6DD;
  border:1px dotted #F9D975 ;
}

.seed-cspv5 .delete{
    cursor:pointer;
}

.seed-cspv5 .dashicons-move{
    cursor:move;
}

.seed-cspv5 .dashicons{
    cursor:pointer;
}
.language_builder_repeatable-containe .select2-container{
    width:100px !important;
}
</style>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/jquery.repeatable.js"></script>

<div class="wrap columns-2 seed-cspv5">

<?php include(SEED_CSPV5_PLUGIN_PATH.'admin/header.php') ?>
<button id="seed_cspv5_cancel-btn" class="button-secondary">&#8592; Go Back to Page Customizer</button>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">

<div id="post-body-content" >
<form id="seed_cspv5_language_builder">
<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name ?>"/>
<div id="seed_cspv5_section_general" class="postbox seedprod-postbox">
<h3 class="hndle"><span><i class="fa fa-language"></i> Language Builder</h3>

<div class="inside">
<p>Save your changes after adding a New Language to Edit the Language.</p>

<table style="width:100%" class="language_builder_repeatable-container">
<tbody>
<tr valign="top">
<td><strong>Language</strong></td>
<td><strong>Flag</strong></td>
<td><strong>Actions</strong></td>
</tr>
<tr><td colspan="6"><hr/></td></tr>
<tr  valign="top" class="field-group">
<td>
    <input placeholder="Enter the default language name" class="regular-text" name="default_lang[label]" type="text" value="<?php echo esc_attr($settings['default_lang']['label']) ?>"></input>
    </td>
    <td>
        <?php 
                    $flags = seed_cspv5_flags();
                    $flags = array_combine($flags,$flags);
                    seed_cspv5_select('default_lang[flag]',str_replace('.png', '', $flags),$settings['default_lang']['flag'], 'width:100px'); ?>
    </td>
    <td>
   </td>
</tr>
<?php 
$c = 0;
if(!empty($settings)){
    foreach($settings as $k => $v){ 
        if(substr( $k, 0, 4 ) === "lang"){
            ?>
            <tr  valign="top" class="field-group seed_cspv5_sortable">
                <td>
                <input  class="regular-text" name="<?php echo $k ?>[label]" type="text" value="<?php echo esc_attr($v['label']) ?>"></input>
                </td>
                <td>
                    <?php
                    if( empty($v['flag'])){
                        $v['flag'] = '';
                    }
                    seed_cspv5_select($k.'[flag]',str_replace('.png', '', $flags),$v['flag'], 'width:100px'); ?>
                </td>
                <td>
                <span class="dashicons dashicons-edit seed_cspv5_edit" data-lang="<?php echo $k ?>"></span>
                <span class="dashicons dashicons-move" title="Move"></span> <span class="dashicons dashicons-trash seed_cspv5_delete" title="Delete"></span></td>
                
            </tr>
            <?php
            $c++;
        }
    } 
}else{
?>


<?php } ?>
</tbody>
</table>   
<button id="textbox-btn" class="seed_cspv5_add button-primary">Add Language</button> 
            
            </div></div>
            <input id="seed_cspv5_save_form" name="submit" type="submit" value="Save All Changes" class="button-primary"><br><br>


</form> 
                
                
</div><!-- #post-body-content -->
                 



</div>
</div>
</div>

<script type="text/template" id="seed_cspv5_field_textbox">
    <tr valign="top" class="field-group seed_cspv5_sortable">
        <td>
        <input name="lang_{?}[label]" type="text" class="regular-text" placeholder="Enter the language name"></input>
        </td>
        <td>
        </td>
        <td>
        <span class="dashicons dashicons-move" title="Move"></span> <span class="dashicons dashicons-trash seed_cspv5_delete" title="Delete"></span></td>
    </tr>
</script>

<script>

<?php $page_id = $page_id; ?>
<?php $save_form_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_save_language','seed_cspv5_save_language')); ?>
var save_form_url = "<?php echo $save_form_ajax_url; ?>";

var start = <?php echo $c ?>;
var return_url = 'options-general.php?page=seed_cspv5_customizer&seed_cspv5_customize=<?php echo $page_id ?>';

var page_id = '<?php echo $page_id; ?>';
var language_detail_link = "<?php echo admin_url(); ?>options-general.php?page=seed_cspv5_language_detail";

jQuery( "#seed_cspv5_cancel-btn" ).click(function(e) {
	e.preventDefault();
	if(return_url != ''){
		window.location.href = return_url;
	}
	
});

function formatState (state) {
  if (!state.id) { return state.text; }
  var $state = jQuery(
    '<span><img style="display:inline;vertical-align: text-bottom;" src="<?php echo SEED_CSPV5_PLUGIN_URL.'template/images/flags-iso/flat/' ?>' + state.element.text + '.png" class="img-flag" /> ' + state.text + '</span>'
  );
  return $state;
};

jQuery( document ).ready(function() {
    jQuery(".language_builder_repeatable-container select").select2({
  templateResult: formatState,
  templateSelection: formatState
});


    jQuery(function($) {
        $(".language_builder_repeatable-container").repeatable({
            addTrigger: ".seed_cspv5_add",
            deleteTrigger: ".seed_cspv5_delete",
            template: "#seed_cspv5_field_textbox",
            startWith: start,
            onDelete: function () {$(this).parent().parent().remove();jQuery('#seed_cspv5_save_form').trigger('click')},
            onAdd: function () { $("#seed_cspv5_email_tr").before($(".language_builder_repeatable-container tr:last"))},
        });
    });

    jQuery( ".language_builder_repeatable-container tbody" ).sortable({
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

// Edit
jQuery('.seed_cspv5_edit').on('click',function(e){
 var lang_id = jQuery(this).attr('data-lang');
 location.href = language_detail_link+'&page_id='+page_id+'&lang_id='+lang_id;
});

// Save Form
jQuery('#seed_cspv5_save_form').on('click',function(e){
    e.preventDefault();
    jQuery(this).prop( "disabled", true );
    var data = jQuery( "#seed_cspv5_language_builder" ).serialize();


    var jqxhr = jQuery.post( save_form_url+'&page_id='+page_id,data, function(data) {
        jQuery("#seed_cspv5_save_form").prop( "disabled", false );
        location.href= location.href+'&updated=true';
        //console.log(data);
        })
        

});

</script>