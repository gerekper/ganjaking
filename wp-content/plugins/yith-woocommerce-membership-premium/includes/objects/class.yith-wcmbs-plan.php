<?php
! defined( 'ABSPATH' ) && exit(); // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Plan' ) ) {
	class YITH_WCMBS_Plan extends YITH_WCMBS_CPT_Object {
		/** @var string */
		protected $object_type = 'plan';

		/**
		 * The version of the object.
		 * This is useful to force saving all metas when changing version.
		 *
		 * @var string
		 */
		protected $object_version = '1.4.0';

		/** @var string */
		protected $post_type = 'yith-wcmbs-plan';

		protected $props_to_meta_keys = array(
			'post_categories'                              => '_post-cats',
			'product_categories'                           => '_product-cats',
			'post_tags'                                    => '_post-tags',
			'product_tags'                                 => '_product-tags',
			'enable_purchasing'                            => '_enable_purchasing',
			'target_products'                              => '_membership-product',
			'duration_enabled'                             => '_membership-duration-enabled',
			'duration'                                     => '_membership-duration',
			'linked_plans_enabled'                         => '_linked-plans-enabled',
			'linked_plans'                                 => '_linked-plans',
			'show_contents_in_membership_details'          => '_show-contents-in-my-account',
			'download_number_first_term'                   => '_initial-download-limit',
			'different_download_number_first_term_enabled' => '_initial-download-limit-enabled',
			'download_number'                              => '_download-limit',
			'download_term_duration'                       => '_download-limit-period',
			'download_term_unit'                           => '_download-limit-period-unit',
			'can_credits_be_accumulated'                   => '_can-be-accumulated',
			'download_limit_type'                          => '_download_limit_type',
			'discount_enabled'                             => '_discount_enabled',
			'discount'                                     => '_discount',
			'post_sorting'                                 => '_post_sorting',
			'page_sorting'                                 => '_page_sorting',
			'product_sorting'                              => '_product_sorting',
			'posts_to_include'                             => '_posts_to_include',
			'products_to_include'                          => '_products_to_include',
		);

		/** @var array */
		protected $data = array(
			'name'                                         => '',
			'post_categories'                              => array(),
			'product_categories'                           => array(),
			'post_tags'                                    => array(),
			'product_tags'                                 => array(),
			'enable_purchasing'                            => 'yes',
			'target_products'                              => array(),
			'duration_enabled'                             => 'yes',
			'duration'                                     => 0,
			'linked_plans_enabled'                         => 'yes',
			'linked_plans'                                 => array(),
			'show_contents_in_membership_details'          => 'no',
			'download_number_first_term'                   => - 1,
			'different_download_number_first_term_enabled' => 'no',
			'download_number'                              => 0,
			'download_term_duration'                       => 1,
			'download_term_unit'                           => 'days',
			'can_credits_be_accumulated'                   => 'no',
			'download_limit_type'                          => 'no',
			'discount_enabled'                             => 'no',
			'discount'                                     => 0,
			'post_sorting'                                 => 'name-asc',
			'page_sorting'                                 => 'name-asc',
			'product_sorting'                              => 'name-asc',
			'posts_to_include'                             => 'specific',
			'products_to_include'                          => 'specific',
		);

		/** @var array */
		protected $extra_data = array(
			'posts'    => array(),
			'pages'    => array(),
			'products' => array(),
		);

		/** @var array */
		protected $int_to_string_array_props = array(
			'linked_plans',
		);

		/** @var array */
		protected $boolean_props = array(
			'enable_purchasing',
			'duration_enabled',
			'linked_plans_enabled',
			'show_contents_in_membership_details',
			'different_download_number_first_term_enabled',
			'can_credits_be_accumulated',
			'download_limit_type',
			'discount_enabled',
		);

		/*
        |--------------------------------------------------------------------------
        | Getters
        |--------------------------------------------------------------------------
        |
        | Methods for getting data from object.
        */

		/**
		 * Return the name
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			return $this->get_prop( 'name', $context );
		}

		/**
		 * Return the post_categories
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_post_categories( $context = 'view' ) {
			return $this->get_prop( 'post_categories', $context );
		}

		/**
		 * Return the product_categories
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_product_categories( $context = 'view' ) {
			return $this->get_prop( 'product_categories', $context );
		}

		/**
		 * Return the post_tags
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_post_tags( $context = 'view' ) {
			return $this->get_prop( 'post_tags', $context );
		}

		/**
		 * Return the product_tags
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_product_tags( $context = 'view' ) {
			return $this->get_prop( 'product_tags', $context );
		}

		/**
		 * Return the posts
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_posts( $context = 'view' ) {
			return $this->get_prop( 'posts', $context );
		}

		/**
		 * Return the pages
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_pages( $context = 'view' ) {
			return $this->get_prop( 'pages', $context );
		}

		/**
		 * Return the products
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_products( $context = 'view' ) {
			return $this->get_prop( 'products', $context );
		}

		/**
		 * Return the enable_purchasing
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_enable_purchasing( $context = 'view' ) {
			return $this->get_prop( 'enable_purchasing', $context );
		}

		/**
		 * Return the target_products
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int[]
		 */
		public function get_target_products( $context = 'view' ) {
			return $this->get_prop( 'target_products', $context );
		}

		/**
		 * Return the duration_enabled
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_duration_enabled( $context = 'view' ) {
			return $this->get_prop( 'duration_enabled', $context );
		}

		/**
		 * Return the duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_duration( $context = 'view' ) {
			return $this->get_prop( 'duration', $context );
		}

		/**
		 * Return the linked_plans
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_linked_plans( $context = 'view' ) {
			return $this->get_prop( 'linked_plans', $context );
		}

		/**
		 * Return the linked_plans_enabled
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_linked_plans_enabled( $context = 'view' ) {
			return $this->get_prop( 'linked_plans_enabled', $context );
		}

		/**
		 * Return the show_contents_in_membership_details
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_show_contents_in_membership_details( $context = 'view' ) {
			return $this->get_prop( 'show_contents_in_membership_details', $context );
		}

		/**
		 * Return the download_number_first_term
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_download_number_first_term( $context = 'view' ) {
			return $this->get_prop( 'download_number_first_term', $context );
		}

		/**
		 * Return the different_download_number_first_term_enabled
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_different_download_number_first_term_enabled( $context = 'view' ) {
			return $this->get_prop( 'different_download_number_first_term_enabled', $context );
		}

		/**
		 * Return the download_number
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_download_number( $context = 'view' ) {
			return $this->get_prop( 'download_number', $context );
		}

		/**
		 * Return the download_term_duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_download_term_duration( $context = 'view' ) {
			return $this->get_prop( 'download_term_duration', $context );
		}

		/**
		 * Return the download_term_unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_download_term_unit( $context = 'view' ) {
			return $this->get_prop( 'download_term_unit', $context );
		}

		/**
		 * Return the can_credits_be_accumulated
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_can_credits_be_accumulated( $context = 'view' ) {
			return $this->get_prop( 'can_credits_be_accumulated', $context );
		}

		/**
		 * Return the download_limit_type
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_download_limit_type( $context = 'view' ) {
			return $this->get_prop( 'download_limit_type', $context );
		}

		/**
		 * Return the discount_enabled
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_discount_enabled( $context = 'view' ) {
			return $this->get_prop( 'discount_enabled', $context );
		}


		/**
		 * Return the discount
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_discount( $context = 'view' ) {
			return $this->get_prop( 'discount', $context );
		}


		/**
		 * Return the post_sorting
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_post_sorting( $context = 'view' ) {
			return $this->get_prop( 'post_sorting', $context );
		}

		/**
		 * Return the page_sorting
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_page_sorting( $context = 'view' ) {
			return $this->get_prop( 'page_sorting', $context );
		}

		/**
		 * Return the product_sorting
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_product_sorting( $context = 'view' ) {
			return $this->get_prop( 'product_sorting', $context );
		}

		/**
		 * Return the posts_to_include
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_posts_to_include( $context = 'view' ) {
			return $this->get_prop( 'posts_to_include', $context );
		}

		/**
		 * Return the products_to_include
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_products_to_include( $context = 'view' ) {
			return $this->get_prop( 'products_to_include', $context );
		}


		/*
        |--------------------------------------------------------------------------
        | Setters
        |--------------------------------------------------------------------------
        |
        | Methods for setting object data.
        */

		/**
		 * Set the name
		 *
		 * @param string $value the value to set
		 */
		public function set_name( $value ) {
			$this->set_prop( 'name', $value );
		}

		/**
		 * Set the post_categories
		 *
		 * @param string $value the value to set
		 */
		public function set_post_categories( $value ) {
			$this->set_prop( 'post_categories', $value );
		}

		/**
		 * Set the product_categories
		 *
		 * @param string $value the value to set
		 */
		public function set_product_categories( $value ) {
			$this->set_prop( 'product_categories', $value );
		}

		/**
		 * Set the post_tags
		 *
		 * @param string $value the value to set
		 */
		public function set_post_tags( $value ) {
			$this->set_prop( 'post_tags', $value );
		}

		/**
		 * Set the product_tags
		 *
		 * @param string $value the value to set
		 */
		public function set_product_tags( $value ) {
			$this->set_prop( 'product_tags', $value );
		}

		/**
		 * Set the posts
		 *
		 * @param array $value the value to set
		 */
		public function set_posts( $value ) {
			$value = ! ! $value && is_array( $value ) ? $value : array();
			$value = array_filter( array_map( 'absint', $value ) );
			$this->set_prop( 'posts', $value );
		}

		/**
		 * Set the pages
		 *
		 * @param array $value the value to set
		 */
		public function set_pages( $value ) {
			$value = ! ! $value && is_array( $value ) ? $value : array();
			$value = array_filter( array_map( 'absint', $value ) );
			$this->set_prop( 'pages', $value );
		}

		/**
		 * Set the products
		 *
		 * @param array $value the value to set
		 */
		public function set_products( $value ) {
			$value = ! ! $value && is_array( $value ) ? $value : array();
			$value = array_filter( array_map( 'absint', $value ) );
			$this->set_prop( 'products', $value );
		}

		/**
		 * Set the enable_purchasing
		 *
		 * @param string $value the value to set
		 */
		public function set_enable_purchasing( $value ) {
			$this->set_prop( 'enable_purchasing', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the target_products
		 *
		 * @param array|string $value the value to set.
		 */
		public function set_target_products( $value ) {
			if ( ! $value ) {
				$value = array();
			} elseif ( is_string( $value ) ) {
				$value = explode( ',', $value );
			} elseif ( ! is_array( $value ) ) {
				$value = (array) $value;
			}

			$value = array_map( 'absint', $value );

			$this->set_prop( 'target_products', $value );
		}

		/**
		 * Set the duration_enabled
		 *
		 * @param string $value the value to set
		 */
		public function set_duration_enabled( $value ) {
			$this->set_prop( 'duration_enabled', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the duration
		 *
		 * @param string $value the value to set
		 */
		public function set_duration( $value ) {
			$this->set_prop( 'duration', $value );
		}

		/**
		 * Set the linked_plans
		 *
		 * @param array $value the value to set
		 */
		public function set_linked_plans( $value ) {
			$value = ! ! $value && is_array( $value ) ? $value : array( $value );
			$value = array_filter( array_map( 'absint', $value ) );
			$value = array_diff( $value, array( $this->get_id() ) );
			$this->set_prop( 'linked_plans', $value );
		}

		/**
		 * Set the linked_plans_enabled
		 *
		 * @param string|bool $value the value to set
		 */
		public function set_linked_plans_enabled( $value ) {
			$this->set_prop( 'linked_plans_enabled', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the show_contents_in_membership_details
		 *
		 * @param string $value the value to set
		 */
		public function set_show_contents_in_membership_details( $value ) {
			$this->set_prop( 'show_contents_in_membership_details', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the download_number_first_term
		 *
		 * @param string $value the value to set
		 */
		public function set_download_number_first_term( $value ) {
			$this->set_prop( 'download_number_first_term', $value );
		}

		/**
		 * Set the different_download_number_first_term_enabled
		 *
		 * @param string $value the value to set
		 */
		public function set_different_download_number_first_term_enabled( $value ) {
			$this->set_prop( 'different_download_number_first_term_enabled', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the download_number
		 *
		 * @param string $value the value to set
		 */
		public function set_download_number( $value ) {
			$this->set_prop( 'download_number', $value );
		}

		/**
		 * Set the download_term_duration
		 *
		 * @param string $value the value to set
		 */
		public function set_download_term_duration( $value ) {
			$this->set_prop( 'download_term_duration', $value );
		}

		/**
		 * Set the download_term_unit
		 *
		 * @param string $value the value to set
		 */
		public function set_download_term_unit( $value ) {
			$this->set_prop( 'download_term_unit', $value );
		}

		/**
		 * Set the can_credits_be_accumulated
		 *
		 * @param string $value the value to set
		 */
		public function set_can_credits_be_accumulated( $value ) {
			$this->set_prop( 'can_credits_be_accumulated', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the download_limit_type
		 *
		 * @param string $value the value to set
		 */
		public function set_download_limit_type( $value ) {
			$value = in_array( $value, array( 'no', 'credits' ) ) ? $value : 'no';
			$this->set_prop( 'download_limit_type', $value );
		}

		/**
		 * Set the discount_enabled
		 *
		 * @param string $value the value to set
		 */
		public function set_discount_enabled( $value ) {
			$this->set_prop( 'discount_enabled', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the discount
		 *
		 * @param int $value the value to set
		 */
		public function set_discount( $value ) {
			$this->set_prop( 'discount', absint( $value ) );
		}

		/**
		 * Set the post_sorting
		 *
		 * @param string $value the value to set
		 */
		public function set_post_sorting( $value ) {
			$allowed_values = array( 'name-asc', 'name-desc', 'date-asc', 'date-desc' );
			if ( in_array( $value, $allowed_values ) ) {
				$this->set_prop( 'post_sorting', $value );
			}
		}

		/**
		 * Set the page_sorting
		 *
		 * @param string $value the value to set
		 */
		public function set_page_sorting( $value ) {
			$allowed_values = array( 'name-asc', 'name-desc', 'date-asc', 'date-desc' );
			if ( in_array( $value, $allowed_values ) ) {
				$this->set_prop( 'page_sorting', $value );
			}
		}

		/**
		 * Set the product_sorting
		 *
		 * @param string $value the value to set
		 */
		public function set_product_sorting( $value ) {
			$allowed_values = array( 'name-asc', 'name-desc', 'date-asc', 'date-desc' );
			if ( in_array( $value, $allowed_values ) ) {
				$this->set_prop( 'product_sorting', $value );
			}
		}

		/**
		 * Set the posts_to_include
		 *
		 * @param string $value the value to set
		 */
		public function set_posts_to_include( $value ) {
			$allowed_values = array( 'all', 'specific' );
			if ( in_array( $value, $allowed_values ) ) {
				$this->set_prop( 'posts_to_include', $value );
			}
		}

		/**
		 * Set the products_to_include
		 *
		 * @param string $value the value to set
		 */
		public function set_products_to_include( $value ) {
			$allowed_values = array( 'all', 'specific' );
			if ( in_array( $value, $allowed_values ) ) {
				$this->set_prop( 'products_to_include', $value );
			}
		}


		/*
        |--------------------------------------------------------------------------
        | Conditionals
        |--------------------------------------------------------------------------
        |
        */

		/**
		 * is the plan unlimited?
		 *
		 * @return bool
		 */
		public function is_unlimited() {
			return 'no' === $this->get_duration_enabled() || ! $this->get_duration();
		}

		/**
		 * is the plan purchasable?
		 *
		 * @return bool
		 */
		public function is_purchasable() {
			return 'yes' === $this->get_enable_purchasing();
		}

		/**
		 * is the different download number for the first term enabled?
		 *
		 * @return bool
		 */
		public function is_different_download_number_first_term_enabled() {
			return 'yes' === $this->get_different_download_number_first_term_enabled();
		}

		/**
		 * Does the plan have credits?
		 *
		 * @return bool
		 */
		public function has_credits() {
			return 'credits' === $this->get_download_limit_type();
		}

		/**
		 * Does the plan have linked plans?
		 *
		 * @return bool
		 */
		public function has_linked_plans_enabled() {
			return 'yes' === $this->get_linked_plans_enabled();
		}

		/**
		 * Show contents in history?
		 *
		 * @return bool
		 */
		public function show_contents_in_membership_details() {
			return 'yes' === $this->get_show_contents_in_membership_details();
		}

		/**
		 * Can credits be accumulated?
		 *
		 * @return bool
		 */
		public function can_credits_be_accumulated() {
			return 'yes' === $this->get_can_credits_be_accumulated();
		}

		/**
		 * Does the plan has discount?
		 *
		 * @return bool
		 */
		public function has_discount() {
			return 'yes' === $this->get_discount_enabled() && $this->get_discount();
		}


		/*
        |--------------------------------------------------------------------------
        | Others
        |--------------------------------------------------------------------------
        |
        */

		/**
		 * Validate props to ensure coherence
		 */
		protected function validate_props() {
			if ( ! $this->get_target_products( 'edit' ) ) {
				$this->set_enable_purchasing( false );
			}

			if ( ! $this->get_duration( 'edit' ) ) {
				$this->set_duration_enabled( false );
			}

			if ( ! $this->get_download_number( 'edit' ) ) {
				$this->set_download_limit_type( 'no' );
			}

			if ( $this->get_download_number_first_term( 'edit' ) < 0 ) { // backward compatibility < 1.4.0!
				$this->set_download_number_first_term( 0 );
				$this->set_different_download_number_first_term_enabled( false );
			}

			if ( ! $this->get_linked_plans( 'edit' ) ) { // backward compatibility < 1.4.0!
				$this->set_linked_plans_enabled( false );
			}

			if ( ! $this->has_linked_plans_enabled() ) {
				/**
				 * If linked plans are disabled, empty the linked plans array to make queries for '_linked-plans' meta working fine!
				 *
				 * @see yith_wcmbs_get_plans_including_all_posts()
				 */
				$this->set_linked_plans( array() );
			}
		}

		/*
        |--------------------------------------------------------------------------
        | Extra Data
        |--------------------------------------------------------------------------
        |
        */

		/**
		 * Update extra data to DB.
		 *
		 * @param string $prop
		 * @param mixed  $value
		 */
		protected function update_extra_prop( $prop, $value ) {
			$setter = 'set_' . $prop . '_extra_data';
			if ( is_callable( array( $this, $setter ) ) ) {
				$this->$setter( $value );
			}
		}

		/**
		 * Retrieve extra data from DB.
		 *
		 * @param string $prop
		 * @param mixed  $default
		 *
		 * @return mixed
		 */
		protected function get_extra_data_from_db( $prop, $default ) {
			$value  = false;
			$getter = 'get_' . $prop . '_extra_data';
			if ( is_callable( array( $this, $getter ) ) ) {
				$value = $this->$getter();
			}

			return $value;
		}

		/**
		 * Return ids of specific assigned posts
		 *
		 * @return int[]
		 */
		protected function get_posts_extra_data() {
			return $this->get_assigned_items( 'post' );
		}

		/**
		 * Return ids of specific assigned pages
		 *
		 * @return int[]
		 */
		protected function get_pages_extra_data() {
			return $this->get_assigned_items( 'page' );
		}

		/**
		 * Return ids of specific assigned products
		 *
		 * @return int[]
		 */
		protected function get_products_extra_data() {
			return $this->get_assigned_items( 'product' );
		}

		/**
		 * Return ids of specific assigned posts
		 *
		 * @param int[] $values The values
		 *
		 * @return void
		 */
		protected function set_posts_extra_data( $values ) {
			$this->update_assigned_items( 'post', $values );
		}

		/**
		 * Return ids of specific assigned pages
		 *
		 * @param int[] $values The values
		 *
		 * @return void
		 */
		protected function set_pages_extra_data( $values ) {
			$this->update_assigned_items( 'page', $values );
		}

		/**
		 * Return ids of specific assigned products
		 *
		 * @param int[] $values The values
		 *
		 * @return void
		 */
		protected function set_products_extra_data( $values ) {
			$this->update_assigned_items( 'product', $values );
		}

		/**
		 * Return ids of specific assigned items (posts, pages, products)
		 *
		 * @param string $post_type
		 * @param array  $args
		 *
		 * @return int[]
		 */
		protected function get_assigned_items( $post_type, $args = array() ) {
			$defaults = array(
				'posts_per_page'             => - 1,
				'post_type'                  => $post_type,
				'fields'                     => 'ids',
				'yith_wcmbs_suppress_filter' => true,
				'meta_query'                 => array(
					array(
						'key'     => '_yith_wcmbs_restrict_access_plan',
						'value'   => serialize( (string) $this->get_id() ),
						'compare' => 'LIKE',
					),
				),
			);

			$args = wp_parse_args( $args, $defaults );

			$items = get_posts( $args );

			return ! ! $items ? $items : array();
		}

		/**
		 * Update specific assigned items (posts, pages, products)
		 *
		 * @param string $post_type
		 * @param array  $values
		 */
		protected function update_assigned_items( $post_type, $values ) {
			$prev_value = $this->get_assigned_items( $post_type );

			$to_add    = array_diff( $values, $prev_value );
			$to_remove = array_diff( $prev_value, $values );

			foreach ( $to_add as $id ) {
				$plans = yith_wcmbs_get_plans_meta_for_post( $id );
				$plans = array_unique( array_merge( $plans, array( $this->get_id() ) ) );
				yith_wcmbs_update_plans_meta_for_post( $id, $plans );
			}

			foreach ( $to_remove as $id ) {
				$plans = yith_wcmbs_get_plans_meta_for_post( $id );
				$plans = array_unique( array_diff( $plans, array( $this->get_id() ) ) );
				yith_wcmbs_update_plans_meta_for_post( $id, $plans );
			}
		}

		/*
        |--------------------------------------------------------------------------
        | Non-CRUD Getters
        |--------------------------------------------------------------------------
        |
        */
		public function get_post_sorting_order() {
			list( $order_by, $order ) = explode( '-', $this->get_post_sorting(), 2 );

			return $order_by;
		}

		public function get_post_sorting_order_by() {
			list( $order_by, $order ) = explode( '-', $this->get_post_sorting(), 2 );

			return strtoupper( $order ) === 'ASC' ? 'ASC' : 'DESC';
		}

		public function get_page_sorting_order() {
			list( $order_by, $order ) = explode( '-', $this->get_page_sorting(), 2 );

			return $order_by;
		}

		public function get_page_sorting_order_by() {
			list( $order_by, $order ) = explode( '-', $this->get_page_sorting(), 2 );

			return strtoupper( $order ) === 'ASC' ? 'ASC' : 'DESC';
		}

		public function get_product_sorting_order() {
			list( $order_by, $order ) = explode( '-', $this->get_product_sorting(), 2 );

			return $order_by;
		}

		public function get_product_sorting_order_by() {
			list( $order_by, $order ) = explode( '-', $this->get_product_sorting(), 2 );

			return strtoupper( $order ) === 'ASC' ? 'ASC' : 'DESC';
		}

		/**
		 * Get the ids of items that are in this plan
		 *
		 * @param array $args
		 *
		 * @return array
		 */
		public function get_restricted_item_ids( $args = array() ) {
			$default_args = array(
				'include_products' => false,
				'include_media'    => true,
				'parse_by_delay'   => false,
				'membership'       => false,
				'include_linked'   => true,
				'exclude_hidden'   => false,
			);

			$args             = wp_parse_args( $args, $default_args );
			$include_products = $args['include_products'];
			$include_media    = $args['include_media'];
			$include_linked   = $args['include_linked'];
			$exclude_hidden   = $args['exclude_hidden'];
			$parse_by_delay   = $args['parse_by_delay'];
			$membership       = $args['membership'];

			$parse_by_delay = apply_filters( 'yith_wcmbs_get_restricted_items_in_plan_parse_by_delay', $parse_by_delay, $this->get_id(), $args );
			$exclude_hidden = apply_filters( 'yith_wcmbs_get_restricted_items_in_plan_exclude_hidden', $exclude_hidden, $this->get_id(), $args );

			if ( $parse_by_delay && $membership ) {
				$membership = yith_wcmbs_get_membership( $membership );
			}

			$restricted_post_types = YITH_WCMBS_Manager()->post_types;

			if ( ! $include_media ) {
				$restricted_post_types = array_diff( $restricted_post_types, array( 'attachment' ) );
			}

			if ( ! $include_products ) {
				$restricted_post_types = array_diff( $restricted_post_types, array( 'product' ) );
			}

			$plan_items = array();

			foreach ( $restricted_post_types as $post_type ) {
				$current_items = $this->get_included_items( $post_type, array( 'include_linked' => $include_linked, 'include_all' => false ) );
				$plan_items    = array_unique( array_merge( $plan_items, $current_items ) );
			}

			if ( $parse_by_delay && $membership ) {
				$plan_items = array_filter( $plan_items, function ( $item_id ) use ( $membership ) {
					return $membership->has_access_without_delay( $item_id );
				} );
			}

			return apply_filters( 'yith_wcmbs_get_restricted_items_in_plan', $plan_items, $this->get_id(), $args );
		}


		/**
		 * Return products with specific meta set
		 *
		 * @return array
		 */
		public function get_specific_products_in_plan() {
			return $this->get_products_extra_data();
		}

		/**
		 * Are all posts included?
		 *
		 * @return bool
		 */
		public function are_all_posts_included() {
			$all_included = 'all' === $this->get_posts_to_include();
			if ( ! $all_included ) {
				$linked_plans = $this->get_linked_plans();
				if ( $linked_plans ) {
					foreach ( $linked_plans as $linked_plan ) {
						$linked_plan = yith_wcmbs_get_plan( $linked_plan );
						if ( $linked_plan && 'all' === $linked_plan->get_posts_to_include() ) {
							$all_included = true;
							break;
						}
					}
				}
			}

			return $all_included;
		}

		/**
		 * Are all products included?
		 *
		 * @return bool
		 */
		public function are_all_products_included() {
			$all_included = 'all' === $this->get_products_to_include();
			if ( ! $all_included ) {
				$linked_plans = $this->get_linked_plans();
				if ( $linked_plans ) {
					foreach ( $linked_plans as $linked_plan ) {
						$linked_plan = yith_wcmbs_get_plan( $linked_plan );
						if ( $linked_plan && 'all' === $linked_plan->get_products_to_include() ) {
							$all_included = true;
							break;
						}
					}
				}
			}

			return $all_included;
		}

		public function get_included_items( $post_type, $args = array() ) {
			global $wpdb;
			$allowed_orders = array(
				'name' => 'post_title',
				'date' => 'post_date',
			);

			$default_args = array(
				'include_linked' => true,
				'include_all'    => true, // include all items if the "all posts" or "all products" options is enabled on plan. Use it with attention to performance.
				'order_by'       => 'name',
				'order'          => 'ASC',
				'items_per_page' => - 1,
				'paginate'       => false,
				'page'           => 1,
			);

			$args           = wp_parse_args( $args, $default_args );
			$include_linked = $args['include_linked'];
			$order          = in_array( $args['order'], array_keys( $allowed_orders ) ) ? $args['order'] : 'name';
			$order_col      = $allowed_orders[ $order ];
			$order_by       = $args['order_by'] === 'ASC' ? 'ASC' : 'DESC';
			$items_per_page = intval( $args['items_per_page'] );
			$page           = absint( $args['page'] );
			$paginate       = ! ! $args['paginate'];

			$plan_ids = array( $this->get_id() );
			if ( $include_linked ) {
				$linked_ids = $this->get_linked_plans();
				$plan_ids   = ! ! $linked_ids ? array_merge( $plan_ids, $linked_ids ) : $plan_ids;
			}

			$select   = "SELECT SQL_CALC_FOUND_ROWS posts.ID FROM {$wpdb->posts} AS posts";
			$join     = array();
			$where    = array( '1 = 1' );
			$group_by = "GROUP BY posts.ID";
			$where_or = [];
			$limit    = '';

			// All Posts - All Products
			$all_included = ( 'post' === $post_type && $this->are_all_posts_included() ) || 'product' === $post_type && $this->are_all_products_included();

			if ( ! $all_included ) {
				// Terms
				if ( in_array( $post_type, array( 'post', 'product' ), true ) ) {
					$post_categories    = $this->get_post_categories();
					$post_tags          = $this->get_post_tags();
					$product_categories = $this->get_product_categories();
					$product_tags       = $this->get_product_tags();

					foreach ( $plan_ids as $plan_id ) {
						if ( $plan_id !== $this->get_id() ) {
							$current_plan = yith_wcmbs_get_plan( $plan_id );
							if ( ! $current_plan ) {
								continue;
							}

							$post_categories    = array_unique( array_merge( $post_categories, $current_plan->get_post_categories() ) );
							$post_tags          = array_unique( array_merge( $post_tags, $current_plan->get_post_tags() ) );
							$product_categories = array_unique( array_merge( $product_categories, $current_plan->get_product_categories() ) );
							$product_tags       = array_unique( array_merge( $product_tags, $current_plan->get_product_tags() ) );
						}
					}

					$term_ids = array();
					if ( 'post' === $post_type ) {
						$term_tax_ids_tags = yith_wcmbs_get_term_taxonomy_ids( $post_tags, 'post_tag' );
						$term_tax_ids_cats = yith_wcmbs_get_term_taxonomy_ids( $post_categories, 'category' );

						$term_ids = array_merge( $term_tax_ids_tags, $term_tax_ids_cats );

					} elseif ( 'product' === $post_type ) {
						$term_tax_ids_tags = yith_wcmbs_get_term_taxonomy_ids( $product_tags, 'product_tag' );
						$term_tax_ids_cats = yith_wcmbs_get_term_taxonomy_ids( $product_categories, 'product_cat' );

						$term_ids = array_merge( $term_tax_ids_tags, $term_tax_ids_cats );
					}

					$term_ids = apply_filters( 'yith_wcmbs_plan_get_included_items_query_term_ids', $term_ids, $post_type, $args, $this );
					$term_ids = array_filter( array_map( 'absint', $term_ids ) );

					if ( $term_ids ) {
						$join[]     = "LEFT JOIN {$wpdb->term_relationships} AS terms ON ( posts.ID = terms.object_id )";
						$term_ids   = implode( ', ', $term_ids );
						$term_where = sprintf( 'terms.term_taxonomy_id IN (%s)', $term_ids );

						$where_or[] = $term_where;
					}
				}

				// Post Meta
				$join[]             = "INNER JOIN {$wpdb->postmeta} AS post_meta ON ( posts.ID = post_meta.post_id )";
				$post_meta_where_or = array();
				foreach ( $plan_ids as $plan_id ) {
					$post_meta_where_or[] = $wpdb->prepare( "post_meta.meta_value LIKE %s", '%' . serialize( (string) $plan_id ) . '%' );
				}

				$post_meta_where_or = implode( ' OR ', $post_meta_where_or );
				$post_meta_where    = $wpdb->prepare( "post_meta.meta_key = %s", '_yith_wcmbs_restrict_access_plan' );
				$post_meta_where    = "$post_meta_where AND ({$post_meta_where_or})";

				$where_or[] = $post_meta_where;

				// Apply the WHERE in OR
				$where_or_query = '(' . implode( ') OR (', $where_or ) . ')';
				$where[]        = "AND ({$where_or_query})";
			}

			// Downloadable Products
			if ( 'product' === $post_type && YITH_WCMBS_Products_Manager()->is_allowed_download() ) {
				$join[]                      = "INNER JOIN {$wpdb->postmeta} AS post_meta_2 ON ( posts.ID = post_meta_2.post_id )";
				$downloadable_query          = $wpdb->prepare( 'post_meta_2.meta_key = %s AND post_meta_2.meta_value = %s', '_downloadable', 'yes' );
				$downloadable_variables      = "SELECT DISTINCT(post_parent) 
					from $wpdb->posts as variations 
					INNER JOIN {$wpdb->postmeta} AS variations_pm ON (variations.ID = variations_pm.post_id AND post_parent != 0 AND post_type = 'product_variation' AND variations_pm.meta_key = '_downloadable') 
					WHERE variations_pm.meta_value = 'yes'  
				";
				$downloadable_query_variable = "posts.ID in ($downloadable_variables)";

				$where[] = "AND ( ({$downloadable_query}) OR ($downloadable_query_variable) )";
			}

			// Post Type and Status
			$where[] = $wpdb->prepare( "AND posts.post_type = %s", $post_type );
			if ( 'attachment' === $post_type ) {
				$where[] = "AND (posts.post_status <> 'trash' AND posts.post_status <> 'auto-draft')";
			} else {
				$where[] = "AND posts.post_status = 'publish'";
			}

			// Order by
			$order_by_query = "ORDER BY posts.{$order_col} {$order_by}";

			// Limit
			if ( $items_per_page >= 0 ) {
				$offset = $page > 1 ? absint( ( $page - 1 ) * $items_per_page ) . ', ' : '';
				$limit  = 'LIMIT ' . $offset . $items_per_page;
			}

			$join  = apply_filters( 'yith_wcmbs_plan_get_included_items_query_join', $join, $post_type, $args, $this );
			$where = apply_filters( 'yith_wcmbs_plan_get_included_items_query_where', $where, $post_type, $args, $this );

			$join  = implode( ' ', $join );
			$where = implode( ' ', $where );

			$query = "$select $join WHERE $where $group_by $order_by_query $limit";

			$items = $wpdb->get_col( $query );
			$items = ! ! $items ? array_map( 'absint', $items ) : array();

			if ( $paginate ) {
				$total = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
				$items = (object) array(
					'items'         => $items,
					'total'         => $total,
					'max_num_pages' => $items_per_page > 0 ? absint( ceil( $total / $items_per_page ) ) : 1,
				);

			}

			return $items;
		}
	}
}


if ( ! function_exists( 'yith_wcmbs_get_plan' ) ) {
	function yith_wcmbs_get_plan( $plan ) {
		$the_plan = new YITH_WCMBS_Plan( $plan );

		return $the_plan->is_valid() ? $the_plan : false;
	}
}