<?php

if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('SRP_Update_For_Simple_Product')) {

	/**
	 * SRP_Update_For_Simple_Product Class.
	 */
	class SRP_Update_For_Simple_Product extends WP_Background_Process {

		/**
				 * Action Name.
				 * 
		 * @var string
		 */
		protected $action = 'rs_simple_product_background_updater';

		/**
		 * Task
		 *
		 * Override this method to perform any actions required on each
		 * queue item. Return the modified item for further processing
		 * in the next pass through. Or, return false to remove the
		 * item from the queue.
		 *
		 * @param mixed $item Queue item to iterate over
		 *
		 * @return mixed
		 */
		protected function task( $item) {
			$this->rs_update_simple_product_data($item);
			return false;
		}

		/**
		 * Complete
		 *
		 * Override if applicable, but ensure that the below actions are
		 * performed, or, call parent::complete().
		 */
		protected function complete() {
			global $wpdb;
			parent::complete();
			$offset = get_option('rs_simple_product_background_updater_offset');
			$ids = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT ID FROM {$wpdb->posts} as p INNER JOIN {$wpdb->postmeta} as p1 ON p.ID=p1.post_id WHERE p.post_type = 'product' AND p1.meta_key = '_rewardsystemcheckboxvalue' AND p1.meta_value = 'yes' LIMIT %d,1000", $offset));
			if (is_array($ids) && !empty($ids)) {
				SRP_Background_Process::callback_to_update_simple_product($offset);
			} else {
				SRP_Background_Process::$rs_progress_bar->fp_increase_progress(40);
				FP_WooCommerce_Log::log('Simple Product Upgrade Completed');
				delete_option('rs_simple_product_background_updater_offset');
				delete_option('rs_variable_product_background_updater_offset');
				SRP_Background_Process::callback_to_update_variable_product();
			}
		}

		public static function rs_update_simple_product_data( $product_id) {
			if ('rs_data' != $product_id) {
				if ('' == get_post_meta($product_id, 'rs_check_if_already_exists', true)) {
					update_post_meta($product_id, '_rewardsystemreferralcheckboxvalue', 'yes');
					update_post_meta($product_id, 'rs_check_if_already_exists', 'yes');
				}
			}
			return $product_id;
		}

	}
}
