<?php
/**
 * Provide a admin area view for the plugin
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Login_Addon
 * @subpackage Revslider_Login_Addon/admin/partials
 */

//saved values
$revslider_login_addon_values = array();
parse_str(get_option('revslider_login_addon'), $revslider_login_addon_values);

//defaults
$revslider_login_addon_values['revslider-login-addon-type'] 	= isset($revslider_login_addon_values['revslider-login-addon-type']) 	? $revslider_login_addon_values['revslider-login-addon-type'] : 'slider';
$revslider_login_addon_values['revslider-login-addon-slider'] 	= isset($revslider_login_addon_values['revslider-login-addon-slider']) 	? $revslider_login_addon_values['revslider-login-addon-slider'] : '';
$revslider_login_addon_values['revslider-login-addon-page'] 	= isset($revslider_login_addon_values['revslider-login-addon-page']) 	? $revslider_login_addon_values['revslider-login-addon-page'] : '';
$revslider_login_addon_values['revslider-login-addon-lost-password-link'] = isset($revslider_login_addon_values['revslider-login-addon-lost-password-link']) ? $revslider_login_addon_values['revslider-login-addon-lost-password-link'] : '0';
$revslider_login_addon_values['revslider-login-addon-redirect-to'] = isset($revslider_login_addon_values['revslider-login-addon-redirect-to']) ? $revslider_login_addon_values['revslider-login-addon-redirect-to'] : '0';
$revslider_login_addon_values['revslider-login-addon-lost-password-overtake'] = isset($revslider_login_addon_values['revslider-login-addon-lost-password-overtake']) ? $revslider_login_addon_values['revslider-login-addon-lost-password-overtake'] : '0';
$revslider_login_addon_values['revslider-login-addon-remember-me'] = isset($revslider_login_addon_values['revslider-login-addon-remember-me']) ? $revslider_login_addon_values['revslider-login-addon-remember-me'] : '0';

// Available Sliders
$slider = new RevSliderSlider();
$arrSliders = $slider->get_sliders();
$defSlider = "";

// Available Pages
$pages = get_pages(array());
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="revslider_login_addon_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">
	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('Login Page', 'revslider-login-addon'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	<div class="tp-clearfix"></div>
	<div class="rs-sbs-slideout-inner">
	<!-- Start Settings -->
		<form id="revslider-login-addon-form">
			
			<div class="rs-dash-content-with-icon">
				<div class="rs-dash-strong-content"><?php _e("Login Content","revslider-login-addon"); ?></div>
				<div><?php _e('Display a slider or a whole page content?','revslider-login-addon'); ?></div>				
			</div>
			<div class="rs-dash-content-space"></div>
	      	<input name="revslider-login-addon-type" <?php checked( $revslider_login_addon_values['revslider-login-addon-type'], 'slider' , 1 ); ?> type="radio" value="slider">&nbsp;&nbsp;Slider
	      	&nbsp;&nbsp;
	      	<input name="revslider-login-addon-type" <?php checked( $revslider_login_addon_values['revslider-login-addon-type'], 'page' , 1 ); ?> type="radio" value="page">&nbsp;&nbsp;Page
	      	
	      	<div class="rs-dash-content-space"></div>
			
	      	<div id="revslider_login_type_slider" class="revslider_login_type_details">
	      		<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php _e("Slider","revslider-login-addon"); ?></div>
					<div><?php _e('Display full page with the selected slider','revslider-login-addon'); ?></div>	
				</div>

		      	<div class="rs-dash-content-space"></div>
	      		<select name="revslider-login-addon-slider">
		      	<?php
		      		$slider_select_options = "";
					foreach($arrSliders as $sliderony){
						$slider_alias = $sliderony->get_alias();
						echo '<option value="'.$slider_alias.'" '.selected( $revslider_login_addon_values['revslider-login-addon-slider'], $slider_alias , 0 ).'>'. $sliderony->get_title() . '</option>';
					} 
				?>
				</select>

				<div class="rs-dash-content-space"></div>
				<div class="rs-dash-content-with-icon">
						<div class="rs-dash-strong-content"><?php _e("Page Title","revslider-login-addon"); ?></div>
						<div><?php _e('Page Title for login page','revslider-login-addon'); ?></div>	
				</div>
				
				<div class="rs-dash-content-space"></div>
			    <input name="revslider-login-addon-page-title" style="width:300px" type="text" value="<?php echo isset($revslider_login_addon_values['revslider-login-addon-page-title']) ? stripslashes($revslider_login_addon_values['revslider-login-addon-page-title']) : '';?>">
	      	</div>
	      	
	      	<div id="revslider_login_type_page" class="revslider_login_type_details">
				<div class="rs-dash-content-with-icon">
						<div class="rs-dash-strong-content"><?php _e("Page","revslider-login-addon"); ?></div>
						<div><?php _e('Select a page content to be displayed (works with <a href="https://revolution.themepunch.com">RevSlider</a> and <a href="https://essential.themepunch.com">EssGrid</a> shortcodes, other shortcodes not guaranteed to function).','revslider-login-addon'); ?></div>	
				</div>
				<div class="rs-dash-content-space"></div>
		      	<select name="revslider-login-addon-page">
			      	<?php
			      		foreach($pages as $page){
			      			if(!$page->post_password) echo '<option value="'.$page->ID.'" '.selected( $revslider_login_addon_values['revslider-login-addon-page'], $page->ID , 0 ).'>'. $page->post_title . '</option>';
						}
					?>
				</select>
	      	</div>
	      	<div class="rs-dash-content-space"></div>
			<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php _e("Default Redirect Link","revslider-login-addon"); ?></div>
					<div><?php _e('If Login Page is called directly link here after sucessfull log in','revslider-login-addon'); ?></div>	
			</div>
			<div class="rs-dash-content-space"></div>
		    <input name="revslider-login-addon-redirect-to" style="width:300px" type="text" value="<?php echo isset($revslider_login_addon_values['revslider-login-addon-redirect-to']) ? stripslashes($revslider_login_addon_values['revslider-login-addon-redirect-to']) : '';?>">
		    <div class="rs-dash-content-space"></div>
		    <div class="rs-dash-content-with-icon">
				<div class="rs-dash-strong-content"><?php _e("Activate Lost Password Link","revslider-login-addon"); ?></div>
				<div><?php _e('Include lost password link beneath the form automatically?','revslider-login-addon'); ?></div>				
			</div>
			<div class="rs-dash-content-space"></div>
			<input type="checkbox" name="revslider-login-addon-lost-password-link" value="1" <?php checked( $revslider_login_addon_values['revslider-login-addon-lost-password-link'], '1' , 1 ); ?>> <?php _e("Display link","revslider-login-addon"); ?>
			<div class="rs-dash-content-space"></div>
		    <div class="rs-dash-content-with-icon">
				<div class="rs-dash-strong-content"><?php _e("Overtake Lost Password","revslider-login-addon"); ?></div>
				<div><?php _e('Display Lost Password Form within Slider Revolution Login Form','revslider-login-addon'); ?></div>				
			</div>
			<div class="rs-dash-content-space"></div>
			<input type="checkbox" name="revslider-login-addon-lost-password-overtake" value="1" <?php checked( $revslider_login_addon_values['revslider-login-addon-lost-password-overtake'], '1' , 1 ); ?>> <?php _e("Overtake Standard Form","revslider-login-addon"); ?>

			<div class="rs-dash-content-space"></div>
		    <div class="rs-dash-content-with-icon">
				<div class="rs-dash-strong-content"><?php _e("Display Remember me","revslider-login-addon"); ?></div>
				<div><?php _e('Display Remember me checkbox within Slider Revolution Login Form','revslider-login-addon'); ?></div>				
			</div>
			<div class="rs-dash-content-space"></div>
			<input type="checkbox" name="revslider-login-addon-remember-me" value="1" <?php checked( $revslider_login_addon_values['revslider-login-addon-remember-me'], '1' , 1 ); ?>> <?php _e("Remember me?","revslider-login-addon"); ?>

			<!-- SAVE -->
		    <div class="rs-dash-content-space"></div>
	      	<span id="ajax_revslider_login_addon_nonce" class="hidden"><?php echo wp_create_nonce( 'ajax_revslider_login_addon_nonce' ) ?></span>
			<div class="rs-dash-bottom-wrapper">
				<span style="display:none" id="revslider-login-addon-wait" class="loader_round">Please Wait...</span>					
				<a href="javascript:void(0);" id="revslider-login-addon-save" class="rs-dash-button">Save</a>
			</div>	
		</form>
	<!-- End Settings -->
	</div>
</div>