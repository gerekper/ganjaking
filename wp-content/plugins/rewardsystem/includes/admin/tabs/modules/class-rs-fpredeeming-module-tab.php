<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSRedeemingModule' ) ) {

	class RSRedeemingModule {

		public static function init() {

			add_action( 'rs_default_settings_fpredeeming' , array( __CLASS__, 'set_default_value' ) ) ;

			add_action( 'woocommerce_rs_settings_tabs_fpredeeming' , array( __CLASS__, 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

			add_action( 'woocommerce_update_options_fprsmodules_fpredeeming' , array( __CLASS__, 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

			add_action( 'woocommerce_admin_field_exclude_product_selection' , array( __CLASS__, 'rs_select_product_to_exclude' ) ) ;

			add_action( 'woocommerce_admin_field_include_product_selection' , array( __CLASS__, 'rs_select_product_to_include' ) ) ;

			add_action( 'woocommerce_admin_field_user_role_based_minimum_available_point_restriction' , array( __CLASS__, 'user_role_based_minimum_available_point_restriction' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_disable_redeeming_module' , array( __CLASS__, 'enable_module' ) ) ;

			if ( class_exists( 'SUMODiscounts' ) ) {
				add_filter( 'woocommerce_fpredeeming' , array( __CLASS__, 'setting_for_hide_redeem_field_when_sumo_discount_is_active' ) ) ;
			}

			add_action( 'fp_action_to_reset_module_settings_fpredeeming' , array( __CLASS__, 'reset_redeeming_module' ) ) ;

			if ( class_exists( 'SUMOMemberships' ) ) {
				add_filter( 'woocommerce_fpredeeming' , array( __CLASS__, 'add_field_for_membership' ) ) ;
			}

			add_action( 'woocommerce_admin_field_rs_user_role_dynamics_for_redeem' , array( __CLASS__, 'render_add_rule_for_redeeming_percentage' ) ) ;

			add_filter( 'woocommerce_fpredeeming' , array( __CLASS__, 'reward_system_add_settings_to_action' ) ) ;

			add_action( 'woocommerce_admin_field_rs_user_purchase_history_redeem' , array( __CLASS__, 'render_redeeming_user_purchase_history_rule' ) ) ;

			add_action( 'rs_display_save_button_fpredeeming' , array( 'RSTabManagement', 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fpredeeming' , array( 'RSTabManagement', 'rs_display_reset_button' ) ) ;

			add_action( 'woocommerce_admin_field_rs_include_products_for_product_level_redeem' , array( __CLASS__, 'include_products_for_product_level_redeem' ) ) ;
			add_action( 'woocommerce_admin_field_rs_exclude_products_for_product_level_redeem' , array( __CLASS__, 'exclude_products_for_product_level_redeem' ) ) ;
			add_action( 'woocommerce_admin_field_save_and_update_button' , array( __CLASS__, 'rs_save_button_for_update' ) ) ;
		}

		/*
		 * Render redeeming user purchase history rule.
		 */

		public static function render_redeeming_user_purchase_history_rule() {
			include SRP_PLUGIN_PATH . '/includes/admin/views/redeeming-user-purchase-history.php' ;
		}

		/*
		 * Function to Define Name of the Tab
		 */

		public static function add_field_for_membership( $settings ) {
			$updated_settings = array() ;
			$membership_level = sumo_get_membership_levels() ;
			foreach ( $settings as $section ) {
				$updated_settings[] = $section ;
				if ( isset( $section[ 'id' ] ) && '_rs_member_level_redeem_points_purchase_history' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
										$updated_settings[] = array(
												'type' => 'rs_modulecheck_end',
										) ;
										$updated_settings[] = array(
										'type' => 'rs_wrapper_start',
										) ;                
										$updated_settings[] = array(
										'name' => __( 'Reward Points Redeem Percentage based on Membership Plan' , 'rewardsystem' ),
										'type' => 'title',
										'id'   => '_rs_membership_plan_for_redeem',
										) ;
										$updated_settings[] = array(
										'name'   => __( 'Don\'t allow Redeeming when the user hasn\'t purchased any membership plan through SUMO Memberships' , 'rewardsystem' ),
										'desc'   => __( 'Don\'t allow Redeeming when the user hasn\'t purchased any membership plan through SUMO Memberships' , 'rewardsystem' ),
										'id'     => 'rs_restrict_redeem_when_no_membership_plan',
										'css'    => 'min-width:150px;',
										'type'   => 'checkbox',
										'newids' => 'rs_restrict_redeem_when_no_membership_plan',
										) ;
										$updated_settings[] = array(
										'name'    => __( 'Membership Plan based Redeem Level' , 'rewardsystem' ),
										'desc'    => __( 'Enable this option to modify Redeem points based on membership plan' , 'rewardsystem' ),
										'id'      => 'rs_enable_membership_plan_based_redeem',
										'css'     => 'min-width:150px;',
										'std'     => 'yes',
										'default' => 'yes',
										'type'    => 'checkbox',
										'newids'  => 'rs_enable_membership_plan_based_redeem',
										) ;
										foreach ( $membership_level as $key => $value ) {
											$updated_settings[] = array(
												'name'     => __( 'Reward Points Redeem Percentage for ' . $value , 'rewardsystem' ),
												'desc'     => __( 'Please Enter Percentage of Redeem for ' . $value , 'rewardsystem' ),
												'class'    => 'rewardpoints_membership_plan_for_redeem',
												'id'       => 'rs_reward_membership_plan_for_redeem' . $key,
												'css'      => 'min-width:150px;',
												'std'      => '100',
												'type'     => 'text',
												'newids'   => 'rs_reward_membership_plan_for_redeem' . $key,
												'desc_tip' => true,
													) ;
										}
										$updated_settings[] = array(
										'type' => 'sectionend',
										'id'   => '_rs_membership_plan_for_redeem',
										) ;
				}
			}
			return $updated_settings ;
		}

		/*
		 * Add Rule for redeeming percentage.
		 */

		public static function render_add_rule_for_redeeming_percentage() {
			include SRP_PLUGIN_PATH . '/includes/admin/views/redeeming-percentage-rule.php' ;
		}

		/*
		 * Function to add settings for Member Level in Member Level Tab
		 */

		public static function reward_system_add_settings_to_action( $settings ) {
			global $wp_roles ;
			$updated_settings = array() ;
			$mainvariable     = array() ;
			global $woocommerce ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && '_rs_user_role_reward_points_for_redeem' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
					foreach ( $wp_roles->role_names as $value => $key ) {
						$updated_settings[] = array(
							'name'     => __( 'Reward Points Redeeming Percentage for ' . sanitize_text_field($key) . ' User Role' , 'rewardsystem' ),
							'desc'     => __( 'Please Enter Percentage of Redeeming Reward Points for ' . sanitize_text_field($key) , 'rewardsystem' ),
							'class'    => 'rewardpoints_userrole_for_redeem',
							'id'       => 'rs_reward_user_role_for_redeem_' . sanitize_text_field($value),
							'css'      => 'min-width:150px;',
							'std'      => '100',
							'default'  => '100',
							'type'     => 'text',
							'newids'   => 'rs_reward_user_role_for_redeem_' . sanitize_text_field($value),
							'desc_tip' => true,
								) ;
					}
					$updated_settings[] = array(
						'type' => 'sectionend', 'id'   => '_rs_user_role_reward_points_for_redeem',
							) ;
				}

				$updated_settings[] = $section ;
			}

			return $updated_settings ;
		}

		/*
		 * Function label settings to Member Level Tab
		 */

		public static function reward_system_admin_fields() {
			global $woocommerce ;
			//Section and option details
			if ( class_exists( 'WooCommerce_PDF_Invoices' ) ) {
				$section_title = __('Message Settings in Edit Order Page and Invoices' , 'rewardsystem');
			} else {
				$section_title = __('Message Settings in Edit Order Page', 'rewardsystem') ;
			}
			$newcombinedarray = fp_paid_order_status() ;
			$categorylist     = fp_product_category() ;
			$available_payment_gateways = rs_get_payment_gateways() ;
			
			/**
			 * Hook:woocommerce_fpredeeming.
			 * 
			 * @since 1.0
			 */
			return apply_filters( 'woocommerce_fpredeeming' , array(
				array(
					'type' => 'rs_modulecheck_start',
				),
				array(
					'name' => __( 'Redeeming Module' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_activate_redeeming_module',
				),
				array(
					'type' => 'rs_enable_disable_redeeming_module',
				),
				array(
					'name'     => __( 'Apply Redeeming Before Tax' , 'rewardsystem' ),
					'desc'     => __('Works with WooCommerce Versions 2.2 or older', 'rewardsystem'),
					'id'       => 'rs_apply_redeem_before_tax',
					'std'      => '1',
					'default'  => '1',
					'type'     => 'select',
					'newids'   => 'rs_apply_redeem_before_tax',
					'options'  => array(
						'1' => __( 'Enable' , 'rewardsystem' ),
						'2' => __( 'Disable' , 'rewardsystem' ),
					),
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Free Shipping when Reward Points is Redeemed' , 'rewardsystem' ),
					'id'       => 'rs_apply_shipping_tax',
					'std'      => '2',
					'default'  => '2',
					'type'     => 'select',
					'newids'   => 'rs_apply_shipping_tax',
					'options'  => array(
						'1' => __( 'Enable' , 'rewardsystem' ),
						'2' => __( 'Disable' , 'rewardsystem' ),
					),
					'desc_tip' => true,
				),
				array( 'type' => 'sectionend', 'id' => '_rs_activate_redeeming_module' ),
				array(
					'type' => 'rs_modulecheck_end',
				),

				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Redeeming Points Global Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => 'rs_redeeming_global_setting',
				),
				array(
					'name'    => __( 'Redeeming Based on' , 'rewardsystem' ),
					'id'      => 'rs_select_redeeming_based_on',
					'std'     => '2',
					'default' => '2',
					'type'    => 'select',
					'newids'  => 'rs_select_redeeming_based_on',
					'options' => array(
						'1' => __( 'Product Level' , 'rewardsystem' ),
						'2' => __( 'Cart Level' , 'rewardsystem' ),
				),
					'desc'    => __( '<b>i) Cart Level:</b> Discount from redeeming points will be applied to the Cart Subtotal.<br/>
									<b>ii) Product Level:</b> You can set maximum points to redeem in each Product. For Simple Product, you can see the settings in General Section whereas you can see the settings in each variation in Variable Product.' , 'rewardsystem' ),
					'desc_tip' => true,
				),
				array(
					'name'    => __( 'Maximum Redemption Error Message' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_message_for_product_level',
					'std'     => __( 'You can redeem a maximum of [rsmaximumpoints] points in this order' , 'rewardsystem' ),
					'default' => __( 'You can redeem a maximum of [rsmaximumpoints] points in this order' , 'rewardsystem' ),
					'type'    => 'textarea',
					'newids'  => 'rs_redeeming_message_for_product_level',
				),
				array(
					'name'    => __( 'Redeeming Restriction Error Message' , 'rewardsystem' ),
					'id'      => 'rs_error_msg_for_disabled_redeeming_products',
					'std'     => __( 'Points are restricted to redeeming for the products added to the cart' , 'rewardsystem' ),
					'default' => __( 'Points are restricted to redeeming for the products added to the cart' , 'rewardsystem' ),
					'type'    => 'textarea',
					'newids'  => 'rs_error_msg_for_disabled_redeeming_products',
				),
				array(
					'name'    => __( 'Bulk Update' , 'rewardsystem' ),
					'id'      => 'rs_enable_bulk_update_for_product_level_redeeming',
					'std'     => 'no',
					'default' => 'no',
					'type'    => 'checkbox',
					'newids'  => 'rs_enable_bulk_update_for_product_level_redeeming',
				),
				array(
					'name'    => __( 'Product Selection' , 'rewardsystem' ),
					'id'      => 'rs_product_level_redeem_product_selection_type',
					'std'     => '1',
					'type'    => 'select',
					'newids'  => 'rs_product_level_redeem_product_selection_type',
					'options'  => array(
						'1' => __( 'All Products' , 'rewardsystem' ),
						'2' => __( 'Include Products' , 'rewardsystem' ),
						'3' => __( 'Exclude Products' , 'rewardsystem' ),
						'4' => __( 'Include Categories' , 'rewardsystem' ),
						'5' => __( 'Exclude Categories' , 'rewardsystem' ),
					),
					'class' => 'srp-rp-bulk-update-opt',
				),
				array(
					'type' => 'rs_include_products_for_product_level_redeem',
				),
				array(
					'type' => 'rs_exclude_products_for_product_level_redeem',
				),
				array(
					'name'    => __( 'Select the Categories to Include' , 'rewardsystem' ),
					'id'      => 'rs_product_level_redeem_include_categories',
					'std'     => '',
					'class'   => 'srp-rp-product-include-categories srp-rp-bulk-update-opt srp-rp-product-selection',
					'default' => '',
					'newids'  => 'rs_product_level_redeem_include_categories',
					'type'    => 'multiselect',
					'options' => fp_product_category(),
				),
				array(
					'name'    => __( 'Select the Categories to Exclude' , 'rewardsystem' ),
					'id'      => 'rs_product_level_redeem_exclude_categories',
					'std'     => '',
					'class'   => 'srp-rp-product-exclude-categories srp-rp-bulk-update-opt srp-rp-product-selection',
					'default' => '',
					'newids'  => 'rs_product_level_redeem_exclude_categories',
					'type'    => 'multiselect',
					'options' => fp_product_category(),
				),
				array(
					'name'    => __( 'Enable Redeeming Points' , 'rewardsystem' ),
					'id'      => 'rs_enable_maximum_redeeming_points',
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'newids'  => 'rs_enable_maximum_redeeming_points',
					'options' => array(
						'1' => __( 'Enable' , 'rewardsystem' ),
						'2' => __( 'Disable' , 'rewardsystem' ),
				),
					'class'  => 'srp-rp-bulk-update-opt',
				),
				array(
					'name'              => __( 'Maximum Points can be Redeemed' , 'rewardsystem' ),
					'id'                => 'rs_maximum_redeeming_points',
					'std'               => '',
					'default'           => '',
					'type'              => 'number',
					'newids'            => 'rs_maximum_redeeming_points',
					'custom_attributes' => array(
						'min' => 1,
					),
					'class'  => 'srp-rp-bulk-update-opt',
				),
				array(
					'type' => 'save_and_update_button',
				),
				array( 'type' => 'sectionend', 'id' => 'rs_redeeming_global_setting' ),
				array(
					'type' => 'rs_wrapper_end',
				),

				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Reward Points Redeeming Order Status Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => 'rs_redeeming_status_setting',
				),
				array(
					'name'     => __( 'Redeemed Points will be deducted when the Order Status reaches' , 'rewardsystem' ),
					'desc'     => __( 'We have planned to remove this setting in our next update to avoid unnecessary confusion. Hence, the points applied to the order will be deducted from the account once a user has placed the order & it will be added back to the account when the order status changes to Failure Statuses such as <b>Cancelled/Refunded/Failed.</b>
<br><br>Please contact our support at http://fantasticplugins.com/support by opening a ticket if you have any queries/suggestions regarding this scenario.' , 'rewardsystem' ),
					'id'       => 'rs_order_status_control_redeem',
					'std'      => array( 'completed', 'pending', 'processing', 'on-hold' ),
					'default'  => array( 'completed', 'pending', 'processing', 'on-hold' ),
					'type'     => 'multiselect',
					'options'  => $newcombinedarray,
					'newids'   => 'rs_order_status_control_redeem',
					'desc_tip' => false,
				),
				array( 'type' => 'sectionend', 'id' => 'rs_redeeming_status_setting' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Member Level Settings for Redeeming' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => 'rs_member_level_setting_for_redeem',
				),
				array(
					'name'     => __( 'Priority Level Selection' , 'rewardsystem' ),
					'desc'     => __( 'If more than one type(level) is enabled then use the highest/lowest percentage' , 'rewardsystem' ),
					'id'       => 'rs_choose_priority_level_selection_for_redeem',
					'class'    => 'rs_choose_priority_level_selection_for_redeem',
					'std'      => '1',
					'default'  => '1',
					'type'     => 'radio',
					'newids'   => 'rs_choose_priority_level_selection_for_redeem',
					'options'  => array(
						'1' => __( 'Use the level that gives highest percentage' , 'rewardsystem' ),
						'2' => __( 'Use the level that gives lowest percentage' , 'rewardsystem' ),
					),
					'desc_tip' => true,
				),
				array( 'type' => 'sectionend', 'id' => 'rs_member_level_setting_for_redeem' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Reward Points Redeeming Percentage based on User Role' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_user_role_reward_points_for_redeem',
				),
				array(
					'name'    => __( 'User Role based Redeeming Level' , 'rewardsystem' ),
					'desc'    => __( 'Enable this option to modify Redeeming points based on user role' , 'rewardsystem' ),
					'id'      => 'rs_enable_user_role_based_reward_points_for_redeem',
					'css'     => 'min-width:150px;',
					'std'     => 'yes',
					'default' => 'yes',
					'type'    => 'checkbox',
					'newids'  => 'rs_enable_user_role_based_reward_points_for_redeem',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_user_role_reward_points_for_redeem' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Reward Points Redeeming Percentage based on Earned Points' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_member_level_earning_points_for_redeem',
				),
				array(
					'name'    => __( 'Points to Redeem based on Earned Points' , 'rewardsystem' ),
					'desc'    => __( 'Enable this option to modify Redeeming Points percentage based on earned points' , 'rewardsystem' ),
					'id'      => 'rs_enable_redeem_level_based_reward_points',
					'css'     => 'min-width:150px;',
					'std'     => 'no',
					'default' => 'no',
					'type'    => 'checkbox',
					'newids'  => 'rs_enable_earned_level_based_reward_points_for_redeem',
				),
				array(
					'name'    => __( 'Earned Points is decided' , 'rewardsystem' ),
					'id'      => 'rs_select_redeem_points_based_on',
					'css'     => 'min-width:150px;',
					'std'     => '1',
					'type'    => 'select',
					'newids'  => 'rs_select_redeem_points_based_on',
					'options' => array(
						'1' => __( 'Based on Total Earned Points' , 'rewardsystem' ),
						'2' => __( 'Based on Current Points' , 'rewardsystem' ),
				),
				),
				array(
					'type' => 'rs_user_role_dynamics_for_redeem',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_member_level_earning_points_for_redeem' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Reward Points Redeeming Percentage based on Purchase History' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_member_level_redeem_points_purchase_history',
				),
				array(
					'name'    => __( 'Purchase History based on Redeeming Level' , 'rewardsystem' ),
					'desc'    => __( 'Enable this option to modify Redeeming points based on Purchase history' , 'rewardsystem' ),
					'id'      => 'rs_enable_user_purchase_history_based_reward_points_redeem',
					'css'     => 'min-width:150px;',
					'std'     => 'yes',
					'default' => 'yes',
					'type'    => 'checkbox',
					'newids'  => 'rs_enable_user_purchase_history_based_reward_points_redeem',
				),
				array(
					'type' => 'rs_user_purchase_history_redeem',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_member_level_redeem_points_purchase_history' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Redeeming Settings for Cart Page' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_redeem_settings',
				),
				array(
					'name'    => __( 'Enable Automatic Points Redeeming in Cart Page' , 'rewardsystem' ),
					'desc'    => __( 'When enabled, available reward points will be automatically applied on cart to get a discount' , 'rewardsystem' ),
					'id'      => 'rs_enable_disable_auto_redeem_points',
					'type'    => 'checkbox',
					'std'     => 'no',
					'default' => 'no',
					'newids'  => 'rs_enable_disable_auto_redeem_points',
				),
				array(
					'name'    => __( 'Enable Automatic Points Redeeming in Checkout Page' , 'rewardsystem' ),
					'desc'    => __( 'When enabled, available reward points will be automatically applied on checkout to get a discount when the page is redirected to checkout directly from shop page' , 'rewardsystem' ),
					'id'      => 'rs_enable_disable_auto_redeem_checkout',
					'type'    => 'checkbox',
					'std'     => 'no',
					'default' => 'no',
					'newids'  => 'rs_enable_disable_auto_redeem_checkout',
				),
				array(
					'name'    => __( 'Manual Redeeming Field Type' , 'rewardsystem' ),
					'id'      => 'rs_redeem_field_type_option',
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'newids'  => 'rs_redeem_field_type_option',
					'options' => array(
						'1' => __( 'Default' , 'rewardsystem' ),
						'2' => __( 'Button' , 'rewardsystem' ),
					),
				),
				array(
					'name'              => __( 'Percentage of Cart Total to be Redeemed' , 'rewardsystem' ),
					'desc'              => __( 'Enter the Percentage of the cart total that has to be Redeemed' , 'rewardsystem' ),
					'id'                => 'rs_percentage_cart_total_redeem',
					'std'               => 100,
					'default'           => 100,
					'type'              => 'number',
					'newids'            => 'rs_percentage_cart_total_redeem',
					'desc_tip'          => true,
					'custom_attributes' => array(
						'min' => 1,
					),
				),
				array(
					'name'    => __( 'Redeeming Button Notice' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_button_option_message',
					'std'     => __( '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed' , 'rewardsystem' ),
					'default' => __( '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed' , 'rewardsystem' ),
					'type'    => 'textarea',
					'newids'  => 'rs_redeeming_button_option_message',
				),
				array(
					'name'    => __( 'Enable Default Redeeming Type' , 'rewardsystem' ),
					'id'      => 'rs_default_redeeming_type_enabled',
					'type'    => 'checkbox',
					'std'     => 'no',
					'default' => 'no',
					'newids'  => 'rs_default_redeeming_type_enabled',
					'class'   => 'srp-rp-cart-level-redeem',
					'desc'    => __('By enabling this checkbox, you can set the default redeeming type with either Predefined Values/Multiples of Points.', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Default Redeeming Type' , 'rewardsystem' ),
					'id'      => 'rs_default_redeeming_type',
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'newids'  => 'rs_default_redeeming_type',
					'options' => array(
						'1' => __( 'Predefined Values' , 'rewardsystem' ),
						'2' => __( 'Multiples of Points' , 'rewardsystem' ),
								),
				),
				array(
					'name'    => __( 'Enter the predefined values' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_predefined_option_values',
					'type'    => 'textarea',
					'desc_tip' => true,
										'std'     => '',
					'default' => '',
					'desc'    => __('Enter the values with a comma separator', 'rewardsystem'),
					'newids'  => 'rs_redeeming_predefined_option_values',
				),
				array(
					'name'    => __( 'Enter the Multiplier Value' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_start_sequence_number',
					'type'    => 'text',
										'std'     => '',
					'default' => '',
					'newids'  => 'rs_redeeming_start_sequence_number',
				),
				array(
					'name'    => __( 'Points Selection Label' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_predefined_points_selection_label',
					'type'    => 'textarea',
										'std'     => 'Points Selection',
					'default' => 'Points Selection',
					'newids'  => 'rs_redeeming_predefined_points_selection_label',
				),
				array(
					'name'    => __( 'Choose Option Label' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_predefined_choose_option_label',
					'type'    => 'textarea',
										'std'     => 'Select the Points',
					'default' => 'Select the Points',
					'newids'  => 'rs_redeeming_predefined_choose_option_label',
				),
				array(
					'name'    => __( 'Message' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_start_sequence_msg',
					'type'    => 'textarea',
										'std'     => 'Please enter the points value multiples of {multiplier_value}',
					'default' => 'Please enter the points value multiples of {multiplier_value}',
					'newids'  => 'rs_redeeming_start_sequence_msg',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_redeem_settings' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Redeeming Settings for Checkout Page' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_restriction_in_checkout',
				),
				array(
					'name'    => __( 'Show/hide Redeeming Field in Checkout Page' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_redeem_field_checkout',
					'std'     => '2',
					'default' => '2',
					'type'    => 'select',
					'newids'  => 'rs_show_hide_redeem_field_checkout',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Manual Redeeming Field Type' , 'rewardsystem' ),
					'id'      => 'rs_redeem_field_type_option_checkout',
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'newids'  => 'rs_redeem_field_type_option_checkout',
					'options' => array(
						'1' => __( 'Default' , 'rewardsystem' ),
						'2' => __( 'Button' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Enable Default Redeeming Type' , 'rewardsystem' ),
					'id'      => 'rs_default_redeeming_type_enabled_checkout',
					'type'    => 'checkbox',
					'std'     => 'no',
					'default' => 'no',
					'newids'  => 'rs_default_redeeming_type_enabled_checkout',
					'class'   => 'srp-rp-cart-level-redeem',
					'desc'    => __('By enabling this checkbox, you can set the default redeeming type with either Predefined Values/Multiples of Points.', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Default Redeeming Type' , 'rewardsystem' ),
					'id'      => 'rs_default_redeeming_type_checkout',
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'newids'  => 'rs_default_redeeming_type_checkout',
					'options' => array(
						'1' => __( 'Predefined Values' , 'rewardsystem' ),
						'2' => __( 'Multiples of Points' , 'rewardsystem' ),
					),
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'    => __( 'Enter the predefined values' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_predefined_option_values_checkout',
					'type'    => 'textarea',
					'desc_tip' => true,
					'std'     => '',
					'default' => '',
					'desc'    => __('Enter the values with a comma separator', 'rewardsystem'),
					'newids'  => 'rs_redeeming_predefined_option_values_checkout',
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'    => __( 'Enter the Multiplier Value' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_start_sequence_number_checkout',
					'type'    => 'text',
					'std'     => '',
					'default' => '',                            
					'newids'  => 'rs_redeeming_start_sequence_number_checkout',
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'    => __( 'Points Selection Label' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_predefined_points_selection_label_checkout',
					'type'    => 'textarea',
					'std'     => 'Points Selection',                            
					'default' => 'Points Selection',
					'newids'  => 'rs_redeeming_predefined_points_selection_label_checkout',
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'    => __( 'Choose Option Label' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_predefined_choose_option_label_checkout',
					'type'    => 'textarea',
					'std'     => 'Select the Points',
					'default' => 'Select the Points',
					'newids'  => 'rs_redeeming_predefined_choose_option_label_checkout',
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'    => __( 'Message' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_start_sequence_msg_checkout',
					'type'    => 'textarea',
					'std'     => 'Please enter the points value multiples of {multiplier_value}',                               
					'default' => 'Please enter the points value multiples of {multiplier_value}',
					'newids'  => 'rs_redeeming_start_sequence_msg_checkout',
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'              => __( 'Percentage of Cart Total to be Redeemed' , 'rewardsystem' ),
					'desc'              => __( 'Enter the Percentage of the cart total that has to be Redeemed' , 'rewardsystem' ),
					'id'                => 'rs_percentage_cart_total_redeem_checkout',
					'std'               => 100,
					'default'           => 100,
					'type'              => 'number',
					'newids'            => 'rs_percentage_cart_total_redeem_checkout',
					'desc_tip'          => true,
					'custom_attributes' => array(
						'min' => 1,
					),
				),
				array(
					'name'     => __( 'Show/Hide WooCommerce Coupon Field' , 'rewardsystem' ),
					'id'       => 'rs_show_hide_coupon_field_checkout',
					'std'      => '1',
					'default'  => '1',
					'type'     => 'select',
					'newids'   => 'rs_show_hide_coupon_field_checkout',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Redeeming Field label' , 'rewardsystem' ),
					'desc'     => __( 'This Text will be displayed as redeeming field label in checkout page' , 'rewardsystem' ),
					'id'       => 'rs_reedming_field_label_checkout',
					'std'      => __( 'Have Reward Points ?' , 'rewardsystem' ),
					'default'  => __( 'Have Reward Points ?' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_reedming_field_label_checkout',
					'class'    => 'rs_reedming_field_label_checkout',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Redeeming Field Link label' , 'rewardsystem' ),
					'desc'     => __( 'This Text will be displayed as redeeming field link label in checkout page' , 'rewardsystem' ),
					'id'       => 'rs_reedming_field_link_label_checkout',
					'std'      => __( 'Redeem it' , 'rewardsystem' ),
					'default'  => __( 'Redeem it' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_reedming_field_link_label_checkout',
					'class'    => 'rs_reedming_field_link_label_checkout',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Redeeming Link Call to Action' , 'rewardsystem' ),
					'desc'     => __( 'Show/Hide Redeem It Link Call To Action in WooCommerce' , 'rewardsystem' ),
					'id'       => 'rs_show_hide_redeem_it_field_checkout',
					'std'      => '1',
					'default'  => '1',
					'type'     => 'select',
					'newids'   => 'rs_show_hide_redeem_it_field_checkout',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Redeeming Button Message ' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message for the Redeeming Button' , 'rewardsystem' ),
					'id'       => 'rs_redeeming_button_option_message_checkout',
					'std'      => __( '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed' , 'rewardsystem' ),
					'default'  => __( '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed' , 'rewardsystem' ),
					'type'     => 'textarea',
					'newids'   => 'rs_redeeming_button_option_message_checkout',
					'desc_tip' => true,
				),
				array( 'type' => 'sectionend', 'id' => '_rs_restriction_in_checkout' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Redeeming Settings for Cart and Checkout Page' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_restriction_in_cart_and_checkout',
				),
				array(
					'name'    => __( 'Redeeming Field Label' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_redeem_caption',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_redeem_caption',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'     => __( 'Redeeming Field Label' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Label which will be displayed in Redeem Field' , 'rewardsystem' ),
					'id'       => 'rs_redeem_field_caption',
					'std'      => __( 'Redeem your Reward Points:' , 'rewardsystem' ),
					'default'  => __( 'Redeem your Reward Points:' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_redeem_field_caption',
					'desc_tip' => true,
				),
				array(
					'name'    => __( 'Show/Hide Redeeming Field Placeholder' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_redeem_placeholder',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_redeem_placeholder',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'     => __( 'Placeholder' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Placeholder which will be displayed in Redeem Field' , 'rewardsystem' ),
					'id'       => 'rs_redeem_field_placeholder',
					'std'      => __( 'Reward Points to Enter' , 'rewardsystem' ),
					'default'  => __( 'Reward Points to Enter' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_redeem_field_placeholder',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Redeeming Field Submit Button Caption' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Label which will be displayed in Submit Button' , 'rewardsystem' ),
					'id'       => 'rs_redeem_field_submit_button_caption',
					'std'      => __( 'Apply Reward Points' , 'rewardsystem' ),
					'default'  => __( 'Apply Reward Points' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_redeem_field_submit_button_caption',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Coupon Label Settings' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed in Cart Subtotal' , 'rewardsystem' ),
					'id'       => 'rs_coupon_label_message',
					'std'      => __( 'Redeemed Points Value' , 'rewardsystem' ),
					'default'  => __( 'Redeemed Points Value' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_coupon_label_message',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Extra Class Name for Redeeming Field Submit Button' , 'rewardsystem' ),
					'desc'     => __( 'Add Extra Class Name to the Cart Apply Reward Points Button, Don\'t Enter dot(.) before Class Name' , 'rewardsystem' ),
					'id'       => 'rs_extra_class_name_apply_reward_points',
					'std'      => '',
					'default'  => '',
					'type'     => 'text',
					'newids'   => 'rs_extra_class_name_apply_reward_points',
					'desc_tip' => true,
				),
				array( 'type' => 'sectionend', 'id' => '_rs_restriction_in_cart_and_checkout' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Redeeming Restriction' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_cart_remaining_setting',
				),
				array(
					'name'     => __( 'Redeeming/WooCommerce Coupon Field display on cart & checkout' , 'rewardsystem' ),
					'id'       => 'rs_show_hide_redeem_field',
					'css'      => '',
					'std'      => '1',
					'default'  => '1',
					'type'     => 'select',
					'newids'   => 'rs_show_hide_redeem_field',
					'options'  => array(
						'1' => __( 'Display Both' , 'rewardsystem' ),
						'2' => __( 'Hide WooCommerce Coupon Field' , 'rewardsystem' ),
						'3' => __( 'Hide Redeeming Points Field' , 'rewardsystem' ),
						'4' => __( 'Hide Both' , 'rewardsystem' ),
						'5' => __( 'Hide one when coupon/point is used' , 'rewardsystem' ),
					),
					'desc_tip' => true,
				),
				array(
					'name'    => __( 'Redeemed Points is applied on' , 'rewardsystem' ),
					'id'      => 'rs_apply_redeem_basedon_cart_or_product_total',
					'newids'  => 'rs_apply_redeem_basedon_cart_or_product_total',
					'class'   => 'rs_apply_redeem_basedon_cart_or_product_total srp-rp-cart-level-redeem',
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Cart Subtotal' , 'rewardsystem' ),
						'2' => __( 'Product Total' , 'rewardsystem' ),
					),
				),
				array(
					'name'     => __( 'Redeeming Field Selection' , 'rewardsystem' ),
					'id'       => 'rs_hide_redeeming_field',
					'class'    => 'rs_hide_redeeming_field',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_hide_redeeming_field',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
					'desc_tip' => true,
					'desc'     => __( 'This option can be used to controls the redeeming field display when redeeming is restricted to specific products/categories.' , 'rewardsystem' ),
				),
				array(
					'name'    => __( 'Enable to Restrict Redeeming for Sale Price Product(s)' , 'rewardsystem' ),
					'id'      => 'rs_restrict_sale_price_for_redeeming',
					'class'   => 'rs_restrict_sale_price_for_redeeming',
					'std'     => 'no',
					'default' => 'no',
					'type'    => 'checkbox',
					'newids'  => 'rs_restrict_sale_price_for_redeeming',
				),
				array(
					'name'    => __( 'Error Message' , 'rewardsystem' ),
					'id'      => 'rs_redeeming_message_restrict_for_sale_price_product',
					'std'     => __( 'Sorry, redeeming is not applicable for sale price product(s)' , 'rewardsystem' ),
					'default' => __( 'Sorry, redeeming is not applicable for sale price product(s)' , 'rewardsystem' ),
					'type'    => 'textarea',
					'newids'  => 'rs_redeeming_message_restrict_for_sale_price_product',
				),
				array(
					'name'   => __( 'Enable Redeeming for Selected Products' , 'rewardsystem' ),
					'id'     => 'rs_enable_redeem_for_selected_products',
					'type'   => 'checkbox',
					'newids' => 'rs_enable_redeem_for_selected_products',
					'class'  => 'srp-rp-cart-level-redeem',
				),
				array(
					'type' => 'include_product_selection',
				),
				array(
					'name'    => __( 'Products excluded from Redeeming' , 'rewardsystem' ),
					'id'      => 'rs_exclude_products_for_redeeming',
					'class'   => 'rs_exclude_products_for_redeeming srp-rp-cart-level-redeem',
					'std'     => 'no',
					'default' => 'no',
					'type'    => 'checkbox',
					'newids'  => 'rs_exclude_products_for_redeeming',
				),
				array(
					'type' => 'exclude_product_selection',
				),
				array(
					'name'    => __( 'Enable Redeeming for Selected Category' , 'rewardsystem' ),
					'id'      => 'rs_enable_redeem_for_selected_category',
					'class'   => 'rs_enable_redeem_for_selected_category srp-rp-cart-level-redeem',
					'std'     => '',
					'default' => '',
					'type'    => 'checkbox',
					'newids'  => 'rs_enable_redeem_for_selected_category',
				),
				array(
					'name'     => __( 'Categories allowed for Redeeming' , 'rewardsystem' ),
					'desc'     => __( 'Select Category to enable redeeming' , 'rewardsystem' ),
					'id'       => 'rs_select_category_to_enable_redeeming',
					'class'    => 'rs_select_category_to_enable_redeeming srp-rp-cart-level-redeem',
					'css'      => 'min-width:350px',
					'std'      => '',
					'default'  => '',
					'type'     => 'multiselect',
					'newids'   => 'rs_select_category_to_enable_redeeming',
					'options'  => $categorylist,
					'desc_tip' => true,
				),
				array(
					'name'    => __( 'Exclude Category for Redeeming' , 'rewardsystem' ),
					'id'      => 'rs_exclude_category_for_redeeming',
					'std'     => '',
					'default' => '',
					'type'    => 'checkbox',
					'newids'  => 'rs_exclude_category_for_redeeming',
				),
				array(
					'name'     => __( 'Categories excluded from Redeeming' , 'rewardsystem' ),
					'desc'     => __( 'Select Category to enable redeeming' , 'rewardsystem' ),
					'id'       => 'rs_exclude_category_to_enable_redeeming',
					'class'    => 'rs_exclude_category_to_enable_redeeming',
					'css'      => 'min-width:350px',
					'std'      => '',
					'default'  => '',
					'type'     => 'multiselect',
					'newids'   => 'rs_exclude_category_to_enable_redeeming',
					'options'  => $categorylist,
					'desc_tip' => true,
				),
				array(
					'name'    => __('Hide Available Balance Points', 'rewardsystem'),
					'desc'    => __( 'By enabling this checkbox, available reward points message will be hidden in the cart and checkout page when points redeemed on any one of the pages' , 'rewardsystem' ),
					'id'      => 'rs_available_points_display',
					'std'     => '',
					'default' => '',
					'type'    => 'checkbox',
					'newids'  => 'rs_available_points_display',
				),
				array(
					'name'    => __( 'Restrict redeeming points applying on Tax Cost', 'rewardsystem' ),
					'desc'    => __( 'By enabling this checkbox, points redeeming in cart & checkout will not apply on tax cost.', 'rewardsystem' ),
					'id'      => 'rs_enable_redeem_point_without_incl_tax',
					'type'    => 'checkbox',
					'std'     => 'no',
					'default' => 'no',
					'newids'  => 'rs_enable_redeem_point_without_incl_tax',
				),
				array(
					'name'     => __( 'Maximum Redeeming Threshold Percentage for Auto Redeeming', 'rewardsystem' ),
					'desc'     => __( 'Enter the Percentage of the cart total that has to be Auto Redeemed', 'rewardsystem' ),
					'id'       => 'rs_percentage_cart_total_auto_redeem',
					'std'      => '100 ',
					'default'  => '100',
					'type'     => 'text',
					'newids'   => 'rs_percentage_cart_total_auto_redeem',
					'desc_tip' => true,
				),
				array(
					'name'    => __( 'Maximum Redeeming Threshold Value (Discount) Type', 'rewardsystem' ),
					'id'      => 'rs_max_redeem_discount',
					'std'     => '',
					'default' => '',
					'newids'  => 'rs_max_redeem_discount',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'By Fixed Value' , 'rewardsystem' ),
						'2' => __( 'By Percentage of Cart Total' , 'rewardsystem' ),
					),
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'     => __( 'Maximum Redeeming Threshold Value (Discount) for Order in ' . get_woocommerce_currency_symbol() , 'rewardsystem' ),
					'desc'     => __( 'Enter a Fixed or Decimal Number greater than 0' , 'rewardsystem' ),
					'id'       => 'rs_fixed_max_redeem_discount',
					'std'      => '',
					'default'  => '',
					'type'     => 'text',
					'newids'   => 'rs_fixed_max_redeem_discount',
					'desc_tip' => true,
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'     => __( 'Maximum Redeeming Threshold Value (Discount) for Order in Percentage %' , 'rewardsystem' ),
					'desc'     => __( 'Enter a Fixed or Decimal Number greater than 0' , 'rewardsystem' ),
					'id'       => 'rs_percent_max_redeem_discount',
					'std'      => '',
					'default'  => '',
					'type'     => 'text',
					'newids'   => 'rs_percent_max_redeem_discount',
					'desc_tip' => true,
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'     => __( 'Minimum Points required for Redeeming for the First Time' , 'rewardsystem' ),
					'desc'     => __( 'Enter Minimum Points to be Earned for Redeeming First Time in Cart/Checkout' , 'rewardsystem' ),
					'id'       => 'rs_first_time_minimum_user_points',
					'std'      => '',
					'default'  => '',
					'type'     => 'text',
					'newids'   => 'rs_first_time_minimum_user_points',
					'desc_tip' => true,
				),
				array(
					'name'    => __( 'Show/Hide First Time Redeeming Minimum Points Required Warning Message' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_first_redeem_error_message',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_first_redeem_error_message',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'     => __( 'Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when the user doesn\'t have enough points for first time redeeming' , 'rewardsystem' ),
					'id'       => 'rs_min_points_first_redeem_error_message',
					'std'      => __( 'You need Minimum of [firstredeempoints] Points when redeeming for the First time' , 'rewardsystem' ),
					'default'  => __( 'You need Minimum of [firstredeempoints] Points when redeeming for the First time' , 'rewardsystem' ),
					'type'     => 'textarea',
					'newids'   => 'rs_min_points_first_redeem_error_message',
					'desc_tip' => true,
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'    => __( 'Minimum Points required for Redeeming after First Redeeming' , 'rewardsystem' ),
					'id'      => 'rs_minimum_user_points_to_redeem',
					'std'     => '',
					'default' => '',
					'type'    => 'text',
					'newids'  => 'rs_minimum_user_points_to_redeem',
				),
				array(
					'name'    => __( 'Show/Hide Minimum Points required for Redeeming after First Redeeming' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_after_first_redeem_error_message',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_after_first_redeem_error_message',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'     => __( 'Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when the Current User doesn\'t have minimum points for Redeeming ' , 'rewardsystem' ),
					'id'       => 'rs_min_points_after_first_error',
					'std'      => __( 'You need minimum of [points_after_first_redeem] Points for Redeeming' , 'rewardsystem' ),
					'default'  => __( 'You need minimum of [points_after_first_redeem] Points for Redeeming' , 'rewardsystem' ),
					'type'     => 'textarea',
					'newids'   => 'rs_min_points_after_first_error',
					'desc_tip' => true,
				),
				array(
					'name'    => __( 'Minimum Points to be entered for Redeeming' , 'rewardsystem' ),
					'id'      => 'rs_minimum_redeeming_points',
					'std'     => '',
					'default' => '',
					'type'    => 'text',
					'newids'  => 'rs_minimum_redeeming_points',
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'    => __( 'Maximum Points above which points cannot be Redeemed' , 'rewardsystem' ),
					'id'      => 'rs_maximum_redeeming_points',
					'std'     => '',
					'default' => '',
					'type'    => 'text',
					'newids'  => 'rs_maximum_redeeming_points',
					'class'   => 'srp-rp-cart-level-redeem',
				),
								array(
					'name'    => __( 'Maximum Redeeming Restriction Per Day' , 'rewardsystem' ),
					'id'      => 'rs_maximum_redeeming_per_day_restriction_enabled',
					'class'   => 'rs_maximum_redeeming_per_day_restriction_enabled srp-rp-cart-level-redeem',
					'newids'  => 'rs_maximum_redeeming_per_day_restriction_enabled',
					'type'    => 'checkbox',
					'std'     => 'no',
					'default' => 'no',             
				),
				array(
					'name'    => __( 'Maximum Points can be Redeemed Per Day' , 'rewardsystem' ),
					'id'      => 'rs_maximum_redeeming_per_day_restriction',
					'class'   => 'rs_maximum_redeeming_per_day_restriction srp-rp-cart-level-redeem',
					'newids'  => 'rs_maximum_redeeming_per_day_restriction',
					'std'     => '', 
					'default' => '',
					'type'    => 'text',
				),
				array(
					'name'     => __( 'Error Message' , 'rewardsystem' ),
					'id'       => 'rs_maximum_redeeming_per_day_error',
					'class'    => 'rs_maximum_redeeming_per_day_error srp-rp-cart-level-redeem',
					'newids'   => 'rs_maximum_redeeming_per_day_error',
					'std'      => 'You are allowed to redeem a maximum of [max_points] points per day.',
					'default'  => 'You are allowed to redeem a maximum of [max_points] points per day.',
					'type'     => 'textarea',
				),
				array(
					'name'    => __( 'Minimum Cart Total to Redeem Point(s)' , 'rewardsystem' ),
					'id'      => 'rs_minimum_cart_total_points',
					'std'     => '',
					'default' => '',
					'type'    => 'text',
					'newids'  => 'rs_minimum_cart_total_points',
				),
				array(
					'name'    => __( 'Show/Hide Minimum Cart Total to Redeem Point(s)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_minimum_cart_total_error_message',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_minimum_cart_total_error_message',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'     => __( 'Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when current Cart total is less than minimum Cart Total for Redeeming' , 'rewardsystem' ),
					'id'       => 'rs_min_cart_total_redeem_error',
					'std'      => __( 'You need minimum cart Total of [currencysymbol][carttotal] in order to Redeem' , 'rewardsystem' ),
					'default'  => __( 'You need minimum cart Total of [currencysymbol][carttotal] in order to Redeem' , 'rewardsystem' ),
					'type'     => 'textarea',
					'newids'   => 'rs_min_cart_total_redeem_error',
					'desc_tip' => true,
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'    => __( 'Maximum Cart Total to Redeem Point(s)' , 'rewardsystem' ),
					'id'      => 'rs_maximum_cart_total_points',
					'std'     => '',
					'default' => '',
					'type'    => 'text',
					'newids'  => 'rs_maximum_cart_total_points',
				),
				array(
					'name'    => __( 'Show/Hide Maximum Cart Total to Redeem Point(s)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_maximum_cart_total_error_message',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_maximum_cart_total_error_message',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'     => __( 'Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when current Cart total is less than Maximum Cart Total for Redeeming' , 'rewardsystem' ),
					'id'       => 'rs_max_cart_total_redeem_error',
					'std'      => __( 'You Cannot Redeem Points Because you Reach the Maximum Cart total [currencysymbol][carttotal]' , 'rewardsystem' ),
					'default'  => __( 'You Cannot Redeem Points Because you Reach the Maximum Cart total [currencysymbol][carttotal]' , 'rewardsystem' ),
					'type'     => 'textarea',
					'newids'   => 'rs_max_cart_total_redeem_error',
					'desc_tip' => true,
					'class'   => 'srp-rp-cart-level-redeem',
				),
				array(
					'name'    => __( 'Redeeming Restriction based on Available Points' , 'rewardsystem' ),
					'id'      => 'rs_minimum_available_points_restriction_is_enabled',
					'class'   => 'rs_minimum_available_points_restriction_is_enabled',
					'newids'  => 'rs_minimum_available_points_restriction_is_enabled',
					'std'     => 0!=get_option('rs_available_points_based_redeem', 0) ? 'yes' :'no',
					'default' => 0!=get_option('rs_available_points_based_redeem', 0) ? 'yes' :'no',
					'type'    => 'checkbox',
				),
				array(
					'name'    => __( 'Minimum Available Points required for' , 'rewardsystem' ),
					'id'      => 'rs_minimum_available_points_based_on',
					'class'   => 'rs_minimum_available_points_based_on rs_hide_minimum_available_point_restriction_fields',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_minimum_available_points_based_on',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'All User(s)' , 'rewardsystem' ),
						'2' => __( 'User Role(s)' , 'rewardsystem' ),
					),
				),
				array(
					'name'   => __( 'Minimum Available Points required to Redeem' , 'rewardsystem' ),
					'id'     => 'rs_available_points_based_redeem',
					'class'  => 'rs_available_points_based_redeem rs_hide_minimum_available_point_restriction_fields',
					'type'   => 'number',
					'newids' => 'rs_available_points_based_redeem',
				),
				array(
					'type' => 'user_role_based_minimum_available_point_restriction',
				),
				array(
					'name'     => __( 'Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the message which will be displayed when a user enters the points value less than the value configured in this field' , 'rewardsystem' ),
					'id'       => 'rs_available_points_redeem_error',
					'class'    => 'rs_available_points_redeem_error rs_hide_minimum_available_point_restriction_fields',
					'std'      => __( 'You are eligible to redeem your points only when you have [available_points] Points in your account' , 'rewardsystem' ),
					'default'  => __( 'You are eligible to redeem your points only when you have [available_points] Points in your account' , 'rewardsystem' ),
					'type'     => 'textarea',
					'newids'   => 'rs_available_points_redeem_error',
					'desc_tip' => true,
				),
			array(
					'name'     => __( 'Restrict Redeem Points when Selected Payment Gateway is used', 'rewardsystem' ),
					'desc'     => __( 'Enabling this option will restrict redeem points when Selected Payment gateway is used on order', 'rewardsystem' ),
					'id'       => 'rs_select_payment_gateway_for_restrict_redeem_points',
					'class'    => 'rs_select_payment_gateway_for_restrict_redeem_points',
					'std'      => '',
					'default'  => '',
					'type'     => 'multiselect',
					'options'  => $available_payment_gateways,
					'newids'   => 'rs_select_payment_gateway_for_restrict_redeem_points',
					'desc_tip' => true,
				),
				array( 'type' => 'sectionend', 'id' => '_rs_cart_remaining_setting' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
										/* translators: %s: - section title */
					'name' => esc_html__( sprintf('%s', $section_title) , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_err_msg_setting_in_edit_order',
				),
				array(
					'name'   => __( 'Display Redeemed Points' , 'rewardsystem' ),
					'desc'   => __( 'Enable Message for Redeem Points' , 'rewardsystem' ),
					'id'     => 'rs_enable_msg_for_redeem_points',
					'newids' => 'rs_enable_msg_for_redeem_points',
					'class'  => 'rs_enable_msg_for_redeem_points',
					'type'   => 'checkbox',
				),
				array(
					'name'     => __( 'Message to Redeemed Points' , 'rewardsystem' ),
					'desc'     => __( 'Message to Redeemed Points' , 'rewardsystem' ),
					'id'       => 'rs_msg_for_redeem_points',
					'newids'   => 'rs_msg_for_redeem_points',
					'class'    => 'rs_msg_for_redeem_points',
					'std'      => __( 'Points Redeemed in this Order [redeempoints]' , 'rewardsystem' ),
					'default'  => __( 'Points Redeemed in this Order [redeempoints]' , 'rewardsystem' ),
					'type'     => 'textarea',
					'desc_tip' => true,
				),
				array( 'type' => 'sectionend', 'id' => '_rs_err_msg_setting_in_edit_order' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Error Message Settings for Redeeming Field' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_err_msg_setting',
				),
				array(
					'name'     => __( 'Error Message to display when User enters less than Minimum Points[Default Type]' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when Entered Points is less than Minimum Redeeming Points which is set in this Page' , 'rewardsystem' ),
					'id'       => 'rs_minimum_redeem_point_error_message',
					'std'      => __( 'Please Enter Points more than [rsminimumpoints]' , 'rewardsystem' ),
					'default'  => __( 'Please Enter Points more than [rsminimumpoints]' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_minimum_redeem_point_error_message',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Error Message to display when User enters more than Maximum Points[Default Type]' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when Entered Points is more than Maximum Redeeming Points which is set in this Page' , 'rewardsystem' ),
					'id'       => 'rs_maximum_redeem_point_error_message',
					'std'      => __( 'Please Enter Points less than [rsmaximumpoints]' , 'rewardsystem' ),
					'default'  => __( 'Please Enter Points less than [rsmaximumpoints]' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_maximum_redeem_point_error_message',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Error Message to display when User enters less than the Minimum Points  or more than Maximum Points[Default Type]' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when Maximum and Minimum Redeeming Points are Equal which is set in this Page' , 'rewardsystem' ),
					'id'       => 'rs_minimum_and_maximum_redeem_point_error_message',
					'std'      => __( 'Please Enter [rsequalpoints] Points' , 'rewardsystem' ),
					'default'  => __( 'Please Enter [rsequalpoints] Points' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_minimum_and_maximum_redeem_point_error_message',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Error Message to display when User enters less than Minimum Points[Button Type]' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when Entered Points is less than Minimum Redeeming Points which is set in this Page' , 'rewardsystem' ),
					'id'       => 'rs_minimum_redeem_point_error_message_for_button_type',
					'std'      => __( 'You cannot redeem because the current points to be redeemed is less than [rsminimumpoints] Points' , 'rewardsystem' ),
					'default'  => __( 'You cannot redeem because the current points to be redeemed is less than [rsminimumpoints] Points' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_minimum_redeem_point_error_message_for_button_type',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Error Message to display when User enters more than Maximum Points[Button Type]' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when Entered Points is more than Maximum Redeeming Points which is set in this Page' , 'rewardsystem' ),
					'id'       => 'rs_maximum_redeem_point_error_message_for_button_type',
					'std'      => __( 'You cannot redeem because the current points to be redeemed is more than [rsmaximumpoints] points' , 'rewardsystem' ),
					'default'  => __( 'You cannot redeem because the current points to be redeemed is more than [rsmaximumpoints] points' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_maximum_redeem_point_error_message_for_button_type',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Error Message to display when User enters less than the Minimum Points  or more than Maximum Points[Button Type]' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when Maximum and Minimum Redeeming Points are Equal which is set in this Page' , 'rewardsystem' ),
					'id'       => 'rs_minimum_and_maximum_redeem_point_error_message_for_buttontype',
					'std'      => __( 'You cannot redeem because the points to be redeemed is not equal to [rsequalpoints] Points' , 'rewardsystem' ),
					'default'  => __( 'You cannot redeem because the points to be redeemed is not equal to [rsequalpoints] Points' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_minimum_and_maximum_redeem_point_error_message_for_buttontype',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Redeeming Field Empty Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when Redeem Field has Empty Value' , 'rewardsystem' ),
					'id'       => 'rs_redeem_empty_error_message',
					'std'      => __( 'No Reward Points Entered' , 'rewardsystem' ),
					'default'  => __( 'No Reward Points Entered' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_redeem_empty_error_message',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Unwanted Characters in Redeeming Field Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when redeeming field value contain characters' , 'rewardsystem' ),
					'id'       => 'rs_redeem_character_error_message',
					'std'      => __( 'Please Enter Only Numbers' , 'rewardsystem' ),
					'default'  => __( 'Please Enter Only Numbers' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_redeem_character_error_message',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Insufficient Points for Redeeming Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when Entered Reward Points is more than Earned Reward Points' , 'rewardsystem' ),
					'id'       => 'rs_redeem_max_error_message',
					'std'      => __( 'Reward Points you entered is more than Your Earned Reward Points' , 'rewardsystem' ),
					'default'  => __( 'Reward Points you entered is more than Your Earned Reward Points' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_redeem_max_error_message',
					'desc_tip' => true,
				),
				array(
					'name'    => __( 'Current User Points is Empty Error Message' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_points_empty_error_message',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_points_empty_error_message',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'     => __( 'Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when the Current User Points is Empty' , 'rewardsystem' ),
					'id'       => 'rs_current_points_empty_error_message',
					'std'      => __( 'You don\'t have Points for Redeeming' , 'rewardsystem' ),
					'default'  => __( 'You don\'t have Points for Redeeming' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_current_points_empty_error_message',
					'desc_tip' => true,
				),
				array(
					'name'    => __( 'Error Message to display when Auto Redeeming/WooCommerce Coupon is not applicable to use in the cart' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_auto_redeem_not_applicable',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_auto_redeem_not_applicable',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'     => __( 'Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when Auto Redeeming/WooCommerce Coupon is not applicable to use in the cart' , 'rewardsystem' ),
					'id'       => 'rs_auto_redeem_not_applicable_error_message',
					'std'      => __( 'Auto Redeem is not applicable to your cart contents.' , 'rewardsystem' ),
					'default'  => __( 'Auto Redeem is not applicable to your cart contents.' , 'rewardsystem' ),
					'type'     => 'text',
					'newids'   => 'rs_auto_redeem_not_applicable_error_message',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Error Message' , 'rewardsystem' ),
					'desc'     => __( 'Enter the Message which will be displayed when Auto Redeeming/WooCommerce Coupon is not applicable to use in the cart' , 'rewardsystem' ),
					'id'       => 'rs_redeeming_gateway_restriction_error',
					'std'      => __( 'Redeeming is not applicable to the payment gateway you have selected. Hence, the discount applied through points has been removed.' , 'rewardsystem' ),
					'default'  => __( 'Redeeming is not applicable to the payment gateway you have selected. Hence, the discount applied through points has been removed.' , 'rewardsystem' ),
					'type'     => 'textarea',
					'newids'   => 'rs_redeeming_gateway_restriction_error',
					'desc_tip' => true,
				),
				array( 'type' => 'sectionend', 'id' => '_rs_err_msg_setting' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Shortcodes used in Redeeming Module' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => 'rs_shortcodes_in_checkout',
				),
				array(
					'type' => 'title',
					'desc' => __('<b>[cartredeempoints]</b> - To display points can redeem based on cart total amount<br><br>'
					. '<b>[currencysymbol]</b> - To display currency symbol<br><br>'
					. '<b>[pointsvalue]</b> - To display currency value equivalent of redeeming points<br><br>'
					. '<b>[productname]</b> - To display product name<br><br>'
					. '<b>[firstredeempoints] </b> - To display points required for first time redeeming<br><br>'
					. '<b>[points_after_first_redeem]</b> - To display points required after first redeeming<br><br>'
					. '<b>[rsminimumpoints]</b> - To display minimum points required to redeem<br><br>'
					. '<b>[rsmaximumpoints]</b> - To display maximum points required to redeem<br><br>'
					. '<b>[rsequalpoints]</b> - To display exact points to redeem<br><br>'
					. '<b>[carttotal]</b> - To display cart total value<br><br>' , 'rewardsystem' ),
				),
								array(
					'type' => 'title',
					'desc' => __('<b>Note:</b> <br/>We recommend don’t use the above shortcodes anywhere on your site. It will give the value only on the place where we have predefined.<br/> Please check by using the shortcodes available in the <b>Shortcodes </b> tab which will give the value globally.', 'rewardsystem'),
					'id'   => 'rs_shortcode_note_redeeming',
				),
				array( 'type' => 'sectionend', 'id' => 'rs_shortcodes_in_checkout' ),
				array(
					'type' => 'rs_wrapper_end',
				),
					) ) ;
		}

		/**
		 * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
		 */
		public static function reward_system_register_admin_settings() {
			woocommerce_admin_fields( self::reward_system_admin_fields() ) ;
		}

		/**
		 * Update the Settings on Save Changes may happen in SUMO Reward Points
		 */
		public static function reward_system_update_settings() {
			woocommerce_update_options( self::reward_system_admin_fields() ) ;
			if ( isset( $_REQUEST[ 'rs_select_products_to_enable_redeeming' ] ) ) {
				update_option( 'rs_select_products_to_enable_redeeming' , wc_clean(wp_unslash($_REQUEST[ 'rs_select_products_to_enable_redeeming' ] ))) ;
			} else {
				update_option( 'rs_select_products_to_enable_redeeming' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_exclude_products_to_enable_redeeming' ] ) ) {
				update_option( 'rs_exclude_products_to_enable_redeeming' , wc_clean(wp_unslash($_REQUEST[ 'rs_exclude_products_to_enable_redeeming' ] ))) ;
			} else {
				update_option( 'rs_exclude_products_to_enable_redeeming' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_redeeming_module_checkbox' ] ) ) {
				update_option( 'rs_redeeming_activated' , wc_clean(wp_unslash($_REQUEST[ 'rs_redeeming_module_checkbox' ] ) ));
			} else {
				update_option( 'rs_redeeming_activated' , 'no' ) ;
			}
			if ( isset( $_REQUEST[ 'rewards_dynamic_rule_for_redeem' ] ) ) {
				update_option( 'rewards_dynamic_rule_for_redeem' , wc_clean(wp_unslash($_REQUEST[ 'rewards_dynamic_rule_for_redeem' ] ))) ;
			} else {
				update_option( 'rewards_dynamic_rule_for_redeem' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rewards_dynamic_rule_purchase_history_redeem' ] ) ) {
				update_option( 'rewards_dynamic_rule_purchase_history_redeem' , wc_clean(wp_unslash($_REQUEST[ 'rewards_dynamic_rule_purchase_history_redeem' ] ))) ;
			} else {
				update_option( 'rewards_dynamic_rule_purchase_history_redeem' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_include_products_for_product_level_redeem' ] ) ) {
				update_option( 'rs_include_products_for_product_level_redeem' , wc_clean(wp_unslash($_REQUEST[ 'rs_include_products_for_product_level_redeem' ] ))) ;
			} else {
				update_option( 'rs_include_products_for_product_level_redeem' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_exclude_products_for_product_level_redeem' ] ) ) {
				update_option( 'rs_exclude_products_for_product_level_redeem' , wc_clean(wp_unslash($_REQUEST[ 'rs_exclude_products_for_product_level_redeem' ] ) ));
			} else {
				update_option( 'rs_exclude_products_for_product_level_redeem' , '' ) ;
			}

			global $wp_roles ;
			if ( is_object( $wp_roles ) ) {
				foreach ( $wp_roles->role_names as $key => $value ) {
					if ( isset( $_REQUEST[ 'rs_minimum_available_points_to_redeem_for_' . $key ] ) ) {
						update_option( 'rs_minimum_available_points_to_redeem_for_' . $key , wc_clean( wp_unslash($_REQUEST[ 'rs_minimum_available_points_to_redeem_for_' . $key ] ))) ;
					} else {
						update_option( 'rs_minimum_available_points_to_redeem_for_' . $key , '' ) ;
					}
				}
			}
		}

		/**
		 * Initialize the Default Settings by looping this function
		 */
		public static function set_default_value() {
			foreach ( self::reward_system_admin_fields() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		public static function reset_redeeming_module() {
			$settings = self::reward_system_admin_fields() ;
			RSTabManagement::reset_settings( $settings ) ;
			update_option( 'rs_redeem_point' , '1' ) ;
			update_option( 'rs_redeem_point_value' , '1' ) ;
		}

		public static function enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_redeeming_activated' ) , 'rs_redeeming_module_checkbox' , 'rs_redeeming_activated' ) ;
		}

		public static function setting_for_hide_redeem_field_when_sumo_discount_is_active( $settings ) {
			$updated_settings = array() ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && ( '_rs_cart_remaining_setting' === $section[ 'id' ] ) &&
						isset( $section[ 'type' ] ) && ( 'sectionend' === $section[ 'type' ] ) ) {
					$updated_settings[] = array(
						'name'     => __( 'Show Redeeming Field' , 'rewardsystem' ),
						'id'       => 'rs_show_redeeming_field',
						'std'      => '1',
						'default'  => '1',
						'type'     => 'select',
						'newids'   => 'rs_show_redeeming_field',
						'options'  => array(
							'1' => __( 'Always' , 'rewardsystem' ),
							'2' => __( 'When Price is not altered through SUMO Discounts Plugin' , 'rewardsystem' ),
						),
						'desc_tip' => true,
							) ;
				}
				$updated_settings[] = $section ;
			}
			return $updated_settings ;
		}

		/*
		 * Function to select products to exclude
		 */

		public static function rs_select_product_to_exclude() {
			$field_id    = 'rs_exclude_products_to_enable_redeeming' ;
			$field_label = 'Products excluded from Redeeming' ;
			$getproducts = get_option( 'rs_exclude_products_to_enable_redeeming' ) ;
			echo do_shortcode(rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) );
		}

		/*
		 * Function to select products to include
		 */

		public static function rs_select_product_to_include() {
			$field_id    = 'rs_select_products_to_enable_redeeming' ;
			$field_label = 'Products allowed for Redeeming' ;
			$getproducts = get_option( 'rs_select_products_to_enable_redeeming' ) ;
			echo do_shortcode(rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) );
		}

		/*
		 * User role based minimum available point restriction. 
		 */

		public static function user_role_based_minimum_available_point_restriction() {

			global $wp_roles ;
			if ( ! is_object( $wp_roles ) ) {
				return ;
			}

			ob_start() ;
			foreach ( $wp_roles->role_names as $role_key => $role_value ) {

				$field_id = 'rs_minimum_available_points_to_redeem_for_' . sanitize_text_field($role_key) ;
				?>
				<tr>
					<th class="titledesc" scope="row">
						<label><?php esc_html_e( 'Minimum Available Points required to redeem for ' . sanitize_text_field($role_value) , 'rewardsystem' ) ; ?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="number"
							   class="rs_minimum_available_points_to_redeem_value rs_hide_minimum_available_point_restriction_fields"  
							   id="<?php echo esc_attr( $field_id ) ; ?>" 
							   name="<?php echo esc_attr( $field_id ) ; ?>" 
							   value="<?php echo esc_attr( get_option($field_id) ) ; ?>"  >
					</td>
				</tr>
				<?php
			}

			$content = ob_get_contents() ;
			ob_end_flush() ;

			return $content ;
		}

		public static function rs_select_products_to_update() {
			$field_id    = 'rs_select_particular_products' ;
			$field_label = esc_html__('Select Particular Products' , 'rewardsystem');
			$getproducts = get_option( 'rs_select_particular_products' ) ;
			echo do_shortcode(rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) );
		}

		public static function include_products_for_product_level_redeem() {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="rs_include_products_for_product_level_redeem"><?php esc_html_e('Select the Products to Include', 'rewardsystem'); ?></label>
				</th>
				<td class="forminp forminp-select">
				<?php
					$args = array(
						'class'             => 'srp-rp-product-include-products srp-rp-bulk-update-opt srp-rp-product-selection',
						'id'                => 'rs_include_products_for_product_level_redeem',
						'list_type'         => 'products',
						'action'            => 'srp_product_search',
						'placeholder'       => 'Search for a Product...',
						'multiple'          => true,
						'allow_clear'       => true,
						'options'           => get_option('rs_include_products_for_product_level_redeem', true),
					) ;
					srp_select2_html( $args) ; 
					?>
				</td>
			</tr> 
			<?php
		}

		public static function exclude_products_for_product_level_redeem() {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="rs_exclude_products_for_product_level_redeem"><?php esc_html_e('Select the Products to Exclude', 'rewardsystem'); ?></label>
				</th>
				<td class="forminp forminp-select">
				<?php
					$args = array(
						'class'             => 'srp-rp-product-exclude-products srp-rp-bulk-update-opt srp-rp-product-selection',
						'id'                => 'rs_exclude_products_for_product_level_redeem',
						'list_type'         => 'products',
						'action'            => 'srp_product_search',
						'placeholder'       => 'Search for a Product...',
						'multiple'          => true,
						'allow_clear'       => true,
						'options'           => get_option('rs_exclude_products_for_product_level_redeem', true),
					) ;
					srp_select2_html( $args) ; 
					?>
				</td>
			</tr> 
			<?php
		}

		public static function rs_save_button_for_update() {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row"></th>
				<td class="forminp forminp-select">
					<input type="submit" class="rs_sumo_reward_button button-primary srp-rp-bulk-update-opt" value="<?php esc_html_e('Save and Update', 'rewardsystem'); ?>"/>
				</td>
			</tr>
			<?php
		}
	}

	RSRedeemingModule::init() ;
}
