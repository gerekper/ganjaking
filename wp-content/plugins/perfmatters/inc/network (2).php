<?php
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

		$sites = array_map('get_object_vars', get_sites(array('deleted' => 0, 'number' => 1000)));
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

echo "<form method='POST' action='edit.php?action=perfmatters_update_network_options'>";
	settings_fields('perfmatters_network');
	perfmatters_settings_section('perfmatters_network', 'perfmatters_network', 'dashicons-networking');
	submit_button();
echo "</form>";

echo "<form method='POST' style='margin: 30px auto 10px;'>";
	echo '<div>';
		echo "<h2>" . __('Apply Default Settings', 'perfmatters') . "</h2>";

		wp_nonce_field('perfmatters-network-apply', '_wpnonce', true, true);
		echo "<p>" . __('Select a site from the dropdown and click to apply the settings from your network default (above).', 'perfmatters') . "</p>";

		echo "<select name='perfmatters_network_apply_blog' style='margin-right: 10px;'>";
			$sites = array_map('get_object_vars', get_sites(array('deleted' => 0, 'number' => 1000)));
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

//apply defaults to blog
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