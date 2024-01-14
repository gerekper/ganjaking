<?php

/*
 * Buying Points Module Setting
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSBuyingPoints' ) ) {

	class RSBuyingPoints {

		public static function init() {

			add_action( 'woocommerce_rs_settings_tabs_fpbuyingpoints' , array( __CLASS__, 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

			add_action( 'woocommerce_update_options_fprsmodules_fpbuyingpoints' , array( __CLASS__, 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

			add_action( 'rs_default_settings_fpbuyingpoints' , array( __CLASS__, 'set_default_value' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_buyingpoints_module' , array( __CLASS__, 'enable_module' ) ) ;

			add_action( 'woocommerce_admin_field_rs_include_products_for_buying_points' , array( __CLASS__, 'rs_include_products_for_buying_points' ) ) ;

			add_action( 'woocommerce_admin_field_rs_exclude_products_for_buying_points' , array( __CLASS__, 'rs_exclude_products_for_buying_points' ) ) ;

			add_action( 'woocommerce_admin_field_rs_button_for_buying_points' , array( __CLASS__, 'rs_save_button_for_buying_points' ) ) ;
						
			if ( class_exists( 'SUMOSubscriptions' ) || class_exists( 'WC_Subscriptions' )) {
				add_filter( 'woocommerce_fpbuyingpoints_settings' , array( __CLASS__, 'render_subscription_settings' ) ) ;
			}
						
						add_action( 'fp_action_to_reset_module_settings_fpbuyingpoints' , array( __CLASS__, 'reset_buying_points_module' ) ) ;
						
						add_action( 'rs_display_save_button_fpbuyingpoints' , array( 'RSTabManagement', 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fpbuyingpoints' , array( 'RSTabManagement', 'rs_display_reset_button' ) ) ;
		}

		/*
		 * Function label settings to Member Level Tab
		 */

		public static function reward_system_admin_fields() {
			global $woocommerce ;
						/**
						 * Hook:woocommerce_fpbuyingpoints_settings.
						 * 
						 * @since 1.0
						 */
			return apply_filters( 'woocommerce_fpbuyingpoints_settings' , array(
				array(
					'type' => 'rs_modulecheck_start',
				),
				array(
					'name' => __( 'Buying Points Module' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_activate_buyingpoints_module',
				),
				array(
					'type' => 'rs_enable_buyingpoints_module',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_activate_buyingpoints_module' ),
				array(
					'type' => 'rs_modulecheck_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Buying Reward Points Bulk Update Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => 'rs_bulk_update_settings_for_buying_points',
				),
				array(
					'name'    => __( 'Product Selection' , 'rewardsystem' ),
					'id'      => 'rs_buying_points_is_applicable',
					'std'     => '1',
					'class'   => 'rs_buying_points_is_applicable',
					'default' => '1',
					'newids'  => 'rs_buying_points_is_applicable',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'All Product(s)' , 'rewardsystem' ),
						'2' => __( 'Include Product(s)' , 'rewardsystem' ),
						'3' => __( 'Exclude Product(s)' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'rs_include_products_for_buying_points',
				),
				array(
					'type' => 'rs_exclude_products_for_buying_points',
				),
				array(
					'name'    => __( 'Enable Buying of SUMO Reward Points' , 'rewardsystem'),
					'id'      => 'rs_enable_buying_points',
					'std'     => 'no',
					'class'   => 'rs_enable_buying_points',
					'default' => 'no',
					'newids'  => 'rs_enable_buying_points',
					'type'    => 'select',
					'options' => array(
						'yes' => __( 'Enable' , 'rewardsystem' ),
						'no'  => __( 'Disable' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Buy Reward Points' , 'rewardsystem' ),
					'id'      => 'rs_points_for_buying_points',
					'class'   => 'rs_points_for_buying_points',
					'std'     => '',
					'default' => '',
					'type'    => 'text',
					'newids'  => 'rs_points_for_buying_points',
				),
				array(
					'type' => 'rs_button_for_buying_points',
				),
				array( 'type' => 'sectionend', 'id' => 'rs_bulk_update_settings_for_buying_points' ),
				array(
					'type' => 'rs_wrapper_end',
				),
								array(
					'type' => 'rs_subscription_compatible_start',
				),
				array(
					'name' => __( 'Subscriptions Compatibility Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_subscription_settings',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_subscription_settings' ),
				array(
					'type' => 'rs_subscription_compatible_end',
				),
								array(
					'type' => 'rs_wrapper_start',
				),
								array( 'type' => 'sectionend', 'id' => 'rs_subscription_compatible_end' ),
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
		 * Reset buying points module
		 */
		public static function reset_buying_points_module() {
			$settings = self::reward_system_admin_fields() ;
			RSTabManagement::reset_settings( $settings ) ;
		}

		/**
		 * Update the Settings on Save Changes may happen in SUMO Reward Points
		 */
		public static function reward_system_update_settings() {
			woocommerce_update_options( self::reward_system_admin_fields() ) ;
			if ( isset( $_REQUEST[ 'rs_buyingpoints_module_checkbox' ] ) ) {
				update_option( 'rs_buyingpoints_activated' , wc_clean(wp_unslash($_REQUEST[ 'rs_buyingpoints_module_checkbox' ])) ) ;
			} else {
				update_option( 'rs_buyingpoints_activated' , 'no' ) ;
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
			RSModulesTab::checkbox_for_module( get_option( 'rs_buyingpoints_activated' ) , 'rs_buyingpoints_module_checkbox' , 'rs_buyingpoints_activated' ) ;
		}

		public static function rs_include_products_for_buying_points() {
			$field_id    = 'rs_include_products_for_buying_points' ;
			$field_label = 'Include Product(s)' ;
			$getproducts = get_option( 'rs_include_products_for_buying_points' ) ;
			echo do_shortcode(rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) );
		}

		public static function rs_exclude_products_for_buying_points() {
			$field_id    = 'rs_exclude_products_for_buying_points' ;
			$field_label = 'Exclude Product(s)' ;
			$getproducts = get_option( 'rs_exclude_products_for_buying_points' ) ;
			echo do_shortcode(rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) );
		}

		public static function rs_save_button_for_buying_points() {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row"></th>
				<td class="forminp forminp-select">
					<input type="button" class="rs_bulk_update_button_for_buying_points button-primary" value="<?php esc_html_e('Save and Update', 'rewardsystem'); ?>"/>
				</td>
			</tr>
			<?php
		}
				
		public static function render_subscription_settings( $settings ) {
					
				$updated_settings = array() ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && '_rs_subscription_settings' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
					if (class_exists( 'WC_Subscriptions' )) {
						$updated_settings[] = array(
								'type' => 'title',
								'id'   => 'rs_wc_subscription',
								'desc' => __('<h3>WooCommerce Subscriptions</h3><br><br>', 'rewardsystem'),
						) ;        
						$updated_settings[] = array(
						'name'   => __( 'Restrict Buying Reward Points for Renewal Orders' , 'rewardsystem' ),
						'id'     => 'rs_award_buying_point_wc_renewal_order',
						'std'    => 'no',
						'type'   => 'checkbox',
						'newids' => 'rs_award_buying_point_wc_renewal_order',
						) ;
						$updated_settings[] = array(
						'type' => 'sectionend',
						'id'   => 'rs_wc_subscription',
						) ;
					}
									
					if (class_exists( 'SUMOSubscriptions' )) {
						$updated_settings[] = array(
								'type' => 'title',
								'id'   => 'rs_sumo_subscription_buying',
								'desc' => __('<h3>SUMO Subscriptions</h3><br><br>', 'rewardsystem'),
						) ;        
						$updated_settings[] = array(
						'name'   => __( 'Restrict Buying Reward Points for Renewal Orders' , 'rewardsystem' ),
						'id'     => 'rs_award_buying_point_for_renewal_order',
						'std'    => 'no',
						'type'   => 'checkbox',
						'newids' => 'rs_award_buying_point_for_renewal_order',
						) ;
						$updated_settings[] = array(
						'type' => 'sectionend',
						'id'   => 'rs_sumo_subscription_buying',
						) ;
					}    
				}
				$updated_settings[] = $section ;
			}

			return $updated_settings ;
		}
	}

	RSBuyingPoints::init() ;
}
