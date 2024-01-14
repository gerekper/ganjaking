<?php

/**
 * Referral Purchase Points Bulk Update Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Referral_Purchase_Bulk_Update_Action' ) ) {

	/**
	 * Class.
	 * */
	class RS_Referral_Purchase_Bulk_Update_Action extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_referral_purchase_bulk_update_action';
			$this->action_scheduler_name         = 'rs_referral_purchase_bulk_update_action';
			$this->chunked_action_scheduler_name = 'rs_chunked_referral_purchase_bulk_update_data';
			$this->option_name                   = 'rs_referral_purchase_bulk_update_data';
			$this->settings_option_name          = 'rs_referral_purchase_bulk_update_settings_args';

			// Do ajax action.
			add_action( 'wp_ajax_bulk_update_points_for_referral_purchase', array( $this, 'do_ajax_action' ) );

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __( 'Bulk Updating Referral Purchase Settings for Product(s) is under process...', 'rewardsystem' );
			return $label;
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __( 'Bulk Updating Referral Purchase Settings for Product(s) Completed Successfully.', 'rewardsystem' );
			return $msg;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpreferralsystem' ), SRP_ADMIN_URL );
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
					'product_selection'                     => isset( $_POST[ 'productselection' ] ) ? wc_clean( wp_unslash( $_POST[ 'productselection' ] ) ) : '',
					'selected_products'                     => isset( $_POST[ 'selectedproducts' ] ) ? wc_clean( wp_unslash( $_POST[ 'selectedproducts' ] ) ) : array(),
					'selected_categories'                   => isset( $_POST[ 'selectedcategories' ] ) ? wc_clean( wp_unslash( $_POST[ 'selectedcategories' ] ) ) : array(),
					'enable_referral_reward'                => isset( $_POST[ 'enablereferralreward' ] ) ? wc_clean( wp_unslash( $_POST[ 'enablereferralreward' ] ) ) : '',
					'referral_reward_type'                  => isset( $_POST[ 'referralrewardtype' ] ) ? wc_clean( wp_unslash( $_POST[ 'referralrewardtype' ] ) ) : '',
					'referral_reward_point'                 => isset( $_POST[ 'referralrewardpoint' ] ) ? wc_clean( wp_unslash( $_POST[ 'referralrewardpoint' ] ) ) : '',
					'referral_reward_percent'               => isset( $_POST[ 'referralrewardpercent' ] ) ? wc_clean( wp_unslash( $_POST[ 'referralrewardpercent' ] ) ) : '',
					'referral_point_for_getting_refer'      => isset( $_POST[ 'referralpointforgettingrefer' ] ) ? wc_clean( wp_unslash( $_POST[ 'referralpointforgettingrefer' ] ) ) : '',
					'referral_reward_percent_getting_refer' => isset( $_POST[ 'referralrewardpercentgettingrefer' ] ) ? wc_clean( wp_unslash( $_POST[ 'referralrewardpercentgettingrefer' ] ) ) : '',
				);

				$args = array(
					'post_type'      => array( 'product', 'product_variation' ),
					'posts_per_page' => '-1',
					'post_status'    => 'publish',
					'fields'         => 'ids',
				);

				if ( '2' == $setting_values[ 'product_selection' ] ) {
					if ( empty( $setting_values[ 'selected_products' ] ) ) {
						throw new exception( __( 'No data found', 'rewardsystem' ) );
					}

					$args[ 'post__in' ] = $setting_values[ 'selected_products' ];
				}

				$product_ids = get_posts( $args );
				if ( ! srp_check_is_array( $product_ids ) ) {
					throw new exception( __( 'No Product(s) Found', 'rewardsystem' ) );
				}
								
								$option_args = array(
									'rs_which_product_selection' => $setting_values[ 'product_selection' ],
									'rs_select_particular_products' => $setting_values[ 'selected_products' ],
									'rs_select_particular_categories' => $setting_values[ 'selected_categories' ],
									'rs_local_enable_disable_referral_reward' => $setting_values[ 'enable_referral_reward' ],
									'rs_local_referral_reward_type' => $setting_values[ 'referral_reward_type' ], 
									'rs_local_referral_reward_point' => $setting_values[ 'referral_reward_point' ],
									'rs_local_referral_reward_percent' => $setting_values[ 'referral_reward_percent' ],
									'rs_local_referral_reward_point_for_getting_referred' => $setting_values[ 'referral_point_for_getting_refer' ],
									'rs_local_referral_reward_percent_for_getting_referred' => $setting_values[ 'referral_reward_percent_getting_refer' ],
								);
						
								foreach ($option_args as $option_name => $option_value) {
									update_option(sanitize_key($option_name), $option_value);
								}

								$this->schedule_action( $product_ids, $setting_values );
								$redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback', 'rs_action_scheduler' => $this->get_id() ), SRP_ADMIN_URL ) );
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

				$product_selection = isset( $settings_data[ 'product_selection' ] ) ? $settings_data[ 'product_selection' ] : '';
				if ( '1' == $product_selection || '2' == $product_selection ) {
					$this->update_settings_for_products( $product_id, $settings_data );
				} else if ( '3' == $product_selection || '4' == $product_selection ) {
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
				'_rewardsystemreferralcheckboxvalue'                => '1' == $settings_data[ 'enable_referral_reward' ] ? 'yes' : 'no',
				'_referral_rewardsystem_options'                    => $settings_data[ 'referral_reward_type' ],
				'_referralrewardsystempoints'                       => $settings_data[ 'referral_reward_point' ],
				'_referralrewardsystempercent'                      => $settings_data[ 'referral_reward_percent' ],
				'_referral_rewardsystem_options_getrefer'           => $settings_data[ 'referral_reward_type_getting_refer' ],
				'_referralrewardsystempoints_for_getting_referred'  => $settings_data[ 'referral_point_for_getting_refer' ],
				'_referralrewardsystempercent_for_getting_referred' => $settings_data[ 'referral_reward_percent_getting_refer' ],
				// Variable Product Settings.
				'_enable_referral_reward_points'                    => '1' == $settings_data[ 'enable_referral_reward' ] ? 'yes' : 'no',
				'_select_referral_reward_rule'                      => $settings_data[ 'referral_reward_type' ],
				'_referral_reward_points'                           => $settings_data[ 'referral_reward_point' ],
				'_referral_reward_percent'                          => $settings_data[ 'referral_reward_percent' ],
				'_select_referral_reward_rule_getrefer'             => $settings_data[ 'referral_reward_type_getting_refer' ],
				'_referral_reward_points_getting_refer'             => $settings_data[ 'referral_point_for_getting_refer' ],
				'_referral_reward_percent_getting_refer'            => $settings_data[ 'referral_reward_percent_getting_refer' ],
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

			$product_selection = isset( $settings_data[ 'product_selection' ] ) ? $settings_data[ 'product_selection' ] : '';
			$cat_ids           = array();
			if ( '3' == $product_selection ) {
				$cat_ids = get_terms(
						array(
							'taxonomy' => 'product_cat',
							'fields'   => 'ids',
						)
				);
			} else {
				$cat_ids = isset( $settings_data[ 'selected_categories' ] ) ? $settings_data[ 'selected_categories' ] : array();
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
					'enable_referral_reward_system_category'  => '1' == $settings_data[ 'enable_referral_reward' ] ? 'yes' : 'no',
					'enable_rs_rule'                          => $settings_data[ 'referral_reward_type' ],
					'referral_rs_category_points'             => $settings_data[ 'referral_reward_point' ],
					'referral_rs_category_percent'            => $settings_data[ 'referral_reward_percent' ],
					'referral_enable_rs_rule_refer'           => $settings_data[ 'referral_reward_type_getting_refer' ],
					'referral_rs_category_points_get_refered' => $settings_data[ 'referral_point_for_getting_refer' ],
					'referral_rs_category_percent_get_refer'  => $settings_data[ 'referral_reward_percent_getting_refer' ],
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
				'_rewardsystemreferralcheckboxvalue'                => '1' == $settings_data[ 'enable_referral_reward' ] ? 'yes' : 'no',
				'_referral_rewardsystem_options'                    => '',
				'_referralrewardsystempoints'                       => '',
				'_referralrewardsystempercent'                      => '',
				'_referral_rewardsystem_options_getrefer'           => '',
				'_referralrewardsystempoints_for_getting_referred'  => '',
				'_referralrewardsystempercent_for_getting_referred' => '',
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
					// Variable Product Settings.
					'_enable_referral_reward_points'         => '1' == $settings_data[ 'enable_referral_reward' ] ? 'yes' : 'no',
					'_select_referral_reward_rule'           => '',
					'_referral_reward_points'                => '',
					'_referral_reward_percent'               => '',
					'_select_referral_reward_rule_getrefer'  => '',
					'_referral_reward_points_getting_refer'  => '',
					'_referral_reward_percent_getting_refer' => '',
				);

				foreach ( $product_meta_datas as $meta_key => $meta_value ) {
					update_post_meta( $product_id, sanitize_key( $meta_key ), $meta_value );
				}
			}
		}
	}

}
