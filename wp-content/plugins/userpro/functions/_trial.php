<?php

	add_action('userpro_pre_form_message', 'userpro_trial_notice');
	function userpro_trial_notice(){
		if (get_option('userpro_trial') == 1) {
			echo '<div class="userpro-message userpro-message-demo"><p>'.sprintf(__('You are using a trial version of UserPro plugin. If you have purchased the plugin, please enter your purchase code to enable the full version. You can enter your <a href="%s">purchase code here</a>.','userpro'), admin_url() . 'admin.php?page=userpro&tab=licensing').'</p></div>';
		}
	}