<?php
namespace ElementPack\Base;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * \Base\Condition
 * @since  5.3.0
 */
abstract class Condition {

	/**
	 * @var Module_Base
	 */
	protected static $_instances = [];

	protected $element_id;

	/**
	 * @return string of the current module class name
	 * @since 5.3.0
	 */
	public static function class_name() {
		return get_called_class();
	}

	/**
	 * @return static
	 */
	public static function instance() {
		if ( empty( static::$_instances[ static::class_name() ] ) ) {
			static::$_instances[ static::class_name() ] = new static();
		}

		return static::$_instances[ static::class_name() ];
	}

	/**
	 * Defaults to true
	 * @return bool if current condition is supported
	 * @since  5.3.0
	 */
	public static function is_supported() {
		return true;
	}

	/**
	 * Get the name of condition
	 * @return string as per our condition control name
	 * @since  5.3.0
	 */
	public function get_name() {}

	/**
	 * Get the title of condition
	 * @return string as per condition control title
	 * @since  5.3.0
	 */
	public function get_title() {}

	/**
	 * Get the control name
	 * @return string as per condition control name
	 * @since  5.3.0
	 */
	public function get_name_control() {
		return false; }

	/**
	 * Get the control value
	 * @return string as per condition control value
	 * @since  5.3.0
	 */
	public function get_value_control() {}

	/**
	 * Check the condition
	 * @param string $relation Comparison operator for compare function
	 * @param mixed $val will check the control value as per condition needs
	 * @since 5.3.0
	 */
	public function check( $relation, $val ) {}

	/**
	 * Compare conditions.
	 * Calls compare method
	 * @param mixed  $left_val  First value to compare.
	 * @param mixed  $right_val Second value to compare.
	 * @param string $relation  Comparison operator.
	 * @return bool
	 * @since 5.3.0
	 *
	 */
	public function compare( $left_val, $right_val, $relation ) {
		switch ( $relation ) {
			case 'is':
				return $left_val == $right_val;
			case 'not':
				return $left_val != $right_val;
			default:
				return $left_val === $right_val;
		}
	}

	/**
	 * Set Condition Element ID
	 * Set the element ID for this condition
	 * @return string
	 * @since  5.3.0
	 */
	public function set_element_id( $id ) {
		$this->element_id = $id;
	}

	/**
	 * Get Condition Element ID
	 * Returns the previously set element id
	 * @return string
	 * @since  5.3.0
	 */
	protected function get_element_id() {
		return $this->element_id;
	}
}
