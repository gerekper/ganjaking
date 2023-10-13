<?php
/**
 * Global Availability Rule
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Global_Availability_Rule' ) ) {
	/**
	 * Class YITH_WCBK_Global_Availability_Rule
	 * the Global availability rule class.
	 *
	 * @since 5.0.0
	 */
	class YITH_WCBK_Global_Availability_Rule extends YITH_WCBK_Data {

		/**
		 * The ID
		 *
		 * @var int
		 */
		protected $id;

		/**
		 * The data
		 *
		 * @var array
		 */
		protected $data = array(
			'name'                 => '',
			'type'                 => 'generic',
			'enabled'              => 'yes',
			'date_ranges'          => array(),
			'availabilities'       => array(),
			'priority'             => 0,
			'exclude_products'     => 'no',
			'excluded_product_ids' => array(),
		);

		/**
		 * The object type
		 *
		 * @var string
		 */
		protected $object_type = 'global_availability_rule';

		/**
		 * YITH_WCBK_Global_Availability_Rule constructor.
		 *
		 * @param int|YITH_WCBK_Global_Availability_Rule $rule The object.
		 *
		 * @throws Exception If passed booking is invalid.
		 */
		public function __construct( $rule = 0 ) {
			parent::__construct( $rule );

			$this->data_store = WC_Data_Store::load( 'yith-wcbk-global-availability-rule' );

			if ( is_numeric( $rule ) && $rule > 0 ) {
				$this->set_id( $rule );
			} elseif ( $rule instanceof self ) {
				$this->set_id( absint( $rule->get_id() ) );
			} else {
				$this->set_object_read( true );
			}

			if ( $this->get_id() > 0 ) {
				$this->data_store->read( $this );
			}
		}

		/*
		|--------------------------------------------------------------------------
		| CRUD Getters
		|--------------------------------------------------------------------------
		|
		| Functions for getting data.
		*/

		/**
		 * Return the name.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			return $this->get_prop( 'name', $context );
		}

		/**
		 * Return the type.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_type( $context = 'view' ) {
			return $this->get_prop( 'type', $context );
		}

		/**
		 * Return the enabled.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_enabled( $context = 'view' ) {
			return $this->get_prop( 'enabled', $context );
		}

		/**
		 * Return the date_ranges.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_date_ranges( $context = 'view' ) {
			return $this->get_prop( 'date_ranges', $context );
		}

		/**
		 * Return the availabilities.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_availabilities( $context = 'view' ) {
			return $this->get_prop( 'availabilities', $context );
		}

		/**
		 * Return the priority.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_priority( $context = 'view' ) {
			return $this->get_prop( 'priority', $context );
		}

		/**
		 * Return the exclude_products.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_exclude_products( $context = 'view' ) {
			return $this->get_prop( 'exclude_products', $context );
		}

		/**
		 * Return the excluded_product_ids.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_excluded_product_ids( $context = 'view' ) {
			return $this->get_prop( 'excluded_product_ids', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| CRUD Setters
		|--------------------------------------------------------------------------
		|
		| Functions for getting data.
		*/

		/**
		 * Set the name
		 *
		 * @param string $value The value to set.
		 */
		public function set_name( $value ) {
			$this->set_prop( 'name', $value );
		}

		/**
		 * Set the type
		 *
		 * @param string $value The value to set.
		 */
		public function set_type( $value ) {
			$value = in_array( $value, array( 'generic', 'specific' ), true ) ? $value : 'generic';
			$this->set_prop( 'type', $value );
		}

		/**
		 * Set the enabled
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_enabled( $value ) {
			$this->set_prop( 'enabled', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the date_ranges
		 *
		 * @param array $value The value to set.
		 */
		public function set_date_ranges( $value ) {
			$this->set_prop( 'date_ranges', is_array( $value ) ? $value : array() );
		}

		/**
		 * Set the availabilities
		 *
		 * @param array $availabilities The value to set.
		 */
		public function set_availabilities( $availabilities ) {
			$availabilities = is_array( $availabilities ) ? $availabilities : array();

			foreach ( $availabilities as &$availability ) {
				$is_all_day = 'yes' === ( $availability['all_day'] ?? 'yes' );
				if ( $is_all_day ) {
					$availability['time_slots'] = array();
				}
			}

			$this->set_prop( 'availabilities', $availabilities );
		}

		/**
		 * Set the priority
		 *
		 * @param int $value The value to set.
		 */
		public function set_priority( $value ) {
			$this->set_prop( 'priority', absint( $value ) );
		}

		/**
		 * Set the exclude_products
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_exclude_products( $value ) {
			$this->set_prop( 'exclude_products', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the excluded_product_ids
		 *
		 * @param int[] $value The value to set.
		 */
		public function set_excluded_product_ids( $value ) {
			$value = is_array( $value ) ? array_map( 'absint', $value ) : array();
			$value = array_filter( array_unique( $value ) );

			$this->set_prop( 'excluded_product_ids', $value );
		}

		/*
		|--------------------------------------------------------------------------
		| Non-CRUD Getters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Retrieve the related availability rule.
		 *
		 * @return YITH_WCBK_Availability_Rule
		 */
		public function get_availability_rule() {

			$rule = new YITH_WCBK_Availability_Rule();
			$rule->set_name( $this->get_name() );
			$rule->set_type( $this->get_type() );
			$rule->set_enabled( $this->get_enabled() );
			$rule->set_date_ranges( $this->get_date_ranges() );
			$rule->set_availabilities( $this->get_availabilities() );

			return $rule;
		}
	}
}
