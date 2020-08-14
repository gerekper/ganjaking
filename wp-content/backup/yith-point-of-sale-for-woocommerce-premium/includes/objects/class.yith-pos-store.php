<?php
// Exit if accessed directly
! defined( 'YITH_POS' ) && exit();

if ( ! class_exists( 'YITH_POS_Store' ) ) {
	class YITH_POS_Store extends YITH_POS_CPT_Object {
		/** @var array */
		protected $data = array(
			'name'          => '',
			'vat_number'    => '',
			'address'       => '',
			'city'          => '',
			'country_state' => '',
			'postcode'      => '',
			'phone'         => '',
			'fax'           => '',
			'email'         => '',
			'website'       => '',
			'facebook'      => '',
			'twitter'       => '',
			'instagram'     => '',
			'youtube'       => '',
			'managers'      => array(),
			'cashiers'      => array(),
			'enabled'       => 'yes',
		);

		/** @var string */
		protected $object_type = 'store';

		/** @var string */
		protected $post_type = 'yith-pos-store';

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from object.
		*/

		/**
		 * Return the name of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			return $this->get_prop( 'name', $context );
		}

		/**
		 * Return the vat number of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_vat_number( $context = 'view' ) {
			return $this->get_prop( 'vat_number', $context );
		}

		/**
		 * Return the address of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_address( $context = 'view' ) {
			return $this->get_prop( 'address', $context );
		}

		/**
		 * Return the city of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_city( $context = 'view' ) {
			return $this->get_prop( 'city', $context );
		}

		/**
		 * Return the country_state of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_country_state( $context = 'view' ) {
			return $this->get_prop( 'country_state', $context );
		}

		/**
		 * Return the postcode of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_postcode( $context = 'view' ) {
			return $this->get_prop( 'postcode', $context );
		}

		/**
		 * Return the phone of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_phone( $context = 'view' ) {
			return $this->get_prop( 'phone', $context );
		}

		/**
		 * Return the fax of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_fax( $context = 'view' ) {
			return $this->get_prop( 'fax', $context );
		}

		/**
		 * Return the email of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_email( $context = 'view' ) {
			return $this->get_prop( 'email', $context );
		}

		/**
		 * Return the website of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_website( $context = 'view' ) {
			return $this->get_prop( 'website', $context );
		}

		/**
		 * Return the facebook of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_facebook( $context = 'view' ) {
			return $this->get_prop( 'facebook', $context );
		}

		/**
		 * Return the twitter of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_twitter( $context = 'view' ) {
			return $this->get_prop( 'twitter', $context );
		}

		/**
		 * Return the instagram of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_instagram( $context = 'view' ) {
			return $this->get_prop( 'instagram', $context );
		}

		/**
		 * Return the youtube of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_youtube( $context = 'view' ) {
			return $this->get_prop( 'youtube', $context );
		}

		/**
		 * Return the managers of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_managers( $context = 'view' ) {
			return $this->get_prop( 'managers', $context );
		}

		/**
		 * Return the cashiers of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_cashiers( $context = 'view' ) {
			return $this->get_prop( 'cashiers', $context );
		}

		/**
		 * Return the "enabled" status of the Store
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_enabled( $context = 'view' ) {
			return $this->get_prop( 'enabled', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		|
		| Functions for setting object data. These should not update anything in the
		| database itself and should only change what is stored in the class
		| object.
		*/

		/**
		 * Set the name of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_name( $value ) {
			$this->set_prop( 'name', $value );
		}

		/**
		 * Set the vat_number of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_vat_number( $value ) {
			$this->set_prop( 'vat_number', $value );
		}

		/**
		 * Set the address of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_address( $value ) {
			$this->set_prop( 'address', $value );
		}

		/**
		 * Set the city of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_city( $value ) {
			$this->set_prop( 'city', $value );
		}

		/**
		 * Set the country_state of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_country_state( $value ) {
			$this->set_prop( 'country_state', $value );
		}

		/**
		 * Set the postcode of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_postcode( $value ) {
			$this->set_prop( 'postcode', $value );
		}

		/**
		 * Set the phone of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_phone( $value ) {
			$this->set_prop( 'phone', $value );
		}

		/**
		 * Set the fax of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_fax( $value ) {
			$this->set_prop( 'fax', $value );
		}

		/**
		 * Set the email of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_email( $value ) {
			$this->set_prop( 'email', $value );
		}

		/**
		 * Set the website of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_website( $value ) {
			$this->set_prop( 'website', $value );
		}

		/**
		 * Set the facebook of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_facebook( $value ) {
			$this->set_prop( 'facebook', $value );
		}

		/**
		 * Set the twitter of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_twitter( $value ) {
			$this->set_prop( 'twitter', $value );
		}

		/**
		 * Set the instagram of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_instagram( $value ) {
			$this->set_prop( 'instagram', $value );
		}

		/**
		 * Set the youtube of the Store
		 *
		 * @param string $value the value to set
		 */
		public function set_youtube( $value ) {
			$this->set_prop( 'youtube', $value );
		}

		/**
		 * Set the managers of the Store
		 *
		 * @param array $value the value to set
		 */
		public function set_managers( $value ) {
			$this->set_prop( 'managers', array_filter( array_unique( array_map( 'absint', $value ) ) ) );
		}

		/**
		 * Set the cashiers of the Store
		 *
		 * @param array $value the value to set
		 */
		public function set_cashiers( $value ) {
			$this->set_prop( 'cashiers', array_filter( array_unique( array_map( 'absint', $value ) ) ) );
		}

		/**
		 * Set the "enabled" status of the Store
		 *
		 * @param bool $value the value to set
		 */
		public function set_enabled( $value ) {
			$this->set_prop( 'enabled', wc_bool_to_string( $value ) );
		}

		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		*/

		/**
		 * is published?
		 *
		 * @return bool
		 */
		public function is_published() {
			return 'publish' === $this->get_post_status();
		}

		/**
		 * is enabled?
		 *
		 * @return bool
		 */
		public function is_enabled() {
			return 'yes' === $this->get_enabled() && $this->is_published();
		}

		/*
		|--------------------------------------------------------------------------
		| Non-CRUD Getters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Return the registers of the Store
		 *
		 * @param array $args
		 *
		 * @return array
		 */
		public function get_register_ids( $args = array() ) {
			$args[ 'fields' ] = 'ids';

			return yith_pos_get_registers_by_store( $this->get_id(), $args );
		}


		/**
		 * Return the country code
		 *
		 * @return string
		 */
		public function get_country() {
			$location = wc_format_country_state_string( $this->get_country_state() );

			return $location[ 'country' ];
		}

		/**
		 * Return the state
		 *
		 * @return string
		 */
		public function get_state() {
			$location = wc_format_country_state_string( $this->get_country_state() );

			return $location[ 'state' ];
		}

		/**
		 * return the address of the store formatted
		 *
		 * @return string
		 */
		public function get_formatted_address() {
			$formatted_address = WC()->countries->get_formatted_address( array(
				                                                             'address_1' => $this->get_address(),
				                                                             'city'      => $this->get_city(),
				                                                             'state'     => $this->get_state(),
				                                                             'postcode'  => $this->get_postcode(),
				                                                             'country'   => $this->get_country(),
			                                                             ) );

			return apply_filters( 'yith_pos_store_get_formatted_address', $formatted_address, $this );
		}

		/*
		|--------------------------------------------------------------------------
		| Utilities
		|--------------------------------------------------------------------------
		*/

		/**
		 * Delete all registers of the store
		 */
		public function delete_all_registers() {
			$register_ids = $this->get_register_ids( array( 'post_status' => array( 'publish', 'draft', 'trash', 'auto-draft' ) ) );
			foreach ( $register_ids as $id ) {
				wp_delete_post( $id );
			}
		}
	}
}

if ( ! function_exists( 'yith_pos_get_store' ) ) {
	function yith_pos_get_store( $store ) {
		$the_store = new YITH_POS_Store( $store );

		return $the_store->is_valid() ? $the_store : false;
	}
}