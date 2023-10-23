<?php
//register network menu and settings
function perfmatters_network_admin_menu() {

	//Add Network Settings Menu Item
    $perfmatters_network_settings_page = add_submenu_page('settings.php', 'Perfmatters Network Settings', 'Perfmatters', 'manage_network_options', 'perfmatters', 'perfmatters_admin');

    //Add Load Action to Enqueue Scripts
	add_action('load-' . $perfmatters_network_settings_page, 'perfmatters_settings_load');

    //Create Site Option if Not Found
    if(get_site_option('perfmatters_network') == false) {    
        add_site_option('perfmatters_network', true);
    }
 
 	//Add Settings Section
    add_settings_section('perfmatters_network', 'Network Setup', '__return_false', 'perfmatters_network');
   
   	//Add Options Fields
	add_settings_field(
		'access', 
		perfmatters_title(__('Network Access', 'perfmatters'), 'access', 'https://perfmatters.io/docs/wordpress-multisite/'),
		'perfmatters_network_access_callback', 
		'perfmatters_network', 
		'perfmatters_network',
		array(
			'tooltip' => __('Choose who has access to manage Perfmatters plugin settings.', 'perfmatters')
		)
	);

	add_settings_field(
		'default', 
		perfmatters_title(__('Network Default', 'perfmatters'), 'default', 'https://perfmatters.io/docs/wordpress-multisite/'),
		'perfmatters_network_default_callback', 
		'perfmatters_network', 
		'perfmatters_network',
		array(
			'tooltip' => __('Choose a subsite that you want to pull default settings from.', 'perfmatters')
		)
	);

	//Clean Uninstall
    add_settings_field(
        'clean_uninstall', 
        perfmatters_title(__('Clean Uninstall', 'perfmatters'), 'clean_uninstall', 'https://perfmatters.io/docs/clean-uninstall/'), 
        'perfmatters_print_input', 
        'perfmatters_network', 
        'perfmatters_network', 
        array(
            'id' => 'clean_uninstall',
            'option' => 'perfmatters_network',
            'tooltip' => __('When enabled, this will cause all Perfmatters options data to be removed from your database when the plugin is uninstalled.', 'perfmatters')
        )
    );

	//Register Setting
	register_setting('perfmatters_network', 'perfmatters_network');
}
add_filter('network_admin_menu', 'perfmatters_network_admin_menu');

//network access callback
function perfmatters_network_access_callback() {
	$perfmatters_network = get_site_option('perfmatters_network');

	echo "<select name='perfmatters_network[access]' id='access'>";
		echo "<option value=''>" . __('Site Admins (Default)', 'perfmatters') . "</option>";
		echo "<option value='super' " . ((!empty($perfmatters_network['access']) && $perfmatters_network['access'] == 'super') ? "selected" : "") . ">" . __('Super Admins Only', 'perfmatters') . "</option>";
	echo "<select>";

	//tooltip
    if(!empty($args['tooltip'])) {
        perfmatters_tooltip($args['tooltip']);
    }
}

//network default callback
function perfmatters_network_default_callback() {
	$perfmatters_network = get_site_option('perfmatters_network');

	echo "<select name='perfmatters_network[default]' id='default'>";
		$sites = array_map('get_object_vars', get_sites(array('deleted' => 0, 'number' => 1000)));
		if(is_array($sites) && $sites !== array()) {
			echo "<option value=''>" . __('None', 'perfmatters') . "</option>";
			foreach($sites as $site) {
				echo "<option value='" . $site['blog_id'] . "' " . ((!empty($perfmatters_network['default']) && $perfmatters_network['default'] == $site['blog_id']) ? "selected" : "") . ">" . $site['blog_id'] . ": " . $site['domain'] . $site['path'] . "</option>";
			}
		}
	echo "<select>";
	
	//tooltip
    if(!empty($args['tooltip'])) {
        perfmatters_tooltip($args['tooltip']);
    }
}

//update perfmatters network options
function perfmatters_update_network_options() {

	//Verify Post Referring Page
  	check_admin_referer('perfmatters_network-options');
 
	//Get Registered Options
	global $new_whitelist_options;
	$options = $new_whitelist_options['perfmatters_network'];

	//Loop Through Registered Options
	foreach($options as $option) {
		if(isset($_POST[$option])) {

			//Update Site Uption
			update_site_option($option, $_POST[$option]);
		}
	}

	//Redirect to Network Settings Page
	wp_redirect(add_query_arg(array('page' => 'perfmatters', 'updated' => 'true'), network_admin_url('settings.php')));

	exit;
}
add_action('network_admin_edit_perfmatters_update_network_options',  'perfmatters_update_network_options');