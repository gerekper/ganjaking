<?php 
//if no tab is set, default to options tab
$tab = !empty($_GET['tab']) ? $_GET['tab'] : 'options';

//restore defaults
if(!empty($_POST['restore'])) {
	if($tab == 'options') {
		$defaults = perfmatters_default_options();
		if(!empty($defaults)) {
			update_option("perfmatters_options", $defaults);
		}
	}
	elseif($tab == 'tools') {
		$defaults = perfmatters_default_tools();
		update_option("perfmatters_tools", $defaults);
	}
}

$tools = get_option('perfmatters_tools');
if(!empty($tools['accessibility_mode'])) {
	echo "<style>#perfmatters-admin .perfmatters-tooltip-subtext{display: none;}</style>";
}

//plugin settings wrapper
echo "<div id='perfmatters-admin' class='wrap'>";

	//hidden h2 for admin notice placement
	echo "<h2 style='display: none;'></h2>";

    //tab navigation
	echo "<div class='nav-tab-wrapper'>";
		echo "<a href='?page=perfmatters&tab=options' class='nav-tab " . ($tab == 'options' || '' ? 'nav-tab-active' : '') . "'>" . __('Options', 'perfmatters') . "</a>";
		echo "<a href='?page=perfmatters&tab=tools' class='nav-tab " . ($tab == 'tools' ? 'nav-tab-active' : '') . "'>" . __('Tools', 'perfmatters') . "</a>";
		if(!is_plugin_active_for_network('perfmatters/perfmatters.php')) {
			echo "<a href='?page=perfmatters&tab=license' class='nav-tab " . ($tab == 'license' ? 'nav-tab-active' : '') . "'>" . __('License', 'perfmatters') . "</a>";
		}
	echo "</div>";

	//plugin options form
	echo "<form method='post' action='options.php' id='perfmatters-options-form'" . ($tab == 'tools' ? " enctype='multipart/form-data'" : "") . ">";

		//main options tab
		if($tab == 'options') {

			//options subnav
			echo "<input type='hidden' name='section' id='subnav-section' />";
			echo "<div class='perfmatters-subnav'>";
				echo "<a href='#options-general' id='general-section' rel='general' class='active'><span class='dashicons dashicons-dashboard'></span>" . __('General', 'perfmatters') . "</a>";
				echo "<a href='#options-assets' id='assets-section' rel='assets'><span class='dashicons dashicons-editor-code'></span>" . __('Assets', 'perfmatters') . "</a>";
				echo "<a href='#options-preload' id='preload-section' rel='preload'><span class='dashicons dashicons-clock'></span>" . __('Preloading', 'perfmatters') . "</a>";
				echo "<a href='#options-lazyload' id='lazyload-section' rel='lazyload'><span class='dashicons dashicons-images-alt2'></span>" . __('Lazy Loading', 'perfmatters') . "</a>";
				echo "<a href='#options-fonts' id='fonts-section' rel='fonts'><span class='dashicons dashicons-editor-paste-text'></span>" . __('Fonts', 'perfmatters') . "</a>";
				echo "<a href='#options-cdn' id='cdn-section' rel='cdn'><span class='dashicons dashicons-admin-site-alt2'></span>" . __('CDN', 'perfmatters') . "</a>";
				echo "<a href='#options-analytics' id='analytics-section' rel='analytics'><span class='dashicons dashicons-chart-bar'></span>" . __('Analytics', 'perfmatters') . "</a>";
			echo "</div>";

			settings_fields('perfmatters_options');

			echo "<section id='options-general' class='section-content active'>";
		    	perfmatters_settings_section('perfmatters_options', 'perfmatters_options');
		    	perfmatters_settings_section('perfmatters_options', 'login_url');
		    	perfmatters_settings_section('perfmatters_options', 'perfmatters_woocommerce');
		    echo "</section>";

		    echo "<section id='options-assets' class='section-content hide'>";
		    	perfmatters_settings_section('perfmatters_options', 'assets');
		    	perfmatters_settings_section('perfmatters_options', 'assets_js');
		    	perfmatters_settings_section('perfmatters_options', 'assets_code');
		    echo "</section>";

		    echo "<section id='options-preload' class='section-content hide'>";
		    	perfmatters_settings_section('perfmatters_options', 'preload');
		    echo "</section>";

		    echo "<section id='options-lazyload' class='section-content hide'>";
		    	perfmatters_settings_section('perfmatters_options', 'lazyload');
		    echo "</section>";

		    echo "<section id='options-fonts' class='section-content hide'>";
		    	perfmatters_settings_section('perfmatters_options', 'perfmatters_fonts');
		    echo "</section>";

		    echo "<section id='options-cdn' class='section-content hide'>";
		    	perfmatters_settings_section('perfmatters_options', 'perfmatters_cdn');
		    echo "</section>";

		    echo "<section id='options-analytics' class='section-content hide'>";
		    	perfmatters_settings_section('perfmatters_options', 'perfmatters_analytics');
		    echo "</section>";

		    submit_button();

		//tools tab
		} elseif($tab == 'tools') {

			//tools subnav
			echo "<input type='hidden' name='section' id='subnav-section' />";
			echo "<div class='perfmatters-subnav'>";
				echo "<a href='#tools-plugin' id='plugin-section' rel='plugin' class='active'><span class='dashicons dashicons-admin-plugins'></span>" . __('Plugin', 'perfmatters') . "</a>";
				echo "<a href='#tools-database' id='database-section' rel='database'><span class='dashicons dashicons-networking'></span>" . __('Database', 'perfmatters') . "</a>";
			echo "</div>";

			settings_fields('perfmatters_tools');

			echo "<section id='tools-plugin' class='section-content active'>";
		    	perfmatters_settings_section('perfmatters_tools', 'plugin');
		    echo "</section>";

		    echo "<section id='tools-database' class='section-content hide'>";
		    	perfmatters_settings_section('perfmatters_tools', 'database');
		    echo "</section>";

		    submit_button();

		    echo "<script>
				jQuery(document).ready(function($) {
					var optimizeSchedule = $('#perfmatters-admin #optimize_schedule');
					var previousValue = $(optimizeSchedule).val();
					$(optimizeSchedule).change(function() {
						var newValue = $(this).val();
						if(newValue && newValue != previousValue) {
							$('#perfmatters-optimize-schedule-warning').show();
						}
						else {
							$('#perfmatters-optimize-schedule-warning').hide();
						}
					});
				});
			</script>";
		}

	echo "</form>";

	if($tab != 'license') {

		//restore defaults button
		echo "<form method='post' action='' id='perfmatters-restore' onsubmit=\"return confirm('" . __('Restore default settings?', 'perfmatters') . "');\">";
			echo "<input type='submit' id='restore' name='restore' class='button button-secondary' value='" . __('Restore Defaults', 'perfmatters') . "'>";
		echo "</form>";

		echo "<script>//display correct tab content based on URL anchor
			var hash = jQuery.trim(window.location.hash);
		    if(hash) {
		    	jQuery('#perfmatters-options-form').attr('action', 'options.php' + hash);

		    	//get displaying tab content jQuery selector
				var active_tab_selector = jQuery('.perfmatters-subnav > a.active').attr('href');				
							
				//find actived navigation and remove 'active' css
				jQuery('.perfmatters-subnav > a.active').removeClass('active');
							
				//add 'active' css into clicked navigation
				jQuery(hash + '-section').addClass('active');
							
				//hide displaying tab content
				jQuery(active_tab_selector).removeClass('active').addClass('hide');
							
				//show target tab content
				var target_tab_selector = jQuery(hash + '-section').attr('href');
				jQuery(target_tab_selector).removeClass('hide').addClass('active');
			}
		</script>";
	}
	else {

		//license custom form output
		require_once('license.php');
	}

echo "</div>";