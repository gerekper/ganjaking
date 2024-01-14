<?php

/**
 * Redeeming Points Bulk Update Action Scheduler.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!class_exists('RS_Redeeming_Points_Bulk_Update_Action')) {

	/**
	 * Class.
	 * */
	class RS_Redeeming_Points_Bulk_Update_Action extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id = 'rs_redeeming_points_bulk_update_action';
			$this->action_scheduler_name = 'rs_redeeming_points_bulk_update_action';
			$this->chunked_action_scheduler_name = 'rs_chunked_redeeming_points_bulk_update_action';
			$this->option_name = 'rs_redeeming_points_bulk_update_data';
			$this->settings_option_name = 'rs_redeeming_points_bulk_update_settings_args';

			// Do ajax action.
			add_action('wp_ajax_redeeming_points_bulk_update_action', array( $this, 'do_ajax_action' ));

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __('Redeeming Points for Product(s) is under process...', 'rewardsystem');
			return $label;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg(array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpredeeming' ), SRP_ADMIN_URL);
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __('Updating Redeeming Points for Product(s) Completed Successfully.', 'rewardsystem');
			return $msg;
		}

		/*
		 * Do ajax action.
		 */

		public function do_ajax_action() {

			check_ajax_referer('redeeming-points-bulk-update', 'sumo_security');

			try {
				
				if ( ! isset( $_POST['product_level_bulk_update_data'] ) ) {
					throw new exception(__('Something went wrong!', 'rewardsystem'));
				}
				
				$primary_data = array();
				$primary      = filter_var( $_POST['product_level_bulk_update_data'], FILTER_SANITIZE_STRING );
				parse_str( $primary, $primary_data );

				$bulk_array = srp_get_bulk_action_redeeming_points_field_keys();

				if ( srp_check_is_array( $bulk_array ) ) {
					foreach ( $bulk_array as $each_meta ) {
						if ( 'rs_enable_bulk_update_for_product_level_redeeming' === $each_meta ) {
							$data = isset( $primary_data['rs_enable_bulk_update_for_product_level_redeeming'] ) && '1' === $primary_data['rs_enable_bulk_update_for_product_level_redeeming'] ? 'yes' : 'no';
						} else {
							$data = isset( $primary_data[ $each_meta ] ) ? $primary_data[ $each_meta ] : '';
						}
						update_option( $each_meta, $data );
					}
				}

				$primary_datas = array(
					'srp_product_selection_type'=> isset($primary_data['rs_product_level_redeem_product_selection_type']) ? $primary_data['rs_product_level_redeem_product_selection_type'] : '',
					'srp_include_products'      => isset($primary_data['rs_include_products_for_product_level_redeem']) ? $primary_data['rs_include_products_for_product_level_redeem'] : array(),
					'srp_exclude_products'      => isset($primary_data['rs_exclude_products_for_product_level_redeem']) ? $primary_data['rs_exclude_products_for_product_level_redeem'] : array(),
					'srp_include_categories'    => isset($primary_data['rs_product_level_redeem_include_categories']) ? $primary_data['rs_product_level_redeem_include_categories'] : array(),
					'srp_exclude_categories'    => isset($primary_data['rs_product_level_redeem_exclude_categories']) ? $primary_data['rs_product_level_redeem_exclude_categories'] : array(),
				);
				
				$ids = srp_get_selected_product_ids( $primary_datas );
				
				if ( srp_check_is_array( $ids )) {
					foreach ( $ids as $id ) {
						$product_object = wc_get_product( $id );

						if ( 'variable' === $product_object->get_type() ) {
							$child_ids = $product_object->get_children();

							if ( srp_check_is_array( $child_ids ) ) {
								foreach ( $child_ids as $child_id ) {
									$product_ids[] = (string) $child_id;
								}
							}
						} elseif ( 'variation' === $product_object->get_type() ) {
							$product_ids[] = $id;
						} elseif ( 'simple' === $product_object->get_type() ) {
							$product_ids[] = $id;
						}
					}
				}

				if (!srp_check_is_array($product_ids)) {
					throw new exception(__('No Product(s) Found', 'rewardsystem'));
				}

				$settings_data = array(
					'primary_data' => $primary_data,
				);

				$this->schedule_action($product_ids, $settings_data);
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
			if ( ! srp_check_is_array( $product_ids ) ) {
				return;
			}

			$settings_data = $this->get_settings_data();
			$primary_data  = $settings_data['primary_data'];

			if ( srp_check_is_array( $primary_data ) ) {
				
				$enable_max_redeem = isset( $primary_data['rs_enable_maximum_redeeming_points'] ) ? wc_clean( wp_unslash($primary_data['rs_enable_maximum_redeeming_points'])) : '2';
				$redeeming_points = isset( $primary_data['rs_maximum_redeeming_points'] ) ? wc_clean( wp_unslash($primary_data['rs_maximum_redeeming_points'])) : '';
				
				foreach ( $product_ids as $product_id ) {
					update_post_meta( $product_id, '_rewardsystem_redeeming_points_enable', $enable_max_redeem );
					update_post_meta( $product_id, '_rewardsystem_max_redeeming_points', $redeeming_points );
				}
			}
		}
	}

}
