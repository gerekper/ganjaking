<?php
//security check
if(!function_exists('wp_get_current_user') || !current_user_can('manage_options') || is_admin() || !isset($_GET['perfmatters']) || !perfmatters_network_access() || perfmatters_is_page_builder()) {
	return;
}

global $pmsm_print_flag;

//script manager already printed
if($pmsm_print_flag) {
	return;
}

$pmsm_print_flag = true;

//set variables
global $perfmatters_tools;
global $wp;
global $wp_scripts;
global $wp_styles;
global $perfmatters_options;
global $currentID;
$currentID = perfmatters_get_current_ID();
$pmsm_tab = !empty($_POST['tab']) ? $_POST['tab'] : 'main';

//filter language locale for script manager ui
switch_to_locale(apply_filters('perfmatters_script_manager_locale', ''));

//process settings form
if(isset($_POST['pmsm_save_settings'])) {

	//validate settings nonce
	if(!isset($_POST['perfmatters_script_manager_settings_nonce']) || !wp_verify_nonce($_POST['perfmatters_script_manager_settings_nonce'], 'perfmatter_script_manager_save_settings')) {
		print 'Sorry, your nonce did not verify.';
	    exit;
	}
	else {

		//update settings
		update_option('perfmatters_script_manager_settings', (!empty($_POST['perfmatters_script_manager_settings']) ? $_POST['perfmatters_script_manager_settings'] : ''));
	}
}

//manually closed disclaimer
if(isset($_POST['pmsm_disclaimer_close'])) {
	if(isset($_POST['pmsm_disclaimer_close_nonce']) && wp_verify_nonce($_POST['pmsm_disclaimer_close_nonce'], 'pmsm_disclaimer_close')) {
		$settings = get_option('perfmatters_script_manager_settings');
		if(empty($settings) || !is_array($settings)) {
			$settings = array();
		}
		$settings['hide_disclaimer'] = 1;
		update_option('perfmatters_script_manager_settings', $settings);
	}
}

//process reset form
if(isset($_POST['perfmatters_script_manager_settings_reset'])) {
	delete_option('perfmatters_script_manager');
	delete_option('perfmatters_script_manager_settings');
}

//global trash
if(isset($_POST['pmsm_global_trash'])) {

	$trash = explode("|", $_POST['pmsm_global_trash']);

	if(count($trash) == 4) {
		list($category, $type, $script, $detail) = $trash;


		$options = get_option('perfmatters_script_manager');

		unset($options[$category][$type][$script][$detail]);

		if($category == 'disabled' && $detail == 'everywhere') {
			unset($options['enabled'][$type][$script]);
		}

		//clean up the options array before saving
		perfmatters_script_manager_filter_options($options);

		update_option('perfmatters_script_manager', $options);
	}
}

//load script manager settings
global $perfmatters_script_manager_settings;
$perfmatters_script_manager_settings = get_option('perfmatters_script_manager_settings');

//build array of existing plugin disables
global $perfmatters_disables;
$perfmatters_disables = array();
if(!empty($perfmatters_options['disable_google_maps'])) {
	$perfmatters_disables[] = 'maps.google.com';
	$perfmatters_disables[] = 'maps.googleapis.com';
	$perfmatters_disables[] = 'maps.gstatic.com';
}
if(!empty($perfmatters_options['disable_google_fonts'])) {
	$perfmatters_disables[] = 'fonts.googleapis.com';
}

//setup filters array
global $perfmatters_filters;
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

//load script manager options
global $perfmatters_script_manager_options;
$perfmatters_script_manager_options = get_option('perfmatters_script_manager');

//load styles
include('script_manager_css.php');

//disable shortcodes
remove_all_shortcodes();

//wrapper
echo "<div id='perfmatters-script-manager-wrapper' " . (isset($_GET['perfmatters']) ? "style='display: flex;'" : "") . ">";

	//header
	echo "<div id='perfmatters-script-manager-header'>";

		echo "<div id='pmsm-header-hero'>";

			//menu toggle
			echo "<span id='pmsm-menu-toggle'><span class='dashicons dashicons-menu'></span></span>";

			//logo
			echo "<img src='" . plugins_url('img/logo.svg', dirname(__FILE__)) . "' title='Perfmatters' id='perfmatters-logo' />";

		echo "</div>";
	
		//main navigation
		echo "<form method='POST'>";
			echo "<div id='perfmatters-script-manager-tabs'>";
				echo "<button name='tab' value=''" . ($pmsm_tab == 'main' ? " class='active'" : "")  . " title='" . __('Script Manager', 'perfmatters') . "'><span class='dashicons dashicons-admin-settings'></span>" . __('Script Manager', 'perfmatters') . "</button>";
				echo "<button name='tab' value='global'" . ($pmsm_tab == 'global' ? " class='active'" : "")  . " title='" . __('Global View', 'perfmatters') . "'><span class='dashicons dashicons-admin-site'></span>" . __('Global View', 'perfmatters') . "</button>";
				echo "<button name='tab' value='settings'" . ($pmsm_tab == 'settings' ? " class='active'" : "")  . " title='" . __('Settings', 'perfmatters') . "'><span class='dashicons dashicons-admin-generic'></span>" . __('Settings', 'perfmatters') . "</button>";
			echo "</div>";
		echo "</form>";

	echo "</div>";

	//main container
	echo "<div id='perfmatters-script-manager'>";

		//visible container
		echo "<div id='pmsm-viewport'>";

			echo '<div id="pmsm-notices">';

				//disclaimer
				if(empty($perfmatters_script_manager_settings['hide_disclaimer'])) {
					echo '<div class="pmsm-notice">';
						echo '<form method="POST">';
							echo $pmsm_tab != 'main' ? '<input type="hidden" name="tab" value="' . $pmsm_tab . '" />' : '';
							wp_nonce_field('pmsm_disclaimer_close', 'pmsm_disclaimer_close_nonce');
							echo '<button type="submit" id="pmsm-disclaimer-close" name="pmsm_disclaimer_close"/><span class="dashicons dashicons-dismiss"></span></button>';
						echo '</form>';
							_e('We recommend testing Script Manager changes on a staging/dev site first, as you could break your site\'s appearance.', 'perfmatters');
							echo ' <a href="https://perfmatters.io/docs/disable-scripts-per-post-page/" target="_blank">' . __('View Documentation', 'perfmatters') . '</a>';
					echo '</div>';
				}

				//testing mode
				if(!empty($perfmatters_script_manager_settings['testing_mode'])) {
					echo '<div class="pmsm-notice pmsm-notice-warning">' . __('You are in Testing Mode. Changes will only be visible to logged-in admins.') . '</div>';
				}
			echo '</div>';

			//universal form
			echo "<form method='POST' id='pmsm-" . $pmsm_tab . "-form'>";

				//content container
				echo "<div id='perfmatters-script-manager-container'>";

					//main tab
					if($pmsm_tab == 'main') {

						//title bar
						echo "<div class='perfmatters-script-manager-title-bar'>";
							echo "<h1>" . __('Script Manager', 'perfmatters') . "</h1>";
							echo "<p>" . __('Manage scripts loading on the current page.', 'perfmatters') . "</p>";
						echo "</div>";

						//load master array
						global $master_array;
						$master_array = perfmatters_script_manager_load_master_array();

						//print scripts
						foreach($master_array['resources'] as $category => $groups) {
							if(!empty($groups)) {
								echo "<h3>" . $category . "</h3>";
								if($category != "misc") {
									echo "<div style='background: #ffffff; padding: 10px;'>";
									foreach($groups as $group => $details) {
										echo "<div class='perfmatters-script-manager-group'>";
										
											echo "<div class='pmsm-group-heading'>";

												echo "<h4>" . (!empty($details['name']) ? $details['name'] : "") . "</h4>";

												//Status
												echo "<div class='perfmatters-script-manager-status' style='float: right; white-space: nowrap; margin-left: 10px;'>";

													if(!empty($details['size'])) {
														echo "<span class='pmsm-group-tag pmsm-group-size'>" . __('Total size', 'perfmatters') . ": " . round($details['size'] / 1024, 1) . " KB</span>";
													}

												    perfmatters_script_manager_print_status($category, $group);
												echo "</div>";

											echo "</div>";
											

											$assets = !empty($details['assets']) ? $details['assets'] : false;

											perfmatters_script_manager_print_section($category, $group, $assets);

										echo "</div>";
									}
									echo "</div>";
								}
								else {
									if(!empty($groups['assets'])) {
										perfmatters_script_manager_print_section($category, $category, $groups['assets']);
									}
								}
							}
						}

						//loading wrapper
						echo "<div id='pmsm-loading-wrapper'>";
							if(function_exists('is_amp_endpoint') && is_amp_endpoint()) {
								echo "<span class='pmsm-loading-text'>" . __('The Script Manager does not support AMP pages.', 'perfmatters') . "</span>";
							}
							else {
								echo "<span class='pmsm-loading-text'>" . __('Loading Scripts', 'perfmatters') . "<span class='pmsm-spinner'></span></span>";
							}
						echo "</div>";

					}
					//global view tab
					elseif($pmsm_tab == 'global') {

						echo "<input type='hidden' name='tab' value='global' />";

						//title bar
						echo "<div class='perfmatters-script-manager-title-bar'>";
							echo "<h1>" . __('Global View', 'perfmatters') . "</h1>";
							echo "<p>" . __('This is a visual representation of the Script Manager configuration across your entire site.', 'perfmatters') . "</p>";
						echo "</div>";
						
						//global scripts display
						if(!empty($perfmatters_script_manager_options)) {
							foreach($perfmatters_script_manager_options as $category => $types) {
								echo "<h3>" . $category . "</h3>";
								if(!empty($types)) {
									echo "<div class='perfmatters-script-manager-section'>";
										echo "<table>";
											echo "<thead>";
												echo "<tr>";
													echo "<th>" . __('Type', 'perfmatters') . "</th>";
													echo "<th>" . __('Script', 'perfmatters') . "</th>";
													echo "<th>" . __('Setting', 'perfmatters') . "</th>";
													echo "<th style='width: 20px;'></th>";
												echo "</tr>";
											echo "</thead>";
											echo "<tbody>";
												foreach($types as $type => $scripts) {
													if(!empty($scripts)) {
														foreach($scripts as $script => $details) {
															if(!empty($details)) {
																foreach($details as $detail => $values) {
																	echo "<tr>";
																		echo "<td><span style='font-weight: bold;'>" . $type . "</span></td>";
																		echo "<td><span style='font-weight: bold;'>" . $script . "</span></td>";
																		echo "<td>";
																			echo "<span style='font-weight: bold;'>" . $detail . "</span>";
																			if($detail == "current" || $detail == "post_types") {
																				if(!empty($values)) {
																					echo " (";
																					$valueString = "";
																					foreach($values as $key => $value) {
																						if($detail == "current") {
																							if((int)$value !== 0) {
																								if($value == 'pmsm-404') {
																									$valueString.= '404, ';
																								}
																								else {
																									$valueString.= "<a href='" . get_permalink($value) . "' target='_blank'>" . $value . "</a>, ";
																								}
																							}
																							else {
																								$valueString.= "<a href='" . get_home_url() . "' target='_blank'>homepage</a>, ";
																							}
																						}
																						elseif($detail == "post_types") {
																							$valueString.= $value . ", ";
																						}
																					}
																					echo rtrim($valueString, ", ");
																					echo ")";
																				}
																			}
																			elseif($detail == "user_status") {
																				echo " (" . $values . ")";
																			}
																		echo "</td>";
																		echo "<td>";
																			echo "<button class='pmsm-action-button' name='pmsm_global_trash' value='" . $category . "|" . $type . "|" . $script . "|" . $detail . "' onClick=\"return confirm('Are you sure you want to delete this option?');\">";
																				echo "<span class='dashicons dashicons-trash'></span>";
																			echo "</button>";

																		echo "</td>";
																	echo "</tr>";
																}
															}
														}
													}
												}
											echo "</tbody>";
										echo "</table>";
									echo "</div>";
								}
							}
						}
						else {
							echo "<div class='perfmatters-script-manager-section'>";
								echo "<p style='padding: 20px; text-align: center;'>" . __("You don't have any scripts disabled yet.") . "</p>";
							echo "</div>";
						}
					}
					//settings tab
					elseif($pmsm_tab == 'settings') {

						//title bar
						echo "<div class='perfmatters-script-manager-title-bar'>";
							echo "<h1>" . __('Settings', 'perfmatters') . "</h1>";
							echo "<p>" . __('View and manage all of your Script Manager settings.', 'perfmatters') . "</p>";
						echo "</div>";

						//settings container
						echo "<div id='script-manager-settings' class='perfmatters-script-manager-section'>";
							
								echo "<input type='hidden' name='tab' value='settings' />";

								echo "<table>";
									echo "<tbody>";
										echo "<tr>";
											echo "<th>" . perfmatters_title(__('Display Archives', 'perfmatters'), 'separate_archives') . "</th>";
											echo "<td>";
												$args = array(
										            'id' => 'separate_archives',
										            'option' => 'perfmatters_script_manager_settings'
										        );
												perfmatters_print_input($args);
												echo "<div>" . __('Add WordPress archives to your Script Manager selection options. Archive posts will no longer be grouped with their post type.', 'perfmatters') . "</div>";
											echo "</td>";
										echo "</tr>";
										echo "<tr>";
											echo "<th>" . perfmatters_title(__('Display Dependencies', 'perfmatters'), 'separate_archives') . "</th>";
											echo "<td>";
												$args = array(
										            'id' => 'dependencies',
										            'option' => 'perfmatters_script_manager_settings'
										        );
												perfmatters_print_input($args);
												echo "<div>" . __('Show dependencies for each script.', 'perfmatters') . "</div>";
											echo "</td>";
										echo "</tr>";
										echo "<tr>";
											echo "<th>" . perfmatters_title(__('Testing Mode', 'perfmatters'), 'testing_mode') . "</th>";
											echo "<td>";
												$args = array(
										            'id' => 'testing_mode',
										            'option' => 'perfmatters_script_manager_settings'
										        );
												perfmatters_print_input($args);
												echo "<div>" . __('Restrict your Script Manager configuration to logged-in admins only.', 'perfmatters') . ' <a href="https://perfmatters.io/docs/testing-mode/" target="_blank">' . __('View Documentation', 'perfmatters') . '</a>' . "</div>";
											echo "</td>";
										echo "</tr>";
										echo "<tr>";
											echo "<th>" . perfmatters_title(__('MU Mode', 'perfmatters'), 'mu_mode') . "</th>";
											echo "<td>";

												$args = array(
										            'id' => 'mu_mode',
										            'option' => 'perfmatters_script_manager_settings'
										        );
												perfmatters_print_input($args);
												echo "<div>" . __('Must-use (MU) mode requires elevated permissions and a file to be copied into the mu-plugins directory. This gives you more control and the ability to disable plugin queries, inline CSS, etc.', 'perfmatters') . ' <a href="https://perfmatters.io/docs/mu-mode/" target="_blank">' . __('View Documentation', 'perfmatters') . '</a>' . "</div>";

												echo "<div style='background: #faf3c4; padding: 10px; margin-top: 7px;'><strong>" . __('Warning', 'perfmatters') . ":</strong> " . __('Any previous plugin-level disables will now disable the entire plugin. Please review your existing Script Manager configuration before enabling this option.', 'perfmatters') . "</div>";

												//mu plugin file check
												if(!empty($perfmatters_script_manager_settings['mu_mode'])) {
													if(file_exists(WPMU_PLUGIN_DIR . "/perfmatters_mu.php")) {

														//$mu_plugins = get_mu_plugins();
														if(!function_exists('get_plugin_data')) {
													        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
													    }

													    //get plugin data
													    $mu_plugin_data = get_plugin_data(WPMU_PLUGIN_DIR . "/perfmatters_mu.php");

														if(empty($mu_plugin_data['Version']) || !defined('PERFMATTERS_VERSION') || ($mu_plugin_data['Version'] != PERFMATTERS_VERSION)) {
															$mu_message = __("MU plugin version mismatch.", 'perfmatters');
															$mu_class = "pmsm-mu-mismatch";
														}
														else {
															$mu_message = __("MU plugin installed.", 'perfmatters');
															$mu_class = "pmsm-mu-found";
														}
													}
													else {
														$mu_message = __("MU plugin file not found.", 'perfmatters');
														$mu_class = "pmsm-mu-missing";
													}

													echo "<div class='" . $mu_class . "'>" . $mu_message . "</div>";
												}

											echo "</td>";
										echo "</tr>";
										echo "<tr>";
											echo "<th>" . perfmatters_title(__('Hide Disclaimer', 'perfmatters'), 'hide_disclaimer') . "</th>";
											echo "<td>";
												$args = array(
										            'id' => 'hide_disclaimer',
										            'option' => 'perfmatters_script_manager_settings'
										        );
												perfmatters_print_input($args);
												echo "<div>" . __('Hide the disclaimer message box across all Script Manager views.', 'perfmatters') . "</div>";
											echo "</td>";
										echo "</tr>";
										echo "<tr>";
											echo "<th>" . perfmatters_title(__('Reset Script Manager', 'perfmatters'), 'reset_script_manager') . "</th>";
											echo "<td>";
												//Reset Form
												echo "<div>";
													echo "<input type='submit' name='pmsm-reset' id='pmsm-reset' class='pmsm-reset' value='" . __('Reset Script Manager', 'perfmatters') . "' />";
												echo "</div>";
												echo "<div>";
													echo "<span class='perfmatters-tooltip-text'>" . __('Remove and reset all of your existing Script Manager settings.', 'perfmatters') . "</span>";
												echo "</div>";
											echo "</td>";
										echo "</tr>";
									echo "</tbody>";
								echo "</table>";

								//Nonce
								wp_nonce_field('perfmatter_script_manager_save_settings', 'perfmatters_script_manager_settings_nonce');

						echo "</div>";
					}
				echo "</div>";

				//toolbar
				echo "<div class='perfmatters-script-manager-toolbar'>";
					echo "<div class='perfmatters-script-manager-toolbar-wrapper'>";
						echo "<div class='perfmatters-script-manager-toolbar-container'>";

							//save button
							echo "<div id='pmsm-save'>";
								if($pmsm_tab != 'global') {
									echo "<input type='submit' name='pmsm_save_" . $pmsm_tab . "' value='" . __('Save', 'perfmatters') . "' />";
									echo "<span class='pmsm-spinner'></span>";
								}
							echo "</div>";

							//copyright
							echo "<div class='pmsm-copyright'>© " . date("Y") . " Perfmatters</div>";

						echo "</div>";

						//message
						echo "<div id='pmsm-message' class='pmsm-message'></div>";

					echo "</div>";
				echo "</div>";
			echo "</form>";
		echo "</div>";

		//hidden reset form
		if($pmsm_tab == 'settings') {
			echo "<form method='POST' id='pmsm-reset-form' pmsm-confirm=\"" . __('Are you sure? This will remove and reset all of your existing Script Manager settings and cannot be undone!') . "\">";
				echo "<input type='hidden' name='tab' value='settings' />";
				echo "<input type='hidden' name='perfmatters_script_manager_settings_reset' class='pmsm-reset' value='submit' />";
			echo "</form>";
		}

	echo "</div>";
echo "</div>";