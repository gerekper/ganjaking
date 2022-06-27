<?php
//save license key
if(isset($_POST['perfmatters_save_license']) && isset($_POST['perfmatters_edd_license_key'])) {

	//save license option
	if(is_network_admin()) {
		update_site_option('perfmatters_edd_license_key', trim($_POST['perfmatters_edd_license_key']));
	}
	else {
		update_option('perfmatters_edd_license_key', trim($_POST['perfmatters_edd_license_key']));
	}

	if(is_multisite()) {

		//check license info
		$license_info = perfmatters_check_license();

		if(!empty($license_info->activations_left) && $license_info->activations_left == 'unlimited') {
			
			//activate after save
			perfmatters_activate_license();
		}
	}
	else {
		//activate after save
		perfmatters_activate_license();
	}
}

//activate license
if(isset($_POST['perfmatters_edd_license_activate'])) {
	perfmatters_activate_license();
}

//deactivate license
if(isset($_POST['perfmatters_edd_license_deactivate'])) {
	perfmatters_deactivate_license();
}

//remove license
if(isset($_POST['perfmatters_remove_license'])) {

	//deactivate before removing
	perfmatters_deactivate_license();

	//remove license option
	if(is_network_admin()) {
		delete_site_option('perfmatters_edd_license_key');
	}
	else {
		delete_option('perfmatters_edd_license_key');
	}
}

//get license key
$license = is_network_admin() ? get_site_option('perfmatters_edd_license_key') : get_option('perfmatters_edd_license_key');

//start custom license form
echo "<form method='post' action=''>";

	echo '<div class="perfmatters-settings-section">';

		//tab header
		echo "<h2>" . __('License', 'perfmatters') . "</h2>";

		echo "<table class='form-table'>";
			echo "<tbody>";

				//license key
				echo "<tr>";
					echo "<th>" . perfmatters_title(__('License Key', 'perfmatters'), (empty($license) ? 'perfmatters_edd_license_key' : false), 'https://perfmatters.io/docs/troubleshooting-license-key-activation/') . "</th>";
					echo "<td>";

						echo "<input id='perfmatters_edd_license_key' name='perfmatters_edd_license_key' type='password' class='regular-text' value='" . (!empty($license) ? 'yerawizardharry' : '') . "' style='margin-right: 10px;' maxlength='50' />";

						if(empty($license)) {
							//save license button
							echo "<input type='submit' name='perfmatters_save_license' class='button button-primary' value='" . __('Save License', 'perfmatters') . "'>";
						}
						else {
							//remove license button
							echo "<input type='submit' class='button perfmatters-button-warning' name='perfmatters_remove_license' value='" . __('Remove License', 'perfmatters') . "' />";
						}

						perfmatters_tooltip(__('Save or remove your license key.', 'perfmatters'));

					echo "</td>";
				echo "</tr>";

				if(!empty($license)) {

					//force disable styles on license input
					echo "<style>
					input[name=\"perfmatters_edd_license_key\"] {
						background: rgba(255,255,255,.5);
					    border-color: rgba(222,222,222,.75);
					    box-shadow: inset 0 1px 2px rgba(0,0,0,.04);
					    color: rgba(51,51,51,.5);
					    pointer-events: none;
					}
					</style>";

					//check license info
					$license_info = perfmatters_check_license();

					if(!empty($license_info)) {

						//activate/deactivate license
						if(!empty($license_info->license) && $license_info->license != 'invalid') {
							echo "<tr>";
								echo "<th>" . __('Activate License', 'perfmatters') . "</th>";
								echo "<td>";
									if($license_info->license == 'valid') {
										echo "<input type='submit' class='button-secondary' name='perfmatters_edd_license_deactivate' value='" . __('Deactivate License', 'perfmatters') . "' style='margin-right: 10px;' />";
										echo "<span style='color:green;line-height:30px;'><span class='dashicons dashicons-cloud'style='line-height:30px;'></span> " . __('License is activated.', 'novashare') . "</span>";
									} 
									elseif(!is_multisite() || (!empty($license_info->activations_left) && $license_info->activations_left == 'unlimited')) {
										echo "<input type='submit' class='button-secondary' name='perfmatters_edd_license_activate' value='" . __('Activate License', 'perfmatters') . "' style='margin-right: 10px;' />";
										echo "<span style='color:red;line-height:30px;'><span class='dashicons dashicons-warning'style='line-height:30px;'></span> " . __('License is not activated.', 'novashare') . "</span>";
									}
									else {
										echo "<span style='color:red; display: block;'>" . __('Unlimited License needed for use in a multisite environment. Please contact support to upgrade.', 'perfmatters') . "</span>";
									}
								echo "</td>";
							echo "</tr>";
						}

						//license status (active/expired)
						if(!empty($license_info->license)) {
							echo "<tr>";
								echo "<th>" . __('License Status', 'perfmatters') . "</th>";
								echo "<td" . ($license_info->license == "expired" ? " style='color: red;'" : "") . ">";
									echo ucfirst($license_info->license);
									if($license_info->license == "expired") {
										echo "<br />";
										echo "<a href='https://perfmatters.io/checkout/?edd_license_key=" . $license . "&download_id=696' class='button-primary' style='margin-top: 10px;' target='_blank'>" . __('Renew Your License for Updates + Support!', 'perfmatters') . "</a>";
									}
								echo "</td>";
							echo "</tr>";
						}

						//licenses used
						if(!empty($license_info->site_count) && !empty($license_info->license_limit) && !is_network_admin()) {
							echo "<tr>";
								echo "<th>" . __('Licenses Used', 'perfmatters') . "</th>";
								echo "<td>" . $license_info->site_count . "/" . $license_info->license_limit . "</td>";
							echo "</tr>";
						}

						//expiration date
						if(!empty($license_info->expires)) {
							echo "<tr>";
								echo "<th>" . __('Expiration Date', 'perfmatters') . "</th>";
								echo "<td>" . ($license_info->expires != 'lifetime' ? date("F d, Y", strtotime($license_info->expires)) : __('Lifetime', 'perfmatters')) . "</td>";
							echo "</tr>";
						}
					}
				}
			echo "</tbody>";
		echo "</table>";
	echo '</div>';
echo "</form>";