<?php
/*
 * Simple Product Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSSimpleProduct' ) ) {

	class RSSimpleProduct {

		public static function init() {

			add_action( 'woocommerce_product_options_general_product_data', array( __CLASS__, 'settings_for_simple_product' ) );

			add_action( 'woocommerce_product_options_advanced', array( __CLASS__, 'setting_for_social_actions' ) );

			add_action( 'woocommerce_product_options_advanced', array( __CLASS__, 'setting_for_point_pricing' ) );

			add_action( 'woocommerce_process_product_meta', array( __CLASS__, 'save_settings' ), 10, 2 );
		}

		/* Product and Referral Product Purchase Settings for Simple Product */

		public static function settings_for_simple_product() {
			global $product_object;
			if ( ! is_admin() ) {
				return;
			}
			?>
			<div class="options_group show_if_simple show_if_subscription show_if_booking show_if_external">
				<?php
				if ( 'yes' === get_option( 'rs_product_purchase_activated' ) && 'yes' === get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
					woocommerce_wp_select(
						array(
							'id'          => '_rewardsystemcheckboxvalue',
							'class'       => 'rewardsystemcheckboxvalue',
							'desc_tip'    => true,
							'default'     => 'yes',
							'description' => __(
								'Enable will Turn On Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
								. 'Disable will Turn Off Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. ',
								'rewardsystem'
							),
							'label'       => __( 'Enable SUMO Reward Points for Product Purchase', 'rewardsystem' ),
							'options'     => array(
								'yes' => __( 'Enable', 'rewardsystem' ),
								'no'  => __( 'Disable', 'rewardsystem' ),
							),
						)
					);
					woocommerce_wp_select(
						array(
							'id'      => '_rewardsystem_options',
							'class'   => 'rewardsystem_options show_if_enable',
							'label'   => __( 'Reward Type', 'rewardsystem' ),
							'options' => array(
								'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
								'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
							),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'          => '_rewardsystempoints',
							'class'       => 'show_if_enable',
							'name'        => '_rewardsystempoints',
							'desc_tip'    => true,
							'description' => __(
								'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
								. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
								'rewardsystem'
							),
							'label'       => __( 'Reward Points', 'rewardsystem' ),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'          => '_rewardsystempercent',
							'class'       => 'show_if_enable',
							'name'        => '_rewardsystempercent',
							'desc_tip'    => true,
							'description' => __(
								'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
								. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
								'rewardsystem'
							),
							'label'       => __( 'Reward Points in Percent %', 'rewardsystem' ),
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'                => 'rs_number_of_qty_for_simple_product',
							'class'             => 'show_if_enable',
							'name'              => 'rs_number_of_qty_for_simple_product',
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => '1',
							),
							'label'             => __( 'Minimum Quantity required to Earn Points', 'rewardsystem' ),
						)
					);
				}
				if ( 'yes' == get_option( 'rs_buyingpoints_activated' ) ) {
					woocommerce_wp_select(
						array(
							'id'      => '_rewardsystem_buying_reward_points',
							'class'   => '_rewardsystem_buying_reward_points',
							'label'   => __( 'Enable Buying of SUMO Reward Points', 'rewardsystem' ),
							'options' => array(
								'no'  => __( 'Disable', 'rewardsystem' ),
								'yes' => __( 'Enable', 'rewardsystem' ),
							),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'    => '_rewardsystem_assign_buying_points',
							'class' => 'show_if_buy_reward_points_enable',
							'name'  => '_rewardsystem_assign_buying_points',
							'label' => __( 'Buy Reward Points', 'rewardsystem' ),
						)
					);
				}
				if ( 'yes' == get_option( 'rs_referral_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) ) {
					woocommerce_wp_select(
						array(
							'id'          => '_rewardsystemreferralcheckboxvalue',
							'class'       => '_rewardsystemreferralcheckboxvalue',
							'desc_tip'    => true,
							'description' => __(
								'Enable will Turn On Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
								. 'Disable will Turn Off Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. ',
								'rewardsystem'
							),
							'label'       => __( 'Enable Referral Reward Points for Product Purchase', 'rewardsystem' ),
							'options'     => array(
								'no'  => __( 'Disable', 'rewardsystem' ),
								'yes' => __( 'Enable', 'rewardsystem' ),
							),
						)
					);
					woocommerce_wp_select(
						array(
							'id'      => '_referral_rewardsystem_options',
							'class'   => 'referral_rewardsystem_options show_if_referral_enable',
							'label'   => __( 'Referral Reward Type', 'rewardsystem' ),
							'options' => array(
								'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
								'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
							),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'          => '_referralrewardsystempoints',
							'class'       => 'show_if_referral_enable',
							'name'        => '_referralrewardsystempoints',
							'desc_tip'    => true,
							'description' => __(
								'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
								. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
								'rewardsystem'
							),
							'label'       => __( 'Referral Reward Points', 'rewardsystem' ),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'          => '_referralrewardsystempercent',
							'class'       => 'show_if_referral_enable',
							'name'        => '_referralrewardsystempercent',
							'desc_tip'    => true,
							'description' => __(
								'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
								. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
								'rewardsystem'
							),
							'label'       => __( 'Referral Reward Points in Percent %', 'rewardsystem' ),
						)
					);

					woocommerce_wp_select(
						array(
							'id'      => '_referral_rewardsystem_options_getrefer',
							'class'   => 'referral_rewardsystem_options_get show_if_referral_enable',
							'label'   => __( 'Getting Referred Reward Type', 'rewardsystem' ),
							'options' => array(
								'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
								'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
							),
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'          => '_referralrewardsystempoints_for_getting_referred',
							'class'       => 'show_if_referral_enable',
							'name'        => '_referralrewardsystempoints_for_getting_referred',
							'desc_tip'    => true,
							'description' => __(
								'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
								. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
								'rewardsystem'
							),
							'label'       => __( 'Reward Points for Getting Referred', 'rewardsystem' ),
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'          => '_referralrewardsystempercent_for_getting_referred',
							'class'       => 'show_if_referral_enable',
							'name'        => '_referralrewardsystempercent_for_getting_referred',
							'desc_tip'    => true,
							'description' => __(
								'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
								. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
								'rewardsystem'
							),
							'label'       => __( ' Reward Points in Percent % for Getting Referred', 'rewardsystem' ),
						)
					);
				}

				if ( 'yes' == get_option( 'rs_redeeming_activated' ) && '1' === get_option( 'rs_select_redeeming_based_on' ) ) {
					woocommerce_wp_select(
						array(
							'label'   => __( 'Enable Redeeming Points', 'rewardsystem' ),
							'id'      => '_rewardsystem_redeeming_points_enable',
							'class'   => '_rewardsystem_redeeming_points_enable',
							'default' => '1',
							'std'     => '1',
							'options' => array(
								'1' => __( 'Enable', 'rewardsystem' ),
								'2' => __( 'Disable', 'rewardsystem' ),
							),
						)
					);
					woocommerce_wp_text_input(
						array(
							'label'             => __( 'Maximum Points can be Redeemed', 'rewardsystem' ),
							'id'                => '_rewardsystem_max_redeeming_points',
							'name'              => '_rewardsystem_max_redeeming_points',
							'custom_attributes' => array(
								'min' => '1',
							),
							'std'               => '',
							'default'           => '',
						)
					);
				}
				?>
			</div>
			<?php
		}

		/* Social Action Settings */

		public static function setting_for_social_actions() {
			if ( ! is_admin() ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_social_reward_activated' ) ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_product_category_level_for_social_reward' ) ) {
				return;
			}

			woocommerce_wp_select(
				array(
					'id'          => '_socialrewardsystemcheckboxvalue',
					'class'       => 'socialrewardsystemcheckboxvalue',
					'desc_tip'    => true,
					'description' => __(
						'Enable will Turn On Reward Points for Product Purchase and Category/Global Settings will be considered when applicable. '
						. 'Disable will Turn Off Reward Points for Product Purchase and Category/Global Settings will not be considered. ',
						'rewardsystem'
					),
					'label'       => __( 'Enable SUMO Reward Points for Social Promotion', 'rewardsystem' ),
					'options'     => array(
						'no'  => __( 'Disable', 'rewardsystem' ),
						'yes' => __( 'Enable', 'rewardsystem' ),
					),
				)
			);

			woocommerce_wp_select(
				array(
					'id'      => '_social_rewardsystem_options_facebook',
					'class'   => 'social_rewardsystem_options_facebook show_if_social_enable',
					'label'   => __( 'Facebook Like Reward Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
						'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempoints_facebook',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempoints_facebook',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Facebook Like Reward Points', 'rewardsystem' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempercent_facebook',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempercent_facebook',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Facebook Like Reward Points in Percent %', 'rewardsystem' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_social_rewardsystem_options_facebook_share',
					'class'   => ' _social_rewardsystem_options_facebook_share show_if_social_enable',
					'label'   => __( 'Facebook Share Reward Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
						'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempoints_facebook_share',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempoints_facebook_share',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Facebook Share Reward Points', 'rewardsystem' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempercent_facebook_share',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempercent_facebook_share',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Facebook Share Reward Points in Percent %', 'rewardsystem' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_social_rewardsystem_options_twitter',
					'class'   => 'social_rewardsystem_options_twitter show_if_social_enable',
					'label'   => __( 'Twitter Tweet Reward Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
						'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempoints_twitter',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempoints_twitter',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Twitter Tweet Reward Points', 'rewardsystem' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempercent_twitter',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempercent_twitter',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Twitter Tweet Reward Percent %', 'rewardsystem' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_social_rewardsystem_options_twitter_follow',
					'class'   => '_social_rewardsystem_options_twitter_follow show_if_social_enable',
					'label'   => __( 'Twitter Follow Reward Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
						'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempoints_twitter_follow',
					'class'       => '_socialrewardsystempoints_twitter_follow_field show_if_social_enable',
					'name'        => '_socialrewardsystempoints_twitter_follow',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Twitter Follow Reward Points', 'rewardsystem' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempercent_twitter_follow',
					'class'       => '_socialrewardsystempercent_twitter_follow_field show_if_social_enable',
					'name'        => '_socialrewardsystempercent_twitter_follow',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Twitter Follow Reward Percent %', 'rewardsystem' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_social_rewardsystem_options_google',
					'class'   => 'social_rewardsystem_options_google show_if_social_enable',
					'label'   => __( 'Google+1 Reward Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
						'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempoints_google',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempoints_google',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Google+1 Reward Points', 'rewardsystem' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempercent_google',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempercent_google',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Google+1 Reward Percent %', 'rewardsystem' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_social_rewardsystem_options_vk',
					'class'   => 'social_rewardsystem_options_vk show_if_social_enable',
					'label'   => __( 'VK.com Like Reward Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
						'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempoints_vk',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempoints_vk',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'VK.com Like Reward Points ', 'rewardsystem' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempercent_vk',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempercent_vk',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'VK.com Like Reward Percent %', 'rewardsystem' ),
				)
			);

			woocommerce_wp_select(
				array(
					'id'      => '_social_rewardsystem_options_instagram',
					'class'   => '_social_rewardsystem_options_instagram show_if_social_enable',
					'label'   => __( 'Instagram Reward Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
						'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempoints_instagram',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempoints_instagram',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Instagram Reward Points ', 'rewardsystem' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempercent_instagram',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempercent_instagram',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'Instagram Reward Percent %', 'rewardsystem' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_social_rewardsystem_options_ok_follow',
					'class'   => '_social_rewardsystem_options_ok_follow show_if_social_enable',
					'label'   => __( 'OK.ru Share Reward Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
						'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempoints_ok_follow',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempoints_ok_follow',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ',
						'rewardsystem'
					),
					'label'       => __( 'OK.ru Share Reward Points', 'rewardsystem' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_socialrewardsystempercent_ok_follow',
					'class'       => 'show_if_social_enable',
					'name'        => '_socialrewardsystempercent_ok_follow',
					'desc_tip'    => true,
					'description' => __(
						'When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
						. 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.',
						'rewardsystem'
					),
					'label'       => __( 'OK.ru Share Reward Percent %', 'rewardsystem' ),
				)
			);
		}

		/* Point Price Settings */

		public static function setting_for_point_pricing() {
			if ( ! is_admin() ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_point_price_activated' ) ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_product_category_level_for_points_price' ) ) {
				return;
			}

			if ( 2 == get_option( 'rs_enable_disable_point_priceing' ) ) {
				return;
			}

			global $post;
			$ProductObj = srp_product_object( $post->ID );
			if ( 'variation' == srp_product_type( $post->ID ) || 'variable' == srp_product_type( $post->ID ) ) {
				return;
			}

			woocommerce_wp_select(
				array(
					'id'      => '_rewardsystem_enable_point_price',
					'class'   => '_rewardsystem_enable_point_price',
					'label'   => __( 'Enable Point Pricing', 'rewardsystem' ),
					'options' => array(
						'no'  => __( 'Disable', 'rewardsystem' ),
						'yes' => __( 'Enable', 'rewardsystem' ),
					),
				)
			);

			woocommerce_wp_select(
				array(
					'id'      => '_rewardsystem_enable_point_price_type',
					'class'   => '_rewardsystem_enable_point_price_type',
					'label'   => __( 'Pricing Display Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'Currency & Point Price', 'rewardsystem' ),
						'2' => __( 'Only Point Price', 'rewardsystem' ),
					),
					'std'     => '1',
				)
			);

			woocommerce_wp_select(
				array(
					'id'      => '_rewardsystem_enable_point_price_type_booking',
					'class'   => '_rewardsystem_enable_point_price_type_booking',
					'label'   => __( 'Point Price Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'By Fixed', 'rewardsystem' ),
					),
					'std'     => '1',
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_rewardsystem_point_price_type',
					'class'   => '_rewardsystem_point_price_type',
					'label'   => __( 'Point Price Type', 'rewardsystem' ),
					'options' => array(
						'1' => __( 'By Fixed', 'rewardsystem' ),
						'2' => __( 'Based On Conversion', 'rewardsystem' ),
					),
					'std'     => '1',
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'    => '_rewardsystem__points',
					'class' => '_rewardsystem__points',
					'name'  => '_rewardsystem__points',
					'label' => __( 'Points to Product', 'rewardsystem' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'       => '_rewardsystem__points_based_on_conversion',
					'class'    => '_rewardsystem__points_based_on_conversion',
					'name'     => '_rewardsystem__points_based_on_conversion',
					'readonly' => 'readonly',
					'label'    => __( 'Points Based On Conversion', 'rewardsystem' ),
				)
			);
		}

		/* Save Product Level Settings */

		public static function save_settings( $post_id, $post ) {
			if ( ! is_admin() ) {
				return;
			}

			/* Save Buying Point Settings - Start */
			if ( 'yes' == get_option( 'rs_buyingpoints_activated' ) ) {
				if ( isset( $_REQUEST['_rewardsystem_buying_reward_points'] ) ) {
					update_post_meta( $post_id, '_rewardsystem_buying_reward_points', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_buying_reward_points'] ) ) );
				}

				if ( isset( $_REQUEST['_rewardsystem_assign_buying_points'] ) ) {
					update_post_meta( $post_id, '_rewardsystem_assign_buying_points', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_assign_buying_points'] ) ) );
				}
			}
			/* Save Buying Point Settings - End */

			/* Save Product Purchase Settings - Start */
			if ( 'yes' == get_option( 'rs_product_purchase_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
				if ( isset( $_REQUEST['_rewardsystemcheckboxvalue'] ) ) {
					update_post_meta( $post_id, '_rewardsystemcheckboxvalue', wc_clean( wp_unslash( $_REQUEST['_rewardsystemcheckboxvalue'] ) ) );
				}

				if ( isset( $_REQUEST['_rewardsystem_options'] ) ) {
					update_post_meta( $post_id, '_rewardsystem_options', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_options'] ) ) );
				}

				if ( isset( $_REQUEST['_rewardsystempoints'] ) ) {
					update_post_meta( $post_id, '_rewardsystempoints', wc_clean( wp_unslash( $_REQUEST['_rewardsystempoints'] ) ) );
				}

				if ( isset( $_REQUEST['_rewardsystempercent'] ) ) {
					update_post_meta( $post_id, '_rewardsystempercent', wc_clean( wp_unslash( $_REQUEST['_rewardsystempercent'] ) ) );
				}

				if ( isset( $_REQUEST['rs_number_of_qty_for_simple_product'] ) ) {
					$number_of_qty = ! empty( $_REQUEST['rs_number_of_qty_for_simple_product'] ) ? absint( $_REQUEST['rs_number_of_qty_for_simple_product'] ) : '';
					update_post_meta( $post_id, 'rs_number_of_qty_for_simple_product', $number_of_qty );
				}
			}
			/* Save Product Purchase Settings - End */

			/* Save Referral Product Purchase Settings - Start */
			if ( 'yes' == get_option( 'rs_referral_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) ) {
				if ( isset( $_REQUEST['_rewardsystemreferralcheckboxvalue'] ) ) {
					update_post_meta( $post_id, '_rewardsystemreferralcheckboxvalue', wc_clean( wp_unslash( $_REQUEST['_rewardsystemreferralcheckboxvalue'] ) ) );
				}

				if ( isset( $_REQUEST['_referral_rewardsystem_options'] ) ) {
					update_post_meta( $post_id, '_referral_rewardsystem_options', wc_clean( wp_unslash( $_REQUEST['_referral_rewardsystem_options'] ) ) );
				}

				if ( isset( $_REQUEST['_referralrewardsystempoints'] ) ) {
					update_post_meta( $post_id, '_referralrewardsystempoints', wc_clean( wp_unslash( $_REQUEST['_referralrewardsystempoints'] ) ) );
				}

				if ( isset( $_REQUEST['_referralrewardsystempercent'] ) ) {
					update_post_meta( $post_id, '_referralrewardsystempercent', wc_clean( wp_unslash( $_REQUEST['_referralrewardsystempercent'] ) ) );
				}

				if ( isset( $_REQUEST['_referralrewardsystempoints_for_getting_referred'] ) ) {
					update_post_meta( $post_id, '_referralrewardsystempoints_for_getting_referred', wc_clean( wp_unslash( $_REQUEST['_referralrewardsystempoints_for_getting_referred'] ) ) );
				}

				if ( isset( $_REQUEST['_referral_rewardsystem_options_getrefer'] ) ) {
					update_post_meta( $post_id, '_referral_rewardsystem_options_getrefer', wc_clean( wp_unslash( $_REQUEST['_referral_rewardsystem_options_getrefer'] ) ) );
				}

				if ( isset( $_REQUEST['_referralrewardsystempercent_for_getting_referred'] ) ) {
					update_post_meta( $post_id, '_referralrewardsystempercent_for_getting_referred', wc_clean( wp_unslash( $_REQUEST['_referralrewardsystempercent_for_getting_referred'] ) ) );
				}
			}
			/* Save Referral Product Purchase Settings - End */

			/* Save Social Action Settings - Start */
			if ( 'yes' == get_option( 'rs_social_reward_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_social_reward' ) ) {
				if ( isset( $_REQUEST['_socialrewardsystemcheckboxvalue'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystemcheckboxvalue', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystemcheckboxvalue'] ) ) );
				}

				if ( isset( $_REQUEST['_social_rewardsystem_options_facebook'] ) ) {
					update_post_meta( $post_id, '_social_rewardsystem_options_facebook', wc_clean( wp_unslash( $_REQUEST['_social_rewardsystem_options_facebook'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempoints_facebook'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempoints_facebook', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempoints_facebook'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempercent_facebook'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempercent_facebook', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempercent_facebook'] ) ) );
				}

				if ( isset( $_REQUEST['_social_rewardsystem_options_facebook_share'] ) ) {
					update_post_meta( $post_id, '_social_rewardsystem_options_facebook_share', wc_clean( wp_unslash( $_REQUEST['_social_rewardsystem_options_facebook_share'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempoints_facebook_share'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempoints_facebook_share', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempoints_facebook_share'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempercent_facebook_share'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempercent_facebook_share', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempercent_facebook_share'] ) ) );
				}

				if ( isset( $_REQUEST['_social_rewardsystem_options_twitter'] ) ) {
					update_post_meta( $post_id, '_social_rewardsystem_options_twitter', wc_clean( wp_unslash( $_REQUEST['_social_rewardsystem_options_twitter'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempoints_twitter'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempoints_twitter', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempoints_twitter'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempercent_twitter'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempercent_twitter', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempercent_twitter'] ) ) );
				}

				if ( isset( $_REQUEST['_social_rewardsystem_options_twitter_follow'] ) ) {
					update_post_meta( $post_id, '_social_rewardsystem_options_twitter_follow', wc_clean( wp_unslash( $_REQUEST['_social_rewardsystem_options_twitter_follow'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempoints_twitter_follow'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempoints_twitter_follow', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempoints_twitter_follow'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempercent_twitter_follow'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempercent_twitter_follow', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempercent_twitter_follow'] ) ) );
				}

				if ( isset( $_REQUEST['_social_rewardsystem_options_google'] ) ) {
					update_post_meta( $post_id, '_social_rewardsystem_options_google', wc_clean( wp_unslash( $_REQUEST['_social_rewardsystem_options_google'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempoints_google'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempoints_google', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempoints_google'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempercent_google'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempercent_google', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempercent_google'] ) ) );
				}

				if ( isset( $_REQUEST['_social_rewardsystem_options_vk'] ) ) {
					update_post_meta( $post_id, '_social_rewardsystem_options_vk', wc_clean( wp_unslash( $_REQUEST['_social_rewardsystem_options_vk'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempoints_vk'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempoints_vk', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempoints_vk'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempercent_vk'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempercent_vk', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempercent_vk'] ) ) );
				}

				if ( isset( $_REQUEST['_social_rewardsystem_options_instagram'] ) ) {
					update_post_meta( $post_id, '_social_rewardsystem_options_instagram', wc_clean( wp_unslash( $_REQUEST['_social_rewardsystem_options_instagram'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempoints_instagram'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempoints_instagram', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempoints_instagram'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempercent_instagram'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempercent_instagram', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempercent_instagram'] ) ) );
				}

				if ( isset( $_REQUEST['_social_rewardsystem_options_ok_follow'] ) ) {
					update_post_meta( $post_id, '_social_rewardsystem_options_ok_follow', wc_clean( wp_unslash( $_REQUEST['_social_rewardsystem_options_ok_follow'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempoints_ok_follow'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempoints_ok_follow', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempoints_ok_follow'] ) ) );
				}

				if ( isset( $_REQUEST['_socialrewardsystempercent_ok_follow'] ) ) {
					update_post_meta( $post_id, '_socialrewardsystempercent_ok_follow', wc_clean( wp_unslash( $_REQUEST['_socialrewardsystempercent_ok_follow'] ) ) );
				}
			}
			/* Save Social Action Settings - End */

			/* Save Point Price Settings - Start */
			if ( 'yes' == get_option( 'rs_point_price_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_points_price' ) ) {
				if ( isset( $_REQUEST['_rewardsystem_enable_point_price'] ) ) {
					update_post_meta( $post_id, '_rewardsystem_enable_point_price', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_enable_point_price'] ) ) );
				}

				if ( isset( $_REQUEST['_rewardsystem__points'] ) ) {
					update_post_meta( $post_id, '_rewardsystem__points', wc_clean( wp_unslash( $_REQUEST['_rewardsystem__points'] ) ) );
				}

				if ( isset( $_REQUEST['_rewardsystem_point_price_type'] ) ) {
					update_post_meta( $post_id, '_rewardsystem_point_price_type', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_point_price_type'] ) ) );
				}

				if ( isset( $_REQUEST['_rewardsystem_enable_point_price_type'] ) ) {
					update_post_meta( $post_id, '_rewardsystem_enable_point_price_type', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_enable_point_price_type'] ) ) );
				}

				if ( isset( $_REQUEST['_rewardsystem__points_based_on_conversion'] ) ) {
					$sale_price    = isset( $_REQUEST['_sale_price'] ) ? wc_clean( wp_unslash( $_REQUEST['_sale_price'] ) ) : '';
					$regular_price = isset( $_REQUEST['_regular_price'] ) ? wc_clean( wp_unslash( $_REQUEST['_regular_price'] ) ) : '';

					$price           = empty( $sale_price ) ? $regular_price : $sale_price;
					$converted_value = redeem_point_conversion( $price, get_current_user_id() );
					update_post_meta( $post_id, '_rewardsystem__points_based_on_conversion', $converted_value );
				}
			}
			/* Save Point Price Settings - End */

			/* Save Redeeming Points Settings - Start */
			if ( 'yes' == get_option( 'rs_redeeming_activated' ) && '1' === get_option( 'rs_select_redeeming_based_on' ) ) {
				if ( isset( $_REQUEST['_rewardsystem_redeeming_points_enable'] ) ) {
					update_post_meta( $post_id, '_rewardsystem_redeeming_points_enable', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_redeeming_points_enable'] ) ) );
				}

				if ( isset( $_REQUEST['_rewardsystem_max_redeeming_points'] ) ) {
					update_post_meta( $post_id, '_rewardsystem_max_redeeming_points', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_max_redeeming_points'] ) ) );
				}
			}
			/* Save Redeeming Points Settings - End */
		}
	}

	RSSimpleProduct::init();
}
