<?php

/*
 * Bonus Reward Points Module Setting
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
if (!class_exists('RSBonusPointsModule')) {

	class RSBonusPointsModule {
		/*
		 * Init.
		 */

		public static function init() {

			add_action('woocommerce_rs_settings_tabs_fpbonuspoints', array( __CLASS__, 'reward_system_register_admin_settings' )); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

			add_action('woocommerce_update_options_fprsmodules_fpbonuspoints', array( __CLASS__, 'reward_system_update_settings' )); // call the woocommerce_update_options_{slugname} to update the reward system                               

			add_action('woocommerce_admin_field_rs_enable_disable_bonuspoints_module', array( __CLASS__, 'render_enable_module' ));

			add_action('rs_display_save_button_fpbonuspoints', array( 'RSTabManagement', 'rs_display_save_button' ));

			add_action('rs_display_reset_button_fpbonuspoints', array( 'RSTabManagement', 'rs_display_reset_button' ));

			add_action('woocommerce_admin_field_rs_bonus_points_without_repeat_rule_for_orders', array( __CLASS__, 'render_without_repeat_rule_for_orders' ));

			add_action('woocommerce_admin_field_rs_bonus_points_log', array( __CLASS__, 'render_bonus_points_log' ));

			add_action('fp_action_to_reset_module_settings_fpbonuspoints', array( __CLASS__, 'reset_action_tab' ));

			add_action('admin_footer', array( __CLASS__, 'preview_template' ));
		}

		/*
		 * Function label settings for bonus reward points module.
		 */

		public static function reward_system_admin_fields() {
						/**
						 * Hook:woocommerce_fpbonuspoints_settings.
						 * 
						 * @since 1.0
						 */
			return apply_filters('woocommerce_fpbonuspoints_settings', array(
				array(
					'type' => 'rs_modulecheck_start',
				),
				array(
					'name' => __('Bonus Reward Points Module', 'rewardsystem'),
					'type' => 'title',
					'id' => '_rs_activate_bonuspoints_module',
				),
				array(
					'type' => 'rs_enable_disable_bonuspoints_module',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_activate_bonuspoints_module' ),
				array(
					'type' => 'rs_modulecheck_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __('Bonus Reward Points for Orders', 'rewardsystem'),
					'type' => 'title',
					'id' => 'rs_bonus_points_for_orders_section',
				),
				array(
					'name' => __('Bonus Reward Points for Orders', 'rewardsystem'),
					'type' => 'checkbox',
					'id' => 'rs_enable_bonus_point_for_orders',
					'newids' => 'rs_enable_bonus_point_for_orders',
					'class' => 'rs_enable_bonus_point_for_orders',
					'std' => 'no',
					'default' => 'no',
					'desc' => __('By enabling this checkbox, you can award bonus points to your users based on the number of orders placed on your site.<br/><span class="rs-bonus-setting-desc"><b>Note:</b> Bonus Points will be awarded only for the orders which are in <b>Completed</b> status. Bonus Points awarded will not be revised back from the account when the order status changed from <b>Completed</b> to <b>Cancelled/Refunded/Failed</b>.</span>', 'rewardsystem'),
				),
				array(
					'name' => __('Rules', 'rewardsystem'),
					'type' => 'select',
					'id' => 'rs_bonus_points_rules_for_orders_type',
					'newids' => 'rs_bonus_points_rules_for_orders_type',
					'class' => 'rs_bonus_points_rules_for_orders_type',
					'std' => '1',
					'default' => '1',
					'options' => array(
						'1' => __('With Repeat', 'rewardsystem'),
						'2' => __('Without Repeat', 'rewardsystem'),
					),
				),
				array(
					'name' => __('Enter the Number of Orders', 'rewardsystem'),
					'type' => 'number',
					'id' => 'rs_bonus_points_number_of_orders_with_repeat',
					'newids' => 'rs_bonus_points_number_of_orders_with_repeat',
					'class' => 'rs_bonus_points_number_of_orders_with_repeat',
					'custom_attributes' => array(
						'min' => '1',
					),
				),
				array(
					'name' => __('Enter the Points', 'rewardsystem'),
					'type' => 'number',
					'id' => 'rs_bonus_points_value_number_of_orders_with_repeat',
					'newids' => 'rs_bonus_points_value_number_of_orders_with_repeat',
					'class' => 'rs_bonus_points_value_number_of_orders_with_repeat',
					'custom_attributes' => array(
						'min' => '1',
					),
				),
				array(
					'name' => __('From Date', 'rewardsystem'),
					'type' => 'text',
					'id' => 'rs_bonus_points_from_date_number_of_orders_with_repeat',
					'newids' => 'rs_bonus_points_from_date_number_of_orders_with_repeat',
					'class' => 'rs_bonus_points_from_date_number_of_orders_with_repeat',
				),
				array(
					'name' => __('To Date', 'rewardsystem'),
					'type' => 'text',
					'id' => 'rs_bonus_points_to_date_number_of_orders_with_repeat',
					'newids' => 'rs_bonus_points_to_date_number_of_orders_with_repeat',
					'class' => 'rs_bonus_points_to_date_number_of_orders_with_repeat',
				),
				array(
					'name' => __('Notification', 'rewardsystem'),
					'desc' => __('By enabling this checkbox, you can notify your users by email when they receive the bonus points for reaching the specified number of orders.', 'rewardsystem'),
					'id' => 'rs_number_of_orders_bonus_email_enabled',
					'std' => 'no',
					'default' => 'no',
					'type' => 'checkbox',
					'newids' => 'rs_number_of_orders_bonus_email_enabled',
				),
				array(
					'name' => __('Subject', 'rewardsystem'),
					'id' => 'rs_email_subject_number_of_orders_bonus_point',
					'std' => 'Bonus Reward Points for Orders - Notification',
					'default' => 'Bonus Reward Points for Orders - Notification',
					'type' => 'textarea',
					'newids' => 'rs_email_subject_number_of_orders_bonus_point',
					'class' => 'rs_email_subject_number_of_orders_bonus_point',
				),
				array(
					'name' => __('Message', 'rewardsystem'),
					'id' => 'rs_email_message_number_of_orders_bonus_point',
					'std' => 'Hi [username],<br/><br/> You have earned <b>[rs_bonus_points_for_orders]</b> bonus points for placing succesfull orders on [site_link].<br/><br/>Currently, you have <b>[rs_available_points]</b> points in your account.<br/><br/>Thanks',
					'default' => 'Hi [username],<br/><br/> You have earned <b>[rs_bonus_points_for_orders]</b> bonus points for placing succesfull orders on [site_link].<br/><br/>Currently, you have <b>[rs_available_points]</b> points in your account.<br/><br/>Thanks',
					'type' => 'textarea',
					'newids' => 'rs_email_message_number_of_orders_bonus_point',
					'class' => 'rs_email_message_number_of_orders_bonus_point',
				),
				array(
					'type' => 'rs_bonus_points_without_repeat_rule_for_orders',
				),
				array(
					'type' => 'sectionend',
					'id' => 'rs_bonus_points_for_orders_section',
				),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __('Bonus Reward Points Details', 'rewardsystem'),
					'type' => 'title',
					'id' => 'rs_bonus_points_log_section',
				),
				array(
					'type' => 'rs_bonus_points_log',
				),
				array(
					'type' => 'sectionend',
					'id' => 'rs_bonus_points_log_section',
				),
				array(
					'type' => 'rs_wrapper_end',
				),
			));
		}

		/**
		 * Register admin fields function.
		 */
		public static function reward_system_register_admin_settings() {
			woocommerce_admin_fields(self::reward_system_admin_fields());
		}

		/**
		 * Update the settings.
		 */
		public static function reward_system_update_settings() {

			woocommerce_update_options(self::reward_system_admin_fields());

			$options_data = array(
				'rs_bonus_points_activated' => isset($_REQUEST['rs_bonus_points_module_checkbox']) ? wc_clean(wp_unslash($_REQUEST['rs_bonus_points_module_checkbox'])) : 'no',
				'rs_bonus_points_number_of_orders_without_repeat_rules' => isset($_REQUEST['rs_bonus_points_number_of_orders_without_repeat_rules']) ? wc_clean(wp_unslash($_REQUEST['rs_bonus_points_number_of_orders_without_repeat_rules'])) : array(),
			);

			foreach ($options_data as $option_key => $option_value) {
				update_option($option_key, $option_value);
			}
		}

		/**
		 * Render enable module.
		 */
		public static function render_enable_module() {
			RSModulesTab::checkbox_for_module(get_option('rs_bonus_points_activated'), 'rs_bonus_points_module_checkbox', 'rs_bonus_points_activated');
		}

		/**
		 * Render without repeat rule for orders in bonus points.
		 */
		public static function render_without_repeat_rule_for_orders() {
			include SRP_PLUGIN_PATH . '/includes/admin/views/bonus-points/html-bonus-points-without-repeat-rule-for-orders.php';
		}

		/**
		 * Render bonus points log.
		 */
		public static function render_bonus_points_log() {
			
			if (isset($_GET['srp_action'], $_GET['user_id'])) {
				
				$bonus_points_log = new SRP_View_User_Bonus_Log_List_Table();
				$bonus_points_log->prepare_items();
				$bonus_points_log->display();
			} else {
				$bonus_points_log = new SRP_Bonus_Log_List_Table();
				$bonus_points_log->prepare_items();
				$bonus_points_log->search_box(__('Search Users', 'rewardsystem'), 'search_id');
				$bonus_points_log->display();
			}
		}

		/**
		 * Reset action tab.
		 */
		public static function reset_action_tab() {
			$settings = self::reward_system_admin_fields();
			RSTabManagement::reset_settings($settings);
		}

		/**
		 * Placed order ids for bonus point preview template. 
		 * */
		public static function preview_template() {

			$title = esc_html__('Orders', 'rewardsystem');
			$template_name = 'rs-bonus-placed-order-ids-backbone-modal';
			$wrapper_class_name = 'rs-bonus-placed-order-ids-contents-wrapper';
			$contents_class_name = 'rs-bonus-placed-order-ids-contents';

			include SRP_PLUGIN_PATH . '/includes/admin/views/backbone-modal/html-backbone-modal.php';
		}
	}

	RSBonusPointsModule::init();
}
