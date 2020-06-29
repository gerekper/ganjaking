<?php

$page_id = '';
if(!empty($_REQUEST['page_id'])){
    $page_id = $_REQUEST['page_id'];
}

// Get form settings
$settings_name = 'seed_cspv5_'.$page_id.'_prizes';
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
<form id="seed_cspv5_prize_builder">
<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name ?>"/>
<div id="seed_cspv5_section_general" class="postbox seedprod-postbox">
<h3 class="hndle">Prize Levels</h3>
<div class="inside">
<table style="width:100%" class="form_builder_repeatable-container">
<tbody>
<tr valign="top">
<td></td>
<td><strong>How Many Referrals Required</strong></td>
<td><strong>Prize Description</strong></td>
<td><strong>Prize Reveal</strong></td>
</tr>
<tr><td colspan="6"><hr/></td></tr>
<tr valign="top">
	<td>Prize Level 1</td>
	<td><input name="prize_1[number]" type="textbox" value="<?php echo (!empty($settings['prize_1']['number'])) ? $settings['prize_1']['number'] : '' ?>"></input></td>
	<td><input name="prize_1[description]" type="textbox" value="<?php echo (!empty($settings['prize_1']['description'])) ? htmlentities($settings['prize_1']['description'],ENT_QUOTES, "UTF-8") : '' ?>"></input></td>
	<td><textarea name="prize_1[reveal]"><?php echo (!empty($settings['prize_1']['reveal'])) ? htmlentities($settings['prize_1']['reveal'],ENT_QUOTES, "UTF-8") : '' ?></textarea></td>
</tr>
<tr valign="top">
	<td>Prize Level 2</td>
	<td><input name="prize_2[number]" type="textbox" value="<?php echo (!empty($settings['prize_2']['number'])) ? $settings['prize_2']['number'] : '' ?>"></input></td>
	<td><input name="prize_2[description]" type="textbox" value="<?php echo (!empty($settings['prize_2']['description'])) ? htmlentities($settings['prize_2']['description'],ENT_QUOTES, "UTF-8") : '' ?>"></input></td>
	<td><textarea name="prize_2[reveal]"><?php echo (!empty($settings['prize_2']['reveal'])) ? htmlentities($settings['prize_2']['reveal'],ENT_QUOTES, "UTF-8") : '' ?></textarea></td>
</tr>
<tr valign="top">
	<td>Prize Level 3</td>
	<td><input name="prize_3[number]" type="textbox" value="<?php echo (!empty($settings['prize_3']['number'])) ? $settings['prize_3']['number'] : '' ?>"></input></td>
	<td><input name="prize_3[description]" type="textbox" value="<?php echo (!empty($settings['prize_3']['description'])) ? htmlentities($settings['prize_3']['description'],ENT_QUOTES, "UTF-8") : '' ?>"></input></td>
	<td><textarea name="prize_3[reveal]"><?php echo (!empty($settings['prize_3']['reveal'])) ? htmlentities($settings['prize_3']['reveal'],ENT_QUOTES, "UTF-8") : '' ?></textarea></td>
</tr>
<tr valign="top">
	<td>Prize Level 4</td>
	<td><input name="prize_4[number]" type="textbox" value="<?php echo (!empty($settings['prize_4']['number'])) ? $settings['prize_4']['number'] : '' ?>"></input></td>
	<td><input name="prize_4[description]" type="textbox" value="<?php echo (!empty($settings['prize_4']['description'])) ? htmlentities($settings['prize_4']['description'],ENT_QUOTES, "UTF-8") : '' ?>"></input></td>
	<td><textarea name="prize_4[reveal]"><?php echo (!empty($settings['prize_4']['reveal'])) ? htmlentities($settings['prize_4']['reveal'],ENT_QUOTES, "UTF-8") : '' ?></textarea></td>
</tr>
<tr valign="top">
	<td>Prize Level 5</td>
	<td><input name="prize_5[number]" type="textbox" value="<?php echo (!empty($settings['prize_5']['number'])) ? $settings['prize_5']['number'] : '' ?>"></input></td>
	<td><input name="prize_5[description]" type="textbox" value="<?php echo (!empty($settings['prize_5']['description'])) ? htmlentities($settings['prize_5']['description'],ENT_QUOTES, "UTF-8") : '' ?>"></input></td>
	<td><textarea name="prize_5[reveal]"><?php echo (!empty($settings['prize_5']['reveal'])) ?  htmlentities($settings['prize_5']['reveal'],ENT_QUOTES, "UTF-8") : '' ?></textarea></td>
</tr>


</tbody>
</table>   
            
            </div></div>
            <input id="seed_cspv5_save_prizes" name="submit" type="submit" value="Save All Changes" class="button-primary"><br><br>
<button id="seed_cspv5_cancel-btn" class="button-secondary">Go Back to Page Customizer</button>
</form> 
                
                
</div><!-- #post-body-content -->
                   <div id="postbox-container-1" class="postbox-container">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">

                             <div class="postbox ">
                                <div class="handlediv" title="Click to toggle"><br /></div>
                                <h3 class="hndle"><span><i class="fa fa-rocket"></i>&nbsp;&nbsp;<?php _e('Instructions', 'seedprod-coming-soon-pro-5') ?></span></h3>
                                <div class="inside">

                                	<strong>How Many Referrals Required</strong>
                                    <p>Enter the number of how many referrals are required to reveal this prize.</p>
                                    <strong>Prize Description</strong>
                                    <p>Enter the Description of the Prize the user will see before it is revealed.</p>
                                    <strong>Prize Reveal</strong>
                                    <p>This is what will be revealed once the user refers the required number of user. You could enter a coupon code, link to a download or other instruction to access the prize.</p>

                                    <strong>Example</strong>
                                    <p>Say I enter "2" in the How Many Referrals Required field, and "30% Off Coupon Code" in the Prize Description and "USE CODE: 30%OFF" in the Prize Reveal. The user would see that it takes to referrals to get the coupon code. Once they got 2 the code would then be revealed.<br><a href="https://support.seedprod.com/article/38-how-prize-levels-work" target="_blank">See this video for a walk through example.</a></p>

                                 
                                </div>
                            </div>
                        </div>
                    </div> 



</div>
</div>
</div>

<script>
<?php $save_form_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_save_prizes','seed_cspv5_save_prizes')); ?>
var save_form_url = "<?php echo $save_form_ajax_url; ?>";

<?php $return_url = (isset($_GET['return']))? preg_replace("/#.*|#.*&|&tab=design/", "", $_GET['return']).'&tab=form#header-form-settings' : ''; ?>
var return_url = '<?php echo urldecode($return_url); ?>';

<?php $page_id = $page_id; ?>
var page_id = '<?php echo $page_id; ?>';

// Save Form
jQuery('#seed_cspv5_save_prizes').on('click',function(e){
    e.preventDefault();
    jQuery(this).prop( "disabled", true );
    var data = jQuery( "#seed_cspv5_prize_builder" ).serialize();
console.log('sdsd');

    var jqxhr = jQuery.post( save_form_url+'&page_id='+page_id,data, function(data) {
        jQuery("#seed_cspv5_save_prizes").prop( "disabled", false );
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
</script>