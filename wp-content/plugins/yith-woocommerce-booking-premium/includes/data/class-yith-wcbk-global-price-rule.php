<?php
/**
 * Global Price rule
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Global_Price_Rule' ) ) {
	/**
	 * Class YITH_WCBK_Global_Price_Rule
	 * the global price rule class.
	 *
	 * @since 5.0.0
	 */
	class YITH_WCBK_Global_Price_Rule extends YITH_WCBK_Data {
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
			'enabled'              => 'yes',
			'conditions'           => array(),
			'change_base_price'    => 'no',
			'base_price_operator'  => 'add',
			'base_price'           => 0,
			'change_base_fee'      => 'no',
			'base_fee_operator'    => 'add',
			'base_fee'             => 0,
			'priority'             => 0,
			'exclude_products'     => 'no',
			'excluded_product_ids' => array(),
		);

		/**
		 * The object type
		 *
		 * @var string
		 */
		protected $object_type = 'global_price_rule';

		/**
		 * The constructor.
		 *
		 * @param int|self $rule The object.
		 *
		 * @throws Exception If passed object is invalid.
		 */
		public function __construct( $rule = 0 ) {
			parent::__construct( $rule );

			$this->data_store = WC_Data_Store::load( 'yith-wcbk-global-price-rule' );

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
		 * Return the conditions.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 */
		public function get_conditions( $context = 'view' ) {
			return $this->get_prop( 'conditions', $context );
		}

		/**
		 * Return the change_base_price.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_change_base_price( $context = 'view' ) {
			return $this->get_prop( 'change_base_price', $context );
		}

		/**
		 * Return the base_price_operator.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_base_price_operator( $context = 'view' ) {
			return $this->get_prop( 'base_price_operator', $context );
		}

		/**
		 * Return the base_price.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return double
		 */
		public function get_base_price( $context = 'view' ) {
			return $this->get_prop( 'base_price', $context );
		}

		/**
		 * Return the change_base_fee.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_change_base_fee( $context = 'view' ) {
			return $this->get_prop( 'change_base_fee', $context );
		}

		/**
		 * Return the base_fee_operator.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_base_fee_operator( $context = 'view' ) {
			return $this->get_prop( 'base_fee_operator', $context );
		}

		/**
		 * Return the base_fee.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return double
		 */
		public function get_base_fee( $context = 'view' ) {
			return $this->get_prop( 'base_fee', $context );
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
		 * Set the enabled
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_enabled( $value ) {
			$this->set_prop( 'enabled', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the conditions
		 *
		 * @param array $value The value to set.
		 */
		public function set_conditions( $value ) {
			$this->set_prop( 'conditions', is_array( $value ) ? $value : array() );
		}

		/**
		 * Set the change_base_price
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_change_base_price( $value ) {
			$this->set_prop( 'change_base_price', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the base_price_operator
		 *
		 * @param string $value The value to set.
		 */
		public function set_base_price_operator( $value ) {
			$allowed_operators = array( 'add', 'sub', 'mul', 'div', 'set-to', 'add-percentage', 'sub-percentage' );
			if ( in_array( $value, $allowed_operators, true ) ) {
				$this->set_prop( 'base_price_operator', $value );
			}
		}

		/**
		 * Set the base_price
		 *
		 * @param string|double $value The value to set.
		 */
		public function set_base_price( $value ) {
			$this->set_prop( 'base_price', wc_format_decimal( $value, false, true ) );
		}

		/**
		 * Set the change_base_fee
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_change_base_fee( $value ) {
			$this->set_prop( 'change_base_fee', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the base_fee_operator
		 *
		 * @param string $value The value to set.
		 */
		public function set_base_fee_operator( $value ) {
			$allowed_operators = array( 'add', 'sub', 'mul', 'div', 'set-to', 'add-percentage', 'sub-percentage' );
			if ( in_array( $value, $allowed_operators, true ) ) {
				$this->set_prop( 'base_fee_operator', $value );
			}
		}

		/**
		 * Set the base_fee
		 *
		 * @param string|double $value The value to set.
		 */
		public function set_base_fee( $value ) {
			$this->set_prop( 'base_fee', wc_format_decimal( $value, false, true ) );
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
		 * Retrieve the related price rule.
		 *
		 * @return YITH_WCBK_Price_Rule
		 */
		public function get_price_rule() {
			$rule = new YITH_WCBK_Price_Rule();
			$rule->set_name( $this->get_name() );
			$rule->set_conditions( $this->get_conditions() );
			$rule->set_enabled( $this->get_enabled() );

			if ( 'yes' === $this->get_change_base_price() ) {
				$rule->set_base_price_operator( $this->get_base_price_operator() );
				$rule->set_base_price( $this->get_base_price() );
			}

			if ( 'yes' === $this->get_change_base_fee() ) {
				$rule->set_base_fee_operator( $this->get_base_fee_operator() );
				$rule->set_base_fee( $this->get_base_fee() );
			}

			return $rule;
		}
	}
}
