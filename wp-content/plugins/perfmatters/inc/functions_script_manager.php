<?php

//actions and filters
if(!empty($perfmatters_options['assets']['script_manager'])) {
	add_action('shutdown', 'perfmatters_script_manager', 0);
	add_action('admin_bar_menu', 'perfmatters_script_manager_admin_bar', 1000);
	add_filter('post_row_actions', 'perfmatters_script_manager_row_actions', 10, 2);
	add_filter('page_row_actions', 'perfmatters_script_manager_row_actions', 10, 2);
	add_action('script_loader_src', 'perfmatters_dequeue_scripts', 1000, 2);
	add_action('style_loader_src', 'perfmatters_dequeue_scripts', 1000, 2);
	add_action('update_option_perfmatters_script_manager_settings', 'perfmatters_script_manager_update_option', 10, 3);
	add_action('add_option_perfmatters_script_manager_settings', 'perfmatters_script_manager_settings_add_option', 10, 2);
	add_action('wp_enqueue_scripts', 'perfmatters_script_manager_scripts');
	add_action('init', 'perfmatters_script_manager_force_admin_bar');
	add_action('wp_ajax_pmsm_save', 'perfmatters_script_manager_update');
	add_action('admin_notices', 'perfmatters_script_manager_mu_notice');
	add_filter('autoptimize_filter_js_exclude', 'perfmatters_script_manager_exclude_autoptimize');
	add_filter('sgo_js_minify_exclude', 'perfmatters_script_manager_exclude_sgo');
}

//script manager front end
function perfmatters_script_manager() {
	include('script_manager.php');
}

//Script Manager Admin Bar Link
function perfmatters_script_manager_admin_bar($wp_admin_bar) {

	//check for proper access
	if(!current_user_can('manage_options') || !perfmatters_network_access() || perfmatters_is_page_builder()) {
		return;
	}

	if(is_admin()) {

		if(function_exists('get_current_screen')) {
			$current_screen = get_current_screen();
			$permalink = get_permalink();
			if($current_screen->base == 'post' && $current_screen->action != 'add' && !empty($permalink)) {

				global $post;

				//get public post types
				$post_types = get_post_types(array('public' => true));

				if(!empty($post->post_type) && in_array($post->post_type, $post_types)) {

					$href = add_query_arg('perfmatters', '', $permalink);
					$menu_text = __('Script Manager', 'perfmatters');
				}
			}
		}
	}
	else {
		global $wp;

		$href = add_query_arg(str_replace(array('&perfmatters', 'perfmatters'), '', $_SERVER['QUERY_STRING']), '', home_url($wp->request));

		if(!isset($_GET['perfmatters'])) {
			$href.= !empty($_SERVER['QUERY_STRING']) ? '&perfmatters' : '?perfmatters';
			$menu_text = __('Script Manager', 'perfmatters');
		}
		else {
			$menu_text = __('Close Script Manager', 'perfmatters');
		}
	}

	//build node and add to admin bar
	if(!empty($menu_text) && !empty($href)) {
		$args = array(
			'id'    => 'perfmatters_script_manager',
			'title' => $menu_text,
			'href'  => $href
		);
		$wp_admin_bar->add_node($args);
	}
}

//script manage links in row actions
function perfmatters_script_manager_row_actions($actions, $post) {

	//check for proper access
	if(!current_user_can('manage_options') || !perfmatters_network_access()) {
		return $actions;
	}

	//get post permalink
	$permalink = get_permalink($post->ID);

	if(!empty($permalink)) {

		//get public post types
		$post_types = get_post_types(array('public' => true));

		if(!empty($post->post_type) && in_array($post->post_type, $post_types)) {

			//add perfmatters query arg
	    	$script_manager_link = add_query_arg('perfmatters', '', $permalink);

	    	//merge link array with existing row actions
		    $actions = array_merge($actions, array(
		        'script_manager' => sprintf('<a href="%1$s">%2$s</a>', esc_url($script_manager_link), __('Script Manager', 'perfmatters'))
		    ));
		}
	}
 
    return $actions;
}

//Script Manager Force Admin Bar
function perfmatters_script_manager_force_admin_bar() {
	if(!function_exists('wp_get_current_user') || !current_user_can('manage_options') || is_admin() || !isset($_GET['perfmatters']) || !perfmatters_network_access() || is_admin_bar_showing()) {
		return;
	}
	add_filter('show_admin_bar', '__return_true' , 9999);
}

//Script Manager Scripts
function perfmatters_script_manager_scripts() {
	if(!current_user_can('manage_options') || is_admin() || !isset($_GET['perfmatters']) || !perfmatters_network_access()) {
		return;
	}

	wp_register_script('perfmatters-script-manager-js', plugins_url('js/script-manager.js', dirname(__FILE__)), array(), PERFMATTERS_VERSION);
	wp_enqueue_script('perfmatters-script-manager-js');

	//pass some data to our js file
	$pmsm = array(
		'currentID' => perfmatters_get_current_ID(),
		'ajaxURL'   => admin_url('admin-ajax.php'),
		'messages'  => array(
			'buttonSave'     => __('Save', 'perfmatters'),
			'buttonSaving'   => __('Saving', 'perfmatters'),
			'updateSuccess'  => __('Settings saved successfully!', 'perfmatters'),
			'updateFailure'  => __('Settings failed to update.', 'perfmatters'),
			'updateNoOption' => __('No disabled location selected.', 'perfmatters'),
			'updateNoChange' => __('No options were changed.', 'perfmatters')
		)
	);
	wp_localize_script('perfmatters-script-manager-js', 'pmsm', $pmsm);
}

//create array of all assets for the script manager
function perfmatters_script_manager_load_master_array() {

	if(!function_exists('get_plugins')) {
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');
	}

	global $wp_scripts;
	global $wp_styles;
	global $perfmatters_script_manager_settings;

	$master_array = array('plugins' => array(), 'themes' => array(), 'misc' => array());

	//mu mode
	if(!empty($perfmatters_script_manager_settings['mu_mode'])) {

		//grab global from mu plugin file
		global $pmsm_active_plugins;

		if(!empty($pmsm_active_plugins)) {

			foreach($pmsm_active_plugins as $key => $path) {

				$explode = explode('/', $path);

				$data = get_plugins("/" . $explode[0]);

				$master_array['plugins'][$explode[0]] = array('name' => $data[key($data)]['Name']);
			}
		}
	}

	$perfmatters_filters = array(
		"js" => array (
			"title" => "JS",
			"scripts" => $wp_scripts
		),
		"css" => array(
			"title" => "CSS",
			"scripts" => $wp_styles
		)
	);

	$loaded_plugins = array();
	$loaded_themes = array();
	$dependencies = array();
	$requires = array();

	foreach($perfmatters_filters as $type => $data) {

		if(!empty($data["scripts"]->done)) {

			$plug_org_scripts = array_unique($data["scripts"]->done);

			foreach($plug_org_scripts as $key => $val) {

				$src = $data['scripts']->registered[$val]->src;

				if(strpos($src, "/wp-content/plugins/") !== false) {
					$explode = explode("/wp-content/plugins/", $src);
					$explode = explode('/', $explode[1]);
					if(!array_key_exists($explode[0], $loaded_plugins)) {
						$file_plugin = get_plugins('/' . $explode[0]);
						if(!empty($file_plugin)) {
							$loaded_plugins[$explode[0]] = $file_plugin;
							$master_array['plugins'][$explode[0]] = array('name' => $file_plugin[key($file_plugin)]['Name']);
						}
					}
					else {
						$file_plugin = $loaded_plugins[$explode[0]];
					}
					$master_reference = &$master_array['plugins'][$explode[0]];
			    }
			    elseif(strpos($src, "/wp-content/themes/") !== false) {
					$explode = explode("/wp-content/themes/", $src);
					$explode = explode('/', $explode[1]);
					if(!array_key_exists($explode[0], $loaded_themes)) {
						$file_theme = wp_get_theme('/' . $explode[0]);
						$loaded_themes[$explode[0]] = $file_theme;
						$master_array['themes'][$explode[0]] = array('name' => $file_theme->get('Name'));
					}
					else {
						$file_theme = $loaded_themes[$explode[0]];
					}
					$master_reference = &$master_array['themes'][$explode[0]];
			    }
			    else {
			    	$master_reference = &$master_array['misc'];
			    }

			    //file size
			    if(!empty($src)) {
			    	$file_path = str_replace('/wp-content', '', WP_CONTENT_DIR) . '/' . strtok(ltrim(str_replace(get_home_url(), '', $src), '/'), '?');
				    $size = file_exists($file_path) ? filesize($file_path) : 0;
				    $master_reference['size'] = (isset($master_reference['size']) ? $master_reference['size'] : 0) + $size;
			    }
			    else {
			    	$size = '';
			    }

				//add asset to array
			    $asset_array = array('type' => $type, 'handle' => $val, 'size' => $size);

			    //dependencies
			    if(!empty($data['scripts']->registered[$val]->deps)) {
			    	$asset_array['deps'] = $data['scripts']->registered[$val]->deps;
			    	$dependencies[$type][$val] = $data['scripts']->registered[$val]->deps;

			    	//sort requires
			    	foreach($data['scripts']->registered[$val]->deps as $key => $handle) {
			    		$requires[$type][$handle][] = $val;
			    	}
			    }

			    $master_reference['assets'][] = $asset_array;

			    unset($master_reference);
			}
		}
	}

	//don't show perfmatters in the list
	if(isset($master_array['plugins']['perfmatters'])) {
		unset($master_array['plugins']['perfmatters']);
	}

	$master_array = array('resources' => $master_array, 'requires' => $requires);

	return $master_array;
}

//print script manager section
function perfmatters_script_manager_print_section($category, $group, $scripts = false) {
	global $perfmatters_script_manager_options;
	global $currentID;
	$options = $perfmatters_script_manager_options;
	$settings = get_option('perfmatters_script_manager_settings');

	$mu_mode = !empty($settings['mu_mode']) && $category == 'plugins';

	$statusDisabled = false;
	if(isset($options['disabled'][$category][$group]['everywhere']) 
		|| (isset($options['disabled'][$category][$group]['current']) && in_array($currentID, $options['disabled'][$category][$group]['current'], TRUE)) 
		|| !empty($options['disabled'][$category][$group]['regex']) 
		|| (!empty($options['disabled'][$category][$group]['404']) && $currentID === 'pmsm-404' && !$mu_mode) 
		|| isset($options['disabled'][$category][$group]['post_types']) 
		|| (!empty($options['disabled'][$category][$group]['archives']) && !$mu_mode) 
		|| !empty($options['disabled'][$category][$group]['user_status']) 
		|| !empty($options['disabled'][$category][$group]['device_type'])
	) {
		$statusDisabled = true;
	}

	echo "<div class='perfmatters-script-manager-section'>";
		if(!empty($scripts)) {
			echo "<table " . ($statusDisabled ? "style='display: none;'" : "") . ">";
				echo "<thead>";
					echo "<tr>";
						echo "<th class='pmsm-column-status'>" . __('Status', 'perfmatters') . "</th>";
						echo "<th>" . __('Script', 'perfmatters') . "</th>";
						echo "<th class='pmsm-column-type'>" . __('Type', 'perfmatters') . "</th>";
						echo "<th class='pmsm-column-size'>" . __('Size', 'perfmatters') . "</th>";
					echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
					foreach($scripts as $key => $details) {
						perfmatters_script_manager_print_script($category, $group, $details);
					}
				echo "</tbody>";
			echo "</table>";
		}

		if($category != "misc") {
			
			echo "<div class='perfmatters-script-manager-assets-disabled' " . (!$statusDisabled ? "style='display: none;'" : "") . ">";
				echo "<div class='perfmatters-script-manager-controls'>";

					//Disable
					perfmatters_script_manager_print_disable($category, $group);

					//Enable
					perfmatters_script_manager_print_enable($category, $group);

				echo "</div>";

				//group disabled message
				if($mu_mode) {
					echo "<p>" . __('MU Mode is currently enabled, the above settings will apply to the entire plugin.', 'perfmatters') . "</p>";
				}
				else {
					echo "<p>" . __('The above settings will apply to all assets in this group.', 'perfmatters') . "</p>";
				}
				
			echo "</div>";
		}
	echo "</div>";
}

//print script manager script
function perfmatters_script_manager_print_script($category, $group, $details) {

	global $perfmatters_tools;
	global $perfmatters_script_manager_settings;
	global $master_array;
	global $perfmatters_filters;
	global $perfmatters_disables;
	global $perfmatters_script_manager_options;
	global $currentID;
	global $statusDisabled;

	$options = $perfmatters_script_manager_options;

	$data = $perfmatters_filters[$details['type']];

	if(empty($data["scripts"]->registered[$details['handle']]->src) && (empty($perfmatters_script_manager_settings['dependencies']) || (empty($data["scripts"]->registered[$details['handle']]->deps) && empty($master_array['requires'][$details['type']][$details['handle']])))) {
		return;
	}

	//Check for disables already set
	if(!empty($perfmatters_disables)) {
		foreach($perfmatters_disables as $key => $val) {
			if(strpos($data["scripts"]->registered[$details['handle']]->src, $val) !== false) {
				return;
			}
		}
	}

	echo "<tr>";	

		//Status
		echo "<td class='perfmatters-script-manager-status'>";

			if(!empty($data["scripts"]->registered[$details['handle']]->src)) {
				perfmatters_script_manager_print_status($details['type'], $details['handle']);
			}

		echo "</td>";

		//Script Cell
		echo "<td class='perfmatters-script-manager-script'>";

			//Script Handle
			echo "<span class='pmsm-script-handle'>" . $details['handle'] . "</span>";

			//script path
			if(!empty($data["scripts"]->registered[$details['handle']]->src)) {
				echo "<a href='" . $data["scripts"]->registered[$details['handle']]->src . "' target='_blank'>" . str_replace(get_home_url(), '', $data["scripts"]->registered[$details['handle']]->src) . "</a>";
			}

			//dependencies
			if(!empty($perfmatters_script_manager_settings['dependencies']) && (!empty($data["scripts"]->registered[$details['handle']]->deps) || !empty($master_array['requires'][$details['type']][$details['handle']]))) {

				echo "<div class='pmsm-dependencies'>";

					if(!empty($data["scripts"]->registered[$details['handle']]->deps)) {
						echo "<div class='pmsm-deps'>";
							echo "<span>" . __('Depends on' , 'perfmatters') . ":</span> ";

							$dep_string = "";
							foreach($data["scripts"]->registered[$details['handle']]->deps as $key => $dep_handle) {
								$dep_string.= $dep_handle . ", ";
							}
							echo rtrim($dep_string, ", ");
						echo "</div>";
					}

					
					if(!empty($master_array['requires'][$details['type']][$details['handle']])) {
						echo "<div class='pmsm-reqs'>";
							echo "<span>" . __('Required by' , 'perfmatters') . ":</span> ";

							$req_string = "";
							foreach($master_array['requires'][$details['type']][$details['handle']] as $key => $req_handle) {
								$req_string.= $req_handle . ", ";
							}
							echo rtrim($req_string, ", ");
						echo "</div>";
					}

				echo "</div>";
			}

			//controls
			if(!empty($data["scripts"]->registered[$details['handle']]->src)) {
				echo "<div class='perfmatters-script-manager-controls' " . (!$statusDisabled ? "style='display: none;'" : "") . ">";

					//disable
					perfmatters_script_manager_print_disable($details['type'], $details['handle']);

					//enable
					perfmatters_script_manager_print_enable($details['type'], $details['handle']);

				echo "</div>";
			}
			
		echo "</td>";

		//Type
		echo "<td class='perfmatters-script-manager-type pmsm-script-type-" . $details['type'] . "'>";
			if(!empty($details['type'])) {
				echo "<span class='pmsm-tag'>" . $details['type'] . "</span>";
			}
		echo "</td>";

		//Size					
		echo "<td class='perfmatters-script-manager-size'>";
			if(!empty($details['size'])) {
				echo round($details['size'] / 1024, 1) . ' KB';
			}
		echo "</td>";

	echo "</tr>";
}

//print status toggle
function perfmatters_script_manager_print_status($type, $handle) {
	global $perfmatters_tools;
	global $perfmatters_script_manager_options;
	global $currentID;
	$options = $perfmatters_script_manager_options;
	$settings = get_option('perfmatters_script_manager_settings');

	$mu_mode = !empty($settings['mu_mode']) && $type == 'plugins';

	global $statusDisabled;
	$statusDisabled = false;

	//get disabled status
	if(isset($options['disabled'][$type][$handle]['everywhere']) 
		|| (isset($options['disabled'][$type][$handle]['current']) && in_array($currentID, $options['disabled'][$type][$handle]['current'], TRUE)) 
		|| !empty($options['disabled'][$type][$handle]['regex']) 
		|| (!empty($options['disabled'][$type][$handle]['404']) && $currentID === 'pmsm-404' && !$mu_mode) 
		|| isset($options['disabled'][$type][$handle]['post_types']) 
		|| (!empty($options['disabled'][$type][$handle]['archives']) && !$mu_mode) 
		|| !empty($options['disabled'][$type][$handle]['user_status']) 
		|| !empty($options['disabled'][$type][$handle]['device_type'])
	) {
		$statusDisabled = true;
	}

	//mu mode label
	if($mu_mode) {
		echo "<span class='pmsm-group-tag pmsm-mu-mode-badge'" . (!$statusDisabled ? " style='display: none;'" : "") . ">" . __('MU Mode', 'perfmatters') . "</span>";
	}

	//print status input
	if(!empty($perfmatters_tools['accessibility_mode']) && $perfmatters_tools['accessibility_mode'] == "1") {
		echo "<select name='pmsm_status[" . $type . "][" . $handle . "]' class='perfmatters-status-select " . ($statusDisabled ? "disabled" : "") . "'>";
			echo "<option value='enabled' class='perfmatters-option-enabled'>" . __('ON', 'perfmatters') . "</option>";
			echo "<option value='disabled' class='perfmatters-option-everywhere' " . ($statusDisabled ? "selected" : "") . ">" . __('OFF', 'perfmatters') . "</option>";
		echo "</select>";
	}
	else {
		echo "<div class='pmsm-checkbox-container'>";
			echo "<input type='hidden' name='pmsm_status[" . $type . "][" . $handle . "]' value='enabled' />";
	        echo "<label for='pmsm_status_" . $type . "_" . $handle . "' class='perfmatters-script-manager-switch'>";
	        	echo "<input type='checkbox' id='pmsm_status_" . $type . "_" . $handle . "' name='pmsm_status[" . $type . "][" . $handle . "]' value='disabled' " . ($statusDisabled ? "checked" : "") . " class='perfmatters-status-toggle'>";
	        	echo "<div class='perfmatters-script-manager-slider'></div>";
	       	echo "</label>";
	    echo "</div>";
	}
}

//print disable options
function perfmatters_script_manager_print_disable($type, $handle) {
	global $perfmatters_script_manager_settings;
	global $perfmatters_script_manager_options;
	global $currentID;
	$options = $perfmatters_script_manager_options;

	$pmsm_hide = !empty($options['disabled'][$type][$handle]['everywhere']) ? ' pmsm-hide' : '';

	echo "<div class='perfmatters-script-manager-disable'>";
		echo "<div style='font-size: 16px;'>" . __('Disabled', 'perfmatters') . "</div>";

		//locations
		echo "<div class='pmsm-input-group'>";
			echo "<span class='pmsm-input-group-label'>Locations:</span>";
			echo "<div class='pmsm-input-group-container'>";

				//everywhere
				echo "<div class='pmsm-checkbox-container'>";
					echo "<input type='hidden' name='pmsm_disabled[" . $type . "][" . $handle . "][everywhere]' value='' />";
					echo "<label for='" . $type . "-" . $handle . "-disable-everywhere'>";
						echo "<input type='checkbox' name='pmsm_disabled[" . $type . "][" . $handle . "][everywhere]' id='" . $type . "-" . $handle . "-disable-everywhere' class='perfmatters-disable-select pmsm-disable-everywhere' value='1' ";
						echo (!empty($options['disabled'][$type][$handle]['everywhere']) ? "checked" : "");
						echo " />";
						echo __('Everywhere', 'perfmatters');
					echo "</label>";
				echo "</div>";

				//id is available
				if(!empty($currentID) || $currentID === 0) {
					echo "<div class='pmsm-checkbox-container pmsm-everywhere-hide" . $pmsm_hide . "'>";

						//404 template
						if($currentID === "pmsm-404") {
							if(empty($perfmatters_script_manager_settings['mu_mode']) || $type != 'plugins') {
								echo "<input type='hidden' name='pmsm_disabled[" . $type . "][" . $handle . "][404]' value='' />";
								echo "<label for='" . $type . "-" . $handle . "-disable-404'>";
									echo "<input type='checkbox' name='pmsm_disabled[" . $type . "][" . $handle . "][404]' id='" . $type . "-" . $handle . "-disable-404' value='404' ";
										if(!empty($options['disabled'][$type][$handle]['404'])) {
											echo "checked";
										}
									echo " />";
									echo __("404 Template", 'perfmatters');
								echo "</label>";
							}
						}
						//current url
						else {
							echo "<input type='hidden' name='pmsm_disabled[" . $type . "][" . $handle . "][current]' value='' />";
							echo "<label for='" . $type . "-" . $handle . "-disable-current'>";
								echo "<input type='checkbox' name='pmsm_disabled[" . $type . "][" . $handle . "][current]' id='" . $type . "-" . $handle . "-disable-current' value='" . $currentID ."' ";
									if(isset($options['disabled'][$type][$handle]['current'])) {
										if(in_array($currentID, $options['disabled'][$type][$handle]['current'])) {
											echo "checked";
										}
									}
								echo " />";
								echo __("Current URL", 'perfmatters');
							echo "</label>";
						}

					echo "</div>";
				}

				//post types
				$post_types = get_post_types(array('public' => true), 'objects', 'and');
				if(!empty($post_types)) {
					if(isset($post_types['attachment'])) {
						unset($post_types['attachment']);
					}
					echo "<div class='pmsm-checkbox-container pmsm-everywhere-hide" . $pmsm_hide . "'>";
						echo "<input type='hidden' name='pmsm_disabled[" . $type . "][" . $handle . "][post_types]' value='' />";
						foreach($post_types as $key => $value) {
							echo "<label for='" . $type . "-" . $handle . "-disabled-" . $key . "' title='" . $key . " (Post Type)'>";
								echo "<input type='checkbox' name='pmsm_disabled[" . $type . "][" . $handle . "][post_types][]' id='" . $type . "-" . $handle . "-disabled-" . $key . "' value='" . $key ."' ";
									if(isset($options['disabled'][$type][$handle]['post_types'])) {
										if(in_array($key, $options['disabled'][$type][$handle]['post_types'])) {
											echo "checked";
										}
									}
								echo " />" . $value->label;
							echo "</label>";
						}
					echo "</div>";
				}

				//archives
				if(!empty($perfmatters_script_manager_settings['separate_archives']) && (empty($perfmatters_script_manager_settings['mu_mode']) || $type != 'plugins')) {
					echo "<div class='pmsm-checkbox-container pmsm-everywhere-hide" . $pmsm_hide . "'>";
						echo "<input type='hidden' name='pmsm_disabled[" . $type . "][" . $handle . "][archives]' value='' />";

						//built-in tax archives
						$wp_archives = array('category' => 'Categories', 'post_tag' => 'Tags', 'author' => 'Authors');
						foreach($wp_archives as $key => $value) {
							echo "<label for='" . $type . "-" . $handle . "-disable-archive-" . $key . "' title='" . $key . " (WordPress Taxonomy Archive)'>";
								echo "<input type='checkbox' name='pmsm_disabled[" . $type . "][" . $handle . "][archives][]' id='" . $type . "-" . $handle . "-disable-archive-" . $key . "' value='" . $key ."' ";
									if(isset($options['disabled'][$type][$handle]['archives'])) {
										if(in_array($key, $options['disabled'][$type][$handle]['archives'])) {
											echo "checked";
										}
									}
								echo " />" . $value;
							echo "</label>";
						}

						//custom tax archives
						$taxonomies = get_taxonomies(array('public' => true, '_builtin' => false), 'objects', 'and');
						if(!empty($taxonomies)) {
							foreach($taxonomies as $key => $value) {
								echo "<label for='" . $type . "-" . $handle . "-disable-archive-" . $key . "' title='" . $key . " (Custom Taxonomy Archive)'>";
									echo "<input type='checkbox' name='pmsm_disabled[" . $type . "][" . $handle . "][archives][]' id='" . $type . "-" . $handle . "-disable-archive-" . $key . "' value='" . $key ."' ";
										if(isset($options['disabled'][$type][$handle]['archives'])) {
											if(in_array($key, $options['disabled'][$type][$handle]['archives'])) {
												echo "checked";
											}
										}
									echo " />" . $value->label;
								echo "</label>";
							}
						}

						//post type archives
						$archive_post_types = get_post_types(array('public' => true, 'has_archive' => true), 'objects', 'and');
						if(!empty($archive_post_types)) {
							foreach($archive_post_types as $key => $value) {
								echo "<label for='" . $type . "-" . $handle . "-disable-archive-" . $key . "' title='" . $key . " (Post Type Archive)'>";
									echo "<input type='checkbox' name='pmsm_disabled[" . $type . "][" . $handle . "][archives][]' id='" . $type . "-" . $handle . "-disable-archive-" . $key . "' value='" . $key ."' ";
										if(isset($options['disabled'][$type][$handle]['archives'])) {
											if(in_array($key, $options['disabled'][$type][$handle]['archives'])) {
												echo "checked";
											}
										}
									echo " />" . $value->label;
								echo "</label>";
							}
						}
					echo "</div>";
				}

			echo "</div>";
		echo "</div>";

		//users
		echo "<div class='pmsm-input-group pmsm-everywhere-hide" . $pmsm_hide . "'>";
			echo "<label for='" . $type . "-" . $handle . "-enable-user-status-value' style='width: 100%;'>";
				echo "<span class='pmsm-input-group-label'>" . __('Users', 'perfmatters') . ":</span>";
				echo "<select name='pmsm_disabled[" . $type . "][" . $handle . "][user_status]' id='" . $type . "-" . $handle . "-enable-user-status-value'>";
					echo "<option value=''>" . __('Default', 'perfmatters') . "</option>";
					echo "<option value='loggedin'" . (!empty($options['disabled'][$type][$handle]['user_status']) && $options['disabled'][$type][$handle]['user_status'] == 'loggedin' ? " selected" : "") . ">" . __('Logged In', 'perfmatters') . "</option>";
					echo "<option value='loggedout'" . (!empty($options['disabled'][$type][$handle]['user_status']) && $options['disabled'][$type][$handle]['user_status'] == 'loggedout' ? " selected" : "") . ">" . __('Logged Out', 'perfmatters') . "</option>";
				echo "</select>";
			echo "</label>";
		echo "</div>";

		//devices
		echo "<div class='pmsm-input-group pmsm-everywhere-hide" . $pmsm_hide . "'>";
			echo "<label for='" . $type . "-" . $handle . "-enable-device-type-value' style='width: 100%;'>";
				echo "<span class='pmsm-input-group-label'>" . __('Devices', 'perfmatters') . ":</span>";
				echo "<select name='pmsm_disabled[" . $type . "][" . $handle . "][device_type]' id='" . $type . "-" . $handle . "-enable-device-type-value'>";
					echo "<option value=''>" . __('Default', 'perfmatters') . "</option>";
					echo "<option value='desktop'" . (!empty($options['disabled'][$type][$handle]['device_type']) && $options['disabled'][$type][$handle]['device_type'] == 'desktop' ? " selected" : "") . ">" . __('Desktop', 'perfmatters') . "</option>";
					echo "<option value='mobile'" . (!empty($options['disabled'][$type][$handle]['device_type']) && $options['disabled'][$type][$handle]['device_type'] == 'mobile' ? " selected" : "") . ">" . __('Mobile', 'perfmatters') . "</option>";
				echo "</select>";
			echo "</label>";
		echo "</div>";

		//regex
		echo "<div class='pmsm-input-group pmsm-disable-regex pmsm-everywhere-hide" . $pmsm_hide . "'>";
			echo "<label for='pmsm_disabled-" . $type . "-" . $handle . "-regex-value' style='width: 100%;'>";
				echo "<span class='pmsm-input-group-label'>" . __('Regex', 'perfmatters') . ":</span>";
				echo "<input type='text' name='pmsm_disabled[" . $type . "][" . $handle . "][regex]' id='pmsm_disabled-" . $type . "-" . $handle . "-regex-value' value='" . (!empty($options['disabled'][$type][$handle]['regex']) ? esc_attr($options['disabled'][$type][$handle]['regex']) : "") . "' />";
			echo "</label>";
		echo "</div>";

	echo "</div>";
}

//print enable options
function perfmatters_script_manager_print_enable($type, $handle) {
	global $perfmatters_script_manager_settings;
	global $perfmatters_script_manager_options;
	global $currentID;

	$options = $perfmatters_script_manager_options;

	echo "<div class='perfmatters-script-manager-enable'"; if(empty($options['disabled'][$type][$handle]['everywhere'])) { echo " style='display: none;'"; } echo">";

		echo "<div style='font-size: 16px;'>" . __('Exceptions', 'perfmatters') . "</div>";

		//locations
		echo "<div class='pmsm-input-group'>";
			echo "<span class='pmsm-input-group-label'>Locations:</span>";
			echo "<div class='pmsm-input-group-container'>";

				//Current URL
				if(!empty($currentID) || $currentID === 0) {
					echo "<div class='pmsm-checkbox-container'>";

						//404 check
						if($currentID === "pmsm-404") {
							if(empty($perfmatters_script_manager_settings['mu_mode']) || $type != 'plugins') {
								echo "<input type='hidden' name='pmsm_enabled[" . $type . "][" . $handle . "][404]' value='' />";
								echo "<label for='" . $type . "-" . $handle . "-enable-404'>";
									echo "<input type='checkbox' name='pmsm_enabled[" . $type . "][" . $handle . "][404]' id='" . $type . "-" . $handle . "-enable-404' value='404' ";
										if(!empty($options['enabled'][$type][$handle]['404'])) {
											echo "checked";
										}
									echo " />";
									echo __("404 Template", 'perfmatters');
								echo "</label>";
							}
						}
						else {
							echo "<input type='hidden' name='pmsm_enabled[" . $type . "][" . $handle . "][current]' value='' />";
							echo "<label for='" . $type . "-" . $handle . "-enable-current'>";
								echo "<input type='checkbox' name='pmsm_enabled[" . $type . "][" . $handle . "][current]' id='" . $type . "-" . $handle . "-enable-current' value='" . $currentID ."' ";
									if(isset($options['enabled'][$type][$handle]['current'])) {
										if(in_array($currentID, $options['enabled'][$type][$handle]['current'])) {
											echo "checked";
										}
									}
								echo " />";
								echo __("Current URL", 'perfmatters');
							echo "</label>";
						}

					echo "</div>";
				}

				//Post Types
				$post_types = get_post_types(array('public' => true), 'objects', 'and');
				if(!empty($post_types)) {
					if(isset($post_types['attachment'])) {
						unset($post_types['attachment']);
					}
					echo "<div class='pmsm-checkbox-container'>";
						echo "<input type='hidden' name='pmsm_enabled[" . $type . "][" . $handle . "][post_types]' value='' />";
						foreach($post_types as $key => $value) {
							echo "<label for='" . $type . "-" . $handle . "-enable-" . $key . "' title='" . $key . " (Post Type)'>";
								echo "<input type='checkbox' name='pmsm_enabled[" . $type . "][" . $handle . "][post_types][]' id='" . $type . "-" . $handle . "-enable-" . $key . "' value='" . $key ."' ";
									if(isset($options['enabled'][$type][$handle]['post_types'])) {
										if(in_array($key, $options['enabled'][$type][$handle]['post_types'])) {
											echo "checked";
										}
									}
								echo " />" . $value->label;
							echo "</label>";
						}
					echo "</div>";
				}

				//Archives
				if(!empty($perfmatters_script_manager_settings['separate_archives']) && (empty($perfmatters_script_manager_settings['mu_mode']) || $type != 'plugins')) {
					echo "<div class='pmsm-checkbox-container'>";
						echo "<input type='hidden' name='pmsm_enabled[" . $type . "][" . $handle . "][archives]' value='' />";

						//Built-In Tax Archives
						$wp_archives = array('category' => 'Categories', 'post_tag' => 'Tags', 'author' => 'Authors');
						foreach($wp_archives as $key => $value) {
							echo "<label for='" . $type . "-" . $handle . "-enable-archive-" . $key . "' title='" . $key . " (WordPress Taxonomy Archive)'>";
								echo "<input type='checkbox' name='pmsm_enabled[" . $type . "][" . $handle . "][archives][]' id='" . $type . "-" . $handle . "-enable-archive-" . $key . "' value='" . $key ."' ";
									if(isset($options['enabled'][$type][$handle]['archives'])) {
										if(in_array($key, $options['enabled'][$type][$handle]['archives'])) {
											echo "checked";
										}
									}
								echo " />" . $value;
							echo "</label>";
						}

						//Custom Tax Archives
						$taxonomies = get_taxonomies(array('public' => true, '_builtin' => false), 'objects', 'and');
						if(!empty($taxonomies)) {
							foreach($taxonomies as $key => $value) {
								echo "<label for='" . $type . "-" . $handle . "-enable-archive-" . $key . "' title='" . $key . " (Custom Taxonomy Archive)'>";
									echo "<input type='checkbox' name='pmsm_enabled[" . $type . "][" . $handle . "][archives][]' id='" . $type . "-" . $handle . "-enable-archive-" . $key . "' value='" . $key ."' ";
										if(isset($options['enabled'][$type][$handle]['archives'])) {
											if(in_array($key, $options['enabled'][$type][$handle]['archives'])) {
												echo "checked";
											}
										}
									echo " />" . $value->label;
								echo "</label>";
							}
						}

						//Post Type Archives
						$archive_post_types = get_post_types(array('public' => true, 'has_archive' => true), 'objects', 'and');
						if(!empty($archive_post_types)) {
							foreach($archive_post_types as $key => $value) {
								echo "<label for='" . $type . "-" . $handle . "-enable-archive-" . $key . "' title='" . $key . " (Post Type Archive)'>";
									echo "<input type='checkbox' name='pmsm_enabled[" . $type . "][" . $handle . "][archives][]' id='" . $type . "-" . $handle . "-enable-archive-" . $key . "' value='" . $key ."' ";
										if(isset($options['enabled'][$type][$handle]['archives'])) {
											if(in_array($key, $options['enabled'][$type][$handle]['archives'])) {
												echo "checked";
											}
										}
									echo " />" . $value->label;
								echo "</label>";
							}
						}
					echo "</div>";
				}

			echo "</div>";
		echo "</div>";

		//users
		echo "<div class='pmsm-input-group'>";
			echo "<label for='" . $type . "-" . $handle . "-enable-user-status-value' style='width: 100%;'>";
				echo "<span class='pmsm-input-group-label'>" . __('Users', 'perfmatters') . ":</span>";
				echo "<select name='pmsm_enabled[" . $type . "][" . $handle . "][user_status]' id='" . $type . "-" . $handle . "-enable-user-status-value'>";
					echo "<option value=''>" . __('Default', 'perfmatters') . "</option>";
					echo "<option value='loggedin'" . (!empty($options['enabled'][$type][$handle]['user_status']) && $options['enabled'][$type][$handle]['user_status'] == 'loggedin' ? " selected" : "") . ">" . __('Logged In', 'perfmatters') . "</option>";
					echo "<option value='loggedout'" . (!empty($options['enabled'][$type][$handle]['user_status']) && $options['enabled'][$type][$handle]['user_status'] == 'loggedout' ? " selected" : "") . ">" . __('Logged Out', 'perfmatters') . "</option>";
				echo "</select>";
			echo "</label>";
		echo "</div>";

		//devices
		echo "<div class='pmsm-input-group'>";
			echo "<label for='" . $type . "-" . $handle . "-enable-device-type-value' style='width: 100%;'>";
				echo "<span class='pmsm-input-group-label'>" . __('Devices', 'perfmatters') . ":</span>";
				echo "<select name='pmsm_enabled[" . $type . "][" . $handle . "][device_type]' id='" . $type . "-" . $handle . "-enable-device-type-value'>";
					echo "<option value=''>" . __('Default', 'perfmatters') . "</option>";
					echo "<option value='desktop'" . (!empty($options['enabled'][$type][$handle]['device_type']) && $options['enabled'][$type][$handle]['device_type'] == 'desktop' ? " selected" : "") . ">" . __('Desktop', 'perfmatters') . "</option>";
					echo "<option value='mobile'" . (!empty($options['enabled'][$type][$handle]['device_type']) && $options['enabled'][$type][$handle]['device_type'] == 'mobile' ? " selected" : "") . ">" . __('Mobile', 'perfmatters') . "</option>";
				echo "</select>";
			echo "</label>";
		echo "</div>";

		//Regex
		echo "<div class='pmsm-input-group pmsm-enable-regex'>";
			echo "<label for='" . $type . "-" . $handle . "-enable-regex-value' style='width: 100%;'>";
				echo "<span class='pmsm-input-group-label'>" . __('Regex', 'perfmatters') . ":</span>";
				echo "<input type='text' name='pmsm_enabled[" . $type . "][" . $handle . "][regex]' id='" . $type . "-" . $handle . "-enable-regex-value' value='" . (!empty($options['enabled'][$type][$handle]['regex']) ? esc_attr($options['enabled'][$type][$handle]['regex']) : "") . "' />";
			echo "</label>";
		echo "</div>";

	echo "</div>";
}

//script manager update funciton triggered by ajax call
function perfmatters_script_manager_update() {

	if(!empty($_POST['pmsm_data'])) {

		//parse the data
		$pmsm_data = array();
		parse_str($_POST['pmsm_data'], $pmsm_data);

		//grab current ID
		if(isset($_POST['current_id'])) {
			if($_POST['current_id'] === 'pmsm-404') {
				$currentID = $_POST['current_id'];
			}
			else {
				$currentID = (int)$_POST['current_id'];
			}
		}
		else {
			$currentID = "";
		}

		//get script manager settings
		$settings = get_option('perfmatters_script_manager_settings');

		//get existing script manager options
		$options = get_option('perfmatters_script_manager');

		//clone saved options for later
		$options_old = $options;

		//setup filters to walk through
		$perfmatters_filters = array("js", "css", "plugins", "themes");

		foreach($perfmatters_filters as $type) {

			//check status array
			if(isset($pmsm_data['pmsm_status'][$type])) {
				foreach($pmsm_data['pmsm_status'][$type] as $handle => $status) {

					//status toggle was enabled
					if($status == 'enabled') {

						//remove current url disable
						if(isset($options['disabled'][$type][$handle]['current'])) {
							$current_key = array_search($currentID, $options['disabled'][$type][$handle]['current']);
							if($current_key !== false) {
								unset($options['disabled'][$type][$handle]['current'][$current_key]);
							}
						}

						//remove current url exception
						if(isset($options['enabled'][$type][$handle]['current'])) {
							$current_key = array_search($currentID, $options['enabled'][$type][$handle]['current']);
							if($current_key !== false) {
								unset($options['enabled'][$type][$handle]['current'][$current_key]);
							}
						}

						//remove disables
						if(isset($options['disabled'][$type][$handle])) {
							unset($options['disabled'][$type][$handle]['everywhere']);
							unset($options['disabled'][$type][$handle]['post_types']);
							unset($options['disabled'][$type][$handle]['archives']);
							unset($options['disabled'][$type][$handle]['user_status']);
							unset($options['disabled'][$type][$handle]['device_type']);
							unset($options['disabled'][$type][$handle]['regex']);
							if($currentID === 'pmsm-404') {
								unset($options['disabled'][$type][$handle]['404']);
							}
						}

						//remove exceptions
						if(isset($options['enabled'][$type][$handle])) {
							unset($options['enabled'][$type][$handle]['post_types']);
							unset($options['enabled'][$type][$handle]['archives']);
							unset($options['enabled'][$type][$handle]['user_status']);
							unset($options['enabled'][$type][$handle]['device_type']);
							unset($options['enabled'][$type][$handle]['regex']);
							if($currentID === 'pmsm-404') {
								unset($options['enabled'][$type][$handle]['404']);
							}
						}
					}
				}
			}

			//check disabled array
			if(isset($pmsm_data['pmsm_disabled'][$type])) {
				foreach($pmsm_data['pmsm_disabled'][$type] as $handle => $value) {

					$disabled_trash = array();

					//make sure status is disabled and we have a value to set
					if((empty($pmsm_data['pmsm_status'][$type][$handle]) || $pmsm_data['pmsm_status'][$type][$handle] != 'enabled') && !empty($value)) {

						if(!empty($value['everywhere'])) {
							$options['disabled'][$type][$handle]['everywhere'] = 1;
							$disabled_trash = array('current', 'regex', '404', 'post_types', 'archives', 'user_status', 'device_type');
						}
						else {

							if(isset($value['everywhere'])) {
								$disabled_trash = array('everywhere');
								unset($options['enabled'][$type][$handle]);
								unset($pmsm_data['pmsm_enabled'][$type][$handle]);
							}

							if(isset($value['current'])) {
								if(!empty($value['current']) || $value['current'] === "0") {
									if(!isset($options['disabled'][$type][$handle]['current']) || !is_array($options['disabled'][$type][$handle]['current'])) {
										$options['disabled'][$type][$handle]['current'] = array();
									}
									if(!in_array($value['current'], $options['disabled'][$type][$handle]['current'], TRUE)) {
										array_push($options['disabled'][$type][$handle]['current'], $currentID);
									}
								}
								else {
									if(isset($options['disabled'][$type][$handle]['current'])) {
										$current_key = array_search($currentID, $options['disabled'][$type][$handle]['current']);
										if($current_key !== false) {
											unset($options['disabled'][$type][$handle]['current'][$current_key]);
										}
									}
								}
							}

							if(isset($value['404'])) {
								if(!empty($value['404'])) {
									$options['disabled'][$type][$handle]['404'] = 1;
								}
								else {
									unset($options['disabled'][$type][$handle]['404']);
								}
							}

							//set post type disable
							if(isset($value['post_types'])) {
								if(!empty($value['post_types'])) {
									$options['disabled'][$type][$handle]['post_types'] = array();
									foreach($value['post_types'] as $key => $post_type) {
										if(isset($options['disabled'][$type][$handle]['post_types'])) {
											if(!in_array($post_type, $options['disabled'][$type][$handle]['post_types'])) {
												array_push($options['disabled'][$type][$handle]['post_types'], $post_type);
											}
										}
									}
								}
								else {
									unset($options['disabled'][$type][$handle]['post_types']);
								}
							}

							//set archives disable
							if(!empty($settings['separate_archives']) && $settings['separate_archives'] == "1") {
								if(isset($value['archives'])) {
									if(is_array($value['archives'])) {
										$value['archives'] = array_filter($value['archives']);
									}
									if(!empty($value['archives'])) {
										$options['disabled'][$type][$handle]['archives'] = array();
										foreach($value['archives'] as $key => $archive) {
											if(!in_array($archive, $options['disabled'][$type][$handle]['archives'])) {
												array_push($options['disabled'][$type][$handle]['archives'], $archive);
											}
										}
									}
									else {
										unset($options['disabled'][$type][$handle]['archives']);
									}
								}
							}

							//set user status disable
							if(isset($value['user_status'])) {
								if(!empty($value['user_status'])) {
									$options['disabled'][$type][$handle]['user_status'] = $value['user_status'];
								}
								else {
									unset($options['disabled'][$type][$handle]['user_status']);
								}
							}

							//set device type disable
							if(isset($value['device_type'])) {
								if(!empty($value['device_type'])) {
									$options['disabled'][$type][$handle]['device_type'] = $value['device_type'];
								}
								else {
									unset($options['disabled'][$type][$handle]['device_type']);
								}
							}

							//set regex disable
							if(isset($value['regex'])) {
								if(!empty($value['regex'])) {
									$options['disabled'][$type][$handle]['regex'] = $value['regex'];
								}
								else {
									unset($options['disabled'][$type][$handle]['regex']);
								}
							}
						}
					}

					//empty disabled trash
					if(!empty($disabled_trash) && isset($options['disabled'][$type][$handle])) {
						foreach($disabled_trash as $trash) {
							unset($options['disabled'][$type][$handle][$trash]);
						}
					}
				}
			}

			//check enabled array
			if(isset($pmsm_data['pmsm_enabled'][$type])) {
				foreach($pmsm_data['pmsm_enabled'][$type] as $handle => $value) {

					//make sure status is disabled and we have a value to set
					if((empty($pmsm_data['pmsm_status'][$type][$handle]) || $pmsm_data['pmsm_status'][$type][$handle] != 'enabled') && !empty($value)) {

						//set current url exception
						if(isset($value['current'])) {
							if(!empty($value['current']) || $value['current'] === "0") {
								if(!isset($options['enabled'][$type][$handle]['current']) || !is_array($options['enabled'][$type][$handle]['current'])) {
									$options['enabled'][$type][$handle]['current'] = array();
								}
								if(!in_array($value['current'], $options['enabled'][$type][$handle]['current'], TRUE)) {
									array_push($options['enabled'][$type][$handle]['current'], $value['current']);
								}
							}
							else {
								if(isset($options['enabled'][$type][$handle]['current'])) {
									$current_key = array_search($currentID, $options['enabled'][$type][$handle]['current']);
									if($current_key !== false) {
										unset($options['enabled'][$type][$handle]['current'][$current_key]);
									}
								}
							}
						}

						//set 404 exception
						if(isset($value['404'])) {
							if(!empty($value['404'])) {
								$options['enabled'][$type][$handle]['404'] = 1;
							}
							else {
								unset($options['enabled'][$type][$handle]['404']);
							}
						}

						//set post types exception
						if(isset($value['post_types'])) {
							if(!empty($value['post_types'])) {
								$options['enabled'][$type][$handle]['post_types'] = array();
								foreach($value['post_types'] as $key => $post_type) {
									if(isset($options['enabled'][$type][$handle]['post_types'])) {
										if(!in_array($post_type, $options['enabled'][$type][$handle]['post_types'])) {
											array_push($options['enabled'][$type][$handle]['post_types'], $post_type);
										}
									}
								}
							}
							else {
								unset($options['enabled'][$type][$handle]['post_types']);
							}
						}

						//set archives exception
						if(!empty($settings['separate_archives']) && $settings['separate_archives'] == "1") {
							if(isset($value['archives'])) {
								if(is_array($value['archives'])) {
									$value['archives'] = array_filter($value['archives']);
								}
								if(!empty($value['archives'])) {
									$options['enabled'][$type][$handle]['archives'] = array();
									foreach($value['archives'] as $key => $archive) {
										if(!in_array($archive, $options['enabled'][$type][$handle]['archives'])) {
											array_push($options['enabled'][$type][$handle]['archives'], $archive);
										}
									}
								}
								else {
									unset($options['enabled'][$type][$handle]['archives']);
								}
							}
						}

						//set user status exception
						if(isset($value['user_status'])) {
							if(!empty($value['user_status'])) {
								$options['enabled'][$type][$handle]['user_status'] = $value['user_status'];
							}
							else {
								unset($options['enabled'][$type][$handle]['user_status']);
							}
						}

						//set device type exception
						if(isset($value['device_type'])) {
							if(!empty($value['device_type'])) {
								$options['enabled'][$type][$handle]['device_type'] = $value['device_type'];
							}
							else {
								unset($options['enabled'][$type][$handle]['device_type']);
							}
						}

						//set regex exception
						if(isset($value['regex'])) {
							if(!empty($value['regex'])) {
								$options['enabled'][$type][$handle]['regex'] = $value['regex'];
							}
							else {
								unset($options['enabled'][$type][$handle]['regex']);
							}
						}
					}
				}
			}
		}

		//clean up the options array before saving
		perfmatters_script_manager_filter_options($options);

		if(update_option('perfmatters_script_manager', $options)) {
			echo 'update_success';
		}
		elseif($options == $options_old) {
			echo 'update_nooption';
		}
		else {
			echo 'update_failure';
		}
	}
	else {
		echo 'update_nochange';
	}
	wp_die();
}

function perfmatters_script_manager_filter_options(&$options) {
	foreach($options as $key => $item) {
        is_array($item) && $options[$key] = perfmatters_script_manager_filter_options($item);
        if(empty($options[$key]) && $options[$key] != 0) {
        	unset($options[$key]);
        }
    }
    return $options;
}

//after script manager settings option update
function perfmatters_script_manager_update_option($old_value, $value, $option) {
	pmsm_settings_update_process($old_value, $value);
}

//after script manager settings option add
function perfmatters_script_manager_settings_add_option($option, $value) {
	pmsm_settings_update_process('', $value);
}

//process settings update
function pmsm_settings_update_process($old_value, $value) {

	//trigger success popup message
	add_action('shutdown', function() {
		echo "<script>pmsmPopupMessage('" . __('Settings saved successfully!', 'perfmatters') . "');</script>";    
	}, 9999);

	//mu mode was enabled
	if(!empty($value['mu_mode']) && empty($old_value['mu_mode'])) {

		$mu_version_match = false;

		//make sure mu directory exists
		if(!file_exists(WPMU_PLUGIN_DIR)) {
			@mkdir(WPMU_PLUGIN_DIR);
		}

		//remove existing mu plugin file
		if(file_exists(WPMU_PLUGIN_DIR . "/perfmatters_mu.php")) {

			if(!function_exists('get_plugin_data')) {
		        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		    }

		    //get plugin data
		    $mu_plugin_data = get_plugin_data(WPMU_PLUGIN_DIR . "/perfmatters_mu.php");

			if(!empty($mu_plugin_data['Version']) && defined('PERFMATTERS_VERSION') && $mu_plugin_data['Version'] == PERFMATTERS_VERSION) {
				$mu_version_match = true;
			}
			else {
				@unlink(WPMU_PLUGIN_DIR . "/perfmatters_mu.php");
			}
		}
		
		//copy current mu plugin file
		if(file_exists(plugin_dir_path(__FILE__) . "/perfmatters_mu.php") && !$mu_version_match) {
			@copy(plugin_dir_path(__FILE__) . "/perfmatters_mu.php", WPMU_PLUGIN_DIR . "/perfmatters_mu.php");
		}
	}
}

//dequeue scripts based on script manager configuration
function perfmatters_dequeue_scripts($src, $handle) {
	
	if(is_admin() || isset($_GET['perfmatters']) || perfmatters_is_page_builder()) {
		return $src;
	}

	//load settings
	$settings = get_option('perfmatters_script_manager_settings');

	if(!empty($settings['testing_mode']) && !current_user_can('manage_options')) {
		return $src;
	}

	//get script type
	$type = current_filter() == 'script_loader_src' ? "js" : "css";

	//load options
	$options = get_option('perfmatters_script_manager');
	$currentID = perfmatters_get_current_ID();

	//get category + group from src
	preg_match('/\/wp-content\/(.*?\/.*?)\//', $src, $match);
	if(!empty($match[1])) {
		$match = explode("/", $match[1]);
		$category = $match[0];
		$group = $match[1];
	}

	//check for group disable settings and override
	if(!empty($category) && !empty($group) && !empty($options['disabled'][$category][$group])) {
		if(!empty($options['disabled'][$category][$group]['everywhere']) 
			|| (!empty($options['disabled'][$category][$group]['current']) && in_array($currentID, $options['disabled'][$category][$group]['current'])) 
			|| (!empty($options['disabled'][$category][$group]['404']) && $currentID === 'pmsm-404') 
			|| !empty($options['disabled'][$category][$group]['post_types']) 
			|| !empty($options['disabled'][$category][$group]['archives']) 
			|| !empty($options['disabled'][$category][$group]['user_status']) 
			|| !empty($options['disabled'][$category][$group]['device_type']) 
			|| !empty($options['disabled'][$category][$group]['regex'])
		) {
			$type = $category;
			$handle = $group;
		}
	}

	//disable is set, check options
	if(!empty($options['disabled'][$type][$handle])) {

		$disabled_option = $options['disabled'][$type][$handle];

		$is_archive = !empty($settings['separate_archives']) && is_archive();

		if(!empty($disabled_option['everywhere']) 
			|| (!empty($disabled_option['current']) && in_array($currentID, $disabled_option['current'])) 
			|| (!empty($disabled_option['404']) && $currentID === 'pmsm-404') 
			|| (!$is_archive && pmsm_check_post_types($disabled_option)) 
			|| ($is_archive && pmsm_check_archives($disabled_option)) 
			|| pmsm_check_user_status($disabled_option) 
			|| pmsm_check_device_type($disabled_option) 
			|| (!empty($disabled_option['regex']) && preg_match($disabled_option['regex'], home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']))))
		) {

			//enabled checks
			if(!empty($options['enabled'][$type][$handle])) {

				$enabled_option = $options['enabled'][$type][$handle];
		
				//current url check
				if(!empty($enabled_option['current']) && in_array($currentID, $enabled_option['current'])) {
					return $src;
				}

				//404 check
				if(!empty($enabled_option['404']) && $currentID === 'pmsm-404') {
					return $src;
				}

				//user status check
				if(pmsm_check_user_status($enabled_option)) {
					return $src;
				} 

				//device type check
				if(pmsm_check_device_type($enabled_option)) {
					return $src;
				} 

				//regex check
				if(!empty($options['enabled'][$type][$handle]['regex'])) {
		  			$current_url = home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']));
		  			if(preg_match($enabled_option['regex'], $current_url)) {
						return $src;
					}
				}

				//archive check
				if(!empty($settings['separate_archives']) && is_archive()) {
					return pmsm_check_archives($enabled_option) ? $src : false;
				}

				//post type check
				if(pmsm_check_post_types($enabled_option)) {
					return $src;
				}
			}

			return false;
		}
	}

	//original script src
	return $src;
}

//Script Manager Get Current ID
function perfmatters_get_current_ID() {

	global $currentID;

	//check if global is set and return
	if(!empty($currentID) || $currentID === 0) {
		return $currentID;
	}
	
	global $wp_query;

	//make sure we have a usable query
	if(empty($wp_query->posts) || $wp_query->is_archive()) {

		//404 check
		if(is_404()) {
			return 'pmsm-404';
		} 

		//woocommerce shop check
		if(function_exists('is_shop') && is_shop()) {
			return wc_get_page_id('shop');
		}

		return '';
	}

	$currentID = '';
	
	if(is_object($wp_query)) {
		$currentID = $wp_query->get_queried_object_id();
	}
    
	if($currentID === 0) {
		if(!is_front_page()) {
			$postID = get_the_ID();
			if($postID !== 0) {
				$currentID = $postID;
			}
		}
	}

	if(has_filter('perfmatters_get_current_ID')) {
		$currentID = apply_filters('perfmatters_get_current_ID', $currentID);
	}

	return $currentID;
}

//check if current post type is set in option
function pmsm_check_post_types($option) {
	if(!empty($option['post_types'])) {
		if(is_front_page() || is_home()) {
			if(get_option('show_on_front') == 'page' && in_array('page', $option['post_types'])) {
				return true;
			}
		}
		else {
			if(in_array(get_post_type(), $option['post_types'])) {
				return true;
			}
		}
	}
	return false;
}

//check if current archive is set in option
function pmsm_check_archives($option) {
	if(!empty($option['archives'])) {
		$object = get_queried_object();
		if(!empty($object)) {

			$objectClass = get_class($object);
			if($objectClass == "WP_Post_Type") {
				if(in_array($object->name, $option['archives'])) {
					return true;
				}
			}
			elseif($objectClass == "WP_User") {
				if(in_array("author", $option['archives'])) {
					return true;
				}
			}
			else {
				if(in_array($object->taxonomy, $option['archives'])) {
					return true;
				}
			}
		}
	}
	return false;
}

//check if current user status is set
function pmsm_check_user_status($option) {
	if(!empty($option['user_status'])) {
		$status = is_user_logged_in();
		if(($status && $option['user_status'] == 'loggedin') || (!$status && $option['user_status'] == 'loggedout')) {
			return true;
		}
	}
	return false;
}

//check if current device type is set
function pmsm_check_device_type($option) {
	if(!empty($option['device_type'])) {
		$mobile = wp_is_mobile();
		if(($mobile && $option['device_type'] == 'mobile') || (!$mobile && $option['device_type'] == 'desktop')) {
			return true;
		}
	}
	return false;
}

//check if mu mode is on and version is correct
function perfmatters_script_manager_mu_notice() {
	$pmsm_settings = get_option('perfmatters_script_manager_settings');
	if(!empty($pmsm_settings['mu_mode'])) {

		if(!function_exists('get_plugin_data')) {
	        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
	    }

	    //get plugin data
	    $mu_plugin_data = get_plugin_data(WPMU_PLUGIN_DIR . "/perfmatters_mu.php");

		//display mu version mismatch notice
		if(defined('PERFMATTERS_VERSION') && !empty($mu_plugin_data['Version']) && $mu_plugin_data['Version'] != PERFMATTERS_VERSION) {
			echo "<div class='notice notice-warning'>";
				echo "<p>";
					echo "<strong>" . __('Perfmatters Warning', 'perfmatters') . ":</strong> ";
					echo __('MU plugin version mismatch.', 'perfmatters') . " <a href='https://perfmatters.io/docs/mu-mode/' target='_blank'>" . __('View Documentation', 'perfmatters') . "</a>";
				echo "</p>";
			echo "</div>";
		}
		elseif(!file_exists(WPMU_PLUGIN_DIR . "/perfmatters_mu.php")) {
			echo "<div class='notice notice-error'>";
				echo "<p>";
					echo "<strong>" . __('Perfmatters Warning', 'perfmatters') . ":</strong> ";
					echo __('MU plugin file not found.', 'perfmatters') . " <a href='https://perfmatters.io/docs/mu-mode/' target='_blank'>" . __('View Documentation', 'perfmatters') . "</a>";
				echo "</p>";
			echo "</div>";
		}
	}
}

//exclude our script manager js from autoptimize
function perfmatters_script_manager_exclude_autoptimize($exclude) {
	if(!strpos($exclude, 'script-manager.js')) {
		$exclude.= ',script-manager.js';
	}
	return $exclude;
}

//exclude our script manager js from siteground optimizer
function perfmatters_script_manager_exclude_sgo($exclude) {
	$exclude[] = 'perfmatters-script-manager-js';
    return $exclude;
}