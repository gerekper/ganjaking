<?php

/*
 * Birthday Reward Points Setting Tsb
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSBirthdayPoints' ) ) {

	class RSBirthdayPoints {

		public static function init() {

			add_action( 'woocommerce_rs_settings_tabs_fpbirthday' , array( __CLASS__, 'register_admin_options' ) ) ;

			add_action( 'rs_default_settings_fpbirthday' , array( __CLASS__, 'set_default_value' ) ) ;

			add_action( 'woocommerce_update_options_fprsmodules_fpbirthday' , array( __CLASS__, 'update_settings' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_disable_birthday_module' , array( __CLASS__, 'enable_module' ) ) ;

			add_action( 'woocommerce_admin_field_birthday_reward_log' , array( __CLASS__, 'birthday_reward_log' ) ) ;

			add_action( 'fp_action_to_reset_module_settings_fpbirthday' , array( __CLASS__, 'reset_birthday_points_module' ) ) ;

			add_action( 'rs_display_save_button_fpbirthday' , array( 'RSTabManagement', 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fpbirthday' , array( 'RSTabManagement', 'rs_display_reset_button' ) ) ;
		}

		public static function settings_option() {
			/**
			 * Hook:woocommerce_fpbirthday_settings.
			 *
			 * @since 1.0
			 */
			return apply_filters(
					'woocommerce_fpbirthday_settings' , array(
				array(
					'type' => 'rs_modulecheck_start',
					),
					array(
					'name' => __( 'Birthday Reward Points' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_activate_birthday_module',
					),
					array(
					'type' => 'rs_enable_disable_birthday_module',
					),
					array(
					'type' => 'sectionend',
					'id'   => '_rs_activate_birthday_module',
					),
					array(
					'type' => 'rs_modulecheck_end',
					),
					array(
					'type' => 'rs_wrapper_start',
					),
					array(
					'name' => __( 'Birthday Reward Points Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_birthday_setting',
					),
					array(
					'name'    => __( 'Birthday Reward Points' , 'rewardsystem' ),
					'desc'    => __( 'By enabling this checkbox, you can award points to your users every year when they reaching the given birthday date.' , 'rewardsystem' ),
					'id'      => 'rs_enable_bday_points',
					'type'    => 'checkbox',
					'std'     => 'no',
					'default' => 'no',
					'newids'  => 'rs_enable_bday_points',
					),
					array(
					'name'              => __( 'Enter the Points' , 'rewardsystem' ),
					'id'                => 'rs_bday_points',
					'newids'            => 'rs_bday_points',
					'type'              => 'number',
					'std'               => '',
					'default'           => '',
					'custom_attributes' => array(
						'min'  => '0',
						'step' => 'any',
					),
					),
					array(
					'name'     => __( 'Validity Period for Points' , 'rewardsystem' ),
					'id'       => 'rs_bday_validity_period',
					'type'     => 'number',
					'std'      => '',
					'default'  => '',
					'desc_tip' => true,
					'newids'   => 'rs_bday_validity_period',
					'desc'     => __( 'The points will be expired after the number of days exceeded from the date of earning.' , 'rewardsystem' ),
					),
					array(
					'type' => 'sectionend',
					'id'   => '_rs_birthday_setting',
					),
					array(
					'type' => 'rs_wrapper_end',
					),
					array(
					'type' => 'rs_wrapper_start',
					),
					array(
					'name' => __( 'Birthday Field Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => 'rs_bday_field_settings',
					),
					array(
					'name'    => __( 'Birthday Field Name' , 'rewardsystem' ),
					'id'      => 'rs_bday_field_label',
					'std'     => 'DOB',
					'default' => 'DOB',
					'type'    => 'text',
					'newids'  => 'rs_bday_field_label',
					),
					array(
					'name'    => __( 'Description' , 'rewardsystem' ),
					'id'      => 'rs_bday_field_reason_label',
					'std'     => 'Enter your birthdate to earn {birthday_points} points every year.',
					'default' => 'Enter your birthdate to earn {birthday_points} points every year.',
					'type'    => 'textarea',
					'newids'  => 'rs_bday_field_reason_label',
					),
					array(
					'name'    => __( 'Birthday Field' , 'rewardsystem' ),
					'id'      => 'rs_enable_bday_field_mandatory',
					'type'    => 'checkbox',
					'std'     => 'no',
					'default' => 'no',
					'desc'    => __( 'By enabling this checkbox, you can set the birthday field as a mandatory one.' , 'rewardsystem' ),
					'newids'  => 'rs_enable_bday_field_mandatory',
					),
					array(
					'name'    => __( 'Error Message' , 'rewardsystem' ),
					'id'      => 'rs_bday_field_mandatory_error',
					'std'     => '{field_name} field is mandatory',
					'default' => '{field_name} field is mandatory',
					'type'    => 'textarea',
					'newids'  => 'rs_bday_field_mandatory_error',
					),
					array(
					'type' => 'sectionend',
					'id'   => 'rs_bday_field_settings',
					),
					array(
					'type' => 'rs_wrapper_end',
					),
					array(
					'type' => 'rs_wrapper_start',
					),
					array(
					'name' => __( 'Email Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_bday_email_settings',
					),
					array(
					'name'    => __( 'Notification' , 'rewardsystem' ),
					'desc'    => __( 'By enabling this checkbox, you can notify your users by email when they receive the Birthday Reward Points.' , 'rewardsystem' ),
					'id'      => 'rs_send_mail_for_bday_points',
					'type'    => 'checkbox',
					'std'     => 'no',
					'default' => 'no',
					'newids'  => 'rs_send_mail_for_bday_points',
					),
					array(
					'name'    => __( 'Subject' , 'rewardsystem' ),
					'id'      => 'rs_email_subject_for_bday_points',
					'std'     => '[username] - Birthday Reward Points - Notification',
					'default' => 'Birthday Reward Points - Notification',
					'type'    => 'textarea',
					'newids'  => 'rs_email_subject_for_bday_points',
					),
					array(
					'name'    => __( 'Message' , 'rewardsystem' ),
					'id'      => 'rs_email_message_for_bday_points',
					'std'     => 'Hi [username], You have earned [rs_birthday_points] points for your Birthday. Currently, you have [rs_available_points] points in your account.',
					'default' => 'Hi [username], You have earned [rs_birthday_points] points for your Birthday. Currently, you have [rs_available_points] points in your account.',
					'type'    => 'textarea',
					'newids'  => 'rs_email_message_for_bday_points',
					),
					array(
					'type' => 'sectionend',
					'id'   => '_rs_bday_email_settings',
					),
					array(
					'type' => 'rs_wrapper_end',
					),
					array(
					'type' => 'rs_wrapper_start',
					),
					array(
					'name' => __( 'Birthday Details' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => 'rs_bday_details',
					),
					array(
					'type' => 'birthday_reward_log',
					),
					array(
					'type' => 'sectionend',
					'id'   => 'rs_bday_details',
					),
					array(
					'type' => 'rs_wrapper_end',
					),
					)
			) ;
		}

		public static function register_admin_options() {
			woocommerce_admin_fields( self::settings_option() ) ;
		}

		public static function update_settings() {
			woocommerce_update_options( self::settings_option() ) ;
			if ( isset( $_REQUEST[ 'rs_bday_module_checkbox' ] ) ) {
				update_option( 'rs_bday_points_activated' , wc_clean( wp_unslash( $_REQUEST[ 'rs_bday_module_checkbox' ] ) ) ) ;
			} else {
				update_option( 'rs_bday_points_activated' , 'no' ) ;
			}
		}

		public static function set_default_value() {
			foreach ( self::settings_option() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		public static function enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_bday_points_activated' ) , 'rs_bday_module_checkbox' , 'rs_bday_points_activated' ) ;
		}

		public static function reset_birthday_points_module() {
			$settings = self::settings_option() ;
			RSTabManagement::reset_settings( $settings ) ;
		}

		public static function birthday_reward_log() {
			$birthday_reward_table = new SRP_Birthday_Reward_Table() ;
			$birthday_reward_table->prepare_items() ;
			$birthday_reward_table->search_box( __( 'Search Users' , 'rewardsystem' ) , 'search_id' ) ;
			$birthday_reward_table->display() ;
		}
	}

	RSBirthdayPoints::init() ;
}
