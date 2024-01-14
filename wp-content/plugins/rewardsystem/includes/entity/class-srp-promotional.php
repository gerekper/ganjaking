<?php

/**
 * Promotional Points.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'SRP_Promotional' ) ) {

	/**
	 * SRP_Promotional Class.
	 */
	class SRP_Promotional extends SRP_Post {

		/**
		 * Post Type.
		 */
		protected $post_type = SRP_Register_Post_Type::PROMOTIONAL_POSTTYPE ;

		/**
		 * Post Status.
		 */
		protected $post_status = 'publish' ;
		
		/**
		 * Enable Rule.
		 */
		protected $srp_enable ;

		/**
		 * Promotional name.
		 */
		protected $srp_name ;

		/**
		 * Promotional Points Value.
		 */
		protected $srp_point ;

		/**
		 * From Date.
		 */
		protected $srp_from_date ;

		/**
		 * To Date.
		 */
		protected $srp_to_date ;

		/**
		 * Meta data keys
		 */
		protected $meta_data_keys = array(
			'srp_enable'      => '',
			'srp_name'      => '',
			'srp_point'     => '',
			'srp_from_date' => '',
			'srp_to_date'   => '',
				) ;

		/**
		 * Set Id.
		 */
		public function set_id( $value ) {

			$this->id = $value ;
		}
		
		/**
		 * Set Enable.
		 */
		public function set_enable( $value ) {

			$this->srp_enable = $value ;
		}

		/**
		 * Set Name.
		 */
		public function set_name( $value ) {

			$this->srp_name = $value ;
		}

		/**
		 * Set Point.
		 */
		public function set_point( $value ) {

			$this->srp_point = $value ;
		}

		/**
		 * Set From Date.
		 */
		public function set_from_date( $value ) {

			$this->srp_from_date = $value ;
		}

		/**
		 * Set To Date.
		 */
		public function set_to_date( $value ) {

			$this->srp_to_date = $value ;
		}

		/**
		 * Get Id.
		 */
		public function get_id() {

			return $this->id ;
		}
		
		/**
		 * Get Enable.
		 */
		public function get_enable() {

			return $this->srp_enable ;
		}

		/**
		 * Get Name.
		 */
		public function get_name() {

			return $this->srp_name ;
		}

		/**
		 * Get Point.
		 */
		public function get_point() {

			return $this->srp_point ;
		}

		/**
		 * Get From Date.
		 */
		public function get_from_date() {

			return $this->srp_from_date ;
		}

		/**
		 * Get To Date.
		 */
		public function get_to_date() {

			return $this->srp_to_date ;
		}
	}

}
