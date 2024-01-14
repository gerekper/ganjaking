<?php

/**
 * Social Points Bulk Update Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Social_Points_Bulk_Update_Action' ) ) {

	/**
	 * Class.
	 * */
	class RS_Social_Points_Bulk_Update_Action extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_social_points_bulk_update_action';
			$this->action_scheduler_name         = 'rs_social_points_bulk_update_action';
			$this->chunked_action_scheduler_name = 'rs_chunked_social_points_bulk_update_action';
			$this->option_name                   = 'rs_social_points_bulk_update_data';
			$this->settings_option_name          = 'rs_social_points_bulk_update_settings_args';

			// Do ajax action.
			add_action( 'wp_ajax_bulk_update_social_points_action', array( $this, 'do_ajax_action' ) );

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __( 'Bulk Updating Social Settings for Product(s) is under process...', 'rewardsystem' );
			return $label;
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __( 'Bulk Updating Social Settings for Product(s) Completed Successfully.', 'rewardsystem' );
			return $msg;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpsocialreward' ), SRP_ADMIN_URL );
		}

		/*
		 * Do ajax action.
		 */

		public function do_ajax_action() {
			check_ajax_referer( 'social-reward-bulk-update', 'sumo_security' );

			try {
				if ( ! isset( $_POST ) ) {
					throw new exception( __( 'Invalid data', 'rewardsystem' ) );
				}

				$setting_values = array(
					'product_selection'             => isset( $_POST[ 'productselection' ] ) ? wc_clean( wp_unslash( $_POST[ 'productselection' ] ) ) : '',
					'enable_reward'                 => isset( $_POST[ 'enablereward' ] ) ? wc_clean( wp_unslash( $_POST[ 'enablereward' ] ) ) : '',
					'selected_products'             => isset( $_POST[ 'selectedproducts' ] ) ? wc_clean( wp_unslash( $_POST[ 'selectedproducts' ] ) ) : '',
					'selected_categories'           => isset( $_POST[ 'selectedcategories' ] ) ? wc_clean( wp_unslash( $_POST[ 'selectedcategories' ] ) ) : '',
					'fb_like_reward_type'           => isset( $_POST[ 'fblikerewardtype' ] ) ? wc_clean( wp_unslash( $_POST[ 'fblikerewardtype' ] ) ) : '',
					'fb_like_reward_points'         => isset( $_POST[ 'fblikerewardpoints' ] ) ? wc_clean( wp_unslash( $_POST[ 'fblikerewardpoints' ] ) ) : '',
					'fb_like_reward_percent'        => isset( $_POST[ 'fblikerewardpercent' ] ) ? wc_clean( wp_unslash( $_POST[ 'fblikerewardpercent' ] ) ) : '',
					'fb_share_reward_type'          => isset( $_POST[ 'fbsharerewardtype' ] ) ? wc_clean( wp_unslash( $_POST[ 'fbsharerewardtype' ] ) ) : '',
					'fb_share_reward_points'        => isset( $_POST[ 'fbsharerewardpoints' ] ) ? wc_clean( wp_unslash( $_POST[ 'fbsharerewardpoints' ] ) ) : '',
					'fb_share_reward_percent'       => isset( $_POST[ 'fbsharerewardpercent' ] ) ? wc_clean( wp_unslash( $_POST[ 'fbsharerewardpercent' ] ) ) : '',
					'twitter_reward_type'           => isset( $_POST[ 'twitterrewardtype' ] ) ? wc_clean( wp_unslash( $_POST[ 'twitterrewardtype' ] ) ) : '',
					'twitter_reward_points'         => isset( $_POST[ 'twitterrewardpoints' ] ) ? wc_clean( wp_unslash( $_POST[ 'twitterrewardpoints' ] ) ) : '',
					'twitter_reward_percent'        => isset( $_POST[ 'twitterrewardpercent' ] ) ? wc_clean( wp_unslash( $_POST[ 'twitterrewardpercent' ] ) ) : '',
					'gplus_reward_type'             => isset( $_POST[ 'gplusrewardtype' ] ) ? wc_clean( wp_unslash( $_POST[ 'gplusrewardtype' ] ) ) : '',
					'gplus_reward_points'           => isset( $_POST[ 'gplusrewardpoints' ] ) ? wc_clean( wp_unslash( $_POST[ 'gplusrewardpoints' ] ) ) : '',
					'gplus_reward_percent'          => isset( $_POST[ 'gplusrewardpercent' ] ) ? wc_clean( wp_unslash( $_POST[ 'gplusrewardpercent' ] ) ) : '',
					'vk_reward_type'                => isset( $_POST[ 'vkrewardtype' ] ) ? wc_clean( wp_unslash( $_POST[ 'vkrewardtype' ] ) ) : '',
					'vk_reward_points'              => isset( $_POST[ 'vkrewardpoints' ] ) ? wc_clean( wp_unslash( $_POST[ 'vkrewardpoints' ] ) ) : '',
					'vk_reward_percent'             => isset( $_POST[ 'vkrewardpercent' ] ) ? wc_clean( wp_unslash( $_POST[ 'vkrewardpercent' ] ) ) : '',
					'twitter_follow_reward_type'    => isset( $_POST[ 'twitterfollowrewardtype' ] ) ? wc_clean( wp_unslash( $_POST[ 'twitterfollowrewardtype' ] ) ) : '',
					'twitter_follow_reward_points'  => isset( $_POST[ 'twitterfollowrewardpoints' ] ) ? wc_clean( wp_unslash( $_POST[ 'twitterfollowrewardpoints' ] ) ) : '',
					'twitter_follow_reward_percent' => isset( $_POST[ 'twitterfollowrewardpercent' ] ) ? wc_clean( wp_unslash( $_POST[ 'twitterfollowrewardpercent' ] ) ) : '',
					'instagram_reward_type'         => isset( $_POST[ 'instagramrewardtype' ] ) ? wc_clean( wp_unslash( $_POST[ 'instagramrewardtype' ] ) ) : '',
					'instagram_reward_points'       => isset( $_POST[ 'instagramrewardpoints' ] ) ? wc_clean( wp_unslash( $_POST[ 'instagramrewardpoints' ] ) ) : '',
					'instagram_reward_percent'      => isset( $_POST[ 'instagramrewardpercent' ] ) ? wc_clean( wp_unslash( $_POST[ 'instagramrewardpercent' ] ) ) : '',
					'ok_reward_type'                => isset( $_POST[ 'okrewardtype' ] ) ? wc_clean( wp_unslash( $_POST[ 'okrewardtype' ] ) ) : '',
					'ok_reward_points'              => isset( $_POST[ 'okrewardpoints' ] ) ? wc_clean( wp_unslash( $_POST[ 'okrewardpoints' ] ) ) : '',
					'ok_reward_percent'             => isset( $_POST[ 'okrewardpercent' ] ) ? wc_clean( wp_unslash( $_POST[ 'okrewardpercent' ] ) ) : '',
				);

				$setting_values[ 'enable_reward' ] = '1' == $setting_values[ 'enable_reward' ] ? 'yes' : 'no';

				$args = array(
					'post_type'      => 'product',
					'posts_per_page' => '-1',
					'post_status'    => 'publish',
					'fields'         => 'ids',
				);

				if ( '2' == $setting_values[ 'product_selection' ] ) {
					if ( empty( $setting_values[ 'selected_products' ] ) ) {
						throw new exception( __( 'No data found', 'rewardsystem' ) );
					}

					$args[ 'include' ] = $setting_values[ 'selected_products' ];
				}

				$product_ids = get_posts( $args );
				if ( ! srp_check_is_array( $product_ids ) ) {
					throw new exception( __( 'No Product(s) Found', 'rewardsystem' ) );
				}
								
								$option_args = array(
					'rs_which_social_product_selection'       => $setting_values['product_selection'],
					'rs_local_enable_disable_social_reward'   => $setting_values['enable_reward'],
					'rs_select_particular_social_products'    => $setting_values['selected_products'],
					'rs_select_particular_social_categories'  => $setting_values['selected_categories'],
					'rs_local_reward_type_for_facebook'       => $setting_values['fb_like_reward_type'],
					'rs_local_reward_points_facebook'         => $setting_values['fb_like_reward_points'],
					'rs_local_reward_percent_facebook'        => $setting_values['fb_like_reward_percent'],
					'rs_local_reward_type_for_facebook_share' => $setting_values['fb_share_reward_type'],
					'rs_local_reward_points_facebook_share'   => $setting_values['fb_share_reward_points'],
					'rs_local_reward_percent_facebook_share'  => $setting_values['fb_share_reward_percent'],
					'rs_local_reward_type_for_twitter'        => $setting_values['twitter_reward_type'],
					'rs_local_reward_points_twitter'          => $setting_values['twitter_reward_points'],
					'rs_local_reward_percent_twitter'         => $setting_values['twitter_reward_percent'],
					'rs_local_reward_type_for_google'         => $setting_values['gplus_reward_type'],
					'rs_local_reward_points_google'           => $setting_values['gplus_reward_points'],
					'rs_local_reward_percent_google'          => $setting_values['gplus_reward_percent'],
					'rs_local_reward_type_for_vk'             => $setting_values['vk_reward_type'],
					'rs_local_reward_points_vk'               => $setting_values['vk_reward_points'],
					'rs_local_reward_percent_vk'              => $setting_values['vk_reward_percent'],
					'rs_local_reward_type_for_twitter_follow' => $setting_values['twitter_follow_reward_type'],
					'rs_local_reward_points_twitter_follow'   => $setting_values['twitter_follow_reward_points'],
					'rs_local_reward_percent_twitter_follow'  => $setting_values['twitter_follow_reward_percent'],
					'rs_local_reward_type_for_instagram'      => $setting_values['instagram_reward_type'],
					'rs_local_reward_points_instagram'        => $setting_values['instagram_reward_points'],
					'rs_local_reward_percent_instagram'       => $setting_values['instagram_reward_percent'],
					'rs_local_reward_type_for_ok_follow'      => $setting_values['ok_reward_type'],
					'rs_local_reward_points_ok_follow'        => $setting_values['ok_reward_points'],
					'rs_local_reward_percent_ok_follow'       => $setting_values['ok_reward_percent'],
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
				'_socialrewardsystemcheckboxvalue'            => $settings_data[ 'enable_reward' ],
				'_social_rewardsystem_options_facebook'       => $settings_data[ 'fb_like_reward_type' ],
				'_socialrewardsystempoints_facebook'          => $settings_data[ 'fb_like_reward_points' ],
				'_socialrewardsystempercent_facebook'         => $settings_data[ 'fb_like_reward_percent' ],
				'_social_rewardsystem_options_facebook_share' => $settings_data[ 'fb_share_reward_type' ],
				'_socialrewardsystempoints_facebook_share'    => $settings_data[ 'fb_share_reward_points' ],
				'_socialrewardsystempercent_facebook_share'   => $settings_data[ 'fb_share_reward_percent' ],
				'_social_rewardsystem_options_twitter'        => $settings_data[ 'twitter_reward_type' ],
				'_socialrewardsystempoints_twitter'           => $settings_data[ 'twitter_reward_points' ],
				'_socialrewardsystempercent_twitter'          => $settings_data[ 'twitter_reward_percent' ],
				'_social_rewardsystem_options_twitter_follow' => $settings_data[ 'twitter_follow_reward_type' ],
				'_socialrewardsystempoints_twitter_follow'    => $settings_data[ 'twitter_follow_reward_points' ],
				'_socialrewardsystempercent_twitter_follow'   => $settings_data[ 'twitter_follow_reward_percent' ],
				'_social_rewardsystem_options_ok_follow'      => $settings_data[ 'ok_reward_type' ],
				'_socialrewardsystempoints_ok_follow'         => $settings_data[ 'ok_reward_points' ],
				'_socialrewardsystempercent_ok_follow'        => $settings_data[ 'ok_reward_percent' ],
				'_social_rewardsystem_options_google'         => $settings_data[ 'gplus_reward_type' ],
				'_socialrewardsystempoints_google'            => $settings_data[ 'gplus_reward_points' ],
				'_socialrewardsystempercent_google'           => $settings_data[ 'gplus_reward_percent' ],
				'_social_rewardsystem_options_vk'             => $settings_data[ 'vk_reward_type' ],
				'_socialrewardsystempoints_vk'                => $settings_data[ 'vk_reward_points' ],
				'_socialrewardsystempercent_vk'               => $settings_data[ 'vk_reward_percent' ],
				'_social_rewardsystem_options_instagram'      => $settings_data[ 'instagram_reward_type' ],
				'_socialrewardsystempoints_instagram'         => $settings_data[ 'instagram_reward_points' ],
				'_socialrewardsystempercent_instagram'        => $settings_data[ 'instagram_reward_percent' ],
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
			if ( $product_selection ) {
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
					'_socialrewardsystemcheckboxvalue'            => $settings_data[ 'enable_reward' ],
					'_social_rewardsystem_options_facebook'       => $settings_data[ 'fb_like_reward_type' ],
					'_socialrewardsystempoints_facebook'          => $settings_data[ 'fb_like_reward_points' ],
					'_socialrewardsystempercent_facebook'         => $settings_data[ 'fb_like_reward_percent' ],
					'_social_rewardsystem_options_facebook_share' => $settings_data[ 'fb_share_reward_type' ],
					'_socialrewardsystempoints_facebook_share'    => $settings_data[ 'fb_share_reward_points' ],
					'_socialrewardsystempercent_facebook_share'   => $settings_data[ 'fb_share_reward_percent' ],
					'_social_rewardsystem_options_twitter'        => $settings_data[ 'twitter_reward_type' ],
					'_socialrewardsystempoints_twitter'           => $settings_data[ 'twitter_reward_points' ],
					'_socialrewardsystempercent_twitter'          => $settings_data[ 'twitter_reward_percent' ],
					'_social_rewardsystem_options_twitter_follow' => $settings_data[ 'twitter_follow_reward_type' ],
					'_socialrewardsystempoints_twitter_follow'    => $settings_data[ 'twitter_follow_reward_points' ],
					'_socialrewardsystempercent_twitter_follow'   => $settings_data[ 'twitter_follow_reward_percent' ],
					'_social_rewardsystem_options_ok_follow'      => $settings_data[ 'ok_reward_type' ],
					'_socialrewardsystempoints_ok_follow'         => $settings_data[ 'ok_reward_points' ],
					'_socialrewardsystempercent_ok_follow'        => $settings_data[ 'ok_reward_percent' ],
					'_social_rewardsystem_options_google'         => $settings_data[ 'gplus_reward_type' ],
					'_socialrewardsystempoints_google'            => $settings_data[ 'gplus_reward_points' ],
					'_socialrewardsystempercent_google'           => $settings_data[ 'gplus_reward_percent' ],
					'_social_rewardsystem_options_vk'             => $settings_data[ 'vk_reward_type' ],
					'_socialrewardsystempoints_vk'                => $settings_data[ 'vk_reward_points' ],
					'_socialrewardsystempercent_vk'               => $settings_data[ 'vk_reward_percent' ],
					'_social_rewardsystem_options_instagram'      => $settings_data[ 'instagram_reward_type' ],
					'_socialrewardsystempoints_instagram'         => $settings_data[ 'instagram_reward_points' ],
					'_socialrewardsystempercent_instagram'        => $settings_data[ 'instagram_reward_percent' ],
				);

				foreach ( $category_meta_datas as $category_meta_key => $category_meta_value ) {
					srp_update_term_meta( $category_id, $category_meta_key, $category_meta_value );
				}

				$update_product_meta = true;
			}

			if ( ! $update_product_meta ) {
				return;
			}

			$product_meta_datas = array(
				'_socialrewardsystemcheckboxvalue'            => $settings_data[ 'enable_reward' ],
				'_social_rewardsystem_options_facebook'       => '',
				'_socialrewardsystempoints_facebook'          => '',
				'_socialrewardsystempercent_facebook'         => '',
				'_social_rewardsystem_options_facebook_share' => '',
				'_socialrewardsystempoints_facebook_share'    => '',
				'_socialrewardsystempercent_facebook_share'   => '',
				'_social_rewardsystem_options_twitter'        => '',
				'_socialrewardsystempoints_twitter'           => '',
				'_socialrewardsystempercent_twitter'          => '',
				'_social_rewardsystem_options_twitter_follow' => '',
				'_socialrewardsystempoints_twitter_follow'    => '',
				'_socialrewardsystempercent_twitter_follow'   => '',
				'_social_rewardsystem_options_ok_follow'      => '',
				'_socialrewardsystempoints_ok_follow'         => '',
				'_socialrewardsystempercent_ok_follow'        => '',
				'_social_rewardsystem_options_google'         => '',
				'_socialrewardsystempoints_google'            => '',
				'_socialrewardsystempercent_google'           => '',
				'_social_rewardsystem_options_vk'             => '',
				'_socialrewardsystempoints_vk'                => '',
				'_socialrewardsystempercent_vk'               => '',
				'_social_rewardsystem_options_instagram'      => '',
				'_socialrewardsystempoints_instagram'         => '',
				'_socialrewardsystempercent_instagram'        => '',
			);

			foreach ( $product_meta_datas as $meta_key => $meta_value ) {
				update_post_meta( $product_id, sanitize_key( $meta_key ), $meta_value );
			}
		}
	}

}
