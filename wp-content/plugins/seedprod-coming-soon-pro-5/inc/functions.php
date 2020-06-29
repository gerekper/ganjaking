<?php

/**
* Add to array if value does not exist
*/
function seed_cspv5_array_add($arr, $key, $value){
    if (!array_key_exists($key, $arr)) {

        $arr[$key] = $value;
    }
    return $arr;
}

function seed_button_func( $atts ){
    $a = shortcode_atts( array(
        'href' => '',
        'class' => '',
        'text' => 'Button',
        'target' => '',
    ), $atts );
    return "<a href='{$a['href']}' target='{$a['target']}' class='btn btn-primary {$a['class']}'>{$a['text']}</a>";
}
add_shortcode( 'seed_button', 'seed_button_func' );

function seed_bypass_form_func( $atts ){
    $a = shortcode_atts( array(
        'msg' => 'Password',
        'button-txt' => 'Enter',
        'return' => '',
    ), $atts );
    ob_start();
    ?>
    <div class="row">
    <div class="col-md-12 seperate">
    <div class="input-group">
    <input type="password" id="cspio-bypass" class="form-control input-lg form-el" placeholder="<?php echo $a['msg'] ?>"></input>
    <span class="input-group-btn">
    <button id="cspio-bypass-btn" class="btn btn-lg btn-primary form-el noglow"><?php echo $a['button-txt'] ?></button>
    </span>
    </div>
    </div>
    </div>
    <script>
    jQuery( document ).ready(function($) {
        $( "#cspio-bypass-btn" ).click(function(e) {
          e.preventDefault();
          window.location = "?bypass="+$("#cspio-bypass").val()+'&return=<?php echo urlencode($a['return']) ?>';
        });
    });
    </script>
    
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
add_shortcode( 'seed_bypass_form', 'seed_bypass_form_func' );

/**
* Page default settings
*/
function seed_cspv5_get_page_default_settings(){
    $settings = array();
    // Default Block
    $blocks = array('logo','headline','description','form','progress_bar','countdown','social_profiles','share_buttons','column');
    if(!empty($settings->blocks)){
    foreach($block as $v){
        $settings->blocks = seed_cspv5_array_add($v);
    }
    $blocks = $settings->blocks;
    }

    //var_dump($settings);
    //die();

    $fields = array('name');

    $settings = seed_cspv5_array_add($settings,'disabled_fields', '');
    $settings = seed_cspv5_array_add($settings,'typekit_id', '');
    $settings = seed_cspv5_array_add($settings,'publish_method', '');
    $settings = seed_cspv5_array_add($settings,'header_scripts', '');
    $settings = seed_cspv5_array_add($settings,'footer_scripts', '');
    $settings = seed_cspv5_array_add($settings,'conversion_scripts', '');
    $settings = seed_cspv5_array_add($settings,'theme_css', '');
    $settings = seed_cspv5_array_add($settings,'custom_css', '');
    $settings = seed_cspv5_array_add($settings,'footer_affiliate_link', '');
    $settings = seed_cspv5_array_add($settings,'credit_type', '');
    $settings = seed_cspv5_array_add($settings,'progress_bar_method', '');
    $settings = seed_cspv5_array_add($settings,'button_subset', '');
    $settings = seed_cspv5_array_add($settings,'show_sharebutton_on', 'thank-you');
    $settings = seed_cspv5_array_add($settings,'fields',$fields);
    $settings = seed_cspv5_array_add($settings,'blocks',$blocks);
    $settings = seed_cspv5_array_add($settings,'logo','');
    $settings = seed_cspv5_array_add($settings,'headline','Coming Soon');
    $settings = seed_cspv5_array_add($settings,'description',"Get ready! Something really cool is coming!");
    $settings = seed_cspv5_array_add($settings,'emaillist','database');
    $settings = seed_cspv5_array_add($settings,'privacy_policy_link_text','');
    $settings = seed_cspv5_array_add($settings,'privacy_policy','');
    $settings = seed_cspv5_array_add($settings,'thankyou_msg','Thank You!');
    $settings = seed_cspv5_array_add($settings,'tweet_text','');
    $settings = seed_cspv5_array_add($settings,'facebook_thumbnail','');
    $settings = seed_cspv5_array_add($settings,'pinterest_thumbnail','');
    $settings = seed_cspv5_array_add($settings,'progress_bar_start_date','');
    $settings = seed_cspv5_array_add($settings,'progress_bar_end_date','');
    $settings = seed_cspv5_array_add($settings,'progressbar_percentage','0');
    $settings = seed_cspv5_array_add($settings,'countdown_date','');
    $settings = seed_cspv5_array_add($settings,'countdown_timezone','US/Eastern');
    $settings = seed_cspv5_array_add($settings,'countdown_format','dHMS');
    $settings = seed_cspv5_array_add($settings,'social_profiles_size','');
    $settings = seed_cspv5_array_add($settings,'favicon','');
    $settings = seed_cspv5_array_add($settings,'seo_title','');
    $settings = seed_cspv5_array_add($settings,'seo_description','');
    $settings = seed_cspv5_array_add($settings,'ga_analytics','');
    $settings = seed_cspv5_array_add($settings,'footer_credit_text','');
    $settings = seed_cspv5_array_add($settings,'footer_credit_img','');
    $settings = seed_cspv5_array_add($settings,'footer_credit_link','');
    $settings = seed_cspv5_array_add($settings,'background_color','#ffffff');
    $settings = seed_cspv5_array_add($settings,'contactform_color','');
    $settings = seed_cspv5_array_add($settings,'socialprofile_color','');
    $settings = seed_cspv5_array_add($settings,'background_size','cover');
    $settings = seed_cspv5_array_add($settings,'background_repeat','no-repeat');
    $settings = seed_cspv5_array_add($settings,'background_position','fixed');
    $settings = seed_cspv5_array_add($settings,'background_attachment','center top');
    $settings = seed_cspv5_array_add($settings,'background_image','');
    $settings = seed_cspv5_array_add($settings,'bg_slideshow_slide_speed','3');
    $settings = seed_cspv5_array_add($settings,'bg_slideshow_slide_transition','1');
    $settings = seed_cspv5_array_add($settings,'bg_video_url','');
    $settings = seed_cspv5_array_add($settings,'share_buttons_twitter','');
    $settings = seed_cspv5_array_add($settings,'share_buttons_facebook','');
    $settings = seed_cspv5_array_add($settings,'share_buttons_facebook_send','');
    $settings = seed_cspv5_array_add($settings,'share_buttons_google','');
    $settings = seed_cspv5_array_add($settings,'share_buttons_linkedin','');
    $settings = seed_cspv5_array_add($settings,'share_buttons_pinit','');
    $settings = seed_cspv5_array_add($settings,'share_buttons_tumblr','');
    $settings = seed_cspv5_array_add($settings,'show_sharebutton_on_front','');
    $settings = seed_cspv5_array_add($settings,'enable_form','1');
    $settings = seed_cspv5_array_add($settings,'enable_fitvid','1');
    $settings = seed_cspv5_array_add($settings,'bg_slideshow','');
    $settings = seed_cspv5_array_add($settings,'bg_slideshow_randomize','');
    $settings = seed_cspv5_array_add($settings,'bg_video','');
    $settings = seed_cspv5_array_add($settings,'bg_video_loop','');
    $settings = seed_cspv5_array_add($settings,'container_flat','1');
    $settings = seed_cspv5_array_add($settings,'progressbar_effect','');
    $settings = seed_cspv5_array_add($settings,'headline_subset','');
    $settings = seed_cspv5_array_add($settings,'theme','0');
    $settings = seed_cspv5_array_add($settings,'social_profiles_blank','1');
    $settings = seed_cspv5_array_add($settings,'headline_font','Open Sans');
    $settings = seed_cspv5_array_add($settings,'headline_color','#333333');
    $settings = seed_cspv5_array_add($settings,'headline_size','32');
    $settings = seed_cspv5_array_add($settings,'headline_weight','400');
    $settings = seed_cspv5_array_add($settings,'headline_line_height','1');
    $settings = seed_cspv5_array_add($settings,'text_font','Open Sans');
    $settings = seed_cspv5_array_add($settings,'text_color','#555555');
    $settings = seed_cspv5_array_add($settings,'text_size','16');
    $settings = seed_cspv5_array_add($settings,'text_weight','400');
    $settings = seed_cspv5_array_add($settings,'text_line_height','1.5');
    $settings = seed_cspv5_array_add($settings,'text_subset','');
    $settings = seed_cspv5_array_add($settings,'button_font','Open Sans');
    $settings = seed_cspv5_array_add($settings,'button_color','#000000');
    $settings = seed_cspv5_array_add($settings,'element_border_color','#000000');
    $settings = seed_cspv5_array_add($settings,'form_border_color','#000000');
    $settings = seed_cspv5_array_add($settings,'form_color','#f5f5f5');
    $settings = seed_cspv5_array_add($settings,'button_size','14');
    $settings = seed_cspv5_array_add($settings,'button_weight','400');
    $settings = seed_cspv5_array_add($settings,'button_line_height','1');
    $settings = seed_cspv5_array_add($settings,'container_color','#ffffff');
    $settings = seed_cspv5_array_add($settings,'background_overlay','rgba(0,0,0,0.5)');
    $settings = seed_cspv5_array_add($settings,'container_position','1');
    $settings = seed_cspv5_array_add($settings,'container_width','600');
    $settings = seed_cspv5_array_add($settings,'form_width','100');
    $settings = seed_cspv5_array_add($settings,'container_radius','2');
    $settings = seed_cspv5_array_add($settings,'container_effect_animation','');
    $settings = seed_cspv5_array_add($settings,'txt_email_field','Email');
    $settings = seed_cspv5_array_add($settings,'txt_subscribe_button','Notify Me');
    $settings = seed_cspv5_array_add($settings,'txt_name_field','Name');
    $settings = seed_cspv5_array_add($settings,'txt_already_subscribed_msg',"You're already subscribed.");
    $settings = seed_cspv5_array_add($settings,'txt_invalid_email_msg','Invalid Email');
    $settings = seed_cspv5_array_add($settings,'txt_invalid_name_msg','Invalid Name');
    $settings = seed_cspv5_array_add($settings,'txt_stats_referral_url','Your Referral URL is:');
    $settings = seed_cspv5_array_add($settings,'txt_stats_referral_clicks','Clicks');
    $settings = seed_cspv5_array_add($settings,'txt_stats_referral_stats','Your Referral Stats');
    $settings = seed_cspv5_array_add($settings,'txt_stats_referral_subscribers','Referred Subscribers');
    $settings = seed_cspv5_array_add($settings,'txt_countdown_days','Days');
    $settings = seed_cspv5_array_add($settings,'txt_countdown_day','Day');
    $settings = seed_cspv5_array_add($settings,'txt_countdown_hours','Hours');
    $settings = seed_cspv5_array_add($settings,'txt_countdown_hour','Hour');
    $settings = seed_cspv5_array_add($settings,'txt_countdown_minutes','Minutes');
    $settings = seed_cspv5_array_add($settings,'txt_countdown_minute','Minute');
    $settings = seed_cspv5_array_add($settings,'txt_countdown_seconds','Seconds');
    $settings = seed_cspv5_array_add($settings,'txt_countdown_second','Second');

    $settings = seed_cspv5_array_add($settings,'txt_contact_us','Contact Us');
    $settings = seed_cspv5_array_add($settings,'txt_contact_form_email','Email');
    $settings = seed_cspv5_array_add($settings,'txt_contact_form_msg','Message');
    $settings = seed_cspv5_array_add($settings,'txt_contact_form_send','Send');
    $settings = seed_cspv5_array_add($settings,'txt_contact_form_error','Please enter your email and a message.');
    $settings = seed_cspv5_array_add($settings,'txt_prize_level_more','Refer %d more subscribers to claim this.');

    

    return $settings;
}


/**
 * Update cookie length for bypass url
 */
function seed_cspv5_change_wp_cookie_logout( $expirein ) {
    global $seed_cspv5_bypass_expires;
    if(!empty($seed_cspv5_bypass_expires)){
        return $seed_cspv5_bypass_expires; // Modify the exire cookie
    }else{
        return $expirein;
    }
}


/**
 * Get roles
 */
function seed_cspv5_get_roles() {
    global $wp_roles;
    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();

    if ( is_multisite() )
        $roles['superadmin'] = __('Super Admin','seedprod');

    return $roles;
}

/**
* Check per
*/
function seed_cspv5_cu( $rper = null ) {
    if(!empty($rper)){
        $uper = explode(",", get_option('seed_cspv5_per'));
        if(in_array($rper,$uper)){
            return true;
        }else{
            return false;
        }
    }else{
        $a = get_option('seed_cspv5_a');
        if($a){
            return true;
        }else{
            return false;
        }
    }
}


/**
 * Get Plugin API value
 */
function seed_cspv5_get_plugin_api_value($k = null) {
    return false;
    global $seed_cspv5;
    extract($seed_cspv5);
    if(!empty($plugin_api)){
        $plugin_api = str_replace(array("\n\r","\n"), "&", $plugin_api);
        parse_str($plugin_api, $plugin_api);
        if(array_key_exists($k, $plugin_api)){
            return $plugin_api[$k];
        }else{
            return false;
        }

    }
}

/**
 *  Get IP
 */
function seed_cspv5_get_ip(){
    $ip = '';
    if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) AND strlen($_SERVER['HTTP_X_FORWARDED_FOR'])>6 ){
        $ip = strip_tags($_SERVER['HTTP_X_FORWARDED_FOR']);
    }elseif( !empty($_SERVER['HTTP_CLIENT_IP']) AND strlen($_SERVER['HTTP_CLIENT_IP'])>6 ){
         $ip = strip_tags($_SERVER['HTTP_CLIENT_IP']);
    }elseif(!empty($_SERVER['REMOTE_ADDR']) AND strlen($_SERVER['REMOTE_ADDR'])>6){
         $ip = strip_tags($_SERVER['REMOTE_ADDR']);
    }//endif
    if(!$ip) $ip="127.0.0.1";
    return strip_tags($ip);
}


/**
 *  Get Ref Link
 */
function seed_cspv5_ref_link(){
    global $seed_cspv5_post_result;
    $ref_link = '';


    if(!empty($seed_cspv5_post_result['ref'])){

        $ref_url = $_SERVER["HTTP_REFERER"];
        if(empty($ref_url)){
         $ref_url =  $_REQUEST['href'];
        }
            $ref_url_parts = parse_url($ref_url);
            $port = '';
            if(!empty($ref_url_parts['port'])){
                $port = ':'.$ref_url_parts['port'];
            }
            if(!empty($ref_url_parts['port'])){
                if($ref_url_parts['port'] == '80'){
                    $port = '';
                }
            }
            $ref_link = $ref_url_parts['scheme'].'://'.$ref_url_parts['host'].$port.$ref_url_parts['path'];
            $ref_link = $ref_link.'?ref='.$seed_cspv5_post_result['ref'];
    }else{
        if(empty($_REQUEST['href'])){
            $ref_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        }else{
            $ref_link =  $_REQUEST['href'];
        }
    }
    return $ref_link;
}

/* Delete Free Version Nag */

function seed_cspv5_deactivate_free_version_notice() {
   ?>
   <div class="notice notice-error is-dismissible">
      <p><?php echo sprintf( __( 'You need to deactivate the Free Version of the Coming Soon & Maintenance Mode by Seedprod plugin so there are no conflicts with the Pro Version.<br> %sClick this link to deactivate the Free Version.%s', 'seedprod-coming-soon-pro' ), '<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=coming-soon%2Fcoming-soon.php&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_coming-soon/coming-soon.php' ) . '">', '</a>' ); ?></p>
   </div>
   <?php
}

function seed_cspv5_remove_free_nag() {
   if ( function_exists( 'seed_csp4_load_textdomain' ) ) {
      add_action( 'admin_notices', 'seed_cspv5_deactivate_free_version_notice' );
      return;
   }
}
add_action( 'plugins_loaded', 'seed_cspv5_remove_free_nag' );

/* Import nag */
add_action( 'admin_menu', 'seed_cspv5_import_nag' );

function seed_cspv5_import_nag(){
  // cspv5 nag
  if(isset($_GET['action']) && $_GET['action'] == 'seed_cspv5_dismiss_v4_nag'){
    update_option('seed_cspv5_dismiss_v4_nag',true);
  }
  $v4s = get_option('seed_cspv5');
  $v4_nag = get_option('seed_cspv5_dismiss_v4_nag');

  if((isset($_GET['page']) && $_GET['page'] == 'seed_cspv5') && !empty($v4s) && empty($v4_nag)){
    add_settings_error(
        null,
        'seed_cspv5_import_v4',
        'We have detected settings from Version 4, would like to import these settings? Note: This will delete and replace your current page if you have one setup. <a href="'. admin_url() .'options-general.php?page=seed_cspv5_import&v=cspv5">Yes, Import Settings</a> | <a href="'. admin_url() .'options-general.php?page=seed_cspv5&action=seed_cspv5_dismiss_v4_nag">No</a>  ',
        'error'
    );
  }
  // coming-soon nag
  if(isset($_GET['action']) && $_GET['action'] == 'seed_cspv5_dismiss_coming_soon_nag'){
    update_option('seed_cspv5_dismiss_coming_soon_nag',true);
  }
  $coming_soon_s = get_option('seed_csp4_settings_content');
  $coming_soon_nag = get_option('seed_cspv5_dismiss_coming_soon_nag');

  if((isset($_GET['page']) && $_GET['page'] == 'seed_cspv5') && !empty($coming_soon_s) && empty($coming_soon_nag)){

    add_settings_error(
        null,
        'seed_cspv5_import_coming_soon',
        'We have detected settings from our Free Coming Soon and Maintenance Mode Plugin, would like to import these settings? Note: This will delete and replace your current page if you have one setup. <a href="'. admin_url() .'options-general.php?page=seed_cspv5_import&v=coming-soon">Yes, Import Settings</a> | <a href="'. admin_url() .'options-general.php?page=seed_cspv5&action=seed_cspv5_dismiss_coming_soon_nag">No</a>  ',
        'error'
    );
  }
}


/* API nag */
add_action( 'admin_menu', 'seed_cspv5_api_nag' );

function seed_cspv5_api_nag(){
  $api_nag = get_option('seed_cspv5_api_nag');
  if((isset($_GET['page']) && $_GET['page'] == 'seed_cspv5') && !empty($api_nag)){
    add_settings_error(
        null,
        'seed_cspv5_api_nag',
        $api_nag,
        'error'
    );
  }
}

if(seed_cspv5_cu('none')){
  add_action( 'admin_menu', 'seed_cspv5_l_nag' );
}

function seed_cspv5_l_nag(){
  if((isset($_GET['page']) && $_GET['page'] == 'seed_cspv5')){
    add_settings_error(
        null,
        'seed_cspv5_l_nag',
        '<a href="options-general.php?page=seed_cspv5_welcome">Please enter a valid license key.</a>',
        'error'
    );
  }
}


function seed_cspv5_extensions() {

	$extensions = array(
		SEED_CSPV5_PLUGIN_PATH.'extentions/mailchimp/mailchimp.php',
        SEED_CSPV5_PLUGIN_PATH.'extentions/mailchimp/mailchimp-v3.php',
        SEED_CSPV5_PLUGIN_PATH.'extentions/convertkit/convertkit.php',
        SEED_CSPV5_PLUGIN_PATH.'extentions/activecampaign/activecampaign.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/database/database.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/sendy/sendy.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/mailpoet/mailpoet.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/madmimi/madmimi.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/infusionsoft/infusionsoft.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/icontact/icontact.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/htmlwebform/htmlwebform.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/gravityforms/gravityforms.php',
        SEED_CSPV5_PLUGIN_PATH.'extentions/ninjaforms/ninjaforms.php',
        SEED_CSPV5_PLUGIN_PATH.'extentions/followupemails/followupemails.php',
        SEED_CSPV5_PLUGIN_PATH.'extentions/formidable/formidable.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/getresponse/getresponse.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/feedburner/feedburner.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/constantcontact/constantcontact.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/campaignmonitor/campaignmonitor.php',
		SEED_CSPV5_PLUGIN_PATH.'extentions/aweber/aweber.php',
        SEED_CSPV5_PLUGIN_PATH.'extentions/drip/drip.php',
        SEED_CSPV5_PLUGIN_PATH.'extentions/mymail/mymail.php',
        SEED_CSPV5_PLUGIN_PATH.'extentions/feedblitz/feedblitz.php',
        SEED_CSPV5_PLUGIN_PATH.'extentions/zapier/zapier.php',
	);

	$active_extensions = apply_filters( 'seed_cspv5_active_extensions', $extensions );

	foreach ( $active_extensions as $i ) {
		require_once( $i );
	}

} // END seed_cspv5_extensions()

seed_cspv5_extensions();


function seed_cspv5_select($id,$option_values,$selected = null,$style=null){
echo "<select id='$id' name='$id' class='form-control input-sm' style='{$style}'>";
if(!empty($option_values)){
foreach ( $option_values as $k => $v ) {
	if(is_array($v)){
		echo '<optgroup label="'.ucwords($k).'">';
		foreach ( $v as $k1=>$v1 ) {
			echo '<option value="'.$k1.'"' . selected( $selected , $k1, false ) . ">$v1</option>";
		}
		echo '</optgroup>';
	}else{
			if(!isset($options[ $id ])){
				$options[ $id ] = '';
			}
    		echo "<option value='$k' " . selected( $selected , $k, false ) . ">$v</option>";
	}
}
}
echo "</select> ";
}

function seed_cspv5_select_lang($id,$option_values,$selected = null,$style=null){
echo "<select id='$id' name='$id' class='form-control input-sm' style='{$style}'>";
if(!empty($option_values)){
foreach ( $option_values as $k => $v ) {
    if(is_array($v)){
        echo '<optgroup label="'.ucwords($k).'">';
        foreach ( $v as $k1=>$v1 ) {
            echo '<option value="'.$k1.'"' . selected( $selected , $k1, false ) . ">$v1</option>";
        }
        echo '</optgroup>';
    }else{
            if(!isset($options[ $id ])){
                $options[ $id ] = '';
            }
            $language_name = $v;
            $language_name = explode("|",$language_name);
            if(!empty($language_name[0])){
                $v = $language_name[0];
            }
            echo "<option value='$k' " . selected( $selected , $k, false ) . ">$v</option>";
    }
}
}
echo "</select> ";
}





add_action( 'pre_update_option_seed_cspv5_settings_content', 'seed_cspv5_sanatize_bypass_url', 10, 2 );

function seed_cspv5_sanatize_bypass_url( $old_value, $new_value )
{

    $old_value['client_view_url'] = sanitize_title_with_dashes($old_value['client_view_url']);
    return $old_value;
}

function seed_cspv5_flags(){
    return array(
'AD.png',
'AE.png',
'AF.png',
'AG.png',
'AI.png',
'AL.png',
'AM.png',
'AN.png',
'AO.png',
'AQ.png',
'AR.png',
'AS.png',
'AT.png',
'AU.png',
'AW.png',
'AX.png',
'AZ.png',
'BA.png',
'BB.png',
'BD.png',
'BE.png',
'BF.png',
'BG.png',
'BH.png',
'BI.png',
'BJ.png',
'BL.png',
'BM.png',
'BN.png',
'BO.png',
'BR.png',
'BS.png',
'BT.png',
'BW.png',
'BY.png',
'BZ.png',
'CA.png',
'CC.png',
'CD.png',
'CF.png',
'CG.png',
'CH.png',
'CI.png',
'CK.png',
'CL.png',
'CM.png',
'CN.png',
'CO.png',
'CR.png',
'CU.png',
'CV.png',
'CX.png',
'CY.png',
'CZ.png',
'DE.png',
'DJ.png',
'DK.png',
'DM.png',
'DO.png',
'DZ.png',
'EC.png',
'EE.png',
'EG.png',
'EH.png',
'ER.png',
'ES.png',
'ET.png',
'EU.png',
'FI.png',
'FJ.png',
'FK.png',
'FM.png',
'FO.png',
'FR.png',
'GA.png',
'GB.png',
'GD.png',
'GE.png',
'GG.png',
'GH.png',
'GI.png',
'GL.png',
'GM.png',
'GN.png',
'GQ.png',
'GR.png',
'GS.png',
'GT.png',
'GU.png',
'GW.png',
'GY.png',
'HK.png',
'HN.png',
'HR.png',
'HT.png',
'HU.png',
'ID.png',
'IE.png',
'IL.png',
'IM.png',
'IN.png',
'IQ.png',
'IR.png',
'IS.png',
'IT.png',
'JE.png',
'JM.png',
'JO.png',
'JP.png',
'KE.png',
'KG.png',
'KH.png',
'KI.png',
'KM.png',
'KN.png',
'KP.png',
'KR.png',
'KV.png',
'KW.png',
'KY.png',
'KZ.png',
'LA.png',
'LB.png',
'LC.png',
'LI.png',
'LK.png',
'LR.png',
'LS.png',
'LT.png',
'LU.png',
'LV.png',
'LY.png',
'MA.png',
'MC.png',
'MD.png',
'ME.png',
'MG.png',
'MH.png',
'MK.png',
'ML.png',
'MM.png',
'MN.png',
'MO.png',
'MP.png',
'MR.png',
'MS.png',
'MT.png',
'MU.png',
'MV.png',
'MW.png',
'MX.png',
'MY.png',
'MZ.png',
'NA.png',
'NC.png',
'NE.png',
'NF.png',
'NG.png',
'NI.png',
'NL.png',
'NO.png',
'NP.png',
'NR.png',
'NU.png',
'NZ.png',
'OM.png',
'PA.png',
'PE.png',
'PG.png',
'PH.png',
'PK.png',
'PL.png',
'PN.png',
'PR.png',
'PS.png',
'PT.png',
'PW.png',
'PY.png',
'QA.png',
'RO.png',
'RS.png',
'RU.png',
'RW.png',
'SA.png',
'SB.png',
'SC.png',
'SD.png',
'SE.png',
'SG.png',
'SH.png',
'SI.png',
'SK.png',
'SL.png',
'SM.png',
'SN.png',
'SO.png',
'SR.png',
'SS.png',
'ST.png',
'SV.png',
'SY.png',
'SZ.png',
'TC.png',
'TD.png',
'TG.png',
'TH.png',
'TJ.png',
'TM.png',
'TN.png',
'TO.png',
'TP.png',
'TR.png',
'TT.png',
'TV.png',
'TW.png',
'TZ.png',
'UA.png',
'UG.png',
'US.png',
'UY.png',
'UZ.png',
'VA.png',
'VC.png',
'VE.png',
'VG.png',
'VI.png',
'VN.png',
'VU.png',
'WS.png',
'YE.png',
'YT.png',
'ZA.png',
'ZM.png',
'ZW.png',
);
}


add_shortcode( 'seed_bypass_url', 'seed_cspv5_bypass_url' );
function seed_cspv5_bypass_url($echo = true){

    global $seed_cspv5;
    $seed_cspv5 = get_option('seed_cspv5_settings_content');
    extract($seed_cspv5);

    $output = home_url('/').'?bypass='.$client_view_url.'&return='.urlencode($_SERVER['REQUEST_URI']);

    $output = apply_filters('seed_cspv5_bypass_url', $output);

    if ( $echo ) {
        echo $output;
    } else {
        return $output;
    }
}


add_shortcode( 'seed_bypass_link', 'seed_cspv5_bypass_link' );
function seed_cspv5_bypass_link($atts,$echo = true){

    extract( shortcode_atts( array(
        'text' => 'Bypass',
        'class' => '',
    ), $atts ) );

    global $seed_cspv5;
    $seed_cspv5 = get_option('seed_cspv5_settings_content');
    extract($seed_cspv5);

    $output = '<a href="'.seed_cspv5_bypass_url(false).'" class="'.$class.'">'.$text.'</a>';

    $output = apply_filters('seed_cspv5_bypass_link', $output);

    if ( $echo ) {
        echo $output;
    } else {
        return $output;
    }
}

add_shortcode( 'seed_contact_form', 'seed_cspv5_contact_form' );
function seed_cspv5_contact_form($atts,$echo = true){

    extract( shortcode_atts( array(
        'text' => 'Contact Us',
        'icon' => true
    ), $atts ) );

    global $seed_cspv5;
    $seed_cspv5 = get_option('seed_cspv5_settings_content');
    extract($seed_cspv5);

    $icon_code = '';
    if($icon){
        $icon_code = '<i class="fa fa-envelope "></i>';
    }

    $output = '<a href="javascript:void(0)" onclick="javascript:'."jQuery('#cspio-cf-modal').modal('show');".'">'.$icon_code.' '.$text.'</a>';

    $output = apply_filters('seed_cspv5_contact_', $output);

    if ( $echo ) {
        echo $output;
    } else {
        return $output;
    }
}

/**
 * @param array      $array
 * @param int|string $position
 * @param mixed      $insert
 */
function seed_cspv5_array_insert(&$array, $position, $insert)
{
    if (is_int($position)) {
        array_splice($array, $position, 0, $insert);
    } else {
        $pos   = array_search($position, array_keys($array));
        $array = array_merge(
            array_slice($array, 0, $pos),
            $insert,
            array_slice($array, $pos)
        );
    }
}

function seed_cspv5_change_string_boolean_to_boolean(&$item){
    if ($item == "true") {
        $item = true;
    } else if ($item == "false") {
        $item = false;
    } 
}


function seed_cspv5_add_email_integrations( $arr ) {
    // Maybe modify $example in some way.
    $arr = $arr + array(
    'feedblitz' => 'FeedBlitz',
    'drip' => 'Drip',
    'feedburner' => 'FeedBurner',
    'activecampaign' => 'Active Campaign',
    'aweber' => 'Aweber',
    'campaignmonitor' => 'Campaign Monitor',
    'constantcontact' => 'Constant Contact',
    'convertkit' => 'ConvertKit',
    'getresponse' => 'Get Response',
    'gravityforms' => 'Gravity Forms',
    'ninjaforms' => 'Ninja Forms',
    'followupemails' => 'Follow-Up Emails',
    'formidable' => 'Formidable',
    'icontact' => 'iContact',
    'infusionsoft' => 'Infusionsoft',
    'madmimi' => 'Mad Mimi',
    'mailchimp' => 'MailChimp',
    'sendy' => 'Sendy',
    'zapier' => 'Zapier',
    'mailpoet' => 'MailPoet',
    'mymail' => 'Mailster formerly MyMail',
    'htmlwebform' => 'HTML Web Form / Shortcode'
    );
    return $arr;
}
if(seed_cspv5_cu('em')){
    add_filter( 'seed_cspv5_providers', 'seed_cspv5_add_email_integrations' );
}
