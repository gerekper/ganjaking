<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * This class defines wccs cronjob for the plugin.
 */

if (!class_exists('WCCS_Cron')) {

	class WCCS_Cron {
	

		public function __construct() {
			
			// cronjob to update exchange rates for currencies
			add_action('wccs_update_rates', array( $this, 'wccs_update_rates_callback' ), 10, 1);
			
			// custom cron recurrences
			add_filter('cron_schedules', array( $this, 'custom_cron_recurrence' ));
		}

		public static function wccs_update_rates_callback( $is_cron ) {

			$currencies = get_option('wccs_currencies', array());

			
			if (count($currencies)) {
				$codes = array_keys($currencies);
				
				$latest = wccs_get_exchange_rates(implode(',', $codes));
				if (!isset($latest['error'])) {
					$changed = array();
					foreach ($currencies as $code => $info) {
						if (isset($latest['rates'][$code])) {
							if ($currencies[$code]['rate'] != $latest['rates'][$code]) {
								$changed[$currencies[$code]['label']] = $latest['rates'][$code];
							}
							   $currencies[$code]['rate'] = $latest['rates'][$code];
						}
					}
					
					update_option('wccs_currencies', $currencies);
					
					// send email with changed rates
					$send_email = get_option('wccs_admin_email', 0);
					if ($send_email && count($changed) && $is_cron) {
						$sitename = get_option('blogname');
						$admin_email = get_option('admin_email');
						if (get_option('wccs_email', '')) {
							$to = get_option('wccs_email');
						} else {
							$to = $admin_email;
						}
						$subject = __('Currency rates updated', 'wccs');
						$body = wccs_get_email_body('currency_update', array( 'changed' => $changed ));
						$headers = array();
						$headers[] = 'Content-Type: text/html; charset=UTF-8';
						$headers[] = 'From: ' . $sitename . ' <' . $admin_email . '>';

						wp_mail($to, $subject, $body, $headers);
					}
				}
			}
		}
		
		public function custom_cron_recurrence( $schedules ) {

			$schedules['weekly'] = array(
			'display' => __('Weekly', 'wccs'),
			'interval' => 604800,
			);
			
			return $schedules;
		}
	}

	$wccs_cron = new WCCS_Cron();
}
