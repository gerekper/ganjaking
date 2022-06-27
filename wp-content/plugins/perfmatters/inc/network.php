<?php
function perfmatters_network_admin_menu() {

	//Add Network Settings Menu Item
    $perfmatters_network_settings_page = add_submenu_page('settings.php', 'Perfmatters Network Settings', 'Perfmatters', 'manage_network_options', 'perfmatters', 'perfmatters_network_page_callback');

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

//Perfmatters Network Access
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

//Perfmatters Network Default
function perfmatters_network_default_callback() {
	$perfmatters_network = get_site_option('perfmatters_network');

	echo "<select name='perfmatters_network[default]' id='default'>";
		$sites = array_map('get_object_vars', get_sites(array('deleted' => 0)));
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
 
//Perfmatters Network Settings Page
function perfmatters_network_page_callback() {
	if(isset($_POST['perfmatters_apply_defaults'])) {
		check_admin_referer('perfmatters-network-apply');
		if(isset($_POST['perfmatters_network_apply_blog']) && is_numeric($_POST['perfmatters_network_apply_blog'])) {
			$blog = get_blog_details($_POST['perfmatters_network_apply_blog']);
			if($blog) {
				$perfmatters_network = get_site_option('perfmatters_network');

				if(!empty($perfmatters_network['default'])) {

					if($blog->blog_id != $perfmatters_network['default']) {

						$option_names = array(
							'perfmatters_options',
							'perfmatters_tools'
						);

						foreach($option_names as $option_name) {

							//clear selected blog previous option
							delete_blog_option($blog->blog_id, $option_name);

							//grab new option from default blog
							$new_option = get_blog_option($perfmatters_network['default'], $option_name);

							//remove options we don't want to copy
							if($option_name == 'perfmatters_option') {
								unset($new_option['cdn']['cdn_url']);
							}

							//update selected blog with default option
							update_blog_option($blog->blog_id, $option_name, $new_option);

						}

						//Default Settings Updated Notice
						echo "<div class='notice updated is-dismissible'><p>" . __('Default settings applied!', 'perfmatters') . "</p></div>";
					}
					else {
						//Can't Apply to Network Default
						echo "<div class='notice error is-dismissible'><p>" . __('Select a site that is not already the Network Default.', 'perfmatters') . "</p></div>";
					}
				}
				else {
					//Network Default Not Set
					echo "<div class='notice error is-dismissible'><p>" . __('Network Default not set.', 'perfmatters') . "</p></div>";
				}
			}
			else {
				//Blog Not Found Notice
				echo "<div class='notice error is-dismissible'><p>" . __('Error: Blog Not Found.', 'perfmatters') . "</p></div>";
			}
		}
	}
	elseif(isset($_POST['perfmatters_apply_defaults_all'])) {
		check_admin_referer('perfmatters-network-apply');

		$perfmatters_network = get_site_option('perfmatters_network');

		if(!empty($perfmatters_network['default'])) {

			$sites = array_map('get_object_vars', get_sites(array('deleted' => 0)));
			if(is_array($sites) && $sites !== array()) {

				$update_count = 0;

				foreach($sites as $site) {
					$apply = perfmatters_apply_defaults_to_blog($site['blog_id'], $perfmatters_network['default']);
					if($apply) {
						$update_count++;
					}
				}

				if($update_count > 0) {
					//default settings applied
					echo "<div class='notice updated is-dismissible'><p>" . __('Default settings applied!', 'perfmatters') . "</p></div>";
				}
			}
			else {
				//no sites available
				echo "<div class='notice error is-dismissible'><p>" . __('No available sites found.', 'perfmatters') . "</p></div>";
			}
		}
		else {
			//network default not set
			echo "<div class='notice error is-dismissible'><p>" . __('Network Default not set.', 'perfmatters') . "</p></div>";
		}
	}

	//Options Updated
	if(isset($_GET['updated'])) {
		echo "<div class='notice updated is-dismissible'><p>" . __('Options saved.', 'perfmatters') . "</p></div>";
	}

	//if no tab is set, default to first/network tab
	if(empty($_GET['tab'])) {
		$_GET['tab'] = 'network';
	} 

	echo "<div id='perfmatters-admin' class='wrap'>";

		//hidden h2 for admin notice placement
		echo "<h2 style='display: none;'></h2>";

		//Network Tab Content
		if($_GET['tab'] == 'network') {

	  		echo "<form method='POST' action='edit.php?action=perfmatters_update_network_options' style='overflow: hidden;'>";
			    settings_fields('perfmatters_network');
			    perfmatters_settings_section('perfmatters_network', 'perfmatters_network');
			    submit_button();
	  		echo "</form>";

	  		echo "<form method='POST' style='margin-top: 25px;'>";
	  			echo '<div class="perfmatters-settings-section">';
		  			echo "<h2>" . __('Apply Default Settings', 'perfmatters') . "</h2>";

					wp_nonce_field('perfmatters-network-apply', '_wpnonce', true, true);
					echo "<p>" . __('Select a site from the dropdown and click to apply the settings from your network default (above).', 'perfmatters') . "</p>";

					echo "<select name='perfmatters_network_apply_blog' style='margin-right: 10px;'>";
						$sites = array_map('get_object_vars', get_sites(array('deleted' => 0)));
						if(is_array($sites) && $sites !== array()) {
							echo "<option value=''>" . __('Select a Site', 'perfmatters') . "</option>";
							foreach($sites as $site) {
								echo "<option value='" . $site['blog_id'] . "'>" . $site['blog_id'] . ": " . $site['domain'] . $site['path'] . "</option>";
							}
						}
					echo "</select>";

					echo "<input type='submit' name='perfmatters_apply_defaults' value='" . __('Apply Default Settings', 'perfmatters') . "' class='button' />";

					echo "<br />";

					echo "<p>" . __('Apply the settings from your network default to all sites. Depending on the amount, this may take a while.', 'perfmatters') . "</p>";

					echo "<input type='submit' name='perfmatters_apply_defaults_all' value='" . __('Apply Default Settings to All Sites', 'perfmatters') . "' class='button' onclick='return confirm(\"" . __('Are you sure? This will permanently overwrite all Perfmatters options for all subsites.', 'perfmatters') . "\");' />";
				echo '</div>';
			echo "</form>";
		}
		elseif($_GET['tab'] == 'license') {

			//license output
			require_once('license.php');
		}
		elseif($_GET['tab'] == 'support') {

			//support output
			require_once('support.php');
		}

	echo "</div>";
}
 
//Update Perfmatters Network Options
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

function perfmatters_apply_defaults_to_blog($blog_id, $network_default) {

	$blog = get_blog_details($blog_id);

	if($blog && !empty($network_default)) {

		if($blog->blog_id != $network_default) {

			$option_names = array(
				'perfmatters_options',
				'perfmatters_tools'
			);

			foreach($option_names as $option_name) {

				//clear selected blog previous option
				delete_blog_option($blog->blog_id, $option_name);

				//grab new option from default blog
				$new_option = get_blog_option($network_default, $option_name);

				//remove options we don't want to copy
				if($option_name == 'perfmatters_options') {
					unset($new_option['cdn']['cdn_url']);
				}

				//update selected blog with default option
				update_blog_option($blog->blog_id, $option_name, $new_option);
			}
			return true;
		}
	}
	return false;	
}