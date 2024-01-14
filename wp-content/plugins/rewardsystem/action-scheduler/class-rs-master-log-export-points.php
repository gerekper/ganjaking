<?php

/**
 * Master Log Export Points Action Scheduler.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!class_exists('RS_Master_Log_Export_Points')) {

	/**
	 * Class.
	 * */
	class RS_Master_Log_Export_Points extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id = 'rs_master_log_export_points';
			$this->action_scheduler_name = 'rs_master_log_export_points';
			$this->chunked_action_scheduler_name = 'rs_chunked_master_log_export_points_data';
			$this->option_name = 'rs_master_log_export_points_data';
			$this->settings_option_name = 'rs_master_log_export_points_settings_args';

			// Do ajax action.
			add_action('wp_ajax_export_log', array( $this, 'do_ajax_action' ));

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __('Exporting Points for User(s) is under process...', 'rewardsystem');
			return $label;
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __('Exporting Points for User(s) Completed Successfully.', 'rewardsystem');
			return $msg;
		}

		/**
		 * Get settings URL.
		 */
		public function get_settings_url() {
			return add_query_arg(array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmasterlog' ), SRP_ADMIN_URL);
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg(array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmasterlog', 'export_log' => 'yes' ), SRP_ADMIN_URL);
		}

		/*
		 * Admin init.
		 */

		public function do_ajax_action() {
			check_ajax_referer('fp-export-log', 'sumo_security');

			try {

				if (!isset($_POST)) {
					throw new exception(esc_html__('Invalid Request', 'rewardsystem'));
				}

				delete_option('rs_data_to_export');
				$setting_values = array();
				$setting_values['user_type'] = isset($_POST['usertype']) ? wc_clean(wp_unslash($_POST['usertype'])) : '';
				$setting_values['selected_users'] = isset($_POST['selecteduser']) ? wc_clean(wp_unslash($_POST['selecteduser'])) : array();

				$args = array(
					'fields' => 'ids',
				);

				if ('2' == $setting_values['user_type']) {
					if (!srp_check_is_array($setting_values['selected_users'])) {
						throw new exception(esc_html__('Selected User(s) data is empty', 'rewardsystem'));
					}

					$args['include'] = $setting_values['selected_users'];
				}

				if ('yes' == get_option('rs_enable_reward_program')) {
					$args['meta_key'] = 'allow_user_to_earn_reward_points';
					$args['meta_value'] = 'yes';
				}

				$user_ids = get_users($args);
				if (!srp_check_is_array($user_ids)) {
					throw new exception(esc_html__('No User(s) Found', 'rewardsystem'));
				}

				$this->schedule_action($user_ids, $setting_values);
				$redirect_url = esc_url_raw(add_query_arg(array( 'page' => 'rewardsystem_callback', 'rs_action_scheduler' => $this->get_id() ), SRP_ADMIN_URL));
				wp_send_json_success(array( 'redirect_url' => $redirect_url ));
			} catch (Exception $e) {
				wp_send_json_error(array( 'error' => $e->getMessage() ));
			}
		}

		/*
		 * Chunked scheduler action.
		 */

		public function chunked_scheduler_action( $user_ids ) {

			if (!srp_check_is_array($user_ids)) {
				return;
			}

			global $wpdb;
			$overall_data = array();
			foreach ($user_ids as $user_id) {
				$user = get_user_by('ID', $user_id);
				if (!is_object($user)) {
					continue;
				}

				$record_points_table_logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}rsrecordpoints WHERE userid = %d", $user_id), ARRAY_A);
				$record_points_table_logs = $record_points_table_logs + (array) get_option('rsoveralllog');
				if (srp_check_is_array($record_points_table_logs)) {
					foreach ($record_points_table_logs as $values) {
						if (empty($values)) {
							continue;
						}

						if (isset($values['earnedpoints'])) {
							$username = get_user_meta($values['userid'], 'nickname', true);
							$refuserid = get_user_meta($values['refuserid'], 'nickname', true);
							$nomineeid = get_user_meta($values['nomineeid'], 'nickname', true);
							$usernickname = get_user_meta($values['userid'], 'nickname', true);
							$earnpoints = $values['earnedpoints'];
							$redeempoints = $values['redeempoints'];
							$eventname = RSPointExpiry::msg_for_log(true, true, true, $earnpoints, $values['checkpoints'], $values['productid'], $values['orderid'], $values['variationid'], $values['userid'], $refuserid, $values['reasonindetail'], $redeempoints, true, $nomineeid, $usernickname, $values['nomineepoints'], $values);
						} else {
							$username = get_user_meta($values['userid'], 'nickname', true);
							$earnpoints = round_off_type($values['totalvalue']);
							$redeempoints = round_off_type($values['totalvalue']);
							$eventname = $values['eventname'];
							$values['earneddate'] = $values['date'];
						}
												
												$customer      = new WC_Customer( absint($user_id) );
						$overall_data[] = array(
							'user_name' => $username,
														'first_name' => $user->first_name,
							'last_name' => $user->last_name,
							'user_email' => $user->user_email,
														'phone_number' => $customer->get_billing_phone(),
							'points' => empty($earnpoints) ? $redeempoints : $earnpoints,
							'event' => $eventname,
							'date' => date_display_format($values['earneddate']),
							'expiry_date' => 999999999999 != $values['expirydate'] ? date_display_format($values['expirydate']) : '-',
						);
					}
				}
			}

			$old_data = !empty(get_option('rs_data_to_export')) ? get_option('rs_data_to_export') : array();
			$mergedata = array_merge($old_data, $overall_data);
			update_option('rs_data_to_export', $mergedata);
		}
	}

}
