<?php
/*
 * Simple Product Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RSCategoryField' ) ) {

	class RSCategoryField {

		public static function init() {
			add_action( 'product_cat_add_form_fields', array( __CLASS__, 'rs_admin_setting_for_category_page' ) );

			add_action( 'product_cat_edit_form_fields', array( __CLASS__, 'rs_edit_admin_settings_for_category_page' ), 10, 2 );

			add_action( 'created_term', array( __CLASS__, 'rs_save_admin_settings_for_category_page' ), 10, 3 );

			add_action( 'edit_term', array( __CLASS__, 'rs_save_admin_settings_for_category_page' ), 10, 3 );
		}

		public static function rs_admin_setting_for_category_page() {
			if ( 'yes' == get_option( 'rs_point_price_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_points_price' ) ) {
				?>
				<h4><?php esc_html_e( 'Category Settings for Point Price', 'rewardsystem' ); ?></h4>
				<div class="form-field">
					<label for="enable_point_price_category"><?php esc_html_e( 'Enable Point Pricing', 'rewardsystem' ); ?></label>
					<select id="enable_point_price_category" name="enable_point_price_category" class="postform srp-point-price-enable-category" data-parent="form">
						<option value="yes"><?php esc_html_e( 'Enable', 'rewardsystem' ); ?></option>
						<option value="no"><?php esc_html_e( 'Disable', 'rewardsystem' ); ?></option>
					</select>
					<p>
					<?php
						esc_html_e(
							'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
							. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
							'rewardsystem'
						);
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="pricing_category_types"><?php esc_html_e( 'Pricing Display Type', 'rewardsystem' ); ?></label>
					<select id = "pricing_category_types" name="pricing_category_types" class="postform srp-show-if-point-price-enable-category srp-point-pricing-display-type" data-parent="div">
						<option value = "1"><?php esc_html_e( 'Currency and Point Price', 'rewardsystem' ); ?></option>
						<option value = "2"><?php esc_html_e( 'Only Point Price', 'rewardsystem' ); ?></option>
					</select>
					<p>
					<?php
						esc_html_e(
							'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
							. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
							'rewardsystem'
						);
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="point_price_category_type"><?php esc_html_e( 'Point Price Type', 'rewardsystem' ); ?></label>
					<select id="point_price_category_type" name="point_price_category_type" class="postform srp-show-if-point-price-enable-category srp-point-price-type" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'Based On Conversion', 'rewardsystem' ); ?></option>
					</select>
					<p>
					<?php
						esc_html_e(
							'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
							. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
							'rewardsystem'
						);
					?>
						</p>
				</div>

				<div class="form-field">
					<label for="rs_category_points_price"><?php esc_html_e( 'By fixed Points Price', 'rewardsystem' ); ?></label>
					<input type="text" name="rs_category_points_price" id="rs_category_points_price" value="" class="srp-show-if-point-price-enable-category srp-point-price-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
			<?php } if ( 'yes' == get_option( 'rs_product_purchase_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) { ?>
				<h3><?php esc_html_e( 'Category Settings for Reward Points', 'rewardsystem' ); ?></h3>
				<div class="form-field">
					<label for="enable_reward_system_category"><?php esc_html_e( 'Enable SUMO Reward Points for Product Purchase', 'rewardsystem' ); ?></label>
					<select id="enable_reward_system_category" name="enable_reward_system_category" class="postform enable-reward-system-category" data-parent="form">
						<option value="yes"><?php esc_html_e( 'Enable', 'rewardsystem' ); ?></option>
						<option value="no"><?php esc_html_e( 'Disable', 'rewardsystem' ); ?></option>
					</select>
					<p>
					<?php
						esc_html_e(
							'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
							. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
							'rewardsystem'
						);
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="enable_rs_rule"><?php esc_html_e( 'Reward Type', 'rewardsystem' ); ?></label>
					<select id="enable_rs_rule" name="enable_rs_rule" class="postform enable-rs-rule srp-show-if-enable-reward-on-category" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>
				<div class="form-field">
					<label for="rs_category_points"><?php esc_html_e( 'Reward Points', 'rewardsystem' ); ?></label>
					<input type="text" name="rs_category_points" id="rs_category_points" value="" class="srp-show-if-enable-reward-on-category"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="rs_category_percent"><?php esc_html_e( 'Reward Percent in %', 'rewardsystem' ); ?></label>
					<input type="text" name="rs_category_percent" id="rs_category_percent" value="" class="srp-show-if-enable-reward-on-category"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="rs_get_min_quantity"><?php esc_html_e( 'Minimum Quantity required to Earn Points', 'rewardsystem' ); ?></label>
					<input type="number" name="rs_get_min_quantity" id="rs_get_min_quantity" value="" min="1" class="srp-show-if-enable-reward-on-category"/>
				</div>
			<?php } if ( 'yes' == get_option( 'rs_referral_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) ) { ?>
				<div class="form-field">
					<label for="enable_referral_reward_system_category"><?php esc_html_e( 'Enable Referral Reward Points for Product Purchase', 'rewardsystem' ); ?></label>
					<select id="enable_referral_reward_system_category" name="enable_referral_reward_system_category" class="postform srp-enable-referral-system-category"  data-parent="form">
						<option value="yes"><?php esc_html_e( 'Enable', 'rewardsystem' ); ?></option>
						<option value="no"><?php esc_html_e( 'Disable', 'rewardsystem' ); ?></option>
					</select>
					<p>
					<?php
						esc_html_e(
							'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
							. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
							'rewardsystem'
						);
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="referral_enable_rs_rule"><?php esc_html_e( 'Referral Reward Type', 'rewardsystem' ); ?></label>
					<select id="referral_enable_rs_rule" name="referral_enable_rs_rule" class="postform srp-referral-type srp-show-if-referral-enable-category" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>
				<div class="form-field">
					<label for="referral_rs_category_points"><?php esc_html_e( 'Referral Reward Points', 'rewardsystem' ); ?></label>
					<input type="text" name="referral_rs_category_points" id="referral_rs_category_points" value="" class="srp-show-if-referral-enable-category srp-referral-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="referral_rs_category_percent"><?php esc_html_e( 'Reward Percent in %', 'rewardsystem' ); ?></label>
					<input type="text" name="referral_rs_category_percent" id="referral_rs_category_percent" value="" class="srp-show-if-referral-enable-category srp-referral-percent"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>

				<div class="form-field">
					<label for="referral_enable_rs_rule_refer"><?php esc_html_e( 'Reward Type for Getting Referred', 'rewardsystem' ); ?></label>
					<select id="referral_enable_rs_rule_refer" name="referral_enable_rs_rule_refer" class="postform srp-getting-referred-type srp-show-if-referral-enable-category" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>

				<div class="form-field">
					<label for="referral_rs_category_points_get_refered"><?php esc_html_e( ' Reward Points for Getting Referred', 'rewardsystem' ); ?></label>
					<input type="text" name="referral_rs_category_points_get_refered" id="referral_rs_category_points_get_refered" value="" class="srp-show-if-referral-enable-category srp-getrefer-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>

				<div class="form-field">
					<label for="referral_rs_category_percent_get_refer"><?php esc_html_e( 'Reward Points in Percent % for Getting Referred', 'rewardsystem' ); ?></label>
					<input type="text" name="referral_rs_category_percent_get_refer" id="referral_rs_category_percent_get_refer" value="" class="srp-show-if-referral-enable-category srp-getrefer-percent"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
			<?php } if ( 'yes' == get_option( 'rs_social_reward_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_social_reward' ) ) { ?>
				<div class="form-field">
					<label for="enable_social_reward_system_category"><?php esc_html_e( 'Enable SUMO Reward Points for Social Promotion', 'rewardsystem' ); ?></label>
					<select id="enable_social_reward_system_category" name="enable_social_reward_system_category" class="postform srp-enable-social-reward-category" data-parent="form">
						<option value="yes"><?php esc_html_e( 'Enable', 'rewardsystem' ); ?></option>
						<option value="no"><?php esc_html_e( 'Disable', 'rewardsystem' ); ?></option>
					</select>
					<p>
						<?php
						esc_html_e(
							'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
							. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
							'rewardsystem'
						);
						?>
					</p>
				</div>
				<!-- Social Rewards Field for Facebook in Category Start -->
				<div class="form-field">
					<label for="social_facebook_enable_rs_rule"><?php esc_html_e( 'Facebook Like Reward Type', 'rewardsystem' ); ?></label>
					<select id="social_facebook_enable_rs_rule" name="social_facebook_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-facebook-like-reward-type" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>
				<div class="form-field">
					<label for="social_facebook_rs_category_points"><?php esc_html_e( 'Facebook Like Reward Points', 'rewardsystem' ); ?></label>
					<input type="text" name="social_facebook_rs_category_points" id="social_facebook_rs_category_points" value="" class="srp-show-if-social-reward-enable-category srp-facebook-like-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="social_facebook_rs_category_percent"><?php esc_html_e( 'Facebook Like Reward Points in Percent %' ); ?></label>
					<input type="text" name="social_facebook_rs_category_percent" id="social_facebook_rs_category_percent" value="" class="srp-show-if-social-reward-enable-category srp-facebook-like-percent"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<!-- Social Rewards Field for Facebook in Category which is End -->

				<div class="form-field">
					<label for="social_facebook_share_enable_rs_rule"><?php esc_html_e( 'Facebook Share Reward Type', 'rewardsystem' ); ?></label>
					<select id="social_facebook_share_enable_rs_rule" name="social_facebook_share_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-facebook-share-reward-type" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>
				<div class="form-field">
					<label for="social_facebook_share_rs_category_points"><?php esc_html_e( 'Facebook Share Reward Points', 'rewardsystem' ); ?></label>
					<input type="text" name="social_facebook_share_rs_category_points" id="social_facebook_share_rs_category_points" value="" class="srp-show-if-social-reward-enable-category srp-facebook-share-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="social_facebook_share_rs_category_percent"><?php esc_html_e( 'Facebook Share Reward Points in Percent %' ); ?></label>
					<input type="text" name="social_facebook_share_rs_category_percent" id="social_facebook_share_rs_category_percent" value="" class="srp-show-if-social-reward-enable-category srp-facebook-share-percent"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<!-- Social Rewards Field for Twitter in Category Start -->
				<div class="form-field">
					<label for="social_twitter_enable_rs_rule"><?php esc_html_e( 'Twitter Tweet Reward Type', 'rewardsystem' ); ?></label>
					<select id="social_twitter_enable_rs_rule" name="social_twitter_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-twitter-tweet-reward-type" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>
				<div class="form-field">
					<label for="social_twitter_rs_category_points"><?php esc_html_e( 'Twitter Tweet Reward Points', 'rewardsystem' ); ?></label>
					<input type="text" name="social_twitter_rs_category_points" id="social_twitter_rs_category_points" value="" class="srp-show-if-social-reward-enable-category srp-twitter-tweet-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="social_twitter_rs_category_percent"><?php esc_html_e( 'Twitter Tweet Reward Percent %' ); ?></label>
					<input type="text" name="social_twitter_rs_category_percent" id="social_twitter_rs_category_percent" value="" class="srp-show-if-social-reward-enable-category srp-twitter-tweet-percent"/>
				</div>
				<!-- Social Rewards Field for Twitter in Category which is End -->
				<div class="form-field">
					<label for="social_twitter_follow_enable_rs_rule"><?php esc_html_e( 'Twitter Follow Reward Type', 'rewardsystem' ); ?></label>
					<select id="social_twitter_follow_enable_rs_rule" name="social_twitter_follow_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-twitter-follow-reward-type" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>
				<div class="form-field">
					<label for="social_twitter_follow_rs_category_points"><?php esc_html_e( 'Twitter Follow Reward Points', 'rewardsystem' ); ?></label>
					<input type="text" name="social_twitter_follow_rs_category_points" id="social_twitter_follow_rs_category_points" value="" class="srp-show-if-social-reward-enable-category srp-twitter-follow-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="social_twitter_follow_rs_category_percent"><?php esc_html_e( 'Twitter Follow Reward Percent %' ); ?></label>
					<input type="text" name="social_twitter_follow_rs_category_percent" id="social_twitter_follow_rs_category_percent" value="" class="srp-show-if-social-reward-enable-category srp-twitter-follow-percent"/>
				</div>

				<!-- Social Rewards Field for Google in Category Start -->
				<div class="form-field">
					<label for="social_google_enable_rs_rule"><?php esc_html_e( 'Google+1 Reward Type', 'rewardsystem' ); ?></label>
					<select id="social_google_enable_rs_rule" name="social_google_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-google-plus-reward-type" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>
				<div class="form-field">
					<label for="social_google_rs_category_points"><?php esc_html_e( 'Google+1 Reward Points', 'rewardsystem' ); ?></label>
					<input type="text" name="social_google_rs_category_points" id="social_google_rs_category_points" value="" class="srp-show-if-social-reward-enable-category srp-google-plus-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="social_google_rs_category_percent"><?php esc_html_e( 'Google+1 Reward Percent %' ); ?></label>
					<input type="text" name="social_google_rs_category_percent" id="social_google_rs_category_percent" value="" class="srp-show-if-social-reward-enable-category srp-google-plus-percent"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<!-- Social Rewards Field for Google in Category which is End -->
				<!-- Social Rewards Field for VK in Category Start -->
				<div class="form-field">
					<label for="social_vk_enable_rs_rule"><?php esc_html_e( 'VK.com Like Reward Type', 'rewardsystem' ); ?></label>
					<select id="social_vk_enable_rs_rule" name="social_vk_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-vk-like-reward-type" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>
				<div class="form-field">
					<label for="social_vk_rs_category_points"><?php esc_html_e( 'VK.com Like Reward Points', 'rewardsystem' ); ?></label>
					<input type="text" name="social_vk_rs_category_points" id="social_vk_rs_category_points" value="" class="srp-show-if-social-reward-enable-category srp-vk-like-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="social_vk_rs_category_percent"><?php esc_html_e( 'VK.com Like Reward Percent %' ); ?></label>
					<input type="text" name="social_vk_rs_category_percent" id="social_vk_rs_category_percent" value="" class="srp-show-if-social-reward-enable-category srp-vk-like-percent"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<!-- Social Rewards Field for VK in Category which is End -->
				<!-- Social Rewards Field for Instagram in Category which is Start -->
				<div class="form-field">
					<label for="social_instagram_enable_rs_rule"><?php esc_html_e( 'Instagram Reward Type', 'rewardsystem' ); ?></label>
					<select id="social_instagram_enable_rs_rule" name="social_instagram_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-instagram-reward-type" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>
				<div class="form-field">
					<label for="social_instagram_rs_category_points"><?php esc_html_e( 'Instagram Reward Points', 'rewardsystem' ); ?></label>
					<input type="text" name="social_instagram_rs_category_points" id="social_instagram_rs_category_points" value="" class="srp-show-if-social-reward-enable-category srp-instagram-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="social_instagram_rs_category_percent"><?php esc_html_e( 'Instagram Reward Percent %' ); ?></label>
					<input type="text" name="social_instagram_rs_category_percent" id="social_instagram_rs_category_percent" value="" class="srp-show-if-social-reward-enable-category srp-instagram-percent"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<!-- Social Rewards Field for Instagram in Category which is End -->
				<!-- Social Rewards Field for OK.ru in Category which is Start -->
				<div class="form-field">
					<label for="social_ok_follow_enable_rs_rule"><?php esc_html_e( 'OK.ru Share Reward Type', 'rewardsystem' ); ?></label>
					<select id="social_ok_follow_enable_rs_rule" name="social_ok_follow_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-ok-share-reward-type" data-parent="div">
						<option value="1"><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
						<option value="2"><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
					</select>
				</div>
				<div class="form-field">
					<label for="social_ok_follow_rs_category_points"><?php esc_html_e( 'OK.ru Share Reward Points', 'rewardsystem' ); ?></label>
					<input type="text" name="social_ok_follow_rs_category_points" id="social_ok_follow_rs_category_points" value="" class="srp-show-if-social-reward-enable-category srp-ok-share-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
						</p>
				</div>
				<div class="form-field">
					<label for="social_ok_follow_rs_category_percent"><?php esc_html_e( 'OK.ru Share Reward Percent %' ); ?></label>
					<input type="text" name="social_ok_follow_rs_category_percent" id="social_ok_follow_rs_category_percent" value="" class="srp-show-if-social-reward-enable-category srp-ok-share-percent"/>
				</div>
				<!-- Social Rewards Field for OK.ru in Category which is End -->
				<?php
			}
		}

		public static function rs_edit_admin_settings_for_category_page( $term, $taxonomy ) {
			$enablesocialvalue   = srp_term_meta( $term->term_id, 'enable_social_reward_system_category' );
			$enablevalueforpoint = srp_term_meta( $term->term_id, 'enable_point_price_category' );
			$pointprice          = srp_term_meta( $term->term_id, 'rs_category_points_price' );
			$pointpricetype      = srp_term_meta( $term->term_id, 'point_price_category_type' );
			$enable_pricing_type = srp_term_meta( $term->term_id, 'pricing_category_types' );

			$enablevalue           = srp_term_meta( $term->term_id, 'enable_reward_system_category' );
			$display_type          = srp_term_meta( $term->term_id, 'enable_rs_rule' );
			$rewardpoints          = srp_term_meta( $term->term_id, 'rs_category_points' );
			$rewardpercent         = srp_term_meta( $term->term_id, 'rs_category_percent' );
			$min_qty               = srp_term_meta( $term->term_id, 'rs_get_min_quantity' );
			$enablereferralvalue   = srp_term_meta( $term->term_id, 'enable_referral_reward_system_category' );
			$referralrewardpoints  = srp_term_meta( $term->term_id, 'referral_rs_category_points' );
			$referralrewardpercent = srp_term_meta( $term->term_id, 'referral_rs_category_percent' );
			$referralrewardrule    = srp_term_meta( $term->term_id, 'referral_enable_rs_rule' );

			$referralrewardpoints_get_refer  = srp_term_meta( $term->term_id, 'referral_rs_category_points_get_refered' );
			$referralrewardpercent_get_refer = srp_term_meta( $term->term_id, 'referral_rs_category_percent_get_refer' );
			$referral_enable_rs_rule_refer   = srp_term_meta( $term->term_id, 'referral_enable_rs_rule_refer' );

			$socialfacebooktype    = srp_term_meta( $term->term_id, 'social_facebook_enable_rs_rule' );
			$socialfacebookpoints  = srp_term_meta( $term->term_id, 'social_facebook_rs_category_points' );
			$socialfacebookpercent = srp_term_meta( $term->term_id, 'social_facebook_rs_category_percent' );

			$socialfacebooktype_share    = srp_term_meta( $term->term_id, 'social_facebook_share_enable_rs_rule' );
			$socialfacebookpoints_share  = srp_term_meta( $term->term_id, 'social_facebook_share_rs_category_points' );
			$socialfacebookpercent_share = srp_term_meta( $term->term_id, 'social_facebook_share_rs_category_percent' );

			$socialtwittertype           = srp_term_meta( $term->term_id, 'social_twitter_enable_rs_rule' );
			$socialtwitterpoints         = srp_term_meta( $term->term_id, 'social_twitter_rs_category_points' );
			$socialtwitterpercent        = srp_term_meta( $term->term_id, 'social_twitter_rs_category_percent' );
			$socialtwittertype_follow    = srp_term_meta( $term->term_id, 'social_twitter_follow_enable_rs_rule' );
			$socialtwitterpoints_follow  = srp_term_meta( $term->term_id, 'social_twitter_follow_rs_category_points' );
			$socialtwitterpercent_follow = srp_term_meta( $term->term_id, 'social_twitter_follow_rs_category_percent' );

			$socialoktype_follow    = srp_term_meta( $term->term_id, 'social_ok_follow_enable_rs_rule' );
			$socialokpoints_follow  = srp_term_meta( $term->term_id, 'social_ok_follow_rs_category_points' );
			$socialokpercent_follow = srp_term_meta( $term->term_id, 'social_ok_follow_rs_category_percent' );

			$socialgoogletype    = srp_term_meta( $term->term_id, 'social_google_enable_rs_rule' );
			$socialgooglepoints  = srp_term_meta( $term->term_id, 'social_google_rs_category_points' );
			$socialgooglepercent = srp_term_meta( $term->term_id, 'social_google_rs_category_percent' );

			$socialvktype    = srp_term_meta( $term->term_id, 'social_vk_enable_rs_rule' );
			$socialvkpoints  = srp_term_meta( $term->term_id, 'social_vk_rs_category_points' );
			$socialvkpercent = srp_term_meta( $term->term_id, 'social_vk_rs_category_percent' );

			$socialinstagramtype    = srp_term_meta( $term->term_id, 'social_instagram_enable_rs_rule' );
			$socialinstagrampoints  = srp_term_meta( $term->term_id, 'social_instagram_rs_category_points' );
			$socialinstagrampercent = srp_term_meta( $term->term_id, 'social_instagram_rs_category_percent' );
			if ( 'yes' == get_option( 'rs_point_price_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_points_price' ) ) {
				?>
				<tr class="form-field">
					<th scope="row" valign="top"><label> <?php esc_html_e( 'Enable Point Pricing', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="enable_point_price_category" name="enable_point_price_category" class="postform srp-point-price-enable-category" data-parent="div">
							<option value="yes"<?php selected( 'yes', $enablevalueforpoint ); ?>><?php esc_html_e( 'Enable', 'rewardsystem' ); ?></option>
							<option value="no"<?php selected( 'no', $enablevalueforpoint ); ?>><?php esc_html_e( 'Disable', 'rewardsystem' ); ?> </option>
						</select>
						<p>
						<?php
							esc_html_e(
								'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
								. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
								'rewardsystem'
							);
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label> <?php esc_html_e( 'Pricing Display Type', 'rewardsystem' ); ?></label></th>
					<td>
						<select id = "pricing_category_types" name="pricing_category_types" class="postform srp-show-if-point-price-enable-category srp-point-pricing-display-type" data-parent="tr">
							<option value = "1"<?php selected( '1', $enable_pricing_type ); ?>><?php esc_html_e( 'Currency and Point Price', 'rewardsystem' ); ?></option>
							<option value = "2"<?php selected( '2', $enable_pricing_type ); ?>><?php esc_html_e( 'Only Point Price', 'rewardsystem' ); ?></option>
						</select>
						<p>
						<?php
							esc_html_e(
								'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
								. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
								'rewardsystem'
							);
						?>
						</p>						
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Point Price Type', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="point_price_category_type" name="point_price_category_type" class="postform srp-show-if-point-price-enable-category srp-point-price-type" data-parent="tr">
							<option value="1" <?php selected( '1', $pointpricetype ); ?>><?php esc_html_e( 'By Fixed', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $pointpricetype ); ?>><?php esc_html_e( 'Based on conversion', 'rewardsystem' ); ?></option>
						</select>
						<p class="description">
						<?php
							esc_html_e(
								'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
								. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
								'rewardsystem'
							);
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><?php esc_html_e( 'By Fixed Point', 'rewardsystem' ); ?></label></th>
				<td>
					<input type="text" name="rs_category_points_price" id="rs_category_points_price" value="<?php echo esc_attr( $pointprice ); ?>" class="srp-show-if-point-price-enable-category srp-point-price-fixed"/>
					<p>
					<?php
					esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
					?>
							</p>
				</td>
				</tr>
				<?php
			}
			if ( 'yes' == get_option( 'rs_product_purchase_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
				?>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Enable SUMO Reward Points for Product Purchase', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="enable_reward_system_category" name="enable_reward_system_category" class="postform enable-reward-system-category">
							<option value="yes" <?php selected( 'yes', $enablevalue ); ?>><?php esc_html_e( 'Enable', 'rewardsystem' ); ?></option>
							<option value="no" <?php selected( 'no', $enablevalue ); ?>><?php esc_html_e( 'Disable', 'rewardsystem' ); ?></option>
						</select>
						<p class="description">
						<?php
							esc_html_e(
								'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
								. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
								'rewardsystem'
							);
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Reward Type', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="enable_rs_rule" name="enable_rs_rule" class="postform enable-rs-rule srp-show-if-enable-reward-on-category" data-parent="tr">
							<option value="1" <?php selected( '1', $display_type ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $display_type ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Reward Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="rs_category_points" id="rs_category_points" value="<?php echo esc_attr( $rewardpoints ); ?>" class="srp-show-if-enable-reward-on-category"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Reward Percent', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="rs_category_percent" id="rs_category_percent" value="<?php echo esc_attr( $rewardpercent ); ?>" class="srp-show-if-enable-reward-on-category"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Minimum Quantity required to Earn Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="number" name="rs_get_min_quantity" id="rs_get_min_quantity" value="<?php echo esc_attr( $min_qty ); ?>" min="1" class="srp-show-if-enable-reward-on-category"/>
					</td>
				</tr>
				<?php
			}
			if ( 'yes' == get_option( 'rs_referral_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) ) {
				?>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Enable Referral Reward Points for Product Purchase', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="enable_referral_reward_system_category" name="enable_referral_reward_system_category" class="postform srp-enable-referral-system-category" data-parent="div">
							<option value="yes" <?php selected( 'yes', $enablereferralvalue ); ?>><?php esc_html_e( 'Enable', 'rewardsystem' ); ?></option>
							<option value="no" <?php selected( 'no', $enablereferralvalue ); ?>><?php esc_html_e( 'Disable', 'rewardsystem' ); ?></option>
						</select>
						<p class="description">
						<?php
							esc_html_e(
								'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
								. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
								'rewardsystem'
							);
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Referral Reward Type', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="enable_rs_rule" name="referral_enable_rs_rule" class="postform srp-referral-type srp-show-if-referral-enable-category" data-parent="tr">
							<option value="1" <?php selected( '1', $referralrewardrule ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $referralrewardrule ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Referral Reward Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="referral_rs_category_points" id="referral_rs_category_points" value="<?php echo esc_attr( $referralrewardpoints ); ?>" class="srp-show-if-referral-enable-category srp-referral-fixed"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Referral Reward Percent', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="referral_rs_category_percent" id="referral_rs_category_percent" value="<?php echo esc_attr( $referralrewardpercent ); ?>" class="srp-show-if-referral-enable-category srp-referral-percent"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( ' Reward Type for Getting Referred', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="referral_enable_rs_rule_refer" name="referral_enable_rs_rule_refer" class="postform srp-getting-referred-type srp-show-if-referral-enable-category" data-parent="tr">
							<option value="1" <?php selected( '1', $referral_enable_rs_rule_refer ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $referral_enable_rs_rule_refer ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( ' Reward Points for Getting Referred', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="referral_rs_category_points_get_refered" id="referral_rs_category_points_get_refered" value="<?php echo esc_attr( $referralrewardpoints_get_refer ); ?>" class="srp-show-if-referral-enable-category srp-getrefer-fixed"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>     
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( ' Reward Points In  Percent % for Getting Referred', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="referral_rs_category_percent_get_refer" id="referral_rs_category_percent_get_refer" value="<?php echo esc_attr( $referralrewardpercent_get_refer ); ?>" class="srp-show-if-referral-enable-category srp-getrefer-percent"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
			<?php } if ( 'yes' == get_option( 'rs_social_reward_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_social_reward' ) ) { ?>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Enable SUMO Reward Points for Social Promotion', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="enable_social_reward_system_category" name="enable_social_reward_system_category" class="postform srp-enable-social-reward-category" data-parent="div">
							<option value="yes" <?php selected( 'yes', $enablesocialvalue ); ?>><?php esc_html_e( 'Enable', 'rewardsystem' ); ?></option>
							<option value="no" <?php selected( 'no', $enablesocialvalue ); ?>><?php esc_html_e( 'Disable', 'rewardsystem' ); ?></option>
						</select>
						<p class="description">
						<?php
							esc_html_e(
								'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
								. 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ',
								'rewardsystem'
							);
						?>
							</p>
					</td>
				</tr>
				<!-- Below Field is for Facebook Social Rewards in Category Level Start-->
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Reward Type for Facebook', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="social_facebook_enable_rs_rule" name="social_facebook_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-facebook-like-reward-type" data-parent="tr">
							<option value="1" <?php selected( '1', $socialfacebooktype ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $socialfacebooktype ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Facebook Reward Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_facebook_rs_category_points" id="social_facebook_rs_category_points" value="<?php echo esc_attr( $socialfacebookpoints ); ?>" class="srp-show-if-social-reward-enable-category srp-facebook-like-fixed"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Facebook Reward in Percent %', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_facebook_rs_category_percent" id="social_facebook_rs_category_percent" value="<?php echo esc_attr( $socialfacebookpercent ); ?>" class="srp-show-if-social-reward-enable-category srp-facebook-like-percent"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<!-- Below Field is for Facebook Social Rewards in Category Level Ends -->
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Reward Type for Facebook Share', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="social_facebook_share_enable_rs_rule" name="social_facebook_share_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-facebook-share-reward-type" data-parent="tr">
							<option value="1" <?php selected( '1', $socialfacebooktype_share ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $socialfacebooktype_share ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Facebook  Share Reward Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_facebook_share_rs_category_points" id="social_facebook_share_rs_category_points" value="<?php echo esc_attr( $socialfacebookpoints_share ); ?>" class="srp-show-if-social-reward-enable-category srp-facebook-share-fixed"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Facebook Share Reward in Percent %', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_facebook_share_rs_category_percent" id="social_facebook_share_rs_category_percent" value="<?php echo esc_attr( $socialfacebookpercent_share ); ?>" class="srp-show-if-social-reward-enable-category srp-facebook-share-percent"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<!-- Below Field is for Twitter Social Rewards in Category Level Start-->
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Reward Type for Twitter', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="social_twitter_enable_rs_rule" name="social_twitter_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-twitter-tweet-reward-type" data-parent="tr">
							<option value="1" <?php selected( '1', $socialtwittertype ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $socialtwittertype ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Twitter Reward Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_twitter_rs_category_points" id="social_twitter_rs_category_points" value="<?php echo esc_attr( $socialtwitterpoints ); ?>" class="srp-show-if-social-reward-enable-category srp-twitter-tweet-fixed"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Twitter Reward in Percent %', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_twitter_rs_category_percent" id="social_twitter_rs_category_percent" value="<?php echo esc_attr( $socialtwitterpercent ); ?>" class="srp-show-if-social-reward-enable-category srp-twitter-tweet-percent"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<!-- Below Field is for Twitter Social Rewards in Category Level Ends -->
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Reward Type for Twitter Follow', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="social_twitter_follow_enable_rs_rule" name="social_twitter_follow_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-twitter-follow-reward-type" data-parent="tr">
							<option value="1" <?php selected( '1', $socialtwittertype_follow ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $socialtwittertype_follow ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Twitter Follow Reward Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_twitter_follow_rs_category_points" id="social_twitter_follow_rs_category_points" value="<?php echo esc_attr( $socialtwitterpoints_follow ); ?>" class="srp-show-if-social-reward-enable-category srp-twitter-follow-fixed"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Twitter Follow Reward in Percent %', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_twitter_follow_rs_category_percent" id="social_twitter_follow_rs_category_percent" value="<?php echo esc_attr( $socialtwitterpercent_follow ); ?>" class="srp-show-if-social-reward-enable-category srp-twitter-follow-percent"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<!-- Below Field is for Google Social Rewards in Category Level Start-->
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Reward Type for Google', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="social_google_enable_rs_rule" name="social_google_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-google-plus-reward-type" data-parent="tr">
							<option value="1" <?php selected( '1', $socialgoogletype ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $socialgoogletype ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Google Reward Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_google_rs_category_points" id="social_twitter_rs_category_points" value="<?php echo esc_attr( $socialgooglepoints ); ?>" class="srp-show-if-social-reward-enable-category srp-google-plus-fixed"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Google Reward in Percent %', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_google_rs_category_percent" id="social_google_rs_category_percent" value="<?php echo esc_html( $socialgooglepercent ); ?>" class="srp-show-if-social-reward-enable-category srp-google-plus-percent"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<!-- Below Field is for Google Social Rewards in Category Level Ends -->

				<!-- Below Field is for VK Social Rewards in Category Level Start-->
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Reward Type for VK', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="social_vk_enable_rs_rule" name="social_vk_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-vk-like-reward-type" data-parent="tr">
							<option value="1" <?php selected( '1', $socialvktype ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $socialvktype ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social VK Reward Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_vk_rs_category_points" id="social_vk_rs_category_points" value="<?php echo esc_html( $socialvkpoints ); ?>" class="srp-show-if-social-reward-enable-category srp-vk-like-fixed"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social VK Reward in Percent %', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_vk_rs_category_percent" id="social_vk_rs_category_percent" value="<?php echo esc_html( $socialvkpercent ); ?>" class="srp-show-if-social-reward-enable-category srp-vk-like-percent"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<!-- Below Field is for VK Social Rewards in Category Level Ends -->

				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Reward Type for Instagram', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="social_instagram_enable_rs_rule" name="social_instagram_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-instagram-reward-type" data-parent="tr">
							<option value="1" <?php selected( '1', $socialinstagramtype ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $socialinstagramtype ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Instagram Reward Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_instagram_rs_category_points" id="social_instagram_rs_category_points" value="<?php echo esc_html( $socialinstagrampoints ); ?>" class="srp-show-if-social-reward-enable-category srp-instagram-fixed"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Instagram Reward in Percent %', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_instagram_rs_category_percent" id="social_instagram_rs_category_percent" value="<?php echo esc_attr( $socialinstagrampercent ); ?>" class="srp-show-if-social-reward-enable-category srp-instagram-percent"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<!-- Below Field is for Twitter Social Rewards in Category Level Ends -->
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social Reward Type for OK.ru Share', 'rewardsystem' ); ?></label></th>
					<td>
						<select id="social_ok_follow_enable_rs_rule" name="social_ok_follow_enable_rs_rule" class="postform srp-show-if-social-reward-enable-category srp-ok-share-reward-type" data-parent="tr">
							<option value="1" <?php selected( '1', $socialoktype_follow ); ?>><?php esc_html_e( 'By Fixed Reward Points', 'rewardsystem' ); ?></option>
							<option value="2" <?php selected( '2', $socialoktype_follow ); ?>><?php esc_html_e( 'By Percentage of Product Price', 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social OK.ru Share Reward Points', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_ok_follow_rs_category_points" id="social_ok_follow_rs_category_points" value="<?php echo esc_html( $socialokpoints_follow ); ?>" class="srp-show-if-social-reward-enable-category srp-ok-share-fixed"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label><?php esc_html_e( 'Social OK.ru Share Reward in Percent %', 'rewardsystem' ); ?></label></th>
					<td>
						<input type="text" name="social_ok_follow_rs_category_percent" id="social_ok_follow_rs_category_percent" value="<?php echo esc_html( $socialokpercent_follow ); ?>" class="srp-show-if-social-reward-enable-category srp-ok-share-percent"/>
						<p class="description">
						<?php
						esc_html_e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored', 'rewardsystem' )
						?>
							</p>
					</td>
				</tr>
				<?php
			}
		}

		public static function rs_save_admin_settings_for_category_page( $term_id, $tt_id, $taxonomy ) {
			if ( isset( $_REQUEST['enable_rs_rule'] ) ) {
				srp_update_term_meta( $term_id, 'enable_rs_rule', wc_clean( wp_unslash( $_REQUEST['enable_rs_rule'] ) ) );
			}

			if ( isset( $_REQUEST['enable_point_price_category'] ) ) {
				srp_update_term_meta( $term_id, 'enable_point_price_category', wc_clean( wp_unslash( $_REQUEST['enable_point_price_category'] ) ) );
			}

			if ( isset( $_REQUEST['rs_category_points_price'] ) ) {
				srp_update_term_meta( $term_id, 'rs_category_points_price', wc_clean( wp_unslash( $_REQUEST['rs_category_points_price'] ) ) );
			}

			if ( isset( $_REQUEST['point_price_category_type'] ) ) {
				srp_update_term_meta( $term_id, 'point_price_category_type', wc_clean( wp_unslash( $_REQUEST['point_price_category_type'] ) ) );
			}

			if ( isset( $_REQUEST['pricing_category_types'] ) ) {
				srp_update_term_meta( $term_id, 'pricing_category_types', wc_clean( wp_unslash( $_REQUEST['pricing_category_types'] ) ) );
			}

			if ( isset( $_REQUEST['referral_enable_rs_rule'] ) ) {
				srp_update_term_meta( $term_id, 'referral_enable_rs_rule', wc_clean( wp_unslash( $_REQUEST['referral_enable_rs_rule'] ) ) );
			}

			if ( isset( $_REQUEST['enable_reward_system_category'] ) ) {
				srp_update_term_meta( $term_id, 'enable_reward_system_category', wc_clean( wp_unslash( $_REQUEST['enable_reward_system_category'] ) ) );
			}

			if ( isset( $_REQUEST['enable_referral_reward_system_category'] ) ) {
				srp_update_term_meta( $term_id, 'enable_referral_reward_system_category', wc_clean( wp_unslash( $_REQUEST['enable_referral_reward_system_category'] ) ) );
			}

			if ( isset( $_REQUEST['rs_category_points'] ) ) {
				srp_update_term_meta( $term_id, 'rs_category_points', wc_clean( wp_unslash( $_REQUEST['rs_category_points'] ) ) );
			}
			if ( isset( $_REQUEST['rs_category_percent'] ) ) {
				srp_update_term_meta( $term_id, 'rs_category_percent', wc_clean( wp_unslash( $_REQUEST['rs_category_percent'] ) ) );
			}
			if ( isset( $_REQUEST['rs_get_min_quantity'] ) ) {
				srp_update_term_meta( $term_id, 'rs_get_min_quantity', wc_clean( wp_unslash( $_REQUEST['rs_get_min_quantity'] ) ) );
			}

			if ( isset( $_REQUEST['referral_rs_category_points'] ) ) {
				srp_update_term_meta( $term_id, 'referral_rs_category_points', wc_clean( wp_unslash( $_REQUEST['referral_rs_category_points'] ) ) );
			}
			if ( isset( $_REQUEST['referral_rs_category_percent'] ) ) {
				srp_update_term_meta( $term_id, 'referral_rs_category_percent', wc_clean( wp_unslash( $_REQUEST['referral_rs_category_percent'] ) ) );
			}
			if ( isset( $_REQUEST['referral_enable_rs_rule_refer'] ) ) {
				srp_update_term_meta( $term_id, 'referral_enable_rs_rule_refer', wc_clean( wp_unslash( $_REQUEST['referral_enable_rs_rule_refer'] ) ) );
			}
			if ( isset( $_REQUEST['referral_rs_category_points_get_refered'] ) ) {
				srp_update_term_meta( $term_id, 'referral_rs_category_points_get_refered', wc_clean( wp_unslash( $_REQUEST['referral_rs_category_points_get_refered'] ) ) );
			}
			if ( isset( $_REQUEST['referral_rs_category_percent_get_refer'] ) ) {
				srp_update_term_meta( $term_id, 'referral_rs_category_percent_get_refer', wc_clean( wp_unslash( $_REQUEST['referral_rs_category_percent_get_refer'] ) ) );
			}

			// social updation for facebook,twitter,google
			if ( isset( $_REQUEST['enable_social_reward_system_category'] ) ) {
				srp_update_term_meta( $term_id, 'enable_social_reward_system_category', wc_clean( wp_unslash( $_REQUEST['enable_social_reward_system_category'] ) ) );
			}

			/* Facebook Rule and its Points Start */
			if ( isset( $_REQUEST['social_facebook_enable_rs_rule'] ) ) {
				srp_update_term_meta( $term_id, 'social_facebook_enable_rs_rule', wc_clean( wp_unslash( $_REQUEST['social_facebook_enable_rs_rule'] ) ) );
			}
			if ( isset( $_REQUEST['social_facebook_rs_category_points'] ) ) {
				srp_update_term_meta( $term_id, 'social_facebook_rs_category_points', wc_clean( wp_unslash( $_REQUEST['social_facebook_rs_category_points'] ) ) );
			}
			if ( isset( $_REQUEST['social_facebook_rs_category_percent'] ) ) {
				srp_update_term_meta( $term_id, 'social_facebook_rs_category_percent', wc_clean( wp_unslash( $_REQUEST['social_facebook_rs_category_percent'] ) ) );
			}

			/* Facebook Rule and its Points End */
			if ( isset( $_REQUEST['social_facebook_share_enable_rs_rule'] ) ) {
				srp_update_term_meta( $term_id, 'social_facebook_share_enable_rs_rule', wc_clean( wp_unslash( $_REQUEST['social_facebook_share_enable_rs_rule'] ) ) );
			}
			if ( isset( $_REQUEST['social_facebook_share_rs_category_points'] ) ) {
				srp_update_term_meta( $term_id, 'social_facebook_share_rs_category_points', wc_clean( wp_unslash( $_REQUEST['social_facebook_share_rs_category_points'] ) ) );
			}
			if ( isset( $_REQUEST['social_facebook_share_rs_category_percent'] ) ) {
				srp_update_term_meta( $term_id, 'social_facebook_share_rs_category_percent', wc_clean( wp_unslash( $_REQUEST['social_facebook_share_rs_category_percent'] ) ) );
			}

			/* Twitter Rule and Its Points updation Start */
			if ( isset( $_REQUEST['social_twitter_enable_rs_rule'] ) ) {
				srp_update_term_meta( $term_id, 'social_twitter_enable_rs_rule', wc_clean( wp_unslash( $_REQUEST['social_twitter_enable_rs_rule'] ) ) );
			}
			if ( isset( $_REQUEST['social_twitter_rs_category_points'] ) ) {
				srp_update_term_meta( $term_id, 'social_twitter_rs_category_points', wc_clean( wp_unslash( $_REQUEST['social_twitter_rs_category_points'] ) ) );
			}
			if ( isset( $_REQUEST['social_twitter_rs_category_percent'] ) ) {
				srp_update_term_meta( $term_id, 'social_twitter_rs_category_percent', wc_clean( wp_unslash( $_REQUEST['social_twitter_rs_category_percent'] ) ) );
			}
			/* Twitter Rule and Its Points Updation End */
			if ( isset( $_REQUEST['social_twitter_follow_enable_rs_rule'] ) ) {
				srp_update_term_meta( $term_id, 'social_twitter_follow_enable_rs_rule', wc_clean( wp_unslash( $_REQUEST['social_twitter_follow_enable_rs_rule'] ) ) );
			}
			if ( isset( $_REQUEST['social_twitter_follow_rs_category_points'] ) ) {
				srp_update_term_meta( $term_id, 'social_twitter_follow_rs_category_points', wc_clean( wp_unslash( $_REQUEST['social_twitter_follow_rs_category_points'] ) ) );
			}
			if ( isset( $_REQUEST['social_twitter_follow_rs_category_percent'] ) ) {
				srp_update_term_meta( $term_id, 'social_twitter_follow_rs_category_percent', wc_clean( wp_unslash( $_REQUEST['social_twitter_follow_rs_category_percent'] ) ) );
			}

			/* Google Rule and Its Points updation Start */
			if ( isset( $_REQUEST['social_google_enable_rs_rule'] ) ) {
				srp_update_term_meta( $term_id, 'social_google_enable_rs_rule', wc_clean( wp_unslash( $_REQUEST['social_google_enable_rs_rule'] ) ) );
			}
			if ( isset( $_REQUEST['social_google_rs_category_points'] ) ) {
				srp_update_term_meta( $term_id, 'social_google_rs_category_points', wc_clean( wp_unslash( $_REQUEST['social_google_rs_category_points'] ) ) );
			}
			if ( isset( $_REQUEST['social_google_rs_category_percent'] ) ) {
				srp_update_term_meta( $term_id, 'social_google_rs_category_percent', wc_clean( wp_unslash( $_REQUEST['social_google_rs_category_percent'] ) ) );
			}
			/* Google Rule and Its Points Updation End */

			/* VK Rule and Its Points updation Start */
			if ( isset( $_REQUEST['social_vk_enable_rs_rule'] ) ) {
				srp_update_term_meta( $term_id, 'social_vk_enable_rs_rule', wc_clean( wp_unslash( $_REQUEST['social_vk_enable_rs_rule'] ) ) );
			}
			if ( isset( $_REQUEST['social_vk_rs_category_points'] ) ) {
				srp_update_term_meta( $term_id, 'social_vk_rs_category_points', wc_clean( wp_unslash( $_REQUEST['social_vk_rs_category_points'] ) ) );
			}
			if ( isset( $_REQUEST['social_vk_rs_category_percent'] ) ) {
				srp_update_term_meta( $term_id, 'social_vk_rs_category_percent', wc_clean( wp_unslash( $_REQUEST['social_vk_rs_category_percent'] ) ) );
			}
			/* VK Rule and Its Points Updation End */

			if ( isset( $_REQUEST['social_instagram_enable_rs_rule'] ) ) {
				srp_update_term_meta( $term_id, 'social_instagram_enable_rs_rule', wc_clean( wp_unslash( $_REQUEST['social_instagram_enable_rs_rule'] ) ) );
			}
			if ( isset( $_REQUEST['social_instagram_rs_category_points'] ) ) {
				srp_update_term_meta( $term_id, 'social_instagram_rs_category_points', wc_clean( wp_unslash( $_REQUEST['social_instagram_rs_category_points'] ) ) );
			}
			if ( isset( $_REQUEST['social_instagram_rs_category_percent'] ) ) {
				srp_update_term_meta( $term_id, 'social_instagram_rs_category_percent', wc_clean( wp_unslash( $_REQUEST['social_instagram_rs_category_percent'] ) ) );
			}

			if ( isset( $_REQUEST['social_ok_follow_enable_rs_rule'] ) ) {
				srp_update_term_meta( $term_id, 'social_ok_follow_enable_rs_rule', wc_clean( wp_unslash( $_REQUEST['social_ok_follow_enable_rs_rule'] ) ) );
			}
			if ( isset( $_REQUEST['social_ok_follow_rs_category_points'] ) ) {
				srp_update_term_meta( $term_id, 'social_ok_follow_rs_category_points', wc_clean( wp_unslash( $_REQUEST['social_ok_follow_rs_category_points'] ) ) );
			}
			if ( isset( $_REQUEST['social_ok_follow_rs_category_percent'] ) ) {
				srp_update_term_meta( $term_id, 'social_ok_follow_rs_category_percent', wc_clean( wp_unslash( $_REQUEST['social_ok_follow_rs_category_percent'] ) ) );
			}

			delete_transient( 'wc_term_counts' );
		}
	}

	RSCategoryField::init();
}
