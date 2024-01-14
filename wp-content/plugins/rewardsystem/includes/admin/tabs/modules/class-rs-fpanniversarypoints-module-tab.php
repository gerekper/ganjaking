<?php

/*
 * Anniversary Reward Points Module Setting
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSAnniversaryPointsModule' ) ) {

	class RSAnniversaryPointsModule {
		/*
		 * Init.
		 */

		public static function init() {

			add_action( 'woocommerce_rs_settings_tabs_fpanniversarypoints', array( __CLASS__, 'reward_system_register_admin_settings' ) ); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

			add_action( 'woocommerce_update_options_fprsmodules_fpanniversarypoints', array( __CLASS__, 'reward_system_update_settings' ) ); // call the woocommerce_update_options_{slugname} to update the reward system                               

			add_action( 'woocommerce_admin_field_rs_enable_disable_anniversarypoints_module', array( __CLASS__, 'render_enable_module' ) );

			add_action( 'woocommerce_admin_field_rs_account_anniversary_rule_based_type', array( __CLASS__, 'render_account_anniversary_rule_based' ) );

			add_action( 'woocommerce_admin_field_rs_custom_anniversary_rule_based_type', array( __CLASS__, 'render_custom_anniversary_rule_based' ) );

			add_action( 'woocommerce_admin_field_rs_anniversary_details_table', array( __CLASS__, 'render_anniversary_details' ) );

			add_action( 'rs_display_save_button_fpanniversarypoints', array( 'RSTabManagement', 'rs_display_save_button' ) );

			add_action( 'rs_display_reset_button_fpanniversarypoints', array( 'RSTabManagement', 'rs_display_reset_button' ) );

			add_action( 'fp_action_to_reset_module_settings_fpanniversarypoints', array( __CLASS__, 'reset_action_tab' ) );

			add_action( 'admin_footer', array( __CLASS__, 'preview_template' ) );
		}

		/*
		 * Function label settings for bonus reward points module.
		 */

		public static function reward_system_admin_fields() {
						/**
						 * Hook:woocommerce_fpanniversarypoints_settings.
						 * 
						 * @since 1.0
						 */
			return apply_filters( 'woocommerce_fpanniversarypoints_settings', array(
				array(
					'type' => 'rs_modulecheck_start',
				),
				array(
					'name' => __( 'Anniversary Reward Points Module', 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_activate_anniversarypoints_module',
				),
				array(
					'type' => 'rs_enable_disable_anniversarypoints_module',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_activate_anniversarypoints_module' ),
				array(
					'type' => 'rs_modulecheck_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Account Anniversary Reward Points', 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_account_anniversary_points_section',
				),
				array(
					'name'    => __( 'Account Anniversary Reward Points', 'rewardsystem' ),
					'type'    => 'checkbox',
					'id'      => 'rs_enable_account_anniversary_point',
					'newids'  => 'rs_enable_account_anniversary_point',
					'class'   => 'rs_enable_account_anniversary_point',
					'std'     => 'no',
					'default' => 'no',
					'desc'    => __( 'By enabling this checkbox, you can award points to your users for maintaining an account on your site.', 'rewardsystem' ),
				),
				array(
					'name'    => __( 'Award Points for', 'rewardsystem' ),
					'type'    => 'select',
					'id'      => 'rs_account_anniversary_point_type',
					'newids'  => 'rs_account_anniversary_point_type',
					'class'   => 'rs_account_anniversary_point_type',
					'std'     => 'one_time',
					'default' => 'one_time',
					'options' => array(
						'one_time'   => __( 'One Time', 'rewardsystem' ),
						'every_year' => __( 'Every Year', 'rewardsystem' ),
						'rule_based' => __( 'Reaching Level', 'rewardsystem' ),
					),
				),
				array(
					'name'              => __( 'Enter the Points Value', 'rewardsystem' ),
					'type'              => 'number',
					'id'                => 'rs_account_anniversary_point_value',
					'newids'            => 'rs_account_anniversary_point_value',
					'class'             => 'rs_account_anniversary_point_value',
					'custom_attributes' => array(
						'min' => '1',
					),
				),
				array(
					'name'    => __( 'Reward Log', 'rewardsystem' ),
					'type'    => 'textarea',
					'id'      => 'rs_account_anniversary_field_reward_log',
					'newids'  => 'rs_account_anniversary_field_reward_log',
					'class'   => 'rs_account_anniversary_field_reward_log',
										'std'     => 'Points earned for Account Anniversary',
					'default' => 'Points earned for Account Anniversary',
				),
				array(
					'name'    => __( 'Notification', 'rewardsystem' ),
					'desc'    => __( 'By enabling this checkbox, you can send email to your users for maintaining their account on your site', 'rewardsystem' ),
					'id'      => 'rs_enable_account_anniversary_mail',
					'std'     => 'no',
					'default' => 'no',
					'type'    => 'checkbox',
					'newids'  => 'rs_enable_account_anniversary_mail',
				),
				array(
					'name'    => __( 'Subject', 'rewardsystem' ),
					'id'      => 'rs_email_subject_account_anniversary',
					'std'     => 'Account Anniversary Reward Points - Notification',
					'default' => 'Account Anniversary Reward Points - Notification',
					'type'    => 'textarea',
					'newids'  => 'rs_email_subject_account_anniversary',
					'class'   => 'rs_email_subject_account_anniversary',
				),
				array(
					'name'    => __( 'Message', 'rewardsystem' ),
					'id'      => 'rs_email_message_account_anniversary',
					'std'     => 'Hi [username],<br/><br/> You have earned <b>[rs_account_maintenance_points]</b> points for maintaining the account on the site [site_link].<br/><br/>Currently, you have <b>[rs_available_points]</b> points in your account.<br/><br/>Thanks',
					'default' => 'Hi [username],<br/><br/> You have earned <b>[rs_account_maintenance_points]</b> points for maintaining the account on the site [site_link].<br/><br/>Currently, you have <b>[rs_available_points]</b> points in your account.<br/><br/>Thanks',
					'type'    => 'textarea',
					'newids'  => 'rs_email_message_account_anniversary',
					'class'   => 'rs_email_message_account_anniversary',
				),
				array(
					'type' => 'rs_account_anniversary_rule_based_type',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_account_anniversary_points_section' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Custom Anniversary Reward Points', 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_custom_anniversary_points_section',
				),
				array(
					'name'    => __( 'Custom Anniversary Reward Points', 'rewardsystem' ),
					'type'    => 'checkbox',
					'id'      => 'rs_enable_custom_anniversary_point',
					'newids'  => 'rs_enable_custom_anniversary_point',
					'class'   => 'rs_enable_custom_anniversary_point',
					'std'     => 'no',
					'default' => 'no',
					'desc'    => __( 'By enabling this checkbox, you can award points to your users when reaching the Anniversary Date given by them.', 'rewardsystem' ),
				),
				array(
					'name'    => __( 'Anniversary Type', 'rewardsystem' ),
					'type'    => 'select',
					'id'      => 'rs_custom_anniversary_point_type',
					'newids'  => 'rs_custom_anniversary_point_type',
					'class'   => 'rs_custom_anniversary_point_type',
					'std'     => 'single_anniversary',
					'default' => 'single_anniversary',
					'options' => array(
						'single_anniversary'   => __( 'Single Anniversary', 'rewardsystem' ),
						'multiple_anniversary' => __( 'Multiple Anniversary', 'rewardsystem' ),
					),
				),
				array(
					'name'              => __( 'Enter the Points Value', 'rewardsystem' ),
					'type'              => 'number',
					'id'                => 'rs_custom_anniversary_point_value',
					'newids'            => 'rs_custom_anniversary_point_value',
					'class'             => 'rs_custom_anniversary_point_value',
					'custom_attributes' => array(
						'min' => '1',
					),
				),
				array(
					'name'    => __( 'Repeat Every Year', 'rewardsystem' ),
					'type'    => 'checkbox',
					'id'      => 'rs_enable_repeat_custom_anniversary_point',
					'newids'  => 'rs_enable_repeat_custom_anniversary_point',
					'class'   => 'rs_enable_repeat_custom_anniversary_point',
					'std'     => 'no',
					'default' => 'no',
				),
				array(
					'name'    => __( 'Anniversary Field', 'rewardsystem' ),
					'type'    => 'checkbox',
					'id'      => 'rs_enable_mandatory_custom_anniversary_point',
					'newids'  => 'rs_enable_mandatory_custom_anniversary_point',
					'class'   => 'rs_enable_mandatory_custom_anniversary_point',
					'std'     => 'no',
					'default' => 'no',
					'desc'    => __( 'By enabling this checkbox, you can set the anniversary field as a mandatory one.', 'rewardsystem' ),
				),
				array(
					'name'    => __( 'Anniversary Field Name', 'rewardsystem' ),
					'type'    => 'textarea',
					'id'      => 'rs_custom_anniversary_field_name',
					'newids'  => 'rs_custom_anniversary_field_name',
					'class'   => 'rs_custom_anniversary_field_name',
					'default' => 'Anniversary',
				),
				array(
					'name'    => __( 'Description', 'rewardsystem' ),
					'type'    => 'textarea',
					'id'      => 'rs_custom_anniversary_field_desc',
					'newids'  => 'rs_custom_anniversary_field_desc',
					'class'   => 'rs_custom_anniversary_field_desc',
					'default' => 'Select the anniversary date to earn {anniversary_points} points when reaching the given date.',
				),
				array(
					'name'    => __( 'Reward Log', 'rewardsystem' ),
					'type'    => 'textarea',
					'id'      => 'rs_custom_anniversary_field_reward_log',
					'newids'  => 'rs_custom_anniversary_field_reward_log',
					'class'   => 'rs_custom_anniversary_field_reward_log',
										'std'     => 'Points earned for Account Anniversary',
					'default' => 'Points earned for {anniversary_name}',
				),
				array(
					'name'    => __( 'Notification', 'rewardsystem' ),
					'desc'    => __( 'By enabling this checkbox, you can send email to your users when they earn Anniversary Reward Points.', 'rewardsystem' ),
					'id'      => 'rs_enable_custom_anniversary_mail',
					'std'     => 'no',
					'default' => 'no',
					'type'    => 'checkbox',
					'newids'  => 'rs_enable_custom_anniversary_mail',
				),
				array(
					'name'    => __( 'Subject', 'rewardsystem' ),
					'id'      => 'rs_email_subject_custom_anniversary',
					'std'     => 'Anniversary Reward Points - Notification',
					'default' => 'Anniversary Reward Points - Notification',
					'type'    => 'textarea',
					'newids'  => 'rs_email_subject_custom_anniversary',
					'class'   => 'rs_email_subject_custom_anniversary',
				),
				array(
					'name'    => __( 'Message', 'rewardsystem' ),
					'id'      => 'rs_email_message_custom_anniversary',
					'std'     => 'Hi [username],<br/><br/> You have earned <b>[rs_anniversary_points]</b> points for reaching the Anniversary Date given on the site [site_link].<br/><br/>Currently, you have <b>[rs_available_points]</b> points in your account.<br/><br/>Thanks',
					'default' => 'Hi [username],<br/><br/> You have earned <b>[rs_anniversary_points]</b> points for reaching the Anniversary Date given on the site [site_link].<br/><br/>Currently, you have <b>[rs_available_points]</b> points in your account.<br/><br/>Thanks',
					'type'    => 'textarea',
					'newids'  => 'rs_email_message_custom_anniversary',
					'class'   => 'rs_email_message_custom_anniversary',
				),
				array(
					'type' => 'rs_custom_anniversary_rule_based_type',
				),
				array(
					'type' => 'sectionend',
					'id'   => '_rs_custom_anniversary_points_section',
				),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Anniversary Details', 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_anniversary_details_table_section',
				),
				array(
					'type' => 'rs_anniversary_details_table',
				),
				array(
					'type' => 'sectionend',
					'id'   => '_rs_anniversary_details_table_section',
				),
				array(
					'type' => 'rs_wrapper_end',
				),
					) );
		}

		/**
		 * Register admin fields function.
		 */
		public static function reward_system_register_admin_settings() {
			woocommerce_admin_fields( self::reward_system_admin_fields() );
		}

		/**
		 * Update the settings.
		 */
		public static function reward_system_update_settings() {

			woocommerce_update_options( self::reward_system_admin_fields() );

			$options_data = array(
				'rs_account_anniversary_rules' => isset( $_REQUEST[ 'rs_account_anniversary_rules' ] ) ? wc_clean( wp_unslash( $_REQUEST[ 'rs_account_anniversary_rules' ] ) ) : array(),
				'rs_custom_anniversary_rules'  => isset( $_REQUEST[ 'rs_custom_anniversary_rules' ] ) ? wc_clean( wp_unslash( $_REQUEST[ 'rs_custom_anniversary_rules' ] ) ) : array(),
			);

			foreach ( $options_data as $option_key => $option_value ) {
				update_option( $option_key, $option_value );
			}
		}

		/**
		 * Render enable module.
		 */
		public static function render_enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_anniversary_points_activated' ), 'rs_anniversary_points_module_checkbox', 'rs_anniversary_points_activated' );
		}

		/**
		 * Render account anniversary rule based.
		 */
		public static function render_account_anniversary_rule_based() {
			include SRP_PLUGIN_PATH . '/includes/admin/views/anniversary-points/html-account-anniversary-rule-based.php';
		}

		/**
		 * Render custom anniversary rule based.
		 */
		public static function render_custom_anniversary_rule_based() {
			include SRP_PLUGIN_PATH . '/includes/admin/views/anniversary-points/html-custom-anniversary-rule-based.php';
		}

		/**
		 * Render anniversary details.
		 */
		public static function render_anniversary_details() {

			$anniv_reward_table = new SRP_Anniversary_Log_List_Table();
			$anniv_reward_table->prepare_items();
			$anniv_reward_table->search_box( __( 'Search Users', 'rewardsystem' ), 'search_id' );
			$anniv_reward_table->display();
		}

		/**
		 * Reset action tab.
		 */
		public static function reset_action_tab() {
			$settings = self::reward_system_admin_fields();
			RSTabManagement::reset_settings( $settings );
		}

		/**
		 * Render multiple anniversary dates preview template. 
		 * */
		public static function preview_template() {

			self::account_anniversary_points_preview();

			self::single_anniversary_points_preview();

			self::multiple_anniversary_points_preview();
		}

		/**
		 * Account anniversary points preview. 
		 * */
		public static function account_anniversary_points_preview() {

			$title               = esc_html__( 'Account Anniversary Details', 'rewardsystem' );
			$template_name       = 'rs-account-anniversary-points-backbone-modal';
			$wrapper_class_name  = 'rs-account-anniversary-points-contents-wrapper';
			$contents_class_name = 'rs-account-anniversary-points-contents';

			include SRP_PLUGIN_PATH . '/includes/admin/views/backbone-modal/html-backbone-modal.php';
		}

		/**
		 * Single anniversary points preview. 
		 * */
		public static function single_anniversary_points_preview() {

			$title               = esc_html__( 'Single Anniversary Details', 'rewardsystem' );
			$template_name       = 'rs-single-anniversary-points-backbone-modal';
			$wrapper_class_name  = 'rs-single-anniversary-points-contents-wrapper';
			$contents_class_name = 'rs-single-anniversary-points-contents';

			include SRP_PLUGIN_PATH . '/includes/admin/views/backbone-modal/html-backbone-modal.php';
		}

		/**
		 * Multiple anniversary points preview. 
		 * */
		public static function multiple_anniversary_points_preview() {

			$title               = esc_html__( 'Multiple Anniversary Details', 'rewardsystem' );
			$template_name       = 'rs-multiple-anniversary-points-backbone-modal';
			$wrapper_class_name  = 'rs-multiple-anniversary-points-contents-wrapper';
			$contents_class_name = 'rs-multiple-anniversary-points-contents';

			include SRP_PLUGIN_PATH . '/includes/admin/views/backbone-modal/html-backbone-modal.php';
		}
	}

	RSAnniversaryPointsModule::init();
}
