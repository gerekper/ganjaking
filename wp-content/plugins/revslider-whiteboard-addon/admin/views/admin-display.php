<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Slider Revolution Whiteboard Add-on
 */
 
 if( !defined( 'ABSPATH') ) exit();
?>
<div id="rev_addon_whiteboard_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">
	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('Configure Whiteboard', 'rs_whiteboard'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	<div class="tp-clearfix"></div>
	<div class="rs-sbs-slideout-inner">

	<!-- Start Settings -->
		<h3 class="tp-steps wb"><span>1</span> <?php _e('"Add-ons" Tab','rs_whiteboard'); ?></h3>
		<img src="<?php echo WHITEBOARD_PLUGIN_URL . "admin/assets/images/tutorial1.jpg"; ?>">
		<div class="wb-featuretext"><?php _e('Right after installing the Whiteboard add-on, you are ready for action!','rs_whiteboard'); ?></div>

		<h3 class="tp-steps wb"><span>2</span> <?php _e('Select "Whiteboard"','rs_whiteboard'); ?></h3>
		<img src="<?php echo WHITEBOARD_PLUGIN_URL . "admin/assets/images/tutorial2.jpg"; ?>">
		<div class="wb-featuretext"><?php _e('Select a layer you would like to add the Whiteboard effect to, and configure the options.','rs_whiteboard'); ?></div>

		<h3 class="tp-steps wb"><span>3</span> <?php _e('"Draw" Layers','rs_whiteboard'); ?></h3>
		<img src="<?php echo WHITEBOARD_PLUGIN_URL . "admin/assets/images/tutorial3.jpg"; ?>">
		<div class="wb-featuretext"><?php _e('The "Draw" setting allows you to let layers appear like they are being drawn. You can configure "Jitter" and hand movement to create your desired effect!','rs_whiteboard'); ?></div>

		<h3 class="tp-steps wb"><span>4</span> <?php _e('"Write" Text','rs_whiteboard'); ?></h3>
		<img src="<?php echo WHITEBOARD_PLUGIN_URL . "admin/assets/images/tutorial4.jpg"; ?>">
		<div class="wb-featuretext"><?php _e('The "Write" setting is only available for text layers and creates a great illusion of your texts being written by hand! Similar to the "Draw" effect you can configure the animation to your liking.','rs_whiteboard'); ?></div>

		<h3 class="tp-steps wb"><span>5</span> <?php _e('"Move" Layers','rs_whiteboard'); ?></h3>
		<img src="<?php echo WHITEBOARD_PLUGIN_URL . "admin/assets/images/tutorial5.jpg"; ?>">
		<div class="wb-featuretext"><?php _e('Using the "Move" setting you can move-in or reveal any layer. Another cool effect for a kinetic animated whiteboard!','rs_whiteboard'); ?></div>

		<h3 class="tp-steps wb"><span>6</span> <?php _e('Advanced Options','rs_whiteboard'); ?></h3>
		<img src="<?php echo WHITEBOARD_PLUGIN_URL . "admin/assets/images/tutorial6.jpg"; ?>">
		<div class="wb-featuretext"><?php _e('In the slider settings you will also find a "Whiteboard" tab, on the right. This is where all the default settings and advanced goodies are found!','rs_whiteboard'); ?></div>
	<!-- End Settings -->
	</div>
</div>