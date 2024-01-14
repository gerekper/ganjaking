<?php

/*
 * Promotional Points Setting Tsb
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSPromotionalPoints' ) ) {

	class RSPromotionalPoints {

		public static function init() {

			add_action( 'woocommerce_rs_settings_tabs_fppromotional' , array( __CLASS__, 'register_admin_options' ) ) ;

			add_action( 'rs_default_settings_fppromotional' , array( __CLASS__, 'set_default_value' ) ) ;

			add_action( 'woocommerce_update_options_fprsmodules_fppromotional' , array( __CLASS__, 'update_settings' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_disable_promotional_module' , array( __CLASS__, 'enable_module' ) ) ;

			add_action( 'woocommerce_admin_field_fp_promotional_rules' , array( __CLASS__, 'fp_promotional_rules' ) ) ;

			add_action( 'fp_action_to_reset_module_settings_fppromotional' , array( __CLASS__, 'reset_promotional_module' ) ) ;

			add_action( 'rs_display_save_button_fppromotional' , array( 'RSTabManagement', 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fppromotional' , array( 'RSTabManagement', 'rs_display_reset_button' ) ) ;
		}

		public static function settings_option() {
						/**
						 * Hook:woocommerce_fppromotional_settings.
						 * 
						 * @since 1.0
						 */
			return apply_filters( 'woocommerce_fppromotional_settings' , array(
				array(
					'type' => 'rs_modulecheck_start',
				),
				array(
					'name' => __( 'Promotion Reward Points' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_activate_promotional_module',
				),
				array(
					'type' => 'rs_enable_disable_promotional_module',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_activate_promotional_module' ),
				array(
					'type' => 'rs_modulecheck_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Promotion Reward Points Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_promotional_setting',
				),
				array(
					'type' => 'fp_promotional_rules',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_promotional_setting' ),
				array(
					'type' => 'rs_wrapper_end',
				),
								array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_message_setting',
				),
								array(
					'type' => 'title',
					'desc' => __('<b>[rs_promotion_message] – Use this Shortcode to display Promotion Message in any page</b>', 'rewardsystem'),
								),
				array(
									'name'    => __( 'Promotion Message' , 'rewardsystem' ),
									'id'      => 'rs_message_for_promotion',
									'std'     => 'Earn {multiplicator_Value}X Reward Points till {to_date}',
									'default' => 'Earn {multiplicator_Value}X Reward Points till {to_date}',
									'type'    => 'textarea',
									'newids'  => 'rs_message_for_promotion',
								),
				array( 'type' => 'sectionend', 'id' => '_rs_message_setting' ),
				array(
					'type' => 'rs_wrapper_end',
				),
					) ) ;
		}

		public static function register_admin_options() {
			woocommerce_admin_fields( self::settings_option() ) ;
		}

		public static function update_settings() {
			woocommerce_update_options( self::settings_option() ) ;
			if ( isset( $_REQUEST[ 'rs_promotional_module_checkbox' ] ) ) {
				update_option( 'rs_promotional_points_activated' , wc_clean( wp_unslash( $_REQUEST[ 'rs_promotional_module_checkbox' ] ) ) ) ;
			} else {
				update_option( 'rs_promotional_points_activated' , 'no' ) ;
			}

			if ( isset( $_REQUEST[ 'srp_promotional_rules' ] ) ) {

				$promotional_rules = wc_clean( wp_unslash( $_REQUEST[ 'srp_promotional_rules' ] ) ) ;

				foreach ( $promotional_rules as $rule_id => $rules ) {
					$update = true ;

					if ( 'new' == $rule_id ) {
						foreach ( $rules as $rule ) {

							$update = srp_get_error_msg_for_rules( $update , $rule ) ;
							if ( $update ) {
								$rule_post_args          = array(
									'post_title' => $rule[ 'srp_name' ],
										) ;
								$rule[ 'srp_enable' ]    = isset( $rule[ 'srp_enable' ] ) ? 'yes' : 'no' ;
								$rule[ 'srp_from_date' ] = $rule[ 'srp_from_date' ] ;
								$rule[ 'srp_to_date' ]   = $rule[ 'srp_to_date' ] ;
								$rule_id                 = srp_create_new_rule( $rule , $rule_post_args ) ;
							}
						}
					} else {
						$update = srp_get_error_msg_for_rules( $update , $rules ) ;
						if ( $update ) {
							$rules[ 'srp_enable' ]    = isset( $rules[ 'srp_enable' ] ) ? 'yes' : 'no' ;
							$rules[ 'srp_from_date' ] = $rules[ 'srp_from_date' ] ;
							$rules[ 'srp_to_date' ]   = $rules[ 'srp_to_date' ] ;
							srp_update_rule( $rule_id , $rules ) ;
						}
					}
				}
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
			RSModulesTab::checkbox_for_module( get_option( 'rs_promotional_points_activated' ) , 'rs_promotional_module_checkbox' , 'rs_promotional_points_activated' ) ;
		}

		public static function reset_points_url_module() {
			$settings = self::settings_option() ;
			RSTabManagement::reset_settings( $settings ) ;
		}

		public static function fp_promotional_rules() {
			$rule_ids = srp_get_rule_ids() ;

			include_once SRP_PLUGIN_PATH . '/includes/admin/views/promotional/promotional-settings.php' ;
		}
	}

	RSPromotionalPoints::init() ;
}
