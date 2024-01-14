<?php

/**
 * Buying Points Bulk Update Action Scheduler.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!class_exists('RS_Buying_Points_Bulk_Update_Action')) {

	/**
	 * Class.
	 * */
	class RS_Buying_Points_Bulk_Update_Action extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id = 'rs_buying_points_bulk_update_action';
			$this->action_scheduler_name = 'rs_buying_points_bulk_update_action';
			$this->chunked_action_scheduler_name = 'rs_chunked_buying_points_bulk_update_action';
			$this->option_name = 'rs_buying_points_bulk_update_data';
			$this->settings_option_name = 'rs_buying_points_bulk_update_settings_args';

			// Do ajax action.
			add_action('wp_ajax_buying_points_bulk_update_action', array( $this, 'do_ajax_action' ));

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __('Buying Points for Product(s) is under process...', 'rewardsystem');
			return $label;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg(array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpbuyingpoints' ), SRP_ADMIN_URL);
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __('Updating Buying Points for Product(s) Completed Successfully.', 'rewardsystem');
			return $msg;
		}

		/*
		 * Do ajax action.
		 */

		public function do_ajax_action() {

			check_ajax_referer('buying-reward-bulk-update', 'sumo_security');

			try {
				if (!isset($_POST)) {
					throw new exception(__('Invalid data', 'rewardsystem'));
				}

				$setting_values = array();
				$setting_values['applicable_product'] = isset($_POST['applicable_product']) ? wc_clean(wp_unslash($_POST['applicable_product'])) : '';
				$setting_values['enable_buying_point'] = isset($_POST['enable_buying_point']) ? wc_clean(wp_unslash($_POST['enable_buying_point'])) : '';
				$setting_values['buying_point'] = isset($_POST['buying_point']) ? wc_clean(wp_unslash($_POST['buying_point'])) : '';
				$setting_values['include_products'] = isset($_POST['include_products']) ? wc_clean(wp_unslash($_POST['include_products'])) : array();
				$setting_values['exclude_products'] = isset($_POST['exclude_products']) ? wc_clean(wp_unslash($_POST['exclude_products'])) : array();

				$args = array(
					'post_type' => array( 'product', 'product_variation' ),
					'posts_per_page' => '-1',
					'post_status' => 'publish',
					'fields' => 'ids',
				);

				switch ($setting_values['applicable_product']) {
					case '2':
						$args['include'] = srp_check_is_array($setting_values['include_products']) ? $setting_values['include_products'] : array();
						break;
					case '3':
						$args['exclude'] = srp_check_is_array($setting_values['exclude_products']) ? $setting_values['exclude_products'] : array();
						break;
				}

				$product_ids = get_posts($args);
				if (!srp_check_is_array($product_ids)) {
					throw new exception(__('No Product(s) Found', 'rewardsystem'));
				}

				$option_args = array(
					'rs_buying_points_is_applicable' => $setting_values['applicable_product'],
					'rs_include_products_for_buying_points' => $setting_values['include_products'],
					'rs_exclude_products_for_buying_points' => $setting_values['exclude_products'],
					'rs_enable_buying_points' => $setting_values['enable_buying_point'],
					'rs_points_for_buying_points' => $setting_values['buying_point'],
				);

				foreach ($option_args as $option_name => $option_value) {
					update_option(sanitize_key($option_name), $option_value);
				}

				$this->schedule_action($product_ids, $setting_values);
				$redirect_url = esc_url_raw(add_query_arg(array( 'page' => 'rewardsystem_callback', 'rs_action_scheduler' => $this->get_id() ), SRP_ADMIN_URL));
				wp_send_json_success(array( 'redirect_url' => $redirect_url ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/*
		 * Chunked scheduler action.
		 */

		public function chunked_scheduler_action( $product_ids ) {

			if (!srp_check_is_array($product_ids)) {
				return;
			}

			$settings_data = $this->get_settings_data();
			if (empty($settings_data)) {
				return;
			}

			foreach ($product_ids as $product_id) {
				$product = wc_get_product($product_id);
				$settings_data['enable_buying_point'] = is_object($product) && 'variation' == $product->get_type() && 'yes' == $settings_data['enable_buying_point']? '1': $settings_data['enable_buying_point'];
				$meta_args = array(
					'_rewardsystem_buying_reward_points' => $settings_data['enable_buying_point'],
					'_rewardsystem_assign_buying_points' => $settings_data['buying_point'],
				);

				foreach ($meta_args as $meta_key => $meta_value) {
					// Update metas.
					update_post_meta($product_id, sanitize_key($meta_key), $meta_value);
				}
			}
		}
	}

}
