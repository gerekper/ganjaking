<?php
/**
 * Class YITH_WCBK_Simple_Object
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Simple_Object' ) ) {
	/**
	 * Class YITH_WCBK_Simple_Object
	 *
	 * @since 2.1.0
	 */
	abstract class YITH_WCBK_Simple_Object {

		/**
		 * The data.
		 *
		 * @var array
		 */
		protected $data = array();

		/**
		 * The object type
		 *
		 * @var string
		 */
		protected $object_type = 'simple_object';

		/**
		 * YITH_WCBK_Simple_Object constructor.
		 *
		 * @param array|object $args Arguments of data.
		 */
		public function __construct( $args = array() ) {
			if ( is_object( $args ) ) {
				$args = get_object_vars( $args );
			}

			/**
			 * Filter default data.
			 *
			 * @since 2.1.13
			 */
			$this->data = apply_filters( 'yith_wcbk_' . $this->object_type . '_object_default_data', $this->data, $this );

			$this->populate_props( $args );
		}

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @return string
		 */
		protected function get_hook_prefix() {
			return 'yith_wcbk_' . $this->object_type . '_get_';
		}

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @return string
		 */
		protected function get_hook() {
			return 'yith_wcbk_' . $this->object_type . '_get';
		}

		/**
		 * Get object properties
		 *
		 * @param string $prop    The prop.
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return mixed
		 */
		protected function get_prop( $prop, $context = 'view' ) {
			$value = null;

			if ( array_key_exists( $prop, $this->data ) ) {
				$value = $this->data[ $prop ];

				if ( 'view' === $context ) {
					$value = apply_filters( $this->get_hook_prefix() . $prop, $value, $this );
					$value = apply_filters( $this->get_hook(), $value, $prop, $this );
				}
			}

			return $value;
		}

		/**
		 * Populate
		 *
		 * @param array $args The arguments.
		 */
		protected function populate_props( $args = array() ) {
			$args = is_array( $args ) ? $args : array();

			foreach ( $args as $prop => $value ) {
				$setter = "set_{$prop}";
				if ( is_callable( array( $this, $setter ) ) ) {
					$this->{$setter}( $value );
				} else {
					$this->set_prop( $prop, $value );
				}
			}
		}

		/**
		 * Set object properties
		 *
		 * @param string $prop  The property.
		 * @param mixed  $value The value.
		 */
		public function set_prop( $prop, $value ) {
			if ( array_key_exists( $prop, $this->data ) ) {
				$this->data[ $prop ] = $value;
			}
		}

		/**
		 * Return an array of data
		 *
		 * @return array
		 */
		public function to_array() {
			return array_map( 'yith_wcbk_simple_object_to_array_deep', $this->get_data() );
		}

		/**
		 * Return an array of data
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_data() {
			return $this->data;
		}
	}
}
