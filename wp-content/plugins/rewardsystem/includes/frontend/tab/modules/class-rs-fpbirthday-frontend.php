<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'SRPBirthdayPoints' ) ) {

	class SRPBirthdayPoints {

		public static function init() {
			//Display Birthday Field in Register Form
			add_action( 'woocommerce_register_form' , array( __CLASS__, 'birthday_field' ) ) ;
			//Display Birthday Field in Edit Account Form
			add_action( 'woocommerce_edit_account_form' , array( __CLASS__, 'birthday_field' ) ) ;
			// Validate birthday field on registration page.
			add_filter( 'woocommerce_registration_errors' , array( __CLASS__, 'validate_birthday_field' ) , 12 , 1 ) ;
			// Validate birthday field on edit account page.
			add_action( 'woocommerce_save_account_details_errors' , array( __CLASS__, 'validate_birthday_field_edit_account' ) , 12 , 1 ) ;
			// Render birthday field in checkout page.
			add_filter('woocommerce_checkout_fields', array( __CLASS__, 'birthday_field_in_checkout' ));
		}

		/**
		 * Display Birthday Field in Register Field
		 */
		public static function birthday_field() {
			$birthday_date = get_user_meta( get_current_user_id() , 'srp_birthday_date' , true ) ;

			$args = array(
				'id'                => 'srp_birthday_date',
				'value'             => $birthday_date,
				'custom_attributes' => empty( $birthday_date ) ? array() : array( 'readonly' => 'readonly' ),
					) ;

			$class_name = 'woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide' ;

			$bday_points = get_option( 'rs_bday_points' ) ;
			$label       = str_replace( '{birthday_points}' , $bday_points , get_option( 'rs_bday_field_reason_label' ) ) ;

			include SRP_PLUGIN_PATH . '/templates/birthday-field.php' ;
		}
				
		/**
		 * Validate birthday date field.
		 */
		public static function validate_birthday_field( $errors ) {
										
			if ('yes' !=get_option('rs_bday_points_activated') || 'yes' != get_option('rs_enable_bday_points') || !get_option('rs_bday_points')) {
				return $errors;
			}
					
			if (!isset($_REQUEST['srp_birthday_date'])) {
				return $errors;
			}
					
			if ('yes' != get_option('rs_enable_bday_field_mandatory')) {
				return $errors;
			}
					
			if (!empty(wc_clean(wp_unslash($_REQUEST['srp_birthday_date'])))) {
				return $errors;
			}
					
			$errors->add('error', str_replace('{field_name}', get_option('rs_bday_field_label', 'DOB'), get_option('rs_bday_field_mandatory_error', '{field_name} field is mandatory')));
					
			return $errors;
		}
				
				/**
		 * Validate birthday date field in edit account page.
		 */
		public static function validate_birthday_field_edit_account( $errors ) {
					
			if ('yes' !=get_option('rs_bday_points_activated') || 'yes' != get_option('rs_enable_bday_points') || !get_option('rs_bday_points')) {
				return;
			}
					
			if (!isset($_REQUEST['srp_birthday_date'])) {
				return;
			}
					
			if ('yes' != get_option('rs_enable_bday_field_mandatory')) {
				return;
			}
					
			if (!empty(wc_clean(wp_unslash($_REQUEST['srp_birthday_date'])))) {
				return;
			}
					
			$errors->add('error', str_replace('{field_name}', get_option('rs_bday_field_label', 'DOB'), get_option('rs_bday_field_mandatory_error', '{field_name} field is mandatory')));
		}
				
				/**
		 * Render birthday field in checkout page.
		 */
		public static function birthday_field_in_checkout( $fields ) {
					
			if (get_current_user_id()) {
					return $fields;
			}
															
				$fields['account']['srp_birthday_date'] = array(
					'type'         => 'date',
					'label'        => get_option('rs_bday_field_label'),
					'required'     => 'yes' == get_option('rs_enable_bday_field_mandatory'),
							);
																		
							return $fields;
		}
	}

	SRPBirthdayPoints::init() ;
}
