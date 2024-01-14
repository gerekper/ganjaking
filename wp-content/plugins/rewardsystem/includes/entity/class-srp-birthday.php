<?php

/**
 * Birthday.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'SRP_Birthday' ) ) {

	/**
	 * SRP_Birthday Class.
	 */
	class SRP_Birthday extends SRP_Post {

		/**
		 * Post Type.
		 */
		protected $post_type = SRP_Register_Post_Type::BIRTHDAY_POSTTYPE ;

		/**
		 * Post Status.
		 */
		protected $post_status = 'publish' ;

		/**
		 * User Id.
		 */
		protected $srp_user_id ;

		/**
		 * User Id.
		 */
		protected $srp_user_email ;

		/**
		 * User Name.
		 */
		protected $srp_user_name ;

		/**
		 * Birthday Date.
		 */
		protected $srp_birthday_date ;

		/**
		 * Birthday Update on Date.
		 */
		protected $srp_birthday_updated_on ;

		/**
		 * Birthday Date/Month.
		 */
		protected $srp_birthday_month ;
				
				/**
		 * Points Issued Date.
		 */
		protected $srp_issued_date ;
				
		/**
		 * Points Issued Year.
		 */
		protected $srp_issued_year ;

		/**
		 * Last Points Issued Year.
		 */
		protected $srp_last_issued_year ;

		/**
		 * Meta data keys
		 */
		protected $meta_data_keys = array(
			'srp_user_id'             => '',
			'srp_user_email'          => '',
			'srp_user_name'           => '',
			'srp_birthday_date'       => '',
			'srp_birthday_month'      => '',
			'srp_birthday_updated_on' => '',
			'srp_issued_date'         => '',
			'srp_issued_year'         => array(),
			'srp_last_issued_year'    => '',
				) ;

		/**
		 * Set Id.
		 */
		public function set_id( $value ) {

			$this->id = $value ;
		}

		/**
		 * Set User Id.
		 */
		public function set_user_id( $value ) {

			$this->srp_user_id = $value ;
		}

		/**
		 * Set User Email.
		 */
		public function set_user_email( $value ) {

			$this->srp_user_email = $value ;
		}
		
		/**
		 * Set User Name.
		 */
		public function set_user_name( $value ) {

			$this->srp_user_name = $value ;
		}

		/**
		 * Set Birthday Date.
		 */
		public function set_birthday_date( $value ) {

			$this->srp_birthday_date = $value ;
		}

		/**
		 * Set Birthday Updated on.
		 */
		public function set_birthday_updated_on( $value ) {

			$this->srp_birthday_updated_on = $value ;
		}

		/**
		 * Set Birthday Date/Month.
		 */
		public function set_birthday_month( $value ) {

			$this->srp_birthday_month = $value ;
		}

		/**
		 * Set Issued Date.
		 */
		public function set_issued_date( $value ) {

			$this->srp_issued_date = $value ;
		}

		/**
		 * Set Issued Year.
		 */
		public function set_issued_year( $value ) {

			$this->srp_issued_year = $value ;
		}

		/**
		 * Set Last Issued Year.
		 */
		public function set_last_issued_year( $value ) {

			$this->srp_last_issued_year = $value ;
		}

		/**
		 * Get Id.
		 */
		public function get_id() {

			return $this->id ;
		}

		/**
		 * Get User Id.
		 */
		public function get_user_id() {

			return $this->srp_user_id ;
		}

		/**
		 * Get User Email.
		 */
		public function get_user_email() {

			return $this->srp_user_email ;
		}
		
		/**
		 * Get User Name.
		 */
		public function get_user_name() {

			return $this->srp_user_name ;
		}

		/**
		 * Get Birthday Date.
		 */
		public function get_birthday_date() {

			return $this->srp_birthday_date ;
		}

		/**
		 * Get Birthday Updated on.
		 */
		public function get_birthday_updated_on() {

			return $this->srp_birthday_updated_on ;
		}

		/**
		 * Get Birthday Date/Month.
		 */
		public function get_birthday_month() {

			return $this->srp_birthday_month ;
		}

		/**
		 * Get Issued Date.
		 */
		public function get_issued_date() {

			return $this->srp_issued_date ;
		}

		/**
		 * Get Issued Year.
		 */
		public function get_issued_year() {

			return $this->srp_issued_year ;
		}

		/**
		 * Get Last Issued Year.
		 */
		public function get_last_issued_year() {

			return $this->srp_last_issued_year ;
		}
	}

}
