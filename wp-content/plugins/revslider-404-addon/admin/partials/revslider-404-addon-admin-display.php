<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_404_Addon
 * @subpackage Revslider_404_Addon/admin/partials
 */

//saved values
$revslider_404_addon_values = array();
parse_str(get_option('revslider_404_addon'), $revslider_404_addon_values);

//defaults
$revslider_404_addon_values['revslider-404-addon-type'] = isset($revslider_404_addon_values['revslider-404-addon-type']) ? $revslider_404_addon_values['revslider-404-addon-type'] : 'slider';
$revslider_404_addon_values['revslider-404-addon-active'] = isset($revslider_404_addon_values['revslider-404-addon-active']) ? $revslider_404_addon_values['revslider-404-addon-active'] : '0';
$revslider_404_addon_values['revslider-404-addon-slider'] = isset($revslider_404_addon_values['revslider-404-addon-slider']) ? $revslider_404_addon_values['revslider-404-addon-slider'] : '';
$revslider_404_addon_values['revslider-404-addon-page'] = isset($revslider_404_addon_values['revslider-404-addon-page']) ? $revslider_404_addon_values['revslider-404-addon-page'] : '';

// Available Sliders
$slider = new RevSlider();
$arrSliders = $slider->getArrSliders();
$defSlider = "";

// Available Pages
$pages = get_pages(array());
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="revslider_404_addon_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">
	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('404', 'revslider-404-addon'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	<div class="tp-clearfix"></div>
	<div class="rs-sbs-slideout-inner">
	<!-- Start Settings -->
		<form id="revslider-404-addon-form">
			<div id="subcat-404-main" class="subcat-wrapper">
				<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php _e("Activate 404 Mode","revslider-404-addon"); ?></div>
					<div><?php _e('Turn it on?','revslider-404-addon'); ?></div>				
				</div>
				<div class="rs-dash-content-space"></div>
				<input type="checkbox" name="revslider-404-addon-active" value="1" <?php checked( $revslider_404_addon_values['revslider-404-addon-active'], '1' , 1 ); ?>> <?php _e("Active","revslider-404-addon"); ?>
				<div class="rs-dash-content-space"></div>
				<div class="rs-dash-content-with-icon">
					<div class="rs-dash-strong-content"><?php _e("404 Content","revslider-404-addon"); ?></div>
					<div><?php _e('Display a slider or a whole page content?','revslider-404-addon'); ?></div>				
				</div>
				<div class="rs-dash-content-space"></div>
		      	<input name="revslider-404-addon-type" <?php checked( $revslider_404_addon_values['revslider-404-addon-type'], 'slider' , 1 ); ?> type="radio" value="slider">&nbsp;&nbsp;Slider
		      	&nbsp;&nbsp;
		      	<input name="revslider-404-addon-type" <?php checked( $revslider_404_addon_values['revslider-404-addon-type'], 'page' , 1 ); ?> type="radio" value="page">&nbsp;&nbsp;Page
		      	
		      	<div class="rs-dash-content-space"></div>
				
		      	<div id="revslider_404_type_slider" class="revslider_404_type_details">
		      		<div class="rs-dash-content-with-icon">
						<div class="rs-dash-strong-content"><?php _e("Slider","revslider-404-addon"); ?></div>
						<div><?php _e('Display full page with the selected slider','revslider-404-addon'); ?></div>	
					</div>

			      	<div class="rs-dash-content-space"></div>
		      		<select name="revslider-404-addon-slider">
			      	<?php
			      		$slider_select_options = "";
						foreach($arrSliders as $sliderony){
							$slider_alias = $sliderony->getAlias();
							echo '<option value="'.$slider_alias.'" '.selected( $revslider_404_addon_values['revslider-404-addon-slider'], $slider_alias , 0 ).'>'. $sliderony->getTitle() . '</option>';
						} 
					?>
					</select>

					<div class="rs-dash-content-space"></div>
					<div class="rs-dash-content-with-icon">
							<div class="rs-dash-strong-content"><?php _e("Page Title","revslider-404-addon"); ?></div>
							<div><?php _e('Page Title for 404 page','revslider-404-addon'); ?></div>	
					</div>
					
					<div class="rs-dash-content-space"></div>
				    <input name="revslider-404-addon-page-title" style="width:300px" type="text" value="<?php echo isset($revslider_404_addon_values['revslider-404-addon-page-title']) ? stripslashes($revslider_404_addon_values['revslider-404-addon-page-title']) : '';?>">
		      	</div>
		      	
		      	<div id="revslider_404_type_page" class="revslider_404_type_details">
					<div class="rs-dash-content-with-icon">
							<div class="rs-dash-strong-content"><?php _e("Page","revslider-404-addon"); ?></div>
							<div><?php _e('Select a page content to be displayed (works with <a href="https://revolution.themepunch.com">RevSlider</a> and <a href="https://essential.themepunch.com">EssGrid</a> shortcodes, other shortcodes not guaranteed to function).','revslider-404-addon'); ?></div>	
					</div>
					<div class="rs-dash-content-space"></div>
			      	<select name="revslider-404-addon-page">
				      	<?php
				      		foreach($pages as $page){
				      			if(!$page->post_password) echo '<option value="'.$page->ID.'" '.selected( $revslider_404_addon_values['revslider-404-addon-page'], $page->ID , 0 ).'>'. $page->post_title . '</option>';
							}
						?>
					</select>
		      	</div>
		      	<div class="rs-dash-content-space"></div>
			</div>	
			<span id="ajax_revslider_404_addon_nonce" class="hidden"><?php echo wp_create_nonce( 'ajax_revslider_404_addon_nonce' ) ?></span>
			<div class="rs-dash-bottom-wrapper">
				<span style="display:none" id="revslider-404-addon-wait" class="loader_round">Please Wait...</span>					
				<a href="javascript:void(0);" id="revslider-404-addon-save" class="rs-dash-button">Save</a>
			</div>	
		</form>
	<!-- End Settings -->
	</div>
</div>