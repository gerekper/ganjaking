<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'SRPBirthdayPoints' ) ) {

	class SRPBirthdayPoints {

		public static function init() {
			//Display Birthday Field in Register Form
			add_action( 'woocommerce_register_form' , array( __CLASS__ , 'birthday_field' ) ) ;
			//Display Birthday Field in Edit Account Form
			add_action( 'woocommerce_edit_account_form' , array( __CLASS__ , 'birthday_field' ) ) ;
		}

		/**
		 * Display Birthday Field in Register Field
		 */
		public static function birthday_field() {
			$birthday_date = get_user_meta( get_current_user_id() , 'srp_birthday_date' , true ) ;

			$args = array(
				'id'                => 'srp_birthday_date' ,
				'value'             => $birthday_date ,
				'custom_attributes' => empty( $birthday_date ) ? array() : array( 'readonly' => 'readonly' ) ,
					) ;

			$class_name = 'woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide' ;

			$bday_points = get_option( 'rs_bday_points' ) ;
			$label       = str_replace( '{birthday_points}' , $bday_points , get_option( 'rs_bday_field_reason_label' ) ) ;

			include SRP_PLUGIN_PATH . '/templates/birthday-field.php' ;
		}

	}

	SRPBirthdayPoints::init() ;
}
