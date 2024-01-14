<?php

/**
 * Update Expired Points Action Scheduler.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!class_exists('RS_Update_Expired_Points_Action')) {

	/**
	 * Class.
	 * */
	class RS_Update_Expired_Points_Action extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id = 'rs_update_expired_points_action';
			$this->action_scheduler_name = 'rs_update_expired_points_action';
			$this->chunked_action_scheduler_name = 'rs_chunked_update_expired_points_action_data';
			$this->option_name = 'rs_update_expired_points_action_data';
			$this->settings_option_name = 'rs_update_expired_points_action_settings_args';

			// Do ajax action.
			add_action('wp_ajax_refresh_expired_points', array( $this, 'do_ajax_action' ));

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __('Expired Points Updated for User(s) is under process...', 'rewardsystem');
			return $label;
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __('Expired Points Updated for User(s) Completed Successfully.', 'rewardsystem');
			return $msg;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg(array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsgeneral' ), SRP_ADMIN_URL);
		}

		/*
		 * Do ajax action.
		 */

		public function do_ajax_action() {
			check_ajax_referer('fp-refresh-points', 'sumo_security');

			try {

				if (!isset($_POST)) {
					throw new exception(esc_html__('Invalid Request', 'rewardsystem'));
				}

				$user_ids = get_users(array( 'fields' => 'ids' ));
				if (!srp_check_is_array($user_ids)) {
					throw new exception(esc_html__('No User(s) Found', 'rewardsystem'));
				}

				global $wpdb;
				$imploded_user_ids = implode(',', $user_ids);
				$db = &$wpdb;
				$user_ids = $db->get_col($db->prepare("SELECT userid FROM {$db->prefix}rspointexpiry WHERE expirydate < %d and expirydate NOT IN(999999999999) and expiredpoints IN(0) and userid IN($imploded_user_ids)", time()));
				if (!srp_check_is_array($user_ids)) {
					throw new exception(esc_html__('No Data Found', 'rewardsystem'));
				}

				$this->schedule_action($user_ids, array());
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
			$current_time = time();
			foreach ($user_ids as $user_id) {
				$user = get_user_by('ID', $user_id);
				if (!is_object($user)) {
					continue;
				}

				$expired_points_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}rspointexpiry WHERE expirydate < %d and expirydate NOT IN(999999999999) and expiredpoints IN(0) and userid=%d", $current_time, $user_id), ARRAY_A);
				if (!srp_check_is_array($expired_points_data)) {
					continue;
				}

				foreach ($expired_points_data as $expired_points_value) {
					$wpdb->update("{$wpdb->prefix}rspointexpiry", array( 'expiredpoints' => $expired_points_value['earnedpoints'] - $expired_points_value['usedpoints'] ), array( 'id' => $expired_points_value['id'] ));
				}
			}
		}
	}

}
