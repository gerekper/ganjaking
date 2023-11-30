<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */

if (!class_exists('WCCS_Deactivator')) {
	
	class WCCS_Deactivator {
	

		public static function deactivate() {
			// clear cronjob
			wp_clear_scheduled_hook('wccs_update_rates', array( true ));
		}
	}
	
}
