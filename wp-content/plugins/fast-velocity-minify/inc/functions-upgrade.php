<?php


# run after updating
add_action( 'plugins_loaded', 'fastvelocity_plugin_upgrade_completed');
function fastvelocity_plugin_upgrade_completed() {
	
	global $fastvelocity_plugin_version;
	
	# current FVM install date, create if it doesn't exist
	$ver = get_option("fastvelocity_plugin_version");
	if ($ver == false) { $ver = '1'; }
	
	# save current version on upgrade
	if ($ver != $fastvelocity_plugin_version) {
		update_option( "fastvelocity_plugin_version", $fastvelocity_plugin_version, 'no');
	}
	
	# run for any update lower than 2.8.6
	if (version_compare($ver, '2.8.6', '<')) {

		# default ignore list update
		$exc = array('/themes/Avada/assets/js/main.min.js', '/plugins/woocommerce-product-search/js/product-search.js', '/plugins/revslider/public/assets/js/jquery.themepunch.tools.min.js', '/js/TweenMax.min.js', '/themes/jupiter/assets/js/min/full-scripts', '/plugins/LayerSlider/static/layerslider/js/greensock.js', '/themes/kalium/assets/js/main.min.js', '/js/mediaelement/', '/plugins/elementor/assets/js/common.min.js', '/plugins/elementor/assets/js/frontend.min.js', '/plugins/elementor-pro/assets/js/frontend.min.js', '/themes/kalium/assets/js/main.min.js', '/wp-includes/js/mediaelement/wp-mediaelement.min.js');
		update_option('fastvelocity_min_ignorelist', implode(PHP_EOL, $exc), 'no');

	}
}


# upgrade notifications
function fastvelocity_plugin_update_message($currentPluginMetadata, $newPluginMetadata) {
	if (isset($newPluginMetadata->upgrade_notice) && strlen(trim($newPluginMetadata->upgrade_notice)) > 0){
		echo '<span style="display:block; background: #F7FCFE; padding: 14px 0 6px 0; margin: 10px -12px -12px -16px;">';
		echo '<span class="notice notice-info" style="display:block; padding: 10px; margin: 0;">';
		echo '<span class="dashicons dashicons-megaphone" style="margin-left: 2px; margin-right: 6px;"></span>';
		echo strip_tags($newPluginMetadata->upgrade_notice);
		echo '</span>'; 
		echo '</span>'; 
	}
}
add_action( 'in_plugin_update_message-fast-velocity-minify/fvm.php', 'fastvelocity_plugin_update_message', 10, 2 );
