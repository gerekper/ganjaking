<?php

/**
 * Product Purchase Points Bulk Update Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Product_Purchase_Bulk_Update_Action' ) ) {

	/**
	 * Class.
	 * */
	class RS_Product_Purchase_Bulk_Update_Action extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_product_purchase_bulk_update_action';
			$this->action_scheduler_name         = 'rs_product_purchase_bulk_update_action';
			$this->chunked_action_scheduler_name = 'rs_chunked_product_purchase_bulk_update_data';
			$this->option_name                   = 'rs_product_purchase_bulk_update_data';
			$this->settings_option_name          = 'rs_product_purchase_bulk_update_settings_args';

			// Do ajax action.
			add_action( 'wp_ajax_bulk_update_points_for_product_purchase', array( $this, 'do_ajax_action' ) );

			parent::__construct();
		}

		/**
		 * Get progress bar label.
		 */
		public function get_progress_bar_label() {
			$label = __( 'Bulk Updating Product Purchase Settings for Product(s) is under process...', 'rewardsystem' );
			return $label;
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __( 'Bulk Updating Product Purchase Settings for Product(s) Completed Successfully.', 'rewardsystem' );
			return $msg;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg(
				array(
					'page'    => 'rewardsystem_callback',
					'tab'     => 'fprsmodules',
					'section' => 'fpproductpurchase',
				),
				SRP_ADMIN_URL
			);
		}

		/*
		 * Do ajax action.
		 */

		public function do_ajax_action() {
			check_ajax_referer( 'product-purchase-bulk-update', 'sumo_security' );

			try {
				if ( ! isset( $_POST ) ) {
					throw new exception( __( 'Invalid data', 'rewardsystem' ) );
				}

				$setting_values = array(
					'product_selection'   => isset( $_POST['productselection'] ) ? wc_clean( wp_unslash( $_POST['productselection'] ) ) : '',
					'enable_reward'       => isset( $_POST['enablereward'] ) ? wc_clean( wp_unslash( $_POST['enablereward'] ) ) : '',
					'selected_products'   => isset( $_POST['selectedproducts'] ) ? wc_clean( wp_unslash( $_POST['selectedproducts'] ) ) : array(),
					'selected_categories' => isset( $_POST['selectedcategories'] ) ? wc_clean( wp_unslash( $_POST['selectedcategories'] ) ) : array(),
					'reward_type'         => isset( $_POST['rewardtype'] ) ? wc_clean( wp_unslash( $_POST['rewardtype'] ) ) : '',
					'reward_points'       => isset( $_POST['rewardpoints'] ) ? wc_clean( wp_unslash( $_POST['rewardpoints'] ) ) : '',
					'reward_percent'      => isset( $_POST['rewardpercent'] ) ? wc_clean( wp_unslash( $_POST['rewardpercent'] ) ) : '',
					'minimum_qty'         => isset( $_POST['minimum_qty'] ) ? wc_clean( wp_unslash( $_POST['minimum_qty'] ) ) : '',
				);

				$args = array(
					'post_type'      => array( 'product', 'product_variation' ),
					'posts_per_page' => '-1',
					'post_status'    => 'publish',
					'fields'         => 'ids',
				);

				if ( '2' == $setting_values['product_selection'] ) {
					if ( empty( $setting_values['selected_products'] ) ) {
						throw new exception( __( 'No data found', 'rewardsystem' ) );
					}

					$args['post__in'] = $setting_values['selected_products'];
				}

				$product_ids = get_posts( $args );
				if ( ! srp_check_is_array( $product_ids ) ) {
					throw new exception( __( 'No Product(s) Found', 'rewardsystem' ) );
				}

				$option_args = array(
					'rs_which_product_selection'      => $setting_values['product_selection'],
					'rs_select_particular_products'   => $setting_values['selected_products'],
					'rs_select_particular_categories' => $setting_values['selected_categories'],
					'rs_local_enable_disable_reward'  => $setting_values['enable_reward'],
					'rs_local_reward_type'            => $setting_values['reward_type'],
					'rs_local_reward_points'          => $setting_values['reward_points'],
					'rs_local_reward_percent'         => $setting_values['reward_percent'],
					'rs_local_minimum_number_of_qty'  => $setting_values['minimum_qty'],
				);

				foreach ( $option_args as $option_name => $option_value ) {
					update_option( sanitize_key( $option_name ), $option_value );
				}

				$this->schedule_action( $product_ids, $setting_values );
				$redirect_url = esc_url_raw(
					add_query_arg(
						array(
							'page'                => 'rewardsystem_callback',
							'rs_action_scheduler' => $this->get_id(),
						),
						SRP_ADMIN_URL
					)
				);
				wp_send_json_success( array( 'redirect_url' => $redirect_url ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
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
			if ( empty( $settings_data ) ) {
				return;
			}

			foreach ( $product_ids as $product_id ) {

				$product_selection = isset( $settings_data['product_selection'] ) ? $settings_data['product_selection'] : '';
				if ( '1' == $product_selection || '2' == $product_selection ) {
					$this->update_settings_for_products( $product_id, $settings_data );
				} elseif ( '3' == $product_selection || '4' == $product_selection ) {
					$this->update_settings_for_categories( $product_id, $settings_data );
				}
			}
		}

		/*
		 * Update settings for products.
		 */

		public function update_settings_for_products( $product_id, $settings_data ) {

			$meta_datas = array(
				// Simple Product Settings.
				'_rewardsystemcheckboxvalue'            => ( '1' === $settings_data['enable_reward'] ) ? 'yes' : 'no',
				'_rewardsystem_options'                 => $settings_data['reward_type'],
				'_rewardsystempoints'                   => $settings_data['reward_points'],
				'_rewardsystempercent'                  => $settings_data['reward_percent'],
				'rs_number_of_qty_for_simple_product'   => $settings_data['minimum_qty'],
				// Variable Product Settings.
				'_enable_reward_points'                 => ( '1' === $settings_data['enable_reward'] ) ? 'yes' : 'no',
				'_select_reward_rule'                   => $settings_data['reward_type'],
				'_reward_points'                        => $settings_data['reward_points'],
				'_reward_percent'                       => $settings_data['reward_percent'],
				'rs_number_of_qty_for_variable_product' => $settings_data['minimum_qty'],
			);

			foreach ( $meta_datas as $meta_key => $meta_value ) {
				update_post_meta( $product_id, sanitize_key( $meta_key ), $meta_value );
			}
		}

		/*
		 * Update settings for categories.
		 */

		public function update_settings_for_categories( $product_id, $settings_data ) {

			$product_categories = get_the_terms( $product_id, 'product_cat' );
			if ( ! srp_check_is_array( $product_categories ) ) {
				return;
			}

			$product_selection = isset( $settings_data['product_selection'] ) ? $settings_data['product_selection'] : '';
			$cat_ids           = array();
			if ( '3' == $product_selection ) {
				$cat_ids = get_terms(
					array(
						'taxonomy' => 'product_cat',
						'fields'   => 'ids',
					)
				);
			} else {
				$cat_ids = isset( $settings_data['selected_categories'] ) ? $settings_data['selected_categories'] : array();
			}

			if ( ! srp_check_is_array( $cat_ids ) ) {
				return;
			}

			$update_product_meta = false;
			foreach ( $product_categories as $category ) {

				if ( ! is_object( $category ) ) {
					continue;
				}

				$category_id = $category->term_id;
				if ( ! in_array( $category_id, $cat_ids ) ) {
					continue;
				}

				$category_meta_datas = array(
					'enable_reward_system_category' => '1' == $settings_data['enable_reward'] ? 'yes' : 'no',
					'enable_rs_rule'                => $settings_data['reward_type'],
					'rs_category_points'            => $settings_data['reward_points'],
					'rs_category_percent'           => $settings_data['reward_percent'],
					'rs_get_min_quantity'           => $settings_data['minimum_qty'],
				);

				foreach ( $category_meta_datas as $category_meta_key => $category_meta_value ) {
					srp_update_term_meta( $category_id, $category_meta_key, $category_meta_value );
				}

				$update_product_meta = true;
			}

			if ( ! $update_product_meta ) {
				return;
			}

			$this->update_settings_for_simple_product_categories( $product_id, $settings_data );
			$this->update_settings_for_variable_product_categories( $product_id, $settings_data );
		}

		/*
		 * Update settings for simple product categories.
		 */

		public function update_settings_for_simple_product_categories( $product_id, $settings_data ) {

			$product_meta_datas = array(
				// Simple Product Settings.
				'_rewardsystemcheckboxvalue'          => '1' == $settings_data['enable_reward'] ? 'yes' : 'no',
				'_rewardsystem_options'               => '',
				'_rewardsystempoints'                 => '',
				'_rewardsystempercent'                => '',
				'rs_number_of_qty_for_simple_product' => '',
			);

			foreach ( $product_meta_datas as $meta_key => $meta_value ) {
				update_post_meta( $product_id, sanitize_key( $meta_key ), $meta_value );
			}
		}

		/*
		 * Update settings for variable product categories.
		 */

		public function update_settings_for_variable_product_categories( $product_id, $settings_data ) {

			$product = wc_get_product( $product_id );
			if ( ! is_object( $product ) ) {
				return;
			}

			$variations = $product->get_children();
			if ( ! srp_check_is_array( $variations ) ) {
				return;
			}

			foreach ( $variations as $variation ) {
				if ( ! is_object( $variation ) ) {
					continue;
				}

				$product_meta_datas = array(
					'_enable_reward_points'               => '1' == $settings_data['enable_reward'] ? 'yes' : 'no',
					'_select_reward_rule'                 => '',
					'_reward_points'                      => '',
					'_reward_percent'                     => '',
					'rs_number_of_qty_for_simple_product' => '',
				);

				foreach ( $product_meta_datas as $meta_key => $meta_value ) {
					update_post_meta( $product_id, sanitize_key( $meta_key ), $meta_value );
				}
			}
		}
	}

}
