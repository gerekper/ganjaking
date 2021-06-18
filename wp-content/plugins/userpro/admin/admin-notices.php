<?php

function userpro_admin_notice($message, $errormsg = false)
{
	if ($errormsg) {
		echo '<div id="message" class="error">';
	}
	else {
		echo '<div id="message" class="updated fade">';
	}

	echo "<p><strong>$message</strong></p></div>";
}

function userpro_admin_notices()
{
	if (current_user_can('manage_options') && get_option('userpro_trial') == 1) {
		userpro_admin_notice( sprintf(__('You are using a trial version of UserPro plugin. If you have purchased the plugin, please enter your purchase code to enable the full version. You can enter your <a href="%s">purchase code here</a>.','userpro'), admin_url() . 'admin.php?page=userpro&tab=licensing'), true);
	}
}

add_action('admin_notices', 'userpro_admin_notices');