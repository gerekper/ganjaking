
<!-- Main Content -->
<div class="wrap seed_cspv5">
<?php include(SEED_CSPV5_PLUGIN_PATH.'admin/header.php') ?>

<?php
/*
* coming-soon Upgrade
*/

if(isset($_GET['v']) && $_GET['v'] == 'coming-soon'){

  $s1 = get_option('seed_csp4_settings_content');
  $s2 = get_option('seed_csp4_settings_design');
  $s3 = get_option('seed_csp4_settings_advanced');

  if(empty($s1))
      $s1 = array();

  if(empty($s2))
      $s2 = array();

  if(empty($s3))
      $s3 = array();

  $cs_settings  = $s1 + $s2 + $s3;

$include_exclude_options = '0';
$v5_settings = array (
  'status' => $cs_settings['status'],
  'redirect_url' => '',
  'include_exclude_options' => $include_exclude_options,
  'disable_default_excluded_urls' => @$cs_settings['disable_default_excluded_urls'],
  'include_url_pattern' => '',
  'exclude_url_pattern' => '',
  'client_view_url' => '',
  'bypass_expires' => '',
  'ip_access' => '',
  'include_roles' => ''
);


update_option('seed_cspv5_settings_content',$v5_settings);

//Find Coming Soon Page
global $wpdb;
$tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
$cs_page_id = get_option('seed_cspv5_coming_soon_page_id');
if(empty($cs_page_id)){
  // create page

  $default_settings = seed_cspv5_get_page_default_settings();

  $wpdb->insert(
      $tablename,
      array(
          'name' => 'Coming Soon Page',
          'type' => 'cs',
          'path' => '',
          'settings' => serialize(array()) ,
      ),
        array(
            '%s',
            '%s',
        )
  );
  $cs_page_id = $wpdb->insert_id;
  update_option('seed_cspv5_coming_soon_page_id',$cs_page_id);


}


$bg_overlay = 'rgba(0,0,0,0)';
if(isset($cs_settings['bg_overlay'])){
  $bg_overlay = 'rgba(0,0,0,0.5)';
}

$max_width = 600;
if(isset($cs_settings['max_width'])){
  $max_width = $cs_settings['max_width'];
}

$container_color = 'rgba(0,0,0,0)';
if(isset($cs_settings['enable_well'])){
  $container_color = '#ffffff';
}

if(empty($cs_settings['bg_color'])){
  $cs_settings['bg_color'] = '#ffffff';
}

$new_settings = array (
  'page_id' => $cs_page_id ,
  'name' => 'Coming Soon Page',
  'logo' => $cs_settings['logo'],
  'headline' => $cs_settings['headline'],
  'description' => $cs_settings['description'],
  'favicon' => $cs_settings['favicon'],
  'seo_title' => $cs_settings['seo_title'],
  'seo_description' => $cs_settings['seo_description'],
  'ga_analytics' => $cs_settings['ga_analytics'],
  'enable_background_overlay' => '1',
  'background_overlay' => $bg_overlay,
  'theme' => '12',
  'background_color' => $cs_settings['bg_color'],
  'background_image' => $cs_settings['bg_image'],
  'background_repeat' => $cs_settings['bg_repeat'],
  'background_position' => $cs_settings['bg_position'],
  'background_attachment' => $cs_settings['bg_attahcment'],
  'bg_slideshow_slide_speed' => 3000,
  'container_transparent' => '1',
  'container_color' => $container_color,
  'container_width' => '600',
  'text_color' => $cs_settings['text_color'],
  'headline_color' => $cs_settings['headline_color'],
  'button_color' => $cs_settings['link_color'],
  'custom_css' => str_replace('seed-csp4','cspio',$cs_settings['custom_css']),
  'header_scripts' => $cs_settings['header_scripts'],
  'footer_scripts' => $cs_settings['footer_scripts'],
);



$default_settings = seed_cspv5_get_page_default_settings();

$new_settings = array_merge($default_settings,$new_settings);

$new_settings = base64_encode(serialize($new_settings));

$tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
$r = $wpdb->update(
    $tablename,
    array(
        'settings' => $new_settings,

    ),
    array( 'id' => $cs_page_id ),
    array(
        '%s',

    ),
    array( '%d' )
);

if($r !== false){

echo '<p>Your settings have been imported!<br><strong>Please test to make sure every imported correctly.</strong><p>';
echo '<a id="seed-back" href="'.admin_url().'options-general.php?page=seed_cspv5">← Back to Settings</a>';

update_option('seed_cspv5_dismiss_coming_soon_nag',true);
}else{
  echo '<p>There was a problem importing your settings. Please contact support.<p>';
}

}

/*
* V4 Upgrade
*/

if(isset($_GET['v']) && $_GET['v'] == 'cspv4'){
// Get v4 settings
$v4_settings = get_option('seed_cspv4');

$include_exclude_options = '0';
if(!empty($v4_settings['include_url_pattern'])){
  $include_exclude_options = '2';
}elseif(!empty($v4_settings['exclude_url_pattern'])){
  $include_exclude_options = '3';
}

$v5_settings = array (
  'status' => $v4_settings['status'],
  'redirect_url' => $v4_settings['redirect_url'],
  'include_exclude_options' => $include_exclude_options,
  'disable_default_excluded_urls' => array($v4_settings['disable_default_excludes']),
  'include_url_pattern' => (!empty($v4_settings['include_url_pattern'])) ? '>>>'.$v4_settings['include_url_pattern'] : '',
  'exclude_url_pattern' => (!empty($v4_settings['exclude_url_pattern'])) ? '>>>'.$v4_settings['exclude_url_pattern'] : '',
  'client_view_url' => $v4_settings['client_view_url'],
  'bypass_expires' => $v4_settings['bypass_expires'],
  'ip_access' => $v4_settings['ip_access'],
  'include_roles' => $v4_settings['include_roles']
);

update_option('seed_cspv5_settings_content',$v5_settings);
update_option('seed_cspv5_license_key',$v4_settings['api_key']);


//Find Coming Soon Page
global $wpdb;
$tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
$cs_page_id = get_option('seed_cspv5_coming_soon_page_id');
if(empty($cs_page_id)){
  // create page

  $default_settings = seed_cspv5_get_page_default_settings();

  $wpdb->insert(
      $tablename,
      array(
          'name' => 'Coming Soon Page',
          'type' => 'cs',
          'path' => '',
          'settings' => serialize(array()) ,
      ),
        array(
            '%s',
            '%s',
        )
  );
  $cs_page_id = $wpdb->insert_id;
  update_option('seed_cspv5_coming_soon_page_id',$cs_page_id);

}



// Update Subscribers table
global $wpdb;
$tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;
$sql = "UPDATE $tablename SET page_id = %d WHERE page_id = 0";
$safe_sql = $wpdb->prepare($sql,$cs_page_id );
$update_result =$wpdb->get_var($safe_sql);

// Update settings

// Are share buttons blanks
$enable_socialshare_buttons = '';
$enable_socialbuttons = '';
foreach ($v4_settings['share_buttons'] as $key => $value) {
    $value = trim($value);
    if (!empty($value)){
      $enable_socialbuttons = '1';
    }
}

// Where to show share buttons
$show_sharebutton_on = 'thank-you';
if(!empty($v4_settings['show_sharebutton_on_front'])){
  $show_sharebutton_on = 'both';
}

$progress_bar_method = 'date';
if(!empty($v4_settings['progressbar_percentage'])){
  $progress_bar_method = 'percentage';
}

if(empty($v4_settings['thankyou_msg'])){
  $v4_settings['thankyou_msg'] = 'Thank you! You\'ll be notified soon.';
}

// social_profiles
$social_profiles = array();
foreach ($v4_settings['social_profiles'] as $key => $value) {
    $value = trim($value);
    if (!empty($value)){
      $social_profiles[] =  array('url' => $value,'icon' => 'fa-'.$key);
    }
}
if(!empty($social_profiles)){
  $enable_socialprofiles = '1';
}else{
  $enable_socialprofiles = '';
}

// bg images

$bg_slideshow_images = array();
foreach ($v4_settings['bg_slideshow_images'] as $key => $v) {
    $value = trim($v['image']);
    $value2 = trim($v['url']);
    if (!empty($value)){
      $bg_slideshow_images[] =  $value;
    } elseif(!empty($value2)){
      $bg_slideshow_images[] =  $value2;
    }
}

// container_position
$container_position = '1';
if($v4_settings['container_position'] == 'left'){
  $container_position = '4';
}
if($v4_settings['container_position'] == 'right'){
  $container_position = '8';
}

// Text font;

$text_font = 'Helvetica, Arial, sans-serif';
if($v4_settings['text_font']['google']){
  $text_font = "'".$v4_settings['text_font']['font-family']."'";
}
$headline_font = 'Helvetica, Arial, sans-serif';
if($v4_settings['headline_font']['google']){
  $headline_font = "'".$v4_settings['headline_font']['font-family']."'";
}
$button_font = 'Helvetica, Arial, sans-serif';
if($v4_settings['button_font']['google']){
  $button_font = "'".$v4_settings['button_font']['font-family']."'";
}



// share buttons
$share_buttons = array();
//var_dump($v4_settings['share_buttons']);
foreach ($v4_settings['share_buttons'] as $key => $value) {
  if(!empty( $value)){
    //var_dump($key);
    $share_buttons[$key] = '1';
  }
}


// timezone

$countdown_timezone = get_option('timezone_string');
if(empty($countdown_timezone)){
  $countdown_timezone = 'US/Eastern';
}

if(!isset($v4_settings['countdown_hour'])){
  $v4_settings['countdown_hour'] = 0;
}

if(!isset($v4_settings['countdown_minute'])){
  $v4_settings['countdown_minute'] = 0;
}

//var_dump($container_position);

$new_settings = array (
  'first_run' => '',
  'page_id' => $cs_page_id ,
  'name' => 'Coming Soon Page',
  'logo' => $v4_settings['logo']['url'],
  'headline' => $v4_settings['headline'],
  'description' => $v4_settings['description'],
  'enable_form' => '1',
  'emaillist' => $v4_settings['emaillist'],
  'enable_reflink' => $v4_settings['enable_reflink'],
  'display_name' => $v4_settings['name_field'],
  'require_name' => $v4_settings['name_field_required'],
  'thankyou_msg' => $v4_settings['thankyou_msg'],
  'enable_socialprofiles' => $enable_socialprofiles,
  'social_profiles_size' => 'fa-2x',
  'social_profiles_blank' => $v4_settings['social_profiles_blank'],
  'social_profiles' => $social_profiles,
  'enable_socialbuttons'  => $enable_socialbuttons,
  'share_buttons' => $share_buttons,
  'show_sharebutton_on' => $show_sharebutton_on,
  'tweet_text' => $v4_settings['tweet_text'],
  'facebook_thumbnail' => $v4_settings['facebook_thumbnail']['url'],
  'pinterest_thumbnail' => $v4_settings['pinterest_thumbnail']['url'],
  'enable_countdown' => $v4_settings['enable_countdown'],
  'countdown_timezone' => $countdown_timezone,
  'countdown_date' => $v4_settings['countdown_date'].' '.$v4_settings['countdown_hour'].':'.$v4_settings['countdown_minute'],
  'countdown_launch' => $v4_settings['countdown_launch'],
  'countdown_format' => $v4_settings['countdown_format'],
  'enable_progressbar' => $v4_settings['enable_progressbar'],
  'progress_bar_method' => $progress_bar_method,
  'progress_bar_start_date' => $v4_settings['progress_bar_start_date'],
  'progress_bar_end_date' => $v4_settings['progress_bar_end_date'],
  'progressbar_percentage' => $v4_settings['progressbar_percentage'],
  'credit_type' => 'text',
  'enable_footercredit' => '1',
  'footer_credit_text' => $v4_settings['footer_credit_text'],
  'footer_credit_img' => $v4_settings['footer_credit_img']['url'],
  'footer_credit_link' => $v4_settings['footer_credit_link'],
  'footer_affiliate_link' => $v4_settings['footer_affiliate_link'],
  'blocks' =>
  array (
    0 => 'logo',
    1 => 'headline',
    2 => 'description',
    3 => 'form',
    4 => 'progress_bar',
    5 => 'countdown',
    6 => 'social_profiles',
    7 => 'share_buttons',
    8 => 'column',
  ),
  'favicon' => $v4_settings['favicon']['url'],
  'seo_title' => $v4_settings['seo_title'],
  'seo_description' => $v4_settings['seo_description'],
  'ga_analytics' => $v4_settings['ga_analytics'],
  'theme' => '12',
  'background_color' => $v4_settings['background']['background-color'],
  'background_image' => $v4_settings['background']['background-image'],
  'enable_background_overlay' => '',
  'background_overlay' => 'rgba(0,0,0,0)',
  'background_size' => $v4_settings['background']['background-size'],
  'background_repeat' => $v4_settings['background']['background-repeat'],
  'background_position' => $v4_settings['background']['background-position'],
  'background_attachment' => $v4_settings['background']['background-attachment'],
  'bg_slideshow' => $v4_settings['bg_slideshow'],
  'bg_slideshow_images' => $bg_slideshow_images,
  'bg_slideshow_slide_speed' => $v4_settings['bg_slideshow_slide_speed'] / 1000,
  'bg_video' => $v4_settings['bg_video'],
  'bg_video_url' => $v4_settings['bg_video_url'],
  'bg_video_audio' => $v4_settings['bg_video_audio'],
  'bg_video_loop' => $v4_settings['bg_video_loop'],
  'container_effect_animation' => $v4_settings['container_effect_animation'],
  'container_flat' => $v4_settings['container_flat'],
  'container_color' => $v4_settings['container_color']['rgba'],
  'container_radius' => $v4_settings['container_radius'],
  'container_position' => $container_position,
  'container_width' => intval($v4_settings['container_width']['width']),
  'button_color' => $v4_settings['button_font']['color'],
  'form_color' => '#f5f5f5',
  'text_font' => $text_font,
  'text_weight' => $v4_settings['text_font']['font-weight'],
  'text_subset' => $v4_settings['text_font']['subsets'],
  'text_color' => $v4_settings['text_font']['color'],
  'text_size' => intval($v4_settings['text_font']['font-size']),
  'text_line_height' => '1.50',
  'headline_font' => $headline_font,
  'headline_weight' => $v4_settings['headline_font']['font-weight'],
  'headline_subset' => $v4_settings['headline_font']['subsets'],
  'headline_color' => $v4_settings['headline_font']['color'],
  'headline_size' => intval($v4_settings['headline_font']['font-size']),
  'headline_line_height' => '1.00',
  'button_font' => $button_font,
  'button_weight' => $v4_settings['button_font']['font-weight'],
  'button_subset' => $v4_settings['button_font']['subsets'],
  'typekit_id' => $v4_settings['typekit_id'],
  'custom_css' => str_replace('cspv4','cspio',$v4_settings['custom_css']),
  'txt_subscribe_button' => $v4_settings['txt_subscribe_button'],
  'txt_email_field' => $v4_settings['txt_email_field'],
  'txt_name_field' => $v4_settings['txt_name_field'],
  'privacy_policy_link_text' => $v4_settings['privacy_policy_link_text'],
  'txt_already_subscribed_msg' => $v4_settings['txt_already_subscribed_msg'],
  'txt_invalid_email_msg' => $v4_settings['txt_invalid_email_msg'],
  'txt_invalid_name_msg' => $v4_settings['txt_invalid_name_msg'],
  'txt_stats_referral_url' => $v4_settings['txt_stats_referral_url'],
  'txt_stats_referral_stats' => $v4_settings['txt_stats_referral_stats'],
  'txt_stats_referral_clicks' => $v4_settings['txt_stats_referral_clicks'],
  'txt_stats_referral_subscribers' => $v4_settings['txt_stats_referral_subscribers'],
  'txt_countdown_days' => $v4_settings['txt_countdown_days'],
  'txt_countdown_day' => $v4_settings['txt_countdown_day'],
  'txt_countdown_hours' => $v4_settings['txt_countdown_hours'],
  'txt_countdown_hour' => $v4_settings['txt_countdown_hour'],
  'txt_countdown_minutes' => $v4_settings['txt_countdown_minutes'],
  'txt_countdown_minute' => $v4_settings['txt_countdown_minute'],
  'txt_countdown_seconds' => $v4_settings['txt_countdown_seconds'],
  'txt_countdown_second' => $v4_settings['txt_countdown_second'],
  'enable_fitvid' => $v4_settings['enable_fitvidjs'],
  'header_scripts' => $v4_settings['header_scripts'],
  'footer_scripts' => $v4_settings['footer_scripts'],
  'conversion_scripts' => $v4_settings['conversion_scripts'],
  'enable_wp_head_footer' => $v4_settings['enable_wp_head_footer'],
  'enable_background_adv_settings' => '1',
);
//var_dump($new_settings);
// Unset fields
$isset_fields = array(
    'name_field' => 'display_name',
    'name_field_required' => 'require_name',
    'enable_reflink' => 'enable_reflink',
    'enable_countdown' => 'enable_countdown',
    'enable_progressbar' => 'enable_progressbar',
    'countdown_launch' => 'countdown_launch',
    'enable_background_overlay' => 'enable_background_overlay',

  );


foreach($isset_fields as $k=>$v){
  if(empty($v4_settings[$k])){
    unset($new_settings[$v]);
  }
}

if(empty($v4_settings['bg_video_audio'])){
  unset($new_settings['bg_video_audio']);
}

if(empty($v4_settings['bg_video_loop'])){
  unset($new_settings['bg_video_loop']);
}

if(empty($v4_settings['enable_wp_head_footer'])){
  unset($new_settings['enable_wp_head_footer']);
}

if(empty($v4_settings['enable_fitvid'])){
  unset($new_settings['enable_fitvid']);
}

if(empty($v4_settings['enable_retinajs'])){
  unset($new_settings['enable_retinajs']);
}


$new_settings = base64_encode(serialize($new_settings));

$tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
$r = $wpdb->update(
    $tablename,
    array(
        'settings' => $new_settings,

    ),
    array( 'id' => $cs_page_id ),
    array(
        '%s',

    ),
    array( '%d' )
);
//var_dump($r);

// Update Mail Settings
if($v4_settings['emaillist'] == 'database'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'database_notifications' => $v4_settings['database_notifications'],
    'database_notifications_emails' => $v4_settings['database_notifications_emails'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'activecampaign'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'activecampaign_api_url' => $v4_settings['activecampaign_api_url'],
    'activecampaign_api_key' => $v4_settings['activecampaign_api_key'],
    'activecampaign_listid' => $v4_settings['activecampaign_listid'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'aweber'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'aweber_authorization_code' => $v4_settings['aweber_authorization_code'],
    'aweber_listid' => $v4_settings['aweber_listid'],
  );
  $a = get_option('seed_cspv4_aweber_auth');
  update_option('seed_cspv5_aweber_auth_'.$cs_page_id,$a);
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'campaignmonitor'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'campaignmonitor_api_key' => $v4_settings['campaignmonitor_api_key'],
    'campaignmonitor_client_id' => $v4_settings['campaignmonitor_client_id'],
    'campaignmonitor_list_id' => $v4_settings['campaignmonitor_listid'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'constantcontact'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'constantcontact_username' => $v4_settings['constantcontact_username'],
    'constantcontact_password' => $v4_settings['constantcontact_password'],
    'constantcontact_listid' => $v4_settings['constantcontact_listid'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'convertkit'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'convertkit_api_key' => $v4_settings['convertkit_api_key'],
    'convertkit_listid' => $v4_settings['convertkit_listid'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'drip'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'drip_api_key' => $v4_settings['drip_api_key'],
    'drip_account_id' => $v4_settings['drip_account_id'],
    'drip_enable_double_optin' => $v4_settings['drip_enable_double_optin'],
    'drip_list_id' => $v4_settings['drip_listid'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'feedblitz'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'feedblitz_api_key' => $v4_settings['feedblitz_api_key'],
    'feedblitz_listid' => $v4_settings['feedblitz_listid'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'feedburner'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'feedburner_addr' => $v4_settings['feedburner_addr'],
    'feedburner_local' => $v4_settings['feedburner_local'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'followupemails'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'followupemails_email_id' => $v4_settings['followupemails_email_id'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'getresponse'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'getresponse_api_key' => $v4_settings['getresponse_api_key'],
    'getresponse_listid' => $v4_settings['getresponse_listid'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'gravityforms'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'gravityforms_enable_thankyou_page' => $v4_settings['gravityforms_enable_thankyou_page'],
    'gravityforms_form_id' => $v4_settings['gravityforms_form_id'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'html'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'html_integration' => $v4_settings['html_integration'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'icontact'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'icontact_username' => $v4_settings['icontact_username'],
    'icontact_password' => $v4_settings['icontact_password'],
    'icontact_listid' => $v4_settings['icontact_listid'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'infusionsoft'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'infusionsoft_app' => $v4_settings['infusionsoft_app'],
    'infusionsoft_tag_id' => $v4_settings['infusionsoft_tag_id'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'madmimi'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'madmimi_api_key' => $v4_settings['madmimi_api_key'],
    'madmimi_username' => $v4_settings['madmimi_username'],
    'madmimi_listid' => $v4_settings['madmimi_listid'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'mailchimp'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'mailchimp_api_key' => $v4_settings['mailchimp_api_key'],
    'mailchimp_listid' => $v4_settings['mailchimp_listid'],
    'mailchimp_welcome_email' => $v4_settings['mailchimp_welcome_email'],
    'mailchimp_group_name' => $v4_settings['mailchimp_group_name'],
    'mailchimp_groups' => $v4_settings['mailchimp_groups'],
    'mailchimp_update_existing' => $v4_settings['mailchimp_update_existing'],
    'mailchimp_replace_interests' => $v4_settings['mailchimp_replace_interests'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'mailpoet'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'mailpoet_list_id' => $v4_settings['mailpoet_list_id'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'mymail'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'mymail_form_id' => $v4_settings['mymail_form_id'],
  );
  update_option($setting_name,$data);
}

if($v4_settings['emaillist'] == 'sendy'){
  $setting_name = "seed_cspv5_".$cs_page_id.'_'.$v4_settings['emaillist'];
  $data = array(
    'settings_name' => $setting_name,
    'page_id' => $cs_page_id,
    'emaillist' => $v4_settings['emaillist'],
    'sendy_url' => $v4_settings['sendy_url'],
    'sendy_list_id' => $v4_settings['sendy_list_id'],
  );
  update_option($setting_name,$data);
}


if($r){

echo '<p>Your settings have been imported!<br><strong>Please test to make sure every imported correctly.</strong><p>';
echo '<a id="seed-back" href="'.admin_url().'options-general.php?page=seed_cspv5">← Back to Settings</a>';

update_option('seed_cspv5_dismiss_v4_nag',true);
}else{
  echo '<p>There was a problem importing your settings. Please contact support.<p>';
}
}
