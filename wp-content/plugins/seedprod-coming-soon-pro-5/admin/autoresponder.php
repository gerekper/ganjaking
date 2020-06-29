<?php

$page_id = '';
if(!empty($_REQUEST['page_id'])){
    $page_id = $_REQUEST['page_id'];
}

// Get autoresponder settings
$settings_name = 'seed_cspv5_'.$page_id.'_autoresponder';
$settings = get_option($settings_name);
if(!empty($settings)){
    $settings = maybe_unserialize($settings);
}


//

?>
<div class="wrap columns-2 seed-cspv5">

<?php include(SEED_CSPV5_PLUGIN_PATH.'admin/header.php') ?>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">

<div id="post-body-content" >
<form id="seed_cspv5_autoresponder_builder">
<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name ?>"/>
<div id="seed_cspv5_section_general" class="postbox seedprod-postbox">
<h3 class="hndle">Autoresponder</h3>
<div class="inside">
<table class="form-table">
<tbody>
<tr valign="top">
    <th scope="row">
        <strong>From Email</strong>
    </th>
    <td>

        <input type="text" class="large-text" name="from_email" value="<?php echo (!empty($settings['from_email'])) ? $settings['from_email'] : '' ?>" />
        <br>
        <small>This email will be sent after a user successfully subscribes. Make sure to complete all the fields.</small>

    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <strong>Subject</strong>
    </th>
    <td>
        <input type="text" class="large-text" name="subject" value="<?php echo (!empty($settings['subject'])) ? $settings['subject'] : '' ?>" />
        <br>

    </td>
</tr>
<tr valign="top">
     <th scope="row">
        <strong>Message</strong>
    </th>
    <td>
          <?php
            if(empty($settings['autoresponder'])){
                $autoresponder = '';
            }else{
                $autoresponder = $settings['autoresponder'];
            }
            $content   = $autoresponder;
            $editor_id = 'autoresponder';
            $args      = array(
                 'textarea_name' => "autoresponder",
            ); 
            
            wp_editor( $content, $editor_id, $args ); 
            ?>


            <small class="description">Template Tags<br><code>{referral_url}</code> - If you have referral tracking enable you can include the visitor's referral url in the auto responder email. </small>
    </td>
</tr>
</tbody>
</table>   
            
            </div></div>
            <input id="seed_cspv5_save_autoresponder" name="submit" type="submit" value="Save All Changes" class="button-primary"><br><br>
<button id="seed_cspv5_cancel-btn" class="button-secondary">Go Back to Page Customizer</button>
</form> 
                
                
</div><!-- #post-body-content -->
<!--                    <div id="postbox-container-1" class="postbox-container">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">

                             <div class="postbox ">
                                <div class="handlediv" title="Click to toggle"><br /></div>
                                <h3 class="hndle"><span><i class="fa fa-rocket"></i>&nbsp;&nbsp;<?php _e('Instructions', 'seedprod-coming-soon-pro-5') ?></span></h3>
                                <div class="inside">

                                	<strong>How Many Referrals Required</strong>
                                    <p>Enter the number of how many referrals are required to reveal this autoresponder.</p>
                                    <strong>Prize Description</strong>
                                    <p>Enter the Description of the Prize the user will see before it is revealed.</p>
                                    <strong>Prize Reveal</strong>
                                    <p>This is what will be revealed once the user refers the required number of user. You could enter a coupon code, link to a download or other instruction to access the autoresponder.</p>

                                    <strong>Example</strong>
                                    <p>Say I enter "2" in the How Many Referrals Required field, and "30% Off Coupon Code" in the Prize Description and "USE CODE: 30%OFF" in the Prize Reveal. The user would see that it takes to referrals to get the coupon code. Once they got 2 the code would then be revealed.<br><a href="http://support.seedprod.com/article/134-how-autoresponder-levels-work" target="_blank">See this video for a walk through example.</a></p>

                                 
                                </div>
                            </div>
                        </div>
                    </div>  -->



</div>
</div>
</div>

<script>
<?php $save_form_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_save_autoresponder','seed_cspv5_save_autoresponder')); ?>
var save_form_url = "<?php echo $save_form_ajax_url; ?>";

<?php $return_url = (isset($_GET['return']))? preg_replace("/#.*|#.*&|&tab=design/", "", $_GET['return']).'&tab=form#header-form-settings' : ''; ?>
var return_url = '<?php echo urldecode($return_url); ?>';

<?php $page_id = $page_id; ?>
var page_id = '<?php echo $page_id; ?>';

// Save Form
jQuery('#seed_cspv5_save_autoresponder').on('click',function(e){
    e.preventDefault();
    jQuery(this).prop( "disabled", true );
    var data = jQuery( "#seed_cspv5_autoresponder_builder" ).serialize();


    var jqxhr = jQuery.post( save_form_url+'&page_id='+page_id,data, function(data) {
        jQuery("#seed_cspv5_save_autoresponder").prop( "disabled", false );
        location.href= location.href+'&updated=true';
        //console.log(data);
        })
        

});


jQuery( "#seed_cspv5_cancel-btn" ).click(function(e) {
	e.preventDefault();
	if(return_url != ''){
		window.location.href = return_url;
	}
	
});

    jQuery( document ).on( 'tinymce-editor-init', function( event, editor ) {

        if(editor.id == 'autoresponder'){
            var autoresponder = tinyMCE.get('autoresponder');
            autoresponder.on('change',function(ed, l) {
                //console.log('dsdsadsa');
                var contents = tinymce.get('autoresponder').getContent();
                jQuery('#autoresponder').text(contents);
                //save_page(false);
            });
        }


    });
</script>