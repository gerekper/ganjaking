<?php
/**
 * Variable Product Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSVariableProduct' ) ) {

	class RSVariableProduct {

		public static function init() {

			add_action( 'woocommerce_product_options_general_product_data', array( __CLASS__, 'settings_for_general_variable_product' ) );

			add_action( 'woocommerce_product_after_variable_attributes', array( __CLASS__, 'settings_for_variable_product' ), 10, 3 );

			add_action( 'woocommerce_save_product_variation', array( __CLASS__, 'save_settings' ), 10, 2 );

			add_action( 'woocommerce_process_product_meta', array( __CLASS__, 'save_settings_for_variable_product' ), 10, 2 );
		}

		public static function settings_for_variable_product( $loop, $variation_data, $variations ) {
			if ( ! is_admin() ) {
				return;
			}

			wp_localize_script( 'fp_variable_product', 'fp_variation_params', array( 'loop' => $loop ) );
			$variation_data = get_post_meta( $variations->ID );
			if ( 'yes' == get_option( 'rs_point_price_activated' ) && '1' == get_option( 'rs_enable_disable_point_priceing' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_points_price' ) ) {
				$EnablePointPrice = isset( $variation_data['_enable_reward_points_price'][0] ) ? $variation_data['_enable_reward_points_price'][0] : '2';
				woocommerce_wp_select(
					array(
						'id'      => '_enable_reward_points_price[' . $loop . ']',
						'label'   => __( 'Enable Point Pricing', 'rewardsystem' ),
						'class'   => '_enable_reward_points_price_variation srp-point-price-enable',
						'value'   => $EnablePointPrice,
						'default' => '1',
						'options' => array(
							'1' => __( 'Enable', 'rewardsystem' ),
							'2' => __( 'Disable', 'rewardsystem' ),
						),
					)
				);

				$PricingType = isset( $variation_data['_enable_reward_points_pricing_type'][0] ) ? $variation_data['_enable_reward_points_pricing_type'][0] : '';
				woocommerce_wp_select(
					array(
						'id'            => '_enable_reward_points_pricing_type[' . $loop . ']',
						'label'         => __( 'Pricing Display Type', 'rewardsystem' ),
						'value'         => $PricingType,
						'class'         => 'fp_point_price srp-show-if-point-price-enable srp-point-pricing-display-type',
						'default'       => '1',
						'options'       => array(
							'1' => __( 'Currency & Points', 'rewardsystem' ),
							'2' => __( 'Points Only', 'rewardsystem' ),
						),
						'wrapper_class' => ( '1' !== $EnablePointPrice ) ? 'srp-field-hide' : '',
					)
				);

				$PointPriceType = isset( $variation_data['_enable_reward_points_price_type'][0] ) ? $variation_data['_enable_reward_points_price_type'][0] : '';
				woocommerce_wp_select(
					array(
						'id'            => '_enable_reward_points_price_type[' . $loop . ']',
						'label'         => __( 'Point Price Type', 'rewardsystem' ),
						'value'         => $PointPriceType,
						'class'         => 'fp_point_price_currency srp-show-if-point-price-enable srp-point-price-type',
						'default'       => '1',
						'options'       => array(
							'1' => __( 'By Fixed', 'rewardsystem' ),
							'2' => __( 'Based On Conversion', 'rewardsystem' ),
						),
						'wrapper_class' => ( '1' !== $EnablePointPrice || '1' !== $PricingType ) ? 'srp-field-hide' : '',
					)
				);

				$PointPriceValue = isset( $variation_data['_price_points_based_on_conversion'][0] ) ? $variation_data['_price_points_based_on_conversion'][0] : '';
				woocommerce_wp_text_input(
					array(
						'id'            => '_price_points_based_on_conversion[' . $loop . ']',
						'label'         => __( 'Point Price Based on Conversion', 'rewardsystem' ),
						'class'         => 'fp_variation_points_price srp-show-if-point-price-enable srp-point-price-based-on-conversion',
						'size'          => '5',
						'value'         => $PointPriceValue,
						'wrapper_class' => ( '1' !== $EnablePointPrice ) ? 'srp-field-hide' : '',
					)
				);

				$FixedPointPrice = isset( $variation_data['price_points'][0] ) ? $variation_data['price_points'][0] : '';
				woocommerce_wp_text_input(
					array(
						'id'            => 'price_points[' . $loop . ']',
						'label'         => __( 'By Fixed Point Price', 'rewardsystem' ),
						'size'          => '5',
						'class'         => 'fp_variation_points_price_field srp-show-if-point-price-enable srp-point-price-fixed',
						'value'         => $FixedPointPrice,
						'wrapper_class' => ( '1' !== $EnablePointPrice || '1' !== $PointPriceType ) ? 'srp-field-hide' : '',
					)
				);
			}
			if ( 'yes' == get_option( 'rs_product_purchase_activated' ) ) {
				if ( 'yes' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
					$EnableRewardPoint = isset( $variation_data['_enable_reward_points'][0] ) ? $variation_data['_enable_reward_points'][0] : '';
					woocommerce_wp_select(
						array(
							'id'          => '_enable_reward_points[' . $loop . ']',
							'label'       => __( 'Enable SUMO Reward Points', 'rewardsystem' ),
							'default'     => '1',
							'desc_tip'    => true,
							'description' => __(
								'Enable will Turn On Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
								. 'Disable will Turn Off Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. ',
								'rewardsystem'
							),
							'value'       => ! empty( $EnableRewardPoint ) ? $EnableRewardPoint : '2',
							'options'     => array(
								'1' => __( 'Enable', 'rewardsystem' ),
								'2' => __( 'Disable', 'rewardsystem' ),
							),
							'class'       => 'srp-enable-reward-points',
						)
					);

					$RewardType = isset( $variation_data['_select_reward_rule'][0] ) ? $variation_data['_select_reward_rule'][0] : '';
					woocommerce_wp_select(
						array(
							'id'                => '_select_reward_rule[' . $loop . ']',
							'label'             => __( 'Reward Type', 'rewardsystem' ),
							'default'           => '2',
							'value'             => ! empty( $RewardType ) ? $RewardType : '2',
							'options'           => array(
								'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
								'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
							),
							'class'             => 'srp-variation-reward-type srp-show-if-reward-points-enable',
							'custom_attributes' => array(
								'data-loop' => $loop,
							),
						)
					);

					$RewardPoints = isset( $variation_data['_reward_points'][0] ) ? $variation_data['_reward_points'][0] : '';
					woocommerce_wp_text_input(
						array(
							'id'            => '_reward_points[' . $loop . ']',
							'label'         => __( 'Reward Points', 'rewardsystem' ),
							'description'   => __(
								'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
								. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ',
								'rewardsystem'
							),
							'desc_tip'      => true,
							'value'         => $RewardPoints,
							'class'         => 'srp-variation-fixed-points srp-show-if-reward-points-enable',
							'wrapper_class' => ( '2' !== $RewardType ) ? '' : 'srp-field-hide',
						)
					);

					$RewardPercent = isset( $variation_data['_reward_percent'][0] ) ? $variation_data['_reward_percent'][0] : '';
					woocommerce_wp_text_input(
						array(
							'id'            => '_reward_percent[' . $loop . ']',
							'label'         => __( 'Reward Points in Percent %', 'rewardsystem' ),
							'description'   => __(
								'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
								. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ',
								'rewardsystem'
							),
							'desc_tip'      => true,
							'value'         => $RewardPercent,
							'class'         => 'srp-variation-percentage-points srp-show-if-reward-points-enable',
							'wrapper_class' => ( '1' !== $RewardType ) ? '' : 'srp-field-hide',
						)
					);

					$minimium_quantity_value = isset( $variation_data['rs_number_of_qty_for_variable_product'][0] ) ? $variation_data['rs_number_of_qty_for_variable_product'][0] : '';
					woocommerce_wp_text_input(
						array(
							'id'                => 'rs_number_of_qty_for_variable_product[' . $loop . ']',
							'label'             => __( 'Minimum Quantity required to Earn Points', 'rewardsystem' ),
							'type'              => 'number',
							'value'             => $minimium_quantity_value,
							'custom_attributes' => array(
								'step' => '1',
							),
							'class'             => 'srp-show-if-reward-points-enable',
						)
					);
				}
			}
			if ( 'yes' == get_option( 'rs_buyingpoints_activated' ) ) {
				$EnableBuyingPoint = isset( $variation_data['_rewardsystem_buying_reward_points'][0] ) ? $variation_data['_rewardsystem_buying_reward_points'][0] : '';
				woocommerce_wp_select(
					array(
						'id'      => '_rewardsystem_buying_reward_points[' . $loop . ']',
						'label'   => __( 'Enable Buying of SUMO Reward Points', 'rewardsystem' ),
						'default' => '2',
						'value'   => $EnableBuyingPoint,
						'options' => array(
							'1' => __( 'Enable', 'rewardsystem' ),
							'2' => __( 'Disable', 'rewardsystem' ),
						),
					)
				);
				$BuyingPoints = isset( $variation_data['_rewardsystem_assign_buying_points'][0] ) ? $variation_data['_rewardsystem_assign_buying_points'][0] : '';
				woocommerce_wp_text_input(
					array(
						'id'    => '_rewardsystem_assign_buying_points[' . $loop . ']',
						'label' => __( 'Buy Reward Points', 'rewardsystem' ),
						'value' => $BuyingPoints,
					)
				);
			}
			if ( 'yes' == get_option( 'rs_referral_activated' ) && 'yes' == get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) ) {
				$EnableReferralPoint = isset( $variation_data['_enable_referral_reward_points'][0] ) ? $variation_data['_enable_referral_reward_points'][0] : '2';
				woocommerce_wp_select(
					array(
						'id'          => '_enable_referral_reward_points[' . $loop . ']',
						'label'       => __( 'Enable Referral Reward Points', 'rewardsystem' ),
						'default'     => '2',
						'desc_tip'    => true,
						'description' => __(
							'Enable will Turn On Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
							. 'Disable will Turn Off Referral Reward Points for Product Purchase and Category/Product Settings will be considered if it is available.',
							'rewardsystem'
						),
						'value'       => $EnableReferralPoint,
						'options'     => array(
							'1' => __( 'Enable', 'rewardsystem' ),
							'2' => __( 'Disable', 'rewardsystem' ),
						),
						'class'       => 'srp-referral-points-enable',
					)
				);

				$ReferralRewardType = isset( $variation_data['_select_referral_reward_rule'][0] ) ? $variation_data['_select_referral_reward_rule'][0] : '';
				woocommerce_wp_select(
					array(
						'id'            => '_select_referral_reward_rule[' . $loop . ']',
						'label'         => __( 'Referral Reward Type', 'rewardsystem' ),
						'default'       => '2',
						'value'         => $ReferralRewardType,
						'options'       => array(
							'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
							'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
						),
						'class'         => 'srp-show-if-referral-points-enable srp-referral-type',
						'wrapper_class' => ( '2' === $EnableReferralPoint ) ? 'srp-field-hide' : '',
					)
				);

				$ReferralRewardPoints = isset( $variation_data['_referral_reward_points'][0] ) ? $variation_data['_referral_reward_points'][0] : '';
				woocommerce_wp_text_input(
					array(
						'id'            => '_referral_reward_points[' . $loop . ']',
						'label'         => __( 'Referral Reward Points', 'rewardsystem' ),
						'description'   => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ',
							'rewardsystem'
						),
						'desc_tip'      => true,
						'value'         => $ReferralRewardPoints,
						'class'         => 'srp-show-if-referral-points-enable srp-referral-fixed',
						'wrapper_class' => ( '2' === $EnableReferralPoint || '2' === $ReferralRewardType ) ? 'srp-field-hide' : '',
					)
				);

				$ReferralRewardPercent = isset( $variation_data['_referral_reward_percent'][0] ) ? $variation_data['_referral_reward_percent'][0] : '';
				woocommerce_wp_text_input(
					array(
						'id'            => '_referral_reward_percent[' . $loop . ']',
						'label'         => __( 'Referral Reward Points in Percent %', 'rewardsystem' ),
						'description'   => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ',
							'rewardsystem'
						),
						'desc_tip'      => true,
						'value'         => $ReferralRewardPercent,
						'class'         => 'srp-show-if-referral-points-enable srp-referral-percent',
						'wrapper_class' => ( '2' === $EnableReferralPoint || '1' === $ReferralRewardType ) ? 'srp-field-hide' : '',
					)
				);

				$GettingReferRewardType = isset( $variation_data['_select_referral_reward_rule_getrefer'][0] ) ? $variation_data['_select_referral_reward_rule_getrefer'][0] : '';
				woocommerce_wp_select(
					array(
						'id'            => '_select_referral_reward_rule_getrefer[' . $loop . ']',
						'label'         => __( 'Reward Type for Getting Referred', 'rewardsystem' ),
						'default'       => '2',
						'value'         => $GettingReferRewardType,
						'options'       => array(
							'1' => __( 'By Fixed Reward Points', 'rewardsystem' ),
							'2' => __( 'By Percentage of Product Price', 'rewardsystem' ),
						),
						'class'         => 'srp-show-if-referral-points-enable srp-getting-referred-type',
						'wrapper_class' => ( '2' === $EnableReferralPoint ) ? 'srp-field-hide' : '',
					)
				);

				$GettingReferPoints = isset( $variation_data['_referral_reward_points_getting_refer'][0] ) ? $variation_data['_referral_reward_points_getting_refer'][0] : '';
				woocommerce_wp_text_input(
					array(
						'id'            => '_referral_reward_points_getting_refer[' . $loop . ']',
						'label'         => __( 'Reward Points for Getting Referred', 'rewardsystem' ),
						'description'   => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ',
							'rewardsystem'
						),
						'desc_tip'      => true,
						'value'         => $GettingReferPoints,
						'class'         => 'srp-show-if-referral-points-enable srp-getrefer-fixed',
						'wrapper_class' => ( '2' === $EnableReferralPoint || '2' === $GettingReferPoints ) ? 'srp-field-hide' : '',
					)
				);

				$GettingReferPercent = isset( $variation_data['_referral_reward_percent_getting_refer'][0] ) ? $variation_data['_referral_reward_percent_getting_refer'][0] : '';
				woocommerce_wp_text_input(
					array(
						'id'            => '_referral_reward_percent_getting_refer[' . $loop . ']',
						'label'         => __( 'Reward Points in Percent % for Getting Referred', 'rewardsystem' ),
						'description'   => __(
							'When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
							. 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ',
							'rewardsystem'
						),
						'desc_tip'      => true,
						'value'         => $GettingReferPercent,
						'class'         => 'srp-show-if-referral-points-enable srp-getrefer-percent',
						'wrapper_class' => ( '2' === $EnableReferralPoint || '1' === $GettingReferPoints ) ? 'srp-field-hide' : '',
					)
				);
			}

			if ( 'yes' == get_option( 'rs_redeeming_activated' ) && '1' === get_option( 'rs_select_redeeming_based_on' ) ) {
				$enable_redeem = isset( $variation_data['_rewardsystem_redeeming_points_enable'][0] ) ? $variation_data['_rewardsystem_redeeming_points_enable'][0] : '2';
				woocommerce_wp_select(
					array(
						'label'   => __( 'Enable Redeeming Points', 'rewardsystem' ),
						'id'      => '_rewardsystem_redeeming_points_enable[' . $loop . ']',
						'class'   => '_rewardsystem_redeeming_points_enable',
						'default' => '1',
						'options' => array(
							'1' => __( 'Enable', 'rewardsystem' ),
							'2' => __( 'Disable', 'rewardsystem' ),
						),
						'value'   => $enable_redeem,
					)
				);
				$redeem_points = isset( $variation_data['_rewardsystem_max_redeeming_points'][0] ) ? $variation_data['_rewardsystem_max_redeeming_points'][0] : ' ';
				woocommerce_wp_text_input(
					array(
						'label'             => __( 'Maximum Points can be Redeemed', 'rewardsystem' ),
						'id'                => '_rewardsystem_max_redeeming_points[' . $loop . ']',
						'class'             => 'srp-show-if-redeem-points-enable',
						'default'           => '',
						'custom_attributes' => array(
							'min' => '1',
						),
						'value'             => $redeem_points,
						'wrapper_class'     => ( '1' !== $enable_redeem ) ? 'srp-field-hide' : '',
					)
				);
			}
		}

		public static function save_settings( $variation_id, $i ) {
			if ( isset( $_REQUEST['_enable_reward_points_price_type'][ $i ] ) ) {
				update_post_meta( $variation_id, '_enable_reward_points_price_type', wc_clean( wp_unslash( $_REQUEST['_enable_reward_points_price_type'][ $i ] ) ) );
			}

			$regular_price  = isset( $_REQUEST['variable_regular_price'][ $i ] ) ? wc_clean( wp_unslash( $_REQUEST['variable_regular_price'][ $i ] ) ) : '';
			$Price          = isset( $_REQUEST['variable_sale_price'][ $i ] ) ? wc_clean( wp_unslash( $_REQUEST['variable_sale_price'][ $i ] ) ) : $regilar_price;
			$PointPriceType = get_post_meta( $variation_id, '_enable_reward_points_price_type', true );
			if ( 2 == $PointPriceType ) {
				$Points = redeem_point_conversion( $Price, get_current_user_id() );
				update_post_meta( $variation_id, '_price_points_based_on_conversion', stripslashes( $Points ) );
			}

			if ( isset( $_REQUEST['_rewardsystem_buying_reward_points'][ $i ] ) ) {
				update_post_meta( $variation_id, '_rewardsystem_buying_reward_points', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_buying_reward_points'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_rewardsystem_assign_buying_points'][ $i ] ) ) {
				update_post_meta( $variation_id, '_rewardsystem_assign_buying_points', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_assign_buying_points'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_reward_points'][ $i ] ) ) {
				update_post_meta( $variation_id, '_reward_points', wc_clean( wp_unslash( $_REQUEST['_reward_points'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_enable_reward_points_price'][ $i ] ) ) {
				update_post_meta( $variation_id, '_enable_reward_points_price', wc_clean( wp_unslash( $_REQUEST['_enable_reward_points_price'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['price_points'][ $i ] ) ) {
				update_post_meta( $variation_id, 'price_points', wc_clean( wp_unslash( $_REQUEST['price_points'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_enable_reward_points_pricing_type'][ $i ] ) ) {
				update_post_meta( $variation_id, '_enable_reward_points_pricing_type', wc_clean( wp_unslash( $_REQUEST['_enable_reward_points_pricing_type'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_reward_percent'][ $i ] ) ) {
				update_post_meta( $variation_id, '_reward_percent', wc_clean( wp_unslash( $_REQUEST['_reward_percent'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['rs_number_of_qty_for_variable_product'][ $i ] ) ) {
				$number_of_qty = isset( $_REQUEST['rs_number_of_qty_for_variable_product'][ $i ] ) ? wc_clean( wp_unslash( $_REQUEST['rs_number_of_qty_for_variable_product'][ $i ] ) ) : '';
				update_post_meta( $variation_id, 'rs_number_of_qty_for_variable_product', $number_of_qty );
			}

			if ( isset( $_REQUEST['_select_reward_rule'][ $i ] ) ) {
				update_post_meta( $variation_id, '_select_reward_rule', wc_clean( wp_unslash( $_REQUEST['_select_reward_rule'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_referral_reward_points'][ $i ] ) ) {
				update_post_meta( $variation_id, '_referral_reward_points', wc_clean( wp_unslash( $_REQUEST['_referral_reward_points'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_referral_reward_percent'][ $i ] ) ) {
				update_post_meta( $variation_id, '_referral_reward_percent', wc_clean( wp_unslash( $_REQUEST['_referral_reward_percent'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_referral_reward_percent_getting_refer'][ $i ] ) ) {
				update_post_meta( $variation_id, '_referral_reward_percent_getting_refer', wc_clean( wp_unslash( $_REQUEST['_referral_reward_percent_getting_refer'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_select_referral_reward_rule_getrefer'][ $i ] ) ) {
				update_post_meta( $variation_id, '_select_referral_reward_rule_getrefer', wc_clean( wp_unslash( $_REQUEST['_select_referral_reward_rule_getrefer'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_referral_reward_points_getting_refer'][ $i ] ) ) {
				update_post_meta( $variation_id, '_referral_reward_points_getting_refer', wc_clean( wp_unslash( $_REQUEST['_referral_reward_points_getting_refer'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_select_referral_reward_rule'][ $i ] ) ) {
				update_post_meta( $variation_id, '_select_referral_reward_rule', wc_clean( wp_unslash( $_REQUEST['_select_referral_reward_rule'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_enable_reward_points'][ $i ] ) ) {
				update_post_meta( $variation_id, '_enable_reward_points', wc_clean( wp_unslash( $_REQUEST['_enable_reward_points'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_enable_referral_reward_points'][ $i ] ) ) {
				update_post_meta( $variation_id, '_enable_referral_reward_points', wc_clean( wp_unslash( $_REQUEST['_enable_referral_reward_points'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_rewardsystem_redeeming_points_enable'][ $i ] ) ) {
				update_post_meta( $variation_id, '_rewardsystem_redeeming_points_enable', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_redeeming_points_enable'][ $i ] ) ) );
			}

			if ( isset( $_REQUEST['_rewardsystem_max_redeeming_points'][ $i ] ) ) {
				update_post_meta( $variation_id, '_rewardsystem_max_redeeming_points', wc_clean( wp_unslash( $_REQUEST['_rewardsystem_max_redeeming_points'][ $i ] ) ) );
			}
		}

		public static function settings_for_general_variable_product() {
			global $product_object;
			if ( ! is_admin() ) {
				return;
			}

			if ( 'yes' == get_option( 'rs_reward_action_activated' ) ) {
				if ( 'yes' == get_option( 'rs_enable_product_review_points', 'yes' ) ) {
					woocommerce_wp_text_input(
						array(
							'id'    => 'rs_product_review_reward_points_for_product_level',
							'class' => 'show_if_review_reward_points_enable',
							'name'  => 'rs_product_review_reward_points_for_product_level',
							'label' => __( 'Product Review Reward Points ', 'rewardsystem' ),
						)
					);
				}
			}
		}

		public static function save_settings_for_variable_product( $post_id, $post ) {
			if ( ! is_admin() ) {
				return;
			}

			if ( 'yes' == get_option( 'rs_reward_action_activated' ) ) {
				if ( 'yes' == get_option( 'rs_enable_product_review_points', 'yes' ) ) {
					if ( isset( $_REQUEST['rs_product_review_reward_points_for_product_level'] ) ) {
						update_post_meta( $post_id, 'rs_product_review_reward_points_for_product_level', wc_clean( wp_unslash( $_REQUEST['rs_product_review_reward_points_for_product_level'] ) ) );
					}
				}
			}
		}
	}

	RSVariableProduct::init();
}
