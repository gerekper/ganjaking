<?php
echo '<input type="hidden" name="tab" value="global" />';

//title bar
echo '<div class="perfmatters-script-manager-title-bar">';
	echo '<h1>' . __('Global View', 'perfmatters') . '</h1>';
	echo '<p>' . __('This is a visual representation of the Script Manager configuration across your entire site.', 'perfmatters') . '</p>';
echo '</div>';

$options = get_option('perfmatters_script_manager');

//global scripts display
if(!empty($options)) {
	foreach($options as $category => $types) {

		//category header
		echo '<h3>' . $category . '</h3>';

		if(!empty($types)) {

			$type_names = array(
				'js' => 'JavaScript',
				'css' => 'CSS',
				'plugins' => 'Plugins'
			);

			echo '<div style="background: #ffffff; padding: 10px;">';
				
				foreach($types as $type => $scripts) {
					echo '<div class="perfmatters-script-manager-group">';
						echo '<div class="pmsm-group-heading">';
							echo '<h4>' . $type_names[$type] . '</h4>';
						echo '</div>';

						echo '<div class="perfmatters-script-manager-section">';

							echo '<table>';
								echo '<thead>';
									echo '<tr>';
										echo '<th>' . __('Handle', 'perfmatters') . '</th>';
										echo '<th>' . __('Setting', 'perfmatters') . '</th>';
										echo '<th style="width: 40px;"></th>';
									echo '</tr>';
								echo '</thead>';
								echo '<tbody>';

									if(!empty($scripts)) {

										foreach($scripts as $script => $details) {

											if(!empty($details)) {

												foreach($details as $detail => $values) {
													
													$contains_junk = false;

													echo '<tr>';
														echo '<td>' . $script . '</td>';
														echo '<td>';
															echo '' . $detail . '';
															if($detail == 'current' || $detail == 'post_types' || $detail == 'archives') {
																if(!empty($values)) {
																	echo ' (';
																	$valueString = '';
																	foreach($values as $key => $value) {
																		if($detail == 'current') {
																			if((int)$value !== 0) {
																				if($value == 'pmsm-404') {
																					$valueString.= '404, ';
																				}
																				else {
																					$permalink = get_permalink($value);
																					if($permalink) {
																						$valueString.= '<a href="' . $permalink . '" title="' . get_the_title($value) . '" target="_blank">' . $value . '</a>, ';
																					}
																					else {
																						$valueString.= '<a style="color: orange; text-decoration: line-through;">' . $value . '</a>, ';
																						$contains_junk = true;
																					}
																				}
																			}
																			else {
																				$valueString.= '<a href="' . get_home_url() . '" target="_blank">homepage</a>, ';
																			}
																		}
																		else {
																			$valueString.= $value . ', ';
																		}
																	}
																	echo rtrim($valueString, ", ");
																	echo ')';
																}
															}
															elseif($detail !== 'everywhere') {
																echo ' (' . $values . ')';
															}
														echo '</td>';
														echo '<td style="text-align: right;">';

															//refresh button
															if($contains_junk) {
																echo '<button class="pmsm-action-button" name="pmsm_global_refresh" title="Refresh" value="' . $category . '|' . $type . '|' . $script . '">';
																	echo '<span class="dashicons dashicons-update"></span>';
																echo '</button>';
															}

															//trash button
															echo '<button class="pmsm-action-button" name="pmsm_global_trash" title="Delete" value="' . $category . '|' . $type . '|' . $script . '|' . $detail . '" onClick="return confirm(\'Are you sure you want to delete this option?\');">';
																echo '<span class="dashicons dashicons-trash"></span>';
															echo '</button>';

														echo '</td>';
													echo '</tr>';
												}
											}
										}
									}
								echo '</tbody>';
							echo '</table>';
						echo '</div>';
					echo '</div>';
				}	
			echo '</div>';
		}
	}
}
else {
	echo '<div class="perfmatters-script-manager-section">';
		echo '<p style="padding: 20px; text-align: center;">' . __("You don't have any scripts disabled yet.", 'perfmatters') . '</p>';
	echo '</div>';
}