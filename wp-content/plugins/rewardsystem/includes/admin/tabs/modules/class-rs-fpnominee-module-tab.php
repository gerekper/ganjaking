<?php
/*
 * Nominee Setting Tab
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSNominee' ) ) {

	class RSNominee {

		public static function init() {
			add_action( 'woocommerce_rs_settings_tabs_fpnominee' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

			add_action( 'woocommerce_update_options_fprsmodules_fpnominee' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

			add_action( 'rs_default_settings_fpnominee' , array( __CLASS__ , 'set_default_value' ) ) ;

			add_action( 'woocommerce_admin_field_rs_select_nominee_for_user' , array( __CLASS__ , 'rs_select_user_as_nominee' ) ) ;

			add_action( 'woocommerce_admin_field_rs_select_nominee_for_user_shortcode' , array( __CLASS__ , 'rs_select_user_as_nominee_shortcode' ) ) ;

			add_action( 'woocommerce_admin_field_rs_select_nominee_for_user_in_checkout' , array( __CLASS__ , 'rs_select_user_as_nominee_in_checkout' ) ) ;

			add_action( 'woocommerce_admin_field_rs_nominee_list_table' , array( __CLASS__ , 'rs_function_to_display_nominee_list_table' ) ) ;

			add_action( 'fp_action_to_reset_module_settings_fpnominee' , array( __CLASS__ , 'reset_nominee_module' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_disable_nominee_module' , array( __CLASS__ , 'enable_module' ) ) ;
			
			add_action( 'rs_display_save_button_fpnominee' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fpnominee' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
		}

		/*
		 * Function label settings to Member Level Tab
		 */

		public static function reward_system_admin_fields() {
			global $woocommerce ;
			global $wp_roles ;
			foreach ( $wp_roles->roles as $values => $key ) {
				$userroleslug[] = $values ;
				$userrolename[] = $key[ 'name' ] ;
			}

			$newcombineduserrole = array_combine( ( array ) $userroleslug , ( array ) $userrolename ) ;
			return apply_filters( 'woocommerce_fpnominee_settings' , array(
				array(
					'type' => 'rs_modulecheck_start' ,
				) ,
				array(
					'name' => __( 'Nominee Module' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_activate_nominee_module'
				) ,
				array(
					'type' => 'rs_enable_disable_nominee_module' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_activate_nominee_module' ) ,
				array(
					'type' => 'rs_modulecheck_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Nominee Settings for Product Purchase in Checkout Page' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_nominee_setting_in_checkout'
				) ,
				array(
					'name'    => __( 'Nominee Field' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_nominee_field_in_checkout' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_show_hide_nominee_field_in_checkout' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'     => __( 'My Nominee Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the My Nominee Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_nominee_title_in_checkout' ,
					'std'      => 'My Nominee' ,
					'default'  => 'My Nominee' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_nominee_title_in_checkout' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Nominee User Selection' , 'rewardsystem' ) ,
					'id'      => 'rs_select_type_of_user_for_nominee_checkout' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'By User(s)' , 'rewardsystem' ) ,
						'2' => __( 'By User Role(s)' , 'rewardsystem' ) ,
					) ,
					'newids'  => 'rs_select_type_of_user_for_nominee_checkout' ,
				) ,
				array(
					'type' => 'rs_select_nominee_for_user_in_checkout' ,
				) ,
				array(
					'name'        => __( 'User Role Selection' , 'rewardsystem' ) ,
					'id'          => 'rs_select_users_role_for_nominee_checkout' ,
					'css'         => 'min-width:343px;' ,
					'std'         => '' ,
					'default'     => '' ,
					'placeholder' => 'Search for a User Role' ,
					'type'        => 'multiselect' ,
					'options'     => $newcombineduserrole ,
					'newids'      => 'rs_select_users_role_for_nominee_checkout' ,
					'desc_tip'    => false ,
				) ,
				array(
					'name'    => __( 'Nominee Selection' , 'rewardsystem' ) ,
					'id'      => 'rs_nominee_selection_in_checkout' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Optional' , 'rewardsystem' ) ,
						'2' => __( 'Mandatory' , 'rewardsystem' ) ,
					) ,
					'newids'  => 'rs_nominee_selection_in_checkout' ,
				) ,
				array(
					'name'    => __( 'Checkout Page Nominee is identified based on' , 'rewardsystem' ) ,
					'id'      => 'rs_select_type_of_user_for_nominee_name_checkout' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'User Email ' , 'rewardsystem' ) ,
						'2' => __( 'Username' , 'rewardsystem' ) ,
					) ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_nominee_setting_in_checkout' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Nominee Settings for Product Purchase in My Account Page' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_nominee_setting'
				) ,
				array(
					'name'    => __( 'Nominee Field' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_nominee_field' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_show_hide_nominee_field' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'     => __( 'My Nominee Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the My Nominee Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_nominee_title' ,
					'std'      => 'My Nominee' ,
					'default'  => 'My Nominee' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_nominee_title' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Nominee User Selection' , 'rewardsystem' ) ,
					'id'      => 'rs_select_type_of_user_for_nominee' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'By User(s)' , 'rewardsystem' ) ,
						'2' => __( 'By User Role(s)' , 'rewardsystem' ) ,
					) ,
					'newids'  => 'rs_select_type_of_user_for_nominee' ,
				) ,
				array(
					'type' => 'rs_select_nominee_for_user' ,
				) ,
				array(
					'name'        => __( 'User Role Selection' , 'rewardsystem' ) ,
					'id'          => 'rs_select_users_role_for_nominee' ,
					'css'         => 'min-width:343px;' ,
					'std'         => '' ,
					'default'     => '' ,
					'placeholder' => 'Search for a User Role' ,
					'type'        => 'multiselect' ,
					'options'     => $newcombineduserrole ,
					'newids'      => 'rs_select_users_role_for_nominee' ,
					'desc_tip'    => false ,
				) ,
				array(
					'name'    => __( 'My Account Page Nominee is identified based on' , 'rewardsystem' ) ,
					'id'      => 'rs_select_type_of_user_for_nominee_name' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'User Email ' , 'rewardsystem' ) ,
						'2' => __( 'Username' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Nominee Field - Shortcode' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_nominee_field_shortcode' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_show_hide_nominee_field_shortcode' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'     => __( 'My Nominee Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the My Nominee Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_nominee_title_shortcode' ,
					'std'      => 'My Nominee' ,
					'default'  => 'My Nominee' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_nominee_title_shortcode' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Nominee User Selection' , 'rewardsystem' ) ,
					'id'      => 'rs_select_type_of_user_for_nominee_shortcode' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'By User(s)' , 'rewardsystem' ) ,
						'2' => __( 'By User Role(s)' , 'rewardsystem' ) ,
					) ,
					'newids'  => 'rs_select_type_of_user_for_nominee_shortcode' ,
				) ,
				array(
					'type' => 'rs_select_nominee_for_user_shortcode' ,
				) ,
				array(
					'name'        => __( 'User Role Selection' , 'rewardsystem' ) ,
					'id'          => 'rs_select_users_role_for_nominee_shortcode' ,
					'css'         => 'min-width:343px;' ,
					'std'         => '' ,
					'default'     => '' ,
					'placeholder' => 'Search for a User Role' ,
					'type'        => 'multiselect' ,
					'options'     => $newcombineduserrole ,
					'newids'      => 'rs_select_users_role_for_nominee_shortcode' ,
					'desc_tip'    => false ,
				) ,
				array(
					'name'    => __( 'My Account Page Nominee is identified based on' , 'rewardsystem' ) ,
					'id'      => 'rs_select_type_of_user_for_nominee_name_shortcode' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'newids'  => 'rs_select_type_of_user_for_nominee_name_shortcode' ,
					'options' => array(
						'1' => __( 'User Email ' , 'rewardsystem' ) ,
						'2' => __( 'Username' , 'rewardsystem' ) ,
					) ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_nominee_setting' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Nominated Users List' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_nominated_user_list'
				) ,
				array(
					'type' => 'rs_nominee_list_table'
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_nominated_user_list' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
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
			if ( isset( $_REQUEST[ 'rs_select_users_role_for_nominee' ] ) ) {
				update_option( 'rs_select_users_role_for_nominee' , wc_clean(wp_unslash($_REQUEST[ 'rs_select_users_role_for_nominee' ] ))) ;
			} else {
				update_option( 'rs_select_users_role_for_nominee' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_select_users_role_for_nominee_shortcode' ] ) ) {
				update_option( 'rs_select_users_role_for_nominee_shortcode' , wc_clean(wp_unslash($_REQUEST[ 'rs_select_users_role_for_nominee_shortcode' ])) ) ;
			} else {
				update_option( 'rs_select_users_role_for_nominee_shortcode' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_select_users_list_for_nominee' ] ) ) {
				update_option( 'rs_select_users_list_for_nominee' , wc_clean(wp_unslash($_REQUEST[ 'rs_select_users_list_for_nominee' ] ) ));
			} else {
				update_option( 'rs_select_users_list_for_nominee' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_select_users_list_for_nominee_shortcode' ] ) ) {
				update_option( 'rs_select_users_list_for_nominee_shortcode' , wc_clean(wp_unslash($_REQUEST[ 'rs_select_users_list_for_nominee_shortcode' ] ) ));
			} else {
				update_option( 'rs_select_users_list_for_nominee_shortcode' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_select_users_role_for_nominee_checkout' ] ) ) {
				update_option( 'rs_select_users_role_for_nominee_checkout' , wc_clean(wp_unslash($_REQUEST[ 'rs_select_users_role_for_nominee_checkout' ] ))) ;
			} else {
				update_option( 'rs_select_users_role_for_nominee_checkout' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_select_users_list_for_nominee_in_checkout' ] ) ) {
				update_option( 'rs_select_users_list_for_nominee_in_checkout' , wc_clean(wp_unslash($_REQUEST[ 'rs_select_users_list_for_nominee_in_checkout' ] ) ));
			} else {
				update_option( 'rs_select_users_list_for_nominee_in_checkout' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_nominee_module_checkbox' ] ) ) {
				update_option( 'rs_nominee_activated' , wc_clean(wp_unslash($_REQUEST[ 'rs_nominee_module_checkbox' ] ) ));
			} else {
				update_option( 'rs_nominee_activated' , 'no' ) ;
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

		public static function enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_nominee_activated' ) , 'rs_nominee_module_checkbox' , 'rs_nominee_activated') ;
		}

		/*
		 * Function to Select user as Nominee
		 */

		public static function rs_select_user_as_nominee() {
			$field_id    = 'rs_select_users_list_for_nominee' ;
			$field_label = __('User Selection' , 'rewardsystem');
			$getuser     = get_option( 'rs_select_users_list_for_nominee' ) ;
			echo wp_kses_post(user_selection_field( $field_id , $field_label , $getuser ) );
		}

		public static function rs_select_user_as_nominee_shortcode() {
			$field_id    = 'rs_select_users_list_for_nominee_shortcode' ;
			$field_label = __('User Selection' , 'rewardsystem');
			$getuser     = get_option( 'rs_select_users_list_for_nominee_shortcode' ) ;
			echo wp_kses_post(user_selection_field( $field_id , $field_label , $getuser ) );
		}

		public static function rs_select_user_as_nominee_in_checkout() {
			$field_id    = 'rs_select_users_list_for_nominee_in_checkout' ;
			$field_label = __('User Selection', 'rewardsystem') ;
			$getuser     = get_option( 'rs_select_users_list_for_nominee_in_checkout' ) ;
			echo wp_kses_post(user_selection_field( $field_id , $field_label , $getuser ) );
		}

		public static function rs_function_to_display_nominee_list_table() {
			$newwp_list_table_for_users = new WP_List_Table_For_Nominee() ;
			$newwp_list_table_for_users->prepare_items() ;
			$plugin_url                 = WP_PLUGIN_URL ;
			$newwp_list_table_for_users->display() ;
		}

		public static function reset_nominee_module() {
			$settings = self::reward_system_admin_fields() ;
			RSTabManagement::reset_settings( $settings ) ;
		}

	}

	RSNominee::init() ;
}
