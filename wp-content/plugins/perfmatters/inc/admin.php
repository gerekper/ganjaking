<?php 
//selected tab
$tab = $_GET['tab'] ?? 'options';

$tools = get_option('perfmatters_tools');
if(!empty($tools['accessibility_mode'])) {
	echo '<style>#perfmatters-admin .perfmatters-tooltip-subtext{display: none;}</style>';
}

//settings wrapper
echo '<div id="perfmatters-admin" class="wrap">';

	//hidden h2 for admin notice placement
	echo '<h2 style="display: none;"></h2>';

	//main options tab
	if($tab == 'options') {

		echo '<form method="post" action="options.php" id="perfmatters-options-form">';

			//options subnav
			echo '<input type="hidden" name="section" id="subnav-section" />';
			echo '<div class="perfmatters-subnav">';
				echo '<a href="#options-general" id="general-section" rel="general" class="active"><span class="dashicons dashicons-dashboard"></span>' . __('General', 'perfmatters') . '</a>';
				echo '<a href="#options-assets" id="assets-section" rel="assets"><span class="dashicons dashicons-editor-code"></span>' . __('Assets', 'perfmatters') . '</a>';
				echo '<a href="#options-preload" id="preload-section" rel="preload"><span class="dashicons dashicons-clock"></span>' . __('Preloading', 'perfmatters') . '</a>';
				echo '<a href="#options-lazyload" id="lazyload-section" rel="lazyload"><span class="dashicons dashicons-images-alt2"></span>' . __('Lazy Loading', 'perfmatters') . '</a>';
				echo '<a href="#options-fonts" id="fonts-section" rel="fonts"><span class="dashicons dashicons-editor-paste-text"></span>' . __('Fonts', 'perfmatters') . '</a>';
				echo '<a href="#options-cdn" id="cdn-section" rel="cdn"><span class="dashicons dashicons-admin-site-alt2"></span>' . __('CDN', 'perfmatters') . '</a>';
				echo '<a href="#options-analytics" id="analytics-section" rel="analytics"><span class="dashicons dashicons-chart-bar"></span>' . __('Analytics', 'perfmatters') . '</a>';
			echo '</div>';

			settings_fields('perfmatters_options');

			echo '<section id="options-general" class="section-content active">';
		    	perfmatters_settings_section('perfmatters_options', 'perfmatters_options');
		    	perfmatters_settings_section('perfmatters_options', 'login_url');
		    	perfmatters_settings_section('perfmatters_options', 'perfmatters_woocommerce');
		    echo '</section>';

		    echo '<section id="options-assets" class="section-content">';
		    	perfmatters_settings_section('perfmatters_options', 'assets');
		    	perfmatters_settings_section('perfmatters_options', 'assets_js');
		    	perfmatters_settings_section('perfmatters_options', 'assets_css');
		    	perfmatters_settings_section('perfmatters_options', 'assets_code');
		    echo '</section>';

		    echo '<section id="options-preload" class="section-content">';
		    	perfmatters_settings_section('perfmatters_options', 'preload');
		    echo '</section>';

		    echo '<section id="options-lazyload" class="section-content">';
		    	perfmatters_settings_section('perfmatters_options', 'lazyload');
		    echo '</section>';

		    echo '<section id="options-fonts" class="section-content">';
		    	perfmatters_settings_section('perfmatters_options', 'perfmatters_fonts');
		    echo '</section>';

		    echo '<section id="options-cdn" class="section-content">';
		    	perfmatters_settings_section('perfmatters_options', 'perfmatters_cdn');
		    echo '</section>';

		    echo '<section id="options-analytics" class="section-content">';
		    	perfmatters_settings_section('perfmatters_options', 'perfmatters_analytics');
		    echo '</section>';

		    submit_button();

	    echo '</form>';

	//tools tab
	} elseif($tab == 'tools') {

		echo '<form method="post" action="options.php" enctype="multipart/form-data" id="perfmatters-options-form">';

			//tools subnav
			echo '<input type="hidden" name="section" id="subnav-section" />';
			echo '<div class="perfmatters-subnav">';
				echo '<a href="#tools-plugin" id="plugin-section" rel="plugin" class="active"><span class="dashicons dashicons-admin-plugins"></span>' . __('Plugin', 'perfmatters') . '</a>';
				echo '<a href="#tools-database" id="database-section" rel="database"><span class="dashicons dashicons-networking"></span>' . __('Database', 'perfmatters') . '</a>';
			echo '</div>';

			settings_fields('perfmatters_tools');

			echo '<section id="tools-plugin" class="section-content active">';
		    	perfmatters_settings_section('perfmatters_tools', 'plugin');
		    echo '</section>';

		    echo '<section id="tools-database" class="section-content">';
		    	perfmatters_settings_section('perfmatters_tools', 'database');
		    echo '</section>';

		    submit_button();

		    //optimize schedule warning display
		    echo '<script>jQuery(document).ready(function(e){var i=e("#perfmatters-admin #optimize_schedule"),t=e(i).val();e(i).change(function(){var i=e(this).val();i&&i!=t?e("#perfmatters-optimize-schedule-warning").show():e("#perfmatters-optimize-schedule-warning").hide()})});</script>';

		echo '</form>';
	}
	elseif($tab == 'license') {
		require_once('license.php');
	}
	elseif($tab == 'support') {
		require_once('support.php');
	}

	//display correct section based on URL anchor
	echo '<script>!function(a){var t=a.trim(window.location.hash);if(t){a("#perfmatters-options-form").attr("action","options.php"+t);var e=a(".perfmatters-subnav > a.active");a(e).removeClass("active");var r=a(t+"-section");a(r).addClass("active"),a(a(e).attr("href")).removeClass("active"),a(a(r).attr("href")).addClass("active")}}(jQuery);</script>';

echo "</div>";