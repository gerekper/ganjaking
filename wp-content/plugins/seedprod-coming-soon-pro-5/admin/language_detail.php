
<?php

$page_id = '';
if(!empty($_REQUEST['page_id'])){
    $page_id = $_REQUEST['page_id'];
}


$lang_id = '';
if(!empty($_REQUEST['lang_id'])){
    $lang_id = $_REQUEST['lang_id'];
}

// Get Page Settings
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


// Get settings
$settings_name = 'seed_cspv5_'.$page_id.'_language_'.$lang_id;
$settings = get_option($settings_name);
if(!empty($settings)){
    $settings = maybe_unserialize($settings);
}

$parent_settings_name =  'seed_cspv5_'.$page_id.'_language';
$parent_settings = get_option($parent_settings_name);
?>

<style>


.seed-cspv5 label{
    font-weight:bold;
    display:block;

}

.settings_page_seed_cspv5_language_detail #post-body{
    margin-right:0 !important;
}



</style>

<div class="wrap columns-2 seed-cspv5">

<?php include(SEED_CSPV5_PLUGIN_PATH.'admin/header.php') ?>
<button id="seed_cspv5_cancel-btn" class="button-secondary">&#8592; Go Back to Languages</button>
<br><br>
<button id="seed_cspv5_cancel-customizer-btn" class="button-secondary">&#8592; Go Back to Page Customizer</button>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">

<div id="post-body-content" >
<form id="seed_cspv5_language_builder">
<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name ?>"/>
<div id="seed_cspv5_section_general" class="postbox seedprod-postbox">
<h3 class="hndle">Language Builder</h3>
<div class="inside">
<table style="width:100%" class="language_builder_repeatable-container">
<tbody>
<tr valign="top">
<td><strong>Default Language</strong></td>
<td><strong><?php echo $parent_settings[$lang_id] ["label"] ?></strong></td>
</tr>
<tr><td colspan="6"><hr/></td></tr>
<tr>
    <td>
    <label>Headline</label>
    <code>
    <?php echo  $page_settings['headline'] ?>
    </code>
    </td>
    <td>
    <label>Headline</label>
    <input type="textbox" name="headline" class="regular-text" value="<?php echo (isset($settings['headline']))? esc_attr($settings['headline'])  : '' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Description</label>
   <textarea  autocomplete="off" rows="10" class="regular-text" style="width:25em" readonly><?php echo $page_settings['description'] ?></textarea>
    </td>
    <td>
    <label>Description</label>
    <textarea  autocomplete="off" rows="10" class="regular-text" style="width:25em" name="description" id="description"><?php echo (isset($settings['description']))?$settings['description'] : '' ?></textarea>

    </td>
</tr>

<tr>
    <td>
    <label>Thank You Message</label>
   <textarea  autocomplete="off" rows="10" class="regular-text" style="width:25em" readonly><?php echo $page_settings['thankyou_msg'] ?></textarea>
    </td>
    <td>
    <label>Thank You Message</label>
    <textarea  autocomplete="off" rows="10" class="regular-text" style="width:25em" name="thankyou_msg" id="thankyou_msg"><?php echo (isset($settings['thankyou_msg']))?$settings['thankyou_msg'] : '' ?></textarea>

    </td>
</tr>

<tr>
    <td>
    <label>Subscribe Button</label>
    <code>
    <?php echo esc_html($page_settings['txt_subscribe_button']) ?>
    </code>
    </td>
    <td>
    <label>Subscribe Button</label>
    <input type="textbox" name="txt_subscribe_button" class="regular-text" value="<?php echo (isset($settings['txt_subscribe_button']))?esc_attr($settings['txt_subscribe_button']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Email Field</label>
    <code>
    <?php echo esc_html($page_settings['txt_email_field']) ?>
    </code>
    </td>
    <td>
    <label>Email Field</label>
    <input type="textbox" name="txt_email_field" class="regular-text" value="<?php echo  (isset($settings['txt_email_field']))?esc_attr($settings['txt_email_field']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Name Field</label>
    <code>
    <?php echo esc_html($page_settings['txt_name_field']) ?>
    </code>
    </td>
    <td>
    <label>Name Field</label>
    <input type="textbox" name="txt_name_field" class="regular-text" value="<?php echo(isset($settings['txt_name_field']))?esc_attr($settings['txt_name_field']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Privacy Policy Text</label>
    <code>
    <?php echo esc_html($page_settings['privacy_policy_link_text']) ?>
    </code>
    </td>
    <td>
    <label>Privacy Policy Text</label>
    <input type="textbox" name="privacy_policy_link_text" class="regular-text" value="<?php echo  (isset($settings['privacy_policy_link_text']))?esc_attr($settings['privacy_policy_link_text']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Already Subscribed</label>
    <code>
    <?php echo esc_html($page_settings['txt_already_subscribed_msg']) ?>
    </code>
    </td>
    <td>
    <label>Already Subscribed</label>
    <input type="textbox" name="txt_already_subscribed_msg" class="regular-text" value="<?php echo (isset($settings['txt_already_subscribed_msg']))?esc_attr($settings['txt_already_subscribed_msg']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Invalid Email</label>
    <code>
    <?php echo esc_html($page_settings['txt_invalid_email_msg']) ?>
    </code>
    </td>
    <td>
    <label>Invalid Email</label>
    <input type="textbox" name="txt_invalid_email_msg" class="regular-text" value="<?php echo (isset($settings['txt_invalid_email_msg']))?esc_attr($settings['txt_invalid_email_msg']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Invalid Name</label>
    <code>
    <?php echo esc_html($page_settings['txt_invalid_name_msg']) ?>
    </code>
    </td>
    <td>
    <label>Invalid Name</label>
    <input type="textbox" name="txt_invalid_name_msg" class="regular-text" value="<?php echo (isset($settings['txt_invalid_name_msg']))?esc_attr($settings['txt_invalid_name_msg']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Referral URL Message</label>
    <code>
    <?php echo esc_html($page_settings['txt_stats_referral_url']) ?>
    </code>
    </td>
    <td>
    <label>Referral URL Message</label>
    <input type="textbox" name="txt_stats_referral_url" class="regular-text" value="<?php echo (isset($settings['txt_stats_referral_url']))?esc_attr($settings['txt_stats_referral_url']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Referral Stats Message</label>
    <code>
    <?php echo esc_html($page_settings['txt_stats_referral_stats']) ?>
    </code>
    </td>
    <td>
    <label>Referral Stats Message</label>
    <input type="textbox" name="txt_stats_referral_stats" class="regular-text" value="<?php echo (isset($settings['txt_stats_referral_stats']))?esc_attr($settings['txt_stats_referral_stats']) :'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Referral Stats Clicks</label>
    <code>
    <?php echo esc_html($page_settings['txt_stats_referral_clicks']) ?>
    </code>
    </td>
    <td>
    <label>Referral Stats Clicks</label>
    <input type="textbox" name="txt_stats_referral_clicks" class="regular-text" value="<?php echo (isset($settings['txt_stats_referral_clicks']))?esc_attr($settings['txt_stats_referral_clicks']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Referral Stats Subscribers</label>
    <code>
    <?php echo esc_html($page_settings['txt_stats_referral_subscribers']) ?>
    </code>
    </td>
    <td>
    <label>Referral Stats Subscribers</label>
    <input type="textbox" name="txt_stats_referral_subscribers" class="regular-text" value="<?php echo (isset($settings['txt_stats_referral_subscribers']))?esc_attr($settings['txt_stats_referral_subscribers']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Days</label>
    <code>
    <?php echo esc_html($page_settings['txt_countdown_days']) ?>
    </code>
    </td>
    <td>
    <label>Days</label>
    <input type="textbox" name="txt_countdown_days" class="regular-text" value="<?php echo (isset($settings['txt_countdown_days']))?esc_attr($settings['txt_countdown_days']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Day</label>
    <code>
    <?php echo esc_html($page_settings['txt_countdown_day']) ?>
    </code>
    </td>
    <td>
    <label>Day</label>
    <input type="textbox" name="txt_countdown_day" class="regular-text" value="<?php echo (isset($settings['txt_countdown_day']))?esc_attr($settings['txt_countdown_day']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Hours</label>
    <code>
    <?php echo esc_html($page_settings['txt_countdown_hours']) ?>
    </code>
    </td>
    <td>
    <label>Hours</label>
    <input type="textbox" name="txt_countdown_hours" class="regular-text" value="<?php echo (isset($settings['txt_countdown_hours']))?esc_attr($settings['txt_countdown_hours']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Hour</label>
    <code>
    <?php echo esc_html($page_settings['txt_countdown_hour']) ?>
    </code>
    </td>
    <td>
    <label>Hour</label>
    <input type="textbox" name="txt_countdown_hour" class="regular-text" value="<?php echo (isset($settings['txt_countdown_hour']))?esc_attr($settings['txt_countdown_hour']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Minutes</label>
    <code>
    <?php echo esc_html($page_settings['txt_countdown_minutes']) ?>
    </code>
    </td>
    <td>
    <label>Minutes</label>
    <input type="textbox" name="txt_countdown_minutes" class="regular-text" value="<?php echo (isset($settings['txt_countdown_minutes']))?esc_attr($settings['txt_countdown_minutes']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Minute</label>
    <code>
    <?php echo esc_html($page_settings['txt_countdown_minute']) ?>
    </code>
    </td>
    <td>
    <label>Minute</label>
    <input type="textbox" name="txt_countdown_minute" class="regular-text" value="<?php echo (isset($settings['txt_countdown_minute']))?esc_attr($settings['txt_countdown_minute']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Seconds</label>
    <code>
    <?php echo esc_html($page_settings['txt_countdown_seconds']) ?>
    </code>
    </td>
    <td>
    <label>Seconds</label>
    <input type="textbox" name="txt_countdown_seconds" class="regular-text" value="<?php echo (isset($settings['txt_countdown_seconds']))?esc_attr($settings['txt_countdown_seconds']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Second</label>
    <code>
    <?php echo esc_html($page_settings['txt_countdown_second']) ?>
    </code>
    </td>
    <td>
    <label>Second</label>
    <input type="textbox" name="txt_countdown_second" class="regular-text" value="<?php echo (isset($settings['txt_countdown_second']))?esc_attr($settings['txt_countdown_second']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Contact Form Label</label>
    <code>
    <?php echo esc_html($page_settings['txt_contact_us']) ?>
    </code>
    </td>
    <td>
    <label>Contact Form Label</label>
    <input type="textbox" name="txt_contact_us" class="regular-text" value="<?php echo (isset($settings['txt_contact_us']))?esc_attr($settings['txt_contact_us']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Contact Form Email</label>
    <code>
    <?php echo esc_html($page_settings['txt_contact_form_email']) ?>
    </code>
    </td>
    <td>
    <label>Contact Form Email</label>
    <input type="textbox" name="txt_contact_form_email" class="regular-text" value="<?php echo (isset($settings['txt_contact_form_email']))?esc_attr($settings['txt_contact_form_email']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Contact Form Message</label>
    <code>
    <?php echo esc_html($page_settings['txt_contact_form_msg']) ?>
    </code>
    </td>
    <td>
    <label>Contact Form Message</label>
    <input type="textbox" name="txt_contact_form_msg" class="regular-text" value="<?php echo (isset($settings['txt_contact_form_msg']))?esc_attr($settings['txt_contact_form_msg']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Contact Form Send</label>
    <code>
    <?php echo esc_html($page_settings['txt_contact_form_send']) ?>
    </code>
    </td>
    <td>
    <label>Contact Form Send</label>
    <input type="textbox" name="txt_contact_form_send" class="regular-text" value="<?php echo (isset($settings['txt_contact_form_send']))?esc_attr($settings['txt_contact_form_send']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Contact Form Error</label>
    <code>
    <?php echo esc_html($page_settings['txt_contact_form_error']) ?>
    </code>
    </td>
    <td>
    <label>Contact Form Error</label>
    <input type="textbox" name="txt_contact_form_error" class="regular-text" value="<?php echo (isset($settings['txt_contact_form_error']))?esc_attr($settings['txt_contact_form_error']):'' ?>">
    </td>
</tr>
<tr>
    <td>
    <label>Optin Confirmation Text</label>
    <code>
    <?php echo esc_html($page_settings['optin_confirmation_text']) ?>
    </code>
    </td>
    <td>
    <label>Optin Confirmation Text</label>
    <input type="textbox" name="optin_confirmation_text" class="regular-text" value="<?php echo (isset($settings['optin_confirmation_text']))?esc_attr($settings['optin_confirmation_text']):'' ?>">
    </td>
</tr>
</tbody>
</table>   
            
            </div></div>
            <input id="seed_cspv5_save_form" name="submit" type="submit" value="Save All Changes" class="button-primary">
<br><br>

</form> 
                
                
</div><!-- #post-body-content -->




</div>
</div>
</div>



<script>
<?php $page_id = $page_id; ?>
<?php $save_form_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_save_language_detail','seed_cspv5_save_language_detail')); ?>
var save_form_url = "<?php echo $save_form_ajax_url; ?>";


var return_url = 'options-general.php?page=seed_cspv5_language&page_id=<?php echo $page_id ?>';
var return_customizer_url = 'options-general.php?page=seed_cspv5_customizer&seed_cspv5_customize=<?php echo $page_id ?>';

var page_id = '<?php echo $page_id; ?>';
var language_detail_link = "<?php echo admin_url(); ?>options-general.php?page=seed_cspv5_language_detail";

jQuery( "#seed_cspv5_cancel-btn" ).click(function(e) {
	e.preventDefault();
	if(return_url != ''){
		window.location.href = return_url;
	}
	
});

jQuery( "#seed_cspv5_cancel-customizer-btn" ).click(function(e) {
    e.preventDefault();
    if(return_url != ''){
        window.location.href = return_customizer_url;
    }
    
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